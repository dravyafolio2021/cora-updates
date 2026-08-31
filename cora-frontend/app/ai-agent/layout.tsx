import type { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
  title: 'AI Co-Founder — Cora Autonomous Operations & RAG Memory',
  description: 'Meet your AI Co-Founder. Autonomous operations triage, voice-to-scope audio briefs, and live RAG memory across your entire professional service business.',
  alternates: {
    canonical: 'https://heycora.in/ai-agent',
  },
  openGraph: {
    title: 'AI Co-Founder — Cora Autonomous Operations & RAG Memory',
    description: 'Meet your AI Co-Founder. Autonomous operations triage, voice-to-scope audio briefs, and live RAG memory across your entire professional service business.',
    url: 'https://heycora.in/ai-agent',
    siteName: 'Cora',
  },
};

export default function AiAgentLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
