'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { 
  ArrowRight, 
  Sparkles, 
  Star, 
  Layers, 
  Eye, 
  Copy, 
  Check, 
  Code2, 
  LayoutTemplate,
  Bot,
  Play
} from 'lucide-react';
import { TemplateItem } from '@/lib/templates-data';

interface TemplateCardProps {
  template: TemplateItem;
  onQuickPreview?: (template: TemplateItem) => void;
}

export function TemplateCard({ template, onQuickPreview }: TemplateCardProps) {
  const [copied, setCopied] = useState(false);
  const [copiesCount, setCopiesCount] = useState<number>(template.copiesCount || 650);

  const formatNumber = (num: number) => {
    if (num >= 1000) {
      return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
    }
    return num.toString();
  };

  const handleCopyPrompt = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    const promptToCopy = template.quickCopyPrompt || template.sectionsIncluded[0]?.promptText || '';
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      navigator.clipboard.writeText(promptToCopy);
      setCopied(true);
      setCopiesCount(prev => prev + 1);
      setTimeout(() => setCopied(false), 2200);
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
      
      {/* Top Preview Frame with Live Media Stream */}
      <div 
        onClick={() => onQuickPreview && onQuickPreview(template)}
        className="relative w-full aspect-[16/10] bg-zinc-950 overflow-hidden border-b border-zinc-100 flex items-center justify-center cursor-pointer select-none"
      >
        
        {/* Live Media Thumbnail: Video or Image/GIF */}
        {template.mediaType === 'video' && template.mediaUrl ? (
          <video
            src={template.mediaUrl}
            autoPlay
            loop
            muted
            playsInline
            className="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
            aria-label={template.title}
          />
        ) : template.mediaUrl ? (
          <img
            src={template.mediaUrl}
            alt={template.title}
            loading="lazy"
            className="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
          />
        ) : (
          <div className="w-full h-full bg-zinc-900 flex items-center justify-center text-zinc-600">
            <Sparkles className="w-8 h-8 opacity-40" />
          </div>
        )}

        {/* Top Floating Format Tag */}
        <div className="absolute top-2.5 inset-x-3 flex items-center justify-between z-10 pointer-events-none">
          <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-900 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full border border-zinc-200/80 shadow-xs flex items-center gap-1.5">
            {getFormatIcon(template.assetFormat)}
            <span>{template.formatBadge}</span>
          </span>
          <span className="text-[9.5px] font-mono font-semibold px-2 py-0.5 rounded-full bg-black/75 backdrop-blur-md text-white border border-white/10">
            {template.badge}
          </span>
        </div>

        {/* Hover Quick Action Overlay */}
        <div className="absolute inset-0 bg-zinc-950/50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col justify-end p-3.5 z-20 backdrop-blur-[1.5px]">
          <div className="flex items-center justify-between gap-2">
            <button
              type="button"
              onClick={(e) => {
                e.stopPropagation();
                if (onQuickPreview) onQuickPreview(template);
              }}
              className="px-3 py-1.5 rounded-xl bg-white/95 hover:bg-white text-zinc-950 text-xs font-semibold shadow-md transition-all flex items-center gap-1.5 cursor-pointer backdrop-blur-xs"
            >
              <Eye className="w-3.5 h-3.5 text-zinc-700" />
              <span>Preview</span>
            </button>

            <button
              type="button"
              onClick={handleCopyPrompt}
              className="px-3.5 py-1.5 rounded-xl bg-zinc-900 hover:bg-black text-white text-xs font-semibold shadow-md transition-all flex items-center gap-1.5 cursor-pointer border border-zinc-700"
            >
              {copied ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5 text-zinc-300" />}
              <span>{copied ? 'Copied!' : 'Use Prompt'}</span>
            </button>
          </div>
        </div>

      </div>

      {/* Card Content Details */}
      <div className="p-4 sm:p-5 flex-1 flex flex-col justify-between">
        
        <div>
          {/* Views & Remixes Metrics Row */}
          <div className="flex items-center justify-between gap-2 mb-2 text-xs text-zinc-500 font-mono">
            <div className="flex items-center gap-3">
              <span className="inline-flex items-center gap-1 text-zinc-600 font-medium">
                <Sparkles className="w-3 h-3 text-purple-600" />
                <span>{formatNumber(copiesCount)} copies</span>
              </span>
              <span className="inline-flex items-center gap-1 text-zinc-500">
                <Eye className="w-3 h-3 text-zinc-400" />
                <span>{formatNumber(template.viewsCount || 2400)} views</span>
              </span>
            </div>

            <div className="flex items-center gap-1 text-[11px] font-medium text-zinc-700">
              <Star className="w-3 h-3 text-amber-500 fill-amber-500" />
              <span className="font-bold">{template.rating}</span>
            </div>
          </div>

          {/* Title & Tagline */}
          <Link href={`/templates/${template.slug}`} className="block group/title">
            <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950 group-hover/title:text-black tracking-tight line-clamp-1 mb-1">
              {template.title}
            </h3>
            <p className="text-xs text-zinc-600 line-clamp-2 leading-relaxed font-normal mb-3.5">
              {template.tagline}
            </p>
          </Link>

          {/* Compatible Frameworks Badge Row */}
          <div className="flex flex-wrap gap-1.5 mb-4">
            {template.compatibleBuilders.slice(0, 3).map((builder, idx) => (
              <span key={idx} className="text-[10px] font-mono text-zinc-600 bg-zinc-100/90 px-2 py-0.5 rounded border border-zinc-200/70">
                {builder}
              </span>
            ))}
            {template.compatibleBuilders.length > 3 && (
              <span className="text-[10px] font-mono text-zinc-400 bg-zinc-50 px-1.5 py-0.5 rounded border border-zinc-200/70">
                +{template.compatibleBuilders.length - 3}
              </span>
            )}
          </div>
        </div>

        {/* Bottom Action Footer */}
        <div className="pt-3 border-t border-zinc-100 flex items-center justify-between gap-2">
          <button
            type="button"
            onClick={handleCopyPrompt}
            className="inline-flex items-center gap-1 text-xs font-semibold text-zinc-700 hover:text-zinc-950 transition-colors cursor-pointer"
          >
            {copied ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Copy className="w-3.5 h-3.5 text-zinc-500" />}
            <span>{copied ? 'Prompt Copied!' : 'Copy Prompt'}</span>
          </button>

          <Link
            href={`/templates/${template.slug}`}
            className="inline-flex items-center gap-1 text-xs font-bold text-zinc-950 group-hover:text-black transition-colors"
          >
            <span>Explore</span>
            <ArrowRight className="w-3 h-3 transition-transform group-hover:translate-x-0.5" />
          </Link>
        </div>

      </div>

    </div>
  );
}
