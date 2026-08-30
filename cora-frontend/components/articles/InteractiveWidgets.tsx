'use client';

import React, { useState } from 'react';
import { Calculator, ArrowRight, CheckCircle2, TrendingUp, ShieldCheck } from 'lucide-react';
import Link from 'next/link';

interface InteractiveWidgetProps {
  widgetType?: 'gst-calculator' | 'deal-simulator' | 'comparison-matrix' | 'none';
}

export function InteractiveWidget({ widgetType }: InteractiveWidgetProps) {
  if (!widgetType || widgetType === 'none') return null;

  if (widgetType === 'gst-calculator') {
    return <GstCalculatorWidget />;
  }

  if (widgetType === 'deal-simulator') {
    return <DealSimulatorWidget />;
  }

  if (widgetType === 'comparison-matrix') {
    return <ComparisonMatrixWidget />;
  }

  return null;
}

function GstCalculatorWidget() {
  const [baseAmount, setBaseAmount] = useState<number>(100000);
  const [isInterState, setIsInterState] = useState<boolean>(false);

  const gstRate = 0.18;
  const totalTax = baseAmount * gstRate;
  const cgst = totalTax / 2;
  const sgst = totalTax / 2;
  const igst = totalTax;
  const grandTotal = baseAmount + totalTax;

  return (
    <div className="my-8 p-6 sm:p-8 rounded-3xl bg-zinc-950 text-white border border-zinc-800 shadow-xl space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2">
          <div className="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white">
            <Calculator className="w-4 h-4" />
          </div>
          <div>
            <h4 className="text-base font-bold text-white">Interactive 18% GST Calculator</h4>
            <p className="text-xs text-zinc-400">SAC Code 998381 Tax Math Simulator</p>
          </div>
        </div>
        <span className="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-[10px] font-mono font-bold text-emerald-400 border border-emerald-500/20">
          INDIA STATUTORY
        </span>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 items-end">
        <div className="space-y-2">
          <label className="text-xs font-mono text-zinc-400">Project Commercial Value (₹)</label>
          <input
            type="number"
            value={baseAmount}
            onChange={(e) => setBaseAmount(Number(e.target.value) || 0)}
            className="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-mono text-base focus:outline-hidden focus:border-white transition-all"
            min="0"
            step="5000"
          />
        </div>

        <div className="space-y-2">
          <label className="text-xs font-mono text-zinc-400">Supply Classification</label>
          <div className="grid grid-cols-2 gap-2">
            <button
              onClick={() => setIsInterState(false)}
              className={`px-3 py-2.5 rounded-xl text-xs font-semibold transition-all ${
                !isInterState
                  ? 'bg-white text-zinc-950 shadow-sm'
                  : 'bg-zinc-900 text-zinc-400 border border-zinc-800 hover:text-white'
              }`}
            >
              Intra-State (Same)
            </button>
            <button
              onClick={() => setIsInterState(true)}
              className={`px-3 py-2.5 rounded-xl text-xs font-semibold transition-all ${
                isInterState
                  ? 'bg-white text-zinc-950 shadow-sm'
                  : 'bg-zinc-900 text-zinc-400 border border-zinc-800 hover:text-white'
              }`}
            >
              Inter-State (IGST)
            </button>
          </div>
        </div>
      </div>

      <div className="p-5 rounded-2xl bg-zinc-900/90 border border-zinc-800 space-y-3 font-mono text-xs">
        <div className="flex justify-between text-zinc-400">
          <span>Taxable Project Base:</span>
          <span className="text-white">₹{baseAmount.toLocaleString('en-IN')}</span>
        </div>

        {!isInterState ? (
          <>
            <div className="flex justify-between text-zinc-400">
              <span>CGST (9.0%):</span>
              <span className="text-emerald-400">+₹{cgst.toLocaleString('en-IN')}</span>
            </div>
            <div className="flex justify-between text-zinc-400">
              <span>SGST (9.0%):</span>
              <span className="text-emerald-400">+₹{sgst.toLocaleString('en-IN')}</span>
            </div>
          </>
        ) : (
          <div className="flex justify-between text-zinc-400">
            <span>IGST (18.0%):</span>
            <span className="text-emerald-400">+₹{igst.toLocaleString('en-IN')}</span>
          </div>
        )}

        <div className="pt-3 border-t border-zinc-800 flex justify-between text-sm font-bold text-white">
          <span>Total Invoiced Gross:</span>
          <span className="text-base text-emerald-400">₹{grandTotal.toLocaleString('en-IN')}</span>
        </div>
      </div>
    </div>
  );
}

