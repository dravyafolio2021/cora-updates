import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Redact PDF Online Free - Black Out Sensitive Text & Data | Cora',
  description: 'Permanently redact sensitive text, Aadhaar numbers, PAN cards, and financial records in PDF files 100% in your browser. Irreversible client-side sanitization.',
  keywords: [
    'redact pdf',
    'black out pdf online',
    'mask aadhaar in pdf',
    'redact pan card pdf',
    'sanitize pdf free',
    'hide confidential info in pdf',
    'pdf redaction tool',
    'cora pdf redact',
    'kyc mask pdf'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/redact-pdf',
  },
  openGraph: {
    title: 'Redact PDF Online Free - Black Out Sensitive Text & Data | Cora',
    description: 'Permanently redact sensitive text, Aadhaar numbers, PAN cards, and financial records in PDF files 100% in your browser. Irreversible client-side sanitization.',
    url: 'https://heycora.in/tools/redact-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Redact PDF Online Free - Black Out Sensitive Text & Data | Cora',
    description: 'Permanently redact sensitive text, Aadhaar numbers, PAN cards, and financial records in PDF files 100% in your browser. Irreversible client-side sanitization.',
  },
};

export default function RedactPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
