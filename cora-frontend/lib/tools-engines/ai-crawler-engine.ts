// ══════════════════════════════════════════════════════════════════════════
// AI CRAWLER ACCESS & ROBOTS.TXT DIRECTIVE ENGINE (PURE COMPUTATION)
// RFC 9309 compliant parsing for OpenAI, Anthropic, Perplexity & Google
// ══════════════════════════════════════════════════════════════════════════

export type CrawlerAccessStatus = 'allowed' | 'blocked' | 'partially_blocked' | 'unspecified';

export interface AiCrawlerDirective {
  userAgent: string;
  provider: string;
  category: 'Search & Citations' | 'Model Training' | 'Web Search';
  status: CrawlerAccessStatus;
  ruleMatched?: string;
  explanation: string;
}

export interface AiCrawlerFinding {
  type: 'error' | 'warning' | 'positive';
  title: string;
  description: string;
  remedy?: string;
}

export interface AiCrawlerAuditResult {
  overallScore: number; // 0 to 100
  allowedBotsCount: number;
  totalBotsChecked: number;
  crawlers: AiCrawlerDirective[];
  findings: AiCrawlerFinding[];
  recommendedSnippet: string;
  accessibilityBand: {
    label: string;
    description: string;
  };
}

export const AI_CRAWLER_SAMPLE_ROBOTS = {
  balanced_recommended: `# Recommended Balanced AI Configuration
# Allows AI search bots to cite your site, blocks generic model training scrapers

User-agent: Googlebot
Allow: /

User-agent: Bingbot
Allow: /

User-agent: OAI-SearchBot
Allow: /

User-agent: Claude-SearchBot
Allow: /

User-agent: PerplexityBot
Allow: /

User-agent: GPTBot
Disallow: /

User-agent: ClaudeBot
Disallow: /

User-agent: Google-Extended
Disallow: /

User-agent: *
Disallow: /wp-admin/
Allow: /wp-admin/admin-ajax.php
`,

  wp_default_typical: `# Standard WordPress Default
User-agent: *
Disallow: /wp-admin/
Allow: /wp-admin/admin-ajax.php

Sitemap: https://example.com/wp-sitemap.xml
`,

  strict_block_all_ai: `# Strict AI Block All
User-agent: *
Disallow: /

User-agent: GPTBot
Disallow: /

User-agent: OAI-SearchBot
Disallow: /

User-agent: ClaudeBot
Disallow: /

User-agent: PerplexityBot
Disallow: /
`,
};

interface AgentRule {
  agent: string;
  disallows: string[];
  allows: string[];
}

