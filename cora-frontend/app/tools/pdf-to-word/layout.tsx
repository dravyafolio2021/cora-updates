import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'PDF to Word Converter Online Free (.doc / .docx) | Cora',
  description: 'Convert PDF documents into editable Word files (.doc) online free. Extract structured text, clauses, and headings with 100% in-browser memory privacy.',
  keywords: [
    'pdf to word converter online free',
    'pdf to word',
    'convert pdf to docx free',
    'pdf to doc',
    'editable word document from pdf',
    'in-browser pdf to word',
    'cora pdf tools'
  ],
  openGraph: {
    title: 'PDF to Word Converter Online Free (.doc / .docx) | Cora',
    description: 'Convert PDF documents into editable Microsoft Word files online free with zero server uploads.',
    url: 'https://heycora.in/tools/pdf-to-word',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'PDF to Word Converter Online Free (.doc / .docx)',
    description: 'Convert PDF documents into editable Microsoft Word files online free with zero server uploads.',
  },
};

export default function PdfToWordLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
