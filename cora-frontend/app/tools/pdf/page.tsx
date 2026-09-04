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
  Files, 
  Scissors, 
  Trash2, 
  Layers, 
  FileText, 
  Minimize2, 
  Wrench, 
  ScanLine, 
  Image as ImageIcon, 
  FileType, 
  Presentation, 
  Table, 
  Code, 
  FileSpreadsheet, 
  FileCode2, 
  RotateCw, 
  Hash, 
  Stamp, 
  Crop, 
  Unlock, 
  ShieldAlert, 
  FileCheck, 
  EyeOff, 
  GitCompare, 
  Bot, 
  Languages, 
  ArrowRight, 
  Plus, 
  Minus,
  CheckCircle2,
  SlidersHorizontal,
  X
} from 'lucide-react';

interface PdfToolItem {
  id: string;
  slug: string;
  name: string;
  description: string;
  category: 'organize' | 'optimize' | 'convert_to' | 'convert_from' | 'edit' | 'security' | 'intelligence';
  badge?: string;
  icon: React.ComponentType<{ className?: string }>;
}

interface PdfCategoryGroup {
  id: 'organize' | 'optimize' | 'convert_to' | 'convert_from' | 'edit' | 'security' | 'intelligence';
  title: string;
  subtitle: string;
  icon: React.ComponentType<{ className?: string }>;
}

const CATEGORIES: PdfCategoryGroup[] = [
  {
    id: 'organize',
    title: 'ORGANIZE PDF',
    subtitle: 'Merge, split, arrange and extract pages',
    icon: Files,
  },
  {
    id: 'optimize',
    title: 'OPTIMIZE PDF',
    subtitle: 'Compress, repair and optical text scan',
    icon: Minimize2,
  },
  {
    id: 'convert_to',
    title: 'CONVERT TO PDF',
    subtitle: 'Turn images, docs, slides and sheets into PDF',
    icon: FileType,
  },
  {
    id: 'convert_from',
    title: 'CONVERT FROM PDF',
    subtitle: 'Extract PDF content into images, docs and code',
    icon: FileCode2,
  },
  {
    id: 'edit',
    title: 'EDIT PDF',
    subtitle: 'Rotate, paginate, watermark and crop',
    icon: Stamp,
  },
  {
    id: 'security',
    title: 'PDF SECURITY',
    subtitle: 'Digital eSign, encryption, redaction and audit',
    icon: ShieldCheck,
  },
  {
    id: 'intelligence',
    title: 'PDF INTELLIGENCE',
    subtitle: 'AI risk clause scanning and vernacular translation',
    icon: Sparkles,
  },
];

