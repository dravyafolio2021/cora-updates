import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Split Image Online Free - Cut Photos into Grids & Carousels | Cora',
  description: 'Split and slice images online free in your browser RAM. Cut photos into 3-part seamless Instagram carousels, 9-part profile grids (3x3), or custom tile matrices with 1-click batch downloads and zero cloud uploads.',
  keywords: [
    'split image online',
    'slice image into grid',
    'instagram carousel splitter',
    '3x3 grid photo cutter',
    'split photo into tiles',
    'image partition tool',
    'crop image into pieces',
    'cora creative studio'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/split-image',
  },
  openGraph: {
    title: 'Split Image Online Free - Cut Photos into Grids & Carousels | Cora',
    description: 'Slice panoramic photos into 3-part seamless carousels or 9-part profile grids in browser memory with zero cloud uploads.',
    url: 'https://heycora.in/tools/split-image',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Split Image Online Free - Cut Photos into Grids & Carousels | Cora',
    description: 'Slice photos into seamless Instagram carousels and 3x3 profile grids in browser memory.',
  },
};

export default function SplitImageLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
