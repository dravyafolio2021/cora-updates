export function generatePlatformSchemas() {
  const organizationSchema = {
    "@context": "https://schema.org",
    "@type": "Organization",
    "@id": "https://heycora.in/#organization",
    "name": "Cora Platforms Inc.",
    "legalName": "Cora Platforms Inc.",
    "url": "https://heycora.in",
    "logo": "https://heycora.in/apple-touch-icon.png",
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
    "name": "Cora Studio OS",
    "description": "The 20-in-1 Autonomous Operating System for Creative Studios, Production Houses & Agencies.",
    "publisher": {
      "@id": "https://heycora.in/#organization"
    },
    "potentialAction": {
      "@type": "SearchAction",
      "target": "https://heycora.in/compare?q={search_term_string}",
      "query-input": "required name=search_term_string"
    }
  };

  const softwareApplicationSchema = {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "@id": "https://heycora.in/#software",
    "name": "Cora Studio OS",
    "operatingSystem": "Web, macOS, Windows, iOS, Android (PWA)",
    "applicationCategory": "BusinessApplication, CRMApplication, FinancialApplication",
    "description": "Cora is the autonomous AI operating system that unifies client CRM pipelines, SHA-256 legal contracts, multi-model AI voice-to-scope agents, and automated 18% GST invoicing into one command center.",
    "url": "https://heycora.in",
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.9",
      "bestRating": "5.0",
      "ratingCount": "142"
    },
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
        "price": "4999",
        "priceCurrency": "INR",
        "description": "2,500 AI Agent Runs/mo, All Pro Features, UPI & RuPay Direct Billing, Full GST Invoicing (B2B)."
      },
      {
        "@type": "Offer",
        "name": "Starter Workspace Plan",
        "price": "9",
        "priceCurrency": "USD",
        "description": "5,000 AI Agent Runs/mo, Claude 3.5 Sonnet Access, Custom Workspace Domain."
      },
      {
        "@type": "Offer",
        "name": "Pro Studio Plan",
        "price": "19",
        "priceCurrency": "USD",
        "description": "10,000 AI Agent Runs/mo, All Frontier Models (Claude, GPT-4o, Gemini), 3 Team Seats, GST Invoicing."
      },
      {
        "@type": "Offer",
        "name": "Scale & Production House Plan",
        "price": "39",
        "priceCurrency": "USD",
        "description": "Unlimited AI Executions, 10 Team Seats, Custom Personas, 99.95% SLA."
      }
    ]
  };

  const faqSchema = {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "What is Cora Studio OS?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Cora Studio OS is a 20-in-1 autonomous operating system engineered for commercial photography studios, film production houses, wedding cinematographers, real estate brokerages, and creative agencies. It automates client proposals, SHA-256 contract signing, crew call sheets, and 18% GST invoicing."
        }
      },
      {
        "@type": "Question",
        "name": "How does Cora compare to HoneyBook and Studio Ninja?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Unlike HoneyBook and Studio Ninja, Cora features native 18% CGST/SGST/IGST tax calculation, WhatsApp dispatch automation, autonomous AI voice-to-scope drafting, and saves creative businesses over ₹65,000 to ₹4.5 Lakhs annually."
        }
      },
      {
        "@type": "Question",
        "name": "Are digital signatures in Cora legally binding in India and the US?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Yes, Cora's E-Signature Vault generates cryptographic SHA-256 audit stamps including signer IP address, timestamp, and device fingerprint, compliant with Section 65B of the Indian Information Technology Act 2000 and the US ESIGN Act."
        }
      },
      {
        "@type": "Question",
        "name": "Does Cora train public AI models on my proprietary media or shoot data?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "No. Cora enforces a strict Zero AI Model Training Guarantee. Your photos, 4K RAW video assets, client proposals, and rate cards are never used to train public LLMs or foundation models."
        }
      },
      {
        "@type": "Question",
        "name": "Is there a free trial or money-back guarantee?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Cora offers a Free Forever plan with 1,000 monthly AI actions and unlimited funnels, plus an unconditional 14-day 100% money-back guarantee on all paid plans with self-serve 1-click cancellation."
        }
      }
    ]
  };

  return [organizationSchema, webSiteSchema, softwareApplicationSchema, faqSchema];
}
