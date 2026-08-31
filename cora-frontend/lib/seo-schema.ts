import type { Article, ArticleCategory } from './articles-data';

export function generatePlatformSchemas() {
  const organizationSchema = {
    "@context": "https://schema.org",
    "@type": "Organization",
    "@id": "https://heycora.in/#organization",
    "name": "Cora Platforms Inc.",
    "legalName": "Cora Platforms Inc.",
    "alternateName": ["Cora", "HeyCora", "Cora OS", "Cora AI Co-Founder", "Co-founder for Operations, Revenue & Automation"],
    "url": "https://heycora.in",
    "logo": "https://heycora.in/apple-touch-icon.png",
    "image": "https://heycora.in/og-image.png",
    "description": "Cora (Co-founder for Operations, Revenue & Automation) is the autonomous AI operating system for professional service businesses, creative studios, and agencies.",
    "sameAs": [
      "https://twitter.com/dravyafolio",
      "https://linkedin.com/in/dravyafolio",
      "https://instagram.com/dravyafolio",
      "https://github.com/dravyafolio2021"
    ],
    "founder": {
      "@type": "Person",
      "name": "Dravya Bansal",
      "jobTitle": "Founder & CEO",
      "email": "dravya.bansal@heycora.in"
    },
    "contactPoint": {
      "@type": "ContactPoint",
      "contactType": "Customer Support",
      "email": "support@heycora.in",
      "url": "https://heycora.in/contact"
    }
  };

  const webSiteSchema = {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "@id": "https://heycora.in/#website",
    "url": "https://heycora.in",
    "name": "Cora",
    "alternateName": ["HeyCora", "Cora AI", "Cora AI Co-Founder", "Cora for Agencies", "Co-founder for Operations, Revenue & Automation"],
    "description": "Cora — The AI Co-Founder for Agencies. Run your proposals, SHA-256 contracts, 18% GST invoices, and client vaults on autopilot.",
    "publisher": {
      "@id": "https://heycora.in/#organization"
    },
    "potentialAction": {
      "@type": "SearchAction",
      "target": {
        "@type": "EntryPoint",
        "urlTemplate": "https://heycora.in/articles/?q={search_term_string}"
      },
      "query-input": "required name=search_term_string"
    }
  };

  const sitelinksNavigationSchema = {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "@id": "https://heycora.in/#sitelinks",
    "name": "Cora Primary Navigation",
    "itemListElement": [
      {
        "@type": "SiteNavigationElement",
        "position": 1,
        "name": "Pricing",
        "description": "Cora pricing: Free tier, Pro at ₹2,999/mo, and Enterprise plans with zero seat penalties.",
        "url": "https://heycora.in/pricing"
      },
      {
        "@type": "SiteNavigationElement",
        "position": 2,
        "name": "AI Co-Founder",
        "description": "Meet your AI Co-Founder. Autonomous operations triage, voice-to-scope audio briefs, and live RAG memory.",
        "url": "https://heycora.in/ai-agent"
      },
      {
        "@type": "SiteNavigationElement",
        "position": 3,
        "name": "Features & 20 Modules",
        "description": "Explore 20 unified modules: SHA-256 digital contracts, automated 18% GST invoicing, Kanban CRM, and crew dispatch.",
        "url": "https://heycora.in/features"
      },
      {
        "@type": "SiteNavigationElement",
        "position": 4,
        "name": "Articles & Compare",
        "description": "Educational how-to guides, Indian GST playbooks, operational benchmarks, and side-by-side competitor comparisons.",
        "url": "https://heycora.in/articles"
      },
      {
        "@type": "SiteNavigationElement",
        "position": 5,
        "name": "Documentation & APIs",
        "description": "Platform architecture guides, REST API specs, MCP gateway configuration, and developer tutorials.",
        "url": "https://heycora.in/docs"
      },
      {
        "@type": "SiteNavigationElement",
        "position": 6,
        "name": "Our Story",
        "description": "Why we built Cora: replacing 7 fragmented subscriptions with 1 intelligent AI Co-Founder.",
        "url": "https://heycora.in/about"
      }
    ]
  };

  const softwareApplicationSchema = {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "@id": "https://heycora.in/#software",
    "name": "Cora — The AI Co-Founder for Agencies",
    "alternateName": "Cora Agency OS",
    "operatingSystem": "Web, macOS, Windows, iOS, Android (PWA)",
    "applicationCategory": "BusinessApplication, CRMApplication, FinancialApplication",
    "description": "Cora is the autonomous AI co-founder and operating system for creative agencies and service businesses. Run proposals, SHA-256 contracts, 18% GST invoices, and delivery on autopilot.",
    "url": "https://heycora.in",
    "featureList": [
      "Autonomous AI Co-Founder with Voice-to-Scope Execution",
      "Frontier Multi-Model AI Routing (Claude 3.5 Sonnet, GPT-4o, Gemini 2.0 Flash)",
      "Automated Lead Capture & Kanban Deal Pipeline",
      "Legally Binding SHA-256 E-Sign Vault (Indian IT Act 2000 & US ESIGN Compliant)",
      "18% CGST/SGST/IGST Automated Tax Engine & Invoicing",
      "Studio Camera Gear Checkouts & MLS Property Listings",
      "Timeline Crew Dispatch & Automated Call Sheets",
      "4K RAW Client Media Hub & Proofing Portals",
      "Visual Landing Page Funnel Canvas with Git Synchronization"
    ],
    "offers": [
      {
        "@type": "Offer",
        "name": "Free Forever Plan",
        "price": "0",
        "priceCurrency": "USD",
        "description": "1,000 AI Agent Runs/mo, Core Models, Document Vault, Unlimited Client Funnels."
      },
      {
        "@type": "Offer",
        "name": "India Studio Pro Plan",
        "price": "2999",
        "priceCurrency": "INR",
        "description": "All 20 Modules, Multi-Model Frontier AI, UPI & RuPay Direct Billing, 18% GST Invoicing."
      }
    ]
  };

  const faqSchema = {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "What is Cora?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Cora stands for Co-founder for Operations, Revenue & Automation. It is the autonomous AI operating system for professional service businesses, creative studios, and agencies that runs proposals, SHA-256 contracts, 18% GST invoices, crew dispatch, and client delivery on autopilot."
        }
      },
      {
        "@type": "Question",
        "name": "How does Cora compare to HoneyBook and Studio Ninja?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Unlike HoneyBook and Studio Ninja, Cora features native 18% CGST/SGST/IGST tax calculation, WhatsApp dispatch automation, autonomous AI voice-to-scope drafting, and saves creative businesses over ₹57,000 to ₹4.5 Lakhs annually."
        }
      },
      {
        "@type": "Question",
        "name": "Are digital signatures in Cora legally binding in India and the US?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Yes, Cora's E-Signature Vault generates cryptographic SHA-256 audit stamps including signer IP address, timestamp, and device fingerprint, compliant with Section 65B of the Indian Information Technology Act 2000 and the US ESIGN Act."
        }
      }
    ]
  };

  return [organizationSchema, webSiteSchema, sitelinksNavigationSchema, softwareApplicationSchema, faqSchema];
}

