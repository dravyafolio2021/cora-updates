'use client';

import React from 'react';
import Image from 'next/image';
import {
  Sparkles,
  ArrowRight,
  Play,
  BookOpen,
  Users,
  GraduationCap,
} from 'lucide-react';

export function CommunityStoriesSection() {
  return (
    <section className="py-20 sm:py-28 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100">
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header (Matching Reference 1:1) ── */}
        <div className="max-w-[800px] mx-auto text-center mb-14 sm:mb-18">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[48px] font-bold text-zinc-950 leading-[1.1] tracking-[-0.03em]">
            Learn more about <br />
            service business engineering
          </h2>
        </div>

        {/* ── Clay-Style Bento Grid ── */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-6 max-w-[1200px] mx-auto">
          
          {/* Card 1: Summit / Masterclass (Col 1-4, Row 1-2) */}
          <div className="md:col-span-4 bg-[#1E0B2B] text-white rounded-[32px] p-7 flex flex-col justify-between relative overflow-hidden group min-h-[380px] shadow-sm">
            <div className="space-y-4 relative z-10">
              <span className="text-[11px] font-bold uppercase tracking-wider text-purple-300">
                ANNUAL MASTERCLASS
              </span>
              <div className="font-display text-4xl sm:text-5xl font-black tracking-tight text-white/90">
                CORA <br />
                SUMMIT
              </div>
            </div>

            <div className="space-y-3 relative z-10 pt-8">
              <h3 className="font-bold text-lg text-white leading-snug">
                The modern creative business conference returns in 2026
              </h3>
              <a
                href="/about"
                className="inline-flex items-center gap-1.5 text-xs font-bold text-purple-200 hover:text-white transition-colors"
              >
                <span>Get tickets</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </a>
            </div>
          </div>

          {/* Card 2: Academy / University Guide (Col 5-12, Row 1) */}
          <div className="md:col-span-8 bg-[#F8F8F9] rounded-[32px] p-7 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6 border border-zinc-200/80 shadow-2xs group hover:border-zinc-300 transition-all">
            <div className="space-y-3 max-w-[380px]">
              <h3 className="font-display text-2xl font-bold text-zinc-950">
                Get started with Cora Academy
              </h3>
              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                Find the video lessons &amp; workflows that help you level up your rate cards, automate 18% GST math, and build confidently.
              </p>
              <div className="pt-2">
                <a
                  href="/tools"
                  className="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-950 hover:text-indigo-600 transition-colors"
                >
                  <span>Go to Academy</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </a>
              </div>
            </div>

            <div className="relative w-full sm:w-[220px] h-[140px] rounded-2xl overflow-hidden bg-white border border-zinc-200/80 shrink-0 shadow-inner">
              <Image
                src="/images/usecase_agency_team.jpg"
                alt="Cora Academy"
                fill
                className="object-cover group-hover:scale-105 transition-transform duration-500"
              />
            </div>
          </div>

          {/* Card 3: Livestream Case Study (Col 5-8, Row 2) */}
          <div className="md:col-span-4 bg-zinc-900 text-white rounded-[32px] p-6 relative overflow-hidden flex flex-col justify-between min-h-[220px] group shadow-sm">
            <Image
              src="/images/usecase_commercial_studio.jpg"
              alt="Studio Livestream"
              fill
              className="object-cover opacity-35 group-hover:scale-105 transition-transform duration-500 -z-0"
            />
            <div className="relative z-10 flex items-center justify-between">
              <span className="text-[10px] font-bold uppercase tracking-wider bg-rose-600 text-white px-2 py-0.5 rounded-full">
                LIVESTREAM
              </span>
              <div className="w-7 h-7 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center">
                <Play className="w-3 h-3 text-white fill-white" />
              </div>
            </div>

            <div className="relative z-10 space-y-2 pt-4">
              <h4 className="font-bold text-sm sm:text-base text-white leading-snug">
                How Top Studios Scale to ₹25L/mo Retainers with ₹0 Tax Errors
              </h4>
              <a
                href="/use-cases"
                className="inline-flex items-center gap-1 text-xs font-semibold text-zinc-300 hover:text-white"
              >
                <span>Watch now</span>
                <ArrowRight className="w-3 h-3" />
              </a>
            </div>
          </div>

          {/* Card 4: Community Spotlight (Col 9-12, Row 2) */}
          <div className="md:col-span-4 bg-[#F8F8F9] rounded-[32px] p-6 border border-zinc-200/80 flex flex-col justify-between min-h-[220px] group hover:border-zinc-300 transition-all">
            <div className="space-y-2">
              <span className="text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                COMMUNITY STORY
              </span>
              <h4 className="font-bold text-base text-zinc-950 leading-snug">
                How Priya built an automated commercial wedding photography agency in Mumbai
              </h4>
            </div>

            <div className="pt-4 flex items-center justify-between">
              <div className="flex items-center gap-2.5">
                <div className="relative w-8 h-8 rounded-full overflow-hidden border border-zinc-200">
                  <Image
                    src="/images/agent_card_calendar.jpg"
                    alt="Priya Founder"
                    fill
                    className="object-cover"
                  />
                </div>
                <div className="text-[11px] font-bold text-zinc-800">Priya Sharma</div>
              </div>

              <a
                href="/use-cases"
                className="inline-flex items-center gap-1 text-xs font-bold text-zinc-950 hover:text-indigo-600"
              >
                <span>Read story</span>
                <ArrowRight className="w-3 h-3" />
              </a>
            </div>
          </div>

        </div>

      </div>
    </section>
  );
}
