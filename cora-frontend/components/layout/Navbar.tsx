'use client';

import React, { useState, useRef, useEffect } from 'react';
import Link from 'next/link';
import Image from 'next/image';
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
  Code
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export function Navbar() {
  const [activeDropdown, setActiveDropdown] = useState<string | null>(null);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [activeMobileSubmenu, setActiveMobileSubmenu] = useState<string | null>(null);
  const [isScrolled, setIsScrolled] = useState(false);
  const [isHovered, setIsHovered] = useState(false);
  const navRef = useRef<HTMLElement>(null);
  const timeoutRef = useRef<NodeJS.Timeout | null>(null);

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

                {/* 5. Direct Get A Demo Link */}
                <Link
                  href="/demo"
                  className="px-3 py-1.5 rounded-lg text-zinc-800 hover:text-zinc-950 hover:bg-zinc-100/60 transition-colors font-semibold"
                >
                  Get A Demo
                </Link>

                {/* 6. Company Small Dropdown */}
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

                  {/* ── Compact Standard Company Dropdown Card ── */}
                  {activeDropdown === 'company' && (
                    <div
                      className="absolute top-full left-0 mt-1 w-60 rounded-2xl bg-white border border-zinc-200/90 shadow-[0px_15px_35px_rgba(0,0,0,0.08)] p-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150"
                      onMouseEnter={() => handleMouseEnter('company')}
                      onMouseLeave={handleMouseLeave}
                    >
                      <Link
                        href="/about"
                        onClick={() => setActiveDropdown(null)}
                        className="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-zinc-50 text-xs font-semibold text-zinc-800 hover:text-zinc-950 transition-colors"
                      >
                        <Sparkles className="w-3.5 h-3.5 text-zinc-500" />
                        <span>About Our Mission</span>
                      </Link>

                      <Link
                        href="/brand"
                        onClick={() => setActiveDropdown(null)}
                        className="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-zinc-50 text-xs font-semibold text-zinc-800 hover:text-zinc-950 transition-colors"
                      >
                        <Layers className="w-3.5 h-3.5 text-zinc-500" />
                        <span>Brand &amp; Design Assets</span>
                      </Link>

                      <Link
                        href="/about#founder"
                        onClick={() => setActiveDropdown(null)}
                        className="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-zinc-50 text-xs font-semibold text-zinc-800 hover:text-zinc-950 transition-colors"
                      >
                        <User className="w-3.5 h-3.5 text-zinc-500" />
                        <span>Founder: Dravya Bansal</span>
                      </Link>

                      <Link
                        href="/contact"
                        onClick={() => setActiveDropdown(null)}
                        className="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-zinc-50 text-xs font-semibold text-zinc-800 hover:text-zinc-950 transition-colors"
                      >
                        <MessageSquare className="w-3.5 h-3.5 text-zinc-500" />
                        <span>Advisory Desk &amp; Contact</span>
                      </Link>

                      <Link
                        href="/security"
                        onClick={() => setActiveDropdown(null)}
                        className="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-zinc-50 text-xs font-semibold text-zinc-800 hover:text-zinc-950 transition-colors border-t border-zinc-100 mt-1 pt-2"
                      >
                        <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" />
                        <span>Security &amp; Standards</span>
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
                
                {/* ── DROPDOWN: FEATURES (20 BUILT MODULES ACROSS 4 CORE PILLARS) ── */}
                {activeDropdown === 'features' && (
                  <div className="grid grid-cols-12 gap-6 items-stretch">
                    
                    {/* 1. INTELLIGENCE & AI */}
                    <div className="col-span-2 space-y-3.5">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                        INTELLIGENCE &amp; AI
                      </span>
                      <div className="space-y-1.5">
                        <Link
                          href="/features#ai-cofounder"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200/60 group-hover:scale-105 transition-transform">
                            <Bot className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">AI Co-Founder</span>
                        </Link>

                        <Link
                          href="/features#content-ai"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200/60 group-hover:scale-105 transition-transform">
                            <Sparkles className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">Content AI &amp; SEO</span>
                        </Link>

                        <Link
                          href="/features#rag-mcp"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-200/60 group-hover:scale-105 transition-transform">
                            <BrainCircuit className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">RAG Memory MCP</span>
                        </Link>

                        <Link
                          href="/ai-agent"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-200/60 group-hover:scale-105 transition-transform">
                            <Zap className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">Voice-to-Scope</span>
                        </Link>
                      </div>
                    </div>

                    {/* 2. SALES & CLIENT CRM */}
                    <div className="col-span-2 space-y-3.5">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                        SALES &amp; CRM
                      </span>
                      <div className="space-y-1.5">
                        <Link
                          href="/features#lead-crm"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-200/60 group-hover:scale-105 transition-transform">
                            <Kanban className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">Kanban Lead CRM</span>
                        </Link>

                        <Link
                          href="/features#canvas-builder"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center shrink-0 border border-violet-200/60 group-hover:scale-105 transition-transform">
                            <LayoutTemplate className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">Funnel Builder</span>
                        </Link>

                        <Link
                          href="/features#form-builder"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 border border-teal-200/60 group-hover:scale-105 transition-transform">
                            <FormInput className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">Visual Forms</span>
                        </Link>

                        <Link
                          href="/features#review-portal"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200/60 group-hover:scale-105 transition-transform">
                            <Star className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">5★ Review Portal</span>
                        </Link>
                      </div>
                    </div>

                    {/* 3. OPERATIONS & LOGISTICS */}
                    <div className="col-span-2 space-y-3.5">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                        OPERATIONS &amp; LEGAL
                      </span>
                      <div className="space-y-1.5">
                        <Link
                          href="/features#esign-vault"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-200/60 group-hover:scale-105 transition-transform">
                            <FileText className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">SHA-256 E-Signs</span>
                        </Link>

                        <Link
                          href="/features#crew-dispatch"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-200/60 group-hover:scale-105 transition-transform">
                            <Send className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">Crew Dispatch</span>
                        </Link>

                        <Link
                          href="/features#master-calendar"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-200/60 group-hover:scale-105 transition-transform">
                            <Calendar className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">Master Calendar</span>
                        </Link>

                        <Link
                          href="/features#task-board"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200/60 group-hover:scale-105 transition-transform">
                            <CheckSquare className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">Task Board</span>
                        </Link>
                      </div>
                    </div>

                    {/* 4. FINANCE & INFRASTRUCTURE */}
                    <div className="col-span-2 space-y-3.5">
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                        FINANCE &amp; ASSETS
                      </span>
                      <div className="space-y-1.5">
                        <Link
                          href="/features#gst-invoicing"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200/60 group-hover:scale-105 transition-transform">
                            <Receipt className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">18% GST Invoicing</span>
                        </Link>

                        <Link
                          href="/features#asset-gear"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-200/60 group-hover:scale-105 transition-transform">
                            <Camera className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">Gear &amp; Inventory</span>
                        </Link>

                        <Link
                          href="/features#media-hub"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 border border-orange-200/60 group-hover:scale-105 transition-transform">
                            <HardDrive className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">Media Hub &amp; RAW</span>
                        </Link>

                        <Link
                          href="/features#rbac-system"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-center gap-2 p-1.5 rounded-xl hover:bg-zinc-50 transition-colors group"
                        >
                          <div className="w-6 h-6 rounded-lg bg-zinc-100 text-zinc-800 flex items-center justify-center shrink-0 border border-zinc-200/80 group-hover:scale-105 transition-transform">
                            <Users2 className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-semibold text-zinc-800 group-hover:text-zinc-950 truncate">Multi-Tenant RBAC</span>
                        </Link>
                      </div>
                    </div>

                    {/* 5. RIGHT HIGHLIGHT CARD (CLAY-STYLE GRADIENT SHOWCASE) */}
                    <div className="col-span-4">
                      <Link
                        href="/features"
                        onClick={() => setActiveDropdown(null)}
                        className="h-full rounded-2xl bg-gradient-to-br from-[#0EA5E9] via-[#0284C7] to-[#0369A1] text-white p-6 relative overflow-hidden flex flex-col justify-between shadow-lg group block hover:brightness-105 transition-all"
                      >
                        {/* Glassmorphic Emblem Container */}
                        <div className="bg-white/20 backdrop-blur-md rounded-2xl p-2.5 border border-white/30 flex items-center justify-between shadow-inner">
                          <div className="flex items-center gap-1.5">
                            <span className="w-6 h-6 rounded-full bg-amber-500/30 text-amber-200 flex items-center justify-center text-[10px] font-mono font-bold border border-amber-300/40">AI</span>
                            <span className="w-6 h-6 rounded-full bg-emerald-500/30 text-emerald-200 flex items-center justify-center text-[10px] font-mono font-bold border border-emerald-300/40">CRM</span>
                            <span className="w-6 h-6 rounded-full bg-sky-500/30 text-sky-200 flex items-center justify-center text-[10px] font-mono font-bold border border-sky-300/40">GST</span>
                          </div>
                          <span className="text-[10px] font-mono font-bold text-sky-200 bg-white/10 px-2 py-0.5 rounded-full border border-white/20">
                            20 BUILT • 8 ROADMAP
                          </span>
                        </div>

                        <div className="space-y-1.5 pt-3">
                          <span className="text-[10px] font-mono font-bold text-sky-200 uppercase tracking-wider block">
                            CORA STUDIO OS
                          </span>
                          <h4 className="font-display text-sm font-bold text-white leading-snug">
                            The complete 20-in-1 autonomous operating system for creative studios.
                          </h4>
                        </div>

                        <div className="pt-2">
                          <span className="inline-flex items-center gap-1.5 text-xs font-bold text-white group-hover:translate-x-1 transition-transform">
                            <span>Explore all 20 modules &amp; roadmap</span>
                            <ArrowRight className="w-3.5 h-3.5" />
                          </span>
                        </div>
                      </Link>
                    </div>

                  </div>
                )}

                {/* ── DROPDOWN: INDUSTRIES (5 HIGH-CONVICTION CREATIVE VERTICALS) ── */}
                {activeDropdown === 'industries' && (
                  <div className="grid grid-cols-12 gap-8 items-stretch">
                    
                    {/* Left 8 Cols: 5 Focused Vertical Cards */}
                    <div className="col-span-8 space-y-4">
                      <div className="flex items-center justify-between">
                        <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                          TARGET PRODUCTION WORKSPACES
                        </span>
                        <span className="text-[10px] font-mono font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/80">
                          PRE-SEEDED SCHEMAS
                        </span>
                      </div>

                      <div className="grid grid-cols-2 gap-3">
                        
                        {/* 1. Commercial Studios */}
                        <Link
                          href="/use-cases#commercial-studios"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-3 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/80 transition-all group"
                        >
                          <div className="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 border border-orange-200/60 group-hover:scale-105 transition-transform">
                            <Camera className="w-4 h-4" />
                          </div>
                          <div>
                            <div className="text-xs font-bold text-zinc-950 group-hover:text-black">Commercial Photo &amp; Film</div>
                            <div className="text-[11px] text-zinc-500 font-normal mt-0.5 leading-snug">4K RAW vaults, gear inventory &amp; model releases</div>
                          </div>
                        </Link>

                        {/* 2. Real Estate */}
                        <Link
                          href="/use-cases#real-estate-media"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-3 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/80 transition-all group"
                        >
                          <div className="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-200/60 group-hover:scale-105 transition-transform">
                            <Building2 className="w-4 h-4" />
                          </div>
                          <div>
                            <div className="text-xs font-bold text-zinc-950 group-hover:text-black">Real Estate &amp; Brokerages</div>
                            <div className="text-[11px] text-zinc-500 font-normal mt-0.5 leading-snug">Property catalog, shoot tours &amp; MLS AI copy</div>
                          </div>
                        </Link>

                        {/* 3. Wedding & Events */}
                        <Link
                          href="/use-cases#wedding-events"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-3 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/80 transition-all group"
                        >
                          <div className="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-200/60 group-hover:scale-105 transition-transform">
                            <Sparkles className="w-4 h-4" />
                          </div>
                          <div>
                            <div className="text-xs font-bold text-zinc-950 group-hover:text-black">Wedding &amp; Event Production</div>
                            <div className="text-[11px] text-zinc-500 font-normal mt-0.5 leading-snug">Multi-day call sheets, crew dispatch &amp; GST</div>
                          </div>
                        </Link>

                        {/* 4. Interior Design */}
                        <Link
                          href="/use-cases#interior-design"
                          onClick={() => setActiveDropdown(null)}
                          className="flex items-start gap-3 p-3 rounded-2xl hover:bg-zinc-50 border border-transparent hover:border-zinc-200/80 transition-all group"
                        >
                          <div className="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-200/60 group-hover:scale-105 transition-transform">
                            <LayoutTemplate className="w-4 h-4" />
                          </div>
                          <div>
                            <div className="text-xs font-bold text-zinc-950 group-hover:text-black">Interior &amp; Architecture</div>
                            <div className="text-[11px] text-zinc-500 font-normal mt-0.5 leading-snug">Milestone sign-offs &amp; visual asset registries</div>
                          </div>
                        </Link>

                        {/* 5. Creative Agencies (Span 2) */}
                        <Link
                          href="/use-cases#creative-agencies"
                          onClick={() => setActiveDropdown(null)}
                          className="col-span-2 flex items-start gap-3 p-3 rounded-2xl bg-zinc-50/70 hover:bg-zinc-100/70 border border-zinc-200/60 transition-all group"
                        >
                          <div className="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200/60 group-hover:scale-105 transition-transform">
                            <Cpu className="w-4 h-4" />
                          </div>
                          <div className="flex-1 flex items-center justify-between">
                            <div>
                              <div className="text-xs font-bold text-zinc-950 group-hover:text-black">Creative &amp; Growth Agencies</div>
                              <div className="text-[11px] text-zinc-500 font-normal mt-0.5">Content AI studio, Kanban deal pipelines &amp; recurring retainer billing</div>
                            </div>
                            <span className="text-xs font-bold text-zinc-900 group-hover:translate-x-0.5 transition-transform shrink-0 pr-2">
                              Explore →
                            </span>
                          </div>
                        </Link>

                      </div>
                    </div>

                    {/* Right 4 Cols: Industry Benchmark Highlight Card */}
                    <div className="col-span-4">
                      <div className="h-full rounded-2xl bg-gradient-to-br from-[#0F172A] via-[#1E293B] to-[#0A0D12] text-white p-6 relative overflow-hidden flex flex-col justify-between border border-zinc-800 shadow-lg">
                        <div className="space-y-2">
                          <span className="text-[10px] font-mono font-bold text-emerald-400 uppercase tracking-wider block">
                            TAILORED INDUSTRY SCHEMAS
                          </span>
                          <h4 className="font-display text-sm font-bold text-white leading-snug">
                            Every workspace automatically seeds industry-specific contract templates, rate cards, and HSN tax codes.
                          </h4>
                        </div>

                        <div className="pt-4 border-t border-zinc-800/80">
                          <Link
                            href="/use-cases"
                            onClick={() => setActiveDropdown(null)}
                            className="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400 hover:text-emerald-300 transition-colors"
                          >
                            <span>Compare all industry workflows</span>
                            <ArrowRight className="w-3.5 h-3.5" />
                          </Link>
                        </div>
                      </div>
                    </div>

                  </div>
                )}

                {/* ── DROPDOWN: RESOURCES (3 ARTISTIC VISUAL CARDS: FREE TOOLS, GUIDES, ARTICLES/COMPARE) ── */}
                {activeDropdown === 'resources' && (
                  <div className="grid grid-cols-3 gap-6 items-stretch">
                    
                    {/* Card 1: Free Public Tools (Solid Soft Lavender / Periwinkle) */}
                    <Link
                      href="/tools"
                      onClick={() => setActiveDropdown(null)}
                      className="group relative rounded-[28px] bg-[#EEF2FF] hover:bg-[#E0E7FF] border-0 p-6 sm:p-7 flex flex-col justify-between overflow-hidden shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-300 min-h-[270px]"
                    >
                      <div className="space-y-1.5 z-10 max-w-[190px]">
                        <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight leading-tight">
                          Free Tools
                        </h3>
                        <p className="text-zinc-600 text-xs font-normal leading-relaxed">
                          Instant calculators &amp; AI generators with zero login
                        </p>
                      </div>

                      {/* Relatable 3D Tool & Calculator Artwork */}
                      <div className="absolute -right-3 -bottom-3 w-[165px] h-[165px] rounded-2xl overflow-hidden shadow-md border-2 border-white/80 pointer-events-none transition-transform duration-300 group-hover:scale-105 group-hover:-rotate-2">
                        <Image
                          src="/images/cora_nav_tools_relatable.png"
                          alt="Free Tools 3D Calculator & Tax Generator"
                          fill
                          className="object-cover"
                        />
                      </div>

                      <div className="pt-6 z-10">
                        <span className="inline-flex items-center gap-1.5 text-xs sm:text-[13px] font-bold text-indigo-950 group-hover:translate-x-1 transition-transform">
                          <span>Browse all micro-tools</span>
                          <ArrowRight className="w-3.5 h-3.5" />
                        </span>
                      </div>
                    </Link>

                    {/* Card 2: Guides & Playbooks (Solid Soft Sky Blue) */}
                    <Link
                      href="/features#esign-vault"
                      onClick={() => setActiveDropdown(null)}
                      className="group relative rounded-[28px] bg-[#E0F2FE] hover:bg-[#BAE6FD] border-0 p-6 sm:p-7 flex flex-col justify-between overflow-hidden shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-300 min-h-[270px]"
                    >
                      <div className="space-y-1.5 z-10 max-w-[190px]">
                        <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight leading-tight">
                          Guides &amp; Playbooks
                        </h3>
                        <p className="text-zinc-600 text-xs font-normal leading-relaxed">
                          Studio SOPs, rate cards &amp; legal contract templates
                        </p>
                      </div>

                      {/* Relatable 3D Playbook & Legal Document Artwork */}
                      <div className="absolute -right-3 -bottom-3 w-[165px] h-[165px] rounded-2xl overflow-hidden shadow-md border-2 border-white/80 pointer-events-none transition-transform duration-300 group-hover:scale-105 group-hover:rotate-2">
                        <Image
                          src="/images/cora_nav_guides_relatable.png"
                          alt="Guides & Playbooks Contract Vault"
                          fill
                          className="object-cover"
                        />
                      </div>

                      <div className="pt-6 z-10">
                        <span className="inline-flex items-center gap-1.5 text-xs sm:text-[13px] font-bold text-sky-950 group-hover:translate-x-1 transition-transform">
                          <span>Explore playbooks</span>
                          <ArrowRight className="w-3.5 h-3.5" />
                        </span>
                      </div>
                    </Link>

                    {/* Card 3: Articles & Market Comparisons (Solid Soft Mint Green) */}
                    <Link
                      href="/compare"
                      onClick={() => setActiveDropdown(null)}
                      className="group relative rounded-[28px] bg-[#DCFCE7] hover:bg-[#D1FAE5] border-0 p-6 sm:p-7 flex flex-col justify-between overflow-hidden shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-300 min-h-[270px]"
                    >
                      <div className="space-y-1.5 z-10 max-w-[190px]">
                        <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight leading-tight">
                          Articles &amp; Compare
                        </h3>
                        <p className="text-zinc-600 text-xs font-normal leading-relaxed">
                          Side-by-side benchmarks vs legacy market stacks
                        </p>
                      </div>

                      {/* Relatable 3D Benchmark & Analytics Comparison Artwork */}
                      <div className="absolute -right-3 -bottom-3 w-[165px] h-[165px] rounded-2xl overflow-hidden shadow-md border-2 border-white/80 pointer-events-none transition-transform duration-300 group-hover:scale-105 group-hover:-rotate-2">
                        <Image
                          src="/images/cora_nav_compare_relatable.png"
                          alt="Articles & Compare Benchmarks Matrix"
                          fill
                          className="object-cover"
                        />
                      </div>

                      <div className="pt-6 z-10">
                        <span className="inline-flex items-center gap-1.5 text-xs sm:text-[13px] font-bold text-emerald-950 group-hover:translate-x-1 transition-transform">
                          <span>Browse all 8 comparisons</span>
                          <ArrowRight className="w-3.5 h-3.5" />
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
                  <span>Back</span>
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
            <div className="flex-1 overflow-y-auto py-6">
              
              {/* Level 1: Main Category List (Clay style) */}
              {!activeMobileSubmenu && (
                <div className="space-y-1 text-base font-semibold text-zinc-900">
                  {/* AI Co-Founders & Get A Demo Featured Row on Mobile */}
                  <div className="grid grid-cols-2 gap-2 mb-3">
                    <Link
                      href="/ai-agent"
                      onClick={() => setMobileMenuOpen(false)}
                      className="p-3 rounded-2xl bg-zinc-950 text-white flex flex-col justify-between shadow-sm transition-all min-h-[72px]"
                    >
                      <div className="flex items-center justify-between">
                        <Sparkles className="w-4 h-4 text-amber-400" />
                        <span className="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-emerald-500 text-white">
                          AI
                        </span>
                      </div>
                      <span className="font-bold text-xs">AI Co-Founder</span>
                    </Link>

                    <Link
                      href="/demo"
                      onClick={() => setMobileMenuOpen(false)}
                      className="p-3 rounded-2xl bg-white border border-zinc-200 text-zinc-950 flex flex-col justify-between shadow-2xs transition-all min-h-[72px] hover:bg-zinc-50"
                    >
                      <div className="flex items-center justify-between">
                        <ArrowRight className="w-4 h-4 text-zinc-500" />
                        <span className="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-zinc-100 text-zinc-700">
                          LIVE
                        </span>
                      </div>
                      <span className="font-bold text-xs">Get A Demo</span>
                    </Link>
                  </div>

                  <button
                    type="button"
                    onClick={() => setActiveMobileSubmenu('product')}
                    className="w-full py-4 flex items-center justify-between text-left hover:text-black transition-colors"
                  >
                    <span>Product</span>
                    <ChevronRight className="w-4 h-4 text-zinc-400" />
                  </button>

                  <button
                    type="button"
                    onClick={() => setActiveMobileSubmenu('use-cases')}
                    className="w-full py-4 flex items-center justify-between text-left hover:text-black transition-colors"
                  >
                    <span>Use Cases</span>
                    <ChevronRight className="w-4 h-4 text-zinc-400" />
                  </button>

                  <button
                    type="button"
                    onClick={() => setActiveMobileSubmenu('compare')}
                    className="w-full py-4 flex items-center justify-between text-left hover:text-black transition-colors"
                  >
                    <span>Compare</span>
                    <ChevronRight className="w-4 h-4 text-zinc-400" />
                  </button>

                  <button
                    type="button"
                    onClick={() => setActiveMobileSubmenu('integrations')}
                    className="w-full py-4 flex items-center justify-between text-left hover:text-black transition-colors"
                  >
                    <span>Integrations</span>
                    <ChevronRight className="w-4 h-4 text-zinc-400" />
                  </button>

                  <button
                    type="button"
                    onClick={() => setActiveMobileSubmenu('resources')}
                    className="w-full py-4 flex items-center justify-between text-left hover:text-black transition-colors"
                  >
                    <span>Resources</span>
                    <ChevronRight className="w-4 h-4 text-zinc-400" />
                  </button>

                  <button
                    type="button"
                    onClick={() => setActiveMobileSubmenu('company')}
                    className="w-full py-4 flex items-center justify-between text-left hover:text-black transition-colors"
                  >
                    <span>Company</span>
                    <ChevronRight className="w-4 h-4 text-zinc-400" />
                  </button>

                  <Link
                    href="/pricing"
                    onClick={() => setMobileMenuOpen(false)}
                    className="w-full py-4 flex items-center justify-between hover:text-black transition-colors block"
                  >
                    <span>Pricing</span>
                  </Link>
                </div>
              )}

              {/* Level 2 Submenu: Product */}
              {activeMobileSubmenu === 'product' && (
                <div className="space-y-3 animate-in fade-in slide-in-from-right-3 duration-150">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-2">
                    CORE WORKSPACE MODULES
                  </span>
                  <Link href="/ai-agent" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/70"><Sparkles className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">AI Agent Workspace</div><div className="text-xs text-zinc-500">Autonomous co-founder &amp; memory</div></div>
                  </Link>
                  <Link href="/features#gst-invoicing" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/70"><Receipt className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">18% GST Invoicing</div><div className="text-xs text-zinc-500">CGST/SGST automated splitting</div></div>
                  </Link>
                  <Link href="/features#esign-vault" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-200/70"><FileText className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">SHA-256 E-Sign Vault</div><div className="text-xs text-zinc-500">IT Act 2000 digital contracts</div></div>
                  </Link>
                  <Link href="/features#crm-leads" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-200/70"><Kanban className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">Kanban Lead CRM</div><div className="text-xs text-zinc-500">Milestones &amp; WhatsApp dispatch</div></div>
                  </Link>
                  <Link href="/features#crew-dispatch" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-200/70"><Send className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">Crew &amp; Gear Dispatch</div><div className="text-xs text-zinc-500">Automated shoot call sheets</div></div>
                  </Link>
                  <div className="pt-2">
                    <Link href="/features" onClick={() => setMobileMenuOpen(false)} className="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
                      <span>View all 20 modules &amp; roadmap</span>
                      <ArrowRight className="w-3.5 h-3.5" />
                    </Link>
                  </div>
                </div>
              )}

              {/* Level 2 Submenu: Use Cases */}
              {activeMobileSubmenu === 'use-cases' && (
                <div className="space-y-3 animate-in fade-in slide-in-from-right-3 duration-150">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-2">
                    INDUSTRY WORKSPACES
                  </span>
                  <Link href="/use-cases#commercial-studios" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-200/70"><Camera className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">Commercial Photo &amp; Film</div><div className="text-xs text-zinc-500">Gear checkouts &amp; 4K RAW proofing</div></div>
                  </Link>
                  <Link href="/use-cases#real-estate-media" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200/70"><Building2 className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">Real Estate &amp; Brokerages</div><div className="text-xs text-zinc-500">Property listings &amp; MLS AI copy</div></div>
                  </Link>
                  <Link href="/use-cases#wedding-events" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200/70"><Sparkles className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">Wedding &amp; Event Production</div><div className="text-xs text-zinc-500">Multi-day call sheets &amp; advance GST</div></div>
                  </Link>
                  <Link href="/use-cases#interior-design" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-200/70"><LayoutTemplate className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">Interior &amp; Architecture</div><div className="text-xs text-zinc-500">Milestone stage billing &amp; contracts</div></div>
                  </Link>
                  <Link href="/use-cases#creative-agencies" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/70"><Cpu className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">Creative &amp; Growth Agencies</div><div className="text-xs text-zinc-500">Monthly retainers &amp; review portals</div></div>
                  </Link>
                </div>
              )}

              {/* Level 2 Submenu: Compare */}
              {activeMobileSubmenu === 'compare' && (
                <div className="space-y-3 animate-in fade-in slide-in-from-right-3 duration-150">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-2">
                    HEAD-TO-HEAD BENCHMARKS
                  </span>
                  <Link href="/compare/cora-vs-honeybook" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Cora vs HoneyBook</div><div className="text-xs text-zinc-500">Save ₹65k/yr + Native GST</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/compare/cora-vs-studio-ninja" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Cora vs Studio Ninja</div><div className="text-xs text-zinc-500">Modern UI + Autonomous AI Agents</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/compare/cora-vs-hubspot" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Cora vs HubSpot CRM</div><div className="text-xs text-zinc-500">Save ₹4.5L/yr without seat penalties</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/compare/cora-vs-docusign" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Cora vs DocuSign</div><div className="text-xs text-zinc-500">Unlimited SHA-256 digital envelopes</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <div className="pt-2">
                    <Link href="/compare" onClick={() => setMobileMenuOpen(false)} className="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
                      <span>View all 8 comparison benchmarks</span>
                      <ArrowRight className="w-3.5 h-3.5" />
                    </Link>
                  </div>
                </div>
              )}

              {/* Level 2 Submenu: Integrations */}
              {activeMobileSubmenu === 'integrations' && (
                <div className="space-y-3 animate-in fade-in slide-in-from-right-3 duration-150">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-2">
                    WEBSITE CONNECTORS &amp; EMBEDS
                  </span>
                  <Link href="/integrations/framer" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Framer Connector</div><div className="text-xs text-zinc-500">Client portals, e-signs &amp; 18% GST</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/integrations/webflow" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Webflow Connector</div><div className="text-xs text-zinc-500">Form webhooks &amp; WhatsApp dispatch</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/integrations/wordpress" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">WordPress Connector</div><div className="text-xs text-zinc-500">Replace 6 heavy plugins with 1 script</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/integrations/shopify" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Shopify Custom Quotes</div><div className="text-xs text-zinc-500">High-ticket production &amp; 50/50 split</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/tools/embed-builder" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl bg-zinc-50 border border-zinc-200/80">
                    <div className="w-9 h-9 rounded-xl bg-zinc-950 text-white flex items-center justify-center"><Code className="w-4 h-4 text-emerald-400" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">1-Click Embed Builder</div><div className="text-xs text-zinc-500">Generate script/iframe snippets</div></div>
                  </Link>
                  <div className="pt-2">
                    <Link href="/integrations" onClick={() => setMobileMenuOpen(false)} className="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
                      <span>View all integration guides</span>
                      <ArrowRight className="w-3.5 h-3.5" />
                    </Link>
                  </div>
                </div>
              )}

              {/* Level 2 Submenu: Resources */}
              {activeMobileSubmenu === 'resources' && (
                <div className="space-y-3 animate-in fade-in slide-in-from-right-3 duration-150">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-2">
                    FREE TOOLS &amp; PLAYBOOKS
                  </span>
                  <Link href="/tools/gst-calculator" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/70"><Receipt className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">18% GST Calculator</div><div className="text-xs text-zinc-500">Live reverse tax &amp; split math</div></div>
                  </Link>
                  <Link href="/tools/listing-ai" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/70"><Sparkles className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">Real Estate AI Writer</div><div className="text-xs text-zinc-500">Viral Instagram &amp; MLS descriptions</div></div>
                  </Link>
                  <Link href="/tools" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-zinc-50">
                    <div className="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-200/70"><Layers className="w-4 h-4" /></div>
                    <div><div className="text-sm font-bold text-zinc-950">All Free Tools</div><div className="text-xs text-zinc-500">Rate card creators &amp; calculators</div></div>
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
                    <div><div className="text-sm font-bold text-zinc-950">About &amp; Story</div><div className="text-xs text-zinc-500">Mission and Dravya Bansal</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/brand" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Brand &amp; Design Assets</div><div className="text-xs text-zinc-500">Logos, tokens &amp; SVG kit</div></div>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                  <Link href="/security" onClick={() => setMobileMenuOpen(false)} className="flex items-center justify-between p-2.5 rounded-2xl hover:bg-zinc-50">
                    <div><div className="text-sm font-bold text-zinc-950">Security &amp; SOC-2</div><div className="text-xs text-zinc-500">AES-256 &amp; IT Act 2000</div></div>
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
                    <div><div className="text-sm font-bold text-zinc-950">Advisory Desk</div><div className="text-xs text-zinc-500">Direct contact &amp; support</div></div>
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
