'use client';

import React, { useState, useEffect } from 'react';
import { 
  X, 
  Bell, 
  CheckCircle2, 
  ArrowRight, 
  Mail, 
  User
} from 'lucide-react';
import { UPCOMING_MODULES } from '@/lib/features-data';
import { trackEvent } from '@/components/analytics/Analytics';
import { useToast } from '@/components/ui/Toast';

interface RoadmapNotifyModalProps {
  isOpen: boolean;
  onClose: () => void;
  moduleId: string;
}

const ROADMAP_TITLES: Record<string, { title: string; eta: string }> = {
  'whatsapp-cloud': { title: 'WhatsApp Cloud API', eta: 'Q3 2026' },
  'photo-proofing': { title: 'Client Photo Proofing', eta: 'Q3 2026' },
  'integrated-payments': { title: 'Instant Auto-Payments', eta: 'Q3 2026' },
  'video-storyboard': { title: 'AI Video Storyboard', eta: 'Q4 2026' },
  'multi-branch': { title: 'Multi-Branch Workspaces', eta: 'Q4 2026' },
  'voice-ai-agent': { title: 'Autonomous Voice AI', eta: 'Q4 2026' },
  'tally-zoho-export': { title: 'Tally & Zoho Sync', eta: 'Q4 2026' },
  'client-mobile-app': { title: 'Client Mobile App', eta: 'Q1 2027' },
};

