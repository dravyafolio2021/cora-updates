import { NextRequest, NextResponse } from 'next/server';

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function clean(value: unknown, max = 200): string {
  return String(value || '').trim().slice(0, max);
}

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();

    // Honeypot check
    const honeypot = clean(body?.companyWebsite, 100);
    if (honeypot) {
      return NextResponse.json({ success: true, applicationCaptured: true });
    }

    const name = clean(body?.name, 100);
    const email = clean(body?.email, 150).toLowerCase();
    const phone = clean(body?.phone, 30);
    const companyName = clean(body?.companyName, 150);
    const agencyType = clean(body?.agencyType, 100) || 'Performance Marketing Agency';
    const clientCount = clean(body?.clientCount, 80) || '1–5 active clients';
    const website = clean(body?.website, 300);
    const message = clean(body?.message, 2000);
    const consentNewsletter = Boolean(body?.consentNewsletter);

    const source = clean(body?.utm_source || body?.source || 'partner_page', 100);
    const utmMedium = clean(body?.utm_medium, 100);
    const utmCampaign = clean(body?.utm_campaign, 100);
    const utmTerm = clean(body?.utm_term, 100);
    const utmContent = clean(body?.utm_content, 200);
    const referrer = clean(body?.referrer, 300);
    const landingPage = clean(body?.landing_page, 200) || '/partners/agencies/';

    // 1. Validation
    if (!name || !EMAIL_RE.test(email) || !phone || !companyName) {
      return NextResponse.json(
        { success: false, error: 'Please check the highlighted fields and complete all required inputs.' },
        { status: 400 }
      );
    }

    const phoneDigits = phone.replace(/[^0-9]/g, '');
    if (phoneDigits.length < 7) {
      return NextResponse.json(
        { success: false, error: 'Please provide a valid phone or WhatsApp number.' },
        { status: 400 }
      );
    }

    // 2. Calculate Partner Scoring (1 to 7)
    let partnerScore = 1;
    if (clientCount.includes('6–10')) partnerScore = 2;
    else if (clientCount.includes('11–25')) partnerScore = 3;
    else if (clientCount.includes('26–50')) partnerScore = 4;
    else if (clientCount.includes('50+')) partnerScore = 5;

    if (agencyType.includes('Performance') || agencyType.includes('Shopify') || agencyType.includes('Automation')) {
      partnerScore += 2;
    } else if (agencyType.includes('SEO') || agencyType.includes('Branding')) {
      partnerScore += 1;
    }

    const applicationId = `app_${Date.now()}_${Math.random().toString(36).slice(2, 8)}`;

    // 3. Non-Blocking Beehiiv Sync
    const apiKey = process.env.BEEHIIV_API_KEY;
    const publicationId = process.env.BEEHIIV_PUBLICATION_ID || 'pub_838eba99-e4d5-413b-b744-50d94de64c60';

    let beehiivSynced = false;
    let beehiivSubId: string | null = null;

    if (apiKey && publicationId) {
      try {
        const beehiivPayload = {
          email,
          reactivate_existing: true,
          send_welcome_email: false,
          double_opt_override: 'not_set',
          utm_source: source || 'agency_partner_application',
          utm_medium: utmMedium || 'website',
          utm_campaign: utmCampaign || 'agency_partner_program',
          utm_term: agencyType,
          utm_content: `${companyName} | ${clientCount}`.slice(0, 200),
          referring_site: website || referrer || 'https://heycora.in/partners/agencies/',
          custom_fields: [
            { name: 'First Name', value: name },
            { name: 'Phone', value: phone },
            { name: 'Agency Name', value: companyName },
            { name: 'Agency Type', value: agencyType },
            { name: 'Active Clients', value: clientCount },
            { name: 'Agency Website', value: website },
            { name: 'Partner Note', value: message },
          ],
        };

        const beehiivResponse = await fetch(
          `https://api.beehiiv.com/v2/publications/${encodeURIComponent(publicationId)}/subscriptions`,
          {
            method: 'POST',
            headers: {
              Authorization: `Bearer ${apiKey}`,
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(beehiivPayload),
            cache: 'no-store',
          }
        );

        if (beehiivResponse.ok) {
          const bData = await beehiivResponse.json().catch(() => ({}));
          beehiivSubId = bData?.data?.id || null;
          beehiivSynced = true;
        } else {
          console.error('[Partner Application] Beehiiv API non-blocking error:', beehiivResponse.status);
        }
      } catch (beehiivErr) {
        console.error('[Partner Application] Beehiiv exception (non-blocking):', beehiivErr);
      }
    }

    // 4. Return Success Response
    return NextResponse.json({
      success: true,
      applicationCaptured: true,
      applicationId,
      beehiivSynced,
      partnerScore,
    });
  } catch (error) {
    console.error('[Partner Application] Unexpected error:', error);
    return NextResponse.json(
      { success: false, error: "We couldn't submit this right now. Please try again in a moment." },
      { status: 500 }
    );
  }
}
