'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import { 
  Search, 
  ChevronRight, 
  ShieldCheck, 
  Lock, 
  Zap, 
  Sparkles, 
  Image as ImageIcon, 
  Minimize2, 
  Maximize2, 
  Crop, 
  Grid3X3, 
  RefreshCw, 
  FileType, 
  FileCode2, 
  Stamp, 
  Layers, 
  Scissors, 
  Plus, 
  Minus, 
  Sliders, 
  X,
  Cpu,
  Shield,
  ArrowRight
} from 'lucide-react';

interface ImageToolItem {
  id: string;
  slug: string;
  name: string;
  description: string;
  category: 'optimization' | 'conversion' | 'creative';
  badge?: string;
  icon: React.ComponentType<{ className?: string; strokeWidth?: number }>;
}

interface ImageCategoryGroup {
  id: 'optimization' | 'conversion' | 'creative';
  title: string;
  subtitle: string;
  icon: React.ComponentType<{ className?: string; strokeWidth?: number }>;
}

const CATEGORIES: ImageCategoryGroup[] = [
  {
    id: 'optimization',
    title: 'OPTIMIZATION & SIZING',
    subtitle: 'High-speed canvas compression, smart scaling, aspect cropping, and carousel splitting',
    icon: Minimize2,
  },
  {
    id: 'conversion',
    title: 'FORMAT CONVERSION',
    subtitle: 'Bidirectional client-side transformation between JPG, PNG, WebP, and vector SVG',
    icon: RefreshCw,
  },
  {
    id: 'creative',
    title: 'AI & CREATIVE STUDIO',
    subtitle: 'Alpha edge background removal, intellectual property watermarks, and collage stitching',
    icon: Sparkles,
  },
];

const ALL_IMAGE_TOOLS: ImageToolItem[] = [
  // ── 1. OPTIMIZATION & SIZING ──
  {
    id: 'compress-image',
    slug: 'compress-image',
    name: 'Compress Image',
    description: 'Reduce photo file sizes by up to 85% with smart browser canvas compression. Lossless JPEG, PNG, and WebP.',
    category: 'optimization',
    badge: 'Popular',
    icon: Minimize2,
  },
  {
    id: 'resize-image',
    slug: 'resize-image',
    name: 'Resize Image',
    description: 'Scale photo dimensions by exact pixels or percentage with high-quality bicubic interpolation in local RAM.',
    category: 'optimization',
    badge: 'Precision',
    icon: Maximize2,
  },
  {
    id: 'crop-image',
    slug: 'crop-image',
    name: 'Crop Image',
    description: 'Crop photos to standard aspect ratios (1:1, 16:9, 4:5) or circular profile avatars with interactive visual framing.',
    category: 'optimization',
    badge: 'Visual Framing',
    icon: Crop,
  },
  {
    id: 'split-image',
    slug: 'split-image',
    name: 'Split Image',
    description: 'Slice wide panoramas into seamless Instagram carousel slides, 9-part profile grids, or custom tile matrices.',
    category: 'optimization',
    badge: 'Instagram Slices',
    icon: Grid3X3,
  },

  // ── 2. FORMAT CONVERSION ──
  {
    id: 'convert-image',
    slug: 'convert-image',
    name: 'Convert Image',
    description: 'Universal image format converter. Switch between JPG, PNG, and WebP with real-time compression quality preview.',
    category: 'conversion',
    badge: 'Universal',
    icon: RefreshCw,
  },
  {
    id: 'jpg-to-png',
    slug: 'jpg-to-png',
    name: 'JPG to PNG',
    description: 'Convert compressed JPEG images to high-fidelity, uncompressed PNG format with preserved color depth.',
    category: 'conversion',
    badge: 'Lossless',
    icon: FileType,
  },
  {
    id: 'png-to-jpg',
    slug: 'png-to-jpg',
    name: 'PNG to JPG',
    description: 'Transform bulky PNG files into compact JPEG photos with custom studio background fill colors.',
    category: 'conversion',
    badge: 'Compact',
    icon: FileType,
  },
  {
    id: 'webp-to-jpg',
    slug: 'webp-to-jpg',
    name: 'WebP to JPG',
    description: 'Convert next-generation Google WebP images to universal JPG format for legacy software and printer compatibility.',
    category: 'conversion',
    badge: 'Universal',
    icon: RefreshCw,
  },
  {
    id: 'svg-to-png',
    slug: 'svg-to-png',
    name: 'SVG to PNG',
    description: 'Rasterize scalable vector SVG files into crisp, high-resolution transparent PNG graphics at 1x, 2x, or 4x scale.',
    category: 'conversion',
    badge: 'Vector Rasterizer',
    icon: FileCode2,
  },

  // ── 3. AI & CREATIVE STUDIO ──
  {
    id: 'remove-background',
    slug: 'remove-background',
    name: 'Remove Background',
    description: 'Isolate subjects, strip studio backdrops, and export transparent alpha PNGs directly in your browser memory.',
    category: 'creative',
    badge: 'Chroma Alpha',
    icon: Sparkles,
  },
  {
    id: 'watermark-image',
    slug: 'watermark-image',
    name: 'Watermark Image',
    description: 'Stamp intellectual property notices, client proof banners, and logo overlays across high-res photos in browser memory.',
    category: 'creative',
    badge: 'Proofing IP',
    icon: Stamp,
  },
  {
    id: 'combine-images',
    slug: 'combine-images',
    name: 'Combine Images',
    description: 'Merge multiple photos horizontally, vertically, or in 2x2 collage grids with configurable gaps and studio canvas fills.',
    category: 'creative',
    badge: 'Multi-Stitch',
    icon: Layers,
  },
];

