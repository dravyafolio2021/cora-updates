'use client';

import React, { useState, useEffect, useMemo } from 'react';
import Link from 'next/link';
import { 
  Search, 
  X, 
  ChevronRight, 
  ArrowLeft, 
  ArrowRight, 
  BookOpen, 
  Sparkles, 
  ShieldCheck, 
  Users, 
  Receipt, 
  Code, 
  Compass, 
  Layers, 
  ExternalLink,
  MessageSquare,
  Command,
  Clock,
  Calendar,
  CheckCircle2,
  SlidersHorizontal,
  ChevronDown
} from 'lucide-react';
import { DocArticle, DOCS_DATA, DOC_CATEGORIES } from '@/lib/docs-data';
import { MarkdownRenderer } from './MarkdownRenderer';

interface DocsClientProps {
  currentArticle: DocArticle;
}

const CATEGORY_ICONS: Record<string, React.ElementType> = {
  overview: Compass,
  intelligence: Sparkles,
  sales: Users,
  operations: ShieldCheck,
  finance: Receipt,
  developers: Code,
};

export function DocsClient({ currentArticle }: DocsClientProps) {
  const [searchOpen, setSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [mobileSidebarOpen, setMobileSidebarOpen] = useState(false);
  const [activeHeading, setActiveHeading] = useState<string>('');

  // Keyboard shortcut listener for Command+K or /
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        setSearchOpen((prev) => !prev);
      }
      if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes((e.target as HTMLElement).tagName)) {
        e.preventDefault();
        setSearchOpen(true);
      }
      if (e.key === 'Escape' && searchOpen) {
        setSearchOpen(false);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [searchOpen]);

  // Scroll spy for Right TOC active heading
  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            setActiveHeading(entry.target.id);
          }
        });
      },
      { rootMargin: '-80px 0% -60% 0%' }
    );

    currentArticle.toc.forEach(({ id }) => {
      const el = document.getElementById(id);
      if (el) observer.observe(el);
    });

    return () => observer.disconnect();
  }, [currentArticle]);

  // Fuzzy search filter
  const searchResults = useMemo(() => {
    if (!searchQuery.trim()) return [];
    const q = searchQuery.toLowerCase();
    return DOCS_DATA.filter(
      (doc) =>
        doc.title.toLowerCase().includes(q) ||
        doc.description.toLowerCase().includes(q) ||
        doc.categoryLabel.toLowerCase().includes(q) ||
        doc.content.toLowerCase().includes(q)
    ).slice(0, 8);
  }, [searchQuery]);

  // Find Previous and Next articles for pagination
  const currentIndex = DOCS_DATA.findIndex((d) => d.slug === currentArticle.slug);
  const prevArticle = currentIndex > 0 ? DOCS_DATA[currentIndex - 1] : null;
  const nextArticle = currentIndex < DOCS_DATA.length - 1 ? DOCS_DATA[currentIndex + 1] : null;

  return (
    <div className="min-h-screen bg-white text-zinc-900 flex flex-col justify-between pt-24 sm:pt-28">
      
      {/* ── DOCS SUB-HEADER / BREADCRUMB BAR (BELOW GLOBAL NAVBAR) ── */}
      <div className="border-b border-zinc-200/80 bg-zinc-50/70 backdrop-blur-xs px-4 sm:px-8 py-3">
        <div className="max-w-[1440px] mx-auto flex items-center justify-between gap-4">
          
          {/* Left: Mobile Trigger + Breadcrumbs */}
          <div className="flex items-center gap-2.5 min-w-0">
            <button
              type="button"
              onClick={() => setMobileSidebarOpen(true)}
              className="lg:hidden p-1.5 rounded-lg text-zinc-600 hover:text-zinc-950 hover:bg-zinc-200/60 transition-colors"
              aria-label="Open Sidebar"
            >
              <SlidersHorizontal className="w-4 h-4" />
            </button>

            <Link href="/docs" className="text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors">
              Docs
            </Link>

            <span className="text-zinc-300">/</span>

            <span className="text-xs font-medium text-zinc-500 hidden sm:inline">
              {currentArticle.categoryLabel}
            </span>

            <span className="text-zinc-300 hidden sm:inline">/</span>

            <span className="text-xs font-semibold text-zinc-950 truncate max-w-[240px]">
              {currentArticle.shortTitle}
            </span>
          </div>

          {/* Right: Search Bar Trigger + Workspace Action */}
          <div className="flex items-center gap-3 shrink-0">
            <button
              type="button"
              onClick={() => setSearchOpen(true)}
              className="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white hover:bg-zinc-100 text-zinc-600 hover:text-zinc-950 text-xs font-medium border border-zinc-200 transition-all cursor-pointer shadow-2xs"
            >
              <Search className="w-3.5 h-3.5 text-zinc-400" />
              <span className="hidden sm:inline">Search docs...</span>
              <kbd className="hidden sm:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-mono font-semibold text-zinc-500 bg-zinc-100 rounded-md border border-zinc-200">
                ⌘K
              </kbd>
            </button>

            <a
              href="https://app.heycora.in/workspace/login?source=docs_header"
              className="hidden md:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
            >
              <span>Open Studio OS</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
            </a>
          </div>

        </div>
      </div>

      {/* ── 3-COLUMN MASTER CONTAINER ── */}
      <div className="w-full max-w-[1440px] mx-auto flex-1 flex items-start">
        
        {/* ══════════════════════════════════════════════════════════════════
            1. LEFT NAVIGATION SIDEBAR (CATEGORIES & ARTICLES)
        ══════════════════════════════════════════════════════════════════ */}
        <aside className="hidden lg:block w-[280px] shrink-0 sticky top-28 h-[calc(100vh-112px)] overflow-y-auto p-6 border-r border-zinc-100 space-y-6">
          
          <div className="space-y-5">
            {DOC_CATEGORIES.map((cat) => {
              const IconComp = CATEGORY_ICONS[cat.id] || BookOpen;
              const hasActive = cat.articles.some((a) => a.slug === currentArticle.slug);

              return (
                <div key={cat.id} className="space-y-1.5">
                  <div className="flex items-center gap-2 text-[11px] font-mono font-bold text-zinc-400 uppercase tracking-wider px-2 py-1">
                    <IconComp className="w-3.5 h-3.5 text-zinc-500" />
                    <span>{cat.label}</span>
                  </div>

                  <div className="space-y-0.5">
                    {cat.articles.map((art) => {
                      const isActive = art.slug === currentArticle.slug;
                      return (
                        <Link
                          key={art.slug}
                          href={`/docs/${art.slug}`}
                          className={`flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs transition-colors group ${
                            isActive
                              ? 'bg-zinc-950 text-white font-semibold shadow-2xs'
                              : 'text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100/70 font-medium'
                          }`}
                        >
                          <span className="truncate">{art.shortTitle}</span>
                          {art.badge && !isActive && (
                            <span className="text-[9px] font-mono font-bold text-zinc-500 bg-zinc-100 px-1.5 py-0.5 rounded-md">
                              {art.badge}
                            </span>
                          )}
                        </Link>
                      );
                    })}
                  </div>
                </div>
              );
            })}
          </div>

          <div className="pt-4 border-t border-zinc-100">
            <Link
              href="/changelog"
              className="flex items-center justify-between text-xs font-semibold text-zinc-600 hover:text-zinc-950 p-2 rounded-lg hover:bg-zinc-50 transition-colors"
            >
              <span>Platform Changelog</span>
              <span className="text-[10px] font-mono bg-emerald-50 text-emerald-700 font-bold px-1.5 py-0.5 rounded-full border border-emerald-200">
                v3.2.46
              </span>
            </Link>
          </div>
        </aside>

        {/* ══════════════════════════════════════════════════════════════════
            2. CENTER MAIN ARTICLE PROSE VIEW
        ══════════════════════════════════════════════════════════════════ */}
        <main className="flex-1 min-w-0 px-6 sm:px-12 py-10 lg:py-12 max-w-[840px] mx-auto">
          
          {/* Article Header & Metadata */}
          <div className="space-y-3 pb-8 border-b border-zinc-100">
            
            <div className="flex items-center flex-wrap gap-2">
              <span className="px-2.5 py-0.5 rounded-full bg-zinc-100 text-zinc-800 text-[11px] font-semibold border border-zinc-200/80">
                {currentArticle.categoryLabel}
              </span>
              {currentArticle.badge && (
                <span className="px-2.5 py-0.5 rounded-full bg-zinc-950 text-white text-[10px] font-mono font-bold">
                  {currentArticle.badge}
                </span>
              )}
              <span className="inline-flex items-center gap-1 text-xs text-zinc-400 font-medium">
                <Clock className="w-3.5 h-3.5" />
                <span>{currentArticle.readTime}</span>
              </span>
              <span className="text-zinc-300">•</span>
              <span className="inline-flex items-center gap-1 text-xs text-zinc-400 font-medium">
                <Calendar className="w-3.5 h-3.5" />
                <span>Updated {currentArticle.lastUpdated}</span>
              </span>
            </div>

            <h1 className="font-display text-2xl sm:text-4xl font-bold text-zinc-950 tracking-tight leading-[1.2]">
              {currentArticle.title}
            </h1>

            <p className="text-sm sm:text-base text-zinc-600 leading-relaxed font-normal">
              {currentArticle.description}
            </p>
          </div>

          {/* Rendered Markdown Content */}
          <div className="pt-6">
            <MarkdownRenderer content={currentArticle.content} />
          </div>

          {/* Bottom Pagination Links (Prev / Next Article) */}
          <div className="mt-16 pt-8 border-t border-zinc-200/80 grid grid-cols-1 sm:grid-cols-2 gap-4">
            {prevArticle ? (
              <Link
                href={`/docs/${prevArticle.slug}`}
                className="p-4 rounded-2xl border border-zinc-200 hover:border-zinc-300 bg-white hover:bg-zinc-50/70 transition-all group flex flex-col justify-between shadow-2xs"
              >
                <span className="text-[11px] font-mono font-semibold text-zinc-400 uppercase flex items-center gap-1">
                  <ArrowLeft className="w-3 h-3 group-hover:-translate-x-0.5 transition-transform" />
                  <span>Previous Article</span>
                </span>
                <span className="text-sm font-bold text-zinc-900 group-hover:text-zinc-950 mt-1">
                  {prevArticle.shortTitle}
                </span>
              </Link>
            ) : <div />}

            {nextArticle && (
              <Link
                href={`/docs/${nextArticle.slug}`}
                className="p-4 rounded-2xl border border-zinc-200 hover:border-zinc-300 bg-white hover:bg-zinc-50/70 transition-all group flex flex-col justify-between items-end text-right shadow-2xs"
              >
                <span className="text-[11px] font-mono font-semibold text-zinc-400 uppercase flex items-center gap-1">
                  <span>Next Article</span>
                  <ArrowRight className="w-3 h-3 group-hover:translate-x-0.5 transition-transform" />
                </span>
                <span className="text-sm font-bold text-zinc-900 group-hover:text-zinc-950 mt-1">
                  {nextArticle.shortTitle}
                </span>
              </Link>
            )}
          </div>

          {/* Founder Feedback Callout */}
          <div className="mt-12 p-6 rounded-2xl bg-zinc-50 border border-zinc-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div className="space-y-1">
              <h4 className="text-xs font-bold text-zinc-950">Have questions about this module?</h4>
              <p className="text-xs text-zinc-500 font-normal">Our founding engineering team answers developer inquiries directly.</p>
            </div>
            <a
              href={`mailto:support@heycora.in?subject=Documentation%20Inquiry%20-%20${encodeURIComponent(currentArticle.title)}`}
              className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-colors shrink-0 shadow-2xs"
            >
              <MessageSquare className="w-3.5 h-3.5" />
              <span>Contact Founder</span>
            </a>
          </div>

        </main>

        {/* ══════════════════════════════════════════════════════════════════
            3. RIGHT ON-THIS-PAGE TOC SIDEBAR
        ══════════════════════════════════════════════════════════════════ */}
        <aside className="hidden xl:block w-[240px] shrink-0 sticky top-28 h-[calc(100vh-112px)] overflow-y-auto p-6 space-y-6">
          
          {currentArticle.toc && currentArticle.toc.length > 0 && (
            <div className="space-y-2.5">
              <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                ON THIS PAGE
              </span>
              <nav className="space-y-1">
                {currentArticle.toc.map((item) => {
                  const isActive = activeHeading === item.id;
                  return (
                    <a
                      key={item.id}
                      href={`#${item.id}`}
                      className={`block text-xs py-1 transition-colors leading-snug ${
                        isActive
                          ? 'text-zinc-950 font-semibold pl-2 border-l-2 border-zinc-950'
                          : 'text-zinc-500 hover:text-zinc-900 font-normal pl-2 border-l-2 border-transparent'
                      }`}
                    >
                      {item.title}
                    </a>
                  );
                })}
              </nav>
            </div>
          )}

          <div className="pt-6 border-t border-zinc-100 space-y-3">
            <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
              QUICK RESOURCES
            </span>

            <a
              href="https://app.heycora.in/workspace/login?source=docs_sidebar"
              className="flex items-center justify-between text-xs text-zinc-600 hover:text-zinc-950 transition-colors py-1 group"
            >
              <span>Workspace Portal</span>
              <ExternalLink className="w-3 h-3 text-zinc-400 group-hover:text-zinc-950" />
            </a>

            <Link
              href="/tools/embed-builder"
              className="flex items-center justify-between text-xs text-zinc-600 hover:text-zinc-950 transition-colors py-1 group"
            >
              <span>Embed Builder Tool</span>
              <ExternalLink className="w-3 h-3 text-zinc-400 group-hover:text-zinc-950" />
            </Link>

            <Link
              href="/compare"
              className="flex items-center justify-between text-xs text-zinc-600 hover:text-zinc-950 transition-colors py-1 group"
            >
              <span>Comparison Benchmarks</span>
              <ExternalLink className="w-3 h-3 text-zinc-400 group-hover:text-zinc-950" />
            </Link>
          </div>

          <div className="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200/80 text-[11px] text-emerald-800 space-y-1">
            <div className="flex items-center gap-1.5 font-bold">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" />
              <span>All Systems Live</span>
            </div>
            <p className="text-emerald-700 font-normal">Core API &amp; WebSockets at 99.98% uptime.</p>
          </div>

        </aside>

      </div>

      {/* ══════════════════════════════════════════════════════════════════
          4. MOBILE SLIDE-OUT DRAWER SIDEBAR
      ══════════════════════════════════════════════════════════════════ */}
      {mobileSidebarOpen && (
        <div className="lg:hidden fixed inset-0 z-50 flex">
          <div
            className="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity"
            onClick={() => setMobileSidebarOpen(false)}
          />
          <div className="relative w-full max-w-[300px] bg-white h-full shadow-2xl p-6 overflow-y-auto z-10 flex flex-col justify-between">
            <div className="space-y-6">
              <div className="flex items-center justify-between pb-4 border-b border-zinc-100">
                <span className="font-display font-bold text-base text-zinc-950">Documentation</span>
                <button
                  type="button"
                  onClick={() => setMobileSidebarOpen(false)}
                  className="p-1 text-zinc-400 hover:text-zinc-950"
                >
                  <X className="w-5 h-5" />
                </button>
              </div>

              <div className="space-y-5">
                {DOC_CATEGORIES.map((cat) => (
                  <div key={cat.id} className="space-y-1">
                    <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block px-2">
                      {cat.label}
                    </span>
                    <div className="space-y-0.5">
                      {cat.articles.map((art) => (
                        <Link
                          key={art.slug}
                          href={`/docs/${art.slug}`}
                          onClick={() => setMobileSidebarOpen(false)}
                          className={`block px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors ${
                            art.slug === currentArticle.slug
                              ? 'bg-zinc-950 text-white font-semibold'
                              : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950'
                          }`}
                        >
                          {art.shortTitle}
                        </Link>
                      ))}
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="pt-6 border-t border-zinc-100">
              <a
                href="https://app.heycora.in/workspace/login"
                className="w-full py-2.5 rounded-xl bg-zinc-950 text-white text-xs font-semibold text-center block shadow-2xs"
              >
                Launch Studio OS
              </a>
            </div>
          </div>
        </div>
      )}

      {/* ══════════════════════════════════════════════════════════════════
          5. COMMAND PALETTE MODAL (⌘K FUZZY SEARCH)
      ══════════════════════════════════════════════════════════════════ */}
      {searchOpen && (
        <div className="fixed inset-0 z-50 flex items-start justify-center pt-20 px-4">
          <div
            className="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity"
            onClick={() => setSearchOpen(false)}
          />
          <div className="relative w-full max-w-[560px] bg-white rounded-2xl shadow-2xl border border-zinc-200 overflow-hidden z-10 animate-in fade-in zoom-in-95 duration-150">
            
            {/* Input Header */}
            <div className="flex items-center gap-3 px-4 py-3 border-b border-zinc-100">
              <Search className="w-4 h-4 text-zinc-400 shrink-0" />
              <input
                type="text"
                autoFocus
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search all 23 documentation guides and APIs..."
                className="w-full text-sm font-normal text-zinc-950 placeholder-zinc-400 bg-transparent focus:outline-hidden"
              />
              <button
                type="button"
                onClick={() => setSearchOpen(false)}
                className="p-1 rounded-md text-zinc-400 hover:text-zinc-950 hover:bg-zinc-100 transition-colors text-xs"
              >
                <kbd className="px-1.5 py-0.5 text-[10px] font-mono font-semibold bg-zinc-100 rounded-md">ESC</kbd>
              </button>
            </div>

            {/* Results Body */}
            <div className="max-h-[380px] overflow-y-auto p-2">
              {searchQuery.trim() === '' ? (
                <div className="p-6 text-center text-xs text-zinc-400 space-y-1">
                  <p className="font-semibold text-zinc-600">Quick Navigation</p>
                  <p>Type keywords like <span className="font-mono text-zinc-700">"GST"</span>, <span className="font-mono text-zinc-700">"E-Sign"</span>, <span className="font-mono text-zinc-700">"MCP"</span>, or <span className="font-mono text-zinc-700">"REST API"</span></p>
                </div>
              ) : searchResults.length > 0 ? (
                <div className="space-y-1">
                  {searchResults.map((result) => (
                    <Link
                      key={result.slug}
                      href={`/docs/${result.slug}`}
                      onClick={() => setSearchOpen(false)}
                      className="block p-3 rounded-xl hover:bg-zinc-100/80 transition-colors group cursor-pointer"
                    >
                      <div className="flex items-center justify-between">
                        <span className="text-xs font-bold text-zinc-950 group-hover:text-black">
                          {result.title}
                        </span>
                        <span className="text-[10px] font-mono text-zinc-400 bg-zinc-100 px-2 py-0.5 rounded-md">
                          {result.categoryLabel}
                        </span>
                      </div>
                      <p className="text-[11px] text-zinc-500 line-clamp-1 mt-0.5 font-normal">
                        {result.description}
                      </p>
                    </Link>
                  ))}
                </div>
              ) : (
                <div className="p-8 text-center text-xs text-zinc-500">
                  No articles found matching &ldquo;{searchQuery}&rdquo;.
                </div>
              )}
            </div>

            {/* Modal Footer */}
            <div className="px-4 py-2.5 bg-zinc-50 border-t border-zinc-100 flex items-center justify-between text-[11px] text-zinc-400">
              <span>{DOCS_DATA.length} documentation guides indexed</span>
              <span>Press <kbd className="font-mono font-semibold">ESC</kbd> to exit</span>
            </div>

          </div>
        </div>
      )}

    </div>
  );
}
