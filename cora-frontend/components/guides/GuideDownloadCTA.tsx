'use client';

import React from 'react';
import { Download, FileText, ArrowRight, Check, PackageCheck, Sparkles, CheckCircle2 } from 'lucide-react';
import { DownloadableAsset } from '@/lib/guides-data';
import { trackEvent } from '@/components/analytics/Analytics';

interface GuideDownloadCTAProps {
  asset: DownloadableAsset;
  guideTitle: string;
  guideSlug: string;
  placement: 'hero' | 'mid' | 'end' | 'rail';
  onOpenModal: (position: string) => void;
}

export function GuideDownloadCTA({
  asset,
  guideTitle,
  guideSlug,
  placement,
  onOpenModal,
}: GuideDownloadCTAProps) {
  const handleClick = () => {
    trackEvent('guide_download_cta_click', {
      guide_slug: guideSlug,
      asset_id: asset.assetId,
      cta_position: placement,
    });
    onOpenModal(placement);
  };

  if (placement === 'hero') {
    return (
      <div className="mt-8 p-5 sm:p-6 rounded-3xl border border-zinc-200/80 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.03)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div className="space-y-1">
          <div className="flex items-center gap-2 text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-500">
            <span className="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-bold">
              FREE DOWNLOAD
            </span>
            <span>{asset.fileType.toUpperCase()} &bull; {asset.fileSize || '10 Practical Templates'}</span>
          </div>
          <h3 className="text-sm sm:text-base font-bold text-zinc-950">
            {asset.title}
          </h3>
          <p className="text-xs text-zinc-600 max-w-xl">
            {asset.description}
          </p>
        </div>

        <button
          onClick={handleClick}
          className="shrink-0 px-5 py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all flex items-center gap-2 cursor-pointer shadow-sm active:scale-98"
        >
          <Download className="w-4 h-4" />
          <span>{asset.ctaText || 'Get the Free Pack'}</span>
        </button>
      </div>
    );
  }

  if (placement === 'rail') {
    return (
      <div className="p-5 rounded-3xl border border-zinc-200/80 bg-white shadow-xs text-xs space-y-3.5">
        <div className="flex items-center justify-between gap-1 text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-500">
          <span className="flex items-center gap-1.5">
            <PackageCheck className="w-3.5 h-3.5 text-zinc-700" />
            <span>PLAYBOOK ASSET</span>
          </span>
          <span className="text-emerald-700 font-bold">FREE</span>
        </div>

        <div className="font-display font-bold text-sm text-zinc-950 leading-snug">
          {asset.title}
        </div>

        <p className="text-zinc-600 leading-relaxed text-[11px]">
          {asset.description}
        </p>

        {asset.highlights && asset.highlights.length > 0 && (
          <ul className="space-y-1.5 pt-1 text-[11px] text-zinc-600">
            {asset.highlights.slice(0, 3).map((item, idx) => (
              <li key={idx} className="flex items-start gap-1.5">
                <Check className="w-3 h-3 text-emerald-600 shrink-0 mt-0.5" />
                <span className="line-clamp-1">{item}</span>
              </li>
            ))}
          </ul>
        )}

        <button
          onClick={handleClick}
          className="w-full py-2.5 px-3 rounded-xl bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm active:scale-98"
        >
          <Download className="w-3.5 h-3.5" />
          <span>Download SOP Pack</span>
        </button>
      </div>
    );
  }

  if (placement === 'mid') {
    return (
      <div className="my-12 p-6 sm:p-8 rounded-3xl border border-zinc-200/80 bg-gradient-to-b from-zinc-50/80 via-white to-white shadow-[0_8px_30px_rgb(0,0,0,0.03)]">
        <div className="flex items-center gap-2 text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-500 mb-2.5">
          <span className="w-2 h-2 rounded-full bg-emerald-500" />
          <span>PLAYBOOK IMPLEMENTATION PACK</span>
        </div>

        <h3 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-950">
          Want the 10 ready-to-use templates for this playbook?
        </h3>

        <p className="mt-2 text-xs sm:text-sm text-zinc-600 leading-relaxed max-w-xl">
          Get the complete onboarding email scripts, information intake sheets, access checklists, and kickoff agendas formatted for immediate team use.
        </p>

        {asset.highlights && asset.highlights.length > 0 && (
          <div className="mt-5 grid sm:grid-cols-2 gap-2.5">
            {asset.highlights.map((highlight, idx) => (
              <div
                key={idx}
                className="flex items-start gap-2 p-2.5 rounded-xl bg-white border border-zinc-200/70 text-xs text-zinc-800 font-medium shadow-xs"
              >
                <CheckCircle2 className="w-3.5 h-3.5 text-zinc-900 shrink-0 mt-0.5" />
                <span>{highlight}</span>
              </div>
            ))}
          </div>
        )}

        <div className="mt-6 flex flex-wrap items-center gap-3">
          <button
            onClick={handleClick}
            className="px-5 py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all flex items-center gap-2 cursor-pointer shadow-sm active:scale-98"
          >
            <Download className="w-4 h-4" />
            <span>Download All 10 Templates (Free)</span>
          </button>
          <span className="text-xs font-mono text-zinc-500">
            Instant delivery &bull; No credit card required
          </span>
        </div>
      </div>
    );
  }

  // End of Guide Placement
  return (
    <div className="my-14 p-8 sm:p-10 rounded-3xl border border-zinc-200/80 bg-gradient-to-b from-zinc-50/80 to-white text-center max-w-2xl mx-auto shadow-[0_12px_40px_rgb(0,0,0,0.04)]">
      <div className="w-12 h-12 rounded-2xl bg-zinc-950 text-white flex items-center justify-center mx-auto mb-4 shadow-sm">
        <PackageCheck className="w-6 h-6" />
      </div>

      <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 border border-zinc-200/80 text-[11px] font-mono font-bold text-zinc-800 mb-3">
        <span>COMPLETE SOP PACK</span>
      </div>

      <h3 className="font-display text-2xl sm:text-3xl font-bold tracking-tight text-zinc-950">
        Put this entire onboarding framework to work.
      </h3>

      <p className="mt-3 text-xs sm:text-sm text-zinc-600 leading-relaxed max-w-lg mx-auto">
        Download the 10-part Agency Client Onboarding Pack with client email scripts, scope boundaries, access verification matrices, and milestone review templates.
      </p>

      <div className="mt-6">
        <button
          onClick={handleClick}
          className="px-6 py-3.5 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all inline-flex items-center gap-2 cursor-pointer shadow-md hover:shadow-lg active:scale-98"
        >
          <Download className="w-4 h-4" />
          <span>Get Free Onboarding Pack</span>
        </button>
      </div>
    </div>
  );
}
