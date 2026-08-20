'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { Camera, Film, Building2, User, Sparkles, Clapperboard, Layers } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

const USE_CASES = [
  {
    id: 'commercial_studios',
    title: 'Commercial Photography Studios',
    desc: 'Manage multiple client shoots, crew hold dates, 18% GST tax invoices, and mobile client sign-offs without manual spreadsheets.',
    stat: '+40% Faster',
    statDesc: 'Shoot-to-invoice turnaround time',
    image: '/images/usecase_commercial_studio.jpg',
    category: 'Commercial Studios',
    icon: Camera,
  },
  {
    id: 'real_estate',
    title: 'Real Estate Media Agencies',
    desc: 'Generate AI property descriptions with Claude 3.5 & Gemini, dispatch drone pilots via WhatsApp, and deliver 4K asset links instantly.',
    stat: '3X Faster',
    statDesc: 'Listing copy & drone asset handoff',
    image: '/images/usecase_realestate_agency.jpg',
    category: 'Real Estate Media',
    icon: Building2,
  },
  {
    id: 'production_houses',
    title: 'Film & Video Production Houses',
    desc: 'Lock in high-ticket client retainers with tamper-evident SHA-256 digital signatures, automated call-sheets, and instant UPI milestones.',
    stat: '100% Compliant',
    statDesc: 'Legal e-signatures & B2B GST tax math',
    image: '/images/usecase_production_house.jpg',
    category: 'Production Houses',
    icon: Film,
  },
  {
    id: 'solo_creators',
    title: 'Solo Creators & Lead Photographers',
    desc: 'Run your full creative business from your phone. Send professional client agreements, track invoices, and automate 5-star Google review collection.',
    stat: '20+ Hrs / Wk',
    statDesc: 'Reclaimed founder & admin time',
    image: '/images/usecase_solo_creator.jpg',
    category: 'Solo Creators',
    icon: User,
  },
  {
    id: 'fashion_editorial',
    title: 'Fashion & Editorial Labs',
    desc: 'Coordinate model releases, stylist call times, studio bay rentals, and high-resolution asset approvals in a unified live pipeline.',
    stat: 'Zero Friction',
    statDesc: 'Automated call-sheet dispatch & hold dates',
    image: '/images/bento_crew_camera.jpg',
    category: 'Fashion & Editorial',
    icon: Clapperboard,
  },
  {
    id: 'creative_agencies',
    title: 'Creative Marketing Agencies',
    desc: 'Consolidate 5+ fragmented tools into a single command center. Automate monthly client retainers, proposal drafting, and multi-model AI workflows.',
    stat: '₹1.8L / Mo',
    statDesc: 'Saved in disconnected SaaS subscriptions',
    image: '/images/cora_community_crowd.jpg',
    category: 'Creative Agencies',
    icon: Layers,
  },
];

