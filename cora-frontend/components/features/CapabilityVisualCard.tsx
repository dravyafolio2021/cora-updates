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
  UserCheck
} from 'lucide-react';
import { FeatureModule } from '@/lib/features-data';

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

  // ── CARD 0: ENTITY DETAILS & CUSTOM STUDIO FIELDS (CLICKUP ROW 1 REFERENCE) ──
  if (cardType === 0) {
    return (
      <div className="w-full max-w-[480px] bg-white rounded-2xl border border-zinc-200/90 p-5 sm:p-6 shadow-[0_8px_24px_rgba(0,0,0,0.05)] space-y-4 select-none">
        
        {/* Breadcrumb & Sub-meta */}
        <div className="flex items-center justify-between text-[11px] text-zinc-500 pb-2 border-b border-zinc-100">
          <div className="flex items-center gap-1.5 font-medium">
            <span className="w-2 h-2 rounded-sm bg-rose-500 inline-block" />
            <span className="text-zinc-500">{feature.categoryLabel} /</span>
            <span className="text-zinc-900 font-bold truncate max-w-[160px]">{feature.shortTitle}</span>
          </div>
          <div className="flex items-center gap-2 font-mono text-[10px] text-zinc-400">
            <span>💬 3</span>
            <span>📎 2</span>
          </div>
        </div>

        {/* Title */}
        <div>
          <h4 className="font-display text-base sm:text-lg font-bold text-zinc-950">
            {cap.title}
          </h4>
        </div>

        {/* Metadata Grid */}
        <div className="grid grid-cols-2 gap-2 text-xs">
          <div className="flex items-center justify-between p-2 rounded-xl bg-zinc-50 border border-zinc-200/70">
            <span className="text-zinc-400 text-[10px] font-mono">Status</span>
            <span className="px-2 py-0.5 rounded bg-zinc-950 text-white font-mono text-[10px] font-bold">
              IN PROGRESS ▸
            </span>
          </div>
          <div className="flex items-center justify-between p-2 rounded-xl bg-zinc-50 border border-zinc-200/70">
            <span className="text-zinc-400 text-[10px] font-mono">Lead</span>
            <span className="font-medium text-zinc-900 text-[11px]">Kavya Patel</span>
          </div>
          <div className="flex items-center justify-between p-2 rounded-xl bg-zinc-50 border border-zinc-200/70">
            <span className="text-zinc-400 text-[10px] font-mono">Call Time</span>
            <span className="font-medium text-zinc-900 text-[11px]">Tomorrow 06:30</span>
          </div>
          <div className="flex items-center justify-between p-2 rounded-xl bg-zinc-50 border border-zinc-200/70">
            <span className="text-zinc-400 text-[10px] font-mono">Valuation</span>
            <span className="font-mono font-bold text-emerald-700 text-[11px]">₹4,50,000</span>
          </div>
        </div>

        {/* Studio Fields */}
        <div className="pt-2 border-t border-zinc-100 space-y-1 text-[11px]">
          <div className="flex items-center justify-between py-1 px-2 rounded hover:bg-zinc-50 transition-colors">
            <span className="text-zinc-500">Core Engine</span>
            <span className="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-mono font-bold text-[10px]">
              {cap.tag}
            </span>
          </div>
          <div className="flex items-center justify-between py-1 px-2 rounded hover:bg-zinc-50 transition-colors">
            <span className="text-zinc-500">Audit Status</span>
            <span className="text-emerald-700 font-medium flex items-center gap-1 text-[10px]">
              <CheckCircle2 className="w-3 h-3 text-emerald-600" /> 100% Verified
            </span>
          </div>
        </div>

      </div>
    );
  }

  // ── CARD 1: RELATIONSHIPS & CONNECTED WORKSPACE (CLICKUP ROW 2 REFERENCE) ──
  if (cardType === 1) {
    return (
      <div className="w-full max-w-[480px] bg-white rounded-2xl border border-zinc-200/90 p-5 sm:p-6 shadow-[0_8px_24px_rgba(0,0,0,0.05)] space-y-4 select-none">
        
        {/* Tabs Bar */}
        <div className="flex items-center gap-2 pb-2 border-b border-zinc-100">
          <span className="px-3 py-1 rounded-lg bg-zinc-950 text-white text-[11px] font-bold flex items-center gap-1.5 shadow-2xs">
            <Layers className="w-3 h-3 text-amber-300" /> Relationships
          </span>
          <span className="px-3 py-1 rounded-lg text-zinc-500 text-[11px] font-medium">
            References
          </span>
        </div>

        {/* Connected Operations */}
        <div className="space-y-2 text-xs">
          <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 block">
            Connected Entities
          </span>
          <div className="space-y-1.5">
            <div className="flex items-center justify-between p-2 rounded-xl bg-zinc-50 border border-zinc-200/80">
              <div className="flex items-center gap-2">
                <span className="w-2 h-2 rounded-full bg-purple-500" />
                <span className="font-semibold text-zinc-900">Commercial Production Scope</span>
              </div>
              <span className="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-mono text-[9px] font-bold">
                ✓ SIGNED
              </span>
            </div>

            <div className="flex items-center justify-between p-2 rounded-xl bg-zinc-50 border border-zinc-200/80">
              <div className="flex items-center gap-2">
                <span className="w-2 h-2 rounded-full bg-blue-500" />
                <span className="font-semibold text-zinc-900">18% GST Invoice #INV-089</span>
              </div>
              <span className="font-mono text-zinc-700 text-[10px]">₹5,31,000</span>
            </div>

            <div className="flex items-center justify-between p-2 rounded-xl bg-zinc-50 border border-zinc-200/80">
              <div className="flex items-center gap-2">
                <span className="w-2 h-2 rounded-full bg-emerald-500" />
                <span className="font-semibold text-zinc-900">Sony FX6 Cinema Kit #A</span>
              </div>
              <span className="text-zinc-500 text-[10px]">No Conflicts</span>
            </div>
          </div>
        </div>

        {/* Vault Docs */}
        <div className="pt-2 border-t border-zinc-100 space-y-1 text-xs">
          <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 block">
            Vault Documents
          </span>
          <div className="flex items-center justify-between text-[11px] text-zinc-700 py-0.5">
            <span className="flex items-center gap-1.5 truncate">
              <FileText className="w-3.5 h-3.5 text-zinc-400 shrink-0" /> Commercial MSA Agreement.pdf
            </span>
            <span className="font-mono text-[10px] text-zinc-400 shrink-0">2.4 MB</span>
          </div>
          <div className="flex items-center justify-between text-[11px] text-zinc-700 py-0.5">
            <span className="flex items-center gap-1.5 truncate">
              <Receipt className="w-3.5 h-3.5 text-zinc-400 shrink-0" /> Tax_Invoice_INV2026.pdf
            </span>
            <span className="font-mono text-[10px] text-zinc-400 shrink-0">1.1 MB</span>
          </div>
        </div>

      </div>
    );
  }

  // ── CARD 2: AUTOMATED WORKFLOW TRIGGER ENGINE ──
  if (cardType === 2) {
    return (
      <div className="w-full max-w-[480px] bg-white rounded-2xl border border-zinc-200/90 p-5 sm:p-6 shadow-[0_8px_24px_rgba(0,0,0,0.05)] space-y-4 select-none">
        
        <div className="flex items-center justify-between text-[11px] text-zinc-500 pb-2 border-b border-zinc-100">
          <span className="font-bold text-zinc-900 flex items-center gap-1.5">
            <Sparkles className="w-3.5 h-3.5 text-amber-500" /> Automated Workflow Trigger
          </span>
          <span className="text-emerald-700 font-mono font-bold text-[10px]">100% SUCCESS</span>
        </div>

        <div className="p-3.5 rounded-xl bg-zinc-50 border border-zinc-200 space-y-2.5">
          <div className="flex items-center justify-between text-xs">
            <span className="text-zinc-500">Trigger Condition:</span>
            <span className="font-mono font-bold text-zinc-950">E-Sign Execution</span>
          </div>
          
          <div className="space-y-1.5 text-[11px] text-zinc-700">
            <div className="flex items-center gap-2 p-1.5 rounded-lg bg-white border border-zinc-200/70">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0" />
              <span className="truncate">Generate 18% GST invoice automatically</span>
            </div>
            <div className="flex items-center gap-2 p-1.5 rounded-lg bg-white border border-zinc-200/70">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0" />
              <span className="truncate">Lock Sony FX6 Kit #A &amp; DOP on Master Calendar</span>
            </div>
            <div className="flex items-center gap-2 p-1.5 rounded-lg bg-white border border-zinc-200/70">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0" />
              <span className="truncate">Dispatch WhatsApp reminder with UPI QR</span>
            </div>
          </div>
        </div>

        <div className="flex items-center justify-between text-[10px] font-mono text-zinc-400 pt-1 border-t border-zinc-100">
          <span>Latency: 380ms</span>
          <span>Zero-Lag Webhooks</span>
        </div>

      </div>
    );
  }

  // ── CARD 3: SECURITY, AUDIT & LEGAL SEAL ──
  return (
    <div className="w-full max-w-[480px] bg-white rounded-2xl border border-zinc-200/90 p-5 sm:p-6 shadow-[0_8px_24px_rgba(0,0,0,0.05)] space-y-4 select-none">
      
      <div className="flex items-center justify-between text-[11px] text-zinc-500 pb-2 border-b border-zinc-100">
        <span className="font-bold text-zinc-950 flex items-center gap-1.5">
          <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" /> Security &amp; Compliance Audit
        </span>
        <span className="text-emerald-700 font-bold text-[10px]">VERIFIED</span>
      </div>

      <div className="space-y-2 text-xs">
        <div className="p-2.5 rounded-xl bg-zinc-50 border border-zinc-200/80 space-y-1">
          <div className="flex justify-between font-mono text-[10px]">
            <span className="text-zinc-500">CRYPTOGRAPHIC HASH</span>
            <span className="text-emerald-700 font-bold">SHA-256</span>
          </div>
          <div className="font-mono text-[10px] text-zinc-900 truncate">
            e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855
          </div>
        </div>

        <div className="grid grid-cols-2 gap-2 text-[11px]">
          <div className="p-2 rounded-lg bg-zinc-50 border border-zinc-200/70">
            <span className="text-zinc-400 text-[10px] font-mono block">Tenant Isolation</span>
            <span className="font-bold text-zinc-900">100% Private</span>
          </div>
          <div className="p-2 rounded-lg bg-zinc-50 border border-zinc-200/70">
            <span className="text-zinc-400 text-[10px] font-mono block">Legal Framework</span>
            <span className="font-bold text-zinc-900">IT Act 2000</span>
          </div>
        </div>
      </div>

      <div className="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
        <span className="text-[11px] font-bold text-emerald-800 flex items-center justify-center gap-1">
          <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" /> Immutable &amp; Tamper-Proof Audit
        </span>
      </div>

    </div>
  );
}
