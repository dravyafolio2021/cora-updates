import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Clean & Standardize Spreadsheet Data Online Free | Cora',
  description: 'Clean, sanitize, and format CSV, TSV, and spreadsheet data online free. Trim whitespaces, format Indian phone numbers (+91), standardize ISO dates, title-case names, and remove empty rows in browser memory.',
  keywords: [
    'clean spreadsheet data',
    'clean csv online',
    'standardize phone numbers excel',
    'format dates to iso csv',
    'title case names excel',
    'trim whitespace spreadsheet',
    'free sheet cleaner',
    'cora spreadsheet tools',
    'in-browser data sanitization'
  ],
  openGraph: {
    title: 'Clean & Standardize Spreadsheet Data Online Free | Cora',
    description: 'Clean messy spreadsheets in seconds. Standardize Indian phone numbers, convert dates to ISO, trim irregular spaces, and title-case contact names in browser RAM.',
    url: 'https://heycora.in/tools/clean-sheet-data',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Clean & Standardize Spreadsheet Data Online Free',
    description: 'Clean messy spreadsheets in seconds. Standardize Indian phone numbers, convert dates to ISO, and trim irregular spaces in browser memory.',
  },
};

export default function CleanSheetDataLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
