'use client';

import React, { useState, useRef, useCallback } from 'react';
import { 
  UploadCloud, 
  Trash2, 
  ArrowUp, 
  ArrowDown, 
  Download, 
  Image as ImageIcon, 
  Settings2, 
  RotateCcw,
  Plus
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { convertImagesToPdf, downloadPdfBlob } from '@/lib/pdf-engine';

interface UploadedImageItem {
  id: string;
  file: File;
  name: string;
  sizeFormatted: string;
  previewUrl: string;
  width: number;
  height: number;
}

export default function ImagesToPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [images, setImages] = useState<UploadedImageItem[]>([]);
  const [pageSize, setPageSize] = useState<'a4' | 'letter' | 'fit'>('a4');
  const [margin, setMargin] = useState<number>(20);
  const [fileName, setFileName] = useState<string>('cora-shoot-deliverables');
  const [isDragging, setIsDragging] = useState<boolean>(false);
  const [isGenerating, setIsGenerating] = useState<boolean>(false);

  const formatFileSize = (bytes: number): string => {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
  };

  const processFiles = useCallback((files: FileList | File[]) => {
    const validFiles: File[] = [];
    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      if (
        file.type.startsWith('image/') ||
        file.name.match(/\.(jpe?g|png|webp|avif)$/i)
      ) {
        validFiles.push(file);
      }
    }

    if (validFiles.length === 0) {
      showToast('Please select valid JPG, PNG, or WebP images');
      return;
    }

    validFiles.forEach((file) => {
      const previewUrl = URL.createObjectURL(file);
      const img = new window.Image();
      img.onload = () => {
        const item: UploadedImageItem = {
          id: `${Date.now()}-${Math.random().toString(36).substring(2, 9)}`,
          file,
          name: file.name,
          sizeFormatted: formatFileSize(file.size),
          previewUrl,
          width: img.naturalWidth || img.width,
          height: img.naturalHeight || img.height,
        };
        setImages((prev) => [...prev, item]);
      };
      img.onerror = () => {
        const item: UploadedImageItem = {
          id: `${Date.now()}-${Math.random().toString(36).substring(2, 9)}`,
          file,
          name: file.name,
          sizeFormatted: formatFileSize(file.size),
          previewUrl,
          width: 0,
          height: 0,
        };
        setImages((prev) => [...prev, item]);
      };
      img.src = previewUrl;
    });

    showToast(`Added ${validFiles.length} image${validFiles.length > 1 ? 's' : ''}`);
  }, [showToast]);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      processFiles(e.target.files);
      e.target.value = '';
    }
  };

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(true);
  };

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      processFiles(e.dataTransfer.files);
    }
  };

  const removeImage = (id: string) => {
    setImages((prev) => {
      const target = prev.find((item) => item.id === id);
      if (target?.previewUrl) {
        URL.revokeObjectURL(target.previewUrl);
      }
      return prev.filter((item) => item.id !== id);
    });
    showToast('Image removed');
  };

  const moveImage = (index: number, direction: 'up' | 'down') => {
    setImages((prev) => {
      const newImages = [...prev];
      const targetIndex = direction === 'up' ? index - 1 : index + 1;
      if (targetIndex < 0 || targetIndex >= newImages.length) return prev;
      const temp = newImages[index];
      newImages[index] = newImages[targetIndex];
      newImages[targetIndex] = temp;
      return newImages;
    });
  };

  const clearAll = () => {
    images.forEach((item) => {
      if (item.previewUrl) URL.revokeObjectURL(item.previewUrl);
    });
    setImages([]);
    showToast('All images cleared');
  };

  const handleGeneratePdf = async () => {
    if (images.length === 0) {
      showToast('Please add at least one image first');
      return;
    }

    try {
      setIsGenerating(true);
      const files = images.map((item) => item.file);
      const pdfBytes = await convertImagesToPdf(files, {
        pageSize,
        margin,
      });

      const cleanFileName = fileName.trim() || 'cora-shoot-deliverables';
      downloadPdfBlob(pdfBytes, cleanFileName);
      showToast(`PDF generated successfully (${images.length} pages)`);
    } catch (err) {
      console.error(err);
      showToast('Failed to generate PDF. Please verify image formats.');
    } finally {
      setIsGenerating(false);
    }
  };

  const faqItems = [
    {
      question: 'Are my photos uploaded to any remote server or AI cloud?',
      answer: 'No, never. The conversion runs 100% in your browser using client-side JavaScript memory. High-resolution shoot photos, confidential receipts, and personal scans never leave your device.'
    },
    {
      question: 'Which page format should I select: A4, US Letter, or Fit Original?',
      answer: 'Choose A4 for standard European and Indian print or document submissions. Choose US Letter for American business standards. Choose "Fit Original" if you want each page size to automatically match the natural aspect ratio of the respective image without white borders.'
    },
    {
      question: 'What image formats can I bundle into a single PDF?',
      answer: 'You can upload JPG, JPEG, PNG, and WebP images simultaneously. Our engine automatically standardizes color profiles and embeds them at optimal DPI.'
    },
    {
      question: 'Can I re-order images before generating the document?',
      answer: 'Yes! Use the Up and Down arrow buttons on any image card to arrange your photo album sequence, proposal slides, or invoice scans in the exact order you want them to appear in the PDF.'
    }
  ];

  return (
    <ToolPageShell
      toolId="images-to-pdf"
      badgeTag="📸 Studio Deliverables Engine"
      title="Images to PDF Converter"
      subtitle="Convert high-res JPG, PNG, and WebP shoot photos, pitch deck slides, or scan receipts into clean, standardized multi-page PDFs with zero server uploads."
      faqItems={faqItems}
    >
      <div className="space-y-6">

        {/* ── 1. Upload Dropzone Card ── */}
        <div
          onDragOver={handleDragOver}
          onDragLeave={handleDragLeave}
          onDrop={handleDrop}
          onClick={() => fileInputRef.current?.click()}
          className={`relative rounded-3xl border-2 border-dashed p-6 sm:p-10 text-center cursor-pointer transition-all ${
            isDragging
              ? 'border-zinc-950 bg-zinc-100/80 scale-[0.99]'
              : 'border-zinc-300/80 bg-white hover:border-zinc-500 hover:bg-zinc-50/50 shadow-xs'
          }`}
        >
          <input
            ref={fileInputRef}
            type="file"
            multiple
            accept="image/jpeg,image/png,image/webp"
            onChange={handleFileChange}
            className="hidden"
          />

          <div className="flex flex-col items-center justify-center max-w-md mx-auto space-y-3">
            <div className="w-14 h-14 rounded-2xl bg-zinc-100 flex items-center justify-center text-zinc-900 border border-zinc-200 shadow-2xs">
              <UploadCloud className="w-7 h-7" />
            </div>
            
            <div className="space-y-1">
              <p className="font-display text-base sm:text-lg font-bold text-zinc-950">
                Drop your shoot images or scans here
              </p>
              <p className="text-xs sm:text-sm text-zinc-500 font-normal">
                Supports multiple JPG, PNG, and WebP files &bull; Zero size limits
              </p>
            </div>

            <div className="flex items-center gap-2 pt-2">
              <span className="px-2.5 py-1 rounded-lg bg-zinc-100 border border-zinc-200 text-[11px] font-mono font-medium text-zinc-700">
                JPG
              </span>
              <span className="px-2.5 py-1 rounded-lg bg-zinc-100 border border-zinc-200 text-[11px] font-mono font-medium text-zinc-700">
                PNG
              </span>
              <span className="px-2.5 py-1 rounded-lg bg-zinc-100 border border-zinc-200 text-[11px] font-mono font-medium text-zinc-700">
                WEBP
              </span>
            </div>
          </div>
        </div>

        {/* ── 2. Settings & Options Console ── */}
        <div className="rounded-3xl bg-white border border-zinc-200/90 p-5 sm:p-6 shadow-xs space-y-5">
          <div className="flex items-center justify-between border-b border-zinc-100 pb-4">
            <div className="flex items-center gap-2">
              <Settings2 className="w-4 h-4 text-zinc-950" />
              <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950">
                Page Formatting & Margins
              </h3>
            </div>
            <span className="text-[11px] font-mono text-zinc-400">
              {images.length} image{images.length === 1 ? '' : 's'} queued
            </span>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {/* Page Size Preset */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Page Size
              </label>
              <div className="grid grid-cols-3 gap-1.5">
                {[
                  { id: 'a4', label: 'A4', sub: '210×297mm' },
                  { id: 'letter', label: 'US Letter', sub: '8.5×11"' },
                  { id: 'fit', label: 'Fit Image', sub: 'Original' },
                ].map((item) => (
                  <button
                    key={item.id}
                    type="button"
                    onClick={() => setPageSize(item.id as 'a4' | 'letter' | 'fit')}
                    className={`p-2.5 rounded-xl text-left transition-all cursor-pointer ${
                      pageSize === item.id
                        ? 'bg-zinc-950 text-white shadow-xs'
                        : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                    }`}
                  >
                    <div className="text-xs font-bold font-mono">{item.label}</div>
                    <div className={`text-[10px] ${pageSize === item.id ? 'text-zinc-300' : 'text-zinc-500'}`}>
                      {item.sub}
                    </div>
                  </button>
                ))}
              </div>
            </div>

            {/* Margin Spacing */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Border Margin
              </label>
              <div className="grid grid-cols-4 gap-1.5">
                {[
                  { val: 0, label: 'None (0)' },
                  { val: 10, label: '10px' },
                  { val: 20, label: '20px' },
                  { val: 40, label: '40px' },
                ].map((m) => (
                  <button
                    key={m.val}
                    type="button"
                    onClick={() => setMargin(m.val)}
                    className={`py-2 px-1 text-center rounded-xl text-xs font-mono font-bold transition-all cursor-pointer ${
                      margin === m.val
                        ? 'bg-zinc-950 text-white shadow-xs'
                        : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                    }`}
                  >
                    {m.label}
                  </button>
                ))}
              </div>
            </div>
          </div>

          {/* Output Document Name */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
              Export PDF Filename
            </label>
            <div className="relative">
              <input
                type="text"
                value={fileName}
                onChange={(e) => setFileName(e.target.value)}
                placeholder="cora-shoot-deliverables"
                className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 font-mono text-xs sm:text-sm font-semibold text-zinc-950 focus:outline-none focus:border-zinc-950 transition-colors"
              />
              <span className="absolute right-3.5 top-1/2 -translate-y-1/2 font-mono text-xs text-zinc-400">
                .pdf
              </span>
            </div>
          </div>
        </div>

        {/* ── 3. Image Sequencing & Thumbnail List ── */}
        {images.length > 0 && (
          <div className="rounded-3xl bg-white border border-zinc-200/90 p-5 sm:p-6 shadow-xs space-y-4">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <ImageIcon className="w-4 h-4 text-zinc-950" />
                <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950">
                  Page Order ({images.length})
                </h3>
              </div>
              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={() => fileInputRef.current?.click()}
                  className="inline-flex items-center gap-1 text-xs font-bold text-zinc-700 hover:text-zinc-950 px-2.5 py-1.5 rounded-lg bg-zinc-100 hover:bg-zinc-200 transition-colors cursor-pointer"
                >
                  <Plus className="w-3.5 h-3.5" />
                  <span>Add More</span>
                </button>
                <button
                  type="button"
                  onClick={clearAll}
                  className="inline-flex items-center gap-1 text-xs font-bold text-zinc-500 hover:text-rose-600 px-2.5 py-1.5 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                >
                  <RotateCcw className="w-3.5 h-3.5" />
                  <span>Clear All</span>
                </button>
              </div>
            </div>

            <div className="space-y-2 max-h-[420px] overflow-y-auto pr-1">
              {images.map((item, index) => (
                <div
                  key={item.id}
                  className="flex items-center justify-between gap-3 p-3 rounded-2xl border border-zinc-200/80 bg-zinc-50/50 hover:bg-white transition-colors"
                >
                  {/* Left: Thumbnail & Sequence Badge */}
                  <div className="flex items-center gap-3 min-w-0">
                    <span className="w-6 h-6 rounded-full bg-zinc-200 text-zinc-700 font-mono text-xs font-bold flex items-center justify-center shrink-0">
                      {index + 1}
                    </span>

                    <div className="relative w-12 h-12 rounded-xl overflow-hidden bg-zinc-200 border border-zinc-300/80 shrink-0">
                      {/* eslint-disable-next-line @next/next/no-img-element */}
                      <img
                        src={item.previewUrl}
                        alt={item.name}
                        className="w-full h-full object-cover"
                      />
                    </div>

                    <div className="min-w-0">
                      <p className="text-xs sm:text-sm font-bold text-zinc-900 truncate">
                        {item.name}
                      </p>
                      <p className="text-[11px] font-mono text-zinc-500 truncate">
                        {item.width > 0 ? `${item.width}×${item.height}px • ` : ''}
                        {item.sizeFormatted}
                      </p>
                    </div>
                  </div>

                  {/* Right: Actions */}
                  <div className="flex items-center gap-1 shrink-0">
                    <button
                      type="button"
                      disabled={index === 0}
                      onClick={() => moveImage(index, 'up')}
                      title="Move up"
                      className="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-950 hover:bg-zinc-200 disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer"
                    >
                      <ArrowUp className="w-4 h-4" />
                    </button>
                    <button
                      type="button"
                      disabled={index === images.length - 1}
                      onClick={() => moveImage(index, 'down')}
                      title="Move down"
                      className="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-950 hover:bg-zinc-200 disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer"
                    >
                      <ArrowDown className="w-4 h-4" />
                    </button>
                    <button
                      type="button"
                      onClick={() => removeImage(item.id)}
                      title="Remove image"
                      className="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ── 4. Primary CTA Action ── */}
        <div className="pt-2">
          <button
            type="button"
            disabled={images.length === 0 || isGenerating}
            onClick={handleGeneratePdf}
            className="w-full py-4 px-6 rounded-2xl bg-zinc-950 hover:bg-zinc-800 disabled:bg-zinc-300 text-white font-bold text-sm sm:text-base flex items-center justify-center gap-2.5 shadow-lg active:scale-[0.99] transition-all cursor-pointer disabled:cursor-not-allowed"
          >
            {isGenerating ? (
              <>
                <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                <span>Generating PDF in Memory...</span>
              </>
            ) : (
              <>
                <Download className="w-4 h-4" />
                <span>
                  Convert & Download PDF ({images.length} {images.length === 1 ? 'Page' : 'Pages'})
                </span>
              </>
            )}
          </button>
        </div>

      </div>
    </ToolPageShell>
  );
}
