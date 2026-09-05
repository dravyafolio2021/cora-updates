import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Resize Image Online Free - Exact Pixels & Social Presets | Cora',
  description: 'Resize image dimensions in pixels or percentage. Lock aspect ratio, apply Instagram, YouTube, and X/Twitter presets, and export in high resolution with 100% privacy.',
  keywords: [
    'resize image',
    'resize image online',
    'change image dimensions',
    'instagram image resizer',
    'youtube thumbnail resizer',
    'scale image pixels',
    'image resizer free',
    'aspect ratio lock',
    'cora image resizer'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/resize-image',
  },
  openGraph: {
    title: 'Resize Image Online Free - Exact Pixels & Social Presets | Cora',
    description: 'Resize image dimensions in pixels or percentage with aspect ratio lock and social presets. 100% in-browser privacy.',
    url: 'https://heycora.in/tools/resize-image',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Resize Image Online Free - Exact Pixels & Social Presets | Cora',
    description: 'Resize image dimensions in pixels or percentage with aspect ratio lock and social presets.',
  },
};

export default function ResizeImageLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
