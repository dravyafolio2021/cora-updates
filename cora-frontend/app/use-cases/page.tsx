import React from 'react';
import type { Metadata } from 'next';
import Image from 'next/image';
import { 
  Camera, 
  Building2, 
  Film, 
  User, 
  Sparkles, 
  ArrowRight, 
  CheckCircle2, 
  Zap, 
  Clapperboard, 
  Layers,
  Clock,
  TrendingUp,
  ShieldCheck
} from 'lucide-react';

export const metadata: Metadata = {
  title: 'Industry Solutions & Use Cases — Cora OS for Professional Services',
  description: 'Discover how software agencies, law firms, CA & tax practices, consulting firms, marketing agencies, and media studios automate operations with Cora.',
  alternates: {
    canonical: 'https://heycora.in/use-cases/',
  },
};

const INDUSTRIES = [
  {
    id: 'software-agencies',
    title: 'Software & Tech Agencies',
    subtitle: 'Sprint Retainers, Fixed-Scope Milestones & Code Review Vaults',
    desc: 'Dev agencies and IT consultancies need to manage sprint contracts, milestone escrow approvals, SLA tracking, and recurring client retainers. Cora streamlines your entire delivery pipeline from scoping to 18% GST invoicing with zero manual spreadsheets.',
    metrics: [
      { value: '3X Faster', label: 'Proposal to signed contract' },
      { value: '18% GST', label: 'Automated SAC 998314 software math' },
      { value: '100%', label: 'Milestone sign-off before deploy' }
    ],
    features: [
      'Sprint retainer agreements with automated recurring invoicing',
      'Milestone-gated client approval portals for builds and staging',
      'SLA uptime and maintenance tracking with priority escalation',
      'Cryptographic SHA-256 digital contracts compliant with IT Act 2000'
    ],
    image: '/images/cora_community_crowd.jpg',
    badge: 'Software & Tech Agencies'
  },
  {
    id: 'legal-practices',
    title: 'Law Firms & Legal Practices',
    subtitle: 'SHA-256 E-Sign Vaults, Retainer Agreements & Client Intake Portals',
    desc: 'Legal practices require airtight contract execution, tamper-evident document logs, and secure retainer billing. Cora provides IT Act compliant digital e-signatures, automated NDA workflows, and client intake vaults that save hundreds of admin hours every month.',
    metrics: [
      { value: 'SHA-256', label: 'Cryptographic signature verification' },
      { value: '100%', label: 'IT Act 2000 compliance validity' },
      { value: 'Zero', label: 'Paper contracts or lost agreements' }
    ],
    features: [
      '5-Step guided e-signature flow with audit trails & IP logging',
      'Encrypted client document vault for NDAs and retainer agreements',
      'Automated recurring legal retainer billing & GST tax receipts',
      'Custom intake forms with conditional logic for client onboarding'
    ],
    image: '/images/bento_crew_camera.jpg',
    badge: 'Legal & Law Practices'
  },
  {
    id: 'tax-accounting',
    title: 'Tax & Accounting Firms (CA Practices)',
    subtitle: '18% GST Auto-Splitting, SAC Audits & Client Retainer Automation',
    desc: 'Chartered accountants and tax advisory firms manage high-volume client compliance, monthly filings, and advisory retainers. Cora automates SAC code allocation, CGST/SGST tax math, and automated recurring fee collections.',
    metrics: [
      { value: '18% GST', label: 'Native CGST + SGST tax auto-calculation' },
      { value: 'SAC Codes', label: 'Pre-seeded professional accounting schemas' },
      { value: '+45%', label: 'Faster client invoice collection via UPI' }
    ],
    features: [
      'Automated SAC tax classification for accounting and tax services',
      'One-click UPI QR code and NEFT/RTGS payment collection',
      'Monthly recurring client retainer automation with PDF generation',
      'Secure document depository for tax filings and audit proofs'
    ],
    image: '/images/usecase_commercial_studio.jpg',
    badge: 'Tax & Accounting Firms'
  },
  {
    id: 'consulting',
    title: 'Management & Strategy Consulting',
    subtitle: 'Diagnostic Scopes, Advisory Retainers & Executive Deliverables',
    desc: 'Consulting firms deliver high-value advisory, audits, and transformation decks. Cora unifies proposal drafting, client retainer management, milestone approvals, and board deliverable sharing under one elegant workspace.',
    metrics: [
      { value: '₹5L+ Avg', label: 'Advisory retainer size supported' },
      { value: '2 Minutes', label: 'Scope generation from meeting notes' },
      { value: 'Multi-Seat', label: 'Role-based access for advisory teams' }
    ],
    features: [
      'Voice-to-scope meeting notes to structured advisory agreements',
      'Multi-stage payment milestones (Advance, Diagnostic Wrap, Board Review)',
      'White-label executive client presentation portals',
      'Multi-tenant security with complete workspace data isolation'
    ],
    image: '/images/usecase_production_house.jpg',
    badge: 'Management Consulting'
  },
  {
    id: 'creative-agencies',
    title: 'Digital Marketing & Creative Agencies',
    subtitle: 'Consolidate 5+ Fragmented SaaS Subscriptions into One Command Center',
    desc: 'Marketing and digital media agencies waste thousands of dollars each month across Notion, DocuSign, HoneyBook, Zapier, and ChatGPT accounts. Cora unifies client management, AI copywriting, and billing under a single branded operating system.',
    metrics: [
      { value: '₹1.8L / Mo', label: 'Saved in disconnected SaaS subscriptions' },
      { value: '1 Workspace', label: 'Replacing 5+ fragmented apps' },
      { value: 'Multi-Seat', label: 'Role-based access for your whole team' }
    ],
    features: [
      'Custom domain and white-label client portal branding',
      'Multi-model AI workspace with Claude 3.5 Sonnet, Gemini, and GPT-4o',
      'Client retainer automation with monthly auto-generated GST invoices',
      'Team permission tiers (Account Executive, Project Lead, Finance, Client)'
    ],
    image: '/images/usecase_realestate_agency.jpg',
    badge: 'Digital Marketing Agencies'
  },
  {
    id: 'architecture',
    title: 'Architecture & Engineering Studios',
    subtitle: '3D CAD Stage Milestones, Blueprint Approvals & Milestone Billing',
    desc: 'Architecture and engineering firms coordinate multi-phase design stages, client blueprint sign-offs, contractor draws, and milestone billings. Cora provides clear approval gates and automated progressive invoices.',
    metrics: [
      { value: '4-Stage', label: 'Concept to site completion milestone tracking' },
      { value: '100%', label: 'Signed client change-order protection' },
      { value: 'Zero', label: 'Uncollected milestone draws' }
    ],
    features: [
      'Progressive milestone billing tied to design phase completion',
      'Digital client change-order approvals with e-signatures',
      'Integrated document depository for blueprints and CAD specs',
      'Automated GST tax breakdown on architectural consultancy fees'
    ],
    image: '/images/usecase_solo_creator.jpg',
    badge: 'Architecture & Design'
  },
  {
    id: 'healthcare-clinics',
    title: 'Clinics & Specialized Healthcare Practices',
    subtitle: 'Client Intake Booking, Consent E-Signs & Confidential Records',
    desc: 'Specialized wellness practices, therapy centers, and clinical consultancies require automated booking, paperless intake forms, digital patient consent, and direct payment collection.',
    metrics: [
      { value: '100% Digital', label: 'Paperless intake forms and consent' },
      { value: 'Instant', label: 'Automated appointment confirmation' },
      { value: 'Encrypted', label: 'Confidential client record storage' }
    ],
    features: [
      'Dynamic client intake forms with mobile signature capture',
      'Automated appointment slot picker with WhatsApp confirmations',
      'Direct UPI and corporate payment links with zero friction',
      'Encrypted client profile vault with role-based staff permissions'
    ],
    image: '/images/usecase_commercial_studio.jpg',
    badge: 'Healthcare & Wellness'
  },
  {
    id: 'commercial-studios',
    title: 'Media & Commercial Production Studios',
    subtitle: 'From Client Brief to 18% GST Invoicing with Zero Manual Spreadsheets',
    desc: 'Commercial photo and video studios require juggling model releases, bay bookings, styling crew call times, and corporate purchase orders. Cora automates the entire administrative pipeline.',
    metrics: [
      { value: '+40%', label: 'Faster shoot-to-invoice cycle' },
      { value: '18% GST', label: 'Automated tax math & HSN/SAC tracking' },
      { value: 'Zero', label: 'Lost model release agreements' }
    ],
    features: [
      'Multi-seat crew access for lighting techs, producers, and assistants',
      'Automated legally binding model & location releases signed on mobile',
      'Direct UPI QR and corporate card invoicing with automated tax receipts',
      'Integrated asset gallery proofing and approvals'
    ],
    image: '/images/usecase_production_house.jpg',
    badge: 'Media & Production'
  }
];

