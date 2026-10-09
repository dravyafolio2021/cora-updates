'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { 
  X, 
  ArrowRight, 
  Layers, 
  Sparkles, 
  Laptop, 
  Tablet, 
  Smartphone, 
  CheckCircle2, 
  ExternalLink,
  Copy,
  Check,
  Code2,
  Bot,
  Eye,
  LayoutTemplate
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
  const [activeTab, setActiveTab] = useState<'visual' | 'prompts' | 'code' | 'overview'>('visual');
  const [copiedSectionId, setCopiedSectionId] = useState<string | null>(null);

  if (!isOpen || !template) return null;

  const handleCopy = (text: string, id: string) => {
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      navigator.clipboard.writeText(text);
      setCopiedSectionId(id);
      setTimeout(() => setCopiedSectionId(null), 2000);
    }
  };

  const formatNumber = (num: number) => {
    if (num >= 1000) {
      return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
    }
    return num.toString();
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 animate-in fade-in duration-200">
      
      {/* Backdrop */}
      <div 
        className="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity cursor-pointer"
        onClick={onClose}
      />

      {/* Modal Card */}
      <div className="relative z-10 w-full max-w-[1040px] max-h-[92vh] bg-white rounded-3xl border border-zinc-200 shadow-2xl flex flex-col overflow-hidden text-left animate-in zoom-in-95 duration-200">
        
        {/* Top Action Header */}
        <div className="flex items-center justify-between px-5 sm:px-7 py-4 bg-zinc-50 border-b border-zinc-100 shrink-0">
          <div className="flex items-center gap-3">
            <div 
              className="w-8 h-8 rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-2xs shrink-0"
              style={{ backgroundColor: template.accentColor }}
            >
              ✦
            </div>
            <div>
              <div className="flex items-center gap-2">
                <h3 className="text-sm sm:text-base font-bold text-zinc-950 line-clamp-1">
                  {template.title}
                </h3>
                <span className="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-zinc-200/80 text-zinc-800 shrink-0">
                  {template.formatBadge}
                </span>
              </div>
              <div className="flex items-center gap-3 text-xs text-zinc-500 font-mono mt-0.5">
                <span className="text-purple-600 font-semibold">{formatNumber(template.copiesCount || 650)} copies</span>
                <span>&bull;</span>
                <span>{formatNumber(template.viewsCount || 2400)} views</span>
              </div>
            </div>
          </div>

          {/* Device Controls & Close */}
          <div className="flex items-center gap-2">
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
        <div className="flex items-center gap-4 px-5 sm:px-7 pt-3 border-b border-zinc-100 bg-white shrink-0 overflow-x-auto">
          <button
            type="button"
            onClick={() => setActiveTab('visual')}
            className={`pb-2.5 text-xs sm:text-sm font-semibold border-b-2 transition-all cursor-pointer flex items-center gap-1.5 shrink-0 ${
              activeTab === 'visual'
                ? 'border-zinc-950 text-zinc-950'
                : 'border-transparent text-zinc-500 hover:text-zinc-800'
            }`}
          >
            <Eye className="w-4 h-4 text-zinc-800" />
            <span>Visual Preview</span>
          </button>

          <button
            type="button"
            onClick={() => setActiveTab('prompts')}
            className={`pb-2.5 text-xs sm:text-sm font-semibold border-b-2 transition-all cursor-pointer flex items-center gap-1.5 shrink-0 ${
              activeTab === 'prompts'
                ? 'border-zinc-950 text-zinc-950'
                : 'border-transparent text-zinc-500 hover:text-zinc-800'
            }`}
          >
            <Bot className="w-4 h-4 text-purple-600" />
            <span>AI Master Prompts</span>
            <span className="text-[10px] font-mono px-1.5 py-0.2 bg-purple-100 text-purple-700 rounded-full font-bold">
              {template.sectionsIncluded.length}
            </span>
          </button>

          <button
            type="button"
            onClick={() => setActiveTab('overview')}
            className={`pb-2.5 text-xs sm:text-sm font-semibold border-b-2 transition-all cursor-pointer shrink-0 ${
              activeTab === 'overview'
                ? 'border-zinc-950 text-zinc-950'
                : 'border-transparent text-zinc-500 hover:text-zinc-800'
            }`}
          >
            Asset Specs &amp; Stack
          </button>

          <button
            type="button"
            onClick={() => setActiveTab('code')}
            className={`pb-2.5 text-xs sm:text-sm font-semibold border-b-2 transition-all cursor-pointer flex items-center gap-1.5 shrink-0 ${
              activeTab === 'code'
                ? 'border-zinc-950 text-zinc-950'
                : 'border-transparent text-zinc-500 hover:text-zinc-800'
            }`}
          >
            <Code2 className="w-4 h-4 text-zinc-700" />
            <span>Code &amp; Blueprints</span>
          </button>
        </div>

        {/* Scrollable Content Body */}
        <div className="flex-1 overflow-y-auto p-5 sm:p-7 space-y-6 overscroll-contain">
          
          {/* TAB 1: VISUAL PREVIEW */}
          {activeTab === 'visual' && (
            <div className="flex flex-col items-center justify-center space-y-4">
              <div 
                className={`transition-all duration-300 w-full overflow-hidden rounded-2xl bg-zinc-950 border border-zinc-800 shadow-xl flex items-center justify-center ${
                  deviceView === 'mobile' ? 'max-w-[360px]' : deviceView === 'tablet' ? 'max-w-[640px]' : 'max-w-[960px]'
                }`}
              >
                {template.mediaType === 'video' && template.mediaUrl ? (
                  <video
                    src={template.mediaUrl}
                    autoPlay
                    loop
                    muted
                    playsInline
                    className="w-full h-auto max-h-[58vh] object-contain"
                  />
                ) : template.mediaUrl ? (
                  <img
                    src={template.mediaUrl}
                    alt={template.title}
                    className="w-full h-auto max-h-[58vh] object-contain"
                  />
                ) : (
                  <div className="py-20 text-zinc-500 text-sm">Visual preview unavailable</div>
                )}
              </div>

              {/* Instant Action Bar Under Preview */}
              <div className="w-full flex items-center justify-between p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80">
                <div>
                  <div className="text-xs font-bold text-zinc-950">{template.title}</div>
                  <div className="text-xs text-zinc-500">{template.tagline}</div>
                </div>
                <button
                  onClick={() => handleCopy(template.quickCopyPrompt, 'visual-prompt')}
                  className="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-black text-white text-xs font-bold transition-all flex items-center gap-1.5 shrink-0"
                >
                  {copiedSectionId === 'visual-prompt' ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5" />}
                  <span>{copiedSectionId === 'visual-prompt' ? 'Prompt Copied!' : 'Copy AI Prompt'}</span>
                </button>
              </div>
            </div>
          )}

          {/* TAB 2: AI MASTER PROMPTS */}
          {activeTab === 'prompts' && (
            <div className="space-y-4">
              <div className="p-3.5 rounded-2xl bg-purple-50/70 border border-purple-200/80 flex items-center justify-between">
                <div className="text-xs text-purple-900 font-medium">
                  Copy any section prompt and paste into <strong>Cursor Composer (⌘I)</strong>, <strong>Claude 3.5 Sonnet</strong>, <strong>Lovable</strong>, or <strong>v0</strong>.
                </div>
                <button
                  onClick={() => handleCopy(template.quickCopyPrompt, 'all-prompt')}
                  className="px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 ml-3"
                >
                  {copiedSectionId === 'all-prompt' ? <Check className="w-3.5 h-3.5" /> : <Copy className="w-3.5 h-3.5" />}
                  <span>{copiedSectionId === 'all-prompt' ? 'Copied Master Prompt!' : 'Copy Master Prompt'}</span>
                </button>
              </div>

              {template.sectionsIncluded.map((sec) => (
                <div key={sec.id} className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-3">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                      <span className="text-xs sm:text-sm font-bold text-zinc-950">{sec.name}</span>
                      <span className="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-zinc-200 text-zinc-700">
                        {sec.badge}
                      </span>
                    </div>
                    <button
                      onClick={() => handleCopy(sec.promptText, sec.id)}
                      className="px-2.5 py-1 rounded-lg bg-white border border-zinc-200 text-zinc-800 text-xs font-semibold hover:bg-zinc-100 transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer"
                    >
                      {copiedSectionId === sec.id ? (
                        <>
                          <Check className="w-3.5 h-3.5 text-emerald-600" />
                          <span className="text-emerald-700 font-bold">Copied!</span>
                        </>
                      ) : (
                        <>
                          <Copy className="w-3.5 h-3.5 text-zinc-500" />
                          <span>Copy Prompt</span>
                        </>
                      )}
                    </button>
                  </div>

                  <p className="text-xs text-zinc-600">{sec.description}</p>

                  <div className="p-3 bg-zinc-900 text-zinc-200 rounded-xl font-mono text-[11px] leading-relaxed overflow-x-auto whitespace-pre-wrap border border-zinc-800 select-all">
                    {sec.promptText}
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* TAB 3: OVERVIEW */}
          {activeTab === 'overview' && (
            <div className="space-y-6">
              <div>
                <h4 className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2">
                  Asset Overview
                </h4>
                <p className="text-sm text-zinc-700 leading-relaxed">
                  {template.heroDescription}
                </p>
              </div>

              {/* Supported Builders */}
              <div>
                <h4 className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2">
                  Compatible Builders &amp; Frameworks
                </h4>
                <div className="flex flex-wrap gap-2">
                  {template.compatibleBuilders.map((builder, idx) => (
                    <span key={idx} className="text-xs font-mono text-zinc-800 bg-zinc-100 px-3 py-1 rounded-lg border border-zinc-200/80 font-medium">
                      {builder}
                    </span>
                  ))}
                </div>
              </div>

              {/* Key Features */}
              <div>
                <h4 className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 mb-3">
                  Frontend Highlights
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
                  Replaces Expensive Third-Party Subscriptions:
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

          {/* TAB 4: CODE */}
          {activeTab === 'code' && (
            <div className="space-y-4">
              <div className="text-xs text-zinc-600">
                Production-grade standalone HTML5 markup with Tailwind CSS via CDN. Zero JSX, zero build step, instant browser execution.
              </div>

              {template.sectionsIncluded.map((sec) => (
                <div key={sec.id} className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-2">
                  <div className="flex items-center justify-between">
                    <span className="text-xs font-bold text-zinc-900">{sec.name} — HTML5 / Tailwind CDN</span>
                    <button
                      onClick={() => handleCopy(sec.tailwindSnippet || sec.promptText, `code-${sec.id}`)}
                      className="px-2.5 py-1 rounded-lg bg-white border border-zinc-200 text-zinc-800 text-xs font-semibold flex items-center gap-1.5 shadow-2xs cursor-pointer"
                    >
                      {copiedSectionId === `code-${sec.id}` ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Copy className="w-3.5 h-3.5 text-zinc-500" />}
                      <span>{copiedSectionId === `code-${sec.id}` ? 'Copied HTML!' : 'Copy HTML Code'}</span>
                    </button>
                  </div>
                  <pre className="p-3 bg-zinc-900 text-emerald-400 rounded-xl font-mono text-[11px] overflow-x-auto max-h-48 whitespace-pre">
                    {sec.tailwindSnippet || sec.elementorBlueprint || sec.promptText}
                  </pre>
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
            <span>View Full Asset Details</span>
            <ExternalLink className="w-3.5 h-3.5" />
          </Link>

          <Link
            href={`/templates/${template.slug}`}
            onClick={onClose}
            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all shadow-xs"
          >
            <Sparkles className="w-4 h-4 text-purple-400" />
            <span>Open Asset Page</span>
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>

      </div>

    </div>
  );
}
