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
  QrCode,
  Lock,
  Zap,
} from 'lucide-react';

interface AgentDetail {
  id: string;
  name: string;
  role: string;
  avatar: string;
  badgeColor: string;
  tag: string;
  previewTitle: string;
  previewBadge: string;
  previewContent: React.ReactNode;
}

interface WorkflowSolution {
  id: string;
  tabLabel: string;
  headline: string;
  headlineAccent: string;
  replaces: string[];
  agents: AgentDetail[];
  ctaText: string;
  ctaLink: string;
}

const solutionsData: WorkflowSolution[] = [
  {
    id: 'photography',
    tabLabel: 'Photography & Film',
    headline: 'Deliver shoot bookings on time,',
    headlineAccent: 'every time',
    replaces: ['HoneyBook', 'Studio Ninja', 'QuickBooks', 'DocuSign'],
    ctaText: 'Explore Studio Co-Founder',
    ctaLink: '/use-cases?industry=photography_studio',
    agents: [
      {
        id: 'intake',
        name: 'Intake Co-Founder',
        role: 'Standardizes briefs & locks hold dates',
        avatar: '/images/agent_card_pm.jpg',
        badgeColor: 'bg-amber-500',
        tag: 'WhatsApp Sync',
        previewTitle: 'Automated Brief Intake',
        previewBadge: 'Hold Oct 24-25',
        previewContent: (
          <div className="space-y-2.5 text-xs">
            <div className="p-3 bg-zinc-50 rounded-xl space-y-1 border border-zinc-100">
              <div className="text-[10px] text-zinc-400 font-semibold">Incoming WhatsApp Brief</div>
              <div className="font-semibold text-zinc-900">&ldquo;2-Day Commercial Shoot in Mumbai (4K RAW)&rdquo;</div>
            </div>
            <div className="p-2.5 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-between text-emerald-950 font-medium text-[11.5px]">
              <span>Approved Commercial Package</span>
              <span className="font-mono font-bold text-emerald-800">₹1,20,000 + 18% GST</span>
            </div>
          </div>
        ),
      },
      {
        id: 'rate',
        name: 'Rate & Tax Co-Founder',
        role: '18% GST split & dynamic UPI QR',
        avatar: '/images/agent_card_gst.jpg',
        badgeColor: 'bg-emerald-500',
        tag: '18% GST Split',
        previewTitle: '18% Tax Calculation & UPI QR',
        previewBadge: 'GSTR-1 Valid',
        previewContent: (
          <div className="grid grid-cols-2 gap-2.5 text-xs">
            <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100 space-y-1">
              <div className="text-[10px] text-zinc-400 font-semibold">Tax Math</div>
              <div className="font-mono text-zinc-900 text-xs">CGST 9%: ₹10,800<br/>SGST 9%: ₹10,800</div>
            </div>
            <div className="p-3 bg-emerald-50 rounded-xl border border-emerald-100 space-y-1">
              <div className="text-[10px] text-emerald-700 font-semibold">Dynamic UPI Standee</div>
              <div className="font-mono font-bold text-zinc-950 text-sm">₹1,41,600</div>
            </div>
          </div>
        ),
      },
      {
        id: 'legal',
        name: 'Legal Co-Founder',
        role: 'Commercial NDAs & SHA-256 E-Sign',
        avatar: '/images/agent_card_legal.jpg',
        badgeColor: 'bg-purple-500',
        tag: 'SHA-256 Sign',
        previewTitle: 'Commercial Rights & NDA Agreement',
        previewBadge: 'IT Act Valid',
        previewContent: (
          <div className="p-3 bg-purple-50/70 border border-purple-100 rounded-xl space-y-2 text-xs">
            <div className="flex items-center justify-between">
              <span className="font-bold text-purple-950">50% Advance &amp; Cancellation Clause</span>
              <span className="text-[10px] bg-white text-purple-700 font-bold px-2 py-0.5 rounded shadow-2xs">Locked</span>
            </div>
            <div className="font-mono text-[10px] text-zinc-500 truncate">SHA-256: 7f8a9b1c2d3e4f5a...</div>
          </div>
        ),
      },
      {
        id: 'ledger',
        name: 'Ledger Co-Founder',
        role: 'Reconciles UPI & CA export',
        avatar: '/images/agent_card_finance.jpg',
        badgeColor: 'bg-indigo-500',
        tag: 'CA Export',
        previewTitle: 'Live Operating Cash Flow',
        previewBadge: 'Tally Ready',
        previewContent: (
          <div className="space-y-2 text-xs">
            <div className="p-2.5 bg-indigo-50/70 rounded-xl flex items-center justify-between border border-indigo-100">
              <span className="text-zinc-700">Collected Advance:</span>
              <span className="font-mono font-bold text-indigo-950 text-sm">₹70,800 (UPI)</span>
            </div>
            <div className="flex items-center justify-between text-[11px] text-zinc-500 px-1">
              <span>Pending at Delivery: ₹70,800</span>
              <span className="text-emerald-700 font-bold">✓ GSTR-1 Logged</span>
            </div>
          </div>
        ),
      },
    ],
  },
  {
    id: 'agencies',
    tabLabel: 'Creative Agencies',
    headline: 'Scale client retainers & approvals',
    headlineAccent: 'without scope creep',
    replaces: ['Notion', 'Asana', 'QuickBooks', 'DocuSign'],
    ctaText: 'Explore Agency Co-Founder',
    ctaLink: '/use-cases?industry=creative_agency',
    agents: [
      {
        id: 'agency-brief',
        name: 'Brief Co-Founder',
        role: 'Transforms client DMs into milestones',
        avatar: '/images/agent_card_sales.jpg',
        badgeColor: 'bg-sky-500',
        tag: 'Scope Guard',
        previewTitle: 'Monthly Retainer Deliverables',
        previewBadge: '4 Milestones',
        previewContent: (
          <div className="space-y-2 text-xs">
            <div className="p-2.5 bg-zinc-50 rounded-xl flex items-center justify-between border border-zinc-100">
              <span className="font-semibold text-zinc-900">Sprint 1: Brand Guidelines</span>
              <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Approved</span>
            </div>
            <div className="p-2.5 bg-sky-50 border border-sky-100 rounded-xl flex items-center justify-between">
              <span className="text-sky-950 font-semibold">Sprint 2: 3D Landing Page</span>
              <span className="text-[10px] font-bold text-sky-800 bg-white px-2 py-0.5 rounded">In Progress</span>
            </div>
          </div>
        ),
      },
      {
        id: 'agency-billing',
        name: 'Billing Co-Founder',
        role: 'GSTR-1 compliant corporate invoices',
        avatar: '/images/agent_card_gst.jpg',
        badgeColor: 'bg-emerald-500',
        tag: 'Auto-Invoice',
        previewTitle: 'GSTIN Verified Corporate Bill',
        previewBadge: '₹2,50,000/mo',
        previewContent: (
          <div className="p-3 bg-emerald-50/70 border border-emerald-100 rounded-xl space-y-1.5 text-xs">
            <div className="flex items-center justify-between">
              <span className="font-bold text-emerald-950">GSTIN: 27AAAAA0000A1Z5</span>
              <span className="text-[10px] font-bold text-emerald-700 bg-white px-2 py-0.5 rounded">Verified</span>
            </div>
            <div className="font-mono text-zinc-900 font-bold">Total: ₹2,95,000 (Incl 18% GST)</div>
          </div>
        ),
      },
      {
        id: 'agency-scope',
        name: 'Scope Co-Founder',
        role: 'Detects out-of-scope revisions',
        avatar: '/images/agent_card_legal.jpg',
        badgeColor: 'bg-purple-500',
        tag: 'Addendum Auto',
        previewTitle: 'Scope Creep Protection',
        previewBadge: 'Auto-Quote',
        previewContent: (
          <div className="p-3 bg-purple-50 border border-purple-100 rounded-xl space-y-1 text-xs">
            <div className="font-bold text-purple-950">+3 Extra Video Revisions Requested</div>
            <div className="text-[11px] text-purple-800">Addendum generated: ₹15,000 + GST</div>
          </div>
        ),
      },
      {
        id: 'agency-exec',
        name: 'Executive Co-Founder',
        role: 'Live agency profit & cash flow',
        avatar: '/images/agent_card_finance.jpg',
        badgeColor: 'bg-indigo-500',
        tag: 'Live Profit',
        previewTitle: 'Monthly Agency Net Margin',
        previewBadge: '48% Margin',
        previewContent: (
          <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100 flex items-center justify-between text-xs">
            <div>
              <div className="text-[10px] text-zinc-400">Retainer Revenue</div>
              <div className="font-mono font-bold text-zinc-950 text-sm">₹12,40,000</div>
            </div>
            <div className="text-right">
              <div className="text-[10px] text-zinc-400">Net Profit</div>
              <div className="font-mono font-bold text-emerald-700 text-sm">₹5,95,200</div>
            </div>
          </div>
        ),
      },
    ],
  },
  {
    id: 'real-estate',
    tabLabel: 'Real Estate & Architecture',
    headline: 'Lock site visits & property deals',
    headlineAccent: 'at record speed',
    replaces: ['HubSpot', 'Calendly', 'WhatsApp Groups', 'Excel'],
    ctaText: 'Explore Real Estate Co-Founder',
    ctaLink: '/use-cases?industry=real_estate',
    agents: [
      {
        id: 're-leads',
        name: 'Lead Concierge',
        role: 'Qualifies buyer budgets & site visits',
        avatar: '/images/agent_card_sales.jpg',
        badgeColor: 'bg-sky-500',
        tag: 'Google Maps Pin',
        previewTitle: 'Verified Buyer Site Visit',
        previewBadge: 'Luxury Villa Hold',
        previewContent: (
          <div className="p-3 bg-sky-50/70 border border-sky-100 rounded-xl space-y-1.5 text-xs">
            <div className="font-bold text-sky-950">4BHK Penthouse Walkthrough &bull; Worli</div>
            <div className="text-[11px] text-zinc-600">Google Maps pin &amp; broker token dispatched</div>
          </div>
        ),
      },
      {
        id: 're-contract',
        name: 'Contract Co-Founder',
        role: 'Builder-buyer & channel partner NDAs',
        avatar: '/images/agent_card_legal.jpg',
        badgeColor: 'bg-purple-500',
        tag: 'Channel NDA',
        previewTitle: 'Non-Circumvention Broker Agreement',
        previewBadge: '2% Commission',
        previewContent: (
          <div className="p-3 bg-purple-50 border border-purple-100 rounded-xl space-y-1 text-xs">
            <div className="font-bold text-purple-950">Brokerage Terms Signed</div>
            <div className="text-[10px] text-purple-700">Token deposit escrow verified</div>
          </div>
        ),
      },
      {
        id: 're-tax',
        name: 'Tax Co-Founder',
        role: '1% TDS & GST property math',
        avatar: '/images/agent_card_gst.jpg',
        badgeColor: 'bg-emerald-500',
        tag: 'TDS Auto',
        previewTitle: 'Section 194-IA Property TDS',
        previewBadge: 'Compliant',
        previewContent: (
          <div className="p-3 bg-emerald-50 border border-emerald-100 rounded-xl space-y-1 text-xs">
            <div className="font-bold text-emerald-950">TDS 1%: ₹45,000 Deducted</div>
            <div className="text-[10px] text-emerald-700">Challan 26QB auto-populated</div>
          </div>
        ),
      },
      {
        id: 're-comm',
        name: 'Payout Co-Founder',
        role: 'Tracks channel partner commissions',
        avatar: '/images/agent_card_finance.jpg',
        badgeColor: 'bg-indigo-500',
        tag: 'Instant UPI',
        previewTitle: 'Broker Commission Ledger',
        previewBadge: '₹1,80,000 Settled',
        previewContent: (
          <div className="p-3 bg-zinc-50 border border-zinc-100 rounded-xl flex items-center justify-between text-xs">
            <span className="font-semibold text-zinc-900">Partner Payout:</span>
            <span className="font-mono font-bold text-indigo-950">₹1,80,000 UPI</span>
          </div>
        ),
      },
    ],
  },
  {
    id: 'production',
    tabLabel: 'Production & Events',
    headline: 'Orchestrate 50+ crew call-sheets',
    headlineAccent: 'in one command',
    replaces: ['Google Sheets', 'Monday.com', 'Razorpay', 'WhatsApp'],
    ctaText: 'Explore Production Co-Founder',
    ctaLink: '/use-cases?industry=production_events',
    agents: [
      {
        id: 'prod-call',
        name: 'Call-Sheet Co-Founder',
        role: 'Dispatches custom call-times & maps',
        avatar: '/images/agent_card_calendar.jpg',
        badgeColor: 'bg-yellow-500',
        tag: 'SMS / WhatsApp',
        previewTitle: 'Production Day 1 Call-Sheet',
        previewBadge: '52 Crew Broadcast',
        previewContent: (
          <div className="p-3 bg-amber-50/70 border border-amber-100 rounded-xl space-y-1.5 text-xs">
            <div className="font-bold text-amber-950">Call-Time: 06:30 AM &bull; Mehboob Studio 2</div>
            <div className="text-[10px] text-amber-800 font-semibold">✓ 49/52 Read Receipts Confirmed</div>
          </div>
        ),
      },
      {
        id: 'prod-vendor',
        name: 'Vendor Co-Founder',
        role: 'Gear damage waivers & deposits',
        avatar: '/images/agent_card_legal.jpg',
        badgeColor: 'bg-purple-500',
        tag: 'ARRI Hold',
        previewTitle: 'Camera Equipment Release NDA',
        previewBadge: 'Deposit Held',
        previewContent: (
          <div className="p-3 bg-purple-50 border border-purple-100 rounded-xl space-y-1 text-xs">
            <div className="font-bold text-purple-950">ARRI Alexa Mini LF + Master Anamorphic</div>
            <div className="text-[10px] text-purple-700">Security waiver e-signed</div>
          </div>
        ),
      },
      {
        id: 'prod-payout',
        name: 'Payout Co-Founder',
        role: 'Vendor invoices & instant UPI QR',
        avatar: '/images/agent_card_gst.jpg',
        badgeColor: 'bg-emerald-500',
        tag: '1-Click UPI',
        previewTitle: 'Crew & Vendor Payout Ledger',
        previewBadge: 'Settled',
        previewContent: (
          <div className="p-3 bg-emerald-50 border border-emerald-100 rounded-xl space-y-1 text-xs">
            <div className="font-bold text-emerald-950">Gaffer + Lights Vendor: ₹85,000</div>
            <div className="text-[10px] text-emerald-700">TDS 2% deducted &bull; UPI QR generated</div>
          </div>
        ),
      },
      {
        id: 'prod-budget',
        name: 'Budget Co-Founder',
        role: 'Live production burn rate',
        avatar: '/images/agent_card_finance.jpg',
        badgeColor: 'bg-indigo-500',
        tag: 'Real-Time Burn',
        previewTitle: 'Day-Wise Production Budget',
        previewBadge: '92% On Budget',
        previewContent: (
          <div className="p-3 bg-zinc-50 border border-zinc-100 rounded-xl flex items-center justify-between text-xs">
            <span className="font-semibold text-zinc-900">Total Production Burn:</span>
            <span className="font-mono font-bold text-zinc-950">₹8,45,000 / ₹9,00,000</span>
          </div>
        ),
      },
    ],
  },
];

