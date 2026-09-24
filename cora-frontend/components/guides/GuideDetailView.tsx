'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  ChevronRight, 
  Clock, 
  Home, 
  Layers, 
  ArrowRight, 
  ShieldAlert, 
  Sparkles, 
  BookOpen, 
  Download,
  CheckCircle2,
  Share2,
  ExternalLink
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
    <article className="min-h-screen bg-white text-zinc-900 selection:bg-zinc-200">
      <BlogReadingProgress />

      {/* Non-published Preview Banner */}
      {isUnpublished && (
        <div className="bg-amber-500/10 border-b border-amber-500/30 text-amber-900 pt-20 pb-2.5 px-4 text-xs font-mono text-center flex items-center justify-center gap-2">
          <ShieldAlert className="w-4 h-4 text-amber-600" />
          <span>
            <strong>STATUS: {guide.status.toUpperCase()}</strong> &bull; This playbook is excluded from public sitemaps and search indexing (noindex, nofollow).
          </span>
        </div>
      )}

      {/* ── SECTION 1: APPLE-GRADE HERO MASTHEAD WITH AMBIENT CANVAS ── */}
      <section className={`relative w-full overflow-hidden border-b border-zinc-200/80 bg-gradient-to-b from-[#FAFAF9] via-[#FDFDFD] to-white ${isUnpublished ? 'pt-8' : 'pt-24 sm:pt-32'} pb-14 sm:pb-20`}>
        <ArtisticHeroBackground tone="neutral" />

        <div className="relative z-10 mx-auto max-w-[860px] px-4 sm:px-6">
          {/* Floating Breadcrumb Pill */}
          <nav className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-zinc-200/80 bg-white/80 backdrop-blur-md text-xs font-mono text-zinc-600 mb-6 shadow-xs">
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

          {/* Metadata Chips Bar */}
          <div className="flex flex-wrap items-center gap-2 text-[11px] font-mono mb-4">
            <span className="px-2.5 py-1 rounded-full bg-zinc-950 text-white font-bold uppercase tracking-wider text-[10px]">
              {guide.qualityLabel || 'OPERATIONAL PLAYBOOK'}
            </span>
            {categoryObj && (
              <span className="px-2.5 py-1 rounded-full bg-zinc-100 text-zinc-800 font-semibold border border-zinc-200/70 uppercase tracking-wider text-[10px]">
                {categoryObj.name}
              </span>
            )}
            <span className="text-zinc-400">&bull;</span>
            <span className="text-zinc-600 flex items-center gap-1 font-semibold">
              <Layers className="w-3.5 h-3.5 text-zinc-500" />
              <span>{guide.chapterCount} Chapters</span>
            </span>
            <span className="text-zinc-400">&bull;</span>
            <span className="text-zinc-600 flex items-center gap-1 font-semibold">
              <Clock className="w-3.5 h-3.5 text-zinc-500" />
              <span>{guide.readTime}</span>
            </span>
          </div>

          <h1 className="font-display text-3xl sm:text-5xl lg:text-[46px] font-bold tracking-tight text-zinc-950 leading-[1.12]">
            {guide.title}
          </h1>

          <p className="mt-5 text-base sm:text-lg lg:text-xl text-zinc-600 leading-relaxed font-normal">
            {guide.dek}
          </p>

          {/* Author & Verified Bar */}
          <div className="mt-8 pt-6 border-t border-zinc-200/80 flex flex-wrap items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center font-display font-bold text-sm shadow-xs">
                {guide.author.name.charAt(0)}
              </div>
              <div>
                <div className="font-bold text-xs sm:text-sm text-zinc-950 flex items-center gap-1.5">
                  <span>{guide.author.name}</span>
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Verified Author" />
                </div>
                <div className="text-[11px] text-zinc-500">{guide.author.role}</div>
              </div>
            </div>

            <div className="text-right text-xs font-mono text-zinc-500">
              <div>Published: {guide.publishedAt}</div>
              {guide.updatedAt !== guide.publishedAt && (
                <div className="text-[10px] text-zinc-400">Updated: {guide.updatedAt}</div>
              )}
            </div>
          </div>

          {/* Quick Action Buttons Row */}
          <div className="mt-8 flex flex-wrap items-center gap-3">
            <button
              onClick={scrollToFirstChapter}
              className="px-6 py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all flex items-center gap-2 cursor-pointer shadow-sm active:scale-98"
            >
              <span>Start Reading Chapter 1</span>
              <ArrowRight className="w-4 h-4" />
            </button>

            {guide.downloadableAsset && (
              <button
                onClick={() => handleOpenModal('hero_button')}
                className="px-5 py-3 rounded-xl bg-white hover:bg-zinc-50 text-zinc-900 border border-zinc-200/80 text-xs sm:text-sm font-bold transition-all flex items-center gap-2 cursor-pointer shadow-xs active:scale-98"
              >
                <Download className="w-4 h-4 text-zinc-600" />
                <span>Get 10x SOP Pack (Free)</span>
              </button>
            )}
          </div>
        </div>
      </section>

      {/* ── SECTION 2: MAIN CONTENT & FLOATING CHAPTER RAIL ── */}
      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-12 sm:py-16">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12">
          {/* Main Guide Body (8 cols) */}
          <main className="lg:col-span-8 lg:col-start-1 max-w-[760px]">
            {/* Mobile Chapter Floating Bar */}
            <GuideChapterNavigation chapters={guide.chapters} mode="mobile" />

            {/* Editorial Cover Visual */}
            {guide.coverImage && (
              <div className="mb-12 overflow-hidden rounded-[28px] sm:rounded-[32px] border border-zinc-200/80 bg-zinc-100 shadow-[0_12px_40px_rgb(0,0,0,0.04)]">
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
            <div className="space-y-20">
              {guide.chapters.map((chapter, index) => {
                const nextChapter = guide.chapters[index + 1];

                return (
                  <section
                    key={chapter.slug}
                    id={chapter.slug}
                    className="scroll-mt-28 border-b border-zinc-200/80 pb-16 last:border-b-0"
                  >
                    {/* Chapter Header Card */}
                    <div className="mb-8">
                      <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 border border-zinc-200/80 text-xs font-mono font-bold text-zinc-800 mb-3">
                        <span>CHAPTER {chapter.number}</span>
                        {chapter.readTime && <span className="text-zinc-400">&bull; {chapter.readTime} read</span>}
                      </div>

                      <h2 className="font-display text-2xl sm:text-3xl lg:text-[32px] font-bold tracking-tight text-zinc-950 leading-tight">
                        {chapter.title}
                      </h2>

                      {chapter.summary && (
                        <div className="mt-4 p-4 sm:p-5 rounded-2xl bg-zinc-50/80 border border-zinc-200/70 text-sm text-zinc-700 leading-relaxed font-medium">
                          {chapter.summary}
                        </div>
                      )}
                    </div>

                    {/* Chapter Blocks */}
                    <BlogBlockRenderer
                      blocks={chapter.blocks}
                      articleSlug={guide.slug}
                      category={guide.category}
                    />

                    {/* Mid-Guide Lead Magnet CTA after Chapter 3 */}
                    {index === 2 && guide.downloadableAsset && (
                      <GuideDownloadCTA
                        asset={guide.downloadableAsset}
                        guideTitle={guide.title}
                        guideSlug={guide.slug}
                        placement="mid"
                        onOpenModal={handleOpenModal}
                      />
                    )}

                    {/* Next Chapter Navigation Card */}
                    {nextChapter && (
                      <div className="mt-12 pt-8 border-t border-zinc-100">
                        <a
                          href={`#${nextChapter.slug}`}
                          className="group block p-5 sm:p-6 rounded-2xl border border-zinc-200/80 bg-zinc-50/50 hover:bg-zinc-50 hover:border-zinc-300 transition-all shadow-xs"
                        >
                          <div className="flex items-center justify-between text-[11px] font-mono font-bold text-zinc-500 mb-1">
                            <span>NEXT CHAPTER // {nextChapter.number}</span>
                            <ArrowRight className="w-4 h-4 text-zinc-700 group-hover:translate-x-1 transition-transform" />
                          </div>
                          <div className="font-display font-bold text-base sm:text-lg text-zinc-950 group-hover:text-zinc-700 transition-colors">
                            {nextChapter.title}
                          </div>
                          {nextChapter.summary && (
                            <p className="mt-1 text-xs text-zinc-600 line-clamp-1">
                              {nextChapter.summary}
                            </p>
                          )}
                        </a>
                      </div>
                    )}
                  </section>
                );
              })}
            </div>

            {/* End of Guide Download Asset Card */}
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
            <div className="mt-12 pt-6 border-t border-zinc-200">
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

            {/* Related Tools Layer */}
            {guide.relatedTools && guide.relatedTools.length > 0 && (
              <div className="my-10 p-6 sm:p-8 rounded-3xl border border-zinc-200/80 bg-zinc-50/50 space-y-4">
                <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-500 flex items-center gap-2">
                  <Sparkles className="w-3.5 h-3.5 text-zinc-700" />
                  <span>RECOMMENDED INTERACTIVE TOOLS</span>
                </div>
                <div className="grid gap-3.5 sm:grid-cols-2">
                  {guide.relatedTools.map((tool) => (
                    <Link
                      key={tool.href}
                      href={tool.href}
                      className="p-5 rounded-2xl border border-zinc-200/80 bg-white hover:border-zinc-300 hover:shadow-md transition-all block group"
                    >
                      <div className="flex items-center justify-between text-[10px] font-mono text-zinc-500 mb-1.5">
                        <span className="font-bold text-zinc-800 px-2 py-0.5 rounded bg-zinc-100">{tool.badge}</span>
                        <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-zinc-950 group-hover:translate-x-0.5 transition-all" />
                      </div>
                      <div className="font-bold text-sm sm:text-base text-zinc-950 group-hover:text-zinc-700 transition-colors">
                        {tool.title}
                      </div>
                      <div className="text-xs text-zinc-600 mt-1.5 line-clamp-2 leading-relaxed">
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
              tagline="Get our high-impact operating playbooks and workflow breakdowns delivered to your inbox every week."
              buttonText="Subscribe Free"
              articleSlug={guide.slug}
              category={guide.category}
              placement="end"
            />
          </main>

          {/* Sticky Chapter Rail (4 cols) */}
          <aside className="hidden lg:block lg:col-span-4 lg:col-start-9">
            <div className="sticky top-24 space-y-6">
              {/* Desktop Sticky Chapters Rail */}
              <GuideChapterNavigation chapters={guide.chapters} mode="desktop" />

              {/* Downloadable Asset Sidebar Box */}
              {guide.downloadableAsset && (
                <GuideDownloadCTA
                  asset={guide.downloadableAsset}
                  guideTitle={guide.title}
                  guideSlug={guide.slug}
                  placement="rail"
                  onOpenModal={handleOpenModal}
                />
              )}

              {/* About this Publication */}
              <div className="p-6 rounded-3xl border border-zinc-200/80 bg-white shadow-xs text-xs space-y-3">
                <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-500">
                  ABOUT CORA PLAYBOOKS
                </div>
                <p className="text-zinc-600 leading-relaxed">
                  Cora publishes tested operating procedures, contract frameworks, and cash flow systems for creative and technical service businesses.
                </p>
                <div className="pt-2 border-t border-zinc-100">
                  <Link
                    href="/agency-management-software-india/"
                    className="inline-flex items-center gap-1.5 font-bold text-zinc-950 hover:underline"
                  >
                    <span>Explore Cora Platform</span>
                    <ArrowRight className="w-3.5 h-3.5" />
                  </Link>
                </div>
              </div>
            </div>
          </aside>
        </div>
      </div>

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
