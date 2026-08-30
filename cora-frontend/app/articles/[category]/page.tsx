import React from 'react';
import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Link from 'next/link';
import { ArrowLeft, Sparkles, Compass, Layers, Receipt, Bot, ShieldCheck, BarChart2 } from 'lucide-react';
import { ARTICLE_CATEGORIES, getArticlesByCategory, getCategoryMetadata } from '@/lib/articles-data';
import { ArticleCard } from '@/components/articles/ArticleCard';
import { generateArticleBreadcrumbs } from '@/lib/seo-schema';

export async function generateStaticParams() {
  return ARTICLE_CATEGORIES.map((cat) => ({
    category: cat.id,
  }));
}

interface CategoryPageProps {
  params: Promise<{ category: string }>;
}

export async function generateMetadata({ params }: CategoryPageProps): Promise<Metadata> {
  const { category } = await params;
  const cat = getCategoryMetadata(category);

  if (!cat) return { title: 'Category Not Found | Cora Studio OS' };

  return {
    title: `${cat.name} | Cora Studio OS Guides`,
    description: cat.description,
    openGraph: {
      title: `${cat.name} | Cora Studio OS`,
      description: cat.description,
      url: `https://heycora.in/articles/${cat.id}`,
    },
    alternates: {
      canonical: `https://heycora.in/articles/${cat.id}`,
    }
  };
}

export default async function CategoryPage({ params }: CategoryPageProps) {
  const { category } = await params;
  const cat = getCategoryMetadata(category);

  if (!cat) {
    notFound();
  }

  const articles = getArticlesByCategory(category);
  const breadcrumbSchema = generateArticleBreadcrumbs(cat);

  return (
    <div className="min-h-screen bg-[#FBFaf7] text-zinc-900 selection:bg-zinc-950 selection:text-white pt-24 pb-20">
      
      {/* Schema.org Breadcrumbs */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbSchema) }}
      />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 space-y-12">
        
        {/* Breadcrumb & Hero Header */}
        <div className="space-y-4 max-w-3xl">
          <Link
            href="/articles"
            className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors group"
          >
            <ArrowLeft className="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform" />
            <span>Back to All Articles</span>
          </Link>

          <div className="flex items-center gap-2">
            <span className="px-2.5 py-0.5 rounded-full bg-zinc-200/80 text-[10px] font-mono font-bold text-zinc-800 uppercase tracking-wider">
              {cat.badge}
            </span>
            <span className="text-xs font-mono text-zinc-500">
              {articles.length} In-Depth {articles.length === 1 ? 'Article' : 'Articles'}
            </span>
          </div>

          <h1 className="font-display text-4xl sm:text-5xl font-extrabold tracking-tight text-zinc-950 leading-tight">
            {cat.name}
          </h1>

          <p className="text-base sm:text-lg text-zinc-600 font-normal leading-relaxed">
            {cat.description}
          </p>
        </div>

        {/* Category Articles Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
          {articles.map((article) => (
            <ArticleCard key={article.slug} article={article} />
          ))}
        </div>

        {/* Bottom Explore Other Categories Bar */}
        <div className="pt-16 border-t border-zinc-200 space-y-6">
          <div className="flex items-center justify-between">
            <h3 className="font-display text-xl font-bold text-zinc-950">
              Explore Other Categories
            </h3>
            <Link href="/articles" className="text-xs font-semibold text-zinc-700 hover:text-black">
              View all categories →
            </Link>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            {ARTICLE_CATEGORIES.filter((c) => c.id !== cat.id).map((otherCat) => (
              <Link
                key={otherCat.id}
                href={`/articles/${otherCat.id}`}
                className="p-3.5 rounded-2xl bg-white border border-zinc-200/80 hover:border-zinc-400 hover:shadow-xs transition-all flex flex-col justify-between group"
              >
                <div>
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                    {otherCat.badge}
                  </span>
                  <span className="text-xs font-bold text-zinc-900 group-hover:text-black block line-clamp-1 mt-1">
                    {otherCat.label}
                  </span>
                </div>
              </Link>
            ))}
          </div>
        </div>

      </div>

    </div>
  );
}
