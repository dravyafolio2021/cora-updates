'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, BookOpen, Clock, Download, Layers } from 'lucide-react';
import { Guide, GuideColorTheme } from '@/lib/guides-data';
import { getBlogCategoryById } from '@/lib/blog-data';

interface GuideCardProps {
  guide: Guide;
  featured?: boolean;
  index?: number;
}

const THEME_STYLES: Record<
  GuideColorTheme,
  {
    bg: string;
    hoverBg: string;
    border: string;
    hoverBorder: string;
    tagBorder: string;
    divider: string;
    accent: string;
  }
> = {
  lavender: {
    bg: 'bg-[#ECEFFE]',
    hoverBg: 'hover:bg-[#E2E7FC]',
    border: 'border-[#D7DCF5]',
    hoverBorder: 'hover:border-[#C6CEEE]',
    tagBorder: 'border-indigo-100',
    divider: 'border-indigo-200/60',
    accent: 'text-indigo-950',
  },
  sky: {
    bg: 'bg-[#E2F1F8]',
    hoverBg: 'hover:bg-[#D4EAF6]',
    border: 'border-[#CCE3EF]',
    hoverBorder: 'hover:border-[#B5D7E8]',
    tagBorder: 'border-sky-100',
    divider: 'border-sky-200/60',
    accent: 'text-sky-950',
  },
  sage: {
    bg: 'bg-[#E6F3EC]',
    hoverBg: 'hover:bg-[#D8EDE0]',
    border: 'border-[#CEE5D6]',
    hoverBorder: 'hover:border-[#B9DCC4]',
    tagBorder: 'border-emerald-100',
    divider: 'border-emerald-200/60',
    accent: 'text-emerald-950',
  },
  amber: {
    bg: 'bg-[#FAF3E7]',
    hoverBg: 'hover:bg-[#F5EBDA]',
    border: 'border-[#EFE1CC]',
    hoverBorder: 'hover:border-[#E4D2B6]',
    tagBorder: 'border-amber-100',
    divider: 'border-amber-200/60',
    accent: 'text-amber-950',
  },
  rose: {
    bg: 'bg-[#FDF0F3]',
    hoverBg: 'hover:bg-[#FCE4EC]',
    border: 'border-[#F7D8E1]',
    hoverBorder: 'hover:border-[#EFC4D1]',
    tagBorder: 'border-rose-100',
    divider: 'border-rose-200/60',
    accent: 'text-rose-950',
  },
};

const THEME_KEYS: GuideColorTheme[] = ['lavender', 'sky', 'sage', 'amber', 'rose'];

export function GuideCard({ guide, featured = false, index = 0 }: GuideCardProps) {
  const category = getBlogCategoryById(guide.category);
  const themeKey: GuideColorTheme = guide.colorTheme || THEME_KEYS[index % THEME_KEYS.length];
  const theme = THEME_STYLES[themeKey] || THEME_STYLES.lavender;

  return (
    <article
      className={`group relative flex flex-col justify-between rounded-2xl ${theme.bg} ${theme.hoverBg} border ${theme.border} ${theme.hoverBorder} shadow-2xs hover:shadow-md transition-all duration-300 overflow-hidden p-2 sm:p-2.5 ${
        featured ? 'md:grid md:grid-cols-12 md:gap-8 md:items-center' : ''
      }`}
    >
      <div className={featured ? 'md:col-span-6' : ''}>
        {/* Book Cover Thumbnail */}
        <Link
          href={`/guides/${guide.slug}/`}
          className="block aspect-[16/10] w-full overflow-hidden bg-white relative rounded-xl border border-white/80 shadow-2xs"
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

      <div className={`p-3.5 sm:p-4 flex flex-col justify-between flex-1 ${featured ? 'md:col-span-6 md:p-3' : ''}`}>
        <div>
          {/* Metadata Row */}
          <div className="flex flex-wrap items-center justify-between gap-2 text-[11px] font-mono mb-2">
            <span className="text-zinc-700 font-semibold px-2 py-0.5 rounded-md bg-white/85 border border-white/60 shadow-2xs">
              {category?.name || guide.category}
            </span>
            <div className="flex items-center gap-2 text-zinc-500">
              <span className="flex items-center gap-1">
                <Layers className="w-3 h-3" />
                <span>{guide.chapterCount} Chapters</span>
              </span>
              <span>&bull;</span>
              <span className="flex items-center gap-1">
                <Clock className="w-3 h-3" />
                <span>{guide.readTime}</span>
              </span>
            </div>
          </div>

          {/* Guide Title */}
          <h3 className="font-display text-base sm:text-[17px] font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors leading-snug tracking-tight line-clamp-2">
            <Link href={`/guides/${guide.slug}/`}>
              {guide.title}
            </Link>
          </h3>

          {/* Short Value Proposition */}
          <p className="mt-1.5 text-xs sm:text-sm text-zinc-600 line-clamp-2 leading-relaxed">
            {guide.dek || guide.excerpt}
          </p>

          {/* Resource Badges Row */}
          {guide.resourceBadges && guide.resourceBadges.length > 0 && (
            <div className="mt-3 flex flex-wrap items-center gap-1.5">
              {guide.resourceBadges.map((badge, idx) => (
                <span
                  key={idx}
                  className="px-2 py-0.5 rounded-md bg-white text-zinc-800 text-[10px] font-mono font-medium border border-white/80 shadow-2xs"
                >
                  {badge}
                </span>
              ))}
            </div>
          )}
        </div>

        {/* Read Action Footer */}
        <div className={`pt-3.5 mt-3.5 border-t ${theme.divider} flex items-center justify-between`}>
          <span className="text-xs font-bold text-zinc-950 inline-flex items-center gap-1.5 group-hover:translate-x-0.5 transition-transform">
            <span>Read Digital Book</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </span>
          <span className="text-[11px] font-mono text-zinc-600 font-medium">
            Free Resource
          </span>
        </div>
      </div>
    </article>
  );
}
