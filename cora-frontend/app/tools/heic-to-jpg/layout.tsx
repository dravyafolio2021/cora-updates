import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'HEIC to JPG Converter - Free Apple iPhone Photo Decoder | Cora',
  description: 'Convert Apple iPhone HEIC and HEIF photos to high-quality JPG or PNG images directly in your browser. Batch convert multiple files with instant ZIP download.',
  keywords: [
    'heic to jpg',
    'convert heic to jpg',
    'heic converter',
    'iphone photo to jpg',
    'apple heic to jpg',
    'heic to png',
    'batch heic to jpg',
    'cora heic converter'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/heic-to-jpg',
  },
  openGraph: {
    title: 'HEIC to JPG Converter - Free Apple iPhone Photo Decoder | Cora',
    description: 'Convert Apple iPhone HEIC and HEIF photos to high-quality JPG or PNG images directly in your browser. Batch convert multiple files with instant ZIP download.',
    url: 'https://heycora.in/tools/heic-to-jpg',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'HEIC to JPG Converter - Free Apple iPhone Photo Decoder | Cora',
    description: 'Convert Apple iPhone HEIC and HEIF photos to high-quality JPG or PNG images directly in your browser. Batch convert multiple files with instant ZIP download.',
  },
};

export default function HeicToJpgLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
