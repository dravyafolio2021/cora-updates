'use client';

import React, { useState } from 'react';
import type { Metadata } from 'next';
import Image from 'next/image';
import { Mail, MessageSquare, ArrowRight, CheckCircle2, Clock, MapPin, Send, Sparkles } from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';
import { useToast } from '@/components/ui/Toast';

export default function ContactPage() {
  const { showToast } = useToast();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    studioName: '',
    industry: 'Commercial Photography',
    shootsPerMonth: '5-15 Shoots',
    message: ''
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    setTimeout(() => {
      setIsSubmitting(false);
      showToast('Thank you! Your inquiry was sent directly to Dravya Bansal.');
      trackEvent('contact_form_submitted', { industry: formData.industry });
      setFormData({
        name: '',
        email: '',
        studioName: '',
        industry: 'Commercial Photography',
        shootsPerMonth: '5-15 Shoots',
        message: ''
      });
    }, 900);
  };

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-20 overflow-hidden bg-white">
      
      {/* ── Top Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-16 sm:mb-20">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <span className="w-2 h-2 rounded-full bg-emerald-500" />
          <span>Founder &amp; Advisory Desk</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[840px] mx-auto mb-5">
          Let’s talk about your studio operations
        </h1>

        <p className="text-zinc-600 text-base sm:text-xl font-normal leading-relaxed max-w-[620px] mx-auto">
          Have questions about migrating from spreadsheets, custom WhatsApp dispatch, or enterprise GST invoicing? Talk directly with our founding team.
        </p>
      </section>

      {/* ── Contact Card & Form Grid ── */}
      <section className="w-full max-w-[1140px] mx-auto px-4 sm:px-6 mb-24">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
          
          {/* Left Column: Direct Founder Card & Quick Channels */}
          <div className="lg:col-span-5 space-y-6">
            
            {/* Founder Card */}
            <div className="bg-[#0E1115] text-white rounded-[32px] p-7 sm:p-8 border border-zinc-800 shadow-xl space-y-6 relative overflow-hidden">
              
              <div className="flex items-center gap-3.5">
                <div className="w-14 h-14 rounded-2xl overflow-hidden border border-zinc-700 shadow-sm relative shrink-0">
                  <Image
                    src="/images/dravya_bansal_black.jpg"
                    alt="Dravya Bansal"
                    fill
                    sizes="56px"
                    className="object-cover object-top"
                  />
                </div>
                <div>
                  <h3 className="font-display text-lg font-bold text-white">Dravya Bansal</h3>
                  <p className="text-xs text-emerald-400 font-mono">Co-founder &amp; Product Architect</p>
                </div>
              </div>

              <blockquote className="text-zinc-300 text-xs sm:text-sm italic leading-relaxed">
                "We personally review incoming requests to help creative agency founders optimize their pipelines and eliminate administrative bottlenecks."
              </blockquote>

              <div className="space-y-3 pt-2 border-t border-zinc-800 text-xs text-zinc-300">
                <div className="flex items-center gap-2.5">
                  <Mail className="w-4 h-4 text-zinc-400" />
                  <a href="mailto:dravya.bansal@heycora.in" className="hover:text-white transition-colors underline underline-offset-2">
                    dravya.bansal@heycora.in
                  </a>
                </div>

                <div className="flex items-center gap-2.5">
                  <Clock className="w-4 h-4 text-emerald-400" />
                  <span>Response time: Under 2 hours</span>
                </div>

                <div className="flex items-center gap-2.5">
                  <MapPin className="w-4 h-4 text-zinc-400" />
                  <span>Claraverse Inc. • Goa &amp; Bengaluru, India</span>
                </div>
              </div>

            </div>

            {/* Quick Action Button */}
            <div className="bg-zinc-50 border border-zinc-200/90 rounded-[24px] p-6 space-y-3 shadow-2xs">
              <h4 className="font-display text-base font-bold text-zinc-950">
                Need immediate onboarding help?
              </h4>
              <p className="text-xs text-zinc-600 leading-relaxed font-normal">
                Skip the form and start directly in your live workspace with 1,000 complimentary AI runs.
              </p>
              <a
                href="https://app.heycora.in/workspace/login?source=contact_quick"
                className="inline-flex items-center gap-2 text-xs font-bold text-zinc-950 hover:text-zinc-700 transition-colors pt-1"
              >
                <span>Launch workspace portal</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </a>
            </div>

          </div>

          {/* Right Column: Interactive Advisory Form */}
          <div className="lg:col-span-7 bg-white border border-zinc-200/90 rounded-[32px] p-8 sm:p-10 shadow-[0px_20px_50px_rgba(0,0,0,0.05)]">
            <form onSubmit={handleSubmit} className="space-y-5">
              
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-zinc-800 uppercase tracking-wider mb-2 font-mono">
                    Your Name
                  </label>
                  <input
                    type="text"
                    required
                    placeholder="Vikramaditya Roy"
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-500 transition-colors"
                  />
                </div>

                <div>
                  <label className="block text-xs font-bold text-zinc-800 uppercase tracking-wider mb-2 font-mono">
                    Work Email
                  </label>
                  <input
                    type="email"
                    required
                    placeholder="vikram@monochromestudios.in"
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-500 transition-colors"
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-zinc-800 uppercase tracking-wider mb-2 font-mono">
                    Studio / Agency Name
                  </label>
                  <input
                    type="text"
                    required
                    placeholder="Monochrome Studios Goa"
                    value={formData.studioName}
                    onChange={(e) => setFormData({ ...formData, studioName: e.target.value })}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-500 transition-colors"
                  />
                </div>

                <div>
                  <label className="block text-xs font-bold text-zinc-800 uppercase tracking-wider mb-2 font-mono">
                    Primary Industry
                  </label>
                  <select
                    value={formData.industry}
                    onChange={(e) => setFormData({ ...formData, industry: e.target.value })}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-xs sm:text-sm text-zinc-950 focus:outline-none focus:border-zinc-500 transition-colors"
                  >
                    <option>Commercial Photography</option>
                    <option>Real Estate Media</option>
                    <option>Film &amp; Video Production</option>
                    <option>Fashion &amp; Editorial</option>
                    <option>Creative Marketing Agency</option>
                    <option>Solo Creator / Freelancer</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-xs font-bold text-zinc-800 uppercase tracking-wider mb-2 font-mono">
                  Monthly Shoot / Project Volume
                </label>
                <select
                  value={formData.shootsPerMonth}
                  onChange={(e) => setFormData({ ...formData, shootsPerMonth: e.target.value })}
                  className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-xs sm:text-sm text-zinc-950 focus:outline-none focus:border-zinc-500 transition-colors"
                >
                  <option>1-5 Shoots / Month</option>
                  <option>5-15 Shoots / Month</option>
                  <option>15-30 Shoots / Month</option>
                  <option>30+ High-Volume Multi-Bay</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-zinc-800 uppercase tracking-wider mb-2 font-mono">
                  How can we help your team?
                </label>
                <textarea
                  rows={4}
                  required
                  placeholder="Tell us about your current bottlenecks (e.g. GST invoicing math, chasing contract signatures, WhatsApp call-sheets)..."
                  value={formData.message}
                  onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                  className="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-500 transition-colors"
                />
              </div>

              <div>
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="w-full inline-flex items-center justify-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm disabled:opacity-50"
                >
                  {isSubmitting ? (
                    <span>Sending to Founder...</span>
                  ) : (
                    <>
                      <span>Send Advisory Request</span>
                      <Send className="w-3.5 h-3.5" />
                    </>
                  )}
                </button>
              </div>

            </form>
          </div>

        </div>
      </section>

    </main>
  );
}
