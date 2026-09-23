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

export interface GuideChapter {
  number: string;
  slug: string;
  title: string;
  summary?: string;
  readTime?: string;
  blocks: EditorialBlock[];
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
  qualityLabel: string;
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
    updatedAt: '2026-09-23',
    category: 'agency-operations',
    qualityLabel: 'Playbook',
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
      fileUrl: '/api/guides/download/agency-onboarding-pack',
      fileType: 'template',
      fileSize: '10 practical templates',
      ctaText: 'Get the Onboarding Pack',
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
    relatedGuides: [],
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
    chapters: [
      {
        number: '01',
        slug: 'onboarding-is-a-handoff-problem',
        title: 'Onboarding Is a Handoff Problem Before It Is a Form Problem',
        summary: 'Why the first failure usually happens between sales and delivery — not inside the onboarding checklist.',
        readTime: '4 min',
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
        number: '02',
        slug: 'scope-responsibilities-and-change-rules',
        title: 'Make Scope, Responsibilities, and Change Rules Visible',
        summary: 'How to create a baseline that keeps the engagement flexible without making every request free.',
        readTime: '4 min',
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
          {
            type: 'text',
            content: 'Project-management guidance consistently treats approved changes as something that should be identified, assessed, communicated, and authorised before changed work begins. That principle scales down well to agency work: when a request changes scope, schedule, cost, or responsibility, record the decision before execution. [PMI: Scope Management](https://www.pmi.org/learning/library/scope-management-9099)',
          },
        ],
      },
      {
        number: '03',
        slug: 'collect-information-without-building-a-form-maze',
        title: 'Collect Information Without Building a Form Maze',
        summary: 'Ask only for information that changes a decision, unlocks work, or reduces avoidable back-and-forth.',
        readTime: '3 min',
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
          {
            type: 'keyTakeaway',
            principle: 'Every question needs a job',
            description: 'Keep a question when its answer changes the work, unlocks a dependency, assigns ownership, or prevents a predictable misunderstanding. Delete the rest.',
          },
        ],
      },
      {
        number: '04',
        slug: 'collect-access-without-creating-a-security-mess',
        title: 'Collect Access Without Creating a Security Mess',
        summary: 'Separate true prerequisites from optional access and prefer delegated permissions over password sharing.',
        readTime: '3 min',
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
          {
            type: 'callout',
            variant: 'note',
            title: 'A screenshot is not access',
            content: 'Do not mark an item complete because the client says it was shared. Verify the permission before scheduling work that depends on it.',
          },
        ],
      },
      {
        number: '05',
        slug: 'run-a-kickoff-that-creates-decisions',
        title: 'Run a Kickoff That Creates Decisions — Not a Second Sales Call',
        summary: 'Use the kickoff to confirm ownership, working rules, dependencies, and the first milestone.',
        readTime: '4 min',
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
        number: '06',
        slug: 'build-communication-approval-and-decision-rules',
        title: 'Build Communication, Approval, and Decision Rules',
        summary: 'Make client collaboration easier by defining where updates, feedback, approvals, and changes should live.',
        readTime: '4 min',
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
          {
            type: 'keyTakeaway',
            principle: 'Decisions deserve a durable record',
            description: 'If a decision affects scope, cost, timeline, or final output, record it somewhere both sides can retrieve later. PMI requirements guidance similarly emphasises documented approval and controlled handling of requested changes.',
          },
          {
            type: 'text',
            content: '[PMI: Effective requirements management](https://www.pmi.org/learning/library/effective-requirements-management-project-success-8181)',
          },
        ],
      },
      {
        number: '07',
        slug: 'create-momentum-with-the-first-delivery',
        title: 'Create Momentum With the First Delivery',
        summary: 'Choose a first milestone that proves progress without forcing the team to rush the most important work.',
        readTime: '3 min',
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
          {
            type: 'newsletter',
            heading: 'Build a calmer agency operating system.',
            tagline: 'Get one practical workflow, operating framework, or client-management system every week.',
            buttonText: 'Join Free',
            placement: 'inline',
          },
        ],
      },
      {
        number: '08',
        slug: 'the-first-30-days-and-the-system-after-onboarding',
        title: 'The First 30 Days: Turn Onboarding Into the Normal Operating Rhythm',
        summary: 'Close the onboarding phase by converting its rules, decisions, and context into the recurring way the account runs.',
        readTime: '3 min',
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
            type: 'productMention',
            contextText: 'Cora is designed for this connected operating layer: keep client context, proposals, delivery, approvals, and commercial work together instead of spreading the engagement across disconnected systems.',
            actionText: 'See Cora for agencies →',
            actionHref: '/agency-management-software-india/',
          },
          {
            type: 'contextualCTA',
            badge: 'FREE TOOL',
            title: 'Start before onboarding: make the proposal clearer',
            description: 'Use the Agency Proposal Generator to structure outcomes, scope, responsibilities, commercials, and terms before the handoff begins.',
            ctaText: 'Build Proposal',
            ctaHref: '/tools/agency-proposal-generator/',
            destinationType: 'tool',
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

export function getGuidesByCategory(category: BlogCategoryId, includeUnpublished = false): Guide[] {
  return getAllGuides(includeUnpublished).filter((g) => g.category === category);
}

export function getAllGuideSlugs(includeUnpublished = false): string[] {
  return getAllGuides(includeUnpublished).map((g) => g.slug);
}
