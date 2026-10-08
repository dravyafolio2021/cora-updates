'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { 
  ArrowRight, 
  Sparkles, 
  Star, 
  Layers, 
  Eye, 
  Download, 
  Copy, 
  Check, 
  Code2, 
  LayoutTemplate,
  Bot
} from 'lucide-react';
import { TemplateItem } from '@/lib/templates-data';

interface TemplateCardProps {
  template: TemplateItem;
  onQuickPreview?: (template: TemplateItem) => void;
}

export function TemplateCard({ template, onQuickPreview }: TemplateCardProps) {
  const [copied, setCopied] = useState(false);

  const handleCopyPrompt = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      navigator.clipboard.writeText(template.quickCopyPrompt || template.sectionsIncluded[0]?.promptText || '');
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  const getFormatIcon = (format: string) => {
    switch (format) {
      case 'section_prompts':
        return <Bot className="w-3.5 h-3.5 text-purple-600" />;
      case 'elementor_kit':
        return <LayoutTemplate className="w-3.5 h-3.5 text-emerald-600" />;
      case 'lovable_project':
        return <Code2 className="w-3.5 h-3.5 text-indigo-600" />;
      default:
        return <Layers className="w-3.5 h-3.5 text-zinc-700" />;
    }
  };

  return (
    <div className="group rounded-2xl sm:rounded-3xl bg-white border border-zinc-200/90 hover:border-zinc-300 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_16px_40px_rgba(0,0,0,0.08)] transition-all duration-300 flex flex-col justify-between overflow-hidden text-left">
      
      {/* Top Preview Frame with Atmospheric Vignette */}
      <div className="relative w-full aspect-[16/10] bg-zinc-100 overflow-hidden border-b border-zinc-100 flex items-center justify-center">
        
        {/* Subtle Background Scenic Vignette */}
        <div className="absolute inset-0 bg-gradient-to-br from-zinc-100 via-white to-zinc-50 opacity-90" />
        
        {/* Mock Browser Top Bar */}
        <div className="absolute top-2.5 inset-x-3.5 flex items-center justify-between z-10">
          <div className="flex items-center gap-1">
            <span className="w-2 h-2 rounded-full bg-zinc-300" />
            <span className="w-2 h-2 rounded-full bg-zinc-300" />
            <span className="w-2 h-2 rounded-full bg-zinc-300" />
          </div>
          <span className="text-[10px] font-mono font-medium text-zinc-600 bg-white/95 px-2 py-0.5 rounded-full border border-zinc-200/70 shadow-2xs flex items-center gap-1">
            {getFormatIcon(template.assetFormat)}
            <span>{template.formatBadge}</span>
          </span>
        </div>

        {/* Central Mock Visual Card */}
        <div className="relative z-10 w-[86%] bg-white rounded-xl border border-zinc-200/80 p-3.5 shadow-md group-hover:scale-[1.03] transition-transform duration-300">
          <div className="flex items-center justify-between mb-2">
            <div className="flex items-center gap-2">
              <div 
                className="w-6 h-6 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-2xs shrink-0"
                style={{ backgroundColor: template.accentColor }}
              >
                ✦
              </div>
              <div className="min-w-0">
                <div className="text-xs font-bold text-zinc-950 truncate max-w-[140px]">
                  {template.shortTitle}
                </div>
                <div className="text-[9.5px] text-zinc-400 font-mono truncate">
                  {template.sectionsIncluded.length} Ready Sections Included
                </div>
              </div>
            </div>
            <span className="text-[9.5px] font-mono font-bold px-1.5 py-0.5 rounded-md bg-zinc-100 text-zinc-700 border border-zinc-200/60 shrink-0">
              {template.badge}
            </span>
          </div>

          <div className="space-y-1.5 pt-1.5 border-t border-zinc-100">
            <div className="h-2 w-3/4 bg-zinc-100 rounded-full" />
            <div className="h-2 w-1/2 bg-zinc-100 rounded-full" />
          </div>
        </div>

        {/* Hover Quick View Overlay Action */}
        <div className="absolute inset-0 bg-zinc-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 z-20 backdrop-blur-[2px]">
          {onQuickPreview && (
            <button
              type="button"
              onClick={() => onQuickPreview(template)}
              className="px-3 py-1.5 rounded-full bg-white text-zinc-950 text-xs font-bold shadow-md hover:bg-zinc-100 transition-all flex items-center gap-1.5 cursor-pointer"
            >
              <Eye className="w-3.5 h-3.5 text-zinc-700" />
              <span>Preview</span>
            </button>
          )}

          <button
            type="button"
            onClick={handleCopyPrompt}
            className="px-3 py-1.5 rounded-full bg-purple-600 text-white text-xs font-bold shadow-md hover:bg-purple-700 transition-all flex items-center gap-1.5 cursor-pointer"
          >
            {copied ? <Check className="w-3.5 h-3.5" /> : <Copy className="w-3.5 h-3.5" />}
            <span>{copied ? 'Copied!' : 'Copy Prompt'}</span>
          </button>
        </div>

      </div>

      {/* Card Content Details */}
      <div className="p-4 sm:p-5 flex-1 flex flex-col justify-between">
        
        <div>
          {/* Category Pill & Rating */}
          <div className="flex items-center justify-between gap-2 mb-2">
            <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-600 bg-zinc-100/90 px-2 py-0.5 rounded-full border border-zinc-200/70">
              {template.categoryLabel}
            </span>
            <div className="flex items-center gap-1 text-[11px] font-medium text-zinc-600">
              <Star className="w-3.5 h-3.5 text-amber-500 fill-amber-500" />
              <span className="font-bold text-zinc-900">{template.rating}</span>
              <span className="text-zinc-400">({template.reviewsCount})</span>
            </div>
          </div>

          {/* Title & Tagline */}
          <Link href={`/templates/${template.slug}`} className="block group/title">
            <h3 className="font-display text-base sm:text-lg font-bold text-zinc-950 group-hover/title:text-black tracking-tight mb-1">
              {template.title}
            </h3>
            <p className="text-xs text-zinc-600 line-clamp-2 leading-relaxed font-normal mb-3.5">
              {template.tagline}
            </p>
          </Link>

          {/* Compatible Frameworks Badge Row */}
          <div className="flex flex-wrap gap-1.5 mb-4">
            {template.compatibleBuilders.slice(0, 3).map((builder, idx) => (
              <span key={idx} className="text-[10.5px] font-mono text-zinc-600 bg-zinc-50 px-2 py-0.5 rounded border border-zinc-200/70">
                {builder}
              </span>
            ))}
            {template.compatibleBuilders.length > 3 && (
              <span className="text-[10.5px] font-mono text-zinc-400 bg-zinc-50 px-1.5 py-0.5 rounded border border-zinc-200/70">
                +{template.compatibleBuilders.length - 3}
              </span>
            )}
          </div>
        </div>

        {/* Bottom Action Footer */}
        <div className="pt-3 border-t border-zinc-100 flex items-center justify-between">
          <div className="flex items-center gap-1.5 text-xs text-zinc-500 font-medium">
            <Download className="w-3.5 h-3.5 text-zinc-400" />
            <span>{template.downloadsCount.toLocaleString()}+ installs</span>
          </div>

          <Link
            href={`/templates/${template.slug}`}
            className="inline-flex items-center gap-1 text-xs font-bold text-zinc-950 group-hover:text-black transition-colors"
          >
            <span>Explore Asset</span>
            <ArrowRight className="w-3 h-3 transition-transform group-hover:translate-x-0.5" />
          </Link>
        </div>

      </div>

    </div>
  );
}
