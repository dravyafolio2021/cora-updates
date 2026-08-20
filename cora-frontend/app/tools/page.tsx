import Link from 'next/link';
import { Calculator, Sparkles, ArrowRight, FileCheck, CheckCircle2 } from 'lucide-react';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Free Founder & Business Micro-Tools — Cora',
  description: 'Instant, free AI-powered tools for founders, digital agencies, and solo builders. GST calculators, listing generators, and workflow accelerators.',
};

const TOOLS = [
  {
    slug: 'gst-calculator',
    title: 'Indian GST & B2B Tax Calculator',
    description: 'Instant 18%, 12%, 5%, 28% tax breakdown with CGST/SGST vs IGST segregation, reverse charge, and compliant invoices.',
    icon: Calculator,
    badge: 'Popular in India',
    badgeColor: 'bg-orange-100 text-orange-800',
    runs: 'Free Forever',
  },
  {
    slug: 'listing-ai',
    title: 'Real Estate & Studio Listing AI Generator',
    description: 'Turn raw property or photo shoot details into high-converting marketing copy and client briefs in seconds.',
    icon: Sparkles,
    badge: 'AI Powered',
    badgeColor: 'bg-purple-100 text-purple-900',
    runs: 'Free Forever',
  },
];

export default function ToolsIndexPage() {
  return (
    <div className="py-16 md:py-24">
      <div className="w-full max-w-[1140px] mx-auto px-6">
        
        {/* Header */}
        <div className="text-center max-w-[760px] mx-auto mb-12">
          <div className="inline-flex items-center gap-1.5 font-sans text-[0.8125rem] font-medium text-zinc-600 px-3.5 py-1 bg-white border border-zinc-200 rounded-full mb-4 shadow-sm">
            <span>Free Micro-Tools</span>
          </div>
          <h1 className="font-display text-[clamp(2.2rem,4.5vw,3.4rem)] font-[550] tracking-[-0.035em] text-zinc-950 leading-[1.15] mb-4">
            Tools built for speed and precision.
          </h1>
          <p className="font-sans text-[1.05rem] text-zinc-600 leading-relaxed font-normal">
            Free top-of-funnel utilities to accelerate your client proposals, tax calculations, and AI copywriting. No login or credit card required.
          </p>
        </div>

        {/* Tools Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16 max-w-[920px] mx-auto">
          {TOOLS.map((tool) => {
            const Icon = tool.icon;
            return (
              <Link
                key={tool.slug}
                href={`/tools/${tool.slug}`}
                className="bg-white border border-zinc-200 rounded-2xl p-6 flex flex-col justify-between shadow-sm hover:border-zinc-300 hover:shadow-md hover:-translate-y-0.5 transition-all group"
              >
                <div>
                  <div className="flex items-center justify-between gap-2 mb-4">
                    <div className="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center border border-zinc-200 group-hover:scale-105 transition-transform">
                      <Icon className="w-5 h-5 text-zinc-900" />
                    </div>
                    <span className={`text-[0.6875rem] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full ${tool.badgeColor}`}>
                      {tool.badge}
                    </span>
                  </div>

                  <h2 className="font-display text-xl font-bold tracking-tight text-zinc-950 mb-2 group-hover:text-zinc-800">
                    {tool.title}
                  </h2>
                  <p className="text-sm text-zinc-600 leading-relaxed mb-6 font-normal">
                    {tool.description}
                  </p>
                </div>

                <div className="flex items-center justify-between pt-4 border-t border-zinc-100 text-xs font-semibold text-zinc-950">
                  <span className="text-zinc-400">{tool.runs}</span>
                  <span className="inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                    Use Tool Free <ArrowRight className="w-3.5 h-3.5" />
                  </span>
                </div>
              </Link>
            );
          })}
        </div>

        {/* Bottom CTA Banner */}
        <div className="bg-zinc-950 text-white rounded-3xl p-8 md:p-10 text-center max-w-[920px] mx-auto">
          <h3 className="font-display text-2xl font-bold mb-2">Need a complete AI operating system?</h3>
          <p className="text-sm text-zinc-400 max-w-[600px] mx-auto mb-6">
            Cora orchestrates all your tools, lead pipelines, GST accounting, and multi-model AI workflows in one unified dashboard.
          </p>
          <a
            href="https://app.heycora.in/workspace"
            className="inline-flex items-center gap-2 bg-white text-zinc-950 font-semibold px-6 py-3 rounded-xl text-sm hover:bg-zinc-100 transition-all"
          >
            <span>Get Started Free on Cora</span>
            <ArrowRight className="w-4 h-4" />
          </a>
        </div>

      </div>
    </div>
  );
}
