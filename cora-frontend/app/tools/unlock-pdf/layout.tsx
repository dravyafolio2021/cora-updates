import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Unlock PDF Online Free - Remove Password & Restrictions | Cora',
  description: 'Unlock PDF files and remove passwords, printing restrictions, and copy locks 100% in your browser. Fast, secure, and private client-side decryption.',
  keywords: [
    'unlock pdf',
    'remove password from pdf',
    'pdf password remover',
    'decrypt pdf online free',
    'strip pdf restrictions',
    'remove pdf copy protection',
    'unlock print pdf',
    'cora pdf unlock',
    'unlock bank statement pdf'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/unlock-pdf',
  },
  openGraph: {
    title: 'Unlock PDF Online Free - Remove Password & Restrictions | Cora',
    description: 'Unlock PDF files and remove passwords, printing restrictions, and copy locks 100% in your browser. Fast, secure, and private client-side decryption.',
    url: 'https://heycora.in/tools/unlock-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Unlock PDF Online Free - Remove Password & Restrictions | Cora',
    description: 'Unlock PDF files and remove passwords, printing restrictions, and copy locks 100% in your browser. Fast, secure, and private client-side decryption.',
  },
};

export default function UnlockPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
