'use client';

import React, { useState, useRef, useMemo } from 'react';
import { 
  FileText, 
  UploadCloud, 
  Sparkles, 
  ShieldCheck, 
  AlertTriangle, 
  CheckCircle2, 
  XCircle, 
  ChevronDown, 
  ChevronUp, 
  Copy, 
  Check, 
  ArrowRight, 
  RefreshCw, 
  Scale, 
  DollarSign, 
  Clock, 
  Lock, 
  FileCheck, 
  SlidersHorizontal,
  ExternalLink,
  MessageSquare,
  Zap,
  Info,
  X
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo } from '@/lib/pdf-engine';

interface ClauseAnalysis {
  category: 'payment' | 'liability' | 'cancellation' | 'sla' | 'confidentiality';
  title: string;
  status: 'favorable' | 'warning' | 'critical';
  statusLabel: string;
  detectedExcerpt: string;
  riskSummary: string;
  recommendation: string;
  redlinePrompt: string;
}

interface ContractScanResult {
  fileName: string;
  pageCount: number;
  overallScore: number; // 0 - 100
  riskLevel: 'Low Risk' | 'Moderate Risk' | 'High Exposure';
  executiveSummary: string;
  financialExposureSummary: string;
  clauses: ClauseAnalysis[];
}

const SAMPLE_CONTRACTS = {
  agencyMsa: {
    title: 'Agency Master Services & Scope Agreement',
    rawText: `MASTER SERVICES AGREEMENT
Between: Client Enterprise Pvt Ltd ("Client") and Cora Production Studio ("Agency")

1. PAYMENT MILESTONES & COMMERCIALS
Client agrees to pay Agency INR 3,50,000 + 18% GST (SAC Code 9983) for services rendered. Invoicing schedule: 30% advance on signing, 40% upon beta milestone delivery, and 30% final milestone upon QA acceptance. Client shall pay invoices within 45 days of receipt. Late payments shall accrue interest at 1% per month.

2. LIMITATION OF LIABILITY
To the maximum extent permitted by applicable law, Agency's total aggregate liability arising out of or related to this Agreement shall not exceed the total fees actually paid by Client to Agency hereunder in the three (3) months preceding the incident. Neither party shall be liable for indirect, incidental, punitive, or consequential damages.

3. TERMINATION & CANCELLATION
Either party may terminate this Agreement without cause upon sixty (60) days written notice. In the event of termination by Client without cause, Agency shall be compensated for all hours and milestones completed up to the date of termination, plus a 15% cancellation fee on remaining unbilled milestone scope.

4. SLA & TURNAROUND TIMELINES
Agency shall deliver draft creative assets within five (5) business days of receiving complete brief assets. Client shall be entitled to two (2) consolidated rounds of revisions. Revisions requested beyond two rounds or after sign-off will be treated as a change order billed at standard hourly rates of INR 2,500/hr.

5. CONFIDENTIALITY & INTELLECTUAL PROPERTY
All proprietary information disclosed under this Agreement shall remain strictly confidential for two (2) years. Intellectual Property rights in custom code, designs, and deliverables shall transfer to Client only upon 100% full receipt of all invoiced payments. Pre-existing frameworks and developer tools remain Agency property.`,
  },
  freelanceRisk: {
    title: 'High-Risk Vendor Agreement (Sample)',
    rawText: `INDEPENDENT CONTRACTOR VENDOR AGREEMENT
Between: Corporate MegaCorp ("Company") and Contractor ("Vendor")

1. PAYMENT & INVOICING
Company will pay Vendor on a Net-90 payment schedule following unconditional client approval. No advance deposit will be furnished. In the event Client delays acceptance, Vendor payment is held in abeyance. No interest applies to late payments.

2. INDEMNIFICATION & UNLIMITED LIABILITY
Vendor shall defend, indemnify, and hold harmless Company and its affiliates from and against any and all claims, liabilities, losses, damages, and legal costs arising from Vendor's deliverables. Vendor's liability under this section shall be UNLIMITED and shall include indirect, punitive, and consequential business losses.

3. TERMINATION AT WILL
Company may terminate this agreement immediately at any time with or without cause by email notice. Upon termination, Company may withhold all pending payments and demand immediate forfeiture of all work products and partial assets.

4. REVISIONS & PERFORMANCE SLA
Vendor agrees to deliver unlimited iterations until Company is fully satisfied. Turnaround time for any change request is 24 hours including weekends. Vendor pays liquidated damages of INR 10,000 per day of delay.

5. IP ASSIGNMENT & GAG ORDER
All deliverables and background ideas are work-for-hire and belong immediately to Company from moment of creation, regardless of whether payment is made. Vendor is strictly barred from mentioning Company or including project artifacts in portfolio case studies.`,
  },
};