const ALL_PDF_TOOLS: PdfToolItem[] = [
  // ── 1. ORGANIZE PDF ──
  {
    id: 'merge-pdf',
    slug: 'merge-pdf',
    name: 'Merge PDF',
    description: 'Combine multiple PDF files into one unified document with drag-and-drop reordering.',
    category: 'organize',
    badge: 'Popular',
    icon: Files,
  },
  {
    id: 'split-pdf',
    slug: 'split-pdf',
    name: 'Split PDF',
    description: 'Separate document pages or split custom page ranges into individual PDF files.',
    category: 'organize',
    badge: 'Fast',
    icon: Scissors,
  },
  {
    id: 'remove-pages',
    slug: 'remove-pages',
    name: 'Remove Pages',
    description: 'Delete unwanted sheets, blank pages, or outdated appendixes with 1-click visual selection.',
    category: 'organize',
    icon: Trash2,
  },
  {
    id: 'extract-pages',
    slug: 'extract-pages',
    name: 'Extract Pages',
    description: 'Select and isolate specific pages or custom spans into a pristine standalone PDF.',
    category: 'organize',
    badge: 'Lossless',
    icon: Layers,
  },
  {
    id: 'organize-pdf',
    slug: 'extract-pages',
    name: 'Organize PDF',
    description: 'Sort, reorder, delete, and rearrange page sequences visually in browser memory.',
    category: 'organize',
    icon: SlidersHorizontal,
  },

  // ── 2. OPTIMIZE PDF ──
  {
    id: 'compress-pdf',
    slug: 'compress-pdf',
    name: 'Compress PDF',
    description: 'Reduce PDF file size up to 70% while keeping vector typography and images razor-sharp.',
    category: 'optimize',
    badge: 'Up to -70%',
    icon: Minimize2,
  },
  {
    id: 'repair-pdf',
    slug: 'repair-pdf',
    name: 'Repair PDF',
    description: 'Rebuild damaged cross-reference tables and recover corrupted or unreadable documents.',
    category: 'optimize',
    badge: 'Deep Recovery',
    icon: Wrench,
  },
  {
    id: 'ocr-pdf',
    slug: 'ocr-pdf',
    name: 'OCR PDF',
    description: 'Extract machine-readable text from scanned PDFs and receipts into searchable documents.',
    category: 'optimize',
    badge: '98%+ Accuracy',
    icon: ScanLine,
  },

  // ── 3. CONVERT TO PDF ──
  {
    id: 'images-to-pdf',
    slug: 'images-to-pdf',
    name: 'JPG to PDF',
    description: 'Convert JPG, PNG, WebP, and photo assets into standard clean A4 or Letter PDFs.',
    category: 'convert_to',
    badge: 'High-Res',
    icon: ImageIcon,
  },
  {
    id: 'word-to-pdf',
    slug: 'word-to-pdf',
    name: 'Word to PDF',
    description: 'Convert DOC, DOCX, and raw text drafts into standardized typeset vector PDF files.',
    category: 'convert_to',
    icon: FileType,
  },
  {
    id: 'powerpoint-to-pdf',
    slug: 'powerpoint-to-pdf',
    name: 'PowerPoint to PDF',
    description: 'Standardize 16:9 widescreen and 4:3 pitch decks into universally readable PDF presentations.',
    category: 'convert_to',
    badge: '16:9 Landscape',
    icon: Presentation,
  },
  {
    id: 'excel-to-pdf',
    slug: 'excel-to-pdf',
    name: 'Excel to PDF',
    description: 'Transform spreadsheet rows, balance sheets, and financial calculations into formatted PDFs.',
    category: 'convert_to',
    icon: Table,
  },
  {
    id: 'html-to-pdf',
    slug: 'html-to-pdf',
    name: 'HTML to PDF',
    description: 'Convert web pages, dashboards, and HTML snippets into high-fidelity PDF documents.',
    category: 'convert_to',
    icon: Code,
  },

  // ── 4. CONVERT FROM PDF ──
  {
    id: 'pdf-to-jpg',
    slug: 'pdf-to-jpg',
    name: 'PDF to JPG',
    description: 'Extract PDF pages into high-resolution JPG image files for rapid client previews.',
    category: 'convert_from',
    icon: ImageIcon,
  },
  {
    id: 'pdf-to-word',
    slug: 'pdf-to-word',
    name: 'PDF to Word',
    description: 'Convert PDF contracts and memos back into editable Word documents with intact paragraphs.',
    category: 'convert_from',
    icon: FileText,
  },
  {
    id: 'pdf-to-excel',
    slug: 'pdf-to-excel',
    name: 'PDF to Excel',
    description: 'Extract tabular numbers and billing columns from PDF invoices into CSV or Excel.',
    category: 'convert_from',
    icon: FileSpreadsheet,
  },
  {
    id: 'pdf-to-markdown',
    slug: 'pdf-to-markdown',
    name: 'PDF to Markdown',
    description: 'Convert PDF documentation, briefs, and whitepapers into clean GitHub-flavored Markdown.',
    category: 'convert_from',
    badge: 'Developer',
    icon: FileCode2,
  },

  // ── 5. EDIT PDF ──
  {
    id: 'rotate-pdf',
    slug: 'rotate-pdf',
    name: 'Rotate PDF',
    description: 'Rotate individual or all pages 90°, 180°, or 270° permanently with instant metadata save.',
    category: 'edit',
    icon: RotateCw,
  },
  {
    id: 'number-pdf',
    slug: 'number-pdf',
    name: 'Add Page Numbers',
    description: 'Stamp customized pagination footers (Bottom-Center, "Page X of Y") with offset control.',
    category: 'edit',
    icon: Hash,
  },
  {
    id: 'watermark-pdf',
    slug: 'watermark-pdf',
    name: 'Add Watermark',
    description: 'Protect draft proposals with custom diagonal text stamps and confidentiality markers.',
    category: 'edit',
    icon: Stamp,
  },
  {
    id: 'crop-pdf',
    slug: 'split-pdf',
    name: 'Crop PDF',
    description: 'Trim page margins and adjust printable boundary boxes for uniform print presentation.',
    category: 'edit',
    icon: Crop,
  },

  // ── 6. PDF SECURITY ──
  {
    id: 'unlock-pdf',
    slug: 'repair-pdf',
    name: 'Unlock PDF',
    description: 'Remove permission passwords and restriction flags from protected PDF documents.',
    category: 'security',
    icon: Unlock,
  },
  {
    id: 'protect-pdf',
    slug: 'esign-pdf',
    name: 'Protect PDF',
    description: 'Encrypt confidential agreements with robust password protection and access permissions.',
    category: 'security',
    icon: ShieldAlert,
  },
  {
    id: 'esign-pdf',
    slug: 'esign-pdf',
    name: 'Digital eSign PDF',
    description: 'Draw or type your signature and stamp Section 10A IT Act 2000 compliant digital seals.',
    category: 'security',
    badge: 'IT Act 2000',
    icon: FileCheck,
  },
  {
    id: 'redact-pdf',
    slug: 'watermark-pdf',
    name: 'Redact PDF',
    description: 'Permanently obscure sensitive numbers, Aadhaar figures, or proprietary trade secrets.',
    category: 'security',
    icon: EyeOff,
  },
  {
    id: 'compare-pdf',
    slug: 'translate-pdf',
    name: 'Compare PDF',
    description: 'Side-by-side visual diff scanner highlighting clause additions and contract alterations.',
    category: 'security',
    icon: GitCompare,
  },

  // ── 7. PDF INTELLIGENCE ──
  {
    id: 'ai-pdf-summarizer',
    slug: 'ai-pdf-summarizer',
    name: 'AI PDF Summarizer',
    description: 'Scan complex contracts, detect hidden liability clauses, and extract executive summaries.',
    category: 'intelligence',
    badge: 'AI Radar',
    icon: Bot,
  },
  {
    id: 'translate-pdf',
    slug: 'translate-pdf',
    name: 'Translate PDF',
    description: 'Translate documents into Hindi, Tamil, Telugu, and 20+ languages with side-by-side review.',
    category: 'intelligence',
    badge: 'Vernacular AI',
    icon: Languages,
  },
];

