'use client';

import React, { useState } from 'react';
import { Check, Copy, MessageCircle } from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

export interface BlogShareBarProps {
  title: string;
  url: string;
  articleSlug: string;
  shareTitle?: string;
  shareDescription?: string;
  shareText?: string;
}

export function BlogShareBar({
  title,
  url,
  articleSlug,
  shareTitle,
  shareDescription,
  shareText,
}: BlogShareBarProps) {
  const [copied, setCopied] = useState(false);

  const effectiveTitle = shareTitle || title;
  const effectiveDescription = shareDescription || '';

  const shareWhatsApp = () => {
    trackEvent('article_share', { platform: 'whatsapp', article_slug: articleSlug });
    // WhatsApp format:
    // {shareTitle}\n\n{shareDescription}\n\n{articleUrl}
    const messageParts = [effectiveTitle, effectiveDescription, url].filter(Boolean);
    const waPayload = messageParts.join('\n\n');
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(waPayload)}`, '_blank');
  };

  const shareLinkedIn = () => {
    trackEvent('article_share', { platform: 'linkedin', article_slug: articleSlug });
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`, '_blank');
  };

  const shareTwitter = () => {
    trackEvent('article_share', { platform: 'twitter', article_slug: articleSlug });
    // X (Twitter) format:
    // {shareText OR shareTitle}\n{articleUrl}
    const primaryText = shareText || effectiveTitle;
    const tweetText = `${primaryText}\n${url}`;
    window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(tweetText)}`, '_blank');
  };

  const copyToClipboard = () => {
    trackEvent('article_share', { platform: 'copy_link', article_slug: articleSlug });
    navigator.clipboard.writeText(url);
    setCopied(true);
    setTimeout(() => setCopied(false), 2500);
  };

  return (
    <div className="flex flex-wrap items-center gap-2 py-4 text-xs">
      <span className="text-[11px] font-mono text-zinc-600 uppercase font-semibold mr-1">
        SHARE:
      </span>

      <button
        onClick={shareWhatsApp}
        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 hover:border-emerald-500 hover:text-emerald-600 transition-all cursor-pointer font-medium"
      >
        <MessageCircle className="w-3.5 h-3.5 text-emerald-500" />
        <span>WhatsApp</span>
      </button>

      <button
        onClick={shareLinkedIn}
        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 hover:border-blue-500 hover:text-blue-600 transition-all cursor-pointer font-medium"
      >
        <span>LinkedIn</span>
      </button>

      <button
        onClick={shareTwitter}
        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 hover:border-zinc-500 hover:text-zinc-900 transition-all cursor-pointer font-medium"
      >
        <span>X</span>
      </button>

      <button
        onClick={copyToClipboard}
        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 transition-all cursor-pointer font-medium"
      >
        {copied ? (
          <>
            <Check className="w-3.5 h-3.5 text-emerald-500" />
            <span className="text-emerald-600 font-semibold">Copied!</span>
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
