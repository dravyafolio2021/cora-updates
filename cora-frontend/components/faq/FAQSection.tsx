'use client';

import React, { useState, useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { Plus, Minus, ArrowRight, HelpCircle } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

interface FAQItem {
  question: string;
  answer: string;
}

const FAQS_DATA: FAQItem[] = [
  {
    question: 'How secure is our client, contract, and financial data?',
    answer: 'Your workspace is protected with SHA-256 cryptographic hashing, enterprise-grade AES-256 encryption, and isolated multi-tenant database vaults. All client contracts, GST invoices, and financial records remain 100% confidential, secure, and audit-ready.',
  },
  {
    question: 'Can our agency connect multiple client brands and workspaces?',
    answer: 'Yes. Cora provides multi-tenant workspace architecture with granular role-based access control (RBAC). You can manage unlimited client accounts, assign team leads to specific retainers, and isolate sensitive billing per brand entity.',
  },
  {
    question: 'How does automated 18% GST and SAC code invoicing work?',
    answer: 'Cora automatically calculates tax splits (CGST + SGST or IGST) based on your client’s registered GST state, applies creative/tech SAC codes (e.g., SAC 9983), and generates instant 1-click UPI and payment links ready for WhatsApp or email dispatch.',
  },
  {
    question: 'Is a free trial available before onboarding our team?',
    answer: 'Yes. You can start with a 14-day full-access agency trial with zero credit card required. Test out milestone e-signatures, run retainer cashflow projections, and invite your team members risk-free.',
  },
  {
    question: 'Do you offer customized plans for large creative teams or studios?',
    answer: 'Yes. For agencies with 10+ seats or high-volume retainers, we provide dedicated workspace onboarding, custom SOW master templates, custom domain branding, and priority SLA support.',
  },
];

export function FAQSection() {
  const [openIndex, setOpenIndex] = useState<number | null>(0);
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.faq-anim-item',
        { y: 24, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.7,
          stagger: 0.08,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 80%',
          },
        }
      );
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  const toggleFAQ = (index: number) => {
    const next = openIndex === index ? null : index;
    setOpenIndex(next);
    if (next !== null) {
      trackEvent('faq_expanded', { question: FAQS_DATA[index].question });
    }
  };

  return (
    <section
      ref={sectionRef}
      id="faq"
      className="py-20 sm:py-28 relative z-10 bg-white overflow-hidden"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
          
          {/* ── Left Column: Header & Support Card ── */}
          <div className="lg:col-span-5 space-y-8 sm:space-y-10 faq-anim-item">
            
            {/* Eyebrow & Title */}
            <div className="space-y-3.5">
              <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white rounded-full border border-zinc-200/90 text-xs font-semibold text-zinc-800 shadow-2xs">
                <HelpCircle className="w-3.5 h-3.5 text-zinc-900" />
                <span className="tracking-wide uppercase text-[11px] font-mono">FAQS</span>
                <span className="w-2 h-2 rounded-full bg-emerald-500 ml-0.5" />
              </div>

              <h2 className="font-display text-3xl sm:text-4xl lg:text-[44px] font-bold tracking-[-0.03em] text-zinc-950 leading-[1.15]">
                Frequently<br />asked questions
              </h2>
              
              <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed max-w-[380px]">
                Find quick answers to common questions about the platform, pricing, GST compliance, and workspace security.
              </p>
            </div>

            {/* Support Card (Cora Notion Monochromatic Tokens) */}
            <div className="bg-zinc-50/80 border border-zinc-200/90 rounded-2xl sm:rounded-3xl p-6 sm:p-7 space-y-5 shadow-2xs">
              
              {/* Avatars Pile + Monochromatic Tag */}
              <div className="flex items-center gap-2.5">
                <div className="flex -space-x-2 items-center">
                  <div className="w-8 h-8 rounded-full border-2 border-white overflow-hidden relative shrink-0 shadow-2xs">
                    <Image
                      src="/images/about_team_dev.jpg"
                      alt="Team Member"
                      fill
                      className="object-cover"
                    />
                  </div>
                  <div className="w-8 h-8 rounded-full border-2 border-white overflow-hidden relative shrink-0 shadow-2xs">
                    <Image
                      src="/images/about_team_ananya.jpg"
                      alt="Team Member"
                      fill
                      className="object-cover"
                    />
                  </div>
                  <div className="w-8 h-8 rounded-full border-2 border-white overflow-hidden relative shrink-0 shadow-2xs">
                    <Image
                      src="/images/about_team_aarav.jpg"
                      alt="Team Member"
                      fill
                      className="object-cover"
                    />
                  </div>
                </div>

                <div className="flex items-center gap-1.5 pl-0.5">
                  <span className="text-zinc-400 font-medium text-xs">+</span>
                  <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-zinc-950 text-white text-[10px] font-mono font-semibold tracking-wide shadow-2xs">
                    YOU
                  </span>
                </div>
              </div>

              {/* Text */}
              <div className="space-y-1">
                <h3 className="font-display text-base sm:text-lg font-bold text-zinc-950 tracking-tight">
                  Still have questions?
                </h3>
                <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                  Talk directly with our team or schedule a 1-on-1 walkthrough for your agency.
                </p>
              </div>

              {/* Cora Standard CTA Button */}
              <div>
                <a
                  href="mailto:help@heycora.in?subject=Question%20about%20Cora%20Studio"
                  onClick={() => trackEvent('faq_contact_clicked')}
                  className="inline-flex items-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white px-5 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-sm group cursor-pointer"
                >
                  <span>Talk to our team</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 group-hover:text-white transition-all" />
                </a>
              </div>

            </div>

          </div>

          {/* ── Right Column: Accordion Items (Cora Monochromatic Tokens) ── */}
          <div className="lg:col-span-7 space-y-3 faq-anim-item">
            {FAQS_DATA.map((faq, index) => {
              const isOpen = openIndex === index;
              
              if (isOpen) {
                return (
                  <div
                    key={index}
                    className="bg-zinc-50/90 rounded-2xl border border-zinc-300 p-5 sm:p-6 transition-all duration-200 select-none shadow-2xs"
                  >
                    <button
                      type="button"
                      onClick={() => toggleFAQ(index)}
                      className="w-full text-left flex items-center justify-between gap-4 cursor-pointer"
                    >
                      <h3 className="font-display font-bold text-sm sm:text-base text-zinc-950 tracking-tight">
                        {faq.question}
                      </h3>
                      <div className="w-7 h-7 rounded-xl bg-zinc-950 text-white flex items-center justify-center shrink-0 shadow-2xs">
                        <Minus className="w-3.5 h-3.5" />
                      </div>
                    </button>

                    <div className="pt-3 text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal border-t border-zinc-200/70 mt-3.5">
                      <p>{faq.answer}</p>
                    </div>
                  </div>
                );
              }

              return (
                <div
                  key={index}
                  className="bg-white hover:bg-zinc-50/70 rounded-2xl border border-zinc-200/80 p-5 sm:p-6 transition-all duration-200 select-none shadow-2xs"
                >
                  <button
                    type="button"
                    onClick={() => toggleFAQ(index)}
                    className="w-full text-left flex items-center justify-between gap-4 cursor-pointer"
                  >
                    <h3 className="font-display font-semibold text-sm sm:text-base text-zinc-900 tracking-tight">
                      {faq.question}
                    </h3>
                    <div className="w-7 h-7 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 flex items-center justify-center shrink-0 transition-colors">
                      <Plus className="w-3.5 h-3.5" />
                    </div>
                  </button>
                </div>
              );
            })}
          </div>

        </div>

      </div>
    </section>
  );
}
