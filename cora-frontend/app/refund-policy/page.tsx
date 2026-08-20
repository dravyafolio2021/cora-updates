import React from 'react';
import Link from 'next/link';
import { RefreshCw, CheckCircle2, ArrowLeft, ArrowRight, ShieldCheck, CreditCard, Sparkles } from 'lucide-react';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Refund & Cancellation Policy | Cora Studio OS',
  description: 'Our transparent 14-day money-back guarantee, instant 1-click cancellation, and billing policies.',
};

export default function RefundPolicyPage() {
  return (
    <main className="min-h-screen bg-[#FBFaf7] text-zinc-900 pt-28 sm:pt-36 pb-24">
      <div className="max-w-4xl mx-auto px-4 sm:px-6">
        
        {/* Breadcrumb Navigation */}
        <div className="mb-8">
          <Link 
            href="/" 
            className="inline-flex items-center gap-2 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Back to Home</span>
          </Link>
        </div>

        {/* Header Title */}
        <div className="mb-12 border-b border-zinc-200/80 pb-8">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/80 mb-4">
            <RefreshCw className="w-3.5 h-3.5 text-amber-600" />
            <span>BILLING &amp; SUBSCRIPTION GUARANTEE</span>
          </div>
          <h1 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 mb-4">
            Refund &amp; Cancellation Policy
          </h1>
          <p className="text-zinc-600 text-sm sm:text-base leading-relaxed">
            Effective Date: January 1, 2026 &bull; Last Updated: August 20, 2026
          </p>
        </div>

        {/* 14-Day Guarantee Hero Card */}
        <div className="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-white via-zinc-50 to-white border border-zinc-200/90 shadow-sm mb-12 space-y-4">
          <div className="flex items-center gap-2 text-sm font-bold text-zinc-950">
            <Sparkles className="w-4 h-4 text-emerald-600" />
            <span>14-Day Full Money-Back Guarantee</span>
          </div>
          <p className="text-xs sm:text-sm text-zinc-700 leading-relaxed font-normal">
            We want you to build with complete confidence. If you subscribe to any paid plan on <strong>Cora Studio OS</strong> and feel it does not dramatically streamline your lead funnel, client contracts, or 18% GST invoicing within the first <strong>14 days</strong> of your initial subscription, email us for an unconditional 100% refund.
          </p>
          <div className="pt-2 flex flex-wrap gap-4 text-xs font-semibold text-zinc-700">
            <div className="flex items-center gap-1.5"><CheckCircle2 className="w-4 h-4 text-emerald-600" /><span>Zero cancellation friction</span></div>
            <div className="flex items-center gap-1.5"><CheckCircle2 className="w-4 h-4 text-emerald-600" /><span>Self-serve 1-click downgrade</span></div>
            <div className="flex items-center gap-1.5"><CheckCircle2 className="w-4 h-4 text-emerald-600" /><span>Processed within 3-5 bank days</span></div>
          </div>
        </div>

        {/* Clauses */}
        <div className="space-y-10 text-sm sm:text-base text-zinc-700 leading-relaxed font-normal">
          
          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              1. Monthly Subscriptions
            </h2>
            <p>
              Monthly plans are billed in advance on a 30-day recurring cycle. You may cancel your subscription at any time directly through your <strong>Workspace Admin Settings &rarr; Billing</strong> tab.
            </p>
            <p className="text-sm text-zinc-600">
              Upon cancellation, your account will remain active with full premium features until the end of the current paid monthly billing cycle. No further recurring charges will be initiated.
            </p>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              2. Annual Subscriptions &amp; Early Termination
            </h2>
            <p>
              Annual plans offer significant cost savings (equivalent to 20% discount). Annual subscribers are covered by our 14-day 100% money-back guarantee.
            </p>
            <p className="text-sm text-zinc-600">
              If an annual subscriber requests cancellation after the 14-day window due to exceptional business circumstances, Cora will calculate the refund on a pro-rated basis, adjusting used months at the standard non-discounted monthly plan rate.
            </p>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              3. AI Usage &amp; Third-Party Add-Ons
            </h2>
            <p>
              Autonomous AI agent actions, voice-to-scope minutes, and RAG vector searches included in your tier quota are replenished every billing cycle. Unused monthly AI quota does not carry over to subsequent billing months. Custom enterprise model fine-tuning or dedicated dedicated API quota add-ons are non-refundable once activated.
            </p>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              4. How to Request a Refund
            </h2>
            <p>
              To process your 14-day refund, simply send a brief request from your registered workspace account email:
            </p>
            <div className="p-4 rounded-2xl bg-white border border-zinc-200 text-xs font-mono text-zinc-800 space-y-1">
              <div>To: <a href="mailto:billing@heycora.in" className="text-emerald-700 underline">billing@heycora.in</a></div>
              <div>Subject: Refund Request - [Your Workspace Name]</div>
              <div>Processing Time: Payout initiated within 24 hours to original payment method (Credit Card / UPI / NetBanking / Stripe).</div>
            </div>
          </section>

        </div>

        {/* Bottom CTA */}
        <div className="mt-16 pt-8 border-t border-zinc-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <Link 
            href="/pricing" 
            className="text-xs font-semibold text-zinc-700 hover:text-zinc-950 flex items-center gap-1.5"
          >
            <span>Review our transparent Pricing Plans</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </Link>
          <a
            href="https://app.heycora.in/workspace/login"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-zinc-800 transition-all"
          >
            <span>Access Workspace Billing</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
          </a>
        </div>

      </div>
    </main>
  );
}
