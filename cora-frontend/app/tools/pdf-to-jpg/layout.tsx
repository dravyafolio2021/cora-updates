import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'PDF to JPG Converter Online Free (Crisp 2x Retina) | Cora',
  description: 'Convert PDF pages into high-resolution JPG images at crisp 2x retina clarity online free. 100% private in-browser tool with zero server uploads.',
  keywords: [
    'pdf to jpg converter online free',
    'pdf to jpg',
    'convert pdf to image free',
    'pdf to jpeg high resolution',
    'pdf to jpg 300 dpi',
    'in-browser pdf to image',
    'cora pdf tools'
  ],
  openGraph: {
    title: 'PDF to JPG Converter Online Free (Crisp 2x Retina) | Cora',
    description: 'Convert PDF pages into high-resolution JPG images online free. 100% private in-browser tool.',
    url: 'https://heycora.in/tools/pdf-to-jpg',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'PDF to JPG Converter Online Free (Crisp 2x Retina)',
    description: 'Convert PDF pages into high-resolution JPG images online free with zero server uploads.',
  },
};

export default function PdfToJpgLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
