'use client';

import React, { useState, useRef } from 'react';
import { 
  FileText, 
  RotateCw, 
  RotateCcw, 
  Download, 
  Check, 
  Sparkles, 
  ShieldCheck, 
  AlertCircle, 
  Trash2, 
  Layers, 
  Compass, 
  ArrowRight,
  Maximize2
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo, rotatePdfPages, downloadPdfBlob, PageInfo } from '@/lib/pdf-engine';

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
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

export default function RotatePdfPage() {
  const [file, setFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [pages, setPages] = useState<PageInfo[]>([]);
  const [rotationDegrees, setRotationDegrees] = useState<number>(90);
  const [targetScope, setTargetScope] = useState<'all' | 'custom'>('all');
  const [customRange, setCustomRange] = useState<string>('');
  const [outputFileName, setOutputFileName] = useState<string>('cora-rotated-document.pdf');
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
      setCustomRange(info.pageCount > 1 ? `1-${info.pageCount}` : '1');

      const baseName = loadedFile.name.replace(/\.[^/.]+$/, '');
      setOutputFileName(`${baseName}-rotated.pdf`);

      showToast(`Loaded ${loadedFile.name} (${info.pageCount} pages)`);
    } catch (err: any) {
      console.error(err);
      showToast('Failed to read PDF document. File may be encrypted.');
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

  const resetTool = () => {
    setFile(null);
    setPageCount(0);
    setPages([]);
    setRotationDegrees(90);
    setTargetScope('all');
    setCustomRange('');
  };

  // Rotation execution
  const handleRotate = async () => {
    if (!file) {
      showToast('Please upload a PDF file first');
      return;
    }

    let targetIndices: number[] | undefined = undefined;
    if (targetScope === 'custom') {
      const parsed = parsePageRangeInput(customRange, pageCount);
      if (parsed.length === 0) {
        showToast('Please enter at least 1 valid page number');
        return;
      }
      targetIndices = parsed;
    }

    setIsProcessing(true);
    try {
      const rotatedBytes = await rotatePdfPages(file, rotationDegrees, targetIndices);
      
      const fileName = outputFileName.trim().length > 0 
        ? (outputFileName.endsWith('.pdf') ? outputFileName : `${outputFileName}.pdf`)
        : 'cora-rotated-document.pdf';

      downloadPdfBlob(rotatedBytes, fileName);
      showToast(`Rotated ${targetIndices ? targetIndices.length : pageCount} pages by ${rotationDegrees}°!`);
    } catch (err: any) {
      console.error('Rotation error:', err);
      showToast(err?.message || 'Failed to rotate PDF. Please try again.');
    } finally {
      setIsProcessing(false);
    }
  };

  const rotateFaqs = [
    {
      question: 'Why do scanned contracts and blueprints frequently end up sideways?',
      answer: 'Mobile camera scanner apps and commercial document feeder scanners frequently record documents horizontally when fed along the wide edge. Cora corrects this orientation with zero quality degradation.'
    },
    {
      question: 'Can I rotate only specific architectural drawings or financial spreadsheets?',
      answer: 'Yes. Switch the scope from "All Pages" to "Specific Pages" and specify the page numbers or range (e.g. "3, 6-8"). Only the designated pages will be rotated while the rest remain in their original orientation.'
    },
    {
      question: 'Does rotating a PDF reduce font sharpness or OCR searchability?',
      answer: 'No. Cora applies native PDF transformation matrices to the page geometry. All embedded vector typography, interactive form fields, clickable links, and text layers remain 100% searchable and intact.'
    },
    {
      question: 'Is my document private and secure?',
      answer: 'Completely. All operations are executed locally within your browser using WebAssembly. No files are uploaded to any server or cloud infrastructure.'
    }
  ];

  return (
    <ToolPageShell
      toolId="rotate-pdf"
      badgeTag="Lossless Vector Rotation"
      title="Rotate PDF Pages Online"
      subtitle="Correct sideways scans, invert upside-down contracts, and align landscape blueprints. Fast, lossless, and 100% private in browser memory."
      faqItems={rotateFaqs}
    >
      <div className="space-y-6">
        {/* Hidden Input */}
        <input
          ref={fileInputRef}
          type="file"
          accept="application/pdf,.pdf"
          onChange={handleFileChange}
          className="hidden"
          id="cora-rotate-pdf-input"
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
                <RotateCw className="w-6 h-6 stroke-[1.75]" />
              </div>

              <div className="space-y-1">
                <h3 className="text-sm sm:text-base font-bold text-zinc-950 tracking-tight">
                  Drop your PDF here, or <span className="underline underline-offset-2 text-zinc-900">browse file</span>
                </h3>
                <p className="text-xs text-zinc-500 leading-relaxed">
                  Upload sideways scanned agreements, blueprints, or slides to reorient
                </p>
              </div>

              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 text-[11px] font-mono text-zinc-600 border border-zinc-200/60">
                <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" />
                <span>Zero server upload &bull; 100% private in memory</span>
              </div>
            </div>
          </div>
        ) : (
          /* ── Interactive Rotation Console ── */
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
                    {pageCount} {pageCount === 1 ? 'page' : 'pages'} &bull; {formatBytes(file.size)}
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

            {/* ── Interactive Rotation Angle Selector & Visual Preview ── */}
            <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
              {/* Left Angle Controls (7 Cols) */}
              <div className="md:col-span-7 space-y-4">
                <label className="text-xs font-bold text-zinc-900 block">
                  Select Rotation Angle
                </label>

                <div className="grid grid-cols-3 gap-2.5">
                  <button
                    type="button"
                    onClick={() => setRotationDegrees(90)}
                    className={`p-3.5 rounded-2xl border text-center transition-all cursor-pointer flex flex-col items-center gap-1.5 ${
                      rotationDegrees === 90
                        ? 'border-zinc-950 bg-zinc-950 text-white shadow-sm'
                        : 'border-zinc-200 bg-zinc-50/60 hover:bg-zinc-100 text-zinc-900'
                    }`}
                  >
                    <RotateCw className="w-5 h-5" />
                    <span className="text-xs font-bold font-mono">90° CW</span>
                    <span className="text-[10px] opacity-75">Right</span>
                  </button>

                  <button
                    type="button"
                    onClick={() => setRotationDegrees(180)}
                    className={`p-3.5 rounded-2xl border text-center transition-all cursor-pointer flex flex-col items-center gap-1.5 ${
                      rotationDegrees === 180
                        ? 'border-zinc-950 bg-zinc-950 text-white shadow-sm'
                        : 'border-zinc-200 bg-zinc-50/60 hover:bg-zinc-100 text-zinc-900'
                    }`}
                  >
                    <RotateCw className="w-5 h-5 rotate-90" />
                    <span className="text-xs font-bold font-mono">180°</span>
                    <span className="text-[10px] opacity-75">Invert</span>
                  </button>

                  <button
                    type="button"
                    onClick={() => setRotationDegrees(270)}
                    className={`p-3.5 rounded-2xl border text-center transition-all cursor-pointer flex flex-col items-center gap-1.5 ${
                      rotationDegrees === 270
                        ? 'border-zinc-950 bg-zinc-950 text-white shadow-sm'
                        : 'border-zinc-200 bg-zinc-50/60 hover:bg-zinc-100 text-zinc-900'
                    }`}
                  >
                    <RotateCcw className="w-5 h-5" />
                    <span className="text-xs font-bold font-mono">270° CCW</span>
                    <span className="text-[10px] opacity-75">Left</span>
                  </button>
                </div>

                {/* Scope Selection: All Pages vs Specific Pages */}
                <div className="pt-2 space-y-2.5">
                  <label className="text-xs font-bold text-zinc-900 block">
                    Apply Rotation To
                  </label>

                  <div className="flex items-center gap-2">
                    <button
                      type="button"
                      onClick={() => setTargetScope('all')}
                      className={`flex-1 py-2 px-3 rounded-xl border text-xs font-semibold transition-all cursor-pointer ${
                        targetScope === 'all'
                          ? 'border-zinc-950 bg-zinc-900 text-white shadow-xs'
                          : 'border-zinc-200 bg-zinc-50 text-zinc-700 hover:bg-zinc-100'
                      }`}
                    >
                      All Pages ({pageCount})
                    </button>

                    <button
                      type="button"
                      onClick={() => setTargetScope('custom')}
                      className={`flex-1 py-2 px-3 rounded-xl border text-xs font-semibold transition-all cursor-pointer ${
                        targetScope === 'custom'
                          ? 'border-zinc-950 bg-zinc-900 text-white shadow-xs'
                          : 'border-zinc-200 bg-zinc-50 text-zinc-700 hover:bg-zinc-100'
                      }`}
                    >
                      Specific Pages
                    </button>
                  </div>

                  {targetScope === 'custom' && (
                    <div className="pt-1 animate-in fade-in duration-150">
                      <label className="text-[11px] font-mono text-zinc-500 block mb-1">
                        Page Numbers or Range (e.g. 1-3, 5):
                      </label>
                      <input
                        type="text"
                        value={customRange}
                        onChange={(e) => setCustomRange(e.target.value)}
                        placeholder="e.g. 1, 3-5"
                        className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 bg-white text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950 shadow-2xs"
                      />
                    </div>
                  )}
                </div>
              </div>

              {/* Right Visual Preview Card (5 Cols) */}
              <div className="md:col-span-5 flex flex-col items-center justify-center p-6 rounded-2xl bg-zinc-50 border border-zinc-200/80 text-center">
                <span className="text-[11px] font-mono text-zinc-500 uppercase tracking-wider mb-4 font-semibold">
                  Orientation Preview
                </span>

                <div className="w-36 h-48 flex items-center justify-center">
                  <div
                    style={{ transform: `rotate(${rotationDegrees}deg)` }}
                    className="w-24 h-32 bg-white border-2 border-zinc-900 rounded-lg shadow-md p-2 flex flex-col justify-between transition-transform duration-500 ease-out"
                  >
                    <div className="flex items-center justify-between">
                      <div className="w-4 h-1 bg-zinc-900 rounded" />
                      <div className="w-2 h-2 rounded-full bg-zinc-300" />
                    </div>
                    <div className="space-y-1 my-auto">
                      <div className="h-1 bg-zinc-200 rounded w-full" />
                      <div className="h-1 bg-zinc-200 rounded w-4/5" />
                      <div className="h-1 bg-zinc-200 rounded w-3/5" />
                    </div>
                    <div className="text-[7px] font-mono text-zinc-400 text-center">
                      TOP
                    </div>
                  </div>
                </div>

                <div className="mt-4 font-mono text-xs font-bold text-zinc-900">
                  +{rotationDegrees}° Rotation Applied
                </div>
                <div className="text-[11px] text-zinc-500">
                  {targetScope === 'all' ? `Affecting all ${pageCount} pages` : `Affecting range: ${customRange || 'none'}`}
                </div>
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
                  placeholder="rotated-document.pdf"
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 bg-white text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950 shadow-2xs"
                />
              </div>
            </div>

            {/* ── Action Button ── */}
            <div className="pt-2">
              <button
                type="button"
                disabled={isProcessing}
                onClick={handleRotate}
                className="w-full py-3.5 px-5 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-sm flex items-center justify-center gap-2.5 shadow-md active:scale-[0.99] disabled:opacity-50 disabled:pointer-events-none transition-all cursor-pointer"
              >
                {isProcessing ? (
                  <>
                    <div className="w-4 h-4 rounded-full border-2 border-white/30 border-t-white animate-spin" />
                    <span>Rotating Document...</span>
                  </>
                ) : (
                  <>
                    <RotateCw className="w-4 h-4" />
                    <span>Rotate & Download PDF</span>
                  </>
                )}
              </button>
            </div>
          </div>
        )}
      </div>
    </ToolPageShell>
  );
}
