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
  Layers,
  Bot
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
}

export interface Message {
  id: string;
  sender: 'user' | 'assistant';
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
    label: 'Web Design & Dev', 
    query: 'How does Cora help web design & dev agencies deliver client portals and 18% GST invoices?' 
  },
  { 
    id: 'creative_branding',
    icon: Palette, 
    label: 'Creative & Branding', 
    query: 'How does Cora help creative studios deliver brand assets, lock scopes, and collect milestone payments?' 
  },
  { 
    id: 'marketing_seo',
    icon: TrendingUp, 
    label: 'Performance & SEO', 
    query: 'How does Cora help marketing agencies handle monthly client retainers and 18% GST invoices?' 
  },
  { 
    id: 'software_app',
    icon: Code2, 
    label: 'App & Software Dev', 
    query: 'How does Cora help software studios manage sprint contracts, milestones, and client handoffs?' 
  },
  { 
    id: 'production_media',
    icon: Camera, 
    label: 'Media & Production', 
    query: 'How does Cora help media production teams manage shoot contracts, call sheets, and client delivery?' 
  },
];

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

  // 0. Gibberish
  if (isGibberish(q)) {
    return {
      text: `Let me help point you in the right direction! Ask me anything about how Cora replaces separate tools, automates 18% GST invoices, or sets up client portals.`,
      highlights: [
        { title: 'Branded Client Portals', desc: 'Deliver clients a private workspace alongside their website', badge: 'Client Hub' },
        { title: '18% GST & UPI Invoicing', desc: 'Automated milestone invoices with instant UPI QR & SAC 9983 splits', badge: 'Billing' },
      ],
      quickReplies: [
        { label: 'Web Design Agencies', query: 'How does Cora help web design & dev agencies?' },
        { label: 'Replace PandaDoc & Notion', query: 'How does Cora replace PandaDoc and Notion?' },
        { label: 'Pricing Plans', query: 'What are the pricing plans for Cora?' },
      ],
      ctaText: 'Explore Free Agency Workspace (₹0) →',
      ctaLink: 'https://app.heycora.in/workspace/login?plan=free_forever&source=modal_gibberish',
    };
  }

  // 1. Web Design & Development Agencies
  if (q.includes('web') || q.includes('website') || q.includes('wordpress') || q.includes('webflow') || q.includes('framer') || q.includes('developer') || q.includes('shopify') || q.includes('elementor')) {
    return {
      text: `When you build a client website, bundle Cora as their ready-to-use client portal. You replace $113/mo in tool subscriptions while creating an easy new revenue stream for client portal setups.`,
      planRecommendation: {
        name: 'India Only Plan',
        price: '₹499/mo',
        billingText: 'Billed annually (₹4,999/yr) • Includes Free .in Domain',
        badge: 'Recommended for Web Agencies',
        savingsBadge: 'Replaces 4 Subscriptions',
        features: [
          'Deliver branded client portals with every website build',
          'Lock scopes with SHA-256 digital milestone sign-offs',
          'Automated SAC 9983 18% GST invoices + instant UPI QR',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&agency=web_design&source=modal_web',
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
        { title: 'Branded Client Portals', desc: 'Clients get a private portal to view assets, approve milestones, and pay invoices', badge: 'Client Hub' },
        { title: 'Scope-Lock Contracts', desc: 'SHA-256 e-sign prevents unpaid extra revisions before staging launch', badge: 'Zero Creep' },
      ],
      quickReplies: [
        { label: 'How to charge clients for portals?', query: 'How do agencies package and charge clients for Cora portals?' },
        { label: '18% GST Invoice Demo', query: 'Show me an 18% GST web development invoice breakdown' },
        { label: 'Free Forever Plan', query: 'How does the Free Forever plan work?' },
      ],
    };
  }

  // 2. Creative, Branding & Design Studios
  if (q.includes('creative') || q.includes('brand') || q.includes('design') || q.includes('logo') || q.includes('graphic') || q.includes('ui/ux') || q.includes('ux') || q.includes('figma')) {
    return {
      text: `Deliver brand decks in a luxury client review portal instead of messy emails. Lock 50% advance payments with dynamic UPI QR codes and auto-generate legal copyright deeds.`,
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
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&agency=branding&source=modal_creative',
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
        { title: 'Asset Proofing Portal', desc: 'Clean deck reviews with digital sign-off', badge: 'Client Proofing' },
        { title: '50% Advance Lock', desc: 'Clients settle deposit via UPI/card before source files unlock', badge: 'Cashflow' },
      ],
      quickReplies: [
        { label: 'Copyright Transfer Deeds', query: 'How does Cora generate intellectual property contracts?' },
        { label: 'Check Pricing Options', query: 'What are the pricing plans for Cora?' },
        { label: 'Free Plan Setup', query: 'Can I test this on the Free Forever plan?' },
      ],
    };
  }

  // 3. Performance Marketing, Ads & SEO Agencies
  if (q.includes('marketing') || q.includes('seo') || q.includes('ad') || q.includes('meta ads') || q.includes('google ads') || q.includes('retainer') || q.includes('growth')) {
    return {
      text: `Automate your monthly client retainers with ease. Cora automatically dispatches recurring 18% GST invoices on the 1st of every month with 1-click UPI payment links directly over WhatsApp.`,
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
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&agency=marketing&source=modal_marketing',
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
        { title: '1st-of-Month Retainers', desc: 'Clients receive GST bills and UPI links automatically', badge: 'Autopilot' },
        { title: 'Friendly Reminders', desc: 'Polite, automated WhatsApp reminder sequences for on-time payments', badge: 'Cash Flow' },
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
      text: `Manage client sprints and milestone contracts in one simple hub. Clients sign off on bi-weekly sprint deliverables with SHA-256 audit trails and pay SAC 9983 compliant invoices.`,
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
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&agency=software&source=modal_software',
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
      text: `Cora replaces your CRM, E-sign tool, invoice generator, and client portal with a single workspace. You cut operational software spend by 90% immediately with zero setup complexity.`,
      planRecommendation: {
        name: 'India Only Plan',
        price: '₹499/mo',
        billingText: 'Billed annually (₹4,999/yr) • 2 Months Free + Free .in Domain',
        badge: 'Best Value for Indian Agencies',
        savingsBadge: 'Instant 95% Cost Reduction',
        features: [
          '3,500 monthly AI runs & proposal generator',
          'Unlimited SHA-256 e-sign contracts (PandaDoc alternative)',
          'Instant UPI QR 18% GST bills (Razorpay/Freshbooks alternative)',
        ],
        checkoutUrl: 'https://app.heycora.in/workspace/login?plan=india_only&cycle=annual&source=modal_pricing',
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
        { title: 'Instant 1-Click Activation', desc: 'Activate your workspace in 30 seconds with 2 months free on annual plans', badge: 'Fast Setup' },
      ],
      quickReplies: [
        { label: 'Start Free Forever (₹0)', query: 'Can I start on the Free Forever plan first?' },
        { label: 'Web Agency Handoffs', query: 'How does Cora help web design agencies deliver portals?' },
        { label: 'WhatsApp Automation', query: 'How do WhatsApp client reminders and call-sheets work?' },
      ],
    };
  }

  // Default Friendly Assistant
  return {
    text: `Cora gives your agency an all-in-one operating workspace. You replace 4 separate subscriptions, protect scopes with digital contracts, and deliver custom client portals effortlessly.`,
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
      checkoutUrl: 'https://app.heycora.in/workspace/login?plan=free_forever&source=modal_default',
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
      { title: 'Zero Setup Overhead', desc: 'Get your team and clients up and running in under 5 minutes', badge: 'Fast Launch' },
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
  const [isOpen, setIsOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [activeAgencyPill, setActiveAgencyPill] = useState<string | null>(null);

  const chatScrollContainerRef = useRef<HTMLDivElement>(null);
  const activeInputRef = useRef<HTMLInputElement>(null);

  // Auto-scroll ONLY internal chat container, never window
  useEffect(() => {
    if (isOpen && messages.length > 0 && chatScrollContainerRef.current) {
      chatScrollContainerRef.current.scrollTo({
        top: chatScrollContainerRef.current.scrollHeight,
        behavior: 'smooth',
      });
    }
  }, [messages, isOpen]);

  // Focus input when opened
  useEffect(() => {
    if (isOpen) {
      setTimeout(() => {
        activeInputRef.current?.focus();
      }, 100);
    }
  }, [isOpen]);

  // Escape key closes discussion
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape' && isOpen) {
        setIsOpen(false);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isOpen]);

  const handleSend = async (textToSend?: string, pillId?: string) => {
    const text = (textToSend || inputValue).trim();
    if (pillId) setActiveAgencyPill(pillId);

    if (!text) {
      setIsOpen(true);
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
    setIsOpen(true);
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
          const assistantMsg: Message = {
            id: (Date.now() + 1).toString(),
            sender: 'assistant',
            text: data.output,
            planRecommendation: data.planRecommendation,
            toolSavings: data.toolSavings,
            highlights: data.highlights,
            quickReplies: data.quickReplies,
            ctaText: data.ctaText,
            ctaLink: data.ctaLink,
            timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
          };
          setMessages((prev) => [...prev, assistantMsg]);
          setIsLoading(false);
          return;
        }
      }
    } catch (e) {
      // Fallback gracefully
    }

    setTimeout(() => {
      const response = getSimpleRichReply(text);
      const assistantMsg: Message = {
        id: (Date.now() + 1).toString(),
        sender: 'assistant',
        text: response.text,
        planRecommendation: response.planRecommendation,
        toolSavings: response.toolSavings,
        highlights: response.highlights,
        quickReplies: response.quickReplies,
        ctaText: response.ctaText,
        ctaLink: response.ctaLink,
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      };
      setMessages((prev) => [...prev, assistantMsg]);
      setIsLoading(false);
    }, 200);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (inputValue.trim()) {
      handleSend(inputValue);
    } else {
      setIsOpen(true);
    }
  };

  const handleReset = () => {
    setMessages([]);
    setInputValue('');
    setActiveAgencyPill(null);
    trackEvent('hero_ai_chat_reset');
  };

  return (
    <div className="w-full max-w-[840px] mx-auto text-left relative z-30">
      
      {/* ══════════════════════════════════════════════════════════════════════
          1. ANCHORED DISCUSSION POPOVER (Floats directly above the transformed card)
      ══════════════════════════════════════════════════════════════════════ */}
      {isOpen && (
        <div className="absolute bottom-[calc(100%+12px)] left-0 right-0 z-40 w-full max-h-[46vh] sm:max-h-[380px] bg-white/95 backdrop-blur-2xl rounded-2xl sm:rounded-3xl border border-zinc-200/90 shadow-[0_20px_50px_-10px_rgba(0,0,0,0.2)] ring-1 ring-black/[0.05] flex flex-col overflow-hidden animate-in slide-in-from-bottom-3 fade-in duration-200">
          
          {/* Header Bar */}
          <div className="flex items-center justify-between px-3.5 sm:px-4 py-2 bg-zinc-50/90 border-b border-zinc-100 shrink-0">
            <div className="flex items-center gap-2">
              <div className="w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center shadow-xs">
                <Sparkles className="w-3 h-3 text-emerald-400" />
              </div>
              <div className="flex items-center gap-1.5">
                <span className="text-xs font-bold text-zinc-950">Cora AI</span>
                <span className="px-1.5 py-0.2 text-[9px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-full">
                  Agency Assistant
                </span>
              </div>
            </div>

            <div className="flex items-center gap-1">
              {messages.length > 0 && (
                <button
                  type="button"
                  onClick={handleReset}
                  title="Reset Conversation"
                  className="p-1 rounded-md text-zinc-400 hover:text-zinc-900 hover:bg-zinc-200/70 transition-colors cursor-pointer"
                >
                  <RotateCcw className="w-3.5 h-3.5" />
                </button>
              )}
              <button
                type="button"
                onClick={() => setIsOpen(false)}
                title="Close discussion"
                className="p-1 rounded-md text-zinc-400 hover:text-zinc-900 hover:bg-zinc-200/70 transition-colors cursor-pointer"
              >
                <X className="w-3.5 h-3.5" />
              </button>
            </div>
          </div>

          {/* Scrollable Chat Stream */}
          <div 
            ref={chatScrollContainerRef}
            className="flex-1 overflow-y-auto p-3.5 sm:p-4 space-y-3 overscroll-contain scrollbar-thin scrollbar-thumb-zinc-200 text-left"
          >
            {messages.length === 0 ? (
              <div className="py-5 text-center text-zinc-500 text-xs max-w-[380px] mx-auto space-y-1.5">
                <p className="font-medium text-zinc-800">
                  Hi! Ask me anything about replacing fragmented tools, delivering client portals, or GST invoicing.
                </p>
                <p className="text-[11px] text-zinc-400">
                  Type a question below or tap an agency topic:
                </p>
              </div>
            ) : (
              messages.map((msg) => (
                <div
                  key={msg.id}
                  className={`flex flex-col ${msg.sender === 'user' ? 'items-end' : 'items-start'} gap-1`}
                >
                  <div
                    className={`max-w-[92%] sm:max-w-[88%] rounded-2xl p-3 sm:p-3.5 text-xs sm:text-[13px] leading-relaxed ${
                      msg.sender === 'user'
                        ? 'bg-zinc-950 text-white rounded-br-xs font-medium shadow-2xs'
                        : 'bg-zinc-50 text-zinc-900 rounded-bl-xs border border-zinc-200/90 font-normal shadow-2xs'
                    }`}
                  >
                    {/* Message Content */}
                    <p className={`whitespace-pre-line ${msg.sender === 'user' ? 'text-zinc-100' : 'text-zinc-900'} leading-relaxed`}>
                      {msg.text}
                    </p>

                    {/* Tool Replacement ROI Badge */}
                    {msg.toolSavings && (
                      <div className="mt-2.5 p-2.5 rounded-xl bg-white border border-zinc-200/90 text-left shadow-2xs">
                        <div className="flex items-center justify-between gap-1 pb-1.5 border-b border-zinc-100">
                          <span className="text-[10.5px] font-bold text-zinc-800 flex items-center gap-1">
                            <Layers className="w-3 h-3 text-zinc-600" />
                            <span>Replaces Separate Tools:</span>
                          </span>
                          <span className="px-1.5 py-0.5 text-[9px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded">
                            {msg.toolSavings.annualSavings}
                          </span>
                        </div>
                        <div className="flex flex-wrap gap-1 mt-1.5">
                          {msg.toolSavings.replaced.map((t, idx) => (
                            <span key={idx} className="px-1.5 py-0.5 text-[9.5px] font-medium bg-zinc-100 text-zinc-700 rounded line-through decoration-zinc-400">
                              {t.name} ({t.cost})
                            </span>
                          ))}
                        </div>
                      </div>
                    )}

                    {/* Compact Plan Recommendation & 1-Click Checkout */}
                    {msg.planRecommendation && (
                      <div className="mt-2.5 p-2.5 rounded-xl bg-white border border-zinc-300 shadow-2xs text-left space-y-2">
                        <div className="flex items-center justify-between gap-1">
                          <span className="px-2 py-0.5 text-[9.5px] font-bold bg-zinc-950 text-white rounded flex items-center gap-1">
                            <Sparkles className="w-2.5 h-2.5 text-emerald-400" />
                            <span>{msg.planRecommendation.badge || 'Recommended Plan'}</span>
                          </span>
                          <span className="text-xs font-bold text-zinc-950 font-mono">{msg.planRecommendation.price}</span>
                        </div>

                        <a
                          href={msg.planRecommendation.checkoutUrl}
                          className="w-full py-2 px-3 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold flex items-center justify-between shadow-xs transition-all hover:-translate-y-0.5 cursor-pointer"
                        >
                          <span>{msg.planRecommendation.checkoutText}</span>
                          <ArrowRight className="w-3.5 h-3.5" />
                        </a>
                      </div>
                    )}

                    {/* Standalone CTA link */}
                    {msg.ctaText && msg.ctaLink && !msg.planRecommendation && (
                      <div className="mt-2 pt-0.5">
                        <a
                          href={msg.ctaLink}
                          className="inline-flex items-center gap-1 px-3 py-1.5 bg-zinc-950 text-white rounded-md text-xs font-semibold hover:bg-zinc-800 transition-colors shadow-2xs"
                        >
                          <span>{msg.ctaText}</span>
                          <ArrowRight className="w-3 h-3" />
                        </a>
                      </div>
                    )}
                  </div>

                  {/* Quick Reply Suggestion Buttons */}
                  {msg.quickReplies && msg.quickReplies.length > 0 && (
                    <div className="flex flex-wrap gap-1 pt-0.5">
                      {msg.quickReplies.map((qr, idx) => (
                        <button
                          key={idx}
                          type="button"
                          onClick={() => handleSend(qr.query)}
                          className="text-[10.5px] font-medium bg-white hover:bg-zinc-100 text-zinc-800 px-2.5 py-1 rounded-full border border-zinc-200 shadow-2xs transition-colors cursor-pointer"
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
              <div className="flex items-center gap-2 text-zinc-400 text-xs py-1 pl-1">
                <div className="w-1.5 h-1.5 rounded-full bg-zinc-400 animate-pulse" />
                <span>Cora is thinking...</span>
              </div>
            )}
          </div>

          {/* Topic Pills inside discussion card */}
          <div className="px-3 py-1.5 border-t border-zinc-100 overflow-x-auto scrollbar-none bg-zinc-50/50 shrink-0">
            <div className="flex items-center gap-1.5 min-w-max">
              {agencyPills.map((pill) => {
                const IconComp = pill.icon;
                return (
                  <button
                    key={pill.id}
                    type="button"
                    onClick={() => handleSend(pill.query, pill.id)}
                    className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white hover:bg-zinc-100 active:bg-zinc-200 text-zinc-800 text-[10px] font-medium border border-zinc-200/90 transition-colors cursor-pointer shadow-2xs"
                  >
                    <IconComp className="w-2.5 h-2.5 text-zinc-600" />
                    <span>{pill.label}</span>
                  </button>
                );
              })}
            </div>
          </div>
        </div>
      )}

      {/* ══════════════════════════════════════════════════════════════════════
          2. THE ONE TRANSFORMED IN-HERO INPUT CARD
      ══════════════════════════════════════════════════════════════════════ */}
      <div 
        className={`w-full bg-white/95 backdrop-blur-xl border border-white/80 rounded-2xl sm:rounded-[32px] p-3.5 sm:p-5 shadow-[0px_16px_48px_rgba(0,0,0,0.08)] ring-1 ring-black/[0.04] transition-all hover:shadow-[0px_20px_56px_rgba(0,0,0,0.11)] ${
          isOpen ? 'ring-2 ring-zinc-950/20 shadow-[0px_20px_56px_rgba(0,0,0,0.12)]' : ''
        }`}
      >
        {/* Top Input Bar */}
        <form onSubmit={handleSubmit} className="relative flex items-center justify-between gap-2.5 sm:gap-3 pb-2.5 sm:pb-3 border-b border-zinc-100/90">
          <input
            ref={activeInputRef}
            type="text"
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            onClick={() => setIsOpen(true)}
            onFocus={() => setIsOpen(true)}
            placeholder="Ask anything... e.g. How do agencies deliver client portals?"
            className="w-full bg-transparent text-xs sm:text-sm md:text-[14.5px] font-sans text-zinc-950 placeholder:text-zinc-400 focus:outline-none tracking-tight"
          />

          {/* Right Action: Send button when open, or Google/Brand icons when closed */}
          {isOpen ? (
            <button
              type="submit"
              className="w-7 h-7 rounded-full bg-zinc-950 text-white flex items-center justify-center shrink-0 shadow-xs hover:bg-zinc-800 transition-colors cursor-pointer"
            >
              <Send className="w-3.5 h-3.5" />
            </button>
          ) : (
            <div className="flex items-center gap-1.5 shrink-0" onClick={() => setIsOpen(true)}>
              <div className="w-6 h-6 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-600 text-[10px] font-bold cursor-pointer">
                ✦
              </div>
              <div className="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold cursor-pointer">
                G
              </div>
            </div>
          )}
        </form>

        {/* Bottom Action Row Inside Card */}
        <div className="flex items-center justify-between pt-2.5 sm:pt-3 text-xs">
          <span 
            onClick={() => setIsOpen(true)}
            className="text-zinc-500 text-[11px] sm:text-[11.5px] font-medium truncate pr-2 cursor-pointer hover:text-zinc-800 transition-colors"
          >
            {isOpen ? 'Cora AI is active • Instant answers' : 'Ask our friendly AI • No signup needed'}
          </span>

          <div className="flex items-center gap-1.5">
            {isOpen ? (
              <button
                type="button"
                onClick={() => setIsOpen(false)}
                className="px-3 py-1 text-zinc-500 hover:text-zinc-900 rounded-full text-xs font-medium transition-colors cursor-pointer"
              >
                Close
              </button>
            ) : (
              <button
                type="button"
                onClick={() => {
                  if (inputValue.trim()) {
                    handleSend(inputValue);
                  } else {
                    setIsOpen(true);
                  }
                }}
                className="px-3.5 sm:px-4 py-1.5 bg-zinc-900 hover:bg-zinc-950 text-white rounded-full text-xs font-semibold transition-all hover:-translate-y-0.5 flex items-center gap-1.5 shadow-2xs cursor-pointer shrink-0"
              >
                <Sparkles className="w-3.5 h-3.5 text-emerald-400" />
                <span>Ask AI</span>
              </button>
            )}
          </div>
        </div>
      </div>

      {/* Center-Aligned Agency Type Chips Below */}
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

    </div>
  );
}
