'use client';

import React, { useState, useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { Plus, Minus, ArrowRight, MessageSquare } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

const FAQS_DATA = [
  {
    question: 'What exactly is Cora?',
    answer: 'Cora is an AI co-founder for Indian service businesses. Instead of clicking through complicated software, you manage your clients, invoices, appointments, and marketing via a simple chat interface.',
  },
  {
    question: 'Do I need accounting or technical skills to use Cora?',
    answer: 'No. If you know how to send a message on WhatsApp, you can use Cora. You simply type what you want done in plain English or Hinglish.',
  },
  {
    question: 'Is the free plan really free?',
    answer: 'Yes. The Free Forever plan gives you core chat assistance and up to 15 invoices every month with zero credit card required.',
  },
  {
    question: 'How does Cora handle GST and payments?',
    answer: 'Cora calculates 18% (and custom) GST breakdowns automatically and creates ready-to-share UPI and Razorpay payment links for your clients.',
  },
  {
    question: 'How is Cora different from ChatGPT?',
    answer: 'ChatGPT starts blank every time. Cora already knows your service menu, pricing, client names, and invoices, so you never have to re-explain your business context.',
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
          
          {/* Left Column */}
          <div className="lg:col-span-5 space-y-8 faq-anim-item">
            
            <div className="space-y-3.5">
              <span className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono inline-block">
                COMMON QUESTIONS
              </span>
              <h2 className="font-display text-3xl xs:text-4xl sm:text-[44px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em]">
                Frequently<br className="hidden sm:inline" /> asked questions
              </h2>
              <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed max-w-[380px]">
                Everything you need to know about getting started with Cora.
              </p>
            </div>

            {/* Support Card */}
            <div className="bg-[#F3F5F8] border border-zinc-200/90 rounded-[28px] p-6 sm:p-8 space-y-5 shadow-2xs">
              <div className="flex items-center">
                <div className="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center font-bold text-xs">
                  D
                </div>
              </div>

              <div className="space-y-1.5">
                <h4 className="font-display font-bold text-zinc-950 text-base sm:text-lg">
                  Have a specific question?
                </h4>
                <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                  Talk directly with our founder or start your free workspace in 30 seconds.
                </p>
              </div>

              <div>
                <a
                  href="mailto:dravya.bansal@heycora.in?subject=Question%20about%20Cora"
                  onClick={() => trackEvent('faq_contact_clicked')}
                  className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
                >
                  <span>Chat with Founder</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>
              </div>
            </div>

          </div>

          {/* Right Column: Accordion */}
          <div className="lg:col-span-7 space-y-3.5 faq-anim-item">
            {FAQS_DATA.map((faq, index) => {
              const isOpen = openIndex === index;
              return (
                <div
                  key={index}
                  className={`rounded-2xl border transition-all duration-200 overflow-hidden ${
                    isOpen
                      ? 'bg-zinc-50 border-zinc-950 shadow-2xs'
                      : 'bg-white border-zinc-200/80 hover:border-zinc-300'
                  }`}
                >
                  <button
                    type="button"
                    onClick={() => toggleFAQ(index)}
                    className="w-full p-5 sm:p-6 text-left flex items-center justify-between gap-4 select-none"
                  >
                    <span className="font-display font-bold text-sm sm:text-base text-zinc-950 tracking-tight">
                      {faq.question}
                    </span>
                    <div
                      className={`w-7 h-7 rounded-full flex items-center justify-center shrink-0 transition-colors ${
                        isOpen ? 'bg-zinc-950 text-white' : 'bg-zinc-100 text-zinc-700'
                      }`}
                    >
                      {isOpen ? <Minus className="w-3.5 h-3.5" /> : <Plus className="w-3.5 h-3.5" />}
                    </div>
                  </button>

                  {isOpen && (
                    <div className="px-5 sm:px-6 pb-5 sm:pb-6 text-xs sm:text-sm text-zinc-600 leading-relaxed pt-0">
                      <p>{faq.answer}</p>
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
