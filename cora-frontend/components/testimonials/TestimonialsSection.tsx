'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { Star, ArrowRight, MessageSquare } from 'lucide-react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

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
      <div className="absolute inset-0 z-0 pointer-events-none select-none opacity-40">
        <Image
          src="/images/cora_hero_landscape.jpg"
          alt="Scenic Atmosphere"
          fill
          sizes="100vw"
          className="object-cover object-[center_60%]"
        />
        <div className="absolute inset-0 bg-gradient-to-b from-[#cae6fc]/70 via-white/80 to-white" />
        <div className="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-white to-transparent" />
      </div>

      <div className="relative z-10 w-full max-w-[1200px] mx-auto px-4 sm:px-6">
        
        {/* Top Header Bar */}
        <div className="review-header-anim flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 sm:mb-16">
          <div className="space-y-3">
            <span className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono inline-block">
              TESTIMONIALS
            </span>
            <h2 className="font-display text-3xl xs:text-4xl sm:text-[44px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em]">
              What Indian founders are saying.
            </h2>
            <p className="text-zinc-600 text-sm sm:text-base font-normal">
              Stories from service businesses and solo founders running on Cora.
            </p>
          </div>

          <div className="shrink-0">
            <a
              href="https://app.heycora.in/workspace/login?source=reviews_section"
              onClick={() => trackEvent('cta_click', { section: 'reviews_section' })}
              className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
            >
              <span>Start free today</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
            </a>
          </div>
        </div>

        {/* 2 Clean Unembellished Testimonial Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
          
          {/* Card 1 */}
          <div className="rounded-[28px] p-7 sm:p-8 bg-white/95 backdrop-blur-md border border-zinc-200/80 shadow-2xs flex flex-col justify-between">
            <div>
              <div className="flex items-center gap-1 mb-4">
                {[...Array(5)].map((_, i) => (
                  <Star key={i} className="w-4 h-4 fill-amber-400 text-amber-400" />
                ))}
              </div>

              <p className="text-zinc-800 text-sm sm:text-base leading-relaxed mb-6 italic">
                &ldquo;Managing client inquiries, billing, and repeat booking reminders used to take up my whole evening. With Cora, I just type what I need done in chat and it gets handled in seconds.&rdquo;
              </p>
            </div>

            <div className="pt-4 border-t border-zinc-100 flex items-center gap-3">
              <div className="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center font-bold text-xs">
                S
              </div>
              <div>
                <h4 className="text-xs sm:text-sm font-bold text-zinc-950 leading-snug">
                  [Real testimonial to be added]
                </h4>
                <p className="text-[11px] text-zinc-500 leading-tight">
                  Service Business Founder &bull; Mumbai
                </p>
              </div>
            </div>
          </div>

          {/* Card 2 */}
          <div className="rounded-[28px] p-7 sm:p-8 bg-white/95 backdrop-blur-md border border-zinc-200/80 shadow-2xs flex flex-col justify-between">
            <div>
              <div className="flex items-center gap-1 mb-4">
                {[...Array(5)].map((_, i) => (
                  <Star key={i} className="w-4 h-4 fill-amber-400 text-amber-400" />
                ))}
              </div>

              <p className="text-zinc-800 text-sm sm:text-base leading-relaxed mb-6 italic">
                &ldquo;Creating 18% GST invoices and sharing payment links directly on WhatsApp has made our fee collection so much faster. Clients pay instantly via UPI without delays.&rdquo;
              </p>
            </div>

            <div className="pt-4 border-t border-zinc-100 flex items-center gap-3">
              <div className="w-10 h-10 rounded-full bg-zinc-900 text-white flex items-center justify-center font-bold text-xs">
                C
              </div>
              <div>
                <h4 className="text-xs sm:text-sm font-bold text-zinc-950 leading-snug">
                  [Real testimonial to be added]
                </h4>
                <p className="text-[11px] text-zinc-500 leading-tight">
                  Agency Owner &bull; Bangalore
                </p>
              </div>
            </div>
          </div>

        </div>

      </div>
    </section>
  );
}
