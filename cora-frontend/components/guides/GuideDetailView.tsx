'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ChevronRight, Clock, Home, Layers, ArrowRight, ShieldAlert, Sparkles, BookOpen } from 'lucide-react';
import { Guide } from '@/lib/guides-data';
import { getBlogCategoryById, getArticleBySlug } from '@/lib/blog-data';
import { BlogBlockRenderer } from '@/components/blog/BlogBlockRenderer';
import { BlogReadingProgress } from '@/components/blog/BlogReadingProgress';
import { BlogShareBar } from '@/components/blog/BlogShareBar';
import { BlogSources } from '@/components/blog/BlogSources';
import { BlogAuthorBio } from '@/components/blog/BlogAuthorBio';
import { BlogNewsletterBlock } from '@/components/blog/BlogNewsletterBlock';
import { GuideChapterNavigation } from '@/components/guides/GuideChapterNavigation';
import { GuideDownloadCTA } from '@/components/guides/GuideDownloadCTA';
import { GuideLeadMagnetModal } from '@/components/guides/GuideLeadMagnetModal';

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

  return (
    <article className="min-h-screen bg-white text-zinc-900 selection:bg-zinc-200 pt-20 sm:pt-24">
      <BlogReadingProgress />

      {/* Non-published Preview Banner */}
      {isUnpublished && (
        <div className="bg-amber-500/10 border-b border-amber-500/30 text-amber-900 py-2.5 px-4 text-xs font-mono text-center flex items-center justify-center gap-2">
          <ShieldAlert className="w-4 h-4 text-amber-600" />
          <span>
            <strong>STATUS: {guide.status.toUpperCase()}</strong> &bull; This playbook is excluded from public sitemaps and search indexing (noindex, nofollow).
          </span>
        </div>
      )}

      {/* Breadcrumbs Navigation */}
      <header className="border-b border-zinc-200 bg-[#FBFaf7]">
        <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-4 flex items-center justify-between">
          <div className="flex items-center gap-2 text-xs font-mono text-zinc-500">
            <Link href="/" className="hover:text-zinc-900 transition-colors flex items-center gap-1">
              <Home className="w-3.5 h-3.5" />
              <span>Cora</span>
            </Link>
            <ChevronRight className="w-3 h-3 text-zinc-400" />
            <Link href="/guides" className="hover:text-zinc-900 transition-colors flex items-center gap-1">
              <BookOpen className="w-3.5 h-3.5" />
              <span>Guides</span>
            </Link>
            {categoryObj && (
              <>
                <ChevronRight className="w-3 h-3 text-zinc-400" />
                <span className="text-zinc-900 font-semibold line-clamp-1">{categoryObj.shortName}</span>
              </>
            )}
          </div>

          <div className="flex items-center gap-3">
            <Link
              href="/guides"
              className="text-xs font-bold text-zinc-700 hover:text-zinc-950 font-mono"
            >
              All Playbooks
            </Link>
          </div>
        </div>
      </header>

      {/* Guide Hero Masthead */}
      <header className="bg-[#FBFaf7] border-b border-zinc-200 py-10 sm:py-16">
        <div className="mx-auto max-w-[860px] px-4 sm:px-6">
          <div className="flex flex-wrap items-center gap-2 text-[11px] font-mono mb-4">
            <span className="px-2.5 py-1 rounded-full bg-zinc-200/80 text-zinc-800 font-bold uppercase tracking-wider">
              {guide.qualityLabel || 'Operational Playbook'}
            </span>
            {categoryObj && (
              <span className="font-bold text-zinc-600 uppercase tracking-wider">
                {categoryObj.name}
              </span>
            )}
            <span className="text-zinc-400">&bull;</span>
            <span className="text-zinc-500 flex items-center gap-1">
              <Layers className="w-3.5 h-3.5" />
              <span>{guide.chapterCount} Chapters</span>
            </span>
            <span className="text-zinc-400">&bull;</span>
            <span className="text-zinc-500 flex items-center gap-1">
              <Clock className="w-3.5 h-3.5" />
              <span>{guide.readTime}</span>
            </span>
          </div>

          <h1 className="font-display text-2xl sm:text-4xl lg:text-[42px] font-bold tracking-tight text-zinc-950 leading-[1.16]">
            {guide.title}
          </h1>

          <p className="mt-4 text-base sm:text-lg text-zinc-600 leading-relaxed font-normal">
            {guide.dek}
          </p>

          {/* Author & Timestamp Bar */}
          <div className="mt-8 pt-6 border-t border-zinc-200/80 flex flex-wrap items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center font-display font-bold text-sm">
                {guide.author.name.charAt(0)}
              </div>
              <div>
                <div className="font-bold text-xs sm:text-sm text-zinc-950">
                  {guide.author.name}
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

          {/* Hero Download Asset CTA */}
          {guide.downloadableAsset && (
            <GuideDownloadCTA
              asset={guide.downloadableAsset}
              guideTitle={guide.title}
              guideSlug={guide.slug}
              placement="hero"
              onOpenModal={handleOpenModal}
            />
          )}
        </div>
      </header>

      {/* Main Content Layout with Sticky Chapter Navigation Rail */}
      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-10 sm:py-14">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10">
          {/* Main Guide Body (8 cols) */}
          <main className="lg:col-span-8 lg:col-start-1 max-w-[760px]">
            {/* Mobile Chapter Dropdown */}
            <GuideChapterNavigation chapters={guide.chapters} mode="mobile" />

            {/* Editorial Cover Visual */}
            {guide.coverImage && (
              <div className="mb-10 overflow-hidden rounded-2xl border border-zinc-200/90 bg-[#FBFaf7] shadow-sm">
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
              {guide.chapters.map((chapter, index) => (
                <section
                  key={chapter.slug}
                  id={chapter.slug}
                  className="scroll-mt-28 border-b border-zinc-100 pb-12 last:border-b-0"
                >
                  {/* Chapter Header */}
                  <div className="mb-6">
                    <div className="flex items-center gap-2 text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400 mb-2">
                      <span>CHAPTER {chapter.number}</span>
                      {chapter.readTime && <span>&bull; {chapter.readTime}</span>}
                    </div>

                    <h2 className="font-display text-2xl sm:text-3xl font-bold tracking-tight text-zinc-950">
                      {chapter.title}
                    </h2>

                    {chapter.summary && (
                      <p className="mt-2 text-sm text-zinc-600 leading-relaxed font-medium">
                        {chapter.summary}
                      </p>
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
                </section>
              ))}
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
              <div className="my-10 p-6 rounded-3xl border border-zinc-200 bg-[#FBFaf7] space-y-4">
                <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-500">
                  RECOMMENDED INTERACTIVE TOOLS
                </div>
                <div className="grid gap-3 sm:grid-cols-2">
                  {guide.relatedTools.map((tool) => (
                    <Link
                      key={tool.href}
                      href={tool.href}
                      className="p-4 rounded-2xl border border-zinc-200 bg-white hover:border-zinc-400 transition-all block group"
                    >
                      <div className="flex items-center justify-between text-[10px] font-mono text-zinc-500 mb-1">
                        <span className="font-bold text-zinc-700">{tool.badge}</span>
                        <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                      </div>
                      <div className="font-bold text-sm text-zinc-950 group-hover:text-zinc-700">
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
              <div className="p-5 rounded-2xl border border-zinc-200 bg-[#FBFaf7] text-xs space-y-3">
                <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-500">
                  ABOUT CORA PLAYBOOKS
                </div>
                <p className="text-zinc-600 leading-relaxed">
                  Cora publishes tested operating procedures, contract frameworks, and cash flow systems for creative and technical service businesses.
                </p>
                <div className="pt-2">
                  <Link
                    href="/demo"
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