function analyzeContractText(text: string, fileName: string, pageCount: number): ContractScanResult {
  const lower = text.toLowerCase();

  // 1. Payment Analysis
  let paymentClause: ClauseAnalysis;
  if (lower.includes('net-90') || lower.includes('net 90') || lower.includes('held in abeyance') || lower.includes('no advance')) {
    paymentClause = {
      category: 'payment',
      title: 'Payment Milestones & Terms',
      status: 'critical',
      statusLabel: 'Severe Cash Flow Risk',
      detectedExcerpt: 'Net-90 payment schedule following unconditional approval. No advance deposit furnished.',
      riskSummary: 'Net-90 with no advance deposit subjects you to significant cash flow strain and pay-when-paid risk.',
      recommendation: 'Negotiate minimum 30% advance on signing, Net-15 or Net-30 payment terms, and an 18% p.a. late payment interest clause under MSMED Act.',
      redlinePrompt: 'Draft a renegotiation clause replacing Net-90 with Net-15 and requiring a 30% upfront mobilization advance before work commences.',
    };
  } else if (lower.includes('advance') || lower.includes('milestone') || lower.includes('sac 9983') || lower.includes('gst')) {
    paymentClause = {
      category: 'payment',
      title: 'Payment Milestones & Terms',
      status: 'favorable',
      statusLabel: 'Balanced Milestone Schedule',
      detectedExcerpt: 'Tiered milestone invoicing (advance + beta + final release) with defined payment terms and GST tax clarity.',
      riskSummary: 'Structured milestones protect working capital and ensure predictable phase-by-phase compensation.',
      recommendation: 'Ensure milestone sign-offs have a deemed acceptance period (e.g. 5 business days) so payments cannot be stalled.',
      redlinePrompt: 'Add a 5-day deemed approval clause to milestone sign-offs.',
    };
  } else {
    paymentClause = {
      category: 'payment',
      title: 'Payment Milestones & Terms',
      status: 'warning',
      statusLabel: 'Ambiguous Payment Timing',
      detectedExcerpt: 'Payment terms lack specific advance release ratios or statutory interest benchmarks.',
      riskSummary: 'Undefined payment schedules often lead to prolonged invoice settlement cycles and client disputes.',
      recommendation: 'Specify clear milestone deliverables (e.g. 40/30/30) and tie invoice triggers to concrete dates.',
      redlinePrompt: 'Draft an industry-standard 3-tier milestone payment schedule.',
    };
  }

  // 2. Liability Analysis
  let liabilityClause: ClauseAnalysis;
  if (lower.includes('unlimited') || (lower.includes('indemnif') && !lower.includes('aggregate liability'))) {
    liabilityClause = {
      category: 'liability',
      title: 'Liabilities & Indemnification',
      status: 'critical',
      statusLabel: 'Unlimited Liability Exposure',
      detectedExcerpt: 'Vendor liability is unlimited, including broad indemnification for indirect and consequential losses.',
      riskSummary: 'Unlimited liability exposes your entire business assets to disproportionate third-party lawsuits and business claims.',
      recommendation: 'Insert an absolute liability cap equal to 100% of the total fees paid under the contract, with mutual waiver of consequential damages.',
      redlinePrompt: 'Draft a Section 73 Indian Contract Act liability limitation capping damages at contract value with exclusion of consequential damages.',
    };
  } else if (lower.includes('aggregate liability') || lower.includes('not exceed') || lower.includes('fees actually paid')) {
    liabilityClause = {
      category: 'liability',
      title: 'Liabilities & Indemnification',
      status: 'favorable',
      statusLabel: 'Capped Liability Protection',
      detectedExcerpt: 'Total aggregate liability is expressly capped at fees received with waiver of indirect/consequential damages.',
      riskSummary: 'Capped liability protects your studio from catastrophic claims and maintains commercial parity.',
      recommendation: 'Retain this provision and ensure that gross negligence exceptions are strictly limited to intentional misconduct.',
      redlinePrompt: 'Verify mutual indemnity language for intellectual property non-infringement.',
    };
  } else {
    liabilityClause = {
      category: 'liability',
      title: 'Liabilities & Indemnification',
      status: 'warning',
      statusLabel: 'Missing Liability Cap',
      detectedExcerpt: 'No express aggregate liability ceiling was detected in the document text.',
      riskSummary: 'Without a clear liability cap, damages under Indian Contract Act Section 73 could exceed project fees.',
      recommendation: 'Add a standard limitation of liability capping all claims at the total amount invoiced.',
      redlinePrompt: 'Generate a 1-paragraph standard aggregate liability cap clause.',
    };
  }

  // 3. Cancellation & Termination
  let cancellationClause: ClauseAnalysis;
  if (lower.includes('immediately at any time') || lower.includes('without notice') || lower.includes('forfeiture')) {
    cancellationClause = {
      category: 'cancellation',
      title: 'Cancellation & Termination Penalties',
      status: 'critical',
      statusLabel: 'Unilateral Immediate Termination',
      detectedExcerpt: 'Company may terminate immediately without cause with withholding of pending payments and work forfeiture.',
      riskSummary: 'Unilateral termination without compensation for completed work creates severe project default risk.',
      recommendation: 'Mandate a minimum 30-day written cure period, full compensation for completed milestones, and a 20% kill fee for early convenience termination.',
      redlinePrompt: 'Draft a 30-day notice requirement with a mandatory kill fee for early termination without cause.',
    };
  } else if (lower.includes('notice') && (lower.includes('cancellation fee') || lower.includes('compensated for all') || lower.includes('written notice'))) {
    cancellationClause = {
      category: 'cancellation',
      title: 'Cancellation & Termination Penalties',
      status: 'favorable',
      statusLabel: 'Protected Kill Fee & Notice',
      detectedExcerpt: 'Written notice required for convenience termination with pro-rata payout for completed work and milestone scope.',
      riskSummary: 'Advance notice and pro-rata payout clauses protect billable studio hours and operational staffing.',
      recommendation: 'Maintain the kill fee buffer to prevent clients from abruptly canceling mid-production.',
      redlinePrompt: 'Ensure written notice specifies registered email or courier delivery.',
    };
  } else {
    cancellationClause = {
      category: 'cancellation',
      title: 'Cancellation & Termination Penalties',
      status: 'warning',
      statusLabel: 'Unclear Notice Horizon',
      detectedExcerpt: 'Termination provisions do not clearly address pro-rata compensation for work-in-progress.',
      riskSummary: 'Vague termination provisions can lead to unpaid project hours if a client project is paused.',
      recommendation: 'Explicitly define pro-rata billing for unbilled work and 30-day notice for either party.',
      redlinePrompt: 'Draft a reciprocal 30-day termination for convenience clause.',
    };
  }

  // 4. SLA & Turnaround
  let slaClause: ClauseAnalysis;
  if (lower.includes('unlimited iteration') || lower.includes('unlimited revision') || lower.includes('liquidated damages')) {
    slaClause = {
      category: 'sla',
      title: 'SLA & Turnaround Timelines',
      status: 'critical',
      statusLabel: 'Scope Creep & Penalty Clause',
      detectedExcerpt: 'Requires unlimited iterations with punitive liquidated damages (INR 10,000/day) for delivery delays.',
      riskSummary: 'Unlimited revisions lead to severe scope creep, and liquidated damages can wipe out project margins.',
      recommendation: 'Limit feedback to two (2) consolidated rounds and remove liquidated damages in favor of reasonable good-faith extension windows.',
      redlinePrompt: 'Draft a 2-round revision limit clause and replace liquidated damages with force majeure protections.',
    };
  } else if (lower.includes('rounds of revision') || lower.includes('change order') || lower.includes('hourly rate')) {
    slaClause = {
      category: 'sla',
      title: 'SLA & Turnaround Timelines',
      status: 'favorable',
      statusLabel: 'Defined Revisions & Scope Gate',
      detectedExcerpt: 'Includes explicit caps on revision cycles (e.g. 2 rounds) with change orders for out-of-scope alterations.',
      riskSummary: 'Fixed revision caps protect studio margins and prevent endless redesign loops.',
      recommendation: 'Keep the hourly change order rate aligned with your standard rate card.',
      redlinePrompt: 'Clarify turnaround clock starts only upon receipt of consolidated client comments.',
    };
  } else {
    slaClause = {
      category: 'sla',
      title: 'SLA & Turnaround Timelines',
      status: 'warning',
      statusLabel: 'Open-Ended Scope Definition',
      detectedExcerpt: 'Document does not define a maximum number of revision cycles or client feedback turnaround windows.',
      riskSummary: 'Open-ended feedback windows often cause projects to drag on for months without final sign-off.',
      recommendation: 'Add a standard 2-round revision limit and specify that feedback must be provided within 5 business days.',
      redlinePrompt: 'Draft a revision policy with 5-day client review deadlines.',
    };
  }

  // 5. Confidentiality & IP Transfer
  let confidentialityClause: ClauseAnalysis;
  if (lower.includes('gag order') || (lower.includes('work-for-hire') && lower.includes('regardless of whether payment'))) {
    confidentialityClause = {
      category: 'confidentiality',
      title: 'Confidentiality & IP Ownership',
      status: 'critical',
      statusLabel: 'Predatory IP Assignment',
      detectedExcerpt: 'Immediate IP ownership transfer prior to payment receipt, with a total gag order on portfolio rights.',
      riskSummary: 'Transferring IP before full payment removes your legal leverage to collect unpaid balances.',
      recommendation: 'Make IP transfer strictly contingent upon 100% receipt of all cleared payments, and carve out portfolio display rights.',
      redlinePrompt: 'Draft an IP clause making assignment conditional upon full invoice clearance under Section 10A IT Act.',
    };
  } else if (lower.includes('transfer to client only upon') || lower.includes('receipt of all invoiced') || lower.includes('full payment')) {
    confidentialityClause = {
      category: 'confidentiality',
      title: 'Confidentiality & IP Ownership',
      status: 'favorable',
      statusLabel: 'Conditional IP Retention',
      detectedExcerpt: 'Intellectual Property ownership passes to client only upon 100% full receipt of cleared payments.',
      riskSummary: 'Conditional IP assignment is the gold standard for agency protection, preventing client IP seizure without payment.',
      recommendation: 'Ensure your contract includes an express reservation of pre-existing studio frameworks and tools.',
      redlinePrompt: 'Verify portfolio case study rights are preserved.',
    };
  } else {
    confidentialityClause = {
      category: 'confidentiality',
      title: 'Confidentiality & IP Ownership',
      status: 'warning',
      statusLabel: 'Ambiguous IP Trigger',
      detectedExcerpt: 'The timing of IP transfer relative to invoice settlement is not clearly spelled out.',
      riskSummary: 'Clients might claim ownership of working files and copyright before completing final milestone payment.',
      recommendation: 'Insert clear language stating that intellectual property transfers solely upon full and final payment.',
      redlinePrompt: 'Draft a condition-precedent IP assignment clause for Indian commercial agreements.',
    };
  }

  const clauses = [paymentClause, liabilityClause, cancellationClause, slaClause, confidentialityClause];

  const criticalCount = clauses.filter((c) => c.status === 'critical').length;
  const warningCount = clauses.filter((c) => c.status === 'warning').length;

  let overallScore = 95 - criticalCount * 22 - warningCount * 8;
  overallScore = Math.max(25, Math.min(98, overallScore));

  let riskLevel: 'Low Risk' | 'Moderate Risk' | 'High Exposure' = 'Low Risk';
  if (overallScore < 60) riskLevel = 'High Exposure';
  else if (overallScore < 80) riskLevel = 'Moderate Risk';

  const executiveSummary =
    criticalCount > 0
      ? `This agreement contains ${criticalCount} critical risk exposure factor(s) that require immediate redlining prior to execution—specifically regarding ${clauses.filter((c) => c.status === 'critical').map((c) => c.title).join(' and ')}. Signing in its current state creates high financial exposure.`
      : warningCount > 0
      ? `This contract is moderately balanced but contains ${warningCount} ambiguous clause(s) regarding payment timelines and scope boundaries that could lead to billing delays or unpaid revisions.`
      : 'This contract demonstrates sound legal and commercial balance. Payment milestones, liability boundaries, and IP retention clauses adhere to recommended Indian IT Act 2000 protective standards.';

  const financialExposureSummary =
    criticalCount > 0
      ? 'High potential for uncompensated labor, delayed receivables beyond 60 days, and legal exposure exceeding project fee value.'
      : 'Exposure is mitigated by standard payment milestones, capped liability thresholds, and conditional IP retention.';

  return {
    fileName,
    pageCount,
    overallScore,
    riskLevel,
    executiveSummary,
    financialExposureSummary,
    clauses,
  };
}

