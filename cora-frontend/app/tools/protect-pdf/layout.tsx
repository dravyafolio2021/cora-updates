import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Protect PDF Online Free - Password Protect & Encrypt PDF | Cora',
  description: 'Password protect PDF files with military-grade AES-256 encryption. Restrict printing, copying, and edits 100% in your browser. Zero server uploads.',
  keywords: [
    'protect pdf',
    'password protect pdf',
    'encrypt pdf online',
    'aes 256 pdf encryption',
    'lock pdf with password',
    'restrict pdf printing',
    'disable pdf copying',
    'cora pdf security',
    'secure pdf free'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/protect-pdf',
  },
  openGraph: {
    title: 'Protect PDF Online Free - Password Protect & Encrypt PDF | Cora',
    description: 'Password protect PDF files with military-grade AES-256 encryption. Restrict printing, copying, and edits 100% in your browser. Zero server uploads.',
    url: 'https://heycora.in/tools/protect-pdf',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Protect PDF Online Free - Password Protect & Encrypt PDF | Cora',
    description: 'Password protect PDF files with military-grade AES-256 encryption. Restrict printing, copying, and edits 100% in your browser. Zero server uploads.',
  },
};

export default function ProtectPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
