import React from 'react';
import Link from 'next/link';
import { Sparkles, ArrowLeft, ArrowRight, CheckCircle2, Zap, Receipt, ShieldCheck, Kanban, Bot, Layers } from 'lucide-react';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Changelog & Product Updates | Cora Studio OS',
  description: 'Track the latest features, autonomous AI agent upgrades, and improvements shipped to Cora Studio OS.',
};

export default function ChangelogPage() {
  const releases = [
    {
      version: 'v4.0.0',
      date: 'August 25, 2026',
      badge: 'LATEST RELEASE',
      title: 'Unified Platform Consolidation & Clean Slate Multi-Module Engine',
      description: 'Major milestone release consolidating all creative and business operations into a unified platform architecture. Features complete workspace tenant isolation, dynamic data bridges, autonomous AI co-founder deck, and responsive client portals.',
      highlights: [
        'Complete platform-wide v4.0.0 consolidation with 100% test passing rate across E2E and unit test suites.',
        'Redesigned Mountain Mission summit, Our Motto calendar, and Core Values bento matrix on the About suite.',
        'Unified 5-pillar Autonomous AI Co-Founder Command Deck with real-time telemetry execution stream.',
        'Multi-tenant workspace isolation across CRM, Document Vault, Media Studio, and Financial Co-Founder.',
      ],
      icon: Layers,
      accent: 'indigo',
    },
    {
      version: 'v2.4.0',
      date: 'August 20, 2026',
      badge: 'PREVIOUS RELEASE',
      title: 'Autonomous AI Co-Founder OS & Competitor Comparison Suite',
      description: 'Launched full multi-turn memory AI voice-to-scope routing, automated shoot quote generation, and 8 dedicated head-to-head competitor comparison benchmarks.',
      highlights: [
        'Voice-to-Scope & Proposal Engine: AI converts recorded client audio into actionable project scopes.',
        'High-speed multi-model fallback: Seamless routing across Claude 3.5 Sonnet and Gemini 1.5 Flash.',
        '8 Head-to-Head Comparison pages vs HoneyBook, Studio Ninja, HubSpot, and DocuSign.',
        'Full-height aesthetic mobile drawer with background scroll lock.',
      ],
      icon: Bot,
      accent: 'emerald',
    },
    {
      version: 'v2.3.0',
      date: 'July 15, 2026',
      badge: 'FINANCIAL HUB',
      title: '18% GST Invoicing Engine & Public Calculation Engine',
      description: 'Comprehensive tax engine with intra-state (9% CGST + 9% SGST) and inter-state (18% IGST) automated tax splitting, branded PDF invoices, and live currency conversions.',
      highlights: [
        'Instant 18% GST tax breakdown calculator with GSTIN verification.',
        'One-click downloadable PDF invoices with cryptographic integrity seals.',
        'Dynamic FX converter supporting INR, USD, EUR, GBP, AED, and SGD.',
        'Automated payment tracking and revenue analytics dashboard.',
      ],
      icon: Receipt,
      accent: 'amber',
    },
    {
      version: 'v2.2.0',
      date: 'June 10, 2026',
      badge: 'LEGAL VAULT',
      title: 'SHA-256 E-Sign Vault & 5-Step Document Wizard',
      description: 'Legally binding digital signature contracts adhering to the Indian IT Act 2000 and US ESIGN Act, complete with tamper-proof cryptographic audit stamps.',
      highlights: [
        'Guided 5-step contract creator: Details -> Terms -> GST Math -> E-Sign -> Complete.',
        'Interactive signature drawing canvas with touch & stylus support.',
        'Automated client signing link delivery via email & WhatsApp.',
        'Immutable cryptographic audit trail with signer IP and timestamp.',
      ],
      icon: ShieldCheck,
      accent: 'sky',
    },
    {
      version: 'v2.1.0',
      date: 'May 04, 2026',
      badge: 'SALES & CRM',
      title: 'Kanban CRM Pipeline & Visual Landing Page Canvas',
      description: 'Full sales funnel tracking with drag-and-drop deal stages, revenue forecasting, sliding deal drawers, and real-time Git repository synchronization.',
      highlights: [
        'Multi-stage deal pipeline with custom commercial photography milestones.',
        'Visual drag-and-drop landing page designer with responsive device preview.',
        'Omnichannel lead capture with WhatsApp and email quick dispatch.',
      ],
      icon: Kanban,
      accent: 'purple',
    },
  ];

  return (
    <main className="min-h-screen bg-[#FBFaf7] text-zinc-900 pt-28 sm:pt-36 pb-24">
      <div className="max-w-4xl mx-auto px-4 sm:px-6">
        
        {/* Breadcrumb Navigation */}
        <div className="mb-8">
          <Link 
            href="/" 
            className="inline-flex items-center gap-2 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Back to Home</span>
          </Link>
        </div>

        {/* Header Title */}
        <div className="mb-14 border-b border-zinc-200/80 pb-8">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-800 border border-zinc-200/80 mb-4">
            <Sparkles className="w-3.5 h-3.5 text-zinc-600" />
            <span>SHIPPED UPDATES</span>
          </div>
          <h1 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 mb-4">
            Changelog &amp; Release Log
          </h1>
          <p className="text-zinc-600 text-sm sm:text-base leading-relaxed max-w-2xl">
            Everything new shipped to Cora Studio OS. We release new autonomous features, AI integrations, and workflow improvements weekly.
          </p>
        </div>

        {/* Timeline of Releases */}
        <div className="space-y-12 relative before:absolute before:inset-0 before:left-[19px] sm:before:left-[23px] before:w-[2px] before:bg-zinc-200 before:pointer-events-none">
          {releases.map((rel) => {
            const Icon = rel.icon;
            return (
              <div key={rel.version} className="relative pl-12 sm:pl-16 space-y-4">
                
                {/* Timeline Dot Icon */}
                <div className="absolute left-0 top-1 w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-white border border-zinc-200/90 shadow-sm flex items-center justify-center text-zinc-900 z-10">
                  <Icon className="w-5 h-5 text-zinc-800" />
                </div>

                {/* Release Card */}
                <div className="p-6 sm:p-8 rounded-3xl bg-white border border-zinc-200/90 shadow-sm space-y-4">
                  <div className="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 pb-3">
                    <div className="flex items-center gap-2.5">
                      <span className="font-mono text-sm font-bold text-zinc-950">{rel.version}</span>
                      <span className="text-[10px] font-mono font-bold bg-zinc-100 text-zinc-700 px-2 py-0.5 rounded border border-zinc-200/70">
                        {rel.badge}
                      </span>
                    </div>
                    <span className="text-xs font-mono text-zinc-400">{rel.date}</span>
                  </div>

                  <div>
                    <h2 className="text-lg sm:text-xl font-bold text-zinc-950 tracking-tight">
                      {rel.title}
                    </h2>
                    <p className="text-xs sm:text-sm text-zinc-600 font-normal mt-1 leading-relaxed">
                      {rel.description}
                    </p>
                  </div>

                  <div className="space-y-2 pt-2">
                    <div className="text-[11px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                      Key Capabilities Included:
                    </div>
                    <ul className="space-y-1.5 text-xs sm:text-sm text-zinc-700">
                      {rel.highlights.map((item, idx) => (
                        <li key={idx} className="flex items-start gap-2">
                          <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                          <span>{item}</span>
                        </li>
                      ))}
                    </ul>
                  </div>
                </div>

              </div>
            );
          })}
        </div>

        {/* Bottom CTA */}
        <div className="mt-16 pt-8 border-t border-zinc-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <Link 
            href="/features" 
            className="text-xs font-semibold text-zinc-700 hover:text-zinc-950 flex items-center gap-1.5"
          >
            <span>Explore all 20 built platform modules</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </Link>
          <a
            href="https://app.heycora.in/workspace/login"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-zinc-800 transition-all"
          >
            <span>Test Latest Build in Workspace</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
          </a>
        </div>

      </div>
    </main>
  );
}