const FAQS = [
  {
    question: 'How do Cora image utilities process photos with 100% client-side privacy?',
    answer: 'Unlike traditional online converters that upload your confidential photos to third-party cloud servers, Cora executes all transformations directly inside your browser using the HTML5 Canvas API, Web Workers, and in-memory Blob buffers. Your photos never leave your device.',
  },
  {
    question: 'Are there any hidden file size limitations or processing fees?',
    answer: 'No. Because image processing uses your computer’s local processor and RAM rather than costly cloud servers, all 12 tools are 100% free with no daily caps, no watermark penalties, and no credit cards required.',
  },
  {
    question: 'Can I use these image tools on high-resolution photography (4K and 8K)?',
    answer: 'Yes. The engine processes high-resolution DSLR and mirrorless RAW-converted JPEGs and PNGs up to 50MB per file with sub-second canvas execution.',
  },
  {
    question: 'What is the advantage of converting WebP images to JPG?',
    answer: 'While WebP offers excellent web compression, many desktop graphic editors, older operating systems, email clients, and professional print labs require standard JPEG files. Our WebP converter ensures universal compatibility in one click.',
  },
  {
    question: 'How do the creative tools assist design agencies and creative studios?',
    answer: 'From watermarking client review proofs before contract milestone sign-offs to preparing seamless multi-slide Instagram carousels and creating transparent product catalog cutouts, our creative tools eliminate tedious Photoshop tasks in a lightweight browser tab.',
  },
];

