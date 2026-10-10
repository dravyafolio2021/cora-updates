import React from 'react';
import type { Metadata } from 'next';
import { IndustriesPageClient } from '@/components/industry/IndustriesPageClient';

export const metadata: Metadata = {
  title: 'Industry Solutions & Pre-Seeded Workspaces — Cora OS',
  description: 'Explore 16 turnkey agency and professional services workspaces. Pre-configured with Indian IT Act 2000 digital contracts, automated 18% GST invoicing, and milestone client review portals.',
  keywords: [
    'agency management software India',
    'creative studio operating system',
    'law firm client portal',
    'chartered accountant CRM',
    'architectural milestone billing',
    'IT agency retainer software',
    '18% GST invoicing for agencies'
  ],
  alternates: {
    canonical: 'https://heycora.in/industries/',
  },
  openGraph: {
    title: 'Industry Solutions & Workspaces — Cora OS',
    description: 'Explore 16 turnkey agency and professional services workspaces with pre-seeded legal contracts, GST tax splitting, and milestone approval portals.',
    url: 'https://heycora.in/industries',
    type: 'website',
    siteName: 'Cora OS for Professional Services',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Industry Solutions & Workspaces — Cora OS',
    description: 'Explore 16 turnkey agency and professional services workspaces with pre-seeded legal contracts, GST tax splitting, and milestone approval portals.',
  }
};

export default function IndustriesPage() {
  return <IndustriesPageClient />;
}
