'use client';

import React, { useState, useRef, useCallback } from 'react';
import {
  UploadCloud,
  FileImage,
  Download,
  Trash2,
  RefreshCw,
  CheckCircle2,
  Layers,
  Sliders,
  Palette,
  Archive,
  ArrowRight,
  ShieldCheck,
  Zap,
  HardDriveDownload,
  FileCheck
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import {
  convertImageFormat,
  formatBytes,
  triggerBrowserImageDownload,
  createSimpleZip,
  ConvertImageOptions,
} from '@/lib/image-engine';

type TargetFormat = 'image/jpeg' | 'image/png' | 'image/webp';

interface ImageFileItem {
  id: string;
  file: File;
  name: string;
  originalSize: number;
  previewUrl: string;
  convertedBlob?: Blob;
  convertedUrl?: string;
  convertedSize?: number;
  width?: number;
  height?: number;
  status: 'idle' | 'converting' | 'done' | 'error';
  errorMessage?: string;
}

const FAQS = [
  {
    question: 'How does Cora convert images without uploading to an external server?',
    answer: 'Cora performs all image decoding, pixel buffer manipulation, and re-encoding directly in your web browser memory using HTML5 Canvas APIs. Your photos, private documents, and client assets never leave your computer or touch remote servers.',
  },
  {
    question: 'What background color is used when converting transparent PNGs to JPG?',
    answer: 'Because the JPEG specification does not support alpha transparency channels, Cora lets you choose an explicit background fill: pure white, deep black, Anthropic cream, or any custom hex color.',
  },
  {
    question: 'Can I convert multiple images at once and download them as a ZIP?',
    answer: 'Yes. You can drag and drop dozens of images into the batch dropzone. Cora converts each file in parallel in browser memory and allows 1-click batch ZIP download or individual file downloads.',
  },
  {
    question: 'Is there any quality degradation during format conversion?',
    answer: 'When converting to PNG, output is mathematically lossless. When converting to JPG or WebP, you have full control over the quality slider (up to 100%) to achieve optimal sharpness and file weight.',
  },
  {
    question: 'Are there any file size or daily batch limits?',
    answer: 'There are zero paywalls, no daily conversion limits, and no account requirements. Processing runs purely on your device memory.',
  },
];

const PRESET_BG_COLORS = [
  { label: 'White', value: '#FFFFFF', preview: '#FFFFFF', border: 'border-zinc-300' },
  { label: 'Black', value: '#000000', preview: '#000000', border: 'border-zinc-800' },
  { label: 'Cream', value: '#FBFaf7', preview: '#FBFaf7', border: 'border-amber-200' },
  { label: 'Slate', value: '#F4F4F5', preview: '#F4F4F5', border: 'border-zinc-200' },
];

export default function ConvertImagePage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [items, setItems] = useState<ImageFileItem[]>([]);
  const [targetFormat, setTargetFormat] = useState<TargetFormat>('image/jpeg');
  const [quality, setQuality] = useState<number>(0.92);
  const [backgroundColor, setBackgroundColor] = useState<string>('#FFFFFF');
  const [customColor, setCustomColor] = useState<string>('#FFFFFF');
  const [isConvertingAll, setIsConvertingAll] = useState<boolean>(false);
  const [isZipping, setIsZipping] = useState<boolean>(false);
  const [isDragging, setIsDragging] = useState<boolean>(false);

  const getFormatExtension = (format: TargetFormat): string => {
    switch (format) {
      case 'image/jpeg':
        return 'jpg';
      case 'image/png':
        return 'png';
      case 'image/webp':
        return 'webp';
      default:
        return 'jpg';
    }
  };

  const getOutputFilename = (originalName: string, format: TargetFormat): string => {
    const baseName = originalName.substring(0, originalName.lastIndexOf('.')) || originalName;
    return `${baseName}.${getFormatExtension(format)}`;
  };

  const handleFilesAdded = useCallback((fileList: FileList | File[]) => {
    const validImages: File[] = [];
    for (let i = 0; i < fileList.length; i++) {
      const file = fileList[i];
      if (file.type.startsWith('image/') || /\.(jpg|jpeg|png|webp|avif|bmp|gif)$/i.test(file.name)) {
        validImages.push(file);
      }
    }

    if (validImages.length === 0) {
      showToast('Please upload valid image files (JPG, PNG, WebP).');
      return;
    }

    const newItems: ImageFileItem[] = validImages.map((file) => ({
      id: `${file.name}-${Date.now()}-${Math.random().toString(36).substring(2, 7)}`,
      file,
      name: file.name,
      originalSize: file.size,
      previewUrl: URL.createObjectURL(file),
      status: 'idle',
    }));

    setItems((prev) => [...prev, ...newItems]);
    showToast(`Added ${newItems.length} image${newItems.length > 1 ? 's' : ''} to batch queue.`);
  }, [showToast]);

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFilesAdded(e.dataTransfer.files);
    }
  };

  const removeItem = (id: string) => {
    setItems((prev) => {
      const filtered = prev.filter((item) => item.id !== id);
      const target = prev.find((item) => item.id === id);
      if (target?.previewUrl) URL.revokeObjectURL(target.previewUrl);
      if (target?.convertedUrl) URL.revokeObjectURL(target.convertedUrl);
      return filtered;
    });
  };

  const clearAll = () => {
    items.forEach((item) => {
      if (item.previewUrl) URL.revokeObjectURL(item.previewUrl);
      if (item.convertedUrl) URL.revokeObjectURL(item.convertedUrl);
    });
    setItems([]);
  };

  const convertSingleItem = async (item: ImageFileItem): Promise<ImageFileItem> => {
    const options: ConvertImageOptions = {
      format: targetFormat,
      quality,
      backgroundColor: targetFormat === 'image/jpeg' ? backgroundColor : undefined,
    };

    try {
      const result = await convertImageFormat(item.file, options);
      return {
        ...item,
        convertedBlob: result.blob,
        convertedUrl: result.dataUrl,
        convertedSize: result.size,
        width: result.width,
        height: result.height,
        status: 'done',
      };
    } catch (err) {
      return {
        ...item,
        status: 'error',
        errorMessage: err instanceof Error ? err.message : 'Conversion failed',
      };
    }
  };

  const convertAll = async () => {
    if (items.length === 0) return;
    setIsConvertingAll(true);

    setItems((prev) =>
      prev.map((item) => ({ ...item, status: 'converting' as const }))
    );

    const updatedList: ImageFileItem[] = [];
    for (const item of items) {
      const converted = await convertSingleItem(item);
      updatedList.push(converted);
    }

    setItems(updatedList);
    setIsConvertingAll(false);
    showToast(`Converted ${updatedList.filter((i) => i.status === 'done').length} images successfully.`);
  };

  const downloadSingle = (item: ImageFileItem) => {
    if (!item.convertedBlob && !item.convertedUrl) return;
    const filename = getOutputFilename(item.name, targetFormat);
    triggerBrowserImageDownload(item.convertedBlob || item.convertedUrl!, filename);
    showToast(`Downloaded ${filename}`);
  };

  const downloadAllAsZip = async () => {
    const readyItems = items.filter((i) => i.convertedBlob);
    if (readyItems.length === 0) {
      showToast('Please convert images first before downloading.');
      return;
    }

    setIsZipping(true);
    try {
      const zipEntries = readyItems.map((item) => ({
        name: getOutputFilename(item.name, targetFormat),
        blob: item.convertedBlob!,
      }));

      const zipBlob = await createSimpleZip(zipEntries);
      triggerBrowserImageDownload(zipBlob, `cora-converted-images-${getFormatExtension(targetFormat)}.zip`);
      showToast(`ZIP package downloaded (${readyItems.length} files).`);
    } catch (err) {
      console.error(err);
      showToast('Failed to create ZIP package. Downloading files individually...');
      readyItems.forEach((item, idx) => {
        setTimeout(() => downloadSingle(item), idx * 250);
      });
    } finally {
      setIsZipping(false);
    }
  };

  const convertedCount = items.filter((i) => i.status === 'done').length;

  return (
    <ToolPageShell
      toolId="convert-image"
      badgeTag="Image Format Engine"
      title="Convert Image Online Free"
      subtitle="Batch convert photos and graphics between JPG, PNG, and WebP in browser memory with zero upload delays and custom transparency fill."
      faqItems={FAQS}
      relatedToolSlugs={['svg-to-png', 'heic-to-jpg', 'image-to-text', 'compress-pdf']}
    >
      <div className="space-y-8">
        
        {/* ── Control Configuration Bar ── */}
        <div className="bg-white border border-zinc-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
          <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            
            {/* Target Format Selector */}
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wider text-zinc-600 flex items-center gap-1.5">
                <Layers className="w-3.5 h-3.5 text-zinc-700" />
                Target Output Format
              </label>
              <div className="inline-flex p-1 bg-zinc-100 rounded-xl border border-zinc-200/80">
                {(['image/jpeg', 'image/png', 'image/webp'] as TargetFormat[]).map((fmt) => {
                  const label = fmt === 'image/jpeg' ? 'JPG' : fmt === 'image/png' ? 'PNG' : 'WebP';
                  const active = targetFormat === fmt;
                  return (
                    <button
                      key={fmt}
                      type="button"
                      onClick={() => setTargetFormat(fmt)}
                      className={`px-4 py-2 text-xs font-semibold rounded-lg transition-all ${
                        active
                          ? 'bg-white text-zinc-950 shadow-xs border border-zinc-200/80'
                          : 'text-zinc-600 hover:text-zinc-950'
                      }`}
                    >
                      {label}
                    </button>
                  );
                })}
              </div>
            </div>

            {/* Quality Slider (for JPG and WebP) */}
            {targetFormat !== 'image/png' && (
              <div className="space-y-2 lg:min-w-[220px]">
                <div className="flex items-center justify-between">
                  <label className="text-xs font-semibold uppercase tracking-wider text-zinc-600 flex items-center gap-1.5">
                    <Sliders className="w-3.5 h-3.5 text-zinc-700" />
                    Quality
                  </label>
                  <span className="text-xs font-mono font-bold text-zinc-900">
                    {Math.round(quality * 100)}%
                  </span>
                </div>
                <input
                  type="range"
                  min="0.40"
                  max="1.0"
                  step="0.02"
                  value={quality}
                  onChange={(e) => setQuality(parseFloat(e.target.value))}
                  className="w-full accent-zinc-900 h-1.5 bg-zinc-200 rounded-lg cursor-pointer"
                />
              </div>
            )}

            {/* Background Color Picker for JPG */}
            {targetFormat === 'image/jpeg' && (
              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wider text-zinc-600 flex items-center gap-1.5">
                  <Palette className="w-3.5 h-3.5 text-zinc-700" />
                  Transparency Fill (JPG)
                </label>
                <div className="flex items-center gap-2">
                  {PRESET_BG_COLORS.map((col) => (
                    <button
                      key={col.value}
                      type="button"
                      title={col.label}
                      onClick={() => {
                        setBackgroundColor(col.value);
                        setCustomColor(col.value);
                      }}
                      className={`w-7 h-7 rounded-lg border ${col.border} transition-transform ${
                        backgroundColor === col.value ? 'ring-2 ring-zinc-950 scale-110' : 'hover:scale-105'
                      }`}
                      style={{ backgroundColor: col.preview }}
                    />
                  ))}
                  <input
                    type="color"
                    value={customColor}
                    onChange={(e) => {
                      setCustomColor(e.target.value);
                      setBackgroundColor(e.target.value);
                    }}
                    title="Custom background color"
                    className="w-7 h-7 rounded-lg border border-zinc-300 cursor-pointer p-0.5 bg-white"
                  />
                </div>
              </div>
            )}

            {/* Action CTAs */}
            <div className="flex items-center gap-2 pt-2 lg:pt-0">
              <button
                type="button"
                onClick={convertAll}
                disabled={items.length === 0 || isConvertingAll}
                className="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-semibold text-white bg-zinc-900 hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-xs transition-colors"
              >
                <RefreshCw className={`w-3.5 h-3.5 ${isConvertingAll ? 'animate-spin' : ''}`} />
                <span>{isConvertingAll ? 'Converting...' : 'Convert All'}</span>
              </button>

              {convertedCount > 0 && (
                <button
                  type="button"
                  onClick={downloadAllAsZip}
                  disabled={isZipping}
                  className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-zinc-900 bg-zinc-100 hover:bg-zinc-200 rounded-xl border border-zinc-200 transition-colors"
                >
                  <Archive className="w-3.5 h-3.5 text-zinc-700" />
                  <span>{isZipping ? 'Zipping...' : `Download ZIP (${convertedCount})`}</span>
                </button>
              )}
            </div>
          </div>
        </div>

        {/* ── Batch Upload Dropzone ── */}
        <div
          onDragOver={(e) => {
            e.preventDefault();
            setIsDragging(true);
          }}
          onDragLeave={() => setIsDragging(false)}
          onDrop={handleDrop}
          onClick={() => fileInputRef.current?.click()}
          className={`relative border-2 border-dashed rounded-2xl p-8 sm:p-12 text-center cursor-pointer transition-all ${
            isDragging
              ? 'border-zinc-900 bg-zinc-100/80 scale-[0.99]'
              : 'border-zinc-300 hover:border-zinc-400 bg-white'
          }`}
        >
          <input
            ref={fileInputRef}
            type="file"
            multiple
            accept="image/jpeg,image/png,image/webp,image/avif,image/gif,image/bmp"
            onChange={(e) => {
              if (e.target.files) handleFilesAdded(e.target.files);
              e.target.value = '';
            }}
            className="hidden"
          />

          <div className="w-12 h-12 mx-auto mb-4 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-800">
            <UploadCloud className="w-6 h-6 stroke-[1.8]" />
          </div>

          <h3 className="text-sm font-semibold text-zinc-900 mb-1">
            Drop images here or click to browse
          </h3>
          <p className="text-xs text-zinc-500 max-w-sm mx-auto mb-3">
            Supports batch processing of JPG, PNG, and WebP files. All operations run locally inside your browser memory.
          </p>

          <div className="inline-flex items-center gap-2 text-[11px] font-mono text-zinc-500 bg-zinc-100/90 px-2.5 py-1 rounded-full border border-zinc-200/80">
            <ShieldCheck className="w-3.5 h-3.5 text-emerald-600 stroke-[2]" />
            <span>100% Private In-RAM Execution</span>
          </div>
        </div>

        {/* ── Queue & Batch Table ── */}
        {items.length > 0 && (
          <div className="bg-white border border-zinc-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div className="p-4 sm:px-6 border-b border-zinc-200/80 flex items-center justify-between bg-zinc-50/50">
              <div className="flex items-center gap-2">
                <FileImage className="w-4 h-4 text-zinc-700" />
                <span className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
                  Queued Files ({items.length})
                </span>
                {convertedCount > 0 && (
                  <span className="text-[11px] font-mono bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200/80">
                    {convertedCount} Ready
                  </span>
                )}
              </div>
              <button
                type="button"
                onClick={clearAll}
                className="text-xs font-semibold text-zinc-500 hover:text-rose-600 transition-colors flex items-center gap-1"
              >
                <Trash2 className="w-3.5 h-3.5" />
                <span>Clear All</span>
              </button>
            </div>

            <div className="divide-y divide-zinc-200/70">
              {items.map((item) => (
                <div
                  key={item.id}
                  className="p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-zinc-50/40 transition-colors"
                >
                  {/* Thumbnail & File Name */}
                  <div className="flex items-center gap-3.5 min-w-0">
                    <div className="w-12 h-12 rounded-lg bg-zinc-100 border border-zinc-200 overflow-hidden shrink-0 flex items-center justify-center">
                      <img
                        src={item.convertedUrl || item.previewUrl}
                        alt={item.name}
                        className="w-full h-full object-cover"
                      />
                    </div>
                    <div className="min-w-0">
                      <p className="text-xs font-bold text-zinc-900 truncate">
                        {item.name}
                      </p>
                      <div className="flex items-center gap-2 mt-0.5 text-[11px] font-mono text-zinc-500">
                        <span>Original: {formatBytes(item.originalSize)}</span>
                        {item.convertedSize && (
                          <>
                            <ArrowRight className="w-3 h-3 text-zinc-400" />
                            <span className="font-bold text-zinc-800">
                              {getFormatExtension(targetFormat).toUpperCase()}: {formatBytes(item.convertedSize)}
                            </span>
                          </>
                        )}
                        {item.width && item.height && (
                          <span className="text-zinc-400">
                            ({item.width}x{item.height}px)
                          </span>
                        )}
                      </div>
                    </div>
                  </div>

                  {/* Actions & Status */}
                  <div className="flex items-center gap-2 self-end sm:self-center shrink-0">
                    {item.status === 'converting' && (
                      <span className="inline-flex items-center gap-1.5 text-xs text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-lg">
                        <RefreshCw className="w-3 h-3 animate-spin" />
                        Converting...
                      </span>
                    )}

                    {item.status === 'done' && (
                      <>
                        <span className="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200/80">
                          <CheckCircle2 className="w-3.5 h-3.5" />
                          Ready
                        </span>
                        <button
                          type="button"
                          onClick={() => downloadSingle(item)}
                          className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-zinc-900 bg-zinc-100 hover:bg-zinc-200 rounded-lg border border-zinc-200 transition-colors"
                        >
                          <Download className="w-3.5 h-3.5" />
                          <span>Download</span>
                        </button>
                      </>
                    )}

                    {item.status === 'idle' && (
                      <button
                        type="button"
                        onClick={async () => {
                          const updated = await convertSingleItem(item);
                          setItems((prev) =>
                            prev.map((i) => (i.id === item.id ? updated : i))
                          );
                        }}
                        className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-zinc-900 bg-zinc-100 hover:bg-zinc-200 rounded-lg border border-zinc-200 transition-colors"
                      >
                        <RefreshCw className="w-3.5 h-3.5" />
                        <span>Convert</span>
                      </button>
                    )}

                    <button
                      type="button"
                      onClick={() => removeItem(item.id)}
                      className="p-1.5 text-zinc-400 hover:text-rose-600 rounded-lg transition-colors"
                      title="Remove file"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ── Feature Highlights Grid ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <Zap className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              Zero Latency Memory Engine
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Processes conversions in parallel directly inside your browser without uploading to external APIs or waiting in cloud queues.
            </p>
          </div>

          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <Palette className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              Alpha Fill Protection
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Prevents dark or corrupted transparency artifacts when converting transparent PNG assets into universal JPEG formats.
            </p>
          </div>

          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <HardDriveDownload className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              1-Click ZIP Packaging
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Bundle dozens of converted product shots or web graphics into an uncompressed, structured ZIP archive generated instantly in RAM.
            </p>
          </div>
        </div>

      </div>
    </ToolPageShell>
  );
}
