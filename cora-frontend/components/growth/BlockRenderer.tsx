'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  Check, 
  ChevronRight, 
  ArrowRight, 
  Download, 
  FileText, 
  Info, 
  AlertTriangle, 
  Sparkles, 
  Lightbulb, 
  TrendingUp,
  ExternalLink,
  Code2
} from 'lucide-react';
import { ContentBlock } from '@/lib/content-api';

interface BlockRendererProps {
  blocks: ContentBlock[];
}

export function BlockRenderer({ blocks }: BlockRendererProps) {
  if (!blocks || !Array.isArray(blocks) || blocks.length === 0) {
    return null;
  }

  return (
    <div className="space-y-8 editorial-content">
      {blocks.map((block, index) => (
        <SingleBlockRenderer key={block.id || `block-${index}`} block={block} />
      ))}
    </div>
  );
}

export function SingleBlockRenderer({ block }: { block: ContentBlock | any }) {
  const type = block.type;
  // Support both new Growth API block schema ({ id, type, version, data: {...} }) and flat legacy blocks
  const data = block.data || block;

  switch (type) {
    case 'intro':
      return (
        <p className="text-xl sm:text-2xl text-zinc-900 dark:text-zinc-100 font-normal leading-relaxed tracking-tight border-l-2 border-zinc-900 dark:border-zinc-100 pl-4 py-1">
          {data.content || data.text}
        </p>
      );

    case 'rich_text':
    case 'text':
      return (
        <div 
          className="text-[17px] text-zinc-700 dark:text-zinc-300 leading-relaxed space-y-4 font-normal [&_strong]:text-zinc-900 [&_strong]:dark:text-zinc-100 [&_strong]:font-semibold [&_a]:text-zinc-900 [&_a]:dark:text-zinc-100 [&_a]:underline [&_a]:underline-offset-4 [&_code]:font-mono [&_code]:text-sm [&_code]:bg-zinc-100 [&_code]:dark:bg-zinc-800 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:rounded-md"
          dangerouslySetInnerHTML={{ __html: data.content || data.text || '' }}
        />
      );

    case 'heading': {
      const level = data.level || 2;
      const text = data.text || data.title || '';
      const id = data.id || text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');

      if (level === 3) {
        return (
          <h3 id={id} className="text-2xl font-semibold text-zinc-900 dark:text-zinc-100 tracking-tight pt-6 scroll-mt-24">
            {text}
          </h3>
        );
      }
      if (level === 4) {
        return (
          <h4 id={id} className="text-xl font-medium text-zinc-900 dark:text-zinc-100 tracking-tight pt-4 scroll-mt-24">
            {text}
          </h4>
        );
      }
      return (
        <h2 id={id} className="text-3xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight pt-8 pb-1 scroll-mt-24 border-t border-zinc-200 dark:border-zinc-800 first:border-0 first:pt-0">
          {text}
        </h2>
      );
    }

    case 'statement':
      return (
        <div className="my-8 py-6 px-8 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center">
          <p className="text-2xl sm:text-3xl font-semibold text-zinc-900 dark:text-zinc-100 tracking-tight leading-snug">
            &ldquo;{data.statement || data.content}&rdquo;
          </p>
          {data.subtext && (
            <p className="text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-mono">
              {data.subtext}
            </p>
          )}
        </div>
      );

    case 'quote':
      return (
        <blockquote className="my-6 pl-6 border-l-2 border-zinc-300 dark:border-zinc-700 italic text-lg text-zinc-800 dark:text-zinc-200">
          <p>&ldquo;{data.quote || data.content}&rdquo;</p>
          {(data.author || data.role) && (
            <footer className="not-italic text-sm text-zinc-500 dark:text-zinc-400 mt-2">
              — <strong className="text-zinc-900 dark:text-zinc-100 font-semibold">{data.author}</strong>
              {data.role && <span>, {data.role}</span>}
            </footer>
          )}
        </blockquote>
      );

    case 'list': {
      const items: string[] = data.items || [];
      if (data.ordered) {
        return (
          <ol className="list-decimal list-inside space-y-2 text-[17px] text-zinc-700 dark:text-zinc-300">
            {items.map((it, idx) => (
              <li key={idx} dangerouslySetInnerHTML={{ __html: it }} />
            ))}
          </ol>
        );
      }
      return (
        <ul className="space-y-2 text-[17px] text-zinc-700 dark:text-zinc-300">
          {items.map((it, idx) => (
            <li key={idx} className="flex items-start gap-3">
              <span className="w-1.5 h-1.5 rounded-full bg-zinc-400 dark:bg-zinc-600 mt-2.5 flex-shrink-0" />
              <span dangerouslySetInnerHTML={{ __html: it }} />
            </li>
          ))}
        </ul>
      );
    }

    case 'checklist': {
      const items = data.items || [];
      return (
        <div className="p-6 rounded-2xl bg-zinc-50/80 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 space-y-3">
          {data.title && (
            <h4 className="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
              <Check className="w-4 h-4 text-zinc-900 dark:text-zinc-100" />
              {data.title}
            </h4>
          )}
          <div className="space-y-2.5">
            {items.map((item: any, idx: number) => {
              const label = typeof item === 'string' ? item : item.label;
              const desc = typeof item === 'object' ? item.description : null;
              return (
                <div key={idx} className="flex items-start gap-3 p-3 rounded-xl bg-white dark:bg-zinc-950 border border-zinc-200/70 dark:border-zinc-800">
                  <div className="w-5 h-5 rounded-md bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <Check className="w-3.5 h-3.5" />
                  </div>
                  <div>
                    <p className="text-sm font-medium text-zinc-900 dark:text-zinc-100">{label}</p>
                    {desc && <p className="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{desc}</p>}
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      );
    }

    case 'key_takeaway':
    case 'keyTakeaway':
      return (
        <div className="p-6 rounded-2xl bg-zinc-100/90 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-800">
          <div className="flex items-center gap-2 mb-2">
            <Sparkles className="w-4 h-4 text-zinc-900 dark:text-zinc-100" />
            <span className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
              Key Takeaway
            </span>
          </div>
          <h4 className="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-1">
            {data.principle || data.title}
          </h4>
          <p className="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
            {data.description || data.content}
          </p>
        </div>
      );

    case 'callout': {
      const variant = data.variant || 'insight';
      return (
        <div className="p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
          <div className="flex items-start gap-3">
            <div className="w-7 h-7 rounded-lg bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0 text-zinc-900 dark:text-zinc-100">
              {variant === 'warning' ? <AlertTriangle className="w-4 h-4" /> : <Info className="w-4 h-4" />}
            </div>
            <div className="space-y-1">
              <h5 className="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{data.title}</h5>
              <div 
                className="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed"
                dangerouslySetInnerHTML={{ __html: data.content || '' }}
              />
            </div>
          </div>
        </div>
      );
    }

    case 'stat':
      return (
        <div className="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div className="text-4xl sm:text-5xl font-bold font-mono tracking-tight text-zinc-900 dark:text-zinc-100">
              {data.value}
            </div>
            <div className="text-base font-medium text-zinc-900 dark:text-zinc-100 mt-1">
              {data.label}
            </div>
            {data.description && (
              <p className="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{data.description}</p>
            )}
          </div>
          {data.source && (
            <div className="text-xs text-zinc-400 font-mono self-end sm:self-center">
              Source: {data.source}
            </div>
          )}
        </div>
      );

    case 'comparison': {
      const rows = data.rows || [];
      return (
        <div className="my-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
          {data.title && (
            <div className="px-6 py-4 bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 text-sm font-semibold text-zinc-900 dark:text-zinc-100">
              {data.title}
            </div>
          )}
          <div className="grid grid-cols-2 bg-zinc-100/60 dark:bg-zinc-800/60 text-xs font-mono uppercase tracking-wider text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
            <div className="px-4 py-3 border-r border-zinc-200 dark:border-zinc-800">{data.leftHeader || 'The Old Way'}</div>
            <div className="px-4 py-3 text-zinc-900 dark:text-zinc-100 font-semibold">{data.rightHeader || 'The Cora Way'}</div>
          </div>
          <div className="divide-y divide-zinc-200 dark:divide-zinc-800 bg-white dark:bg-zinc-950 text-sm">
            {rows.map((r: any, idx: number) => (
              <div key={idx} className="grid grid-cols-2">
                <div className="p-4 text-zinc-600 dark:text-zinc-400 border-r border-zinc-200 dark:border-zinc-800">
                  {r.left}
                </div>
                <div className="p-4 text-zinc-900 dark:text-zinc-100 font-medium">
                  {r.right}
                </div>
              </div>
            ))}
          </div>
        </div>
      );
    }

    case 'steps': {
      const steps = data.steps || [];
      return (
        <div className="space-y-4 my-6">
          {steps.map((st: any, idx: number) => (
            <div key={idx} className="flex gap-4 p-5 rounded-2xl bg-zinc-50/70 dark:bg-zinc-900/70 border border-zinc-200 dark:border-zinc-800">
              <div className="w-8 h-8 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center font-mono font-bold text-sm flex-shrink-0">
                {st.number || idx + 1}
              </div>
              <div className="space-y-1">
                <h5 className="text-base font-semibold text-zinc-900 dark:text-zinc-100">{st.title}</h5>
                <p className="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{st.description}</p>
              </div>
            </div>
          ))}
        </div>
      );
    }

    case 'image':
      return (
        <figure className="my-6 space-y-2">
          <div className="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-900 relative aspect-video">
            <Image
              src={data.src || data.file_url}
              alt={data.alt || data.alt_text || 'Editorial asset'}
              fill
              className="object-cover"
            />
          </div>
          {data.caption && (
            <figcaption className="text-xs text-center text-zinc-500 dark:text-zinc-400">
              {data.caption}
            </figcaption>
          )}
        </figure>
      );

    case 'infographic':
      return (
        <div className="my-8 p-6 rounded-2xl bg-zinc-900 text-zinc-100 border border-zinc-800 space-y-4">
          <div className="flex items-center justify-between border-b border-zinc-800 pb-3">
            <span className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-400">
              Process Diagram
            </span>
            <span className="text-xs font-mono text-zinc-500">Cora Systems Framework</span>
          </div>
          <h4 className="text-xl font-semibold tracking-tight">{data.headline || data.title}</h4>
          {data.src ? (
            <div className="relative aspect-video rounded-xl overflow-hidden border border-zinc-800">
              <Image src={data.src} alt={data.headline || 'Infographic'} fill className="object-contain" />
            </div>
          ) : (
            <p className="text-sm text-zinc-400 leading-relaxed">{data.explanation}</p>
          )}
        </div>
      );

    case 'contextual_cta':
    case 'product_mention':
      return (
        <div className="my-8 p-8 rounded-2xl bg-zinc-950 text-white border border-zinc-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
          <div className="space-y-1 max-w-md">
            <span className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-400">
              Platform Solution
            </span>
            <h4 className="text-xl font-bold tracking-tight">{data.title || 'Stop managing ops across 12 disjointed apps'}</h4>
            <p className="text-sm text-zinc-400">{data.description || 'Cora consolidates scoping, e-sign contracts, milestone payments, and team scheduling in one interface.'}</p>
          </div>
          <Link
            href={data.buttonUrl || '/features/ai-cofounder'}
            className="px-5 py-2.5 rounded-xl bg-white text-zinc-900 font-semibold text-sm hover:bg-zinc-100 transition-colors flex items-center gap-2 flex-shrink-0"
          >
            {data.buttonText || 'Explore Features'}
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      );

    case 'lead_magnet_cta':
    case 'download':
      return (
        <div className="my-8 p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-4">
          <div className="flex items-center gap-4">
            <div className="w-12 h-12 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center flex-shrink-0">
              <FileText className="w-6 h-6" />
            </div>
            <div>
              <h5 className="text-base font-semibold text-zinc-900 dark:text-zinc-100">{data.title || 'Download Resource Asset'}</h5>
              <p className="text-xs text-zinc-500 dark:text-zinc-400">{data.fileType || 'PDF Playbook'} {data.fileSize ? `• ${data.fileSize}` : ''}</p>
            </div>
          </div>
          <Link
            href={data.downloadUrl || data.buttonUrl || '#download'}
            className="px-4 py-2 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 text-sm font-semibold hover:opacity-90 flex items-center gap-2 flex-shrink-0"
          >
            <Download className="w-4 h-4" />
            <span>{data.buttonText || 'Download Free'}</span>
          </Link>
        </div>
      );

    case 'faq': {
      const items = data.items || [];
      return (
        <div className="my-8 space-y-4">
          <h4 className="text-xl font-semibold text-zinc-900 dark:text-zinc-100">Frequently Asked Questions</h4>
          <div className="divide-y divide-zinc-200 dark:divide-zinc-800 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden bg-white dark:bg-zinc-900">
            {items.map((it: any, idx: number) => (
              <details key={idx} className="group p-5 text-sm cursor-pointer">
                <summary className="font-semibold text-zinc-900 dark:text-zinc-100 list-none flex items-center justify-between">
                  <span>{it.question}</span>
                  <ChevronRight className="w-4 h-4 transition-transform group-open:rotate-90 text-zinc-400" />
                </summary>
                <div className="mt-3 text-zinc-600 dark:text-zinc-400 leading-relaxed" dangerouslySetInnerHTML={{ __html: it.answer }} />
              </details>
            ))}
          </div>
        </div>
      );
    }

    case 'code':
      return (
        <div className="my-6 rounded-2xl bg-zinc-950 border border-zinc-800 overflow-hidden text-sm font-mono">
          {data.language && (
            <div className="px-4 py-2 bg-zinc-900 border-b border-zinc-800 text-xs text-zinc-400 flex items-center justify-between">
              <span>{data.language}</span>
              <Code2 className="w-3.5 h-3.5" />
            </div>
          )}
          <pre className="p-4 text-zinc-200 overflow-x-auto">
            <code>{data.code || data.content}</code>
          </pre>
        </div>
      );

    case 'divider':
      return <hr className="my-10 border-zinc-200 dark:border-zinc-800" />;

    default:
      return null;
  }
}
