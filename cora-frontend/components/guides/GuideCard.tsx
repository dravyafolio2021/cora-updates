import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, BookOpen, Clock, Download, Layers } from 'lucide-react';
import { Guide } from '@/lib/guides-data';
import { getBlogCategoryById } from '@/lib/blog-data';

interface GuideCardProps {
  guide: Guide;
}

export function GuideCard({ guide }: GuideCardProps) {
  const category = getBlogCategoryById(guide.category);

  return (
    <article className="group flex flex-col justify-between rounded-3xl border border-zinc-200 bg-white overflow-hidden hover:border-zinc-400 transition-all shadow-sm">
      <div>
        {/* Cover Thumbnail */}
        {guide.coverImage && (
          <Link
            href={`/guides/${guide.slug}/`}
            className="block aspect-[16/9] w-full overflow-hidden bg-[#FBFaf7] relative border-b border-zinc-200"
          >
            <Image
              src={guide.coverImage}
              alt={guide.coverAlt || guide.title}
              fill
              sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
              className="object-cover group-hover:scale-[1.02] transition-transform duration-300"
            />
          </Link>
        )}

        <div className="p-6">
          {/* Header Metadata */}
          <div className="flex items-center justify-between gap-2 text-[10px] font-mono mb-3">
            <span className="font-bold text-zinc-500 uppercase tracking-wider">
              {category?.name || guide.category}
            </span>
            <div className="flex items-center gap-3 text-zinc-400">
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

          <h3 className="font-display text-lg sm:text-xl font-bold tracking-tight text-zinc-950 group-hover:text-zinc-700 transition-colors line-clamp-2">
            <Link href={`/guides/${guide.slug}/`}>
              {guide.title}
            </Link>
          </h3>

          <p className="mt-2.5 text-xs sm:text-sm text-zinc-600 line-clamp-2 leading-relaxed">
            {guide.dek || guide.excerpt}
          </p>

          {/* Download Asset Pill */}
          {guide.downloadableAsset && (
            <div className="mt-4 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#FBFaf7] border border-zinc-200/90 text-[11px] font-mono text-zinc-700">
              <Download className="w-3 h-3 text-zinc-500" />
              <span>Includes {guide.downloadableAsset.fileType.toUpperCase()} Download</span>
            </div>
          )}
        </div>
      </div>

      <div className="p-6 pt-0">
        <div className="pt-4 border-t border-zinc-100 flex items-center justify-between text-xs font-bold text-zinc-950">
          <Link
            href={`/guides/${guide.slug}/`}
            className="inline-flex items-center gap-1 hover:underline"
          >
            <span>Read complete playbook</span>
            <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
          </Link>
        </div>
      </div>
    </article>
  );
}
