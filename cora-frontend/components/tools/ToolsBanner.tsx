'use client';

import React from 'react';
import Link from 'next/link';
import { 
  Wrench, 
  Calculator, 
  Code2, 
  FileText, 
  ArrowRight, 
  ArrowUpRight,
  Sparkles
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export function ToolsBanner() {
  return (
    <section className="w-full py-16 sm:py-[100px] relative z-10 bg-zinc-50/70 overflow-hidden">
      
      {/* Subtle Background Radial Ambient Glow */}
      <div 
        aria-hidden="true" 
        className="absolute top-1/2 left-1/4 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-r from-amber-100/30 via-emerald-100/20 to-transparent rounded-full blur-3xl pointer-events-none -z-10" 
      />

      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
          
          {/* ── Left Column: Agency Editorial & Directory Anchor ── */}
          <div className="lg:col-span-5 space-y-4 sm:space-y-5">
            
            {/* Eyebrow Pill */}
            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white rounded-full border border-zinc-200/90 text-xs font-semibold text-zinc-800 shadow-2xs">
              <Wrench className="w-3.5 h-3.5 text-zinc-900" />
              <span className="tracking-wide uppercase text-[11px] font-mono">AGENCY TOOLKIT</span>
              <span className="w-2 h-2 rounded-full bg-emerald-500 ml-0.5" />
            </div>

            {/* Headline */}
            <h3 className="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold tracking-[-0.03em] leading-[1.18] text-zinc-950">
              Free operating tools for agency founders.
            </h3>

            {/* Subtitle */}
            <p className="text-zinc-600 text-sm sm:text-base leading-relaxed max-w-lg">
              No account or credit card required. Model monthly client retainers, calculate 18% GST splits, draft legally binding contracts, and embed lead widgets in seconds.
            </p>

            {/* CTA Link */}
            <div className="pt-1">
              <Link
                href="/tools"
                onClick={() => trackEvent('banner_all_tools_clicked', { source: 'homepage_tools_banner' })}
                className="inline-flex items-center gap-2 text-sm font-semibold text-zinc-950 hover:text-zinc-600 transition-colors group"
              >
                <span>Explore all 30+ free agency tools</span>
                <ArrowRight className="w-4 h-4 text-zinc-950 group-hover:translate-x-1 transition-transform" />
              </Link>
            </div>

          </div>

          {/* ── Right Column: Interactive Agency Tool Cards (7 cols) ── */}
          <div className="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
            
            {/* Tool Card 1: Agency Retainer Calculator */}
            <Link
              href="/tools/retainer-calculator"
              onClick={() => trackEvent('banner_tool_clicked', { tool: 'retainer-calculator' })}
              className="bg-white border border-zinc-200/90 hover:border-zinc-300 p-4 sm:p-5 rounded-2xl flex items-center justify-between gap-3.5 group transition-all duration-200 hover:-translate-y-0.5 shadow-2xs hover:shadow-md"
            >
              <div className="flex items-center gap-3.5 min-w-0">
                <div className="w-11 h-11 rounded-[14px] bg-[#E8F7F0] text-[#167049] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                  <Calculator className="w-5 h-5 stroke-[1.8]" />
                </div>
                <div className="min-w-0">
                  <div className="text-sm font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors">
                    Agency Retainer Calculator
                  </div>
                  <div className="text-xs text-zinc-500 mt-0.5">
                    Model retainers &amp; scope buffers
                  </div>
                </div>
              </div>
              <ArrowUpRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-950 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0" />
            </Link>

            {/* Tool Card 2: 18% GST Invoice Calculator */}
            <Link
              href="/tools/gst-calculator"
              onClick={() => trackEvent('banner_tool_clicked', { tool: 'gst-calculator' })}
              className="bg-white border border-zinc-200/90 hover:border-zinc-300 p-4 sm:p-5 rounded-2xl flex items-center justify-between gap-3.5 group transition-all duration-200 hover:-translate-y-0.5 shadow-2xs hover:shadow-md"
            >
              <div className="flex items-center gap-3.5 min-w-0">
                <div className="w-11 h-11 rounded-[14px] bg-[#F0EEFD] text-[#5D4BD6] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                  <FileText className="w-5 h-5 stroke-[1.8]" />
                </div>
                <div className="min-w-0">
                  <div className="text-sm font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors">
                    18% GST Invoice Math
                  </div>
                  <div className="text-xs text-zinc-500 mt-0.5">
                    Auto CGST, SGST &amp; SAC splits
                  </div>
                </div>
              </div>
              <ArrowUpRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-950 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0" />
            </Link>

            {/* Tool Card 3: Agency SOW & Contract Builder */}
            <Link
              href="/tools/contract-builder"
              onClick={() => trackEvent('banner_tool_clicked', { tool: 'contract-builder' })}
              className="bg-white border border-zinc-200/90 hover:border-zinc-300 p-4 sm:p-5 rounded-2xl flex items-center justify-between gap-3.5 group transition-all duration-200 hover:-translate-y-0.5 shadow-2xs hover:shadow-md"
            >
              <div className="flex items-center gap-3.5 min-w-0">
                <div className="w-11 h-11 rounded-[14px] bg-[#FDEEE8] text-[#C44A2E] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                  <FileText className="w-5 h-5 stroke-[1.8]" />
                </div>
                <div className="min-w-0">
                  <div className="text-sm font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors">
                    Agency Contract Builder
                  </div>
                  <div className="text-xs text-zinc-500 mt-0.5">
                    IT Act binding SOW &amp; NDAs
                  </div>
                </div>
              </div>
              <ArrowUpRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-950 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0" />
            </Link>

            {/* Tool Card 4: 1-Click Client Intake Embed */}
            <Link
              href="/tools/embed-builder"
              onClick={() => trackEvent('banner_tool_clicked', { tool: 'embed-builder' })}
              className="bg-white border border-zinc-200/90 hover:border-zinc-300 p-4 sm:p-5 rounded-2xl flex items-center justify-between gap-3.5 group transition-all duration-200 hover:-translate-y-0.5 shadow-2xs hover:shadow-md"
            >
              <div className="flex items-center gap-3.5 min-w-0">
                <div className="w-11 h-11 rounded-[14px] bg-[#E8F2FD] text-[#2866C5] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                  <Code2 className="w-5 h-5 stroke-[1.8]" />
                </div>
                <div className="min-w-0">
                  <div className="text-sm font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors">
                    Client Intake Form Embed
                  </div>
                  <div className="text-xs text-zinc-500 mt-0.5">
                    Lead booking widget for Webflow
                  </div>
                </div>
              </div>
              <ArrowUpRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-950 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0" />
            </Link>

          </div>

        </div>
      </div>
    </section>
  );
}

