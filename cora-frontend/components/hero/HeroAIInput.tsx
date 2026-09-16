'use client';

import React, { useState, useRef, useEffect } from 'react';
import { createPortal } from 'react-dom';
import {
  Sparkles,
  ArrowRight,
  RotateCcw,
  Globe,
  Palette,
  Code2,
  TrendingUp,
  Camera,
  X,
  Send,
  Check,
  Zap,
  CheckCircle2,
  ChevronRight,
  Layers,
  ExternalLink
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export interface PlanRecommendation {
  name: string;
  price: string;
  billingText?: string;
  badge?: string;
  savingsBadge?: string;
  features: string[];
  checkoutUrl: string;
  checkoutText: string;
}

export interface ToolSavings {
  replaced: { name: string; cost: string }[];
  totalReplacedCost: string;
  coraCost: string;
  annualSavings: string;
}

export interface FeatureHighlight {
  title: string;
  desc: string;
  badge?: string;
}

export interface QuickReply {
  label: string;
  query: string;
  iconName?: string;
}

export interface Message {
  id: string;
  sender: 'user' | 'sales_agent';
  text: string;
  planRecommendation?: PlanRecommendation;
  toolSavings?: ToolSavings;
  highlights?: FeatureHighlight[];
  quickReplies?: QuickReply[];
  ctaText?: string;
  ctaLink?: string;
  timestamp: string;
}

const agencyPills = [
  { 
    id: 'web_design',
    icon: Globe, 
    label: 'Web Design & Dev Agencies', 
    query: 'How does Cora help web design & dev agencies deliver client portals and 18% GST invoices?' 
  },
  { 
    id: 'creative_branding',
    icon: Palette, 
    label: 'Creative & Branding Studios', 
    query: 'How does Cora help creative studios deliver brand assets, lock scopes, and collect milestone payments?' 
  },
  { 
    id: 'marketing_seo',
    icon: TrendingUp, 
    label: 'Performance & SEO Agencies', 
    query: 'How does Cora help marketing agencies handle monthly client retainers and 18% GST invoices?' 
  },
  { 
    id: 'software_app',
    icon: Code2, 
    label: 'App & Software Dev Shops', 
    query: 'How does Cora help software studios manage sprint contracts, milestones, and client handoffs?' 
  },
  { 
    id: 'production_media',
    icon: Camera, 
    label: 'Media & Production Houses', 
    query: 'How does Cora help media production teams manage shoot contracts, call sheets, and client delivery?' 
  },
];

// Helper to detect nonsensical / gibberish typing
function isGibberish(str: string): boolean {
  const clean = str.trim().toLowerCase().replace(/[^a-z]/g, '');
  if (clean.length < 4) return false;

  const vowels = clean.match(/[aeiou]/g) || [];
  const vowelRatio = vowels.length / clean.length;
  if (vowelRatio < 0.12 || vowelRatio > 0.85) return true;

  if (/[bcdfghjklmnpqrstvwxyz]{5,}/.test(clean)) return true;
  if (/(.)\1{3,}/.test(clean)) return true;

  return false;
}

function getSimpleRichReply(query: string): {
  text: string;
  planRecommendation?: PlanRecommendation;
  toolSavings?: ToolSavings;
  highlights?: FeatureHighlight[];
  quickReplies?: QuickReply[];
  ctaText?: string;
  ctaLink?: string;
} {
  const original = query.trim();
  const q = original.toLowerCase();

  // 0. Gibberish / Random keyboard mash detector
  if (isGibberish(q)) {
    return {
      text: `Let me help point you in the right direction. I consult web, creative, and software agencies on replacing expensive tool stacks and automating client portals.`,
      highlights: [
        { title: 'Branded Client Portals', desc: 'Deliver clients a private workspace alongside their website', badge: 'Revenue Boost' },
        { title: '18% GST & UPI Invoicing', desc: 'Automated milestone invoices with instant UPI QR & SAC 9983 splits', badge: 'Billing' },
      ],
      quickReplies: [
        { label: 'Web Design Agencies', query: 'How does Cora help web design & dev agencies?' },
        { label: 'Replace PandaDoc & Notion', query: 'How does Cora replace PandaDoc and Notion?' },
        { label: 'Pricing Plans', query: 'What are the pricing plans for Cora?' },
      ],
      ctaText: 'Explore Free Agency Workspace (₹0) →',
      ctaLink: 'https://app.heycora.in/workspace/login?plan=free_forever&source=sdr_gibberish',
    };
  }

  // 1. Web Design & Development Agencies
  if (q.includes('web') || q.includes('website') || q.includes('wordpress') || q.includes('webflow') || q.includes('framer') || q.includes('developer') || q.includes('shopify') || q.includes('elementor')) {
    return {
      text: `When you build a client website, bundle Cora as their ready-to-use client portal. You eliminate $113/mo in tool subscriptions while creating a new ₹25,000 revenue stream per project for workspace setup.`,
      planRecommendation: {
        name: 'India Only Plan',
        price: '₹499/mo',
        billingText: 'Billed annually (₹4,999/yr) • Includes Free .in Domain',
        badge: 'Top Choice for Web Agencies',
        savingsBadge: 'Replaces 4 Tools • Saves ₹14,500/mo',
        features: [
          'Deliver branded client portals with every website build',
          'Lock scopes with SHA-256 digital milestone sign-offs',
          'Automated SAC 9983 18% GST invoices + instant UPI QR',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&agency=web_design&source=sdr_web',
        checkoutText: 'Claim India Plan & Launch Portal →',
      },
      toolSavings: {
        replaced: [
          { name: 'PandaDoc / E-Sign', cost: '$49/mo' },
          { name: 'HoneyBook / Portals', cost: '$39/mo' },
          { name: 'Typeform / Intake', cost: '$25/mo' },
        ],
        totalReplacedCost: '$113/mo (~₹9,400/mo)',
        coraCost: '₹499/mo',
        annualSavings: '$1,248/year (~₹1,00,000/yr)',
      },
      highlights: [
        { title: 'Branded Client Portals', desc: 'Clients get a private portal to view assets, approve milestones, and pay invoices', badge: 'New Revenue' },
        { title: 'Scope-Lock Contracts', desc: 'SHA-256 e-sign prevents unpaid extra revisions before staging launch', badge: 'Zero Creep' },
      ],
      quickReplies: [
        { label: 'How to charge clients ₹25k?', query: 'How do agencies package and charge clients for Cora portals?' },
        { label: '18% GST Invoice Demo', query: 'Show me an 18% GST web development invoice breakdown' },
        { label: 'Free Forever Option', query: 'How does the Free Forever plan work?' },
      ],
    };
  }

  // 2. Creative, Branding & Design Studios
  if (q.includes('creative') || q.includes('brand') || q.includes('design') || q.includes('logo') || q.includes('graphic') || q.includes('ui/ux') || q.includes('ux') || q.includes('figma')) {
    return {
      text: `Stop delivering high-res brand decks over cluttered email threads. Cora gives your clients a luxury review portal, locks 50% advance payments via UPI QR, and auto-generates legal copyright deeds.`,
      planRecommendation: {
        name: 'India Only Plan',
        price: '₹499/mo',
        billingText: 'Billed annually (₹4,999/yr) • 2 Months Free',
        badge: 'Tailored for Creative Studios',
        savingsBadge: 'Saves ₹12,000/mo on Subscriptions',
        features: [
          'High-resolution brand asset proofing with threaded client feedback',
          'Advance milestone escrow & dynamic UPI settlement',
          'Automated copyright transfer deeds & NDAs',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&agency=branding&source=sdr_creative',
        checkoutText: 'Start Creative Studio Plan →',
      },
      toolSavings: {
        replaced: [
          { name: 'PandaDoc / E-Sign', cost: '$49/mo' },
          { name: 'WeTransfer Pro / File Hub', cost: '$19/mo' },
          { name: 'Invoicing Tool', cost: '$25/mo' },
        ],
        totalReplacedCost: '$93/mo',
        coraCost: '₹499/mo',
        annualSavings: '$1,000+/yr',
      },
      highlights: [
        { title: 'Asset Proofing Portal', desc: 'Clean, client-branded deck reviews with digital sign-off', badge: 'Client Wow' },
        { title: '50% Advance Lock', desc: 'Clients must settle deposit via UPI/card before source files unlock', badge: 'Cashflow' },
      ],
      quickReplies: [
        { label: 'Copyright Transfer Deeds', query: 'How does Cora generate intellectual property contracts?' },
        { label: 'Check Pricing Options', query: 'What are the pricing plans for Cora?' },
        { label: 'Free Plan Setup', query: 'Can I test this on the Free Forever plan?' },
      ],
    };
  }

  // 3. Performance Marketing, Ads & SEO Agencies
  if (q.includes('marketing') || q.includes('seo') || q.includes('ad') || q.includes('meta ads') || q.includes('google ads') || q.includes('retainer') || q.includes('growth') || q.includes('social media') || q.includes('lead gen')) {
    return {
      text: `Automate your monthly client retainers on autopilot. Cora dispatches recurring 18% GST invoices on the 1st of every month with 1-click UPI payment links directly over WhatsApp.`,
      planRecommendation: {
        name: 'India Only Plan',
        price: '₹499/mo',
        billingText: 'Billed annually (₹4,999/yr) • 2 Months Free',
        badge: 'Best for Marketing & Retainers',
        savingsBadge: 'Auto-bills 1st of every month',
        features: [
          'Automated 1st-of-month 18% GST retainer bills on WhatsApp',
          'Lead Kanban pipeline with WhatsApp team notifications',
          'Ad spend pass-through reconciliation with zero tax confusion',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&agency=marketing&source=sdr_marketing',
        checkoutText: 'Start Marketing Retainers →',
      },
      toolSavings: {
        replaced: [
          { name: 'Subscription Invoicing', cost: '$39/mo' },
          { name: 'CRM Pipeline Tool', cost: '$30/mo' },
          { name: 'WhatsApp Bot Service', cost: '$29/mo' },
        ],
        totalReplacedCost: '$98/mo',
        coraCost: '₹499/mo',
        annualSavings: '$1,068/yr saved',
      },
      highlights: [
        { title: '1st-of-Month Retainer Automation', desc: 'No manual invoicing—clients receive GST bills and UPI links automatically', badge: 'Autopilot' },
        { title: 'Zero Chasing Over WhatsApp', desc: 'Polite, automated payment reminders ensure 98% on-time settlement', badge: 'Cash Flow' },
      ],
      quickReplies: [
        { label: '18% GST Invoice Demo', query: 'Make a ₹45,000 monthly retainer invoice with 18% GST' },
        { label: 'Upgrade to India Plan', query: 'How do I upgrade to the India Only Plan?' },
        { label: 'Web Agency Solutions', query: 'How does Cora help web design & dev agencies?' },
      ],
    };
  }

  // 4. Software & App Development Shops
  if (q.includes('software') || q.includes('app') || q.includes('saas') || q.includes('sprint') || q.includes('dev') || q.includes('tech') || q.includes('api') || q.includes('code')) {
    return {
      text: `Eliminate scope disputes in client software projects. Cora lets clients sign off on bi-weekly sprint milestones, releases advance payments in 1 click, and issues SAC 9983 tax compliant invoices.`,
      planRecommendation: {
        name: 'India Only Plan',
        price: '₹499/mo',
        billingText: 'Billed annually (₹4,999/yr) • 2 Months Free',
        badge: 'Recommended for Dev Shops',
        savingsBadge: 'Saves $1,200+/yr on contracts',
        features: [
          'Bi-weekly sprint milestone sign-offs with SHA-256 audit trails',
          'Automated SAC 9983 software consulting GST invoices',
          'Multi-tenant client portal access for technical stakeholders',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&agency=software&source=sdr_software',
        checkoutText: 'Start Software Studio Workspace →',
      },
      toolSavings: {
        replaced: [
          { name: 'PandaDoc / Legal E-Sign', cost: '$49/mo' },
          { name: 'FreshBooks / Invoices', cost: '$30/mo' },
          { name: 'Client Portal Hub', cost: '$40/mo' },
        ],
        totalReplacedCost: '$119/mo',
        coraCost: '₹499/mo',
        annualSavings: '$1,300+/yr saved',
      },
      highlights: [
        { title: 'Sprint Milestone Contracts', desc: 'Clients sign off on UAT before code release with cryptographic proof', badge: 'UAT Lock' },
        { title: 'IP Assignment Deeds', desc: 'Legally binding copyright and source code transfer deeds', badge: 'Legal SLA' },
      ],
      quickReplies: [
        { label: 'Sprint Milestone Demo', query: 'How does milestone-based software billing work?' },
        { label: 'Web Design Agencies', query: 'How does Cora help web design & dev agencies?' },
        { label: 'Pricing Plans', query: 'What are the pricing plans for Cora?' },
      ],
    };
  }

  // 5. Pricing, Costs & Plan Recommendation
  if (q.includes('price') || q.includes('cost') || q.includes('plan') || q.includes('replace') || q.includes('pandadoc') || q.includes('honeybook') || q.includes('notion')) {
    return {
      text: `Cora replaces your CRM, E-sign tool, invoice generator, and client portal with a single platform. You cut operational software spend by 90% immediately while speeding up client payment cycles to under 24 hours.`,
      planRecommendation: {
        name: 'India Only Plan',
        price: '₹499/mo',
        billingText: 'Billed annually (₹4,999/yr) • 2 Months Free + Free .in Domain',
        badge: 'Maximum Value for Indian Studios',
        savingsBadge: 'Instant 95% Cost Reduction',
        features: [
          '3,500 monthly AI SDR runs & proposal generation',
          'Unlimited SHA-256 e-sign contracts (PandaDoc alternative)',
          'Instant UPI QR 18% GST bills (Razorpay/Freshbooks alternative)',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&source=sdr_pricing',
        checkoutText: 'Upgrade to India Plan (₹499/mo) →',
      },
      toolSavings: {
        replaced: [
          { name: 'PandaDoc / DocuSign', cost: '$49/mo' },
          { name: 'HoneyBook / Dubsado', cost: '$39/mo' },
          { name: 'Typeform / Intake forms', cost: '$25/mo' },
          { name: 'QuickBooks / FreshBooks', cost: '$30/mo' },
        ],
        totalReplacedCost: '$143/mo ($1,716/yr)',
        coraCost: '₹499/mo ($6)',
        annualSavings: '$1,608/year saved',
      },
      highlights: [
        { title: 'Immediate $143/mo Savings', desc: 'Cancel PandaDoc, Typeform, and invoicing subscriptions today', badge: 'Cost Saver' },
        { title: 'Instant 1-Click Checkout', desc: 'Activate your workspace in 30 seconds with 2 months free on annual plans', badge: 'Fast Launch' },
      ],
      quickReplies: [
        { label: 'Start Free Forever (₹0)', query: 'Can I start on the Free Forever plan first?' },
        { label: 'Web Agency Handoffs', query: 'How does Cora help web design agencies deliver portals?' },
        { label: 'WhatsApp Automation', query: 'How do WhatsApp client reminders and call-sheets work?' },
      ],
    };
  }

  // Default Agency Sales Consultant
  return {
    text: `Cora gives your agency an all-in-one operating workspace. You replace 4 fragmented subscriptions, stop unpaid revisions with locked milestone contracts, and deliver custom client portals.`,
    planRecommendation: {
      name: 'Free Forever Plan',
      price: '₹0 Forever',
      billingText: 'No Credit Card Required • Instant Activation',
      badge: 'Zero Risk Starter Tier',
      savingsBadge: '1,000 Free AI Runs / Month',
      features: [
        '1,000 monthly AI agent runs & proposal generator',
        'Unlimited SHA-256 digital signature contracts',
        '18% GST tax invoices with instant UPI QR payments',
      ],
      checkoutUrl: 'https://app.heycora.in/workspace/login?plan=free_forever&source=sdr_default',
      checkoutText: 'Start Free Forever Workspace →',
    },
    toolSavings: {
      replaced: [
        { name: 'PandaDoc', cost: '$49/mo' },
        { name: 'HoneyBook', cost: '$39/mo' },
        { name: 'Typeform', cost: '$25/mo' },
      ],
      totalReplacedCost: '$113/mo',
      coraCost: '$0',
      annualSavings: '$1,356/yr saved',
    },
    highlights: [
      { title: 'Unified Agency Workspace', desc: 'Manage proposals, client portals, and GST invoices in one screen', badge: 'All-In-One' },
      { title: 'Zero Onboarding Overhead', desc: 'Get your team and clients up and running in under 5 minutes', badge: 'Fast Launch' },
    ],
    quickReplies: [
      { label: 'Web Design Agencies', query: 'How does Cora help web design & dev agencies?' },
      { label: 'India Only Plan (₹499/mo)', query: 'What is included in the India Only Plan?' },
      { label: 'Replace PandaDoc & Notion', query: 'How does Cora replace PandaDoc and Notion?' },
    ],
  };
}

export function HeroAIInput() {
  const [inputValue, setInputValue] = useState('');
  const [messages, setMessages] = useState<Message[]>([]);
  const [isExpanded, setIsExpanded] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [activeAgencyPill, setActiveAgencyPill] = useState<string | null>(null);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  const messagesEndRef = useRef<HTMLDivElement>(null);
  const chatScrollContainerRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  // Auto-scroll chat to latest message
  useEffect(() => {
    if (isExpanded && messages.length > 0) {
      messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }
  }, [messages, isExpanded]);

  // Prevent background body scrolling when mobile drawer is open
  useEffect(() => {
    if (typeof window !== 'undefined' && window.innerWidth < 640) {
      if (isExpanded) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    }
    return () => {
      if (typeof window !== 'undefined') {
        document.body.style.overflow = '';
      }
    };
  }, [isExpanded]);

  // Isolate scroll within the chat container
  const handleChatWheel = (e: React.WheelEvent<HTMLDivElement>) => {
    const container = chatScrollContainerRef.current;
    if (!container) return;

    const { scrollTop, scrollHeight, clientHeight } = container;
    const isScrollable = scrollHeight > clientHeight;

    if (isScrollable) {
      const isAtTop = scrollTop === 0 && e.deltaY < 0;
      const isAtBottom = scrollTop + clientHeight >= scrollHeight - 1 && e.deltaY > 0;

      if (!isAtTop && !isAtBottom) {
        e.stopPropagation();
      }
    }
  };

  const handleSend = async (textToSend?: string, pillId?: string) => {
    const text = (textToSend || inputValue).trim();
    if (pillId) setActiveAgencyPill(pillId);

    if (!text) {
      setIsExpanded(true);
      return;
    }

    trackEvent('hero_ai_prompt_submitted', { query: text });

    const userMsg: Message = {
      id: Date.now().toString(),
      sender: 'user',
      text,
      timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    };

    setMessages((prev) => [...prev, userMsg]);
    setInputValue('');
    setIsExpanded(true);
    setIsLoading(true);

    try {
      const res = await fetch('/api/ai-preview', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prompt: text }),
      });

      if (res.ok) {
        const data = await res.json();
        if (data.output) {
          const sdrMsg: Message = {
            id: (Date.now() + 1).toString(),
            sender: 'sales_agent',
            text: data.output,
            planRecommendation: data.planRecommendation,
            toolSavings: data.toolSavings,
            highlights: data.highlights,
            quickReplies: data.quickReplies,
            ctaText: data.ctaText,
            ctaLink: data.ctaLink,
            timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
          };
          setMessages((prev) => [...prev, sdrMsg]);
          setIsLoading(false);
          return;
        }
      }
    } catch (e) {
      // Fallback gracefully
    }

    setTimeout(() => {
      const response = getSimpleRichReply(text);
      const sdrMsg: Message = {
        id: (Date.now() + 1).toString(),
        sender: 'sales_agent',
        text: response.text,
        planRecommendation: response.planRecommendation,
        toolSavings: response.toolSavings,
        highlights: response.highlights,
        quickReplies: response.quickReplies,
        ctaText: response.ctaText,
        ctaLink: response.ctaLink,
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      };
      setMessages((prev) => [...prev, sdrMsg]);
      setIsLoading(false);
    }, 200);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    handleSend();
  };

  const handleReset = () => {
    setMessages([]);
    setInputValue('');
    setActiveAgencyPill(null);
    trackEvent('hero_ai_chat_reset');
  };

  const handleCollapse = () => {
    setIsExpanded(false);
  };

  return (
    <div className="w-full max-w-[840px] mx-auto text-left relative z-20">
      
      {/* ── Main Input Card ── */}
      <div 
        className={`w-full bg-white/95 backdrop-blur-xl border border-white/80 rounded-2xl sm:rounded-[32px] p-3.5 sm:p-6 transition-all duration-300 ease-out ${
          isExpanded 
            ? 'shadow-[0px_24px_70px_rgba(0,0,0,0.12)] ring-1 ring-black/[0.06] -translate-y-0.5' 
            : 'shadow-[0px_16px_48px_rgba(0,0,0,0.08)] ring-1 ring-black/[0.04]'
        }`}
      >
        
        {/* Desktop / In-place Header Bar when expanded */}
        {isExpanded && (
          <div className="hidden sm:flex items-center justify-between pb-3 mb-3 border-b border-zinc-100/90 animate-in fade-in duration-200">
            <div className="flex items-center gap-2">
              <div className="w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center shadow-2xs">
                <Sparkles className="w-3.5 h-3.5 text-emerald-400" />
              </div>
              <span className="text-xs sm:text-sm font-bold text-zinc-950">Cora AI Growth Consultant</span>
              <span className="px-2 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-full">
                Live Sales Partner • Instant Setup
              </span>
            </div>

            <div className="flex items-center gap-1.5">
              {messages.length > 0 && (
                <button
                  type="button"
                  onClick={handleReset}
                  title="Reset Conversation"
                  className="w-7 h-7 rounded-full text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 flex items-center justify-center transition-colors cursor-pointer"
                >
                  <RotateCcw className="w-3.5 h-3.5" />
                </button>
              )}
              <button
                type="button"
                onClick={handleCollapse}
                title="Collapse Card"
                className="w-7 h-7 rounded-full text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 flex items-center justify-center transition-colors cursor-pointer"
              >
                <X className="w-4 h-4" />
              </button>
            </div>
          </div>
        )}

        {/* Desktop Expanded Chat Feed */}
        {isExpanded && (
          <div 
            ref={chatScrollContainerRef}
            onWheel={handleChatWheel}
            className="hidden sm:block my-3 max-h-[380px] overflow-y-auto overscroll-contain pr-1.5 space-y-4 scrollbar-thin scrollbar-thumb-zinc-200"
          >
            {messages.length === 0 ? (
              <div className="py-4 text-center text-zinc-500 text-xs">
                Ask how Cora replaces your agency tool stack, automates 18% GST invoices, and unlocks client portals:
              </div>
            ) : (
              messages.map((msg) => (
                <div
                  key={msg.id}
                  className={`flex flex-col ${msg.sender === 'user' ? 'items-end' : 'items-start'} gap-1.5`}
                >
                  <div
                    className={`max-w-[92%] sm:max-w-[88%] rounded-2xl p-4 text-xs sm:text-sm leading-relaxed ${
                      msg.sender === 'user'
                        ? 'bg-zinc-950 text-white rounded-br-xs font-medium'
                        : 'bg-zinc-50/80 text-zinc-900 rounded-bl-xs border border-zinc-200/80 font-normal shadow-2xs'
                    }`}
                  >
                    {/* Active Voice Message Text */}
                    <p className="whitespace-pre-line text-zinc-900 font-normal leading-relaxed">{msg.text}</p>

                    {/* ── Dynamic Tool Replacement & ROI Savings Card ── */}
                    {msg.toolSavings && (
                      <div className="mt-3 p-3 rounded-xl bg-white border border-zinc-200/90 text-left">
                        <div className="flex items-center justify-between gap-2 pb-2 border-b border-zinc-100">
                          <span className="text-[11px] font-bold text-zinc-900 flex items-center gap-1.5">
                            <Layers className="w-3.5 h-3.5 text-zinc-700" />
                            <span>Replaces Fragmented Subscriptions:</span>
                          </span>
                          <span className="px-1.5 py-0.5 text-[9.5px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded">
                            {msg.toolSavings.annualSavings}
                          </span>
                        </div>
                        <div className="flex flex-wrap gap-1.5 mt-2">
                          {msg.toolSavings.replaced.map((t, idx) => (
                            <span key={idx} className="px-2 py-0.5 text-[10.5px] font-medium bg-zinc-100/80 text-zinc-700 rounded-md line-through decoration-zinc-400">
                              {t.name} ({t.cost})
                            </span>
                          ))}
                        </div>
                      </div>
                    )}

                    {/* ── Dynamic Plan Recommendation & Direct 1-Click Checkout Card ── */}
                    {msg.planRecommendation && (
                      <div className="mt-3 p-3.5 rounded-2xl bg-white border border-zinc-300 shadow-sm text-left">
                        {/* Header Badges */}
                        <div className="flex items-center justify-between gap-2 mb-2">
                          <span className="px-2 py-0.5 text-[10px] font-bold bg-zinc-950 text-white rounded-md flex items-center gap-1">
                            <Sparkles className="w-3 h-3 text-emerald-400" />
                            <span>{msg.planRecommendation.badge || 'Recommended Plan'}</span>
                          </span>
                          {msg.planRecommendation.savingsBadge && (
                            <span className="px-2 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                              {msg.planRecommendation.savingsBadge}
                            </span>
                          )}
                        </div>

                        {/* Plan Name & Big Price Tag */}
                        <div className="flex items-baseline justify-between gap-2 mt-2 pt-1 border-t border-zinc-100">
                          <div>
                            <h4 className="text-sm font-bold text-zinc-950">{msg.planRecommendation.name}</h4>
                            {msg.planRecommendation.billingText && (
                              <p className="text-[11px] text-zinc-500 font-medium">{msg.planRecommendation.billingText}</p>
                            )}
                          </div>
                          <div className="text-right">
                            <span className="text-lg font-bold text-zinc-950 font-mono tracking-tight">{msg.planRecommendation.price}</span>
                          </div>
                        </div>

                        {/* Key Features Checklist */}
                        {msg.planRecommendation.features && msg.planRecommendation.features.length > 0 && (
                          <div className="mt-2.5 space-y-1.5 pt-2 border-t border-zinc-100 text-[11.5px] text-zinc-700">
                            {msg.planRecommendation.features.map((feat, i) => (
                              <div key={i} className="flex items-start gap-2">
                                <div className="w-4 h-4 rounded-full bg-zinc-100 flex items-center justify-center shrink-0 mt-0.5">
                                  <Check className="w-2.5 h-2.5 text-zinc-900 stroke-[2.5]" />
                                </div>
                                <span className="leading-snug">{feat}</span>
                              </div>
                            ))}
                          </div>
                        )}

                        {/* Direct 1-Click Checkout CTA Button */}
                        <a
                          href={msg.planRecommendation.checkoutUrl}
                          className="mt-3.5 w-full py-2.5 px-4 bg-zinc-950 hover:bg-zinc-800 text-white rounded-xl text-xs font-semibold flex items-center justify-between shadow-xs transition-all hover:-translate-y-0.5 cursor-pointer"
                        >
                          <span>{msg.planRecommendation.checkoutText}</span>
                          <ArrowRight className="w-3.5 h-3.5" />
                        </a>
                      </div>
                    )}

                    {/* Feature Highlights Grid (Fallback/Secondary) */}
                    {msg.highlights && msg.highlights.length > 0 && !msg.planRecommendation && (
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-3 pt-3 border-t border-zinc-200/60">
                        {msg.highlights.map((h, i) => (
                          <div key={i} className="p-2.5 rounded-xl bg-white border border-zinc-200/80 text-left">
                            <div className="flex items-center justify-between gap-1 mb-1">
                              <span className="text-[11.5px] font-bold text-zinc-950">{h.title}</span>
                              {h.badge && (
                                <span className="px-1.5 py-0.5 text-[9px] font-mono font-bold bg-zinc-100 text-zinc-700 rounded-md">
                                  {h.badge}
                                </span>
                              )}
                            </div>
                            <p className="text-[11px] text-zinc-600 leading-normal">{h.desc}</p>
                          </div>
                        ))}
                      </div>
                    )}

                    {/* Standard CTA Link if standalone */}
                    {msg.ctaText && msg.ctaLink && !msg.planRecommendation && (
                      <div className="mt-3 pt-1">
                        <a
                          href={msg.ctaLink}
                          className="inline-flex items-center gap-1.5 px-3.5 py-2 bg-zinc-950 text-white rounded-lg text-xs font-semibold hover:bg-zinc-800 transition-colors shadow-2xs"
                        >
                          <span>{msg.ctaText}</span>
                          <ArrowRight className="w-3.5 h-3.5" />
                        </a>
                      </div>
                    )}
                  </div>

                  {/* Interactive Quick Replies */}
                  {msg.quickReplies && msg.quickReplies.length > 0 && (
                    <div className="flex flex-wrap gap-1.5 pt-1">
                      {msg.quickReplies.map((qr, idx) => (
                        <button
                          key={idx}
                          type="button"
                          onClick={() => handleSend(qr.query)}
                          className="text-[11px] font-medium bg-white hover:bg-zinc-100 text-zinc-800 px-2.5 py-1 rounded-full border border-zinc-200 shadow-2xs transition-colors cursor-pointer"
                        >
                          {qr.label}
                        </button>
                      ))}
                    </div>
                  )}
                </div>
              ))
            )}

            {isLoading && (
              <div className="flex items-center gap-2 text-zinc-400 text-xs py-1 pl-2">
                <div className="w-2 h-2 rounded-full bg-zinc-400 animate-pulse" />
                <span>Consulting agency growth models...</span>
              </div>
            )}

            <div ref={messagesEndRef} />
          </div>
        )}

        {/* Input Bar Area */}
        <form onSubmit={handleSubmit} className="relative flex items-center justify-between gap-2.5 sm:gap-3 pb-2.5 sm:pb-3 border-b border-zinc-100/90">
          <input
            ref={inputRef}
            type="text"
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            onFocus={() => {
              if (!isExpanded) setIsExpanded(true);
            }}
            placeholder="Ask anything... e.g. How do agencies deliver client portals?"
            className="w-full bg-transparent text-xs sm:text-sm md:text-[14.5px] font-sans text-zinc-950 placeholder:text-zinc-400 focus:outline-none tracking-tight"
          />

          {/* Right Circular Brand Badges */}
          <div className="flex items-center gap-1.5 shrink-0">
            <div className="w-6 h-6 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-600 text-[10px] font-bold">
              ✦
            </div>
            <div className="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">
              G
            </div>
          </div>
        </form>

        {/* Bottom Action Row Inside Card */}
        <div className="flex items-center justify-between pt-2.5 sm:pt-3 text-xs">
          <span className="text-zinc-500 text-[11px] sm:text-[11.5px] font-medium truncate pr-2">
            Ask our AI Agency Growth Consultant &bull; No signup needed
          </span>

          <button
            type="button"
            onClick={() => handleSend()}
            className="px-3.5 sm:px-4 py-1.5 bg-zinc-900 hover:bg-zinc-950 text-white rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer shrink-0"
          >
            <Sparkles className="w-3.5 h-3.5 text-emerald-400" />
            <span>Consult</span>
          </button>
        </div>

      </div>

      {/* ── Center-Aligned Agency Type Chips (Horizontally scrollable with zero clipping on mobile) ── */}
      <div className="mt-3.5 sm:mt-4 w-full overflow-x-auto pb-1 scrollbar-none">
        <div className="flex items-center sm:justify-center gap-1.5 sm:gap-2 min-w-max px-1">
          {agencyPills.map((pill) => {
            const IconComp = pill.icon;
            const isSelected = activeAgencyPill === pill.id;
            return (
              <button
                key={pill.id}
                type="button"
                onClick={() => handleSend(pill.query, pill.id)}
                className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] sm:text-xs font-medium transition-all hover:-translate-y-0.5 shadow-2xs cursor-pointer ${
                  isSelected
                    ? 'bg-zinc-950 text-white border border-zinc-950'
                    : 'bg-white/95 hover:bg-white text-zinc-800 hover:text-zinc-950 border border-zinc-200/90 hover:border-zinc-400'
                }`}
              >
                <IconComp className={`w-3.5 h-3.5 ${isSelected ? 'text-emerald-400' : 'text-zinc-700'}`} />
                <span>{pill.label}</span>
              </button>
            );
          })}
        </div>
      </div>

      {/* ══════════════════════════════════════════════════════════════════════
          MOBILE BOTTOM SLIDE-UP SHEET (Zero Layout Shift, Perfect Ergonomics)
      ══════════════════════════════════════════════════════════════════════ */}
      {isExpanded && mounted && createPortal(
        <div className="sm:hidden fixed inset-0 z-[9999] flex flex-col justify-end">
          
          {/* Backdrop Blur Overlay */}
          <div 
            className="fixed inset-0 bg-zinc-950/70 backdrop-blur-md animate-in fade-in duration-200"
            onClick={handleCollapse}
          />

          {/* Bottom Slide-Up Drawer Sheet */}
          <div className="relative z-10 w-full max-h-[88vh] bg-white rounded-t-3xl px-4 pt-3 pb-6 flex flex-col shadow-[0_-16px_48px_rgba(0,0,0,0.4)] border-t border-zinc-200/90 animate-in slide-in-from-bottom duration-300">
            
            {/* Top Drag Handle Indicator */}
            <div className="flex justify-center pb-2.5">
              <div className="w-12 h-1.5 rounded-full bg-zinc-300" />
            </div>

            {/* Mobile Sheet Header */}
            <div className="flex items-center justify-between pb-3 border-b border-zinc-100/90">
              <div className="flex items-center gap-2.5">
                <div className="w-7 h-7 rounded-full bg-zinc-950 text-white flex items-center justify-center shadow-xs">
                  <Sparkles className="w-4 h-4 text-emerald-400" />
                </div>
                <div>
                  <span className="text-[13px] font-bold text-zinc-950 leading-tight block">Cora AI Growth Consultant</span>
                  <span className="text-[10px] text-zinc-500 font-medium">Live Sales Partner &bull; No Signup</span>
                </div>
              </div>

              <div className="flex items-center gap-1.5">
                {messages.length > 0 && (
                  <button
                    type="button"
                    onClick={handleReset}
                    className="p-1.5 rounded-full text-zinc-400 hover:text-zinc-900 bg-zinc-100 flex items-center justify-center cursor-pointer"
                  >
                    <RotateCcw className="w-3.5 h-3.5" />
                  </button>
                )}
                <button
                  type="button"
                  onClick={handleCollapse}
                  className="p-1.5 rounded-full text-zinc-400 hover:text-zinc-900 bg-zinc-100 flex items-center justify-center cursor-pointer"
                >
                  <X className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* Mobile Chat Feed (Scrollable) */}
            <div className="flex-1 overflow-y-auto my-3 space-y-3 pr-1 max-h-[55vh]">
              {messages.length === 0 ? (
                <div className="py-6 text-center text-zinc-500 text-xs px-4">
                  Select an agency type below or type any question about replacing tools, proposals, and client portals:
                </div>
              ) : (
                messages.map((msg) => (
                  <div
                    key={msg.id}
                    className={`flex flex-col ${msg.sender === 'user' ? 'items-end' : 'items-start'} gap-1`}
                  >
                    <div
                      className={`max-w-[92%] rounded-2xl p-3 text-xs leading-relaxed ${
                        msg.sender === 'user'
                          ? 'bg-zinc-950 text-white rounded-br-xs font-medium'
                          : 'bg-zinc-50 text-zinc-900 rounded-bl-xs border border-zinc-200/80 font-normal shadow-2xs'
                      }`}
                    >
                      <p className="whitespace-pre-line text-zinc-900 font-normal">{msg.text}</p>

                      {/* Tool Replacement on Mobile */}
                      {msg.toolSavings && (
                        <div className="mt-2.5 p-2.5 rounded-xl bg-white border border-zinc-200/90 text-left">
                          <div className="flex items-center justify-between gap-1 pb-1.5 border-b border-zinc-100">
                            <span className="text-[10px] font-bold text-zinc-900 flex items-center gap-1">
                              <Layers className="w-3 h-3 text-zinc-700" />
                              <span>Replaces:</span>
                            </span>
                            <span className="px-1.5 py-0.2 text-[8.5px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded">
                              {msg.toolSavings.annualSavings}
                            </span>
                          </div>
                          <div className="flex flex-wrap gap-1 mt-1.5">
                            {msg.toolSavings.replaced.map((t, idx) => (
                              <span key={idx} className="px-1.5 py-0.5 text-[9.5px] font-medium bg-zinc-100 text-zinc-700 rounded line-through decoration-zinc-400">
                                {t.name}
                              </span>
                            ))}
                          </div>
                        </div>
                      )}

                      {/* Dynamic Plan Recommendation & 1-Click Checkout on Mobile */}
                      {msg.planRecommendation && (
                        <div className="mt-2.5 p-3 rounded-xl bg-white border border-zinc-300 shadow-xs text-left">
                          <div className="flex items-center justify-between gap-1 mb-1.5">
                            <span className="px-1.5 py-0.5 text-[9px] font-bold bg-zinc-950 text-white rounded flex items-center gap-1">
                              <Sparkles className="w-2.5 h-2.5 text-emerald-400" />
                              <span>{msg.planRecommendation.badge || 'Recommended'}</span>
                            </span>
                            {msg.planRecommendation.savingsBadge && (
                              <span className="px-1.5 py-0.5 text-[8.5px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded">
                                {msg.planRecommendation.savingsBadge}
                              </span>
                            )}
                          </div>

                          <div className="flex items-baseline justify-between gap-1 mt-1.5 pt-1 border-t border-zinc-100">
                            <div>
                              <h4 className="text-xs font-bold text-zinc-950">{msg.planRecommendation.name}</h4>
                              {msg.planRecommendation.billingText && (
                                <p className="text-[10px] text-zinc-500 font-medium">{msg.planRecommendation.billingText}</p>
                              )}
                            </div>
                            <span className="text-sm font-bold text-zinc-950 font-mono">{msg.planRecommendation.price}</span>
                          </div>

                          {msg.planRecommendation.features && msg.planRecommendation.features.length > 0 && (
                            <div className="mt-2 space-y-1 pt-1.5 border-t border-zinc-100 text-[10.5px] text-zinc-700">
                              {msg.planRecommendation.features.map((feat, i) => (
                                <div key={i} className="flex items-start gap-1.5">
                                  <Check className="w-3 h-3 text-zinc-900 mt-0.5 shrink-0" />
                                  <span className="leading-tight">{feat}</span>
                                </div>
                              ))}
                            </div>
                          )}

                          <a
                            href={msg.planRecommendation.checkoutUrl}
                            className="mt-3 w-full py-2 px-3 bg-zinc-950 active:bg-zinc-800 text-white rounded-lg text-[11px] font-semibold flex items-center justify-between transition-colors cursor-pointer"
                          >
                            <span>{msg.planRecommendation.checkoutText}</span>
                            <ArrowRight className="w-3 h-3" />
                          </a>
                        </div>
                      )}

                      {/* Highlights fallback on mobile */}
                      {msg.highlights && msg.highlights.length > 0 && !msg.planRecommendation && (
                        <div className="space-y-1.5 mt-2.5 pt-2.5 border-t border-zinc-200/60">
                          {msg.highlights.map((h, i) => (
                            <div key={i} className="p-2 rounded-xl bg-white border border-zinc-200/80 text-left">
                              <div className="flex items-center justify-between gap-1 mb-0.5">
                                <span className="text-[11px] font-bold text-zinc-950">{h.title}</span>
                                {h.badge && (
                                  <span className="px-1.5 py-0.2 text-[8.5px] font-mono font-bold bg-zinc-100 text-zinc-700 rounded">
                                    {h.badge}
                                  </span>
                                )}
                              </div>
                              <p className="text-[10.5px] text-zinc-600 leading-snug">{h.desc}</p>
                            </div>
                          ))}
                        </div>
                      )}

                      {msg.ctaText && msg.ctaLink && !msg.planRecommendation && (
                        <div className="mt-2.5 pt-1">
                          <a
                            href={msg.ctaLink}
                            className="inline-flex items-center gap-1 px-3 py-1.5 bg-zinc-950 text-white rounded-lg text-[11px] font-semibold hover:bg-zinc-800 transition-colors"
                          >
                            <span>{msg.ctaText}</span>
                            <ArrowRight className="w-3 h-3" />
                          </a>
                        </div>
                      )}
                    </div>

                    {msg.quickReplies && msg.quickReplies.length > 0 && (
                      <div className="flex flex-wrap gap-1 pt-1">
                        {msg.quickReplies.map((qr, idx) => (
                          <button
                            key={idx}
                            type="button"
                            onClick={() => handleSend(qr.query)}
                            className="text-[10px] font-medium bg-zinc-100 active:bg-zinc-200 text-zinc-800 px-2 py-0.5 rounded-full border border-zinc-200 transition-colors cursor-pointer"
                          >
                            {qr.label}
                          </button>
                        ))}
                      </div>
                    )}
                  </div>
                ))
              )}

              {isLoading && (
                <div className="flex items-center gap-2 text-zinc-400 text-xs py-1 pl-2">
                  <div className="w-2 h-2 rounded-full bg-zinc-400 animate-pulse" />
                  <span>Consulting agency growth models...</span>
                </div>
              )}

              <div ref={messagesEndRef} />
            </div>

            {/* Mobile Agency Chips inside drawer */}
            <div className="py-2 border-t border-zinc-100 overflow-x-auto pb-1 scrollbar-none">
              <div className="flex items-center gap-1.5 min-w-max">
                {agencyPills.map((pill) => {
                  const IconComp = pill.icon;
                  return (
                    <button
                      key={pill.id}
                      type="button"
                      onClick={() => handleSend(pill.query, pill.id)}
                      className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-zinc-100 active:bg-zinc-200 text-zinc-800 text-[10.5px] font-medium transition-colors cursor-pointer"
                    >
                      <IconComp className="w-3 h-3 text-zinc-600" />
                      <span>{pill.label}</span>
                    </button>
                  );
                })}
              </div>
            </div>

            {/* Mobile Bottom Input Area */}
            <form onSubmit={handleSubmit} className="pt-2 flex items-center gap-2">
              <input
                type="text"
                value={inputValue}
                onChange={(e) => setInputValue(e.target.value)}
                placeholder="Ask about replacing tools or client portals..."
                className="flex-1 bg-zinc-100 rounded-full px-3.5 py-2 text-xs text-zinc-950 placeholder:text-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-950"
              />
              <button
                type="submit"
                className="w-8 h-8 rounded-full bg-zinc-950 text-white flex items-center justify-center shrink-0 shadow-xs cursor-pointer"
              >
                <Send className="w-3.5 h-3.5" />
              </button>
            </form>

          </div>
        </div>,
        document.body
      )}

    </div>
  );
}
