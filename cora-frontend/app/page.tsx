import { Hero } from '@/components/hero/Hero';
import { BrandTicker } from '@/components/social/BrandTicker';
import { FounderValueShowcase } from '@/components/founder-showcase/FounderValueShowcase';
import { ValueTransformationSection } from '@/components/value-transformation/ValueTransformationSection';
import { PlatformLifecycleSection } from '@/components/lifecycle/PlatformLifecycleSection';
import { IntegrationsWaveSection } from '@/components/integrations/IntegrationsWaveSection';
import { IndustryGridSection } from '@/components/industry-grid/IndustryGridSection';
import { ExecutiveConfidenceSection } from '@/components/features-grid/ExecutiveConfidenceSection';
import { CommunityStoriesSection } from '@/components/community/CommunityStoriesSection';
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

      {/* 3. Native Dual-Interface Architecture (Customer Website + Admin Workspace) */}
      <FounderValueShowcase />

      {/* 4. Value Transformation (From WhatsApp Threads to Automated Revenue) */}
      <ValueTransformationSection />

      {/* 5. AI Co-Founder for Every Business (Interactive Industry Showcase) */}
      <PlatformLifecycleSection />

      {/* 6. Integrations — Dual Flowing Wave with Central Cora Emblem */}
      <IntegrationsWaveSection />

      {/* 7. 12-Industry Directory Grid (Every Industry, One Platform) */}
      <IndustryGridSection />

      {/* 8. Plus, Everything You Need to Lead with Confidence (6-Feature Executive Grid) */}
      <ExecutiveConfidenceSection />

      {/* 9. Learn More About Service Business Engineering (Clay-Style Bento Community Hub) */}
      <CommunityStoriesSection />

      {/* 10. PWA Mobile App — Run Your Studio On The Go */}
      <MobilePwaSection />

      {/* 11. Commercial Pricing Matrix & Live FX Toggle */}
      <PricingSection />

      {/* 12. Free Public Micro-Tools Directory Banner */}
      <ToolsBanner />

      {/* 13. Customer Testimonials & Social Proof */}
      <TestimonialsSection />

      {/* 14. SEO & Schema-Linked FAQ Accordion */}
      <FAQSection />
    </main>
  );
}
