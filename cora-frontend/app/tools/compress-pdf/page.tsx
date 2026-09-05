'use client';

import React, { useState, useRef, useCallback } from 'react';
import { 
  UploadCloud, 
  FileText, 
  Download, 
  Trash2, 
  Sparkles, 
  Layers, 
  Check, 
  ShieldCheck, 
  ArrowDown, 
  Gauge, 
  Zap, 
  SlidersHorizontal,
  RefreshCw,
  HardDriveDownload,
  CheckCircle2
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { ToolOutcomeData } from '@/components/tools/ToolOutcomeRoiBanner';
import { ToolOutcomeModalData } from '@/components/tools/ToolOutcomeDrawerModal';
import { useToast } from '@/components/ui/Toast';
import { compressPdf, downloadPdfBlob, getPdfInfo } from '@/lib/pdf-engine';

type CompressionTier = 'extreme' | 'recommended' | 'low';

interface CompressionPreset {
  id: CompressionTier;
  label: string;
  badge: string;
  reductionEstimate: string;
  description: string;
  idealFor: string;
}

const COMPRESSION_PRESETS: CompressionPreset[] = [
  {
    id: 'extreme',
    label: 'Extreme Compression',
    badge: 'Max Reduction',
    reductionEstimate: '~60% reduction',
    description: 'Aggressively downsamples objects and strips redundant metadata streams for lowest file size.',
    idealFor: 'WhatsApp sharing, strict email attachments (<5MB), low bandwidth',
  },
  {
    id: 'recommended',
    label: 'Recommended Balance',
    badge: 'Optimal Ratio',
    reductionEstimate: '~35% reduction',
    description: 'Perfect equilibrium between file size and document crispness. Retains vector clarity and sharp text.',
    idealFor: 'Client proposals, pitch decks, portfolio presentations, general sharing',
  },
  {
    id: 'low',
    label: 'Light Compression',
    badge: 'High Fidelity',
    reductionEstimate: '~18% reduction',
    description: 'Subtle stream recompaction while strictly maintaining maximum image DPI and print fidelity.',
    idealFor: 'Print-ready artwork, formal legal deeds, high-resolution photo portfolios',
  },
];

const COMPRESS_PDF_FAQS = [
  {
    question: 'How does in-browser PDF compression work without uploading files?',
    answer: 'Cora recompacts PDF object streams, removes unreferenced structural dictionaries, and optimizes cross-reference tables directly inside your browser memory using WebAssembly. Your sensitive proposals, financial statements, and client documents never leave your computer or touch external servers.'
  },
  {
    question: 'Will text and vector logos lose clarity after compression?',
    answer: 'No. Digital text and vector shapes (such as company logos, icons, and lines) are preserved mathematically without degradation. The optimizer focuses on deflating embedded stream redundancies and clearing unused internal metadata dictionaries.'
  },
  {
    question: 'Which compression preset should I choose for email or WhatsApp?',
    answer: 'For WhatsApp and email limits (like Gmail 25MB or corporate 10MB filters), Recommended Balance is ideal. If your document still exceeds upload ceilings on government portals or tender platforms, select Extreme Compression to achieve up to 60% size reduction.'
  },
  {
    question: 'Is there a file size limit or daily quota for compressing documents?',
    answer: 'There are zero paywalls, zero daily limits, and no account signup required. Because execution happens client-side, the only practical limit is your device browser memory, comfortably supporting multi-hundred page documents.'
  },
  {
    question: 'Are my confidential client agreements safe from data scraping?',
    answer: 'Absolutely. Unlike conventional cloud PDF converters that store copies on remote servers, Cora operates on a zero-knowledge local architecture. No network requests are made with your file content.'
  }
];

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
}

