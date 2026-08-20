export function generatePlatformSchemas() {
  const organizationSchema = {
    "@context": "https://schema.org",
    "@type": "Organization",
    "@id": "https://heycora.in/#organization",
    "name": "Cora by Claraverse",
    "legalName": "Claraverse Inc.",
    "url": "https://heycora.in",
    "logo": "https://heycora.in/apple-touch-icon.png",
    "sameAs": [
      "https://twitter.com/heycora_ai",
      "https://linkedin.com/company/claraverse",
      "https://github.com/dravyafolio2021"
    ],
    "founder": {
      "@type": "Person",
      "name": "Dravya Bansal",
      "email": "dravya.bansal@heycora.in"
    }
  };

  const softwareApplicationSchema = {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "@id": "https://heycora.in/#software",
    "name": "Cora",
    "operatingSystem": "Web, macOS, Windows, iOS, Android (PWA)",
    "applicationCategory": "BusinessApplication, DeveloperApplication, WorkflowAutomation",
    "description": "Cora is the multi-model AI workspace that consolidates client pipelines, revenue workflows, e-signatures, and specialized frontier AI agents into one hyper-focused command center.",
    "url": "https://heycora.in",
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.9",
      "reviewCount": "128"
    },
    "featureList": [
      "Frontier Multi-Model AI Routing (Claude 3.5 Sonnet, GPT-4o, Gemini 2.0 Flash)",
      "Automated Lead Capture and Pipeline Kanban",
      "Legally Binding Document Vault & Digital Signatures",
      "GST-Compliant B2B Tax Calculation & Invoicing",
      "Native WhatsApp Cloud API & Gmail Automation",
      "Real Estate & Studio AI Listing Generator"
    ],
    "offers": [
      {
        "@type": "Offer",
        "name": "Free Forever Plan",
        "price": "0",
        "priceCurrency": "USD",
        "description": "1,000 AI Agent Runs/mo, Core Models (Gemini & GPT-4o mini), Document Vault, Unlimited Client Funnels."
      },
      {
        "@type": "Offer",
        "name": "India Only Plan",
        "price": "4999",
        "priceCurrency": "INR",
        "description": "2,500 AI Agent Runs/mo, All Pro Features, UPI & RuPay Direct Billing, Full GST Invoicing (B2B)."
      },
      {
        "@type": "Offer",
        "name": "Starter Plan",
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
        "name": "Scale Plan",
        "price": "39",
        "priceCurrency": "USD",
        "description": "Unlimited AI Executions, 10 Team Seats, Custom Fine-Tuned AI Personas, 99.9% SLA."
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
          "text": "Cora is an AI-powered operating system and workspace designed for ambitious founders, digital agencies, and solo builders to automate client funnels, document signing, and multi-model AI agent routing."
        }
      },
      {
        "@type": "Question",
        "name": "Is Cora free to use?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Yes, Cora provides a Free Forever plan with 1,000 AI agent runs per month, core AI models (Gemini & GPT-4o mini), and unlimited funnels with no credit card required."
        }
      },
      {
        "@type": "Question",
        "name": "How does Cora replace my existing software stack?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Cora consolidates fragmented subscriptions including Notion (workspaces), HoneyBook/Pipedrive (CRM funnels), DocuSign (e-signatures), Zapier (automations), and ChatGPT Plus/Claude subscriptions into one unified $19/mo workspace, saving founders $200+ monthly."
        }
      },
      {
        "@type": "Question",
        "name": "Which AI models does Cora support?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Cora supports frontier models from Anthropic (Claude 3.5 Sonnet), OpenAI (GPT-4o, GPT-4o mini), Google (Gemini 2.0 Flash), and Groq/Llama 3 with dynamic intelligent latency routing."
        }
      },
      {
        "@type": "Question",
        "name": "What payment methods are supported in India?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Indian customers can pay via UPI (Google Pay, PhonePe, Paytm, CRED), Indian NetBanking, RuPay cards, and GST-compliant B2B invoices."
        }
      }
    ]
  };

  return [organizationSchema, softwareApplicationSchema, faqSchema];
}
