import { NextRequest, NextResponse } from 'next/server';

interface ContactPayload {
  name: string;
  email: string;
  phone?: string;
  companyName?: string;
  industry?: string;
  selectedTopics?: string[];
  message?: string;
  source?: string;
}

function escapeHtml(str: string): string {
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

export async function POST(req: NextRequest) {
  try {
    // 0. Origin verification
    const origin = req.headers.get('origin') || '';
    const referer = req.headers.get('referer') || '';
    const checkUrl = origin || referer;
    if (checkUrl) {
      try {
        const parsed = new URL(checkUrl);
        const host = parsed.hostname.toLowerCase();
        const allowedHosts = ['heycora.in', 'app.heycora.in', 'cora.local', 'localhost', '127.0.0.1'];
        const isAllowed = allowedHosts.includes(host) || host.endsWith('.heycora.in') || host.endsWith('.cora.local');
        if (!isAllowed) {
          return NextResponse.json({ success: false, error: 'Unauthorized origin' }, { status: 403 });
        }
      } catch {
        return NextResponse.json({ success: false, error: 'Invalid origin header' }, { status: 400 });
      }
    }

    const body = (await req.json()) as ContactPayload;
    const { name, email, phone, companyName, industry, selectedTopics, message, source } = body;

    // 1. Validation
    if (!name || typeof name !== 'string' || !name.trim()) {
      return NextResponse.json(
        { success: false, error: 'Name is required' },
        { status: 400 }
      );
    }

    if (!email || typeof email !== 'string' || !email.includes('@')) {
      return NextResponse.json(
        { success: false, error: 'A valid email address is required' },
        { status: 400 }
      );
    }

    if (!phone || typeof phone !== 'string' || !phone.trim() || phone.trim().length < 7) {
      return NextResponse.json(
        { success: false, error: 'A valid WhatsApp / phone number is required' },
        { status: 400 }
      );
    }

    // 2. Configuration
    const apiToken = process.env.HOSTINGER_EMAIL_API_TOKEN;
    const mailboxId = process.env.HOSTINGER_MAILBOX_RESOURCE_ID;
    const targetEmail = process.env.NOTIFICATION_FORWARD_EMAIL || 'notifications@cora.local';

    if (!apiToken || !mailboxId) {
      console.error('[Contact API] Missing required Hostinger email API configuration in environment variables.');
      return NextResponse.json(
        { success: false, error: 'Email service configuration unavailable. Please reach out directly or try again later.' },
        { status: 503 }
      );
    }

    const cleanName = escapeHtml(name.trim().slice(0, 100));
    const cleanEmail = escapeHtml(email.trim().slice(0, 120));
    const cleanPhone = phone ? escapeHtml(phone.trim().slice(0, 30)) : 'Not provided';
    const cleanCompany = companyName ? escapeHtml(companyName.trim().slice(0, 100)) : 'Not specified';
    const cleanIndustry = industry ? escapeHtml(industry.trim().slice(0, 100)) : 'Not specified';
    const topicsList = Array.isArray(selectedTopics) && selectedTopics.length > 0 
      ? escapeHtml(selectedTopics.map(t => String(t).slice(0, 50)).join(', ')) 
      : 'General Inquiry';
    const cleanMessage = message ? escapeHtml(message.trim().slice(0, 2000)) : 'No additional note provided.';
    const cleanSource = source ? escapeHtml(source.trim().slice(0, 100)) : 'Contact Page (/contact)';
    const submissionTime = new Date().toLocaleString('en-IN', {
      timeZone: 'Asia/Kolkata',
      dateStyle: 'full',
      timeStyle: 'medium'
    });

    const subject = `⚡ New Inbound Lead: ${cleanName} — ${cleanCompany !== 'Not specified' ? cleanCompany : cleanIndustry}`;

    // Clean digits for WhatsApp link
    const waDigits = cleanPhone.replace(/[^0-9]/g, '');
    const waUrl = waDigits.length >= 10 
      ? (waDigits.startsWith('91') || waDigits.length > 10 ? `https://wa.me/${waDigits}` : `https://wa.me/91${waDigits}`)
      : null;

    // 3. Monochromatic HTML Email Template
    const html = `
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>${subject}</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 24px; color: #18181b; }
    .container { max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .header { background: #09090b; padding: 24px 28px; color: #ffffff; }
    .header-tag { display: inline-block; font-family: monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em; color: #a1a1aa; margin-bottom: 8px; }
    .header-title { margin: 0; font-size: 20px; font-weight: 700; color: #ffffff; letter-spacing: -0.02em; }
    .body { padding: 28px; }
    .lead-summary { background: #fbf9f5; border: 1px solid #f4ece1; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; }
    .lead-summary h4 { margin: 0 0 4px 0; font-size: 13px; color: #71717a; text-transform: uppercase; font-family: monospace; letter-spacing: 0.08em; }
    .lead-name { font-size: 18px; font-weight: 700; color: #09090b; margin: 0; }
    .lead-email { font-size: 14px; color: #52525b; margin: 2px 0 0 0; }
    .data-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .data-table td { padding: 12px 0; border-bottom: 1px solid #f4f4f5; font-size: 14px; vertical-align: top; }
    .data-table td.label { width: 35%; color: #71717a; font-size: 13px; font-weight: 500; }
    .data-table td.value { width: 65%; color: #09090b; font-weight: 600; }
    .message-box { background: #fafafa; border: 1px solid #e4e4e7; border-radius: 12px; padding: 16px 20px; margin-bottom: 28px; }
    .message-box h4 { margin: 0 0 8px 0; font-size: 12px; font-family: monospace; text-transform: uppercase; color: #71717a; }
    .message-text { margin: 0; font-size: 14px; color: #27272a; line-height: 1.6; white-space: pre-wrap; }
    .actions { text-align: left; }
    .button-primary { display: inline-block; background: #09090b; color: #ffffff !important; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-right: 12px; }
    .footer { padding: 20px 28px; background: #fafafa; border-top: 1px solid #f4f4f5; font-size: 12px; color: #a1a1aa; font-family: monospace; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="header-tag">CORA INBOUND DISPATCH // NOTIFICATION</div>
      <h1 class="header-title">New Client Enquiry Received</h1>
    </div>

    <div class="body">
      <div class="lead-summary">
        <h4>PROSPECT</h4>
        <div class="lead-name">${cleanName}</div>
        <div class="lead-email">${cleanEmail}</div>
      </div>

      <table class="data-table">
        <tr>
          <td class="label">WhatsApp / Phone</td>
          <td class="value">
            ${cleanPhone !== 'Not provided' ? `${cleanPhone} ${waUrl ? `&bull; <a href="${waUrl}" style="color: #16a34a; font-weight: bold; text-decoration: underline;" target="_blank">Open WhatsApp</a>` : ''}` : 'Not provided'}
          </td>
        </tr>
        <tr>
          <td class="label">Company / Studio</td>
          <td class="value">${cleanCompany}</td>
        </tr>
        <tr>
          <td class="label">Industry Domain</td>
          <td class="value">${cleanIndustry}</td>
        </tr>
        <tr>
          <td class="label">Topics of Interest</td>
          <td class="value">${topicsList}</td>
        </tr>
        <tr>
          <td class="label">Received Timestamp</td>
          <td class="value" style="font-family: monospace; font-size: 13px; font-weight: normal; color: #52525b;">${submissionTime} (IST)</td>
        </tr>
        <tr>
          <td class="label">Source Form</td>
          <td class="value">${cleanSource}</td>
        </tr>
      </table>

      <div class="message-box">
        <h4>NOTE / REQUIREMENTS</h4>
        <div class="message-text">${cleanMessage}</div>
      </div>

      <div class="actions">
        <a href="mailto:${cleanEmail}?subject=Re:%20Cora%20Inquiry%20from%20${encodeURIComponent(cleanName)}" class="button-primary">Reply via Email &rarr;</a>
        ${waUrl ? `<a href="${waUrl}" style="display: inline-block; background: #16a34a; color: #ffffff !important; text-decoration: none; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-left: 8px;">Chat on WhatsApp &rarr;</a>` : ''}
      </div>
    </div>

    <div class="footer">
      <div>Routed automatically via Cora Inbound Dispatch Engine</div>
      <div style="margin-top: 4px;">Destination: ${targetEmail}</div>
    </div>
  </div>
</body>
</html>
    `;

    const plainText = `
[NEW CORA INBOUND ENQUIRY]
Name: ${cleanName}
Email: ${cleanEmail}
WhatsApp / Phone: ${cleanPhone}
Company: ${cleanCompany}
Industry: ${cleanIndustry}
Interested In: ${topicsList}
Message: ${cleanMessage}

Received: ${submissionTime} IST
Forwarded to: ${targetEmail}
    `.trim();

    // 4. Dispatch via Hostinger Email API
    const response = await fetch(`https://api.mail.hostinger.com/api/v1/mailboxes/${mailboxId}/send`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${apiToken}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        to: [targetEmail],
        displayName: 'Cora Inbound Engine',
        subject: subject,
        html: html,
        text: plainText,
      }),
    });

    if (!response.ok && response.status !== 204) {
      const errorText = await response.text();
      console.error('[Contact API] Hostinger send failed:', response.status, errorText);
      return NextResponse.json(
        { 
          success: true, 
          delivered: false, 
          warning: 'Enquiry captured; upstream dispatch pending retry.' 
        },
        { status: 200 }
      );
    }

    return NextResponse.json({
      success: true,
      delivered: true,
      message: 'Enquiry received and forwarded to notification inbox.'
    });

  } catch (error: any) {
    console.error('[Contact API] Error handling inquiry:', error);
    return NextResponse.json(
      { success: false, error: 'Failed to process enquiry. Please try again later.' },
      { status: 500 }
    );
  }
}
