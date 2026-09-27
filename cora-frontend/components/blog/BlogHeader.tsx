import React from 'react';
import Link from 'next/link';
import { Sparkles, ChevronRight, CheckCircle2, Zap } from 'lucide-react';

interface BlogHeaderProps {
  title?: string;
  description?: string;
  badge?: string;
}

export function BlogHeader({
  title = 'Practical Operating Answers for Growing Agencies.',
  description = 'Focused editorial answers to client management, scope creep defence, margin protection, and autonomous workflows.',
  badge = 'CORA EDITORIAL &bull; PLAYBOOKS &amp; SYSTEMS',
}: BlogHeaderProps) {
  return (
    <section className="pt-20 sm:pt-24 pb-6 sm:pb-8 border-b border-zinc-200/80 bg-[#FBFaf7]">
      <div className="mx-auto max-w-[1240px] px-4 sm:px-6">
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-6">
          <div className="max-w-2xl space-y-2.5">
            {/* Breadcrumb / Top Tag */}
            <nav aria-label="Breadcrumb" className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border border-zinc-200/90 bg-white text-[11px] font-mono text-zinc-600 shadow-2xs">
              <Link href="/" className="hover:text-zinc-950 transition-colors">
                Cora
              </Link>
              <ChevronRight className="w-3 h-3 text-zinc-400" />
              <span className="text-zinc-950 font-bold">Editorial Articles</span>
            </nav>

            {/* Compact Heading */}
            <h1 className="font-display text-2xl xs:text-3xl sm:text-4xl font-bold tracking-tight text-zinc-950 leading-[1.18]">
              {title}
            </h1>

            {/* Short Dek */}
            <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
              {description}
            </p>
          </div>

          {/* Quick value trust tags */}
          <div className="shrink-0 flex flex-wrap md:flex-col items-start md:items-end gap-1.5 text-[11px] font-mono text-zinc-600">
            <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-zinc-200 shadow-2xs">
              <Sparkles className="w-3 h-3 text-amber-500 fill-amber-500" />
              <span>45s Direct Answers</span>
            </span>
            <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-zinc-200 shadow-2xs">
              <Zap className="w-3 h-3 text-zinc-900" />
              <span>Zero Fluff &bull; No Paywall</span>
            </span>
          </div>
        </div>
      </div>
    </section>
  );
}

