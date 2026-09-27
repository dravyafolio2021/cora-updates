import type { Metadata } from 'next';
import { BlogHeader } from '@/components/blog/BlogHeader';
import { BlogHubFeed } from '@/components/blog/BlogHubFeed';
import { BlogTopicClusters } from '@/components/blog/BlogTopicClusters';
import { BlogNewsletterBlock } from '@/components/blog/BlogNewsletterBlock';
import { getAllBlogArticles, getFeaturedBlogArticle, getAllBlogCategories } from '@/lib/blog-data';

const url = 'https://heycora.in/blog/';

export const metadata: Metadata = {
  title: 'Cora Blog — Systems, Playbooks & Operations for Agencies',
  description:
    'Practical operating answers, client onboarding systems, scope creep defence, and margin protection frameworks for agencies and service businesses.',
  alternates: { canonical: url },
  openGraph: {
    title: 'Cora Blog — Editorial Publication for Agencies',
    description:
      'Practical operating answers, client onboarding systems, scope creep defence, and margin protection frameworks for agencies and service businesses.',
    url,
    siteName: 'Cora',
    type: 'website',
  },
};

export default function BlogHomePage() {
  const allArticles = getAllBlogArticles();
  const categories = getAllBlogCategories();
  const featured = getFeaturedBlogArticle();

  const collectionSchema = {
    '@context': 'https://schema.org',
    '@type': 'Blog',
    name: 'Cora Editorial Publication',
    description:
      'Practical operating systems, workflows, research and operating lessons for agencies and service businesses.',
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
    <main className="w-full bg-white text-zinc-900 min-h-screen">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(collectionSchema) }}
      />

      {/* 1. Compact Masthead */}
      <BlogHeader />

      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-6 sm:py-10">
        {/* 2. Interactive Feed (Search + Category Filter + Featured + Grid + Popular) */}
        <BlogHubFeed
          articles={allArticles}
          categories={categories}
          featuredArticle={featured}
        />

        {/* 3. Operational Topic Clusters */}
        <BlogTopicClusters />

        {/* 4. Weekly Newsletter Subscription */}
        <section className="my-16">
          <BlogNewsletterBlock placement="end" articleSlug="blog_homepage" category="all" />
        </section>
      </div>
    </main>
  );
}

