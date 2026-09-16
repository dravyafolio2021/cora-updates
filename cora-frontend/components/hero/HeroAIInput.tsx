'use client';

import React, { useState, useRef, useEffect } from 'react';
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
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export interface QuickReply {
  label: string;
  query: string;
}

export interface Message {
  id: string;
  sender: 'user' | 'assistant';
  text: string;
  quickReplies?: QuickReply[];
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

function getSimpleReply(query: string): {
  text: string;
  quickReplies?: QuickReply[];
} {
  const q = query.trim().toLowerCase();

  // 1. Web Design & Development
  if (q.includes('web') || q.includes('website') || q.includes('wordpress') || q.includes('webflow') || q.includes('framer') || q.includes('developer') || q.includes('shopify')) {
    return {
      text: `When you build client websites, you can bundle Cora as their ready-to-use client portal. Clients get a private branded hub to review staging links, sign milestone approvals with SHA-256 digital signatures, and pay 18% GST invoices via instant UPI QR codes.`,
      quickReplies: [
        { label: 'How to charge clients for portals?', query: 'How do agencies package and charge clients for Cora portals?' },
        { label: '18% GST Invoicing', query: 'How does 18% GST and SAC 9983 invoicing work?' },
        { label: 'Free Forever Plan', query: 'What is included in the Free Forever Plan?' },
      ],
    };
  }

  // 2. Creative & Branding Studios
  if (q.includes('creative') || q.includes('brand') || q.includes('design') || q.includes('logo') || q.includes('figma')) {
    return {
      text: `For creative and branding studios, Cora provides a luxury client review hub. You can present brand decks, collect threaded visual feedback, lock 50% advance deposits via UPI QR, and auto-generate legal copyright transfer deeds upon delivery.`,
      quickReplies: [
        { label: 'Copyright Transfer Deeds', query: 'How does Cora generate intellectual property contracts?' },
        { label: 'Pricing Plans', query: 'What are the pricing plans for Cora?' },
        { label: 'Milestone Payments', query: 'How does milestone-based client billing work?' },
      ],
    };
  }

  // 3. Performance Marketing, Ads & Retainers
  if (q.includes('marketing') || q.includes('seo') || q.includes('ad') || q.includes('retainer') || q.includes('growth')) {
    return {
      text: `Cora puts monthly client retainers on autopilot. On the 1st of every month, it dispatches SAC 9983 compliant 18% GST invoices and WhatsApp payment links directly to your clients, with automated polite follow-ups so you get paid on time.`,
      quickReplies: [
        { label: 'WhatsApp Automation', query: 'How do WhatsApp client reminders and retainers work?' },
        { label: '18% GST Breakdown', query: 'How does GST tax invoicing work in Cora?' },
        { label: 'Free Plan Setup', query: 'How do I get started for free?' },
      ],
    };
  }

  // 4. Software & App Development
  if (q.includes('software') || q.includes('app') || q.includes('saas') || q.includes('sprint') || q.includes('dev') || q.includes('code')) {
    return {
      text: `Manage sprint milestones and client sign-offs in one place. Clients review deliverables and sign UAT approvals with cryptographic SHA-256 audit trails before code deployment, with automated SAC 9983 consulting tax billing.`,
      quickReplies: [
        { label: 'Sprint Milestone Demo', query: 'How does milestone-based software billing work?' },
        { label: 'Web Design Portals', query: 'How does Cora help web design agencies?' },
        { label: 'Free Forever Plan', query: 'What is included in the Free Forever Plan?' },
      ],
    };
  }

  // 5. Pricing, Costs & Plans
  if (q.includes('price') || q.includes('cost') || q.includes('plan') || q.includes('replace') || q.includes('pandadoc') || q.includes('honeybook') || q.includes('notion') || q.includes('free')) {
    return {
      text: `You can start completely free on our Free Forever plan (₹0 / $0) with 1,000 monthly AI runs, digital signature contracts, and GST invoicing. For growing teams, our India Only Plan is ₹499/mo billed annually (includes 2 months free and a free .in domain), replacing $110+/mo in separate SaaS subscriptions.`,
      quickReplies: [
        { label: 'Start Free Forever (₹0)', query: 'How do I start on the Free Forever plan?' },
        { label: 'Web Agency Portals', query: 'How does Cora help web design agencies deliver portals?' },
        { label: 'Contract Signatures', query: 'How do digital signature contracts work in Cora?' },
      ],
    };
  }

  // Default Assistant Response
  return {
    text: `Cora gives your agency an all-in-one operating workspace. You can manage proposals, send digitally signed contracts, deliver custom client portals, and generate 18% GST invoices in a single screen.`,
    quickReplies: [
      { label: 'Web Design Agencies', query: 'How does Cora help web design & dev agencies?' },
      { label: 'Pricing Options', query: 'What are the pricing plans for Cora?' },
      { label: 'Client Portals', query: 'How do branded client portals work?' },
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

  // Auto-scroll ONLY internal chat container, never the window
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
            quickReplies: data.quickReplies,
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
      const response = getSimpleReply(text);
      const assistantMsg: Message = {
        id: (Date.now() + 1).toString(),
        sender: 'assistant',
        text: response.text,
        quickReplies: response.quickReplies,
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

  // ══════════════════════════════════════════════════════════════════════════
  // 1. EXPANDED STATE (Single unified modal card in-place with scrollable feed)
  // ══════════════════════════════════════════════════════════════════════════
  if (isOpen) {
    return (
      <div className="w-full max-w-[840px] mx-auto text-left relative z-30 animate-in fade-in zoom-in-95 duration-200">
        <div className="w-full bg-white/98 backdrop-blur-2xl rounded-2xl sm:rounded-[32px] border border-zinc-200/90 shadow-[0_24px_70px_rgba(0,0,0,0.14)] ring-1 ring-black/[0.04] flex flex-col overflow-hidden">
          
          {/* Header Bar */}
          <div className="flex items-center justify-between px-4 sm:px-6 py-3 bg-zinc-50/90 border-b border-zinc-100 shrink-0">
            <div className="flex items-center gap-2.5">
              <div className="w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center shadow-xs">
                <Sparkles className="w-3.5 h-3.5 text-emerald-400" />
              </div>
              <div className="flex items-center gap-2">
                <span className="text-sm font-bold text-zinc-950">Cora AI</span>
                <span className="px-2 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-full">
                  Agency Assistant
                </span>
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

          {/* Truly Scrollable Chat Message Feed */}
          <div 
            ref={chatScrollContainerRef}
            className="min-h-[200px] max-h-[380px] sm:max-h-[420px] overflow-y-auto p-4 sm:p-6 space-y-4 overscroll-contain scrollbar-thin scrollbar-thumb-zinc-300 text-left"
          >
            {messages.length === 0 ? (
              <div className="py-6 text-center text-zinc-500 text-xs sm:text-sm max-w-[420px] mx-auto space-y-2">
                <p className="font-medium text-zinc-800">
                  Hi! Ask me anything about replacing tools, delivering client portals, or GST invoicing.
                </p>
                <p className="text-xs text-zinc-400">
                  Type a question below or choose an agency topic:
                </p>
              </div>
            ) : (
              messages.map((msg) => (
                <div
                  key={msg.id}
                  className={`flex flex-col ${msg.sender === 'user' ? 'items-end' : 'items-start'} gap-1.5`}
                >
                  <div
                    className={`max-w-[88%] sm:max-w-[82%] rounded-2xl p-3.5 sm:p-4 text-xs sm:text-[13.5px] leading-relaxed ${
                      msg.sender === 'user'
                        ? 'bg-zinc-950 text-white rounded-br-xs font-medium shadow-2xs'
                        : 'bg-zinc-50 text-zinc-900 rounded-bl-xs border border-zinc-200/90 font-normal shadow-2xs'
                    }`}
                  >
                    <p className={`whitespace-pre-line ${msg.sender === 'user' ? 'text-zinc-100' : 'text-zinc-900'} leading-relaxed`}>
                      {msg.text}
                    </p>
                  </div>

                  {/* Quick Reply Suggestion Buttons */}
                  {msg.quickReplies && msg.quickReplies.length > 0 && (
                    <div className="flex flex-wrap gap-1.5 pt-1">
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
              <div className="flex items-center gap-2 text-zinc-400 text-xs py-1 pl-1">
                <div className="w-1.5 h-1.5 rounded-full bg-zinc-400 animate-pulse" />
                <span>Cora is thinking...</span>
              </div>
            )}
          </div>

          {/* Agency Topic Chips Inside Modal */}
          <div className="px-4 sm:px-6 py-2 border-t border-zinc-100 bg-zinc-50/50 overflow-x-auto scrollbar-none shrink-0">
            <div className="flex items-center gap-1.5 min-w-max">
              {agencyPills.map((pill) => {
                const IconComp = pill.icon;
                const isSelected = activeAgencyPill === pill.id;
                return (
                  <button
                    key={pill.id}
                    type="button"
                    onClick={() => handleSend(pill.query, pill.id)}
                    className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-medium transition-colors cursor-pointer shadow-2xs ${
                      isSelected
                        ? 'bg-zinc-950 text-white'
                        : 'bg-white hover:bg-zinc-100 text-zinc-800 border border-zinc-200/90'
                    }`}
                  >
                    <IconComp className={`w-3 h-3 ${isSelected ? 'text-emerald-400' : 'text-zinc-600'}`} />
                    <span>{pill.label}</span>
                  </button>
                );
              })}
            </div>
          </div>

          {/* Integrated Modal Input Bar */}
          <form onSubmit={handleSubmit} className="p-3 sm:p-4 bg-white border-t border-zinc-100 flex items-center gap-2.5 shrink-0">
            <input
              ref={activeInputRef}
              type="text"
              value={inputValue}
              onChange={(e) => setInputValue(e.target.value)}
              placeholder="Ask anything about replacing tools, proposals, or portals..."
              className="flex-1 bg-zinc-100 rounded-full px-4 py-2.5 text-xs sm:text-sm text-zinc-950 placeholder:text-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-950"
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
    );
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 2. NORMAL STATE (Compact Hero input bar with agency chips below)
  // ══════════════════════════════════════════════════════════════════════════
  return (
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
            placeholder="Ask anything... e.g. How do agencies deliver client portals?"
            className="w-full bg-transparent text-xs sm:text-sm md:text-[14.5px] font-sans text-zinc-950 placeholder:text-zinc-400 focus:outline-none tracking-tight cursor-pointer"
          />

          {/* Right Circular Brand Badges */}
          <div className="flex items-center gap-1.5 shrink-0" onClick={() => setIsOpen(true)}>
            <div className="w-6 h-6 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-600 text-[10px] font-bold cursor-pointer">
              ✦
            </div>
            <div className="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold cursor-pointer">
              G
            </div>
          </div>
        </form>

        {/* Bottom Action Row Inside Card */}
        <div className="flex items-center justify-between pt-2.5 sm:pt-3 text-xs">
          <span 
            onClick={() => setIsOpen(true)}
            className="text-zinc-500 text-[11px] sm:text-[11.5px] font-medium truncate pr-2 cursor-pointer hover:text-zinc-800 transition-colors"
          >
            Ask our friendly AI &bull; No signup needed
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
            <span>Ask AI</span>
          </button>
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
