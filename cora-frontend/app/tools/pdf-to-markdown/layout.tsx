import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'PDF to Markdown Converter Online Free (.md) | Cora',
  description: 'Convert PDF documents, whitepapers, and specs into clean GitHub-flavored Markdown (.md) online free. 100% private in-browser memory tool with zero server uploads.',
  keywords: [
    'pdf to markdown converter online free',
    'pdf to md',
    'convert pdf to markdown',
    'extract markdown from pdf',
    'pdf to github flavored markdown',
    'in-browser pdf to markdown',
    'cora pdf tools'
  ],
  openGraph: {
    title: 'PDF to Markdown Converter Online Free (.md) | Cora',
    description: 'Convert PDF documents and specs into clean GitHub-flavored Markdown online free.',
    url: 'https://heycora.in/tools/pdf-to-markdown',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'PDF to Markdown Converter Online Free (.md)',
    description: 'Convert PDF documents to clean GitHub-flavored Markdown online free with zero server uploads.',
  },
};

export default function PdfToMarkdownLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
