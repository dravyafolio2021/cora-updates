import React from 'react';
import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Link from 'next/link';
import { ChevronRight, HelpCircle, ArrowRight } from 'lucide-react';
import { ARTICLES_DATA, getArticleBySlug, getCategoryMetadata } from '@/lib/articles-data';
import { ArticleHero } from '@/components/articles/ArticleHero';
import { ArticleTOC } from '@/components/articles/ArticleTOC';
import { RelatedArticles } from '@/components/articles/RelatedArticles';
import { InteractiveWidget } from '@/components/articles/InteractiveWidgets';
import { EditorialVisualGuide } from '@/components/articles/EditorialVisualGuide';
import { MarkdownRenderer } from '@/components/docs/MarkdownRenderer';
import { generateArticleSchema, generateArticleBreadcrumbs, generateArticleFaqSchema } from '@/lib/seo-schema';

export async function generateStaticParams() {
  return ARTICLES_DATA.map((article) => ({
    category: article.category,
    slug: article.slug,
  }));
}

interface ArticlePageProps {
  params: Promise<{ category: string; slug: string }>;
}

export async function generateMetadata({ params }: ArticlePageProps): Promise<Metadata> {
  const { category, slug } = await params;
  const article = getArticleBySlug(slug);

  if (!article || article.category !== category) {
    return { title: 'Article Not Found | Cora', robots: { index: false, follow: false } };
  }

  const canonical = `https://heycora.in/articles/${article.category}/${article.slug}/`;

  return {
    title: `${article.title} | Cora`,
    description: article.description,
    robots: { index: true, follow: true },
    openGraph: {
      title: `${article.title} | Cora`,
      description: article.description,
      url: canonical,
      type: 'article',
      publishedTime: article.publishedAt,
      modifiedTime: article.updatedAt,
      authors: [article.author.name],
      section: article.categoryLabel,
      images: [{ url: '/og-image.png', width: 1200, height: 630, alt: article.title }],
    },
    alternates: {
      canonical,
    }
  };
}

