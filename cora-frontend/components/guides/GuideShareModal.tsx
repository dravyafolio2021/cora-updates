'use client';

import React, { useState } from 'react';
import { X, Copy, Check, Share2, MessageCircle, Twitter, Linkedin } from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

interface GuideShareModalProps {
  isOpen: boolean;
  onClose: () => void;
  guideTitle: string;
  guideSlug: string;
  chapterSlug?: string;
  chapterTitle?: string;
}

function OfficialWhatsAppIcon({ className = 'w-5 h-5' }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm0 18.15c-1.49 0-2.94-.4-4.22-1.15l-.3-.18-3.13.82.83-3.05-.2-.31a8.21 8.21 0 01-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 012.41 5.83c.01 4.54-3.68 8.24-8.19 8.24zm4.52-6.17c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.15.17-.25.25-.41.08-.17.04-.31-.02-.44-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.12.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.59.1.49-.07 1.49-.61 1.7-1.2.21-.59.21-1.09.15-1.2-.06-.11-.23-.17-.48-.29z" />
    </svg>
  );
}

function OfficialLinkedInIcon({ className = 'w-5 h-5' }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.2V10.9H6.46M7.83 6.64a1.64 1.64 0 1 0 0 3.28 1.64 1.64 0 0 0 0-3.28z" />
    </svg>
  );
}

function OfficialXIcon({ className = 'w-5 h-5' }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
    </svg>
  );
}

