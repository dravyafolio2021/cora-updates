'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { ArrowRight, CheckCircle2, ShieldCheck, Sparkles } from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

export function AgencyPartnerApplication() {
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [error, setError] = useState('');
  
  const [form, setForm] = useState({
    name: '',
    email: '',
    phone: '',
    companyName: '',
    agencyType: 'Performance Marketing Agency',
    clientCount: '1–5 active clients',
    website: '',
    message: '',
    consentNewsletter: true,
    companyWebsite: '', // Honeypot
  });

  const [attribution, setAttribution] = useState<{
    utm_source?: string;
    utm_medium?: string;
    utm_campaign?: string;
    utm_term?: string;
    utm_content?: string;
    referrer?: string;
    landing_page?: string;
  }>({});

  // Capture UTMs, Referrer and Trigger View Event
  useEffect(() => {
    trackEvent('agency_partner_form_view', { source: 'partner_page' });

    try {
      const urlParams = new URLSearchParams(window.location.search);
      const storedLast = localStorage.getItem('cora_last_touch_utm');
      const lastTouch = storedLast ? JSON.parse(storedLast) : {};

      const utms = {
        utm_source: urlParams.get('utm_source') || lastTouch.utm_source || 'direct',
        utm_medium: urlParams.get('utm_medium') || lastTouch.utm_medium || '',
        utm_campaign: urlParams.get('utm_campaign') || lastTouch.utm_campaign || '',
        utm_term: urlParams.get('utm_term') || lastTouch.utm_term || '',
        utm_content: urlParams.get('utm_content') || lastTouch.utm_content || '',
        referrer: typeof document !== 'undefined' ? document.referrer : '',
        landing_page: typeof window !== 'undefined' ? window.location.pathname : '/partners/agencies/',
      };
      setAttribution(utms);
    } catch {
      // Non-blocking
    }
  }, []);

  const update = (key: keyof typeof form, value: string | boolean) => {
    setForm((current) => ({ ...current, [key]: value }));
  };

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    setError('');

    // Frontend validation checks
    if (!form.name.trim() || !form.email.trim() || !form.phone.trim() || !form.companyName.trim()) {
      setError('Please check the highlighted fields and complete all required inputs.');
      return;
    }

    if (!form.email.includes('@') || !form.email.includes('.')) {
      setError('Please provide a valid agency work email address.');
      return;
    }

    if (form.phone.replace(/[^0-9]/g, '').length < 7) {
      setError('Please provide a valid phone or WhatsApp number with country code.');
      return;
    }

    trackEvent('agency_partner_form_submit', {
      agency_type: form.agencyType,
      client_count: form.clientCount,
      source: 'partner_page',
    });

    setIsSubmitting(true);

    try {
      const payload = {
        name: form.name.trim().slice(0, 100),
        email: form.email.trim().toLowerCase().slice(0, 150),
        phone: form.phone.trim().slice(0, 30),
        companyName: form.companyName.trim().slice(0, 150),
        agencyType: form.agencyType,
        clientCount: form.clientCount,
        website: form.website.trim().slice(0, 300),
        message: form.message.trim().slice(0, 2000),
        consentNewsletter: form.consentNewsletter,
        companyWebsite: form.companyWebsite, // honeypot
        ...attribution,
      };

      const response = await fetch('/api/partner-application/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });

      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data?.success) {
        throw new Error(data?.error || "We couldn't submit this right now. Please try again in a moment.");
      }

      trackEvent('agency_partner_application_submitted', {
        agency_type: form.agencyType,
        client_count: form.clientCount,
        source: attribution.utm_source || 'partner_page',
        beehiiv_synced: !!data?.beehiivSynced,
        application_id: data?.applicationId || null,
      });

      setSubmitted(true);
    } catch (err: any) {
      setError(err?.message || "We couldn't submit this right now. Please try again in a moment.");
    } finally {
      setIsSubmitting(false);
    }
  };

  if (submitted) {
    return (
      <div className="rounded-3xl border border-zinc-700 bg-zinc-900/95 p-7 sm:p-9 text-white shadow-2xl animate-in fade-in duration-300">
        <div className="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-5">
          <CheckCircle2 className="h-6 w-6" />
        </div>

        <div className="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[11px] font-mono text-emerald-400 font-semibold mb-3">
          <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
          <span>APPLICATION CAPTURED</span>
        </div>

        <h3 className="font-display text-2xl sm:text-3xl font-bold tracking-tight text-white">
          Application received.
        </h3>

        <p className="mt-3 text-sm leading-relaxed text-zinc-300 max-w-lg">
          We’ll review your agency details and reach out if there’s a mutual fit. Meanwhile, you can start exploring Cora for your own agency operations right away.
        </p>

        <div className="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-6 border-t border-zinc-800">
          <a
            href="https://app.heycora.in/workspace/login?plan=free&source=partner_application_success"
            className="inline-flex items-center justify-center gap-2 bg-white hover:bg-zinc-100 text-zinc-950 px-5 py-3 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-md"
          >
            <span>Claim Free Workspace</span>
            <ArrowRight className="w-4 h-4 text-zinc-600" />
          </a>

          <Link
            href="/demo"
            className="inline-flex items-center justify-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 px-5 py-3 rounded-xl text-xs sm:text-sm font-semibold transition-all"
          >
            <span>Explore Interactive Demo</span>
          </Link>
        </div>

        <div className="mt-5 flex items-center gap-2 text-[11px] font-mono text-zinc-500">
          <ShieldCheck className="w-3.5 h-3.5 text-zinc-400 shrink-0" />
          <span>Direct founder review &bull; Response within 24–48 hours</span>
        </div>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="rounded-3xl border border-zinc-700 bg-zinc-900 p-6 sm:p-8">
      {/* Honeypot Field */}
      <input
        type="text"
        tabIndex={-1}
        autoComplete="off"
        value={form.companyWebsite}
        onChange={(e) => update('companyWebsite', e.target.value)}
        className="hidden"
        style={{ display: 'none' }}
      />

      <div className="grid gap-4 sm:grid-cols-2">
        <Field label="Your name *">
          <input
            required
            maxLength={100}
            value={form.name}
            onChange={(e) => update('name', e.target.value)}
            placeholder="e.g. Rohan Verma"
            className="field"
          />
        </Field>

        <Field label="Work email *">
          <input
            required
            type="email"
            maxLength={150}
            value={form.email}
            onChange={(e) => update('email', e.target.value)}
            placeholder="you@agency.com"
            className="field"
          />
        </Field>

        <Field label="WhatsApp / phone *">
          <input
            required
            maxLength={30}
            value={form.phone}
            onChange={(e) => update('phone', e.target.value)}
            placeholder="+91 98765 43210"
            className="field"
          />
        </Field>

        <Field label="Agency name *">
          <input
            required
            maxLength={150}
            value={form.companyName}
            onChange={(e) => update('companyName', e.target.value)}
            placeholder="e.g. Apex Digital"
            className="field"
          />
        </Field>

        <Field label="Agency type *">
          <select
            value={form.agencyType}
            onChange={(e) => update('agencyType', e.target.value)}
            className="field cursor-pointer"
          >
            <option>Performance Marketing Agency</option>
            <option>Web / Shopify Development Agency</option>
            <option>Branding / Design Agency</option>
            <option>SEO / Content Agency</option>
            <option>Automation / Implementation Consultancy</option>
            <option>Creative Studio</option>
            <option>Other Service Agency</option>
          </select>
        </Field>

        <Field label="Active clients *">
          <select
            value={form.clientCount}
            onChange={(e) => update('clientCount', e.target.value)}
            className="field cursor-pointer"
          >
            <option>1–5 active clients</option>
            <option>6–10 active clients</option>
            <option>11–25 active clients</option>
            <option>26–50 active clients</option>
            <option>50+ active clients</option>
          </select>
        </Field>
      </div>

      <div className="mt-4 grid gap-4">
        <Field label="Agency website / portfolio (Optional)">
          <input
            type="url"
            maxLength={300}
            value={form.website}
            onChange={(e) => update('website', e.target.value)}
            placeholder="https://youragency.com"
            className="field"
          />
        </Field>

        <Field label="Anything we should know? (Optional)">
          <textarea
            rows={3}
            maxLength={2000}
            value={form.message}
            onChange={(e) => update('message', e.target.value)}
            placeholder="Who do you serve? What kind of client workflows or stacks do you manage?"
            className="field resize-none"
          />
        </Field>
      </div>

      {/* Consent Checkbox */}
      <div className="mt-4 pt-1">
        <label className="flex items-start gap-2.5 cursor-pointer select-none">
          <input
            type="checkbox"
            checked={form.consentNewsletter}
            onChange={(e) => update('consentNewsletter', e.target.checked)}
            className="mt-0.5 rounded border-zinc-700 bg-zinc-800 text-zinc-100 focus:ring-0 cursor-pointer"
          />
          <span className="text-xs text-zinc-300 leading-relaxed">
            Send me the Cora Operator Brief and agency partner updates. (Unsubscribe anytime).
          </span>
        </label>
      </div>

      {error && (
        <div className="mt-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-xs text-red-300 leading-relaxed">
          {error}
        </div>
      )}

      <button
        type="submit"
        disabled={isSubmitting}
        className="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-5 py-3.5 text-sm font-bold text-zinc-950 transition-all hover:bg-zinc-100 active:scale-[0.99] disabled:opacity-50 cursor-pointer shadow-md"
      >
        {isSubmitting ? 'Submitting Application...' : 'Apply as Agency Partner'}
        {!isSubmitting && <ArrowRight className="h-4 w-4" />}
      </button>

      <p className="mt-3 text-center text-[11px] leading-5 text-zinc-500">
        We use your details strictly to review and coordinate your Cora Agency Partner Program access.
      </p>

      <style jsx>{`
        .field {
          width: 100%;
          border-radius: 0.75rem;
          border: 1px solid rgb(63 63 70);
          background: rgb(24 24 27);
          padding: 0.75rem 0.875rem;
          font-size: 0.875rem;
          color: white;
          outline: none;
          transition: all 0.15s ease;
        }
        .field::placeholder { color: rgb(113 113 122); }
        .field:focus { border-color: rgb(212 212 216); background: rgb(30 30 34); }
      `}</style>
    </form>
  );
}

function Field({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <label className="block">
      <span className="mb-1.5 block text-xs font-medium text-zinc-300">{label}</span>
      {children}
    </label>
  );
}
