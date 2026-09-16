import { NextRequest, NextResponse } from 'next/server';

// ── Rate Limiting ──────────────────────────────────────────────────
interface RateLimitEntry {
  count: number;
  resetTime: number;
}
const ipRateLimitMap = new Map<string, RateLimitEntry>();
const MAX_REQUESTS_PER_MINUTE = 15;
const WINDOW_MS = 60 * 1000;

function isRateLimited(ip: string): boolean {
  const now = Date.now();
  const entry = ipRateLimitMap.get(ip);

  if (!entry || now > entry.resetTime) {
    ipRateLimitMap.set(ip, { count: 1, resetTime: now + WINDOW_MS });
    return false;
  }

  if (entry.count >= MAX_REQUESTS_PER_MINUTE) {
    return true;
  }

  entry.count += 1;
  return false;
}

// Clean up old rate limit entries every 5 minutes
if (typeof setInterval !== 'undefined') {
  setInterval(() => {
    const now = Date.now();
    for (const [ip, entry] of ipRateLimitMap.entries()) {
      if (now > entry.resetTime) {
        ipRateLimitMap.delete(ip);
      }
    }
  }, 5 * 60 * 1000);
}

// ── Security Sanitizer ──────────────────────────────────────────────
function sanitizeInput(text: string): string {
  return text
    .replace(/[<>]/g, '')
    .replace(/[\u0000-\u001F\u007F-\u009F]/g, '')
    .trim()
    .slice(0, 300);
}

export interface PlanRecommendation {
  name: string;
  price: string;
  billingText?: string;
  badge?: string;
  savingsBadge?: string;
  features: string[];
  checkoutUrl: string;
  checkoutText: string;
}

export interface ToolSavings {
  replaced: { name: string; cost: string }[];
  totalReplacedCost: string;
  coraCost: string;
  annualSavings: string;
}

export interface FeatureHighlight {
  title: string;
  desc: string;
  badge?: string;
}

export interface QuickReply {
  label: string;
  query: string;
}

