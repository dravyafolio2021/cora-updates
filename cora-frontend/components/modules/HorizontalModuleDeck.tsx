'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import {
  Plus,
  MessageSquare,
  ShieldCheck,
  Receipt,
  Calendar,
  TrendingUp,
  Sparkles,
  ArrowRight,
} from 'lucide-react';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

interface WorkflowCardData {
  id: string;
  badge: string;
  title: string;
  subtitle: string;
  image: string;
  iconBg: string;
  Icon: React.ComponentType<{ className?: string }>;
  tags: string[];
}

const workflowCards: WorkflowCardData[] = [
  {
    id: 'crm-workflow',
    badge: 'Inquiries & CRM',
    title: 'Client Inquiries & Briefs',
    subtitle: 'Captures incoming client briefs from WhatsApp & calls into structured shoot requests with automated hold tags.',
    image: '/images/indian_card_crm.jpg',
    iconBg: 'bg-amber-600',
    Icon: Sparkles,
    tags: ['WhatsApp Sync', 'Auto-Intake', 'Shoot Briefs'],
  },
  {
    id: 'whatsapp-workflow',
    badge: 'WhatsApp Concierge',
    title: 'Instant Quotes & Closes',
    subtitle: 'Quotes approved rate cards, answers commercial inquiries, and closes bookings 24/7 with zero delay.',
    image: '/images/indian_card_whatsapp.jpg',
    iconBg: 'bg-sky-600',
    Icon: MessageSquare,
    tags: ['Instant Rate Math', '24/7 Follow-ups', 'Fast Closes'],
  },
  {
    id: 'gst-workflow',
    badge: '18% GST Billing',
    title: 'Tax Math & Dynamic UPI QR',
    subtitle: 'Calculates CGST/SGST splits, outputs UPI soundbox payment QR standees, and generates GSTR-1 ready sales PDFs.',
    image: '/images/indian_card_gst.jpg',
    iconBg: 'bg-emerald-600',
    Icon: Receipt,
    tags: ['18% GST Split', 'Dynamic UPI QR', 'Tally Sync'],
  },
  {
    id: 'legal-workflow',
    badge: 'Contracts & E-Sign',
    title: 'Agreements & Model NDAs',
    subtitle: 'Generates IT Act compliant commercial agreements, shoots NDAs, and collects legally binding e-signatures.',
    image: '/images/indian_card_legal.jpg',
    iconBg: 'bg-purple-600',
    Icon: ShieldCheck,
    tags: ['Commercial Terms', '50% Advance Clause', 'E-Signature'],
  },
  {
    id: 'calendar-workflow',
    badge: 'Shoot Bookings',
    title: 'Call-Sheets & Slot Holds',
    subtitle: 'Schedules production slots, sends automated WhatsApp call-time reminders, and locks studio hold dates.',
    image: '/images/indian_card_calendar.jpg',
    iconBg: 'bg-yellow-600',
    Icon: Calendar,
    tags: ['Hold Protection', 'Crew Dispatch', 'Call-Sheets'],
  },
  {
    id: 'finance-workflow',
    badge: 'Cash Flow & Accounts',
    title: 'Revenue & Tax Summary',
    subtitle: 'Live daily revenue tracking, outstanding client balances, and CA-ready ledger export in one tap.',
    image: '/images/indian_card_finance.jpg',
    iconBg: 'bg-indigo-600',
    Icon: TrendingUp,
    tags: ['Live Ledger', 'CA-Ready Export', 'Balance Alerts'],
  },
];

