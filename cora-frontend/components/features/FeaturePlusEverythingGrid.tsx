'use client';

import React from 'react';
import { 
  SlidersHorizontal, 
  Eye, 
  Zap, 
  GitBranch, 
  ListChecks, 
  Users, 
  Repeat, 
  MessageSquare, 
  Clock 
} from 'lucide-react';
import { FeatureModule } from '@/lib/features-data';

interface FeaturePlusEverythingGridProps {
  feature: FeatureModule;
}

export function FeaturePlusEverythingGrid({ feature }: FeaturePlusEverythingGridProps) {
  const items = [
    {
      icon: SlidersHorizontal,
      title: 'Custom fields',
      description: 'Add rate tiers, call times, location GPS, gear kits, shoot genres, or client budget caps.'
    },
    {
      icon: Eye,
      title: 'Multiple views',
      description: 'Switch seamlessly between Kanban CRM, Master Shoot Calendar, Gantt timelines, and Grid tables.'
    },
    {
      icon: Zap,
      title: 'Automations',
      description: 'Trigger auto-generated 18% GST invoices, WhatsApp alerts, and calendar holds when milestones change.'
    },
    {
      icon: GitBranch,
      title: 'Dependencies',
      description: 'Map workflow blockers and automatically lock equipment kits only after advance contracts are e-signed.'
    },
    {
      icon: ListChecks,
      title: 'Subtasks & Checklists',
      description: 'Break complex commercial productions into trackable prep, call sheets, and post-production steps.'
    },
    {
      icon: Users,
      title: 'Multiple Assignees',
      description: 'Assign DOPs, sound engineers, colorists, and executive producers simultaneously to single shoots.'
    },
    {
      icon: Repeat,
      title: 'Recurring Workflows',
      description: 'Set monthly client retainer shoot packages that auto-generate fresh scopes and invoices on schedule.'
    },
    {
      icon: MessageSquare,
      title: 'Comments & Proofing',
      description: 'Discuss frame-accurate revisions and client feedback in direct context with team mentions.'
    },
    {
      icon: Clock,
      title: 'Time & Call Tracking',
      description: 'Track on-set studio hours, overtime rates, and crew shifts with integrated shoot timers and estimates.'
    }
  ];

  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
      
      {/* Central Headline */}
      <div className="text-center max-w-[760px] mx-auto mb-12 sm:mb-16">
        <h2 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950">
          Plus, everything you need to get it <span className="text-zinc-400 font-semibold">done</span>
        </h2>
      </div>

      {/* 3x3 Feature Utilities Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        {items.map((item, idx) => {
          const Icon = item.icon;
          return (
            <div 
              key={idx}
              className="p-6 rounded-2xl bg-white border border-zinc-200/80 hover:border-zinc-300 hover:shadow-xs transition-all flex items-start gap-4 group"
            >
              <div className="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-200/80 flex items-center justify-center text-zinc-700 group-hover:text-zinc-950 group-hover:bg-zinc-100 transition-colors shrink-0">
                <Icon className="w-5 h-5" strokeWidth={1.8} />
              </div>
              <div className="space-y-1">
                <h3 className="font-display text-base font-bold text-zinc-950 group-hover:text-zinc-800 transition-colors">
                  {item.title}
                </h3>
                <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed">
                  {item.description}
                </p>
              </div>
            </div>
          );
        })}
      </div>

    </section>
  );
}