export function GuideShareModal({
  isOpen,
  onClose,
  guideTitle,
  guideSlug,
  chapterSlug,
  chapterTitle,
}: GuideShareModalProps) {
  const [copied, setCopied] = useState(false);

  if (!isOpen) return null;

  const contentParam = chapterSlug || guideSlug;
  const shareAnchor = chapterSlug ? `#${chapterSlug}` : '';
  const shareUrl = typeof window !== 'undefined'
    ? `${window.location.origin}/guides/${guideSlug}/?utm_source=share&utm_medium=organic_share&utm_campaign=guide_share&utm_content=${contentParam}${shareAnchor}`
    : `https://heycora.in/guides/${guideSlug}/?utm_content=${contentParam}${shareAnchor}`;

  const shareTitle = chapterTitle ? `${chapterTitle} — ${guideTitle}` : guideTitle;

  const handleCopyLink = async () => {
    try {
      if (navigator.clipboard) {
        await navigator.clipboard.writeText(shareUrl);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
      }
      trackEvent('guide_share', {
        guide_slug: guideSlug,
        chapter_slug: chapterSlug || 'root',
        channel: 'clipboard',
      });
    } catch (err) {
      console.error('Clipboard copy error:', err);
    }
  };

  const handleNativeShare = async () => {
    if (typeof navigator !== 'undefined' && navigator.share) {
      try {
        await navigator.share({
          title: shareTitle,
          text: `Check out this digital playbook: ${shareTitle}`,
          url: shareUrl,
        });
        trackEvent('guide_share', {
          guide_slug: guideSlug,
          chapter_slug: chapterSlug || 'root',
          channel: 'native',
        });
        onClose();
      } catch {
        // User dismissed share
      }
    } else {
      handleCopyLink();
    }
  };

  const handleSocialShare = (platform: 'linkedin' | 'twitter' | 'whatsapp') => {
    let url = '';
    const encodedUrl = encodeURIComponent(
      typeof window !== 'undefined'
        ? `${window.location.origin}/guides/${guideSlug}/?utm_source=${platform}&utm_medium=organic_share&utm_campaign=guide_share&utm_content=${contentParam}${shareAnchor}`
        : `https://heycora.in/guides/${guideSlug}/?utm_content=${contentParam}`
    );
    const encodedTitle = encodeURIComponent(shareTitle);

    if (platform === 'linkedin') {
      url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`;
    } else if (platform === 'twitter') {
      url = `https://twitter.com/intent/tweet?text=${encodedTitle}&url=${encodedUrl}`;
    } else if (platform === 'whatsapp') {
      url = `https://api.whatsapp.com/send?text=${encodedTitle}%20${encodedUrl}`;
    }

    trackEvent('guide_share', {
      guide_slug: guideSlug,
      chapter_slug: chapterSlug || 'root',
      channel: platform,
    });

    window.open(url, '_blank', 'noopener,noreferrer');
  };

  return (
    <div className="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-zinc-950/60 backdrop-blur-sm animate-in fade-in duration-200">
      <div className="absolute inset-0" onClick={onClose} />

      <div
        className="relative w-full max-w-[460px] bg-white rounded-t-3xl sm:rounded-2xl border border-zinc-200 shadow-2xl p-6 sm:p-7 z-10 animate-in slide-in-from-bottom sm:zoom-in-95 duration-200"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="sm:hidden w-12 h-1 bg-zinc-300 rounded-full mx-auto mb-4" />

        <button
          type="button"
          onClick={onClose}
          className="absolute top-4 right-4 sm:top-5 sm:right-5 p-2 rounded-full text-zinc-400 hover:text-zinc-950 hover:bg-zinc-100 transition-colors"
          aria-label="Close share dialog"
        >
          <X className="w-5 h-5" />
        </button>

        <div className="flex items-center gap-2 mb-2">
          <Share2 className="w-4 h-4 text-zinc-700" />
          <span className="font-display font-bold text-base text-zinc-950">
            Share {chapterTitle ? 'Chapter' : 'Guide'}
          </span>
        </div>
        <p className="text-xs text-zinc-600 line-clamp-2">
          {shareTitle}
        </p>

        {/* Share Channel Buttons Grid */}
        <div className="grid grid-cols-3 gap-2.5 my-5">
          <button
            type="button"
            onClick={() => handleSocialShare('whatsapp')}
            className="flex flex-col items-center justify-center gap-1.5 p-3.5 rounded-xl bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border border-zinc-200/80 transition-all text-xs font-semibold group shadow-2xs"
          >
            <div className="w-6 h-6 flex items-center justify-center text-[#25D366] group-hover:scale-105 transition-transform">
              <OfficialWhatsAppIcon className="w-5 h-5" />
            </div>
            <span className="text-[11.5px] font-semibold">WhatsApp</span>
          </button>

          <button
            type="button"
            onClick={() => handleSocialShare('linkedin')}
            className="flex flex-col items-center justify-center gap-1.5 p-3.5 rounded-xl bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border border-zinc-200/80 transition-all text-xs font-semibold group shadow-2xs"
          >
            <div className="w-6 h-6 flex items-center justify-center text-[#0A66C2] group-hover:scale-105 transition-transform">
              <OfficialLinkedInIcon className="w-5 h-5" />
            </div>
            <span className="text-[11.5px] font-semibold">LinkedIn</span>
          </button>

          <button
            type="button"
            onClick={() => handleSocialShare('twitter')}
            className="flex flex-col items-center justify-center gap-1.5 p-3.5 rounded-xl bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border border-zinc-200/80 transition-all text-xs font-semibold group shadow-2xs"
          >
            <div className="w-6 h-6 flex items-center justify-center text-zinc-950 group-hover:scale-105 transition-transform">
              <OfficialXIcon className="w-4.5 h-4.5" />
            </div>
            <span className="text-[11.5px] font-semibold">X (Twitter)</span>
          </button>
        </div>

        {/* Mobile Native Share Trigger */}
        <button
          type="button"
          onClick={handleNativeShare}
          className="sm:hidden w-full mb-3 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-bold text-xs transition-colors"
        >
          <Share2 className="w-4 h-4" />
          <span>More Share Options...</span>
        </button>

        {/* Copy Link Input Bar */}
        <div className="flex items-center gap-2 p-1.5 rounded-xl bg-zinc-50 border border-zinc-200">
          <input
            type="text"
            readOnly
            value={shareUrl}
            className="w-full bg-transparent px-2 text-xs text-zinc-600 focus:outline-none truncate font-mono select-all"
          />
          <button
            type="button"
            onClick={handleCopyLink}
            className="inline-flex items-center gap-1 bg-zinc-950 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-black transition-colors shrink-0 shadow-2xs"
          >
            {copied ? (
              <>
                <Check className="w-3.5 h-3.5 text-zinc-200" />
                <span>Copied</span>
              </>
            ) : (
              <>
                <Copy className="w-3.5 h-3.5 text-zinc-400" />
                <span>Copy</span>
              </>
            )}
          </button>
        </div>
      </div>
    </div>
  );
}