export default function UseCasesPage() {
  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-20 overflow-hidden bg-white">
      
      {/* ── Top Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-24 sm:mb-32">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <span className="w-2 h-2 rounded-full bg-emerald-500" />
          <span>Professional Service Solutions</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[920px] mx-auto mb-5">
          Purpose-built for modern professional services &amp; agencies
        </h1>

        <p className="text-zinc-600 text-base sm:text-xl font-normal leading-relaxed max-w-[720px] mx-auto mb-8">
          Whether you run a software dev studio, law practice, CA firm, consulting advisory, or creative agency — Cora unifies your client intake, e-signatures, 18% GST billing, and team operations into one connected operating system.
        </p>

        <div className="flex items-center justify-center flex-wrap gap-3.5">
          <a
            href="https://app.heycora.in/workspace/login?source=usecases_hero"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm border border-zinc-800 group"
          >
            <span>Get started for Free</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
          </a>

          <a
            href="mailto:dravya.bansal@heycora.in?subject=Industry%20Inquiry%20from%20Cora"
            className="inline-flex items-center gap-2 bg-white text-zinc-950 border border-zinc-300 hover:border-zinc-400 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-50 transition-all shadow-2xs"
          >
            <span>Chat with Founder</span>
          </a>
        </div>
      </section>

      {/* ── 6 Industry Deep Dives ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 space-y-24 sm:space-y-32">
        {INDUSTRIES.map((ind, idx) => {
          const isEven = idx % 2 === 1;

          return (
            <div
              key={ind.id}
              id={ind.id}
              className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center"
            >
              {/* Text Column */}
              <div className={`lg:col-span-6 space-y-6 ${isEven ? 'lg:order-2' : 'lg:order-1'}`}>
                <div className="space-y-2.5">
                  <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 rounded-lg text-xs font-semibold text-zinc-800 border border-zinc-200/80">
                    <span>{ind.badge}</span>
                  </div>

                  <h2 className="font-display text-2xl xs:text-3xl sm:text-[36px] font-bold text-zinc-950 leading-[1.15] tracking-tight">
                    {ind.title}
                  </h2>

                  <p className="text-zinc-900 font-semibold text-sm sm:text-base">
                    {ind.subtitle}
                  </p>

                  <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed font-normal">
                    {ind.desc}
                  </p>
                </div>

                {/* 3 Metric Badges */}
                <div className="grid grid-cols-3 gap-3 pt-2">
                  {ind.metrics.map((m, i) => (
                    <div key={i} className="bg-zinc-50 rounded-xl p-3 border border-zinc-200/80 text-center">
                      <div className="font-display text-lg sm:text-xl font-bold text-zinc-950">{m.value}</div>
                      <div className="text-[10px] sm:text-[11px] text-zinc-500 font-medium leading-tight mt-0.5">{m.label}</div>
                    </div>
                  ))}
                </div>

                {/* Feature Checkmarks */}
                <div className="space-y-2 pt-2 border-t border-zinc-200/80">
                  {ind.features.map((feat, i) => (
                    <div key={i} className="flex items-start gap-2 text-xs sm:text-sm text-zinc-700 font-medium">
                      <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                      <span>{feat}</span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Visual Card Column */}
              <div className={`lg:col-span-6 flex justify-center ${isEven ? 'lg:order-1' : 'lg:order-2'}`}>
                <div className="w-full h-[380px] sm:h-[460px] rounded-[36px] overflow-hidden relative border border-zinc-200/90 shadow-[0px_20px_50px_rgba(0,0,0,0.06)] group">
                  <Image
                    src={ind.image}
                    alt={ind.title}
                    fill
                    sizes="(max-width: 768px) 100vw, 600px"
                    className="object-cover object-center group-hover:scale-105 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none" />
                  
                  {/* Bottom Float Tag */}
                  <div className="absolute bottom-6 inset-x-6 z-10">
                    <div className="bg-zinc-950/85 backdrop-blur-md text-white rounded-xl px-5 py-3 text-xs font-semibold flex items-center justify-between border border-zinc-700/60 shadow-xs">
                      <span>{ind.title}</span>
                      <span className="text-emerald-400 font-mono text-[11px]">VERIFIED WORKSPACE</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          );
        })}
      </section>

    </main>
  );
}
