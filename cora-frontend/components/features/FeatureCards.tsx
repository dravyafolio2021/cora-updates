'use client';

import React from 'react';
import { Bot, Layers, FileCheck, Search, MessageSquare, Zap, CheckCircle2 } from 'lucide-react';

const features = [
  {
    icon: Bot,
    iconColor: 'text-purple-600',
    iconBg: 'bg-purple-50 border-purple-100',
    tag: 'Frontier AI Routing',
    title: 'Multi-Model AI Orchestration',
    description: 'Switch intelligently between Claude 3.5 Sonnet, GPT-4o, and Gemini 2.0 Flash with unified quota management and zero API key headaches.',
    points: ['Prompt chains for proposals & scripts', '1,000 to unlimited AI runs/mo', 'Intelligent latency auto-routing'],
  },
  {
    icon: Layers,
    iconColor: 'text-blue-600',
    iconBg: 'bg-blue-50 border-blue-100',
    tag: 'Automated Pipelines',
    title: 'Lead Funnel & Call-Sheet Manager',
    description: 'Capture inbound client inquiries, automate call-sheet dispatch, track shoot statuses, and trigger automated reminders in a visual Kanban.',
    points: ['Automated call-time confirmations', 'Stage-based client trigger rules', 'Real-time booking revenue stats'],
  },
  {
    icon: FileCheck,
    iconColor: 'text-emerald-600',
    iconBg: 'bg-emerald-50 border-emerald-100',
    tag: 'Legal & Tax Math',
    title: 'Document Vault & GST Invoicing',
    description: 'Issue legally binding digital contracts with cryptographic audit logs, combined with full Indian B2B GST tax calculation (CGST+SGST/IGST).',
    points: ['100% compliant e-signatures', 'Instant B2B GST invoices with GSTIN check', 'Razorpay & UPI direct settlement'],
  },
  {
    icon: Search,
    iconColor: 'text-amber-600',
    iconBg: 'bg-amber-50 border-amber-100',
    tag: 'SEO & GEO Engine',
    title: 'AI Listing & Content Generator',
    description: 'Generate high-ranking real estate property descriptions, social video hooks, and location-targeted SEO content in under 5 seconds.',
    points: ['Local GEO-targeted keywords', 'Instagram & TikTok video scripts', 'One-click export to CMS & portal'],
  },
  {
    icon: MessageSquare,
    iconColor: 'text-green-600',
    iconBg: 'bg-green-50 border-green-100',
    tag: 'Omnichannel Dispatch',
    title: 'WhatsApp Cloud & Gmail Sync',
    description: 'Send instant booking confirmations, payment reminders, and contracts directly to your clients on WhatsApp and Gmail automatically.',
    points: ['Official Meta WhatsApp Cloud API', 'Automated shoot schedule alerts', 'Two-way calendar synchronization'],
  },
  {
    icon: Zap,
    iconColor: 'text-zinc-900',
    iconBg: 'bg-zinc-100 border-zinc-200',
    tag: 'SaaS Consolidation',
    title: 'Replaces 5+ Fragmented Tools',
    description: 'Cancel your HoneyBook, Notion, Zapier, DocuSign, and individual AI subscriptions. Consolidate your entire business operating stack into Cora.',
    points: ['Saves $200+ per month in SaaS', 'Zero cross-tool data desync', 'Single unified admin workspace'],
  },
];

export function FeatureCards() {
  return (
    <section className="py-16 md:py-24 relative z-10 bg-zinc-50/50 border-t border-zinc-100">
      <div className="w-full max-w-[1140px] mx-auto px-4 sm:px-6">
        
        {/* Section Header */}
        <div className="text-center max-w-[780px] mx-auto mb-14">
          <div className="inline-flex items-center gap-1.5 font-sans text-[0.8125rem] font-medium text-zinc-600 px-3.5 py-1 bg-white border border-zinc-200 rounded-full mb-3.5 shadow-sm">
            <span>Core Capabilities</span>
          </div>
          <h2 className="font-display text-[clamp(1.85rem,3.8vw,2.75rem)] font-[550] tracking-[-0.035em] text-zinc-950 leading-[1.18] mb-3">
            Engineered for high-velocity agencies & solo founders.
          </h2>
          <p className="font-sans text-[clamp(0.85rem,1.1vw,1rem)] text-zinc-600 leading-[1.55]">
            Everything you need to capture leads, close agreements, route frontier AI models, and settle revenue without switching software.
          </p>
        </div>

        {/* 3-Column Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {features.map((feature, idx) => {
            const Icon = feature.icon;
            return (
              <div
                key={idx}
                className="bg-white border border-zinc-200/90 rounded-2xl p-6 flex flex-col justify-between shadow-xs hover:border-zinc-300 hover:shadow-md hover:-translate-y-1 transition-all duration-200 group"
              >
                <div>
                  <div className="flex items-center justify-between mb-4">
                    <div className={`w-10 h-10 rounded-xl flex items-center justify-center border ${feature.iconBg}`}>
                      <Icon className={`w-5 h-5 ${feature.iconColor}`} />
                    </div>
                    <span className="text-[0.6875rem] font-bold uppercase tracking-wider px-2 py-0.5 bg-zinc-100 text-zinc-600 rounded-md">
                      {feature.tag}
                    </span>
                  </div>

                  <h3 className="font-display text-lg font-bold text-zinc-950 mb-2 group-hover:text-zinc-900 transition-colors">
                    {feature.title}
                  </h3>
                  <p className="font-sans text-xs sm:text-[0.8125rem] text-zinc-600 leading-relaxed mb-5">
                    {feature.description}
                  </p>
                </div>

                <div className="pt-4 border-t border-zinc-100 space-y-2">
                  {feature.points.map((pt, pIdx) => (
                    <div key={pIdx} className="flex items-center gap-2 text-xs text-zinc-700">
                      <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                      <span>{pt}</span>
                    </div>
                  ))}
                </div>
              </div>
            );
          })}
        </div>

      </div>
    </section>
  );
}
