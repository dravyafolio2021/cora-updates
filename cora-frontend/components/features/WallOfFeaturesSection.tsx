'use client';

import React, { useState } from 'react';
import Image from 'next/image';
import {
  MessageSquare,
  ShieldCheck,
  Receipt,
  Calendar,
  TrendingUp,
  Sparkles,
  FileText,
  Users,
  CheckCircle2,
  Lock,
  Zap,
  Globe,
  Clock,
  Filter,
  Layers,
  ArrowRight,
  Plus,
  QrCode,
  Share2,
  FolderLock,
  FileSpreadsheet,
  Building,
  Briefcase,
  Compass,
  Search,
  Bell,
  Sliders,
  Smartphone,
  Cpu,
  RefreshCw,
  Terminal,
} from 'lucide-react';

export function WallOfFeaturesSection() {
  const [hoveredTile, setHoveredTile] = useState<string | null>(null);

  return (
    <section
      id="wall-of-features"
      className="py-20 sm:py-28 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100"
    >
      <div className="w-full max-w-[1360px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. Section Header (Matching ClickUp Wall Reference 1:1) ── */}
        <div className="max-w-[860px] mx-auto text-center mb-14 sm:mb-20">
          <div className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-4 border border-zinc-200/80 shadow-2xs">
            <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
            <span>THE ALL-IN-ONE ECOSYSTEM</span>
          </div>

          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[52px] font-bold text-zinc-950 leading-[1.08] tracking-[-0.03em] mb-4">
            All workflows, AI Co-Founders, <br className="hidden sm:inline" />
            and humans in Cora
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[620px] mx-auto">
            40+ built-in modules to replace fragmented software &amp; maximize creative business productivity.
          </p>
        </div>

        {/* ── 2. ClickUp-Style Dense Wall of Feature Tiles & Rich Interactive Cards ── */}
        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4 max-w-[1300px] mx-auto">
          
          {/* Tile 1 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <MessageSquare className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">WhatsApp Sync</span>
          </div>

          {/* Tile 2 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <Receipt className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">18% GST Engine</span>
          </div>

          {/* Large Preview Card 1: Projects & Briefs (Spans 2 cols, 2 rows) */}
          <div className="col-span-2 row-span-2 rounded-3xl bg-gradient-to-b from-[#F9F9FA] to-[#F1F2F4] border border-zinc-200/90 p-6 flex flex-col justify-between shadow-xs relative overflow-hidden group">
            <div className="space-y-2">
              <div className="flex items-center justify-between">
                <div className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-zinc-200 text-zinc-900 text-xs font-bold shadow-2xs">
                  <span className="w-2 h-2 rounded-full bg-sky-500 animate-pulse" />
                  <span>Projects &amp; Briefs</span>
                </div>
                <span className="text-[10px] font-bold text-zinc-400">Live Intake</span>
              </div>
              <h3 className="font-bold text-lg text-zinc-950">
                Automated Client Pipeline
              </h3>
            </div>

            {/* Mockup UI Widget */}
            <div className="bg-white rounded-2xl p-4 border border-zinc-200/80 shadow-sm space-y-2 my-3">
              <div className="flex items-center justify-between text-xs pb-1.5 border-b border-zinc-100">
                <span className="font-semibold text-zinc-800">Lakme Fashion Campaign</span>
                <span className="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">Hold Oct 24</span>
              </div>
              <div className="flex items-center justify-between text-xs">
                <span className="text-zinc-500 text-[11px]">Commercial Rate Card</span>
                <span className="font-mono font-bold text-zinc-900">₹1,41,600 (Incl GST)</span>
              </div>
            </div>

            <div className="flex items-center justify-between text-xs font-semibold text-zinc-500 pt-1">
              <span>Zero manual data entry</span>
              <span className="text-zinc-950 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                Explore <ArrowRight className="w-3.5 h-3.5" />
              </span>
            </div>
          </div>

          {/* Tile 3 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <ShieldCheck className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">SHA-256 E-Sign</span>
          </div>

          {/* Tile 4 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <QrCode className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">Dynamic UPI QR</span>
          </div>

          {/* Tile 5 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <Calendar className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">Slot Holds</span>
          </div>

          {/* Tile 6 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <Users className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">Crew Dispatch</span>
          </div>

          {/* Large Preview Card 2: Contracts & Legal NDAs (Spans 2 cols, 2 rows) */}
          <div className="col-span-2 row-span-2 rounded-3xl bg-gradient-to-b from-[#F9F9FA] to-[#F1F2F4] border border-zinc-200/90 p-6 flex flex-col justify-between shadow-xs relative overflow-hidden group">
            <div className="space-y-2">
              <div className="flex items-center justify-between">
                <div className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-zinc-200 text-zinc-900 text-xs font-bold shadow-2xs">
                  <span className="w-2 h-2 rounded-full bg-purple-500" />
                  <span>Docs &amp; E-Sign</span>
                </div>
                <span className="text-[10px] font-bold text-zinc-400">IT Act Valid</span>
              </div>
              <h3 className="font-bold text-lg text-zinc-950">
                Legally Binding NDAs &amp; Terms
              </h3>
            </div>

            {/* Mockup UI Widget */}
            <div className="bg-white rounded-2xl p-4 border border-zinc-200/80 shadow-sm space-y-2 my-3">
              <div className="flex items-center justify-between text-xs">
                <div className="flex items-center gap-2">
                  <ShieldCheck className="w-4 h-4 text-emerald-600" />
                  <span className="font-semibold text-zinc-800">Commercial Usage Rights</span>
                </div>
                <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">Signed</span>
              </div>
              <div className="text-[11px] text-zinc-500 font-mono">
                SHA-256: 8f9a2b...c3d4e5
              </div>
            </div>

            <div className="flex items-center justify-between text-xs font-semibold text-zinc-500 pt-1">
              <span>50% advance deposit protection</span>
              <span className="text-zinc-950 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                Explore <ArrowRight className="w-3.5 h-3.5" />
              </span>
            </div>
          </div>

          {/* Tile 7 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <TrendingUp className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">Live Ledger</span>
          </div>

          {/* Tile 8 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <FileSpreadsheet className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">Tally Sync</span>
          </div>

          {/* Tile 9 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <Clock className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">Call-Sheets</span>
          </div>

          {/* Tile 10 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <FolderLock className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">Media Vault</span>
          </div>

          {/* Tile 11 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <Globe className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">Client Portals</span>
          </div>

          {/* Tile 12 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <Filter className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">Hold Filters</span>
          </div>

          {/* Tile 13 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <Zap className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">Automations</span>
          </div>

          {/* Tile 14 */}
          <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/70 hover:border-zinc-300 hover:bg-white hover:shadow-xs transition-all flex flex-col items-center justify-center text-center gap-2.5 h-[110px] group cursor-pointer">
            <Lock className="w-5 h-5 text-zinc-600 group-hover:text-zinc-950 transition-colors" />
            <span className="text-xs font-semibold text-zinc-700 group-hover:text-zinc-950">Watermark Lock</span>
          </div>

        </div>

      </div>
    </section>
  );
}
