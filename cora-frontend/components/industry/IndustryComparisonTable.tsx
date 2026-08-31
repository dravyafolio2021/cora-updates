import React from 'react';
import { Check, X, ShieldCheck, Sparkles, ArrowRight } from 'lucide-react';

const COMPARISON_ROWS = [
  {
    feature: 'Pre-Seeded Indian IT Act 2000 Contracts',
    generic: 'Blank templates or custom lawyer drafts (₹15,000+)',
    cora: '64+ Industry-specific turnkey contracts included',
    coraHighlight: true
  },
  {
    feature: '18% GST & SAC Code Auto-Splitting',
    generic: 'Manual spreadsheet math and CA reconciliation',
    cora: 'Automated CGST/SGST splitting by exact SAC code',
    coraHighlight: true
  },
  {
    feature: 'Milestone Escrow & Staging Approvals',
    generic: 'Scattered email threads and unrecorded verbal approvals',
    cora: 'Cryptographic digital sign-off gates before delivery',
    coraHighlight: true
  },
  {
    feature: 'Voice-to-Scope Proposal Generator',
    generic: '3-4 hours typing formal scope documents',
    cora: 'Audio brief to formal contract in 2 minutes',
    coraHighlight: true
  },
  {
    feature: 'Integrated Client Review & Proofing Vault',
    generic: 'Paid separate tools (Frame.io, Google Drive, DocuSign)',
    cora: 'Built-in 4K proofing, asset vaults & Google reviews',
    coraHighlight: true
  },
  {
    feature: 'Monthly SaaS Stack Cost',
    generic: '₹12,000 – ₹25,000 / month across 5+ apps',
    cora: '₹0 Free Tier • Flat ₹2,999 / mo Unlimited',
    coraHighlight: true
  }
];

export function IndustryComparisonTable() {
  return (
    <section className="w-full py-16 sm:py-24 bg-zinc-50/70 border-t border-zinc-200/80">
      <div className="max-w-[1100px] mx-auto px-4 sm:px-6">
        
        {/* Section Header */}
        <div className="text-center max-w-[720px] mx-auto mb-12">
          <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-white px-3 py-1 rounded-full border border-zinc-200 shadow-2xs mb-3 inline-block">
            Architectural Difference
          </span>
          <h2 className="font-display text-2xl sm:text-3xl md:text-4xl font-bold text-zinc-950 tracking-tight mb-3">
            Why Generic Tools Fail Service Businesses
          </h2>
          <p className="text-xs sm:text-sm md:text-base text-zinc-600 font-normal leading-relaxed">
            Generic CRMs treat every business the same. Cora comes pre-seeded with the exact contract structures, tax classifications, and milestone approval gates required by modern Indian service firms.
          </p>
        </div>

        {/* Comparison Table */}
        <div className="rounded-3xl bg-white border border-zinc-200/90 shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="border-b border-zinc-200 bg-zinc-50/80">
                  <th className="p-4 sm:p-5 text-xs font-mono font-bold text-zinc-500 uppercase tracking-wider w-1/3">
                    Operational Capability
                  </th>
                  <th className="p-4 sm:p-5 text-xs font-mono font-bold text-zinc-500 uppercase tracking-wider w-1/3">
                    Generic SaaS Stack (Notion + DocuSign + Zoho)
                  </th>
                  <th className="p-4 sm:p-5 text-xs font-mono font-bold text-zinc-950 uppercase tracking-wider w-1/3 bg-zinc-100/80">
                    <div className="flex items-center gap-1.5">
                      <span className="w-2 h-2 rounded-full bg-emerald-500" />
                      <span>Cora Industry OS</span>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-100 text-xs sm:text-sm">
                {COMPARISON_ROWS.map((row, idx) => (
                  <tr key={idx} className="hover:bg-zinc-50/50 transition-colors">
                    <td className="p-4 sm:p-5 font-semibold text-zinc-900">
                      {row.feature}
                    </td>
                    <td className="p-4 sm:p-5 text-zinc-500">
                      {row.generic}
                    </td>
                    <td className="p-4 sm:p-5 font-medium text-zinc-950 bg-zinc-50/40">
                      <div className="flex items-center gap-2">
                        <Check className="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>{row.cora}</span>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </section>
  );
}
