'use client';

import React, { useState, useRef, useCallback, useEffect } from 'react';
import { 
  UploadCloud, 
  Download, 
  RefreshCw, 
  Sliders, 
  Target, 
  FileImage, 
  Check, 
  ArrowRight, 
  Zap, 
  Trash2,
  Lock,
  Layers
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { 
  loadImageFromFile, 
  compressImageCanvas, 
  formatBytes, 
  triggerBrowserImageDownload 
} from '@/lib/image-engine';

interface ImageMeta {
  file: File;
  name: string;
  originalWidth: number;
  originalHeight: number;
  originalSize: number;
  dataUrl: string;
  imgElement: HTMLImageElement;
}

export default function CompressImagePage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [imageMeta, setImageMeta] = useState<ImageMeta | null>(null);
  const [isDragging, setIsDragging] = useState(false);
  const [isProcessing, setIsProcessing] = useState(false);

  // Settings
  const [mode, setMode] = useState<'quality' | 'target'>('quality');
  const [qualityPercent, setQualityPercent] = useState<number>(80);
  const [targetKb, setTargetKb] = useState<number>(150);
  const [targetFormat, setTargetFormat] = useState<'image/jpeg' | 'image/webp' | 'image/png'>('image/jpeg');
  const [maxDimension, setMaxDimension] = useState<number | 'original'>('original');

  // Compressed Result
  const [compressedResult, setCompressedResult] = useState<{
    blob: Blob;
    dataUrl: string;
    size: number;
    width: number;
    height: number;
  } | null>(null);

  const handleFile = useCallback(async (file: File) => {
    if (!file.type.startsWith('image/')) {
      showToast('Please upload a valid image file (JPG, PNG, or WebP).');
      return;
    }

    try {
      setIsProcessing(true);
      const img = await loadImageFromFile(file);
      const meta: ImageMeta = {
        file,
        name: file.name,
        originalWidth: img.naturalWidth || img.width,
        originalHeight: img.naturalHeight || img.height,
        originalSize: file.size,
        dataUrl: img.src,
        imgElement: img,
      };
      setImageMeta(meta);

      // Auto pick output format matching input unless user already set
      if (file.type === 'image/webp') {
        setTargetFormat('image/webp');
      } else if (file.type === 'image/png') {
        setTargetFormat('image/jpeg'); // Default PNGs to JPEG for maximum compression savings
      } else {
        setTargetFormat('image/jpeg');
      }

      showToast('Image loaded successfully');
    } catch {
      showToast('Could not decode image file.');
    } finally {
      setIsProcessing(false);
    }
  }, [showToast]);

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDragging(true);
  };

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDragging(false);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDragging(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFile(e.dataTransfer.files[0]);
    }
  };

  const handleFileInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      handleFile(e.target.files[0]);
    }
  };

  // Run Compression
  const executeCompression = useCallback(async () => {
    if (!imageMeta) return;

    setIsProcessing(true);
    try {
      const maxDim = maxDimension === 'original' ? undefined : maxDimension;

      if (mode === 'quality') {
        const qualityVal = targetFormat === 'image/png' ? 0.95 : qualityPercent / 100;
        const res = await compressImageCanvas(
          imageMeta.imgElement,
          targetFormat,
          qualityVal,
          maxDim,
          maxDim
        );
        setCompressedResult(res);
      } else {
        // Target File Size Mode (Binary Search Optimization)
        const targetBytes = targetKb * 1024;
        let bestResult: { blob: Blob; dataUrl: string; size: number; width: number; height: number } | null = null;
        
        let lowQ = 0.05;
        let highQ = 0.98;
        let bestQuality = 0.5;

        // Iterative search for optimal quality
        for (let iter = 0; iter < 7; iter++) {
          const testQ = (lowQ + highQ) / 2;
          const attempt = await compressImageCanvas(
            imageMeta.imgElement,
            targetFormat === 'image/png' ? 'image/jpeg' : targetFormat,
            testQ,
            maxDim,
            maxDim
          );

          if (!bestResult || Math.abs(attempt.size - targetBytes) < Math.abs(bestResult.size - targetBytes)) {
            bestResult = attempt;
            bestQuality = testQ;
          }

          if (attempt.size > targetBytes) {
            highQ = testQ;
          } else {
            lowQ = testQ;
          }
        }

        // If even lowest quality is larger than target, downscale dimensions
        if (bestResult && bestResult.size > targetBytes && targetBytes < imageMeta.originalSize) {
          let scaleFactor = Math.sqrt(targetBytes / bestResult.size) * 0.92;
          scaleFactor = Math.max(0.2, Math.min(0.95, scaleFactor));
          const scaledW = Math.round(imageMeta.originalWidth * scaleFactor);
          const scaledH = Math.round(imageMeta.originalHeight * scaleFactor);

          const scaledAttempt = await compressImageCanvas(
            imageMeta.imgElement,
            targetFormat === 'image/png' ? 'image/jpeg' : targetFormat,
            bestQuality,
            scaledW,
            scaledH
          );
          bestResult = scaledAttempt;
        }

        if (bestResult) {
          setCompressedResult(bestResult);
        }
      }
    } catch {
      showToast('Compression failed on this canvas.');
    } finally {
      setIsProcessing(false);
    }
  }, [imageMeta, mode, qualityPercent, targetKb, targetFormat, maxDimension, showToast]);

  useEffect(() => {
    if (imageMeta) {
      executeCompression();
    }
  }, [imageMeta, mode, qualityPercent, targetKb, targetFormat, maxDimension, executeCompression]);

  const handleDownload = () => {
    if (!compressedResult || !imageMeta) return;

    const baseName = imageMeta.name.substring(0, imageMeta.name.lastIndexOf('.')) || imageMeta.name;
    const ext = targetFormat === 'image/webp' ? 'webp' : targetFormat === 'image/png' ? 'png' : 'jpg';
    const outputFilename = `${baseName}-compressed.${ext}`;

    triggerBrowserImageDownload(compressedResult.blob, outputFilename);
    showToast(`Downloaded ${outputFilename}`);
  };

  const handleReset = () => {
    setImageMeta(null);
    setCompressedResult(null);
    if (fileInputRef.current) {
      fileInputRef.current.value = '';
    }
  };

  const calculateSavingsPercent = () => {
    if (!imageMeta || !compressedResult) return 0;
    const diff = imageMeta.originalSize - compressedResult.size;
    const pct = Math.round((diff / imageMeta.originalSize) * 100);
    return pct;
  };

  const savings = calculateSavingsPercent();

  const faqItems = [
    {
      question: 'How does Cora compress images without server uploads?',
      answer: 'Cora runs a client-side HTML5 Canvas and WebGL memory pipeline directly in your local browser runtime. Your images are parsed, re-quantized, and compressed entirely in local RAM. No bytes are sent to external servers, ensuring complete client privacy and zero network transfer delays.',
    },
    {
      question: 'Will compressing my images visibly degrade visual quality?',
      answer: 'By default, Cora uses intelligent perceptual quantization (around 80% quality) which discards imperceptible high-frequency noise while preserving sharp edges, natural skin tones, and rich contrast. Studio portfolios, commercial headshots, and e-commerce listings retain near-lossless clarity at a fraction of the file size.',
    },
    {
      question: 'Which format should I choose between JPEG, WebP, and PNG?',
      answer: 'JPEG is the universal industry standard for photos, headshots, and studio deliverables. WebP provides 25% to 35% higher compression efficiency at equivalent visual quality across modern web browsers. PNG is recommended when transparent backgrounds or razor-sharp vector/typography edges must be preserved losslessly.',
    },
    {
      question: 'How does the Target File Size mode work?',
      answer: 'Target File Size mode uses a binary search heuristic to automatically dial in the exact compression quality and canvas quantization needed to meet your desired budget (e.g. under 200KB or 50KB for government portals, job applications, or strict marketplace listing caps).',
    },
    {
      question: 'Are photoshoot deliverables, sensitive client documents, and headshots safe?',
      answer: 'Yes, 100%. Because Cora processes everything strictly inside your local device memory using native Web APIs, your files never touch any cloud storage, database, or external network. You can disconnect your internet and the compressor will continue functioning with zero degradation.',
    },
  ];

  return (
    <ToolPageShell
      toolId="compress-image"
      badgeTag="Studio Media Engine"
      title="Compress Image Online Free"
      subtitle="Reduce JPG, PNG, and WebP image file sizes by up to 90% without losing visual clarity. 100% private, client-side memory compression with zero server uploads."
      faqItems={faqItems}
    >
      <div className="space-y-6">

        {/* ── Dropzone (If no image loaded) ── */}
        {!imageMeta ? (
          <div
            onDragOver={handleDragOver}
            onDragLeave={handleDragLeave}
            onDrop={handleDrop}
            onClick={() => fileInputRef.current?.click()}
            className={`relative rounded-3xl border-2 border-dashed p-8 sm:p-14 text-center cursor-pointer transition-all ${
              isDragging
                ? 'border-zinc-950 bg-zinc-100/80 scale-[0.99]'
                : 'border-zinc-300/80 bg-white hover:border-zinc-500 hover:bg-zinc-50/50 shadow-xs'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept="image/jpeg,image/png,image/webp,image/avif"
              onChange={handleFileInputChange}
              className="hidden"
            />

            <div className="flex flex-col items-center justify-center max-w-md mx-auto space-y-4">
              <div className="w-16 h-16 rounded-2xl bg-zinc-100 flex items-center justify-center text-zinc-900 border border-zinc-200 shadow-2xs">
                <UploadCloud className="w-8 h-8" />
              </div>

              <div className="space-y-1.5">
                <p className="font-display text-lg sm:text-xl font-bold text-zinc-950">
                  Drop your image here to compress
                </p>
                <p className="text-xs sm:text-sm text-zinc-500 font-normal">
                  Supports high-resolution JPG, PNG, WebP &bull; No size caps &bull; 100% in-browser
                </p>
              </div>

              <div className="pt-2 flex flex-wrap items-center justify-center gap-2">
                <span className="inline-flex items-center gap-1 text-[11px] font-mono text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-md border border-zinc-200">
                  <Lock className="w-3 h-3 text-emerald-600" />
                  Zero Cloud Upload
                </span>
                <span className="inline-flex items-center gap-1 text-[11px] font-mono text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-md border border-zinc-200">
                  <Zap className="w-3 h-3 text-amber-500" />
                  Sub-50ms RAM Processing
                </span>
              </div>
            </div>
          </div>
        ) : (
          /* ── Active Image Compressor Workspace ── */
          <div className="space-y-6">

            {/* Top Toolbar: File Summary & Reset */}
            <div className="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs">
              <div className="flex items-center gap-3 min-w-0">
                <div className="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-800 shrink-0">
                  <FileImage className="w-5 h-5" />
                </div>
                <div className="min-w-0">
                  <p className="text-xs sm:text-sm font-bold text-zinc-950 truncate max-w-xs sm:max-w-md">
                    {imageMeta.name}
                  </p>
                  <p className="text-[11px] font-mono text-zinc-500">
                    Original: {imageMeta.originalWidth}×{imageMeta.originalHeight}px &bull; {formatBytes(imageMeta.originalSize)}
                  </p>
                </div>
              </div>

              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={() => fileInputRef.current?.click()}
                  className="px-3 py-1.5 rounded-xl border border-zinc-200 hover:border-zinc-300 bg-zinc-50 text-zinc-700 text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer"
                >
                  <RefreshCw className="w-3.5 h-3.5" />
                  <span>Replace</span>
                </button>
                <button
                  type="button"
                  onClick={handleReset}
                  className="p-1.5 rounded-xl text-zinc-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                  title="Remove image"
                >
                  <Trash2 className="w-4 h-4" />
                </button>
                <input
                  ref={fileInputRef}
                  type="file"
                  accept="image/jpeg,image/png,image/webp,image/avif"
                  onChange={handleFileInputChange}
                  className="hidden"
                />
              </div>
            </div>

            {/* Compression Settings Panel */}
            <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/80 shadow-2xs space-y-6">
              
              {/* Mode Toggle */}
              <div>
                <div className="flex items-center justify-between mb-2.5">
                  <label className="text-xs font-bold uppercase tracking-wider text-zinc-600 flex items-center gap-1.5">
                    <Sliders className="w-3.5 h-3.5 text-zinc-700" />
                    <span>Compression Mode</span>
                  </label>
                  <span className="text-[11px] font-mono text-zinc-400">
                    {mode === 'quality' ? 'Perceptual Quality Control' : 'Exact File Size Ceiling'}
                  </span>
                </div>

                <div className="grid grid-cols-2 gap-2 p-1 bg-zinc-100 rounded-2xl border border-zinc-200/70">
                  <button
                    type="button"
                    onClick={() => setMode('quality')}
                    className={`py-2 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer ${
                      mode === 'quality'
                        ? 'bg-white text-zinc-950 shadow-xs'
                        : 'text-zinc-600 hover:text-zinc-900'
                    }`}
                  >
                    <Sliders className="w-3.5 h-3.5" />
                    <span>Quality Slider (%)</span>
                  </button>
                  <button
                    type="button"
                    onClick={() => setMode('target')}
                    className={`py-2 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer ${
                      mode === 'target'
                        ? 'bg-white text-zinc-950 shadow-xs'
                        : 'text-zinc-600 hover:text-zinc-900'
                    }`}
                  >
                    <Target className="w-3.5 h-3.5" />
                    <span>Target Size Limit (KB)</span>
                  </button>
                </div>
              </div>

              {/* Mode Specific Controls */}
              {mode === 'quality' ? (
                <div className="space-y-2.5">
                  <div className="flex items-center justify-between">
                    <label className="text-xs font-bold text-zinc-700">
                      Compression Quality: <span className="font-mono text-zinc-950">{qualityPercent}%</span>
                    </label>
                    <div className="flex items-center gap-1 text-[11px] font-mono text-zinc-500">
                      <span>{qualityPercent >= 85 ? 'Near Lossless' : qualityPercent >= 65 ? 'High Efficiency' : 'Aggressive Shrink'}</span>
                    </div>
                  </div>

                  <input
                    type="range"
                    min={10}
                    max={100}
                    step={1}
                    value={qualityPercent}
                    onChange={(e) => setQualityPercent(Number(e.target.value))}
                    className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
                  />

                  <div className="flex items-center justify-between text-[10px] font-mono text-zinc-400">
                    <span>10% (Smallest Size)</span>
                    <span>50%</span>
                    <span>80% (Recommended)</span>
                    <span>100% (Maximum Quality)</span>
                  </div>
                </div>
              ) : (
                <div className="space-y-3">
                  <div className="flex items-center justify-between">
                    <label className="text-xs font-bold text-zinc-700">
                      Target Maximum File Size
                    </label>
                    <span className="text-xs font-mono font-bold text-zinc-950">
                      {targetKb} KB
                    </span>
                  </div>

                  <div className="grid grid-cols-5 gap-1.5">
                    {[50, 100, 200, 500, 1000].map((kb) => (
                      <button
                        key={kb}
                        type="button"
                        onClick={() => setTargetKb(kb)}
                        className={`py-2 px-1 text-center rounded-xl text-xs font-mono font-bold transition-all cursor-pointer ${
                          targetKb === kb
                            ? 'bg-zinc-950 text-white shadow-xs'
                            : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                        }`}
                      >
                        {kb >= 1000 ? '1 MB' : `${kb} KB`}
                      </button>
                    ))}
                  </div>

                  <div className="flex items-center gap-2 pt-1">
                    <input
                      type="number"
                      min={10}
                      max={20000}
                      value={targetKb}
                      onChange={(e) => setTargetKb(Math.max(10, Number(e.target.value)))}
                      className="w-32 px-3 py-1.5 rounded-xl border border-zinc-200 font-mono text-xs font-semibold text-zinc-950 focus:outline-none focus:border-zinc-950"
                    />
                    <span className="text-xs text-zinc-500 font-mono">Custom KB limit</span>
                  </div>
                </div>
              )}

              {/* Format & Dimension Settings */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-zinc-100">
                {/* Format Selector */}
                <div>
                  <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                    Output Format
                  </label>
                  <div className="grid grid-cols-3 gap-1.5">
                    {[
                      { id: 'image/jpeg', label: 'JPG', note: 'Universal' },
                      { id: 'image/webp', label: 'WebP', note: 'Modern' },
                      { id: 'image/png', label: 'PNG', note: 'Lossless' },
                    ].map((fmt) => (
                      <button
                        key={fmt.id}
                        type="button"
                        onClick={() => setTargetFormat(fmt.id as typeof targetFormat)}
                        className={`p-2 rounded-xl text-center transition-all cursor-pointer ${
                          targetFormat === fmt.id
                            ? 'bg-zinc-950 text-white shadow-xs'
                            : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                        }`}
                      >
                        <div className="text-xs font-bold font-mono">{fmt.label}</div>
                        <div className={`text-[10px] ${targetFormat === fmt.id ? 'text-zinc-300' : 'text-zinc-500'}`}>
                          {fmt.note}
                        </div>
                      </button>
                    ))}
                  </div>
                </div>

                {/* Max Dimension */}
                <div>
                  <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                    Max Dimension Limit
                  </label>
                  <div className="grid grid-cols-4 gap-1.5">
                    {[
                      { val: 'original', label: 'Original' },
                      { val: 2560, label: '2.5K' },
                      { val: 1920, label: '1080p' },
                      { val: 1280, label: '720p' },
                    ].map((d) => (
                      <button
                        key={String(d.val)}
                        type="button"
                        onClick={() => setMaxDimension(d.val as typeof maxDimension)}
                        className={`py-2 px-1 text-center rounded-xl text-xs font-mono font-bold transition-all cursor-pointer ${
                          maxDimension === d.val
                            ? 'bg-zinc-950 text-white shadow-xs'
                            : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                        }`}
                      >
                        {d.label}
                      </button>
                    ))}
                  </div>
                </div>
              </div>

            </div>

            {/* Live Before & After Comparison Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              
              {/* Original Card */}
              <div className="rounded-2xl border border-zinc-200/80 bg-white p-4 space-y-3 shadow-2xs">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-bold text-zinc-500 uppercase tracking-wider">Original File</span>
                  <span className="text-xs font-mono font-bold text-zinc-800 bg-zinc-100 px-2 py-0.5 rounded">
                    {formatBytes(imageMeta.originalSize)}
                  </span>
                </div>

                <div className="relative aspect-video rounded-xl overflow-hidden bg-zinc-100 border border-zinc-200/60 flex items-center justify-center">
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={imageMeta.dataUrl}
                    alt="Original"
                    className="max-h-full max-w-full object-contain"
                  />
                </div>

                <div className="text-[11px] font-mono text-zinc-500 flex items-center justify-between">
                  <span>Dimensions: {imageMeta.originalWidth} × {imageMeta.originalHeight}px</span>
                  <span>100% Quality</span>
                </div>
              </div>

              {/* Compressed Card */}
              <div className="rounded-2xl border border-zinc-200/80 bg-white p-4 space-y-3 shadow-2xs">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-1.5">
                    <span className="text-xs font-bold text-zinc-950 uppercase tracking-wider">Optimized File</span>
                    {savings > 0 && (
                      <span className="text-[11px] font-mono font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                        -{savings}% SAVED
                      </span>
                    )}
                  </div>
                  <span className="text-xs font-mono font-extrabold text-zinc-950 bg-zinc-100 px-2 py-0.5 rounded">
                    {compressedResult ? formatBytes(compressedResult.size) : 'Calculating...'}
                  </span>
                </div>

                <div className="relative aspect-video rounded-xl overflow-hidden bg-zinc-100 border border-zinc-200/60 flex items-center justify-center">
                  {isProcessing ? (
                    <div className="flex flex-col items-center gap-2 text-zinc-500">
                      <div className="w-5 h-5 border-2 border-zinc-300 border-t-zinc-900 rounded-full animate-spin" />
                      <span className="text-xs font-mono">Compressing in RAM...</span>
                    </div>
                  ) : compressedResult ? (
                    /* eslint-disable-next-line @next/next/no-img-element */
                    <img
                      src={compressedResult.dataUrl}
                      alt="Compressed Preview"
                      className="max-h-full max-w-full object-contain"
                    />
                  ) : null}
                </div>

                <div className="text-[11px] font-mono text-zinc-500 flex items-center justify-between">
                  <span>
                    Dimensions: {compressedResult ? `${compressedResult.width} × ${compressedResult.height}px` : '...'}
                  </span>
                  <span>
                    Format: {targetFormat === 'image/webp' ? 'WebP' : targetFormat === 'image/png' ? 'PNG' : 'JPEG'}
                  </span>
                </div>
              </div>

            </div>

            {/* Savings Banner Card */}
            {compressedResult && (
              <div className="p-4 rounded-2xl bg-zinc-950 text-white flex flex-wrap items-center justify-between gap-3 shadow-md">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white shrink-0 border border-white/15">
                    <Zap className="w-5 h-5 text-amber-400" />
                  </div>
                  <div>
                    <div className="text-xs sm:text-sm font-bold flex items-center gap-2">
                      <span>{savings >= 0 ? `${savings}% Storage Reduction` : 'Processed'}</span>
                      <span className="text-[10px] font-mono text-emerald-400 bg-emerald-400/20 px-1.5 py-0.5 rounded border border-emerald-400/30">
                        {savings >= 0 ? `Saved ${formatBytes(imageMeta.originalSize - compressedResult.size)}` : 'Optimized'}
                      </span>
                    </div>
                    <div className="text-[11px] font-mono text-zinc-400">
                      From {formatBytes(imageMeta.originalSize)} down to {formatBytes(compressedResult.size)}
                    </div>
                  </div>
                </div>

                <button
                  type="button"
                  disabled={isProcessing}
                  onClick={handleDownload}
                  className="py-2.5 px-5 rounded-xl bg-white hover:bg-zinc-100 text-zinc-950 font-bold text-xs sm:text-sm flex items-center gap-2 shadow-sm transition-all cursor-pointer active:scale-95"
                >
                  <Download className="w-4 h-4" />
                  <span>Download Compressed Image</span>
                </button>
              </div>
            )}

            {/* Primary Action Button */}
            <div className="pt-2">
              <button
                type="button"
                disabled={!compressedResult || isProcessing}
                onClick={handleDownload}
                className="w-full py-4 px-6 rounded-2xl bg-zinc-950 hover:bg-zinc-800 disabled:bg-zinc-300 text-white font-bold text-sm sm:text-base flex items-center justify-center gap-2.5 shadow-lg active:scale-[0.99] transition-all cursor-pointer disabled:cursor-not-allowed"
              >
                <Download className="w-4 h-4" />
                <span>
                  Download Optimized Image ({compressedResult ? formatBytes(compressedResult.size) : 'Ready'})
                </span>
              </button>
            </div>

          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
