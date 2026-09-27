'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, BookOpen, Clock, Download, Layers, FileText, CheckCircle2 } from 'lucide-react';
import { Guide } from '@/lib/guides-data';
import { getBlogCategoryById } from '@/lib/blog-data';

interface GuideCardProps {
  guide: Guide;
  featured?: boolean;
}

export function GuideCard({ guide, featured = false }: GuideCardProps) {
  const category = getBlogCategoryById(guide.category);

  return (
    <article
      className={`group relative flex flex-col justify-between rounded-2xl bg-white border border-zinc-200/90 hover:border-zinc-300/90 shadow-2xs hover:shadow-md transition-all duration-300 overflow-hidden ${
        featured ? 'md:grid md:grid-cols-12 md:gap-8 md:items-center p-2' : ''
      }`}
    >
      <div className={featured ? 'md:col-span-6' : ''}>
        {/* Book Cover Thumbnail */}
        <Link
          href={`/guides/${guide.slug}/`}
          className="block aspect-[16/10] w-full overflow-hidden bg-zinc-100 relative rounded-xl border border-zinc-200/70"
        >
          {guide.coverImage ? (
            <Image
              src={guide.coverImage}
              alt={guide.coverAlt || guide.title}
              fill
              sizes={featured ? '(max-width: 768px) 100vw, 50vw' : '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw'}
              className="object-cover group-hover:scale-[1.02] transition-transform duration-500 ease-out"
            />
          ) : (
            <div className="w-full h-full flex flex-col items-center justify-center bg-zinc-950 text-white p-6 text-center">
              <BookOpen className="w-8 h-8 text-zinc-400 mb-2" />
              <span className="font-display font-bold text-sm">{guide.title}</span>
            </div>
          )}

          {/* Quality Badge Overlay */}
          <div className="absolute top-3 left-3 flex items-center gap-1.5 z-10">
            <span className="px-2.5 py-0.5 rounded-full bg-zinc-950/80 backdrop-blur-md text-white text-[10px] font-mono font-bold tracking-wider uppercase">
              {guide.qualityLabel || 'Playbook'}
            </span>
          </div>

          {/* Resource Badge Overlay */}
          {guide.downloadableAsset && (
            <div className="absolute bottom-3 left-3 flex items-center gap-1 z-10">
              <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/95 backdrop-blur-md text-zinc-950 text-[10px] font-mono font-semibold shadow-sm">
                <Download className="w-2.5 h-2.5 text-zinc-700" />
                <span>PDF Included</span>
              </span>
            </div>
          )}
        </Link>
      </div>

      <div className={`p-5 sm:p-6 flex flex-col justify-between flex-1 ${featured ? 'md:col-span-6 md:p-4' : ''}`}>
        <div>
          {/* Metadata Row */}
          <div className="flex flex-wrap items-center justify-between gap-2 text-[11px] font-mono mb-2.5">
            <span className="text-zinc-600 font-medium">
              {category?.name || guide.category}
            </span>
            <div className="flex items-center gap-2 text-zinc-400">
              <span className="flex items-center gap-1 text-zinc-600">
                <Layers className="w-3 h-3" />
                <span>{guide.chapterCount} Chapters</span>
              </span>
              <span>&bull;</span>
              <span className="flex items-center gap-1 text-zinc-600">
                <Clock className="w-3 h-3" />
                <span>{guide.readTime}</span>
              </span>
            </div>
          </div>

          {/* Guide Title */}
          <h3 className="font-display text-base sm:text-lg font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors leading-snug tracking-tight line-clamp-2">
            <Link href={`/guides/${guide.slug}/`}>
              {guide.title}
            </Link>
          </h3>

          {/* Short Value Proposition */}
          <p className="mt-2 text-xs sm:text-sm text-zinc-600 line-clamp-2 leading-relaxed">
            {guide.dek || guide.excerpt}
          </p>

          {/* Resource Badges Row */}
          {guide.resourceBadges && guide.resourceBadges.length > 0 && (
            <div className="mt-3.5 flex flex-wrap items-center gap-1.5">
              {guide.resourceBadges.map((badge, idx) => (
                <span
                  key={idx}
                  className="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 text-[10px] font-mono font-medium border border-zinc-200/80"
                >
                  {badge}
                </span>
              ))}
            </div>
          )}
        </div>

        {/* Read Action Footer */}
        <div className="pt-4 mt-4 border-t border-zinc-100 flex items-center justify-between">
          <span className="text-xs font-bold text-zinc-950 inline-flex items-center gap-1.5 group-hover:text-zinc-600 transition-colors">
            <span>Read Digital Book</span>
            <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
          </span>
          <span className="text-[11px] font-mono text-zinc-500 font-medium">
            Free Resource
          </span>
        </div>
      </div>
    </article>
  );
}
