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
  Cpu
} from 'lucide-react';
import { FeatureModule } from '@/lib/features-data';

// ── OFFICIAL BRAND LOGOS (VECTOR SVGS) ──

export function ClaudeLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path
        d="M17.472 2a.75.75 0 0 1 .71.512l1.642 5.053a.75.75 0 0 1-.22.788l-4.088 3.395 3.916 3.593a.75.75 0 0 1 .184.8l-1.854 4.978a.75.75 0 0 1-1.345.105l-3.415-4.07-3.415 4.07a.75.75 0 0 1-1.345-.105l-1.854-4.978a.75.75 0 0 1 .184-.8l3.916-3.593-4.088-3.395a.75.75 0 0 1-.22-.788L7.3 2.512A.75.75 0 0 1 8.01 2h9.462z"
        fill="#D97706"
      />
    </svg>
  );
}

export function GeminiLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path
        d="M12 0C12 6.627 6.627 12 0 12C6.627 12 12 17.373 12 24C12 17.373 17.373 12 24 12C17.373 12 12 6.627 12 0Z"
        fill="url(#gemini-grad-card)"
      />
      <defs>
        <linearGradient id="gemini-grad-card" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stopColor="#4E82EE" />
          <stop offset="50%" stopColor="#9B72CB" />
          <stop offset="100%" stopColor="#D96570" />
        </linearGradient>
      </defs>
    </svg>
  );
}

export function OpenAILogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path
        d="M22.2819 9.8211a5.9847 5.9847 0 0 0-.5157-4.9108 6.0462 6.0462 0 0 0-6.5098-2.9A6.0651 6.0651 0 0 0 4.9807 4.1818a5.9847 5.9847 0 0 0-3.9977 2.9 6.0462 6.0462 0 0 0 .7427 7.0966 5.98 5.98 0 0 0 .511 4.9107 6.051 6.051 0 0 0 6.5146 2.9001A5.9847 5.9847 0 0 0 13.259 24a6.0557 6.0557 0 0 0 5.7718-4.2058 5.9894 5.9894 0 0 0 3.9977-2.9001 6.0557 6.0557 0 0 0-.7466-7.0729z"
        fill="#10A37F"
      />
    </svg>
  );
}

export function WhatsAppLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path
        d="M12.031 0C5.395 0 0 5.395 0 12.031c0 2.12.553 4.188 1.604 6.01L.062 24l6.143-1.61A12.03 12.03 0 0 0 12.03 24c6.637 0 12.031-5.395 12.031-12.031S18.668 0 12.031 0z"
        fill="#25D366"
      />
      <path
        d="M17.5 14.3c-.3-.15-1.78-.88-2.06-.98-.28-.1-.48-.15-.68.15-.2.3-.78.98-.95 1.18-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.68-1.64-.93-2.25-.24-.59-.49-.51-.68-.52h-.58c-.2 0-.52.07-.8.37-.27.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.11c.15.2 2.12 3.24 5.14 4.54.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.43.25-.7.25-1.3.18-1.43-.08-.13-.28-.2-.58-.35z"
        fill="#FFFFFF"
      />
    </svg>
  );
}

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
          <div className="p-2.5 rounded-2xl bg-gradient-to-r from-amber-50/60 to-transparent border border-amber-200/60 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <span className="w-2 h-2 rounded-full bg-amber-500 shrink-0" />
              <div>
                <span className="font-bold text-zinc-900 block text-xs">Raymond Commercial Shoot</span>
                <span className="text-[10px] text-zinc-500 font-mono">Matched Rate Card: ₹4.5L/Day • Mumbai</span>
              </div>
            </div>
            <span className="font-mono text-[10px] font-bold text-amber-800 bg-amber-100/80 px-2 py-0.5 rounded">
              RECALLED
            </span>
          </div>

          <div className="p-2.5 rounded-2xl bg-gradient-to-r from-blue-50/60 to-transparent border border-blue-200/60 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <span className="w-2 h-2 rounded-full bg-blue-500 shrink-0" />
              <div>
                <span className="font-bold text-zinc-900 block text-xs">Cinema Kit #A (Sony FX6 4K)</span>
                <span className="text-[10px] text-zinc-500 font-mono">Calendar Verified: 0 Booking Conflicts</span>
              </div>
            </div>
            <span className="font-mono text-[10px] font-bold text-blue-800 bg-blue-100/80 px-2 py-0.5 rounded">
              LOCKED
            </span>
          </div>

          <div className="p-2.5 rounded-2xl bg-gradient-to-r from-purple-50/60 to-transparent border border-purple-200/60 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <span className="w-2 h-2 rounded-full bg-purple-500 shrink-0" />
              <div>
                <span className="font-bold text-zinc-900 block text-xs">18% GST (CGST 9% + SGST 9%)</span>
                <span className="text-[10px] text-zinc-500 font-mono">SAC 998314 • Tax: ₹81,000</span>
              </div>
            </div>
            <span className="font-mono text-[10px] font-bold text-purple-800 bg-purple-100/80 px-2 py-0.5 rounded">
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
            <div className="w-7 h-7 rounded-xl bg-zinc-950 text-white flex items-center justify-center text-xs font-bold shadow-xs">
              🎙️
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
              <div className="w-6 h-6 rounded-lg bg-purple-950 text-purple-300 flex items-center justify-center">
                <Calendar className="w-3.5 h-3.5" />
              </div>
              <div>
                <span className="font-bold text-zinc-900 block text-xs">Studio Call Sheet &amp; Crew Hold</span>
                <span className="text-[10px] text-zinc-500">Call Time 06:30 AM • DOP &amp; Kit Locked</span>
              </div>
            </div>
            <span className="font-mono text-[10px] font-bold text-purple-700 bg-purple-100 px-2 py-0.5 rounded">
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
              <span className="w-1.5 h-1.5 rounded-full bg-blue-500" /> ACTIVE
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
      <div className="p-2.5 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 border border-emerald-200 text-center">
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
