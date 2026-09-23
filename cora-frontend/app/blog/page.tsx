import type { Metadata } from 'next';
import { BlogHeader } from '@/components/blog/BlogHeader';
import { BlogFeaturedCard } from '@/components/blog/BlogFeaturedCard';
import { BlogArticleCard } from '@/components/blog/BlogArticleCard';
import { BlogTopicFilter } from '@/components/blog/BlogTopicFilter';
import { BlogSearch } from '@/components/blog/BlogSearch';
import { BlogNewsletterBlock } from '@/components/blog/BlogNewsletterBlock';
import { getAllBlogArticles, getFeaturedBlogArticle } from '@/lib/blog-data';

const url = 'https://heycora.in/blog/';

export const metadata: Metadata = {
  title: 'Cora Blog — Ideas, Systems & Workflows for High-Output Agencies',
  description:
    'Practical operating systems, workflows, research, and operating lessons for agencies, creative studios, and service businesses.',
  alternates: { canonical: url },
  openGraph: {
    title: 'Cora Blog — Editorial Publication for Agencies',
    description:
      'Practical systems, workflows, research and operating lessons for agencies and service businesses.',
    url,
    siteName: 'Cora',
    type: 'website',
  },
};

export default function BlogHomePage() {
  const allArticles = getAllBlogArticles();
  const featured = getFeaturedBlogArticle();
  const latestArticles = allArticles.filter((a) => a.slug !== featured.slug);

  const collectionSchema = {
    '@context': 'https://schema.org',
    '@type': 'Blog',
    name: 'Cora Editorial Publication',
    description: 'Practical operating systems, workflows, research and operating lessons for agencies and service businesses.',
    url: 'https://heycora.in/blog/',
    publisher: {
      '@type': 'Organization',
      name: 'Cora',
      url: 'https://heycora.in',
      logo: 'https://heycora.in/favicon.png',
    },
    blogPost: allArticles.map((art) => ({
      '@type': 'BlogPosting',
      headline: art.title,
      description: art.excerpt,
      url: `https://heycora.in/blog/${art.slug}/`,
      datePublished: art.publishedAt,
      dateModified: art.updatedAt,
      author: {
        '@type': 'Person',
        name: art.author.name,
      },
    })),
  };

  return (
    <main className="w-full bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 min-h-screen">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(collectionSchema) }}
      />

      {/* Publication Masthead */}
      <BlogHeader />

      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-8 sm:py-12">
        {/* Search & Topic Navigation */}
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
          <BlogTopicFilter />
          <BlogSearch articles={allArticles} />
        </div>

        {/* Flagship Featured Story */}
        {featured && (
          <section className="mb-14">
            <BlogFeaturedCard article={featured} />
          </section>
        )}

        {/* Latest Stories Grid */}
        <section className="my-14">
          <div className="flex items-center justify-between gap-4 mb-6 border-b border-zinc-200/80 dark:border-zinc-800 pb-3">
            <div>
              <div className="text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-400">
                LATEST ARTICLES
              </div>
              <h2 className="font-display text-2xl font-bold tracking-tight text-zinc-950 dark:text-white mt-1">
                Recent Systems & Playbooks
              </h2>
            </div>
            <div className="text-xs font-mono text-zinc-400">
              {allArticles.length} total articles
            </div>
          </div>

          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {latestArticles.map((art) => (
              <BlogArticleCard key={art.slug} article={art} />
            ))}
          </div>
        </section>

        {/* Homepage Newsletter Block */}
        <section className="my-16">
          <BlogNewsletterBlock placement="end" articleSlug="blog_homepage" category="all" />
        </section>
      </div>
    </main>
  );
}
