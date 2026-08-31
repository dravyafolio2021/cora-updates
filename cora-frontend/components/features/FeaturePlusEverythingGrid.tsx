'use client';

import React from 'react';
import { FeatureModule } from '@/lib/features-data';
import { 
  ClaudeLogo, 
  GeminiLogo, 
  OpenAILogo, 
  WhatsAppLogo, 
  UPILogo, 
  GoogleCalendarLogo, 
  SonyCinemaLogo, 
  DaVinciResolveLogo, 
  ITActSealLogo, 
  TallyZohoLogo, 
  SlackLogo, 
  StripeLogo, 
  CloudflareLogo, 
  AppleLogo, 
  MetaLogo, 
  GoogleReviewsLogo, 
  GSTCouncilLogo 
} from './OfficialBrandLogos';

interface FeaturePlusEverythingGridProps {
  feature: FeatureModule;
}

interface CapabilityItem {
  title: string;
  badge: string;
  badgeColor?: string;
  description: string;
  OfficialLogo: React.ComponentType<{ className?: string }>;
  logoBg: string;
}

export function FeaturePlusEverythingGrid({ feature }: FeaturePlusEverythingGridProps) {
  
  // ── DYNAMIC GENERATION BASED ON ACTIVE MODULE & CATEGORY ──
  const getDynamicItems = (feat: FeatureModule): CapabilityItem[] => {
    
    // 1. INTELLIGENCE MODULES (AI Co-Founder, Content AI, RAG MCP, Voice to Scope)
    if (feat.category === 'intelligence') {
      return [
        {
          title: 'Anthropic Claude 3.5 Sonnet',
          badge: 'Deep Reasoning',
          description: 'Synthesizes complex legal contracts, shooting scopes, and rate cards with full context awareness.',
          OfficialLogo: ClaudeLogo,
          logoBg: 'bg-amber-50 border-amber-200'
        },
        {
          title: 'Google Gemini 3.5 Flash',
          badge: 'Sub-80ms Voice',
          description: 'Instantly transcribes multi-lingual audio voice briefs into structured line-item shoot deliverables.',
          OfficialLogo: GeminiLogo,
          logoBg: 'bg-blue-50 border-blue-200'
        },
        {
          title: 'OpenAI GPT-4o Schema Dispatch',
          badge: 'Deterministic JSON',
          description: 'Extracts validated JSON schemas for direct CRM status updates and multi-channel API executions.',
          OfficialLogo: OpenAILogo,
          logoBg: 'bg-emerald-50 border-emerald-200'
        },
        {
          title: 'Official WhatsApp Cloud API',
          badge: 'Verified Business',
          description: 'Dispatches instant client quotes, shoot brief confirmations, and approval buttons directly to WhatsApp.',
          OfficialLogo: WhatsAppLogo,
          logoBg: 'bg-emerald-50 border-emerald-200'
        },
        {
          title: 'NPCI Dynamic UPI QR Code',
          badge: 'Instant Advance',
          description: 'Embeds dynamic payment QR codes with exact 50% milestone values for zero-fee immediate settlement.',
          OfficialLogo: UPILogo,
          logoBg: 'bg-green-50 border-green-200'
        },
        {
          title: 'Section 10A IT Act Digital Seal',
          badge: 'Court Admissible',
          description: 'Generates SHA-256 cryptographic envelopes meeting Indian Information Technology Act standards.',
          OfficialLogo: ITActSealLogo,
          logoBg: 'bg-emerald-50 border-emerald-200'
        },
        {
          title: 'Sony Cinema & FX6 Inventory',
          badge: 'Hardware Lock',
          description: 'Directly checks gear serial registry to guarantee zero camera or lighting kit double-bookings.',
          OfficialLogo: SonyCinemaLogo,
          logoBg: 'bg-zinc-100 border-zinc-300'
        },
        {
          title: 'Google Calendar 2-Way Sync',
          badge: 'CalDAV Hub',
          description: 'Coordinates multi-location studio floors and automatically sets tentative holds for client review.',
          OfficialLogo: GoogleCalendarLogo,
          logoBg: 'bg-blue-50 border-blue-200'
        },
        {
          title: 'GSTN Portal SAC 998314',
          badge: '18% Tax Ledger',
          description: 'Auto-calculates intra-state CGST+SGST or inter-state IGST with one-click export for your CA.',
          OfficialLogo: GSTCouncilLogo,
          logoBg: 'bg-teal-50 border-teal-200'
        }
      ];
    }

    // 2. SALES & CRM MODULES (Lead CRM, Canvas Builder, Form Builder, Review Portal)
    if (feat.category === 'sales') {
      return [
        {
          title: 'WhatsApp Business Cloud',
          badge: 'Automated 2-Way',
          description: 'Captures and replies to commercial shoot inquiries with customized interactive rate card menus.',
          OfficialLogo: WhatsAppLogo,
          logoBg: 'bg-emerald-50 border-emerald-200'
        },
        {
          title: 'Meta Ads & Instagram Direct',
          badge: 'Instant Ingest',
          description: 'Directly feeds Instagram DM and Meta ad lead forms into your visual deal Kanban pipeline.',
          OfficialLogo: MetaLogo,
          logoBg: 'bg-blue-50 border-blue-200'
        },
        {
          title: 'Google Reviews & Business Hub',
          badge: 'Automated 5-Star',
          description: 'Triggers automated review requests upon successful project completion and final RAW delivery.',
          OfficialLogo: GoogleReviewsLogo,
          logoBg: 'bg-amber-50 border-amber-200'
        },
        {
          title: 'NPCI UPI & Payment Gateway',
          badge: 'Token Collection',
          description: 'Collects non-refundable date reservation tokens directly inside interactive quote links.',
          OfficialLogo: UPILogo,
          logoBg: 'bg-green-50 border-green-200'
        },
        {
          title: 'Anthropic Claude AI Deal Scoring',
          badge: 'Win Probability',
          description: 'Evaluates inquiry budgets, client company size, and history to prioritize high-value commercial deals.',
          OfficialLogo: ClaudeLogo,
          logoBg: 'bg-amber-50 border-amber-200'
        },
        {
          title: 'Section 10A IT Act Digital Vault',
          badge: 'E-Sign Registry',
          description: 'Attaches court-admissible commercial master agreements to every CRM deal card with SHA-256 hashes.',
          OfficialLogo: ITActSealLogo,
          logoBg: 'bg-emerald-50 border-emerald-200'
        },
        {
          title: 'Google Calendar Scheduling',
          badge: 'Zero Conflict',
          description: 'Enables prospective brands to book discovery calls with automated CalDAV calendar holds.',
          OfficialLogo: GoogleCalendarLogo,
          logoBg: 'bg-blue-50 border-blue-200'
        },
        {
          title: 'Stripe Global Card Settlement',
          badge: 'International Wire',
          description: 'Accepts multi-currency payments from international agencies with automated FX conversion.',
          OfficialLogo: StripeLogo,
          logoBg: 'bg-indigo-50 border-indigo-200'
        },
        {
          title: 'Slack Team Deal Notifications',
          badge: 'Real-time Webhook',
          description: 'Broadcasts instant channel alerts when high-value client contracts are signed and advances paid.',
          OfficialLogo: SlackLogo,
          logoBg: 'bg-pink-50 border-pink-200'
        }
      ];
    }

    // 3. OPERATIONS MODULES (E-Sign Vault, Crew Dispatch, Master Calendar, Task Board)
    if (feat.category === 'operations') {
      return [
        {
          title: 'Section 10A & 65B IT Act 2000',
          badge: 'Court Admissible',
          description: 'Generates legally binding electronic signatures with immutable SHA-256 audit logs in Indian courts.',
          OfficialLogo: ITActSealLogo,
          logoBg: 'bg-emerald-50 border-emerald-200'
        },
        {
          title: 'WhatsApp Crew Broadcast',
          badge: 'Instant Call Sheets',
          description: 'Sends personalized call times, GPS locations, and gear assignments directly to crew smartphones.',
          OfficialLogo: WhatsAppLogo,
          logoBg: 'bg-emerald-50 border-emerald-200'
        },
        {
          title: 'Google Calendar & Apple CalDAV',
          badge: 'Real-time Sync',
          description: 'Syncs studio shoot stages and sound stages across iOS, Android, and desktop calendar systems.',
          OfficialLogo: GoogleCalendarLogo,
          logoBg: 'bg-blue-50 border-blue-200'
        },
        {
          title: 'Sony Cinema Line Asset Registry',
          badge: 'Conflict Prevention',
          description: 'Tracks Sony FX6, FX3, and Venice serial numbers to prevent double-booking across concurrent shoots.',
          OfficialLogo: SonyCinemaLogo,
          logoBg: 'bg-zinc-100 border-zinc-300'
        },
        {
          title: 'Blackmagic DaVinci Resolve Studio',
          badge: 'Post-Production',
          description: 'Exports timeline clip markers and grading notes mapped directly from client task approval boards.',
          OfficialLogo: DaVinciResolveLogo,
          logoBg: 'bg-rose-50 border-rose-200'
        },
        {
          title: 'NPCI UPI Crew Payouts',
          badge: 'Daily Wage Settlement',
          description: 'Dispatches instant per-diem and freelance crew payments upon call sheet wrap confirmation.',
          OfficialLogo: UPILogo,
          logoBg: 'bg-green-50 border-green-200'
        },
        {
          title: 'Cloudflare Zero-Egress Storage',
          badge: 'Edge CDN',
          description: 'Distributes high-speed call sheet PDFs and shoot moodboards with sub-20ms global edge latency.',
          OfficialLogo: CloudflareLogo,
          logoBg: 'bg-orange-50 border-orange-200'
        },
        {
          title: 'Anthropic Claude Workflow Engine',
          badge: 'Conflict Resolver',
          description: 'Automatically detects DOP schedule overlaps and suggests optimal alternate crew allocations.',
          OfficialLogo: ClaudeLogo,
          logoBg: 'bg-amber-50 border-amber-200'
        },
        {
          title: 'Apple iOS & Android PWA',
          badge: 'Push Alerts',
          description: 'Delivers native push notifications for emergency call-time updates and location changes.',
          OfficialLogo: AppleLogo,
          logoBg: 'bg-zinc-100 border-zinc-300'
        }
      ];
    }

    // 4. FINANCE & ASSET MODULES (GST Invoicing, Asset Gear)
    if (feat.category === 'finance') {
      return [
        {
          title: 'GSTN Government SAC 998314',
          badge: 'Indian Tax Filing',
          description: 'Automatically splits 9% CGST + 9% SGST or 18% IGST based on client state jurisdiction.',
          OfficialLogo: GSTCouncilLogo,
          logoBg: 'bg-teal-50 border-teal-200'
        },
        {
          title: 'NPCI Dynamic UPI QR Engine',
          badge: 'Zero MDR Fee',
          description: 'Generates bank-linked dynamic QR codes on every PDF invoice for instant direct-to-account settlement.',
          OfficialLogo: UPILogo,
          logoBg: 'bg-green-50 border-green-200'
        },
        {
          title: 'Tally Prime & Zoho Books Sync',
          badge: 'CA-Ready Export',
          description: 'Exports unified XML and CSV sales daybooks ready for one-click import into Tally ERP and Zoho.',
          OfficialLogo: TallyZohoLogo,
          logoBg: 'bg-sky-50 border-sky-200'
        },
        {
          title: 'WhatsApp Payment Reminders',
          badge: 'Automated Follow-up',
          description: 'Sends polite automated WhatsApp payment nudges with 1-click UPI links before invoices become overdue.',
          OfficialLogo: WhatsAppLogo,
          logoBg: 'bg-emerald-50 border-emerald-200'
        },
        {
          title: 'Stripe International Multi-Currency',
          badge: 'Global Billing',
          description: 'Invoices international brands in USD, EUR, and GBP with real-time tax compliance and auto-conversion.',
          OfficialLogo: StripeLogo,
          logoBg: 'bg-indigo-50 border-indigo-200'
        },
        {
          title: 'Section 10A IT Act Court Seal',
          badge: 'Legal Proof',
          description: 'Cryptographically seals every tax invoice and payment receipt with SHA-256 court admissibility.',
          OfficialLogo: ITActSealLogo,
          logoBg: 'bg-emerald-50 border-emerald-200'
        },
        {
          title: 'Sony & Profoto Asset Depreciation',
          badge: 'Equipment Audit',
          description: 'Tracks camera body actuations, maintenance schedules, and balance-sheet asset depreciation values.',
          OfficialLogo: SonyCinemaLogo,
          logoBg: 'bg-zinc-100 border-zinc-300'
        },
        {
          title: 'Anthropic Claude Financial Forecast',
          badge: 'Cashflow AI',
          description: 'Predicts studio monthly revenue and identifies outstanding receivables with aging bucket analysis.',
          OfficialLogo: ClaudeLogo,
          logoBg: 'bg-amber-50 border-amber-200'
        },
        {
          title: 'Cloudflare Secure Invoice Vault',
          badge: 'Encrypted PDF',
          description: 'Protects client financial documents with 256-bit AES encryption and tamper-evident download tracking.',
          OfficialLogo: CloudflareLogo,
          logoBg: 'bg-orange-50 border-orange-200'
        }
      ];
    }

    // 5. PLATFORM & INFRASTRUCTURE MODULES (Media Hub, RBAC, Email SMTP, PWA, Docs, Super Admin)
    return [
      {
        title: 'Apple iOS & Android Mobile PWA',
        badge: 'Native Experience',
        description: 'Instant zero-lag mobile dashboard installation with offline caching and background sync.',
        OfficialLogo: AppleLogo,
        logoBg: 'bg-zinc-100 border-zinc-300'
      },
      {
        title: 'Cloudflare Edge CDN Storage',
        badge: 'Zero Egress Fees',
        description: 'Streams and stores 8K RAW footage, proxies, and project master archives with global CDN delivery.',
        OfficialLogo: CloudflareLogo,
        logoBg: 'bg-orange-50 border-orange-200'
      },
      {
        title: 'Official WhatsApp Cloud Platform',
        badge: 'Meta Certified',
        description: 'Connects official studio WhatsApp accounts with automated webhook routing and customer chat.',
        OfficialLogo: WhatsAppLogo,
        logoBg: 'bg-emerald-50 border-emerald-200'
      },
      {
        title: 'Anthropic Claude & Gemini Mesh',
        badge: 'Multi-LLM Failover',
        description: 'Routes queries with sub-80ms automatic fallback across Claude 3.5 Sonnet and Gemini Flash.',
        OfficialLogo: ClaudeLogo,
        logoBg: 'bg-amber-50 border-amber-200'
      },
      {
        title: 'Section 10A IT Act Digital Vault',
        badge: 'SHA-256 Seal',
        description: 'Ensures electronic contract integrity, client signatures, and audit logs are 100% court-admissible.',
        OfficialLogo: ITActSealLogo,
        logoBg: 'bg-emerald-50 border-emerald-200'
      },
      {
        title: 'NPCI UPI Instant Settlement',
        badge: 'Real-time Ledger',
        description: 'Direct banking integration for instant milestone reconciliation and zero-chargeback collection.',
        OfficialLogo: UPILogo,
        logoBg: 'bg-green-50 border-green-200'
      },
      {
        title: 'Blackmagic DaVinci Resolve Workflow',
        badge: 'Studio Integration',
        description: 'Deep integration with post-production color grading timelines and frame-accurate review notes.',
        OfficialLogo: DaVinciResolveLogo,
        logoBg: 'bg-rose-50 border-rose-200'
      },
      {
        title: 'Google Workspace & CalDAV Sync',
        badge: 'Enterprise Calendar',
        description: 'Coordinates bookings, crew rosters, and client discovery calls with bi-directional calendar holds.',
        OfficialLogo: GoogleCalendarLogo,
        logoBg: 'bg-blue-50 border-blue-200'
      },
      {
        title: 'GSTN Indian Tax Compliance',
        badge: 'SAC 998314',
        description: 'Full statutory compliance with automated 18% CGST/SGST breakdowns and CA-ready sales ledger.',
        OfficialLogo: GSTCouncilLogo,
        logoBg: 'bg-teal-50 border-teal-200'
      }
    ];
  };

  const dynamicItems = getDynamicItems(feature);

  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
      
      {/* Central Headline */}
      <div className="text-center max-w-[760px] mx-auto mb-12 sm:mb-16">
        <h2 className="font-display text-3xl sm:text-5xl font-bold tracking-tight bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block">
          Plus, everything you need to get it done
        </h2>
      </div>

      {/* 3x3 Dynamic Feature Matrix with Official Vector Brand Logos */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        {dynamicItems.map((item, idx) => {
          const Logo = item.OfficialLogo;
          return (
            <div 
              key={idx}
              className="p-6 rounded-2xl bg-white border border-zinc-200/80 hover:border-zinc-300 hover:shadow-xs hover:-translate-y-0.5 transition-all flex items-start gap-4 group"
            >
              <div className={`w-11 h-11 rounded-xl ${item.logoBg} border flex items-center justify-center p-2.5 shrink-0 group-hover:scale-105 transition-transform shadow-2xs`}>
                <Logo className="w-6 h-6" />
              </div>
              <div className="space-y-1.5 min-w-0">
                <div className="flex items-center gap-2 flex-wrap">
                  <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950 group-hover:text-zinc-800 transition-colors">
                    {item.title}
                  </h3>
                  <span className="text-[10px] font-mono font-semibold px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-600 border border-zinc-200/80">
                    {item.badge}
                  </span>
                </div>
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
