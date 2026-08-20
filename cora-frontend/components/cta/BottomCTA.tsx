'use client';

import React from 'react';
import { ArrowRight, Sparkles, ShieldCheck, Zap } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export function BottomCTA() {
  const handleGoogleClick = () => {
    trackEvent('cta_bottom_google_signup_clicked', { source: 'bottom_banner' });
  };

  return (
    <section className="py-16 md:py-24 relative z-10 bg-white">
      <div className="w-full max-w-[1140px] mx-auto px-4 sm:px-6">
        
        <div className="bg-zinc-950 text-white rounded-3xl p-8 sm:p-12 md:p-16 text-center relative overflow-hidden shadow-2xl border border-zinc-800">
          
          {/* Ambient Glow */}
          <div className="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl pointer-events-none" />

          <div className="relative z-10 max-w-[720px] mx-auto flex flex-col items-center">
            
            <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-zinc-300 mb-5">
              <Sparkles className="w-3.5 h-3.5 text-purple-400" />
              <span>Get Started in 60 Seconds</span>
            </div>

            <h2 className="font-display text-[clamp(2rem,4.2vw,3.2rem)] font-[550] tracking-[-0.035em] text-white leading-[1.15] mb-4">
              Automate your entire agency workflow today.
            </h2>

            <p className="font-sans text-xs sm:text-base text-zinc-400 leading-relaxed max-w-[580px] mb-8 font-normal">
              Join 1,200+ founders, studios, and agencies routing multi-model AI agents, closing e-sign agreements, and settling invoices in one command center.
            </p>

            {/* CTAs */}
            <div className="inline-flex items-center justify-center gap-4 flex-col sm:flex-row w-full max-w-[360px] sm:max-w-none mb-8">
              <a
                href="https://app.heycora.in/workspace/login?source=bottom_cta_google"
                onClick={handleGoogleClick}
                className="inline-flex items-center justify-center gap-2.5 bg-white text-zinc-950 px-6 py-3.5 rounded-xl font-sans text-sm font-bold tracking-tight shadow-md hover:bg-zinc-100 transition-all hover:-translate-y-0.5 w-full sm:w-auto"
              >
                {/* Google G Icon */}
                <svg className="w-4 h-4" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                  <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.36 24 12 24z"/>
                  <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.98 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                  <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                </svg>
                <span>Sign up with Google</span>
              </a>

              <a
                href="mailto:dravya.bansal@heycora.in"
                className="inline-flex items-center justify-center gap-2 bg-transparent text-white border border-zinc-800 px-5 py-3.5 rounded-xl font-sans text-sm font-medium hover:bg-zinc-900 transition-all hover:-translate-y-0.5 w-full sm:w-auto"
              >
                <span>Connect with founder</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </a>
            </div>

            {/* Trust Badges */}
            <div className="flex items-center justify-center gap-4 sm:gap-6 text-xs text-zinc-400 flex-wrap">
              <div className="flex items-center gap-1.5">
                <Sparkles className="w-3.5 h-3.5 text-purple-400" />
                <span>1,000 free AI runs/mo</span>
              </div>
              <div className="w-1 h-1 rounded-full bg-zinc-700" />
              <div className="flex items-center gap-1.5">
                <Zap className="w-3.5 h-3.5 text-amber-400" />
                <span>1-click setup</span>
              </div>
              <div className="w-1 h-1 rounded-full bg-zinc-700" />
              <div className="flex items-center gap-1.5">
                <ShieldCheck className="w-3.5 h-3.5 text-emerald-400" />
                <span>No credit card needed</span>
              </div>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
