import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, ChevronRight, Clock, Calendar, Sparkles, Home, Bookmark } from 'lucide-react';
import {
  BLOG_ARTICLES,
  BLOG_CATEGORIES,
  getArticleBySlug,
  getBlogCategoryById,
  getArticlesByCategory,
  getRelatedArticles,
  HeadingBlock,
} from '@/lib/blog-data';
import { BlogBlockRenderer } from '@/components/blog/BlogBlockRenderer';
import { BlogTableOfContents } from '@/components/blog/BlogTableOfContents';
import { BlogReadingProgress } from '@/components/blog/BlogReadingProgress';
import { BlogShareBar } from '@/components/blog/BlogShareBar';
import { BlogAuthorBio } from '@/components/blog/BlogAuthorBio';
import { BlogSources } from '@/components/blog/BlogSources';
import { BlogRelatedPosts } from '@/components/blog/BlogRelatedPosts';
import { BlogNewsletterBlock } from '@/components/blog/BlogNewsletterBlock';
import { BlogArticleCard } from '@/components/blog/BlogArticleCard';
import { BlogHeader } from '@/components/blog/BlogHeader';
import { BlogTopicFilter } from '@/components/blog/BlogTopicFilter';

interface PageProps {
  params: Promise<{ slug: string }>;
}

export async function generateStaticParams() {
  const categoryParams = BLOG_CATEGORIES.map((c) => ({ slug: c.slug }));
  const articleParams = BLOG_ARTICLES.map((a) => ({ slug: a.slug }));
  return [...categoryParams, ...articleParams];
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;

  // Check if Category
  const category = getBlogCategoryById(slug);
  if (category) {
    const canonical = `https://heycora.in/blog/${category.slug}/`;
    return {
      title: `${category.name} — Cora Editorial Publication`,
      description: category.description,
      alternates: { canonical },
      openGraph: {
        title: `${category.name} — Cora Editorial Publication`,
        description: category.description,
        url: canonical,
        siteName: 'Cora',
        type: 'website',
      },
    };
  }

  // Check if Article
  const article = getArticleBySlug(slug);
  if (article) {
    const canonical = article.canonicalUrl || `https://heycora.in/blog/${article.slug}/`;
    return {
      title: `${article.seoTitle || article.title} | Cora`,
      description: article.seoDescription || article.excerpt,
      alternates: { canonical },
      openGraph: {
        title: article.title,
        description: article.excerpt,
        url: canonical,
        siteName: 'Cora',
        type: 'article',
        publishedTime: article.publishedAt,
        modifiedTime: article.updatedAt,
        authors: [article.author.name],
        tags: article.tags,
        images: [{ url: article.ogImage || article.coverImage }],
      },
      twitter: {
        card: 'summary_large_image',
        title: article.title,
        description: article.excerpt,
        images: [article.ogImage || article.coverImage],
      },
    };
  }

  return { title: 'Page Not Found' };
}

export default async function BlogDynamicPage({ params }: PageProps) {
  const { slug } = await params;

  // 1. Check if Category Archive
  const category = getBlogCategoryById(slug);
  if (category) {
    const categoryArticles = getArticlesByCategory(category.id);
    return (
      <CategoryArchiveView category={category} articles={categoryArticles} />
    );
  }

  // 2. Check if Individual Article
  const article = getArticleBySlug(slug);
  if (article) {
    return <ArticleDetailView article={article} />;
  }

  return notFound();
}

/* ====================================================================
 * INDIVIDUAL ARTICLE DETAIL VIEW
 * ==================================================================== */

