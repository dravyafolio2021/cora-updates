import React from 'react';
import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Link from 'next/link';
import { Sparkles, CheckCircle2, ChevronRight, HelpCircle, ArrowRight } from 'lucide-react';
import { ARTICLES_DATA, getArticleBySlug, getCategoryMetadata } from '@/lib/articles-data';
import { ArticleHero } from '@/components/articles/ArticleHero';
import { ArticleTOC } from '@/components/articles/ArticleTOC';
import { RelatedArticles } from '@/components/articles/RelatedArticles';
import { InteractiveWidget } from '@/components/articles/InteractiveWidgets';
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
  const { slug } = await params;
  const article = getArticleBySlug(slug);

  if (!article) return { title: 'Article Not Found | Cora Studio OS' };

  return {
    title: `${article.title} | Cora Studio OS`,
    description: article.description,
    openGraph: {
      title: `${article.title} | Cora Studio OS`,
      description: article.description,
      url: `https://heycora.in/articles/${article.category}/${article.slug}`,
      type: 'article',
      publishedTime: article.publishedAt,
      modifiedTime: article.updatedAt,
      authors: [article.author.name],
    },
    alternates: {
      canonical: `https://heycora.in/articles/${article.category}/${article.slug}`,
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
              <div className="p-6 sm:p-7 rounded-3xl bg-zinc-100/90 border border-zinc-300/70 shadow-2xs space-y-3.5">
                <div className="flex items-center gap-2 text-xs font-mono font-bold text-zinc-950 uppercase tracking-wider">
                  <Sparkles className="w-3.5 h-3.5 text-zinc-700" />
                  <span>Key Takeaways &amp; Executive Summary</span>
                </div>

                <ul className="space-y-2.5">
                  {article.keyTakeaways.map((takeaway, idx) => (
                    <li key={idx} className="flex items-start gap-2.5 text-xs sm:text-[13.5px] text-zinc-800 leading-relaxed">
                      <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                      <span>{takeaway}</span>
                    </li>
                  ))}
                </ul>
              </div>
            )}

            {/* ── 2. Interactive Widget Embed ── */}
            {article.interactiveWidget && article.interactiveWidget !== 'none' && (
              <InteractiveWidget widgetType={article.interactiveWidget} />
            )}

            {/* ── 3. Rendered Article Body ── */}
            <div className="prose prose-zinc max-w-none prose-headings:font-display prose-headings:font-bold prose-h2:text-2xl prose-h3:text-xl prose-p:leading-relaxed prose-li:leading-relaxed">
              <MarkdownRenderer content={article.content} />
            </div>

            {/* ── 4. Frequently Asked Questions Accordion ── */}
            {article.faqs && article.faqs.length > 0 && (
              <section className="pt-10 border-t border-zinc-200 space-y-6">
                <div className="flex items-center gap-2">
                  <HelpCircle className="w-5 h-5 text-zinc-700" />
                  <h3 className="font-display text-2xl font-bold text-zinc-950">
                    Frequently Asked Questions
                  </h3>
                </div>

                <div className="space-y-3">
                  {article.faqs.map((faq, fIdx) => (
                    <details
                      key={fIdx}
                      className="group p-5 rounded-2xl bg-white border border-zinc-200/90 transition-all open:border-zinc-400/80 shadow-2xs"
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

            {/* ── 5. Bottom Action Lead Card ── */}
            <div className="p-8 rounded-3xl bg-zinc-950 text-white border border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl">
              <div className="space-y-1 text-center sm:text-left">
                <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-widest block">
                  CORA STUDIO OS
                </span>
                <h4 className="font-display text-xl font-bold text-white">
                  Automate your studio operations today.
                </h4>
                <p className="text-xs text-zinc-400 max-w-md">
                  Join 450+ commercial photography studios, film production crews, and agencies scaling on Cora.
                </p>
              </div>

              <Link
                href="/workspace/login"
                className="px-5 py-2.5 rounded-full bg-white text-zinc-950 text-xs font-semibold hover:bg-zinc-100 transition-all shrink-0 shadow-sm flex items-center gap-1.5 group"
              >
                <span>Get started for Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-700 group-hover:translate-x-0.5 transition-transform" />
              </Link>
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
