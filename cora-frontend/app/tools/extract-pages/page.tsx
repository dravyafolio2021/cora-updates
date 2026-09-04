'use client';

import React, { useState, useRef, useMemo, useEffect } from 'react';
import { 
  Scissors, 
  UploadCloud, 
  FileText, 
  Download, 
  RefreshCw, 
  ShieldCheck, 
  Check, 
  Layers, 
  SlidersHorizontal, 
  ChevronRight, 
  Sparkles,
  ArrowRight,
  FileCheck
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

// Convert 1-based page numbers set into formatted string (e.g. [1,2,3,5] -> "1-3, 5")
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

// Parse human input "1, 3-5, 8" into 1-based page numbers set
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
    question: 'How does Cora extract pages from PDF without uploading my document?',
    answer: 'Cora processes PDF files entirely in local browser memory using client-side JavaScript. Only the specific page objects and xref indices you select are extracted into a new PDF stream. No document data is ever transmitted to a server.',
  },
  {
    question: 'Will page extraction lower text resolution or compress embedded photos?',
    answer: 'No. Cora extracts the raw PDF page streams losslessly. Embedded high-res images, vector typography, digital signatures, and metadata retain their exact original quality.',
  },
  {
    question: 'Can I select non-sequential pages and custom ranges together?',
    answer: 'Yes. You can click individual page chips or type mixed ranges like "1, 4-7, 12". The visual page selector and the range text input stay synchronized in real-time.',
  },
  {
    question: 'What is the maximum page count or file size Cora can process?',
    answer: 'There are zero artificial file size caps, page count restrictions, or paywalls. Performance depends solely on your device memory.',
  },
  {
    question: 'Can I extract pages from password-protected encrypted PDFs?',
    answer: 'If the PDF is standardly accessible or already unlocked, Cora can extract the pages directly. If protected by an owner AES password, you can unlock it first with Cora Unlock PDF.',
  },
];

