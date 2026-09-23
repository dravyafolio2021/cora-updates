'use client';

import React, { useState } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import {
  AlertTriangle,
  ArrowRight,
  Check,
  HelpCircle,
  Info,
  Lightbulb,
  Sparkles,
  Zap,
} from 'lucide-react';
import type { EditorialBlock } from '@/lib/blog-data';
import { BlogInfographics } from './BlogInfographics';
import { BlogNewsletterBlock } from './BlogNewsletterBlock';

interface BlogBlockRendererProps {
  blocks: EditorialBlock[];
  articleSlug: string;
  category: string;
}

export function BlogBlockRenderer({ blocks, articleSlug, category }: BlogBlockRendererProps) {
  return (
    <div className="article-content space-y-6">
      {blocks.map((block, idx) => (
        <BlockItem key={idx} block={block} articleSlug={articleSlug} category={category} />
      ))}
    </div>
  );
}

function BlockItem({
  block,
  articleSlug,
  category,
}: {
  block: EditorialBlock;
  articleSlug: string;
  category: string;
}) {
  switch (block.type) {
    case 'intro':
      return (
        <div className="my-8 rounded-2xl border-l-4 border-zinc-900 bg-[#FBFaf7] p-5 sm:p-6 text-base sm:text-lg font-medium leading-relaxed text-zinc-800">
          {block.content}
        </div>
      );

    case 'text':
      return (
        <p className="text-base sm:text-[17px] leading-relaxed text-zinc-700 font-normal">
          {block.content}
        </p>
      );

    case 'heading': {
      const Tag = block.level === 2 ? 'h2' : block.level === 3 ? 'h3' : 'h4';
      const sizeClasses =
        block.level === 2
          ? 'font-display text-2xl sm:text-3xl font-bold tracking-tight text-zinc-950 mt-12 mb-4 scroll-mt-24'
          : block.level === 3
          ? 'font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 mt-8 mb-3 scroll-mt-24'
          : 'font-display text-lg sm:text-xl font-bold tracking-tight text-zinc-900 mt-6 mb-2 scroll-mt-24';

      return (
        <Tag id={block.id} className={`group flex items-center gap-2 ${sizeClasses}`}>
          <span>{block.text}</span>
          <a
            href={`#${block.id}`}
            aria-label={`Link to ${block.text}`}
            className="opacity-0 group-hover:opacity-100 text-zinc-400 hover:text-zinc-900 transition-opacity text-sm font-mono"
          >
            #
          </a>
        </Tag>
      );
    }

    case 'statement':
      return (
        <div className="my-10 py-6 border-y border-zinc-200 text-center">
          <blockquote className="font-display text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-zinc-950 leading-tight">
            &ldquo;{block.statement}&rdquo;
          </blockquote>
          {block.subtext && (
            <p className="mt-3 text-xs sm:text-sm text-zinc-600 max-w-xl mx-auto leading-relaxed">
              {block.subtext}
            </p>
          )}
        </div>
      );

    case 'quote':
      return (
        <figure className="my-8 pl-5 border-l-2 border-zinc-300">
          <blockquote className="text-base sm:text-lg italic text-zinc-800 leading-relaxed">
            &ldquo;{block.quote}&rdquo;
          </blockquote>
          <figcaption className="mt-2 text-xs font-mono text-zinc-500">
            &mdash; <span className="font-semibold text-zinc-700">{block.author}</span>
            {block.role && <span>, {block.role}</span>}
          </figcaption>
        </figure>
      );

    case 'list':
      if (block.ordered) {
        return (
          <ol className="my-5 space-y-2.5 pl-6 list-decimal text-base sm:text-[17px] text-zinc-700 leading-relaxed">
            {block.items.map((item, i) => (
              <li key={i} className="pl-1">
                {item}
              </li>
            ))}
          </ol>
        );
      }
      return (
        <ul className="my-5 space-y-2.5 pl-6 list-disc text-base sm:text-[17px] text-zinc-700 leading-relaxed">
          {block.items.map((item, i) => (
            <li key={i} className="pl-1">
              {item}
            </li>
          ))}
        </ul>
      );

    case 'keyTakeaway':
      return (
        <div className="my-8 rounded-2xl border border-zinc-900/10 bg-[#FBFaf7] p-5 sm:p-6 shadow-sm">
          <div className="flex items-center gap-2 text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-700 mb-1.5">
            <Zap className="w-3.5 h-3.5 text-amber-500" />
            <span>OPERATING PRINCIPLE // {block.principle}</span>
          </div>
          <p className="text-sm sm:text-base font-medium text-zinc-900 leading-relaxed">
            {block.description}
          </p>
        </div>
      );

    case 'callout': {
      const variants = {
        insight: {
          icon: Lightbulb,
          border: 'border-zinc-300',
          bg: 'bg-zinc-50',
          badge: 'INSIGHT',
          badgeColor: 'text-zinc-700',
        },
        warning: {
          icon: AlertTriangle,
          border: 'border-amber-200',
          bg: 'bg-amber-50/60',
          badge: 'WARNING',
          badgeColor: 'text-amber-700',
        },
        example: {
          icon: Info,
          border: 'border-zinc-300',
          bg: 'bg-[#FBFaf7]',
          badge: 'OPERATIONAL EXAMPLE',
          badgeColor: 'text-zinc-700',
        },
        note: {
          icon: HelpCircle,
          border: 'border-zinc-200',
          bg: 'bg-zinc-50',
          badge: 'NOTE',
          badgeColor: 'text-zinc-600',
        },
        'cora-tip': {
          icon: Sparkles,
          border: 'border-zinc-900',
          bg: 'bg-zinc-950 text-white',
          badge: 'CORA OPERATING TIP',
          badgeColor: 'text-emerald-400',
        },
      };

      const cfg = variants[block.variant] || variants.insight;
      const Icon = cfg.icon;

      return (
        <div className={`my-8 rounded-2xl border p-5 sm:p-6 shadow-sm ${cfg.border} ${cfg.bg}`}>
          <div className="flex items-center gap-2 mb-2">
            <Icon className="w-4 h-4 shrink-0 text-current opacity-80" />
            <span className={`text-[10px] font-mono font-bold uppercase tracking-wider ${cfg.badgeColor}`}>
              {cfg.badge}
            </span>
          </div>
          <h4
            className={`font-display text-base font-bold mb-1.5 ${
              block.variant === 'cora-tip' ? 'text-white' : 'text-zinc-950'
            }`}
          >
            {block.title}
          </h4>
          <p
            className={`text-xs sm:text-sm leading-relaxed ${
              block.variant === 'cora-tip' ? 'text-zinc-300' : 'text-zinc-600'
            }`}
          >
            {block.content}
          </p>
        </div>
      );
    }

    case 'stat':
      return (
        <div className="my-8 rounded-2xl border border-zinc-200 bg-[#FBFaf7] p-6 sm:p-8 text-center shadow-sm">
          <div className="font-display text-4xl sm:text-5xl font-bold tracking-tight text-zinc-950">
            {block.value}
          </div>
          <div className="mt-2 text-sm sm:text-base font-semibold text-zinc-800 max-w-lg mx-auto">
            {block.label}
          </div>
          {block.description && (
            <p className="mt-2 text-xs text-zinc-600 max-w-md mx-auto leading-relaxed">
              {block.description}
            </p>
          )}
          {block.source && (
            <div className="mt-4 pt-3 border-t border-zinc-200/80 text-[11px] font-mono text-zinc-600">
              Source: {block.source}
            </div>
          )}
        </div>
      );

    case 'comparison':
      return (
        <div className="my-8 rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
          {block.title && (
            <div className="px-5 py-3 border-b border-zinc-200 bg-zinc-50 font-semibold text-xs sm:text-sm text-zinc-900">
              {block.title}
            </div>
          )}
          <div className="grid sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-zinc-200">
            <div className="p-4 sm:p-5 bg-zinc-50/50">
              <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-red-600 mb-3">
                {block.leftHeader}
              </div>
              <div className="space-y-3">
                {block.rows.map((row, i) => (
                  <div key={i} className="text-xs sm:text-sm text-zinc-600 leading-normal">
                    {row.label && <span className="font-semibold text-zinc-900 block mb-0.5">{row.label}: </span>}
                    {row.left}
                  </div>
                ))}
              </div>
            </div>

            <div className="p-4 sm:p-5 bg-emerald-50/20">
              <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-600 mb-3">
                {block.rightHeader}
              </div>
              <div className="space-y-3">
                {block.rows.map((row, i) => (
                  <div key={i} className="text-xs sm:text-sm text-zinc-800 font-medium leading-normal">
                    {row.label && <span className="font-semibold text-zinc-950 block mb-0.5">{row.label}: </span>}
                    {row.right}
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      );

    case 'checklist':
      return <InteractiveChecklistBlock title={block.title} items={block.items} />;

    case 'steps':
      return (
        <div className="my-8 space-y-3">
          {block.steps.map((step, i) => (
            <div
              key={i}
              className="rounded-2xl border border-zinc-200 bg-[#FBFaf7] p-4 sm:p-5 flex items-start gap-4 shadow-sm"
            >
              <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-zinc-950 text-white font-mono text-xs font-bold">
                {step.number}
              </div>
              <div className="flex-1 min-w-0">
                <div className="flex flex-wrap items-center justify-between gap-2">
                  <h4 className="font-display text-base font-bold text-zinc-950">
                    {step.title}
                  </h4>
                  {step.badge && (
                    <span className="px-2 py-0.5 rounded-md bg-zinc-200 text-[10px] font-mono text-zinc-700 font-semibold">
                      {step.badge}
                    </span>
                  )}
                </div>
                <p className="mt-1 text-xs sm:text-sm text-zinc-600 leading-relaxed">
                  {step.description}
                </p>
              </div>
            </div>
          ))}
        </div>
      );

    case 'image':
      return (
        <figure
          className={`my-8 ${
            block.breakout ? 'sm:-mx-8 md:-mx-16 lg:-mx-24' : ''
          } rounded-2xl overflow-hidden border border-zinc-200 bg-zinc-100 shadow-sm`}
        >
          <div className="relative aspect-video w-full">
            <Image src={block.src} alt={block.alt} fill className="object-cover" sizes="(max-width: 768px) 100vw, 800px" />
          </div>
          {(block.caption || block.source) && (
            <figcaption className="p-3 text-center text-xs text-zinc-500 border-t border-zinc-200/80 bg-white">
              {block.caption} {block.source && <span className="font-mono">({block.source})</span>}
            </figcaption>
          )}
        </figure>
      );

    case 'infographic':
      return (
        <BlogInfographics
          infographicId={block.infographicId}
          headline={block.headline}
          explanation={block.explanation}
          source={block.source}
        />
      );

    case 'dataChart':
      return (
        <div className="my-8 rounded-2xl border border-zinc-200 bg-[#FBFaf7] p-5 sm:p-7 shadow-sm">
          <div className="mb-4">
            <h4 className="font-display text-base font-bold text-zinc-950">
              {block.title}
            </h4>
            {block.subtitle && (
              <p className="text-xs text-zinc-500 mt-0.5">{block.subtitle}</p>
            )}
          </div>

          <div className="space-y-3.5">
            {block.data.map((item, i) => {
              const numVal = typeof item.value === 'number' ? item.value : 50;
              return (
                <div key={i}>
                  <div className="flex items-center justify-between text-xs mb-1">
                    <span className="font-semibold text-zinc-900">{item.label}</span>
                    <span className="font-mono font-bold text-zinc-700">
                      {item.formattedValue || `${item.value}%`}
                    </span>
                  </div>
                  <div className="w-full h-2 rounded-full bg-zinc-200 overflow-hidden">
                    <div
                      className="h-full rounded-full bg-zinc-900 transition-all duration-500"
                      style={{ width: `${Math.min(100, Math.max(5, numVal))}%` }}
                    />
                  </div>
                  {item.sublabel && (
                    <div className="text-[11px] text-zinc-600 mt-1">{item.sublabel}</div>
                  )}
                </div>
              );
            })}
          </div>

          {block.source && (
            <div className="mt-4 pt-3 border-t border-zinc-200/80 text-[11px] font-mono text-zinc-600">
              Source: {block.source}
            </div>
          )}
        </div>
      );

    case 'table':
      return (
        <div className="my-8 overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm">
          {block.title && (
            <div className="px-5 py-3 border-b border-zinc-200 bg-zinc-50 text-xs font-semibold text-zinc-900">
              {block.title}
            </div>
          )}
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs sm:text-sm">
              <thead className="bg-zinc-50 border-b border-zinc-200 text-[11px] font-mono text-zinc-500 uppercase tracking-wider">
                <tr>
                  {block.headers.map((h, i) => (
                    <th key={i} className="px-4 py-3 font-semibold">
                      {h}
                    </th>
                  ))}
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-200/80 text-zinc-700">
                {block.rows.map((row, rIdx) => (
                  <tr key={rIdx} className="hover:bg-zinc-50/50">
                    {row.map((cell, cIdx) => (
                      <td key={cIdx} className="px-4 py-3">
                        {cell}
                      </td>
                    ))}
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          {block.caption && (
            <div className="px-4 py-2 border-t border-zinc-200/80 text-[11px] text-zinc-500 font-mono">
              {block.caption}
            </div>
          )}
        </div>
      );

    case 'contextualCTA':
      return (
        <div className="my-8 rounded-2xl border border-zinc-900 bg-zinc-950 text-white p-6 sm:p-7 shadow-lg">
          <div className="flex flex-wrap items-center justify-between gap-2 mb-2">
            <span className="px-2.5 py-0.5 rounded-full bg-white/10 text-[10px] font-mono font-bold tracking-wider text-zinc-300">
              {block.badge || 'CORA OPERATIONAL TOOL'}
            </span>
            <span className="text-[11px] font-mono text-zinc-500">Free Interactive Resource</span>
          </div>
          <h4 className="font-display text-lg sm:text-xl font-bold text-white mt-2">{block.title}</h4>
          <p className="mt-2 text-xs sm:text-sm text-zinc-400 leading-relaxed max-w-xl">{block.description}</p>
          <div className="mt-5">
            <Link
              href={block.ctaHref}
              className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-zinc-100 text-zinc-950 text-xs sm:text-sm font-bold transition-all shadow-sm"
            >
              <span>{block.ctaText}</span>
              <ArrowRight className="w-4 h-4" />
            </Link>
          </div>
        </div>
      );

    case 'productMention':
      return (
        <div className="my-6 p-4 rounded-xl border border-zinc-200 bg-[#FBFaf7] text-xs sm:text-sm leading-relaxed text-zinc-600 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <span>{block.contextText}</span>
          <Link
            href={block.actionHref}
            className="inline-flex items-center gap-1.5 font-bold text-zinc-950 hover:underline shrink-0 text-xs"
          >
            <span>{block.actionText}</span>
          </Link>
        </div>
      );

    case 'newsletter':
      return (
        <BlogNewsletterBlock
          heading={block.heading}
          tagline={block.tagline}
          buttonText={block.buttonText}
          articleSlug={articleSlug}
          category={category}
          placement={block.placement}
        />
      );

    case 'leadMagnetCTA':
      return (
        <div className="my-8 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-6 sm:p-7 text-center">
          <span className="px-2.5 py-0.5 rounded-full bg-zinc-200 text-[10px] font-mono font-bold tracking-wider text-zinc-700 uppercase">
            EDITORIAL RESOURCE PACK
          </span>
          <h4 className="mt-3 font-display text-lg sm:text-xl font-bold text-zinc-950">
            {block.title}
          </h4>
          <p className="mt-2 text-xs sm:text-sm text-zinc-600 max-w-lg mx-auto leading-relaxed">
            {block.description}
          </p>
          <div className="mt-5">
            <button
              type="button"
              className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs sm:text-sm font-bold transition-all shadow-sm cursor-pointer"
            >
              <span>{block.ctaText}</span>
              <ArrowRight className="w-4 h-4" />
            </button>
          </div>
        </div>
      );

    default:
      return null;
  }
}

function InteractiveChecklistBlock({
  title,
  items,
}: {
  title?: string;
  items: { label: string; description?: string; checked?: boolean }[];
}) {
  const [checkedState, setCheckedState] = useState<boolean[]>(
    items.map((it) => it.checked ?? true)
  );

  const toggle = (index: number) => {
    setCheckedState((prev) => {
      const next = [...prev];
      next[index] = !next[index];
      return next;
    });
  };

  return (
    <div className="my-8 rounded-2xl border border-zinc-200 bg-white shadow-sm p-5 sm:p-6">
      {title && (
        <div className="mb-4 text-xs sm:text-sm font-bold text-zinc-950 flex items-center justify-between">
          <span>{title}</span>
          <span className="text-[10px] font-mono text-zinc-400">Interactive Checklist</span>
        </div>
      )}
      <div className="space-y-3">
        {items.map((item, i) => {
          const isChecked = checkedState[i];
          return (
            <div
              key={i}
              onClick={() => toggle(i)}
              className="flex items-start gap-3 p-2.5 rounded-xl transition-colors hover:bg-zinc-50 cursor-pointer select-none"
            >
              <div
                className={`mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-md border transition-all ${
                  isChecked
                    ? 'border-emerald-600 bg-emerald-600 text-white'
                    : 'border-zinc-300 bg-white'
                }`}
              >
                {isChecked && <Check className="h-3.5 w-3.5 stroke-[3]" />}
              </div>
              <div className="flex-1 text-xs sm:text-sm">
                <div
                  className={`font-semibold transition-colors ${
                    isChecked ? 'text-zinc-900' : 'text-zinc-500 line-through'
                  }`}
                >
                  {item.label}
                </div>
                {item.description && (
                  <p className="mt-0.5 text-[11px] sm:text-xs text-zinc-500">
                    {item.description}
                  </p>
                )}
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
