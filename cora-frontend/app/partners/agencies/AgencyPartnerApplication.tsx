'use client';

import { useState } from 'react';
import { ArrowRight, CheckCircle2 } from 'lucide-react';
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
  });

  const update = (key: keyof typeof form, value: string) => {
    setForm((current) => ({ ...current, [key]: value }));
  };

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    setError('');
    setIsSubmitting(true);

    try {
      const response = await fetch('/api/contact/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          name: form.name,
          email: form.email,
          phone: form.phone,
          companyName: form.companyName,
          industry: form.agencyType,
          selectedTopics: ['Agency Partner Program', 'Professional Plan Access', 'Partner Commission'],
          message: [
            `Active client range: ${form.clientCount}`,
            form.website ? `Agency website: ${form.website}` : '',
            form.message ? `Note: ${form.message}` : '',
          ].filter(Boolean).join('\n'),
          source: 'Agency Partner Program (/partners/agencies)',
        }),
      });

      const data = await response.json().catch(() => ({}));
      if (!response.ok || data?.success === false) {
        throw new Error(data?.error || 'Could not submit the application.');
      }

      trackEvent('agency_partner_application_submitted', {
        agency_type: form.agencyType,
        client_count: form.clientCount,
        delivered: !!data?.delivered,
      });
      setSubmitted(true);
    } catch (err: any) {
      setError(err?.message || 'Could not submit the application. Please try again.');
    } finally {
      setIsSubmitting(false);
    }
  };

  if (submitted) {
    return (
      <div className="rounded-3xl border border-zinc-700 bg-zinc-900 p-7 sm:p-8">
        <CheckCircle2 className="h-8 w-8 text-emerald-400" />
        <h3 className="mt-5 text-2xl font-semibold">Application received.</h3>
        <p className="mt-3 text-sm leading-7 text-zinc-300">We have your agency details. We will review the fit and contact you using the details you submitted.</p>
        <a href="/agency-management-software-india/" className="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-white underline underline-offset-4">
          Explore Cora for agencies <ArrowRight className="h-4 w-4" />
        </a>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="rounded-3xl border border-zinc-700 bg-zinc-900 p-6 sm:p-8">
      <div className="grid gap-4 sm:grid-cols-2">
        <Field label="Your name">
          <input required value={form.name} onChange={(e) => update('name', e.target.value)} placeholder="Your name" className="field" />
        </Field>
        <Field label="Work email">
          <input required type="email" value={form.email} onChange={(e) => update('email', e.target.value)} placeholder="you@agency.com" className="field" />
        </Field>
        <Field label="WhatsApp / phone">
          <input required value={form.phone} onChange={(e) => update('phone', e.target.value)} placeholder="+91..." className="field" />
        </Field>
        <Field label="Agency name">
          <input required value={form.companyName} onChange={(e) => update('companyName', e.target.value)} placeholder="Agency name" className="field" />
        </Field>
        <Field label="Agency type">
          <select value={form.agencyType} onChange={(e) => update('agencyType', e.target.value)} className="field">
            <option>Performance Marketing Agency</option>
            <option>Web / Shopify Development Agency</option>
            <option>Branding / Design Agency</option>
            <option>SEO / Content Agency</option>
            <option>Automation / Implementation Consultancy</option>
            <option>Creative Studio</option>
            <option>Other Service Agency</option>
          </select>
        </Field>
        <Field label="Active clients">
          <select value={form.clientCount} onChange={(e) => update('clientCount', e.target.value)} className="field">
            <option>1–5 active clients</option>
            <option>6–10 active clients</option>
            <option>11–25 active clients</option>
            <option>26–50 active clients</option>
            <option>50+ active clients</option>
          </select>
        </Field>
      </div>

      <div className="mt-4 grid gap-4">
        <Field label="Agency website / LinkedIn">
          <input value={form.website} onChange={(e) => update('website', e.target.value)} placeholder="https://" className="field" />
        </Field>
        <Field label="Anything we should know?">
          <textarea rows={3} value={form.message} onChange={(e) => update('message', e.target.value)} placeholder="Who do you serve? What kind of client workflows do you manage?" className="field resize-none" />
        </Field>
      </div>

      {error && <p className="mt-4 text-sm text-red-300">{error}</p>}

      <button type="submit" disabled={isSubmitting} className="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-zinc-950 transition hover:bg-zinc-200 disabled:opacity-50">
        {isSubmitting ? 'Submitting...' : 'Apply as Agency Partner'}
        {!isSubmitting && <ArrowRight className="h-4 w-4" />}
      </button>
      <p className="mt-3 text-center text-[11px] leading-5 text-zinc-500">We use your details only to review and contact you about the Cora Agency Partner Program.</p>

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
        }
        .field::placeholder { color: rgb(113 113 122); }
        .field:focus { border-color: rgb(161 161 170); }
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
