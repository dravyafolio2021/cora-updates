import React from 'react';
import { notFound } from 'next/navigation';
import type { Metadata } from 'next';
import { INDUSTRY_WORKSPACES } from '@/lib/industry-data';
import { IndustryDetailClient } from '@/components/industry/IndustryDetailClient';

export async function generateStaticParams() {
  return INDUSTRY_WORKSPACES.map((item) => ({
    slug: item.slug,
  }));
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ slug: string }>;
}): Promise<Metadata> {
  const { slug } = await params;
  const workspace = INDUSTRY_WORKSPACES.find((item) => item.slug === slug);

  if (!workspace) {
    return {
      title: 'Industry Solution Not Found — Cora OS',
    };
  }

  return {
    title: `${workspace.title} Operating System — Cora for ${workspace.sectorLabel}`,
    description: workspace.heroDescription,
    keywords: [
      `${workspace.title} software`,
      `${workspace.shortTitle} CRM`,
      `${workspace.sectorLabel} operating system`,
      `${workspace.sacCode} GST billing`,
      'Indian IT Act 2000 contracts',
      'autonomous service business workspace',
      'client portal and e-signatures',
    ],
    alternates: {
      canonical: `https://heycora.in/use-cases/${workspace.slug}/`,
    },
    openGraph: {
      title: `${workspace.title} Operating System — Cora OS`,
      description: workspace.heroDescription,
      url: `https://heycora.in/use-cases/${workspace.slug}`,
      type: 'article',
      siteName: 'Cora OS for Professional Services',
    },
    twitter: {
      card: 'summary_large_image',
      title: `${workspace.title} Operating System — Cora OS`,
      description: workspace.heroDescription,
    }
  };
}

export default async function IndustrySlugPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const workspace = INDUSTRY_WORKSPACES.find((item) => item.slug === slug);

  if (!workspace) {
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
        name: 'Industries',
        item: 'https://heycora.in/use-cases',
      },
      {
        '@type': 'ListItem',
        position: 3,
        name: workspace.shortTitle,
        item: `https://heycora.in/use-cases/${workspace.slug}`,
      },
    ],
  };

  const softwareSchema = {
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    name: `${workspace.title} Operating System`,
    applicationCategory: 'BusinessApplication',
    operatingSystem: 'Web, iOS, Android, macOS, Windows',
    offers: {
      '@type': 'Offer',
      price: '0',
      priceCurrency: 'INR',
    },
    description: workspace.heroDescription,
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
      <IndustryDetailClient workspace={workspace} />
    </>
  );
}
