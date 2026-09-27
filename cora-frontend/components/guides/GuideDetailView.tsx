'use client';

import React, { useState, useEffect, useRef } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  ArrowRight, 
  ArrowLeft,
  BookOpen, 
  Clock, 
  Layers, 
  Download, 
  Share2, 
  Check, 
  Bookmark, 
  Sparkles,
  ChevronRight,
  ShieldCheck,
  CheckCircle2,
  FileText
} from 'lucide-react';
import { Guide, GuideChapter } from '@/lib/guides-data';
import { getBlogCategoryById } from '@/lib/blog-data';
import { BlogBlockRenderer } from '@/components/blog/BlogBlockRenderer';
import { BlogReadingProgress } from '@/components/blog/BlogReadingProgress';
import { GuideTableOfContents } from './GuideTableOfContents';
import { GuideMobileBar } from './GuideMobileBar';
import { GuideLeadMagnetModal } from './GuideLeadMagnetModal';
import { GuideShareModal } from './GuideShareModal';
import { ShareInsightCard } from './ShareInsightCard';
import { GuideRelatedContent } from './GuideRelatedContent';
import { trackEvent } from '@/components/analytics/Analytics';

interface GuideDetailViewProps {
  guide: Guide;
}

export function GuideDetailView({ guide }: GuideDetailViewProps) {
  const [activeChapterIndex, setActiveChapterIndex] = useState(0);
  const [completedChapters, setCompletedChapters] = useState<Set<number>>(new Set());
  const [resumeChapter, setResumeChapter] = useState<{ index: number; title: string; number: string } | null>(null);
  const [isDownloadModalOpen, setIsDownloadModalOpen] = useState(false);
  const [isShareModalOpen, setIsShareModalOpen] = useState(false);
  const [shareChapterIndex, setShareChapterIndex] = useState<number | null>(null);
  const [modalTrigger, setModalTrigger] = useState('hero');

  const readingCanvasRef = useRef<HTMLDivElement>(null);
  const category = getBlogCategoryById(guide.category);
  const activeChapter = guide.chapters[activeChapterIndex] || guide.chapters[0];

  // 1. Restore Reading Progress from localStorage
  useEffect(() => {
    try {
      const storedProgress = localStorage.getItem(`cora_guide_progress_${guide.slug}`);
      if (storedProgress) {
        const parsed = JSON.parse(storedProgress);
        if (typeof parsed.index === 'number' && parsed.index > 0 && parsed.index < guide.chapters.length) {
          setResumeChapter({
            index: parsed.index,
            title: guide.chapters[parsed.index]?.title || '',
            number: guide.chapters[parsed.index]?.number || '',
          });
        }
      }
    } catch (e) {
      console.error('Failed to load guide progress:', e);
    }
  }, [guide.slug, guide.chapters]);

  // 2. Check URL hash on initial load for stable chapter anchor
  useEffect(() => {
    if (typeof window !== 'undefined' && window.location.hash) {
      const hash = window.location.hash.replace('#', '');
      const matchedIndex = guide.chapters.findIndex((ch) => ch.slug === hash);
      if (matchedIndex !== -1) {
        setActiveChapterIndex(matchedIndex);
        setResumeChapter(null);
      }
    }
  }, [guide.chapters]);

  // 3. Track Chapter View & Save Progress
  useEffect(() => {
    if (!activeChapter) return;

    trackEvent('chapter_view', {
      guide_slug: guide.slug,
      chapter_number: activeChapter.number,
      chapter_slug: activeChapter.slug,
    });

    // Persist progress
    try {
      localStorage.setItem(
        `cora_guide_progress_${guide.slug}`,
        JSON.stringify({ index: activeChapterIndex, slug: activeChapter.slug })
      );
    } catch (e) {
      // Ignore local storage write errors
    }

    // Mark previous chapters as completed
    setCompletedChapters((prev) => {
      const updated = new Set(prev);
      for (let i = 0; i < activeChapterIndex; i++) {
        updated.add(i);
      }
      return updated;
    });

    // Update URL hash without scrolling the full page
    if (typeof window !== 'undefined') {
      window.history.replaceState(null, '', `#${activeChapter.slug}`);
    }
  }, [activeChapterIndex, activeChapter, guide.slug]);

  const handleSelectChapter = (index: number) => {
    setActiveChapterIndex(index);
    setResumeChapter(null);

    // Smooth scroll reading canvas into view
    if (readingCanvasRef.current) {
      readingCanvasRef.current.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  const handleNextChapter = () => {
    if (activeChapterIndex < guide.chapters.length - 1) {
      handleSelectChapter(activeChapterIndex + 1);
    }
  };

  const handlePrevChapter = () => {
    if (activeChapterIndex > 0) {
      handleSelectChapter(activeChapterIndex - 1);
    }
  };

  const openDownloadModal = (placement: string) => {
    setModalTrigger(placement);
    setIsDownloadModalOpen(true);
  };

  const openShareModal = (chapterIdx?: number) => {
    setShareChapterIndex(chapterIdx ?? null);
    setIsShareModalOpen(true);
  };

  return (
    <article className="min-h-screen bg-white text-zinc-950 selection:bg-zinc-200 pb-20 lg:pb-16">
      <BlogReadingProgress />

      {/* ── 1. DIGITAL BOOK HERO SECTION ─────────────────────────────── */}
      <header className="relative w-full border-b border-zinc-200/80 bg-[#FAFAF8] pt-28 sm:pt-32 pb-12 sm:pb-16">
        <div className="max-w-[1240px] mx-auto px-4 sm:px-6">
          {/* Breadcrumbs */}
          <nav aria-label="Breadcrumbs" className="flex items-center gap-2 text-xs font-mono text-zinc-500 mb-6">
            <Link href="/" className="hover:text-zinc-950 transition-colors">
              Home
            </Link>
            <span>/</span>
            <Link href="/guides/" className="hover:text-zinc-950 transition-colors">
              Guides
            </Link>
            <span>/</span>
            <span className="text-zinc-900 font-semibold truncate max-w-[200px] sm:max-w-none">
              {guide.title}
            </span>
          </nav>

          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            {/* Left Column: Metadata & Book Details */}
            <div className="lg:col-span-7 space-y-4 sm:space-y-5">
              {/* Category & Badges */}
              <div className="flex flex-wrap items-center gap-2">
                <span className="px-3 py-1 rounded-full bg-zinc-950 text-white text-[10.5px] font-mono font-bold uppercase tracking-wider">
                  {guide.qualityLabel || 'Digital Book'}
                </span>
                <span className="px-3 py-1 rounded-full bg-zinc-200/70 text-zinc-800 text-[10.5px] font-mono font-semibold">
                  {category?.name || guide.category}
                </span>
                <span className="text-[11px] font-mono text-zinc-600 font-medium">
                  Free Resource
                </span>
              </div>

              {/* Title */}
              <h1 className="font-display text-2xl sm:text-4xl md:text-[42px] font-extrabold text-zinc-950 tracking-tight leading-[1.15]">
                {guide.title}
              </h1>

              {/* Subtitle / Dek */}
              <p className="text-sm sm:text-base text-zinc-700 leading-relaxed max-w-[620px]">
                {guide.dek || guide.excerpt}
              </p>

              {/* Author & Publication Metadata */}
              <div className="pt-2 flex flex-wrap items-center gap-4 text-xs text-zinc-600 border-t border-zinc-200/70">
                <div className="flex items-center gap-2.5">
                  <div className="w-8 h-8 rounded-full bg-zinc-950 text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                    {guide.author.name.charAt(0)}
                  </div>
                  <div>
                    <span className="font-bold text-zinc-950 block">{guide.author.name}</span>
                    <span className="text-[11px] text-zinc-500">{guide.author.role}</span>
                  </div>
                </div>

                <div className="h-4 w-px bg-zinc-300 hidden sm:block" />

                <div className="flex items-center gap-3 font-mono text-[11px] text-zinc-500">
                  <span className="flex items-center gap-1 text-zinc-700">
                    <Layers className="w-3.5 h-3.5" />
                    <span>{guide.chapterCount} Chapters</span>
                  </span>
                  <span>&bull;</span>
                  <span className="flex items-center gap-1 text-zinc-700">
                    <Clock className="w-3.5 h-3.5" />
                    <span>{guide.readTime}</span>
                  </span>
                  <span>&bull;</span>
                  <span>Updated {guide.updatedAt}</span>
                </div>
              </div>

              {/* Action Buttons: Download Playbook & Share */}
              <div className="pt-4 flex flex-wrap items-center gap-3">
                {guide.downloadableAsset && (
                  <button
                    type="button"
                    onClick={() => openDownloadModal('hero')}
                    className="inline-flex items-center justify-center gap-2 bg-zinc-950 hover:bg-black text-white px-5 sm:px-6 py-3 rounded-xl text-xs sm:text-sm font-bold shadow-md transition-all active:scale-[0.99]"
                  >
                    <Download className="w-4 h-4" />
                    <span>Download Playbook ({guide.downloadableAsset.fileType.toUpperCase()})</span>
                  </button>
                )}

                <button
                  type="button"
                  onClick={() => openShareModal()}
                  className="inline-flex items-center justify-center gap-2 bg-white hover:bg-zinc-100 text-zinc-900 border border-zinc-200 px-4 py-3 rounded-xl text-xs sm:text-sm font-bold shadow-2xs transition-colors"
                >
                  <Share2 className="w-4 h-4 text-zinc-600" />
                  <span>Share</span>
                </button>
              </div>
            </div>

            {/* Right Column: Book Cover Presentation */}
            <div className="lg:col-span-5 flex justify-center">
              <div className="relative w-full max-w-[360px] aspect-[1/1.25] rounded-2xl bg-zinc-900 p-3 shadow-2xl border border-zinc-800 rotate-1 hover:rotate-0 transition-transform duration-300">
                <div className="relative w-full h-full rounded-xl overflow-hidden bg-zinc-950 border border-zinc-800">
                  {guide.coverImage && (
                    <Image
                      src={guide.coverImage}
                      alt={guide.coverAlt || guide.title}
                      fill
                      priority
                      className="object-cover"
                      sizes="(max-width: 768px) 100vw, 360px"
                    />
                  )}
                  {/* Digital Book Spine Overlay */}
                  <div className="absolute left-0 inset-y-0 w-4 bg-gradient-to-r from-black/60 to-transparent pointer-events-none" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>

      {/* ── 2. "CONTINUE READING" PROGRESS RESUME BANNER ──────────────── */}
      {resumeChapter && (
        <div className="bg-zinc-900 text-white py-3 px-4 shadow-sm">
          <div className="max-w-[1240px] mx-auto flex flex-wrap items-center justify-between gap-3 text-xs">
            <div className="flex items-center gap-2 min-w-0">
              <Bookmark className="w-4 h-4 text-zinc-400 shrink-0" />
              <span className="text-zinc-300">Pick up where you left off:</span>
              <strong className="text-white truncate">
                Chapter {resumeChapter.number}: {resumeChapter.title}
              </strong>
            </div>
            <div className="flex items-center gap-2 shrink-0">
              <button
                type="button"
                onClick={() => handleSelectChapter(resumeChapter.index)}
                className="bg-white text-zinc-950 hover:bg-zinc-100 px-3 py-1 rounded-lg text-xs font-bold transition-colors"
              >
                Continue Chapter →
              </button>
              <button
                type="button"
                onClick={() => setResumeChapter(null)}
                className="text-zinc-400 hover:text-white text-xs px-2 py-1"
              >
                Dismiss
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ── 3. MAIN DIGITAL BOOK LAYOUT ──────────────────────────────── */}
      <div ref={readingCanvasRef} className="max-w-[1240px] mx-auto px-4 sm:px-6 pt-10 sm:pt-14">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
          
          {/* DESKTOP STICKY SIDEBAR: TABLE OF CONTENTS */}
          <aside className="hidden lg:block lg:col-span-4 sticky top-24 space-y-6">
            <GuideTableOfContents
              chapters={guide.chapters}
              activeChapterIndex={activeChapterIndex}
              onSelectChapter={handleSelectChapter}
              completedChapters={completedChapters}
            />

            {/* Sidebar Download Lead Magnet Card */}
            {guide.downloadableAsset && (
              <div className="p-5 rounded-2xl bg-zinc-50 text-zinc-950 space-y-3 shadow-2xs border border-zinc-200/90">
                <div className="flex items-center justify-between">
                  <span className="text-[10px] font-mono font-bold uppercase text-zinc-500">
                    Included Resource
                  </span>
                  <span className="text-[10px] font-mono font-semibold text-zinc-600 bg-zinc-200/60 px-2 py-0.5 rounded">
                    {guide.downloadableAsset.fileType.toUpperCase()}
                  </span>
                </div>
                <h4 className="font-display font-bold text-sm leading-snug text-zinc-950">
                  {guide.downloadableAsset.title}
                </h4>
                <p className="text-xs text-zinc-600 leading-relaxed">
                  {guide.downloadableAsset.description}
                </p>
                <button
                  type="button"
                  onClick={() => openDownloadModal('sidebar')}
                  className="w-full inline-flex items-center justify-center gap-1.5 bg-zinc-950 text-white hover:bg-black py-2.5 px-4 rounded-xl text-xs font-bold transition-colors shadow-2xs"
                >
                  <Download className="w-3.5 h-3.5" />
                  <span>Get the Playbook Pack</span>
                </button>
              </div>
            )}
          </aside>

          {/* MAIN READING CANVAS */}
          <main className="lg:col-span-8 space-y-12">
            
            {/* ── CHAPTER OPENER SCREEN ───────────────────────────────── */}
            <section
              id={activeChapter.slug}
              className="p-6 sm:p-10 rounded-3xl bg-[#FAFAF8] border border-zinc-200/90 shadow-2xs space-y-5"
            >
              {/* Chapter Number Badge & Header */}
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <span className="font-mono text-xs sm:text-sm font-black text-white bg-zinc-950 px-3 py-1 rounded-xl shadow-2xs">
                    CHAPTER {activeChapter.number}
                  </span>
                  <span className="text-xs font-mono text-zinc-500">
                    of {String(guide.chapters.length).padStart(2, '0')}
                  </span>
                </div>

                <div className="flex items-center gap-2">
                  <span className="text-xs font-mono text-zinc-500 flex items-center gap-1">
                    <Clock className="w-3.5 h-3.5" />
                    <span>{activeChapter.readTime}</span>
                  </span>
                  <button
                    type="button"
                    onClick={() => openShareModal(activeChapterIndex)}
                    className="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-950 hover:bg-zinc-200/70 transition-colors"
                    title="Share Chapter"
                  >
                    <Share2 className="w-4 h-4" />
                  </button>
                </div>
              </div>

              {/* Chapter Title */}
              <h2 className="font-display text-2xl sm:text-3xl md:text-4xl font-extrabold text-zinc-950 tracking-tight leading-snug">
                {activeChapter.title}
              </h2>

              {/* Chapter Summary */}
              {activeChapter.summary && (
                <p className="text-sm sm:text-base text-zinc-700 leading-relaxed font-normal pt-1 border-t border-zinc-200/70">
                  {activeChapter.summary}
                </p>
              )}
            </section>

            {/* ── CHAPTER CONTENT BLOCKS ──────────────────────────────── */}
            <div className="prose-cora text-zinc-900 text-sm sm:text-base leading-relaxed px-1 sm:px-2">
              <BlogBlockRenderer
                blocks={activeChapter.blocks}
                articleSlug={guide.slug}
                category={guide.category}
              />
            </div>

            {/* ── CHAPTER VISUAL INFOGRAPHIC / FRAMEWORK ─────────────── */}
            {activeChapter.infographics && activeChapter.infographics.length > 0 && (
              <div className="my-8 space-y-6">
                {activeChapter.infographics.map((info) => (
                  <div
                    key={info.id}
                    className="p-6 sm:p-8 rounded-2xl bg-zinc-50 text-zinc-950 border border-zinc-200/90 shadow-2xs space-y-4"
                  >
                    <div className="flex items-center justify-between pb-3 border-b border-zinc-200">
                      <div className="flex items-center gap-2">
                        <Sparkles className="w-4 h-4 text-zinc-700" />
                        <span className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-900">
                          {info.title}
                        </span>
                      </div>
                      <span className="text-[10px] font-mono bg-zinc-200/80 text-zinc-700 font-semibold px-2 py-0.5 rounded">
                        Framework
                      </span>
                    </div>

                    {info.items && (
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                        {info.items.map((item, i) => (
                          <div
                            key={i}
                            className="p-4 rounded-xl bg-white border border-zinc-200/80 shadow-2xs flex flex-col justify-between"
                          >
                            <div>
                              <div className="flex items-center justify-between mb-1.5">
                                <span className="font-bold text-xs sm:text-sm text-zinc-950">{item.label}</span>
                                {item.badge && (
                                  <span className="text-[9.5px] font-mono font-semibold text-zinc-800 bg-zinc-100 px-2 py-0.5 rounded border border-zinc-200">
                                    {item.badge}
                                  </span>
                                )}
                              </div>
                              <p className="text-xs text-zinc-600 leading-relaxed">{item.desc}</p>
                            </div>
                          </div>
                        ))}
                      </div>
                    )}
                  </div>
                ))}
              </div>
            )}

            {/* ── SHAREABLE INSIGHT CALLOUT ───────────────────────────── */}
            {activeChapter.shareableInsight && (
              <ShareInsightCard
                insight={activeChapter.shareableInsight}
                guideSlug={guide.slug}
                chapterSlug={activeChapter.slug}
              />
            )}

            {/* ── KEY TAKEAWAY CARD ──────────────────────────────────── */}
            {activeChapter.keyTakeaway && (
              <div className="my-8 p-6 sm:p-7 rounded-2xl bg-zinc-50 border border-zinc-200/90 text-zinc-950 space-y-2.5">
                <div className="flex items-center gap-2">
                  <CheckCircle2 className="w-4 h-4 text-zinc-900" />
                  <span className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-700">
                    Chapter Takeaway: {activeChapter.keyTakeaway.principle}
                  </span>
                </div>
                <p className="text-xs sm:text-sm text-zinc-800 leading-relaxed font-medium">
                  {activeChapter.keyTakeaway.description}
                </p>
                {activeChapter.keyTakeaway.actionableStep && (
                  <div className="pt-2 text-xs text-zinc-950 font-semibold flex items-center gap-1.5">
                    <ArrowRight className="w-3 h-3 text-zinc-700" />
                    <span>Action: {activeChapter.keyTakeaway.actionableStep}</span>
                  </div>
                )}
              </div>
            )}

            {/* ── MID-READING LEAD MAGNET CTA (at Chapter 3 or 4) ─────── */}
            {(activeChapterIndex === 2 || activeChapterIndex === 3) && guide.downloadableAsset && (
              <div className="my-10 p-6 sm:p-8 rounded-2xl bg-zinc-50 border border-zinc-200 shadow-2xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
                <div className="space-y-1 max-w-[420px]">
                  <span className="text-[10.5px] font-mono font-bold text-zinc-500 uppercase">
                    Free Implementation Resource
                  </span>
                  <h4 className="font-display font-bold text-base sm:text-lg text-zinc-950">
                    Download the {guide.downloadableAsset.title}
                  </h4>
                  <p className="text-xs text-zinc-600">
                    Get all 10 templates, checklists, and agreements mentioned in this guide.
                  </p>
                </div>
                <button
                  type="button"
                  onClick={() => openDownloadModal('mid_chapter')}
                  className="inline-flex items-center justify-center gap-1.5 bg-zinc-950 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-colors shadow-2xs shrink-0"
                >
                  <Download className="w-3.5 h-3.5" />
                  <span>Download Playbook</span>
                </button>
              </div>
            )}

            {/* ── CHAPTER-END NAVIGATION BUTTONS ──────────────────────── */}
            <nav className="pt-8 border-t border-zinc-200 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
              {activeChapterIndex > 0 ? (
                <button
                  type="button"
                  onClick={handlePrevChapter}
                  className="flex-1 flex items-center justify-start gap-3 p-4 rounded-2xl bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 transition-colors text-left"
                >
                  <ArrowLeft className="w-4 h-4 text-zinc-500 shrink-0" />
                  <div className="min-w-0">
                    <span className="text-[10px] font-mono text-zinc-500 uppercase block">Previous Chapter</span>
                    <span className="text-xs font-bold text-zinc-950 line-clamp-1">
                      Ch. {guide.chapters[activeChapterIndex - 1]?.number}: {guide.chapters[activeChapterIndex - 1]?.title}
                    </span>
                  </div>
                </button>
              ) : (
                <div className="flex-1" />
              )}

              {activeChapterIndex < guide.chapters.length - 1 ? (
                <button
                  type="button"
                  onClick={handleNextChapter}
                  className="flex-1 flex items-center justify-end gap-3 p-4 rounded-2xl bg-zinc-950 hover:bg-black text-white transition-colors text-right shadow-sm"
                >
                  <div className="min-w-0">
                    <span className="text-[10px] font-mono text-zinc-400 uppercase block">Next Chapter</span>
                    <span className="text-xs font-bold text-white line-clamp-1">
                      Ch. {guide.chapters[activeChapterIndex + 1]?.number}: {guide.chapters[activeChapterIndex + 1]?.title}
                    </span>
                  </div>
                  <ArrowRight className="w-4 h-4 text-zinc-300 shrink-0" />
                </button>
              ) : (
                <button
                  type="button"
                  onClick={() => openDownloadModal('end_of_guide')}
                  className="flex-1 flex items-center justify-center gap-2 p-4 rounded-2xl bg-zinc-950 hover:bg-black text-white font-bold text-xs shadow-sm transition-colors"
                >
                  <Download className="w-4 h-4" />
                  <span>Download Complete Implementation Pack (PDF)</span>
                </button>
              )}
            </nav>

            {/* ── 4. CONTINUE LEARNING SECTION ────────────────────────── */}
            <GuideRelatedContent currentGuide={guide} />

          </main>
        </div>
      </div>

      {/* ── 5. MOBILE STICKY ISLAND BAR ──────────────────────────────── */}
      <GuideMobileBar
        chapters={guide.chapters}
        activeChapterIndex={activeChapterIndex}
        onSelectChapter={handleSelectChapter}
        onOpenDownloadModal={() => openDownloadModal('mobile_bar')}
        asset={guide.downloadableAsset}
        completedChapters={completedChapters}
      />

      {/* ── 6. MODALS: LEAD MAGNET & SOCIAL SHARING ──────────────────── */}
      {guide.downloadableAsset && (
        <GuideLeadMagnetModal
          isOpen={isDownloadModalOpen}
          onClose={() => setIsDownloadModalOpen(false)}
          guideTitle={guide.title}
          guideSlug={guide.slug}
          asset={guide.downloadableAsset}
          triggerPosition={modalTrigger}
        />
      )}

      <GuideShareModal
        isOpen={isShareModalOpen}
        onClose={() => setIsShareModalOpen(false)}
        guideTitle={guide.title}
        guideSlug={guide.slug}
        chapterSlug={shareChapterIndex !== null ? guide.chapters[shareChapterIndex]?.slug : undefined}
        chapterTitle={shareChapterIndex !== null ? guide.chapters[shareChapterIndex]?.title : undefined}
      />
    </article>
  );
}
