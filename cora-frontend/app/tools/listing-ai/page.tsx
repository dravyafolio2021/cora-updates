'use client';

import React, { useState } from 'react';
import { Sparkles, Copy, Check, Building2, Camera } from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';

export default function ListingAiGeneratorPage() {
  const [industry, setIndustry] = useState<'real_estate' | 'studio'>('real_estate');
  const [title, setTitle] = useState<string>('Luxury 3BHK Penthouse in Worli, Mumbai');
  const [highlights, setHighlights] = useState<string>('Sea view, Italian marble flooring, 2 covered car parks, private terrace, 24/7 security');
  const [tone, setTone] = useState<'luxury' | 'urgent' | 'minimalist'>('luxury');
  const [generatedOutput, setGeneratedOutput] = useState<string>(`🏡 LUXURY 3BHK PENTHOUSE IN WORLI, MUMBAI
━━━━━━━━━━━━━━━━━━━━
✨ Key Property Highlights:
• Sea view
• Italian marble flooring
• 2 covered car parks
• Private terrace
• 24/7 security

📍 Prime Location & Unrivaled Architecture
Experience ultra-luxury living designed for discerning homeowners. This property combines bespoke interior craftsmanship with panoramic views and world-class amenities.

🔑 Private Showings & Exclusive Inquiries:
Schedule an appointment today via Cora Verified Client Portal (https://heycora.in).`);
  const [copied, setCopied] = useState<boolean>(false);
  const { showToast } = useToast();

  const handleGenerate = () => {
    let output = '';
    if (industry === 'real_estate') {
      output = `🏡 ${title.toUpperCase()}
━━━━━━━━━━━━━━━━━━━━
✨ Key Property Highlights:
${highlights.split(',').map((h) => `• ${h.trim()}`).join('\n')}

📍 Prime Location & Unrivaled Architecture
Experience ultra-luxury living designed for discerning homeowners. This property combines bespoke interior craftsmanship with panoramic views and world-class amenities.

🔑 Private Showings & Exclusive Inquiries:
Schedule an appointment today via Cora Verified Client Portal (https://heycora.in).`;
    } else {
      output = `📸 ${title.toUpperCase()} — PRODUCTION BRIEF
━━━━━━━━━━━━━━━━━━━━
✨ Shoot Specs & Studio Features:
${highlights.split(',').map((h) => `• ${h.trim()}`).join('\n')}

🎯 Creative Concept & Deliverables
Crafted for editorial, commercial, and high-fashion brand shoots. Full acoustic insulation, calibrated continuous lighting rigs, and private vanity suites.

⚡ Book Call Time & Reserve Studio Slot:
https://app.heycora.in/workspace`;
    }

    setGeneratedOutput(output);
    showToast('Listing description generated!');
  };

  const copyOutput = () => {
    if (!generatedOutput) return;
    navigator.clipboard.writeText(generatedOutput);
    setCopied(true);
    showToast('Listing copied to clipboard!');
    setTimeout(() => setCopied(false), 2000);
  };

  const listingFaqs = [
    {
      question: 'Can I use these generated descriptions for Instagram & MagicBricks?',
      answer: 'Yes. The output formatting includes clean emojis, structured bullet points, and high-intent call-to-actions ready to paste directly into Instagram captions, WhatsApp brochures, 99acres, MagicBricks, or client pitch decks.'
    },
    {
      question: 'How do tone presets alter the AI output style?',
      answer: 'Luxury emphasizes heritage materials and architectural prestige; Urgent introduces scarcity and limited availability; Minimalist strips adjectives for clean editorial aesthetic.'
    }
  ];

  return (
    <ToolPageShell
      toolId="listing-ai"
      badgeTag="AI Studio Brief Engine"
      title="Real Estate & Studio Listing AI Generator"
      subtitle="Generate high-converting property listings, Instagram captions, and commercial shoot briefs in seconds with zero prompt engineering."
      faqItems={listingFaqs}
    >
      {/* ── 70% Tool Workspace (Interactive Form + Live Output) ── */}
      <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
        
        {/* Left Console (5 Cols) */}
        <div className="md:col-span-5 bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs space-y-4">
          
          {/* Category Select */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono mb-2">
              Industry Category
            </label>
            <div className="grid grid-cols-2 gap-2">
              <button
                type="button"
                onClick={() => {
                  setIndustry('real_estate');
                  setTitle('Luxury 3BHK Penthouse in Worli, Mumbai');
                }}
                className={`inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-semibold border transition-all cursor-pointer ${
                  industry === 'real_estate'
                    ? 'bg-zinc-950 text-white border-zinc-950 shadow-xs'
                    : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                }`}
              >
                <Building2 className="w-3.5 h-3.5" />
                <span>Real Estate</span>
              </button>
              <button
                type="button"
                onClick={() => {
                  setIndustry('studio');
                  setTitle('High-Fashion Editorial Daylight Studio');
                }}
                className={`inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-semibold border transition-all cursor-pointer ${
                  industry === 'studio'
                    ? 'bg-zinc-950 text-white border-zinc-950 shadow-xs'
                    : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                }`}
              >
                <Camera className="w-3.5 h-3.5" />
                <span>Studio Shoot</span>
              </button>
            </div>
          </div>

          {/* Title Input */}
          <div>
            <label className="text-xs font-bold text-zinc-800 block mb-1">Title / Property Name</label>
            <input
              type="text"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              className="w-full px-3 py-2 rounded-xl border border-zinc-200 text-xs font-sans text-zinc-900 focus:outline-none focus:border-zinc-950"
            />
          </div>

          {/* Highlights Textarea */}
          <div>
            <label className="text-xs font-bold text-zinc-800 block mb-1">Key Specs & Amenities</label>
            <textarea
              rows={3}
              value={highlights}
              onChange={(e) => setHighlights(e.target.value)}
              className="w-full px-3 py-2 rounded-xl border border-zinc-200 text-xs font-sans text-zinc-900 focus:outline-none focus:border-zinc-950"
            />
          </div>

          {/* Tone Selector */}
          <div>
            <label className="block text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono mb-1.5">
              Tone Preset
            </label>
            <div className="grid grid-cols-3 gap-2">
              {(['luxury', 'urgent', 'minimalist'] as const).map((t) => (
                <button
                  key={t}
                  type="button"
                  onClick={() => setTone(t)}
                  className={`py-2 px-2 rounded-xl text-xs capitalize font-semibold border transition-all cursor-pointer ${
                    tone === t
                      ? 'bg-zinc-950 text-white border-zinc-950 shadow-xs'
                      : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                  }`}
                >
                  {t}
                </button>
              ))}
            </div>
          </div>

          <button
            type="button"
            onClick={handleGenerate}
            className="w-full inline-flex items-center justify-center gap-2 bg-zinc-950 text-white font-bold py-2.5 px-4 rounded-xl text-xs hover:bg-zinc-800 transition-colors shadow-xs cursor-pointer"
          >
            <Sparkles className="w-3.5 h-3.5 text-amber-400" />
            <span>Regenerate AI Copy</span>
          </button>
        </div>

        {/* Right Output Box (7 Cols) */}
        <div className="md:col-span-7 bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs flex flex-col justify-between space-y-4">
          <div>
            <div className="flex items-center justify-between pb-3 border-b border-zinc-200 text-xs font-mono">
              <span className="text-zinc-500 font-semibold uppercase tracking-wider">
                Generated Listing Copy
              </span>
              <span className="text-[10.5px] font-mono bg-zinc-100 border border-zinc-200 text-zinc-800 px-2 py-0.5 rounded-full capitalize font-bold">
                {tone} tone
              </span>
            </div>

            <div className="whitespace-pre-line font-sans text-xs text-zinc-800 leading-relaxed font-normal p-4 bg-zinc-50 border border-zinc-200/80 rounded-2xl max-h-[350px] overflow-y-auto mt-3 select-all">
              {generatedOutput}
            </div>
          </div>

          <button
            type="button"
            onClick={copyOutput}
            className="w-full py-3 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs"
          >
            {copied ? <Check className="w-4 h-4 text-emerald-400" /> : <Copy className="w-4 h-4 text-zinc-400" />}
            <span>{copied ? 'Copied to Clipboard!' : 'Copy Formatted Brief'}</span>
          </button>
        </div>

      </div>
    </ToolPageShell>
  );
}
