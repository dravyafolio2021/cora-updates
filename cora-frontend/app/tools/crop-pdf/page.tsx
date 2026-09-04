'use client';

import React, { useState, useRef, useCallback } from 'react';
import { 
  FileText, 
  Download, 
  ShieldCheck, 
  RotateCcw, 
  CheckCircle2, 
  Crop, 
  ChevronLeft, 
  ChevronRight, 
  Sliders, 
  Sparkles,
  Layers,
  ArrowUpDown,
  ArrowLeftRight
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo, cropPdf, downloadPdfBlob, PageInfo, CropMargins } from '@/lib/pdf-engine';

const CROP_PDF_FAQS = [
  {
    question: 'How does PDF cropping work under the hood?',
    answer: 'Cora adjusts the native PDF MediaBox and CropBox boundary coordinates per ISO 32000 standards. This instructs all PDF readers and printers to display only the viewport inside the cropped region while preserving internal vector structures.'
  },
  {
    question: 'Does cropping reduce resolution or blur vector typography?',
    answer: 'No. Cropping is 100% lossless. Because the document is not converted to raster images, all typography, vector paths, digital signatures, and high-resolution photography retain 100% of their original razor-sharp fidelity.'
  },
  {
    question: 'Can I apply the same crop margins uniformly across all pages?',
    answer: 'Yes. By default, Cora applies your margin values uniformly across every sheet in the document, which is ideal for removing scanner borders, white margins, or punch-hole edges from multi-page files.'
  },
  {
    question: 'Are my confidential blueprints or files stored on any cloud server?',
    answer: 'Never. All margin transformations occur locally in your browser memory using pure client-side WebAssembly. Zero document bytes or metadata are ever transmitted across the internet.'
  },
  {
    question: 'Will cropped PDFs print correctly on standard A4 or US Letter printers?',
    answer: 'Yes. Modern print drivers and readers (like Adobe Acrobat, macOS Preview, and Chrome) respect the updated CropBox dimensions, automatically fitting the cropped content cleanly to your printer\'s paper trays.'
  }
];

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

