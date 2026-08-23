'use client';

import React, { useState } from 'react';
import Image from 'next/image';
import {
  Check,
  ArrowRight,
  Sparkles,
  MessageSquare,
  ShieldCheck,
  Receipt,
  Calendar,
  TrendingUp,
  FileText,
  Users,
  Camera,
  Building,
  Briefcase,
  Layers,
  Palette,
  Clock,
  QrCode,
} from 'lucide-react';

interface WorkflowSolution {
  id: string;
  tabLabel: string;
  headline: string;
  headlineAccent: string;
  description: string;
  replaces: string[];
  features: string[];
  agents: {
    name: string;
    action: string;
    avatar: string;
    badgeColor: string;
    icon: React.ComponentType<{ className?: string }>;
  }[];
  ctaText: string;
  ctaLink: string;
}

const solutionsData: WorkflowSolution[] = [
  {
    id: 'projects',
    tabLabel: 'Photography & Film',
    headline: 'Deliver shoot bookings on time,',
    headlineAccent: 'every time',
    description: 'Get your client inquiries, crew call-sheets, 18% GST invoicing, and deliverables running smoothly with specialized autonomous workflows.',
    replaces: ['HoneyBook', 'Studio Ninja', 'QuickBooks', 'DocuSign'],
    features: [
      'Automate WhatsApp briefs into structured shoot packages',
      'Generate IT Act compliant commercial agreements & model NDAs',
      'Instant 18% GST calculation with dynamic UPI QR standees',
      'Send automated WhatsApp call-time reminders to crew & clients',
    ],
    agents: [
      {
        name: 'Intake Agent',
        action: 'standardizes project kickoff & briefs',
        avatar: '/images/agent_card_pm.jpg',
        badgeColor: 'bg-amber-500',
        icon: Sparkles,
      },
      {
        name: 'Rate Agent',
        action: 'calculates commercial packages & 18% GST splits',
        avatar: '/images/agent_card_gst.jpg',
        badgeColor: 'bg-emerald-500',
        icon: Receipt,
      },
      {
        name: 'Legal Agent',
        action: 'generates model release NDAs & collects e-signatures',
        avatar: '/images/agent_card_legal.jpg',
        badgeColor: 'bg-purple-500',
        icon: ShieldCheck,
      },
      {
        name: 'Ledger Agent',
        action: 'reconciles UPI advances & tracks cash flow',
        avatar: '/images/agent_card_finance.jpg',
        badgeColor: 'bg-indigo-500',
        icon: TrendingUp,
      },
    ],
    ctaText: 'Explore Studio Co-Founder',
    ctaLink: '/use-cases?industry=photography_studio',
  },
  {
    id: 'agencies',
    tabLabel: 'Creative Agencies',
    headline: 'Scale client retainers & approvals',
    headlineAccent: 'without the chaos',
    description: 'Eliminate scope creep and delayed payments. Turn incoming client briefs into signed milestone agreements and automated monthly retainer billing.',
    replaces: ['Notion', 'Asana', 'QuickBooks', 'DocuSign'],
    features: [
      'Auto-draft client scope contracts with 50% upfront deposit terms',
      'Track monthly retainer hours & deliverable milestones in real-time',
      'Automate invoice generation with corporate GSTIN validation',
      'Export CA-ready sales ledger and GSTR-1 summaries in one tap',
    ],
    agents: [
      {
        name: 'Brief Agent',
        action: 'converts messy client messages into clear milestones',
        avatar: '/images/agent_card_sales.jpg',
        badgeColor: 'bg-sky-500',
        icon: MessageSquare,
      },
      {
        name: 'Scope Agent',
        action: 'detects out-of-scope revisions & drafts addendums',
        avatar: '/images/agent_card_legal.jpg',
        badgeColor: 'bg-purple-500',
        icon: ShieldCheck,
      },
      {
        name: 'Billing Agent',
        action: 'dispatches automated GSTR-1 compliant invoices',
        avatar: '/images/agent_card_gst.jpg',
        badgeColor: 'bg-emerald-500',
        icon: Receipt,
      },
      {
        name: 'Executive Agent',
        action: 'reports live monthly revenue & profit margins',
        avatar: '/images/agent_card_finance.jpg',
        badgeColor: 'bg-indigo-500',
        icon: TrendingUp,
      },
    ],
    ctaText: 'Explore Agency Co-Founder',
    ctaLink: '/use-cases?industry=creative_agency',
  },
  {
    id: 'real-estate',
    tabLabel: 'Real Estate & Architecture',
    headline: 'Lock site visits & commercial deals',
    headlineAccent: 'at record speed',
    description: 'From builder inventory walkthroughs to client token advances, coordinate buyer agreements, broker commissions, and property call-sheets on autopilot.',
    replaces: ['HubSpot', 'Calendly', 'WhatsApp Groups', 'Excel'],
    features: [
      'Instant property brochure generation with localized rate math',
      'Standardized brokerage & non-circumvention agreements',
      'Automated site visit scheduling with Google Maps pin sync',
      'Token advance tracking with dynamic UPI soundbox receipts',
    ],
    agents: [
      {
        name: 'Lead Concierge',
        action: 'qualifies buyer budgets & schedules verified site visits',
        avatar: '/images/agent_card_sales.jpg',
        badgeColor: 'bg-sky-500',
        icon: MessageSquare,
      },
      {
        name: 'Contract Agent',
        action: 'drafts builder-buyer & channel partner agreements',
        avatar: '/images/agent_card_legal.jpg',
        badgeColor: 'bg-purple-500',
        icon: ShieldCheck,
      },
      {
        name: 'Tax Agent',
        action: 'calculates TDS on immovable property & GST splits',
        avatar: '/images/agent_card_gst.jpg',
        badgeColor: 'bg-emerald-500',
        icon: Receipt,
      },
      {
        name: 'Commission Agent',
        action: 'tracks broker payouts & transaction milestones',
        avatar: '/images/agent_card_finance.jpg',
        badgeColor: 'bg-indigo-500',
        icon: TrendingUp,
      },
    ],
    ctaText: 'Explore Real Estate Co-Founder',
    ctaLink: '/use-cases?industry=real_estate',
  },
  {
    id: 'production',
    tabLabel: 'Production & Events',
    headline: 'Orchestrate 50+ crew call-sheets',
    headlineAccent: 'in one command',
    description: 'Manage high-stakes event schedules, equipment rental agreements, vendor advances, and multi-location shoot logistics without missing a single detail.',
    replaces: ['Google Sheets', 'Monday.com', 'Razorpay', 'WhatsApp'],
    features: [
      'Generate personalized call-sheets for 50+ crew members in seconds',
      'Manage vendor payouts, GST TDS deductions, and rental holds',
      'Real-time weather & location permit checklist tracking',
      'Automated SMS & WhatsApp call-time broadcasts with read receipts',
    ],
    agents: [
      {
        name: 'Call-Sheet Agent',
        action: 'dispatches custom call-times & location maps to crew',
        avatar: '/images/agent_card_calendar.jpg',
        badgeColor: 'bg-yellow-500',
        icon: Calendar,
      },
      {
        name: 'Vendor Agent',
        action: 'manages equipment release NDAs & security deposits',
        avatar: '/images/agent_card_legal.jpg',
        badgeColor: 'bg-purple-500',
        icon: ShieldCheck,
      },
      {
        name: 'Payout Agent',
        action: 'reconciles vendor invoices & generates instant UPI QR',
        avatar: '/images/agent_card_gst.jpg',
        badgeColor: 'bg-emerald-500',
        icon: Receipt,
      },
      {
        name: 'Budget Agent',
        action: 'tracks day-wise production burn rate in real time',
        avatar: '/images/agent_card_finance.jpg',
        badgeColor: 'bg-indigo-500',
        icon: TrendingUp,
      },
    ],
    ctaText: 'Explore Production Co-Founder',
    ctaLink: '/use-cases?industry=production_events',
  },
  {
    id: 'freelancers',
    tabLabel: 'Solo Creators & Consultants',
    headline: 'Run a 7-figure creative studio',
    headlineAccent: 'as a one-person army',
    description: 'Stop spending half your week on paperwork. Let Cora handle client inquiries, send professional quotes, collect advance payments, and file your GST.',
    replaces: ['7 Subscriptions', '₹15,000/mo in software bills'],
    features: [
      'Professional branded client portal with instant UPI checkout',
      'Pre-built Indian commercial contract templates with advance terms',
      'Auto-remind late-paying clients with gentle WhatsApp follow-ups',
      'Free forever plan with 15 monthly invoices & full AI chat',
    ],
    agents: [
      {
        name: 'Sales Assistant',
        action: 'responds to client inquiries 24/7 with approved rate card',
        avatar: '/images/agent_card_sales.jpg',
        badgeColor: 'bg-sky-500',
        icon: MessageSquare,
      },
      {
        name: 'Agreement Clerk',
        action: 'generates 1-click e-signature links for every new project',
        avatar: '/images/agent_card_legal.jpg',
        badgeColor: 'bg-purple-500',
        icon: ShieldCheck,
      },
      {
        name: 'Invoice Clerk',
        action: 'outputs 18% GST bills & tracks payment receipts',
        avatar: '/images/agent_card_gst.jpg',
        badgeColor: 'bg-emerald-500',
        icon: Receipt,
      },
      {
        name: 'Accounts Clerk',
        action: 'maintains monthly profit, loss, and tax liability',
        avatar: '/images/agent_card_finance.jpg',
        badgeColor: 'bg-indigo-500',
        icon: TrendingUp,
      },
    ],
    ctaText: 'Start Free Workspace',
    ctaLink: '/workspace/login',
  },
];

