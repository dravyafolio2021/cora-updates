/**
 * Cora Editorial System — Phase 2: Guides & Playbooks Data Architecture
 * Chaptered, high-value operating guides + lead magnets
 */

import {
  BlogAuthor,
  BlogCategoryId,
  ArticleEditorialStatus,
  ArticleSource,
  EditorialBlock,
  BLOG_AUTHORS,
} from '@/lib/blog-data';

export type GuideCategoryFilter =
  | 'all'
  | 'operations'
  | 'client-management'
  | 'sales-proposals'
  | 'growth'
  | 'ai-automation'
  | 'finance'
  | 'agency-profitability'
  | 'research';

export interface GuideCategoryMeta {
  id: GuideCategoryFilter;
  name: string;
  description: string;
}

export const GUIDE_CATEGORIES: GuideCategoryMeta[] = [
  { id: 'all', name: 'All Guides', description: 'Explore our complete library of agency playbooks and frameworks.' },
  { id: 'operations', name: 'Operations', description: 'Streamline team delivery, capacity planning, and agency operating systems.' },
  { id: 'client-management', name: 'Client Management', description: 'From signed contracts to long-term client retention and satisfaction.' },
  { id: 'sales-proposals', name: 'Sales & Proposals', description: 'Close high-ticket retainers and eliminate ambiguous scopes.' },
  { id: 'growth', name: 'Growth', description: 'Organic client acquisition and agency positioning systems.' },
  { id: 'ai-automation', name: 'AI & Automation', description: 'Leverage autonomous AI co-founders and generative workflows.' },
  { id: 'finance', name: 'Finance', description: 'GST compliance, digital payments, automated retainers, and cash flow.' },
  { id: 'agency-profitability', name: 'Agency Profitability', description: 'Margin optimization, billable rates, and reducing unpriced work.' },
  { id: 'research', name: 'Research', description: 'Data-driven agency benchmarks, state of creative ops, and industry trends.' },
];

export interface InfographicAsset {
  id: string;
  title: string;
  description?: string;
  type: 'process-flow' | 'matrix' | 'hierarchy' | 'timeline' | 'metric-breakdown';
  items?: { label: string; desc: string; icon?: string; badge?: string }[];
  imageUrl?: string;
  imageAlt?: string;
}

export interface KeyTakeaway {
  principle: string;
  description: string;
  actionableStep?: string;
}

export interface ShareableInsight {
  id: string;
  quote: string;
  author: string;
  context: string;
  chapterNumber: string;
  chapterTitle: string;
}

export interface GuideChapter {
  id: string;
  number: string;
  slug: string;
  title: string;
  summary: string;
  readTime: string;
  featuredImage?: string;
  featuredImageAlt?: string;
  blocks: EditorialBlock[];
  infographics?: InfographicAsset[];
  keyTakeaway?: KeyTakeaway;
  shareableInsight?: ShareableInsight;
}

export interface DownloadableAsset {
  assetId: string;
  title: string;
  description: string;
  fileUrl: string;
  fileType: 'pdf' | 'zip' | 'docx' | 'xlsx' | 'template';
  fileSize?: string;
  thumbnail?: string;
  ctaText?: string;
  highlights: string[];
}

export type GuideColorTheme = 'lavender' | 'sky' | 'sage' | 'amber' | 'rose';

export interface Guide {
  slug: string;
  status: ArticleEditorialStatus;
  title: string;
  dek: string;
  excerpt: string;
  coverImage: string;
  coverAlt: string;
  ogImage?: string;
  ogImageAlt?: string;
  shareTitle?: string;
  shareDescription?: string;
  shareText?: string;
  author: BlogAuthor;
  publishedAt: string;
  updatedAt: string;
  category: BlogCategoryId;
  guideCategory: GuideCategoryFilter;
  colorTheme?: GuideColorTheme;
  qualityLabel: string;
  resourceBadges?: ('Playbook' | 'PDF' | 'Templates' | 'Checklist' | 'Research')[];
  tags: string[];
  readTime: string;
  chapterCount: number;
  featured?: boolean;
  canonicalUrl: string;
  seoTitle: string;
  seoDescription: string;
  robots?: string;
  sources?: ArticleSource[];
  faqs?: { question: string; answer: string }[];
  downloadableAsset?: DownloadableAsset;
  relatedArticles?: string[];
  relatedTools?: { title: string; href: string; badge: string; description: string }[];
  relatedGuides?: string[];
  chapters: GuideChapter[];
  shareableInsights?: ShareableInsight[];
}

