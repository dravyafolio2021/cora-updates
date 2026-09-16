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
  CheckCircle2,
  ChevronRight,
  SlidersHorizontal,
  Bot
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

const agencyPills = [
  { 
    id: 'web_design',
    icon: Globe, 
    label: 'Web Design & Dev Agencies', 
    query: 'How does Cora help web design & dev agencies deliver client websites with ready client portals and billing?' 
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
    query: 'How does Cora help marketing agencies handle monthly client retainers, 18% GST invoices, and WhatsApp reports?' 
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
      text: `I couldn't quite understand "${original}". I'm Cora's AI Agency SDR, trained to help web, creative, software, and marketing agencies automate client handoffs and operations.`,
      highlights: [
        { title: 'Client Portals on Handoff', desc: 'Deliver clients a branded operating workspace alongside their new website', badge: 'Client Handoff' },
        { title: '18% GST & Dynamic UPI', desc: 'Automated milestone invoices with instant zero-fee UPI QR & SAC 9983 splits', badge: 'Billing' },
      ],
      quickReplies: [
        { label: 'Web Design & Dev', query: 'How does Cora help web design & dev agencies?' },
        { label: 'Creative & Branding', query: 'How does Cora help creative studios deliver brand assets?' },
        { label: 'Performance & Retainers', query: 'How does Cora automate monthly client retainers?' },
      ],
      ctaText: 'Explore Free Agency Workspace (₹0) →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_gibberish',
    };
  }

  // 1. Web Design & Development Agencies
  if (q.includes('web') || q.includes('website') || q.includes('wordpress') || q.includes('webflow') || q.includes('framer') || q.includes('developer') || q.includes('shopify') || q.includes('elementor')) {
    return {
      text: `Here is how Cora turns web design agencies into full-service client partners:`,
      highlights: [
        { title: 'Instant Client Portal Handoff', desc: 'When you build a client website, deliver Cora as their built-in CRM, booking form & invoice manager', badge: 'Client Handoff' },
        { title: 'Scope Lock & Milestone Sign-Off', desc: 'Stop endless unpaid revisions with SHA-256 digital milestone acceptance before staging push', badge: 'Contracts' },
        { title: '18% GST Web Development Invoices', desc: 'Auto-calculate SAC 9983 splits and collect 50% advance / 50% launch payments via UPI QR', badge: 'Finance' },
        { title: 'Form Lead Forwarding to WhatsApp', desc: 'Client website contact forms route directly to their phone via automated WhatsApp alerts', badge: 'Lead Engine' },
      ],
      quickReplies: [
        { label: 'Client Handoff Architecture', query: 'How do agencies hand off Cora portals to clients?' },
        { label: 'Scope Creep Defense Contracts', query: 'How does Cora protect web agencies from scope creep?' },
        { label: 'Monthly Retainer Billing', query: 'How does monthly retainer billing work for agencies?' },
      ],
      ctaText: 'Launch Free Web Agency Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?industry=custom&agency_type=web_design&source=sdr_web_agency',
    };
  }

  // 2. Creative, Branding & Design Studios
  if (q.includes('creative') || q.includes('brand') || q.includes('design') || q.includes('logo') || q.includes('graphic') || q.includes('ui/ux') || q.includes('ux') || q.includes('figma')) {
    return {
      text: `Here is how Cora empowers creative & branding studios to protect margins and deliver smoothly:`,
      highlights: [
        { title: 'Asset Proofing & Client Sign-Off', desc: 'Clients review brand guidelines, logos, and decks with threaded feedback and signed approvals', badge: 'Proofing' },
        { title: 'Retainer & Advance Milestone Escrow', desc: 'Lock 50% advance deposit with dynamic UPI links before kickoff and final files release', badge: 'Cash Flow' },
        { title: 'Reusable Creative SOW Templates', desc: 'One-click generate legal design retainers, copyright transfer deeds, and NDAs', badge: 'Legal Vault' },
        { title: 'Zero Email Attachment Limits', desc: 'Deliver high-res brand decks in clean, custom-branded client portals with live open-tracking', badge: 'Branded Portal' },
      ],
      quickReplies: [
        { label: 'Brand Asset Handoff Flow', query: 'How does client brand asset delivery work in Cora?' },
        { label: 'Copyright Transfer Deeds', query: 'How are intellectual property deeds handled?' },
        { label: 'Performance & Retainers', query: 'How does Cora handle monthly client retainers?' },
      ],
      ctaText: 'Start Free Studio Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?industry=custom&agency_type=branding&source=sdr_creative_studio',
    };
  }

  // 3. Performance Marketing, Ads & SEO Agencies
  if (q.includes('marketing') || q.includes('seo') || q.includes('ad') || q.includes('meta ads') || q.includes('google ads') || q.includes('retainer') || q.includes('growth') || q.includes('social media') || q.includes('lead gen')) {
    return {
      text: `Here is how Cora automates operations for performance marketing & SEO agencies:`,
      highlights: [
        { title: 'Recurring Monthly Retainer Invoices', desc: 'Auto-generate 1st-of-the-month GST invoices with UPI payment links sent directly on WhatsApp', badge: 'Retainers' },
        { title: 'Ad Spend Reconciliation', desc: 'Track agency service fee vs client ad spend pass-through with clear transparent tax splits', badge: 'Margin Guard' },
        { title: 'Lead Funnel Kanban with WhatsApp', desc: 'Centralize leads generated from client ad campaigns and auto-dispatch instantly to their team', badge: 'CRM Pipeline' },
        { title: 'Zero-Awkwardness Auto Follow-Ups', desc: 'Polite, automated WhatsApp reminder sequences for overdue retainer invoices', badge: 'Follow-ups' },
      ],
      quickReplies: [
        { label: 'Retainer Agreement Templates', query: 'What contract clauses protect monthly marketing retainers?' },
        { label: '18% GST Invoice Demo', query: 'Make a ₹45,000 monthly retainer invoice with 18% GST' },
        { label: 'Web Design Agencies', query: 'How does Cora help web design & dev agencies?' },
      ],
      ctaText: 'Start Free Marketing Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?industry=custom&agency_type=marketing&source=sdr_marketing_agency',
    };
  }

  // 4. Software & App Development Shops
  if (q.includes('software') || q.includes('app') || q.includes('saas') || q.includes('sprint') || q.includes('dev') || q.includes('tech') || q.includes('api') || q.includes('code')) {
    return {
      text: `Here is how Cora manages client sprints, contracts, and handoffs for software development shops:`,
      highlights: [
        { title: 'Bi-Weekly Sprint Milestone Sign-Off', desc: 'Clients sign off on user acceptance testing (UAT) and release milestone payments in 1 click', badge: 'Milestones' },
        { title: 'IP Transfer & Software SLA Deeds', desc: 'Legally admissible IP assignment and maintenance SLA contracts with cryptographic audit logs', badge: 'Legal SLA' },
        { title: 'Automated TDS & GST Invoicing', desc: 'Includes SAC 9983 software consulting classifications and TDS Section 194J guidance', badge: 'Compliance' },
        { title: 'Multi-Tenant Client Portal Access', desc: 'Give client stakeholders unified access to contracts, sprint deliverables, and tax invoices', badge: 'Client Hub' },
      ],
      quickReplies: [
        { label: 'Sprint Milestone Contracts', query: 'How does milestone-based software billing work?' },
        { label: 'Client Handoff Kit', query: 'How do agencies hand off Cora portals to clients?' },
        { label: 'Web Design Agencies', query: 'How does Cora help web design & dev agencies?' },
      ],
      ctaText: 'Start Free Software Studio Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?industry=custom&agency_type=software&source=sdr_software_shop',
    };
  }

  // 5. Media, Video & Photo Production Houses
  if (q.includes('photo') || q.includes('video') || q.includes('media') || q.includes('production') || q.includes('shoot') || q.includes('commercial') || q.includes('film')) {
    return {
      text: `Here is how Cora powers commercial photo, video, and media production agencies:`,
      highlights: [
        { title: 'Shoot Notes & Call-Sheet Dispatch', desc: 'Auto-generate call-sheets with location pins and crew call times delivered via WhatsApp', badge: 'Dispatch' },
        { title: 'Talent Releases & Client Contracts', desc: 'Send legally binding model releases and client production agreements signed on mobile', badge: 'E-Sign' },
        { title: '50% Advance Booking Invoices', desc: 'Lock shoot dates with 18% GST tax invoices and instant UPI QR payments', badge: 'Billing' },
        { title: 'Media Proofing & Asset Selection', desc: 'Clients view watermark previews, select favorites, and approve finals in their portal', badge: 'Delivery' },
      ],
      quickReplies: [
        { label: 'Shoot Call-Sheet Workflow', query: 'How do WhatsApp call-sheets work?' },
        { label: 'Web Design Agencies', query: 'How does Cora help web design & dev agencies?' },
        { label: 'Free Plan Invoicing', query: 'What is included in the free plan?' },
      ],
      ctaText: 'Start Free Media Studio Workspace →',
      ctaLink: 'https://app.heycora.in/workspace/login?industry=photography_studio&source=sdr_media_studio',
    };
  }

  // 6. Math / Dynamic Number & GST Calculator
  const numMatch = q.match(/(?:₹|rs\.?|inr)?\s*(\d{1,3}(?:,\d{3})*|\d+)(?:\s*(?:k|thousand|lakh))?/i);
  if (numMatch && (q.includes('gst') || q.includes('tax') || q.includes('calculate') || q.includes('invoice') || q.includes('bill') || q.includes('18%'))) {
    let rawNum = parseFloat(numMatch[1].replace(/,/g, ''));
    if (q.includes('k') || q.includes('thousand')) rawNum *= 1000;
    if (q.includes('lakh')) rawNum *= 100000;

    if (rawNum > 0) {
      const cgst = Math.round(rawNum * 0.09);
      const sgst = Math.round(rawNum * 0.09);
      const total = rawNum + cgst + sgst;

      return {
        text: `Here is the exact 18% GST agency invoice breakdown for ₹${rawNum.toLocaleString('en-IN')}:`,
        highlights: [
          { title: `Base Fee: ₹${rawNum.toLocaleString('en-IN')}`, desc: 'Net agency service package / development fee before tax', badge: 'SAC 9983' },
          { title: `18% GST: ₹${(cgst + sgst).toLocaleString('en-IN')}`, desc: `CGST (9%): ₹${cgst.toLocaleString('en-IN')} + SGST (9%): ₹${sgst.toLocaleString('en-IN')}`, badge: '18% Split' },
          { title: `Total Payable: ₹${total.toLocaleString('en-IN')}`, desc: 'Total client amount with instant PhonePe / GPay QR code', badge: 'Total' },
          { title: '1-Click WhatsApp Delivery', desc: 'PDF bill with your agency logo and bank account details generated in 3 seconds', badge: 'Instant' },
        ],
        quickReplies: [
          { label: 'Generate Free Invoice', query: `Make an invoice of ₹${rawNum} for Rahul` },
          { label: 'How does UPI QR work?', query: 'How does UPI QR payment work in invoices?' },
          { label: 'Web Design Agencies', query: 'How does Cora help web design & dev agencies?' },
        ],
        ctaText: `Generate ₹${total.toLocaleString('en-IN')} Invoice Free →`,
        ctaLink: `https://app.heycora.in/workspace/login?source=sdr_calc&amount=${rawNum}`,
      };
    }
  }

  // 7. General Agency Handoff / Recommendation
  if (q.includes('recommend') || q.includes('partner') || q.includes('handoff') || q.includes('client') || q.includes('portal') || q.includes('agency')) {
    return {
      text: `Cora is designed specifically for agencies to serve and empower their clients:`,
      highlights: [
        { title: 'Ready Client Workspaces', desc: 'Give your clients a high-utility workspace to manage leads, view contracts, and pay invoices', badge: 'Client Hub' },
        { title: 'White-Label Ready', desc: 'Deliver custom branded portals with your agency stamp or completely white-labeled', badge: 'Branding' },
        { title: 'Zero Friction Onboarding', desc: 'Clients get started with zero training—simple, clean, and mobile-friendly', badge: 'UX' },
        { title: 'Recurring Value', desc: 'Keep clients retained and connected to your agency ecosystem month after month', badge: 'Retention' },
      ],
      quickReplies: [
        { label: 'Web Design & Dev', query: 'How does Cora help web design & dev agencies?' },
        { label: 'Creative & Branding', query: 'How does Cora help creative studios deliver brand assets?' },
        { label: 'Performance & Retainers', query: 'How does Cora automate monthly client retainers?' },
      ],
      ctaText: 'Join Cora Agency Partner Network →',
      ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_partner',
    };
  }

  // Default Agency Assistant
  return {
    text: `Cora is the autonomous AI operating system built for agencies and service studios. How can we help your agency grow today?`,
    highlights: [
      { title: 'Web & Dev Agencies', desc: 'Deliver branded client portals, automate milestone sign-offs & 18% GST invoices', badge: 'Web Dev' },
      { title: 'Creative & Branding Studios', desc: 'Asset proofing, digital contracts & milestone payments with zero email clutter', badge: 'Studios' },
      { title: 'Marketing & Retainers', desc: 'Auto-recurring WhatsApp invoices, lead intake CRM & campaign notifications', badge: 'Retainers' },
      { title: 'Software Development', desc: 'Sprint milestone sign-offs, IP assignment deeds & client handoff portals', badge: 'Software' },
    ],
    quickReplies: [
      { label: 'Web Design & Dev', query: 'How does Cora help web design & dev agencies?' },
      { label: 'Creative Studios', query: 'How does Cora help creative studios deliver brand assets?' },
      { label: 'Marketing Retainers', query: 'How does Cora automate monthly client retainers?' },
      { label: '18% GST Invoicing', query: 'Make a ₹25,000 invoice with 18% GST' },
    ],
    ctaText: 'Start Free Agency Workspace (No Card) →',
    ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_default',
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
            sender: 'sdr',
            text: data.output,
            highlights: data.highlights || [
              { title: 'Agency Operating System', desc: `Autonomous pipeline powered by ${data.model || 'Cora AI'} in ${data.latency || '280ms'}`, badge: 'Live AI' },
              { title: 'Client Ready Workflows', desc: 'Pre-configured with branded client portals, 18% GST invoices, and WhatsApp alerts', badge: 'Active' },
            ],
            quickReplies: [
              { label: 'Web Design & Dev', query: 'How does Cora help web design & dev agencies?' },
              { label: 'Creative Studios', query: 'How does Cora help creative studios deliver brand assets?' },
              { label: 'Pricing Plans', query: 'What are the pricing plans for Cora?' },
            ],
            ctaText: 'Start Free Agency Workspace (No Card) →',
            ctaLink: 'https://app.heycora.in/workspace/login?source=sdr_api',
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
              <span className="text-xs sm:text-sm font-bold text-zinc-950">Cora Agency AI Concierge</span>
              <span className="px-2 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-full">
                Live Agency Assistant
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
            className="hidden sm:block my-3 max-h-[320px] overflow-y-auto overscroll-contain pr-1.5 space-y-3.5 scrollbar-thin scrollbar-thumb-zinc-200"
          >
            {messages.length === 0 ? (
              <div className="py-4 text-center text-zinc-500 text-xs">
                Ask how Cora powers client handoffs, 18% GST billing, and portals for agencies:
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
        <form onSubmit={handleSubmit} className="relative flex items-center justify-between gap-2.5 sm:gap-3 pb-2.5 sm:pb-3 border-b border-zinc-100/90">
          <input
            ref={inputRef}
            type="text"
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            onFocus={() => {
              if (!isExpanded) setIsExpanded(true);
            }}
            placeholder="Ask anything about Cora... (e.g. How do web agencies deliver client portals?)"
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
            Ask our AI Agency Concierge &bull; No signup needed
          </span>

          <button
            type="button"
            onClick={() => handleSend()}
            className="px-3.5 sm:px-4 py-1.5 bg-zinc-900 hover:bg-zinc-950 text-white rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer shrink-0"
          >
            <Sparkles className="w-3.5 h-3.5 text-emerald-400" />
            <span>Generate</span>
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
                  <span className="text-[13px] font-bold text-zinc-950 leading-tight block">Cora Agency AI Concierge</span>
                  <span className="text-[10px] text-zinc-500 font-medium">Live Agency Assistant &bull; No Signup</span>
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
            <div className="flex-1 overflow-y-auto my-3 space-y-3 pr-1 max-h-[50vh]">
              {messages.length === 0 ? (
                <div className="py-6 text-center text-zinc-500 text-xs px-4">
                  Select an agency type below or type any question about proposals, GST billing, and client portals:
                </div>
              ) : (
                messages.map((msg) => (
                  <div
                    key={msg.id}
                    className={`flex flex-col ${msg.sender === 'user' ? 'items-end' : 'items-start'} gap-1`}
                  >
                    <div
                      className={`max-w-[90%] rounded-2xl px-3.5 py-2.5 text-xs leading-relaxed ${
                        msg.sender === 'user'
                          ? 'bg-zinc-950 text-white rounded-br-xs font-medium'
                          : 'bg-zinc-50 text-zinc-900 rounded-bl-xs border border-zinc-200/80 font-normal'
                      }`}
                    >
                      <p className="whitespace-pre-line">{msg.text}</p>

                      {msg.highlights && msg.highlights.length > 0 && (
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

                      {msg.ctaText && msg.ctaLink && (
                        <div className="mt-2.5 pt-1">
                          <a
                            href={msg.ctaLink}
                            className="inline-flex items-center gap-1 px-3 py-1.5 bg-zinc-950 text-white rounded-lg text-[11px] font-semibold hover:bg-zinc-800 transition-colors"
                          >
                            <span>{msg.ctaText}</span>
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
                            className="text-[10px] font-medium bg-zinc-100 active:bg-zinc-200 text-zinc-800 px-2 py-0.5 rounded-full border border-zinc-200 transition-colors"
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
                      className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-zinc-100 active:bg-zinc-200 text-zinc-800 text-[10.5px] font-medium transition-colors"
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
                placeholder="Ask about agency client handoffs..."
                className="flex-1 bg-zinc-100 rounded-full px-3.5 py-2 text-xs text-zinc-950 placeholder:text-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-950"
              />
              <button
                type="submit"
                className="w-8 h-8 rounded-full bg-zinc-950 text-white flex items-center justify-center shrink-0 shadow-xs"
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
