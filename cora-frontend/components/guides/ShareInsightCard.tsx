'use client';

import React, { useState } from 'react';
import { Share2, Copy, Check, MessageCircle, Twitter, Linkedin, Quote } from 'lucide-react';
import { ShareableInsight } from '@/lib/guides-data';
import { trackEvent } from '@/components/analytics/Analytics';

interface ShareInsightCardProps {
  insight: ShareableInsight;
  guideSlug: string;
  chapterSlug: string;
}

function OfficialWhatsAppIcon({ className = 'w-4 h-4' }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm0 18.15c-1.49 0-2.94-.4-4.22-1.15l-.3-.18-3.13.82.83-3.05-.2-.31a8.21 8.21 0 01-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 012.41 5.83c.01 4.54-3.68 8.24-8.19 8.24zm4.52-6.17c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.15.17-.25.25-.41.08-.17.04-.31-.02-.44-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.12.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.59.1.49-.07 1.49-.61 1.7-1.2.21-.59.21-1.09.15-1.2-.06-.11-.23-.17-.48-.29z" />
    </svg>
  );
}

function OfficialLinkedInIcon({ className = 'w-4 h-4' }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.2V10.9H6.46M7.83 6.64a1.64 1.64 0 1 0 0 3.28 1.64 1.64 0 0 0 0-3.28z" />
    </svg>
  );
}

function OfficialXIcon({ className = 'w-3.5 h-3.5' }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
    </svg>
  );
}

export function ShareInsightCard({ insight, guideSlug, chapterSlug }: ShareInsightCardProps) {
  const [copied, setCopied] = useState(false);

  const getShareUrl = (channel: string) => {
    const baseUrl = typeof window !== 'undefined' ? `${window.location.origin}/guides/${guideSlug}/` : `https://heycora.in/guides/${guideSlug}/`;
    return `${baseUrl}?utm_source=${channel}&utm_medium=organic_share&utm_campaign=guide_insight_share&utm_content=${chapterSlug}#${chapterSlug}`;
  };

  const getShareText = () => {
    return `“${insight.quote}” — ${insight.author} in ${insight.chapterTitle}`;
  };

  const handleCopy = async () => {
    const text = `${getShareText()}\n\nRead the full guide: ${getShareUrl('copy_link')}`;
    try {
      if (navigator.clipboard) {
        await navigator.clipboard.writeText(text);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
      }
      trackEvent('insight_share', {
        guide_slug: guideSlug,
        chapter_slug: chapterSlug,
        insight_id: insight.id,
        channel: 'clipboard',
      });
    } catch (err) {
      console.error('Clipboard copy error:', err);
    }
  };

  const handleNativeShare = async () => {
    const shareUrl = getShareUrl('native_share');
    if (typeof navigator !== 'undefined' && navigator.share) {
      try {
        await navigator.share({
          title: insight.chapterTitle,
          text: `“${insight.quote}” — ${insight.author}`,
          url: shareUrl,
        });
        trackEvent('insight_share', {
          guide_slug: guideSlug,
          chapter_slug: chapterSlug,
          insight_id: insight.id,
          channel: 'native',
        });
      } catch {
        // User cancelled share
      }
    } else {
      handleCopy();
    }
  };

  const shareOnX = () => {
    const text = encodeURIComponent(`“${insight.quote}”\n\nFrom the Cora Agency Guide by @heycora_in:`);
    const url = encodeURIComponent(getShareUrl('twitter'));
    trackEvent('insight_share', {
      guide_slug: guideSlug,
      chapter_slug: chapterSlug,
      insight_id: insight.id,
      channel: 'twitter',
    });
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'noopener,noreferrer');
  };

  const shareOnLinkedIn = () => {
    const url = encodeURIComponent(getShareUrl('linkedin'));
    trackEvent('insight_share', {
      guide_slug: guideSlug,
      chapter_slug: chapterSlug,
      insight_id: insight.id,
      channel: 'linkedin',
    });
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank', 'noopener,noreferrer');
  };

  const shareOnWhatsApp = () => {
    const text = encodeURIComponent(`“${insight.quote}” — ${insight.author}\n\n${getShareUrl('whatsapp')}`);
    trackEvent('insight_share', {
      guide_slug: guideSlug,
      chapter_slug: chapterSlug,
      insight_id: insight.id,
      channel: 'whatsapp',
    });
    window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank', 'noopener,noreferrer');
  };

  return (
    <figure className="my-8 relative overflow-hidden rounded-2xl bg-zinc-50 border border-zinc-200/90 p-6 sm:p-7 shadow-2xs">
      {/* Decorative Large Quote Mark in Background */}
      <Quote className="absolute -top-2 -left-2 w-16 h-16 text-zinc-200/60 pointer-events-none stroke-1" />

      <div className="relative z-10">
        <blockquote className="font-display text-lg sm:text-xl font-bold text-zinc-950 leading-snug tracking-tight">
          “{insight.quote}”
        </blockquote>

        <figcaption className="mt-3 flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-zinc-200/60">
          <div className="text-xs text-zinc-600">
            <span className="font-semibold text-zinc-950">{insight.author}</span>
            <span className="mx-1.5 text-zinc-400">&bull;</span>
            <span className="text-zinc-500 font-mono text-[11px]">{insight.context}</span>
          </div>

          {/* Share Actions Matrix */}
          <div className="flex items-center gap-1">
            <button
              type="button"
              onClick={handleNativeShare}
              className="sm:hidden p-1.5 rounded-lg text-zinc-600 hover:text-zinc-950 hover:bg-zinc-200/70 transition-colors text-xs flex items-center gap-1 font-mono"
              title="Share Insight"
            >
              <Share2 className="w-3.5 h-3.5" />
            </button>
            <button
              type="button"
              onClick={shareOnWhatsApp}
              className="p-1.5 rounded-lg text-zinc-600 hover:text-[#25D366] hover:bg-zinc-200/70 transition-colors"
              title="Share on WhatsApp"
            >
              <OfficialWhatsAppIcon className="w-4 h-4" />
            </button>
            <button
              type="button"
              onClick={shareOnLinkedIn}
              className="p-1.5 rounded-lg text-zinc-600 hover:text-[#0A66C2] hover:bg-zinc-200/70 transition-colors"
              title="Share on LinkedIn"
            >
              <OfficialLinkedInIcon className="w-4 h-4" />
            </button>
            <button
              type="button"
              onClick={shareOnX}
              className="p-1.5 rounded-lg text-zinc-600 hover:text-zinc-950 hover:bg-zinc-200/70 transition-colors"
              title="Share on X"
            >
              <OfficialXIcon className="w-3.5 h-3.5" />
            </button>
            <button
              type="button"
              onClick={handleCopy}
              className="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-zinc-200 text-zinc-900 text-xs font-semibold hover:bg-zinc-100 transition-colors shadow-2xs"
            >
              {copied ? (
                <>
                  <Check className="w-3.5 h-3.5 text-zinc-900" />
                  <span className="text-zinc-900 text-[11px] font-bold">Copied</span>
                </>
              ) : (
                <>
                  <Copy className="w-3.5 h-3.5 text-zinc-500" />
                  <span className="text-[11px]">Copy</span>
                </>
              )}
            </button>
          </div>
        </figcaption>
      </div>
    </figure>
  );
}
