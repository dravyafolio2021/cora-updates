'use client';

import React, { useState } from 'react';
import { UserCheck, Sparkles, FileSignature, CheckCircle, ArrowRight, ShieldCheck } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

const workflowSteps = [
  {
    step: '01',
    title: 'Instant Lead Capture',
    badge: 'Inbound Hook',
    description: 'A client submits an inquiry through your custom portal or public form. Cora instantly logs the lead and dispatches a confirmation to their WhatsApp.',
    code: `{
  "client": "Aura Labs Inc.",
  "service": "Architectural Shoot & Video Tour",
  "budget": "₹85,000",
  "status": "Inquiry Captured"
}`,
  },
  {
    step: '02',
    title: 'AI Proposal Generation',
    badge: 'Claude 3.5 Sonnet',
    description: 'With 1 click, Cora’s frontier AI drafts a tailored shoot proposal, call-sheet schedule, and deliverables agreement based on your agency templates.',
    code: `{
  "ai_model": "claude-3-5-sonnet",
  "contract_type": "Commercial Production",
  "call_time": "08:30 AM IST",
  "generation_latency": "420ms"
}`,
  },
  {
    step: '03',
    title: 'GST Math & Digital E-Sign',
    badge: '100% Legally Binding',
    description: 'The client receives an interactive signing link. Cora automatically calculates 18% GST (CGST/SGST), logs the cryptographic audit trail, and secures the signature.',
    code: `{
  "base_amount": "₹85,000.00",
  "gst_18_percent": "₹15,300.00",
  "total_payable": "₹1,00,300.00",
  "esign_audit": "Verified (IP + Timestamp)"
}`,
  },
  {
    step: '04',
    title: 'Instant Payment Settlement',
    badge: 'UPI & RuPay Ready',
    description: 'Client completes payment via UPI (GPay, PhonePe, CRED) or corporate cards. The booking is marked Paid and synched to your master dashboard.',
    code: `{
  "settlement_rail": "UPI Direct / Razorpay",
  "transaction_status": "Captured & Settled",
  "payout_status": "Scheduled",
  "workspace_state": "Synced"
}`,
  },
];

export function InteractiveWorkflow() {
  const [activeStep, setActiveStep] = useState<number>(0);

  const handleStepClick = (index: number) => {
    setActiveStep(index);
    trackEvent('workflow_step_clicked', { step: index + 1, step_name: workflowSteps[index].title });
  };

  return (
    <section className="py-16 md:py-24 relative z-10 bg-white border-t border-zinc-100">
      <div className="w-full max-w-[1140px] mx-auto px-4 sm:px-6">
        
        {/* Section Header */}
        <div className="text-center max-w-[780px] mx-auto mb-12">
          <div className="inline-flex items-center gap-1.5 font-sans text-[0.8125rem] font-medium text-zinc-600 px-3.5 py-1 bg-white border border-zinc-200 rounded-full mb-3.5 shadow-sm">
            <span>Automated Lifecycle</span>
          </div>
          <h2 className="font-display text-[clamp(1.85rem,3.8vw,2.75rem)] font-[550] tracking-[-0.035em] text-zinc-950 leading-[1.18] mb-3">
            From initial inquiry to settled invoice in minutes.
          </h2>
          <p className="font-sans text-[clamp(0.85rem,1.1vw,1rem)] text-zinc-600 leading-[1.55]">
            See how Cora orchestrates every phase of client execution automatically.
          </p>
        </div>

        {/* Guided Stepper Tabs */}
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
          {workflowSteps.map((ws, idx) => (
            <button
              key={idx}
              type="button"
              onClick={() => handleStepClick(idx)}
              className={`p-3.5 rounded-xl border text-left transition-all duration-200 ${
                activeStep === idx
                  ? 'bg-zinc-950 text-white border-zinc-950 shadow-sm -translate-y-0.5'
                  : 'bg-white text-zinc-700 border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50'
              }`}
            >
              <div className="flex items-center justify-between mb-1.5">
                <span className={`text-[0.6875rem] font-mono font-bold ${activeStep === idx ? 'text-zinc-400' : 'text-zinc-400'}`}>
                  STEP {ws.step}
                </span>
                {activeStep === idx && <div className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />}
              </div>
              <div className={`text-xs font-bold font-display truncate ${activeStep === idx ? 'text-white' : 'text-zinc-900'}`}>
                {ws.title}
              </div>
            </button>
          ))}
        </div>

        {/* Active Step Showcase Card */}
        <div className="bg-zinc-50 border border-zinc-200 rounded-2xl p-6 sm:p-8 shadow-sm grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
          <div>
            <div className="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider px-2.5 py-1 bg-white border border-zinc-200 rounded-md text-zinc-800 mb-4 shadow-2xs">
              <span>{workflowSteps[activeStep].badge}</span>
            </div>
            <h3 className="font-display text-2xl font-bold text-zinc-950 mb-3">
              {workflowSteps[activeStep].title}
            </h3>
            <p className="font-sans text-sm text-zinc-600 leading-relaxed mb-6">
              {workflowSteps[activeStep].description}
            </p>

            <div className="flex items-center gap-3">
              <a
                href="https://app.heycora.in/workspace/login?source=workflow_cta"
                className="inline-flex items-center gap-1.5 bg-zinc-950 text-white px-4 py-2.5 rounded-xl text-xs font-semibold hover:bg-zinc-800 transition-all shadow-sm"
              >
                <span>Try this workflow free</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </a>
            </div>
          </div>

          {/* Code / State Box */}
          <div className="bg-zinc-50 border border-zinc-200/90 rounded-2xl p-5 shadow-xs">
            <div className="flex items-center justify-between mb-3 pb-2 border-b border-zinc-200">
              <div className="flex items-center gap-1.5">
                <div className="w-2.5 h-2.5 rounded-full bg-zinc-200 border border-zinc-300" />
                <div className="w-2.5 h-2.5 rounded-full bg-zinc-200 border border-zinc-300" />
                <div className="w-2.5 h-2.5 rounded-full bg-zinc-200 border border-zinc-300" />
              </div>
              <span className="text-[0.625rem] font-mono text-zinc-400 uppercase tracking-widest">
                Execution State
              </span>
            </div>
            <pre className="font-mono text-xs text-zinc-800 bg-white p-3.5 rounded-xl border border-zinc-200/70 overflow-x-auto leading-relaxed scrollbar-none">
              <code>{workflowSteps[activeStep].code}</code>
            </pre>
          </div>
        </div>

      </div>
    </section>
  );
}
