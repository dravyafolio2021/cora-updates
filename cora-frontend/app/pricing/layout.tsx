import type { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
  title: 'Pricing & Plans — Cora AI Co-Founder',
  description: 'Cora pricing: Free tier, Pro at ₹2,999/mo, and Enterprise plans with zero seat penalties. Simple, transparent pricing for professional service businesses.',
  alternates: {
    canonical: 'https://heycora.in/pricing',
  },
  openGraph: {
    title: 'Pricing & Plans — Cora AI Co-Founder',
    description: 'Cora pricing: Free tier, Pro at ₹2,999/mo, and Enterprise plans with zero seat penalties. Simple, transparent pricing for professional service businesses.',
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
