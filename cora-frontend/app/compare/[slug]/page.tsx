import React from 'react';
import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Link from 'next/link';
import { 
  CheckCircle2, 
  ArrowRight, 
  Sparkles, 
  ChevronLeft,
  HelpCircle,
  ShieldCheck,
  TrendingDown
} from 'lucide-react';
import { COMPARISONS_DATA } from '@/lib/comparisons-data';
import { ComparisonVerdictBox } from '@/components/comparison/ComparisonVerdictBox';
import { ComparisonConsolidationStack } from '@/components/comparison/ComparisonConsolidationStack';
import { ComparisonCategorizedMatrix } from '@/components/comparison/ComparisonCategorizedMatrix';
import { ComparisonMigrationRoadmap } from '@/components/comparison/ComparisonMigrationRoadmap';
import { ComparisonCtaBanner } from '@/components/comparison/ComparisonCtaBanner';

interface PageProps {
  params: Promise<{ slug: string }>;
}

export async function generateStaticParams() {
  return Object.keys(COMPARISONS_DATA).map((slug) => ({
    slug,
  }));
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;
  const comp = COMPARISONS_DATA[slug];

  if (!comp) {
    return {
      title: 'Comparison Not Found — Cora OS',
    };
  }

  return {
    title: `${comp.heroHeadline} | Cora vs ${comp.competitorName}`,
    description: comp.heroSubheadline,
    keywords: [
      `Cora vs ${comp.competitorName}`,
      `${comp.competitorName} alternative`,
      `${comp.competitorName} competitor`,
      'agency operating system',
      'studio CRM comparison',
      '18% GST invoicing software',
      'SHA-256 e-sign contracts',
      'autonomous AI co-founder',
      comp.category
    ],
    alternates: {
      canonical: `https://heycora.in/compare/${slug}`,
    },
    openGraph: {
      title: `${comp.heroHeadline} | Cora vs ${comp.competitorName}`,
      description: comp.heroSubheadline,
      url: `https://heycora.in/compare/${slug}`,
      siteName: 'Cora',
      type: 'article',
      images: [
        {
          url: '/og-image.png',
          width: 1200,
          height: 630,
          alt: `Cora vs ${comp.competitorName} Comparison`,
        },
      ],
    },
    twitter: {
      card: 'summary_large_image',
      title: `${comp.heroHeadline} | Cora vs ${comp.competitorName}`,
      description: comp.heroSubheadline,
      images: ['/og-image.png'],
    },
  };
}

