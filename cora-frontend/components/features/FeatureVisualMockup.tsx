'use client';

import React, { useState } from 'react';
import { 
  Sparkles, 
  Lock, 
  Receipt, 
  Calendar, 
  CheckCircle2, 
  ArrowRight, 
  Layers, 
  Send, 
  FileText, 
  QrCode, 
  Clock, 
  ShieldCheck, 
  Download, 
  Copy, 
  Smartphone,
  Check
} from 'lucide-react';
import { FeatureModule } from '@/lib/features-data';

interface FeatureVisualMockupProps {
  feature: FeatureModule;
}

export function FeatureVisualMockup({ feature }: FeatureVisualMockupProps) {
  const [copiedHash, setCopiedHash] = useState(false);

  const copyHash = () => {
    navigator.clipboard?.writeText('e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855');
    setCopiedHash(true);
    setTimeout(() => setCopiedHash(false), 2000);
  };

  // ── 1. E-SIGN VAULT & CONTRACTS (AUTHENTIC LEGAL DOCUMENT & CRYPTOGRAPHIC SEAL) ──
  if (feature.slug === 'esign-vault' || feature.slug === 'docs-portal') {
    return (
      <div className="w-full bg-white rounded-2xl p-4 sm:p-7 border border-zinc-200/90 shadow-2xs space-y-6">
        
        {/* Document Header & Security Badges */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-zinc-200">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center shadow-xs shrink-0">
              <Lock className="w-5 h-5 text-emerald-400" />
            </div>
            <div>
              <div className="flex items-center gap-2">
                <span className="text-xs font-mono font-bold text-zinc-950">DOC-2026-042</span>
                <span className="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-mono font-bold flex items-center gap-1">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                  100% IT ACT 2000 COMPLIANT
                </span>
              </div>
              <h4 className="font-display text-sm sm:text-base font-bold text-zinc-900 mt-0.5">
                Commercial Film &amp; Photography Master Services Agreement
              </h4>
            </div>
          </div>

          <div className="flex items-center gap-2 text-xs">
            <button
              type="button"
              onClick={copyHash}
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 font-mono text-[11px] text-zinc-600 transition-colors cursor-pointer"
            >
              {copiedHash ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Copy className="w-3.5 h-3.5 text-zinc-400" />}
              <span>{copiedHash ? 'Hash Copied' : 'SHA-256 Seal'}</span>
            </button>
            <span className="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-zinc-950 text-white font-semibold text-xs shadow-2xs">
              <Download className="w-3.5 h-3.5 text-zinc-400" />
              <span>PDF Sealed</span>
            </span>
          </div>
        </div>

        {/* 2-Column Split: Document Terms & Cryptographic Signature Box */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
          
          {/* Left: Authentic Contract Clause Preview */}
          <div className="lg:col-span-7 p-5 rounded-2xl bg-zinc-50/80 border border-zinc-200/80 space-y-3.5 text-xs text-zinc-700 leading-relaxed">
            <div className="flex items-center justify-between pb-2 border-b border-zinc-200 font-mono text-[11px] text-zinc-500">
              <span>PARTIES: Cora Studio × Raymond Apparel Ltd</span>
              <span>VALUE: ₹4,50,000 + 18% GST</span>
            </div>

            <div>
              <span className="font-bold text-zinc-900 block mb-1">Clause 1. Scope of Deliverables</span>
              <p className="text-zinc-600">
                Studio shall deliver 3x 30s Brand Commercials in 4K ProRes 422 HQ, color graded for broadcast, plus 15x vertical 9:16 cutdowns for social campaigns.
              </p>
            </div>

            <div>
              <span className="font-bold text-zinc-900 block mb-1">Clause 2. Intellectual Property &amp; Copyright Release</span>
              <p className="text-zinc-600">
                Full worldwide commercial usage rights transfer immediately upon 100% receipt of the final milestone settlement.
              </p>
            </div>

            <div>
              <span className="font-bold text-zinc-900 block mb-1">Clause 3. Payment Milestones (18% GST Split)</span>
              <p className="text-zinc-600">
                50% advance upon e-signature execution (₹2,65,500 incl. GST), and remaining 50% upon rough cut sign-off.
              </p>
            </div>
          </div>

          {/* Right: Cryptographic Signature & Audit Certificate */}
          <div className="lg:col-span-5 p-5 rounded-2xl bg-white border-2 border-zinc-950 shadow-sm flex flex-col justify-between space-y-4">
            <div>
              <div className="flex items-center justify-between text-[11px] font-mono text-zinc-500 pb-2 border-b border-zinc-100">
                <span>DIGITAL SIGNATURE SEAL</span>
                <span className="text-emerald-700 font-bold flex items-center gap-1">
                  <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" /> VERIFIED
                </span>
              </div>

              {/* Signature Visual Display */}
              <div className="my-4 p-4 rounded-xl bg-zinc-50 border border-zinc-200 text-center relative overflow-hidden">
                <span className="text-[10px] font-mono text-zinc-400 uppercase tracking-wider block mb-1">Authenticated Signer</span>
                <div className="font-serif italic text-2xl text-zinc-950 tracking-wide select-none py-1">
                  Kavya Patel
                </div>
                <div className="text-[11px] text-zinc-500 font-medium">Director of Brand &amp; Marketing</div>
              </div>

              {/* Tamper-Proof Audit Meta */}
              <div className="space-y-1.5 text-[11px] font-mono text-zinc-600 bg-zinc-50/60 p-3 rounded-xl border border-zinc-200/70">
                <div className="flex justify-between">
                  <span className="text-zinc-400">Timestamp:</span>
                  <span className="text-zinc-900 font-bold">26 Aug 2026, 04:15:32 PM IST</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-zinc-400">Auth Method:</span>
                  <span className="text-zinc-900">Aadhaar OTP + Email Auth</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-zinc-400">Network IP:</span>
                  <span className="text-zinc-900">103.21.244.1 (Mumbai, IN)</span>
                </div>
                <div className="flex justify-between pt-1 border-t border-zinc-200/80">
                  <span className="text-zinc-400">SHA-256:</span>
                  <span className="text-zinc-900 font-bold truncate max-w-[150px]">e3b0c44298fc1c14...</span>
                </div>
              </div>
            </div>

            <div className="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
              <span className="text-[11px] font-bold text-emerald-800 flex items-center justify-center gap-1.5">
                <ShieldCheck className="w-4 h-4 text-emerald-600" />
                Legally Binding &amp; Immutable Audit Trail
              </span>
            </div>
          </div>

        </div>

      </div>
    );
  }

  // ── 2. AI CO-FOUNDER & INTELLIGENCE (AUTHENTIC AI COPILOT WORKSPACE) ──
  if (
    feature.slug === 'ai-cofounder' || 
    feature.slug === 'content-ai' || 
    feature.slug === 'rag-mcp' || 
    feature.slug === 'voice-to-scope'
  ) {
    return (
      <div className="w-full bg-white rounded-2xl p-4 sm:p-7 border border-zinc-200/90 shadow-2xs space-y-6">
        
        {/* Top Intelligence Context Bar */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-zinc-200">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center shadow-xs shrink-0">
              <Sparkles className="w-5 h-5 text-amber-300" />
            </div>
            <div>
              <div className="flex items-center gap-2">
                <span className="text-xs font-mono font-bold text-zinc-950">AI STUDIO COPILOT</span>
                <span className="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 border border-zinc-200 text-[10px] font-mono font-bold">
                  Claude 3.5 Sonnet + Gemini 3.5 Flash
                </span>
              </div>
              <h4 className="font-display text-sm sm:text-base font-bold text-zinc-900 mt-0.5">
                Autonomous Commercial Scope &amp; Proposal Synthesizer
              </h4>
            </div>
          </div>

          <div className="flex items-center gap-2 text-xs font-mono">
            <span className="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5 font-bold">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
              142k Context Active
            </span>
          </div>
        </div>

        {/* 2-Column Split: Natural Language Chat Stream vs Generated Commercial Deliverable */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
          
          {/* Left: Chat Interaction & Prompts */}
          <div className="lg:col-span-6 p-5 rounded-2xl bg-zinc-50/90 border border-zinc-200/80 space-y-4 flex flex-col justify-between">
            <div className="space-y-3.5">
              {/* User Voice / Text Prompt Bubble */}
              <div className="flex items-start gap-2.5">
                <div className="w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                  U
                </div>
                <div className="p-3.5 rounded-2xl rounded-tl-sm bg-white border border-zinc-200 shadow-2xs text-xs text-zinc-900 space-y-1">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 block uppercase">Inbound Voice / Prompt</span>
                  <p className="font-medium leading-relaxed">
                    &ldquo;Draft commercial proposal for Raymond — 2 Days shoot with 4K Sony FX6 Kit, 18% GST, and 50% advance milestone.&rdquo;
                  </p>
                </div>
              </div>

              {/* AI Co-Founder Thought & Execution Stream */}
              <div className="flex items-start gap-2.5">
                <div className="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                  ✦
                </div>
                <div className="p-3.5 rounded-2xl rounded-tl-sm bg-white border border-zinc-200 shadow-2xs text-xs text-zinc-900 space-y-2 flex-1">
                  <div className="flex items-center justify-between font-mono text-[10px] text-emerald-700">
                    <span className="font-bold flex items-center gap-1">
                      <CheckCircle2 className="w-3 h-3 text-emerald-600" /> RAG Memory &amp; Pricing Match
                    </span>
                    <span>380ms</span>
                  </div>
                  <p className="text-zinc-700 leading-relaxed">
                    I matched your commercial rate card, verified Sony FX6 Kit #A availability, calculated the 18% GST split, and prepared the proposal PDF.
                  </p>
                  
                  {/* Action Triggers */}
                  <div className="pt-2 border-t border-zinc-100 flex flex-wrap gap-1.5">
                    <span className="px-2 py-1 rounded-md bg-zinc-100 text-zinc-900 font-bold text-[10px] flex items-center gap-1">
                      <FileText className="w-3 h-3 text-zinc-500" /> Proposal PDF Ready
                    </span>
                    <span className="px-2 py-1 rounded-md bg-zinc-100 text-zinc-900 font-bold text-[10px] flex items-center gap-1">
                      <Lock className="w-3 h-3 text-zinc-500" /> E-Sign Envelope
                    </span>
                  </div>
                </div>
              </div>
            </div>

            {/* Bottom Prompt Bar */}
            <div className="pt-3 border-t border-zinc-200 flex items-center gap-2">
              <input
                type="text"
                readOnly
                value="Send WhatsApp quote to client with UPI QR link"
                className="w-full bg-white border border-zinc-200 rounded-xl px-3 py-2 text-xs text-zinc-700 font-medium focus:outline-none"
              />
              <button
                type="button"
                className="px-3 py-2 rounded-xl bg-zinc-950 text-white text-xs font-bold shrink-0 flex items-center gap-1"
              >
                <span>Execute</span>
                <Send className="w-3 h-3 text-amber-300" />
              </button>
            </div>
          </div>

          {/* Right: Live Generated Deliverable Card */}
          <div className="lg:col-span-6 p-5 rounded-2xl bg-white border-2 border-zinc-950 shadow-sm flex flex-col justify-between space-y-4">
            <div>
              <div className="flex items-center justify-between text-[11px] font-mono text-zinc-500 pb-2 border-b border-zinc-100">
                <span>LIVE GENERATED SCOPE</span>
                <span className="text-emerald-700 font-bold">READY TO DISPATCH</span>
              </div>

              {/* Live Quotation Sheet */}
              <div className="my-3 p-4 rounded-xl bg-zinc-50 border border-zinc-200 space-y-3">
                <div className="flex justify-between items-start">
                  <div>
                    <h5 className="font-bold text-xs text-zinc-900">Raymond Autumn Commercial Shoot</h5>
                    <span className="text-[10px] text-zinc-500">2 Shooting Days • Studio 4B Mumbai</span>
                  </div>
                  <span className="font-mono text-xs font-extrabold text-zinc-950">₹4,50,000</span>
                </div>

                <div className="space-y-1.5 text-[11px] text-zinc-600 border-t border-zinc-200/80 pt-2 font-mono">
                  <div className="flex justify-between">
                    <span>Base Production Fee:</span>
                    <span>₹4,50,000</span>
                  </div>
                  <div className="flex justify-between text-zinc-800">
                    <span>18% GST (CGST 9% + SGST 9%):</span>
                    <span>+ ₹81,000</span>
                  </div>
                  <div className="flex justify-between text-xs font-bold text-zinc-950 pt-1 border-t border-zinc-200">
                    <span>Total Contract Value:</span>
                    <span className="text-emerald-700 font-extrabold">₹5,31,000</span>
                  </div>
                </div>

                <div className="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200/90 text-[11px] flex items-center justify-between text-emerald-800 font-medium">
                  <span>50% Advance Milestone Required:</span>
                  <span className="font-mono font-bold">₹2,65,500</span>
                </div>
              </div>
            </div>

            {/* Direct Workflow Actions */}
            <div className="flex items-center gap-2">
              <button
                type="button"
                className="flex-1 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1.5"
              >
                <Lock className="w-3.5 h-3.5 text-emerald-400" />
                <span>Send for E-Sign</span>
              </button>
              <button
                type="button"
                className="px-3.5 py-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-900 text-xs font-bold transition-all flex items-center gap-1"
              >
                <Smartphone className="w-3.5 h-3.5 text-zinc-500" />
                <span>WhatsApp</span>
              </button>
            </div>
          </div>

        </div>

      </div>
    );
  }

  // ── 3. 18% GST INVOICING & UPI (AUTHENTIC TAX INVOICE & INSTANT QR PAYMENTS) ──
  if (feature.slug === 'gst-invoicing') {
    return (
      <div className="w-full bg-white rounded-2xl p-4 sm:p-7 border border-zinc-200/90 shadow-2xs space-y-6">
        
        {/* Header */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-zinc-200">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center shadow-xs shrink-0">
              <Receipt className="w-5 h-5 text-amber-400" />
            </div>
            <div>
              <div className="flex items-center gap-2">
                <span className="text-xs font-mono font-bold text-zinc-950">TAX INVOICE #INV-2026-089</span>
                <span className="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-mono font-bold">
                  GST REGISTRY VERIFIED
                </span>
              </div>
              <h4 className="font-display text-sm sm:text-base font-bold text-zinc-900 mt-0.5">
                Automated 18% CGST/SGST Tax Invoice &amp; Dynamic UPI Engine
              </h4>
            </div>
          </div>

          <div className="flex items-center gap-2 text-xs font-mono">
            <span className="px-2.5 py-1 rounded-lg bg-zinc-100 text-zinc-800 font-bold border border-zinc-200">
              GSTIN: 27AABCU9603R1ZM
            </span>
          </div>
        </div>

        {/* 2-Column Split: Itemized Tax Bill & Scan-to-Pay UPI QR Box */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
          
          {/* Left: Itemized Line Items */}
          <div className="lg:col-span-7 p-5 rounded-2xl bg-zinc-50/80 border border-zinc-200/80 space-y-3 text-xs text-zinc-700">
            <div className="flex justify-between items-center pb-2 border-b border-zinc-200 font-mono text-[11px] text-zinc-500">
              <span>BILLED TO: Titan Watches Ltd</span>
              <span>SAC CODE: 998314</span>
            </div>

            <div className="space-y-2">
              <div className="flex justify-between py-1.5 border-b border-zinc-200/60">
                <div>
                  <span className="font-bold text-zinc-900 block">Commercial Video Production (Day 1)</span>
                  <span className="text-[10px] text-zinc-500">4K Sony FX6 + Lighting Grid + Director Fee</span>
                </div>
                <span className="font-mono font-bold text-zinc-950">₹2,00,000</span>
              </div>

              <div className="flex justify-between py-1.5 border-b border-zinc-200/60">
                <div>
                  <span className="font-bold text-zinc-900 block">Color Grading &amp; Sound Mix</span>
                  <span className="text-[10px] text-zinc-500">DaVinci Resolve Master + Dolby 5.1</span>
                </div>
                <span className="font-mono font-bold text-zinc-950">₹1,20,000</span>
              </div>
            </div>

            <div className="pt-2 space-y-1.5 font-mono text-[11px]">
              <div className="flex justify-between text-zinc-600">
                <span>Taxable Subtotal:</span>
                <span>₹3,20,000</span>
              </div>
              <div className="flex justify-between text-zinc-600">
                <span>CGST (9.0%):</span>
                <span>₹28,800</span>
              </div>
              <div className="flex justify-between text-zinc-600">
                <span>SGST (9.0%):</span>
                <span>₹28,800</span>
              </div>
              <div className="flex justify-between text-xs font-bold text-zinc-950 pt-2 border-t border-zinc-200">
                <span>Total Amount Due:</span>
                <span className="text-emerald-700 font-extrabold text-sm">₹3,77,600</span>
              </div>
            </div>
          </div>

          {/* Right: Scan-to-Pay QR Card */}
          <div className="lg:col-span-5 p-5 rounded-2xl bg-white border-2 border-zinc-950 shadow-sm flex flex-col items-center justify-between text-center space-y-4">
            <div className="w-full">
              <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-1">
                INSTANT UPI RECONCILIATION
              </span>
              <h5 className="font-display text-sm font-bold text-zinc-950">Scan &amp; Settle via UPI</h5>
              
              {/* Dynamic QR Display */}
              <div className="my-3 p-3 bg-zinc-50 rounded-xl border border-zinc-200 inline-block">
                <div className="w-28 h-28 bg-zinc-950 text-white rounded-lg flex items-center justify-center p-2 mx-auto">
                  <QrCode className="w-24 h-24 text-white" />
                </div>
                <span className="text-[10px] font-mono text-zinc-500 mt-2 block">
                  cora.studio@icici
                </span>
              </div>

              <div className="text-[11px] text-zinc-600">
                Supports PhonePe, GPay, Paytm &amp; NetBanking
              </div>
            </div>

            <div className="w-full p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
              <span className="text-[11px] font-bold text-emerald-800 flex items-center justify-center gap-1.5">
                <CheckCircle2 className="w-4 h-4 text-emerald-600" />
                0% Gateway Fees via Direct UPI
              </span>
            </div>
          </div>

        </div>

      </div>
    );
  }

  // ── 4. KANBAN LEAD CRM (AUTHENTIC VISUAL DEAL PIPELINE) ──
  if (feature.slug === 'lead-crm') {
    return (
      <div className="w-full bg-white rounded-2xl p-4 sm:p-7 border border-zinc-200/90 shadow-2xs space-y-6">
        
        {/* Header */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-zinc-200">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center shadow-xs shrink-0">
              <Layers className="w-5 h-5 text-blue-400" />
            </div>
            <div>
              <div className="flex items-center gap-2">
                <span className="text-xs font-mono font-bold text-zinc-950">ACTIVE PIPELINE</span>
                <span className="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-mono font-bold">
                  ₹18.6L Pipeline Value
                </span>
              </div>
              <h4 className="font-display text-sm sm:text-base font-bold text-zinc-900 mt-0.5">
                Visual Studio Lead &amp; Deal Stage Funnel
              </h4>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <span className="text-xs font-bold px-3 py-1.5 rounded-lg bg-zinc-950 text-white">
              + New Lead
            </span>
          </div>
        </div>

        {/* 3-Column Kanban Board */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          
          {/* Column 1: New Inquiries */}
          <div className="p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200 space-y-3">
            <div className="flex items-center justify-between text-xs font-bold text-zinc-700">
              <span>1. Inquiries</span>
              <span className="px-2 py-0.5 rounded-full bg-zinc-200 text-zinc-700 text-[10px]">3</span>
            </div>

            <div className="p-3 rounded-xl bg-white border border-zinc-200 shadow-2xs space-y-2 text-xs">
              <div className="flex justify-between items-start">
                <span className="font-bold text-zinc-900">Nykaa Lookbook</span>
                <span className="font-mono font-bold text-zinc-950">₹2.1L</span>
              </div>
              <p className="text-[11px] text-zinc-500">2-Day Fashion Studio Campaign</p>
              <div className="flex items-center gap-1 text-[10px] text-zinc-400 font-mono">
                <Clock className="w-3 h-3" /> Inquiry: 2h ago
              </div>
            </div>

            <div className="p-3 rounded-xl bg-white border border-zinc-200 shadow-2xs space-y-2 text-xs">
              <div className="flex justify-between items-start">
                <span className="font-bold text-zinc-900">Zomato Ad Shoot</span>
                <span className="font-mono font-bold text-zinc-950">₹1.8L</span>
              </div>
              <p className="text-[11px] text-zinc-500">Food Commercial &amp; Reels</p>
              <div className="flex items-center gap-1 text-[10px] text-zinc-400 font-mono">
                <Clock className="w-3 h-3" /> Inquiry: 5h ago
              </div>
            </div>
          </div>

          {/* Column 2: Proposal Sent */}
          <div className="p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200 space-y-3">
            <div className="flex items-center justify-between text-xs font-bold text-zinc-700">
              <span>2. Proposal &amp; Terms</span>
              <span className="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px]">2</span>
            </div>

            <div className="p-3 rounded-xl bg-white border-2 border-zinc-950 shadow-sm space-y-2 text-xs">
              <div className="flex justify-between items-start">
                <span className="font-bold text-zinc-900">Raymond Autumn</span>
                <span className="font-mono font-bold text-emerald-700">₹4.5L</span>
              </div>
              <p className="text-[11px] text-zinc-600">Scope Generated + 18% GST</p>
              <div className="p-1.5 rounded-lg bg-emerald-50 text-emerald-800 text-[10px] font-bold flex items-center justify-between">
                <span>E-Sign Envelope Sent</span>
                <CheckCircle2 className="w-3 h-3 text-emerald-600" />
              </div>
            </div>
          </div>

          {/* Column 3: Signed & Booked */}
          <div className="p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200 space-y-3">
            <div className="flex items-center justify-between text-xs font-bold text-zinc-700">
              <span>3. Signed &amp; Booked</span>
              <span className="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px]">4</span>
            </div>

            <div className="p-3 rounded-xl bg-white border border-zinc-200 shadow-2xs space-y-2 text-xs">
              <div className="flex justify-between items-start">
                <span className="font-bold text-zinc-900">Titan Watches Commercial</span>
                <span className="font-mono font-bold text-zinc-950">₹3.2L</span>
              </div>
              <div className="p-1.5 rounded-lg bg-zinc-100 text-zinc-800 text-[10px] font-mono flex items-center justify-between">
                <span>50% Advance Settled</span>
                <span className="text-emerald-600 font-bold">✓ BOOKED</span>
              </div>
            </div>
          </div>

        </div>

      </div>
    );
  }

  // ── 5. CREW DISPATCH & MASTER CALENDAR (CALL SHEET & TIMELINE) ──
  if (feature.slug === 'crew-dispatch' || feature.slug === 'master-calendar' || feature.slug === 'task-board') {
    return (
      <div className="w-full bg-white rounded-2xl p-4 sm:p-7 border border-zinc-200/90 shadow-2xs space-y-6">
        
        {/* Header */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-zinc-200">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center shadow-xs shrink-0">
              <Calendar className="w-5 h-5 text-purple-400" />
            </div>
            <div>
              <div className="flex items-center gap-2">
                <span className="text-xs font-mono font-bold text-zinc-950">CALL SHEET #CS-2026-14</span>
                <span className="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-mono font-bold">
                  CONFLICT-FREE VERIFIED
                </span>
              </div>
              <h4 className="font-display text-sm sm:text-base font-bold text-zinc-900 mt-0.5">
                Titan Watches Commercial — Day 1 Production Call Sheet
              </h4>
            </div>
          </div>

          <div className="flex items-center gap-2 text-xs">
            <span className="px-3 py-1.5 rounded-lg bg-zinc-950 text-white font-bold flex items-center gap-1.5 shadow-2xs">
              <Smartphone className="w-3.5 h-3.5 text-amber-300" />
              <span>Broadcast Call Sheet</span>
            </span>
          </div>
        </div>

        {/* 2-Column Split: Call Sheet Details & Assigned Crew Roster */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
          
          {/* Left: Call Sheet Meta */}
          <div className="lg:col-span-6 p-5 rounded-2xl bg-zinc-50/80 border border-zinc-200/80 space-y-3.5 text-xs text-zinc-700">
            <div className="p-3.5 rounded-xl bg-white border border-zinc-200 shadow-2xs space-y-2">
              <div className="flex justify-between items-center">
                <span className="text-[10px] font-mono text-zinc-400 uppercase">General Call Time</span>
                <span className="font-mono text-sm font-extrabold text-zinc-950">06:30 AM IST</span>
              </div>
              <div className="flex justify-between items-center text-[11px] text-zinc-500">
                <span>Location:</span>
                <span className="text-zinc-900 font-bold">Studio 4B, Film City, Goregaon East, Mumbai</span>
              </div>
              <div className="flex justify-between items-center text-[11px] text-zinc-500">
                <span>First Shot Ready:</span>
                <span className="text-zinc-900 font-bold">08:00 AM IST</span>
              </div>
            </div>

            <div className="p-3 rounded-xl bg-zinc-100/80 border border-zinc-200 text-[11px] space-y-1">
              <span className="font-bold text-zinc-900 block">Assigned Gear Package:</span>
              <p className="text-zinc-600 font-mono">
                Kit #A (Sony FX6 Cinema Line) + Sony G-Master 24-70mm, 70-200mm + Aputure 600d Pro
              </p>
            </div>
          </div>

          {/* Right: Crew Dispatch Status */}
          <div className="lg:col-span-6 p-5 rounded-2xl bg-white border-2 border-zinc-950 shadow-sm space-y-3">
            <div className="flex justify-between items-center text-[11px] font-mono text-zinc-500 pb-2 border-b border-zinc-100">
              <span>CREW ROSTER &amp; CONFIRMATIONS</span>
              <span className="text-emerald-700 font-bold">4/4 CONFIRMED</span>
            </div>

            <div className="space-y-2">
              {[
                { role: 'Director of Photography', name: 'Kabir Sharma', kit: 'Kit #A (Sony FX6)', status: 'Confirmed' },
                { role: 'Gaffer / Lighting Lead', name: 'Rohan Sen', kit: 'Aputure Grid', status: 'Confirmed' },
                { role: 'Sound Recordist', name: 'Aarav Mehta', kit: 'Sennheiser 416 + Zoom F8', status: 'Confirmed' },
                { role: '1st AC / Focus Puller', name: 'Vikram Joshi', kit: 'Tilta Nucleus-M', status: 'Confirmed' },
              ].map((crew, idx) => (
                <div key={idx} className="flex items-center justify-between p-2.5 rounded-xl bg-zinc-50 border border-zinc-200/80 text-xs">
                  <div>
                    <span className="font-bold text-zinc-900 block">{crew.name}</span>
                    <span className="text-[10px] text-zinc-500">{crew.role} • {crew.kit}</span>
                  </div>
                  <span className="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-mono font-bold flex items-center gap-1">
                    <Check className="w-3 h-3 text-emerald-600" /> {crew.status}
                  </span>
                </div>
              ))}
            </div>
          </div>

        </div>

      </div>
    );
  }

  // ── DEFAULT FALLBACK: GENERAL CLEAN WORKSPACE METRIC MATRIX ──
  return (
    <div className="w-full bg-white rounded-2xl p-4 sm:p-7 border border-zinc-200/90 shadow-2xs space-y-6">
      
      {/* Header */}
      <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-4 border-b border-zinc-200/80">
        <div>
          <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950">
            {feature.mockup.headerTitle}
          </h3>
          <p className="text-xs text-zinc-500 mt-0.5">
            {feature.mockup.headerSubtitle}
          </p>
        </div>

        <div className="flex items-center gap-2.5 shrink-0">
          <div className="px-3 py-1.5 rounded-xl bg-white border border-zinc-200 shadow-2xs text-left">
            <span className="text-[10px] font-mono text-zinc-400 block">{feature.mockup.metric1.label}</span>
            <span className="text-xs font-mono font-bold text-zinc-950">{feature.mockup.metric1.value}</span>
          </div>
          <div className="px-3 py-1.5 rounded-xl bg-white border border-zinc-200 shadow-2xs text-left">
            <span className="text-[10px] font-mono text-zinc-400 block">{feature.mockup.metric2.label}</span>
            <span className="text-xs font-mono font-bold text-zinc-950">{feature.mockup.metric2.value}</span>
          </div>
        </div>
      </div>

      {/* Data Table Matrix Preview */}
      <div className="w-full overflow-x-auto rounded-2xl border border-zinc-200/90 bg-white shadow-2xs">
        <table className="w-full text-left text-xs border-collapse">
          <thead>
            <tr className="border-b border-zinc-200 bg-zinc-50/80 text-zinc-500 font-mono text-[11px] uppercase">
              {feature.mockup.tableHeaders.map((header, idx) => (
                <th key={idx} className="py-3 px-4 font-semibold">{header}</th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-zinc-100 font-medium">
            {feature.mockup.rows.map((row, rIdx) => (
              <tr key={rIdx} className="hover:bg-zinc-50/80 transition-colors">
                <td className="py-3.5 px-4 text-zinc-900 font-semibold">{row.col1}</td>
                <td className="py-3.5 px-4 text-zinc-600">{row.col2}</td>
                <td className="py-3.5 px-4 text-zinc-700 font-mono text-[11px]">{row.col3}</td>
                <td className="py-3.5 px-4">
                  <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-mono font-bold bg-zinc-100 text-zinc-800 border border-zinc-200">
                    <span className={`w-1.5 h-1.5 rounded-full ${
                      row.statusType === 'success' 
                        ? 'bg-emerald-500' 
                        : row.statusType === 'warning'
                          ? 'bg-amber-500'
                          : row.statusType === 'info'
                            ? 'bg-sky-500'
                            : 'bg-zinc-400'
                    }`} />
                    {row.statusText}
                  </span>
                </td>
                <td className="py-3.5 px-4 text-right">
                  <span className="text-[11px] font-bold text-zinc-950 hover:text-zinc-600 transition-colors cursor-default underline underline-offset-4">
                    {row.actionText} →
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Mockup Footer Caption */}
      <div className="flex items-center justify-between text-[11px] text-zinc-400 pt-1 font-mono">
        <span>⚡ Powered by Cora Atomic UI Engine</span>
        <span>SHA-256 Verified • AES-256 Encrypted</span>
      </div>

    </div>
  );
}
