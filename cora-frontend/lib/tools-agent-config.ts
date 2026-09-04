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
      badge: 'Auto-Calculates SAC 9983',
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
      badge: 'Scope Buffer Shield',
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
      badge: 'Indian IT Act 2000 Sealed',
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
      badge: '0% Gateway Commission',
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
      badge: 'Multi-Model AI Engine',
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
      badge: 'Framer & Webflow Native',
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
      badge: '100% In-Memory Pure JS',
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
      badge: 'Zero Data Leakage',
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
      badge: 'Lossless Vector Rotation',
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
      badge: 'Zero Cloud Uploads',
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
      badge: 'Section 10A IT Act Sealed',
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
      badge: 'All Pages Watermarked',
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

  'compress-pdf': {
    slug: 'compress-pdf',
    agent: {
      name: 'Rohan Verma',
      role: 'Operations & Optimization AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Compress heavy client proposals, portfolio decks, and shoot contracts to under 5MB for instant WhatsApp sharing and email attachments with zero quality loss.',
      image: '/images/cora_ad_retainer_calculator.jpg',
      headline: 'Extreme Lossless PDF Compression',
      description: 'Downsample stream dictionaries and strip redundant metadata in browser.',
      badge: 'Reduces up to 70% Size',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Reduce PDF File Size for Fast Distribution',
      description: 'Eliminate file size bouncebacks on tender portals, government filings, and client WhatsApp pitches with multi-tier in-browser compression.',
      capabilities: [
        '3 Optimization Presets: Extreme, Recommended, Low',
        'Live Before & After File Size Comparison Metrics',
        'Zero Uploads to External Servers (100% In-Memory)',
        'Lossless Text Font Preservation & Vector Crispness',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'number-pdf': {
    slug: 'number-pdf',
    agent: {
      name: 'Kavya Patel',
      role: 'Legal Documentation AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Number multi-page master agreements, technical proposals, and audit binders with customized legal pagination headers and footers in one click.',
      image: '/images/cora_esign_vault_3d.jpg',
      headline: 'Automated Page Numbering',
      description: 'Add clean "Page X of Y" pagination with custom position controls.',
      badge: 'Audit-Ready Pagination',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'Format Professional Legal & Commercial Binders',
      description: 'Stamp uniform, crisp page numbers across multi-page decks with position offsets, custom starting indexes, and format customization.',
      capabilities: [
        'Position Options: Bottom-Center, Bottom-Right, Top-Right',
        'Format Types: "Page X of Y", "Page X", "Numeric Only"',
        'Adjustable Margin Offsets & Font Sizing',
        'Instant Multi-Page Batch Stamping in Pure JS',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'remove-pages': {
    slug: 'remove-pages',
    agent: {
      name: 'Rohan Verma',
      role: 'Document Management AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Delete unwanted draft sheets, duplicate pages, or confidential appendix sheets from client PDF deliverables with visual thumbnail selection.',
      image: '/images/cora_ad_retainer_calculator.jpg',
      headline: 'Delete Unwanted PDF Pages',
      description: 'Select pages to remove or enter custom ranges with instant re-export.',
      badge: 'Zero Quality Loss',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Trim & Purge Pages from Any PDF Document',
      description: 'Quickly remove sensitive pages, blank sheets, or outdated annexures before sending documents to external clients or signing authorities.',
      capabilities: [
        'Interactive Page Badges & Range Selector',
        'Delete Single Sheets or Multi-Page Spans',
        'Lossless Vector Export of Surviving Pages',
        '100% Private In-Browser Execution',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'word-to-pdf': {
    slug: 'word-to-pdf',
    agent: {
      name: 'Aarav Mehta',
      role: 'Content & Publishing AI Co-Founder',
      avatar: '/images/cora_agent_finance.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Convert document text, draft terms, and commercial notes into publication-ready, beautifully typeset A4 PDF documents with automated pagination.',
      image: '/images/cora_gst_upi_3d.jpg',
      headline: 'Instant Word to PDF Converter',
      description: 'Transform draft text and copy into high-res vectorized PDF files.',
      badge: 'A4 Typeset Format',
      ctaText: 'Launch Free with Aarav',
    },
    card2: {
      title: 'Publish Professional Documents Directly in Browser',
      description: 'Eliminate formatting quirks. Convert raw text, proposals, or memos into standardized PDF files ready for printing or e-signing.',
      capabilities: [
        'Automated Word Wrap & Multi-Page Pagination',
        'Custom Document Titles & Typographic Scaling',
        'Vector Crisp Text Embedding with Standard Fonts',
        'Instant 1-Click Client-Side Download',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'ai-pdf-summarizer': {
    slug: 'ai-pdf-summarizer',
    agent: {
      name: 'Kavya Patel',
      role: 'Intelligence & Clause Risk AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Scan lengthy contracts, master service agreements, and tenders in seconds. Highlight hidden indemnities, termination penalties, and payment milestones.',
      image: '/images/cora_esign_vault_3d.jpg',
      headline: 'AI Contract Risk & Clause Scanner',
      description: 'Instant legal clause detection, summary insights, & action items.',
      badge: 'Smart Clause Radar',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'Autonomous Legal & Business Intelligence',
      description: 'Upload any business document to extract critical risk factors, milestone obligations, payment terms, and confidentiality commitments in seconds.',
      capabilities: [
        'Commercial Clause Radar (Payment, Penalty, SLA)',
        'Executive Summary & Risk Scoring Breakdown',
        '1-Tap Direct Bridge to Cora AI Co-Founder',
        '100% In-Browser Inspection (Never Sent to Third Parties)',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'repair-pdf': {
    slug: 'repair-pdf',
    agent: {
      name: 'Vikram Malhotra',
      role: 'Systems & PDF Infrastructure AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Damaged or unreadable PDFs stall client deals and tenders. Cora reconstructs corrupted xref tables, recovers damaged object trees, and sanitizes binary streams in your browser.',
      image: '/images/cora_pdf_pipeline_3d.jpg',
      headline: 'Autonomous PDF Diagnostic & Repair',
      description: 'Reconstruct corrupted cross-reference tables & recover damaged pages.',
      badge: 'Binary Stream Recovery',
      ctaText: 'Launch Free with Vikram',
    },
    card2: {
      title: 'Enterprise PDF Resilience & In-Memory Recovery',
      description: 'Recover unopenable contracts, broken scans, and corrupted client submissions without sending sensitive documents across public cloud networks.',
      capabilities: [
        'Cross-Reference (Xref) Table Rebuilding',
        'Damaged Trailer & Catalog Root Restoration',
        'Orphaned Object Stream Sanitization',
        '100% Client-Side In-Memory Execution',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'ocr-pdf': {
    slug: 'ocr-pdf',
    agent: {
      name: 'Ananya Ray',
      role: 'Document Vision & OCR AI Co-Founder',
      avatar: '/images/cora_agent_design.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Scanned invoices, receipts, and paper contracts are trapped as dumb images. Cora extracts crisp machine-readable text and builds searchable PDFs in seconds.',
      image: '/images/cora_smart_contract_3d.jpg',
      headline: 'Client-Side OCR & Optical Scanner',
      description: 'Convert scanned image PDFs into searchable, selectable vector documents.',
      badge: 'Optical Vision Engine',
      ctaText: 'Launch Free with Ananya',
    },
    card2: {
      title: 'Turn Scanned Paper into Searchable Digital Intelligence',
      description: 'Extract text, numbers, and tabular data from scanned documents with high-confidence optical recognition and immediate full-text copy.',
      capabilities: [
        'High-Accuracy Text & Numeric Extraction',
        'Instant Full-Text Search in Scanned Files',
        'Searchable PDF Layer Recompilation',
        'Pure Local Browser Canvas Processing',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'translate-pdf': {
    slug: 'translate-pdf',
    agent: {
      name: 'Devika Sen',
      role: 'Localization & Multilingual AI Co-Founder',
      avatar: '/images/cora_agent_finance.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Regional client agreements and tenders require fast, accurate translation. Translate contracts and proposals into Hindi, Tamil, Telugu, and 20+ global languages.',
      image: '/images/cora_gst_upi_3d.jpg',
      headline: 'Multilingual Document Translation',
      description: 'Preserve formatting while translating documents across Indian & global tongues.',
      badge: 'Vernacular & Global AI',
      ctaText: 'Launch Free with Devika',
    },
    card2: {
      title: 'Cross-Border & Vernacular Contract Translation',
      description: 'Seamlessly convert commercial agreements, pitch decks, and invoices across languages while maintaining clause numbering and legal layout.',
      capabilities: [
        'Supports Hindi, Tamil, Telugu, Marathi & 20+ Languages',
        'Side-by-Side Bilingual Document Comparison',
        'Editable In-Browser Translation Review',
        'Instant Clean PDF Re-Export',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'extract-pages': {
    slug: 'extract-pages',
    agent: {
      name: 'Rohan Verma',
      role: 'Operations & Document AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Extract signed execution sheets, specific annexures, or critical chapters from massive tender packets without re-rendering or quality degradation.',
      image: '/images/cora_pdf_pipeline_3d.jpg',
      headline: 'Precision PDF Page Extraction',
      description: 'Isolate specific sheets or custom ranges into a pristine standalone PDF.',
      badge: 'Lossless Vector Extract',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Laser-Focused Document Isolation',
      description: 'Extract only what you need. Filter odd/even pages, custom ranges, or individual sheets with live visual preview and instant download.',
      capabilities: [
        'Interactive Page Selection & Grid Previews',
        'Custom Range Queries (e.g. 1-3, 5, 8-12)',
        'Lossless Vector Font & Signature Retention',
        'Zero Cloud File Storage (Pure Browser Engine)',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'powerpoint-to-pdf': {
    slug: 'powerpoint-to-pdf',
    agent: {
      name: 'Aarav Mehta',
      role: 'Presentation & Deck AI Co-Founder',
      avatar: '/images/cora_agent_finance.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Messy PowerPoint files look inconsistent across client devices and OS versions. Convert PPT and PPTX pitch decks into pixel-perfect landscape PDF presentations.',
      image: '/images/cora_smart_contract_3d.jpg',
      headline: 'PowerPoint to PDF Deck Converter',
      description: 'Standardize 16:9 and 4:3 pitch decks into universally readable PDF presentations.',
      badge: 'Standard 16:9 Landscape',
      ctaText: 'Launch Free with Aarav',
    },
    card2: {
      title: 'Universal Client-Ready Pitch Decks',
      description: 'Eliminate font mismatch bugs, missing media links, and slide layout breaks by compiling decks into universally readable landscape PDF decks.',
      capabilities: [
        'Standard 16:9 Widescreen & 4:3 Slide Framing',
        'Pre-Formatted Executive Pitch & Portfolio Templates',
        'Preserves Slide Numbers & Corporate Footers',
        '100% In-Browser Privacy Protection',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'pdf-to-jpg': {
    slug: 'pdf-to-jpg',
    agent: {
      name: 'Aarav Mehta',
      role: 'Creative & Media AI Co-Founder',
      avatar: '/images/cora_agent_finance.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Extract high-resolution visual sheets, portfolio slides, and signed certificate pages into crisp JPG images at 2x retina clarity. 100% private in browser memory.',
      image: '/images/cora_pdf_pipeline_3d.jpg',
      headline: 'High-Res PDF to JPG Rasterizer',
      description: 'Convert PDF sheets into crisp 2x retina JPG images in browser memory.',
      badge: 'Crisp 2x Retina Render',
      ctaText: 'Launch Free with Aarav',
    },
    card2: {
      title: 'Lossless Visual Sheet Conversion',
      description: 'Transform multi-page PDF documents into crystal-clear individual JPG images ready for social media, client presentations, and portfolio showcases.',
      capabilities: [
        'Crisp 2x Retina HTML5 Canvas Rendering',
        'Individual Sheet or Bulk Batch Download',
        'Configurable Image Quality & Scale Presets',
        '100% Client-Side Pure Browser Processing',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'pdf-to-word': {
    slug: 'pdf-to-word',
    agent: {
      name: 'Kavya Patel',
      role: 'Legal & Document Automation AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Locked PDFs prevent redlining and contract revisions. Cora extracts document structure into clean, editable Microsoft Word files in browser memory.',
      image: '/images/bento_esign_seal.jpg',
      headline: 'PDF to Word Doc Extractor',
      description: 'Extract structured text, headers & clauses into editable .doc files.',
      badge: 'Structured Heading Retention',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'Turn Static PDFs into Editable Contracts',
      description: 'Preserve paragraph structure, clause numbering, and headings while converting read-only client agreements into editable Word documents.',
      capabilities: [
        'Smart Heading & Paragraph Structure Parsing',
        '1-Click Export to Microsoft Word (.doc)',
        'Full In-Browser Interactive Document Editor',
        'Zero Cloud File Storage & Absolute Privacy',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'pdf-to-excel': {
    slug: 'pdf-to-excel',
    agent: {
      name: 'Aarav Mehta',
      role: 'Finance & Tax AI Co-Founder',
      avatar: '/images/cora_agent_finance.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Stop retyping invoice line items and GST tax schedules manually. Cora parses tabular PDF records into clean, structured CSV & Excel spreadsheets.',
      image: '/images/cora_gst_upi_3d.jpg',
      headline: 'PDF Table to Excel & CSV Parser',
      description: 'Extract invoices, tax tables & rate cards into clean spreadsheets.',
      badge: 'Auto Table Matrix Detection',
      ctaText: 'Launch Free with Aarav',
    },
    card2: {
      title: 'Automate Tabular Data Extraction',
      description: 'Extract financial statements, client invoices, and contractor rate cards directly into Excel and CSV without manual data entry.',
      capabilities: [
        'Automatic Delimiter & Column Alignment Detection',
        'Interactive In-Browser Spreadsheet Editor',
        'Clean RFC 4180 CSV & Excel Spreadsheet Export',
        '1-Tap Copy as TSV for Direct Sheet Pasting',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'excel-to-pdf': {
    slug: 'excel-to-pdf',
    agent: {
      name: 'Rohan Verma',
      role: 'Operations & Reporting AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Convert messy spreadsheet exports and raw financial tables into publication-ready A4 PDF documents with zebra striping and corporate headers.',
      image: '/images/cora_ad_retainer_calculator.jpg',
      headline: 'Excel & CSV to A4 PDF Table Maker',
      description: 'Format spreadsheets into professional publication-ready PDF tables.',
      badge: 'A4 Vectorized Table Layout',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Publication-Ready Financial Statements',
      description: 'Format raw tabular numbers into crisp, boardroom-ready A4 documents with automatic pagination, custom column widths, and corporate branding.',
      capabilities: [
        'Portrait & Landscape A4 Page Support',
        'Zebra Striping & High-Contrast Headers',
        'Direct CSV Upload or Copy-Paste from Sheets',
        'Automatic Pagination & Summary Row Math',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'pdf-to-markdown': {
    slug: 'pdf-to-markdown',
    agent: {
      name: 'Kavya Patel',
      role: 'Documentation & Knowledge AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Convert documentation, specifications, and commercial agreements into clean GitHub-flavored Markdown with headers, code blocks, and lists.',
      image: '/images/cora_smart_contract_3d.jpg',
      headline: 'PDF to Markdown Document Converter',
      description: 'Extract structured text with headers, lists & code blocks into .md format.',
      badge: 'GitHub-Flavored Markdown',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'Bridge Static Docs into LLM & Dev Workflows',
      description: 'Extract PDF contracts, whitepapers, and technical guides into clean Markdown ready for developer wikis, LLM prompts, and Notion workspaces.',
      capabilities: [
        'Detects Headings (# H1, ## H2, ### H3)',
        'Structured Lists, Blockquotes & Code Snippets',
        'Live Side-by-Side Rendered Markdown Preview',
        '1-Click Clipboard Copy & .md File Download',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'html-to-pdf': {
    slug: 'html-to-pdf',
    agent: {
      name: 'Rohan Verma',
      role: 'Engineering & Document AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Render styled HTML templates, digital invoices, and contract markups directly into standard A4 PDF files with pixel-perfect typography.',
      image: '/images/cora_pdf_pipeline_3d.jpg',
      headline: 'HTML & Styled Content to PDF Compiler',
      description: 'Convert custom HTML, CSS & invoice templates into crisp A4 PDFs.',
      badge: 'Pixel-Perfect A4 Vector PDF',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Automated HTML Document Generation',
      description: 'Paste HTML code or styled invoice markup to instantly generate clean, print-ready PDF files with custom margins and standard typography.',
      capabilities: [
        'Live Code Editor with Instant Sandboxed Preview',
        'Built-in Studio Invoices & Agreement Templates',
        'Standard A4 & US Letter Dimensions',
        '100% In-Browser Zero Server Processing',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'protect-pdf': {
    slug: 'protect-pdf',
    agent: {
      name: 'Kavya Patel',
      role: 'Security & Compliance AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Protect confidential contracts, pitch decks, and financial audits with AES-256 military-grade encryption. Restrict printing, copying, and modifications without leaving your browser.',
      image: '/images/cora_esign_vault_3d.jpg',
      headline: 'AES-256 PDF Encryption & DRM Lock',
      description: 'Set open passwords, deny printing, and block content copying client-side.',
      badge: 'AES-256 Bit Encryption',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'Enterprise Document Security Without Cloud Risk',
      description: 'Cora encrypts PDF files directly in your browser memory. Set granular owner passwords and disable content extraction with zero server-side leaks.',
      capabilities: [
        'AES-256 Bit ISO 32000-1 Standard Cryptography',
        'Granular Print & Copy Extraction Restrictions',
        'Separate User & Owner Password Controls',
        '100% In-Browser Memory (Zero Cloud Uploads)',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'unlock-pdf': {
    slug: 'unlock-pdf',
    agent: {
      name: 'Aarav Mehta',
      role: 'Document Security & Finance AI Co-Founder',
      avatar: '/images/cora_agent_finance.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Locked out of your own bank statements or tax filings? Remove passwords and strip restrictive print/copy permissions cleanly in seconds.',
      image: '/images/cora_pdf_pipeline_3d.jpg',
      headline: 'Instant PDF Password & DRM Unlock',
      description: 'Strip owner restrictions, remove copy blocks, and decrypt securely.',
      badge: 'Zero Server Logs',
      ctaText: 'Launch Free with Aarav',
    },
    card2: {
      title: 'Regain Total Control Over Your Documents',
      description: 'Permanently remove frustrating print blocks and copy restrictions from your legitimate records, creating an unlocked, clean PDF.',
      capabilities: [
        'Instant Removal of Owner Print & Copy Restrictions',
        'Client-Side AES Decryption with Known Passwords',
        'Preserves Original Vector Quality & Formatting',
        'Zero Data Stored or Transmitted to External Servers',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'redact-pdf': {
    slug: 'redact-pdf',
    agent: {
      name: 'Kavya Patel',
      role: 'Privacy & Legal AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Black out sensitive PAN numbers, Aadhaar IDs, bank balances, and confidential contract clauses permanently before sharing with third parties.',
      image: '/images/cora_esign_vault_3d.jpg',
      headline: 'Permanent In-Browser PDF Redaction',
      description: 'Sanitize confidential data, KYC identifiers, and financial values.',
      badge: 'Permanent Vector Redaction',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'True Redaction That Cannot Be Uncovered',
      description: 'Unlike transparent highlighter tricks, Cora burns opaque blackout blocks into the PDF object tree, permanently sanitizing sensitive personal data.',
      capabilities: [
        'Preset Presets for Aadhaar, PAN & Bank Details',
        'Interactive Canvas Drawing for Custom Blackout Boxes',
        'Optional [CONFIDENTIAL] or [REDACTED] Text Overlays',
        '100% Local Browser Memory Processing',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'crop-pdf': {
    slug: 'crop-pdf',
    agent: {
      name: 'Rohan Verma',
      role: 'Operations & Formatting AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Trim messy scanner borders, white margins, and unwanted printer headers across all pages uniformly with precision margin controls.',
      image: '/images/cora_pdf_pipeline_3d.jpg',
      headline: 'Precision PDF Border & Margin Trimmer',
      description: 'Adjust crop boxes and trim whitespace across all pages in seconds.',
      badge: 'Lossless CropBox Geometry',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Studio-Grade Page Margin Precision',
      description: 'Standardize document viewports for printing or screen viewing without re-compressing or rasterizing underlying text and images.',
      capabilities: [
        'Interactive Top, Bottom, Left & Right Margin Sliders',
        'One-Click Trim Presets (0.5 in, 1 in, Header/Footer)',
        'Apply Globally or to Individual Page Ranges',
        'Lossless Native PDF MediaBox & CropBox Geometry',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'compare-pdf': {
    slug: 'compare-pdf',
    agent: {
      name: 'Kavya Patel',
      role: 'Legal Audit & Risk AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Spot subtle clause modifications, revised milestone amounts, and stealth changes between original and revised contract revisions side-by-side.',
      image: '/images/cora_esign_vault_3d.jpg',
      headline: 'Side-by-Side PDF Revision Comparator',
      description: 'Inspect differences between two document drafts with synchronous views.',
      badge: 'Contract Revision Audit',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'Audit Document Revisions in Real Time',
      description: 'Never sign an altered agreement without checking. Upload original and revised drafts to inspect structural, dimensional, and text changes side-by-side.',
      capabilities: [
        'Dual-Pane Synchronized Side-by-Side Viewing',
        'Structural Metadata & Page Count Delta Analysis',
        'Visual Overlay & Difference Inspection Modes',
        '100% In-Browser Memory (Client-Side Only)',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'organize-pdf': {
    slug: 'organize-pdf',
    agent: {
      name: 'Rohan Verma',
      role: 'Operations & Workflow AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Rearrange, reorder, duplicate, and rotate PDF pages visually with intuitive drag-and-drop tiles before binding client deliverables.',
      image: '/images/cora_ad_retainer_calculator.jpg',
      headline: 'Visual Drag-and-Drop Page Tile Organizer',
      description: 'Rearrange page sequences, duplicate sheets, and rotate orientations.',
      badge: 'Visual Page Sequencer',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Effortless Page Sequencing & Manipulation',
      description: 'Prepare clean client binders and proposals. Drag tiles to reorganize page order, duplicate invoices, and rotate upside-down scans in seconds.',
      capabilities: [
        'Smooth Drag-and-Drop Interactive Tile Grid',
        '1-Click Duplicate, Delete & 90° Rotate Controls',
        'Quick Bulk Sequence Reversal & Reset Actions',
        'Lossless Page Tree Export in Browser Memory',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'csv-to-excel': {
    slug: 'csv-to-excel',
    agent: {
      name: 'Aarav Mehta',
      role: 'Finance & Spreadsheet AI Co-Founder',
      avatar: '/images/cora_agent_finance.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Stop wrestling with broken CSV encoding, missing commas, and corrupted date formats. Cora converts CSV and TSV files into native Excel spreadsheets in your browser.',
      image: '/images/cora_gst_upi_3d.jpg',
      headline: 'CSV to Excel Converter',
      description: 'Convert delimited data into clean formatted .xlsx spreadsheets instantly.',
      badge: 'Native XML .xlsx Engine',
      ctaText: 'Launch Free with Aarav',
    },
    card2: {
      title: 'Browser-Native Spreadsheet Compilation',
      description: 'Transform raw delimited files into structured Excel workbooks with custom sheet naming, automatic type detection, and zero server transmission.',
      capabilities: [
        'Instant In-Browser Memory Parsing & XML Generation',
        'Automatic Column Alignment & Type Detection',
        'Interactive Live Data Table Preview',
        '100% Private Client-Side Processing',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'excel-to-csv': {
    slug: 'excel-to-csv',
    agent: {
      name: 'Rohan Verma',
      role: 'Operations & Data AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Export spreadsheet tables and raw rows into clean RFC 4180 standard CSVs. Select custom delimiters including commas, semicolons, tabs, and pipes.',
      image: '/images/cora_ad_retainer_calculator.jpg',
      headline: 'Excel to CSV Delimiter Engine',
      description: 'Convert spreadsheet grids into clean, escaped RFC 4180 CSV files.',
      badge: 'RFC 4180 Compliant',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Clean & Compliant CSV Serialization',
      description: 'Extract rows from spreadsheets with customizable delimiters, proper quote escaping, and 1-tap clipboard copying for developer workflows.',
      capabilities: [
        'Comma, Semicolon, Tab, and Pipe Delimiter Options',
        'RFC 4180 Compliant Quote & Newline Escaping',
        'Instant 1-Click Clipboard Copy & File Download',
        'Zero Cloud Dependency - 100% Private in RAM',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'excel-to-json': {
    slug: 'excel-to-json',
    agent: {
      name: 'Kavya Patel',
      role: 'Engineering & Data AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Turn tabular spreadsheet data into production-ready JSON payloads. Choose between Array of Objects, 2D Arrays, or Keyed Dictionary maps in real time.',
      image: '/images/cora_smart_contract_3d.jpg',
      headline: 'Spreadsheet to JSON Transformer',
      description: 'Transform Excel and CSV rows into formatted, syntax-highlighted JSON.',
      badge: 'Multi-Schema JSON Engine',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'Developer-Grade Spreadsheet to JSON',
      description: 'Convert CSV and spreadsheet tables into clean JSON objects, 2D matrices, or indexed key-value dictionaries with automatic number coercion.',
      capabilities: [
        'Array of Objects, Array of Arrays, and Keyed Maps',
        'Automatic Numeric Type Casting & Null Handling',
        'Formatted & Syntax-Highlighted Output with 1-Click Copy',
        'Direct .json File Export in Pure Browser Memory',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'excel-formula-generator': {
    slug: 'excel-formula-generator',
    agent: {
      name: 'Aarav Mehta',
      role: 'Spreadsheet AI & Financial Modeling Co-Founder',
      avatar: '/images/cora_agent_finance.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Turn plain English prompts into production-grade Excel and Google Sheets formulas. Break down complex nested logic, SUMIFS, and lookup syntax instantly.',
      image: '/images/cora_gst_upi_3d.jpg',
      headline: 'Natural Language Formula Generator',
      description: 'Convert plain English intent into robust formulas with syntax breakdown.',
      badge: 'Excel & Google Sheets',
      ctaText: 'Launch Free with Aarav',
    },
    card2: {
      title: 'Autonomous Spreadsheet Intelligence',
      description: 'Stop getting stuck on nested IFs, SUMIFS, and regex text formulas. Generate bulletproof formulas with argument explanations and error guards in seconds.',
      capabilities: [
        'Plain English to Excel & Sheets Formula Translation',
        'Syntax Breakdown & Argument Documentation',
        'Multi-Condition SUMIFS, XLOOKUP & Regex Patterns',
        '100% Private Client-Side Browser Execution',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'vlookup-generator': {
    slug: 'vlookup-generator',
    agent: {
      name: 'Rohan Verma',
      role: 'Operations & Data Modeling AI Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Build error-free VLOOKUP and modern XLOOKUP formulas without memorizing column indices or syntax quirks. Includes automatic IFERROR fallback handling.',
      image: '/images/cora_ad_retainer_calculator.jpg',
      headline: 'Visual VLOOKUP & XLOOKUP Builder',
      description: 'Interactive visual builder with exact match and fallback handling.',
      badge: 'VLOOKUP + XLOOKUP Dual Engine',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Master Spreadsheet Lookups Visually',
      description: 'Design lookup formulas with real-time visual syntax validation. Generate both classic VLOOKUP and next-generation XLOOKUP with one click.',
      capabilities: [
        'Interactive Visual Range & Column Index Configurator',
        'Dual VLOOKUP and XLOOKUP Simultaneous Generation',
        'Built-in IFERROR & #N/A Missing Data Fallbacks',
        'Absolute Reference Locking ($A$2:$F$500)',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'clean-sheet-data': {
    slug: 'clean-sheet-data',
    agent: {
      name: 'Kavya Patel',
      role: 'Data Sanitization & CRM AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Clean dirty spreadsheets in seconds. Standardize Indian phone numbers, convert dates to ISO, trim irregular spaces, and title-case contact names in browser RAM.',
      image: '/images/cora_smart_contract_3d.jpg',
      headline: 'Browser-Native Sheet Data Sanitizer',
      description: 'Normalize phone numbers, dates, and names with instant live preview.',
      badge: 'Zero Server Uploads',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'Enterprise-Grade Data Sanitization',
      description: 'Sanitize messy customer rosters, financial statements, and lead sheets directly in your browser memory before importing into your CRM or billing ledger.',
      capabilities: [
        'Trim Irregular Spaces & Remove Ghost Rows',
        'Standardize Phone Numbers to Indian +91 Format',
        'Convert Inconsistent Date Formats to ISO YYYY-MM-DD',
        'Live Side-by-Side Before & After Table Preview',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'remove-duplicates-csv': {
    slug: 'remove-duplicates-csv',
    agent: {
      name: 'Rohan Verma',
      role: 'Operations & Data Integrity Co-Founder',
      avatar: '/images/cora_agent_operations.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Deduplicate massive contact lists, transaction registries, and customer CSVs in seconds. Select exact key columns or compare across every field with zero cloud leakage.',
      image: '/images/cora_ad_retainer_calculator.jpg',
      headline: 'CSV Row Deduplication Engine',
      description: 'Filter duplicate sheet rows by key columns with instant metrics.',
      badge: 'Exact Column Match',
      ctaText: 'Launch Free with Rohan',
    },
    card2: {
      title: 'Autonomous Data Hygiene & Deduplication',
      description: 'Purge duplicate entries from marketing lists, payroll rosters, and GST registries before imports. Features column-specific key matching and case sensitivity toggles.',
      capabilities: [
        'Multi-Column Key Checklist (Email, Phone, Unique ID)',
        'Case Sensitive or Normalized Comparison Toggles',
        'Live Duplicate Removal Count & Retention Percentage',
        '100% In-Browser Memory Processing - Zero Data Sent to Servers',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'merge-csv': {
    slug: 'merge-csv',
    agent: {
      name: 'Aarav Mehta',
      role: 'Finance & Consolidation AI Co-Founder',
      avatar: '/images/cora_agent_finance.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Combine dozens of disparate CSV files into a unified master spreadsheet. Cora aligns column headers automatically and compiles consolidated tables in browser RAM.',
      image: '/images/cora_gst_upi_3d.jpg',
      headline: 'Multi-File CSV Consolidation Engine',
      description: 'Merge multiple monthly spreadsheets and lead sheets into one.',
      badge: 'Header Alignment Engine',
      ctaText: 'Launch Free with Aarav',
    },
    card2: {
      title: 'Master CSV Merging & Consolidation',
      description: 'Drag and drop multiple CSV sheets from different branches, team members, or months. Cora aligns matching columns and builds a single consolidated master export.',
      capabilities: [
        'Multi-File Batch Drag & Drop Upload',
        'Automated Column Header Detection & Alignment',
        'Instant Multi-Source Row Consolidation',
        '1-Click Master CSV & Excel Compatible Download',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },

  'split-csv': {
    slug: 'split-csv',
    agent: {
      name: 'Kavya Patel',
      role: 'Engineering & Data Pipeline Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Break massive multi-gigabyte CSV exports into manageable files. Split by exact row count or automatically partition by unique column values like City or Vendor.',
      image: '/images/cora_smart_contract_3d.jpg',
      headline: 'CSV Partitioning & Split Engine',
      description: 'Split massive datasets by row limit or unique column categories.',
      badge: 'Dual Partitioning Modes',
      ctaText: 'Launch Free with Kavya',
    },
    card2: {
      title: 'High-Volume Dataset Partitioning',
      description: 'Bypass spreadsheet row limits and CRM batch caps. Split large datasets into chunks of 500 or 1,000 rows, or partition automatically by distinct values in any column.',
      capabilities: [
        'Split by Configurable Row Batch Limits',
        'Partition by Column Value (City, Vendor, Status, Date)',
        'Automated Clean File Naming with Row Counters',
        '100% Private In-Browser RAM Execution',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  },
};
