import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Word & Document to PDF Converter Online Free | Cora',
  description: 'Convert Word documents (.doc, .docx), plain text, and contracts into publication-ready A4 PDF files online free. 100% private in-browser tool with zero server uploads.',
  keywords: [
    'word to pdf converter online free',
    'word to pdf',
    'doc to pdf converter',
    'convert word to pdf free',
    'document to pdf converter online free',
    'text to pdf',
    'cora pdf tools'
  ],
  openGraph: {
    title: 'Word & Document to PDF Converter Online Free | Cora',
    description: 'Convert Word documents and contract text into standardized A4 PDF files online free. 100% private in-browser tool.',
    url: 'https://heycora.in/tools/word-to-pdf',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Word & Document to PDF Converter Online Free',
    description: 'Convert Word documents and text to PDF online free with zero server uploads.',
  },
};

export default function WordToPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
