import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Split CSV File Online - Split by Row Count or Column Value | Cora',
  description: 'Split large CSV files into smaller batches by row count or partition by unique column values (e.g. City, Vendor, Status) with 100% private in-browser memory execution.',
  keywords: [
    'split csv',
    'split csv by rows',
    'split csv by column value',
    'csv partitioner',
    'split large csv online',
    'client-side csv splitter',
    'cora sheets tools'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/split-csv',
  },
  openGraph: {
    title: 'Split CSV File Online - Split by Row Count or Column Value | Cora',
    description: 'Split large CSV files into smaller batches by row count or partition by unique column values with 100% private in-browser memory execution.',
    url: 'https://heycora.in/tools/split-csv',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Split CSV File Online - Split by Row Count or Column Value | Cora',
    description: 'Split large CSV files into smaller batches by row count or partition by unique column values.',
  },
};

export default function SplitCsvLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
