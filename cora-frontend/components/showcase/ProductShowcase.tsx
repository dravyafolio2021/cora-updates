'use client';

import React, { useState } from 'react';
import { Bot, Layers, FileCheck, DollarSign, Sparkles, Check, ArrowUpRight, Zap, Shield } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export function ProductShowcase() {
  const [activeTab, setActiveTab] = useState<'ai' | 'kanban' | 'vault' | 'gst'>('ai');

  const handleTabChange = (tab: 'ai' | 'kanban' | 'vault' | 'gst') => {
    setActiveTab(tab);
    trackEvent('showcase_tab_switched', { tab_name: tab });
  };

  return (
    <section id="features" className="py-16 md:py-24 relative z-10 border-t border-zinc-100 bg-white">
      <div className="w-full max-w-[1140px] mx-auto px-4 sm:px-6">
        
        {/* Section Header */}
        <div className="text-center max-w-[760px] mx-auto mb-10">
          <div className="inline-flex items-center gap-1.5 font-sans text-[0.8125rem] font-medium text-zinc-600 px-3.5 py-1 bg-white border border-zinc-200 rounded-full mb-3.5 shadow-sm">
            <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
            <span>Interactive Command Center</span>
          </div>
          <h2 className="font-display text-[clamp(1.85rem,3.8vw,2.75rem)] font-[550] tracking-[-0.035em] text-zinc-950 leading-[1.18] mb-3">
            One hyper-focused command center.<br className="hidden sm:block" />
            Zero fragmented tabs.
          </h2>
          <p className="font-sans text-[clamp(0.85rem,1.1vw,1rem)] text-zinc-600 leading-[1.55]">
            Switch seamlessly between frontier AI routing, client pipelines, digital agreements, and GST math.
          </p>
        </div>

        {/* Tab Controls */}
        <div className="flex items-center justify-center mb-7 overflow-x-auto pb-2 scrollbar-none">
          <div className="inline-flex items-center bg-zinc-100/90 p-1.5 rounded-xl border border-zinc-200 shadow-inner max-w-full">
            <button
              type="button"
              onClick={() => handleTabChange('ai')}
              className={`flex items-center gap-2 px-3.5 sm:px-4 py-2 font-sans text-xs sm:text-[0.8125rem] font-semibold rounded-lg transition-all whitespace-nowrap ${
                activeTab === 'ai'
                  ? 'bg-white text-zinc-950 shadow-sm border border-zinc-200/80'
                  : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              <Bot className="w-4 h-4 text-purple-600" />
              <span>Multi-Model AI</span>
            </button>
            <button
              type="button"
              onClick={() => handleTabChange('kanban')}
              className={`flex items-center gap-2 px-3.5 sm:px-4 py-2 font-sans text-xs sm:text-[0.8125rem] font-semibold rounded-lg transition-all whitespace-nowrap ${
                activeTab === 'kanban'
                  ? 'bg-white text-zinc-950 shadow-sm border border-zinc-200/80'
                  : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              <Layers className="w-4 h-4 text-blue-600" />
              <span>Pipeline Kanban</span>
            </button>
            <button
              type="button"
              onClick={() => handleTabChange('vault')}
              className={`flex items-center gap-2 px-3.5 sm:px-4 py-2 font-sans text-xs sm:text-[0.8125rem] font-semibold rounded-lg transition-all whitespace-nowrap ${
                activeTab === 'vault'
                  ? 'bg-white text-zinc-950 shadow-sm border border-zinc-200/80'
                  : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              <FileCheck className="w-4 h-4 text-emerald-600" />
              <span>E-Sign Vault</span>
            </button>
            <button
              type="button"
              onClick={() => handleTabChange('gst')}
              className={`flex items-center gap-2 px-3.5 sm:px-4 py-2 font-sans text-xs sm:text-[0.8125rem] font-semibold rounded-lg transition-all whitespace-nowrap ${
                activeTab === 'gst'
                  ? 'bg-white text-zinc-950 shadow-sm border border-zinc-200/80'
                  : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              <DollarSign className="w-4 h-4 text-amber-600" />
              <span>GST & Invoicing</span>
            </button>
          </div>
        </div>

        {/* Interactive Browser Frame */}
        <div className="bg-white border border-zinc-200 rounded-2xl shadow-xl overflow-hidden max-w-[1020px] mx-auto transition-all duration-300">
          
          {/* Top Window Bar */}
          <div className="bg-zinc-100/80 border-b border-zinc-200 px-4 py-3 flex items-center justify-between">
            <div className="flex items-center gap-1.5">
              <div className="w-2.5 h-2.5 rounded-full bg-zinc-300" />
              <div className="w-2.5 h-2.5 rounded-full bg-zinc-300" />
              <div className="w-2.5 h-2.5 rounded-full bg-zinc-300" />
            </div>
            <div className="bg-white border border-zinc-200 rounded-md px-3 py-1 text-[0.6875rem] font-mono text-zinc-500 flex items-center gap-1.5 shadow-2xs">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
              <span>https://app.heycora.in/workspace/dashboard</span>
            </div>
            <div className="text-[0.6875rem] font-semibold uppercase tracking-wider text-zinc-400">
              v2.4 Live
            </div>
          </div>

          {/* Tab Content 1: Multi-Model AI Router */}
          {activeTab === 'ai' && (
            <div className="p-6 sm:p-8 bg-zinc-50/50">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                  <h3 className="font-display text-lg font-bold text-zinc-950 mb-1">Frontier AI Multi-Model Routing</h3>
                  <p className="text-xs text-zinc-500">Route complex client drafts to Claude 3.5 Sonnet, speed queries to Gemini 2.0, and JSON extraction to GPT-4o.</p>
                </div>
                <div className="flex items-center gap-2">
                  <span className="text-xs font-semibold px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-200 rounded-full">Claude 3.5 Active</span>
                  <span className="text-xs font-semibold px-2.5 py-1 bg-zinc-100 text-zinc-600 rounded-full">GPT-4o Ready</span>
                  <span className="text-xs font-semibold px-2.5 py-1 bg-zinc-100 text-zinc-600 rounded-full">Gemini 2.0 Ready</span>
                </div>
              </div>

              {/* Simulated AI Prompt Box */}
              <div className="bg-white border border-zinc-200 rounded-xl p-4 shadow-sm mb-4">
                <div className="flex items-center gap-2 mb-2 text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                  <Bot className="w-3.5 h-3.5 text-purple-600" />
                  <span>AI Prompt Execution (Zero API key needed)</span>
                </div>
                <div className="bg-zinc-50 rounded-lg p-3 text-xs font-mono text-zinc-800 border border-zinc-100 mb-3">
                  "Generate a commercial photography call-sheet agreement with 18% GST calculation for Mumbai real estate shoot on Saturday."
                </div>
                <div className="border-l-2 border-purple-500 pl-3 py-1 bg-purple-50/30 rounded-r-md">
                  <div className="text-[0.8125rem] font-medium text-zinc-900 mb-1 flex items-center gap-1.5">
                    <Sparkles className="w-3.5 h-3.5 text-purple-600" />
                    <span>Claude 3.5 Sonnet Response (Generated in 380ms):</span>
                  </div>
                  <p className="text-xs text-zinc-600 leading-relaxed font-sans">
                    ✓ Contract #CS-2026-904 generated with full clause set.<br />
                    ✓ Shoot Call-Time: 07:00 AM IST at Bandra West, Mumbai.<br />
                    ✓ Subtotal: ₹45,000 + 18% IGST (₹8,100) = Total: ₹53,100. Razorpay digital payment link attached.
                  </p>
                </div>
              </div>
            </div>
          )}

          {/* Tab Content 2: Pipeline Kanban */}
          {activeTab === 'kanban' && (
            <div className="p-6 sm:p-8 bg-zinc-50/50">
              <div className="flex items-center justify-between mb-6">
                <div>
                  <h3 className="font-display text-lg font-bold text-zinc-950 mb-1">Visual Lead Funnel & Booking Pipeline</h3>
                  <p className="text-xs text-zinc-500">Automate shoot bookings, call-sheet reminders, and client approvals in a clean drag-and-drop board.</p>
                </div>
              </div>

              {/* 3 Kanban Columns */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="bg-white border border-zinc-200 rounded-xl p-3.5 shadow-sm">
                  <div className="flex items-center justify-between mb-3">
                    <span className="text-xs font-bold text-zinc-900">New Inquiry</span>
                    <span className="text-[0.6875rem] font-semibold px-2 py-0.5 bg-zinc-100 rounded-full text-zinc-600">3 leads</span>
                  </div>
                  <div className="bg-zinc-50 border border-zinc-100 rounded-lg p-3 mb-2">
                    <div className="text-xs font-semibold text-zinc-900 mb-0.5">Skyline Towers Shoot</div>
                    <div className="text-[0.6875rem] text-zinc-500">Acme Developers • ₹75,000</div>
                  </div>
                </div>

                <div className="bg-white border border-zinc-200 rounded-xl p-3.5 shadow-sm">
                  <div className="flex items-center justify-between mb-3">
                    <span className="text-xs font-bold text-blue-900">Call-Time Confirmed</span>
                    <span className="text-[0.6875rem] font-semibold px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full">2 active</span>
                  </div>
                  <div className="bg-blue-50/40 border border-blue-100 rounded-lg p-3 mb-2">
                    <div className="text-xs font-semibold text-zinc-900 mb-0.5">Fashion Lookbook 2026</div>
                    <div className="text-[0.6875rem] text-zinc-500">Vogue Studio • Saturday 9:00 AM</div>
                  </div>
                </div>

                <div className="bg-white border border-zinc-200 rounded-xl p-3.5 shadow-sm">
                  <div className="flex items-center justify-between mb-3">
                    <span className="text-xs font-bold text-emerald-900">Contract Signed & Paid</span>
                    <span className="text-[0.6875rem] font-semibold px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full">18 complete</span>
                  </div>
                  <div className="bg-emerald-50/40 border border-emerald-100 rounded-lg p-3 mb-2">
                    <div className="text-xs font-semibold text-zinc-900 mb-0.5">Penthouse 404 Video Tour</div>
                    <div className="text-[0.6875rem] text-zinc-500">₹1,20,000 • Paid via UPI</div>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* Tab Content 3: Document Vault & E-Sign */}
          {activeTab === 'vault' && (
            <div className="p-6 sm:p-8 bg-zinc-50/50">
              <div className="flex items-center justify-between mb-6">
                <div>
                  <h3 className="font-display text-lg font-bold text-zinc-950 mb-1">Legally Binding E-Signature Vault</h3>
                  <p className="text-xs text-zinc-500">Digital signature registry with cryptographic verification, IP timestamps, and PDF download.</p>
                </div>
              </div>

              <div className="bg-white border border-zinc-200 rounded-xl divide-y divide-zinc-100 shadow-sm">
                <div className="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs border border-emerald-100">
                      <Check className="w-4 h-4" />
                    </div>
                    <div>
                      <div className="text-xs font-bold text-zinc-900">Commercial Production Agreement #CPA-882</div>
                      <div className="text-[0.6875rem] text-zinc-500">Signed by Rohan Oberoi (rohan@studio.in) • Timestamp: 19 Aug 2026 14:22 IST</div>
                    </div>
                  </div>
                  <span className="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full self-start sm:self-auto">Verified E-Sign ✓</span>
                </div>
                <div className="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs border border-emerald-100">
                      <Check className="w-4 h-4" />
                    </div>
                    <div>
                      <div className="text-xs font-bold text-zinc-900">Real Estate Exclusivity Retainer #REA-409</div>
                      <div className="text-[0.6875rem] text-zinc-500">Signed by Priya Sharma • IP: 103.21.244.18 (Mumbai, IN)</div>
                    </div>
                  </div>
                  <span className="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full self-start sm:self-auto">Verified E-Sign ✓</span>
                </div>
              </div>
            </div>
          )}

          {/* Tab Content 4: GST & Invoicing */}
          {activeTab === 'gst' && (
            <div className="p-6 sm:p-8 bg-zinc-50/50">
              <div className="flex items-center justify-between mb-6">
                <div>
                  <h3 className="font-display text-lg font-bold text-zinc-950 mb-1">Instant B2B GST Calculation Card</h3>
                  <p className="text-xs text-zinc-500">Compliant with Indian Tax Code (CGST 9% + SGST 9% or IGST 18%) with auto GSTIN format validation.</p>
                </div>
              </div>

              <div className="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm max-w-[580px] mx-auto">
                <div className="flex justify-between items-center pb-3 border-b border-zinc-100 text-xs">
                  <span className="text-zinc-500">Base Services Fee</span>
                  <span className="font-mono font-bold text-zinc-900">₹50,000.00</span>
                </div>
                <div className="flex justify-between items-center py-2 text-xs">
                  <span className="text-zinc-500">CGST (9%)</span>
                  <span className="font-mono text-zinc-700">₹4,500.00</span>
                </div>
                <div className="flex justify-between items-center py-2 text-xs border-b border-zinc-100">
                  <span className="text-zinc-500">SGST (9%)</span>
                  <span className="font-mono text-zinc-700">₹4,500.00</span>
                </div>
                <div className="flex justify-between items-center pt-3 text-sm font-bold">
                  <span className="text-zinc-900">Total B2B Payable</span>
                  <span className="font-mono text-zinc-950">₹59,000.00</span>
                </div>
              </div>
            </div>
          )}

        </div>

      </div>
    </section>
  );
}
