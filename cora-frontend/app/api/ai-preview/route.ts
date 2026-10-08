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

export interface DedicatedOffer {
  code: string;
  badge: string;
  title: string;
  description: string;
  actionText: string;
  actionUrl: string;
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
    const checkUrl = origin || referer;
    if (checkUrl) {
      try {
        const parsed = new URL(checkUrl);
        const host = parsed.hostname.toLowerCase();
        const allowedHosts = ['heycora.in', 'app.heycora.in', 'cora.local', 'localhost', '127.0.0.1'];
        const isAllowedOrigin = allowedHosts.includes(host) || host.endsWith('.heycora.in') || host.endsWith('.cora.local');
        if (!isAllowedOrigin) {
          return NextResponse.json(
            { error: 'Unauthorized request origin' },
            { status: 403 }
          );
        }
      } catch {
        return NextResponse.json(
          { error: 'Invalid request origin' },
          { status: 400 }
        );
      }
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
    const geminiKey = process.env.GEMINI_API_KEY || process.env.GOOGLE_API_KEY || process.env.GEMINI;

    if (geminiKey) {
      try {
        const systemPrompt = `You are the Cora AI SDR (Sales Development Representative) for Cora (heycora.in).
Your job is to handle prospect objections directly with small, simple, punchy active-voice answers (2-3 sentences max).
Never be vague or passive. Directly demonstrate ROI, time saved, and why switching to Cora is a no-brainer.
You have 3 official SDR Deal Desk offers you can trigger when relevant:
1. FREE Website & Client Migration (Coupon: MIGRATEFREE) - Our engineers migrate websites, client records, and templates for free upon upgrade.
2. 10% Extra Annual Discount (Coupon: CORA10) - 10% instant discount + 2 months free on annual plans.
3. 500 Bonus AI Runs (Coupon: BONUS500) - 500 bonus AI runs in first month on annual plan.

User is in ${isIndia ? 'India (INR / GST SAC 9983 context)' : 'Global'}.
Respond in strict JSON with keys:
{
  "text": "Direct, punchy 2-3 sentence active voice answer handling their objection or question",
  "quickReplies": [{"label": "Short label", "query": "Follow up question"}],
  "offer": {
    "code": "MIGRATEFREE or CORA10 or BONUS500 or omitted if not relevant",
    "badge": "e.g. Deal Desk Offer or Free Migration",
    "title": "Short offer title",
    "description": "Short 1-sentence value proposition",
    "actionText": "e.g. Claim Free Migration or Apply 10% Off",
    "actionUrl": "https://heycora.in/pricing/?coupon=CODE"
  }
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
                    { text: `Prospect Query / Objection: "${cleanPrompt}"` },
                  ],
                },
              ],
              generationConfig: {
                temperature: 0.2,
                maxOutputTokens: 350,
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
              offer: parsed.offer?.code ? parsed.offer : undefined,
            });
          }
        }
      } catch (err) {
        // Fall back gracefully
      }
    }

    // 5. High-Converting Deterministic SDR Objection Handling Engine
    const lower = cleanPrompt.toLowerCase();

    // Objection 1: Migration / Moving from WordPress / Webflow / Existing Tools
    if (lower.includes('migrat') || lower.includes('switch') || lower.includes('move') || lower.includes('import') || lower.includes('existing site')) {
      return NextResponse.json({
        success: true,
        output: `We handle your entire website, client list, and past contract migration 100% free with zero downtime. Our engineering team ports your templates and workflows within 48 hours so your team stays focused on client delivery.`,
        quickReplies: [
          { label: 'How does free migration work?', query: 'What is included in the free website migration service?' },
          { label: 'Explore Pricing Plans', query: 'What are the annual discount pricing plans?' },
          { label: 'Start Free Forever', query: 'Can I start for free without a credit card?' },
        ],
        offer: {
          code: 'MIGRATEFREE',
          badge: 'White-Glove Service',
          title: '100% Free Website & Client Migration',
          description: 'Upgrade to any plan today and our engineering team will migrate your sites and templates for free.',
          actionText: 'Claim Free Migration →',
          actionUrl: 'https://heycora.in/pricing/?coupon=MIGRATEFREE&plan=pro',
        },
      });
    }

    // Objection 2: Already using HoneyBook / Notion / Dubsado / HubSpot / WordPress
    if (lower.includes('honeybook') || lower.includes('notion') || lower.includes('dubsado') || lower.includes('hubspot') || lower.includes('wordpress') || lower.includes('plugins') || lower.includes('already use')) {
      return NextResponse.json({
        success: true,
        output: `HoneyBook and Notion don't host your website, build interactive forms, or automate 18% GST SAC 9983 invoices. Cora consolidates your website builder, CRM, SHA-256 e-signatures, and payment links into one tab—cutting your software bill by 60%.`,
        quickReplies: [
          { label: 'Claim Free Migration', query: 'How does free migration from our existing tools work?' },
          { label: 'Compare Costs & ROI', query: 'How much do agencies save switching to Cora?' },
          { label: '10% Annual Discount', query: 'Is there an annual plan discount available?' },
        ],
        offer: {
          code: 'CORA10',
          badge: 'Switching Bonus',
          title: 'Extra 10% Off Annual Plan + Free Migration',
          description: 'Replace $110+/mo in fragmented tools with code CORA10.',
          actionText: 'Apply 10% Discount →',
          actionUrl: 'https://heycora.in/pricing/?coupon=CORA10&plan=pro',
        },
      });
    }

