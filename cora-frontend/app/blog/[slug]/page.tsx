import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, ChevronRight, Clock, Calendar, Sparkles, Home, Bookmark, ShieldAlert } from 'lucide-react';
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

import { buildEditorialMetadata } from '@/lib/editorial-seo';

interface PageProps {
  params: Promise<{ slug: string }>;
}

export async function generateStaticParams() {
  const categoryParams = BLOG_CATEGORIES.map((c) => ({ slug: c.slug }));
  // Strictly only generate public static paths for published articles
  const publishedArticles = BLOG_ARTICLES.filter((a) => a.status === 'published');
  const articleParams = publishedArticles.map((a) => ({ slug: a.slug }));
  return [...categoryParams, ...articleParams];
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;

  // 1. Check if Category
  const category = getBlogCategoryById(slug);
  if (category) {
    const canonical = `https://heycora.in/blog/${category.slug}/`;
    return {
      title: `${category.name} — Cora Editorial Publication`,
      description: category.description,
      alternates: { canonical },
      robots: {
        index: true,
        follow: true,
      },
      openGraph: {
        title: `${category.name} — Cora Editorial Publication`,
        description: category.description,
        url: canonical,
        siteName: 'Cora',
        type: 'website',
      },
    };
  }

  // 2. Check if Article (support draft inspection with strict noindex)
  const article = getArticleBySlug(slug, true);
  if (article) {
    return buildEditorialMetadata(
      {
        slug: article.slug,
        title: article.title,
        dek: article.dek,
        excerpt: article.excerpt,
        seoTitle: article.seoTitle,
        seoDescription: article.seoDescription,
        coverImage: article.coverImage,
        coverAlt: article.coverAlt,
        ogImage: article.ogImage,
        ogImageAlt: article.ogImageAlt,
        shareTitle: article.shareTitle,
        shareDescription: article.shareDescription,
        shareText: article.shareText,
        publishedAt: article.publishedAt,
        updatedAt: article.updatedAt,
        authorName: article.author.name,
        tags: article.tags,
        canonicalUrl: article.canonicalUrl,
        category: article.category,
      },
      { isPublished: article.status === 'published' }
    );
  }

  return { title: 'Page Not Found' };
}

