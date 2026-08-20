'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { ArrowLeft, Calculator, Copy, Check, Sparkles, ArrowRight, ShieldCheck } from 'lucide-react';
import { useToast } from '@/components/ui/Toast';

export default function GstCalculatorPage() {
  const [amount, setAmount] = useState<number>(10000);
  const [gstRate, setGstRate] = useState<number>(18);
  const [taxMode, setTaxMode] = useState<'exclusive' | 'inclusive'>('exclusive');
  const [isInterstate, setIsInterstate] = useState<boolean>(false);
  const [copied, setCopied] = useState<boolean>(false);
  const { showToast } = useToast();

  // Math Calculations
  const baseAmount = taxMode === 'exclusive'
    ? amount
    : amount / (1 + gstRate / 100);

  const gstAmount = taxMode === 'exclusive'
    ? (amount * gstRate) / 100
    : amount - baseAmount;

  const totalAmount = taxMode === 'exclusive'
    ? amount + gstAmount
    : amount;

  const cgstAmount = isInterstate ? 0 : gstAmount / 2;
  const sgstAmount = isInterstate ? 0 : gstAmount / 2;
  const igstAmount = isInterstate ? gstAmount : 0;

  const copyBreakdown = () => {
    const text = `--- GST Calculation Breakdown ---
Base Amount: ₹${baseAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
GST Rate: ${gstRate}% (${taxMode === 'exclusive' ? 'Exclusive' : 'Inclusive'})
${isInterstate ? `IGST (Integrated): ₹${igstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}` : `CGST (Central): ₹${cgstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}\nSGST (State): ₹${sgstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}`}
Total Invoice Amount: ₹${totalAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
Generated via Cora Tools (https://heycora.in/tools/gst-calculator)`;

    navigator.clipboard.writeText(text);
    setCopied(true);
    showToast('GST calculation copied to clipboard!');
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <div className="py-12 md:py-20">
      <div className="w-full max-w-[960px] mx-auto px-6">
        
        {/* Back navigation */}
        <Link
          href="/tools"
          className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 mb-8 transition-colors"
        >
          <ArrowLeft className="w-4 h-4" /> Back to all micro-tools
        </Link>

        {/* Tool Header */}
        <div className="mb-10">
          <div className="inline-flex items-center gap-1.5 font-sans text-xs font-bold text-orange-800 px-3 py-1 bg-orange-100 rounded-full mb-3">
            <span>🇮🇳 Indian Tax Standard</span>
          </div>
          <h1 className="font-display text-3xl md:text-4xl font-bold tracking-tight text-zinc-950 mb-3">
            Indian GST & B2B Invoice Calculator
          </h1>
          <p className="text-zinc-600 text-sm md:text-base leading-relaxed">
            Calculate instant 18%, 12%, 5%, and 28% GST breakdowns with CGST/SGST vs IGST segregation for B2B client invoices and freelance retainers.
          </p>
        </div>

        {/* Interactive Calculator Workspace */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-8 mb-12">
          
          {/* Left Column: Controls */}
          <div className="md:col-span-7 bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm space-y-6">
            
            {/* Input Amount */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Transaction Amount (₹)
              </label>
              <div className="relative">
                <span className="absolute left-3.5 top-1/2 -translate-y-1/2 font-mono font-bold text-zinc-500">₹</span>
                <input
                  type="number"
                  value={amount}
                  onChange={(e) => setAmount(Math.max(0, Number(e.target.value)))}
                  className="w-full pl-9 pr-4 py-2.5 rounded-xl border border-zinc-200 font-mono text-lg font-bold text-zinc-950 focus:outline-none focus:border-zinc-950 transition-colors"
                  placeholder="10000"
                />
              </div>
            </div>

            {/* GST Rate Selection */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                GST Tax Slab
              </label>
              <div className="grid grid-cols-4 gap-2">
                {[5, 12, 18, 28].map((rate) => (
                  <button
                    key={rate}
                    type="button"
                    onClick={() => setGstRate(rate)}
                    className={`py-2 px-3 rounded-xl font-mono text-sm font-bold border transition-all ${
                      gstRate === rate
                        ? 'bg-zinc-950 text-white border-zinc-950 shadow-sm'
                        : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                    }`}
                  >
                    {rate}%
                  </button>
                ))}
              </div>
            </div>

            {/* Exclusive vs Inclusive Toggle */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                GST Inclusion Mode
              </label>
              <div className="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  onClick={() => setTaxMode('exclusive')}
                  className={`py-2 px-3 rounded-xl text-xs font-semibold border transition-all ${
                    taxMode === 'exclusive'
                      ? 'bg-zinc-950 text-white border-zinc-950'
                      : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                  }`}
                >
                  GST Exclusive (+ Tax)
                </button>
                <button
                  type="button"
                  onClick={() => setTaxMode('inclusive')}
                  className={`py-2 px-3 rounded-xl text-xs font-semibold border transition-all ${
                    taxMode === 'inclusive'
                      ? 'bg-zinc-950 text-white border-zinc-950'
                      : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                  }`}
                >
                  GST Inclusive (Inside Total)
                </button>
              </div>
            </div>

            {/* Intra-State vs Inter-State (CGST/SGST vs IGST) */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Supply Jurisdiction
              </label>
              <div className="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  onClick={() => setIsInterstate(false)}
                  className={`py-2 px-3 rounded-xl text-xs font-semibold border transition-all ${
                    !isInterstate
                      ? 'bg-zinc-950 text-white border-zinc-950'
                      : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                  }`}
                >
                  Intra-State (CGST + SGST)
                </button>
                <button
                  type="button"
                  onClick={() => setIsInterstate(true)}
                  className={`py-2 px-3 rounded-xl text-xs font-semibold border transition-all ${
                    isInterstate
                      ? 'bg-zinc-950 text-white border-zinc-950'
                      : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                  }`}
                >
                  Inter-State (IGST)
                </button>
              </div>
            </div>

          </div>

          {/* Right Column: Real-Time Calculation Card */}
          <div className="md:col-span-5 bg-zinc-950 text-white rounded-2xl p-6 flex flex-col justify-between shadow-xl">
            <div>
              <div className="flex items-center justify-between pb-4 border-b border-zinc-800 mb-5">
                <span className="text-xs font-bold uppercase tracking-wider text-zinc-400">Invoice Summary</span>
                <span className="text-xs font-mono bg-zinc-800 text-zinc-300 px-2 py-0.5 rounded">
                  {gstRate}% {taxMode}
                </span>
              </div>

              <div className="space-y-3.5 mb-6 text-sm">
                <div className="flex items-center justify-between">
                  <span className="text-zinc-400">Net Base Value:</span>
                  <span className="font-mono font-bold">
                    ₹{baseAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
                  </span>
                </div>

                {!isInterstate ? (
                  <>
                    <div className="flex items-center justify-between text-xs text-zinc-300">
                      <span className="text-zinc-400">CGST ({gstRate / 2}%):</span>
                      <span className="font-mono">
                        ₹{cgstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
                      </span>
                    </div>
                    <div className="flex items-center justify-between text-xs text-zinc-300">
                      <span className="text-zinc-400">SGST ({gstRate / 2}%):</span>
                      <span className="font-mono">
                        ₹{sgstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
                      </span>
                    </div>
                  </>
                ) : (
                  <div className="flex items-center justify-between text-xs text-zinc-300">
                    <span className="text-zinc-400">IGST ({gstRate}%):</span>
                    <span className="font-mono">
                      ₹{igstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
                    </span>
                  </div>
                )}

                <div className="flex items-center justify-between pt-2 border-t border-zinc-800">
                  <span className="text-zinc-400">Total Tax:</span>
                  <span className="font-mono font-semibold text-orange-400">
                    +₹{gstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
                  </span>
                </div>
              </div>

              <div className="p-4 bg-zinc-900 rounded-xl mb-6">
                <div className="text-xs uppercase tracking-wider text-zinc-400 mb-1">Gross Invoice Total</div>
                <div className="font-mono text-2xl font-bold text-white">
                  ₹{totalAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
                </div>
              </div>
            </div>

            <button
              type="button"
              onClick={copyBreakdown}
              className="w-full inline-flex items-center justify-center gap-2 bg-white text-zinc-950 font-semibold py-2.5 px-4 rounded-xl text-xs hover:bg-zinc-100 transition-colors"
            >
              {copied ? <Check className="w-4 h-4 text-emerald-600" /> : <Copy className="w-4 h-4" />}
              <span>{copied ? 'Copied to Clipboard!' : 'Copy Tax Breakdown'}</span>
            </button>
          </div>

        </div>

        {/* Lead Magnet CTA */}
        <div className="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-2xl p-6 md:p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
          <div>
            <h3 className="font-display text-lg font-bold text-zinc-950 mb-1">Automate GST Invoicing & Client Portals</h3>
            <p className="text-xs md:text-sm text-zinc-600">
              Cora generates compliant GST invoices, tracks payments via Razorpay UPI, and syncs accounting automatically.
            </p>
          </div>
          <a
            href="https://app.heycora.in/workspace"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white text-xs font-semibold px-5 py-3 rounded-xl hover:bg-zinc-800 transition-all shrink-0"
          >
            <span>Start Free on Cora</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </a>
        </div>

      </div>
    </div>
  );
}
