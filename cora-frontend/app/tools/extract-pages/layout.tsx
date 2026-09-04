import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Extract PDF Pages Online Free - Isolate & Save Specific Pages | Cora',
  description: 'Extract specific pages or custom page ranges from your PDF online for free. Lossless quality, instant client-side processing, and 100% data privacy.',
  keywords: [
    'extract pdf pages',
    'extract pages from pdf online',
    'separate pdf pages',
    'extract pdf sheets free',
    'isolate pdf pages',
    'client-side pdf extractor',
    'cora extract pages'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/extract-pages',
  },
  openGraph: {
    title: 'Extract PDF Pages Online Free - Isolate & Save Specific Pages | Cora',
    description: 'Extract specific pages or custom page ranges from your PDF online for free. Lossless quality, instant client-side processing, and 100% data privacy.',
    url: 'https://heycora.in/tools/extract-pages',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Extract PDF Pages Online Free - Isolate & Save Specific Pages | Cora',
    description: 'Extract specific pages or custom page ranges from your PDF online for free. Lossless quality, instant client-side processing, and 100% data privacy.',
  },
};

export default function ExtractPagesLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
