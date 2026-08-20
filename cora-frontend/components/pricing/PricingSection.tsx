'use client';

import React, { useState, useEffect, useCallback } from 'react';
import { Check, Globe, User, Rocket, BarChart3, Shield, RefreshCw, Lock, Quote } from 'lucide-react';
import { useToast } from '../ui/Toast';

declare global {
  interface Window {
    Razorpay?: any;
  }
}

export function PricingSection() {
  const [billing, setBilling] = useState<'monthly' | 'annual'>('annual');
  const [fxRate, setFxRate] = useState<number>(87.0);
  const { showToast } = useToast();

  const isAnnual = billing === 'annual';

  // Fetch Live Real-Time USD to INR Exchange Rate
  useEffect(() => {
    fetch('https://open.er-api.com/v6/latest/USD')
      .then((res) => res.json())
      .then((data) => {
        if (data && data.rates && data.rates.INR) {
          setFxRate(parseFloat(data.rates.INR));
        }
      })
      .catch(() => {
        console.log('Using default FX rate:', fxRate);
      });
  }, [fxRate]);

  // Account-First Onboarding Handler (Industry Standard Flow)
  const handlePlanSelect = useCallback((planType: string) => {
    if (planType === 'india_only' && !isAnnual) {
      setBilling('annual');
      showToast('Switched to Annual mode for the India Only plan. Click again to continue.');
      return;
    }

    const targetUrl = `https://app.heycora.in/workspace/login?plan=${encodeURIComponent(planType)}&billing=${encodeURIComponent(isAnnual ? 'annual' : 'monthly')}`;
    window.location.href = targetUrl;
  }, [isAnnual, showToast]);

  return (
    <section id="pricing" className="py-16 md:py-20 relative z-10 border-t border-zinc-100 bg-gradient-to-b from-zinc-50/50 to-white">
      <div className="w-full max-w-[1140px] mx-auto px-6">
        
        {/* Section Header */}
        <div className="text-center max-w-[780px] mx-auto mb-7">
          <div className="inline-flex items-center gap-1.5 font-sans text-[0.8125rem] font-medium text-zinc-600 px-3.5 py-1 bg-white border border-zinc-200 rounded-full mb-4 shadow-sm">
            <span>Pricing</span>
          </div>
          <h2 className="font-display text-[clamp(2rem,4.2vw,3.15rem)] font-[550] tracking-[-0.035em] text-zinc-950 leading-[1.15] mb-3">
            Simple, transparent pricing.
          </h2>
          <p className="font-sans text-[clamp(0.85rem,1.2vw,1.02rem)] text-zinc-600 leading-[1.55] font-normal">
            Start for free. Upgrade when you need more power.<br />
            Annual plans get <span className="text-indigo-600 font-semibold">2 months free.</span>
          </p>
        </div>

        {/* Sticky Billing Frequency Switcher & Doodle Annotation */}
        <div className="sticky top-4 z-40 flex items-center justify-center mb-8 py-1.5 pointer-events-auto">
          <div className="inline-flex items-center bg-zinc-100/90 backdrop-blur-xl p-1 rounded-full border border-zinc-200/90 shadow-md">
            <button
              type="button"
              onClick={() => setBilling('monthly')}
              className={`px-4 py-1.5 font-sans text-[0.8125rem] font-semibold rounded-full transition-all ${
                !isAnnual ? 'bg-zinc-950 text-white shadow-sm' : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              Monthly
            </button>
            <button
              type="button"
              onClick={() => setBilling('annual')}
              className={`px-4 py-1.5 font-sans text-[0.8125rem] font-semibold rounded-full transition-all ${
                isAnnual ? 'bg-zinc-950 text-white shadow-sm' : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              Annual
            </button>
          </div>

          {/* Doodle Arrow with "Save up to 16% + 2 months free" */}
          <div className="hidden md:flex absolute left-[calc(50%+110px)] -top-1 items-center gap-1.5 font-scribble text-[1.05rem] font-bold text-zinc-950 leading-tight whitespace-nowrap pointer-events-none" aria-hidden="true">
            <svg className="w-7 h-4.5 stroke-zinc-950 -rotate-[10deg]" viewBox="0 0 45 28" fill="none" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
              <path d="M 40 22 C 30 26, 12 24, 6 10 C 5 7, 4 4, 3 2" />
              <path d="M 12 4 L 3 2 L 4 11" />
            </svg>
            <div>
              <span>Save up to 16%</span><br />
              <span className="text-indigo-600">+ 2 months free</span>
            </div>
          </div>
        </div>

        {/* ROW 1: Free Forever + India Only Plan (2 Columns - 50% Height Compact) */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
          
          {/* Card 1: Free Forever */}
          <div className="bg-white border border-zinc-200 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:border-zinc-300 hover:-translate-y-0.5 transition-all">
            <div>
              <div className="flex items-center gap-2 mb-1.5">
                <div className="w-7 h-7 rounded-full bg-purple-50 flex items-center justify-center border border-purple-100">
                  <Globe className="w-3.5 h-3.5 text-purple-700" />
                </div>
                <span className="text-[0.625rem] font-bold uppercase tracking-wider px-2 py-0.5 bg-zinc-100 rounded text-zinc-600">
                  For Everyone
                </span>
              </div>

              <h3 className="font-display text-[1.18rem] font-bold tracking-[-0.02em] text-zinc-950 mb-0.5">Free Forever</h3>
              <p className="text-[0.78rem] text-zinc-600 leading-snug mb-2.5">For individuals exploring and building with AI.</p>

              <div className="flex items-baseline gap-0.5 mb-0.5">
                <span className="font-mono text-[1.15rem] font-semibold text-zinc-950">$</span>
                <span className="font-mono text-[1.95rem] font-bold tracking-[-0.03em] text-zinc-950 leading-none">0</span>
                <span className="text-[0.78rem] text-zinc-600 ml-1">/ forever</span>
              </div>

              <div className="mb-3 text-[0.72rem] text-zinc-500">
                <span>Free for early adopters</span>
              </div>

              {/* Compact 2-Column Features Grid */}
              <ul className="grid grid-cols-1 sm:grid-cols-2 gap-x-3.5 gap-y-1.5 mb-4 pt-3 border-t border-zinc-100">
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>Core AI Models</strong> (Gemini & GPT-4o mini)</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Smart Document Vault</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>1,000</strong> AI Agent Runs / mo</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Community & Discord</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>Unlimited</strong> Client Funnels</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Early feature access</span>
                </li>
              </ul>
            </div>

            <div>
              <a
                href="https://app.heycora.in/workspace/login?plan=free"
                className="w-full inline-flex items-center justify-center py-2 px-4 rounded-xl font-sans text-[0.8125rem] font-semibold bg-white text-zinc-950 border border-zinc-300 hover:bg-zinc-50 hover:border-zinc-950 transition-all hover:-translate-y-0.5"
              >
                <span>Get Started Free →</span>
              </a>
              <div className="text-center text-[0.7rem] text-zinc-400 mt-1.5">No credit card required</div>
            </div>
          </div>

          {/* Card 2: India Only Plan (Dynamic Monthly Disabled State) */}
          <div
            className={`rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 ${
              isAnnual
                ? 'bg-[#fffdfa] border border-amber-200 shadow-sm hover:border-amber-300 hover:-translate-y-0.5'
                : 'bg-zinc-50 border border-zinc-200 opacity-45 grayscale-[0.5] hover:transform-none'
            }`}
          >
            <div>
              <div className="flex items-center gap-2 mb-1.5">
                <div className="w-7 h-7 rounded-full bg-white flex items-center justify-center border border-orange-200 text-sm">
                  <span>🇮🇳</span>
                </div>
                <span className="text-[0.625rem] font-bold uppercase tracking-wider px-2 py-0.5 bg-orange-100 rounded text-orange-800">
                  India Only
                </span>
              </div>

              <h3 className="font-display text-[1.18rem] font-bold tracking-[-0.02em] text-zinc-950 mb-0.5">India Only Plan</h3>
              <p className="text-[0.78rem] text-zinc-600 leading-snug mb-2.5">All Pro features for Indian founders & solo builders.</p>

              <div className="flex items-baseline gap-0.5 mb-0.5">
                <span className="font-mono text-[1.15rem] font-semibold text-zinc-950">₹</span>
                <span className="font-mono text-[1.95rem] font-bold tracking-[-0.03em] text-zinc-950 leading-none">499</span>
                <span className="text-[0.78rem] text-zinc-600 ml-1">/ month</span>
              </div>

              <div className="mb-3 text-[0.72rem]">
                <span className="inline-block font-semibold bg-orange-100 text-orange-800 px-2 py-0.5 rounded-full text-[0.6875rem]">
                  {isAnnual ? 'Billed annually (₹4,999 / year)' : 'Available on annual only'}
                </span>
              </div>

              {/* Compact 2-Column Features Grid */}
              <ul className="grid grid-cols-1 sm:grid-cols-2 gap-x-3.5 gap-y-1.5 mb-4 pt-3 border-t border-zinc-100">
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>All Pro Features</strong> Included</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>2,500</strong> AI Agent Runs / mo</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>UPI, NetBanking & RuPay</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Priority WhatsApp Support</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>Full GST Invoicing</strong> (B2B)</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Indian Rupee Direct Billing</span>
                </li>
              </ul>
            </div>

            <div>
              <button
                type="button"
                onClick={() => handlePlanSelect('india_only')}
                className={`w-full inline-flex items-center justify-center py-2 px-4 rounded-xl font-sans text-[0.8125rem] font-semibold text-white transition-all ${
                  isAnnual
                    ? 'bg-zinc-950 hover:bg-zinc-800 shadow-sm hover:-translate-y-0.5 cursor-pointer'
                    : 'bg-zinc-600 hover:bg-zinc-700 cursor-pointer'
                }`}
              >
                <span>{isAnnual ? 'Get India Plan →' : 'Annual Only (Switch to Unlock) →'}</span>
              </button>
              <div className="text-center text-[0.7rem] text-zinc-400 mt-1.5">
                {isAnnual ? 'Annual billing only' : 'Switch to Annual mode to unlock'}
              </div>
            </div>
          </div>

        </div>

        {/* ROW 2: Global 3-Tier Grid (Starter, Pro, Scale) */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-11 items-stretch">
          
          {/* Starter Plan ($9/mo) */}
          <div className="bg-white border border-zinc-200 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:border-zinc-300 hover:-translate-y-0.5 transition-all">
            <div>
              <div className="flex items-center gap-2 mb-1.5">
                <div className="w-7 h-7 rounded-full bg-blue-50 flex items-center justify-center border border-blue-100">
                  <User className="w-3.5 h-3.5 text-blue-700" />
                </div>
                <span className="text-[0.625rem] font-bold uppercase tracking-wider px-2 py-0.5 bg-zinc-100 rounded text-zinc-600">
                  Starter
                </span>
              </div>

              <h3 className="font-display text-[1.18rem] font-bold tracking-[-0.02em] text-zinc-950 mb-0.5">Starter</h3>
              <p className="text-[0.78rem] text-zinc-600 leading-snug mb-2.5">For solo operators starting their journey.</p>

              <div className="flex items-baseline gap-0.5 mb-0.5">
                <span className="font-mono text-[1.15rem] font-semibold text-zinc-950">$</span>
                <span className="font-mono text-[1.95rem] font-bold tracking-[-0.03em] text-zinc-950 leading-none">9</span>
                <span className="text-[0.78rem] text-zinc-600 ml-1">/ month</span>
              </div>

              <div className="mb-3 text-[0.72rem] flex items-center gap-2">
                <span className="text-zinc-400">{isAnnual ? '$90 / year' : '$108 / year (monthly billed)'}</span>
                {isAnnual && (
                  <span className="bg-purple-100 text-purple-900 px-2 py-0.5 rounded-full font-semibold text-[0.6875rem]">
                    Save $18 with annual
                  </span>
                )}
              </div>

              <ul className="flex flex-col gap-1.5 mb-4 pt-3 border-t border-zinc-100">
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Everything in <strong>Free</strong></span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>5,000</strong> AI Agent Runs / month</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Claude 3.5 Sonnet Access</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Custom Workspace Domain</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Standard Email & Chat Support</span>
                </li>
              </ul>
            </div>

            <div>
              <button
                type="button"
                onClick={() => handlePlanSelect('starter')}
                className="w-full inline-flex items-center justify-center py-2 px-4 rounded-xl font-sans text-[0.8125rem] font-semibold bg-white text-zinc-950 border border-zinc-300 hover:bg-zinc-50 hover:border-zinc-950 transition-all hover:-translate-y-0.5"
              >
                <span>Get Started →</span>
              </button>
              <div className="text-center text-[0.7rem] text-zinc-400 mt-1.5">Cancel anytime</div>
            </div>
          </div>

          {/* Pro Plan ($19/mo) — MOST POPULAR */}
          <div className="bg-white border-2 border-zinc-950 rounded-2xl p-5 flex flex-col justify-between shadow-lg relative hover:-translate-y-0.5 transition-all">
            <div className="absolute -top-2.5 left-1/2 -translate-x-1/2 bg-zinc-950 text-white text-[0.625rem] font-bold tracking-wider uppercase px-2.5 py-0.5 rounded-full shadow-md flex items-center gap-1 whitespace-nowrap">
              <span>★ Most Popular</span>
            </div>

            <div>
              <div className="flex items-center gap-2 mb-1.5">
                <div className="w-7 h-7 rounded-full bg-purple-50 flex items-center justify-center border border-purple-100">
                  <Rocket className="w-3.5 h-3.5 text-purple-700" />
                </div>
                <span className="text-[0.625rem] font-bold uppercase tracking-wider px-2 py-0.5 bg-zinc-100 rounded text-zinc-600">
                  Pro Studio
                </span>
              </div>

              <h3 className="font-display text-[1.18rem] font-bold tracking-[-0.02em] text-zinc-950 mb-0.5">Pro</h3>
              <p className="text-[0.78rem] text-zinc-600 leading-snug mb-2.5">For growing teams needing more power & flexibility.</p>

              <div className="flex items-baseline gap-0.5 mb-0.5">
                <span className="font-mono text-[1.15rem] font-semibold text-zinc-950">$</span>
                <span className="font-mono text-[1.95rem] font-bold tracking-[-0.03em] text-zinc-950 leading-none">19</span>
                <span className="text-[0.78rem] text-zinc-600 ml-1">/ month</span>
              </div>

              <div className="mb-3 text-[0.72rem] flex items-center gap-2">
                <span className="text-zinc-400">{isAnnual ? '$190 / year' : '$228 / year (monthly billed)'}</span>
                {isAnnual && (
                  <span className="bg-purple-100 text-purple-900 px-2 py-0.5 rounded-full font-semibold text-[0.6875rem]">
                    Save $38 with annual
                  </span>
                )}
              </div>

              <ul className="flex flex-col gap-1.5 mb-4 pt-3 border-t border-zinc-100">
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Everything in <strong>Starter</strong></span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>10,000</strong> AI Agent Runs / month</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>All Frontier Models</strong> (Claude, GPT-4o, Gemini)</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>3 Team Seats</strong> Included</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>GST Tax Breakdown & Invoicing</span>
                </li>
              </ul>
            </div>

            <div>
              <button
                type="button"
                onClick={() => handlePlanSelect('pro')}
                className="w-full inline-flex items-center justify-center py-2 px-4 rounded-xl font-sans text-[0.8125rem] font-semibold bg-zinc-950 text-white hover:bg-zinc-800 transition-all hover:-translate-y-0.5 shadow-sm"
              >
                <span>Get Pro →</span>
              </button>
              <div className="text-center text-[0.7rem] text-zinc-400 mt-1.5">Cancel anytime</div>
            </div>
          </div>

          {/* Scale Plan ($39/mo) */}
          <div className="bg-white border border-zinc-200 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:border-zinc-300 hover:-translate-y-0.5 transition-all">
            <div>
              <div className="flex items-center gap-2 mb-1.5">
                <div className="w-7 h-7 rounded-full bg-blue-50 flex items-center justify-center border border-blue-100">
                  <BarChart3 className="w-3.5 h-3.5 text-blue-700" />
                </div>
                <span className="text-[0.625rem] font-bold uppercase tracking-wider px-2 py-0.5 bg-zinc-100 rounded text-zinc-600">
                  Scale & Agency
                </span>
              </div>

              <h3 className="font-display text-[1.18rem] font-bold tracking-[-0.02em] text-zinc-950 mb-0.5">Scale</h3>
              <p className="text-[0.78rem] text-zinc-600 leading-snug mb-2.5">For agencies with high-volume operations.</p>

              <div className="flex items-baseline gap-0.5 mb-0.5">
                <span className="font-mono text-[1.15rem] font-semibold text-zinc-950">$</span>
                <span className="font-mono text-[1.95rem] font-bold tracking-[-0.03em] text-zinc-950 leading-none">39</span>
                <span className="text-[0.78rem] text-zinc-600 ml-1">/ month</span>
              </div>

              <div className="mb-3 text-[0.72rem] flex items-center gap-2">
                <span className="text-zinc-400">{isAnnual ? '$390 / year' : '$468 / year (monthly billed)'}</span>
                {isAnnual && (
                  <span className="bg-purple-100 text-purple-900 px-2 py-0.5 rounded-full font-semibold text-[0.6875rem]">
                    Save $78 with annual
                  </span>
                )}
              </div>

              <ul className="flex flex-col gap-1.5 mb-4 pt-3 border-t border-zinc-100">
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Everything in <strong>Pro</strong></span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>Unlimited</strong> AI Executions</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span><strong>10 Team Seats</strong> Included</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Custom Fine-Tuned AI Personas</span>
                </li>
                <li className="flex items-start gap-1.5 text-[0.75rem] text-zinc-600 leading-snug">
                  <Check className="w-3.5 h-3.5 text-zinc-950 shrink-0 mt-0.5" />
                  <span>Dedicated Account Manager & 99.9% SLA</span>
                </li>
              </ul>
            </div>

            <div>
              <button
                type="button"
                onClick={() => handlePlanSelect('scale')}
                className="w-full inline-flex items-center justify-center py-2 px-4 rounded-xl font-sans text-[0.8125rem] font-semibold bg-white text-zinc-950 border border-zinc-300 hover:bg-zinc-50 hover:border-zinc-950 transition-all hover:-translate-y-0.5"
              >
                <span>Get Scale →</span>
              </button>
              <div className="text-center text-[0.7rem] text-zinc-400 mt-1.5">Cancel anytime</div>
            </div>
          </div>

        </div>

        {/* Trust Guarantee Strip */}
        <div className="flex items-center justify-center flex-wrap gap-7 text-[0.78rem] text-zinc-500 py-2 pb-10">
          <div className="inline-flex items-center gap-1.5">
            <Shield className="w-3.5 h-3.5 stroke-[2] text-zinc-950" />
            <span>No credit card required</span>
          </div>
          <div className="inline-flex items-center gap-1.5">
            <RefreshCw className="w-3.5 h-3.5 stroke-[2] text-zinc-950" />
            <span>Switch, pause, or cancel anytime</span>
          </div>
          <div className="inline-flex items-center gap-1.5">
            <Lock className="w-3.5 h-3.5 stroke-[2] text-zinc-950" />
            <span>Secure 256-bit SSL encrypted payments</span>
          </div>
        </div>

        {/* Bottom Testimonial + Ready to Build Card */}
        <div className="bg-white border border-zinc-200 rounded-2xl p-6 md:p-7 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
          <div className="flex items-start gap-4 flex-1">
            <div className="w-9 h-9 bg-purple-100 text-purple-700 rounded-xl flex items-center justify-center shrink-0">
              <Quote className="w-4.5 h-4.5 fill-current" />
            </div>
            <div>
              <blockquote className="text-[0.875rem] font-medium text-zinc-950 leading-relaxed mb-2">
                "Finally, an AI workspace that doesn't overcharge early builders. The free plan is actually useful."
              </blockquote>
              <div className="flex items-center gap-2 text-[0.74rem]">
                <div className="w-5.5 h-5.5 bg-indigo-100 text-indigo-900 rounded-full flex items-center justify-center font-bold text-[0.7rem]">
                  A
                </div>
                <div>
                  <span className="font-bold text-zinc-950">Ankit Sharma</span> · <span className="text-zinc-400">Founder, New Delhi</span>
                </div>
              </div>
            </div>
          </div>

          <div className="flex flex-col items-start gap-1.5 min-w-[260px] max-md:w-full">
            <h4 className="font-display text-[1.05rem] font-bold tracking-[-0.02em] text-zinc-950">Ready to build your AI advantage?</h4>
            <p className="text-[0.78rem] text-zinc-500 mb-1">Join thousands of builders already scaling with us.</p>
            <a
              href="https://app.heycora.in/workspace/login?plan=free"
              className="inline-flex items-center justify-center bg-zinc-950 text-white px-5 py-2.5 rounded-xl font-sans text-xs font-semibold hover:bg-zinc-800 transition-all max-md:w-full"
            >
              <span>Start Free Today →</span>
            </a>
          </div>
        </div>

      </div>
    </section>
  );
}
