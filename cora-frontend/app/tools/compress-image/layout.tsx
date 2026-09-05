import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Compress Image Online Free - Reduce JPG, PNG & WebP Size | Cora',
  description: 'Compress JPG, PNG, and WebP images directly in your browser. Lossless and high-efficiency compression with 100% client-side privacy. Zero server uploads.',
  keywords: [
    'compress image',
    'compress image online',
    'reduce image size',
    'compress jpg',
    'compress png',
    'compress webp',
    'image optimizer',
    'reduce photo kb',
    'compress image to 50kb',
    'compress image to 200kb',
    'cora image compressor'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/compress-image',
  },
  openGraph: {
    title: 'Compress Image Online Free - Reduce JPG, PNG & WebP Size | Cora',
    description: 'Compress JPG, PNG, and WebP images directly in your browser. 100% client-side privacy with zero server uploads.',
    url: 'https://heycora.in/tools/compress-image',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Compress Image Online Free - Reduce JPG, PNG & WebP Size | Cora',
    description: 'Compress JPG, PNG, and WebP images directly in your browser with zero server uploads.',
  },
};

export default function CompressImageLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