export const GUIDES_DATA: Guide[] = [
  {
    slug: 'agency-client-onboarding-playbook',
    status: 'published',
    title: 'The Agency Client Onboarding Playbook: From Signed Proposal to Confident First Delivery',
    dek: 'A practical operating system for turning a new client into a well-briefed, well-aligned engagement without drowning the team in forms, follow-ups, access requests, and scattered decisions.',
    excerpt: 'A chaptered agency onboarding playbook covering sales-to-delivery handoff, scope alignment, access collection, kickoff, approvals, first delivery, and the first 30 days.',
    coverImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
    coverAlt: 'Agency Client Onboarding Playbook cover',
    ogImage: '/images/guides/agency-client-onboarding-playbook-og.webp',
    ogImageAlt: 'Agency Client Onboarding Playbook social preview',
    shareTitle: 'The Agency Client Onboarding Playbook',
    shareDescription: 'A practical system for moving from signed proposal to a confident first delivery — without scattered access requests, unclear ownership, or kickoff chaos.',
    shareText: 'A practical agency client onboarding system: scope, access, kickoff, approvals, first delivery, and a downloadable implementation pack.',
    author: BLOG_AUTHORS['dravya-bansal'],
    publishedAt: '2026-09-23',
    updatedAt: '2026-09-27',
    category: 'agency-operations',
    guideCategory: 'operations',
    colorTheme: 'lavender',
    qualityLabel: 'Playbook',
    resourceBadges: ['Playbook', 'PDF', 'Templates', 'Checklist'],
    tags: ['Client Onboarding', 'Agency Operations', 'Client Management', 'Scope Management', 'Service Business'],
    readTime: '28 min read',
    chapterCount: 8,
    featured: true,
    canonicalUrl: 'https://heycora.in/guides/agency-client-onboarding-playbook/',
    seoTitle: 'Agency Client Onboarding Playbook: A Practical Operating System',
    seoDescription: 'A practical agency client onboarding playbook covering sales handoff, scope, access, kickoff, approvals, first delivery, and a downloadable onboarding pack.',
    downloadableAsset: {
      assetId: 'agency-onboarding-pack',
      title: 'Agency Client Onboarding Pack',
      description: 'Ten ready-to-adapt operating templates for moving a client from signed proposal to an organised first month.',
      fileUrl: '/downloads/agency-client-onboarding-playbook.pdf',
      fileType: 'pdf',
      fileSize: '12-Page PDF • 10 Templates',
      ctaText: 'Download Playbook (PDF)',
      highlights: [
        'Client welcome email + information request sheet',
        'Access collection + scope alignment checklists',
        'Kickoff agenda + communication and approval rules',
        '30-day roadmap + internal handoff + change request templates',
      ],
    },
    relatedArticles: [
      'agency-client-onboarding-process',
      'how-to-reduce-agency-scope-creep',
      'client-reporting-system-for-agencies',
    ],
    relatedTools: [
      {
        title: 'Agency Proposal Generator',
        href: '/tools/agency-proposal-generator/',
        badge: 'Free Tool',
        description: 'Turn the commercial conversation into a clearer proposal with outcomes, scope, responsibilities, timeline, and terms.',
      },
      {
        title: 'Cora for Agencies',
        href: '/agency-management-software-india/',
        badge: 'Cora',
        description: 'Keep client context, agreements, projects, approvals, and commercial work connected in one operating layer.',
      },
    ],
    relatedGuides: [
      'agency-scope-creep-defence-system',
      'agency-profitability-margin-guide',
      'high-ticket-retainer-proposal-blueprint',
    ],
    sources: [
      {
        title: 'Scope Management',
        publisher: 'Project Management Institute',
        url: 'https://www.pmi.org/learning/library/scope-management-9099',
      },
      {
        title: 'Scope change control: control your projects or your projects will control you!',
        publisher: 'Project Management Institute',
        url: 'https://www.pmi.org/learning/library/scope-control-projects-you-6972',
        publishDate: '2008-10-19',
      },
      {
        title: 'Effective requirements management',
        publisher: 'Project Management Institute',
        url: 'https://www.pmi.org/learning/library/effective-requirements-management-project-success-8181',
      },
    ],
    faqs: [
      {
        question: 'What should an agency client onboarding process include?',
        answer: 'At minimum: a clean sales-to-delivery handoff, confirmed scope and responsibilities, required business information, secure access collection, a kickoff meeting, communication and approval rules, a clear first milestone, and a documented process for changes.',
      },
      {
        question: 'How long should client onboarding take?',
        answer: 'There is no universal number. A simple retainer may move through onboarding in a day or two, while a larger or regulated engagement can take longer. The useful metric is not speed alone; it is how quickly the team reaches a point where scope, ownership, access, decisions, and the first delivery step are clear.',
      },
      {
        question: 'Should agencies start work before every client access is available?',
        answer: 'Only when the missing access does not block or distort the work. Separate true prerequisites from items that can arrive later, and make the dependency visible so the client understands what is holding up a milestone.',
      },
      {
        question: 'How do you prevent scope creep during onboarding?',
        answer: 'Define the outcome, included work, exclusions, responsibilities, approval owner, and change process before execution. When a new request affects deliverables, timeline, capacity, or responsibility, classify it and agree the impact before the team acts on it.',
      },
    ],
    shareableInsights: [
      {
        id: 'insight-1',
        quote: 'The purpose of onboarding is not to collect information. It is to transfer context without losing meaning.',
        author: 'Dravya Bansal',
        context: 'Chapter 01: Sales to Delivery Handoff',
        chapterNumber: '01',
        chapterTitle: 'Onboarding Is a Handoff Problem Before It Is a Form Problem',
      },
      {
        id: 'insight-2',
        quote: 'Scope creep is not change. It is unpriced, undocumented, or unapproved change.',
        author: 'Dravya Bansal',
        context: 'Chapter 02: Scope, Responsibilities & Change Rules',
        chapterNumber: '02',
        chapterTitle: 'Make Scope, Responsibilities, and Change Rules Visible',
      },
      {
        id: 'insight-3',
        quote: 'A kickoff meeting is successful when the next actions are obvious before the call ends.',
        author: 'Dravya Bansal',
        context: 'Chapter 05: Kickoff Execution',
        chapterNumber: '05',
        chapterTitle: 'Run a Kickoff That Creates Decisions — Not a Second Sales Call',
      },
    ],
    chapters: [
      {
        id: 'chapter-1',
        number: '01',
        slug: 'onboarding-is-a-handoff-problem',
        title: 'Onboarding Is a Handoff Problem Before It Is a Form Problem',
        summary: 'Why the first failure usually happens between sales and delivery — not inside the onboarding checklist.',
        readTime: '4 min read',
        featuredImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
        featuredImageAlt: 'Sales to delivery handoff operating visual',
        shareableInsight: {
          id: 'insight-ch1',
          quote: 'The purpose of onboarding is not to collect information. It is to transfer context without losing meaning.',
          author: 'Dravya Bansal',
          context: 'Chapter 01: Sales to Delivery Handoff',
          chapterNumber: '01',
          chapterTitle: 'Onboarding Is a Handoff Problem Before It Is a Form Problem',
        },
        infographics: [
          {
            id: 'info-ch1',
            title: 'The Sales-to-Delivery Context Bridge',
            type: 'process-flow',
            items: [
              { label: 'Commercial Intent', desc: 'Why the client bought and the pain point they need resolved.', icon: 'Zap' },
              { label: 'Promised Boundaries', desc: 'Exact deliverables, timeline commitments, and exclusions.', icon: 'CheckSquare' },
              { label: 'Decision Authority', desc: 'The single stakeholder with final approval rights.', icon: 'User' },
              { label: 'Delivery Launch', desc: 'The immediate 7-day milestone without re-asking questions.', icon: 'ArrowRight' },
            ],
          },
        ],
        keyTakeaway: {
          principle: 'Protect Context Across the Seam',
          description: 'If the salesperson disappeared for two weeks, would delivery know exactly what to do and why? If not, the handoff is incomplete.',
          actionableStep: 'Create a 1-page internal Engagement Brief before sending any client questionnaire.',
        },
        blocks: [
          {
            type: 'intro',
            content: 'A new client can sign a proposal and still arrive inside delivery as a stranger. Sales remembers the nuance. The founder remembers the promises. The client assumes everyone is aligned. The delivery team opens a project board and sees a name, a fee, and a deadline. That gap is where weak onboarding begins.',
          },
          {
            type: 'statement',
            statement: 'The purpose of onboarding is not to collect information. It is to transfer context without losing meaning.',
            subtext: 'Forms, checklists, portals, and automation are useful only when they make the handoff clearer.',
          },
          {
            type: 'heading',
            level: 2,
            text: 'What needs to survive the sales-to-delivery handoff',
            id: 'what-needs-to-survive-the-handoff',
          },
          {
            type: 'checklist',
            title: 'Minimum handoff context',
            items: [
              { label: 'Why the client bought', description: 'The business problem, pressure, or opportunity that created the engagement.' },
              { label: 'What was actually promised', description: 'Not just the proposal bullets — include meaningful commitments made during calls or negotiation.' },
              { label: 'What success means', description: 'The outcome the client will use to judge whether the engagement is working.' },
              { label: 'What can block delivery', description: 'Dependencies, access, approvals, product constraints, legal reviews, or other known risks.' },
              { label: 'Who can decide', description: 'The person who can approve work, resolve ambiguity, and authorise commercial changes.' },
            ],
          },
          {
            type: 'callout',
            variant: 'example',
            title: 'A useful internal handoff question',
            content: 'If the salesperson disappeared for two weeks, would the delivery team still understand what was sold, why it matters, and what should happen next? If not, the handoff is incomplete.',
          },
          {
            type: 'heading',
            level: 2,
            text: 'Start with a single engagement brief',
            id: 'single-engagement-brief',
          },
          {
            type: 'text',
            content: 'Before sending the client another form, create one internal brief that answers five things in plain language: **problem, outcome, scope, owners, next milestone**. Keep links to the proposal, call notes, commercial terms, and known dependencies underneath it. The brief becomes the team’s starting context instead of forcing every person to reconstruct the sale.',
          },
        ],
      },
      {
        id: 'chapter-2',
        number: '02',
        slug: 'scope-responsibilities-and-change-rules',
        title: 'Make Scope, Responsibilities, and Change Rules Visible',
        summary: 'How to create a baseline that keeps the engagement flexible without making every request free.',
        readTime: '4 min read',
        featuredImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
        featuredImageAlt: 'Scope and change governance framework',
        shareableInsight: {
          id: 'insight-ch2',
          quote: 'Scope creep is not change. It is unpriced, undocumented, or unapproved change.',
          author: 'Dravya Bansal',
          context: 'Chapter 02: Scope & Change Governance',
          chapterNumber: '02',
          chapterTitle: 'Make Scope, Responsibilities, and Change Rules Visible',
        },
        infographics: [
          {
            id: 'info-ch2',
            title: 'The Three-Way Scope Change Mechanism',
            type: 'matrix',
            items: [
              { label: '1. Replace', desc: 'Swap in the new deliverable and remove an existing item of equal effort.', badge: 'Zero Cost' },
              { label: '2. Extend', desc: 'Keep scope unchanged, but extend delivery timeline to free up capacity.', badge: 'Schedule Shift' },
              { label: '3. Add', desc: 'Execute the additional request via an approved change order and extra budget.', badge: 'Billed Work' },
            ],
          },
        ],
        keyTakeaway: {
          principle: 'Make the Baseline Visible',
          description: 'A change is easy to discuss when both sides can clearly see what the original agreement included.',
          actionableStep: 'Always respond: "Yes, we can do that. Here is what it changes to the timeline or budget."',
        },
        blocks: [
          {
            type: 'text',
            content: 'Clients do not need a wall of legal language to understand an engagement. They need a clear answer to four questions: **What are we trying to achieve? What are you doing? What are we doing? What happens when something changes?**',
          },
          {
            type: 'keyTakeaway',
            principle: 'Make the baseline visible',
            description: 'A change is easier to discuss when both sides can see what the original agreement included. PMI scope-management guidance similarly emphasises documenting and approving the baseline before using it to evaluate later changes.',
          },
          {
            type: 'heading',
            level: 2,
            text: 'Use an operating scope, not only a deliverables list',
            id: 'use-an-operating-scope',
          },
          {
            type: 'comparison',
            title: 'Deliverables list vs operating scope',
            leftHeader: 'Deliverables only',
            rightHeader: 'Operating scope',
            rows: [
              { label: 'Outcome', left: 'Often implied', right: 'States what business result the work is intended to support' },
              { label: 'Included work', left: 'Lists outputs', right: 'Lists outputs plus meaningful boundaries' },
              { label: 'Responsibilities', left: 'Usually buried', right: 'Shows what the client and agency each own' },
              { label: 'Approvals', left: 'Handled ad hoc', right: 'Names the approval owner and feedback path' },
              { label: 'Change', left: 'Argued after the request', right: 'Defines the decision process before the request arrives' },
            ],
          },
          {
            type: 'heading',
            level: 2,
            text: 'Use three options when a request changes the plan',
            id: 'three-change-options',
          },
          {
            type: 'steps',
            orientation: 'horizontal',
            steps: [
              { number: '01', title: 'Replace', description: 'Add the new request and remove an existing item of similar effort or priority.' },
              { number: '02', title: 'Extend', description: 'Keep the commercial scope but move the delivery timeline to create capacity.' },
              { number: '03', title: 'Add', description: 'Keep the original plan and approve the additional work commercially.' },
            ],
          },
          {
            type: 'callout',
            variant: 'insight',
            title: 'Do not make “out of scope” the whole conversation',
            content: '“Yes, we can do that. Here is what it changes.” protects the boundary without making the agency feel unhelpful. The goal is not to stop change; it is to make the impact of change visible before the work begins.',
          },
        ],
      },
      {
        id: 'chapter-3',
        number: '03',
        slug: 'collect-information-without-building-a-form-maze',
        title: 'Collect Information Without Building a Form Maze',
        summary: 'Ask only for information that changes a decision, unlocks work, or reduces avoidable back-and-forth.',
        readTime: '3 min read',
        featuredImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
        featuredImageAlt: 'Information architecture intake grouping',
        keyTakeaway: {
          principle: 'Every Question Needs a Job',
          description: 'Keep a question when its answer changes the work, unlocks a dependency, or prevents a mistake. Delete the rest.',
          actionableStep: 'Never ask a client to repeat business information they already shared during sales calls.',
        },
        blocks: [
          {
            type: 'intro',
            content: 'A long onboarding form can feel organised while still producing poor context. The better question is not “What can we ask?” It is “What will the team do differently because we know this?”',
          },
          {
            type: 'heading',
            level: 2,
            text: 'Split information into four useful groups',
            id: 'four-information-groups',
          },
          {
            type: 'steps',
            orientation: 'vertical',
            steps: [
              { number: '01', title: 'Business context', description: 'What the company sells, who it serves, what is changing, and why this engagement matters now.' },
              { number: '02', title: 'Commercial context', description: 'Relevant pricing, offers, sales process, acquisition channels, seasonality, or constraints.' },
              { number: '03', title: 'Brand / product context', description: 'Existing guidelines, assets, product information, previous work, research, and customer insight.' },
              { number: '04', title: 'Decision context', description: 'Who reviews, who approves, who owns implementation, and who must be informed.' },
            ],
          },
          {
            type: 'callout',
            variant: 'warning',
            title: 'Do not ask the client to repeat the sales call',
            content: 'If the client already explained the problem, goals, team, and constraints during sales, carry that context forward. Ask only what is missing or needs confirmation.',
          },
        ],
      },
      {
        id: 'chapter-4',
        number: '04',
        slug: 'collect-access-without-creating-a-security-mess',
        title: 'Collect Access Without Creating a Security Mess',
        summary: 'Separate true prerequisites from optional access and prefer delegated permissions over password sharing.',
        readTime: '3 min read',
        featuredImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
        featuredImageAlt: 'Access verification operating protocol',
        keyTakeaway: {
          principle: 'A Screenshot Is Not Access',
          description: 'Never mark an item complete just because the client says it was shared. Verify active permissions before work begins.',
          actionableStep: 'Use three distinct states: Requested -> Received -> Verified.',
        },
        blocks: [
          {
            type: 'text',
            content: 'Access collection becomes painful when agencies send a giant generic checklist to every client. Build access requests from the work you actually sold. A paid-media engagement, Shopify build, SEO retainer, and branding project should not receive the same access list.',
          },
          {
            type: 'comparison',
            title: 'Better access collection',
            leftHeader: 'Weak pattern',
            rightHeader: 'Better pattern',
            rows: [
              { label: 'Passwords', left: 'Primary login shared in chat', right: 'Delegated / role-based access where the platform supports it' },
              { label: 'Timing', left: 'Ask for everything immediately', right: 'Separate launch blockers from access needed later' },
              { label: 'Ownership', left: '“Please share access”', right: 'Name the client owner responsible for each missing item' },
              { label: 'Status', left: 'Follow up from memory', right: 'One visible checklist with missing, received, and verified states' },
            ],
          },
          {
            type: 'heading',
            level: 2,
            text: 'Use three access states',
            id: 'three-access-states',
          },
          {
            type: 'checklist',
            title: 'Access status language',
            items: [
              { label: 'Requested', description: 'The client knows exactly what is needed and why.' },
              { label: 'Received', description: 'The client has shared / delegated access, but the team has not yet validated it.' },
              { label: 'Verified', description: 'The correct team member has tested that the required permission actually works.' },
            ],
          },
        ],
      },
      {
        id: 'chapter-5',
        number: '05',
        slug: 'run-a-kickoff-that-creates-decisions',
        title: 'Run a Kickoff That Creates Decisions — Not a Second Sales Call',
        summary: 'Use the kickoff to confirm ownership, working rules, dependencies, and the first milestone.',
        readTime: '4 min read',
        featuredImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
        featuredImageAlt: 'Kickoff meeting 30-minute agenda structure',
        shareableInsight: {
          id: 'insight-ch5',
          quote: 'A kickoff meeting is successful when the next actions are obvious before the call ends.',
          author: 'Dravya Bansal',
          context: 'Chapter 05: Kickoff Execution',
          chapterNumber: '05',
          chapterTitle: 'Run a Kickoff That Creates Decisions — Not a Second Sales Call',
        },
        keyTakeaway: {
          principle: 'Close with Unambiguous Action',
          description: 'Repeat the next three actions, assigned owners, and hard dates before hitting leave on the call.',
          actionableStep: 'Ask the closing question: "Is there anything you believe we are doing that is not in the scope?"',
        },
        blocks: [
          {
            type: 'statement',
            statement: 'A kickoff meeting is successful when the next actions are obvious before the call ends.',
            subtext: 'The meeting should reduce ambiguity, not generate another document everyone forgets.',
          },
          {
            type: 'heading',
            level: 2,
            text: 'A practical 30–45 minute agenda',
            id: 'kickoff-agenda',
          },
          {
            type: 'steps',
            orientation: 'vertical',
            steps: [
              { number: '01', title: 'Context — 5 minutes', description: 'Restate why the engagement exists and what changed before it started.' },
              { number: '02', title: 'Outcome — 5–7 minutes', description: 'Confirm the result that matters and the constraints that should not be ignored.' },
              { number: '03', title: 'Scope & responsibilities — 8 minutes', description: 'Confirm deliverables, exclusions, client responsibilities, and agency responsibilities.' },
              { number: '04', title: 'Communication & approvals — 7 minutes', description: 'Name the primary channel, update cadence, final approval owner, and escalation route.' },
              { number: '05', title: 'First delivery cycle — 8 minutes', description: 'Show the first milestone, owner, date, dependencies, and what the client should expect next.' },
              { number: '06', title: 'Close — 2 minutes', description: 'Repeat the next three actions, owners, and dates before the meeting ends.' },
            ],
          },
          {
            type: 'callout',
            variant: 'example',
            title: 'The closing question',
            content: '“Before we end: is there anything you believe we are doing that is not visible in the scope or first delivery plan?” This gives hidden expectations one last chance to surface before execution.',
          },
        ],
      },
      {
        id: 'chapter-6',
        number: '06',
        slug: 'build-communication-approval-and-decision-rules',
        title: 'Build Communication, Approval, and Decision Rules',
        summary: 'Make client collaboration easier by defining where updates, feedback, approvals, and changes should live.',
        readTime: '4 min read',
        featuredImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
        featuredImageAlt: 'Communication map and approval ownership architecture',
        keyTakeaway: {
          principle: 'Decisions Deserve a Durable Record',
          description: 'If a decision affects scope, cost, timeline, or final output, record it where both sides can retrieve it.',
          actionableStep: 'Name one client approval owner who consolidates stakeholder feedback.',
        },
        blocks: [
          {
            type: 'text',
            content: '“We communicate on WhatsApp” is not a communication system. A useful collaboration rule tells both sides **what belongs where** and **who can make which decision**.',
          },
          {
            type: 'table',
            title: 'Simple communication map',
            headers: ['Situation', 'Recommended home', 'Why'],
            rows: [
              ['Routine questions', 'Primary client channel', 'Fast coordination without creating a meeting'],
              ['Weekly status', 'Structured update / report', 'Creates one predictable source of progress'],
              ['Deliverable feedback', 'Approval / review thread', 'Keeps feedback attached to the work'],
              ['Scope / timeline change', 'Written decision record', 'Protects both sides from memory-based disagreement'],
              ['Urgent blocker', 'Escalation route', 'Makes genuine urgency visible'],
            ],
          },
          {
            type: 'heading',
            level: 2,
            text: 'Name one approval owner',
            id: 'name-one-approval-owner',
          },
          {
            type: 'text',
            content: 'Multiple stakeholders can review. One person should still own the final decision. Otherwise the agency can receive five pieces of valid feedback that point in different directions. The approval owner consolidates the client’s position before the team acts.',
          },
        ],
      },
      {
        id: 'chapter-7',
        number: '07',
        slug: 'create-momentum-with-the-first-delivery',
        title: 'Create Momentum With the First Delivery',
        summary: 'Choose a first milestone that proves progress without forcing the team to rush the most important work.',
        readTime: '3 min read',
        featuredImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
        featuredImageAlt: 'First delivery milestone principles',
        keyTakeaway: {
          principle: 'Visible, Meaningful, Low-Regret',
          description: 'Deliver an early milestone that proves momentum without making irreversible architectural or creative bets.',
          actionableStep: 'Ship the initial testing plan, creative territory, or verified structure within 7–10 days.',
        },
        blocks: [
          {
            type: 'intro',
            content: 'Clients feel onboarding is “done” when the engagement starts producing visible movement. That does not mean rushing a major deliverable. It means choosing an early milestone that proves the system is working.',
          },
          {
            type: 'heading',
            level: 2,
            text: 'A useful first milestone has three properties',
            id: 'first-milestone-properties',
          },
          {
            type: 'checklist',
            title: 'First milestone test',
            items: [
              { label: 'Visible', description: 'The client can see or understand what changed.' },
              { label: 'Meaningful', description: 'It moves the engagement toward the agreed outcome instead of existing only to look busy.' },
              { label: 'Low-regret', description: 'It does not require the team to make major irreversible decisions before the necessary context exists.' },
            ],
          },
          {
            type: 'callout',
            variant: 'example',
            title: 'Examples by agency type',
            content: 'A performance agency might deliver an account audit and testing plan. A web agency might confirm information architecture and build the first approved section. A branding studio might align on creative territories before producing the full identity system. The milestone changes; the principle does not.',
          },
        ],
      },
      {
        id: 'chapter-8',
        number: '08',
        slug: 'the-first-30-days-and-the-system-after-onboarding',
        title: 'The First 30 Days: Turn Onboarding Into the Normal Operating Rhythm',
        summary: 'Close the onboarding phase by converting its rules, decisions, and context into the recurring way the account runs.',
        readTime: '3 min read',
        featuredImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
        featuredImageAlt: '30-day operating transition roadmap',
        keyTakeaway: {
          principle: 'Convert Onboarding Into Account Rhythm',
          description: 'Good onboarding ends when the client no longer feels like a new client, and the operating system runs smoothly on autopilot.',
          actionableStep: 'Conduct an onboarding retrospective at Day 30 to turn repeated questions into permanent templates.',
        },
        blocks: [
          {
            type: 'steps',
            orientation: 'vertical',
            steps: [
              { number: 'W1', title: 'Make progress visible', description: 'Deliver the first meaningful milestone and surface blockers before the client needs to ask.' },
              { number: 'W2', title: 'Run the first structured review', description: 'Review progress, collect consolidated feedback, and record requested changes.' },
              { number: 'W3', title: 'Stabilise the rhythm', description: 'Fix recurring access, approval, meeting, or reporting bottlenecks.' },
              { number: 'W4', title: 'Run an onboarding retrospective', description: 'Identify what the client asked twice, what access arrived late, what responsibility was unclear, and what should become a template or automation.' },
            ],
          },
          {
            type: 'heading',
            level: 2,
            text: 'The operating system you want after onboarding',
            id: 'operating-system-after-onboarding',
          },
          {
            type: 'text',
            content: 'By the end of the first month, nobody should need to search old sales calls to understand the engagement. The team should be able to see the client context, current scope, owners, pending approvals, active work, commercial decisions, and next milestone without reconstructing the relationship from memory.',
          },
          {
            type: 'statement',
            statement: 'Good onboarding ends when the client no longer feels like a new client.',
            subtext: 'The rules, context, ownership, and delivery rhythm should become the normal way the account runs.',
          },
        ],
      },
    ],
  },
  {
    slug: 'agency-scope-creep-defence-system',
    status: 'published',
    title: 'The Agency Scope Creep Defence System: Protect Margins Without Annoying Clients',
    dek: 'How leading design and growth agencies price out-of-scope work, manage client feedback loops, and enforce boundary rules with zero friction.',
    excerpt: 'A structured playbook for handling unexpected client requests, change orders, revision limits, and scope boundaries gracefully.',
    coverImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
    coverAlt: 'Scope Creep Defence System cover',
    ogImage: '/images/guides/agency-client-onboarding-playbook-og.webp',
    ogImageAlt: 'Scope Creep Defence System social preview',
    shareTitle: 'The Agency Scope Creep Defence System',
    shareDescription: 'Protect agency margins and eliminate unpriced scope changes with practical change governance frameworks.',
    shareText: 'A structured system for handling unexpected client requests and protecting agency profitability.',
    author: BLOG_AUTHORS['dravya-bansal'],
    publishedAt: '2026-09-20',
    updatedAt: '2026-09-27',
    category: 'agency-operations',
    guideCategory: 'agency-profitability',
    colorTheme: 'sky',
    qualityLabel: 'Playbook',
    resourceBadges: ['Playbook', 'Templates', 'PDF'],
    tags: ['Scope Management', 'Agency Margins', 'Client Contracts', 'Profitability'],
    readTime: '22 min read',
    chapterCount: 5,
    featured: false,
    canonicalUrl: 'https://heycora.in/guides/agency-scope-creep-defence-system/',
    seoTitle: 'Agency Scope Creep Defence System: Protect Margins & Retainers',
    seoDescription: 'A practical framework to eliminate scope creep in creative agencies, establish clear change orders, and protect retainer margins.',
    downloadableAsset: {
      assetId: 'scope-defence-pack',
      title: 'Agency Change Order & Revision Kit',
      description: 'Pre-drafted change order forms, revision policies, and polite pushback email scripts.',
      fileUrl: '/downloads/agency-client-onboarding-playbook.pdf',
      fileType: 'pdf',
      fileSize: '8-Page PDF',
      ctaText: 'Get Revision Kit',
      highlights: ['Change order pricing formula', 'Polite pushback email scripts', 'Revision limit contract clauses'],
    },
    relatedArticles: ['how-to-reduce-agency-scope-creep', 'client-reporting-system-for-agencies'],
    chapters: [
      {
        id: 'scope-ch1',
        number: '01',
        slug: 'why-scope-creep-happens',
        title: 'The Anatomy of Unpriced Scope Creep',
        summary: 'How small favor requests compound into a 30% margin deficit across creative accounts.',
        readTime: '4 min read',
        blocks: [
          {
            type: 'intro',
            content: 'Scope creep rarely happens because of one massive unreasonable request. It happens through twelve tiny favours that each seem too small to bill, but together destroy delivery margin.',
          },
        ],
      },
    ],
  },
  {
    slug: 'high-ticket-retainer-proposal-blueprint',
    status: 'published',
    title: 'The High-Ticket Retainer Proposal Blueprint: Win $5k–$20k Monthly Engagements',
    dek: 'The exact proposal architecture, pricing tiers, commercial guarantees, and discovery mechanics used to close predictable recurring retainers.',
    excerpt: 'Build high-converting agency proposals that anchor value, define mutual responsibilities, and lock in recurring monthly contracts.',
    coverImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
    coverAlt: 'High-Ticket Retainer Proposal Blueprint cover',
    ogImage: '/images/guides/agency-client-onboarding-playbook-og.webp',
    ogImageAlt: 'High-Ticket Retainer Proposal Blueprint social preview',
    shareTitle: 'The High-Ticket Retainer Proposal Blueprint',
    shareDescription: 'The exact proposal architecture and pricing framework used to close $5k-$20k monthly retainers.',
    shareText: 'Win high-ticket agency retainers with structured proposals, value pricing, and clear terms.',
    author: BLOG_AUTHORS['dravya-bansal'],
    publishedAt: '2026-09-18',
    updatedAt: '2026-09-27',
    category: 'growth',
    guideCategory: 'sales-proposals',
    colorTheme: 'sage',
    qualityLabel: 'Playbook',
    resourceBadges: ['Playbook', 'Templates', 'PDF'],
    tags: ['Sales Proposals', 'High-Ticket Retainers', 'Value Pricing', 'Agency Sales'],
    readTime: '24 min read',
    chapterCount: 6,
    featured: false,
    canonicalUrl: 'https://heycora.in/guides/high-ticket-retainer-proposal-blueprint/',
    seoTitle: 'High-Ticket Retainer Proposal Blueprint: Agency Pricing & Sales',
    seoDescription: 'Learn how to write high-converting agency proposals that win $5k-$20k monthly retainers with structured commercial terms.',
    downloadableAsset: {
      assetId: 'retainer-proposal-pack',
      title: 'High-Ticket Agency Proposal Deck & Agreement',
      description: 'Editable Figma & Notion proposal template plus Master Services Agreement (MSA).',
      fileUrl: '/downloads/agency-client-onboarding-playbook.pdf',
      fileType: 'pdf',
      fileSize: '14-Page Deck',
      ctaText: 'Get Proposal Deck',
      highlights: ['3-Tier pricing framework', 'Discovery question sheet', 'Standard agency MSA agreement'],
    },
    relatedArticles: ['agency-client-onboarding-process', 'client-reporting-system-for-agencies'],
    chapters: [
      {
        id: 'prop-ch1',
        number: '01',
        slug: 'diagnose-before-proposing',
        title: 'Diagnose Before You Pitch',
        summary: 'Why generic proposals get price-shopped and how to anchor commercials to business outcomes.',
        readTime: '4 min read',
        blocks: [
          {
            type: 'intro',
            content: 'When an agency sends a proposal with a flat price list, the client evaluates it as an expense. When the proposal articulates the financial outcome and risk mitigation, it becomes an investment.',
          },
        ],
      },
    ],
  },
  {
    slug: 'agency-profitability-margin-guide',
    status: 'published',
    title: 'The Creative Agency Profitability & Margin Optimization Manual',
    dek: 'A CFO-grade guide for agency founders on billable utilization, blended hourly rates, contractor margins, and 18% GST tax efficiency.',
    excerpt: 'Master agency unit economics: calculate true billable capacity, eliminate phantom labor costs, and build a 30%+ net margin business.',
    coverImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
    coverAlt: 'Agency Profitability Manual cover',
    ogImage: '/images/guides/agency-client-onboarding-playbook-og.webp',
    ogImageAlt: 'Agency Profitability Manual social preview',
    shareTitle: 'The Creative Agency Profitability Manual',
    shareDescription: 'Master agency unit economics, calculate billable capacity, and build a 30%+ net margin agency.',
    shareText: 'CFO-grade guide on agency profitability, utilization, and margin optimization.',
    author: BLOG_AUTHORS['dravya-bansal'],
    publishedAt: '2026-09-15',
    updatedAt: '2026-09-27',
    category: 'finance-profitability',
    guideCategory: 'agency-profitability',
    colorTheme: 'amber',
    qualityLabel: 'Guide',
    resourceBadges: ['Research', 'PDF', 'Templates'],
    tags: ['Agency Profitability', 'Unit Economics', 'GST Invoicing', 'Margins'],
    readTime: '26 min read',
    chapterCount: 5,
    featured: false,
    canonicalUrl: 'https://heycora.in/guides/agency-profitability-margin-guide/',
    seoTitle: 'Agency Profitability & Margin Optimization Manual',
    seoDescription: 'Understand billable capacity, overhead allocation, and tax compliance to build a resilient, high-margin creative agency.',
    downloadableAsset: {
      assetId: 'profitability-calculator-sheet',
      title: 'Agency Capacity & Margin Calculator Spreadsheet',
      description: 'Excel & Google Sheets model for tracking team utilization, effective hourly rate, and target gross margin.',
      fileUrl: '/downloads/agency-client-onboarding-playbook.pdf',
      fileType: 'xlsx',
      fileSize: 'Capacity Sheet',
      ctaText: 'Get Calculator Sheet',
      highlights: ['True hourly rate calculator', 'Utilization matrix model', 'Overhead allocation formula'],
    },
    relatedArticles: ['client-reporting-system-for-agencies'],
    chapters: [
      {
        id: 'prof-ch1',
        number: '01',
        slug: 'the-illusion-of-revenue',
        title: 'The Illusion of Top-Line Revenue',
        summary: 'Why growing from $50k to $150k monthly can destroy founder cash flow without proper capacity management.',
        readTime: '5 min read',
        blocks: [
          {
            type: 'intro',
            content: 'Revenue is vanity; margin is sanity; cash is reality. Many agency founders scale their team too early based on gross sales, only to find that net profit dropped.',
          },
        ],
      },
    ],
  },
];

export function getAllGuides(includeUnpublished = false): Guide[] {
  if (includeUnpublished) return GUIDES_DATA;
  return GUIDES_DATA.filter((g) => g.status === 'published');
}

export function getFeaturedGuide(includeUnpublished = false): Guide | undefined {
  const guides = getAllGuides(includeUnpublished);
  return guides.find((g) => g.featured) || guides[0];
}

export function getGuideBySlug(slug: string, includeUnpublished = false): Guide | undefined {
  return GUIDES_DATA.find((g) => {
    if (g.slug !== slug) return false;
    if (!includeUnpublished && g.status !== 'published') return false;
    return true;
  });
}

export function getGuidesByCategory(category: GuideCategoryFilter | string, includeUnpublished = false): Guide[] {
  const all = getAllGuides(includeUnpublished);
  if (!category || category === 'all') return all;
  return all.filter((g) => g.guideCategory === category || g.category === category);
}

export function getAllGuideSlugs(includeUnpublished = false): string[] {
  return getAllGuides(includeUnpublished).map((g) => g.slug);
}
