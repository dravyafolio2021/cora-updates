import type { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
  title: 'Pricing & Plans — Cora AI Co-Founder',
  description: 'Cora transparent 4-tier pricing: Free forever plan, Starter at ₹999/mo, Professional at ₹2,499/mo, and Scale at ₹4,499/mo with free website development or migration. 2 months free on annual plans.',
  alternates: {
    canonical: 'https://heycora.in/pricing',
  },
  openGraph: {
    title: 'Pricing & Plans — Cora AI Co-Founder',
    description: 'Cora transparent 4-tier pricing: Free forever plan, Starter at ₹999/mo, Professional at ₹2,499/mo, and Scale at ₹4,499/mo with free website development or migration. 2 months free on annual plans.',
    url: 'https://heycora.in/pricing',
    siteName: 'Cora',
  },
};

export default function PricingLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
