'use client';

import React, { useEffect } from 'react';
import { X, CheckCircle2, ShieldCheck, Receipt, ArrowRight, FileText, Sparkles, Layers, Building2 } from 'lucide-react';
import { IndustryWorkspace } from '@/lib/industry-data';
import { IndustryIcon } from './IndustryIcon';

interface IndustryDetailModalProps {
  workspace: IndustryWorkspace | null;
  isOpen: boolean;
  onClose: () => void;
}

export function IndustryDetailModal({
  workspace,
  isOpen,
  onClose,
}: IndustryDetailModalProps) {
  // ESC key listener & body scroll lock
  useEffect(() => {
    if (!isOpen) return;

    const originalOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';

    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        onClose();
      }
    };

    window.addEventListener('keydown', handleKeyDown);

    return () => {
      document.body.style.overflow = originalOverflow;
      window.removeEventListener('keydown', handleKeyDown);
    };
  }, [isOpen, onClose]);

  if (!isOpen || !workspace) return null;

  return (
    <div className="fixed inset-0 z-50 flex justify-end">
      {/* Dark Blurred Backdrop Overlay */}
      <div
        className="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity animate-in fade-in duration-200"
        onClick={onClose}
      />

      {/* Slide-in Drawer Sheet (Right-sliding on desktop, bottom-up sheet on mobile) */}
      <div className="relative z-10 w-full max-w-2xl bg-white h-full max-h-screen shadow-2xl flex flex-col overflow-hidden animate-in slide-in-from-right duration-300 ease-out border-l border-zinc-200">
        
        {/* Header */}
        <div className="p-6 sm:p-8 border-b border-zinc-100 flex items-start justify-between bg-zinc-50/70">
          <div className="flex items-start gap-4">
            <div className={`w-12 h-12 rounded-2xl ${workspace.accentBg} ${workspace.accentText} border ${workspace.accentBorder} flex items-center justify-center shrink-0 shadow-sm`}>
              <IndustryIcon name={workspace.iconName} className="w-6 h-6" />
            </div>
            <div>
              <div className="flex items-center gap-2 mb-1">
                <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-white px-2.5 py-0.5 rounded-full border border-zinc-200 shadow-2xs">
                  {workspace.sectorBadge}
                </span>
                <span className="text-[10px] font-mono font-semibold text-zinc-600 bg-white px-2.5 py-0.5 rounded-md border border-zinc-200/80">
                  {workspace.sacCode} • {workspace.gstRate}
                </span>
              </div>
              <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
                {workspace.title}
              </h2>
            </div>
          </div>

          <button
            onClick={onClose}
            className="p-2 rounded-xl text-zinc-400 hover:text-zinc-950 hover:bg-zinc-200/60 transition-colors"
            aria-label="Close modal"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Scrollable Content Body */}
        <div className="flex-1 overflow-y-auto p-6 sm:p-8 space-y-8">
          
          {/* Tagline & Overview */}
          <div>
            <h3 className="text-sm font-bold text-zinc-900 mb-2">
              Workspace Overview &amp; Architecture
            </h3>
            <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
              {workspace.description}
            </p>
          </div>

          {/* Key Metrics */}
          <div className="grid grid-cols-3 gap-3 p-4 rounded-2xl bg-zinc-50 border border-zinc-200/70">
            {workspace.metrics.map((m, idx) => (
              <div key={idx} className="text-center">
                <span className="block text-base sm:text-lg font-mono font-bold text-zinc-950">
                  {m.value}
                </span>
                <span className="block text-[11px] text-zinc-500 mt-0.5">
                  {m.label}
                </span>
              </div>
            ))}
          </div>

          {/* Challenge vs Solution */}
          <div className="space-y-3 p-5 rounded-2xl bg-zinc-950 text-white shadow-sm">
            <div className="flex items-center gap-2 text-xs font-mono font-bold uppercase tracking-wider text-zinc-400">
              <Sparkles className="w-4 h-4 text-amber-400" />
              <span>Operational Transformation</span>
            </div>
            <div>
              <span className="text-xs font-bold text-red-400 block mb-1">Old Way (Fragmented Apps):</span>
              <p className="text-xs text-zinc-300 font-normal leading-relaxed">
                {workspace.challengeVsSolution.challenge}
              </p>
            </div>
            <div className="pt-2 border-t border-zinc-800">
              <span className="text-xs font-bold text-emerald-400 block mb-1">Cora Way (Integrated OS):</span>
              <p className="text-xs text-zinc-200 font-normal leading-relaxed">
                {workspace.challengeVsSolution.solution}
              </p>
            </div>
          </div>

          {/* Pre-Seeded Legal Contracts & Workflows */}
          <div className="space-y-3">
            <h3 className="text-sm font-bold text-zinc-900 flex items-center gap-2">
              <FileText className="w-4 h-4 text-zinc-500" />
              <span>Pre-Seeded Contracts &amp; Forms (IT Act 2000 Ready)</span>
            </h3>
            <div className="space-y-2">
              {workspace.preSeededTemplates.map((tmpl, idx) => (
                <div
                  key={idx}
                  className="flex items-start gap-3 p-3 rounded-xl bg-zinc-50 border border-zinc-200/80 text-xs text-zinc-800"
                >
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <div className="min-w-0 flex-1">
                    <span className="font-semibold block text-zinc-900">{tmpl}</span>
                    <span className="text-[11px] text-zinc-500">Includes SHA-256 digital signature and IP audit logging</span>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Sample GST Retainer Math */}
          <div className="space-y-3">
            <h3 className="text-sm font-bold text-zinc-900 flex items-center gap-2">
              <Receipt className="w-4 h-4 text-zinc-500" />
              <span>Pre-Configured GST SAC Tax Schema</span>
            </h3>
            <div className="p-4 rounded-xl bg-zinc-50 border border-zinc-200/80 space-y-2">
              <div className="flex items-center justify-between text-xs font-mono">
                <span className="text-zinc-600">Classification:</span>
                <span className="font-bold text-zinc-950">{workspace.sacCode} • {workspace.gstRate}</span>
              </div>
              <div className="pt-2 border-t border-zinc-200/60 text-xs text-zinc-700 font-mono bg-white p-3 rounded-lg border border-zinc-200">
                <span className="text-[10px] text-zinc-400 font-bold uppercase block mb-1">Sample Retainer Billing Calculation:</span>
                <p className="text-[11.5px] leading-relaxed text-zinc-800">
                  {workspace.sampleRetainerText}
                </p>
              </div>
            </div>
          </div>

          {/* Recommended Module Stack */}
          <div className="space-y-3">
            <h3 className="text-sm font-bold text-zinc-900 flex items-center gap-2">
              <Layers className="w-4 h-4 text-zinc-500" />
              <span>Active Cora Modules in this Workspace</span>
            </h3>
            <div className="grid grid-cols-2 gap-2">
              {workspace.recommendedModules.map((mod) => (
                <div
                  key={mod.id}
                  className="flex items-center gap-2.5 p-3 rounded-xl bg-zinc-50 border border-zinc-200/80 text-xs font-semibold text-zinc-900"
                >
                  <IndustryIcon name={mod.icon} className="w-4 h-4 text-zinc-600" />
                  <span>{mod.title}</span>
                </div>
              ))}
            </div>
          </div>

        </div>

        {/* Fixed Footer CTA */}
        <div className="p-6 border-t border-zinc-200 bg-white flex flex-col sm:flex-row items-center justify-between gap-4">
          <div className="text-xs text-zinc-500 text-center sm:text-left">
            <span className="font-semibold text-zinc-900">Zero Setup Required:</span> Launch with all {workspace.preSeededTemplates.length} templates pre-loaded.
          </div>
          <a
            href={`https://app.heycora.in/workspace/login?industry=${workspace.id}&source=use_cases_modal`}
            className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm group"
          >
            <span>Launch Free {workspace.title} Workspace</span>
            <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
          </a>
        </div>

      </div>
    </div>
  );
}
