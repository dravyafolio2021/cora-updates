import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Merge CSV Files Online - Free Multi-File CSV Combiner | Cora',
  description: 'Merge multiple CSV files into one consolidated master spreadsheet. Aligns mismatched column headers automatically with 100% private in-browser memory execution.',
  keywords: [
    'merge csv',
    'combine csv files online',
    'join multiple csv files',
    'consolidate spreadsheets',
    'csv merger online',
    'client-side csv combiner',
    'cora sheets tools'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/merge-csv',
  },
  openGraph: {
    title: 'Merge CSV Files Online - Free Multi-File CSV Combiner | Cora',
    description: 'Merge multiple CSV files into one consolidated master spreadsheet. Aligns mismatched column headers automatically with 100% private in-browser memory execution.',
    url: 'https://heycora.in/tools/merge-csv',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Merge CSV Files Online - Free Multi-File CSV Combiner | Cora',
    description: 'Merge multiple CSV files into one consolidated master spreadsheet with automatic header alignment.',
  },
};

export default function MergeCsvLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
