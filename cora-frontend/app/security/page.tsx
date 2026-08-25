import React from 'react';
import Link from 'next/link';
import { ShieldCheck, Lock, Key, Server, Cpu, FileCheck, ArrowLeft, ArrowRight, CheckCircle2, Terminal } from 'lucide-react';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Security & Compliance | Cora Platform',
  description: 'Enterprise-grade encryption, SOC-2 readiness, SHA-256 e-signatures, and Indian IT Act 2000 compliance across all professional services.',
};

export default function SecurityPage() {
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
            <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" />
            <span>SECURITY ARCHITECTURE &amp; COMPLIANCE</span>
          </div>
          <h1 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 mb-4">
            Enterprise Security Standards
          </h1>
          <p className="text-zinc-600 text-sm sm:text-base leading-relaxed">
            How Cora protects professional business records, contract vaults, client documents, and financial ledgers with military-grade encryption.
          </p>
        </div>

        {/* Security Grid Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-12">
          
          <div className="p-6 rounded-2xl bg-white border border-zinc-200/90 shadow-sm space-y-2.5">
            <div className="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/60">
              <Lock className="w-4 h-4" />
            </div>
            <h3 className="text-sm font-bold text-zinc-950">AES-256 &amp; TLS 1.3 Encryption</h3>
            <p className="text-xs text-zinc-600 leading-relaxed font-normal">
              Every business record, client document, asset deliverable, and PDF invoice is encrypted using 256-bit Advanced Encryption Standard at rest and enforced via TLS 1.3 in transit.
            </p>
          </div>

          <div className="p-6 rounded-2xl bg-white border border-zinc-200/90 shadow-sm space-y-2.5">
            <div className="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-200/60">
              <FileCheck className="w-4 h-4" />
            </div>
            <h3 className="text-sm font-bold text-zinc-950">SHA-256 Cryptographic E-Signs</h3>
            <p className="text-xs text-zinc-600 leading-relaxed font-normal">
              Client signatures generate immutable cryptographic hash stamps with signer IP, timestamp, and device fingerprint compliant with the Indian IT Act 2000.
            </p>
          </div>

          <div className="p-6 rounded-2xl bg-white border border-zinc-200/90 shadow-sm space-y-2.5">
            <div className="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-200/60">
              <Cpu className="w-4 h-4" />
            </div>
            <h3 className="text-sm font-bold text-zinc-950">Zero AI Training Guarantee</h3>
            <p className="text-xs text-zinc-600 leading-relaxed font-normal">
              Your business proposals, financial ledgers, and client records are zero-retention processed. We never use proprietary tenant data to train base models.
            </p>
          </div>

          <div className="p-6 rounded-2xl bg-white border border-zinc-200/90 shadow-sm space-y-2.5">
            <div className="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/60">
              <Server className="w-4 h-4" />
            </div>
            <h3 className="text-sm font-bold text-zinc-950">100% Indian Data Residency</h3>
            <p className="text-xs text-zinc-600 leading-relaxed font-normal">
              Primary production databases and file storage are hosted in Tier-4 data centers in Mumbai, complying strictly with Indian DPDP Act 2023 guidelines.
            </p>
          </div>

        </div>

        {/* Security Technical Specifications */}
        <div className="space-y-10 text-sm sm:text-base text-zinc-700 leading-relaxed font-normal">
          
          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              1. Multi-Tenant Row-Level Isolation (RLS)
            </h2>
            <p>
              Cora utilizes database-level Row Level Security (RLS). Every query executed by our application layer is bounded by your unique <code className="text-xs bg-zinc-100 px-1.5 py-0.5 rounded text-zinc-900">workspace_id</code>. It is mathematically impossible for one business tenant to view, leak, or mutate records from another organization.
            </p>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              2. Vulnerability Testing &amp; Bug Bounty
            </h2>
            <p>
              We conduct automated static analysis, dynamic AST penetration testing, and annual third-party security audits. If you discover a security vulnerability, please report it responsibly to <a href="mailto:security@heycora.in" className="text-emerald-700 underline font-semibold">security@heycora.in</a> for immediate escalation.
            </p>
          </section>

        </div>

        {/* Bottom CTA */}
        <div className="mt-16 pt-8 border-t border-zinc-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <Link 
            href="/sla" 
            className="text-xs font-semibold text-zinc-700 hover:text-zinc-950 flex items-center gap-1.5"
          >
            <span>Review our 99.95% Service Level Agreement (SLA)</span>
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
