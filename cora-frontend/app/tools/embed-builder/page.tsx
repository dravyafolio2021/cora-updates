'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { 
  Copy, 
  Check, 
  Code, 
  Sparkles, 
  Receipt, 
  FileText, 
  Calendar, 
  ArrowRight, 
  Layers, 
  ExternalLink,
  Zap,
  Globe
} from 'lucide-react';

export default function EmbedBuilderPage() {
  const [platform, setPlatform] = useState<'framer' | 'webflow' | 'wordpress' | 'shopify' | 'html'>('framer');
  const [widgetType, setWidgetType] = useState<'ai_copilot' | 'gst_calc' | 'booking_slots' | 'esign_vault'>('ai_copilot');
  const [workspaceId, setWorkspaceId] = useState('cora_live_demo');
  const [accentColor, setAccentColor] = useState('#09090b');
  const [copied, setCopied] = useState(false);

  const getCodeSnippet = () => {
    if (widgetType === 'ai_copilot') {
      return `<!-- Cora Autonomous AI Co-Founder Widget for ${platform.toUpperCase()} -->
<script 
  src="https://app.heycora.in/embed/copilot.js" 
  data-workspace="${workspaceId}" 
  data-color="${accentColor}" 
  data-position="bottom-right" 
  async>
</script>`;
    }
    if (widgetType === 'gst_calc') {
      return `<!-- Cora 18% GST Calculator Card Embed for ${platform.toUpperCase()} -->
<iframe 
  src="https://heycora.in/tools/gst-calculator?embed=true&workspace=${workspaceId}&color=${encodeURIComponent(accentColor)}" 
  width="100%" 
  height="540" 
  style="border:none;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.06);"
  title="18% GST Invoicing Calculator">
</iframe>`;
    }
    if (widgetType === 'booking_slots') {
      return `<!-- Cora Shoot Booking & Call-Time Slot Picker for ${platform.toUpperCase()} -->
<iframe 
  src="https://app.heycora.in/embed/booking?workspace=${workspaceId}&accent=${encodeURIComponent(accentColor)}" 
  width="100%" 
  height="620" 
  style="border:none;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.06);"
  title="Shoot Booking & Call-Time Slot Picker">
</iframe>`;
    }
    return `<!-- Cora SHA-256 E-Sign Vault Embed for ${platform.toUpperCase()} -->
<iframe 
  src="https://app.heycora.in/embed/contract-sign?workspace=${workspaceId}&accent=${encodeURIComponent(accentColor)}" 
  width="100%" 
  height="680" 
  style="border:none;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.06);"
  title="SHA-256 E-Sign Vault">
</iframe>`;
  };

  const handleCopy = () => {
    navigator.clipboard.writeText(getCodeSnippet());
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <main className="min-h-screen bg-white text-zinc-950 selection:bg-zinc-950 selection:text-white pt-28 sm:pt-36 pb-24">
      <div className="max-w-5xl mx-auto px-4 sm:px-6">
        
        {/* ── Breadcrumbs ──────────────────────────────────────────────── */}
        <nav className="flex items-center gap-2 text-xs text-zinc-500 font-mono mb-8">
          <Link href="/" className="hover:text-zinc-950 transition-colors">Home</Link>
          <span>/</span>
          <Link href="/integrations" className="hover:text-zinc-950 transition-colors">Integrations</Link>
          <span>/</span>
          <span className="text-zinc-950 font-bold">Interactive Embed Builder</span>
        </nav>

        {/* ── 1. Hero Header ────────────────────────────────────────────── */}
        <div className="text-center space-y-4 max-w-3xl mx-auto">
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 rounded-full text-xs font-semibold text-zinc-800 border border-zinc-200">
            <Code className="w-3.5 h-3.5 text-sky-600" />
            <span>Developer &amp; Designer Embed Engine</span>
          </div>

          <h1 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950">
            1-Click Website Embed Builder
          </h1>

          <p className="text-sm sm:text-base text-zinc-600 leading-relaxed">
            Generate clean, high-performance embed snippets for <strong>Framer</strong>, <strong>Webflow</strong>, <strong>WordPress</strong>, or <strong>Shopify</strong> in seconds.
          </p>
        </div>

        {/* ── 2. Interactive Generator ───────────────────────────────────── */}
        <div className="mt-12 grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          {/* Controls Column (5 cols) */}
          <div className="lg:col-span-5 space-y-6">
            
            {/* Step 1: Select Platform */}
            <div className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-3">
              <span className="text-[11px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                1. SELECT PLATFORM
              </span>
              <div className="grid grid-cols-3 gap-2">
                {[
                  { id: 'framer', label: 'Framer' },
                  { id: 'webflow', label: 'Webflow' },
                  { id: 'wordpress', label: 'WordPress' },
                  { id: 'shopify', label: 'Shopify' },
                  { id: 'html', label: 'HTML/React' },
                ].map((p) => (
                  <button
                    key={p.id}
                    type="button"
                    onClick={() => setPlatform(p.id as any)}
                    className={`py-2 px-2.5 rounded-xl text-xs font-semibold border transition-all ${
                      platform === p.id
                        ? 'bg-zinc-950 text-white border-zinc-950 shadow-xs'
                        : 'bg-white text-zinc-700 border-zinc-200 hover:border-zinc-300'
                    }`}
                  >
                    {p.label}
                  </button>
                ))}
              </div>
            </div>

            {/* Step 2: Select Widget Type */}
            <div className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-3">
              <span className="text-[11px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                2. SELECT WIDGET MODULE
              </span>
              <div className="space-y-2">
                {[
                  {
                    id: 'ai_copilot',
                    title: 'Floating AI Co-Founder Chat',
                    desc: 'Voice-to-scope lead capture & FAQs',
                    icon: Sparkles,
                  },
                  {
                    id: 'gst_calc',
                    title: '18% GST Invoice Calculator',
                    desc: 'Live reverse tax & CGST/SGST math',
                    icon: Receipt,
                  },
                  {
                    id: 'booking_slots',
                    title: 'Shoot Booking & Time Slots',
                    desc: 'Visual calendar & service add-ons',
                    icon: Calendar,
                  },
                  {
                    id: 'esign_vault',
                    title: 'SHA-256 E-Sign Vault',
                    desc: 'Digital touch signature canvas',
                    icon: FileText,
                  },
                ].map((w) => {
                  const Icon = w.icon;
                  return (
                    <button
                      key={w.id}
                      type="button"
                      onClick={() => setWidgetType(w.id as any)}
                      className={`w-full p-3 rounded-xl text-left border flex items-start gap-3 transition-all ${
                        widgetType === w.id
                          ? 'bg-zinc-950 text-white border-zinc-950 shadow-xs'
                          : 'bg-white text-zinc-700 border-zinc-200 hover:border-zinc-300'
                      }`}
                    >
                      <Icon className={`w-4 h-4 mt-0.5 shrink-0 ${widgetType === w.id ? 'text-emerald-400' : 'text-zinc-500'}`} />
                      <div>
                        <div className="text-xs font-bold">{w.title}</div>
                        <div className={`text-[11px] ${widgetType === w.id ? 'text-zinc-400' : 'text-zinc-500'}`}>{w.desc}</div>
                      </div>
                    </button>
                  );
                })}
              </div>
            </div>

            {/* Step 3: Workspace ID Input */}
            <div className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-3">
              <span className="text-[11px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                3. WORKSPACE CONFIGURATION
              </span>
              <div>
                <label className="text-xs font-bold text-zinc-700 block mb-1">
                  Workspace Slug or API Key
                </label>
                <input
                  type="text"
                  value={workspaceId}
                  onChange={(e) => setWorkspaceId(e.target.value)}
                  className="w-full bg-white border border-zinc-200 rounded-xl px-3 py-2 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
                  placeholder="e.g. cora_studio_delhi"
                />
              </div>
            </div>

          </div>

          {/* Preview & Code Column (7 cols) */}
          <div className="lg:col-span-7 space-y-6">
            
            {/* Generated Code Box */}
            <div className="p-6 rounded-2xl bg-zinc-950 text-white space-y-4 shadow-lg border border-zinc-800">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <div className="w-3 h-3 rounded-full bg-red-500" />
                  <div className="w-3 h-3 rounded-full bg-amber-500" />
                  <div className="w-3 h-3 rounded-full bg-emerald-500" />
                  <span className="text-xs font-mono text-zinc-400 ml-2">
                    {platform.toUpperCase()} EMBED CODE
                  </span>
                </div>

                <button
                  type="button"
                  onClick={handleCopy}
                  className="inline-flex items-center gap-1.5 bg-zinc-800 hover:bg-zinc-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                >
                  {copied ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5" />}
                  <span>{copied ? 'Copied!' : 'Copy Code'}</span>
                </button>
              </div>

              <pre className="p-4 bg-zinc-900/90 rounded-xl font-mono text-xs text-emerald-400 overflow-x-auto border border-zinc-800 leading-relaxed">
                <code>{getCodeSnippet()}</code>
              </pre>

              <div className="text-[11px] text-zinc-400 flex items-center gap-2 pt-2 border-t border-zinc-800/80">
                <Zap className="w-3.5 h-3.5 text-amber-400 shrink-0" />
                <span>Ultra-lightweight (&lt; 14KB), zero render-blocking, global Cloudflare Edge CDN.</span>
              </div>
            </div>

            {/* Live Interactive Preview Simulation */}
            <div className="p-6 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-4">
              <div className="flex items-center justify-between">
                <span className="text-xs font-mono font-bold text-zinc-700 uppercase tracking-wider">
                  LIVE SIMULATION PREVIEW
                </span>
                <span className="text-[10px] font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                  READY FOR {platform.toUpperCase()}
                </span>
              </div>

              <div className="p-5 bg-white rounded-xl border border-zinc-200 space-y-4 shadow-2xs">
                {widgetType === 'ai_copilot' && (
                  <div className="flex items-center justify-between p-3 rounded-xl bg-zinc-50 border border-zinc-100">
                    <div className="flex items-center gap-3">
                      <div className="w-9 h-9 rounded-lg bg-zinc-950 text-white flex items-center justify-center">
                        <Sparkles className="w-4 h-4 text-emerald-400" />
                      </div>
                      <div>
                        <div className="text-xs font-bold text-zinc-950">AI Studio Co-Founder</div>
                        <div className="text-[11px] text-zinc-500">“Ready to quote your next commercial shoot...”</div>
                      </div>
                    </div>
                    <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping" />
                  </div>
                )}

                {widgetType === 'gst_calc' && (
                  <div className="space-y-2 text-xs">
                    <div className="flex justify-between p-2 bg-zinc-50 rounded-lg">
                      <span className="text-zinc-600">Base Production Fee:</span>
                      <span className="font-bold text-zinc-950">₹50,000</span>
                    </div>
                    <div className="flex justify-between p-2 bg-zinc-50 rounded-lg">
                      <span className="text-zinc-600">18% GST (CGST 9% + SGST 9%):</span>
                      <span className="font-bold text-emerald-700">+ ₹9,000</span>
                    </div>
                    <div className="flex justify-between p-2.5 bg-zinc-950 text-white rounded-lg font-bold">
                      <span>Total Invoice Amount:</span>
                      <span>₹59,000</span>
                    </div>
                  </div>
                )}

                {widgetType === 'booking_slots' && (
                  <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100 text-center space-y-2">
                    <div className="text-xs font-bold text-zinc-950">Commercial Studio Floor Booking</div>
                    <div className="text-[11px] text-zinc-500">Available Slot: Tomorrow 10:00 AM - 6:00 PM (8 hrs)</div>
                    <button className="w-full py-2 bg-zinc-950 text-white rounded-lg text-xs font-bold">
                      Reserve Shoot Time Slot
                    </button>
                  </div>
                )}

                {widgetType === 'esign_vault' && (
                  <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100 space-y-2">
                    <div className="text-xs font-bold text-zinc-950">Production Agreement #CRA-2026-89</div>
                    <div className="h-16 bg-white border border-dashed border-zinc-300 rounded-lg flex items-center justify-center text-zinc-400 font-mono text-[11px]">
                      [ Sign Here with Touch / Stylus ]
                    </div>
                    <div className="text-[10px] font-mono text-zinc-400">SHA-256: 7f83b1657ff1fc53b92dc18148a1...</div>
                  </div>
                )}
              </div>
            </div>

          </div>

        </div>

      </div>
    </main>
  );
}
