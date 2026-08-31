import { Metadata } from 'next';
import { DOCS_DATA } from '@/lib/docs-data';
import { DocsClient } from '@/components/docs/DocsClient';

export const metadata: Metadata = {
  title: 'Documentation & APIs — Cora Developer Hub',
  description: 'Platform architecture guides, REST API specs, MCP gateway configuration, and developer tutorials.',
  alternates: {
    canonical: 'https://heycora.in/docs',
  },
  openGraph: {
    title: 'Documentation & APIs — Cora Developer Hub',
    description: 'Platform architecture guides, REST API specs, MCP gateway configuration, and developer tutorials.',
    url: 'https://heycora.in/docs',
    siteName: 'Cora',
  },
};

export default function DocsIndexPage() {
  const defaultArticle = DOCS_DATA[0]; // Platform Architecture
  return <DocsClient currentArticle={defaultArticle} />;
}
