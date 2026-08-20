'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { Star, ArrowRight, Heart, Users } from 'lucide-react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

const TESTIMONIALS = [
  {
    quote:
      'Cora completely eliminated our spreadsheet chaos. We went from spending 3 hours on 18% GST invoices and hold agreements to doing it all in under 2 minutes.',
    name: 'Karan Makker',
    role: 'Founder & Principal',
    studio: 'Makker Studio (Mumbai)',
    avatar: 'KM',
    avatarBg: 'bg-zinc-950 text-white',
  },
  {
    quote:
      'The automated WhatsApp call-sheet dispatch and mobile digital signatures gave our commercial production agency an enterprise polish our corporate clients love.',
    name: 'Aarav Singhania',
    role: 'Creative Director',
    studio: 'Lumos Media (Delhi)',
    avatar: 'AS',
    avatarBg: 'bg-emerald-900 text-white',
  },
  {
    quote:
      'Switching between Claude 3.5 Sonnet and Gemini 2.0 without managing separate API keys is insane value. It drafts our entire property listing copy in 5 seconds.',
    name: 'Priya Sharma',
    role: 'Principal Broker',
    studio: 'SkyLine Real Estate (Bangalore)',
    avatar: 'PS',
    avatarBg: 'bg-purple-900 text-white',
  },
  {
    quote:
      'Tracking retainers, hold dates, and crew payouts across 14 simultaneous commercial shoots used to give me anxiety. Cora made our full pipeline completely autonomous.',
    name: 'Vikramaditya Roy',
    role: 'Production Head',
    studio: 'Monochrome Studios (Goa)',
    avatar: 'VR',
    avatarBg: 'bg-blue-900 text-white',
  },
  {
    quote:
      'The automated B2B GST tax math breakdown alone saved our accountants 15+ hours during quarterly filing. The best investment for our commercial photography studio.',
    name: 'Rhea Kapoor',
    role: 'Lead Commercial Photographer',
    studio: 'Studio Aperture (Pune)',
    avatar: 'RK',
    avatarBg: 'bg-amber-900 text-white',
  },
  {
    quote:
      'Issuing legally binding SHA-256 e-signed agreements right from my phone while on set is a superpower. Clients sign in seconds on mobile with zero friction.',
    name: 'Dev Malhotra',
    role: 'Cinematographer & Director',
    studio: 'FrameCraft Productions (Hyderabad)',
    avatar: 'DM',
    avatarBg: 'bg-rose-900 text-white',
  },
];

export function TestimonialsSection() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.review-header-anim',
        { y: 25, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          stagger: 0.1,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 80%',
          },
        }
      );
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  return (
    <section
      ref={sectionRef}
      id="reviews"
      className="relative w-full overflow-hidden bg-gradient-to-b from-[#cae6fc]/60 via-[#e4f2fe]/40 to-white py-20 sm:py-28 border-b border-zinc-200/60"
    >
      {/* ── Background Scenic Wildflower Landscape with Smooth Fade ── */}
      <div className="absolute inset-0 z-0 pointer-events-none select-none opacity-40">
        <Image
          src="/images/cora_hero_landscape.jpg"
          alt="Scenic Landscape Atmosphere"
          fill
          sizes="100vw"
          className="object-cover object-[center_60%]"
        />
        {/* Soft overlay gradients */}
        <div className="absolute inset-0 bg-gradient-to-b from-[#cae6fc]/70 via-white/80 to-white" />
        <div className="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-white to-transparent" />
      </div>

      <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── Top Header Bar (Matching Framer Layout) ── */}
        <div className="review-header-anim flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 sm:mb-16">
          
          {/* Left: Heading & Metric Ticker */}
          <div className="space-y-3.5">
            <h2 className="font-display text-3xl xs:text-4xl sm:text-[44px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em]">
              What founders say about the platform
            </h2>

            {/* Sub-bar with 3 inline stats separated by vertical divider lines */}
            <div className="flex items-center flex-wrap gap-3 sm:gap-4 text-xs sm:text-[13px] font-semibold text-zinc-700">
              <div className="flex items-center gap-1.5 text-zinc-900">
                <Star className="w-4 h-4 fill-amber-400 text-amber-400" />
                <span>4.9/5 Rating</span>
              </div>

              <div className="w-px h-3.5 bg-zinc-300 hidden xs:block" />

              <div className="flex items-center gap-1.5 text-zinc-900">
                <Heart className="w-3.5 h-3.5 text-rose-500 fill-rose-500" />
                <span>75+ Studio Stories</span>
              </div>

              <div className="w-px h-3.5 bg-zinc-300 hidden xs:block" />

              <div className="flex items-center gap-1.5 text-zinc-900">
                <Users className="w-3.5 h-3.5 text-emerald-600" />
                <span>1,200+ Growth Community</span>
              </div>
            </div>
          </div>

          {/* Right: Get Started Today Button */}
          <div className="shrink-0">
            <a
              href="https://app.heycora.in/workspace/login?source=reviews_section"
              onClick={() => trackEvent('cta_click', { section: 'reviews_section' })}
              className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm border border-zinc-800 group"
            >
              <span>Get started for Free</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
            </a>
          </div>

        </div>

      </div>

      {/* ── Continuous Horizontal Auto-Scrolling Ticker / Marquee ── */}
      <div className="relative w-full overflow-hidden py-4">
        
        {/* Soft edge fade masks */}
        <div className="absolute left-0 inset-y-0 w-12 sm:w-28 bg-gradient-to-r from-white/90 via-white/40 to-transparent z-10 pointer-events-none" />
        <div className="absolute right-0 inset-y-0 w-12 sm:w-28 bg-gradient-to-l from-white/90 via-white/40 to-transparent z-10 pointer-events-none" />

        <div className="flex gap-6 w-max animate-review-scroll hover:[animation-play-state:paused] px-4">
          
          {/* Double list for seamless infinite loop */}
          {[...TESTIMONIALS, ...TESTIMONIALS].map((t, idx) => (
            <div
              key={idx}
              className="w-[320px] sm:w-[380px] rounded-[30px] p-7 sm:p-8 bg-white/95 backdrop-blur-md border border-zinc-200/80 shadow-[0px_12px_32px_rgba(0,0,0,0.06)] flex flex-col justify-between hover:shadow-[0px_18px_40px_rgba(0,0,0,0.10)] hover:-translate-y-1 transition-all duration-300 shrink-0 select-none"
            >
              <div>
                {/* 5 Golden Stars */}
                <div className="flex items-center gap-1 mb-4">
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} className="w-4 h-4 fill-amber-400 text-amber-400" />
                  ))}
                </div>

                {/* Quotation text */}
                <p className="text-zinc-800 text-xs sm:text-[13.5px] leading-relaxed font-normal mb-6">
                  "{t.quote}"
                </p>
              </div>

              {/* Bottom Author Credentials */}
              <div className="pt-4 border-t border-zinc-100 flex items-center gap-3">
                <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-2xs ${t.avatarBg} shrink-0`}>
                  {t.avatar}
                </div>
                <div>
                  <h4 className="text-xs sm:text-sm font-bold text-zinc-950 tracking-tight leading-snug">
                    {t.name}
                  </h4>
                  <p className="text-[11px] text-zinc-500 leading-tight">
                    {t.role} &bull; <span className="text-zinc-700 font-medium">{t.studio}</span>
                  </p>
                </div>
              </div>
            </div>
          ))}

        </div>

      </div>
    </section>
  );
}
