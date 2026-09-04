'use client';

import React, { useState, useRef, useMemo } from 'react';
import { 
  FileText, 
  Scissors, 
  Download, 
  Check, 
  Sparkles, 
  ShieldCheck, 
  AlertCircle, 
  Trash2, 
  RotateCcw,
  CheckSquare,
  Square,
  FileCheck,
  Layers,
  ArrowRight
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo, extractPdfPages, downloadPdfBlob, PageInfo } from '@/lib/pdf-engine';

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
}

// Convert 0-based sorted array to human string: e.g. [0,1,2,4] -> "1-3, 5"
function formatPageRanges(indices: number[]): string {
  if (indices.length === 0) return '';
  const sorted = [...indices].sort((a, b) => a - b);
  const ranges: string[] = [];
  let start = sorted[0];
  let prev = start;

  for (let i = 1; i <= sorted.length; i++) {
    const curr = sorted[i];
    if (curr === prev + 1) {
      prev = curr;
    } else {
      if (start === prev) {
        ranges.push(`${start + 1}`);
      } else {
        ranges.push(`${start + 1}-${prev + 1}`);
      }
      start = curr;
      prev = curr;
    }
  }

  return ranges.join(', ');
}

// Parse human string "1-3, 5" into 0-based indices
function parsePageRangeInput(input: string, totalPages: number): number[] {
  const result = new Set<number>();
  const tokens = input.split(',').map((t) => t.trim()).filter(Boolean);

  for (const token of tokens) {
    if (token.includes('-')) {
      const parts = token.split('-').map((s) => s.trim());
      const start = parseInt(parts[0], 10);
      const end = parseInt(parts[1], 10);
      if (!isNaN(start) && !isNaN(end)) {
        const min = Math.max(1, Math.min(start, end));
        const max = Math.min(totalPages, Math.max(start, end));
        for (let p = min; p <= max; p++) {
          result.add(p - 1);
        }
      }
    } else {
      const page = parseInt(token, 10);
      if (!isNaN(page) && page >= 1 && page <= totalPages) {
        result.add(page - 1);
      }
    }
  }

  return Array.from(result).sort((a, b) => a - b);
}

