'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  ChevronRight, 
  Clock, 
  Layers, 
  ArrowRight, 
  ShieldAlert, 
  Sparkles, 
  Download,
  PackageCheck,
  Check
} from 'lucide-react';
import { Guide } from '@/lib/guides-data';
import { getBlogCategoryById } from '@/lib/blog-data';
import { BlogBlockRenderer } from '@/components/blog/BlogBlockRenderer';
import { BlogReadingProgress } from '@/components/blog/BlogReadingProgress';
import { BlogShareBar } from '@/components/blog/BlogShareBar';
import { BlogSources } from '@/components/blog/BlogSources';
import { BlogAuthorBio } from '@/components/blog/BlogAuthorBio';
import { BlogNewsletterBlock } from '@/components/blog/BlogNewsletterBlock';
import { GuideChapterNavigation } from '@/components/guides/GuideChapterNavigation';
import { GuideDownloadCTA } from '@/components/guides/GuideDownloadCTA';
import { GuideLeadMagnetModal } from '@/components/guides/GuideLeadMagnetModal';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';

interface GuideDetailViewProps {
  guide: Guide;
}

export function GuideDetailView({ guide }: GuideDetailViewProps) {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [modalPlacement, setModalPlacement] = useState('hero');

  const categoryObj = getBlogCategoryById(guide.category);
  const isUnpublished = guide.status !== 'published';

  const handleOpenModal = (placement: string) => {
    setModalPlacement(placement);
    setIsModalOpen(true);
  };

  const scrollToFirstChapter = () => {
    const firstChapterSlug = guide.chapters[0]?.slug;
    if (firstChapterSlug) {
      const el = document.getElementById(firstChapterSlug);
      if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
      }
    }
  };

  return (
    <article className="min-h-screen bg-white text-zinc-900 selection:bg-zinc-200 pb-16 lg:pb-0">
      <BlogReadingProgress />

      {/* Non-published Preview Banner */}
      {isUnpublished && (
        <div className="bg-amber-500/10 border-b border-amber-500/30 text-amber-900 pt-20 pb-2.5 px-4 text-xs font-mono text-center flex items-center justify-center gap-2">
          <ShieldAlert className="w-4 h-4 text-amber-600" />
          <span>
            <strong>STATUS: {guide.status.toUpperCase()}</strong> &bull; This playbook is excluded from public sitemaps and search indexing.
          </span>
        </div>
      )}

      {/* ── SECTION 1: CLEAN EDITORIAL HERO ── */}
      <section className={`relative w-full overflow-hidden border-b border-zinc-200/80 bg-gradient-to-b from-[#FAFAF9] via-[#FDFDFD] to-white ${isUnpublished ? 'pt-6' : 'pt-24 sm:pt-28'} pb-12 sm:pb-16`}>
        <ArtisticHeroBackground tone="neutral" />

        <div className="relative z-10 mx-auto max-w-[860px] px-4 sm:px-6">
          {/* Breadcrumb Navigation Pill */}
          <nav className="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-zinc-200/80 bg-white/80 backdrop-blur-md text-xs font-mono text-zinc-600 mb-5 shadow-xs">
            <Link href="/" className="hover:text-zinc-950 transition-colors">
              Cora
            </Link>
            <ChevronRight className="w-3 h-3 text-zinc-400" />
            <Link href="/guides" className="hover:text-zinc-950 transition-colors">
              Guides
            </Link>
            {categoryObj && (
              <>
                <ChevronRight className="w-3 h-3 text-zinc-400" />
                <span className="text-zinc-950 font-bold line-clamp-1">{categoryObj.shortName}</span>
              </>
            )}
          </nav>

          {/* Metadata Chips */}
          <div className="flex flex-wrap items-center gap-2 text-[11px] font-mono mb-3.5">
            <span className="px-2 py-0.5 rounded-full bg-zinc-950 text-white font-bold uppercase tracking-wider text-[10px]">
              {guide.qualityLabel || 'PLAYBOOK'}
            </span>
            {categoryObj && (
              <span className="px-2.5 py-0.5 rounded-full bg-zinc-100 text-zinc-800 font-semibold border border-zinc-200/70 uppercase tracking-wider text-[10px]">
                {categoryObj.name}
              </span>
            )}
            <span className="text-zinc-400">&bull;</span>
            <span className="text-zinc-600 flex items-center gap-1 font-semibold">
              <Layers className="w-3 h-3 text-zinc-500" />
              <span>{guide.chapterCount} Chapters</span>
            </span>
            <span className="text-zinc-400">&bull;</span>
            <span className="text-zinc-600 flex items-center gap-1 font-semibold">
              <Clock className="w-3 h-3 text-zinc-500" />
              <span>{guide.readTime}</span>
            </span>
          </div>

          <h1 className="font-display text-2xl sm:text-4xl lg:text-[42px] font-bold tracking-tight text-zinc-950 leading-[1.14]">
            {guide.title}
          </h1>

          <p className="mt-4 text-base sm:text-lg text-zinc-600 leading-relaxed font-normal">
            {guide.dek}
          </p>

          {/* Author & Action Bar */}
          <div className="mt-6 pt-5 border-t border-zinc-200/80 flex flex-wrap items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <div className="relative w-9 h-9 rounded-full overflow-hidden border border-zinc-300/80 bg-zinc-100 shrink-0 shadow-xs">
                {guide.author.avatar ? (
                  <Image
                    src={guide.author.avatar}
                    alt={guide.author.name}
                    fill
                    sizes="36px"
                    className="object-cover"
                  />
                ) : (
                  <div className="w-full h-full bg-zinc-950 text-white flex items-center justify-center font-display font-bold text-xs">
                    {guide.author.name.charAt(0)}
                  </div>
                )}
              </div>
              <div>
                <div className="font-bold text-xs text-zinc-950 flex items-center gap-1.5">
                  <span>{guide.author.name}</span>
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Verified Author" />
                </div>
                <div className="text-[10px] font-mono text-zinc-500">
                  {guide.author.role} &bull; {guide.publishedAt}
                </div>
              </div>
            </div>

            <div className="flex items-center gap-2.5">
              <button
                onClick={scrollToFirstChapter}
                className="px-4 py-2 rounded-xl bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all flex items-center gap-1.5 cursor-pointer shadow-sm active:scale-98"
              >
                <span>Read Chapter 1</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>

              {guide.downloadableAsset && (
                <button
                  onClick={() => handleOpenModal('hero_button')}
                  className="px-3.5 py-2 rounded-xl bg-white hover:bg-zinc-50 text-zinc-900 border border-zinc-200/80 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-xs active:scale-98"
                >
                  <Download className="w-3.5 h-3.5 text-zinc-600" />
                  <span>Get SOP Pack</span>
                </button>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* ── SECTION 2: CLEAN READING CONTENT + SLEEK STICKY RAIL ── */}
      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-10 sm:py-14">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12">
          {/* Main Reading Flow (8 cols) */}
          <main className="lg:col-span-8 lg:col-start-1 max-w-[720px]">
            {/* Editorial Cover Visual */}
            {guide.coverImage && (
              <div className="mb-12 overflow-hidden rounded-2xl border border-zinc-200/80 bg-zinc-100 shadow-sm">
                <Image
                  src={guide.coverImage}
                  alt={guide.coverAlt || guide.title}
                  width={1600}
                  height={900}
                  priority
                  className="w-full h-auto aspect-[16/9] object-cover"
                />
              </div>
            )}

            {/* Chapter Rendering Loop */}
            <div className="space-y-16">
              {guide.chapters.map((chapter, index) => {
                const nextChapter = guide.chapters[index + 1];

                return (
                  <section
                    key={chapter.slug}
                    id={chapter.slug}
                    className="scroll-mt-24 border-b border-zinc-200/70 pb-14 last:border-b-0"
                  >
                    {/* Chapter Header */}
                    <div className="mb-6">
                      <div className="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-zinc-100 border border-zinc-200/80 text-[11px] font-mono font-bold text-zinc-700 mb-2.5">
                        <span>CHAPTER {chapter.number}</span>
                        {chapter.readTime && <span className="text-zinc-400">&bull; {chapter.readTime} read</span>}
                      </div>

                      <h2 className="font-display text-2xl sm:text-3xl font-bold tracking-tight text-zinc-950 leading-snug">
                        {chapter.title}
                      </h2>

                      {chapter.summary && (
                        <p className="mt-2.5 text-xs sm:text-sm text-zinc-600 leading-relaxed font-medium">
                          {chapter.summary}
                        </p>
                      )}
                    </div>

                    {/* Chapter Editorial Blocks */}
                    <BlogBlockRenderer
                      blocks={chapter.blocks}
                      articleSlug={guide.slug}
                      category={guide.category}
                    />

                    {/* Next Chapter Quick Link */}
                    {nextChapter && (
                      <div className="mt-10 pt-6 border-t border-zinc-100">
                        <a
                          href={`#${nextChapter.slug}`}
                          className="group inline-flex items-center gap-2 text-xs font-bold text-zinc-900 hover:text-zinc-600 transition-colors"
                        >
                          <span>Next: Chapter {nextChapter.number} — {nextChapter.title}</span>
                          <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                        </a>
                      </div>
                    )}
                  </section>
                );
              })}
            </div>

            {/* End of Guide Lead Magnet Asset Card */}
            {guide.downloadableAsset && (
              <GuideDownloadCTA
                asset={guide.downloadableAsset}
                guideTitle={guide.title}
                guideSlug={guide.slug}
                placement="end"
                onOpenModal={handleOpenModal}
              />
            )}

            {/* Social Share Bar */}
            <div className="mt-10 pt-6 border-t border-zinc-200">
              <BlogShareBar
                title={guide.title}
                url={guide.canonicalUrl || `https://heycora.in/guides/${guide.slug}/`}
                articleSlug={guide.slug}
                shareTitle={guide.shareTitle}
                shareDescription={guide.shareDescription}
                shareText={guide.shareText}
              />
            </div>

            {/* Sources & Citations */}
            {guide.sources && <BlogSources sources={guide.sources} />}

            {/* Author Bio Box */}
            <BlogAuthorBio author={guide.author} />

            {/* Related Tools */}
            {guide.relatedTools && guide.relatedTools.length > 0 && (
              <div className="my-10 p-6 rounded-2xl border border-zinc-200/80 bg-zinc-50/50 space-y-3.5">
                <div className="text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-500">
                  RECOMMENDED INTERACTIVE TOOLS
                </div>
                <div className="grid gap-3 sm:grid-cols-2">
                  {guide.relatedTools.map((tool) => (
                    <Link
                      key={tool.href}
                      href={tool.href}
                      className="p-4 rounded-xl border border-zinc-200/80 bg-white hover:border-zinc-300 hover:shadow-xs transition-all block group"
                    >
                      <div className="flex items-center justify-between text-[10px] font-mono text-zinc-500 mb-1">
                        <span className="font-bold text-zinc-800">{tool.badge}</span>
                        <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-zinc-950 group-hover:translate-x-0.5 transition-all" />
                      </div>
                      <div className="font-bold text-xs sm:text-sm text-zinc-950 group-hover:text-zinc-700 transition-colors">
                        {tool.title}
                      </div>
                      <div className="text-xs text-zinc-600 mt-1 line-clamp-2">
                        {tool.description}
                      </div>
                    </Link>
                  ))}
                </div>
              </div>
            )}

            {/* End of Guide Newsletter Card */}
            <BlogNewsletterBlock
              heading="Master agency systems and workflows."
              tagline="Get practical operating playbooks and workflow breakdowns delivered to your inbox every week."
              buttonText="Subscribe Free"
              articleSlug={guide.slug}
              category={guide.category}
              placement="end"
            />
          </main>

          {/* Sleek Sticky Sidebar (4 cols) */}
          <aside className="hidden lg:block lg:col-span-4 lg:col-start-9">
            <div className="sticky top-24 space-y-6 max-w-[280px] xl:max-w-[300px] ml-auto">
              {/* Minimal Chapter TOC */}
              <div className="p-4 rounded-2xl border border-zinc-200/80 bg-white shadow-xs">
                <GuideChapterNavigation chapters={guide.chapters} />
              </div>

              {/* Compact Asset Download Card */}
              {guide.downloadableAsset && (
                <div className="p-4 rounded-2xl border border-zinc-200/80 bg-zinc-50/70 text-xs space-y-2.5 shadow-xs">
                  <div className="flex items-center justify-between text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider">
                    <span className="flex items-center gap-1 text-zinc-700">
                      <PackageCheck className="w-3.5 h-3.5" />
                      <span>{guide.downloadableAsset.fileType.toUpperCase()} SOP PACK</span>
                    </span>
                    <span className="text-emerald-700 font-bold">FREE</span>
                  </div>

                  <div className="font-bold text-xs text-zinc-950 leading-snug">
                    {guide.downloadableAsset.title}
                  </div>

                  <p className="text-[11px] text-zinc-600 leading-relaxed">
                    10 ready-to-adapt onboarding templates, client emails, and access checklists.
                  </p>

                  <button
                    onClick={() => handleOpenModal('sidebar_rail')}
                    className="w-full py-2 px-3 rounded-lg bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-xs active:scale-98"
                  >
                    <Download className="w-3.5 h-3.5" />
                    <span>Download Templates</span>
                  </button>
                </div>
              )}
            </div>
          </aside>
        </div>
      </div>

      {/* ── MOBILE STICKY BOTTOM QUICK-CAPTURE BAR (44px-48px) ── */}
      {guide.downloadableAsset && (
        <div className="fixed bottom-0 left-0 right-0 z-40 lg:hidden h-12 bg-white/95 backdrop-blur-md border-t border-zinc-200/90 px-4 flex items-center justify-between shadow-[0_-4px_20px_rgb(0,0,0,0.06)]">
          <div className="flex items-center gap-2 truncate">
            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0" />
            <span className="text-xs font-bold text-zinc-900 truncate">
              {guide.downloadableAsset.title}
            </span>
          </div>

          <button
            onClick={() => handleOpenModal('mobile_sticky_bottom')}
            className="shrink-0 h-8 px-3 rounded-lg bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all flex items-center gap-1.5 shadow-sm active:scale-95"
          >
            <Download className="w-3 h-3" />
            <span>Get Pack</span>
          </button>
        </div>
      )}

      {/* Lead Magnet Download Modal */}
      {guide.downloadableAsset && (
        <GuideLeadMagnetModal
          isOpen={isModalOpen}
          onClose={() => setIsModalOpen(false)}
          guideTitle={guide.title}
          guideSlug={guide.slug}
          asset={guide.downloadableAsset}
          triggerPosition={modalPlacement}
        />
      )}
    </article>
  );
}
