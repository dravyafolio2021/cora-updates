import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Free PDF Tools Online - 25+ Secure In-Browser PDF Utilities | Cora',
  description: 'The ultimate suite of 25+ free in-browser PDF tools. Merge, split, compress, convert, OCR, translate, and secure PDF files with 100% data privacy and zero server uploads.',
  keywords: [
    'pdf tools',
    'free pdf utilities online',
    'merge pdf',
    'compress pdf',
    'convert pdf',
    'ocr pdf',
    'pdf security',
    'edit pdf',
    'client-side pdf tools',
    'cora pdf hub'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/pdf',
  },
  openGraph: {
    title: 'Free PDF Tools Online - 25+ Secure In-Browser PDF Utilities | Cora',
    description: 'The ultimate suite of 25+ free in-browser PDF tools. Merge, split, compress, convert, OCR, translate, and secure PDF files with 100% data privacy and zero server uploads.',
    url: 'https://heycora.in/tools/pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Free PDF Tools Online - 25+ Secure In-Browser PDF Utilities | Cora',
    description: 'The ultimate suite of 25+ free in-browser PDF tools. Merge, split, compress, convert, OCR, translate, and secure PDF files with 100% data privacy and zero server uploads.',
  },
};

export default function PdfCategoryLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
