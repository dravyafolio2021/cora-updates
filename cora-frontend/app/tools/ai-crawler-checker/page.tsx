'use client';

import React, { useState, useMemo, useEffect } from 'react';
import Link from 'next/link';
import {
  ToolContainer,
  ToolFindingsList,
  ToolMethodologyCard,
  ToolWorkedExample,
  ToolPrivacyDisclosure,
  ToolContextualCoraCta,
  ToolFaqAccordion,
  ToolJsonLd,
  FindingItem,
  ToolMobileBar,
  ToolMobileViewToggle,
} from '@/components/tools/framework';
import {
  parseRobotsTxt,
  AI_CRAWLER_SAMPLE_ROBOTS,
  CrawlerAccessStatus,
  AiCrawlerAuditResult,
} from '@/lib/tools-engines/ai-crawler-engine';
import {
  trackToolStarted,
  trackToolCompleted,
  trackToolResultBand,
} from '@/lib/tools-analytics';
import { Bot, ShieldCheck, ShieldAlert, CheckCircle2, XCircle, AlertTriangle, Copy, Check, Code, Sparkles, RefreshCw } from 'lucide-react';

export default function AiCrawlerCheckerPage() {
  const [robotsContent, setRobotsContent] = useState<string>(
    AI_CRAWLER_SAMPLE_ROBOTS.balanced_recommended
  );
  const [hasInteracted, setHasInteracted] = useState(false);
  const [copiedSnippet, setCopiedSnippet] = useState(false);
  const [mobileTab, setMobileTab] = useState<'inputs' | 'results'>('inputs');

  const result: AiCrawlerAuditResult = useMemo(() => {
    return parseRobotsTxt(robotsContent);
  }, [robotsContent]);

  useEffect(() => {
    if (hasInteracted) {
      trackToolCompleted({
        tool_slug: 'ai-crawler-checker',
        calculation_mode: 'client_side',
      });
      trackToolResultBand({
        tool_slug: 'ai-crawler-checker',
        band_name: result.accessibilityBand.label,
        primary_metric_value: result.overallScore,
      });
    }
  }, [hasInteracted, result.accessibilityBand.label, result.overallScore]);

  const handleContentChange = (val: string) => {
    if (!hasInteracted) {
      setHasInteracted(true);
      trackToolStarted({ tool_slug: 'ai-crawler-checker' });
    }
    setRobotsContent(val);
  };

  const loadSample = (key: keyof typeof AI_CRAWLER_SAMPLE_ROBOTS) => {
    if (!hasInteracted) {
      setHasInteracted(true);
      trackToolStarted({ tool_slug: 'ai-crawler-checker' });
    }
    setRobotsContent(AI_CRAWLER_SAMPLE_ROBOTS[key]);
  };

  const copyOptimizedRobots = () => {
    if (typeof navigator !== 'undefined') {
      navigator.clipboard.writeText(result.recommendedSnippet);
      setCopiedSnippet(true);
      setTimeout(() => setCopiedSnippet(false), 2000);
    }
  };

  const findings: FindingItem[] = useMemo(() => {
    return result.findings.map((f, idx) => ({
      id: `finding-${idx}`,
      severity: f.type === 'error' ? 'critical' : f.type === 'warning' ? 'warning' : 'positive',
      title: f.title,
      description: f.description,
      remedy: f.remedy,
    }));
  }, [result.findings]);

  const canonicalUrl = 'https://heycora.in/tools/ai-crawler-checker';
  const metaTitle = 'AI Crawler Access Checker | Test OAI-SearchBot, Claude & Perplexity in robots.txt';
  const metaDescription =
    'Audit your robots.txt file for AI search crawlers. Test whether OAI-SearchBot, ClaudeBot, Perplexity, and Googlebot are blocked or allowed to index and cite your content.';

  const faqs = [
    {
      question: 'What is the difference between AI Search bots and AI Training bots?',
      answer:
        'AI Search bots (like OAI-SearchBot, Claude-SearchBot, and PerplexityBot) index your public pages in real time to cite your business and link directly to your website in AI answers. AI Training bots (like GPTBot, ClaudeBot, and Google-Extended) crawl web content to train future foundation models. Many agencies allow Search bots while blocking Training bots.',
    },
    {
      question: 'Will blocking GPTBot prevent my website from showing in ChatGPT Search?',
      answer:
        'No. OpenAI publisher documentation confirms that ChatGPT Search uses OAI-SearchBot for real-time citations and search indexing. You can disallow GPTBot (training) while allowing OAI-SearchBot (search citations).',
    },
    {
      question: 'How do I test my live robots.txt file with this tool?',
      answer:
        'Open your website at yourdomain.com/robots.txt, copy the full text contents, and paste them into the editor box above. The tool parses directives instantly in your browser using RFC 9309 rules without transmitting data.',
    },
    {
      question: 'How does Cora optimize websites for AI and Generative Engine Optimization (GEO)?',
      answer:
        'Cora generates clean, crawlable robots.txt directives, real-time Schema markup (Organization, Service, Article, SoftwareApplication), and structured entity maps that make your service offerings clearly understandable to ChatGPT, Claude, and Perplexity.',
    },
  ];

  return (
    <ToolContainer
      categoryLabel="AI Search & GEO"
      title="AI Crawler Access & robots.txt Checker"
      subtitle="Audit whether your website allows ChatGPT Search, Claude, Perplexity, and Google to index and cite your business—or if accidental directives are making you invisible."
      badgeTag="RFC 9309 Parser"
      executionMode="browser-only"
      lastReviewedDate="October 2026"
    >
      <ToolJsonLd
        toolTitle={metaTitle}
        toolDescription={metaDescription}
        canonicalUrl={canonicalUrl}
        categoryName="AI Search & GEO"
        faqs={faqs}
      />

      {/* ── MOBILE VIEW TOGGLE ── */}
      <ToolMobileViewToggle
        activeTab={mobileTab}
        onTabChange={setMobileTab}
        inputsLabel="robots.txt"
        resultsLabel="Bot Audit"
      />

      {/* ── WORKSPACE GRID ── */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        
        {/* LEFT COLUMN: ROBOTS.TXT EDITOR (7 cols) */}
        <div className={`lg:col-span-7 space-y-6 ${mobileTab === 'results' ? 'hidden lg:block' : 'block'}`}>
          
          {/* Preset Buttons */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-4 sm:p-6 shadow-xs">
            <div className="flex items-center justify-between gap-2 mb-3">
              <span className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-500">
                Load robots.txt Templates
              </span>
              <span className="text-[11px] font-mono text-zinc-400">1-click presets</span>
            </div>
            <div className="flex items-center gap-2 overflow-x-auto whitespace-nowrap scrollbar-none sm:grid sm:grid-cols-3 -mx-2 px-2 sm:mx-0 sm:px-0 touch-pan-x">
              <button
                type="button"
                onClick={() => loadSample('balanced_recommended')}
                className="shrink-0 min-w-[210px] sm:min-w-0 px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800 touch-manipulation active:scale-[0.98] cursor-pointer"
              >
                <span className="font-semibold block text-zinc-950">Balanced (Recommended)</span>
                <span className="text-[11px] text-zinc-500">Allow Search · Block Training</span>
              </button>
              <button
                type="button"
                onClick={() => loadSample('wp_default_typical')}
                className="shrink-0 min-w-[210px] sm:min-w-0 px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800 touch-manipulation active:scale-[0.98] cursor-pointer"
              >
                <span className="font-semibold block text-zinc-950">Standard WordPress</span>
                <span className="text-[11px] text-zinc-500">Disallow /wp-admin/</span>
              </button>
              <button
                type="button"
                onClick={() => loadSample('strict_block_all_ai')}
                className="shrink-0 min-w-[210px] sm:min-w-0 px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800 touch-manipulation active:scale-[0.98] cursor-pointer"
              >
                <span className="font-semibold block text-zinc-950">Strict AI Block</span>
                <span className="text-[11px] text-zinc-500">Disallow all AI bots</span>
              </button>
            </div>
          </div>

          {/* Live Code / Textarea Box */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-4">
            <div className="flex items-center justify-between">
              <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight flex items-center gap-2">
                <Code className="w-5 h-5 text-zinc-700" />
                <span>Paste Your robots.txt Content</span>
              </h2>
              <span className="text-xs font-mono text-zinc-400">
                {robotsContent.split('\n').length} lines
              </span>
            </div>

            <p className="text-xs text-zinc-500">
              Paste the text from your site’s <code className="bg-zinc-100 px-1 py-0.5 rounded font-mono">/robots.txt</code> URL. Directives are parsed locally in real time.
            </p>

            <div className="relative">
              <textarea
                value={robotsContent}
                onChange={(e) => handleContentChange(e.target.value)}
                rows={14}
                className="w-full rounded-xl border border-zinc-300/90 bg-[#FAF9F5] p-4 font-mono text-xs text-zinc-900 focus:bg-white focus:border-zinc-900 focus:outline-hidden leading-relaxed shadow-inner"
                placeholder="User-agent: *&#10;Disallow: /wp-admin/&#10;..."
                spellCheck={false}
              />
            </div>
          </div>

          {/* Recommended Optimized robots.txt Output */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-4">
            <div className="flex items-center justify-between">
              <div>
                <h3 className="font-display text-base sm:text-lg font-bold text-zinc-950 tracking-tight">
                  Recommended AI-Optimized robots.txt
                </h3>
                <p className="text-xs text-zinc-500">
                  Allows AI search indexers to cite your business while blocking bulk training scrapers.
                </p>
              </div>
              <button
                type="button"
                onClick={copyOptimizedRobots}
                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 text-xs font-semibold font-mono transition-colors shadow-2xs"
              >
                {copiedSnippet ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5" />}
                <span>{copiedSnippet ? 'Copied!' : 'Copy Snippet'}</span>
              </button>
            </div>

            <pre className="rounded-xl border border-zinc-200/80 bg-zinc-950 text-zinc-200 p-4 font-mono text-xs overflow-x-auto leading-relaxed">
              <code>{result.recommendedSnippet}</code>
            </pre>
          </div>

        </div>

        {/* RIGHT COLUMN: BOT AUDIT MATRIX (5 cols) */}
        <div className={`lg:col-span-5 space-y-6 ${mobileTab === 'inputs' ? 'hidden lg:block' : 'block'}`}>
          
          {/* Main Score Card */}
          <div className="rounded-3xl border border-zinc-900 bg-zinc-950 text-white p-6 sm:p-8 shadow-lg sticky top-24">
            <div className="flex items-center justify-between mb-4 border-b border-zinc-800 pb-3">
              <span className="text-xs font-mono font-medium text-zinc-400 uppercase tracking-wider">
                AI Search Readiness
              </span>
              <span className="text-[11px] font-mono px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-300">
                100% Client Calculated
              </span>
            </div>

            <div className="font-mono text-4xl sm:text-5xl font-bold tracking-tight text-white mb-2">
              {result.overallScore} / 100
              <span className="text-sm font-sans font-normal text-zinc-400 block mt-1">
                {result.accessibilityBand.label} ({result.allowedBotsCount} / {result.totalBotsChecked} bots allowed)
              </span>
            </div>

            <div className="w-full bg-zinc-800 h-2 rounded-full overflow-hidden mt-4">
              <div
                className={`h-full rounded-full transition-all duration-300 ${
                  result.overallScore >= 80
                    ? 'bg-emerald-400'
                    : result.overallScore >= 50
                    ? 'bg-amber-400'
                    : 'bg-rose-500'
                }`}
                style={{ width: `${result.overallScore}%` }}
              />
            </div>

            {/* Individual Crawler Status Matrix */}
            <div className="mt-6 pt-6 border-t border-zinc-800 space-y-2.5 font-mono text-xs">
              <span className="text-[11px] uppercase tracking-wider text-zinc-400 font-bold block mb-1">
                Crawler Directive Status
              </span>
              
              {result.crawlers.map((crawler) => {
                const isAllowed = crawler.status === 'allowed';
                const isBlocked = crawler.status === 'blocked';
                const isPartial = crawler.status === 'partially_blocked';

                return (
                  <div
                    key={crawler.userAgent}
                    className="flex items-center justify-between p-2.5 rounded-lg bg-zinc-900/90 border border-zinc-800/80"
                  >
                    <div className="min-w-0 pr-2">
                      <span className="font-bold text-white block truncate">{crawler.userAgent}</span>
                      <span className="text-[10px] text-zinc-400 block truncate">{crawler.provider} · {crawler.category}</span>
                    </div>

                    <div className="shrink-0">
                      {isAllowed ? (
                        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-950/80 border border-emerald-800/70 text-emerald-300 text-[10px] font-bold">
                          <CheckCircle2 className="w-3 h-3 text-emerald-400" />
                          <span>Allowed</span>
                        </span>
                      ) : isBlocked ? (
                        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-rose-950/80 border border-rose-800/70 text-rose-300 text-[10px] font-bold">
                          <XCircle className="w-3 h-3 text-rose-400" />
                          <span>Blocked</span>
                        </span>
                      ) : isPartial ? (
                        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-950/80 border border-amber-800/70 text-amber-300 text-[10px] font-bold">
                          <AlertTriangle className="w-3 h-3 text-amber-400" />
                          <span>Partial</span>
                        </span>
                      ) : (
                        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-zinc-800 text-zinc-400 text-[10px]">
                          <span>Inherits *</span>
                        </span>
                      )}
                    </div>
                  </div>
                );
              })}
            </div>

          </div>

          <ToolFindingsList findings={findings} />

        </div>

      </div>

      {/* WORKED EXAMPLE */}
      <div className="mb-12">
        <ToolWorkedExample
          scenarioDescription="A digital agency noticed a 40% decline in AI answer citations after adding a broad 'Disallow: /' block under 'User-agent: *' intended to block scrapers."
          inputsTable={[
            { label: 'Wildcard Rule', value: 'Disallow: /' },
            { label: 'OAI-SearchBot', value: 'Inherits Block' },
            { label: 'PerplexityBot', value: 'Inherits Block' },
            { label: 'Claude-SearchBot', value: 'Inherits Block' },
          ]}
          steps={[
            {
              step: 'Inspect Wildcard Inheritance',
              detail: 'Under RFC 9309, specific AI search bots inherit wildcard disallows unless explicitly allowed',
              result: 'All AI bots blocked',
            },
            {
              step: 'Add Specific Search Bot Allow Directives',
              detail: 'Declare explicit "Allow: /" rules for OAI-SearchBot, Claude-SearchBot, and PerplexityBot',
              result: 'Citations restored',
            },
            {
              step: 'Keep Disallow for Bulk Training Bots',
              detail: 'Maintain "Disallow: /" under GPTBot and ClaudeBot to protect proprietary copy from training pools',
              result: 'IP protected',
            },
          ]}
          conclusion="By decoupling AI Search indexers from AI Training scrapers, the agency restored its visibility in ChatGPT Search and Perplexity while keeping its content out of general model training pools."
        />
      </div>

      {/* METHODOLOGY & FORMULAS */}
      <div className="mb-12">
        <ToolMethodologyCard
          methodologyVersion="v1.2 (October 2026)"
          formulas={[
            {
              label: 'AI Search Accessibility Score',
              formula: 'Score_ai = ((Allowed_search_bots / Total_search_bots) × 70) + ((Googlebot_allowed ? 1 : 0) × 30)',
              explanation: 'Weighted accessibility score prioritizing active AI search indexers (OpenAI, Anthropic, Perplexity) and core web crawlers.',
            },
            {
              label: 'RFC 9309 Specificity Match',
              formula: 'Priority = Exact_agent_match > Wildcard_match (*)',
              explanation: 'Standard internet robots exclusion protocol rule resolution order.',
            },
          ]}
          assumptions={[
            'Crawlers adhere to official RFC 9309 standards and respect User-agent declarations.',
            'OpenAI Search and Perplexity prioritize crawlable public pages for real-time citations and source links.',
          ]}
          limitations={[
            'Robots.txt only controls crawler access; it does not guarantee automatic top placement or citation frequency in AI responses.',
            'Malicious non-compliant scrapers may disregard robots.txt entirely.',
          ]}
        />
      </div>

      {/* CORA CONVERSION BRIDGE */}
      <div className="mb-12">
        <ToolContextualCoraCta
          toolSlug="ai-crawler-checker"
          badge="The AI Discovery Bridge"
          heading="Make Your Business Understandable to AI Search Engines."
          description="Cora structures your website with real-time entity Schema markup, clean robots.txt directives, and semantic content architecture that ensures ChatGPT, Claude, and Perplexity understand who you are, what you build, and whom you serve."
          bulletPoints={[
            'Automated AI-ready robots.txt with search/training segregation',
            'Full JSON-LD Organization, Service, and Breadcrumb schema',
            'Real-time GEO content audit for AI answer visibility',
          ]}
          ctaText="Explore Cora's AI & GEO Engine"
          ctaHref="/pricing"
          secondaryText="View Feature Modules"
          secondaryHref="/features"
        />
      </div>

      {/* FAQS */}
      <div className="mb-12">
        <ToolFaqAccordion items={faqs} />
      </div>

      {/* PRIVACY */}
      <ToolPrivacyDisclosure toolName="AI Crawler Access Checker" />

      {/* ── STICKY MOBILE LIVE METRIC BAR ── */}
      <ToolMobileBar
        primaryMetricLabel="AI Search Crawlers"
        primaryMetricValue={`${result.allowedBotsCount}/${result.totalBotsChecked} Allowed (${result.overallScore}%)`}
        secondaryMetricLabel="Readiness Band"
        secondaryMetricValue={result.accessibilityBand.label}
        activeTab={mobileTab}
        onToggleTab={() => setMobileTab(mobileTab === 'inputs' ? 'results' : 'inputs')}
        inputsLabel="robots.txt"
        resultsLabel="Bot Audit"
        onExportCsv={copyOptimizedRobots}
      />

    </ToolContainer>
  );
}
