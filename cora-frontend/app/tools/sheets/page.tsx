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
  FileSpreadsheet, 
  Table, 
  Code, 
  Calculator, 
  Files, 
  Scissors, 
  Trash2, 
  FileText, 
  FileCode2, 
  Sliders, 
  FileDown, 
  Plus, 
  Minus, 
  ArrowRight, 
  X,
  RefreshCw
} from 'lucide-react';

interface SheetToolItem {
  id: string;
  slug: string;
  name: string;
  description: string;
  category: 'formulas_ai' | 'convert' | 'operations';
  badge?: string;
  icon: React.ComponentType<{ className?: string }>;
}

interface SheetCategoryGroup {
  id: 'formulas_ai' | 'convert' | 'operations';
  title: string;
  subtitle: string;
  icon: React.ComponentType<{ className?: string }>;
}

const CATEGORIES: SheetCategoryGroup[] = [
  {
    id: 'formulas_ai',
    title: 'FORMULAS & AI',
    subtitle: 'Natural language formula generation, syntax explanation, and visual lookup builders',
    icon: Sparkles,
  },
  {
    id: 'convert',
    title: 'CONVERT SHEETS',
    subtitle: 'Seamless bidirectional conversion between Excel .xlsx, CSV, JSON, and PDF tables',
    icon: RefreshCw,
  },
  {
    id: 'operations',
    title: 'DATA CLEANING & OPERATIONS',
    subtitle: 'High-speed deduplication, phone/date standardization, merging, and dataset partitioning',
    icon: Sliders,
  },
];

const ALL_SHEET_TOOLS: SheetToolItem[] = [
  // ── 1. FORMULAS & AI ──
  {
    id: 'excel-formula-generator',
    slug: 'excel-formula-generator',
    name: 'Formula Generator',
    description: 'Transform plain-English instructions into production-ready Excel and Google Sheets formulas with parameter breakdowns.',
    category: 'formulas_ai',
    badge: 'AI Engine',
    icon: Sparkles,
  },
  {
    id: 'vlookup-builder',
    slug: 'vlookup-generator',
    name: 'VLOOKUP Builder',
    description: 'Design bulletproof lookup formulas visually. Generates both classic VLOOKUP and next-gen XLOOKUP with automatic IFERROR fallbacks.',
    category: 'formulas_ai',
    badge: 'Visual Builder',
    icon: Calculator,
  },
  {
    id: 'clean-sheet-data',
    slug: 'clean-sheet-data',
    name: 'Clean Sheet Data',
    description: 'Normalize dirty spreadsheets: format Indian phone numbers (+91), convert dates to ISO YYYY-MM-DD, trim ghost spaces, and title-case names.',
    category: 'formulas_ai',
    badge: 'Data Hygiene',
    icon: Sliders,
  },

  // ── 2. CONVERT SHEETS ──
  {
    id: 'csv-to-excel',
    slug: 'csv-to-excel',
    name: 'CSV to Excel',
    description: 'Convert raw CSV and TSV files into native, formatted Microsoft Excel (.xlsx) workbooks with automatic numeric type detection.',
    category: 'convert',
    badge: 'Native .xlsx',
    icon: FileSpreadsheet,
  },
  {
    id: 'excel-to-csv',
    slug: 'excel-to-csv',
    name: 'Excel to CSV',
    description: 'Export spreadsheet tables into standardized CSVs with customizable delimiters (comma, semicolon, tab, pipe) and strict quote escaping.',
    category: 'convert',
    badge: 'Multi-Delimiter',
    icon: Table,
  },
  {
    id: 'excel-to-json',
    slug: 'excel-to-json',
    name: 'Excel to JSON',
    description: 'Transform spreadsheet rows into production JSON payloads: Array of Objects, 2D Arrays, or Keyed Dictionary maps in real time.',
    category: 'convert',
    badge: 'Dev Tool',
    icon: FileCode2,
  },
  {
    id: 'excel-to-pdf',
    slug: 'excel-to-pdf',
    name: 'Sheets to PDF',
    description: 'Convert spreadsheet grids into publication-ready A4 PDF documents with custom margins, header branding, and clean zebra striping.',
    category: 'convert',
    badge: 'Vector PDF',
    icon: FileText,
  },
  {
    id: 'pdf-to-excel',
    slug: 'pdf-to-excel',
    name: 'PDF to Excel',
    description: 'Extract tabular statements, bank ledgers, and invoice grids from PDF files directly into structured, editable Excel and CSV sheets.',
    category: 'convert',
    badge: 'Table OCR',
    icon: FileDown,
  },

  // ── 3. DATA CLEANING & OPERATIONS ──
  {
    id: 'remove-duplicates-csv',
    slug: 'remove-duplicates-csv',
    name: 'Remove Duplicates',
    description: 'Identify and purge redundant rows by specific key columns (Email, Phone, ID) or across complete records in pure browser RAM.',
    category: 'operations',
    badge: 'Key Matching',
    icon: Trash2,
  },
  {
    id: 'merge-csv',
    slug: 'merge-csv',
    name: 'Merge CSV Files',
    description: 'Consolidate multiple CSV files into one unified master spreadsheet. Automatically aligns matching column headers and maps disparate fields.',
    category: 'operations',
    badge: 'Multi-File',
    icon: Files,
  },
  {
    id: 'split-csv',
    slug: 'split-csv',
    name: 'Split CSV File',
    description: 'Divide oversized CSV spreadsheets into smaller files by row count or automatically partition by distinct values in any column (e.g. City or Vendor).',
    category: 'operations',
    badge: 'Batch Partition',
    icon: Scissors,
  },
];

