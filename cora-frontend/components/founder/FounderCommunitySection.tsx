'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { ArrowUpRight, Users, Sparkles, CheckCircle2, ArrowRight } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function FounderCommunitySection() {
  const sectionRef = useRef<HTMLElement>(null);
  const showcaseCardRef = useRef<HTMLDivElement>(null);
  const founderImageRef = useRef<HTMLDivElement>(null);
  const badgeLeftRef = useRef<HTMLDivElement>(null);
  const badgeRightRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.founder-anim-item',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          stagger: 0.12,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 78%',
          },
        }
      );

      gsap.fromTo(
        showcaseCardRef.current,
        { y: 45, opacity: 0, scale: 0.98 },
        {
          y: 0,
          opacity: 1,
          scale: 1,
          duration: 0.9,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: showcaseCardRef.current,
            start: 'top 80%',
          },
        }
      );

      if (founderImageRef.current) {
        gsap.fromTo(
          founderImageRef.current,
          { y: 40, opacity: 0, scale: 0.95 },
          {
            y: 0,
            opacity: 1,
            scale: 1,
            duration: 1,
            delay: 0.15,
            ease: 'power3.out',
            scrollTrigger: {
              trigger: showcaseCardRef.current,
              start: 'top 75%',
            },
          }
        );
      }

      if (badgeLeftRef.current) {
        gsap.to(badgeLeftRef.current, {
          y: -8,
          duration: 3.6,
          repeat: -1,
          yoyo: true,
          ease: 'sine.inOut',
        });
      }
      if (badgeRightRef.current) {
        gsap.to(badgeRightRef.current, {
          y: 8,
          duration: 4.0,
          repeat: -1,
          yoyo: true,
          ease: 'sine.inOut',
          delay: 0.4,
        });
      }
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  return (
    <section
      ref={sectionRef}
      id="community"
      className="py-20 sm:py-28 relative z-10 bg-white overflow-hidden border-b border-zinc-200/60"
    >
      <div className="w-full max-w-[1200px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header ── */}
        <div className="founder-anim-item text-center max-w-[780px] mx-auto mb-14 sm:mb-16">
          <span className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono mb-2 inline-block">
            FOUNDER NOTE
          </span>
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
            Built by a founder who was tired of running 8 different tabs.
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[640px] mx-auto">
            We built Cora so you can manage your daily operations simply by talking to an assistant that actually knows your business.
          </p>
        </div>

        {/* ── 3-Column Narrative Framework ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 mb-14 sm:mb-18">
          
          {/* Column 1 */}
          <div className="founder-anim-item space-y-2.5">
            <h3 className="font-display text-xs sm:text-[13px] font-bold uppercase tracking-wider text-zinc-950">
              Built For Busy Founders
            </h3>
            <p className="text-zinc-600 text-xs sm:text-[13.5px] leading-relaxed font-normal">
              Running a service business means wearing 5 hats every day. Cora gives you back hours lost to spreadsheet data entry and endless app switching.
            </p>
          </div>

          {/* Column 2 */}
          <div className="founder-anim-item space-y-2.5">
            <h3 className="font-display text-xs sm:text-[13px] font-bold uppercase tracking-wider text-zinc-950">
              Built In Public
            </h3>
            <p className="text-zinc-600 text-xs sm:text-[13.5px] leading-relaxed font-normal">
              We build directly alongside local service business founders who share weekly feedback to refine workflows for real Indian businesses.
            </p>
          </div>

          {/* Column 3 */}
          <div className="founder-anim-item space-y-2.5">
            <h3 className="font-display text-xs sm:text-[13px] font-bold uppercase tracking-wider text-zinc-950">
              Zero Navigation Friction
            </h3>
            <p className="text-zinc-600 text-xs sm:text-[13.5px] leading-relaxed font-normal">
              Everything happens in one conversational input. Create invoices, check cash flow, and reply to client inquiries without clicking 20 menus.
            </p>
          </div>

        </div>

        {/* ── High-Impact Obsidian Founder Presentation Stage ── */}
        <div
          ref={showcaseCardRef}
          className="relative w-full rounded-[36px] overflow-hidden bg-[#0A0D10] border border-zinc-800 shadow-2xs"
        >
          <div
            className="absolute inset-0 pointer-events-none"
            style={{
              background:
                'radial-gradient(ellipse at 50% 30%, rgba(16, 185, 129, 0.14) 0%, transparent 65%), radial-gradient(ellipse at 20% 80%, rgba(59, 130, 246, 0.08) 0%, transparent 50%)',
            }}
          />

          <div className="relative z-10 flex flex-col items-center pt-10 sm:pt-14 px-6 sm:px-12">
            
            {/* Top Founder Identity Pill */}
            <div className="inline-flex items-center gap-2 px-4 py-1.5 bg-zinc-900/90 backdrop-blur-md rounded-full border border-zinc-700/80 shadow-xs mb-6">
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
              <span className="font-display text-xs font-semibold text-zinc-200 tracking-tight">
                Founder &bull; Dravya Bansal
              </span>
            </div>

            {/* Stage */}
            <div className="relative w-full max-w-[940px] flex items-end justify-center min-h-[380px] sm:min-h-[460px] md:min-h-[500px]">
              
              {/* Floating Badge Left */}
              <div
                ref={badgeLeftRef}
                className="hidden md:flex absolute left-2 lg:left-6 top-16 z-20 bg-zinc-900/90 backdrop-blur-xl rounded-2xl p-4 sm:p-5 border border-zinc-700/80 shadow-2xs flex-col text-left max-w-[240px] transition-transform duration-300 hover:scale-105"
              >
                <div className="flex items-center gap-2 text-xs font-bold text-white mb-1">
                  <div className="w-6 h-6 rounded-lg bg-purple-900/60 text-purple-300 flex items-center justify-center border border-purple-700/50">
                    <Sparkles className="w-3.5 h-3.5" />
                  </div>
                  <span>Product Vision</span>
                </div>
                <p className="text-zinc-400 text-[11px] leading-relaxed">
                  Building simple AI tools tailored for Indian service businesses and solopreneurs.
                </p>
                <div className="mt-2.5 pt-2 border-t border-zinc-800 flex items-center gap-1.5 text-[10px] font-semibold text-purple-300">
                  <span>Cora Studio</span>
                  <span>&bull;</span>
                  <span>Build in Public</span>
                </div>
              </div>

              {/* Central Founder Cutout */}
              <div
                ref={founderImageRef}
                className="relative z-10 w-[320px] sm:w-[400px] md:w-[460px] h-[380px] sm:h-[460px] md:h-[500px] flex items-end justify-center filter drop-shadow-[0px_16px_36px_rgba(0,0,0,0.6)] select-none"
              >
                <Image
                  src="/images/dravya_bansal_cutout_clean.png"
                  alt="Dravya Bansal — Founder of Cora"
                  fill
                  sizes="(max-width: 768px) 400px, 460px"
                  className="object-contain object-bottom"
                  priority
                />
              </div>

              {/* Floating Badge Right */}
              <div
                ref={badgeRightRef}
                className="hidden md:flex absolute right-2 lg:right-6 top-24 z-20 bg-zinc-900/90 backdrop-blur-xl rounded-2xl p-4 sm:p-5 border border-zinc-700/80 shadow-2xs flex-col text-left max-w-[250px] transition-transform duration-300 hover:scale-105"
              >
                <div className="flex items-center gap-2 text-xs font-bold text-emerald-400 mb-1">
                  <div className="w-6 h-6 rounded-lg bg-emerald-950 text-emerald-400 flex items-center justify-center border border-emerald-700/50">
                    <CheckCircle2 className="w-3.5 h-3.5" />
                  </div>
                  <span>Indian Commerce</span>
                </div>
                <p className="text-zinc-400 text-[11px] leading-relaxed">
                  18% GST calculations, instant UPI QR links, and WhatsApp sharing built in.
                </p>
                <div className="mt-2.5 pt-2 border-t border-zinc-800 flex items-center gap-1.5 text-[10px] font-semibold text-emerald-400">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
                  <span>Free Forever Plan</span>
                </div>
              </div>

            </div>

            {/* Bottom Founder Manifesto & Action Bar */}
            <div className="relative z-20 w-full max-w-[820px] pb-10 sm:pb-12 text-center space-y-4 pt-4 border-t border-zinc-800/80">
              
              <div>
                <h4 className="font-display text-2xl sm:text-3xl md:text-[32px] font-bold text-white tracking-tight">
                  Dravya Bansal
                </h4>
                <p className="text-zinc-400 text-xs sm:text-sm font-semibold tracking-wider uppercase mt-0.5">
                  Founder &bull; Cora
                </p>
              </div>

              <blockquote className="text-zinc-300 text-sm sm:text-base font-normal italic leading-relaxed max-w-[680px] mx-auto">
                &ldquo;Running a service business in India shouldn&apos;t mean spending your evenings copying data between WhatsApp, Excel sheets, and billing apps. We built Cora so you can manage your operations simply by talking to an assistant that actually knows your business.&rdquo;
              </blockquote>

              {/* Action Buttons */}
              <div className="flex items-center justify-center flex-wrap gap-3.5 pt-3">
                <a
                  href="https://app.heycora.in/workspace/login?source=founder_stage"
                  onClick={() => trackEvent('founder_cta_click', { action: 'start_free' })}
                  className="inline-flex items-center gap-2 bg-white text-zinc-950 px-5 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm"
                >
                  <Users className="w-4 h-4 text-emerald-600" />
                  <span>Start Free Workspace</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>

                <a
                  href="https://x.com/dravyafolio"
                  target="_blank"
                  rel="noopener noreferrer"
                  onClick={() => trackEvent('founder_social_click', { platform: 'x' })}
                  className="inline-flex items-center gap-1.5 bg-zinc-900 text-zinc-200 border border-zinc-700/80 px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-xs"
                >
                  <span>Follow on X</span>
                  <ArrowUpRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>

                <a
                  href="https://linkedin.com/in/dravyafolio"
                  target="_blank"
                  rel="noopener noreferrer"
                  onClick={() => trackEvent('founder_social_click', { platform: 'linkedin' })}
                  className="inline-flex items-center gap-1.5 bg-zinc-900 text-zinc-200 border border-zinc-700/80 px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-xs"
                >
                  <span>LinkedIn</span>
                  <ArrowUpRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>
              </div>

            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
