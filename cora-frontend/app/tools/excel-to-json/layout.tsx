import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Excel & CSV to JSON Converter Online Free | Cora',
  description: 'Convert Excel spreadsheets, CSV data, and table rows into clean JSON objects, 2D arrays, or keyed dictionaries online free. 100% private in-browser memory tool.',
  keywords: [
    'excel to json converter online free',
    'csv to json converter',
    'convert spreadsheet to json',
    'csv to json array of objects',
    'tsv to json',
    'table to json schema',
    'cora developer tools',
    'in-browser excel to json'
  ],
  openGraph: {
    title: 'Excel & CSV to JSON Converter Online Free | Cora',
    description: 'Transform Excel and CSV spreadsheets into formatted, syntax-highlighted JSON online free with zero server uploads.',
    url: 'https://heycora.in/tools/excel-to-json',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Excel to JSON Converter Online Free',
    description: 'Convert spreadsheet data into clean, formatted JSON arrays or keyed objects in browser memory.',
  },
};

export default function ExcelToJsonLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
