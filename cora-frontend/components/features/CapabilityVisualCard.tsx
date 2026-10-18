'use client';

import React from 'react';
import { 
  Sparkles, 
  Layers, 
  ShieldCheck, 
  CheckCircle2, 
  FileText, 
  Receipt, 
  Clock, 
  Calendar,
  Lock,
  ArrowRight,
  Zap,
  TrendingUp,
  Cpu,
  Mic
} from 'lucide-react';
import { FeatureModule } from '@/lib/features-data';

// ── OFFICIAL BRAND LOGOS (RE-EXPORTED FROM CANONICAL REGISTRY) ──
import { ClaudeLogo, GeminiLogo, OpenAILogo, WhatsAppLogo } from './OfficialBrandLogos';
export { ClaudeLogo, GeminiLogo, OpenAILogo, WhatsAppLogo };

export function UPILogoBadge() {
  return (
    <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-[#097939]/10 text-[#097939] border border-[#097939]/20 font-mono text-[10px] font-bold">
      <span className="w-1.5 h-1.5 rounded-full bg-[#097939]" />
      UPI Dynamic QR
    </span>
  );
}

interface CapabilityVisualCardProps {
  cap: {
    title: string;
    description: string;
    tag: string;
  };
  feature: FeatureModule;
  index: number;
}

export function CapabilityVisualCard({ cap, feature, index }: CapabilityVisualCardProps) {
  const cardType = index % 4;

  // ── CARD 0: CONTEXTUAL VECTOR RAG MEMORY (CLAUDE & GEMINI NEURAL RECALL) ──
  if (cardType === 0) {
    return (
      <div className="w-full max-w-[480px] bg-white rounded-3xl border border-zinc-200/90 p-5 sm:p-6 shadow-[0_12px_36px_rgba(0,0,0,0.06)] space-y-4 select-none relative overflow-hidden">
        
        {/* Top Header with Official AI Logos */}
        <div className="flex items-center justify-between pb-3 border-b border-zinc-100">
          <div className="flex items-center gap-2">
            <div className="flex items-center -space-x-1.5">
              <div className="w-6 h-6 rounded-full bg-amber-50 border border-amber-200 flex items-center justify-center p-1 shadow-xs">
                <ClaudeLogo className="w-3.5 h-3.5" />
              </div>
              <div className="w-6 h-6 rounded-full bg-blue-50 border border-blue-200 flex items-center justify-center p-1 shadow-xs">
                <GeminiLogo className="w-3.5 h-3.5" />
              </div>
            </div>
            <div>
              <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">Neural Knowledge Store</span>
              <span className="text-xs font-bold text-zinc-950">142k Context Window Active</span>
            </div>
          </div>
          <span className="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-mono font-bold border border-emerald-200/80 flex items-center gap-1">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
            99.8% ACCURACY
          </span>
        </div>

        {/* Storytelling Visual Nodes */}
        <div className="space-y-2 text-xs">
          <div className="p-2.5 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <span className="w-2 h-2 rounded-full bg-zinc-900 shrink-0" />
              <div>
                <span className="font-bold text-zinc-900 block text-xs">Raymond Commercial Shoot</span>
                <span className="text-[10px] text-zinc-500 font-mono">Matched Rate Card: ₹4.5L/Day • Mumbai</span>
              </div>
            </div>
            <span className="font-mono text-[10px] font-bold text-zinc-800 bg-zinc-100 px-2 py-0.5 rounded">
              RECALLED
            </span>
          </div>

          <div className="p-2.5 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <span className="w-2 h-2 rounded-full bg-zinc-700 shrink-0" />
              <div>
                <span className="font-bold text-zinc-900 block text-xs">Cinema Kit #A (Sony FX6 4K)</span>
                <span className="text-[10px] text-zinc-500 font-mono">Calendar Verified: 0 Booking Conflicts</span>
              </div>
            </div>
            <span className="font-mono text-[10px] font-bold text-zinc-800 bg-zinc-100 px-2 py-0.5 rounded">
              LOCKED
            </span>
          </div>

          <div className="p-2.5 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <span className="w-2 h-2 rounded-full bg-zinc-800 shrink-0" />
              <div>
                <span className="font-bold text-zinc-900 block text-xs">18% GST (CGST 9% + SGST 9%)</span>
                <span className="text-[10px] text-zinc-500 font-mono">SAC 998314 • Tax: ₹81,000</span>
              </div>
            </div>
            <span className="font-mono text-[10px] font-bold text-zinc-800 bg-zinc-100 px-2 py-0.5 rounded">
              CALCULATED
            </span>
          </div>
        </div>

        {/* Tangible Business Value Footer */}
        <div className="pt-2 border-t border-zinc-100 flex items-center justify-between text-[11px]">
          <span className="text-zinc-500 flex items-center gap-1">
            <Zap className="w-3.5 h-3.5 text-amber-500" />
            <strong className="text-zinc-900">Zero Re-Prompts</strong>
          </span>
          <span className="text-emerald-700 font-bold font-mono">2h ➔ 8s Drafting Speed</span>
        </div>

      </div>
    );
  }

  // ── CARD 1: NATURAL LANGUAGE ACTION DISPATCH (WHATSAPP & E-SIGN AUTO-TRIGGER) ──
  if (cardType === 1) {
    return (
      <div className="w-full max-w-[480px] bg-white rounded-3xl border border-zinc-200/90 p-5 sm:p-6 shadow-[0_12px_36px_rgba(0,0,0,0.06)] space-y-4 select-none relative overflow-hidden">
        
        {/* Inbound Voice / Brief Trigger Header */}
        <div className="flex items-center justify-between pb-3 border-b border-zinc-100">
          <div className="flex items-center gap-2">
            <div className="w-7 h-7 rounded-xl bg-zinc-950 text-white flex items-center justify-center shadow-xs">
              <Mic className="w-3.5 h-3.5 text-white" />
            </div>
            <div>
              <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">Single Voice Command</span>
              <span className="text-xs font-bold text-zinc-950 truncate max-w-[200px] block">
                &ldquo;Draft shoot proposal for Raymond at ₹4.5L&rdquo;
              </span>
            </div>
          </div>
          <span className="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-mono font-bold border border-indigo-200">
            AUTO-DISPATCH
          </span>
        </div>

        {/* Multi-Channel Execution Tree */}
        <div className="space-y-2 text-xs">
          
          <div className="p-2.5 rounded-2xl bg-emerald-50/50 border border-emerald-200/70 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <div className="w-6 h-6 rounded-lg bg-[#25D366] flex items-center justify-center p-1 shadow-2xs">
                <WhatsAppLogo className="w-4 h-4" />
              </div>
              <div>
                <span className="font-bold text-zinc-900 block text-xs">Official WhatsApp Dispatch</span>
                <span className="text-[10px] text-zinc-500">Commercial Proposal PDF + UPI Payment Link</span>
              </div>
            </div>
            <span className="font-mono text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">
              ✓ SENT
            </span>
          </div>

          <div className="p-2.5 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <div className="w-6 h-6 rounded-lg bg-zinc-950 text-white flex items-center justify-center">
                <Lock className="w-3.5 h-3.5 text-amber-300" />
              </div>
              <div>
                <span className="font-bold text-zinc-900 block text-xs">Cryptographic E-Sign Envelope</span>
                <span className="text-[10px] text-zinc-500">SHA-256 Sealed • IT Act 2000 Compliant</span>
              </div>
            </div>
            <span className="font-mono text-[10px] font-bold text-zinc-800 bg-zinc-200 px-2 py-0.5 rounded">
              READY
            </span>
          </div>

          <div className="p-2.5 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <div className="w-6 h-6 rounded-lg bg-zinc-900 text-zinc-400 flex items-center justify-center">
                <Calendar className="w-3.5 h-3.5" />
              </div>
              <div>
                <span className="font-bold text-zinc-900 block text-xs">Studio Call Sheet &amp; Crew Hold</span>
                <span className="text-[10px] text-zinc-500">Call Time 06:30 AM • DOP &amp; Kit Locked</span>
              </div>
            </div>
            <span className="font-mono text-[10px] font-bold text-zinc-900 bg-zinc-100 px-2 py-0.5 rounded">
              SCHEDULED
            </span>
          </div>

        </div>

        {/* Tangible Business Value Footer */}
        <div className="pt-2 border-t border-zinc-100 flex items-center justify-between text-[11px]">
          <span className="text-zinc-500">
            <strong className="text-zinc-900">Zero Admin Overhead</strong>
          </span>
          <span className="text-indigo-600 font-bold font-mono">1 Command ➔ 3 Live Systems</span>
        </div>

      </div>
    );
  }

  // ── CARD 2: 6-TIER RESILIENT MULTI-MODEL MESH (CLAUDE + GEMINI + OPENAI LOGOS) ──
  if (cardType === 2) {
    return (
      <div className="w-full max-w-[480px] bg-white rounded-3xl border border-zinc-200/90 p-5 sm:p-6 shadow-[0_12px_36px_rgba(0,0,0,0.06)] space-y-4 select-none relative overflow-hidden">
        
        {/* Header */}
        <div className="flex items-center justify-between pb-3 border-b border-zinc-100">
          <div className="flex items-center gap-2">
            <div className="w-7 h-7 rounded-xl bg-zinc-950 text-white flex items-center justify-center">
              <Cpu className="w-4 h-4 text-emerald-400" />
            </div>
            <div>
              <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">Autonomous Resilient Mesh</span>
              <span className="text-xs font-bold text-zinc-950">99.99% Studio Uptime Engine</span>
            </div>
          </div>
          <span className="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-mono font-bold border border-emerald-200">
            &lt;80ms FAILOVER
          </span>
        </div>

        {/* 3 Major Model Providers Grid */}
        <div className="space-y-2 text-xs">
          
          <div className="p-2.5 rounded-2xl bg-amber-50/40 border border-amber-200/80 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <div className="w-6 h-6 rounded-lg bg-amber-100 border border-amber-300 flex items-center justify-center p-1">
                <ClaudeLogo className="w-3.5 h-3.5" />
              </div>
              <div>
                <span className="font-bold text-zinc-900 block text-xs">Anthropic Claude 3.5 Sonnet</span>
                <span className="text-[10px] text-zinc-500">Tier 1: Creative Scopes &amp; Complex Legal Terms</span>
              </div>
            </div>
            <span className="flex items-center gap-1 font-mono text-[10px] font-bold text-emerald-700">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" /> PRIMARY
            </span>
          </div>

          <div className="p-2.5 rounded-2xl bg-blue-50/40 border border-blue-200/80 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <div className="w-6 h-6 rounded-lg bg-blue-100 border border-blue-300 flex items-center justify-center p-1">
                <GeminiLogo className="w-3.5 h-3.5" />
              </div>
              <div>
                <span className="font-bold text-zinc-900 block text-xs">Google Gemini 3.5 Flash</span>
                <span className="text-[10px] text-zinc-500">Tier 2: Sub-80ms Vector Extraction &amp; Multimodal</span>
              </div>
            </div>
            <span className="flex items-center gap-1 font-mono text-[10px] font-bold text-blue-700">
              <span className="w-1.5 h-1.5 rounded-full bg-zinc-700" /> ACTIVE
            </span>
          </div>

          <div className="p-2.5 rounded-2xl bg-emerald-50/40 border border-emerald-200/80 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <div className="w-6 h-6 rounded-lg bg-emerald-100 border border-emerald-300 flex items-center justify-center p-1">
                <OpenAILogo className="w-3.5 h-3.5" />
              </div>
              <div>
                <span className="font-bold text-zinc-900 block text-xs">OpenAI GPT-4o Engine</span>
                <span className="text-[10px] text-zinc-500">Tier 3: Parallel Logic &amp; Invoicing Math Auditing</span>
              </div>
            </div>
            <span className="flex items-center gap-1 font-mono text-[10px] font-bold text-zinc-600">
              <span className="w-1.5 h-1.5 rounded-full bg-zinc-400" /> STANDBY
            </span>
          </div>

        </div>

        {/* Business Impact Footer */}
        <div className="pt-2 border-t border-zinc-100 flex items-center justify-between text-[11px]">
          <span className="text-zinc-500">
            <strong className="text-zinc-900">Zero Shoot Downtime</strong>
          </span>
          <span className="text-emerald-700 font-bold font-mono">100% SLA Guarantee</span>
        </div>

      </div>
    );
  }

  // ── CARD 3: REVENUE & COMMERCIAL ROI IMPACT (UPI & IT ACT 2000 LEGAL VERIFICATION) ──
  return (
    <div className="w-full max-w-[480px] bg-white rounded-3xl border border-zinc-200/90 p-5 sm:p-6 shadow-[0_12px_36px_rgba(0,0,0,0.06)] space-y-4 select-none relative overflow-hidden">
      
      {/* Header with Government Legal & UPI Badges */}
      <div className="flex items-center justify-between pb-3 border-b border-zinc-100">
        <div className="flex items-center gap-2">
          <div className="w-7 h-7 rounded-xl bg-emerald-950 text-white flex items-center justify-center">
            <ShieldCheck className="w-4 h-4 text-emerald-400" />
          </div>
          <div>
            <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">Legal &amp; Cashflow Guard</span>
            <span className="text-xs font-bold text-zinc-950">IT Act 2000 • Section 10A</span>
          </div>
        </div>
        <UPILogoBadge />
      </div>

      {/* ROI & Business Value Metric Cards */}
      <div className="grid grid-cols-2 gap-2.5 text-xs">
        
        <div className="p-3 rounded-2xl bg-zinc-50 border border-zinc-200 space-y-1">
          <div className="flex items-center justify-between">
            <span className="text-[10px] font-mono text-zinc-400 uppercase">Deal Cycle</span>
            <span className="text-emerald-700 font-bold text-[10px]">94% FASTER</span>
          </div>
          <div className="font-display font-black text-lg text-zinc-950">
            22 Mins
          </div>
          <span className="text-[10px] text-zinc-500 block">Down from 3.4 days</span>
        </div>

        <div className="p-3 rounded-2xl bg-zinc-50 border border-zinc-200 space-y-1">
          <div className="flex items-center justify-between">
            <span className="text-[10px] font-mono text-zinc-400 uppercase">Payment Delay</span>
            <span className="text-emerald-700 font-bold text-[10px]">0 DISPUTES</span>
          </div>
          <div className="font-display font-black text-lg text-emerald-700">
            Instant UPI
          </div>
          <span className="text-[10px] text-zinc-500 block">50% Advance auto-locked</span>
        </div>

      </div>

      {/* SHA-256 Tamper Evident Banner */}
      <div className="p-2.5 rounded-2xl bg-zinc-50 border border-zinc-200/80 text-center">
        <div className="flex items-center justify-center gap-1.5 text-emerald-950 text-xs font-bold">
          <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
          <span>Court-Admissible SHA-256 Electronic Seal</span>
        </div>
        <span className="font-mono text-[9px] text-emerald-700 block mt-0.5 truncate max-w-[340px] mx-auto">
          Hash: e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b...
        </span>
      </div>

      {/* Bottom Tangible ROI */}
      <div className="pt-2 border-t border-zinc-100 flex items-center justify-between text-[11px]">
        <span className="text-zinc-500 flex items-center gap-1">
          <TrendingUp className="w-3.5 h-3.5 text-emerald-600" />
          <strong className="text-zinc-900">Direct Software Savings</strong>
        </span>
        <span className="text-emerald-700 font-bold font-mono">Save ₹2.5L+ Yearly</span>
      </div>

    </div>
  );
}
