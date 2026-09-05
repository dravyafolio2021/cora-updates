import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Combine Images Online Free - Merge & Stitch Photos Client-Side | Cora',
  description: 'Combine multiple images into one high-resolution composite online free. Stitch photos horizontally, vertically, or in 2x2 collage grids with custom gaps and background colors in browser memory.',
  keywords: [
    'combine images online',
    'merge photos free',
    'stitch images horizontally',
    'stack photos vertically',
    'photo collage maker online',
    'side by side photo joiner',
    'before and after image merger',
    'cora creative studio'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/combine-images',
  },
  openGraph: {
    title: 'Combine Images Online Free - Merge & Stitch Photos Client-Side | Cora',
    description: 'Merge and stitch photos horizontally, vertically, or in 2x2 grids in your browser RAM with zero server uploads.',
    url: 'https://heycora.in/tools/combine-images',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Combine Images Online Free - Merge & Stitch Photos Client-Side | Cora',
    description: 'Merge and stitch photos horizontally, vertically, or in 2x2 grids in your browser RAM with zero server uploads.',
  },
};

export default function CombineImagesLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
