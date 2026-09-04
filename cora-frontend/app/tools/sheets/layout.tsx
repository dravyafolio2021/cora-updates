import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Free Spreadsheet & Sheets Tools Online - 12+ Private In-Browser Utilities | Cora',
  description: 'The complete suite of free in-browser spreadsheet and CSV tools. Generate formulas with AI, build VLOOKUPs, convert between Excel/CSV/JSON/PDF, deduplicate, clean data, merge and split files with 100% client-side privacy.',
  keywords: [
    'free spreadsheet tools',
    'excel tools online',
    'excel formula generator',
    'vlookup builder',
    'csv to excel converter',
    'excel to csv',
    'remove duplicates csv',
    'clean sheet data',
    'merge csv',
    'split csv',
    'cora sheets suite'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/sheets',
  },
  openGraph: {
    title: 'Free Spreadsheet & Sheets Tools Online - 12+ Private In-Browser Utilities | Cora',
    description: 'The complete suite of free in-browser spreadsheet and CSV tools. Generate formulas with AI, convert files, deduplicate, clean, merge, and split with 100% client-side privacy.',
    url: 'https://heycora.in/tools/sheets',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Free Spreadsheet & Sheets Tools Online - 12+ Private In-Browser Utilities | Cora',
    description: 'The complete suite of free in-browser spreadsheet and CSV tools with 100% client-side privacy.',
  },
};

export default function SheetsMasterCategoryLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
