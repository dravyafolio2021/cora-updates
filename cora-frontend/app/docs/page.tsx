import { Metadata } from 'next';
import { DOCS_DATA } from '@/lib/docs-data';
import { DocsClient } from '@/components/docs/DocsClient';

export const metadata: Metadata = {
  title: 'Documentation — Cora Studio OS Developer Hub',
  description: 'Complete architecture reference, API guides, and workflow documentation for Cora Studio OS.',
  alternates: {
    canonical: 'https://heycora.in/docs',
  },
};

export default function DocsIndexPage() {
  const defaultArticle = DOCS_DATA[0]; // Platform Architecture
  return <DocsClient currentArticle={defaultArticle} />;
}
