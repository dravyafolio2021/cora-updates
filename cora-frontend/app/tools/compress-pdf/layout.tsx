import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Compress PDF Online Free - Reduce PDF File Size | Cora',
  description: 'Compress PDF files online for free. Reduce PDF size in your browser with 100% data privacy. No signup or server uploads required.',
  keywords: [
    'compress pdf',
    'compress pdf online',
    'reduce pdf size',
    'compress pdf free',
    'shrink pdf',
    'reduce pdf file size',
    'cora pdf compressor',
    'lossless pdf compression',
    'offline pdf optimizer'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/compress-pdf',
  },
  openGraph: {
    title: 'Compress PDF Online Free - Reduce PDF File Size | Cora',
    description: 'Compress PDF files online for free. Reduce PDF size in your browser with 100% data privacy. No signup or server uploads required.',
    url: 'https://heycora.in/tools/compress-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Compress PDF Online Free - Reduce PDF File Size | Cora',
    description: 'Compress PDF files online for free. Reduce PDF size in your browser with 100% data privacy. No signup or server uploads required.',
  },
};

export default function CompressPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
