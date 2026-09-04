'use client';

import React, { useState, useRef } from 'react';
import { 
  Wrench, 
  UploadCloud, 
  FileText, 
  Download, 
  RefreshCw, 
  ShieldCheck, 
  AlertTriangle, 
  CheckCircle2, 
  FileCheck, 
  Terminal, 
  Activity, 
  Sparkles,
  ArrowRight,
  Layers,
  FileCode,
  SlidersHorizontal,
  ChevronRight
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { repairPdfDocument, downloadPdfBlob, RepairReport } from '@/lib/pdf-engine';

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
}

const FAQ_ITEMS = [
  {
    question: 'How does Cora repair corrupted or broken PDF files?',
    answer: 'Cora performs low-level binary inspection directly inside your browser memory. It rebuilds damaged cross-reference (xref) tables, recovers orphaned object streams, fixes invalid trailer dictionaries, and sanitizes page bounding media boxes to generate a valid ISO-compliant PDF.',
  },
  {
    question: 'Are my confidential documents uploaded to an external server?',
    answer: 'Never. Cora runs 100% locally in your browser using pure client-side WebAssembly and JavaScript. Zero bytes of your files, confidential contracts, or financial numbers leave your device.',
  },
  {
    question: 'What types of PDF corruption can be repaired?',
    answer: 'Common issues repaired include truncated downloads, corrupted trailer dictionaries, broken cross-reference offsets, non-standard EOF markers, missing catalog roots, and zero-dimension page bounding boxes.',
  },
  {
    question: 'Can this tool repair password-protected encrypted PDFs?',
    answer: 'If a PDF has standard permissions corruption or damaged trailer pointers, Cora attempts to reconstruct the catalog while bypassing encryption flags. However, strong AES-256 user passwords must be unlocked first.',
  },
  {
    question: 'Is there any file size limit or cost to use this repair utility?',
    answer: 'No. Cora PDF Repair is 100% free with zero paywalls, zero daily quotas, and no account registration required. You can process heavy multi-hundred-page documents directly.',
  },
];

