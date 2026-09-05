import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Free Background Remover Online - 100% Client-Side Private Tool | Cora',
  description: 'Remove image backgrounds instantly in your browser. 100% private in-memory chroma keying and edge thresholding for portraits, ecommerce products, and logos with zero cloud uploads.',
  keywords: [
    'remove background online free',
    'client-side background remover',
    'transparent png creator',
    'ecommerce product photo background remover',
    'photo cutout online',
    'transparent background maker',
    'private background removal',
    'cora creative studio'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/remove-background',
  },
  openGraph: {
    title: 'Free Background Remover Online - 100% Client-Side Private Tool | Cora',
    description: 'Isolate portraits, ecommerce products, and logos with clean alpha edges directly in your browser RAM. 100% free with zero cloud uploads.',
    url: 'https://heycora.in/tools/remove-background',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Free Background Remover Online - 100% Client-Side Private Tool | Cora',
    description: 'Isolate portraits, ecommerce products, and logos directly in browser RAM with zero cloud uploads.',
  },
};

export default function RemoveBackgroundLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