export function HorizontalModuleDeck() {
  const containerRef = useRef<HTMLDivElement>(null);
  const trackRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const container = containerRef.current;
    const track = trackRef.current;
    if (!container || !track) return;

    const ctx = gsap.context(() => {
      const getScrollAmount = () => {
        return track.scrollWidth - window.innerWidth + (window.innerWidth < 768 ? 60 : 160);
      };

      const tween = gsap.to(track, {
        x: () => -getScrollAmount(),
        ease: 'none',
      });

      ScrollTrigger.create({
        trigger: container,
        start: 'top top',
        end: () => `+=${getScrollAmount() * 1.3}`,
        pin: true,
        animation: tween,
        scrub: 1,
        invalidateOnRefresh: true,
      });

      // Parallax Card Tilt & Depth
      gsap.utils.toArray<HTMLElement>('.cora-parallax-card').forEach((card) => {
        gsap.fromTo(
          card,
          { scale: 0.95, opacity: 0.8 },
          {
            scale: 1,
            opacity: 1,
            ease: 'power2.out',
            scrollTrigger: {
              trigger: card,
              containerAnimation: tween,
              start: 'left 90%',
              end: 'center center',
              scrub: true,
            },
          }
        );
      });
    }, containerRef);

    return () => ctx.revert();
  }, []);

  return (
    <section
      id="modular-workflows"
      ref={containerRef}
      className="bg-[#FAFAFA] relative z-10 overflow-hidden border-b border-zinc-200/60 min-h-screen flex flex-col justify-center py-16 sm:py-20"
    >
      <div className="w-full max-w-[1400px] mx-auto px-4 sm:px-6 mb-8 sm:mb-12">
        {/* ── Section Header ── */}
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-6">
          <div className="max-w-[720px]">
            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-100 border border-zinc-200 text-zinc-700 text-xs font-semibold uppercase tracking-wider mb-3">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse" />
              <span>Modular Workflows</span>
            </div>
            <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-6xl font-bold text-zinc-950 leading-[1.08] tracking-[-0.03em]">
              One chat bar. <br className="hidden sm:inline" />
              Every business task.
            </h2>
          </div>
          <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed max-w-[420px]">
            Scroll horizontally through unified workflows running your client briefs, 18% GST billing, legal NDAs, and financial ledger.
          </p>
        </div>
      </div>

      {/* ── Horizontal Scrolling Card Track ── */}
      <div className="w-full overflow-visible">
        <div
          ref={trackRef}
          className="flex items-center gap-6 sm:gap-8 px-4 sm:px-8 w-max will-change-transform"
        >
          {workflowCards.map((card) => {
            const Icon = card.Icon;
            return (
              <div
                key={card.id}
                className="cora-parallax-card group relative w-[300px] sm:w-[350px] md:w-[380px] h-[480px] sm:h-[530px] rounded-[36px] overflow-hidden shadow-[0px_16px_40px_rgba(0,0,0,0.08)] border border-zinc-200/80 bg-white shrink-0 flex flex-col justify-between p-6 sm:p-7 transition-all duration-300 hover:shadow-[0px_24px_50px_rgba(0,0,0,0.12)] cursor-pointer"
              >
                {/* Full-bleed Portrait Image */}
                <Image
                  src={card.image}
                  alt={card.title}
                  fill
                  className="absolute inset-0 object-cover -z-10 group-hover:scale-105 transition-transform duration-700 ease-out"
                  sizes="(max-width: 768px) 300px, 380px"
                  priority
                />

                {/* Top Badge (Clean Category Label) */}
                <div className="relative z-10 flex items-center justify-start">
                  <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/95 backdrop-blur-md border border-white/90 shadow-[0px_4px_16px_rgba(0,0,0,0.08)]">
                    <div className={`w-5 h-5 rounded-full ${card.iconBg} text-white flex items-center justify-center`}>
                      <Icon className="w-2.5 h-2.5" />
                    </div>
                    <span className="text-xs sm:text-sm font-bold text-zinc-950 tracking-tight">
                      {card.badge}
                    </span>
                  </div>
                </div>

                {/* Bottom Content & Floating Action Button */}
                <div className="relative z-10 space-y-3">
                  {/* Frosted Glass Information Pill */}
                  <div className="bg-white/95 backdrop-blur-md rounded-2xl p-4 border border-white/90 shadow-[0px_8px_24px_rgba(0,0,0,0.08)] space-y-1.5 transition-transform duration-300 group-hover:-translate-y-1">
                    <h3 className="text-base sm:text-lg font-bold text-zinc-950 tracking-tight">
                      {card.title}
                    </h3>
                    <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed line-clamp-2">
                      {card.subtitle}
                    </p>
                    <div className="pt-2 flex items-center gap-1.5 flex-wrap">
                      {card.tags.map((tag, tIdx) => (
                        <span
                          key={tIdx}
                          className="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 text-[10px] font-semibold"
                        >
                          {tag}
                        </span>
                      ))}
                    </div>
                  </div>

                  {/* Clean Bottom Action Row */}
                  <div className="flex items-center justify-between pt-1">
                    <span className="text-xs font-semibold text-zinc-900 bg-white/90 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-2xs">
                      Explore Workflow &rarr;
                    </span>
                    <button
                      type="button"
                      aria-label={`Learn more about ${card.title}`}
                      className="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center shadow-lg hover:scale-110 active:scale-95 transition-transform duration-200"
                    >
                      <Plus className="w-5 h-5 transition-transform duration-300 group-hover:rotate-90" />
                    </button>
                  </div>
                </div>

              </div>
            );
          })}

          {/* ── End Card: Explore All Capabilities CTA ── */}
          <div className="cora-parallax-card w-[280px] sm:w-[320px] h-[480px] sm:h-[530px] rounded-[36px] bg-zinc-950 text-white shrink-0 flex flex-col justify-between p-7 shadow-xl border border-zinc-800 relative overflow-hidden">
            <div className="space-y-2">
              <div className="w-10 h-10 rounded-2xl bg-zinc-800 text-white flex items-center justify-center">
                <Sparkles className="w-5 h-5 text-indigo-400" />
              </div>
              <h3 className="font-display text-2xl font-bold text-white pt-2">
                40+ Built-in Business Workflows
              </h3>
              <p className="text-zinc-400 text-xs leading-relaxed">
                Connect your WhatsApp, bank account, and client rate cards in under 2 minutes.
              </p>
            </div>

            <div className="space-y-3">
              <a
                href="/workspace/login"
                className="w-full inline-flex items-center justify-center gap-2 py-3 px-5 rounded-2xl bg-white text-zinc-950 font-bold text-sm hover:bg-zinc-100 transition-colors shadow-sm"
              >
                <span>Start Free Trial</span>
                <ArrowRight className="w-4 h-4" />
              </a>
              <p className="text-[10px] text-zinc-500 text-center">
                No credit card required &bull; Free forever tier
              </p>
            </div>
          </div>

        </div>
      </div>
    </section>
  );
}
