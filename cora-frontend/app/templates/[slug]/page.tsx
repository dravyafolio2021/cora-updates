import React from 'react';
import type { Metadata } from 'next';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { 
  TEMPLATES_DATA, 
  TemplateItem 
} from '@/lib/templates-data';
import { 
  Sparkles, 
  ArrowRight, 
  CheckCircle2, 
  Star, 
  Download, 
  Layers, 
  ShieldCheck, 
  Receipt, 
  FolderKanban, 
  ChevronRight, 
  Check, 
  ExternalLink,
  Code2,
  Calendar,
  Zap,
  Globe
} from 'lucide-react';
import { TemplateCard } from '@/components/templates/TemplateCard';

interface TemplatePageProps {
  params: Promise<{ slug: string }>;
}

export async function generateStaticParams() {
  return TEMPLATES_DATA.map((t) => ({
    slug: t.slug,
  }));
}

export async function generateMetadata({ params }: TemplatePageProps): Promise<Metadata> {
  const { slug } = await params;
  const template = TEMPLATES_DATA.find((t) => t.slug === slug);

  if (!template) {
    return {
      title: 'Template Not Found — Cora',
      description: 'The requested turnkey workspace template could not be found.',
    };
  }

  const url = `https://heycora.in/templates/${template.slug}`;

  return {
    title: `${template.title} — Turnkey Workspace Schema & Website Kit | Cora`,
    description: template.heroDescription,
    alternates: { canonical: url },
    openGraph: {
      title: `${template.title} — Cora Turnkey Kit`,
      description: template.heroDescription,
      url,
      siteName: 'Cora',
      type: 'website',
    },
  };
}

