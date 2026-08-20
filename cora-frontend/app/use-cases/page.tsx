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
  title: 'Industry Solutions & Use Cases — Cora Studio OS',
  description: 'Discover how commercial photography studios, real estate media agencies, film production houses, and solo creators automate operations with Cora.',
  alternates: {
    canonical: 'https://heycora.in/use-cases/',
  },
};

const INDUSTRIES = [
  {
    id: 'commercial-studios',
    title: 'Commercial Photography Studios',
    subtitle: 'From Client Brief to 18% GST Invoicing with Zero Manual Spreadsheets',
    desc: 'Commercial photo shoots require juggling model releases, studio bay bookings, styling crew call times, and complex corporate purchase orders. Cora automates the entire administrative pipeline so your team can focus on creating world-class imagery.',
    metrics: [
      { value: '+40%', label: 'Faster shoot-to-invoice cycle' },
      { value: '18% GST', label: 'Automated tax math & HSN/SAC tracking' },
      { value: 'Zero', label: 'Lost model release agreements' }
    ],
    features: [
      'Multi-seat crew access for lighting techs, producers, and assistants',
      'Automated legally binding model & location releases signed on mobile',
      'Direct UPI QR and corporate card invoicing with automated tax receipts',
      'Integrated Google Drive RAW asset gallery proofing and approvals'
    ],
    image: '/images/usecase_commercial_studio.jpg',
    badge: 'Photography Studios'
  },
  {
    id: 'real-estate-media',
    title: 'Real Estate Media Agencies',
    subtitle: 'AI Property Copywriting, Drone Dispatch & Same-Day 4K Asset Handoff',
    desc: 'High-velocity real estate media demands instant turnaround. Cora integrates multi-model AI to draft MLS listing descriptions from photos, dispatches drone pilots with WhatsApp call-sheets, and locks deliverables behind instant payment gates.',
    metrics: [
      { value: '3X Faster', label: 'Listing description generation' },
      { value: 'Sub-400ms', label: 'Low-latency AI property copy' },
      { value: '100%', label: 'Payment collected before download' }
    ],
    features: [
      'Claude 3.5 & Gemini 2.0 Flash automated real estate listing descriptions',
      'Automated WhatsApp pilot dispatch with GPS pin and gate code notes',
      'Pay-to-unlock 4K virtual tour & high-res image download portals',
      'Automated broker 5-star Google review collection on final delivery'
    ],
    image: '/images/usecase_realestate_agency.jpg',
    badge: 'Real Estate Media'
  },
  {
    id: 'film-production',
    title: 'Film & Commercial Video Production Houses',
    subtitle: 'High-Ticket Client Retainers, SHA-256 E-Signs & Automated Call-Sheets',
    desc: 'Commercial video productions involve high stakes, multi-day shoots, and large budgets. Cora ensures every retainer milestone is escrowed, call-sheets are delivered via WhatsApp, and legal contracts have tamper-evident cryptographic validity.',
    metrics: [
      { value: '₹4.8Cr+', label: 'In production retainers protected' },
      { value: 'SHA-256', label: 'Cryptographic signature verification' },
      { value: '20+ Hrs', label: 'Producer time saved per shoot' }
    ],
    features: [
      'Multi-stage payment milestones (Advance, Production Wrap, Final Cut)',
      'Digital call-sheet generator with weather, call-times, and department notes',
      'Tamper-evident legal e-signatures compliant with Indian IT Act 2000',
      'Automated crew payout calculations with TDS and GST compliance'
    ],
    image: '/images/usecase_production_house.jpg',
    badge: 'Production Houses'
  },
  {
    id: 'solo-creators',
    title: 'Solo Creators & Lead Photographers',
    subtitle: 'Run Your Full Creative Business from Your Phone',
    desc: 'You started your photography or filmmaking career to create art, not to drown in accounting and email chains. Cora turns your mobile phone into an autonomous executive assistant that drafts scopes, signs clients, and tracks revenue.',
    metrics: [
      { value: '2 Minutes', label: 'Setup time to send your first proposal' },
      { value: '20+ Hrs', label: 'Reclaimed founder time every week' },
      { value: '₹0', label: 'Spent on separate CRM and e-sign apps' }
    ],
    features: [
      'One-tap proposal generation from voice notes and WhatsApp inquiries',
      'Mobile client contract signing with instant SMS & WhatsApp confirmations',
      'Instant UPI payment collection with zero accounting math needed',
      'PWA mobile app that works offline and on set'
    ],
    image: '/images/usecase_solo_creator.jpg',
    badge: 'Solo Creators'
  },
  {
    id: 'fashion-editorial',
    title: 'Fashion & Editorial Labs',
    subtitle: 'Model Releases, Stylist Call Times & Bay Rentals in Sync',
    desc: 'Editorial shoots require tight synchronization between agency bookers, stylists, makeup artists, and studio managers. Cora unifies bay booking schedules, wardrobe checklists, and image usage licensing agreements into a clean live pipeline.',
    metrics: [
      { value: '100%', label: 'Model usage rights digitally archived' },
      { value: 'Zero', label: 'Double-booked studio bay conflicts' },
      { value: 'Instant', label: 'Digital sign-off on wardrobe & prop releases' }
    ],
    features: [
      'Custom image usage rights and exclusivity contract templates',
      'Studio bay rental calendar with hourly and full-day slots',
      'Wardrobe, stylist, and makeup artist call-sheet dispatch',
      'White-label mood board and editorial client presentation portals'
    ],
    image: '/images/bento_crew_camera.jpg',
    badge: 'Fashion & Editorial'
  },
  {
    id: 'creative-agencies',
    title: 'Creative Marketing Agencies',
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
    image: '/images/cora_community_crowd.jpg',
    badge: 'Creative Agencies'
  }
];

export default function UseCasesPage() {
  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-20 overflow-hidden bg-white">
      
      {/* ── Top Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-24 sm:mb-32">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <span className="w-2 h-2 rounded-full bg-emerald-500" />
          <span>Industry Solutions</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[860px] mx-auto mb-5">
          Purpose-built for modern creative operators
        </h1>

        <p className="text-zinc-600 text-base sm:text-xl font-normal leading-relaxed max-w-[680px] mx-auto mb-8">
          Whether you run a 20-person commercial video production house or shoot high-end editorial campaigns solo, Cora is tailored to your exact workflow.
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

      {/* ── Bottom Section CTA ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mt-28 sm:mt-36">
        <div className="w-full rounded-[36px] bg-zinc-950 text-white p-8 sm:p-14 text-center relative overflow-hidden border border-zinc-800 shadow-xl">
          <div className="relative z-10 max-w-[680px] mx-auto space-y-6">
            <h2 className="font-display text-3xl sm:text-4xl font-bold tracking-tight">
              Scale your studio without the administrative overhead
            </h2>
            <p className="text-zinc-400 text-sm sm:text-base leading-relaxed font-normal">
              Join 1,200+ creative agencies and photography studios who have transitioned to Cora.
            </p>

            <div className="flex items-center justify-center flex-wrap gap-3.5 pt-2">
              <a
                href="https://app.heycora.in/workspace/login?source=usecases_bottom"
                className="inline-flex items-center gap-2 bg-white text-zinc-950 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm group"
              >
                <span>Get started for Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-600 group-hover:translate-x-0.5 transition-transform" />
              </a>

              <a
                href="mailto:dravya.bansal@heycora.in?subject=Industry%20Inquiry%20from%20Cora"
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
