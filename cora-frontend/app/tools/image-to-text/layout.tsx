import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Image to Text OCR Online Free - Extract Text from Images | Cora',
  description: 'Extract editable text from receipts, documents, book pages, and screenshots with 100% private in-browser OCR. Live confidence rating and 1-click text download.',
  keywords: [
    'image to text',
    'ocr online',
    'extract text from image',
    'picture to text',
    'screenshot to text',
    'receipt ocr',
    'free image ocr',
    'cora ocr scanner',
    'private image to text'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/image-to-text',
  },
  openGraph: {
    title: 'Image to Text OCR Online Free - Extract Text from Images | Cora',
    description: 'Extract editable text from receipts, documents, book pages, and screenshots with 100% private in-browser OCR. Live confidence rating and 1-click text download.',
    url: 'https://heycora.in/tools/image-to-text',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Image to Text OCR Online Free - Extract Text from Images | Cora',
    description: 'Extract editable text from receipts, documents, book pages, and screenshots with 100% private in-browser OCR. Live confidence rating and 1-click text download.',
  },
};

export default function ImageToTextLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
