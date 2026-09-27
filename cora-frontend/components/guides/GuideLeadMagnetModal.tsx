'use client';

import React, { useState, useEffect } from 'react';
import { X, Check, Download, FileText, ArrowRight, ShieldCheck, Loader2, Sparkles } from 'lucide-react';
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
  triggerPosition = 'hero',
}: GuideLeadMagnetModalProps) {
  const [email, setEmail] = useState('');
  const [firstName, setFirstName] = useState('');
  const [honeypot, setHoneypot] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [isUnlocked, setIsUnlocked] = useState(false);

  // Track modal open
  useEffect(() => {
    if (isOpen) {
      setErrorMessage('');
      trackEvent('lead_modal_open', {
        guide_slug: guideSlug,
        asset_id: asset.assetId,
        trigger_position: triggerPosition,
      });
    }
  }, [isOpen, guideSlug, asset.assetId, triggerPosition]);

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
          firstName: firstName.trim() || undefined,
          source: 'cora_guide_lead_magnet',
          utm_source: 'organic_guide',
          utm_medium: 'website',
          utm_campaign: 'guide_download',
          utm_content: guideSlug,
          tags: ['guide_lead_magnet', `asset_${asset.assetId}`, `guide_${guideSlug}`],
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
      trackEvent('lead_submit', {
        guide_slug: guideSlug,
        asset_id: asset.assetId,
        trigger_position: triggerPosition,
      });

      setIsUnlocked(true);
      setIsLoading(false);
    } catch (err) {
      console.error('Lead capture error:', err);
      // Fail open gracefully so user still receives their resource
      setIsUnlocked(true);
      setIsLoading(false);
    }
  };

  const handleDownloadClick = () => {
    trackEvent('pdf_download', {
      guide_slug: guideSlug,
      asset_id: asset.assetId,
    });
  };

  return (
    <div className="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-zinc-950/60 backdrop-blur-sm animate-in fade-in duration-200">
      {/* Backdrop click dismiss */}
      <div className="absolute inset-0" onClick={onClose} />

      {/* Modal / Bottom Sheet Box */}
      <div
        className="relative w-full max-w-[540px] bg-white rounded-t-3xl sm:rounded-2xl border border-zinc-200 shadow-2xl p-6 sm:p-8 z-10 animate-in slide-in-from-bottom-6 sm:zoom-in-95 duration-250 max-h-[92vh] overflow-y-auto"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Mobile Drag Handle Indicator */}
        <div className="sm:hidden w-12 h-1 bg-zinc-300 rounded-full mx-auto mb-4" />

        {/* Close Button */}
        <button
          type="button"
          onClick={onClose}
          className="absolute top-4 right-4 sm:top-5 sm:right-5 p-2 rounded-full text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 transition-colors"
          aria-label="Close modal"
        >
          <X className="w-5 h-5" />
        </button>

        {!isUnlocked ? (
          /* STATE 1: Lead Capture Form */
          <div>
            {/* Asset Category / Badge */}
            <div className="flex items-center gap-2 mb-3">
              <span className="px-2.5 py-0.5 rounded-full bg-zinc-100 text-zinc-800 text-[10.5px] font-mono font-bold uppercase tracking-wider border border-zinc-200">
                {asset.fileType.toUpperCase()} Download
              </span>
              <span className="text-[11px] font-mono text-zinc-500 font-medium">
                Instant Access
              </span>
            </div>

            {/* Title & Description */}
            <h2 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight leading-snug">
              Download the {asset.title}
            </h2>
            <p className="mt-2 text-xs sm:text-sm text-zinc-600 leading-relaxed">
              {asset.description}
            </p>

            {/* Highlights Checklist */}
            {asset.highlights && asset.highlights.length > 0 && (
              <div className="my-5 p-4 rounded-xl bg-zinc-50 border border-zinc-200/80 space-y-2">
                <span className="text-[11px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                  What’s inside the pack:
                </span>
                <ul className="space-y-1.5 text-xs text-zinc-700">
                  {asset.highlights.map((item, idx) => (
                    <li key={idx} className="flex items-start gap-2">
                      <Check className="w-3.5 h-3.5 text-zinc-900 shrink-0 mt-0.5" />
                      <span>{item}</span>
                    </li>
                  ))}
                </ul>
              </div>
            )}

            {/* Error message */}
            {errorMessage && (
              <div className="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                {errorMessage}
              </div>
            )}

            {/* Capture Form */}
            <form onSubmit={handleSubmit} className="space-y-3">
              {/* Optional Name */}
              <div>
                <label htmlFor="first_name" className="block text-xs font-semibold text-zinc-700 mb-1">
                  First Name <span className="text-zinc-400 font-normal">(optional)</span>
                </label>
                <input
                  id="first_name"
                  type="text"
                  value={firstName}
                  onChange={(e) => setFirstName(e.target.value)}
                  placeholder="e.g. Rohan"
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400"
                />
              </div>

              {/* Required Email */}
              <div>
                <label htmlFor="lead_email" className="block text-xs font-semibold text-zinc-700 mb-1">
                  Work Email <span className="text-rose-500">*</span>
                </label>
                <input
                  id="lead_email"
                  type="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="name@agency.com"
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400"
                />
              </div>

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

              {/* Submit CTA */}
              <button
                type="submit"
                disabled={isLoading}
                className="w-full flex items-center justify-center gap-2 bg-zinc-950 hover:bg-black text-white py-3 px-5 rounded-xl text-xs sm:text-sm font-bold shadow-md transition-all active:scale-[0.99] disabled:opacity-50 mt-4"
              >
                {isLoading ? (
                  <>
                    <Loader2 className="w-4 h-4 animate-spin" />
                    <span>Preparing Download...</span>
                  </>
                ) : (
                  <>
                    <span>Unlock Playbook (Free)</span>
                    <ArrowRight className="w-4 h-4" />
                  </>
                )}
              </button>

              <div className="flex items-center justify-center gap-1.5 pt-2 text-[11px] font-mono text-zinc-500 text-center">
                <ShieldCheck className="w-3.5 h-3.5 text-zinc-400" />
                <span>Zero spam • 1-click unsubscribe anytime</span>
              </div>
            </form>
          </div>
        ) : (
          /* STATE 2: Download Ready (Success) */
          <div className="text-center py-2 space-y-5 animate-in fade-in zoom-in-95 duration-200">
            <div className="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-950 border border-zinc-200 flex items-center justify-center mx-auto shadow-2xs">
              <Check className="w-6 h-6 stroke-[2.5]" />
            </div>

            <div>
              <span className="text-[11px] font-mono font-bold text-zinc-500 uppercase tracking-wider block mb-1">
                Unlocked Successfully
              </span>
              <h2 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
                Your Playbook is Ready
              </h2>
              <p className="mt-1.5 text-xs sm:text-sm text-zinc-600">
                Click below to download the complete {asset.title}. A copy has also been sent to your inbox.
              </p>
            </div>

            {/* Direct PDF Download Button (Primary) */}
            <a
              href={asset.fileUrl}
              download
              onClick={handleDownloadClick}
              className="w-full flex items-center justify-center gap-2 bg-zinc-950 hover:bg-black text-white py-3.5 px-6 rounded-xl text-sm font-bold shadow-md transition-all active:scale-[0.99]"
            >
              <Download className="w-4 h-4" />
              <span>Download PDF Playbook ({asset.fileSize || 'PDF'})</span>
            </a>

            {/* Secondary Product Upsell (Clean & Non-Intrusive) */}
            <div className="pt-5 border-t border-zinc-100 text-left bg-zinc-50 p-4 rounded-xl border border-zinc-200/70">
              <div className="flex items-center gap-1.5 text-[10.5px] font-mono font-bold text-zinc-500 uppercase">
                <Sparkles className="w-3.5 h-3.5 text-indigo-500" />
                <span>Next Step for Agency Founders</span>
              </div>
              <p className="mt-1 text-xs text-zinc-700 font-medium leading-relaxed">
                Want to run client onboarding, GST invoicing, and scope contracts on autopilot?
              </p>
              <a
                href="https://app.heycora.in/workspace/login?source=guide_lead_magnet_success"
                className="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-zinc-950 hover:text-zinc-600 transition-colors"
              >
                <span>Start Free in Cora</span>
                <ArrowRight className="w-3 h-3" />
              </a>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
