import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Cora Comparisons for Service Businesses',
  description: 'Transparent comparisons between Cora and common CRM, workflow, document, billing, and agency software stacks.',
  alternates: { canonical: '/compare/' },
};

export default function CompareLayout({ children }: { children: React.ReactNode }) {
  return children;
}
