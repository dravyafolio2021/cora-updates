'use client';

import React, { useState } from 'react';
import { Copy, Check } from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
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
    const text = `--- Indian GST Calculation Breakdown ---
Base Amount: ₹${baseAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
GST Rate: ${gstRate}% (${taxMode === 'exclusive' ? 'Exclusive (+ Tax)' : 'Inclusive (Inside Total)'})
${isInterstate ? `IGST (Integrated Tax): ₹${igstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}` : `CGST (Central Tax 50%): ₹${cgstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}\nSGST (State Tax 50%): ₹${sgstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}`}
Total Gross Invoice: ₹${totalAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
Generated via Cora Tools (https://heycora.in/tools/gst-calculator)`;

    navigator.clipboard.writeText(text);
    setCopied(true);
    showToast('GST calculation copied to clipboard!');
    setTimeout(() => setCopied(false), 2000);
  };

  const gstFaqs = [
    {
      question: 'What is SAC Code 9983 for Indian creative & tech services?',
      answer: 'SAC 9983 covers Other professional, technical and business services, including software development, UI/UX design, consulting, photography, and marketing agency retainers under the standard 18% GST tax slab.'
    },
    {
      question: 'When should I charge CGST + SGST vs IGST?',
      answer: 'If your client is located in the same Indian state as your registered GSTIN, charge CGST (9%) and SGST (9%). If your client is located in a different Indian state, charge IGST (18%) in full.'
    },
    {
      question: 'How do I handle GST on international client invoices?',
      answer: 'Services exported to clients outside India qualify as Zero-Rated Supplies under a Letter of Undertaking (LUT), meaning 0% GST applies provided payment is realized in convertible foreign exchange.'
    }
  ];

  return (
    <ToolPageShell
      toolId="gst-calculator"
      badgeTag="🇮🇳 Indian Tax Standard"
      title="Indian GST & B2B Invoice Calculator"
      subtitle="Calculate instant 18%, 12%, 5%, and 28% GST breakdowns with CGST/SGST vs IGST segregation for B2B client invoices and freelance retainers."
      promoTitle="Automate GST Invoicing & Client Portals"
      promoSubtitle="Cora auto-generates compliant SAC 9983 tax invoices, collects via 0% fee UPI QR, and reconciles payments directly in client WhatsApp portals."
      promoHighlights={[
        'Auto-split 18% CGST & SGST on client proposals',
        'Instant 0% fee UPI 2.0 payment QR generation',
        'WhatsApp milestone notifications & receipt dispatch',
        'Direct export to Tally Prime, Zoho Books & QuickBooks',
      ]}
      promoCtaText="Launch Free Workspace"
      faqItems={gstFaqs}
    >
      {/* ── 70% Tool Engine (Interactive 2-Card Layout) ── */}
      <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
        
        {/* Left Inputs Console (7 Cols) */}
        <div className="md:col-span-7 rounded-3xl bg-white border border-zinc-200/90 p-5 sm:p-6 shadow-xs space-y-5">
          
          {/* Input Amount */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
              Transaction Amount (₹)
            </label>
            <div className="relative">
              <span className="absolute left-3.5 top-1/2 -translate-y-1/2 font-mono font-bold text-zinc-400">₹</span>
              <input
                type="number"
                value={amount}
                onChange={(e) => setAmount(Math.max(0, Number(e.target.value)))}
                className="w-full pl-9 pr-4 py-3 rounded-2xl border border-zinc-200 font-mono text-lg font-bold text-zinc-950 focus:outline-none focus:border-zinc-950 transition-colors"
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
                  className={`py-2.5 rounded-xl font-mono text-xs font-bold transition-all cursor-pointer ${
                    gstRate === rate
                      ? 'bg-zinc-950 text-white shadow-xs'
                      : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950'
                  }`}
                >
                  {rate}%
                </button>
              ))}
            </div>
          </div>

          {/* Tax Mode Toggle (Exclusive vs Inclusive) */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
              GST Inclusion Mode
            </label>
            <div className="grid grid-cols-2 gap-2">
              <button
                type="button"
                onClick={() => setTaxMode('exclusive')}
                className={`py-2.5 px-3 rounded-xl text-xs font-semibold transition-all cursor-pointer ${
                  taxMode === 'exclusive'
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                }`}
              >
                GST Exclusive (+ Tax)
              </button>
              <button
                type="button"
                onClick={() => setTaxMode('inclusive')}
                className={`py-2.5 px-3 rounded-xl text-xs font-semibold transition-all cursor-pointer ${
                  taxMode === 'inclusive'
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                }`}
              >
                GST Inclusive (Inside Total)
              </button>
            </div>
          </div>

          {/* Supply Jurisdiction Toggle */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
              Supply Jurisdiction
            </label>
            <div className="grid grid-cols-2 gap-2">
              <button
                type="button"
                onClick={() => setIsInterstate(false)}
                className={`py-2.5 px-3 rounded-xl text-xs font-semibold transition-all cursor-pointer ${
                  !isInterstate
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                }`}
              >
                Intra-State (CGST + SGST)
              </button>
              <button
                type="button"
                onClick={() => setIsInterstate(true)}
                className={`py-2.5 px-3 rounded-xl text-xs font-semibold transition-all cursor-pointer ${
                  isInterstate
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                }`}
              >
                Inter-State (IGST)
              </button>
            </div>
          </div>

        </div>

        {/* Right Output Engine Card (5 Cols — Clean Light Minimal Design) */}
        <div className="md:col-span-5 rounded-3xl bg-zinc-50 border border-zinc-200/90 p-5 sm:p-6 shadow-2xs flex flex-col justify-between">
          <div>
            <div className="flex items-center justify-between pb-3 mb-4 border-b border-zinc-200 text-xs">
              <span className="font-mono text-zinc-500 uppercase tracking-wider font-semibold">
                Invoice Summary
              </span>
              <span className="text-[11px] font-mono text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-2 py-0.5 rounded-full font-bold">
                {gstRate}% {taxMode}
              </span>
            </div>

            <div className="space-y-3 font-mono text-xs">
              <div className="flex justify-between text-zinc-600">
                <span>Net Base Value:</span>
                <span className="font-bold text-zinc-950">
                  ₹{baseAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
                </span>
              </div>

              {!isInterstate ? (
                <>
                  <div className="flex justify-between text-zinc-500">
                    <span>CGST ({(gstRate / 2)}%):</span>
                    <span className="font-medium text-zinc-800">₹{cgstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}</span>
                  </div>
                  <div className="flex justify-between text-zinc-500">
                    <span>SGST ({(gstRate / 2)}%):</span>
                    <span className="font-medium text-zinc-800">₹{sgstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}</span>
                  </div>
                </>
              ) : (
                <div className="flex justify-between text-zinc-500">
                  <span>IGST ({gstRate}%):</span>
                  <span className="font-medium text-zinc-800">₹{igstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}</span>
                </div>
              )}

              <div className="flex justify-between text-indigo-600 font-bold pt-2.5 border-t border-zinc-200">
                <span>Total Tax ({gstRate}%):</span>
                <span>+₹{gstAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}</span>
              </div>
            </div>

            {/* Total Highlight */}
            <div className="mt-5 pt-4 border-t border-zinc-200">
              <span className="block text-[11px] font-mono text-zinc-500 uppercase tracking-wider mb-1">
                Gross Invoice Total
              </span>
              <div className="text-2xl sm:text-3xl font-mono font-extrabold text-zinc-950 tracking-tight">
                ₹{totalAmount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}
              </div>
            </div>
          </div>

          {/* Copy Button */}
          <button
            type="button"
            onClick={copyBreakdown}
            className="w-full mt-6 py-3 px-4 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs flex items-center justify-center gap-2 transition-colors cursor-pointer shadow-xs"
          >
            {copied ? (
              <>
                <Check className="w-4 h-4 text-emerald-400" />
                <span>Copied to Clipboard!</span>
              </>
            ) : (
              <>
                <Copy className="w-4 h-4 text-zinc-400" />
                <span>Copy Tax Breakdown</span>
              </>
            )}
          </button>
        </div>

      </div>
    </ToolPageShell>
  );
}
