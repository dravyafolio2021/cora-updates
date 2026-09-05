'use client';

import React, { useState, useEffect, useRef, useCallback } from 'react';
import {
  UploadCloud,
  FileCode,
  Download,
  Eye,
  Sliders,
  Sparkles,
  RefreshCw,
  Copy,
  Check,
  CheckCircle2,
  ShieldCheck,
  Maximize2,
  Code,
  Zap,
  Printer,
  Monitor
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import {
  rasterizeSvgToPng,
  triggerBrowserImageDownload,
  formatBytes,
} from '@/lib/image-engine';

const SAMPLE_SVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300" width="400" height="300">
  <defs>
    <linearGradient id="coraGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#18181B" />
      <stop offset="100%" stop-color="#3F3F46" />
    </linearGradient>
  </defs>
  <rect width="400" height="300" rx="24" fill="#FAFAF9" />
  <circle cx="200" cy="150" r="80" fill="url(#coraGrad)" />
  <path d="M 165 150 L 190 175 L 245 120" fill="none" stroke="#FFFFFF" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" />
  <text x="200" y="260" font-family="system-ui, sans-serif" font-size="14" font-weight="600" fill="#71717A" text-anchor="middle" letter-spacing="1.5">CORA VECTOR STUDIO</text>
</svg>`;

const FAQS = [
  {
    question: 'How does Cora render vector SVGs into high-resolution raster PNGs?',
    answer: 'Cora parses the SVG markup DOM, computes bounding viewBox ratios, and paints mathematical bezier paths directly onto an offscreen canvas at your chosen scale factor before exporting a crisp 32-bit PNG with preserved alpha channels.',
  },
  {
    question: 'Can I export at 300 DPI for high-quality commercial printing?',
    answer: 'Yes. Selecting the 8x multiplier scales your vector graphics by 800% with sub-pixel antialiasing, ensuring logos, badges, and illustrations remain razor-sharp on business cards, posters, and merchandise.',
  },
  {
    question: 'Does the rasterizer support embedded inline styles and gradients?',
    answer: 'Yes. Standard inline CSS styles, linear and radial SVG gradients, clip-paths, masks, and stroke styles are rendered faithfully by the browser’s native vector engine.',
  },
  {
    question: 'Is my proprietary SVG code transmitted to an external cloud server?',
    answer: 'No. All parsing and rasterization occur entirely in your local browser memory. Neither your raw XML code nor the rendered PNG pixels are sent over the network.',
  },
  {
    question: 'What is the difference between 1x, 2x, 4x, and 8x scales?',
    answer: '1x matches standard 72 DPI screen sizing, 2x is optimized for Apple Retina and mobile displays, 4x provides 4K ultra HD clarity, and 8x yields professional 300 DPI print fidelity.',
  },
];

interface ScaleOption {
  scale: number;
  label: string;
  badge: string;
  desc: string;
}

const SCALE_OPTIONS: ScaleOption[] = [
  { scale: 1, label: '1x', badge: 'Standard', desc: 'Web & Email (72 DPI)' },
  { scale: 2, label: '2x', badge: 'Retina', desc: 'HiDPI Screens & Mobile' },
  { scale: 4, label: '4x', badge: 'Ultra HD', desc: '4K Displays & Keynotes' },
  { scale: 8, label: '8x', badge: 'Print 300 DPI', desc: 'Physical Print & Merch' },
];

export default function SvgToPngPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [inputMode, setInputMode] = useState<'upload' | 'code'>('upload');
  const [svgContent, setSvgContent] = useState<string>(SAMPLE_SVG);
  const [fileName, setFileName] = useState<string>('vector-graphic.svg');
  const [selectedScale, setSelectedScale] = useState<number>(2);
  const [isRasterizing, setIsRasterizing] = useState<boolean>(false);
  const [previewPngUrl, setPreviewPngUrl] = useState<string | null>(null);
  const [previewPngBlob, setPreviewPngBlob] = useState<Blob | null>(null);
  const [outputDimensions, setOutputDimensions] = useState<{ width: number; height: number }>({ width: 800, height: 600 });
  const [isCopiedCode, setIsCopiedCode] = useState<boolean>(false);
  const [bgPreviewMode, setBgPreviewMode] = useState<'checkered' | 'white' | 'dark'>('checkered');

  // Extract base dimensions from SVG content
  const parseSvgMetrics = useCallback((svgText: string): { width: number; height: number } => {
    try {
      const parser = new DOMParser();
      const doc = parser.parseFromString(svgText, 'image/svg+xml');
      const svgEl = doc.querySelector('svg');
      if (!svgEl) return { width: 800, height: 600 };

      let w = parseFloat(svgEl.getAttribute('width') || '0');
      let h = parseFloat(svgEl.getAttribute('height') || '0');

      if (!w || !h) {
        const vb = svgEl.getAttribute('viewBox');
        if (vb) {
          const parts = vb.split(/\s+|,/).map(parseFloat);
          if (parts.length === 4) {
            w = parts[2];
            h = parts[3];
          }
        }
      }

      return {
        width: Math.round(w || 800),
        height: Math.round(h || 600),
      };
    } catch {
      return { width: 800, height: 600 };
    }
  }, []);

  const baseMetrics = parseSvgMetrics(svgContent);

  // Generate PNG preview when SVG or scale changes
  const runRasterize = useCallback(async () => {
    if (!svgContent.trim()) return;
    setIsRasterizing(true);

    try {
      const result = await rasterizeSvgToPng(svgContent, selectedScale);
      setPreviewPngUrl(result.dataUrl);
      setPreviewPngBlob(result.blob);
      setOutputDimensions({ width: result.width, height: result.height });
    } catch (err) {
      console.error(err);
      showToast('Could not rasterize SVG. Please verify valid XML markup.');
    } finally {
      setIsRasterizing(false);
    }
  }, [svgContent, selectedScale, showToast]);

  useEffect(() => {
    const timer = setTimeout(() => {
      runRasterize();
    }, 250);
    return () => clearTimeout(timer);
  }, [runRasterize]);

  const handleFileUpload = (file: File) => {
    if (!file.name.toLowerCase().endsWith('.svg') && file.type !== 'image/svg+xml') {
      showToast('Please select a valid .svg file.');
      return;
    }

    setFileName(file.name);
    const reader = new FileReader();
    reader.onload = (e) => {
      const content = e.target?.result as string;
      setSvgContent(content);
      showToast(`Loaded ${file.name}`);
    };
    reader.readAsText(file);
  };

  const handleDownload = () => {
    if (!previewPngBlob && !previewPngUrl) return;
    const base = fileName.replace(/\.svg$/i, '') || 'raster-export';
    const finalName = `${base}-${selectedScale}x.png`;
    triggerBrowserImageDownload(previewPngBlob || previewPngUrl!, finalName);
    showToast(`Downloaded ${finalName} (${outputDimensions.width}x${outputDimensions.height}px)`);
  };

  const copySvgCode = () => {
    navigator.clipboard.writeText(svgContent);
    setIsCopiedCode(true);
    showToast('SVG XML code copied to clipboard.');
    setTimeout(() => setIsCopiedCode(false), 2000);
  };

  return (
    <ToolPageShell
      toolId="svg-to-png"
      badgeTag="Vector Rasterizer"
      title="SVG to PNG Converter"
      subtitle="Rasterize vector SVG files or raw XML code to razor-sharp PNGs at 1x, 2x Retina, 4x Ultra HD, or 8x Print DPI in browser memory."
      faqItems={FAQS}
      relatedToolSlugs={['convert-image', 'heic-to-jpg', 'image-to-text', 'images-to-pdf']}
    >
      <div className="space-y-8">
        
        {/* ── Input Mode Tabs & Scale Configuration ── */}
        <div className="bg-white border border-zinc-200/90 rounded-2xl p-5 sm:p-6 shadow-xs space-y-6">
          
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-200/80 pb-4">
            
            {/* Input Toggle */}
            <div className="flex items-center gap-1 p-1 bg-zinc-100 rounded-xl border border-zinc-200/80 w-fit">
              <button
                type="button"
                onClick={() => setInputMode('upload')}
                className={`flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all ${
                  inputMode === 'upload'
                    ? 'bg-white text-zinc-950 shadow-xs border border-zinc-200/80'
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                <UploadCloud className="w-3.5 h-3.5" />
                <span>Upload SVG File</span>
              </button>

              <button
                type="button"
                onClick={() => setInputMode('code')}
                className={`flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all ${
                  inputMode === 'code'
                    ? 'bg-white text-zinc-950 shadow-xs border border-zinc-200/80'
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                <Code className="w-3.5 h-3.5" />
                <span>Paste SVG Code</span>
              </button>
            </div>

            {/* Scale Multipliers */}
            <div className="flex items-center gap-1.5 flex-wrap">
              <span className="text-xs font-semibold uppercase tracking-wider text-zinc-500 mr-1 flex items-center gap-1">
                <Sliders className="w-3.5 h-3.5 text-zinc-600" />
                Scale:
              </span>
              {SCALE_OPTIONS.map((opt) => {
                const active = selectedScale === opt.scale;
                return (
                  <button
                    key={opt.scale}
                    type="button"
                    onClick={() => setSelectedScale(opt.scale)}
                    className={`px-3 py-1.5 text-xs font-semibold rounded-lg transition-all border ${
                      active
                        ? 'bg-zinc-900 text-white border-zinc-900 shadow-xs'
                        : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                    }`}
                  >
                    <span>{opt.label}</span>
                    <span className={`ml-1 text-[10px] font-mono opacity-80 ${active ? 'text-zinc-300' : 'text-zinc-500'}`}>
                      {opt.badge}
                    </span>
                  </button>
                );
              })}
            </div>
          </div>

          {/* Upload Dropzone View */}
          {inputMode === 'upload' ? (
            <div
              onClick={() => fileInputRef.current?.click()}
              className="border-2 border-dashed border-zinc-300 hover:border-zinc-400 bg-zinc-50/50 hover:bg-zinc-50 rounded-xl p-8 text-center cursor-pointer transition-all"
            >
              <input
                ref={fileInputRef}
                type="file"
                accept=".svg,image/svg+xml"
                onChange={(e) => {
                  if (e.target.files && e.target.files[0]) {
                    handleFileUpload(e.target.files[0]);
                  }
                  e.target.value = '';
                }}
                className="hidden"
              />
              <div className="w-10 h-10 mx-auto mb-3 rounded-xl bg-white border border-zinc-200 flex items-center justify-center text-zinc-800 shadow-2xs">
                <UploadCloud className="w-5 h-5 stroke-[1.8]" />
              </div>
              <p className="text-xs font-bold text-zinc-900 mb-0.5">
                Click to browse or drop an SVG file
              </p>
              <p className="text-[11px] text-zinc-500">
                Active file: <span className="font-mono text-zinc-700">{fileName}</span>
              </p>
            </div>
          ) : (
            /* XML Code Editor View */
            <div className="space-y-2">
              <div className="flex items-center justify-between text-xs text-zinc-600">
                <span className="font-mono text-[11px] font-bold text-zinc-700 flex items-center gap-1">
                  <FileCode className="w-3.5 h-3.5" />
                  Raw SVG XML Markup
                </span>
                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={copySvgCode}
                    className="inline-flex items-center gap-1 text-[11px] font-semibold text-zinc-600 hover:text-zinc-950 transition-colors"
                  >
                    {isCopiedCode ? <Check className="w-3 h-3 text-emerald-600" /> : <Copy className="w-3 h-3" />}
                    <span>{isCopiedCode ? 'Copied' : 'Copy'}</span>
                  </button>
                  <button
                    type="button"
                    onClick={() => setSvgContent(SAMPLE_SVG)}
                    className="text-[11px] font-semibold text-zinc-500 hover:text-zinc-900 transition-colors"
                  >
                    Reset Sample
                  </button>
                </div>
              </div>
              <textarea
                value={svgContent}
                onChange={(e) => setSvgContent(e.target.value)}
                rows={6}
                placeholder="<svg ...> ... </svg>"
                className="w-full p-3 font-mono text-xs text-zinc-800 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-zinc-900 selection:bg-zinc-900 selection:text-white"
                spellCheck={false}
              />
            </div>
          )}

        </div>

        {/* ── Live Vector Preview & Metrics Output ── */}
        <div className="bg-white border border-zinc-200/90 rounded-2xl overflow-hidden shadow-xs">
          
          {/* Header Bar */}
          <div className="p-4 sm:px-6 border-b border-zinc-200/80 flex flex-wrap items-center justify-between gap-4 bg-zinc-50/50">
            <div className="flex items-center gap-3">
              <Eye className="w-4 h-4 text-zinc-700" />
              <span className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
                Live Raster Preview
              </span>
              <div className="flex items-center gap-1.5 text-[11px] font-mono text-zinc-600 bg-white px-2.5 py-1 rounded-full border border-zinc-200">
                <span>Vector: {baseMetrics.width}×{baseMetrics.height}</span>
                <span className="text-zinc-400">→</span>
                <span className="font-bold text-zinc-900">
                  Export: {outputDimensions.width}×{outputDimensions.height}px ({selectedScale}x)
                </span>
              </div>
            </div>

            {/* Backdrop Style Options & Download */}
            <div className="flex items-center gap-2">
              <div className="inline-flex p-0.5 bg-zinc-100 rounded-lg border border-zinc-200">
                <button
                  type="button"
                  title="Checkered transparency"
                  onClick={() => setBgPreviewMode('checkered')}
                  className={`px-2 py-1 text-[11px] font-semibold rounded ${
                    bgPreviewMode === 'checkered' ? 'bg-white text-zinc-900 shadow-2xs' : 'text-zinc-500'
                  }`}
                >
                  Alpha
                </button>
                <button
                  type="button"
                  title="White background"
                  onClick={() => setBgPreviewMode('white')}
                  className={`px-2 py-1 text-[11px] font-semibold rounded ${
                    bgPreviewMode === 'white' ? 'bg-white text-zinc-900 shadow-2xs' : 'text-zinc-500'
                  }`}
                >
                  White
                </button>
                <button
                  type="button"
                  title="Dark background"
                  onClick={() => setBgPreviewMode('dark')}
                  className={`px-2 py-1 text-[11px] font-semibold rounded ${
                    bgPreviewMode === 'dark' ? 'bg-zinc-900 text-white shadow-2xs' : 'text-zinc-500'
                  }`}
                >
                  Dark
                </button>
              </div>

              <button
                type="button"
                onClick={handleDownload}
                disabled={!previewPngUrl || isRasterizing}
                className="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-zinc-900 hover:bg-zinc-800 disabled:opacity-50 rounded-xl shadow-xs transition-colors"
              >
                {isRasterizing ? (
                  <RefreshCw className="w-3.5 h-3.5 animate-spin" />
                ) : (
                  <Download className="w-3.5 h-3.5" />
                )}
                <span>Download PNG ({selectedScale}x)</span>
              </button>
            </div>
          </div>

          {/* Preview Canvas Area */}
          <div
            className={`min-h-[340px] max-h-[540px] flex items-center justify-center p-8 overflow-auto ${
              bgPreviewMode === 'white'
                ? 'bg-white'
                : bgPreviewMode === 'dark'
                ? 'bg-zinc-950'
                : 'bg-[#F4F4F5]'
            }`}
            style={
              bgPreviewMode === 'checkered'
                ? {
                    backgroundImage: `
                      linear-gradient(45deg, #E4E4E7 25%, transparent 25%), 
                      linear-gradient(-45deg, #E4E4E7 25%, transparent 25%), 
                      linear-gradient(45deg, transparent 75%, #E4E4E7 75%), 
                      linear-gradient(-45deg, transparent 75%, #E4E4E7 75%)
                    `,
                    backgroundSize: '16px 16px',
                    backgroundPosition: '0 0, 0 8px, 8px -8px, -8px 0px',
                  }
                : undefined
            }
          >
            {isRasterizing ? (
              <div className="flex flex-col items-center gap-2 text-zinc-500 text-xs">
                <RefreshCw className="w-5 h-5 animate-spin text-zinc-800" />
                <span>Rasterizing vector bezier curves...</span>
              </div>
            ) : previewPngUrl ? (
              <img
                src={previewPngUrl}
                alt="Rasterized PNG Preview"
                className="max-h-[460px] max-w-full object-contain rounded-lg shadow-sm border border-zinc-200/50"
              />
            ) : (
              <div className="text-center text-xs text-zinc-400">
                No SVG loaded. Please upload or paste valid SVG markup.
              </div>
            )}
          </div>

          {/* Footer Details */}
          <div className="p-4 sm:px-6 bg-zinc-50/70 border-t border-zinc-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-[11px] font-mono text-zinc-600">
            <div className="flex items-center gap-3">
              <span>Selected Multiplier: <strong className="text-zinc-900">{selectedScale}x</strong></span>
              <span>Rendered Format: <strong className="text-zinc-900">32-bit PNG (RGBA)</strong></span>
              {previewPngBlob && (
                <span>File Size: <strong className="text-zinc-900">{formatBytes(previewPngBlob.size)}</strong></span>
              )}
            </div>
            <div className="flex items-center gap-1.5 text-zinc-500">
              <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" />
              <span>100% In-Memory Rasterization</span>
            </div>
          </div>

        </div>

        {/* ── Feature Cards ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <Printer className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              8x Print 300 DPI Export
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Super-sample vector illustrations to maximum commercial resolution with clean mathematical edge smoothing.
            </p>
          </div>

          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <Monitor className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              Retina & HiDPI Alignment
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Generate pixel-perfect 2x assets for modern Apple MacBook Retina screens, iPhones, and Android displays.
            </p>
          </div>

          <div className="p-5 bg-white border border-zinc-200/90 rounded-xl space-y-2">
            <div className="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-800">
              <Code className="w-4 h-4 stroke-[2]" />
            </div>
            <h4 className="text-xs font-bold text-zinc-900 uppercase tracking-wider">
              Direct XML Paste Engine
            </h4>
            <p className="text-xs text-zinc-600 leading-relaxed">
              Paste SVG code directly from Figma, Illustrator, or icon sets without having to save intermediate files.
            </p>
          </div>
        </div>

      </div>
    </ToolPageShell>
  );
}
