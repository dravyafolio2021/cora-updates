import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Free Client-Side Image Tools Online - 12+ Private In-Browser Utilities | Cora',
  description: 'The complete suite of free in-browser image tools. Compress photos, convert between JPG/PNG/WebP/SVG, remove backgrounds, add watermarks, combine photos, and slice Instagram carousels with 100% client-side privacy.',
  keywords: [
    'free image tools online',
    'client-side image tools',
    'remove background free',
    'compress image online',
    'watermark photo free',
    'combine images online',
    'split image into grid',
    'jpg to png converter',
    'png to jpg converter',
    'svg to png rasterizer',
    'cora image studio'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/images',
  },
  openGraph: {
    title: 'Free Client-Side Image Tools Online - 12+ Private In-Browser Utilities | Cora',
    description: 'The complete suite of free in-browser image tools. Compression, format conversion, background removal, and creative studio utilities with 100% client-side privacy.',
    url: 'https://heycora.in/tools/images',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Free Client-Side Image Tools Online - 12+ Private In-Browser Utilities | Cora',
    description: 'The complete suite of free in-browser image tools with 100% client-side privacy.',
  },
};

export default function ImagesMasterCategoryLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
