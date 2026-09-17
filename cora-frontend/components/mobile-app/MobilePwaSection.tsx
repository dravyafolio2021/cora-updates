'use client';

import React, { useState } from 'react';
import Image from 'next/image';
import {
  Smartphone,
  FileText,
  Users,
  CreditCard,
  BarChart3,
  ArrowRight,
  Sparkles,
  Search,
  X,
  Zap,
  ShieldCheck,
  Layers,
  CheckCircle2,
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export function MobilePwaSection() {
  const [showPwaModal, setShowPwaModal] = useState(false);
  const [copiedState, setCopiedState] = useState(false);

  const handleInstallClick = () => {
    trackEvent('mobile_pwa_install_click', { section: 'mobile_pwa_section' });
    setCopiedState(true);
    setTimeout(() => setCopiedState(false), 3000);
  };

  return (
    <section
      id="mobile-app"
      className="w-full py-16 sm:py-[100px] relative z-10 bg-white overflow-hidden"
    >
      {/* ── Natural Atmospheric Scenic Background & Soft Ambient Lighting ── */}
      <div className="absolute inset-0 pointer-events-none select-none overflow-hidden -z-10">
        
        {/* 1. Natural Atmospheric Sky & Meadow Landscape Canvas */}
        <div className="absolute inset-0 opacity-40 mix-blend-multiply pointer-events-none">
          <Image
            src="/images/cora_hero_landscape.jpg"
            alt="Cora Atmospheric Landscape"
            fill
            className="object-cover object-[center_35%]"
            sizes="100vw"
          />
        </div>

        {/* 2. Silky Multi-Stop Pure White Fade Veils */}
        <div
          className="absolute inset-0 pointer-events-none"
          style={{
            background: 'linear-gradient(180deg, rgba(255, 255, 255, 0.82) 0%, rgba(255, 255, 255, 0.65) 30%, rgba(255, 255, 255, 0.92) 75%, #ffffff 100%)',
          }}
        />

        {/* 3. Soft Ambient Luminous Aura centered behind phone */}
        <div
          className="absolute top-1/2 right-[5%] sm:right-[15%] -translate-y-1/2 w-[500px] sm:w-[700px] h-[500px] sm:h-[700px] rounded-full blur-[100px] opacity-70 pointer-events-none"
          style={{
            background: 'radial-gradient(circle at 50% 50%, rgba(254, 240, 199, 0.6) 0%, rgba(224, 242, 254, 0.5) 45%, transparent 75%)',
          }}
        />

        {/* 4. Top & Bottom Smooth Transition Fades */}
        <div className="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-white via-white/80 to-transparent pointer-events-none" />
        <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white via-white/90 to-transparent pointer-events-none" />
      </div>

      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {/* Edge-to-Edge Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
          
          {/* ── Left Column: Editorial & Action Deck (7 cols) ── */}
          <div className="lg:col-span-7 space-y-7 sm:space-y-8">
            
            <div className="space-y-5">
              {/* Eyebrow Pill */}
              <div>
                <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white/90 backdrop-blur-sm rounded-full border border-zinc-200/90 text-xs font-semibold text-zinc-800 shadow-2xs">
                  <Smartphone className="w-3.5 h-3.5 text-zinc-900" />
                  <span className="tracking-wide uppercase text-[11px] font-mono">MOBILE ACCESS</span>
                  <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse ml-0.5" />
                </div>
              </div>

              {/* Headings */}
              <div className="space-y-3">
                <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[46px] xl:text-[50px] font-bold leading-[1.12] tracking-[-0.03em] text-zinc-950">
                  Cora on your phone. <br />
                  <span className="bg-gradient-to-r from-zinc-700 via-zinc-500 to-zinc-400 bg-clip-text text-transparent">
                    Your business, always with you.
                  </span>
                </h2>

                <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[540px]">
                  Install Cora as a PWA and access your workspace anytime, anywhere.
                </p>
              </div>

              {/* CTA Action Buttons */}
              <div className="flex flex-wrap items-center gap-3 pt-1">
                <a
                  href="https://app.heycora.in/workspace/login?source=mobile_pwa_button"
                  onClick={handleInstallClick}
                  className="inline-flex items-center gap-2.5 bg-zinc-950 text-white px-6 py-3.5 rounded-2xl text-sm font-semibold hover:bg-zinc-800 active:scale-[0.98] transition-all shadow-sm group"
                >
                  <Smartphone className="w-4 h-4 text-zinc-400 group-hover:text-white transition-colors" />
                  <span>Install App Now</span>
                  <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                </a>

                <button
                  type="button"
                  onClick={() => setShowPwaModal(true)}
                  className="inline-flex items-center gap-2 bg-white hover:bg-zinc-50 text-zinc-800 px-5 py-3.5 rounded-2xl text-sm font-medium border border-zinc-200/90 transition-all shadow-2xs cursor-pointer"
                >
                  <Sparkles className="w-4 h-4 text-purple-600" />
                  <span>What is a PWA?</span>
                  <Search className="w-3.5 h-3.5 text-zinc-400 ml-1" />
                </button>
              </div>

              {/* Toast Feedback upon Clicking Install */}
              {copiedState && (
                <div className="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 flex items-center gap-2 animate-fadeIn max-w-md">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                  <span>Open <strong>heycora.in</strong> in Safari/Chrome → Tap <strong>Share</strong> → <strong>Add to Home Screen</strong></span>
                </div>
              )}

              {/* Supported Platforms Strip with Unified Official Monochrome Icons */}
              <div className="flex flex-wrap items-center gap-3 text-xs text-zinc-500 font-medium pt-1">
                <span>Works on all devices. No app store required.</span>
                <div className="inline-flex items-center gap-2.5 text-zinc-700 ml-0.5">
                  
                  {/* Official Apple Logo */}
                  <span title="iOS (Apple)" className="hover:text-zinc-950 transition-colors cursor-default">
                    <svg className="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.61-.75 1.04-1.8 0.92-2.87-.93.04-2.02.62-2.66 1.37-.56.65-.99 1.7-0.88 2.72 1.03.08 2.02-.48 2.62-1.22z" />
                    </svg>
                  </span>

                  {/* Official Android Robot */}
                  <span title="Android" className="hover:text-zinc-950 transition-colors cursor-default">
                    <svg className="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.551 0 .9993.4482.9993.9993.0001.5511-.4482.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.411 13.856 8.125 12 8.125c-1.856 0-3.5902.286-5.1368.8247L4.841 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.33 14.65 0 18.775h24c-.33-4.125-2.6889-7.5883-6.1185-9.4536z" />
                    </svg>
                  </span>

                  {/* Official Google Chrome */}
                  <span title="Google Chrome & Safari Web" className="hover:text-zinc-950 transition-colors cursor-default">
                    <svg className="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 0C8.21 0 4.831 1.757 2.632 4.501l3.953 6.848A5.454 5.454 0 0 1 12 6.545h10.691A12 12 0 0 0 12 0zM1.931 5.47A11.943 11.943 0 0 0 0 12c0 6.012 4.42 10.991 10.189 11.864l3.953-6.847a5.45 5.45 0 0 1-6.852-2.296L1.931 5.47zm8.705 9.875l-3.953 6.848C8.32 23.367 10.108 24 12 24c6.627 0 12-5.373 12-12 0-.61-.045-1.209-.133-1.795H13.81a5.455 5.455 0 0 1-3.174 5.14zM12 7.636a4.364 4.364 0 1 0 0 8.728 4.364 4.364 0 0 0 0-8.728z" />
                    </svg>
                  </span>

                </div>
              </div>
            </div>

            {/* ── 4 Capability Squircle Badges Row ── */}
            <div className="pt-2 sm:pt-4">
              <div className="grid grid-cols-4 gap-3 sm:gap-4 max-w-[440px]">
                
                {/* Invoices */}
                <div className="flex flex-col items-center text-center gap-2">
                  <div className="w-14 h-14 sm:w-16 sm:h-16 rounded-[18px] sm:rounded-[20px] bg-[#E8F7F0] text-[#167049] flex items-center justify-center transition-transform hover:scale-105 shadow-2xs">
                    <FileText className="w-6 h-6 sm:w-7 sm:h-7 stroke-[1.8]" />
                  </div>
                  <span className="text-xs sm:text-[13px] font-medium text-zinc-800">Invoices</span>
                </div>

                {/* Clients */}
                <div className="flex flex-col items-center text-center gap-2">
                  <div className="w-14 h-14 sm:w-16 sm:h-16 rounded-[18px] sm:rounded-[20px] bg-[#F0EEFD] text-[#5D4BD6] flex items-center justify-center transition-transform hover:scale-105 shadow-2xs">
                    <Users className="w-6 h-6 sm:w-7 sm:h-7 stroke-[1.8]" />
                  </div>
                  <span className="text-xs sm:text-[13px] font-medium text-zinc-800">Clients</span>
                </div>

                {/* Payments */}
                <div className="flex flex-col items-center text-center gap-2">
                  <div className="w-14 h-14 sm:w-16 sm:h-16 rounded-[18px] sm:rounded-[20px] bg-[#FDEEE8] text-[#C44A2E] flex items-center justify-center transition-transform hover:scale-105 shadow-2xs">
                    <CreditCard className="w-6 h-6 sm:w-7 sm:h-7 stroke-[1.8]" />
                  </div>
                  <span className="text-xs sm:text-[13px] font-medium text-zinc-800">Payments</span>
                </div>

                {/* Everything */}
                <div className="flex flex-col items-center text-center gap-2">
                  <div className="w-14 h-14 sm:w-16 sm:h-16 rounded-[18px] sm:rounded-[20px] bg-[#E8F2FD] text-[#2866C5] flex items-center justify-center transition-transform hover:scale-105 shadow-2xs">
                    <BarChart3 className="w-6 h-6 sm:w-7 sm:h-7 stroke-[1.8]" />
                  </div>
                  <span className="text-xs sm:text-[13px] font-medium text-zinc-800">Everything</span>
                </div>

              </div>

              {/* Centered Sub-Divider Line */}
              <div className="flex items-center gap-3 mt-5 max-w-[440px]">
                <div className="flex-1 h-px bg-zinc-200/80" />
                <span className="text-xs text-zinc-400 font-medium">Same power. On the go.</span>
                <div className="flex-1 h-px bg-zinc-200/80" />
              </div>
            </div>

          </div>

          {/* ── Right Column: Clean Floating 3D Angled Phone (5 cols) ── */}
          <div className="lg:col-span-5 relative flex items-center justify-center min-h-[440px] sm:min-h-[500px] lg:min-h-[560px] pt-8 sm:pt-6">
            
            {/* Handwritten Sketch Annotation (Desktop & Tablet) - Positioned safely above phone */}
            <div className="hidden sm:block absolute -top-6 sm:-top-8 lg:-top-10 right-0 sm:right-2 lg:-right-6 z-20 pointer-events-none text-right select-none">
              <div className="font-serif italic text-zinc-900 text-sm sm:text-[15px] font-semibold tracking-tight transform rotate-2">
                Same workspace. <br />
                Now in your pocket.
              </div>
              {/* Curved Arrow Vector pointing down to phone */}
              <svg className="w-11 h-9 text-zinc-700 mt-1 ml-auto mr-4 transform rotate-6" viewBox="0 0 50 40" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round">
                <path d="M44 4 C 32 16, 22 8, 9 26" />
                <path d="M17 24 L 8 26 L 8 18" />
              </svg>
            </div>

            {/* 3D Angled Phone Container with Soft Depth Shadow */}
            <div className="relative w-[270px] xs:w-[300px] sm:w-[330px] lg:w-[360px] xl:w-[380px] select-none transition-transform duration-300 hover:scale-[1.02]">
              <Image
                src="/images/mobile_pwa_phone_mockup.png"
                alt="Cora Mobile App UI running on iPhone"
                width={560}
                height={966}
                priority
                className="w-full h-auto drop-shadow-[0_24px_48px_rgba(0,0,0,0.16)]"
                sizes="(max-width: 640px) 280px, (max-width: 1024px) 340px, 380px"
              />
            </div>

          </div>

        </div>

      </div>

      {/* ── Educational "What is a PWA?" Modal ── */}
      {showPwaModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fadeIn">
          <div className="bg-white rounded-3xl border border-zinc-200 shadow-2xl max-w-lg w-full p-6 sm:p-8 relative space-y-5">
            
            <button
              type="button"
              onClick={() => setShowPwaModal(false)}
              className="absolute top-5 right-5 p-2 rounded-full text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 transition-colors"
            >
              <X className="w-5 h-5" />
            </button>

            <div className="space-y-2">
              <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-semibold border border-purple-200">
                <Sparkles className="w-3.5 h-3.5" />
                <span>Progressive Web App (PWA)</span>
              </div>
              <h3 className="font-display text-2xl font-bold text-zinc-950">
                The App Store without the friction.
              </h3>
              <p className="text-zinc-600 text-sm leading-relaxed">
                A Progressive Web App gives you 100% native phone performance directly through your browser without downloading 500MB from an app store.
              </p>
            </div>

            <div className="space-y-3 pt-2">
              
              <div className="flex items-start gap-3 p-3 bg-zinc-50 rounded-2xl border border-zinc-200/80">
                <div className="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                  <Zap className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-sm font-bold text-zinc-950">Instant Sub-50ms Launch</div>
                  <div className="text-xs text-zinc-600">Saved directly to your home screen. Opens full-screen with zero browser address bars.</div>
                </div>
              </div>

              <div className="flex items-start gap-3 p-3 bg-zinc-50 rounded-2xl border border-zinc-200/80">
                <div className="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0 mt-0.5">
                  <ShieldCheck className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-sm font-bold text-zinc-950">Offline & Low-Bandwidth Ready</div>
                  <div className="text-xs text-zinc-600">Cached locally so you can draft invoices and view client numbers even in basements.</div>
                </div>
              </div>

              <div className="flex items-start gap-3 p-3 bg-zinc-50 rounded-2xl border border-zinc-200/80">
                <div className="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                  <Layers className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-sm font-bold text-zinc-950">Automatic Zero-Downtime Updates</div>
                  <div className="text-xs text-zinc-600">Never wait for App Store approvals. You always have the newest AI models and tax features.</div>
                </div>
              </div>

            </div>

            <div className="pt-2">
              <button
                type="button"
                onClick={() => setShowPwaModal(false)}
                className="w-full py-3 bg-zinc-950 text-white rounded-xl text-sm font-semibold hover:bg-zinc-800 transition-colors shadow-2xs"
              >
                Got it, close
              </button>
            </div>

          </div>
        </div>
      )}

    </section>
  );
}




