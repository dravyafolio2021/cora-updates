'use client';

import React, { useState, useEffect } from 'react';
import { 
  X, 
  Bell, 
  Check, 
  CheckCircle2, 
  ArrowRight, 
  Sparkles, 
  Mail, 
  Building, 
  Phone, 
  Layers,
  Lock,
  ChevronRight
} from 'lucide-react';
import { UPCOMING_MODULES } from '@/lib/features-data';
import { trackEvent } from '@/components/analytics/Analytics';
import { useToast } from '@/components/ui/Toast';

interface RoadmapNotifyDrawerProps {
  isOpen: boolean;
  onClose: () => void;
  initialModuleId?: string;
}

const ROADMAP_MODULE_OPTIONS = [
  { id: 'whatsapp-cloud', label: 'WhatsApp Cloud API', eta: 'Q3 2026', desc: '2-way CRM client chat' },
  { id: 'photo-proofing', label: 'Client Photo Proofing', eta: 'Q3 2026', desc: 'Watermarked gallery approvals' },
  { id: 'integrated-payments', label: 'Instant Auto-Payments', eta: 'Q3 2026', desc: 'UPI & card auto-reconciliation' },
  { id: 'video-storyboard', label: 'AI Video Storyboard', eta: 'Q4 2026', desc: 'AI script decks & scene cards' },
  { id: 'multi-branch', label: 'Multi-Branch Workspaces', eta: 'Q4 2026', desc: 'Shared regional studio hubs' },
  { id: 'voice-ai-agent', label: 'Autonomous Voice AI', eta: 'Q4 2026', desc: 'Inbound phone booking concierge' },
  { id: 'tally-zoho-export', label: 'Tally & Zoho Sync', eta: 'Q4 2026', desc: '1-click CA tax ledger export' },
  { id: 'client-mobile-app', label: 'Client Mobile App', eta: 'Q1 2027', desc: 'Branded iOS & Android portal' },
];

