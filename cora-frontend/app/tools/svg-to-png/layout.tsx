import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'SVG to PNG Converter - High Resolution Vector Rasterizer | Cora',
  description: 'Convert SVG files or raw XML code to high-resolution PNG images up to 8x Print 300 DPI. 100% private client-side rasterization in browser memory.',
  keywords: [
    'svg to png',
    'convert svg to png',
    'svg rasterizer',
    'vector to png',
    'high resolution svg to png',
    'retina png converter',
    'cora svg to png',
    'svg code to png'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/svg-to-png',
  },
  openGraph: {
    title: 'SVG to PNG Converter - High Resolution Vector Rasterizer | Cora',
    description: 'Convert SVG files or raw XML code to high-resolution PNG images up to 8x Print 300 DPI. 100% private client-side rasterization in browser memory.',
    url: 'https://heycora.in/tools/svg-to-png',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'SVG to PNG Converter - High Resolution Vector Rasterizer | Cora',
    description: 'Convert SVG files or raw XML code to high-resolution PNG images up to 8x Print 300 DPI. 100% private client-side rasterization in browser memory.',
  },
};

export default function SvgToPngLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
