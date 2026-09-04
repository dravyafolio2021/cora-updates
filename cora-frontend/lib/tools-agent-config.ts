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
      image: '/images/cora_ad_retainer_calculator.jpg',
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
      image: '/images/cora_ad_listing_ai.jpg',
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
      image: '/images/cora_ad_embed_builder.jpg',
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

  'merge-pdf': {
    slug: 'merge-pdf',
    agent: {
      name: 'Kavya Patel',
      role: 'Legal & Document Automation AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Combine confidential proposals, pitch decks, annexures, and master service agreements into a single polished document. 100% private in browser memory.',
      image: '/images/bento_esign_seal.jpg',
      headline: 'Private Client-Side PDF Merge',
      description: 'Combine contracts, pitch decks & rate cards with zero server uploads.',
      badge: '⚡ 100% In-Memory Pure JS',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'Automate Multi-Doc Proposals & Pitch Presentations',
      description: 'Cora turns scattered pitch slide decks, master services agreements, and GST invoices into unified signature-ready client proposals with cryptographically verified seals.',
      capabilities: [
        'Drag-and-Drop Sequential Page Merging',
        'Zero Server Uploads (100% Client-Side)',
        'Automatic File Size & Page Summaries',
        'Instant SHA-256 Audit Trail Protection',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'split-pdf': {
    slug: 'split-pdf',
    agent: {
      name: 'Rohan Verma',
      role: 'Contracts & Scope AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Isolate signed signature pages, extract project milestone annexures, or separate specific deck slides without exposing sensitive client data to third-party servers.',
      image: '/images/cora_esign_vault_3d.jpg',
      headline: 'Precision Page Splitter & Extractor',
      description: 'Extract signature pages and confidential annexures instantly.',
      badge: '⚡ Zero Data Leakage',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Extract Signature Pages & Scopes Instantly',
      description: 'Cleanly slice 50-page vendor contracts down to the relevant statement of work and executed signatory page, ready to forward to banking or legal teams.',
      capabilities: [
        'Interactive Visual Page Grid Selection',
        'Custom Page Range Syntax (e.g. 1-3, 5)',
        '100% In-Browser Memory Processing',
        'Instant Single-Click Extracted PDF Download',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'rotate-pdf': {
    slug: 'rotate-pdf',
    agent: {
      name: 'Dev Sharma',
      role: 'Studio Operations AI Co-Founder',
      avatar: '/images/about_team_dev.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Fix inverted mobile scans, rotate architectural blueprints from portrait to landscape, and align orientation across all pages in under a second.',
      image: '/images/card_bg_contract_seal.jpg',
      headline: 'Instant PDF Orientation Engine',
      description: 'Rotate 90°, 180°, or 270° with lossless vector preservation.',
      badge: '⚡ Lossless Vector Rotation',
      ctaText: 'Launch Free with Dev',
    },
    card2: {
      title: 'Clean Up Scanned Client Agreements & Blueprints',
      description: 'Correct orientation on sideways scanned GST certificates, inverted lease deeds, and landscape photo call-sheets before sending them to clients.',
      capabilities: [
        '90° Clockwise, 180°, and 270° Rotation',
        'Target All Pages or Specific Page Subsets',
        'Retains Full Font & Vector Quality',
        'Fast Pure Client-Side WebAssembly/JS',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'images-to-pdf': {
    slug: 'images-to-pdf',
    agent: {
      name: 'Tanya Sen',
      role: 'Creative Deliverables AI Co-Founder',
      avatar: '/images/cora_agent_marketing.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Bundle client photo proofs, portfolio decks, shoot receipts, and model scans into clean, standardized PDFs without file size limits or cloud compression.',
      image: '/images/cora_ad_listing_ai.jpg',
      headline: 'Instant Multi-Image to PDF Engine',
      description: 'Convert JPG, PNG, and WebP into print-ready A4 or Letter PDFs.',
      badge: '⚡ Zero Cloud Uploads',
      ctaText: 'Launch Free with Tanya',
    },
    card2: {
      title: 'Studio Shoot Proofing & Deliverable Packaging',
      description: 'Streamline high-res client photo review packages, vendor receipts, and lookbooks into clean, uniform documents processed 100% inside your browser.',
      capabilities: [
        'Multi-Image Reordering & Thumbnail Grid',
        'A4, US Letter, and Fit Original Aspect Presets',
        'Configurable Page Margin Controls',
        '100% In-Browser Memory Pure JS Execution',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'esign-pdf': {
    slug: 'esign-pdf',
    agent: {
      name: 'Kavya Patel',
      role: 'Legal & Compliance AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Sign commercial production agreements, model release deeds, and vendor invoices with legally valid digital signatures under Section 10A of the Indian IT Act 2000.',
      image: '/images/cora_esign_vault_3d.jpg',
      headline: 'Section 10A Digital e-Signature',
      description: 'Draw or type signatures with cryptographic timestamp stamping.',
      badge: '⚡ Section 10A IT Act Sealed',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'Court-Admissible Digital Signatures on Any PDF',
      description: 'Empower clients, photographers, and agency partners to sign contracts effortlessly on mobile or desktop with touch signature pads and automated audit logs.',
      capabilities: [
        'Smooth Touch & Mouse HTML5 Canvas Signature Pad',
        'Typed Legal Signature Generator with Script Typography',
        'Target Page Selection & Interactive Placement Presets',
        'Section 10A IT Act 2000 Compliance & SHA-256 Audit Trail',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'watermark-pdf': {
    slug: 'watermark-pdf',
    agent: {
      name: 'Rohan Verma',
      role: 'Media Asset Protection AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Protect unreleased shoot proofs, confidential pitch proposals, and draft agreements with custom diagonal or horizontal watermarks across all document pages.',
      image: '/images/cora_ad_retainer_calculator.jpg',
      headline: 'Document Protection & Watermarking',
      description: 'Stamp custom confidentiality notices with adjustable opacity and angles.',
      badge: '⚡ All Pages Watermarked',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Safeguard Commercial IP Before Final Milestone Payout',
      description: 'Prevent unauthorized client distribution of draft deliverables. Apply prominent semi-transparent watermarks to client review decks with zero quality degradation.',
      capabilities: [
        'Custom Text & One-Tap Presets (CONFIDENTIAL, DRAFT)',
        'Adjustable Opacity (10% - 80%) & 45° Angle Controls',
        'Instant Multi-Page Batch Stamping',
        'Vector Crisp Typography with Lossless PDF Export',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },
};


