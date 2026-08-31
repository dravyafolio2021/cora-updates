import type { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
  title: 'Features & 20 Modules — Cora AI Co-Founder',
  description: 'Explore 20 unified modules: SHA-256 digital contracts, automated 18% GST invoicing, Kanban CRM, crew dispatch, and 4K media vaults in one OS.',
  alternates: {
    canonical: 'https://heycora.in/features',
  },
  openGraph: {
    title: 'Features & 20 Modules — Cora AI Co-Founder',
    description: 'Explore 20 unified modules: SHA-256 digital contracts, automated 18% GST invoicing, Kanban CRM, crew dispatch, and 4K media vaults in one OS.',
    url: 'https://heycora.in/features',
    siteName: 'Cora',
  },
};

export default function FeaturesLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
