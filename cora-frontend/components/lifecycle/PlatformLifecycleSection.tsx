'use client';

import React, { useState, useEffect, useCallback } from 'react';
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
import {
  SiNotion,
  SiAsana,
  SiQuickbooks,
  SiJira,
  SiLinear,
  SiStripe,
  SiRazorpay,
  SiLooker,
  SiWhatsapp,
  SiGoogledrive,
} from 'react-icons/si';
import { TbBrandMonday } from 'react-icons/tb';

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
    id: 'creative',
    tabLabel: 'Creative & Design Studios',
    headline: 'Lock retainer milestones & scope',
    headlineAccent: 'without unpaid revisions',
    replaces: ['Notion', 'Asana', 'DocuSign', 'QuickBooks'],
    ctaText: 'Explore Creative Agency Workflows',
    ctaLink: '/use-cases/software-agencies',
    agents: [
      {
        id: 'agency-brief',
        name: 'Brief & Sprint Co-Founder',
        role: 'Converts WhatsApp client requests into structured sprint milestones',
        avatar: '/images/cora_agent_creative.jpg',
        badgeColor: 'bg-sky-500',
        tag: 'Scope Guard',
        previewTitle: 'Active Monthly Retainer Sprint',
        previewBadge: 'Sprint 2 of 4',
        previewContent: (
          <div className="space-y-2 text-xs">
            <div className="p-2.5 bg-zinc-50 rounded-xl flex items-center justify-between border border-zinc-100">
              <span className="font-semibold text-zinc-900">Sprint 1: Brand System & Figma Tokens</span>
              <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Approved</span>
            </div>
            <div className="p-2.5 bg-sky-50 border border-sky-100 rounded-xl flex items-center justify-between">
              <span className="text-sky-950 font-semibold">Sprint 2: 3D Landing Page & Lottie</span>
              <span className="text-[10px] font-bold text-sky-800 bg-white px-2 py-0.5 rounded">In Progress</span>
            </div>
          </div>
        ),
      },
      {
        id: 'agency-billing',
        name: 'Billing & GST Co-Founder',
        role: 'Generates 18% GST corporate invoices with instant UPI dynamic QR',
        avatar: '/images/cora_agent_finance.jpg',
        badgeColor: 'bg-emerald-500',
        tag: 'GSTR-1 Auto',
        previewTitle: 'GSTIN Verified Corporate Retainer',
        previewBadge: '₹2,50,000/mo',
        previewContent: (
          <div className="p-3 bg-emerald-50/70 border border-emerald-100 rounded-xl space-y-1.5 text-xs">
            <div className="flex items-center justify-between">
              <span className="font-bold text-emerald-950">GSTIN: 27AAECR2026A1Z8</span>
              <span className="text-[10px] font-bold text-emerald-700 bg-white px-2 py-0.5 rounded">Verified</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-zinc-600 text-[11px]">Base: ₹2,50,000 + 18% GST (₹45,000)</span>
              <span className="font-mono text-zinc-950 font-bold">Total: ₹2,95,000</span>
            </div>
          </div>
        ),
      },
      {
        id: 'agency-scope',
        name: 'Scope & Addendum Co-Founder',
        role: 'Detects out-of-scope revisions and auto-bills paid change orders',
        avatar: '/images/cora_agent_contracts.jpg',
        badgeColor: 'bg-purple-500',
        tag: 'Scope Intercept',
        previewTitle: 'Scope Creep Interception',
        previewBadge: 'Addendum #04',
        previewContent: (
          <div className="p-3 bg-purple-50 border border-purple-100 rounded-xl space-y-1 text-xs">
            <div className="font-bold text-purple-950">+4 Extra 3D Scene Variations Requested</div>
            <div className="text-[11px] text-purple-800">Auto-generated Addendum: ₹28,000 + 18% GST signed</div>
          </div>
        ),
      },
      {
        id: 'agency-exec',
        name: 'Margin & P&L Co-Founder',
        role: 'Tracks real-time contractor costs, retainers, and net agency margin',
        avatar: '/images/cora_agent_delivery.jpg',
        badgeColor: 'bg-indigo-500',
        tag: 'Live Margin',
        previewTitle: 'Monthly Agency Retainer P&L',
        previewBadge: '54% Net Margin',
        previewContent: (
          <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100 flex items-center justify-between text-xs">
            <div>
              <div className="text-[10px] text-zinc-400 font-semibold uppercase">Retainer Revenue</div>
              <div className="font-mono font-bold text-zinc-950 text-sm">₹14,50,000</div>
            </div>
            <div className="text-right">
              <div className="text-[10px] text-zinc-400 font-semibold uppercase">Net Agency Profit</div>
              <div className="font-mono font-bold text-emerald-700 text-sm">₹7,83,000</div>
            </div>
          </div>
        ),
      },
    ],
  },
  {
    id: 'performance',
    tabLabel: 'Performance & Media Agencies',
    headline: 'Scale client ad spend & ROAS',
    headlineAccent: 'with zero reporting overhead',
    replaces: ['Looker Studio', 'Supermetrics', 'Slack', 'Razorpay'],
    ctaText: 'Explore Performance Agency Workflows',
    ctaLink: '/use-cases/software-agencies',
    agents: [
      {
        id: 'perf-roas',
        name: 'ROAS & Brief Co-Founder',
        role: 'Pulls live ad spend, blended CAC, and ROAS into unified WhatsApp briefs',
        avatar: '/images/cora_agent_sales.jpg',
        badgeColor: 'bg-sky-500',
        tag: 'Meta & Google Sync',
        previewTitle: 'Multi-Platform Ad Performance',
        previewBadge: '4.2x Blended ROAS',
        previewContent: (
          <div className="space-y-2 text-xs">
            <div className="p-2.5 bg-sky-50/70 border border-sky-100 rounded-xl flex items-center justify-between">
              <span className="font-semibold text-sky-950">Meta Ads (₹3.4L Spend)</span>
              <span className="font-mono font-bold text-sky-800">4.2x ROAS (₹14.28L)</span>
            </div>
            <div className="p-2.5 bg-zinc-50 border border-zinc-100 rounded-xl flex items-center justify-between">
              <span className="text-zinc-700">Google Search (₹1.8L Spend)</span>
              <span className="font-mono font-bold text-zinc-900">4.8x ROAS (₹8.64L)</span>
            </div>
          </div>
        ),
      },
      {
        id: 'perf-billing',
        name: 'Spend % & Billing Co-Founder',
        role: 'Calculates base retainer + 12% ad spend performance bonus automatically',
        avatar: '/images/cora_agent_finance.jpg',
        badgeColor: 'bg-emerald-500',
        tag: 'Spend % Math',
        previewTitle: 'Monthly Performance Billing',
        previewBadge: 'Auto-Calculated',
        previewContent: (
          <div className="p-3 bg-emerald-50/70 border border-emerald-100 rounded-xl space-y-1.5 text-xs">
            <div className="flex items-center justify-between">
              <span className="text-zinc-700">Base Retainer + 12% Spend Accelerator</span>
              <span className="text-[10px] font-bold text-emerald-800 bg-white px-2 py-0.5 rounded">GSTR-1 Ready</span>
            </div>
            <div className="font-mono text-zinc-950 font-bold text-sm">₹1,87,400 + 18% GST (₹33,732)</div>
          </div>
        ),
      },
      {
        id: 'perf-fatigue',
        name: 'Creative Fatigue Co-Founder',
        role: 'Flags ad fatigue and queues high-converting UGC hook scripts',
        avatar: '/images/cora_agent_creative.jpg',
        badgeColor: 'bg-amber-500',
        tag: 'Creative Guard',
        previewTitle: 'Creative Fatigue Interception',
        previewBadge: 'Action Required',
        previewContent: (
          <div className="p-3 bg-amber-50/70 border border-amber-100 rounded-xl space-y-1 text-xs">
            <div className="font-bold text-amber-950">Ad Set #3 Frequency Hit 4.2 (CTR Drop 28%)</div>
            <div className="text-[11px] text-amber-800">Dispatched 3 new UGC hook scripts to production</div>
          </div>
        ),
      },
      {
        id: 'perf-payout',
        name: 'TDS & Payout Co-Founder',
        role: 'Logs Section 194C 2% TDS deductions and generates quarterly 26AS ledgers',
        avatar: '/images/cora_agent_contracts.jpg',
        badgeColor: 'bg-indigo-500',
        tag: '26AS Reconciled',
        previewTitle: 'Corporate Client TDS Ledger',
        previewBadge: '100% Matched',
        previewContent: (
          <div className="p-3 bg-zinc-50 border border-zinc-100 rounded-xl flex items-center justify-between text-xs">
            <div>
              <div className="text-[10px] text-zinc-400">194C 2% TDS Deducted</div>
              <div className="font-mono font-bold text-zinc-950">₹3,748</div>
            </div>
            <div className="text-right">
              <div className="text-[10px] text-zinc-400">Net RTGS Settled</div>
              <div className="font-mono font-bold text-indigo-950">₹2,17,384</div>
            </div>
          </div>
        ),
      },
    ],
  },
  {
    id: 'software',
    tabLabel: 'Software & Web Studios',
    headline: 'Ship client software on schedule',
    headlineAccent: 'with milestone-locked deposits',
    replaces: ['Jira', 'Linear', 'Harvest', 'Stripe'],
    ctaText: 'Explore Dev Studio Workflows',
    ctaLink: '/use-cases/software-agencies',
    agents: [
      {
        id: 'dev-spec',
        name: 'Spec & Sprint Co-Founder',
        role: 'Transforms raw client chats into scoped PRD epics and sprint tickets',
        avatar: '/images/cora_agent_delivery.jpg',
        badgeColor: 'bg-sky-500',
        tag: 'PRD Auto',
        previewTitle: 'Autonomous Architecture Spec & PRD',
        previewBadge: 'v1.4 Locked',
        previewContent: (
          <div className="space-y-2 text-xs">
            <div className="p-2.5 bg-sky-50/70 border border-sky-100 rounded-xl flex items-center justify-between">
              <span className="font-semibold text-sky-950">Next.js 16 WebApp + RBAC Auth</span>
              <span className="font-mono font-bold text-sky-800">Sprint 1 Ready</span>
            </div>
            <div className="p-2 bg-zinc-50 border border-zinc-100 rounded-lg text-[11px] text-zinc-600">
              Milestones: M1 (Architecture 30%) &bull; M2 (Core API 40%) &bull; M3 (Launch 30%)
            </div>
          </div>
        ),
      },
      {
        id: 'dev-escrow',
        name: 'Milestone Escrow Co-Founder',
        role: 'Locks milestone payments with legally binding SHA-256 e-sign agreements',
        avatar: '/images/cora_agent_contracts.jpg',
        badgeColor: 'bg-purple-500',
        tag: 'SHA-256 Seal',
        previewTitle: 'Master Services Agreement (MSA)',
        previewBadge: 'IT Act Valid',
        previewContent: (
          <div className="p-3 bg-purple-50 border border-purple-100 rounded-xl space-y-1 text-xs">
            <div className="flex items-center justify-between font-bold text-purple-950">
              <span>Milestone 1 Advance (40%)</span>
              <span className="text-[10px] bg-white text-purple-700 font-bold px-2 py-0.5 rounded">₹3,20,000 Locked</span>
            </div>
            <div className="font-mono text-[10px] text-zinc-500 truncate">SHA-256: 9e3b4a2c1f8d70a...</div>
          </div>
        ),
      },
      {
        id: 'dev-change',
        name: 'Change Order Co-Founder',
        role: 'Converts unexpected feature requests into signed budget addendums',
        avatar: '/images/cora_agent_creative.jpg',
        badgeColor: 'bg-amber-500',
        tag: 'Budget Delta',
        previewTitle: 'Feature Scope Delta Detected',
        previewBadge: 'Auto-Quote',
        previewContent: (
          <div className="p-3 bg-amber-50/70 border border-amber-100 rounded-xl space-y-1 text-xs">
            <div className="font-bold text-amber-950">+Custom Payment Gateway Integration (Razorpay/Stripe)</div>
            <div className="text-[11px] text-amber-800">+₹65,000 (3 Dev Days) &bull; Auto-signed via Client Portal</div>
          </div>
        ),
      },
      {
        id: 'dev-profit',
        name: 'Dev Margin & Burn Co-Founder',
        role: 'Tracks blended developer hourly rates, server burn, and net studio margin',
        avatar: '/images/cora_agent_finance.jpg',
        badgeColor: 'bg-indigo-500',
        tag: 'Live Profit',
        previewTitle: 'Project Profitability Index',
        previewBadge: '61% Margin',
        previewContent: (
          <div className="p-3 bg-zinc-50 border border-zinc-100 rounded-xl flex items-center justify-between text-xs">
            <div>
              <div className="text-[10px] text-zinc-400 font-semibold uppercase">Contract Value</div>
              <div className="font-mono font-bold text-zinc-950 text-sm">₹8,00,000 + GST</div>
            </div>
            <div className="text-right">
              <div className="text-[10px] text-zinc-400 font-semibold uppercase">Net Studio Margin</div>
              <div className="font-mono font-bold text-emerald-700 text-sm">₹4,88,000 (61%)</div>
            </div>
          </div>
        ),
      },
    ],
  },
  {
    id: 'video',
    tabLabel: 'Video & Content Studios',
    headline: 'Produce 100+ monthly reels & ads',
    headlineAccent: 'without feedback chaos',
    replaces: ['Frame.io', 'Google Drive', 'Monday.com', 'WhatsApp'],
    ctaText: 'Explore Video Studio Workflows',
    ctaLink: '/use-cases/software-agencies',
    agents: [
      {
        id: 'video-call',
        name: 'Call-Sheet & Crew Co-Founder',
        role: 'Broadcasts call-sheets, gear lists, and Google Maps pins to crew WhatsApp',
        avatar: '/images/cora_agent_delivery.jpg',
        badgeColor: 'bg-yellow-500',
        tag: 'Crew Broadcast',
        previewTitle: 'Commercial Shoot Day 1 Call-Sheet',
        previewBadge: '18 Crew Dispatched',
        previewContent: (
          <div className="p-3 bg-amber-50/70 border border-amber-100 rounded-xl space-y-1.5 text-xs">
            <div className="font-bold text-amber-950">Call-Time: 07:00 AM &bull; Studio 4, Film City</div>
            <div className="text-[10px] text-amber-800 font-semibold">✓ 18/18 Read Receipts Confirmed on WhatsApp</div>
          </div>
        ),
      },
      {
        id: 'video-portal',
        name: 'Review & Stamp Co-Founder',
        role: 'Hosts client video review portals with frame-accurate timestamp feedback',
        avatar: '/images/cora_agent_creative.jpg',
        badgeColor: 'bg-purple-500',
        tag: 'Frame-Accurate',
        previewTitle: 'Branded Client Review Portal',
        previewBadge: 'Cut v3 Approved',
        previewContent: (
          <div className="p-3 bg-purple-50 border border-purple-100 rounded-xl space-y-1 text-xs">
            <div className="font-bold text-purple-950">Nike Brand Commercial 60s (4K ProRes)</div>
            <div className="text-[11px] text-purple-800">Timestamp 00:14: &ldquo;Approved &bull; 1-Click Milestone Released&rdquo;</div>
          </div>
        ),
      },
      {
        id: 'video-payout',
        name: 'Vendor & Gear Co-Founder',
        role: 'Collects 50% shoot advance and settles sound, light & gaffer vendors via UPI',
        avatar: '/images/cora_agent_finance.jpg',
        badgeColor: 'bg-emerald-500',
        tag: 'Instant UPI',
        previewTitle: 'Production Vendor Settlement',
        previewBadge: '₹1,45,000 Settled',
        previewContent: (
          <div className="p-3 bg-emerald-50 border border-emerald-100 rounded-xl space-y-1 text-xs">
            <div className="font-bold text-emerald-950">Gaffer + Arri Light Kit: ₹45,000 Settled</div>
            <div className="text-[10px] text-emerald-700">TDS 2% deducted &bull; Client advance ₹2.5L collected</div>
          </div>
        ),
      },
      {
        id: 'video-burn',
        name: 'Shoot Budget & Burn Co-Founder',
        role: 'Monitors live shoot expenses against client quote in real time',
        avatar: '/images/cora_agent_contracts.jpg',
        badgeColor: 'bg-indigo-500',
        tag: 'Live Burn Rate',
        previewTitle: 'Live Shoot Budget Utilization',
        previewBadge: '88% On Budget',
        previewContent: (
          <div className="p-3 bg-zinc-50 border border-zinc-100 rounded-xl flex items-center justify-between text-xs">
            <div>
              <div className="text-[10px] text-zinc-400 font-semibold uppercase">Quoted Shoot Budget</div>
              <div className="font-mono font-bold text-zinc-950">₹5,50,000</div>
            </div>
            <div className="text-right">
              <div className="text-[10px] text-zinc-400 font-semibold uppercase">Current Burn</div>
              <div className="font-mono font-bold text-indigo-950">₹4,84,000 (88%)</div>
            </div>
          </div>
        ),
      },
    ],
  },
];