const PDF_SUITE_FAQS = [
  {
    question: 'Are all 25+ PDF tools really 100% free with no login or paywalls?',
    answer: 'Yes, 100% free forever. There are zero daily limits, zero page restrictions, and no account or credit card is ever required. Cora provides essential document utilities as open digital infrastructure.',
  },
  {
    question: 'How does Cora ensure 100% document privacy for sensitive client agreements?',
    answer: 'All operations execute locally in your device browser memory via client-side WebAssembly and JavaScript engines (pdf-lib). Zero bytes of your confidential contracts, financial figures, or identity details are ever uploaded to cloud servers.',
  },
  {
    question: 'What is the difference between Convert TO PDF and Convert FROM PDF?',
    answer: 'Convert TO PDF transforms external formats (Word DOCX, PowerPoint PPTX, Excel XLSX, JPG/PNG, and HTML) into standardized, vector-rendered PDF documents. Convert FROM PDF reverses this process, extracting text, spreadsheets, images, and Markdown out of existing PDF packets.',
  },
  {
    question: 'Are digital signatures created with Cora legally binding in India?',
    answer: 'Yes. Electronic signatures and timestamped audit markers generated by Cora conform to Section 10A of the Information Technology Act 2000 regarding the validity of contracts formed electronically.',
  },
  {
    question: 'How do these free standalone PDF tools connect to Cora Studio Workspace?',
    answer: 'These standalone micro-tools solve immediate ad-hoc tasks for free. When your creative agency or boutique consultancy scales and needs automated proposal generation, milestone escrow hold, dynamic 0% fee UPI invoicing, and client portals, you can upgrade to a full Cora Studio Workspace in 3 minutes.',
  },
];

