'use client';

import React from 'react';
import Link from 'next/link';
import { FeatureModule } from '@/lib/features-data';
import {
  LeadCrmColorIcon,
  EsignVaultColorIcon,
  GstInvoicingColorIcon,
  MasterCalendarColorIcon,
  CrewDispatchColorIcon,
  AssetGearColorIcon,
  MediaHubColorIcon,
  VoiceScopeColorIcon,
  ReviewPortalColorIcon,
  FormBuilderColorIcon,
  ContentAiColorIcon,
  RbacSecurityColorIcon,
  TaskBoardColorIcon,
  EmailSmtpColorIcon,
  PwaPushColorIcon,
  AllModulesColorIcon
} from '@/components/features/ModuleAppGlyphs';

interface FeaturePlatformBeginningGridProps {
  feature: FeatureModule;
}

export function FeaturePlatformBeginningGrid({ feature }: FeaturePlatformBeginningGridProps) {
  const modules = [
    {
      slug: 'lead-crm',
      name: 'Lead CRM',
      desc: 'Visual Kanban pipeline & stages',
      Icon: LeadCrmColorIcon
    },
    {
      slug: 'esign-vault',
      name: 'E-Sign Vault',
      desc: 'IT Act 2000 digital contracts',
      Icon: EsignVaultColorIcon
    },
    {
      slug: 'gst-invoicing',
      name: '18% GST Invoicing',
      desc: 'SAC 9983 billing & UPI QR',
      Icon: GstInvoicingColorIcon
    },
    {
      slug: 'master-calendar',
      name: 'Master Calendar',
      desc: 'Client shoots & sprint holds',
      Icon: MasterCalendarColorIcon
    },
    {
      slug: 'crew-dispatch',
      name: 'Crew Dispatch',
      desc: 'Call sheets & team dispatch',
      Icon: CrewDispatchColorIcon
    },
    {
      slug: 'asset-gear',
      name: 'Asset & Gear Hub',
      desc: 'Hardware & inventory tracking',
      Icon: AssetGearColorIcon
    },
    {
      slug: 'media-hub',
      name: 'Media Vault',
      desc: '4K watermark client galleries',
      Icon: MediaHubColorIcon
    },
    {
      slug: 'voice-to-scope',
      name: 'Voice-to-Scope',
      desc: 'Voice notes to signed scopes',
      Icon: VoiceScopeColorIcon
    },
    {
      slug: 'review-portal',
      name: 'Review Portal',
      desc: '5-star Google review engine',
      Icon: ReviewPortalColorIcon
    },
    {
      slug: 'form-builder',
      name: 'Form Builder',
      desc: 'Embeddable client intake forms',
      Icon: FormBuilderColorIcon
    },
    {
      slug: 'content-ai',
      name: 'Content AI',
      desc: 'Viral scripts & SEO content',
      Icon: ContentAiColorIcon
    },
    {
      slug: 'rbac-system',
      name: 'RBAC Security',
      desc: 'Role permissions & audit logs',
      Icon: RbacSecurityColorIcon
    },
    {
      slug: 'task-board',
      name: 'Task Board',
      desc: 'Sprint milestones & proofing',
      Icon: TaskBoardColorIcon
    },
    {
      slug: 'email-smtp',
      name: 'Email & SMTP',
      desc: 'Custom domain proposal inbox',
      Icon: EmailSmtpColorIcon
    },
    {
      slug: 'pwa-push',
      name: 'Mobile App',
      desc: 'iOS & Android push alerts',
      Icon: PwaPushColorIcon
    },
    {
      slug: '',
      name: 'All 20 Modules',
      desc: 'Explore complete agency OS',
      Icon: AllModulesColorIcon,
      isExploreAll: true
    }
  ];

  return (
    <section className="w-full max-w-[1280px] mx-auto px-4 sm:px-6 mb-16 sm:mb-[100px]">
      
      {/* Central Headline */}
      <div className="text-center max-w-[800px] mx-auto mb-12 sm:mb-16">
        <span className="text-xs font-mono font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/60 inline-block mb-3">
          THE CORA PLATFORM
        </span>
        <div>
          <h2 className="font-display text-3xl sm:text-5xl font-bold tracking-tight bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block mb-4">
            {feature.shortTitle} is just the beginning
          </h2>
        </div>
        <p className="text-zinc-600 text-sm sm:text-base leading-relaxed max-w-[680px] mx-auto">
          {feature.shortTitle} integrates natively across the entire Cora platform. E-Sign, 18% GST Invoicing, CRM, Call Sheets, and client vaults unite in a single, high-throughput workspace.
        </p>
      </div>

      {/* 4x4 Grid of Platform Apps with Frosted Color Squircle Icons */}
      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-4.5">
        {modules.map((mod, idx) => {
          const Icon = mod.Icon;
          const href = mod.isExploreAll ? '/features' : `/features/${mod.slug}`;
          
          return (
            <Link
              key={idx}
              href={href}
              className="p-3.5 sm:p-4.5 rounded-2xl bg-white border border-zinc-200/80 hover:border-zinc-300 hover:shadow-[0_8px_24px_rgba(0,0,0,0.06)] hover:-translate-y-0.5 transition-all flex items-center gap-3.5 group cursor-pointer"
            >
              <Icon className="w-11 h-11 sm:w-12 sm:h-12 shrink-0" />
              <div className="min-w-0 space-y-0.5">
                <h4 className="font-display text-sm sm:text-[14.5px] font-bold text-zinc-900 group-hover:text-black transition-colors truncate">
                  {mod.name}
                </h4>
                <p className="text-[11px] sm:text-xs text-zinc-500 truncate font-normal group-hover:text-zinc-700 transition-colors">
                  {mod.desc}
                </p>
              </div>
            </Link>
          );
        })}
      </div>

    </section>
  );
}