// ── Tool Brand Icons for Replaces Badges ─────────────────────────────────────
function ToolIcon({ name }: { name: string }) {
  switch (name) {
    case 'Notion':
      return <SiNotion className="w-3.5 h-3.5 shrink-0 text-zinc-950" />;
    case 'Asana':
      return <SiAsana className="w-3.5 h-3.5 shrink-0 text-[#F06A6D]" />;
    case 'DocuSign':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <rect width="24" height="24" rx="4.5" fill="#005CB9" />
          <path d="M5.5 13.5c1.8-3.2 4.8-5.8 8.8-5.8-1.8 2.8-4.2 5.8-7.8 6.8l-1-1z" fill="#FFC820" />
          <path d="M12 11.5c1.5-1.5 3.2-2.5 5-2.8-1 1.8-2.2 3.5-3.8 4.8l-1.2-2z" fill="#FFFFFF" />
        </svg>
      );
    case 'QuickBooks':
      return <SiQuickbooks className="w-3.5 h-3.5 shrink-0 text-[#2CA01C]" />;
    case 'Looker Studio':
      return <SiLooker className="w-3.5 h-3.5 shrink-0 text-[#4285F4]" />;
    case 'Supermetrics':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <rect width="24" height="24" rx="4.5" fill="#FF4F00" />
          <path d="M16 8h-6a2 2 0 0 0-2 2v0.5a2 2 0 0 0 2 2h4a2 2 0 0 1 2 2V15a2 2 0 0 1-2 2H8" stroke="#FFFFFF" strokeWidth="2.2" strokeLinecap="round" />
        </svg>
      );
    case 'Slack':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52-2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313z" fill="#E01E5A"/>
          <path d="M8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312z" fill="#36C5F0"/>
          <path d="M18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312z" fill="#2EB67D"/>
          <path d="M15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.527 2.527 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z" fill="#ECB22E"/>
        </svg>
      );
    case 'Razorpay':
      return <SiRazorpay className="w-3.5 h-3.5 shrink-0 text-[#0C2340]" />;
    case 'Jira':
      return <SiJira className="w-3.5 h-3.5 shrink-0 text-[#0052CC]" />;
    case 'Linear':
      return <SiLinear className="w-3.5 h-3.5 shrink-0 text-[#5E6AD2]" />;
    case 'Harvest':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <rect width="24" height="24" rx="4.5" fill="#FA5D00" />
          <circle cx="12" cy="12" r="7" stroke="#FFFFFF" strokeWidth="2.2" />
          <path d="M12 8v4.5l3 2" stroke="#FFFFFF" strokeWidth="2" strokeLinecap="round" />
        </svg>
      );
    case 'Stripe':
      return <SiStripe className="w-3.5 h-3.5 shrink-0 text-[#635BFF]" />;
    case 'Frame.io':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <rect width="24" height="24" rx="4.5" fill="#5F2EEA" />
          <path d="M7 6h10v3H10v2.5h6v3H10V18H7V6z" fill="#FFFFFF" />
        </svg>
      );
    case 'Google Drive':
      return <SiGoogledrive className="w-3.5 h-3.5 shrink-0 text-[#0F9D58]" />;
    case 'Monday.com':
      return <TbBrandMonday className="w-3.5 h-3.5 shrink-0 text-[#F43F5E]" />;
    case 'WhatsApp':
      return <SiWhatsapp className="w-3.5 h-3.5 shrink-0 text-[#25D366]" />;
    default:
      return null;
  }
}

