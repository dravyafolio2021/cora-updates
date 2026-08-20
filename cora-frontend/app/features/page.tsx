import React from 'react';
import type { Metadata } from 'next';
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
  Smartphone
} from 'lucide-react';

export const metadata: Metadata = {
  title: 'Features & Architecture — Cora Studio OS',
  description: 'Explore the 5 core operational pillars of Cora: Multi-Model AI Brain, Mobile E-Signs, Automated 18% GST Invoicing, WhatsApp Dispatch, and Cloud Media Vault.',
  alternates: {
    canonical: 'https://heycora.in/features/',
  },
};

const FEATURE_PILLARS = [
  {
    id: 'ai-brain',
    pillar: '01 / FRONTIER AI ORCHESTRATION',
    title: 'Multi-Model AI Brain with Zero API Keys',
    desc: 'Never juggle multiple AI subscriptions again. Cora automatically routes complex commercial contracts to Anthropic Claude 3.5 Sonnet, ultra-fast listing copy to Google Gemini 2.0 Flash, and JSON extraction to OpenAI GPT-4o.',
    badges: ['Claude 3.5 Sonnet', 'Gemini 2.0 Flash', 'GPT-4o', 'Sub-400ms Routing'],
    points: [
      'Intelligent dynamic routing based on prompt complexity and token size',
      'Automated shoot proposal drafting from WhatsApp voice notes & client messages',
      'Multilingual property & commercial shoot copy generation in 12+ languages',
      'Zero per-model API setup — enterprise frontier AI included out of the box'
    ],
    image: '/images/bento_ai_seo.jpg',
    stat: '1.4M+',
    statLabel: 'AI studio operations executed monthly'
  },
  {
    id: 'esign-vault',
    pillar: '02 / DIGITAL CONTRACTS & VAULT',
    title: 'Legally Binding E-Signatures & Document Vault',
    desc: 'Issue tamper-evident client agreements, model releases, and crew NDAs that sign in seconds on mobile without requiring any app download. Backed by immutable SHA-256 cryptographic audit logs.',
    badges: ['SHA-256 Hash', 'IT Act 2000 Ready', 'Audit Trails', 'Mobile Sign-Off'],
    points: [
      'One-tap mobile signature capture with verified signee IP & timestamps',
      'Fully compliant with the Indian Information Technology Act 2000 & global e-sign laws',
      'Automated counter-signatures and watermarked PDF certificate generation',
      'Centralized encrypted vault for lifetime contract storage and instant retrieval'
    ],
    image: '/images/bento_esign_seal.jpg',
    stat: '100%',
    statLabel: 'Legal compliance with audit certificates'
  },
  {
    id: 'gst-invoicing',
    pillar: '03 / REVENUE & COMPLIANCE',
    title: 'Automated 18% GST Invoicing & Tax Math',
    desc: 'Eliminate quarterly accounting headaches. Cora automatically splits CGST, SGST, and IGST according to client state jurisdictions, validates GSTIN numbers, and collects payments via instant UPI QR and corporate cards.',
    badges: ['18% GST Split', 'Instant UPI QR', 'Razorpay & Stripe', 'Auto HSN/SAC'],
    points: [
      'Automated intra-state (CGST + SGST) vs inter-state (IGST) tax calculation',
      'Direct payment links with Google Pay, PhonePe, Paytm, RuPay, and corporate cards',
      'One-click export for CA quarterly filing (GSTR-1 & GSTR-3B formatted summaries)',
      'Automated payment milestone releases and retainer hold locks'
    ],
    image: '/images/bento_gst_upi.jpg',
    stat: '₹4.8Cr+',
    statLabel: 'In commercial invoices settled on Cora'
  },
  {
    id: 'dispatch-engine',
    pillar: '04 / LOGISTICS & DISPATCH',
    title: 'Automated WhatsApp Call-Sheet & Crew Dispatch',
    desc: 'Keep your entire production crew in sync without manual messaging chaos. Cora auto-generates call-sheets with location pins, call-times, gear checklists, and dispatches them directly via WhatsApp.',
    badges: ['WhatsApp Cloud API', 'Google Calendar Sync', 'Location Pins', 'Real-Time Alerts'],
    points: [
      'Official Meta Cloud WhatsApp API integration for instant call-sheet delivery',
      'Two-way Google Calendar synchronization for bay bookings and call times',
      'Automated reminder triggers sent 24h and 2h prior to shoot call times',
      'Crew confirmation tracking with live status indicators in your dashboard'
    ],
    image: '/images/bento_crew_camera.jpg',
    stat: '20+ Hrs',
    statLabel: 'Reclaimed founder & coordinator time weekly'
  },
  {
    id: 'media-vault',
    pillar: '05 / ASSET DELIVERY',
    title: 'Encrypted 4K RAW Media Vault & Client Approvals',
    desc: 'Deliver client photo and video deliverables with professional white-label asset galleries. Restrict access until payment milestones are cleared, and capture client selection approvals in real time.',
    badges: ['Google Drive Direct', '4K & RAW Ready', 'Watermarked Previews', 'Pay-to-Unlock'],
    points: [
      'Direct integration with Google Drive and AWS S3 for zero-storage markup',
      'Automated deliverable unlock upon successful UPI / Stripe payment receipt',
      'Client asset proofing with one-click image approval and revision markers',
      'Automated 5-star Google review requests triggered upon final download'
    ],
    image: '/images/bento_website_canvas.jpg',
    stat: '3X Faster',
    statLabel: 'Deliverable handoff and client sign-off'
  }
];

