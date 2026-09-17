import React from 'react';
import { Check, X, ShieldCheck, Sparkles, ArrowRight } from 'lucide-react';

const COMPARISON_ROWS = [
  {
    feature: 'Pre-Seeded Agency Master Contracts',
    generic: 'Blank templates or expensive lawyer drafts (₹15,000+)',
    cora: '64+ Turnkey digital agency contracts with SHA-256 e-sign',
    coraHighlight: true
  },
  {
    feature: '18% GST SAC Code Auto-Splitting',
    generic: 'Manual spreadsheet math and delayed CA reconciliation',
    cora: 'Automatic CGST/SGST splitting by SAC 998314/998361',
    coraHighlight: true
  },
  {
    feature: 'Milestone Escrow & Staging Approvals',
    generic: 'Unrecorded WhatsApp approvals causing scope creep',
    cora: 'Cryptographic client sign-off gates before code deploy',
    coraHighlight: true
  },
  {
    feature: 'Voice-to-Scope Proposal Generator',
    generic: '3-4 hours typing formal scope & SOW documents',
    cora: 'Convert 2-min audio brief to client contract in seconds',
    coraHighlight: true
  },
  {
    feature: 'Client Review & Asset Proofing Vault',
    generic: 'Fragmented subscriptions (DocuSign, Drive, Frame.io)',
    cora: 'Integrated 4K asset vault, Figma sign-off & reviews',
    coraHighlight: true
  },
  {
    feature: 'Monthly Agency Software Cost',
    generic: '₹15,000 – ₹30,000 / month across 6+ disparate apps',
    cora: '₹0 Free Tier • Flat ₹2,999 / mo for entire agency',
    coraHighlight: true
  }
];

export function IndustryComparisonTable() {
  return (
    <section className="w-full py-16 sm:py-[100px] bg-zinc-50/70 border-t border-zinc-200/80">
      <div className="max-w-[1100px] mx-auto px-4 sm:px-6">
        
        {/* Section Header */}
        <div className="text-center max-w-[720px] mx-auto mb-12">
          <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-white px-3 py-1 rounded-full border border-zinc-200 shadow-2xs mb-3 inline-block">
            Architectural Advantage
          </span>
          <h2 className="font-display text-2xl sm:text-3xl md:text-4xl font-bold text-zinc-950 tracking-tight mb-3">
            Why Generic CRMs Fail High-Performing Agencies
          </h2>
          <p className="text-xs sm:text-sm md:text-base text-zinc-600 font-normal leading-relaxed">
            Generic CRMs force your team to stitch together 5+ separate tools. Cora delivers pre-seeded agency master agreements, automated 18% GST SAC billing, milestone review portals, and living AI copilots in one unified platform.
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
