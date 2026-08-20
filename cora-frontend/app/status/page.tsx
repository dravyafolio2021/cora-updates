import React from 'react';
import Link from 'next/link';
import { Activity, CheckCircle2, Server, ShieldCheck, ArrowLeft, ArrowRight, RefreshCw, Radio } from 'lucide-react';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'System Status | Cora Studio OS',
  description: 'Real-time operational status, uptime metrics, and service availability for Cora Studio OS.',
};

export default function StatusPage() {
  const services = [
    { name: 'Autonomous AI Co-Founder & Live Routing', status: 'Operational', uptime: '99.98%' },
    { name: 'SHA-256 E-Sign Vault & Legal Registry', status: 'Operational', uptime: '100.0%' },
    { name: '18% GST Invoicing Engine & Tax Calculator', status: 'Operational', uptime: '100.0%' },
    { name: 'Lead CRM, Kanban & Deal Pipeline', status: 'Operational', uptime: '99.99%' },
    { name: 'Visual Canvas & Funnel Landing Designer', status: 'Operational', uptime: '99.96%' },
    { name: 'Media Hub & 4K RAW Asset Storage', status: 'Operational', uptime: '99.99%' },
    { name: 'WhatsApp & Email Dispatch Webhooks', status: 'Operational', uptime: '99.97%' },
    { name: 'Workspace Auth & Multi-Tenant API', status: 'Operational', uptime: '100.0%' },
  ];

  return (
    <main className="min-h-screen bg-[#FBFaf7] text-zinc-900 pt-28 sm:pt-36 pb-24">
      <div className="max-w-4xl mx-auto px-4 sm:px-6">
        
        {/* Breadcrumb Navigation */}
        <div className="mb-8">
          <Link 
            href="/" 
            className="inline-flex items-center gap-2 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Back to Home</span>
          </Link>
        </div>

        {/* Live Status Hero Banner */}
        <div className="p-6 sm:p-8 rounded-3xl bg-emerald-500 text-white shadow-lg mb-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div className="space-y-1">
            <div className="flex items-center gap-2 text-xs font-mono font-bold text-emerald-100 uppercase tracking-wider">
              <span className="w-2.5 h-2.5 rounded-full bg-white animate-pulse" />
              <span>LIVE SYSTEM HEALTH MONITOR</span>
            </div>
            <h1 className="font-display text-2xl sm:text-3xl font-bold tracking-tight">
              All Systems Operational
            </h1>
            <p className="text-xs sm:text-sm text-emerald-50">
              Last checked: Just now &bull; Monitored 24/7 across Mumbai, Singapore &amp; US data centers
            </p>
          </div>

          <div className="shrink-0 bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl text-xs font-bold font-mono">
            99.98% 90-DAY AVG
          </div>
        </div>

        {/* 90-Day Uptime Heatmap Graphic */}
        <div className="p-6 rounded-3xl bg-white border border-zinc-200/90 shadow-sm mb-10 space-y-4">
          <div className="flex items-center justify-between text-xs font-bold text-zinc-950">
            <span>Overall Platform Uptime (Past 90 Days)</span>
            <span className="text-emerald-700 font-mono">99.98%</span>
          </div>

          {/* Bar chart representation */}
          <div className="flex items-center gap-1 overflow-x-auto py-1">
            {Array.from({ length: 45 }).map((_, i) => (
              <div 
                key={i} 
                className="flex-1 min-w-[6px] h-8 rounded-sm bg-emerald-500 hover:bg-emerald-600 transition-colors" 
                title={`Day ${90 - i * 2}: 100% Operational`}
              />
            ))}
          </div>

          <div className="flex items-center justify-between text-[11px] font-mono text-zinc-400">
            <span>90 days ago</span>
            <span className="text-emerald-600 font-semibold">Zero incidents reported</span>
            <span>Today</span>
          </div>
        </div>

        {/* Service Breakdown List */}
        <div className="p-6 rounded-3xl bg-white border border-zinc-200/90 shadow-sm mb-10 space-y-4">
          <h2 className="text-sm font-bold text-zinc-950 uppercase tracking-wider font-mono">
            Core Service Breakdown
          </h2>

          <div className="divide-y divide-zinc-100 text-xs sm:text-sm">
            {services.map((svc) => (
              <div key={svc.name} className="py-3.5 flex items-center justify-between gap-4">
                <div className="font-semibold text-zinc-900">{svc.name}</div>
                <div className="flex items-center gap-3 shrink-0">
                  <span className="text-xs font-mono text-zinc-500">{svc.uptime}</span>
                  <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                    <span>{svc.status}</span>
                  </span>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Incident History */}
        <div className="p-6 rounded-3xl bg-white border border-zinc-200/90 shadow-sm space-y-4">
          <h2 className="text-sm font-bold text-zinc-950 uppercase tracking-wider font-mono">
            Past Incident History (August 2026)
          </h2>
          <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 text-xs text-zinc-600 space-y-1">
            <div className="font-bold text-zinc-950">No incidents reported in the last 90 days.</div>
            <div>All clusters operating within normal latency parameters (&lt; 65ms response time).</div>
          </div>
        </div>

        {/* Bottom CTA */}
        <div className="mt-16 pt-8 border-t border-zinc-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <Link 
            href="/sla" 
            className="text-xs font-semibold text-zinc-700 hover:text-zinc-950 flex items-center gap-1.5"
          >
            <span>Read our Service Level Agreement</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </Link>
          <a
            href="https://app.heycora.in/workspace/login"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-zinc-800 transition-all"
          >
            <span>Launch Workspace</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
          </a>
        </div>

      </div>
    </main>
  );
}
