import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Remove Pages from PDF Online Free - Delete PDF Pages | Cora',
  description: 'Delete unwanted pages from any PDF document for free. 100% private in-browser tool with interactive page selection, range inputs, zero server uploads, and instant download.',
  keywords: [
    'remove pages from pdf',
    'delete pdf pages',
    'remove pdf pages online free',
    'delete pages from pdf',
    'delete pdf page free',
    'cora pdf tools'
  ],
  openGraph: {
    title: 'Remove Pages from PDF Online Free - Delete PDF Pages | Cora',
    description: 'Delete unwanted pages from any PDF document for free. 100% private in-browser tool with zero server uploads and visual page selection.',
    url: 'https://heycora.in/tools/remove-pages',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Remove Pages from PDF Online Free - Delete PDF Pages',
    description: 'Delete unwanted pages from any PDF document for free. 100% private in-browser tool.',
  },
};

export default function RemovePagesLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
