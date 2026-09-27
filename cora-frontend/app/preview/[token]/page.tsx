import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import Link from 'next/link';
import Image from 'next/image';
import { ShieldAlert, ArrowLeft, Clock, Calendar, Sparkles } from 'lucide-react';
import { fetchPreviewContent } from '@/lib/content-api';
import { BlockRenderer } from '@/components/growth/BlockRenderer';

interface PreviewPageProps {
  params: Promise<{
    token: string;
  }>;
}

export const dynamicParams = false;

export async function generateStaticParams() {
  return [{ token: 'sample-preview' }];
}

export const metadata: Metadata = {
  title: 'Content Preview — Cora Growth Studio',
  robots: {
    index: false,
    follow: false,
  },
};

export default async function PreviewPage({ params }: PreviewPageProps) {
  const { token } = await params;
  const entry = await fetchPreviewContent(token);

  if (!entry) {
    return (
      <div className="min-h-screen flex items-center justify-center p-6 bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
        <div className="max-w-md w-full p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center space-y-4 shadow-xl">
          <div className="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mx-auto text-zinc-900 dark:text-zinc-100">
            <ShieldAlert className="w-6 h-6" />
          </div>
          <h1 className="text-xl font-bold tracking-tight">Preview Link Expired or Invalid</h1>
          <p className="text-sm text-zinc-500 dark:text-zinc-400">
            This preview token is invalid or has reached its 24-hour expiration window. Please generate a fresh preview link in the Cora Growth Content Studio.
          </p>
          <Link
            href="/"
            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 text-sm font-semibold hover:opacity-90"
          >
            <ArrowLeft className="w-4 h-4" />
            Back to Home
          </Link>
        </div>
      </div>
    );
  }

  const isGuide = entry.type === 'guide';

  return (
    <div className="min-h-screen bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
      {/* Sticky Preview Mode Top Banner */}
      <div className="sticky top-0 z-50 bg-zinc-900 text-zinc-100 border-b border-zinc-800 px-4 py-3 shadow-md">
        <div className="max-w-5xl mx-auto flex items-center justify-between gap-4 text-xs font-mono">
          <div className="flex items-center gap-2.5">
            <span className="w-2 h-2 rounded-full bg-amber-400 animate-pulse flex-shrink-0" />
            <span className="font-semibold uppercase tracking-wider text-amber-300">
              Live Preview Mode
            </span>
            <span className="text-zinc-400 hidden sm:inline">•</span>
            <span className="text-zinc-400 hidden sm:inline">Status: {entry.status.toUpperCase()}</span>
            <span className="text-zinc-400 hidden md:inline">• ID: {entry.id}</span>
          </div>
          <div className="flex items-center gap-2 text-zinc-400">
            <span className="bg-zinc-800 px-2 py-0.5 rounded border border-zinc-700">noindex, nofollow</span>
          </div>
        </div>
      </div>

      {/* Main Content Container */}
      <main className="max-w-4xl mx-auto px-6 py-12 sm:py-16">
        {/* Header Metadata */}
        <header className="space-y-6 pb-10 border-b border-zinc-200 dark:border-zinc-800">
          <div className="flex flex-wrap items-center gap-3">
            <span className="px-3 py-1 rounded-full text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-700 uppercase tracking-wider">
              {entry.type}
            </span>
            {entry.primary_keyword && (
              <span className="px-3 py-1 rounded-full text-xs font-mono text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                🎯 {entry.primary_keyword}
              </span>
            )}
            <span className="flex items-center gap-1.5 text-xs text-zinc-500 font-mono ml-auto">
              <Clock className="w-3.5 h-3.5" />
              {entry.read_time}
            </span>
          </div>

          <h1 className="text-3xl sm:text-5xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100 leading-tight">
            {entry.title}
          </h1>

          {entry.excerpt && (
            <p className="text-lg sm:text-xl text-zinc-600 dark:text-zinc-400 leading-relaxed font-normal">
              {entry.excerpt}
            </p>
          )}

          {/* Author info */}
          <div className="flex items-center gap-3 pt-2">
            {entry.author?.avatar && (
              <Image
                src={entry.author.avatar}
                alt={entry.author.name}
                width={40}
                height={40}
                className="rounded-full border border-zinc-200 dark:border-zinc-800 object-cover"
              />
            )}
            <div>
              <p className="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{entry.author?.name || 'Studio Admin'}</p>
              <p className="text-xs text-zinc-500">{entry.author?.role || 'Cora Editorial Team'}</p>
            </div>
          </div>
        </header>

        {/* Content Body */}
        <div className="py-10">
          {isGuide && entry.chapters && entry.chapters.length > 0 ? (
            <div className="space-y-16">
              {entry.chapters.map((chapter, cIdx) => (
                <section key={chapter.id || chapter.slug || cIdx} id={chapter.slug} className="space-y-6 pt-8 border-t border-zinc-200 dark:border-zinc-800 first:border-0 first:pt-0">
                  <div className="flex items-center gap-3">
                    <span className="w-8 h-8 rounded-lg bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center font-mono font-bold text-xs">
                      {chapter.number}
                    </span>
                    <h2 className="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                      {chapter.title}
                    </h2>
                  </div>
                  {chapter.summary && (
                    <p className="text-base text-zinc-600 dark:text-zinc-400 leading-relaxed italic">
                      {chapter.summary}
                    </p>
                  )}
                  <BlockRenderer blocks={chapter.blocks} />
                </section>
              ))}
            </div>
          ) : (
            <BlockRenderer blocks={entry.content} />
          )}
        </div>

        {/* Sources Section if present */}
        {entry.sources && entry.sources.length > 0 && (
          <footer className="mt-16 pt-8 border-t border-zinc-200 dark:border-zinc-800 space-y-3">
            <h4 className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-500">
              Primary Sources & Research Citations
            </h4>
            <ul className="space-y-1.5 text-xs text-zinc-600 dark:text-zinc-400">
              {entry.sources.map((src, idx) => (
                <li key={idx} className="flex items-center gap-2">
                  <span>[{idx + 1}]</span>
                  <a href={src.url} target="_blank" rel="noopener noreferrer" className="underline hover:text-zinc-900 dark:hover:text-zinc-100">
                    {src.title} — {src.publisher}
                  </a>
                </li>
              ))}
            </ul>
          </footer>
        )}
      </main>
    </div>
  );
}
