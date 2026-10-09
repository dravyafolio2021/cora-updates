export type AssetFormat = 'native_page' | 'section_prompts' | 'elementor_kit' | 'lovable_project';

export interface TemplateSectionItem {
  id: string;
  name: string;
  badge: string;
  description: string;
  promptText: string;
  tailwindSnippet?: string;
  elementorBlueprint?: string;
}

export interface TemplateItem {
  id: string;
  slug: string;
  title: string;
  shortTitle: string;
  tagline: string;
  category: 'landing_pages' | 'section_prompts' | 'elementor_kits' | 'lovable_projects';
  categoryLabel: string;
  assetFormat: AssetFormat;
  formatBadge: string;
  badge: string;
  rating: number;
  reviewsCount: number;
  downloadsCount: number;
  viewsCount?: number;
  copiesCount?: number;
  mediaType?: 'image' | 'video';
  mediaUrl?: string;
  heroDescription: string;
  accentBg: string;
  accentBorder: string;
  accentText: string;
  accentColor: string;
  coverImage: string;
  compatibleBuilders: string[];
  sectionsIncluded: TemplateSectionItem[];
  features: string[];
  toolsReplaced: { name: string; monthlyCostUSD: number }[];
  frameworkStack: string[];
  quickCopyPrompt: string;
  faqs: { question: string; answer: string }[];
}

export interface TemplateCategory {
  id: string;
  label: string;
  shortLabel: string;
  badge: string;
  count: number;
}

export const TEMPLATE_CATEGORIES: TemplateCategory[] = [
  {
    "id": "all",
    "label": "All HTML Assets",
    "shortLabel": "All Assets",
    "badge": "48+ ASSETS",
    "count": 48
  },
  {
    "id": "landing_pages",
    "label": "Landing Pages",
    "shortLabel": "Landing Pages",
    "badge": "HTML + TAILWIND CDN",
    "count": 13
  },
  {
    "id": "section_prompts",
    "label": "AI Section Prompts",
    "shortLabel": "AI Prompts",
    "badge": "STANDALONE HTML",
    "count": 8
  },
  {
    "id": "elementor_kits",
    "label": "Design Styles & Themes",
    "shortLabel": "Styles",
    "badge": "HTML + TAILWIND CDN",
    "count": 15
  },
  {
    "id": "lovable_projects",
    "label": "Interactive & Animations",
    "shortLabel": "Animations",
    "badge": "STANDALONE HTML",
    "count": 12
  }
];