export function UseCasesSection() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.usecase-anim-item',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          stagger: 0.1,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 78%',
          },
        }
      );
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  return (
    <section
      ref={sectionRef}
      id="use-cases"
      className="py-20 sm:py-28 relative z-10 bg-[#FAFAFA] border-b border-zinc-200/70 overflow-hidden"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header ── */}
        <div className="usecase-anim-item text-center max-w-[780px] mx-auto mb-14 sm:mb-16">
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-800 mb-3.5 shadow-2xs">
            <span className="w-2 h-2 rounded-full bg-emerald-500" />
            <span>Target Workspaces</span>
          </div>
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em]">
            Who this platform is built for
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[620px] mx-auto mt-3">
            Purpose-built workflows for modern commercial studios, agency founders, and high-velocity production teams.
          </p>
        </div>

      </div>

      {/* ── Infinite Horizontal Marquee with 1 Unique Card Per Industry ── */}
      <div className="relative w-full overflow-hidden py-4">
        
        {/* Soft edge fade masks */}
        <div className="absolute left-0 inset-y-0 w-12 sm:w-28 bg-gradient-to-r from-[#FAFAFA] via-[#FAFAFA]/60 to-transparent z-10 pointer-events-none" />
        <div className="absolute right-0 inset-y-0 w-12 sm:w-28 bg-gradient-to-l from-[#FAFAFA] via-[#FAFAFA]/60 to-transparent z-10 pointer-events-none" />

        <div className="flex gap-6 w-max animate-review-scroll hover:[animation-play-state:paused] px-4">
          
          {/* Double list for smooth infinite scrolling */}
          {[...USE_CASES, ...USE_CASES].map((item, idx) => (
            <div
              key={`${item.id}-${idx}`}
              className="w-[320px] sm:w-[380px] h-[440px] sm:h-[480px] rounded-[32px] overflow-hidden relative p-7 sm:p-8 flex flex-col justify-between text-white border border-white/20 shadow-[0px_14px_36px_rgba(0,0,0,0.12)] shrink-0 select-none group"
            >
              {/* Background Image */}
              <Image
                src={item.image}
                alt={item.title}
                fill
                sizes="(max-width: 768px) 320px, 380px"
                className="object-cover object-center group-hover:scale-105 transition-transform duration-700"
              />

              {/* Dark Gradient Overlay */}
              <div className="absolute inset-0 bg-gradient-to-t from-black/95 via-black/50 to-black/30 pointer-events-none" />

              {/* Top Category Badge & Title */}
              <div className="relative z-10 space-y-3">
                <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-md rounded-xl border border-white/30 text-[11px] font-semibold text-white shadow-2xs">
                  {React.createElement(item.icon, { className: 'w-3.5 h-3.5' })}
                  <span>{item.category}</span>
                </div>

                <h3 className="font-display text-xl sm:text-2xl font-bold text-white tracking-tight leading-snug">
                  {item.title}
                </h3>

                <p className="text-zinc-300 text-xs sm:text-[13px] leading-relaxed font-normal">
                  {item.desc}
                </p>
              </div>

              {/* Bottom Glassmorphic Stat Badge */}
              <div className="relative z-10 bg-white/15 backdrop-blur-xl rounded-2xl p-4 border border-white/25 shadow-xs">
                <div className="text-lg sm:text-xl font-bold text-white tracking-tight font-display">
                  {item.stat}
                </div>
                <p className="text-[11px] text-zinc-200 font-medium leading-tight mt-0.5">
                  {item.statDesc}
                </p>
              </div>

            </div>
          ))}

        </div>

      </div>

      {/* ── Bottom Metrics Badges & Founder Quote Bar ── */}
      <div className="w-full max-w-[1080px] mx-auto px-4 sm:px-6 mt-16 sm:mt-20">
        
        {/* 4 Neutral Feature Badges */}
        <div className="usecase-anim-item flex items-center justify-center flex-wrap gap-2.5 sm:gap-3 mb-8">
          {['1,200+ Workspaces', '4.9 Rating', 'Real-time Dispatch', '100% Tax Compliant'].map((badge, i) => (
            <div
              key={i}
              className="px-4 py-2 bg-white rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-800 shadow-2xs"
            >
              {badge}
            </div>
          ))}
        </div>

        {/* Founder Quote */}
        <div className="usecase-anim-item text-center max-w-[680px] mx-auto space-y-4">
          <blockquote className="text-zinc-700 text-sm sm:text-base font-normal italic leading-relaxed">
            "We built Cora to remove administrative guesswork from creative operations and give founders clear, autonomous execution."
          </blockquote>

          <div className="flex items-center justify-center gap-2.5 pt-1">
            <div className="w-8 h-8 rounded-full overflow-hidden border border-zinc-300 shadow-2xs relative">
              <Image
                src="/images/dravya_bansal_black.jpg"
                alt="Dravya Bansal"
                fill
                sizes="32px"
                className="object-cover object-top"
              />
            </div>
            <div className="text-xs font-semibold text-zinc-950 text-left">
              <span>Dravya Bansal</span>
              <span className="text-zinc-500 font-normal ml-1">&bull; Co-founder, Cora</span>
            </div>
          </div>
        </div>

      </div>

    </section>
  );
}
