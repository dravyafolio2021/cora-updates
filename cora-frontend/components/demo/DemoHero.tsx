'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  Play, 
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
  Camera,
  Film
} from 'lucide-react';

interface DemoHeroProps {
  onOpenDrawer: () => void;
}

export function DemoHero({ onOpenDrawer }: DemoHeroProps) {
  const [activeTab, setActiveTab] = useState<'pipeline' | 'calendar' | 'gst' | 'vault'>('pipeline');
  const [isPlayingVideo, setIsPlayingVideo] = useState(false);

  return (
    <section className="relative pt-12 sm:pt-16 pb-16 sm:pb-24 overflow-hidden">
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── Top Hero Copy ── */}
        <div className="text-center max-w-[820px] mx-auto space-y-5 mb-10 sm:mb-14">
          
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
          <p className="font-sans text-base sm:text-lg md:text-xl text-zinc-600 font-normal leading-relaxed max-w-[640px] mx-auto">
            Explore the complete autonomous solution for modern creative studios, production houses, and agency teams—no signup or credit card required.
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
            <span><strong>2,500+</strong> studio founders &amp; production crews trust Cora</span>
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
                { id: 'pipeline', label: 'Pipeline & CRM', icon: Layers },
                { id: 'calendar', label: 'Call Sheets & Slots', icon: Calendar },
                { id: 'gst', label: '18% GST Invoicing', icon: DollarSign },
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
                    <h3 className="font-display text-xl font-bold text-zinc-950">Commercial Shoot Pipeline</h3>
                    <p className="text-xs text-zinc-500 font-normal">Active production projects across multi-camera sets &amp; campaign deliverables.</p>
                  </div>
                  <div className="flex items-center gap-2">
                    <span className="px-3 py-1 rounded-lg bg-zinc-200/80 text-zinc-800 text-xs font-mono font-medium">
                      Total Pipeline: ₹34.8 Lakhs
                    </span>
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                  {[
                    {
                      stage: 'New Discovery Scope',
                      count: '3 Deals',
                      items: [
                        { client: 'Nykaa Beauty', project: 'Summer Glow Film', val: '₹4.5L', tag: 'AI Scope Gen' },
                        { client: 'Zepto Labs', project: 'Brand Campaign Shoot', val: '₹2.8L', tag: 'Awaiting Sign' }
                      ]
                    },
                    {
                      stage: 'Shoot Booked',
                      count: '2 Active',
                      items: [
                        { client: 'Boat Audio', project: 'Studio Bay 1 & 2', val: '₹6.2L', tag: 'Call Sheets Sent' },
                        { client: 'Myntra Studio', project: 'Spring Catalog E-Comm', val: '₹3.9L', tag: 'Crew Confirmed' }
                      ]
                    },
                    {
                      stage: 'Post-Production',
                      count: '4 In Progress',
                      items: [
                        { client: 'Licious', project: 'Commercial TVC Cut', val: '₹8.0L', tag: 'Color Grading' },
                        { client: 'Cult.Fit', project: 'Hero Training Reel', val: '₹3.4L', tag: 'VFX Review' }
                      ]
                    },
                    {
                      stage: 'Invoiced & Completed',
                      count: '6 Delivered',
                      items: [
                        { client: 'Tata CliQ Luxury', project: 'Watch Campaign 4K', val: '₹6.0L', tag: 'GST Paid • Instant UPI' }
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
                    <h3 className="font-display text-xl font-bold text-zinc-950">Dynamic Call-Time &amp; Studio Bay Schedule</h3>
                    <p className="text-xs text-zinc-500 font-normal">Real-time studio bay occupancy, call sheet dispatches, and equipment rosters.</p>
                  </div>
                  <span className="px-3 py-1 rounded-lg bg-zinc-200/80 text-zinc-800 text-xs font-mono font-medium">
                    Today: 4 Bays Booked • 0 Conflicts
                  </span>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                  <div className="bg-white rounded-2xl p-5 border border-zinc-200 shadow-2xs space-y-3">
                    <div className="flex items-center justify-between text-xs font-mono text-zinc-400">
                      <span>STUDIO BAY A (CYC WALL)</span>
                      <span className="text-emerald-600 font-bold">IN SESSION</span>
                    </div>
                    <h4 className="font-display font-bold text-zinc-950 text-base">Vogue India Editorial Shoot</h4>
                    <p className="text-xs text-zinc-600">Call-Time: 08:30 AM | Wrap: 06:00 PM</p>
                    <div className="p-3 bg-zinc-50 rounded-xl text-xs space-y-1 font-mono text-zinc-600">
                      <div>Director: Rohan Sen</div>
                      <div>Cam: Sony FX9 + Cooke Primes</div>
                      <div>Status: Call sheets confirmed by 14 crew</div>
                    </div>
                  </div>

                  <div className="bg-white rounded-2xl p-5 border border-zinc-200 shadow-2xs space-y-3">
                    <div className="flex items-center justify-between text-xs font-mono text-zinc-400">
                      <span>STUDIO BAY B (PRODUCT SET)</span>
                      <span className="text-zinc-700 font-bold">CALL TIME 02:00 PM</span>
                    </div>
                    <h4 className="font-display font-bold text-zinc-950 text-base">D2C Beverage Commercial 4K</h4>
                    <p className="text-xs text-zinc-600">Call-Time: 02:00 PM | Wrap: 09:30 PM</p>
                    <div className="p-3 bg-zinc-50 rounded-xl text-xs space-y-1 font-mono text-zinc-600">
                      <div>Lead: Ananya Iyer</div>
                      <div>Lighting: 4x Aputure 600d Pro Grid</div>
                      <div>Status: Automated WhatsApp reminder sent</div>
                    </div>
                  </div>

                  <div className="bg-white rounded-2xl p-5 border border-zinc-200 shadow-2xs space-y-3">
                    <div className="flex items-center justify-between text-xs font-mono text-zinc-400">
                      <span>EDIT SUITE 01</span>
                      <span className="text-purple-700 font-bold">LOCKED &amp; RENDERING</span>
                    </div>
                    <h4 className="font-display font-bold text-zinc-950 text-base">Zomato TVC Master Cut</h4>
                    <p className="text-xs text-zinc-600">DaVinci Color Grade + Dolby 5.1 Mix</p>
                    <div className="p-3 bg-zinc-50 rounded-xl text-xs space-y-1 font-mono text-zinc-600">
                      <div>Editor: Tanya Varma</div>
                      <div>Deliverable: 9:16 + 16:9 ProRes 4444</div>
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
                    <p className="text-xs text-zinc-500 font-normal">Real-time SAC 998311 tax splits, automated CGST/SGST/IGST, and instant QR settlement.</p>
                  </div>
                  <span className="px-3 py-1 rounded-lg bg-zinc-200/80 text-zinc-800 text-xs font-mono font-medium">
                    SAC Code: 998311 (Photography &amp; Video Production)
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
                        <span>Commercial Production Fee (3 Days)</span>
                        <span className="font-semibold text-zinc-950">₹3,50,000.00</span>
                      </div>
                      <div className="flex justify-between text-zinc-600">
                        <span>Studio Bay &amp; Lighting Grip Rental</span>
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
                      <span>ZERO AR CHAOS</span>
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
                    <p className="text-xs text-zinc-500 font-normal">Legally binding master service agreements, commercial usage rights, and non-disclosure certificates.</p>
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
                    <h4 className="font-display font-bold text-zinc-950 text-base">Commercial Model Release &amp; Broadcast Rights</h4>
                    <p className="text-xs text-zinc-600">Client: Spotify India | Talent: 6 Featured Artists</p>
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
                    <h4 className="font-display font-bold text-zinc-950 text-base">Master Production Services &amp; Cancellation Terms</h4>
                    <p className="text-xs text-zinc-600">Client: Red Bull Media House | Project: Mountain Series</p>
                    <div className="p-3 bg-zinc-50 rounded-xl space-y-1 text-xs font-mono text-zinc-600">
                      <div>Sent to: procurement@redbull.com</div>
                      <div>Clauses: 50% Non-Refundable Advance, Weather Day Policy</div>
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
                <span>Request personalized studio configuration</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
