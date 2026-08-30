import React from 'react';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import type { Metadata } from 'next';
import { 
  ArrowRight, 
  CheckCircle2, 
  Copy, 
  Zap, 
  ShieldCheck, 
  Code, 
  ChevronRight, 
  Receipt, 
  FileText, 
  ExternalLink,
  MessageSquare,
  Building2,
  Sparkles
} from 'lucide-react';
import { INTEGRATIONS_LIST, IntegrationData } from '@/lib/integrations-data';

export async function generateStaticParams() {
  return INTEGRATIONS_LIST.map((item) => ({
    slug: item.slug,
  }));
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ slug: string }>;
}): Promise<Metadata> {
  const { slug } = await params;
  const integration = INTEGRATIONS_LIST.find((item) => item.slug === slug);

  if (!integration) {
    return {
      title: 'Integration Not Found — Cora Studio OS',
    };
  }

  return {
    title: `${integration.name} CRM & Invoicing Integration — Cora Studio OS`,
    description: integration.heroDescription,
    keywords: [
      `${integration.name} CRM integration`,
      `${integration.name} client portal`,
      `${integration.name} 18% GST invoices`,
      `${integration.name} e-signature contracts`,
      `${integration.name} studio booking embed`,
    ],
    alternates: {
      canonical: `https://heycora.in/integrations/${integration.slug}/`,
    },
    openGraph: {
      title: `${integration.name} + Cora: ${integration.tagline}`,
      description: integration.heroDescription,
      url: `https://heycora.in/integrations/${integration.slug}`,
      type: 'article',
    },
  };
}

