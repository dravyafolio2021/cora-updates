import { Hero } from '@/components/hero/Hero';
import { BrandTicker } from '@/components/social/BrandTicker';
import { WallOfFeaturesSection } from '@/components/features/WallOfFeaturesSection';
import { HorizontalModuleDeck } from '@/components/modules/HorizontalModuleDeck';
import { PlatformLifecycleSection } from '@/components/lifecycle/PlatformLifecycleSection';
import { IntegrationsWaveSection } from '@/components/integrations/IntegrationsWaveSection';
import { IndustryGridSection } from '@/components/industry-grid/IndustryGridSection';
import { ExecutiveConfidenceSection } from '@/components/features-grid/ExecutiveConfidenceSection';
import { MobilePwaSection } from '@/components/mobile-app/MobilePwaSection';
import { PricingSection } from '@/components/pricing/PricingSection';
import { ToolsBanner } from '@/components/tools/ToolsBanner';
import { TestimonialsSection } from '@/components/testimonials/TestimonialsSection';
import { FAQSection } from '@/components/faq/FAQSection';

export default function HomePage() {
  return (
    <main className="w-full relative overflow-x-hidden">
      {/* 1. Hero Section (Conversational input & Hero) */}
      <Hero />

      {/* 2. Infinite Logo Marquee with Centered Line Badge */}
      <BrandTicker />

      {/* 3. Wall of Features — All Workflows, AI Co-Founders & Humans in Cora */}
      <WallOfFeaturesSection />

      {/* 4. Pinned Horizontal Parallax Module Deck (One Chat Bar. Every Business Task.) */}
      <HorizontalModuleDeck />

      {/* 4. AI Co-Founder for Every Business (Interactive Industry Showcase) */}
      <PlatformLifecycleSection />

      {/* 5. Integrations — Dual Flowing Wave with Central Cora Emblem */}
      <IntegrationsWaveSection />

      {/* 6. 12-Industry Directory Grid (Every Industry, One Platform) */}
      <IndustryGridSection />

      {/* 7. Plus, Everything You Need to Lead with Confidence (6-Feature Executive Grid) */}
      <ExecutiveConfidenceSection />

      {/* 8. PWA Mobile App — Run Your Studio On The Go */}
      <MobilePwaSection />

      {/* 9. Commercial Pricing Matrix & Live FX Toggle */}
      <PricingSection />

      {/* 10. Free Public Micro-Tools Directory Banner */}
      <ToolsBanner />

      {/* 11. Customer Testimonials & Social Proof */}
      <TestimonialsSection />

      {/* 12. SEO & Schema-Linked FAQ Accordion */}
      <FAQSection />
    </main>
  );
}
