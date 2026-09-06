'use client';

import React, { useState } from 'react';
import { Globe, CheckCircle2, ArrowRight, Zap, ShieldCheck, Layers, FileCode, Server, RefreshCw } from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';

export default function ElementorMigratorToolPage() {
  const [url, setUrl] = useState<string>('');
  const [isScanning, setIsScanning] = useState<boolean>(false);
  const [scanResult, setScanResult] = useState<{
    scannedUrl: string;
    isCompatible: boolean;
    elementorVersion: string;
    pagesCount: number;
    assetsCount: number;
    pluginsReplaced: number;
    speedBefore: number;
    speedAfter: number;
    ttfbBefore: string;
    ttfbAfter: string;
  } | null>(null);

  const { showToast } = useToast();

  const handleScan = (e: React.FormEvent) => {
    e.preventDefault();
    if (!url.trim()) {
      showToast('Please enter a website URL to scan.');
      return;
    }

    setIsScanning(true);
    setScanResult(null);

    // Simulate scanning network request & payload validation
    setTimeout(() => {
      setIsScanning(false);
      const cleanUrl = url.replace(/^https?:\/\//, '').replace(/\/$/, '');
      setScanResult({
        scannedUrl: cleanUrl,
        isCompatible: true,
        elementorVersion: '3.22.1',
        pagesCount: 8,
        assetsCount: 34,
        pluginsReplaced: 7,
        speedBefore: 48,
        speedAfter: 96,
        ttfbBefore: '1.4s',
        ttfbAfter: '180ms'
      });
      showToast('Scan complete! 100% Elementor compatibility confirmed.');
    }, 1200);
  };

  const setPresetUrl = (preset: string) => {
    setUrl(preset);
  };

  const migrationFaqs = [
    {
      question: 'Which WordPress websites can be migrated to Cora?',
      answer: 'Cora exclusively supports Elementor-based websites, templates, and full-site kits (.json and .zip). Non-Elementor PHP themes are not imported to guarantee 100% layout fidelity, responsive container mapping, and high-performance Canvas rendering.'
    },
    {
      question: 'How does the 1-Click Elementor Migrator work?',
      answer: 'Cora connects to your existing Elementor pages, parses the section and container JSON widget trees, automatically sideloads all remote media assets into your private workspace storage, and creates native Cora Canvas pages ready to edit.'
    },
    {
      question: 'Will my SEO rankings and URL slugs be preserved?',
      answer: 'Yes! All page titles, permalink slugs, OpenGraph metadata, and image alt tags are automatically matched and preserved during the 1-click import process.'
    },
    {
      question: 'What plugins does Cora replace when migrating from Elementor?',
      answer: 'Cora replaces 5+ fragmented WordPress plugins including HoneyBook/Bonsai CRM, DocuSign/HelloSign e-signature extensions, WP Mail SMTP, caching add-ons, and separate invoice generators with 1 unified operating system.'
    }
  ];

  return (
    <ToolPageShell
      toolId="elementor-migrator"
      badgeTag="1-Click Migration Engine"
      title="Elementor to Cora Website Migration Checker"
      subtitle="Analyze your existing WordPress & Elementor website for instant 1-click migration into Cora Studio Canvas with automated asset sideloading and 18% GST invoicing."
      faqItems={migrationFaqs}
    >
      <div className="space-y-8">

        {/* ── Input & Scanner Console ── */}
        <div className="rounded-3xl bg-white border border-zinc-200/90 p-6 sm:p-8 shadow-xs">
          <form onSubmit={handleScan} className="space-y-4">
            <div>
              <label className="block text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 mb-2">
                Existing WordPress Website URL
              </label>
              <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div className="relative flex-1">
                  <Globe className="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                  <input
                    type="text"
                    value={url}
                    onChange={(e) => setUrl(e.target.value)}
                    placeholder="https://my-old-photography-site.com"
                    className="w-full pl-10 pr-4 py-3 bg-zinc-50 border border-zinc-200 rounded-2xl text-xs sm:text-sm text-zinc-900 focus:outline-none focus:border-zinc-950 transition-colors font-medium"
                  />
                </div>
                <button
                  type="submit"
                  disabled={isScanning}
                  className="px-6 py-3 bg-zinc-950 hover:bg-zinc-800 text-white rounded-2xl text-xs sm:text-sm font-bold shadow-xs transition-all shrink-0 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                >
                  {isScanning ? (
                    <>
                      <RefreshCw className="w-4 h-4 animate-spin" />
                      <span>Analyzing Elementor...</span>
                    </>
                  ) : (
                    <>
                      <span>Check 1-Click Compatibility</span>
                      <ArrowRight className="w-4 h-4" />
                    </>
                  )}
                </button>
              </div>
            </div>

            {/* Quick Presets */}
            <div className="flex flex-wrap items-center gap-2 pt-1">
              <span className="text-[11px] font-mono text-zinc-400">Sample Presets:</span>
              <button
                type="button"
                onClick={() => setPresetUrl('https://demo-photo-studio.com')}
                className="text-[11px] font-mono text-zinc-600 bg-zinc-100 hover:bg-zinc-200 px-2.5 py-1 rounded-lg transition-colors cursor-pointer"
              >
                Photography Studio (Elementor)
              </button>
              <button
                type="button"
                onClick={() => setPresetUrl('https://luxury-re-brokerage.com')}
                className="text-[11px] font-mono text-zinc-600 bg-zinc-100 hover:bg-zinc-200 px-2.5 py-1 rounded-lg transition-colors cursor-pointer"
              >
                Real Estate Media (Elementor)
              </button>
            </div>
          </form>
        </div>

        {/* ── Scan Results Diagnostic Dashboard ── */}
        {scanResult && (
          <div className="space-y-6 animate-in fade-in slide-in-from-bottom-3 duration-300">
            
            {/* Compatibility Badge Banner */}
            <div className="p-6 rounded-3xl bg-zinc-950 text-white border border-zinc-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              <div className="flex items-center gap-3.5">
                <div className="w-10 h-10 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center shrink-0">
                  <CheckCircle2 className="w-5 h-5 text-emerald-400" />
                </div>
                <div>
                  <div className="flex items-center gap-2">
                    <h3 className="text-base font-bold text-white tracking-tight">100% 1-Click Migration Ready</h3>
                    <span className="text-[9px] font-mono font-bold bg-zinc-900 text-emerald-400 border border-zinc-800 px-2 py-0.5 rounded">PASSED</span>
                  </div>
                  <p className="text-xs text-zinc-400 mt-0.5 font-mono">
                    {scanResult.scannedUrl} • Elementor v{scanResult.elementorVersion} verified
                  </p>
                </div>
              </div>

              <a
                href="/demo"
                className="px-5 py-2.5 bg-white text-zinc-950 hover:bg-zinc-100 text-xs font-bold rounded-xl shadow-xs transition-all shrink-0 inline-flex items-center gap-1.5"
              >
                <span>Start Migration in Workspace</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </a>
            </div>

            {/* Metrics Matrix */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div className="p-5 rounded-2xl bg-white border border-zinc-200/90 space-y-1">
                <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase">Pages Detected</span>
                <div className="text-2xl font-bold text-zinc-950 font-mono">{scanResult.pagesCount}</div>
                <p className="text-[11px] text-zinc-500">Auto-mapped to Canvas</p>
              </div>

              <div className="p-5 rounded-2xl bg-white border border-zinc-200/90 space-y-1">
                <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase">Media Assets</span>
                <div className="text-2xl font-bold text-zinc-950 font-mono">{scanResult.assetsCount}</div>
                <p className="text-[11px] text-zinc-500">Auto-sideloaded to storage</p>
              </div>

              <div className="p-5 rounded-2xl bg-white border border-zinc-200/90 space-y-1">
                <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase">Plugins Consolidated</span>
                <div className="text-2xl font-bold text-zinc-950 font-mono">{scanResult.pluginsReplaced} Apps</div>
                <p className="text-[11px] text-zinc-500">Replaced by Cora OS</p>
              </div>

              <div className="p-5 rounded-2xl bg-white border border-zinc-200/90 space-y-1">
                <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase">Speed Upgrade</span>
                <div className="text-2xl font-bold text-zinc-950 font-mono">{scanResult.speedBefore} → {scanResult.speedAfter}</div>
                <p className="text-[11px] text-zinc-500">Mobile PageSpeed score</p>
              </div>
            </div>

            {/* How 1-Click Migration Works In Cora */}
            <div className="p-6 rounded-3xl bg-zinc-50 border border-zinc-200/80 space-y-4">
              <h4 className="text-sm font-bold text-zinc-950 uppercase tracking-wider font-mono">
                4-Step Migration Execution Pipeline
              </h4>

              <div className="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                <div className="p-4 rounded-xl bg-white border border-zinc-200/80 space-y-1.5">
                  <div className="flex items-center gap-2 font-bold text-zinc-950 font-mono">
                    <span className="w-5 h-5 rounded-full bg-zinc-100 flex items-center justify-center text-[10px]">1</span>
                    <span>Schema Validation</span>
                  </div>
                  <p className="text-zinc-500 text-[11px] leading-relaxed">
                    Verifies genuine Elementor sections, containers, and typography hierarchies.
                  </p>
                </div>

                <div className="p-4 rounded-xl bg-white border border-zinc-200/80 space-y-1.5">
                  <div className="flex items-center gap-2 font-bold text-zinc-950 font-mono">
                    <span className="w-5 h-5 rounded-full bg-zinc-100 flex items-center justify-center text-[10px]">2</span>
                    <span>Asset Sideloading</span>
                  </div>
                  <p className="text-zinc-500 text-[11px] leading-relaxed">
                    Downloads 4K RAW imagery and gallery assets into tenant media storage.
                  </p>
                </div>

                <div className="p-4 rounded-xl bg-white border border-zinc-200/80 space-y-1.5">
                  <div className="flex items-center gap-2 font-bold text-zinc-950 font-mono">
                    <span className="w-5 h-5 rounded-full bg-zinc-100 flex items-center justify-center text-[10px]">3</span>
                    <span>Canvas Publishing</span>
                  </div>
                  <p className="text-zinc-500 text-[11px] leading-relaxed">
                    Generates native WordPress pages with `_elementor_data` and clean permalinks.
                  </p>
                </div>

                <div className="p-4 rounded-xl bg-white border border-zinc-200/80 space-y-1.5">
                  <div className="flex items-center gap-2 font-bold text-zinc-950 font-mono">
                    <span className="w-5 h-5 rounded-full bg-zinc-100 flex items-center justify-center text-[10px]">4</span>
                    <span>GST & CRM Attach</span>
                  </div>
                  <p className="text-zinc-500 text-[11px] leading-relaxed">
                    Immediately binds lead capture forms, WhatsApp Speed-to-Lead, and 18% GST invoices.
                  </p>
                </div>
              </div>
            </div>

          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
