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
  Archive,
  ArrowRight,
  ShieldCheck,
  Zap,
  HardDriveDownload,
  Smartphone,
  AlertCircle
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import {
  decodeHeicToBlob,
  formatBytes,
  triggerBrowserImageDownload,
  createSimpleZip,
} from '@/lib/image-engine';

type OutputFormat = 'image/jpeg' | 'image/png';

interface HeicFileItem {
  id: string;
  file: File;
  name: string;
  originalSize: number;
  previewUrl?: string;
  convertedBlob?: Blob;
  convertedSize?: number;
  width?: number;
  height?: number;
  status: 'idle' | 'decoding' | 'done' | 'error';
  errorMessage?: string;
}

const FAQS = [
  {
    question: 'What is HEIC format and why do Apple devices use it?',
    answer: 'HEIC (High Efficiency Image Container) is Apple’s default image format based on HEVC (H.265) compression. While it halves storage requirements on iPhones, many Windows computers, web forms, and Android devices cannot open HEIC files without specialized software.',
  },
  {
    question: 'Does Cora upload my private iPhone photos to a cloud server?',
    answer: 'No. All decoding and re-encoding occur 100% locally in your browser memory. Your personal family snapshots, client photoshoot assets, and sensitive documents are never transmitted outside your computer.',
  },
  {
    question: 'Can I convert multiple HEIC photos at once and download a ZIP file?',
    answer: 'Yes. You can drag and drop dozens of iPhone photos simultaneously. Cora decodes each image in browser RAM and lets you download individual files or package everything into an instant ZIP archive with one click.',
  },
  {
    question: 'Should I choose JPG or PNG as the target format?',
    answer: 'JPG is recommended for photographic snapshots and camera photos as it provides universal compatibility with compact file weight. PNG is ideal if you want lossless rendering.',
  },
  {
    question: 'Will image orientation and aspect ratio be preserved?',
    answer: 'Yes. The decoder automatically honors camera orientation flags, ensuring that vertical portrait photos and wide landscape shots render correctly without distortion.',
  },
];

