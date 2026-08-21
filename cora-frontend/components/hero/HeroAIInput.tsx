'use client';

import React, { useState, useRef, useEffect } from 'react';
import {
  Sparkles,
  ArrowRight,
  RotateCcw,
  Camera,
  Building2,
  Home,
  Scissors,
  X,
  Send,
  ChevronDown,
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

interface FeatureHighlight {
  title: string;
  desc: string;
  badge?: string;
}

interface QuickReply {
  label: string;
  query: string;
  iconName?: string;
}

interface Message {
  id: string;
  sender: 'user' | 'sdr';
  text: string;
  highlights?: FeatureHighlight[];
  quickReplies?: QuickReply[];
  ctaText?: string;
  ctaLink?: string;
  timestamp: string;
}

const industryPills = [
  { 
    icon: Camera, 
    label: 'Photo & Video Studios', 
    query: 'How does Cora help a photo and video studio?' 
  },
  { 
    icon: Building2, 
    label: 'Creative & Digital Agencies', 
    query: 'How does Cora help digital and creative agencies?' 
  },
  { 
    icon: Home, 
    label: 'Real Estate Brokers', 
    query: 'How does Cora help real estate brokers and property consultants?' 
  },
  { 
    icon: Scissors, 
    label: 'Salons, Spas & Clinics', 
    query: 'How does Cora help salons, spas, and wellness clinics?' 
  },
];

function getSimpleRichReply(query: string): {
  text: string;
  highlights?: FeatureHighlight[];
  quickReplies?: QuickReply[];
  ctaText?: string;
  ctaLink?: string;
} {
  const q = query.trim().toLowerCase();

  // 1. Photo & Video Studios
  if (q.includes('photo') || q.includes('video') || q.includes('studio') || q.includes('shoot') || q.includes('camera') || q.includes('production')) {
    return {
      text: `Here's how Cora runs your entire photo & video production studio:`,
      highlights: [
        { title: 'Shoot Notes & Hold Dates', desc: 'Track client shoot dates, call times, and crew allocations in chat', badge: 'Bookings' },
        { title: '18% GST & Instant UPI', desc: 'Generate 18% GST invoices and send UPI payment links directly on WhatsApp', badge: 'Billing' },
        { title: 'Digital E-Sign Agreements', desc: 'Send shoot contracts and capture legally binding client signatures on mobile', badge: 'E-Sign' },
        { title: 'Crew Call-Sheets', desc: 'Auto-generate shoot call-sheets and dispatch details to your team', badge: 'Dispatch' },
      ],
      quickReplies: [
        { label: 'Creative & Digital Agencies', query: 'How does Cora help digital and creative agencies?' },
        { label: '18% GST Billing Demo', query: 'Make a ₹15,000 invoice with 18% GST for Rahul' },
        { label: 'Free Plan Details', query: 'What is included in the free plan?' },
      ],
      ctaText: 'Start Free Studio Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?industry=photography_studio&source=sdr_industry_chip',
    };
  }

  // 2. Creative & Digital Agencies
  if (q.includes('agency') || q.includes('digital') || q.includes('creative') || q.includes('freelancer') || q.includes('marketing') || q.includes('design')) {
    return {
      text: `Here's how Cora streamlines operations for creative & digital agencies:`,
      highlights: [
        { title: 'Client Scopes & Proposals', desc: 'Draft custom project proposals, retainers, and deliverable agreements in seconds', badge: 'Proposals' },
        { title: 'Inquiry-to-Cash CRM', desc: 'Track leads from initial WhatsApp discovery call to final milestone payment', badge: 'Pipeline' },
        { title: 'Automated Reminders', desc: 'Send polite payment reminders and milestone sign-off links with zero awkwardness', badge: 'Follow-ups' },
        { title: 'Cash Flow & Margins', desc: 'Monitor project margins, monthly recurring retainers, and contractor payouts', badge: 'Finance' },
      ],
      quickReplies: [
        { label: 'Photo & Video Studios', query: 'How does Cora help a photo and video studio?' },
        { label: 'Real Estate Brokers', query: 'How does Cora help real estate brokers and property consultants?' },
        { label: 'Pricing Plans', query: 'What are the pricing plans for Cora?' },
      ],
      ctaText: 'Start Free Agency Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?industry=custom&source=sdr_industry_chip',
    };
  }

  // 3. Real Estate Brokers
  if (q.includes('real estate') || q.includes('property') || q.includes('broker') || q.includes('realtor') || q.includes('listing')) {
    return {
      text: `Here's how Cora powers modern real estate brokers & property consultants:`,
      highlights: [
        { title: 'Buyer Lead Matching', desc: 'Log buyer budgets, locations, and match matching property inventory', badge: 'Leads' },
        { title: 'WhatsApp Property Briefs', desc: 'Generate clean property briefs and listing descriptions in seconds', badge: 'Listings' },
        { title: 'Site Visit Coordination', desc: 'Track client property visits, site viewing notes, and client hold requests', badge: 'Visits' },
        { title: 'Brokerage Fee Invoices', desc: 'Create 18% GST brokerage invoices with instant UPI QR payment links', badge: 'Invoices' },
      ],
      quickReplies: [
        { label: 'Salons & Clinics', query: 'How does Cora help salons, spas, and wellness clinics?' },
        { label: 'Photo & Video Studios', query: 'How does Cora help a photo and video studio?' },
        { label: 'Is card required?', query: 'Do I need a credit card to get started?' },
      ],
      ctaText: 'Start Free Real Estate Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?industry=real_estate&source=sdr_industry_chip',
    };
  }

  // 4. Salons, Spas & Clinics
  if (q.includes('salon') || q.includes('spa') || q.includes('clinic') || q.includes('dental') || q.includes('health') || q.includes('fitness') || q.includes('gym') || q.includes('wellness')) {
    return {
      text: `Here's how Cora manages daily operations for salons, spas, and clinics:`,
      highlights: [
        { title: 'WhatsApp Booking Confirmations', desc: 'Send instant booking confirmations and automated appointment reminders', badge: 'Appointments' },
        { title: 'Service Menu Digital Bills', desc: 'Generate instant GST bills and UPI QR codes for walk-in client checkout', badge: 'Checkout' },
        { title: 'Client Visit History', desc: 'Remember past services, preferences, and repeat customer frequency', badge: 'Memory' },
        { title: 'Daily Collections & Cash Flow', desc: 'Track daily UPI/cash collections and staff service commissions in real time', badge: 'Accounts' },
      ],
      quickReplies: [
        { label: 'Photo & Video Studios', query: 'How does Cora help a photo and video studio?' },
        { label: 'Creative & Digital Agencies', query: 'How does Cora help digital and creative agencies?' },
        { label: 'Free Forever Plan', query: 'What is included in the free plan?' },
      ],
      ctaText: 'Start Free Clinic / Salon Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?industry=custom&source=sdr_industry_chip',
    };
  }

  // 5. GST & Invoicing
  if (q.includes('gst') || q.includes('invoice') || q.includes('bill') || q.includes('tax') || q.includes('15,000') || q.includes('15000')) {
    return {
      text: `Cora creates compliant 18% GST invoices in under 10 seconds:`,
      highlights: [
        { title: 'Auto CGST / SGST', desc: 'Automatic state split calculations with zero math errors', badge: '18% GST' },
        { title: 'Direct UPI & QR', desc: 'Clients scan to pay instantly via PhonePe, GPay, or Paytm', badge: 'UPI' },
      ],
      quickReplies: [
        { label: 'Photo & Video Studios', query: 'How does Cora help a photo and video studio?' },
        { label: 'Pricing Plans', query: 'What are the pricing plans for Cora?' },
      ],
      ctaText: 'Generate First GST Invoice Free →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_gst',
    };
  }

  // 6. Pricing
  if (q.includes('price') || q.includes('pricing') || q.includes('plan') || q.includes('cost') || q.includes('free') || q.includes('299') || q.includes('999')) {
    return {
      text: `Cora offers transparent pricing in Indian Rupees with a Free Forever plan:`,
      highlights: [
        { title: 'Free Forever (₹0)', desc: '1 User workspace, core AI chat, client manager, up to 15 GST invoices/mo', badge: '₹0' },
        { title: 'Standard (₹299/mo)', desc: 'Billed annually. Full AI memory, 3 team seats, unlimited invoices', badge: 'Popular' },
        { title: 'Business (₹999/mo)', desc: 'Billed annually. Up to 10 team seats, multi-location & priority support', badge: 'Agencies' },
      ],
      quickReplies: [
        { label: 'Photo & Video Studios', query: 'How does Cora help a photo and video studio?' },
        { label: 'Creative & Digital Agencies', query: 'How does Cora help digital and creative agencies?' },
      ],
      ctaText: 'Start Free Forever (No Card) →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_pricing',
    };
  }

  // 7. Greetings / Default
  return {
    text: `Cora is your AI co-founder. Type what you need in plain English or Hinglish:`,
    highlights: [
      { title: 'Single Chat Input', desc: 'Manage invoices, bookings, and inquiries without clicking through 10 apps', badge: 'Simple' },
      { title: 'Business Memory', desc: 'Remembers your client rates, service menu, and active jobs automatically', badge: 'Context' },
    ],
    quickReplies: [
      { label: 'Photo & Video Studios', query: 'How does Cora help a photo and video studio?' },
      { label: 'Creative & Digital Agencies', query: 'How does Cora help digital and creative agencies?' },
      { label: 'Real Estate Brokers', query: 'How does Cora help real estate brokers and property consultants?' },
      { label: 'Salons & Clinics', query: 'How does Cora help salons, spas, and wellness clinics?' },
    ],
    ctaText: 'Start Free Forever (No Card) →',
    ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_default',
  };
}

export function HeroAIInput() {
  const [inputValue, setInputValue] = useState('');
  const [messages, setMessages] = useState<Message[]>([]);
  const [isExpanded, setIsExpanded] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  const messagesEndRef = useRef<HTMLDivElement>(null);
  const chatScrollContainerRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  // Auto-scroll chat to latest message
  useEffect(() => {
    if (isExpanded && messages.length > 0) {
      messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }
  }, [messages, isExpanded]);

  // Isolate scroll within the chat container so it never scrolls the underlying window
  const handleChatWheel = (e: React.WheelEvent<HTMLDivElement>) => {
    const container = chatScrollContainerRef.current;
    if (!container) return;

    const { scrollTop, scrollHeight, clientHeight } = container;
    const isScrollable = scrollHeight > clientHeight;

    if (isScrollable) {
      // Prevent parent/body scrolling if scrolling within limits
      const isAtTop = scrollTop === 0 && e.deltaY < 0;
      const isAtBottom = scrollTop + clientHeight >= scrollHeight - 1 && e.deltaY > 0;

      if (!isAtTop && !isAtBottom) {
        e.stopPropagation();
      }
    }
  };

  const handleSend = (textToSend?: string) => {
    const text = (textToSend || inputValue).trim();
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

    setTimeout(() => {
      const response = getSimpleRichReply(text);
      const sdrMsg: Message = {
        id: (Date.now() + 1).toString(),
        sender: 'sdr',
        text: response.text,
        highlights: response.highlights,
        quickReplies: response.quickReplies,
        ctaText: response.ctaText,
        ctaLink: response.ctaLink,
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      };
      setMessages((prev) => [...prev, sdrMsg]);
      setIsLoading(false);
    }, 350);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    handleSend();
  };

  const handleReset = () => {
    setMessages([]);
    setInputValue('');
    trackEvent('hero_ai_chat_reset');
  };

  const handleCollapse = () => {
    setIsExpanded(false);
  };

  return (
    <div className="w-full max-w-[820px] mx-auto text-left relative z-20">
      
      {/* ── Expanding Hero AI Card (Smooth In-Place Expansion with Isolated Scroll) ── */}
      <div 
        className={`w-full bg-white/95 backdrop-blur-xl border border-white/80 rounded-[28px] sm:rounded-[32px] p-4 sm:p-6 transition-all duration-300 ease-out ${
          isExpanded 
            ? 'shadow-[0px_24px_70px_rgba(0,0,0,0.12)] ring-1 ring-black/[0.06] -translate-y-1' 
            : 'shadow-[0px_16px_48px_rgba(0,0,0,0.08)] ring-1 ring-black/[0.04]'
        }`}
      >
        
        {/* Expanded Top Header Bar (Only visible when expanded) */}
        {isExpanded && (
          <div className="flex items-center justify-between pb-3 mb-3 border-b border-zinc-100/90 animate-in fade-in duration-200">
            <div className="flex items-center gap-2">
              <div className="w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center shadow-2xs">
                <Sparkles className="w-3.5 h-3.5 text-emerald-400" />
              </div>
              <span className="text-xs sm:text-sm font-bold text-zinc-950">Cora AI Sales Concierge</span>
              <span className="px-2 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-full">
                Active Session
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

        {/* Expanded Scrollable Chat Feed (Isolated Scroll, Never Scrolls Window) */}
        {isExpanded && (
          <div 
            ref={chatScrollContainerRef}
            onWheel={handleChatWheel}
            className="my-3 max-h-[320px] overflow-y-auto overscroll-contain pr-1.5 space-y-3.5 scrollbar-thin scrollbar-thumb-zinc-200"
          >
            {messages.length === 0 ? (
              <div className="py-4 text-center text-zinc-500 text-xs">
                Ask a question below or click any of the 4 industry chips to see how Cora works for you:
              </div>
            ) : (
              messages.map((msg) => (
                <div
                  key={msg.id}
                  className={`flex flex-col ${msg.sender === 'user' ? 'items-end' : 'items-start'} gap-1.5`}
                >
                  <div
                    className={`max-w-[88%] sm:max-w-[85%] rounded-2xl px-4 py-3 text-xs sm:text-sm leading-relaxed ${
                      msg.sender === 'user'
                        ? 'bg-zinc-950 text-white rounded-br-xs font-medium'
                        : 'bg-zinc-50 text-zinc-900 rounded-bl-xs border border-zinc-200/70 font-normal'
                    }`}
                  >
                    <p className="whitespace-pre-line">{msg.text}</p>

                    {msg.highlights && msg.highlights.length > 0 && (
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

                    {msg.ctaText && msg.ctaLink && (
                      <div className="mt-3 pt-1">
                        <a
                          href={msg.ctaLink}
                          className="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-zinc-950 text-white rounded-lg text-xs font-semibold hover:bg-zinc-800 transition-colors shadow-2xs"
                        >
                          <span>{msg.ctaText}</span>
                        </a>
                      </div>
                    )}
                  </div>

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
                <span>Cora is thinking...</span>
              </div>
            )}

            <div ref={messagesEndRef} />
          </div>
        )}

        {/* Input Bar Area */}
        <form onSubmit={handleSubmit} className="relative flex items-center justify-between gap-3 pb-3 border-b border-zinc-100/90">
          <input
            ref={inputRef}
            type="text"
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            onFocus={() => {
              if (!isExpanded) setIsExpanded(true);
            }}
            placeholder="Ask anything about Cora... (e.g. How does 18% GST billing or WhatsApp booking work?)"
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
        <div className="flex items-center justify-between pt-3 text-xs">
          <span className="text-zinc-500 text-[11.5px] font-medium">
            Ask our AI Sales Concierge &bull; No signup needed
          </span>

          <button
            type="button"
            onClick={() => handleSend()}
            className="px-4 py-1.5 bg-zinc-400 hover:bg-zinc-500 text-white rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer"
          >
            <Sparkles className="w-3.5 h-3.5" />
            <span>Generate</span>
          </button>
        </div>

      </div>

      {/* ── Center-Aligned 4 Industry Chips ── */}
      <div className="mt-4 flex items-center justify-center gap-2 flex-wrap w-full">
        {industryPills.map((pill, idx) => {
          const IconComp = pill.icon;
          return (
            <button
              key={idx}
              type="button"
              onClick={() => handleSend(pill.query)}
              className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/95 hover:bg-white text-zinc-800 hover:text-zinc-950 text-xs font-medium transition-all hover:-translate-y-0.5 border border-zinc-200/90 hover:border-zinc-400 shadow-2xs cursor-pointer"
            >
              <IconComp className="w-3.5 h-3.5 text-zinc-700" />
              <span>{pill.label}</span>
            </button>
          );
        })}
      </div>

    </div>
  );
}
