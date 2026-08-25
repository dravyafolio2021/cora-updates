'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { 
  Mail, 
  MessageSquare, 
  ArrowRight, 
  ArrowLeft,
  ShieldCheck, 
  Clock, 
  MapPin, 
  Send, 
  Sparkles, 
  Headphones, 
  Building2, 
  CheckCircle2,
  Palette,
  Briefcase,
  Camera,
  Scale,
  Stethoscope,
  User,
  Zap,
  Kanban,
  Receipt,
  FileCheck,
  Calendar,
  Layers,
  Check,
  ChevronRight
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';
import { useToast } from '@/components/ui/Toast';

const INDUSTRIES = [
  { id: 'agency', title: 'Creative & Digital Agency', desc: 'Design, marketing & tech services', icon: Palette },
  { id: 'consulting', title: 'Consulting & Advisory', desc: 'Management, strategy & financial', icon: Briefcase },
  { id: 'studio', title: 'Commercial Studio & Media', desc: 'Photo, film & production houses', icon: Camera },
  { id: 'realestate', title: 'Real Estate & Brokerage', desc: 'Property firms & asset brokers', icon: Building2 },
  { id: 'legal', title: 'Legal, Tax & Accounting', desc: 'Law practices & financial advisory', icon: Scale },
  { id: 'healthcare', title: 'Healthcare & Wellness', desc: 'Clinics & specialized practices', icon: Stethoscope },
  { id: 'solo', title: 'Solo Founder / Specialist', desc: 'Independent service operators', icon: User },
  { id: 'other', title: 'Other Professional Services', desc: 'Corporate & specialized b2b', icon: Layers },
];

const WORKFLOW_REQUIREMENTS = [
  { id: 'ai_scoping', title: 'Autonomous AI Proposals', icon: Zap },
  { id: 'crm', title: 'Kanban CRM & Lead Funnel', icon: Kanban },
  { id: 'gst', title: '18% GST Invoicing & Math', icon: Receipt },
  { id: 'esign', title: 'SHA-256 E-Sign Vault', icon: FileCheck },
  { id: 'schedule', title: 'Team & Resource Calendar', icon: Calendar },
  { id: 'whatsapp', title: 'WhatsApp Client Updates', icon: MessageSquare },
  { id: 'enterprise', title: 'Enterprise Security & SLA', icon: ShieldCheck },
  { id: 'integrations', title: 'Website Embeds & API', icon: Sparkles },
];

const TEAM_SIZES = ['1-5 members', '6-15 members', '16-50 members', '50+ members'];
const REVENUE_BRACKETS = ['Under ₹5L/mo', '₹5L – ₹25L/mo', '₹25L – ₹1Cr/mo', '₹1Cr+/mo Enterprise'];

