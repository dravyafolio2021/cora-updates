'use client';

import React, { useState } from 'react';
import { 
  Copy, 
  Check, 
  Code, 
  Sparkles, 
  Receipt, 
  FileText, 
  Calendar, 
  Zap,
  Globe
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';

export default function EmbedBuilderPage() {
  const [platform, setPlatform] = useState<'framer' | 'webflow' | 'wordpress' | 'shopify' | 'html'>('framer');
  const [widgetType, setWidgetType] = useState<'ai_copilot' | 'gst_calc' | 'booking_slots' | 'esign_vault'>('ai_copilot');
  const [workspaceId, setWorkspaceId] = useState('cora_live_demo');
  const [accentColor, setAccentColor] = useState('#09090b');
  const [copied, setCopied] = useState(false);
  const { showToast } = useToast();

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
    showToast('Embed code snippet copied to clipboard!');
    setTimeout(() => setCopied(false), 2000);
  };

  const embedFaqs = [
    {
      question: 'How do I add these widgets to Framer or Webflow?',
      answer: 'In Framer, drop an Embed component onto your canvas and paste the snippet. In Webflow, add an HTML Embed element and paste the code.'
    },
    {
      question: 'Does the embed affect my website loading speed or SEO?',
      answer: 'No. All scripts load asynchronously via global Cloudflare Edge CDN with total payload size under 14KB, ensuring zero Google Core Web Vitals degradation.'
    }
  ];

  return (
    <ToolPageShell
      toolId="embed-builder"
      badgeTag="⚡ Zero-Code Developer Engine"
      title="1-Click Website Embed & Widget Builder"
      subtitle="Generate clean, high-performance embed snippets for Framer, Webflow, WordPress, or Shopify in seconds with direct CRM sync."
      faqItems={embedFaqs}
    >
      {/* ── 70% Tool Workspace (Interactive Form + Code Output) ── */}
      <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
        
        {/* Left Console (5 Cols) */}
        <div className="md:col-span-5 bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs space-y-4">
          
          {/* Target CMS Platform */}
          <div>
            <label className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono block mb-2">
              1. Target Platform
            </label>
            <div className="grid grid-cols-3 gap-1.5">
              {[
                { id: 'framer', label: 'Framer' },
                { id: 'webflow', label: 'Webflow' },
                { id: 'wordpress', label: 'WordPress' },
                { id: 'shopify', label: 'Shopify' },
                { id: 'html', label: 'Custom HTML' },
              ].map((p) => (
                <button
                  key={p.id}
                  type="button"
                  onClick={() => setPlatform(p.id as any)}
                  className={`py-2 px-2 rounded-xl text-xs font-bold border transition-all cursor-pointer ${
                    platform === p.id
                      ? 'bg-zinc-950 text-white border-zinc-950 shadow-xs'
                      : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                  }`}
                >
                  {p.label}
                </button>
              ))}
            </div>
          </div>

          {/* Widget Type Selection */}
          <div>
            <label className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono block mb-2">
              2. Widget Component
            </label>
            <div className="space-y-1.5">
              {[
                {
                  id: 'ai_copilot',
                  title: 'AI Co-Founder Chatbot',
                  desc: '24/7 lead intake on WhatsApp',
                  icon: Sparkles,
                },
                {
                  id: 'gst_calc',
                  title: '18% GST Invoice Calculator',
                  desc: 'Live reverse tax & SAC math',
                  icon: Receipt,
                },
                {
                  id: 'booking_slots',
                  title: 'Shoot Booking & Calendar',
                  desc: 'Time slot picker & intake',
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
                    className={`w-full p-2.5 rounded-xl text-left border flex items-start gap-2.5 transition-all cursor-pointer ${
                      widgetType === w.id
                        ? 'bg-zinc-950 text-white border-zinc-950 shadow-xs'
                        : 'bg-zinc-50 text-zinc-700 border-zinc-200 hover:bg-zinc-100'
                    }`}
                  >
                    <Icon className={`w-3.5 h-3.5 mt-0.5 shrink-0 ${widgetType === w.id ? 'text-indigo-400' : 'text-zinc-500'}`} />
                    <div>
                      <div className="text-xs font-bold">{w.title}</div>
                      <div className={`text-[10.5px] ${widgetType === w.id ? 'text-zinc-400' : 'text-zinc-500'}`}>{w.desc}</div>
                    </div>
                  </button>
                );
              })}
            </div>
          </div>

          {/* Workspace ID Input */}
          <div className="pt-2 border-t border-zinc-100">
            <label className="text-xs font-bold text-zinc-800 block mb-1">Workspace ID / Slug</label>
            <input
              type="text"
              value={workspaceId}
              onChange={(e) => setWorkspaceId(e.target.value)}
              className="w-full px-3 py-2 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
              placeholder="e.g. cora_studio_delhi"
            />
          </div>

        </div>

        {/* Right Output Code Box (7 Cols) */}
        <div className="md:col-span-7 bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs flex flex-col justify-between space-y-4">
          <div>
            <div className="flex items-center justify-between pb-3 border-b border-zinc-200 text-xs font-mono">
              <span className="text-zinc-500 font-semibold uppercase tracking-wider">
                {platform.toUpperCase()} Embed Code
              </span>
              <span className="text-[10.5px] font-mono text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full font-bold">
                Async &lt; 14KB CDN
              </span>
            </div>

            <pre className="p-4 bg-zinc-950 text-emerald-400 rounded-2xl font-mono text-xs overflow-x-auto border border-zinc-800 leading-relaxed max-h-[350px] mt-3 select-all">
              <code>{getCodeSnippet()}</code>
            </pre>
          </div>

          <button
            type="button"
            onClick={handleCopy}
            className="w-full py-3 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs"
          >
            {copied ? <Check className="w-4 h-4 text-emerald-400" /> : <Copy className="w-4 h-4 text-zinc-400" />}
            <span>{copied ? 'Code Copied!' : 'Copy Embed Code Snippet'}</span>
          </button>
        </div>

      </div>
    </ToolPageShell>
  );
}
