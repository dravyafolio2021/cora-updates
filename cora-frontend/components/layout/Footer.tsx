'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { usePathname } from 'next/navigation';
import { ArrowRight, Mail, CheckCircle2, ChevronDown, Instagram, Linkedin, Twitter, Youtube } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

interface FooterSection {
  id: string;
  title: string;
  links: { label: string; href: string; highlight?: boolean; accent?: string }[];
}

const FOOTER_SECTIONS: FooterSection[] = [
  {
    id: 'platform',
    title: 'Platform',
    links: [
      { label: 'Features', href: '/features' },
      { label: 'Articles & Guides', href: '/articles', highlight: true },
      { label: 'Documentation', href: '/docs' },
      { label: 'Get A Demo', href: '/demo' },
      { label: 'AI Co-Founder', href: '/ai-agent' },
      { label: 'Use Cases', href: '/use-cases' },
      { label: 'Pricing', href: '/pricing' },
      { label: 'Changelog', href: '/changelog' },
    ],
  },
  {
    id: 'compare',
    title: 'Compare',
    links: [
      { label: 'vs HoneyBook', href: '/compare/cora-vs-honeybook' },
      { label: 'vs Studio Ninja', href: '/compare/cora-vs-studio-ninja' },
      { label: 'vs HubSpot', href: '/compare/cora-vs-hubspot' },
      { label: 'vs DocuSign', href: '/compare/cora-vs-docusign' },
      { label: 'All Comparisons →', href: '/compare', accent: 'text-emerald-700 font-medium' },
    ],
  },
  {
    id: 'ecosystem',
    title: 'Ecosystem',
    links: [
      { label: 'Integrations', href: '/integrations', highlight: true },
      { label: 'Embed Builder', href: '/tools/embed-builder' },
      { label: '18% GST Calculator', href: '/tools/gst-calculator' },
      { label: 'Brand & Assets', href: '/brand' },
      { label: 'About & Story', href: '/about' },
    ],
  },
  {
    id: 'legal',
    title: 'Trust & Legal',
    links: [
      { label: 'Terms of Service', href: '/terms' },
      { label: 'Privacy Policy', href: '/privacy' },
      { label: 'Refund Policy', href: '/refund-policy' },
      { label: 'Security & Trust', href: '/security' },
      { label: '99.95% SLA', href: '/sla' },
    ],
  },
];