export default async function TemplateDetailPage({ params }: TemplatePageProps) {
  const { slug } = await params;
  const template = TEMPLATES_DATA.find((t) => t.slug === slug);

  if (!template) {
    notFound();
  }

  const relatedTemplates = TEMPLATES_DATA.filter(
    (t) => t.id !== template.id && (t.category === template.category || t.sacCode === template.sacCode)
  ).slice(0, 3);

  const totalMonthlySavings = template.toolsReplaced.reduce((acc, curr) => acc + curr.monthlyCostUSD, 0);

  const jsonLd = {
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    name: template.title,
    applicationCategory: 'BusinessApplication',
    operatingSystem: 'Web, Cloud',
    description: template.heroDescription,
    offers: {
      '@type': 'Offer',
      price: '0.00',
      priceCurrency: 'USD',
      availability: 'https://schema.org/InStock',
    },
    aggregateRating: {
      '@type': 'AggregateRating',
      ratingValue: template.rating.toString(),
      reviewCount: template.reviewsCount.toString(),
    },
  };

  return (
    <main className="w-full bg-white text-zinc-900 min-h-screen selection:bg-zinc-900 selection:text-white pt-24 sm:pt-32 pb-20">
      
      {/* JSON-LD Schema */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
      />

      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. BREADCRUMBS ── */}
        <nav aria-label="Breadcrumb" className="mb-6 sm:mb-8">
          <ol className="flex items-center gap-2 text-xs font-medium text-zinc-500">
            <li>
              <Link href="/" className="hover:text-zinc-950 transition-colors">Home</Link>
            </li>
            <li><ChevronRight className="w-3.5 h-3.5 text-zinc-300" /></li>
            <li>
              <Link href="/templates" className="hover:text-zinc-950 transition-colors">Templates &amp; Themes</Link>
            </li>
            <li><ChevronRight className="w-3.5 h-3.5 text-zinc-300" /></li>
            <li className="text-zinc-950 font-bold truncate max-w-[200px] sm:max-w-none">
              {template.shortTitle}
            </li>
          </ol>
        </nav>

        {/* ── 2. HERO SHOWCASE CARD ── */}
        <div className="rounded-3xl bg-zinc-50/80 border border-zinc-200/90 p-6 sm:p-10 mb-12 sm:mb-16 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            {/* Left Header Specs */}
            <div className="lg:col-span-7 space-y-4 text-left">
              
              <div className="flex flex-wrap items-center gap-2">
                <span className="text-[10px] font-mono font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-zinc-200/80 text-zinc-800">
                  {template.categoryLabel}
                </span>
                <span className="text-[10px] font-mono font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200/60">
                  {template.sacCode} ({template.gstRate})
                </span>
                <div className="flex items-center gap-1 text-xs font-semibold text-zinc-700 bg-white px-2.5 py-1 rounded-full border border-zinc-200/80 shadow-2xs">
                  <Star className="w-3.5 h-3.5 text-amber-500 fill-amber-500" />
                  <span>{template.rating}</span>
                  <span className="text-zinc-400">({template.reviewsCount} reviews)</span>
                </div>
              </div>

              <h1 className="font-display text-3xl sm:text-4xl md:text-5xl font-semibold text-zinc-950 tracking-tight leading-[1.12]">
                {template.title}
              </h1>

              <p className="text-sm sm:text-base text-zinc-600 font-normal leading-relaxed">
                {template.heroDescription}
              </p>

              {/* Action Buttons */}
              <div className="flex flex-wrap items-center gap-3 pt-3">
                <a
                  href={`https://app.heycora.in/workspace/login?template=${template.slug}&source=template_hero`}
                  className="px-6 py-3 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs sm:text-sm font-bold transition-all shadow-sm flex items-center gap-2"
                >
                  <Sparkles className="w-4 h-4 text-emerald-400" />
                  <span>Deploy Schema into Workspace</span>
                  <ArrowRight className="w-4 h-4" />
                </a>

                <a
                  href={`https://app.heycora.in/preview/sample-preview?template=${template.slug}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="px-5 py-3 rounded-xl bg-white hover:bg-zinc-100 text-zinc-900 border border-zinc-200 text-xs sm:text-sm font-semibold transition-all shadow-2xs flex items-center gap-1.5"
                >
                  <span>Live Staging Demo</span>
                  <ExternalLink className="w-3.5 h-3.5 text-zinc-500" />
                </a>
              </div>

              {/* Trust Metric Row */}
              <div className="flex items-center gap-5 pt-2 text-xs text-zinc-500 font-mono">
                <span>✓ {template.downloadsCount.toLocaleString()}+ live instances</span>
                <span>✓ Zero plugin fees</span>
                <span>✓ 100% white-label</span>
              </div>

            </div>

            {/* Right Interactive Visual Card */}
            <div className="lg:col-span-5">
              <div className="rounded-2xl bg-white border border-zinc-200/90 p-5 sm:p-6 shadow-md space-y-4 text-left">
                
                <div className="flex items-center justify-between pb-3 border-b border-zinc-100">
                  <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
                    Blueprint Overview
                  </span>
                  <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                    Saves ${totalMonthlySavings}/mo
                  </span>
                </div>

                {/* Included Key Components */}
                <div className="space-y-2 text-xs">
                  <div className="flex items-center justify-between p-2.5 rounded-xl bg-zinc-50">
                    <span className="font-semibold text-zinc-900 flex items-center gap-2">
                      <Layers className="w-4 h-4 text-zinc-500" />
                      Included Pages &amp; Views
                    </span>
                    <span className="font-mono font-bold text-zinc-950">{template.includedPages.length} Pages</span>
                  </div>

                  <div className="flex items-center justify-between p-2.5 rounded-xl bg-zinc-50">
                    <span className="font-semibold text-zinc-900 flex items-center gap-2">
                      <ShieldCheck className="w-4 h-4 text-zinc-500" />
                      E-Sign Contract Template
                    </span>
                    <span className="font-mono font-bold text-emerald-700">SHA-256</span>
                  </div>

                  <div className="flex items-center justify-between p-2.5 rounded-xl bg-zinc-50">
                    <span className="font-semibold text-zinc-900 flex items-center gap-2">
                      <Receipt className="w-4 h-4 text-zinc-500" />
                      GST Invoicing Engine
                    </span>
                    <span className="font-mono font-bold text-zinc-950">{template.gstRate}</span>
                  </div>

                  <div className="flex items-center justify-between p-2.5 rounded-xl bg-zinc-50">
                    <span className="font-semibold text-zinc-900 flex items-center gap-2">
                      <FolderKanban className="w-4 h-4 text-zinc-500" />
                      Database Collections
                    </span>
                    <span className="font-mono font-bold text-zinc-950">{template.schemaStructures.length} Schemas</span>
                  </div>
                </div>

                {/* Subscriptions Replaced List */}
                <div className="pt-2 border-t border-zinc-100">
                  <span className="text-[11px] text-zinc-500 font-medium block mb-2">
                    Eliminates Separate Subscriptions:
                  </span>
                  <div className="flex flex-wrap gap-1.5">
                    {template.toolsReplaced.map((t, idx) => (
                      <span key={idx} className="text-[10px] font-mono text-zinc-700 bg-zinc-100 px-2 py-0.5 rounded border border-zinc-200/60">
                        {t.name}
                      </span>
                    ))}
                  </div>
                </div>

              </div>
            </div>

          </div>
        </div>

        {/* ── 3. INCLUDED PAGES BREAKDOWN ── */}
        <section className="mb-14 sm:mb-20 text-left">
          <div className="mb-6 sm:mb-8">
            <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 block mb-1">
              Architecture Specs
            </span>
            <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
              Pre-Configured Pages &amp; Workflows
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {template.includedPages.map((page, idx) => (
              <div key={idx} className="p-5 rounded-2xl bg-white border border-zinc-200/90 shadow-2xs space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-zinc-100 text-zinc-700 uppercase">
                    {page.type}
                  </span>
                  <span className="text-xs font-mono text-zinc-400">/{page.slug}</span>
                </div>
                <h3 className="text-sm font-bold text-zinc-950">
                  {page.name}
                </h3>
                <p className="text-xs text-zinc-600 leading-relaxed font-normal">
                  {page.description}
                </p>
              </div>
            ))}
          </div>
        </section>

        {/* ── 4. DATABASE & SCHEMA ARCHITECTURE ── */}
        <section className="mb-14 sm:mb-20 text-left">
          <div className="mb-6 sm:mb-8">
            <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 block mb-1">
              Data Model
            </span>
            <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
              Integrated CRM &amp; Database Schemas
            </h2>
          </div>

          <div className="space-y-5">
            {template.schemaStructures.map((schema, idx) => (
              <div key={idx} className="rounded-2xl bg-zinc-50 border border-zinc-200/90 p-5 sm:p-6 space-y-3">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <h3 className="text-sm sm:text-base font-bold text-zinc-950">{schema.name}</h3>
                    <span className="text-[10px] font-mono bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full font-bold">
                      {schema.badge}
                    </span>
                  </div>
                </div>
                <p className="text-xs text-zinc-600">{schema.description}</p>

                <div className="border border-zinc-200 rounded-xl overflow-hidden bg-white">
                  <table className="w-full text-left text-xs">
                    <thead className="bg-zinc-100/80 text-zinc-600 font-mono text-[10.5px]">
                      <tr>
                        <th className="p-3">Field Name</th>
                        <th className="p-3">Data Type</th>
                        <th className="p-3">Required</th>
                        <th className="p-3">Description</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-zinc-100 font-sans">
                      {schema.fields.map((f, fIdx) => (
                        <tr key={fIdx} className="hover:bg-zinc-50/60">
                          <td className="p-3 font-mono font-bold text-zinc-900">{f.name}</td>
                          <td className="p-3 font-mono text-purple-700">{f.type}</td>
                          <td className="p-3 font-mono text-zinc-500">{f.required ? 'Yes' : 'No'}</td>
                          <td className="p-3 text-zinc-600">{f.description}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            ))}
          </div>
        </section>

        {/* ── 5. RELATED TEMPLATES ── */}
        {relatedTemplates.length > 0 && (
          <section className="mb-14 sm:mb-20 text-left">
            <div className="flex items-center justify-between mb-6 sm:mb-8">
              <div>
                <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 block mb-1">
                  Discover More
                </span>
                <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
                  Related Turnkey Templates
                </h2>
              </div>
              <Link
                href="/templates"
                className="text-xs font-bold text-zinc-950 hover:text-black inline-flex items-center gap-1"
              >
                <span>View All 16 Schemas</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </Link>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
              {relatedTemplates.map((rel) => (
                <TemplateCard key={rel.id} template={rel} />
              ))}
            </div>
          </section>
        )}

        {/* ── 6. BOTTOM CALL TO ACTION ── */}
        <div className="rounded-3xl bg-zinc-950 text-white p-8 sm:p-12 text-center relative overflow-hidden shadow-xl">
          <div className="relative z-10 max-w-xl mx-auto space-y-4">
            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-emerald-400 border border-white/15 text-xs font-mono font-bold">
              <Sparkles className="w-3.5 h-3.5" />
              <span>Deploy in 60 seconds</span>
            </span>
            <h2 className="font-display text-2xl sm:text-4xl font-bold tracking-tight">
              Ready to launch your {template.shortTitle} workspace?
            </h2>
            <p className="text-xs sm:text-sm text-zinc-400 leading-relaxed">
              Deploy pre-configured client portals, GST invoicing, and e-signatures into your Cora Workspace with one click.
            </p>
            <div className="pt-2">
              <a
                href={`https://app.heycora.in/workspace/login?template=${template.slug}&source=template_bottom_cta`}
                className="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-zinc-950 text-xs sm:text-sm font-bold hover:bg-zinc-100 transition-all shadow-md"
              >
                <span>Deploy {template.shortTitle} Free</span>
                <ArrowRight className="w-4 h-4" />
              </a>
            </div>
          </div>
        </div>

      </div>

    </main>
  );
}
