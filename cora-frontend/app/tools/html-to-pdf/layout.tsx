import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'HTML to PDF Converter Online Free | Cora',
  description: 'Convert styled HTML code, invoice templates, and web snippets into clean formatted A4 PDF documents online free. 100% private in-browser memory execution.',
  keywords: [
    'html to pdf converter online free',
    'html to pdf',
    'convert html code to pdf',
    'invoice html to pdf free',
    'styled html to a4 pdf',
    'in-browser html to pdf',
    'cora pdf tools'
  ],
  openGraph: {
    title: 'HTML to PDF Converter Online Free | Cora',
    description: 'Convert styled HTML and custom web templates into clean formatted A4 PDF documents online free.',
    url: 'https://heycora.in/tools/html-to-pdf',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'HTML to PDF Converter Online Free',
    description: 'Convert styled HTML code to formatted PDF documents online free with zero server uploads.',
  },
};

export default function HtmlToPdfLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
