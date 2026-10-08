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
  ChevronRight, 
  Check, 
  ExternalLink,
  Code2,
  Copy,
  Bot,
  LayoutTemplate,
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
      title: 'Asset Not Found — Cora',
      description: 'The requested frontend asset could not be found.',
    };
  }

  const url = `https://heycora.in/templates/${template.slug}`;

  return {
    title: `${template.title} — Frontend Asset & AI Prompts | Cora`,
    description: template.heroDescription,
    alternates: { canonical: url },
    openGraph: {
      title: `${template.title} — Cora Frontend Asset`,
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
    (t) => t.id !== template.id && (t.category === template.category || t.assetFormat === template.assetFormat)
  ).slice(0, 3);

  const totalMonthlySavings = template.toolsReplaced.reduce((acc, curr) => acc + curr.monthlyCostUSD, 0);

  const jsonLd = {
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    name: template.title,
    applicationCategory: 'DesignApplication',
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
              <Link href="/templates" className="hover:text-zinc-950 transition-colors">Frontend Assets</Link>
            </li>
            <li><ChevronRight className="w-3.5 h-3.5 text-zinc-300" /></li>
            <li className="text-zinc-950 font-bold truncate max-w-[200px] sm:max-w-none">
              {template.shortTitle}
            </li>
          </ol>
        </nav>

        {/* ── 2. ASSET HERO HEADER ── */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start pb-12 sm:pb-16 border-b border-zinc-100">
          
          {/* Left Column: Title & Metadata */}
          <div className="lg:col-span-7 space-y-4 sm:space-y-6">
            
            <div className="flex flex-wrap items-center gap-2">
              <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-700 bg-zinc-100 px-2.5 py-1 rounded-full border border-zinc-200">
                {template.categoryLabel}
              </span>
              <span className="text-[10px] font-mono font-bold px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 border border-purple-200/60">
                {template.formatBadge}
              </span>
              <span className="text-[10px] font-mono font-bold px-2.5 py-1 rounded-full bg-zinc-200 text-zinc-800">
                {template.badge}
              </span>
            </div>

            <h1 className="font-display text-3xl sm:text-5xl font-bold text-zinc-950 tracking-tight leading-[1.12]">
              {template.title}
            </h1>

            <p className="text-sm sm:text-base text-zinc-600 leading-relaxed font-normal">
              {template.heroDescription}
            </p>

            {/* Compatible Frameworks */}
            <div>
              <div className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2">
                Compatible Builders &amp; Frameworks
              </div>
              <div className="flex flex-wrap gap-2">
                {template.compatibleBuilders.map((builder, idx) => (
                  <span key={idx} className="text-xs font-mono text-zinc-800 bg-zinc-50 px-3 py-1 rounded-lg border border-zinc-200 font-medium">
                    {builder}
                  </span>
                ))}
              </div>
            </div>

            {/* Trust Metrics Pill Bar */}
            <div className="flex flex-wrap items-center gap-4 sm:gap-6 pt-2 text-xs font-medium text-zinc-600">
              <div className="flex items-center gap-1.5">
                <Star className="w-4 h-4 text-amber-500 fill-amber-500" />
                <span className="font-bold text-zinc-950">{template.rating}</span>
                <span className="text-zinc-400">({template.reviewsCount} reviews)</span>
              </div>
              <div className="flex items-center gap-1.5">
                <Download className="w-4 h-4 text-zinc-400" />
                <span>{template.downloadsCount.toLocaleString()}+ installs</span>
              </div>
              <div className="flex items-center gap-1.5">
                <Layers className="w-4 h-4 text-zinc-400" />
                <span>{template.sectionsIncluded.length} Ready Sections</span>
              </div>
            </div>

            {/* Primary Action Buttons */}
            <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-4">
              <a
                href="#sections-breakdown"
                className="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all shadow-sm group"
              >
                <Bot className="w-4 h-4 text-purple-400" />
                <span>Inspect AI Prompts &amp; Code</span>
                <ArrowRight className="w-4 h-4 transition-transform group-hover:translate-x-0.5" />
              </a>

              <a
                href="/templates"
                className="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-900 text-xs sm:text-sm font-bold transition-all"
              >
                <span>Browse All Assets</span>
              </a>
            </div>

          </div>

          {/* Right Column: Visual Preview Card & Replaced Tools */}
          <div className="lg:col-span-5 space-y-6">
            
            {/* Visual Preview Box */}
            <div className="rounded-3xl bg-zinc-50 border border-zinc-200/90 p-5 sm:p-6 shadow-sm">
              <div className="flex items-center justify-between mb-4">
                <span className="text-xs font-mono font-bold text-zinc-500 uppercase tracking-wider">
                  Asset Blueprint Box
                </span>
                <span className="text-[10px] font-mono text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-full font-bold">
                  MIT License / Free
                </span>
              </div>

              <div className="space-y-3">
                {template.sectionsIncluded.map((sec, idx) => (
                  <div key={sec.id} className="flex items-start justify-between p-3 rounded-xl bg-white border border-zinc-200/70 shadow-2xs">
                    <div className="flex items-start gap-2.5">
                      <div className="w-6 h-6 rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center text-[11px] font-bold text-zinc-700 shrink-0 mt-0.5">
                        {idx + 1}
                      </div>
                      <div>
                        <div className="text-xs font-bold text-zinc-900">{sec.name}</div>
                        <div className="text-[10.5px] text-zinc-500">{sec.badge}</div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Savings Box */}
            <div className="rounded-3xl bg-zinc-50 border border-zinc-200/90 p-5 sm:p-6">
              <div className="flex items-center justify-between mb-3">
                <span className="text-xs font-bold text-zinc-950">Subscription Tools Replaced</span>
                <span className="text-xs font-mono font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                  Saves ~${totalMonthlySavings}/mo
                </span>
              </div>
              <ul className="space-y-2">
                {template.toolsReplaced.map((tool, idx) => (
                  <li key={idx} className="flex items-center justify-between text-xs text-zinc-600 bg-white p-2.5 rounded-xl border border-zinc-200/60">
                    <span className="font-medium text-zinc-800">{tool.name}</span>
                    <span className="font-mono text-zinc-500">${tool.monthlyCostUSD}/mo</span>
                  </li>
                ))}
              </ul>
            </div>

          </div>

        </div>

        {/* ── 3. DETAILED SECTIONS & AI PROMPT VIEWER ── */}
        <section id="sections-breakdown" className="py-12 sm:py-16 border-b border-zinc-100">
          <div className="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
            <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-3 py-1 rounded-full border border-zinc-200 mb-3 inline-block">
              Section Blueprints
            </span>
            <h2 className="font-display text-2xl sm:text-4xl font-bold text-zinc-950 tracking-tight mb-2">
              Included Section Prompts &amp; Code
            </h2>
            <p className="text-xs sm:text-sm text-zinc-600 font-normal">
              Copy any prompt directly into Cursor, Claude 3.5 Sonnet, Lovable, or v0 to generate clean responsive UI.
            </p>
          </div>

          <div className="space-y-6">
            {template.sectionsIncluded.map((sec, idx) => (
              <div key={sec.id} className="p-5 sm:p-7 rounded-3xl bg-zinc-50/80 border border-zinc-200/90 space-y-4">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                  <div className="flex items-center gap-3">
                    <span className="w-7 h-7 rounded-xl bg-zinc-950 text-white font-mono text-xs font-bold flex items-center justify-center shrink-0">
                      {idx + 1}
                    </span>
                    <div>
                      <h3 className="text-sm sm:text-base font-bold text-zinc-950">{sec.name}</h3>
                      <p className="text-xs text-zinc-500">{sec.description}</p>
                    </div>
                  </div>
                  <span className="text-[10px] font-mono bg-purple-100 text-purple-800 px-2.5 py-1 rounded-full font-bold self-start sm:self-auto">
                    {sec.badge}
                  </span>
                </div>

                {/* AI Prompt Box */}
                <div className="space-y-1.5">
                  <div className="text-[11px] font-mono font-bold text-zinc-500 uppercase tracking-wider flex items-center gap-1.5">
                    <Bot className="w-3.5 h-3.5 text-purple-600" />
                    <span>Engineered AI Prompt (Claude &amp; Cursor)</span>
                  </div>
                  <div className="p-4 bg-zinc-900 text-zinc-200 rounded-2xl font-mono text-xs leading-relaxed overflow-x-auto whitespace-pre-wrap border border-zinc-800 select-all">
                    {sec.promptText}
                  </div>
                </div>

                {/* Tailwind / Blueprint Snippet if available */}
                {(sec.tailwindSnippet || sec.elementorBlueprint) && (
                  <div className="space-y-1.5 pt-2">
                    <div className="text-[11px] font-mono font-bold text-zinc-500 uppercase tracking-wider flex items-center gap-1.5">
                      <Code2 className="w-3.5 h-3.5 text-zinc-700" />
                      <span>{sec.tailwindSnippet ? 'Tailwind / React Component Snippet' : 'Elementor Blueprint JSON'}</span>
                    </div>
                    <pre className="p-4 bg-zinc-950 text-emerald-400 rounded-2xl font-mono text-xs leading-relaxed overflow-x-auto max-h-56 whitespace-pre border border-zinc-800">
                      {sec.tailwindSnippet || sec.elementorBlueprint}
                    </pre>
                  </div>
                )}
              </div>
            ))}
          </div>
        </section>

        {/* ── 4. KEY CAPABILITIES & FEATURES ── */}
        <section className="py-12 sm:py-16 border-b border-zinc-100">
          <div className="max-w-3xl mx-auto space-y-6">
            <h2 className="font-display text-xl sm:text-3xl font-bold text-zinc-950 tracking-tight text-center">
              Frontend Quality Standards
            </h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
              {template.features.map((feature, idx) => (
                <div key={idx} className="flex items-start gap-3 p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span className="text-xs sm:text-sm font-medium text-zinc-800 leading-snug">{feature}</span>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* ── 5. RELATED ASSETS ── */}
        {relatedTemplates.length > 0 && (
          <section className="py-12 sm:py-16">
            <div className="flex items-center justify-between mb-8">
              <div>
                <h2 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
                  Related Frontend Assets
                </h2>
                <span className="text-xs text-zinc-500">
                  Explore other high-converting UI kits and prompts
                </span>
              </div>
              <Link
                href="/templates"
                className="text-xs font-bold text-zinc-900 hover:text-black inline-flex items-center gap-1 transition-colors"
              >
                <span>View All</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </Link>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
              {relatedTemplates.map((rel) => (
                <TemplateCard key={rel.id} template={rel} />
              ))}
            </div>
          </section>
        )}

      </div>
    </main>
  );
}