export function PlatformLifecycleSection() {
  const [activeTab, setActiveTab] = useState<string>('projects');
  const currentSolution = solutionsData.find((s) => s.id === activeTab) || solutionsData[0];

  return (
    <section
      id="how-it-works"
      className="py-20 sm:py-28 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100"
    >
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. Section Header ── */}
        <div className="max-w-[800px] mx-auto text-center mb-10 sm:mb-12">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[52px] font-bold text-zinc-950 leading-[1.08] tracking-[-0.03em] mb-4">
            AI co-founder for every <span className="text-zinc-400 font-semibold">business</span>
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[560px] mx-auto">
            Your key workflows, automated by specialized Cora co-founders.
          </p>
        </div>

        {/* ── 2. Filter Pills / Industry Tabs (ClickUp Style) ── */}
        <div className="flex items-center justify-start sm:justify-center gap-2 sm:gap-2.5 overflow-x-auto pb-4 sm:pb-0 mb-10 sm:mb-14 scrollbar-none select-none">
          {solutionsData.map((item) => {
            const isActive = item.id === activeTab;
            return (
              <button
                key={item.id}
                onClick={() => setActiveTab(item.id)}
                type="button"
                className={`px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm font-semibold tracking-tight whitespace-nowrap transition-all duration-200 cursor-pointer ${
                  isActive
                    ? 'bg-transparent text-sky-600 border-2 border-sky-500 shadow-xs'
                    : 'bg-transparent text-zinc-600 border border-dashed border-zinc-300 hover:text-zinc-950 hover:border-zinc-400'
                }`}
              >
                {item.tabLabel}
              </button>
            );
          })}
        </div>

        {/* ── 3. Main Showcase Card (Matching Reference 1:1) ── */}
        <div className="bg-[#F7F7F8] rounded-[36px] p-6 sm:p-10 lg:p-14 border border-zinc-200/80 shadow-[0px_8px_30px_rgba(0,0,0,0.03)] transition-all duration-300">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
            
            {/* Left Column: Solution Headline, Replaces & Features */}
            <div className="lg:col-span-6 space-y-6">
              <div>
                <h3 className="font-display text-3xl sm:text-4xl lg:text-[42px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em]">
                  {currentSolution.headline} <br />
                  <span className="text-zinc-400 font-semibold">{currentSolution.headlineAccent}</span>
                </h3>
                <p className="text-zinc-600 text-sm sm:text-base leading-relaxed mt-4">
                  {currentSolution.description}
                </p>
              </div>

              {/* Replaces Badges */}
              <div className="flex items-center gap-2 flex-wrap pt-1">
                <span className="text-[11px] font-bold uppercase tracking-wider text-zinc-400">
                  REPLACES
                </span>
                {currentSolution.replaces.map((app, rIdx) => (
                  <span
                    key={rIdx}
                    className="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-zinc-200/80 text-zinc-700 text-xs font-semibold shadow-2xs"
                  >
                    {app}
                  </span>
                ))}
              </div>

              {/* Bullet Checklist */}
              <div className="space-y-3 pt-2">
                {currentSolution.features.map((feat, fIdx) => (
                  <div key={fIdx} className="flex items-start gap-2.5 text-xs sm:text-sm text-zinc-700 font-medium">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>{feat}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Right Column: Stack of 4 Specialized Co-Founders / Agents */}
            <div className="lg:col-span-6 space-y-3.5">
              {currentSolution.agents.map((agent, aIdx) => (
                <div
                  key={aIdx}
                  className="bg-white rounded-2xl p-4 sm:p-4.5 border border-zinc-200/80 shadow-[0px_2px_8px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-zinc-300 transition-all duration-200 flex items-center justify-between gap-3 group cursor-pointer"
                >
                  <div className="flex items-center gap-3 min-w-0">
                    <div className="relative w-10 h-10 rounded-full overflow-hidden shrink-0 border border-zinc-200 shadow-2xs">
                      <Image
                        src={agent.avatar}
                        alt={agent.name}
                        fill
                        className="object-cover"
                      />
                      <div className="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-white flex items-center justify-center">
                        <span className={`w-2 h-2 rounded-full ${agent.badgeColor}`} />
                      </div>
                    </div>

                    <div className="text-xs sm:text-sm truncate">
                      <span className="font-bold text-zinc-950">{agent.name}</span>{' '}
                      <span className="text-zinc-600 font-normal">{agent.action}</span>
                    </div>
                  </div>

                  <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-950 group-hover:translate-x-0.5 transition-all shrink-0" />
                </div>
              ))}

              {/* Bottom Action Button (Explore Solution) */}
              <div className="pt-3">
                <a
                  href={currentSolution.ctaLink}
                  className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-800 px-6 py-3 rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition-all hover:-translate-y-0.5"
                >
                  <span>{currentSolution.ctaText}</span>
                  <ArrowRight className="w-4 h-4" />
                </a>
              </div>
            </div>

          </div>
        </div>

      </div>
    </section>
  );
}
