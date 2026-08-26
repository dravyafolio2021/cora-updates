'use client';

import React from 'react';
import { 
  Sparkles, 
  Bot, 
  Check, 
  CheckCircle2, 
  Calendar, 
  Clock, 
  Paperclip, 
  User, 
  Users, 
  ArrowRight, 
  Layers, 
  ShieldCheck, 
  Lock, 
  FileText, 
  Receipt, 
  Camera, 
  Send, 
  HardDrive, 
  Cpu, 
  Radio, 
  Bell, 
  Smartphone, 
  Building, 
  Search, 
  Star, 
  Film,
  Zap,
  Sliders,
  CheckCheck,
  Mail,
  GitBranch,
  CreditCard,
  MessageCircle,
  Video,
  PhoneCall,
  FileSpreadsheet,
  TabletSmartphone
} from 'lucide-react';

interface ModuleCardVisualProps {
  slug: string;
  category?: string;
  title: string;
}

export function ModuleCardVisual({ slug, category, title }: ModuleCardVisualProps) {
  
  // ── 1. AI CO-FOUNDER (Peach/Coral Radiant Theme with Floating 3D Pills) ──
  if (slug === 'ai-cofounder') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FFF5F1] via-[#FFEAE3] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] flex flex-col items-center gap-2.5">
          {/* Top Pill with Avatar & Checkmark */}
          <div className="w-full bg-white/95 backdrop-blur-md rounded-2xl p-2.5 shadow-[0_8px_20px_rgba(255,107,74,0.12)] border border-white/80 flex items-center justify-between gap-2 transform -translate-y-1">
            <div className="flex items-center gap-2">
              <div className="w-6 h-6 rounded-lg bg-orange-500 text-white flex items-center justify-center font-bold text-[10px] shadow-2xs">
                <Bot className="w-3.5 h-3.5" />
              </div>
              <span className="text-[11px] font-bold text-zinc-900 truncate">Draft ₹2.5L Vogue Proposal</span>
            </div>
            <span className="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-bold">
              ✓
            </span>
          </div>

          {/* Main Vibrant Coral Floating Action Pill */}
          <div className="w-[94%] bg-gradient-to-r from-[#FF5E48] to-[#FF7A59] text-white rounded-2xl py-2.5 px-4 shadow-[0_12px_24px_rgba(255,94,72,0.35)] flex items-center justify-center gap-2 transform scale-105 group-hover:scale-110 transition-transform duration-300">
            <Sparkles className="w-4 h-4 text-amber-200 animate-pulse" />
            <span className="text-xs font-bold tracking-tight">Autonomous Operations</span>
          </div>

          {/* Bottom Angled Floating Tag Pills */}
          <div className="w-full flex items-center justify-between gap-2 px-1">
            <span className="px-2.5 py-1 rounded-xl bg-white/95 text-[10px] font-semibold text-zinc-700 shadow-sm border border-zinc-100 flex items-center gap-1">
              <span className="w-1.5 h-1.5 rounded-full bg-blue-500" />
              6-Tier Fallback
            </span>
            <span className="px-2.5 py-1 rounded-xl bg-white/95 text-[10px] font-semibold text-zinc-700 shadow-sm border border-zinc-100 flex items-center gap-1">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
              24/7 Active
            </span>
          </div>
        </div>
      </div>
    );
  }

  // ── 2. CONTENT AI (Warm Amber & Gold Creative Suite) ──
  if (slug === 'content-ai') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FFFDF2] via-[#FEF7D8] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] flex flex-col gap-2">
          <div className="w-full bg-white/95 backdrop-blur-md rounded-2xl p-3 shadow-[0_10px_25px_rgba(234,179,8,0.14)] border border-amber-100/80">
            <div className="flex items-center justify-between text-[10px] text-zinc-400 font-mono mb-1.5">
              <span className="font-bold text-amber-600 flex items-center gap-1">
                <Sparkles className="w-3 h-3" /> VIRAL REEL SCRIPT
              </span>
              <span>0:30s</span>
            </div>
            <p className="text-[11px] font-bold text-zinc-900 line-clamp-1 mb-2">
              &ldquo;Top 3 Wedding Lighting Setups in 2026&rdquo;
            </p>
            <div className="flex items-center gap-1.5">
              <span className="px-2 py-0.5 rounded-md bg-amber-50 text-[9px] font-bold text-amber-700 border border-amber-200/60">
                Hook: 0-3s
              </span>
              <span className="px-2 py-0.5 rounded-md bg-zinc-100 text-[9px] font-medium text-zinc-600">
                10x Reach
              </span>
            </div>
          </div>

          <div className="self-center bg-zinc-950 text-white px-4 py-1.5 rounded-full text-[10px] font-bold shadow-md flex items-center gap-1.5">
            <span>✨ 1-Click Multi-Format Export</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 3. RAG MCP (Deep Indigo / Neural Circuit Theme) ──
  if (slug === 'rag-mcp') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F5F3FF] via-[#ECE6FE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col items-center gap-2">
          <div className="flex items-center gap-2">
            <span className="px-3 py-1 bg-white rounded-xl text-[10px] font-bold text-purple-700 shadow-sm border border-purple-100 flex items-center gap-1">
              <Cpu className="w-3 h-3 text-purple-600" /> MCP Standard
            </span>
            <span className="text-purple-400 font-bold">⇄</span>
            <span className="px-3 py-1 bg-white rounded-xl text-[10px] font-bold text-indigo-700 shadow-sm border border-indigo-100 flex items-center gap-1">
              <Layers className="w-3 h-3 text-indigo-600" /> Vector Store
            </span>
          </div>

          <div className="w-full bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(124,58,237,0.12)] border border-purple-100/80 text-center">
            <div className="flex items-center justify-center gap-1.5 text-[11px] font-bold text-zinc-900 mb-1">
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              Living Studio Context Sync
            </div>
            <p className="text-[10px] text-zinc-500">
              Instant recall across all past shoot briefs &amp; rates
            </p>
          </div>
        </div>
      </div>
    );
  }

  // ── 4. VOICE TO SCOPE (Cyan / Audio Waveform Theme) ──
  if (slug === 'voice-to-scope') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0FDFA] via-[#CCFBF1] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col items-center gap-2.5">
          <div className="w-full bg-white/90 backdrop-blur-md rounded-2xl p-3 shadow-[0_8px_20px_rgba(13,148,136,0.12)] border border-teal-100 flex items-center justify-between gap-2">
            <div className="w-7 h-7 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-xs">
              <Radio className="w-3.5 h-3.5 animate-pulse" />
            </div>
            <div className="flex items-center gap-1 flex-1 justify-center h-5">
              <span className="w-1 h-3 bg-teal-400 rounded-full animate-bounce" />
              <span className="w-1 h-5 bg-teal-600 rounded-full" />
              <span className="w-1 h-2 bg-teal-300 rounded-full" />
              <span className="w-1 h-4 bg-teal-500 rounded-full" />
              <span className="w-1 h-5 bg-teal-600 rounded-full animate-bounce" />
              <span className="w-1 h-3 bg-teal-400 rounded-full" />
            </div>
            <span className="text-[10px] font-mono font-bold text-teal-700">01:42</span>
          </div>

          <div className="px-3 py-1.5 bg-zinc-950 text-white rounded-xl text-[10px] font-bold flex items-center gap-1.5 shadow-sm">
            <CheckCircle2 className="w-3.5 h-3.5 text-teal-300" />
            <span>Structured Brief &amp; Call Sheet Ready</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 5. LEAD CRM (Cool Sky Blue Kanban & Deals) ──
  if (slug === 'lead-crm') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F2F7FF] via-[#E4F0FE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] flex flex-col gap-2">
          <div className="flex items-center justify-between px-1">
            <span className="px-2.5 py-0.5 rounded-full bg-blue-100/90 text-blue-700 text-[10px] font-bold">
              + Inbound Lead
            </span>
            <div className="flex items-center gap-1 text-[10px] font-mono text-zinc-500">
              <span className="w-1.5 h-1.5 rounded-full bg-blue-500" /> Pipeline: ₹12.4L
            </div>
          </div>

          <div className="w-full bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(59,130,246,0.15)] border border-blue-100">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-400 mb-1">
              <span className="font-bold text-blue-600">● FASHION EDITORIAL</span>
              <span>Tomorrow</span>
            </div>
            <p className="text-xs font-bold text-zinc-900 mb-2.5">
              Lakmé Campaign Shoot
            </p>
            <div className="flex items-center justify-between pt-1 border-t border-zinc-100">
              <span className="text-xs font-mono font-bold text-zinc-900">₹1,85,000</span>
              <span className="px-2 py-0.5 rounded-md bg-emerald-50 text-[9px] font-bold text-emerald-700 border border-emerald-200/80">
                Contract Sent
              </span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 6. CANVAS BUILDER (Visual Designer & Layout Engine) ──
  if (slug === 'canvas-builder') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F8FAFC] via-[#EEF2F6] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(0,0,0,0.06)] border border-zinc-200/80">
          <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-100">
            <div className="flex items-center gap-1.5">
              <span className="w-2 h-2 rounded-full bg-rose-400" />
              <span className="w-2 h-2 rounded-full bg-amber-400" />
              <span className="w-2 h-2 rounded-full bg-emerald-400" />
            </div>
            <div className="flex items-center gap-1 bg-zinc-100 px-1.5 py-0.5 rounded-md text-[9px] font-mono text-zinc-600">
              <span>Desktop</span> ⌄
            </div>
          </div>
          <div className="space-y-1.5">
            <div className="w-full h-6 bg-zinc-100 rounded-lg flex items-center justify-center text-[9px] font-bold text-zinc-600">
              [ Hero Video Banner ]
            </div>
            <div className="grid grid-cols-2 gap-1.5">
              <div className="h-8 bg-zinc-50 border border-dashed border-zinc-200 rounded-lg flex items-center justify-center text-[8px] text-zinc-400">
                Pricing Grid
              </div>
              <div className="h-8 bg-zinc-950 text-white rounded-lg flex items-center justify-center text-[8px] font-bold">
                Book Shoot ↗
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 7. FORM BUILDER (Interactive Lead Capture Widget) ──
  if (slug === 'form-builder') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FDF4FF] via-[#FCE7F3] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(217,70,239,0.12)] border border-pink-100">
          <div className="flex items-center justify-between text-[10px] font-bold text-pink-600 mb-2">
            <span>Shoot Inquiry Form</span>
            <span className="text-[9px] font-mono text-zinc-400">&lt;iframe /&gt;</span>
          </div>
          <div className="space-y-1.5 text-[10px]">
            <div className="px-2.5 py-1.5 bg-zinc-50 rounded-lg border border-zinc-200 text-zinc-700">
              Full Name: <span className="font-bold text-zinc-900">Karan Mehta</span>
            </div>
            <div className="px-2.5 py-1.5 bg-zinc-50 rounded-lg border border-zinc-200 text-zinc-700 flex justify-between">
              <span>Shoot Type:</span>
              <span className="font-bold text-pink-600">Commercial Film ⌄</span>
            </div>
          </div>
          <div className="mt-2.5 w-full py-1.5 bg-zinc-950 text-white rounded-lg text-[10px] font-bold text-center">
            Submit &amp; Schedule
          </div>
        </div>
      </div>
    );
  }

  // ── 8. REVIEW PORTAL (5-Star Reputation & Proof Engine) ──
  if (slug === 'review-portal') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FFFBEB] via-[#FEF3C7] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col items-center gap-2">
          <div className="w-full bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(245,158,11,0.15)] border border-amber-100 text-center">
            <div className="flex items-center justify-center gap-1 text-amber-500 mb-1">
              {[...Array(5)].map((_, i) => (
                <Star key={i} className="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
              ))}
            </div>
            <p className="text-[11px] font-bold text-zinc-900 mb-0.5">
              5.0 • &ldquo;Outstanding 4K Production!&rdquo;
            </p>
            <span className="text-[9px] text-zinc-400">Google Verified Client</span>
          </div>

          <span className="px-3 py-1 bg-zinc-950 text-white rounded-full text-[9px] font-bold flex items-center gap-1 shadow-sm">
            <span>⚡ Automated WhatsApp Review Trigger</span>
          </span>
        </div>
      </div>
    );
  }

  // ── 9. E-SIGN VAULT (Fresh Mint / Cryptographic Security) ──
  if (slug === 'esign-vault') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0FDF4] via-[#DCFCE7] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] flex flex-col gap-2">
          <div className="w-full bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(34,197,94,0.15)] border border-emerald-100">
            <div className="flex items-center justify-between text-[10px] text-zinc-500 mb-1.5">
              <span className="font-bold text-emerald-700 flex items-center gap-1">
                <Lock className="w-3 h-3" /> SHA-256 VAULT
              </span>
              <span className="font-mono text-[9px]">ID: 8F2A...90B</span>
            </div>
            
            <div className="p-2 bg-emerald-50/70 rounded-xl border border-emerald-200/80 mb-2">
              <div className="text-[11px] font-bold text-zinc-900">Commercial Production Agreement</div>
              <div className="text-[9px] text-emerald-700">100% IT Act 2000 Compliant</div>
            </div>

            <div className="flex items-center justify-between pt-1">
              <span className="font-serif italic text-xs text-zinc-800">Shruti Sharma ✓</span>
              <span className="text-[9px] font-mono font-bold text-emerald-600 bg-white px-2 py-0.5 rounded border border-emerald-200">
                TIMESTAMPED
              </span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 10. CREW DISPATCH (Interactive Call Sheet & Schedule Matrix) ──
  if (slug === 'crew-dispatch') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F5F3FF] via-[#EDE9FE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(139,92,246,0.14)] border border-purple-100">
          <div className="flex items-center justify-between text-[10px] font-bold text-purple-700 mb-2">
            <span>Call Sheet Dispatch</span>
            <span className="text-[9px] font-mono text-zinc-500">0 Conflicts</span>
          </div>

          <div className="flex items-center justify-between gap-1 mb-2.5">
            {['S', 'M', 'T', 'W', 'T', 'F'].map((day, idx) => (
              <span
                key={idx}
                className={`w-6 h-6 rounded-lg text-[9px] font-bold flex items-center justify-center ${
                  idx === 1 || idx === 2
                    ? 'bg-purple-600 text-white shadow-2xs'
                    : 'bg-zinc-100 text-zinc-500'
                }`}
              >
                {day}
              </span>
            ))}
          </div>

          <div className="p-2 bg-purple-50/60 rounded-xl border border-purple-100 text-[10px] flex items-center justify-between">
            <span className="text-zinc-700 font-medium">📍 Studio Floor B</span>
            <span className="font-mono font-bold text-purple-800">06:30 AM Call</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 11. MASTER CALENDAR (Shoot Scheduler with Live Color Bars) ──
  if (slug === 'master-calendar') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0F9FF] via-[#E0F2FE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(2,132,199,0.12)] border border-sky-100">
          <div className="flex items-center justify-between text-[10px] font-bold text-sky-800 mb-2">
            <span>November 2026</span>
            <span className="text-[9px] font-mono text-zinc-500">4 Shoots Active</span>
          </div>
          
          <div className="space-y-1.5 text-[10px]">
            <div className="p-1.5 rounded-lg bg-sky-500 text-white font-semibold flex items-center justify-between">
              <span>● Vogue Fashion Shoot</span>
              <span className="text-[9px] font-mono">09:00 AM</span>
            </div>
            <div className="p-1.5 rounded-lg bg-emerald-500 text-white font-semibold flex items-center justify-between">
              <span>● Luxury Villa Drone Tour</span>
              <span className="text-[9px] font-mono">02:30 PM</span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 12. TASK BOARD (Milestone Task Tracker) ──
  if (slug === 'task-board') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F8FAFC] via-[#F1F5F9] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(0,0,0,0.06)] border border-zinc-200">
          <div className="flex items-center justify-between text-[10px] text-zinc-500 mb-2">
            <span className="font-bold text-zinc-900">Post-Production</span>
            <span className="px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 font-bold text-[8px]">RUSH</span>
          </div>

          <div className="space-y-1 text-[10px]">
            <div className="flex items-center gap-1.5 text-zinc-800 font-medium">
              <span className="w-4 h-4 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center text-[9px] font-bold">✓</span>
              <span>4K RAW Ingest &amp; Backup</span>
            </div>
            <div className="flex items-center gap-1.5 text-zinc-800 font-medium">
              <span className="w-4 h-4 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center text-[9px] font-bold">✓</span>
              <span>Davinci Resolve Color Grade</span>
            </div>
            <div className="flex items-center gap-1.5 text-zinc-400">
              <span className="w-4 h-4 rounded border border-zinc-300" />
              <span>Final Client Delivery</span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 13. GST INVOICING (18% GST Calculation Breakdown Card) ──
  if (slug === 'gst-invoicing') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FEFCE8] via-[#FEF08A]/30 to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(202,138,4,0.15)] border border-amber-200/80">
          <div className="flex items-center justify-between text-[10px] text-zinc-500 mb-1.5">
            <span className="font-bold text-amber-800">TAX INVOICE</span>
            <span className="text-[9px] font-mono font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
              PAID VIA UPI
            </span>
          </div>

          <div className="space-y-1 text-[10px] pb-2 mb-2 border-b border-zinc-100">
            <div className="flex justify-between text-zinc-600">
              <span>Subtotal:</span>
              <span className="font-mono">₹1,50,000</span>
            </div>
            <div className="flex justify-between text-amber-700 font-semibold">
              <span>CGST (9%) + SGST (9%):</span>
              <span className="font-mono">+ ₹27,000</span>
            </div>
          </div>

          <div className="flex justify-between items-center text-xs font-bold text-zinc-950">
            <span>Total Payable:</span>
            <span className="font-mono text-sm font-extrabold text-zinc-950">₹1,77,000</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 14. ASSET GEAR (Equipment QR & Check-In/Out) ──
  if (slug === 'asset-gear') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0F9FF] via-[#E0F2FE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(14,165,233,0.12)] border border-sky-100">
          <div className="flex items-center gap-2 mb-2">
            <div className="w-7 h-7 rounded-lg bg-sky-500 text-white flex items-center justify-center">
              <Camera className="w-4 h-4" />
            </div>
            <div>
              <div className="text-[11px] font-bold text-zinc-900">Sony FX6 Cinema Kit</div>
              <div className="text-[9px] font-mono text-zinc-400">Barcode: #SN-98214</div>
            </div>
          </div>

          <div className="flex items-center justify-between p-1.5 bg-sky-50 rounded-lg text-[9px] font-bold text-sky-800">
            <span>STATUS: CHECKED OUT</span>
            <span>Return: 8:00 PM</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 15. MEDIA HUB (Folder Storage & Aspect Ratio Crop) ──
  if (slug === 'media-hub') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#EFF6FF] via-[#DBEAFE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(37,99,235,0.12)] border border-blue-100">
          <div className="flex items-center justify-between text-[10px] font-bold text-blue-700 mb-2">
            <span>RAW Media Vault</span>
            <span className="text-[9px] font-mono text-zinc-400">4.2 TB Stored</span>
          </div>

          <div className="flex items-center justify-center gap-2 py-1.5 bg-zinc-50 rounded-xl border border-zinc-100 mb-2">
            <span className="px-2 py-1 bg-white rounded-md text-[9px] font-bold text-zinc-900 shadow-2xs border border-zinc-200">
              1:1
            </span>
            <span className="px-2 py-1 bg-blue-600 rounded-md text-[9px] font-bold text-white shadow-2xs">
              4:3 Preset
            </span>
            <span className="px-2 py-1 bg-white rounded-md text-[9px] font-bold text-zinc-900 shadow-2xs border border-zinc-200">
              16:9
            </span>
          </div>

          <div className="text-[9px] text-center text-zinc-500 font-medium">
            Sub-50ms Global Edge CDN Delivery
          </div>
        </div>
      </div>
    );
  }

  // ── 16. RBAC SYSTEM (Enterprise Role Access) ──
  if (slug === 'rbac-system') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FAF5FF] via-[#F3E8FF] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(168,85,247,0.12)] border border-purple-100">
          <div className="text-[10px] font-bold text-purple-800 mb-2">Studio Access Roles</div>
          <div className="space-y-1.5 text-[10px]">
            <div className="flex items-center justify-between p-1.5 bg-purple-50 rounded-lg">
              <span className="font-bold text-zinc-900">Admin (Shruti)</span>
              <span className="text-[9px] font-bold text-purple-700">Full Access</span>
            </div>
            <div className="flex items-center justify-between p-1.5 bg-zinc-50 rounded-lg">
              <span className="text-zinc-700">Lead DOP / Camera</span>
              <span className="text-[9px] text-zinc-500">Shoots Only</span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 17. EMAIL SMTP (Branded Delivery & High Deliverability) ──
  if (slug === 'email-smtp') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F5F3FF] via-[#EDE9FE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(139,92,246,0.12)] border border-purple-100">
          <div className="flex items-center gap-2 mb-2">
            <div className="w-7 h-7 rounded-lg bg-purple-600 text-white flex items-center justify-center">
              <Mail className="w-4 h-4" />
            </div>
            <div>
              <div className="text-[11px] font-bold text-zinc-900">Custom SMTP Sender</div>
              <div className="text-[9px] font-mono text-zinc-400">From: studio@brand.com</div>
            </div>
          </div>
          <div className="flex items-center justify-between p-1.5 bg-purple-50 rounded-lg text-[9px] font-bold text-purple-800">
            <span>DKIM / SPF: VERIFIED</span>
            <span>84.6% Open</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 18. PWA PUSH (Real-Time Studio Mobile Alerts) ──
  if (slug === 'pwa-push') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0FDF4] via-[#DCFCE7] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(34,197,94,0.12)] border border-emerald-100">
          <div className="flex items-center justify-between text-[10px] text-zinc-500 mb-1.5">
            <span className="font-bold text-emerald-800 flex items-center gap-1">
              <Bell className="w-3 h-3 text-emerald-600" /> PWA PUSH NOTIFICATION
            </span>
            <span className="text-[9px] font-mono">2m ago</span>
          </div>
          <div className="text-[11px] font-bold text-zinc-900 mb-0.5">
            Shoot Tomorrow: Taj Lands End
          </div>
          <div className="text-[9px] text-zinc-500">
            Call Time: 06:30 AM • 4 Crew Confirmed
          </div>
        </div>
      </div>
    );
  }

  // ── 19. DOCS PORTAL (Interactive Developer Hub & API) ──
  if (slug === 'docs-portal') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F8FAFC] via-[#F1F5F9] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-zinc-950 text-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(0,0,0,0.15)] border border-zinc-800">
          <div className="flex items-center justify-between text-[9px] font-mono text-zinc-400 mb-2">
            <span>REST / MCP API</span>
            <span className="text-emerald-400">200 OK</span>
          </div>
          <div className="p-2 bg-zinc-900 rounded-xl font-mono text-[10px] text-emerald-300 mb-1.5">
            GET /v1/shoots/live
          </div>
          <div className="text-[9px] text-zinc-400 text-center">
            Interactive Test Console &amp; Docs
          </div>
        </div>
      </div>
    );
  }

  // ── 20. SUPER ADMIN (Multi-Workspace Tenant Hub) ──
  if (slug === 'super-admin') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FFF1F2] via-[#FFE4E6] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white/95 rounded-2xl p-3 shadow-[0_10px_25px_rgba(244,63,94,0.12)] border border-rose-100">
          <div className="text-[10px] font-bold text-rose-800 mb-2">Multi-Workspace Governance</div>
          <div className="space-y-1.5 text-[10px]">
            <div className="flex items-center justify-between p-1.5 bg-rose-50 rounded-lg font-medium text-zinc-900">
              <span>Mumbai Flagship</span>
              <span className="font-mono font-bold text-rose-700">₹48.2L</span>
            </div>
            <div className="flex items-center justify-between p-1.5 bg-zinc-50 rounded-lg text-zinc-600">
              <span>Bangalore Media</span>
              <span className="font-mono font-bold">₹22.4L</span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 21. ROADMAP MODULES (Dark / Amber Glow Mockups) ──
  if (slug === 'whatsapp-cloud') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#18181B] via-[#0F0F12] to-black relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[240px] bg-zinc-900/90 rounded-2xl p-3 border border-zinc-800 text-white shadow-xl">
          <div className="flex items-center gap-2 mb-2 text-emerald-400 text-[10px] font-bold">
            <MessageCircle className="w-3.5 h-3.5" /> WhatsApp Cloud 2.0
          </div>
          <div className="p-1.5 bg-zinc-800 rounded-lg text-[10px] text-zinc-200 mb-1">
            &ldquo;Your shoot is confirmed for 9:00 AM! 📸&rdquo;
          </div>
          <div className="text-[9px] text-emerald-400 font-semibold">2-Way Live Sync</div>
        </div>
      </div>
    );
  }

  if (slug === 'photo-proofing') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#18181B] via-[#0F0F12] to-black relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[240px] bg-zinc-900/90 rounded-2xl p-3 border border-zinc-800 text-white shadow-xl">
          <div className="flex items-center justify-between text-[10px] font-bold text-amber-400 mb-2">
            <span>Watermark Proofing</span>
            <span>⭐ Favorite</span>
          </div>
          <div className="h-10 bg-zinc-800 rounded-lg flex items-center justify-center text-[10px] font-mono text-zinc-400 border border-zinc-700">
            [ Watermarked RAW Proofs ]
          </div>
        </div>
      </div>
    );
  }

  // ── DEFAULT FALLBACK (Rich Floating Capsule) ──
  return (
    <div className="w-full h-full bg-gradient-to-b from-zinc-50 via-zinc-100/70 to-white relative flex items-center justify-center p-4">
      <div className="relative w-full max-w-[240px] bg-white rounded-2xl p-3.5 shadow-[0_8px_20px_rgba(0,0,0,0.05)] border border-zinc-200/90 text-center">
        <div className="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-zinc-950 text-white mb-2 shadow-2xs">
          <Sparkles className="w-4 h-4 text-zinc-200" />
        </div>
        <div className="text-xs font-bold text-zinc-950 mb-0.5 truncate">{title}</div>
        <div className="text-[10px] text-zinc-500 font-mono">Live in Workspace</div>
      </div>
    </div>
  );
}
