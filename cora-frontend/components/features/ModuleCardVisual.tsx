'use client';

import React from 'react';
import { 
  Sparkles, 
  Bot, 
  Check, 
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

export function ModuleCardVisual({ slug, title }: ModuleCardVisualProps) {
  
  // ── 1. AI CO-FOUNDER (Strict Monochrome Tactile Stack) ──
  if (slug === 'ai-cofounder') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col items-center gap-2">
          {/* Top Pill with Avatar & Check */}
          <div className="w-full bg-white rounded-xl p-2.5 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80 flex items-center justify-between gap-2">
            <div className="flex items-center gap-2 min-w-0">
              <div className="w-5 h-5 rounded-md bg-zinc-950 text-white flex items-center justify-center text-[10px]">
                <Bot className="w-3 h-3" />
              </div>
              <span className="text-[11px] font-semibold text-zinc-900 truncate">Draft ₹2.5L Proposal</span>
            </div>
            <span className="w-4 h-4 rounded-full bg-zinc-100 text-zinc-700 flex items-center justify-center text-[9px] font-bold">
              ✓
            </span>
          </div>

          {/* Main Solid Black Action Pill */}
          <div className="w-[92%] bg-zinc-950 text-white rounded-xl py-2 px-3.5 shadow-sm flex items-center justify-center gap-1.5">
            <Sparkles className="w-3.5 h-3.5 text-zinc-300" />
            <span className="text-xs font-semibold">Autonomous Operations</span>
          </div>

          {/* Bottom Neutral Sub-tags */}
          <div className="w-full flex items-center justify-between gap-1.5 px-0.5">
            <span className="px-2 py-0.5 rounded-md bg-white text-[10px] font-medium text-zinc-600 shadow-2xs border border-zinc-200/70 font-mono">
              6-Tier Fallback
            </span>
            <span className="px-2 py-0.5 rounded-md bg-white text-[10px] font-medium text-zinc-600 shadow-2xs border border-zinc-200/70 font-mono">
              24/7 Execution
            </span>
          </div>
        </div>
      </div>
    );
  }

  // ── 2. CONTENT AI (Clean Monochromatic Script Card) ──
  if (slug === 'content-ai') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
            <div className="flex items-center justify-between text-[10px] text-zinc-400 font-mono mb-1.5">
              <span className="font-bold text-zinc-900 flex items-center gap-1">
                <FileText className="w-3 h-3 text-zinc-700" /> VIRAL REEL SCRIPT
              </span>
              <span>0:30s</span>
            </div>
            <p className="text-[11px] font-semibold text-zinc-900 truncate mb-2">
              &ldquo;Top 3 Wedding Lighting Setups in 2026&rdquo;
            </p>
            <div className="flex items-center gap-1.5">
              <span className="px-2 py-0.5 rounded-md bg-zinc-100 text-[9px] font-mono font-semibold text-zinc-800 border border-zinc-200/60">
                Hook: 0-3s
              </span>
              <span className="px-2 py-0.5 rounded-md bg-zinc-50 text-[9px] font-mono text-zinc-500 border border-zinc-200/40">
                10x Reach
              </span>
            </div>
          </div>

          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-semibold shadow-xs flex items-center gap-1.5">
            <span>Multi-Format Export</span>
            <ArrowRight className="w-3 h-3 text-zinc-400" />
          </div>
        </div>
      </div>
    );
  }

  // ── 3. RAG MCP (Neutral Circuit Chips) ──
  if (slug === 'rag-mcp') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col items-center gap-2">
          <div className="flex items-center gap-2">
            <span className="px-2.5 py-1 bg-white rounded-lg text-[10px] font-mono font-semibold text-zinc-900 shadow-2xs border border-zinc-200 flex items-center gap-1">
              <Cpu className="w-3 h-3 text-zinc-700" /> MCP Standard
            </span>
            <span className="text-zinc-400 text-xs">⇄</span>
            <span className="px-2.5 py-1 bg-white rounded-lg text-[10px] font-mono font-semibold text-zinc-900 shadow-2xs border border-zinc-200 flex items-center gap-1">
              <Layers className="w-3 h-3 text-zinc-700" /> Vector Store
            </span>
          </div>

          <div className="w-full bg-white rounded-xl p-2.5 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80 text-center">
            <div className="text-[11px] font-semibold text-zinc-900 mb-0.5">
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

  // ── 4. VOICE TO SCOPE (Audio Waveform in Neutral Slate) ──
  if (slug === 'voice-to-scope') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col items-center gap-2">
          <div className="w-full bg-white rounded-xl p-2.5 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80 flex items-center justify-between gap-2">
            <div className="w-6 h-6 rounded-lg bg-zinc-950 text-white flex items-center justify-center shadow-2xs">
              <Radio className="w-3 h-3 text-zinc-300" />
            </div>
            <div className="flex items-center gap-1 flex-1 justify-center h-4">
              <span className="w-1 h-2 bg-zinc-300 rounded-full" />
              <span className="w-1 h-4 bg-zinc-800 rounded-full" />
              <span className="w-1 h-3 bg-zinc-400 rounded-full" />
              <span className="w-1 h-4 bg-zinc-900 rounded-full" />
              <span className="w-1 h-2 bg-zinc-300 rounded-full" />
            </div>
            <span className="text-[10px] font-mono font-semibold text-zinc-700">01:42</span>
          </div>

          <div className="px-3 py-1 bg-zinc-950 text-white rounded-lg text-[10px] font-medium flex items-center gap-1 shadow-xs">
            <Check className="w-3 h-3 text-zinc-300" />
            <span>Structured Brief Ready</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 5. LEAD CRM (Monochromatic Kanban Deal Card) ──
  if (slug === 'lead-crm') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col gap-1.5">
          <div className="flex items-center justify-between px-0.5">
            <span className="px-2 py-0.5 rounded-md bg-white border border-zinc-200 text-zinc-800 text-[10px] font-semibold shadow-2xs">
              + Inbound Lead
            </span>
            <span className="text-[10px] font-mono text-zinc-500">Pipeline: ₹12.4L</span>
          </div>

          <div className="w-full bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-400 mb-1">
              <span className="font-semibold text-zinc-900">FASHION EDITORIAL</span>
              <span>Tomorrow</span>
            </div>
            <p className="text-xs font-semibold text-zinc-900 mb-2 truncate">
              Lakmé Campaign Shoot
            </p>
            <div className="flex items-center justify-between pt-1.5 border-t border-zinc-100">
              <span className="text-xs font-mono font-bold text-zinc-900">₹1,85,000</span>
              <span className="px-2 py-0.5 rounded bg-zinc-100 text-[9px] font-mono font-semibold text-zinc-800 border border-zinc-200/60">
                Contract Sent
              </span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 6. CANVAS BUILDER (Monochrome Layout Mockup) ──
  if (slug === 'canvas-builder') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-100">
            <div className="flex items-center gap-1">
              <span className="w-1.5 h-1.5 rounded-full bg-zinc-300" />
              <span className="w-1.5 h-1.5 rounded-full bg-zinc-300" />
              <span className="w-1.5 h-1.5 rounded-full bg-zinc-300" />
            </div>
            <div className="bg-zinc-100 px-1.5 py-0.5 rounded text-[9px] font-mono text-zinc-700">
              Desktop ⌄
            </div>
          </div>
          <div className="space-y-1.5">
            <div className="w-full h-6 bg-zinc-100 rounded-lg flex items-center justify-center text-[9px] font-semibold text-zinc-700">
              [ Hero Video Banner ]
            </div>
            <div className="grid grid-cols-2 gap-1.5">
              <div className="h-7 bg-zinc-50 border border-dashed border-zinc-200 rounded-lg flex items-center justify-center text-[8px] text-zinc-500">
                Pricing Grid
              </div>
              <div className="h-7 bg-zinc-950 text-white rounded-lg flex items-center justify-center text-[8px] font-semibold">
                Book Shoot ↗
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 7. FORM BUILDER (Monochrome Form Preview) ──
  if (slug === 'form-builder') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="flex items-center justify-between text-[10px] font-semibold text-zinc-900 mb-2">
            <span>Shoot Inquiry Form</span>
            <span className="text-[9px] font-mono text-zinc-400">&lt;iframe /&gt;</span>
          </div>
          <div className="space-y-1.5 text-[10px]">
            <div className="px-2 py-1 bg-zinc-50 rounded-md border border-zinc-200/70 text-zinc-700">
              Name: <span className="font-semibold text-zinc-900">Karan Mehta</span>
            </div>
            <div className="px-2 py-1 bg-zinc-50 rounded-md border border-zinc-200/70 text-zinc-700 flex justify-between">
              <span>Type:</span>
              <span className="font-semibold text-zinc-900">Commercial Film ⌄</span>
            </div>
          </div>
          <div className="mt-2 w-full py-1 bg-zinc-950 text-white rounded-md text-[9px] font-semibold text-center">
            Submit &amp; Schedule
          </div>
        </div>
      </div>
    );
  }

  // ── 8. REVIEW PORTAL (5-Star Neutral Slate Card) ──
  if (slug === 'review-portal') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col items-center gap-2">
          <div className="w-full bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80 text-center">
            <div className="flex items-center justify-center gap-1 text-zinc-900 mb-1">
              {[...Array(5)].map((_, i) => (
                <Star key={i} className="w-3.5 h-3.5 fill-zinc-900 text-zinc-900" />
              ))}
            </div>
            <p className="text-[11px] font-semibold text-zinc-900 mb-0.5">
              5.0 • &ldquo;Outstanding 4K Production!&rdquo;
            </p>
            <span className="text-[9px] text-zinc-400 font-mono">Google Verified</span>
          </div>

          <span className="px-3 py-1 bg-zinc-950 text-white rounded-full text-[9px] font-medium shadow-xs">
            Automated WhatsApp Trigger
          </span>
        </div>
      </div>
    );
  }

  // ── 9. E-SIGN VAULT (Clean Neutral Legal Seal) ──
  if (slug === 'esign-vault') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
            <div className="flex items-center justify-between text-[10px] text-zinc-400 font-mono mb-1.5">
              <span className="font-semibold text-zinc-900 flex items-center gap-1">
                <Lock className="w-3 h-3 text-zinc-700" /> SHA-256 VAULT
              </span>
              <span>ID: 8F2A...90B</span>
            </div>
            
            <div className="p-2 bg-zinc-50 rounded-lg border border-zinc-200/70 mb-2">
              <div className="text-[11px] font-semibold text-zinc-900">Commercial Production Agreement</div>
              <div className="text-[9px] text-zinc-500 font-mono">IT Act 2000 Compliant</div>
            </div>

            <div className="flex items-center justify-between pt-1">
              <span className="font-serif italic text-xs text-zinc-900 font-semibold">Shruti Sharma ✓</span>
              <span className="text-[9px] font-mono font-semibold text-zinc-700 bg-zinc-100 px-1.5 py-0.5 rounded border border-zinc-200">
                TIMESTAMPED
              </span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 10. CREW DISPATCH (Call Sheet Matrix in Monochrome) ──
  if (slug === 'crew-dispatch') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="flex items-center justify-between text-[10px] font-semibold text-zinc-900 mb-2">
            <span>Call Sheet Dispatch</span>
            <span className="text-[9px] font-mono text-zinc-500">0 Conflicts</span>
          </div>

          <div className="flex items-center justify-between gap-1 mb-2">
            {['S', 'M', 'T', 'W', 'T', 'F'].map((day, idx) => (
              <span
                key={idx}
                className={`w-6 h-6 rounded-md text-[9px] font-mono font-semibold flex items-center justify-center ${
                  idx === 1 || idx === 2
                    ? 'bg-zinc-950 text-white'
                    : 'bg-zinc-100 text-zinc-500'
                }`}
              >
                {day}
              </span>
            ))}
          </div>

          <div className="p-1.5 bg-zinc-50 rounded-lg border border-zinc-200/70 text-[10px] flex items-center justify-between">
            <span className="text-zinc-700">Studio Floor B</span>
            <span className="font-mono font-bold text-zinc-950">06:30 AM Call</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 11. MASTER CALENDAR (Monochrome Schedule) ──
  if (slug === 'master-calendar') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="flex items-center justify-between text-[10px] font-semibold text-zinc-900 mb-2">
            <span>November 2026</span>
            <span className="text-[9px] font-mono text-zinc-500">4 Shoots</span>
          </div>
          
          <div className="space-y-1.5 text-[10px]">
            <div className="p-1.5 rounded-md bg-zinc-950 text-white font-medium flex items-center justify-between">
              <span>● Vogue Fashion Shoot</span>
              <span className="text-[9px] font-mono text-zinc-300">09:00 AM</span>
            </div>
            <div className="p-1.5 rounded-md bg-zinc-100 text-zinc-900 border border-zinc-200 font-medium flex items-center justify-between">
              <span>● Villa Drone Tour</span>
              <span className="text-[9px] font-mono text-zinc-500">02:30 PM</span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 12. TASK BOARD (Milestone Checklist) ──
  if (slug === 'task-board') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="flex items-center justify-between text-[10px] text-zinc-500 mb-2">
            <span className="font-semibold text-zinc-900">Post-Production</span>
            <span className="px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-800 font-mono text-[8px] font-bold">RUSH</span>
          </div>

          <div className="space-y-1 text-[10px]">
            <div className="flex items-center gap-1.5 text-zinc-800 font-medium">
              <span className="w-3.5 h-3.5 rounded bg-zinc-950 text-white flex items-center justify-center text-[8px] font-bold">✓</span>
              <span>4K RAW Ingest &amp; Backup</span>
            </div>
            <div className="flex items-center gap-1.5 text-zinc-800 font-medium">
              <span className="w-3.5 h-3.5 rounded bg-zinc-950 text-white flex items-center justify-center text-[8px] font-bold">✓</span>
              <span>Resolve Color Grade</span>
            </div>
            <div className="flex items-center gap-1.5 text-zinc-400">
              <span className="w-3.5 h-3.5 rounded border border-zinc-300" />
              <span>Final Client Delivery</span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 13. GST INVOICING (B2B Tax Breakdown Card) ──
  if (slug === 'gst-invoicing') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="flex items-center justify-between text-[10px] text-zinc-500 mb-1.5 font-mono">
            <span className="font-bold text-zinc-900">TAX INVOICE</span>
            <span className="text-[9px] font-bold text-zinc-700 bg-zinc-100 px-1.5 py-0.2 rounded border border-zinc-200">
              PAID
            </span>
          </div>

          <div className="space-y-1 text-[10px] pb-1.5 mb-1.5 border-b border-zinc-100">
            <div className="flex justify-between text-zinc-600">
              <span>Subtotal:</span>
              <span className="font-mono">₹1,50,000</span>
            </div>
            <div className="flex justify-between text-zinc-700 font-semibold">
              <span>18% GST (CGST/SGST):</span>
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

  // ── 14. ASSET GEAR (Cinema Gear Tracking) ──
  if (slug === 'asset-gear') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="flex items-center gap-2 mb-2">
            <div className="w-6 h-6 rounded-md bg-zinc-950 text-white flex items-center justify-center">
              <Camera className="w-3.5 h-3.5" />
            </div>
            <div>
              <div className="text-[11px] font-semibold text-zinc-900">Sony FX6 Cinema Kit</div>
              <div className="text-[9px] font-mono text-zinc-400">#SN-98214</div>
            </div>
          </div>

          <div className="flex items-center justify-between p-1.5 bg-zinc-50 rounded-md text-[9px] font-mono font-semibold text-zinc-800 border border-zinc-200/60">
            <span>CHECKED OUT</span>
            <span>Return: 8:00 PM</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 15. MEDIA HUB (Folder Storage & Presets) ──
  if (slug === 'media-hub') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="flex items-center justify-between text-[10px] font-semibold text-zinc-900 mb-2">
            <span>RAW Media Vault</span>
            <span className="text-[9px] font-mono text-zinc-400">4.2 TB</span>
          </div>

          <div className="flex items-center justify-center gap-1.5 py-1 bg-zinc-50 rounded-lg border border-zinc-200/70 mb-1.5">
            <span className="px-2 py-0.5 bg-white rounded text-[9px] font-mono font-semibold text-zinc-700 shadow-2xs border border-zinc-200">
              1:1
            </span>
            <span className="px-2 py-0.5 bg-zinc-950 rounded text-[9px] font-mono font-semibold text-white shadow-2xs">
              4:3 Preset
            </span>
            <span className="px-2 py-0.5 bg-white rounded text-[9px] font-mono font-semibold text-zinc-700 shadow-2xs border border-zinc-200">
              16:9
            </span>
          </div>

          <div className="text-[9px] text-center text-zinc-400 font-mono">
            Global Edge CDN Delivery
          </div>
        </div>
      </div>
    );
  }

  // ── 16. RBAC SYSTEM (Access Permissions) ──
  if (slug === 'rbac-system') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="text-[10px] font-semibold text-zinc-900 mb-2">Studio Access Roles</div>
          <div className="space-y-1 text-[10px]">
            <div className="flex items-center justify-between p-1.5 bg-zinc-50 rounded-md border border-zinc-200/60">
              <span className="font-semibold text-zinc-900">Admin (Shruti)</span>
              <span className="text-[9px] font-mono font-semibold text-zinc-700">Full Access</span>
            </div>
            <div className="flex items-center justify-between p-1.5 bg-white rounded-md text-zinc-600">
              <span>Lead DOP / Camera</span>
              <span className="text-[9px] font-mono text-zinc-400">Shoots Only</span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 17. EMAIL SMTP (Branded Delivery) ──
  if (slug === 'email-smtp') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="flex items-center gap-2 mb-2">
            <div className="w-6 h-6 rounded-md bg-zinc-950 text-white flex items-center justify-center">
              <Mail className="w-3.5 h-3.5" />
            </div>
            <div>
              <div className="text-[11px] font-semibold text-zinc-900">Custom SMTP Sender</div>
              <div className="text-[9px] font-mono text-zinc-400">studio@brand.com</div>
            </div>
          </div>
          <div className="flex items-center justify-between p-1 bg-zinc-50 rounded-md text-[9px] font-mono text-zinc-700 border border-zinc-200/60">
            <span>DKIM: VERIFIED</span>
            <span>84.6% Open</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 18. PWA PUSH (Notification Banner) ──
  if (slug === 'pwa-push') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="flex items-center justify-between text-[10px] text-zinc-400 font-mono mb-1">
            <span className="font-semibold text-zinc-900 flex items-center gap-1">
              <Bell className="w-3 h-3 text-zinc-700" /> PUSH ALERT
            </span>
            <span>2m ago</span>
          </div>
          <div className="text-[11px] font-semibold text-zinc-900 mb-0.5 truncate">
            Shoot: Taj Lands End
          </div>
          <div className="text-[9px] text-zinc-500 font-mono">
            Call: 06:30 AM • 4 Confirmed
          </div>
        </div>
      </div>
    );
  }

  // ── 19. DOCS PORTAL (Clean API Snippet) ──
  if (slug === 'docs-portal') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="flex items-center justify-between text-[9px] font-mono text-zinc-400 mb-1.5">
            <span>REST API</span>
            <span className="text-zinc-900 font-bold">200 OK</span>
          </div>
          <div className="p-1.5 bg-zinc-100 rounded-md font-mono text-[10px] text-zinc-900 mb-1">
            GET /v1/shoots/live
          </div>
          <div className="text-[9px] text-zinc-400 text-center font-mono">
            Interactive Test Console
          </div>
        </div>
      </div>
    );
  }

  // ── 20. SUPER ADMIN (Multi-Workspace Hub) ──
  if (slug === 'super-admin') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[250px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80">
          <div className="text-[10px] font-semibold text-zinc-900 mb-1.5">Multi-Workspace Governance</div>
          <div className="space-y-1 text-[10px]">
            <div className="flex items-center justify-between p-1 bg-zinc-50 rounded-md border border-zinc-200/60 font-medium text-zinc-900">
              <span>Mumbai Studio</span>
              <span className="font-mono font-bold text-zinc-900">₹48.2L</span>
            </div>
            <div className="flex items-center justify-between p-1 text-zinc-600">
              <span>Bangalore Media</span>
              <span className="font-mono">₹22.4L</span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── ROADMAP MOCKUPS (Clean White / Light Zinc Monochrome) ──
  if (slug === 'whatsapp-cloud') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[240px] bg-white rounded-xl p-3 border border-zinc-200/80 shadow-[0_4px_12px_rgba(0,0,0,0.04)]">
          <div className="flex items-center gap-1.5 mb-1.5 text-zinc-900 text-[10px] font-semibold">
            <MessageCircle className="w-3.5 h-3.5 text-zinc-700" /> WhatsApp Cloud API
          </div>
          <div className="p-1.5 bg-zinc-50 rounded-md text-[10px] text-zinc-700 mb-1 border border-zinc-200/60">
            &ldquo;Shoot confirmed for 9:00 AM&rdquo;
          </div>
          <div className="text-[9px] text-zinc-400 font-mono">2-Way Live Sync</div>
        </div>
      </div>
    );
  }

  if (slug === 'photo-proofing') {
    return (
      <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
        <div className="relative w-full max-w-[240px] bg-white rounded-xl p-3 border border-zinc-200/80 shadow-[0_4px_12px_rgba(0,0,0,0.04)]">
          <div className="flex items-center justify-between text-[10px] font-semibold text-zinc-900 mb-1.5">
            <span>Watermark Proofing</span>
            <span className="text-zinc-500 font-mono">5 Stars</span>
          </div>
          <div className="h-8 bg-zinc-50 rounded-md flex items-center justify-center text-[9px] font-mono text-zinc-500 border border-dashed border-zinc-200">
            [ Watermarked RAW Gallery ]
          </div>
        </div>
      </div>
    );
  }

  // ── DEFAULT CLEAN MONOCHROME FALLBACK ──
  return (
    <div className="w-full h-full bg-[#FAFAFA] relative flex items-center justify-center p-4">
      <div className="relative w-full max-w-[240px] bg-white rounded-xl p-3 shadow-[0_4px_12px_rgba(0,0,0,0.04)] border border-zinc-200/80 text-center">
        <div className="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-zinc-950 text-white mb-1.5 shadow-2xs">
          <Sparkles className="w-3.5 h-3.5 text-zinc-200" />
        </div>
        <div className="text-xs font-semibold text-zinc-950 truncate">{title}</div>
        <div className="text-[9px] text-zinc-400 font-mono mt-0.5">In Pipeline</div>
      </div>
    </div>
  );
}
