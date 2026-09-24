'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, BookOpen, Clock, Download, Layers, Sparkles } from 'lucide-react';
import { Guide } from '@/lib/guides-data';
import { getBlogCategoryById } from '@/lib/blog-data';

interface GuideCardProps {
  guide: Guide;
}

export function GuideCard({ guide }: GuideCardProps) {
  const category = getBlogCategoryById(guide.category);

  return (
    <article className="group flex flex-col justify-between rounded-[28px] border border-zinc-200/80 bg-white hover:border-zinc-300 hover:shadow-[0_12px_40px_rgb(0,0,0,0.06)] transition-all duration-300 overflow-hidden">
      <div>
        {/* Cover Thumbnail with Apple-style smooth hover */}
        {guide.coverImage && (
          <Link
            href={`/guides/${guide.slug}/`}
            className="block aspect-[16/9] w-full overflow-hidden bg-zinc-100 relative border-b border-zinc-200/70"
          >
            <Image
              src={guide.coverImage}
              alt={guide.coverAlt || guide.title}
              fill
              sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
              className="object-cover group-hover:scale-[1.03] transition-transform duration-500 ease-out"
            />
            {/* Subtle Gradient Overlay */}
            <div className="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
          </Link>
        )}

        <div className="p-6 sm:p-7">
          {/* Header Metadata Chips */}
          <div className="flex flex-wrap items-center justify-between gap-2 text-[11px] font-mono mb-3.5">
            <span className="px-2.5 py-0.5 rounded-full bg-zinc-100 text-zinc-800 font-semibold border border-zinc-200/70 uppercase tracking-wider text-[10px]">
              {category?.name || guide.category}
            </span>
            <div className="flex items-center gap-2.5 text-zinc-400 text-[11px]">
              <span className="flex items-center gap-1">
                <Layers className="w-3 h-3 text-zinc-500" />
                <span>{guide.chapterCount} Ch.</span>
              </span>
              <span>&bull;</span>
              <span className="flex items-center gap-1">
                <Clock className="w-3 h-3 text-zinc-500" />
                <span>{guide.readTime}</span>
              </span>
            </div>
          </div>

          <h3 className="font-display text-lg sm:text-xl font-bold tracking-tight text-zinc-950 group-hover:text-zinc-700 transition-colors line-clamp-2 leading-snug">
            <Link href={`/guides/${guide.slug}/`}>
              {guide.title}
            </Link>
          </h3>

          <p className="mt-2.5 text-xs sm:text-sm text-zinc-600 line-clamp-2 leading-relaxed">
            {guide.dek || guide.excerpt}
          </p>

          {/* Download Asset Pill */}
          {guide.downloadableAsset && (
            <div className="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-zinc-50 border border-zinc-200/80 text-[11px] font-mono text-zinc-700">
              <Download className="w-3 h-3 text-zinc-600" />
              <span>Includes {guide.downloadableAsset.fileType.toUpperCase()} Pack</span>
            </div>
          )}
        </div>
      </div>

      <div className="p-6 sm:p-7 pt-0">
        <div className="pt-4 border-t border-zinc-100 flex items-center justify-between text-xs font-bold text-zinc-950">
          <Link
            href={`/guides/${guide.slug}/`}
            className="inline-flex items-center gap-1.5 group-hover:text-zinc-600 transition-colors"
          >
            <span>Read complete playbook</span>
            <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
          </Link>
        </div>
      </div>
    </article>
  );
}
