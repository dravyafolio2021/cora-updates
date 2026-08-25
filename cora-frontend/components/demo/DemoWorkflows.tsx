'use client';

import React from 'react';
import Image from 'next/image';
import { ArrowRight, CheckCircle2 } from 'lucide-react';

interface DemoWorkflowsProps {
  onOpenDrawer: () => void;
}

const WORKFLOW_CARDS = [
  {
    role: 'Teams & Operators',
    image: '/images/usecase_solo_creator.jpg',
    tag: 'OPERATIONS & EXECUTION',
    desc: 'Check out how Cora helps your team coordinate daily deliverables, track client milestones, and ensure zero task chaos across active projects.',
    points: [
      'Visual Kanban & project stage tracking',
      'Automated client approval & asset proofing',
      'Instant WhatsApp client & schedule notifications'
    ]
  },
  {
    role: 'Founders & Executives',
    image: '/images/usecase_production_house.jpg',
    tag: 'BUSINESS INTELLIGENCE',
    desc: 'Discover how Cora gives you complete visibility over high-value client pipelines, 18% GST tax splits, and instant cryptographic contract signoffs.',
    points: [
      'Automated 18% GST & TDS ledger reconciliation',
      'Legally binding SHA-256 e-signatures & MSAs',
      'Instant UPI & net-banking milestone payouts'
    ]
  },
  {
    role: 'Managers & Team Leads',
    image: '/images/usecase_commercial_studio.jpg',
    tag: 'RESOURCE ALLOCATION',
    desc: 'See how Cora helps you balance team capacity, automate client scope generation, and eliminate double-booking across client schedules.',
    points: [
      'Conflict-free multi-calendar scheduling',
      'AI-powered discovery quote & proposal generator',
      'Vendor & freelance contractor roster management'
    ]
  }
];

export function DemoWorkflows({ onOpenDrawer }: DemoWorkflowsProps) {
  return (
    <section className="py-16 sm:py-24 bg-white border-b border-zinc-100">
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 space-y-12 sm:space-y-16">
        
        {/* Section Header */}
        <div className="text-center space-y-3 max-w-[680px] mx-auto">
          <span className="text-[11px] sm:text-xs font-mono font-semibold uppercase tracking-widest text-zinc-500 block">
            WORKFLOWS
          </span>
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 tracking-tight leading-[1.12]">
            Experience a day in the life with Cora.
          </h2>
          <p className="text-sm sm:text-base text-zinc-600 font-normal leading-relaxed">
            See how Cora eliminates administrative friction across your entire business—whether you&apos;re an executive founder, project manager, or team operator.
          </p>
        </div>

        {/* 3 Role Workflow Cards Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
          {WORKFLOW_CARDS.map((card, idx) => (
            <div
              key={idx}
              className="group bg-zinc-50/70 hover:bg-white rounded-3xl p-4 sm:p-5 border border-zinc-200/80 hover:border-zinc-300 transition-all duration-300 shadow-2xs hover:shadow-lg flex flex-col justify-between"
            >
              <div className="space-y-4">
                {/* Visual Image */}
                <div className="relative w-full h-[220px] sm:h-[240px] rounded-2xl overflow-hidden bg-zinc-200 border border-zinc-200/60">
                  <Image
                    src={card.image}
                    alt={card.role}
                    fill
                    sizes="(max-width: 768px) 100vw, 380px"
                    className="object-cover group-hover:scale-103 transition-transform duration-500"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-zinc-950/40 via-transparent to-transparent pointer-events-none" />
                  
                  {/* Tag */}
                  <div className="absolute top-3 left-3">
                    <span className="px-2.5 py-1 rounded-lg bg-zinc-950/80 backdrop-blur-md text-white text-[10px] font-mono font-semibold uppercase tracking-wider">
                      {card.tag}
                    </span>
                  </div>
                </div>

                {/* Content */}
                <div className="space-y-2 pt-1">
                  <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
                    {card.role}
                  </h3>
                  <p className="text-xs sm:text-sm text-zinc-600 font-normal leading-relaxed">
                    {card.desc}
                  </p>
                </div>

                {/* Key Bullet Points */}
                <div className="space-y-1.5 pt-2 border-t border-zinc-200/60">
                  {card.points.map((pt, i) => (
                    <div key={i} className="flex items-center gap-2 text-xs text-zinc-700 font-medium">
                      <CheckCircle2 className="w-3.5 h-3.5 text-zinc-950 shrink-0" />
                      <span>{pt}</span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Action */}
              <div className="pt-5 mt-4 border-t border-zinc-200/60">
                <button
                  type="button"
                  onClick={onOpenDrawer}
                  className="w-full py-2.5 rounded-xl border border-zinc-200 bg-white text-zinc-900 text-xs font-semibold hover:bg-zinc-950 hover:text-white transition-all flex items-center justify-center gap-1.5 shadow-2xs group-hover:border-zinc-950"
                >
                  <span>Explore {card.role} Demo</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </button>
              </div>

            </div>
          ))}
        </div>

      </div>
    </section>
  );
}
