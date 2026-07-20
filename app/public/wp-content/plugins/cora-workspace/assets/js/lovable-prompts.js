/**
 * Cora × Lovable Prompt Library v1.0
 * Component definitions + prompt fragment assembler for the Lovable Studio drawer.
 */
window.CORA_PROMPT_LIBRARY = (function () {
  'use strict';

  // ── Component definitions ──────────────────────────────────────────────────

  var COMPONENTS = {
    'property-grid': {
      id: 'property-grid',
      title: 'Property Listings Grid',
      icon: '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>',
      description: 'Live property cards from Cora CRM',
      coraFeature: 'Properties API',
      coraAttr: 'data-cora-inject="properties"',
      promptFragment: 'A responsive property listings grid. Each card must show: hero image (16:9 aspect ratio), property title, address, price badge, and 3 stat pills (beds, baths, sq.ft). The outer grid container element must have the attribute: data-cora-inject="properties". Add a data-cora-cols="3" attribute for a 3-column layout (Cora will auto-populate cards from live data). Include a data-cora-limit="12" attribute to set the maximum number of listings shown.',
      technicalNote: 'Grid container must have data-cora-inject="properties"'
    },
    'search-bar': {
      id: 'search-bar',
      title: 'Property Search Bar',
      icon: '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>',
      description: 'Search/filter live property listings',
      coraFeature: 'Search API',
      coraAttr: 'data-cora-search="true"',
      promptFragment: 'A prominent search bar with a text input field and a search button. The search input element must have the attributes: data-cora-search="true" and data-cora-target="#property-grid" (pointing to the property grid so Cora can update results in real time). Style: pill-shaped, white background, subtle drop shadow. Include filter pills below for: All, Residential, Commercial, Land.',
      technicalNote: 'Input must have data-cora-search="true" and data-cora-target pointing to the grid container'
    },
    'lead-form': {
      id: 'lead-form',
      title: 'Lead Capture Form',
      icon: '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
      description: 'Enquiry form that saves leads to Cora',
      coraFeature: 'Leads CRM',
      coraAttr: 'data-cora-inject="lead-form"',
      promptFragment: 'A clean lead capture enquiry form. Fields: Full Name (name="name"), Email Address (name="email"), Phone Number (name="phone"), Message textarea (name="message"), and a Submit button (type="submit"). The <form> element must have the attribute: data-cora-inject="lead-form". Add a hidden input: <input type="hidden" id="cora-nonce" name="cora_nonce" value=""> (Cora fills this automatically). Style: white card, padded, rounded corners, a single submit CTA button.',
      technicalNote: 'The <form> element must have data-cora-inject="lead-form". Include hidden input id="cora-nonce"'
    },
    'hero-banner': {
      id: 'hero-banner',
      title: 'Hero Banner Section',
      icon: '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="14" rx="2"/><path d="M3 17h18M3 21h18" stroke-linecap="round"/></svg>',
      description: 'Full-width hero with headline & CTA',
      coraFeature: 'Static / CMS',
      coraAttr: null,
      promptFragment: 'A full-width hero banner at the top of the page. Contains: a large bold headline (h1), a one-line subtitle, and two CTA buttons side by side ("Browse Listings" and "Get a Free Valuation"). Background: a high-quality real estate/architecture photo with a dark overlay gradient. Text is white. The section should be 100vh tall on desktop, 70vh on mobile.',
      technicalNote: 'Static section — no Cora attributes needed'
    },
    'agent-card': {
      id: 'agent-card',
      title: 'Agent Profile Card',
      icon: '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
      description: 'Agent bio card with contact details',
      coraFeature: 'Agents API',
      coraAttr: 'data-cora-inject="agent"',
      promptFragment: 'An agent profile card. Contains: circular avatar photo, agent name (bold), designation tag, phone number with a call icon, email with a mail icon, and a "Schedule Viewing" button. The card wrapper must have: data-cora-inject="agent". For fields that Cora should fill dynamically, add data-cora-field attributes: e.g. <span data-cora-field="name">, <span data-cora-field="phone">. Style: minimal white card, left-aligned, subtle shadow.',
      technicalNote: 'Card container must have data-cora-inject="agent". Use data-cora-field on text elements'
    },
    'stats-bar': {
      id: 'stats-bar',
      title: 'Stats / Metrics Bar',
      icon: '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
      description: 'Animated counter stats row',
      coraFeature: 'Static / Customizable',
      coraAttr: null,
      promptFragment: 'A horizontal stats bar with 4 metric tiles: "500+ Properties Listed", "12 Years Experience", "₹200Cr+ Transactions", "98% Client Satisfaction". Each tile has a large bold number, a label below it, and a thin divider between tiles. Animate the numbers counting up on scroll using a simple IntersectionObserver. Background: dark zinc or black for contrast.',
      technicalNote: 'Static section — no Cora attributes needed'
    },
    'testimonials': {
      id: 'testimonials',
      title: 'Testimonials Carousel',
      icon: '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
      description: 'Client review cards from Cora CRM',
      coraFeature: 'Testimonials API',
      coraAttr: 'data-cora-inject="testimonials"',
      promptFragment: 'A testimonials section with a heading "What Our Clients Say" and a horizontal auto-scrolling carousel of review cards. Each card has: a large opening quote mark, review text, client name (bold), and a 5-star rating. The carousel container must have: data-cora-inject="testimonials" and data-cora-limit="6". Auto-scroll every 4 seconds with a smooth transition. Include left/right arrow navigation buttons.',
      technicalNote: 'Carousel container must have data-cora-inject="testimonials"'
    },
    'booking-button': {
      id: 'booking-button',
      title: 'Booking / Enquiry Button',
      icon: '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
      description: 'Sends booking requests to Cora',
      coraFeature: 'Bookings API',
      coraAttr: 'data-cora-inject="booking-button"',
      promptFragment: 'A prominent CTA button labeled "Book a Site Visit". The button element must have: data-cora-inject="booking-button". Style: full-width on mobile, auto-width on desktop, dark background, white text, rounded, slight shadow. When clicked, Cora automatically sends the booking request to the CRM (no custom JS needed).',
      technicalNote: 'Button must have data-cora-inject="booking-button"'
    }
  };

  // ── Style presets ──────────────────────────────────────────────────────────

  var STYLES = {
    modern: {
      label: 'Modern Clean',
      prompt: 'Visual style: Clean and modern. White and light gray backgrounds. Sharp card borders (1px solid #e5e7eb). System sans-serif typography. Subtle drop shadows on cards. Accent color: #18181b (zinc-900).'
    },
    luxury: {
      label: 'Luxury Premium',
      prompt: 'Visual style: Luxury and premium. Rich black and deep charcoal backgrounds with gold/cream accent tones (#c9a96e). Serif headings, wide letter-spacing, generous whitespace. Cinematic full-width imagery. No rounded corners — sharp edges throughout.'
    },
    minimal: {
      label: 'Ultra Minimal',
      prompt: 'Visual style: Ultra-minimal. Pure white background, no shadows, only thin 1px borders. Monochrome (black and white only). Maximum whitespace. Small, understated typography. No decorative elements.'
    },
    vibrant: {
      label: 'Bold & Vibrant',
      prompt: 'Visual style: Bold and vibrant. Deep indigo/violet gradient background (#4f46e5 → #7c3aed). White text. Bright CTA buttons. Glassmorphism cards (backdrop-blur, semi-transparent white). Dynamic and energetic feel.'
    }
  };

  // ── Layout presets ─────────────────────────────────────────────────────────

  var LAYOUTS = {
    homepage: 'Page layout: Full homepage. Sections from top to bottom: Hero Banner → Stats Bar → Property Search Bar → Property Listings Grid → Testimonials Carousel → Lead Capture Form → Footer with contact details.',
    listings: 'Page layout: Property listings page. Top: Search bar + filter chips row. Below: Full-width property grid (3 columns desktop, 1 column mobile). Sticky sidebar on desktop with filter options.',
    landing: 'Page layout: Lead generation landing page. Top: Hero banner with headline and single CTA. Middle: 3 feature benefit tiles. Below: Agent profile card + lead capture form side by side. Bottom: Social proof strip.',
    detail: 'Page layout: Single property detail page. Top: Full-width image gallery (lightbox). Below: Property title, price, and stat badges. Right sidebar: Booking button + Agent card + Contact form. Bottom: Map embed placeholder.'
  };

  // ── Assembler ──────────────────────────────────────────────────────────────

  function buildPrompt(selectedIds, styleKey, layoutKey) {
    var style  = STYLES[styleKey]  || STYLES.modern;
    var layout = LAYOUTS[layoutKey] || LAYOUTS.homepage;
    var selected = selectedIds.map(function (id) { return COMPONENTS[id]; }).filter(Boolean);

    var technicalReqs = selected
      .filter(function (c) { return c.coraAttr; })
      .map(function (c) { return '  • ' + c.title + ': ' + c.technicalNote; })
      .join('\n');

    var componentDescs = selected
      .map(function (c) { return c.promptFragment; })
      .join('\n\n');

    var baseUrl = (window.location || {}).origin || 'https://yoursite.com';

    var prompt =
'Build a real estate website page with the following specifications.\n\n' +
'━━━ LAYOUT ━━━\n' +
layout + '\n\n' +
'━━━ COMPONENTS ━━━\n' +
componentDescs + '\n\n' +
'━━━ STYLE ━━━\n' +
style.prompt + '\n\n' +
'━━━ TECHNICAL REQUIREMENTS (CRITICAL — do not omit or rename these) ━━━\n' +
'These attributes are how Cora\'s backend connects live data to your design.\n' +
(technicalReqs || '  • No backend data attributes required for selected components.') + '\n\n' +
'Additional required elements:\n' +
'  • Include a hidden input anywhere in the HTML: <input type="hidden" id="cora-nonce" name="cora_nonce" value="">\n' +
'  • All API calls in your JavaScript MUST use: window.CORA_API_URL as the base URL\n' +
'  • All API calls MUST include the header: X-WP-Nonce: window.CORA_NONCE\n' +
'  • Do NOT use mock/hardcoded data for components marked with data-cora-inject — Cora auto-fills them\n\n' +
'━━━ DEPLOYMENT NOTES ━━━\n' +
'  • This page will be served from: ' + baseUrl + '\n' +
'  • Push to GitHub and the owner will import via Cora platform\n' +
'  • All images should use real Unsplash/Pexels URLs — no placeholder.com links\n' +
'  • Make it fully responsive (mobile-first)\n' +
'  • Export as a standard React + Vite project (the default Lovable setup is perfect)';

    return prompt;
  }

  // ── Public API ─────────────────────────────────────────────────────────────

  return {
    COMPONENTS: COMPONENTS,
    STYLES: STYLES,
    LAYOUTS: LAYOUTS,
    buildPrompt: buildPrompt
  };

})();
