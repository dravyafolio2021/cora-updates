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
  Clapperboard
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';
import { MODULE_GLYPH_MAP } from '@/components/features/ModuleAppGlyphs';

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

  const handleMouseEnter = (menuKey: string) => {
    if (timeoutRef.current) clearTimeout(timeoutRef.current);
    setActiveDropdown(menuKey);
    setIsHovered(true);
  };

  const handleMouseLeave = () => {
    timeoutRef.current = setTimeout(() => {
      setActiveDropdown(null);
      setIsHovered(false);
    }, 150);
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

              {/* ── Desktop Navigation: Features, Industries, Resources, Pricing, Company ── */}
              <nav className="hidden lg:flex items-center gap-1 text-xs font-semibold text-zinc-800 font-sans">

                {/* 1. Features Dropdown */}
                <div
                  className="relative py-1.5"
                  onMouseEnter={() => handleMouseEnter('features')}
                >
                  <button
                    type="button"
                    onClick={() => setActiveDropdown(activeDropdown === 'features' ? null : 'features')}
                    className={`px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition-colors group ${
                      activeDropdown === 'features'
                        ? 'text-zinc-950 bg-zinc-100/90 font-bold'
                        : 'text-zinc-800 hover:text-zinc-950 hover:bg-zinc-100/60'
                    }`}
                  >
                    <span>Features</span>
                    <ChevronDown className={`w-3.5 h-3.5 stroke-[2.4] text-zinc-600 group-hover:text-zinc-950 transition-transform duration-200 ${activeDropdown === 'features' ? 'rotate-180 text-zinc-950' : ''}`} />
                  </button>
                </div>

                {/* 2. Industries Dropdown */}
                <div
                  className="relative py-1.5"
                  onMouseEnter={() => handleMouseEnter('industries')}
                >
                  <button
                    type="button"
                    onClick={() => setActiveDropdown(activeDropdown === 'industries' ? null : 'industries')}
                    className={`px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition-colors group ${
                      activeDropdown === 'industries'
                        ? 'text-zinc-950 bg-zinc-100/90 font-bold'
                        : 'text-zinc-800 hover:text-zinc-950 hover:bg-zinc-100/60'
                    }`}
                  >
                    <span>Industries</span>
                    <ChevronDown className={`w-3.5 h-3.5 stroke-[2.4] text-zinc-600 group-hover:text-zinc-950 transition-transform duration-200 ${activeDropdown === 'industries' ? 'rotate-180 text-zinc-950' : ''}`} />
                  </button>
                </div>

                {/* 3. Resources Dropdown */}
                <div
                  className="relative py-1.5"
                  onMouseEnter={() => handleMouseEnter('resources')}
                >
                  <button
                    type="button"
                    onClick={() => setActiveDropdown(activeDropdown === 'resources' ? null : 'resources')}
                    className={`px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition-colors group ${
                      activeDropdown === 'resources'
                        ? 'text-zinc-950 bg-zinc-100/90 font-bold'
                        : 'text-zinc-800 hover:text-zinc-950 hover:bg-zinc-100/60'
                    }`}
                  >
                    <span>Resources</span>
                    <ChevronDown className={`w-3.5 h-3.5 stroke-[2.4] text-zinc-600 group-hover:text-zinc-950 transition-transform duration-200 ${activeDropdown === 'resources' ? 'rotate-180 text-zinc-950' : ''}`} />
                  </button>
                </div>

                {/* 4. Direct Pricing Link */}
                <Link
                  href="/pricing"
                  className="px-3 py-1.5 rounded-lg text-zinc-800 hover:text-zinc-950 hover:bg-zinc-100/60 transition-colors"
                >
                  Pricing
                </Link>

                {/* 5. Company Dropdown */}
                <div
                  className="relative py-1.5"
                  onMouseEnter={() => handleMouseEnter('company')}
                >
                  <button
                    type="button"
                    onClick={() => setActiveDropdown(activeDropdown === 'company' ? null : 'company')}
                    className={`px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition-colors group ${
                      activeDropdown === 'company'
                        ? 'text-zinc-950 bg-zinc-100/90 font-bold'
                        : 'text-zinc-800 hover:text-zinc-950 hover:bg-zinc-100/60'
                    }`}
                  >
                    <span>Company</span>
                    <ChevronDown className={`w-3.5 h-3.5 stroke-[2.4] text-zinc-600 group-hover:text-zinc-950 transition-transform duration-200 ${activeDropdown === 'company' ? 'rotate-180 text-zinc-950' : ''}`} />
                  </button>

                  {/* ── Minimal Clean Company Dropdown ── */}
                  {activeDropdown === 'company' && (
                    <div
                      className="absolute top-full right-0 mt-2 w-52 rounded-2xl bg-white border border-zinc-200/90 shadow-[0px_12px_32px_rgba(0,0,0,0.08)] p-1.5 z-50 animate-in fade-in slide-in-from-top-1 duration-150"
                      onMouseEnter={() => handleMouseEnter('company')}
                      onMouseLeave={handleMouseLeave}
                    >
                      <Link
                        href="/about"
                        onClick={() => setActiveDropdown(null)}
                        className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/80 transition-colors"
                      >
                        <Sparkles className="w-3.5 h-3.5 text-zinc-500" />
                        <span>About</span>
                      </Link>

                      <Link
                        href="/brand"
                        onClick={() => setActiveDropdown(null)}
                        className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/80 transition-colors"
                      >
                        <Layers className="w-3.5 h-3.5 text-zinc-500" />
                        <span>Brand &amp; Assets</span>
                      </Link>

                      <Link
                        href="/security"
                        onClick={() => setActiveDropdown(null)}
                        className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/80 transition-colors"
                      >
                        <ShieldCheck className="w-3.5 h-3.5 text-zinc-500" />
                        <span>Security</span>
                      </Link>

                      <Link
                        href="/contact"
                        onClick={() => setActiveDropdown(null)}
                        className="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/80 transition-colors"
                      >
                        <MessageSquare className="w-3.5 h-3.5 text-zinc-500" />
                        <span>Contact</span>
                      </Link>

                      <div className="my-1 border-t border-zinc-100" />

                      <Link
                        href="/status"
                        onClick={() => setActiveDropdown(null)}
                        className="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-zinc-500 hover:text-zinc-950 hover:bg-zinc-50 transition-colors"
                      >
                        <span>System Status</span>
                        <span className="flex items-center gap-1.5 text-[11px] font-mono text-emerald-600 font-semibold">
                          <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                          99.98%
                        </span>
                      </Link>
                    </div>
                  )}
                </div>

              </nav>
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
              MEGA MENU DROPDOWN PANEL (FOR FEATURES, INDUSTRIES, RESOURCES, AI)
          ══════════════════════════════════════════════════════════════════ */}
          {activeDropdown && activeDropdown !== 'company' && (
            <div
              className="hidden lg:block absolute left-4 right-4 sm:left-6 sm:right-6 top-[62px] z-50 pt-2"
              onMouseEnter={() => handleMouseEnter(activeDropdown)}
              onMouseLeave={handleMouseLeave}
            >
              <div className="w-full max-w-[1240px] mx-auto rounded-[28px] bg-white border border-zinc-200/90 shadow-[0px_25px_70px_rgba(0,0,0,0.12)] p-8 sm:p-10 animate-in fade-in slide-in-from-top-3 duration-200">

                {/* ── DROPDOWN: FEATURES (20 BUILT MODULES ACROSS 4 EQUAL PILLARS) ── */}
                {activeDropdown === 'features' && (
                  <div className="space-y-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-start">

                      {/* 1. INTELLIGENCE & AI */}
                      <div className="space-y-1.5">
                        <div className="flex items-center justify-between pb-2 mb-1 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Intelligence &amp; AI
                          </span>
                          <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-md border border-emerald-200/60">
                            FLAGSHIP
                          </span>
                        </div>

                        <Link
                          href="/features/ai-cofounder"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <Bot className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                AI Co-Founder
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Autonomous operations triage &amp; quotes
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/content-ai"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <Sparkles className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                Content AI &amp; GEO
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              3-Act viral scripts &amp; answer density
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/rag-mcp"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <BrainCircuit className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                RAG Memory MCP
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Semantic memory &amp; IDE connection
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/voice-to-scope"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <Zap className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                Voice-to-Scope
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Audio briefs to structured contracts
                            </p>
                          </div>
                        </Link>
                      </div>

                      {/* 2. SALES & PIPELINE */}
                      <div className="space-y-1.5">
                        <div className="flex items-center justify-between pb-2 mb-1 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Sales &amp; Pipeline
                          </span>
                          <span className="text-[9px] font-mono font-bold text-zinc-600 bg-zinc-100 px-1.5 py-0.5 rounded-md">
                            GROWTH
                          </span>
                        </div>

                        <Link
                          href="/features/lead-crm"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <Kanban className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                Kanban Lead CRM
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Visual deal stages &amp; WhatsApp follow-up
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/canvas-builder"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <LayoutTemplate className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                Funnel Builder
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              High-converting landing pages &amp; reels
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/form-builder"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <FormInput className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                Visual Forms
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Dynamic briefs &amp; call-time slot intake
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/review-portal"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <Star className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                5★ Review Portal
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Smart sentiment routing &amp; Google sync
                            </p>
                          </div>
                        </Link>
                      </div>

                      {/* 3. OPERATIONS & LEGAL */}
                      <div className="space-y-1.5">
                        <div className="flex items-center justify-between pb-2 mb-1 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Operations &amp; Legal
                          </span>
                          <span className="text-[9px] font-mono font-bold text-zinc-600 bg-zinc-100 px-1.5 py-0.5 rounded-md">
                            LEGAL TECH
                          </span>
                        </div>

                        <Link
                          href="/features/esign-vault"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <FileText className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                SHA-256 E-Signs
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              5-Step legally binding digital contracts
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/crew-dispatch"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <Send className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                Crew Dispatch
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Automated call sheets &amp; conflict matrix
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/master-calendar"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <Calendar className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                Master Calendar
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Multi-location shoot scheduling &amp; iCal
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/task-board"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <CheckSquare className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                Task Board
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Post-production milestones &amp; proofing
                            </p>
                          </div>
                        </Link>
                      </div>

                      {/* 4. FINANCE & ASSETS */}
                      <div className="space-y-1.5">
                        <div className="flex items-center justify-between pb-2 mb-1 border-b border-zinc-100">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            Finance &amp; Assets
                          </span>
                          <span className="text-[9px] font-mono font-bold text-zinc-600 bg-zinc-100 px-1.5 py-0.5 rounded-md">
                            INDIA GST
                          </span>
                        </div>

                        <Link
                          href="/features/gst-invoicing"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <Receipt className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                18% GST Invoicing
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Automated CGST/SGST splitting &amp; SAC
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/asset-gear"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <Camera className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                Gear &amp; Inventory
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Equipment registry &amp; shoot reservations
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/media-hub"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <Film className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                Media Hub &amp; RAW
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              Workspace library &amp; client 4K galleries
                            </p>
                          </div>
                        </Link>

                        <Link
                          href="/features/rbac-system"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-2 rounded-xl hover:bg-zinc-100/70 border border-transparent hover:border-zinc-200/60 transition-all group"
                        >
                          <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                            <ShieldCheck className="w-4 h-4 stroke-[1.9]" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center justify-between">
                              <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                                Multi-Tenant RBAC
                              </span>
                              <ArrowRight className="w-3 h-3 text-zinc-400 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" />
                            </div>
                            <p className="text-[11px] text-zinc-500 line-clamp-1 font-normal group-hover:text-zinc-700 transition-colors">
                              30-Point security &amp; capability matrix
                            </p>
                          </div>
                        </Link>
                      </div>

                    </div>

                    {/* ── Sleek Monochromatic Bottom Bar ── */}
                    <div className="-mx-8 -mb-8 sm:-mx-10 sm:-mb-10 mt-8 px-8 sm:px-10 py-4 bg-zinc-50/90 rounded-b-[28px] border-t border-zinc-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                      <div className="flex items-center gap-3 text-xs text-zinc-600">
                        <span className="flex items-center gap-1.5 font-bold text-zinc-950">
                          <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                          20 Live Modules
                        </span>
                        <span className="text-zinc-300">•</span>
                        <span className="hidden md:inline">Single-plugin architecture with 100% MySQL tenant isolation</span>
                        <span className="text-zinc-300 hidden md:inline">•</span>
                        <Link href="/docs" onClick={() => setActiveDropdown(null)} className="font-semibold text-zinc-900 hover:text-black underline underline-offset-4 decoration-zinc-300">
                          API Specs &amp; Docs →
                        </Link>
                      </div>

                      <div className="flex items-center gap-3">
                        <Link
                          href="/demo"
                          onClick={() => setActiveDropdown(null)}
                          className="text-xs font-semibold text-zinc-600 hover:text-zinc-950 transition-colors"
                        >
                          Interactive Demo
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

                {/* ── DROPDOWN: INDUSTRIES (4-COLUMN CLEAN MINIMALIST ARCHITECTURE) ── */}
                {activeDropdown === 'industries' && (
                  <div className="space-y-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 items-start">

                      {/* Column 1: Studio & Production */}
                      <div className="space-y-3.5">
                        <span className="text-[11px] font-bold font-mono tracking-wider text-zinc-400 uppercase block pb-1 border-b border-zinc-100">
                          Studio &amp; Production
                        </span>
                        <div className="space-y-2.5">
                          <Link
                            href="/use-cases#commercial-studios"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Camera className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Photography Studios
                            </span>
                          </Link>

                          <Link
                            href="/use-cases#film-production"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Clapperboard className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Film &amp; Video Production
                            </span>
                          </Link>

                          <Link
                            href="/use-cases#real-estate-media"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Building2 className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Real Estate Media
                            </span>
                          </Link>

                          <Link
                            href="/use-cases#stage-rentals"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Film className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Stage &amp; Gear Rentals
                            </span>
                          </Link>
                        </div>
                      </div>

                      {/* Column 2: Design & Agencies */}
                      <div className="space-y-3.5">
                        <span className="text-[11px] font-bold font-mono tracking-wider text-zinc-400 uppercase block pb-1 border-b border-zinc-100">
                          Design &amp; Agencies
                        </span>
                        <div className="space-y-2.5">
                          <Link
                            href="/use-cases#creative-agencies"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Cpu className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Creative Agencies
                            </span>
                          </Link>

                          <Link
                            href="/use-cases#interior-design"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <LayoutTemplate className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Architecture &amp; Design
                            </span>
                          </Link>

                          <Link
                            href="/use-cases#solo-creators"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Sparkles className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Creators &amp; Talent
                            </span>
                          </Link>

                          <Link
                            href="/use-cases#fashion-editorial"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Layers className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Fashion &amp; Editorial
                            </span>
                          </Link>
                        </div>
                      </div>

                      {/* Column 3: Professional Services */}
                      <div className="space-y-3.5">
                        <span className="text-[11px] font-bold font-mono tracking-wider text-zinc-400 uppercase block pb-1 border-b border-zinc-100">
                          Professional Services
                        </span>
                        <div className="space-y-2.5">
                          <Link
                            href="/use-cases#commercial-studios"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Scale className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Legal &amp; Law Practices
                            </span>
                          </Link>

                          <Link
                            href="/use-cases#creative-agencies"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Briefcase className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Consulting &amp; Advisory
                            </span>
                          </Link>

                          <Link
                            href="/use-cases#creative-agencies"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Receipt className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Finance &amp; Accounting
                            </span>
                          </Link>

                          <Link
                            href="/use-cases#creative-agencies"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <ShieldCheck className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Clinics &amp; Wellness
                            </span>
                          </Link>
                        </div>
                      </div>

                      {/* Column 4: Scale & Deployment */}
                      <div className="space-y-3.5">
                        <span className="text-[11px] font-bold font-mono tracking-wider text-zinc-400 uppercase block pb-1 border-b border-zinc-100">
                          Scale &amp; Deployment
                        </span>
                        <div className="space-y-2.5">
                          <Link
                            href="/use-cases#solo-creators"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <User className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Solo Practice (1 Person)
                            </span>
                          </Link>

                          <Link
                            href="/use-cases#commercial-studios"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Building2 className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Boutique Studio (2–10)
                            </span>
                          </Link>

                          <Link
                            href="/use-cases#creative-agencies"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Layers className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Enterprise Firm (10+)
                            </span>
                          </Link>

                          <Link
                            href="/features/custom-workspace"
                            onClick={() => setActiveDropdown(null)}
                            className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-50 transition-all group"
                          >
                            <div className="w-8 h-8 rounded-xl bg-zinc-100/90 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60 group-hover:bg-zinc-950 group-hover:text-white transition-all shadow-2xs">
                              <Zap className="w-4 h-4 stroke-[1.8]" />
                            </div>
                            <span className="text-[13px] font-semibold text-zinc-900 group-hover:text-black tracking-tight">
                              Custom Workspace Mode
                            </span>
                          </Link>
                        </div>
                      </div>

                    </div>

                    {/* Bottom Bar */}
                    <div className="-mx-8 -mb-8 sm:-mx-10 sm:-mb-10 mt-6 px-8 sm:px-10 py-3.5 bg-zinc-50/90 rounded-b-[28px] border-t border-zinc-100 flex items-center justify-between">
                      <span className="text-xs text-zinc-500 font-medium">
                        Pre-seeded legal contracts, GST tax math &amp; workflows for 12+ verticals
                      </span>
                      <Link
                        href="/use-cases"
                        onClick={() => setActiveDropdown(null)}
                        className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-900 hover:text-black transition-colors group/link"
                      >
                        <span>Explore all industry solutions</span>
                        <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-black group-hover/link:translate-x-0.5 transition-all" />
                      </Link>
                    </div>
                  </div>
                )}

                {/* ── DROPDOWN: RESOURCES (CLEAN MONOCHROMATIC UI PREVIEW CARDS) ── */}
                {activeDropdown === 'resources' && (
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">

                    {/* Card 1: Free Micro-Tools & Calculators */}
                    <Link
                      href="/tools"
                      onClick={() => setActiveDropdown(null)}
                      className="group rounded-2xl bg-white border border-zinc-200/90 hover:border-zinc-950 p-6 flex flex-col justify-between hover:shadow-md transition-all duration-200"
                    >
                      <div>
                        <div className="flex items-center justify-between mb-3">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            CALCULATORS &amp; AI
                          </span>
                          <span className="text-[9px] font-mono font-semibold text-zinc-600 bg-zinc-100 px-2 py-0.5 rounded border border-zinc-200">
                            ZERO LOGIN
                          </span>
                        </div>
                        <h3 className="text-base font-bold text-zinc-950 tracking-tight mb-1 group-hover:text-black">
                          Free Micro-Tools
                        </h3>
                        <p className="text-zinc-500 text-xs font-normal leading-relaxed mb-5">
                          Instant GST tax breakdown, real estate MLS copywriter &amp; client embed builders.
                        </p>

                        {/* UI Preview Block: Mini GST Breakdown Widget */}
                        <div className="rounded-xl bg-zinc-50 border border-zinc-200/80 p-3 font-mono text-[11px] space-y-1.5 group-hover:bg-zinc-100/60 transition-colors">
                          <div className="flex items-center justify-between text-zinc-400 text-[10px]">
                            <span>TAX CALCULATION</span>
                            <span className="font-semibold text-zinc-700">SAC 998381</span>
                          </div>
                          <div className="flex items-center justify-between text-zinc-900 font-semibold">
                            <span>Shoot Base:</span>
                            <span>₹1,50,000</span>
                          </div>
                          <div className="flex items-center justify-between text-zinc-500 text-[10px] pt-1.5 border-t border-zinc-200/70">
                            <span>+ 18% GST (CGST/SGST):</span>
                            <span className="font-bold text-zinc-900">₹27,000</span>
                          </div>
                        </div>
                      </div>

                      <div className="pt-5 mt-4 border-t border-zinc-100 flex items-center justify-between">
                        <span className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-900 group-hover:text-black">
                          <span>Browse all micro-tools</span>
                          <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-black group-hover:translate-x-0.5 transition-transform" />
                        </span>
                      </div>
                    </Link>

                    {/* Card 2: Documentation & Developer Hub */}
                    <Link
                      href="/docs"
                      onClick={() => setActiveDropdown(null)}
                      className="group rounded-2xl bg-white border border-zinc-200/90 hover:border-zinc-950 p-6 flex flex-col justify-between hover:shadow-md transition-all duration-200"
                    >
                      <div>
                        <div className="flex items-center justify-between mb-3">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            DEVELOPER &amp; REST APIS
                          </span>
                          <span className="text-[9px] font-mono font-semibold text-zinc-600 bg-zinc-100 px-2 py-0.5 rounded border border-zinc-200">
                            25 SPECS
                          </span>
                        </div>
                        <h3 className="text-base font-bold text-zinc-950 tracking-tight mb-1 group-hover:text-black">
                          Documentation
                        </h3>
                        <p className="text-zinc-500 text-xs font-normal leading-relaxed mb-5">
                          25 architecture guides, REST API specs, RBAC matrix &amp; developer quickstarts.
                        </p>

                        {/* UI Preview Block: Mini Terminal Code Block */}
                        <div className="rounded-xl bg-zinc-950 text-zinc-300 p-3 font-mono text-[11px] space-y-1.5 border border-zinc-800 shadow-2xs">
                          <div className="flex items-center justify-between text-[10px] text-zinc-500 pb-1 border-b border-zinc-800">
                            <span className="flex items-center gap-1.5">
                              <span className="w-1.5 h-1.5 rounded-full bg-zinc-600" />
                              <span>REST v1</span>
                            </span>
                            <span className="text-zinc-400">200 OK</span>
                          </div>
                          <div className="text-zinc-300 truncate text-[11px]">
                            <span className="text-zinc-500">GET</span> /api/v1/workspaces/active
                          </div>
                          <div className="text-[10px] text-zinc-500 flex items-center justify-between pt-0.5">
                            <span>tenant_id: &quot;studio_re&quot;</span>
                            <span className="text-zinc-400">12ms</span>
                          </div>
                        </div>
                      </div>

                      <div className="pt-5 mt-4 border-t border-zinc-100 flex items-center justify-between">
                        <span className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-900 group-hover:text-black">
                          <span>Browse 25 docs &amp; specs</span>
                          <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-black group-hover:translate-x-0.5 transition-transform" />
                        </span>
                      </div>
                    </Link>

                    {/* Card 3: Articles & Market Comparisons */}
                    <Link
                      href="/articles"
                      onClick={() => setActiveDropdown(null)}
                      className="group rounded-2xl bg-white border border-zinc-200/90 hover:border-zinc-950 p-6 flex flex-col justify-between hover:shadow-md transition-all duration-200"
                    >
                      <div>
                        <div className="flex items-center justify-between mb-3">
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                            BENCHMARKS &amp; GUIDES
                          </span>
                          <span className="text-[9px] font-mono font-semibold text-zinc-600 bg-zinc-100 px-2 py-0.5 rounded border border-zinc-200">
                            24 ARTICLES
                          </span>
                        </div>
                        <h3 className="text-base font-bold text-zinc-950 tracking-tight mb-1 group-hover:text-black">
                          Articles &amp; Compare
                        </h3>
                        <p className="text-zinc-500 text-xs font-normal leading-relaxed mb-5">
                          Side-by-side benchmarks vs HoneyBook, Studio Ninja, HubSpot &amp; legacy stacks.
                        </p>

                        {/* UI Preview Block: Mini Benchmark Comparison Matrix */}
                        <div className="rounded-xl bg-zinc-50 border border-zinc-200/80 p-3 text-[11px] space-y-1.5 group-hover:bg-zinc-100/60 transition-colors">
                          <div className="flex items-center justify-between text-zinc-400 font-mono text-[10px]">
                            <span>CAPABILITY</span>
                            <span>CORA VS LEGACY</span>
                          </div>
                          <div className="flex items-center justify-between font-medium text-zinc-900">
                            <span>18% GST Tax Engine</span>
                            <span className="text-[11px] font-mono font-bold text-zinc-900">Native ✓</span>
                          </div>
                          <div className="flex items-center justify-between text-zinc-500 text-[10px] pt-1.5 border-t border-zinc-200/70">
                            <span>SaaS Subscriptions:</span>
                            <span className="font-semibold text-zinc-900 font-mono">1 App vs 5</span>
                          </div>
                        </div>
                      </div>

                      <div className="pt-5 mt-4 border-t border-zinc-100 flex items-center justify-between">
                        <span className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-900 group-hover:text-black">
                          <span>Browse all 24 guides</span>
                          <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-black group-hover:translate-x-0.5 transition-transform" />
                        </span>
                      </div>
                    </Link>

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
                <a
                  href="https://app.heycora.in/workspace/login?source=mobile_header"
                  className="text-xs font-semibold text-zinc-900 bg-zinc-100 hover:bg-zinc-200 px-3.5 py-1.5 rounded-full transition-colors"
                >
                  Get started
                </a>
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
                  {/* AI Co-Founder Full-Width Flagship USP Hero Card */}
                  <Link
                    href="/ai-agent"
                    onClick={() => setMobileMenuOpen(false)}
                    className="p-4 rounded-2xl bg-zinc-950 text-white flex items-center justify-between shadow-md transition-all mb-4 border border-zinc-800 group"
                  >
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-amber-400 shrink-0">
                        <Sparkles className="w-5 h-5" />
                      </div>
                      <div>
                        <div className="flex items-center gap-2">
                          <span className="font-bold text-sm text-white">AI Co-Founder</span>
                          <span className="text-[9px] font-mono font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            FLAGSHIP USP
                          </span>
                        </div>
                        <p className="text-xs text-zinc-400 font-normal mt-0.5 line-clamp-1">
                          Autonomous operations triage &amp; RAG memory
                        </p>
                      </div>
                    </div>
                    <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:translate-x-1 group-hover:text-white transition-transform shrink-0" />
                  </Link>

                  {/* 1. Features */}
                  <button
                    type="button"
                    onClick={() => setActiveMobileSubmenu('features')}
                    className="w-full py-3.5 flex items-center justify-between text-left hover:text-black transition-colors"
                  >
                    <span>Features</span>
                    <ChevronRight className="w-4 h-4 text-zinc-400" />
                  </button>

                  {/* 2. Industries */}
                  <button
                    type="button"
                    onClick={() => setActiveMobileSubmenu('industries')}
                    className="w-full py-3.5 flex items-center justify-between text-left hover:text-black transition-colors"
                  >
                    <span>Industries</span>
                    <ChevronRight className="w-4 h-4 text-zinc-400" />
                  </button>

                  {/* 3. Resources */}
                  <button
                    type="button"
                    onClick={() => setActiveMobileSubmenu('resources')}
                    className="w-full py-3.5 flex items-center justify-between text-left hover:text-black transition-colors"
                  >
                    <span>Resources</span>
                    <ChevronRight className="w-4 h-4 text-zinc-400" />
                  </button>

                  {/* 4. Pricing (Direct Link) */}
                  <Link
                    href="/pricing"
                    onClick={() => setMobileMenuOpen(false)}
                    className="w-full py-3.5 flex items-center justify-between hover:text-black transition-colors block"
                  >
                    <span>Pricing</span>
                  </Link>

                  {/* 5. Company */}
                  <button
                    type="button"
                    onClick={() => setActiveMobileSubmenu('company')}
                    className="w-full py-3.5 flex items-center justify-between text-left hover:text-black transition-colors"
                  >
                    <span>Company</span>
                    <ChevronRight className="w-4 h-4 text-zinc-400" />
                  </button>
                </div>
              )}

              {/* Level 2 Submenu: Features (4 Balanced Pillars) */}
              {activeMobileSubmenu === 'features' && (
                <div className="space-y-5 animate-in fade-in slide-in-from-right-3 duration-150">

                  {/* Pillar 1: Intelligence & AI */}
                  <div className="space-y-1">
                    <div className="flex items-center justify-between pb-1 mb-1 border-b border-zinc-100">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                        Intelligence &amp; AI
                      </span>
                      <span className="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-md">
                        FLAGSHIP
                      </span>
                    </div>
                    <Link href="/features/ai-cofounder" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><Bot className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">AI Co-Founder</div><div className="text-[11px] text-zinc-500">Autonomous triage &amp; quotes</div></div>
                    </Link>
                    <Link href="/features/content-ai" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><Sparkles className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">Content AI &amp; GEO</div><div className="text-[11px] text-zinc-500">3-Act viral scripts &amp; SEO</div></div>
                    </Link>
                    <Link href="/features/rag-mcp" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><BrainCircuit className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">RAG Memory MCP</div><div className="text-[11px] text-zinc-500">Semantic memory &amp; IDE connection</div></div>
                    </Link>
                    <Link href="/features/voice-to-scope" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><Zap className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">Voice-to-Scope</div><div className="text-[11px] text-zinc-500">Audio briefs to structured contracts</div></div>
                    </Link>
                  </div>

                  {/* Pillar 2: Sales & Pipeline */}
                  <div className="space-y-1">
                    <div className="flex items-center justify-between pb-1 mb-1 border-b border-zinc-100">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                        Sales &amp; Pipeline
                      </span>
                      <span className="text-[9px] font-mono font-bold text-zinc-600 bg-zinc-100 px-1.5 py-0.5 rounded-md">
                        GROWTH
                      </span>
                    </div>
                    <Link href="/features/lead-crm" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><Kanban className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">Kanban Lead CRM</div><div className="text-[11px] text-zinc-500">Visual deal stages &amp; WhatsApp</div></div>
                    </Link>
                    <Link href="/features/canvas-builder" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><LayoutTemplate className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">Funnel Builder</div><div className="text-[11px] text-zinc-500">Landing pages &amp; reels</div></div>
                    </Link>
                    <Link href="/features/form-builder" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><FormInput className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">Visual Forms</div><div className="text-[11px] text-zinc-500">Dynamic briefs &amp; call-time intake</div></div>
                    </Link>
                    <Link href="/features/review-portal" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><Star className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">5★ Review Portal</div><div className="text-[11px] text-zinc-500">Sentiment routing &amp; Google sync</div></div>
                    </Link>
                  </div>

                  {/* Pillar 3: Operations & Legal */}
                  <div className="space-y-1">
                    <div className="flex items-center justify-between pb-1 mb-1 border-b border-zinc-100">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                        Operations &amp; Legal
                      </span>
                      <span className="text-[9px] font-mono font-bold text-zinc-600 bg-zinc-100 px-1.5 py-0.5 rounded-md">
                        LEGAL TECH
                      </span>
                    </div>
                    <Link href="/features/esign-vault" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><FileText className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">SHA-256 E-Signs</div><div className="text-[11px] text-zinc-500">5-Step legally binding contracts</div></div>
                    </Link>
                    <Link href="/features/crew-dispatch" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><Send className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">Crew Dispatch</div><div className="text-[11px] text-zinc-500">Automated call sheets &amp; conflicts</div></div>
                    </Link>
                    <Link href="/features/master-calendar" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><Calendar className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">Master Calendar</div><div className="text-[11px] text-zinc-500">Multi-location shoot scheduling</div></div>
                    </Link>
                    <Link href="/features/task-board" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><CheckSquare className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">Task Board</div><div className="text-[11px] text-zinc-500">Post-production &amp; proofing</div></div>
                    </Link>
                  </div>

                  {/* Pillar 4: Finance & Assets */}
                  <div className="space-y-1">
                    <div className="flex items-center justify-between pb-1 mb-1 border-b border-zinc-100">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                        Finance &amp; Assets
                      </span>
                      <span className="text-[9px] font-mono font-bold text-zinc-600 bg-zinc-100 px-1.5 py-0.5 rounded-md">
                        INDIA GST
                      </span>
                    </div>
                    <Link href="/features/gst-invoicing" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><Receipt className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">18% GST Invoicing</div><div className="text-[11px] text-zinc-500">CGST/SGST auto-splitting &amp; SAC</div></div>
                    </Link>
                    <Link href="/features/asset-gear" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><Camera className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">Gear &amp; Inventory</div><div className="text-[11px] text-zinc-500">Equipment registry &amp; checkouts</div></div>
                    </Link>
                    <Link href="/features/media-hub" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><Film className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">Media Hub &amp; RAW</div><div className="text-[11px] text-zinc-500">Library &amp; 4K client galleries</div></div>
                    </Link>
                    <Link href="/features/rbac-system" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-50 transition-colors">
                      <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0"><ShieldCheck className="w-4 h-4" /></div>
                      <div><div className="text-xs font-bold text-zinc-950">Multi-Tenant RBAC</div><div className="text-[11px] text-zinc-500">30-Point security matrix</div></div>
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

              {/* Level 2 Submenu: Industries (8 Core Professional Service Business Types) */}
              {activeMobileSubmenu === 'industries' && (
                <div className="space-y-3 animate-in fade-in slide-in-from-right-3 duration-150">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-2">
                    PROFESSIONAL SERVICE WORKSPACES
                  </span>
                  <Link href="/use-cases#commercial-studios" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60"><Camera className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-xs font-semibold text-zinc-950">Photography &amp; Film Studios</div><div className="text-[11px] text-zinc-500">Shoots, 4K RAW vaults, crew call sheets &amp; proofing</div></div>
                  </Link>
                  <Link href="/use-cases#creative-agencies" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60"><Cpu className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-xs font-semibold text-zinc-950">Creative &amp; Marketing Agencies</div><div className="text-[11px] text-zinc-500">Monthly retainers, campaign sprints &amp; review portals</div></div>
                  </Link>
                  <Link href="/use-cases#real-estate-media" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60"><Building2 className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-xs font-semibold text-zinc-950">Real Estate Agencies &amp; Media</div><div className="text-[11px] text-zinc-500">Listings catalog, property shoots &amp; MLS AI copy</div></div>
                  </Link>
                  <Link href="/use-cases#interior-design" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60"><LayoutTemplate className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-xs font-semibold text-zinc-950">Architecture &amp; Design Studios</div><div className="text-[11px] text-zinc-500">Milestone sign-offs, 3D render vaults &amp; stage billing</div></div>
                  </Link>
                  <Link href="/use-cases#creative-agencies" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60"><Briefcase className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-xs font-semibold text-zinc-950">Consulting &amp; Advisory Firms</div><div className="text-[11px] text-zinc-500">Diagnostic scopes, fixed fees &amp; executive retainers</div></div>
                  </Link>
                  <Link href="/use-cases#commercial-studios" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60"><Scale className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-xs font-semibold text-zinc-950">Legal &amp; Law Practices</div><div className="text-[11px] text-zinc-500">SHA-256 e-signs, client retainers &amp; IT Act compliance</div></div>
                  </Link>
                  <Link href="/use-cases#creative-agencies" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60"><Receipt className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-xs font-semibold text-zinc-950">Finance &amp; Accounting Firms</div><div className="text-[11px] text-zinc-500">Tax compliance, 18% GST audit vaults &amp; retainers</div></div>
                  </Link>
                  <Link href="/use-cases#interior-design" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50 transition-colors">
                    <div className="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0 border border-zinc-200/60"><Film className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-xs font-semibold text-zinc-950">Stage &amp; Equipment Rentals</div><div className="text-[11px] text-zinc-500">Hourly floor bookings, serialized gear loans &amp; deposits</div></div>
                  </Link>
                  <div className="pt-2">
                    <Link href="/use-cases" onClick={() => setMobileMenuOpen(false)} className="text-xs font-semibold text-zinc-950 flex items-center gap-1.5 hover:text-zinc-600 transition-colors">
                      <span>Explore all 8 industry schemas</span>
                      <ArrowRight className="w-3.5 h-3.5" />
                    </Link>
                  </div>
                </div>
              )}

              {/* Level 2 Submenu: Resources */}
              {activeMobileSubmenu === 'resources' && (
                <div className="space-y-3 animate-in fade-in slide-in-from-right-3 duration-150">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-2">
                    KNOWLEDGE, GUIDES &amp; BENCHMARKS
                  </span>
                  <Link href="/articles" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center border border-zinc-200/70"><Sparkles className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-sm font-semibold text-zinc-950">Articles &amp; Compare</div><div className="text-xs text-zinc-500">24 guides, playbooks &amp; benchmarks</div></div>
                  </Link>
                  <Link href="/docs" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center border border-zinc-200/70"><FileText className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-sm font-semibold text-zinc-950">Documentation &amp; APIs</div><div className="text-xs text-zinc-500">25 architecture guides &amp; REST specs</div></div>
                  </Link>
                  <Link href="/tools" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center border border-zinc-200/70"><Receipt className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-sm font-semibold text-zinc-950">Free Tools &amp; Calculators</div><div className="text-xs text-zinc-500">18% GST calculator &amp; AI writer</div></div>
                  </Link>
                  <Link href="/compare" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center border border-zinc-200/70"><BarChart2 className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-sm font-semibold text-zinc-950">Head-to-Head Comparisons</div><div className="text-xs text-zinc-500">vs HoneyBook, Studio Ninja, HubSpot</div></div>
                  </Link>
                  <Link href="/blog/" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center border border-zinc-200/70"><Layers className="w-4 h-4 stroke-[1.8]" /></div>
                    <div><div className="text-sm font-semibold text-zinc-950">WordPress &amp; Agency Blog</div><div className="text-xs text-zinc-500">Publishing &amp; stack alternatives</div></div>
                  </Link>
                </div>
              )}

              {/* Level 2 Submenu: Company */}
              {activeMobileSubmenu === 'company' && (
                <div className="space-y-3 animate-in fade-in slide-in-from-right-3 duration-150">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-2">
                    COMPANY &amp; ECOSYSTEM
                  </span>
                  <Link href="/about" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">About &amp; Story</div><div className="text-xs text-zinc-500">Mission and leadership</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/brand" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Brand &amp; Design Assets</div><div className="text-xs text-zinc-500">Logos, tokens &amp; SVG kit</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/security" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Security &amp; Trust</div><div className="text-xs text-zinc-500">AES-256 &amp; IT Act 2000</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/sla" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">99.95% SLA</div><div className="text-xs text-zinc-500">Uptime guarantee &amp; credits</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/status" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">System Status</div><div className="text-xs text-zinc-500">99.98% 90-day health</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/changelog" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Changelog</div><div className="text-xs text-zinc-500">v2.4 Latest release notes</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/contact" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Advisory Desk &amp; Support</div><div className="text-xs text-zinc-500">Direct contact &amp; inquiry</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                </div>
              )}

            </div>

            {/* 3. Bottom Anchored CTAs (Exact Clay style) */}
            {!activeMobileSubmenu && (
              <div className="pt-4 border-t border-zinc-100 space-y-2.5 shrink-0">
                <a
                  href="mailto:dravya.bansal@heycora.in?subject=Inquiry%20from%20Website"
                  className="w-full inline-flex items-center justify-center bg-[#F4F4F5] text-zinc-900 px-6 py-3.5 rounded-2xl text-sm font-semibold hover:bg-zinc-200 transition-colors shadow-2xs"
                >
                  Chat with Founder
                </a>

                <a
                  href="https://app.heycora.in/workspace/login?source=mobile_menu"
                  className="w-full inline-flex items-center justify-center gap-2 bg-black text-white px-6 py-3.5 rounded-2xl text-sm font-bold hover:bg-zinc-800 transition-colors shadow-sm"
                >
                  <span>Get started for Free</span>
                  <ArrowRight className="w-4 h-4 text-zinc-400" />
                </a>
              </div>
            )}

          </div>
        )}
      </header>
    </>
  );
}
