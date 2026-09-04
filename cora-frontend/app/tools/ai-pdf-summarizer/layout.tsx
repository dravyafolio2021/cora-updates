import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'AI PDF Summarizer & Legal Contract Scanner Free | Cora',
  description: 'Instant client-side AI PDF summarizer and legal contract scanner. Analyze payment milestones, liability exposure, cancellation penalties, SLA turnaround, and confidentiality without uploading files.',
  keywords: [
    'ai pdf summarizer & legal contract scanner free',
    'ai pdf summarizer',
    'contract scanner',
    'ai contract analysis',
    'legal pdf scanner',
    'free contract review',
    'cora legal ai'
  ],
  openGraph: {
    title: 'AI PDF Summarizer & Legal Contract Scanner Free | Cora',
    description: 'Instant client-side AI PDF summarizer and legal contract scanner. Analyze payment terms, liabilities, and SLAs in your browser.',
    url: 'https://heycora.in/tools/ai-pdf-summarizer',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'AI PDF Summarizer & Legal Contract Scanner Free',
    description: '100% private in-browser AI contract analysis and PDF executive summarizer.',
  },
};

export default function AiPdfSummarizerLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
