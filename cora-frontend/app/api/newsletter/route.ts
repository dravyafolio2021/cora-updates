import { NextRequest, NextResponse } from 'next/server';

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    const email = String(body?.email || '').trim().toLowerCase();
    const source = String(body?.source || 'website').trim().slice(0, 80);
    const path = String(body?.path || '').trim().slice(0, 200);
    const referrer = String(body?.referrer || '').trim().slice(0, 300);
    const honeypot = String(body?.companyWebsite || '').trim();

    if (honeypot) {
      return NextResponse.json({ success: true });
    }

    if (!EMAIL_RE.test(email)) {
      return NextResponse.json({ success: false, error: 'Enter a valid email address.' }, { status: 400 });
    }

    const apiKey = process.env.BEEHIIV_API_KEY;
    const publicationId = process.env.BEEHIIV_PUBLICATION_ID;
    const automationId = process.env.BEEHIIV_AUTOMATION_ID;
    const newsletterListId = process.env.BEEHIIV_NEWSLETTER_LIST_ID;

    if (!apiKey || !publicationId) {
      console.error('[Newsletter] Missing BEEHIIV_API_KEY or BEEHIIV_PUBLICATION_ID');
      return NextResponse.json(
        { success: false, error: 'Newsletter signup is being configured. Please try again shortly.' },
        { status: 503 }
      );
    }

    const customUtmSource = body?.utm_source ? String(body.utm_source).trim().slice(0, 80) : null;
    const customUtmMedium = body?.utm_medium ? String(body.utm_medium).trim().slice(0, 80) : null;
    const customUtmCampaign = body?.utm_campaign ? String(body.utm_campaign).trim().slice(0, 80) : null;
    const customUtmContent = body?.utm_content ? String(body.utm_content).trim().slice(0, 120) : null;
    const incomingTags = Array.isArray(body?.tags) ? body.tags.map((t: unknown) => String(t).trim().slice(0, 50)).filter(Boolean) : [];

    const payload: Record<string, unknown> = {
      email,
      reactivate_existing: true,
      send_welcome_email: !automationId,
      double_opt_override: 'not_set',
      utm_source: customUtmSource || source || 'website',
      utm_medium: customUtmMedium || 'website',
      utm_campaign: customUtmCampaign || 'cora_operator_brief',
      utm_content: customUtmContent || path || undefined,
      referring_site: referrer || 'https://heycora.in',
    };

    if (incomingTags.length > 0) {
      payload.tags = incomingTags;
    }

    if (automationId) payload.automation_ids = [automationId];
    if (newsletterListId) payload.newsletter_list_ids = [newsletterListId];

    const beehiivResponse = await fetch(
      `https://api.beehiiv.com/v2/publications/${encodeURIComponent(publicationId)}/subscriptions`,
      {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${apiKey}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
        cache: 'no-store',
      }
    );

    const data = await beehiivResponse.json().catch(() => ({}));

    if (!beehiivResponse.ok) {
      console.error('[Newsletter] Beehiiv subscription failed', beehiivResponse.status, data);
      const message = beehiivResponse.status === 429
        ? 'Too many requests. Please try again in a moment.'
        : 'Could not subscribe right now. Please try again.';
      return NextResponse.json({ success: false, error: message }, { status: 502 });
    }

    const subscriberId = data?.data?.id;

    // Attach tags via Beehiiv V2 tags endpoint if subscriber was created/retrieved
    if (subscriberId && incomingTags.length > 0) {
      try {
        await fetch(
          `https://api.beehiiv.com/v2/publications/${encodeURIComponent(publicationId)}/subscriptions/${encodeURIComponent(subscriberId)}/tags`,
          {
            method: 'POST',
            headers: {
              Authorization: `Bearer ${apiKey}`,
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ tags: incomingTags }),
            cache: 'no-store',
          }
        );
      } catch (tagErr) {
        console.warn('[Newsletter] Non-blocking tag attachment error', tagErr);
      }
    }

    return NextResponse.json({
      success: true,
      subscriberId: subscriberId || null,
      status: data?.data?.status || null,
    });
  } catch (error) {
    console.error('[Newsletter] Unexpected error', error);
    return NextResponse.json({ success: false, error: 'Could not subscribe right now. Please try again.' }, { status: 500 });
  }
}
