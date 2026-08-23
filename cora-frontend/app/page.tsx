import { Hero } from '@/components/hero/Hero';
import { BrandTicker } from '@/components/social/BrandTicker';
import { FounderValueShowcase } from '@/components/founder-showcase/FounderValueShowcase';
import { ValueTransformationSection } from '@/components/value-transformation/ValueTransformationSection';
import { PlatformLifecycleSection } from '@/components/lifecycle/PlatformLifecycleSection';
import { ProductFreedomRoiSection } from '@/components/product/ProductFreedomRoiSection';
import { UseCasesSection } from '@/components/use-cases/UseCasesSection';
import { IntegrationsRadialSection } from '@/components/integrations/IntegrationsRadialSection';
import { FounderCommunitySection } from '@/components/founder/FounderCommunitySection';
import { MobilePwaSection } from '@/components/mobile-app/MobilePwaSection';
import { PricingSection } from '@/components/pricing/PricingSection';
import { ToolsBanner } from '@/components/tools/ToolsBanner';
import { TestimonialsSection } from '@/components/testimonials/TestimonialsSection';
import { FAQSection } from '@/components/faq/FAQSection';

export default function HomePage() {
  return (
    <main className="w-full relative overflow-x-hidden">
      {/* 1. Hero Section (Sky & Meadow with Interactive SDR prompt) */}
      <Hero />

      {/* 2. Infinite Logo Marquee with Centered Line Badge */}
      <BrandTicker />

      {/* 3. Native Dual-Interface Architecture (Customer Website + Admin Workspace) */}
      <FounderValueShowcase />

      {/* 4. Value Transformation (From WhatsApp Threads to Automated Revenue) */}
      <ValueTransformationSection />

      {/* 5. AI Co-Founder for Every Business (Interactive Industry Showcase) */}
      <PlatformLifecycleSection />

      {/* 5. How It Works, Agency ROI & Enterprise Security Architecture */}
      <ProductFreedomRoiSection />

      {/* 6. Use Cases — Who This Platform Is Built For */}
      <UseCasesSection />

      {/* 7. Integrations — Radial Orbit Tool Ecosystem */}
      <IntegrationsRadialSection />

      {/* 8. Join the Movement — Founder Dravya Bansal & Community Spotlight */}
      <FounderCommunitySection />

      {/* 9. PWA Mobile App — Run Your Studio On The Go */}
      <MobilePwaSection />

      {/* 10. Commercial Pricing Matrix & Live FX Toggle */}
      <PricingSection />

      {/* 11. Free Public Micro-Tools Directory Banner */}
      <ToolsBanner />

      {/* 12. Customer Testimonials & Social Proof */}
      <TestimonialsSection />

      {/* 13. SEO & Schema-Linked FAQ Accordion */}
      <FAQSection />
    </main>
  );
}
