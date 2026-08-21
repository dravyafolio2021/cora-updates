'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { ArrowRight, ChevronRight, Zap, Lock } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';
import { HeroAIInput } from './HeroAIInput';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function Hero() {
  const heroRef = useRef<HTMLElement>(null);
  const bgRef = useRef<HTMLDivElement>(null);
  const contentRef = useRef<HTMLDivElement>(null);
  const badgeRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      // 1. Initial Smooth Staggered Entrance Animation
      const tl = gsap.timeline({ defaults: { ease: 'power3.out', duration: 1.0 } });
      
      tl.fromTo(
        '.hero-anim-item',
        { y: 20, opacity: 0 },
        { y: 0, opacity: 1, stagger: 0.08, delay: 0.05 }
      );

      // 2. Continuous Subtle Floating Animation on 3D AI Badge
      if (badgeRef.current) {
        gsap.to(badgeRef.current, {
          y: -4,
          rotation: -1.2,
          duration: 2.8,
          repeat: -1,
          yoyo: true,
          ease: 'sine.inOut',
        });
      }

      // 3. Smooth Parallax Scrolling on Background
      if (heroRef.current && bgRef.current) {
        gsap.to(bgRef.current, {
          yPercent: 10,
          ease: 'none',
          scrollTrigger: {
            trigger: heroRef.current,
            start: 'top top',
            end: 'bottom top',
            scrub: 1.2,
          },
        });
      }
    }, heroRef);

    return () => ctx.revert();
  }, []);

  const handleGoogleSignup = () => {
    trackEvent('hero_google_signup_clicked', { source: 'hero_google_cta' });
  };

  const handleFounderClick = () => {
    trackEvent('hero_connect_founder_clicked', { source: 'hero_founder_link' });
  };

  return (
    <section
      ref={heroRef}
      className="relative w-full overflow-hidden bg-gradient-to-b from-[#5caae8] via-[#cae6fc] to-white pt-28 sm:pt-36 pb-16 sm:pb-28"
    >
      {/* ── Background Scenic Landscape with GSAP Parallax ─────────── */}
      <div
        ref={bgRef}
        className="absolute inset-0 -top-10 -bottom-24 z-0 pointer-events-none select-none will-change-transform"
      >
        <Image
          src="/images/cora_hero_landscape.jpg"
          alt="Cora Vivid Sky & Meadow Hills Landscape"
          fill
          priority
          className="object-cover object-[center_20%] sm:object-top"
          sizes="100vw"
        />
        
        {/* Linear Gradient Overlay */}
        <div
          className="absolute inset-0 pointer-events-none"
          style={{
            background: 'linear-gradient(180deg, rgba(92, 170, 232, 0.35) 0%, rgba(255, 255, 255, 0.25) 40%, rgba(152, 168, 105, 0.35) 68%, rgba(255, 255, 255, 0.95) 90%, #ffffff 100%)',
          }}
        />

        {/* Tall Soft Multi-Stop Pure White Fade */}
        <div className="absolute inset-x-0 bottom-0 h-[60%] sm:h-[55%] bg-gradient-to-t from-white via-white/80 via-white/40 to-transparent pointer-events-none" />
      </div>

      {/* Front-Facing Bottom Fade Veil */}
      <div className="absolute inset-x-0 bottom-0 h-40 sm:h-64 bg-gradient-to-t from-white via-white/95 to-transparent pointer-events-none z-[5]" />

      <div
        ref={contentRef}
        className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6 flex flex-col items-center text-center gap-6 sm:gap-8"
      >
        {/* 1. Top Announcement Pill */}
        <div className="hero-anim-item">
          <a
            href="https://app.heycora.in/workspace/login?source=hero_pill"
            className="inline-flex items-center gap-2 px-3 sm:px-3.5 py-1.5 bg-white/90 backdrop-blur-md rounded-full shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] outline outline-1 outline-offset-[-1px] outline-white/80 hover:bg-white hover:outline-zinc-300 transition-all hover:-translate-y-0.5"
          >
            <div className="px-2 py-0.5 bg-zinc-950 rounded-full inline-flex items-center justify-center">
              <span className="text-white text-[11px] sm:text-xs font-bold font-sans uppercase leading-4 tracking-wide">
                BETA 2.4
              </span>
            </div>
            <span className="text-zinc-800 text-[11px] sm:text-xs font-semibold font-sans leading-4">
              Multi-Model AI Workflows Live
            </span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-800 stroke-[2]" />
          </a>
        </div>

        {/* 2. Main Display Headline & Subtitle */}
        <div className="hero-anim-item flex flex-col items-center max-w-[940px] gap-3 sm:gap-4">
          
          {/* Headline */}
          <h1 className="font-display text-[2.1rem] xs:text-[2.45rem] sm:text-5xl md:text-[58px] font-semibold text-zinc-950 leading-[1.12] sm:leading-[1.10] md:leading-[68px] tracking-[-0.035em] flex flex-col items-center justify-center gap-1.5 sm:gap-2">
            <div className="inline-flex items-center justify-center flex-wrap gap-2 sm:gap-3">
              <span>Your</span>
              <div
                ref={badgeRef}
                className="relative inline-flex items-center justify-center w-11 h-11 xs:w-12 xs:h-12 sm:w-15 sm:h-15 md:w-[64px] md:h-[64px] shrink-0 self-center transition-transform duration-300 hover:scale-105"
              >
                <Image
                  src="/images/cora_3d_ai_badge.png"
                  alt="3D AI Badge"
                  fill
                  className="object-contain rounded-xl sm:rounded-2xl shadow-[0px_6px_14px_rgba(0,0,0,0.12)]"
                />
              </div>
              <span>Cofounder with</span>
            </div>
            <div className="text-center px-1">
              Website, Forms &amp; Finance....
            </div>
          </h1>

          {/* Subtitle */}
          <p className="w-full max-w-[620px] text-center text-zinc-800 text-sm sm:text-base md:text-lg font-normal font-sans leading-relaxed sm:leading-7 px-2">
            Automate client funnels, document signing, and frontier multi-model AI agent routing in one hyper-focused workspace.
          </p>

        </div>

        {/* 3. Action CTAs & Trust Badges */}
        <div className="hero-anim-item flex flex-col items-center gap-4 sm:gap-6 w-full">
          
          {/* Dual Buttons */}
          <div className="flex items-center justify-center gap-3 sm:gap-4 flex-col sm:flex-row w-full max-w-[340px] sm:max-w-none">
            
            {/* Primary CTA: Sign up with Google */}
            <a
              href="https://app.heycora.in/workspace/login?source=hero_google_cta"
              onClick={handleGoogleSignup}
              className="px-6 sm:px-10 py-3 sm:py-2.5 bg-zinc-950 text-white rounded-xl shadow-[0px_1px_2px_0px_rgba(0,0,0,0.08)] shadow-[0px_4px_14px_-2px_rgba(0,0,0,0.16)] outline outline-1 outline-offset-[-1px] outline-white/15 flex items-center justify-center gap-2.5 hover:bg-zinc-800 transition-all hover:-translate-y-0.5 active:translate-y-0 w-full sm:w-auto shrink-0"
            >
              {/* Official Google Icon */}
              <div className="w-5 h-5 flex items-center justify-center shrink-0">
                <svg className="w-4 h-4" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                  <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.36 24 12 24z"/>
                  <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.98 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                  <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                </svg>
              </div>
              <span className="text-white text-sm sm:text-base font-semibold font-sans leading-6">
                Sign up with Google
              </span>
            </a>

            {/* Secondary CTA: Connect with founder */}
            <a
              href="mailto:dravya.bansal@heycora.in?subject=Connecting%20with%20Cora%20Founder"
              onClick={handleFounderClick}
              className="px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl flex items-center justify-center gap-1.5 text-zinc-950 text-sm sm:text-base font-medium font-sans leading-6 hover:bg-white/40 transition-colors"
            >
              <span>Connect with founder</span>
              <ChevronRight className="w-4 h-4 text-zinc-950" />
            </a>

          </div>

          {/* Trust Badges Row */}
          <div className="w-full max-w-[485px] relative flex flex-wrap items-center justify-center gap-2.5 sm:gap-5 px-2">
            
            {/* Scribbled "free forever" */}
            <div className="relative inline-flex items-center rotate-[-1.5deg] px-2 py-0.5">
              <span className="font-scribble text-xl sm:text-2xl font-bold text-zinc-950 leading-5 sm:leading-6 relative z-10 flex items-center gap-1">
                <span className="text-sm sm:text-base">✦</span>
                <span>free forever</span>
              </span>
              <svg
                className="absolute inset-0 w-full h-full text-violet-400 stroke-current fill-none pointer-events-none -rotate-1 scale-110"
                viewBox="0 0 130 36"
                preserveAspectRatio="none"
              >
                <path
                  d="M10,18 C18,6 112,4 122,16 C128,24 98,32 58,32 C24,32 6,26 10,18 Z"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
              </svg>
            </div>

            <div className="w-px h-3.5 bg-zinc-300 hidden sm:block" />

            <div className="flex items-center gap-1.5">
              <Zap className="w-3.5 h-3.5 text-zinc-950 opacity-90" />
              <span className="text-zinc-700 text-xs sm:text-sm font-medium font-sans leading-5">
                1-click setup
              </span>
            </div>

            <div className="w-px h-3.5 bg-zinc-300 hidden sm:block" />

            <div className="flex items-center gap-1.5">
              <Lock className="w-3.5 h-3.5 text-zinc-950 opacity-90" />
              <span className="text-zinc-700 text-xs sm:text-sm font-medium font-sans leading-5">
                No credit card needed
              </span>
            </div>

          </div>

        </div>

        {/* 4. Interactive AI Input Product Copilot */}
        <div className="hero-anim-item w-full flex justify-center">
          <HeroAIInput />
        </div>

      </div>
    </section>
  );
}
