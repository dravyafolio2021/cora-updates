'use client';

import React, { useState } from 'react';
import { DemoHero } from '@/components/demo/DemoHero';
import { DemoBrandTicker } from '@/components/demo/DemoBrandTicker';
import { DemoWorkflows } from '@/components/demo/DemoWorkflows';
import { DemoPersonalizedCallout } from '@/components/demo/DemoPersonalizedCallout';
import { DemoBookingDrawer } from '@/components/demo/DemoBookingDrawer';

export default function DemoPage() {
  const [isDrawerOpen, setIsDrawerOpen] = useState(false);

  return (
    <main className="min-h-screen bg-white text-zinc-950 flex flex-col selection:bg-zinc-950 selection:text-white">
      
      {/* Hero Section with Interactive Workspace Simulator */}
      <DemoHero onOpenDrawer={() => setIsDrawerOpen(true)} />

      {/* Monochromatic Logo Bar */}
      <DemoBrandTicker />

      {/* Workflows: Experience a Day in the Life */}
      <DemoWorkflows onOpenDrawer={() => setIsDrawerOpen(true)} />

      {/* 1:1 Demo Personalized Callout */}
      <DemoPersonalizedCallout onOpenDrawer={() => setIsDrawerOpen(true)} />

      {/* Right-Sliding Interactive Demo Booking Drawer */}
      <DemoBookingDrawer
        isOpen={isDrawerOpen}
        onClose={() => setIsDrawerOpen(false)}
      />

    </main>
  );
}
