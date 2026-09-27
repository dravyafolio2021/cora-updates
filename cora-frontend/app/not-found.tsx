'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { 
  ArrowRight, 
  MessageSquare, 
  Bot, 
  Sparkles, 
  CheckCircle2, 
  Search, 
  Home, 
  BookOpen, 
  Layers
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

export default function NotFound() {
  const [email, setEmail] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSubscribed, setIsSubscribed] = useState(false);

  const handleEmailSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!email || !email.includes('@')) return;
    setIsSubmitting(true);
    try {
      const res = await fetch('/api/newsletter/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email: email.trim(),
          source: '404_branded_page',
          path: '/404',
          referrer: typeof document !== 'undefined' ? document.referrer : '',
        }),
      });
      const data = await res.json().catch(() => ({}));
      if (res.ok && data?.success !== false) {
        trackEvent('newsletter_subscribed', { source: '404_branded_page' });
        setIsSubscribed(true);
      } else {
        window.location.href = 'https://business-on-autopilot.beehiiv.com/?modal=signup';
      }
    } catch {
      window.location.href = 'https://business-on-autopilot.beehiiv.com/?modal=signup';
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <main className="w-full min-h-[calc(100vh-80px)] flex flex-col justify-center items-center px-4 sm:px-6 md:px-8 py-8 md:py-12 bg-gradient-to-b from-white via-[#FAF9F5] to-[#FAF9F5] relative overflow-hidden">
      
      {/* Ambient background glow */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-zinc-200/40 rounded-full blur-3xl pointer-events-none -z-10" />

      <div className="w-full max-w-4xl mx-auto text-center flex flex-col items-center">
        
        {/* 404 Status Pill */}
        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 border border-zinc-200/80 text-zinc-700 text-xs font-mono font-medium mb-4 shadow-2xs">
          <span className="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse" />
          <span>Error 404 &bull; Page Not Found</span>
        </div>

        {/* Headline */}
        <h1 className="font-display text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-semibold text-zinc-950 tracking-[-0.03em] leading-tight mb-2.5">
          Looks like this route took an off-turn.
        </h1>

        <p className="text-zinc-600 text-xs sm:text-sm md:text-base font-normal max-w-xl mx-auto leading-relaxed mb-6">
          The page you requested may have moved or doesn&apos;t exist. Let&apos;s get you back to building your business on autopilot.
        </p>

        {/* ── 2 High-Value Promo Cards: AI Co-Founder + Free Forever Plan ── */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3.5 w-full max-w-2xl text-left mb-6">
          
          {/* Card 1: AI Co-Founder */}
          <Link
            href="/ai-agent"
            onClick={() => trackEvent('cta_click', { section: '404_promo_ai_cofounder' })}
            className="group relative rounded-2xl bg-white/90 hover:bg-white border border-zinc-200/80 hover:border-zinc-300 p-4 transition-all duration-200 shadow-2xs hover:shadow-xs flex flex-col justify-between"
          >
            <div>
              <div className="flex items-center justify-between gap-2 mb-2">
                <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-zinc-100 text-zinc-800 text-[11px] font-medium">
                  <Bot className="w-3.5 h-3.5 text-zinc-600" />
                  <span>Autonomous AI</span>
                </div>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-zinc-950 group-hover:translate-x-0.5 transition-all" />
              </div>
              <h2 className="font-display text-sm font-semibold text-zinc-950 tracking-tight mb-1">
                Meet Cora AI Co-Founder
              </h2>
              <p className="text-zinc-600 text-xs leading-relaxed line-clamp-2">
                Draft client proposals, generate 18% GST invoices, and dispatch team workflows on autopilot.
              </p>
            </div>
            <span className="text-[11px] font-semibold text-zinc-900 group-hover:underline pt-2 inline-block">
              Explore AI Co-Founder &rarr;
            </span>
          </Link>

          {/* Card 2: Free Forever Plan */}
          <a
            href="https://app.heycora.in/workspace/login?source=404_free_forever"
            onClick={() => trackEvent('cta_click', { section: '404_promo_free_forever' })}
            className="group relative rounded-2xl bg-white/90 hover:bg-white border border-zinc-200/80 hover:border-zinc-300 p-4 transition-all duration-200 shadow-2xs hover:shadow-xs flex flex-col justify-between"
          >
            <div>
              <div className="flex items-center justify-between gap-2 mb-2">
                <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200/60 text-[11px] font-medium">
                  <Sparkles className="w-3.5 h-3.5 text-emerald-600" />
                  <span>Free Forever Plan</span>
                </div>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-zinc-950 group-hover:translate-x-0.5 transition-all" />
              </div>
              <h2 className="font-display text-sm font-semibold text-zinc-950 tracking-tight mb-1">
                Start Free Workspace
              </h2>
              <p className="text-zinc-600 text-xs leading-relaxed line-clamp-2">
                Zero credit card needed. Get pre-seeded GST invoices, proposal generator, and client portal.
              </p>
            </div>
            <span className="text-[11px] font-semibold text-emerald-700 group-hover:underline pt-2 inline-block">
              Launch Free Workspace &rarr;
            </span>
          </a>

        </div>

        {/* ── Email Capture Briefing Form ── */}
        <div className="w-full max-w-md mb-6">
          {isSubscribed ? (
            <div className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium w-full justify-center">
              <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
              <span>You&apos;re subscribed to Operator Brief! Check your inbox.</span>
            </div>
          ) : (
            <form onSubmit={handleEmailSubmit} className="flex items-center gap-1.5 w-full">
              <input
                type="email"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="Enter work email for Operator Brief..."
                className="flex-1 min-w-0 bg-white hover:bg-zinc-50/50 focus:bg-white border border-zinc-200 focus:border-zinc-950 rounded-xl px-3.5 py-2.5 text-xs text-zinc-900 placeholder:text-zinc-400 focus:outline-none transition-all shadow-2xs"
              />
              <button
                type="submit"
                disabled={isSubmitting}
                className="shrink-0 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-50 text-white px-4 py-2.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all cursor-pointer shadow-2xs"
              >
                <span>{isSubmitting ? '...' : 'Get Updates'}</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </button>
            </form>
          )}
          <p className="text-[11px] text-zinc-500 mt-1.5">
            Weekly playbooks on agency ops, margin defense &amp; automation. No spam.
          </p>
        </div>

        {/* ── Quick Action Shortcuts & Chat with Founder ── */}
        <div className="flex flex-wrap items-center justify-center gap-2.5">
          <Link
            href="/"
            onClick={() => trackEvent('cta_click', { section: '404_back_home' })}
            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-zinc-50 text-zinc-900 border border-zinc-200/90 text-xs font-medium transition-all shadow-2xs"
          >
            <Home className="w-3.5 h-3.5 text-zinc-500" />
            <span>Back to Home</span>
          </Link>

          <Link
            href="/guides"
            onClick={() => trackEvent('cta_click', { section: '404_explore_guides' })}
            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-zinc-50 text-zinc-900 border border-zinc-200/90 text-xs font-medium transition-all shadow-2xs"
          >
            <BookOpen className="w-3.5 h-3.5 text-zinc-500" />
            <span>Explore Guides</span>
          </Link>

          <Link
            href="/tools"
            onClick={() => trackEvent('cta_click', { section: '404_free_tools' })}
            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-zinc-50 text-zinc-900 border border-zinc-200/90 text-xs font-medium transition-all shadow-2xs"
          >
            <Layers className="w-3.5 h-3.5 text-zinc-500" />
            <span>Free Micro-Tools</span>
          </Link>

          <a
            href="mailto:dravya.bansal@heycora.in?subject=Help%20from%20404%20Page%20-%20HeyCora"
            onClick={() => trackEvent('cta_click', { section: '404_chat_founder' })}
            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-medium transition-all shadow-2xs"
          >
            <MessageSquare className="w-3.5 h-3.5 text-zinc-400" />
            <span>Chat with Founder</span>
          </a>
        </div>

      </div>

    </main>
  );
}
