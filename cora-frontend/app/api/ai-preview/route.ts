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
        const systemPrompt = `You are Cora AI, a friendly, concise, and helpful assistant for agency founders and operators.
Answer the user's question directly in 2-3 natural, human conversational sentences in active voice.
Explain how Cora solves their problem (e.g. delivering branded client portals, automated 18% GST invoices, scope-locking digital signatures, or replacing separate tools).
Do NOT dump large bullet lists or sales pitches. Speak naturally like a knowledgeable peer.
User is in ${isIndia ? 'India (INR / GST context)' : 'Global'}.
Respond in strict JSON with keys:
{
  "text": "2-3 natural, concise sentences answering their question directly",
  "quickReplies": [{"label": "short label", "query": "follow up question"}]
}`;

        const geminiRes = await fetch(
          `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${geminiKey}`,
          {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              contents: [
                {
                  parts: [
                    { text: systemPrompt },
                    { text: `User Question: "${cleanPrompt}"` },
                  ],
                },
              ],
              generationConfig: {
                temperature: 0.3,
                maxOutputTokens: 300,
                responseMimeType: 'application/json',
              },
            }),
          }
        );

        if (geminiRes.ok) {
          const gemData = await geminiRes.json();
          const rawText = gemData.candidates?.[0]?.content?.parts?.[0]?.text;
          if (rawText) {
            const parsed = JSON.parse(rawText);
            return NextResponse.json({
              output: parsed.text || '',
              quickReplies: parsed.quickReplies || [],
            });
          }
        }
      } catch (err) {
        // Fall back gracefully
      }
    }

    // 5. High-Performance Deterministic Conversational Engine
    const lower = cleanPrompt.toLowerCase();

    // Web Design & Dev
    if (lower.includes('web') || lower.includes('website') || lower.includes('wordpress') || lower.includes('framer') || lower.includes('webflow') || lower.includes('handoff')) {
      return NextResponse.json({
        success: true,
        output: `When you build a client website, you can bundle Cora as their ready-to-use client portal. Clients get a private space to review staging links, approve milestones with digital signatures, and pay 18% GST invoices via instant UPI QR codes.`,
        quickReplies: [
          { label: 'How to charge clients for portals?', query: 'How do agencies package and charge clients for Cora portals?' },
          { label: '18% GST Invoicing', query: 'How does 18% GST and SAC 9983 invoicing work?' },
          { label: 'Free Forever Plan', query: 'What is included in the Free Forever Plan?' },
        ],
      });
    }

    // Pricing / Cost / Plans
    if (lower.includes('price') || lower.includes('cost') || lower.includes('plan') || lower.includes('replace') || lower.includes('pandadoc') || lower.includes('honeybook') || lower.includes('notion') || lower.includes('free')) {
      return NextResponse.json({
        success: true,
        output: isIndia
          ? `You can start on the Free Forever plan at ₹0 with 1,000 monthly AI runs, digital contracts, and GST invoicing. For growing studios, our India Only Plan is ₹499/mo (billed annually with 2 months free and custom domain connection support).`
          : `You can start on our Free Forever plan with no credit card required. For growing teams, the Starter Plan is $9/mo billed annually ($108/yr), which includes full client portals, digital e-signatures, and CRM workflows.`,
        quickReplies: [
          { label: 'Start Free Forever (₹0)', query: 'How do I start on the Free Forever plan?' },
          { label: 'Web Agency Portals', query: 'How does Cora help web design agencies deliver portals?' },
          { label: 'Contract Signatures', query: 'How do digital signature contracts work in Cora?' },
        ],
      });
    }

    // Creative, Branding & Design
    if (lower.includes('creative') || lower.includes('brand') || lower.includes('design') || lower.includes('logo') || lower.includes('figma')) {
      return NextResponse.json({
        success: true,
        output: `For creative and design studios, Cora offers a private client review hub. You can share brand decks, collect threaded feedback, lock 50% milestone deposits via UPI QR, and auto-generate legal copyright transfer deeds.`,
        quickReplies: [
          { label: 'Copyright Transfer Deeds', query: 'How does Cora generate intellectual property contracts?' },
          { label: 'Pricing Plans', query: 'What are the pricing plans for Cora?' },
          { label: 'Milestone Payments', query: 'How does milestone-based client billing work?' },
        ],
      });
    }

    // Marketing, SEO, Retainers
    if (lower.includes('marketing') || lower.includes('seo') || lower.includes('ad') || lower.includes('retainer') || lower.includes('growth')) {
      return NextResponse.json({
        success: true,
        output: `Cora puts monthly client retainers on autopilot. On the 1st of every month, it dispatches SAC 9983 compliant 18% GST invoices and WhatsApp payment links directly to your clients, with polite follow-ups so you get paid on time.`,
        quickReplies: [
          { label: 'WhatsApp Automation', query: 'How do WhatsApp client reminders and retainers work?' },
          { label: '18% GST Breakdown', query: 'How does GST tax invoicing work in Cora?' },
          { label: 'Free Plan Setup', query: 'How do I get started for free?' },
        ],
      });
    }

    // Software & App Development
    if (lower.includes('software') || lower.includes('app') || lower.includes('saas') || lower.includes('sprint') || lower.includes('dev') || lower.includes('code')) {
      return NextResponse.json({
        success: true,
        output: `Manage sprint milestones and client sign-offs in one place. Clients review deliverables and sign UAT approvals with cryptographic SHA-256 audit trails before code deployment, with automated SAC 9983 tax billing.`,
        quickReplies: [
          { label: 'Sprint Milestone Demo', query: 'How does milestone-based software billing work?' },
          { label: 'Web Design Portals', query: 'How does Cora help web design agencies?' },
          { label: 'Free Forever Plan', query: 'What is included in the Free Forever Plan?' },
        ],
      });
    }

    // Default Conversational Answer
    return NextResponse.json({
      success: true,
      output: `Cora gives your agency an all-in-one operating workspace. You can manage proposals, send digitally signed contracts, deliver custom client portals, and generate 18% GST invoices in one screen.`,
      quickReplies: [
        { label: 'Web Design Agencies', query: 'How does Cora help web design & dev agencies?' },
        { label: 'Pricing Options', query: 'What are the pricing plans for Cora?' },
        { label: 'Client Portals', query: 'How do branded client portals work?' },
      ],
    });
  } catch (error) {
    return NextResponse.json(
      { error: 'Internal processing error' },
      { status: 500, headers: { 'X-Content-Type-Options': 'nosniff' } }
    );
  }
}
