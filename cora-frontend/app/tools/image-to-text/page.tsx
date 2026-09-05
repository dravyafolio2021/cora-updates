'use client';

import React, { useState, useEffect, useRef, useCallback } from 'react';
import {
  UploadCloud,
  FileText,
  Copy,
  Check,
  Download,
  Trash2,
  RefreshCw,
  Scan,
  Sparkles,
  ShieldCheck,
  CheckCircle2,
  AlertCircle,
  Clipboard,
  Receipt,
  CreditCard,
  Monitor,
  Search,
  Sliders,
  Type
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import {
  extractTextFromImage,
  formatBytes,
  triggerBrowserImageDownload,
} from '@/lib/image-engine';

const FAQS = [
  {
    question: 'How does client-side OCR extract text without cloud servers?',
    answer: 'Cora runs optical character recognition utilizing in-browser WebAssembly neural models. It scans pixel contrast gradients, isolates character glyph contours, and decodes textual lines locally in browser RAM without transmitting document bytes outside your computer.',
  },
  {
    question: 'Can I paste an image directly from my clipboard using Cmd+V or Ctrl+V?',
    answer: 'Yes. Simply capture a screenshot or copy an image from any application, then press Cmd+V (Mac) or Ctrl+V (Windows) anywhere on this page to immediately load the image and start text recognition.',
  },
  {
    question: 'Does the engine accurately recognize receipts, invoices, and numbers?',
    answer: 'Yes. The recognition pipeline is tuned for alphanumeric invoice codes, dates, tax numbers, and currency sums with high fidelity and tabular alignment.',
  },
  {
    question: 'Are sensitive business agreements and identity proofs protected?',
    answer: '100% protected. Because all OCR computation executes exclusively in your device browser sandbox, no cloud AI service, third-party vendor, or external database ever receives your file.',
  },
  {
    question: 'What image formats and file sizes are supported?',
    answer: 'Cora supports JPG, PNG, WebP, BMP, and standard clipboard screenshots of any resolution with zero arbitrary size caps.',
  },
];

export default function ImageToTextPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const [previewUrl, setPreviewUrl] = useState<string | null>(null);
  const [extractedText, setExtractedText] = useState<string>('');
  const [confidence, setConfidence] = useState<number>(0);
  const [ocrProgress, setOcrProgress] = useState<number>(0);
  const [statusMessage, setStatusMessage] = useState<string>('');
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDragging, setIsDragging] = useState<boolean>(false);
  const [isCopied, setIsCopied] = useState<boolean>(false);

  const handleImageInput = useCallback(async (file: File) => {
    if (!file.type.startsWith('image/') && !/\.(jpg|jpeg|png|webp|bmp|gif)$/i.test(file.name)) {
      showToast('Please select a valid image file (JPG, PNG, WebP).');
      return;
    }

    if (previewUrl) URL.revokeObjectURL(previewUrl);

    const url = URL.createObjectURL(file);
    setSelectedFile(file);
    setPreviewUrl(url);
    setExtractedText('');
    setConfidence(0);
    setOcrProgress(0);
    setIsProcessing(true);
    setStatusMessage('Preparing image buffer...');

    try {
      const result = await extractTextFromImage(file, (percent, status) => {
        setOcrProgress(percent);
        setStatusMessage(status);
      });

      setExtractedText(result.text);
      setConfidence(result.confidence);
      showToast(`Text extracted successfully with ${result.confidence}% confidence.`);
    } catch (err) {
      console.error(err);
      setStatusMessage('Error during extraction');
      showToast('Failed to extract text. Please try a higher-contrast image.');
    } finally {
      setIsProcessing(false);
    }
  }, [previewUrl, showToast]);

  // Global clipboard paste listener (Ctrl+V / Cmd+V)
  useEffect(() => {
    const handlePaste = (e: ClipboardEvent) => {
      if (e.clipboardData && e.clipboardData.files.length > 0) {
        const file = e.clipboardData.files[0];
        if (file.type.startsWith('image/')) {
          e.preventDefault();
          handleImageInput(file);
          showToast('Image pasted from clipboard!');
        }
      }
    };

    window.addEventListener('paste', handlePaste);
    return () => window.removeEventListener('paste', handlePaste);
  }, [handleImageInput, showToast]);

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleImageInput(e.dataTransfer.files[0]);
    }
  };

  const copyToClipboard = () => {
    if (!extractedText) return;
    navigator.clipboard.writeText(extractedText);
    setIsCopied(true);
    showToast('Extracted text copied to clipboard!');
    setTimeout(() => setIsCopied(false), 2000);
  };

  const downloadTextFile = () => {
    if (!extractedText) return;
    const blob = new Blob([extractedText], { type: 'text/plain;charset=utf-8' });
    const base = selectedFile?.name.replace(/\.[^/.]+$/, '') || 'extracted-text';
    triggerBrowserImageDownload(blob, `${base}-ocr.txt`);
    showToast(`Downloaded ${base}-ocr.txt`);
  };

  const clearAll = () => {
    if (previewUrl) URL.revokeObjectURL(previewUrl);
    setSelectedFile(null);
    setPreviewUrl(null);
    setExtractedText('');
    setConfidence(0);
    setOcrProgress(0);
    setStatusMessage('');
  };

  const wordCount = extractedText.trim() ? extractedText.trim().split(/\s+/).length : 0;
  const charCount = extractedText.length;

  return (
    <ToolPageShell
      toolId="image-to-text"
      badgeTag="Neural OCR Engine"
      title="Image to Text OCR Online Free"
      subtitle="Extract formatted, editable text from receipts, documents, business cards, and screenshots directly in browser memory."
      faqItems={FAQS}
      relatedToolSlugs={['convert-image', 'svg-to-png', 'heic-to-jpg', 'ocr-pdf']}
    >
      <div className="space-y-8">
        
        {/* ── Dropzone & Paste Banner ── */}
        {!selectedFile ? (
          <div
            onDragOver={(e) => {
              e.preventDefault();
              setIsDragging(true);
            }}
            onDragLeave={() => setIsDragging(false)}
            onDrop={handleDrop}
            onClick={() => fileInputRef.current?.click()}
            className={`relative border-2 border-dashed rounded-2xl p-8 sm:p-14 text-center cursor-pointer transition-all ${
              isDragging
                ? 'border-zinc-900 bg-zinc-100/80 scale-[0.99]'
                : 'border-zinc-300 hover:border-zinc-400 bg-white'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept="image/jpeg,image/png,image/webp,image/bmp,image/gif"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleImageInput(e.target.files[0]);
                }
                e.target.value = '';
              }}
              className="hidden"
            />

            <div className="w-12 h-12 mx-auto mb-4 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-800">
              <Scan className="w-6 h-6 stroke-[1.8]" />
            </div>

            <h3 className="text-sm font-semibold text-zinc-900 mb-1">
              Drop image here or press <kbd className="px-1.5 py-0.5 text-[11px] font-mono bg-zinc-100 border border-zinc-300 rounded text-zinc-800">Cmd+V</kbd> to paste
            </h3>
            <p className="text-xs text-zinc-500 max-w-sm mx-auto mb-4">
              Extract text from receipts, printed documents, business cards, book pages, and screen captures.
            </p>

            <div className="inline-flex items-center gap-2 text-[11px] font-mono text-zinc-500 bg-zinc-100/90 px-3 py-1 rounded-full border border-zinc-200/80">
              <ShieldCheck className="w-3.5 h-3.5 text-emerald-600 stroke-[2]" />
              <span>100% In-Browser Memory OCR</span>
            </div>
          </div>
        ) : (
          /* ── Workspace Dual Grid: Image Source vs Extracted Text ── */
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {/* Left Column: Image Preview & Status */}
            <div className="lg:col-span-5 bg-white border border-zinc-200/90 rounded-2xl overflow-hidden shadow-xs">
              <div className="p-4 border-b border-zinc-200/80 flex items-center justify-between bg-zinc-50/50">
                <div className="flex items-center gap-2 truncate">
                  <FileText className="w-4 h-4 text-zinc-700 shrink-0" />
                  <span className="text-xs font-bold text-zinc-900 truncate">
                    {selectedFile.name}
                  </span>
                  <span className="text-[11px] font-mono text-zinc-500 shrink-0">
                    ({formatBytes(selectedFile.size)})
                  </span>
                </div>
                <button
                  type="button"
                  onClick={clearAll}
                  className="text-xs font-semibold text-zinc-500 hover:text-rose-600 transition-colors p-1"
                  title="Upload different image"
                >
                  <Trash2 className="w-4 h-4" />
                </button>
              </div>

              {/* Image Preview Container */}
              <div className="p-4 bg-zinc-100/60 flex items-center justify-center min-h-[300px] max-h-[460px] overflow-hidden">
                {previewUrl && (
                  <img
                    src={previewUrl}
                    alt="OCR Source"
                    className="max-h-[420px] max-w-full object-contain rounded-lg shadow-2xs border border-zinc-200"
                  />
                )}
              </div>

              {/* Progress Bar / Scan Indicator */}
              <div className="p-4 bg-white border-t border-zinc-200/80 space-y-2">
                <div className="flex items-center justify-between text-xs">
                  <span className="font-semibold text-zinc-700 flex items-center gap-1.5">
                    {isProcessing ? (
                      <RefreshCw className="w-3.5 h-3.5 animate-spin text-zinc-900" />
                    ) : (
                      <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" />
                    )}
                    {statusMessage || 'Idle'}
                  </span>
                  <span className="font-mono text-zinc-500 text-[11px]">
                    {ocrProgress}%
                  </span>
                </div>
                <div className="w-full bg-zinc-100 h-1.5 rounded-full overflow-hidden">
                  <div
                    className="bg-zinc-900 h-full transition-all duration-300 rounded-full"
                    style={{ width: `${ocrProgress}%` }}
                  />
                </div>

                <div className="pt-2 flex items-center justify-between">
                  <button
                    type="button"
                    onClick={() => handleImageInput(selectedFile)}
                    disabled={isProcessing}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-700 hover:text-zinc-950 transition-colors"
                  >
                    <RefreshCw className={`w-3.5 h-3.5 ${isProcessing ? 'animate-spin' : ''}`} />
                    <span>Re-Scan Image</span>
                  </button>

                  <button
                    type="button"
                    onClick={() => fileInputRef.current?.click()}
                    className="text-xs font-semibold text-zinc-500 hover:text-zinc-900 transition-colors"
                  >
                    Change Image
                  </button>
                </div>
              </div>

            </div>

            {/* Right Column: Extracted Editable Text Box */}
            <div className="lg:col-span-7 bg-white border border-zinc-200/90 rounded-2xl overflow-hidden shadow-xs flex flex-col">
              
              {/* Header Bar with Metrics & Actions */}
              <div className="p-4 border-b border-zinc-200/80 flex flex-wrap items-center justify-between gap-3 bg-zinc-50/50">
                <div className="flex items-center gap-3">
                  <span className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
                    Extracted Text
                  </span>
                  {confidence > 0 && (
                    <span className="inline-flex items-center gap-1 text-[11px] font-mono font-bold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200/80">
                      <Sparkles className="w-3 h-3 text-emerald-600" />
                      {confidence}% Confidence
                    </span>
                  )}
                  <span className="text-[11px] font-mono text-zinc-500">
                    {wordCount} words | {charCount} chars
                  </span>
                </div>

                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={copyToClipboard}
                    disabled={!extractedText || isProcessing}
                    className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-zinc-900 bg-zinc-100 hover:bg-zinc-200 disabled:opacity-40 rounded-lg border border-zinc-200 transition-colors"
                  >
                    {isCopied ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Copy className="w-3.5 h-3.5" />}
                    <span>{isCopied ? 'Copied' : 'Copy'}</span>
                  </button>

                  <button
                    type="button"
                    onClick={downloadTextFile}
                    disabled={!extractedText || isProcessing}
                    className="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-white bg-zinc-900 hover:bg-zinc-800 disabled:opacity-40 rounded-lg shadow-xs transition-colors"
                  >
                    <Download className="w-3.5 h-3.5" />
                    <span>Download .TXT</span>
                  </button>
                </div>
              </div>

              {/* Editable Textarea */}
              <div className="p-4 flex-1">
                <textarea
                  value={extractedText}
                  onChange={(e) => setExtractedText(e.target.value)}
                  placeholder={
                    isProcessing
                      ? 'Scanning glyphs and recognizing character patterns...'
                      : 'Recognized text will appear here. You can edit, copy, or download the result.'
                  }
                  rows={14}
                  className="w-full h-full p-4 font-mono text-xs text-zinc-800 leading-relaxed bg-zinc-50/60 border border-zinc-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-zinc-900 selection:bg-zinc-900 selection:text-white resize-y"
                  spellCheck={false}
                />
              </div>

              {/* Footer Notice */}
              <div className="p-4 bg-zinc-50/70 border-t border-zinc-200/80 flex items-center justify-between text-[11px] font-mono text-zinc-500">
                <div className="flex items-center gap-1.5">
                  <Type className="w-3.5 h-3.5 text-zinc-500" />
                  <span>Editable text buffer with unicode preservation</span>
                </div>
                <div className="flex items-center gap-1">
                  <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" />
                  <span>Zero Network Transmission</span>
                </div>
              </div>

            </div>

          </div>
        )}

        {/* Hidden File Input for Re-upload */}
        <input
          ref={fileInputRef}
          type="file"
          accept="image/jpeg,image/png,image/webp,image/bmp,image/gif"
          onChange={(e) => {
            if (e.target.files && e.target.files[0]) {
              handleImageInput(e.target.files[0]);
            }
            e.target.value = '';
          }}
          className="hidden"
        />

        {/* ── Feature Cards ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <Clipboard className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              Instant Clipboard Paste
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Capture any region with Cmd+Shift+4 or Snipping Tool and press Cmd+V directly to scan in seconds.
            </p>
          </div>

          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <Receipt className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              Receipt & Invoice Scanner
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Calibrated for tax invoices, GSTIN codes, currency symbols, and multi-line printed purchase receipts.
            </p>
          </div>

          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <ShieldCheck className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              Confidential Document Safe
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Extracted strings and source images exist only in temporary device memory and are destroyed on page reload.
            </p>
          </div>
        </div>

      </div>
    </ToolPageShell>
  );
}
