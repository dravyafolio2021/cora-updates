'use client';

import React, { useState } from 'react';
import { Copy, Check, ArrowRight } from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
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

  const retainerFaqs = [
    {
      question: 'Why should I include a scope creep buffer in retainers?',
      answer: 'Unbilled revisions, client Slack syncs, and emergency turnarounds typically eat 15-25% of agency billable hours. A built-in scope buffer guarantees you hit your profit targets without awkward mid-month renegotiations.'
    },
    {
      question: 'How do I calculate billable capacity for a creative studio team?',
      answer: 'Standard agency utilization assumes 75% of work hours are billable (approx 30 hours per person per week), with remaining time allocated to business development, admin, and internal review.'
    },
    {
      question: 'How does 18% GST apply to recurring retainer agreements?',
      answer: 'Monthly agency retainers are classified under SAC 9983 and subject to standard 18% GST for domestic Indian clients, with CGST/SGST split for intra-state billing.'
    }
  ];

  return (
    <ToolPageShell
      toolId="retainer-calculator"
      badgeTag="Agency Financial Model"
      title="Service Retainer & Scope Buffer Calculator"
      subtitle="Convert your target monthly agency revenue into sustainable client retainer packages with built-in scope creep insurance and 18% GST invoice splitting."
      faqItems={retainerFaqs}
    >
      {/* ── 70% Tool Workspace (Interactive Form + Live Output) ── */}
      <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
        
        {/* Left Console (7 Cols) */}
        <div className="md:col-span-7 bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs space-y-5">
          
          {/* Target Monthly Revenue */}
          <div>
            <div className="flex justify-between items-center mb-1.5">
              <label className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono">
                Target Monthly Revenue
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
              className="w-full h-2 bg-zinc-100 rounded-lg appearance-none cursor-pointer accent-zinc-950"
            />
          </div>

          {/* Billable Capacity */}
          <div>
            <div className="flex justify-between items-center mb-1.5">
              <label className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono">
                Billable Capacity (Hrs / Week)
              </label>
              <span className="font-mono text-sm font-bold text-zinc-950">
                {billableHoursPerWeek} hrs/wk
              </span>
            </div>
            <input
              type="range"
              min="10"
              max="80"
              step="5"
              value={billableHoursPerWeek}
              onChange={(e) => setBillableHoursPerWeek(Number(e.target.value))}
              className="w-full h-2 bg-zinc-100 rounded-lg appearance-none cursor-pointer accent-zinc-950"
            />
          </div>

          {/* Client Account Capacity */}
          <div>
            <label className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono block mb-2">
              Target Active Client Accounts
            </label>
            <div className="grid grid-cols-5 gap-2">
              {[2, 3, 4, 6, 8].map((c) => (
                <button
                  key={c}
                  type="button"
                  onClick={() => setClientCount(c)}
                  className={`py-2 rounded-xl text-xs font-mono font-bold transition-all border cursor-pointer ${
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
                Scope Creep Buffer
              </label>
              <span className="font-mono text-xs font-bold text-indigo-700">
                +{scopeBufferPercent}% Protected
              </span>
            </div>
            <div className="grid grid-cols-4 gap-2">
              {[0, 15, 20, 30].map((b) => (
                <button
                  key={b}
                  type="button"
                  onClick={() => setScopeBufferPercent(b)}
                  className={`py-2 rounded-xl text-xs font-mono font-bold transition-all border cursor-pointer ${
                    scopeBufferPercent === b
                      ? 'bg-zinc-950 text-white border-zinc-950 shadow-xs'
                      : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                  }`}
                >
                  {b === 0 ? '0% (Raw)' : `+${b}%`}
                </button>
              ))}
            </div>
          </div>

        </div>

        {/* Right Output Card (5 Cols — Clean Light Minimal Design) */}
        <div className="md:col-span-5 bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs flex flex-col justify-between space-y-5">
          <div>
            <div className="flex items-center justify-between pb-3 mb-4 border-b border-zinc-200 text-xs">
              <span className="font-mono text-zinc-500 uppercase tracking-wider font-semibold">
                Retainer Package
              </span>
              <span className="text-[10.5px] font-mono text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-2 py-0.5 rounded-full font-bold">
                18% SAC 9983
              </span>
            </div>

            {/* Big Retainer Number */}
            <div className="py-2">
              <span className="text-xs text-zinc-500 block mb-1">Per Client Retainer</span>
              <div className="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-zinc-950">
                ₹{retainerPerClient.toLocaleString('en-IN')}
                <span className="text-xs font-normal text-zinc-400 ml-1">/ mo</span>
              </div>
              <span className="text-xs font-mono text-zinc-500 mt-1.5 block">
                + ₹{gstOnRetainer.toLocaleString('en-IN')} GST = <strong className="text-zinc-950">₹{totalWithGst.toLocaleString('en-IN')} Total</strong>
              </span>
            </div>

            {/* Metric Breakdown Table */}
            <div className="space-y-2 pt-4 border-t border-zinc-200 text-xs font-mono">
              <div className="flex justify-between text-zinc-600">
                <span>Baseline Hourly Rate:</span>
                <span className="font-bold text-zinc-950">₹{hourlyRate.toLocaleString('en-IN')} / hr</span>
              </div>
              <div className="flex justify-between text-zinc-600">
                <span>Hours per Client:</span>
                <span className="font-bold text-zinc-950">{hoursPerClientPerWeek} hrs / week</span>
              </div>
              <div className="flex justify-between text-indigo-600 font-semibold pt-1 border-t border-zinc-100">
                <span>Scope Buffer Reserve:</span>
                <span>+₹{Math.round(effectiveRevenueTarget - targetRevenue).toLocaleString('en-IN')} / mo</span>
              </div>
            </div>
          </div>

          {/* Action Button */}
          <button
            type="button"
            onClick={copyBreakdown}
            className="w-full py-3 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs"
          >
            {copied ? <Check className="w-4 h-4 text-emerald-400" /> : <Copy className="w-4 h-4 text-zinc-400" />}
            <span>{copied ? 'Retainer Math Copied!' : 'Copy Proposal Summary'}</span>
          </button>
        </div>

      </div>
    </ToolPageShell>
  );
}
