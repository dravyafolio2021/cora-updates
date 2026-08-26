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
  TabletSmartphone,
  MousePointer
} from 'lucide-react';

interface ModuleCardVisualProps {
  slug: string;
  category?: string;
  title: string;
}

export function ModuleCardVisual({ slug, category, title }: ModuleCardVisualProps) {
  
  // ── 1. AI CO-FOUNDER (Coral/Peach Reference Style: 3D Floating Action Pills) ──
  if (slug === 'ai-cofounder') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FFF5F2] via-[#FFEBE5] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] flex flex-col items-center gap-2.5">
          {/* Top Pill with Avatar & Checkmark */}
          <div className="w-full bg-white rounded-2xl p-2.5 shadow-[0_8px_20px_rgba(0,0,0,0.06)] border border-white flex items-center justify-between gap-2 transform -translate-y-1">
            <div className="flex items-center gap-2">
              <div className="w-6 h-6 rounded-lg bg-orange-500 text-white flex items-center justify-center font-bold text-[10px] shadow-xs">
                <User className="w-3.5 h-3.5" />
              </div>
              <span className="text-[11px] font-bold text-zinc-900 truncate">Assign tasks to members</span>
            </div>
            <span className="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-bold">
              ✓
            </span>
          </div>

          {/* Main Vibrant Coral Floating Action Pill */}
          <div className="w-[94%] bg-gradient-to-r from-[#FF5E48] to-[#FF7A59] text-white rounded-2xl py-2.5 px-4 shadow-[0_12px_24px_rgba(255,94,72,0.35)] flex items-center justify-center gap-2 transform scale-105 group-hover:scale-110 transition-transform duration-300">
            <Sparkles className="w-4 h-4 text-amber-200 animate-pulse" />
            <span className="text-xs font-bold tracking-tight">Smart Task Management</span>
          </div>

          {/* Bottom Angled Floating Tag Pills */}
          <div className="w-full flex items-center justify-between gap-2 px-1">
            <span className="px-2.5 py-1 rounded-xl bg-white text-[10px] font-semibold text-zinc-700 shadow-sm border border-zinc-100 flex items-center gap-1.5">
              <span className="w-4 h-4 rounded-md bg-blue-500 text-white flex items-center justify-center text-[8px] font-bold">≡</span>
              Organize all task
            </span>
            <span className="px-2.5 py-1 rounded-xl bg-white text-[10px] font-semibold text-zinc-700 shadow-sm border border-zinc-100 flex items-center gap-1.5">
              <span className="w-4 h-4 rounded-md bg-emerald-500 text-white flex items-center justify-center text-[8px] font-bold">::</span>
              Prioritize each task
            </span>
          </div>
        </div>
      </div>
    );
  }

  // ── 2. LEAD CRM (Cool Ice Blue Reference Style: Add task cursor + Deal card with avatars) ──
  if (slug === 'lead-crm') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F2F7FF] via-[#EBF3FE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] flex flex-col gap-2">
          {/* Top Pill with Blue Cursor */}
          <div className="flex items-center justify-between px-1 relative">
            <div className="px-3 py-1 rounded-xl bg-white text-blue-600 text-[11px] font-bold shadow-sm border border-blue-50 flex items-center gap-1.5">
              <span className="text-blue-500 font-extrabold">+</span> Add new task
            </div>
            <div className="flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-600 text-white text-[9px] font-bold shadow-xs">
              <MousePointer className="w-2.5 h-2.5 fill-white" />
              <span>Briono</span>
            </div>
          </div>

          {/* Center Card: Error tag, Date, Title, Avatars */}
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(59,130,246,0.12)] border border-blue-100/60">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-400 mb-1">
              <span className="font-bold text-blue-600 flex items-center gap-1">
                ● ERROR
              </span>
              <span>📅 7 October 2025</span>
            </div>
            <p className="text-xs font-bold text-zinc-900 mb-2.5">
              Design system requirements
            </p>
            <div className="flex items-center justify-between pt-1 border-t border-zinc-100">
              {/* Overlapping Avatar Stack */}
              <div className="flex -space-x-1.5 items-center">
                <div className="w-5 h-5 rounded-full bg-rose-400 border border-white text-white text-[8px] flex items-center justify-center font-bold">K</div>
                <div className="w-5 h-5 rounded-full bg-amber-400 border border-white text-white text-[8px] flex items-center justify-center font-bold">S</div>
                <div className="w-5 h-5 rounded-full bg-emerald-400 border border-white text-white text-[8px] flex items-center justify-center font-bold">A</div>
                <div className="w-5 h-5 rounded-full bg-indigo-400 border border-white text-white text-[8px] flex items-center justify-center font-bold">R</div>
              </div>
              <span className="text-[10px] font-mono text-zinc-500 flex items-center gap-1">
                <Paperclip className="w-3 h-3 text-zinc-400" /> 6
              </span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 3. MASTER CALENDAR & AUTOMATION (Fresh Mint Reference Style: Repeat Weekly Modal) ──
  if (slug === 'master-calendar' || slug === 'crew-dispatch') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F2FAF5] via-[#E8F6EE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(16,185,129,0.12)] border border-emerald-100/80">
          {/* Header */}
          <div className="flex items-center justify-between text-[11px] font-bold text-zinc-900 mb-2">
            <span>Repeat weekly</span>
            <span className="text-[10px] text-zinc-500 font-normal bg-zinc-100 px-2 py-0.5 rounded-md">Every 1 Week ⌄</span>
          </div>

          {/* Day Selector Matrix */}
          <div className="flex items-center justify-between gap-1 mb-2.5">
            {['S', 'M', 'T', 'W', 'T', 'F'].map((day, idx) => (
              <span
                key={idx}
                className={`w-6 h-6 rounded-full text-[9px] font-bold flex items-center justify-center ${
                  idx >= 1 && idx <= 4
                    ? 'bg-emerald-500 text-white shadow-2xs'
                    : 'text-zinc-400'
                }`}
              >
                {day}
              </span>
            ))}
          </div>

          {/* Start & Time Rows */}
          <div className="space-y-1 text-[10px] mb-2.5 text-zinc-600">
            <div className="flex justify-between items-center">
              <span>Start</span>
              <span className="font-semibold text-zinc-900">Sun, May 12, 2025 ⌄</span>
            </div>
            <div className="flex justify-between items-center">
              <span>Create at</span>
              <span className="font-semibold text-zinc-900">4:25 Am GMT +7 ⌄</span>
            </div>
          </div>

          {/* Buttons */}
          <div className="grid grid-cols-2 gap-2 pt-1 border-t border-zinc-100">
            <button type="button" className="py-1 rounded-xl bg-zinc-100 text-[10px] font-semibold text-zinc-700 text-center">
              Cancel
            </button>
            <button type="button" className="py-1 rounded-xl bg-zinc-950 text-[10px] font-bold text-white text-center shadow-xs">
              Save
            </button>
          </div>
        </div>
      </div>
    );
  }

  // ── 4. CONTENT AI (Warm Amber Script Suite) ──
  if (slug === 'content-ai') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FFFDF2] via-[#FEF7D8] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(234,179,8,0.12)] border border-amber-100">
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

          <div className="self-center bg-zinc-950 text-white px-4 py-1.5 rounded-full text-[10px] font-bold shadow-sm flex items-center gap-1.5">
            <span>✨ 1-Click Multi-Format Export</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 5. RAG MCP (Deep Violet & Neural Circuit) ──
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

          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(124,58,237,0.12)] border border-purple-100 text-center">
            <div className="flex items-center justify-center gap-1.5 text-[11px] font-bold text-zinc-900 mb-1">
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              Living Studio Context Sync
            </div>
            <p className="text-[10px] text-zinc-500">
              Instant recall across all past shoot briefs
            </p>
          </div>
        </div>
      </div>
    );
  }

  // ── 6. VOICE TO SCOPE (Cyan Waveform Studio) ──
  if (slug === 'voice-to-scope') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0FDFA] via-[#CCFBF1] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col items-center gap-2.5">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_8px_20px_rgba(13,148,136,0.12)] border border-teal-100 flex items-center justify-between gap-2">
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
            <span>Structured Brief &amp; Call Sheet</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 7. CANVAS BUILDER (Visual Designer) ──
  if (slug === 'canvas-builder') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F8FAFC] via-[#EEF2F6] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(0,0,0,0.06)] border border-zinc-200">
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

  // ── 8. FORM BUILDER (Pink / Lead Capture Form) ──
  if (slug === 'form-builder') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FDF4FF] via-[#FCE7F3] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(217,70,239,0.12)] border border-pink-100">
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
          <div className="mt-2 w-full py-1.5 bg-zinc-950 text-white rounded-lg text-[10px] font-bold text-center">
            Submit &amp; Schedule
          </div>
        </div>
      </div>
    );
  }

  // ── 9. REVIEW PORTAL (Golden Star Reputation) ──
  if (slug === 'review-portal') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FFFBEB] via-[#FEF3C7] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col items-center gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(245,158,11,0.12)] border border-amber-100 text-center">
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

          <span className="px-3 py-1 bg-zinc-950 text-white rounded-full text-[9px] font-bold flex items-center gap-1 shadow-xs">
            <span>⚡ Automated WhatsApp Review Trigger</span>
          </span>
        </div>
      </div>
    );
  }

  // ── 10. E-SIGN VAULT (Mint / SHA-256 Verified Seal) ──
  if (slug === 'esign-vault') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0FDF4] via-[#DCFCE7] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(34,197,94,0.12)] border border-emerald-100">
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
              <span className="font-serif italic text-xs text-zinc-800">Kavya Patel ✓</span>
              <span className="text-[9px] font-mono font-bold text-emerald-600 bg-white px-2 py-0.5 rounded border border-emerald-200">
                TIMESTAMPED
              </span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 11. TASK BOARD (Milestone Task Tracker) ──
  if (slug === 'task-board') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F8FAFC] via-[#F1F5F9] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(0,0,0,0.06)] border border-zinc-200">
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

  // ── 12. GST INVOICING (18% GST Calculation Breakdown Card) ──
  if (slug === 'gst-invoicing') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FEFCE8] via-[#FEF08A]/30 to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[260px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(202,138,4,0.12)] border border-amber-200/80">
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

  // ── 13. ASSET GEAR (Equipment QR & Check-In/Out) ──
  if (slug === 'asset-gear') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0F9FF] via-[#E0F2FE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(14,165,233,0.12)] border border-sky-100">
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

  // ── 14. MEDIA HUB (Folder Storage & Aspect Ratio Crop) ──
  if (slug === 'media-hub') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#EFF6FF] via-[#DBEAFE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(37,99,235,0.12)] border border-blue-100">
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

  // ── 15. RBAC SYSTEM (Enterprise Role Access) ──
  if (slug === 'rbac-system') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FAF5FF] via-[#F3E8FF] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(168,85,247,0.12)] border border-purple-100">
          <div className="text-[10px] font-bold text-purple-800 mb-2">Studio Access Roles</div>
          <div className="space-y-1.5 text-[10px]">
            <div className="flex items-center justify-between p-1.5 bg-purple-50 rounded-lg">
              <span className="font-bold text-zinc-900">Studio Admin</span>
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

  // ── 16. EMAIL SMTP (Lavender Branded Delivery) ──
  if (slug === 'email-smtp') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F5F3FF] via-[#EDE9FE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(139,92,246,0.12)] border border-purple-100">
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

  // ── 17. PWA PUSH (Mint Push Alert Banner) ──
  if (slug === 'pwa-push') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0FDF4] via-[#DCFCE7] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(34,197,94,0.12)] border border-emerald-100">
          <div className="flex items-center justify-between text-[10px] text-zinc-500 mb-1.5">
            <span className="font-bold text-emerald-800 flex items-center gap-1">
              <Bell className="w-3 h-3 text-emerald-600" /> PWA NOTIFICATION
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

  // ── 18. DOCS PORTAL (Developer REST API) ──
  if (slug === 'docs-portal') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F8FAFC] via-[#F1F5F9] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(0,0,0,0.06)] border border-zinc-200">
          <div className="flex items-center justify-between text-[9px] font-mono text-zinc-500 mb-2">
            <span>REST / MCP API</span>
            <span className="text-emerald-600 font-bold">200 OK</span>
          </div>
          <div className="p-2 bg-zinc-50 rounded-xl font-mono text-[10px] text-zinc-900 mb-1.5 border border-zinc-200/80">
            GET /v1/shoots/live
          </div>
          <div className="text-[9px] text-zinc-400 text-center">
            Interactive Test Console &amp; Docs
          </div>
        </div>
      </div>
    );
  }

  // ── 19. SUPER ADMIN (Rose Quartz Governance) ──
  if (slug === 'super-admin') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FFF1F2] via-[#FFE4E6] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(244,63,94,0.12)] border border-rose-100">
          <div className="text-[10px] font-bold text-rose-800 mb-2">Multi-Workspace Hub</div>
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

  // ── 20. ROADMAP: WHATSAPP CLOUD (Light Fresh Mint / WhatsApp Green) ──
  if (slug === 'whatsapp-cloud') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0FDF4] via-[#DCFCE7] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(34,197,94,0.12)] border border-emerald-100">
          <div className="flex items-center gap-1.5 mb-2 text-emerald-700 text-[10px] font-bold">
            <MessageCircle className="w-3.5 h-3.5 text-emerald-600" /> WhatsApp Cloud 2.0
          </div>
          <div className="p-2 bg-emerald-50/70 rounded-xl text-[10px] text-zinc-800 mb-1.5 border border-emerald-100">
            &ldquo;Your shoot is confirmed for 9:00 AM! 📸&rdquo;
          </div>
          <div className="flex items-center justify-between text-[9px]">
            <span className="text-zinc-400">Status: Delivered</span>
            <span className="text-emerald-700 font-bold">2-Way Sync</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 21. ROADMAP: PHOTO PROOFING (Light Sky Blue Gallery) ──
  if (slug === 'photo-proofing') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0F9FF] via-[#E0F2FE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(14,165,233,0.12)] border border-sky-100">
          <div className="flex items-center justify-between text-[10px] font-bold text-sky-800 mb-2">
            <span>Watermark Proofing 2.0</span>
            <span className="text-amber-500 flex items-center gap-0.5">⭐ Favorite</span>
          </div>
          <div className="grid grid-cols-3 gap-1.5 mb-2">
            <div className="h-10 bg-sky-50 rounded-lg flex items-center justify-center text-[8px] font-mono text-sky-700 border border-sky-200">
              Proof #1 ✓
            </div>
            <div className="h-10 bg-sky-50 rounded-lg flex items-center justify-center text-[8px] font-mono text-sky-700 border border-sky-200">
              Proof #2 ✓
            </div>
            <div className="h-10 bg-zinc-50 rounded-lg flex items-center justify-center text-[8px] font-mono text-zinc-400 border border-zinc-200">
              Proof #3
            </div>
          </div>
          <div className="text-[9px] text-center text-sky-700 font-bold">
            Client Album Approval Ready
          </div>
        </div>
      </div>
    );
  }

  // ── 22. ROADMAP: INTEGRATED PAYMENTS (Light Amber / UPI Settlement) ──
  if (slug === 'integrated-payments') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FEFCE8] via-[#FEF08A]/30 to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(202,138,4,0.12)] border border-amber-100">
          <div className="flex items-center justify-between text-[10px] text-amber-800 font-bold mb-2">
            <span className="flex items-center gap-1"><CreditCard className="w-3.5 h-3.5" /> Auto-Reconcile</span>
            <span className="text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded text-[8px]">ACTIVE</span>
          </div>
          <div className="p-2 bg-amber-50/70 rounded-xl text-[10px] text-zinc-900 font-bold mb-1.5 flex justify-between">
            <span>Razorpay / UPI Settlement</span>
            <span className="font-mono text-emerald-700">₹2,40,000</span>
          </div>
          <div className="text-[9px] text-zinc-500 text-center">
            Instant Bank Ledger Reconciliation
          </div>
        </div>
      </div>
    );
  }

  // ── 23. ROADMAP: VIDEO STORYBOARD (Light Coral / Deck Generator) ──
  if (slug === 'video-storyboard') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#FFF5F2] via-[#FFEBE5] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(255,94,72,0.12)] border border-rose-100">
          <div className="flex items-center justify-between text-[10px] font-bold text-rose-700 mb-2">
            <span className="flex items-center gap-1"><Video className="w-3.5 h-3.5" /> AI Storyboard Deck</span>
            <span className="text-zinc-400 text-[9px]">4 Scenes</span>
          </div>
          <div className="grid grid-cols-2 gap-1.5 mb-1.5">
            <div className="p-1.5 bg-rose-50 rounded-lg text-[9px] text-zinc-800 font-medium border border-rose-100">
              Scene 1: Hook 🎬
            </div>
            <div className="p-1.5 bg-rose-50 rounded-lg text-[9px] text-zinc-800 font-medium border border-rose-100">
              Scene 2: Reveal ✨
            </div>
          </div>
          <div className="text-[9px] text-center text-rose-700 font-bold">
            YouTube &amp; Reel Deck Generator
          </div>
        </div>
      </div>
    );
  }

  // ── 24. ROADMAP: MULTI-BRANCH (Light Purple Franchise Hub) ──
  if (slug === 'multi-branch') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F5F3FF] via-[#EDE9FE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(139,92,246,0.12)] border border-purple-100">
          <div className="flex items-center justify-between text-[10px] font-bold text-purple-700 mb-2">
            <span className="flex items-center gap-1"><GitBranch className="w-3.5 h-3.5" /> Multi-Branch Network</span>
            <span className="text-[9px] font-mono text-zinc-400">3 Hubs</span>
          </div>
          <div className="p-2 bg-purple-50 rounded-xl text-[10px] text-zinc-900 font-semibold mb-1 flex justify-between">
            <span>Shared Regional Gear</span>
            <span className="text-purple-700 font-bold">12 Kits</span>
          </div>
          <div className="text-[9px] text-zinc-500 text-center">
            Consolidated Studio Financials
          </div>
        </div>
      </div>
    );
  }

  // ── 25. ROADMAP: VOICE AI AGENT (Light Cyan Voice Concierge) ──
  if (slug === 'voice-ai-agent') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0FDFA] via-[#CCFBF1] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(13,148,136,0.12)] border border-teal-100">
          <div className="flex items-center justify-between text-[10px] font-bold text-teal-700 mb-2">
            <span className="flex items-center gap-1"><PhoneCall className="w-3.5 h-3.5" /> Inbound Voice AI</span>
            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
          </div>
          <div className="p-2 bg-teal-50 rounded-xl text-[10px] text-zinc-900 font-medium mb-1">
            &ldquo;Hi! I can help you book your shoot date.&rdquo;
          </div>
          <div className="text-[9px] text-teal-700 font-bold text-center">
            ElevenLabs &amp; Twilio Live Agent
          </div>
        </div>
      </div>
    );
  }

  // ── 26. ROADMAP: TALLY ZOHO EXPORT (Light Mint CA Ledger) ──
  if (slug === 'tally-zoho-export') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#F0FDF4] via-[#DCFCE7] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(34,197,94,0.12)] border border-emerald-100">
          <div className="flex items-center justify-between text-[10px] font-bold text-emerald-800 mb-2">
            <span className="flex items-center gap-1"><FileSpreadsheet className="w-3.5 h-3.5" /> GSTR-1 Ledger Sync</span>
            <span className="text-[9px] font-mono text-emerald-600 font-bold">XML / JSON</span>
          </div>
          <div className="p-2 bg-emerald-50 rounded-xl text-[10px] text-zinc-900 font-semibold mb-1 flex justify-between">
            <span>Tally Prime &amp; Zoho</span>
            <span className="text-emerald-700 font-bold">1-Click CA Sync</span>
          </div>
          <div className="text-[9px] text-zinc-500 text-center">
            Audited Indian Tax Formats
          </div>
        </div>
      </div>
    );
  }

  // ── 27. ROADMAP: CLIENT MOBILE APP (Light Blue Mobile Companion) ──
  if (slug === 'client-mobile-app') {
    return (
      <div className="w-full h-full bg-gradient-to-b from-[#EFF6FF] via-[#DBEAFE] to-white relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-2xl p-3 shadow-[0_10px_25px_rgba(37,99,235,0.12)] border border-blue-100">
          <div className="flex items-center justify-between text-[10px] font-bold text-blue-700 mb-2">
            <span className="flex items-center gap-1"><TabletSmartphone className="w-3.5 h-3.5" /> Client Portal App</span>
            <span className="text-[9px] text-zinc-400">iOS &amp; Android</span>
          </div>
          <div className="p-2 bg-blue-50 rounded-xl text-[10px] text-zinc-900 font-semibold mb-1 flex justify-between">
            <span>Sign &amp; Track Shoot</span>
            <span className="text-blue-700 font-bold">White-Labeled</span>
          </div>
          <div className="text-[9px] text-zinc-500 text-center">
            Native Push Notifications
          </div>
        </div>
      </div>
    );
  }

  // ── DEFAULT FALLBACK (Clean Light Skeuomorphic Capsule) ──
  return (
    <div className="w-full h-full bg-gradient-to-b from-zinc-50 via-zinc-100/60 to-white relative flex items-center justify-center p-4">
      <div className="relative w-full max-w-[240px] bg-white rounded-2xl p-3.5 shadow-[0_8px_20px_rgba(0,0,0,0.05)] border border-zinc-200/90 text-center">
        <div className="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-zinc-950 text-white mb-2 shadow-xs">
          <Sparkles className="w-4 h-4 text-zinc-200" />
        </div>
        <div className="text-xs font-bold text-zinc-950 mb-0.5 truncate">{title}</div>
        <div className="text-[10px] text-zinc-500 font-mono">Live in Workspace</div>
      </div>
    </div>
  );
}
