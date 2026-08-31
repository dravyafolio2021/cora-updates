'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { ArrowLeft, Scale, Copy, Check, ShieldCheck, ArrowRight, FileText } from 'lucide-react';
import { useToast } from '@/components/ui/Toast';

const TEMPLATES = [
  {
    id: 'msa_scope_lock',
    title: 'Master Services Agreement (Scope-Locked)',
    description: 'Fixed-fee or milestone digital services contract with scope creep addendum gates.',
    clause: (p: any) => `MASTER SERVICES AGREEMENT (MSA)
Governed under the Indian Contract Act, 1872 & Information Technology Act, 2000 (Section 10A)

1. PARTIES:
This Agreement is entered into between ${p.providerName || '[Provider Business Name]'} ("Service Provider") and ${p.clientName || '[Client Entity Name]'} ("Client").

2. SCOPE & MILESTONES:
Service Provider shall perform digital services as defined in Schedule A. Any feature request, design revision, or scope modification beyond the agreed deliverable shall require a formal written Addendum signed under Section 10A of the IT Act 2000.

3. FEES & 18% GST:
In consideration of the Services, Client agrees to pay ₹${p.feeAmount || '1,00,000'} per milestone. All invoices shall include 18% GST under SAC Code 9983. Invoices are payable within 7 business days.

4. INTELLECTUAL PROPERTY & TITLE PASSING:
All source code, designs, and deliverables remain the exclusive property of Service Provider until 100% of the invoiced milestone fee has cleared Service Provider's designated bank account. Upon full settlement, title transfers to Client under Section 14 of the Indian Copyright Act 1957.

5. DISPUTE RESOLUTION & JURISDICTION:
This agreement is governed by the laws of India. Courts in ${p.jurisdiction || 'Mumbai, Maharashtra'} shall have exclusive jurisdiction.`
  },
  {
    id: 'sprint_retainer',
    title: 'Agile Sprint Retainer & Staging Escrow Deed',
    description: '2-week sprint retainers with pre-deployment staging sign-off locks.',
    clause: (p: any) => `AGILE SPRINT RETAINER & STAGING SIGN-OFF DEED
Enforceable under Section 10A of the Indian Information Technology Act, 2000

1. SPRINT CYCLES:
Services shall be delivered in continuous two-week Sprint Cycles. The monthly recurring retainer fee is ₹${p.feeAmount || '1,50,000'} + 18% GST, payable in advance on the 1st of each calendar month.

2. STAGING GATE SIGN-OFF:
Production deployment to public domains shall only execute following written or digital sign-off of the staging build by ${p.clientName || '[Client Entity Name]'}. Failure to review staging builds within 3 business days shall constitute deemed approval.

3. CANCELLATION & NOTICE:
Either party may terminate this retainer by providing a minimum ${p.noticePeriod || '30'}-day written notice. Fees paid for active sprints are non-refundable.

4. ELECTRONIC RECORD INTEGRITY:
The parties acknowledge that electronic signatures with SHA-256 hash timestamps shall constitute conclusive evidence of mutual assent.`
  },
  {
    id: 'nda_ip',
    title: 'Mutual Non-Disclosure & IP Assignment',
    description: 'Bilateral confidentiality deed protecting trade secrets and proprietary algorithms.',
    clause: (p: any) => `MUTUAL NON-DISCLOSURE & PROPRIETARY IP AGREEMENT

1. CONFIDENTIAL INFORMATION:
All technical specifications, product architecture, client records, and commercial pricing disclosed by ${p.providerName || '[Provider Business Name]'} and ${p.clientName || '[Client Entity Name]'} shall remain strictly confidential.

2. EXCLUSION OF REVERSE ENGINEERING:
Client expressly covenants not to decompile, disassemble, or reverse-engineer any proprietary software, ML pipelines, or UI frameworks provided by Service Provider.

3. DURATION:
Confidentiality obligations shall endure for a period of three (3) years from the date of disclosure.

4. GOVERNING LAW:
Jurisdiction is exclusively vested in the competent courts of ${p.jurisdiction || 'Bengaluru, Karnataka'}.`
  }
];

