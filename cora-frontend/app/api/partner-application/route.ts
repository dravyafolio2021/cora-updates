import { NextRequest, NextResponse } from 'next/server';

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function clean(value: unknown, max = 200) {
  return String(value || '').trim().slice(0, max);
}

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    const name = clean(body?.name, 100);
    const email = clean(body?.email, 140).toLowerCase();
    const phone = clean(body?.phone, 40);
    const companyName = clean(body?.companyName, 120);
    const agencyType = clean(body?.agencyType, 100);
    const clientCount = clean(body?.clientCount, 80);
    const website = clean(body?.website, 220);
    const message = clean(body?.message, 1200);

    if (!name || !EMAIL_RE.test(email) || !phone || !companyName) {
      return NextResponse.json(
        { success: false, error: 'Please complete your name, work email, phone and agency name.' },
        { status: 400 }
      );
    }

    const apiKey = process.env.BEEHIIV_API_KEY;
    const publicationId = process.env.BEEHIIV_PUBLICATION_ID;

    if (!apiKey || !publicationId) {
      return NextResponse.json(
        { success: false, error: 'Partner applications are being configured. Please try again shortly.' },
        { status: 503 }
      );
    }

    const beehiivPayload = {
      email,
      reactivate_existing: true,
      send_welcome_email: false,
      double_opt_override: 'not_set',
      utm_source: 'partner_application',
      utm_medium: 'website',
      utm_campaign: 'cora_agency_partner_program',
      utm_term: agencyType,
      utm_content: `${companyName} | ${clientCount}`.slice(0, 200),
      referring_site: website || 'https://heycora.in/partners/agencies/',
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

    const beehiivData = await beehiivResponse.json().catch(() => ({}));

    if (!beehiivResponse.ok) {
      console.error('[Partner application] Beehiiv capture failed', beehiivResponse.status, beehiivData);
      return NextResponse.json(
        { success: false, error: 'Could not submit the application right now. Please try again.' },
        { status: 502 }
      );
    }

    // Optional notification email. The application remains captured in Beehiiv even if this is not configured.
    const hostingerToken = process.env.HOSTINGER_EMAIL_API_TOKEN;
    const mailboxId = process.env.HOSTINGER_MAILBOX_RESOURCE_ID;
    const targetEmail = process.env.NOTIFICATION_FORWARD_EMAIL || 'dravya.bansal@heycora.in';

    let notified = false;
    if (hostingerToken && mailboxId) {
      const text = [
        '[NEW CORA AGENCY PARTNER APPLICATION]',
        `Name: ${name}`,
        `Email: ${email}`,
        `Phone: ${phone}`,
        `Agency: ${companyName}`,
        `Agency type: ${agencyType}`,
        `Active clients: ${clientCount}`,
        website ? `Website: ${website}` : '',
        message ? `Note: ${message}` : '',
      ].filter(Boolean).join('\n');

      try {
        const mailResponse = await fetch(`https://api.mail.hostinger.com/api/v1/mailboxes/${mailboxId}/send`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${hostingerToken}`,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            to: [targetEmail],
            displayName: 'Cora Partner Program',
            subject: `New Cora agency partner application — ${companyName}`,
            text,
            html: `<pre style="font-family:Arial,sans-serif;white-space:pre-wrap">${text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</pre>`,
          }),
        });
        notified = mailResponse.ok || mailResponse.status === 204;
      } catch (error) {
        console.error('[Partner application] Optional email notification failed', error);
      }
    }

    return NextResponse.json({
      success: true,
      subscriberId: beehiivData?.data?.id || null,
      notified,
    });
  } catch (error) {
    console.error('[Partner application] Unexpected error', error);
    return NextResponse.json(
      { success: false, error: 'Could not submit the application right now. Please try again.' },
      { status: 500 }
    );
  }
}
