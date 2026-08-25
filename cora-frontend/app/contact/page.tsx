'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { Mail, MessageSquare, ArrowRight, ShieldCheck, Clock, MapPin, Send, Sparkles, Headphones, Building2, CheckCircle2 } from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';
import { useToast } from '@/components/ui/Toast';

export default function ContactPage() {
  const { showToast } = useToast();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    companyName: '',
    industry: 'Creative Agency',
    teamSize: '1-5 members',
    inquiryType: 'Product Demo & Sales',
    message: ''
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    setTimeout(() => {
      setIsSubmitting(false);
      showToast('Thank you! Your message has been received. Our team will reply within 2-4 hours.');
      trackEvent('contact_form_submitted', { 
        industry: formData.industry,
        inquiryType: formData.inquiryType 
      });
      setFormData({
        name: '',
        email: '',
        companyName: '',
        industry: 'Creative Agency',
        teamSize: '1-5 members',
        inquiryType: 'Product Demo & Sales',
        message: ''
      });
    }, 800);
  };

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-24 overflow-hidden bg-[#FBFaf7] text-zinc-900">
      
      {/* ── Top Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-16 sm:mb-20">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white rounded-full border border-zinc-200/90 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
          <span>CONTACT &amp; SALES DESK</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[840px] mx-auto mb-5">
          Get in touch with our team
        </h1>

        <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[640px] mx-auto">
          Have questions about Cora for your professional services business, need enterprise deployment assistance, or want to discuss custom workflows? We&apos;re here to help.
        </p>
      </section>

      {/* ── Contact Department Cards & Form Grid ── */}
      <section className="w-full max-w-[1180px] mx-auto px-4 sm:px-6">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
          
          {/* Left Column: SaaS Department Channels & Quick Access */}
          <div className="lg:col-span-5 space-y-4">
            
            {/* Department Card 1: Sales & Demos */}
            <div className="p-5 sm:p-6 rounded-2xl bg-white border border-zinc-200/90 shadow-2xs space-y-3">
              <div className="flex items-center gap-3">
                <div className="w-9 h-9 rounded-xl bg-zinc-100 text-zinc-900 flex items-center justify-center border border-zinc-200/70 shrink-0">
                  <Building2 className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-sm font-bold text-zinc-950">Sales &amp; Enterprise</h3>
                  <p className="text-xs text-zinc-500">Volume tiers, custom AI pipelines &amp; onboarding</p>
                </div>
              </div>
              <div className="pt-1 flex items-center justify-between text-xs">
                <a href="mailto:sales@heycora.in" className="font-mono text-zinc-900 hover:text-emerald-700 font-semibold underline underline-offset-2">
                  sales@heycora.in
                </a>
                <span className="text-zinc-400 font-mono text-[11px]">&lt; 2 hr response</span>
              </div>
            </div>

            {/* Department Card 2: Technical Support */}
            <div className="p-5 sm:p-6 rounded-2xl bg-white border border-zinc-200/90 shadow-2xs space-y-3">
              <div className="flex items-center gap-3">
                <div className="w-9 h-9 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center border border-sky-200/70 shrink-0">
                  <Headphones className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-sm font-bold text-zinc-950">Customer Support</h3>
                  <p className="text-xs text-zinc-500">Workspace troubleshooting, billing &amp; integrations</p>
                </div>
              </div>
              <div className="pt-1 flex items-center justify-between text-xs">
                <a href="mailto:support@heycora.in" className="font-mono text-zinc-900 hover:text-emerald-700 font-semibold underline underline-offset-2">
                  support@heycora.in
                </a>
                <span className="text-zinc-400 font-mono text-[11px]">24/7 priority</span>
              </div>
            </div>

            {/* Department Card 3: Security & Legal */}
            <div className="p-5 sm:p-6 rounded-2xl bg-white border border-zinc-200/90 shadow-2xs space-y-3">
              <div className="flex items-center gap-3">
                <div className="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200/70 shrink-0">
                  <ShieldCheck className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-sm font-bold text-zinc-950">Security &amp; Compliance</h3>
                  <p className="text-xs text-zinc-500">SOC-2, DPDP Act 2023, DPAs &amp; vulnerability reports</p>
                </div>
              </div>
              <div className="pt-1 flex items-center justify-between text-xs">
                <a href="mailto:security@heycora.in" className="font-mono text-zinc-900 hover:text-emerald-700 font-semibold underline underline-offset-2">
                  security@heycora.in
                </a>
                <span className="text-zinc-400 font-mono text-[11px]">Direct escalation</span>
              </div>
            </div>

            {/* Instant Self-Serve Workspace Banner */}
            <div className="p-6 rounded-2xl bg-zinc-950 text-white space-y-3 shadow-md">
              <div className="flex items-center justify-between">
                <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-emerald-400">
                  INSTANT ACCESS
                </span>
                <Sparkles className="w-4 h-4 text-emerald-400" />
              </div>
              <h4 className="font-display text-base font-bold text-white">
                Want to test Cora right now?
              </h4>
              <p className="text-xs text-zinc-400 leading-relaxed font-normal">
                Skip the contact form and launch your workspace in 30 seconds. Includes 1,000 complimentary AI runs.
              </p>
              <a
                href="https://app.heycora.in/workspace/login?source=contact_card"
                className="inline-flex items-center gap-2 text-xs font-bold text-white bg-zinc-800 hover:bg-zinc-700 px-4 py-2 rounded-xl transition-colors mt-1"
              >
                <span>Launch Free Workspace</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>
            </div>

            {/* Legal Entity & Office Address */}
            <div className="p-4 rounded-xl bg-zinc-100/80 border border-zinc-200/80 text-[11px] text-zinc-500 space-y-1 font-mono">
              <div className="text-zinc-900 font-bold">Cora Platforms</div>
              <div>UDYAM Registered MSME &bull; Govt. of India</div>
              <div>Bengaluru &amp; Mumbai, India</div>
            </div>

          </div>

          {/* Right Column: Professional SaaS Contact Form */}
          <div className="lg:col-span-7 bg-white border border-zinc-200/90 rounded-[28px] p-6 sm:p-9 shadow-sm">
            <div className="mb-6 pb-5 border-b border-zinc-100">
              <h2 className="font-display text-xl font-bold text-zinc-950 mb-1">
                Send us a message
              </h2>
              <p className="text-xs text-zinc-500 font-normal">
                Fill out the details below and our solutions team will respond promptly.
              </p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-4">
              
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-1.5 font-mono">
                    Full Name
                  </label>
                  <input
                    type="text"
                    required
                    placeholder="Shruti Anand"
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                  />
                </div>

                <div>
                  <label className="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-1.5 font-mono">
                    Work Email
                  </label>
                  <input
                    type="email"
                    required
                    placeholder="shruti@agency.com"
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-1.5 font-mono">
                    Company / Business Name
                  </label>
                  <input
                    type="text"
                    required
                    placeholder="Acme Global Services"
                    value={formData.companyName}
                    onChange={(e) => setFormData({ ...formData, companyName: e.target.value })}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                  />
                </div>

                <div>
                  <label className="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-1.5 font-mono">
                    Industry / Business Type
                  </label>
                  <select
                    value={formData.industry}
                    onChange={(e) => setFormData({ ...formData, industry: e.target.value })}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-zinc-950 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                  >
                    <option>Creative Agency</option>
                    <option>Consulting &amp; Advisory</option>
                    <option>Commercial Studio &amp; Media</option>
                    <option>Real Estate &amp; Brokerage</option>
                    <option>Legal &amp; Accounting Advisory</option>
                    <option>Healthcare &amp; Clinic</option>
                    <option>Independent Professional / Founder</option>
                    <option>Other Professional Services</option>
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-1.5 font-mono">
                    Team Size
                  </label>
                  <select
                    value={formData.teamSize}
                    onChange={(e) => setFormData({ ...formData, teamSize: e.target.value })}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-zinc-950 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                  >
                    <option>1-5 members</option>
                    <option>6-15 members</option>
                    <option>16-50 members</option>
                    <option>50+ members</option>
                  </select>
                </div>

                <div>
                  <label className="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-1.5 font-mono">
                    Inquiry Type
                  </label>
                  <select
                    value={formData.inquiryType}
                    onChange={(e) => setFormData({ ...formData, inquiryType: e.target.value })}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-zinc-950 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                  >
                    <option>Product Demo &amp; Sales</option>
                    <option>Custom Enterprise Deployment</option>
                    <option>Billing &amp; Plan Inquiries</option>
                    <option>Technical Support</option>
                    <option>Partnership &amp; Integrations</option>
                    <option>General Question</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-1.5 font-mono">
                  How can we help your organization?
                </label>
                <textarea
                  rows={4}
                  required
                  placeholder="Tell us about your team's requirements, workflow challenges, or questions..."
                  value={formData.message}
                  onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                  className="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-3.5 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                />
              </div>

              <div className="pt-2">
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="w-full inline-flex items-center justify-center gap-2 bg-zinc-950 text-white px-6 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm disabled:opacity-50"
                >
                  {isSubmitting ? (
                    <span>Sending message...</span>
                  ) : (
                    <>
                      <span>Send Message</span>
                      <Send className="w-3.5 h-3.5 text-zinc-400" />
                    </>
                  )}
                </button>
              </div>

              <p className="text-center text-[11px] text-zinc-400 font-mono pt-1">
                Zero spam guaranteed &bull; Governed by our <Link href="/privacy" className="underline hover:text-zinc-600">Privacy Policy</Link>
              </p>

            </form>
          </div>

        </div>
      </section>

    </main>
  );
}
