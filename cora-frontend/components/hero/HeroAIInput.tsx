'use client';

import React, { useState, useRef, useEffect } from 'react';
import {
  Sparkles,
  ArrowRight,
  RotateCcw,
  Camera,
  Building2,
  Receipt,
  FileSignature,
  Home,
  Briefcase,
  Layers,
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

const idlePills = [
  { icon: Camera, label: 'Photo & Video Studio', query: 'How does Cora help a photo and video studio?' },
  { icon: Building2, label: 'Digital Agency', query: 'How does Cora manage agency clients and proposals?' },
  { icon: Receipt, label: '18% GST Invoicing', query: 'Make a ₹15,000 invoice with 18% GST for Rahul' },
  { icon: FileSignature, label: 'Legal E-Signatures', query: 'How do legal e-signatures and client agreements work?' },
  { icon: Home, label: 'Real Estate AI', query: 'How does real estate property listing AI work?' },
];

function getSimpleRichReply(query: string): {
  text: string;
  highlights?: FeatureHighlight[];
  quickReplies?: QuickReply[];
  ctaText?: string;
  ctaLink?: string;
} {
  const q = query.trim().toLowerCase();

  // 1. Greetings
  if (['hey', 'hi', 'hello', 'yo', 'sup', 'heya', 'good morning', 'good afternoon', 'hola'].includes(q) || q.length <= 3) {
    return {
      text: `Hey! I'm Cora, your AI co-founder. What workflow would you like to run today?`,
      highlights: [
        { title: 'Inquiry Funnel', desc: 'Auto-capture client leads from WhatsApp & web', badge: 'Auto' },
        { title: '18% GST Invoicing', desc: 'Instant UPI QR codes & compliant tax invoices', badge: 'Verified' },
      ],
      quickReplies: [
        { label: 'Photo & Video Studio', query: 'How does Cora help a photo and video studio?', iconName: 'camera' },
        { label: 'Digital Agency', query: 'How does Cora manage agency clients and proposals?', iconName: 'building' },
        { label: '18% GST Invoicing', query: 'Make a ₹15,000 invoice with 18% GST for Rahul', iconName: 'receipt' },
      ],
      ctaText: 'Start Free Forever (No Card) →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_greet',
    };
  }

  // 2. GST & Invoicing
  if (q.includes('gst') || q.includes('invoice') || q.includes('bill') || q.includes('tax') || q.includes('15,000') || q.includes('15000')) {
    return {
      text: `Cora creates compliant 18% GST invoices in under 10 seconds:`,
      highlights: [
        { title: 'Auto CGST / SGST', desc: 'Automatic state split calculations with zero math errors', badge: '18% GST' },
        { title: 'Direct UPI & QR', desc: 'Clients scan to pay instantly via PhonePe, GPay, or Paytm', badge: 'UPI' },
      ],
      quickReplies: [
        { label: 'Legal E-Signatures', query: 'How do legal e-signatures and client agreements work?', iconName: 'signature' },
        { label: 'Pricing Plans', query: 'What are the pricing plans?', iconName: 'zap' },
      ],
      ctaText: 'Generate First GST Invoice Free →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_gst',
    };
  }

  // 3. Studio / Photo / Video
  if (q.includes('photo') || q.includes('video') || q.includes('studio') || q.includes('shoot')) {
    return {
      text: `Studios manage their bookings, client terms, and billing in one unified chat:`,
      highlights: [
        { title: 'Client Booking Notes', desc: 'Auto-track hold dates, call times, and service packages', badge: 'Bookings' },
        { title: 'Instant WhatsApp Bills', desc: 'Share payment receipts and UPI QR links directly on WhatsApp', badge: 'Billing' },
      ],
      quickReplies: [
        { label: '18% GST Invoices', query: 'Make a ₹15,000 invoice with 18% GST for Rahul', iconName: 'receipt' },
        { label: 'Legal E-Signatures', query: 'How do legal e-signatures work?', iconName: 'signature' },
      ],
      ctaText: 'Start Free Studio Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_studio',
    };
  }

  // 4. Agency / Solopreneur
  if (q.includes('agency') || q.includes('digital') || q.includes('creative') || q.includes('freelancer')) {
    return {
      text: `Agencies replace 8-10 disconnected apps with one conversational workspace:`,
      highlights: [
        { title: 'Fast Client Scopes', desc: 'Draft custom project proposals with your saved rates', badge: 'Proposals' },
        { title: 'Deal Pipeline', desc: 'Track clients from first inquiry to final payment', badge: 'CRM' },
      ],
      quickReplies: [
        { label: 'Pricing Plans', query: 'What are the pricing plans for Cora?', iconName: 'receipt' },
        { label: '18% GST Invoicing', query: 'Make a ₹15,000 invoice with 18% GST for Rahul', iconName: 'receipt' },
      ],
      ctaText: 'Start Free Agency Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_agency',
    };
  }

  // 5. Default
  return {
    text: `Cora is your AI co-founder. Type what you need in plain English or Hinglish:`,
    highlights: [
      { title: 'Single Chat Input', desc: 'Type what you need done without clicking through 10 apps', badge: 'Simple' },
      { title: 'Business Memory', desc: 'Remembers your client rates, history, and active jobs', badge: 'Context' },
    ],
    quickReplies: [
      { label: '18% GST Invoicing', query: 'Make a ₹15,000 invoice with 18% GST for Rahul', iconName: 'receipt' },
      { label: 'Pricing Plans', query: 'What are the pricing plans?', iconName: 'zap' },
    ],
    ctaText: 'Start Free Forever (No Card) →',
    ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_default',
  };
}

export function HeroAIInput() {
  const [inputValue, setInputValue] = useState('');
  const [messages, setMessages] = useState<Message[]>([]);
  const [isOpen, setIsOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  useEffect(() => {
    if (isOpen && messages.length > 0) {
      scrollToBottom();
    }
  }, [messages, isOpen]);

  const handleSend = (textToSend?: string) => {
    const text = (textToSend || inputValue).trim();
    if (!text) return;

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
    }, 450);
  };

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      handleSend();
    }
  };

  const handleReset = () => {
    setMessages([]);
    setIsOpen(false);
    setInputValue('');
    trackEvent('hero_ai_chat_reset');
  };

  return (
    <div className="w-full max-w-[820px] mx-auto text-left relative z-20">
      
      {/* ── Main Outer White Card ── */}
      <div className="w-full bg-white/95 backdrop-blur-xl border border-white/80 rounded-[28px] sm:rounded-[32px] p-4 sm:p-6 shadow-[0px_16px_48px_rgba(0,0,0,0.08)] ring-1 ring-black/[0.04] transition-all">
        
        {/* Top Input Bar Area */}
        <div className="relative flex items-center justify-between gap-3 pb-3 border-b border-zinc-100/90">
          <input
            ref={inputRef}
            type="text"
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            onKeyDown={handleKeyDown}
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
        </div>

        {/* Expanded Messages Viewport */}
        {isOpen && messages.length > 0 && (
          <div className="my-4 max-h-[320px] overflow-y-auto space-y-4 pr-1 scrollbar-thin scrollbar-thumb-zinc-200">
            {messages.map((msg) => (
              <div
                key={msg.id}
                className={`flex flex-col ${msg.sender === 'user' ? 'items-end' : 'items-start'} gap-1.5`}
              >
                <div
                  className={`max-w-[85%] rounded-2xl px-4 py-3 text-xs sm:text-sm leading-relaxed ${
                    msg.sender === 'user'
                      ? 'bg-zinc-950 text-white rounded-br-xs font-medium'
                      : 'bg-zinc-100/90 text-zinc-900 rounded-bl-xs border border-zinc-200/70 font-normal'
                  }`}
                >
                  <p className="whitespace-pre-line">{msg.text}</p>

                  {msg.highlights && msg.highlights.length > 0 && (
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-3 pt-3 border-t border-zinc-200/60">
                      {msg.highlights.map((h, i) => (
                        <div key={i} className="p-2.5 rounded-xl bg-white border border-zinc-200/80 text-left">
                          <div className="flex items-center justify-between gap-1 mb-1">
                            <span className="text-[11px] font-bold text-zinc-950">{h.title}</span>
                            {h.badge && (
                              <span className="px-1.5 py-0.5 text-[9px] font-mono font-bold bg-zinc-100 text-zinc-700 rounded-md">
                                {h.badge}
                              </span>
                            )}
                          </div>
                          <p className="text-[11px] text-zinc-500 leading-normal">{h.desc}</p>
                        </div>
                      ))}
                    </div>
                  )}

                  {msg.ctaText && msg.ctaLink && (
                    <div className="mt-3 pt-2">
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
                        onClick={() => handleSend(qr.query)}
                        className="text-[11px] font-medium bg-white hover:bg-zinc-100 text-zinc-800 px-2.5 py-1 rounded-full border border-zinc-200 shadow-2xs transition-colors"
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
                <span>Cora is thinking...</span>
              </div>
            )}

            <div ref={messagesEndRef} />
          </div>
        )}

        {/* Bottom Action Row Inside Card */}
        <div className="flex items-center justify-between pt-3 text-xs">
          <span className="text-zinc-500 text-[11.5px] font-medium">
            Ask our AI Sales Concierge &bull; No signup needed
          </span>

          <div className="flex items-center gap-2">
            {isOpen && (
              <button
                onClick={handleReset}
                title="Reset Chat"
                className="w-7 h-7 rounded-lg text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 flex items-center justify-center transition-colors"
              >
                <RotateCcw className="w-3.5 h-3.5" />
              </button>
            )}

            <button
              onClick={() => handleSend()}
              className="px-4 py-1.5 bg-zinc-400 hover:bg-zinc-500 text-white rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-2xs"
            >
              <Sparkles className="w-3.5 h-3.5" />
              <span>Generate</span>
            </button>
          </div>
        </div>

      </div>

      {/* ── Bottom Row Outside Card (Pills + Made in Cora Badge) ── */}
      <div className="mt-4 flex items-center justify-between flex-wrap gap-2.5">
        <div className="flex items-center gap-2 flex-wrap">
          {idlePills.map((pill, idx) => {
            const IconComp = pill.icon;
            return (
              <button
                key={idx}
                onClick={() => handleSend(pill.query)}
                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/90 hover:bg-white text-zinc-800 text-xs font-medium transition-all hover:-translate-y-0.5 border border-zinc-200/80 shadow-2xs"
              >
                <IconComp className="w-3.5 h-3.5 text-zinc-600" />
                <span>{pill.label}</span>
              </button>
            );
          })}
        </div>

        {/* Made in Cora Badge */}
        <div className="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white/90 backdrop-blur-md rounded-full border border-zinc-200/80 text-zinc-800 text-xs font-semibold shadow-2xs">
          <Layers className="w-3.5 h-3.5 text-zinc-900" />
          <span>Made in Cora</span>
        </div>
      </div>

    </div>
  );
}
