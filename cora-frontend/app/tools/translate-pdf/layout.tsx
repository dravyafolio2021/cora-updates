import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Translate PDF Online Free - Multilingual Document Translator | Cora',
  description: 'Translate PDF documents into Hindi, Tamil, Telugu, Spanish, French, and 20+ languages online for free. Side-by-side bilingual comparison with 100% in-browser privacy.',
  keywords: [
    'translate pdf',
    'translate pdf online free',
    'translate pdf to hindi',
    'multilingual pdf translator',
    'pdf document translation',
    'translate contract pdf',
    'vernacular pdf translator',
    'cora translate pdf'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/translate-pdf',
  },
  openGraph: {
    title: 'Translate PDF Online Free - Multilingual Document Translator | Cora',
    description: 'Translate PDF documents into Hindi, Tamil, Telugu, Spanish, French, and 20+ languages online for free. Side-by-side bilingual comparison with 100% in-browser privacy.',
    url: 'https://heycora.in/tools/translate-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Translate PDF Online Free - Multilingual Document Translator | Cora',
    description: 'Translate PDF documents into Hindi, Tamil, Telugu, Spanish, French, and 20+ languages online for free. Side-by-side bilingual comparison with 100% in-browser privacy.',
  },
};

export default function TranslatePdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
