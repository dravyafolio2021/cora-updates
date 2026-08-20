'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  Sparkles, 
  ShieldCheck, 
  FileText, 
  Receipt, 
  Send, 
  HardDrive, 
  ArrowRight, 
  CheckCircle2, 
  Zap, 
  Layers, 
  Cpu, 
  Clock, 
  Lock,
  Smartphone,
  Bot,
  Kanban,
  LayoutTemplate,
  Camera,
  Calendar,
  FormInput,
  Star,
  CheckSquare,
  Mail,
  BrainCircuit,
  Users2,
  BookOpen,
  Settings,
  Compass,
  MessageCircle,
  Image as ImageIcon,
  CreditCard,
  Video,
  GitBranch,
  PhoneCall,
  FileSpreadsheet,
  TabletSmartphone,
  ChevronRight,
  Filter
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

// ── 20 Built Modules (Live in Product) ──
const BUILT_MODULES = [
  {
    id: 'ai-cofounder',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    title: 'Autonomous AI Co-Founder & Live Chat',
    desc: 'Multi-turn conversational memory, proactive operational dialogue, natural language action execution, and a 6-tier fallback engine.',
    icon: Bot,
    color: 'emerald',
    tags: ['Multi-Turn Memory', 'Proactive Dialogue', 'Action Execution', '6-Tier Fallback'],
    status: 'Live in Product'
  },
  {
    id: 'lead-crm',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    title: 'Lead Management CRM & Kanban Pipeline',
    desc: 'Visual deal stages, dynamic revenue forecasting, sliding deal drawer sheets, and automated WhatsApp / Email client outreach.',
    icon: Kanban,
    color: 'blue',
    tags: ['Kanban Board', 'Revenue Forecast', 'Sliding Drawer', 'Omnichannel Reach'],
    status: 'Live in Product'
  },
  {
    id: 'canvas-builder',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    title: 'Visual Canvas & Funnel Page Builder',
    desc: 'Drag-and-drop landing page & shoot funnel designer with responsive device preview and automatic Git repository sync.',
    icon: LayoutTemplate,
    color: 'violet',
    tags: ['Drag & Drop', 'Device Preview', 'Git Auto-Sync', 'High Conversion'],
    status: 'Live in Product'
  },
  {
    id: 'content-ai',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    title: 'Content AI Suite & Editorial Studio',
    desc: 'Quill WYSIWYG rich text editor, 7-tab lifecycle workflow, GEO-targeted SEO generator, and IndexNow instant search indexing.',
    icon: Sparkles,
    color: 'amber',
    tags: ['Quill WYSIWYG', 'GEO-Targeted SEO', 'IndexNow Sync', '7-Tab Lifecycle'],
    status: 'Live in Product'
  },
  {
    id: 'esign-vault',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    title: 'Secure Document Vault & E-Sign Suite',
    desc: 'Guided 5-step document wizard, legal canvas e-signatures, immutable SHA-256 cryptographic audit logs, and PDF generator.',
    icon: FileText,
    color: 'rose',
    tags: ['5-Step Wizard', 'SHA-256 Audit', 'IT Act 2000', 'Tamper Evident'],
    status: 'Live in Product'
  },
  {
    id: 'gst-invoicing',
    category: 'finance',
    categoryLabel: 'Finance & Media',
    title: 'Financials, Invoicing & GST Tax Hub',
    desc: 'Automated 18% CGST/SGST/IGST tax engine, branded PDF tax invoices, real-time payment tracking, and revenue analytics.',
    icon: Receipt,
    color: 'emerald',
    tags: ['18% GST Engine', 'CGST/SGST Split', 'Instant UPI QR', 'CA Export'],
    status: 'Live in Product'
  },
  {
    id: 'asset-gear',
    category: 'finance',
    categoryLabel: 'Finance & Media',
    title: 'Asset & Equipment / Property Listing Manager',
    desc: 'Studio camera gear inventory check-in/out tracking and comprehensive real estate property listings catalog.',
    icon: Camera,
    color: 'sky',
    tags: ['Barcode Scan', 'Gear Check-In/Out', 'Property MLS', 'Status Badges'],
    status: 'Live in Product'
  },
  {
    id: 'crew-dispatch',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    title: 'Crew & Team Dispatch Scheduler',
    desc: 'Timeline crew grid, call-time slot pickers, GPS shoot location mapping, and automated schedule conflict guards.',
    icon: Send,
    color: 'indigo',
    tags: ['Timeline Grid', 'Slot Pickers', 'GPS Mapping', 'Conflict Guard'],
    status: 'Live in Product'
  },
  {
    id: 'master-calendar',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    title: 'Master Calendar & Booking Manager',
    desc: 'Day/Week/Month multi-view grid, 5-step booking modal, client shoot scheduling, and real-time showing locks.',
    icon: Calendar,
    color: 'purple',
    tags: ['Day/Week/Month', '5-Step Booking', 'Stage Locks', 'Google Cal Sync'],
    status: 'Live in Product'
  },
  {
    id: 'form-builder',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    title: 'Visual Form Builder & Share Engine',
    desc: 'Drag-and-drop form builder, live standalone public URLs, embeddable iframe codes, and auto-sync directly to CRM pipeline.',
    icon: FormInput,
    color: 'teal',
    tags: ['Drag & Drop', 'Live URL Share', 'Embed Codes', 'Direct CRM Sync'],
    status: 'Live in Product'
  },
  {
    id: 'review-portal',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    title: 'Review & Reputation Acquisition Portal',
    desc: 'Public 5-star client feedback portal, automated review request campaigns, and direct Google Business Profile routing.',
    icon: Star,
    color: 'amber',
    tags: ['5★ Feedback', 'Review Campaigns', 'Google Routing', 'Reputation Score'],
    status: 'Live in Product'
  },
  {
    id: 'media-hub',
    category: 'finance',
    categoryLabel: 'Finance & Media',
    title: 'Studio Media Hub & Asset Manager',
    desc: 'Folder-based cloud media library, 1:1, 4:3, 16:9 aspect-ratio crop presets, and automatic SEO metadata tagging.',
    icon: HardDrive,
    color: 'blue',
    tags: ['Folder Library', 'Crop Presets', 'SEO Tagging', 'RAW Storage'],
    status: 'Live in Product'
  },
  {
    id: 'task-board',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    title: 'Client Task & Milestone Board',
    desc: 'Production task board with priority badges, deadline countdown timers, team assignments, and client milestone sign-offs.',
    icon: CheckSquare,
    color: 'emerald',
    tags: ['Priority Badges', 'Deadline Timers', 'Milestones', 'Role Assign'],
    status: 'Live in Product'
  },
  {
    id: 'email-smtp',
    category: 'finance',
    categoryLabel: 'Finance & Media',
    title: 'Email Management & SMTP Suite',
    desc: 'Visual HTML template composer, dynamic variable tags, SMTP diagnostic connectivity tester, and live outbox delivery logs.',
    icon: Mail,
    color: 'sky',
    tags: ['HTML Composer', 'Variable Tags', 'SMTP Diagnostic', 'Delivery Logs'],
    status: 'Live in Product'
  },
  {
    id: 'rag-mcp',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    title: 'AI Tools MCP & Self-Learning RAG Knowledge Base',
    desc: 'Model Context Protocol server, vector memory store, and living business context sync for hyper-personalized studio responses.',
    icon: BrainCircuit,
    color: 'purple',
    tags: ['MCP Server', 'Vector Memory', 'Living Context', 'Self-Learning'],
    status: 'Live in Product'
  },
  {
    id: 'rbac-system',
    category: 'platform',
    categoryLabel: 'Platform & Governance',
    title: 'Multi-Tenant Role-Based Access Control (RBAC)',
    desc: 'Strict tenant data isolation and granular permissions matrix for Super Admins, Managers, Photographers, Agents, and Editors.',
    icon: Users2,
    color: 'zinc',
    tags: ['Tenant Isolation', '5-Tier Roles', 'Granular Matrix', 'Zero Leakage'],
    status: 'Live in Product'
  },
  {
    id: 'pwa-push',
    category: 'platform',
    categoryLabel: 'Platform & Governance',
    title: 'Progressive Web App (PWA) & Web Push Engine',
    desc: 'Installable mobile PWA, VAPID background push notifications, offline service worker cache, and orientation lock.',
    icon: Smartphone,
    color: 'emerald',
    tags: ['Installable PWA', 'VAPID Push', 'Offline Worker', 'iOS & Android'],
    status: 'Live in Product'
  },
  {
    id: 'docs-portal',
    category: 'platform',
    categoryLabel: 'Platform & Governance',
    title: 'Public Developer Docs & Knowledge Base',
    desc: 'Notion-styled 3-column developer documentation at /docs, command palette search (⌘K), and interactive API playground.',
    icon: BookOpen,
    color: 'blue',
    tags: ['Notion Layout', '⌘K Search', 'API Playground', 'Open Guides'],
    status: 'Live in Product'
  },
  {
    id: 'super-admin',
    category: 'platform',
    categoryLabel: 'Platform & Governance',
    title: 'Super Admin Multi-Tenant Governance Suite',
    desc: 'Global workspace management, per-tenant module feature toggles, AI token usage metrics, and audit log inspector.',
    icon: Settings,
    color: 'zinc',
    tags: ['Global Manager', 'Module Toggles', 'Token Monitor', 'Security Logs'],
    status: 'Live in Product'
  },
  {
    id: 'onboarding-wizard',
    category: 'platform',
    categoryLabel: 'Platform & Governance',
    title: 'Guided Onboarding & Setup Wizard',
    desc: 'Multi-step setup wizard, 3D logo pedestal, automatic industry schema seeding (Commercial, Real Estate, Video, Agency).',
    icon: Compass,
    color: 'amber',
    tags: ['3D Pedestal', 'Schema Seeding', 'Instant Setup', 'Zero Friction'],
    status: 'Live in Product'
  }
];

