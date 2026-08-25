'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  ArrowRight, 
  Calendar, 
  FileText, 
  DollarSign, 
  Layers, 
  Bot, 
  CheckCircle2, 
  ShieldCheck, 
  Clock, 
  Sparkles,
  ExternalLink,
  ChevronRight,
  Star,
  Users,
  Building,
  Briefcase
} from 'lucide-react';

interface DemoHeroProps {
  onOpenDrawer: () => void;
}

export function DemoHero({ onOpenDrawer }: DemoHeroProps) {
  const [activeTab, setActiveTab] = useState<'pipeline' | 'calendar' | 'gst' | 'vault'>('pipeline');

  return (
    <section className="relative pt-12 sm:pt-16 pb-16 sm:pb-24 overflow-hidden">
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── Top Hero Copy ── */}
        <div className="text-center max-w-[840px] mx-auto space-y-5 mb-10 sm:mb-14">
          
          {/* Eyebrow Pill */}
          <div className="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-zinc-100 border border-zinc-200/80 text-xs font-mono font-medium text-zinc-700 shadow-2xs">
            <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
            <span className="uppercase tracking-wider">EXPLORE CORA</span>
          </div>

          {/* Master Heading */}
          <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl md:text-[68px] font-bold text-zinc-950 tracking-[-0.035em] leading-[1.08]">
            See Cora in action.
          </h1>

          {/* Subtitle */}
          <p className="font-sans text-base sm:text-lg md:text-xl text-zinc-600 font-normal leading-relaxed max-w-[680px] mx-auto">
            Experience the complete autonomous operating system for creative agencies, consultancies, commercial studios, and growing service businesses—no signup or credit card required.
          </p>

          {/* CTAs */}
          <div className="flex items-center justify-center flex-wrap gap-3.5 pt-2">
            <Link
              href="/workspace/login"
              className="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-zinc-950 text-white text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm"
            >
              <span>Get started today</span>
              <ArrowRight className="w-4 h-4" />
            </Link>

            <button
              type="button"
              onClick={onOpenDrawer}
              className="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl border border-zinc-200 bg-white text-zinc-900 text-sm font-semibold hover:bg-zinc-50 hover:border-zinc-300 transition-all shadow-2xs"
            >
              <span>Book a 1:1 demo</span>
            </button>
          </div>

          {/* Social Proof Bar */}
          <div className="flex items-center justify-center flex-wrap gap-2 sm:gap-3 pt-3 text-xs text-zinc-500 font-medium">
            <div className="flex items-center text-amber-500 gap-0.5">
              {[...Array(5)].map((_, i) => (
                <Star key={i} className="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
              ))}
            </div>
            <span><strong>2,500+</strong> business founders &amp; agency teams trust Cora</span>
            <span className="text-zinc-300 hidden sm:inline">•</span>
            <span className="font-mono text-[11px] text-zinc-400 uppercase tracking-wider hidden sm:inline">
              ISO 27001 • SOC 2 Ready • 100% Data Residency
            </span>
          </div>

        </div>

        {/* ── Interactive Command Center Showcase Window ── */}
        <div className="w-full max-w-[1100px] mx-auto rounded-3xl bg-white border border-zinc-200 shadow-2xl overflow-hidden">
          
          {/* macOS Titlebar & Tabs Bar */}
          <div className="bg-zinc-900 text-white px-4 sm:px-6 py-3.5 flex items-center justify-between border-b border-zinc-800">
            <div className="flex items-center gap-3">
              <div className="flex items-center gap-1.5">
                <span className="w-3 h-3 rounded-full bg-zinc-700" />
                <span className="w-3 h-3 rounded-full bg-zinc-700" />
                <span className="w-3 h-3 rounded-full bg-zinc-700" />
              </div>
              <div className="hidden sm:flex items-center gap-1.5 bg-zinc-800/80 px-3 py-1 rounded-lg text-xs font-mono text-zinc-300 border border-zinc-700/60">
                <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                <span>app.heycora.in/workspace/live-demo</span>
              </div>
            </div>

            {/* Interactive Tab Switcher */}
            <div className="flex items-center gap-1 bg-zinc-800/90 p-1 rounded-xl border border-zinc-700/80 overflow-x-auto scrollbar-none">
              {[
                { id: 'pipeline', label: 'Client Pipeline & CRM', icon: Layers },
                { id: 'calendar', label: 'Bookings & Calendar', icon: Calendar },
                { id: 'gst', label: '18% GST & Invoicing', icon: DollarSign },
                { id: 'vault', label: 'E-Sign Legal Vault', icon: ShieldCheck }
              ].map((tab) => {
                const Icon = tab.icon;
                const isActive = activeTab === tab.id;

                return (
                  <button
                    key={tab.id}
                    type="button"
                    onClick={() => setActiveTab(tab.id as any)}
                    className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all whitespace-nowrap ${
                      isActive
                        ? 'bg-white text-zinc-950 shadow-xs font-semibold'
                        : 'text-zinc-400 hover:text-white'
                    }`}
                  >
                    <Icon className="w-3.5 h-3.5" />
                    <span>{tab.label}</span>
                  </button>
                );
              })}
            </div>
          </div>

          {/* Interactive Screen Display */}
          <div className="p-5 sm:p-8 bg-zinc-50/60 min-h-[460px] flex flex-col justify-between">
            
            {/* TAB 1: PIPELINE & CRM */}
            {activeTab === 'pipeline' && (
              <div className="space-y-6 animate-in fade-in duration-300">
                <div className="flex items-center justify-between flex-wrap gap-4 border-b border-zinc-200 pb-4">
                  <div>
                    <h3 className="font-display text-xl font-bold text-zinc-950">Commercial Client Pipeline</h3>
                    <p className="text-xs text-zinc-500 font-normal">Cross-industry deals spanning creative retainers, advisory projects, and enterprise engagements.</p>
                  </div>
                  <div className="flex items-center gap-2">
                    <span className="px-3 py-1 rounded-lg bg-zinc-200/80 text-zinc-800 text-xs font-mono font-medium">
                      Active Pipeline: ₹42.6 Lakhs
                    </span>
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                  {[
                    {
                      stage: 'Discovery & AI Scoping',
                      count: '3 Deals',
                      items: [
                        { client: 'Zepto Labs', project: 'Growth Campaign Retainer', val: '₹4.5L', tag: 'AI Scope Gen' },
                        { client: 'DLF Luxury', project: 'Commercial Showcase Deal', val: '₹6.8L', tag: 'Awaiting Sign' }
                      ]
                    },
                    {
                      stage: 'Confirmed & Scheduled',
                      count: '2 Active',
                      items: [
                        { client: 'Nykaa Brands', project: 'Q3 Brand Campaign', val: '₹8.2L', tag: 'Contract Executed' },
                        { client: 'Urban Company', project: 'Advisory Consultation', val: '₹3.5L', tag: 'Milestone 1 Paid' }
                      ]
                    },
                    {
                      stage: 'In Execution',
                      count: '4 Projects',
                      items: [
                        { client: 'Licious D2C', project: 'Brand TVC & Digital Cut', val: '₹7.0L', tag: 'Reviewing Proofs' },
                        { client: 'Cult.Fit Wellness', project: 'Annual Creative Strategy', val: '₹5.4L', tag: 'Sprint 2 In Progress' }
                      ]
                    },
                    {
                      stage: 'Invoiced & Settled',
                      count: '8 Delivered',
                      items: [
                        { client: 'Tata CliQ Luxury', project: 'Enterprise Production Sprint', val: '₹9.2L', tag: 'GST Paid • Instant UPI' }
                      ]
                    }
                  ].map((col, idx) => (
                    <div key={idx} className="bg-white rounded-2xl p-4 border border-zinc-200/80 shadow-2xs space-y-3">
                      <div className="flex items-center justify-between border-b border-zinc-100 pb-2">
                        <span className="text-xs font-bold text-zinc-900">{col.stage}</span>
                        <span className="text-[10px] font-mono text-zinc-400 uppercase">{col.count}</span>
                      </div>
                      <div className="space-y-2.5">
                        {col.items.map((item, i) => (
                          <div key={i} className="p-3 rounded-xl bg-zinc-50 border border-zinc-100 space-y-1.5 hover:border-zinc-300 transition-colors">
                            <div className="flex items-center justify-between">
                              <span className="text-xs font-bold text-zinc-900">{item.client}</span>
                              <span className="text-xs font-mono font-semibold text-zinc-950">{item.val}</span>
                            </div>
                            <div className="text-[11px] text-zinc-500">{item.project}</div>
                            <span className="inline-block text-[10px] font-mono px-2 py-0.5 rounded-md bg-zinc-200/60 text-zinc-700">
                              {item.tag}
                            </span>
                          </div>
                        ))}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* TAB 2: CALL SHEETS & CALENDAR */}
            {activeTab === 'calendar' && (
              <div className="space-y-6 animate-in fade-in duration-300">
                <div className="flex items-center justify-between flex-wrap gap-4 border-b border-zinc-200 pb-4">
                  <div>
                    <h3 className="font-display text-xl font-bold text-zinc-950">Dynamic Booking &amp; Client Appointment Calendar</h3>
                    <p className="text-xs text-zinc-500 font-normal">Real-time scheduling for client strategy calls, on-site walkthroughs, and production sessions.</p>
                  </div>
                  <span className="px-3 py-1 rounded-lg bg-zinc-200/80 text-zinc-800 text-xs font-mono font-medium">
                    Today: 6 Sessions Confirmed • 0 Conflicts
                  </span>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                  <div className="bg-white rounded-2xl p-5 border border-zinc-200 shadow-2xs space-y-3">
                    <div className="flex items-center justify-between text-xs font-mono text-zinc-400">
                      <span>CLIENT STRATEGY SPRINT</span>
                      <span className="text-emerald-600 font-bold">IN SESSION</span>
                    </div>
                    <h4 className="font-display font-bold text-zinc-950 text-base">Fintech Brand Positioning &amp; GTM</h4>
                    <p className="text-xs text-zinc-600">Time: 10:00 AM - 11:30 AM IST</p>
                    <div className="p-3 bg-zinc-50 rounded-xl text-xs space-y-1 font-mono text-zinc-600">
                      <div>Lead: Partner Strategy Team</div>
                      <div>Format: Interactive Video Conference</div>
                      <div>Status: Notes auto-transcribed by Cora AI</div>
                    </div>
                  </div>

                  <div className="bg-white rounded-2xl p-5 border border-zinc-200 shadow-2xs space-y-3">
                    <div className="flex items-center justify-between text-xs font-mono text-zinc-400">
                      <span>ON-SITE COMMERCIAL SESSION</span>
                      <span className="text-zinc-700 font-bold">02:00 PM IST</span>
                    </div>
                    <h4 className="font-display font-bold text-zinc-950 text-base">Architectural Space Walkthrough</h4>
                    <p className="text-xs text-zinc-600">Time: 02:00 PM - 05:00 PM IST</p>
                    <div className="p-3 bg-zinc-50 rounded-xl text-xs space-y-1 font-mono text-zinc-600">
                      <div>Client: Prestige Estates Luxury Wing</div>
                      <div>Team: Lead Architect + 3 Spatial Reviewers</div>
                      <div>Status: Automated WhatsApp reminder sent</div>
                    </div>
                  </div>

                  <div className="bg-white rounded-2xl p-5 border border-zinc-200 shadow-2xs space-y-3">
                    <div className="flex items-center justify-between text-xs font-mono text-zinc-400">
                      <span>DELIVERABLE REVIEW</span>
                      <span className="text-purple-700 font-bold">READY FOR SIGNOFF</span>
                    </div>
                    <h4 className="font-display font-bold text-zinc-950 text-base">Annual Retainer Campaign Assets</h4>
                    <p className="text-xs text-zinc-600">Final Deliverable Milestone Verification</p>
                    <div className="p-3 bg-zinc-50 rounded-xl text-xs space-y-1 font-mono text-zinc-600">
                      <div>Stakeholder: VP of Marketing (Swiggy)</div>
                      <div>Deliverable: Master Creative Suite + 18% GST Bill</div>
                      <div>Status: Auto-synced to Client Vault</div>
                    </div>
                  </div>
                </div>
              </div>
            )}

            {/* TAB 3: 18% GST INVOICING */}
            {activeTab === 'gst' && (
              <div className="space-y-6 animate-in fade-in duration-300">
                <div className="flex items-center justify-between flex-wrap gap-4 border-b border-zinc-200 pb-4">
                  <div>
                    <h3 className="font-display text-xl font-bold text-zinc-950">Automated Indian GST &amp; UPI Invoicing Engine</h3>
                    <p className="text-xs text-zinc-500 font-normal">Real-time SAC professional service tax splits, automated CGST/SGST/IGST, and instant QR settlement.</p>
                  </div>
                  <span className="px-3 py-1 rounded-lg bg-zinc-200/80 text-zinc-800 text-xs font-mono font-medium">
                    SAC Code: 998311 / 998314 (Professional &amp; Creative Services)
                  </span>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                  <div className="md:col-span-7 bg-white rounded-2xl p-6 border border-zinc-200 shadow-2xs space-y-4">
                    <div className="flex justify-between items-center border-b border-zinc-100 pb-3">
                      <div>
                        <div className="text-xs font-mono text-zinc-400 uppercase">INVOICE #CORA-2026-8941</div>
                        <div className="text-sm font-bold text-zinc-950">Client: Reliance Brands Lifestyle</div>
                      </div>
                      <span className="px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-800 text-xs font-semibold">
                        PAID VIA UPI
                      </span>
                    </div>

                    <div className="space-y-2 text-xs font-mono">
                      <div className="flex justify-between text-zinc-600">
                        <span>Professional Creative &amp; Advisory Services</span>
                        <span className="font-semibold text-zinc-950">₹3,50,000.00</span>
                      </div>
                      <div className="flex justify-between text-zinc-600">
                        <span>Digital Asset Production &amp; Licensing</span>
                        <span className="font-semibold text-zinc-950">₹75,000.00</span>
                      </div>
                      <div className="flex justify-between text-zinc-600">
                        <span>CGST (9%)</span>
                        <span className="text-zinc-800">₹38,250.00</span>
                      </div>
                      <div className="flex justify-between text-zinc-600">
                        <span>SGST (9%)</span>
                        <span className="text-zinc-800">₹38,250.00</span>
                      </div>
                      <div className="border-t border-zinc-200 pt-2 flex justify-between text-sm font-bold text-zinc-950">
                        <span>Total Invoice Amount (Incl. 18% GST)</span>
                        <span>₹5,01,500.00</span>
                      </div>
                    </div>
                  </div>

                  <div className="md:col-span-5 bg-zinc-900 text-white rounded-2xl p-6 border border-zinc-800 space-y-4">
                    <div className="flex items-center gap-2 text-xs font-mono text-zinc-400">
                      <CheckCircle2 className="w-4 h-4 text-emerald-400" />
                      <span>ZERO AR HEADACHES</span>
                    </div>
                    <h4 className="font-display text-lg font-bold text-white">Instant UPI Settlement Rail</h4>
                    <p className="text-xs text-zinc-300 leading-relaxed font-normal">
                      Clients scan one dynamic QR code or pay via net-banking. Cora verifies transaction hashes instantly and marks milestone deliverables as unlocked.
                    </p>
                    <div className="pt-2 text-xs font-mono text-zinc-400">
                      Auto-TDS certificates generated &amp; filed automatically.
                    </div>
                  </div>
                </div>
              </div>
            )}

            {/* TAB 4: E-SIGN LEGAL VAULT */}
            {activeTab === 'vault' && (
              <div className="space-y-6 animate-in fade-in duration-300">
                <div className="flex items-center justify-between flex-wrap gap-4 border-b border-zinc-200 pb-4">
                  <div>
                    <h3 className="font-display text-xl font-bold text-zinc-950">Cryptographic E-Sign Legal Vault</h3>
                    <p className="text-xs text-zinc-500 font-normal">Legally binding master service agreements, retainer contracts, NDAs, and commercial usage certificates.</p>
                  </div>
                  <span className="px-3 py-1 rounded-lg bg-zinc-200/80 text-zinc-800 text-xs font-mono font-medium">
                    SHA-256 Audit Trail • IT Act 2000 Compliant
                  </span>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div className="bg-white rounded-2xl p-5 border border-zinc-200 shadow-2xs space-y-3">
                    <div className="flex items-center justify-between">
                      <span className="text-xs font-mono text-zinc-400">AGREEMENT #CORA-LGL-089</span>
                      <span className="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-bold">EXECUTED</span>
                    </div>
                    <h4 className="font-display font-bold text-zinc-950 text-base">Annual Master Service Agreement (MSA)</h4>
                    <p className="text-xs text-zinc-600">Client: Spotify India | Engagement: Enterprise Retainer</p>
                    <div className="p-3 bg-zinc-50 rounded-xl space-y-1 text-xs font-mono text-zinc-600">
                      <div>Signer: Legal Counsel (legal@spotify.com)</div>
                      <div>Timestamp: Today, 11:24:08 AM IST</div>
                      <div>Hash: <code className="text-zinc-950">e3b0c44298fc1c149afbf4c8...</code></div>
                    </div>
                  </div>

                  <div className="bg-white rounded-2xl p-5 border border-zinc-200 shadow-2xs space-y-3">
                    <div className="flex items-center justify-between">
                      <span className="text-xs font-mono text-zinc-400">AGREEMENT #CORA-LGL-090</span>
                      <span className="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[10px] font-bold">PENDING SIGNATURE</span>
                    </div>
                    <h4 className="font-display font-bold text-zinc-950 text-base">Commercial Scope of Work &amp; Retainer Terms</h4>
                    <p className="text-xs text-zinc-600">Client: Red Bull Media House | Project: Enterprise Campaign</p>
                    <div className="p-3 bg-zinc-50 rounded-xl space-y-1 text-xs font-mono text-zinc-600">
                      <div>Sent to: procurement@redbull.com</div>
                      <div>Clauses: 50% Milestone Advance, IP Licensing Terms</div>
                      <div>Action: Auto-reminder scheduled in 4 hours</div>
                    </div>
                  </div>
                </div>
              </div>
            )}

            {/* Bottom Floating Bar */}
            <div className="mt-8 pt-4 border-t border-zinc-200 flex items-center justify-between flex-wrap gap-4 text-xs font-mono text-zinc-500">
              <div className="flex items-center gap-2">
                <span className="w-2 h-2 rounded-full bg-emerald-500" />
                <span>Live Interactive Workspace Active</span>
              </div>
              <button
                type="button"
                onClick={onOpenDrawer}
                className="inline-flex items-center gap-1.5 font-sans font-semibold text-zinc-950 hover:underline"
              >
                <span>Request personalized workflow configuration</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