    // Objection 3: Pricing, Cost, Discounts & Annual Deals
    if (lower.includes('discount') || lower.includes('coupon') || lower.includes('deal') || lower.includes('offer') || lower.includes('price') || lower.includes('cost') || lower.includes('expensive') || lower.includes('annual')) {
      return NextResponse.json({
        success: true,
        output: `You can start completely free with no credit card required. When you are ready to scale, use coupon CORA10 to lock in an additional 10% discount on top of 2 months free on our annual plan.`,
        quickReplies: [
          { label: 'Apply 10% Discount', query: 'How do I redeem the 10% annual plan coupon?' },
          { label: 'Free Website Migration', query: 'Can I get free migration if I upgrade now?' },
          { label: 'Bonus AI Runs', query: 'How do I claim 500 bonus AI runs?' },
        ],
        offer: {
          code: 'CORA10',
          badge: 'Limited SDR Offer',
          title: '10% Instant Discount + 2 Months Free',
          description: 'Lock in full client portals, digital contracts, and GST invoicing for only ₹449/mo ($8/mo).',
          actionText: 'Claim 10% Off Now →',
          actionUrl: 'https://heycora.in/pricing/?coupon=CORA10&plan=pro',
        },
      });
    }

    // Objection 4: AI Runs, Quota & Limits
    if (lower.includes('ai limit') || lower.includes('ai run') || lower.includes('quota') || lower.includes('limit') || lower.includes('token') || lower.includes('throttl')) {
      return NextResponse.json({
        success: true,
        output: `Cora gives you 15,000 monthly AI runs on Professional and 25,000 on Scale with zero hourly rate limits. Upgrade today with code BONUS500 to receive 500 additional AI runs in your first month.`,
        quickReplies: [
          { label: 'Claim 500 Bonus AI Runs', query: 'How do I activate the 500 bonus AI runs?' },
          { label: 'See Pricing Tiers', query: 'What are the quota limits across plans?' },
          { label: 'Free Website Migration', query: 'Is website migration included with Professional?' },
        ],
        offer: {
          code: 'BONUS500',
          badge: 'AI Power Boost',
          title: '+500 Bonus AI Runs for Month 1',
          description: 'Get extra AI capacity for website generation, proposal drafts, and SEO copy.',
          actionText: 'Unlock Bonus AI Runs →',
          actionUrl: 'https://heycora.in/pricing/?coupon=BONUS500&plan=pro',
        },
      });
    }

    // Default SDR Answer
    return NextResponse.json({
      success: true,
      output: `Cora unifies your website, client leads, digital contracts, and 18% GST billing in one place so you never lose revenue to workflow sprawl. You can start completely free or upgrade today with free white-glove migration included.`,
      quickReplies: [
        { label: 'Free Website Migration', query: 'How does free website migration work if we switch?' },
        { label: '10% Annual Discount', query: 'What discounts are available on annual plans?' },
        { label: 'Compare vs HoneyBook', query: 'How does Cora compare to HoneyBook and Notion?' },
      ],
      offer: {
        code: 'CORA10',
        badge: 'Exclusive Deal Desk',
        title: '10% Off Annual Plan + Free Migration',
        description: 'Use code CORA10 at checkout to unlock discounted annual pricing and free migration.',
        actionText: 'Explore Offers & Pricing →',
        actionUrl: 'https://heycora.in/pricing/?coupon=CORA10',
      },
    });
  } catch (error) {
    return NextResponse.json(
      { error: 'Internal processing error' },
      { status: 500, headers: { 'X-Content-Type-Options': 'nosniff' } }
    );
  }
}
