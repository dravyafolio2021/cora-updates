'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, Mail, Instagram, Linkedin, Twitter, Facebook } from 'lucide-react';
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

      <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
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
          
          {/* Main 4-Column Navigation Layout */}
          <div className="grid grid-cols-1 md:grid-cols-12 gap-10 md:gap-12 pb-12 border-b border-zinc-200/80">
            
            {/* Col 1: Brand Bio & Support Email (Span 5) */}
            <div className="md:col-span-5 space-y-6">
              <Link href="/" className="inline-flex items-center gap-2.5 text-zinc-950 font-display font-bold text-2xl tracking-tight group">
                <div className="w-9 h-9 rounded-[10px] bg-zinc-950 text-white flex items-center justify-center font-mono text-sm font-bold shadow-2xs border border-zinc-800">
                  &lt; &gt;
                </div>
                <span className="font-display font-bold text-2xl text-zinc-950 tracking-tight">
                  Cora
                </span>
              </Link>

              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed max-w-[340px] font-normal">
                An autonomous operating system for modern commercial photography studios, film production houses, and creative agencies.
              </p>

              <div>
                <a
                  href="mailto:support@heycora.in"
                  className="inline-flex items-center gap-2 bg-[#100F12] text-white px-4 py-2.5 rounded-xl text-xs font-semibold hover:bg-zinc-800 transition-all border border-zinc-800 shadow-2xs"
                >
                  <Mail className="w-3.5 h-3.5 text-zinc-400" />
                  <span>support@heycora.in</span>
                </a>
              </div>
            </div>

            {/* Col 2: Quick Links (Span 2) */}
            <div className="md:col-span-2 space-y-3.5">
              <div className="font-display text-xs font-bold text-zinc-950 uppercase tracking-wider">
                Quick links
              </div>
              <ul className="space-y-2.5 text-xs sm:text-[13px] text-zinc-600 font-medium">
                <li><a href="/#capabilities" className="hover:text-zinc-950 transition-colors">Features</a></li>
                <li><a href="/#how-it-works" className="hover:text-zinc-950 transition-colors">How It Works</a></li>
                <li><a href="/#use-cases" className="hover:text-zinc-950 transition-colors">Use Cases</a></li>
                <li><a href="/#integrations" className="hover:text-zinc-950 transition-colors">Integrations</a></li>
                <li><a href="/#stats" className="hover:text-zinc-950 transition-colors">Platform Stats</a></li>
              </ul>
            </div>

            {/* Col 3: Pages & Tools (Span 3) */}
            <div className="md:col-span-3 space-y-3.5">
              <div className="font-display text-xs font-bold text-zinc-950 uppercase tracking-wider">
                Pages &amp; Tools
              </div>
              <ul className="space-y-2.5 text-xs sm:text-[13px] text-zinc-600 font-medium">
                <li><Link href="/tools/gst-calculator" className="hover:text-zinc-950 transition-colors">18% GST Tax Calculator</Link></li>
                <li><Link href="/tools/listing-ai" className="hover:text-zinc-950 transition-colors">Real Estate AI Writer</Link></li>
                <li><Link href="/tools" className="hover:text-zinc-950 transition-colors">All Public Micro-Tools &rarr;</Link></li>
                <li><a href="/#pricing" className="hover:text-zinc-950 transition-colors">Commercial Pricing</a></li>
                <li><a href="https://app.heycora.in/workspace/login" className="hover:text-zinc-950 transition-colors text-zinc-900 font-semibold">Workspace Portal &rarr;</a></li>
              </ul>
            </div>

            {/* Col 4: Support & Legal (Span 2) */}
            <div className="md:col-span-2 space-y-3.5">
              <div className="font-display text-xs font-bold text-zinc-950 uppercase tracking-wider">
                Support
              </div>
              <ul className="space-y-2.5 text-xs sm:text-[13px] text-zinc-600 font-medium">
                <li><a href="/#faq" className="hover:text-zinc-950 transition-colors">FAQs</a></li>
                <li><a href="mailto:support@heycora.in" className="hover:text-zinc-950 transition-colors">Contact Support</a></li>
                <li><a href="https://x.com/dravyafolio" target="_blank" rel="noopener noreferrer" className="hover:text-zinc-950 transition-colors">Changelog &amp; Updates</a></li>
                <li><span className="text-zinc-400">Privacy Policy</span></li>
              </ul>
            </div>

          </div>

          {/* ── Sub-Footer: Copyright & Social Links ── */}
          <div className="pt-8 flex flex-col sm:flex-row items-center justify-between gap-6 text-xs text-zinc-500">
            <div>
              &copy; {new Date().getFullYear()} Cora Inc. All rights reserved. Indian IT Act 2000 &amp; GST compliant.
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