export default function ContractBuilderPage() {
  const [selectedTemplate, setSelectedTemplate] = useState<string>('msa_scope_lock');
  const [providerName, setProviderName] = useState<string>('Aarav Mehta Studio');
  const [clientName, setClientName] = useState<string>('Apex Enterprises Private Limited');
  const [feeAmount, setFeeAmount] = useState<string>('1,50,000');
  const [noticePeriod, setNoticePeriod] = useState<string>('30');
  const [jurisdiction, setJurisdiction] = useState<string>('Mumbai, Maharashtra');
  const [copied, setCopied] = useState<boolean>(false);
  const { showToast } = useToast();

  const currentTmpl = TEMPLATES.find(t => t.id === selectedTemplate) || TEMPLATES[0];
  const generatedText = currentTmpl.clause({
    providerName,
    clientName,
    feeAmount,
    noticePeriod,
    jurisdiction
  });

  const copyContract = () => {
    navigator.clipboard.writeText(generatedText);
    setCopied(true);
    showToast('Legal contract text copied to clipboard!');
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <div className="py-12 md:py-20 bg-white min-h-screen">
      <div className="w-full max-w-[1040px] mx-auto px-4 sm:px-6">
        
        {/* Back navigation */}
        <Link
          href="/tools"
          className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 mb-8 transition-colors"
        >
          <ArrowLeft className="w-4 h-4" /> Back to all micro-tools
        </Link>

        {/* Header */}
        <div className="mb-10">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-200 text-[11px] font-mono font-bold mb-3">
            <Scale className="w-3.5 h-3.5" />
            <span>Indian IT Act 2000 (Section 10A) Compliant</span>
          </div>
          <h1 className="font-display text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight text-zinc-950 mb-3">
            Contract Clause &amp; Agreement Builder
          </h1>
          <p className="text-sm sm:text-base text-zinc-600 max-w-[700px] leading-relaxed">
            Generate legally enforceable digital contract clauses with scope creep protection, intellectual property locks, and 18% GST terms.
          </p>
        </div>

        {/* Template Selector Pills */}
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8">
          {TEMPLATES.map((tmpl) => (
            <button
              key={tmpl.id}
              type="button"
              onClick={() => setSelectedTemplate(tmpl.id)}
              className={`p-4 rounded-2xl text-left border transition-all cursor-pointer ${
                selectedTemplate === tmpl.id
                  ? 'bg-zinc-950 text-white border-zinc-950 shadow-md'
                  : 'bg-white text-zinc-800 border-zinc-200 hover:border-zinc-300'
              }`}
            >
              <span className="font-bold text-xs sm:text-sm block mb-1">
                {tmpl.title}
              </span>
              <span className={`text-[11px] leading-relaxed block ${selectedTemplate === tmpl.id ? 'text-zinc-300' : 'text-zinc-500'}`}>
                {tmpl.description}
              </span>
            </button>
          ))}
        </div>

        {/* Main Grid: Form Inputs & Live Preview */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* Parameters Form (Left) */}
          <div className="lg:col-span-5 bg-white border border-zinc-200 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 pb-2 border-b border-zinc-100">
              Contract Parameters
            </h3>

            <div>
              <label className="text-xs font-bold text-zinc-800 block mb-1">Your Business Name</label>
              <input
                type="text"
                value={providerName}
                onChange={(e) => setProviderName(e.target.value)}
                className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
              />
            </div>

            <div>
              <label className="text-xs font-bold text-zinc-800 block mb-1">Client Entity Name</label>
              <input
                type="text"
                value={clientName}
                onChange={(e) => setClientName(e.target.value)}
                className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
              />
            </div>

            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="text-xs font-bold text-zinc-800 block mb-1">Fee (INR)</label>
                <input
                  type="text"
                  value={feeAmount}
                  onChange={(e) => setFeeAmount(e.target.value)}
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
                />
              </div>
              <div>
                <label className="text-xs font-bold text-zinc-800 block mb-1">Notice (Days)</label>
                <input
                  type="text"
                  value={noticePeriod}
                  onChange={(e) => setNoticePeriod(e.target.value)}
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
                />
              </div>
            </div>

            <div>
              <label className="text-xs font-bold text-zinc-800 block mb-1">Jurisdiction City / State</label>
              <input
                type="text"
                value={jurisdiction}
                onChange={(e) => setJurisdiction(e.target.value)}
                className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
              />
            </div>

            <div className="p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200/80 text-[11px] text-zinc-600 font-mono space-y-1">
              <span className="font-bold text-zinc-900 block">✓ Digital E-Sign Seal Included</span>
              <span>SHA-256 integrity hash and timestamp clauses pre-formatted.</span>
            </div>
          </div>

          {/* Live Preview Box (Right) */}
          <div className="lg:col-span-7 bg-zinc-950 text-white rounded-3xl p-6 sm:p-7 shadow-xl space-y-4">
            <div className="flex items-center justify-between pb-3 border-b border-zinc-800 text-xs font-mono">
              <span className="text-zinc-400">Generated Legal Agreement Text</span>
              <span className="text-emerald-400 font-bold">100% Client-Side</span>
            </div>

            <div className="bg-zinc-900/90 border border-zinc-800 rounded-2xl p-4 font-mono text-xs text-zinc-300 leading-relaxed max-h-[380px] overflow-y-auto whitespace-pre-wrap select-all">
              {generatedText}
            </div>

            <div className="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
              <button
                onClick={copyContract}
                className="w-full sm:w-auto px-6 py-3 rounded-xl bg-white hover:bg-zinc-100 text-zinc-950 font-semibold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm"
              >
                {copied ? <Check className="w-4 h-4 text-emerald-600" /> : <Copy className="w-4 h-4" />}
                <span>{copied ? 'Contract Text Copied!' : 'Copy Legal Clauses'}</span>
              </button>

              <a
                href="https://app.heycora.in/workspace/login?source=tools_contract_builder"
                className="w-full sm:w-auto px-5 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-xs border border-zinc-700 transition-all flex items-center justify-center gap-1.5"
              >
                <span>Automated E-Sign in Cora</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>
            </div>
          </div>

        </div>

      </div>
    </div>
  );
}
