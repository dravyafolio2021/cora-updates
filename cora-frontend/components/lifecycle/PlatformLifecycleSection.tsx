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
        avatar: '/images/about_team_ananya.jpg',
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
        avatar: '/images/about_team_meera.jpg',
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
        avatar: '/images/agency_female_director.jpg',
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
        avatar: '/images/agency_female_lead.jpg',
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
        avatar: '/images/about_team_tanya.jpg',
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
        avatar: '/images/about_team_meera.jpg',
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
        avatar: '/images/agency_female_director.jpg',
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
        avatar: '/images/about_team_dev.jpg',
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
        avatar: '/images/about_team_ananya.jpg',
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
        avatar: '/images/agency_female_director.jpg',
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
        avatar: '/images/agency_female_lead.jpg',
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
        avatar: '/images/about_team_dev.jpg',
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
        avatar: '/images/about_team_pooja.jpg',
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
        avatar: '/images/agency_female_lead.jpg',
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
        avatar: '/images/about_team_meera.jpg',
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
        avatar: '/images/about_team_dev.jpg',
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
      return (
        <svg className="w-3.5 h-3.5 shrink-0 text-zinc-900" viewBox="0 0 24 24" fill="currentColor">
          <path d="M4.459 4.208c.746.606 1.026.56 2.428.466l13.215-.793c.28 0 .047-.28-.046-.373L17.76 1.455c-.466-.466-1.026-.746-1.865-.653L2.827 1.874c-.373.047-.466.326-.326.606zm.793 4.244v13.525c0 .7.373 1.026 1.12 1.026l14.288-.84c.746-.046.886-.606.886-1.166V6.96c0-.653-.28-.933-.84-.887l-14.568.84c-.606.046-.886.42-.886 1.54zm12.368 1.4l.093 10.307c0 .42-.187.606-.606.606-.327 0-.514-.14-.793-.42l-5.69-8.487v8.067c0 .513-.28.746-.746.746h-.84c-.466 0-.606-.233-.606-.746V10.74c0-.42.187-.653.606-.653.373 0 .606.187.886.513l5.83 8.674V10.554c0-.513.233-.746.746-.746h.513c.467 0 .607.28.607.746z"/>
        </svg>
      );
    case 'Asana':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="#F06A6A">
          <circle cx="12" cy="6.5" r="4.5" />
          <circle cx="5.5" cy="17" r="4.5" />
          <circle cx="18.5" cy="17" r="4.5" />
        </svg>
      );
    case 'DocuSign':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <rect width="24" height="24" rx="4" fill="#005CB9" />
          <path d="M6 14.5c1.5-3.5 4.5-6.5 9-6.5-1.5 3-4 6.5-8 7.5l-1-1z" fill="#FFC820" />
          <path d="M12.5 12c1.5-1.5 3.5-3 5.5-3.5-1 2-2.5 4-4.5 5.5l-1-2z" fill="#FFFFFF" />
        </svg>
      );
    case 'QuickBooks':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="11" fill="#2CA01C" />
          <path d="M10.5 8H8a4 4 0 0 0 0 8h2.5v-2H8a2 2 0 1 1 0-4h2.5V8zm3 8H16a4 4 0 0 0 0-8h-2.5v2H16a2 2 0 1 1 0 4h-2.5v2z" fill="#FFFFFF" />
        </svg>
      );
    case 'Looker Studio':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <rect x="3" y="11" width="4" height="10" rx="1" fill="#4285F4" />
          <rect x="10" y="6" width="4" height="15" rx="1" fill="#00AC47" />
          <rect x="17" y="2" width="4" height="19" rx="1" fill="#FBBC05" />
        </svg>
      );
    case 'Supermetrics':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <rect width="24" height="24" rx="4" fill="#FF4F00" />
          <path d="M16.5 7.5h-7a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h5a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2h-7" stroke="#FFFFFF" strokeWidth="2.5" strokeLinecap="round" />
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
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <path d="M12.5 2.5L7.2 13.8h5.3L9.8 19.2l9.7-9.2h-5.2l3.2-7.5h-5z" fill="#3395FF" />
        </svg>
      );
    case 'Jira':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <path d="M11.53 2c0 5.26-4.27 9.53-9.53 9.53h-.03V2h9.56z" fill="#0052CC"/>
          <path d="M11.53 11.97c0 5.26-4.27 9.53-9.53 9.53h-.03V11.97h9.56z" fill="#2684FF"/>
          <path d="M22 11.97c0 5.26-4.27 9.53-9.53 9.53h-.03V11.97H22z" fill="#0052CC"/>
        </svg>
      );
    case 'Linear':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="#5E6AD2">
          <path d="M3.5 3.5a1 1 0 0 1 1.4 0l15.6 15.6a1 1 0 0 1-1.4 1.4L3.5 4.9a1 1 0 0 1 0-1.4z" />
          <path d="M3 8a1 1 0 0 1 1.7-.7l12 12a1 1 0 0 1-1.4 1.4L3.3 8.7A1 1 0 0 1 3 8z" fillOpacity="0.7" />
          <path d="M3 13a1 1 0 0 1 1.7-.7l7 7a1 1 0 0 1-1.4 1.4L3.3 13.7A1 1 0 0 1 3 13z" fillOpacity="0.4" />
        </svg>
      );
    case 'Harvest':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <rect width="24" height="24" rx="4" fill="#FA5D00" />
          <circle cx="12" cy="12" r="7" stroke="#FFFFFF" strokeWidth="2.2" />
          <path d="M12 8v4.5l3 2" stroke="#FFFFFF" strokeWidth="2" strokeLinecap="round" />
        </svg>
      );
    case 'Stripe':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <rect width="24" height="24" rx="4" fill="#635BFF" />
          <path d="M13.5 10.8c-.8-.4-1.2-.7-1.2-1.1 0-.4.4-.7 1.1-.7.8 0 1.6.3 2.2.7l.6-1.5c-.7-.4-1.7-.7-2.8-.7-2.1 0-3.5 1.1-3.5 2.8 0 2.2 3 1.8 3 2.8 0 .5-.5.8-1.3.8-1 0-2-.5-2.6-.9l-.6 1.6c.8.5 2 .9 3.2.9 2.2 0 3.7-1.1 3.7-2.9 0-2.3-3.2-1.9-3.2-2.9z" fill="#FFFFFF" />
        </svg>
      );
    case 'Frame.io':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <rect width="24" height="24" rx="4" fill="#5F2EEA" />
          <path d="M7 6h10v3H10v2.5h6v3H10V18H7V6z" fill="#FFFFFF" />
        </svg>
      );
    case 'Google Drive':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <path d="M8.2 3.5L2 14.2l3.8 6.5L12 10 8.2 3.5z" fill="#0066DA" />
          <path d="M15.8 3.5H8.2L12 10l7.6 13.2h7.6L15.8 3.5z" fill="#00AC47" />
          <path d="M2 14.2l3.8 6.5h15.4L17.4 14.2H2z" fill="#FFBA00" />
        </svg>
      );
    case 'Monday.com':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none">
          <circle cx="6" cy="12" r="3" fill="#F43F5E" />
          <circle cx="12" cy="12" r="3" fill="#EAB308" />
          <circle cx="18" cy="12" r="3" fill="#10B981" />
        </svg>
      );
    case 'WhatsApp':
      return (
        <svg className="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
          <path
            d="M12.031 0C5.395 0 0 5.395 0 12.031c0 2.12.553 4.188 1.604 6.01L.062 24l6.143-1.61A12.03 12.03 0 0 0 12.03 24c6.637 0 12.031-5.395 12.031-12.031S18.668 0 12.031 0z"
            fill="#25D366"
          />
          <path
            d="M17.5 14.3c-.3-.15-1.78-.88-2.06-.98-.28-.1-.48-.15-.68.15-.2.3-.78.98-.95 1.18-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.68-1.64-.93-2.25-.24-.59-.49-.51-.68-.52h-.58c-.2 0-.52.07-.8.37-.27.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.11c.15.2 2.12 3.24 5.14 4.54.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.43.25-.7.25-1.3.18-1.43-.08-.13-.28-.2-.58-.35z"
            fill="#FFFFFF"
          />
        </svg>
      );
    default:
      return null;
  }
}

export function PlatformLifecycleSection() {
  const [activeTab, setActiveTab] = useState<string>('creative');
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
