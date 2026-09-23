import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { getGuideBySlug, getAllGuideSlugs } from '@/lib/guides-data';
import { buildEditorialMetadata } from '@/lib/editorial-seo';
import { GuideDetailView } from '@/components/guides/GuideDetailView';

interface GuidePageProps {
  params: Promise<{ slug: string }>;
}

export async function generateStaticParams() {
  // Generate static export pages for all registered guides in GUIDES_DATA
  const slugs = getAllGuideSlugs(true);
  return slugs.map((slug) => ({ slug }));
}

export async function generateMetadata({ params }: GuidePageProps): Promise<Metadata> {
  const { slug } = await params;
  const guide = getGuideBySlug(slug, true);

  if (!guide) {
    return { title: 'Guide Not Found' };
  }

  return buildEditorialMetadata(
    {
      slug: guide.slug,
      title: guide.title,
      dek: guide.dek,
      excerpt: guide.excerpt,
      seoTitle: guide.seoTitle,
      seoDescription: guide.seoDescription,
      coverImage: guide.coverImage,
      coverAlt: guide.coverAlt,
      ogImage: guide.ogImage,
      ogImageAlt: guide.ogImageAlt,
      shareTitle: guide.shareTitle,
      shareDescription: guide.shareDescription,
      shareText: guide.shareText,
      publishedAt: guide.publishedAt,
      updatedAt: guide.updatedAt,
      authorName: guide.author.name,
      tags: guide.tags,
      canonicalUrl: guide.canonicalUrl,
      category: guide.category,
      pathPrefix: '/guides/',
    },
    { isPublished: guide.status === 'published' }
  );
}

export default async function GuideDynamicPage({ params }: GuidePageProps) {
  const { slug } = await params;
  const guide = getGuideBySlug(slug, true);

  if (!guide) {
    notFound();
  }

  const guideSchema = {
    '@context': 'https://schema.org',
    '@type': 'Article',
    mainEntityOfPage: {
      '@type': 'WebPage',
      '@id': guide.canonicalUrl || `https://heycora.in/guides/${guide.slug}/`,
    },
    headline: guide.title,
    description: guide.excerpt,
    image: [guide.ogImage || guide.coverImage],
    datePublished: guide.publishedAt,
    dateModified: guide.updatedAt,
    author: {
      '@type': 'Person',
      name: guide.author.name,
      url: guide.author.linkedin || guide.author.x || 'https://heycora.in/about/',
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

  const breadcrumbsSchema = {
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
        name: 'Guides & Playbooks',
        item: 'https://heycora.in/guides/',
      },
      {
        '@type': 'ListItem',
        position: 3,
        name: guide.title,
        item: guide.canonicalUrl || `https://heycora.in/guides/${guide.slug}/`,
      },
    ],
  };

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(guideSchema) }}
      />
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbsSchema) }}
      />
      {guide.faqs && guide.faqs.length > 0 && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify({
              '@context': 'https://schema.org',
              '@type': 'FAQPage',
              mainEntity: guide.faqs.map((faq) => ({
                '@type': 'Question',
                name: faq.question,
                acceptedAnswer: {
                  '@type': 'Answer',
                  text: faq.answer,
                },
              })),
            }),
          }}
        />
      )}
      <GuideDetailView guide={guide} />
    </>
  );
}