export default function SplitPdfPage() {
  const [file, setFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [pages, setPages] = useState<PageInfo[]>([]);
  const [selectedPages, setSelectedPages] = useState<Set<number>>(new Set());
  const [rangeInput, setRangeInput] = useState<string>('');
  const [outputFileName, setOutputFileName] = useState<string>('cora-extracted-pages.pdf');
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);

  const fileInputRef = useRef<HTMLInputElement>(null);
  const { showToast } = useToast();

  const handleFileLoad = async (loadedFile: File) => {
    if (loadedFile.type !== 'application/pdf' && !loadedFile.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please select a valid PDF file');
      return;
    }

    setIsLoading(true);
    try {
      const info = await getPdfInfo(loadedFile);
      setFile(loadedFile);
      setPageCount(info.pageCount);
      setPages(info.pages);

      // Default select first page or all if small
      const initialSelection = new Set<number>();
      if (info.pageCount <= 3) {
        for (let i = 0; i < info.pageCount; i++) initialSelection.add(i);
      } else {
        initialSelection.add(0); // select page 1 by default
      }
      setSelectedPages(initialSelection);
      setRangeInput(formatPageRanges(Array.from(initialSelection)));

      const baseName = loadedFile.name.replace(/\.[^/.]+$/, '');
      setOutputFileName(`${baseName}-extracted.pdf`);

      showToast(`Loaded ${loadedFile.name} (${info.pageCount} pages)`);
    } catch (err: any) {
      console.error(err);
      showToast('Failed to parse PDF document. It may be password-protected.');
    } finally {
      setIsLoading(false);
    }
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      handleFileLoad(e.target.files[0]);
      e.target.value = '';
    }
  };

  const handleDrop = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFileLoad(e.dataTransfer.files[0]);
    }
  };

  const handleDragOver = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(true);
  };

  const handleDragLeave = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
  };

  // Toggle page in grid
  const togglePage = (pageIndex: number) => {
    setSelectedPages((prev) => {
      const next = new Set(prev);
      if (next.has(pageIndex)) {
        next.delete(pageIndex);
      } else {
        next.add(pageIndex);
      }
      setRangeInput(formatPageRanges(Array.from(next)));
      return next;
    });
  };

  // Range text change
  const handleRangeInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const text = e.target.value;
    setRangeInput(text);
    if (pageCount > 0) {
      const parsed = parsePageRangeInput(text, pageCount);
      setSelectedPages(new Set(parsed));
    }
  };

  // Selection presets
  const selectAll = () => {
    const all = new Set<number>();
    for (let i = 0; i < pageCount; i++) all.add(i);
    setSelectedPages(all);
    setRangeInput(formatPageRanges(Array.from(all)));
  };

  const deselectAll = () => {
    setSelectedPages(new Set());
    setRangeInput('');
  };

  const selectOddPages = () => {
    const odds = new Set<number>();
    for (let i = 0; i < pageCount; i += 2) odds.add(i);
    setSelectedPages(odds);
    setRangeInput(formatPageRanges(Array.from(odds)));
  };

  const selectEvenPages = () => {
    const evens = new Set<number>();
    for (let i = 1; i < pageCount; i += 2) evens.add(i);
    setSelectedPages(evens);
    setRangeInput(formatPageRanges(Array.from(evens)));
  };

  const selectLastPage = () => {
    if (pageCount > 0) {
      const last = new Set<number>([pageCount - 1]);
      setSelectedPages(last);
      setRangeInput(formatPageRanges(Array.from(last)));
      showToast('Selected final signature page');
    }
  };

  const resetTool = () => {
    setFile(null);
    setPageCount(0);
    setPages([]);
    setSelectedPages(new Set());
    setRangeInput('');
  };

  // Extraction Execution
  const handleExtract = async () => {
    if (!file) {
      showToast('Please upload a PDF file first');
      return;
    }
    if (selectedPages.size === 0) {
      showToast('Please select at least 1 page to extract');
      return;
    }

    setIsProcessing(true);
    try {
      const sortedIndices = Array.from(selectedPages).sort((a, b) => a - b);
      const extractedBytes = await extractPdfPages(file, sortedIndices);
      
      const fileName = outputFileName.trim().length > 0 
        ? (outputFileName.endsWith('.pdf') ? outputFileName : `${outputFileName}.pdf`)
        : 'extracted-pages.pdf';

      downloadPdfBlob(extractedBytes, fileName);
      showToast(`Extracted ${sortedIndices.length} pages successfully!`);
    } catch (err: any) {
      console.error('Extraction error:', err);
      showToast(err?.message || 'Failed to extract pages. Please try again.');
    } finally {
      setIsProcessing(false);
    }
  };

  const splitFaqs = [
    {
      question: 'Why isolate signed signature pages or specific scopes?',
      answer: 'Full client agreements often contain confidential internal hourly rates, sensitive trade secrets, or preliminary negotiations. Isolating the executed Section 10A signature deed or Statement of Work allows you to share only relevant legal proof with third-party banks, auditors, or vendors.'
    },
    {
      question: 'How do I specify multiple separate page ranges?',
      answer: 'Use comma-separated numbers and hyphens. For example, "1-3, 5, 8-10" extracts pages 1 through 3, page 5, and pages 8 through 10. You can also tap page cards directly in the visual grid to toggle them.'
    },
    {
      question: 'Is any document data sent to an external server?',
      answer: 'No. The entire extraction engine runs 100% client-side inside your browser via WebAssembly. Your confidential NDAs, agency pitch decks, and financial statements never leave your device.'
    },
    {
      question: 'Are extracted pages compressed or degraded?',
      answer: 'No. Cora extracts the raw native page streams losslessly. Embedded typography, digital signatures, vector artwork, and high-resolution images retain 100% of their original fidelity.'
    }
  ];

  return (
    <ToolPageShell
      toolId="split-pdf"
      badgeTag="In-Memory Page Extractor"
      title="Split & Extract PDF Pages"
      subtitle="Extract specific pages, isolate executed signature deeds, or remove unwanted sections. 100% private, client-side browser execution with zero server uploads."
      faqItems={splitFaqs}
    >
      <div className="space-y-6">
        {/* Hidden Input */}
        <input
          ref={fileInputRef}
          type="file"
          accept="application/pdf,.pdf"
          onChange={handleFileChange}
          className="hidden"
          id="cora-split-pdf-input"
        />

        {/* ── Dropzone (If no file loaded) ── */}
        {!file ? (
          <div
            onDrop={handleDrop}
            onDragOver={handleDragOver}
            onDragLeave={handleDragLeave}
            onClick={() => fileInputRef.current?.click()}
            className={`relative rounded-3xl border-2 border-dashed p-6 sm:p-12 text-center transition-all cursor-pointer select-none group ${
              isDraggingOver
                ? 'border-zinc-950 bg-zinc-100/80 shadow-md scale-[0.99]'
                : 'border-zinc-200/90 bg-white hover:border-zinc-400 hover:bg-zinc-50/50 shadow-2xs'
            }`}
          >
            <div className="max-w-md mx-auto flex flex-col items-center justify-center space-y-3">
              <div className="w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-200/80 flex items-center justify-center text-zinc-900 group-hover:scale-105 transition-transform duration-200">
                <Scissors className="w-6 h-6 stroke-[1.75]" />
              </div>

              <div className="space-y-1">
                <h3 className="text-sm sm:text-base font-bold text-zinc-950 tracking-tight">
                  Drop your PDF here, or <span className="underline underline-offset-2 text-zinc-900">browse file</span>
                </h3>
                <p className="text-xs text-zinc-500 leading-relaxed">
                  Upload a single multi-page contract, proposal, or presentation to split
                </p>
              </div>

              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 text-[11px] font-mono text-zinc-600 border border-zinc-200/60">
                <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" />
                <span>Zero server upload &bull; 100% private in memory</span>
              </div>
            </div>
          </div>
        ) : (
          /* ── Interactive Split Console ── */
          <div className="bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs space-y-6">
            {/* Active File Header */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-zinc-100">
              <div className="flex items-center gap-3 min-w-0">
                <div className="w-10 h-10 rounded-xl bg-zinc-900 text-white flex items-center justify-center shrink-0">
                  <FileText className="w-5 h-5 stroke-[1.75]" />
                </div>
                <div className="min-w-0">
                  <h4 className="text-sm font-bold text-zinc-950 truncate">
                    {file.name}
                  </h4>
                  <p className="text-[11px] font-mono text-zinc-500">
                    {pageCount} pages &bull; {formatBytes(file.size)}
                  </p>
                </div>
              </div>

              <div className="flex items-center gap-2 shrink-0">
                <button
                  type="button"
                  onClick={() => fileInputRef.current?.click()}
                  className="px-3 py-1.5 rounded-xl border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-800 text-xs font-semibold transition-colors cursor-pointer"
                >
                  Change File
                </button>
                <button
                  type="button"
                  onClick={resetTool}
                  className="p-1.5 rounded-xl border border-zinc-200 text-zinc-400 hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-colors cursor-pointer"
                  title="Remove document"
                >
                  <Trash2 className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* ── Selection Control Bar & Range Input ── */}
            <div className="space-y-3">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <label className="text-xs font-bold text-zinc-900 flex items-center gap-2">
                  <span>Page Range / Selection</span>
                  <span className="text-[11px] font-mono font-normal text-zinc-500">
                    ({selectedPages.size} of {pageCount} selected)
                  </span>
                </label>

                {/* Quick Presets */}
                <div className="flex flex-wrap items-center gap-1.5">
                  <button
                    type="button"
                    onClick={selectAll}
                    className="px-2.5 py-1 rounded-lg border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-700 text-[11px] font-medium transition-colors cursor-pointer"
                  >
                    Select All
                  </button>
                  <button
                    type="button"
                    onClick={deselectAll}
                    className="px-2.5 py-1 rounded-lg border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-700 text-[11px] font-medium transition-colors cursor-pointer"
                  >
                    Clear
                  </button>
                  <button
                    type="button"
                    onClick={selectOddPages}
                    className="px-2.5 py-1 rounded-lg border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-700 text-[11px] font-medium transition-colors cursor-pointer"
                  >
                    Odd
                  </button>
                  <button
                    type="button"
                    onClick={selectEvenPages}
                    className="px-2.5 py-1 rounded-lg border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-700 text-[11px] font-medium transition-colors cursor-pointer"
                  >
                    Even
                  </button>
                  <button
                    type="button"
                    onClick={selectLastPage}
                    className="px-2.5 py-1 rounded-lg border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-700 text-[11px] font-medium transition-colors cursor-pointer"
                  >
                    Signature Page
                  </button>
                </div>
              </div>

              {/* Text Range Input */}
              <div className="relative">
                <input
                  type="text"
                  value={rangeInput}
                  onChange={handleRangeInputChange}
                  placeholder="e.g. 1-3, 5, 8-10"
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 bg-white text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950 shadow-2xs"
                />
                <span className="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-mono text-zinc-400 pointer-events-none">
                  Total: {pageCount}p
                </span>
              </div>
            </div>

            {/* ── Visual Page Grid ── */}
            <div className="space-y-2">
              <div className="flex items-center justify-between text-[11px] font-mono text-zinc-500">
                <span>Interactive Page Selector (Tap to select)</span>
                <span>{selectedPages.size} pages marked for export</span>
              </div>

              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 max-h-[420px] overflow-y-auto p-1 rounded-2xl border border-zinc-100 bg-zinc-50/50">
                {pages.map((p, idx) => {
                  const isSelected = selectedPages.has(idx);
                  const isLastPage = idx === pageCount - 1;

                  return (
                    <button
                      key={idx}
                      type="button"
                      onClick={() => togglePage(idx)}
                      className={`relative rounded-2xl p-3 text-left transition-all border flex flex-col justify-between aspect-[1/1.3] group cursor-pointer ${
                        isSelected
                          ? 'border-zinc-950 bg-white ring-2 ring-zinc-950 shadow-sm'
                          : 'border-zinc-200/90 bg-white/80 hover:border-zinc-400 hover:bg-white'
                      }`}
                    >
                      {/* Top Row: Page number & Check indicator */}
                      <div className="flex items-center justify-between w-full">
                        <span className="text-[11px] font-mono font-bold text-zinc-700">
                          P.{idx + 1}
                        </span>
                        <div
                          className={`w-4 h-4 rounded-md flex items-center justify-center transition-colors ${
                            isSelected
                              ? 'bg-zinc-950 text-white'
                              : 'border border-zinc-300 text-transparent group-hover:border-zinc-400'
                          }`}
                        >
                          <Check className="w-3 h-3 stroke-[2.5]" />
                        </div>
                      </div>

                      {/* Mock Page Content Canvas */}
                      <div className="w-full my-auto space-y-1.5 px-1 opacity-75">
                        <div className="h-1.5 bg-zinc-200 rounded w-2/3" />
                        <div className="h-1 bg-zinc-150 rounded w-full bg-zinc-100" />
                        <div className="h-1 bg-zinc-150 rounded w-5/6 bg-zinc-100" />
                        <div className="h-1 bg-zinc-150 rounded w-4/6 bg-zinc-100" />
                        {isLastPage && (
                          <div className="mt-2 pt-1 border-t border-dashed border-zinc-200 flex items-center justify-between">
                            <span className="text-[8px] font-mono text-emerald-600 font-bold">SEAL</span>
                            <div className="w-4 h-1.5 bg-emerald-100 rounded" />
                          </div>
                        )}
                      </div>

                      {/* Bottom Footer */}
                      <div className="text-[10px] font-mono text-zinc-400 text-center w-full truncate">
                        {isLastPage ? 'Signatory Page' : `Sheet ${idx + 1}`}
                      </div>
                    </button>
                  );
                })}
              </div>
            </div>

            {/* ── Summary & Export Filename ── */}
            <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/80 space-y-3">
              <div>
                <label className="text-[11px] font-mono font-bold text-zinc-600 uppercase tracking-wider block mb-1.5">
                  Export Filename
                </label>
                <input
                  type="text"
                  value={outputFileName}
                  onChange={(e) => setOutputFileName(e.target.value)}
                  placeholder="extracted-pages.pdf"
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 bg-white text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950 shadow-2xs"
                />
              </div>
            </div>

            {/* ── Action Button ── */}
            <div className="pt-2">
              <button
                type="button"
                disabled={selectedPages.size === 0 || isProcessing}
                onClick={handleExtract}
                className="w-full py-3.5 px-5 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-sm flex items-center justify-center gap-2.5 shadow-md active:scale-[0.99] disabled:opacity-50 disabled:pointer-events-none transition-all cursor-pointer"
              >
                {isProcessing ? (
                  <>
                    <div className="w-4 h-4 rounded-full border-2 border-white/30 border-t-white animate-spin" />
                    <span>Extracting {selectedPages.size} Pages...</span>
                  </>
                ) : (
                  <>
                    <Scissors className="w-4 h-4" />
                    <span>Extract {selectedPages.size} {selectedPages.size === 1 ? 'Page' : 'Pages'} & Download</span>
                  </>
                )}
              </button>
              {selectedPages.size === 0 && (
                <p className="text-[11px] text-zinc-400 text-center mt-2">
                  Select at least 1 page from the grid or range input to extract
                </p>
              )}
            </div>
          </div>
        )}
      </div>
    </ToolPageShell>
  );
}
