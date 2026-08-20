'use client';

import React, { useState, useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { Plus, Minus, ArrowRight } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

const FAQS_DATA = [
  {
    question: 'How secure is my client data and financial records?',
    answer: 'Your data is protected with industry-standard 256-bit encryption, SOC-2 compliant cloud infrastructure, and immutable cryptographic audit hashes (SHA-256) satisfying the Indian Information Technology Act 2000.',
  },
  {
    question: 'Can I connect my WhatsApp, email, and payment rails?',
    answer: 'Yes! Cora integrates directly with Meta WhatsApp Cloud API for automated call-sheet dispatch, Google Calendar for studio call times, and Razorpay/UPI/Stripe for automated 18% GST invoice collection in under 2 minutes.',
  },
  {
    question: 'How does the multi-model AI routing work?',
    answer: 'Cora dynamically routes requests between Claude 3.5 Sonnet (for complex legal contracts & studio proposals) and Google Gemini 2.0 Flash (for sub-400ms property copy & speed extraction) with zero per-model API keys required.',
  },
  {
    question: 'Is a free trial available before subscribing?',
    answer: 'Yes, every workspace receives a 14-day full access pass with 1,000 complimentary AI agent runs, unlimited e-signatures, and GST invoicing with no credit card required to start.',
  },
  {
    question: 'Do you offer plans for production houses or agencies?',
    answer: 'Yes. Our Studio Pro and Agency Scale tiers support multiple team seats, custom role permissions (crew vs accountant vs client), dedicated WhatsApp phone numbers, and custom branded client portals.',
  },
];

export function FAQSection() {
  const [openIndex, setOpenIndex] = useState<number | null>(0);
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.faq-anim-item',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          stagger: 0.1,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 78%',
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
      className="py-20 sm:py-28 relative z-10 bg-white border-b border-zinc-200/70 overflow-hidden"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-start">
          
          {/* ── Left Column: Title & "Still have questions?" Card ── */}
          <div className="lg:col-span-5 space-y-8 faq-anim-item">
            
            <div className="space-y-3.5">
              <h2 className="font-display text-3xl xs:text-4xl sm:text-[44px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em]">
                Frequently<br className="hidden sm:inline" /> asked questions
              </h2>
              <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed max-w-[380px]">
                Find quick answers to common questions about the platform, pricing, and security.
              </p>
            </div>

            {/* "Still have questions?" Support Card */}
            <div className="bg-[#F3F5F8] border border-zinc-200/90 rounded-[28px] p-6 sm:p-8 space-y-5 shadow-[0px_10px_30px_rgba(0,0,0,0.03)]">
              
              {/* Overlapping Avatars */}
              <div className="flex items-center">
                <div className="flex -space-x-2.5 overflow-hidden">
                  <div className="w-9 h-9 rounded-full border-2 border-white overflow-hidden shadow-2xs relative">
                    <Image src="/images/dravya_bansal_black.jpg" alt="Support 1" fill className="object-cover" />
                  </div>
                  <div className="w-9 h-9 rounded-full border-2 border-white overflow-hidden shadow-2xs relative">
                    <Image src="/images/usecase_solo_creator.jpg" alt="Support 2" fill className="object-cover" />
                  </div>
                  <div className="w-9 h-9 rounded-full border-2 border-white overflow-hidden shadow-2xs relative">
                    <Image src="/images/usecase_realestate_agency.jpg" alt="Support 3" fill className="object-cover" />
                  </div>
                </div>

                <div className="flex items-center gap-1 ml-3 text-xs font-bold text-zinc-700">
                  <span>+</span>
                  <span className="w-7 h-7 rounded-full bg-[#1A73E8] text-white flex items-center justify-center text-[10px] font-bold shadow-2xs">
                    You
                  </span>
                </div>
              </div>

              <div className="space-y-1">
                <h3 className="font-display text-lg font-bold text-zinc-950 tracking-tight">
                  Still have questions?
                </h3>
                <p className="text-zinc-600 text-xs sm:text-sm font-normal leading-relaxed">
                  Reach out, and our founder and engineering team will guide you.
                </p>
              </div>

              <div>
                <a
                  href="mailto:dravya.bansal@heycora.in?subject=Question%20about%20Cora%20Studio%20OS"
                  onClick={() => trackEvent('faq_talk_team_clicked')}
                  className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm border border-zinc-800 group"
                >
                  <span>Talk to our team</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                </a>
              </div>

            </div>

          </div>

          {/* ── Right Column: Expandable Rounded Accordions ── */}
          <div className="lg:col-span-7 space-y-3.5 faq-anim-item">
            {FAQS_DATA.map((faq, idx) => {
              const isOpen = openIndex === idx;

              return (
                <div
                  key={idx}
                  onClick={() => toggleFAQ(idx)}
                  className={`rounded-[24px] border transition-all duration-300 cursor-pointer select-none ${
                    isOpen
                      ? 'bg-[#ECEFF3] border-zinc-300/80 p-6 sm:p-7 shadow-xs'
                      : 'bg-white border-zinc-200/90 hover:border-zinc-300 p-5 sm:p-6 hover:bg-zinc-50/60'
                  }`}
                >
                  {/* Top Bar: Question & Circle Button */}
                  <div className="flex items-center justify-between gap-4">
                    <span className="font-display text-sm sm:text-base font-bold text-zinc-950 leading-snug">
                      {faq.question}
                    </span>

                    <div
                      className={`w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition-colors ${
                        isOpen
                          ? 'bg-zinc-950 text-white shadow-2xs'
                          : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200'
                      }`}
                    >
                      {isOpen ? (
                        <Minus className="w-4 h-4 stroke-[2.5]" />
                      ) : (
                        <Plus className="w-4 h-4 stroke-[2.5]" />
                      )}
                    </div>
                  </div>

                  {/* Open Answer Drawer */}
                  {isOpen && (
                    <div className="mt-3.5 pt-2 text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
                      {faq.answer}
                    </div>
                  )}
                </div>
              );
            })}
          </div>

        </div>

      </div>
    </section>
  );
}
