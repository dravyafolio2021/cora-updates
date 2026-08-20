import { NextRequest, NextResponse } from 'next/server';

// ── In-Memory Rate Limiting Bucket ──────────────────────────────────
interface RateLimitEntry {
  count: number;
  resetTime: number;
}
const ipRateLimitMap = new Map<string, RateLimitEntry>();
const MAX_REQUESTS_PER_MINUTE = 6;
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
    .replace(/[<>]/g, '') // remove HTML tags
    .replace(/[\u0000-\u001F\u007F-\u009F]/g, '') // remove control chars
    .trim()
    .slice(0, 250); // limit to 250 chars max
}

export async function POST(req: NextRequest) {
  try {
    // 1. Origin & Referer Verification
    const origin = req.headers.get('origin') || '';
    const referer = req.headers.get('referer') || '';
    const allowedHosts = ['heycora.in', 'app.heycora.in', 'localhost:3000', '127.0.0.1'];
    const isAllowedOrigin = allowedHosts.some(
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
        { error: 'Rate limit exceeded. Please wait a minute before sending another query.' },
        {
          status: 429,
          headers: {
            'Retry-After': '60',
            'X-Content-Type-Options': 'nosniff',
          },
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
    if (!cleanPrompt) {
      return NextResponse.json(
        { error: 'Prompt content is invalid' },
        { status: 400 }
      );
    }

    // 4. Geolocation heuristic
    const countryHeader =
      req.headers.get('cf-ipcountry') || req.headers.get('x-vercel-ip-country') || '';
    const acceptLanguage = req.headers.get('accept-language') || '';
    const isIndia =
      countryHeader === 'IN' ||
      acceptLanguage.includes('en-IN') ||
      acceptLanguage.includes('hi');

    // 5. Prompt Injection / Abuse Protection
    const lower = cleanPrompt.toLowerCase();
    const hasJailbreak = [
      'ignore all',
      'ignore previous',
      'system prompt',
      'api key',
      'dan mode',
      'jailbreak',
      'reveal your',
      'exfiltrate',
    ].some((pattern) => lower.includes(pattern));

    if (hasJailbreak) {
      return NextResponse.json(
        {
          success: true,
          resultType: 'security_fallback',
          output: `Cora is a dedicated workspace for client funnels, 18% GST billing, and multi-model AI routing. You can explore all capabilities safely on the Free Forever Plan.`,
          model: 'Claude 3.5 Sonnet',
          latency: '280ms',
          isIndia,
          recommendedPlan: isIndia
            ? { name: 'India Only Plan', price: '₹4,999/yr', monthly: '₹499/mo', currency: 'INR' }
            : { name: 'Starter Plan', price: '$9/mo', monthly: '$9/mo', currency: 'USD' },
        },
        {
          headers: {
            'X-Content-Type-Options': 'nosniff',
            'Cache-Control': 'no-store, private',
          },
        }
      );
    }

    // 6. Safe Structured Output
    let resultType = 'general';
    let output = '';

    if (lower.includes('gst') || lower.includes('invoice') || lower.includes('tax') || lower.includes('bill')) {
      resultType = 'gst_invoice';
      output = `Automated 18% GST calculation with split CGST (9%) + SGST (9%), client GSTIN verification, and instant UPI QR settlement.`;
    } else if (lower.includes('listing') || lower.includes('real estate') || lower.includes('property') || lower.includes('villa')) {
      resultType = 'listing';
      output = `GEO-targeted portal listings (MagicBricks/99acres), Instagram Reels video hooks, and automated WhatsApp brochures.`;
    } else if (lower.includes('whatsapp') || lower.includes('call-sheet') || lower.includes('shoot') || lower.includes('booking')) {
      resultType = 'call_sheet';
      output = `Automated WhatsApp call-sheets and shoot reminder alerts delivered 24h & 2h before call time with 1-tap client confirmations.`;
    } else {
      resultType = 'proposal';
      output = `Bespoke commercial proposals generated with Claude 3.5 in 5 seconds with attached SHA-256 digital signature links.`;
    }

    return NextResponse.json(
      {
        success: true,
        resultType,
        output,
        model: 'Claude 3.5 Sonnet',
        latency: '310ms',
        isIndia,
        recommendedPlan: isIndia
          ? { name: 'India Only Plan', price: '₹4,999/yr', monthly: '₹499/mo', currency: 'INR' }
          : { name: 'Starter Plan', price: '$9/mo', monthly: '$9/mo', currency: 'USD' },
      },
      {
        headers: {
          'X-Content-Type-Options': 'nosniff',
          'Cache-Control': 'no-store, private',
        },
      }
    );
  } catch (error) {
    return NextResponse.json(
      { error: 'Internal processing error' },
      { status: 500, headers: { 'X-Content-Type-Options': 'nosniff' } }
    );
  }
}