// ── 8 Upcoming Roadmap Modules (Building Soon) ──
const UPCOMING_MODULES = [
  {
    id: 'whatsapp-cloud',
    title: 'Native WhatsApp Business Cloud API',
    desc: 'Direct 2-way client chat embedded inside CRM, template broadcasts, automated booking reminder bots, and quick-reply scopes.',
    icon: MessageCircle,
    eta: 'Q3 2026',
    status: 'Building Soon'
  },
  {
    id: 'photo-proofing',
    title: 'Client Photo Proofing & Selection Portal 2.0',
    desc: 'Watermarked client selection galleries, favorite star ratings, photo feedback pin drops, and multi-version album approvals.',
    icon: ImageIcon,
    eta: 'Q3 2026',
    status: 'Building Soon'
  },
  {
    id: 'integrated-payments',
    title: 'Integrated Payment Gateways (Auto-Reconcile)',
    desc: 'Direct Razorpay, Stripe, and UPI payment collection links embedded on invoices with instant automated ledger reconciliation.',
    icon: CreditCard,
    eta: 'Q3 2026',
    status: 'Building Soon'
  },
  {
    id: 'video-storyboard',
    title: 'AI Video Script & Motion Graphic Storyboard',
    desc: 'Presentation deck generator, video editing prompts, and viral YouTube Shorts & Instagram Reels scriptwriting engine.',
    icon: Video,
    eta: 'Q4 2026',
    status: 'Building Soon'
  },
  {
    id: 'multi-branch',
    title: 'Multi-Branch & Franchise Workspace System',
    desc: 'Multi-location studio management, cross-branch consolidated financial reporting, and shared regional gear inventory pools.',
    icon: GitBranch,
    eta: 'Q4 2026',
    status: 'Building Soon'
  },
  {
    id: 'voice-ai-agent',
    title: 'Autonomous Voice AI Call Agent',
    desc: 'Inbound and outbound conversational voice AI via ElevenLabs & Twilio for automated booking confirmations and buyer qualification.',
    icon: PhoneCall,
    eta: 'Q4 2026',
    status: 'Building Soon'
  },
  {
    id: 'tally-zoho-export',
    title: 'Automated Accounting & Tally/Zoho Export',
    desc: 'One-click GSTR-1 ready sales ledger export in structured XML/JSON format for Tally Prime and Zoho Books CA sync.',
    icon: FileSpreadsheet,
    eta: 'Q4 2026',
    status: 'Building Soon'
  },
  {
    id: 'client-mobile-app',
    title: 'White-Labeled Client Mobile Companion App',
    desc: 'Native iOS & Android app for studio clients to sign contracts, track shoot milestones, pay invoices, and view proofs.',
    icon: TabletSmartphone,
    eta: 'Q1 2027',
    status: 'Building Soon'
  }
];