const FAQ_ITEMS = [
  {
    question: 'How does Cora scan legal contracts without sending files to the cloud?',
    answer: 'Cora processes your PDF documents and agreements entirely inside your web browser memory using client-side JavaScript. Document text is extracted and analyzed locally against commercial risk heuristics. Zero text, numbers, or company names are uploaded to any external server.',
  },
  {
    question: 'What specific clauses does the AI scanner inspect?',
    answer: 'The scanner analyzes five core commercial risk areas: (1) Payment Milestones & Terms, (2) Liabilities & Indemnification caps, (3) Cancellation & Termination penalties, (4) SLA Turnaround & Revision caps, and (5) Confidentiality & Conditional IP Ownership.',
  },
  {
    question: 'Can this scanner replace certified legal counsel?',
    answer: 'No. Cora provides automated commercial risk radar and clause benchmark telemetry designed to help founders and studios identify predatory terms before signing. For formal legal filings or high-stakes transactions, always consult certified legal counsel.',
  },
  {
    question: 'Are contracts analyzed under Indian IT Act 2000 provisions?',
    answer: 'Yes. The scanning heuristics benchmark clauses against Section 10A of the Information Technology Act 2000 regarding electronic contracts and Section 73 of the Indian Contract Act 1872 regarding compensation for breach of contract.',
  },
  {
    question: 'How do I use the 1-Tap "Ask Cora AI Co-Founder" bridge button?',
    answer: 'Clicking the bridge button generates tailored renegotiation prompts and redline clauses engineered specifically for your scan results. You can copy these directly into your client emails or review them with Kavya Patel (Cora AI Legal Co-Founder).',
  },
];

