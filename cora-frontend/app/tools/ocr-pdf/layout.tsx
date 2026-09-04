import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'OCR PDF Online Free - Convert Scanned PDF to Searchable Text | Cora',
  description: 'Convert scanned PDFs, receipts, and images into editable, searchable text online for free. 100% private in-browser optical character recognition with zero server uploads.',
  keywords: [
    'ocr pdf',
    'ocr pdf online',
    'extract text from scanned pdf',
    'searchable pdf free',
    'convert scanned pdf to text',
    'client-side ocr',
    'cora ocr scanner',
    'pdf optical character recognition'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/ocr-pdf',
  },
  openGraph: {
    title: 'OCR PDF Online Free - Convert Scanned PDF to Searchable Text | Cora',
    description: 'Convert scanned PDFs, receipts, and images into editable, searchable text online for free. 100% private in-browser optical character recognition with zero server uploads.',
    url: 'https://heycora.in/tools/ocr-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'OCR PDF Online Free - Convert Scanned PDF to Searchable Text | Cora',
    description: 'Convert scanned PDFs, receipts, and images into editable, searchable text online for free. 100% private in-browser optical character recognition with zero server uploads.',
  },
};

export default function OcrPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