export default function ContactPage() {
  const { showToast } = useToast();
  const [currentStep, setCurrentStep] = useState<1 | 2 | 3 | 4>(1);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [ticketNumber, setTicketNumber] = useState('');

  const [formData, setFormData] = useState({
    industry: 'agency',
    teamSize: '1-5 members',
    selectedWorkflows: ['ai_scoping', 'crm', 'gst'] as string[],
    revenueBracket: '₹5L – ₹25L/mo',
    name: '',
    email: '',
    companyName: '',
    phone: '',
    message: ''
  });

  const toggleWorkflow = (id: string) => {
    setFormData(prev => ({
      ...prev,
      selectedWorkflows: prev.selectedWorkflows.includes(id)
        ? prev.selectedWorkflows.filter(w => w !== id)
        : [...prev.selectedWorkflows, id]
    }));
  };

  const handleNextStep = (e: React.FormEvent) => {
    e.preventDefault();
    if (currentStep === 1) {
      setCurrentStep(2);
      trackEvent('contact_step_1_completed', { industry: formData.industry, teamSize: formData.teamSize });
    } else if (currentStep === 2) {
      if (formData.selectedWorkflows.length === 0) {
        showToast('Please select at least one workflow requirement.');
        return;
      }
      setCurrentStep(3);
      trackEvent('contact_step_2_completed', { workflows: formData.selectedWorkflows });
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.name || !formData.email || !formData.companyName) {
      showToast('Please complete all required contact fields.');
      return;
    }

    setIsSubmitting(true);
    const ref = `CORA-${Math.floor(1000 + Math.random() * 9000)}`;

    setTimeout(() => {
      setIsSubmitting(false);
      setTicketNumber(ref);
      setCurrentStep(4);
      showToast(`Inquiry received! Reference ID: ${ref}`);
      trackEvent('contact_form_submitted', { 
        ref,
        industry: formData.industry,
        teamSize: formData.teamSize,
        workflows: formData.selectedWorkflows
      });
    }, 850);
  };

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-24 overflow-hidden bg-white text-zinc-900">
      
      {/* ── Top Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-12 sm:mb-16">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-full border border-zinc-200/90 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <span className="w-1.5 h-1.5 rounded-full bg-zinc-800" />
          <span>SOLUTIONS &amp; SALES DESK</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[840px] mx-auto mb-4">
          Let’s build your autonomous workspace
        </h1>

        <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[620px] mx-auto">
          Tell us about your organization’s workflow bottlenecks. Our solutions team in Mumbai will prepare a tailored deployment architecture.
        </p>
      </section>

      {/* ── Main Multi-Step Interactive Container Grid ── */}
      <section className="w-full max-w-[1200px] mx-auto px-4 sm:px-6">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
          
          {/* Left Column: Direct Department Channels & Corporate Card */}
          <div className="lg:col-span-4 space-y-4">
            
            {/* Sales & Enterprise */}
            <div className="p-5 rounded-2xl bg-zinc-50/70 border border-zinc-200/90 shadow-2xs space-y-2.5">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-xl bg-white text-zinc-900 flex items-center justify-center border border-zinc-200/80 shrink-0">
                  <Building2 className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-xs font-bold text-zinc-950">Enterprise &amp; Sales</h3>
                  <p className="text-[11px] text-zinc-500">Custom AI quotas &amp; volume tiers</p>
                </div>
              </div>
              <div className="flex items-center justify-between text-xs pt-1 border-t border-zinc-200/60">
                <a href="mailto:sales@heycora.in" className="font-mono text-zinc-900 hover:text-zinc-600 font-semibold underline underline-offset-2">
                  sales@heycora.in
                </a>
                <span className="text-zinc-400 font-mono text-[10px]">&lt; 2 hr SLA</span>
              </div>
            </div>

            {/* Support Desk */}
            <div className="p-5 rounded-2xl bg-zinc-50/70 border border-zinc-200/90 shadow-2xs space-y-2.5">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-xl bg-white text-zinc-900 flex items-center justify-center border border-zinc-200/80 shrink-0">
                  <Headphones className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-xs font-bold text-zinc-950">Client Support Desk</h3>
                  <p className="text-[11px] text-zinc-500">Workspace migrations &amp; technical help</p>
                </div>
              </div>
              <div className="flex items-center justify-between text-xs pt-1 border-t border-zinc-200/60">
                <a href="mailto:support@heycora.in" className="font-mono text-zinc-900 hover:text-zinc-600 font-semibold underline underline-offset-2">
                  support@heycora.in
                </a>
                <span className="text-zinc-400 font-mono text-[10px]">24/7 Priority</span>
              </div>
            </div>

            {/* Security & DPA */}
            <div className="p-5 rounded-2xl bg-zinc-50/70 border border-zinc-200/90 shadow-2xs space-y-2.5">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-xl bg-white text-zinc-900 flex items-center justify-center border border-zinc-200/80 shrink-0">
                  <ShieldCheck className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-xs font-bold text-zinc-950">Security &amp; Legal</h3>
                  <p className="text-[11px] text-zinc-500">DPDP Act 2023, DPAs &amp; audit trails</p>
                </div>
              </div>
              <div className="flex items-center justify-between text-xs pt-1 border-t border-zinc-200/60">
                <a href="mailto:security@heycora.in" className="font-mono text-zinc-900 hover:text-zinc-600 font-semibold underline underline-offset-2">
                  security@heycora.in
                </a>
                <span className="text-zinc-400 font-mono text-[10px]">Direct Desk</span>
              </div>
            </div>

            {/* Instant Self-Serve Workspace Banner (Strict Monochrome) */}
            <div className="p-6 rounded-2xl bg-zinc-950 text-white space-y-3 border border-zinc-800 shadow-sm">
              <div className="flex items-center justify-between">
                <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">
                  INSTANT START
                </span>
                <Sparkles className="w-4 h-4 text-zinc-400" />
              </div>
              <h4 className="font-display text-sm font-bold text-white leading-snug">
                Want to test Cora right now?
              </h4>
              <p className="text-xs text-zinc-400 leading-relaxed font-normal">
                Skip the form and launch your workspace in 30 seconds with 1,000 complimentary AI runs.
              </p>
              <a
                href="https://app.heycora.in/workspace/login?source=contact_flow"
                className="inline-flex items-center gap-2 text-xs font-bold text-white bg-zinc-800 hover:bg-zinc-700 px-4 py-2 rounded-xl transition-colors mt-1"
              >
                <span>Launch Free Workspace</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>
            </div>

            {/* Office & Legal Registration */}
            <div className="p-4 rounded-xl bg-zinc-50 border border-zinc-200/80 text-[11px] text-zinc-500 space-y-1 font-mono">
              <div className="text-zinc-900 font-bold">Cora Platforms</div>
              <div>UDYAM Registered MSME &bull; Govt. of India</div>
              <div>Mumbai, India</div>
            </div>

          </div>

          {/* Right Column: Multi-Step Interactive SaaS Form */}
          <div className="lg:col-span-8 bg-white border border-zinc-200 rounded-[32px] p-6 sm:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.04)]">
            
            {/* ── Stepper Header Bar ── */}
            {currentStep < 4 && (
              <div className="mb-8 pb-6 border-b border-zinc-100">
                <div className="flex items-center justify-between gap-4 mb-3">
                  <div className="flex items-center gap-2">
                    <span className="text-xs font-mono font-bold text-zinc-950 uppercase tracking-wider">
                      STEP {currentStep} OF 3
                    </span>
                    <span className="text-xs text-zinc-400">&bull;</span>
                    <span className="text-xs font-semibold text-zinc-600">
                      {currentStep === 1 && 'Industry & Team'}
                      {currentStep === 2 && 'Workflow Requirements'}
                      {currentStep === 3 && 'Contact & Message'}
                    </span>
                  </div>
                  <span className="text-xs font-mono font-semibold text-zinc-400">
                    {currentStep === 1 && '33%'}
                    {currentStep === 2 && '66%'}
                    {currentStep === 3 && '100%'}
                  </span>
                </div>

                {/* Progress Bar Track */}
                <div className="w-full h-1.5 rounded-full bg-zinc-100 overflow-hidden">
                  <div 
                    className="h-full bg-zinc-950 rounded-full transition-all duration-300 ease-out"
                    style={{ width: currentStep === 1 ? '33%' : currentStep === 2 ? '66%' : '100%' }}
                  />
                </div>
              </div>
            )}

            {/* ══════════════════════════════════════════════════════════════════
                STEP 1: BUSINESS TYPE & TEAM SIZE
            ══════════════════════════════════════════════════════════════════ */}
            {currentStep === 1 && (
              <form onSubmit={handleNextStep} className="space-y-6 animate-in fade-in duration-200">
                <div>
                  <h2 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 mb-1">
                    What type of business do you operate?
                  </h2>
                  <p className="text-xs sm:text-sm text-zinc-500 font-normal">
                    Select the domain that best describes your professional services organization.
                  </p>
                </div>

                {/* Industry Cards Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  {INDUSTRIES.map(item => {
                    const Icon = item.icon;
                    const isSelected = formData.industry === item.id;
                    return (
                      <button
                        type="button"
                        key={item.id}
                        onClick={() => setFormData({ ...formData, industry: item.id })}
                        className={`p-3.5 rounded-2xl border text-left flex items-start gap-3 transition-all ${
                          isSelected
                            ? 'border-zinc-950 bg-zinc-950 text-white shadow-sm'
                            : 'border-zinc-200 bg-white text-zinc-900 hover:border-zinc-300 hover:bg-zinc-50/70'
                        }`}
                      >
                        <div className={`w-8 h-8 rounded-xl flex items-center justify-center shrink-0 border ${
                          isSelected ? 'bg-zinc-800 text-white border-zinc-700' : 'bg-zinc-100 text-zinc-900 border-zinc-200/80'
                        }`}>
                          <Icon className="w-4 h-4" />
                        </div>
                        <div className="space-y-0.5 min-w-0 flex-1">
                          <div className="text-xs font-bold truncate">{item.title}</div>
                          <div className={`text-[11px] leading-tight ${isSelected ? 'text-zinc-300' : 'text-zinc-500'}`}>
                            {item.desc}
                          </div>
                        </div>
                        {isSelected && (
                          <div className="w-4 h-4 rounded-full bg-white text-zinc-950 flex items-center justify-center shrink-0 mt-0.5">
                            <Check className="w-2.5 h-2.5 stroke-[3]" />
                          </div>
                        )}
                      </button>
                    );
                  })}
                </div>

                {/* Team Size Segmented Control */}
                <div className="space-y-2 pt-2">
                  <label className="block text-xs font-mono font-bold text-zinc-700 uppercase tracking-wider">
                    Team Size
                  </label>
                  <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    {TEAM_SIZES.map(size => {
                      const isSelected = formData.teamSize === size;
                      return (
                        <button
                          type="button"
                          key={size}
                          onClick={() => setFormData({ ...formData, teamSize: size })}
                          className={`py-2.5 px-3 rounded-xl text-xs font-semibold border transition-all ${
                            isSelected
                              ? 'bg-zinc-950 text-white border-zinc-950 shadow-2xs'
                              : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                          }`}
                        >
                          {size}
                        </button>
                      );
                    })}
                  </div>
                </div>

                {/* Bottom Stepper CTA */}
                <div className="pt-4 flex justify-end">
                  <button
                    type="submit"
                    className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm group"
                  >
                    <span>Continue to Requirements</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                  </button>
                </div>
              </form>
            )}

            {/* ══════════════════════════════════════════════════════════════════
                STEP 2: WORKFLOW REQUIREMENTS & REVENUE SCALE
            ══════════════════════════════════════════════════════════════════ */}
            {currentStep === 2 && (
              <form onSubmit={handleNextStep} className="space-y-6 animate-in fade-in duration-200">
                <div>
                  <h2 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 mb-1">
                    What workflows are you looking to automate?
                  </h2>
                  <p className="text-xs sm:text-sm text-zinc-500 font-normal">
                    Select all the modules and operational capabilities your business needs.
                  </p>
                </div>

                {/* Multi-Select Interactive Feature Pills */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  {WORKFLOW_REQUIREMENTS.map(item => {
                    const Icon = item.icon;
                    const isSelected = formData.selectedWorkflows.includes(item.id);
                    return (
                      <button
                        type="button"
                        key={item.id}
                        onClick={() => toggleWorkflow(item.id)}
                        className={`p-3.5 rounded-2xl border text-left flex items-center justify-between transition-all ${
                          isSelected
                            ? 'border-zinc-950 bg-zinc-950 text-white shadow-2xs'
                            : 'border-zinc-200 bg-white text-zinc-800 hover:border-zinc-300 hover:bg-zinc-50/70'
                        }`}
                      >
                        <div className="flex items-center gap-3">
                          <div className={`w-7 h-7 rounded-lg flex items-center justify-center shrink-0 border ${
                            isSelected ? 'bg-zinc-800 text-white border-zinc-700' : 'bg-zinc-100 text-zinc-800 border-zinc-200/80'
                          }`}>
                            <Icon className="w-3.5 h-3.5" />
                          </div>
                          <span className="text-xs font-bold">{item.title}</span>
                        </div>
                        
                        <div className={`w-4 h-4 rounded-full border flex items-center justify-center shrink-0 ${
                          isSelected ? 'bg-white border-white text-zinc-950' : 'border-zinc-300 bg-zinc-50'
                        }`}>
                          {isSelected && <Check className="w-2.5 h-2.5 stroke-[3]" />}
                        </div>
                      </button>
                    );
                  })}
                </div>

                {/* Revenue / Scale Segmented Control */}
                <div className="space-y-2 pt-2">
                  <label className="block text-xs font-mono font-bold text-zinc-700 uppercase tracking-wider">
                    Monthly Operational Scale
                  </label>
                  <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    {REVENUE_BRACKETS.map(bracket => {
                      const isSelected = formData.revenueBracket === bracket;
                      return (
                        <button
                          type="button"
                          key={bracket}
                          onClick={() => setFormData({ ...formData, revenueBracket: bracket })}
                          className={`py-2.5 px-3 rounded-xl text-xs font-semibold border transition-all text-center ${
                            isSelected
                              ? 'bg-zinc-950 text-white border-zinc-950 shadow-2xs'
                              : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                          }`}
                        >
                          {bracket}
                        </button>
                      );
                    })}
                  </div>
                </div>

                {/* Bottom Stepper CTAs */}
                <div className="pt-4 flex items-center justify-between">
                  <button
                    type="button"
                    onClick={() => setCurrentStep(1)}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-600 hover:text-zinc-950 transition-colors"
                  >
                    <ArrowLeft className="w-3.5 h-3.5" />
                    <span>Back</span>
                  </button>

                  <button
                    type="submit"
                    className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm group"
                  >
                    <span>Continue to Contact Info</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                  </button>
                </div>
              </form>
            )}

            {/* ══════════════════════════════════════════════════════════════════
                STEP 3: CONTACT INFORMATION & MESSAGE
            ══════════════════════════════════════════════════════════════════ */}
            {currentStep === 3 && (
              <form onSubmit={handleSubmit} className="space-y-5 animate-in fade-in duration-200">
                <div>
                  <h2 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 mb-1">
                    Where should we send your workspace proposal?
                  </h2>
                  <p className="text-xs sm:text-sm text-zinc-500 font-normal">
                    Enter your contact details so our solutions desk can reply directly.
                  </p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-1.5 font-mono">
                      Your Name *
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
                      Work Email *
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
                      Company / Organization *
                    </label>
                    <input
                      type="text"
                      required
                      placeholder="Acme Global Advisory"
                      value={formData.companyName}
                      onChange={(e) => setFormData({ ...formData, companyName: e.target.value })}
                      className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-1.5 font-mono">
                      Phone / WhatsApp (Optional)
                    </label>
                    <input
                      type="tel"
                      placeholder="+91 98200 XXXXX"
                      value={formData.phone}
                      onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                      className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-1.5 font-mono">
                    Project Notes &amp; Specific Questions
                  </label>
                  <textarea
                    rows={3}
                    placeholder="Tell us about specific integrations, timelines, or custom workflow requirements..."
                    value={formData.message}
                    onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-3.5 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-950 focus:bg-white transition-colors"
                  />
                </div>

                {/* Bottom Submit CTAs */}
                <div className="pt-4 flex items-center justify-between">
                  <button
                    type="button"
                    onClick={() => setCurrentStep(2)}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-600 hover:text-zinc-950 transition-colors"
                  >
                    <ArrowLeft className="w-3.5 h-3.5" />
                    <span>Back</span>
                  </button>

                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className="inline-flex items-center gap-2 bg-zinc-950 text-white px-7 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm disabled:opacity-50"
                  >
                    {isSubmitting ? (
                      <span>Sending inquiry...</span>
                    ) : (
                      <>
                        <span>Submit Inquiry</span>
                        <Send className="w-3.5 h-3.5 text-zinc-400" />
                      </>
                    )}
                  </button>
                </div>

                <p className="text-center text-[11px] text-zinc-400 font-mono pt-1">
                  Protected by enterprise encryption &bull; Governed by our <Link href="/privacy" className="underline hover:text-zinc-600">Privacy Policy</Link>
                </p>
              </form>
            )}

            {/* ══════════════════════════════════════════════════════════════════
                STEP 4: INTERACTIVE SUCCESS CONFIRMATION SCREEN
            ══════════════════════════════════════════════════════════════════ */}
            {currentStep === 4 && (
              <div className="py-8 text-center space-y-6 animate-in zoom-in-95 duration-200">
                <div className="w-16 h-16 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center mx-auto text-zinc-950 shadow-2xs">
                  <CheckCircle2 className="w-8 h-8 text-zinc-950" />
                </div>

                <div className="space-y-2 max-w-[480px] mx-auto">
                  <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 rounded-full text-[11px] font-mono font-bold text-zinc-700">
                    <span>REFERENCE ID:</span>
                    <span className="text-zinc-950">{ticketNumber}</span>
                  </div>
                  
                  <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950">
                    Inquiry Received!
                  </h2>
                  <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed">
                    Thank you, <strong>{formData.name}</strong>. Our solutions team in Mumbai will review your organization’s requirements and respond to <strong>{formData.email}</strong> within 2-4 business hours.
                  </p>
                </div>

                {/* Instant Next Steps Action Grid */}
                <div className="pt-4 max-w-[500px] mx-auto grid grid-cols-1 sm:grid-cols-2 gap-3 text-left">
                  <a
                    href="https://app.heycora.in/workspace/login?source=contact_success"
                    className="p-4 rounded-2xl bg-zinc-950 text-white flex flex-col justify-between space-y-2 hover:bg-zinc-800 transition-colors shadow-sm"
                  >
                    <div className="flex items-center justify-between">
                      <span className="text-[10px] font-mono font-bold uppercase text-zinc-400">FREE WORKSPACE</span>
                      <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                    </div>
                    <div className="text-xs font-bold">Launch Workspace Portal</div>
                    <div className="text-[11px] text-zinc-400">Start live in 30 seconds with 1,000 AI runs.</div>
                  </a>

                  <Link
                    href="/demo"
                    className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 text-zinc-950 flex flex-col justify-between space-y-2 hover:bg-zinc-100 transition-colors"
                  >
                    <div className="flex items-center justify-between">
                      <span className="text-[10px] font-mono font-bold uppercase text-zinc-500">LIVE PREVIEW</span>
                      <ArrowRight className="w-3.5 h-3.5 text-zinc-500" />
                    </div>
                    <div className="text-xs font-bold">Explore Interactive Demo</div>
                    <div className="text-[11px] text-zinc-500">Simulate autonomous pipeline modules.</div>
                  </Link>
                </div>

                <div className="pt-2">
                  <button
                    type="button"
                    onClick={() => {
                      setCurrentStep(1);
                      setFormData({
                        industry: 'agency',
                        teamSize: '1-5 members',
                        selectedWorkflows: ['ai_scoping', 'crm', 'gst'],
                        revenueBracket: '₹5L – ₹25L/mo',
                        name: '',
                        email: '',
                        companyName: '',
                        phone: '',
                        message: ''
                      });
                    }}
                    className="text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors underline underline-offset-2"
                  >
                    Submit another inquiry
                  </button>
                </div>

              </div>
            )}

          </div>

        </div>
      </section>

    </main>
  );
}
