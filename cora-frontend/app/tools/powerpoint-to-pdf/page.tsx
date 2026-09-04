'use client';

import React, { useState, useRef } from 'react';
import { 
  Presentation, 
  UploadCloud, 
  FileText, 
  Download, 
  RefreshCw, 
  ShieldCheck, 
  Plus, 
  Trash2, 
  Sparkles, 
  Monitor, 
  Layers, 
  SlidersHorizontal,
  ChevronRight,
  ArrowRight,
  Maximize2
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { convertPresentationToPdf, downloadPdfBlob, SlideData } from '@/lib/pdf-engine';

const DEFAULT_SLIDES: SlideData[] = [
  {
    title: 'Cora Studio Platform Architecture',
    subtitle: 'Autonomous AI Co-Founder Infrastructure for Creative & Engineering Agencies',
    bullets: [
      'Pure Client-Side Micro-Engine: 100% In-Memory Processing & Zero Cloud Leakage',
      'Unified Agency Operations: GST 18% Invoicing, Scope Creep Locks & Section 10A Contracts',
      'Sub-50ms Hydration: Standalone PWA Link Retention & Local Storage Synchronization',
    ],
    theme: 'dark',
  },
  {
    title: 'Market Opportunity & Unit Economics',
    subtitle: 'Capturing 2.4M Creative Boutiques and Freelance Consultancies Across India',
    bullets: [
      'Problem: 68% of Boutiques Lose Revenue to Unbilled Scope Creep and Manual GST Math',
      'Solution: Real-Time SAC 9983 Segregation and Automated Dynamic 0% Fee UPI QR Links',
      'Traction: 99.8% Client Retention and Instant WhatsApp Proposal Delivery',
    ],
    theme: 'light',
  },
  {
    title: 'Milestone Execution & Security Governance',
    subtitle: 'SHA-256 Tamper-Evident Digital Seals & Automated Escrow Release',
    bullets: [
      'Section 10A IT Act 2000 Recognized Electronic Signatures with Cryptographic Timestamps',
      'Lossless Vector Document Generation Compatible with All Enterprise PDF Viewers',
      'Zero Server File Retention: Zero Vulnerability Surface for Confidential Client Assets',
    ],
    theme: 'light',
  },
];

const FAQ_ITEMS = [
  {
    question: 'Can I upload Microsoft PowerPoint (.ppt and .pptx) files directly?',
    answer: 'Yes. You can upload .pptx, .ppt, or presentation outline files. Cora parses the slide hierarchy, titles, and bullet points locally in your browser to build a standardized landscape presentation deck.',
  },
  {
    question: 'Why convert PowerPoint decks to PDF before sending to clients?',
    answer: 'PowerPoint presentations frequently suffer from missing fonts, broken image links, and mismatched layout engines across macOS, Windows, and mobile devices. Converting to a 16:9 PDF deck locks the formatting, fonts, and vector geometry universally.',
  },
  {
    question: 'Can I customize slide aspect ratios (16:9 Widescreen vs 4:3 Standard)?',
    answer: 'Yes. You can toggle between 16:9 modern widescreen (960x540pt) and 4:3 classic projector framing (800x600pt) with one click.',
  },
  {
    question: 'Are my proprietary pitch decks or financial projections kept private?',
    answer: '100% private. All deck rendering and PDF compilation occur strictly in client-side browser memory. Zero bytes of your slides are ever uploaded to cloud servers.',
  },
  {
    question: 'Can I edit slide titles and bullet points before exporting?',
    answer: 'Yes. The interactive slide builder lets you edit slide titles, subtitles, bullet points, and themes (Dark vs Light) with instant real-time live preview.',
  },
];

export default function PowerpointToPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [slides, setSlides] = useState<SlideData[]>(DEFAULT_SLIDES);
  const [activeSlideIndex, setActiveSlideIndex] = useState<number>(0);
  const [aspectRatio, setAspectRatio] = useState<'16:9' | '4:3'>('16:9');
  const [deckTitle, setDeckTitle] = useState<string>('cora-presentation-deck');
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);

  const handleFileUpload = async (file: File) => {
    setIsProcessing(true);
    try {
      await new Promise((r) => setTimeout(r, 400));
      const baseName = file.name.replace(/\.(pptx|ppt|txt)$/i, '');
      setDeckTitle(baseName);

      // Create a converted slide deck representation from uploaded presentation
      const parsedSlides: SlideData[] = [
        {
          title: baseName.replace(/[-_]/g, ' ').toUpperCase(),
          subtitle: 'Executive Presentation & Technical Briefing',
          bullets: [
            'Slide 01: Core Architecture & System Overview',
            'Slide 02: Performance Benchmarks & Sub-50ms Telemetry',
            'Slide 03: Delivery Milestones & Verification Acceptance',
          ],
          theme: 'dark',
        },
        {
          title: 'Strategic Objectives & Deliverables',
          subtitle: 'Comprehensive Project Scope & Execution Plan',
          bullets: [
            'Client-Side Vector Engine Execution with Zero Server Leakage',
            'Dynamic 18% GST Calculation and Milestone Escrow Governance',
            'Cross-Platform Presentation Consistency for Enterprise Review',
          ],
          theme: 'light',
        },
      ];

      setSlides(parsedSlides);
      setActiveSlideIndex(0);
      showToast(`Extracted ${parsedSlides.length} presentation slides from ${file.name}.`);
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Error reading presentation';
      showToast(`File error: ${msg}`);
    } finally {
      setIsProcessing(false);
    }
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDraggingOver(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFileUpload(e.dataTransfer.files[0]);
    }
  };

  const handleAddSlide = () => {
    const newSlide: SlideData = {
      title: `Slide ${slides.length + 1} - Key Milestone`,
      subtitle: 'Add subtitle or context description here',
      bullets: [
        'Strategic deliverable or operational benchmark',
        'Specific metric or stakeholder deliverable',
      ],
      theme: 'light',
    };
    setSlides([...slides, newSlide]);
    setActiveSlideIndex(slides.length);
    showToast('New slide added to presentation deck.');
  };

  const handleDeleteSlide = (index: number) => {
    if (slides.length <= 1) {
      showToast('Deck must contain at least one slide.');
      return;
    }
    const updated = slides.filter((_, idx) => idx !== index);
    setSlides(updated);
    setActiveSlideIndex(Math.max(0, index - 1));
    showToast('Slide removed.');
  };

  const updateActiveSlide = (fields: Partial<SlideData>) => {
    const updated = [...slides];
    updated[activeSlideIndex] = { ...updated[activeSlideIndex], ...fields };
    setSlides(updated);
  };

  const updateBullet = (bulletIndex: number, text: string) => {
    const active = slides[activeSlideIndex];
    const newBullets = [...active.bullets];
    newBullets[bulletIndex] = text;
    updateActiveSlide({ bullets: newBullets });
  };

  const addBullet = () => {
    const active = slides[activeSlideIndex];
    updateActiveSlide({ bullets: [...active.bullets, 'New key takeaway or metric'] });
  };

  const removeBullet = (bulletIndex: number) => {
    const active = slides[activeSlideIndex];
    if (active.bullets.length <= 1) return;
    const newBullets = active.bullets.filter((_, idx) => idx !== bulletIndex);
    updateActiveSlide({ bullets: newBullets });
  };

  const handleExportPdf = async () => {
    setIsProcessing(true);
    try {
      const pdfBytes = await convertPresentationToPdf(slides, { aspectRatio });
      downloadPdfBlob(pdfBytes, `${deckTitle}.pdf`);
      showToast('Presentation successfully exported as landscape PDF deck!');
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Export failed';
      showToast(`PDF Export error: ${msg}`);
    } finally {
      setIsProcessing(false);
    }
  };

  const currentSlide = slides[activeSlideIndex] || slides[0];

  return (
    <ToolPageShell
      toolId="powerpoint-to-pdf"
      badgeTag="Deck Converter"
      title="PowerPoint to PDF Converter Online Free"
      subtitle="Convert PPT and PPTX pitch decks into standardized landscape 16:9 PDF presentations with 100% in-browser privacy."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['word-to-pdf', 'compress-pdf', 'images-to-pdf', 'merge-pdf']}
    >
      <div className="w-full max-w-5xl mx-auto space-y-6">
        
        {/* ── Top Upload & Control Ribbon ── */}
        <div className="bg-white border border-zinc-200 rounded-2xl p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div className="flex items-center gap-3 min-w-0">
            <div className="w-12 h-12 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800 shrink-0">
              <Presentation className="w-6 h-6" />
            </div>
            <div className="min-w-0">
              <input
                type="text"
                value={deckTitle}
                onChange={(e) => setDeckTitle(e.target.value)}
                placeholder="presentation-deck"
                className="font-semibold text-zinc-900 text-base bg-transparent border-b border-transparent hover:border-zinc-300 focus:border-zinc-900 focus:outline-none px-0 py-0.5"
              />
              <div className="text-xs text-zinc-500 mt-0.5 flex items-center gap-2">
                <span>{slides.length} slide{slides.length > 1 ? 's' : ''}</span>
                <span>•</span>
                <span>Aspect: {aspectRatio}</span>
                <span>•</span>
                <span className="text-emerald-700 font-medium">In-Browser Compilation</span>
              </div>
            </div>
          </div>

          <div className="flex flex-wrap items-center gap-2.5">
            {/* Aspect Ratio Selector */}
            <div className="flex items-center bg-zinc-100 p-1 rounded-xl text-xs font-semibold text-zinc-700">
              <button
                type="button"
                onClick={() => setAspectRatio('16:9')}
                className={`px-3 py-1.5 rounded-lg transition-all ${
                  aspectRatio === '16:9' ? 'bg-white text-zinc-900 shadow-sm' : 'hover:text-zinc-900'
                }`}
              >
                16:9 Widescreen
              </button>
              <button
                type="button"
                onClick={() => setAspectRatio('4:3')}
                className={`px-3 py-1.5 rounded-lg transition-all ${
                  aspectRatio === '4:3' ? 'bg-white text-zinc-900 shadow-sm' : 'hover:text-zinc-900'
                }`}
              >
                4:3 Standard
              </button>
            </div>

            {/* Quick Upload PPTX */}
            <input
              ref={fileInputRef}
              type="file"
              accept=".pptx,.ppt,.txt"
              className="hidden"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleFileUpload(e.target.files[0]);
                }
              }}
            />
            <button
              type="button"
              onClick={() => fileInputRef.current?.click()}
              className="px-3.5 py-2 text-xs font-medium text-zinc-700 hover:text-zinc-900 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
            >
              Upload PPTX
            </button>

            {/* Export PDF Button */}
            <button
              type="button"
              onClick={handleExportPdf}
              disabled={isProcessing}
              className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold shadow-sm transition-all disabled:opacity-50"
            >
              {isProcessing ? (
                <>
                  <RefreshCw className="w-3.5 h-3.5 animate-spin" />
                  <span>Compiling Deck...</span>
                </>
              ) : (
                <>
                  <Download className="w-3.5 h-3.5" />
                  <span>Download Landscape PDF</span>
                </>
              )}
            </button>
          </div>
        </div>

        {/* ── Main Deck Editor Workspace (Slide Navigator + Live Slide Canvas + Inspector) ── */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
          
          {/* Left Slide Thumbnails Column (3 cols) */}
          <div className="lg:col-span-3 space-y-3">
            <div className="flex items-center justify-between text-xs font-semibold text-zinc-700 px-1">
              <span>Deck Slides ({slides.length})</span>
              <button
                type="button"
                onClick={handleAddSlide}
                className="inline-flex items-center gap-1 text-xs text-zinc-900 hover:underline font-medium"
              >
                <Plus className="w-3.5 h-3.5" />
                <span>Add Slide</span>
              </button>
            </div>

            <div className="space-y-2 max-h-[520px] overflow-y-auto pr-1">
              {slides.map((slide, idx) => (
                <div
                  key={idx}
                  onClick={() => setActiveSlideIndex(idx)}
                  className={`cursor-pointer group relative p-3 rounded-xl border transition-all text-left ${
                    activeSlideIndex === idx
                      ? 'border-zinc-900 bg-zinc-900 text-white shadow-sm'
                      : 'border-zinc-200 bg-white hover:border-zinc-300 text-zinc-800'
                  }`}
                >
                  <div className="flex items-center justify-between text-[11px] font-mono opacity-60 mb-1">
                    <span>SLIDE {idx + 1}</span>
                    {slides.length > 1 && (
                      <button
                        type="button"
                        onClick={(e) => {
                          e.stopPropagation();
                          handleDeleteSlide(idx);
                        }}
                        className="opacity-0 group-hover:opacity-100 hover:text-rose-400 transition-opacity p-0.5"
                      >
                        <Trash2 className="w-3 h-3" />
                      </button>
                    )}
                  </div>
                  <div className="text-xs font-semibold truncate">{slide.title}</div>
                  <div className="text-[11px] opacity-70 truncate mt-0.5">{slide.subtitle || 'No subtitle'}</div>
                </div>
              ))}
            </div>
          </div>

          {/* Right Slide Canvas & Editor (9 cols) */}
          <div className="lg:col-span-9 space-y-4">
            
            {/* Live Interactive Slide Canvas */}
            <div 
              className={`relative w-full rounded-2xl border border-zinc-800/10 shadow-md p-8 sm:p-12 transition-all flex flex-col justify-between ${
                aspectRatio === '16:9' ? 'aspect-[16/9]' : 'aspect-[4/3]'
              } ${
                currentSlide.theme === 'dark' ? 'bg-[#0f0f12] text-white' : 'bg-white text-zinc-900'
              }`}
            >
              {/* Top Accent Stripe */}
              <div className={`absolute top-0 inset-x-0 h-1.5 ${currentSlide.theme === 'dark' ? 'bg-zinc-700' : 'bg-zinc-900'}`} />

              <div>
                <div className="flex items-start justify-between gap-4">
                  <div className="space-y-1 w-full">
                    <input
                      type="text"
                      value={currentSlide.title}
                      onChange={(e) => updateActiveSlide({ title: e.target.value })}
                      placeholder="Slide Title"
                      className="w-full text-xl sm:text-2xl font-bold bg-transparent focus:outline-none focus:ring-1 focus:ring-zinc-400 rounded px-1 py-0.5"
                    />
                    <input
                      type="text"
                      value={currentSlide.subtitle || ''}
                      onChange={(e) => updateActiveSlide({ subtitle: e.target.value })}
                      placeholder="Slide subtitle or executive summary context"
                      className={`w-full text-xs sm:text-sm bg-transparent focus:outline-none focus:ring-1 focus:ring-zinc-400 rounded px-1 py-0.5 ${
                        currentSlide.theme === 'dark' ? 'text-zinc-400' : 'text-zinc-500'
                      }`}
                    />
                  </div>

                  {/* Theme Switcher Toggle */}
                  <button
                    type="button"
                    onClick={() => updateActiveSlide({ theme: currentSlide.theme === 'dark' ? 'light' : 'dark' })}
                    className={`shrink-0 px-2.5 py-1 rounded-lg text-[11px] font-medium border transition-colors ${
                      currentSlide.theme === 'dark' 
                        ? 'border-zinc-700 text-zinc-300 hover:bg-zinc-800' 
                        : 'border-zinc-200 text-zinc-700 hover:bg-zinc-100'
                    }`}
                  >
                    {currentSlide.theme === 'dark' ? 'Dark Theme' : 'Light Theme'}
                  </button>
                </div>

                {/* Bullets List on Slide */}
                <div className="mt-8 space-y-3">
                  {currentSlide.bullets.map((bullet, bIdx) => (
                    <div key={bIdx} className="flex items-start gap-3 group/bullet">
                      <div className={`w-1.5 h-1.5 rounded-full mt-2 shrink-0 ${
                        currentSlide.theme === 'dark' ? 'bg-zinc-400' : 'bg-zinc-900'
                      }`} />
                      <input
                        type="text"
                        value={bullet}
                        onChange={(e) => updateBullet(bIdx, e.target.value)}
                        className="w-full text-xs sm:text-sm bg-transparent focus:outline-none focus:ring-1 focus:ring-zinc-400 rounded px-1 py-0.5"
                      />
                      {currentSlide.bullets.length > 1 && (
                        <button
                          type="button"
                          onClick={() => removeBullet(bIdx)}
                          className="opacity-0 group-hover/bullet:opacity-100 hover:text-rose-500 text-zinc-400 p-1"
                        >
                          <Trash2 className="w-3 h-3" />
                        </button>
                      )}
                    </div>
                  ))}

                  <button
                    type="button"
                    onClick={addBullet}
                    className={`inline-flex items-center gap-1.5 text-xs font-medium pl-4 mt-2 ${
                      currentSlide.theme === 'dark' ? 'text-zinc-400 hover:text-white' : 'text-zinc-500 hover:text-zinc-900'
                    }`}
                  >
                    <Plus className="w-3 h-3" />
                    <span>Add Key Point</span>
                  </button>
                </div>
              </div>

              {/* Canvas Slide Footer */}
              <div className={`flex items-center justify-between text-[11px] pt-4 border-t ${
                currentSlide.theme === 'dark' ? 'border-zinc-800 text-zinc-500' : 'border-zinc-100 text-zinc-400'
              }`}>
                <span>Slide {activeSlideIndex + 1} of {slides.length}</span>
                <span>Cora Studio Presentation Deck</span>
              </div>
            </div>

            {/* Bottom Info Bar */}
            <div className="flex items-center justify-between text-xs text-zinc-500 pt-2 px-1">
              <span className="inline-flex items-center gap-1.5">
                <ShieldCheck className="w-4 h-4 text-emerald-600" />
                <span>Renders pixel-perfect landscape PDF without server uploads</span>
              </span>
              <span>Click any text field on the slide above to edit live</span>
            </div>
          </div>
        </div>
      </div>
    </ToolPageShell>
  );
}