export function generateArticleSchema(article: Article) {
  const articleUrl = `https://heycora.in/articles/${article.category}/${article.slug}/`;

  return {
    "@context": "https://schema.org",
    "@type": "Article",
    "@id": `${articleUrl}#article`,
    "headline": article.title,
    "description": article.description,
    "image": "https://heycora.in/images/cora_hero_landscape.jpg",
    "author": {
      "@type": "Organization",
      "name": article.author.name,
      "description": article.author.role
    },
    "publisher": {
      "@id": "https://heycora.in/#organization"
    },
    "articleSection": article.categoryLabel,
    "inLanguage": "en-IN",
    "datePublished": article.publishedAt,
    "dateModified": article.updatedAt,
    "mainEntityOfPage": {
      "@type": "WebPage",
      "@id": articleUrl
    }
  };
}

export function generateArticleBreadcrumbs(category: ArticleCategory, article?: Article) {
  const items = [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "https://heycora.in"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Articles & Guides",
      "item": "https://heycora.in/articles/"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": category.name,
      "item": `https://heycora.in/articles/${category.id}/`
    }
  ];

  if (article) {
    items.push({
      "@type": "ListItem",
      "position": 4,
      "name": article.shortTitle || article.title,
      "item": `https://heycora.in/articles/${category.id}/${article.slug}/`
    });
  }

  return {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": items
  };
}

export function generateArticleFaqSchema(faqs: { question: string; answer: string }[]) {
  return {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": faqs.map((f) => ({
      "@type": "Question",
      "name": f.question,
      "acceptedAnswer": {
        "@type": "Answer",
        "text": f.answer
      }
    }))
  };
}
