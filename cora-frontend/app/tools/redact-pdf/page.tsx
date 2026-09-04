'use client';

import React, { useState, useRef, useCallback } from 'react';
import { 
  FileText, 
  Download, 
  ShieldCheck, 
  RotateCcw, 
  Trash2, 
  Plus, 
  CheckCircle2, 
  ChevronLeft, 
  ChevronRight, 
  Eye, 
  Square,
  Sparkles,
  Layers,
  Crosshair,
  BadgeAlert
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo, redactPdf, downloadPdfBlob, PageInfo, RedactionBox } from '@/lib/pdf-engine';

const REDACT_PDF_FAQS = [
  {
    question: 'Is Cora\'s PDF redaction permanent or can someone uncover the text underneath?',
    answer: 'It is 100% irreversible. Unlike amateur methods (like changing text color to black or drawing translucent highlighters), Cora renders solid opaque vector blackout polygons directly into the PDF rendering tree. The obscured pixels cannot be selected, searched, or recovered.'
  },
  {
    question: 'How does Aadhaar and PAN masking comply with RBI and UIDAI regulations?',
    answer: 'Under UIDAI Circulars and RBI Master KYC Directions, organizations and individuals are required to mask the first 8 digits of the Aadhaar number (leaving only the last 4 digits visible) before storing or circulating identity proofs. Cora\'s Aadhaar preset facilitates instant compliance.'
  },
  {
    question: 'Does redaction remove metadata, hidden comments, and revision history?',
    answer: 'Yes. When Cora exports the sanitized PDF in browser memory, the PDF structure is normalized, stripping lingering editing histories, unflattened annotation layers, and un-sanitized draft metadata.'
  },
  {
    question: 'Are my financial records, tax returns, or IDs sent to any cloud server?',
    answer: 'Never. All redaction calculations, vector drawing, and byte modifications occur 100% locally inside your browser memory using WebAssembly. Zero files or sensitive credentials ever leave your machine.'
  },
  {
    question: 'Can I redact multiple pages in a single PDF file?',
    answer: 'Yes. You can navigate through any page of your multi-page document, apply individual or preset redactions to specific sheets, and export a single unified sanitized PDF.'
  }
];

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