function DealSimulatorWidget() {
  const [dealValue, setDealValue] = useState<number>(200000);
  const [crewCost, setCrewCost] = useState<number>(50000);
  const [gearRental, setGearRental] = useState<number>(25000);
  const [postProd, setPostProd] = useState<number>(20000);

  const totalDirectCosts = crewCost + gearRental + postProd;
  const netProfit = Math.max(0, dealValue - totalDirectCosts);
  const marginPercent = dealValue > 0 ? Math.round((netProfit / dealValue) * 100) : 0;

  return (
    <div className="my-8 p-6 sm:p-8 rounded-3xl bg-zinc-950 text-white border border-zinc-800 shadow-xl space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2">
          <div className="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white">
            <TrendingUp className="w-4 h-4" />
          </div>
          <div>
            <h4 className="text-base font-bold text-white">Shoot Deal Profitability Simulator</h4>
            <p className="text-xs text-zinc-400">Model net margin before sending proposals</p>
          </div>
        </div>
        <span className={`px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold border ${
          marginPercent >= 45
            ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
            : marginPercent >= 30
            ? 'bg-amber-500/10 text-amber-400 border-amber-500/20'
            : 'bg-rose-500/10 text-rose-400 border-rose-500/20'
        }`}>
          {marginPercent}% MARGIN ({marginPercent >= 45 ? 'HEALTHY' : marginPercent >= 30 ? 'MODERATE' : 'LOW'})
        </span>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div className="space-y-1.5">
          <label className="text-xs font-mono text-zinc-400">Target Client Quote (₹)</label>
          <input
            type="number"
            value={dealValue}
            onChange={(e) => setDealValue(Number(e.target.value) || 0)}
            className="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-mono text-sm"
          />
        </div>
        <div className="space-y-1.5">
          <label className="text-xs font-mono text-zinc-400">Crew &amp; Assistant Labor (₹)</label>
          <input
            type="number"
            value={crewCost}
            onChange={(e) => setCrewCost(Number(e.target.value) || 0)}
            className="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-mono text-sm"
          />
        </div>
        <div className="space-y-1.5">
          <label className="text-xs font-mono text-zinc-400">Gear &amp; Studio Stage Rentals (₹)</label>
          <input
            type="number"
            value={gearRental}
            onChange={(e) => setGearRental(Number(e.target.value) || 0)}
            className="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-mono text-sm"
          />
        </div>
        <div className="space-y-1.5">
          <label className="text-xs font-mono text-zinc-400">Post-Production &amp; Editing (₹)</label>
          <input
            type="number"
            value={postProd}
            onChange={(e) => setPostProd(Number(e.target.value) || 0)}
            className="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-white font-mono text-sm"
          />
        </div>
      </div>

      <div className="p-4 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-between text-xs font-mono">
        <div>
          <div className="text-zinc-400">Total Direct Expenses:</div>
          <div className="text-white font-bold text-sm">₹{totalDirectCosts.toLocaleString('en-IN')}</div>
        </div>
        <div className="text-right">
          <div className="text-zinc-400">Estimated Net Studio Profit:</div>
          <div className="text-emerald-400 font-bold text-base">₹{netProfit.toLocaleString('en-IN')}</div>
        </div>
      </div>
    </div>
  );
}

function ComparisonMatrixWidget() {
  return (
    <div className="my-8 p-6 rounded-3xl bg-zinc-50 border border-zinc-200 space-y-4">
      <div className="flex items-center justify-between">
        <h4 className="text-sm font-bold text-zinc-950 font-display">
          Cora Studio OS vs. Legacy CRM Ecosystem
        </h4>
        <Link href="/compare" className="text-xs font-semibold text-zinc-900 hover:text-black flex items-center gap-1">
          <span>View all 8 comparisons</span>
          <ArrowRight className="w-3.5 h-3.5" />
        </Link>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
        <div className="p-3.5 rounded-xl bg-white border border-zinc-200/80 space-y-1">
          <div className="font-bold text-zinc-900">Autonomous AI</div>
          <div className="text-zinc-500 text-[11px]">Claude 3.5 + Gemini routing with RAG context</div>
        </div>
        <div className="p-3.5 rounded-xl bg-white border border-zinc-200/80 space-y-1">
          <div className="font-bold text-zinc-900">18% GST Compliant</div>
          <div className="text-zinc-500 text-[11px]">Automated CGST/SGST splitting &amp; UPI clearing</div>
        </div>
        <div className="p-3.5 rounded-xl bg-white border border-zinc-200/80 space-y-1">
          <div className="font-bold text-zinc-900">All-in-One 20 Modules</div>
          <div className="text-zinc-500 text-[11px]">Save ₹57,000+/yr by consolidating 5 software tools</div>
        </div>
      </div>
    </div>
  );
}
