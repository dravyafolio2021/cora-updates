'use client';

import React from 'react';
import { 
  Calculator, 
  Sparkles, 
  Code, 
  Scale, 
  Receipt, 
  QrCode, 
  Check, 
  Copy, 
  ArrowRight,
  ShieldCheck,
  Zap,
  Lock
} from 'lucide-react';

interface ToolCardVisualProps {
  slug: string;
}

export function ToolCardVisual({ slug }: ToolCardVisualProps) {
  
  // ── 1. GST CALCULATOR ──
  if (slug === 'gst-calculator') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F0FDF4] via-[#DCFCE7] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-emerald-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(16,185,129,0.08)] border border-emerald-100">
            <div className="flex items-center justify-between text-[10px] font-mono mb-1.5">
              <span className="font-bold text-emerald-700">18% GST (SAC 9983)</span>
              <span className="bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded text-[9px] font-bold">CGST + SGST</span>
            </div>
            <div className="flex items-center justify-between text-xs font-mono font-bold text-zinc-900">
              <span>Base: ₹50,000</span>
              <span className="text-emerald-600">Total: ₹59,000</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-xs flex items-center gap-1.5">
            <Copy className="w-3 h-3 text-emerald-400" />
            <span>1-Click Proposal Breakdown Copy</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 2. RETAINER CALCULATOR ──
  if (slug === 'retainer-calculator') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#EFF6FF] via-[#DBEAFE] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-blue-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(37,99,235,0.08)] border border-blue-100">
            <div className="flex items-center justify-between text-[10px] font-mono mb-1.5">
              <span className="font-bold text-blue-700">MONTHLY CAPACITY MATH</span>
              <span className="bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded text-[9px] font-bold">40 Hrs/Wk</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Optimal Retainer Tier</span>
              <span className="text-blue-600 font-mono">₹1,50,000/mo</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-xs">
            🛡️ Auto +20% Scope Creep Buffer
          </div>
        </div>
      </div>
    );
  }

  // ── 3. LISTING AI GENERATOR ──
  if (slug === 'listing-ai') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#FAF5FF] via-[#F3E8FF] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-purple-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(168,85,247,0.08)] border border-purple-100">
            <div className="flex items-center justify-between text-[10px] font-mono mb-1.5">
              <span className="font-bold text-purple-700 flex items-center gap-1">
                <Sparkles className="w-3 h-3 text-purple-500" />
                AI PROMPT ENGINE
              </span>
              <span className="bg-purple-100 text-purple-800 px-1.5 py-0.5 rounded text-[9px] font-bold">Luxury Tone</span>
            </div>
            <p className="text-[11px] font-bold text-zinc-900 truncate">
              &ldquo;Sea-Facing 3BHK Penthouse • Bandra West&rdquo;
            </p>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-xs">
            ⚡ Brochure, Reel Script &amp; Ad Copy
          </div>
        </div>
      </div>
    );
  }

  // ── 4. CONTRACT CLAUSE BUILDER ──
  if (slug === 'contract-builder') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F8FAFC] via-[#F1F5F9] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-slate-200">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(100,116,139,0.08)] border border-slate-200">
            <div className="flex items-center justify-between text-[10px] font-mono text-slate-600 mb-1.5">
              <span className="font-bold text-slate-900 flex items-center gap-1">
                <Scale className="w-3 h-3 text-slate-700" />
                IT ACT 2000 SEC 10A
              </span>
              <span className="text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded font-bold">SHA-256</span>
            </div>
            <p className="text-xs font-bold text-zinc-900 truncate">
              Scope Lock &amp; Escrow Milestone Deed
            </p>
          </div>
          <div className="flex justify-center text-[10px] font-mono text-zinc-600">
            🔒 Legally Binding Digital Enforcement
          </div>
        </div>
      </div>
    );
  }

  // ── 5. EMBED BUILDER ──
  if (slug === 'embed-builder') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#FFFBEB] via-[#FEF3C7] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-amber-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-zinc-950 text-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(245,158,11,0.08)] border border-zinc-800 font-mono text-[10px]">
            <div className="flex items-center justify-between text-zinc-400 mb-1.5">
              <span className="text-amber-400 font-bold">&lt;iframe /&gt;</span>
              <span>Framer • Webflow</span>
            </div>
            <p className="text-zinc-300 truncate">
              src=&quot;https://app.heycora.in/embed/lead-form&quot;
            </p>
          </div>
          <div className="flex items-center justify-between px-2 text-[10px] font-mono text-zinc-600 bg-white/90 rounded-xl p-1.5 border border-zinc-200">
            <span>Live Copilot Widget:</span>
            <span className="font-bold text-emerald-600">✓ 0ms Lag</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 6. UPI QR GENERATOR ──
  if (slug === 'upi-qr-generator') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F0FDFA] via-[#CCFBF1] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-teal-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(20,184,166,0.08)] border border-teal-100">
            <div className="flex items-center justify-between text-[10px] font-mono mb-1.5">
              <span className="font-bold text-teal-700 flex items-center gap-1">
                <QrCode className="w-3 h-3" />
                UPI INTENT 2.0
              </span>
              <span className="bg-teal-100 text-teal-800 px-1.5 py-0.5 rounded text-[9px] font-bold">0% PG Fees</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Direct Bank Settlement</span>
              <span className="text-teal-600 font-mono">GPay / PhonePe</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-xs">
            ⚡ Instant Dynamic QR Generation
          </div>
        </div>
      </div>
    );
  }

  return null;
}
