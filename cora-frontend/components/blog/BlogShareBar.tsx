'use client';

import React, { useState } from 'react';
import { Share2, Check, Copy, MessageCircle } from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

interface BlogShareBarProps {
  title: string;
  url: string;
  articleSlug: string;
}

export function BlogShareBar({ title, url, articleSlug }: BlogShareBarProps) {
  const [copied, setCopied] = useState(false);

  const encodedUrl = encodeURIComponent(url);
  const encodedTitle = encodeURIComponent(`${title} — via Cora`);

  const shareWhatsApp = () => {
    trackEvent('article_share', { platform: 'whatsapp', article_slug: articleSlug });
    window.open(`https://api.whatsapp.com/send?text=${encodedTitle}%20${encodedUrl}`, '_blank');
  };

  const shareLinkedIn = () => {
    trackEvent('article_share', { platform: 'linkedin', article_slug: articleSlug });
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`, '_blank');
  };

  const shareTwitter = () => {
    trackEvent('article_share', { platform: 'twitter', article_slug: articleSlug });
    window.open(`https://twitter.com/intent/tweet?text=${encodedTitle}&url=${encodedUrl}`, '_blank');
  };

  const copyToClipboard = () => {
    trackEvent('article_share', { platform: 'copy_link', article_slug: articleSlug });
    navigator.clipboard.writeText(url);
    setCopied(true);
    setTimeout(() => setCopied(false), 2500);
  };

  return (
    <div className="flex flex-wrap items-center gap-2 py-4 text-xs">
      <span className="text-[11px] font-mono text-zinc-600 dark:text-zinc-400 uppercase font-semibold mr-1">
        SHARE:
      </span>

      <button
        onClick={shareWhatsApp}
        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer font-medium"
      >
        <MessageCircle className="w-3.5 h-3.5 text-emerald-500" />
        <span>WhatsApp</span>
      </button>

      <button
        onClick={shareLinkedIn}
        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all cursor-pointer font-medium"
      >
        <span>LinkedIn</span>
      </button>

      <button
        onClick={shareTwitter}
        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:border-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-all cursor-pointer font-medium"
      >
        <span>X</span>
      </button>

      <button
        onClick={copyToClipboard}
        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer font-medium"
      >
        {copied ? (
          <>
            <Check className="w-3.5 h-3.5 text-emerald-500" />
            <span className="text-emerald-600 dark:text-emerald-400 font-semibold">Copied!</span>
          </>
        ) : (
          <>
            <Copy className="w-3.5 h-3.5 text-zinc-400" />
            <span>Copy Link</span>
          </>
        )}
      </button>
    </div>
  );
}