const CATEGORIES = [
  { id: 'all', label: 'All Modules (28)' },
  { id: 'intelligence', label: 'Intelligence & AI (3)' },
  { id: 'sales', label: 'Sales & CRM (4)' },
  { id: 'operations', label: 'Operations & Legal (4)' },
  { id: 'finance', label: 'Finance & Media (4)' },
  { id: 'platform', label: 'Platform & Governance (5)' },
  { id: 'roadmap', label: 'Upcoming Roadmap (8)' }
];

export default function FeaturesPage() {
  const [activeCategory, setActiveCategory] = useState('all');

  const filteredBuiltModules = activeCategory === 'all' 
    ? BUILT_MODULES 
    : activeCategory === 'roadmap'
      ? []
      : BUILT_MODULES.filter(m => m.category === activeCategory);

  const showRoadmap = activeCategory === 'all' || activeCategory === 'roadmap';

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-24 overflow-hidden bg-white">
      
      {/* ── Top Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-16 sm:mb-20">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-xl border border-zinc-200/80 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
          <span>20 Built Modules Live • 8 In Active Development</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[940px] mx-auto mb-5">
          The all-in-one autonomous operating system for creative studios
        </h1>

        <p className="text-zinc-600 text-base sm:text-xl font-normal leading-relaxed max-w-[720px] mx-auto mb-8">
          Replace 8+ fragmented SaaS tools with one unified workspace. From multi-model AI agents and Kanban CRM to legal e-signatures, GST invoices, and crew dispatch.
        </p>

        <div className="flex items-center justify-center flex-wrap gap-3.5">
          <a
            href="https://app.heycora.in/workspace/login?source=features_hero"
            onClick={() => trackEvent('features_page_cta_clicked')}
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm border border-zinc-800 group"
          >
            <span>Get started for Free</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
          </a>

          <a
            href="mailto:dravya.bansal@heycora.in?subject=Features%20Inquiry%20from%20Cora"
            className="inline-flex items-center gap-2 bg-white text-zinc-950 border border-zinc-300 hover:border-zinc-400 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-50 transition-all shadow-2xs"
          >
            <span>Chat with Founder</span>
          </a>
        </div>
      </section>

      {/* ── Category Filter Pills ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-12">
        <div className="flex items-center gap-2 overflow-x-auto pb-3 scrollbar-none border-b border-zinc-100">
          {CATEGORIES.map((cat) => (
            <button
              key={cat.id}
              onClick={() => {
                setActiveCategory(cat.id);
                trackEvent('features_category_filter', { category: cat.id });
              }}
              className={`px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all ${
                activeCategory === cat.id
                  ? 'bg-zinc-950 text-white shadow-sm'
                  : 'bg-zinc-100/80 text-zinc-600 hover:text-zinc-950 hover:bg-zinc-200/60'
              }`}
            >
              {cat.label}
            </button>
          ))}
        </div>
      </section>

      {/* ── 20 Built Modules (Live in Product) Grid ── */}
      {filteredBuiltModules.length > 0 && (
        <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24">
          <div className="flex items-center justify-between mb-8">
            <div className="flex items-center gap-2.5">
              <span className="w-2.5 h-2.5 rounded-full bg-emerald-500" />
              <h2 className="font-display text-xl sm:text-2xl font-bold text-zinc-950">
                Built Modules (Live in Product)
              </h2>
            </div>
            <span className="text-xs font-mono font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200/80 px-2.5 py-1 rounded-lg">
              {filteredBuiltModules.length} Available Now
            </span>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredBuiltModules.map((mod) => {
              const Icon = mod.icon;
              return (
                <div
                  key={mod.id}
                  className="bg-white rounded-[24px] border border-zinc-200/90 p-6 flex flex-col justify-between hover:shadow-[0_12px_30px_rgba(0,0,0,0.06)] hover:border-zinc-300 transition-all group"
                >
                  <div className="space-y-4">
                    <div className="flex items-start justify-between gap-3">
                      <div className="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                        <Icon className="w-5 h-5" />
                      </div>
                      <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded-md flex items-center gap-1">
                        <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                        LIVE
                      </span>
                    </div>

                    <div>
                      <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-1">
                        {mod.categoryLabel}
                      </span>
                      <h3 className="font-display text-base font-bold text-zinc-950 leading-snug">
                        {mod.title}
                      </h3>
                    </div>

                    <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed">
                      {mod.desc}
                    </p>
                  </div>

                  <div className="pt-5 border-t border-zinc-100 mt-5">
                    <div className="flex flex-wrap gap-1.5">
                      {mod.tags.map((tag) => (
                        <span
                          key={tag}
                          className="text-[10px] font-medium bg-zinc-100 text-zinc-700 px-2 py-0.5 rounded-md border border-zinc-200/60"
                        >
                          {tag}
                        </span>
                      ))}
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </section>
      )}

      {/* ── 8 Upcoming Roadmap Modules Section ── */}
      {showRoadmap && (
        <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28">
          <div className="bg-zinc-950 rounded-[36px] p-8 sm:p-12 md:p-14 text-white border border-zinc-800 shadow-2xl relative overflow-hidden">
            
            <div className="max-w-[720px] mb-12">
              <div className="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/10 rounded-xl border border-amber-500/30 text-xs font-semibold text-amber-300 mb-4">
                <span className="w-2 h-2 rounded-full bg-amber-400 animate-pulse" />
                <span>Active Product Roadmap</span>
              </div>
              <h2 className="font-display text-2xl sm:text-4xl font-bold tracking-tight mb-3">
                Upcoming Modules &amp; Enterprise Features
              </h2>
              <p className="text-zinc-400 text-xs sm:text-sm leading-relaxed">
                Currently undergoing private alpha testing with high-volume photography and film studio partners across Mumbai, Delhi, and Bangalore.
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
              {UPCOMING_MODULES.map((item) => {
                const Icon = item.icon;
                return (
                  <div
                    key={item.id}
                    className="bg-zinc-900/80 rounded-2xl p-5 border border-zinc-800 flex flex-col justify-between hover:border-zinc-700 transition-all group"
                  >
                    <div className="space-y-3">
                      <div className="flex items-center justify-between">
                        <div className="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                          <Icon className="w-4 h-4" />
                        </div>
                        <span className="text-[10px] font-mono font-bold text-amber-300 bg-amber-500/20 px-2 py-0.5 rounded-full border border-amber-500/30">
                          {item.eta}
                        </span>
                      </div>

                      <h4 className="font-display text-xs sm:text-sm font-bold text-white leading-snug">
                        {item.title}
                      </h4>

                      <p className="text-zinc-400 text-xs leading-relaxed">
                        {item.desc}
                      </p>
                    </div>

                    <div className="pt-4 mt-3 border-t border-zinc-800/80 flex items-center justify-between text-[11px] text-zinc-500">
                      <span>Status</span>
                      <span className="text-amber-400 font-semibold flex items-center gap-1">
                        <span className="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse" />
                        {item.status}
                      </span>
                    </div>
                  </div>
                );
              })}
            </div>

          </div>
        </section>
      )}

      {/* ── Why Cora Replaces 8+ Subscriptions ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 text-center">
        <div className="max-w-[720px] mx-auto mb-14">
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight mb-4">
            Replace 8+ fragmented subscriptions with one unified stack
          </h2>
          <p className="text-zinc-600 text-sm sm:text-base leading-relaxed">
            Stop paying ₹35,000+ monthly across separate CRM, invoicing, e-sign, cloud storage, scheduling, and AI tools.
          </p>
        </div>

        <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-[980px] mx-auto text-left">
          <div className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80">
            <span className="text-xs font-mono font-bold text-rose-500 line-through block mb-1">DocuSign / PandaDoc</span>
            <span className="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" /> Cora SHA-256 E-Signs
            </span>
          </div>

          <div className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80">
            <span className="text-xs font-mono font-bold text-rose-500 line-through block mb-1">HubSpot / Pipedrive</span>
            <span className="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" /> Cora Kanban CRM
            </span>
          </div>

          <div className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80">
            <span className="text-xs font-mono font-bold text-rose-500 line-through block mb-1">Calendly / Acuity</span>
            <span className="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" /> Cora Booking Calendar
            </span>
          </div>

          <div className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80">
            <span className="text-xs font-mono font-bold text-rose-500 line-through block mb-1">Zoho / FreshBooks</span>
            <span className="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" /> Cora 18% GST Invoicing
            </span>
          </div>
        </div>
      </section>

      {/* ── Bottom Call To Action ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        <div className="w-full rounded-[36px] bg-gradient-to-br from-[#0F172A] via-[#1E293B] to-[#0A0D12] text-white p-8 sm:p-14 text-center relative overflow-hidden border border-zinc-800 shadow-xl">
          <div className="relative z-10 max-w-[680px] mx-auto space-y-6">
            <h2 className="font-display text-3xl sm:text-4xl font-bold tracking-tight">
              Ready to run your studio on Cora OS?
            </h2>
            <p className="text-zinc-400 text-sm sm:text-base leading-relaxed font-normal">
              Activate your workspace now with 1,000 free operations and full access to all 20 built modules. No credit card required.
            </p>

            <div className="flex items-center justify-center flex-wrap gap-3.5 pt-2">
              <a
                href="https://app.heycora.in/workspace/login?source=features_bottom"
                className="inline-flex items-center gap-2 bg-white text-zinc-950 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm group"
              >
                <span>Get started for Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-600 group-hover:translate-x-0.5 transition-transform" />
              </a>

              <a
                href="mailto:dravya.bansal@heycora.in?subject=Studio%20Onboarding%20Inquiry"
                className="inline-flex items-center gap-2 bg-zinc-900 text-white border border-zinc-700 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
              >
                <span>Chat with Founder</span>
              </a>
            </div>
          </div>
        </div>
      </section>

    </main>
  );
}
