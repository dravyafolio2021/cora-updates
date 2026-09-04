import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Crop PDF Online Free - Trim PDF Margins & White Borders | Cora',
  description: 'Crop PDF margins and trim unwanted borders across all pages online for free. Precision margin sliders and interactive crop preview. 100% private in-browser tool.',
  keywords: [
    'crop pdf',
    'trim pdf margins',
    'cut pdf borders',
    'remove white margins from pdf',
    'crop pdf pages online free',
    'pdf crop tool',
    'cora crop pdf',
    'crop pdf letter a4'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/crop-pdf',
  },
  openGraph: {
    title: 'Crop PDF Online Free - Trim PDF Margins & White Borders | Cora',
    description: 'Crop PDF margins and trim unwanted borders across all pages online for free. Precision margin sliders and interactive crop preview. 100% private in-browser tool.',
    url: 'https://heycora.in/tools/crop-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Crop PDF Online Free - Trim PDF Margins & White Borders | Cora',
    description: 'Crop PDF margins and trim unwanted borders across all pages online for free. Precision margin sliders and interactive crop preview. 100% private in-browser tool.',
  },
};

export default function CropPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