const STEP_DURATION_MS = 4500; // 4.5 seconds per block

export function PlatformLifecycleSection() {
  const [activeTab, setActiveTab] = useState<string>('creative');
  const [selectedAgentIndex, setSelectedAgentIndex] = useState<number>(0);
  const [progress, setProgress] = useState<number>(0);
  const [isPaused, setIsPaused] = useState<boolean>(false);

  const currentTabIndex = solutionsData.findIndex((s) => s.id === activeTab);
  const currentSolution = solutionsData[currentTabIndex >= 0 ? currentTabIndex : 0];
  const activeAgent = currentSolution.agents[selectedAgentIndex] || currentSolution.agents[0];

  const handleTabChange = useCallback((tabId: string) => {
    setActiveTab(tabId);
    setSelectedAgentIndex(0);
    setProgress(0);
  }, []);

  const handleAgentClick = useCallback((index: number) => {
    setSelectedAgentIndex(index);
    setProgress(0);
  }, []);

  // ── Auto-Cycling Timer with Smooth Progress ─────────────────────────────────
  useEffect(() => {
    if (isPaused) return;

    const intervalMs = 30;
    const progressIncrement = intervalMs / STEP_DURATION_MS;

    const timer = setInterval(() => {
      setProgress((prev) => {
        if (prev + progressIncrement >= 1) {
          // Time to advance
          if (selectedAgentIndex < currentSolution.agents.length - 1) {
            setSelectedAgentIndex((curr) => curr + 1);
          } else {
            // Completed all agents in current tab, advance to next tab
            const nextTabIndex = (currentTabIndex + 1) % solutionsData.length;
            setActiveTab(solutionsData[nextTabIndex].id);
            setSelectedAgentIndex(0);
          }
          return 0;
        }
        return prev + progressIncrement;
      });
    }, intervalMs);

    return () => clearInterval(timer);
  }, [isPaused, selectedAgentIndex, currentTabIndex, currentSolution.agents.length]);

  return (
    <section
      id="how-it-works"
      className="py-14 sm:py-20 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. Section Header ── */}
        <div className="max-w-[760px] mx-auto text-center mb-8 sm:mb-10">
          <h2 className="font-display text-2xl xs:text-3xl sm:text-4xl lg:text-[44px] font-bold leading-[1.2] tracking-[-0.03em] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block pb-2 mb-1.5">
            AI co-founder for every agency model
          </h2>
          <p className="text-zinc-600 text-xs sm:text-sm font-normal leading-relaxed max-w-[560px] mx-auto">
            Click any autonomous co-founder to preview real-time deliverables, 18% GST billing math, and active scope protection.
          </p>
        </div>

        {/* ── 2. Filter Pills / Industry Tabs ── */}
        <div className="flex items-center justify-start sm:justify-center gap-2 overflow-x-auto pb-3 sm:pb-0 mb-8 sm:mb-10 scrollbar-none select-none">
          {solutionsData.map((item) => {
            const isActive = item.id === activeTab;
            return (
              <button
                key={item.id}
                onClick={() => handleTabChange(item.id)}
                type="button"
                className={`relative px-4 py-1.5 rounded-full text-xs sm:text-[13px] font-semibold tracking-tight whitespace-nowrap transition-all duration-200 cursor-pointer ${
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
        <div
          onMouseEnter={() => setIsPaused(true)}
          onMouseLeave={() => setIsPaused(false)}
          className="bg-[#F8F8F9] rounded-[32px] p-6 sm:p-8 lg:p-10 border border-zinc-200/80 shadow-[0px_6px_24px_rgba(0,0,0,0.03)] transition-all duration-300"
        >
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
            
            {/* Left Column: Headline + Live Interactive Artifact UI Card */}
            <div className="lg:col-span-6 space-y-4">
              <div>
                <h3 className="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold leading-[1.24] tracking-[-0.03em] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block pb-1.5">
                  {currentSolution.headline} {currentSolution.headlineAccent}
                </h3>
              </div>

              {/* Replaces Badges as Interactive Icon Buttons */}
              <div className="flex items-center gap-2 flex-wrap pt-0.5">
                <span className="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mr-0.5">
                  REPLACES
                </span>
                {currentSolution.replaces.map((app, rIdx) => (
                  <div
                    key={rIdx}
                    className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-zinc-200/90 text-zinc-800 text-[11.5px] font-semibold shadow-2xs hover:border-zinc-300 hover:bg-zinc-50 transition-all duration-150 cursor-default select-none"
                  >
                    <ToolIcon name={app} />
                    <span>{app}</span>
                  </div>
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
                <div key={`${currentSolution.id}-${activeAgent.id}`} className="transition-opacity duration-300 animate-fadeIn">
                  {activeAgent.previewContent}
                </div>

                <div className="pt-1 flex items-center justify-between text-[11px] font-semibold text-zinc-400">
                  <span>AI Generated in 0.8s</span>
                  <span className="text-zinc-900 font-bold flex items-center gap-1">
                    Auto-Synced &rarr;
                  </span>
                </div>
              </div>
            </div>

            {/* Right Column: Stack of 4 Clickable Interactive Co-Founders with Border Progress */}
            <div className="lg:col-span-6 space-y-2.5">
              {currentSolution.agents.map((agent, aIdx) => {
                const isSelected = aIdx === selectedAgentIndex;
                return (
                  <div
                    key={agent.id}
                    onClick={() => handleAgentClick(aIdx)}
                    className={`relative rounded-2xl p-3.5 sm:p-4 border transition-all duration-200 flex items-center justify-between gap-3 group cursor-pointer ${
                      isSelected
                        ? 'bg-white border-zinc-900/10 shadow-md ring-1 ring-zinc-950/10'
                        : 'bg-white/80 hover:bg-white border-zinc-200/80 hover:border-zinc-300 shadow-2xs'
                    }`}
                  >
                    {/* Animated Border Progress Bar around the Active Block */}
                    {isSelected && (
                      <svg
                        className="absolute inset-0 w-full h-full pointer-events-none rounded-2xl overflow-visible z-20"
                        style={{ filter: 'drop-shadow(0 0 1px rgba(0,0,0,0.15))' }}
                      >
                        <rect
                          x="1"
                          y="1"
                          width="calc(100% - 2px)"
                          height="calc(100% - 2px)"
                          rx="15"
                          fill="none"
                          stroke="#18181B"
                          strokeWidth="2.4"
                          pathLength="100"
                          strokeDasharray="100"
                          strokeDashoffset={Math.max(0, 100 - progress * 100)}
                          strokeLinecap="round"
                        />
                      </svg>
                    )}

                    <div className="flex items-center gap-3 min-w-0 z-10">
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

                    <ArrowRight className={`w-4 h-4 transition-all shrink-0 z-10 ${
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
