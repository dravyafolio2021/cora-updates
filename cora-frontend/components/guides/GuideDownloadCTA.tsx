'use client';

import React from 'react';
import { Download, FileText, ArrowRight, Check, PackageCheck } from 'lucide-react';
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
      <div className="mt-8 p-5 sm:p-6 rounded-2xl border border-zinc-200 bg-[#FBFaf7] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div className="space-y-1">
          <div className="flex items-center gap-2 text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-500">
            <span className="px-2 py-0.5 rounded bg-zinc-200/70 text-zinc-800 font-bold">
              FREE DOWNLOAD
            </span>
            <span>{asset.fileType.toUpperCase()} {asset.fileSize ? `• ${asset.fileSize}` : ''}</span>
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
          className="shrink-0 px-4 py-2.5 rounded-xl bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all flex items-center gap-2 cursor-pointer shadow-sm"
        >
          <Download className="w-3.5 h-3.5" />
          <span>Get the Pack Free</span>
        </button>
      </div>
    );
  }

  if (placement === 'rail') {
    return (
      <div className="p-5 rounded-2xl border border-zinc-200 bg-[#FBFaf7] text-xs space-y-3">
        <div className="flex items-center justify-between gap-1 text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-500">
          <span>OPERATIONAL PACK</span>
          <span className="text-emerald-700 font-bold">FREE</span>
        </div>

        <div className="font-display font-bold text-sm text-zinc-950 leading-snug">
          {asset.title}
        </div>

        <p className="text-zinc-600 leading-relaxed text-[11px]">
          Includes editable SOP checklists, contract clauses, and kickoff decks.
        </p>

        <button
          onClick={handleClick}
          className="w-full py-2.5 px-3 rounded-xl bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm"
        >
          <Download className="w-3.5 h-3.5" />
          <span>Download Pack</span>
        </button>
      </div>
    );
  }

  if (placement === 'mid') {
    return (
      <div className="my-10 p-6 sm:p-8 rounded-3xl border border-zinc-200/90 bg-[#FBFaf7] shadow-sm">
        <div className="flex items-center gap-2 text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-500 mb-2">
          <PackageCheck className="w-4 h-4 text-zinc-700" />
          <span>CHAPTER RESOURCE PACK</span>
        </div>

        <h3 className="font-display text-lg sm:text-xl font-bold tracking-tight text-zinc-950">
          Want the editable templates for this playbook?
        </h3>

        <p className="mt-2 text-xs sm:text-sm text-zinc-600 leading-relaxed max-w-xl">
          Get {asset.title} with editable Notion boards, intake questionnaires, and contract clauses ready to implement.
        </p>

        <div className="mt-5 flex flex-wrap items-center gap-3">
          <button
            onClick={handleClick}
            className="px-5 py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all flex items-center gap-2 cursor-pointer shadow-sm"
          >
            <Download className="w-4 h-4" />
            <span>Download Onboarding Pack</span>
          </button>
          <span className="text-[11px] font-mono text-zinc-600">Instant unlock &bull; Zero spam</span>
        </div>
      </div>
    );
  }

  // End of Guide placement
  return (
    <div className="my-12 p-8 rounded-3xl border border-zinc-200 bg-[#FBFaf7] text-center max-w-2xl mx-auto shadow-sm">
      <div className="w-12 h-12 rounded-2xl bg-zinc-950 text-white flex items-center justify-center mx-auto mb-4">
        <Download className="w-6 h-6" />
      </div>

      <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-500 mb-1">
        COMPLETE PLAYBOOK ASSETS
      </div>

      <h3 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-950">
        Deploy This System in Your Agency
      </h3>

      <p className="mt-2 text-xs sm:text-sm text-zinc-600 max-w-md mx-auto leading-relaxed">
        Download {asset.title} with editable Notion intake templates, kickoff agenda slides, and milestone contract clauses.
      </p>

      {asset.highlights && (
        <div className="my-6 max-w-md mx-auto grid grid-cols-1 sm:grid-cols-2 gap-2 text-left">
          {asset.highlights.map((h, i) => (
            <div key={i} className="flex items-start gap-2 text-xs text-zinc-800">
              <Check className="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" />
              <span className="leading-snug">{h}</span>
            </div>
          ))}
        </div>
      )}

      <button
        onClick={handleClick}
        className="px-6 py-3.5 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all inline-flex items-center gap-2 cursor-pointer shadow-md"
      >
        <Download className="w-4 h-4" />
        <span>{asset.ctaText || 'Download Complete Pack'}</span>
      </button>
    </div>
  );
}
