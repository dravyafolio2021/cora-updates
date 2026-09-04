import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Compare PDF Online Free - Side-by-Side PDF Difference Checker | Cora',
  description: 'Compare two PDF files side-by-side online for free. Spot clause changes, page revisions, and structural differences in legal contracts. 100% private in-browser tool.',
  keywords: [
    'compare pdf',
    'compare pdf files online free',
    'side by side pdf comparison',
    'pdf diff checker',
    'contract revision comparator',
    'pdf document diff',
    'cora compare pdf',
    'legal pdf compare'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/compare-pdf',
  },
  openGraph: {
    title: 'Compare PDF Online Free - Side-by-Side PDF Difference Checker | Cora',
    description: 'Compare two PDF files side-by-side online for free. Spot clause changes, page revisions, and structural differences in legal contracts. 100% private in-browser tool.',
    url: 'https://heycora.in/tools/compare-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Compare PDF Online Free - Side-by-Side PDF Difference Checker | Cora',
    description: 'Compare two PDF files side-by-side online for free. Spot clause changes, page revisions, and structural differences in legal contracts. 100% private in-browser tool.',
  },
};

export default function ComparePdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
