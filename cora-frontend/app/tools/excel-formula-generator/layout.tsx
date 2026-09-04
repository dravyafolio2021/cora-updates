import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'AI Excel & Google Sheets Formula Generator Online Free | Cora',
  description: 'Generate advanced Excel and Google Sheets formulas from plain English descriptions instantly. Convert natural language queries into XLOOKUP, SUMIFS, regex extraction, and conditional formulas with zero errors. 100% in-browser memory.',
  keywords: [
    'excel formula generator',
    'google sheets formula generator',
    'ai excel formula maker',
    'convert text to excel formula',
    'sumifs generator',
    'xlookup generator',
    'sheets formula ai',
    'free spreadsheet formula generator',
    'excel prompt to formula',
    'cora spreadsheet tools'
  ],
  openGraph: {
    title: 'AI Excel & Google Sheets Formula Generator Online Free | Cora',
    description: 'Turn plain English prompts into production-grade Excel and Google Sheets formulas. Instant syntax breakdown and 100% private in-browser generation.',
    url: 'https://heycora.in/tools/excel-formula-generator',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'AI Excel & Google Sheets Formula Generator Online Free',
    description: 'Turn plain English prompts into production-grade Excel and Google Sheets formulas instantly.',
  },
};

export default function ExcelFormulaGeneratorLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
