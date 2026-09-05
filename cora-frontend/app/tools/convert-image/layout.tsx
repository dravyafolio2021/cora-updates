import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Convert Image Online Free - Batch JPG, PNG, WebP Converter | Cora',
  description: 'Convert images online between JPG, PNG, and WebP with 100% private client-side processing in browser memory. Batch dropzone with instant ZIP download.',
  keywords: [
    'convert image',
    'image converter',
    'png to jpg',
    'jpg to png',
    'webp to png',
    'png to webp',
    'convert image online',
    'batch image converter',
    'cora image converter',
    'private image converter'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/convert-image',
  },
  openGraph: {
    title: 'Convert Image Online Free - Batch JPG, PNG, WebP Converter | Cora',
    description: 'Convert images online between JPG, PNG, and WebP with 100% private client-side processing in browser memory. Batch dropzone with instant ZIP download.',
    url: 'https://heycora.in/tools/convert-image',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Convert Image Online Free - Batch JPG, PNG, WebP Converter | Cora',
    description: 'Convert images online between JPG, PNG, and WebP with 100% private client-side processing in browser memory. Batch dropzone with instant ZIP download.',
  },
};

export default function ConvertImageLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
