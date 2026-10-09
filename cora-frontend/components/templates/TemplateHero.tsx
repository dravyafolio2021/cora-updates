'use client';

import React from 'react';
import { Sparkles } from 'lucide-react';
import { GlobalResourcesHero } from '@/components/resources/GlobalResourcesHero';

interface TemplateHeroProps {
  searchQuery: string;
  onSearchChange: (query: string) => void;
  totalTemplatesCount: number;
}

export function TemplateHero() {
  return (
    <GlobalResourcesHero
      tone="neutral"
      badgeIcon={<Sparkles className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-purple-600" />}
      badgeText="Frontend Assets & UI Templates"
      titleLine1="Frontend Assets & UI Kits"
      titleLine2="Landing pages, AI section prompts, Elementor & Lovable"
      description="Copy-paste ready Tailwind landing pages, battle-tested AI section prompts for Claude & Cursor, importable Elementor Pro kits, and 1-click forkable Lovable web apps."
      emailSource="templates_hero"
      emailPlaceholder="Enter your work email for new HTML templates..."
      emailButtonText="Get Instant Access"
    />
  );
}

