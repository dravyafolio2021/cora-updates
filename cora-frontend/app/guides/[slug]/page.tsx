import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { getGuideBySlug, getAllGuideSlugs, Guide } from '@/lib/guides-data';
import { buildEditorialMetadata } from '@/lib/editorial-seo';
import { GuideDetailView } from '@/components/guides/GuideDetailView';
import { fetchContentBySlug, fetchContentEntries } from '@/lib/content-api';

interface GuidePageProps {
  params: Promise<{ slug: string }>;
}

export const dynamicParams = false;

export async function generateStaticParams() {
  const fallbackSlugs = getAllGuideSlugs(true).map((slug) => ({ slug }));
  const growthGuides = await fetchContentEntries({ type: 'guide', status: 'published' });
  const growthSlugs = growthGuides.map((g) => ({ slug: g.slug }));
  return [...fallbackSlugs, ...growthSlugs];
}

export async function generateMetadata({ params }: GuidePageProps): Promise<Metadata> {
  const { slug } = await params;

  // 1. Check Growth CMS
  const growthGuide = await fetchContentBySlug(slug, 'guide');
  if (growthGuide) {
    return buildEditorialMetadata(
      {
        slug: growthGuide.slug,
        title: growthGuide.title,
        dek: growthGuide.excerpt,
        excerpt: growthGuide.excerpt,
        seoTitle: growthGuide.seo?.title || growthGuide.title,
        seoDescription: growthGuide.seo?.meta_description || growthGuide.excerpt,
        coverImage: growthGuide.seo?.og_image || '/images/cora_footer_landscape.jpg',
        coverAlt: growthGuide.title,
        ogImage: growthGuide.seo?.og_image || '/images/cora_footer_landscape.jpg',
        ogImageAlt: growthGuide.title,
        shareTitle: growthGuide.seo?.title || growthGuide.title,
        shareDescription: growthGuide.seo?.meta_description || growthGuide.excerpt,
        publishedAt: growthGuide.published_at || growthGuide.created_at,
        updatedAt: growthGuide.updated_at,
        authorName: growthGuide.author?.name || 'Dravya Bansal',
        tags: growthGuide.secondary_keywords || ['Agency Operations'],
        canonicalUrl: `https://heycora.in/guides/${growthGuide.slug}/`,
        category: 'client-management',
        pathPrefix: '/guides/',
      },
      { isPublished: growthGuide.status === 'published' }
    );
  }

  // 2. Fallback
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

  // 1. Check Growth CMS
  const growthGuide = await fetchContentBySlug(slug, 'guide');
  let guide: Guide | null = null;

  if (growthGuide) {
    guide = {
      slug: growthGuide.slug,
      status: (growthGuide.status === 'published' ? 'published' : 'draft') as any,
      title: growthGuide.title,
      dek: growthGuide.excerpt,
      excerpt: growthGuide.excerpt,
      coverImage: growthGuide.seo?.og_image || '/images/cora_footer_landscape.jpg',
      coverAlt: growthGuide.title,
      ogImage: growthGuide.seo?.og_image || '/images/cora_footer_landscape.jpg',
      ogImageAlt: growthGuide.title,
      author: {
        slug: 'dravya-bansal',
        name: growthGuide.author?.name || 'Dravya Bansal',
        role: growthGuide.author?.role || 'Co-founder & CEO, Cora',
        avatar: growthGuide.author?.avatar || '/images/founder.jpeg',
        shortBio: 'Co-founder & CEO at Cora.',
        bio: 'Co-founder & CEO at Cora. Dravya leads product strategy, autonomous operations, and infrastructure engineering.',
      },
      publishedAt: growthGuide.published_at || growthGuide.created_at,
      updatedAt: growthGuide.updated_at,
      category: 'client-management',
      qualityLabel: 'Pillar Playbook',
      tags: growthGuide.secondary_keywords || ['Agency Operations', 'Client Onboarding', 'Async Workflows'],
      readTime: growthGuide.read_time || '18 min read',
      canonicalUrl: `https://heycora.in/guides/${growthGuide.slug}/`,
      seoTitle: growthGuide.seo?.title || growthGuide.title,
      seoDescription: growthGuide.seo?.meta_description || growthGuide.excerpt,
      chapterCount: growthGuide.chapters ? growthGuide.chapters.length : 0,
      downloadableAsset: {
        assetId: 'agency-onboarding-pack',
        title: growthGuide.cta?.title || 'Agency Client Onboarding Pack',
        description: growthGuide.cta?.description || 'Operational templates for moving a client from signed proposal to an organised first month.',
        fileUrl: growthGuide.cta?.download_url || '/uploads/growth/Agency_Client_Onboarding_Playbook.pdf',
        fileType: 'pdf',
        fileSize: growthGuide.cta?.file_size || '2.0 MB',
        highlights: [
          '8 structured onboarding chapters',
          'Pre-kickoff access & credential deposit checklists',
          '72-hour async sign-off SLA matrix',
          'Milestone lock and payment gating templates',
        ],
      },
      chapters: (growthGuide.chapters || []).map((ch: any) => {
        const rawBlocks = [...(ch.blocks || [])];
        const adaptedBlocks: any[] = [];

        // 1. Featured Image Block
        if (ch.featured_asset_url) {
          adaptedBlocks.push({
            id: `blk_feat_${ch.slug}`,
            type: 'image',
            version: 1,
            data: {
              url: ch.featured_asset_url,
              alt: `${ch.title} Featured Architecture`,
              caption: `${ch.title} — Operational Blueprint`,
              aspectRatio: '16/9',
            },
          });
        }

        // 2. Main Content Blocks
        adaptedBlocks.push(...rawBlocks);

        // 3. Infographics Blocks
        if (Array.isArray(ch.infographic_asset_ids)) {
          ch.infographic_asset_ids.forEach((infoUrl: string, infoIdx: number) => {
            adaptedBlocks.push({
              id: `blk_info_${ch.slug}_${infoIdx}`,
              type: 'image',
              version: 1,
              data: {
                url: infoUrl,
                alt: `${ch.title} Infographic Matrix ${infoIdx + 1}`,
                caption: `${ch.title} — Visual Process Flow`,
                aspectRatio: '16/9',
              },
            });
          });
        }

        return {
          id: ch.id || ch.slug || String(ch.number),
          number: ch.number,
          slug: ch.slug,
          title: ch.title,
          summary: ch.summary,
          readTime: ch.read_time || '3 min read',
          blocks: adaptedBlocks.map((b: any) => ({
            type: b.type,
            ...(b.data || b),
          })),
        };
      }),
      guideCategory: 'operations' as any,
      sources: growthGuide.sources || [],
      relatedArticles: [],
      relatedGuides: [],
    };
  }

  // 2. Fallback
  if (!guide) {
    guide = getGuideBySlug(slug, true) || null;
  }

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