export default async function BlogDynamicPage({ params }: PageProps) {
  const { slug } = await params;

  // 1. Check if Category Archive
  const category = getBlogCategoryById(slug);
  if (category) {
    const categoryArticles = getArticlesByCategory(category.id, false);
    return (
      <CategoryArchiveView category={category} articles={categoryArticles} />
    );
  }

  // 2. Check if Individual Article
  const article = getArticleBySlug(slug, true);
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
  const isUnpublished = article.status !== 'published';

  const articleSchema = {
    '@context': 'https://schema.org',
    '@type': 'Article',
    mainEntityOfPage: {
      '@type': 'WebPage',
      '@id': article.canonicalUrl || `https://heycora.in/blog/${article.slug}/`,
    },
    headline: article.title,
    description: article.excerpt,
    image: [article.ogImage || article.coverImage],
    datePublished: article.publishedAt,
    dateModified: article.updatedAt,
    author: {
      '@type': 'Person',
      name: article.author.name,
      url: article.author.linkedin || article.author.x || 'https://heycora.in/about/',
    },
    publisher: {
      '@type': 'Organization',
      name: 'Cora',
      logo: {
        '@type': 'ImageObject',
        url: 'https://heycora.in/favicon.png',
      },
    },
  };

  return (
    <article className="min-h-screen bg-white text-zinc-900 selection:bg-zinc-200">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(articleSchema) }}
      />

      <BlogReadingProgress />

      {/* Non-published Preview Banner */}
      {isUnpublished && (
        <div className="bg-amber-500/10 border-b border-amber-500/30 text-amber-900 py-2.5 px-4 text-xs font-mono text-center flex items-center justify-center gap-2">
          <ShieldAlert className="w-4 h-4 text-amber-600" />
          <span>
            <strong>STATUS: {article.status.toUpperCase()}</strong> &bull; This article is excluded from public sitemaps and search indexing (noindex, nofollow).
          </span>
        </div>
      )}

      {/* Navigation Header */}
      <header className="border-b border-zinc-200 bg-[#FBFaf7]">
        <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-4 flex items-center justify-between">
          <div className="flex items-center gap-2 text-xs font-mono text-zinc-500">
            <Link href="/" className="hover:text-zinc-900 transition-colors flex items-center gap-1">
              <Home className="w-3.5 h-3.5" />
              <span>Cora</span>
            </Link>
            <ChevronRight className="w-3 h-3 text-zinc-400" />
            <Link href="/blog" className="hover:text-zinc-900 transition-colors">
              Blog
            </Link>
            {categoryObj && (
              <>
                <ChevronRight className="w-3 h-3 text-zinc-400" />
                <Link
                  href={`/blog/${categoryObj.slug}/`}
                  className="hover:text-zinc-900 transition-colors line-clamp-1"
                >
                  {categoryObj.shortName}
                </Link>
              </>
            )}
          </div>

          <div className="flex items-center gap-3">
            <Link
              href="/blog"
              className="text-xs font-bold text-zinc-700 hover:text-zinc-950 font-mono"
            >
              All Articles
            </Link>
          </div>
        </div>
      </header>

      {/* Article Hero Masthead */}
      <header className="bg-[#FBFaf7] border-b border-zinc-200 py-10 sm:py-16">
        <div className="mx-auto max-w-[820px] px-4 sm:px-6">
          <div className="flex flex-wrap items-center gap-2 text-[11px] font-mono mb-4">
            <span className="px-2.5 py-1 rounded-full bg-zinc-200/70 text-zinc-800 font-bold uppercase tracking-wider">
              {article.qualityLabel}
            </span>
            {categoryObj && (
              <Link
                href={`/blog/${categoryObj.slug}/`}
                className="font-bold text-zinc-600 hover:text-zinc-950 uppercase tracking-wider transition-colors"
              >
                {categoryObj.name}
              </Link>
            )}
            <span className="text-zinc-400">&bull;</span>
            <span className="text-zinc-500 flex items-center gap-1">
              <Clock className="w-3.5 h-3.5" />
              <span>{article.readTime}</span>
            </span>
          </div>

          <h1 className="font-display text-2xl sm:text-4xl lg:text-[42px] font-bold tracking-tight text-zinc-950 leading-[1.18]">
            {article.title}
          </h1>

          <p className="mt-4 text-base sm:text-lg text-zinc-600 leading-relaxed font-normal">
            {article.dek}
          </p>

          <div className="mt-8 pt-6 border-t border-zinc-200/80 flex flex-wrap items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center font-display font-bold text-sm">
                {article.author.name.charAt(0)}
              </div>
              <div>
                <div className="font-bold text-xs sm:text-sm text-zinc-950">
                  {article.author.name}
                </div>
                <div className="text-[11px] text-zinc-500">{article.author.role}</div>
              </div>
            </div>

            <div className="text-right text-xs font-mono text-zinc-500">
              <div>Published: {article.publishedAt}</div>
              {article.updatedAt !== article.publishedAt && (
                <div className="text-[10px] text-zinc-400">Updated: {article.updatedAt}</div>
              )}
            </div>
          </div>
        </div>
      </header>

      {/* Main Content Layout with Sticky TOC Rail */}
      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-10 sm:py-14">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10">
          {/* Main Article Body (8 cols) */}
          <main className="lg:col-span-8 lg:col-start-1 max-w-[760px]">
            {/* Mobile TOC */}
            <BlogTableOfContents headings={headings} mode="mobile" />

            {/* Article Editorial Cover Image */}
            {article.coverImage && (
              <div className="mb-10 overflow-hidden rounded-2xl border border-zinc-200/90 bg-[#FBFaf7] shadow-sm">
                <Image
                  src={article.coverImage}
                  alt={article.coverAlt || article.title}
                  width={1600}
                  height={900}
                  priority
                  className="w-full h-auto aspect-[16/9] object-cover"
                />
              </div>
            )}

            {/* Structured Editorial Blocks */}
            <BlogBlockRenderer
              blocks={article.blocks}
              articleSlug={article.slug}
              category={article.category}
            />

            {/* Social Share Bar */}
            <div className="mt-12 pt-6 border-t border-zinc-200">
              <BlogShareBar
                title={article.title}
                url={article.canonicalUrl || `https://heycora.in/blog/${article.slug}/`}
                articleSlug={article.slug}
                shareTitle={article.shareTitle}
                shareDescription={article.shareDescription}
                shareText={article.shareText}
              />
            </div>

            {/* Sources & Citations */}
            <BlogSources sources={article.sources} />

            {/* Author Bio Box */}
            <BlogAuthorBio author={article.author} />

            {/* End of Article Newsletter Card */}
            <BlogNewsletterBlock
              heading="Like this operating guide?"
              tagline="Get our high-impact agency operating systems and workflow breakdowns delivered to your inbox every week."
              buttonText="Subscribe Free"
              articleSlug={article.slug}
              category={article.category}
              placement="end"
            />
          </main>

          {/* Sticky TOC Rail (4 cols) */}
          <aside className="hidden lg:block lg:col-span-4 lg:col-start-9">
            <div className="sticky top-24 space-y-6">
              <BlogTableOfContents headings={headings} mode="desktop" />

              <div className="p-5 rounded-2xl border border-zinc-200 bg-[#FBFaf7] text-xs space-y-3">
                <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-500">
                  ABOUT THIS PUBLICATION
                </div>
                <p className="text-zinc-600 leading-relaxed">
                  Cora publishes operational workflows, contract systems, and agency playbooks for creative and technical service businesses.
                </p>
                <div className="pt-2">
                  <Link
                    href="/demo"
                    className="inline-flex items-center gap-1.5 font-bold text-zinc-950 hover:underline"
                  >
                    <span>Explore Cora Platform</span>
                    <ArrowRight className="w-3.5 h-3.5" />
                  </Link>
                </div>
              </div>
            </div>
          </aside>
        </div>

        {/* Related Articles Footer Carousel/Grid */}
        <BlogRelatedPosts articles={related} />
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
    <main className="min-h-screen bg-white text-zinc-900">
      <BlogHeader />

      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-8 sm:py-12">
        <div className="mb-8">
          <div className="flex items-center gap-2 text-xs font-mono text-zinc-500 mb-2">
            <Link href="/blog" className="hover:text-zinc-900 transition-colors">
              Blog
            </Link>
            <ChevronRight className="w-3 h-3 text-zinc-400" />
            <span className="text-zinc-900 font-semibold">{category.name}</span>
          </div>

          <h1 className="font-display text-3xl sm:text-4xl font-bold tracking-tight text-zinc-950">
            {category.name}
          </h1>
          <p className="mt-2 text-sm sm:text-base text-zinc-600 max-w-2xl leading-relaxed">
            {category.description}
          </p>
        </div>

        <BlogTopicFilter activeCategory={category.id} />

        <div className="my-8">
          <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400 mb-4">
            {articles.length} {articles.length === 1 ? 'ARTICLE' : 'ARTICLES'} IN {category.name.toUpperCase()}
          </div>

          {articles.length === 0 ? (
            <div className="p-12 text-center rounded-3xl border border-zinc-200 bg-[#FBFaf7]">
              <h3 className="font-display text-lg font-bold text-zinc-950">
                New editorial articles coming soon in {category.name}.
              </h3>
              <p className="mt-2 text-xs text-zinc-600 max-w-md mx-auto">
                Subscribe to our weekly brief below to get notified when the next playbook is published.
              </p>
              <div className="mt-6 max-w-md mx-auto">
                <BlogNewsletterBlock placement="inline" category={category.id} />
              </div>
            </div>
          ) : (
            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {articles.map((art) => (
                <BlogArticleCard key={art.slug} article={art} />
              ))}
            </div>
          )}
        </div>
      </div>
    </main>
  );
}
