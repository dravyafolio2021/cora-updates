import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Organize PDF Online Free - Rearrange, Reorder & Duplicate Pages | Cora',
  description: 'Visually rearrange, reorder, duplicate, and rotate PDF pages online for free. Drag-and-drop page tile organizer. 100% private in-browser tool.',
  keywords: [
    'organize pdf',
    'rearrange pdf pages',
    'reorder pdf pages online free',
    'duplicate pdf pages',
    'sort pdf pages',
    'rotate pdf pages',
    'pdf page organizer',
    'cora organize pdf',
    'drag and drop pdf pages'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/organize-pdf',
  },
  openGraph: {
    title: 'Organize PDF Online Free - Rearrange, Reorder & Duplicate Pages | Cora',
    description: 'Visually rearrange, reorder, duplicate, and rotate PDF pages online for free. Drag-and-drop page tile organizer. 100% private in-browser tool.',
    url: 'https://heycora.in/tools/organize-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Organize PDF Online Free - Rearrange, Reorder & Duplicate Pages | Cora',
    description: 'Visually rearrange, reorder, duplicate, and rotate PDF pages online for free. Drag-and-drop page tile organizer. 100% private in-browser tool.',
  },
};

export default function OrganizePdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
