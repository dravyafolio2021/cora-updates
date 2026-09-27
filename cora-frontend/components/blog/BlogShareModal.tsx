'use client';

import React, { useState } from 'react';
import { X, Copy, Check, MessageCircle, Share2, Sparkles } from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

interface BlogShareModalProps {
  isOpen: boolean;
  onClose: () => void;
  title: string;
  url: string;
  articleSlug: string;
  insightText?: string;
}

export function BlogShareModal({
  isOpen,
  onClose,
  title,
  url,
  articleSlug,
  insightText,
}: BlogShareModalProps) {
  const [copied, setCopied] = useState(false);

  if (!isOpen) return null;

  const buildShareUrl = (source: string) => {
    const cleanUrl = url.split('?')[0];
    return `${cleanUrl}?utm_source=${source}&utm_medium=editorial_share&utm_campaign=cora_blog_${articleSlug}`;
  };

  const shareWhatsApp = () => {
    const targetUrl = buildShareUrl('whatsapp');
    trackEvent('article_share_modal', { platform: 'whatsapp', article_slug: articleSlug });
    const text = insightText
      ? `"${insightText}"\n\n— From "${title}" by Cora:\n${targetUrl}`
      : `${title}\n\n${targetUrl}`;
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
  };

  const shareLinkedIn = () => {
    const targetUrl = buildShareUrl('linkedin');
    trackEvent('article_share_modal', { platform: 'linkedin', article_slug: articleSlug });
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(targetUrl)}`, '_blank');
  };

  const shareX = () => {
    const targetUrl = buildShareUrl('twitter');
    trackEvent('article_share_modal', { platform: 'twitter', article_slug: articleSlug });
    const text = insightText
      ? `"${insightText}"\n\nvia @cora_hq\n${targetUrl}`
      : `${title}\n\n${targetUrl}`;
    window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}`, '_blank');
  };

  const copyLink = () => {
    const targetUrl = buildShareUrl('copy_link');
    trackEvent('article_share_modal', { platform: 'copy_link', article_slug: articleSlug });
    navigator.clipboard.writeText(targetUrl);
    setCopied(true);
    setTimeout(() => setCopied(false), 2500);
  };

  return (
    <div 
      className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs animate-in fade-in duration-200"
      onClick={onClose}
    >
      <div 
        className="w-full max-w-md bg-white rounded-3xl border border-zinc-200 p-6 sm:p-7 shadow-xl space-y-5 animate-in zoom-in-95 duration-200"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header */}
        <div className="flex items-center justify-between border-b border-zinc-200/80 pb-3.5">
          <div className="flex items-center gap-2">
            <div className="w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center">
              <Share2 className="w-3 h-3" />
            </div>
            <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950">
              Share Editorial Insight
            </h3>
          </div>
          <button
            type="button"
            onClick={onClose}
            className="w-7 h-7 rounded-full border border-zinc-200 flex items-center justify-center text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-colors cursor-pointer"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Insight Preview Card (if text is present) */}
        {insightText && (
          <div className="p-4 rounded-2xl bg-[#FBFaf7] border border-zinc-200 text-xs sm:text-sm text-zinc-800 leading-relaxed font-normal italic">
            &ldquo;{insightText}&rdquo;
          </div>
        )}

        {/* Share Buttons Grid */}
        <div className="grid grid-cols-2 gap-2.5">
          <button
            type="button"
            onClick={shareWhatsApp}
            className="flex items-center justify-center gap-2 px-4 py-3 rounded-2xl border border-zinc-200/90 bg-white hover:border-emerald-500 hover:bg-emerald-50/50 text-xs font-bold text-zinc-900 transition-all cursor-pointer shadow-2xs"
          >
            <MessageCircle className="w-4 h-4 text-emerald-600" />
            <span>WhatsApp</span>
          </button>

          <button
            type="button"
            onClick={shareLinkedIn}
            className="flex items-center justify-center gap-2 px-4 py-3 rounded-2xl border border-zinc-200/90 bg-white hover:border-blue-500 hover:bg-blue-50/50 text-xs font-bold text-zinc-900 transition-all cursor-pointer shadow-2xs"
          >
            <span className="font-bold text-blue-600">in</span>
            <span>LinkedIn</span>
          </button>

          <button
            type="button"
            onClick={shareX}
            className="flex items-center justify-center gap-2 px-4 py-3 rounded-2xl border border-zinc-200/90 bg-white hover:border-zinc-500 hover:bg-zinc-50 text-xs font-bold text-zinc-900 transition-all cursor-pointer shadow-2xs"
          >
            <span className="font-bold text-zinc-950">𝕏</span>
            <span>Post on X</span>
          </button>

          <button
            type="button"
            onClick={copyLink}
            className="flex items-center justify-center gap-2 px-4 py-3 rounded-2xl border border-zinc-200/90 bg-white hover:bg-zinc-50 text-xs font-bold text-zinc-900 transition-all cursor-pointer shadow-2xs"
          >
            {copied ? (
              <>
                <Check className="w-4 h-4 text-emerald-600" />
                <span className="text-emerald-700">Copied!</span>
              </>
            ) : (
              <>
                <Copy className="w-4 h-4 text-zinc-500" />
                <span>Copy Link</span>
              </>
            )}
          </button>
        </div>

        <div className="text-center">
          <p className="text-[11px] font-mono text-zinc-600">
            Link includes clean UTM tracking &bull; 100% free editorial access
          </p>
        </div>
      </div>
    </div>
  );
}
