'use client';

import React, { useState, useRef, useCallback, useEffect } from 'react';
import { 
  UploadCloud, 
  Download, 
  RefreshCw, 
  Crop as CropIcon, 
  FileImage, 
  Trash2, 
  ZoomIn, 
  ZoomOut, 
  RotateCcw, 
  Move,
  Circle,
  Square,
  RectangleHorizontal,
  RectangleVertical,
  Maximize,
  Lock,
  Layers,
  Sparkles
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { 
  loadImageFromFile, 
  cropImageCanvas, 
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

type CropPreset = '1:1' | '4:5' | '16:9' | 'freeform' | 'circle';

interface CropBox {
  x: number;
  y: number;
  width: number;
  height: number;
}

export default function CropImagePage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);

  const [imageMeta, setImageMeta] = useState<ImageMeta | null>(null);
  const [isDraggingFile, setIsDraggingFile] = useState(false);
  const [isProcessing, setIsProcessing] = useState(false);

  // Crop Controls
  const [preset, setPreset] = useState<CropPreset>('1:1');
  const [cropBox, setCropBox] = useState<CropBox>({ x: 0, y: 0, width: 500, height: 500 });
  const [zoomLevel, setZoomLevel] = useState<number>(1); // 1 to 2.5
  const [exportFormat, setExportFormat] = useState<'image/png' | 'image/jpeg'>('image/png');

  // Dragging Crop Box
  const [isDraggingCrop, setIsDraggingCrop] = useState(false);
  const dragStartPos = useRef<{ mouseX: number; mouseY: number; boxX: number; boxY: number }>({
    mouseX: 0,
    mouseY: 0,
    boxX: 0,
    boxY: 0,
  });

  // Cropped Output Result
  const [croppedResult, setCroppedResult] = useState<{
    blob: Blob;
    dataUrl: string;
    size: number;
  } | null>(null);

  // Initialize Crop Box given an aspect ratio preset
  const initCropForPreset = useCallback((p: CropPreset, naturalW: number, naturalH: number) => {
    let targetRatio = 1;
    if (p === '1:1' || p === 'circle') targetRatio = 1;
    else if (p === '4:5') targetRatio = 4 / 5;
    else if (p === '16:9') targetRatio = 16 / 9;
    else targetRatio = naturalW / naturalH; // freeform default

    let boxW = naturalW;
    let boxH = naturalW / targetRatio;

    if (boxH > naturalH) {
      boxH = naturalH;
      boxW = naturalH * targetRatio;
    }

    // Default to 85% of max possible centered crop
    boxW = Math.round(boxW * 0.9);
    boxH = Math.round(boxH * 0.9);
    const boxX = Math.round((naturalW - boxW) / 2);
    const boxY = Math.round((naturalH - boxH) / 2);

    setCropBox({
      x: Math.max(0, boxX),
      y: Math.max(0, boxY),
      width: Math.max(10, boxW),
      height: Math.max(10, boxH),
    });
  }, []);

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
      initCropForPreset('1:1', naturalW, naturalH);
      setPreset('1:1');
      setZoomLevel(1);

      showToast('Image loaded successfully');
    } catch {
      showToast('Could not decode image file.');
    } finally {
      setIsProcessing(false);
    }
  }, [initCropForPreset, showToast]);

  const handleFileDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDraggingFile(true);
  };

  const handleFileDragLeave = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDraggingFile(false);
  };

  const handleFileDrop = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDraggingFile(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFile(e.dataTransfer.files[0]);
    }
  };

  const handlePresetSelect = (newPreset: CropPreset) => {
    setPreset(newPreset);
    if (imageMeta) {
      initCropForPreset(newPreset, imageMeta.originalWidth, imageMeta.originalHeight);
    }
  };

  // Zoom Handler
  const handleZoomChange = (newZoom: number) => {
    setZoomLevel(newZoom);
    if (!imageMeta) return;

    // Adjust crop box size inversely with zoom
    const zoomFactor = 1 / newZoom;
    let targetRatio = cropBox.width / cropBox.height;
    if (preset === '1:1' || preset === 'circle') targetRatio = 1;
    else if (preset === '4:5') targetRatio = 4 / 5;
    else if (preset === '16:9') targetRatio = 16 / 9;

    let baseW = imageMeta.originalWidth * zoomFactor * 0.9;
    let baseH = baseW / targetRatio;
    if (baseH > imageMeta.originalHeight) {
      baseH = imageMeta.originalHeight * zoomFactor * 0.9;
      baseW = baseH * targetRatio;
    }

    baseW = Math.round(Math.max(50, Math.min(imageMeta.originalWidth, baseW)));
    baseH = Math.round(Math.max(50, Math.min(imageMeta.originalHeight, baseH)));

    // Keep centered
    const centerX = cropBox.x + cropBox.width / 2;
    const centerY = cropBox.y + cropBox.height / 2;

    let newX = Math.round(centerX - baseW / 2);
    let newY = Math.round(centerY - baseH / 2);

    newX = Math.max(0, Math.min(imageMeta.originalWidth - baseW, newX));
    newY = Math.max(0, Math.min(imageMeta.originalHeight - baseH, newY));

    setCropBox({ x: newX, y: newY, width: baseW, height: baseH });
  };

  // Mouse Drag on Viewport to Move Crop Box
  const handleMouseDownOnBox = (e: React.MouseEvent) => {
    e.preventDefault();
    setIsDraggingCrop(true);
    dragStartPos.current = {
      mouseX: e.clientX,
      mouseY: e.clientY,
      boxX: cropBox.x,
      boxY: cropBox.y,
    };
  };

  useEffect(() => {
    const handleMouseMove = (e: MouseEvent) => {
      if (!isDraggingCrop || !imageMeta || !containerRef.current) return;

      const containerRect = containerRef.current.getBoundingClientRect();
      const scaleX = imageMeta.originalWidth / containerRect.width;
      const scaleY = imageMeta.originalHeight / containerRect.height;

      const deltaX = (e.clientX - dragStartPos.current.mouseX) * scaleX;
      const deltaY = (e.clientY - dragStartPos.current.mouseY) * scaleY;

      let nextX = Math.round(dragStartPos.current.boxX + deltaX);
      let nextY = Math.round(dragStartPos.current.boxY + deltaY);

      nextX = Math.max(0, Math.min(imageMeta.originalWidth - cropBox.width, nextX));
      nextY = Math.max(0, Math.min(imageMeta.originalHeight - cropBox.height, nextY));

      setCropBox((prev) => ({ ...prev, x: nextX, y: nextY }));
    };

    const handleMouseUp = () => {
      setIsDraggingCrop(false);
    };

    if (isDraggingCrop) {
      window.addEventListener('mousemove', handleMouseMove);
      window.addEventListener('mouseup', handleMouseUp);
    }

    return () => {
      window.removeEventListener('mousemove', handleMouseMove);
      window.removeEventListener('mouseup', handleMouseUp);
    };
  }, [isDraggingCrop, imageMeta, cropBox.width, cropBox.height]);

  // Execute Live Cropping to Canvas
  const executeCrop = useCallback(async () => {
    if (!imageMeta || cropBox.width <= 0 || cropBox.height <= 0) return;

    setIsProcessing(true);
    try {
      const isCircle = preset === 'circle';
      const format = isCircle ? 'image/png' : exportFormat;

      const res = await cropImageCanvas(
        imageMeta.imgElement,
        cropBox.x,
        cropBox.y,
        cropBox.width,
        cropBox.height,
        isCircle,
        format
      );

      setCroppedResult({
        blob: res.blob,
        dataUrl: res.dataUrl,
        size: res.blob.size,
      });
    } catch {
      showToast('Error cropping canvas in memory.');
    } finally {
      setIsProcessing(false);
    }
  }, [imageMeta, cropBox, preset, exportFormat, showToast]);

  useEffect(() => {
    if (imageMeta) {
      const timer = setTimeout(() => {
        executeCrop();
      }, 120);
      return () => clearTimeout(timer);
    }
  }, [imageMeta, cropBox, preset, exportFormat, executeCrop]);

  const handleDownload = () => {
    if (!croppedResult || !imageMeta) return;

    const baseName = imageMeta.name.substring(0, imageMeta.name.lastIndexOf('.')) || imageMeta.name;
    const isCircle = preset === 'circle';
    const ext = isCircle || exportFormat === 'image/png' ? 'png' : 'jpg';
    const outputFilename = `${baseName}-cropped-${preset.replace(':', 'x')}.${ext}`;

    triggerBrowserImageDownload(croppedResult.blob, outputFilename);
    showToast(`Downloaded ${outputFilename}`);
  };

  const handleReset = () => {
    setImageMeta(null);
    setCroppedResult(null);
    if (fileInputRef.current) {
      fileInputRef.current.value = '';
    }
  };

  const faqItems = [
    {
      question: 'How do the aspect ratio presets work?',
      answer: 'Cora provides industry standard ratios including 1:1 Square (Instagram post & avatar), 4:5 Portrait (Instagram feed standard), 16:9 Widescreen (YouTube & presentations), Freeform (unconstrained framing), and Circle (circular avatar preview with transparent PNG background).',
    },
    {
      question: 'Does cropping reduce photo resolution?',
      answer: 'No. Cora crops the original uncompressed source pixels with 1:1 sub-pixel fidelity without downscaling unless requested. If your source image is 4000×3000px, a centered crop will preserve all high-resolution sensor detail.',
    },
    {
      question: 'How does the Circle Avatar crop preserve transparency?',
      answer: 'When you select the Circle preset, Cora applies an HTML5 Canvas circular clipping path and exports as an alpha-channel 32-bit PNG. The area outside the circle is transparent, making it ready for immediate use across Discord, Slack, LinkedIn, and iOS app icons.',
    },
    {
      question: 'Can I pan and reposition the crop frame directly?',
      answer: 'Yes! Simply click and drag anywhere inside the crop box on the interactive viewport, or use the fine-tuning X and Y position sliders. You can also zoom in and out to reframe your subject effortlessly.',
    },
    {
      question: 'Is my photo uploaded to any server or cloud API?',
      answer: 'Zero cloud interaction. Every operation executes strictly in your browser runtime memory via HTML5 Canvas. Your sensitive client deliverables, portraits, and headshots remain 100% private on your machine.',
    },
  ];

  return (
    <ToolPageShell
      toolId="crop-image"
      badgeTag="Precision Framing Engine"
      title="Crop Image Online Free"
      subtitle="Crop photos to 1:1 square, 4:5 portrait, 16:9 widescreen, or circular avatars. Interactive zoom, pan, and sub-pixel precision with zero server uploads."
      faqItems={faqItems}
    >
      <div className="space-y-6">

        {/* ── Dropzone (If no image loaded) ── */}
        {!imageMeta ? (
          <div
            onDragOver={handleFileDragOver}
            onDragLeave={handleFileDragLeave}
            onDrop={handleFileDrop}
            onClick={() => fileInputRef.current?.click()}
            className={`relative rounded-3xl border-2 border-dashed p-8 sm:p-14 text-center cursor-pointer transition-all ${
              isDraggingFile
                ? 'border-zinc-950 bg-zinc-100/80 scale-[0.99]'
                : 'border-zinc-300/80 bg-white hover:border-zinc-500 hover:bg-zinc-50/50 shadow-xs'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept="image/jpeg,image/png,image/webp,image/avif"
              onChange={(e) => e.target.files?.[0] && handleFile(e.target.files[0])}
              className="hidden"
            />

            <div className="flex flex-col items-center justify-center max-w-md mx-auto space-y-4">
              <div className="w-16 h-16 rounded-2xl bg-zinc-100 flex items-center justify-center text-zinc-900 border border-zinc-200 shadow-2xs">
                <CropIcon className="w-8 h-8" />
              </div>

              <div className="space-y-1.5">
                <p className="font-display text-lg sm:text-xl font-bold text-zinc-950">
                  Drop your image here to crop
                </p>
                <p className="text-xs sm:text-sm text-zinc-500 font-normal">
                  Standard aspect ratios &bull; Circular avatar mode &bull; Zero server uploads
                </p>
              </div>

              <div className="pt-2 flex flex-wrap items-center justify-center gap-2">
                <span className="inline-flex items-center gap-1 text-[11px] font-mono text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-md border border-zinc-200">
                  <Square className="w-3 h-3 text-zinc-700" />
                  1:1, 4:5 & 16:9 Presets
                </span>
                <span className="inline-flex items-center gap-1 text-[11px] font-mono text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-md border border-zinc-200">
                  <Circle className="w-3 h-3 text-zinc-700" />
                  Round Avatar Mask
                </span>
              </div>
            </div>
          </div>
        ) : (
          /* ── Active Image Cropper Workspace ── */
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
                    Source: {imageMeta.originalWidth}×{imageMeta.originalHeight}px &bull; {formatBytes(imageMeta.originalSize)}
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
                  onChange={(e) => e.target.files?.[0] && handleFile(e.target.files[0])}
                  className="hidden"
                />
              </div>
            </div>

            {/* Ratio Presets Selector Bar */}
            <div className="p-4 sm:p-5 rounded-3xl bg-white border border-zinc-200/80 shadow-2xs space-y-3">
              <div className="flex items-center justify-between">
                <label className="text-xs font-bold uppercase tracking-wider text-zinc-600 flex items-center gap-1.5">
                  <CropIcon className="w-3.5 h-3.5 text-zinc-700" />
                  <span>Select Aspect Ratio Preset</span>
                </label>
                <span className="text-[11px] font-mono text-zinc-400">
                  Target Crop: {cropBox.width}×{cropBox.height}px
                </span>
              </div>

              <div className="grid grid-cols-2 sm:grid-cols-5 gap-2">
                {[
                  { id: '1:1' as CropPreset, label: '1:1 Square', icon: Square, desc: 'Instagram / Avatar' },
                  { id: '4:5' as CropPreset, label: '4:5 Portrait', icon: RectangleVertical, desc: 'Instagram Feed' },
                  { id: '16:9' as CropPreset, label: '16:9 Widescreen', icon: RectangleHorizontal, desc: 'YouTube / Banner' },
                  { id: 'circle' as CropPreset, label: 'Circle Mask', icon: Circle, desc: 'Round Profile' },
                  { id: 'freeform' as CropPreset, label: 'Freeform', icon: Maximize, desc: 'Custom Bounds' },
                ].map((item) => {
                  const Icon = item.icon;
                  const isActive = preset === item.id;
                  return (
                    <button
                      key={item.id}
                      type="button"
                      onClick={() => handlePresetSelect(item.id)}
                      className={`p-3 rounded-2xl border text-left transition-all cursor-pointer ${
                        isActive
                          ? 'border-zinc-950 bg-zinc-950 text-white shadow-xs'
                          : 'border-zinc-200 bg-zinc-50/80 hover:bg-white text-zinc-800'
                      }`}
                    >
                      <div className="flex items-center gap-2 mb-1">
                        <Icon className="w-4 h-4" />
                        <span className="text-xs font-bold font-mono">{item.label}</span>
                      </div>
                      <div className={`text-[10px] truncate ${isActive ? 'text-zinc-300' : 'text-zinc-500'}`}>
                        {item.desc}
                      </div>
                    </button>
                  );
                })}
              </div>
            </div>

            {/* Interactive Crop Viewport & Controls Grid */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
              
              {/* Left 7 Cols: Interactive Visual Crop Stage */}
              <div className="lg:col-span-7 space-y-3">
                <div className="flex items-center justify-between text-xs font-bold text-zinc-700">
                  <span className="flex items-center gap-1.5">
                    <Move className="w-3.5 h-3.5 text-zinc-600" />
                    Interactive Crop Box (Click & Drag to reposition)
                  </span>
                  <span className="text-[11px] font-mono text-zinc-400">
                    Pos: ({cropBox.x}, {cropBox.y})
                  </span>
                </div>

                <div 
                  ref={containerRef}
                  className="relative rounded-2xl overflow-hidden bg-zinc-900 border border-zinc-700 select-none shadow-md flex items-center justify-center min-h-[340px] max-h-[480px]"
                >
                  {/* Base Image */}
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={imageMeta.dataUrl}
                    alt="Crop Source"
                    className="max-h-[460px] w-full object-contain pointer-events-none"
                  />

                  {/* Dark Vignette Overlay Mask */}
                  <div className="absolute inset-0 bg-black/50 pointer-events-none" />

                  {/* Interactive Highlighted Crop Box Overlay */}
                  {imageMeta.originalWidth > 0 && (
                    <div
                      onMouseDown={handleMouseDownOnBox}
                      style={{
                        position: 'absolute',
                        left: `${(cropBox.x / imageMeta.originalWidth) * 100}%`,
                        top: `${(cropBox.y / imageMeta.originalHeight) * 100}%`,
                        width: `${(cropBox.width / imageMeta.originalWidth) * 100}%`,
                        height: `${(cropBox.height / imageMeta.originalHeight) * 100}%`,
                        borderRadius: preset === 'circle' ? '9999px' : '8px',
                        cursor: isDraggingCrop ? 'grabbing' : 'grab',
                      }}
                      className="border-2 border-white shadow-[0_0_0_9999px_rgba(0,0,0,0.55)] transition-all"
                    >
                      {/* Grid Lines (Rule of Thirds) */}
                      {preset !== 'circle' && (
                        <div className="absolute inset-0 grid grid-cols-3 grid-rows-3 pointer-events-none">
                          <div className="border-r border-b border-white/30" />
                          <div className="border-r border-b border-white/30" />
                          <div className="border-b border-white/30" />
                          <div className="border-r border-b border-white/30" />
                          <div className="border-r border-b border-white/30" />
                          <div className="border-b border-white/30" />
                          <div className="border-r border-white/30" />
                          <div className="border-r border-white/30" />
                          <div />
                        </div>
                      )}

                      {/* Center Drag Handle Badge */}
                      <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div className="px-2 py-0.5 rounded-full bg-black/70 backdrop-blur-sm text-[10px] font-mono text-white border border-white/20 shadow-xs flex items-center gap-1">
                          <Move className="w-2.5 h-2.5" />
                          <span>Drag</span>
                        </div>
                      </div>
                    </div>
                  )}
                </div>

                {/* Zoom & Fine-tune Controls */}
                <div className="p-4 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-3">
                  <div className="flex items-center justify-between">
                    <label className="text-xs font-bold text-zinc-700 flex items-center gap-1.5">
                      <ZoomIn className="w-3.5 h-3.5 text-zinc-950" />
                      <span>Zoom Frame: {zoomLevel.toFixed(1)}x</span>
                    </label>
                    <button
                      type="button"
                      onClick={() => handleZoomChange(1)}
                      className="text-[11px] font-mono text-zinc-500 hover:text-zinc-900 flex items-center gap-1 cursor-pointer"
                    >
                      <RotateCcw className="w-3 h-3" />
                      <span>Reset Zoom</span>
                    </button>
                  </div>

                  <div className="flex items-center gap-3">
                    <ZoomOut className="w-4 h-4 text-zinc-400 shrink-0" />
                    <input
                      type="range"
                      min={1}
                      max={2.5}
                      step={0.1}
                      value={zoomLevel}
                      onChange={(e) => handleZoomChange(Number(e.target.value))}
                      className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
                    />
                    <ZoomIn className="w-4 h-4 text-zinc-800 shrink-0" />
                  </div>
                </div>
              </div>

              {/* Right 5 Cols: Live Result Preview & Export Options */}
              <div className="lg:col-span-5 space-y-4">
                
                {/* Live Preview Card */}
                <div className="p-5 rounded-3xl bg-white border border-zinc-200/80 shadow-2xs space-y-4">
                  <div className="flex items-center justify-between">
                    <span className="text-xs font-bold text-zinc-950 uppercase tracking-wider flex items-center gap-1.5">
                      <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
                      <span>Live Cropped Preview</span>
                    </span>
                    <span className="text-xs font-mono font-bold text-zinc-950 bg-zinc-100 px-2 py-0.5 rounded">
                      {cropBox.width} × {cropBox.height}px
                    </span>
                  </div>

                  {/* Cropped Output Viewport */}
                  <div className="relative aspect-square rounded-2xl overflow-hidden bg-zinc-100 border border-zinc-200 flex items-center justify-center p-2">
                    {/* Checkerboard transparency pattern */}
                    <div 
                      className="absolute inset-0 opacity-20 pointer-events-none"
                      style={{
                        backgroundImage: 'linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%)',
                        backgroundSize: '16px 16px',
                        backgroundPosition: '0 0, 0 8px, 8px -8px, -8px 0px'
                      }}
                    />

                    {isProcessing ? (
                      <div className="flex flex-col items-center gap-2 text-zinc-500 z-10">
                        <div className="w-5 h-5 border-2 border-zinc-300 border-t-zinc-900 rounded-full animate-spin" />
                        <span className="text-xs font-mono">Rendering crop...</span>
                      </div>
                    ) : croppedResult ? (
                      /* eslint-disable-next-line @next/next/no-img-element */
                      <img
                        src={croppedResult.dataUrl}
                        alt="Cropped Result Preview"
                        className={`max-h-full max-w-full object-contain relative z-10 ${
                          preset === 'circle' ? 'rounded-full' : 'rounded-lg'
                        }`}
                      />
                    ) : null}
                  </div>

                  {/* Format Selector */}
                  <div className="pt-2 border-t border-zinc-100 space-y-2">
                    <label className="text-xs font-bold uppercase tracking-wider text-zinc-600 block">
                      Download Format
                    </label>
                    <div className="grid grid-cols-2 gap-2">
                      <button
                        type="button"
                        onClick={() => setExportFormat('image/png')}
                        className={`p-2.5 rounded-xl border text-center transition-all cursor-pointer ${
                          exportFormat === 'image/png' || preset === 'circle'
                            ? 'bg-zinc-950 text-white shadow-xs'
                            : 'bg-zinc-50 border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                        }`}
                      >
                        <div className="text-xs font-mono font-bold">PNG</div>
                        <div className="text-[10px] text-zinc-400">
                          {preset === 'circle' ? 'Transparent Mask' : 'Lossless Sharpness'}
                        </div>
                      </button>

                      <button
                        type="button"
                        disabled={preset === 'circle'}
                        onClick={() => setExportFormat('image/jpeg')}
                        className={`p-2.5 rounded-xl border text-center transition-all cursor-pointer ${
                          exportFormat === 'image/jpeg' && preset !== 'circle'
                            ? 'bg-zinc-950 text-white shadow-xs'
                            : 'bg-zinc-50 border-zinc-200 text-zinc-700 hover:bg-zinc-100 disabled:opacity-40 disabled:cursor-not-allowed'
                        }`}
                      >
                        <div className="text-xs font-mono font-bold">JPEG</div>
                        <div className="text-[10px] text-zinc-400">Photo Standard</div>
                      </button>
                    </div>
                  </div>

                  {/* 1-Click Download Button */}
                  <button
                    type="button"
                    disabled={!croppedResult || isProcessing}
                    onClick={handleDownload}
                    className="w-full py-3.5 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-800 disabled:bg-zinc-300 text-white font-bold text-sm flex items-center justify-center gap-2 shadow-md active:scale-95 transition-all cursor-pointer disabled:cursor-not-allowed"
                  >
                    <Download className="w-4 h-4" />
                    <span>
                      Download Cropped Image ({croppedResult ? formatBytes(croppedResult.size) : 'Ready'})
                    </span>
                  </button>
                </div>

              </div>

            </div>

          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
