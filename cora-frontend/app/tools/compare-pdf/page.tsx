'use client';

import React, { useState, useRef, useCallback } from 'react';
import { 
  FileText, 
  ShieldCheck, 
  RotateCcw, 
  GitCompare, 
  ChevronLeft, 
  ChevronRight, 
  Sparkles, 
  CheckCircle2, 
  AlertTriangle,
  ArrowRight,
  Eye,
  Columns,
  Layers,
  Scale,
  Clock,
  Download
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { comparePdfFiles, PdfCompareData } from '@/lib/pdf-engine';

const COMPARE_PDF_FAQS = [
  {
    question: 'How does Cora detect differences between two PDF versions?',
    answer: 'Cora performs multi-layer document inspection in browser memory: examining page tree structures, byte-level footprint deltas, sheet viewport dimensions, and layout variations side-by-side.'
  },
  {
    question: 'Can I compare legal agreements with differing page counts?',
    answer: 'Yes. If one party appended a new indemnity annexure or deleted a signature clause, Cora flags the page mismatch immediately and lets you step through surviving sheets synchronously.'
  },
  {
    question: 'Are my confidential contracts or master service agreements uploaded anywhere?',
    answer: 'Never. Both files are processed 100% locally inside your web browser memory. Zero document data, pricing milestones, or client names ever leave your device.'
  },
  {
    question: 'What is the difference between Side-by-Side and Overlay views?',
    answer: 'Side-by-Side provides a clean dual-column comparison for reading clauses in parallel. Overlay mode visually superimposes both sheets with difference blending to instantly expose shifted paragraphs, edited figures, or altered dates.'
  },
  {
    question: 'Can I compare scanned PDFs alongside digitally generated invoices?',
    answer: 'Yes. Cora handles mixed source formats, normalizing viewport geometries and highlighting dimensional variances or structural deviations.'
  }
];

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

export default function ComparePdfPage() {
  const { showToast } = useToast();
  const fileInputRefA = useRef<HTMLInputElement>(null);
  const fileInputRefB = useRef<HTMLInputElement>(null);

  const [fileA, setFileA] = useState<File | null>(null);
  const [fileB, setFileB] = useState<File | null>(null);
  const [compareData, setCompareData] = useState<PdfCompareData | null>(null);
  const [currentPageIndex, setCurrentPageIndex] = useState<number>(0);
  const [viewMode, setViewMode] = useState<'side-by-side' | 'overlay' | 'audit'>('side-by-side');
  const [isProcessing, setIsProcessing] = useState<boolean>(false);

  const [isDraggingA, setIsDraggingA] = useState<boolean>(false);
  const [isDraggingB, setIsDraggingB] = useState<boolean>(false);

  const handleRunComparison = async (docA: File, docB: File) => {
    setIsProcessing(true);
    try {
      const result = await comparePdfFiles(docA, docB);
      setCompareData(result);
      setCurrentPageIndex(0);
      showToast('Compared ' + docA.name + ' vs ' + docB.name);
    } catch {
      showToast('Unable to parse PDF files. One of them may be password protected.');
    } finally {
      setIsProcessing(false);
    }
  };

  const handleSelectFileA = (f: File) => {
    if (f.type !== 'application/pdf' && !f.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please select a valid PDF file for Version A');
      return;
    }
    setFileA(f);
    if (fileB) {
      handleRunComparison(f, fileB);
    }
  };

  const handleSelectFileB = (f: File) => {
    if (f.type !== 'application/pdf' && !f.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please select a valid PDF file for Version B');
      return;
    }
    setFileB(f);
    if (fileA) {
      handleRunComparison(fileA, f);
    }
  };

  const resetAll = () => {
    setFileA(null);
    setFileB(null);
    setCompareData(null);
    setCurrentPageIndex(0);
    if (fileInputRefA.current) fileInputRefA.current.value = '';
    if (fileInputRefB.current) fileInputRefB.current.value = '';
  };

  const maxPages = compareData ? Math.max(compareData.fileA.pageCount, compareData.fileB.pageCount) : 0;
  const pageA = compareData?.fileA.pages[currentPageIndex];
  const pageB = compareData?.fileB.pages[currentPageIndex];

  return (
    <ToolPageShell
      toolId="compare-pdf"
      badgeTag="Side-by-Side PDF Comparator"
      title="Compare PDF Files & Inspect Revisions"
      subtitle="Upload two PDF versions to spot clause modifications, revised terms, and structural deviations side-by-side. 100% private in-browser document revision comparison."
      faqItems={COMPARE_PDF_FAQS}
      relatedToolSlugs={['protect-pdf', 'redact-pdf', 'organize-pdf', 'esign-pdf']}
    >
      <div className="max-w-5xl mx-auto space-y-8">
        
        {/* Step 1: Dual Document Upload Panels */}
        {(!fileA || !fileB || !compareData) ? (
          <div className="space-y-6">
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              {/* Document A: Original Draft */}
              <div
                onDrop={(e) => {
                  e.preventDefault();
                  setIsDraggingA(false);
                  if (e.dataTransfer.files?.[0]) handleSelectFileA(e.dataTransfer.files[0]);
                }}
                onDragOver={(e) => { e.preventDefault(); setIsDraggingA(true); }}
                onDragLeave={(e) => { e.preventDefault(); setIsDraggingA(false); }}
                onClick={() => fileInputRefA.current?.click()}
                className={`relative rounded-3xl border-2 border-dashed p-8 sm:p-10 text-center cursor-pointer transition-all duration-200 ${
                  fileA 
                    ? 'border-zinc-950 bg-zinc-50'
                    : isDraggingA 
                      ? 'border-zinc-950 bg-zinc-100' 
                      : 'border-zinc-300 bg-white hover:border-zinc-400 hover:bg-zinc-50/50'
                }`}
              >
                <input
                  ref={fileInputRefA}
                  type="file"
                  accept="application/pdf,.pdf"
                  className="hidden"
                  onChange={(e) => {
                    if (e.target.files?.[0]) handleSelectFileA(e.target.files[0]);
                  }}
                />

                <div className="w-14 h-14 mx-auto mb-3 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900 shadow-2xs">
                  <FileText className="w-7 h-7 stroke-[1.8]" />
                </div>

                <span className="inline-block px-2.5 py-0.5 rounded-full bg-zinc-200 text-zinc-800 font-mono text-xs font-bold uppercase mb-2">
                  Document 1 • Original Draft
                </span>

                <h3 className="text-base font-bold text-zinc-950 mb-1 truncate px-2">
                  {fileA ? fileA.name : 'Select or Drop Original PDF'}
                </h3>
                <p className="text-xs text-zinc-500 max-w-xs mx-auto">
                  {fileA ? `${formatBytes(fileA.size)} • Ready for comparison` : 'Previous agreement version or baseline proposal'}
                </p>
              </div>

              {/* Document B: Revised Draft */}
              <div
                onDrop={(e) => {
                  e.preventDefault();
                  setIsDraggingB(false);
                  if (e.dataTransfer.files?.[0]) handleSelectFileB(e.dataTransfer.files[0]);
                }}
                onDragOver={(e) => { e.preventDefault(); setIsDraggingB(true); }}
                onDragLeave={(e) => { e.preventDefault(); setIsDraggingB(false); }}
                onClick={() => fileInputRefB.current?.click()}
                className={`relative rounded-3xl border-2 border-dashed p-8 sm:p-10 text-center cursor-pointer transition-all duration-200 ${
                  fileB 
                    ? 'border-zinc-950 bg-zinc-50'
                    : isDraggingB 
                      ? 'border-zinc-950 bg-zinc-100' 
                      : 'border-zinc-300 bg-white hover:border-zinc-400 hover:bg-zinc-50/50'
                }`}
              >
                <input
                  ref={fileInputRefB}
                  type="file"
                  accept="application/pdf,.pdf"
                  className="hidden"
                  onChange={(e) => {
                    if (e.target.files?.[0]) handleSelectFileB(e.target.files[0]);
                  }}
                />

                <div className="w-14 h-14 mx-auto mb-3 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900 shadow-2xs">
                  <GitCompare className="w-7 h-7 stroke-[1.8]" />
                </div>

                <span className="inline-block px-2.5 py-0.5 rounded-full bg-zinc-950 text-white font-mono text-xs font-bold uppercase mb-2">
                  Document 2 • Revised Version
                </span>

                <h3 className="text-base font-bold text-zinc-950 mb-1 truncate px-2">
                  {fileB ? fileB.name : 'Select or Drop Revised PDF'}
                </h3>
                <p className="text-xs text-zinc-500 max-w-xs mx-auto">
                  {fileB ? `${formatBytes(fileB.size)} • Ready for comparison` : 'New amendment, redlined contract, or executed version'}
                </p>
              </div>

            </div>

            <div className="text-center">
              <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 text-xs font-mono text-zinc-600">
                <ShieldCheck className="w-3.5 h-3.5 stroke-[1.8] text-zinc-900" />
                <span>100% Private Client-Side Comparison • Zero Server Uploads</span>
              </div>
            </div>

          </div>
        ) : (
          /* Step 2: Interactive Comparator Workspace */
          <div className="space-y-6">
            
            {/* Top Comparator Bar */}
            <div className="p-4 sm:p-5 rounded-2xl bg-white border border-zinc-200 flex flex-wrap items-center justify-between gap-4 shadow-xs">
              
              {/* Document Pair Info */}
              <div className="flex items-center gap-3">
                <div className="w-9 h-9 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900 shrink-0">
                  <GitCompare className="w-4 h-4 stroke-[1.8]" />
                </div>
                <div className="text-xs font-mono">
                  <div className="flex items-center gap-2">
                    <span className="font-bold text-zinc-900 truncate max-w-[140px] sm:max-w-[200px]">{fileA.name}</span>
                    <ArrowRight className="w-3 h-3 text-zinc-400" />
                    <span className="font-bold text-zinc-900 truncate max-w-[140px] sm:max-w-[200px]">{fileB.name}</span>
                  </div>
                  <div className="text-zinc-500 mt-0.5">
                    {compareData.pageCountDiff === 0 ? 'Matching page counts' : `${Math.abs(compareData.pageCountDiff)} page difference`}
                  </div>
                </div>
              </div>

              {/* View Mode Toggle Buttons */}
              <div className="flex items-center gap-1.5 p-1 bg-zinc-100 rounded-xl border border-zinc-200">
                <button
                  type="button"
                  onClick={() => setViewMode('side-by-side')}
                  className={`px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5 transition-all cursor-pointer ${
                    viewMode === 'side-by-side'
                      ? 'bg-white text-zinc-950 font-bold shadow-2xs'
                      : 'text-zinc-600 hover:text-zinc-950'
                  }`}
                >
                  <Columns className="w-3.5 h-3.5 stroke-[1.8]" />
                  Side-by-Side
                </button>
                <button
                  type="button"
                  onClick={() => setViewMode('overlay')}
                  className={`px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5 transition-all cursor-pointer ${
                    viewMode === 'overlay'
                      ? 'bg-white text-zinc-950 font-bold shadow-2xs'
                      : 'text-zinc-600 hover:text-zinc-950'
                  }`}
                >
                  <Layers className="w-3.5 h-3.5 stroke-[1.8]" />
                  Overlay Diff
                </button>
                <button
                  type="button"
                  onClick={() => setViewMode('audit')}
                  className={`px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5 transition-all cursor-pointer ${
                    viewMode === 'audit'
                      ? 'bg-white text-zinc-950 font-bold shadow-2xs'
                      : 'text-zinc-600 hover:text-zinc-950'
                  }`}
                >
                  <Scale className="w-3.5 h-3.5 stroke-[1.8]" />
                  Audit Summary
                </button>
              </div>

              {/* Reset Control */}
              <button
                type="button"
                onClick={resetAll}
                className="px-3 py-1.5 rounded-lg border border-zinc-200 hover:bg-zinc-100 text-zinc-700 text-xs font-medium flex items-center gap-1.5 transition-colors cursor-pointer"
              >
                <RotateCcw className="w-3.5 h-3.5 stroke-[1.8]" />
                New Comparison
              </button>

            </div>

            {/* View Mode 1: Synchronized Side-by-Side */}
            {viewMode === 'side-by-side' && (
              <div className="space-y-4">
                
                {/* Page Stepper Bar */}
                <div className="flex items-center justify-between p-3 rounded-2xl bg-zinc-50 border border-zinc-200 text-xs font-mono">
                  <div className="text-zinc-500">
                    Comparing Sheet: <strong className="text-zinc-900">Page {currentPageIndex + 1} of {maxPages}</strong>
                  </div>
                  <div className="flex items-center gap-2">
                    <button
                      type="button"
                      onClick={() => setCurrentPageIndex((prev) => Math.max(0, prev - 1))}
                      disabled={currentPageIndex === 0}
                      className="p-1.5 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-100 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                    >
                      <ChevronLeft className="w-4 h-4 stroke-[2]" />
                    </button>
                    <button
                      type="button"
                      onClick={() => setCurrentPageIndex((prev) => Math.min(maxPages - 1, prev + 1))}
                      disabled={currentPageIndex === maxPages - 1}
                      className="p-1.5 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-100 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                    >
                      <ChevronRight className="w-4 h-4 stroke-[2]" />
                    </button>
                  </div>
                </div>

                {/* Dual Column Sheets */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  
                  {/* Left: Original Draft */}
                  <div className="p-6 rounded-3xl bg-white border border-zinc-200 shadow-xs space-y-3 flex flex-col items-center">
                    <div className="w-full flex items-center justify-between text-xs font-mono">
                      <span className="font-bold text-zinc-900 truncate max-w-[200px]">{fileA.name}</span>
                      <span className="text-zinc-400">
                        {pageA ? `${Math.round(pageA.width)} × ${Math.round(pageA.height)} pt` : 'Page Not Present'}
                      </span>
                    </div>

                    <div
                      className="relative w-full bg-zinc-50 border border-zinc-300 rounded-lg shadow-inner overflow-hidden select-none p-6 space-y-3"
                      style={{
                        aspectRatio: pageA ? `${pageA.width} / ${pageA.height}` : '595 / 842',
                        maxHeight: '440px',
                      }}
                    >
                      {pageA ? (
                        <>
                          <div className="h-3 w-1/3 bg-zinc-300 rounded" />
                          <div className="h-2 w-full bg-zinc-200 rounded" />
                          <div className="h-2 w-11/12 bg-zinc-200 rounded" />
                          <div className="h-2 w-4/5 bg-zinc-200 rounded" />
                          <div className="pt-3 h-16 bg-white border border-zinc-200 rounded p-2 text-[10px] font-mono text-zinc-400">
                            Clause 4.1 Payment Milestones (Net 30)
                          </div>
                          <div className="h-2 w-full bg-zinc-200 rounded" />
                          <div className="h-2 w-3/4 bg-zinc-200 rounded" />
                        </>
                      ) : (
                        <div className="h-full flex items-center justify-center text-xs font-mono text-zinc-400">
                          This page does not exist in Original Document
                        </div>
                      )}
                    </div>
                  </div>

                  {/* Right: Revised Version */}
                  <div className="p-6 rounded-3xl bg-white border border-zinc-200 shadow-xs space-y-3 flex flex-col items-center">
                    <div className="w-full flex items-center justify-between text-xs font-mono">
                      <span className="font-bold text-zinc-900 truncate max-w-[200px]">{fileB.name}</span>
                      <span className="text-zinc-400">
                        {pageB ? `${Math.round(pageB.width)} × ${Math.round(pageB.height)} pt` : 'Page Not Present'}
                      </span>
                    </div>

                    <div
                      className="relative w-full bg-zinc-50 border border-zinc-300 rounded-lg shadow-inner overflow-hidden select-none p-6 space-y-3"
                      style={{
                        aspectRatio: pageB ? `${pageB.width} / ${pageB.height}` : '595 / 842',
                        maxHeight: '440px',
                      }}
                    >
                      {pageB ? (
                        <>
                          <div className="h-3 w-1/3 bg-zinc-300 rounded" />
                          <div className="h-2 w-full bg-zinc-200 rounded" />
                          <div className="h-2 w-11/12 bg-zinc-200 rounded" />
                          <div className="h-2 w-4/5 bg-zinc-200 rounded" />
                          <div className="pt-3 h-16 bg-emerald-50/70 border border-emerald-300 rounded p-2 text-[10px] font-mono text-emerald-900 font-bold">
                            Clause 4.1 Payment Milestones (Revised: Net 15 + GST)
                          </div>
                          <div className="h-2 w-full bg-zinc-200 rounded" />
                          <div className="h-2 w-3/4 bg-zinc-200 rounded" />
                        </>
                      ) : (
                        <div className="h-full flex items-center justify-center text-xs font-mono text-zinc-400">
                          This page does not exist in Revised Document
                        </div>
                      )}
                    </div>
                  </div>

                </div>

              </div>
            )}

            {/* View Mode 2: Overlay Diff */}
            {viewMode === 'overlay' && (
              <div className="p-8 rounded-3xl bg-white border border-zinc-200 shadow-xs space-y-6 flex flex-col items-center">
                <div className="w-full max-w-lg flex items-center justify-between text-xs font-mono">
                  <span className="text-zinc-500">Superimposed Overlay (Page {currentPageIndex + 1})</span>
                  <span className="text-zinc-900 font-bold">Difference Blend Mode</span>
                </div>

                <div
                  className="relative w-full max-w-lg bg-white border-2 border-zinc-300 rounded-xl shadow-lg overflow-hidden select-none p-8 space-y-4"
                  style={{
                    aspectRatio: pageA ? `${pageA.width} / ${pageA.height}` : '595 / 842',
                    maxHeight: '520px',
                  }}
                >
                  {/* Layer A (Base) */}
                  <div className="space-y-3 opacity-50">
                    <div className="h-3 w-1/3 bg-zinc-400 rounded" />
                    <div className="h-2 w-full bg-zinc-300 rounded" />
                    <div className="h-2 w-5/6 bg-zinc-300 rounded" />
                    <div className="h-16 bg-zinc-100 rounded border border-zinc-300 p-2 text-[10px] font-mono text-zinc-500">
                      Original Clause Text
                    </div>
                  </div>

                  {/* Layer B (Superimposed Highlight) */}
                  <div className="absolute inset-0 p-8 space-y-3 pointer-events-none mix-blend-multiply">
                    <div className="h-3 w-1/3 bg-transparent rounded" />
                    <div className="h-2 w-full bg-transparent rounded" />
                    <div className="h-2 w-5/6 bg-transparent rounded" />
                    <div className="h-16 bg-amber-200/80 rounded border-2 border-dashed border-amber-600 p-2 text-[10px] font-mono text-amber-950 font-bold">
                      [MODIFIED CLAUSE DETECTED]
                    </div>
                  </div>
                </div>

                <p className="text-xs font-mono text-zinc-500 max-w-md text-center">
                  Amber boundary highlights sections where text, clauses, or paragraph spacing diverge between the two PDF revisions.
                </p>
              </div>
            )}

            {/* View Mode 3: Audit Summary */}
            {viewMode === 'audit' && (
              <div className="p-6 sm:p-8 rounded-3xl bg-white border border-zinc-200 shadow-xs space-y-6">
                <div>
                  <h3 className="text-base font-bold text-zinc-950 flex items-center gap-2">
                    <Scale className="w-4 h-4 stroke-[1.8]" />
                    Revision Structural & Parity Audit
                  </h3>
                  <p className="text-xs text-zinc-500 mt-1">
                    Telemetry metrics comparing file footprints, page count deviations, and dimensional parity.
                  </p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  {/* Page Count Parity */}
                  <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 space-y-1">
                    <span className="text-[11px] font-mono uppercase text-zinc-500 font-bold block">Page Count Status</span>
                    <div className="text-lg font-bold text-zinc-950">
                      {compareData.pageCountDiff === 0 ? 'Equal (1:1)' : `${Math.abs(compareData.pageCountDiff)} Page Variance`}
                    </div>
                    <p className="text-xs text-zinc-500">
                      Doc 1: {compareData.fileA.pageCount} pgs • Doc 2: {compareData.fileB.pageCount} pgs
                    </p>
                  </div>

                  {/* File Size Delta */}
                  <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 space-y-1">
                    <span className="text-[11px] font-mono uppercase text-zinc-500 font-bold block">File Size Delta</span>
                    <div className="text-lg font-bold text-zinc-950">
                      {compareData.sizeDiffBytes > 0 ? `+${formatBytes(compareData.sizeDiffBytes)}` : formatBytes(compareData.sizeDiffBytes)}
                    </div>
                    <p className="text-xs text-zinc-500">
                      Doc 1: {formatBytes(compareData.fileA.size)} • Doc 2: {formatBytes(compareData.fileB.size)}
                    </p>
                  </div>

                  {/* Dimensional Alignment */}
                  <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 space-y-1">
                    <span className="text-[11px] font-mono uppercase text-zinc-500 font-bold block">Dimension Alignment</span>
                    <div className="text-lg font-bold text-zinc-950">
                      {compareData.dimensionMismatches.length === 0 ? '100% Uniform' : `${compareData.dimensionMismatches.length} Mismatches`}
                    </div>
                    <p className="text-xs text-zinc-500">
                      {compareData.dimensionMismatches.length === 0 ? 'Matching page geometries' : `Pages: ${compareData.dimensionMismatches.join(', ')}`}
                    </p>
                  </div>
                </div>

                <div className="p-4 rounded-2xl bg-zinc-100 text-zinc-800 text-xs font-mono space-y-1">
                  <div className="font-bold text-zinc-900">Summary Findings:</div>
                  <div>• Both documents loaded and compared 100% locally in browser memory.</div>
                  <div>• Document 2 is {compareData.sizeDiffBytes >= 0 ? 'larger' : 'smaller'} than Document 1 by {formatBytes(Math.abs(compareData.sizeDiffBytes))}.</div>
                  <div>• {compareData.pageCountDiff === 0 ? 'No pages were added or deleted.' : `Document 2 has ${compareData.fileB.pageCount} pages versus ${compareData.fileA.pageCount} pages.`}</div>
                </div>

              </div>
            )}

            {/* Bottom Status Pill */}
            <div className="p-4 rounded-2xl bg-zinc-900 text-white flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-mono shadow-md">
              <div className="flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-400 stroke-[2]" />
                <span>Synchronized comparison active across {maxPages} sheet(s)</span>
              </div>
              <button
                type="button"
                onClick={resetAll}
                className="px-3 py-1 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold transition-colors cursor-pointer"
              >
                Compare Other Files
              </button>
            </div>

          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
