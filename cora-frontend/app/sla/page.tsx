import React from 'react';
import Link from 'next/link';
import { Activity, Clock, ShieldCheck, CheckCircle2, ArrowLeft, ArrowRight, AlertTriangle } from 'lucide-react';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Service Level Agreement (SLA) | Cora Platform',
  description: 'Our 99.95% uptime commitment, response times, and service credit guarantees for professional business workspaces.',
};

export default function SLAPage() {
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

        {/* Header Title */}
        <div className="mb-12 border-b border-zinc-200/80 pb-8">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-800 border border-sky-200/80 mb-4">
            <Activity className="w-3.5 h-3.5 text-sky-600" />
            <span>ENTERPRISE AVAILABILITY &amp; UPTIME</span>
          </div>
          <h1 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 mb-4">
            Service Level Agreement (SLA)
          </h1>
          <p className="text-zinc-600 text-sm sm:text-base leading-relaxed">
            Effective Date: January 1, 2026 &bull; Guaranteed Monthly Availability Commitment
          </p>
        </div>

        {/* Uptime Commitment Card */}
        <div className="p-6 sm:p-8 rounded-3xl bg-white border border-zinc-200/90 shadow-sm mb-12 flex flex-col sm:flex-row items-center justify-between gap-6">
          <div>
            <div className="text-xs font-mono font-bold text-emerald-700 uppercase tracking-wider mb-1">
              GUARANTEED PRODUCTION COMMITMENT
            </div>
            <div className="font-display text-4xl sm:text-5xl font-bold text-zinc-950">
              99.95%
            </div>
            <p className="text-xs sm:text-sm text-zinc-600 mt-2 max-w-[460px]">
              Cora guarantees at least 99.95% monthly uptime for core workspace services including E-Sign Vaults, CRM Pipelines, and Invoicing Engines across all professional service workspaces.
            </p>
          </div>

          <div className="shrink-0">
            <Link
              href="/status"
              className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80 hover:bg-emerald-100/80 transition-colors shadow-2xs"
            >
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              <span>View Live Status Page</span>
            </Link>
          </div>
        </div>

        {/* SLA Breakdown Table */}
        <div className="space-y-10 text-sm sm:text-base text-zinc-700 leading-relaxed font-normal">
          
          <section className="space-y-4">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              1. Monthly Uptime &amp; Service Credit Schedule
            </h2>
            <p>
              If Cora fails to meet the guaranteed 99.95% monthly uptime commitment, customers on paid plans are eligible for automatic service credits:
            </p>

            <div className="overflow-x-auto rounded-2xl border border-zinc-200 bg-white">
              <table className="w-full text-left text-xs sm:text-sm">
                <thead className="bg-zinc-50 border-b border-zinc-200 text-zinc-900 font-bold">
                  <tr>
                    <th className="p-3.5 sm:p-4">Monthly Uptime Percentage</th>
                    <th className="p-3.5 sm:p-4">Service Credit (% of Monthly Bill)</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-100 text-zinc-700">
                  <tr>
                    <td className="p-3.5 sm:p-4 font-mono">99.0% – 99.94%</td>
                    <td className="p-3.5 sm:p-4 font-semibold text-emerald-700">10% Credit</td>
                  </tr>
                  <tr>
                    <td className="p-3.5 sm:p-4 font-mono">95.0% – 98.99%</td>
                    <td className="p-3.5 sm:p-4 font-semibold text-emerald-700">25% Credit</td>
                  </tr>
                  <tr>
                    <td className="p-3.5 sm:p-4 font-mono">&lt; 95.0%</td>
                    <td className="p-3.5 sm:p-4 font-semibold text-emerald-700">50% Credit</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              2. Support Response Times
            </h2>
            <p>
              We maintain tier-based response metrics for enterprise business operations:
            </p>
            <ul className="list-disc pl-5 space-y-2 text-sm text-zinc-600">
              <li><strong>Critical Incidents (Service Disruption):</strong> &lt; 30 minutes (24/7 dedicated escalation).</li>
              <li><strong>High Priority (Billing / E-Sign Delays):</strong> &lt; 2 hours during standard business hours.</li>
              <li><strong>General Inquiries:</strong> &lt; 6 hours.</li>
            </ul>
          </section>

        </div>

        {/* Bottom CTA */}
        <div className="mt-16 pt-8 border-t border-zinc-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <Link 
            href="/status" 
            className="text-xs font-semibold text-zinc-700 hover:text-zinc-950 flex items-center gap-1.5"
          >
            <span>Check Real-Time Incident Status</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </Link>
          <a
            href="https://app.heycora.in/workspace/login"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-zinc-800 transition-all"
          >
            <span>Open Workspace</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
          </a>
        </div>

      </div>
    </main>
  );
}
