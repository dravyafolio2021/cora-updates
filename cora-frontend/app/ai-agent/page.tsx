'use client';

import React, { useState } from 'react';
import type { Metadata } from 'next';
import Image from 'next/image';
import { 
  Sparkles, 
  ArrowRight, 
  Send, 
  Mic, 
  Bot, 
  BrainCircuit, 
  CheckCircle2, 
  Zap, 
  ShieldCheck, 
  Lock, 
  Cpu,
  FileText,
  Receipt,
  MessageSquare
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

const AGENT_MODES = [
  {
    id: 'voice-to-proposal',
    title: 'Voice Note to Commercial Scope',
    prompt: '"Hey Cora, just got off call with Nike Brand Team. 2-day shoot at Studio 4, 3 models, 4K video + stills, 50 edits, deliverables in 5 days. Budget ₹4.5L + GST. Generate proposal and retainer contract."',
    response: {
      title: 'Commercial Shoot Proposal & Scope of Work',
      client: 'Nike India Brand Team',
      shootDays: '2 Days (Studio 04 Bay Rental)',
      deliverables: '4K ProRes Master + 50 High-Res Retouched Stills',
      baseFee: '₹4,50,000',
      gst18: '₹81,000 (18% IGST)',
      total: '₹5,31,000',
      milestones: '50% Advance (₹2,65,500) • 50% on Final Master Delivery',
      eSignStatus: 'Ready for SHA-256 Client Signature Hash'
    }
  },
  {
    id: 'call-sheet-dispatch',
    title: 'WhatsApp Call-Sheet Dispatch',
    prompt: '"Dispatch crew call-sheet for tomorrow\'s fashion editorial at Mehboob Studio Bay 02. Call time 7:30 AM, lunch at 1:00 PM. Include gear checklist & parking instructions."',
    response: {
      title: 'Automated WhatsApp Call-Sheet #CS-892',
      recipient: '12 Crew Members (Lighting, MUA, Stylist, DP, Grips)',
      callTime: '07:30 AM IST (Mehboob Studio, Bay 02)',
      weatherNotes: '28°C Clear • Air-Conditioned Stage',
      gearChecklist: 'Sony FX6 A/B Cams, 24-70 GM II, Aputure 600d x 3, C-Stands x 6',
      whatsappStatus: 'Dispatched via Meta Cloud API with Live Delivery Receipts'
    }
  },
  {
    id: 'gst-invoice',
    title: 'Instant 18% GST Invoice & UPI Gate',
    prompt: '"Create tax invoice for Oberoi Realty commercial shoot. GSTIN: 27AABCO1234F1Z5. Include 18% CGST/SGST split and generate instant UPI QR payment link."',
    response: {
      title: 'Tax Invoice #CORA-2026-084',
      billedTo: 'Oberoi Realty Ltd (GSTIN: 27AABCO1234F1Z5)',
      hsnSac: '998381 (Commercial Photography Services)',
      taxableAmount: '₹3,00,000',
      cgst9: '₹27,000 (9%)',
      sgst9: '₹27,000 (9%)',
      totalAmount: '₹3,54,000',
      paymentGate: 'Dynamic UPI QR + Razorpay Instant Settlement Link'
    }
  }
];

export default function AIAgentPage() {
  const [selectedMode, setSelectedMode] = useState(0);

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-20 overflow-hidden bg-white">
      
      {/* ── Top Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-20 sm:mb-28">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-50 rounded-xl border border-emerald-200/80 text-xs font-semibold text-emerald-900 mb-4 shadow-2xs">
          <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
          <span>Workspace-Level Autonomous Intelligence</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[880px] mx-auto mb-5">
          Your studio's personalized AI executive agent
        </h1>

        <p className="text-zinc-600 text-base sm:text-xl font-normal leading-relaxed max-w-[680px] mx-auto mb-8">
          Trained on your studio rate cards, client history, legal contract templates, and 18% GST rules. Executes complex administrative pipelines in seconds.
        </p>

        <div className="flex items-center justify-center flex-wrap gap-3.5">
          <a
            href="https://app.heycora.in/workspace/login?source=ai_agent_hero"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm border border-zinc-800 group"
          >
            <span>Get started for Free</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
          </a>

          <a
            href="mailto:dravya.bansal@heycora.in?subject=AI%20Agent%20Custom%20Inquiry"
            className="inline-flex items-center gap-2 bg-white text-zinc-950 border border-zinc-300 hover:border-zinc-400 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-50 transition-all shadow-2xs"
          >
            <span>Chat with Founder</span>
          </a>
        </div>
      </section>

      {/* ── Interactive AI Agent Simulator ── */}
      <section className="w-full max-w-[1140px] mx-auto px-4 sm:px-6 mb-28 sm:mb-36">
        <div className="bg-[#0D1117] rounded-[36px] border border-zinc-800 p-6 sm:p-10 md:p-12 shadow-2xl text-white relative overflow-hidden">
          
          {/* Mode Switcher Tabs */}
          <div className="flex items-center gap-2 overflow-x-auto pb-4 mb-8 border-b border-zinc-800">
            {AGENT_MODES.map((mode, idx) => (
              <button
                key={mode.id}
                onClick={() => {
                  setSelectedMode(idx);
                  trackEvent('ai_agent_simulator_switch', { mode: mode.id });
                }}
                className={`px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all ${
                  selectedMode === idx
                    ? 'bg-white text-zinc-950 shadow-sm'
                    : 'bg-zinc-900 text-zinc-400 hover:text-white border border-zinc-800'
                }`}
              >
                {mode.title}
              </button>
            ))}
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {/* Input Prompt Box */}
            <div className="lg:col-span-5 space-y-4">
              <div className="flex items-center justify-between text-xs text-zinc-400 font-mono">
                <span>INBOUND OPERATIONAL PROMPT</span>
                <span className="text-emerald-400 flex items-center gap-1">
                  <Mic className="w-3 h-3" /> Voice / WhatsApp
                </span>
              </div>

              <div className="bg-zinc-900/90 rounded-2xl p-5 border border-zinc-800 text-zinc-200 text-xs sm:text-sm leading-relaxed font-mono">
                {AGENT_MODES[selectedMode].prompt}
              </div>

              <div className="flex items-center justify-between text-xs text-zinc-500 pt-2">
                <span className="flex items-center gap-1.5">
                  <BrainCircuit className="w-3.5 h-3.5 text-emerald-400" />
                  Routed to Claude 3.5 Sonnet
                </span>
                <span className="font-mono text-emerald-400">LATENCY: 340MS</span>
              </div>
            </div>

            {/* Generated Structured Output */}
            <div className="lg:col-span-7 bg-zinc-900/60 rounded-2xl p-6 border border-zinc-800 space-y-4">
              <div className="flex items-center justify-between border-b border-zinc-800 pb-3">
                <div className="flex items-center gap-2">
                  <Sparkles className="w-4 h-4 text-emerald-400" />
                  <span className="font-bold text-sm text-white">{AGENT_MODES[selectedMode].response.title}</span>
                </div>
                <span className="text-[10px] font-mono bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-2 py-0.5 rounded-full">
                  AUTONOMOUSLY GENERATED
                </span>
              </div>

              <div className="space-y-2.5 text-xs sm:text-[13px] text-zinc-300">
                {Object.entries(AGENT_MODES[selectedMode].response).map(([key, val]) => {
                  if (key === 'title') return null;
                  return (
                    <div key={key} className="flex flex-col sm:flex-row sm:items-baseline justify-between gap-1 py-1.5 border-b border-zinc-800/60">
                      <span className="text-zinc-500 capitalize font-mono text-[11px]">{key.replace(/([A-Z])/g, ' $1')}</span>
                      <span className="font-semibold text-white text-right">{val}</span>
                    </div>
                  );
                })}
              </div>

              <div className="pt-2 flex justify-end">
                <a
                  href="https://app.heycora.in/workspace/login"
                  className="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400 hover:text-emerald-300 transition-colors"
                >
                  <span>Execute in your workspace</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </a>
              </div>
            </div>

          </div>

        </div>
      </section>

      {/* ── 3 Core Pillars of Cora AI Agent ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 sm:mb-36">
        <div className="text-center max-w-[680px] mx-auto mb-16">
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            Why workspace-level AI beats generic chat apps
          </h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          
          <div className="bg-zinc-50 rounded-[28px] p-8 border border-zinc-200/90 space-y-4 shadow-2xs">
            <div className="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center">
              <Bot className="w-5 h-5" />
            </div>
            <h3 className="font-display text-xl font-bold text-zinc-950">
              Trained on Studio Context
            </h3>
            <p className="text-zinc-600 text-sm leading-relaxed">
              Your Cora AI Agent knows your hourly studio rates, assistant day fees, equipment inventory, and client contract terms.
            </p>
          </div>

          <div className="bg-zinc-50 rounded-[28px] p-8 border border-zinc-200/90 space-y-4 shadow-2xs">
            <div className="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center">
              <Zap className="w-5 h-5" />
            </div>
            <h3 className="font-display text-xl font-bold text-zinc-950">
              Multi-Model Speed Routing
            </h3>
            <p className="text-zinc-600 text-sm leading-relaxed">
              Routes light property copy to sub-400ms Gemini 2.0 Flash and complex production contracts to Anthropic Claude 3.5 Sonnet.
            </p>
          </div>

          <div className="bg-zinc-50 rounded-[28px] p-8 border border-zinc-200/90 space-y-4 shadow-2xs">
            <div className="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center">
              <Lock className="w-5 h-5" />
            </div>
            <h3 className="font-display text-xl font-bold text-zinc-950">
              Zero API Keys &amp; SOC-2 Safe
            </h3>
            <p className="text-zinc-600 text-sm leading-relaxed">
              Never pay separate OpenAI or Anthropic monthly subscriptions. Everything is included with enterprise-grade data isolation.
            </p>
          </div>

        </div>
      </section>

      {/* ── Bottom Section CTA ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        <div className="w-full rounded-[36px] bg-zinc-950 text-white p-8 sm:p-14 text-center relative overflow-hidden border border-zinc-800 shadow-xl">
          <div className="relative z-10 max-w-[680px] mx-auto space-y-6">
            <h2 className="font-display text-3xl sm:text-4xl font-bold tracking-tight">
              Deploy your studio AI agent today
            </h2>
            <p className="text-zinc-400 text-sm sm:text-base leading-relaxed font-normal">
              Get 1,000 complimentary AI agent runs and test automated proposals, contracts, and call-sheet dispatch without a credit card.
            </p>

            <div className="flex items-center justify-center flex-wrap gap-3.5 pt-2">
              <a
                href="https://app.heycora.in/workspace/login?source=ai_agent_bottom"
                className="inline-flex items-center gap-2 bg-white text-zinc-950 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm group"
              >
                <span>Get started for Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-600 group-hover:translate-x-0.5 transition-transform" />
              </a>

              <a
                href="mailto:dravya.bansal@heycora.in?subject=AI%20Agent%20Custom%20Inquiry"
                className="inline-flex items-center gap-2 bg-zinc-900 text-white border border-zinc-700 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
              >
                <span>Chat with Founder</span>
              </a>
            </div>
          </div>
        </div>
      </section>

    </main>
  );
}