const MASTER_SHEETS_FAQS = [
  {
    question: 'Are my financial spreadsheets, customer rosters, or CSV files uploaded to the cloud?',
    answer: 'No. Every single utility in the Cora Sheets Suite operates 100% client-side inside your browser RAM via high-performance JavaScript engines. Zero bytes of sensitive numbers, rates, vendor names, or GST records are ever transmitted over the network.',
  },
  {
    question: 'How do Cora spreadsheet tools compare to traditional desktop software like Excel?',
    answer: 'Desktop spreadsheet software often hits performance bottlenecks with massive row limits, breaks character encodings during CSV exports, and requires manual complex macro scripting. Cora provides instantaneous web micro-tools built for specific high-friction operations—such as multi-column deduplication, multi-file merging, and clean JSON exports—in seconds without software installation.',
  },
  {
    question: 'Can I use these tools completely free with no usage caps or paywalls?',
    answer: 'Yes, 100% free with zero daily limits, zero login gates, and no watermark restrictions. You can process unlimited datasets, merge multiple branch files, and generate complex formulas without ever entering a credit card.',
  },
  {
    question: 'How does the AI Formula Generator create formulas without cloud data leaks?',
    answer: 'The natural language formula generator interprets intent and constructs mathematical syntax based on structural query patterns and standard Excel/Sheets function libraries. Your confidential spreadsheet numbers and raw cell records remain strictly on your local machine.',
  },
  {
    question: 'How does the Cora Sheets Suite integrate with Cora Workspace for Studios?',
    answer: 'These standalone micro-tools solve immediate ad-hoc spreadsheet tasks. When your studio or consultancy grows and you want autonomous commercial invoicing, 18% GST splits, client contract signing, and dynamic payment links automated on autopilot, you can launch a full Cora Studio Workspace in minutes.',
  },
];

export default function MasterSheetsCategoryPage() {
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(0);

  // Filter tools by search query and category
  const filteredTools = useMemo(() => {
    const q = searchQuery.toLowerCase().trim();
    return ALL_SHEET_TOOLS.filter((tool) => {
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
    const map = new Map<string, SheetToolItem[]>();
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
        
        {/* ── Hero Header Assembly (Unboxed Blended Continuous Canvas, Zero Breadcrumbs) ── */}
        <div className="text-center max-w-3xl mx-auto space-y-4 pt-1">
          <div>
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-900 text-white text-xs font-semibold tracking-wide uppercase shadow-sm">
              <FileSpreadsheet className="w-3.5 h-3.5 text-zinc-300" />
              <span>Master Sheets Utility Suite</span>
            </div>
          </div>

          <h1 className="text-3xl sm:text-5xl font-extrabold tracking-tight text-zinc-950">
            Client-Side Sheets Tools. Zero Uploads. 100% Free.
          </h1>

          <p className="text-sm sm:text-base text-zinc-600 leading-relaxed">
            Formulas, visual VLOOKUP, deduplication, CSV converters, data cleaners, and split engines in your browser. 
            Engineered for finance leads, agency operators, and high-velocity teams.
          </p>

          {/* ── Real-Time Search Filter Bar ── */}
          <div className="pt-2 max-w-xl mx-auto">
            <div className="relative flex items-center">
              <Search className="w-4 h-4 absolute left-4 text-zinc-400 pointer-events-none" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search across all spreadsheet tools (e.g. formula, vlookup, duplicates, csv)..."
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
            All Tools ({ALL_SHEET_TOOLS.length})
          </button>

          {CATEGORIES.map((cat) => {
            const count = ALL_SHEET_TOOLS.filter((t) => t.category === cat.id).length;
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

        {/* ── 4. Structured Category Tool Sections (Exact Same 5-Column Grid as PDF Suite) ── */}
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
                All spreadsheet operations compile locally in your browser RAM. Your confidential financial figures, client lead rosters, and records never touch an external server.
              </p>
            </div>

            <div className="space-y-2">
              <div className="w-9 h-9 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800">
                <Zap className="w-5 h-5 text-zinc-800" />
              </div>
              <h4 className="text-sm font-semibold text-zinc-900">Instant Local Processing</h4>
              <p className="text-xs text-zinc-500 leading-relaxed">
                Fast execution powered by client-side JavaScript memory engines. No upload queues, no network latency bottlenecks, and zero wait periods.
              </p>
            </div>

            <div className="space-y-2">
              <div className="w-9 h-9 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800">
                <Lock className="w-5 h-5 text-zinc-800" />
              </div>
              <h4 className="text-sm font-semibold text-zinc-900">100% Free Forever</h4>
              <p className="text-xs text-zinc-500 leading-relaxed">
                Zero paywalls, zero daily quotas, and no credit card required. Built as open digital utility infrastructure for modern operators and studios.
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
              Everything you need to know about Cora&apos;s client-side spreadsheet engine.
            </p>
          </div>

          <div className="space-y-3">
            {MASTER_SHEETS_FAQS.map((faq, idx) => {
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
            <span>Cora for Creative & Financial Operations</span>
          </div>

          <h3 className="text-2xl sm:text-3xl font-bold tracking-tight">
            Ready to Automate Your Financial Ledger & Client Proposals?
          </h3>

          <p className="text-xs sm:text-sm text-zinc-400 max-w-xl mx-auto leading-relaxed">
            Move beyond manual spreadsheet reconciliations. Generate automated client proposals, 18% GST invoices, dynamic 0% fee UPI links, and milestone tracking in a single workspace.
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