export const TEMPLATES_DATA: TemplateItem[] = [
  {
    "id": "tmpl-01",
    "slug": "animated-landing-page-one-product-that-travels-the-whole-page",
    "title": "Animated Landing Page: One Product That Travels the Whole Page",
    "shortTitle": "Animated Landing Page",
    "tagline": "Production-grade Animated Landing Page: One Product That Travels the Whole Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.93,
    "reviewsCount": 234,
    "downloadsCount": 1202,
    "viewsCount": 3869,
    "copiesCount": 567,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/aa1509da-deb0-4bef-bc77-8e2ef1c33207.gif?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Animated Landing Page: One Product That Travels the Whole Page instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/aa1509da-deb0-4bef-bc77-8e2ef1c33207.gif?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-1-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Animated Landing Page: One Product That Travels the Whole Page.",
        "promptText": "Generate a standalone HTML5 hero section for \"Animated Landing Page: One Product That Travels the Whole Page\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Animated Landing Page: One Product That Travels the Whole Page (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Animated Landing Page: One Product That Travels the Whole Page\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Animated Landing Page: One Product That Travels the Whole Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-1-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Animated Landing Page: One Product That Travels the Whole Page\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-1-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Animated Landing Page: One Product That Travels the Whole Page\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Animated Landing Page</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Animated Landing Page: One Product That Travels the Whole Page\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Animated Landing Page: One Product That Travels the Whole Page\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Animated Landing Page: One Product That Travels the Whole Page\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-02",
    "slug": "animated-landing-page-portal-hero-that-parts-to-uncover-a-full-bleed-image",
    "title": "Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image",
    "shortTitle": "Animated Landing Page",
    "tagline": "Production-grade Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.96,
    "reviewsCount": 71,
    "downloadsCount": 4116,
    "viewsCount": 2164,
    "copiesCount": 581,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/71af9b15-0596-4b08-b997-69920ca62cd3.gif?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/71af9b15-0596-4b08-b997-69920ca62cd3.gif?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-2-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image.",
        "promptText": "Generate a standalone HTML5 hero section for \"Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-2-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-2-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Animated Landing Page</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Animated Landing Page: Portal Hero That Parts to Uncover a Full Bleed Image\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-03",
    "slug": "lumina-saas-landing-page",
    "title": "Lumina SaaS Landing Page",
    "shortTitle": "Lumina SaaS Landing Page",
    "tagline": "Production-grade Lumina SaaS Landing Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.91,
    "reviewsCount": 153,
    "downloadsCount": 3518,
    "viewsCount": 3483,
    "copiesCount": 554,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/7c559ec1-83bb-46c6-bd4b-3e0cf404de62/1770779920155.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Lumina SaaS Landing Page instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/7c559ec1-83bb-46c6-bd4b-3e0cf404de62/1770779920155.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-3-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Lumina SaaS Landing Page.",
        "promptText": "Generate a standalone HTML5 hero section for \"Lumina SaaS Landing Page\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Lumina SaaS Landing Page (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Lumina SaaS Landing Page\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Lumina SaaS Landing Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-3-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Lumina SaaS Landing Page\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-3-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Lumina SaaS Landing Page\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Lumina SaaS Landing Page</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Lumina SaaS Landing Page\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Lumina SaaS Landing Page\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Lumina SaaS Landing Page\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-04",
    "slug": "red-noir-style",
    "title": "Red Noir Style",
    "shortTitle": "Red Noir Style",
    "tagline": "Production-grade Red Noir Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.96,
    "reviewsCount": 174,
    "downloadsCount": 2052,
    "viewsCount": 1633,
    "copiesCount": 621,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/9cc80d0e-1a06-4b2d-97a2-f4c93436fc69/4739694a-be35-4493-bdef-f43fd1d095ec/1767696321250.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Red Noir Style instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/9cc80d0e-1a06-4b2d-97a2-f4c93436fc69/4739694a-be35-4493-bdef-f43fd1d095ec/1767696321250.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-4-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Red Noir Style.",
        "promptText": "Generate a standalone HTML5 hero section for \"Red Noir Style\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Red Noir Style (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Red Noir Style\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Red Noir Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-4-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Red Noir Style\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-4-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Red Noir Style\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Red Noir Style</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Red Noir Style\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Red Noir Style\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Red Noir Style\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-05",
    "slug": "kinetic-orange-style",
    "title": "Kinetic Orange Style",
    "shortTitle": "Kinetic Orange Style",
    "tagline": "Production-grade Kinetic Orange Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.97,
    "reviewsCount": 211,
    "downloadsCount": 4032,
    "viewsCount": 3548,
    "copiesCount": 611,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/723c8592-5795-43de-8068-05f205f21660/9bba597a-d171-450b-b5d0-a3a737433136/1767934028801.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Kinetic Orange Style instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/723c8592-5795-43de-8068-05f205f21660/9bba597a-d171-450b-b5d0-a3a737433136/1767934028801.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-5-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Kinetic Orange Style.",
        "promptText": "Generate a standalone HTML5 hero section for \"Kinetic Orange Style\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Kinetic Orange Style (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Kinetic Orange Style\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Kinetic Orange Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-5-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Kinetic Orange Style\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-5-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Kinetic Orange Style\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Kinetic Orange Style</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Kinetic Orange Style\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Kinetic Orange Style\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Kinetic Orange Style\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-06",
    "slug": "neural-noir-interface-style",
    "title": "Neural Noir Interface Style",
    "shortTitle": "Neural Noir Interface Style",
    "tagline": "Production-grade Neural Noir Interface Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.93,
    "reviewsCount": 195,
    "downloadsCount": 2939,
    "viewsCount": 2968,
    "copiesCount": 622,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/3fb83fbd-a6f3-44a0-af74-b3afca0b7682/1768518296768.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Neural Noir Interface Style instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/3fb83fbd-a6f3-44a0-af74-b3afca0b7682/1768518296768.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-6-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Neural Noir Interface Style.",
        "promptText": "Generate a standalone HTML5 hero section for \"Neural Noir Interface Style\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Neural Noir Interface Style (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Neural Noir Interface Style\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Neural Noir Interface Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-6-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Neural Noir Interface Style\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-6-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Neural Noir Interface Style\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Neural Noir Interface Style</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Neural Noir Interface Style\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Neural Noir Interface Style\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Neural Noir Interface Style\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-07",
    "slug": "nature-inspired-style",
    "title": "Nature Inspired Style",
    "shortTitle": "Nature Inspired Style",
    "tagline": "Production-grade Nature Inspired Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.94,
    "reviewsCount": 223,
    "downloadsCount": 1753,
    "viewsCount": 4810,
    "copiesCount": 513,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/66ce62c6-8d53-4708-9723-3454aec97405/1b908311-3be0-4947-9d88-769d1950b9d6/1767956039662.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Nature Inspired Style instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/66ce62c6-8d53-4708-9723-3454aec97405/1b908311-3be0-4947-9d88-769d1950b9d6/1767956039662.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-7-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Nature Inspired Style.",
        "promptText": "Generate a standalone HTML5 hero section for \"Nature Inspired Style\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Nature Inspired Style (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Nature Inspired Style\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Nature Inspired Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-7-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Nature Inspired Style\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-7-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Nature Inspired Style\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Nature Inspired Style</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Nature Inspired Style\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Nature Inspired Style\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Nature Inspired Style\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-08",
    "slug": "synapse",
    "title": "Synapse",
    "shortTitle": "Synapse",
    "tagline": "Production-grade Synapse standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.97,
    "reviewsCount": 290,
    "downloadsCount": 1981,
    "viewsCount": 2388,
    "copiesCount": 589,
    "mediaType": "video",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/456925c9-ac7e-4d99-80f5-8addbcab5780.mp4",
    "heroDescription": "Build and preview Synapse instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/456925c9-ac7e-4d99-80f5-8addbcab5780.mp4",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-8-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Synapse.",
        "promptText": "Generate a standalone HTML5 hero section for \"Synapse\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Synapse (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Synapse\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Synapse standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-8-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Synapse\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-8-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Synapse\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Synapse</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Synapse\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Synapse\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Synapse\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-09",
    "slug": "saas-landing-page-for-developer-tool",
    "title": "SaaS Landing Page for Developer Tool",
    "shortTitle": "SaaS Landing Page for Develope...",
    "tagline": "Production-grade SaaS Landing Page for Developer Tool standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.94,
    "reviewsCount": 69,
    "downloadsCount": 2656,
    "viewsCount": 1668,
    "copiesCount": 557,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/a8f544ea-ba22-4214-ade4-4cbd56118443/1770866149216.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview SaaS Landing Page for Developer Tool instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/a8f544ea-ba22-4214-ade4-4cbd56118443/1770866149216.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-9-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for SaaS Landing Page for Developer Tool.",
        "promptText": "Generate a standalone HTML5 hero section for \"SaaS Landing Page for Developer Tool\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for SaaS Landing Page for Developer Tool (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        SaaS Landing Page for Developer Tool\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade SaaS Landing Page for Developer Tool standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-9-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"SaaS Landing Page for Developer Tool\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-9-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"SaaS Landing Page for Developer Tool\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience SaaS Landing Page for Develope...</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"SaaS Landing Page for Developer Tool\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"SaaS Landing Page for Developer Tool\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: SaaS Landing Page for Developer Tool\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-10",
    "slug": "terminal-portfolio-website-retro-crt-phosphor-green-developer-portfolio",
    "title": "Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio",
    "shortTitle": "Terminal Portfolio Website",
    "tagline": "Production-grade Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.91,
    "reviewsCount": 251,
    "downloadsCount": 2183,
    "viewsCount": 2658,
    "copiesCount": 819,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/a5c28285-79ce-467d-8e50-75d0d047f115.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/a5c28285-79ce-467d-8e50-75d0d047f115.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-10-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio.",
        "promptText": "Generate a standalone HTML5 hero section for \"Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-10-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-10-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Terminal Portfolio Website</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Terminal Portfolio Website: Retro CRT Phosphor-Green Developer Portfolio\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-11",
    "slug": "ascii-hero",
    "title": "Ascii hero",
    "shortTitle": "Ascii hero",
    "tagline": "Production-grade Ascii hero standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "section_prompts",
    "categoryLabel": "AI Section Prompt",
    "assetFormat": "section_prompts",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "AI MASTER PROMPT",
    "rating": 4.94,
    "reviewsCount": 281,
    "downloadsCount": 1611,
    "viewsCount": 3131,
    "copiesCount": 784,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/d0848987-0c46-4d26-9e88-a652fee697e1/1785721008007.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Ascii hero instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/d0848987-0c46-4d26-9e88-a652fee697e1/1785721008007.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-11-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Ascii hero.",
        "promptText": "Generate a standalone HTML5 hero section for \"Ascii hero\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Ascii hero (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>AI SECTION PROMPT</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Ascii hero\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Ascii hero standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-11-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Ascii hero\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-11-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Ascii hero\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Ascii hero</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Ascii hero\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Ascii hero\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Ascii hero\nCategory: AI Section Prompt",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-12",
    "slug": "serene-find-my-dream",
    "title": "Serene \u2014 Find My Dream",
    "shortTitle": "Serene",
    "tagline": "Production-grade Serene \u2014 Find My Dream standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "section_prompts",
    "categoryLabel": "AI Section Prompt",
    "assetFormat": "section_prompts",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "AI MASTER PROMPT",
    "rating": 4.98,
    "reviewsCount": 203,
    "downloadsCount": 3674,
    "viewsCount": 3511,
    "copiesCount": 660,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/e9729669-1c86-4800-85a5-c2754d7a1591/1785029818112.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Serene \u2014 Find My Dream instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/e9729669-1c86-4800-85a5-c2754d7a1591/1785029818112.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-12-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Serene \u2014 Find My Dream.",
        "promptText": "Generate a standalone HTML5 hero section for \"Serene \u2014 Find My Dream\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Serene \u2014 Find My Dream (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>AI SECTION PROMPT</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Serene \u2014 Find My Dream\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Serene \u2014 Find My Dream standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-12-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Serene \u2014 Find My Dream\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-12-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Serene \u2014 Find My Dream\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Serene</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Serene \u2014 Find My Dream\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Serene \u2014 Find My Dream\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Serene \u2014 Find My Dream\nCategory: AI Section Prompt",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-13",
    "slug": "softly-digital-wellness-app",
    "title": "Softly - Digital Wellness App",
    "shortTitle": "Softly - Digital Wellness App",
    "tagline": "Production-grade Softly - Digital Wellness App standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "section_prompts",
    "categoryLabel": "AI Section Prompt",
    "assetFormat": "section_prompts",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "AI MASTER PROMPT",
    "rating": 4.92,
    "reviewsCount": 225,
    "downloadsCount": 1887,
    "viewsCount": 2731,
    "copiesCount": 805,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/efa2aaa5-0358-40b7-b524-ba300874c206/thumbnails/f8bde81c-e9f4-40df-8791-4f4ad81e977d.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Softly - Digital Wellness App instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/efa2aaa5-0358-40b7-b524-ba300874c206/thumbnails/f8bde81c-e9f4-40df-8791-4f4ad81e977d.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-13-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Softly - Digital Wellness App.",
        "promptText": "Generate a standalone HTML5 hero section for \"Softly - Digital Wellness App\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Softly - Digital Wellness App (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>AI SECTION PROMPT</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Softly - Digital Wellness App\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Softly - Digital Wellness App standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-13-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Softly - Digital Wellness App\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-13-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Softly - Digital Wellness App\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Softly - Digital Wellness App</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Softly - Digital Wellness App\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Softly - Digital Wellness App\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Softly - Digital Wellness App\nCategory: AI Section Prompt",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-14",
    "slug": "glassmorphism-style",
    "title": "Glassmorphism Style",
    "shortTitle": "Glassmorphism Style",
    "tagline": "Production-grade Glassmorphism Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.98,
    "reviewsCount": 65,
    "downloadsCount": 2285,
    "viewsCount": 3958,
    "copiesCount": 626,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/285ed12a-0b4a-435d-b212-b2dc30cee0cb/8ba9a623-981c-49ae-8ce7-679d477ef084/1767756243811.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Glassmorphism Style instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/285ed12a-0b4a-435d-b212-b2dc30cee0cb/8ba9a623-981c-49ae-8ce7-679d477ef084/1767756243811.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-14-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Glassmorphism Style.",
        "promptText": "Generate a standalone HTML5 hero section for \"Glassmorphism Style\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Glassmorphism Style (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Glassmorphism Style\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Glassmorphism Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-14-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Glassmorphism Style\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-14-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Glassmorphism Style\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Glassmorphism Style</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Glassmorphism Style\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Glassmorphism Style\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Glassmorphism Style\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-15",
    "slug": "mosaic-grid-architecture-style",
    "title": "Mosaic Grid Architecture Style",
    "shortTitle": "Mosaic Grid Architecture Style",
    "tagline": "Production-grade Mosaic Grid Architecture Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.95,
    "reviewsCount": 116,
    "downloadsCount": 2656,
    "viewsCount": 4799,
    "copiesCount": 561,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/d920e8cf-1030-472b-92cc-5bf9453149b5/1769428363698.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Mosaic Grid Architecture Style instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/d920e8cf-1030-472b-92cc-5bf9453149b5/1769428363698.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-15-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Mosaic Grid Architecture Style.",
        "promptText": "Generate a standalone HTML5 hero section for \"Mosaic Grid Architecture Style\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Mosaic Grid Architecture Style (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Mosaic Grid Architecture Style\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Mosaic Grid Architecture Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-15-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Mosaic Grid Architecture Style\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-15-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Mosaic Grid Architecture Style\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Mosaic Grid Architecture Style</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Mosaic Grid Architecture Style\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Mosaic Grid Architecture Style\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Mosaic Grid Architecture Style\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-16",
    "slug": "glassmorphism-card",
    "title": "Glassmorphism card",
    "shortTitle": "Glassmorphism card",
    "tagline": "Production-grade Glassmorphism card standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "section_prompts",
    "categoryLabel": "AI Section Prompt",
    "assetFormat": "section_prompts",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "AI MASTER PROMPT",
    "rating": 4.94,
    "reviewsCount": 139,
    "downloadsCount": 1766,
    "viewsCount": 4666,
    "copiesCount": 696,
    "mediaType": "video",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/8a35caaa-1f8e-4437-afdc-a4ee495e1578.mp4",
    "heroDescription": "Build and preview Glassmorphism card instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/8a35caaa-1f8e-4437-afdc-a4ee495e1578.mp4",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-16-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Glassmorphism card.",
        "promptText": "Generate a standalone HTML5 hero section for \"Glassmorphism card\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Glassmorphism card (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>AI SECTION PROMPT</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Glassmorphism card\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Glassmorphism card standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-16-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Glassmorphism card\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-16-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Glassmorphism card\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Glassmorphism card</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Glassmorphism card\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Glassmorphism card\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Glassmorphism card\nCategory: AI Section Prompt",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-17",
    "slug": "cinematic-style",
    "title": "Cinematic Style",
    "shortTitle": "Cinematic Style",
    "tagline": "Production-grade Cinematic Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.96,
    "reviewsCount": 284,
    "downloadsCount": 3974,
    "viewsCount": 3995,
    "copiesCount": 646,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/5b9fab26-1846-47f4-bbf9-c25625de9c15/640a611e-0067-41ab-bb5d-6d7e627e20ff/1767957267935.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Cinematic Style instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/5b9fab26-1846-47f4-bbf9-c25625de9c15/640a611e-0067-41ab-bb5d-6d7e627e20ff/1767957267935.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-17-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Cinematic Style.",
        "promptText": "Generate a standalone HTML5 hero section for \"Cinematic Style\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Cinematic Style (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Cinematic Style\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Cinematic Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-17-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Cinematic Style\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-17-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Cinematic Style\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Cinematic Style</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Cinematic Style\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Cinematic Style\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Cinematic Style\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-18",
    "slug": "bold-editorial-style",
    "title": "Bold Editorial Style",
    "shortTitle": "Bold Editorial Style",
    "tagline": "Production-grade Bold Editorial Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.95,
    "reviewsCount": 88,
    "downloadsCount": 3700,
    "viewsCount": 1542,
    "copiesCount": 821,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/36133045-75cc-4611-bdbb-0ec16bdc76fd/thumbnails/4cbe9ac1-be61-4114-917f-caaa1a7fa235.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Bold Editorial Style instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/36133045-75cc-4611-bdbb-0ec16bdc76fd/thumbnails/4cbe9ac1-be61-4114-917f-caaa1a7fa235.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-18-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Bold Editorial Style.",
        "promptText": "Generate a standalone HTML5 hero section for \"Bold Editorial Style\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Bold Editorial Style (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Bold Editorial Style\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Bold Editorial Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-18-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Bold Editorial Style\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-18-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Bold Editorial Style\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Bold Editorial Style</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Bold Editorial Style\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Bold Editorial Style\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Bold Editorial Style\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-19",
    "slug": "observe-see-the-shape-of-the-noise",
    "title": "Observe \u2014 See the shape of the noise",
    "shortTitle": "Observe",
    "tagline": "Production-grade Observe \u2014 See the shape of the noise standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "section_prompts",
    "categoryLabel": "AI Section Prompt",
    "assetFormat": "section_prompts",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "AI MASTER PROMPT",
    "rating": 4.93,
    "reviewsCount": 142,
    "downloadsCount": 2993,
    "viewsCount": 2252,
    "copiesCount": 593,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/6714409b-5286-4411-8799-21cbc39565e9/1785161973036.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Observe \u2014 See the shape of the noise instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/6714409b-5286-4411-8799-21cbc39565e9/1785161973036.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-19-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Observe \u2014 See the shape of the noise.",
        "promptText": "Generate a standalone HTML5 hero section for \"Observe \u2014 See the shape of the noise\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Observe \u2014 See the shape of the noise (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>AI SECTION PROMPT</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Observe \u2014 See the shape of the noise\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Observe \u2014 See the shape of the noise standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-19-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Observe \u2014 See the shape of the noise\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-19-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Observe \u2014 See the shape of the noise\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Observe</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Observe \u2014 See the shape of the noise\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Observe \u2014 See the shape of the noise\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Observe \u2014 See the shape of the noise\nCategory: AI Section Prompt",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-20",
    "slug": "brutalist-style-ecommerce-page",
    "title": "Brutalist Style Ecommerce Page",
    "shortTitle": "Brutalist Style Ecommerce Page",
    "tagline": "Production-grade Brutalist Style Ecommerce Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.96,
    "reviewsCount": 101,
    "downloadsCount": 3381,
    "viewsCount": 3871,
    "copiesCount": 862,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/10ecb2c3-19d7-405b-926e-3fa74e142661/1770867159017.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Brutalist Style Ecommerce Page instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/10ecb2c3-19d7-405b-926e-3fa74e142661/1770867159017.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-20-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Brutalist Style Ecommerce Page.",
        "promptText": "Generate a standalone HTML5 hero section for \"Brutalist Style Ecommerce Page\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Brutalist Style Ecommerce Page (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Brutalist Style Ecommerce Page\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Brutalist Style Ecommerce Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-20-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Brutalist Style Ecommerce Page\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-20-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Brutalist Style Ecommerce Page\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Brutalist Style Ecommerce Page</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Brutalist Style Ecommerce Page\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Brutalist Style Ecommerce Page\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Brutalist Style Ecommerce Page\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-21",
    "slug": "high-contrast-landing-page",
    "title": "High Contrast Landing Page",
    "shortTitle": "High Contrast Landing Page",
    "tagline": "Production-grade High Contrast Landing Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.98,
    "reviewsCount": 103,
    "downloadsCount": 1329,
    "viewsCount": 4702,
    "copiesCount": 903,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/36133045-75cc-4611-bdbb-0ec16bdc76fd/thumbnails/87ff6929-9218-400c-8e53-29de4297f821.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview High Contrast Landing Page instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/36133045-75cc-4611-bdbb-0ec16bdc76fd/thumbnails/87ff6929-9218-400c-8e53-29de4297f821.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-21-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for High Contrast Landing Page.",
        "promptText": "Generate a standalone HTML5 hero section for \"High Contrast Landing Page\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for High Contrast Landing Page (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        High Contrast Landing Page\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade High Contrast Landing Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-21-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"High Contrast Landing Page\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-21-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"High Contrast Landing Page\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience High Contrast Landing Page</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"High Contrast Landing Page\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"High Contrast Landing Page\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: High Contrast Landing Page\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-22",
    "slug": "deep-red-style",
    "title": "Deep Red Style",
    "shortTitle": "Deep Red Style",
    "tagline": "Production-grade Deep Red Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.92,
    "reviewsCount": 113,
    "downloadsCount": 2743,
    "viewsCount": 4547,
    "copiesCount": 671,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/c0ee9faa-a4c2-422b-a59b-35216d817ee9/9930e9a0-b26d-4609-912b-2c24eb68242e/1768258746034.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Deep Red Style instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/c0ee9faa-a4c2-422b-a59b-35216d817ee9/9930e9a0-b26d-4609-912b-2c24eb68242e/1768258746034.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-22-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Deep Red Style.",
        "promptText": "Generate a standalone HTML5 hero section for \"Deep Red Style\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Deep Red Style (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Deep Red Style\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Deep Red Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-22-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Deep Red Style\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-22-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Deep Red Style\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Deep Red Style</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Deep Red Style\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Deep Red Style\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Deep Red Style\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-23",
    "slug": "animated-aurora-background-hero-dark-developer-tool-landing-page",
    "title": "Animated Aurora Background Hero: Dark Developer-Tool Landing Page",
    "shortTitle": "Animated Aurora Background Hero",
    "tagline": "Production-grade Animated Aurora Background Hero: Dark Developer-Tool Landing Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.93,
    "reviewsCount": 125,
    "downloadsCount": 4040,
    "viewsCount": 3573,
    "copiesCount": 958,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/24a01df1-b0e3-4dc4-afd3-900830e2dd75.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Animated Aurora Background Hero: Dark Developer-Tool Landing Page instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/24a01df1-b0e3-4dc4-afd3-900830e2dd75.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-23-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Animated Aurora Background Hero: Dark Developer-Tool Landing Page.",
        "promptText": "Generate a standalone HTML5 hero section for \"Animated Aurora Background Hero: Dark Developer-Tool Landing Page\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Animated Aurora Background Hero: Dark Developer-Tool Landing Page (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Animated Aurora Background Hero: Dark Developer-Tool Landing Page\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Animated Aurora Background Hero: Dark Developer-Tool Landing Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-23-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Animated Aurora Background Hero: Dark Developer-Tool Landing Page\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-23-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Animated Aurora Background Hero: Dark Developer-Tool Landing Page\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Animated Aurora Background Hero</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Animated Aurora Background Hero: Dark Developer-Tool Landing Page\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Animated Aurora Background Hero: Dark Developer-Tool Landing Page\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Animated Aurora Background Hero: Dark Developer-Tool Landing Page\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-24",
    "slug": "brutalist-e-commerce-page",
    "title": "Brutalist E-commerce Page",
    "shortTitle": "Brutalist E-commerce Page",
    "tagline": "Production-grade Brutalist E-commerce Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.92,
    "reviewsCount": 162,
    "downloadsCount": 3733,
    "viewsCount": 3294,
    "copiesCount": 712,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/da34d4ac-cb7e-4391-af07-ec9085970f46/1770867822164.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Brutalist E-commerce Page instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/da34d4ac-cb7e-4391-af07-ec9085970f46/1770867822164.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-24-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Brutalist E-commerce Page.",
        "promptText": "Generate a standalone HTML5 hero section for \"Brutalist E-commerce Page\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Brutalist E-commerce Page (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Brutalist E-commerce Page\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Brutalist E-commerce Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-24-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Brutalist E-commerce Page\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-24-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Brutalist E-commerce Page\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Brutalist E-commerce Page</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Brutalist E-commerce Page\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Brutalist E-commerce Page\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Brutalist E-commerce Page\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-25",
    "slug": "luxury-focused-design-system",
    "title": "Luxury-focused Design System",
    "shortTitle": "Luxury-focused Design System",
    "tagline": "Production-grade Luxury-focused Design System standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.95,
    "reviewsCount": 188,
    "downloadsCount": 4151,
    "viewsCount": 1821,
    "copiesCount": 636,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/36133045-75cc-4611-bdbb-0ec16bdc76fd/thumbnails/9047e64c-e86a-4b2b-a37d-3107c69b717d.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Luxury-focused Design System instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/36133045-75cc-4611-bdbb-0ec16bdc76fd/thumbnails/9047e64c-e86a-4b2b-a37d-3107c69b717d.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-25-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Luxury-focused Design System.",
        "promptText": "Generate a standalone HTML5 hero section for \"Luxury-focused Design System\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Luxury-focused Design System (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Luxury-focused Design System\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Luxury-focused Design System standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-25-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Luxury-focused Design System\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-25-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Luxury-focused Design System\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Luxury-focused Design System</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Luxury-focused Design System\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Luxury-focused Design System\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Luxury-focused Design System\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-26",
    "slug": "bold-editorial-studio-style",
    "title": "Bold Editorial Studio Style",
    "shortTitle": "Bold Editorial Studio Style",
    "tagline": "Production-grade Bold Editorial Studio Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.96,
    "reviewsCount": 274,
    "downloadsCount": 2854,
    "viewsCount": 4309,
    "copiesCount": 809,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/c83e4f73-9af1-43a7-be18-3e1f85166359/78740b9d-a825-44a1-ac81-51da434278a3/1767952517451.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Bold Editorial Studio Style instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/c83e4f73-9af1-43a7-be18-3e1f85166359/78740b9d-a825-44a1-ac81-51da434278a3/1767952517451.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-26-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Bold Editorial Studio Style.",
        "promptText": "Generate a standalone HTML5 hero section for \"Bold Editorial Studio Style\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Bold Editorial Studio Style (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Bold Editorial Studio Style\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Bold Editorial Studio Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-26-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Bold Editorial Studio Style\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-26-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Bold Editorial Studio Style\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Bold Editorial Studio Style</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Bold Editorial Studio Style\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Bold Editorial Studio Style\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Bold Editorial Studio Style\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-27",
    "slug": "babybites-sophisticated-palette",
    "title": "BabyBites - Sophisticated Palette",
    "shortTitle": "BabyBites - Sophisticated Pale...",
    "tagline": "Production-grade BabyBites - Sophisticated Palette standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.95,
    "reviewsCount": 175,
    "downloadsCount": 1666,
    "viewsCount": 2732,
    "copiesCount": 622,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/36133045-75cc-4611-bdbb-0ec16bdc76fd/thumbnails/298613ea-ac38-44c4-b183-bca3e8d029b4.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview BabyBites - Sophisticated Palette instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/36133045-75cc-4611-bdbb-0ec16bdc76fd/thumbnails/298613ea-ac38-44c4-b183-bca3e8d029b4.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-27-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for BabyBites - Sophisticated Palette.",
        "promptText": "Generate a standalone HTML5 hero section for \"BabyBites - Sophisticated Palette\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for BabyBites - Sophisticated Palette (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        BabyBites - Sophisticated Palette\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade BabyBites - Sophisticated Palette standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-27-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"BabyBites - Sophisticated Palette\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-27-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"BabyBites - Sophisticated Palette\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience BabyBites - Sophisticated Pale...</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"BabyBites - Sophisticated Palette\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"BabyBites - Sophisticated Palette\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: BabyBites - Sophisticated Palette\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-28",
    "slug": "animated-landing-page-cut-out-subject-rising-between-two-lines-of-split-display-type",
    "title": "Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type",
    "shortTitle": "Animated Landing Page",
    "tagline": "Production-grade Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.96,
    "reviewsCount": 84,
    "downloadsCount": 1549,
    "viewsCount": 4345,
    "copiesCount": 534,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/ad962f6e-204d-4fb0-b52b-afe4b47ebb9b.gif?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/ad962f6e-204d-4fb0-b52b-afe4b47ebb9b.gif?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-28-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type.",
        "promptText": "Generate a standalone HTML5 hero section for \"Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-28-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-28-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Animated Landing Page</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Animated Landing Page: Cut-Out Subject Rising Between Two Lines of Split Display Type\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-29",
    "slug": "midnight-editorial-style",
    "title": "Midnight Editorial Style",
    "shortTitle": "Midnight Editorial Style",
    "tagline": "Production-grade Midnight Editorial Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "elementor_kits",
    "categoryLabel": "Design Style",
    "assetFormat": "elementor_kit",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "DESIGN TOKENS",
    "rating": 4.92,
    "reviewsCount": 197,
    "downloadsCount": 2829,
    "viewsCount": 4494,
    "copiesCount": 858,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/a36ad1fa-6ed0-4a22-b24c-c64c863d9a71/fd7824aa-041a-4b80-9447-60e1f2383c37/1767926755858.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Midnight Editorial Style instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/a36ad1fa-6ed0-4a22-b24c-c64c863d9a71/fd7824aa-041a-4b80-9447-60e1f2383c37/1767926755858.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-29-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Midnight Editorial Style.",
        "promptText": "Generate a standalone HTML5 hero section for \"Midnight Editorial Style\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Midnight Editorial Style (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>DESIGN STYLE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Midnight Editorial Style\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Midnight Editorial Style standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-29-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Midnight Editorial Style\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-29-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Midnight Editorial Style\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Midnight Editorial Style</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Midnight Editorial Style\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Midnight Editorial Style\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Midnight Editorial Style\nCategory: Design Style",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-30",
    "slug": "hyper-saturated-fluid",
    "title": "Hyper-Saturated Fluid",
    "shortTitle": "Hyper-Saturated Fluid",
    "tagline": "Production-grade Hyper-Saturated Fluid standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.93,
    "reviewsCount": 180,
    "downloadsCount": 3017,
    "viewsCount": 2813,
    "copiesCount": 815,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/9dc7110c-6503-4a1e-b052-0a24ae46081f/847c4d03-e56e-422b-ba8e-5f5262db3e3c/1767351135230.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Hyper-Saturated Fluid instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/9dc7110c-6503-4a1e-b052-0a24ae46081f/847c4d03-e56e-422b-ba8e-5f5262db3e3c/1767351135230.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-30-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Hyper-Saturated Fluid.",
        "promptText": "Generate a standalone HTML5 hero section for \"Hyper-Saturated Fluid\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Hyper-Saturated Fluid (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Hyper-Saturated Fluid\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Hyper-Saturated Fluid standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-30-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Hyper-Saturated Fluid\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-30-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Hyper-Saturated Fluid\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Hyper-Saturated Fluid</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Hyper-Saturated Fluid\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Hyper-Saturated Fluid\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Hyper-Saturated Fluid\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-31",
    "slug": "tubes-interactive-background",
    "title": "Tubes Interactive Background",
    "shortTitle": "Tubes Interactive Background",
    "tagline": "Production-grade Tubes Interactive Background standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.97,
    "reviewsCount": 219,
    "downloadsCount": 1147,
    "viewsCount": 3516,
    "copiesCount": 950,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/070b9477-5ebf-41b4-ab04-9b4d4975e984/79f658a0-893a-4705-8427-831d1091c385/1767688413116.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Tubes Interactive Background instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/070b9477-5ebf-41b4-ab04-9b4d4975e984/79f658a0-893a-4705-8427-831d1091c385/1767688413116.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-31-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Tubes Interactive Background.",
        "promptText": "Generate a standalone HTML5 hero section for \"Tubes Interactive Background\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Tubes Interactive Background (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Tubes Interactive Background\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Tubes Interactive Background standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-31-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Tubes Interactive Background\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-31-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Tubes Interactive Background\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Tubes Interactive Background</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Tubes Interactive Background\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Tubes Interactive Background\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Tubes Interactive Background\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-32",
    "slug": "scroll-journey-line",
    "title": "Scroll Journey Line",
    "shortTitle": "Scroll Journey Line",
    "tagline": "Production-grade Scroll Journey Line standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "section_prompts",
    "categoryLabel": "AI Section Prompt",
    "assetFormat": "section_prompts",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "AI MASTER PROMPT",
    "rating": 4.93,
    "reviewsCount": 237,
    "downloadsCount": 3299,
    "viewsCount": 4042,
    "copiesCount": 963,
    "mediaType": "video",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/f2890579-0e12-47a7-a098-c923b3401f1d.mp4",
    "heroDescription": "Build and preview Scroll Journey Line instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/f2890579-0e12-47a7-a098-c923b3401f1d.mp4",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-32-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Scroll Journey Line.",
        "promptText": "Generate a standalone HTML5 hero section for \"Scroll Journey Line\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Scroll Journey Line (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>AI SECTION PROMPT</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Scroll Journey Line\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Scroll Journey Line standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-32-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Scroll Journey Line\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-32-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Scroll Journey Line\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Scroll Journey Line</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Scroll Journey Line\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Scroll Journey Line\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Scroll Journey Line\nCategory: AI Section Prompt",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-33",
    "slug": "animated-scroll-printed-product-page-warm-cream-and-red-editorial-with-a-horizontal-card-rail",
    "title": "Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail",
    "shortTitle": "Animated Scroll-Printed Produc...",
    "tagline": "Production-grade Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.94,
    "reviewsCount": 120,
    "downloadsCount": 1556,
    "viewsCount": 3875,
    "copiesCount": 684,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/68858fe4-1859-401d-916a-e3efae680b53.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/68858fe4-1859-401d-916a-e3efae680b53.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-33-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail.",
        "promptText": "Generate a standalone HTML5 hero section for \"Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-33-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-33-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Animated Scroll-Printed Produc...</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Animated Scroll-Printed Product Page: Warm Cream and Red Editorial With a Horizontal Card Rail\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-34",
    "slug": "force-field-background",
    "title": "Force Field Background",
    "shortTitle": "Force Field Background",
    "tagline": "Production-grade Force Field Background standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.97,
    "reviewsCount": 269,
    "downloadsCount": 4057,
    "viewsCount": 3108,
    "copiesCount": 511,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/febbd3b8-b30b-407b-a997-55442e42be27/f8ed68c7-e970-4fa3-85de-5064855eb254/1767689085963.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Force Field Background instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/febbd3b8-b30b-407b-a997-55442e42be27/f8ed68c7-e970-4fa3-85de-5064855eb254/1767689085963.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-34-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Force Field Background.",
        "promptText": "Generate a standalone HTML5 hero section for \"Force Field Background\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Force Field Background (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Force Field Background\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Force Field Background standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-34-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Force Field Background\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-34-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Force Field Background\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Force Field Background</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Force Field Background\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Force Field Background\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Force Field Background\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-35",
    "slug": "animated-photographic-landing-page-scroll-opened-image-frames-and-a-circular-iris-reveal",
    "title": "Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal",
    "shortTitle": "Animated Photographic Landing ...",
    "tagline": "Production-grade Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.98,
    "reviewsCount": 174,
    "downloadsCount": 1831,
    "viewsCount": 3300,
    "copiesCount": 900,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/58390210-a01d-464c-8dd1-ce59eab36e0f.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/58390210-a01d-464c-8dd1-ce59eab36e0f.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-35-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal.",
        "promptText": "Generate a standalone HTML5 hero section for \"Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-35-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-35-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Animated Photographic Landing ...</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Animated Photographic Landing Page: Scroll-Opened Image Frames and a Circular Iris Reveal\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-36",
    "slug": "the-stacking-cards-effect",
    "title": "The \"Stacking Cards\" Effect",
    "shortTitle": "The \"Stacking Cards\" Effect",
    "tagline": "Production-grade The \"Stacking Cards\" Effect standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.96,
    "reviewsCount": 260,
    "downloadsCount": 2322,
    "viewsCount": 4815,
    "copiesCount": 830,
    "mediaType": "video",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/022542b0-a81a-4e3a-a7f2-82d73da7464e.mp4",
    "heroDescription": "Build and preview The \"Stacking Cards\" Effect instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/022542b0-a81a-4e3a-a7f2-82d73da7464e.mp4",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-36-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for The \"Stacking Cards\" Effect.",
        "promptText": "Generate a standalone HTML5 hero section for \"The \"Stacking Cards\" Effect\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for The \"Stacking Cards\" Effect (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        The \"Stacking Cards\" Effect\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade The \"Stacking Cards\" Effect standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-36-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"The \"Stacking Cards\" Effect\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-36-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"The \"Stacking Cards\" Effect\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience The \"Stacking Cards\" Effect</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"The \"Stacking Cards\" Effect\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"The \"Stacking Cards\" Effect\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: The \"Stacking Cards\" Effect\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-37",
    "slug": "animated-memphis-landing-page-hero-bold-postmodern-product-site",
    "title": "Animated Memphis Landing Page Hero (Bold Postmodern Product Site)",
    "shortTitle": "Animated Memphis Landing Page ...",
    "tagline": "Production-grade Animated Memphis Landing Page Hero (Bold Postmodern Product Site) standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.97,
    "reviewsCount": 140,
    "downloadsCount": 1726,
    "viewsCount": 3744,
    "copiesCount": 611,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/f034e25a-ef28-4da3-bd67-ae3c27904444.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Animated Memphis Landing Page Hero (Bold Postmodern Product Site) instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/f034e25a-ef28-4da3-bd67-ae3c27904444.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-37-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Animated Memphis Landing Page Hero (Bold Postmodern Product Site).",
        "promptText": "Generate a standalone HTML5 hero section for \"Animated Memphis Landing Page Hero (Bold Postmodern Product Site)\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Animated Memphis Landing Page Hero (Bold Postmodern Product Site) (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Animated Memphis Landing Page Hero (Bold Postmodern Product Site)\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Animated Memphis Landing Page Hero (Bold Postmodern Product Site) standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-37-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Animated Memphis Landing Page Hero (Bold Postmodern Product Site)\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-37-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Animated Memphis Landing Page Hero (Bold Postmodern Product Site)\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Animated Memphis Landing Page ...</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Animated Memphis Landing Page Hero (Bold Postmodern Product Site)\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Animated Memphis Landing Page Hero (Bold Postmodern Product Site)\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Animated Memphis Landing Page Hero (Bold Postmodern Product Site)\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-38",
    "slug": "animated-didone-landing-page-blush-and-dusty-pink-editorial-with-multiply-blended-product-photography",
    "title": "Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography",
    "shortTitle": "Animated Didone Landing Page",
    "tagline": "Production-grade Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.91,
    "reviewsCount": 280,
    "downloadsCount": 3272,
    "viewsCount": 3459,
    "copiesCount": 908,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/a172b6b1-dcfc-47ea-8198-9bb41f16b876.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/a172b6b1-dcfc-47ea-8198-9bb41f16b876.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-38-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography.",
        "promptText": "Generate a standalone HTML5 hero section for \"Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-38-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-38-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Animated Didone Landing Page</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Animated Didone Landing Page: Blush and Dusty Pink Editorial With Multiply-Blended Product Photography\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-39",
    "slug": "card-swap",
    "title": "Card Swap",
    "shortTitle": "Card Swap",
    "tagline": "Production-grade Card Swap standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.98,
    "reviewsCount": 73,
    "downloadsCount": 1179,
    "viewsCount": 2577,
    "copiesCount": 760,
    "mediaType": "video",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/95796f7a-9c86-427a-b9b7-42789194ddf6.mp4",
    "heroDescription": "Build and preview Card Swap instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/95796f7a-9c86-427a-b9b7-42789194ddf6.mp4",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-39-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Card Swap.",
        "promptText": "Generate a standalone HTML5 hero section for \"Card Swap\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Card Swap (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Card Swap\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Card Swap standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-39-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Card Swap\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-39-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Card Swap\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Card Swap</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Card Swap\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Card Swap\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Card Swap\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-40",
    "slug": "animated-scroll-pinned-webgl-ribbon-hero-dark-indigo-ai-audio-landing-page",
    "title": "Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page",
    "shortTitle": "Animated Scroll-Pinned WebGL R...",
    "tagline": "Production-grade Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "landing_pages",
    "categoryLabel": "Landing Page",
    "assetFormat": "native_page",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "HERO SYSTEM",
    "rating": 4.91,
    "reviewsCount": 106,
    "downloadsCount": 2359,
    "viewsCount": 4849,
    "copiesCount": 935,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/d74130c6-cd7d-44c5-9c18-36c772a66bae.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/d74130c6-cd7d-44c5-9c18-36c772a66bae.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-40-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page.",
        "promptText": "Generate a standalone HTML5 hero section for \"Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>LANDING PAGE</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-40-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-40-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Animated Scroll-Pinned WebGL R...</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Animated Scroll-Pinned WebGL Ribbon Hero: Dark Indigo AI Audio Landing Page\nCategory: Landing Page",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-41",
    "slug": "hyper-speed-loading-state",
    "title": "Hyper-Speed Loading State",
    "shortTitle": "Hyper-Speed Loading State",
    "tagline": "Production-grade Hyper-Speed Loading State standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "section_prompts",
    "categoryLabel": "AI Section Prompt",
    "assetFormat": "section_prompts",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "AI MASTER PROMPT",
    "rating": 4.97,
    "reviewsCount": 66,
    "downloadsCount": 1422,
    "viewsCount": 4846,
    "copiesCount": 800,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/76761dad-ddad-4f10-bd16-f5c71144d577/1768468296297.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Hyper-Speed Loading State instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/drafts/76761dad-ddad-4f10-bd16-f5c71144d577/1768468296297.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-41-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Hyper-Speed Loading State.",
        "promptText": "Generate a standalone HTML5 hero section for \"Hyper-Speed Loading State\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Hyper-Speed Loading State (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>AI SECTION PROMPT</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Hyper-Speed Loading State\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Hyper-Speed Loading State standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-41-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Hyper-Speed Loading State\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-41-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Hyper-Speed Loading State\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Hyper-Speed Loading State</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Hyper-Speed Loading State\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Hyper-Speed Loading State\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Hyper-Speed Loading State\nCategory: AI Section Prompt",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-42",
    "slug": "pill-nav",
    "title": "Pill Nav",
    "shortTitle": "Pill Nav",
    "tagline": "Production-grade Pill Nav standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.92,
    "reviewsCount": 241,
    "downloadsCount": 3281,
    "viewsCount": 4592,
    "copiesCount": 545,
    "mediaType": "video",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/0e6cc2c8-3d97-43cc-87a5-d271b3f63f33.mp4",
    "heroDescription": "Build and preview Pill Nav instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/0e6cc2c8-3d97-43cc-87a5-d271b3f63f33.mp4",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-42-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Pill Nav.",
        "promptText": "Generate a standalone HTML5 hero section for \"Pill Nav\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Pill Nav (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Pill Nav\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Pill Nav standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-42-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Pill Nav\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-42-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Pill Nav\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Pill Nav</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Pill Nav\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Pill Nav\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Pill Nav\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-43",
    "slug": "testimonial",
    "title": "Testimonial",
    "shortTitle": "Testimonial",
    "tagline": "Production-grade Testimonial standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.93,
    "reviewsCount": 87,
    "downloadsCount": 3351,
    "viewsCount": 3952,
    "copiesCount": 753,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/29d400b1-7278-456b-8771-1b25f5ffb582/1daae677-3c3f-424f-bd09-8b0822659842/1767230681166.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Testimonial instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/29d400b1-7278-456b-8771-1b25f5ffb582/1daae677-3c3f-424f-bd09-8b0822659842/1767230681166.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-43-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Testimonial.",
        "promptText": "Generate a standalone HTML5 hero section for \"Testimonial\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Testimonial (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Testimonial\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Testimonial standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-43-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Testimonial\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-43-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Testimonial\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Testimonial</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Testimonial\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Testimonial\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Testimonial\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-44",
    "slug": "magnification-dock",
    "title": "Magnification Dock",
    "shortTitle": "Magnification Dock",
    "tagline": "Production-grade Magnification Dock standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.98,
    "reviewsCount": 99,
    "downloadsCount": 2833,
    "viewsCount": 4823,
    "copiesCount": 820,
    "mediaType": "video",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/4f3f35f5-7136-4fa7-aad0-b827f9907130.mp4",
    "heroDescription": "Build and preview Magnification Dock instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/4f3f35f5-7136-4fa7-aad0-b827f9907130.mp4",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-44-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Magnification Dock.",
        "promptText": "Generate a standalone HTML5 hero section for \"Magnification Dock\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Magnification Dock (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Magnification Dock\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Magnification Dock standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-44-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Magnification Dock\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-44-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Magnification Dock\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Magnification Dock</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Magnification Dock\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Magnification Dock\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Magnification Dock\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-45",
    "slug": "circular-gallery",
    "title": "Circular Gallery",
    "shortTitle": "Circular Gallery",
    "tagline": "Production-grade Circular Gallery standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.97,
    "reviewsCount": 96,
    "downloadsCount": 3925,
    "viewsCount": 4343,
    "copiesCount": 883,
    "mediaType": "video",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/dc1ce977-8005-4a11-a488-9746d5b0580e.mp4",
    "heroDescription": "Build and preview Circular Gallery instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/dc1ce977-8005-4a11-a488-9746d5b0580e.mp4",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-45-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Circular Gallery.",
        "promptText": "Generate a standalone HTML5 hero section for \"Circular Gallery\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Circular Gallery (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Circular Gallery\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Circular Gallery standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-45-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Circular Gallery\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-45-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Circular Gallery\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Circular Gallery</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Circular Gallery\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Circular Gallery\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Circular Gallery\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-46",
    "slug": "radiant-prompt-input",
    "title": "Radiant Prompt Input",
    "shortTitle": "Radiant Prompt Input",
    "tagline": "Production-grade Radiant Prompt Input standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "section_prompts",
    "categoryLabel": "AI Section Prompt",
    "assetFormat": "section_prompts",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "AI MASTER PROMPT",
    "rating": 4.95,
    "reviewsCount": 140,
    "downloadsCount": 3761,
    "viewsCount": 2884,
    "copiesCount": 853,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/d2a7422b-1c93-41e6-a000-e313c7ad67f9/80ef4c96-3b67-465e-a056-30988465b642/1767671823794.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Radiant Prompt Input instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/hmac-uploads/screenshots/components/d2a7422b-1c93-41e6-a000-e313c7ad67f9/80ef4c96-3b67-465e-a056-30988465b642/1767671823794.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-46-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Radiant Prompt Input.",
        "promptText": "Generate a standalone HTML5 hero section for \"Radiant Prompt Input\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Radiant Prompt Input (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>AI SECTION PROMPT</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Radiant Prompt Input\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Radiant Prompt Input standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-46-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Radiant Prompt Input\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-46-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Radiant Prompt Input\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Radiant Prompt Input</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Radiant Prompt Input\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Radiant Prompt Input\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Radiant Prompt Input\nCategory: AI Section Prompt",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-47",
    "slug": "light-beam-button",
    "title": "Light Beam Button",
    "shortTitle": "Light Beam Button",
    "tagline": "Production-grade Light Beam Button standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.93,
    "reviewsCount": 108,
    "downloadsCount": 1595,
    "viewsCount": 3369,
    "copiesCount": 741,
    "mediaType": "image",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/46eeeb1c-89dd-47e4-bf19-41548ec16401.png?width=600&quality=75&resize=contain",
    "heroDescription": "Build and preview Light Beam Button instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/render/image/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/46eeeb1c-89dd-47e4-bf19-41548ec16401.png?width=600&quality=75&resize=contain",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-47-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Light Beam Button.",
        "promptText": "Generate a standalone HTML5 hero section for \"Light Beam Button\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Light Beam Button (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Light Beam Button\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Light Beam Button standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-47-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Light Beam Button\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-47-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Light Beam Button\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Light Beam Button</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Light Beam Button\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Light Beam Button\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Light Beam Button\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  },
  {
    "id": "tmpl-48",
    "slug": "masonry-gallery",
    "title": "Masonry Gallery",
    "shortTitle": "Masonry Gallery",
    "tagline": "Production-grade Masonry Gallery standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.",
    "category": "lovable_projects",
    "categoryLabel": "Interactive & Animation",
    "assetFormat": "lovable_project",
    "formatBadge": "HTML + TAILWIND CDN",
    "badge": "STANDALONE HTML",
    "rating": 4.93,
    "reviewsCount": 186,
    "downloadsCount": 3509,
    "viewsCount": 2634,
    "copiesCount": 520,
    "mediaType": "video",
    "mediaUrl": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/0b135974-e9b2-4f4a-89ec-9d60326a236f.mp4",
    "heroDescription": "Build and preview Masonry Gallery instantly. Engineered as a complete standalone HTML document using Tailwind CSS via CDN (`<script src=\"https://cdn.tailwindcss.com\"></script>`), pure CSS/vanilla JS interactions, and zero build tool bloat. Perfect for Cursor AI, Claude 3.5 Sonnet, and direct browser execution.",
    "accentBg": "bg-zinc-950",
    "accentBorder": "border-zinc-800",
    "accentText": "text-zinc-100",
    "accentColor": "#09090b",
    "coverImage": "https://vgbujcuwptvheqijyjbe.supabase.co/storage/v1/object/public/preview-artifacts/prompt-library/7d97ebb3-363d-49d3-a029-fa9a23815e3f/thumbnails/0b135974-e9b2-4f4a-89ec-9d60326a236f.mp4",
    "compatibleBuilders": [
      "HTML5 Standalone",
      "Tailwind CSS CDN",
      "Claude 3.5 Sonnet",
      "Cursor AI",
      "v0",
      "Lovable"
    ],
    "sectionsIncluded": [
      {
        "id": "sec-48-1",
        "name": "Focal Layout Architecture",
        "badge": "HTML5 HERO",
        "description": "Main hero structure and typography for Masonry Gallery.",
        "promptText": "Generate a standalone HTML5 hero section for \"Masonry Gallery\" using Tailwind CSS via CDN. Output pure HTML with class=\"...\", zero JSX or TSX.",
        "tailwindSnippet": "<!-- 1. Focal Layout for Masonry Gallery (HTML + Tailwind CDN) -->\n<section class=\"relative overflow-hidden py-16 sm:py-24 bg-zinc-950 text-zinc-100 antialiased font-sans\">\n  <div class=\"max-w-7xl mx-auto px-6 lg:px-8\">\n    <div class=\"text-center max-w-3xl mx-auto\">\n      <span class=\"inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-zinc-900 border border-zinc-800 text-zinc-300\">\n        <i data-lucide=\"sparkles\" class=\"w-3.5 h-3.5 text-zinc-400\"></i>\n        <span>INTERACTIVE & ANIMATION</span>\n      </span>\n      <h1 class=\"mt-4 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight font-display\">\n        Masonry Gallery\n      </h1>\n      <p class=\"mt-4 text-sm sm:text-base text-zinc-400 leading-relaxed\">\n        Production-grade Masonry Gallery standalone HTML template with Tailwind CSS via CDN. Instant browser preview, zero build step.\n      </p>\n    </div>\n  </div>\n</section>"
      },
      {
        "id": "sec-48-2",
        "name": "Interactive Feature Matrix",
        "badge": "HTML5 GRID",
        "description": "Responsive 3-column card grid with hover transitions and Lucide icons.",
        "promptText": "Build a pure HTML5 3-column feature grid for \"Masonry Gallery\" with Tailwind CDN utility classes and Lucide icons.",
        "tailwindSnippet": "<!-- 2. Interactive Feature Matrix (HTML + Tailwind CDN) -->\n<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-6 py-12\">\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"zap\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Zero Build Lag</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Runs instantly in any browser via Tailwind CSS CDN script.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"layers\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Pure HTML & CSS</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Zero JSX or Node.js runtime required. Copy, paste and deploy.</p>\n  </div>\n  <div class=\"p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 hover:border-zinc-700 transition-all duration-300\">\n    <div class=\"w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-200 mb-4\">\n      <i data-lucide=\"sparkles\" class=\"w-5 h-5\"></i>\n    </div>\n    <h3 class=\"text-base font-semibold text-white mb-2\">Micro-Interactions</h3>\n    <p class=\"text-xs text-zinc-400 leading-relaxed\">Smooth hover elevations and accessible keyboard focus outlines.</p>\n  </div>\n</div>"
      },
      {
        "id": "sec-48-3",
        "name": "Action & Conversion Trigger",
        "badge": "HTML5 CTA",
        "description": "Stand-alone call-to-action block with high-contrast buttons.",
        "promptText": "Create an instant HTML5 CTA conversion block for \"Masonry Gallery\" with pure Tailwind CSS CDN classes.",
        "tailwindSnippet": "<!-- 3. Conversion Trigger Action (HTML + Tailwind CDN) -->\n<div class=\"max-w-2xl mx-auto px-6 py-8 text-center\">\n  <div class=\"rounded-2xl bg-zinc-900 border border-zinc-800 p-6 sm:p-8\">\n    <h3 class=\"text-lg font-bold text-white mb-2\">Experience Masonry Gallery</h3>\n    <p class=\"text-xs text-zinc-400 mb-6\">Open the standalone HTML template in your browser or paste into your project.</p>\n    <div class=\"flex flex-col sm:flex-row items-center justify-center gap-3\">\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all\">\n        Launch Template\n      </button>\n      <button class=\"w-full sm:w-auto px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs font-semibold hover:bg-zinc-700 transition-all\">\n        Copy HTML Code\n      </button>\n    </div>\n  </div>\n</div>"
      }
    ],
    "features": [
      "100% Pure Standalone HTML5 + Tailwind CSS via CDN",
      "Zero JSX, TSX, React, or npm dependencies required",
      "Double-click to preview instantly in any browser",
      "1-Click AI Master Prompt optimized for Cursor & Claude"
    ],
    "toolsReplaced": [
      {
        "name": "UI Component Subscriptions",
        "monthlyCostUSD": 29
      },
      {
        "name": "Custom Animation Design",
        "monthlyCostUSD": 99
      },
      {
        "name": "Design Token Kits",
        "monthlyCostUSD": 49
      }
    ],
    "frameworkStack": [
      "HTML5",
      "Tailwind CSS (CDN)",
      "Vanilla JS / Alpine.js",
      "Lucide Icons (CDN)",
      "GSAP (CDN)"
    ],
    "quickCopyPrompt": "You are an expert Frontend Engineer. Build a complete, production-ready, standalone HTML5 template for \"Masonry Gallery\".\n\n### CRITICAL OUTPUT FORMAT REQUIREMENTS:\n- **Output ONLY single-file standard HTML (`<!DOCTYPE html> ... </html>`)**.\n- **STRICT PROHIBITION**: Do NOT output JSX, TSX, React components, Next.js code, or npm import statements. Everything must be pure HTML + CSS + JS in one file.\n- **Tailwind CSS via CDN**: Include `<script src=\"https://cdn.tailwindcss.com\"></script>` in `<head>`.\n- **Icons**: Load Lucide Icons via CDN `<script src=\"https://unpkg.com/lucide@latest\"></script>` and initialize with `<script>lucide.createIcons();</script>`.\n- **Fonts**: Load Google Fonts `Inter` and `JetBrains Mono`.\n- **Interactive Animations**: Use vanilla JavaScript or GSAP via CDN (`<script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js\"></script>`) embedded inside `<script>` tags at the bottom.\n\n### Component & Design Specifications:\n1. **Design System & Palette**:\n   - Monochromatic Notion/Shopify aesthetic (neutral zinc-900 / zinc-950 dark, zinc-50 / pure white light).\n   - High precision typography with crisp contrast, clean borders (`border-zinc-200` / `border-zinc-800`), and soft shadows.\n2. **Component Architecture for \"Masonry Gallery\"**:\n   - Master layout container with mobile, tablet, and desktop responsiveness.\n   - Micro-interactions on buttons, hover states, and smooth spring-like transitions.\n3. **Stand-Alone Portability**:\n   - Must open directly and render flawlessly by double clicking the `.html` file in any web browser.\n\nComponent: Masonry Gallery\nCategory: Interactive & Animation",
    "faqs": [
      {
        "question": "What is the output format of this prompt?",
        "answer": "All prompts produce standard, single-file HTML5 templates styled with Tailwind CSS via CDN (<script src=\"https://cdn.tailwindcss.com\"></script>). No JSX, React, or build setups required."
      },
      {
        "question": "How do I run and test the output HTML?",
        "answer": "Copy the AI output, save it into a file named index.html, and double-click to open it in Chrome, Safari, or Firefox. It renders instantly without compiling."
      },
      {
        "question": "Can I paste this HTML into WordPress, Webflow, or custom sites?",
        "answer": "Yes! Because the template outputs standard HTML and loads Tailwind CSS via CDN, you can paste it directly into HTML embed blocks or standalone static sites."
      }
    ]
  }
];

export const FEATURED_TEMPLATES: TemplateItem[] = TEMPLATES_DATA.slice(0, 6);

export function getTemplateBySlug(slug: string): TemplateItem | undefined {
  return TEMPLATES_DATA.find((t) => t.slug === slug);
}

export function getTemplatesByCategory(categoryId: string): TemplateItem[] {
  if (categoryId === 'all') return TEMPLATES_DATA;
  return TEMPLATES_DATA.filter((t) => t.category === categoryId);
}
