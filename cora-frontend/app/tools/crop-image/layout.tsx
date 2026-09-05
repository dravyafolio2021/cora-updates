import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Crop Image Online Free - 1:1, 4:5, 16:9 & Circular Framing | Cora',
  description: 'Crop images online to standard aspect ratios (1:1 square, 4:5 portrait, 16:9 widescreen, circle avatar). Interactive zoom and pan with 100% in-browser privacy.',
  keywords: [
    'crop image',
    'crop image online',
    'circle crop',
    'crop photo to 1:1',
    'crop photo 16:9',
    'crop photo 4:5',
    'freeform image cropper',
    'circular image crop',
    'cora image cropper'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/crop-image',
  },
  openGraph: {
    title: 'Crop Image Online Free - 1:1, 4:5, 16:9 & Circular Framing | Cora',
    description: 'Crop images online to standard aspect ratios (1:1 square, 4:5 portrait, 16:9 widescreen, circle avatar). 100% client-side privacy.',
    url: 'https://heycora.in/tools/crop-image',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Crop Image Online Free - 1:1, 4:5, 16:9 & Circular Framing | Cora',
    description: 'Crop images online with interactive framing, standard aspect ratios, and circular avatar mode.',
  },
};

export default function CropImageLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
