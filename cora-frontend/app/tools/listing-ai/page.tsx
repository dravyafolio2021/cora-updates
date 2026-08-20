'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { ArrowLeft, Sparkles, Copy, Check, ArrowRight, Building2, Camera } from 'lucide-react';
import { useToast } from '@/components/ui/Toast';

export default function ListingAiGeneratorPage() {
  const [industry, setIndustry] = useState<'real_estate' | 'studio'>('real_estate');
  const [title, setTitle] = useState<string>('Luxury 3BHK Penthouse in Worli, Mumbai');
  const [highlights, setHighlights] = useState<string>('Sea view, Italian marble flooring, 2 covered car parks, private terrace, 24/7 security');
  const [tone, setTone] = useState<'luxury' | 'urgent' | 'minimalist'>('luxury');
  const [generatedOutput, setGeneratedOutput] = useState<string>('');
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

  return (
    <div className="py-12 md:py-20">
      <div className="w-full max-w-[960px] mx-auto px-6">
        
        {/* Back link */}
        <Link
          href="/tools"
          className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 mb-8 transition-colors"
        >
          <ArrowLeft className="w-4 h-4" /> Back to all micro-tools
        </Link>

        {/* Header */}
        <div className="mb-10">
          <div className="inline-flex items-center gap-1.5 font-sans text-xs font-bold text-purple-900 px-3 py-1 bg-purple-100 rounded-full mb-3">
            <Sparkles className="w-3.5 h-3.5" />
            <span>AI Copywriter</span>
          </div>
          <h1 className="font-display text-3xl md:text-4xl font-bold tracking-tight text-zinc-950 mb-3">
            Real Estate & Studio Listing AI Generator
          </h1>
          <p className="text-zinc-600 text-sm md:text-base leading-relaxed">
            Generate high-converting property listings, Instagram captions, and commercial shoot briefs in seconds.
          </p>
        </div>

        {/* Tool Workspace */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-8 mb-12">
          
          {/* Left Form */}
          <div className="md:col-span-6 bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm space-y-5">
            
            {/* Category Select */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Industry Category
              </label>
              <div className="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  onClick={() => setIndustry('real_estate')}
                  className={`inline-flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl text-xs font-semibold border transition-all ${
                    industry === 'real_estate'
                      ? 'bg-zinc-950 text-white border-zinc-950'
                      : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                  }`}
                >
                  <Building2 className="w-4 h-4" /> Real Estate
                </button>
                <button
                  type="button"
                  onClick={() => setIndustry('studio')}
                  className={`inline-flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl text-xs font-semibold border transition-all ${
                    industry === 'studio'
                      ? 'bg-zinc-950 text-white border-zinc-950'
                      : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                  }`}
                >
                  <Camera className="w-4 h-4" /> Photo Studio
                </button>
              </div>
            </div>

            {/* Title / Property Name */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Listing Headline / Title
              </label>
              <input
                type="text"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-sm text-zinc-950 focus:outline-none focus:border-zinc-950 transition-colors"
                placeholder="e.g. 4BHK Sky Villa with Infinity Pool"
              />
            </div>

            {/* Key Amenities / Features */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Key Features (comma separated)
              </label>
              <textarea
                rows={3}
                value={highlights}
                onChange={(e) => setHighlights(e.target.value)}
                className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-sm text-zinc-950 focus:outline-none focus:border-zinc-950 transition-colors"
                placeholder="Sea view, private terrace, 2 car parks..."
              />
            </div>

            {/* Tone Selector */}
            <div>
              <label className="block text-xs font-bold uppercase tracking-wider text-zinc-600 mb-2">
                Copywriting Tone
              </label>
              <div className="grid grid-cols-3 gap-2">
                {(['luxury', 'urgent', 'minimalist'] as const).map((t) => (
                  <button
                    key={t}
                    type="button"
                    onClick={() => setTone(t)}
                    className={`py-2 px-2.5 rounded-xl text-xs capitalize font-semibold border transition-all ${
                      tone === t
                        ? 'bg-zinc-950 text-white border-zinc-950'
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
              className="w-full inline-flex items-center justify-center gap-2 bg-zinc-950 text-white font-semibold py-3 px-4 rounded-xl text-xs hover:bg-zinc-800 transition-colors shadow-sm"
            >
              <Sparkles className="w-4 h-4 text-purple-400" />
              <span>Generate AI Description</span>
            </button>
          </div>

          {/* Right Output Box */}
          <div className="md:col-span-6 bg-zinc-950 text-white rounded-2xl p-6 flex flex-col justify-between shadow-xl">
            <div>
              <div className="flex items-center justify-between pb-4 border-b border-zinc-800 mb-4">
                <span className="text-xs font-bold uppercase tracking-wider text-zinc-400">Generated Listing</span>
                <span className="text-xs font-mono bg-zinc-800 text-zinc-300 px-2 py-0.5 rounded capitalize">
                  {tone}
                </span>
              </div>

              {generatedOutput ? (
                <div className="whitespace-pre-line font-sans text-xs md:text-sm text-zinc-200 leading-relaxed font-normal p-3 bg-zinc-900 rounded-xl max-h-[300px] overflow-y-auto">
                  {generatedOutput}
                </div>
              ) : (
                <div className="py-16 text-center text-zinc-500 text-xs">
                  Click "Generate AI Description" to create your listing copy.
                </div>
              )}
            </div>

            {generatedOutput && (
              <button
                type="button"
                onClick={copyOutput}
                className="mt-6 w-full inline-flex items-center justify-center gap-2 bg-white text-zinc-950 font-semibold py-2.5 px-4 rounded-xl text-xs hover:bg-zinc-100 transition-colors"
              >
                {copied ? <Check className="w-4 h-4 text-emerald-600" /> : <Copy className="w-4 h-4" />}
                <span>{copied ? 'Copied to Clipboard!' : 'Copy to Clipboard'}</span>
              </button>
            )}
          </div>

        </div>

        {/* Lead Capture Banner */}
        <div className="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-2xl p-6 md:p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
          <div>
            <h3 className="font-display text-lg font-bold text-zinc-950 mb-1">Scale Your Agency with Cora Workspaces</h3>
            <p className="text-xs md:text-sm text-zinc-600">
              Automate multi-model AI listing generation, client contracts, GST tax calculations, and lead CRM in one dashboard.
            </p>
          </div>
          <a
            href="https://app.heycora.in/workspace"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white text-xs font-semibold px-5 py-3 rounded-xl hover:bg-zinc-800 transition-all shrink-0"
          >
            <span>Get Started Free</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </a>
        </div>

      </div>
    </div>
  );
}
