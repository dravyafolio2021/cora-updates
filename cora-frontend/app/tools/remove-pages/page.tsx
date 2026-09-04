'use client';

import React, { useState, useRef, useMemo, useEffect } from 'react';
import { 
  Trash2, 
  UploadCloud, 
  FileText, 
  Scissors, 
  Download, 
  RefreshCw, 
  ShieldCheck, 
  AlertCircle, 
  Check, 
  FileX, 
  Layers, 
  RotateCcw,
  Sparkles,
  SlidersHorizontal,
  ChevronRight
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo, removePagesFromPdf, downloadPdfBlob, PageInfo } from '@/lib/pdf-engine';

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
}

// Convert 1-based page numbers set into formatted string (e.g. [2,3,4,7] -> "2-4, 7")
function formatPageRanges(pageNumbers: number[]): string {
  if (pageNumbers.length === 0) return '';
  const sorted = [...pageNumbers].sort((a, b) => a - b);
  const ranges: string[] = [];
  let start = sorted[0];
  let prev = start;

  for (let i = 1; i <= sorted.length; i++) {
    const curr = sorted[i];
    if (curr === prev + 1) {
      prev = curr;
    } else {
      if (start === prev) {
        ranges.push(`${start}`);
      } else {
        ranges.push(`${start}-${prev}`);
      }
      start = curr;
      prev = curr;
    }
  }

  return ranges.join(', ');
}

// Parse human input "2, 4-6, 8" into 1-based page numbers set
function parsePageRangeInput(input: string, totalPages: number): Set<number> {
  const result = new Set<number>();
  if (!input.trim() || totalPages <= 0) return result;

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
          result.add(p);
        }
      }
    } else {
      const page = parseInt(token, 10);
      if (!isNaN(page) && page >= 1 && page <= totalPages) {
        result.add(page);
      }
    }
  }

  return result;
}

const FAQ_ITEMS = [
  {
    question: 'How does Cora remove pages from PDF without uploading my document?',
    answer: 'Cora uses a client-side WebAssembly and JavaScript PDF engine (pdf-lib) that runs 100% inside your browser memory. Your confidential contracts, invoices, and sensitive documents are processed locally without transmitting a single byte to an external web server.',
  },
  {
    question: 'Can I delete multiple non-consecutive pages or page ranges at once?',
    answer: 'Yes. You can click on individual page chips to toggle them for deletion, or type custom range queries such as "2, 5-8, 11" in the range bar. Both the interactive grid and range input synchronize in real-time.',
  },
  {
    question: 'Will deleting pages affect document resolution or vector text quality?',
    answer: 'No. Cora extracts the surviving page object trees losslessly. All vector fonts, digital signature certificates, embedded images, and original page geometries remain pixel-perfect with zero re-rasterization compression.',
  },
  {
    question: 'What happens if I try to delete all pages from the PDF?',
    answer: 'A valid PDF document must contain at least one surviving page. Cora includes built-in guardrails that disable the deletion button and warn you if all pages have been selected for deletion.',
  },
  {
    question: 'Is there any file size limit or daily quota on this tool?',
    answer: 'There are no artificial file size caps, page count restrictions, or paywalls. Processing speed depends entirely on your device memory, and Cora will never ask you to create an account or provide payment information.',
  },
];