function ArticleDetailView({ article }: { article: NonNullable<ReturnType<typeof getArticleBySlug>> }) {
  const related = getRelatedArticles(article.slug, article.category, 3);
  const headings = article.blocks
    .filter((b): b is HeadingBlock => b.type === 'heading' && b.level <= 3)
    .map((h) => ({ id: h.id, text: h.text, level: h.level }));

  const categoryObj = getBlogCategoryById(article.category);
  const currentUrl = `https://heycora.in/blog/${article.slug}/`;

  const articleSchema = {
    '@context': 'https://schema.org',
    '@type': 'BlogPosting',
    headline: article.title,
    description: article.excerpt,
    image: article.coverImage,
    datePublished: article.publishedAt,
    dateModified: article.updatedAt,
    mainEntityOfPage: {
      '@type': 'WebPage',
      '@id': currentUrl,
    },
    author: {
      '@type': 'Person',
      name: article.author.name,
      jobTitle: article.author.role,
      url: article.author.linkedin || 'https://heycora.in/about',
    },
    publisher: {
      '@type': 'Organization',
      name: 'Cora',
      url: 'https://heycora.in',
      logo: 'https://heycora.in/favicon.png',
    },
  };

  const breadcrumbSchema = {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
      { '@type': 'ListItem', position: 1, name: 'Home', item: 'https://heycora.in' },
      { '@type': 'ListItem', position: 2, name: 'Blog', item: 'https://heycora.in/blog/' },
      {
        '@type': 'ListItem',
        position: 3,
        name: categoryObj?.name || 'Category',
        item: `https://heycora.in/blog/${categoryObj?.slug || article.category}/`,
      },
      { '@type': 'ListItem', position: 4, name: article.title, item: currentUrl },
    ],
  };

  const faqSchema = article.faqs?.length
    ? {
        '@context': 'https://schema.org',
        '@type': 'FAQPage',
        mainEntity: article.faqs.map((f) => ({
          '@type': 'Question',
          name: f.question,
          acceptedAnswer: { '@type': 'Answer', text: f.answer },
        })),
      }
    : null;

  return (
    <article className="w-full bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 min-h-screen">
      {/* Schema Injection */}
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(articleSchema) }} />
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbSchema) }} />
      {faqSchema && (
        <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }} />
      )}

      {/* Top Reading Progress Bar */}
      <BlogReadingProgress />

      {/* Article Header Section */}
      <header className="pt-28 sm:pt-36 pb-10 border-b border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-950">
        <div className="mx-auto max-w-[1240px] px-4 sm:px-6">
          {/* Breadcrumb */}
          <nav className="flex items-center gap-1.5 text-[11px] font-mono text-zinc-600 dark:text-zinc-400 mb-6 flex-wrap">
            <Link href="/" className="hover:text-zinc-900 dark:hover:text-white transition-colors">
              Home
            </Link>
            <ChevronRight className="w-3 h-3 text-zinc-400" />
            <Link href="/blog" className="hover:text-zinc-900 dark:hover:text-white transition-colors">
              Blog
            </Link>
            <ChevronRight className="w-3 h-3 text-zinc-400" />
            <Link
              href={`/blog/${categoryObj?.slug || article.category}/`}
              className="hover:text-zinc-900 dark:hover:text-white transition-colors"
            >
              {categoryObj?.name || article.category}
            </Link>
          </nav>

          <div className="max-w-4xl">
            {/* Quality Label & Category */}
            <div className="inline-flex items-center gap-2 text-[11px] font-mono mb-4">
              <span className="px-2.5 py-0.5 rounded-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold uppercase tracking-wider">
                {article.qualityLabel}
              </span>
              <span className="text-zinc-400">&bull;</span>
              <span className="font-bold text-zinc-600 dark:text-zinc-400 uppercase">
                {categoryObj?.name || article.category}
              </span>
            </div>

            {/* H1 Title */}
            <h1 className="font-display text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-zinc-950 dark:text-white leading-[1.12]">
              {article.title}
            </h1>

            {/* Editorial Dek / Summary */}
            {article.dek && (
              <p className="mt-5 text-base sm:text-lg lg:text-xl text-zinc-600 dark:text-zinc-400 leading-relaxed font-normal">
                {article.dek}
              </p>
            )}

            {/* Author & Publishing Metadata */}
            <div className="mt-8 pt-6 border-t border-zinc-200/80 dark:border-zinc-800 flex flex-wrap items-center justify-between gap-4">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-full bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 flex items-center justify-center font-bold text-sm font-mono shrink-0">
                  {article.author.name.charAt(0)}
                </div>
                <div className="text-xs">
                  <div className="font-bold text-zinc-900 dark:text-zinc-100">{article.author.name}</div>
                  <div className="text-zinc-600 dark:text-zinc-400 text-[11px] flex items-center gap-1.5 mt-0.5">
                    <span>Published {article.publishedAt}</span>
                    {article.updatedAt !== article.publishedAt && (
                      <>
                        <span>&bull;</span>
                        <span>Updated {article.updatedAt}</span>
                      </>
                    )}
                    <span>&bull;</span>
                    <span className="flex items-center gap-1">
                      <Clock className="w-3 h-3" />
                      {article.readTime}
                    </span>
                  </div>
                </div>
              </div>

              {/* Social Share Bar */}
              <BlogShareBar title={article.title} url={currentUrl} articleSlug={article.slug} />
            </div>
          </div>
        </div>
      </header>

      {/* Main Reading Canvas */}
      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-10 sm:py-14">
        <div className="grid lg:grid-cols-[240px_minmax(0,1fr)] xl:grid-cols-[260px_minmax(0,780px)] gap-10 lg:gap-14 items-start">
          {/* Left Rail: Sticky Table of Contents */}
          <aside className="w-full">
            <BlogTableOfContents headings={headings} />
          </aside>

          {/* Center Column: 740-780px Reading Article Body */}
          <div className="min-w-0 max-w-[780px]">
            {/* Hero Cover Visual */}
            <div className="relative aspect-[16/9] w-full rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-900 mb-8 shadow-xs">
              <Image
                src={article.coverImage}
                alt={article.coverAlt}
                fill
                priority
                className="object-cover"
                sizes="(max-width: 1024px) 100vw, 780px"
              />
            </div>

            {/* Modular Structured Block Engine */}
            <BlogBlockRenderer
              blocks={article.blocks}
              articleSlug={article.slug}
              category={article.category}
            />

            {/* Sources & Citations Section */}
            <BlogSources sources={article.sources} />

            {/* Author Profile Card */}
            <BlogAuthorBio author={article.author} />

            {/* End-of-Article Newsletter Card */}
            <BlogNewsletterBlock placement="end" articleSlug={article.slug} category={article.category} />

            {/* Related Articles Tray */}
            <BlogRelatedPosts articles={related} />
          </div>
        </div>
      </div>
    </article>
  );
}

