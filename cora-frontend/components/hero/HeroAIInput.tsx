'use client';

import React, { useState, useRef, useEffect } from 'react';
import {
  Sparkles,
  ArrowRight,
  RotateCcw,
  X,
  Camera,
  Building2,
  Receipt,
  FileSignature,
  Home,
  Briefcase,
  MessageSquare,
  Zap,
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
  { icon: Camera, label: 'Photo & Video Studio', query: 'I run a photo and video production studio' },
  { icon: Building2, label: 'Digital Agency', query: 'I manage a digital marketing or creative agency' },
  { icon: Receipt, label: '18% GST Invoicing', query: 'How does 18% GST invoicing work?' },
  { icon: FileSignature, label: 'Legal E-Signatures', query: 'Are contracts and e-signatures in Cora legally binding?' },
  { icon: Home, label: 'Real Estate AI', query: 'How does real estate property listing AI work?' },
];

function getSimpleRichReply(query: string, isIndia: boolean): {
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
      text: `Hey! I'm Shruti from Cora. What workflow would you like to automate today?`,
      highlights: [
        { title: 'Inquiry Funnel', desc: 'Auto-capture client leads from WhatsApp & web', badge: 'Auto' },
        { title: '18% GST Invoicing', desc: 'Instant UPI QR codes & compliant tax invoices', badge: 'Verified' },
      ],
      quickReplies: [
        { label: 'Photo & Video Studio', query: 'I run a photo and video production studio', iconName: 'camera' },
        { label: 'Digital Agency', query: 'I manage a digital marketing or creative agency', iconName: 'building' },
        { label: 'Real Estate Broker', query: 'I work in real estate and luxury properties', iconName: 'home' },
      ],
      ctaText: 'Start Free Forever (No Card) →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_greet',
    };
  }

  // 2. Photo / Video Studio Branch
  if (q.includes('photo') || q.includes('video') || q.includes('production studio') || q.includes('shoot')) {
    return {
      text: `Studios automate their entire commercial booking pipeline on Cora:`,
      highlights: [
        { title: 'WhatsApp Call-Sheets', desc: 'Sent 24h & 2h before call-time to crew & client phones', badge: 'Meta API' },
        { title: '18% GST & UPI', desc: 'Auto-splits CGST/SGST with direct UPI payment QR links', badge: 'Instant' },
      ],
      quickReplies: [
        { label: 'WhatsApp Call-Sheets', query: 'Tell me more about WhatsApp call-sheet alerts', iconName: 'message' },
        { label: 'Legal E-Sign Contracts', query: 'How does the legal e-sign contract vault work?', iconName: 'signature' },
      ],
      ctaText: 'Start Free Studio Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_studio',
    };
  }

  // 3. Agency Branch
  if (q.includes('agency') || q.includes('freelancer') || q.includes('consultant') || q.includes('marketing')) {
    return {
      text: `Cora replaces HoneyBook, DocuSign, and Notion for agency workflows:`,
      highlights: [
        { title: '5-Second AI Proposals', desc: 'Draft bespoke client proposals with Claude 3.5 Sonnet', badge: 'Multi-Model' },
        { title: 'Kanban Deal Pipeline', desc: 'Track clients from first inquiry to final payment', badge: 'Drag & Drop' },
      ],
      quickReplies: [
        { label: 'Pricing Plans', query: 'What are the pricing plans?', iconName: 'receipt' },
        { label: 'Sample Agreement', query: 'Show me an example client agreement', iconName: 'signature' },
      ],
      ctaText: isIndia ? 'Start Free (India ₹499/mo available) →' : 'Start Free Forever with Google →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_agency',
    };
  }

  // 4. Real Estate Branch
  if (q.includes('real estate') || q.includes('property') || q.includes('broker') || q.includes('villa') || q.includes('penthouse')) {
    return {
      text: `Cora's Real Estate Copilot saves agents 4+ hours per property listing:`,
      highlights: [
        { title: 'GEO-Targeted SEO', desc: 'Ready-to-post descriptions for MagicBricks & 99acres', badge: 'SEO Ranked' },
        { title: 'Social Video Hooks', desc: 'Viral Instagram Reels scripts & WhatsApp brochures', badge: 'Instant' },
      ],
      quickReplies: [
        { label: 'Sample Property Listing', query: 'Show me a luxury property listing sample', iconName: 'home' },
        { label: 'Check Pricing Plans', query: 'What are the pricing plans?', iconName: 'receipt' },
      ],
      ctaText: 'Try Real Estate Copilot for Free →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_realestate',
    };
  }

  // 5. Pricing & Plans
  if (q.includes('price') || q.includes('cost') || q.includes('how much') || q.includes('plan') || q.includes('discount')) {
    return {
      text: `Transparent pricing with zero credit card required to start:`,
      highlights: [
        { title: 'Free Forever Plan', desc: '1,000 free AI runs/mo, Kanban CRM, and document vault', badge: '₹0 Forever' },
        { title: 'India Only Plan', desc: 'Unlimited GST invoices, WhatsApp API & UPI integration', badge: '₹499/mo' },
      ],
      quickReplies: [
        { label: 'Start Free Forever', query: 'How do I start on the Free Forever Plan?', iconName: 'zap' },
        { label: 'India ₹499 Plan', query: 'Tell me about the India ₹499 plan', iconName: 'receipt' },
      ],
      ctaText: 'Sign up for Free Forever (No Card) →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_pricing',
    };
  }

  // 6. GST & Invoicing
  if (q.includes('gst') || q.includes('tax') || q.includes('invoice') || q.includes('bill') || q.includes('upi')) {
    return {
      text: `Automated Indian B2B GST calculation and payment collection:`,
      highlights: [
        { title: 'Auto Tax Split', desc: 'Calculates CGST/SGST (9%+9%) or IGST (18%) with GSTIN lookup', badge: 'Compliant' },
        { title: 'Razorpay UPI QR', desc: 'Direct UPI payment links for instant bank settlements', badge: 'Direct Pay' },
      ],
      quickReplies: [
        { label: 'Legal Contract Integration', query: 'Can I attach legal contracts to invoices?', iconName: 'signature' },
        { label: 'Try Free Forever', query: 'How do I start on the Free Forever Plan?', iconName: 'zap' },
      ],
      ctaText: 'Generate Compliant Invoices on Cora →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_gst',
    };
  }

  // 7. E-Signatures
  if (q.includes('esign') || q.includes('sign') || q.includes('legal') || q.includes('contract') || q.includes('agreement') || q.includes('security')) {
    return {
      text: `100% legally binding contracts under Section 10A of the Indian IT Act:`,
      highlights: [
        { title: 'SHA-256 Audit Seal', desc: 'Verified IP timestamps and tamper-evident signatures', badge: 'Legally Binding' },
        { title: 'Cancel DocuSign', desc: 'Save $40+/mo by using Cora’s built-in PDF vault for free', badge: 'Free Vault' },
      ],
      quickReplies: [
        { label: 'Check Pricing Plans', query: 'What are the pricing plans?', iconName: 'receipt' },
        { label: 'Set Up E-Sign Vault', query: 'How do I set up my workspace?', iconName: 'signature' },
      ],
      ctaText: 'Unlock Free E-Sign Vault →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_esign',
    };
  }

  // Fallback
  return {
    text: `Cora replaces 5+ separate subscriptions in one clean AI workspace:`,
    highlights: [
      { title: 'Multi-Model AI', desc: 'Claude 3.5 Sonnet, GPT-4o, and Gemini routing', badge: 'Frontier AI' },
      { title: 'Funnels & GST Billing', desc: 'Lead capture, WhatsApp alerts, and legal e-sign vault', badge: 'All-in-One' },
    ],
    quickReplies: [
      { label: 'Studio Features', query: 'I run a photo and video production studio', iconName: 'camera' },
      { label: 'Agency Features', query: 'I manage a digital marketing or creative agency', iconName: 'building' },
    ],
    ctaText: isIndia ? 'Start Free (India ₹499/mo available) →' : 'Start Free Forever with Google →',
    ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_default',
  };
}

