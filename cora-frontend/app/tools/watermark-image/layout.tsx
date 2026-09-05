import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Watermark Image Online Free - 100% Client-Side Protection | Cora',
  description: 'Watermark photos and creative proofs online free. Apply custom diagonal text, studio logos, repeated tiled patterns, and opacity stamps in your browser RAM with zero uploads.',
  keywords: [
    'watermark image online free',
    'photo watermark tool',
    'add watermark to photo',
    'client proof watermark',
    'protect photography online',
    'text watermark maker',
    'logo watermark app',
    'cora creative studio'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/watermark-image',
  },
  openGraph: {
    title: 'Watermark Image Online Free - 100% Client-Side Protection | Cora',
    description: 'Add custom text stamps or logo watermarks to your photography deliverables. 100% in-browser memory protection with zero cloud uploads.',
    url: 'https://heycora.in/tools/watermark-image',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Watermark Image Online Free - 100% Client-Side Protection | Cora',
    description: 'Protect your creative deliverables with custom text stamps and logo watermarks in browser RAM.',
  },
};

export default function WatermarkImageLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
