'use client';

import React, { useState, useRef } from 'react';
import Link from 'next/link';
import { 
  Sparkles, 
  ArrowRight, 
  Send, 
  Check, 
  Copy, 
  X
} from 'lucide-react';
import { useToast } from '@/components/ui/Toast';

interface ToolsHeroAIInputProps {
  onSearchChange: (query: string) => void;
  searchQuery: string;
}

interface AIResponse {
  type: 'gst' | 'retainer' | 'contract' | 'upi' | 'embed' | 'general';
  title: string;
  answer: string;
  dataSnippet?: string;
  actionToolSlug: string;
  actionToolLabel: string;
}

export function ToolsHeroAIInput({ onSearchChange, searchQuery }: ToolsHeroAIInputProps) {
  const [inputVal, setInputVal] = useState<string>(searchQuery);
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [aiResponse, setAiResponse] = useState<AIResponse | null>(null);
  const [copied, setCopied] = useState<boolean>(false);
  const { showToast } = useToast();
  const inputRef = useRef<HTMLInputElement>(null);

  const quickPrompts = [
    'Calculate 18% GST on ₹75,000',
    'Model ₹3L/mo agency retainer',
    'Draft Section 10A IT Act clause',
    'Generate ₹25K UPI payment QR',
  ];

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const val = e.target.value;
    setInputVal(val);
    onSearchChange(val);
    if (!val.trim()) {
      setAiResponse(null);
    }
  };

  const handleQuickPromptClick = (prompt: string) => {
    setInputVal(prompt);
    onSearchChange(prompt);
    processAIQuery(prompt);
  };

  const processAIQuery = (queryText: string) => {
    const q = queryText.toLowerCase().trim();
    if (!q) return;

    setIsLoading(true);

    setTimeout(() => {
      // 1. Math / GST Calculator Prompt
      const numMatch = q.match(/(?:₹|rs\.?|inr)?\s*(\d{1,3}(?:,\d{3})*|\d+)(?:\s*(?:k|thousand|lakh|l))?/i);
      if (numMatch && (q.includes('gst') || q.includes('tax') || q.includes('calculate') || q.includes('invoice') || q.includes('bill') || q.includes('18%') || q.includes('cgst') || q.includes('sgst'))) {
        let rawNum = parseFloat(numMatch[1].replace(/,/g, ''));
        if (q.includes('k') || q.includes('thousand')) rawNum *= 1000;
        if (q.includes('lakh') || q.includes('l')) rawNum *= 100000;

        const base = rawNum;
        const cgst = Math.round(base * 0.09);
        const sgst = Math.round(base * 0.09);
        const total = base + cgst + sgst;

        setAiResponse({
          type: 'gst',
          title: `18% GST Breakdown for ₹${base.toLocaleString('en-IN')}`,
          answer: `Base Amount: ₹${base.toLocaleString('en-IN')} + CGST (9%): ₹${cgst.toLocaleString('en-IN')} + SGST (9%): ₹${sgst.toLocaleString('en-IN')} = Total Invoice: ₹${total.toLocaleString('en-IN')} (SAC 9983 Compliant).`,
          dataSnippet: `Base: ₹${base.toLocaleString('en-IN')} | CGST (9%): ₹${cgst.toLocaleString('en-IN')} | SGST (9%): ₹${sgst.toLocaleString('en-IN')} | Total: ₹${total.toLocaleString('en-IN')}`,
          actionToolSlug: 'gst-calculator',
          actionToolLabel: 'Open Full GST & B2B Tax Calculator',
        });
        setIsLoading(false);
        return;
      }

      // 2. Retainer Calculator Prompt
      if (q.includes('retainer') || q.includes('rate') || q.includes('hourly') || q.includes('capacity') || q.includes('scope')) {
        let target = 300000;
        if (numMatch) {
          let n = parseFloat(numMatch[1].replace(/,/g, ''));
          if (q.includes('k') || q.includes('thousand')) n *= 1000;
          if (q.includes('lakh') || q.includes('l')) n *= 100000;
          if (n > 10000) target = n;
        }

        const hourly = Math.round((target * 1.2) / 126);
        const retainerPerClient = Math.round((target * 1.2) / 4);

        setAiResponse({
          type: 'retainer',
          title: `Retainer Math for ₹${target.toLocaleString('en-IN')}/mo Target`,
          answer: `To hit ₹${target.toLocaleString('en-IN')}/mo with a 20% scope creep buffer (4 clients @ 30 hrs/wk total): Minimum retainer per client is ₹${retainerPerClient.toLocaleString('en-IN')}/mo at ₹${hourly.toLocaleString('en-IN')}/hr baseline rate.`,
          dataSnippet: `Target: ₹${target.toLocaleString('en-IN')} | Hourly Rate: ₹${hourly.toLocaleString('en-IN')}/hr | Tier: ₹${retainerPerClient.toLocaleString('en-IN')}/mo per client`,
          actionToolSlug: 'retainer-calculator',
          actionToolLabel: 'Open Retainer & Scope Calculator',
        });
        setIsLoading(false);
        return;
      }

      // 3. Contract / NDA Prompt
      if (q.includes('contract') || q.includes('nda') || q.includes('clause') || q.includes('it act') || q.includes('agreement') || q.includes('legal')) {
        setAiResponse({
          type: 'contract',
          title: 'Indian IT Act 2000 Section 10A Legal Clause',
          answer: 'Enforceable under Section 10A of the IT Act 2000: "The parties agree that electronic records, digital signatures with SHA-256 hash timestamps, and email milestone approvals constitute valid legal assent."',
          dataSnippet: 'Governed by Indian Contract Act 1872 & IT Act 2000 (Section 10A). Title transfer conditional upon 100% invoice settlement.',
          actionToolSlug: 'contract-builder',
          actionToolLabel: 'Open Contract Clause Builder',
        });
        setIsLoading(false);
        return;
      }

      // 4. UPI Prompt
      if (q.includes('upi') || q.includes('qr') || q.includes('payment') || q.includes('gpay') || q.includes('phonepe')) {
        let amt = 25000;
        if (numMatch) {
          let n = parseFloat(numMatch[1].replace(/,/g, ''));
          if (q.includes('k') || q.includes('thousand')) n *= 1000;
          if (q.includes('lakh') || q.includes('l')) n *= 100000;
          if (n > 100) amt = n;
        }

        setAiResponse({
          type: 'upi',
          title: `Dynamic UPI Payment String (₹${amt.toLocaleString('en-IN')})`,
          answer: `upi://pay?pa=business@okhdfcbank&pn=Studio+Services&am=${amt}&cu=INR&tn=Invoice+Settlement (0% gateway fees, instant settlement).`,
          dataSnippet: `upi://pay?pa=business@okhdfcbank&pn=Studio&am=${amt}&cu=INR`,
          actionToolSlug: 'upi-qr-generator',
          actionToolLabel: 'Open Dynamic UPI QR Generator',
        });
        setIsLoading(false);
        return;
      }

      // 5. Default General AI Response
      setAiResponse({
        type: 'general',
        title: 'Cora Micro-Tools Copilot',
        answer: `I matched tools for "${queryText}". You can calculate 18% GST, generate legally binding Indian IT Act contracts, model agency retainers, or generate zero-fee UPI QR codes.`,
        actionToolSlug: 'gst-calculator',
        actionToolLabel: 'Explore Recommended Tool',
      });
      setIsLoading(false);
    }, 280);
  };

  const handleFormSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!inputVal.trim()) return;
    processAIQuery(inputVal);
  };

  const copySnippet = (text: string) => {
    navigator.clipboard.writeText(text);
    setCopied(true);
    showToast('Copied to clipboard!');
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <div className="w-full max-w-[700px] mx-auto text-left">
      
      {/* ── Main AI Input Capsule ── */}
      <form
        onSubmit={handleFormSubmit}
        className="bg-white/95 backdrop-blur-md rounded-3xl p-3 sm:p-4 border border-zinc-200/90 shadow-[0_16px_45px_rgba(0,0,0,0.06)] hover:border-zinc-300 focus-within:border-zinc-950 focus-within:ring-4 focus-within:ring-zinc-950/5 transition-all duration-300 relative"
      >
        {/* Top Text Input Area */}
        <div className="flex items-center gap-3 px-2">
          <Sparkles className="w-5 h-5 text-indigo-600 shrink-0" />
          <input
            ref={inputRef}
            type="text"
            value={inputVal}
            onChange={handleInputChange}
            placeholder="Ask AI Copilot or run instant tools (e.g. 'Calculate 18% GST on ₹75,000')..."
            className="w-full bg-transparent text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:outline-none"
          />
          {inputVal && (
            <button
              type="button"
              onClick={() => {
                setInputVal('');
                onSearchChange('');
                setAiResponse(null);
              }}
              className="p-1 rounded-full text-zinc-400 hover:text-zinc-700 transition-colors"
            >
              <X className="w-4 h-4" />
            </button>
          )}
        </div>

        {/* Bottom Action Strip */}
        <div className="flex items-center justify-between pt-3 mt-3 border-t border-zinc-100/90 text-xs">
          <div className="flex items-center gap-1.5 text-zinc-500 font-medium">
            <span className="w-2 h-2 rounded-full bg-emerald-500" />
            <span className="text-[11px] sm:text-xs">AI Copilot Active • Zero login needed</span>
          </div>

          <button
            type="submit"
            disabled={isLoading || !inputVal.trim()}
            className="inline-flex items-center gap-1.5 px-4 sm:px-5 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 disabled:opacity-50 text-white font-semibold text-xs transition-all shadow-xs cursor-pointer"
          >
            {isLoading ? (
              <span>Computing...</span>
            ) : (
              <>
                <span>Run AI</span>
                <Send className="w-3 h-3 text-zinc-400" />
              </>
            )}
          </button>
        </div>
      </form>

      {/* ── Quick Prompt Chips ── */}
      <div className="flex items-center gap-1.5 overflow-x-auto whitespace-nowrap scrollbar-none py-2.5 px-1">
        <span className="text-[10px] font-mono text-zinc-400 uppercase tracking-wider mr-0.5">
          Try:
        </span>
        {quickPrompts.map((prompt, idx) => (
          <button
            key={idx}
            type="button"
            onClick={() => handleQuickPromptClick(prompt)}
            className="text-[11px] font-medium text-zinc-600 bg-white/80 hover:bg-white border border-zinc-200/80 hover:border-zinc-400 px-3 py-1 rounded-full shadow-2xs transition-all cursor-pointer hover:text-zinc-950"
          >
            {prompt}
          </button>
        ))}
      </div>

      {/* ── Interactive Clean Light AI Response Card (Zero Neon Box) ── */}
      {aiResponse && (
        <div className="mt-3 p-5 sm:p-6 rounded-3xl bg-white text-zinc-900 shadow-[0_16px_40px_rgba(0,0,0,0.08)] border border-zinc-200/90 animate-in fade-in slide-in-from-top-2 duration-200 space-y-3">
          <div className="flex items-center justify-between pb-3 border-b border-zinc-100 text-xs">
            <span className="font-bold text-zinc-950 flex items-center gap-1.5">
              <Sparkles className="w-4 h-4 text-indigo-600" />
              <span>{aiResponse.title}</span>
            </span>
            <button
              onClick={() => setAiResponse(null)}
              className="text-zinc-400 hover:text-zinc-700 p-1 transition-colors"
            >
              <X className="w-4 h-4" />
            </button>
          </div>

          <p className="text-xs sm:text-sm text-zinc-700 leading-relaxed font-normal">
            {aiResponse.answer}
          </p>

          {aiResponse.dataSnippet && (
            <div className="p-3 rounded-2xl bg-zinc-50 border border-zinc-200/80 font-mono text-xs text-zinc-800 flex items-center justify-between gap-2">
              <span className="truncate">{aiResponse.dataSnippet}</span>
              <button
                onClick={() => copySnippet(aiResponse.dataSnippet!)}
                className="p-1.5 rounded-xl bg-white border border-zinc-200 text-zinc-600 hover:text-zinc-950 shadow-2xs transition-colors shrink-0"
                title="Copy snippet"
              >
                {copied ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Copy className="w-3.5 h-3.5" />}
              </button>
            </div>
          )}

          <div className="pt-2 flex items-center justify-between">
            <Link
              href={`/tools/${aiResponse.actionToolSlug}`}
              className="text-xs font-semibold text-zinc-950 hover:text-zinc-700 inline-flex items-center gap-1 transition-colors"
            >
              <span>{aiResponse.actionToolLabel}</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </Link>

            <button
              onClick={() => setAiResponse(null)}
              className="text-[11px] text-zinc-400 hover:text-zinc-600"
            >
              Dismiss
            </button>
          </div>
        </div>
      )}

    </div>
  );
}
