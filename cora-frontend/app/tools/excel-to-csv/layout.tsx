import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Excel & Spreadsheet to CSV Converter Online Free | Cora',
  description: 'Convert Excel spreadsheet rows, TSV data, and delimited tables into clean RFC 4180 compliant CSV files with custom delimiters online free. 100% private in-browser memory tool.',
  keywords: [
    'excel to csv converter online free',
    'convert spreadsheet to csv',
    'tsv to csv',
    'custom delimiter csv converter',
    'pipe separated to csv',
    'semicolon csv converter',
    'cora spreadsheet tools',
    'in-browser excel to csv'
  ],
  openGraph: {
    title: 'Excel & Spreadsheet to CSV Converter Online Free | Cora',
    description: 'Convert Excel tables and delimited spreadsheets into clean RFC 4180 CSV files online free with custom delimiters.',
    url: 'https://heycora.in/tools/excel-to-csv',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Excel to CSV Delimiter Converter Online Free',
    description: 'Convert spreadsheet data into clean RFC 4180 CSV files with custom delimiter selection.',
  },
};

export default function ExcelToCsvLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