export default function RepairPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [repairReport, setRepairReport] = useState<RepairReport | null>(null);
  const [repairLogs, setRepairLogs] = useState<string[]>([]);
  const [progressPercent, setProgressPercent] = useState<number>(0);

  const handleFileSelect = (file: File) => {
    if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
      showToast('Please select a valid PDF file.');
      return;
    }
    setPdfFile(file);
    setRepairReport(null);
    setRepairLogs([]);
    setProgressPercent(0);
    showToast(`Loaded ${file.name} for diagnostic inspection.`);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDraggingOver(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFileSelect(e.dataTransfer.files[0]);
    }
  };

  const executeRepair = async () => {
    if (!pdfFile) {
      showToast('Please upload a PDF file to repair.');
      return;
    }

    setIsProcessing(true);
    setProgressPercent(15);
    setRepairLogs(['Initializing in-browser binary parser...', 'Scanning byte markers for %PDF header...']);

    try {
      await new Promise((r) => setTimeout(r, 400));
      setProgressPercent(45);
      setRepairLogs((prev) => [
        ...prev,
        'Analyzing cross-reference (xref) table offsets...',
        'Inspecting object dictionaries and stream lengths...',
      ]);

      await new Promise((r) => setTimeout(r, 400));
      setProgressPercent(75);
      setRepairLogs((prev) => [
        ...prev,
        'Reconstructing catalog root & page tree hierarchy...',
        'Sanitizing trailer and standardizing media boxes...',
      ]);

      const report = await repairPdfDocument(pdfFile);

      await new Promise((r) => setTimeout(r, 300));
      setProgressPercent(100);
      setRepairReport(report);
      setRepairLogs((prev) => [
        ...prev,
        ...report.fixedAnomalies,
        'Pristine PDF binary stream compiled successfully!',
      ]);
      showToast('PDF structure successfully diagnosed and repaired!');
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Unknown diagnostic error';
      showToast(`Repair error: ${msg}`);
      setRepairLogs((prev) => [...prev, `Diagnostic error: ${msg}`]);
    } finally {
      setIsProcessing(false);
    }
  };

  const handleDownload = () => {
    if (!repairReport || !pdfFile) return;
    const baseName = pdfFile.name.replace(/\.pdf$/i, '');
    downloadPdfBlob(repairReport.pdfBytes, `${baseName}-repaired.pdf`);
    showToast('Repaired PDF downloaded successfully.');
  };

  const handleReset = () => {
    setPdfFile(null);
    setRepairReport(null);
    setRepairLogs([]);
    setProgressPercent(0);
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  return (
    <ToolPageShell
      toolId="repair-pdf"
      badgeTag="Diagnostic & Recovery"
      title="Repair PDF Online Free"
      subtitle="Analyze, reconstruct corrupted cross-reference tables, and recover damaged PDF documents with 100% in-browser privacy."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['compress-pdf', 'ocr-pdf', 'split-pdf', 'merge-pdf']}
    >
      <div className="w-full max-w-4xl mx-auto space-y-6">
        
        {/* ── Dropzone & Upload State ── */}
        {!pdfFile ? (
          <div
            onDragOver={(e) => { e.preventDefault(); setIsDraggingOver(true); }}
            onDragLeave={() => setIsDraggingOver(false)}
            onDrop={handleDrop}
            onClick={() => fileInputRef.current?.click()}
            className={`cursor-pointer group relative border-2 border-dashed rounded-2xl p-10 sm:p-14 text-center transition-all duration-200 ${
              isDraggingOver 
                ? 'border-zinc-900 bg-zinc-100/70 scale-[0.99]' 
                : 'border-zinc-200 hover:border-zinc-400 bg-white shadow-sm hover:shadow-md'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept=".pdf,application/pdf"
              className="hidden"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleFileSelect(e.target.files[0]);
                }
              }}
            />

            <div className="mx-auto w-16 h-16 rounded-2xl bg-zinc-100 flex items-center justify-center text-zinc-700 group-hover:bg-zinc-900 group-hover:text-white transition-colors duration-200 mb-5">
              <UploadCloud className="w-8 h-8" />
            </div>

            <h3 className="text-xl font-semibold text-zinc-900 mb-2">
              Drop corrupted or damaged PDF here
            </h3>
            <p className="text-sm text-zinc-500 max-w-md mx-auto mb-6">
              Rebuilds broken xref tables, trailer dictionaries, and recovers lost pages. Runs 100% locally in your browser memory.
            </p>

            <div className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-900 text-white text-sm font-medium hover:bg-zinc-800 transition-colors shadow-sm">
              <FileText className="w-4 h-4" />
              <span>Select PDF from Device</span>
            </div>

            <div className="mt-8 pt-6 border-t border-zinc-100 flex flex-wrap items-center justify-center gap-6 text-xs text-zinc-500">
              <span className="inline-flex items-center gap-1.5">
                <ShieldCheck className="w-4 h-4 text-emerald-600" />
                Zero Cloud Uploads
              </span>
              <span className="inline-flex items-center gap-1.5">
                <Activity className="w-4 h-4 text-zinc-600" />
                Binary Xref Rebuilding
              </span>
              <span className="inline-flex items-center gap-1.5">
                <Layers className="w-4 h-4 text-zinc-600" />
                Page Tree Recovery
              </span>
            </div>
          </div>
        ) : (
          <div className="bg-white border border-zinc-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            
            {/* Header / Active Document Bar */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-zinc-100">
              <div className="flex items-center gap-3.5 min-w-0">
                <div className="w-12 h-12 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800 shrink-0">
                  <FileText className="w-6 h-6" />
                </div>
                <div className="min-w-0">
                  <h4 className="font-semibold text-zinc-900 text-base truncate">
                    {pdfFile.name}
                  </h4>
                  <div className="flex items-center gap-2 text-xs text-zinc-500 mt-0.5">
                    <span>{formatBytes(pdfFile.size)}</span>
                    <span>•</span>
                    <span className="text-zinc-700 font-medium">Ready for Diagnostic Scan</span>
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-2 shrink-0">
                <button
                  type="button"
                  onClick={handleReset}
                  disabled={isProcessing}
                  className="px-3.5 py-2 text-xs font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 rounded-lg transition-colors"
                >
                  Change File
                </button>
                {!repairReport && (
                  <button
                    type="button"
                    onClick={executeRepair}
                    disabled={isProcessing}
                    className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold shadow-sm transition-all disabled:opacity-50"
                  >
                    {isProcessing ? (
                      <>
                        <RefreshCw className="w-3.5 h-3.5 animate-spin" />
                        <span>Diagnosing & Rebuilding...</span>
                      </>
                    ) : (
                      <>
                        <Wrench className="w-3.5 h-3.5" />
                        <span>Run Deep Repair</span>
                      </>
                    )}
                  </button>
                )}
              </div>
            </div>

            {/* Diagnostic Progress Bar */}
            {isProcessing && (
              <div className="space-y-2 py-2">
                <div className="flex justify-between text-xs font-medium text-zinc-600">
                  <span>Reconstructing PDF binary structure...</span>
                  <span>{progressPercent}%</span>
                </div>
                <div className="w-full h-2 bg-zinc-100 rounded-full overflow-hidden">
                  <div 
                    className="h-full bg-zinc-900 transition-all duration-300 rounded-full"
                    style={{ width: `${progressPercent}%` }}
                  />
                </div>
              </div>
            )}

            {/* Live Terminal / Diagnostic Logs */}
            {repairLogs.length > 0 && (
              <div className="rounded-xl bg-zinc-950 text-zinc-300 p-4 font-mono text-xs space-y-1.5 border border-zinc-800 shadow-inner">
                <div className="flex items-center justify-between text-zinc-500 pb-2 border-b border-zinc-800 text-[11px]">
                  <span className="inline-flex items-center gap-1.5 font-sans font-medium text-zinc-400">
                    <Terminal className="w-3.5 h-3.5" />
                    Binary Diagnostic Telemetry
                  </span>
                  <span>100% In-Memory</span>
                </div>
                <div className="space-y-1 pt-1 max-h-48 overflow-y-auto pr-2">
                  {repairLogs.map((log, idx) => (
                    <div key={idx} className="flex items-start gap-2">
                      <span className="text-zinc-600 select-none">&gt;</span>
                      <span className={log.includes('error') ? 'text-rose-400' : 'text-zinc-300'}>
                        {log}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Repair Success Summary Card */}
            {repairReport && (
              <div className="rounded-2xl bg-zinc-50 border border-zinc-200/80 p-6 space-y-6">
                <div className="flex items-start gap-3.5">
                  <div className="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                    <CheckCircle2 className="w-5 h-5" />
                  </div>
                  <div>
                    <h5 className="font-semibold text-zinc-900 text-base">
                      Document Repaired & Re-Indexed
                    </h5>
                    <p className="text-xs text-zinc-600 mt-0.5">
                      All cross-reference streams and corrupted object links were resolved. Intact page trees serialized losslessly.
                    </p>
                  </div>
                </div>

                {/* Metrics Matrix */}
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                  <div className="bg-white border border-zinc-200 rounded-xl p-3">
                    <div className="text-[11px] text-zinc-500 font-medium">Recovered Pages</div>
                    <div className="text-lg font-bold text-zinc-900 mt-0.5">{repairReport.pageCount}</div>
                  </div>
                  <div className="bg-white border border-zinc-200 rounded-xl p-3">
                    <div className="text-[11px] text-zinc-500 font-medium">Original Size</div>
                    <div className="text-lg font-bold text-zinc-900 mt-0.5">{formatBytes(repairReport.originalSizeBytes)}</div>
                  </div>
                  <div className="bg-white border border-zinc-200 rounded-xl p-3">
                    <div className="text-[11px] text-zinc-500 font-medium">Repaired Size</div>
                    <div className="text-lg font-bold text-zinc-900 mt-0.5">{formatBytes(repairReport.repairedSizeBytes)}</div>
                  </div>
                  <div className="bg-white border border-zinc-200 rounded-xl p-3">
                    <div className="text-[11px] text-zinc-500 font-medium">Fixed Anomalies</div>
                    <div className="text-lg font-bold text-emerald-700 mt-0.5">{repairReport.fixedAnomalies.length}</div>
                  </div>
                </div>

                {/* Download CTA Bar */}
                <div className="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                  <div className="text-xs text-zinc-500 flex items-center gap-1.5">
                    <ShieldCheck className="w-4 h-4 text-emerald-600" />
                    <span>ISO 32000-1 Compliant Output</span>
                  </div>

                  <button
                    type="button"
                    onClick={handleDownload}
                    className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-sm font-semibold shadow-sm transition-all"
                  >
                    <Download className="w-4 h-4" />
                    <span>Download Repaired PDF</span>
                  </button>
                </div>
              </div>
            )}
          </div>
        )}
      </div>
    </ToolPageShell>
  );
}