export default function HeicToJpgPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [items, setItems] = useState<HeicFileItem[]>([]);
  const [outputFormat, setOutputFormat] = useState<OutputFormat>('image/jpeg');
  const [quality, setQuality] = useState<number>(0.92);
  const [isDecodingAll, setIsDecodingAll] = useState<boolean>(false);
  const [isZipping, setIsZipping] = useState<boolean>(false);
  const [isDragging, setIsDragging] = useState<boolean>(false);

  const getExtension = (format: OutputFormat): string => {
    return format === 'image/jpeg' ? 'jpg' : 'png';
  };

  const getTargetFilename = (originalName: string, format: OutputFormat): string => {
    const base = originalName.replace(/\.(heic|heif)$/i, '') || 'iphone-photo';
    return `${base}.${getExtension(format)}`;
  };

  const handleFiles = useCallback((fileList: FileList | File[]) => {
    const validFiles: File[] = [];
    for (let i = 0; i < fileList.length; i++) {
      const f = fileList[i];
      if (
        f.name.toLowerCase().endsWith('.heic') ||
        f.name.toLowerCase().endsWith('.heif') ||
        f.type === 'image/heic' ||
        f.type === 'image/heif'
      ) {
        validFiles.push(f);
      }
    }

    if (validFiles.length === 0) {
      showToast('Please select Apple .heic or .heif photo files.');
      return;
    }

    const newItems: HeicFileItem[] = validFiles.map((file) => ({
      id: `${file.name}-${Date.now()}-${Math.random().toString(36).substring(2, 7)}`,
      file,
      name: file.name,
      originalSize: file.size,
      status: 'idle',
    }));

    setItems((prev) => [...prev, ...newItems]);
    showToast(`Added ${newItems.length} HEIC photo${newItems.length > 1 ? 's' : ''}.`);
  }, [showToast]);

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFiles(e.dataTransfer.files);
    }
  };

  const removeItem = (id: string) => {
    setItems((prev) => {
      const target = prev.find((i) => i.id === id);
      if (target?.previewUrl) URL.revokeObjectURL(target.previewUrl);
      return prev.filter((i) => i.id !== id);
    });
  };

  const clearAll = () => {
    items.forEach((item) => {
      if (item.previewUrl) URL.revokeObjectURL(item.previewUrl);
    });
    setItems([]);
  };

  const decodeSingleItem = async (item: HeicFileItem): Promise<HeicFileItem> => {
    try {
      const res = await decodeHeicToBlob(item.file, outputFormat, quality);
      return {
        ...item,
        previewUrl: res.dataUrl,
        convertedBlob: res.blob,
        convertedSize: res.blob.size,
        width: res.width,
        height: res.height,
        status: 'done',
      };
    } catch (err) {
      return {
        ...item,
        status: 'error',
        errorMessage: err instanceof Error ? err.message : 'Decoding failed',
      };
    }
  };

  const decodeAll = async () => {
    if (items.length === 0) return;
    setIsDecodingAll(true);

    setItems((prev) =>
      prev.map((item) => ({ ...item, status: 'decoding' as const }))
    );

    const updated: HeicFileItem[] = [];
    for (const item of items) {
      const decoded = await decodeSingleItem(item);
      updated.push(decoded);
    }

    setItems(updated);
    setIsDecodingAll(false);
    const successCount = updated.filter((i) => i.status === 'done').length;
    showToast(`Decoded ${successCount} photo${successCount > 1 ? 's' : ''} successfully.`);
  };

  const downloadSingle = (item: HeicFileItem) => {
    if (!item.convertedBlob && !item.previewUrl) return;
    const name = getTargetFilename(item.name, outputFormat);
    triggerBrowserImageDownload(item.convertedBlob || item.previewUrl!, name);
    showToast(`Downloaded ${name}`);
  };

  const downloadAllZip = async () => {
    const ready = items.filter((i) => i.convertedBlob);
    if (ready.length === 0) {
      showToast('Please decode photos before downloading.');
      return;
    }

    setIsZipping(true);
    try {
      const zipList = ready.map((i) => ({
        name: getTargetFilename(i.name, outputFormat),
        blob: i.convertedBlob!,
      }));
      const zipBlob = await createSimpleZip(zipList);
      triggerBrowserImageDownload(zipBlob, `cora-heic-converted-${getExtension(outputFormat)}.zip`);
      showToast(`ZIP package downloaded (${ready.length} files).`);
    } catch (err) {
      console.error(err);
      showToast('Failed to create ZIP package. Downloading files individually...');
      ready.forEach((item, idx) => {
        setTimeout(() => downloadSingle(item), idx * 250);
      });
    } finally {
      setIsZipping(false);
    }
  };

  const readyCount = items.filter((i) => i.status === 'done').length;

  return (
    <ToolPageShell
      toolId="heic-to-jpg"
      badgeTag="Apple iOS Photo Engine"
      title="HEIC to JPG Converter"
      subtitle="Decode Apple iPhone and iPad .heic / .heif photos to universal JPG or PNG in browser memory with batch ZIP download."
      faqItems={FAQS}
      relatedToolSlugs={['convert-image', 'svg-to-png', 'image-to-text', 'compress-pdf']}
    >
      <div className="space-y-8">
        
        {/* ── Configuration Bar ── */}
        <div className="bg-white border border-zinc-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
          <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            
            {/* Target Format */}
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wider text-zinc-600 flex items-center gap-1.5">
                <Layers className="w-3.5 h-3.5 text-zinc-700" />
                Target Output Format
              </label>
              <div className="inline-flex p-1 bg-zinc-100 rounded-xl border border-zinc-200/80">
                {(['image/jpeg', 'image/png'] as OutputFormat[]).map((fmt) => {
                  const label = fmt === 'image/jpeg' ? 'JPEG / JPG' : 'Lossless PNG';
                  const active = outputFormat === fmt;
                  return (
                    <button
                      key={fmt}
                      type="button"
                      onClick={() => setOutputFormat(fmt)}
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

            {/* Quality Slider (for JPG) */}
            {outputFormat === 'image/jpeg' && (
              <div className="space-y-2 lg:min-w-[240px]">
                <div className="flex items-center justify-between">
                  <label className="text-xs font-semibold uppercase tracking-wider text-zinc-600 flex items-center gap-1.5">
                    <Sliders className="w-3.5 h-3.5 text-zinc-700" />
                    JPEG Quality
                  </label>
                  <span className="text-xs font-mono font-bold text-zinc-900">
                    {Math.round(quality * 100)}%
                  </span>
                </div>
                <input
                  type="range"
                  min="0.50"
                  max="1.0"
                  step="0.02"
                  value={quality}
                  onChange={(e) => setQuality(parseFloat(e.target.value))}
                  className="w-full accent-zinc-900 h-1.5 bg-zinc-200 rounded-lg cursor-pointer"
                />
              </div>
            )}

            {/* Actions */}
            <div className="flex items-center gap-2 pt-2 lg:pt-0">
              <button
                type="button"
                onClick={decodeAll}
                disabled={items.length === 0 || isDecodingAll}
                className="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-semibold text-white bg-zinc-900 hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-xs transition-colors"
              >
                <RefreshCw className={`w-3.5 h-3.5 ${isDecodingAll ? 'animate-spin' : ''}`} />
                <span>{isDecodingAll ? 'Decoding...' : 'Convert All Photos'}</span>
              </button>

              {readyCount > 0 && (
                <button
                  type="button"
                  onClick={downloadAllZip}
                  disabled={isZipping}
                  className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-zinc-900 bg-zinc-100 hover:bg-zinc-200 rounded-xl border border-zinc-200 transition-colors"
                >
                  <Archive className="w-3.5 h-3.5 text-zinc-700" />
                  <span>{isZipping ? 'Packaging...' : `Download ZIP (${readyCount})`}</span>
                </button>
              )}
            </div>

          </div>
        </div>

        {/* ── Dropzone ── */}
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
            accept=".heic,.heif,image/heic,image/heif"
            onChange={(e) => {
              if (e.target.files) handleFiles(e.target.files);
              e.target.value = '';
            }}
            className="hidden"
          />

          <div className="w-12 h-12 mx-auto mb-4 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-800">
            <Smartphone className="w-6 h-6 stroke-[1.8]" />
          </div>

          <h3 className="text-sm font-semibold text-zinc-900 mb-1">
            Drop Apple HEIC or HEIF photos here
          </h3>
          <p className="text-xs text-zinc-500 max-w-sm mx-auto mb-3">
            Supports batch processing of iPhone and iPad camera photos. Execution runs entirely in browser memory.
          </p>

          <div className="inline-flex items-center gap-2 text-[11px] font-mono text-zinc-500 bg-zinc-100/90 px-2.5 py-1 rounded-full border border-zinc-200/80">
            <ShieldCheck className="w-3.5 h-3.5 text-emerald-600 stroke-[2]" />
            <span>Client-Side Local Memory Decoding</span>
          </div>
        </div>

        {/* ── Batch Queue ── */}
        {items.length > 0 && (
          <div className="bg-white border border-zinc-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div className="p-4 sm:px-6 border-b border-zinc-200/80 flex items-center justify-between bg-zinc-50/50">
              <div className="flex items-center gap-2">
                <FileImage className="w-4 h-4 text-zinc-700" />
                <span className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
                  HEIC Files ({items.length})
                </span>
                {readyCount > 0 && (
                  <span className="text-[11px] font-mono bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200/80">
                    {readyCount} Decoded
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
                  {/* Thumbnail & Info */}
                  <div className="flex items-center gap-3.5 min-w-0">
                    <div className="w-12 h-12 rounded-lg bg-zinc-100 border border-zinc-200 overflow-hidden shrink-0 flex items-center justify-center">
                      {item.previewUrl ? (
                        <img
                          src={item.previewUrl}
                          alt={item.name}
                          className="w-full h-full object-cover"
                        />
                      ) : (
                        <Smartphone className="w-5 h-5 text-zinc-400" />
                      )}
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
                              {getExtension(outputFormat).toUpperCase()}: {formatBytes(item.convertedSize)}
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
                    {item.status === 'decoding' && (
                      <span className="inline-flex items-center gap-1.5 text-xs text-zinc-600 bg-zinc-100 px-2.5 py-1 rounded-lg">
                        <RefreshCw className="w-3 h-3 animate-spin" />
                        Decoding...
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
                          const updated = await decodeSingleItem(item);
                          setItems((prev) =>
                            prev.map((i) => (i.id === item.id ? updated : i))
                          );
                        }}
                        className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-zinc-900 bg-zinc-100 hover:bg-zinc-200 rounded-lg border border-zinc-200 transition-colors"
                      >
                        <RefreshCw className="w-3.5 h-3.5" />
                        <span>Decode</span>
                      </button>
                    )}

                    {item.status === 'error' && (
                      <span className="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-700 bg-rose-50 px-2 py-1 rounded-lg border border-rose-200">
                        <AlertCircle className="w-3.5 h-3.5" />
                        Failed
                      </span>
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

        {/* ── Feature Highlight Cards ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <Smartphone className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              Apple iOS Photo Support
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Native decoder calibrated for iPhone 11 through iPhone 16 Pro Max camera outputs, Live Photos, and portrait modes.
            </p>
          </div>

          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <ShieldCheck className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              100% On-Device Privacy
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Family photos and confidential client deliverables never touch external cloud storage or remote servers.
            </p>
          </div>

          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <HardDriveDownload className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              Instant ZIP Download
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Convert whole vacation albums or photo libraries in batches and export them as a single convenient .zip archive.
            </p>
          </div>
        </div>

      </div>
    </ToolPageShell>
  );
}
