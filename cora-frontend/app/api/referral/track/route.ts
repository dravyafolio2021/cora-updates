import { NextRequest, NextResponse } from 'next/server';

export async function POST(req: NextRequest) {
  try {
    const body = await req.json().catch(() => ({}));
    const referralCode = (body.referralCode || body.ref || body.code || '').trim();

    if (!referralCode) {
      return NextResponse.json(
        { success: false, error: 'Missing referral code' },
        { status: 400 }
      );
    }

    // Determine target WordPress affiliate endpoint
    const growthApiUrl = process.env.CORA_GROWTH_API_URL || process.env.NEXT_PUBLIC_CORA_GROWTH_API_URL || '';
    const baseWp = growthApiUrl 
      ? growthApiUrl.replace('/cora-growth/v1', '/cora-affiliate/v1') 
      : 'http://cora.local/wp-json/cora-affiliate/v1';

    const targetUrl = `${baseWp}/track-click`;

    const trackPayload = {
      referral_code: referralCode,
      source_url: body.sourceUrl || '',
      landing_page: body.landingPage || '',
      referrer: body.referrer || '',
      user_agent: body.userAgent || req.headers.get('user-agent') || '',
      ip: req.headers.get('x-forwarded-for') || req.headers.get('x-real-ip') || '',
      timestamp: body.timestamp || new Date().toISOString()
    };

    // Forward to WordPress with short timeout so it doesn't block
    try {
      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), 2000);

      await fetch(targetUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'User-Agent': 'Cora-Frontend-Proxy/1.0'
        },
        body: JSON.stringify(trackPayload),
        signal: controller.signal
      }).catch(() => {
        // WordPress endpoint may be offline in dev/staging, ignore
      });

      clearTimeout(timeoutId);
    } catch (wpError) {
      // Graceful offline fallback
    }

    return NextResponse.json({
      success: true,
      code: referralCode,
      discountApplied: 10,
      tracked: true
    });
  } catch (error: any) {
    return NextResponse.json(
      { success: false, error: error?.message || 'Internal server error' },
      { status: 500 }
    );
  }
}
