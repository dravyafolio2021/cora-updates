export type SearchContentPage = {
  slug: string;
  title: string;
  shortTitle: string;
  eyebrow: string;
  description: string;
  intent: string;
  publishedAt: string;
  updatedAt: string;
  summary: string;
  painPoints: { title: string; description: string }[];
  sections: { heading: string; paragraphs: string[]; bullets?: string[] }[];
  comparison: { label: string; wordpress: string; cora: string }[];
  faqs: { question: string; answer: string }[];
};

export const WORDPRESS_CONTENT: SearchContentPage[] = [
  {
    slug: 'alternative-for-agencies',
    title: 'A Practical WordPress Alternative for Service and Creative Agencies',
    shortTitle: 'WordPress alternative for agencies',
    eyebrow: 'WordPress alternative for agencies',
    description:
      'Compare WordPress with Cora for agency websites, lead capture, content publishing, proposals, client workflows, and invoicing—without a forced migration.',
    intent: 'For agency founders searching for a simpler WordPress alternative or a way to make an existing WordPress site operational.',
    publishedAt: '2026-08-30',
    updatedAt: '2026-08-30',
    summary:
      'WordPress is a capable content management system, but most agencies need more than pages and plugins. Cora is designed for the operating work around the website: capturing an enquiry, understanding the brief, following up, preparing documents, and moving the client relationship forward. You can connect it to an existing WordPress site or use Cora as the website and workspace layer when you are ready to simplify further.',
    painPoints: [
      {
        title: 'The website becomes a brochure',
        description: 'The pages stay online, but enquiries, scopes, follow-ups, documents, and payments move into separate tools.',
      },
      {
        title: 'Every improvement needs maintenance',
        description: 'A new form, workflow, or content feature often means another plugin, integration, login, update, and possible conflict.',
      },
      {
        title: 'Publishing loses momentum',
        description: 'Research, drafting, formatting, uploading, metadata, and internal links become a manual chain that is easy to postpone.',
      },
    ],
    sections: [
      {
        heading: 'The real question is not “Can WordPress do it?”',
        paragraphs: [
          'WordPress can be extended to do almost anything. For an agency, the more useful question is how many moving parts the team must maintain to make it happen. A typical stack can include a page builder, form plugin, SEO plugin, caching layer, security plugin, CRM, proposal tool, e-sign tool, invoice tool, automation service, and several spreadsheets.',
          'That stack may be reasonable for an agency with a dedicated WordPress team. It becomes expensive when the founder or a small delivery team owns the maintenance. The cost is not only subscription fees. It is the attention lost every time a lead or piece of content crosses tools.',
        ],
      },
      {
        heading: 'What an agency website should do after someone submits a form',
        paragraphs: [
          'The website should preserve the context that brought the prospect in, create a usable lead record, help the team qualify the opportunity, and make the next step obvious. The visitor should not disappear into a shared inbox while someone copies their details into a CRM later.',
        ],
        bullets: [
          'Capture the service, budget, timeline, source page, and original message.',
          'Create a clear owner and next action for the enquiry.',
          'Turn discovery notes into a scope or proposal without retyping the context.',
          'Connect approval, documents, invoicing, and delivery to the same client record.',
          'Keep the website content connected to the questions prospects actually ask.',
        ],
      },
      {
        heading: 'Keep WordPress or replace it? Use this rule',
        paragraphs: [
          'Keep WordPress when your team publishes comfortably, the site is fast, and the plugin stack is stable. Connect Cora to the enquiries and client operations behind it. This is the lowest-risk path because the public website can stay untouched while the operating workflow improves.',
          'Consider replacing the website layer when routine changes require developer help, nobody publishes because the workflow is tedious, or the site has become inseparable from fragile plugins. Migration should follow evidence from the workflow, not a blanket belief that one platform is always better.',
        ],
      },
    ],
    comparison: [
      { label: 'Primary role', wordpress: 'Website and content management', cora: 'Website-connected business operations' },
      { label: 'Agency lead workflow', wordpress: 'Usually assembled with plugins and external tools', cora: 'Designed around one connected client context' },
      { label: 'Content operations', wordpress: 'Publishing destination; research and preparation are often external', cora: 'Aims to connect planning, creation, and publishing workflows' },
      { label: 'Maintenance model', wordpress: 'Hosting, themes, plugins, updates, and compatibility', cora: 'Managed product with fewer customer-maintained components' },
      { label: 'Migration', wordpress: 'Current site can remain live', cora: 'Can begin as an operating layer; replacement is optional' },
    ],
    faqs: [
      {
        question: 'Is Cora a complete WordPress replacement?',
        answer: 'It can replace parts of a WordPress-led stack, but it does not need to begin as a rip-and-replace project. Agencies can keep their current website and use Cora for the connected lead and client workflow first.',
      },
      {
        question: 'Can I use Cora with an existing WordPress agency website?',
        answer: 'Yes. The practical starting point is to connect enquiry capture and move the post-enquiry workflow into Cora while leaving the public site in place.',
      },
      {
        question: 'Who should stay on WordPress?',
        answer: 'Stay when your team publishes regularly, performance is good, and the stack is easy to maintain. The case for change is strongest when operational work is fragmented or routine publishing has stopped.',
      },
    ],
  },
  {
    slug: 'elementor-alternative-for-agencies',
    title: 'Elementor Alternative for Agencies That Are Tired of Maintaining Pages',
    shortTitle: 'Elementor alternative for agencies',
    eyebrow: 'Elementor alternative for agencies',
    description:
      'A candid Elementor vs Cora guide for agencies frustrated by page maintenance, plugin dependencies, slow publishing, and disconnected lead workflows.',
    intent: 'For teams searching for an Elementor alternative because page editing and maintenance are consuming delivery time.',
    publishedAt: '2026-08-30',
    updatedAt: '2026-08-30',
    summary:
      'Elementor solves visual page building. It does not automatically solve the agency workflow around those pages. If the frustration is design freedom, keep a visual builder. If the frustration is that every form, article, lead, and client action lives in a different place, the better alternative is a connected website-and-workspace system.',
    painPoints: [
      { title: 'Simple edits feel risky', description: 'Global styles, responsive settings, template conditions, and add-ons can make a small change affect several pages.' },
      { title: 'The editor is not the workflow', description: 'The page can look finished while forms, follow-up, proposals, and client records remain disconnected.' },
      { title: 'Content waits for a specialist', description: 'Founders delay useful pages because formatting and publishing still depend on the person who understands the build.' },
    ],
    sections: [
      {
        heading: 'When Elementor is still the right choice',
        paragraphs: [
          'Elementor remains useful when visual control is the highest priority, the team has reliable WordPress skills, and reusable templates make publishing predictable. A mature agency can operate it efficiently when ownership is clear and the add-on stack is intentionally limited.',
          'Do not migrate only because another platform sounds newer. A working site with a repeatable publishing process is an asset. The decision should begin with the bottleneck you are trying to remove.',
        ],
      },
      {
        heading: 'When the page builder has become an operating bottleneck',
        paragraphs: [
          'The warning sign is not that Elementor lacks features. It is that the team avoids touching the site. Service pages become stale, case studies sit in documents, and the blog is empty because publishing requires too many decisions and handoffs.',
          'At that point, adding another widget does not address the problem. The system needs a simpler content model and a direct connection between what prospects search for, what the agency publishes, and what happens after a visitor asks for help.',
        ],
        bullets: [
          'A non-developer should be able to publish from an approved structure.',
          'Metadata, schema, canonical URLs, and internal links should follow consistent rules.',
          'Forms should pass useful context into the lead workflow.',
          'The team should not rebuild the same layout for every problem or comparison page.',
        ],
      },
      {
        heading: 'A low-risk path away from Elementor dependency',
        paragraphs: [
          'Start with new search pages rather than redesigning the entire site. Publish high-intent pages in a structured system, measure whether they attract relevant visitors and conversations, and move the rest of the site only when the new workflow proves easier.',
          'Cora can also sit behind the existing Elementor site first. That separates the website migration decision from the urgent need to improve enquiry handling and client operations.',
        ],
      },
    ],
    comparison: [
      { label: 'Best at', wordpress: 'Visual WordPress page composition', cora: 'Connecting the website to business workflows' },
      { label: 'Publishing model', wordpress: 'Canvas-based editing with templates and responsive controls', cora: 'Structured, reusable page and content workflows' },
      { label: 'Forms and follow-up', wordpress: 'Depends on form, CRM, and automation setup', cora: 'Built to retain context across the client journey' },
      { label: 'Ongoing ownership', wordpress: 'Needs someone comfortable with WordPress and Elementor', cora: 'Designed for an operating team, not only a site specialist' },
    ],
    faqs: [
      { question: 'Is Cora a drag-and-drop page builder like Elementor?', answer: 'No. Elementor is primarily a visual page builder. Cora is positioned as a connected website and business workspace, so the comparison is about the operating system around the pages, not widget-for-widget parity.' },
      { question: 'Do I need to rebuild my Elementor site to use Cora?', answer: 'No. You can keep the current site and connect the lead workflow first. A page migration can be tested incrementally with new search-led pages.' },
      { question: 'What should an agency migrate first?', answer: 'Start with the pages the team cannot publish consistently: service problem pages, comparisons, use cases, and articles tied to real prospect questions.' },
    ],
  },
  {
    slug: 'woocommerce-alternative-for-service-businesses',
    title: 'WooCommerce Alternative for Service Businesses Selling Projects, Not Products',
    shortTitle: 'WooCommerce alternative for services',
    eyebrow: 'WooCommerce alternative for service businesses',
    description:
      'Compare WooCommerce with Cora for agencies and service businesses that sell scoped projects, retainers, deposits, approvals, and milestones rather than a product catalog.',
    intent: 'For service businesses using WooCommerce as a workaround for quotes, deposits, custom scopes, and project payments.',
    publishedAt: '2026-08-30',
    updatedAt: '2026-08-30',
    summary:
      'WooCommerce is excellent when the transaction is a product in a cart. Service work usually begins with uncertainty: the scope, timeline, people, approval, deposit, and deliverables must be agreed before payment. Cora is a better conceptual fit when the sale is a relationship and a workflow rather than a standard SKU.',
    painPoints: [
      { title: 'A project is forced into a product model', description: 'Custom scopes, phased deliverables, and changing requirements do not behave like inventory in a cart.' },
      { title: 'Checkout begins too early', description: 'High-consideration services often need qualification and agreement before the buyer is ready to pay.' },
      { title: 'Plugins reconstruct the missing workflow', description: 'Deposits, bookings, forms, invoices, subscriptions, and documents accumulate as separate extensions.' },
    ],
    sections: [
      {
        heading: 'WooCommerce is not the problem—the transaction model is',
        paragraphs: [
          'For a store with defined products, prices, stock, shipping, and returns, WooCommerce is a strong choice. Its ecosystem exists for commerce. Difficulties appear when an agency, consultant, production house, or real estate service tries to represent a negotiated engagement as a product.',
          'A ₹2,000 template and a ₹2,00,000 branding project do not need the same buying flow. The service engagement needs discovery, scope, responsibility, approvals, documents, staged payments, and ongoing communication.',
        ],
      },
      {
        heading: 'The service-business flow to design instead',
        paragraphs: [
          'Begin with intent and qualification, not a cart. Capture what the buyer is trying to achieve, preserve the source page and context, and make the next step appropriate to the complexity of the work.',
        ],
        bullets: [
          'Enquiry with service, budget range, urgency, and useful context.',
          'Discovery notes attached to the same opportunity.',
          'A scope or proposal that can be revised and approved.',
          'A clear agreement and deposit or milestone schedule.',
          'Delivery, approval, final billing, and follow-up in one client history.',
        ],
      },
      {
        heading: 'When to keep WooCommerce',
        paragraphs: [
          'Keep WooCommerce for fixed-price products, paid downloads, tickets, standardized packages, or any offer that genuinely benefits from cart and checkout behavior. A business can use WooCommerce for products and Cora for service enquiries without forcing one system to handle both transaction types.',
          'This distinction also produces clearer search pages. Product shoppers can land on commerce pages; service buyers can land on pages that answer their problem and invite a scoped conversation.',
        ],
      },
    ],
    comparison: [
      { label: 'Transaction model', wordpress: 'Products, carts, checkout, and orders', cora: 'Enquiries, scopes, approvals, and client workflows' },
      { label: 'Best fit', wordpress: 'Standardized products and packages', cora: 'Custom services, projects, and retainers' },
      { label: 'Payment timing', wordpress: 'Usually at checkout', cora: 'After qualification or at agreed milestones' },
      { label: 'Context after purchase', wordpress: 'Order record plus extensions', cora: 'One continuing client and project context' },
    ],
    faqs: [
      { question: 'Should a service business stop using WooCommerce?', answer: 'Only if most sales require discovery, custom scope, approval, or milestone billing. Keep WooCommerce for offers that genuinely work as standardized products.' },
      { question: 'Can Cora and WooCommerce be used together?', answer: 'Yes. Use WooCommerce for product-like transactions and route custom service enquiries into Cora.' },
      { question: 'What is the main difference between WooCommerce and Cora?', answer: 'WooCommerce organizes commerce around products and orders. Cora organizes service work around enquiries, clients, scopes, documents, and the actions that follow.' },
    ],
  },
  {
    slug: 'content-publishing-workflow-for-agencies',
    title: 'A Simpler Website Content Publishing Workflow for Busy Agencies',
    shortTitle: 'Agency content publishing workflow',
    eyebrow: 'WordPress content workflow for agencies',
    description:
      'A practical workflow for agencies that struggle to research, write, format, optimize, and publish useful website content consistently in WordPress.',
    intent: 'For agency founders whose blog and service pages stay outdated because publishing is fragmented and tedious.',
    publishedAt: '2026-08-30',
    updatedAt: '2026-08-30',
    summary:
      'Consistency does not come from asking the founder to “write more.” It comes from removing repeated decisions and tool handoffs. A useful agency content system begins with real buyer questions, turns each question into a structured brief, and carries the approved answer through metadata, internal links, schema, and publishing without rebuilding the process every time.',
    painPoints: [
      { title: 'Research starts from zero', description: 'The writer searches broadly instead of beginning with sales calls, enquiries, objections, and delivery experience.' },
      { title: 'The draft moves across tools', description: 'Context is lost while copying between research, AI, documents, project management, and WordPress.' },
      { title: 'SEO happens at the end', description: 'Titles, internal links, FAQs, metadata, and schema become a final checklist rather than part of the content model.' },
    ],
    sections: [
      {
        heading: 'Use a question-driven content queue',
        paragraphs: [
          'Every useful page should answer a question that appears in a prospect conversation, support message, proposal objection, or delivery discussion. Record the exact language, the audience, the stage of the decision, and the action the reader should take next.',
          'For Cora, “WordPress alternative” is too broad on its own. The actionable questions are narrower: why an agency stopped publishing, whether Elementor is still worth maintaining, or whether WooCommerce fits a custom service business.',
        ],
      },
      {
        heading: 'Define one publish-ready page contract',
        paragraphs: [
          'A page contract is the minimum complete set of fields required to publish. When these fields live together, the team does not need to remember a separate SEO routine for every article.',
        ],
        bullets: [
          'Primary audience, search intent, and one clear question.',
          'Unique title, description, URL slug, and canonical URL.',
          'Direct answer near the top, followed by evidence and practical detail.',
          'Three to five internal links chosen by topic relationship.',
          'Visible FAQs that match the structured data.',
          'Published and updated dates, author or organization, and a next action.',
        ],
      },
      {
        heading: 'Publish clusters, not fifty unrelated articles',
        paragraphs: [
          'A cluster starts with a hub that explains the broad problem and routes readers to specific answers. The supporting pages should cover distinct intent rather than repeat the same keyword with minor wording changes. This gives people and search systems a clear map of the subject.',
          'The first Cora cluster links WordPress frustration to four distinct decisions: keep or replace WordPress, move away from Elementor dependence, choose a service workflow instead of a cart, and repair the content publishing process itself. Future pages should be added only when they answer a new repeated question.',
        ],
      },
    ],
    comparison: [
      { label: 'Idea source', wordpress: 'Often an ad hoc keyword list', cora: 'Buyer questions and observed workflow pain' },
      { label: 'Draft structure', wordpress: 'Recreated for each post', cora: 'Reusable publish-ready content contract' },
      { label: 'SEO work', wordpress: 'Frequently added after writing', cora: 'Metadata, links, schema, and intent planned together' },
      { label: 'Success signal', wordpress: 'Number of posts published', cora: 'Relevant discovery, conversations, activation, and learning' },
    ],
    faqs: [
      { question: 'How often should a small agency publish?', answer: 'Use a pace the team can sustain while maintaining quality. One strong page that answers a repeated buyer question is more useful than several generic posts created only to meet a calendar.' },
      { question: 'Should every agency article target a keyword?', answer: 'Every page should serve a clear discovery or buyer intent, but not every useful page needs large search volume. Prioritize relevance to the audience and a distinct question.' },
      { question: 'What should an agency publish first?', answer: 'Start with the problems already heard in sales: alternatives, comparisons, cost or workflow objections, implementation questions, and examples of how the work changes.' },
    ],
  },
];

export const WORDPRESS_CONTENT_BY_SLUG = Object.fromEntries(
  WORDPRESS_CONTENT.map((page) => [page.slug, page]),
) as Record<string, SearchContentPage>;
