export interface IntegrationData {
  slug: string;
  name: string;
  tagline: string;
  category: 'Website Builder' | 'CMS & Ecommerce' | 'Modern Framework';
  heroDescription: string;
  badgeColor: string;
  stats: {
    setupTime: string;
    pluginsReplaced: string;
    monthlySavings: string;
  };
  whyItMatters: string;
  painPoints: {
    title: string;
    description: string;
  }[];
  stepByStepGuide: {
    step: string;
    title: string;
    description: string;
    codeSnippet?: string;
  }[];
  capabilities: {
    title: string;
    description: string;
  }[];
  faqs: {
    question: string;
    answer: string;
  }[];
}

export const INTEGRATIONS_LIST: IntegrationData[] = [
  {
    slug: 'framer',
    name: 'Framer',
    tagline: 'The Autonomous Client Portal, E-Sign & 18% GST Engine for Framer Sites',
    category: 'Website Builder',
    heroDescription: 'Transform any Framer portfolio or agency template into a full autonomous business backend. Connect your Framer contact forms to Cora’s Kanban CRM, collect SHA-256 legal signatures, and dispatch automated 18% GST invoices in 2 minutes.',
    badgeColor: 'sky',
    stats: {
      setupTime: '< 2 mins',
      pluginsReplaced: '4 3rd-party tools',
      monthlySavings: '$180 / mo',
    },
    whyItMatters: 'Framer is world-class for modern web design and animations, but lacks built-in client portals, Indian 18% GST tax calculation, legal e-signature contracts, and automated WhatsApp shoot dispatch. Cora bridges the gap seamlessly.',
    painPoints: [
      {
        title: 'No Native E-Signature Contracts',
        description: 'Framer forms cannot legally bind clients to project terms, requiring expensive DocuSign or PandaDoc subscriptions.',
      },
      {
        title: 'Missing Indian GST & Automated Invoicing',
        description: 'Framer Ecommerce lacks CGST/SGST/IGST breakdown and automated B2B PDF invoice generation.',
      },
      {
        title: 'Disconnected Form Submissions',
        description: 'Form submissions get buried in email inboxes instead of automatically creating deals in a Kanban pipeline.',
      },
    ],
    stepByStepGuide: [
      {
        step: '01',
        title: 'Copy the 1-Line Cora Client Script',
        description: 'Grab your unique workspace embed code snippet from your Cora Settings.',
        codeSnippet: '<script src="https://app.heycora.in/embed/cora-widget.js" data-workspace="YOUR_WORKSPACE_ID" async></script>',
      },
      {
        step: '02',
        title: 'Paste into Framer Custom Code Settings',
        description: 'Open your Framer Project Settings -> General -> Custom Code -> End of <body> tag and paste the script.',
      },
      {
        step: '03',
        title: 'Map Form Actions Automatically',
        description: 'All Framer form submissions will now instantly trigger Cora’s AI voice-to-scope engine, create CRM deals, and dispatch client contracts.',
      },
    ],
    capabilities: [
      {
        title: 'Embeddable 18% GST Calculator Widget',
        description: 'Let prospective clients calculate commercial photo/film shoot budgets directly on your Framer pricing page.',
      },
      {
        title: 'Client Portal Floating Drawer',
        description: 'Clients can view signed shoot agreements, milestone deliverables, and invoices without leaving your Framer site.',
      },
      {
        title: 'Instant WhatsApp & Email Notification',
        description: 'Every form inquiry triggers an immediate AI-generated proposal draft and WhatsApp dispatch to your team.',
      },
    ],
    faqs: [
      {
        question: 'Does this slow down my Framer website?',
        answer: 'Not at all. The Cora script is lightweight (< 14KB gzipped), loaded asynchronously over global CDN edge nodes with zero render-blocking delay.',
      },
      {
        question: 'Do I need a Framer paid plan to use Cora?',
        answer: 'You can use Cora embeds on any Framer custom domain or iframe component. Custom code injection requires a Framer Mini or Basic plan.',
      },
      {
        question: 'Are digital signatures on Framer forms legally valid in India?',
        answer: 'Yes. Cora cryptographically timestamps every signature with SHA-256 hash, IP address, and browser fingerprint compliant with Section 65B of the Indian IT Act 2000.',
      },
    ],
  },
  {
    slug: 'webflow',
    name: 'Webflow',
    tagline: 'Supercharge Webflow with AI Proposals, SHA-256 E-Signs & GST Invoicing',
    category: 'Website Builder',
    heroDescription: 'Connect Webflow CMS forms, logic, and client portals directly to Cora’s autonomous operating system. Eliminate disjointed Zapier zaps, expensive CRM add-ons, and manual PDF invoice generation.',
    badgeColor: 'blue',
    stats: {
      setupTime: '< 3 mins',
      pluginsReplaced: '5 Zapier Zaps + CRM',
      monthlySavings: '$220 / mo',
    },
    whyItMatters: 'Webflow gives creative teams incredible visual control, but orchestrating post-inquiry workflows (client proposals, legal contracts, gear checkouts, and GST invoicing) typically requires 5+ complex integrations. Cora handles it out-of-the-box.',
    painPoints: [
      {
        title: 'Zapier & Make Fatigue',
        description: 'Maintaining complex multi-step zaps to sync Webflow form submissions into CRMs and e-sign platforms causes frequent breakages.',
      },
      {
        title: 'No Multi-Day Shoot Scheduling',
        description: 'Webflow booking forms cannot handle complex creative crew dispatch or equipment inventory management.',
      },
      {
        title: 'Expensive CRM Seat Penalties',
        description: 'Hooking Webflow to HubSpot or Salesforce adds hundreds of dollars per month in per-seat charges.',
      },
    ],
    stepByStepGuide: [
      {
        step: '01',
        title: 'Generate Cora Webflow Webhook Endpoint',
        description: 'In your Cora dashboard, generate a dedicated Webflow Ingest Webhook URL.',
      },
      {
        step: '02',
        title: 'Add Form Action in Webflow Designer',
        description: 'Set your Webflow form method to POST and action URL to your Cora webhook endpoint.',
      },
      {
        step: '03',
        title: 'Embed the Floating Client Vault Widget',
        description: 'Paste the Cora client portal embed script into Webflow Page Settings -> Footer Code.',
        codeSnippet: '<script src="https://app.heycora.in/embed/cora-webflow.js" data-token="WORKSPACE_TOKEN" async></script>',
      },
    ],
    capabilities: [
      {
        title: 'Native Webflow Form Webhooks',
        description: 'Instant lead parsing, AI voice-to-scope draft creation, and revenue forecasting directly from Webflow forms.',
      },
      {
        title: 'Client Review & Approval Portals',
        description: 'Clients can approve 4K RAW proofing galleries and sign shoot contracts directly through your branded domain.',
      },
      {
        title: 'Automated 18% GST B2B Invoicing',
        description: 'Generate and send CBIC-compliant GST invoices with CGST/SGST/IGST breakdown upon deal closing.',
      },
    ],
    faqs: [
      {
        question: 'Can I map custom form fields from Webflow into Cora CRM?',
        answer: 'Yes. Cora automatically detects and maps standard fields (Name, Email, Phone, Shoot Date, Budget, Location) and stores extra data in custom CRM metadata.',
      },
      {
        question: 'Does this work with Webflow Logic?',
        answer: 'Yes, you can trigger Cora webhooks either via standard Webflow Form submissions or via Webflow Logic automation blocks.',
      },
    ],
  },
  {
    slug: 'wordpress',
    name: 'WordPress',
    tagline: 'Replace 6 Bloated Plugins with One High-Performance Autonomous OS',
    category: 'CMS & Ecommerce',
    heroDescription: 'Eliminate heavy WordPress plugin bloat. Replace WooCommerce GST extensions, Gravity Forms, WP E-Signature, WP ERP, and backup plugins with one ultra-fast, lightweight Cora integration.',
    badgeColor: 'emerald',
    stats: {
      setupTime: '< 2 mins',
      pluginsReplaced: '6 Heavy Plugins',
      monthlySavings: '₹15,000 / mo',
    },
    whyItMatters: 'Managing creative studios on WordPress often leads to plugin conflicts, slow database queries, and vulnerable e-sign extensions. Cora moves heavy business logic off your WordPress server into an ultra-fast cloud engine.',
    painPoints: [
      {
        title: 'Plugin Conflicts & Slow Page Load',
        description: 'Running multiple commercial CRM, invoice, and contract plugins drags down WordPress Core Web Vitals and TTFB.',
      },
      {
        title: 'Fragile E-Signature Storage',
        description: 'Storing legal contracts in standard WordPress MySQL tables risks data loss and lacks enterprise SHA-256 audit compliance.',
      },
      {
        title: 'Manual WhatsApp & Email Dispatch',
        description: 'WordPress SMTP plugins frequently land client notifications in spam folders.',
      },
    ],
    stepByStepGuide: [
      {
        step: '01',
        title: 'Install Cora Connector Snippet',
        description: 'Add our lightweight 1-line script via your theme functions.php or any header/footer code manager plugin.',
        codeSnippet: '<script src="https://app.heycora.in/embed/cora-wp.js" data-api-key="YOUR_API_KEY" async></script>',
      },
      {
        step: '02',
        title: 'Connect Existing Contact Form 7 / Elementor Forms',
        description: 'Cora automatically listens to Elementor, WPForms, and Gravity Forms submissions without altering your page layouts.',
      },
      {
        step: '03',
        title: 'Enjoy Instant CRM & Automated GST Invoicing',
        description: 'All inquiries flow directly into your visual Kanban board with automated client contract dispatch.',
      },
    ],
    capabilities: [
      {
        title: 'Elementor & Gutenberg Form Capture',
        description: 'Zero-configuration listener for existing WordPress form builders.',
      },
      {
        title: '18% GST Invoicing with HSN/SAC Code Support',
        description: 'Compliant with Indian Tax Code (SAC 9983 for photography and media production).',
      },
      {
        title: 'Cloud Document Vault Isolation',
        description: 'Contracts are safely encrypted in AWS/Tier-4 cloud vaults instead of vulnerable local WordPress media folders.',
      },
    ],
    faqs: [
      {
        question: 'Will this conflict with WooCommerce or Elementor?',
        answer: 'No. Cora operates externally via clean JavaScript APIs and webhooks, keeping your WordPress database 100% clean and fast.',
      },
      {
        question: 'Can I replace WooCommerce invoicing with Cora?',
        answer: 'Yes! Cora generates full B2B GST PDF invoices with QR codes, bank payment links, and automated reconciliation.',
      },
    ],
  },
  {
    slug: 'shopify',
    name: 'Shopify',
    tagline: 'High-Ticket Commercial Quotes, Custom Scope & 18% GST for Shopify',
    category: 'CMS & Ecommerce',
    heroDescription: 'Transform standard Shopify product catalogs into custom creative service workflows. Capture custom photography, videography, and branding quotes, collect legally binding contracts, and issue 18% GST invoices.',
    badgeColor: 'purple',
    stats: {
      setupTime: '< 3 mins',
      pluginsReplaced: '3 B2B Invoice Apps',
      monthlySavings: '$120 / mo',
    },
    whyItMatters: 'Shopify is great for direct-to-consumer checkouts, but fails when selling high-ticket custom production packages, requiring deposit milestones, custom scope contracts, and Indian B2B GST tax invoices.',
    painPoints: [
      {
        title: 'Rigid Checkout Flows',
        description: 'Shopify cannot handle multi-step milestone payments (50% booking deposit + 50% on final RAW asset delivery).',
      },
      {
        title: 'Missing E-Signature Contracts',
        description: 'No way to collect legally binding model releases, shoot waivers, or copyright licensing terms during checkout.',
      },
      {
        title: 'Complex Indian B2B GST Invoicing',
        description: 'Shopify default invoices lack mandatory B2B GSTIN input fields, CGST/SGST breakdowns, and SAC codes.',
      },
    ],
    stepByStepGuide: [
      {
        step: '01',
        title: 'Add Cora Custom Quote Button',
        description: 'Add our custom quote button next to standard "Add to Cart" on your Shopify theme.',
        codeSnippet: '<button class="cora-quote-btn" data-service="Commercial Shoot Package">Request Custom Quote &amp; Scope</button>',
      },
      {
        step: '02',
        title: 'Include the Cora Embed Script',
        description: 'Paste the Cora script into your theme.liquid right before </head>.',
      },
      {
        step: '03',
        title: 'Automate Milestone Invoicing',
        description: 'Convert custom quote requests into signed contracts and 18% GST invoices with split deposit billing.',
      },
    ],
    capabilities: [
      {
        title: 'Milestone Stage Billing (50/50 Split)',
        description: 'Collect initial shoot retainer deposits and automate final balance collection upon gallery approval.',
      },
      {
        title: 'Commercial Licensing Agreements',
        description: 'Attach legally binding usage rights and copyright transfer agreements to order checkouts.',
      },
    ],
    faqs: [
      {
        question: 'Does this work with Shopify Plus and standard Shopify?',
        answer: 'Yes, Cora works seamlessly with all Shopify plans (Basic, Shopify, Advanced, and Plus).',
      },
    ],
  },
  {
    slug: 'nextjs',
    name: 'Next.js & React',
    tagline: 'The Headless Business OS API & React SDK for Custom Studio Websites',
    category: 'Modern Framework',
    heroDescription: 'Build bespoke developer-first client experiences on Next.js, React, and Tailwind CSS. Use Cora’s headless APIs and React components for CRM deal ingestion, real-time GST tax calculations, and digital signatures.',
    badgeColor: 'zinc',
    stats: {
      setupTime: '< 1 min',
      pluginsReplaced: 'Entire backend stack',
      monthlySavings: '$300+ / mo',
    },
    whyItMatters: 'Developers building custom Next.js websites for agencies and studios shouldn’t have to build custom CRM backends, tax engines, or PDF generators from scratch. Cora gives you a ready-made headless API.',
    painPoints: [
      {
        title: 'Building Tax & Invoice Logic from Scratch',
        description: 'Writing complex 18% CGST/SGST/IGST tax algorithms and PDF rendering pipelines wastes hundreds of engineering hours.',
      },
      {
        title: 'Maintaining Database Schemas',
        description: 'Handling multi-tenant CRM databases, file storage, and webhook dispatch requires extensive backend maintenance.',
      },
    ],
    stepByStepGuide: [
      {
        step: '01',
        title: 'Install Cora React SDK or API Webhook',
        description: 'Import our lightweight client SDK or send JSON payloads to your Cora workspace API endpoint.',
        codeSnippet: 'import { CoraProvider, ESignModal } from "@heycora/react";',
      },
      {
        step: '02',
        title: 'Trigger Autonomous AI & Invoicing',
        description: 'Send form inputs and retrieve live GST calculations and digital contract signing links via REST API.',
      },
    ],
    capabilities: [
      {
        title: 'Headless REST & Webhook APIs',
        description: 'Full programmatic access to Leads, Invoices, Contracts, Gear Listings, and Crew Schedules.',
      },
      {
        title: 'Prebuilt Tailwind UI Components',
        description: 'Drop-in 18% GST calculator cards, e-signature pads, and client portal modals.',
      },
    ],
    faqs: [
      {
        question: 'Is there a rate limit on the Next.js API endpoints?',
        answer: 'Cora provides enterprise-grade API throughput with up to 1,000 requests per minute on Pro and Scale plans.',
      },
    ],
  },
];
