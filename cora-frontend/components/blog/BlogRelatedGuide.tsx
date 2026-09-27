'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { BookOpen, ArrowRight, Layers, Download, CheckCircle2, ShieldCheck } from 'lucide-react';
import type { ParentGuideRef } from '@/lib/blog-data';

interface BlogRelatedGuideProps {
  guide: ParentGuideRef;
}

export function BlogRelatedGuide({ guide }: BlogRelatedGuideProps) {
  if (!guide) return null;

  return (
    <section 
      aria-label="Parent Guide & Digital Book Spotlight"
      className="my-10 sm:my-14 rounded-3xl border border-zinc-200/90 bg-[#FBFaf7] p-6 sm:p-8 lg:p-9 shadow-sm relative overflow-hidden"
    >
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div className="max-w-2xl space-y-3">
          {/* Header pill */}
          <div className="flex flex-wrap items-center gap-2 text-[11px] font-mono">
            <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-zinc-950 text-white font-bold uppercase tracking-wider">
              <BookOpen className="w-3 h-3 text-zinc-300" />
              <span>Parent Digital Book</span>
            </span>
            {guide.readTime && (
              <span className="text-zinc-600 font-medium">
                &bull; {guide.readTime}
              </span>
            )}
          </div>

          {/* Title */}
          <h3 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-950 leading-snug">
            <Link 
              href={`/guides/${guide.slug}/`}
              className="hover:text-zinc-700 transition-colors"
            >
              {guide.title}
            </Link>
          </h3>

          {/* Description */}
          <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
            {guide.dek}
          </p>

          {/* Resource Badges */}
          {guide.resourceBadges && guide.resourceBadges.length > 0 && (
            <div className="pt-2 flex flex-wrap items-center gap-2">
              {guide.resourceBadges.map((badge, idx) => (
                <span 
                  key={idx}
                  className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full border border-zinc-200/80 bg-white text-[11px] font-mono font-medium text-zinc-700 shadow-2xs"
                >
                  <CheckCircle2 className="w-3 h-3 text-zinc-900" />
                  <span>{badge}</span>
                </span>
              ))}
            </div>
          )}
        </div>

        {/* CTA Button */}
        <div className="shrink-0 pt-2 md:pt-0">
          <Link
            href={`/guides/${guide.slug}/`}
            className="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-zinc-950 hover:bg-black text-white text-xs sm:text-sm font-bold transition-all shadow-sm hover:shadow-md group cursor-pointer"
          >
            <span>{guide.ctaText || 'Read Full Digital Book'}</span>
            <ArrowRight className="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
          </Link>
        </div>
      </div>
    </section>
  );
}