export default function CompressPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [tier, setTier] = useState<CompressionTier>('recommended');
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [isProcessing, setIsProcessing] = useState<boolean>(false);

  // Compression results state
  const [compressionResult, setCompressionResult] = useState<{
    pdfBytes: Uint8Array;
    originalSizeBytes: number;
    compressedSizeBytes: number;
    compressionRatioPercent: number;
  } | null>(null);

  const [outputFileName, setOutputFileName] = useState<string>('cora-compressed-document.pdf');

  const handleFileLoad = useCallback(async (file: File) => {
    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please select a valid PDF file (.pdf)');
      return;
    }

    try {
      const info = await getPdfInfo(file);
      setSelectedFile(file);
      setPageCount(info.pageCount);
      setCompressionResult(null);

      const baseName = file.name.replace(/\.[^/.]+$/, '');
      setOutputFileName(`${baseName}-compressed.pdf`);

      showToast(`Loaded ${file.name} (${info.pageCount} ${info.pageCount === 1 ? 'page' : 'pages'})`);
    } catch (err) {
      console.error(err);
      showToast('Could not inspect PDF. File may be encrypted or corrupted.');
    }
  }, [showToast]);

  const handleFileInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      handleFileLoad(e.target.files[0]);
      e.target.value = '';
    }
  };

  const handleDrop = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFileLoad(e.dataTransfer.files[0]);
    }
  };

  const handleDragOver = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(true);
  };

  const handleDragLeave = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
  };

  const handleReset = () => {
    setSelectedFile(null);
    setPageCount(0);
    setCompressionResult(null);
    setTier('recommended');
  };

  const [activeOutcome, setActiveOutcome] = useState<ToolOutcomeData | null>(null);
  const [activeOutcomeModal, setActiveOutcomeModal] = useState<ToolOutcomeModalData | null>(null);

  const handleExecuteCompression = async () => {
    if (!selectedFile) {
      showToast('Please upload a PDF file first');
      return;
    }

    setIsProcessing(true);
    try {
      const result = await compressPdf(selectedFile, tier);
      setCompressionResult(result);
      showToast(`Optimized successfully: ${result.compressionRatioPercent}% size saved`);
    } catch (err) {
      console.error(err);
      showToast('Compression failed. Please try a different PDF or preset.');
    } finally {
      setIsProcessing(false);
    }
  };

  const handleDownload = () => {
    if (!compressionResult || !selectedFile) return;
    const finalName = outputFileName.trim().length > 0
      ? (outputFileName.endsWith('.pdf') ? outputFileName : `${outputFileName}.pdf`)
      : 'compressed-document.pdf';

    downloadPdfBlob(compressionResult.pdfBytes, finalName);
    showToast('Download started');

    const originalKb = (selectedFile.size / 1024).toFixed(0);
    const newKb = (compressionResult.compressedSizeBytes / 1024).toFixed(0);

    // 1. Keep Top Banner
    setActiveOutcome({
      summaryTitle: `Optimized: ${originalKb} KB down to ${newKb} KB (${compressionResult.compressionRatioPercent}% Saved)`,
      timeSavedEstimate: '~15 mins turnaround time saved',
      securityProof: '0 bytes sent to external servers • 100% In-Browser RAM',
      suggestedNextStep: {
        headline: 'Delivering final project deliverables or portfolio decks to a client?',
        description: 'Host your deliverables in a branded, high-speed client portal with live open-tracking, feedback threads, and zero email attachment size limits.',
        ctaLabel: 'Share via Client Portal with Rohan (Free)',
        ctaHref: `/workspace/login?mode=signup&ref=tofu_compressed_deck&savings=${compressionResult.compressionRatioPercent}`,
      },
    });

    // 2. Trigger Post-Download Outcome Modal Drawer (Instant Value-First Bridge)
    setActiveOutcomeModal({
      summaryTitle: `Optimized ${originalKb} KB down to ${newKb} KB (${compressionResult.compressionRatioPercent}% Space Saved)`,
      timeSavedEstimate: '~15 mins manual optimization saved',
      securityProof: '100% Client-Side RAM • Zero Bytes Sent to Remote Cloud',
      downloadFileName: finalName,
      suggestedNextStep: {
        badge: 'Recommended Next Action for Studios',
        headline: 'Delivering final deliverables or decks to a client?',
        description: 'Skip messy WeTransfer links or email size limits. Deliver via a personalized client portal where clients view, approve, and sign deliverables with automated WhatsApp updates.',
        ctaLabel: 'Deliver via Client Portal with Rohan (Free)',
        ctaHref: `/workspace/login?mode=signup&ref=tofu_modal_compressed&savings=${compressionResult.compressionRatioPercent}`,
      },
    });
  };

  return (
    <ToolPageShell
      toolId="compress-pdf"
      badgeTag="High-Traffic PDF Optimizer"
      title="Compress PDF Online"
      subtitle="Reduce PDF file size in seconds while preserving document crispness. 100% private, executed locally in your browser with zero server uploads."
      faqItems={COMPRESS_PDF_FAQS}
      activeOutcome={activeOutcome}
      activeOutcomeModal={activeOutcomeModal}
      onCloseOutcomeModal={() => setActiveOutcomeModal(null)}
    >
      <div className="space-y-6">
        
        {/* ── File Selection & Drop Area ── */}
        {!selectedFile ? (
          <div
            onDrop={handleDrop}
            onDragOver={handleDragOver}
            onDragLeave={handleDragLeave}
            onClick={() => fileInputRef.current?.click()}
            className={`relative group cursor-pointer border-2 border-dashed rounded-3xl p-8 sm:p-12 text-center transition-all ${
              isDraggingOver
                ? 'border-zinc-900 bg-zinc-100/80 scale-[0.99]'
                : 'border-zinc-200 hover:border-zinc-400 bg-white shadow-xs hover:shadow-sm'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept="application/pdf,.pdf"
              onChange={handleFileInputChange}
              className="hidden"
            />

            <div className="mx-auto w-16 h-16 rounded-2xl bg-zinc-100 group-hover:bg-zinc-950 group-hover:text-white text-zinc-700 flex items-center justify-center mb-5 transition-all duration-200">
              <UploadCloud className="w-8 h-8" strokeWidth={1.8} />
            </div>

            <h3 className="text-lg font-bold text-zinc-900 mb-2">
              Drop your PDF file here, or browse
            </h3>
            <p className="text-xs sm:text-sm text-zinc-500 max-w-md mx-auto mb-6">
              Instant in-memory optimization. Perfect for heavy client decks, proposals, contracts, and portfolios.
            </p>

            <div className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-100 text-zinc-700 text-xs font-medium">
              <ShieldCheck className="w-4 h-4 text-zinc-600" strokeWidth={1.8} />
              <span>100% In-Browser Privacy — Zero Server Uploads</span>
            </div>
          </div>
        ) : (
          /* ── Loaded File Header Card ── */
          <div className="p-4 sm:p-5 rounded-2xl border border-zinc-200 bg-white shadow-2xs">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div className="flex items-start sm:items-center gap-3.5 min-w-0">
                <div className="w-12 h-12 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0 text-zinc-800">
                  <FileText className="w-6 h-6" strokeWidth={1.8} />
                </div>
                <div className="min-w-0">
                  <h4 className="text-sm font-semibold text-zinc-900 truncate" title={selectedFile.name}>
                    {selectedFile.name}
                  </h4>
                  <div className="flex items-center gap-3 text-xs text-zinc-500 font-mono mt-0.5">
                    <span>{formatBytes(selectedFile.size)}</span>
                    <span className="w-1 h-1 rounded-full bg-zinc-300" />
                    <span>{pageCount} {pageCount === 1 ? 'Page' : 'Pages'}</span>
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-2 self-end sm:self-center">
                <button
                  type="button"
                  onClick={() => fileInputRef.current?.click()}
                  className="px-3 py-1.5 rounded-lg border border-zinc-200 hover:border-zinc-300 text-zinc-700 hover:text-zinc-900 text-xs font-medium transition-colors"
                >
                  Change File
                </button>
                <input
                  ref={fileInputRef}
                  type="file"
                  accept="application/pdf,.pdf"
                  onChange={handleFileInputChange}
                  className="hidden"
                />
                <button
                  type="button"
                  onClick={handleReset}
                  className="p-1.5 rounded-lg border border-zinc-200 hover:border-red-200 text-zinc-400 hover:text-red-600 hover:bg-red-50/50 transition-colors"
                  title="Remove File"
                >
                  <Trash2 className="w-4 h-4" strokeWidth={1.8} />
                </button>
              </div>
            </div>
          </div>
        )}

        {/* ── Compression Tier Selector ── */}
        {selectedFile && (
          <div className="space-y-3">
            <div className="flex items-center justify-between">
              <label className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-600 flex items-center gap-1.5">
                <SlidersHorizontal className="w-3.5 h-3.5 text-zinc-500" strokeWidth={1.8} />
                <span>Select Compression Preset</span>
              </label>
              <span className="text-[11px] text-zinc-400 font-mono">
                {COMPRESSION_PRESETS.find((p) => p.id === tier)?.reductionEstimate}
              </span>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
              {COMPRESSION_PRESETS.map((preset) => {
                const isSelected = tier === preset.id;
                return (
                  <button
                    key={preset.id}
                    type="button"
                    onClick={() => {
                      setTier(preset.id);
                      if (compressionResult) {
                        setCompressionResult(null);
                      }
                    }}
                    className={`text-left p-4 rounded-2xl border transition-all relative cursor-pointer ${
                      isSelected
                        ? 'border-zinc-950 bg-zinc-900 text-white shadow-sm ring-1 ring-zinc-950'
                        : 'border-zinc-200 bg-white hover:border-zinc-300 text-zinc-900'
                    }`}
                  >
                    <div className="flex items-center justify-between mb-2">
                      <span className={`text-[10px] font-mono uppercase tracking-wider px-2 py-0.5 rounded-md font-semibold ${
                        isSelected 
                          ? 'bg-zinc-800 text-zinc-200 border border-zinc-700' 
                          : 'bg-zinc-100 text-zinc-600 border border-zinc-200'
                      }`}>
                        {preset.badge}
                      </span>
                      {isSelected && (
                        <div className="w-5 h-5 rounded-full bg-white text-zinc-950 flex items-center justify-center">
                          <Check className="w-3 h-3" strokeWidth={2.2} />
                        </div>
                      )}
                    </div>

                    <h4 className={`text-sm font-bold mb-1 ${isSelected ? 'text-white' : 'text-zinc-900'}`}>
                      {preset.label}
                    </h4>
                    
                    <p className={`text-xs leading-relaxed mb-3 ${isSelected ? 'text-zinc-300' : 'text-zinc-500'}`}>
                      {preset.description}
                    </p>

                    <div className={`pt-2 border-t text-[11px] ${
                      isSelected ? 'border-zinc-800 text-zinc-400' : 'border-zinc-100 text-zinc-400'
                    }`}>
                      <span className="font-medium">Best for: </span>
                      {preset.idealFor}
                    </div>
                  </button>
                );
              })}
            </div>
          </div>
        )}

        {/* ── Execute Compress Button (when not yet compressed or re-compressing) ── */}
        {selectedFile && !compressionResult && (
          <div className="pt-2">
            <button
              type="button"
              disabled={isProcessing}
              onClick={handleExecuteCompression}
              className="w-full py-4 px-6 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-sm flex items-center justify-center gap-2.5 shadow-md active:scale-[0.99] disabled:opacity-50 disabled:pointer-events-none transition-all cursor-pointer"
            >
              {isProcessing ? (
                <>
                  <div className="w-4 h-4 rounded-full border-2 border-white/30 border-t-white animate-spin" />
                  <span>Optimizing PDF in Browser Memory...</span>
                </>
              ) : (
                <>
                  <Gauge className="w-4 h-4" strokeWidth={1.8} />
                  <span>Compress PDF with {COMPRESSION_PRESETS.find((p) => p.id === tier)?.label}</span>
                </>
              )}
            </button>
            <p className="text-[11px] text-zinc-400 text-center mt-2.5 flex items-center justify-center gap-1.5">
              <ShieldCheck className="w-3.5 h-3.5 text-zinc-400" strokeWidth={1.8} />
              <span>Zero data transmission. Fully executed in your browser.</span>
            </p>
          </div>
        )}

        {/* ── Live Before/After Size Comparison & Instant Download ── */}
        {selectedFile && compressionResult && (
          <div className="space-y-6 pt-2">
            <div className="p-6 rounded-3xl border border-zinc-200 bg-white shadow-sm space-y-6">
              
              {/* Header Status */}
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-zinc-100">
                <div className="flex items-center gap-2.5">
                  <div className="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                    <CheckCircle2 className="w-4 h-4" strokeWidth={2.2} />
                  </div>
                  <div>
                    <h4 className="text-sm font-bold text-zinc-900">
                      Compression Complete
                    </h4>
                    <p className="text-xs text-zinc-500">
                      Optimized using {COMPRESSION_PRESETS.find((p) => p.id === tier)?.label}
                    </p>
                  </div>
                </div>

                <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-700 text-xs font-mono font-bold self-start sm:self-auto">
                  <span>{compressionResult.compressionRatioPercent}% Saved</span>
                </div>
              </div>

              {/* Before vs After Metric Grid */}
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-100">
                  <span className="text-[10px] font-mono uppercase tracking-wider text-zinc-400 block mb-1">
                    Original Size
                  </span>
                  <span className="text-lg font-mono font-bold text-zinc-700">
                    {formatBytes(compressionResult.originalSizeBytes)}
                  </span>
                </div>

                <div className="p-4 rounded-2xl bg-zinc-950 text-white border border-zinc-900 shadow-2xs">
                  <span className="text-[10px] font-mono uppercase tracking-wider text-zinc-400 block mb-1">
                    Optimized Size
                  </span>
                  <span className="text-lg font-mono font-bold text-white">
                    {formatBytes(compressionResult.compressedSizeBytes)}
                  </span>
                </div>

                <div className="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                  <span className="text-[10px] font-mono uppercase tracking-wider text-emerald-600 block mb-1">
                    Total Reduced
                  </span>
                  <span className="text-lg font-mono font-bold text-emerald-700">
                    {formatBytes(Math.max(0, compressionResult.originalSizeBytes - compressionResult.compressedSizeBytes))}
                  </span>
                </div>
              </div>

              {/* Visual Footprint Comparison Bar */}
              <div className="space-y-2">
                <div className="flex items-center justify-between text-xs text-zinc-500 font-mono">
                  <span>Footprint Comparison</span>
                  <span>{formatBytes(compressionResult.compressedSizeBytes)} / {formatBytes(compressionResult.originalSizeBytes)}</span>
                </div>
                <div className="h-4 w-full bg-zinc-100 rounded-full overflow-hidden flex p-0.5">
                  <div 
                    style={{ width: `${Math.max(8, 100 - compressionResult.compressionRatioPercent)}%` }}
                    className="h-full bg-zinc-950 rounded-full transition-all duration-500"
                    title="Optimized file footprint"
                  />
                  <div 
                    style={{ width: `${Math.min(92, compressionResult.compressionRatioPercent)}%` }}
                    className="h-full bg-emerald-500/20 rounded-r-full transition-all duration-500"
                    title="Space reclaimed"
                  />
                </div>
                <div className="flex items-center justify-between text-[11px] text-zinc-400">
                  <span className="flex items-center gap-1.5">
                    <span className="w-2 h-2 rounded-full bg-zinc-950 inline-block" />
                    New Compact Size ({100 - compressionResult.compressionRatioPercent}%)
                  </span>
                  <span className="flex items-center gap-1.5">
                    <span className="w-2 h-2 rounded-full bg-emerald-300 inline-block" />
                    Reclaimed Space ({compressionResult.compressionRatioPercent}%)
                  </span>
                </div>
              </div>

              {/* Export Filename & Download Controls */}
              <div className="pt-2 space-y-4">
                <div>
                  <label className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-600 block mb-1.5">
                    Download Filename
                  </label>
                  <input
                    type="text"
                    value={outputFileName}
                    onChange={(e) => setOutputFileName(e.target.value)}
                    placeholder="compressed-document.pdf"
                    className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 bg-white text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950 shadow-2xs"
                  />
                </div>

                <div className="flex flex-col sm:flex-row gap-2.5">
                  <button
                    type="button"
                    onClick={handleDownload}
                    className="flex-1 py-3.5 px-5 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-sm flex items-center justify-center gap-2.5 shadow-md active:scale-[0.99] transition-all cursor-pointer"
                  >
                    <Download className="w-4 h-4" strokeWidth={1.8} />
                    <span>Download Compressed PDF</span>
                  </button>

                  <button
                    type="button"
                    onClick={() => {
                      setCompressionResult(null);
                      handleExecuteCompression();
                    }}
                    className="px-4 py-3.5 rounded-2xl border border-zinc-200 hover:border-zinc-300 bg-white text-zinc-700 hover:text-zinc-900 text-xs font-semibold flex items-center justify-center gap-2 transition-colors cursor-pointer"
                  >
                    <RefreshCw className="w-3.5 h-3.5" strokeWidth={1.8} />
                    <span>Re-compress</span>
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
