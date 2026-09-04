import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Excel & CSV to PDF Table Converter Online Free | Cora',
  description: 'Convert Excel spreadsheets, CSV data, and pasted table rows into clean publication-ready A4 PDF tables online free. 100% private in-browser tool with zero server uploads.',
  keywords: [
    'excel to pdf table converter',
    'csv to pdf',
    'spreadsheet to pdf free',
    'paste excel to pdf',
    'table to a4 pdf',
    'in-browser excel to pdf',
    'cora pdf tools'
  ],
  openGraph: {
    title: 'Excel & CSV to PDF Table Converter Online Free | Cora',
    description: 'Convert Excel spreadsheets and CSV data into clean formatted A4 PDF tables online free.',
    url: 'https://heycora.in/tools/excel-to-pdf',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Excel & CSV to PDF Table Converter Online Free',
    description: 'Convert Excel spreadsheets and CSV tables to PDF online free with zero server uploads.',
  },
};

export default function ExcelToPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