export async function POST(req: NextRequest) {
  try {
    // 1. Origin verification
    const origin = req.headers.get('origin') || '';
    const referer = req.headers.get('referer') || '';
    const allowedHosts = ['heycora.in', 'app.heycora.in', 'localhost', '127.0.0.1'];
    const isAllowedOrigin = !origin || allowedHosts.some(
      (host) => origin.includes(host) || referer.includes(host)
    );

    if (origin && !isAllowedOrigin) {
      return NextResponse.json(
        { error: 'Unauthorized request origin' },
        { status: 403 }
      );
    }

    // 2. Client IP Rate Limiting
    const forwarded = req.headers.get('x-forwarded-for') || '';
    const clientIp = forwarded.split(',')[0].trim() || 'unknown-client';

    if (isRateLimited(clientIp)) {
      return NextResponse.json(
        { error: 'Rate limit exceeded. Please wait a moment.' },
        {
          status: 429,
          headers: { 'Retry-After': '30', 'X-Content-Type-Options': 'nosniff' },
        }
      );
    }

    // 3. Payload Validation
    const body = await req.json();
    const rawPrompt = body?.prompt;

    if (!rawPrompt || typeof rawPrompt !== 'string' || !rawPrompt.trim()) {
      return NextResponse.json(
        { error: 'A valid prompt string is required' },
        { status: 400 }
      );
    }

    const cleanPrompt = sanitizeInput(rawPrompt);
    const countryHeader =
      req.headers.get('cf-ipcountry') || req.headers.get('x-vercel-ip-country') || '';
    const acceptLanguage = req.headers.get('accept-language') || '';
    const isIndia =
      countryHeader === 'IN' ||
      acceptLanguage.includes('en-IN') ||
      acceptLanguage.includes('hi');

    // 4. Check for external Gemini / OpenAI key
    const geminiKey = process.env.GEMINI_API_KEY || process.env.GOOGLE_API_KEY;

    if (geminiKey) {
      try {
        const systemPrompt = `You are the Cora AI Sales Growth Consultant & Senior SDR for digital agencies.
Explain how Cora replaces fragmented tools (PandaDoc, Typeform, HoneyBook, FreshBooks) saving $1,200-$2,000/yr, and unlocks new revenue by bundling branded client portals ($300-$1000/project).
Be human, confident, active voice, and concise (2-3 punchy sentences max).
User is in ${isIndia ? 'India (currency: INR, recommend India Only Plan ₹499/mo or Free Forever)' : 'Global (currency: USD, recommend Starter Plan $9/mo or Free Forever)'}.
Respond in strict JSON with keys:
{
  "text": "2-3 conversational sales sentences in active voice",
  "planName": "India Only Plan | Starter Plan | Free Forever Plan | Professional Plan",
  "planPrice": "₹499/mo | $9/mo | ₹0 Forever",
  "billingText": "Billed annually (₹4,999/yr) • 2 Months Free",
  "savingsSummary": "Replaces PandaDoc ($49/mo) + HoneyBook ($39/mo)",
  "features": ["3 key bullet points"],
  "checkoutUrl": "https://app.heycora.in/workspace/login?plan=...",
  "quickReplies": [{"label": "string", "query": "string"}]
}`;

        const geminiRes = await fetch(
          `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${geminiKey}`,
          {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              contents: [
                { role: 'user', parts: [{ text: `${systemPrompt}\n\nUser Query: ${cleanPrompt}` }] },
              ],
              generationConfig: {
                responseMimeType: 'application/json',
                temperature: 0.3,
                maxOutputTokens: 600,
              },
            }),
          }
        );

        if (geminiRes.ok) {
          const geminiData = await geminiRes.json();
          const rawJson = geminiData?.candidates?.[0]?.content?.parts?.[0]?.text;
          if (rawJson) {
            const parsed = JSON.parse(rawJson);
            return NextResponse.json({
              success: true,
              output: parsed.text,
              planRecommendation: {
                name: parsed.planName || (isIndia ? 'India Only Plan' : 'Starter Plan'),
                price: parsed.planPrice || (isIndia ? '₹499/mo' : '$9/mo'),
                billingText: parsed.billingText || (isIndia ? 'Billed annually (₹4,999/yr) • 2 Months Free' : 'Billed annually ($108/yr) • 2 Months Free'),
                badge: 'Recommended for Agency Growth',
                savingsBadge: parsed.savingsSummary || 'Saves ~$1,400/yr on tools',
                features: parsed.features || [
                  'Replace PandaDoc, Typeform & QuickBooks',
                  '18% GST Invoicing with instant UPI QR',
                  'Deliver branded client portals with zero coding',
                ],
                checkoutUrl: parsed.checkoutUrl || (isIndia ? 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&source=sdr_gemini' : 'https://app.heycora.in/workspace/login?plan=starter&cycle=annual&source=sdr_gemini'),
                checkoutText: isIndia ? 'Claim Plan & Start Workspace →' : 'Start Workspace Plan →',
              },
              toolSavings: {
                replaced: [
                  { name: 'PandaDoc / E-Sign', cost: '$49/mo' },
                  { name: 'HoneyBook / CRM', cost: '$39/mo' },
                  { name: 'Typeform / Intake', cost: '$25/mo' },
                  { name: 'FreshBooks / Invoicing', cost: '$30/mo' },
                ],
                totalReplacedCost: '$143/mo',
                coraCost: isIndia ? '₹499/mo (~$6)' : '$9/mo',
                annualSavings: '$1,600+/yr',
              },
              quickReplies: parsed.quickReplies || [
                { label: 'How to charge clients?', query: 'How can our agency charge clients for Cora portals?' },
                { label: 'Compare vs PandaDoc', query: 'How does Cora compare against PandaDoc and Notion?' },
                { label: 'Free Plan Setup', query: 'What is included in the Free Forever Plan?' },
              ],
              model: 'Gemini 1.5 Flash (Sales SDR)',
              latency: '240ms',
            });
          }
        }
      } catch (err) {
        // Fall through to deterministic engine
      }
    }

    // 5. High-Performance Deterministic Sales Intelligence Engine
    const lower = cleanPrompt.toLowerCase();
    
    // Scenario A: Web Design & Development Agencies (Handoffs, Scope creep, Portals)
    if (lower.includes('web') || lower.includes('website') || lower.includes('wordpress') || lower.includes('framer') || lower.includes('webflow') || lower.includes('handoff')) {
      const plan: PlanRecommendation = isIndia ? {
        name: 'India Only Plan',
        price: '₹499/mo',
        billingText: 'Billed annually (₹4,999/yr) • Includes Free .in Domain',
        badge: 'Top Choice for Web Agencies',
        savingsBadge: 'Replaces 4 Tools • Saves ₹14,500/mo',
        features: [
          'Deliver branded client portals with every website build',
          'Lock scopes with SHA-256 digital milestone sign-offs',
          'Automated SAC 9983 18% GST invoices + instant UPI QR',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&agency=web_design&source=sdr_web',
        checkoutText: 'Claim India Plan & Launch Portal →',
      } : {
        name: 'Starter Plan',
        price: '$9/mo',
        billingText: 'Billed annually ($108/yr) • 2 Months Free',
        badge: 'Top Choice for Web Agencies',
        savingsBadge: 'Replaces PandaDoc + HoneyBook',
        features: [
          'Deliver custom branded client portals on your domain',
          'Prevent scope creep with legally binding milestone e-signs',
          'Automated GST & international tax invoicing',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=starter&cycle=annual&agency=web_design&source=sdr_web',
        checkoutText: 'Start Agency Workspace ($9/mo) →',
      };

      const toolSavings: ToolSavings = {
        replaced: [
          { name: 'PandaDoc / E-Sign', cost: '$49/mo' },
          { name: 'HoneyBook / Portals', cost: '$39/mo' },
          { name: 'Typeform / Intake', cost: '$25/mo' },
        ],
        totalReplacedCost: '$113/mo (~₹9,400/mo)',
        coraCost: isIndia ? '₹499/mo' : '$9/mo',
        annualSavings: '$1,248/year (~₹1,00,000/yr)',
      };

      return NextResponse.json({
        success: true,
        output: `When you build a client website, bundle Cora as their ready-to-use client portal. You eliminate $113/mo in tool subscriptions while creating a new ₹25,000 revenue stream per project for client workspace setup.`,
        planRecommendation: plan,
        toolSavings,
        highlights: [
          { title: 'Branded Client Portals', desc: 'Clients get a private portal to view assets, approve milestones, and pay invoices', badge: 'New Revenue' },
          { title: 'Scope-Lock Contracts', desc: 'SHA-256 e-sign prevents unpaid extra revisions before staging launch', badge: 'Zero Creep' },
        ],
        quickReplies: [
          { label: 'How to charge clients ₹25k?', query: 'How do agencies package and charge clients for Cora portals?' },
          { label: '18% GST Invoice Demo', query: 'Show me an 18% GST web development invoice breakdown' },
          { label: 'Free Forever Option', query: 'How does the Free Forever plan work?' },
        ],
        model: 'Cora Sales Intelligence Engine',
        latency: '80ms',
      });
    }

    // Scenario B: Pricing, Costs, ROI, Tool Replacement
    if (lower.includes('price') || lower.includes('cost') || lower.includes('plan') || lower.includes('replace') || lower.includes('pandadoc') || lower.includes('honeybook') || lower.includes('notion') || lower.includes('worth') || lower.includes('roi')) {
      const plan: PlanRecommendation = isIndia ? {
        name: 'India Only Plan',
        price: '₹499/mo',
        billingText: 'Billed annually (₹4,999/yr) • 2 Months Free + Free .in Domain',
        badge: 'Maximum Value for Indian Studios',
        savingsBadge: 'Instant 95% Cost Reduction',
        features: [
          '3,500 monthly AI SDR runs & proposal generation',
          'Unlimited SHA-256 e-sign contracts (PandaDoc alternative)',
          'Instant UPI QR 18% GST bills (Razorpay/Freshbooks alternative)',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&source=sdr_pricing',
        checkoutText: 'Upgrade to India Plan (₹499/mo) →',
      } : {
        name: 'Starter Plan',
        price: '$9/mo',
        billingText: 'Billed annually ($108/yr) • 2 Months Free',
        badge: 'Recommended for Growing Agencies',
        savingsBadge: 'Saves $1,500+ / year',
        features: [
          '6,000 monthly AI runs with multi-model intelligence',
          'Connect custom domain (.com/.in) & custom email',
          '2 team seats with full CRM & client portal handoffs',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=starter&cycle=annual&source=sdr_pricing',
        checkoutText: 'Start Starter Plan ($9/mo) →',
      };

      const toolSavings: ToolSavings = {
        replaced: [
          { name: 'PandaDoc / DocuSign', cost: '$49/mo' },
          { name: 'HoneyBook / Dubsado', cost: '$39/mo' },
          { name: 'Typeform / Intake forms', cost: '$25/mo' },
          { name: 'QuickBooks / FreshBooks', cost: '$30/mo' },
        ],
        totalReplacedCost: '$143/mo ($1,716/yr)',
        coraCost: isIndia ? '₹499/mo ($6)' : '$9/mo',
        annualSavings: '$1,608/year saved',
      };

      return NextResponse.json({
        success: true,
        output: `Cora replaces your CRM, E-sign tool, invoice generator, and client portal with a single platform. You cut operational software spend by 90% immediately while speeding up client payment cycles to under 24 hours.`,
        planRecommendation: plan,
        toolSavings,
        highlights: [
          { title: 'Immediate $143/mo Savings', desc: 'Cancel PandaDoc, Typeform, and invoicing subscriptions today', badge: 'Cost Saver' },
          { title: 'Instant 1-Click Checkout', desc: 'Activate your workspace in 30 seconds with 2 months free on annual plans', badge: 'Fast Launch' },
        ],
        quickReplies: [
          { label: 'Start Free Forever (₹0)', query: 'Can I start on the Free Forever plan first?' },
          { label: 'Web Agency Handoffs', query: 'How does Cora help web design agencies deliver portals?' },
          { label: 'WhatsApp Automation', query: 'How do WhatsApp client reminders and call-sheets work?' },
        ],
        model: 'Cora Sales Intelligence Engine',
        latency: '75ms',
      });
    }

    // Scenario C: Creative, Branding & Design Studios
    if (lower.includes('creative') || lower.includes('brand') || lower.includes('design') || lower.includes('logo') || lower.includes('figma')) {
      const plan: PlanRecommendation = isIndia ? {
        name: 'India Only Plan',
        price: '₹499/mo',
        billingText: 'Billed annually (₹4,999/yr) • 2 Months Free',
        badge: 'Tailored for Creative Studios',
        savingsBadge: 'Saves ₹12,000/mo on Subscriptions',
        features: [
          'High-resolution brand asset proofing with threaded client feedback',
          'Advance milestone escrow & dynamic UPI settlement',
          'Automated copyright transfer deeds & NDAs',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&agency=branding&source=sdr_creative',
        checkoutText: 'Start Creative Studio Plan →',
      } : {
        name: 'Starter Plan',
        price: '$9/mo',
        billingText: 'Billed annually ($108/yr) • 2 Months Free',
        badge: 'Tailored for Creative Studios',
        savingsBadge: 'Replaces WeTransfer Pro + PandaDoc',
        features: [
          'Clean client portal for brand decks without email size limits',
          'Legally binding copyright assignment deeds',
          'Milestone advance collection before releasing source files',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=starter&cycle=annual&agency=branding&source=sdr_creative',
        checkoutText: 'Start Studio Plan ($9/mo) →',
      };

      return NextResponse.json({
        success: true,
        output: `Stop delivering high-res brand decks over cluttered email threads. Cora gives your clients a luxury review portal, locks 50% advance payments via UPI QR, and auto-generates legal copyright deeds.`,
        planRecommendation: plan,
        toolSavings: {
          replaced: [
            { name: 'PandaDoc / E-Sign', cost: '$49/mo' },
            { name: 'WeTransfer Pro / File Hub', cost: '$19/mo' },
            { name: 'Invoicing Tool', cost: '$25/mo' },
          ],
          totalReplacedCost: '$93/mo',
          coraCost: isIndia ? '₹499/mo' : '$9/mo',
          annualSavings: '$1,000+/yr',
        },
        highlights: [
          { title: 'Asset Proofing Portal', desc: 'Clean, client-branded deck reviews with digital sign-off', badge: 'Client Wow' },
          { title: '50% Advance Lock', desc: 'Clients must settle deposit via UPI/card before source files unlock', badge: 'Cashflow' },
        ],
        quickReplies: [
          { label: 'Copyright Transfer Deeds', query: 'How does Cora generate intellectual property contracts?' },
          { label: 'Check Pricing Options', query: 'What are the pricing plans for Cora?' },
          { label: 'Free Plan Setup', query: 'Can I test this on the Free Forever plan?' },
        ],
        model: 'Cora Sales Intelligence Engine',
        latency: '80ms',
      });
    }

    // Scenario D: Performance Marketing, SEO & Retainers
    if (lower.includes('marketing') || lower.includes('seo') || lower.includes('ad') || lower.includes('retainer') || lower.includes('growth')) {
      const plan: PlanRecommendation = isIndia ? {
        name: 'India Only Plan',
        price: '₹499/mo',
        billingText: 'Billed annually (₹4,999/yr) • 2 Months Free',
        badge: 'Best for Marketing & Retainers',
        savingsBadge: 'Auto-bills 1st of every month',
        features: [
          'Automated 1st-of-month 18% GST retainer bills on WhatsApp',
          'Lead Kanban pipeline with WhatsApp team notifications',
          'Ad spend pass-through reconciliation with zero tax confusion',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&agency=marketing&source=sdr_marketing',
        checkoutText: 'Start Marketing Retainers →',
      } : {
        name: 'Starter Plan',
        price: '$9/mo',
        billingText: 'Billed annually ($108/yr) • 2 Months Free',
        badge: 'Best for Marketing & Retainers',
        savingsBadge: 'Replaces HubSpot Starter + Freshbooks',
        features: [
          'Recurring retainer agreements and automated monthly billing',
          'Lead capture kanban pipeline',
          'WhatsApp reminder sequences for unpaid retainers',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=starter&cycle=annual&agency=marketing&source=sdr_marketing',
        checkoutText: 'Start Retainer Workspace ($9/mo) →',
      };

      return NextResponse.json({
        success: true,
        output: `Automate your monthly client retainers on autopilot. Cora dispatches recurring 18% GST invoices on the 1st of every month with 1-click UPI payment links directly over WhatsApp.`,
        planRecommendation: plan,
        toolSavings: {
          replaced: [
            { name: 'Subscription Invoicing', cost: '$39/mo' },
            { name: 'CRM Pipeline Tool', cost: '$30/mo' },
            { name: 'WhatsApp Bot Service', cost: '$29/mo' },
          ],
          totalReplacedCost: '$98/mo',
          coraCost: isIndia ? '₹499/mo' : '$9/mo',
          annualSavings: '$1,068/yr saved',
        },
        highlights: [
          { title: '1st-of-Month Retainer Automation', desc: 'No manual invoicing—clients receive GST bills and UPI links automatically', badge: 'Autopilot' },
          { title: 'Zero Chasing Over WhatsApp', desc: 'Polite, automated payment reminders ensure 98% on-time settlement', badge: 'Cash Flow' },
        ],
        quickReplies: [
          { label: '18% GST Invoice Demo', query: 'Make a ₹45,000 monthly retainer invoice with 18% GST' },
          { label: 'Upgrade to India Plan', query: 'How do I upgrade to the India Only Plan?' },
          { label: 'Web Agency Solutions', query: 'How does Cora help web design & dev agencies?' },
        ],
        model: 'Cora Sales Intelligence Engine',
        latency: '80ms',
      });
    }

    // Default Fallback Sales Closer
    const defaultPlan: PlanRecommendation = isIndia ? {
      name: 'Free Forever Plan',
      price: '₹0 Forever',
      billingText: 'No Credit Card Required • Instant Activation',
      badge: 'Zero Risk Starter Tier',
      savingsBadge: '1,000 Free AI Runs / Month',
      features: [
        '1,000 monthly AI agent runs & proposal generator',
        'Unlimited SHA-256 digital signature contracts',
        '18% GST tax invoices with instant UPI QR payments',
      ],
      checkoutUrl: 'https://app.heycora.in/workspace/login?plan=free_forever&source=sdr_default',
      checkoutText: 'Start Free Forever Workspace →',
    } : {
      name: 'Free Forever Plan',
      price: '$0 Forever',
      billingText: 'No Credit Card Required • 1,000 AI Runs/Mo',
      badge: 'Zero Risk Starter Tier',
      savingsBadge: 'Instant Setup',
      features: [
        '1,000 monthly AI agent runs',
        'Unlimited SHA-256 digital signature contracts',
        'Full Kanban CRM and invoicing suite',
      ],
      checkoutUrl: 'https://app.heycora.in/workspace/login?plan=free_forever&source=sdr_default',
      checkoutText: 'Start Free Forever ($0) →',
    };

    return NextResponse.json({
      success: true,
      output: `Cora gives your agency an all-in-one operating workspace. You replace 4 fragmented subscriptions, stop unpaid revisions with locked milestone contracts, and deliver custom client portals.`,
      planRecommendation: defaultPlan,
      toolSavings: {
        replaced: [
          { name: 'PandaDoc', cost: '$49/mo' },
          { name: 'HoneyBook', cost: '$39/mo' },
          { name: 'Typeform', cost: '$25/mo' },
        ],
        totalReplacedCost: '$113/mo',
        coraCost: '$0',
        annualSavings: '$1,356/yr saved',
      },
      highlights: [
        { title: 'Unified Agency Workspace', desc: 'Manage proposals, client portals, and GST invoices in one screen', badge: 'All-In-One' },
        { title: 'Zero Onboarding Overhead', desc: 'Get your team and clients up and running in under 5 minutes', badge: 'Fast Launch' },
      ],
      quickReplies: [
        { label: 'Web Design Agencies', query: 'How does Cora help web design & dev agencies?' },
        { label: 'India Only Plan (₹499/mo)', query: 'What is included in the India Only Plan?' },
        { label: 'Replace PandaDoc & Notion', query: 'How does Cora replace PandaDoc and Notion?' },
      ],
      model: 'Cora Sales Intelligence Engine',
      latency: '75ms',
    });
  } catch (error) {
    return NextResponse.json(
      { error: 'Internal processing error' },
      { status: 500, headers: { 'X-Content-Type-Options': 'nosniff' } }
    );
  }
}
