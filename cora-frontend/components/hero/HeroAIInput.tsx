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
  MessageSquare,
  Bot,
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
  const [heroInputValue, setHeroInputValue] = useState('');
  const [modalInputValue, setModalInputValue] = useState('');
  const [messages, setMessages] = useState<Message[]>([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  const messagesEndRef = useRef<HTMLDivElement>(null);
  const modalInputRef = useRef<HTMLInputElement>(null);

  // Lock body scroll when modal is active
  useEffect(() => {
    if (isModalOpen) {
      document.body.style.overflow = 'hidden';
      // Auto-focus modal input after opening
      setTimeout(() => {
        modalInputRef.current?.focus();
      }, 100);
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [isModalOpen]);

  // Escape key listener to close modal
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape' && isModalOpen) {
        setIsModalOpen(false);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isModalOpen]);

  // Auto-scroll chat to latest message
  useEffect(() => {
    if (isModalOpen && messages.length > 0) {
      messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }
  }, [messages, isModalOpen]);

  const handleSend = (textToSend?: string) => {
    const text = (textToSend || modalInputValue || heroInputValue).trim();
    if (!text) {
      // If empty and opened from hero, open modal with default welcome
      setIsModalOpen(true);
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
    setHeroInputValue('');
    setModalInputValue('');
    setIsModalOpen(true);
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
    }, 400);
  };

  const handleHeroSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    handleSend(heroInputValue);
  };

  const handleModalSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    handleSend(modalInputValue);
  };

  const handleReset = () => {
    setMessages([]);
    setModalInputValue('');
    setHeroInputValue('');
    trackEvent('hero_ai_chat_reset');
  };

  const handleCloseModal = () => {
    setIsModalOpen(false);
  };

  return (
    <div className="w-full max-w-[820px] mx-auto text-left relative z-20">
      
      {/* ── Normal Static Hero Input Card (Stays Clean & Undisturbed) ── */}
      <div 
        onClick={() => {
          if (!heroInputValue) {
            // If empty, open modal with welcome context or let user type
            setIsModalOpen(true);
          }
        }}
        className="w-full bg-white/95 backdrop-blur-xl border border-white/80 rounded-[28px] sm:rounded-[32px] p-4 sm:p-6 shadow-[0px_16px_48px_rgba(0,0,0,0.08)] ring-1 ring-black/[0.04] transition-all cursor-pointer"
      >
        
        {/* Top Input Bar Area */}
        <form onSubmit={handleHeroSubmit} className="relative flex items-center justify-between gap-3 pb-3 border-b border-zinc-100/90">
          <input
            type="text"
            value={heroInputValue}
            onChange={(e) => setHeroInputValue(e.target.value)}
            onFocus={() => setIsModalOpen(true)}
            placeholder="Ask anything about Cora... (e.g. How does 18% GST billing or WhatsApp booking work?)"
            className="w-full bg-transparent text-xs sm:text-sm md:text-[14.5px] font-sans text-zinc-950 placeholder:text-zinc-400 focus:outline-none tracking-tight cursor-text"
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
            onClick={(e) => {
              e.stopPropagation();
              handleSend(heroInputValue);
            }}
            className="px-4 py-1.5 bg-zinc-400 hover:bg-zinc-500 text-white rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer"
          >
            <Sparkles className="w-3.5 h-3.5" />
            <span>Generate</span>
          </button>
        </div>

      </div>

      {/* ── Center-Aligned Industry Chips ── */}
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

      {/* ── Full Screen Session Modal Popup (Zero Page Scroll Disturbance) ── */}
      {isModalOpen && (
        <div 
          className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-zinc-950/60 backdrop-blur-md animate-in fade-in duration-200"
          onClick={handleCloseModal}
        >
          <div
            onClick={(e) => e.stopPropagation()}
            className="w-full max-w-[760px] bg-white rounded-[28px] sm:rounded-[32px] shadow-[0px_24px_80px_rgba(0,0,0,0.25)] border border-zinc-200/90 flex flex-col max-h-[88vh] overflow-hidden animate-in zoom-in-95 duration-200"
          >
            
            {/* Modal Header */}
            <div className="px-5 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/70">
              <div className="flex items-center gap-2.5">
                <div className="w-8 h-8 rounded-full bg-zinc-950 text-white flex items-center justify-center shadow-2xs">
                  <Sparkles className="w-4 h-4 text-emerald-400" />
                </div>
                <div>
                  <h3 className="text-sm font-bold text-zinc-950">Cora AI Sales Concierge</h3>
                  <p className="text-[11px] text-zinc-500">Ask about workflows, Indian GST, pricing & features</p>
                </div>
              </div>

              <div className="flex items-center gap-1.5">
                {messages.length > 0 && (
                  <button
                    type="button"
                    onClick={handleReset}
                    title="Reset Conversation"
                    className="w-8 h-8 rounded-full text-zinc-500 hover:text-zinc-950 hover:bg-zinc-200/60 flex items-center justify-center transition-colors cursor-pointer"
                  >
                    <RotateCcw className="w-4 h-4" />
                  </button>
                )}
                <button
                  type="button"
                  onClick={handleCloseModal}
                  title="Close (Esc)"
                  className="w-8 h-8 rounded-full text-zinc-500 hover:text-zinc-950 hover:bg-zinc-200/60 flex items-center justify-center transition-colors cursor-pointer"
                >
                  <X className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* Modal Scrollable Chat Messages Container */}
            <div className="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 overscroll-contain bg-white">
              
              {/* Initial Welcome Greeting if no messages yet */}
              {messages.length === 0 && (
                <div className="py-6 text-center max-w-md mx-auto space-y-4">
                  <div className="w-12 h-12 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center mx-auto text-zinc-800">
                    <Bot className="w-6 h-6" />
                  </div>
                  <div>
                    <h4 className="text-base font-bold text-zinc-950">What can Cora do for your business?</h4>
                    <p className="text-xs text-zinc-500 mt-1">Select an industry below or ask any specific workflow question:</p>
                  </div>
                  <div className="flex flex-wrap gap-2 justify-center pt-2">
                    {industryPills.map((pill, idx) => {
                      const IconComp = pill.icon;
                      return (
                        <button
                          key={idx}
                          type="button"
                          onClick={() => handleSend(pill.query)}
                          className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-100 hover:bg-zinc-200/80 text-zinc-800 text-xs font-medium transition-colors"
                        >
                          <IconComp className="w-3.5 h-3.5 text-zinc-600" />
                          <span>{pill.label}</span>
                        </button>
                      );
                    })}
                  </div>
                </div>
              )}

              {/* Message List */}
              {messages.map((msg) => (
                <div
                  key={msg.id}
                  className={`flex flex-col ${msg.sender === 'user' ? 'items-end' : 'items-start'} gap-1.5`}
                >
                  <div
                    className={`max-w-[90%] sm:max-w-[85%] rounded-2xl px-4 py-3 text-xs sm:text-sm leading-relaxed ${
                      msg.sender === 'user'
                        ? 'bg-zinc-950 text-white rounded-br-xs font-medium'
                        : 'bg-zinc-50 text-zinc-900 rounded-bl-xs border border-zinc-200/80 font-normal'
                    }`}
                  >
                    <p className="whitespace-pre-line">{msg.text}</p>

                    {msg.highlights && msg.highlights.length > 0 && (
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-3 pt-3 border-t border-zinc-200/60">
                        {msg.highlights.map((h, i) => (
                          <div key={i} className="p-3 rounded-xl bg-white border border-zinc-200/80 text-left">
                            <div className="flex items-center justify-between gap-1 mb-1">
                              <span className="text-[11.5px] font-bold text-zinc-950">{h.title}</span>
                              {h.badge && (
                                <span className="px-1.5 py-0.5 text-[9.5px] font-mono font-bold bg-zinc-100 text-zinc-700 rounded-md">
                                  {h.badge}
                                </span>
                              )}
                            </div>
                            <p className="text-[11.5px] text-zinc-600 leading-normal">{h.desc}</p>
                          </div>
                        ))}
                      </div>
                    )}

                    {msg.ctaText && msg.ctaLink && (
                      <div className="mt-3 pt-2">
                        <a
                          href={msg.ctaLink}
                          className="inline-flex items-center gap-1.5 px-4 py-2 bg-zinc-950 text-white rounded-xl text-xs font-semibold hover:bg-zinc-800 transition-colors shadow-2xs"
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
                          className="text-[11px] font-medium bg-white hover:bg-zinc-100 text-zinc-800 px-3 py-1.5 rounded-full border border-zinc-200 shadow-2xs transition-colors cursor-pointer"
                        >
                          {qr.label}
                        </button>
                      ))}
                    </div>
                  )}
                </div>
              ))}

              {isLoading && (
                <div className="flex items-center gap-2 text-zinc-400 text-xs py-2 pl-2">
                  <div className="w-2 h-2 rounded-full bg-zinc-400 animate-pulse" />
                  <span>Cora is analyzing...</span>
                </div>
              )}

              <div ref={messagesEndRef} />
            </div>

            {/* Modal Bottom Input Bar */}
            <div className="p-3 sm:p-4 border-t border-zinc-100 bg-zinc-50/70">
              <form onSubmit={handleModalSubmit} className="flex items-center gap-2">
                <input
                  ref={modalInputRef}
                  type="text"
                  value={modalInputValue}
                  onChange={(e) => setModalInputValue(e.target.value)}
                  placeholder="Ask a question or request a workflow demo..."
                  className="flex-1 bg-white border border-zinc-200/90 rounded-2xl px-4 py-2.5 text-xs sm:text-sm text-zinc-950 placeholder:text-zinc-400 focus:outline-none focus:border-zinc-500 shadow-2xs"
                />
                <button
                  type="submit"
                  disabled={!modalInputValue.trim()}
                  className="px-4 py-2.5 bg-zinc-950 disabled:bg-zinc-300 text-white rounded-2xl text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer disabled:cursor-not-allowed"
                >
                  <Send className="w-3.5 h-3.5" />
                  <span className="hidden sm:inline">Send</span>
                </button>
              </form>
            </div>

          </div>
        </div>
      )}

    </div>
  );
}
