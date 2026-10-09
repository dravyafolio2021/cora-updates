'use client';

import React from 'react';
import { Sparkles, Check, Code2, Copy, LayoutTemplate } from 'lucide-react';
import { GlobalResourcesHero, ResourcePillar } from '@/components/resources/GlobalResourcesHero';

interface TemplateHeroProps {
  searchQuery: string;
  onSearchChange: (query: string) => void;
  totalTemplatesCount: number;
}

const PILLARS: ResourcePillar[] = [
  {
    icon: <Check className="w-3.5 h-3.5 text-emerald-600 font-bold" />,
    text: '100% Free Forever',
  },
  {
    icon: <Copy className="w-3.5 h-3.5 text-purple-600" />,
    text: '1-Click Copy AI Prompts',
  },
  {
    icon: <LayoutTemplate className="w-3.5 h-3.5 text-blue-600" />,
    text: 'Elementor .json Kits',
  },
  {
    icon: <Code2 className="w-3.5 h-3.5 text-indigo-600" />,
    text: 'Lovable & v0 Projects',
  },
];

export function TemplateHero({
  searchQuery,
  onSearchChange,
}: TemplateHeroProps) {
  return (
    <GlobalResourcesHero
      tone="studio"
      badgeIcon={<Sparkles className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-purple-600" />}
      badgeText="Frontend Assets & UI Templates"
      titleLine1="Frontend Assets & UI Kits"
      titleLine2="Landing pages, AI section prompts, Elementor & Lovable"
      description="Copy-paste ready Tailwind landing pages, battle-tested AI section prompts for Claude & Cursor, importable Elementor Pro kits, and 1-click forkable Lovable web apps."
      pillars={PILLARS}
      searchProps={{
        value: searchQuery,
        onChange: onSearchChange,
        placeholder: 'Search assets e.g. Hero prompts, Pricing table, Elementor kit, Lovable CRM, Agency page...',
      }}
    />
  );
}

