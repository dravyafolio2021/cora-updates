import React from 'react';
import Link from 'next/link';
import { ShieldCheck, FileText, ArrowLeft, ArrowRight } from 'lucide-react';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Terms of Service | Cora Platform',
  description: 'Read the Terms of Service governing the use of Cora Platform, our AI workspaces, contracts, and financial tools for professional service businesses.',
};

export default function TermsPage() {
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
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-800 border border-zinc-200/80 mb-4">
            <FileText className="w-3.5 h-3.5 text-zinc-600" />
            <span>LEGAL &amp; COMPLIANCE</span>
          </div>
          <h1 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 mb-4">
            Terms of Service
          </h1>
          <p className="text-zinc-600 text-sm sm:text-base leading-relaxed">
            Effective Date: January 1, 2026 &bull; Last Updated: August 20, 2026
          </p>
        </div>

        {/* Executive Summary Card */}
        <div className="p-6 rounded-2xl bg-white border border-zinc-200/90 shadow-sm mb-12 space-y-3">
          <div className="flex items-center gap-2 text-sm font-bold text-zinc-950">
            <ShieldCheck className="w-4 h-4 text-emerald-600" />
            <span>Executive Summary</span>
          </div>
          <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
            By accessing or using <strong>Cora Platform</strong> (&ldquo;Cora&rdquo;, &ldquo;we&rdquo;, &ldquo;our&rdquo;), you agree to these Terms. Cora is the autonomous business operating system designed for professional service businesses, creative agencies, consultancies, commercial studios, legal &amp; financial advisory firms, healthcare clinics, and independent service founders. You retain 100% intellectual property ownership of all your uploaded deliverables, client data, contracts, and business records. We never sell your data or train foundation models on your proprietary content.
          </p>
        </div>

        {/* Legal Sections */}
        <div className="space-y-10 text-sm sm:text-base text-zinc-700 leading-relaxed font-normal">
          
          {/* Section 1 */}
          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              1. Acceptance of Terms &amp; Eligibility
            </h2>
            <p>
              By creating an account, subscribing to any paid plan, or accessing the Cora platform at <code className="text-xs bg-zinc-100 px-1.5 py-0.5 rounded text-zinc-900">heycora.in</code> or <code className="text-xs bg-zinc-100 px-1.5 py-0.5 rounded text-zinc-900">app.heycora.in</code>, you acknowledge that you are at least 18 years of age and authorized to bind your agency, studio, consultancy, clinic, brokerage, or corporate entity.
            </p>
          </section>

          {/* Section 2 */}
          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              2. Workspace Services &amp; AI Agents
            </h2>
            <p>
              Cora provides an autonomous operating system including AI discovery scoping, Kanban lead pipelines, 18% GST tax calculation engines, SHA-256 e-signature registries, booking schedules, and project asset vaults. You understand that:
            </p>
            <ul className="list-disc pl-5 space-y-2 text-sm text-zinc-600">
              <li>AI-generated draft scopes, client proposals, and summaries are assistance tools and should be reviewed prior to formal execution.</li>
              <li>You are solely responsible for ensuring commercial agreements, client retainers, and financial invoices comply with your jurisdiction&apos;s commercial law.</li>
            </ul>
          </section>

          {/* Section 3 */}
          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              3. Intellectual Property &amp; Content Ownership
            </h2>
            <p>
              <strong>Your Content Remains Yours:</strong> All business documents, client rosters, project deliverables, creative media, legal contracts, and financial receipts uploaded to Cora remain your sole exclusive property. Cora claims zero copyright, licensing, or IP rights over your business assets.
            </p>
            <p>
              <strong>Platform IP:</strong> The Cora software, user interface design system, vector components, and autonomous AI orchestration engine are protected by copyright and intellectual property laws.
            </p>
          </section>

          {/* Section 4 */}
          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              4. Subscription, Billing &amp; Taxes
            </h2>
            <p>
              Cora offers monthly and annual subscription tiers. Subscriptions renew automatically unless cancelled prior to the billing date.
            </p>
            <ul className="list-disc pl-5 space-y-2 text-sm text-zinc-600">
              <li><strong>Indian Accounts:</strong> Billed in INR. Cora operates as a registered enterprise under the UDYAM MSME framework (Ministry of Micro, Small and Medium Enterprises, Govt. of India). Tax invoices and receipts are issued in compliance with Indian regulatory standards.</li>
              <li><strong>International Accounts:</strong> Invoices are billed in USD via authorized payment gateways.</li>
              <li><strong>Cancellation:</strong> You can cancel anytime inside your workspace dashboard with 1 click. Access continues until the current billing cycle expires.</li>
            </ul>
          </section>

          {/* Section 5 */}
          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              5. E-Signatures &amp; Legal Validity
            </h2>
            <p>
              Cora&apos;s E-Signature Vault utilizes cryptographic SHA-256 audit logging compliant with the <strong>Indian Information Technology Act 2000</strong> and <strong>US ESIGN Act</strong>. Both parties agree that digital signatures rendered on the platform constitute legally binding consent.
            </p>
          </section>

          {/* Section 6 */}
          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              6. Limitation of Liability
            </h2>
            <p>
              To the maximum extent permitted by law, Cora shall not be liable for indirect, incidental, or consequential damages resulting from third-party network downtime, cloud storage outages, or client payment disputes. Our aggregate liability is limited to the fees paid by you in the 12 months preceding the claim.
            </p>
          </section>

          {/* Section 7 */}
          <section className="space-y-3">
            <h2 className="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              7. Contact &amp; Governance
            </h2>
            <p>
              These Terms are governed by the laws of India. For any legal inquiries or formal notices, contact our compliance desk at:
            </p>
            <div className="p-4 rounded-xl bg-white border border-zinc-200 font-mono text-xs text-zinc-800 space-y-1">
              <div>Cora Platforms Inc. &bull; Legal Compliance Office</div>
              <div>Email: <a href="mailto:legal@heycora.in" className="text-emerald-700 underline">legal@heycora.in</a></div>
              <div>Support: <a href="mailto:support@heycora.in" className="text-emerald-700 underline">support@heycora.in</a></div>
            </div>
          </section>

        </div>

        {/* Bottom CTA */}
        <div className="mt-16 pt-8 border-t border-zinc-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <Link 
            href="/privacy" 
            className="text-xs font-semibold text-zinc-700 hover:text-zinc-950 flex items-center gap-1.5"
          >
            <span>Read our Privacy Policy</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </Link>
          <a
            href="https://app.heycora.in/workspace/login"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-zinc-800 transition-all"
          >
            <span>Go to Workspace</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
          </a>
        </div>

      </div>
    </main>
  );
}
