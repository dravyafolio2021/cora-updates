import React from 'react';
import { notFound } from 'next/navigation';
import type { Metadata } from 'next';
import { BUILT_MODULES } from '@/lib/features-data';
import { FeatureDetailClient } from '@/components/features/FeatureDetailClient';

export async function generateStaticParams() {
  return BUILT_MODULES.map((item) => ({
    slug: item.slug,
  }));
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ slug: string }>;
}): Promise<Metadata> {
  const { slug } = await params;
  const feature = BUILT_MODULES.find((item) => item.slug === slug);

  if (!feature) {
    return {
      title: 'Feature Not Found — Cora Studio OS',
    };
  }

  return {
    title: `${feature.title} — Cora Studio OS`,
    description: feature.heroDescription,
    keywords: [
      `${feature.shortTitle}`,
      `${feature.shortTitle} for studios`,
      `${feature.categoryLabel}`,
      'creative studio management',
      'autonomous studio operating system',
      'commercial photography crm',
      '18% GST tax invoices',
      'legal e-signatures',
    ],
    alternates: {
      canonical: `https://heycora.in/features/${feature.slug}/`,
    },
    openGraph: {
      title: `${feature.title} — Cora Studio OS`,
      description: feature.heroDescription,
      url: `https://heycora.in/features/${feature.slug}`,
      type: 'article',
      siteName: 'Cora Studio OS',
    },
    twitter: {
      card: 'summary_large_image',
      title: `${feature.title} — Cora Studio OS`,
      description: feature.heroDescription,
    }
  };
}

export default async function FeaturePage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const feature = BUILT_MODULES.find((item) => item.slug === slug);

  if (!feature) {
    notFound();
  }

  // Schema.org Structured Data
  const breadcrumbSchema = {
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
        name: 'Features',
        item: 'https://heycora.in/features',
      },
      {
        '@type': 'ListItem',
        position: 3,
        name: feature.shortTitle,
        item: `https://heycora.in/features/${feature.slug}`,
      },
    ],
  };

  const softwareSchema = {
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    name: feature.title,
    applicationCategory: 'BusinessApplication',
    operatingSystem: 'Web, iOS, Android, macOS, Windows',
    offers: {
      '@type': 'Offer',
      price: '0',
      priceCurrency: 'INR',
      description: 'Free Forever plan with 1,000 monthly operations',
    },
    featureList: feature.capabilities.map((c) => c.title),
  };

  const faqSchema = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: feature.faqs.map((faq) => ({
      '@type': 'Question',
      name: faq.question,
      acceptedAnswer: {
        '@type': 'Answer',
        text: faq.answer,
      },
    })),
  };

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbSchema) }}
      />
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(softwareSchema) }}
      />
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }}
      />
      
      <main className="w-full relative overflow-hidden bg-white text-zinc-900">
        <FeatureDetailClient feature={feature} />
      </main>
    </>
  );
}
