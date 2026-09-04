import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'VLOOKUP & XLOOKUP Formula Generator Online Free | Cora',
  description: 'Interactive visual VLOOKUP and XLOOKUP formula generator. Configure lookup value, table array, return column index, exact match, and IFERROR fallback with one-click copy. 100% in-browser memory.',
  keywords: [
    'vlookup generator',
    'xlookup generator',
    'excel vlookup maker',
    'visual vlookup builder',
    'vlookup syntax generator',
    'iferror vlookup',
    'cora spreadsheet tools',
    'google sheets vlookup',
    'xlookup vs vlookup generator'
  ],
  openGraph: {
    title: 'VLOOKUP & XLOOKUP Formula Generator Online Free | Cora',
    description: 'Build error-free VLOOKUP and modern XLOOKUP formulas visually. Interactive visual builder with exact match, fallback defaults, syntax guide, and one-click copy.',
    url: 'https://heycora.in/tools/vlookup-generator',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'VLOOKUP & XLOOKUP Formula Generator Online Free',
    description: 'Build error-free VLOOKUP and modern XLOOKUP formulas visually with one-click copy.',
  },
};

export default function VlookupGeneratorLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
