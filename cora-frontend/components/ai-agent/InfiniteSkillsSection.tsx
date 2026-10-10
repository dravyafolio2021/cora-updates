'use client';

import React from 'react';
import Image from 'next/image';
import {
  Bot,
  Mic,
  PenTool,
  Database,
  FileText,
  Sparkles,
  Users,
  MessageSquare,
  Layout,
  Globe,
  Layers,
  Zap,
  Calendar,
  Send,
  Camera,
  FileCheck,
  ShieldCheck,
  Scale,
  Receipt,
  QrCode,
  CreditCard,
  RefreshCw,
  CheckCircle2,
  Shield,
} from 'lucide-react';

interface SkillItem {
  id: string;
  label: string;
  icon: React.ComponentType<{ className?: string }>;
}

// ── EXACT CORA PRODUCT FEATURES (LOCKED TO EXACTLY 4 CONTINUOUS ROWS) ──
const DESKTOP_SKILLS_ROWS: SkillItem[][] = [
  // Row 1: AI & Intelligence Features (Drifts Left)
  [
    { id: 'ai-cofounder', label: 'AI Co-Founder', icon: Bot },
    { id: 'voice-to-text', label: 'Voice to Text', icon: Mic },
    { id: 'ai-seo-content', label: 'AI SEO & Content', icon: PenTool },
    { id: 'ai-memory-search', label: 'AI Memory Search', icon: Database },
    { id: 'proposal-generator', label: 'Proposal & SOW Generator', icon: FileText },
    { id: 'listing-ai', label: 'Listing AI', icon: Sparkles },
  ],
  // Row 2: Sales & Client CRM Features (Drifts Right)
  [
    { id: 'lead-crm', label: 'Lead CRM', icon: Users },
    { id: 'whatsapp-cloud', label: 'WhatsApp Cloud Dispatch', icon: MessageSquare },
    { id: 'landing-pages', label: 'Landing Page Builder', icon: Layout },
    { id: 'client-portals', label: 'Client Portals', icon: Globe },
    { id: 'rate-cards', label: 'Rate Card Engine', icon: Layers },
    { id: 'instant-quotes', label: 'Instant Quotes', icon: Zap },
  ],
  // Row 3: Studio Operations & Legal Features (Drifts Left)
  [
    { id: 'calendar-holds', label: 'Studio Calendar Holds', icon: Calendar },
    { id: 'crew-call-sheets', label: 'Crew Call-Sheets', icon: Send },
    { id: 'gear-inventory', label: 'Gear Inventory Tracking', icon: Camera },
    { id: 'digital-contracts', label: 'Digital Contracts', icon: FileCheck },
    { id: 'esign-vault', label: 'SHA-256 E-Sign Vault', icon: ShieldCheck },
    { id: 'model-releases', label: 'Model Release Forms', icon: Scale },
  ],
  // Row 4: Finance, GST & Billing Features (Drifts Right)
  [
    { id: 'gst-invoicing', label: '18% GST Invoicing', icon: Receipt },
    { id: 'upi-qr', label: 'Dynamic UPI QR Standee', icon: QrCode },
    { id: 'expense-tracking', label: 'Expense Tracking', icon: CreditCard },
    { id: 'tally-zoho-sync', label: 'Tally & Zoho Sync', icon: RefreshCw },
    { id: 'auto-payments', label: 'Razorpay Auto-Payments', icon: CheckCircle2 },
    { id: 'multi-tenant-rbac', label: 'Multi-Tenant RBAC', icon: Shield },
  ],
];