export function RoadmapNotifyModal({ isOpen, onClose, moduleId }: RoadmapNotifyModalProps) {
  const { showToast } = useToast();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSuccess, setIsSuccess] = useState(false);

  const activeModule = ROADMAP_TITLES[moduleId] || {
    title: UPCOMING_MODULES.find(m => m.id === moduleId)?.title || 'This Feature',
    eta: UPCOMING_MODULES.find(m => m.id === moduleId)?.eta || 'Coming Soon'
  };

  useEffect(() => {
    if (isOpen) {
      setIsSuccess(false);
      setName('');
      setEmail('');
    }
  }, [isOpen, moduleId]);

  // iOS-Safe Scroll Lock & Escape Key Handler
  useEffect(() => {
    if (!isOpen) return;

    const scrollY = window.scrollY;
    const originalHtmlOverflow = document.documentElement.style.overflow;
    const originalBodyOverflow = document.body.style.overflow;

    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';

    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') onClose();
    };

    window.addEventListener('keydown', handleKeyDown);

    return () => {
      document.documentElement.style.overflow = originalHtmlOverflow;
      document.body.style.overflow = originalBodyOverflow;
      window.scrollTo(0, scrollY);
      window.removeEventListener('keydown', handleKeyDown);
    };
  }, [isOpen, onClose]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!email.trim() || !email.includes('@')) {
      showToast('Please enter a valid email address.');
      return;
    }

    setIsSubmitting(true);

    const leadPayload = {
      name: name.trim() || 'Studio Founder',
      email: email.trim(),
      moduleId,
      moduleTitle: activeModule.title,
      eta: activeModule.eta,
      timestamp: new Date().toISOString()
    };

    // 1. Save to local storage
    try {
      const existing = JSON.parse(localStorage.getItem('cora_roadmap_leads') || '[]');
      existing.push(leadPayload);
      localStorage.setItem('cora_roadmap_leads', JSON.stringify(existing));
    } catch {}

    // 2. Track Analytics Event
    trackEvent('roadmap_notify_lead_captured', {
      email: leadPayload.email,
      name: leadPayload.name,
      module: leadPayload.moduleTitle
    });

    // 3. Post to backend lead receiver
    try {
      fetch('https://app.heycora.in/wp-admin/admin-ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'cora_workspace_submit_lead',
          lead_name: name.trim() || 'Studio Founder',
          lead_email: email.trim(),
          lead_source: `Roadmap Alert: ${activeModule.title} (${activeModule.eta})`
        })
      }).catch(() => {});
    } catch {}

    setTimeout(() => {
      setIsSubmitting(false);
      setIsSuccess(true);
      showToast(`You're on the list for ${activeModule.title}!`);
    }, 400);
  };

  if (!isOpen) return null;

  return (
    <div 
      className="fixed inset-0 z-50 flex items-center justify-center p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="modal-title"
    >
      {/* Darkened Backdrop */}
      <div 
        className="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity duration-200 animate-in fade-in"
        onClick={onClose}
        aria-hidden="true"
      />

      {/* Pop-up Card */}
      <div 
        className="relative w-full max-w-[420px] bg-white rounded-[28px] p-6 sm:p-7 shadow-2xl border border-zinc-200/90 z-10 animate-in zoom-in-95 duration-200 overflow-hidden"
      >
        {/* Close Button */}
        <button
          type="button"
          onClick={onClose}
          className="absolute top-5 right-5 w-8 h-8 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-500 hover:text-zinc-950 flex items-center justify-center transition-colors cursor-pointer"
          aria-label="Close dialog"
        >
          <X className="w-4 h-4" />
        </button>

        {isSuccess ? (
          /* SUCCESS STATE */
          <div className="py-4 text-center space-y-4 animate-in zoom-in-95 duration-150">
            <div className="w-14 h-14 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center mx-auto shadow-2xs">
              <CheckCircle2 className="w-7 h-7" />
            </div>

            <div className="space-y-1">
              <h3 className="font-display text-lg font-bold text-zinc-950">
                You&apos;re on the early access list!
              </h3>
              <p className="text-xs text-zinc-600 leading-relaxed max-w-xs mx-auto">
                We&apos;ll notify <span className="font-bold text-zinc-900">{email}</span> the moment <span className="font-bold text-zinc-900">{activeModule.title}</span> is ready for beta testing.
              </p>
            </div>

            <button
              type="button"
              onClick={onClose}
              className="w-full py-2.5 rounded-xl bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all cursor-pointer shadow-xs"
            >
              Done
            </button>
          </div>
        ) : (
          /* FORM STATE */
          <form onSubmit={handleSubmit} className="space-y-5">
            
            {/* Header with Dynamic Module Badge */}
            <div className="space-y-2 pr-6">
              <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200/80 text-[10px] font-mono font-bold text-amber-700">
                <Bell className="w-3 h-3 text-amber-500" />
                <span>ETA {activeModule.eta}</span>
              </div>

              <h2 id="modal-title" className="font-display text-xl font-bold text-zinc-950 leading-tight">
                Notify me for {activeModule.title}
              </h2>

              <p className="text-xs text-zinc-600 leading-relaxed">
                Be the first to get private beta access when this module launches.
              </p>
            </div>

            {/* Inputs: Name & Email only */}
            <div className="space-y-3 pt-1">
              
              {/* Full Name */}
              <div className="space-y-1">
                <label htmlFor="notify-name" className="text-xs font-semibold text-zinc-700">
                  Your Name
                </label>
                <div className="relative">
                  <User className="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                  <input
                    id="notify-name"
                    type="text"
                    required
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder="e.g. Shruti Sharma"
                    className="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-zinc-50 border border-zinc-200 text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:bg-white transition-all"
                  />
                </div>
              </div>

              {/* Work Email */}
              <div className="space-y-1">
                <label htmlFor="notify-email" className="text-xs font-semibold text-zinc-700">
                  Email Address
                </label>
                <div className="relative">
                  <Mail className="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                  <input
                    id="notify-email"
                    type="email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="founder@studio.com"
                    className="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-zinc-50 border border-zinc-200 text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:bg-white transition-all"
                  />
                </div>
              </div>

            </div>

            {/* Submit CTA */}
            <div className="pt-1">
              <button
                type="submit"
                disabled={isSubmitting}
                className="w-full py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 active:scale-[0.99] transition-all shadow-md flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
              >
                {isSubmitting ? (
                  <span>Saving...</span>
                ) : (
                  <>
                    <span>Notify Me When Ready</span>
                    <ArrowRight className="w-4 h-4 text-zinc-400" />
                  </>
                )}
              </button>
            </div>

          </form>
        )}
      </div>
    </div>
  );
}
