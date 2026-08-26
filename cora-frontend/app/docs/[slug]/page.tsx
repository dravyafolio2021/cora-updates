import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { DOCS_DATA } from '@/lib/docs-data';
import { DocsClient } from '@/components/docs/DocsClient';

interface PageProps {
  params: Promise<{
    slug: string;
  }>;
}

export async function generateStaticParams() {
  return DOCS_DATA.map((article) => ({
    slug: article.slug,
  }));
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;
  const article = DOCS_DATA.find((a) => a.slug === slug);

  if (!article) {
    return {
      title: 'Documentation Not Found — Cora',
    };
  }

  return {
    title: `${article.title} — Cora Documentation`,
    description: article.description,
    alternates: {
      canonical: `https://heycora.in/docs/${article.slug}`,
    },
    openGraph: {
      title: `${article.title} — Cora Documentation`,
      description: article.description,
      url: `https://heycora.in/docs/${article.slug}`,
      siteName: 'Cora Platform',
      type: 'article',
    },
  };
}

export default async function DocSlugPage({ params }: PageProps) {
  const { slug } = await params;
  const article = DOCS_DATA.find((a) => a.slug === slug);

  if (!article) {
    notFound();
  }

  return <DocsClient currentArticle={article} />;
}