function renderQuickReplyIcon(iconName?: string) {
  switch (iconName) {
    case 'camera':
      return <Camera className="w-3 h-3 text-zinc-700" />;
    case 'building':
      return <Building2 className="w-3 h-3 text-zinc-700" />;
    case 'receipt':
      return <Receipt className="w-3 h-3 text-zinc-700" />;
    case 'signature':
      return <FileSignature className="w-3 h-3 text-zinc-700" />;
    case 'home':
      return <Home className="w-3 h-3 text-zinc-700" />;
    case 'briefcase':
      return <Briefcase className="w-3 h-3 text-zinc-700" />;
    case 'message':
      return <MessageSquare className="w-3 h-3 text-zinc-700" />;
    default:
      return <Zap className="w-3 h-3 text-zinc-700" />;
  }
}

export function HeroAIInput() {
  const [prompt, setPrompt] = useState<string>('');
  const [isActive, setIsActive] = useState<boolean>(false);
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [isIndia, setIsIndia] = useState<boolean>(true);
  const [messages, setMessages] = useState<Message[]>([]);
  const chatContainerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    try {
      const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
      setIsIndia(tz.includes('Calcutta') || tz.includes('Kolkata') || tz.includes('IST'));
    } catch {
      setIsIndia(true);
    }
  }, []);

  // Smooth, non-intrusive internal scroll
  useEffect(() => {
    if (isActive && chatContainerRef.current) {
      const el = chatContainerRef.current;
      el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
    }
  }, [messages, isLoading, isActive]);

  const handleSend = (textToSend?: string) => {
    const query = (textToSend || prompt).trim();
    if (!query) return;

    setIsActive(true);

    const userMsg: Message = {
      id: `user-${Date.now()}`,
      sender: 'user',
      text: query,
      timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    };

    setMessages((prev) => [...prev, userMsg]);
    setPrompt('');
    setIsLoading(true);
    trackEvent('hero_sdr_query_sent', { query: query.slice(0, 50) });

    setTimeout(() => {
      const sdrData = getSimpleRichReply(query, isIndia);
      const sdrMsg: Message = {
        id: `sdr-${Date.now()}`,
        sender: 'sdr',
        text: sdrData.text,
        highlights: sdrData.highlights,
        quickReplies: sdrData.quickReplies,
        ctaText: sdrData.ctaText,
        ctaLink: sdrData.ctaLink,
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      };
      setMessages((prev) => [...prev, sdrMsg]);
      setIsLoading(false);
      trackEvent('hero_sdr_response_rendered');
    }, 350);
  };

  const handleCloseModal = () => {
    setIsActive(false);
  };

  const handleResetChat = () => {
    setMessages([]);
    setPrompt('');
    setIsActive(false);
  };

  return (
    <div className="w-full max-w-[880px] flex flex-col items-center gap-4 relative z-20 text-left">
      
      {/* ── 1. Minimal Normal (Idle) State Card ── */}
      {!isActive && (
        <>
          <div className="w-full bg-white/95 backdrop-blur-xl rounded-2xl sm:rounded-3xl shadow-[0px_25px_60px_-15px_rgba(0,0,0,0.18)] outline outline-1 outline-offset-[-1px] outline-white/90 p-4 sm:p-5 transition-all duration-300 hover:shadow-[0px_30px_70px_-15px_rgba(0,0,0,0.22)]">
            
            {/* Input Row */}
            <div className="relative mb-2">
              <textarea
                value={prompt}
                onChange={(e) => setPrompt(e.target.value)}
                onKeyDown={(e) => {
                  if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    handleSend();
                  }
                }}
                placeholder="Ask anything about Cora... (e.g. How does 18% GST billing or WhatsApp booking work?)"
                rows={2}
                className="w-full bg-transparent text-zinc-950 placeholder-zinc-400 font-sans text-sm sm:text-base resize-none focus:outline-none leading-relaxed"
              />
            </div>

            {/* Bottom Bar: Clean with Generate Button on Right */}
            <div className="flex items-center justify-between pt-2 border-t border-zinc-100">
              <span className="text-xs text-zinc-500 font-medium hidden sm:inline-block">
                Ask our AI Sales Concierge • No signup needed
              </span>

              <button
                type="button"
                disabled={isLoading || !prompt.trim()}
                onClick={() => handleSend()}
                className="ml-auto inline-flex items-center justify-center gap-1.5 bg-zinc-950 text-white disabled:opacity-40 disabled:cursor-not-allowed hover:bg-zinc-800 px-5 sm:px-6 py-2 rounded-full font-sans text-xs sm:text-sm font-semibold shadow-sm active:translate-y-0 hover:-translate-y-0.5 transition-all"
              >
                <Sparkles className="w-3.5 h-3.5" />
                <span>Generate</span>
              </button>
            </div>

          </div>

          {/* Quick Action Pill Tags (Clean Vector Icons Only, No Emojis) */}
          <div className="flex items-center justify-center gap-2 flex-wrap max-w-full px-2">
            {idlePills.map((pill, idx) => {
              const Icon = pill.icon;
              return (
                <button
                  key={idx}
                  type="button"
                  onClick={() => handleSend(pill.query)}
                  className="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white/90 backdrop-blur-md border border-white/80 rounded-full text-zinc-800 text-xs font-semibold shadow-2xs hover:bg-white hover:border-zinc-300 hover:-translate-y-0.5 transition-all whitespace-nowrap"
                >
                  <Icon className="w-3.5 h-3.5 text-zinc-700" />
                  <span>{pill.label}</span>
                </button>
              );
            })}
          </div>
        </>
      )}

      {/* ── 2. Active Discussion Modal with Smooth Non-Trapping Scrolling ── */}
      {isActive && (
        <div className="w-full bg-white/98 backdrop-blur-xl rounded-2xl sm:rounded-3xl shadow-[0px_25px_60px_-15px_rgba(0,0,0,0.22)] border border-zinc-200/90 p-4 sm:p-5 text-left transition-all duration-300">
          
          {/* Header Bar */}
          <div className="flex items-center justify-between pb-3 border-b border-zinc-100 mb-3">
            <div className="flex items-center gap-2.5">
              <div className="w-7 h-7 rounded-full bg-zinc-950 text-white flex items-center justify-center text-xs font-bold font-display shadow-2xs">
                S
              </div>
              <div>
                <div className="text-xs font-bold text-zinc-950 flex items-center gap-1.5">
                  <span>Shruti</span>
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                  <span className="text-[10px] font-normal text-emerald-600 bg-emerald-50 px-1.5 py-0.2 rounded">Online</span>
                </div>
                <div className="text-[11px] text-zinc-500">Cora Sales Concierge • Discussion</div>
              </div>
            </div>

            <div className="flex items-center gap-2">
              <button
                type="button"
                onClick={handleResetChat}
                className="inline-flex items-center gap-1 text-[11px] text-zinc-500 hover:text-zinc-900 px-2 py-1 rounded hover:bg-zinc-100 transition-colors"
                title="Reset conversation"
              >
                <RotateCcw className="w-3 h-3" />
                <span>Reset</span>
              </button>

              <button
                type="button"
                onClick={handleCloseModal}
                className="w-7 h-7 rounded-full text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 flex items-center justify-center transition-colors"
                title="Minimize discussion"
              >
                <X className="w-4 h-4" />
              </button>
            </div>
          </div>

          {/* Chat Messages Stream with Smooth Touch & Mouse Scrolling */}
          <div
            ref={chatContainerRef}
            className="space-y-3 max-h-[460px] overflow-y-auto pr-1 mb-3 scroll-smooth touch-pan-y overscroll-contain"
            style={{ WebkitOverflowScrolling: 'touch' }}
          >
            {messages.map((msg) => (
              <div
                key={msg.id}
                className={`flex flex-col ${msg.sender === 'user' ? 'items-end' : 'items-start'}`}
              >
                {/* Message Bubble */}
                <div
                  className={`max-w-[94%] sm:max-w-[85%] rounded-2xl p-3 sm:p-3.5 text-xs sm:text-sm leading-relaxed ${
                    msg.sender === 'user'
                      ? 'bg-zinc-950 text-white rounded-br-xs shadow-sm'
                      : 'bg-zinc-50 text-zinc-900 border border-zinc-200/70 rounded-bl-xs shadow-2xs'
                  }`}
                >
                  {/* SDR Intro Text */}
                  <p className="font-medium text-zinc-900 mb-2 whitespace-pre-line">{msg.text}</p>

                  {/* Concise 2-Tile Highlights */}
                  {msg.sender === 'sdr' && msg.highlights && msg.highlights.length > 0 && (
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 my-2.5">
                      {msg.highlights.map((h, hIdx) => (
                        <div
                          key={hIdx}
                          className="bg-white border border-zinc-200/80 rounded-xl p-2.5 shadow-2xs flex flex-col justify-between text-left"
                        >
                          <div>
                            <div className="flex items-center justify-between gap-1 mb-0.5">
                              <span className="font-bold text-xs text-zinc-950">{h.title}</span>
                              {h.badge && (
                                <span className="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 bg-zinc-100 text-zinc-700 rounded">
                                  {h.badge}
                                </span>
                              )}
                            </div>
                            <p className="text-[11px] text-zinc-600 leading-snug">{h.desc}</p>
                          </div>
                        </div>
                      ))}
                    </div>
                  )}

                  {/* Quick Reply Chips (Clean Vector Icons Only) */}
                  {msg.sender === 'sdr' && msg.quickReplies && (
                    <div className="mt-2 pt-2 border-t border-zinc-200/60 flex items-center gap-1.5 flex-wrap">
                      {msg.quickReplies.map((qr, idx) => (
                        <button
                          key={idx}
                          type="button"
                          onClick={() => handleSend(qr.query)}
                          className="inline-flex items-center gap-1 text-[11px] font-semibold bg-white border border-zinc-200 text-zinc-900 hover:bg-zinc-100 hover:border-zinc-300 px-2.5 py-1 rounded-full shadow-2xs hover:-translate-y-0.5 active:translate-y-0 transition-all"
                        >
                          {renderQuickReplyIcon(qr.iconName)}
                          <span>{qr.label}</span>
                        </button>
                      ))}
                    </div>
                  )}

                  {/* High-Converting 1-Click CTA */}
                  {msg.sender === 'sdr' && msg.ctaLink && (
                    <div className="mt-2.5 pt-2.5 border-t border-zinc-200/70 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                      <a
                        href={msg.ctaLink}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="inline-flex items-center gap-1.5 bg-zinc-950 text-white hover:bg-zinc-800 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm hover:-translate-y-0.5 active:translate-y-0"
                      >
                        <span>{msg.ctaText}</span>
                        <ArrowRight className="w-3.5 h-3.5" />
                      </a>
                      <span className="text-[10px] text-zinc-500 font-medium">1,000 Free AI Runs • No Card</span>
                    </div>
                  )}
                </div>

                <span className="text-[10px] text-zinc-400 mt-0.5 px-1 font-mono">
                  {msg.timestamp}
                </span>
              </div>
            ))}

            {isLoading && (
              <div className="flex items-center gap-2 text-xs text-zinc-500 p-2">
                <div className="w-2 h-2 rounded-full bg-zinc-400 animate-ping" />
                <span>Shruti is typing...</span>
              </div>
            )}
          </div>

          {/* Active Input Row */}
          <div className="pt-2.5 border-t border-zinc-100 flex items-center gap-2">
            <input
              type="text"
              value={prompt}
              onChange={(e) => setPrompt(e.target.value)}
              onKeyDown={(e) => {
                if (e.key === 'Enter') {
                  e.preventDefault();
                  handleSend();
                }
              }}
              placeholder="Ask a question or select a topic above..."
              className="flex-1 bg-zinc-50 border border-zinc-200/80 rounded-full px-3.5 py-2 text-xs sm:text-sm text-zinc-950 placeholder-zinc-400 focus:outline-none focus:border-zinc-400 transition-colors"
            />

            <button
              type="button"
              disabled={isLoading || !prompt.trim()}
              onClick={() => handleSend()}
              className="inline-flex items-center justify-center gap-1.5 bg-zinc-950 text-white disabled:opacity-40 disabled:cursor-not-allowed hover:bg-zinc-800 px-4 py-2 rounded-full font-sans text-xs sm:text-sm font-semibold shadow-sm active:translate-y-0 hover:-translate-y-0.5 transition-all shrink-0"
            >
              <Sparkles className="w-3.5 h-3.5" />
              <span>Send</span>
            </button>
          </div>

        </div>
      )}

    </div>
  );
}