export default async function ComparisonDetailPage({ params }: PageProps) {
  const { slug } = await params;
  const comp = COMPARISONS_DATA[slug];

  if (!comp) {
    notFound();
  }

  const canonical = `https://heycora.in/compare/${comp.slug}`;
  const schemas = [
    {
      '@context': 'https://schema.org',
      '@type': 'BreadcrumbList',
      itemListElement: [
        { '@type': 'ListItem', position: 1, name: 'Home', item: 'https://heycora.in/' },
        { '@type': 'ListItem', position: 2, name: 'Competitor Comparisons', item: 'https://heycora.in/compare' },
        { '@type': 'ListItem', position: 3, name: `Cora vs ${comp.competitorName}`, item: canonical },
      ],
    },
    {
      '@context': 'https://schema.org',
      '@type': 'Product',
      name: 'Cora Platform',
      category: 'Agency & Studio Operating System',
      description: comp.heroSubheadline,
      brand: { '@type': 'Brand', name: 'Cora' },
      offers: {
        '@type': 'Offer',
        price: '2999',
        priceCurrency: 'INR',
        availability: 'https://schema.org/InStock',
        url: 'https://heycora.in/pricing'
      }
    },
    {
      '@context': 'https://schema.org',
      '@type': 'FAQPage',
      mainEntity: comp.faqs.map((faq) => ({
        '@type': 'Question',
        name: faq.q,
        acceptedAnswer: { '@type': 'Answer', text: faq.a },
      })),
    },
  ];

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-24 overflow-hidden bg-white">
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schemas) }} />
      
      {/* ── Breadcrumb & Back Link ── */}
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-8">
        <Link 
          href="/compare"
          className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors"
        >
          <ChevronLeft className="w-4 h-4" />
          <span>All Comparisons</span>
        </Link>
      </div>

      {/* ── Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-16 sm:mb-20">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-xl border border-zinc-200/80 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <span className="font-bold text-emerald-600 font-mono">CORA PLATFORM</span>
          <span className="text-zinc-400">vs</span>
          <span className="font-bold text-zinc-800 uppercase font-mono">{comp.competitorName}</span>
        </div>

        <h1 className="font-display text-3xl xs:text-4xl sm:text-5xl md:text-6xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.035em] max-w-[980px] mx-auto mb-6">
          {comp.heroHeadline}
        </h1>

        <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[760px] mx-auto mb-8">
          {comp.heroSubheadline}
        </p>

        <div className="flex items-center justify-center flex-wrap gap-3.5">
          <a
            href={`https://app.heycora.in/workspace/login?source=compare_${comp.slug}`}
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm group cursor-pointer"
          >
            <span>Switch to Cora for Free</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
          </a>

          <Link
            href="/contact"
            className="inline-flex items-center gap-2 bg-white text-zinc-950 border border-zinc-300 hover:border-zinc-400 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-50 transition-all shadow-2xs"
          >
            <span>Talk to Founder</span>
          </Link>
        </div>
      </section>

      {/* ── Quick GEO Executive Verdict Box (Targeting AI Search Overviews) ── */}
      <ComparisonVerdictBox comp={comp} />

      {/* ── Tool Stack Consolidation & Savings Stack ── */}
      <ComparisonConsolidationStack comp={comp} />

      {/* ── Categorized Side-by-Side Feature Matrix ── */}
      <ComparisonCategorizedMatrix comp={comp} />

      {/* ── Key Advantages & Why Switch Reasons ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-28">
        <div className="text-center max-w-[640px] mx-auto mb-12">
          <span className="text-[11px] font-mono font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/60 inline-block mb-3">
            CORE ADVANTAGES
          </span>
          <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight mb-2">
            Why Agency Founders Are Migrating to Cora
          </h2>
          <p className="text-zinc-500 text-xs sm:text-sm">
            Proven operational benefits of unifying contracts, AI triage, and invoicing into one workspace.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {comp.whySwitchReasons.map((item, idx) => (
            <div 
              key={idx}
              className="p-6 sm:p-7 rounded-3xl bg-zinc-50 border border-zinc-200/80 space-y-3 hover:border-zinc-300 transition-all"
            >
              <div className="w-8 h-8 rounded-xl bg-zinc-950 text-white flex items-center justify-center font-mono text-xs font-bold">
                0{idx + 1}
              </div>
              <h3 className="font-display text-base font-bold text-zinc-950">
                {item.title}
              </h3>
              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                {item.desc}
              </p>
            </div>
          ))}
        </div>
      </section>

      {/* ── Zero-Downtime 5-Minute Migration Roadmap ── */}
      <ComparisonMigrationRoadmap comp={comp} />

      {/* ── FAQ Section (GEO Optimized with Direct Factual Q&A) ── */}
      {comp.faqs.length > 0 && (
        <section className="w-full max-w-[900px] mx-auto px-4 sm:px-6 mb-28">
          <div className="text-center mb-10">
            <span className="text-[11px] font-mono font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/60 inline-block mb-3">
              MIGRATION FAQ
            </span>
            <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight mb-2">
              Frequently Asked Questions
            </h2>
            <p className="text-zinc-500 text-xs sm:text-sm">
              Direct, factual answers regarding migration, legal compliance, and workspace security.
            </p>
          </div>

          <div className="space-y-4">
            {comp.faqs.map((faq, idx) => (
              <div 
                key={idx}
                className="p-5 sm:p-6 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-2"
              >
                <h4 className="font-display text-sm sm:text-base font-bold text-zinc-950 flex items-start gap-2.5">
                  <HelpCircle className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>{faq.q}</span>
                </h4>
                <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed pl-6">
                  {faq.a}
                </p>
              </div>
            ))}
          </div>
        </section>
      )}

      {/* ── Bottom High-Converting Conversion CTA Banner ── */}
      <ComparisonCtaBanner comp={comp} />

    </main>
  );
}
