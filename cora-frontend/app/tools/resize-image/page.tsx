'use client';

import React, { useState, useRef, useCallback, useEffect } from 'react';
import { 
  UploadCloud, 
  Download, 
  RefreshCw, 
  Lock as LockIcon, 
  Unlock, 
  Maximize2, 
  FileImage, 
  Trash2,
  Zap,
  Sliders,
  Sparkles,
  Layers
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { 
  loadImageFromFile, 
  resizeImageCanvas, 
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

interface SocialPreset {
  id: string;
  label: string;
  category: string;
  width: number;
  height: number;
  ratio: string;
}

const SOCIAL_PRESETS: SocialPreset[] = [
  { id: 'ig-square', label: 'Instagram Post', category: 'Instagram', width: 1080, height: 1080, ratio: '1:1' },
  { id: 'ig-story', label: 'Instagram Story', category: 'Instagram', width: 1080, height: 1920, ratio: '9:16' },
  { id: 'yt-thumb', label: 'YouTube Thumbnail', category: 'YouTube', width: 1280, height: 720, ratio: '16:9' },
  { id: 'x-header', label: 'X / Twitter Header', category: 'Twitter', width: 1500, height: 500, ratio: '3:1' },
  { id: 'li-banner', label: 'LinkedIn Banner', category: 'LinkedIn', width: 1584, height: 396, ratio: '4:1' },
  { id: 'fb-cover', label: 'Facebook Cover', category: 'Facebook', width: 820, height: 312, ratio: '2.6:1' },
];

export default function ResizeImagePage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [imageMeta, setImageMeta] = useState<ImageMeta | null>(null);
  const [isDragging, setIsDragging] = useState(false);
  const [isProcessing, setIsProcessing] = useState(false);

  // Dimension Controls
  const [width, setWidth] = useState<number>(1080);
  const [height, setHeight] = useState<number>(1080);
  const [lockAspect, setLockAspect] = useState<boolean>(true);
  const [scalePercent, setScalePercent] = useState<number>(100);

  // Format & Quality
  const [format, setFormat] = useState<'image/png' | 'image/jpeg' | 'image/webp'>('image/png');
  const [quality, setQuality] = useState<number>(92);

  // Resized Output Result
  const [resizedResult, setResizedResult] = useState<{
    blob: Blob;
    dataUrl: string;
    size: number;
  } | null>(null);

  const handleFile = useCallback(async (file: File) => {
    if (!file.type.startsWith('image/')) {
      showToast('Please upload a valid image file (JPG, PNG, or WebP).');
      return;
    }

    try {
      setIsProcessing(true);
      const img = await loadImageFromFile(file);
      const naturalW = img.naturalWidth || img.width;
      const naturalH = img.naturalHeight || img.height;

      const meta: ImageMeta = {
        file,
        name: file.name,
        originalWidth: naturalW,
        originalHeight: naturalH,
        originalSize: file.size,
        dataUrl: img.src,
        imgElement: img,
      };

      setImageMeta(meta);
      setWidth(naturalW);
      setHeight(naturalH);
      setScalePercent(100);

      // Match format
      if (file.type === 'image/webp') setFormat('image/webp');
      else if (file.type === 'image/jpeg') setFormat('image/jpeg');
      else setFormat('image/png');

      showToast('Image loaded successfully');
    } catch {
      showToast('Failed to decode image file.');
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

  // Adjust Width with Aspect Ratio Lock
  const handleWidthChange = (newWidth: number) => {
    const validW = Math.max(1, newWidth);
    setWidth(validW);
    if (imageMeta) {
      if (lockAspect && imageMeta.originalWidth > 0) {
        const ratio = imageMeta.originalHeight / imageMeta.originalWidth;
        setHeight(Math.round(validW * ratio));
      }
      const pct = Math.round((validW / imageMeta.originalWidth) * 100);
      setScalePercent(pct);
    }
  };

  // Adjust Height with Aspect Ratio Lock
  const handleHeightChange = (newHeight: number) => {
    const validH = Math.max(1, newHeight);
    setHeight(validH);
    if (imageMeta) {
      if (lockAspect && imageMeta.originalHeight > 0) {
        const ratio = imageMeta.originalWidth / imageMeta.originalHeight;
        setWidth(Math.round(validH * ratio));
      }
      const pct = Math.round((validH / imageMeta.originalHeight) * 100);
      setScalePercent(pct);
    }
  };

  // Adjust by Scale Percentage
  const handleScaleChange = (pct: number) => {
    setScalePercent(pct);
    if (imageMeta) {
      const factor = pct / 100;
      setWidth(Math.round(imageMeta.originalWidth * factor));
      setHeight(Math.round(imageMeta.originalHeight * factor));
    }
  };

  // Apply Social Preset
  const handleApplyPreset = (preset: SocialPreset) => {
    setLockAspect(false); // disable lock for fixed-dimension preset
    setWidth(preset.width);
    setHeight(preset.height);
    if (imageMeta) {
      const avgScale = Math.round((preset.width / imageMeta.originalWidth) * 100);
      setScalePercent(avgScale);
    }
    showToast(`Applied ${preset.label} (${preset.width}×${preset.height}px)`);
  };

  // Execute Resizing
  const executeResize = useCallback(async () => {
    if (!imageMeta || width <= 0 || height <= 0) return;

    setIsProcessing(true);
    try {
      const res = await resizeImageCanvas(
        imageMeta.imgElement,
        width,
        height,
        format,
        quality / 100
      );
      setResizedResult(res);
    } catch {
      showToast('Error during canvas resizing.');
    } finally {
      setIsProcessing(false);
    }
  }, [imageMeta, width, height, format, quality, showToast]);

  useEffect(() => {
    if (imageMeta) {
      const timeoutId = setTimeout(() => {
        executeResize();
      }, 150);
      return () => clearTimeout(timeoutId);
    }
  }, [imageMeta, width, height, format, quality, executeResize]);

  const handleDownload = () => {
    if (!resizedResult || !imageMeta) return;

    const baseName = imageMeta.name.substring(0, imageMeta.name.lastIndexOf('.')) || imageMeta.name;
    const ext = format === 'image/webp' ? 'webp' : format === 'image/jpeg' ? 'jpg' : 'png';
    const outputFilename = `${baseName}-${width}x${height}.${ext}`;

    triggerBrowserImageDownload(resizedResult.blob, outputFilename);
    showToast(`Downloaded ${outputFilename}`);
  };

  const handleReset = () => {
    setImageMeta(null);
    setResizedResult(null);
    if (fileInputRef.current) {
      fileInputRef.current.value = '';
    }
  };

  const faqItems = [
    {
      question: 'Will resizing with Cora maintain crisp visual sharpness?',
      answer: 'Yes. Cora employs multi-pass bicubic canvas interpolation with high-fidelity anti-aliasing. Downscaling photos compresses pixels cleanly without aliasing artifacts, while moderate upscaling preserves smooth gradient contours.',
    },
    {
      question: 'What does the Lock Aspect Ratio toggle do?',
      answer: 'When Lock Aspect Ratio is active, changing the width automatically calculates and adjusts the height (and vice versa) based on the exact original image ratio, preventing horizontal stretching or vertical squishing.',
    },
    {
      question: 'How do the social media presets work?',
      answer: 'The social presets instantly apply exact platform dimensions (e.g. 1080×1080 for Instagram square posts, 1080×1920 for Stories/Reels, 1500×500 for X/Twitter headers, and 1280×720 for YouTube thumbnails), saving you from remembering arbitrary pixel specifications.',
    },
    {
      question: 'Can I upscale small images to high resolution?',
      answer: 'You can scale images up to 200% or 300% of their original dimensions. Cora uses browser-native high-quality image smoothing to reduce pixelation during enlargement.',
    },
    {
      question: 'Are my uploaded studio photos private and safe?',
      answer: 'Yes, 100%. All image resizing, canvas drawing, and blob generation happen directly within your browser runtime memory. Zero bytes leave your machine or get transmitted to any cloud servers.',
    },
  ];

  return (
    <ToolPageShell
      toolId="resize-image"
      badgeTag="Social Media & Sizing Engine"
      title="Resize Image Online Free"
      subtitle="Change image dimensions in pixels or percentage with aspect ratio lock. 1-click presets for Instagram, YouTube, X, and LinkedIn. 100% private in browser memory."
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
                  Drop your image here to resize
                </p>
                <p className="text-xs sm:text-sm text-zinc-500 font-normal">
                  Scale pixels, lock aspect ratio &bull; Social media presets &bull; Zero server uploads
                </p>
              </div>

              <div className="pt-2 flex flex-wrap items-center justify-center gap-2">
                <span className="inline-flex items-center gap-1 text-[11px] font-mono text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-md border border-zinc-200">
                  <Maximize2 className="w-3 h-3 text-zinc-700" />
                  Exact Pixel Dimensions
                </span>
                <span className="inline-flex items-center gap-1 text-[11px] font-mono text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-md border border-zinc-200">
                  <Zap className="w-3 h-3 text-amber-500" />
                  Instant Canvas Rescaling
                </span>
              </div>
            </div>
          </div>
        ) : (
          /* ── Active Image Resizer Workspace ── */
          <div className="space-y-6">

            {/* Top Toolbar: File Summary & Replace */}
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

            {/* Social Media Presets Grid */}
            <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/80 shadow-2xs space-y-3">
              <div className="flex items-center justify-between">
                <h3 className="text-xs font-bold uppercase tracking-wider text-zinc-700 flex items-center gap-1.5">
                  <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
                  <span>Popular Social Media Presets</span>
                </h3>
                <span className="text-[11px] font-mono text-zinc-400">1-Tap Standard Sizes</span>
              </div>

              <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
                {SOCIAL_PRESETS.map((preset) => {
                  const isActive = width === preset.width && height === preset.height;
                  return (
                    <button
                      key={preset.id}
                      type="button"
                      onClick={() => handleApplyPreset(preset)}
                      className={`p-2.5 rounded-2xl border text-left transition-all cursor-pointer ${
                        isActive
                          ? 'border-zinc-950 bg-zinc-950 text-white shadow-xs'
                          : 'border-zinc-200 bg-zinc-50/80 hover:bg-white text-zinc-800'
                      }`}
                    >
                      <div className="text-[10px] font-mono text-zinc-400 uppercase">{preset.category}</div>
                      <div className="text-xs font-bold truncate leading-tight mt-0.5">{preset.label}</div>
                      <div className={`text-[11px] font-mono mt-1 ${isActive ? 'text-zinc-300' : 'text-zinc-500'}`}>
                        {preset.width}×{preset.height}
                      </div>
                    </button>
                  );
                })}
              </div>
            </div>

            {/* Dimension Settings & Controls */}
            <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/80 shadow-2xs space-y-6">
              
              {/* Width / Height Inputs with Lock Aspect Button */}
              <div className="space-y-3">
                <label className="text-xs font-bold uppercase tracking-wider text-zinc-600 block">
                  Custom Dimensions (Pixels)
                </label>

                <div className="flex flex-col sm:flex-row items-center gap-3">
                  
                  {/* Width Input */}
                  <div className="flex-1 w-full">
                    <div className="text-[11px] font-mono text-zinc-500 mb-1 flex items-center justify-between">
                      <span>Width (px)</span>
                      <span>Orig: {imageMeta.originalWidth}px</span>
                    </div>
                    <div className="relative">
                      <input
                        type="number"
                        min={1}
                        max={16000}
                        value={width}
                        onChange={(e) => handleWidthChange(Number(e.target.value))}
                        className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 font-mono text-sm font-bold text-zinc-950 focus:outline-none focus:border-zinc-950 transition-colors"
                      />
                      <span className="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-mono text-zinc-400">
                        px
                      </span>
                    </div>
                  </div>

                  {/* Lock Aspect Ratio Toggle Button */}
                  <div className="sm:pt-5">
                    <button
                      type="button"
                      onClick={() => setLockAspect(!lockAspect)}
                      className={`p-2.5 rounded-xl border flex items-center gap-1.5 transition-all cursor-pointer ${
                        lockAspect
                          ? 'border-zinc-950 bg-zinc-950 text-white shadow-xs'
                          : 'border-zinc-200 bg-zinc-100 text-zinc-600 hover:bg-zinc-200'
                      }`}
                      title={lockAspect ? 'Aspect ratio locked' : 'Aspect ratio unlocked'}
                    >
                      {lockAspect ? (
                        <>
                          <LockIcon className="w-4 h-4" />
                          <span className="text-xs font-bold hidden sm:inline">Locked</span>
                        </>
                      ) : (
                        <>
                          <Unlock className="w-4 h-4" />
                          <span className="text-xs font-bold hidden sm:inline">Freeform</span>
                        </>
                      )}
                    </button>
                  </div>

                  {/* Height Input */}
                  <div className="flex-1 w-full">
                    <div className="text-[11px] font-mono text-zinc-500 mb-1 flex items-center justify-between">
                      <span>Height (px)</span>
                      <span>Orig: {imageMeta.originalHeight}px</span>
                    </div>
                    <div className="relative">
                      <input
                        type="number"
                        min={1}
                        max={16000}
                        value={height}
                        onChange={(e) => handleHeightChange(Number(e.target.value))}
                        className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 font-mono text-sm font-bold text-zinc-950 focus:outline-none focus:border-zinc-950 transition-colors"
                      />
                      <span className="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-mono text-zinc-400">
                        px
                      </span>
                    </div>
                  </div>

                </div>
              </div>

              {/* Percentage Scaling Slider & Quick Chips */}
              <div className="space-y-2.5 pt-2 border-t border-zinc-100">
                <div className="flex items-center justify-between">
                  <label className="text-xs font-bold text-zinc-700">
                    Scale Percentage: <span className="font-mono text-zinc-950">{scalePercent}%</span>
                  </label>
                  <div className="flex items-center gap-1">
                    {[25, 50, 75, 100, 200].map((pct) => (
                      <button
                        key={pct}
                        type="button"
                        onClick={() => handleScaleChange(pct)}
                        className={`px-2 py-1 rounded-lg text-xs font-mono font-semibold transition-all cursor-pointer ${
                          scalePercent === pct
                            ? 'bg-zinc-950 text-white shadow-2xs'
                            : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-700'
                        }`}
                      >
                        {pct}%
                      </button>
                    ))}
                  </div>
                </div>

                <input
                  type="range"
                  min={10}
                  max={300}
                  step={1}
                  value={scalePercent}
                  onChange={(e) => handleScaleChange(Number(e.target.value))}
                  className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
                />

                <div className="flex items-center justify-between text-[10px] font-mono text-zinc-400">
                  <span>10% (Mini)</span>
                  <span>50% (Half)</span>
                  <span>100% (Original)</span>
                  <span>200% (Double)</span>
                  <span>300% (Maximum)</span>
                </div>
              </div>

              {/* Format & Quality Settings */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-zinc-100">
                {/* Format Selector */}
                <div>
                  <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                    Export Format
                  </label>
                  <div className="grid grid-cols-3 gap-1.5">
                    {[
                      { id: 'image/png', label: 'PNG', note: 'Lossless' },
                      { id: 'image/jpeg', label: 'JPEG', note: 'Photo' },
                      { id: 'image/webp', label: 'WebP', note: 'Modern' },
                    ].map((fmt) => (
                      <button
                        key={fmt.id}
                        type="button"
                        onClick={() => setFormat(fmt.id as typeof format)}
                        className={`p-2 rounded-xl text-center transition-all cursor-pointer ${
                          format === fmt.id
                            ? 'bg-zinc-950 text-white shadow-xs'
                            : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                        }`}
                      >
                        <div className="text-xs font-bold font-mono">{fmt.label}</div>
                        <div className={`text-[10px] ${format === fmt.id ? 'text-zinc-300' : 'text-zinc-500'}`}>
                          {fmt.note}
                        </div>
                      </button>
                    ))}
                  </div>
                </div>

                {/* Quality Slider */}
                <div>
                  <div className="flex items-center justify-between mb-2">
                    <label className="text-xs font-bold uppercase tracking-wider text-zinc-600">
                      Output Quality: <span className="font-mono text-zinc-950">{quality}%</span>
                    </label>
                  </div>
                  <input
                    type="range"
                    min={40}
                    max={100}
                    value={quality}
                    onChange={(e) => setQuality(Number(e.target.value))}
                    disabled={format === 'image/png'}
                    className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950 disabled:opacity-40"
                  />
                  <span className="text-[10px] font-mono text-zinc-400 mt-1 block">
                    {format === 'image/png' ? 'PNG is natively lossless (100% sharp)' : '92% delivers studio clarity'}
                  </span>
                </div>
              </div>

            </div>

            {/* Side-by-Side Comparison Preview */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              
              {/* Original Preview */}
              <div className="rounded-2xl border border-zinc-200/80 bg-white p-4 space-y-3 shadow-2xs">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-bold text-zinc-500 uppercase tracking-wider">Original Dimensions</span>
                  <span className="text-xs font-mono font-bold text-zinc-800 bg-zinc-100 px-2 py-0.5 rounded">
                    {imageMeta.originalWidth} × {imageMeta.originalHeight}px
                  </span>
                </div>

                <div className="relative aspect-video rounded-xl overflow-hidden bg-zinc-100 border border-zinc-200/60 flex items-center justify-center">
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={imageMeta.dataUrl}
                    alt="Original Image"
                    className="max-h-full max-w-full object-contain"
                  />
                </div>

                <div className="text-[11px] font-mono text-zinc-500 flex items-center justify-between">
                  <span>File: {formatBytes(imageMeta.originalSize)}</span>
                  <span>Ratio: {(imageMeta.originalWidth / imageMeta.originalHeight).toFixed(2)}:1</span>
                </div>
              </div>

              {/* Resized Live Preview */}
              <div className="rounded-2xl border border-zinc-200/80 bg-white p-4 space-y-3 shadow-2xs">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-bold text-zinc-950 uppercase tracking-wider">Resized Target</span>
                  <span className="text-xs font-mono font-extrabold text-zinc-950 bg-zinc-100 px-2 py-0.5 rounded">
                    {width} × {height}px
                  </span>
                </div>

                <div className="relative aspect-video rounded-xl overflow-hidden bg-zinc-100 border border-zinc-200/60 flex items-center justify-center">
                  {isProcessing ? (
                    <div className="flex flex-col items-center gap-2 text-zinc-500">
                      <div className="w-5 h-5 border-2 border-zinc-300 border-t-zinc-900 rounded-full animate-spin" />
                      <span className="text-xs font-mono">Rescaling in RAM...</span>
                    </div>
                  ) : resizedResult ? (
                    /* eslint-disable-next-line @next/next/no-img-element */
                    <img
                      src={resizedResult.dataUrl}
                      alt="Resized Preview"
                      className="max-h-full max-w-full object-contain"
                    />
                  ) : null}
                </div>

                <div className="text-[11px] font-mono text-zinc-500 flex items-center justify-between">
                  <span>Est. Size: {resizedResult ? formatBytes(resizedResult.size) : '...'}</span>
                  <span>Scale: {scalePercent}%</span>
                </div>
              </div>

            </div>

            {/* Primary Action Button */}
            <div className="pt-2">
              <button
                type="button"
                disabled={!resizedResult || isProcessing}
                onClick={handleDownload}
                className="w-full py-4 px-6 rounded-2xl bg-zinc-950 hover:bg-zinc-800 disabled:bg-zinc-300 text-white font-bold text-sm sm:text-base flex items-center justify-center gap-2.5 shadow-lg active:scale-[0.99] transition-all cursor-pointer disabled:cursor-not-allowed"
              >
                <Download className="w-4 h-4" />
                <span>
                  Download Resized Image ({width} × {height}px)
                </span>
              </button>
            </div>

          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
