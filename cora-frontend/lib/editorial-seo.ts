import type { Metadata } from 'next';

/**
 * Reusable Editorial SEO & Social Preview Engine
 * Designed for Blog Articles, Guides, Research, and Lead Magnets
 */

export interface EditorialSocialMetadataInput {
  slug: string;
  title: string;
  dek?: string;
  excerpt?: string;
  seoTitle?: string;
  seoDescription?: string;
  coverImage: string;
  coverAlt?: string;
  ogImage?: string;
  ogImageAlt?: string;
  shareTitle?: string;
  shareDescription?: string;
  shareText?: string;
  publishedAt?: string;
  updatedAt?: string;
  authorName?: string;
  tags?: string[];
  canonicalUrl?: string;
  category?: string;
  pathPrefix?: string; // Default: '/blog/'
}

export interface ResolvedEditorialSocialData {
  canonicalUrl: string;
  seoTitle: string;
  seoDescription: string;
  coverImage: string;
  coverAlt: string;
  ogImage: string;
  ogImageUrl: string;
  ogImageAlt: string;
  shareTitle: string;
  shareDescription: string;
  shareText: string;
}

const SITE_ORIGIN = 'https://heycora.in';

/**
 * Ensures any image path is converted into an absolute public URL
 */
export function toAbsoluteUrl(pathOrUrl: string): string {
  if (!pathOrUrl) return `${SITE_ORIGIN}/images/cora_pricing_pure_sky.jpg`;
  if (pathOrUrl.startsWith('http://') || pathOrUrl.startsWith('https://')) {
    return pathOrUrl;
  }
  const cleanPath = pathOrUrl.startsWith('/') ? pathOrUrl : `/${pathOrUrl}`;
  return `${SITE_ORIGIN}${cleanPath}`;
}

/**
 * Resolves all social metadata fields with robust fallbacks
 */
export function resolveEditorialSocialData(
  input: EditorialSocialMetadataInput
): ResolvedEditorialSocialData {
  const prefix = input.pathPrefix || '/blog/';
  const cleanPrefix = prefix.endsWith('/') ? prefix : `${prefix}/`;
  const canonicalUrl =
    input.canonicalUrl || `${SITE_ORIGIN}${cleanPrefix}${input.slug}/`;

  const coverImage = input.coverImage || '/images/cora_pricing_pure_sky.jpg';
  const coverAlt = input.coverAlt || input.title;

  const ogImage = input.ogImage || coverImage;
  const ogImageUrl = toAbsoluteUrl(ogImage);
  const ogImageAlt = input.ogImageAlt || coverAlt;

  const seoTitle = input.seoTitle || input.title;
  const seoDescription =
    input.seoDescription || input.excerpt || input.dek || input.title;

  const shareTitle = input.shareTitle || input.title;
  const shareDescription =
    input.shareDescription || input.excerpt || input.seoDescription || input.dek || '';

  const shareText =
    input.shareText ||
    (shareDescription ? `${shareTitle}\n\n${shareDescription}` : shareTitle);

  return {
    canonicalUrl,
    seoTitle,
    seoDescription,
    coverImage,
    coverAlt,
    ogImage,
    ogImageUrl,
    ogImageAlt,
    shareTitle,
    shareDescription,
    shareText,
  };
}

/**
 * Builds standard Next.js Metadata for editorial entities (Articles, Guides, Whitepapers)
 */
export function buildEditorialMetadata(
  input: EditorialSocialMetadataInput,
  options?: { isPublished?: boolean }
): Metadata {
  const isPublished = options?.isPublished ?? true;
  const resolved = resolveEditorialSocialData(input);

  if (!isPublished) {
    return {
      title: `[DRAFT] ${resolved.shareTitle} | Cora Editorial`,
      description: resolved.seoDescription,
      robots: {
        index: false,
        follow: false,
        nocache: true,
        googleBot: {
          index: false,
          follow: false,
          'max-video-preview': -1,
          'max-image-preview': 'none',
          'max-snippet': -1,
        },
      },
    };
  }

  return {
    title: `${resolved.seoTitle} | Cora`,
    description: resolved.seoDescription,
    alternates: {
      canonical: resolved.canonicalUrl,
    },
    robots: {
      index: true,
      follow: true,
      googleBot: {
        index: true,
        follow: true,
        'max-video-preview': -1,
        'max-image-preview': 'large',
        'max-snippet': -1,
      },
    },
    openGraph: {
      type: 'article',
      title: resolved.shareTitle,
      description: resolved.shareDescription,
      url: resolved.canonicalUrl,
      siteName: 'Cora',
      publishedTime: input.publishedAt,
      modifiedTime: input.updatedAt || input.publishedAt,
      authors: input.authorName ? [input.authorName] : ['Cora Editorial'],
      tags: input.tags,
      images: [
        {
          url: resolved.ogImageUrl,
          width: 1200,
          height: 630,
          alt: resolved.ogImageAlt,
          type: 'image/webp',
        },
      ],
    },
    twitter: {
      card: 'summary_large_image',
      title: resolved.shareTitle,
      description: resolved.shareDescription,
      images: [resolved.ogImageUrl],
      creator: '@dravyafolio',
      site: '@dravyafolio',
    },
  };
}
