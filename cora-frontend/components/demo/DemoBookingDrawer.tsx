'use client';

import React, { useState, useEffect } from 'react';
import { X, Calendar, Clock, CheckCircle2, ArrowRight, Building, User, Mail, Phone, Sparkles } from 'lucide-react';

interface DemoBookingDrawerProps {
  isOpen: boolean;
  onClose: () => void;
}

export function DemoBookingDrawer({ isOpen, onClose }: DemoBookingDrawerProps) {
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    studioName: '',
    studioType: 'photography_studio',
    teamSize: '6-20',
    preferredSlot: 'morning',
    notes: ''
  });

  // Lock body scroll when drawer is open
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
      setIsSubmitted(false);
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [isOpen]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitted(true);
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex justify-end">
      {/* Backdrop */}
      <div 
        onClick={onClose}
        className="fixed inset-0 bg-zinc-950/40 backdrop-blur-xs transition-opacity duration-300"
        aria-hidden="true"
      />

      {/* Right-Sliding Drawer Sheet */}
      <div className="relative z-10 w-full max-w-[540px] h-full bg-white shadow-2xl flex flex-col justify-between overflow-y-auto animate-in slide-in-from-right duration-300">
        
        {/* Drawer Header */}
        <div className="p-6 sm:p-8 border-b border-zinc-100 flex items-center justify-between sticky top-0 bg-white/95 backdrop-blur-md z-10">
          <div className="space-y-1">
            <div className="flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              <span className="text-[11px] font-mono font-semibold uppercase tracking-wider text-zinc-500">
                1:1 Studio Walkthrough
              </span>
            </div>
            <h2 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
              Book a live demo.
            </h2>
          </div>
          <button
            onClick={onClose}
            type="button"
            className="w-9 h-9 rounded-full border border-zinc-200 flex items-center justify-center text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-colors"
            aria-label="Close drawer"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Drawer Body */}
        <div className="p-6 sm:p-8 flex-1">
          {isSubmitted ? (
            <div className="h-full flex flex-col items-center justify-center text-center space-y-5 py-12">
              <div className="w-14 h-14 rounded-2xl bg-zinc-950 text-white flex items-center justify-center shadow-lg">
                <CheckCircle2 className="w-7 h-7" />
              </div>
              <div className="space-y-2 max-w-[360px]">
                <h3 className="font-display text-2xl font-bold text-zinc-950">
                  Demo requested!
                </h3>
                <p className="text-sm text-zinc-600 leading-relaxed font-normal">
                  Thank you, <strong className="text-zinc-950">{formData.name || 'Founder'}</strong>. Our workflow specialist will connect with you at <strong className="text-zinc-950">{formData.email || 'your email'}</strong> to confirm your personalized walkthrough.
                </p>
              </div>
              <div className="p-4 rounded-xl bg-zinc-50 border border-zinc-200/80 text-left w-full space-y-2 text-xs text-zinc-600 font-mono">
                <div className="flex justify-between">
                  <span className="text-zinc-400">Studio:</span>
                  <span className="font-semibold text-zinc-950">{formData.studioName || 'Creative Studio'}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-zinc-400">Preferred Slot:</span>
                  <span className="font-semibold text-zinc-950 capitalize">{formData.preferredSlot} (IST)</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-zinc-400">Status:</span>
                  <span className="font-semibold text-emerald-600">Calendar invite pending</span>
                </div>
              </div>
              <button
                type="button"
                onClick={onClose}
                className="w-full py-3 rounded-xl bg-zinc-950 text-white text-sm font-semibold hover:bg-zinc-800 transition-colors"
              >
                Done
              </button>
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="space-y-5">
              <p className="text-sm text-zinc-600 font-normal leading-relaxed">
                See how Cora eliminates administrative friction, automates 18% GST billing, and unifies client contracts for your specific workflow.
              </p>

              {/* Full Name */}
              <div className="space-y-1.5">
                <label className="text-xs font-semibold text-zinc-700 block">
                  Your Full Name <span className="text-zinc-400">*</span>
                </label>
                <div className="relative">
                  <input
                    type="text"
                    required
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    placeholder="e.g. Rahul Sharma"
                    className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-sm text-zinc-950 placeholder:text-zinc-400 focus:outline-hidden focus:border-zinc-950 transition-colors bg-white shadow-2xs"
                  />
                  <User className="w-4 h-4 text-zinc-400 absolute right-3.5 top-3 pointer-events-none" />
                </div>
              </div>

              {/* Work Email */}
              <div className="space-y-1.5">
                <label className="text-xs font-semibold text-zinc-700 block">
                  Work Email <span className="text-zinc-400">*</span>
                </label>
                <div className="relative">
                  <input
                    type="email"
                    required
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    placeholder="rahul@studio.com"
                    className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-sm text-zinc-950 placeholder:text-zinc-400 focus:outline-hidden focus:border-zinc-950 transition-colors bg-white shadow-2xs"
                  />
                  <Mail className="w-4 h-4 text-zinc-400 absolute right-3.5 top-3 pointer-events-none" />
                </div>
              </div>

              {/* Phone / WhatsApp */}
              <div className="space-y-1.5">
                <label className="text-xs font-semibold text-zinc-700 block">
                  Phone / WhatsApp <span className="text-zinc-400">*</span>
                </label>
                <div className="relative">
                  <input
                    type="tel"
                    required
                    value={formData.phone}
                    onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                    placeholder="+91 98765 43210"
                    className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-sm text-zinc-950 placeholder:text-zinc-400 focus:outline-hidden focus:border-zinc-950 transition-colors bg-white shadow-2xs"
                  />
                  <Phone className="w-4 h-4 text-zinc-400 absolute right-3.5 top-3 pointer-events-none" />
                </div>
              </div>

              {/* Studio / Agency Name */}
              <div className="space-y-1.5">
                <label className="text-xs font-semibold text-zinc-700 block">
                  Studio or Production House Name <span className="text-zinc-400">*</span>
                </label>
                <div className="relative">
                  <input
                    type="text"
                    required
                    value={formData.studioName}
                    onChange={(e) => setFormData({ ...formData, studioName: e.target.value })}
                    placeholder="e.g. Apex Visual Studios"
                    className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-sm text-zinc-950 placeholder:text-zinc-400 focus:outline-hidden focus:border-zinc-950 transition-colors bg-white shadow-2xs"
                  />
                  <Building className="w-4 h-4 text-zinc-400 absolute right-3.5 top-3 pointer-events-none" />
                </div>
              </div>

              {/* Studio Type */}
              <div className="space-y-1.5">
                <label className="text-xs font-semibold text-zinc-700 block">
                  Primary Business Type
                </label>
                <select
                  value={formData.studioType}
                  onChange={(e) => setFormData({ ...formData, studioType: e.target.value })}
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-sm text-zinc-950 focus:outline-hidden focus:border-zinc-950 transition-colors bg-white shadow-2xs"
                >
                  <option value="photography_studio">Commercial Photography Studio</option>
                  <option value="film_production">Film &amp; Video Production House</option>
                  <option value="creative_agency">Creative Agency / Design Studio</option>
                  <option value="rental_studio">Studio Rental &amp; Equipment Bay</option>
                  <option value="solo_creator">Solo Commercial Creator / Director</option>
                </select>
              </div>

              {/* Preferred Time Slot */}
              <div className="space-y-1.5">
                <label className="text-xs font-semibold text-zinc-700 block">
                  Preferred Demo Time Slot
                </label>
                <div className="grid grid-cols-3 gap-2">
                  {[
                    { id: 'morning', label: 'Morning (10 AM - 1 PM)' },
                    { id: 'afternoon', label: 'Afternoon (2 PM - 5 PM)' },
                    { id: 'evening', label: 'Evening (5 PM - 8 PM)' }
                  ].map((slot) => (
                    <button
                      key={slot.id}
                      type="button"
                      onClick={() => setFormData({ ...formData, preferredSlot: slot.id })}
                      className={`p-2.5 rounded-xl border text-xs font-medium transition-all text-center ${
                        formData.preferredSlot === slot.id
                          ? 'border-zinc-950 bg-zinc-950 text-white shadow-xs'
                          : 'border-zinc-200 text-zinc-600 hover:bg-zinc-50'
                      }`}
                    >
                      {slot.label}
                    </button>
                  ))}
                </div>
              </div>

              {/* Submit CTA */}
              <div className="pt-2">
                <button
                  type="submit"
                  className="w-full py-3.5 rounded-xl bg-zinc-950 text-white text-sm font-semibold hover:bg-zinc-800 transition-colors shadow-sm flex items-center justify-center gap-2"
                >
                  <span>Confirm Live Demo Booking</span>
                  <ArrowRight className="w-4 h-4" />
                </button>
                <p className="text-[11px] text-zinc-400 text-center mt-2">
                  No credit card required. Instant confirmation via email.
                </p>
              </div>
            </form>
          )}
        </div>

        {/* Drawer Footer Note */}
        <div className="p-4 border-t border-zinc-100 bg-zinc-50/80 text-center">
          <span className="text-xs text-zinc-500 font-mono">
            Direct Founder Hotline: dravya.bansal@heycora.in
          </span>
        </div>

      </div>
    </div>
  );
}