export default function MasterPdfCategoryPage() {
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(0);

  // Filter tools by search query and category
  const filteredTools = useMemo(() => {
    const q = searchQuery.toLowerCase().trim();
    return ALL_PDF_TOOLS.filter((tool) => {
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
    const map = new Map<string, PdfToolItem[]>();
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
        
        {/* ── 1. Top Breadcrumbs ── */}
        <nav aria-label="Breadcrumb" className="flex items-center gap-2 text-xs text-zinc-500">
          <Link href="/tools" className="hover:text-zinc-900 transition-colors font-medium">
            Tools Directory
          </Link>
          <ChevronRight className="w-3.5 h-3.5 text-zinc-400" />
          <span className="text-zinc-900 font-semibold">Master PDF Suite</span>
        </nav>

        {/* ── 2. Hero Header Assembly (Unboxed Blended Continuous Canvas) ── */}
        <div className="text-center max-w-3xl mx-auto space-y-4">
          <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-900 text-white text-xs font-semibold tracking-wide uppercase shadow-sm">
            <Files className="w-3.5 h-3.5 text-zinc-300" />
            <span>Master PDF Utility Suite</span>
          </div>

          <h1 className="text-3xl sm:text-5xl font-extrabold tracking-tight text-zinc-950">
            25+ Client-Side PDF Tools. Zero Uploads. 100% Free.
          </h1>

          <p className="text-sm sm:text-base text-zinc-600 leading-relaxed">
            Merge, split, compress, convert, OCR, translate, eSign, and protect contracts in your browser. 
            Engineered for creative agencies, legal deeds, and high-velocity studios.
          </p>

          {/* ── Real-Time Search Filter Bar ── */}
          <div className="pt-2 max-w-xl mx-auto">
            <div className="relative flex items-center">
              <Search className="w-4 h-4 absolute left-4 text-zinc-400 pointer-events-none" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search across all 25+ PDF tools (e.g. compress, ocr, translate, word)..."
                className="w-full pl-11 pr-10 py-3.5 bg-white border border-zinc-200/90 hover:border-zinc-300 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 rounded-2xl text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400 shadow-sm transition-all focus:outline-none"
              />
              {searchQuery && (
                <button
                  type="button"
                  onClick={() => setSearchQuery('')}
                  aria-label="Clear search query"
                  className="absolute right-3.5 p-1 text-zinc-400 hover:text-zinc-700 rounded-full"
                >
                  <X className="w-4 h-4" />
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

        {/* ── 3. Quick Category Filter Pills ── */}
        <div className="flex flex-wrap items-center justify-center gap-2 pt-2">
          <button
            type="button"
            onClick={() => setSelectedCategory('all')}
            className={`px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all ${
              selectedCategory === 'all'
                ? 'bg-zinc-900 text-white shadow-sm'
                : 'bg-white text-zinc-700 hover:bg-zinc-100 border border-zinc-200'
            }`}
          >
            All Tools ({ALL_PDF_TOOLS.length})
          </button>

          {CATEGORIES.map((cat) => {
            const count = ALL_PDF_TOOLS.filter((t) => t.category === cat.id).length;
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

        {/* ── 4. Structured Category Tool Sections (Matching Screenshot Schema) ── */}
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
                      <CategoryIcon className="w-4 h-4" />
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
                          <span className="font-semibold text-zinc-900 group-hover:translate-x-0.5 transition-transform">
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

        {/* ── 5. Enterprise Trust & Privacy Matrix ── */}
        <div className="bg-white border border-zinc-200 rounded-2xl p-6 sm:p-8 shadow-sm">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
            <div className="space-y-2">
              <div className="w-9 h-9 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800">
                <ShieldCheck className="w-5 h-5 text-emerald-600" />
              </div>
              <h4 className="text-sm font-semibold text-zinc-900">Zero Server Uploads</h4>
              <p className="text-xs text-zinc-500 leading-relaxed">
                All 25+ PDF operations compile locally in your browser memory. Your confidential contracts, invoices, and identities never touch an external server.
              </p>
            </div>

            <div className="space-y-2">
              <div className="w-9 h-9 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800">
                <Zap className="w-5 h-5 text-zinc-800" />
              </div>
              <h4 className="text-sm font-semibold text-zinc-900">Instant Local Processing</h4>
              <p className="text-xs text-zinc-500 leading-relaxed">
                Fast execution powered by client-side WebAssembly. No queue times, no file upload transfer bottlenecks, and zero wait periods.
              </p>
            </div>

            <div className="space-y-2">
              <div className="w-9 h-9 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800">
                <Lock className="w-5 h-5 text-zinc-800" />
              </div>
              <h4 className="text-sm font-semibold text-zinc-900">100% Free Forever</h4>
              <p className="text-xs text-zinc-500 leading-relaxed">
                Zero paywalls, zero daily quotas, and no credit card required. Built as open digital infrastructure for the modern Indian creative economy.
              </p>
            </div>
          </div>
        </div>

        {/* ── 6. Rich FAQ Accordion ── */}
        <div className="max-w-3xl mx-auto space-y-6 pt-6">
          <div className="text-center space-y-1.5">
            <h3 className="text-2xl font-bold text-zinc-900 tracking-tight">
              Frequently Asked Questions
            </h3>
            <p className="text-xs text-zinc-500">
              Everything you need to know about Cora&apos;s client-side PDF architecture.
            </p>
          </div>

          <div className="space-y-3">
            {PDF_SUITE_FAQS.map((faq, idx) => {
              const isOpen = openFaqIndex === idx;
              return (
                <div
                  key={idx}
                  className="border border-zinc-200 rounded-xl bg-white overflow-hidden transition-colors"
                >
                  <button
                    type="button"
                    onClick={() => toggleFaq(idx)}
                    className="w-full flex items-center justify-between p-4 text-left font-semibold text-xs sm:text-sm text-zinc-900 hover:bg-zinc-50 transition-colors"
                  >
                    <span>{faq.question}</span>
                    <div className="w-5 h-5 rounded-full bg-zinc-100 flex items-center justify-center shrink-0 ml-3">
                      {isOpen ? (
                        <Minus className="w-3 h-3 text-zinc-700" />
                      ) : (
                        <Plus className="w-3 h-3 text-zinc-700" />
                      )}
                    </div>
                  </button>

                  {isOpen && (
                    <div className="px-4 pb-4 text-xs text-zinc-600 leading-relaxed border-t border-zinc-100 pt-3">
                      {faq.answer}
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>

        {/* ── 7. Studio Workspace Conversion CTA ── */}
        <div className="bg-zinc-900 text-white rounded-2xl p-8 sm:p-10 text-center max-w-4xl mx-auto space-y-4 shadow-lg">
          <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-800 text-zinc-300 text-xs font-medium">
            <Sparkles className="w-3.5 h-3.5 text-zinc-300" />
            <span>Cora for Creative & Engineering Agencies</span>
          </div>

          <h3 className="text-2xl sm:text-3xl font-bold tracking-tight">
            Ready to Automate Your Entire Studio Workflow?
          </h3>

          <p className="text-xs sm:text-sm text-zinc-400 max-w-xl mx-auto leading-relaxed">
            Move beyond manual PDF utilities. Generate automated client proposals, 18% GST invoices, dynamic 0% fee UPI links, and Section 10A deeds in a single unified workspace.
          </p>

          <div className="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
            <Link
              href="/pricing"
              className="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-zinc-900 text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-colors shadow-sm"
            >
              <span>Explore Workspace Plans</span>
              <ArrowRight className="w-4 h-4" />
            </Link>

            <Link
              href="/tools"
              className="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-white text-xs sm:text-sm font-medium transition-colors"
            >
              <span>Back to All Micro-Tools</span>
            </Link>
          </div>
        </div>

      </div>
    </div>
  );
}
