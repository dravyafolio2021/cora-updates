import React from 'react';
import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Link from 'next/link';
import { 
  CheckCircle2, 
  XCircle, 
  ArrowRight, 
  Sparkles, 
  ShieldCheck, 
  Zap, 
  HelpCircle,
  TrendingDown,
  Building2,
  Receipt,
  FileText,
  ChevronLeft
} from 'lucide-react';
import { COMPARISONS_DATA } from '@/lib/comparisons-data';

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
      title: 'Comparison Not Found — Cora',
    };
  }

  return {
    title: `${comp.heroHeadline} | Cora vs ${comp.competitorName}`,
    description: comp.heroSubheadline,
    alternates: {
      canonical: `https://heycora.in/compare/${slug}`,
    },
  };
}

export default async function ComparisonDetailPage({ params }: PageProps) {
  const { slug } = await params;
  const comp = COMPARISONS_DATA[slug];

  if (!comp) {
    notFound();
  }

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-24 overflow-hidden bg-white">
      
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
          <span className="font-bold text-emerald-600">CORA</span>
          <span>vs</span>
          <span className="font-bold text-zinc-700">{comp.competitorName.toUpperCase()}</span>
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
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm group"
          >
            <span>Switch to Cora for Free</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
          </a>

          <a
            href="mailto:dravya.bansal@heycora.in?subject=Migration%20from%20Competitor"
            className="inline-flex items-center gap-2 bg-white text-zinc-950 border border-zinc-300 hover:border-zinc-400 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-50 transition-all shadow-2xs"
          >
            <span>Talk to Founder</span>
          </a>
        </div>
      </section>

      {/* ── Pricing & Savings Callout Card ── */}
      <section className="w-full max-w-[1040px] mx-auto px-4 sm:px-6 mb-20">
        <div className="bg-emerald-50/60 rounded-3xl border border-emerald-200/80 p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-xs">
          <div className="space-y-1 text-center md:text-left">
            <span className="text-[10px] font-mono font-bold text-emerald-800 uppercase tracking-wider block">
              FINANCIAL ROI COMPARISON
            </span>
            <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950">
              {comp.priceComparison.savingsPerYear}
            </h3>
            <p className="text-zinc-600 text-xs sm:text-sm">
              Cora: <span className="font-semibold text-zinc-900">{comp.priceComparison.cora}</span> vs {comp.competitorName}: <span className="font-semibold text-zinc-900">{comp.priceComparison.competitor}</span>
            </p>
          </div>

          <div className="shrink-0">
            <span className="inline-flex items-center gap-1.5 bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-xs">
              <TrendingDown className="w-4 h-4" />
              <span>Up to 70% Less Overhead</span>
            </span>
          </div>
        </div>
      </section>

      {/* ── Feature Comparison Matrix Table ── */}
      <section className="w-full max-w-[1040px] mx-auto px-4 sm:px-6 mb-24">
        <div className="text-center max-w-[640px] mx-auto mb-10">
          <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight mb-2">
            Detailed Feature Breakdown
          </h2>
          <p className="text-zinc-500 text-xs sm:text-sm">
            Compare capabilities side-by-side between Cora Studio OS and {comp.competitorName}.
          </p>
        </div>

        <div className="w-full overflow-x-auto rounded-[28px] border border-zinc-200/90 bg-white shadow-[0_10px_30px_rgba(0,0,0,0.04)]">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="bg-zinc-50/80 border-b border-zinc-200 text-zinc-900 text-xs font-bold">
                <th className="p-4 sm:p-5 w-[40%]">Capability / Feature</th>
                <th className="p-4 sm:p-5 w-[30%] bg-emerald-50/50 text-emerald-950 font-display font-black text-sm">
                  Cora OS
                </th>
                <th className="p-4 sm:p-5 w-[30%] text-zinc-600">
                  {comp.competitorName}
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-zinc-100 text-xs">
              {comp.featuresTable.map((row, idx) => (
                <tr key={idx} className="hover:bg-zinc-50/50 transition-colors">
                  <td className="p-4 sm:p-5">
                    <div className="font-semibold text-zinc-900">{row.feature}</div>
                    {row.note && <div className="text-[11px] text-zinc-500 mt-0.5">{row.note}</div>}
                  </td>
                  <td className="p-4 sm:p-5 bg-emerald-50/20 font-semibold text-zinc-950">
                    {typeof row.cora === 'boolean' ? (
                      row.cora ? (
                        <span className="inline-flex items-center gap-1.5 text-emerald-700 font-bold">
                          <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                          <span>Included</span>
                        </span>
                      ) : (
                        <span className="inline-flex items-center gap-1.5 text-zinc-400">
                          <XCircle className="w-4 h-4 text-zinc-300 shrink-0" />
                          <span>No</span>
                        </span>
                      )
                    ) : (
                      <span className="text-emerald-800 font-bold flex items-center gap-1.5">
                        <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                        {row.cora}
                      </span>
                    )}
                  </td>
                  <td className="p-4 sm:p-5 text-zinc-600">
                    {typeof row.competitor === 'boolean' ? (
                      row.competitor ? (
                        <span className="inline-flex items-center gap-1.5 text-zinc-700 font-medium">
                          <CheckCircle2 className="w-4 h-4 text-zinc-400 shrink-0" />
                          <span>Yes</span>
                        </span>
                      ) : (
                        <span className="inline-flex items-center gap-1.5 text-rose-500 font-medium">
                          <XCircle className="w-4 h-4 text-rose-400 shrink-0" />
                          <span>Missing</span>
                        </span>
                      )
                    ) : (
                      <span className="text-zinc-600">{row.competitor}</span>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* ── Key Advantages / Why Switch ── */}
      <section className="w-full max-w-[1040px] mx-auto px-4 sm:px-6 mb-24">
        <div className="text-center max-w-[640px] mx-auto mb-12">
          <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight mb-2">
            Why Teams Are Migrating to Cora
          </h2>
          <p className="text-zinc-500 text-xs sm:text-sm">
            Core reasons studio founders choose Cora over legacy software.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {comp.whySwitchReasons.map((item, idx) => (
            <div 
              key={idx}
              className="p-6 sm:p-7 rounded-3xl bg-zinc-50 border border-zinc-200/80 space-y-3"
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

      {/* ── FAQ Section ── */}
      {comp.faqs.length > 0 && (
        <section className="w-full max-w-[840px] mx-auto px-4 sm:px-6 mb-28">
          <div className="text-center mb-10">
            <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight mb-2">
              Frequently Asked Questions
            </h2>
            <p className="text-zinc-500 text-xs sm:text-sm">
              Answers to common migration questions.
            </p>
          </div>

          <div className="space-y-4">
            {comp.faqs.map((faq, idx) => (
              <div 
                key={idx}
                className="p-5 sm:p-6 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-2"
              >
                <h4 className="font-display text-sm font-bold text-zinc-950 flex items-center gap-2">
                  <HelpCircle className="w-4 h-4 text-emerald-600 shrink-0" />
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

      {/* ── Bottom CTA ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        <div className="w-full rounded-[36px] bg-gradient-to-br from-[#0F172A] via-[#1E293B] to-[#0A0D12] text-white p-8 sm:p-14 text-center relative overflow-hidden border border-zinc-800 shadow-xl">
          <div className="relative z-10 max-w-[680px] mx-auto space-y-6">
            <h2 className="font-display text-3xl sm:text-4xl font-bold tracking-tight">
              Migrate from {comp.competitorName} in under 5 minutes
            </h2>
            <p className="text-zinc-400 text-sm sm:text-base leading-relaxed font-normal">
              Activate your workspace now with 1,000 free operations and 1-click data import. No credit card required.
            </p>

            <div className="flex items-center justify-center flex-wrap gap-3.5 pt-2">
              <a
                href={`https://app.heycora.in/workspace/login?source=compare_bottom_${comp.slug}`}
                className="inline-flex items-center gap-2 bg-white text-zinc-950 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm group"
              >
                <span>Get started for Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-600 group-hover:translate-x-0.5 transition-transform" />
              </a>

              <a
                href="mailto:dravya.bansal@heycora.in?subject=Competitor%20Migration%20Assistance"
                className="inline-flex items-center gap-2 bg-zinc-900 text-white border border-zinc-700 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
              >
                <span>Chat with Founder</span>
              </a>
            </div>
          </div>
        </div>
      </section>

    </main>
  );
}
