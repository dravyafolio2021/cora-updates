import React from 'react';
import Link from 'next/link';
import type { Metadata } from 'next';
import { ArrowRight, CheckCircle2, Zap, Layers, Code, ShieldCheck, Sparkles, Building2, ExternalLink } from 'lucide-react';
import { INTEGRATIONS_LIST } from '@/lib/integrations-data';

export const metadata: Metadata = {
  title: 'Integrations & Website Embeds — Cora Studio OS',
  description: 'Connect Cora’s autonomous AI operating system to Framer, Webflow, WordPress, Shopify, and Next.js. Collect e-signatures, 18% GST invoices, and CRM leads seamlessly.',
  keywords: [
    'Framer CRM integration',
    'Webflow client portal',
    'WordPress studio plugin alternative',
    'Shopify B2B GST invoices',
    'Next.js studio headless backend',
    'Framer 18% GST calculator embed'
  ],
  alternates: {
    canonical: 'https://heycora.in/integrations/',
  },
};

export default function IntegrationsHubPage() {
  return (
    <main className="min-h-screen bg-white text-zinc-950 selection:bg-zinc-950 selection:text-white pt-28 sm:pt-36 pb-20">
      
      {/* ── 1. Hero Header ────────────────────────────────────────────── */}
      <section className="max-w-5xl mx-auto px-4 sm:px-6 text-center space-y-6">
        <div className="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 rounded-full text-xs font-semibold text-zinc-800 border border-zinc-200">
          <Zap className="w-3.5 h-3.5 text-amber-600" />
          <span>Ecosystem &amp; Embed Engine</span>
        </div>

        <h1 className="font-display text-3xl sm:text-5xl md:text-6xl font-bold tracking-tight text-zinc-950 max-w-4xl mx-auto leading-[1.15]">
          Turn any website into an <br className="hidden sm:inline" />
          <span className="text-zinc-500">Autonomous Business Backend</span>
        </h1>

        <p className="text-base sm:text-lg text-zinc-600 max-w-2xl mx-auto leading-relaxed">
          Connect your existing Framer, Webflow, WordPress, or Shopify site to Cora with 1 line of code. Collect SHA-256 contracts, issue 18% GST invoices, and trigger AI proposals instantly.
        </p>

        <div className="pt-2 flex flex-wrap items-center justify-center gap-3">
          <Link
            href="/tools/embed-builder"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-3 rounded-xl text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all shadow-sm"
          >
            <span>Launch Interactive Embed Builder</span>
            <ArrowRight className="w-4 h-4" />
          </Link>
          <a
            href="https://app.heycora.in/workspace/login?source=integrations_hub"
            className="inline-flex items-center gap-2 bg-zinc-100 text-zinc-900 px-5 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-200 transition-all"
          >
            <span>Get Workspace API Keys</span>
          </a>
        </div>
      </section>

      {/* ── 2. Master Integrations Grid ───────────────────────────────── */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 mt-16 sm:mt-24">
        <div className="flex items-center justify-between border-b border-zinc-200 pb-4 mb-8">
          <div>
            <h2 className="text-lg sm:text-xl font-bold text-zinc-950">Supported Platforms &amp; CMS</h2>
            <p className="text-xs sm:text-sm text-zinc-500">Step-by-step guides, 1-click embed codes, and webhook integrations</p>
          </div>
          <span className="text-xs font-mono font-bold text-zinc-400 bg-zinc-100 px-2.5 py-1 rounded-md">
            5 CONNECTORS LIVE
          </span>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {INTEGRATIONS_LIST.map((item) => (
            <Link
              key={item.slug}
              href={`/integrations/${item.slug}`}
              className="group p-6 rounded-2xl bg-white border border-zinc-200/80 hover:border-zinc-950 hover:shadow-md transition-all flex flex-col justify-between"
            >
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <div className="w-12 h-12 rounded-xl bg-zinc-100 text-zinc-950 flex items-center justify-center font-bold text-lg border border-zinc-200 group-hover:scale-105 transition-transform">
                    {item.name.charAt(0)}
                  </div>
                  <span className="text-[10px] font-mono font-bold text-zinc-500 bg-zinc-100 px-2 py-0.5 rounded">
                    {item.category}
                  </span>
                </div>

                <div>
                  <h3 className="text-base font-bold text-zinc-950 group-hover:text-black flex items-center gap-1.5">
                    <span>{item.name} Integration</span>
                    <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-950 group-hover:translate-x-1 transition-all" />
                  </h3>
                  <p className="text-xs text-zinc-600 mt-1 line-clamp-2 leading-relaxed">
                    {item.tagline}
                  </p>
                </div>

                <div className="pt-2 grid grid-cols-2 gap-2 text-[11px] bg-zinc-50 p-2.5 rounded-xl border border-zinc-100 font-mono">
                  <div>
                    <span className="text-zinc-400 block text-[10px]">SETUP TIME</span>
                    <span className="font-bold text-zinc-800">{item.stats.setupTime}</span>
                  </div>
                  <div>
                    <span className="text-zinc-400 block text-[10px]">SAVINGS</span>
                    <span className="font-bold text-emerald-700">{item.stats.monthlySavings}</span>
                  </div>
                </div>
              </div>

              <div className="mt-5 pt-4 border-t border-zinc-100 flex items-center justify-between text-xs font-semibold text-zinc-900 group-hover:underline">
                <span>View Integration Guide</span>
                <span>→</span>
              </div>
            </Link>
          ))}
        </div>
      </section>

      {/* ── 3. Why Embed Cora? ─────────────────────────────────────────── */}
      <section className="max-w-5xl mx-auto px-4 sm:px-6 mt-20 sm:mt-28">
        <div className="p-8 sm:p-12 rounded-3xl bg-zinc-950 text-white space-y-8 shadow-xl">
          <div className="max-w-2xl space-y-3">
            <span className="text-xs font-mono text-emerald-400 uppercase tracking-wider font-bold">
              ZERO CODE REBUILD
            </span>
            <h2 className="font-display text-2xl sm:text-4xl font-bold tracking-tight">
              Keep your visual website. <br />
              Automate the painful operational backend.
            </h2>
            <p className="text-sm sm:text-base text-zinc-400 leading-relaxed">
              You’ve already spent hours perfecting your design on Framer or Webflow. Cora gives you enterprise infrastructure without touching your layout or moving hosting servers.
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4 border-t border-zinc-800 text-xs">
            <div className="space-y-2">
              <div className="text-sm font-bold text-white flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-400" />
                <span>SHA-256 E-Sign Vault</span>
              </div>
              <p className="text-zinc-400 leading-relaxed">
                Send legally binding contracts automatically when prospects submit your booking forms.
              </p>
            </div>

            <div className="space-y-2">
              <div className="text-sm font-bold text-white flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-400" />
                <span>18% GST Tax Hub</span>
              </div>
              <p className="text-zinc-400 leading-relaxed">
                Automate CGST/SGST splitting, HSN/SAC code 9983, and branded PDF invoice generation.
              </p>
            </div>

            <div className="space-y-2">
              <div className="text-sm font-bold text-white flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-400" />
                <span>WhatsApp AI Dispatch</span>
              </div>
              <p className="text-zinc-400 leading-relaxed">
                Notify your crew, confirm shoot time slots, and follow up with clients on autopilot.
              </p>
            </div>
          </div>
        </div>
      </section>

    </main>
  );
}
