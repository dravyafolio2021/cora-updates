'use client';

import React, { useState, useRef, useEffect, useCallback } from 'react';
import { 
  UploadCloud, 
  Download, 
  Sparkles, 
  ShieldCheck, 
  RotateCcw, 
  Sliders, 
  Layers, 
  Eye, 
  Check, 
  Pipette, 
  Grid, 
  Maximize2, 
  RefreshCw,
  Image as ImageIcon,
  CheckCircle2,
  FileCheck,
  AlertCircle
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { formatBytes, triggerBrowserImageDownload } from '@/lib/image-engine';

const FAQS = [
  {
    question: 'How does client-side background removal work without uploading photos?',
    answer: 'The background removal engine runs directly inside your browser using an HTML5 Canvas and an in-memory chroma-isolation algorithm. It examines the color delta across RGB color space, applies boundary distance formulas, and feathers edge alpha channels in your machine’s RAM. Zero bytes of your photos are ever uploaded to any cloud server.',
  },
  {
    question: 'What types of photos work best with this tool?',
    answer: 'Photos with solid or high-contrast backgrounds—such as studio e-commerce product photos on white, gray, or colored cycs, headshots taken against studio backdrops, and vector graphics or logos—achieve razor-sharp cutouts. You can fine-tune the tolerance and edge feathering sliders to isolate subjects accurately.',
  },
  {
    question: 'Can I replace the background with solid studio colors instead of transparency?',
    answer: 'Yes. While the default output is a transparent PNG with a preserved alpha channel, you can instantly preview and download your cutouts filled with Studio White (#FFFFFF), Studio Black (#09090B), Soft Gray (#F4F4F5), or Warm Studio Cream (#FBFaf7).',
  },
  {
    question: 'Can I select a specific background color to remove with an eyedropper?',
    answer: 'Yes. Click the "Eyedropper" button, then click anywhere on your image preview. The engine will sample that exact pixel’s RGB values and key out matching colors across the photo.',
  },
  {
    question: 'What is the maximum file size or resolution supported?',
    answer: 'Because all image rendering takes place in your local browser memory, there are no artificial file size caps. The tool effortlessly handles high-resolution 4K and 8K studio photography (20MB+) without compression loss.',
  },
];

type BackdropStyle = 'transparent' | '#FFFFFF' | '#09090B' | '#F4F4F5' | '#FBFaf7';

export default function RemoveBackgroundPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const originalCanvasRef = useRef<HTMLCanvasElement>(null);

  const [imageFile, setImageFile] = useState<File | null>(null);
  const [originalImage, setOriginalImage] = useState<HTMLImageElement | null>(null);
  const [isDragging, setIsDragging] = useState(false);
  const [isProcessing, setIsProcessing] = useState(false);

  // Background removal parameters
  const [keyColor, setKeyColor] = useState<{ r: number; g: number; b: number }>({ r: 255, g: 255, b: 255 });
  const [tolerance, setTolerance] = useState<number>(32);
  const [feather, setFeather] = useState<number>(2);
  const [isEyedropperActive, setIsEyedropperActive] = useState(false);
  const [backdropStyle, setBackdropStyle] = useState<BackdropStyle>('transparent');
  const [viewMode, setViewMode] = useState<'split' | 'cutout' | 'original'>('split');
  const [processedBlob, setProcessedBlob] = useState<Blob | null>(null);
  const [outputStats, setOutputStats] = useState<{ width: number; height: number; originalSize: number; resultSize: number } | null>(null);

  // Generate a high-contrast studio sample product to test immediately
  const handleLoadSample = useCallback(() => {
    const canvas = document.createElement('canvas');
    canvas.width = 800;
    canvas.height = 600;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Solid studio background (clean pure white)
    ctx.fillStyle = '#FFFFFF';
    ctx.fillRect(0, 0, 800, 600);

    // Draw studio shadow
    const shadowGrad = ctx.createRadialGradient(400, 480, 20, 400, 480, 200);
    shadowGrad.addColorStop(0, 'rgba(0,0,0,0.18)');
    shadowGrad.addColorStop(1, 'rgba(0,0,0,0)');
    ctx.fillStyle = shadowGrad;
    ctx.beginPath();
    ctx.ellipse(400, 480, 180, 28, 0, 0, Math.PI * 2);
    ctx.fill();

    // Draw elegant studio camera / product body
    ctx.fillStyle = '#18181B';
    ctx.beginPath();
    ctx.roundRect(260, 220, 280, 220, 24);
    ctx.fill();

    // Camera lens ring (metallic zinc)
    ctx.fillStyle = '#3F3F46';
    ctx.beginPath();
    ctx.arc(400, 330, 76, 0, Math.PI * 2);
    ctx.fill();

    // Camera glass (deep black)
    ctx.fillStyle = '#09090B';
    ctx.beginPath();
    ctx.arc(400, 330, 60, 0, Math.PI * 2);
    ctx.fill();

    // Optical flare reflection
    ctx.fillStyle = 'rgba(255, 255, 255, 0.15)';
    ctx.beginPath();
    ctx.ellipse(380, 310, 36, 20, -Math.PI / 4, 0, Math.PI * 2);
    ctx.fill();

    // Studio branding tag
    ctx.fillStyle = '#FAFAFA';
    ctx.font = 'bold 15px sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('AARAV MEHTA STUDIO', 400, 260);

    const dataUrl = canvas.toDataURL('image/png');
    const img = new Image();
    img.onload = () => {
      setOriginalImage(img);
      setImageFile(null);
      setOutputStats({
        width: 800,
        height: 600,
        originalSize: Math.round(dataUrl.length * 0.75),
        resultSize: 0,
      });
      setKeyColor({ r: 255, g: 255, b: 255 });
      showToast('Loaded demo studio product asset');
    };
    img.src = dataUrl;
  }, [showToast]);

  // Load user image file
  const handleFile = useCallback((file: File) => {
    if (!file.type.startsWith('image/')) {
      showToast('Please select a valid image file (JPG, PNG, WebP).');
      return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = new Image();
      img.onload = () => {
        setOriginalImage(img);
        setImageFile(file);
        setOutputStats({
          width: img.naturalWidth,
          height: img.naturalHeight,
          originalSize: file.size,
          resultSize: 0,
        });

        // Auto-detect corner background color from 4 corners
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = img.naturalWidth;
        tempCanvas.height = img.naturalHeight;
        const ctx = tempCanvas.getContext('2d');
        if (ctx) {
          ctx.drawImage(img, 0, 0);
          try {
            const p1 = ctx.getImageData(2, 2, 1, 1).data;
            const p2 = ctx.getImageData(img.naturalWidth - 3, 2, 1, 1).data;
            const p3 = ctx.getImageData(2, img.naturalHeight - 3, 1, 1).data;
            const p4 = ctx.getImageData(img.naturalWidth - 3, img.naturalHeight - 3, 1, 1).data;
            const avgR = Math.round((p1[0] + p2[0] + p3[0] + p4[0]) / 4);
            const avgG = Math.round((p1[1] + p2[1] + p3[1] + p4[1]) / 4);
            const avgB = Math.round((p1[2] + p2[2] + p3[2] + p4[2]) / 4);
            setKeyColor({ r: avgR, g: avgG, b: avgB });
          } catch {
            setKeyColor({ r: 255, g: 255, b: 255 });
          }
        }
        showToast(`Loaded ${file.name} (${img.naturalWidth}×${img.naturalHeight})`);
      };
      img.src = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }, [showToast]);

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      handleFile(e.dataTransfer.files[0]);
    }
  };

  // Re-process cutout whenever parameters change
  useEffect(() => {
    if (!originalImage || !canvasRef.current) return;

    setIsProcessing(true);
    const canvas = canvasRef.current;
    const w = originalImage.naturalWidth || originalImage.width;
    const h = originalImage.naturalHeight || originalImage.height;
    canvas.width = w;
    canvas.height = h;

    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    if (!ctx) return;

    // Draw background replacement if not transparent
    if (backdropStyle !== 'transparent') {
      ctx.fillStyle = backdropStyle;
      ctx.fillRect(0, 0, w, h);
    } else {
      ctx.clearRect(0, 0, w, h);
    }

    // Process pixels in memory
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = w;
    tempCanvas.height = h;
    const tempCtx = tempCanvas.getContext('2d', { willReadFrequently: true });
    if (!tempCtx) return;

    tempCtx.drawImage(originalImage, 0, 0, w, h);
    const imgData = tempCtx.getImageData(0, 0, w, h);
    const data = imgData.data;

    const targetR = keyColor.r;
    const targetG = keyColor.g;
    const targetB = keyColor.b;
    const tolThreshold = (tolerance / 100) * 441.67; // max distance in 3D RGB is sqrt(255^2*3) = 441.67
    const featherSpread = (feather / 100) * 100;

    for (let i = 0; i < data.length; i += 4) {
      const r = data[i];
      const g = data[i + 1];
      const b = data[i + 2];

      const dr = r - targetR;
      const dg = g - targetG;
      const db = b - targetB;
      const dist = Math.sqrt(dr * dr + dg * dg + db * db);

      if (dist <= tolThreshold) {
        // Complete background match
        data[i + 3] = 0;
      } else if (dist < tolThreshold + featherSpread && featherSpread > 0) {
        // Edge feathering interpolation
        const factor = (dist - tolThreshold) / featherSpread;
        data[i + 3] = Math.round(data[i + 3] * factor);
      }
    }

    tempCtx.putImageData(imgData, 0, 0);

    // Draw cutout on final preview canvas
    ctx.drawImage(tempCanvas, 0, 0);

    canvas.toBlob((blob) => {
      if (blob) {
        setProcessedBlob(blob);
        setOutputStats((prev) => prev ? { ...prev, resultSize: blob.size } : null);
      }
      setIsProcessing(false);
    }, 'image/png');
  }, [originalImage, keyColor, tolerance, feather, backdropStyle]);

  // Handle Eyedropper click on canvas
  const handleCanvasClick = (e: React.MouseEvent<HTMLCanvasElement>) => {
    if (!isEyedropperActive || !originalImage || !canvasRef.current) return;
    const rect = canvasRef.current.getBoundingClientRect();
    const scaleX = canvasRef.current.width / rect.width;
    const scaleY = canvasRef.current.height / rect.height;
    const x = Math.round((e.clientX - rect.left) * scaleX);
    const y = Math.round((e.clientY - rect.top) * scaleY);

    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = canvasRef.current.width;
    tempCanvas.height = canvasRef.current.height;
    const ctx = tempCanvas.getContext('2d');
    if (ctx) {
      ctx.drawImage(originalImage, 0, 0);
      const pixel = ctx.getImageData(x, y, 1, 1).data;
      setKeyColor({ r: pixel[0], g: pixel[1], b: pixel[2] });
      setIsEyedropperActive(false);
      showToast(`Selected color: RGB(${pixel[0]}, ${pixel[1]}, ${pixel[2]})`);
    }
  };

  const handleDownload = () => {
    if (!processedBlob) {
      showToast('No processed image available to download.');
      return;
    }
    const name = imageFile?.name ? imageFile.name.replace(/\.[^/.]+$/, '') : 'studio-subject';
    triggerBrowserImageDownload(processedBlob, `${name}-cutout.png`);
    showToast('Cutout PNG downloaded successfully!');
  };

  return (
    <ToolPageShell
      toolId="remove-background"
      badgeTag="CREATIVE STUDIO"
      title="Instant Background Remover"
      subtitle="Isolate subjects, strip studio backdrops, and export transparent alpha PNGs directly in your browser memory with zero cloud uploads."
      faqItems={FAQS}
    >
      <div className="space-y-6">
        {/* Top Control Header Card */}
        <div className="bg-white border border-zinc-200 rounded-2xl p-4 sm:p-6 shadow-sm">
          {!originalImage ? (
            /* Upload Dropzone */
            <div
              onDragOver={(e) => { e.preventDefault(); setIsDragging(true); }}
              onDragLeave={() => setIsDragging(false)}
              onDrop={handleDrop}
              onClick={() => fileInputRef.current?.click()}
              className={`relative cursor-pointer border-2 border-dashed rounded-xl p-8 sm:p-12 text-center transition-all ${
                isDragging 
                  ? 'border-zinc-900 bg-zinc-50/80 scale-[0.99]' 
                  : 'border-zinc-200 hover:border-zinc-400 bg-[#FAFAF9]'
              }`}
            >
              <input
                ref={fileInputRef}
                type="file"
                accept="image/png, image/jpeg, image/webp"
                className="hidden"
                onChange={(e) => {
                  if (e.target.files && e.target.files[0]) {
                    handleFile(e.target.files[0]);
                  }
                }}
              />
              <div className="flex flex-col items-center justify-center space-y-3">
                <div className="w-14 h-14 rounded-2xl bg-white border border-zinc-200 shadow-sm flex items-center justify-center">
                  <UploadCloud className="w-7 h-7 text-zinc-800" strokeWidth={1.8} />
                </div>
                <div>
                  <p className="text-base font-semibold text-zinc-900">
                    Click to select photo or drag and drop
                  </p>
                  <p className="text-xs text-zinc-700 mt-1">
                    Supports high-resolution PNG, JPG, and WebP (up to 50MB)
                  </p>
                </div>

                <div className="pt-2 flex flex-wrap items-center justify-center gap-3">
                  <span className="inline-flex items-center gap-1.5 text-xs text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-full">
                    <ShieldCheck className="w-3.5 h-3.5 text-zinc-700" strokeWidth={2} />
                    100% In-Browser Privacy
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
                    Try Demo Studio Product
                  </button>
                </div>
              </div>
            </div>
          ) : (
            /* Active Image Workspace Controls */
            <div className="space-y-6">
              <div className="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-zinc-100">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center">
                    <ImageIcon className="w-5 h-5 text-zinc-800" strokeWidth={1.8} />
                  </div>
                  <div>
                    <h3 className="text-sm font-semibold text-zinc-900">
                      {imageFile ? imageFile.name : 'Demo Studio Product'}
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
                      setOriginalImage(null);
                      setImageFile(null);
                      setProcessedBlob(null);
                    }}
                    className="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-600 hover:text-zinc-900 bg-zinc-100 hover:bg-zinc-200/80 px-3 py-1.5 rounded-lg transition-colors"
                  >
                    <RotateCcw className="w-3.5 h-3.5" strokeWidth={1.8} />
                    New Photo
                  </button>
                  <button
                    type="button"
                    onClick={handleDownload}
                    disabled={!processedBlob}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-zinc-900 hover:bg-zinc-800 px-4 py-1.5 rounded-lg shadow-sm transition-colors disabled:opacity-50"
                  >
                    <Download className="w-3.5 h-3.5" strokeWidth={1.8} />
                    Download PNG
                  </button>
                </div>
              </div>

              {/* Tuning Sliders & Backdrop Selector */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                {/* 1. Key Color & Tolerance */}
                <div className="space-y-3 bg-[#FAFAF9] p-3.5 rounded-xl border border-zinc-200/80">
                  <div className="flex items-center justify-between">
                    <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider">
                      Key Color
                    </label>
                    <button
                      type="button"
                      onClick={() => {
                        setIsEyedropperActive(!isEyedropperActive);
                        if (!isEyedropperActive) {
                          showToast('Click anywhere on the preview to sample background color');
                        }
                      }}
                      className={`inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded border transition-colors ${
                        isEyedropperActive 
                          ? 'bg-zinc-900 text-white border-zinc-900' 
                          : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                      }`}
                    >
                      <Pipette className="w-3 h-3" strokeWidth={1.8} />
                      {isEyedropperActive ? 'Click Canvas' : 'Pick from Image'}
                    </button>
                  </div>

                  <div className="flex items-center gap-2">
                    <div 
                      className="w-8 h-8 rounded-lg border border-zinc-300 shadow-inner flex-shrink-0"
                      style={{ backgroundColor: `rgb(${keyColor.r}, ${keyColor.g}, ${keyColor.b})` }}
                    />
                    <div className="text-xs font-mono text-zinc-600">
                      RGB({keyColor.r}, {keyColor.g}, {keyColor.b})
                    </div>
                  </div>

                  <div className="pt-2">
                    <div className="flex items-center justify-between text-xs text-zinc-700 mb-1">
                      <span>Color Tolerance</span>
                      <span className="font-mono font-medium">{tolerance}%</span>
                    </div>
                    <input
                      type="range"
                      min="5"
                      max="85"
                      value={tolerance}
                      onChange={(e) => setTolerance(Number(e.target.value))}
                      className="w-full accent-zinc-900 cursor-pointer h-1.5 bg-zinc-200 rounded-lg appearance-none"
                    />
                  </div>
                </div>

                {/* 2. Edge Softness / Feathering */}
                <div className="space-y-3 bg-[#FAFAF9] p-3.5 rounded-xl border border-zinc-200/80">
                  <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Edge Feathering
                  </label>
                  <div className="pt-2">
                    <div className="flex items-center justify-between text-xs text-zinc-700 mb-1">
                      <span>Boundary Softness</span>
                      <span className="font-mono font-medium">{feather}px</span>
                    </div>
                    <input
                      type="range"
                      min="0"
                      max="15"
                      value={feather}
                      onChange={(e) => setFeather(Number(e.target.value))}
                      className="w-full accent-zinc-900 cursor-pointer h-1.5 bg-zinc-200 rounded-lg appearance-none"
                    />
                  </div>
                  <p className="text-[11px] text-zinc-700 leading-relaxed">
                    Higher values soften hair, fur, and delicate edges to eliminate halo artifacts.
                  </p>
                </div>

                {/* 3. Output Background Replacement */}
                <div className="space-y-3 bg-[#FAFAF9] p-3.5 rounded-xl border border-zinc-200/80">
                  <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Background Fill
                  </label>
                  <div className="flex flex-wrap gap-2 pt-1">
                    {[
                      { id: 'transparent', label: 'Alpha', bg: 'transparent' },
                      { id: '#FFFFFF', label: 'White', bg: '#FFFFFF' },
                      { id: '#09090B', label: 'Black', bg: '#09090B' },
                      { id: '#F4F4F5', label: 'Gray', bg: '#F4F4F5' },
                      { id: '#FBFaf7', label: 'Cream', bg: '#FBFaf7' },
                    ].map((opt) => (
                      <button
                        key={opt.id}
                        type="button"
                        onClick={() => setBackdropStyle(opt.id as BackdropStyle)}
                        className={`flex items-center gap-1.5 px-2.5 py-1 text-xs rounded-lg border transition-all ${
                          backdropStyle === opt.id
                            ? 'border-zinc-900 bg-white font-semibold text-zinc-900 shadow-xs ring-1 ring-zinc-900'
                            : 'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300'
                        }`}
                      >
                        <span 
                          className={`w-3 h-3 rounded-full border border-zinc-300 ${opt.id === 'transparent' ? 'bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:4px_4px]' : ''}`}
                          style={opt.id !== 'transparent' ? { backgroundColor: opt.bg } : {}}
                        />
                        {opt.label}
                      </button>
                    ))}
                  </div>
                </div>
              </div>

              {/* View Mode Bar */}
              <div className="flex items-center justify-between pt-2">
                <div className="inline-flex rounded-lg bg-zinc-100 p-0.5 border border-zinc-200">
                  <button
                    type="button"
                    onClick={() => setViewMode('split')}
                    className={`px-3 py-1 text-xs font-medium rounded-md transition-colors ${
                      viewMode === 'split' ? 'bg-white text-zinc-900 shadow-xs' : 'text-zinc-600 hover:text-zinc-900'
                    }`}
                  >
                    Side by Side
                  </button>
                  <button
                    type="button"
                    onClick={() => setViewMode('cutout')}
                    className={`px-3 py-1 text-xs font-medium rounded-md transition-colors ${
                      viewMode === 'cutout' ? 'bg-white text-zinc-900 shadow-xs' : 'text-zinc-600 hover:text-zinc-900'
                    }`}
                  >
                    Cutout Only
                  </button>
                  <button
                    type="button"
                    onClick={() => setViewMode('original')}
                    className={`px-3 py-1 text-xs font-medium rounded-md transition-colors ${
                      viewMode === 'original' ? 'bg-white text-zinc-900 shadow-xs' : 'text-zinc-600 hover:text-zinc-900'
                    }`}
                  >
                    Original
                  </button>
                </div>

                {isProcessing && (
                  <span className="inline-flex items-center gap-1.5 text-xs text-zinc-700 animate-pulse">
                    <RefreshCw className="w-3 h-3 animate-spin" strokeWidth={2} />
                    Rendering alpha mask...
                  </span>
                )}
              </div>

              {/* Canvas Preview Area */}
              <div className="rounded-xl border border-zinc-200 overflow-hidden bg-zinc-50 p-4 min-h-[380px] flex items-center justify-center">
                {viewMode === 'split' && (
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                    <div className="space-y-1.5 text-center">
                      <span className="text-[11px] font-semibold uppercase tracking-wider text-zinc-700">
                        Original Photo
                      </span>
                      <div className="relative border border-zinc-200 rounded-lg overflow-hidden bg-white max-h-[420px] flex items-center justify-center">
                        {/* eslint-disable-next-line @next/next/no-img-element */}
                        <img
                          src={originalImage.src}
                          alt="Original subject"
                          className="max-h-[400px] w-auto object-contain"
                        />
                      </div>
                    </div>

                    <div className="space-y-1.5 text-center">
                      <span className="text-[11px] font-semibold uppercase tracking-wider text-zinc-700">
                        Isolated Alpha Cutout
                      </span>
                      <div 
                        className={`relative border border-zinc-200 rounded-lg overflow-hidden max-h-[420px] flex items-center justify-center ${
                          backdropStyle === 'transparent'
                            ? 'bg-[linear-gradient(45deg,#f4f4f5_25%,transparent_25%),linear-gradient(-45deg,#f4f4f5_25%,transparent_25%),linear-gradient(45deg,transparent_75%,#f4f4f5_75%),linear-gradient(-45deg,transparent_75%,#f4f4f5_75%)] [background-size:16px_16px] [background-position:0_0,0_8px,8px_-8px,-8px_0px]'
                            : ''
                        }`}
                        style={backdropStyle !== 'transparent' ? { backgroundColor: backdropStyle } : {}}
                      >
                        <canvas
                          ref={canvasRef}
                          onClick={handleCanvasClick}
                          className={`max-h-[400px] w-auto object-contain ${
                            isEyedropperActive ? 'cursor-crosshair' : ''
                          }`}
                        />
                      </div>
                    </div>
                  </div>
                )}

                {viewMode === 'cutout' && (
                  <div 
                    className={`relative border border-zinc-200 rounded-lg overflow-hidden w-full max-h-[500px] flex items-center justify-center p-2 ${
                      backdropStyle === 'transparent'
                        ? 'bg-[linear-gradient(45deg,#f4f4f5_25%,transparent_25%),linear-gradient(-45deg,#f4f4f5_25%,transparent_25%),linear-gradient(45deg,transparent_75%,#f4f4f5_75%),linear-gradient(-45deg,transparent_75%,#f4f4f5_75%)] [background-size:16px_16px] [background-position:0_0,0_8px,8px_-8px,-8px_0px]'
                        : ''
                    }`}
                    style={backdropStyle !== 'transparent' ? { backgroundColor: backdropStyle } : {}}
                  >
                    <canvas
                      ref={canvasRef}
                      onClick={handleCanvasClick}
                      className={`max-h-[480px] w-auto object-contain ${
                        isEyedropperActive ? 'cursor-crosshair' : ''
                      }`}
                    />
                  </div>
                )}

                {viewMode === 'original' && (
                  <div className="relative border border-zinc-200 rounded-lg overflow-hidden w-full max-h-[500px] flex items-center justify-center p-2 bg-white">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src={originalImage.src}
                      alt="Original"
                      className="max-h-[480px] w-auto object-contain"
                    />
                  </div>
                )}
              </div>

              {/* Status bar */}
              <div className="flex flex-wrap items-center justify-between gap-3 text-xs text-zinc-700 bg-[#FAFAF9] px-4 py-2.5 rounded-xl border border-zinc-200">
                <div className="flex items-center gap-2">
                  <span className="w-2 h-2 rounded-full bg-zinc-900" />
                  <span>Ready to Export: <strong>{outputStats?.width} × {outputStats?.height} px</strong></span>
                </div>
                <div>
                  Export Size: <strong>{formatBytes(outputStats?.resultSize || 0)}</strong> (Lossless PNG)
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </ToolPageShell>
  );
}
