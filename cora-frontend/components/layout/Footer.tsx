'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, Mail, Instagram, Linkedin, Twitter, Sparkles, ShieldCheck } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export function Footer() {
  return (
    <footer className="relative w-full overflow-hidden pt-20 sm:pt-28 pb-12">
      
      {/* ── Background Landscape Horizon & Atmospheric Overlays ── */}
      <div className="absolute inset-0 pointer-events-none select-none z-0">
        <Image
          src="/images/cora_hero_landscape.jpg"
          alt="Landscape Horizon"
          fill
          sizes="100vw"
          className="object-cover object-[center_55%]"
        />
        {/* Soft sky overlays fading down into the card */}
        <div className="absolute inset-0 bg-gradient-to-b from-white via-white/80 to-transparent" />
        <div className="absolute inset-0 bg-gradient-to-t from-white/90 via-transparent to-transparent" />
      </div>

      <div className="relative z-10 w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Top Conversion CTA Banner ── */}
        <div className="text-center max-w-[760px] mx-auto mb-16 sm:mb-20">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-[48px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-4">
            Ready to run an autonomous studio?
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[600px] mx-auto mb-8">
            Join 1,200+ founders and creative operators using AI dispatch, legally binding digital contracts, and automated GST invoicing.
          </p>

          {/* Action Buttons following Cora Design System */}
          <div className="flex items-center justify-center flex-wrap gap-3.5">
            <a
              href="https://app.heycora.in/workspace/login?source=footer_cta"
              onClick={() => trackEvent('cta_click', { section: 'footer_cta_primary' })}
              className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm border border-zinc-800 group"
            >
              <span>Get started for Free</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
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

        {/* ── Master White Footer Card ── */}
        <div className="w-full rounded-[36px] bg-white border border-zinc-200/90 shadow-[0px_20px_60px_rgba(0,0,0,0.06)] p-8 sm:p-12 md:p-14">
          
          {/* Main 5-Column Navigation Layout */}
          <div className="grid grid-cols-2 md:grid-cols-12 gap-8 md:gap-10 pb-12 border-b border-zinc-200/80">
            
            {/* Col 1: Brand Bio & Support Email (Span 4) */}
            <div className="col-span-2 md:col-span-4 space-y-5">
              <Link href="/" className="text-zinc-950 font-display font-bold text-2xl sm:text-3xl tracking-tight block">
                <span>Cora</span>
              </Link>

              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed max-w-[320px] font-normal">
                An autonomous operating system for modern commercial photography studios, film production houses, and creative agencies.
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
                <li><Link href="/features" className="hover:text-zinc-950 transition-colors">20 Built Modules</Link></li>
                <li><Link href="/ai-agent" className="hover:text-zinc-950 transition-colors">AI Co-Founder</Link></li>
                <li><Link href="/use-cases" className="hover:text-zinc-950 transition-colors">5 Workspaces</Link></li>
                <li><Link href="/pricing" className="hover:text-zinc-950 transition-colors">Pricing Plans</Link></li>
                <li><Link href="/changelog" className="hover:text-zinc-950 transition-colors">Changelog &bull; v2.4</Link></li>
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
                <li><Link href="/compare/cora-vs-hubspot" className="hover:text-zinc-950 transition-colors">vs HubSpot CRM</Link></li>
                <li><Link href="/compare/cora-vs-docusign" className="hover:text-zinc-950 transition-colors">vs DocuSign</Link></li>
                <li><Link href="/compare" className="hover:text-zinc-950 transition-colors text-emerald-700 font-bold">All 8 Benchmarks &rarr;</Link></li>
              </ul>
            </div>

            {/* Col 4: Tools & Ecosystem (Span 2) */}
            <div className="col-span-1 md:col-span-2 space-y-3">
              <div className="font-display text-xs font-bold text-zinc-950 uppercase tracking-wider">
                Ecosystem
              </div>
              <ul className="space-y-2 text-xs sm:text-[13px] text-zinc-600 font-medium">
                <li><Link href="/integrations" className="hover:text-zinc-950 transition-colors font-semibold text-zinc-900">Framer &amp; Webflow Hub</Link></li>
                <li><Link href="/tools/embed-builder" className="hover:text-zinc-950 transition-colors">1-Click Embed Builder</Link></li>
                <li><Link href="/tools/gst-calculator" className="hover:text-zinc-950 transition-colors">18% GST Calculator</Link></li>
                <li><Link href="/tools/listing-ai" className="hover:text-zinc-950 transition-colors">Real Estate AI</Link></li>
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
                <li><Link href="/refund-policy" className="hover:text-zinc-950 transition-colors">14-Day Refunds</Link></li>
                <li><Link href="/security" className="hover:text-zinc-950 transition-colors">Security &amp; SOC-2</Link></li>
                <li><Link href="/sla" className="hover:text-zinc-950 transition-colors">99.95% SLA</Link></li>
              </ul>
            </div>

          </div>

          {/* ── Sub-Footer: Copyright, Legal Links & Socials ── */}
          <div className="pt-8 flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-zinc-500">
            <div className="text-center md:text-left">
              &copy; {new Date().getFullYear()} Cora Platforms Inc. All rights reserved. Indian IT Act 2000 &amp; GST compliant.
            </div>

            {/* Middle: Inline Policy Quick Links */}
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

            {/* Social Icons in Clean Rounded-xl Buttons */}
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
