'use client';

import React, { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { 
  Scissors, 
  Dumbbell, 
  Stethoscope, 
  Utensils, 
  Building2, 
  Briefcase, 
  CheckCircle2, 
  ArrowRight 
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

const USE_CASES = [
  {
    id: 'salons_spas',
    title: 'Salons & Spas',
    desc: 'Manage appointments, repeat client preferences, service menus, and instant UPI billing.',
    badge: 'Appointments & Billing',
    icon: Scissors,
  },
  {
    id: 'gyms_fitness',
    title: 'Gyms & Fitness Studios',
    desc: 'Track active memberships, renewal dates, trainer schedules, and fee collections without spreadsheets.',
    badge: 'Memberships & Fees',
    icon: Dumbbell,
  },
  {
    id: 'dental_clinics',
    title: 'Dental & Health Clinics',
    desc: 'Organize patient appointments, consultation invoices, and automatic follow-up reminders.',
    badge: 'Patient Visits',
    icon: Stethoscope,
  },
  {
    id: 'restaurants_cafes',
    title: 'Restaurants & Cafes',
    desc: 'Track vendor invoices, daily supplier expenses, party bookings, and catering inquiries.',
    badge: 'Expenses & Catering',
    icon: Utensils,
  },
  {
    id: 'real_estate',
    title: 'Real Estate Agencies',
    desc: 'Organize buyer inquiries, property site visits, agreement drafting, and commission tracking.',
    badge: 'Leads & Deals',
    icon: Building2,
  },
  {
    id: 'agencies_solo',
    title: 'Creative Agencies & Solo Founders',
    desc: 'Handle project milestones, client scope approvals, deliverable handoffs, and tax-ready GST accounts.',
    badge: 'Retainers & Invoices',
    icon: Briefcase,
  },
];

export function UseCasesSection() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.usecase-anim-item',
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

  const handleCardClick = (id: string, title: string) => {
    trackEvent('usecase_card_clicked', { usecase_id: id, usecase_title: title });
  };

  return (
    <section
      id="use-cases"
      ref={sectionRef}
      className="py-20 sm:py-28 bg-[#FAFAFA] relative z-10 overflow-hidden border-b border-zinc-200/60"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* Section Header */}
        <div className="text-center max-w-[760px] mx-auto mb-14 sm:mb-18 usecase-anim-item">
          <span className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono mb-2 inline-block">
            WHO THIS IS FOR
          </span>
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-4">
            Built for businesses that deliver real services.
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed">
            Run your appointments, client records, and cash flow from one conversational assistant.
          </p>
        </div>

        {/* 6 Grid Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
          {USE_CASES.map((uc) => {
            const IconComp = uc.icon;
            return (
              <div
                key={uc.id}
                onClick={() => handleCardClick(uc.id, uc.title)}
                className="usecase-anim-item bg-white rounded-3xl p-6 sm:p-7 border border-zinc-200/80 shadow-2xs hover:shadow-md transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1"
              >
                <div>
                  <div className="flex items-center justify-between mb-4">
                    <div className="w-12 h-12 rounded-2xl bg-zinc-100 flex items-center justify-center text-zinc-900 border border-zinc-200/80 group-hover:bg-zinc-950 group-hover:text-white transition-colors">
                      <IconComp className="w-5 h-5" />
                    </div>
                    <span className="text-[10px] font-mono font-bold uppercase px-2.5 py-1 bg-zinc-100 text-zinc-700 rounded-full border border-zinc-200/60">
                      {uc.badge}
                    </span>
                  </div>

                  <h3 className="font-display text-xl font-bold text-zinc-950 mb-2">
                    {uc.title}
                  </h3>

                  <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed">
                    {uc.desc}
                  </p>
                </div>

                <div className="pt-6 mt-4 border-t border-zinc-100 flex items-center justify-between">
                  <span className="text-xs font-semibold text-zinc-900 group-hover:text-zinc-950 inline-flex items-center gap-1.5">
                    <span>Explore workflow</span>
                    <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                  </span>
                </div>
              </div>
            );
          })}
        </div>

        {/* Bottom CTA Box */}
        <div className="mt-12 text-center usecase-anim-item">
          <a
            href="https://app.heycora.in/workspace/login?source=usecases_footer"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-800 px-6 py-3 rounded-xl text-xs sm:text-sm font-semibold shadow-2xs transition-all hover:-translate-y-0.5"
          >
            <span>Start your business workspace for free</span>
            <ArrowRight className="w-4 h-4" />
          </a>
        </div>

      </div>
    </section>
  );
}