// ── EXACT CORA PRODUCT FEATURES FOR MOBILE VIEW (4 COMPACT ROWS) ──
const MOBILE_SKILLS_ROWS: SkillItem[][] = [
  // Mobile Row 1 (Drifts Left)
  [
    { id: 'm-ai-cofounder', label: 'AI Co-Founder', icon: Bot },
    { id: 'm-voice-to-text', label: 'Voice to Text', icon: Mic },
    { id: 'm-lead-crm', label: 'Lead CRM', icon: Users },
    { id: 'm-whatsapp', label: 'WhatsApp Dispatch', icon: MessageSquare },
  ],
  // Mobile Row 2 (Drifts Right)
  [
    { id: 'm-gst-invoicing', label: '18% GST Invoicing', icon: Receipt },
    { id: 'm-upi-qr', label: 'Dynamic UPI QR', icon: QrCode },
    { id: 'm-calendar-holds', label: 'Studio Calendar Holds', icon: Calendar },
    { id: 'm-digital-contracts', label: 'Digital Contracts', icon: FileCheck },
  ],
  // Mobile Row 3 (Drifts Left)
  [
    { id: 'm-esign-vault', label: 'SHA-256 E-Sign Vault', icon: ShieldCheck },
    { id: 'm-call-sheets', label: 'Crew Call-Sheets', icon: Send },
    { id: 'm-ai-seo', label: 'AI SEO & Content', icon: PenTool },
    { id: 'm-ai-memory', label: 'AI Memory Search', icon: Database },
  ],
  // Mobile Row 4 (Drifts Right)
  [
    { id: 'm-client-portals', label: 'Client Portals', icon: Globe },
    { id: 'm-gear-inventory', label: 'Gear Inventory', icon: Camera },
    { id: 'm-tally-sync', label: 'Tally & Zoho Sync', icon: RefreshCw },
    { id: 'm-rate-cards', label: 'Rate Card Engine', icon: Layers },
  ],
];

