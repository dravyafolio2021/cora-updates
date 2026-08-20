import React from 'react';
import Link from 'next/link';
import { Lock, ShieldCheck, ArrowLeft, ArrowRight, EyeOff, KeyRound, Database, Server } from 'lucide-react';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Privacy Policy | Cora Studio OS',
  description: 'Understand how Cora protects your creative media, client roster, and financial data with bank-grade encryption and zero AI training.',
};

export default function PrivacyPage() {
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
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80 mb-4">
            <Lock className="w-3.5 h-3.5 text-emerald-600" />
            <span>DATA INTEGRITY &amp; PRIVACY</span>
          </div>
          <h1 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 mb-4">
            Privacy Policy
          </h1>
          <p className="text-zinc-600 text-sm sm:text-base leading-relaxed">
            Effective Date: January 1, 2026 &bull; Last Updated: August 20, 2026
          </p>
        </div>

        {/* Highlight Banner: Zero AI Training Guarantee */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-12">
          <div className="p-5 rounded-2xl bg-white border border-zinc-200/90 shadow-sm space-y-2">
            <div className="flex items-center gap-2 text-sm font-bold text-zinc-950">
              <EyeOff className="w-4 h-4 text-emerald-600" />
              <span>Zero AI Model Training</span>
            </div>
            <p className="text-xs text-zinc-600 leading-relaxed font-normal">
              Your shoot briefs, rate cards, and client proposals are <strong>never</strong> used to train public LLMs or proprietary foundation models. Your data remains isolated to your tenant.
            </p>
          </div>

          <div className="p-5 rounded-2xl bg-white border border-zinc-200/90 shadow-sm space-y-2">
            <div className="flex items-center gap-2 text-sm font-bold text-zinc-950">
              <KeyRound className="w-4 h-4 text-sky-600" />
              <span>AES-256 Bank Grade Encryption</span>
            </div>
            <p className="text-xs text-zinc-600 leading-relaxed font-normal">
              All documents, signed contracts, and GST invoices are encrypted with <strong>AES-256</strong> at rest and transmitted strictly over <strong>TLS 1.3</strong> protocols.
            </p>
          </div>
        </div>

        {/* Privacy Clauses */}
        <div className="space-y-10 text-sm sm:text-base text-zinc-700 leading-relaxed font-normal">
          
          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              1. Information We Collect
            </h2>
            <p>
              When you operate your workspace on Cora, we collect only the minimum required data to provide our autonomous operating services:
            </p>
            <ul className="list-disc pl-5 space-y-2 text-sm text-zinc-600">
              <li><strong>Account Credentials:</strong> Name, work email, studio name, and phone number for 2FA authentication.</li>
              <li><strong>Workspace Records:</strong> Leads in CRM pipelines, shoot dates, crew roster assignments, and contract signatories.</li>
              <li><strong>Financial Identifiers:</strong> Business GSTIN, PAN, billing address, and bank payout details for invoice rendering.</li>
              <li><strong>Technical Metadata:</strong> IP addresses, browser user-agents, and audit timestamps for SHA-256 e-signature validity.</li>
            </ul>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              2. How We Use Your Data
            </h2>
            <p>
              Your data is processed strictly to power your day-to-day studio operations:
            </p>
            <ul className="list-disc pl-5 space-y-2 text-sm text-zinc-600">
              <li>Generating automated 18% CGST/SGST/IGST tax invoices and calculation breakdowns.</li>
              <li>Orchestrating autonomous AI workflows (proposals, voice-to-scope, and shoot call sheets).</li>
              <li>Cryptographically sealing legal contracts and recording immutable SHA-256 audit logs.</li>
              <li>Delivering automated WhatsApp and email reminders to your clients and crew.</li>
            </ul>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              3. Data Isolation &amp; Multi-Tenant Architecture
            </h2>
            <p>
              Every studio workspace is logically separated in our multi-tenant database using strict row-level security (RLS). No other studio, operator, or team can ever query, view, or access your files or financial ledgers.
            </p>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              4. Third-Party Sub-Processors
            </h2>
            <p>
              We partner only with verified, enterprise-grade cloud infrastructure providers that maintain SOC-2 and ISO-27001 certifications:
            </p>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
              <div className="p-3 rounded-xl bg-white border border-zinc-200 text-xs">
                <div className="font-bold text-zinc-950">Cloud Hosting</div>
                <div className="text-zinc-500 mt-0.5">AWS &amp; Hostinger Enterprise (Mumbai / Global)</div>
              </div>
              <div className="p-3 rounded-xl bg-white border border-zinc-200 text-xs">
                <div className="font-bold text-zinc-950">AI Orchestration</div>
                <div className="text-zinc-500 mt-0.5">Anthropic Claude &amp; Google Vertex AI APIs</div>
              </div>
              <div className="p-3 rounded-xl bg-white border border-zinc-200 text-xs">
                <div className="font-bold text-zinc-950">Payment Gateways</div>
                <div className="text-zinc-500 mt-0.5">Razorpay &amp; Stripe (PCI-DSS Level 1)</div>
              </div>
            </div>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              5. Global Privacy Rights &amp; Data Deletion
            </h2>
            <p>
              Under the <strong>Indian Digital Personal Data Protection (DPDP) Act 2023</strong>, <strong>GDPR</strong>, and <strong>CCPA</strong>, you have full authority to export your data or request complete account erasure:
            </p>
            <ul className="list-disc pl-5 space-y-2 text-sm text-zinc-600">
              <li><strong>One-Click JSON/CSV Export:</strong> Download your entire lead history, contracts, and invoices anytime.</li>
              <li><strong>Permanent Workspace Erasure:</strong> Upon request, all database records, backups, and storage buckets are permanently shredded within 30 days.</li>
            </ul>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              6. Privacy Officer Contact
            </h2>
            <p>
              For data access requests, DPA agreements, or privacy inquiries, contact our Data Protection Officer directly:
            </p>
            <div className="p-4 rounded-xl bg-white border border-zinc-200 font-mono text-xs text-zinc-800 space-y-1">
              <div>Cora Platforms Inc. &bull; Data Protection Officer</div>
              <div>Email: <a href="mailto:privacy@heycora.in" className="text-emerald-700 underline">privacy@heycora.in</a></div>
              <div>Response Time: Within 24 business hours</div>
            </div>
          </section>

        </div>

        {/* Bottom CTA */}
        <div className="mt-16 pt-8 border-t border-zinc-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <Link 
            href="/security" 
            className="text-xs font-semibold text-zinc-700 hover:text-zinc-950 flex items-center gap-1.5"
          >
            <span>Learn more about our Security Standards</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </Link>
          <a
            href="https://app.heycora.in/workspace/login"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-zinc-800 transition-all"
          >
            <span>Launch Secure Workspace</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
          </a>
        </div>

      </div>
    </main>
  );
}
