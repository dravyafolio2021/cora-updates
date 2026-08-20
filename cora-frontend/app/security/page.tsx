import React from 'react';
import Link from 'next/link';
import { ShieldCheck, Lock, Key, Server, Cpu, FileCheck, ArrowLeft, ArrowRight, CheckCircle2, Terminal } from 'lucide-react';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Security & Compliance | Cora Studio OS',
  description: 'Enterprise-grade encryption, SOC-2 readiness, SHA-256 e-signatures, and Indian IT Act 2000 compliance.',
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
            How Cora protects commercial shoot media, contract vaults, and financial ledgers with military-grade encryption.
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
              Every shoot photo, 4K video asset, and PDF invoice is encrypted using 256-bit Advanced Encryption Standard at rest and enforced via TLS 1.3 in transit.
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
              <Server className="w-4 h-4" />
            </div>
            <h3 className="text-sm font-bold text-zinc-950">Multi-Tenant Row Level Isolation</h3>
            <p className="text-xs text-zinc-600 leading-relaxed font-normal">
              Zero cross-tenant data leakage. Database queries enforce strict tenant boundaries and granular role-based permissions (Super Admin, Studio Owner, Crew Member).
            </p>
          </div>

          <div className="p-6 rounded-2xl bg-white border border-zinc-200/90 shadow-sm space-y-2.5">
            <div className="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-200/60">
              <Cpu className="w-4 h-4" />
            </div>
            <h3 className="text-sm font-bold text-zinc-950">Zero AI Training Guarantee</h3>
            <p className="text-xs text-zinc-600 leading-relaxed font-normal">
              Proprietary contracts, rate cards, and financial balances are strictly segregated and never submitted into training sets for public AI models.
            </p>
          </div>

        </div>

        {/* Security Deep Dive */}
        <div className="space-y-10 text-sm sm:text-base text-zinc-700 leading-relaxed font-normal">
          
          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              1. Compliance &amp; Regulatory Standards
            </h2>
            <p>
              Cora is engineered to meet stringent global and Indian regulatory requirements:
            </p>
            <ul className="list-disc pl-5 space-y-2 text-sm text-zinc-600">
              <li><strong>Indian IT Act 2000 (Section 65B):</strong> Digital contract admissibility in commercial arbitration and legal courts.</li>
              <li><strong>GST Tax Engine Verification:</strong> 18% CGST/SGST/IGST tax calculation engine adheres to CBIC invoicing guidelines.</li>
              <li><strong>SOC-2 Type II Readiness:</strong> Continuous internal controls monitoring for security, availability, and confidentiality.</li>
              <li><strong>GDPR &amp; DPDP Act 2023:</strong> Full user rights to data portability, export, and complete workspace deletion.</li>
            </ul>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              2. Infrastructure &amp; Data Center Resilience
            </h2>
            <p>
              Our production infrastructure is hosted across Tier-4 data centers with automated daily offsite snapshots, geo-redundant backups, and 99.95% guaranteed uptime.
            </p>
          </section>

          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              3. Responsible Vulnerability Disclosure
            </h2>
            <p>
              We welcome reports from independent security researchers. If you discover a potential vulnerability, please email our security engineering desk:
            </p>
            <div className="p-4 rounded-xl bg-white border border-zinc-200 font-mono text-xs text-zinc-800 space-y-1">
              <div>Cora Security Response Team</div>
              <div>Email: <a href="mailto:security@heycora.in" className="text-emerald-700 underline">security@heycora.in</a></div>
              <div>PGP Fingerprint Available Upon Request &bull; Coordinated Disclosure Policy</div>
            </div>
          </section>

        </div>

        {/* Bottom CTA */}
        <div className="mt-16 pt-8 border-t border-zinc-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <Link 
            href="/sla" 
            className="text-xs font-semibold text-zinc-700 hover:text-zinc-950 flex items-center gap-1.5"
          >
            <span>View Service Level Agreement (SLA)</span>
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
