import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'PDF to Excel & CSV Converter Online Free (.xlsx / .csv) | Cora',
  description: 'Convert PDF tables, invoices, and bank statements into structured Excel spreadsheets (.xlsx) and CSV files online free. 100% private in-browser memory execution.',
  keywords: [
    'pdf to excel converter online free',
    'pdf to csv',
    'extract table from pdf to excel',
    'pdf to xlsx free',
    'convert invoice pdf to excel',
    'in-browser pdf to spreadsheet',
    'cora pdf tools'
  ],
  openGraph: {
    title: 'PDF to Excel & CSV Converter Online Free (.xlsx / .csv) | Cora',
    description: 'Convert PDF tables and financial statements into clean Excel & CSV spreadsheets online free.',
    url: 'https://heycora.in/tools/pdf-to-excel',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'PDF to Excel & CSV Converter Online Free (.xlsx / .csv)',
    description: 'Convert PDF tables into Excel & CSV spreadsheets online free with zero server uploads.',
  },
};

export default function PdfToExcelLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
