import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Add Page Numbers to PDF Online Free - Number PDF Pages | Cora',
  description: 'Add page numbers to PDF files online for free. Custom pagination, Bates numbering, page offsets, and position controls. 100% private in-browser tool.',
  keywords: [
    'add page numbers to pdf',
    'number pdf pages',
    'pdf page numbering',
    'paginate pdf online free',
    'bates numbering pdf',
    'insert page numbers in pdf',
    'cora pdf tools',
    'page numbers pdf free'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/number-pdf',
  },
  openGraph: {
    title: 'Add Page Numbers to PDF Online Free - Number PDF Pages | Cora',
    description: 'Add page numbers to PDF files online for free. Custom pagination, Bates numbering, page offsets, and position controls. 100% private in-browser tool.',
    url: 'https://heycora.in/tools/number-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Add Page Numbers to PDF Online Free - Number PDF Pages | Cora',
    description: 'Add page numbers to PDF files online for free. Custom pagination, Bates numbering, page offsets, and position controls. 100% private in-browser tool.',
  },
};

export default function NumberPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
