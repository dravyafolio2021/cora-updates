'use client';

import React, { useState, useRef, useEffect, useCallback } from 'react';
import { 
  UploadCloud, 
  Download, 
  Sparkles, 
  ShieldCheck, 
  RotateCcw, 
  Columns2, 
  Rows2, 
  Grid2X2, 
  Trash2, 
  ArrowLeft, 
  ArrowRight, 
  Plus, 
  Sliders, 
  Check, 
  Layers, 
  RefreshCw,
  Image as ImageIcon,
  CheckCircle2,
  FileCheck,
  AlertCircle
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { formatBytes, triggerBrowserImageDownload } from '@/lib/image-engine';

interface PhotoItem {
  id: string;
  file?: File;
  name: string;
  img: HTMLImageElement;
  width: number;
  height: number;
  size: number;
}

const FAQS = [
  {
    question: 'How are different photo dimensions handled when stitching?',
    answer: 'The stitching engine automatically normalizes matching dimensions based on your selected layout. In Horizontal mode, images are scaled to match the maximum height while preserving natural aspect ratios. In Vertical mode, images scale to match the maximum width. In Grid mode, tiles are fitted into proportional cells.',
  },
  {
    question: 'Can I reorder photos or remove individual photos from the collage?',
    answer: 'Yes. Each photo card in the staging tray has directional reordering buttons (move left/right) and a delete button so you can assemble the exact visual sequence desired.',
  },
  {
    question: 'Can I add a gap or border between the photos?',
    answer: 'Yes. Use the "Border Gap" slider to set anywhere from 0px (flush seamless stitch) up to 64px of spacing, and pick your preferred studio background color (White, Black, Gray, Cream, or Transparent).',
  },
  {
    question: 'Is there any compression loss or resolution reduction?',
    answer: 'None. The composite is rendered at the full combined pixel dimensions of your uploaded source assets on an HTML5 high-resolution canvas, resulting in ultra-crisp studio exports.',
  },
  {
    question: 'Are my images stored or uploaded to any server?',
    answer: 'No. The entire process runs 100% locally in your web browser memory. Zero files or image bytes are ever transmitted over the network.',
  },
];

type StitchLayout = 'horizontal' | 'vertical' | 'grid';

export default function CombineImagesPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);
  const previewCanvasRef = useRef<HTMLCanvasElement>(null);

  // Photos State
  const [photos, setPhotos] = useState<PhotoItem[]>([]);
  const [isDragging, setIsDragging] = useState(false);

  // Layout & Styling
  const [layout, setLayout] = useState<StitchLayout>('horizontal');
  const [gap, setGap] = useState<number>(16);
  const [borderRadius, setBorderRadius] = useState<number>(8);
  const [bgColor, setBgColor] = useState<string>('#FFFFFF');
  const [exportFormat, setExportFormat] = useState<'image/jpeg' | 'image/png'>('image/jpeg');

  // Status
  const [isRendering, setIsRendering] = useState(false);
  const [combinedBlob, setCombinedBlob] = useState<Blob | null>(null);
  const [canvasDimensions, setCanvasDimensions] = useState<{ width: number; height: number } | null>(null);

  // Generate 2 demo studio assets for instant testing
  const handleLoadSample = useCallback(() => {
    // Canvas 1: Studio Project Before
    const c1 = document.createElement('canvas');
    c1.width = 800;
    c1.height = 600;
    const ctx1 = c1.getContext('2d');
    if (!ctx1) return;
    ctx1.fillStyle = '#27272A';
    ctx1.fillRect(0, 0, 800, 600);
    ctx1.fillStyle = '#A1A1AA';
    ctx1.font = 'bold 28px sans-serif';
    ctx1.textAlign = 'center';
    ctx1.fillText('INITIAL WIREFRAME SPEC', 400, 290);
    ctx1.font = '16px sans-serif';
    ctx1.fillText('Aarav Mehta Design Architecture', 400, 330);
    const d1 = c1.toDataURL('image/jpeg');

    // Canvas 2: Studio Project After
    const c2 = document.createElement('canvas');
    c2.width = 800;
    c2.height = 600;
    const ctx2 = c2.getContext('2d');
    if (!ctx2) return;
    ctx2.fillStyle = '#09090B';
    ctx2.fillRect(0, 0, 800, 600);
    ctx2.fillStyle = '#FAFAFA';
    ctx2.font = 'bold 28px sans-serif';
    ctx2.textAlign = 'center';
    ctx2.fillText('FINAL PRODUCTION RELEASE', 400, 290);
    ctx2.fillStyle = '#A1A1AA';
    ctx2.font = '16px sans-serif';
    ctx2.fillText('Clean Scaled Multi-Tenant Architecture', 400, 330);
    const d2 = c2.toDataURL('image/jpeg');

    const img1 = new Image();
    const img2 = new Image();
    let loaded = 0;

    const onDone = () => {
      loaded++;
      if (loaded === 2) {
        setPhotos([
          {
            id: 'demo-1',
            name: 'Initial-Wireframe.jpg',
            img: img1,
            width: 800,
            height: 600,
            size: 65000,
          },
          {
            id: 'demo-2',
            name: 'Final-Production.jpg',
            img: img2,
            width: 800,
            height: 600,
            size: 72000,
          },
        ]);
        showToast('Loaded 2 demo studio showcase assets');
      }
    };

    img1.onload = onDone;
    img2.onload = onDone;
    img1.src = d1;
    img2.src = d2;
  }, [showToast]);

  // Handle file uploads (multiple)
  const handleFiles = (fileList: FileList | File[]) => {
    const validFiles = Array.from(fileList).filter((f) => f.type.startsWith('image/'));
    if (validFiles.length === 0) {
      showToast('Please upload valid image files (JPG, PNG, WebP).');
      return;
    }

    let loadedCount = 0;
    const newItems: PhotoItem[] = [];

    validFiles.forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const img = new Image();
        img.onload = () => {
          newItems.push({
            id: `photo-${Date.now()}-${index}-${Math.random()}`,
            file,
            name: file.name,
            img,
            width: img.naturalWidth,
            height: img.naturalHeight,
            size: file.size,
          });
          loadedCount++;
          if (loadedCount === validFiles.length) {
            setPhotos((prev) => [...prev, ...newItems]);
            showToast(`Added ${validFiles.length} photo${validFiles.length > 1 ? 's' : ''}`);
          }
        };
        img.src = e.target?.result as string;
      };
      reader.readAsDataURL(file);
    });
  };

  const movePhoto = (index: number, direction: 'left' | 'right') => {
    setPhotos((prev) => {
      const next = [...prev];
      const targetIndex = direction === 'left' ? index - 1 : index + 1;
      if (targetIndex < 0 || targetIndex >= next.length) return prev;
      const temp = next[index];
      next[index] = next[targetIndex];
      next[targetIndex] = temp;
      return next;
    });
  };

  const removePhoto = (id: string) => {
    setPhotos((prev) => prev.filter((p) => p.id !== id));
  };

  // Re-stitch combined canvas
  useEffect(() => {
    if (photos.length < 2 || !previewCanvasRef.current) return;

    setIsRendering(true);
    const canvas = previewCanvasRef.current;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let targetCanvasW = 0;
    let targetCanvasH = 0;

    // Calculate layout positioning
    interface RenderPos {
      img: HTMLImageElement;
      x: number;
      y: number;
      w: number;
      h: number;
    }
    const renderPositions: RenderPos[] = [];

    if (layout === 'horizontal') {
      // Scale all photos to uniform target height (max height of photos)
      const baseH = Math.max(...photos.map((p) => p.height));
      let currentX = gap;

      photos.forEach((p) => {
        const aspect = p.width / p.height;
        const drawW = Math.round(baseH * aspect);
        renderPositions.push({
          img: p.img,
          x: currentX,
          y: gap,
          w: drawW,
          h: baseH,
        });
        currentX += drawW + gap;
      });

      targetCanvasW = currentX;
      targetCanvasH = baseH + gap * 2;
    } else if (layout === 'vertical') {
      // Scale all photos to uniform target width (max width of photos)
      const baseW = Math.max(...photos.map((p) => p.width));
      let currentY = gap;

      photos.forEach((p) => {
        const aspect = p.height / p.width;
        const drawH = Math.round(baseW * aspect);
        renderPositions.push({
          img: p.img,
          x: gap,
          y: currentY,
          w: baseW,
          h: drawH,
        });
        currentY += drawH + gap;
      });

      targetCanvasW = baseW + gap * 2;
      targetCanvasH = currentY;
    } else if (layout === 'grid') {
      // 2 columns grid
      const cols = 2;
      const maxColW = Math.max(...photos.map((p) => p.width));
      const maxRowH = Math.max(...photos.map((p) => p.height));

      const rows = Math.ceil(photos.length / cols);
      targetCanvasW = cols * maxColW + (cols + 1) * gap;
      targetCanvasH = rows * maxRowH + (rows + 1) * gap;

      photos.forEach((p, idx) => {
        const c = idx % cols;
        const r = Math.floor(idx / cols);
        const cellX = gap + c * (maxColW + gap);
        const cellY = gap + r * (maxRowH + gap);

        // Center within cell
        const aspect = p.width / p.height;
        let drawW = maxColW;
        let drawH = Math.round(maxColW / aspect);
        if (drawH > maxRowH) {
          drawH = maxRowH;
          drawW = Math.round(maxRowH * aspect);
        }
        const offsetX = cellX + Math.round((maxColW - drawW) / 2);
        const offsetY = cellY + Math.round((maxRowH - drawH) / 2);

        renderPositions.push({
          img: p.img,
          x: offsetX,
          y: offsetY,
          w: drawW,
          h: drawH,
        });
      });
    }

    canvas.width = targetCanvasW;
    canvas.height = targetCanvasH;
    setCanvasDimensions({ width: targetCanvasW, height: targetCanvasH });

    // Background fill
    if (bgColor === 'transparent') {
      ctx.clearRect(0, 0, targetCanvasW, targetCanvasH);
    } else {
      ctx.fillStyle = bgColor;
      ctx.fillRect(0, 0, targetCanvasW, targetCanvasH);
    }

    // Draw images with optional rounded corners
    renderPositions.forEach((pos) => {
      ctx.save();
      if (borderRadius > 0) {
        ctx.beginPath();
        ctx.roundRect(pos.x, pos.y, pos.w, pos.h, borderRadius);
        ctx.clip();
      }
      ctx.drawImage(pos.img, pos.x, pos.y, pos.w, pos.h);
      ctx.restore();
    });

    canvas.toBlob((blob) => {
      if (blob) setCombinedBlob(blob);
      setIsRendering(false);
    }, exportFormat, 0.92);
  }, [photos, layout, gap, borderRadius, bgColor, exportFormat]);

  const handleDownload = () => {
    if (!combinedBlob) {
      showToast('No combined image ready to export.');
      return;
    }
    const ext = exportFormat === 'image/png' ? 'png' : 'jpg';
    triggerBrowserImageDownload(combinedBlob, `studio-composite-${layout}.${ext}`);
    showToast(`Downloaded combined ${ext.toUpperCase()}`);
  };

  return (
    <ToolPageShell
      toolId="combine-images"
      badgeTag="LAYOUT & STITCHING"
      title="Multi-Photo Stitching Studio"
      subtitle="Merge multiple photos horizontally, vertically, or in 2x2 collage grids with configurable gaps and studio canvas fills in browser RAM."
      faqItems={FAQS}
    >
      <div className="space-y-6">
        <div className="bg-white border border-zinc-200 rounded-2xl p-4 sm:p-6 shadow-sm">
          {photos.length < 2 ? (
            /* Upload Dropzone */
            <div
              onDragOver={(e) => { e.preventDefault(); setIsDragging(true); }}
              onDragLeave={() => setIsDragging(false)}
              onDrop={(e) => {
                e.preventDefault();
                setIsDragging(false);
                if (e.dataTransfer.files) handleFiles(e.dataTransfer.files);
              }}
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
                multiple
                accept="image/png, image/jpeg, image/webp"
                className="hidden"
                onChange={(e) => {
                  if (e.target.files) handleFiles(e.target.files);
                }}
              />
              <div className="flex flex-col items-center justify-center space-y-3">
                <div className="w-14 h-14 rounded-2xl bg-white border border-zinc-200 shadow-sm flex items-center justify-center">
                  <UploadCloud className="w-7 h-7 text-zinc-800" strokeWidth={1.8} />
                </div>
                <div>
                  <p className="text-base font-semibold text-zinc-900">
                    Select 2 or more photos to stitch together
                  </p>
                  <p className="text-xs text-zinc-700 mt-1">
                    Drag and drop before/after photos, catalog variations, or portfolio pieces
                  </p>
                </div>

                <div className="pt-2 flex flex-wrap items-center justify-center gap-3">
                  <span className="inline-flex items-center gap-1.5 text-xs text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-full">
                    <ShieldCheck className="w-3.5 h-3.5 text-zinc-700" strokeWidth={2} />
                    100% In-Browser Memory Stitched
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
                    Load Demo Portfolio Showcase
                  </button>
                </div>
              </div>
            </div>
          ) : (
            /* Active Layout & Stitching Workspace */
            <div className="space-y-6">
              {/* Header Bar */}
              <div className="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-zinc-100">
                <div>
                  <h3 className="text-sm font-semibold text-zinc-900">
                    Stitching {photos.length} Photos
                  </h3>
                  <p className="text-xs text-zinc-700">
                    Combined Canvas: {canvasDimensions?.width} × {canvasDimensions?.height} px
                  </p>
                </div>

                <div className="flex items-center gap-2">
                  <input
                    ref={fileInputRef}
                    type="file"
                    multiple
                    accept="image/png, image/jpeg, image/webp"
                    className="hidden"
                    onChange={(e) => {
                      if (e.target.files) handleFiles(e.target.files);
                    }}
                  />
                  <button
                    type="button"
                    onClick={() => fileInputRef.current?.click()}
                    className="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-700 bg-white border border-zinc-200 hover:bg-zinc-50 px-3 py-1.5 rounded-lg transition-colors"
                  >
                    <Plus className="w-3.5 h-3.5" strokeWidth={1.8} />
                    Add Photo
                  </button>
                  <button
                    type="button"
                    onClick={() => setPhotos([])}
                    className="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-600 hover:text-zinc-900 bg-zinc-100 hover:bg-zinc-200/80 px-3 py-1.5 rounded-lg transition-colors"
                  >
                    <RotateCcw className="w-3.5 h-3.5" strokeWidth={1.8} />
                    Reset
                  </button>
                  <button
                    type="button"
                    onClick={handleDownload}
                    disabled={!combinedBlob}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-zinc-900 hover:bg-zinc-800 px-4 py-1.5 rounded-lg shadow-sm transition-colors disabled:opacity-50"
                  >
                    <Download className="w-3.5 h-3.5" strokeWidth={1.8} />
                    Export Combined Photo
                  </button>
                </div>
              </div>

              {/* Staging Photo Strip (Reorder & Remove) */}
              <div className="space-y-2">
                <span className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                  Photo Sequence ({photos.length})
                </span>
                <div className="flex gap-3 overflow-x-auto pb-2">
                  {photos.map((photo, idx) => (
                    <div
                      key={photo.id}
                      className="relative flex-shrink-0 w-36 bg-[#FAFAF9] border border-zinc-200 rounded-xl p-2 group"
                    >
                      <div className="w-full h-24 rounded-lg overflow-hidden bg-white border border-zinc-100 flex items-center justify-center mb-2">
                        {/* eslint-disable-next-line @next/next/no-img-element */}
                        <img
                          src={photo.img.src}
                          alt={photo.name}
                          className="max-h-full max-w-full object-contain"
                        />
                      </div>
                      <p className="text-[11px] font-medium text-zinc-800 truncate mb-1">
                        #{idx + 1} {photo.name}
                      </p>
                      <div className="flex items-center justify-between pt-1 border-t border-zinc-200">
                        <div className="flex items-center gap-1">
                          <button
                            type="button"
                            disabled={idx === 0}
                            onClick={() => movePhoto(idx, 'left')}
                            className="p-1 rounded text-zinc-500 hover:text-zinc-900 disabled:opacity-30"
                            title="Move left"
                          >
                            <ArrowLeft className="w-3 h-3" />
                          </button>
                          <button
                            type="button"
                            disabled={idx === photos.length - 1}
                            onClick={() => movePhoto(idx, 'right')}
                            className="p-1 rounded text-zinc-500 hover:text-zinc-900 disabled:opacity-30"
                            title="Move right"
                          >
                            <ArrowRight className="w-3 h-3" />
                          </button>
                        </div>
                        <button
                          type="button"
                          onClick={() => removePhoto(photo.id)}
                          className="p-1 rounded text-zinc-400 hover:text-zinc-900 transition-colors"
                          title="Remove photo"
                        >
                          <Trash2 className="w-3 h-3" />
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Layout Controls Bar */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                {/* 1. Alignment Style */}
                <div className="space-y-3 bg-[#FAFAF9] p-4 rounded-xl border border-zinc-200/80">
                  <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Layout Alignment
                  </label>
                  <div className="grid grid-cols-3 gap-2">
                    {[
                      { id: 'horizontal', label: 'Side by Side', icon: Columns2 },
                      { id: 'vertical', label: 'Stacked', icon: Rows2 },
                      { id: 'grid', label: '2x2 Grid', icon: Grid2X2 },
                    ].map((mode) => {
                      const Icon = mode.icon;
                      return (
                        <button
                          key={mode.id}
                          type="button"
                          onClick={() => setLayout(mode.id as StitchLayout)}
                          className={`flex flex-col items-center justify-center p-2.5 rounded-lg border text-xs font-medium transition-all ${
                            layout === mode.id
                              ? 'bg-zinc-900 text-white border-zinc-900 shadow-xs'
                              : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                          }`}
                        >
                          <Icon className="w-4 h-4 mb-1" strokeWidth={1.8} />
                          {mode.label}
                        </button>
                      );
                    })}
                  </div>
                </div>

                {/* 2. Gap & Corner Rounding */}
                <div className="space-y-3 bg-[#FAFAF9] p-4 rounded-xl border border-zinc-200/80">
                  <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Spacing & Radii
                  </label>
                  <div>
                    <div className="flex items-center justify-between text-xs text-zinc-600 mb-1">
                      <span>Border Gap</span>
                      <span className="font-mono font-medium">{gap}px</span>
                    </div>
                    <input
                      type="range"
                      min="0"
                      max="64"
                      value={gap}
                      onChange={(e) => setGap(Number(e.target.value))}
                      className="w-full accent-zinc-900 cursor-pointer h-1.5 bg-zinc-200 rounded-lg appearance-none"
                    />
                  </div>

                  <div className="pt-2">
                    <div className="flex items-center justify-between text-xs text-zinc-600 mb-1">
                      <span>Photo Corner Radius</span>
                      <span className="font-mono font-medium">{borderRadius}px</span>
                    </div>
                    <input
                      type="range"
                      min="0"
                      max="32"
                      value={borderRadius}
                      onChange={(e) => setBorderRadius(Number(e.target.value))}
                      className="w-full accent-zinc-900 cursor-pointer h-1.5 bg-zinc-200 rounded-lg appearance-none"
                    />
                  </div>
                </div>

                {/* 3. Canvas Background & Export */}
                <div className="space-y-3 bg-[#FAFAF9] p-4 rounded-xl border border-zinc-200/80">
                  <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Background & Format
                  </label>
                  <div className="flex items-center gap-2">
                    {[
                      { hex: '#FFFFFF', label: 'White' },
                      { hex: '#09090B', label: 'Black' },
                      { hex: '#F4F4F5', label: 'Gray' },
                      { hex: '#FBFaf7', label: 'Cream' },
                      { hex: 'transparent', label: 'Transparent' },
                    ].map((c) => (
                      <button
                        key={c.hex}
                        type="button"
                        onClick={() => setBgColor(c.hex)}
                        title={c.label}
                        className={`w-6 h-6 rounded-full border transition-all ${
                          bgColor === c.hex ? 'ring-2 ring-zinc-900 scale-110' : 'border-zinc-300'
                        } ${c.hex === 'transparent' ? 'bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:4px_4px]' : ''}`}
                        style={c.hex !== 'transparent' ? { backgroundColor: c.hex } : {}}
                      />
                    ))}
                  </div>

                  <div className="pt-2 flex gap-2">
                    <button
                      type="button"
                      onClick={() => setExportFormat('image/jpeg')}
                      className={`flex-1 py-1 text-xs font-medium rounded border transition-colors ${
                        exportFormat === 'image/jpeg' ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-700 border-zinc-200'
                      }`}
                    >
                      JPEG
                    </button>
                    <button
                      type="button"
                      onClick={() => setExportFormat('image/png')}
                      className={`flex-1 py-1 text-xs font-medium rounded border transition-colors ${
                        exportFormat === 'image/png' ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-700 border-zinc-200'
                      }`}
                    >
                      PNG
                    </button>
                  </div>
                </div>
              </div>

              {/* Preview Status */}
              <div className="flex items-center justify-between pt-1">
                <span className="text-xs font-medium text-zinc-600">Composited Canvas Output</span>
                {isRendering && (
                  <span className="inline-flex items-center gap-1.5 text-xs text-zinc-700">
                    <RefreshCw className="w-3 h-3 animate-spin" strokeWidth={2} />
                    Stitching canvas...
                  </span>
                )}
              </div>

              {/* Rendered Preview Box */}
              <div className="rounded-xl border border-zinc-200 overflow-hidden bg-zinc-900 p-4 min-h-[380px] flex items-center justify-center shadow-inner">
                <canvas
                  ref={previewCanvasRef}
                  className="max-h-[500px] w-auto object-contain rounded shadow-lg"
                />
              </div>
            </div>
          )}
        </div>
      </div>
    </ToolPageShell>
  );
}
