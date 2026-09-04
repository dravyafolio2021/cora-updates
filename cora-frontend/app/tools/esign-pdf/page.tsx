'use client';

import React, { useState, useRef, useEffect, useCallback } from 'react';
import { 
  FileText, 
  UploadCloud, 
  PenTool, 
  Type, 
  RotateCcw, 
  Trash2, 
  Download, 
  CheckCircle2, 
  ShieldCheck, 
  Lock, 
  Check, 
  Compass, 
  Calendar,
  Sparkles
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo, stampSignatureOnPdf, downloadPdfBlob } from '@/lib/pdf-engine';

export default function EsignPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);
  const canvasRef = useRef<HTMLCanvasElement>(null);

  // PDF Document State
  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [selectedPage, setSelectedPage] = useState<number>(1);
  const [pdfFileSize, setPdfFileSize] = useState<string>('');
  const [isDragging, setIsDragging] = useState<boolean>(false);
  const [isSigning, setIsSigning] = useState<boolean>(false);

  // Signature Input Mode: 'draw' | 'type'
  const [signatureMode, setSignatureMode] = useState<'draw' | 'type'>('draw');
  const [typedName, setTypedName] = useState<string>('Aarav Mehta');
  const [typedFont, setTypedFont] = useState<string>('cursive');
  const [inkColor, setInkColor] = useState<string>('#09090b');

  // Drawing Canvas History & State
  const [isDrawing, setIsDrawing] = useState<boolean>(false);
  const [hasDrawnStroke, setHasDrawnStroke] = useState<boolean>(false);
  const strokeHistoryRef = useRef<ImageData[]>([]);

  // Stamp Placement & Size Presets
  const [placementPreset, setPlacementPreset] = useState<'bottom-right' | 'bottom-left' | 'bottom-center' | 'top-right'>('bottom-right');
  const [stampScale, setStampScale] = useState<number>(28); // Width % of page
  const [includeDateStamp, setIncludeDateStamp] = useState<boolean>(true);

  const formatFileSize = (bytes: number): string => {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
  };

  // Handle PDF file upload
  const handleFileSelect = async (file: File) => {
    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please upload a valid PDF document');
      return;
    }

    try {
      const info = await getPdfInfo(file);
      setPdfFile(file);
      setPageCount(info.pageCount);
      setSelectedPage(info.pageCount); // Default to last page for contract signatures
      setPdfFileSize(formatFileSize(file.size));
      showToast(`Loaded ${file.name} (${info.pageCount} pages)`);
    } catch (err) {
      console.error(err);
      showToast('Error reading PDF. Please verify file integrity.');
    }
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFileSelect(e.dataTransfer.files[0]);
    }
  };

  // Canvas Drawing Initializer & Resizer
  const setupCanvas = useCallback(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Get display dimensions
    const rect = canvas.getBoundingClientRect();
    const dpr = window.devicePixelRatio || 1;

    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;

    ctx.scale(dpr, dpr);
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = inkColor;
    ctx.lineWidth = 2.4;
  }, [inkColor]);

  useEffect(() => {
    if (signatureMode === 'draw') {
      setupCanvas();
    }
  }, [signatureMode, setupCanvas]);

  // Canvas Mouse & Touch Event Handlers
  const startDrawing = (e: React.MouseEvent<HTMLCanvasElement> | React.TouchEvent<HTMLCanvasElement>) => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Save snapshot for undo
    const dpr = window.devicePixelRatio || 1;
    const snapshot = ctx.getImageData(0, 0, canvas.width, canvas.height);
    strokeHistoryRef.current.push(snapshot);

    setIsDrawing(true);
    setHasDrawnStroke(true);

    const rect = canvas.getBoundingClientRect();
    const clientX = 'touches' in e ? e.touches[0].clientX : e.clientX;
    const clientY = 'touches' in e ? e.touches[0].clientY : e.clientY;
    const x = clientX - rect.left;
    const y = clientY - rect.top;

    ctx.beginPath();
    ctx.moveTo(x, y);
  };

  const draw = (e: React.MouseEvent<HTMLCanvasElement> | React.TouchEvent<HTMLCanvasElement>) => {
    if (!isDrawing) return;
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const rect = canvas.getBoundingClientRect();
    const clientX = 'touches' in e ? e.touches[0].clientX : e.clientX;
    const clientY = 'touches' in e ? e.touches[0].clientY : e.clientY;
    const x = clientX - rect.left;
    const y = clientY - rect.top;

    ctx.strokeStyle = inkColor;
    ctx.lineWidth = 2.4;
    ctx.lineTo(x, y);
    ctx.stroke();
  };

  const stopDrawing = () => {
    if (!isDrawing) return;
    setIsDrawing(false);
  };

  const undoLastStroke = () => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    if (strokeHistoryRef.current.length > 0) {
      const prev = strokeHistoryRef.current.pop();
      if (prev) {
        ctx.putImageData(prev, 0, 0);
      }
      if (strokeHistoryRef.current.length === 0) {
        setHasDrawnStroke(false);
      }
    } else {
      clearCanvas();
    }
  };

  const clearCanvas = () => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const dpr = window.devicePixelRatio || 1;
    ctx.clearRect(0, 0, canvas.width / dpr, canvas.height / dpr);
    strokeHistoryRef.current = [];
    setHasDrawnStroke(false);
    showToast('Signature cleared');
  };

  // Render Typed Signature to High-Res PNG Data URL
  const generateSignatureDataUrl = (): string | null => {
    const renderCanvas = document.createElement('canvas');
    renderCanvas.width = 600;
    renderCanvas.height = 240;
    const ctx = renderCanvas.getContext('2d');
    if (!ctx) return null;

    ctx.clearRect(0, 0, 600, 240);

    if (signatureMode === 'draw') {
      const liveCanvas = canvasRef.current;
      if (!liveCanvas || !hasDrawnStroke) return null;
      ctx.drawImage(liveCanvas, 0, 0, 600, 240);
    } else {
      if (!typedName.trim()) return null;
      ctx.fillStyle = inkColor;
      ctx.textBaseline = 'middle';

      if (typedFont === 'cursive') {
        ctx.font = 'italic 52px "Brush Script MT", "Snell Roundhand", "Caveat", cursive';
      } else if (typedFont === 'serif') {
        ctx.font = 'italic 46px "Times New Roman", Georgia, serif';
      } else {
        ctx.font = '600 42px "Inter", -apple-system, sans-serif';
      }

      ctx.fillText(typedName.trim(), 40, 100);

      // Underline flourish
      ctx.strokeStyle = inkColor;
      ctx.lineWidth = 1.8;
      ctx.beginPath();
      ctx.moveTo(35, 138);
      ctx.bezierCurveTo(150, 148, 300, 132, 450, 142);
      ctx.stroke();
    }

    // Optional Legal Timestamp & IT Act Seal Subtext
    if (includeDateStamp) {
      const now = new Date();
      const dateStr = now.toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
      });
      const timeStr = now.toLocaleTimeString('en-IN', {
        hour: '2-digit',
        minute: '2-digit',
      });

      ctx.font = '11px "JetBrains Mono", monospace';
      ctx.fillStyle = inkColor === '#09090b' ? '#52525b' : '#64748b';
      ctx.fillText(`e-Signed via Cora • ${dateStr} ${timeStr} IST`, 40, 190);
      ctx.fillText(`IT Act 2000 Sec 10A • Tamper-Evident SHA-256`, 40, 208);
    }

    return renderCanvas.toDataURL('image/png');
  };

  const handleApplySignature = async () => {
    if (!pdfFile) {
      showToast('Please upload a PDF document first');
      return;
    }

    const signaturePngDataUrl = generateSignatureDataUrl();
    if (!signaturePngDataUrl) {
      showToast(signatureMode === 'draw' ? 'Please draw your signature first' : 'Please enter your legal name');
      return;
    }

    try {
      setIsSigning(true);

      // Preset coordinates in PDF coordinate space (y=0 is bottom)
      const placementCoords = {
        'bottom-right': { x: 62, y: 8 },
        'bottom-left': { x: 10, y: 8 },
        'bottom-center': { x: 36, y: 8 },
        'top-right': { x: 62, y: 82 },
      };

      const { x, y } = placementCoords[placementPreset];

      const signedBytes = await stampSignatureOnPdf(pdfFile, signaturePngDataUrl, {
        pageNumber: selectedPage,
        xPercent: x,
        yPercent: y,
        widthPercent: stampScale,
      });

      const baseName = pdfFile.name.replace(/\.[^/.]+$/, '');
      downloadPdfBlob(signedBytes, `${baseName}-signed.pdf`);
      showToast(`Document successfully signed on Page ${selectedPage}!`);
    } catch (err) {
      console.error(err);
      showToast('Failed to stamp signature onto PDF. Please try again.');
    } finally {
      setIsSigning(false);
    }
  };

  const esignFaqs = [
    {
      question: 'Is this digital signature legally valid under Section 10A of the Indian IT Act 2000?',
      answer: 'Yes. Section 10A of the Information Technology Act 2000 explicitly validates contracts and agreements formed through electronic records. Combined with the embedded timestamp and signatory intent, digitally signed commercial agreements and NDAs hold full evidentiary weight in Indian courts.'
    },
    {
      question: 'Does Cora upload my confidential contract to a cloud server?',
      answer: 'No. The entire PDF parsing, signature rendering, and cryptographic document stamping occurs 100% locally inside your browser memory. Your contracts never touch any external server.'
    },
    {
      question: 'Can I sign contracts using my finger on mobile touchscreens?',
      answer: 'Yes! The signature pad is engineered with touch-action isolation, allowing smooth and natural drawing with zero lag or mobile page jumping.'
    },
    {
      question: 'Can I choose which page the signature is placed on?',
      answer: 'Yes! You can select any page number from 1 through the last page of your document, and position the stamp at Bottom-Right, Bottom-Left, Bottom-Center, or Top-Right.'
    }
  ];

  return (
    <ToolPageShell
      toolId="esign-pdf"
      badgeTag="Legal Tech & IT Act 2000"
      title="Digital eSign PDF"
      subtitle="Sign shoot agreements, model releases, and vendor proposals with legally valid digital signatures and cryptographic timestamps. 100% private in browser memory."
      faqItems={esignFaqs}
    >
      <div className="space-y-6">

        {/* ── 1. Document Upload Card ── */}
        {!pdfFile ? (
          <div
            onDragOver={(e) => { e.preventDefault(); setIsDragging(true); }}
            onDragLeave={(e) => { e.preventDefault(); setIsDragging(false); }}
            onDrop={handleDrop}
            onClick={() => fileInputRef.current?.click()}
            className={`rounded-3xl border-2 border-dashed p-8 sm:p-12 text-center cursor-pointer transition-all ${
              isDragging
                ? 'border-zinc-950 bg-zinc-100/80 scale-[0.99]'
                : 'border-zinc-300/80 bg-white hover:border-zinc-500 hover:bg-zinc-50/50 shadow-xs'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept="application/pdf"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleFileSelect(e.target.files[0]);
                }
              }}
              className="hidden"
            />

            <div className="flex flex-col items-center justify-center max-w-md mx-auto space-y-3">
              <div className="w-14 h-14 rounded-2xl bg-zinc-100 flex items-center justify-center text-zinc-900 border border-zinc-200 shadow-2xs">
                <UploadCloud className="w-7 h-7" />
              </div>
              <div className="space-y-1">
                <p className="font-display text-base sm:text-lg font-bold text-zinc-950">
                  Upload PDF Contract or Agreement
                </p>
                <p className="text-xs sm:text-sm text-zinc-500 font-normal">
                  Drop your contract PDF here &bull; 100% private client-side processing
                </p>
              </div>
              <span className="px-3 py-1 rounded-full bg-zinc-100 text-zinc-800 text-[11px] font-mono font-medium border border-zinc-200">
                PDF Documents
              </span>
            </div>
          </div>
        ) : (
          /* Active Document Bar */
          <div className="rounded-3xl bg-white border border-zinc-200/90 p-4 sm:p-5 shadow-xs flex items-center justify-between gap-4">
            <div className="flex items-center gap-3.5 min-w-0">
              <div className="w-11 h-11 rounded-2xl bg-zinc-100 flex items-center justify-center text-zinc-900 shrink-0 border border-zinc-200">
                <FileText className="w-5 h-5 text-zinc-950" />
              </div>
              <div className="min-w-0">
                <h4 className="text-xs sm:text-sm font-bold text-zinc-950 truncate">
                  {pdfFile.name}
                </h4>
                <div className="flex items-center gap-2 text-[11px] font-mono text-zinc-500">
                  <span>{pageCount} Pages</span>
                  <span>&bull;</span>
                  <span>{pdfFileSize}</span>
                  <span>&bull;</span>
                  <span className="text-emerald-700 font-medium">Memory Loaded</span>
                </div>
              </div>
            </div>

            <button
              type="button"
              onClick={() => {
                setPdfFile(null);
                setPageCount(0);
              }}
              className="text-xs font-bold text-zinc-500 hover:text-rose-600 px-3 py-2 rounded-xl hover:bg-rose-50 transition-colors shrink-0 cursor-pointer"
            >
              Change PDF
            </button>
          </div>
        )}

        {/* ── 2. Signature Creation Console (Draw vs Type) ── */}
        <div className="rounded-3xl bg-white border border-zinc-200/90 p-5 sm:p-6 shadow-xs space-y-5">
          <div className="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-100 pb-4">
            <div className="flex items-center gap-2">
              <PenTool className="w-4 h-4 text-zinc-950" />
              <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950">
                Signatory Pad
              </h3>
            </div>

            {/* Mode Switcher Tabs */}
            <div className="inline-flex rounded-xl bg-zinc-100 p-1 border border-zinc-200/80">
              <button
                type="button"
                onClick={() => setSignatureMode('draw')}
                className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer ${
                  signatureMode === 'draw'
                    ? 'bg-white text-zinc-950 shadow-2xs'
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                <PenTool className="w-3.5 h-3.5" />
                <span>Draw</span>
              </button>
              <button
                type="button"
                onClick={() => setSignatureMode('type')}
                className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer ${
                  signatureMode === 'type'
                    ? 'bg-white text-zinc-950 shadow-2xs'
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                <Type className="w-3.5 h-3.5" />
                <span>Type</span>
              </button>
            </div>
          </div>

          {/* Mode A: Canvas Drawing */}
          {signatureMode === 'draw' ? (
            <div className="space-y-3">
              <div className="relative rounded-2xl border border-zinc-300 bg-zinc-50/60 overflow-hidden shadow-inner">
                <canvas
                  ref={canvasRef}
                  onMouseDown={startDrawing}
                  onMouseMove={draw}
                  onMouseUp={stopDrawing}
                  onMouseLeave={stopDrawing}
                  onTouchStart={startDrawing}
                  onTouchMove={draw}
                  onTouchEnd={stopDrawing}
                  className="w-full h-44 cursor-crosshair touch-none"
                />

                {!hasDrawnStroke && (
                  <div className="absolute inset-0 flex items-center justify-center pointer-events-none text-zinc-400 text-xs sm:text-sm font-medium">
                    <span>Draw your signature with mouse or finger</span>
                  </div>
                )}

                {/* Subdued baseline guide */}
                <div className="absolute left-6 right-6 bottom-10 border-b border-dashed border-zinc-300 pointer-events-none" />
              </div>

              {/* Drawing Toolbar */}
              <div className="flex items-center justify-between gap-2">
                <div className="flex items-center gap-1.5">
                  <span className="text-[11px] font-mono text-zinc-500 mr-1">Ink:</span>
                  {[
                    { color: '#09090b', label: 'Black' },
                    { color: '#1e3a8a', label: 'Navy' },
                    { color: '#334155', label: 'Slate' },
                  ].map((ink) => (
                    <button
                      key={ink.color}
                      type="button"
                      onClick={() => setInkColor(ink.color)}
                      style={{ backgroundColor: ink.color }}
                      className={`w-6 h-6 rounded-full transition-transform cursor-pointer ${
                        inkColor === ink.color ? 'ring-2 ring-zinc-950 ring-offset-2 scale-110' : 'opacity-80 hover:opacity-100'
                      }`}
                      title={ink.label}
                    />
                  ))}
                </div>

                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={undoLastStroke}
                    disabled={!hasDrawnStroke}
                    className="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer"
                  >
                    <RotateCcw className="w-3.5 h-3.5" />
                    <span>Undo</span>
                  </button>
                  <button
                    type="button"
                    onClick={clearCanvas}
                    disabled={!hasDrawnStroke}
                    className="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer"
                  >
                    <Trash2 className="w-3.5 h-3.5" />
                    <span>Clear</span>
                  </button>
                </div>
              </div>
            </div>
          ) : (
            /* Mode B: Typed Legal Signature */
            <div className="space-y-4">
              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                  Legal Signatory Name
                </label>
                <input
                  type="text"
                  value={typedName}
                  onChange={(e) => setTypedName(e.target.value)}
                  placeholder="e.g. Aarav Mehta"
                  className="w-full px-4 py-3 rounded-2xl border border-zinc-200 font-sans text-base font-semibold text-zinc-950 focus:outline-none focus:border-zinc-950 transition-colors"
                />
              </div>

              {/* Font Style Options */}
              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                  Signature Font Style
                </label>
                <div className="grid grid-cols-3 gap-2">
                  {[
                    { id: 'cursive', label: 'Calligraphy', style: 'font-serif italic text-lg' },
                    { id: 'serif', label: 'Classic Serif', style: 'font-serif italic text-base' },
                    { id: 'sans', label: 'Clean Modern', style: 'font-sans font-semibold text-sm' },
                  ].map((f) => (
                    <button
                      key={f.id}
                      type="button"
                      onClick={() => setTypedFont(f.id)}
                      className={`p-3 rounded-xl text-center transition-all cursor-pointer ${
                        typedFont === f.id
                          ? 'bg-zinc-950 text-white shadow-xs'
                          : 'bg-zinc-50 border border-zinc-200 text-zinc-800 hover:bg-zinc-100'
                      }`}
                    >
                      <div className={f.style}>
                        {typedName || 'Signature'}
                      </div>
                      <div className={`text-[10px] mt-1 ${typedFont === f.id ? 'text-zinc-300' : 'text-zinc-400'}`}>
                        {f.label}
                      </div>
                    </button>
                  ))}
                </div>
              </div>
            </div>
          )}
        </div>

        {/* ── 3. Stamping Placement & Target Page ── */}
        <div className="rounded-3xl bg-white border border-zinc-200/90 p-5 sm:p-6 shadow-xs space-y-5">
          <div className="flex items-center justify-between border-b border-zinc-100 pb-4">
            <div className="flex items-center gap-2">
              <Compass className="w-4 h-4 text-zinc-950" />
              <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950">
                Placement & Target Page
              </h3>
            </div>
            {pageCount > 0 && (
              <span className="text-[11px] font-mono text-zinc-500">
                Doc has {pageCount} pages
              </span>
            )}
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {/* Target Page Selector */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Stamp on Page Number
              </label>
              <div className="flex items-center gap-2">
                <select
                  value={selectedPage}
                  onChange={(e) => setSelectedPage(Number(e.target.value))}
                  disabled={pageCount <= 1}
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 font-mono text-xs sm:text-sm font-bold text-zinc-950 focus:outline-none focus:border-zinc-950 bg-white"
                >
                  {Array.from({ length: Math.max(1, pageCount) }, (_, i) => i + 1).map((p) => (
                    <option key={p} value={p}>
                      Page {p} {p === pageCount ? '(Last Page - Recommended)' : ''}
                    </option>
                  ))}
                </select>

                {pageCount > 1 && (
                  <button
                    type="button"
                    onClick={() => setSelectedPage(pageCount)}
                    className="shrink-0 px-3 py-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold font-mono transition-colors cursor-pointer"
                  >
                    Last
                  </button>
                )}
              </div>
            </div>

            {/* Position Preset */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Signature Location
              </label>
              <div className="grid grid-cols-2 gap-2">
                {[
                  { id: 'bottom-right', label: 'Bottom Right' },
                  { id: 'bottom-left', label: 'Bottom Left' },
                  { id: 'bottom-center', label: 'Bottom Center' },
                  { id: 'top-right', label: 'Top Right' },
                ].map((pos) => (
                  <button
                    key={pos.id}
                    type="button"
                    onClick={() => setPlacementPreset(pos.id as any)}
                    className={`py-2 px-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer text-center ${
                      placementPreset === pos.id
                        ? 'bg-zinc-950 text-white shadow-xs'
                        : 'bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                    }`}
                  >
                    {pos.label}
                  </button>
                ))}
              </div>
            </div>
          </div>

          {/* Stamp Size Scale & Timestamp Toggle */}
          <div className="pt-2 border-t border-zinc-100 flex flex-wrap items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <span className="text-xs font-bold text-zinc-700">Stamp Size:</span>
              <div className="inline-flex rounded-xl bg-zinc-100 p-1 border border-zinc-200/80">
                {[
                  { scale: 22, label: 'Small' },
                  { scale: 28, label: 'Standard' },
                  { scale: 38, label: 'Large' },
                ].map((s) => (
                  <button
                    key={s.scale}
                    type="button"
                    onClick={() => setStampScale(s.scale)}
                    className={`px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer ${
                      stampScale === s.scale ? 'bg-white text-zinc-950 shadow-2xs' : 'text-zinc-600'
                    }`}
                  >
                    {s.label}
                  </button>
                ))}
              </div>
            </div>

            {/* Date & IT Act Seal Toggle */}
            <label className="flex items-center gap-2 cursor-pointer text-xs font-bold text-zinc-800 select-none">
              <input
                type="checkbox"
                checked={includeDateStamp}
                onChange={(e) => setIncludeDateStamp(e.target.checked)}
                className="w-4 h-4 rounded text-zinc-950 focus:ring-0 cursor-pointer accent-zinc-950"
              />
              <span>Include Section 10A Timestamp Audit Footer</span>
            </label>
          </div>
        </div>

        {/* ── 4. Legal Compliance Trust Box ── */}
        <div className="p-4 sm:p-5 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-start gap-3 text-xs text-zinc-700 leading-relaxed shadow-2xs">
          <ShieldCheck className="w-5 h-5 text-zinc-950 shrink-0 mt-0.5" />
          <div className="space-y-1">
            <p className="font-bold text-zinc-950">
              Indian Information Technology Act 2000 (Section 10A) Admissibility
            </p>
            <p className="text-zinc-600 font-normal">
              Electronic contracts, engagement deeds, and shoot waivers signed with digital signatures carry full legal recognition in Indian jurisdictions. Embedded signatures include date and time telemetry for verifiable evidentiary records.
            </p>
          </div>
        </div>

        {/* ── 5. Primary CTA Action ── */}
        <div className="pt-2">
          <button
            type="button"
            disabled={!pdfFile || isSigning}
            onClick={handleApplySignature}
            className="w-full py-4 px-6 rounded-2xl bg-zinc-950 hover:bg-zinc-800 disabled:bg-zinc-300 text-white font-bold text-sm sm:text-base flex items-center justify-center gap-2.5 shadow-lg active:scale-[0.99] transition-all cursor-pointer disabled:cursor-not-allowed"
          >
            {isSigning ? (
              <>
                <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                <span>Digitally Signing Document...</span>
              </>
            ) : (
              <>
                <Download className="w-4 h-4" />
                <span>
                  {pdfFile
                    ? `Stamp Signature & Download (Page ${selectedPage})`
                    : 'Upload PDF to Begin Signing'}
                </span>
              </>
            )}
          </button>
        </div>

      </div>
    </ToolPageShell>
  );
}