export default function CropPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [pages, setPages] = useState<PageInfo[]>([]);
  const [currentPageIndex, setCurrentPageIndex] = useState<number>(0);

  // Margin values in points (72 pt = 1 inch, ~28.35 pt = 1 cm)
  const [topMargin, setTopMargin] = useState<number>(36);
  const [bottomMargin, setBottomMargin] = useState<number>(36);
  const [leftMargin, setLeftMargin] = useState<number>(36);
  const [rightMargin, setRightMargin] = useState<number>(36);

  // Scope: 'all' or 'current'
  const [applyScope, setApplyScope] = useState<'all' | 'current'>('all');

  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [croppedBytes, setCroppedBytes] = useState<Uint8Array | null>(null);

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
      setCroppedBytes(null);
      showToast('Loaded ' + loadedFile.name + ' (' + info.pageCount + ' pages)');
    } catch {
      showToast('Unable to parse PDF. File may be encrypted.');
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
    setTopMargin(36);
    setBottomMargin(36);
    setLeftMargin(36);
    setRightMargin(36);
    setCroppedBytes(null);
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  const currentPage = pages[currentPageIndex] || { width: 595, height: 842 };

  // Margin quick presets
  const applyPreset = (t: number, b: number, l: number, r: number, name: string) => {
    setTopMargin(t);
    setBottomMargin(b);
    setLeftMargin(l);
    setRightMargin(r);
    showToast('Applied preset: ' + name);
  };

  const handleCrop = async () => {
    if (!pdfFile) return;

    // Safety checks: ensure margins don't exceed page dimensions
    if (topMargin + bottomMargin >= currentPage.height - 20) {
      showToast('Vertical margins exceed page height. Please reduce top/bottom margins.');
      return;
    }
    if (leftMargin + rightMargin >= currentPage.width - 20) {
      showToast('Horizontal margins exceed page width. Please reduce left/right margins.');
      return;
    }

    setIsProcessing(true);
    try {
      const margins: CropMargins = {
        top: topMargin,
        bottom: bottomMargin,
        left: leftMargin,
        right: rightMargin,
      };

      const targetIndices = applyScope === 'current' ? [currentPageIndex] : undefined;
      const output = await cropPdf(pdfFile, margins, targetIndices);
      setCroppedBytes(output);
      showToast('PDF cropped successfully across ' + (applyScope === 'all' ? 'all ' + pageCount + ' pages' : 'Page ' + (currentPageIndex + 1)));
    } catch (err: any) {
      showToast(err?.message || 'Failed to crop PDF');
    } finally {
      setIsProcessing(false);
    }
  };

  const handleDownload = () => {
    if (!croppedBytes || !pdfFile) return;
    const baseName = pdfFile.name.replace(/\.pdf$/i, '');
    downloadPdfBlob(croppedBytes, `${baseName}_cropped.pdf`);
    showToast('Cropped PDF downloaded');
  };

  // Preview box calculations
  const previewWidthPercent = Math.max(10, ((currentPage.width - leftMargin - rightMargin) / currentPage.width) * 100);
  const previewHeightPercent = Math.max(10, ((currentPage.height - topMargin - bottomMargin) / currentPage.height) * 100);
  const previewLeftPercent = (leftMargin / currentPage.width) * 100;
  const previewTopPercent = (topMargin / currentPage.height) * 100;

  return (
    <ToolPageShell
      toolId="crop-pdf"
      badgeTag="Precision PDF Trimmer"
      title="Crop PDF & Trim White Margins"
      subtitle="Trim printer borders, scanner margins, and white edges across all pages uniformly with precision slider controls. 100% private, lossless client-side PDF trimming."
      faqItems={CROP_PDF_FAQS}
      relatedToolSlugs={['redact-pdf', 'organize-pdf', 'compress-pdf', 'rotate-pdf']}
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
              <Crop className="w-8 h-8 stroke-[1.8]" />
            </div>

            <h3 className="text-lg sm:text-xl font-bold text-zinc-950 mb-1">
              Select or Drop PDF to Crop
            </h3>
            <p className="text-sm text-zinc-500 max-w-md mx-auto mb-4">
              Trim whitespace, remove messy scanner margins, and resize viewport dimensions across all pages.
            </p>

            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 text-xs font-mono text-zinc-600">
              <ShieldCheck className="w-3.5 h-3.5 stroke-[1.8] text-zinc-900" />
              <span>Lossless Native CropBox Geometry • 100% In-Browser Memory</span>
            </div>
          </div>
        ) : (
          /* Step 2: Interactive Cropper Workspace */
          <div className="space-y-6">
            
            {/* Top Status Bar */}
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
                  Preview: Page {currentPageIndex + 1} of {pageCount}
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

            {/* Quick Presets Strip */}
            <div className="p-4 sm:p-5 rounded-3xl bg-white border border-zinc-200 shadow-xs flex flex-wrap items-center justify-between gap-3">
              <div className="flex items-center gap-2 text-xs font-mono text-zinc-500">
                <Sparkles className="w-4 h-4 stroke-[1.8] text-zinc-900" />
                <span className="font-bold text-zinc-900">Margin Presets:</span>
              </div>
              <div className="flex flex-wrap items-center gap-2">
                <button
                  type="button"
                  onClick={() => applyPreset(36, 36, 36, 36, '0.5 Inch (36pt)')}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
                >
                  0.5&quot; All Sides
                </button>
                <button
                  type="button"
                  onClick={() => applyPreset(72, 72, 72, 72, '1.0 Inch (72pt)')}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
                >
                  1.0&quot; All Sides
                </button>
                <button
                  type="button"
                  onClick={() => applyPreset(54, 54, 18, 18, 'Header & Footer Trim')}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
                >
                  Header / Footer Trim
                </button>
                <button
                  type="button"
                  onClick={() => applyPreset(18, 18, 54, 18, 'Binder / Punch Margin')}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
                >
                  Left Binder Trim
                </button>
                <button
                  type="button"
                  onClick={() => applyPreset(0, 0, 0, 0, 'Zero Margins')}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium border border-zinc-200 hover:bg-zinc-100 text-zinc-600 transition-colors cursor-pointer"
                >
                  Reset to Zero
                </button>
              </div>
            </div>

            {/* Main Split: Interactive Visual Sheet Preview (Left) & Precision Margin Sliders (Right) */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
              
              {/* Left Column: Visual Crop Guide */}
              <div className="lg:col-span-6 p-6 rounded-3xl bg-zinc-100 border border-zinc-200 shadow-inner flex flex-col items-center justify-center">
                <div className="w-full max-w-sm text-center mb-3 text-xs font-mono text-zinc-500 flex items-center justify-between">
                  <span>Sheet: {Math.round(currentPage.width)} × {Math.round(currentPage.height)} pt</span>
                  <span className="font-bold text-zinc-900">
                    Cropped: {Math.round(currentPage.width - leftMargin - rightMargin)} × {Math.round(currentPage.height - topMargin - bottomMargin)} pt
                  </span>
                </div>

                {/* Simulated Sheet Box */}
                <div
                  className="relative w-full max-w-sm bg-white border border-zinc-300 rounded-lg shadow-md overflow-hidden select-none"
                  style={{
                    aspectRatio: `${currentPage.width} / ${currentPage.height}`,
                    maxHeight: '480px',
                  }}
                >
                  {/* Mock Page Content */}
                  <div className="p-6 space-y-3 opacity-30">
                    <div className="h-3 w-2/5 bg-zinc-400 rounded" />
                    <div className="h-2 w-full bg-zinc-300 rounded" />
                    <div className="h-2 w-5/6 bg-zinc-300 rounded" />
                    <div className="h-2 w-full bg-zinc-300 rounded" />
                    <div className="h-20 bg-zinc-100 border border-zinc-200 rounded" />
                    <div className="h-2 w-4/6 bg-zinc-300 rounded" />
                    <div className="h-2 w-full bg-zinc-300 rounded" />
                  </div>

                  {/* Top Trim Shadow Mask */}
                  <div 
                    className="absolute inset-x-0 top-0 bg-zinc-950/20 backdrop-blur-[1px] border-b border-dashed border-zinc-950 flex items-center justify-center text-[10px] font-mono text-zinc-800 font-bold"
                    style={{ height: `${previewTopPercent}%` }}
                  >
                    {topMargin > 15 && `Top: ${topMargin} pt`}
                  </div>

                  {/* Bottom Trim Shadow Mask */}
                  <div 
                    className="absolute inset-x-0 bottom-0 bg-zinc-950/20 backdrop-blur-[1px] border-t border-dashed border-zinc-950 flex items-center justify-center text-[10px] font-mono text-zinc-800 font-bold"
                    style={{ height: `${(bottomMargin / currentPage.height) * 100}%` }}
                  >
                    {bottomMargin > 15 && `Bottom: ${bottomMargin} pt`}
                  </div>

                  {/* Left Trim Shadow Mask */}
                  <div 
                    className="absolute inset-y-0 left-0 bg-zinc-950/20 backdrop-blur-[1px] border-r border-dashed border-zinc-950 flex items-center justify-center text-[10px] font-mono text-zinc-800 font-bold"
                    style={{ 
                      width: `${previewLeftPercent}%`,
                      top: `${previewTopPercent}%`,
                      bottom: `${(bottomMargin / currentPage.height) * 100}%`
                    }}
                  >
                    {leftMargin > 20 && `${leftMargin} pt`}
                  </div>

                  {/* Right Trim Shadow Mask */}
                  <div 
                    className="absolute inset-y-0 right-0 bg-zinc-950/20 backdrop-blur-[1px] border-l border-dashed border-zinc-950 flex items-center justify-center text-[10px] font-mono text-zinc-800 font-bold"
                    style={{ 
                      width: `${(rightMargin / currentPage.width) * 100}%`,
                      top: `${previewTopPercent}%`,
                      bottom: `${(bottomMargin / currentPage.height) * 100}%`
                    }}
                  >
                    {rightMargin > 20 && `${rightMargin} pt`}
                  </div>

                  {/* Active Visible Crop Box Border */}
                  <div
                    className="absolute border-2 border-emerald-600 rounded pointer-events-none transition-all duration-75 flex items-center justify-center"
                    style={{
                      top: `${previewTopPercent}%`,
                      left: `${previewLeftPercent}%`,
                      width: `${previewWidthPercent}%`,
                      height: `${previewHeightPercent}%`,
                    }}
                  >
                    <span className="text-[9px] font-mono font-bold bg-emerald-600 text-white px-1.5 py-0.5 rounded shadow-xs">
                      Active Viewport
                    </span>
                  </div>
                </div>
              </div>

              {/* Right Column: Margin Controls */}
              <div className="lg:col-span-6 p-6 sm:p-8 rounded-3xl bg-white border border-zinc-200 shadow-xs space-y-6 flex flex-col justify-between">
                <div>
                  <h3 className="text-base font-bold text-zinc-950 flex items-center gap-2">
                    <Sliders className="w-4 h-4 stroke-[1.8]" />
                    Precision Margin Trim (in Points)
                  </h3>
                  <p className="text-xs text-zinc-500 mt-1">
                    72 points = 1.0 inch • 28.35 points = 1.0 cm. Slide to adjust boundaries.
                  </p>
                </div>

                <div className="space-y-4">
                  {/* Top Margin */}
                  <div>
                    <div className="flex items-center justify-between text-xs font-mono mb-1.5">
                      <span className="text-zinc-700 font-semibold flex items-center gap-1.5">
                        <ArrowUpDown className="w-3.5 h-3.5 stroke-[1.8]" /> Top Margin:
                      </span>
                      <span className="font-bold text-zinc-950">{topMargin} pt ({(topMargin / 72).toFixed(2)} in)</span>
                    </div>
                    <input
                      type="range"
                      min={0}
                      max={Math.floor(currentPage.height * 0.4)}
                      value={topMargin}
                      onChange={(e) => setTopMargin(parseInt(e.target.value, 10))}
                      className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
                    />
                  </div>

                  {/* Bottom Margin */}
                  <div>
                    <div className="flex items-center justify-between text-xs font-mono mb-1.5">
                      <span className="text-zinc-700 font-semibold flex items-center gap-1.5">
                        <ArrowUpDown className="w-3.5 h-3.5 stroke-[1.8]" /> Bottom Margin:
                      </span>
                      <span className="font-bold text-zinc-950">{bottomMargin} pt ({(bottomMargin / 72).toFixed(2)} in)</span>
                    </div>
                    <input
                      type="range"
                      min={0}
                      max={Math.floor(currentPage.height * 0.4)}
                      value={bottomMargin}
                      onChange={(e) => setBottomMargin(parseInt(e.target.value, 10))}
                      className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
                    />
                  </div>

                  {/* Left Margin */}
                  <div>
                    <div className="flex items-center justify-between text-xs font-mono mb-1.5">
                      <span className="text-zinc-700 font-semibold flex items-center gap-1.5">
                        <ArrowLeftRight className="w-3.5 h-3.5 stroke-[1.8]" /> Left Margin:
                      </span>
                      <span className="font-bold text-zinc-950">{leftMargin} pt ({(leftMargin / 72).toFixed(2)} in)</span>
                    </div>
                    <input
                      type="range"
                      min={0}
                      max={Math.floor(currentPage.width * 0.4)}
                      value={leftMargin}
                      onChange={(e) => setLeftMargin(parseInt(e.target.value, 10))}
                      className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
                    />
                  </div>

                  {/* Right Margin */}
                  <div>
                    <div className="flex items-center justify-between text-xs font-mono mb-1.5">
                      <span className="text-zinc-700 font-semibold flex items-center gap-1.5">
                        <ArrowLeftRight className="w-3.5 h-3.5 stroke-[1.8]" /> Right Margin:
                      </span>
                      <span className="font-bold text-zinc-950">{rightMargin} pt ({(rightMargin / 72).toFixed(2)} in)</span>
                    </div>
                    <input
                      type="range"
                      min={0}
                      max={Math.floor(currentPage.width * 0.4)}
                      value={rightMargin}
                      onChange={(e) => setRightMargin(parseInt(e.target.value, 10))}
                      className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
                    />
                  </div>
                </div>

                {/* Scope Radio Selector */}
                <div className="pt-4 border-t border-zinc-100">
                  <span className="block text-xs font-mono font-bold uppercase tracking-wider text-zinc-700 mb-2">
                    Application Target Scope
                  </span>
                  <div className="grid grid-cols-2 gap-3">
                    <button
                      type="button"
                      onClick={() => setApplyScope('all')}
                      className={`p-3 rounded-xl border text-left transition-all cursor-pointer ${
                        applyScope === 'all'
                          ? 'border-zinc-950 bg-zinc-950 text-white font-bold'
                          : 'border-zinc-200 bg-white text-zinc-700 hover:border-zinc-300'
                      }`}
                    >
                      <div className="text-xs">All {pageCount} Pages</div>
                      <div className={`text-[10px] mt-0.5 ${applyScope === 'all' ? 'text-zinc-300' : 'text-zinc-400'}`}>
                        Uniform trim across document
                      </div>
                    </button>

                    <button
                      type="button"
                      onClick={() => setApplyScope('current')}
                      className={`p-3 rounded-xl border text-left transition-all cursor-pointer ${
                        applyScope === 'current'
                          ? 'border-zinc-950 bg-zinc-950 text-white font-bold'
                          : 'border-zinc-200 bg-white text-zinc-700 hover:border-zinc-300'
                      }`}
                    >
                      <div className="text-xs">Page {currentPageIndex + 1} Only</div>
                      <div className={`text-[10px] mt-0.5 ${applyScope === 'current' ? 'text-zinc-300' : 'text-zinc-400'}`}>
                        Apply only to active preview
                      </div>
                    </button>
                  </div>
                </div>

              </div>

            </div>

            {/* Action Bar */}
            <div className="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 rounded-3xl bg-zinc-900 text-white shadow-lg">
              <div>
                <div className="text-sm font-bold flex items-center gap-2">
                  <ShieldCheck className="w-4 h-4 text-emerald-400 stroke-[2]" />
                  Ready to Crop Document
                </div>
                <p className="text-xs text-zinc-400 mt-0.5">
                  Trimming {topMargin}pt top, {bottomMargin}pt bottom, {leftMargin}pt left, {rightMargin}pt right • 100% Lossless
                </p>
              </div>

              {!croppedBytes ? (
                <button
                  type="button"
                  onClick={handleCrop}
                  disabled={isProcessing}
                  className="w-full sm:w-auto px-6 py-3 rounded-2xl bg-white hover:bg-zinc-100 text-zinc-950 text-sm font-bold flex items-center justify-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shadow-md active:scale-95"
                >
                  {isProcessing ? (
                    <>
                      <div className="w-4 h-4 border-2 border-zinc-950 border-t-transparent rounded-full animate-spin" />
                      Trimming In Memory...
                    </>
                  ) : (
                    <>
                      <Crop className="w-4 h-4 stroke-[2]" />
                      Crop & Trim PDF
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
                  Download Cropped PDF
                </button>
              )}
            </div>

            {/* Success Banner */}
            {croppedBytes && (
              <div className="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between gap-3 text-xs font-mono animate-in fade-in">
                <div className="flex items-center gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-700 stroke-[2]" />
                  <span>Cropped PDF generated! File size: <strong>{formatBytes(croppedBytes.length)}</strong></span>
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
