'use client';

import React, { useState, useEffect } from 'react';
import { 
  X, 
  Check, 
  Sparkles, 
  ArrowRight, 
  ShieldCheck, 
  Receipt, 
  Globe, 
  Gift, 
  Building2, 
  Lock, 
  Rocket, 
  TrendingUp, 
  Leaf,
  Info,
  ChevronDown,
  ChevronUp
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

export type PlanKey = 'free' | 'starter' | 'pro' | 'scale';
export type BillingCycle = 'monthly' | 'annual';

interface CheckoutSheetProps {
  isOpen: boolean;
  onClose: () => void;
  initialPlan?: PlanKey;
  initialBilling?: BillingCycle;
  currency?: 'INR' | 'USD';
}

interface PlanDetails {
  key: PlanKey;
  name: string;
  badge?: string;
  headerColor: string;
  icon: React.ElementType;
  monthlyPriceINR: number;
  annualMonthlyEquivalentINR: number;
  annualTotalINR: number;
  monthlyPriceUSD: number;
  annualMonthlyEquivalentUSD: number;
  annualTotalUSD: number;
  description: string;
  features: string[];
  exclusiveBonus?: string;
}

const PLAN_CATALOG: Record<PlanKey, PlanDetails> = {
  free: {
    key: 'free',
    name: 'Free Forever',
    badge: '100% Free',
    headerColor: '#18181b',
    icon: Leaf,
    monthlyPriceINR: 0,
    annualMonthlyEquivalentINR: 0,
    annualTotalINR: 0,
    monthlyPriceUSD: 0,
    annualMonthlyEquivalentUSD: 0,
    annualTotalUSD: 0,
    description: 'For solo operators, solopreneurs & creators starting out.',
    features: [
      '1,000 AI Runs / month',
      'Live portfolio & website on heycora.in',
      'Kanban CRM & lead funnel',
      'SHA-256 court-admissible e-signatures',
      '18% GST invoicing & PDF generation',
      '1 Workspace seat'
    ]
  },
  starter: {
    key: 'starter',
    name: 'Starter',
    headerColor: '#0b7a4d',
    icon: Rocket,
    monthlyPriceINR: 999,
    annualMonthlyEquivalentINR: 833,
    annualTotalINR: 9990,
    monthlyPriceUSD: 9,
    annualMonthlyEquivalentUSD: 7.5,
    annualTotalUSD: 90,
    description: 'For independent operators establishing their brand with custom domains.',
    features: [
      '5,000 AI Runs / month (6,000 on annual)',
      'Connect custom domain (.com / .in)',
      'Custom business email sending',
      'Up to 2 team seats',
      '100% white-label client view',
      '2 Months Free with Annual Billing'
    ]
  },
  pro: {
    key: 'pro',
    name: 'Professional',
    badge: 'Recommended',
    headerColor: '#be006b',
    icon: Sparkles,
    monthlyPriceINR: 2499,
    annualMonthlyEquivalentINR: 2083,
    annualTotalINR: 24990,
    monthlyPriceUSD: 24,
    annualMonthlyEquivalentUSD: 20,
    annualTotalUSD: 240,
    description: 'Autonomous backbone with advanced AI, official WhatsApp dispatch & UPI QR.',
    features: [
      'Advanced AI Reasoning Engine',
      '15,000 AI Runs / month',
      'Official WhatsApp automated dispatch',
      'Dynamic UPI QR code on invoices (0% fee)',
      'Automated GST tax splits (CGST/SGST/IGST)',
      'Up to 5 team seats & granular roles',
      '2 Months Free with Annual Billing'
    ]
  },
  scale: {
    key: 'scale',
    name: 'Scale',
    badge: 'Agencies & Scale',
    headerColor: '#5438dc',
    icon: TrendingUp,
    monthlyPriceINR: 4499,
    annualMonthlyEquivalentINR: 3749,
    annualTotalINR: 44990,
    monthlyPriceUSD: 45,
    annualMonthlyEquivalentUSD: 37.5,
    annualTotalUSD: 450,
    description: 'High-throughput infrastructure for agencies & multi-member teams.',
    exclusiveBonus: 'Free Custom Website Development or Migration included with dedicated technical onboarding.',
    features: [
      'Free Custom Website Development or Migration',
      'All Frontier AI Engines (GPT-4o, Claude 3.5, Gemini 1.5 Pro)',
      '25,000 AI Runs / month',
      'Autonomous AI Research Agent',
      'Unlimited* team seats',
      'Custom Webhooks & REST API access',
      'Dedicated account manager & SLA'
    ]
  }
};

export function CheckoutSheet({
  isOpen,
  onClose,
  initialPlan = 'pro',
  initialBilling = 'annual',
  currency = 'INR'
}: CheckoutSheetProps) {
  const [selectedPlan, setSelectedPlan] = useState<PlanKey>(initialPlan);
  const [billingCycle, setBillingCycle] = useState<BillingCycle>(initialBilling);
  const [hasGstin, setHasGstin] = useState(false);
  const [gstinNumber, setGstinNumber] = useState('');
  const [businessName, setBusinessName] = useState('');
  const [taxState, setTaxState] = useState<'intra' | 'inter'>('intra');
  const [showTaxBreakdown, setShowTaxBreakdown] = useState(true);

  // Sync state when props change
  useEffect(() => {
    if (initialPlan) setSelectedPlan(initialPlan);
  }, [initialPlan]);

  useEffect(() => {
    if (initialBilling) setBillingCycle(initialBilling);
  }, [initialBilling]);

  // Lock body scroll when drawer is open
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [isOpen]);

  if (!isOpen) return null;

  const plan = PLAN_CATALOG[selectedPlan];
  const isAnnual = billingCycle === 'annual';
  const isINR = currency === 'INR';

  // Math Calculations (Base price is exclusive of taxes)
  const isFree = selectedPlan === 'free';
  
  let basePrice = 0;
  let fullMonthlyAnnualTotal = 0;
  let annualDiscount = 0;
  
  if (!isFree) {
    if (isINR) {
      if (isAnnual) {
        fullMonthlyAnnualTotal = plan.monthlyPriceINR * 12;
        basePrice = plan.annualTotalINR;
        annualDiscount = fullMonthlyAnnualTotal - basePrice; // 2 Months Free (₹1,998 / ₹4,998 / ₹8,998)
      } else {
        basePrice = plan.monthlyPriceINR;
      }
    } else {
      if (isAnnual) {
        fullMonthlyAnnualTotal = plan.monthlyPriceUSD * 12;
        basePrice = plan.annualTotalUSD;
        annualDiscount = fullMonthlyAnnualTotal - basePrice;
      } else {
        basePrice = plan.monthlyPriceUSD;
      }
    }
  }

  // 18% GST (Standard Indian B2B SaaS Tax rate)
  const gstRate = 0.18;
  const totalTax = isFree ? 0 : basePrice * gstRate;
  const cgst = isFree ? 0 : totalTax / 2; // 9%
  const sgst = isFree ? 0 : totalTax / 2; // 9%
  const igst = isFree ? 0 : totalTax;     // 18%
  const grandTotal = basePrice + totalTax;

  const handleProceed = () => {
    trackEvent('checkout_proceed', {
      plan: selectedPlan,
      billing: billingCycle,
      basePrice,
      totalTax,
      grandTotal,
      currency
    });

    const params = new URLSearchParams({
      plan: selectedPlan,
      billing: billingCycle,
      currency,
      tax_state: taxState
    });

    if (hasGstin && gstinNumber) {
      params.set('gstin', gstinNumber.trim().toUpperCase());
      if (businessName) params.set('company', businessName.trim());
    }

    window.location.href = `https://app.heycora.in/workspace/login?${params.toString()}`;
  };

  return (
    <div className="fixed inset-0 z-[100] flex justify-end">
      {/* Backdrop Overlay */}
      <div 
        onClick={onClose}
        className="fixed inset-0 bg-zinc-950/50 backdrop-blur-xs transition-opacity duration-300"
        aria-hidden="true"
      />

      {/* Drawer / Bottom Sheet Container (Bottom sheet on mobile, right drawer on md+) */}
      <div className="relative z-10 w-full md:max-w-[580px] h-[92vh] md:h-full mt-auto md:mt-0 bg-white rounded-t-3xl md:rounded-none shadow-2xl flex flex-col justify-between overflow-hidden animate-in slide-in-from-bottom md:slide-in-from-right duration-300 border-l border-zinc-200">
        
        {/* Mobile Pull Handle */}
        <div className="md:hidden pt-3 pb-1 flex justify-center shrink-0">
          <div className="w-12 h-1.5 rounded-full bg-zinc-300" />
        </div>

        {/* Drawer Header */}
        <div className="px-5 py-4 sm:px-7 sm:py-5 border-b border-zinc-200/80 bg-white/95 backdrop-blur-md sticky top-0 z-20 flex items-center justify-between">
          <div className="space-y-0.5">
            <div className="flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              <span className="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500">
                Transparent Checkout &bull; 18% GST Invoice
              </span>
            </div>
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight">
              Order Summary &amp; Tax Split
            </h2>
          </div>
          <button
            onClick={onClose}
            type="button"
            className="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-zinc-200 flex items-center justify-center text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-colors cursor-pointer"
            aria-label="Close checkout drawer"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Drawer Scrollable Content */}
        <div className="px-5 py-4 sm:px-7 sm:py-6 overflow-y-auto flex-1 space-y-5">
          
          {/* 1. Plan Selector Pills */}
          <div className="space-y-2">
            <label className="text-xs font-semibold text-zinc-700 block">
              Select Plan:
            </label>
            <div className="grid grid-cols-4 gap-1.5 p-1 bg-zinc-100 rounded-2xl border border-zinc-200/80">
              {(Object.keys(PLAN_CATALOG) as PlanKey[]).map((key) => {
                const p = PLAN_CATALOG[key];
                const isSelected = selectedPlan === key;
                return (
                  <button
                    key={key}
                    type="button"
                    onClick={() => {
                      setSelectedPlan(key);
                      trackEvent('checkout_plan_change', { plan: key });
                    }}
                    className={`py-2 px-1 text-center rounded-xl transition-all cursor-pointer ${
                      isSelected
                        ? 'bg-zinc-950 text-white shadow-xs font-bold'
                        : 'text-zinc-600 hover:text-zinc-950 text-xs font-medium'
                    }`}
                  >
                    <div className="text-[11px] sm:text-xs truncate">{p.name}</div>
                    <div className="text-[9px] sm:text-[10px] opacity-80 font-mono">
                      {key === 'free' 
                        ? '₹0' 
                        : (isINR 
                            ? `₹${isAnnual ? p.annualMonthlyEquivalentINR : p.monthlyPriceINR}` 
                            : `$${isAnnual ? p.annualMonthlyEquivalentUSD : p.monthlyPriceUSD}`)}
                    </div>
                  </button>
                );
              })}
            </div>
          </div>

          {/* 2. Billing Cycle Switcher */}
          {!isFree && (
            <div className="flex items-center justify-between p-3 rounded-2xl bg-zinc-50 border border-zinc-200/80">
              <div className="space-y-0.5">
                <div className="text-xs font-bold text-zinc-950">Billing Cycle</div>
                <div className="text-[11px] text-zinc-500 font-normal">
                  {isAnnual ? 'Paying for 10 months (2 Months Free included)' : 'Monthly billing (Cancel anytime)'}
                </div>
              </div>

              <div className="inline-flex items-center p-0.5 bg-zinc-200/70 rounded-xl">
                <button
                  type="button"
                  onClick={() => setBillingCycle('monthly')}
                  className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-all ${
                    !isAnnual ? 'bg-white text-zinc-950 shadow-2xs' : 'text-zinc-600 hover:text-zinc-950'
                  }`}
                >
                  Monthly
                </button>
                <button
                  type="button"
                  onClick={() => setBillingCycle('annual')}
                  className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1 ${
                    isAnnual ? 'bg-zinc-950 text-white shadow-2xs' : 'text-zinc-600 hover:text-zinc-950'
                  }`}
                >
                  <span>Annual</span>
                  <span className="bg-emerald-500 text-white text-[9px] font-bold px-1.5 py-0.2 rounded-full">
                    2 Mo. Free
                  </span>
                </button>
              </div>
            </div>
          )}

          {/* 3. Scale Plan Special Bonus Callout */}
          {selectedPlan === 'scale' && (
            <div className="p-3.5 rounded-2xl bg-gradient-to-r from-purple-50 via-indigo-50 to-purple-50 border border-purple-200 flex items-start gap-3 shadow-2xs">
              <div className="w-8 h-8 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                <Gift className="w-4 h-4" />
              </div>
              <div className="space-y-0.5 min-w-0">
                <div className="text-xs font-bold text-purple-950 flex items-center gap-1.5">
                  <span>SCALE PLAN EXCLUSIVE BONUS</span>
                  <span className="px-1.5 py-0.2 rounded bg-purple-200/80 text-[9px] font-mono font-extrabold text-purple-900">FREE</span>
                </div>
                <div className="text-[11px] sm:text-xs text-purple-900 leading-relaxed font-medium">
                  Includes <strong>100% Free Custom Website Development or Migration</strong> by our engineering team. We migrate your site, design, and forms with zero downtime.
                </div>
              </div>
            </div>
          )}

          {/* 4. Plan Summary Card */}
          <div className="p-4 rounded-2xl bg-white border border-zinc-200/90 shadow-2xs space-y-3">
            <div className="flex items-center justify-between pb-3 border-b border-zinc-100">
              <div className="flex items-center gap-2.5">
                <div 
                  className="w-8 h-8 rounded-xl flex items-center justify-center text-white shadow-2xs"
                  style={{ backgroundColor: plan.headerColor }}
                >
                  <plan.icon className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-sm font-bold text-zinc-950">{plan.name}</h3>
                  <span className="text-[11px] text-zinc-500 font-mono">
                    {isFree ? 'Zero Cost Forever' : (isAnnual ? 'Annual Subscription' : 'Monthly Subscription')}
                  </span>
                </div>
              </div>
              <div className="text-right">
                <div className="text-base font-bold text-zinc-950 font-display">
                  {isFree 
                    ? '₹0' 
                    : (isINR 
                        ? `₹${basePrice.toLocaleString('en-IN')}` 
                        : `$${basePrice}`)}
                </div>
                <div className="text-[10px] text-zinc-400 font-mono">
                  {isFree ? 'No card needed' : (isAnnual ? '/ year (excl. tax)' : '/ month (excl. tax)')}
                </div>
              </div>
            </div>

            {/* Feature Pills */}
            <div className="space-y-1.5">
              <div className="text-[11px] font-mono text-zinc-500 font-bold uppercase tracking-wider">
                Plan Inclusions:
              </div>
              <ul className="space-y-1.5 text-xs text-zinc-700">
                {plan.features.slice(0, 4).map((feat, idx) => (
                  <li key={idx} className="flex items-start gap-2">
                    <Check className="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" />
                    <span className="text-[11.5px] leading-snug">{feat}</span>
                  </li>
                ))}
              </ul>
            </div>
          </div>

          {/* 5. Tax Split & Calculation Engine */}
          {!isFree && (
            <div className="p-4 rounded-2xl bg-zinc-50/90 border border-zinc-200/80 space-y-3">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <Receipt className="w-4 h-4 text-zinc-700" />
                  <span className="text-xs font-bold text-zinc-950">GST Tax Calculation</span>
                </div>
                
                {/* State Tax Toggle (Intra vs Inter) */}
                <div className="flex items-center gap-1.5 text-[10px] font-mono">
                  <button
                    type="button"
                    onClick={() => setTaxState('intra')}
                    className={`px-2 py-0.5 rounded-md transition-all ${
                      taxState === 'intra'
                        ? 'bg-zinc-950 text-white font-bold'
                        : 'bg-zinc-200/70 text-zinc-600'
                    }`}
                    title="Intra-State (CGST 9% + SGST 9%)"
                  >
                    CGST+SGST (9%+9%)
                  </button>
                  <button
                    type="button"
                    onClick={() => setTaxState('inter')}
                    className={`px-2 py-0.5 rounded-md transition-all ${
                      taxState === 'inter'
                        ? 'bg-zinc-950 text-white font-bold'
                        : 'bg-zinc-200/70 text-zinc-600'
                    }`}
                    title="Inter-State (IGST 18%)"
                  >
                    IGST (18%)
                  </button>
                </div>
              </div>

              {/* Price Calculation Rows */}
              <div className="space-y-2 text-xs pt-1 border-t border-zinc-200/60 font-mono">
                {isAnnual && annualDiscount > 0 && (
                  <>
                    <div className="flex justify-between text-zinc-500">
                      <span>Standard 12-Month Rate:</span>
                      <span className="line-through">
                        {isINR ? `₹${fullMonthlyAnnualTotal.toLocaleString('en-IN')}` : `$${fullMonthlyAnnualTotal}`}
                      </span>
                    </div>
                    <div className="flex justify-between text-emerald-700 font-semibold">
                      <span className="flex items-center gap-1">
                        <Gift className="w-3 h-3 text-emerald-600" />
                        <span>2 Months Free Discount:</span>
                      </span>
                      <span>
                        -{isINR ? `₹${annualDiscount.toLocaleString('en-IN')}` : `$${annualDiscount}`}
                      </span>
                    </div>
                  </>
                )}

                <div className="flex justify-between text-zinc-700">
                  <span>Taxable Base Subtotal:</span>
                  <span className="font-semibold text-zinc-950">
                    {isINR ? `₹${basePrice.toLocaleString('en-IN')}` : `$${basePrice}`}
                  </span>
                </div>

                {taxState === 'intra' ? (
                  <>
                    <div className="flex justify-between text-zinc-500 text-[11px]">
                      <span>CGST (9.0%):</span>
                      <span>{isINR ? `₹${cgst.toFixed(2)}` : `$${cgst.toFixed(2)}`}</span>
                    </div>
                    <div className="flex justify-between text-zinc-500 text-[11px]">
                      <span>SGST (9.0%):</span>
                      <span>{isINR ? `₹${sgst.toFixed(2)}` : `$${sgst.toFixed(2)}`}</span>
                    </div>
                  </>
                ) : (
                  <div className="flex justify-between text-zinc-500 text-[11px]">
                    <span>IGST (18.0%):</span>
                    <span>{isINR ? `₹${igst.toFixed(2)}` : `$${igst.toFixed(2)}`}</span>
                  </div>
                )}

                <div className="pt-2 border-t border-zinc-200 flex justify-between items-baseline text-sm font-bold text-zinc-950">
                  <span>Total Amount Payable:</span>
                  <span className="text-base text-zinc-950 font-display">
                    {isINR ? `₹${grandTotal.toFixed(2)}` : `$${grandTotal.toFixed(2)}`}
                  </span>
                </div>
              </div>

              <div className="text-[10px] text-zinc-400 leading-tight pt-1">
                * All base plan prices are exclusive of applicable Indian GST (18%). An official GST invoice with QR &amp; HSN/SAC code is generated instantly.
              </div>
            </div>
          )}

          {/* 6. Optional Business GSTIN for Input Tax Credit */}
          {!isFree && (
            <div className="p-3.5 rounded-2xl bg-white border border-zinc-200/80 space-y-2">
              <div className="flex items-center justify-between">
                <label className="text-xs font-semibold text-zinc-800 flex items-center gap-1.5 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={hasGstin}
                    onChange={(e) => setHasGstin(e.target.checked)}
                    className="rounded border-zinc-300 text-zinc-950 focus:ring-zinc-950"
                  />
                  <span>I have a GSTIN (Claim 18% Input Tax Credit)</span>
                </label>
                <span className="text-[10px] font-mono text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded">
                  B2B Tax Credit
                </span>
              </div>

              {hasGstin && (
                <div className="pt-2 space-y-2 animate-in fade-in duration-150">
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                      <label className="text-[10px] font-mono text-zinc-500 block mb-1">GSTIN Number *</label>
                      <input
                        type="text"
                        maxLength={15}
                        value={gstinNumber}
                        onChange={(e) => setGstinNumber(e.target.value.toUpperCase())}
                        placeholder="27AAAAA0000A1Z5"
                        className="w-full px-3 py-1.5 rounded-lg border border-zinc-200 text-xs font-mono uppercase text-zinc-900 focus:outline-none focus:border-zinc-950"
                      />
                    </div>
                    <div>
                      <label className="text-[10px] font-mono text-zinc-500 block mb-1">Company / Studio Name</label>
                      <input
                        type="text"
                        value={businessName}
                        onChange={(e) => setBusinessName(e.target.value)}
                        placeholder="Apex Media Pvt Ltd"
                        className="w-full px-3 py-1.5 rounded-lg border border-zinc-200 text-xs text-zinc-900 focus:outline-none focus:border-zinc-950"
                      />
                    </div>
                  </div>
                  <div className="text-[10px] text-emerald-700 font-mono">
                    ✓ You can claim {isINR ? `₹${totalTax.toFixed(2)}` : `$${totalTax.toFixed(2)}`} as ITC in your monthly GSTR-3B filing.
                  </div>
                </div>
              )}
            </div>
          )}

          {/* 7. Security Trust & Compliance Badges */}
          <div className="grid grid-cols-3 gap-2 text-center text-[10px] font-mono text-zinc-500 pt-1">
            <div className="p-2 rounded-xl bg-zinc-50 border border-zinc-100 flex flex-col items-center justify-center gap-1">
              <ShieldCheck className="w-4 h-4 text-emerald-600" />
              <span>AES-256 Encrypted</span>
            </div>
            <div className="p-2 rounded-xl bg-zinc-50 border border-zinc-100 flex flex-col items-center justify-center gap-1">
              <Receipt className="w-4 h-4 text-zinc-800" />
              <span>GST Tax Invoice</span>
            </div>
            <div className="p-2 rounded-xl bg-zinc-50 border border-zinc-100 flex flex-col items-center justify-center gap-1">
              <Lock className="w-4 h-4 text-zinc-800" />
              <span>Cancel Anytime</span>
            </div>
          </div>

        </div>

        {/* Drawer Sticky Bottom Action Footer */}
        <div className="p-4 sm:p-6 border-t border-zinc-200 bg-white/95 backdrop-blur-md sticky bottom-0 z-20 space-y-2">
          <div className="flex items-center justify-between text-xs">
            <div>
              <span className="text-zinc-500">Total payable today: </span>
              <strong className="text-zinc-950 font-mono text-sm sm:text-base">
                {isFree ? '₹0 (Free)' : (isINR ? `₹${grandTotal.toFixed(2)}` : `$${grandTotal.toFixed(2)}`)}
              </strong>
            </div>
            {!isFree && (
              <span className="text-[11px] text-zinc-400 font-mono">
                Includes 18% GST ({isINR ? `₹${totalTax.toFixed(2)}` : `$${totalTax.toFixed(2)}`})
              </span>
            )}
          </div>

          <button
            type="button"
            onClick={handleProceed}
            className="w-full py-3.5 sm:py-4 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs sm:text-sm transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer group active:scale-[0.99]"
          >
            <span>{isFree ? 'Create Free Workspace Now' : `Proceed to Workspace & Payment →`}</span>
            <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:translate-x-0.5 group-hover:text-white transition-all" />
          </button>

          <p className="text-[10px] text-zinc-400 text-center font-mono">
            {isFree 
              ? 'Instant setup &bull; No credit card required &bull; Full data ownership' 
              : 'Instant activation &bull; 100% money back guarantee &bull; Indian IT Act & DPDP compliant'}
          </p>
        </div>

      </div>
    </div>
  );
}
