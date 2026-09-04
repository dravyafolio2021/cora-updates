import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'CSV to Excel Converter Online Free (.xlsx) | Cora',
  description: 'Convert CSV, TSV, and delimited text files into native Microsoft Excel spreadsheets (.xlsx) online free. 100% private in-browser memory tool with zero server uploads.',
  keywords: [
    'csv to excel converter online free',
    'convert csv to xlsx',
    'tsv to excel',
    'spreadsheet converter in browser',
    'free csv to excel',
    'cora spreadsheet tools',
    'convert delimited text to excel'
  ],
  openGraph: {
    title: 'CSV to Excel Converter Online Free (.xlsx) | Cora',
    description: 'Convert CSV and TSV files into native Excel spreadsheets (.xlsx) online free with zero server uploads.',
    url: 'https://heycora.in/tools/csv-to-excel',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'CSV to Excel Converter Online Free (.xlsx)',
    description: 'Convert delimited text and CSV data into Microsoft Excel workbooks online free.',
  },
};

export default function CsvToExcelLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
