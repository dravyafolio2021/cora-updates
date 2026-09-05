'use client';

import React, { useState, useRef, useEffect, useCallback } from 'react';
import { 
  UploadCloud, 
  Download, 
  Sparkles, 
  ShieldCheck, 
  RotateCcw, 
  Type, 
  Image as ImageIcon, 
  Grid, 
  Sliders, 
  Layers, 
  Check, 
  Maximize2, 
  RefreshCw,
  Eye,
  CheckCircle2,
  FileCheck,
  AlertCircle
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { formatBytes, triggerBrowserImageDownload } from '@/lib/image-engine';

const FAQS = [
  {
    question: 'Why should I watermark client review proofs before contract signing?',
    answer: 'Sending unmarked high-resolution previews exposes photographers, studios, and agencies to client ghosting or unauthorized social posting before milestones are settled. Applying a semi-transparent diagonal proof watermark ensures clients can review composition and color while preserving intellectual property.',
  },
  {
    question: 'Are my original photos or watermark logos uploaded to any server?',
    answer: 'Never. The entire compositing, font rendering, rotation matrix calculations, and pixel blending execute 100% locally in your browser RAM using an HTML5 2D canvas context. No files ever leave your device.',
  },
  {
    question: 'Can I upload my own transparent PNG studio logo?',
    answer: 'Yes. Switch the Watermark Type to "Logo Overlay" to upload your brand’s transparent PNG, SVG, or WebP logo. You can adjust its opacity, scale size, rotation angle, and repeat it in a tiled security grid.',
  },
  {
    question: 'What is the advantage of the Tiled Grid pattern?',
    answer: 'A single watermark in a corner can easily be cropped out. The repeated Tiled Grid pattern stamps the watermark at an angle across the entire surface of the photograph at even intervals, making it impossible to crop without destroying the subject.',
  },
  {
    question: 'Does applying a watermark reduce the resolution of my base photo?',
    answer: 'No. The canvas renders at the exact natural pixel dimensions of your original photo (e.g. 6000×4000 full-frame DSLR capture) and exports a lossless PNG or high-quality JPEG without downscaling.',
  },
];

type WatermarkMode = 'text' | 'logo';
type PositionMode = 'center' | 'bottom-right' | 'top-right' | 'bottom-left' | 'top-left';

export default function WatermarkImagePage() {
  const { showToast } = useToast();
  const baseFileInputRef = useRef<HTMLInputElement>(null);
  const logoFileInputRef = useRef<HTMLInputElement>(null);
  const previewCanvasRef = useRef<HTMLCanvasElement>(null);

  // State
  const [baseImage, setBaseImage] = useState<HTMLImageElement | null>(null);
  const [baseFile, setBaseFile] = useState<File | null>(null);
  const [isDragging, setIsDragging] = useState(false);

  // Watermark Settings
  const [mode, setMode] = useState<WatermarkMode>('text');
  const [text, setText] = useState<string>('PROOF - AARAV MEHTA STUDIO');
  const [textColor, setTextColor] = useState<string>('#FFFFFF');
  const [opacity, setOpacity] = useState<number>(45); // 10% to 100%
  const [fontSize, setFontSize] = useState<number>(42); // Scaled based on image width
  const [rotation, setRotation] = useState<number>(-30); // -90 to +90 deg
  const [isTiled, setIsTiled] = useState<boolean>(true);
  const [position, setPosition] = useState<PositionMode>('center');
  const [exportFormat, setExportFormat] = useState<'image/jpeg' | 'image/png'>('image/jpeg');

  // Logo Overlay
  const [logoImage, setLogoImage] = useState<HTMLImageElement | null>(null);
  const [logoFile, setLogoFile] = useState<File | null>(null);
  const [logoScale, setLogoScale] = useState<number>(30); // % of image width

  // Status
  const [isRendering, setIsRendering] = useState(false);
  const [renderedBlob, setRenderedBlob] = useState<Blob | null>(null);
  const [outputStats, setOutputStats] = useState<{ width: number; height: number; originalSize: number; resultSize: number } | null>(null);

  // Quick text presets
  const TEXT_PRESETS = [
    'PROOF - AARAV MEHTA STUDIO',
    'CONFIDENTIAL',
    'CLIENT DRAFT - NOT FINAL',
    'DO NOT COPY OR DISTRIBUTE',
    'PREVIEW ONLY',
  ];

  // Load sample demo photograph
  const handleLoadSample = useCallback(() => {
    const canvas = document.createElement('canvas');
    canvas.width = 1200;
    canvas.height = 800;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Elegant monochrome studio architectural gradient
    const grad = ctx.createLinearGradient(0, 0, 1200, 800);
    grad.addColorStop(0, '#18181B');
    grad.addColorStop(0.5, '#27272A');
    grad.addColorStop(1, '#09090B');
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, 1200, 800);

    // Architectural geometric lines
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.08)';
    ctx.lineWidth = 2;
    for (let i = 0; i < 1200; i += 80) {
      ctx.beginPath();
      ctx.moveTo(i, 0);
      ctx.lineTo(i + 300, 800);
      ctx.stroke();
    }

    // Focal subject card
    ctx.fillStyle = '#FAFAFA';
    ctx.beginPath();
    ctx.roundRect(350, 240, 500, 320, 20);
    ctx.fill();

    ctx.fillStyle = '#09090B';
    ctx.font = '600 28px sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('ARCHITECTURAL PORTFOLIO', 600, 380);

    ctx.fillStyle = '#71717A';
    ctx.font = '16px sans-serif';
    ctx.fillText('Residential Penthouse Project • Mumbai', 600, 420);

    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
    const img = new Image();
    img.onload = () => {
      setBaseImage(img);
      setBaseFile(null);
      setOutputStats({
        width: 1200,
        height: 800,
        originalSize: Math.round(dataUrl.length * 0.75),
        resultSize: 0,
      });
      showToast('Loaded demo architectural photograph');
    };
    img.src = dataUrl;
  }, [showToast]);

  // Load Base Image File
  const handleBaseFile = useCallback((file: File) => {
    if (!file.type.startsWith('image/')) {
      showToast('Please upload a valid image file (JPG, PNG, WebP).');
      return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = new Image();
      img.onload = () => {
        setBaseImage(img);
        setBaseFile(file);
        setOutputStats({
          width: img.naturalWidth,
          height: img.naturalHeight,
          originalSize: file.size,
          resultSize: 0,
        });
        showToast(`Loaded ${file.name} (${img.naturalWidth}×${img.naturalHeight})`);
      };
      img.src = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }, [showToast]);

  // Load Logo Overlay File
  const handleLogoFile = (file: File) => {
    if (!file.type.startsWith('image/')) {
      showToast('Please upload a valid logo image (PNG or SVG recommended).');
      return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = new Image();
      img.onload = () => {
        setLogoImage(img);
        setLogoFile(file);
        showToast(`Loaded logo: ${file.name}`);
      };
      img.src = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  };

  // Re-composite watermark onto canvas
  useEffect(() => {
    if (!baseImage || !previewCanvasRef.current) return;

    setIsRendering(true);
    const canvas = previewCanvasRef.current;
    const w = baseImage.naturalWidth || baseImage.width;
    const h = baseImage.naturalHeight || baseImage.height;
    canvas.width = w;
    canvas.height = h;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // 1. Draw base photo
    ctx.drawImage(baseImage, 0, 0, w, h);

    // 2. Set watermark global alpha
    ctx.save();
    ctx.globalAlpha = opacity / 100;

    const rad = (rotation * Math.PI) / 180;

    if (mode === 'text' && text.trim().length > 0) {
      // Dynamic responsive font scale relative to base image width
      const computedPx = Math.max(16, Math.round((fontSize / 1000) * w));
      ctx.font = `bold ${computedPx}px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`;
      ctx.fillStyle = textColor;
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';

      // Drop shadow for legibility across light and dark photos
      ctx.shadowColor = textColor === '#FFFFFF' ? 'rgba(0, 0, 0, 0.6)' : 'rgba(255, 255, 255, 0.4)';
      ctx.shadowBlur = Math.round(computedPx * 0.15);
      ctx.shadowOffsetX = 1;
      ctx.shadowOffsetY = 1;

      if (isTiled) {
        // Tiled Grid Stamp
        const stepX = Math.round(w / 3);
        const stepY = Math.round(h / 3);
        const marginX = stepX / 2;
        const marginY = stepY / 2;

        for (let x = marginX; x < w + stepX; x += stepX) {
          for (let y = marginY; y < h + stepY; y += stepY) {
            ctx.save();
            ctx.translate(x, y);
            ctx.rotate(rad);
            ctx.fillText(text, 0, 0);
            ctx.restore();
          }
        }
      } else {
        // Single Stamp Position
        let posX = w / 2;
        let posY = h / 2;
        const padding = Math.round(w * 0.08);

        if (position === 'top-left') {
          posX = padding;
          posY = padding;
        } else if (position === 'top-right') {
          posX = w - padding;
          posY = padding;
        } else if (position === 'bottom-left') {
          posX = padding;
          posY = h - padding;
        } else if (position === 'bottom-right') {
          posX = w - padding;
          posY = h - padding;
        }

        ctx.save();
        ctx.translate(posX, posY);
        ctx.rotate(rad);
        ctx.fillText(text, 0, 0);
        ctx.restore();
      }
    } else if (mode === 'logo' && logoImage) {
      const targetLogoW = Math.round(w * (logoScale / 100));
      const aspect = (logoImage.naturalHeight || logoImage.height) / (logoImage.naturalWidth || logoImage.width);
      const targetLogoH = Math.round(targetLogoW * aspect);

      if (isTiled) {
        const stepX = Math.round(w / 2.5);
        const stepY = Math.round(h / 2.5);
        for (let x = stepX / 2; x < w + stepX; x += stepX) {
          for (let y = stepY / 2; y < h + stepY; y += stepY) {
            ctx.save();
            ctx.translate(x, y);
            ctx.rotate(rad);
            ctx.drawImage(logoImage, -targetLogoW / 2, -targetLogoH / 2, targetLogoW, targetLogoH);
            ctx.restore();
          }
        }
      } else {
        let posX = w / 2;
        let posY = h / 2;
        const padding = Math.round(w * 0.05);

        if (position === 'top-left') {
          posX = padding + targetLogoW / 2;
          posY = padding + targetLogoH / 2;
        } else if (position === 'top-right') {
          posX = w - padding - targetLogoW / 2;
          posY = padding + targetLogoH / 2;
        } else if (position === 'bottom-left') {
          posX = padding + targetLogoW / 2;
          posY = h - padding - targetLogoH / 2;
        } else if (position === 'bottom-right') {
          posX = w - padding - targetLogoW / 2;
          posY = h - padding - targetLogoH / 2;
        }

        ctx.save();
        ctx.translate(posX, posY);
        ctx.rotate(rad);
        ctx.drawImage(logoImage, -targetLogoW / 2, -targetLogoH / 2, targetLogoW, targetLogoH);
        ctx.restore();
      }
    }

    ctx.restore();

    // Export blob for download
    canvas.toBlob((blob) => {
      if (blob) {
        setRenderedBlob(blob);
        setOutputStats((prev) => prev ? { ...prev, resultSize: blob.size } : null);
      }
      setIsRendering(false);
    }, exportFormat, 0.92);
  }, [baseImage, mode, text, textColor, opacity, fontSize, rotation, isTiled, position, logoImage, logoScale, exportFormat]);

  const handleDownload = () => {
    if (!renderedBlob) {
      showToast('No watermarked image ready to download.');
      return;
    }
    const ext = exportFormat === 'image/png' ? 'png' : 'jpg';
    const originalName = baseFile ? baseFile.name.replace(/\.[^/.]+$/, '') : 'studio-deliverable';
    triggerBrowserImageDownload(renderedBlob, `${originalName}-watermarked.${ext}`);
    showToast(`Downloaded watermarked ${ext.toUpperCase()}`);
  };

  return (
    <ToolPageShell
      toolId="watermark-image"
      badgeTag="PROOFING & IP"
      title="Studio Watermark Protector"
      subtitle="Stamp intellectual property notices, client proof banners, and logo overlays across high-res photos in browser memory."
      faqItems={FAQS}
    >
      <div className="space-y-6">
        <div className="bg-white border border-zinc-200 rounded-2xl p-4 sm:p-6 shadow-sm">
          {!baseImage ? (
            /* Upload Dropzone */
            <div
              onDragOver={(e) => { e.preventDefault(); setIsDragging(true); }}
              onDragLeave={() => setIsDragging(false)}
              onDrop={(e) => {
                e.preventDefault();
                setIsDragging(false);
                if (e.dataTransfer.files?.[0]) handleBaseFile(e.dataTransfer.files[0]);
              }}
              onClick={() => baseFileInputRef.current?.click()}
              className={`relative cursor-pointer border-2 border-dashed rounded-xl p-8 sm:p-12 text-center transition-all ${
                isDragging 
                  ? 'border-zinc-900 bg-zinc-50/80 scale-[0.99]' 
                  : 'border-zinc-200 hover:border-zinc-400 bg-[#FAFAF9]'
              }`}
            >
              <input
                ref={baseFileInputRef}
                type="file"
                accept="image/png, image/jpeg, image/webp"
                className="hidden"
                onChange={(e) => {
                  if (e.target.files?.[0]) handleBaseFile(e.target.files[0]);
                }}
              />
              <div className="flex flex-col items-center justify-center space-y-3">
                <div className="w-14 h-14 rounded-2xl bg-white border border-zinc-200 shadow-sm flex items-center justify-center">
                  <UploadCloud className="w-7 h-7 text-zinc-800" strokeWidth={1.8} />
                </div>
                <div>
                  <p className="text-base font-semibold text-zinc-900">
                    Upload base photo to watermark
                  </p>
                  <p className="text-xs text-zinc-700 mt-1">
                    Supports high-resolution PNG, JPG, and WebP (up to 50MB)
                  </p>
                </div>

                <div className="pt-2 flex flex-wrap items-center justify-center gap-3">
                  <span className="inline-flex items-center gap-1.5 text-xs text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-full">
                    <ShieldCheck className="w-3.5 h-3.5 text-zinc-700" strokeWidth={2} />
                    100% Client-Side Privacy
                  </span>
                  <button
                    type="button"
                    onClick={(e) => {
                      e.stopPropagation();
                      handleLoadSample();
                    }}
                    className="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-900 bg-white border border-zinc-200 px-3 py-1 rounded-full hover:bg-zinc-50 shadow-xs transition-colors"
                  >
                    <Sparkles className="w-3.5 h-3.5 text-zinc-700" strokeWidth={2} />
                    Try Demo Architectural Photo
                  </button>
                </div>
              </div>
            </div>
          ) : (
            /* Active Watermarking Studio Workspace */
            <div className="space-y-6">
              <div className="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-zinc-100">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center">
                    <ImageIcon className="w-5 h-5 text-zinc-800" strokeWidth={1.8} />
                  </div>
                  <div>
                    <h3 className="text-sm font-semibold text-zinc-900">
                      {baseFile ? baseFile.name : 'Architectural Demo Proof'}
                    </h3>
                    <p className="text-xs text-zinc-700">
                      {outputStats?.width} × {outputStats?.height} px • {formatBytes(outputStats?.originalSize || 0)}
                    </p>
                  </div>
                </div>

                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={() => {
                      setBaseImage(null);
                      setBaseFile(null);
                      setLogoImage(null);
                      setRenderedBlob(null);
                    }}
                    className="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-600 hover:text-zinc-900 bg-zinc-100 hover:bg-zinc-200/80 px-3 py-1.5 rounded-lg transition-colors"
                  >
                    <RotateCcw className="w-3.5 h-3.5" strokeWidth={1.8} />
                    New Photo
                  </button>
                  <button
                    type="button"
                    onClick={handleDownload}
                    disabled={!renderedBlob}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-zinc-900 hover:bg-zinc-800 px-4 py-1.5 rounded-lg shadow-sm transition-colors disabled:opacity-50"
                  >
                    <Download className="w-3.5 h-3.5" strokeWidth={1.8} />
                    Download Proof ({exportFormat === 'image/png' ? 'PNG' : 'JPG'})
                  </button>
                </div>
              </div>

              {/* Mode Toggle: Text Watermark vs Logo Overlay */}
              <div className="flex items-center gap-2">
                <div className="inline-flex rounded-lg bg-zinc-100 p-0.5 border border-zinc-200">
                  <button
                    type="button"
                    onClick={() => setMode('text')}
                    className={`inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-md transition-colors ${
                      mode === 'text' ? 'bg-white text-zinc-900 shadow-xs' : 'text-zinc-600 hover:text-zinc-900'
                    }`}
                  >
                    <Type className="w-3.5 h-3.5" strokeWidth={1.8} />
                    Text Watermark
                  </button>
                  <button
                    type="button"
                    onClick={() => setMode('logo')}
                    className={`inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-md transition-colors ${
                      mode === 'logo' ? 'bg-white text-zinc-900 shadow-xs' : 'text-zinc-600 hover:text-zinc-900'
                    }`}
                  >
                    <ImageIcon className="w-3.5 h-3.5" strokeWidth={1.8} />
                    Logo Overlay
                  </button>
                </div>
              </div>

              {/* Parameter Controls Panel */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                {/* Column 1: Watermark Content */}
                <div className="space-y-3 bg-[#FAFAF9] p-4 rounded-xl border border-zinc-200/80">
                  {mode === 'text' ? (
                    <>
                      <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                        Stamp Text
                      </label>
                      <input
                        type="text"
                        value={text}
                        onChange={(e) => setText(e.target.value)}
                        placeholder="e.g. PROOF - AARAV MEHTA STUDIO"
                        className="w-full text-xs font-medium px-3 py-2 rounded-lg border border-zinc-300 bg-white text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900"
                      />

                      {/* Quick presets */}
                      <div className="space-y-1.5 pt-1">
                        <span className="text-[10px] uppercase font-semibold text-zinc-600">Quick Presets</span>
                        <div className="flex flex-wrap gap-1.5">
                          {TEXT_PRESETS.map((preset) => (
                            <button
                              key={preset}
                              type="button"
                              onClick={() => setText(preset)}
                              className="text-[10px] font-medium bg-white hover:bg-zinc-100 text-zinc-700 px-2 py-0.5 rounded border border-zinc-200 transition-colors"
                            >
                              {preset}
                            </button>
                          ))}
                        </div>
                      </div>

                      {/* Color Selector */}
                      <div className="pt-2 flex items-center justify-between">
                        <span className="text-xs text-zinc-600">Color</span>
                        <div className="flex items-center gap-1.5">
                          {[
                            { hex: '#FFFFFF', label: 'White' },
                            { hex: '#18181B', label: 'Dark' },
                            { hex: '#DC2626', label: 'Red' },
                            { hex: '#D97706', label: 'Gold' },
                          ].map((c) => (
                            <button
                              key={c.hex}
                              type="button"
                              onClick={() => setTextColor(c.hex)}
                              title={c.label}
                              className={`w-5 h-5 rounded-full border transition-all ${
                                textColor === c.hex ? 'ring-2 ring-zinc-900 scale-110' : 'border-zinc-300'
                              }`}
                              style={{ backgroundColor: c.hex }}
                            />
                          ))}
                        </div>
                      </div>
                    </>
                  ) : (
                    <>
                      <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                        Upload Brand Logo
                      </label>
                      <input
                        ref={logoFileInputRef}
                        type="file"
                        accept="image/png, image/svg+xml, image/webp"
                        className="hidden"
                        onChange={(e) => {
                          if (e.target.files?.[0]) handleLogoFile(e.target.files[0]);
                        }}
                      />
                      <button
                        type="button"
                        onClick={() => logoFileInputRef.current?.click()}
                        className="w-full py-3 px-3 border border-dashed border-zinc-300 hover:border-zinc-500 rounded-lg text-xs font-medium text-zinc-700 bg-white flex items-center justify-center gap-2 transition-colors"
                      >
                        <UploadCloud className="w-4 h-4" strokeWidth={1.8} />
                        {logoImage ? 'Replace Logo Image' : 'Select PNG / SVG Logo'}
                      </button>

                      {logoImage && (
                        <div className="pt-2">
                          <div className="flex items-center justify-between text-xs text-zinc-600 mb-1">
                            <span>Logo Scale</span>
                            <span className="font-mono font-medium">{logoScale}%</span>
                          </div>
                          <input
                            type="range"
                            min="10"
                            max="70"
                            value={logoScale}
                            onChange={(e) => setLogoScale(Number(e.target.value))}
                            className="w-full accent-zinc-900 cursor-pointer h-1.5 bg-zinc-200 rounded-lg appearance-none"
                          />
                        </div>
                      )}
                    </>
                  )}
                </div>

                {/* Column 2: Opacity & Sizing */}
                <div className="space-y-3 bg-[#FAFAF9] p-4 rounded-xl border border-zinc-200/80">
                  <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Opacity & Scale
                  </label>

                  <div>
                    <div className="flex items-center justify-between text-xs text-zinc-600 mb-1">
                      <span>Opacity</span>
                      <span className="font-mono font-medium">{opacity}%</span>
                    </div>
                    <input
                      type="range"
                      min="10"
                      max="100"
                      value={opacity}
                      onChange={(e) => setOpacity(Number(e.target.value))}
                      className="w-full accent-zinc-900 cursor-pointer h-1.5 bg-zinc-200 rounded-lg appearance-none"
                    />
                  </div>

                  {mode === 'text' && (
                    <div className="pt-2">
                      <div className="flex items-center justify-between text-xs text-zinc-600 mb-1">
                        <span>Font Size</span>
                        <span className="font-mono font-medium">{fontSize}</span>
                      </div>
                      <input
                        type="range"
                        min="16"
                        max="90"
                        value={fontSize}
                        onChange={(e) => setFontSize(Number(e.target.value))}
                        className="w-full accent-zinc-900 cursor-pointer h-1.5 bg-zinc-200 rounded-lg appearance-none"
                      />
                    </div>
                  )}

                  <div className="pt-2">
                    <div className="flex items-center justify-between text-xs text-zinc-600 mb-1">
                      <span>Rotation</span>
                      <span className="font-mono font-medium">{rotation}°</span>
                    </div>
                    <input
                      type="range"
                      min="-90"
                      max="90"
                      value={rotation}
                      onChange={(e) => setRotation(Number(e.target.value))}
                      className="w-full accent-zinc-900 cursor-pointer h-1.5 bg-zinc-200 rounded-lg appearance-none"
                    />
                  </div>
                </div>

                {/* Column 3: Layout & Pattern */}
                <div className="space-y-3 bg-[#FAFAF9] p-4 rounded-xl border border-zinc-200/80">
                  <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Pattern & Placement
                  </label>

                  <div className="flex items-center justify-between pt-1">
                    <span className="text-xs text-zinc-700 font-medium">Repeated Tiled Grid</span>
                    <button
                      type="button"
                      onClick={() => setIsTiled(!isTiled)}
                      className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors ${
                        isTiled ? 'bg-zinc-900' : 'bg-zinc-300'
                      }`}
                    >
                      <span
                        className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform ${
                          isTiled ? 'translate-x-4.5' : 'translate-x-1'
                        }`}
                      />
                    </button>
                  </div>

                  {!isTiled && (
                    <div className="pt-2">
                      <span className="text-xs text-zinc-600 block mb-1.5">Stamp Anchor Position</span>
                      <div className="grid grid-cols-3 gap-1">
                        {[
                          { id: 'top-left', label: 'TL' },
                          { id: 'top-right', label: 'TR' },
                          { id: 'center', label: 'Center' },
                          { id: 'bottom-left', label: 'BL' },
                          { id: 'bottom-right', label: 'BR' },
                        ].map((pos) => (
                          <button
                            key={pos.id}
                            type="button"
                            onClick={() => setPosition(pos.id as PositionMode)}
                            className={`py-1 text-xs rounded border transition-colors ${
                              position === pos.id
                                ? 'bg-zinc-900 text-white border-zinc-900 font-medium'
                                : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                            }`}
                          >
                            {pos.label}
                          </button>
                        ))}
                      </div>
                    </div>
                  )}

                  <div className="pt-2">
                    <span className="text-xs text-zinc-600 block mb-1.5">Export Format</span>
                    <div className="flex gap-2">
                      <button
                        type="button"
                        onClick={() => setExportFormat('image/jpeg')}
                        className={`flex-1 py-1 text-xs font-medium rounded border transition-colors ${
                          exportFormat === 'image/jpeg' ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-700 border-zinc-200'
                        }`}
                      >
                        JPEG (High Res)
                      </button>
                      <button
                        type="button"
                        onClick={() => setExportFormat('image/png')}
                        className={`flex-1 py-1 text-xs font-medium rounded border transition-colors ${
                          exportFormat === 'image/png' ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-700 border-zinc-200'
                        }`}
                      >
                        Lossless PNG
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              {/* Render Status */}
              <div className="flex items-center justify-between pt-1">
                <span className="text-xs font-medium text-zinc-600">Live Protected Preview</span>
                {isRendering && (
                  <span className="inline-flex items-center gap-1.5 text-xs text-zinc-700">
                    <RefreshCw className="w-3 h-3 animate-spin" strokeWidth={2} />
                    Compositing watermark...
                  </span>
                )}
              </div>

              {/* Interactive Preview Canvas */}
              <div className="rounded-xl border border-zinc-200 overflow-hidden bg-zinc-900 p-3 min-h-[380px] flex items-center justify-center shadow-inner">
                <canvas
                  ref={previewCanvasRef}
                  className="max-h-[500px] w-auto object-contain rounded shadow-lg"
                />
              </div>

              {/* Footer Metrics */}
              <div className="flex flex-wrap items-center justify-between gap-3 text-xs text-zinc-700 bg-[#FAFAF9] px-4 py-2.5 rounded-xl border border-zinc-200">
                <div className="flex items-center gap-2">
                  <span className="w-2 h-2 rounded-full bg-zinc-900" />
                  <span>Native Resolution: <strong>{outputStats?.width} × {outputStats?.height} px</strong></span>
                </div>
                <div>
                  Protected Size: <strong>{formatBytes(outputStats?.resultSize || 0)}</strong>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </ToolPageShell>
  );
}