export default async function IntegrationDetailPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const integration = INTEGRATIONS_LIST.find((item) => item.slug === slug);

  if (!integration) {
    notFound();
  }

  // Generate Breadcrumbs & FAQ JSON-LD Schema
  const breadcrumbSchema = {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
      {
        '@type': 'ListItem',
        position: 1,
        name: 'Home',
        item: 'https://heycora.in',
      },
      {
        '@type': 'ListItem',
        position: 2,
        name: 'Integrations',
        item: 'https://heycora.in/integrations',
      },
      {
        '@type': 'ListItem',
        position: 3,
        name: `${integration.name} Integration`,
        item: `https://heycora.in/integrations/${integration.slug}`,
      },
    ],
  };

  const faqSchema = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: integration.faqs.map((faq) => ({
      '@type': 'Question',
      name: faq.question,
      acceptedAnswer: {
        '@type': 'Answer',
        text: faq.answer,
      },
    })),
  };

  return (
    <main className="min-h-screen bg-white text-zinc-950 selection:bg-zinc-950 selection:text-white pt-28 sm:pt-36 pb-24">
      
      {/* Schema.org Injected Entity Graph */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify([breadcrumbSchema, faqSchema]) }}
      />

      <div className="max-w-5xl mx-auto px-4 sm:px-6">
        
        {/* ── Breadcrumb Hierarchy ──────────────────────────────────────── */}
        <nav className="flex items-center gap-2 text-xs text-zinc-500 font-mono mb-8">
          <Link href="/" className="hover:text-zinc-950 transition-colors">Home</Link>
          <span>/</span>
          <Link href="/integrations" className="hover:text-zinc-950 transition-colors">Integrations</Link>
          <span>/</span>
          <span className="text-zinc-950 font-bold">{integration.name}</span>
        </nav>

        {/* ── 1. Hero Header ────────────────────────────────────────────── */}
        <div className="space-y-6">
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 rounded-full text-xs font-semibold text-zinc-800 border border-zinc-200">
            <span className="w-2 h-2 rounded-full bg-emerald-500" />
            <span>Official {integration.name} Connector</span>
          </div>

          <h1 className="font-display text-3xl sm:text-5xl md:text-6xl font-bold tracking-tight text-zinc-950 leading-[1.15]">
            {integration.tagline}
          </h1>

          <p className="text-base sm:text-lg text-zinc-600 max-w-3xl leading-relaxed">
            {integration.heroDescription}
          </p>

          {/* Quick Metrics Bar */}
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 font-mono text-xs max-w-3xl">
            <div className="p-3 bg-white rounded-xl border border-zinc-100">
              <span className="text-[10px] text-zinc-400 block font-bold uppercase">SETUP TIME</span>
              <span className="text-sm font-bold text-zinc-900">{integration.stats.setupTime}</span>
            </div>
            <div className="p-3 bg-white rounded-xl border border-zinc-100">
              <span className="text-[10px] text-zinc-400 block font-bold uppercase">PLUGINS REPLACED</span>
              <span className="text-sm font-bold text-zinc-900">{integration.stats.pluginsReplaced}</span>
            </div>
            <div className="p-3 bg-white rounded-xl border border-zinc-100">
              <span className="text-[10px] text-zinc-400 block font-bold uppercase">ESTIMATED ROI</span>
              <span className="text-sm font-bold text-emerald-700">{integration.stats.monthlySavings}</span>
            </div>
          </div>

          <div className="pt-2 flex flex-wrap items-center gap-3">
            <Link
              href="/tools/embed-builder"
              className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-3 rounded-xl text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all shadow-sm"
            >
              <span>Get 1-Click Embed Snippet</span>
              <ArrowRight className="w-4 h-4" />
            </Link>
            <a
              href="https://app.heycora.in/workspace/login?source=integration_detail"
              className="inline-flex items-center gap-2 bg-zinc-100 text-zinc-900 px-5 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-200 transition-all"
            >
              <span>Create Free Cora Workspace</span>
            </a>
          </div>
        </div>

        {/* ── 2. The Operational Problem ─────────────────────────────────── */}
        <section className="mt-16 sm:mt-24 pt-12 border-t border-zinc-200">
          <div className="max-w-2xl">
            <span className="text-xs font-mono font-bold text-red-600 uppercase tracking-wider">
              THE BOTTLENECK
            </span>
            <h2 className="text-2xl sm:text-3xl font-bold text-zinc-950 mt-1">
              Why running studio operations directly on {integration.name} breaks down
            </h2>
            <p className="text-sm text-zinc-600 mt-2 leading-relaxed">
              {integration.whyItMatters}
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            {integration.painPoints.map((pain, idx) => (
              <div key={idx} className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-2">
                <div className="text-xs font-mono font-bold text-zinc-400">PAIN 0{idx + 1}</div>
                <h3 className="text-sm font-bold text-zinc-950">{pain.title}</h3>
                <p className="text-xs text-zinc-600 leading-relaxed">{pain.description}</p>
              </div>
            ))}
          </div>
        </section>

        {/* ── 3. Step-by-Step Setup Guide ────────────────────────────────── */}
        <section className="mt-16 sm:mt-24 pt-12 border-t border-zinc-200">
          <div className="max-w-2xl">
            <span className="text-xs font-mono font-bold text-emerald-600 uppercase tracking-wider">
              FAST ONBOARDING
            </span>
            <h2 className="text-2xl sm:text-3xl font-bold text-zinc-950 mt-1">
              How to connect {integration.name} to Cora in 3 simple steps
            </h2>
          </div>

          <div className="space-y-6 mt-8">
            {integration.stepByStepGuide.map((step, idx) => (
              <div key={idx} className="p-6 rounded-2xl bg-white border border-zinc-200/80 space-y-3">
                <div className="flex items-center gap-3">
                  <div className="w-8 h-8 rounded-lg bg-zinc-950 text-white font-mono font-bold text-xs flex items-center justify-center">
                    {step.step}
                  </div>
                  <h3 className="text-base font-bold text-zinc-950">{step.title}</h3>
                </div>
                
                <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed pl-11">
                  {step.description}
                </p>

                {step.codeSnippet && (
                  <div className="ml-11 mt-3 p-3 bg-zinc-950 text-zinc-100 rounded-xl font-mono text-xs overflow-x-auto border border-zinc-800">
                    <code>{step.codeSnippet}</code>
                  </div>
                )}
              </div>
            ))}
          </div>
        </section>

        {/* ── 4. Key Capabilities Added ─────────────────────────────────── */}
        <section className="mt-16 sm:mt-24 pt-12 border-t border-zinc-200">
          <h2 className="text-2xl sm:text-3xl font-bold text-zinc-950">
            What Cora adds to your {integration.name} website
          </h2>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            {integration.capabilities.map((cap, idx) => (
              <div key={idx} className="p-6 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-3">
                <div className="w-10 h-10 rounded-xl bg-white text-zinc-950 flex items-center justify-center border border-zinc-200 shadow-2xs">
                  <Sparkles className="w-5 h-5 text-emerald-600" />
                </div>
                <h3 className="text-sm font-bold text-zinc-950">{cap.title}</h3>
                <p className="text-xs text-zinc-600 leading-relaxed">{cap.description}</p>
              </div>
            ))}
          </div>
        </section>

        {integration.slug === 'wordpress' && (
          <section className="mt-16 sm:mt-24 pt-12 border-t border-zinc-200">
            <span className="text-xs font-mono font-bold text-emerald-700 uppercase tracking-wider">
              WORDPRESS DECISION GUIDES
            </span>
            <h2 className="text-2xl sm:text-3xl font-bold text-zinc-950 mt-1 max-w-2xl">
              Not sure whether to connect Cora or replace part of your stack?
            </h2>
            <p className="text-sm text-zinc-600 mt-3 leading-relaxed max-w-2xl">
              Start with the bottleneck you already feel. These practical guides compare the workflows without requiring a full website migration.
            </p>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mt-7">
              {[
                ['/blog/wordpress/alternative-for-agencies/', 'WordPress alternative for agencies'],
                ['/blog/elementor/elementor-alternative-for-agencies/', 'Elementor alternative for agencies'],
                ['/blog/woocommerce/woocommerce-alternative-for-service-businesses/', 'WooCommerce alternative for service businesses'],
                ['/blog/content-marketing/content-publishing-workflow-for-agencies/', 'A simpler agency content workflow'],
              ].map(([href, label]) => (
                <Link key={href} href={href} className="flex items-center justify-between rounded-2xl border border-zinc-200 p-5 text-sm font-bold hover:border-zinc-400 transition-colors">
                  <span>{label}</span>
                  <ArrowRight className="w-4 h-4 text-zinc-400" />
                </Link>
              ))}
            </div>
          </section>
        )}

        {/* ── 5. Frequently Asked Questions ──────────────────────────────── */}
        <section className="mt-16 sm:mt-24 pt-12 border-t border-zinc-200">
          <div className="max-w-2xl">
            <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
              HELP &amp; FAQS
            </span>
            <h2 className="text-2xl sm:text-3xl font-bold text-zinc-950 mt-1">
              Frequently asked questions about {integration.name}
            </h2>
          </div>

          <div className="space-y-4 mt-8">
            {integration.faqs.map((faq, idx) => (
              <div key={idx} className="p-5 rounded-2xl bg-white border border-zinc-200/80 space-y-2">
                <h3 className="text-sm font-bold text-zinc-950">{faq.question}</h3>
                <p className="text-xs text-zinc-600 leading-relaxed">{faq.answer}</p>
              </div>
            ))}
          </div>
        </section>

      </div>
    </main>
  );
}
