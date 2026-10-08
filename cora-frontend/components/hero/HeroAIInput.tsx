'use client';

import React, { useState, useRef, useEffect } from 'react';
import Link from 'next/link';
import {
  Sparkles,
  RotateCcw,
  Globe,
  Palette,
  Code2,
  TrendingUp,
  Camera,
  X,
  Send,
  Tag,
  Gift,
  Zap,
  ArrowRight,
  ShieldCheck,
  Check,
  Copy,
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export interface DedicatedOffer {
  code: string;
  badge: string;
  title: string;
  description: string;
  actionText: string;
  actionUrl: string;
}

export interface QuickReply {
  label: string;
  query: string;
}

export interface Message {
  id: string;
  sender: 'user' | 'assistant';
  text: string;
  quickReplies?: QuickReply[];
  offer?: DedicatedOffer;
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

const sampleObjections = [
  { label: 'Why not HoneyBook / Notion?', query: 'We already use Notion and HoneyBook. Why should we switch to Cora?' },
  { label: 'Free Website Migration?', query: 'Do you offer free website and client migration if we upgrade today?' },
  { label: 'Annual Discount Deals?', query: 'What discounts and coupon codes are available on annual plans?' },
  { label: 'Why not WordPress + Plugins?', query: 'Why not just build on WordPress with multiple plugins?' },
  { label: 'AI Run Quotas?', query: 'How do monthly AI run quotas work and can I get bonus runs?' },
];

function getSimpleSdrReply(query: string): {
  text: string;
  quickReplies?: QuickReply[];
  offer?: DedicatedOffer;
} {
  const q = query.trim().toLowerCase();

  // 1. Migration Objection
  if (q.includes('migrat') || q.includes('switch') || q.includes('move') || q.includes('import') || q.includes('existing')) {
    return {
      text: `We migrate your entire website, client records, and proposal templates 100% free with zero downtime. Our engineering team ports your workflows within 48 hours so you start billing immediately.`,
      quickReplies: [
        { label: 'Claim Free Migration', query: 'How do I get the free website migration with my upgrade?' },
        { label: '10% Annual Discount', query: 'Is there a discount code on annual plans?' },
        { label: 'Start Free Forever', query: 'Can I start for free without a credit card?' },
      ],
      offer: {
        code: 'MIGRATEFREE',
        badge: 'White-Glove Migration',
        title: '100% Free Website & Client Migration',
        description: 'Upgrade today and our engineering team will migrate your sites and templates for free.',
        actionText: 'Claim Free Migration →',
        actionUrl: 'https://heycora.in/pricing/?coupon=MIGRATEFREE&plan=pro',
      },
    };
  }

  // 2. HoneyBook / Notion / Tool Sprawl Objection
  if (q.includes('honeybook') || q.includes('notion') || q.includes('wordpress') || q.includes('dubsado') || q.includes('hubspot') || q.includes('plugins') || q.includes('already use')) {
    return {
      text: `HoneyBook and Notion don't host websites, build forms, or generate compliant 18% GST SAC 9983 invoices. Cora consolidates your website builder, CRM, SHA-256 e-signatures, and payment links into one tab—cutting your software bill by 60%.`,
      quickReplies: [
        { label: 'Claim 10% Discount', query: 'How do I apply the 10% annual coupon?' },
        { label: 'Free Migration', query: 'Can you migrate our clients from HoneyBook for free?' },
        { label: 'Compare Pricing', query: 'What are the pricing options across plans?' },
      ],
      offer: {
        code: 'CORA10',
        badge: 'Switching Bonus',
        title: 'Extra 10% Off Annual Plan + Free Migration',
        description: 'Replace $110+/mo in fragmented tools with code CORA10 at checkout.',
        actionText: 'Apply 10% Discount →',
        actionUrl: 'https://heycora.in/pricing/?coupon=CORA10&plan=pro',
      },
    };
  }

  // 3. Pricing / Cost / Discount Inquiries
  if (q.includes('price') || q.includes('cost') || q.includes('discount') || q.includes('coupon') || q.includes('deal') || q.includes('expensive') || q.includes('annual')) {
    return {
      text: `You can start completely free with no credit card. When you are ready to scale, use coupon CORA10 to lock in an additional 10% discount on top of 2 months free on our annual plan.`,
      quickReplies: [
        { label: 'Apply 10% Off', query: 'How do I redeem the 10% annual discount code?' },
        { label: 'Free Website Migration', query: 'Does the annual plan include free website migration?' },
        { label: '500 Bonus AI Runs', query: 'How do I claim 500 bonus AI runs?' },
      ],
      offer: {
        code: 'CORA10',
        badge: 'Limited SDR Offer',
        title: '10% Instant Discount + 2 Months Free',
        description: 'Lock in full client portals, digital contracts, and GST invoicing for only ₹449/mo ($8/mo).',
        actionText: 'Claim 10% Off Now →',
        actionUrl: 'https://heycora.in/pricing/?coupon=CORA10&plan=pro',
      },
    };
  }

  // 4. AI Runs & Quotas
  if (q.includes('ai run') || q.includes('quota') || q.includes('limit') || q.includes('token') || q.includes('ai limit')) {
    return {
      text: `Cora gives you 15,000 monthly AI runs on Professional and 25,000 on Scale with zero hourly rate limits. Upgrade today with code BONUS500 to receive 500 additional AI runs in your first month.`,
      quickReplies: [
        { label: 'Claim 500 Bonus AI Runs', query: 'How do I activate the 500 bonus AI runs?' },
        { label: 'Free Website Migration', query: 'Is website migration included with Professional?' },
        { label: 'Pricing Plans', query: 'What are the pricing options across plans?' },
      ],
      offer: {
        code: 'BONUS500',
        badge: 'AI Power Boost',
        title: '+500 Bonus AI Runs for Month 1',
        description: 'Get extra AI capacity for website generation, proposal drafts, and SEO copy.',
        actionText: 'Unlock Bonus AI Runs →',
        actionUrl: 'https://heycora.in/pricing/?coupon=BONUS500&plan=pro',
      },
    };
  }

  // 5. Default SDR Response
  return {
    text: `Cora unifies your website, client leads, digital contracts, and 18% GST billing in one place so you never lose revenue to workflow sprawl. You can start completely free or upgrade today with free white-glove migration included.`,
    quickReplies: [
      { label: 'Free Website Migration', query: 'How does free website migration work if we switch?' },
      { label: '10% Annual Discount', query: 'What discounts are available on annual plans?' },
      { label: 'Compare vs HoneyBook', query: 'How does Cora compare to HoneyBook and Notion?' },
    ],
    offer: {
      code: 'CORA10',
      badge: 'Exclusive Deal Desk',
      title: '10% Off Annual Plan + Free Migration',
      description: 'Use code CORA10 at checkout to unlock discounted annual pricing and free migration.',
      actionText: 'Explore Offers & Pricing →',
      actionUrl: 'https://heycora.in/pricing/?coupon=CORA10',
    },
  };
}

export function HeroAIInput() {
  const [inputValue, setInputValue] = useState('');
  const [messages, setMessages] = useState<Message[]>([]);
  const [isOpen, setIsOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [activeAgencyPill, setActiveAgencyPill] = useState<string | null>(null);
  const [copiedCode, setCopiedCode] = useState<string | null>(null);

  const chatScrollContainerRef = useRef<HTMLDivElement>(null);
  const activeInputRef = useRef<HTMLInputElement>(null);

  // Auto-scroll internal chat container
  useEffect(() => {
    if (isOpen && messages.length > 0 && chatScrollContainerRef.current) {
      chatScrollContainerRef.current.scrollTo({
        top: chatScrollContainerRef.current.scrollHeight,
        behavior: 'smooth',
      });
    }
  }, [messages, isOpen]);

  // Focus input when SDR modal opens
  useEffect(() => {
    if (isOpen) {
      setTimeout(() => {
        activeInputRef.current?.focus();
      }, 100);
    }
  }, [isOpen]);

  // Escape key closes SDR bar
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape' && isOpen) {
        setIsOpen(false);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isOpen]);

  const handleCopyCode = (code: string) => {
    navigator.clipboard.writeText(code);
    setCopiedCode(code);
    setTimeout(() => setCopiedCode(null), 2000);
  };

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
            quickReplies: data.quickReplies,
            offer: data.offer,
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
      const response = getSimpleSdrReply(text);
      const assistantMsg: Message = {
        id: (Date.now() + 1).toString(),
        sender: 'assistant',
        text: response.text,
        quickReplies: response.quickReplies,
        offer: response.offer,
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      };
      setMessages((prev) => [...prev, assistantMsg]);
      setIsLoading(false);
    }, 180);
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
    <>
      {/* ── 1. COMPACT HERO INPUT BAR (Never expands hero section height) ── */}
      <div className="w-full max-w-[840px] mx-auto text-left relative z-20">
        
        {/* The Natural Hero Input Card */}
        <div 
          className="w-full bg-white/95 backdrop-blur-xl border border-white/80 rounded-2xl sm:rounded-[32px] p-3.5 sm:p-5 shadow-[0px_16px_48px_rgba(0,0,0,0.08)] ring-1 ring-black/[0.04] transition-all hover:shadow-[0px_20px_56px_rgba(0,0,0,0.11)]"
        >
          {/* Top Input Bar Trigger */}
          <form onSubmit={handleSubmit} className="relative flex items-center justify-between gap-2.5 sm:gap-3 pb-2.5 sm:pb-3 border-b border-zinc-100/90">
            <input
              type="text"
              value={inputValue}
              onChange={(e) => setInputValue(e.target.value)}
              onClick={() => setIsOpen(true)}
              onFocus={() => setIsOpen(true)}
              placeholder="Ask anything... e.g. Why should we switch from HoneyBook to Cora?"
              className="w-full bg-transparent text-xs sm:text-sm md:text-[14.5px] font-sans text-zinc-950 placeholder:text-zinc-400 focus:outline-none tracking-tight cursor-pointer"
            />

            {/* Right Circular Brand Badges */}
            <div className="flex items-center gap-1.5 shrink-0" onClick={() => setIsOpen(true)}>
              <div className="w-6 h-6 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-600 text-[10px] font-bold cursor-pointer">
                ✦
              </div>
              <div className="w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[10px] font-bold cursor-pointer shadow-xs">
                AI
              </div>
            </div>
          </form>

          {/* Bottom Action Row Inside Card */}
          <div className="flex items-center justify-between pt-2.5 sm:pt-3 text-xs">
            <span 
              onClick={() => setIsOpen(true)}
              className="text-zinc-500 text-[11px] sm:text-[11.5px] font-medium truncate pr-2 cursor-pointer hover:text-zinc-800 transition-colors"
            >
              Ask our SDR AI &bull; Instant objection handling &amp; migration offers
            </span>

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
              <span>Ask AI SDR</span>
            </button>
          </div>
        </div>

        {/* Center-Aligned Agency Type Chips Below (CUSHIONED PADDING PREVENTS HOVER CLIPPING) */}
        <div className="mt-3.5 sm:mt-4 w-full overflow-x-auto py-2.5 px-1 scrollbar-none">
          <div className="flex items-center sm:justify-center gap-1.5 sm:gap-2 min-w-max px-2 py-1">
            {agencyPills.map((pill) => {
              const IconComp = pill.icon;
              const isSelected = activeAgencyPill === pill.id;
              return (
                <button
                  key={pill.id}
                  type="button"
                  onClick={() => handleSend(pill.query, pill.id)}
                  className={`inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-[11px] sm:text-xs font-medium transition-all hover:-translate-y-0.5 shadow-2xs cursor-pointer ${
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

      {/* ── 2. STICKY FLOATING BOTTOM-CENTER AI SDR BAR & DIALOGUE MODAL ── */}
      {isOpen && (
        <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-2 sm:p-4 md:p-6 animate-in fade-in duration-200">
          
          {/* Backdrop Blur Overlay */}
          <div
            className="fixed inset-0 bg-black/35 backdrop-blur-xs transition-opacity cursor-pointer"
            onClick={() => setIsOpen(false)}
          />

          {/* The Floating Center-Bottom SDR Panel */}
          <div className="relative z-10 w-full max-w-[700px] max-h-[85vh] sm:max-h-[80vh] bg-white/98 backdrop-blur-2xl rounded-3xl border border-zinc-200/90 shadow-[0_24px_70px_rgba(0,0,0,0.22)] ring-1 ring-black/[0.06] flex flex-col overflow-hidden animate-in slide-in-from-bottom-5 duration-200 text-left">
            
            {/* Header Bar with Live Deal Desk Status */}
            <div className="flex items-center justify-between px-4 sm:px-6 py-3 bg-zinc-50/95 border-b border-zinc-100 shrink-0">
              <div className="flex items-center gap-2.5">
                <div className="w-7 h-7 rounded-full bg-zinc-950 text-white flex items-center justify-center shadow-xs">
                  <Sparkles className="w-3.5 h-3.5 text-emerald-400" />
                </div>
                <div>
                  <div className="flex items-center gap-2">
                    <span className="text-xs sm:text-sm font-bold text-zinc-950">Cora AI</span>
                    <span className="px-2 py-0.5 text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800 border border-emerald-200/80 rounded-full flex items-center gap-1">
                      <span className="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse" />
                      Deal Desk SDR
                    </span>
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-1.5">
                {messages.length > 0 && (
                  <button
                    type="button"
                    onClick={handleReset}
                    title="Reset Conversation"
                    className="p-1.5 rounded-full text-zinc-400 hover:text-zinc-900 hover:bg-zinc-200/70 transition-colors cursor-pointer"
                  >
                    <RotateCcw className="w-3.5 h-3.5" />
                  </button>
                )}
                <button
                  type="button"
                  onClick={() => setIsOpen(false)}
                  title="Close (Esc)"
                  className="p-1.5 rounded-full text-zinc-400 hover:text-zinc-900 hover:bg-zinc-200/70 transition-colors cursor-pointer"
                >
                  <X className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* Scrollable Conversation Feed */}
            <div 
              ref={chatScrollContainerRef}
              className="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 overscroll-contain scrollbar-thin scrollbar-thumb-zinc-300 min-h-[220px]"
            >
              {messages.length === 0 ? (
                <div className="py-6 sm:py-8 text-center text-zinc-500 text-xs sm:text-sm max-w-[460px] mx-auto space-y-3">
                  <div className="w-10 h-10 rounded-2xl bg-zinc-100 text-zinc-900 flex items-center justify-center mx-auto border border-zinc-200 shadow-2xs">
                    <Sparkles className="w-5 h-5 text-emerald-600" />
                  </div>
                  <p className="font-semibold text-zinc-900 text-sm sm:text-base">
                    Test me with a tough prospect objection.
                  </p>
                  <p className="text-xs text-zinc-500 leading-relaxed">
                    Ask why you should switch from HoneyBook, request our free website migration service, or claim exclusive annual discounts.
                  </p>
                  
                  {/* Quick Objection Prompts */}
                  <div className="flex flex-wrap items-center justify-center gap-1.5 pt-2">
                    {sampleObjections.map((obj, i) => (
                      <button
                        key={i}
                        type="button"
                        onClick={() => handleSend(obj.query)}
                        className="text-[11px] font-medium bg-zinc-100/80 hover:bg-zinc-200 text-zinc-800 px-3 py-1.5 rounded-full border border-zinc-200/80 transition-colors cursor-pointer text-left"
                      >
                        {obj.label}
                      </button>
                    ))}
                  </div>
                </div>
              ) : (
                messages.map((msg) => (
                  <div
                    key={msg.id}
                    className={`flex flex-col ${msg.sender === 'user' ? 'items-end' : 'items-start'} gap-2`}
                  >
                    {/* Message Bubble */}
                    <div
                      className={`max-w-[90%] sm:max-w-[85%] rounded-2xl p-3.5 sm:p-4 text-xs sm:text-[13.5px] leading-relaxed ${
                        msg.sender === 'user'
                          ? 'bg-zinc-950 text-white rounded-br-xs font-medium shadow-2xs'
                          : 'bg-zinc-50 text-zinc-900 rounded-bl-xs border border-zinc-200/90 font-normal shadow-2xs'
                      }`}
                    >
                      <p className={`whitespace-pre-line ${msg.sender === 'user' ? 'text-zinc-100' : 'text-zinc-900'} leading-relaxed font-sans`}>
                        {msg.text}
                      </p>
                    </div>

                    {/* Dedicated High-Converting Deal Card (When Triggered by SDR) */}
                    {msg.offer && (
                      <div className="w-full max-w-[90%] sm:max-w-[85%] rounded-2xl bg-gradient-to-br from-emerald-50/80 via-white to-zinc-50 p-3.5 sm:p-4 border border-emerald-200 shadow-sm animate-in fade-in zoom-in-95 duration-150">
                        <div className="flex items-start justify-between gap-2 mb-1.5">
                          <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold">
                            <Gift className="w-3 h-3" />
                            {msg.offer.badge}
                          </span>
                          <button
                            type="button"
                            onClick={() => handleCopyCode(msg.offer!.code)}
                            className="inline-flex items-center gap-1 text-[10px] font-mono font-bold text-zinc-600 hover:text-zinc-900 bg-white px-2 py-0.5 rounded border border-zinc-200/80 shadow-2xs cursor-pointer"
                          >
                            {copiedCode === msg.offer.code ? (
                              <>
                                <Check className="w-2.5 h-2.5 text-emerald-600" />
                                <span>COPIED</span>
                              </>
                            ) : (
                              <>
                                <Copy className="w-2.5 h-2.5 text-zinc-400" />
                                <span>{msg.offer.code}</span>
                              </>
                            )}
                          </button>
                        </div>

                        <h5 className="text-xs sm:text-sm font-bold text-zinc-950 mb-0.5">
                          {msg.offer.title}
                        </h5>
                        <p className="text-[11.5px] text-zinc-600 mb-3 leading-snug">
                          {msg.offer.description}
                        </p>

                        <a
                          href={msg.offer.actionUrl}
                          className="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 transition-all shadow-xs w-full sm:w-auto"
                        >
                          <span>{msg.offer.actionText}</span>
                        </a>
                      </div>
                    )}

                    {/* Quick Reply Objection Follow-ups */}
                    {msg.quickReplies && msg.quickReplies.length > 0 && (
                      <div className="flex flex-wrap gap-1.5 pt-0.5">
                        {msg.quickReplies.map((qr, idx) => (
                          <button
                            key={idx}
                            type="button"
                            onClick={() => handleSend(qr.query)}
                            className="text-[11px] font-medium bg-white hover:bg-zinc-100 text-zinc-800 px-3 py-1 rounded-full border border-zinc-200 shadow-2xs transition-colors cursor-pointer"
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
                <div className="flex items-center gap-2 text-zinc-500 text-xs py-1.5 pl-1">
                  <div className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                  <span>Cora SDR is formulating a direct response...</span>
                </div>
              )}
            </div>

            {/* Quick Objection Presets Carousel Above Input */}
            <div className="px-4 sm:px-6 py-2 border-t border-zinc-100 bg-zinc-50/80 overflow-x-auto scrollbar-none shrink-0">
              <div className="flex items-center gap-1.5 min-w-max">
                <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider pr-1">
                  Objections:
                </span>
                {sampleObjections.map((obj, i) => (
                  <button
                    key={i}
                    type="button"
                    onClick={() => handleSend(obj.query)}
                    className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-medium bg-white hover:bg-zinc-100 text-zinc-800 border border-zinc-200/90 transition-colors cursor-pointer shadow-2xs"
                  >
                    <span>{obj.label}</span>
                  </button>
                ))}
              </div>
            </div>

            {/* Integrated SDR Input Bar */}
            <form onSubmit={handleSubmit} className="p-3 sm:p-4 bg-white border-t border-zinc-100 flex items-center gap-2.5 shrink-0">
              <input
                ref={activeInputRef}
                type="text"
                value={inputValue}
                onChange={(e) => setInputValue(e.target.value)}
                placeholder="Type your sales objection, question, or request a deal..."
                className="flex-1 bg-zinc-100 rounded-full px-4 py-2.5 text-xs sm:text-sm text-zinc-950 placeholder:text-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-950 font-sans"
              />
              <button
                type="submit"
                className="w-9 h-9 rounded-full bg-zinc-950 text-white flex items-center justify-center shrink-0 shadow-xs hover:bg-zinc-800 transition-colors cursor-pointer"
              >
                <Send className="w-4 h-4" />
              </button>
            </form>

          </div>
        </div>
      )}
    </>
  );
}