export function RoadmapNotifyDrawer({ isOpen, onClose, initialModuleId }: RoadmapNotifyDrawerProps) {
  const { showToast } = useToast();
  const [selectedModules, setSelectedModules] = useState<string[]>([]);
  const [email, setEmail] = useState('');
  const [studioName, setStudioName] = useState('');
  const [phone, setPhone] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSuccess, setIsSuccess] = useState(false);

  // Sync initial module when drawer opens
  useEffect(() => {
    if (isOpen) {
      if (initialModuleId && ROADMAP_MODULE_OPTIONS.some(m => m.id === initialModuleId)) {
        setSelectedModules([initialModuleId]);
      } else if (selectedModules.length === 0) {
        setSelectedModules(['whatsapp-cloud', 'photo-proofing']);
      }
      setIsSuccess(false);
    }
  }, [isOpen, initialModuleId]);

  // iOS-Safe Scroll Lock & Escape Key Handler
  useEffect(() => {
    if (!isOpen) return;

    const scrollY = window.scrollY;
    const originalHtmlOverflow = document.documentElement.style.overflow;
    const originalBodyOverflow = document.body.style.overflow;
    const originalBodyPosition = document.body.style.position;
    const originalBodyTop = document.body.style.top;
    const originalBodyWidth = document.body.style.width;

    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';

    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') onClose();
    };

    window.addEventListener('keydown', handleKeyDown);

    return () => {
      document.documentElement.style.overflow = originalHtmlOverflow;
      document.body.style.overflow = originalBodyOverflow;
      document.body.style.position = originalBodyPosition;
      document.body.style.top = originalBodyTop;
      document.body.style.width = originalBodyWidth;
      window.scrollTo(0, scrollY);
      window.removeEventListener('keydown', handleKeyDown);
    };
  }, [isOpen, onClose]);

  const toggleModule = (id: string) => {
    setSelectedModules(prev => {
      if (prev.includes(id)) {
        // Keep at least one selected if possible
        if (prev.length === 1) return prev;
        return prev.filter(m => m !== id);
      } else {
        return [...prev, id];
      }
    });
  };

  const handleSelectAll = () => {
    if (selectedModules.length === ROADMAP_MODULE_OPTIONS.length) {
      setSelectedModules([initialModuleId || 'whatsapp-cloud']);
    } else {
      setSelectedModules(ROADMAP_MODULE_OPTIONS.map(m => m.id));
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!email.trim() || !email.includes('@')) {
      showToast('Please enter a valid email address.');
      return;
    }

    if (selectedModules.length === 0) {
      showToast('Please select at least one module.');
      return;
    }

    setIsSubmitting(true);

    const leadPayload = {
      email: email.trim(),
      studioName: studioName.trim() || 'Not specified',
      phone: phone.trim() || 'Not specified',
      interestedModules: selectedModules,
      selectedModuleLabels: ROADMAP_MODULE_OPTIONS.filter(m => selectedModules.includes(m.id)).map(m => m.label),
      timestamp: new Date().toISOString(),
      source: 'features_roadmap_drawer'
    };

    // 1. Save to localStorage backup
    try {
      const existingLeads = JSON.parse(localStorage.getItem('cora_roadmap_leads') || '[]');
      existingLeads.push(leadPayload);
      localStorage.setItem('cora_roadmap_leads', JSON.stringify(existingLeads));
    } catch {
      // Ignore localStorage errors
    }

    // 2. Track Analytics Event
    trackEvent('roadmap_lead_captured', {
      email: leadPayload.email,
      module_count: selectedModules.length,
      modules: selectedModules.join(',')
    });

    // 3. Post to backend endpoint asynchronously
    try {
      fetch('https://app.heycora.in/wp-admin/admin-ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'cora_workspace_submit_lead',
          lead_name: studioName || 'Studio Founder',
          lead_email: email,
          lead_phone: phone,
          lead_source: `Roadmap Early Access: ${selectedModules.join(', ')}`
        })
      }).catch(() => {});
    } catch {
      // Fail safely without interrupting user feedback
    }

    setTimeout(() => {
      setIsSubmitting(false);
      setIsSuccess(true);
      showToast("Priority access confirmed! We'll notify you.");
    }, 600);
  };

  if (!isOpen) return null;

  return (
    <div 
      className="fixed inset-0 z-50 flex justify-end"
      role="dialog"
      aria-modal="true"
      aria-labelledby="roadmap-drawer-title"
    >
      {/* Darkened Backdrop */}
      <div 
        className="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity duration-300 animate-in fade-in"
        onClick={onClose}
        onTouchMove={(e) => e.preventDefault()}
        aria-hidden="true"
      />

      {/* Slide-over Drawer Sheet: Right Slide on Desktop, Bottom Sheet on Mobile */}
      <div 
        className="relative w-full max-w-lg lg:max-w-[480px] bg-white h-full max-h-screen shadow-2xl z-10 flex flex-col overflow-hidden animate-in slide-in-from-right sm:slide-in-from-right duration-300 ease-out border-l border-zinc-200"
        style={{ touchAction: 'pan-y' }}
      >
        
        {/* Header Bar */}
        <div className="px-6 pt-5 pb-4 border-b border-zinc-100 flex items-center justify-between shrink-0 bg-white">
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shadow-2xs">
              <Bell className="w-4 h-4" />
            </div>
            <div>
              <span className="text-[10px] font-mono font-bold text-amber-700 uppercase tracking-wider block">
                Early Access Waitlist
              </span>
              <h2 id="roadmap-drawer-title" className="font-display text-base font-bold text-zinc-950">
                Get Notified on Release
              </h2>
            </div>
          </div>

          <button
            type="button"
            onClick={onClose}
            className="w-8 h-8 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-600 hover:text-zinc-950 flex items-center justify-center transition-colors cursor-pointer"
            aria-label="Close early access drawer"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Drawer Body (Scrollable) */}
        <div 
          className="flex-1 overflow-y-auto overscroll-contain px-6 py-5 space-y-6"
          style={{ WebkitOverflowScrolling: 'touch' }}
        >
          
          {/* SUCCESS STATE */}
          {isSuccess ? (
            <div className="py-8 text-center space-y-5 animate-in zoom-in-95 duration-200">
              <div className="w-16 h-16 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center mx-auto shadow-sm">
                <CheckCircle2 className="w-8 h-8" />
              </div>

              <div className="space-y-1.5 max-w-sm mx-auto">
                <h3 className="font-display text-xl font-bold text-zinc-950">
                  You&apos;re on the Priority List!
                </h3>
                <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
                  We&apos;ve reserved your early access spot for <span className="font-bold text-zinc-900">{selectedModules.length} module{selectedModules.length > 1 ? 's' : ''}</span>. We&apos;ll email <span className="font-bold text-zinc-900">{email}</span> the moment beta builds go live.
                </p>
              </div>

              {/* Selected Modules Summary Pill */}
              <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 text-left space-y-2">
                <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                  Tracked Modules:
                </span>
                <div className="flex flex-wrap gap-1.5">
                  {ROADMAP_MODULE_OPTIONS.filter(m => selectedModules.includes(m.id)).map(m => (
                    <span key={m.id} className="text-xs font-semibold text-zinc-800 bg-white px-2.5 py-1 rounded-lg border border-zinc-200/80 flex items-center gap-1 shadow-2xs">
                      <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                      {m.label}
                    </span>
                  ))}
                </div>
              </div>

              <button
                type="button"
                onClick={onClose}
                className="w-full py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all shadow-sm cursor-pointer"
              >
                Done
              </button>
            </div>
          ) : (
            
            /* FORM STATE */
            <form onSubmit={handleSubmit} className="space-y-6">
              
              {/* Intent Info Banner */}
              <div className="p-4 rounded-2xl bg-gradient-to-r from-amber-50/70 via-orange-50/40 to-transparent border border-amber-200/70 text-xs text-zinc-700 space-y-1">
                <div className="font-bold text-zinc-950 flex items-center gap-1.5">
                  <Sparkles className="w-3.5 h-3.5 text-amber-600" />
                  <span>Private Beta Guarantee</span>
                </div>
                <p className="text-[11px] text-zinc-600 leading-relaxed">
                  Early waitlist members receive 1,000 free operations, zero queue delay, and direct feedback channels with our engineering team.
                </p>
              </div>

              {/* Dynamic Module Selector (Select, not type) */}
              <div className="space-y-2.5">
                <div className="flex items-center justify-between">
                  <label className="text-xs font-bold text-zinc-950 flex items-center gap-1">
                    <span>Select Modules of Interest</span>
                    <span className="text-rose-500">*</span>
                  </label>
                  <button
                    type="button"
                    onClick={handleSelectAll}
                    className="text-[11px] font-semibold text-zinc-500 hover:text-zinc-950 cursor-pointer underline"
                  >
                    {selectedModules.length === ROADMAP_MODULE_OPTIONS.length ? 'Clear extra' : 'Select all (8)'}
                  </button>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  {ROADMAP_MODULE_OPTIONS.map((item) => {
                    const isChecked = selectedModules.includes(item.id);
                    return (
                      <button
                        key={item.id}
                        type="button"
                        onClick={() => toggleModule(item.id)}
                        className={`p-3 rounded-xl text-left border transition-all cursor-pointer flex items-start justify-between gap-2 ${
                          isChecked
                            ? 'bg-zinc-950 text-white border-zinc-950 shadow-xs'
                            : 'bg-zinc-50/80 text-zinc-800 border-zinc-200/90 hover:bg-white hover:border-zinc-300'
                        }`}
                      >
                        <div className="space-y-0.5 min-w-0">
                          <div className="flex items-center gap-1.5">
                            <span className="text-xs font-bold truncate">{item.label}</span>
                          </div>
                          <span className={`text-[10px] font-mono block ${isChecked ? 'text-zinc-400' : 'text-zinc-500'}`}>
                            {item.eta}
                          </span>
                        </div>

                        <div className={`w-4 h-4 rounded-md flex items-center justify-center shrink-0 mt-0.5 border ${
                          isChecked 
                            ? 'bg-white text-zinc-950 border-white' 
                            : 'border-zinc-300 bg-white'
                        }`}>
                          {isChecked && <Check className="w-3 h-3 stroke-[3]" />}
                        </div>
                      </button>
                    );
                  })}
                </div>
              </div>

              {/* Contact Information Fields */}
              <div className="space-y-3.5 pt-1">
                
                {/* Email Address */}
                <div className="space-y-1.5">
                  <label htmlFor="lead-email" className="text-xs font-bold text-zinc-950 flex items-center gap-1">
                    <span>Work Email Address</span>
                    <span className="text-rose-500">*</span>
                  </label>
                  <div className="relative">
                    <Mail className="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input
                      id="lead-email"
                      type="email"
                      required
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      placeholder="founder@studio.com"
                      className="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-zinc-50 border border-zinc-200 text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:bg-white transition-all"
                    />
                  </div>
                </div>

                {/* Studio Name (Optional) */}
                <div className="space-y-1.5">
                  <label htmlFor="lead-studio" className="text-xs font-semibold text-zinc-700 flex items-center justify-between">
                    <span>Studio / Agency Name</span>
                    <span className="text-[10px] font-mono text-zinc-400">Optional</span>
                  </label>
                  <div className="relative">
                    <Building className="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input
                      id="lead-studio"
                      type="text"
                      value={studioName}
                      onChange={(e) => setStudioName(e.target.value)}
                      placeholder="e.g. Luminary Films Mumbai"
                      className="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-zinc-50 border border-zinc-200 text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:bg-white transition-all"
                    />
                  </div>
                </div>

                {/* WhatsApp Number (Optional) */}
                <div className="space-y-1.5">
                  <label htmlFor="lead-phone" className="text-xs font-semibold text-zinc-700 flex items-center justify-between">
                    <span>WhatsApp Number</span>
                    <span className="text-[10px] font-mono text-zinc-400">For release alert</span>
                  </label>
                  <div className="relative">
                    <Phone className="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input
                      id="lead-phone"
                      type="tel"
                      value={phone}
                      onChange={(e) => setPhone(e.target.value)}
                      placeholder="+91 98765 43210"
                      className="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-zinc-50 border border-zinc-200 text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:bg-white transition-all"
                    />
                  </div>
                </div>

              </div>

              {/* Submit CTA */}
              <div className="pt-2">
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="w-full py-3.5 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 active:scale-[0.99] transition-all shadow-md flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                >
                  {isSubmitting ? (
                    <span>Registering Priority Access...</span>
                  ) : (
                    <>
                      <span>Notify Me on Early Access</span>
                      <ArrowRight className="w-4 h-4 text-zinc-400" />
                    </>
                  )}
                </button>
                <div className="flex items-center justify-center gap-1 text-[10px] text-zinc-400 mt-2">
                  <Lock className="w-3 h-3" />
                  <span>No spam. Instant unsubscribe at any time.</span>
                </div>
              </div>

            </form>
          )}

        </div>

      </div>
    </div>
  );
}
