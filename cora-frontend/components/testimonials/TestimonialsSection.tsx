'use client';

import React from 'react';
import Image from 'next/image';
import { Star, ArrowRight } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

interface Testimonial {
  name: string;
  role: string;
  agency: string;
  avatar: string;
  quote: string;
}

const TESTIMONIALS: Testimonial[] = [
  {
    name: 'Kavya Patel',
    role: 'Founder & Creative Director',
    agency: 'Studio Monolith • Bengaluru',
    avatar: '/images/about_team_ananya.jpg',
    quote: 'Managing client proposals, scope change approvals, and retainer billing used to eat up our entire week. With Cora, our proposal-to-paid cycle dropped from 9 days down to 48 hours.',
  },
  {
    name: 'Aarav Mehta',
    role: 'Managing Partner',
    agency: 'Apex Digital Labs • Mumbai',
    avatar: '/images/about_team_dev.jpg',
    quote: 'Automated 18% GST invoices with SAC code math and sharing payment links directly on WhatsApp completely solved our collection delays. Clients pay milestone UPI without friction.',
  },
  {
    name: 'Vikram Seth',
    role: 'Director of Operations',
    agency: 'Veloce Studio • Delhi NCR',
    avatar: '/images/about_team_aarav.jpg',
    quote: 'The SHA-256 e-sign contracts and automated milestone lock give our enterprise clients immense confidence. Closing ₹5L+ monthly retainers is on complete autopilot.',
  },
  {
    name: 'Pooja Nair',
    role: 'Partner & Client Lead',
    agency: 'Origin Creative • Pune',
    avatar: '/images/about_team_tanya.jpg',
    quote: 'The client portal and deliverable approval flows eliminated messy email chains. Clients review work, approve milestone releases, and settle balances instantly.',
  },
  {
    name: 'Meera Kapoor',
    role: 'Principal & Creative Director',
    agency: 'Atelier Craft • Mumbai',
    avatar: '/images/about_team_meera.jpg',
    quote: 'The retainer calculator and real-time scope tracking prevent unpaid project creep before it starts. We recovered over 14 hours of administrative time every single week.',
  },
  {
    name: 'Rohan Deshmukh',
    role: 'Co-Founder & Design Lead',
    agency: 'PixelCraft Studios • Hyderabad',
    avatar: '/images/agency_female_director.jpg',
    quote: 'Generating professional GST invoices with instant QR codes in 10 seconds has transformed our cashflow. Clients settle milestone balances the same afternoon.',
  },
];

export function TestimonialsSection() {
  return (
    <section
      id="reviews"
      className="relative w-full overflow-hidden bg-white py-16 sm:py-[100px] select-none"
    >
      {/* ── Scenic Meadow & Sky Background with Seamless Gradient Edge Blending ── */}
      <div className="absolute inset-0 z-0 pointer-events-none select-none overflow-hidden">
        <div className="absolute inset-0 [mask-image:linear-gradient(to_bottom,transparent_0%,black_16%,black_84%,transparent_100%)] [-webkit-mask-image:linear-gradient(to_bottom,transparent_0%,black_16%,black_84%,transparent_100%)]">
          <Image
            src="/images/card_bg_meadow_sky.jpg"
            alt="Atmospheric scenic meadow"
            fill
            priority
            className="object-cover object-bottom scale-105"
          />
        </div>
      </div>

      <div className="relative z-10 w-full max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* ── Top Header Bar (Matched to Reference Image) ── */}
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 sm:mb-14">
          
          <div className="space-y-3.5 max-w-2xl">
            <h2 className="font-display text-3xl sm:text-4xl lg:text-[44px] font-bold leading-[1.14] tracking-[-0.03em] text-zinc-950">
              What founders say<br />about the platform
            </h2>
            
            {/* Social Proof Metric Sub-bar */}
            <div className="flex flex-wrap items-center gap-3 text-xs sm:text-sm text-zinc-700 font-medium">
              <span className="inline-flex items-center gap-1.5">
                <span className="text-amber-500">⭐</span> 4.9/5 Rating
              </span>
              <span className="text-zinc-300">|</span>
              <span className="inline-flex items-center gap-1.5">
                <span className="text-rose-500">❤️</span> 75+ Testimonials
              </span>
              <span className="text-zinc-300">|</span>
              <span className="inline-flex items-center gap-1.5">
                <span className="text-blue-600">👥</span> 10K+ Growth community
              </span>
            </div>
          </div>

          {/* Right Action: Cora Design System Standard CTA Button */}
          <div className="flex items-center gap-3 shrink-0">
            <a
              href="https://app.heycora.in/workspace/login?source=reviews_section"
              onClick={() => trackEvent('cta_click', { section: 'reviews_section' })}
              className="inline-flex items-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white px-5 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-sm group cursor-pointer"
            >
              <span>Get started today</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 group-hover:text-white transition-all" />
            </a>
          </div>

        </div>

      </div>

      {/* ── Edge-to-Edge Infinite Loop Automoving Marquee Track ── */}
      <div className="relative z-10 w-full overflow-hidden select-none py-2">
        {/* Continuous Autoscroll Marquee */}
        <div className="flex w-max gap-5 sm:gap-6 animate-marquee hover:[animation-play-state:paused] py-2">
          {/* Duplicate list twice for seamless infinite edge-to-edge looping */}
          {[...TESTIMONIALS, ...TESTIMONIALS].map((t, idx) => (
            <div
              key={idx}
              className="w-[310px] xs:w-[350px] sm:w-[380px] min-h-[250px] sm:min-h-[270px] shrink-0 rounded-[28px] sm:rounded-[32px] bg-white border border-zinc-100/80 p-6 sm:p-7 flex flex-col justify-between shadow-md hover:shadow-lg transition-shadow select-none"
            >
              {/* Top: 5 Gold Rating Stars */}
              <div className="space-y-3.5">
                <div className="flex items-center gap-1">
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} className="w-4 h-4 fill-amber-400 text-amber-400" />
                  ))}
                </div>

                {/* Middle: Testimonial Quote */}
                <p className="text-zinc-800 text-xs sm:text-[14px] leading-relaxed font-normal">
                  &ldquo;{t.quote}&rdquo;
                </p>
              </div>

              {/* Bottom: Founder Profile Row */}
              <div className="flex items-center gap-3.5 pt-4 border-t border-zinc-100 mt-4">
                <div className="w-10 h-10 rounded-full overflow-hidden relative shrink-0 border border-zinc-200/80 shadow-2xs">
                  <Image
                    src={t.avatar}
                    alt={t.name}
                    fill
                    className="object-cover"
                  />
                </div>
                <div className="space-y-0.5">
                  <div className="font-display text-sm sm:text-base font-bold text-zinc-950 tracking-tight leading-tight">
                    {t.name}
                  </div>
                  <div className="text-[11px] sm:text-xs text-zinc-500 font-medium leading-tight">
                    {t.role}
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}