export default function RemovePagesPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [pages, setPages] = useState<PageInfo[]>([]);
  const [pagesToDelete, setPagesToDelete] = useState<Set<number>>(new Set()); // 1-based page numbers
  const [rangeInput, setRangeInput] = useState<string>('');
  const [outputFileName, setOutputFileName] = useState<string>('cora-trimmed-document.pdf');
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);

  // Synchronize range input when pagesToDelete changes via click
  const updateRangeInputFromSet = (set: Set<number>) => {
    const formatted = formatPageRanges(Array.from(set));
    setRangeInput(formatted);
  };

  const handleFileLoad = async (loadedFile: File) => {
    if (loadedFile.type !== 'application/pdf' && !loadedFile.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please select a valid PDF file');
      return;
    }

    setIsLoading(true);
    try {
      const info = await getPdfInfo(loadedFile);
      setPdfFile(loadedFile);
      setPageCount(info.pageCount);
      setPages(info.pages);
      
      // Default: No pages marked for deletion initially
      setPagesToDelete(new Set());
      setRangeInput('');

      const baseName = loadedFile.name.replace(/\.pdf$/i, '');
      setOutputFileName(`${baseName}-trimmed.pdf`);
      showToast(`Loaded ${loadedFile.name} (${info.pageCount} pages)`);
    } catch (err) {
      console.error(err);
      showToast('Failed to load PDF. The file may be password protected or corrupted.');
    } finally {
      setIsLoading(false);
    }
  };

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDraggingOver(true);
  };

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDraggingOver(false);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDraggingOver(false);
    const files = e.dataTransfer.files;
    if (files && files.length > 0) {
      handleFileLoad(files[0]);
    }
  };

  const togglePageDeletion = (pageNum: number) => {
    const nextSet = new Set(pagesToDelete);
    if (nextSet.has(pageNum)) {
      nextSet.delete(pageNum);
    } else {
      nextSet.add(pageNum);
    }
    setPagesToDelete(nextSet);
    updateRangeInputFromSet(nextSet);
  };

  const handleRangeInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const val = e.target.value;
    setRangeInput(val);
    const parsed = parsePageRangeInput(val, pageCount);
    setPagesToDelete(parsed);
  };

  const selectEvenPages = () => {
    const nextSet = new Set<number>();
    for (let i = 2; i <= pageCount; i += 2) {
      nextSet.add(i);
    }
    setPagesToDelete(nextSet);
    updateRangeInputFromSet(nextSet);
    showToast(`Marked ${nextSet.size} even pages for deletion`);
  };

  const selectOddPages = () => {
    const nextSet = new Set<number>();
    for (let i = 1; i <= pageCount; i += 2) {
      nextSet.add(i);
    }
    setPagesToDelete(nextSet);
    updateRangeInputFromSet(nextSet);
    showToast(`Marked ${nextSet.size} odd pages for deletion`);
  };

  const selectAllPages = () => {
    const nextSet = new Set<number>();
    for (let i = 1; i <= pageCount; i++) {
      nextSet.add(i);
    }
    setPagesToDelete(nextSet);
    updateRangeInputFromSet(nextSet);
  };

  const clearSelection = () => {
    setPagesToDelete(new Set());
    setRangeInput('');
    showToast('Cleared deletion selection');
  };

  const invertSelection = () => {
    const nextSet = new Set<number>();
    for (let i = 1; i <= pageCount; i++) {
      if (!pagesToDelete.has(i)) {
        nextSet.add(i);
      }
    }
    setPagesToDelete(nextSet);
    updateRangeInputFromSet(nextSet);
    showToast(`Inverted selection: ${nextSet.size} pages marked`);
  };

  const handleRemovePages = async () => {
    if (!pdfFile || pagesToDelete.size === 0) return;

    if (pagesToDelete.size >= pageCount) {
      showToast('Cannot delete all pages. At least one page must remain.');
      return;
    }

    setIsProcessing(true);
    try {
      const pageNumbersArray = Array.from(pagesToDelete);
      const trimmedBytes = await removePagesFromPdf(pdfFile, pageNumbersArray);
      downloadPdfBlob(trimmedBytes, outputFileName);
      showToast(`Successfully removed ${pagesToDelete.size} ${pagesToDelete.size === 1 ? 'page' : 'pages'}!`);
    } catch (err) {
      console.error(err);
      showToast('Error removing pages. Please try again.');
    } finally {
      setIsProcessing(false);
    }
  };

  const resetAll = () => {
    setPdfFile(null);
    setPageCount(0);
    setPages([]);
    setPagesToDelete(new Set());
    setRangeInput('');
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  const survivingCount = pageCount - pagesToDelete.size;
  const isAllSelected = pageCount > 0 && pagesToDelete.size === pageCount;

  return (
    <ToolPageShell
      toolId="remove-pages"
      badgeTag="Client-Side PDF"
      title="Remove Pages from PDF Online Free - Delete PDF Pages"
      subtitle="Select unwanted pages or specify custom ranges to purge sheets from your PDF in seconds. 100% private in-browser execution with zero server uploads."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['split-pdf', 'merge-pdf', 'rotate-pdf', 'watermark-pdf']}
    >
      <div className="w-full max-w-4xl mx-auto space-y-6">
        
        {/* ── File Upload / Dropzone ── */}
        {!pdfFile ? (
          <div
            onDragOver={handleDragOver}
            onDragLeave={handleDragLeave}
            onDrop={handleDrop}
            onClick={() => fileInputRef.current?.click()}
            className={`group relative rounded-3xl border-2 border-dashed p-10 sm:p-14 text-center cursor-pointer transition-all duration-200 ${
              isDraggingOver
                ? 'border-zinc-900 bg-zinc-100/80 scale-[0.99]'
                : 'border-zinc-200 bg-white hover:border-zinc-400 hover:bg-zinc-50/50'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept="application/pdf"
              className="hidden"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleFileLoad(e.target.files[0]);
                }
              }}
            />

            <div className="flex flex-col items-center justify-center space-y-4">
              <div className="w-16 h-16 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900 group-hover:scale-105 transition-transform duration-200">
                {isLoading ? (
                  <RefreshCw className="w-7 h-7 stroke-[1.8] animate-spin text-zinc-600" />
                ) : (
                  <UploadCloud className="w-7 h-7 stroke-[1.8] text-zinc-800" />
                )}
              </div>

              <div className="space-y-1.5 max-w-md">
                <h3 className="text-base sm:text-lg font-semibold text-zinc-900">
                  {isLoading ? 'Inspecting PDF Structure...' : 'Drop your PDF document here'}
                </h3>
                <p className="text-xs sm:text-sm text-zinc-500">
                  Select a document from your computer or phone. Pure client-side memory execution ensures complete confidentiality.
                </p>
              </div>

              <div className="pt-2">
                <span className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-900 text-white text-xs font-semibold shadow-sm group-hover:bg-zinc-800 transition-colors">
                  <FileText className="w-3.5 h-3.5 stroke-[1.8]" />
                  Browse PDF File
                </span>
              </div>

              <div className="flex items-center gap-4 text-[11px] font-mono text-zinc-400 pt-3">
                <span className="flex items-center gap-1.5">
                  <ShieldCheck className="w-3.5 h-3.5 text-emerald-600 stroke-[1.8]" />
                  Zero Server Uploads
                </span>
                <span>•</span>
                <span>Unlimited Pages</span>
                <span>•</span>
                <span>Lossless Vector Quality</span>
              </div>
            </div>
          </div>
        ) : (
          /* ── Interactive Page Removal Workspace ── */
          <div className="space-y-6">
            
            {/* Top Document Status Strip */}
            <div className="p-4 sm:p-5 rounded-2xl bg-white border border-zinc-200/90 shadow-xs flex flex-wrap items-center justify-between gap-4">
              <div className="flex items-center gap-3.5 min-w-0">
                <div className="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0 text-zinc-900">
                  <FileText className="w-5 h-5 stroke-[1.8]" />
                </div>
                <div className="min-w-0">
                  <h4 className="text-sm font-bold text-zinc-900 truncate max-w-xs sm:max-w-md">
                    {pdfFile.name}
                  </h4>
                  <p className="text-xs font-mono text-zinc-500 flex items-center gap-2 mt-0.5">
                    <span>{formatBytes(pdfFile.size)}</span>
                    <span>•</span>
                    <span className="text-zinc-900 font-semibold">{pageCount} total pages</span>
                  </p>
                </div>
              </div>

              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={resetAll}
                  className="px-3 py-1.5 rounded-lg border border-zinc-200 hover:bg-zinc-100 text-zinc-700 text-xs font-medium flex items-center gap-1.5 transition-colors cursor-pointer"
                >
                  <RotateCcw className="w-3.5 h-3.5 stroke-[1.8]" />
                  Replace File
                </button>
              </div>
            </div>

            {/* Range Input & Quick Filters */}
            <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/90 shadow-xs space-y-4">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                  <label className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-700 block">
                    Custom Page Range to Delete
                  </label>
                  <p className="text-xs text-zinc-500 mt-0.5">
                    Type page numbers or ranges separated by commas (e.g. 2, 4-6, 8)
                  </p>
                </div>

                {/* Telemetry Counter Pill */}
                <div className="flex items-center gap-2 text-xs font-mono shrink-0">
                  <span className="px-2.5 py-1 rounded-md bg-zinc-100 text-zinc-600 border border-zinc-200">
                    Total: <strong className="text-zinc-900">{pageCount}</strong>
                  </span>
                  <span className="px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                    Deleting: <strong className="text-rose-900">{pagesToDelete.size}</strong>
                  </span>
                  <span className="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Remaining: <strong className="text-emerald-900">{survivingCount}</strong>
                  </span>
                </div>
              </div>

              <div className="relative">
                <input
                  type="text"
                  value={rangeInput}
                  onChange={handleRangeInputChange}
                  placeholder="e.g. 2, 4-6, 8"
                  className="w-full pl-3.5 pr-24 py-2.5 rounded-xl border border-zinc-300 bg-white text-sm font-mono text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950 transition-all"
                />
                {rangeInput && (
                  <button
                    type="button"
                    onClick={clearSelection}
                    className="absolute right-2.5 top-1/2 -translate-y-1/2 px-2 py-1 rounded-md bg-zinc-100 hover:bg-zinc-200 text-[11px] font-mono text-zinc-600 transition-colors"
                  >
                    Clear
                  </button>
                )}
              </div>

              {/* Quick Preset Selector Buttons */}
              <div className="flex flex-wrap items-center gap-2 pt-1 border-t border-zinc-100">
                <span className="text-[11px] font-mono text-zinc-400 mr-1 flex items-center gap-1">
                  <SlidersHorizontal className="w-3 h-3 stroke-[1.8]" /> Quick Select:
                </span>
                <button
                  type="button"
                  onClick={selectEvenPages}
                  className="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
                >
                  Delete Even Pages
                </button>
                <button
                  type="button"
                  onClick={selectOddPages}
                  className="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
                >
                  Delete Odd Pages
                </button>
                <button
                  type="button"
                  onClick={invertSelection}
                  className="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
                >
                  Invert Selection
                </button>
                <button
                  type="button"
                  onClick={clearSelection}
                  className="px-2.5 py-1 rounded-lg text-xs font-medium border border-zinc-200 hover:bg-zinc-100 text-zinc-600 transition-colors cursor-pointer"
                >
                  Clear All Marks
                </button>
              </div>
            </div>

            {/* Interactive Page Chips Grid */}
            <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/90 shadow-xs space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <h4 className="text-sm font-bold text-zinc-900">
                    Interactive Page Sheet Selector
                  </h4>
                  <p className="text-xs text-zinc-500 mt-0.5">
                    Click any page thumbnail below to mark it for deletion
                  </p>
                </div>
                <div className="text-[11px] font-mono text-zinc-400">
                  {pagesToDelete.size} marked of {pageCount}
                </div>
              </div>

              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                {Array.from({ length: pageCount }, (_, i) => i + 1).map((pageNum) => {
                  const isMarkedForDelete = pagesToDelete.has(pageNum);
                  return (
                    <button
                      key={pageNum}
                      type="button"
                      onClick={() => togglePageDeletion(pageNum)}
                      className={`group relative p-3 rounded-2xl border text-left transition-all duration-150 flex flex-col justify-between h-32 select-none cursor-pointer ${
                        isMarkedForDelete
                          ? 'border-rose-500 bg-rose-50/60 ring-2 ring-rose-500/20 shadow-xs'
                          : 'border-zinc-200 bg-zinc-50/50 hover:border-zinc-350 hover:bg-white'
                      }`}
                    >
                      {/* Top Header: Page Number & Status */}
                      <div className="flex items-center justify-between w-full">
                        <span className={`text-[11px] font-mono font-bold ${
                          isMarkedForDelete ? 'text-rose-900 line-through' : 'text-zinc-800'
                        }`}>
                          Page {pageNum}
                        </span>
                        <div
                          className={`w-5 h-5 rounded-md flex items-center justify-center text-[10px] transition-colors ${
                            isMarkedForDelete
                              ? 'bg-rose-600 text-white'
                              : 'border border-zinc-300 text-transparent group-hover:border-zinc-400'
                          }`}
                        >
                          {isMarkedForDelete ? (
                            <Trash2 className="w-3 h-3 stroke-[2]" />
                          ) : (
                            <Check className="w-3 h-3 stroke-[2] text-zinc-300 group-hover:text-zinc-400" />
                          )}
                        </div>
                      </div>

                      {/* Mock Sheet Preview Visual */}
                      <div className="w-full space-y-1.5 px-1 py-2 opacity-60">
                        <div className={`h-1.5 rounded w-3/4 ${isMarkedForDelete ? 'bg-rose-200' : 'bg-zinc-300'}`} />
                        <div className={`h-1 rounded w-full ${isMarkedForDelete ? 'bg-rose-150' : 'bg-zinc-200'}`} />
                        <div className={`h-1 rounded w-5/6 ${isMarkedForDelete ? 'bg-rose-150' : 'bg-zinc-200'}`} />
                        <div className={`h-1 rounded w-2/3 ${isMarkedForDelete ? 'bg-rose-150' : 'bg-zinc-200'}`} />
                      </div>

                      {/* Bottom Status Pill */}
                      <div className="w-full pt-1 border-t border-zinc-200/60 flex items-center justify-between text-[10px] font-mono">
                        <span className={isMarkedForDelete ? 'text-rose-700 font-bold' : 'text-zinc-400'}>
                          {isMarkedForDelete ? 'DELETE' : 'KEEP'}
                        </span>
                        <span className="text-zinc-400">
                          #{pageNum}
                        </span>
                      </div>
                    </button>
                  );
                })}
              </div>
            </div>

            {/* Export Filename & Guardrails */}
            <div className="p-5 rounded-3xl bg-white border border-zinc-200/90 shadow-xs space-y-4">
              <div>
                <label className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-700 block mb-1.5">
                  Export Filename
                </label>
                <input
                  type="text"
                  value={outputFileName}
                  onChange={(e) => setOutputFileName(e.target.value)}
                  placeholder="trimmed-document.pdf"
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 bg-white text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950 shadow-2xs"
                />
              </div>

              {/* Warning if all pages marked */}
              {isAllSelected && (
                <div className="p-3.5 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-2.5 text-amber-900">
                  <AlertCircle className="w-4 h-4 text-amber-600 shrink-0 mt-0.5 stroke-[1.8]" />
                  <div className="text-xs">
                    <strong>Cannot delete all pages:</strong> At least one page must remain in the PDF. Please unmark at least one page to proceed.
                  </div>
                </div>
              )}

              {/* Download Action Button */}
              <button
                type="button"
                disabled={pagesToDelete.size === 0 || isAllSelected || isProcessing}
                onClick={handleRemovePages}
                className="w-full py-4 px-6 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-sm flex items-center justify-center gap-2.5 shadow-md active:scale-[0.99] disabled:opacity-45 disabled:pointer-events-none transition-all cursor-pointer"
              >
                {isProcessing ? (
                  <>
                    <RefreshCw className="w-4 h-4 stroke-[2] animate-spin" />
                    <span>Purging Pages & Compiling PDF...</span>
                  </>
                ) : (
                  <>
                    <Scissors className="w-4 h-4 stroke-[1.8]" />
                    <span>
                      {pagesToDelete.size === 0
                        ? 'Select Pages to Remove'
                        : `Remove ${pagesToDelete.size} ${pagesToDelete.size === 1 ? 'Page' : 'Pages'} & Download PDF`}
                    </span>
                    <Download className="w-4 h-4 stroke-[1.8] ml-1" />
                  </>
                )}
              </button>

              {pagesToDelete.size === 0 && (
                <p className="text-[11px] text-zinc-400 text-center">
                  Select one or more pages above or enter a custom range to enable download
                </p>
              )}
            </div>

          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
