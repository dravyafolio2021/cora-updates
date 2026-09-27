'use client';

import React, { useState } from 'react';
import { Check, Copy, MessageCircle, Share2 } from 'lucide-react';
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

  const buildShareUrl = (source: string) => {
    const cleanUrl = url.split('?')[0];
    return `${cleanUrl}?utm_source=${source}&utm_medium=editorial_share&utm_campaign=cora_blog_${articleSlug}`;
  };

  const shareNative = async () => {
    const shareUrl = buildShareUrl('webshare');
    if (typeof navigator !== 'undefined' && navigator.share) {
      try {
        await navigator.share({
          title: effectiveTitle,
          text: effectiveDescription,
          url: shareUrl,
        });
        trackEvent('article_share', { platform: 'webshare', article_slug: articleSlug });
        return;
      } catch (e) {
        // Fall back to copy
      }
    }
    copyToClipboard();
  };

  const shareWhatsApp = () => {
    const shareUrl = buildShareUrl('whatsapp');
    trackEvent('article_share', { platform: 'whatsapp', article_slug: articleSlug });
    const messageParts = [effectiveTitle, effectiveDescription, shareUrl].filter(Boolean);
    const waPayload = messageParts.join('\n\n');
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(waPayload)}`, '_blank');
  };

  const shareLinkedIn = () => {
    const shareUrl = buildShareUrl('linkedin');
    trackEvent('article_share', { platform: 'linkedin', article_slug: articleSlug });
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl)}`, '_blank');
  };

  const shareTwitter = () => {
    const shareUrl = buildShareUrl('twitter');
    trackEvent('article_share', { platform: 'twitter', article_slug: articleSlug });
    const primaryText = shareText || effectiveTitle;
    const tweetText = `${primaryText}\n\n${shareUrl}`;
    window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(tweetText)}`, '_blank');
  };

  const copyToClipboard = () => {
    const shareUrl = buildShareUrl('copy_link');
    trackEvent('article_share', { platform: 'copy_link', article_slug: articleSlug });
    navigator.clipboard.writeText(shareUrl);
    setCopied(true);
    setTimeout(() => setCopied(false), 2500);
  };

  return (
    <div className="flex flex-wrap items-center justify-between gap-3 py-4 text-xs">
      <div className="flex flex-wrap items-center gap-2">
        <span className="text-[11px] font-mono text-zinc-600 uppercase font-semibold mr-1 flex items-center gap-1.5">
          <Share2 className="w-3.5 h-3.5 text-zinc-600" />
          <span>SHARE:</span>
        </span>

        <button
          type="button"
          onClick={shareWhatsApp}
          className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200/80 bg-white text-zinc-700 hover:border-emerald-500 hover:text-emerald-700 hover:bg-emerald-50/40 transition-all cursor-pointer font-medium shadow-2xs"
        >
          <MessageCircle className="w-3.5 h-3.5 text-emerald-600" />
          <span>WhatsApp</span>
        </button>

        <button
          type="button"
          onClick={shareLinkedIn}
          className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200/80 bg-white text-zinc-700 hover:border-blue-500 hover:text-blue-700 hover:bg-blue-50/40 transition-all cursor-pointer font-medium shadow-2xs"
        >
          <span className="font-bold text-blue-600">in</span>
          <span>LinkedIn</span>
        </button>

        <button
          type="button"
          onClick={shareTwitter}
          className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200/80 bg-white text-zinc-700 hover:border-zinc-500 hover:text-zinc-950 hover:bg-zinc-50 transition-all cursor-pointer font-medium shadow-2xs"
        >
          <span className="font-bold text-zinc-950">𝕏</span>
          <span>Post</span>
        </button>

        <button
          type="button"
          onClick={copyToClipboard}
          className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200/80 bg-white text-zinc-700 hover:bg-zinc-50 hover:text-zinc-950 transition-all cursor-pointer font-medium shadow-2xs"
        >
          {copied ? (
            <>
              <Check className="w-3.5 h-3.5 text-emerald-600 stroke-[3]" />
              <span className="text-emerald-700 font-bold">Link Copied!</span>
            </>
          ) : (
            <>
              <Copy className="w-3.5 h-3.5 text-zinc-500" />
              <span>Copy Link</span>
            </>
          )}
        </button>
      </div>

      <div className="hidden sm:block text-[11px] font-mono text-zinc-600">
        Free to share with your team &bull; No paywall
      </div>
    </div>
  );
}

