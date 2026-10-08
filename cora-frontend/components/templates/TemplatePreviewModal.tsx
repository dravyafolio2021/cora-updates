'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { 
  X, 
  ArrowRight, 
  Layers, 
  ShieldCheck, 
  Receipt, 
  Sparkles, 
  Laptop, 
  Tablet, 
  Smartphone, 
  CheckCircle2, 
  ExternalLink 
} from 'lucide-react';
import { TemplateItem } from '@/lib/templates-data';

interface TemplatePreviewModalProps {
  template: TemplateItem | null;
  isOpen: boolean;
  onClose: () => void;
}

export function TemplatePreviewModal({
  template,
  isOpen,
  onClose,
}: TemplatePreviewModalProps) {
  const [deviceView, setDeviceView] = useState<'desktop' | 'tablet' | 'mobile'>('desktop');
  const [activeTab, setActiveTab] = useState<'overview' | 'pages' | 'schemas'>('overview');

  if (!isOpen || !template) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 animate-in fade-in duration-200">
      
      {/* Backdrop */}
      <div 
        className="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity cursor-pointer"
        onClick={onClose}
      />

      {/* Modal Card */}
      <div className="relative z-10 w-full max-w-[1040px] max-h-[90vh] bg-white rounded-3xl border border-zinc-200 shadow-2xl flex flex-col overflow-hidden text-left animate-in zoom-in-95 duration-200">
        
        {/* Top Action Header */}
        <div className="flex items-center justify-between px-5 sm:px-7 py-4 bg-zinc-50 border-b border-zinc-100 shrink-0">
          <div className="flex items-center gap-3">
            <div 
              className="w-8 h-8 rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-2xs"
              style={{ backgroundColor: template.accentColor }}
            >
              ✦
            </div>
            <div>
              <div className="flex items-center gap-2">
                <h3 className="text-sm sm:text-base font-bold text-zinc-950">
                  {template.title}
                </h3>
                <span className="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-zinc-200/80 text-zinc-800">
                  {template.badge}
                </span>
              </div>
              <span className="text-xs text-zinc-500">
                {template.categoryLabel} &bull; {template.sacCode} ({template.gstRate})
              </span>
            </div>
          </div>

          {/* Device Controls & Close */}
          <div className="flex items-center gap-2">
            {/* View Mode Switcher */}
            <div className="hidden sm:flex items-center bg-zinc-200/80 p-0.5 rounded-lg text-zinc-600">
              <button
                type="button"
                onClick={() => setDeviceView('desktop')}
                title="Desktop View"
                className={`p-1.5 rounded-md text-xs font-semibold transition-all ${deviceView === 'desktop' ? 'bg-white text-zinc-950 shadow-2xs' : 'hover:text-zinc-900'}`}
              >
                <Laptop className="w-4 h-4" />
              </button>
              <button
                type="button"
                onClick={() => setDeviceView('tablet')}
                title="Tablet View"
                className={`p-1.5 rounded-md text-xs font-semibold transition-all ${deviceView === 'tablet' ? 'bg-white text-zinc-950 shadow-2xs' : 'hover:text-zinc-900'}`}
              >
                <Tablet className="w-4 h-4" />
              </button>
              <button
                type="button"
                onClick={() => setDeviceView('mobile')}
                title="Mobile View"
                className={`p-1.5 rounded-md text-xs font-semibold transition-all ${deviceView === 'mobile' ? 'bg-white text-zinc-950 shadow-2xs' : 'hover:text-zinc-900'}`}
              >
                <Smartphone className="w-4 h-4" />
              </button>
            </div>

            <button
              type="button"
              onClick={onClose}
              className="p-1.5 rounded-full text-zinc-400 hover:text-zinc-900 hover:bg-zinc-200/80 transition-colors cursor-pointer ml-1"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Tab Navigation */}
        <div className="flex items-center gap-4 px-5 sm:px-7 pt-3 border-b border-zinc-100 bg-white shrink-0">
          <button
            type="button"
            onClick={() => setActiveTab('overview')}
            className={`pb-2.5 text-xs sm:text-sm font-semibold border-b-2 transition-all cursor-pointer ${
              activeTab === 'overview'
                ? 'border-zinc-950 text-zinc-950'
                : 'border-transparent text-zinc-500 hover:text-zinc-800'
            }`}
          >
            Overview &amp; Features
          </button>
          <button
            type="button"
            onClick={() => setActiveTab('pages')}
            className={`pb-2.5 text-xs sm:text-sm font-semibold border-b-2 transition-all cursor-pointer flex items-center gap-1.5 ${
              activeTab === 'pages'
                ? 'border-zinc-950 text-zinc-950'
                : 'border-transparent text-zinc-500 hover:text-zinc-800'
            }`}
          >
            <span>Included Pages</span>
            <span className="text-[10px] font-mono px-1.5 py-0.2 bg-zinc-100 text-zinc-600 rounded-full">
              {template.includedPages.length}
            </span>
          </button>
          <button
            type="button"
            onClick={() => setActiveTab('schemas')}
            className={`pb-2.5 text-xs sm:text-sm font-semibold border-b-2 transition-all cursor-pointer flex items-center gap-1.5 ${
              activeTab === 'schemas'
                ? 'border-zinc-950 text-zinc-950'
                : 'border-transparent text-zinc-500 hover:text-zinc-800'
            }`}
          >
            <span>Database Schemas</span>
            <span className="text-[10px] font-mono px-1.5 py-0.2 bg-zinc-100 text-zinc-600 rounded-full">
              {template.schemaStructures.length}
            </span>
          </button>
        </div>

        {/* Scrollable Content Body */}
        <div className="flex-1 overflow-y-auto p-5 sm:p-7 space-y-6 overscroll-contain">
          
          {activeTab === 'overview' && (
            <div className="space-y-6">
              <div>
                <h4 className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2">
                  Kit Description
                </h4>
                <p className="text-sm text-zinc-700 leading-relaxed">
                  {template.heroDescription}
                </p>
              </div>

              {/* Key Features */}
              <div>
                <h4 className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 mb-3">
                  Pre-Configured Capabilities
                </h4>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                  {template.features.map((feature, idx) => (
                    <div key={idx} className="flex items-start gap-2 p-3 rounded-xl bg-zinc-50 border border-zinc-200/80">
                      <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                      <span className="text-xs font-medium text-zinc-800 leading-snug">{feature}</span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Tools Replaced */}
              <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80">
                <div className="text-xs font-bold text-zinc-900 mb-2">
                  Replaces Separately Paid Software Subscriptions:
                </div>
                <div className="flex flex-wrap gap-2">
                  {template.toolsReplaced.map((t, idx) => (
                    <span key={idx} className="text-xs font-mono text-zinc-700 bg-white px-2.5 py-1 rounded-lg border border-zinc-200 shadow-2xs">
                      {t.name} <span className="text-zinc-400 font-sans">(saves ${t.monthlyCostUSD}/mo)</span>
                    </span>
                  ))}
                </div>
              </div>
            </div>
          )}

          {activeTab === 'pages' && (
            <div className="space-y-3">
              {template.includedPages.map((page, idx) => (
                <div key={idx} className="flex items-start justify-between p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200/80">
                  <div className="flex items-start gap-3">
                    <div className="w-7 h-7 rounded-lg bg-white border border-zinc-200 flex items-center justify-center text-xs font-bold text-zinc-700 shrink-0 mt-0.5">
                      {idx + 1}
                    </div>
                    <div>
                      <div className="text-xs sm:text-sm font-bold text-zinc-950 flex items-center gap-2">
                        <span>{page.name}</span>
                        <span className="text-[10px] font-mono uppercase bg-zinc-200 px-1.5 py-0.2 rounded text-zinc-700">
                          {page.type}
                        </span>
                      </div>
                      <p className="text-xs text-zinc-600 mt-0.5">
                        {page.description}
                      </p>
                    </div>
                  </div>
                  <span className="text-[11px] font-mono text-zinc-400">/{page.slug}</span>
                </div>
              ))}
            </div>
          )}

          {activeTab === 'schemas' && (
            <div className="space-y-4">
              {template.schemaStructures.map((schema, idx) => (
                <div key={idx} className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-3">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                      <span className="text-xs sm:text-sm font-bold text-zinc-950">{schema.name}</span>
                      <span className="text-[10px] font-mono bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full font-bold">
                        {schema.badge}
                      </span>
                    </div>
                  </div>
                  <p className="text-xs text-zinc-600">{schema.description}</p>
                  
                  <div className="border border-zinc-200 rounded-xl overflow-hidden bg-white">
                    <table className="w-full text-left text-xs">
                      <thead className="bg-zinc-100/80 text-zinc-600 font-mono text-[10.5px]">
                        <tr>
                          <th className="p-2.5">Field Name</th>
                          <th className="p-2.5">Type</th>
                          <th className="p-2.5">Description</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-zinc-100 font-sans">
                        {schema.fields.map((f, fIdx) => (
                          <tr key={fIdx} className="hover:bg-zinc-50/60">
                            <td className="p-2.5 font-mono font-bold text-zinc-900">{f.name}</td>
                            <td className="p-2.5 font-mono text-purple-700">{f.type}</td>
                            <td className="p-2.5 text-zinc-600">{f.description}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>
              ))}
            </div>
          )}

        </div>

        {/* Bottom CTA Footer */}
        <div className="flex items-center justify-between p-4 sm:p-6 bg-zinc-50 border-t border-zinc-100 shrink-0">
          <Link
            href={`/templates/${template.slug}`}
            onClick={onClose}
            className="text-xs sm:text-sm font-bold text-zinc-700 hover:text-zinc-950 inline-flex items-center gap-1.5 transition-colors"
          >
            <span>Full Blueprint Specs</span>
            <ExternalLink className="w-3.5 h-3.5" />
          </Link>

          <a
            href={`https://app.heycora.in/workspace/login?template=${template.slug}&source=template_modal`}
            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all shadow-xs"
          >
            <Sparkles className="w-4 h-4 text-emerald-400" />
            <span>Deploy Schema to Workspace</span>
            <ArrowRight className="w-4 h-4" />
          </a>
        </div>

      </div>

    </div>
  );
}
