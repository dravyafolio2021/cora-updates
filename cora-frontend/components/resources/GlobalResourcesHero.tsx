'use client';

import React, { useState } from 'react';
import { Search, Mail, ArrowRight, Check, Loader2 } from 'lucide-react';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';

export interface ResourcePillar {
  icon: React.ReactNode;
  text: string;
}

export interface ResourceSearchProps {
  value: string;
  onChange: (val: string) => void;
  placeholder?: string;
}

export interface GlobalResourcesHeroProps {
  badgeIcon?: React.ReactNode;
  badgeText: string;
  titleLine1: string;
  titleLine2?: string;
  description: string;
  searchProps?: ResourceSearchProps;
  showEmailCapture?: boolean;
  emailSource?: string;
  emailPlaceholder?: string;
  emailButtonText?: string;
  children?: React.ReactNode;
  tone?: 'studio' | 'neutral' | 'warm';
}

export function GlobalResourcesHero({
  badgeIcon,
  badgeText,
  titleLine1,
  titleLine2,
  description,
  searchProps,
  showEmailCapture = true,
  emailSource = 'resources_hero',
  emailPlaceholder = 'Enter your work email for free instant access...',
  emailButtonText = 'Get Instant Access',
  children,
  tone = 'neutral'
}: GlobalResourcesHeroProps) {
  const [email, setEmail] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSubscribed, setIsSubscribed] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');

  const handleEmailSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!email || !email.includes('@')) return;
    setIsSubmitting(true);
    setErrorMessage('');

    try {
      const res = await fetch('/api/newsletter/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email: email.trim(),
          source: emailSource,
          path: typeof window !== 'undefined' ? window.location.pathname : '',
        }),
      });

      const data = await res.json().catch(() => ({}));
      if (!res.ok || data?.success === false) {
        throw new Error(data?.error || 'Could not subscribe.');
      }
      setIsSubscribed(true);
    } catch (err: any) {
      console.error('[Beehiiv Hero Capture]', err);
      window.location.href = 'https://business-on-autopilot.beehiiv.com/?modal=signup';
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <section className="relative w-full pt-[128px] sm:pt-36 pb-10 sm:pb-16 overflow-hidden">
      <ArtisticHeroBackground tone={tone} />

      <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center">
        
        {/* Status Badge */}
        <div className="mb-4 sm:mb-6">
          <span className="inline-flex items-center gap-1.5 px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-full bg-white/95 backdrop-blur-md text-zinc-900 border border-zinc-200/80 text-xs sm:text-sm font-medium shadow-2xs">
            {badgeIcon}
            <span>{badgeText}</span>
          </span>
        </div>

        {/* 2-Line Headline */}
        <h1 className="font-display text-4xl sm:text-6xl md:text-7xl font-semibold text-zinc-950 tracking-[-0.035em] leading-[1.12] sm:leading-[1.08] mb-4 sm:mb-6 max-w-[980px] mx-auto">
          <span className="block">{titleLine1}</span>
          {titleLine2 && (
            <span className="block mt-1 sm:mt-2 text-zinc-600 font-normal text-2xl sm:text-4xl md:text-5xl">
              {titleLine2}
            </span>
          )}
        </h1>

        {/* Subtitle */}
        <p className="text-zinc-600 text-sm sm:text-base md:text-lg font-normal leading-relaxed max-w-[680px] mx-auto mb-7 sm:mb-8 px-3">
          {description}
        </p>

        {/* Integrated Beehiiv API Email Capture Form */}
        {showEmailCapture && (
          <div className="max-w-[560px] mx-auto mb-6">
            {isSubscribed ? (
              <div className="p-3.5 sm:p-4 rounded-2xl bg-white/95 border border-zinc-200/90 shadow-sm backdrop-blur-md flex items-center justify-center gap-2.5 text-xs sm:text-sm font-semibold text-zinc-950 animate-in fade-in zoom-in-95 duration-200">
                <div className="w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center shrink-0">
                  <Check className="w-3 h-3 stroke-[3]" />
                </div>
                <span>You’re in! We’ve sent instant access and updates to your inbox.</span>
              </div>
            ) : (
              <form onSubmit={handleEmailSubmit} className="space-y-2">
                <div className="flex flex-col sm:flex-row items-center gap-2 p-1.5 rounded-2xl bg-white/95 border border-zinc-200/90 shadow-[0_8px_30px_rgba(0,0,0,0.06)] backdrop-blur-xl hover:border-zinc-300 transition-all">
                  <div className="relative w-full flex items-center">
                    <Mail className="w-4 h-4 text-zinc-400 absolute left-3.5 pointer-events-none" />
                    <input
                      type="email"
                      required
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      placeholder={emailPlaceholder}
                      className="w-full pl-10 pr-3 py-2.5 text-xs sm:text-sm bg-transparent text-zinc-950 placeholder:text-zinc-400 focus:outline-none"
                    />
                  </div>
                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className="w-full sm:w-auto shrink-0 inline-flex items-center justify-center gap-1.5 bg-zinc-950 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-2xs cursor-pointer disabled:opacity-50"
                  >
                    {isSubmitting ? (
                      <Loader2 className="w-3.5 h-3.5 animate-spin" />
                    ) : (
                      <>
                        <span>{emailButtonText}</span>
                        <ArrowRight className="w-3.5 h-3.5" />
                      </>
                    )}
                  </button>
                </div>
                {errorMessage && (
                  <p className="text-[11px] text-rose-600 font-medium">{errorMessage}</p>
                )}
                <div className="flex items-center justify-center gap-2 text-[11px] text-zinc-500 font-mono">
                  <span>Zero spam • 100% Free • Unsubscribe anytime</span>
                </div>
              </form>
            )}
          </div>
        )}

        {/* Central Search Bar */}
        {searchProps && (
          <div className="max-w-[560px] mx-auto mb-4">
            <div className="relative flex items-center bg-white/90 backdrop-blur-md border border-zinc-200/80 rounded-full px-4 py-2 shadow-2xs hover:border-zinc-300 transition-all">
              <Search className="w-4 h-4 text-zinc-400 mr-3 shrink-0" />
              <input
                type="text"
                value={searchProps.value}
                onChange={(e) => searchProps.onChange(e.target.value)}
                placeholder={searchProps.placeholder || "Search..."}
                className="w-full bg-transparent text-xs sm:text-sm text-zinc-950 placeholder:text-zinc-400 focus:outline-none font-sans"
              />
              {searchProps.value && (
                <button
                  type="button"
                  onClick={() => searchProps.onChange('')}
                  className="text-xs font-mono text-zinc-400 hover:text-zinc-900 px-2 py-0.5 rounded cursor-pointer"
                >
                  Clear
                </button>
              )}
            </div>
          </div>
        )}

        {/* Custom Actions / Forms / Children */}
        {children}

      </div>
    </section>
  );
}