export default function ExtractPagesPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [pages, setPages] = useState<PageInfo[]>([]);
  const [selectedPages, setSelectedPages] = useState<Set<number>>(new Set()); // 1-based page numbers
  const [rangeInput, setRangeInput] = useState<string>('');
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);

  const handleFileSelect = async (file: File) => {
    if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
      showToast('Please select a valid PDF file.');
      return;
    }

    setPdfFile(file);
    setIsProcessing(true);

    try {
      const info = await getPdfInfo(file);
      setPageCount(info.pageCount);
      setPages(info.pages);
      // Default: select first page or all pages
      const initial = new Set<number>([1]);
      setSelectedPages(initial);
      setRangeInput('1');
      showToast(`Loaded ${file.name} (${info.pageCount} page${info.pageCount > 1 ? 's' : ''}).`);
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Error reading PDF file';
      showToast(`PDF error: ${msg}`);
      setPdfFile(null);
    } finally {
      setIsProcessing(false);
    }
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDraggingOver(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFileSelect(e.dataTransfer.files[0]);
    }
  };

  const togglePageSelection = (pageNumber: number) => {
    const updated = new Set(selectedPages);
    if (updated.has(pageNumber)) {
      updated.delete(pageNumber);
    } else {
      updated.add(pageNumber);
    }
    setSelectedPages(updated);
    setRangeInput(formatPageRanges(Array.from(updated)));
  };

  const handleRangeInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const val = e.target.value;
    setRangeInput(val);
    const parsed = parsePageRangeInput(val, pageCount);
    setSelectedPages(parsed);
  };

  const selectAll = () => {
    const all = new Set<number>();
    for (let i = 1; i <= pageCount; i++) all.add(i);
    setSelectedPages(all);
    setRangeInput(formatPageRanges(Array.from(all)));
  };

  const deselectAll = () => {
    setSelectedPages(new Set());
    setRangeInput('');
  };

  const selectOdd = () => {
    const odds = new Set<number>();
    for (let i = 1; i <= pageCount; i += 2) odds.add(i);
    setSelectedPages(odds);
    setRangeInput(formatPageRanges(Array.from(odds)));
  };

  const selectEven = () => {
    const evens = new Set<number>();
    for (let i = 2; i <= pageCount; i += 2) evens.add(i);
    setSelectedPages(evens);
    setRangeInput(formatPageRanges(Array.from(evens)));
  };

  const handleExtractPages = async () => {
    if (!pdfFile || selectedPages.size === 0) {
      showToast('Please select at least one page to extract.');
      return;
    }

    setIsProcessing(true);
    try {
      // 0-based indices sorted
      const zeroBasedIndices = Array.from(selectedPages)
        .map((p) => p - 1)
        .sort((a, b) => a - b);

      const extractedBytes = await extractPdfPages(pdfFile, zeroBasedIndices);
      const baseName = pdfFile.name.replace(/\.pdf$/i, '');
      const rangeLabel = formatPageRanges(Array.from(selectedPages)).replace(/[^0-9,-]/g, '_');
      downloadPdfBlob(extractedBytes, `${baseName}-extracted-[p${rangeLabel}].pdf`);
      showToast(`Successfully extracted ${selectedPages.size} page(s)!`);
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Failed to extract pages';
      showToast(`Extraction error: ${msg}`);
    } finally {
      setIsProcessing(false);
    }
  };

  const handleReset = () => {
    setPdfFile(null);
    setPageCount(0);
    setPages([]);
    setSelectedPages(new Set());
    setRangeInput('');
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  return (
    <ToolPageShell
      toolId="extract-pages"
      badgeTag="Page Isolation"
      title="Extract PDF Pages Online Free"
      subtitle="Select and isolate specific pages or page ranges from your PDF into a clean standalone document. 100% private in-browser engine."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['split-pdf', 'remove-pages', 'merge-pdf', 'rotate-pdf']}
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
              <Scissors className="w-8 h-8" />
            </div>

            <h3 className="text-xl font-semibold text-zinc-900 mb-2">
              Drop PDF to extract pages
            </h3>
            <p className="text-sm text-zinc-500 max-w-md mx-auto mb-6">
              Pick individual sheets or enter custom spans. Runs 100% in your browser memory without server uploads.
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
                <Sparkles className="w-4 h-4 text-zinc-600" />
                Lossless Vector Quality
              </span>
              <span className="inline-flex items-center gap-1.5">
                <Layers className="w-4 h-4 text-zinc-600" />
                Multi-Range Selection
              </span>
            </div>
          </div>
        ) : (
          <div className="bg-white border border-zinc-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            
            {/* Header Document & Reset Bar */}
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
                    <span>{pageCount} total page{pageCount > 1 ? 's' : ''}</span>
                    <span>•</span>
                    <span className="text-zinc-900 font-medium">{selectedPages.size} selected for extraction</span>
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

                <button
                  type="button"
                  onClick={handleExtractPages}
                  disabled={isProcessing || selectedPages.size === 0}
                  className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold shadow-sm transition-all disabled:opacity-50"
                >
                  {isProcessing ? (
                    <>
                      <RefreshCw className="w-3.5 h-3.5 animate-spin" />
                      <span>Extracting Pages...</span>
                    </>
                  ) : (
                    <>
                      <Scissors className="w-3.5 h-3.5" />
                      <span>Extract ({selectedPages.size}) Page{selectedPages.size !== 1 ? 's' : ''}</span>
                    </>
                  )}
                </button>
              </div>
            </div>

            {/* Quick Presets & Range Query Input */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-zinc-50 border border-zinc-200/80">
              {/* Range Input */}
              <div className="flex-1 min-w-[200px]">
                <label className="block text-[11px] font-medium text-zinc-500 mb-1">
                  Page Range Query (e.g. 1-3, 5, 7-9)
                </label>
                <input
                  type="text"
                  value={rangeInput}
                  onChange={handleRangeInputChange}
                  placeholder={`e.g. 1-${Math.min(3, pageCount)}`}
                  className="w-full px-3 py-1.5 text-xs bg-white border border-zinc-200 rounded-lg focus:outline-none focus:border-zinc-900 text-zinc-900 font-mono"
                />
              </div>

              {/* Quick Select Buttons */}
              <div className="flex flex-wrap items-center gap-1.5 self-end sm:self-center">
                <button
                  type="button"
                  onClick={selectAll}
                  className="px-2.5 py-1 text-xs font-medium bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700 rounded-lg transition-colors"
                >
                  All
                </button>
                <button
                  type="button"
                  onClick={deselectAll}
                  className="px-2.5 py-1 text-xs font-medium bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700 rounded-lg transition-colors"
                >
                  Clear
                </button>
                <button
                  type="button"
                  onClick={selectOdd}
                  className="px-2.5 py-1 text-xs font-medium bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700 rounded-lg transition-colors"
                >
                  Odd
                </button>
                <button
                  type="button"
                  onClick={selectEven}
                  className="px-2.5 py-1 text-xs font-medium bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700 rounded-lg transition-colors"
                >
                  Even
                </button>
              </div>
            </div>

            {/* Interactive Page Chips Grid */}
            <div className="space-y-3">
              <div className="flex items-center justify-between text-xs text-zinc-500">
                <span>Click pages to toggle extraction:</span>
                <span className="font-mono">{selectedPages.size} / {pageCount} pages selected</span>
              </div>

              <div className="grid grid-cols-3 sm:grid-cols-6 md:grid-cols-8 gap-2.5 max-h-96 overflow-y-auto p-1">
                {Array.from({ length: pageCount }, (_, i) => i + 1).map((pageNum) => {
                  const isSelected = selectedPages.has(pageNum);
                  return (
                    <button
                      key={pageNum}
                      type="button"
                      onClick={() => togglePageSelection(pageNum)}
                      className={`relative flex flex-col items-center justify-center p-3 rounded-xl border transition-all duration-150 aspect-[3/4] ${
                        isSelected
                          ? 'border-zinc-900 bg-zinc-900 text-white shadow-sm'
                          : 'border-zinc-200 bg-zinc-50/50 hover:bg-zinc-100/70 text-zinc-600'
                      }`}
                    >
                      <div className="text-xs font-bold font-mono">{pageNum}</div>
                      <div className="text-[10px] opacity-70 mt-1">Page</div>
                      
                      {isSelected && (
                        <div className="absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-white text-zinc-900 flex items-center justify-center text-[10px]">
                          <Check className="w-2.5 h-2.5 stroke-[3]" />
                        </div>
                      )}
                    </button>
                  );
                })}
              </div>
            </div>

            {/* Bottom Extraction Action Bar */}
            <div className="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-zinc-100">
              <div className="text-xs text-zinc-500 flex items-center gap-1.5">
                <ShieldCheck className="w-4 h-4 text-emerald-600" />
                <span>Extracted losslessly directly in browser memory</span>
              </div>

              <button
                type="button"
                onClick={handleExtractPages}
                disabled={isProcessing || selectedPages.size === 0}
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-sm font-semibold shadow-sm transition-all disabled:opacity-50"
              >
                <Download className="w-4 h-4" />
                <span>Download Extracted PDF</span>
              </button>
            </div>
          </div>
        )}
      </div>
    </ToolPageShell>
  );
}
