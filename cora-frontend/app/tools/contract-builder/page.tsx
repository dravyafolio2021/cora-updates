'use client';

import React, { useState } from 'react';
import { Copy, Check, Scale } from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';

const TEMPLATES = [
  {
    id: 'msa_scope_lock',
    title: 'Master Services Agreement',
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
    title: 'Agile Sprint Retainer Deed',
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
    title: 'Mutual NDA & IP Deed',
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
  const [providerName, setProviderName] = useState<string>('Studio Services');
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
    showToast('Legal contract copied to clipboard!');
    setTimeout(() => setCopied(false), 2000);
  };

  const contractFaqs = [
    {
      question: 'Are digital contracts legally binding in India?',
      answer: 'Yes. Under Section 10A of the Information Technology Act, 2000 and the Indian Contract Act, 1872, digital agreements formed via electronic records and validated with cryptographic audit logs are legally enforceable in Indian courts.'
    },
    {
      question: 'Why is IP title transfer condition important for agencies?',
      answer: 'Tying intellectual property rights assignment to full invoice payment ensures you retain copyright leverage until 100% of agreed fees are realized.'
    },
    {
      question: 'What is a Section 10A electronic record?',
      answer: 'Section 10A validates contract formation through electronic communications where mutual assent is recorded with timestamped digital audit trails.'
    }
  ];

  return (
    <ToolPageShell
      toolId="contract-builder"
      badgeTag="⚖️ IT Act 2000 • Section 10A"
      title="Indian Digital Contract & NDA Builder"
      subtitle="Generate court-admissible freelance contracts, MSAs, and NDAs pre-seeded with Indian Contract Act 1872 clauses, scope locks, and 18% GST terms."
      faqItems={contractFaqs}
    >
      {/* ── 70% Tool Workspace (Interactive Form + Live Clause Box) ── */}
      <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
        
        {/* Left Console (5 Cols) */}
        <div className="md:col-span-5 bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs space-y-4">
          
          {/* Template Selection */}
          <div>
            <label className="text-xs font-bold text-zinc-800 block mb-2 font-mono uppercase tracking-wider">
              Agreement Template
            </label>
            <div className="space-y-1.5">
              {TEMPLATES.map((tmpl) => (
                <button
                  key={tmpl.id}
                  type="button"
                  onClick={() => setSelectedTemplate(tmpl.id)}
                  className={`w-full text-left p-2.5 rounded-xl text-xs transition-all border cursor-pointer ${
                    selectedTemplate === tmpl.id
                      ? 'bg-zinc-950 text-white border-zinc-950 font-bold shadow-xs'
                      : 'bg-zinc-50 border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                  }`}
                >
                  <div className="font-semibold">{tmpl.title}</div>
                </button>
              ))}
            </div>
          </div>

          <div className="space-y-3 pt-3 border-t border-zinc-100">
            <div>
              <label className="text-xs font-bold text-zinc-800 block mb-1">Your Business Name</label>
              <input
                type="text"
                value={providerName}
                onChange={(e) => setProviderName(e.target.value)}
                className="w-full px-3 py-2 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
              />
            </div>

            <div>
              <label className="text-xs font-bold text-zinc-800 block mb-1">Client Entity Name</label>
              <input
                type="text"
                value={clientName}
                onChange={(e) => setClientName(e.target.value)}
                className="w-full px-3 py-2 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
              />
            </div>

            <div className="grid grid-cols-2 gap-2">
              <div>
                <label className="text-xs font-bold text-zinc-800 block mb-1">Fee (INR)</label>
                <input
                  type="text"
                  value={feeAmount}
                  onChange={(e) => setFeeAmount(e.target.value)}
                  className="w-full px-3 py-2 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
                />
              </div>
              <div>
                <label className="text-xs font-bold text-zinc-800 block mb-1">Notice (Days)</label>
                <input
                  type="text"
                  value={noticePeriod}
                  onChange={(e) => setNoticePeriod(e.target.value)}
                  className="w-full px-3 py-2 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
                />
              </div>
            </div>

            <div>
              <label className="text-xs font-bold text-zinc-800 block mb-1">Jurisdiction City / State</label>
              <input
                type="text"
                value={jurisdiction}
                onChange={(e) => setJurisdiction(e.target.value)}
                className="w-full px-3 py-2 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
              />
            </div>
          </div>

        </div>

        {/* Right Live Preview Box (7 Cols) */}
        <div className="md:col-span-7 bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs flex flex-col justify-between space-y-4">
          <div>
            <div className="flex items-center justify-between pb-3 border-b border-zinc-200 text-xs font-mono">
              <span className="text-zinc-500 font-semibold uppercase tracking-wider">
                Generated Legal Agreement
              </span>
              <span className="text-[10.5px] font-mono text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full font-bold">
                Section 10A Ready
              </span>
            </div>

            <div className="bg-zinc-50 border border-zinc-200/80 rounded-2xl p-4 font-mono text-[11.5px] text-zinc-800 leading-relaxed max-h-[360px] overflow-y-auto whitespace-pre-wrap select-all mt-3">
              {generatedText}
            </div>
          </div>

          <button
            type="button"
            onClick={copyContract}
            className="w-full py-3 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs"
          >
            {copied ? <Check className="w-4 h-4 text-emerald-400" /> : <Copy className="w-4 h-4 text-zinc-400" />}
            <span>{copied ? 'Contract Clauses Copied!' : 'Copy Legal Clauses'}</span>
          </button>
        </div>

      </div>
    </ToolPageShell>
  );
}