export default function ImageToolsMasterHubPage() {
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(0);

  // Filter tools by search query and category
  const filteredTools = useMemo(() => {
    const q = searchQuery.toLowerCase().trim();
    return ALL_IMAGE_TOOLS.filter((tool) => {
      const matchesCategory = selectedCategory === 'all' || tool.category === selectedCategory;
      if (!matchesCategory) return false;
      if (!q) return true;

      return (
        tool.name.toLowerCase().includes(q) ||
        tool.description.toLowerCase().includes(q) ||
        (tool.badge && tool.badge.toLowerCase().includes(q))
      );
    });
  }, [searchQuery, selectedCategory]);

  // Group filtered tools by category
  const groupedTools = useMemo(() => {
    const map = new Map<string, ImageToolItem[]>();
    CATEGORIES.forEach((c) => map.set(c.id, []));

    filteredTools.forEach((tool) => {
      const list = map.get(tool.category) || [];
      list.push(tool);
      map.set(tool.category, list);
    });

    return map;
  }, [filteredTools]);

  const toggleFaq = (index: number) => {
    setOpenFaqIndex(openFaqIndex === index ? null : index);
  };

  return (
    <div className="relative w-full bg-[#FAFAF9] text-zinc-900 min-h-screen pt-[108px] sm:pt-[116px] pb-24 sm:pb-20 selection:bg-zinc-900 selection:text-white overflow-hidden">
      
      {/* ── Seamless Full-Width Background Pattern (Blueprint Grid) ── */}
      <div 
        aria-hidden="true"
        className="absolute top-0 inset-x-0 h-[520px] pointer-events-none opacity-[0.45]"
        style={{
          backgroundImage: `
            linear-gradient(to right, rgba(228, 228, 231, 0.7) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(228, 228, 231, 0.7) 1px, transparent 1px)
          `,
          backgroundSize: '32px 32px',
          maskImage: 'radial-gradient(ellipse 90% 70% at 50% 10%, black 40%, transparent 100%)',
          WebkitMaskImage: 'radial-gradient(ellipse 90% 70% at 50% 10%, black 40%, transparent 100%)',
        }}
      />

      <div className="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        {/* ── Hero Header Assembly (Zero Breadcrumbs, Exact UX match) ── */}
        <div className="text-center max-w-3xl mx-auto space-y-4 pt-1">
          {/* Centered Hero Badge */}
          <div>
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-900 text-white text-xs font-semibold tracking-wide uppercase shadow-sm">
              <ImageIcon className="w-3.5 h-3.5 text-zinc-300" strokeWidth={1.8} />
              <span>12 Free Client-Side Image Utilities</span>
            </div>
          </div>

          {/* Headline */}
          <h1 className="text-3xl sm:text-5xl font-extrabold tracking-tight text-zinc-950">
            Client-Side Image Tools. Zero Uploads. 100% Free.
          </h1>

          {/* Subtitle */}
          <p className="text-sm sm:text-base text-zinc-600 leading-relaxed">
            Compression, format conversion, background removal, and creative studio utilities in your browser.
          </p>

          {/* 3 Value Pills */}
          <div className="flex flex-wrap items-center justify-center gap-2.5 pt-1">
            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
              <Cpu className="w-3.5 h-3.5 text-zinc-700" strokeWidth={1.8} />
              100% In-Browser RAM
            </span>
            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
              <Shield className="w-3.5 h-3.5 text-zinc-700" strokeWidth={1.8} />
              Zero Server Transmission
            </span>
            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
              <Zap className="w-3.5 h-3.5 text-zinc-700" strokeWidth={1.8} />
              Instant Local Processing
            </span>
          </div>

          {/* ── Real-Time Search Filter Bar ── */}
          <div className="pt-3 max-w-xl mx-auto">
            <div className="relative flex items-center">
              <Search className="w-4 h-4 absolute left-4 text-zinc-400 pointer-events-none" strokeWidth={1.8} />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search across image tools (e.g. compress, watermark, background, convert)..."
                className="w-full pl-11 pr-10 py-3.5 bg-white border border-zinc-200/90 hover:border-zinc-300 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 rounded-2xl text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400 shadow-sm transition-all focus:outline-none"
              />
              {searchQuery && (
                <button
                  type="button"
                  onClick={() => setSearchQuery('')}
                  aria-label="Clear search query"
                  className="absolute right-3.5 p-1 text-zinc-400 hover:text-zinc-700 rounded-full"
                >
                  <X className="w-4 h-4" strokeWidth={1.8} />
                </button>
              )}
            </div>

            {searchQuery && (
              <div className="text-left text-xs text-zinc-500 mt-2 px-2">
                Found <span className="font-semibold text-zinc-900">{filteredTools.length}</span> tool{filteredTools.length !== 1 ? 's' : ''} matching &quot;{searchQuery}&quot;
              </div>
            )}
          </div>
        </div>

        {/* ── Quick Category Filter Pills ── */}
        <div className="flex flex-wrap items-center justify-center gap-2 pt-1">
          <button
            type="button"
            onClick={() => setSelectedCategory('all')}
            className={`px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all ${
              selectedCategory === 'all'
                ? 'bg-zinc-900 text-white shadow-sm'
                : 'bg-white text-zinc-700 hover:bg-zinc-100 border border-zinc-200'
            }`}
          >
            All Utilities ({ALL_IMAGE_TOOLS.length})
          </button>

          {CATEGORIES.map((cat) => {
            const count = ALL_IMAGE_TOOLS.filter((t) => t.category === cat.id).length;
            const isSelected = selectedCategory === cat.id;
            return (
              <button
                key={cat.id}
                type="button"
                onClick={() => setSelectedCategory(cat.id)}
                className={`inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all ${
                  isSelected
                    ? 'bg-zinc-900 text-white shadow-sm'
                    : 'bg-white text-zinc-700 hover:bg-zinc-100 border border-zinc-200'
                }`}
              >
                <span>{cat.title}</span>
                <span className={`text-[10px] px-1.5 py-0.2 rounded-full ${isSelected ? 'bg-zinc-800 text-zinc-200' : 'bg-zinc-100 text-zinc-600'}`}>
                  {count}
                </span>
              </button>
            );
          })}
        </div>

        {/* ── 3 Groups: Structured Category Tool Sections (5-Column Responsive Cards Grid) ── */}
        <div className="space-y-12">
          {CATEGORIES.map((cat) => {
            const toolsInCat = groupedTools.get(cat.id) || [];
            if (toolsInCat.length === 0) return null;

            const CategoryIcon = cat.icon;

            return (
              <section key={cat.id} className="space-y-4">
                {/* Category Header Strip */}
                <div className="flex flex-col sm:flex-row sm:items-baseline justify-between border-b border-zinc-200 pb-3 gap-1">
                  <div className="flex items-center gap-2.5">
                    <div className="w-7 h-7 rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-800">
                      <CategoryIcon className="w-4 h-4" strokeWidth={1.8} />
                    </div>
                    <div>
                      <h2 className="text-base sm:text-lg font-bold text-zinc-900 tracking-tight">
                        {cat.title}
                      </h2>
                    </div>
                  </div>
                  <p className="text-xs text-zinc-500 sm:text-right">
                    {cat.subtitle} ({toolsInCat.length} tool{toolsInCat.length > 1 ? 's' : ''})
                  </p>
                </div>

                {/* 5-Column Responsive Cards Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5">
                  {toolsInCat.map((tool) => {
                    const ToolIcon = tool.icon;
                    return (
                      <Link
                        key={tool.id}
                        href={`/tools/${tool.slug}`}
                        className="group relative flex flex-col justify-between p-4 bg-white border border-zinc-200 rounded-2xl hover:border-zinc-400 hover:shadow-md transition-all duration-200 text-left"
                      >
                        <div className="space-y-3">
                          <div className="flex items-center justify-between">
                            <div className="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-200 flex items-center justify-center text-zinc-700 group-hover:bg-zinc-900 group-hover:text-white group-hover:border-zinc-900 transition-colors duration-200">
                              <ToolIcon className="w-5 h-5 stroke-[1.8]" />
                            </div>

                            {tool.badge && (
                              <span className="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-700 border border-zinc-200">
                                {tool.badge}
                              </span>
                            )}
                          </div>

                          <div>
                            <h3 className="text-sm font-semibold text-zinc-900 group-hover:text-zinc-950 flex items-center gap-1">
                              <span>{tool.name}</span>
                              <ChevronRight className="w-3.5 h-3.5 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all text-zinc-400" />
                            </h3>
                            <p className="text-xs text-zinc-500 line-clamp-2 mt-1 leading-relaxed">
                              {tool.description}
                            </p>
                          </div>
                        </div>

                        <div className="pt-3 mt-3 border-t border-zinc-100 flex items-center justify-between text-[11px] text-zinc-400 group-hover:text-zinc-600">
                          <span>Client-Side</span>
                          <span className="font-semibold text-zinc-900 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                            Open Tool &rarr;
                          </span>
                        </div>
                      </Link>
                    );
                  })}
                </div>
              </section>
            );
          })}
        </div>

        {/* ── Enterprise Trust & Privacy Matrix ── */}
        <div className="bg-white border border-zinc-200 rounded-2xl p-6 sm:p-8 shadow-sm">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
            <div className="space-y-2">
              <div className="w-9 h-9 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800">
                <ShieldCheck className="w-5 h-5 text-emerald-600" strokeWidth={1.8} />
              </div>
              <h4 className="text-sm font-semibold text-zinc-900">Zero Server Uploads</h4>
              <p className="text-xs text-zinc-500 leading-relaxed">
                All image compression, rasterization, and background extraction execute locally in your browser memory. Your high-resolution creative deliverables never touch an external cloud server.
              </p>
            </div>

            <div className="space-y-2">
              <div className="w-9 h-9 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800">
                <Zap className="w-5 h-5 text-zinc-800" strokeWidth={1.8} />
              </div>
              <h4 className="text-sm font-semibold text-zinc-900">Instant Local Processing</h4>
              <p className="text-xs text-zinc-500 leading-relaxed">
                Hardware-accelerated HTML5 Canvas and Web Worker engines deliver sub-50ms render operations without network queues or bandwidth choking.
              </p>
            </div>

            <div className="space-y-2">
              <div className="w-9 h-9 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800">
                <Lock className="w-5 h-5 text-zinc-800" strokeWidth={1.8} />
              </div>
              <h4 className="text-sm font-semibold text-zinc-900">100% Free Forever</h4>
              <p className="text-xs text-zinc-500 leading-relaxed">
                Zero paywalls, zero daily limits, and no credit card required. Built as open creative utility infrastructure for modern studios and operators.
              </p>
            </div>
          </div>
        </div>

        {/* ── Monochromatic FAQ Accordion ── */}
        <div className="max-w-3xl mx-auto space-y-6 pt-6">
          <div className="text-center space-y-1.5">
            <h3 className="text-2xl font-bold text-zinc-900 tracking-tight">
              Frequently Asked Questions
            </h3>
            <p className="text-xs sm:text-sm text-zinc-500">
              Technical details on in-browser image rendering and local memory execution.
            </p>
          </div>

          <div className="space-y-3">
            {FAQS.map((faq, index) => {
              const isOpen = openFaqIndex === index;
              return (
                <div
                  key={index}
                  className="border border-zinc-200 rounded-xl bg-white overflow-hidden transition-all duration-200 shadow-xs"
                >
                  <button
                    type="button"
                    onClick={() => toggleFaq(index)}
                    className="w-full flex items-center justify-between p-4 sm:p-5 text-left transition-colors hover:bg-zinc-50/70"
                  >
                    <span className="text-xs sm:text-sm font-semibold text-zinc-900 pr-4">
                      {faq.question}
                    </span>
                    <span className="flex-shrink-0 w-6 h-6 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-600">
                      {isOpen ? (
                        <Minus className="w-3.5 h-3.5" strokeWidth={1.8} />
                      ) : (
                        <Plus className="w-3.5 h-3.5" strokeWidth={1.8} />
                      )}
                    </span>
                  </button>

                  {isOpen && (
                    <div className="px-4 sm:px-5 pb-4 sm:pb-5 text-xs sm:text-sm text-zinc-600 leading-relaxed border-t border-zinc-100 pt-3 bg-[#FAFAF9]">
                      {faq.answer}
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>

      </div>
    </div>
  );
}