export function PlatformLifecycleSection() {
  const [activeTab, setActiveTab] = useState<string>('photography');
  const currentSolution = solutionsData.find((s) => s.id === activeTab) || solutionsData[0];
  const [selectedAgentIndex, setSelectedAgentIndex] = useState<number>(0);
  const activeAgent = currentSolution.agents[selectedAgentIndex] || currentSolution.agents[0];

  const handleTabChange = (tabId: string) => {
    setActiveTab(tabId);
    setSelectedAgentIndex(0);
  };

  return (
    <section
      id="how-it-works"
      className="py-14 sm:py-20 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. Section Header ── */}
        <div className="max-w-[760px] mx-auto text-center mb-8 sm:mb-10">
          <h2 className="font-display text-2xl xs:text-3xl sm:text-4xl lg:text-[44px] font-bold leading-[1.1] tracking-[-0.03em] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block mb-2.5">
            AI co-founder for every business
          </h2>
          <p className="text-zinc-600 text-xs sm:text-sm font-normal leading-relaxed max-w-[500px] mx-auto">
            Click any autonomous co-founder to preview live operational output.
          </p>
        </div>

        {/* ── 2. Filter Pills / Industry Tabs (ClickUp Style) ── */}
        <div className="flex items-center justify-start sm:justify-center gap-2 overflow-x-auto pb-3 sm:pb-0 mb-8 sm:mb-10 scrollbar-none select-none">
          {solutionsData.map((item) => {
            const isActive = item.id === activeTab;
            return (
              <button
                key={item.id}
                onClick={() => handleTabChange(item.id)}
                type="button"
                className={`px-4 py-1.5 rounded-full text-xs sm:text-[13px] font-semibold tracking-tight whitespace-nowrap transition-all duration-200 cursor-pointer ${
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

        {/* ── 3. Interactive Visual Showcase Container ── */}
        <div className="bg-[#F8F8F9] rounded-[32px] p-6 sm:p-8 lg:p-10 border border-zinc-200/80 shadow-[0px_6px_24px_rgba(0,0,0,0.03)] transition-all duration-300">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
            
            {/* Left Column: Headline + Live Interactive Artifact UI Card */}
            <div className="lg:col-span-6 space-y-4">
              <div>
                <h3 className="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold leading-[1.14] tracking-[-0.03em] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block">
                  {currentSolution.headline} {currentSolution.headlineAccent}
                </h3>
              </div>

              {/* Replaces Badges */}
              <div className="flex items-center gap-1.5 flex-wrap">
                <span className="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mr-1">
                  REPLACES
                </span>
                {currentSolution.replaces.map((app, rIdx) => (
                  <span
                    key={rIdx}
                    className="px-2 py-0.5 rounded-md bg-white border border-zinc-200/80 text-zinc-700 text-[11px] font-semibold shadow-2xs"
                  >
                    {app}
                  </span>
                ))}
              </div>

              {/* Live Interactive Preview Card for Selected Agent */}
              <div className="bg-white rounded-2xl p-5 border border-zinc-200/90 shadow-[0px_4px_16px_rgba(0,0,0,0.04)] space-y-3 relative overflow-hidden transition-all duration-300">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                  <div className="flex items-center gap-2">
                    <div className={`w-2 h-2 rounded-full ${activeAgent.badgeColor} animate-pulse`} />
                    <span className="text-xs font-bold text-zinc-900">{activeAgent.previewTitle}</span>
                  </div>
                  <span className="text-[10px] font-bold text-zinc-700 bg-zinc-100 px-2 py-0.5 rounded-full">
                    {activeAgent.previewBadge}
                  </span>
                </div>

                {/* Render Selected Agent Live Visual Content */}
                {activeAgent.previewContent}

                <div className="pt-1 flex items-center justify-between text-[11px] font-semibold text-zinc-400">
                  <span>AI Generated in 0.8s</span>
                  <span className="text-zinc-900 font-bold flex items-center gap-1">
                    Auto-Synced &rarr;
                  </span>
                </div>
              </div>
            </div>

            {/* Right Column: Stack of 4 Clickable Interactive Co-Founders */}
            <div className="lg:col-span-6 space-y-2.5">
              {currentSolution.agents.map((agent, aIdx) => {
                const isSelected = aIdx === selectedAgentIndex;
                return (
                  <div
                    key={agent.id}
                    onClick={() => setSelectedAgentIndex(aIdx)}
                    className={`rounded-2xl p-3.5 sm:p-4 border transition-all duration-200 flex items-center justify-between gap-3 group cursor-pointer ${
                      isSelected
                        ? 'bg-white border-zinc-950 shadow-md ring-1 ring-zinc-950'
                        : 'bg-white/80 hover:bg-white border-zinc-200/80 hover:border-zinc-300 shadow-2xs'
                    }`}
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

                      <div className="text-xs sm:text-[13px] truncate">
                        <div className="font-bold text-zinc-950 flex items-center gap-1.5">
                          <span>{agent.name}</span>
                          <span className="text-[10px] font-semibold text-zinc-500 bg-zinc-100 px-1.5 py-0.2 rounded">
                            {agent.tag}
                          </span>
                        </div>
                        <div className="text-zinc-500 font-normal truncate mt-0.5">
                          {agent.role}
                        </div>
                      </div>
                    </div>

                    <ArrowRight className={`w-4 h-4 transition-all shrink-0 ${
                      isSelected ? 'text-zinc-950 translate-x-1' : 'text-zinc-300 group-hover:text-zinc-700'
                    }`} />
                  </div>
                );
              })}

              {/* Bottom Action Button */}
              <div className="pt-2">
                <a
                  href={currentSolution.ctaLink}
                  className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-800 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition-all hover:-translate-y-0.5"
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
