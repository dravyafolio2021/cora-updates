'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import { 
  FileSpreadsheet, 
  Search, 
  Sparkles, 
  ArrowLeft, 
  ArrowRight, 
  ShieldCheck, 
  Lock, 
  Zap, 
  Check, 
  CheckCircle2, 
  Table, 
  Code, 
  Calculator, 
  Files, 
  Scissors, 
  Trash2, 
  Filter, 
  FileText, 
  Layers, 
  ChevronRight, 
  Plus, 
  Minus,
  RefreshCw,
  FileCode2,
  Sliders,
  FileDown,
  Info
} from 'lucide-react';

interface SheetTool {
  id: string;
  slug: string;
  name: string;
  description: string;
  category: 'formulas_ai' | 'convert' | 'operations';
  badge: string;
  badgeType?: 'primary' | 'neutral' | 'accent';
  icon: React.ComponentType<{ className?: string; strokeWidth?: number }>;
  highlights: string[];
}

const CATEGORY_DEFINITIONS = [
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

const ALL_SHEET_TOOLS: SheetTool[] = [
  // ── 1. FORMULAS & AI ──
  {
    id: 'excel-formula-generator',
    slug: 'excel-formula-generator',
    name: 'Excel Formula Generator',
    description: 'Transform plain-English instructions into production-ready Excel and Google Sheets formulas with parameter breakdowns.',
    category: 'formulas_ai',
    badge: 'AI Engine',
    badgeType: 'primary',
    icon: Sparkles,
    highlights: ['Natural language to formula', 'Excel & Sheets syntax', 'Nested IFs & SUMIFS'],
  },
  {
    id: 'vlookup-builder',
    slug: 'vlookup-generator',
    name: 'VLOOKUP & XLOOKUP Builder',
    description: 'Design bulletproof lookup formulas visually. Generates both classic VLOOKUP and next-gen XLOOKUP with automatic IFERROR fallbacks.',
    category: 'formulas_ai',
    badge: 'Visual Builder',
    badgeType: 'neutral',
    icon: Calculator,
    highlights: ['Visual column selector', 'Exact match locking', 'IFERROR fallback handling'],
  },
  {
    id: 'formula-explainer',
    slug: 'formula-explainer',
    name: 'Formula Explainer',
    description: 'Paste complex, nested formulas to receive a step-by-step plain English breakdown of logic, ranges, and potential edge-case errors.',
    category: 'formulas_ai',
    badge: 'Syntax Audit',
    badgeType: 'neutral',
    icon: Code,
    highlights: ['Step-by-step logic breakdown', 'Error diagnostic tips', 'Syntax tree analysis'],
  },

  // ── 2. CONVERT SHEETS ──
  {
    id: 'csv-to-excel',
    slug: 'csv-to-excel',
    name: 'CSV to Excel Converter',
    description: 'Convert raw CSV and TSV files into native, formatted Microsoft Excel (.xlsx) workbooks with automatic numeric type detection.',
    category: 'convert',
    badge: 'Native .xlsx',
    badgeType: 'primary',
    icon: FileSpreadsheet,
    highlights: ['RFC 4180 compliant', 'Native XML workbook', '100% in-browser RAM'],
  },
  {
    id: 'excel-to-csv',
    slug: 'excel-to-csv',
    name: 'Excel to CSV Delimiter Engine',
    description: 'Export spreadsheet tables into standardized CSVs with customizable delimiters (comma, semicolon, tab, pipe) and strict quote escaping.',
    category: 'convert',
    badge: 'Multi-Delimiter',
    badgeType: 'neutral',
    icon: Table,
    highlights: ['Comma, tab, semicolon, pipe', 'RFC 4180 quote escaping', '1-click copy to clipboard'],
  },
  {
    id: 'excel-to-json',
    slug: 'excel-to-json',
    name: 'Excel to JSON Transformer',
    description: 'Transform spreadsheet rows into production JSON payloads: Array of Objects, 2D Arrays, or Keyed Dictionary maps in real time.',
    category: 'convert',
    badge: 'Dev Tool',
    badgeType: 'neutral',
    icon: FileCode2,
    highlights: ['Array of objects or 2D', 'Automatic numeric coercion', 'Formatted syntax output'],
  },
  {
    id: 'excel-to-pdf',
    slug: 'excel-to-pdf',
    name: 'Sheets to PDF Table Maker',
    description: 'Convert spreadsheet grids into publication-ready A4 PDF documents with custom margins, header branding, and clean zebra striping.',
    category: 'convert',
    badge: 'Vector PDF',
    badgeType: 'neutral',
    icon: FileText,
    highlights: ['A4 portrait & landscape', 'Auto-pagination headers', '300 DPI vector lines'],
  },
  {
    id: 'pdf-to-excel',
    slug: 'pdf-to-excel',
    name: 'PDF to Excel & CSV Parser',
    description: 'Extract tabular statements, bank ledgers, and invoice grids from PDF files directly into structured, editable Excel and CSV sheets.',
    category: 'convert',
    badge: 'Table OCR',
    badgeType: 'neutral',
    icon: FileDown,
    highlights: ['Table bounding box parser', 'Direct .xlsx download', 'Zero server OCR leaks'],
  },

  // ── 3. DATA CLEANING & OPERATIONS ──
  {
    id: 'remove-duplicates-csv',
    slug: 'remove-duplicates-csv',
    name: 'Remove Duplicates from CSV',
    description: 'Identify and purge redundant rows by specific key columns (Email, Phone, ID) or across complete records in pure browser RAM.',
    category: 'operations',
    badge: 'Key Matching',
    badgeType: 'primary',
    icon: Trash2,
    highlights: ['Column checklist filtering', 'Case sensitivity toggle', 'Duplicate row highlights'],
  },
  {
    id: 'clean-sheet-data',
    slug: 'clean-sheet-data',
    name: 'Clean & Sanitize Sheet Data',
    description: 'Normalize dirty spreadsheets: format Indian phone numbers (+91), convert dates to ISO YYYY-MM-DD, trim ghost spaces, and title-case names.',
    category: 'operations',
    badge: 'Data Hygiene',
    badgeType: 'neutral',
    icon: Sliders,
    highlights: ['+91 Phone normalization', 'ISO date standardization', 'Ghost row elimination'],
  },
  {
    id: 'merge-csv',
    slug: 'merge-csv',
    name: 'Merge CSV Files',
    description: 'Consolidate multiple CSV files into one unified master spreadsheet. Automatically aligns matching column headers and maps disparate fields.',
    category: 'operations',
    badge: 'Multi-File',
    badgeType: 'neutral',
    icon: Files,
    highlights: ['Batch multi-file drop', 'Automatic schema alignment', 'Origin file tracking column'],
  },
  {
    id: 'split-csv',
    slug: 'split-csv',
    name: 'Split CSV File',
    description: 'Divide oversized CSV spreadsheets into smaller files by row count or automatically partition by distinct values in any column (e.g. City or Vendor).',
    category: 'operations',
    badge: 'Batch Partition',
    badgeType: 'neutral',
    icon: Scissors,
    highlights: ['Split by max row limit', 'Partition by column value', '1-click batch download'],
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

export default function SheetsMasterHubPage() {
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(0);

  // Filter tools based on search and active category tab
  const filteredTools = useMemo(() => {
    return ALL_SHEET_TOOLS.filter((tool) => {
      const matchesCategory = selectedCategory === 'all' || tool.category === selectedCategory;
      const q = searchQuery.toLowerCase().trim();
      if (!q) return matchesCategory;

      const matchesSearch =
        tool.name.toLowerCase().includes(q) ||
        tool.description.toLowerCase().includes(q) ||
        tool.badge.toLowerCase().includes(q) ||
        tool.highlights.some(h => h.toLowerCase().includes(q));

      return matchesCategory && matchesSearch;
    });
  }, [searchQuery, selectedCategory]);

  const toggleFaq = (index: number) => {
    setOpenFaqIndex(openFaqIndex === index ? null : index);
  };

  return (
    <div className="relative w-full bg-[#FAFAF9] text-zinc-900 min-h-screen pt-[108px] sm:pt-[116px] pb-24 sm:pb-20 selection:bg-zinc-900 selection:text-white overflow-hidden">
      
      {/* ── Continuous Unboxed Blueprint Grid Background ── */}
      <div 
        aria-hidden="true"
        className="absolute inset-0 pointer-events-none opacity-[0.45]"
        style={{
          backgroundImage: `
            linear-gradient(to right, rgba(228, 228, 231, 0.7) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(228, 228, 231, 0.7) 1px, transparent 1px)
          `,
          backgroundSize: '28px 28px'
        }}
      />

      <div className="relative z-10 w-full max-w-6xl mx-auto px-4 sm:px-6">

        {/* ── Centered Breadcrumb Navigation ── */}
        <div className="flex justify-center mb-6">
          <Link
            href="/tools"
            className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/85 backdrop-blur-md border border-zinc-200/80 text-xs font-medium text-zinc-600 hover:text-zinc-950 hover:border-zinc-300 transition-all shadow-2xs group"
          >
            <ArrowLeft className="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform" />
            <span>Tools Directory</span>
            <span className="text-zinc-300 font-mono">/</span>
            <span className="text-zinc-950 font-semibold">Master Sheets Suite</span>
          </Link>
        </div>

        {/* ── Hero Section ── */}
        <div className="text-center max-w-3xl mx-auto mb-10 sm:mb-12">
          {/* Badge */}
          <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-zinc-200 text-xs font-semibold text-zinc-800 shadow-2xs mb-4">
            <FileSpreadsheet className="w-3.5 h-3.5 text-zinc-800" strokeWidth={2} />
            <span>12 Free Client-Side Spreadsheet Utilities</span>
          </div>

          {/* Headline */}
          <h1 className="font-display text-3xl sm:text-5xl md:text-6xl font-semibold text-zinc-950 tracking-[-0.03em] leading-[1.15] mb-4">
            The Master Sheets Suite
          </h1>

          {/* Subtitle */}
          <p className="text-zinc-600 text-sm sm:text-base md:text-lg font-normal leading-relaxed max-w-2xl mx-auto">
            High-performance browser utilities for formulas, format conversion, and large-scale CSV operations. Zero cloud uploads, zero data retention, and 100% private in-browser RAM execution.
          </p>

          {/* Value Pillars */}
          <div className="flex flex-wrap items-center justify-center gap-4 sm:gap-6 mt-6 text-xs font-medium text-zinc-700">
            <div className="flex items-center gap-1.5 bg-white/80 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/80 shadow-2xs">
              <Check className="w-3.5 h-3.5 text-emerald-600 font-bold" />
              <span>100% In-Browser RAM</span>
            </div>
            <div className="flex items-center gap-1.5 bg-white/80 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/80 shadow-2xs">
              <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" />
              <span>Zero Server Transmission</span>
            </div>
            <div className="flex items-center gap-1.5 bg-white/80 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/80 shadow-2xs">
              <Zap className="w-3.5 h-3.5 text-amber-500" />
              <span>Instant Local Processing</span>
            </div>
          </div>
        </div>

        {/* ── Real-Time Search & Category Switcher Bar ── */}
        <div className="bg-white/90 backdrop-blur-md rounded-2xl border border-zinc-200/80 p-3 sm:p-4 shadow-xs mb-10 max-w-4xl mx-auto">
          <div className="flex flex-col sm:flex-row gap-3 items-center justify-between">
            
            {/* Search Input */}
            <div className="relative w-full sm:w-80">
              <Search className="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search formulas, converters, CSV tools..."
                className="w-full pl-10 pr-4 py-2 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 placeholder:text-zinc-400 focus:outline-hidden focus:ring-2 focus:ring-zinc-900/10 focus:border-zinc-900 transition-all"
              />
              {searchQuery && (
                <button
                  type="button"
                  onClick={() => setSearchQuery('')}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-zinc-400 hover:text-zinc-700"
                >
                  Clear
                </button>
              )}
            </div>

            {/* Category Filter Pills */}
            <div className="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
              <button
                type="button"
                onClick={() => setSelectedCategory('all')}
                className={`px-3 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all ${
                  selectedCategory === 'all'
                    ? 'bg-zinc-900 text-white shadow-2xs'
                    : 'bg-zinc-100/80 text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900'
                }`}
              >
                All Tools ({ALL_SHEET_TOOLS.length})
              </button>
              {CATEGORY_DEFINITIONS.map((cat) => {
                const count = ALL_SHEET_TOOLS.filter(t => t.category === cat.id).length;
                return (
                  <button
                    key={cat.id}
                    type="button"
                    onClick={() => setSelectedCategory(cat.id)}
                    className={`px-3 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all ${
                      selectedCategory === cat.id
                        ? 'bg-zinc-900 text-white shadow-2xs'
                        : 'bg-zinc-100/80 text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900'
                    }`}
                  >
                    {cat.title.split(' ')[0]} ({count})
                  </button>
                );
              })}
            </div>

          </div>
        </div>

        {/* ── Grouped Categories Display ── */}
        <div className="space-y-12">
          {CATEGORY_DEFINITIONS.map((group) => {
            const groupTools = filteredTools.filter(tool => tool.category === group.id);
            if (groupTools.length === 0) return null;

            const GroupIcon = group.icon;

            return (
              <section key={group.id} className="space-y-4">
                
                {/* Group Header */}
                <div className="flex flex-col sm:flex-row sm:items-baseline justify-between gap-1 pb-3 border-b border-zinc-200/80">
                  <div className="flex items-center gap-2">
                    <span className="p-1.5 rounded-lg bg-zinc-100 text-zinc-800">
                      <GroupIcon className="w-4 h-4" strokeWidth={2} />
                    </span>
                    <h2 className="text-sm sm:text-base font-bold tracking-tight text-zinc-950 uppercase">
                      {group.title}
                    </h2>
                    <span className="text-xs font-mono text-zinc-400">
                      ({groupTools.length} {groupTools.length === 1 ? 'tool' : 'tools'})
                    </span>
                  </div>
                  <p className="text-xs text-zinc-500">
                    {group.subtitle}
                  </p>
                </div>

                {/* Group Tools Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {groupTools.map((tool) => {
                    const ToolIcon = tool.icon;
                    return (
                      <Link
                        key={tool.id}
                        href={`/tools/${tool.slug}`}
                        className="group relative bg-white rounded-2xl border border-zinc-200/80 p-5 hover:border-zinc-400 hover:shadow-md transition-all flex flex-col justify-between"
                      >
                        <div>
                          {/* Card Top: Icon & Badge */}
                          <div className="flex items-center justify-between gap-2 mb-3">
                            <span className="p-2.5 rounded-xl bg-zinc-100 text-zinc-900 group-hover:bg-zinc-900 group-hover:text-white transition-colors">
                              <ToolIcon className="w-4 h-4" strokeWidth={1.8} />
                            </span>
                            <span className="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-zinc-100 text-zinc-700 border border-zinc-200/60">
                              {tool.badge}
                            </span>
                          </div>

                          {/* Title & Description */}
                          <h3 className="text-sm font-semibold text-zinc-950 group-hover:text-zinc-900 mb-1.5 flex items-center gap-1">
                            <span>{tool.name}</span>
                            <ChevronRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-1 group-hover:text-zinc-900 transition-all opacity-0 group-hover:opacity-100" />
                          </h3>
                          <p className="text-xs text-zinc-500 leading-relaxed line-clamp-2">
                            {tool.description}
                          </p>
                        </div>

                        {/* Highlights List */}
                        <div className="mt-4 pt-3 border-t border-zinc-100 space-y-1">
                          {tool.highlights.map((h, hIdx) => (
                            <div key={hIdx} className="flex items-center gap-1.5 text-[11px] text-zinc-600">
                              <Check className="w-3 h-3 text-emerald-600 shrink-0" strokeWidth={2.2} />
                              <span>{h}</span>
                            </div>
                          ))}
                        </div>
                      </Link>
                    );
                  })}
                </div>

              </section>
            );
          })}

          {filteredTools.length === 0 && (
            <div className="bg-white rounded-2xl border border-zinc-200 p-12 text-center max-w-md mx-auto">
              <div className="w-10 h-10 rounded-full bg-zinc-100 flex items-center justify-center mx-auto mb-3 text-zinc-500">
                <Search className="w-5 h-5" />
              </div>
              <h3 className="text-sm font-semibold text-zinc-900 mb-1">No tools match your search</h3>
              <p className="text-xs text-zinc-500 mb-4">
                We couldn&apos;t find any spreadsheet tool matching &ldquo;{searchQuery}&rdquo;. Try a different keyword or reset filters.
              </p>
              <button
                type="button"
                onClick={() => {
                  setSearchQuery('');
                  setSelectedCategory('all');
                }}
                className="px-4 py-2 bg-zinc-900 text-white rounded-xl text-xs font-medium hover:bg-zinc-800 transition-colors"
              >
                Reset All Filters
              </button>
            </div>
          )}
        </div>

        {/* ── Trust & Architecture Banner ── */}
        <div className="mt-16 bg-white rounded-2xl border border-zinc-200/80 p-6 sm:p-8 shadow-xs">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="space-y-2">
              <div className="flex items-center gap-2">
                <span className="p-2 rounded-xl bg-zinc-100 text-zinc-900">
                  <ShieldCheck className="w-4 h-4 text-emerald-600" strokeWidth={2} />
                </span>
                <h4 className="text-sm font-semibold text-zinc-950">100% In-Browser Memory</h4>
              </div>
              <p className="text-xs text-zinc-500 leading-relaxed">
                Raw CSV cells and financial records remain isolated within your local browser JavaScript runtime. No server uploads, no logs, and no external AI data harvesting.
              </p>
            </div>

            <div className="space-y-2">
              <div className="flex items-center gap-2">
                <span className="p-2 rounded-xl bg-zinc-100 text-zinc-900">
                  <Zap className="w-4 h-4 text-amber-500" strokeWidth={2} />
                </span>
                <h4 className="text-sm font-semibold text-zinc-950">Zero Daily Limits</h4>
              </div>
              <p className="text-xs text-zinc-500 leading-relaxed">
                No credit card required, zero file size gates, and no login walls. Process multi-gigabyte files, combine 50+ CSVs, and generate unlimited formulas for free.
              </p>
            </div>

            <div className="space-y-2">
              <div className="flex items-center gap-2">
                <span className="p-2 rounded-xl bg-zinc-100 text-zinc-900">
                  <Layers className="w-4 h-4 text-indigo-600" strokeWidth={2} />
                </span>
                <h4 className="text-sm font-semibold text-zinc-950">Seamless Studio Automation</h4>
              </div>
              <p className="text-xs text-zinc-500 leading-relaxed">
                Need to automate GST math, WhatsApp client proposals, and UPI payment reconciliation on autopilot? Upgrade to Cora Studio Workspace in 3 minutes.
              </p>
            </div>
          </div>
        </div>

        {/* ── Master FAQ Accordion ── */}
        <div className="mt-16 max-w-3xl mx-auto">
          <div className="text-center mb-8">
            <h3 className="font-display text-2xl font-semibold text-zinc-950 mb-2">
              Frequently Asked Questions
            </h3>
            <p className="text-xs sm:text-sm text-zinc-500">
              Everything you need to know about browser-native spreadsheet processing and client data privacy
            </p>
          </div>

          <div className="space-y-3">
            {MASTER_SHEETS_FAQS.map((faq, idx) => {
              const isOpen = openFaqIndex === idx;
              return (
                <div
                  key={idx}
                  className="bg-white rounded-2xl border border-zinc-200/80 overflow-hidden transition-all shadow-xs"
                >
                  <button
                    type="button"
                    onClick={() => toggleFaq(idx)}
                    className="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-4 hover:bg-zinc-50/50 transition-colors"
                  >
                    <span className="text-xs sm:text-sm font-semibold text-zinc-900">
                      {faq.question}
                    </span>
                    <span className="p-1 rounded-lg bg-zinc-100 text-zinc-600 shrink-0">
                      {isOpen ? <Minus className="w-3.5 h-3.5" /> : <Plus className="w-3.5 h-3.5" />}
                    </span>
                  </button>
                  {isOpen && (
                    <div className="px-4 pb-4 sm:px-5 sm:pb-5 text-xs text-zinc-600 leading-relaxed border-t border-zinc-100 pt-3">
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
