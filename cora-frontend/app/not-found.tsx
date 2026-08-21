'use client';

import React from 'react';
import Link from 'next/link';
import { 
  ArrowLeft, 
  ArrowRight, 
  Sparkles, 
  Zap, 
  Building2, 
  Receipt, 
  Layers, 
  ShieldCheck, 
  Home, 
  Search,
  Code2
} from 'lucide-react';

export default function NotFound() {
  return (
    <main className="min-h-screen bg-gradient-to-b from-zinc-50/70 via-white to-zinc-50/50 text-zinc-950 selection:bg-zinc-950 selection:text-white pt-28 sm:pt-36 pb-24 flex flex-col justify-center">
      
      <div className="max-w-4xl mx-auto px-4 sm:px-6 text-center space-y-8">
        
        {/* ── 1. Top Indicator Badge ───────────────────────────────────── */}
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100/80 backdrop-blur-md rounded-full text-xs font-mono font-bold text-zinc-800 border border-zinc-200/80 shadow-2xs">
          <span className="w-2 h-2 rounded-full bg-amber-500 animate-pulse" />
          <span>SCENE NOT ON CALL SHEET &bull; 404</span>
        </div>

        {/* ── 2. Display Typography ────────────────────────────────────── */}
        <div className="space-y-4 max-w-2xl mx-auto">
          <h1 className="font-display text-4xl sm:text-6xl md:text-7xl font-bold tracking-tight text-zinc-950 leading-[1.1]">
            Lost in the <br className="hidden sm:inline" />
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-zinc-900 via-zinc-600 to-zinc-400">
              creative pipeline?
            </span>
          </h1>

          <p className="text-sm sm:text-base text-zinc-600 leading-relaxed max-w-lg mx-auto">
            The page or shoot asset you’re looking for might have been moved, renamed, or doesn’t exist in this workspace.
          </p>
        </div>

        {/* ── 3. Primary Action Buttons ─────────────────────────────────── */}
        <div className="flex flex-wrap items-center justify-center gap-3 pt-2">
          <Link
            href="/"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-2xl text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all shadow-sm hover:-translate-y-0.5"
          >
            <Home className="w-4 h-4" />
            <span>Return to Homepage</span>
          </Link>

          <Link
            href="/ai-agent"
            className="inline-flex items-center gap-2 bg-emerald-50 text-emerald-950 border border-emerald-200/80 px-5 py-3.5 rounded-2xl text-xs sm:text-sm font-bold hover:bg-emerald-100 transition-all shadow-2xs hover:-translate-y-0.5"
          >
            <Sparkles className="w-4 h-4 text-emerald-600" />
            <span>Launch AI Co-Founder</span>
          </Link>

          <a
            href="https://app.heycora.in/workspace/login?source=404_page"
            className="inline-flex items-center gap-2 bg-zinc-100 text-zinc-900 px-5 py-3.5 rounded-2xl text-xs sm:text-sm font-semibold hover:bg-zinc-200 transition-all"
          >
            <span>Open Studio App</span>
            <ArrowRight className="w-4 h-4 text-zinc-500" />
          </a>
        </div>

        {/* ── 4. Quick Recovery Hub Grid ───────────────────────────────── */}
        <div className="mt-12 pt-12 border-t border-zinc-200/80 max-w-3xl mx-auto text-left">
          <div className="flex items-center justify-between mb-4">
            <span className="text-[11px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
              POPULAR RECOVERY DESTINATIONS
            </span>
            <span className="text-xs font-mono text-zinc-400">
              Cora Studio OS v2.4
            </span>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            
            {/* Features */}
            <Link
              href="/features"
              className="p-4 rounded-2xl bg-white border border-zinc-200/70 hover:border-zinc-950 hover:shadow-xs transition-all group flex flex-col justify-between"
            >
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-200/60 group-hover:scale-105 transition-transform">
                  <Zap className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-xs font-bold text-zinc-950 group-hover:text-black">20 Built Modules</div>
                  <div className="text-[11px] text-zinc-500 font-normal">CRM, E-Signs &amp; Invoices</div>
                </div>
              </div>
            </Link>

            {/* Industries */}
            <Link
              href="/use-cases"
              className="p-4 rounded-2xl bg-white border border-zinc-200/70 hover:border-zinc-950 hover:shadow-xs transition-all group flex flex-col justify-between"
            >
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-200/60 group-hover:scale-105 transition-transform">
                  <Building2 className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-xs font-bold text-zinc-950 group-hover:text-black">5 Workspaces</div>
                  <div className="text-[11px] text-zinc-500 font-normal">Photo, Film &amp; Real Estate</div>
                </div>
              </div>
            </Link>

            {/* GST Calculator */}
            <Link
              href="/tools/gst-calculator"
              className="p-4 rounded-2xl bg-white border border-zinc-200/70 hover:border-zinc-950 hover:shadow-xs transition-all group flex flex-col justify-between"
            >
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/60 group-hover:scale-105 transition-transform">
                  <Receipt className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-xs font-bold text-zinc-950 group-hover:text-black">18% GST Calculator</div>
                  <div className="text-[11px] text-zinc-500 font-normal">Free B2B Split Math</div>
                </div>
              </div>
            </Link>

            {/* Integrations */}
            <Link
              href="/integrations"
              className="p-4 rounded-2xl bg-white border border-zinc-200/70 hover:border-zinc-950 hover:shadow-xs transition-all group flex flex-col justify-between"
            >
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-200/60 group-hover:scale-105 transition-transform">
                  <Code2 className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-xs font-bold text-zinc-950 group-hover:text-black">Integrations Hub</div>
                  <div className="text-[11px] text-zinc-500 font-normal">Framer, Webflow &amp; WP</div>
                </div>
              </div>
            </Link>

            {/* Compare */}
            <Link
              href="/compare"
              className="p-4 rounded-2xl bg-white border border-zinc-200/70 hover:border-zinc-950 hover:shadow-xs transition-all group flex flex-col justify-between"
            >
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200/60 group-hover:scale-105 transition-transform">
                  <ShieldCheck className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-xs font-bold text-zinc-950 group-hover:text-black">Compare Market</div>
                  <div className="text-[11px] text-zinc-500 font-normal">vs HoneyBook &amp; HubSpot</div>
                </div>
              </div>
            </Link>

            {/* Pricing */}
            <Link
              href="/pricing"
              className="p-4 rounded-2xl bg-white border border-zinc-200/70 hover:border-zinc-950 hover:shadow-xs transition-all group flex flex-col justify-between"
            >
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/60 group-hover:scale-105 transition-transform">
                  <Layers className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-xs font-bold text-zinc-950 group-hover:text-black">Transparent Pricing</div>
                  <div className="text-[11px] text-zinc-500 font-normal">From $0 Free Forever</div>
                </div>
              </div>
            </Link>

          </div>
        </div>

      </div>

    </main>
  );
}
