'use client';

import React from 'react';
import Link from 'next/link';
import { 
  Users, 
  Lock, 
  Receipt, 
  Calendar, 
  Send, 
  Camera, 
  HardDrive, 
  Radio, 
  Star, 
  FileText, 
  Sparkles, 
  ShieldCheck, 
  Layers, 
  Mail, 
  Smartphone, 
  ArrowRight 
} from 'lucide-react';
import { FeatureModule } from '@/lib/features-data';

interface FeaturePlatformBeginningGridProps {
  feature: FeatureModule;
}

export function FeaturePlatformBeginningGrid({ feature }: FeaturePlatformBeginningGridProps) {
  const modules = [
    {
      slug: 'lead-crm',
      name: 'Lead CRM',
      desc: 'Visual Kanban pipeline & stages',
      icon: Users,
      bg: 'bg-rose-500'
    },
    {
      slug: 'esign-vault',
      name: 'E-Sign Vault',
      desc: 'IT Act 2000 digital contracts',
      icon: Lock,
      bg: 'bg-emerald-600'
    },
    {
      slug: 'gst-invoicing',
      name: '18% GST Invoicing',
      desc: 'SAC 998314 billing & UPI QR',
      icon: Receipt,
      bg: 'bg-amber-500'
    },
    {
      slug: 'master-calendar',
      name: 'Master Calendar',
      desc: 'Multi-location shoot holds',
      icon: Calendar,
      bg: 'bg-blue-600'
    },
    {
      slug: 'crew-dispatch',
      name: 'Crew Dispatch',
      desc: 'Call sheets & gear allocations',
      icon: Send,
      bg: 'bg-indigo-600'
    },
    {
      slug: 'asset-gear',
      name: 'Asset & Gear Hub',
      desc: 'Sony FX6 kit check-ins',
      icon: Camera,
      bg: 'bg-zinc-900'
    },
    {
      slug: 'media-hub',
      name: 'Media Vault',
      desc: '8K RAW footage delivery',
      icon: HardDrive,
      bg: 'bg-purple-600'
    },
    {
      slug: 'voice-to-scope',
      name: 'Voice-to-Scope',
      desc: 'Audio brief transcription',
      icon: Radio,
      bg: 'bg-teal-600'
    },
    {
      slug: 'review-portal',
      name: 'Review Portal',
      desc: '5-star Google review engine',
      icon: Star,
      bg: 'bg-amber-600'
    },
    {
      slug: 'form-builder',
      name: 'Form Builder',
      desc: 'Embeddable lead widgets',
      icon: FileText,
      bg: 'bg-cyan-600'
    },
    {
      slug: 'content-ai',
      name: 'Content AI',
      desc: 'Viral scripts & storyboard copy',
      icon: Sparkles,
      bg: 'bg-violet-600'
    },
    {
      slug: 'rbac-system',
      name: 'RBAC Security',
      desc: 'Role permissions & audit logs',
      icon: ShieldCheck,
      bg: 'bg-emerald-700'
    },
    {
      slug: 'task-board',
      name: 'Task Board',
      desc: 'Post-production milestones',
      icon: Layers,
      bg: 'bg-zinc-800'
    },
    {
      slug: 'email-smtp',
      name: 'Email & SMTP',
      desc: 'Verified custom domain inbox',
      icon: Mail,
      bg: 'bg-blue-500'
    },
    {
      slug: 'pwa-push',
      name: 'Mobile App',
      desc: 'iOS & Android instant alerts',
      icon: Smartphone,
      bg: 'bg-pink-600'
    },
    {
      slug: '',
      name: 'All 20 Modules',
      desc: 'Explore complete studio OS',
      icon: ArrowRight,
      bg: 'bg-zinc-950',
      isExploreAll: true
    }
  ];

  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
      
      {/* Central Headline */}
      <div className="text-center max-w-[800px] mx-auto mb-12 sm:mb-16">
        <span className="text-xs font-mono font-bold uppercase tracking-wider text-indigo-600 mb-2 block">
          THE CORA PLATFORM
        </span>
        <h2 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 mb-4">
          {feature.shortTitle} is just the <span className="text-zinc-400 font-semibold">beginning</span>
        </h2>
        <p className="text-zinc-600 text-sm sm:text-base leading-relaxed max-w-[680px] mx-auto">
          {feature.shortTitle} is the connective tissue of the Cora platform. E-Sign, 18% GST Invoicing, CRM, Call Sheets, and more in a single, converged workspace. Explore everything you unlock when your studio runs in one place.
        </p>
      </div>

      {/* 4x4 Grid of Platform Apps */}
      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
        {modules.map((mod, idx) => {
          const Icon = mod.icon;
          const href = mod.isExploreAll ? '/features' : `/features/${mod.slug}`;
          
          return (
            <Link
              key={idx}
              href={href}
              className="p-4 sm:p-5 rounded-2xl bg-white border border-zinc-200/80 hover:border-zinc-300 hover:shadow-xs transition-all flex items-start gap-3.5 group cursor-pointer"
            >
              <div className={`w-9 h-9 rounded-xl ${mod.bg} text-white flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform`}>
                <Icon className="w-4 h-4" strokeWidth={2} />
              </div>
              <div className="min-w-0 space-y-0.5">
                <h4 className="font-display text-sm font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors truncate">
                  {mod.name}
                </h4>
                <p className="text-[11px] text-zinc-500 truncate font-normal">
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