export function Footer() {
  const pathname = usePathname();
  const [newsletterEmail, setNewsletterEmail] = useState('');
  const [isSubscribing, setIsSubscribing] = useState(false);
  const [newsletterSubscribed, setNewsletterSubscribed] = useState(false);
  const [openSection, setOpenSection] = useState<string | null>(null);

  const toggleSection = (id: string) => {
    setOpenSection((prev) => (prev === id ? null : id));
  };

  const handleNewsletterSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!newsletterEmail || !newsletterEmail.includes('@')) return;
    setIsSubscribing(true);
    try {
      const res = await fetch('/api/newsletter/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email: newsletterEmail.trim(),
          source: 'footer_minimal_widget',
          path: pathname || '',
          referrer: typeof document !== 'undefined' ? document.referrer : '',
        }),
      });
      const data = await res.json().catch(() => ({}));
      if (res.ok && data?.success !== false) {
        trackEvent('newsletter_subscribed', { source: 'footer_minimal_widget' });
        setNewsletterSubscribed(true);
      } else {
        window.location.href = 'https://business-on-autopilot.beehiiv.com/?modal=signup';
      }
    } catch {
      window.location.href = 'https://business-on-autopilot.beehiiv.com/?modal=signup';
    } finally {
      setIsSubscribing(false);
    }
  };

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
  const isToolDetailPage = pathname?.startsWith('/tools/') && pathname !== '/tools';
  const shouldHideFooterCta = is404 || isLegalPage || isDocsPage || isToolDetailPage;

  return (
    <footer className="relative w-full overflow-hidden pt-16 sm:pt-24 pb-28 sm:pb-36 md:pb-48 bg-[#FAF9F5]">
      
      {/* ── Responsive Ambient Landscape Background (Mobile Portrait & Desktop Landscape) ── */}
      <div className="absolute inset-0 pointer-events-none select-none z-0">
        {/* Mobile Portrait Artwork (with extended top sky) */}
        <div className="relative w-full h-full block sm:hidden">
          <Image
            src="/images/cora_footer_bg_mobile.webp"
            alt="Cora Horizon Mobile"
            fill
            sizes="100vw"
            priority={false}
            className="object-cover object-bottom"
          />
        </div>

        {/* Desktop Landscape Artwork (panoramic widescreen) */}
        <div className="relative w-full h-full hidden sm:block">
          <Image
            src="/images/cora_footer_bg_desktop.webp"
            alt="Cora Horizon Desktop"
            fill
            sizes="100vw"
            priority={false}
            className="object-cover object-bottom"
          />
        </div>

        {/* Seamless Soft Top Gradient Blend Transition */}
        <div className="absolute inset-0 bg-gradient-to-b from-[#FAF9F5] via-[#FAF9F5]/45 to-transparent pointer-events-none" />
      </div>

      <div className="relative z-10 w-full max-w-[1280px] mx-auto px-4 sm:px-6 md:px-8">
        
        {/* ── Top Conversion CTA Banner (Hidden on 404, Docs, Legal & Tool Detail Pages) ── */}
        {!shouldHideFooterCta && (
          <div className="text-center max-w-[760px] mx-auto mb-16 sm:mb-20 px-2 sm:px-0">
            {pathname?.startsWith('/tools') ? (
              <>
                <h2 className="font-display text-2xl xs:text-3xl sm:text-4xl md:text-[44px] font-semibold text-zinc-950 leading-[1.15] tracking-[-0.03em] mb-3">
                  Need these micro-tools in your client portal?
                </h2>
                <p className="text-zinc-600 text-xs sm:text-base font-normal leading-relaxed max-w-[540px] mx-auto mb-6 sm:mb-8">
                  Launch your free workspace. Automated 18% GST tax invoices and digital contracts pre-seeded.
                </p>
                <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 w-full max-w-sm sm:max-w-none mx-auto">
                  <a
                    href="https://app.heycora.in/workspace/login?source=footer_cta_tools"
                    onClick={() => trackEvent('cta_click', { section: 'footer_cta_tools_primary' })}
                    className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs text-center cursor-pointer"
                  >
                    <span>Get started free</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </a>
                  <Link
                    href="/demo"
                    onClick={() => trackEvent('cta_click', { section: 'footer_cta_tools_demo' })}
                    className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-zinc-950 border border-zinc-300 hover:border-zinc-400 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-50 transition-all shadow-2xs text-center cursor-pointer"
                  >
                    <span>Explore Interactive Demo</span>
                  </Link>
                </div>
              </>
            ) : (
              <>
                <h2 className="font-display text-2xl xs:text-3xl sm:text-4xl md:text-[48px] font-semibold text-zinc-950 leading-[1.14] tracking-[-0.03em] mb-3 sm:mb-4">
                  Ready to simplify your business?
                </h2>
                <p className="text-zinc-600 text-sm sm:text-lg font-normal leading-relaxed max-w-[600px] mx-auto mb-6 sm:mb-8">
                  Join Indian founders managing their daily operations, GST invoices, and WhatsApp leads in one place.
                </p>

                <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 w-full max-w-sm sm:max-w-none mx-auto">
                  <a
                    href="https://app.heycora.in/workspace/login?source=footer_cta"
                    onClick={() => trackEvent('cta_click', { section: 'footer_cta_primary' })}
                    className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs text-center cursor-pointer"
                  >
                    <span>Start free — no card needed</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </a>

                  <a
                    href="mailto:dravya.bansal@heycora.in?subject=Inquiry%20from%20Cora%20Website"
                    onClick={() => trackEvent('cta_click', { section: 'footer_cta_chat_founder' })}
                    className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-zinc-950 border border-zinc-300 hover:border-zinc-400 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-50 transition-all shadow-2xs text-center cursor-pointer"
                  >
                    <span>Chat with Founder</span>
                  </a>
                </div>
              </>
            )}
          </div>
        )}

        {/* ── Open Editorial Navigation Grid ── */}
        <div className="grid grid-cols-2 md:grid-cols-12 gap-8 md:gap-10 pb-10 sm:pb-12">
          
          {/* Col 1: Clean CORA Logo Block & Support Email */}
          <div className="col-span-2 md:col-span-4 space-y-4">
            
            {/* Clean Light-weight CORA Wordmark */}
            <Link
              href="/"
              className="text-zinc-950 font-display uppercase tracking-[-0.03em] hover:opacity-80 transition-opacity block w-fit font-semibold text-[1.85rem] sm:text-[2rem] leading-none"
            >
              <span>CORA</span>
            </Link>

            <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed max-w-[340px] font-normal">
              The AI co-founder for Indian service businesses, clinics, gyms, salons, and solo founders.
            </p>

            {/* Minimal Newsletter Subscribe Widget */}
            <div className="pt-1 pb-1 max-w-[340px]">
              {newsletterSubscribed ? (
                <div className="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-medium">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                  <span>Subscribed to Operator Brief!</span>
                </div>
              ) : (
                <form onSubmit={handleNewsletterSubmit} className="flex items-center gap-1.5 w-full">
                  <input
                    type="email"
                    required
                    value={newsletterEmail}
                    onChange={(e) => setNewsletterEmail(e.target.value)}
                    placeholder="Enter work email for Operator Brief..."
                    className="flex-1 min-w-0 bg-white/90 hover:bg-white focus:bg-white border border-zinc-200 focus:border-zinc-950 rounded-xl px-3.5 py-2.5 text-xs text-zinc-900 placeholder:text-zinc-400 focus:outline-none transition-all shadow-2xs"
                  />
                  <button
                    type="submit"
                    disabled={isSubscribing}
                    className="shrink-0 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-50 text-white px-4 py-2.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all cursor-pointer shadow-2xs"
                  >
                    <span>{isSubscribing ? '...' : 'Join'}</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </button>
                </form>
              )}
            </div>

            <div className="flex flex-col gap-2.5 pt-1">
              <a
                href="mailto:support@heycora.in"
                className="inline-flex items-center gap-2.5 bg-zinc-200/60 hover:bg-zinc-200 text-zinc-800 px-4 py-2.5 rounded-xl text-xs font-medium transition-all border border-zinc-200/50 shadow-2xs w-fit"
              >
                <Mail className="w-3.5 h-3.5 text-zinc-600" />
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

          {/* Desktop Link Columns (Hidden on mobile, 4 columns on md:) */}
          <div className="hidden md:grid md:col-span-8 md:grid-cols-4 md:gap-8">
            {FOOTER_SECTIONS.map((section) => (
              <div key={section.id} className="space-y-3">
                <div className="font-display text-xs font-semibold text-zinc-950 uppercase tracking-wider">
                  {section.title}
                </div>
                <ul className="space-y-2.5 text-xs sm:text-[13px] text-zinc-600 font-normal sm:font-medium">
                  {section.links.map((link) => (
                    <li key={link.href}>
                      <Link
                        href={link.href}
                        className={`hover:text-zinc-950 transition-colors ${
                          link.accent || (link.highlight ? 'font-medium text-zinc-950' : '')
                        }`}
                      >
                        {link.label}
                      </Link>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>

          {/* Mobile Closed Accordions (Visible on mobile, hidden on md:) */}
          <div className="md:hidden col-span-2 border-t border-zinc-200/80 pt-2 divide-y divide-zinc-200/70">
            {FOOTER_SECTIONS.map((section) => {
              const isOpen = openSection === section.id;
              return (
                <div key={section.id} className="py-2.5">
                  <button
                    type="button"
                    onClick={() => toggleSection(section.id)}
                    className="w-full flex items-center justify-between py-1 text-left cursor-pointer group"
                    aria-expanded={isOpen}
                  >
                    <span className="font-display text-xs font-semibold text-zinc-950 uppercase tracking-wider group-hover:text-zinc-700 transition-colors">
                      {section.title}
                    </span>
                    <ChevronDown
                      className={`w-4 h-4 text-zinc-500 transition-transform duration-200 ${
                        isOpen ? 'rotate-180 text-zinc-900' : ''
                      }`}
                    />
                  </button>
                  {isOpen && (
                    <ul className="pt-2 pb-1 space-y-2.5 text-xs text-zinc-600 font-normal pl-1 animate-in fade-in slide-in-from-top-1 duration-200">
                      {section.links.map((link) => (
                        <li key={link.href}>
                          <Link
                            href={link.href}
                            className={`block py-0.5 hover:text-zinc-950 transition-colors ${
                              link.accent || (link.highlight ? 'font-medium text-zinc-950' : '')
                            }`}
                          >
                            {link.label}
                          </Link>
                        </li>
                      ))}
                    </ul>
                  )}
                </div>
              );
            })}
          </div>

        </div>

        {/* ── Sub-Footer Divider & Metadata ── */}
        <div className="pt-6 pb-6 border-t border-zinc-200/80 flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-zinc-500">
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
              className="w-8 h-8 rounded-xl bg-zinc-200/60 hover:bg-zinc-200 text-zinc-700 hover:text-zinc-950 flex items-center justify-center transition-colors border border-zinc-200/50 shadow-2xs"
            >
              <Instagram className="w-4 h-4" />
            </a>

            <a
              href="https://linkedin.com/in/dravyafolio"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="LinkedIn"
              className="w-8 h-8 rounded-xl bg-zinc-200/60 hover:bg-zinc-200 text-zinc-700 hover:text-zinc-950 flex items-center justify-center transition-colors border border-zinc-200/50 shadow-2xs"
            >
              <Linkedin className="w-4 h-4" />
            </a>

            <a
              href="https://youtube.com/@dravyafolio"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="YouTube"
              className="w-8 h-8 rounded-xl bg-zinc-200/60 hover:bg-zinc-200 text-zinc-700 hover:text-zinc-950 flex items-center justify-center transition-colors border border-zinc-200/50 shadow-2xs"
            >
              <Youtube className="w-4 h-4" />
            </a>

            <a
              href="https://x.com/dravyafolio"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="X (Twitter)"
              className="w-8 h-8 rounded-xl bg-zinc-200/60 hover:bg-zinc-200 text-zinc-700 hover:text-zinc-950 flex items-center justify-center transition-colors border border-zinc-200/50 shadow-2xs"
            >
              <Twitter className="w-4 h-4" />
            </a>
          </div>
        </div>

      </div>

    </footer>
  );
}
