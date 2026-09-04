import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Remove Duplicates from CSV Online - Free & Private In-Browser | Cora',
  description: 'Deduplicate CSV rows instantly by selected key columns or entire rows. 100% client-side execution in your browser RAM with zero server uploads and zero data leaks.',
  keywords: [
    'remove duplicates csv',
    'csv deduplicator',
    'find duplicate rows in csv',
    'dedupe spreadsheet',
    'excel remove duplicates online',
    'client-side csv cleaner',
    'cora sheets tools'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/remove-duplicates-csv',
  },
  openGraph: {
    title: 'Remove Duplicates from CSV Online - Free & Private In-Browser | Cora',
    description: 'Deduplicate CSV rows instantly by selected key columns or entire rows. 100% client-side execution in your browser RAM with zero server uploads.',
    url: 'https://heycora.in/tools/remove-duplicates-csv',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Remove Duplicates from CSV Online - Free & Private In-Browser | Cora',
    description: 'Deduplicate CSV rows instantly by selected key columns or entire rows. 100% client-side execution in your browser RAM.',
  },
};

export default function RemoveDuplicatesCsvLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
