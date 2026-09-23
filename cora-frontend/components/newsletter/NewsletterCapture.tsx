'use client';

import { useState } from 'react';
import Link from 'next/link';
import { ArrowRight, CheckCircle2 } from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

type NewsletterCaptureProps = {
  source: string;
  eyebrow?: string;
  title?: string;
  description?: string;
  compact?: boolean;
  dark?: boolean;
  hostedFallbackUrl?: string;
};

export function NewsletterCapture({
  source,
  eyebrow = 'CORA OPERATOR BRIEF',
  title = 'One useful agency system every week.',
  description = 'Practical workflows, templates and operating ideas for running a calmer, more profitable client-service business.',
  compact = false,
  dark = false,
  hostedFallbackUrl = 'https://business-on-autopilot.beehiiv.com/?modal=signup',
}: NewsletterCaptureProps) {
  const [email, setEmail] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [done, setDone] = useState(false);
  const [error, setError] = useState('');

  const openHostedFallback = () => {
    trackEvent('newsletter_fallback_opened', { source });
    window.location.href = hostedFallbackUrl;
  };

  const submit = async (event: React.FormEvent) => {
    event.preventDefault();
    setError('');
    setSubmitting(true);

    try {
      const response = await fetch('/api/newsletter/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email: email.trim(),
          source,
          path: typeof window !== 'undefined' ? window.location.pathname : '',
          referrer: typeof document !== 'undefined' ? document.referrer : '',
        }),
      });

      const data = await response.json().catch(() => ({}));
      if (!response.ok || data?.success === false) {
        throw new Error(data?.error || 'Could not subscribe through the website API.');
      }

      trackEvent('newsletter_subscribed', { source });
      setDone(true);
    } catch (err: any) {
      console.error('[NewsletterCapture] Website API unavailable; using Beehiiv hosted signup.', err);
      setError('Opening the secure Beehiiv signup…');
      window.setTimeout(openHostedFallback, 650);
    } finally {
      setSubmitting(false);
    }
  };

  const shell = dark
    ? 'border-zinc-800 bg-zinc-950 text-white'
    : 'border-zinc-200 bg-white text-zinc-950';
  const muted = dark ? 'text-zinc-400' : 'text-zinc-600';
  const field = dark
    ? 'border-zinc-700 bg-zinc-900 text-white placeholder:text-zinc-600 focus:border-zinc-500'
    : 'border-zinc-300 bg-white text-zinc-950 placeholder:text-zinc-400 focus:border-zinc-500';

  if (done) {
    return (
      <div className={`rounded-3xl border ${shell} ${compact ? 'p-5 sm:p-6' : 'p-6 sm:p-8'}`}>
        <div className="flex items-start gap-3">
          <CheckCircle2 className="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" />
          <div>
            <div className="font-display text-lg font-semibold">You’re in.</div>
            <p className={`mt-1 text-sm leading-6 ${muted}`}>The next Cora Operator Brief will land in your inbox.</p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <section className={`rounded-3xl border ${shell} ${compact ? 'p-5 sm:p-6' : 'p-6 sm:p-8'}`}>
      <div className={compact ? '' : 'grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end'}>
        <div>
          <div className={`text-[10px] font-mono font-bold uppercase tracking-[0.16em] ${dark ? 'text-zinc-500' : 'text-zinc-400'}`}>
            {eyebrow}
          </div>
          <h3 className={`font-display font-semibold tracking-[-0.03em] ${compact ? 'mt-2 text-xl' : 'mt-3 text-2xl sm:text-3xl'}`}>
            {title}
          </h3>
          <p className={`mt-2 max-w-2xl text-sm leading-6 ${muted}`}>{description}</p>
        </div>

        <form onSubmit={submit} className={`${compact ? 'mt-4' : 'mt-5 lg:mt-0'} min-w-0 lg:min-w-[390px]`}>
          <div className="flex flex-col gap-2 sm:flex-row">
            <input
              type="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="you@agency.com"
              aria-label="Email address"
              className={`min-w-0 flex-1 rounded-xl border px-4 py-3 text-sm outline-none transition ${field}`}
            />
            <button
              type="submit"
              disabled={submitting}
              className={`${dark ? 'bg-white text-zinc-950 hover:bg-zinc-200' : 'bg-zinc-950 text-white hover:bg-zinc-800'} inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold transition disabled:opacity-50`}
            >
              {submitting ? 'Joining...' : 'Join Free'}
              {!submitting && <ArrowRight className="h-4 w-4" />}
            </button>
          </div>
          {error && <p className="mt-2 text-xs text-amber-600">{error}</p>}
          <div className={`mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[10px] leading-4 ${dark ? 'text-zinc-600' : 'text-zinc-400'}`}>
            <span>Useful operator notes only. Unsubscribe anytime.</span>
            {source !== 'newsletter_page' && (
              <Link href="/newsletter/" className="font-semibold underline underline-offset-2 hover:text-zinc-700">
                About the brief
              </Link>
            )}
            <button
              type="button"
              onClick={openHostedFallback}
              className="font-semibold underline underline-offset-2 hover:text-zinc-700"
            >
              Use Beehiiv signup
            </button>
          </div>
        </form>
      </div>
    </section>
  );
}
