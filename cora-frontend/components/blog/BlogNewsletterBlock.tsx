'use client';

import React, { useState } from 'react';
import { Mail, Check, ArrowRight, Sparkles, ShieldCheck } from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

interface BlogNewsletterBlockProps {
  heading?: string;
  tagline?: string;
  buttonText?: string;
  articleSlug?: string;
  category?: string;
  placement?: 'inline' | 'end';
}

export function BlogNewsletterBlock({
  heading = 'Run your business with fewer moving parts.',
  tagline = 'One practical operating system, workflow breakdown, or agency case study every week.',
  buttonText = 'Join Free',
  articleSlug = 'general_blog',
  category = 'agency-operations',
  placement = 'inline',
}: BlogNewsletterBlockProps) {
  const [email, setEmail] = useState('');
  const [status, setStatus] = useState<'idle' | 'loading' | 'success' | 'error'>('idle');
  const [errorMessage, setErrorMessage] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!email || !email.includes('@')) {
      setErrorMessage('Please enter a valid work email.');
      setStatus('error');
      return;
    }

    setStatus('loading');
    setErrorMessage('');

    try {
      const response = await fetch('/api/newsletter/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email: email.trim().toLowerCase(),
          source: `cora_blog_${placement}`,
          path: typeof window !== 'undefined' ? window.location.pathname : `/blog/${articleSlug}/`,
          referrer: typeof document !== 'undefined' ? document.referrer : '',
          utm_source: 'cora_blog',
          utm_medium: 'website',
          utm_campaign: 'article_subscriber',
          utm_content: articleSlug,
        }),
      });

      const data = await response.json().catch(() => ({}));
      if (!response.ok && !data?.success) {
        throw new Error(data?.error || 'Could not subscribe right now.');
      }

      trackEvent('newsletter_signup', {
        source: `cora_blog_${placement}`,
        article_slug: articleSlug,
        category: category,
      });

      setStatus('success');
      setEmail('');
    } catch (err: any) {
      setErrorMessage(err?.message || 'Something went wrong. Please try again.');
      setStatus('error');
    }
  };

  if (status === 'success') {
    return (
      <div className="my-10 rounded-3xl border border-zinc-200 dark:border-zinc-800 bg-[#FBFaf7] dark:bg-zinc-900/90 p-6 sm:p-8 text-center shadow-sm animate-in fade-in duration-300">
        <div className="w-10 h-10 mx-auto rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-3">
          <Check className="w-5 h-5" />
        </div>
        <h4 className="font-display text-lg sm:text-xl font-bold text-zinc-950 dark:text-zinc-50">
          You're subscribed to the Operator Brief.
        </h4>
        <p className="mt-2 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 max-w-md mx-auto leading-relaxed">
          Check your inbox for our latest agency operating breakdown. Unsubscribe anytime.
        </p>
      </div>
    );
  }

  return (
    <div
      className={`my-10 rounded-3xl border p-6 sm:p-8 ${
        placement === 'end'
          ? 'border-zinc-900 dark:border-zinc-700 bg-zinc-950 text-white shadow-xl'
          : 'border-zinc-200 dark:border-zinc-800 bg-[#FBFaf7] dark:bg-zinc-900/80 text-zinc-900 dark:text-zinc-100 shadow-sm'
      }`}
    >
      <div className="max-w-xl mx-auto text-center">
        <div
          className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-mono font-bold tracking-wider uppercase mb-3 ${
            placement === 'end'
              ? 'bg-white/10 text-zinc-300 border border-white/15'
              : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700'
          }`}
        >
          <Sparkles className="w-3 h-3 text-amber-500" />
          <span>CORA OPERATOR BRIEF</span>
        </div>

        <h3
          className={`font-display text-xl sm:text-2xl font-bold tracking-tight ${
            placement === 'end' ? 'text-white' : 'text-zinc-950 dark:text-zinc-50'
          }`}
        >
          {heading}
        </h3>

        <p
          className={`mt-2 text-xs sm:text-sm leading-relaxed ${
            placement === 'end' ? 'text-zinc-400' : 'text-zinc-600 dark:text-zinc-400'
          }`}
        >
          {tagline}
        </p>

        <form onSubmit={handleSubmit} className="mt-5 flex flex-col sm:flex-row items-center gap-2 max-w-md mx-auto">
          <input
            type="email"
            required
            placeholder="Enter your work email..."
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            disabled={status === 'loading'}
            className={`w-full flex-1 px-4 py-3 rounded-xl text-xs sm:text-sm outline-none transition-all ${
              placement === 'end'
                ? 'bg-zinc-900 border border-zinc-700 text-white placeholder:text-zinc-500 focus:border-zinc-400'
                : 'bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-zinc-400 shadow-sm'
            }`}
          />
          <button
            type="submit"
            disabled={status === 'loading'}
            className={`w-full sm:w-auto px-5 py-3 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer ${
              placement === 'end'
                ? 'bg-white hover:bg-zinc-100 text-zinc-950'
                : 'bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-100'
            }`}
          >
            {status === 'loading' ? 'Joining...' : buttonText}
            {status !== 'loading' && <ArrowRight className="w-4 h-4" />}
          </button>
        </form>

        {errorMessage && (
          <p className="mt-3 text-xs text-red-500 dark:text-red-400">{errorMessage}</p>
        )}

        <div
          className={`mt-4 flex items-center justify-center gap-2 text-[11px] font-mono ${
            placement === 'end' ? 'text-zinc-500' : 'text-zinc-500 dark:text-zinc-400'
          }`}
        >
          <ShieldCheck className="w-3.5 h-3.5" />
          <span>Zero spam &bull; Direct founder insights &bull; Unsubscribe anytime</span>
        </div>
      </div>
    </div>
  );
}