export function parseRobotsTxt(rawContent: string): AiCrawlerAuditResult {
  const lines = rawContent.split('\n');
  const rulesByAgent: Record<string, { allows: string[]; disallows: string[] }> = {};
  let currentAgents: string[] = [];

  for (const rawLine of lines) {
    const line = rawLine.split('#')[0].trim();
    if (!line) continue;

    const [directive, ...rest] = line.split(':');
    const key = directive.trim().toLowerCase();
    const val = rest.join(':').trim();

    if (key === 'user-agent') {
      const agent = val.toLowerCase();
      if (currentAgents.length === 0 || !lines[lines.indexOf(rawLine) - 1]?.toLowerCase().startsWith('user-agent')) {
        currentAgents = [agent];
      } else {
        currentAgents.push(agent);
      }
      for (const ag of currentAgents) {
        if (!rulesByAgent[ag]) {
          rulesByAgent[ag] = { allows: [], disallows: [] };
        }
      }
    } else if (key === 'disallow' && currentAgents.length > 0) {
      for (const ag of currentAgents) {
        if (rulesByAgent[ag]) rulesByAgent[ag].disallows.push(val);
      }
    } else if (key === 'allow' && currentAgents.length > 0) {
      for (const ag of currentAgents) {
        if (rulesByAgent[ag]) rulesByAgent[ag].allows.push(val);
      }
    }
  }

  const targetBots = [
    { name: 'OAI-SearchBot', provider: 'OpenAI (ChatGPT Search)', category: 'Search & Citations' as const },
    { name: 'GPTBot', provider: 'OpenAI (Foundation Training)', category: 'Model Training' as const },
    { name: 'Claude-SearchBot', provider: 'Anthropic (Claude Search)', category: 'Search & Citations' as const },
    { name: 'ClaudeBot', provider: 'Anthropic (Claude Training)', category: 'Model Training' as const },
    { name: 'PerplexityBot', provider: 'Perplexity (AI Search Index)', category: 'Search & Citations' as const },
    { name: 'Googlebot', provider: 'Google Search', category: 'Web Search' as const },
    { name: 'Google-Extended', provider: 'Google (Gemini Training)', category: 'Model Training' as const },
    { name: 'Bingbot', provider: 'Microsoft (Bing & Copilot)', category: 'Web Search' as const },
  ];

  const wildcard = rulesByAgent['*'];

  const crawlers: AiCrawlerDirective[] = targetBots.map((bot) => {
    const specific = rulesByAgent[bot.name.toLowerCase()];
    let status: CrawlerAccessStatus = 'allowed';
    let explanation = 'Allowed to crawl public pages.';
    let ruleMatched: string | undefined;

    if (specific) {
      if (specific.disallows.includes('/') || specific.disallows.includes('')) {
        if (specific.allows.includes('/')) {
          status = 'allowed';
          ruleMatched = `Allow: / under User-agent: ${bot.name}`;
          explanation = 'Explicitly allowed via specific User-agent directive.';
        } else {
          status = 'blocked';
          ruleMatched = `Disallow: / under User-agent: ${bot.name}`;
          explanation = `Blocked from accessing site content via specific ${bot.name} directive.`;
        }
      } else if (specific.disallows.length > 0) {
        status = 'partially_blocked';
        ruleMatched = `Disallow: ${specific.disallows[0]}`;
        explanation = `Certain paths (${specific.disallows.join(', ')}) are restricted.`;
      }
    } else if (wildcard) {
      if (wildcard.disallows.includes('/')) {
        status = 'blocked';
        ruleMatched = 'Disallow: / under User-agent: *';
        explanation = 'Inheriting wildcard block from User-agent: *';
      } else if (wildcard.disallows.length > 0) {
        status = 'partially_blocked';
        ruleMatched = `Wildcard restriction (${wildcard.disallows[0]})`;
        explanation = `Inheriting wildcard directory restrictions (${wildcard.disallows.join(', ')}).`;
      }
    }

    return {
      userAgent: bot.name,
      provider: bot.provider,
      category: bot.category,
      status,
      ruleMatched,
      explanation,
    };
  });

  const searchBots = crawlers.filter((c) => c.category === 'Search & Citations' || c.category === 'Web Search');
  const allowedSearchCount = searchBots.filter((c) => c.status === 'allowed' || c.status === 'partially_blocked').length;
  const overallScore = Math.round((allowedSearchCount / searchBots.length) * 100);
  const allowedBotsCount = crawlers.filter((c) => c.status === 'allowed').length;

  const findings: AiCrawlerFinding[] = [];

  const oaiBot = crawlers.find((c) => c.userAgent === 'OAI-SearchBot');
  if (oaiBot?.status === 'blocked') {
    findings.push({
      type: 'error',
      title: 'OAI-SearchBot is Blocked (Ineligible for ChatGPT Search Citations)',
      description: 'Your robots.txt blocks OAI-SearchBot, preventing ChatGPT Search from citing your website and linking to your business in live AI answers.',
      remedy: 'Add "User-agent: OAI-SearchBot \\n Allow: /" to your robots.txt file.',
    });
  }

  const perplexity = crawlers.find((c) => c.userAgent === 'PerplexityBot');
  if (perplexity?.status === 'blocked') {
    findings.push({
      type: 'warning',
      title: 'PerplexityBot is Blocked',
      description: 'Perplexity AI Search is disallowed from indexing your pages for answer citations.',
      remedy: 'Declare "User-agent: PerplexityBot \\n Allow: /" explicitly.',
    });
  }

  const gptBot = crawlers.find((c) => c.userAgent === 'GPTBot');
  if (gptBot?.status === 'allowed') {
    findings.push({
      type: 'warning',
      title: 'GPTBot Training Scraper is Allowed',
      description: 'GPTBot is allowed to scrape your full site content for generic model training.',
      remedy: 'If you wish to protect your written IP while remaining in ChatGPT Search, disallow GPTBot while allowing OAI-SearchBot.',
    });
  }

  if (findings.length === 0) {
    findings.push({
      type: 'positive',
      title: 'Optimal AI Search & Citation Configuration',
      description: 'Your robots.txt cleanly segregates AI search indexers from generic model scrapers.',
    });
  }

  let bandLabel = 'Full AI Discovery';
  let bandDesc = 'All major AI search indexers (ChatGPT, Claude, Perplexity) are permitted to cite your content.';

  if (overallScore < 40) {
    bandLabel = 'Invisible to AI Search';
    bandDesc = 'Critical search crawlers are blocked. Your site cannot be indexed or cited in real-time AI search.';
  } else if (overallScore < 80) {
    bandLabel = 'Partial AI Accessibility';
    bandDesc = 'Some AI search crawlers are blocked or subject to wildcard restrictions.';
  }

  return {
    overallScore,
    allowedBotsCount,
    totalBotsChecked: crawlers.length,
    crawlers,
    findings,
    recommendedSnippet: AI_CRAWLER_SAMPLE_ROBOTS.balanced_recommended,
    accessibilityBand: {
      label: bandLabel,
      description: bandDesc,
    },
  };
}
