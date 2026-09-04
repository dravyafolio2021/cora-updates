import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'PowerPoint to PDF Converter Online Free (PPT, PPTX to PDF) | Cora',
  description: 'Convert PowerPoint presentations (PPT, PPTX) into clean landscape PDF decks online for free. Standard 16:9 & 4:3 aspect ratios with 100% in-browser privacy.',
  keywords: [
    'powerpoint to pdf',
    'pptx to pdf online free',
    'ppt to pdf converter',
    'convert presentation to pdf',
    'pitch deck to pdf',
    'client-side presentation converter',
    'cora powerpoint to pdf'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/powerpoint-to-pdf',
  },
  openGraph: {
    title: 'PowerPoint to PDF Converter Online Free (PPT, PPTX to PDF) | Cora',
    description: 'Convert PowerPoint presentations (PPT, PPTX) into clean landscape PDF decks online for free. Standard 16:9 & 4:3 aspect ratios with 100% in-browser privacy.',
    url: 'https://heycora.in/tools/powerpoint-to-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'PowerPoint to PDF Converter Online Free (PPT, PPTX to PDF) | Cora',
    description: 'Convert PowerPoint presentations (PPT, PPTX) into clean landscape PDF decks online for free. Standard 16:9 & 4:3 aspect ratios with 100% in-browser privacy.',
  },
};

export default function PowerpointToPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
