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
        const systemPrompt = `You are Cora's top AI Sales Development Representative (SDR) for Cora (heycora.in).
CRITICAL RULES:
1. NEVER dump generic paragraphs, sales fluff, or bullet lists.
2. Give a direct, crisp, 1-2 sentence active-voice answer (max 35-40 words total) that directly answers the user's EXACT query or objection.
3. Explicitly acknowledge the specific agency type, competitor, or feature they mentioned (e.g. Webflow, Notion, GST invoicing, client portals, retainers).
4. Explain clearly how Cora solves that specific friction in 1 punchy sentence.
5. Offer codes to attach when relevant:
   - Migration inquiries: MIGRATEFREE (100% Free Website & Client Migration)
   - Pricing/discounts/HoneyBook/Notion comparisons: CORA10 (Extra 10% Off Annual Plan)
   - AI capacity/token inquiries: BONUS500 (+500 Bonus AI Runs Month 1)

User Context: ${isIndia ? 'India (INR / 18% GST SAC 9983)' : 'Global'}.
Strict JSON format:
{
  "text": "Direct, conversational 1-2 sentence active voice answer tailored to their exact query",
  "quickReplies": [{"label": "Short label", "query": "Follow up question"}],
  "offer": {
    "code": "MIGRATEFREE or CORA10 or BONUS500 or omit if not applicable",
    "badge": "Short badge",
    "title": "Short offer title",
    "description": "1 short sentence benefit",
    "actionText": "Short CTA",
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
                temperature: 0.15,
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
              offer: parsed.offer?.code ? parsed.offer : undefined,
            });
          }
        }
      } catch (err) {
        // Fall back gracefully
      }
    }

    // 5. Intelligent Query-Aligned Deterministic SDR Engine
    const lower = cleanPrompt.toLowerCase();

    // Specific Vertical: Web Design & Dev
    if (lower.includes('web design') || lower.includes('web dev') || lower.includes('development agency')) {
      return NextResponse.json({
        success: true,
        output: `Cora gives your web agency staging preview portals, automated client design feedback, and 1-click 18% GST milestone invoicing with Indian SAC 9983 compliance.`,
        quickReplies: [
          { label: 'Free Website Migration', query: 'How does free website migration work if we switch?' },
          { label: 'See GST Invoicing', query: 'How does 18% GST invoicing work in Cora?' },
          { label: 'Claim 10% Discount', query: 'Is there an annual discount code available?' },
        ],
        offer: {
          code: 'MIGRATEFREE',
          badge: 'White-Glove Migration',
          title: '100% Free Website & Client Migration',
          description: 'Our engineering team will migrate your client sites and templates with zero downtime.',
          actionText: 'Claim Free Migration →',
          actionUrl: 'https://heycora.in/pricing/?coupon=MIGRATEFREE&plan=pro',
        },
      });
    }

    // Specific Vertical: Creative & Branding
    if (lower.includes('creative') || lower.includes('branding') || lower.includes('brand asset')) {
      return NextResponse.json({
        success: true,
        output: `For branding agencies, Cora lets you deliver brand guidelines in protected client vaults, lock scopes with digital signatures, and collect milestone payments seamlessly.`,
        quickReplies: [
          { label: 'Lock Scope E-Signs', query: 'How do digital contract signatures work in Cora?' },
          { label: 'Client Vault Portals', query: 'Can clients download high-res files from the portal?' },
          { label: 'Claim 10% Discount', query: 'How do I apply the 10% annual coupon?' },
        ],
        offer: {
          code: 'CORA10',
          badge: 'Studio Deal',
          title: '10% Off Annual Plan + Free Migration',
          description: 'Lock in branded client portals and contract e-signatures with code CORA10.',
          actionText: 'Claim 10% Off Now →',
          actionUrl: 'https://heycora.in/pricing/?coupon=CORA10&plan=pro',
        },
      });
    }

    // Specific Vertical: Performance, SEO & Retainers
    if (lower.includes('marketing') || lower.includes('seo') || lower.includes('retainer')) {
      return NextResponse.json({
        success: true,
        output: `Cora automates your monthly client retainers, auto-generates 18% GST invoices on the 1st of every month, and gives clients a live portal to track deliverables.`,
        quickReplies: [
          { label: 'Automate Retainers', query: 'How do recurring monthly client retainers work?' },
          { label: 'GST Compliance', query: 'How does SAC 9983 GST invoicing work?' },
          { label: 'Claim 10% Discount', query: 'What discounts are available on annual plans?' },
        ],
        offer: {
          code: 'CORA10',
          badge: 'Retainer Boost',
          title: '10% Off Annual Plan + 2 Months Free',
          description: 'Automate recurring agency billing and client portals for ₹449/mo ($8/mo).',
          actionText: 'Apply 10% Discount →',
          actionUrl: 'https://heycora.in/pricing/?coupon=CORA10&plan=pro',
        },
      });
    }

    // Specific Vertical: Software / App Development
    if (lower.includes('software') || lower.includes('app dev') || lower.includes('sprint')) {
      return NextResponse.json({
        success: true,
        output: `Cora streamlines sprint handoffs with gated client staging portals, tracked scope change orders, and SHA-256 e-signed master service agreements.`,
        quickReplies: [
          { label: 'Sprint Contracts', query: 'How does Cora handle digital contracts and scope locks?' },
          { label: 'Client Portals', query: 'How do client review portals work?' },
          { label: '500 Bonus AI Runs', query: 'How do I claim 500 bonus AI runs?' },
        ],
        offer: {
          code: 'BONUS500',
          badge: 'Developer Boost',
          title: '+500 Bonus AI Runs for Month 1',
          description: 'Get extra AI capacity for scope drafting and proposal generation.',
          actionText: 'Unlock Bonus AI Runs →',
          actionUrl: 'https://heycora.in/pricing/?coupon=BONUS500&plan=pro',
        },
      });
    }

    // Specific Vertical: Media & Production
    if (lower.includes('production') || lower.includes('media') || lower.includes('shoot') || lower.includes('photo')) {
      return NextResponse.json({
        success: true,
        output: `Cora keeps your shoot contracts, call sheets, and raw asset delivery vaults organized under one link with built-in milestone payment releases.`,
        quickReplies: [
          { label: 'Shoot Contracts', query: 'Can clients e-sign shoot agreements on mobile?' },
          { label: 'Asset Vaults', query: 'How do file deliveries and download gates work?' },
          { label: 'Claim 10% Discount', query: 'How do I get the annual discount code?' },
        ],
        offer: {
          code: 'CORA10',
          badge: 'Production Deal',
          title: '10% Off Annual Plan + Free Migration',
          description: 'Replace fragmented tools with all-in-one studio management.',
          actionText: 'Claim 10% Off Now →',
          actionUrl: 'https://heycora.in/pricing/?coupon=CORA10&plan=pro',
        },
      });
    }

    // Objection 1: Free Website Migration
    if (lower.includes('migrat') || lower.includes('switch') || lower.includes('move') || lower.includes('import') || lower.includes('existing site')) {
      return NextResponse.json({
        success: true,
        output: `Yes—our engineering team ports your entire website, client database, and contract templates 100% free within 48 hours with zero downtime.`,
        quickReplies: [
          { label: 'Claim Free Migration', query: 'How do I get the free website migration with my upgrade?' },
          { label: '10% Annual Discount', query: 'Is there an annual discount code available?' },
          { label: 'Start Free Forever', query: 'Can I start for free without a credit card?' },
        ],
        offer: {
          code: 'MIGRATEFREE',
          badge: 'White-Glove Migration',
          title: '100% Free Website & Client Migration',
          description: 'Upgrade today and our team will port your existing sites and workflows for free.',
          actionText: 'Claim Free Migration →',
          actionUrl: 'https://heycora.in/pricing/?coupon=MIGRATEFREE&plan=pro',
        },
      });
    }

    // Objection 2: HoneyBook, Notion, WordPress, Dubsado, HubSpot
    if (lower.includes('honeybook') || lower.includes('notion') || lower.includes('dubsado') || lower.includes('hubspot') || lower.includes('wordpress') || lower.includes('plugins') || lower.includes('already use')) {
      return NextResponse.json({
        success: true,
        output: `HoneyBook doesn't host websites or automate Indian GST invoices, and Notion lacks contract e-signatures. Cora unifies your website, client CRM, contracts, and payments into one tab—cutting software costs by 60%.`,
        quickReplies: [
          { label: 'Free Migration', query: 'Can you migrate our clients from HoneyBook for free?' },
          { label: 'Claim 10% Discount', query: 'How do I apply the 10% annual coupon?' },
          { label: 'Compare Pricing', query: 'What are the pricing options across plans?' },
        ],
        offer: {
          code: 'CORA10',
          badge: 'Switching Bonus',
          title: 'Extra 10% Off Annual Plan + Free Migration',
          description: 'Replace $110+/mo in fragmented subscriptions with code CORA10.',
          actionText: 'Apply 10% Discount →',
          actionUrl: 'https://heycora.in/pricing/?coupon=CORA10&plan=pro',
        },
      });
    }

    // Objection 3: Pricing, Cost, Discounts & Annual Deals
    if (lower.includes('discount') || lower.includes('coupon') || lower.includes('deal') || lower.includes('offer') || lower.includes('price') || lower.includes('cost') || lower.includes('expensive') || lower.includes('annual')) {
      return NextResponse.json({
        success: true,
        output: `You can start free with zero credit card. When scaling, coupon CORA10 gives you an extra 10% instant discount on top of 2 months free on annual plans.`,
        quickReplies: [
          { label: 'Apply 10% Discount', query: 'How do I redeem the 10% annual discount code?' },
          { label: 'Free Website Migration', query: 'Does the annual plan include free website migration?' },
          { label: '500 Bonus AI Runs', query: 'How do I claim 500 bonus AI runs?' },
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

    // Objection 4: AI Runs & Quotas
    if (lower.includes('ai limit') || lower.includes('ai run') || lower.includes('quota') || lower.includes('limit') || lower.includes('token') || lower.includes('throttl')) {
      return NextResponse.json({
        success: true,
        output: `You get 15,000 monthly AI runs on Professional and 25,000 on Scale with zero hourly throttles, plus 500 bonus runs in Month 1 with code BONUS500.`,
        quickReplies: [
          { label: 'Claim 500 Bonus AI Runs', query: 'How do I activate the 500 bonus AI runs?' },
          { label: 'Free Website Migration', query: 'Is website migration included with Professional?' },
          { label: 'Pricing Plans', query: 'What are the pricing options across plans?' },
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

    // Objection 5: GST / Invoicing / UPI
    if (lower.includes('gst') || lower.includes('invoice') || lower.includes('upi') || lower.includes('sac') || lower.includes('tax')) {
      return NextResponse.json({
        success: true,
        output: `Cora calculates 18% CGST/SGST or IGST, applies SAC code 9983 for agency services, and generates downloadable B2B GST tax invoices with instant UPI QR codes.`,
        quickReplies: [
          { label: 'See GST Invoicing', query: 'How does 18% GST invoicing work in Cora?' },
          { label: 'Free Website Migration', query: 'How does free website migration work if we switch?' },
          { label: 'Claim 10% Discount', query: 'What discounts are available on annual plans?' },
        ],
        offer: {
          code: 'CORA10',
          badge: 'Invoicing Ready',
          title: '10% Off Annual Plan + Free Migration',
          description: 'Automate compliant 18% GST invoices and instant UPI client billing.',
          actionText: 'Claim 10% Off Now →',
          actionUrl: 'https://heycora.in/pricing/?coupon=CORA10&plan=pro',
        },
      });
    }

    // Default SDR Answer (Crisp, direct, active voice)
    return NextResponse.json({
      success: true,
      output: `Cora consolidates your website builder, client proposals, SHA-256 contracts, and 18% GST billing into one workspace—saving agencies 15+ hours each month.`,
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
