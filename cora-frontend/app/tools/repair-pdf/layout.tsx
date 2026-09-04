import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Repair PDF Online Free - Fix Corrupted & Damaged PDFs | Cora',
  description: 'Repair corrupted, broken, or unreadable PDF documents online for free. Reconstruct xref tables and recover pages directly in your browser with 100% data privacy.',
  keywords: [
    'repair pdf',
    'fix corrupted pdf',
    'repair damaged pdf',
    'recover broken pdf',
    'pdf repair tool',
    'rebuild pdf xref table',
    'client-side pdf repair',
    'cora repair pdf'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/repair-pdf',
  },
  openGraph: {
    title: 'Repair PDF Online Free - Fix Corrupted & Damaged PDFs | Cora',
    description: 'Repair corrupted, broken, or unreadable PDF documents online for free. Reconstruct xref tables and recover pages directly in your browser with 100% data privacy.',
    url: 'https://heycora.in/tools/repair-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Repair PDF Online Free - Fix Corrupted & Damaged PDFs | Cora',
    description: 'Repair corrupted, broken, or unreadable PDF documents online for free. Reconstruct xref tables and recover pages directly in your browser with 100% data privacy.',
  },
};

export default function RepairPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
