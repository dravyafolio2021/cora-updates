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
      primaryText: 'Stop calculating 18% GST splits and chasing client payment screenshots manually. Cora turns WhatsApp chats into tax invoices & 0% fee UPI links.',
      image: '/images/cora_gst_upi_3d.jpg',
      headline: 'Automated 18% GST Invoicing',
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
      primaryText: 'Underpricing your retainer scope leads to burnout. Cora models team capacity, adds scope buffers, and bills recurring client retainers on autopilot.',
      image: '/images/card_visual_finance.jpg',
      headline: 'Scope Protection & Retainers',
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
      primaryText: 'Drafting agreements from messy templates leaves you legally exposed. Cora generates Section 10A IT Act deeds with SHA-256 digital seals in seconds.',
      image: '/images/cora_esign_vault_3d.jpg',
      headline: 'Section 10A Digital Contracts',
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
      primaryText: 'Stop paying 2-3% payment gateway commissions on client retainers. Cora generates dynamic NPCI UPI 2.0 QR codes with automated reconciliation.',
      image: '/images/bento_gst_upi.jpg',
      headline: '0% Fee UPI Direct Settlements',
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
      primaryText: 'Generate high-converting property listings, Instagram captions, and commercial shoot briefs in seconds with zero prompt engineering.',
      image: '/images/cora_hero_realestate_cinematic.jpg',
      headline: 'Multi-Model Studio AI Briefs',
      description: 'Optimized for MagicBricks, 99acres, & Instagram.',
      badge: '⚡ Multi-Model AI Engine',
      ctaText: 'Launch Free with Tanya',
    },
    card2: {
      title: 'Autonomous Marketing & Listing Engine for Studios',
      description: 'Distribute polished listing briefs, social carousels, and client onboarding proposals across multiple channels in a single click.',
      capabilities: [
        'Auto-Generates MLS & Property Specs',
        'Instagram Caption & Tag Generator',
        'Commercial Shoot Production Briefs',
        'Multi-Tone Presets (Luxury / Editorial)',
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
      primaryText: 'Embed booking calendars, GST calculators, and AI copilots into Framer, Webflow, WordPress, or Shopify in under 60 seconds.',
      image: '/images/card_visual_agent.jpg',
      headline: '1-Click Website Embed Widgets',
      description: 'Cloudflare Edge CDN delivery with < 14KB bundle footprint.',
      badge: '⚡ Framer & Webflow Native',
      ctaText: 'Launch Free with Dev',
    },
    card2: {
      title: 'Zero-Code Website Lead Intake on Edge CDN',
      description: 'Drop responsive widgets onto your existing portfolio or agency site to capture client briefs, automate bookings, and sync directly to CRM.',
      capabilities: [
        'Framer, Webflow, & WordPress Embeds',
        'Dynamic Cal-Sync Shoot Slot Picker',
        '18% GST Invoice Calculator Widget',
        'Ultra-Lightweight &lt; 14KB CDN Footprint',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },
};
