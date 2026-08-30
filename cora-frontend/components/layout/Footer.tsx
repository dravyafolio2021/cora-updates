'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { usePathname } from 'next/navigation';
import { ArrowRight, Mail, Instagram, Linkedin, Twitter } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export function Footer() {
  const pathname = usePathname();
  const is404 = pathname === '/404' || pathname?.includes('_not-found');
  const isDocsPage = pathname?.startsWith('/docs');
  const isLegalPage = [
    '/terms',
    '/privacy',
    '/refund-policy',
    '/security',
    '/sla',
    '/contact'
  ].includes(pathname || '');

  return (
    <footer className="relative w-full overflow-hidden pt-12 sm:pt-16 pb-12">
      
      {/* ── Background Landscape Horizon (Hidden on 404, Docs & Legal Pages) ── */}
      {!is404 && !isLegalPage && !isDocsPage && (
        <div className="absolute inset-0 pointer-events-none select-none z-0">
          <Image
            src="/images/cora_hero_landscape.jpg"
            alt="Landscape Horizon"
            fill
            sizes="100vw"
            className="object-cover object-[center_55%]"
          />
          <div className="absolute inset-0 bg-gradient-to-b from-white via-white/80 to-transparent" />
          <div className="absolute inset-0 bg-gradient-to-t from-white/90 via-transparent to-transparent" />
        </div>
      )}

      <div className="relative z-10 w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Top Conversion CTA Banner (Hidden on 404, Docs & Legal Pages) ── */}
        {!is404 && !isLegalPage && !isDocsPage && (
          <div className="text-center max-w-[760px] mx-auto mb-16 sm:mb-20">
            <h2 className="font-display text-3xl xs:text-4xl sm:text-[48px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-4">
              Ready to simplify your business?
            </h2>
            <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[600px] mx-auto mb-8">
              Join Indian founders managing their daily operations, GST invoices, and WhatsApp leads in one place.
            </p>

            <div className="flex items-center justify-center flex-wrap gap-3.5">
              <a
                href="https://app.heycora.in/workspace/login?source=footer_cta"
                onClick={() => trackEvent('cta_click', { section: 'footer_cta_primary' })}
                className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
              >
                <span>Start free — no card needed</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>

              <a
                href="mailto:dravya.bansal@heycora.in?subject=Inquiry%20from%20Cora%20Website"
                onClick={() => trackEvent('cta_click', { section: 'footer_cta_chat_founder' })}
                className="inline-flex items-center gap-2 bg-white text-zinc-950 border border-zinc-300 hover:border-zinc-400 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-50 transition-all shadow-2xs"
              >
                <span>Chat with Founder</span>
              </a>
            </div>
          </div>
        )}

        {/* ── Master White Footer Card ── */}
        <div className="w-full rounded-[36px] bg-white border border-zinc-200/90 shadow-2xs p-8 sm:p-12 md:p-14">
          
          <div className="grid grid-cols-2 md:grid-cols-12 gap-8 md:gap-10 pb-12 border-b border-zinc-200/80">
            
            {/* Col 1: Brand Bio & Support Email (Span 4) */}
            <div className="col-span-2 md:col-span-4 space-y-5">
              <Link href="/" className="text-zinc-950 font-display font-bold text-2xl sm:text-3xl tracking-tight block">
                <span>Cora</span>
              </Link>

              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed max-w-[320px] font-normal">
                The AI co-founder for Indian service businesses, clinics, gyms, salons, and solo founders.
              </p>

              <div className="flex flex-col gap-2.5 pt-1">
                <a
                  href="mailto:support@heycora.in"
                  className="inline-flex items-center gap-2 bg-[#100F12] text-white px-3.5 py-2 rounded-xl text-xs font-semibold hover:bg-zinc-800 transition-all border border-zinc-800 shadow-2xs w-fit"
                >
                  <Mail className="w-3.5 h-3.5 text-zinc-400" />
                  <span>support@heycora.in</span>
                </a>

                <Link
                  href="/status"
                  className="inline-flex items-center gap-1.5 text-xs text-zinc-600 hover:text-zinc-950 transition-colors w-fit pt-1"
                >
                  <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                  <span className="font-semibold text-emerald-700">All systems operational (99.98%)</span>
                </Link>
              </div>
            </div>

            {/* Col 2: Product & Platform (Span 2) */}
            <div className="col-span-1 md:col-span-2 space-y-3">
              <div className="font-display text-xs font-bold text-zinc-950 uppercase tracking-wider">
                Platform
              </div>
              <ul className="space-y-2 text-xs sm:text-[13px] text-zinc-600 font-medium">
                <li><Link href="/features" className="hover:text-zinc-950 transition-colors">Features</Link></li>
                <li><Link href="/articles" className="hover:text-zinc-950 transition-colors font-semibold text-zinc-950">Articles &amp; Guides</Link></li>
                <li><Link href="/docs" className="hover:text-zinc-950 transition-colors">Documentation</Link></li>
                <li><Link href="/demo" className="hover:text-zinc-950 transition-colors">Get A Demo</Link></li>
                <li><Link href="/ai-agent" className="hover:text-zinc-950 transition-colors">AI Co-Founder</Link></li>
                <li><Link href="/use-cases" className="hover:text-zinc-950 transition-colors">Use Cases</Link></li>
                <li><Link href="/pricing" className="hover:text-zinc-950 transition-colors">Pricing</Link></li>
                <li><Link href="/changelog" className="hover:text-zinc-950 transition-colors">Changelog</Link></li>
              </ul>
            </div>

            {/* Col 3: Competitor Comparisons (Span 2) */}
            <div className="col-span-1 md:col-span-2 space-y-3">
              <div className="font-display text-xs font-bold text-zinc-950 uppercase tracking-wider">
                Compare
              </div>
              <ul className="space-y-2 text-xs sm:text-[13px] text-zinc-600 font-medium">
                <li><Link href="/compare/cora-vs-honeybook" className="hover:text-zinc-950 transition-colors">vs HoneyBook</Link></li>
                <li><Link href="/compare/cora-vs-studio-ninja" className="hover:text-zinc-950 transition-colors">vs Studio Ninja</Link></li>
                <li><Link href="/compare/cora-vs-hubspot" className="hover:text-zinc-950 transition-colors">vs HubSpot</Link></li>
                <li><Link href="/compare/cora-vs-docusign" className="hover:text-zinc-950 transition-colors">vs DocuSign</Link></li>
                <li><Link href="/compare" className="hover:text-zinc-950 transition-colors text-emerald-700 font-bold">All Comparisons &rarr;</Link></li>
              </ul>
            </div>

            {/* Col 4: Tools & Ecosystem (Span 2) */}
            <div className="col-span-1 md:col-span-2 space-y-3">
              <div className="font-display text-xs font-bold text-zinc-950 uppercase tracking-wider">
                Ecosystem
              </div>
              <ul className="space-y-2 text-xs sm:text-[13px] text-zinc-600 font-medium">
                <li><Link href="/integrations" className="hover:text-zinc-950 transition-colors font-semibold text-zinc-900">Integrations</Link></li>
                <li><Link href="/tools/embed-builder" className="hover:text-zinc-950 transition-colors">Embed Builder</Link></li>
                <li><Link href="/tools/gst-calculator" className="hover:text-zinc-950 transition-colors">18% GST Calculator</Link></li>
                <li><Link href="/brand" className="hover:text-zinc-950 transition-colors">Brand &amp; Assets</Link></li>
                <li><Link href="/about" className="hover:text-zinc-950 transition-colors">About &amp; Story</Link></li>
              </ul>
            </div>

            {/* Col 5: Trust & Policies (Span 2) */}
            <div className="col-span-1 md:col-span-2 space-y-3">
              <div className="font-display text-xs font-bold text-zinc-950 uppercase tracking-wider">
                Trust &amp; Legal
              </div>
              <ul className="space-y-2 text-xs sm:text-[13px] text-zinc-600 font-medium">
                <li><Link href="/terms" className="hover:text-zinc-950 transition-colors">Terms of Service</Link></li>
                <li><Link href="/privacy" className="hover:text-zinc-950 transition-colors">Privacy Policy</Link></li>
                <li><Link href="/refund-policy" className="hover:text-zinc-950 transition-colors">Refund Policy</Link></li>
                <li><Link href="/security" className="hover:text-zinc-950 transition-colors">Security &amp; Trust</Link></li>
                <li><Link href="/sla" className="hover:text-zinc-950 transition-colors">99.95% SLA</Link></li>
              </ul>
            </div>

          </div>

          {/* ── Sub-Footer ── */}
          <div className="pt-8 flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-zinc-500">
            <div className="text-center md:text-left">
              &copy; {new Date().getFullYear()} Cora. All rights reserved. UDYAM Registered MSME (Govt. of India) &bull; Indian IT Act 2000 compliant.
            </div>

            <div className="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-xs text-zinc-500 font-medium">
              <Link href="/terms" className="hover:text-zinc-950 transition-colors">Terms</Link>
              <span>&bull;</span>
              <Link href="/privacy" className="hover:text-zinc-950 transition-colors">Privacy</Link>
              <span>&bull;</span>
              <Link href="/refund-policy" className="hover:text-zinc-950 transition-colors">Refunds</Link>
              <span>&bull;</span>
              <Link href="/security" className="hover:text-zinc-950 transition-colors">Security</Link>
              <span>&bull;</span>
              <Link href="/sla" className="hover:text-zinc-950 transition-colors">SLA</Link>
              <span>&bull;</span>
              <Link href="/status" className="hover:text-zinc-950 transition-colors">Status</Link>
            </div>

            <div className="flex items-center gap-2">
              <a
                href="https://instagram.com/dravyafolio"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Instagram"
                className="w-8 h-8 rounded-xl bg-zinc-100 hover:bg-zinc-200/80 text-zinc-700 hover:text-zinc-950 flex items-center justify-center transition-colors border border-zinc-200/80 shadow-2xs"
              >
                <Instagram className="w-4 h-4" />
              </a>

              <a
                href="https://linkedin.com/in/dravyafolio"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="LinkedIn"
                className="w-8 h-8 rounded-xl bg-zinc-100 hover:bg-zinc-200/80 text-zinc-700 hover:text-zinc-950 flex items-center justify-center transition-colors border border-zinc-200/80 shadow-2xs"
              >
                <Linkedin className="w-4 h-4" />
              </a>

              <a
                href="https://x.com/dravyafolio"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="X (Twitter)"
                className="w-8 h-8 rounded-xl bg-zinc-100 hover:bg-zinc-200/80 text-zinc-700 hover:text-zinc-950 flex items-center justify-center transition-colors border border-zinc-200/80 shadow-2xs"
              >
                <Twitter className="w-4 h-4" />
              </a>
            </div>
          </div>

        </div>

      </div>
    </footer>
  );
}
