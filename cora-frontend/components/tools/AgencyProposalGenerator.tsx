'use client';

import { useMemo, useState } from 'react';

type FormState = {
  agencyType: string;
  clientName: string;
  projectGoal: string;
  deliverables: string;
  timeline: string;
  price: string;
  revisions: string;
  paymentTerms: string;
};

const initialState: FormState = {
  agencyType: 'Performance Marketing Agency',
  clientName: '',
  projectGoal: '',
  deliverables: '',
  timeline: '90 days',
  price: '',
  revisions: '2 rounds',
  paymentTerms: '50% advance, 50% before final handover',
};

function cleanLines(value: string) {
  return value
    .split(/\n|,/)
    .map((item) => item.trim())
    .filter(Boolean);
}

export function AgencyProposalGenerator() {
  const [form, setForm] = useState<FormState>(initialState);
  const [generated, setGenerated] = useState(false);
  const [copied, setCopied] = useState(false);

  const proposal = useMemo(() => {
    const client = form.clientName.trim() || 'Client';
    const goal = form.projectGoal.trim() || 'Achieve the agreed business outcome through a focused agency engagement.';
    const deliverables = cleanLines(form.deliverables);
    const deliveryList = deliverables.length
      ? deliverables.map((item) => `- ${item}`).join('\n')
      : '- Final deliverables will follow the approved scope.\n- The agency will document any scope change before starting additional work.';

    return `PROPOSAL & SCOPE OF WORK\n\nPrepared for: ${client}\nAgency type: ${form.agencyType}\n\n1. PROJECT OBJECTIVE\n${goal}\n\n2. SCOPE OF WORK\n${deliveryList}\n\n3. DELIVERY TIMELINE\nThe engagement will run for ${form.timeline || 'the agreed timeline'}. The agency will confirm milestone dates after the client shares the required access, assets and approvals.\n\n4. COMMERCIALS\nProject / retainer fee: ${form.price.trim() || 'To be confirmed'}\nPayment terms: ${form.paymentTerms || 'To be confirmed'}\n\n5. REVISION & APPROVAL RULE\nThe fee includes ${form.revisions || 'the agreed revision rounds'}. The client will consolidate feedback before each revision round. The agency will quote additional work before starting anything outside the approved scope.\n\n6. CLIENT RESPONSIBILITIES\n- Share required access, brand assets and source material on time.\n- Nominate one decision-maker for approvals.\n- Review milestones within the agreed review window.\n- Confirm scope changes in writing before the agency starts additional work.\n\n7. OUT OF SCOPE\nAnything not listed in the approved deliverables stays outside this engagement unless both parties approve a written scope change.\n\n8. NEXT STEP\nApprove the scope, confirm the commercial terms and schedule the kickoff.\n\nGenerated with Cora — heycora.in`;
  }, [form]);

  const update = (key: keyof FormState, value: string) => {
    setForm((current) => ({ ...current, [key]: value }));
    setGenerated(false);
    setCopied(false);
  };

  const copyProposal = async () => {
    await navigator.clipboard.writeText(proposal);
    setCopied(true);
    window.setTimeout(() => setCopied(false), 1800);
  };

  const downloadProposal = () => {
    const blob = new Blob([proposal], { type: 'text/plain;charset=utf-8' });
    const href = URL.createObjectURL(blob);
    const anchor = document.createElement('a');
    anchor.href = href;
    anchor.download = `agency-proposal-${(form.clientName || 'client').toLowerCase().replace(/[^a-z0-9]+/g, '-')}.txt`;
    document.body.appendChild(anchor);
    anchor.click();
    anchor.remove();
    URL.revokeObjectURL(href);
  };

  return (
    <div className="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
      <div className="rounded-3xl border border-zinc-200 bg-white p-6 sm:p-8">
        <div className="grid gap-5">
          <label className="grid gap-2 text-sm font-medium">
            Agency type
            <select value={form.agencyType} onChange={(e) => update('agencyType', e.target.value)} className="rounded-xl border border-zinc-300 bg-white px-4 py-3 font-normal outline-none focus:border-zinc-950">
              <option>Performance Marketing Agency</option>
              <option>Design & Branding Agency</option>
              <option>Web & Development Agency</option>
              <option>SEO & Content Agency</option>
              <option>Creative Production Agency</option>
              <option>Consulting Agency</option>
            </select>
          </label>

          <label className="grid gap-2 text-sm font-medium">
            Client name
            <input value={form.clientName} onChange={(e) => update('clientName', e.target.value)} placeholder="Acme Pvt. Ltd." className="rounded-xl border border-zinc-300 px-4 py-3 font-normal outline-none focus:border-zinc-950" />
          </label>

          <label className="grid gap-2 text-sm font-medium">
            What outcome does the client want?
            <textarea value={form.projectGoal} onChange={(e) => update('projectGoal', e.target.value)} placeholder="Increase qualified leads while improving conversion from paid traffic." rows={3} className="rounded-xl border border-zinc-300 px-4 py-3 font-normal outline-none focus:border-zinc-950" />
          </label>

          <label className="grid gap-2 text-sm font-medium">
            Deliverables
            <textarea value={form.deliverables} onChange={(e) => update('deliverables', e.target.value)} placeholder={'Meta campaign management\n4 new ad creatives every month\nLanding page CRO\nWeekly performance review'} rows={5} className="rounded-xl border border-zinc-300 px-4 py-3 font-normal outline-none focus:border-zinc-950" />
          </label>

          <div className="grid gap-4 sm:grid-cols-2">
            <label className="grid gap-2 text-sm font-medium">
              Timeline
              <input value={form.timeline} onChange={(e) => update('timeline', e.target.value)} placeholder="90 days" className="rounded-xl border border-zinc-300 px-4 py-3 font-normal outline-none focus:border-zinc-950" />
            </label>
            <label className="grid gap-2 text-sm font-medium">
              Fee
              <input value={form.price} onChange={(e) => update('price', e.target.value)} placeholder="₹50,000 / month + GST" className="rounded-xl border border-zinc-300 px-4 py-3 font-normal outline-none focus:border-zinc-950" />
            </label>
          </div>

          <div className="grid gap-4 sm:grid-cols-2">
            <label className="grid gap-2 text-sm font-medium">
              Included revisions
              <input value={form.revisions} onChange={(e) => update('revisions', e.target.value)} className="rounded-xl border border-zinc-300 px-4 py-3 font-normal outline-none focus:border-zinc-950" />
            </label>
            <label className="grid gap-2 text-sm font-medium">
              Payment terms
              <input value={form.paymentTerms} onChange={(e) => update('paymentTerms', e.target.value)} className="rounded-xl border border-zinc-300 px-4 py-3 font-normal outline-none focus:border-zinc-950" />
            </label>
          </div>

          <button onClick={() => setGenerated(true)} className="mt-2 rounded-xl bg-zinc-950 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-zinc-800">
            Generate Proposal
          </button>
        </div>
      </div>

      <div className="rounded-3xl border border-zinc-200 bg-zinc-50 p-6 sm:p-8">
        {!generated ? (
          <div className="flex min-h-[520px] flex-col items-center justify-center text-center">
            <div className="text-sm font-semibold uppercase tracking-[0.15em] text-zinc-400">Your output</div>
            <h2 className="mt-4 max-w-md text-3xl font-semibold tracking-[-0.035em]">Turn rough project details into a clean scope in one click.</h2>
            <p className="mt-4 max-w-md text-sm leading-7 text-zinc-600">Fill the brief once. The generator structures the objective, scope, timeline, commercials, approval rules, exclusions and next step.</p>
          </div>
        ) : (
          <div>
            <div className="flex flex-wrap items-center justify-between gap-3">
              <div>
                <div className="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-400">Generated proposal</div>
                <h2 className="mt-2 text-2xl font-semibold tracking-[-0.03em]">Ready to edit and send.</h2>
              </div>
              <div className="flex gap-2">
                <button onClick={copyProposal} className="rounded-full border border-zinc-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-zinc-100">{copied ? 'Copied' : 'Copy'}</button>
                <button onClick={downloadProposal} className="rounded-full bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Download</button>
              </div>
            </div>
            <pre className="mt-6 max-h-[650px] overflow-auto whitespace-pre-wrap rounded-2xl border border-zinc-200 bg-white p-5 font-sans text-sm leading-7 text-zinc-700">{proposal}</pre>
            <div className="mt-5 rounded-2xl bg-zinc-950 p-5 text-white">
              <div className="text-sm font-semibold">Stop rebuilding proposals from scratch.</div>
              <p className="mt-2 text-sm leading-6 text-zinc-300">Cora keeps leads, scope, projects, client approvals and billing in one workspace.</p>
              <a href="https://app.heycora.in/workspace/onboarding" className="mt-4 inline-flex rounded-full bg-white px-4 py-2 text-sm font-semibold text-zinc-950">Start Free</a>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
