'use client';

import React, { useState, useRef } from 'react';
import { 
  FileText, 
  UploadCloud, 
  Stamp, 
  Download, 
  Eye, 
  Sliders, 
  Sparkles, 
  ShieldAlert, 
  Check, 
  Type, 
  RotateCw
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo, watermarkPdf, downloadPdfBlob } from '@/lib/pdf-engine';

export default function WatermarkPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // PDF Document State
  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [pdfFileSize, setPdfFileSize] = useState<string>('');
  const [isDragging, setIsDragging] = useState<boolean>(false);
  const [isProcessing, setIsProcessing] = useState<boolean>(false);

  // Watermark Customization State
  const [watermarkText, setWatermarkText] = useState<string>('CONFIDENTIAL PROOF');
  const [opacityPercent, setOpacityPercent] = useState<number>(25); // 10% to 80%
  const [angle, setAngle] = useState<number>(45); // 45, 0, -45, 90
  const [fontSize, setFontSize] = useState<number>(54); // 36, 48, 54, 72
  const [colorPreset, setColorPreset] = useState<'gray' | 'dark' | 'red'>('gray');

  const formatFileSize = (bytes: number): string => {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
  };

  const handleFileSelect = async (file: File) => {
    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please upload a valid PDF document');
      return;
    }

    try {
      const info = await getPdfInfo(file);
      setPdfFile(file);
      setPageCount(info.pageCount);
      setPdfFileSize(formatFileSize(file.size));
      showToast(`Loaded ${file.name} (${info.pageCount} pages)`);
    } catch (err) {
      console.error(err);
      showToast('Error reading PDF. Please verify file integrity.');
    }
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFileSelect(e.dataTransfer.files[0]);
    }
  };

  // Color RGB mapping
  const colorMap = {
    gray: { r: 0.5, g: 0.5, b: 0.5, label: 'Subtle Slate' },
    dark: { r: 0.1, g: 0.1, b: 0.1, label: 'Pitch Black' },
    red: { r: 0.85, g: 0.18, b: 0.18, label: 'Alert Crimson' },
  };

  const textPresets = [
    'CONFIDENTIAL',
    'DRAFT PROOF',
    'SAMPLE PROOF',
    'CLIENT REVIEW ONLY',
    'DO NOT DISTRIBUTE',
  ];

  const handleApplyWatermark = async () => {
    if (!pdfFile) {
      showToast('Please upload a PDF document first');
      return;
    }

    if (!watermarkText.trim()) {
      showToast('Please enter watermark text');
      return;
    }

    try {
      setIsProcessing(true);
      const chosenColor = colorMap[colorPreset];

      const watermarkedBytes = await watermarkPdf(pdfFile, watermarkText.trim(), {
        opacity: opacityPercent / 100,
        color: { r: chosenColor.r, g: chosenColor.g, b: chosenColor.b },
        fontSize,
        angle,
      });

      const baseName = pdfFile.name.replace(/\.[^/.]+$/, '');
      downloadPdfBlob(watermarkedBytes, `${baseName}-watermarked.pdf`);
      showToast(`Watermark applied across all ${pageCount} pages!`);
    } catch (err) {
      console.error(err);
      showToast('Failed to apply watermark. Please try again.');
    } finally {
      setIsProcessing(false);
    }
  };

  const watermarkFaqs = [
    {
      question: 'Does applying a watermark reduce image resolution or compress text?',
      answer: 'No. The watermark is applied as a non-destructive vector text layer on top of each page. Your original images, vector fonts, and layout remain at full 100% native quality.'
    },
    {
      question: 'Will the watermark be stamped on every page in my PDF?',
      answer: 'Yes! The watermark is automatically stamped across all pages in the document with uniform angle, opacity, and font alignment.'
    },
    {
      question: 'Is my confidential client PDF uploaded to any external server?',
      answer: 'No. Watermarking runs entirely in your browser using client-side JavaScript memory. Sensitive client pitch decks, legal contracts, and shoot previews never leave your device.'
    },
    {
      question: 'Can I add custom agency names or shoot codes as the watermark?',
      answer: 'Yes. You can use our one-click presets like "CONFIDENTIAL" or "DRAFT PROOF", or type your custom agency name, client shoot code, or date.'
    }
  ];

  return (
    <ToolPageShell
      toolId="watermark-pdf"
      badgeTag="🛡️ Media & Asset Security"
      title="Add Watermark to PDF"
      subtitle="Stamp custom diagonal or horizontal watermarks across every page of your shoot previews, proposals, and confidential proofs. 100% client-side pure JS."
      faqItems={watermarkFaqs}
    >
      <div className="space-y-6">

        {/* ── 1. Document Upload Card ── */}
        {!pdfFile ? (
          <div
            onDragOver={(e) => { e.preventDefault(); setIsDragging(true); }}
            onDragLeave={(e) => { e.preventDefault(); setIsDragging(false); }}
            onDrop={handleDrop}
            onClick={() => fileInputRef.current?.click()}
            className={`rounded-3xl border-2 border-dashed p-8 sm:p-12 text-center cursor-pointer transition-all ${
              isDragging
                ? 'border-zinc-950 bg-zinc-100/80 scale-[0.99]'
                : 'border-zinc-300/80 bg-white hover:border-zinc-500 hover:bg-zinc-50/50 shadow-xs'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept="application/pdf"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleFileSelect(e.target.files[0]);
                }
              }}
              className="hidden"
            />

            <div className="flex flex-col items-center justify-center max-w-md mx-auto space-y-3">
              <div className="w-14 h-14 rounded-2xl bg-zinc-100 flex items-center justify-center text-zinc-900 border border-zinc-200 shadow-2xs">
                <UploadCloud className="w-7 h-7" />
              </div>
              <div className="space-y-1">
                <p className="font-display text-base sm:text-lg font-bold text-zinc-950">
                  Upload PDF to Watermark
                </p>
                <p className="text-xs sm:text-sm text-zinc-500 font-normal">
                  Drop proposal, photo deck, or agreement PDF here &bull; All pages stamped
                </p>
              </div>
              <span className="px-3 py-1 rounded-full bg-zinc-100 text-zinc-800 text-[11px] font-mono font-medium border border-zinc-200">
                PDF Documents
              </span>
            </div>
          </div>
        ) : (
          /* Active Document Bar */
          <div className="rounded-3xl bg-white border border-zinc-200/90 p-4 sm:p-5 shadow-xs flex items-center justify-between gap-4">
            <div className="flex items-center gap-3.5 min-w-0">
              <div className="w-11 h-11 rounded-2xl bg-zinc-100 flex items-center justify-center text-zinc-900 shrink-0 border border-zinc-200">
                <FileText className="w-5 h-5 text-zinc-950" />
              </div>
              <div className="min-w-0">
                <h4 className="text-xs sm:text-sm font-bold text-zinc-950 truncate">
                  {pdfFile.name}
                </h4>
                <div className="flex items-center gap-2 text-[11px] font-mono text-zinc-500">
                  <span>{pageCount} Pages</span>
                  <span>&bull;</span>
                  <span>{pdfFileSize}</span>
                  <span>&bull;</span>
                  <span className="text-emerald-700 font-medium">Ready to Watermark</span>
                </div>
              </div>
            </div>

            <button
              type="button"
              onClick={() => {
                setPdfFile(null);
                setPageCount(0);
              }}
              className="text-xs font-bold text-zinc-500 hover:text-rose-600 px-3 py-2 rounded-xl hover:bg-rose-50 transition-colors shrink-0 cursor-pointer"
            >
              Change PDF
            </button>
          </div>
        )}

        {/* ── 2. Watermark Text Customization ── */}
        <div className="rounded-3xl bg-white border border-zinc-200/90 p-5 sm:p-6 shadow-xs space-y-5">
          <div className="flex items-center justify-between border-b border-zinc-100 pb-4">
            <div className="flex items-center gap-2">
              <Stamp className="w-4 h-4 text-zinc-950" />
              <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950">
                Watermark Text & Presets
              </h3>
            </div>
            <span className="text-[11px] font-mono text-zinc-400">
              {watermarkText.length} chars
            </span>
          </div>

          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
              Custom Watermark Text
            </label>
            <input
              type="text"
              value={watermarkText}
              onChange={(e) => setWatermarkText(e.target.value)}
              placeholder="e.g. CONFIDENTIAL PROOF"
              className="w-full px-4 py-3 rounded-2xl border border-zinc-200 font-mono text-sm sm:text-base font-bold text-zinc-950 tracking-wider focus:outline-none focus:border-zinc-950 transition-colors"
            />
          </div>

          {/* One-Tap Quick Presets */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
              Quick Preset Tags
            </label>
            <div className="flex flex-wrap gap-2">
              {textPresets.map((preset) => (
                <button
                  key={preset}
                  type="button"
                  onClick={() => setWatermarkText(preset)}
                  className={`px-3 py-1.5 rounded-xl font-mono text-xs font-bold transition-all cursor-pointer ${
                    watermarkText === preset
                      ? 'bg-zinc-950 text-white shadow-xs'
                      : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-700'
                  }`}
                >
                  {preset}
                </button>
              ))}
            </div>
          </div>
        </div>

        {/* ── 3. Visual Styling & Angle Slider ── */}
        <div className="rounded-3xl bg-white border border-zinc-200/90 p-5 sm:p-6 shadow-xs space-y-5">
          <div className="flex items-center justify-between border-b border-zinc-100 pb-4">
            <div className="flex items-center gap-2">
              <Sliders className="w-4 h-4 text-zinc-950" />
              <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950">
                Typography, Opacity & Rotation
              </h3>
            </div>
            <span className="text-[11px] font-mono text-zinc-500">
              {opacityPercent}% opacity &bull; {angle}°
            </span>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
            {/* Opacity Slider */}
            <div>
              <div className="flex items-center justify-between mb-2">
                <label className="text-xs font-bold uppercase tracking-wider text-zinc-600">
                  Opacity Level
                </label>
                <span className="text-xs font-mono font-bold text-zinc-950">
                  {opacityPercent}%
                </span>
              </div>
              <input
                type="range"
                min="10"
                max="80"
                step="5"
                value={opacityPercent}
                onChange={(e) => setOpacityPercent(Number(e.target.value))}
                className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
              />
              <div className="flex justify-between text-[10px] font-mono text-zinc-400 mt-1">
                <span>10% (Faint)</span>
                <span>45% (Standard)</span>
                <span>80% (Bold)</span>
              </div>
            </div>

            {/* Rotation Angle Preset */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Rotation Angle
              </label>
              <div className="grid grid-cols-3 gap-2">
                {[
                  { deg: 45, label: '45° Diagonal' },
                  { deg: 0, label: '0° Flat' },
                  { deg: 90, label: '90° Vertical' },
                ].map((item) => (
                  <button
                    key={item.deg}
                    type="button"
                    onClick={() => setAngle(item.deg)}
                    className={`py-2 px-2 rounded-xl text-xs font-mono font-bold transition-all cursor-pointer text-center ${
                      angle === item.deg
                        ? 'bg-zinc-950 text-white shadow-xs'
                        : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                    }`}
                  >
                    {item.label}
                  </button>
                ))}
              </div>
            </div>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3 border-t border-zinc-100">
            {/* Font Size Preset */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Font Size
              </label>
              <div className="grid grid-cols-4 gap-1.5">
                {[36, 48, 54, 72].map((size) => (
                  <button
                    key={size}
                    type="button"
                    onClick={() => setFontSize(size)}
                    className={`py-2 px-1 text-center rounded-xl text-xs font-mono font-bold transition-all cursor-pointer ${
                      fontSize === size
                        ? 'bg-zinc-950 text-white shadow-xs'
                        : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                    }`}
                  >
                    {size}px
                  </button>
                ))}
              </div>
            </div>

            {/* Color Preset */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Watermark Color Tone
              </label>
              <div className="grid grid-cols-3 gap-2">
                {(['gray', 'dark', 'red'] as const).map((col) => (
                  <button
                    key={col}
                    type="button"
                    onClick={() => setColorPreset(col)}
                    className={`py-2 px-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 ${
                      colorPreset === col
                        ? 'bg-zinc-950 text-white shadow-xs'
                        : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                    }`}
                  >
                    <span
                      className={`w-2.5 h-2.5 rounded-full ${
                        col === 'gray' ? 'bg-zinc-400' : col === 'dark' ? 'bg-zinc-950' : 'bg-rose-600'
                      }`}
                    />
                    <span>{colorMap[col].label.split(' ')[1]}</span>
                  </button>
                ))}
              </div>
            </div>
          </div>
        </div>

        {/* ── 4. Live Visual Sheet Preview ── */}
        <div className="rounded-3xl bg-white border border-zinc-200/90 p-5 sm:p-6 shadow-xs space-y-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2">
              <Eye className="w-4 h-4 text-zinc-950" />
              <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950">
                Live Page Placement Preview
              </h3>
            </div>
            <span className="text-[11px] font-mono text-zinc-400">
              Sample Document Sheet
            </span>
          </div>

          {/* Paper Mockup Container */}
          <div className="relative w-full max-w-sm mx-auto h-64 bg-zinc-50 border border-zinc-300/80 rounded-2xl shadow-inner p-6 flex flex-col justify-between overflow-hidden select-none">
            
            {/* Mock text lines representing document content */}
            <div className="space-y-2 opacity-30 pointer-events-none">
              <div className="h-3.5 bg-zinc-400 rounded-md w-3/4" />
              <div className="h-2.5 bg-zinc-300 rounded-md w-full" />
              <div className="h-2.5 bg-zinc-300 rounded-md w-5/6" />
              <div className="h-2.5 bg-zinc-300 rounded-md w-4/5" />
            </div>

            <div className="space-y-2 opacity-30 pointer-events-none">
              <div className="h-2.5 bg-zinc-300 rounded-md w-full" />
              <div className="h-2.5 bg-zinc-300 rounded-md w-11/12" />
              <div className="h-2.5 bg-zinc-300 rounded-md w-2/3" />
            </div>

            {/* Overlaid Rotated Watermark */}
            <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
              <div
                style={{
                  transform: `rotate(${angle}deg)`,
                  opacity: opacityPercent / 100,
                  color: colorPreset === 'red' ? '#dc2626' : colorPreset === 'dark' ? '#18181b' : '#64748b',
                }}
                className="font-mono font-black text-xl sm:text-2xl uppercase tracking-widest whitespace-nowrap text-center transition-transform duration-200"
              >
                {watermarkText || 'SAMPLE WATERMARK'}
              </div>
            </div>

            <div className="flex justify-between items-center opacity-30 pointer-events-none pt-4 border-t border-zinc-200 text-[9px] font-mono text-zinc-400">
              <span>Page 1 of {pageCount || 1}</span>
              <span>CONFIDENTIAL</span>
            </div>
          </div>
        </div>

        {/* ── 5. Primary CTA Action ── */}
        <div className="pt-2">
          <button
            type="button"
            disabled={!pdfFile || isProcessing || !watermarkText.trim()}
            onClick={handleApplyWatermark}
            className="w-full py-4 px-6 rounded-2xl bg-zinc-950 hover:bg-zinc-800 disabled:bg-zinc-300 text-white font-bold text-sm sm:text-base flex items-center justify-center gap-2.5 shadow-lg active:scale-[0.99] transition-all cursor-pointer disabled:cursor-not-allowed"
          >
            {isProcessing ? (
              <>
                <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                <span>Applying Watermark to All Pages...</span>
              </>
            ) : (
              <>
                <Download className="w-4 h-4" />
                <span>
                  {pdfFile
                    ? `Stamp Watermark & Download (${pageCount} Pages)`
                    : 'Upload PDF to Add Watermark'}
                </span>
              </>
            )}
          </button>
        </div>

      </div>
    </ToolPageShell>
  );
}
