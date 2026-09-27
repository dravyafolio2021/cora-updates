'use client';

import React, { useState, useRef, useEffect } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { usePathname } from 'next/navigation';
import {
  ChevronDown,
  ChevronLeft,
  ChevronRight,
  ArrowRight,
  ArrowUpRight,
  Sparkles,
  ShieldCheck,
  Receipt,
  Send,
  FileText,
  HardDrive,
  Cpu,
  Camera,
  Building2,
  Film,
  User,
  Layers,
  Menu,
  X,
  Zap,
  ExternalLink,
  MessageSquare,
  Bot,
  BrainCircuit,
  Lock,
  Kanban,
  LayoutTemplate,
  FormInput,
  Star,
  CheckSquare,
  Users2,
  Calendar,
  Code,
  BarChart2,
  Briefcase,
  Scale,
  Clapperboard,
  Calculator,
  Terminal,
  BookOpen,
  Heart,
  Scissors,
  Files,
  ImageIcon,
  FileCheck,
  Stamp,
  RotateCw,
  Minimize2,
  Hash,
  Trash2,
  FileType,
  Table,
  FileSpreadsheet,
  Crop,
  Sliders,
  Clock
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';
import {
  AiCofounderColorIcon,
  VoiceScopeColorIcon,
  ContentAiColorIcon,
  RagMemoryColorIcon,
  LeadCrmColorIcon,
  CanvasBuilderColorIcon,
  FormBuilderColorIcon,
  ReviewPortalColorIcon,
  EsignVaultColorIcon,
  CrewDispatchColorIcon,
  MasterCalendarColorIcon,
  TaskBoardColorIcon,
  GstInvoicingColorIcon,
  AssetGearColorIcon,
  MediaHubColorIcon,
  RbacSecurityColorIcon,
  MODULE_GLYPH_MAP
} from '@/components/features/ModuleAppGlyphs';

export function Navbar() {
  const pathname = usePathname();
  const [activeDropdown, setActiveDropdown] = useState<string | null>(null);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [activeMobileSubmenu, setActiveMobileSubmenu] = useState<string | null>(null);
  const [isScrolled, setIsScrolled] = useState(false);
  const [isHovered, setIsHovered] = useState(false);
  const navRef = useRef<HTMLElement>(null);
  const timeoutRef = useRef<NodeJS.Timeout | null>(null);

  // Dedicated independent header on Docs pages
  if (pathname?.startsWith('/docs')) {
    return null;
  }

  // Track scroll state for sticky header backdrop styling
  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 20);
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  // Bulletproof body scroll lock when mobile menu is open (handles iOS & Android)
  useEffect(() => {
    if (mobileMenuOpen) {
      const scrollY = window.scrollY;
      document.documentElement.style.overflow = 'hidden';
      document.body.style.overflow = 'hidden';
      document.body.style.position = 'fixed';
      document.body.style.top = `-${scrollY}px`;
      document.body.style.left = '0';
      document.body.style.right = '0';
      document.body.style.width = '100%';
    } else {
      const scrollY = document.body.style.top;
      document.documentElement.style.overflow = '';
      document.body.style.overflow = '';
      document.body.style.position = '';
      document.body.style.top = '';
      document.body.style.left = '';
      document.body.style.right = '';
      document.body.style.width = '';
      if (scrollY) {
        window.scrollTo(0, parseInt(scrollY || '0') * -1);
      }
      setActiveMobileSubmenu(null);
    }
    return () => {
      document.documentElement.style.overflow = '';
      document.body.style.overflow = '';
      document.body.style.position = '';
      document.body.style.top = '';
      document.body.style.left = '';
      document.body.style.right = '';
      document.body.style.width = '';
    };
  }, [mobileMenuOpen]);

  // Close dropdown on click outside
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (navRef.current && !navRef.current.contains(event.target as Node)) {
        setActiveDropdown(null);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const isToolsPage = pathname?.startsWith('/tools') || false;

  const handleMouseEnter = (menuKey: string) => {
    if (timeoutRef.current) clearTimeout(timeoutRef.current);
    setActiveDropdown(menuKey);
    setIsHovered(true);
  };

  const handleMouseLeave = () => {
    timeoutRef.current = setTimeout(() => {
      setActiveDropdown(null);
      setIsHovered(false);
    }, 220);
  };

  const hasSolidBg = isScrolled || isHovered || activeDropdown !== null || mobileMenuOpen;

  return (
    <>
      {/* ── Floating Island Header with Curved Bottom Edges ── */}
      <header
        ref={navRef}
        className="w-full fixed top-0 left-0 right-0 z-50 px-2 sm:px-4 md:px-6 transition-all duration-300"
        onMouseLeave={handleMouseLeave}
      >
        <div
          className={`w-full max-w-[1240px] mx-auto px-5 sm:px-8 py-3 transition-all duration-300 ${
            hasSolidBg
              ? 'bg-white/95 backdrop-blur-md rounded-b-[24px] sm:rounded-b-[28px] border-b border-x border-zinc-200/90 shadow-[0_12px_36px_rgba(0,0,0,0.07)]'
              : 'bg-transparent border-transparent shadow-none'
          }`}
        >
          <div className="flex items-center justify-between gap-4">

            {/* ── Brand Logo & Main Nav Items Group ── */}
            <div className="flex items-center gap-7 lg:gap-9">
              <Link
                href="/"
                className="text-zinc-950 font-display uppercase hover:opacity-80 transition-opacity shrink-0"
                style={{ fontWeight: 600, fontSize: '1.25rem', letterSpacing: '0' }}
              >
                <span>CORA</span>
              </Link>

              {/* ── Desktop Navigation ── */}
              {isToolsPage ? (
                /* Tools Custom Menu: PDF, Finance, Contracts, AI Copy, Code & Embeds, All Tools */
                <nav className="hidden lg:flex items-center gap-1 text-xs font-semibold text-zinc-800 font-sans">
                  {/* 1. PDF Tools Dropdown */}
                  <div
                    className="relative py-1"
                    onMouseEnter={() => handleMouseEnter('tools-pdf')}
                  >
                    <button
                      type="button"
                      onClick={() => setActiveDropdown(activeDropdown === 'tools-pdf' ? null : 'tools-pdf')}
                      className={`px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition-all duration-200 ease-out group ${
                        activeDropdown === 'tools-pdf'
                          ? 'text-zinc-950 bg-zinc-100 font-bold shadow-2xs'
                          : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/70'
                      }`}
                    >
                      <span>PDF</span>
                      <ChevronDown className={`w-3.5 h-3.5 stroke-[2.2] text-zinc-500 group-hover:text-zinc-950 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] ${activeDropdown === 'tools-pdf' ? 'rotate-180 text-zinc-950 scale-105' : ''}`} />
                    </button>

                    {activeDropdown === 'tools-pdf' && (
                      <div
                        className="absolute top-full left-0 mt-3 w-[660px] rounded-3xl bg-white border border-zinc-200/90 shadow-[0px_24px_60px_rgba(0,0,0,0.12)] p-4 z-50 animate-in fade-in slide-in-from-top-2 duration-150"
                        onMouseEnter={() => handleMouseEnter('tools-pdf')}
                        onMouseLeave={handleMouseLeave}
                      >
                        <div className="grid grid-cols-2 gap-3">
                          {/* Column 1: ORGANIZE & OPTIMIZE */}
                          <div className="space-y-1">
                            <div className="px-2.5 py-1 text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider flex items-center justify-between">
                              <span>Organize &amp; Optimize</span>
                              <span className="text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200/60 font-mono text-[9px]">100% Private</span>
                            </div>

                            <Link
                              href="/tools/compress-pdf"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Minimize2 className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black flex items-center gap-1.5">
                                  Compress PDF
                                  <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200/60">Popular</span>
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Reduce file size up to 70% in browser</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/merge-pdf"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 border border-rose-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Files className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black flex items-center gap-1.5">
                                  Merge PDF Files
                                  <span className="text-[9px] font-mono font-bold text-rose-700 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200/60">Fast</span>
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Combine documents with visual reordering</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/split-pdf"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Scissors className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  Split &amp; Extract PDF
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Extract custom page ranges &amp; sheets</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/remove-pages"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-red-50 text-red-600 border border-red-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Trash2 className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  Remove Pages
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Delete unwanted sheets with 1 click</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/watermark-pdf"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 border border-violet-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Stamp className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  Add Watermark to PDF
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Stamp text or confidential marks</p>
                              </div>
                            </Link>
                          </div>

                          {/* Column 2: CONVERT, SECURITY & AI */}
                          <div className="space-y-1">
                            <div className="px-2.5 py-1 text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider flex items-center justify-between">
                              <span>Convert &amp; Security</span>
                              <span className="text-zinc-600 bg-zinc-100 px-1.5 py-0.2 rounded border border-zinc-200 font-mono text-[9px]">Zero Upload</span>
                            </div>

                            <Link
                              href="/tools/images-to-pdf"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 border border-teal-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <ImageIcon className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  Images to PDF
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Convert JPG, PNG &amp; WebP to PDF</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/word-to-pdf"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <FileType className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black flex items-center gap-1.5">
                                  Word to PDF
                                  <span className="text-[9px] font-mono font-bold text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200/60">New</span>
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Publish text &amp; memos into A4 PDF</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/number-pdf"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 border border-purple-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Hash className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  Add Page Numbers
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Custom pagination headers &amp; footers</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/esign-pdf"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <FileCheck className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black flex items-center gap-1.5">
                                  Digital eSign PDF
                                  <span className="text-[9px] font-mono font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.2 rounded border border-indigo-200/60">Sec 10A</span>
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Draw or type legally valid signatures</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/ai-pdf-summarizer"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Bot className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black flex items-center gap-1.5">
                                  AI PDF Summarizer
                                  <span className="text-[9px] font-mono font-bold text-amber-700 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200/60">AI Radar</span>
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Scan contract risks &amp; clauses</p>
                              </div>
                            </Link>
                          </div>
                        </div>

                        {/* Footer Action to Dedicated Category Page */}
                        <div className="mt-3 pt-3 border-t border-zinc-100 flex items-center justify-between px-2">
                          <span className="text-[11px] font-medium text-zinc-500">
                            Looking for OCR, Repair, Protect or Excel?
                          </span>
                          <Link
                            href="/tools/pdf"
                            onClick={() => setActiveDropdown(null)}
                            className="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-950 hover:text-black transition-colors group/link"
                          >
                            <span>Explore all 28 PDF Tools</span>
                            <ArrowRight className="w-3.5 h-3.5 group-hover/link:translate-x-0.5 transition-transform" />
                          </Link>
                        </div>
                      </div>
                    )}
                  </div>

                  {/* 2. Sheets Dropdown */}
                  <div
                    className="relative py-1"
                    onMouseEnter={() => handleMouseEnter('tools-sheets')}
                  >
                    <button
                      type="button"
                      onClick={() => setActiveDropdown(activeDropdown === 'tools-sheets' ? null : 'tools-sheets')}
                      className={`px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition-all duration-200 ease-out group ${
                        activeDropdown === 'tools-sheets'
                          ? 'text-zinc-950 bg-zinc-100 font-bold shadow-2xs'
                          : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/70'
                      }`}
                    >
                      <span>Sheets</span>
                      <ChevronDown className={`w-3.5 h-3.5 stroke-[2.2] text-zinc-500 group-hover:text-zinc-950 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] ${activeDropdown === 'tools-sheets' ? 'rotate-180 text-zinc-950 scale-105' : ''}`} />
                    </button>

                    {activeDropdown === 'tools-sheets' && (
                      <div
                        className="absolute top-full left-0 mt-3 w-[660px] rounded-3xl bg-white border border-zinc-200/90 shadow-[0px_24px_60px_rgba(0,0,0,0.12)] p-4 z-50 animate-in fade-in slide-in-from-top-2 duration-150"
                        onMouseEnter={() => handleMouseEnter('tools-sheets')}
                        onMouseLeave={handleMouseLeave}
                      >
                        <div className="grid grid-cols-2 gap-3">
                          {/* Column 1: FORMULAS & INTELLIGENCE */}
                          <div className="space-y-1">
                            <div className="px-2.5 py-1 text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider flex items-center justify-between">
                              <span>Formulas &amp; AI</span>
                              <span className="text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200/60 font-mono text-[9px]">Zero Errors</span>
                            </div>

                            <Link
                              href="/tools/excel-formula-generator"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Sparkles className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black flex items-center gap-1.5">
                                  Formula Generator
                                  <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200/60">AI</span>
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Plain English to Excel &amp; Sheets formula</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/vlookup-generator"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Table className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  VLOOKUP &amp; XLOOKUP
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Interactive visual lookup formula builder</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/clean-sheet-data"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 border border-teal-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <FileCheck className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  Clean Sheet Data
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Normalize phone numbers, dates &amp; names</p>
                              </div>
                            </Link>
                          </div>

                          {/* Column 2: CONVERT & CLEANING */}
                          <div className="space-y-1">
                            <div className="px-2.5 py-1 text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider flex items-center justify-between">
                              <span>Convert &amp; Operations</span>
                              <span className="text-zinc-600 bg-zinc-100 px-1.5 py-0.2 rounded border border-zinc-200 font-mono text-[9px]">100% In-RAM</span>
                            </div>

                            <Link
                              href="/tools/csv-to-excel"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <FileSpreadsheet className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  CSV to Excel (.xlsx)
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Convert delimited files to native workbooks</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/remove-duplicates-csv"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 border border-rose-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Trash2 className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  Remove Duplicates
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Deduplicate rows by key columns</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/excel-to-json"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 border border-purple-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Code className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  Excel / CSV to JSON
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Arrays, objects &amp; keyed dictionary maps</p>
                              </div>
                            </Link>
                          </div>
                        </div>

                        {/* Footer Action to Dedicated Category Page */}
                        <div className="mt-3 pt-3 border-t border-zinc-100 flex items-center justify-between px-2">
                          <span className="text-[11px] font-medium text-zinc-500">
                            Need to merge, split, or convert to CSV?
                          </span>
                          <Link
                            href="/tools/sheets"
                            onClick={() => setActiveDropdown(null)}
                            className="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-950 hover:text-black transition-colors group/link"
                          >
                            <span>Explore all Sheets Tools</span>
                            <ArrowRight className="w-3.5 h-3.5 group-hover/link:translate-x-0.5 transition-transform" />
                          </Link>
                        </div>
                      </div>
                    )}
                  </div>

                  {/* 3. Images Dropdown */}
                  <div
                    className="relative py-1"
                    onMouseEnter={() => handleMouseEnter('tools-images')}
                  >
                    <button
                      type="button"
                      onClick={() => setActiveDropdown(activeDropdown === 'tools-images' ? null : 'tools-images')}
                      className={`px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition-all duration-200 ease-out group ${
                        activeDropdown === 'tools-images'
                          ? 'text-zinc-950 bg-zinc-100 font-bold shadow-2xs'
                          : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/70'
                      }`}
                    >
                      <span>Images</span>
                      <ChevronDown className={`w-3.5 h-3.5 stroke-[2.2] text-zinc-500 group-hover:text-zinc-950 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] ${activeDropdown === 'tools-images' ? 'rotate-180 text-zinc-950 scale-105' : ''}`} />
                    </button>

                    {activeDropdown === 'tools-images' && (
                      <div
                        className="absolute top-full left-0 mt-3 w-[660px] rounded-3xl bg-white border border-zinc-200/90 shadow-[0px_24px_60px_rgba(0,0,0,0.12)] p-4 z-50 animate-in fade-in slide-in-from-top-2 duration-150"
                        onMouseEnter={() => handleMouseEnter('tools-images')}
                        onMouseLeave={handleMouseLeave}
                      >
                        <div className="grid grid-cols-2 gap-3">
                          {/* Column 1: OPTIMIZATION & FORMAT */}
                          <div className="space-y-1">
                            <div className="px-2.5 py-1 text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider flex items-center justify-between">
                              <span>Optimization &amp; Format</span>
                              <span className="text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200/60 font-mono text-[9px]">100% In-RAM</span>
                            </div>

                            <Link
                              href="/tools/compress-image"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Minimize2 className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black flex items-center gap-1.5">
                                  Compress Image
                                  <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200/60">Popular</span>
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Shrink JPG, PNG, WebP up to 80%</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/resize-image"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Crop className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  Resize Image
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Exact dimensions &amp; social presets</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/heic-to-jpg"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <ImageIcon className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black flex items-center gap-1.5">
                                  HEIC to JPG
                                  <span className="text-[9px] font-mono font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.2 rounded border border-indigo-200/60">iPhone</span>
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Decode Apple photos in browser memory</p>
                              </div>
                            </Link>
                          </div>

                          {/* Column 2: AI & CREATIVE */}
                          <div className="space-y-1">
                            <div className="px-2.5 py-1 text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider flex items-center justify-between">
                              <span>AI &amp; Creative Studio</span>
                              <span className="text-purple-700 bg-purple-50 px-1.5 py-0.2 rounded border border-purple-200/60 font-mono text-[9px]">Zero Upload</span>
                            </div>

                            <Link
                              href="/tools/remove-background"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 border border-rose-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Sparkles className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black flex items-center gap-1.5">
                                  Remove Background
                                  <span className="text-[9px] font-mono font-bold text-rose-700 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200/60">AI</span>
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Isolate subjects with transparent PNG</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/profile-photo-maker"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 border border-teal-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <Sliders className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  Profile Photo Maker
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Circular avatar framing &amp; studio colors</p>
                              </div>
                            </Link>

                            <Link
                              href="/tools/image-to-text"
                              onClick={() => setActiveDropdown(null)}
                              className="flex items-start gap-3 p-2.5 rounded-xl hover:bg-zinc-50 transition-all group"
                            >
                              <div className="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 border border-purple-200/60 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform mt-0.5">
                                <FileText className="w-4 h-4 stroke-[2]" />
                              </div>
                              <div>
                                <div className="text-[13px] font-bold text-zinc-900 group-hover:text-black">
                                  Image to Text (OCR)
                                </div>
                                <p className="text-[11px] text-zinc-500 font-normal mt-0.5">Scan &amp; extract copy from receipts or books</p>
                              </div>
                            </Link>
                          </div>
                        </div>

                        {/* Footer Action to Dedicated Category Page */}
                        <div className="mt-3 pt-3 border-t border-zinc-100 flex items-center justify-between px-2">
                          <span className="text-[11px] font-medium text-zinc-500">
                            Looking for SVG, watermark, split or combine?
                          </span>
                          <Link
                            href="/tools/images"
                            onClick={() => setActiveDropdown(null)}
                            className="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-950 hover:text-black transition-colors group/link"
                          >
                            <span>Explore all 12 Image Tools</span>
                            <ArrowRight className="w-3.5 h-3.5 group-hover/link:translate-x-0.5 transition-transform" />
                          </Link>
                        </div>
                      </div>
                    )}
                  </div>

                </nav>
              ) : (
                /* Default Site Navigation: Features, Industries, Resources, Pricing, Company */
                <nav className="hidden lg:flex items-center gap-1 text-xs font-semibold text-zinc-800 font-sans">

                  {/* 1. Features Dropdown */}
                  <div
                    className="relative py-1"
                    onMouseEnter={() => handleMouseEnter('features')}
                  >
                    <button
                      type="button"
                      onClick={() => setActiveDropdown(activeDropdown === 'features' ? null : 'features')}
                      className={`px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition-all duration-200 ease-out group ${
                        activeDropdown === 'features'
                          ? 'text-zinc-950 bg-zinc-100 font-bold shadow-2xs'
                          : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/70'
                      }`}
                    >
                      <span>Features</span>
                      <ChevronDown className={`w-3.5 h-3.5 stroke-[2.2] text-zinc-500 group-hover:text-zinc-950 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] ${activeDropdown === 'features' ? 'rotate-180 text-zinc-950 scale-105' : ''}`} />
                    </button>
                  </div>

                  {/* 2. Industries Dropdown */}
                  <div
                    className="relative py-1"
                    onMouseEnter={() => handleMouseEnter('industries')}
                  >
                    <button
                      type="button"
                      onClick={() => setActiveDropdown(activeDropdown === 'industries' ? null : 'industries')}
                      className={`px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition-all duration-200 ease-out group ${
                        activeDropdown === 'industries'
                          ? 'text-zinc-950 bg-zinc-100 font-bold shadow-2xs'
                          : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/70'
                      }`}
                    >
                      <span>Industries</span>
                      <ChevronDown className={`w-3.5 h-3.5 stroke-[2.2] text-zinc-500 group-hover:text-zinc-950 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] ${activeDropdown === 'industries' ? 'rotate-180 text-zinc-950 scale-105' : ''}`} />
                    </button>
                  </div>

                  {/* 3. Resources Dropdown */}
                  <div
                    className="relative py-1"
                    onMouseEnter={() => handleMouseEnter('resources')}
                  >
                    <button
                      type="button"
                      onClick={() => setActiveDropdown(activeDropdown === 'resources' ? null : 'resources')}
                      className={`px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition-all duration-200 ease-out group ${
                        activeDropdown === 'resources'
                          ? 'text-zinc-950 bg-zinc-100 font-bold shadow-2xs'
                          : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/70'
                      }`}
                    >
                      <span>Resources</span>
                      <ChevronDown className={`w-3.5 h-3.5 stroke-[2.2] text-zinc-500 group-hover:text-zinc-950 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] ${activeDropdown === 'resources' ? 'rotate-180 text-zinc-950 scale-105' : ''}`} />
                    </button>
                  </div>

                  {/* 4. Direct Pricing Link */}
                  <Link
                    href="/pricing"
                    className="px-3.5 py-1.5 rounded-full text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/70 transition-all duration-200 ease-out"
                  >
                    Pricing
                  </Link>

                  {/* 5. Company Dropdown */}
                  <div
                    className="relative py-1"
                    onMouseEnter={() => handleMouseEnter('company')}
                  >
                    <button
                      type="button"
                      onClick={() => setActiveDropdown(activeDropdown === 'company' ? null : 'company')}
                      className={`px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition-all duration-200 ease-out group ${
                        activeDropdown === 'company'
                          ? 'text-zinc-950 bg-zinc-100 font-bold shadow-2xs'
                          : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/70'
                      }`}
                    >
                      <span>Company</span>
                      <ChevronDown className={`w-3.5 h-3.5 stroke-[2.2] text-zinc-500 group-hover:text-zinc-950 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] ${activeDropdown === 'company' ? 'rotate-180 text-zinc-950 scale-105' : ''}`} />
                    </button>

                    {/* ── Modern Clean Company Dropdown Card ── */}
                    {activeDropdown === 'company' && (
                      <div
                        className="absolute top-full right-0 mt-3 w-56 rounded-2xl bg-white border border-zinc-200/90 shadow-[0px_20px_50px_rgba(0,0,0,0.1)] p-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150"
                        onMouseEnter={() => handleMouseEnter('company')}
                        onMouseLeave={handleMouseLeave}
                      >
                        <Link
                          href="/about"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3 p-2 rounded-xl text-xs font-semibold text-zinc-800 hover:text-black hover:bg-zinc-50 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                            <Sparkles className="w-4 h-4 stroke-[2]" />
                          </div>
                          <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black">About Cora</span>
                        </Link>

                        <Link
                          href="/brand"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3 p-2 rounded-xl text-xs font-semibold text-zinc-800 hover:text-black hover:bg-zinc-50 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 border border-amber-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                            <Layers className="w-4 h-4 stroke-[2]" />
                          </div>
                          <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black">Brand &amp; Assets</span>
                        </Link>

                        <Link
                          href="/security"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3 p-2 rounded-xl text-xs font-semibold text-zinc-800 hover:text-black hover:bg-zinc-50 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 border border-blue-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                            <ShieldCheck className="w-4 h-4 stroke-[2]" />
                          </div>
                          <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black">Security &amp; Trust</span>
                        </Link>

                        <Link
                          href="/contact"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3 p-2 rounded-xl text-xs font-semibold text-zinc-800 hover:text-black hover:bg-zinc-50 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 border border-purple-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                            <MessageSquare className="w-4 h-4 stroke-[2]" />
                          </div>
                          <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black">Contact &amp; Support</span>
                        </Link>

                        <div className="my-1.5 border-t border-zinc-100" />

                        <Link
                          href="/status"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50 transition-colors"
                        >
                          <span className="font-semibold text-xs text-zinc-800">System Status</span>
                          <span className="flex items-center gap-1.5 text-[11px] font-mono text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                            99.98%
                          </span>
                        </Link>
                      </div>
                    )}
                  </div>

                </nav>
              )}
            </div>

            {/* ── Right Actions: AI AGENT USP Trigger + Primary CTA + Minimal Mobile Menu ── */}
            <div className="flex items-center gap-2 sm:gap-3">

              {/* AI AGENT Direct Funnel Link (Core USP - Hidden on Mobile) */}
              <Link
                href="/ai-agent"
                className="hidden md:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold border bg-white/80 hover:bg-white text-zinc-900 border-zinc-200/90 shadow-2xs transition-all hover:-translate-y-0.5"
              >
                <Sparkles className="w-3.5 h-3.5 text-emerald-500" />
                <span>AI AGENT</span>
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
              </Link>

              {/* CTA Button: If on /tools*, show "Back to Site" button instead of "Get started for Free" */}
              {isToolsPage ? (
                <>
                  <Link
                    href="/"
                    className="hidden sm:inline-flex items-center justify-center gap-1.5 bg-zinc-950 text-white border border-zinc-800 px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold hover:bg-zinc-800 transition-all shadow-sm active:translate-y-0 hover:-translate-y-0.5 whitespace-nowrap group"
                  >
                    <span>Back to Site</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                  </Link>

                  <Link
                    href="/"
                    className="sm:hidden inline-flex items-center gap-1 text-xs font-bold text-zinc-950 px-2.5 py-1.5 rounded-lg hover:bg-zinc-100/80 transition-colors"
                  >
                    <span>Back to Site</span>
                    <ArrowRight className="w-3 h-3 text-zinc-600" />
                  </Link>
                </>
              ) : (
                <>
                  {/* Minimal Text Link on Mobile / Solid Button on Desktop */}
                  <a
                    href="https://app.heycora.in/workspace/login?source=navbar"
                    onClick={() => trackEvent('header_cta_clicked')}
                    className="hidden sm:inline-flex items-center justify-center gap-1.5 bg-zinc-950 text-white border border-zinc-800 px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold hover:bg-zinc-800 transition-all shadow-sm active:translate-y-0 hover:-translate-y-0.5 whitespace-nowrap group"
                  >
                    <span>Get started for Free</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                  </a>

                  {/* Ultra-Minimal Text Button on Small Mobile Screens (< 640px) */}
                  <a
                    href="https://app.heycora.in/workspace/login?source=navbar_mobile"
                    onClick={() => trackEvent('header_cta_clicked')}
                    className="sm:hidden inline-flex items-center gap-1 text-xs font-bold text-zinc-950 px-2.5 py-1.5 rounded-lg hover:bg-zinc-100/80 transition-colors"
                  >
                    <span>Get Started</span>
                    <ArrowRight className="w-3 h-3 text-zinc-600" />
                  </a>
                </>
              )}

              {/* Mobile Hamburger / Close Button (No background in normal state) */}
              <button
                type="button"
                onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                className="lg:hidden p-2 rounded-xl text-zinc-950 hover:bg-black/5 transition-colors focus:outline-none"
                aria-label="Toggle Menu"
              >
                {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
              </button>
            </div>

          </div>

          {/* ══════════════════════════════════════════════════════════════════
              MEGA MENU DROPDOWN PANEL (FOR FEATURES, INDUSTRIES, RESOURCES)
          ══════════════════════════════════════════════════════════════════ */}
          {(activeDropdown === 'features' || activeDropdown === 'industries' || activeDropdown === 'resources') && (
            <div
              className="hidden lg:block absolute left-0 right-0 top-[64px] z-50 px-2 sm:px-4 md:px-6 pt-3"
              onMouseEnter={() => handleMouseEnter(activeDropdown)}
              onMouseLeave={handleMouseLeave}
            >
              <div className={`w-full ${activeDropdown === 'resources' ? 'max-w-[1140px] p-6 sm:p-7 rounded-[28px]' : 'max-w-[1240px] p-8 sm:p-10 rounded-[28px]'} mx-auto bg-white border border-zinc-200/90 shadow-[0px_20px_50px_rgba(0,0,0,0.10)] transition-all duration-300 ease-out`}>

                {/* ── DROPDOWN: FEATURES (20 BUILT MODULES ACROSS 4 EQUAL PILLARS) ── */}
                {activeDropdown === 'features' && (
                  <div key="features-tab" className="space-y-6 animate-in fade-in zoom-in-[0.99] duration-200 ease-out fill-mode-forwards">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 xl:gap-8 items-start">

                      {/* 1. INTELLIGENCE & AI */}
                      <div className="space-y-1">
                        <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Intelligence &amp; AI
                          </span>
                          <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                            FLAGSHIP
                          </span>
                        </div>

                        <Link
                          href="/features/ai-cofounder"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <AiCofounderColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                AI Co-Founder
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Automate daily ops &amp; triage
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/voice-to-scope"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <VoiceScopeColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                Voice-to-Scope
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Turn voice notes into scopes
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/content-ai"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <ContentAiColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                Content AI &amp; GEO
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Generate viral video scripts
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/rag-mcp"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <RagMemoryColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                RAG Memory MCP
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Sync client context with IDE
                            </p>
                          </div>
                        </Link>
                      </div>

                      {/* 2. GROWTH & PIPELINE */}
                      <div className="space-y-1">
                        <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Growth &amp; Pipeline
                          </span>
                          <span className="text-[9px] font-mono font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200/60">
                            GROWTH
                          </span>
                        </div>

                        <Link
                          href="/features/lead-crm"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <LeadCrmColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                Kanban Lead CRM
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Close deals on WhatsApp
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/canvas-builder"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <CanvasBuilderColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                Funnel Builder
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Build high-converting pages
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/form-builder"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <FormBuilderColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                Visual Forms
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Capture briefs &amp; book calls
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/review-portal"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <ReviewPortalColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                5★ Review Portal
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Collect 5-star Google reviews
                            </p>
                          </div>
                        </Link>
                      </div>

                      {/* 3. OPERATIONS & LEGAL */}
                      <div className="space-y-1">
                        <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Operations &amp; Legal
                          </span>
                          <span className="text-[9px] font-mono font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200/60">
                            LEGAL TECH
                          </span>
                        </div>

                        <Link
                          href="/features/esign-vault"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <EsignVaultColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                SHA-256 E-Signs
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Sign legal contracts in 60s
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/crew-dispatch"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <CrewDispatchColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                Crew Dispatch
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Dispatch call sheets fast
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/master-calendar"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <MasterCalendarColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                Master Calendar
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Schedule shoots &amp; dates
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/task-board"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <TaskBoardColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                Task Board
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Track sprints &amp; proofing
                            </p>
                          </div>
                        </Link>
                      </div>

                      {/* 4. FINANCE & ASSETS */}
                      <div className="space-y-1">
                        <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Finance &amp; Assets
                          </span>
                          <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                            INDIA GST
                          </span>
                        </div>

                        <Link
                          href="/features/gst-invoicing"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <GstInvoicingColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                18% GST Invoicing
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Auto GST bills &amp; UPI QR
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/asset-gear"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <AssetGearColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                Gear &amp; Inventory
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Track gear &amp; prevent loss
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/media-hub"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <MediaHubColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                Media Hub &amp; RAW
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Deliver 4K proof galleries
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/rbac-system"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <RbacSecurityColorIcon className="w-9 h-9" />
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13.5px] font-bold text-zinc-900 group-hover:text-black transition-colors tracking-tight">
                                Multi-Tenant RBAC
                              </span>
                              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11.5px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Control team permissions
                            </p>
                          </div>
                        </Link>
                      </div>

                    </div>

                    {/* ── Sleek Monochromatic Bottom Bar ── */}
                    <div className="-mx-8 -mb-8 sm:-mx-10 sm:-mb-10 mt-6 px-8 sm:px-10 py-3.5 bg-zinc-50/90 rounded-b-[28px] border-t border-zinc-100 flex items-center justify-between">
                      <div className="flex items-center gap-2.5">
                        <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                        <span className="text-xs font-semibold text-zinc-900">20 Live Modules</span>
                        <span className="text-zinc-300">·</span>
                        <span className="text-xs text-zinc-500 font-medium">Automate client operations, contract vaults &amp; 18% GST cashflow</span>
                      </div>

                      <div className="flex items-center gap-3">
                        <Link
                          href="/docs"
                          onClick={() => setActiveDropdown(null)}
                          className="text-xs font-semibold text-zinc-600 hover:text-zinc-950 transition-colors"
                        >
                          API Specs &amp; Docs
                        </Link>
                        <Link
                          href="/features"
                          onClick={() => setActiveDropdown(null)}
                          className="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all shadow-2xs group"
                        >
                          <span>Explore All 20 Modules</span>
                          <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                        </Link>
                      </div>
                    </div>

                  </div>
                )}

                {/* ── DROPDOWN: INDUSTRIES (4-COLUMN CLEAN MINIMAL ARCHITECTURE) ── */}
                {activeDropdown === 'industries' && (
                  <div key="industries-tab" className="space-y-6 animate-in fade-in zoom-in-[0.99] duration-200 ease-out fill-mode-forwards">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 xl:gap-8 items-start">

                      {/* Column 1: Tech & Dev */}
                      <div className="space-y-1">
                        <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Tech &amp; Dev
                          </span>
                          <span className="text-[9px] font-mono font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200/60">
                            TECH &amp; DEV
                          </span>
                        </div>
                        <div className="space-y-1">
                          <Link
                            href="/use-cases/software-agencies"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 border border-blue-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Code className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Software Agencies
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/web-app-studios"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 border border-indigo-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <LayoutTemplate className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Web &amp; App Studios
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/it-tech-services"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <ShieldCheck className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              IT &amp; Tech Services
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/ai-automation"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 border border-amber-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Zap className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              AI &amp; Automation
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>
                        </div>
                      </div>

                      {/* Column 2: Legal & Finance */}
                      <div className="space-y-1">
                        <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Legal &amp; Finance
                          </span>
                          <span className="text-[9px] font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200/60">
                            LEGAL &amp; FINANCE
                          </span>
                        </div>
                        <div className="space-y-1">
                          <Link
                            href="/use-cases/lawyers-law-firms"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-slate-500/10 text-slate-700 border border-slate-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Scale className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Lawyers &amp; Law Firms
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/tax-ca-firms"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Receipt className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Tax &amp; CA Firms
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/financial-advisors"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 border border-indigo-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Briefcase className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Financial Advisors
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/audit-compliance"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 border border-purple-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Layers className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Audit &amp; Compliance
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>
                        </div>
                      </div>

                      {/* Column 3: Marketing & Creative */}
                      <div className="space-y-1">
                        <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Marketing &amp; Creative
                          </span>
                          <span className="text-[9px] font-mono font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200/60">
                            MARKETING &amp; DESIGN
                          </span>
                        </div>
                        <div className="space-y-1">
                          <Link
                            href="/use-cases/marketing-seo"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 border border-sky-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <BarChart2 className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Marketing &amp; SEO
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/design-uiux"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-violet-500/10 text-violet-600 border border-violet-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Sparkles className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Design &amp; UI/UX
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/photo-video-studios"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-600 border border-rose-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Camera className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Photo &amp; Video Studios
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/architecture-interiors"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-orange-500/10 text-orange-600 border border-orange-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Building2 className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Architecture &amp; Interiors
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>
                        </div>
                      </div>

                      {/* Column 4: Services & Lifestyle */}
                      <div className="space-y-1">
                        <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Services &amp; Lifestyle
                          </span>
                          <span className="text-[9px] font-mono font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200/60">
                            SERVICES &amp; ADVISORY
                          </span>
                        </div>
                        <div className="space-y-1">
                          <Link
                            href="/use-cases/consultants-advisors"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 border border-indigo-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Briefcase className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Consultants &amp; Advisors
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/doctors-clinics"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 border border-teal-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Heart className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Doctors &amp; Clinics
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/salons-wellness"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-pink-500/10 text-pink-600 border border-pink-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Scissors className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Salons, Spas &amp; Wellness
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>

                          <Link
                            href="/use-cases/real-estate-property"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/60 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                              <Building2 className="w-4 h-4 stroke-[2]" />
                            </div>
                            <span className="text-[13px] font-bold text-zinc-900 group-hover:text-black tracking-tight flex-1">
                              Real Estate &amp; Property
                            </span>
                            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                          </Link>
                        </div>
                      </div>

                    </div>

                    {/* Bottom Bar */}
                    <div className="-mx-8 -mb-8 sm:-mx-10 sm:-mb-10 mt-6 px-8 sm:px-10 py-3.5 bg-zinc-50/90 rounded-b-[28px] border-t border-zinc-100 flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <span className="w-2 h-2 rounded-full bg-emerald-500" />
                        <span className="text-xs text-zinc-600 font-medium">
                          Pre-seeded contracts, 18% GST tax math &amp; workflows for 16+ business verticals
                        </span>
                      </div>
                      <Link
                        href="/use-cases"
                        onClick={() => setActiveDropdown(null)}
                        className="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all shadow-2xs group"
                      >
                        <span>Explore All Industry Workspaces</span>
                        <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                      </Link>
                    </div>
                  </div>
                )}

                {/* ── DROPDOWN: RESOURCES (ATMOSPHERIC PASTEL HERO CARDS + FLOATING MOCKUPS) ── */}
                {/* ── DROPDOWN: RESOURCES (3 ATMOSPHERIC CARDS: FREE TOOLS | GUIDES | BLOGS + 1 BOTTOM RAIL) ── */}
                {activeDropdown === 'resources' && (
                  <div key="resources-tab" className="space-y-3.5 animate-in fade-in zoom-in-[0.99] duration-200 ease-out fill-mode-forwards">
                    
                    {/* Top 3 Side-by-Side Atmospheric Hero Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-4 items-stretch">

                      {/* ── CARD 1: FREE TOOLS (Atmospheric Periwinkle / Slate Pastel) ── */}
                      <div className="relative overflow-hidden rounded-2xl bg-gradient-to-b from-[#ebf1ff] via-[#dfebfd] to-[#d0e0fb] border border-blue-200/70 p-5 sm:p-5.5 flex flex-col justify-between shadow-[0_4px_20px_rgba(79,114,205,0.06)] hover:shadow-md hover:border-blue-300 transition-all duration-300 min-h-[310px]">
                        {/* Soft atmospheric cloud & glow accents */}
                        <div className="absolute top-0 right-0 -mt-8 -mr-8 w-44 h-44 rounded-full bg-white/50 blur-2xl pointer-events-none" />
                        <div className="absolute bottom-0 left-0 -mb-8 -ml-8 w-40 h-40 rounded-full bg-blue-300/30 blur-2xl pointer-events-none" />

                        <div className="relative z-10">
                          {/* Centered Cora Header */}
                          <div className="text-center mb-3.5">
                            <h3 className="text-base font-bold text-zinc-900 tracking-tight leading-snug">
                              Free Tools
                            </h3>
                            <p className="text-[11.5px] text-zinc-600 font-normal mt-0.5 leading-tight">
                              Quick business calculators &amp; generators
                            </p>
                          </div>

                          {/* Floating UI Mockups */}
                          <div className="space-y-2.5 w-full pt-0.5">
                            {/* Floating Mockup 1: GST Calculator */}
                            <Link
                              href="/tools/gst-calculator"
                              onClick={() => setActiveDropdown(null)}
                              className="block rounded-xl bg-white/95 backdrop-blur-md p-2.5 sm:p-3 border border-white/90 shadow-[0_4px_16px_rgba(0,0,0,0.06)] hover:shadow-md hover:scale-[1.01] transition-all group"
                            >
                              <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                  <div className="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200/60">
                                    <Calculator className="w-3 h-3" />
                                  </div>
                                  <span className="text-xs font-bold text-zinc-900 group-hover:text-emerald-700 transition-colors">
                                    GST Calculator
                                  </span>
                                </div>
                                <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full border border-emerald-200/60">
                                  18% Math
                                </span>
                              </div>
                              <div className="mt-1.5 pt-1.5 border-t border-zinc-100 flex items-center justify-between text-[10.5px] font-mono text-zinc-500">
                                <span>₹1,00,000 + 18% GST</span>
                                <span className="font-bold text-zinc-900">₹1,18,000</span>
                              </div>
                            </Link>

                            {/* Floating Mockup 2: Pricing Calculator */}
                            <Link
                              href="/tools/retainer-calculator"
                              onClick={() => setActiveDropdown(null)}
                              className="block rounded-xl bg-white/95 backdrop-blur-md p-2.5 sm:p-3 border border-white/90 shadow-[0_6px_20px_rgba(0,0,0,0.08)] hover:shadow-md hover:scale-[1.01] transition-all group ml-2 sm:ml-3"
                            >
                              <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                  <div className="w-5 h-5 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-200/60">
                                    <BarChart2 className="w-3 h-3" />
                                  </div>
                                  <span className="text-xs font-bold text-zinc-900 group-hover:text-blue-700 transition-colors">
                                    Pricing Calculator
                                  </span>
                                </div>
                                <span className="text-[9px] font-mono font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded-full border border-blue-200/60">
                                  Retainer
                                </span>
                              </div>
                              <div className="mt-1.5 pt-1.5 border-t border-zinc-100 flex items-center justify-between text-[10.5px] font-mono text-zinc-500">
                                <span>Target Margin 65%</span>
                                <span className="font-bold text-zinc-900">₹45,000/mo</span>
                              </div>
                            </Link>

                            {/* Floating Pill: Proposal Generator */}
                            <div className="pt-0.5 flex items-center justify-start">
                              <Link
                                href="/tools/agency-proposal-generator"
                                onClick={() => setActiveDropdown(null)}
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/90 backdrop-blur-md border border-white/90 text-[11px] font-semibold text-zinc-800 hover:bg-white hover:text-zinc-950 transition-all shadow-2xs group"
                              >
                                <FileText className="w-3.5 h-3.5 text-zinc-600 group-hover:scale-110 transition-transform" />
                                <span>Proposal Generator</span>
                                <span className="text-[9px] font-mono font-bold text-zinc-600 bg-zinc-100 px-1.5 py-0.2 rounded border border-zinc-200/80">
                                  E-Sign
                                </span>
                              </Link>
                            </div>
                          </div>
                        </div>

                        {/* Bottom Action */}
                        <div className="relative z-10 pt-3 mt-3.5 flex items-center justify-between border-t border-blue-200/50">
                          <Link
                            href="/tools"
                            onClick={() => setActiveDropdown(null)}
                            className="inline-flex items-center gap-1.5 text-xs font-bold text-blue-950 hover:text-blue-700 transition-colors group"
                          >
                            <span>Explore Free Tools</span>
                            <span className="w-4.5 h-4.5 rounded-full bg-white/90 shadow-2xs flex items-center justify-center group-hover:translate-x-0.5 group-hover:bg-white transition-all">
                              <ArrowRight className="w-2.5 h-2.5 stroke-[2.2]" />
                            </span>
                          </Link>
                        </div>
                      </div>

                      {/* ── CARD 2: GUIDES (Atmospheric Sky / Cyan Pastel) ── */}
                      <div className="relative overflow-hidden rounded-2xl bg-gradient-to-b from-[#e3f6fe] via-[#d6f0fa] to-[#c2e7f7] border border-sky-200/70 p-5 sm:p-5.5 flex flex-col justify-between shadow-[0_4px_20px_rgba(56,189,248,0.06)] hover:shadow-md hover:border-sky-300 transition-all duration-300 min-h-[310px]">
                        {/* Soft atmospheric cloud & glow accents */}
                        <div className="absolute top-0 right-0 -mt-8 -mr-8 w-44 h-44 rounded-full bg-white/50 blur-2xl pointer-events-none" />
                        <div className="absolute bottom-0 left-0 -mb-8 -ml-8 w-40 h-40 rounded-full bg-sky-300/30 blur-2xl pointer-events-none" />

                        <div className="relative z-10">
                          {/* Centered Cora Header */}
                          <div className="text-center mb-3.5">
                            <h3 className="text-base font-bold text-zinc-900 tracking-tight leading-snug">
                              Guides
                            </h3>
                            <p className="text-[11.5px] text-zinc-600 font-normal mt-0.5 leading-tight">
                              Chaptered digital books, SOPs &amp; playbooks
                            </p>
                          </div>

                          {/* Floating UI Mockups */}
                          <div className="space-y-2.5 w-full pt-0.5">
                            {/* Floating Mockup 1: Playbooks & SOPs */}
                            <Link
                              href="/guides"
                              onClick={() => setActiveDropdown(null)}
                              className="block rounded-xl bg-white/95 backdrop-blur-md p-2.5 sm:p-3 border border-white/90 shadow-[0_4px_16px_rgba(0,0,0,0.06)] hover:shadow-md hover:scale-[1.01] transition-all group"
                            >
                              <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                  <div className="w-5 h-5 rounded-md bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200/60">
                                    <BookOpen className="w-3 h-3" />
                                  </div>
                                  <span className="text-xs font-bold text-zinc-900 group-hover:text-amber-800 transition-colors">
                                    Playbooks &amp; SOPs
                                  </span>
                                </div>
                                <span className="text-[9px] font-mono font-bold text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded-full border border-amber-200/60">
                                  Guides
                                </span>
                              </div>
                              <div className="mt-1.5 flex flex-wrap gap-1">
                                <span className="text-[9px] font-mono text-amber-900 bg-amber-50/90 px-1.5 py-0.2 rounded border border-amber-200/60 font-medium">
                                  # Onboarding
                                </span>
                                <span className="text-[9px] font-mono text-amber-900 bg-amber-50/90 px-1.5 py-0.2 rounded border border-amber-200/60 font-medium">
                                  # Scope Creep
                                </span>
                                <span className="text-[9px] font-mono text-amber-900 bg-amber-50/90 px-1.5 py-0.2 rounded border border-amber-200/60 font-medium">
                                  # Retainers
                                </span>
                              </div>
                            </Link>

                            {/* Floating Mockup 2: Scope Creep Defence Playbook */}
                            <Link
                              href="/guides/agency-scope-creep-defence-system"
                              onClick={() => setActiveDropdown(null)}
                              className="block rounded-xl bg-white/95 backdrop-blur-md p-2.5 sm:p-3 border border-white/90 shadow-[0_6px_20px_rgba(0,0,0,0.08)] hover:shadow-md hover:scale-[1.01] transition-all group ml-2 sm:ml-3"
                            >
                              <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                  <div className="w-5 h-5 rounded-md bg-sky-50 text-sky-700 flex items-center justify-center shrink-0 border border-sky-200/60">
                                    <Layers className="w-3 h-3" />
                                  </div>
                                  <span className="text-xs font-bold text-zinc-900 group-hover:text-sky-800 transition-colors">
                                    Scope Creep Defence
                                  </span>
                                </div>
                                <span className="text-[9px] font-mono font-bold text-sky-700 bg-sky-50 px-1.5 py-0.5 rounded-full border border-sky-200/60">
                                  5 Chapters
                                </span>
                              </div>
                              <div className="mt-1.5 pt-1.5 border-t border-zinc-100 flex items-center justify-between text-[10.5px] font-mono text-zinc-600">
                                <span>Margin Defence System</span>
                                <span className="text-emerald-700 font-bold">✓ 100% Free</span>
                              </div>
                            </Link>

                            {/* Floating Pill: Flagship Onboarding Guide */}
                            <div className="pt-0.5 flex items-center justify-start">
                              <Link
                                href="/guides/agency-client-onboarding-playbook"
                                onClick={() => setActiveDropdown(null)}
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/90 backdrop-blur-md border border-white/90 text-[11px] font-semibold text-zinc-800 hover:bg-white hover:text-zinc-950 transition-all shadow-2xs group"
                              >
                                <Sparkles className="w-3.5 h-3.5 text-amber-600 group-hover:scale-110 transition-transform" />
                                <span>Client Onboarding Book</span>
                                <span className="text-[9px] font-mono font-bold text-amber-800 bg-amber-100/70 px-1.5 py-0.2 rounded border border-amber-200/80">
                                  8 Chapters
                                </span>
                              </Link>
                            </div>
                          </div>
                        </div>

                        {/* Bottom Action */}
                        <div className="relative z-10 pt-3 mt-3.5 flex items-center justify-between border-t border-sky-200/50">
                          <Link
                            href="/guides"
                            onClick={() => setActiveDropdown(null)}
                            className="inline-flex items-center gap-1.5 text-xs font-bold text-sky-950 hover:text-sky-700 transition-colors group"
                          >
                            <span>Explore Guides</span>
                            <span className="w-4.5 h-4.5 rounded-full bg-white/90 shadow-2xs flex items-center justify-center group-hover:translate-x-0.5 group-hover:bg-white transition-all">
                              <ArrowRight className="w-2.5 h-2.5 stroke-[2.2]" />
                            </span>
                          </Link>
                        </div>
                      </div>

                      {/* ── CARD 3: BLOGS (Atmospheric Warm Sand / Amber Pastel) ── */}
                      <div className="relative overflow-hidden rounded-2xl bg-gradient-to-b from-[#fef7ee] via-[#fef0dd] to-[#fde5c8] border border-amber-200/70 p-5 sm:p-5.5 flex flex-col justify-between shadow-[0_4px_20px_rgba(245,158,11,0.06)] hover:shadow-md hover:border-amber-300 transition-all duration-300 min-h-[310px]">
                        {/* Soft atmospheric cloud & glow accents */}
                        <div className="absolute top-0 right-0 -mt-8 -mr-8 w-44 h-44 rounded-full bg-white/50 blur-2xl pointer-events-none" />
                        <div className="absolute bottom-0 left-0 -mb-8 -ml-8 w-40 h-40 rounded-full bg-amber-300/30 blur-2xl pointer-events-none" />

                        <div className="relative z-10">
                          {/* Centered Cora Header */}
                          <div className="text-center mb-3.5">
                            <h3 className="text-base font-bold text-zinc-900 tracking-tight leading-snug">
                              Blogs
                            </h3>
                            <p className="text-[11.5px] text-zinc-600 font-normal mt-0.5 leading-tight">
                              Focused editorial answers to agency workflows
                            </p>
                          </div>

                          {/* Floating UI Mockups */}
                          <div className="space-y-2.5 w-full pt-0.5">
                            {/* Floating Mockup 1: Scope Creep Editorial */}
                            <Link
                              href="/blog/how-to-stop-agency-scope-creep"
                              onClick={() => setActiveDropdown(null)}
                              className="block rounded-xl bg-white/95 backdrop-blur-md p-2.5 sm:p-3 border border-white/90 shadow-[0_4px_16px_rgba(0,0,0,0.06)] hover:shadow-md hover:scale-[1.01] transition-all group"
                            >
                              <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                  <div className="w-5 h-5 rounded-md bg-amber-100/70 text-amber-800 flex items-center justify-center shrink-0 border border-amber-200/60">
                                    <Sparkles className="w-3 h-3 text-amber-600 fill-amber-600" />
                                  </div>
                                  <span className="text-xs font-bold text-zinc-900 group-hover:text-amber-900 transition-colors">
                                    Stop Scope Creep
                                  </span>
                                </div>
                                <span className="text-[9px] font-mono font-bold text-amber-900 bg-amber-50 px-1.5 py-0.5 rounded-full border border-amber-200/60">
                                  45s Read
                                </span>
                              </div>
                              <div className="mt-1.5 pt-1.5 border-t border-zinc-100 flex items-center justify-between text-[10.5px] font-mono text-zinc-600">
                                <span>Positive Friction Method</span>
                                <span className="text-emerald-700 font-bold">✓ Playbook</span>
                              </div>
                            </Link>

                            {/* Floating Mockup 2: Onboarding Coordination */}
                            <Link
                              href="/blog/agency-client-onboarding-process"
                              onClick={() => setActiveDropdown(null)}
                              className="block rounded-xl bg-white/95 backdrop-blur-md p-2.5 sm:p-3 border border-white/90 shadow-[0_6px_20px_rgba(0,0,0,0.08)] hover:shadow-md hover:scale-[1.01] transition-all group ml-2 sm:ml-3"
                            >
                              <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                  <div className="w-5 h-5 rounded-md bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200/60">
                                    <FileText className="w-3 h-3" />
                                  </div>
                                  <span className="text-xs font-bold text-zinc-900 group-hover:text-emerald-800 transition-colors">
                                    5-Step Client Kickoff
                                  </span>
                                </div>
                                <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full border border-emerald-200/60">
                                  72h SLA
                                </span>
                              </div>
                              <div className="mt-1.5 pt-1.5 border-t border-zinc-100 flex items-center justify-between text-[10.5px] font-mono text-zinc-600">
                                <span>Eliminate WhatsApp Chaos</span>
                                <span className="text-emerald-700 font-bold">✓ SOP</span>
                              </div>
                            </Link>

                            {/* Floating Pill: Topic Clusters */}
                            <div className="pt-0.5 flex items-center justify-start">
                              <Link
                                href="/blog"
                                onClick={() => setActiveDropdown(null)}
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/90 backdrop-blur-md border border-white/90 text-[11px] font-semibold text-zinc-800 hover:bg-white hover:text-zinc-950 transition-all shadow-2xs group"
                              >
                                <Layers className="w-3.5 h-3.5 text-amber-700 group-hover:scale-110 transition-transform" />
                                <span>Explore Topic Clusters</span>
                                <span className="text-[9px] font-mono font-bold text-amber-800 bg-amber-100/70 px-1.5 py-0.2 rounded border border-amber-200/80">
                                  5 Clusters
                                </span>
                              </Link>
                            </div>
                          </div>
                        </div>

                        {/* Bottom Action */}
                        <div className="relative z-10 pt-3 mt-3.5 flex items-center justify-between border-t border-amber-200/50">
                          <Link
                            href="/blog"
                            onClick={() => setActiveDropdown(null)}
                            className="inline-flex items-center gap-1.5 text-xs font-bold text-amber-950 hover:text-amber-800 transition-colors group"
                          >
                            <span>Explore Blogs</span>
                            <span className="w-4.5 h-4.5 rounded-full bg-white/90 shadow-2xs flex items-center justify-center group-hover:translate-x-0.5 group-hover:bg-white transition-all">
                              <ArrowRight className="w-2.5 h-2.5 stroke-[2.2]" />
                            </span>
                          </Link>
                        </div>
                      </div>

                    </div>

                    {/* ── CARD 4: DEVELOPER HUB (Atmospheric Sage / Mint Pastel Full-Width Rail) ── */}
                    <div className="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#eaf7ee] via-[#f3faf5] to-[#e0f4e6] border border-emerald-200/70 p-3 sm:p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-[0_2px_10px_rgba(16,185,129,0.04)] hover:border-emerald-300 transition-all">
                      <div className="flex items-center gap-3">
                        <div className="w-8 h-8 rounded-lg bg-white text-emerald-800 border border-emerald-200/70 flex items-center justify-center shrink-0 font-mono font-bold text-xs shadow-2xs select-none">
                          &lt;/&gt;
                        </div>
                        <div>
                          <h4 className="font-bold text-xs text-zinc-950">
                            Building something custom?
                          </h4>
                          <p className="text-[11px] text-zinc-600 font-normal">
                            Developer Docs, Integrations &amp; API access
                          </p>
                        </div>
                      </div>

                      <Link
                        href="/docs"
                        onClick={() => setActiveDropdown(null)}
                        className="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-emerald-50 text-emerald-800 text-xs font-bold transition-all shadow-2xs group shrink-0 border border-emerald-200/70"
                      >
                        <span>Developer Hub</span>
                        <span className="w-4 h-4 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                          <ArrowRight className="w-2.5 h-2.5 stroke-[2.5]" />
                        </span>
                      </Link>
                    </div>

                  </div>
                )}

              </div>
            </div>
          )}

        </div>

        {/* ══════════════════════════════════════════════════════════════════
            MOBILE FULL-SCREEN DRILL-DOWN MENU (CLAY INSPIRATION & ZERO SCROLL)
        ══════════════════════════════════════════════════════════════════ */}
        {mobileMenuOpen && (
          <div className="lg:hidden fixed inset-0 bg-white z-[999] flex flex-col justify-between p-6 sm:p-8 animate-in fade-in duration-150">

            {/* 1. Header Bar */}
            <div className="flex items-center justify-between pb-6 border-b border-zinc-100 shrink-0">
              {activeMobileSubmenu ? (
                <button
                  type="button"
                  onClick={() => setActiveMobileSubmenu(null)}
                  className="flex items-center gap-1.5 text-sm font-bold text-zinc-950 hover:text-black py-1"
                >
                  <ChevronLeft className="w-4 h-4" />
                  <span className="capitalize">{activeMobileSubmenu === 'features' ? 'All Features' : activeMobileSubmenu}</span>
                </button>
              ) : (
                <Link href="/" onClick={() => setMobileMenuOpen(false)} className="font-display text-xl font-bold text-zinc-950 tracking-tight">
                  CORA
                </Link>
              )}

              <div className="flex items-center gap-3">
                {isToolsPage ? (
                  <Link
                    href="/"
                    onClick={() => setMobileMenuOpen(false)}
                    className="text-xs font-semibold text-zinc-900 bg-zinc-100 hover:bg-zinc-200 px-3.5 py-1.5 rounded-full transition-colors flex items-center gap-1"
                  >
                    <span>Back to Site</span>
                    <ArrowRight className="w-3 h-3 text-zinc-600" />
                  </Link>
                ) : (
                  <div className="relative inline-flex items-center rotate-[-1.5deg] px-2.5 py-0.5 select-none">
                    <span className="font-scribble text-xl font-bold text-zinc-950 leading-none relative z-10 flex items-center gap-1">
                      <span className="text-sm">✦</span>
                      <span>free forever</span>
                    </span>
                    <svg
                      className="absolute inset-0 w-full h-full text-violet-400 stroke-current fill-none pointer-events-none -rotate-1 scale-110"
                      viewBox="0 0 130 36"
                      preserveAspectRatio="none"
                    >
                      <path
                        d="M10,18 C18,6 112,4 122,16 C128,24 98,32 58,32 C24,32 6,26 10,18 Z"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      />
                    </svg>
                  </div>
                )}
                <button
                  type="button"
                  onClick={() => { setMobileMenuOpen(false); setActiveMobileSubmenu(null); }}
                  className="p-1.5 text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 rounded-full transition-colors"
                  aria-label="Close Menu"
                >
                  <X className="w-5 h-5" />
                </button>
              </div>
            </div>

            {/* 2. Middle Content (Scrollable if necessary) */}
            <div className="flex-1 overflow-y-auto py-4">

              {/* Level 1: Main Category List (Matching Desktop Header Order) */}
              {!activeMobileSubmenu && (
                <div className="space-y-1 text-base font-semibold text-zinc-900">
                  {isToolsPage ? (
                    /* Tools-Specific Mobile Menu */
                    <div className="space-y-4">
                      {/* Back to Site Card */}
                      <Link
                        href="/"
                        onClick={() => setMobileMenuOpen(false)}
                        className="p-4 rounded-2xl bg-zinc-950 text-white flex items-center justify-between shadow-md transition-all border border-zinc-800 group"
                      >
                        <div className="flex items-center gap-3">
                          <div className="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white shrink-0">
                            <ArrowRight className="w-5 h-5 rotate-180 text-zinc-400 group-hover:text-white" />
                          </div>
                          <div>
                            <div className="flex items-center gap-2">
                              <span className="font-bold text-sm text-white">Back to Main Platform</span>
                            </div>
                            <p className="text-xs text-zinc-400 font-normal mt-0.5">
                              Return to Cora features, pricing &amp; solutions
                            </p>
                          </div>
                        </div>
                        <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:translate-x-1 group-hover:text-white transition-transform shrink-0" />
                      </Link>

                      {/* Tools Quick Directory */}
                      <div className="pt-2">
                        <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Free Micro-Tools (34)
                          </span>
                          <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                            Zero Login
                          </span>
                        </div>

                        <div className="space-y-1">
                          {/* PDF Tools */}
                          <Link
                            href="/tools/compress-pdf"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Minimize2 className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Compress PDF</span>
                                <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200/60">Popular</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Reduce file size up to 70% in browser</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/word-to-pdf"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <FileType className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Word to PDF</span>
                                <span className="text-[9px] font-mono font-bold text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200/60">New</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Publish text &amp; copy into clean A4 PDF</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/number-pdf"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 border border-purple-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Hash className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Add Page Numbers</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Custom pagination headers &amp; footers</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/remove-pages"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-red-50 text-red-600 border border-red-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Trash2 className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Remove Pages</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Delete unwanted sheets with 1 click</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/ai-pdf-summarizer"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Bot className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">AI PDF Summarizer</span>
                                <span className="text-[9px] font-mono font-bold text-amber-700 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200/60">AI</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Scan contract risks &amp; extract clauses</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/merge-pdf"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 border border-rose-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Files className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Merge PDF Files</span>
                                <span className="text-[9px] font-mono font-bold text-rose-700 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200/60">Fast</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Combine documents with visual reordering</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/images-to-pdf"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <ImageIcon className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Images to PDF Converter</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Convert JPG, PNG &amp; WebP to PDF</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/esign-pdf"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <FileCheck className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Digital eSign PDF</span>
                                <span className="text-[9px] font-mono font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.2 rounded border border-indigo-200/60">Sec 10A</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Draw or type legally valid signatures</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/split-pdf"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Scissors className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Split &amp; Extract PDF</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Custom page ranges &amp; single sheets</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/rotate-pdf"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <RotateCw className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Rotate PDF Pages</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Rotate 90°, 180° or 270° permanently</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/watermark-pdf"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-violet-50 text-violet-600 border border-violet-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Stamp className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Add Watermark to PDF</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Stamp text or confidential marks</p>
                            </div>
                          </Link>

                          {/* Business & Tax Tools */}
                          <Link
                            href="/tools/gst-calculator"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Calculator className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">GST Tax Calculator</span>
                                <span className="text-[9px] font-mono font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.2 rounded border border-indigo-200/60">SAC 9983</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">18% tax breakdown &amp; CGST/SGST</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/retainer-calculator"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 border border-orange-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Receipt className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Retainer Math Calculator</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">20% scope creep buffer &amp; tiers</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/contract-builder"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Scale className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Contract Clause Builder</span>
                                <span className="text-[9px] font-mono font-bold text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200/60">Sec 10A</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Indian IT Act compliant terms</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/listing-ai"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 border border-rose-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Sparkles className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Listing AI Generator</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Brochures, captions &amp; creative copy</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/upi-qr-generator"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 border border-purple-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Zap className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Dynamic UPI QR Link</span>
                                <span className="text-[9px] font-mono font-bold text-purple-700 bg-purple-50 px-1.5 py-0.2 rounded border border-purple-200/60">0% Fee</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">GPay &amp; PhonePe intent links</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools/embed-builder"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-zinc-50 transition-colors"
                          >
                            <div className="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 border border-teal-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                              <Code className="w-4 h-4 stroke-[2]" />
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="flex items-center justify-between">
                                <span className="text-sm font-bold text-zinc-950">Embed Widget Builder</span>
                              </div>
                              <p className="text-xs text-zinc-500 font-normal mt-0.5">Framer &amp; Webflow lead forms</p>
                            </div>
                          </Link>

                          <Link
                            href="/tools"
                            onClick={() => setMobileMenuOpen(false)}
                            className="flex items-center justify-between p-3 rounded-2xl bg-zinc-50 hover:bg-zinc-100 transition-colors mt-2"
                          >
                            <span className="text-sm font-bold text-zinc-950">View All 12 Micro-Tools Hub</span>
                            <ArrowRight className="w-4 h-4 text-zinc-400" />
                          </Link>
                        </div>
                      </div>
                    </div>
                  ) : (
                    <>
                      {/* Clean Mobile Menu Navigation List */}
                      <div className="space-y-1">
                        {/* 1. Features */}
                        <button
                          type="button"
                          onClick={() => setActiveMobileSubmenu('features')}
                          className="w-full p-2.5 rounded-xl hover:bg-zinc-50 flex items-center justify-between text-left text-[15px] font-semibold text-zinc-900 hover:text-black transition-colors"
                        >
                          <span>Features</span>
                          <ChevronRight className="w-4 h-4 text-zinc-400" />
                        </button>

                        {/* 2. Industries */}
                        <button
                          type="button"
                          onClick={() => setActiveMobileSubmenu('industries')}
                          className="w-full p-2.5 rounded-xl hover:bg-zinc-50 flex items-center justify-between text-left text-[15px] font-semibold text-zinc-900 hover:text-black transition-colors"
                        >
                          <span>Industries</span>
                          <ChevronRight className="w-4 h-4 text-zinc-400" />
                        </button>

                        {/* 3. Resources */}
                        <button
                          type="button"
                          onClick={() => setActiveMobileSubmenu('resources')}
                          className="w-full p-2.5 rounded-xl hover:bg-zinc-50 flex items-center justify-between text-left text-[15px] font-semibold text-zinc-900 hover:text-black transition-colors"
                        >
                          <span>Resources</span>
                          <ChevronRight className="w-4 h-4 text-zinc-400" />
                        </button>

                        {/* 4. Pricing (Direct Link + Free Forever Badge) */}
                        <Link
                          href="/pricing"
                          onClick={() => setMobileMenuOpen(false)}
                          className="w-full p-2.5 rounded-xl hover:bg-zinc-50 flex items-center justify-between text-[15px] font-semibold text-zinc-900 hover:text-black transition-colors"
                        >
                          <span>Pricing</span>
                          <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                            Free Tier
                          </span>
                        </Link>

                        {/* 5. Company */}
                        <button
                          type="button"
                          onClick={() => setActiveMobileSubmenu('company')}
                          className="w-full p-2.5 rounded-xl hover:bg-zinc-50 flex items-center justify-between text-left text-[15px] font-semibold text-zinc-900 hover:text-black transition-colors"
                        >
                          <span>Company</span>
                          <ChevronRight className="w-4 h-4 text-zinc-400" />
                        </button>
                      </div>

                      {/* Anchored Promo Cards (Near / Above the Bottom CTAs) */}
                      <div className="pt-5 space-y-2.5">
                        {/* ── AI CO-FOUNDER VISUAL HERO BANNER ── */}
                        <Link
                          href="/ai-agent"
                          onClick={() => setMobileMenuOpen(false)}
                          className="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#2563eb] via-[#7c3aed] to-[#ec4899] text-white p-4 flex flex-col justify-between shadow-md hover:shadow-lg transition-all group block"
                        >
                          {/* Ambient Glowing Highlights & Agent Visual Artwork */}
                          <div className="absolute right-0 top-0 bottom-0 w-[45%] pointer-events-none opacity-40 mix-blend-screen overflow-hidden">
                            <Image
                              src="/images/cora_telemetry_female_agent.png"
                              alt="Cora AI Co-Founder"
                              fill
                              className="object-cover object-center"
                            />
                          </div>

                          <div className="relative z-10 max-w-[70%] space-y-1">
                            <h3 className="font-display text-base sm:text-lg font-bold text-white tracking-tight leading-snug">
                              AI Co-Founder
                            </h3>
                            <p className="text-[11px] text-white/90 font-normal leading-snug">
                              Automate daily operations, turn voice notes into scopes, and triage client work on autopilot.
                            </p>
                          </div>

                          <div className="relative z-10 pt-3 flex items-center justify-between">
                            <span className="inline-flex items-center gap-1.5 bg-white text-zinc-950 px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm group-hover:bg-zinc-100 transition-colors">
                              <span>Try AI Co-Founder</span>
                              <ArrowRight className="w-3 h-3 stroke-[2.5]" />
                            </span>
                          </div>
                        </Link>
                      </div>
                    </>
                  )}
                </div>
              )}

              {/* Level 2 Submenu: Features (4 Balanced Pillars) */}
              {activeMobileSubmenu === 'features' && (
                <div className="space-y-5 animate-in fade-in slide-in-from-right-3 duration-150">

                  {/* Pillar 1: Intelligence & AI */}
                  <div className="space-y-1">
                    <div className="flex items-center justify-between pb-1.5 mb-1.5 border-b border-zinc-100">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                        Intelligence &amp; AI
                      </span>
                      <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                        FLAGSHIP
                      </span>
                    </div>
                    <Link href="/features/ai-cofounder" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <AiCofounderColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">AI Co-Founder</div><div className="text-[11px] text-zinc-500">Automate daily operations &amp; triage</div></div>
                    </Link>
                    <Link href="/features/voice-to-scope" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <VoiceScopeColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">Voice-to-Scope</div><div className="text-[11px] text-zinc-500">Turn voice notes into signed scopes</div></div>
                    </Link>
                    <Link href="/features/content-ai" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <ContentAiColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">Content AI &amp; GEO</div><div className="text-[11px] text-zinc-500">Generate viral scripts &amp; rank in AI</div></div>
                    </Link>
                    <Link href="/features/rag-mcp" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <RagMemoryColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">RAG Memory MCP</div><div className="text-[11px] text-zinc-500">Sync client context with IDE &amp; AI</div></div>
                    </Link>
                  </div>

                  {/* Pillar 2: Growth & Pipeline */}
                  <div className="space-y-1">
                    <div className="flex items-center justify-between pb-1.5 mb-1.5 border-b border-zinc-100">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                        Growth &amp; Pipeline
                      </span>
                      <span className="text-[9px] font-mono font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200/60">
                        GROWTH
                      </span>
                    </div>
                    <Link href="/features/lead-crm" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <LeadCrmColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">Kanban Lead CRM</div><div className="text-[11px] text-zinc-500">Close high-ticket deals on WhatsApp</div></div>
                    </Link>
                    <Link href="/features/canvas-builder" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <CanvasBuilderColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">Funnel Builder</div><div className="text-[11px] text-zinc-500">Launch high-converting landing pages</div></div>
                    </Link>
                    <Link href="/features/form-builder" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <FormBuilderColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">Visual Forms</div><div className="text-[11px] text-zinc-500">Capture rich briefs &amp; book calls</div></div>
                    </Link>
                    <Link href="/features/review-portal" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <ReviewPortalColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">5★ Review Portal</div><div className="text-[11px] text-zinc-500">Collect 5-star reviews on autopilot</div></div>
                    </Link>
                  </div>

                  {/* Pillar 3: Operations & Legal */}
                  <div className="space-y-1">
                    <div className="flex items-center justify-between pb-1.5 mb-1.5 border-b border-zinc-100">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                        Operations &amp; Legal
                      </span>
                      <span className="text-[9px] font-mono font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200/60">
                        LEGAL TECH
                      </span>
                    </div>
                    <Link href="/features/esign-vault" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <EsignVaultColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">SHA-256 E-Signs</div><div className="text-[11px] text-zinc-500">Lock legal contracts in 60 seconds</div></div>
                    </Link>
                    <Link href="/features/crew-dispatch" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <CrewDispatchColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">Crew Dispatch</div><div className="text-[11px] text-zinc-500">Dispatch call sheets with 0 conflicts</div></div>
                    </Link>
                    <Link href="/features/master-calendar" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <MasterCalendarColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">Master Calendar</div><div className="text-[11px] text-zinc-500">Coordinate client shoots &amp; dates</div></div>
                    </Link>
                    <Link href="/features/task-board" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <TaskBoardColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">Task Board</div><div className="text-[11px] text-zinc-500">Deliver sprints &amp; client proofing</div></div>
                    </Link>
                  </div>

                  {/* Pillar 4: Finance & Assets */}
                  <div className="space-y-1">
                    <div className="flex items-center justify-between pb-1.5 mb-1.5 border-b border-zinc-100">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                        Finance &amp; Assets
                      </span>
                      <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                        INDIA GST
                      </span>
                    </div>
                    <Link href="/features/gst-invoicing" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <GstInvoicingColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">18% GST Invoicing</div><div className="text-[11px] text-zinc-500">Issue GST bills &amp; collect UPI pay</div></div>
                    </Link>
                    <Link href="/features/asset-gear" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <AssetGearColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">Gear &amp; Inventory</div><div className="text-[11px] text-zinc-500">Track gear &amp; prevent equipment loss</div></div>
                    </Link>
                    <Link href="/features/media-hub" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <MediaHubColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">Media Hub &amp; RAW</div><div className="text-[11px] text-zinc-500">Deliver 4K watermark-proof galleries</div></div>
                    </Link>
                    <Link href="/features/rbac-system" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                      <RbacSecurityColorIcon className="w-8 h-8" />
                      <div><div className="text-xs font-bold text-zinc-950">Multi-Tenant RBAC</div><div className="text-[11px] text-zinc-500">Protect data with team permissions</div></div>
                    </Link>
                  </div>

                  <div className="pt-2">
                    <Link href="/features" onClick={() => setMobileMenuOpen(false)} className="text-xs font-bold text-zinc-950 flex items-center gap-1.5 hover:text-zinc-600 transition-colors">
                      <span>Explore all 20 modules &amp; roadmap</span>
                      <ArrowRight className="w-3.5 h-3.5" />
                    </Link>
                  </div>
                </div>
              )}

              {/* Level 2 Submenu: Industries (Industry Workspaces) */}
              {activeMobileSubmenu === 'industries' && (
                <div className="space-y-1.5 animate-in fade-in slide-in-from-right-3 duration-150">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-2">
                    INDUSTRY WORKSPACES
                  </span>
                  <Link href="/use-cases/software-agencies" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 border border-blue-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Code className="w-4 h-4 stroke-[2]" /></div>
                    <span className="text-xs font-bold text-zinc-950">Software Agencies</span>
                  </Link>
                  <Link href="/use-cases/lawyers-law-firms" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-lg bg-slate-500/10 text-slate-700 border border-slate-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Scale className="w-4 h-4 stroke-[2]" /></div>
                    <span className="text-xs font-bold text-zinc-950">Lawyers &amp; Law Firms</span>
                  </Link>
                  <Link href="/use-cases/tax-ca-firms" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Receipt className="w-4 h-4 stroke-[2]" /></div>
                    <span className="text-xs font-bold text-zinc-950">Tax &amp; CA Firms</span>
                  </Link>
                  <Link href="/use-cases/consultants-advisors" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 border border-indigo-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Briefcase className="w-4 h-4 stroke-[2]" /></div>
                    <span className="text-xs font-bold text-zinc-950">Consultants &amp; Advisors</span>
                  </Link>
                  <Link href="/use-cases/marketing-seo" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 border border-sky-500/20 flex items-center justify-center shrink-0 shadow-2xs"><BarChart2 className="w-4 h-4 stroke-[2]" /></div>
                    <span className="text-xs font-bold text-zinc-950">Marketing &amp; SEO</span>
                  </Link>
                  <Link href="/use-cases/design-uiux" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-lg bg-violet-500/10 text-violet-600 border border-violet-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Sparkles className="w-4 h-4 stroke-[2]" /></div>
                    <span className="text-xs font-bold text-zinc-950">Design &amp; UI/UX</span>
                  </Link>
                  <Link href="/use-cases/doctors-clinics" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 border border-teal-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Heart className="w-4 h-4 stroke-[2]" /></div>
                    <span className="text-xs font-bold text-zinc-950">Doctors &amp; Clinics</span>
                  </Link>
                  <Link href="/use-cases/salons-wellness" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-lg bg-pink-500/10 text-pink-600 border border-pink-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Scissors className="w-4 h-4 stroke-[2]" /></div>
                    <span className="text-xs font-bold text-zinc-950">Salons, Spas &amp; Wellness</span>
                  </Link>
                  <Link href="/use-cases/real-estate-property" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Building2 className="w-4 h-4 stroke-[2]" /></div>
                    <span className="text-xs font-bold text-zinc-950">Real Estate &amp; Property</span>
                  </Link>
                  <Link href="/use-cases/photo-video-studios" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-600 border border-rose-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Camera className="w-4 h-4 stroke-[2]" /></div>
                    <span className="text-xs font-bold text-zinc-950">Photo &amp; Video Studios</span>
                  </Link>
                  <div className="pt-2">
                    <Link href="/use-cases" onClick={() => setMobileMenuOpen(false)} className="text-xs font-bold text-zinc-950 flex items-center gap-1.5 hover:text-zinc-600 transition-colors">
                      <span>Explore all 16+ industry schemas</span>
                      <ArrowRight className="w-3.5 h-3.5" />
                    </Link>
                  </div>
                </div>
              )}

              {/* Level 2 Submenu: Resources (Simple, Clean Mobile Layout) */}
              {activeMobileSubmenu === 'resources' && (
                <div className="space-y-2 animate-in fade-in slide-in-from-right-3 duration-150">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-2">
                    RESOURCES &amp; TOOLS
                  </span>

                  {/* 1. Free Tools */}
                  <Link
                    href="/tools"
                    onClick={() => setMobileMenuOpen(false)}
                    className="flex items-center gap-3.5 p-3 rounded-2xl bg-blue-50/60 hover:bg-blue-50 border border-blue-100/80 transition-all group"
                  >
                    <div className="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 border border-blue-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                      <Calculator className="w-4.5 h-4.5 stroke-[2]" />
                    </div>
                    <div className="min-w-0 flex-1">
                      <div className="flex items-center justify-between">
                        <span className="text-[13px] font-bold text-zinc-950">Free Tools</span>
                        <span className="text-[9px] font-mono font-bold text-blue-700 bg-blue-100/70 px-2 py-0.5 rounded-full">
                          Calculators
                        </span>
                      </div>
                      <p className="text-[11px] text-zinc-500 font-normal mt-0.5">
                        GST, pricing &amp; agency proposal generators
                      </p>
                    </div>
                    <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-700 group-hover:translate-x-0.5 transition-all" />
                  </Link>

                  {/* 2. Guides */}
                  <Link
                    href="/guides"
                    onClick={() => setMobileMenuOpen(false)}
                    className="flex items-center gap-3.5 p-3 rounded-2xl bg-sky-50/60 hover:bg-sky-50 border border-sky-100/80 transition-all group"
                  >
                    <div className="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 border border-sky-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                      <BookOpen className="w-4.5 h-4.5 stroke-[2]" />
                    </div>
                    <div className="min-w-0 flex-1">
                      <div className="flex items-center justify-between">
                        <span className="text-[13px] font-bold text-zinc-950">Guides &amp; Playbooks</span>
                        <span className="text-[9px] font-mono font-bold text-sky-700 bg-sky-100/70 px-2 py-0.5 rounded-full">
                          Playbooks
                        </span>
                      </div>
                      <p className="text-[11px] text-zinc-500 font-normal mt-0.5">
                        Chaptered digital books &amp; agency SOPs
                      </p>
                    </div>
                    <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-700 group-hover:translate-x-0.5 transition-all" />
                  </Link>

                  {/* 3. Blogs */}
                  <Link
                    href="/blog"
                    onClick={() => setMobileMenuOpen(false)}
                    className="flex items-center gap-3.5 p-3 rounded-2xl bg-amber-50/60 hover:bg-amber-50 border border-amber-100/80 transition-all group"
                  >
                    <div className="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 border border-amber-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                      <Sparkles className="w-4.5 h-4.5 stroke-[2]" />
                    </div>
                    <div className="min-w-0 flex-1">
                      <div className="flex items-center justify-between">
                        <span className="text-[13px] font-bold text-zinc-950">Blogs &amp; Articles</span>
                        <span className="text-[9px] font-mono font-bold text-amber-800 bg-amber-100/70 px-2 py-0.5 rounded-full">
                          Editorial
                        </span>
                      </div>
                      <p className="text-[11px] text-zinc-500 font-normal mt-0.5">
                        Tactical answers to client &amp; workflow issues
                      </p>
                    </div>
                    <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-700 group-hover:translate-x-0.5 transition-all" />
                  </Link>

                  {/* 4. Developer Hub */}
                  <Link
                    href="/docs"
                    onClick={() => setMobileMenuOpen(false)}
                    className="flex items-center gap-3.5 p-3 rounded-2xl bg-emerald-50/60 hover:bg-emerald-50 border border-emerald-100/80 transition-all group"
                  >
                    <div className="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-700 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform font-mono font-bold text-xs">
                      &lt;/&gt;
                    </div>
                    <div className="min-w-0 flex-1">
                      <div className="flex items-center justify-between">
                        <span className="text-[13px] font-bold text-zinc-950">Developer Hub</span>
                        <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-full">
                          API &amp; Docs
                        </span>
                      </div>
                      <p className="text-[11px] text-zinc-500 font-normal mt-0.5">
                        REST APIs, webhooks &amp; architecture specs
                      </p>
                    </div>
                    <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-700 group-hover:translate-x-0.5 transition-all" />
                  </Link>
                </div>
              )}

              {/* Level 2 Submenu: Company */}
              {activeMobileSubmenu === 'company' && (
                <div className="space-y-2 animate-in fade-in slide-in-from-right-3 duration-150">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-2">
                    COMPANY &amp; ECOSYSTEM
                  </span>
                  <Link href="/about" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Sparkles className="w-4 h-4 stroke-[2]" /></div>
                    <div className="text-xs font-bold text-zinc-950">About Cora</div>
                  </Link>
                  <Link href="/brand" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 border border-amber-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Layers className="w-4 h-4 stroke-[2]" /></div>
                    <div className="text-xs font-bold text-zinc-950">Brand &amp; Design Assets</div>
                  </Link>
                  <Link href="/security" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 border border-blue-500/20 flex items-center justify-center shrink-0 shadow-2xs"><ShieldCheck className="w-4 h-4 stroke-[2]" /></div>
                    <div className="text-xs font-bold text-zinc-950">Security &amp; Trust</div>
                  </Link>
                  <Link href="/contact" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 border border-purple-500/20 flex items-center justify-center shrink-0 shadow-2xs"><MessageSquare className="w-4 h-4 stroke-[2]" /></div>
                    <div className="text-xs font-bold text-zinc-950">Contact &amp; Support</div>
                  </Link>
                  <Link href="/status" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-600 border border-teal-500/20 flex items-center justify-center shrink-0 shadow-2xs"><Zap className="w-4 h-4 stroke-[2]" /></div>
                    <div className="text-xs font-bold text-zinc-950 flex items-center justify-between w-full pr-2">
                      <span>System Status</span>
                      <span className="text-[10px] font-mono text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">99.98%</span>
                    </div>
                  </Link>
                </div>
              )}

              {/* Non-sticky footer inside submenus */}
              {activeMobileSubmenu && (
                <div className="pt-8 mt-6 border-t border-zinc-100 space-y-2.5 pb-6">
                  {/* 🇮🇳 Tonal Secondary India Founder Plan Callout */}
                  <Link
                    href="/pricing"
                    onClick={() => setMobileMenuOpen(false)}
                    className="group flex items-center justify-between p-2.5 rounded-2xl bg-zinc-50 hover:bg-zinc-100/90 border border-zinc-200/90 transition-all text-zinc-950 shadow-2xs"
                  >
                    <div className="flex items-center gap-2.5 min-w-0">
                      <div className="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-sm shrink-0 border border-zinc-200 shadow-2xs">
                        🇮🇳
                      </div>
                      <div className="min-w-0">
                        <div className="flex items-center gap-1.5 flex-wrap">
                          <span className="text-xs font-bold text-zinc-950 tracking-tight">India Founder Plan</span>
                          <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/60">
                            ₹499<span className="text-emerald-600 font-normal">/mo</span>
                          </span>
                          <span className="text-[9.5px] font-mono line-through text-zinc-400">₹1,999</span>
                        </div>
                        <p className="text-[10.5px] text-zinc-500 font-normal truncate">
                          Special pricing valid till 1 January 2027
                        </p>
                      </div>
                    </div>
                    <div className="shrink-0 pl-2">
                      <span className="inline-flex items-center gap-1 text-[11px] font-bold text-zinc-900 bg-white group-hover:bg-zinc-50 px-2.5 py-1.5 rounded-xl border border-zinc-200 shadow-2xs transition-colors">
                        <span>Claim</span>
                        <ArrowRight className="w-3 h-3 text-zinc-500 group-hover:text-zinc-950 group-hover:translate-x-0.5 transition-all" />
                      </span>
                    </div>
                  </Link>

                  <a
                    href="https://app.heycora.in/workspace/login?source=mobile_menu"
                    className="w-full group inline-flex items-center justify-center gap-2.5 bg-zinc-950 hover:bg-black text-white px-5 py-3.5 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-md active:scale-[0.99] border border-zinc-900"
                  >
                    {/* Official Google Icon */}
                    <div className="w-4 h-4 flex items-center justify-center shrink-0">
                      <svg className="w-3.5 h-3.5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.36 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.98 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                      </svg>
                    </div>
                    <span>Get started with Google</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-white group-hover:translate-x-0.5 transition-all" />
                  </a>

                  {/* Trust Badges Bar */}
                  <div className="flex items-center justify-center gap-2 pt-0.5 text-[10.5px] font-mono text-zinc-500">
                    <span className="flex items-center gap-1.5">
                      <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" /> Free Forever
                    </span>
                    <span>&bull;</span>
                    <span>No Card Needed</span>
                    <span>&bull;</span>
                    <span>Made for India 🇮🇳</span>
                  </div>
                </div>
              )}

            </div>

            {/* 3. Bottom Anchored CTAs (Only rendered on main Level-1 menu, never sticky in submenus) */}
            {!activeMobileSubmenu && (
              <div className="pt-2.5 border-t border-zinc-100 space-y-2 shrink-0">
                {/* 🇮🇳 Tonal Secondary India Founder Plan Callout */}
                <Link
                  href="/pricing"
                  onClick={() => setMobileMenuOpen(false)}
                  className="group flex items-center justify-between p-2.5 rounded-2xl bg-zinc-50 hover:bg-zinc-100/90 border border-zinc-200/90 transition-all text-zinc-950 shadow-2xs"
                >
                  <div className="flex items-center gap-2.5 min-w-0">
                    <div className="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-sm shrink-0 border border-zinc-200 shadow-2xs">
                      🇮🇳
                    </div>
                    <div className="min-w-0">
                      <div className="flex items-center gap-1.5 flex-wrap">
                        <span className="text-xs font-bold text-zinc-950 tracking-tight">India Founder Plan</span>
                        <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/60">
                          ₹499<span className="text-emerald-600 font-normal">/mo</span>
                        </span>
                        <span className="text-[9.5px] font-mono line-through text-zinc-400">₹1,999</span>
                      </div>
                      <p className="text-[10.5px] text-zinc-500 font-normal truncate">
                        Special pricing valid till 1 January 2027
                      </p>
                    </div>
                  </div>
                  <div className="shrink-0 pl-2">
                    <span className="inline-flex items-center gap-1 text-[11px] font-bold text-zinc-900 bg-white group-hover:bg-zinc-50 px-2.5 py-1.5 rounded-xl border border-zinc-200 shadow-2xs transition-colors">
                      <span>Claim</span>
                      <ArrowRight className="w-3 h-3 text-zinc-500 group-hover:text-zinc-950 group-hover:translate-x-0.5 transition-all" />
                    </span>
                  </div>
                </Link>

                {isToolsPage ? (
                  <Link
                    href="/"
                    onClick={() => setMobileMenuOpen(false)}
                    className="w-full inline-flex items-center justify-center gap-2 bg-zinc-950 text-white px-5 py-3.5 rounded-xl text-xs font-bold hover:bg-black transition-colors shadow-sm"
                  >
                    <span>Back to Main Site</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                ) : (
                  <a
                    href="https://app.heycora.in/workspace/login?source=mobile_menu"
                    className="w-full group inline-flex items-center justify-center gap-2.5 bg-zinc-950 hover:bg-black text-white px-5 py-3.5 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-md active:scale-[0.99] border border-zinc-900"
                  >
                    {/* Official Google Icon */}
                    <div className="w-4 h-4 flex items-center justify-center shrink-0">
                      <svg className="w-3.5 h-3.5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.36 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.98 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                      </svg>
                    </div>
                    <span>Get started with Google</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-white group-hover:translate-x-0.5 transition-all" />
                  </a>
                )}

                {/* Trust Badges Bar */}
                <div className="flex items-center justify-center gap-2 pt-0.5 text-[10.5px] font-mono text-zinc-500">
                  <span className="flex items-center gap-1.5">
                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" /> Free Forever
                  </span>
                  <span>&bull;</span>
                  <span>No Card Needed</span>
                  <span>&bull;</span>
                  <span>Made for India 🇮🇳</span>
                </div>
              </div>
            )}

          </div>
        )}
      </header>
    </>
  );
}
