import type { Metadata } from 'next';
import { DesignSystemExplorer } from '@/components/design-system/DesignSystemExplorer';
import { designSystem } from '@/lib/design-system';

export const metadata: Metadata = {
  title: 'Cora Design System — Visual Component Library & Agent Contract',
  description: designSystem.description,
  alternates: { canonical: '/design-system/', types: { 'application/json': '/design-system/manifest.json', 'text/plain': '/design-system/llms.txt', 'text/css': '/design-system/tokens.css' } },
  openGraph: { title: 'Cora Design System', description: designSystem.description, url: '/design-system/', type: 'website' },
};

export default function DesignSystemPage() {
  return <DesignSystemExplorer />;
}
