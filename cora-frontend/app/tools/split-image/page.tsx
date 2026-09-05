'use client';

import React, { useState, useRef, useEffect, useCallback } from 'react';
import { 
  UploadCloud, 
  Download, 
  Sparkles, 
  ShieldCheck, 
  RotateCcw, 
  Grid3X3, 
  Columns3, 
  Grid2X2, 
  Sliders, 
  Check, 
  Maximize2, 
  FileArchive, 
  Layers, 
  RefreshCw,
  Image as ImageIcon,
  CheckCircle2,
  FileCheck,
  AlertCircle
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { formatBytes, triggerBrowserImageDownload, createSimpleZip } from '@/lib/image-engine';

interface SlicedTile {
  index: number;
  row: number;
  col: number;
  width: number;
  height: number;
  dataUrl: string;
  blob: Blob;
}

const FAQS = [
  {
    question: 'What is a 3-part seamless carousel and how does it work on Instagram?',
    answer: 'A seamless carousel takes a wide panoramic image (usually 3:1 aspect ratio) and splits it into 3 equal consecutive square or 4:5 vertical slides. When posted together in a multi-image carousel post on Instagram or LinkedIn, viewers can swipe seamlessly from slide to slide without any visible break in the composition.',
  },
  {
    question: 'How does the 9-part grid (3x3) split work for social media feeds?',
    answer: 'The 3x3 split divides a high-resolution photo into 9 square tiles. When uploaded to an Instagram profile in reverse sequential order (from tile 9 down to tile 1), the tiles assemble into a massive 3x3 photo mural on your main profile grid.',
  },
  {
    question: 'Can I choose custom rows and columns for arbitrary grids?',
    answer: 'Yes. Select the "Custom Grid" mode to define any row count (1 to 6) and column count (1 to 6), such as a 2-part split (1x2), 4-part quad (2x2), or 6-part grid (2x3).',
  },
  {
    question: 'Does splitting compress or degrade my image resolution?',
    answer: 'No. The slicer calculates pixel-precise bounding boxes on the original full-resolution image matrix without downsampling, exporting each tile at maximum fidelity in PNG or high-quality JPEG.',
  },
  {
    question: 'Can I download all sliced tiles in a single ZIP file?',
    answer: 'Yes. Click "Download All Tiles (.ZIP)" to package all sequentially numbered image files into a single ZIP archive created 100% in browser RAM with zero server transmission.',
  },
];

type SplitPreset = 'carousel-3' | 'grid-9' | 'custom';

export default function SplitImagePage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Base Image
  const [sourceImage, setSourceImage] = useState<HTMLImageElement | null>(null);
  const [sourceFile, setSourceFile] = useState<File | null>(null);
  const [isDragging, setIsDragging] = useState(false);

  // Split Configuration
  const [preset, setPreset] = useState<SplitPreset>('carousel-3');
  const [rows, setRows] = useState<number>(1);
  const [cols, setCols] = useState<number>(3);
  const [exportFormat, setExportFormat] = useState<'image/jpeg' | 'image/png'>('image/jpeg');

  // Slices
  const [slicedTiles, setSlicedTiles] = useState<SlicedTile[]>([]);
  const [isSlicing, setIsSlicing] = useState(false);
  const [isZipping, setIsZipping] = useState(false);

  // Preset Handlers
  const handlePresetSelect = (newPreset: SplitPreset) => {
    setPreset(newPreset);
    if (newPreset === 'carousel-3') {
      setRows(1);
      setCols(3);
    } else if (newPreset === 'grid-9') {
      setRows(3);
      setCols(3);
    }
  };

  // Generate a panoramic demo landscape for instant testing
  const handleLoadSample = useCallback(() => {
    const canvas = document.createElement('canvas');
    canvas.width = 2400;
    canvas.height = 800;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Rich architectural panorama gradient
    const grad = ctx.createLinearGradient(0, 0, 2400, 800);
    grad.addColorStop(0, '#09090B');
    grad.addColorStop(0.35, '#18181B');
    grad.addColorStop(0.7, '#27272A');
    grad.addColorStop(1, '#09090B');
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, 2400, 800);

    // Architectural geometric mesh
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.07)';
    ctx.lineWidth = 1.5;
    for (let x = 0; x < 2400; x += 100) {
      ctx.beginPath();
      ctx.moveTo(x, 0);
      ctx.lineTo(x + 400, 800);
      ctx.stroke();
    }

    // Panorama slide focal points
    // Slide 1
    ctx.fillStyle = '#FAFAFA';
    ctx.font = 'bold 36px sans-serif';
    ctx.fillText('PART 1: DISCOVERY', 200, 420);
    ctx.font = '20px sans-serif';
    ctx.fillStyle = '#A1A1AA';
    ctx.fillText('Research & Architectural Mapping', 200, 460);

    // Slide 2
    ctx.fillStyle = '#FAFAFA';
    ctx.font = 'bold 36px sans-serif';
    ctx.fillText('PART 2: ARCHITECTURE', 1000, 420);
    ctx.font = '20px sans-serif';
    ctx.fillStyle = '#A1A1AA';
    ctx.fillText('Autonomous Engine & Workspaces', 1000, 460);

    // Slide 3
    ctx.fillStyle = '#FAFAFA';
    ctx.font = 'bold 36px sans-serif';
    ctx.fillText('PART 3: PRODUCTION', 1800, 420);
    ctx.font = '20px sans-serif';
    ctx.fillStyle = '#A1A1AA';
    ctx.fillText('Client Deliverable & Handover', 1800, 460);

    const dataUrl = canvas.toDataURL('image/jpeg', 0.95);
    const img = new Image();
    img.onload = () => {
      setSourceImage(img);
      setSourceFile(null);
      setPreset('carousel-3');
      setRows(1);
      setCols(3);
      showToast('Loaded demo 3:1 panoramic showcase');
    };
    img.src = dataUrl;
  }, [showToast]);

  // Handle image upload
  const handleFile = useCallback((file: File) => {
    if (!file.type.startsWith('image/')) {
      showToast('Please upload a valid image file (JPG, PNG, WebP).');
      return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = new Image();
      img.onload = () => {
        setSourceImage(img);
        setSourceFile(file);
        showToast(`Loaded ${file.name} (${img.naturalWidth}×${img.naturalHeight})`);
      };
      img.src = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }, [showToast]);

  // Slice image into tiles whenever parameters change
  useEffect(() => {
    if (!sourceImage) return;

    setIsSlicing(true);
    const totalW = sourceImage.naturalWidth || sourceImage.width;
    const totalH = sourceImage.naturalHeight || sourceImage.height;

    const tileW = Math.floor(totalW / cols);
    const tileH = Math.floor(totalH / rows);

    const tiles: SlicedTile[] = [];
    let tileIndex = 1;

    for (let r = 0; r < rows; r++) {
      for (let c = 0; c < cols; c++) {
        const startX = c * tileW;
        const startY = r * tileH;
        // Last column or row takes any remainder pixel to preserve full integrity
        const currentTileW = (c === cols - 1) ? totalW - startX : tileW;
        const currentTileH = (r === rows - 1) ? totalH - startY : tileH;

        const tileCanvas = document.createElement('canvas');
        tileCanvas.width = currentTileW;
        tileCanvas.height = currentTileH;
        const ctx = tileCanvas.getContext('2d');
        if (ctx) {
          ctx.drawImage(
            sourceImage,
            startX,
            startY,
            currentTileW,
            currentTileH,
            0,
            0,
            currentTileW,
            currentTileH
          );

          const dataUrl = tileCanvas.toDataURL(exportFormat, 0.95);
          // Convert synchronously for state display
          tileCanvas.toBlob((blob) => {
            if (blob) {
              tiles.push({
                index: tileIndex++,
                row: r + 1,
                col: c + 1,
                width: currentTileW,
                height: currentTileH,
                dataUrl,
                blob,
              });
              if (tiles.length === rows * cols) {
                // Sort by index ascending
                tiles.sort((a, b) => a.index - b.index);
                setSlicedTiles([...tiles]);
                setIsSlicing(false);
              }
            }
          }, exportFormat, 0.95);
        }
      }
    }
  }, [sourceImage, rows, cols, exportFormat]);

  // 1-Click Batch Download as ZIP
  const handleDownloadAllZip = async () => {
    if (slicedTiles.length === 0) return;
    setIsZipping(true);
    try {
      const ext = exportFormat === 'image/png' ? 'png' : 'jpg';
      const baseName = sourceFile ? sourceFile.name.replace(/\.[^/.]+$/, '') : 'panorama-slice';

      const zipFiles = slicedTiles.map((tile) => ({
        name: `${baseName}-tile-${tile.index.toString().padStart(2, '0')}.${ext}`,
        blob: tile.blob,
      }));

      const zipBlob = await createSimpleZip(zipFiles);
      triggerBrowserImageDownload(zipBlob, `${baseName}-tiles-${rows}x${cols}.zip`);
      showToast(`Exported ${slicedTiles.length} tiles in ZIP archive`);
    } catch (err) {
      showToast('Error generating ZIP file: ' + err);
    } finally {
      setIsZipping(false);
    }
  };

  // Download individual tile
  const handleDownloadSingleTile = (tile: SlicedTile) => {
    const ext = exportFormat === 'image/png' ? 'png' : 'jpg';
    const baseName = sourceFile ? sourceFile.name.replace(/\.[^/.]+$/, '') : 'slice';
    triggerBrowserImageDownload(tile.blob, `${baseName}-tile-${tile.index}.${ext}`);
    showToast(`Downloaded tile #${tile.index}`);
  };

  return (
    <ToolPageShell
      toolId="split-image"
      badgeTag="SOCIAL & TILES"
      title="Seamless Image Slicer"
      subtitle="Slice wide panoramas into seamless Instagram carousel slides, 9-part profile grids, or custom tile matrices in browser RAM."
      faqItems={FAQS}
    >
      <div className="space-y-6">
        <div className="bg-white border border-zinc-200 rounded-2xl p-4 sm:p-6 shadow-sm">
          {!sourceImage ? (
            /* Upload Dropzone */
            <div
              onDragOver={(e) => { e.preventDefault(); setIsDragging(true); }}
              onDragLeave={() => setIsDragging(false)}
              onDrop={(e) => {
                e.preventDefault();
                setIsDragging(false);
                if (e.dataTransfer.files?.[0]) handleFile(e.dataTransfer.files[0]);
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
                accept="image/png, image/jpeg, image/webp"
                className="hidden"
                onChange={(e) => {
                  if (e.target.files?.[0]) handleFile(e.target.files[0]);
                }}
              />
              <div className="flex flex-col items-center justify-center space-y-3">
                <div className="w-14 h-14 rounded-2xl bg-white border border-zinc-200 shadow-sm flex items-center justify-center">
                  <UploadCloud className="w-7 h-7 text-zinc-800" strokeWidth={1.8} />
                </div>
                <div>
                  <p className="text-base font-semibold text-zinc-900">
                    Upload image to slice into tiles or carousel
                  </p>
                  <p className="text-xs text-zinc-700 mt-1">
                    Supports high-resolution panoramas, portfolio graphics, and photos (up to 50MB)
                  </p>
                </div>

                <div className="pt-2 flex flex-wrap items-center justify-center gap-3">
                  <span className="inline-flex items-center gap-1.5 text-xs text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-full">
                    <ShieldCheck className="w-3.5 h-3.5 text-zinc-700" strokeWidth={2} />
                    100% In-Browser Memory Slicing
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
                    Try Demo 3:1 Panoramic Carousel
                  </button>
                </div>
              </div>
            </div>
          ) : (
            /* Active Image Slicing Studio */
            <div className="space-y-6">
              {/* Header Bar */}
              <div className="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-zinc-100">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center">
                    <ImageIcon className="w-5 h-5 text-zinc-800" strokeWidth={1.8} />
                  </div>
                  <div>
                    <h3 className="text-sm font-semibold text-zinc-900">
                      {sourceFile ? sourceFile.name : 'Architectural Panorama'}
                    </h3>
                    <p className="text-xs text-zinc-700">
                      {sourceImage.naturalWidth} × {sourceImage.naturalHeight} px • Slicing into {rows * cols} Tiles ({rows}×{cols})
                    </p>
                  </div>
                </div>

                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={() => {
                      setSourceImage(null);
                      setSourceFile(null);
                      setSlicedTiles([]);
                    }}
                    className="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-600 hover:text-zinc-900 bg-zinc-100 hover:bg-zinc-200/80 px-3 py-1.5 rounded-lg transition-colors"
                  >
                    <RotateCcw className="w-3.5 h-3.5" strokeWidth={1.8} />
                    New Photo
                  </button>
                  <button
                    type="button"
                    onClick={handleDownloadAllZip}
                    disabled={isZipping || slicedTiles.length === 0}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-zinc-900 hover:bg-zinc-800 px-4 py-1.5 rounded-lg shadow-sm transition-colors disabled:opacity-50"
                  >
                    <FileArchive className="w-3.5 h-3.5" strokeWidth={1.8} />
                    {isZipping ? 'Creating ZIP...' : 'Download All Tiles (.ZIP)'}
                  </button>
                </div>
              </div>

              {/* Mode Selection & Matrix Settings */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                {/* 1. Presets */}
                <div className="space-y-3 bg-[#FAFAF9] p-4 rounded-xl border border-zinc-200/80">
                  <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Split Preset
                  </label>
                  <div className="space-y-2">
                    {[
                      { id: 'carousel-3', label: '3-Part Carousel (1×3)', desc: 'Instagram swipeable slides', icon: Columns3 },
                      { id: 'grid-9', label: '9-Part Grid (3×3)', desc: 'Instagram 3x3 profile mural', icon: Grid3X3 },
                      { id: 'custom', label: 'Custom Matrix', desc: 'Define arbitrary rows & cols', icon: Grid2X2 },
                    ].map((item) => {
                      const Icon = item.icon;
                      return (
                        <button
                          key={item.id}
                          type="button"
                          onClick={() => handlePresetSelect(item.id as SplitPreset)}
                          className={`w-full flex items-center gap-3 p-2.5 rounded-lg border text-left transition-all ${
                            preset === item.id
                              ? 'bg-zinc-900 text-white border-zinc-900 shadow-xs'
                              : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                          }`}
                        >
                          <Icon className="w-4 h-4 flex-shrink-0" strokeWidth={1.8} />
                          <div>
                            <p className="text-xs font-semibold">{item.label}</p>
                            <p className={`text-[10px] ${preset === item.id ? 'text-zinc-300' : 'text-zinc-600'}`}>
                              {item.desc}
                            </p>
                          </div>
                        </button>
                      );
                    })}
                  </div>
                </div>

                {/* 2. Grid Dimensions (Enabled in Custom) */}
                <div className="space-y-3 bg-[#FAFAF9] p-4 rounded-xl border border-zinc-200/80">
                  <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Grid Partitioning
                  </label>

                  <div>
                    <div className="flex items-center justify-between text-xs text-zinc-600 mb-1">
                      <span>Columns (Vertical Slices)</span>
                      <span className="font-mono font-medium">{cols}</span>
                    </div>
                    <input
                      type="range"
                      min="1"
                      max="6"
                      value={cols}
                      disabled={preset !== 'custom'}
                      onChange={(e) => setCols(Number(e.target.value))}
                      className="w-full accent-zinc-900 cursor-pointer h-1.5 bg-zinc-200 rounded-lg appearance-none disabled:opacity-40"
                    />
                  </div>

                  <div className="pt-2">
                    <div className="flex items-center justify-between text-xs text-zinc-600 mb-1">
                      <span>Rows (Horizontal Slices)</span>
                      <span className="font-mono font-medium">{rows}</span>
                    </div>
                    <input
                      type="range"
                      min="1"
                      max="6"
                      value={rows}
                      disabled={preset !== 'custom'}
                      onChange={(e) => setRows(Number(e.target.value))}
                      className="w-full accent-zinc-900 cursor-pointer h-1.5 bg-zinc-200 rounded-lg appearance-none disabled:opacity-40"
                    />
                  </div>

                  <p className="text-[11px] text-zinc-700 pt-1">
                    Each tile resolution: ~{Math.round(sourceImage.naturalWidth / cols)} × {Math.round(sourceImage.naturalHeight / rows)} px
                  </p>
                </div>

                {/* 3. Output Format */}
                <div className="space-y-3 bg-[#FAFAF9] p-4 rounded-xl border border-zinc-200/80">
                  <label className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Export Settings
                  </label>

                  <div className="flex gap-2 pt-1">
                    <button
                      type="button"
                      onClick={() => setExportFormat('image/jpeg')}
                      className={`flex-1 py-1.5 text-xs font-medium rounded-lg border transition-colors ${
                        exportFormat === 'image/jpeg' ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-700 border-zinc-200'
                      }`}
                    >
                      JPEG (Standard)
                    </button>
                    <button
                      type="button"
                      onClick={() => setExportFormat('image/png')}
                      className={`flex-1 py-1.5 text-xs font-medium rounded-lg border transition-colors ${
                        exportFormat === 'image/png' ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-700 border-zinc-200'
                      }`}
                    >
                      Lossless PNG
                    </button>
                  </div>

                  <div className="pt-3 border-t border-zinc-200">
                    <button
                      type="button"
                      onClick={handleDownloadAllZip}
                      disabled={isZipping || slicedTiles.length === 0}
                      className="w-full py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-lg text-xs font-medium flex items-center justify-center gap-1.5 transition-colors"
                    >
                      <Download className="w-3.5 h-3.5" strokeWidth={1.8} />
                      Batch ZIP ({slicedTiles.length} files)
                    </button>
                  </div>
                </div>
              </div>

              {/* Visual Slice Grid Overlay on Source Image */}
              <div className="space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Slice Grid Overlay
                  </span>
                  {isSlicing && (
                    <span className="inline-flex items-center gap-1.5 text-xs text-zinc-700">
                      <RefreshCw className="w-3 h-3 animate-spin" strokeWidth={2} />
                      Slicing pixels...
                    </span>
                  )}
                </div>

                <div className="relative border border-zinc-200 rounded-xl overflow-hidden bg-zinc-950 p-2 flex items-center justify-center">
                  <div className="relative inline-block max-w-full">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src={sourceImage.src}
                      alt="Source for slicing"
                      className="max-h-[380px] w-auto object-contain block rounded"
                    />

                    {/* CSS Grid Overlay */}
                    <div 
                      className="absolute inset-0 grid pointer-events-none rounded overflow-hidden"
                      style={{
                        gridTemplateColumns: `repeat(${cols}, 1fr)`,
                        gridTemplateRows: `repeat(${rows}, 1fr)`,
                      }}
                    >
                      {Array.from({ length: rows * cols }).map((_, idx) => (
                        <div
                          key={idx}
                          className="border border-dashed border-white/60 relative flex items-start justify-start p-1.5"
                        >
                          <span className="bg-zinc-900/85 text-white text-[10px] font-mono font-bold px-1.5 py-0.5 rounded shadow">
                            #{idx + 1}
                          </span>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              </div>

              {/* Sliced Output Tiles Tray */}
              <div className="space-y-3 pt-2">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-semibold text-zinc-700 uppercase tracking-wider block">
                    Individual Tile Previews ({slicedTiles.length})
                  </span>
                  <span className="text-xs text-zinc-700">
                    Click any tile to download individually
                  </span>
                </div>

                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                  {slicedTiles.map((tile) => (
                    <div
                      key={tile.index}
                      className="bg-[#FAFAF9] border border-zinc-200 rounded-xl p-2.5 flex flex-col justify-between hover:border-zinc-400 transition-all group"
                    >
                      <div>
                        <div className="w-full aspect-square rounded-lg overflow-hidden bg-white border border-zinc-200 flex items-center justify-center mb-2">
                          {/* eslint-disable-next-line @next/next/no-img-element */}
                          <img
                            src={tile.dataUrl}
                            alt={`Tile #${tile.index}`}
                            className="max-h-full max-w-full object-contain"
                          />
                        </div>
                        <div className="flex items-center justify-between text-xs mb-1">
                          <span className="font-semibold text-zinc-900">Tile #{tile.index}</span>
                          <span className="text-[10px] text-zinc-600 font-mono">
                            R{tile.row} : C{tile.col}
                          </span>
                        </div>
                        <p className="text-[10px] text-zinc-600 truncate mb-2">
                          {tile.width} × {tile.height} px
                        </p>
                      </div>

                      <button
                        type="button"
                        onClick={() => handleDownloadSingleTile(tile)}
                        className="w-full py-1 text-[11px] font-medium bg-white hover:bg-zinc-900 hover:text-white text-zinc-700 border border-zinc-200 rounded-lg flex items-center justify-center gap-1 transition-colors"
                      >
                        <Download className="w-3 h-3" />
                        Download
                      </button>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </ToolPageShell>
  );
}
