export interface ToolAgentData {
  slug: string;
  agent: {
    name: string;
    role: string;
    avatar: string;
    status: string;
  };
  card1: {
    primaryText: string;
    image: string;
    headline: string;
    description: string;
    badge: string;
    ctaText: string;
  };
  card2: {
    title: string;
    description: string;
    capabilities: string[];
    ctaText: string;
  };
}

export const TOOL_AGENT_REGISTRY: Record<string, ToolAgentData> = {
  'gst-calculator': {
    slug: 'gst-calculator',
    agent: {
      name: 'Aarav Mehta',
      role: 'Finance & Tax AI Co-Founder',
      avatar: '/images/cora_agent_finance.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Stop calculating 18% GST splits and chasing client payment screenshots manually. Cora turns client WhatsApp chats into compliant SAC 9983 tax invoices & 0% fee UPI payment links on autopilot.',
      image: '/images/cora_gst_upi_3d.jpg',
      headline: 'Automated 18% GST Invoicing & Client Portals',
      description: 'Auto-split CGST/SGST, collect via UPI, & sync to Tally.',
      badge: '⚡ Auto-Calculates SAC 9983',
      ctaText: 'Launch Free with Aarav',
    },
    card2: {
      title: 'Upgrade from Manual Tax Math to Autonomous Billing',
      description: 'Cora handles client proposals, digital milestone release, SAC code tax categorization, and payment reconciliation in one unified dashboard.',
      capabilities: [
        '18% CGST + SGST vs IGST Auto-Split',
        'Dynamic 0% Fee UPI QR Payment Links',
        'Client WhatsApp Payment Reminders',
        'Direct Export to Tally & Zoho Books',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'retainer-calculator': {
    slug: 'retainer-calculator',
    agent: {
      name: 'Rohan Verma',
      role: 'Operations & Retainers AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Underpricing your retainer scope leads to burnout. Cora models your team capacity, adds automatic scope buffers, and bills recurring client retainers with milestone escrow protection.',
      image: '/images/card_visual_finance.jpg',
      headline: 'Scope Protection & Automated Recurring Retainers',
      description: 'Set hourly floors, lock buffers, and auto-collect fees.',
      badge: '⚡ Scope Buffer Shield',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Never Lose Revenue to Unbilled Scope Creep',
      description: 'Automate monthly recurring billing, client approval checkpoints, and capacity forecasting so your studio scales with predictable cash flow.',
      capabilities: [
        'Automated Monthly Retainer Invoicing',
        'Scope Change Request Approvals',
        'Escrow Milestone Hold & Release',
        'Real-Time Margin & Capacity Telemetry',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'contract-builder': {
    slug: 'contract-builder',
    agent: {
      name: 'Kavya Patel',
      role: 'Legal & Compliance AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Drafting agreements from messy templates leaves you legally exposed. Cora generates Section 10A IT Act 2000 compliant digital deeds with tamper-evident SHA-256 digital seals in seconds.',
      image: '/images/cora_esign_vault_3d.jpg',
      headline: 'Section 10A Compliant Digital Contracts & NDAs',
      description: 'Court-admissible e-signatures with cryptographic logs.',
      badge: '⚡ Indian IT Act 2000 Sealed',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'Send Legally Enforceable Agreements on WhatsApp',
      description: 'Clients sign contracts directly on mobile with OTP authentication and cryptographic audit trails. Never chase scanned PDF signatures again.',
      capabilities: [
        'Court-Admissible Digital Signatures',
        'WhatsApp One-Tap Client E-Signing',
        'SHA-256 Tamper-Evident Audit Trails',
        'Encrypted Cloud Document Vault',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'upi-qr-generator': {
    slug: 'upi-qr-generator',
    agent: {
      name: 'Aarav Mehta',
      role: 'Payments & Settlement AI Co-Founder',
      avatar: '/images/cora_agent_finance.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Stop paying 2-3% payment gateway commissions on large client retainers. Cora generates dynamic NPCI UPI 2.0 QR codes with exact amounts and automated payment status reconciliation.',
      image: '/images/bento_gst_upi.jpg',
      headline: '0% Transaction Fee UPI Direct Bank Settlements',
      description: 'Zero gateway cuts, instant GPay/PhonePe bank transfers.',
      badge: '⚡ 0% Gateway Commission',
      ctaText: 'Launch Free with Aarav',
    },
    card2: {
      title: 'Collect 100% of Your Invoice Amount Instantly',
      description: 'Clients scan dynamic QR codes or click direct deep-links from WhatsApp to transfer directly into your business bank account with zero fees.',
      capabilities: [
        'NPCI UPI 2.0 Dynamic QR Generation',
        'GPay, PhonePe, Paytm Deep-Link Pay',
        'Instant WhatsApp Payment Receipts',
        'Direct Current Account Settlement',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'listing-ai': {
    slug: 'listing-ai',
    agent: {
      name: 'Tanya Sen',
      role: 'Creative Brief & Marketing AI Co-Founder',
      avatar: '/images/cora_agent_marketing.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Struggling to write compelling property brochures and creative shoot briefs? Cora turns raw specs into high-converting Instagram decks, commercial briefs, and luxury pitch collateral.',
      image: '/images/bento_website_canvas.jpg',
      headline: 'AI Studio Briefs & Luxury Property Decks',
      description: 'Turn client requirements into ready-to-pitch brochures.',
      badge: '⚡ High-Converting Studio Copy',
      ctaText: 'Launch Free with Tanya',
    },
    card2: {
      title: 'Automate Creative Intake & Pitch Decks',
      description: 'Transform client intake forms into structured production call sheets, moodboards, and automated project proposals in seconds.',
      capabilities: [
        'Automated Shoot Brief Generation',
        'Luxury Property & Architecture Decks',
        'Multi-Format Instagram & Web Copy',
        '1-Click Client Approval Flow',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'embed-builder': {
    slug: 'embed-builder',
    agent: {
      name: 'Dev Sharma',
      role: 'Full-Stack Integrations AI Co-Founder',
      avatar: '/images/about_team_dev.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Need booking calculators and lead intake forms on your website? Cora generates zero-code embeddable widgets for Framer, Webflow, and WordPress that sync directly into your CRM.',
      image: '/images/bento_ai_seo.jpg',
      headline: 'Zero-Code Interactive Web Embeds & Calculators',
      description: 'Copy 1 line of HTML into Framer, Webflow, or WordPress.',
      badge: '⚡ Zero-Code HTML / JS',
      ctaText: 'Launch Free with Dev',
    },
    card2: {
      title: 'Turn Website Visitors into Paying Retainer Clients',
      description: 'Embed real-time quote calculators and booking forms on your website. Submissions instantly create lead cards in your Cora Kanban CRM.',
      capabilities: [
        '1-Line Framer & Webflow Embed Scripts',
        'Instant CRM Pipeline Synchronization',
        'Auto-WhatsApp Lead Alerts',
        'Custom Monochromatic Styling Engine',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },
};
