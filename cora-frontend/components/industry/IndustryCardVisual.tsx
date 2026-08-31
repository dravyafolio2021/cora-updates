'use client';

import React from 'react';
import { 
  Code, 
  LayoutTemplate, 
  ShieldCheck, 
  Zap, 
  Scale, 
  Receipt, 
  Briefcase, 
  Layers, 
  BarChart2, 
  Sparkles, 
  Camera, 
  Building2, 
  Heart, 
  Scissors,
  CheckCircle2,
  Lock,
  ArrowRight,
  Clock,
  Calendar,
  MousePointer,
  Cpu,
  FileText
} from 'lucide-react';

interface IndustryCardVisualProps {
  slug: string;
  title: string;
}

export function IndustryCardVisual({ slug, title }: IndustryCardVisualProps) {
  
  // ── 1. SOFTWARE AGENCIES (Sprint Retainers, Staging Gates & Escrow) ──
  if (slug === 'software-agencies') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F0F6FF] via-[#E4EFFE] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-blue-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          {/* Top Sprint Pill */}
          <div className="flex items-center justify-between px-1">
            <span className="px-2.5 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-mono font-bold shadow-2xs flex items-center gap-1.5">
              <span className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
              Sprint 14 Active
            </span>
            <span className="text-[10px] font-mono text-blue-900 bg-white/80 border border-blue-200 px-2 py-0.5 rounded-md">
              SAC 998314
            </span>
          </div>

          {/* Staging Approval Card */}
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(37,99,235,0.08)] border border-blue-100/80">
            <div className="flex items-center justify-between text-[11px] font-bold text-zinc-900 mb-1">
              <span>Staging Deployment Sign-Off</span>
              <span className="text-emerald-600 font-mono text-[10px]">✓ Verified</span>
            </div>
            <div className="flex items-center justify-between text-[10px] text-zinc-500 pt-1 border-t border-zinc-100">
              <span>SHA-256 Escrow Release</span>
              <span className="font-mono font-bold text-zinc-900">₹2,50,000 + 18% GST</span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 2. WEB & APP STUDIOS (4-Phase Wireframe to Launch Gates) ──
  if (slug === 'web-app-studios') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F5F3FF] via-[#EAE5FE] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-indigo-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(99,102,241,0.08)] border border-indigo-100">
            <div className="flex items-center justify-between text-[10px] font-bold text-indigo-700 mb-2">
              <span>4-PHASE MILESTONE TRACKER</span>
              <span className="bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200/60">50% Advance</span>
            </div>
            {/* Step Indicators */}
            <div className="grid grid-cols-4 gap-1 text-center text-[9px] font-mono">
              <div className="bg-emerald-500 text-white py-1 rounded-md font-bold">Scope ✓</div>
              <div className="bg-indigo-600 text-white py-1 rounded-md font-bold">Figma</div>
              <div className="bg-zinc-100 text-zinc-600 py-1 rounded-md">Build</div>
              <div className="bg-zinc-100 text-zinc-400 py-1 rounded-md">Launch</div>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-xs flex items-center gap-1.5">
            <Sparkles className="w-3 h-3 text-amber-300" />
            <span>Zero Scope Creep Protection</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 3. IT & TECH SERVICES (99.9% Uptime SLAs & Hardware Assets) ──
  if (slug === 'it-tech-services') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F0FDF4] via-[#DCFCE7] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-emerald-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(16,185,129,0.08)] border border-emerald-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-400 mb-1.5">
              <span className="font-bold text-emerald-700 flex items-center gap-1">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                99.98% UPTIME SLA
              </span>
              <span>24/7 Monitored</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Managed Cloud Retainer</span>
              <span className="text-[11px] font-mono text-emerald-600">₹1.20L/mo</span>
            </div>
          </div>
          <div className="flex items-center justify-between px-1 text-[10px] font-mono text-zinc-600 bg-white/90 rounded-xl p-1.5 border border-zinc-200">
            <span>Hardware Registry:</span>
            <span className="font-bold text-zinc-900">42 Assigned Devices</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 4. AI & AUTOMATION AGENCIES (Autonomous Agents & Token Ledgers) ──
  if (slug === 'ai-automation') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#FFFBEB] via-[#FEF3C7] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-amber-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(245,158,11,0.08)] border border-amber-100">
            <div className="flex items-center justify-between text-[10px] font-mono mb-1.5">
              <span className="font-bold text-amber-700 flex items-center gap-1">
                <Zap className="w-3 h-3 text-amber-500 fill-amber-500" />
                AI AGENT WORKFLOW
              </span>
              <span className="bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded text-[9px] font-bold">RAG MCP</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Living Context Sync</span>
              <span className="text-[10px] font-mono text-zinc-500">1.2M Tokens</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-xs">
            🔒 Enterprise Zero Data Training Deed
          </div>
        </div>
      </div>
    );
  }

  // ── 5. LAWYERS & LAW FIRMS (SHA-256 E-Sign Vaults & Retainers) ──
  if (slug === 'lawyers-law-firms') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F8FAFC] via-[#F1F5F9] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-slate-200">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(100,116,139,0.08)] border border-slate-200">
            <div className="flex items-center justify-between text-[10px] font-mono text-slate-600 mb-1.5">
              <span className="font-bold text-slate-900 flex items-center gap-1">
                <Scale className="w-3 h-3 text-slate-700" />
                IT ACT 2000 VALID
              </span>
              <span className="text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded font-bold">SHA-256</span>
            </div>
            <p className="text-xs font-bold text-zinc-900 truncate">
              Corporate Legal Retainer Agreement
            </p>
          </div>
          <div className="flex items-center justify-between px-2 py-1 bg-slate-900 text-white rounded-xl text-[10px] font-mono">
            <span>RCM Reverse Charge:</span>
            <span className="text-emerald-400 font-bold">18% Compliant</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 6. TAX & CA PRACTICES (18% GST Auto-Math & Dynamic UPI) ──
  if (slug === 'tax-ca-firms') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F0FDF4] via-[#DCFCE7] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-emerald-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(16,185,129,0.08)] border border-emerald-100">
            <div className="flex items-center justify-between text-[10px] font-mono mb-1.5">
              <span className="font-bold text-emerald-700">SAC 998222 AUDIT</span>
              <span className="bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded text-[9px] font-bold">UPI QR</span>
            </div>
            <div className="flex justify-between text-xs font-mono text-zinc-900 font-bold">
              <span>CGST 9% + SGST 9%</span>
              <span className="text-emerald-600">₹3,54,000</span>
            </div>
          </div>
          <div className="flex items-center justify-between px-2 text-[10px] font-mono text-zinc-500">
            <span>ICAI Aligned SA-210</span>
            <span className="text-emerald-600 font-bold">✓ Ready to File</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 7. FINANCIAL ADVISORS (SEBI Risk Profiles & Wealth Retainers) ──
  if (slug === 'financial-advisors') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#EEF2FF] via-[#E0E7FF] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-indigo-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(99,102,241,0.08)] border border-indigo-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-indigo-700 mb-1.5">
              <span className="font-bold">SEBI RIA PROFILE</span>
              <span className="font-bold text-zinc-900">Score 84/100</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Wealth Advisory Fee</span>
              <span className="text-indigo-600 font-mono">1.0% AUM</span>
            </div>
          </div>
          <div className="self-center bg-indigo-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-xs">
            🔒 Encrypted Portfolio Vault
          </div>
        </div>
      </div>
    );
  }

  // ── 8. AUDIT & COMPLIANCE (30-Point Capability Matrix) ──
  if (slug === 'audit-compliance') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#FAF5FF] via-[#F3E8FF] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-purple-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(168,85,247,0.08)] border border-purple-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-purple-700 mb-1.5">
              <span className="font-bold">30-POINT AUDIT MATRIX</span>
              <span className="text-emerald-600 font-bold">100% Passed</span>
            </div>
            <div className="space-y-1 text-[10px] text-zinc-600 font-medium">
              <div className="flex items-center justify-between"><span>Access Controls</span><span className="text-emerald-600">✓</span></div>
              <div className="flex items-center justify-between"><span>Financial Proofs</span><span className="text-emerald-600">✓</span></div>
            </div>
          </div>
          <div className="flex justify-center text-[10px] font-mono text-purple-800 font-bold">
            Multi-Branch Isolation Active
          </div>
        </div>
      </div>
    );
  }

  // ── 9. MARKETING & SEO (Monthly Retainers & Viral AI Copy) ──
  if (slug === 'marketing-seo') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F0F9FF] via-[#E0F2FE] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-sky-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(14,165,233,0.08)] border border-sky-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-sky-700 mb-1.5">
              <span className="font-bold">ROAS 4.8X TRACKER</span>
              <span className="bg-sky-100 text-sky-800 px-1.5 py-0.5 rounded text-[9px] font-bold">Meta + Google</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Growth Retainer</span>
              <span className="text-sky-600 font-mono">₹1.50L/mo</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-xs flex items-center gap-1.5">
            <Sparkles className="w-3 h-3 text-amber-300" />
            <span>White-Label Client Dashboards</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 10. DESIGN & UI/UX (2-Week Design Sprints & Gated Assets) ──
  if (slug === 'design-uiux') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F5F3FF] via-[#EDE9FE] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-violet-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(139,92,246,0.08)] border border-violet-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-violet-700 mb-1.5">
              <span className="font-bold">2-WEEK SPRINT GATE</span>
              <span className="bg-violet-100 text-violet-800 px-1.5 py-0.5 rounded text-[9px] font-bold">Figma</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>High-Res Brand Package</span>
              <span className="text-emerald-600 text-[10px]">🔒 Unlocks on Pay</span>
            </div>
          </div>
          <div className="flex justify-center text-[10px] font-mono text-zinc-500">
            Copyright Deed: Indian Copyright Act 1957
          </div>
        </div>
      </div>
    );
  }

  // ── 11. PHOTO & VIDEO STUDIOS (4K Proofing & Call Sheets) ──
  if (slug === 'photo-video-studios') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#FFF1F2] via-[#FFE4E6] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-rose-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(244,63,94,0.08)] border border-rose-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-rose-700 mb-1.5">
              <span className="font-bold">4K PROOFING VAULT</span>
              <span className="bg-rose-100 text-rose-800 px-1.5 py-0.5 rounded text-[9px] font-bold">50% Advance</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Crew Call Sheet Ready</span>
              <span className="text-rose-600 font-mono">8K RAW</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-xs">
            🎥 Barcoded Cinema Gear Inventory
          </div>
        </div>
      </div>
    );
  }

  // ── 12. ARCHITECTURE & INTERIORS (3D CAD & Progressive Draws) ──
  if (slug === 'architecture-interiors') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#FFF7ED] via-[#FFEDD5] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-orange-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(249,115,22,0.08)] border border-orange-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-orange-700 mb-1.5">
              <span className="font-bold">PROGRESSIVE DRAW #2</span>
              <span className="bg-orange-100 text-orange-800 px-1.5 py-0.5 rounded text-[9px] font-bold">30% 3D CAD</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Good-for-Construction (GFC)</span>
              <span className="text-emerald-600 font-bold">✓ Signed</span>
            </div>
          </div>
          <div className="flex justify-center text-[10px] font-mono text-orange-800 font-bold">
            Site Change-Order Addendum Protected
          </div>
        </div>
      </div>
    );
  }

  // ── 13. CONSULTANTS & ADVISORS (Voice-to-Scope & Strategy Decks) ──
  if (slug === 'consultants-advisors') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#EEF2FF] via-[#E0E7FF] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-indigo-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(99,102,241,0.08)] border border-indigo-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-indigo-700 mb-1.5">
              <span className="font-bold">VOICE-TO-SCOPE AI</span>
              <span className="bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded text-[9px] font-bold">2 Min Proposal</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Corporate Strategy Mandate</span>
              <span className="text-indigo-600 font-mono">₹4.00L/mo</span>
            </div>
          </div>
          <div className="self-center bg-indigo-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-xs">
            📊 Executive Board Presentation Depository
          </div>
        </div>
      </div>
    );
  }

  // ── 14. DOCTORS & CLINICS (Digital Patient Intake & Consent) ──
  if (slug === 'doctors-clinics') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F0FDFA] via-[#CCFBF1] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-teal-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(20,184,166,0.08)] border border-teal-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-teal-700 mb-1.5">
              <span className="font-bold">DIGITAL INTAKE FORM</span>
              <span className="text-emerald-600 font-bold">100% Paperless</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Medical Consent Sign-Off</span>
              <span className="text-teal-600 font-mono">WhatsApp Alert</span>
            </div>
          </div>
          <div className="flex justify-center text-[10px] font-mono text-zinc-500">
            🔒 AES-256 Confidential Patient Vault
          </div>
        </div>
      </div>
    );
  }

  // ── 15. SALONS & WELLNESS (Bridal Packages & Dynamic UPI QR) ──
  if (slug === 'salons-wellness') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#FDF2F8] via-[#FCE7F3] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-pink-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(236,72,153,0.08)] border border-pink-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-pink-700 mb-1.5">
              <span className="font-bold">BRIDAL BEAUTY PACKAGE</span>
              <span className="bg-pink-100 text-pink-800 px-1.5 py-0.5 rounded text-[9px] font-bold">50% Deposit</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Stylist Chair Booked</span>
              <span className="text-pink-600 font-mono">UPI QR</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-xs">
            ⭐ Automated 5-Star Google Reviews
          </div>
        </div>
      </div>
    );
  }

  // ── 16. REAL ESTATE & PROPERTY (Listing Microsites & Brokerage Mandates) ──
  if (slug === 'real-estate-property') {
    return (
      <div className="w-full h-44 bg-gradient-to-b from-[#F0FDF4] via-[#DCFCE7] to-white relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-emerald-100/60">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(16,185,129,0.08)] border border-emerald-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-emerald-700 mb-1.5">
              <span className="font-bold">BROKERAGE MANDATE</span>
              <span className="text-emerald-600 font-bold">100% Signed</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>4K Virtual Tour Microsite</span>
              <span className="text-emerald-600 font-mono">Listing AI</span>
            </div>
          </div>
          <div className="flex justify-center text-[10px] font-mono text-zinc-500">
            SAC 997212 Real Estate Brokerage
          </div>
        </div>
      </div>
    );
  }

  // Default Fallback
  return (
    <div className="w-full h-44 bg-zinc-50 flex items-center justify-center rounded-t-3xl border-b border-zinc-100">
      <span className="text-xs font-mono text-zinc-400">{title} Workspace</span>
    </div>
  );
}