export default function FeaturesPage() {
  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-20 overflow-hidden bg-white">
      
      {/* ── Top Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-24 sm:mb-32">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <span className="w-2 h-2 rounded-full bg-emerald-500" />
          <span>Platform Architecture</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[840px] mx-auto mb-5">
          Engineered for autonomous studio execution
        </h1>

        <p className="text-zinc-600 text-base sm:text-xl font-normal leading-relaxed max-w-[660px] mx-auto mb-8">
          From initial inbound inquiry to AI scoping, tamper-evident e-signs, 18% GST settlement, and WhatsApp crew dispatch in one command center.
        </p>

        <div className="flex items-center justify-center flex-wrap gap-3.5">
          <a
            href="https://app.heycora.in/workspace/login?source=features_hero"
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

      {/* ── 5 Feature Pillars In-Depth ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 space-y-24 sm:space-y-32">
        {FEATURE_PILLARS.map((pillar, idx) => {
          const isEven = idx % 2 === 1;

          return (
            <div
              key={pillar.id}
              id={pillar.id}
              className={`grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center ${
                isEven ? 'lg:flex-row-reverse' : ''
              }`}
            >
              {/* Text Column */}
              <div className={`lg:col-span-6 space-y-6 ${isEven ? 'lg:order-2' : 'lg:order-1'}`}>
                <div className="space-y-3">
                  <span className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono">
                    {pillar.pillar}
                  </span>
                  
                  <h2 className="font-display text-2xl xs:text-3xl sm:text-[38px] font-bold text-zinc-950 leading-[1.15] tracking-tight">
                    {pillar.title}
                  </h2>

                  <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed">
                    {pillar.desc}
                  </p>
                </div>

                {/* Badge Tags */}
                <div className="flex flex-wrap gap-2 pt-1">
                  {pillar.badges.map((b, i) => (
                    <span
                      key={i}
                      className="px-3 py-1 bg-zinc-100 rounded-lg text-xs font-semibold text-zinc-800 border border-zinc-200/80"
                    >
                      {b}
                    </span>
                  ))}
                </div>

                {/* Bullet Points */}
                <div className="space-y-2.5 pt-2 border-t border-zinc-200/80">
                  {pillar.points.map((pt, i) => (
                    <div key={i} className="flex items-start gap-2.5 text-xs sm:text-sm font-medium text-zinc-700">
                      <span className="text-emerald-600 font-bold text-base leading-none">›</span>
                      <span>{pt}</span>
                    </div>
                  ))}
                </div>

                {/* Stat Highlight Card */}
                <div className="bg-zinc-50 rounded-2xl p-4 sm:p-5 border border-zinc-200/90 flex items-center justify-between shadow-2xs">
                  <div>
                    <div className="font-display text-2xl sm:text-3xl font-extrabold text-zinc-950 tracking-tight">
                      {pillar.stat}
                    </div>
                    <div className="text-xs text-zinc-500 font-medium mt-0.5">
                      {pillar.statLabel}
                    </div>
                  </div>

                  <div className="w-10 h-10 rounded-xl bg-white border border-zinc-200/90 flex items-center justify-center text-zinc-900 shadow-2xs">
                    <CheckCircle2 className="w-5 h-5 text-emerald-600" />
                  </div>
                </div>
              </div>

              {/* Visual Card Column */}
              <div className={`lg:col-span-6 flex justify-center ${isEven ? 'lg:order-1' : 'lg:order-2'}`}>
                <div className="w-full h-[380px] sm:h-[460px] rounded-[36px] overflow-hidden relative border border-zinc-200/90 shadow-[0px_20px_50px_rgba(0,0,0,0.06)] group">
                  <Image
                    src={pillar.image}
                    alt={pillar.title}
                    fill
                    sizes="(max-width: 768px) 100vw, 600px"
                    className="object-cover object-center group-hover:scale-105 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none" />
                  
                  {/* Bottom Float Tag */}
                  <div className="absolute bottom-6 inset-x-6 z-10">
                    <div className="bg-zinc-950/85 backdrop-blur-md text-white rounded-xl px-5 py-3 text-xs font-semibold flex items-center justify-between border border-zinc-700/60 shadow-xs">
                      <span>{pillar.title}</span>
                      <span className="text-emerald-400 font-mono text-[11px]">ACTIVE</span>
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
              Ready to automate your studio operations?
            </h2>
            <p className="text-zinc-400 text-sm sm:text-base leading-relaxed font-normal">
              Get started in under 2 minutes. Receive 1,000 complimentary AI runs, full document e-signs, and GST invoicing without a credit card.
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
                href="mailto:dravya.bansal@heycora.in?subject=Features%20Inquiry%20from%20Cora"
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
