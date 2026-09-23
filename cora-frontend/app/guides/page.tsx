import type { Metadata } from 'next';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, BookOpen, Clock, Download, Layers, Sparkles } from 'lucide-react';
import { getAllGuides, getFeaturedGuide } from '@/lib/guides-data';
import { GuideCard } from '@/components/guides/GuideCard';
import { GuideHeader } from '@/components/guides/GuideHeader';
import { BlogNewsletterBlock } from '@/components/blog/BlogNewsletterBlock';

export const metadata: Metadata = {
  title: 'Guides & Operating Playbooks | Cora',
  description:
    'Comprehensive chaptered playbooks, contract frameworks, client onboarding systems, and downloadable SOP packs for service agencies.',
  alternates: {
    canonical: 'https://heycora.in/guides/',
  },
  openGraph: {
    title: 'Guides & Operating Playbooks | Cora',
    description:
      'Comprehensive chaptered playbooks, contract frameworks, and downloadable SOP packs for service agencies.',
    url: 'https://heycora.in/guides/',
    siteName: 'Cora',
    type: 'website',
    images: [{ url: 'https://heycora.in/images/cora_pricing_pure_sky.jpg' }],
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Guides & Operating Playbooks | Cora',
    description:
      'Comprehensive chaptered playbooks, contract frameworks, and downloadable SOP packs for service agencies.',
  },
};

export default function GuidesHubPage() {
  const publishedGuides = getAllGuides(false);
  const featuredGuide = getFeaturedGuide(false);
  const allGuidesIncludingReview = getAllGuides(true);

  return (
    <main className="min-h-screen bg-white text-zinc-900 selection:bg-zinc-200">
      <GuideHeader />

      {/* Hero Masthead */}
      <section className="border-b border-zinc-200 bg-[#FBFaf7] py-12 sm:py-16">
        <div className="mx-auto max-w-[1240px] px-4 sm:px-6">
          <div className="flex items-center gap-2 text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 mb-4">
            <Sparkles className="w-3.5 h-3.5 text-zinc-600" />
            <span>OPERATIONAL PLAYBOOKS &amp; SOP LIBRARY</span>
          </div>

          <h1 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 max-w-3xl leading-[1.12]">
            In-Depth Operating Guides for Modern Service Agencies
          </h1>

          <p className="mt-4 text-base sm:text-lg text-zinc-600 max-w-2xl leading-relaxed">
            Step-by-step chaptered playbooks on client onboarding, contract architecture, scope control, and retainer governance — accompanied by downloadable SOP packs.
          </p>
        </div>
      </section>

      {/* Main Guides Directory */}
      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-10 sm:py-14">
        {/* If published guides exist */}
        {publishedGuides.length > 0 ? (
          <div className="space-y-12">
            {/* Featured Hero Guide */}
            {featuredGuide && (
              <div className="rounded-3xl border border-zinc-200 bg-[#FBFaf7] p-6 sm:p-10 shadow-sm">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                  <div className="lg:col-span-7 space-y-4">
                    <div className="flex items-center gap-2 text-[11px] font-mono font-bold text-zinc-500">
                      <span className="px-2.5 py-0.5 rounded-full bg-zinc-200/80 text-zinc-800 uppercase">
                        FEATURED PLAYBOOK
                      </span>
                      <span>&bull;</span>
                      <span>{featuredGuide.chapterCount} Chapters</span>
                    </div>

                    <h2 className="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-zinc-950 tracking-tight leading-tight">
                      <Link href={`/guides/${featuredGuide.slug}/`} className="hover:text-zinc-700 transition-colors">
                        {featuredGuide.title}
                      </Link>
                    </h2>

                    <p className="text-sm sm:text-base text-zinc-600 leading-relaxed">
                      {featuredGuide.dek}
                    </p>

                    <div className="pt-2 flex flex-wrap items-center gap-4">
                      <Link
                        href={`/guides/${featuredGuide.slug}/`}
                        className="px-5 py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all flex items-center gap-2 shadow-sm"
                      >
                        <span>Read Playbook</span>
                        <ArrowRight className="w-4 h-4" />
                      </Link>

                      {featuredGuide.downloadableAsset && (
                        <div className="text-xs font-mono text-zinc-600 flex items-center gap-1.5">
                          <Download className="w-3.5 h-3.5" />
                          <span>Includes {featuredGuide.downloadableAsset.fileType.toUpperCase()} SOP Pack</span>
                        </div>
                      )}
                    </div>
                  </div>

                  {featuredGuide.coverImage && (
                    <div className="lg:col-span-5">
                      <Link
                        href={`/guides/${featuredGuide.slug}/`}
                        className="block aspect-[16/9] w-full rounded-2xl overflow-hidden border border-zinc-200 relative shadow-sm"
                      >
                        <Image
                          src={featuredGuide.coverImage}
                          alt={featuredGuide.coverAlt || featuredGuide.title}
                          fill
                          sizes="(max-width: 1024px) 100vw, 500px"
                          className="object-cover hover:scale-[1.02] transition-transform duration-300"
                        />
                      </Link>
                    </div>
                  )}
                </div>
              </div>
            )}

            {/* Other Published Guides Grid */}
            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {publishedGuides
                .filter((g) => g.slug !== featuredGuide?.slug)
                .map((guide) => (
                  <GuideCard key={guide.slug} guide={guide} />
                ))}
            </div>
          </div>
        ) : (
          /* When launch guides are currently undergoing editorial review */
          <div className="space-y-10">
            <div className="p-8 sm:p-12 rounded-3xl border border-zinc-200 bg-[#FBFaf7] text-center max-w-2xl mx-auto">
              <div className="w-12 h-12 rounded-2xl bg-zinc-950 text-white flex items-center justify-center mx-auto mb-4">
                <BookOpen className="w-6 h-6" />
              </div>

              <h2 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-950">
                New Operational Playbooks In Final Editorial Review
              </h2>

              <p className="mt-3 text-xs sm:text-sm text-zinc-600 leading-relaxed">
                We are currently finalizing our flagship chaptered playbooks on agency client onboarding, contract architecture, and cash flow workflows. Subscribe below to receive early download access when published.
              </p>

              <div className="mt-6 max-w-md mx-auto">
                <BlogNewsletterBlock placement="inline" heading="Get early access to agency playbooks" />
              </div>
            </div>

            {/* Internal Preview Access for In-Review Playbooks */}
            {allGuidesIncludingReview.length > 0 && (
              <div className="pt-6">
                <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400 mb-4">
                  PLAYBOOKS CURRENTLY IN EDITORIAL REVIEW:
                </div>
                <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                  {allGuidesIncludingReview.map((guide) => (
                    <GuideCard key={guide.slug} guide={guide} />
                  ))}
                </div>
              </div>
            )}
          </div>
        )}
      </div>
    </main>
  );
}