/* ====================================================================
 * CATEGORY ARCHIVE VIEW
 * ==================================================================== */

function CategoryArchiveView({
  category,
  articles,
}: {
  category: NonNullable<ReturnType<typeof getBlogCategoryById>>;
  articles: ReturnType<typeof getArticlesByCategory>;
}) {
  return (
    <main className="w-full bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 min-h-screen">
      <BlogHeader
        title={category.name}
        description={category.description}
        badge={`CORA EDITORIAL // ${category.badge.toUpperCase()}`}
      />

      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-8 sm:py-12">
        {/* Topic Filter Tabs */}
        <BlogTopicFilter activeCategory={category.slug} />

        {/* Category Articles Grid */}
        <section className="my-10">
          <div className="flex items-center justify-between gap-4 mb-6 border-b border-zinc-200/80 dark:border-zinc-800 pb-3">
            <div>
              <div className="text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-400">
                TOPIC ARCHIVE
              </div>
              <h2 className="font-display text-2xl font-bold tracking-tight text-zinc-950 dark:text-white mt-1">
                All {category.name} Articles
              </h2>
            </div>
            <div className="text-xs font-mono text-zinc-400">
              {articles.length} {articles.length === 1 ? 'article' : 'articles'}
            </div>
          </div>

          {articles.length === 0 ? (
            <div className="rounded-3xl border border-zinc-200 dark:border-zinc-800 bg-[#FBFaf7] dark:bg-zinc-900/60 p-12 text-center text-zinc-500">
              New articles for {category.name} are currently in editorial review.
            </div>
          ) : (
            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {articles.map((art) => (
                <BlogArticleCard key={art.slug} article={art} />
              ))}
            </div>
          )}
        </section>

        {/* Newsletter Block */}
        <section className="my-16">
          <BlogNewsletterBlock
            placement="end"
            articleSlug={`category_${category.slug}`}
            category={category.id}
          />
        </section>
      </div>
    </main>
  );
}
