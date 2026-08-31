'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { ArrowLeft, Receipt, Copy, Check, Sparkles, ArrowRight, ShieldCheck } from 'lucide-react';
import { useToast } from '@/components/ui/Toast';

export default function RetainerCalculatorPage() {
  const [targetRevenue, setTargetRevenue] = useState<number>(300000);
  const [billableHoursPerWeek, setBillableHoursPerWeek] = useState<number>(30);
  const [clientCount, setClientCount] = useState<number>(4);
  const [scopeBufferPercent, setScopeBufferPercent] = useState<number>(20);
  const [copied, setCopied] = useState<boolean>(false);
  const { showToast } = useToast();

  // Math Calculations
  const monthlyBillableHours = billableHoursPerWeek * 4.2;
  const effectiveRevenueTarget = targetRevenue * (1 + scopeBufferPercent / 100);
  const hourlyRate = Math.round(effectiveRevenueTarget / monthlyBillableHours);
  const retainerPerClient = Math.round(effectiveRevenueTarget / clientCount);
  const hoursPerClientPerWeek = Math.round((billableHoursPerWeek / clientCount) * 10) / 10;
  const gstOnRetainer = Math.round(retainerPerClient * 0.18);
  const totalWithGst = retainerPerClient + gstOnRetainer;

  const copyBreakdown = () => {
    const text = `--- Agency Retainer & Scope Allocation Breakdown ---
Target Monthly Revenue: ₹${targetRevenue.toLocaleString('en-IN')}
Scope Buffer (+${scopeBufferPercent}%): ₹${Math.round(effectiveRevenueTarget - targetRevenue).toLocaleString('en-IN')}
Total Effective Target: ₹${Math.round(effectiveRevenueTarget).toLocaleString('en-IN')}

Baseline Hourly Rate: ₹${hourlyRate.toLocaleString('en-IN')} / hr
Client Capacity: ${clientCount} active accounts (${hoursPerClientPerWeek} hrs/wk per client)
Recommended Retainer: ₹${retainerPerClient.toLocaleString('en-IN')} / month
18% GST (SAC 9983): ₹${gstOnRetainer.toLocaleString('en-IN')}
Total Invoice to Client: ₹${totalWithGst.toLocaleString('en-IN')} / month

Generated via Cora Tools (https://heycora.in/tools/retainer-calculator)`;

    navigator.clipboard.writeText(text);
    setCopied(true);
    showToast('Retainer breakdown copied to clipboard!');
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <div className="py-12 md:py-20 bg-white min-h-screen">
      <div className="w-full max-w-[960px] mx-auto px-4 sm:px-6">
        
        {/* Back navigation */}
        <Link
          href="/tools"
          className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 mb-8 transition-colors"
        >
          <ArrowLeft className="w-4 h-4" /> Back to all micro-tools
        </Link>

        {/* Tool Header */}
        <div className="mb-10">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200/80 text-[11px] font-mono font-bold mb-3">
            <Receipt className="w-3.5 h-3.5" />
            <span>Zero-Login Agency Financial Tool</span>
          </div>
          <h1 className="font-display text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight text-zinc-950 mb-3">
            Service Retainer &amp; Scope Buffer Calculator
          </h1>
          <p className="text-sm sm:text-base text-zinc-600 max-w-[700px] leading-relaxed">
            Convert your target monthly agency revenue into sustainable client retainer packages with built-in scope creep insurance and 18% GST invoice splitting.
          </p>
        </div>

        {/* Interactive Calculator Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* Controls Form (Left) */}
          <div className="lg:col-span-7 bg-white border border-zinc-200 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            
            {/* Target Monthly Revenue */}
            <div>
              <div className="flex justify-between items-center mb-2">
                <label className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono">
                  Target Monthly Net Revenue (INR)
                </label>
                <span className="font-mono text-sm font-bold text-zinc-950">
                  ₹{targetRevenue.toLocaleString('en-IN')}
                </span>
              </div>
              <input
                type="range"
                min="50000"
                max="2000000"
                step="25000"
                value={targetRevenue}
                onChange={(e) => setTargetRevenue(Number(e.target.value))}
                className="w-full accent-zinc-950 h-2 bg-zinc-100 rounded-lg cursor-pointer"
              />
              <div className="flex justify-between text-[11px] font-mono text-zinc-400 mt-1">
                <span>₹50K</span>
                <span>₹10L</span>
                <span>₹20L</span>
              </div>
            </div>

            {/* Billable Hours Per Week */}
            <div>
              <div className="flex justify-between items-center mb-2">
                <label className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono">
                  Weekly Billable Hours
                </label>
                <span className="font-mono text-sm font-bold text-zinc-950">
                  {billableHoursPerWeek} hrs / week
                </span>
              </div>
              <input
                type="range"
                min="10"
                max="60"
                step="5"
                value={billableHoursPerWeek}
                onChange={(e) => setBillableHoursPerWeek(Number(e.target.value))}
                className="w-full accent-zinc-950 h-2 bg-zinc-100 rounded-lg cursor-pointer"
              />
              <div className="flex justify-between text-[11px] font-mono text-zinc-400 mt-1">
                <span>10 hrs (Part-time)</span>
                <span>30 hrs (Optimal)</span>
                <span>60 hrs (Agency Team)</span>
              </div>
            </div>

            {/* Active Client Capacity */}
            <div>
              <div className="flex justify-between items-center mb-2">
                <label className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono">
                  Target Client Capacity
                </label>
                <span className="font-mono text-sm font-bold text-zinc-950">
                  {clientCount} Clients
                </span>
              </div>
              <div className="grid grid-cols-5 gap-2">
                {[2, 3, 4, 6, 8].map((c) => (
                  <button
                    key={c}
                    type="button"
                    onClick={() => setClientCount(c)}
                    className={`py-2 rounded-xl text-xs font-mono font-bold transition-all border ${
                      clientCount === c
                        ? 'bg-zinc-950 text-white border-zinc-950 shadow-xs'
                        : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                    }`}
                  >
                    {c}
                  </button>
                ))}
              </div>
            </div>

            {/* Scope Creep Buffer */}
            <div>
              <div className="flex justify-between items-center mb-2">
                <label className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono">
                  Scope Creep &amp; Overhead Buffer
                </label>
                <span className="font-mono text-sm font-bold text-emerald-700">
                  +{scopeBufferPercent}% Protected
                </span>
              </div>
              <div className="grid grid-cols-4 gap-2">
                {[0, 15, 20, 30].map((b) => (
                  <button
                    key={b}
                    type="button"
                    onClick={() => setScopeBufferPercent(b)}
                    className={`py-2 rounded-xl text-xs font-mono font-bold transition-all border ${
                      scopeBufferPercent === b
                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs'
                        : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                    }`}
                  >
                    {b === 0 ? '0% (Raw)' : `+${b}%`}
                  </button>
                ))}
              </div>
            </div>

          </div>

          {/* Results Summary Card (Right) */}
          <div className="lg:col-span-5 bg-zinc-950 text-white border border-zinc-800 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col justify-between space-y-6">
            <div>
              <div className="flex items-center justify-between pb-4 border-b border-zinc-800">
                <span className="text-[11px] font-mono uppercase tracking-wider text-zinc-400 font-bold">
                  Recommended Retainer
                </span>
                <span className="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] font-mono font-bold">
                  18% SAC 9983
                </span>
              </div>

              {/* Big Retainer Number */}
              <div className="py-5">
                <span className="text-xs text-zinc-400 block mb-1">Per Client Retainer</span>
                <div className="font-display text-4xl sm:text-5xl font-extrabold tracking-tight text-white">
                  ₹{retainerPerClient.toLocaleString('en-IN')}
                  <span className="text-xs font-normal text-zinc-400 ml-1">/ mo</span>
                </div>
                <span className="text-xs font-mono text-zinc-400 mt-2 block">
                  + ₹{gstOnRetainer.toLocaleString('en-IN')} GST = <strong className="text-emerald-400">₹{totalWithGst.toLocaleString('en-IN')} Total</strong>
                </span>
              </div>

              {/* Metric Breakdown Table */}
              <div className="space-y-2.5 pt-4 border-t border-zinc-800/80 text-xs font-mono">
                <div className="flex justify-between text-zinc-300">
                  <span className="text-zinc-400">Baseline Hourly Rate:</span>
                  <span className="font-bold text-white">₹{hourlyRate.toLocaleString('en-IN')} / hr</span>
                </div>
                <div className="flex justify-between text-zinc-300">
                  <span className="text-zinc-400">Hours per Client:</span>
                  <span className="font-bold text-white">{hoursPerClientPerWeek} hrs / week</span>
                </div>
                <div className="flex justify-between text-zinc-300">
                  <span className="text-zinc-400">Scope Buffer Fund:</span>
                  <span className="font-bold text-emerald-400">+₹{Math.round(effectiveRevenueTarget - targetRevenue).toLocaleString('en-IN')} / mo</span>
                </div>
              </div>
            </div>

            {/* Action Buttons */}
            <div className="space-y-3 pt-4 border-t border-zinc-800">
              <button
                onClick={copyBreakdown}
                className="w-full py-3.5 rounded-xl bg-white hover:bg-zinc-100 text-zinc-950 font-semibold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm"
              >
                {copied ? <Check className="w-4 h-4 text-emerald-600" /> : <Copy className="w-4 h-4" />}
                <span>{copied ? 'Retainer Math Copied!' : 'Copy Proposal Summary'}</span>
              </button>

              <a
                href="https://app.heycora.in/workspace/login?source=tools_retainer_calc"
                className="w-full py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-xs border border-zinc-700 transition-all flex items-center justify-center gap-1.5"
              >
                <span>Automate Retainers in Cora Workspace</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>
            </div>

          </div>

        </div>

      </div>
    </div>
  );
}