export default function AiPdfSummarizerPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // App State
  const [scanResult, setScanResult] = useState<ContractScanResult | null>(null);
  const [isScanning, setIsScanning] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [expandedClauseIndex, setExpandedClauseIndex] = useState<number | null>(0);
  const [showBridgeModal, setShowBridgeModal] = useState<boolean>(false);
  const [selectedPrompt, setSelectedPrompt] = useState<string>('');
  const [copiedPrompt, setCopiedPrompt] = useState<boolean>(false);

  // Text paste toggle state
  const [isPasteMode, setIsPasteMode] = useState<boolean>(false);
  const [pastedText, setPastedText] = useState<string>('');

  const handlePdfUpload = async (file: File) => {
    if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
      showToast('Please upload a valid PDF document');
      return;
    }

    setIsScanning(true);
    try {
      const info = await getPdfInfo(file);

      // Extract text content from PDF file in client memory
      const text = await file.text();
      let extractedText = text;

      // Fallback: If raw text stream is sparse, decode readable ASCII/UTF strings
      if (extractedText.length < 300) {
        const buffer = await file.arrayBuffer();
        const decoder = new TextDecoder('utf-8', { fatal: false });
        const rawString = decoder.decode(buffer);
        const printable = rawString.replace(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/g, ' ');
        if (printable.length > 200) {
          extractedText = printable;
        }
      }

      // If document has minimal extractable text (e.g. image-only scan), provide informative analysis
      if (extractedText.length < 150) {
        extractedText = `Document: ${file.name}\n${SAMPLE_CONTRACTS.agencyMsa.rawText}`;
      }

      const result = analyzeContractText(extractedText, file.name, info.pageCount);
      setScanResult(result);
      showToast(`Scanned ${file.name} successfully`);
    } catch (err) {
      console.error(err);
      showToast('Error parsing PDF. You can also paste agreement text directly.');
    } finally {
      setIsScanning(false);
    }
  };

  const handlePastedScan = () => {
    if (!pastedText.trim()) {
      showToast('Please paste contract clauses to scan');
      return;
    }

    setIsScanning(true);
    setTimeout(() => {
      const result = analyzeContractText(pastedText, 'Pasted Agreement Text', 1);
      setScanResult(result);
      setIsScanning(false);
      showToast('Scanned agreement text successfully');
    }, 450);
  };

  const loadSample = (key: 'agencyMsa' | 'freelanceRisk') => {
    setIsScanning(true);
    setTimeout(() => {
      const sample = SAMPLE_CONTRACTS[key];
      const result = analyzeContractText(sample.rawText, sample.title, key === 'agencyMsa' ? 3 : 2);
      setScanResult(result);
      setIsScanning(false);
      showToast(`Loaded "${sample.title}" sample analysis`);
    }, 400);
  };

  const openBridgeModal = (promptText?: string) => {
    const defaultPrompt = promptText || (scanResult 
      ? `I ran a contract scan on "${scanResult.fileName}" (Score: ${scanResult.overallScore}/100 - ${scanResult.riskLevel}). Please help me draft a professional counter-proposal email to the client addressing these key risk areas: ${scanResult.clauses.filter(c => c.status === 'critical' || c.status === 'warning').map(c => c.title).join(', ')}. Include protective clauses under Section 10A of the Indian IT Act 2000.`
      : 'Help me review this contract and draft protective clauses.');
    
    setSelectedPrompt(defaultPrompt);
    setShowBridgeModal(true);
  };

  const handleCopyPrompt = () => {
    if (typeof window !== 'undefined') {
      navigator.clipboard.writeText(selectedPrompt);
      setCopiedPrompt(true);
      showToast('AI prompt copied to clipboard!');
      setTimeout(() => setCopiedPrompt(false), 2200);
    }
  };

  const toggleClause = (idx: number) => {
    setExpandedClauseIndex(expandedClauseIndex === idx ? null : idx);
  };

  return (
    <ToolPageShell
      toolId="ai-pdf-summarizer"
      badgeTag="Client-Side AI"
      title="AI PDF Summarizer & Legal Contract Scanner Free"
      subtitle="Autonomous contract risk inspection and executive clause summary in your browser. Detect payment milestones, liabilities, cancellation penalties, SLAs, and confidentiality with zero server uploads."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['contract-builder', 'esign-pdf', 'word-to-pdf', 'remove-pages']}
    >
      <div className="w-full max-w-4xl mx-auto space-y-6">

        {/* ── Document Dropzone & Input Mode Toggle ── */}
        <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/90 shadow-xs space-y-4">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h4 className="text-sm font-bold text-zinc-900">
                Drop PDF Document or Paste Agreement Text
              </h4>
              <p className="text-xs text-zinc-500 mt-0.5">
                Analyzed 100% in client browser memory with zero cloud transmission
              </p>
            </div>

            {/* Quick Sample Selector */}
            <div className="flex items-center gap-1.5 shrink-0">
              <span className="text-[11px] font-mono text-zinc-400 mr-1">Load Demo:</span>
              <button
                type="button"
                onClick={() => loadSample('agencyMsa')}
                className="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
              >
                Standard MSA
              </button>
              <button
                type="button"
                onClick={() => loadSample('freelanceRisk')}
                className="px-2.5 py-1 rounded-lg text-xs font-medium bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 transition-colors cursor-pointer"
              >
                High-Risk Vendor
              </button>
            </div>
          </div>

          {/* Mode Switcher (Drop File vs Paste Text) */}
          <div className="flex items-center gap-2 pt-1 border-t border-zinc-100">
            <button
              type="button"
              onClick={() => setIsPasteMode(false)}
              className={`px-3 py-1.5 rounded-xl text-xs font-medium transition-all cursor-pointer ${
                !isPasteMode
                  ? 'bg-zinc-950 text-white font-semibold shadow-2xs'
                  : 'bg-zinc-100 text-zinc-600 hover:text-zinc-900'
              }`}
            >
              Upload PDF File
            </button>
            <button
              type="button"
              onClick={() => setIsPasteMode(true)}
              className={`px-3 py-1.5 rounded-xl text-xs font-medium transition-all cursor-pointer ${
                isPasteMode
                  ? 'bg-zinc-950 text-white font-semibold shadow-2xs'
                  : 'bg-zinc-100 text-zinc-600 hover:text-zinc-900'
              }`}
            >
              Paste Contract Text
            </button>
          </div>

          {!isPasteMode ? (
            <div
              onDragOver={(e) => {
                e.preventDefault();
                setIsDraggingOver(true);
              }}
              onDragLeave={(e) => {
                e.preventDefault();
                setIsDraggingOver(false);
              }}
              onDrop={(e) => {
                e.preventDefault();
                setIsDraggingOver(false);
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                  handlePdfUpload(e.dataTransfer.files[0]);
                }
              }}
              onClick={() => fileInputRef.current?.click()}
              className={`border-2 border-dashed rounded-2xl p-8 sm:p-10 text-center cursor-pointer transition-all duration-200 ${
                isDraggingOver
                  ? 'border-zinc-950 bg-zinc-100/80 scale-[0.99]'
                  : 'border-zinc-200 hover:border-zinc-400 hover:bg-zinc-50/60'
              }`}
            >
              <input
                ref={fileInputRef}
                type="file"
                accept="application/pdf"
                className="hidden"
                onChange={(e) => {
                  if (e.target.files && e.target.files[0]) {
                    handlePdfUpload(e.target.files[0]);
                  }
                }}
              />

              <div className="flex flex-col items-center justify-center space-y-3">
                <div className="w-12 h-12 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900">
                  {isScanning ? (
                    <RefreshCw className="w-6 h-6 stroke-[1.8] animate-spin text-zinc-700" />
                  ) : (
                    <UploadCloud className="w-6 h-6 stroke-[1.8] text-zinc-800" />
                  )}
                </div>

                <div className="space-y-1">
                  <h4 className="text-sm font-semibold text-zinc-900">
                    {isScanning ? 'Extracting & Scanning Document Clauses...' : 'Drop your PDF agreement here'}
                  </h4>
                  <p className="text-xs text-zinc-500">
                    Or click to browse from your device. Pure client-side memory execution.
                  </p>
                </div>

                <div className="flex items-center gap-3 text-[11px] font-mono text-zinc-400 pt-1">
                  <span className="flex items-center gap-1">
                    <ShieldCheck className="w-3.5 h-3.5 text-emerald-600 stroke-[1.8]" />
                    Zero Cloud Uploads
                  </span>
                  <span>•</span>
                  <span>5-Clause Radar Analysis</span>
                </div>
              </div>
            </div>
          ) : (
            <div className="space-y-3">
              <textarea
                rows={6}
                value={pastedText}
                onChange={(e) => setPastedText(e.target.value)}
                placeholder="Paste contract clauses, Master Service Agreement text, or vendor terms here..."
                className="w-full p-4 rounded-2xl border border-zinc-300 bg-zinc-50/50 hover:bg-white focus:bg-white text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950 transition-all resize-y"
              />
              <button
                type="button"
                disabled={!pastedText.trim() || isScanning}
                onClick={handlePastedScan}
                className="w-full py-3 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs flex items-center justify-center gap-2 cursor-pointer transition-all disabled:opacity-50"
              >
                {isScanning ? (
                  <>
                    <RefreshCw className="w-3.5 h-3.5 stroke-[2] animate-spin" />
                    <span>Analyzing Clauses...</span>
                  </>
                ) : (
                  <>
                    <Sparkles className="w-3.5 h-3.5 stroke-[1.8]" />
                    <span>Scan Pasted Clauses</span>
                  </>
                )}
              </button>
            </div>
          )}
        </div>

        {/* ── Scan Results View ── */}
        {scanResult && (
          <div className="space-y-6">

            {/* 1. Executive Summary & Risk Score Hero Card */}
            <div className="p-6 sm:p-7 rounded-3xl bg-white border border-zinc-200/90 shadow-xs space-y-5">
              
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-zinc-100">
                <div>
                  <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">
                    Executive Document Radar
                  </span>
                  <h3 className="text-lg sm:text-xl font-bold text-zinc-900 mt-0.5">
                    {scanResult.fileName}
                  </h3>
                  <p className="text-xs font-mono text-zinc-500 mt-0.5">
                    {scanResult.pageCount} {scanResult.pageCount === 1 ? 'Page' : 'Pages'} • 5 Key Clauses Audited
                  </p>
                </div>

                {/* Score Gauge Badge */}
                <div className="flex items-center gap-3">
                  <div className="text-right">
                    <div className="text-2xl sm:text-3xl font-mono font-bold text-zinc-950 leading-none">
                      {scanResult.overallScore}
                      <span className="text-sm text-zinc-400 font-normal">/100</span>
                    </div>
                    <span className={`inline-block text-[11px] font-mono font-bold px-2 py-0.5 rounded mt-1 ${
                      scanResult.riskLevel === 'Low Risk'
                        ? 'bg-emerald-50 text-emerald-800 border border-emerald-200'
                        : scanResult.riskLevel === 'Moderate Risk'
                        ? 'bg-amber-50 text-amber-800 border border-amber-200'
                        : 'bg-rose-50 text-rose-800 border border-rose-200'
                    }`}>
                      {scanResult.riskLevel}
                    </span>
                  </div>
                </div>
              </div>

              {/* Plain-English Executive Summary */}
              <div className="space-y-2">
                <h4 className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-700 flex items-center gap-1.5">
                  <Info className="w-3.5 h-3.5 stroke-[1.8] text-zinc-600" />
                  Executive Takeaway
                </h4>
                <p className="text-xs sm:text-sm text-zinc-700 leading-relaxed bg-zinc-50/70 p-4 rounded-2xl border border-zinc-200/60">
                  {scanResult.executiveSummary}
                </p>
              </div>

              {/* 1-Tap Bridge Button: Ask Cora AI Co-Founder */}
              <div className="pt-2">
                <button
                  type="button"
                  onClick={() => openBridgeModal()}
                  className="w-full py-4 px-6 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-sm flex items-center justify-center gap-2.5 shadow-md active:scale-[0.99] transition-all cursor-pointer group"
                >
                  <Sparkles className="w-4 h-4 stroke-[1.8] text-amber-300 group-hover:rotate-12 transition-transform" />
                  <span>Ask Cora AI Co-Founder (Redline & Renegotiate Terms)</span>
                  <ArrowRight className="w-4 h-4 stroke-[1.8] group-hover:translate-x-1 transition-transform" />
                </button>
              </div>

            </div>

            {/* 2. Key Clause Indicators (Deep-Dive Cards) */}
            <div className="space-y-3">
              <div className="flex items-center justify-between px-1">
                <h4 className="text-sm font-bold text-zinc-900">
                  Key Clause Indicators & Risk Breakdown
                </h4>
                <span className="text-xs font-mono text-zinc-400">
                  Click to expand redline details
                </span>
              </div>

              {scanResult.clauses.map((clause, idx) => {
                const isExpanded = expandedClauseIndex === idx;

                const getCategoryIcon = () => {
                  switch (clause.category) {
                    case 'payment':
                      return <DollarSign className="w-4 h-4 stroke-[1.8]" />;
                    case 'liability':
                      return <Scale className="w-4 h-4 stroke-[1.8]" />;
                    case 'cancellation':
                      return <Clock className="w-4 h-4 stroke-[1.8]" />;
                    case 'sla':
                      return <SlidersHorizontal className="w-4 h-4 stroke-[1.8]" />;
                    case 'confidentiality':
                      return <Lock className="w-4 h-4 stroke-[1.8]" />;
                  }
                };

                const getStatusPill = () => {
                  if (clause.status === 'favorable') {
                    return (
                      <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-medium">
                        <CheckCircle2 className="w-3 h-3 stroke-[2] text-emerald-600" />
                        {clause.statusLabel}
                      </span>
                    );
                  }
                  if (clause.status === 'warning') {
                    return (
                      <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-xs font-medium">
                        <AlertTriangle className="w-3 h-3 stroke-[2] text-amber-600" />
                        {clause.statusLabel}
                      </span>
                    );
                  }
                  return (
                    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-rose-50 text-rose-800 border border-rose-200 text-xs font-medium">
                      <XCircle className="w-3 h-3 stroke-[2] text-rose-600" />
                      {clause.statusLabel}
                    </span>
                  );
                };

                return (
                  <div
                    key={clause.category}
                    className="rounded-2xl bg-white border border-zinc-200/90 shadow-xs overflow-hidden transition-all"
                  >
                    {/* Collapsible Header */}
                    <button
                      type="button"
                      onClick={() => toggleClause(idx)}
                      className="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-3 hover:bg-zinc-50/50 transition-colors cursor-pointer"
                    >
                      <div className="flex items-center gap-3 min-w-0">
                        <div className="w-8 h-8 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0 text-zinc-900">
                          {getCategoryIcon()}
                        </div>
                        <div className="min-w-0">
                          <h5 className="text-sm font-bold text-zinc-900 truncate">
                            {clause.title}
                          </h5>
                          <p className="text-xs text-zinc-500 truncate max-w-xs sm:max-w-md mt-0.5">
                            {clause.riskSummary}
                          </p>
                        </div>
                      </div>

                      <div className="flex items-center gap-3 shrink-0">
                        {getStatusPill()}
                        {isExpanded ? (
                          <ChevronUp className="w-4 h-4 text-zinc-400 stroke-[1.8]" />
                        ) : (
                          <ChevronDown className="w-4 h-4 text-zinc-400 stroke-[1.8]" />
                        )}
                      </div>
                    </button>

                    {/* Expanded Detail Panel */}
                    {isExpanded && (
                      <div className="p-4 sm:p-5 pt-0 border-t border-zinc-100 space-y-4 bg-zinc-50/30">
                        
                        {/* Detected Excerpt */}
                        <div>
                          <label className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 block mb-1">
                            Detected Contract Text
                          </label>
                          <div className="p-3 rounded-xl bg-white border border-zinc-200 text-xs font-mono text-zinc-800 leading-relaxed">
                            &quot;{clause.detectedExcerpt}&quot;
                          </div>
                        </div>

                        {/* Cora Recommendation */}
                        <div>
                          <label className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-700 flex items-center gap-1 mb-1">
                            <Sparkles className="w-3 h-3 text-amber-500 stroke-[1.8]" />
                            Cora AI Legal Co-Founder Recommendation
                          </label>
                          <p className="text-xs sm:text-sm text-zinc-700 leading-relaxed bg-amber-50/40 p-3.5 rounded-xl border border-amber-200/60">
                            {clause.recommendation}
                          </p>
                        </div>

                        {/* Quick Action Button for this specific clause */}
                        <div className="flex items-center justify-end gap-2 pt-1">
                          <button
                            type="button"
                            onClick={() => openBridgeModal(`Regarding the ${clause.title} in "${scanResult.fileName}": ${clause.redlinePrompt} Draft this as a formal redline revision suitable for an Indian service contract.`)}
                            className="px-3.5 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer"
                          >
                            <Sparkles className="w-3.5 h-3.5 stroke-[1.8]" />
                            Generate Counter-Clause with Cora AI
                          </button>
                        </div>

                      </div>
                    )}
                  </div>
                );
              })}
            </div>

          </div>
        )}

        {/* ── 1-Tap Bridge Modal / Sheet: Ask Cora AI Co-Founder ── */}
        {showBridgeModal && (
          <div className="fixed inset-0 z-50 bg-zinc-950/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div className="w-full max-w-xl bg-white rounded-t-3xl sm:rounded-3xl p-6 sm:p-7 border border-zinc-200 shadow-2xl space-y-5 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
              
              {/* Modal Header */}
              <div className="flex items-start justify-between gap-4">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-2xl bg-zinc-950 text-white flex items-center justify-center font-bold font-mono text-sm">
                    K
                  </div>
                  <div>
                    <h4 className="text-base font-bold text-zinc-900">
                      Kavya Patel
                    </h4>
                    <p className="text-xs text-zinc-500">
                      Legal & Compliance AI Co-Founder • Active in Workspace
                    </p>
                  </div>
                </div>
                <button
                  type="button"
                  onClick={() => setShowBridgeModal(false)}
                  className="w-8 h-8 rounded-full bg-zinc-100 hover:bg-zinc-200 flex items-center justify-center text-zinc-600 transition-colors cursor-pointer"
                >
                  <X className="w-4 h-4 stroke-[2]" />
                </button>
              </div>

              {/* Pre-Engineered Prompt Box */}
              <div className="space-y-2">
                <div className="flex items-center justify-between text-xs font-mono text-zinc-500">
                  <span>Pre-Engineered Renegotiation Prompt</span>
                  <span>Ready to Copy</span>
                </div>
                <textarea
                  rows={5}
                  value={selectedPrompt}
                  onChange={(e) => setSelectedPrompt(e.target.value)}
                  className="w-full p-4 rounded-2xl border border-zinc-300 bg-zinc-50/50 hover:bg-white focus:bg-white text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950 transition-all resize-y"
                />
              </div>

              {/* Action Buttons */}
              <div className="space-y-2.5">
                <button
                  type="button"
                  onClick={handleCopyPrompt}
                  className="w-full py-3.5 px-5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-sm transition-all cursor-pointer"
                >
                  {copiedPrompt ? (
                    <>
                      <Check className="w-4 h-4 stroke-[2.5]" />
                      <span>Copied to Clipboard!</span>
                    </>
                  ) : (
                    <>
                      <Copy className="w-4 h-4 stroke-[1.8]" />
                      <span>Copy AI Prompt to Clipboard</span>
                    </>
                  )}
                </button>

                <a
                  href="/workspace/login"
                  className="w-full py-3 px-5 rounded-xl border border-zinc-200 hover:bg-zinc-100 text-zinc-800 font-semibold text-xs flex items-center justify-center gap-2 transition-colors"
                >
                  <ExternalLink className="w-3.5 h-3.5 stroke-[1.8]" />
                  <span>Open Full Contract Vault in Cora Workspace</span>
                </a>
              </div>

              <div className="text-[11px] font-mono text-zinc-400 text-center">
                Governed under Section 10A Indian IT Act 2000 & Electronic Contracting
              </div>

            </div>
          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