export default async function ArticlePage({ params }: ArticlePageProps) {
  const { category, slug } = await params;
  const article = getArticleBySlug(slug);
  const cat = getCategoryMetadata(category);

  if (!article || !cat || article.category !== category) {
    notFound();
  }

  // Schema.org Structured Data
  const articleSchema = generateArticleSchema(article);
  const breadcrumbSchema = generateArticleBreadcrumbs(cat, article);
  const faqSchema = article.faqs && article.faqs.length > 0 ? generateArticleFaqSchema(article.faqs) : null;

  return (
    <div className="min-h-screen bg-[#FBFaf7] text-zinc-900 selection:bg-zinc-950 selection:text-white pt-24 pb-20">

      {/* ── Structured SEO Data ── */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(articleSchema) }}
      />
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbSchema) }}
      />
      {faqSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }}
        />
      )}

      <article className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 space-y-12">

        {/* ── Article Hero Header ── */}
        <ArticleHero article={article} />

        {/* ── Main Layout: Article Body + Table of Contents ── */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

          {/* Main Article Content Column (8 Cols) */}
          <main className="lg:col-span-8 space-y-10">

            {/* ── 1. High Answer-Density Key Takeaways Box (GEO Optimization) ── */}
            {article.keyTakeaways && article.keyTakeaways.length > 0 && (
              <section className="border-y-2 border-zinc-950 py-6 sm:py-7 space-y-4">
                <div className="text-[10px] font-mono font-bold text-[#B6422B] uppercase tracking-[0.18em]">
                  Editor&apos;s abstract
                </div>

                <ul className="divide-y divide-zinc-300 border-t border-zinc-300">
                  {article.keyTakeaways.map((takeaway, idx) => (
                    <li key={idx} className="grid grid-cols-[2.5rem_1fr] py-3 text-xs sm:text-[13.5px] text-zinc-800 leading-relaxed">
                      <span className="font-mono text-[10px] text-[#B6422B]">0{idx + 1}</span>
                      <span className="font-medium">{takeaway}</span>
                    </li>
                  ))}
                </ul>
              </section>
            )}

            {/* ── 2. Interactive Widget Embed ── */}
            {article.interactiveWidget && article.interactiveWidget !== 'none' && (
              <InteractiveWidget widgetType={article.interactiveWidget} />
            )}

            {/* ── 3. Editorial visual lesson ── */}
            {article.visualGuide && (
              <EditorialVisualGuide guide={article.visualGuide} />
            )}

            {/* ── 4. Rendered Article Body ── */}
            {(article.category === 'finance-gst' || article.category === 'legal-contracts') && (
                <aside className="border-y border-amber-700 bg-amber-50 px-1 py-5 text-xs sm:text-sm leading-relaxed text-amber-950">
                <strong>Important:</strong> This article provides general educational information, not legal, tax, accounting, or financial advice. Rules and outcomes depend on your facts and may change. Consult a qualified professional before acting.
              </aside>
            )}

            <div className="prose prose-zinc max-w-none prose-headings:font-display prose-headings:font-bold prose-h2:text-2xl prose-h3:text-xl prose-p:leading-relaxed prose-li:leading-relaxed">
              <MarkdownRenderer content={article.content} />
            </div>

            {/* ── 5. Frequently Asked Questions Accordion ── */}
            {article.faqs && article.faqs.length > 0 && (
              <section className="pt-10 border-t border-zinc-200 space-y-6">
                <div className="flex items-center gap-2">
                  <HelpCircle className="w-5 h-5 text-zinc-700" />
                  <h3 className="font-display text-2xl font-bold text-zinc-950">
                    Frequently Asked Questions
                  </h3>
                </div>

                <div className="border-t-2 border-zinc-950">
                  {article.faqs.map((faq, fIdx) => (
                    <details
                      key={fIdx}
                      className="group border-b border-zinc-300 py-5"
                    >
                      <summary className="font-display font-semibold text-sm sm:text-base text-zinc-950 cursor-pointer list-none flex items-center justify-between gap-4">
                        <span>{faq.question}</span>
                        <ChevronRight className="w-4 h-4 text-zinc-400 group-open:rotate-90 transition-transform shrink-0" />
                      </summary>
                      <p className="pt-3 text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
                        {faq.answer}
                      </p>
                    </details>
                  ))}
                </div>
              </section>
            )}

            {/* ── 6. Bottom Action Lead Card ── */}
            <div className="border-y-2 border-zinc-950 bg-zinc-950 px-6 py-8 text-white flex flex-col sm:flex-row items-center justify-between gap-6">
              <div className="space-y-1 text-center sm:text-left">
                <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-widest block">
                  CORA STUDIO OS
                </span>
                <h4 className="font-display text-xl font-bold text-white">
                  Connect your website and client workflow.
                </h4>
                <p className="text-xs text-zinc-400 max-w-md">
                  Bring enquiries, documents, billing, and daily client operations into one workspace.
                </p>
              </div>

              <a
                href={`https://app.heycora.in/workspace/login?source=article_${article.slug}`}
                className="border-b border-white pb-1 text-xs font-semibold text-white transition-all shrink-0 flex items-center gap-1.5 group"
              >
                <span>Get started for Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-700 group-hover:translate-x-0.5 transition-transform" />
              </a>
            </div>

          </main>

          {/* Table of Contents Sticky Sidebar (4 Cols) */}
          <aside className="hidden lg:block lg:col-span-4 space-y-6">
            <ArticleTOC toc={article.toc} />
          </aside>

        </div>

        {/* ── Related Articles Cross-Linking ── */}
        <RelatedArticles
          currentSlug={article.slug}
          relatedSlugs={article.relatedSlugs || []}
          category={article.category}
        />

      </article>

    </div>
  );
}
