import type { Metadata } from 'next';
import { BlogHubClientView } from '@/components/blog/BlogHubClientView';
import { getAllBlogArticles, getFeaturedBlogArticle, getAllBlogCategories, BlogArticle } from '@/lib/blog-data';
import { fetchContentEntries, adaptCmsEntryToBlogArticle } from '@/lib/content-api';

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

export default async function BlogHomePage() {
  const staticArticles = getAllBlogArticles();
  const growthEntries = await fetchContentEntries({ type: 'article', status: 'published' }).catch(() => []);
  const adaptedGrowth = growthEntries.map(adaptCmsEntryToBlogArticle);

  const cmsSlugSet = new Set(adaptedGrowth.map((a) => a.slug));
  const allArticles: BlogArticle[] = [
    ...adaptedGrowth,
    ...staticArticles.filter((a) => !cmsSlugSet.has(a.slug)),
  ].sort((a, b) => new Date(b.publishedAt).getTime() - new Date(a.publishedAt).getTime());

  const featured = allArticles.find((a) => a.featured) || allArticles[0] || getFeaturedBlogArticle();

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
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(collectionSchema) }}
      />
      <BlogHubClientView
        initialArticles={allArticles}
        initialFeaturedArticle={featured}
      />
    </>
  );
}

