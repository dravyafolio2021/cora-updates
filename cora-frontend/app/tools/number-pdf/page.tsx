'use client';

import React, { useState, useRef, useCallback, useMemo } from 'react';
import { 
  UploadCloud, 
  FileText, 
  Download, 
  Trash2, 
  Check, 
  ShieldCheck, 
  Hash, 
  Sliders, 
  Eye, 
  AlignLeft, 
  AlignCenter, 
  AlignRight, 
  ArrowUpRight,
  Plus,
  Minus,
  RotateCcw,
  Sparkles,
  Layers,
  CheckCircle2
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { addPageNumbersToPdf, downloadPdfBlob, getPdfInfo, PageNumberOptions } from '@/lib/pdf-engine';

type PositionOption = 'bottom-center' | 'bottom-right' | 'bottom-left' | 'top-right';
type FormatOption = 'page_x_of_y' | 'page_x' | 'num_only';

interface PositionChoice {
  id: PositionOption;
  label: string;
  sublabel: string;
  icon: React.ComponentType<{ className?: string; strokeWidth?: number }>;
}

const POSITION_CHOICES: PositionChoice[] = [
  {
    id: 'bottom-center',
    label: 'Bottom Center',
    sublabel: 'Standard footer',
    icon: AlignCenter,
  },
  {
    id: 'bottom-right',
    label: 'Bottom Right',
    sublabel: 'Corporate reports',
    icon: AlignRight,
  },
  {
    id: 'bottom-left',
    label: 'Bottom Left',
    sublabel: 'Legal binders',
    icon: AlignLeft,
  },
  {
    id: 'top-right',
    label: 'Top Right',
    sublabel: 'Header pagination',
    icon: ArrowUpRight,
  },
];

interface FormatChoice {
  id: FormatOption;
  label: string;
  sample: (current: number, total: number) => string;
  description: string;
}

const FORMAT_CHOICES: FormatChoice[] = [
  {
    id: 'page_x_of_y',
    label: 'Page X of Y',
    sample: (cur, tot) => `Page ${cur} of ${tot}`,
    description: 'Best for audit trails & multi-page contracts',
  },
  {
    id: 'page_x',
    label: 'Page X',
    sample: (cur) => `Page ${cur}`,
    description: 'Clean editorial & presentation pagination',
  },
  {
    id: 'num_only',
    label: 'Numbers Only',
    sample: (cur) => `${cur}`,
    description: 'Minimalist numeric style',
  },
];

const NUMBER_PDF_FAQS = [
  {
    question: 'Are added page numbers court-admissible and audit-compliant?',
    answer: 'Yes. Page numbering strictly conforms to legal indexing standards for commercial deeds, tender submissions, and court evidence binders. Sequential pagination prevents page tampering and satisfies Section 10A IT Act documentation standards.'
  },
  {
    question: 'How do I start numbering from a specific number (e.g. Page 2 or Bates offset)?',
    answer: 'Use the "Start Number" control. You can set the initial number to any index (such as starting at 10 for appendices or continuing from a previous contract amendment volume).'
  },
  {
    question: 'Can I choose where the page numbers appear on each sheet?',
    answer: 'Yes. Choose between Bottom-Center, Bottom-Right, Bottom-Left, or Top-Right. The tool calculates exact page dimensions per sheet, ensuring uniform margins across mixed portrait and landscape pages.'
  },
  {
    question: 'Does this tool upload or store my confidential documents?',
    answer: 'No. All typography rendering and PDF byte stamping is executed 100% locally in your web browser using WebAssembly. Zero document bytes or metadata ever leave your computer.'
  },
  {
    question: 'Will numbering damage existing vector graphics or form fields?',
    answer: 'No. Page numbers are stamped onto a separate non-destructive vector overlay layer. Existing text, high-resolution photography, tables, and digital signature fields remain untouched.'
  }
];

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
}