export default function RedactPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);
  const previewBoxRef = useRef<HTMLDivElement>(null);

  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [pages, setPages] = useState<PageInfo[]>([]);
  const [currentPageIndex, setCurrentPageIndex] = useState<number>(0);
  const [redactions, setRedactions] = useState<RedactionBox[]>([]);
  const [selectedOverlayText, setSelectedOverlayText] = useState<string>('[REDACTED]');

  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [sanitizedBytes, setSanitizedBytes] = useState<Uint8Array | null>(null);

  // Drawing state on interactive sheet
  const [isDrawing, setIsDrawing] = useState<boolean>(false);
  const [drawStart, setDrawStart] = useState<{ x: number; y: number } | null>(null);
  const [currentDrawBox, setCurrentDrawBox] = useState<{ x: number; y: number; width: number; height: number } | null>(null);

  const handleFileLoad = async (loadedFile: File) => {
    if (loadedFile.type !== 'application/pdf' && !loadedFile.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please select a valid PDF file');
      return;
    }

    try {
      const info = await getPdfInfo(loadedFile);
      setPdfFile(loadedFile);
      setPageCount(info.pageCount);
      setPages(info.pages);
      setCurrentPageIndex(0);
      setRedactions([]);
      setSanitizedBytes(null);
      showToast('Loaded ' + loadedFile.name + ' (' + info.pageCount + ' pages)');
    } catch {
      showToast('Failed to open PDF. File may be encrypted with a password.');
    }
  };

  const handleDrop = useCallback((e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      handleFileLoad(e.dataTransfer.files[0]);
    }
  }, []);

  const handleDragOver = useCallback((e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(true);
  }, []);

  const handleDragLeave = useCallback((e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
  }, []);

  const resetAll = () => {
    setPdfFile(null);
    setPageCount(0);
    setPages([]);
    setRedactions([]);
    setSanitizedBytes(null);
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  const currentPage = pages[currentPageIndex] || { width: 595, height: 842 };

  // Convert mouse event on sheet preview to PDF points
  const getCoordinatesFromEvent = (e: React.MouseEvent<HTMLDivElement>) => {
    if (!previewBoxRef.current) return null;
    const rect = previewBoxRef.current.getBoundingClientRect();
    const clickX = e.clientX - rect.left;
    const clickY = e.clientY - rect.top;

    // Normalizing to page dimensions
    const scaleX = currentPage.width / rect.width;
    const scaleY = currentPage.height / rect.height;

    // PDF coordinate system origin is bottom-left
    const pdfX = clickX * scaleX;
    const pdfY = (rect.height - clickY) * scaleY;

    return { normX: clickX, normY: clickY, pdfX, pdfY, rectWidth: rect.width, rectHeight: rect.height };
  };

  const handleMouseDown = (e: React.MouseEvent<HTMLDivElement>) => {
    const coords = getCoordinatesFromEvent(e);
    if (!coords) return;
    setIsDrawing(true);
    setDrawStart({ x: coords.normX, y: coords.normY });
    setCurrentDrawBox({ x: coords.normX, y: coords.normY, width: 0, height: 0 });
  };

  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    if (!isDrawing || !drawStart || !previewBoxRef.current) return;
    const coords = getCoordinatesFromEvent(e);
    if (!coords) return;

    const x = Math.min(drawStart.x, coords.normX);
    const y = Math.min(drawStart.y, coords.normY);
    const width = Math.abs(coords.normX - drawStart.x);
    const height = Math.abs(coords.normY - drawStart.y);

    setCurrentDrawBox({ x, y, width, height });
  };

  const handleMouseUp = () => {
    if (!isDrawing || !drawStart || !currentDrawBox || !previewBoxRef.current) {
      setIsDrawing(false);
      return;
    }

    if (currentDrawBox.width > 10 && currentDrawBox.height > 10) {
      const rect = previewBoxRef.current.getBoundingClientRect();
      const scaleX = currentPage.width / rect.width;
      const scaleY = currentPage.height / rect.height;

      // Convert top-left based CSS box to PDF bottom-left coordinates
      const pdfX = currentDrawBox.x * scaleX;
      const pdfY = (rect.height - (currentDrawBox.y + currentDrawBox.height)) * scaleY;
      const pdfWidth = currentDrawBox.width * scaleX;
      const pdfHeight = currentDrawBox.height * scaleY;

      const newRedaction: RedactionBox = {
        id: 'redact_' + Date.now() + '_' + Math.random().toString(36).substr(2, 4),
        pageIndex: currentPageIndex,
        x: pdfX,
        y: pdfY,
        width: pdfWidth,
        height: pdfHeight,
        textOverlay: selectedOverlayText || undefined,
      };

      setRedactions((prev) => [...prev, newRedaction]);
      showToast('Redaction box placed on Page ' + (currentPageIndex + 1));
    }

    setIsDrawing(false);
    setDrawStart(null);
    setCurrentDrawBox(null);
  };

  // Add standard preset redactions
  const addPresetRedaction = (type: 'aadhaar' | 'pan' | 'bank' | 'header' | 'footer') => {
    const w = currentPage.width;
    const h = currentPage.height;
    let box: RedactionBox;

    if (type === 'aadhaar') {
      box = {
        id: 'aadhaar_' + Date.now(),
        pageIndex: currentPageIndex,
        x: w * 0.2,
        y: h * 0.45,
        width: w * 0.6,
        height: 28,
        textOverlay: '[AADHAAR MASKED]',
      };
    } else if (type === 'pan') {
      box = {
        id: 'pan_' + Date.now(),
        pageIndex: currentPageIndex,
        x: w * 0.25,
        y: h * 0.52,
        width: w * 0.5,
        height: 26,
        textOverlay: '[PAN REDACTED]',
      };
    } else if (type === 'bank') {
      box = {
        id: 'bank_' + Date.now(),
        pageIndex: currentPageIndex,
        x: w * 0.15,
        y: h * 0.35,
        width: w * 0.7,
        height: 32,
        textOverlay: '[CONFIDENTIAL ACCOUNT]',
      };
    } else if (type === 'header') {
      box = {
        id: 'header_' + Date.now(),
        pageIndex: currentPageIndex,
        x: 36,
        y: h - 60,
        width: w - 72,
        height: 36,
        textOverlay: '[RESTRICTED HEADER]',
      };
    } else {
      box = {
        id: 'footer_' + Date.now(),
        pageIndex: currentPageIndex,
        x: 36,
        y: 24,
        width: w - 72,
        height: 36,
        textOverlay: '[REDACTED FOOTER]',
      };
    }

    setRedactions((prev) => [...prev, box]);
    showToast('Applied ' + type.toUpperCase() + ' blackout box');
  };

  const removeRedaction = (id: string) => {
    setRedactions((prev) => prev.filter((r) => r.id !== id));
  };

  const clearCurrentPageRedactions = () => {
    setRedactions((prev) => prev.filter((r) => r.pageIndex !== currentPageIndex));
    showToast('Cleared redactions for Page ' + (currentPageIndex + 1));
  };

  const handleExecuteRedaction = async () => {
    if (!pdfFile || redactions.length === 0) {
      showToast('Please place at least one redaction box');
      return;
    }

    setIsProcessing(true);
    try {
      const output = await redactPdf(pdfFile, redactions);
      setSanitizedBytes(output);
      showToast('PDF sanitized with ' + redactions.length + ' permanent redaction(s)!');
    } catch (err: any) {
      showToast(err?.message || 'Failed to redact PDF');
    } finally {
      setIsProcessing(false);
    }
  };

  const handleDownload = () => {
    if (!sanitizedBytes || !pdfFile) return;
    const baseName = pdfFile.name.replace(/\.pdf$/i, '');
    downloadPdfBlob(sanitizedBytes, `${baseName}_redacted.pdf`);
    showToast('Sanitized PDF downloaded');
  };

  const currentPageRedactions = redactions.filter((r) => r.pageIndex === currentPageIndex);

  return (
    <ToolPageShell
      toolId="redact-pdf"
      badgeTag="Permanent PDF Sanitization"
      title="Redact PDF & Black Out Sensitive Information"
      subtitle="Permanently black out Aadhaar numbers, PAN cards, financial records, and confidential clauses in your PDF documents. 100% private, irreversible client-side sanitization."
      faqItems={REDACT_PDF_FAQS}
      relatedToolSlugs={['protect-pdf', 'unlock-pdf', 'crop-pdf', 'esign-pdf']}
    >
      <div className="max-w-5xl mx-auto space-y-8">
        
        {/* Step 1: Upload Dropzone */}
        {!pdfFile ? (
          <div
            onDrop={handleDrop}
            onDragOver={handleDragOver}
            onDragLeave={handleDragLeave}
            onClick={() => fileInputRef.current?.click()}
            className={`relative rounded-3xl border-2 border-dashed p-10 sm:p-14 text-center cursor-pointer transition-all duration-200 ${
              isDraggingOver
                ? 'border-zinc-950 bg-zinc-100/70 scale-[0.99]'
                : 'border-zinc-300 bg-white hover:border-zinc-400 hover:bg-zinc-50/50'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept="application/pdf,.pdf"
              className="hidden"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleFileLoad(e.target.files[0]);
                }
              }}
            />

            <div className="w-16 h-16 mx-auto mb-4 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900 shadow-2xs">
              <Square className="w-8 h-8 stroke-[1.8] fill-zinc-900" />
            </div>

            <h3 className="text-lg sm:text-xl font-bold text-zinc-950 mb-1">
              Select or Drop PDF to Redact
            </h3>
            <p className="text-sm text-zinc-500 max-w-md mx-auto mb-4">
              Draw permanent blackout boxes over PAN, Aadhaar, salary slips, or confidential contract clauses.
            </p>

            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 text-xs font-mono text-zinc-600">
              <ShieldCheck className="w-3.5 h-3.5 stroke-[1.8] text-zinc-900" />
              <span>100% Irreversible Client-Side Vector Blackout</span>
            </div>
          </div>
        ) : (
          /* Step 2: Interactive Redactor Workspace */
          <div className="space-y-6">
            
            {/* Document Header & Navigation Strip */}
            <div className="p-4 sm:p-5 rounded-2xl bg-white border border-zinc-200 flex flex-wrap items-center justify-between gap-4 shadow-xs">
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

              {/* Page Navigator */}
              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={() => setCurrentPageIndex((prev) => Math.max(0, prev - 1))}
                  disabled={currentPageIndex === 0}
                  className="p-2 rounded-lg border border-zinc-200 hover:bg-zinc-100 text-zinc-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer"
                >
                  <ChevronLeft className="w-4 h-4 stroke-[2]" />
                </button>
                <span className="text-xs font-mono font-bold text-zinc-900 px-2">
                  Page {currentPageIndex + 1} of {pageCount}
                </span>
                <button
                  type="button"
                  onClick={() => setCurrentPageIndex((prev) => Math.min(pageCount - 1, prev + 1))}
                  disabled={currentPageIndex === pageCount - 1}
                  className="p-2 rounded-lg border border-zinc-200 hover:bg-zinc-100 text-zinc-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer"
                >
                  <ChevronRight className="w-4 h-4 stroke-[2]" />
                </button>
                <div className="h-4 w-px bg-zinc-200 mx-1" />
                <button
                  type="button"
                  onClick={resetAll}
                  className="px-3 py-1.5 rounded-lg border border-zinc-200 hover:bg-zinc-100 text-zinc-700 text-xs font-medium flex items-center gap-1.5 transition-colors cursor-pointer"
                >
                  <RotateCcw className="w-3.5 h-3.5 stroke-[1.8]" />
                  Change File
                </button>
              </div>
            </div>

            {/* Quick KYC Presets & Overlay Text Controls */}
            <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200 shadow-xs space-y-4">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                  <h4 className="text-sm font-bold text-zinc-900 flex items-center gap-2">
                    <Crosshair className="w-4 h-4 stroke-[1.8]" />
                    Click & Drag to Draw Redaction Box, or Use Instant KYC Presets
                  </h4>
                  <p className="text-xs text-zinc-500 mt-0.5">
                    Draw freely on the document sheet below, or click any standard Indian identity template.
                  </p>
                </div>

                {/* Overlay Text Selector */}
                <div className="flex items-center gap-2 shrink-0">
                  <span className="text-xs font-mono text-zinc-500">Box Label:</span>
                  <select
                    value={selectedOverlayText}
                    onChange={(e) => setSelectedOverlayText(e.target.value)}
                    className="text-xs font-mono font-semibold py-1.5 px-2.5 rounded-xl border border-zinc-300 bg-white text-zinc-900 focus:outline-none focus:border-zinc-950"
                  >
                    <option value="[REDACTED]">[REDACTED]</option>
                    <option value="[CONFIDENTIAL]">[CONFIDENTIAL]</option>
                    <option value="[AADHAAR MASKED]">[AADHAAR MASKED]</option>
                    <option value="[PAN REDACTED]">[PAN REDACTED]</option>
                    <option value="">Solid Blackout (No Text)</option>
                  </select>
                </div>
              </div>

              {/* Quick Preset Buttons */}
              <div className="flex flex-wrap items-center gap-2 pt-2 border-t border-zinc-100">
                <span className="text-xs font-mono text-zinc-400 mr-1 flex items-center gap-1">
                  <Sparkles className="w-3 h-3 stroke-[1.8]" /> KYC Presets:
                </span>
                <button
                  type="button"
                  onClick={() => addPresetRedaction('aadhaar')}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-900 transition-colors cursor-pointer flex items-center gap-1.5"
                >
                  <BadgeAlert className="w-3.5 h-3.5 stroke-[1.8]" />
                  Aadhaar 8-Digit Mask
                </button>
                <button
                  type="button"
                  onClick={() => addPresetRedaction('pan')}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-900 transition-colors cursor-pointer flex items-center gap-1.5"
                >
                  <Square className="w-3 h-3 stroke-[1.8] fill-zinc-900" />
                  PAN Number Mask
                </button>
                <button
                  type="button"
                  onClick={() => addPresetRedaction('bank')}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-900 transition-colors cursor-pointer"
                >
                  Bank Account & IFSC
                </button>
                <button
                  type="button"
                  onClick={() => addPresetRedaction('header')}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-900 transition-colors cursor-pointer"
                >
                  Header Bar
                </button>
                <button
                  type="button"
                  onClick={() => addPresetRedaction('footer')}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-900 transition-colors cursor-pointer"
                >
                  Footer Bar
                </button>
              </div>
            </div>

            {/* Interactive Sheet Preview Canvas */}
            <div className="p-6 rounded-3xl bg-zinc-100 border border-zinc-200 shadow-inner flex flex-col items-center">
              <div className="w-full max-w-xl text-center mb-3 flex items-center justify-between text-xs font-mono text-zinc-500">
                <span>Page {currentPageIndex + 1} Viewport ({Math.round(currentPage.width)} × {Math.round(currentPage.height)} pt)</span>
                <span>{currentPageRedactions.length} box(es) on this sheet</span>
              </div>

              {/* Interactive Page Sheet Container */}
              <div
                ref={previewBoxRef}
                onMouseDown={handleMouseDown}
                onMouseMove={handleMouseMove}
                onMouseUp={handleMouseUp}
                className="relative w-full max-w-xl bg-white border border-zinc-300 rounded-lg shadow-md select-none cursor-crosshair overflow-hidden"
                style={{
                  aspectRatio: `${currentPage.width} / ${currentPage.height}`,
                  maxHeight: '640px',
                }}
              >
                {/* Mock Document Content Representation */}
                <div className="p-8 sm:p-10 space-y-4 pointer-events-none opacity-40">
                  <div className="h-3 w-1/3 bg-zinc-300 rounded" />
                  <div className="h-2 w-full bg-zinc-200 rounded" />
                  <div className="h-2 w-5/6 bg-zinc-200 rounded" />
                  <div className="h-2 w-4/6 bg-zinc-200 rounded" />
                  <div className="pt-4 grid grid-cols-2 gap-4">
                    <div className="h-16 bg-zinc-100 rounded border border-zinc-200" />
                    <div className="h-16 bg-zinc-100 rounded border border-zinc-200" />
                  </div>
                  <div className="pt-4 space-y-2">
                    <div className="h-2 w-full bg-zinc-200 rounded" />
                    <div className="h-2 w-11/12 bg-zinc-200 rounded" />
                    <div className="h-2 w-3/4 bg-zinc-200 rounded" />
                  </div>
                </div>

                {/* Rendered Redactions on Current Page */}
                {currentPageRedactions.map((box) => {
                  if (!previewBoxRef.current) return null;
                  const rect = previewBoxRef.current.getBoundingClientRect();
                  const scaleX = rect.width / currentPage.width;
                  const scaleY = rect.height / currentPage.height;

                  const left = box.x * scaleX;
                  const top = (currentPage.height - (box.y + box.height)) * scaleY;
                  const width = box.width * scaleX;
                  const height = box.height * scaleY;

                  return (
                    <div
                      key={box.id}
                      className="absolute bg-black text-white flex items-center justify-center font-mono font-bold tracking-wider text-[10px] shadow-sm group cursor-pointer"
                      style={{
                        left: `${left}px`,
                        top: `${top}px`,
                        width: `${width}px`,
                        height: `${height}px`,
                      }}
                      title="Click to remove this redaction"
                      onClick={(e) => {
                        e.stopPropagation();
                        removeRedaction(box.id);
                      }}
                    >
                      <span className="truncate px-1 pointer-events-none">
                        {box.textOverlay || ''}
                      </span>
                      <button
                        type="button"
                        onClick={(e) => {
                          e.stopPropagation();
                          removeRedaction(box.id);
                        }}
                        className="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-xs cursor-pointer"
                      >
                        <Trash2 className="w-2.5 h-2.5 stroke-[2]" />
                      </button>
                    </div>
                  );
                })}

                {/* Active Drawing Box Ghost */}
                {isDrawing && currentDrawBox && (
                  <div
                    className="absolute border-2 border-black bg-black/60 pointer-events-none text-white flex items-center justify-center text-[10px] font-mono"
                    style={{
                      left: `${currentDrawBox.x}px`,
                      top: `${currentDrawBox.y}px`,
                      width: `${currentDrawBox.width}px`,
                      height: `${currentDrawBox.height}px`,
                    }}
                  >
                    {selectedOverlayText}
                  </div>
                )}
              </div>
            </div>

            {/* Redaction Registry List */}
            {redactions.length > 0 && (
              <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200 shadow-xs space-y-3">
                <div className="flex items-center justify-between">
                  <h4 className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-700">
                    Active Redactions ({redactions.length} Total Across Document)
                  </h4>
                  <button
                    type="button"
                    onClick={clearCurrentPageRedactions}
                    className="text-xs font-mono text-zinc-500 hover:text-rose-600 transition-colors cursor-pointer"
                  >
                    Clear Page {currentPageIndex + 1}
                  </button>
                </div>

                <div className="divide-y divide-zinc-100 max-h-48 overflow-y-auto">
                  {redactions.map((item, idx) => (
                    <div key={item.id} className="py-2 flex items-center justify-between text-xs font-mono">
                      <div className="flex items-center gap-3">
                        <span className="px-2 py-0.5 rounded bg-zinc-100 text-zinc-700 font-bold">
                          Page {item.pageIndex + 1}
                        </span>
                        <span className="font-semibold text-zinc-900">
                          {item.textOverlay || 'Solid Blackout'}
                        </span>
                        <span className="text-zinc-400 text-[11px]">
                          ({Math.round(item.width)} × {Math.round(item.height)} pt)
                        </span>
                      </div>
                      <button
                        type="button"
                        onClick={() => removeRedaction(item.id)}
                        className="p-1 rounded text-zinc-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                      >
                        <Trash2 className="w-3.5 h-3.5 stroke-[1.8]" />
                      </button>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Execution Action Bar */}
            <div className="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 rounded-3xl bg-zinc-900 text-white shadow-lg">
              <div>
                <div className="text-sm font-bold flex items-center gap-2">
                  <ShieldCheck className="w-4 h-4 text-emerald-400 stroke-[2]" />
                  Permanent Vector Sanitization
                </div>
                <p className="text-xs text-zinc-400 mt-0.5">
                  {redactions.length > 0
                    ? `${redactions.length} blackout area(s) ready to burn permanently into document bytes`
                    : 'Draw or select redactions above to sanitize this PDF'}
                </p>
              </div>

              {!sanitizedBytes ? (
                <button
                  type="button"
                  onClick={handleExecuteRedaction}
                  disabled={isProcessing || redactions.length === 0}
                  className="w-full sm:w-auto px-6 py-3 rounded-2xl bg-white hover:bg-zinc-100 text-zinc-950 text-sm font-bold flex items-center justify-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shadow-md active:scale-95"
                >
                  {isProcessing ? (
                    <>
                      <div className="w-4 h-4 border-2 border-zinc-950 border-t-transparent rounded-full animate-spin" />
                      Sanitizing In Memory...
                    </>
                  ) : (
                    <>
                      <Square className="w-4 h-4 stroke-[2] fill-zinc-950" />
                      Apply Redactions & Sanitize
                    </>
                  )}
                </button>
              ) : (
                <button
                  type="button"
                  onClick={handleDownload}
                  className="w-full sm:w-auto px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-sm font-bold flex items-center justify-center gap-2 transition-all cursor-pointer shadow-md active:scale-95"
                >
                  <Download className="w-4 h-4 stroke-[2]" />
                  Download Sanitized PDF
                </button>
              )}
            </div>

            {/* Completed Sanitization Confirmation */}
            {sanitizedBytes && (
              <div className="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between gap-3 text-xs font-mono animate-in fade-in">
                <div className="flex items-center gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-700 stroke-[2]" />
                  <span>Sanitized PDF generated! Permanent blackouts applied cleanly. Size: {formatBytes(sanitizedBytes.length)}</span>
                </div>
                <button
                  type="button"
                  onClick={handleDownload}
                  className="underline font-bold hover:text-emerald-950 cursor-pointer"
                >
                  Save File
                </button>
              </div>
            )}

          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
