'use client';

import React from 'react';
import { 
  Code, 
  LayoutTemplate, 
  ShieldCheck, 
  Zap, 
  Scale, 
  Receipt, 
  Briefcase, 
  Layers, 
  BarChart2, 
  Sparkles, 
  Camera, 
  Building2, 
  Heart, 
  Scissors,
  CheckCircle2,
  Lock,
  ArrowRight,
  Clock,
  Calendar,
  MousePointer,
  Cpu,
  FileText,
  Check,
  Star,
  Film
} from 'lucide-react';

interface IndustryCardVisualProps {
  slug: string;
  title: string;
}

export function IndustryCardVisual({ slug, title }: IndustryCardVisualProps) {
  
  // ── 1. SOFTWARE AGENCIES (Sprint Retainers & Milestone Sign-Offs) ──
  if (slug === 'software-agencies') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          {/* Top Sprint Pill */}
          <div className="flex items-center justify-between px-1">
            <span className="px-2.5 py-0.5 rounded-full bg-zinc-950 text-white text-[10px] font-mono font-bold shadow-2xs flex items-center gap-1.5">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
              Active Sprint
            </span>
            <span className="text-[10px] font-mono text-zinc-600 bg-white border border-zinc-200 px-2 py-0.5 rounded-md">
              18% GST Retainer
            </span>
          </div>

          {/* Staging Approval Card */}
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[11px] font-bold text-zinc-900 mb-1">
              <span>Client Milestone Approval</span>
              <span className="text-zinc-900 font-mono text-[10px] flex items-center gap-0.5">
                <Check className="w-3 h-3 text-emerald-600" /> Approved
              </span>
            </div>
            <div className="flex items-center justify-between text-[10px] text-zinc-500 pt-1 border-t border-zinc-100">
              <span>Milestone Release</span>
              <span className="font-mono font-bold text-zinc-900">₹2,50,000 + 18% GST</span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // ── 2. WEB & APP STUDIOS (4-Phase Wireframe to Launch Gates) ──
  if (slug === 'web-app-studios') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-bold text-zinc-900 mb-2">
              <span>PROJECT PHASES</span>
              <span className="bg-zinc-100 text-zinc-800 px-2 py-0.5 rounded-full border border-zinc-200/70">50% Deposit</span>
            </div>
            {/* Step Indicators */}
            <div className="grid grid-cols-4 gap-1 text-center text-[9px] font-mono">
              <div className="bg-zinc-950 text-white py-1 rounded-md font-bold">Scope</div>
              <div className="bg-zinc-800 text-white py-1 rounded-md font-bold">Design</div>
              <div className="bg-zinc-100 text-zinc-700 py-1 rounded-md">Build</div>
              <div className="bg-zinc-100 text-zinc-400 py-1 rounded-md">Launch</div>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-2xs flex items-center gap-1.5">
            <Sparkles className="w-3 h-3 text-zinc-300" />
            <span>Scope Protection Active</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 3. IT & TECH SERVICES (Uptime SLAs & Hardware Assets) ──
  if (slug === 'it-tech-services') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-400 mb-1.5">
              <span className="font-bold text-zinc-900 flex items-center gap-1">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                99.9% UPTIME SLA
              </span>
              <span>24/7 Monitored</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Managed Support Retainer</span>
              <span className="text-[11px] font-mono text-zinc-900 font-bold">₹1.20L/mo</span>
            </div>
          </div>
          <div className="flex items-center justify-between px-2 text-[10px] font-mono text-zinc-600 bg-white rounded-xl p-1.5 border border-zinc-200/80 shadow-2xs">
            <span>Assigned Hardware:</span>
            <span className="font-bold text-zinc-900">42 Devices</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 4. AI & AUTOMATION AGENCIES (Autonomous Workflows & Knowledge Base) ──
  if (slug === 'ai-automation') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono mb-1.5">
              <span className="font-bold text-zinc-900 flex items-center gap-1">
                <Zap className="w-3 h-3 text-zinc-700" />
                AUTOMATED WORKFLOW
              </span>
              <span className="bg-zinc-100 text-zinc-800 px-1.5 py-0.5 rounded text-[9px] font-bold border border-zinc-200/80">Live</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Studio Knowledge Sync</span>
              <span className="text-[10px] font-mono text-zinc-500">Connected</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-2xs flex items-center gap-1">
            <Lock className="w-3 h-3 text-zinc-400" />
            <span>Private &amp; Secure Data</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 5. LAWYERS & LAW FIRMS (Legal E-Sign Vaults & Retainers) ──
  if (slug === 'lawyers-law-firms') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-600 mb-1.5">
              <span className="font-bold text-zinc-900 flex items-center gap-1">
                <Scale className="w-3 h-3 text-zinc-700" />
                LEGAL COMPLIANCE
              </span>
              <span className="text-zinc-900 bg-zinc-100 border border-zinc-200 px-1.5 py-0.5 rounded font-bold">Tamper-Proof</span>
            </div>
            <p className="text-xs font-bold text-zinc-900 truncate">
              Client Retainer Agreement
            </p>
          </div>
          <div className="flex items-center justify-between px-2.5 py-1 bg-zinc-900 text-white rounded-xl text-[10px] font-mono shadow-2xs">
            <span>Status:</span>
            <span className="text-emerald-400 font-bold flex items-center gap-1">
              <Check className="w-2.5 h-2.5" /> Legally Signed
            </span>
          </div>
        </div>
      </div>
    );
  }

  // ── 6. TAX & CA PRACTICES (18% GST Tax Math & Instant UPI) ──
  if (slug === 'tax-ca-firms') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono mb-1.5">
              <span className="font-bold text-zinc-900">TAX AUDIT RETAINER</span>
              <span className="bg-zinc-100 text-zinc-800 px-1.5 py-0.5 rounded text-[9px] font-bold border border-zinc-200/80">UPI QR</span>
            </div>
            <div className="flex justify-between text-xs font-mono text-zinc-900 font-bold">
              <span>CGST 9% + SGST 9%</span>
              <span className="text-zinc-950">₹3,54,000</span>
            </div>
          </div>
          <div className="flex items-center justify-between px-2 text-[10px] font-mono text-zinc-500">
            <span>Accountant-Ready Report</span>
            <span className="text-zinc-900 font-bold flex items-center gap-0.5">
              <Check className="w-3 h-3 text-emerald-600" /> Ready to Export
            </span>
          </div>
        </div>
      </div>
    );
  }

  // ── 7. FINANCIAL ADVISORS (Portfolio Records & Wealth Retainers) ──
  if (slug === 'financial-advisors') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-700 mb-1.5">
              <span className="font-bold">CLIENT RISK PROFILE</span>
              <span className="font-bold text-zinc-900">Score 84/100</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Advisory Retainer</span>
              <span className="text-zinc-900 font-mono">Quarterly</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-2xs flex items-center gap-1">
            <Lock className="w-3 h-3 text-zinc-400" />
            <span>Encrypted Portfolio Vault</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 8. AUDIT & COMPLIANCE (Multi-Point Audit Matrix) ──
  if (slug === 'audit-compliance') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-700 mb-1.5">
              <span className="font-bold">AUDIT CHECKLIST</span>
              <span className="text-emerald-700 font-bold flex items-center gap-0.5">
                <Check className="w-3 h-3 text-emerald-600" /> 100% Passed
              </span>
            </div>
            <div className="space-y-1 text-[10px] text-zinc-600 font-medium">
              <div className="flex items-center justify-between"><span>Access Permissions</span><Check className="w-3 h-3 text-emerald-600" /></div>
              <div className="flex items-center justify-between"><span>Financial Records</span><Check className="w-3 h-3 text-emerald-600" /></div>
            </div>
          </div>
          <div className="flex justify-center text-[10px] font-mono text-zinc-700 font-bold">
            Multi-Branch Security Active
          </div>
        </div>
      </div>
    );
  }

  // ── 9. MARKETING & SEO (Monthly Retainers & Client Dashboards) ──
  if (slug === 'marketing-seo') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-700 mb-1.5">
              <span className="font-bold">CAMPAIGN PERFORMANCE</span>
              <span className="bg-zinc-100 text-zinc-800 px-1.5 py-0.5 rounded text-[9px] font-bold border border-zinc-200/80">Live</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Growth Retainer</span>
              <span className="text-zinc-900 font-mono font-bold">₹1.50L/mo</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-2xs flex items-center gap-1.5">
            <Sparkles className="w-3 h-3 text-zinc-300" />
            <span>Branded Client Dashboard</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 10. DESIGN & UI/UX (Design Sprints & Asset Deliverables) ──
  if (slug === 'design-uiux') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-700 mb-1.5">
              <span className="font-bold">2-WEEK SPRINT</span>
              <span className="bg-zinc-100 text-zinc-800 px-1.5 py-0.5 rounded text-[9px] font-bold border border-zinc-200/80">Figma</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>High-Res Brand Files</span>
              <span className="text-zinc-600 text-[10px] flex items-center gap-1">
                <Lock className="w-2.5 h-2.5" /> Unlocks on Pay
              </span>
            </div>
          </div>
          <div className="flex justify-center text-[10px] font-mono text-zinc-500">
            Client IP &amp; Copyright Transfer Ready
          </div>
        </div>
      </div>
    );
  }

  // ── 11. PHOTO & VIDEO STUDIOS (4K Client Proofing & Call Sheets) ──
  if (slug === 'photo-video-studios') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-700 mb-1.5">
              <span className="font-bold">CLIENT PROOFING VAULT</span>
              <span className="bg-zinc-100 text-zinc-800 px-1.5 py-0.5 rounded text-[9px] font-bold border border-zinc-200/80">50% Advance</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Crew Call Sheet</span>
              <span className="text-zinc-900 font-mono font-bold">4K Deliverables</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-2xs flex items-center gap-1">
            <Film className="w-3 h-3 text-zinc-300" />
            <span>Studio Equipment Assigned</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 12. ARCHITECTURE & INTERIORS (Drawings & Project Milestones) ──
  if (slug === 'architecture-interiors') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-700 mb-1.5">
              <span className="font-bold">MILESTONE STAGE #2</span>
              <span className="bg-zinc-100 text-zinc-800 px-1.5 py-0.5 rounded text-[9px] font-bold border border-zinc-200/80">3D Concepts</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Construction Drawings</span>
              <span className="text-zinc-900 font-bold flex items-center gap-0.5">
                <Check className="w-3 h-3 text-emerald-600" /> Signed
              </span>
            </div>
          </div>
          <div className="flex justify-center text-[10px] font-mono text-zinc-700 font-bold">
            Change-Order Addendum Protected
          </div>
        </div>
      </div>
    );
  }

  // ── 13. CONSULTANTS & ADVISORS (Voice Briefs & Strategy Retainers) ──
  if (slug === 'consultants-advisors') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-700 mb-1.5">
              <span className="font-bold">VOICE SCOPE BUILDER</span>
              <span className="bg-zinc-100 text-zinc-800 px-1.5 py-0.5 rounded text-[9px] font-bold border border-zinc-200/80">Instant Proposal</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Strategy Advisory Mandate</span>
              <span className="text-zinc-900 font-mono font-bold">₹4.00L/mo</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-2xs flex items-center gap-1">
            <FileText className="w-3 h-3 text-zinc-400" />
            <span>Executive Presentation Ready</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 14. DOCTORS & CLINICS (Digital Intake & Consent) ──
  if (slug === 'doctors-clinics') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-700 mb-1.5">
              <span className="font-bold">DIGITAL INTAKE FORM</span>
              <span className="text-zinc-900 font-bold flex items-center gap-0.5">
                <Check className="w-3 h-3 text-emerald-600" /> 100% Paperless
              </span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Client Consent Form</span>
              <span className="text-zinc-600 font-mono">Mobile Verified</span>
            </div>
          </div>
          <div className="flex justify-center text-[10px] font-mono text-zinc-500 flex items-center gap-1">
            <Lock className="w-3 h-3 text-zinc-400" />
            <span>Confidential Client Vault</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 15. SALONS & WELLNESS (Booking Packages & Fast Payments) ──
  if (slug === 'salons-wellness') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-700 mb-1.5">
              <span className="font-bold">STUDIO SERVICE PACKAGE</span>
              <span className="bg-zinc-100 text-zinc-800 px-1.5 py-0.5 rounded text-[9px] font-bold border border-zinc-200/80">50% Advance</span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Appointment Reserved</span>
              <span className="text-zinc-900 font-mono font-bold">UPI QR</span>
            </div>
          </div>
          <div className="self-center bg-zinc-950 text-white px-3.5 py-1 rounded-full text-[10px] font-bold shadow-2xs flex items-center gap-1">
            <Star className="w-3 h-3 text-zinc-300" />
            <span>Automated 5-Star Reviews</span>
          </div>
        </div>
      </div>
    );
  }

  // ── 16. REAL ESTATE & PROPERTY (Property Portals & Brokerage Mandates) ──
  if (slug === 'real-estate-property') {
    return (
      <div className="w-full h-44 bg-zinc-50/75 relative flex items-center justify-center p-3.5 overflow-hidden rounded-t-3xl border-b border-zinc-200/80">
        <div className="relative w-full max-w-[270px] flex flex-col gap-2">
          <div className="w-full bg-white rounded-2xl p-3 shadow-2xs border border-zinc-200/90">
            <div className="flex items-center justify-between text-[10px] font-mono text-zinc-700 mb-1.5">
              <span className="font-bold">EXCLUSIVE MANDATE</span>
              <span className="text-zinc-900 font-bold flex items-center gap-0.5">
                <Check className="w-3 h-3 text-emerald-600" /> Signed
              </span>
            </div>
            <div className="flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Virtual Tour Landing Page</span>
              <span className="text-zinc-900 font-mono font-bold">Live</span>
            </div>
          </div>
          <div className="flex justify-center text-[10px] font-mono text-zinc-500">
            18% GST Invoicing &amp; Brokerage Ledger
          </div>
        </div>
      </div>
    );
  }

  // Default Fallback
  return (
    <div className="w-full h-44 bg-zinc-50 flex items-center justify-center rounded-t-3xl border-b border-zinc-100">
      <span className="text-xs font-mono text-zinc-400">{title} Workspace</span>
    </div>
  );
}