export default function NumberPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // File state
  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isNumbered, setIsNumbered] = useState<boolean>(false);

  // Pagination configuration
  const [position, setPosition] = useState<PositionOption>('bottom-center');
  const [format, setFormat] = useState<FormatOption>('page_x_of_y');
  const [startNumber, setStartNumber] = useState<number>(1);
  const [fontSize, setFontSize] = useState<number>(10);
  const [outputFileName, setOutputFileName] = useState<string>('cora-numbered-document.pdf');

  const handleFileLoad = useCallback(async (file: File) => {
    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please select a valid PDF file (.pdf)');
      return;
    }

    try {
      const info = await getPdfInfo(file);
      setSelectedFile(file);
      setPageCount(info.pageCount);
      setIsNumbered(false);

      const baseName = file.name.replace(/\.[^/.]+$/, '');
      setOutputFileName(`${baseName}-numbered.pdf`);

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
    setIsNumbered(false);
    setStartNumber(1);
    setFontSize(10);
    setPosition('bottom-center');
    setFormat('page_x_of_y');
  };

  const handleApplyNumbering = async () => {
    if (!selectedFile) {
      showToast('Please upload a PDF file first');
      return;
    }

    setIsProcessing(true);
    try {
      const options: PageNumberOptions = {
        position,
        format,
        startNumber: Math.max(1, startNumber),
        fontSize,
      };

      const numberedBytes = await addPageNumbersToPdf(selectedFile, options);

      const finalName = outputFileName.trim().length > 0
        ? (outputFileName.endsWith('.pdf') ? outputFileName : `${outputFileName}.pdf`)
        : 'numbered-document.pdf';

      downloadPdfBlob(numberedBytes, finalName);
      setIsNumbered(true);
      showToast(`Successfully added page numbers to ${pageCount} pages!`);
    } catch (err) {
      console.error(err);
      showToast('Failed to add page numbers. Please check file permissions.');
    } finally {
      setIsProcessing(false);
    }
  };

  // Sample preview text calculation
  const previewSampleText = useMemo(() => {
    const total = pageCount > 0 ? pageCount : 12;
    if (format === 'page_x_of_y') {
      return `Page ${startNumber} of ${total + startNumber - 1}`;
    }
    if (format === 'page_x') {
      return `Page ${startNumber}`;
    }
    return `${startNumber}`;
  }, [format, startNumber, pageCount]);

  return (
    <ToolPageShell
      toolId="number-pdf"
      badgeTag="Document Numbering Engine"
      title="Add Page Numbers to PDF"
      subtitle="Insert clean, customized page numbers, legal pagination, and Bates-style headers or footers across any PDF. 100% private in your browser."
      faqItems={NUMBER_PDF_FAQS}
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
              Add uniform pagination, Bates numbering, and audit-ready headers or footers in pure JavaScript.
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
                    <span>{pageCount} {pageCount === 1 ? 'Page' : 'Pages'} to paginate</span>
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

        {/* ── Main Controls & Live Preview (When file loaded) ── */}
        {selectedFile && (
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {/* Left Column: Numbering Options (7 cols) */}
            <div className="lg:col-span-7 space-y-6">

              {/* 1. Position Selector */}
              <div className="space-y-3">
                <label className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-600 block">
                  1. Stamp Position
                </label>
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                  {POSITION_CHOICES.map((pos) => {
                    const isSelected = position === pos.id;
                    const Icon = pos.icon;
                    return (
                      <button
                        key={pos.id}
                        type="button"
                        onClick={() => setPosition(pos.id)}
                        className={`p-3 rounded-xl border text-left transition-all cursor-pointer ${
                          isSelected
                            ? 'border-zinc-950 bg-zinc-950 text-white shadow-2xs'
                            : 'border-zinc-200 bg-white hover:border-zinc-300 text-zinc-900'
                        }`}
                      >
                        <div className="flex items-center justify-between mb-2">
                          <Icon className={`w-4 h-4 ${isSelected ? 'text-white' : 'text-zinc-600'}`} strokeWidth={1.8} />
                          {isSelected && <Check className="w-3 h-3 text-white" strokeWidth={2.2} />}
                        </div>
                        <div className={`text-xs font-bold leading-tight ${isSelected ? 'text-white' : 'text-zinc-900'}`}>
                          {pos.label}
                        </div>
                        <div className={`text-[10px] mt-0.5 ${isSelected ? 'text-zinc-300' : 'text-zinc-400'}`}>
                          {pos.sublabel}
                        </div>
                      </button>
                    );
                  })}
                </div>
              </div>

              {/* 2. Format Selector */}
              <div className="space-y-3">
                <label className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-600 block">
                  2. Numbering Format
                </label>
                <div className="space-y-2">
                  {FORMAT_CHOICES.map((fmt) => {
                    const isSelected = format === fmt.id;
                    return (
                      <button
                        key={fmt.id}
                        type="button"
                        onClick={() => setFormat(fmt.id)}
                        className={`w-full p-3.5 rounded-xl border text-left transition-all flex items-center justify-between cursor-pointer ${
                          isSelected
                            ? 'border-zinc-950 bg-zinc-50/80 ring-1 ring-zinc-950'
                            : 'border-zinc-200 bg-white hover:border-zinc-300'
                        }`}
                      >
                        <div className="flex items-center gap-3">
                          <div className={`w-4 h-4 rounded-full border flex items-center justify-center ${
                            isSelected ? 'border-zinc-950 bg-zinc-950' : 'border-zinc-300 bg-white'
                          }`}>
                            {isSelected && <div className="w-1.5 h-1.5 rounded-full bg-white" />}
                          </div>
                          <div>
                            <span className="text-xs font-bold text-zinc-900 block">
                              {fmt.label}
                            </span>
                            <span className="text-[11px] text-zinc-500">
                              {fmt.description}
                            </span>
                          </div>
                        </div>

                        <span className="px-2.5 py-1 rounded-md bg-zinc-100 text-zinc-800 text-xs font-mono font-semibold">
                          {fmt.sample(startNumber, pageCount || 12)}
                        </span>
                      </button>
                    );
                  })}
                </div>
              </div>

              {/* 3. Start Number & Font Size Grid */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                {/* Start Number */}
                <div className="p-4 rounded-2xl border border-zinc-200 bg-white space-y-2">
                  <label className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-600 block">
                    Start Number
                  </label>
                  <p className="text-[11px] text-zinc-500">
                    Offset first stamped page (e.g. skip cover)
                  </p>
                  <div className="flex items-center gap-2 pt-1">
                    <button
                      type="button"
                      onClick={() => setStartNumber((prev) => Math.max(1, prev - 1))}
                      className="w-8 h-8 rounded-lg border border-zinc-200 hover:border-zinc-300 bg-zinc-50 hover:bg-zinc-100 flex items-center justify-center text-zinc-700 transition-colors"
                      title="Decrease start number"
                    >
                      <Minus className="w-3.5 h-3.5" strokeWidth={1.8} />
                    </button>
                    <input
                      type="number"
                      min={1}
                      value={startNumber}
                      onChange={(e) => setStartNumber(Math.max(1, parseInt(e.target.value, 10) || 1))}
                      className="w-20 px-2 py-1.5 text-center text-sm font-mono font-bold rounded-lg border border-zinc-200 bg-white focus:outline-none focus:border-zinc-950"
                    />
                    <button
                      type="button"
                      onClick={() => setStartNumber((prev) => prev + 1)}
                      className="w-8 h-8 rounded-lg border border-zinc-200 hover:border-zinc-300 bg-zinc-50 hover:bg-zinc-100 flex items-center justify-center text-zinc-700 transition-colors"
                      title="Increase start number"
                    >
                      <Plus className="w-3.5 h-3.5" strokeWidth={1.8} />
                    </button>
                  </div>
                </div>

                {/* Font Size */}
                <div className="p-4 rounded-2xl border border-zinc-200 bg-white space-y-2">
                  <label className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-600 block">
                    Font Size
                  </label>
                  <p className="text-[11px] text-zinc-500">
                    Helvetica vector point size
                  </p>
                  <div className="flex items-center gap-1.5 pt-1">
                    {[8, 9, 10, 11, 12].map((sz) => (
                      <button
                        key={sz}
                        type="button"
                        onClick={() => setFontSize(sz)}
                        className={`flex-1 py-1.5 rounded-lg border text-xs font-mono font-semibold transition-all ${
                          fontSize === sz
                            ? 'border-zinc-950 bg-zinc-950 text-white'
                            : 'border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-700'
                        }`}
                      >
                        {sz}pt
                      </button>
                    ))}
                  </div>
                </div>

              </div>

              {/* Filename & Execute Button */}
              <div className="space-y-4 pt-2">
                <div>
                  <label className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-600 block mb-1.5">
                    Output Filename
                  </label>
                  <input
                    type="text"
                    value={outputFileName}
                    onChange={(e) => setOutputFileName(e.target.value)}
                    placeholder="numbered-document.pdf"
                    className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 bg-white text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950 shadow-2xs"
                  />
                </div>

                <button
                  type="button"
                  disabled={isProcessing}
                  onClick={handleApplyNumbering}
                  className="w-full py-4 px-6 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-sm flex items-center justify-center gap-2.5 shadow-md active:scale-[0.99] disabled:opacity-50 disabled:pointer-events-none transition-all cursor-pointer"
                >
                  {isProcessing ? (
                    <>
                      <div className="w-4 h-4 rounded-full border-2 border-white/30 border-t-white animate-spin" />
                      <span>Numbering {pageCount} Pages in Browser...</span>
                    </>
                  ) : (
                    <>
                      <Download className="w-4 h-4" strokeWidth={1.8} />
                      <span>Apply Page Numbers & Download</span>
                    </>
                  )}
                </button>
              </div>

            </div>

            {/* Right Column: Live Interactive Preview Sheet (5 cols) */}
            <div className="lg:col-span-5 space-y-4">
              <label className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-600 flex items-center gap-1.5">
                <Eye className="w-3.5 h-3.5 text-zinc-500" strokeWidth={1.8} />
                <span>Live Pagination Preview</span>
              </label>

              {/* Miniature A4 Document Mockup */}
              <div className="relative aspect-[1/1.38] w-full max-w-[340px] mx-auto bg-white rounded-2xl border-2 border-zinc-300 shadow-md p-6 flex flex-col justify-between select-none">
                
                {/* Top Section */}
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-1.5">
                    <div className="w-2.5 h-2.5 rounded-full bg-zinc-200" />
                    <div className="h-2 w-16 bg-zinc-100 rounded-sm" />
                  </div>
                  {position === 'top-right' ? (
                    <div className="px-2 py-0.5 rounded border border-zinc-900 bg-zinc-950 text-white text-[10px] font-mono font-bold ring-2 ring-zinc-200">
                      {previewSampleText}
                    </div>
                  ) : (
                    <div className="h-2 w-8 bg-zinc-100 rounded-sm" />
                  )}
                </div>

                {/* Simulated Content Lines */}
                <div className="space-y-3 py-4 opacity-40">
                  <div className="h-3 w-3/4 bg-zinc-200 rounded-sm" />
                  <div className="h-2 w-full bg-zinc-100 rounded-sm" />
                  <div className="h-2 w-5/6 bg-zinc-100 rounded-sm" />
                  <div className="h-2 w-4/5 bg-zinc-100 rounded-sm" />
                  <div className="h-10 w-full bg-zinc-50 border border-dashed border-zinc-200 rounded-lg flex items-center justify-center">
                    <span className="text-[9px] font-mono text-zinc-400">Section 10A IT Act Digital Vault</span>
                  </div>
                  <div className="h-2 w-full bg-zinc-100 rounded-sm" />
                  <div className="h-2 w-2/3 bg-zinc-100 rounded-sm" />
                </div>

                {/* Bottom Section with Conditional Placement */}
                <div className="relative pt-4 border-t border-zinc-100 flex items-center justify-between min-h-[32px]">
                  
                  {/* Bottom Left */}
                  <div className="w-1/3 flex justify-start">
                    {position === 'bottom-left' && (
                      <div className="px-2 py-0.5 rounded border border-zinc-900 bg-zinc-950 text-white text-[10px] font-mono font-bold ring-2 ring-zinc-200">
                        {previewSampleText}
                      </div>
                    )}
                  </div>

                  {/* Bottom Center */}
                  <div className="w-1/3 flex justify-center">
                    {position === 'bottom-center' && (
                      <div className="px-2 py-0.5 rounded border border-zinc-900 bg-zinc-950 text-white text-[10px] font-mono font-bold ring-2 ring-zinc-200">
                        {previewSampleText}
                      </div>
                    )}
                  </div>

                  {/* Bottom Right */}
                  <div className="w-1/3 flex justify-end">
                    {position === 'bottom-right' && (
                      <div className="px-2 py-0.5 rounded border border-zinc-900 bg-zinc-950 text-white text-[10px] font-mono font-bold ring-2 ring-zinc-200">
                        {previewSampleText}
                      </div>
                    )}
                  </div>

                </div>

                {/* Stamping spec indicator overlay */}
                <div className="absolute bottom-2 inset-x-0 text-center">
                  <span className="text-[9px] font-mono text-zinc-400">
                    Helvetica {fontSize}pt | Position: {position}
                  </span>
                </div>

              </div>

              {/* Status Note */}
              <div className="p-3.5 rounded-xl bg-zinc-100/80 border border-zinc-200 text-xs text-zinc-600 flex items-start gap-2.5">
                <ShieldCheck className="w-4 h-4 text-zinc-500 shrink-0 mt-0.5" strokeWidth={1.8} />
                <span>
                  Page numbers are automatically aligned on each page using native vector coordinates. Mixed portrait/landscape pages are scaled dynamically.
                </span>
              </div>

              {isNumbered && (
                <div className="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" strokeWidth={2.2} />
                  <span>Document downloaded! Check your downloads folder.</span>
                </div>
              )}

            </div>

          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
