'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { 
  ArrowRight, 
  ArrowLeft,
  CheckCircle2,
  Send, 
  Sparkles,
  Check
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';
import { useToast } from '@/components/ui/Toast';

const INDUSTRIES = [
  'Creative Agency',
  'Consulting & Advisory',
  'Commercial Studio & Media',
  'Real Estate & Brokerage',
  'Legal & Accounting',
  'Healthcare & Clinic',
  'Solo Specialist / Founder',
  'Other Professional Services'
];

const TOPICS = [
  'AI Proposals & Scoping',
  'Kanban Lead CRM',
  '18% GST Invoicing',
  'SHA-256 E-Signs',
  'Team Scheduling',
  'Enterprise & Custom API'
];

export default function ContactPage() {
  const { showToast } = useToast();
  const [step, setStep] = useState<1 | 2 | 3>(1);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const [formData, setFormData] = useState({
    name: '',
    email: '',
    companyName: '',
    industry: 'Creative Agency',
    selectedTopics: ['AI Proposals & Scoping', '18% GST Invoicing'] as string[],
    message: ''
  });

  const toggleTopic = (topic: string) => {
    setFormData(prev => ({
      ...prev,
      selectedTopics: prev.selectedTopics.includes(topic)
        ? prev.selectedTopics.filter(t => t !== topic)
        : [...prev.selectedTopics, topic]
    }));
  };

  const handleStep1Submit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.name.trim() || !formData.email.trim()) {
      showToast('Please enter your name and work email.');
      return;
    }
    setStep(2);
    trackEvent('contact_step_1_completed', { email: formData.email });
  };

  const handleFinalSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    setTimeout(() => {
      setIsSubmitting(false);
      setStep(3);
      showToast('Thank you! Your message has been sent.');
      trackEvent('contact_form_submitted', { 
        industry: formData.industry,
        topics: formData.selectedTopics 
      });
    }, 700);
  };

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-24 overflow-hidden bg-white text-zinc-900">
      <div className="w-full max-w-xl mx-auto px-4 sm:px-6">
        
        {/* ── Header ── */}
        <div className="text-center mb-8 sm:mb-10">
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 rounded-full border border-zinc-200/90 text-xs font-semibold text-zinc-800 mb-3 shadow-2xs">
            <span className="w-1.5 h-1.5 rounded-full bg-zinc-800" />
            <span>GET IN TOUCH</span>
          </div>

          <h1 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight mb-2">
            Let’s talk with our team
          </h1>

          <p className="text-zinc-500 text-xs sm:text-sm font-normal leading-relaxed max-w-md mx-auto">
            Have questions about Cora, need help choosing a plan, or exploring custom workflows? We usually reply within 2 hours.
          </p>
        </div>

        {/* ── Main Form Card ── */}
        <div className="bg-white border border-zinc-200/90 rounded-3xl p-6 sm:p-8 shadow-[0_16px_40px_rgba(0,0,0,0.04)]">
          
          {/* Step Progress Pill (Hidden on Success) */}
          {step < 3 && (
            <div className="flex items-center justify-between mb-6 pb-4 border-b border-zinc-100 text-xs text-zinc-400 font-mono">
              <span className="text-zinc-900 font-bold">STEP {step} OF 2</span>
              <span>{step === 1 ? '50%' : '100%'}</span>
            </div>
          )}

          {/* ═══════════════════════════════════════════════════════════
              STEP 1: BASIC INFORMATION
          ═══════════════════════════════════════════════════════════ */}
          {step === 1 && (
            <form onSubmit={handleStep1Submit} className="space-y-4 animate-in fade-in duration-150">
              <div>
                <label className="block text-xs font-medium text-zinc-700 mb-1.5">
                  Your Name *
                </label>
                <input
                  type="text"
                  required
                  placeholder="Alex Morgan"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  className="w-full bg-zinc-50/70 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                />
              </div>

              <div>
                <label className="block text-xs font-medium text-zinc-700 mb-1.5">
                  Work Email *
                </label>
                <input
                  type="email"
                  required
                  placeholder="name@company.com"
                  value={formData.email}
                  onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                  className="w-full bg-zinc-50/70 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                />
              </div>

              <div>
                <label className="block text-xs font-medium text-zinc-700 mb-1.5">
                  Company / Organization Name
                </label>
                <input
                  type="text"
                  placeholder="Acme Advisory"
                  value={formData.companyName}
                  onChange={(e) => setFormData({ ...formData, companyName: e.target.value })}
                  className="w-full bg-zinc-50/70 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                />
              </div>

              <div className="pt-2">
                <button
                  type="submit"
                  className="w-full inline-flex items-center justify-center gap-2 bg-zinc-950 text-white px-5 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm group"
                >
                  <span>Continue</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                </button>
              </div>
            </form>
          )}

          {/* ═══════════════════════════════════════════════════════════
              STEP 2: BUSINESS & REQUIREMENTS
          ═══════════════════════════════════════════════════════════ */}
          {step === 2 && (
            <form onSubmit={handleFinalSubmit} className="space-y-4 animate-in fade-in duration-150">
              <div>
                <label className="block text-xs font-medium text-zinc-700 mb-1.5">
                  Industry / Business Domain
                </label>
                <select
                  value={formData.industry}
                  onChange={(e) => setFormData({ ...formData, industry: e.target.value })}
                  className="w-full bg-zinc-50/70 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-sm text-zinc-950 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                >
                  {INDUSTRIES.map(item => (
                    <option key={item} value={item}>{item}</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-xs font-medium text-zinc-700 mb-1.5">
                  What are you interested in?
                </label>
                <div className="flex flex-wrap gap-1.5">
                  {TOPICS.map(topic => {
                    const isSelected = formData.selectedTopics.includes(topic);
                    return (
                      <button
                        type="button"
                        key={topic}
                        onClick={() => toggleTopic(topic)}
                        className={`px-3 py-1.5 rounded-lg text-xs font-medium border transition-all flex items-center gap-1.5 ${
                          isSelected
                            ? 'bg-zinc-950 text-white border-zinc-950 shadow-2xs'
                            : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                        }`}
                      >
                        {isSelected && <Check className="w-3 h-3 stroke-[3]" />}
                        <span>{topic}</span>
                      </button>
                    );
                  })}
                </div>
              </div>

              <div>
                <label className="block text-xs font-medium text-zinc-700 mb-1.5">
                  How can we help?
                </label>
                <textarea
                  rows={3}
                  placeholder="Tell us about your team's workflow or any questions..."
                  value={formData.message}
                  onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                  className="w-full bg-zinc-50/70 border border-zinc-200 rounded-xl p-3 text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                />
              </div>

              <div className="pt-2 flex items-center justify-between gap-3">
                <button
                  type="button"
                  onClick={() => setStep(1)}
                  className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 py-2 transition-colors"
                >
                  <ArrowLeft className="w-3.5 h-3.5" />
                  <span>Back</span>
                </button>

                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="inline-flex items-center justify-center gap-2 bg-zinc-950 text-white px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm disabled:opacity-50"
                >
                  {isSubmitting ? (
                    <span>Sending...</span>
                  ) : (
                    <>
                      <span>Send Message</span>
                      <Send className="w-3.5 h-3.5 text-zinc-400" />
                    </>
                  )}
                </button>
              </div>
            </form>
          )}

          {/* ═══════════════════════════════════════════════════════════
              STEP 3: SUCCESS CONFIRMATION
          ═══════════════════════════════════════════════════════════ */}
          {step === 3 && (
            <div className="py-6 text-center space-y-4 animate-in zoom-in-95 duration-150">
              <div className="w-12 h-12 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center mx-auto text-zinc-950 shadow-2xs">
                <CheckCircle2 className="w-6 h-6 text-zinc-950" />
              </div>

              <div className="space-y-1 max-w-sm mx-auto">
                <h2 className="font-display text-xl font-bold text-zinc-950">
                  Message Received!
                </h2>
                <p className="text-xs text-zinc-500 leading-relaxed">
                  Thank you, <strong>{formData.name}</strong>. Our team in Mumbai will review your note and respond to <strong>{formData.email}</strong> shortly.
                </p>
              </div>

              <div className="pt-3 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a
                  href="https://app.heycora.in/workspace/login?source=contact_done"
                  className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-zinc-950 text-white px-5 py-2.5 rounded-xl text-xs font-semibold hover:bg-zinc-800 transition-colors"
                >
                  <span>Launch Free Workspace</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>

                <Link
                  href="/demo"
                  className="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-zinc-700 bg-zinc-100 hover:bg-zinc-200 transition-colors"
                >
                  <span>View Live Demo</span>
                </Link>
              </div>

              <div className="pt-2">
                <button
                  type="button"
                  onClick={() => {
                    setStep(1);
                    setFormData({
                      name: '',
                      email: '',
                      companyName: '',
                      industry: 'Creative Agency',
                      selectedTopics: ['AI Proposals & Scoping', '18% GST Invoicing'],
                      message: ''
                    });
                  }}
                  className="text-xs text-zinc-400 hover:text-zinc-700 transition-colors underline underline-offset-2"
                >
                  Send another message
                </button>
              </div>
            </div>
          )}

        </div>

        {/* ── Minimal Sub-Bar (No Heavy Cards) ── */}
        <div className="mt-8 text-center text-xs text-zinc-400 space-y-2">
          <div className="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 font-mono text-[11px]">
            <span>Support: <a href="mailto:support@heycora.in" className="text-zinc-600 underline">support@heycora.in</a></span>
            <span>&bull;</span>
            <span>Enterprise: <a href="mailto:sales@heycora.in" className="text-zinc-600 underline">sales@heycora.in</a></span>
            <span>&bull;</span>
            <span>Mumbai, India</span>
          </div>
          <p className="text-[11px] text-zinc-400">
            Cora Platforms &bull; UDYAM Registered MSME &bull; Indian IT Act 2000 Compliant
          </p>
        </div>

      </div>
    </main>
  );
}
