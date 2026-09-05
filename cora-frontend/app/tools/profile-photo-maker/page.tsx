'use client';

import React, { useState, useRef, useCallback, useEffect } from 'react';
import { 
  UploadCloud, 
  Download, 
  RefreshCw, 
  User, 
  ZoomIn, 
  ZoomOut, 
  Move, 
  Palette, 
  Sparkles, 
  Trash2, 
  FileImage,
  Circle,
  Sliders,
  RotateCcw,
  Check,
  ShieldCheck
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { 
  loadImageFromFile, 
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

interface BackdropOption {
  id: string;
  name: string;
  type: 'solid' | 'gradient' | 'transparent';
  color1: string;
  color2?: string;
  cssStyle: string;
}

const BACKDROP_OPTIONS: BackdropOption[] = [
  { 
    id: 'transparent', 
    name: 'Transparent', 
    type: 'transparent', 
    color1: 'transparent', 
    cssStyle: 'bg-transparent' 
  },
  { 
    id: 'anthropic-cream', 
    name: 'Anthropic Cream', 
    type: 'solid', 
    color1: '#FBFaf7', 
    cssStyle: 'bg-[#FBFaf7]' 
  },
  { 
    id: 'studio-warm', 
    name: 'Studio Warm', 
    type: 'gradient', 
    color1: '#F9F6F0', 
    color2: '#E8E3D9', 
    cssStyle: 'bg-gradient-to-br from-[#F9F6F0] to-[#E8E3D9]' 
  },
  { 
    id: 'pure-white', 
    name: 'Pure White', 
    type: 'solid', 
    color1: '#FFFFFF', 
    cssStyle: 'bg-white' 
  },
  { 
    id: 'solid-dark', 
    name: 'Solid Dark', 
    type: 'solid', 
    color1: '#09090B', 
    cssStyle: 'bg-zinc-950' 
  },
  { 
    id: 'slate-deep', 
    name: 'Slate Studio', 
    type: 'gradient', 
    color1: '#334155', 
    color2: '#0F172A', 
    cssStyle: 'bg-gradient-to-br from-slate-700 to-slate-900' 
  },
  { 
    id: 'indigo-exec', 
    name: 'Indigo Exec', 
    type: 'gradient', 
    color1: '#4F46E5', 
    color2: '#312E81', 
    cssStyle: 'bg-gradient-to-br from-indigo-600 to-indigo-900' 
  },
  { 
    id: 'emerald-growth', 
    name: 'Emerald', 
    type: 'gradient', 
    color1: '#059669', 
    color2: '#064E3B', 
    cssStyle: 'bg-gradient-to-br from-emerald-600 to-emerald-900' 
  },
];

interface RingColorOption {
  id: string;
  name: string;
  hex: string;
}

const RING_COLORS: RingColorOption[] = [
  { id: 'white', name: 'White', hex: '#FFFFFF' },
  { id: 'zinc-950', name: 'Dark Slate', hex: '#09090B' },
  { id: 'emerald', name: 'Emerald', hex: '#10B981' },
  { id: 'amber', name: 'Amber Gold', hex: '#F59E0B' },
  { id: 'indigo', name: 'Indigo', hex: '#6366F1' },
  { id: 'sky', name: 'Sky Blue', hex: '#0EA5E9' },
];

export default function ProfilePhotoMakerPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);
  const hiddenCanvasRef = useRef<HTMLCanvasElement>(null);

  const [imageMeta, setImageMeta] = useState<ImageMeta | null>(null);
  const [isDraggingFile, setIsDraggingFile] = useState(false);
  const [isProcessing, setIsProcessing] = useState(false);

  // Framing Adjustments
  const [zoom, setZoom] = useState<number>(1.1); // 1.0 to 3.0
  const [panX, setPanX] = useState<number>(0); // -100 to 100 percent
  const [panY, setPanY] = useState<number>(0); // -100 to 100 percent

  // Mouse Drag on Viewport to Pan
  const [isDraggingPan, setIsDraggingPan] = useState(false);
  const dragStartRef = useRef<{ mouseX: number; mouseY: number; startPanX: number; startPanY: number }>({
    mouseX: 0,
    mouseY: 0,
    startPanX: 0,
    startPanY: 0,
  });

  // Backdrop & Styling
  const [selectedBackdrop, setSelectedBackdrop] = useState<BackdropOption>(BACKDROP_OPTIONS[1]); // Anthropic cream default
  const [hasRing, setHasRing] = useState<boolean>(true);
  const [ringColor, setRingColor] = useState<RingColorOption>(RING_COLORS[0]); // White
  const [ringThickness, setRingThickness] = useState<number>(10); // 0 to 24px (scaled on 1024 canvas)

  // Output Result
  const [renderedDataUrl, setRenderedDataUrl] = useState<string | null>(null);
  const [renderedBlob, setRenderedBlob] = useState<Blob | null>(null);

  const handleFile = useCallback(async (file: File) => {
    if (!file.type.startsWith('image/')) {
      showToast('Please upload a valid portrait or headshot image.');
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
      setZoom(1.15);
      setPanX(0);
      setPanY(0);
      showToast('Headshot loaded successfully');
    } catch {
      showToast('Failed to load image file.');
    } finally {
      setIsProcessing(false);
    }
  }, [showToast]);

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDraggingFile(true);
  };

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDraggingFile(false);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDraggingFile(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFile(e.dataTransfer.files[0]);
    }
  };

  // Drag to Pan Handler on Circle Viewport
  const handleMouseDownOnCircle = (e: React.MouseEvent) => {
    e.preventDefault();
    setIsDraggingPan(true);
    dragStartRef.current = {
      mouseX: e.clientX,
      mouseY: e.clientY,
      startPanX: panX,
      startPanY: panY,
    };
  };

  useEffect(() => {
    const handleMouseMove = (e: MouseEvent) => {
      if (!isDraggingPan) return;
      const deltaX = e.clientX - dragStartRef.current.mouseX;
      const deltaY = e.clientY - dragStartRef.current.mouseY;

      // Map pixel drag to pan percentage
      const nextX = Math.round(dragStartRef.current.startPanX + deltaX * 0.4);
      const nextY = Math.round(dragStartRef.current.startPanY + deltaY * 0.4);

      setPanX(Math.max(-100, Math.min(100, nextX)));
      setPanY(Math.max(-100, Math.min(100, nextY)));
    };

    const handleMouseUp = () => {
      setIsDraggingPan(false);
    };

    if (isDraggingPan) {
      window.addEventListener('mousemove', handleMouseMove);
      window.addEventListener('mouseup', handleMouseUp);
    }

    return () => {
      window.removeEventListener('mousemove', handleMouseMove);
      window.removeEventListener('mouseup', handleMouseUp);
    };
  }, [isDraggingPan, panX, panY]);

  // High-Resolution 1024x1024 Canvas Render
  const renderAvatar = useCallback(() => {
    if (!imageMeta) return;

    const canvas = hiddenCanvasRef.current || document.createElement('canvas');
    const size = 1024;
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    ctx.clearRect(0, 0, size, size);

    const centerX = size / 2;
    const centerY = size / 2;
    const radius = (size / 2) - (hasRing ? (ringThickness * 2) : 4);

    // 1. Draw Backdrop in Circle
    ctx.save();
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
    ctx.closePath();
    ctx.clip();

    if (selectedBackdrop.type === 'solid') {
      ctx.fillStyle = selectedBackdrop.color1;
      ctx.fillRect(0, 0, size, size);
    } else if (selectedBackdrop.type === 'gradient' && selectedBackdrop.color2) {
      const grad = ctx.createLinearGradient(0, 0, size, size);
      grad.addColorStop(0, selectedBackdrop.color1);
      grad.addColorStop(1, selectedBackdrop.color2);
      ctx.fillStyle = grad;
      ctx.fillRect(0, 0, size, size);
    } else {
      // Transparent - leave transparent
    }

    // 2. Draw Scaled & Panned Image Inside Circular Clip
    const img = imageMeta.imgElement;
    const naturalW = img.naturalWidth || img.width;
    const naturalH = img.naturalHeight || img.height;

    // Cover calculation
    const imgRatio = naturalW / naturalH;
    let baseW = size;
    let baseH = size;
    if (imgRatio > 1) {
      baseW = size * imgRatio;
      baseH = size;
    } else {
      baseW = size;
      baseH = size / imgRatio;
    }

    const scaledW = baseW * zoom;
    const scaledH = baseH * zoom;

    // Offsets
    const offsetX = centerX - (scaledW / 2) + ((panX / 100) * (size / 2));
    const offsetY = centerY - (scaledH / 2) + ((panY / 100) * (size / 2));

    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = 'high';
    ctx.drawImage(img, offsetX, offsetY, scaledW, scaledH);

    ctx.restore();

    // 3. Draw Outer Ring Border (if active)
    if (hasRing && ringThickness > 0) {
      ctx.save();
      ctx.beginPath();
      const ringR = radius + (ringThickness);
      ctx.arc(centerX, centerY, ringR, 0, Math.PI * 2);
      ctx.strokeStyle = ringColor.hex;
      ctx.lineWidth = ringThickness * 2;
      ctx.stroke();
      ctx.restore();
    }

    // Export Data URL and Blob
    canvas.toBlob((blob) => {
      if (blob) {
        setRenderedBlob(blob);
        setRenderedDataUrl(canvas.toDataURL('image/png'));
      }
    }, 'image/png');
  }, [imageMeta, zoom, panX, panY, selectedBackdrop, hasRing, ringThickness, ringColor]);

  useEffect(() => {
    if (imageMeta) {
      renderAvatar();
    }
  }, [imageMeta, zoom, panX, panY, selectedBackdrop, hasRing, ringThickness, ringColor, renderAvatar]);

  const handleDownload = () => {
    if (!renderedBlob || !imageMeta) return;

    const baseName = imageMeta.name.substring(0, imageMeta.name.lastIndexOf('.')) || imageMeta.name;
    const outputFilename = `${baseName}-profile-avatar.png`;

    triggerBrowserImageDownload(renderedBlob, outputFilename);
    showToast(`Downloaded ${outputFilename}`);
  };

  const handleReset = () => {
    setImageMeta(null);
    setRenderedDataUrl(null);
    setRenderedBlob(null);
    if (fileInputRef.current) {
      fileInputRef.current.value = '';
    }
  };

  const faqItems = [
    {
      question: 'What makes a high-converting profile photo for LinkedIn and WhatsApp Business?',
      answer: 'High-converting avatars feature clean subject framing (shoulders and head filling 65-75% of the circle), an uncluttered studio neutral background (like Anthropic cream or slate), and a subtle high-contrast border ring to pop against dark and light UI themes.',
    },
    {
      question: 'How does the circular framing and positioning work?',
      answer: 'Cora lets you zoom from 1x to 3x and click-and-drag directly on the circular preview to position your face perfectly at eye level. The output renders as a crisp 1024×1024px high-DPI image with sub-pixel anti-aliasing.',
    },
    {
      question: 'Why is the Anthropic cream backdrop recommended?',
      answer: 'Warm cream backgrounds (#FBFaf7 / #F9F6F0) create an elegant, welcoming editorial aesthetic popularized by top design studios. It provides warm organic contrast that looks significantly more polished than generic flat primary colors.',
    },
    {
      question: 'Is the downloaded avatar compatible with Slack, Zoom, and Google Workspace?',
      answer: 'Yes. The 1024×1024 PNG export conforms to the avatar guidelines of LinkedIn, Slack, Zoom, Google Workspace, GitHub, X/Twitter, and WhatsApp with zero downscaling blur.',
    },
    {
      question: 'Are my personal headshots or team photos safe?',
      answer: 'Absolutely. All processing occurs 100% inside your local browser memory using client-side HTML5 Canvas. Your photos are never sent to external servers, cloud AI providers, or remote databases.',
    },
  ];

  return (
    <ToolPageShell
      toolId="profile-photo-maker"
      badgeTag="Brand Identity Studio"
      title="Profile Photo Maker"
      subtitle="Transform casual headshots into clean executive profile photos. Circular framing, custom studio backdrops, and accent ring borders with 100% private in-browser execution."
      faqItems={faqItems}
    >
      <div className="space-y-6">

        {/* Hidden Canvas for High-DPI Output */}
        <canvas ref={hiddenCanvasRef} className="hidden" />

        {/* ── Dropzone (If no image loaded) ── */}
        {!imageMeta ? (
          <div
            onDragOver={handleDragOver}
            onDragLeave={handleDragLeave}
            onDrop={handleDrop}
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
                <User className="w-8 h-8" />
              </div>

              <div className="space-y-1.5">
                <p className="font-display text-lg sm:text-xl font-bold text-zinc-950">
                  Upload your headshot or portrait
                </p>
                <p className="text-xs sm:text-sm text-zinc-500 font-normal">
                  Turn casual selfies into executive avatars &bull; Studio backdrops &bull; Zero server uploads
                </p>
              </div>

              <div className="pt-2 flex flex-wrap items-center justify-center gap-2">
                <span className="inline-flex items-center gap-1 text-[11px] font-mono text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-md border border-zinc-200">
                  <Circle className="w-3 h-3 text-zinc-700" />
                  1024×1024 High-DPI
                </span>
                <span className="inline-flex items-center gap-1 text-[11px] font-mono text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-md border border-zinc-200">
                  <Palette className="w-3 h-3 text-zinc-700" />
                  Anthropic Studio Cream
                </span>
              </div>
            </div>
          </div>
        ) : (
          /* ── Active Profile Photo Studio Workspace ── */
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
                  <span>Replace Photo</span>
                </button>
                <button
                  type="button"
                  onClick={handleReset}
                  className="p-1.5 rounded-xl text-zinc-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                  title="Remove photo"
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

            {/* Main Studio Editor: Two-Column Interactive Layout */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
              
              {/* Left Column: Interactive Avatar Viewport Stage (5 Cols) */}
              <div className="lg:col-span-5 space-y-4">
                
                <div className="p-6 rounded-3xl bg-white border border-zinc-200/80 shadow-2xs space-y-4">
                  <div className="flex items-center justify-between">
                    <span className="text-xs font-bold uppercase tracking-wider text-zinc-950 flex items-center gap-1.5">
                      <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
                      <span>Live Avatar Stage</span>
                    </span>
                    <span className="text-[10px] font-mono text-zinc-500">
                      1024 × 1024px PNG
                    </span>
                  </div>

                  {/* The Interactive Circular Preview Stage */}
                  <div className="relative aspect-square rounded-2xl bg-zinc-100 border border-zinc-200/70 p-4 flex items-center justify-center overflow-hidden">
                    
                    {/* Subtle checkerboard backing */}
                    <div 
                      className="absolute inset-0 opacity-20 pointer-events-none"
                      style={{
                        backgroundImage: 'linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%)',
                        backgroundSize: '16px 16px',
                        backgroundPosition: '0 0, 0 8px, 8px -8px, -8px 0px'
                      }}
                    />

                    {/* Circular Interactive Masked Avatar */}
                    <div
                      onMouseDown={handleMouseDownOnCircle}
                      className="relative w-64 h-64 sm:w-72 sm:h-72 rounded-full overflow-hidden shadow-xl border border-black/10 select-none z-10 transition-shadow hover:shadow-2xl"
                      style={{
                        cursor: isDraggingPan ? 'grabbing' : 'grab',
                      }}
                    >
                      {/* Rendered Live Image */}
                      {renderedDataUrl ? (
                        /* eslint-disable-next-line @next/next/no-img-element */
                        <img
                          src={renderedDataUrl}
                          alt="Rendered Avatar Preview"
                          className="w-full h-full object-cover pointer-events-none"
                        />
                      ) : (
                        <div className="w-full h-full flex items-center justify-center text-zinc-400">
                          <div className="w-5 h-5 border-2 border-zinc-400 border-t-zinc-900 rounded-full animate-spin" />
                        </div>
                      )}

                      {/* Floating Pan Prompt */}
                      <div className="absolute inset-x-0 bottom-2 flex justify-center pointer-events-none">
                        <span className="px-2 py-0.5 rounded-full bg-black/60 backdrop-blur-sm text-[10px] font-mono text-white border border-white/20 flex items-center gap-1 shadow-xs">
                          <Move className="w-2.5 h-2.5" />
                          <span>Drag to Reposition</span>
                        </span>
                      </div>
                    </div>
                  </div>

                  {/* Primary Download Button Under Preview */}
                  <button
                    type="button"
                    disabled={!renderedBlob || isProcessing}
                    onClick={handleDownload}
                    className="w-full py-3.5 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-800 disabled:bg-zinc-300 text-white font-bold text-sm flex items-center justify-center gap-2 shadow-md active:scale-95 transition-all cursor-pointer disabled:cursor-not-allowed"
                  >
                    <Download className="w-4 h-4" />
                    <span>Download Executive Avatar (1024×1024)</span>
                  </button>

                </div>

              </div>

              {/* Right Column: Framing, Backdrop & Border Ring Controls (7 Cols) */}
              <div className="lg:col-span-7 space-y-5">
                
                {/* 1. Zoom & Pan Controls */}
                <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/80 shadow-2xs space-y-4">
                  <div className="flex items-center justify-between">
                    <label className="text-xs font-bold uppercase tracking-wider text-zinc-700 flex items-center gap-1.5">
                      <Sliders className="w-3.5 h-3.5 text-zinc-950" />
                      <span>Framing & Face Scale</span>
                    </label>
                    <button
                      type="button"
                      onClick={() => {
                        setZoom(1.15);
                        setPanX(0);
                        setPanY(0);
                      }}
                      className="text-[11px] font-mono text-zinc-500 hover:text-zinc-900 flex items-center gap-1 cursor-pointer"
                    >
                      <RotateCcw className="w-3 h-3" />
                      <span>Reset Position</span>
                    </button>
                  </div>

                  {/* Zoom Slider */}
                  <div className="space-y-1.5">
                    <div className="flex items-center justify-between text-xs">
                      <span className="font-medium text-zinc-600">Zoom Scale</span>
                      <span className="font-mono font-bold text-zinc-950">{Math.round(zoom * 100)}%</span>
                    </div>
                    <div className="flex items-center gap-3">
                      <ZoomOut className="w-4 h-4 text-zinc-400 shrink-0" />
                      <input
                        type="range"
                        min={0.8}
                        max={2.5}
                        step={0.05}
                        value={zoom}
                        onChange={(e) => setZoom(Number(e.target.value))}
                        className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
                      />
                      <ZoomIn className="w-4 h-4 text-zinc-800 shrink-0" />
                    </div>
                  </div>

                  {/* Pan X and Pan Y Fine-Tuning */}
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div className="space-y-1">
                      <div className="flex items-center justify-between text-[11px]">
                        <span className="text-zinc-600 font-mono">Horizontal Pan (X)</span>
                        <span className="font-mono font-bold text-zinc-950">{panX}%</span>
                      </div>
                      <input
                        type="range"
                        min={-100}
                        max={100}
                        value={panX}
                        onChange={(e) => setPanX(Number(e.target.value))}
                        className="w-full h-1.5 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
                      />
                    </div>

                    <div className="space-y-1">
                      <div className="flex items-center justify-between text-[11px]">
                        <span className="text-zinc-600 font-mono">Vertical Pan (Y)</span>
                        <span className="font-mono font-bold text-zinc-950">{panY}%</span>
                      </div>
                      <input
                        type="range"
                        min={-100}
                        max={100}
                        value={panY}
                        onChange={(e) => setPanY(Number(e.target.value))}
                        className="w-full h-1.5 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
                      />
                    </div>
                  </div>
                </div>

                {/* 2. Studio Backdrop Options */}
                <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/80 shadow-2xs space-y-3">
                  <div className="flex items-center justify-between">
                    <label className="text-xs font-bold uppercase tracking-wider text-zinc-700 flex items-center gap-1.5">
                      <Palette className="w-3.5 h-3.5 text-zinc-950" />
                      <span>Studio Backdrop Color</span>
                    </label>
                    <span className="text-[11px] font-mono text-zinc-400">
                      {selectedBackdrop.name}
                    </span>
                  </div>

                  <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    {BACKDROP_OPTIONS.map((bd) => {
                      const isSelected = selectedBackdrop.id === bd.id;
                      return (
                        <button
                          key={bd.id}
                          type="button"
                          onClick={() => setSelectedBackdrop(bd)}
                          className={`p-2 rounded-2xl border text-left flex items-center gap-2.5 transition-all cursor-pointer ${
                            isSelected
                              ? 'border-zinc-950 bg-zinc-950 text-white shadow-xs'
                              : 'border-zinc-200 bg-zinc-50/80 hover:bg-white text-zinc-800'
                          }`}
                        >
                          <div 
                            className={`w-6 h-6 rounded-full border shrink-0 ${bd.cssStyle} ${
                              bd.id === 'transparent' ? 'border-dashed border-zinc-400' : 'border-black/15'
                            }`}
                          />
                          <div className="min-w-0 flex-1">
                            <p className="text-xs font-bold truncate leading-tight">{bd.name}</p>
                          </div>
                        </button>
                      );
                    })}
                  </div>
                </div>

                {/* 3. Circular Border Ring Toggle & Customizer */}
                <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/80 shadow-2xs space-y-4">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                      <Circle className="w-3.5 h-3.5 text-zinc-950" />
                      <label className="text-xs font-bold uppercase tracking-wider text-zinc-700">
                        Circular Border Ring
                      </label>
                    </div>

                    {/* Ring Toggle */}
                    <button
                      type="button"
                      onClick={() => setHasRing(!hasRing)}
                      className={`px-3 py-1 rounded-full text-xs font-mono font-bold transition-all cursor-pointer ${
                        hasRing
                          ? 'bg-zinc-950 text-white'
                          : 'bg-zinc-100 text-zinc-500 hover:bg-zinc-200'
                      }`}
                    >
                      {hasRing ? 'Ring Active' : 'Off'}
                    </button>
                  </div>

                  {hasRing && (
                    <div className="space-y-4 pt-1 animate-in fade-in duration-200">
                      
                      {/* Color Presets */}
                      <div className="space-y-1.5">
                        <span className="text-[11px] font-mono text-zinc-500 block">Border Ring Color</span>
                        <div className="flex flex-wrap items-center gap-2">
                          {RING_COLORS.map((rc) => {
                            const isSelected = ringColor.id === rc.id;
                            return (
                              <button
                                key={rc.id}
                                type="button"
                                onClick={() => setRingColor(rc)}
                                className={`px-2.5 py-1.5 rounded-xl border text-xs font-medium flex items-center gap-2 transition-all cursor-pointer ${
                                  isSelected
                                    ? 'border-zinc-950 bg-zinc-100 text-zinc-950 font-bold shadow-xs'
                                    : 'border-zinc-200 bg-white text-zinc-600 hover:bg-zinc-50'
                                }`}
                              >
                                <span 
                                  className="w-3 h-3 rounded-full border border-black/20"
                                  style={{ backgroundColor: rc.hex }}
                                />
                                <span>{rc.name}</span>
                              </button>
                            );
                          })}
                        </div>
                      </div>

                      {/* Stroke Thickness Slider */}
                      <div className="space-y-1.5">
                        <div className="flex items-center justify-between text-[11px] font-mono">
                          <span className="text-zinc-600">Stroke Thickness</span>
                          <span className="font-bold text-zinc-950">{ringThickness}px</span>
                        </div>
                        <input
                          type="range"
                          min={2}
                          max={24}
                          value={ringThickness}
                          onChange={(e) => setRingThickness(Number(e.target.value))}
                          className="w-full h-1.5 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950"
                        />
                      </div>

                    </div>
                  )}
                </div>

              </div>

            </div>

          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
