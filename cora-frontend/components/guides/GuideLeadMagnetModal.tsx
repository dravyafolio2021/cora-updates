'use client';

import React, { useState, useEffect } from 'react';
import { X, Check, Download, FileText, ArrowRight, ShieldCheck, Loader2 } from 'lucide-react';
import { DownloadableAsset } from '@/lib/guides-data';
import { trackEvent } from '@/components/analytics/Analytics';

interface GuideLeadMagnetModalProps {
  isOpen: boolean;
  onClose: () => void;
  guideTitle: string;
  guideSlug: string;
  asset: DownloadableAsset;
  triggerPosition?: string;
}

export function GuideLeadMagnetModal({
  isOpen,
  onClose,
  guideTitle,
  guideSlug,
  asset,
  triggerPosition = 'general',
}: GuideLeadMagnetModalProps) {
  const [email, setEmail] = useState('');
  const [honeypot, setHoneypot] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [isUnlocked, setIsUnlocked] = useState(false);

  // Reset state whenever modal opens
  useEffect(() => {
    if (isOpen) {
      setErrorMessage('');
      // Keep unlocked state if already unlocked in this session
    }
  }, [isOpen]);

  // Handle ESC key press
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape' && isOpen) {
        onClose();
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isOpen, onClose]);

  if (!isOpen) return null;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMessage('');

    if (!email || !email.includes('@')) {
      setErrorMessage('Please enter a valid work email address.');
      return;
    }

    if (honeypot) {
      // Spam honeypot triggered
      setIsUnlocked(true);
      return;
    }

    setIsLoading(true);

    try {
      const response = await fetch('/api/newsletter', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email,
          source: 'cora_guide_lead_magnet',
          utm_source: 'cora_guides',
          utm_medium: 'website',
          utm_campaign: 'lead_magnet',
          utm_content: guideSlug,
          tags: ['guide_subscriber', 'lead_magnet', `asset_${asset.assetId}`, `guide_${guideSlug}`],
          path: typeof window !== 'undefined' ? window.location.pathname : `/guides/${guideSlug}/`,
          referrer: typeof document !== 'undefined' ? document.referrer : '',
        }),
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok && !data.success) {
        setErrorMessage(data.error || 'Could not verify email. Please try again.');
        setIsLoading(false);
        return;
      }

      // Track conversion
      trackEvent('guide_lead_capture_success', {
        guide_slug: guideSlug,
        asset_id: asset.assetId,
        cta_position: triggerPosition,
      });

      setIsUnlocked(true);
      setIsLoading(false);
    } catch (err) {
      console.error('[Guide Lead Magnet] Error', err);
      // Fail gracefully: let the user get the asset rather than blocking
      setIsUnlocked(true);
      setIsLoading(false);
    }
  };

  const handleDownloadAsset = () => {
    trackEvent('guide_download_success', {
      guide_slug: guideSlug,
      asset_id: asset.assetId,
      cta_position: triggerPosition,
    });

    const downloadUrl = asset.fileUrl || `/api/guides/download/${asset.assetId}`;
    window.open(downloadUrl, '_blank');
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
      {/* Backdrop */}
      <div
        onClick={onClose}
        className="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
      />

      {/* Modal Dialog Card */}
      <div className="relative w-full max-w-lg rounded-3xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-2xl z-10 text-zinc-900 selection:bg-zinc-200 animate-in fade-in zoom-in-95 duration-200">
        {/* Close Button */}
        <button
          onClick={onClose}
          className="absolute right-5 top-5 p-1.5 rounded-full text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 transition-colors cursor-pointer"
          aria-label="Close dialog"
        >
          <X className="w-5 h-5" />
        </button>

        {!isUnlocked ? (
          <div>
            {/* Asset Header Badge */}
            <div className="flex items-center gap-2 text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 mb-3">
              <span className="px-2.5 py-0.5 rounded-md bg-zinc-100 border border-zinc-200 text-zinc-800">
                {asset.fileType.toUpperCase()} {asset.fileSize ? `• ${asset.fileSize}` : 'PACKAGE'}
              </span>
              <span>FREE OPERATIONAL ASSET</span>
            </div>

            <h3 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-950 leading-snug">
              {asset.title}
            </h3>

            <p className="mt-2 text-xs sm:text-sm text-zinc-600 leading-relaxed">
              {asset.description}
            </p>

            {/* Highlights List */}
            {asset.highlights && asset.highlights.length > 0 && (
              <div className="my-5 rounded-2xl border border-zinc-200/80 bg-[#FBFaf7] p-4 space-y-2">
                <div className="text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-400 mb-1">
                  WHAT&apos;S INCLUDED IN THIS PACK:
                </div>
                {asset.highlights.map((highlight, idx) => (
                  <div key={idx} className="flex items-start gap-2.5 text-xs text-zinc-800">
                    <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                    <span>{highlight}</span>
                  </div>
                ))}
              </div>
            )}

            {/* Form */}
            <form onSubmit={handleSubmit} className="space-y-3 pt-1">
              {/* Spam Honeypot */}
              <input
                type="text"
                name="companyWebsite"
                value={honeypot}
                onChange={(e) => setHoneypot(e.target.value)}
                tabIndex={-1}
                autoComplete="off"
                className="hidden"
              />

              <div>
                <label
                  htmlFor="guide-modal-email"
                  className="block text-xs font-mono font-bold uppercase tracking-wider text-zinc-700 mb-1.5"
                >
                  Work Email Address
                </label>
                <input
                  id="guide-modal-email"
                  type="email"
                  required
                  placeholder="operator@youragency.com"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="w-full px-4 py-3 rounded-xl border border-zinc-300 text-sm focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950 bg-white placeholder:text-zinc-400"
                />
              </div>

              {errorMessage && (
                <div className="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-700">
                  {errorMessage}
                </div>
              )}

              <button
                type="submit"
                disabled={isLoading}
                className="w-full mt-2 py-3.5 px-5 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold tracking-wide hover:bg-zinc-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm disabled:opacity-50"
              >
                {isLoading ? (
                  <>
                    <Loader2 className="w-4 h-4 animate-spin" />
                    <span>Unlocking Pack...</span>
                  </>
                ) : (
                  <>
                    <span>{asset.ctaText || 'Get the Complete Pack Free'}</span>
                    <ArrowRight className="w-4 h-4" />
                  </>
                )}
              </button>

              <div className="pt-2 flex items-center justify-center gap-1.5 text-[11px] text-zinc-600 font-mono text-center">
                <ShieldCheck className="w-3.5 h-3.5 text-zinc-600" />
                <span>Zero spam. Instant download access unlocked immediately.</span>
              </div>
            </form>
          </div>
        ) : (
          /* Unlocked Success State */
          <div className="text-center py-4">
            <div className="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
              <Check className="w-6 h-6 stroke-[2.5]" />
            </div>

            <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-600 mb-1">
              ✓ UNLOCKED &amp; READY
            </div>

            <h3 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-950">
              Your SOP Pack is Ready
            </h3>

            <p className="mt-2 text-xs sm:text-sm text-zinc-600 max-w-sm mx-auto leading-relaxed">
              We&apos;ve unlocked {asset.title} for your agency. Click below to download the package directly to your device.
            </p>

            <div className="mt-6 space-y-2">
              <button
                onClick={handleDownloadAsset}
                className="w-full py-3.5 px-5 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold tracking-wide hover:bg-zinc-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md"
              >
                <Download className="w-4 h-4" />
                <span>Download Asset ({asset.fileType.toUpperCase()})</span>
              </button>

              <button
                onClick={onClose}
                className="w-full py-2.5 px-4 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition-colors"
              >
                Return to Guide
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