export function InfiniteSkillsSection() {
  return (
    <section id="skills-constellation" className="py-16 sm:py-[100px] bg-white relative z-10 border-t border-zinc-100 overflow-hidden">
      
      {/* ── EMBEDDED SEAMLESS MARQUEE ANIMATION KEYFRAMES ── */}
      <style dangerouslySetInnerHTML={{ __html: `
        @keyframes coraDriftLeft {
          0% { transform: translate3d(0, 0, 0); }
          100% { transform: translate3d(-50%, 0, 0); }
        }
        @keyframes coraDriftRight {
          0% { transform: translate3d(-50%, 0, 0); }
          100% { transform: translate3d(0, 0, 0); }
        }
        .cora-marquee-track-left {
          display: flex;
          width: max-content;
          will-change: transform;
          animation: coraDriftLeft 42s linear infinite;
        }
        .cora-marquee-track-right {
          display: flex;
          width: max-content;
          will-change: transform;
          animation: coraDriftRight 42s linear infinite;
        }
        .cora-marquee-track-left-fast {
          display: flex;
          width: max-content;
          will-change: transform;
          animation: coraDriftLeft 34s linear infinite;
        }
        .cora-marquee-track-right-fast {
          display: flex;
          width: max-content;
          will-change: transform;
          animation: coraDriftRight 34s linear infinite;
        }
        .cora-marquee-track-left:hover,
        .cora-marquee-track-right:hover,
        .cora-marquee-track-left-fast:hover,
        .cora-marquee-track-right-fast:hover {
          animation-play-state: paused;
        }
      `}} />

      <div className="w-full max-w-[1360px] mx-auto px-4 sm:px-6">
        
        {/* Section Header */}
        <div className="max-w-[760px] mx-auto text-center mb-10 sm:mb-14 space-y-3">
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 rounded-full border border-zinc-200/80 text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-800 shadow-2xs">
            <Sparkles className="w-3.5 h-3.5 text-zinc-600" />
            <span>PRODUCT CAPABILITIES</span>
          </div>
          <h2 className="font-display text-3xl sm:text-4xl lg:text-[46px] font-bold text-zinc-950 tracking-tight leading-[1.12]">
            One workspace. All your features.
          </h2>
          <p className="text-zinc-600 text-sm sm:text-base max-w-[640px] mx-auto leading-relaxed">
            From client brief intake and 18% GST tax invoices to SHA-256 NDAs and studio hold scheduling &mdash; every core feature is built natively into your Cora workspace.
          </p>
        </div>

        {/* ── SHOWCASE CONTAINER WITH RESPONSIVE DESKTOP & MOBILE BACKGROUNDS ── */}
        <div className="relative w-full rounded-[28px] sm:rounded-[36px] lg:rounded-[44px] overflow-hidden border border-zinc-200/80 shadow-xl bg-[#FAF6EE]">
          
          {/* 1. Desktop Background Image (Horizontal Landscape) */}
          <div className="hidden md:block absolute inset-0 z-0 pointer-events-none">
            <Image
              src="/images/cora_agents_skills_bg_desktop.png"
              alt="Cora AI Super Agents Team"
              fill
              priority
              className="object-cover object-bottom select-none"
            />
          </div>

          {/* 2. Mobile Background Image (Vertical Portrait) */}
          <div className="block md:hidden absolute inset-0 z-0 pointer-events-none">
            <Image
              src="/images/cora_agents_skills_bg_mobile.jpg"
              alt="Cora AI Super Agents Team Mobile"
              fill
              priority
              className="object-cover object-bottom select-none"
            />
          </div>

          {/* ── 3. EXACT 4-ROW PRODUCT FEATURE PILLS (DESKTOP & TABLET) ── */}
          <div className="hidden md:block relative z-10 pt-10 sm:pt-12 lg:pt-14 pb-56 sm:pb-72 md:pb-80 lg:pb-[360px] xl:pb-[400px] overflow-hidden">
            <div className="flex flex-col gap-3 lg:gap-3.5 w-full">
              {DESKTOP_SKILLS_ROWS.map((row, rIdx) => {
                const isEven = rIdx % 2 === 0;
                const trackClass = isEven ? 'cora-marquee-track-left' : 'cora-marquee-track-right';
                // Repeat items for seamless continuous looping
                const loopItems = [...row, ...row, ...row, ...row];

                return (
                  <div key={`desktop-row-${rIdx}`} className="w-full overflow-hidden flex items-center">
                    <div className={`${trackClass} flex items-center gap-2.5 lg:gap-3 pr-2.5 lg:pr-3`}>
                      {loopItems.map((feature, sIdx) => {
                        const Icon = feature.icon;
                        return (
                          <div
                            key={`d-${rIdx}-${feature.id}-${sIdx}`}
                            className="inline-flex items-center gap-2 px-3.5 lg:px-4 py-2 lg:py-2.5 rounded-2xl text-xs lg:text-[13px] font-semibold bg-white text-zinc-950 border border-zinc-200/90 shadow-[0_2px_12px_rgba(0,0,0,0.08)] select-none shrink-0 cursor-default"
                          >
                            <Icon className="w-3.5 h-3.5 lg:w-4 lg:h-4 shrink-0 text-[#991b1b]" />
                            <span className="tracking-tight whitespace-nowrap">{feature.label}</span>
                          </div>
                        );
                      })}
                    </div>
                  </div>
                );
              })}
            </div>
          </div>

          {/* ── 4. EXACT 4-ROW PRODUCT FEATURE PILLS (MOBILE VIEW) ── */}
          <div className="block md:hidden relative z-10 pt-6 pb-[420px] xs:pb-[480px] overflow-hidden">
            <div className="flex flex-col gap-2 w-full">
              {MOBILE_SKILLS_ROWS.map((row, rIdx) => {
                const isEven = rIdx % 2 === 0;
                const trackClass = isEven ? 'cora-marquee-track-left-fast' : 'cora-marquee-track-right-fast';
                const loopItems = [...row, ...row, ...row, ...row];

                return (
                  <div key={`mobile-row-${rIdx}`} className="w-full overflow-hidden flex items-center">
                    <div className={`${trackClass} flex items-center gap-1.5 pr-1.5`}>
                      {loopItems.map((feature, sIdx) => {
                        const Icon = feature.icon;
                        return (
                          <div
                            key={`m-${rIdx}-${feature.id}-${sIdx}`}
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-semibold bg-white text-zinc-950 border border-zinc-200/90 shadow-[0_2px_8px_rgba(0,0,0,0.08)] select-none shrink-0 cursor-default"
                          >
                            <Icon className="w-3 h-3 shrink-0 text-[#991b1b]" />
                            <span className="tracking-tight whitespace-nowrap">{feature.label}</span>
                          </div>
                        );
                      })}
                    </div>
                  </div>
                );
              })}
            </div>
          </div>

        </div>

      </div>
    </section>
  );
}
