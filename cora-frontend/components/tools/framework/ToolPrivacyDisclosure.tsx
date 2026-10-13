'use client';

import React from 'react';
import { ShieldCheck, Lock, EyeOff, Database } from 'lucide-react';

export interface ToolPrivacyDisclosureProps {
  toolName?: string;
  executionMode?: 'browser-only' | 'remote-audit' | 'ai-assisted';
}

export function ToolPrivacyDisclosure({
  toolName = 'this tool',
  executionMode = 'browser-only',
}: ToolPrivacyDisclosureProps) {
  return (
    <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs">
      <div className="flex items-center gap-2 border-b border-zinc-200/70 pb-4 mb-4">
        <ShieldCheck className="w-5 h-5 text-emerald-600" />
        <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight">
          Privacy Guarantee & Data Handling Notice
        </h3>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <div className="rounded-xl border border-zinc-200/70 bg-zinc-50/70 p-4">
          <div className="flex items-center gap-2 mb-2 font-semibold text-zinc-900">
            <Lock className="w-4 h-4 text-zinc-700" />
            <span>100% Client-Side</span>
          </div>
          <p className="text-zinc-600 leading-relaxed">
            All numerical inputs, pricing assumptions, rates, and configurations entered into {toolName} are computed in real time inside your browser session using local JavaScript execution.
          </p>
        </div>

        <div className="rounded-xl border border-zinc-200/70 bg-zinc-50/70 p-4">
          <div className="flex items-center gap-2 mb-2 font-semibold text-zinc-900">
            <Database className="w-4 h-4 text-zinc-700" />
            <span>Zero Server Logging</span>
          </div>
          <p className="text-zinc-600 leading-relaxed">
            We do not transmit, log, store, or serialize your financial models, agency figures, or client metrics to Cora backend servers or third-party cloud data warehouses.
          </p>
        </div>

        <div className="rounded-xl border border-zinc-200/70 bg-zinc-50/70 p-4">
          <div className="flex items-center gap-2 mb-2 font-semibold text-zinc-900">
            <EyeOff className="w-4 h-4 text-zinc-700" />
            <span>No Account Required</span>
          </div>
          <p className="text-zinc-600 leading-relaxed">
            Full diagnostic findings, band scores, formulas, and actionable remedies are provided freely without requiring an email address, login, credit card, or lead gate.
          </p>
        </div>
      </div>
    </div>
  );
}
