import type { Metadata } from 'next';
import Link from 'next/link';
import { AgencyProposalGenerator } from '@/components/tools/AgencyProposalGenerator';

const url = 'https://heycora.in/tools/agency-proposal-generator/';

export const metadata: Metadata = {
  title: 'Free Agency Proposal & Scope Generator | Cora',
  description:
    'Create a client-ready agency proposal and scope of work in minutes. Structure objectives, deliverables, timelines, commercials, revisions, exclusions and next steps for free.',
  alternates: { canonical: url },
  openGraph: {
    title: 'Free Agency Proposal & Scope Generator | Cora',
    description: 'Turn rough client details into a clean proposal and scope of work in minutes.',
    url,
    siteName: 'Cora',
    type: 'website',
  },
};

export default function AgencyProposalGeneratorPage() {
  const schema = {
    '@context': 'https://schema.org',
    '@type': 'WebApplication',
    name: 'Agency Proposal & Scope Generator',
    applicationCategory: 'BusinessApplication',
    operatingSystem: 'Web',
    url,
    isAccessibleForFree: true,
    description:
      'A free browser-based tool that helps agencies structure a client proposal and scope of work from project details.',
    provider: {
      '@type': 'Organization',
      name: 'Cora',
      url: 'https://heycora.in',
    },
  };

  return (
    <main className="bg-white text-zinc-950">
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }} />

      <section className="border-b border-zinc-200 px-5 pb-12 pt-28 sm:px-8 lg:pt-36">
        <div className="mx-auto max-w-6xl">
          <div className="max-w-4xl">
            <p className="text-sm font-semibold uppercase tracking-[0.18em] text-zinc-500">Free Agency Tool · No Login</p>
            <h1 className="mt-5 text-balance text-4xl font-semibold tracking-[-0.045em] sm:text-6xl">
              Turn a rough client brief into a proposal you can actually send.
            </h1>
            <p className="mt-6 max-w-3xl text-lg leading-8 text-zinc-600">
              Add the client goal, deliverables, timeline and commercials. Cora structures the scope, approval rules, exclusions and next step so you do not start from a blank document.
            </p>
          </div>
        </div>
      </section>

      <section className="bg-zinc-50 px-5 py-12 sm:px-8 lg:py-16">
        <div className="mx-auto max-w-6xl">
          <AgencyProposalGenerator />
        </div>
      </section>

      <section className="px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto grid max-w-6xl gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:gap-20">
          <div>
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">Why we built it</p>
            <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">Good proposals reduce ambiguity before the project starts.</h2>
          </div>
          <div className="grid gap-4 sm:grid-cols-2">
            {[
              ['Clear scope', 'Define what the agency will deliver before work begins.'],
              ['Approval rules', 'Set revision and feedback rules before they turn into scope creep.'],
              ['Commercial clarity', 'Put pricing and payment terms beside the work they cover.'],
              ['Faster kickoff', 'Give the client one clear next step after reviewing the proposal.'],
            ].map(([title, body]) => (
              <div key={title} className="rounded-2xl border border-zinc-200 p-5">
                <h3 className="font-semibold">{title}</h3>
                <p className="mt-2 text-sm leading-6 text-zinc-600">{body}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="border-y border-zinc-200 bg-zinc-50 px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-6xl rounded-3xl bg-zinc-950 p-8 text-white sm:p-10 lg:flex lg:items-center lg:justify-between lg:gap-12">
          <div className="max-w-2xl">
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-400">From free tool to operating system</p>
            <h2 className="mt-3 text-3xl font-semibold tracking-[-0.035em]">Keep the proposal connected to the work that follows.</h2>
            <p className="mt-4 text-sm leading-7 text-zinc-300">Cora connects client context, scope, projects, approvals and billing instead of making your team move the same information between tools.</p>
          </div>
          <div className="mt-7 flex shrink-0 flex-wrap gap-3 lg:mt-0">
            <a href="https://app.heycora.in/workspace/onboarding" className="rounded-full bg-white px-5 py-3 text-sm font-semibold text-zinc-950">Start Free</a>
            <Link href="/agency-management-software-india/" className="rounded-full border border-zinc-700 px-5 py-3 text-sm font-semibold text-white">Cora for Agencies</Link>
          </div>
        </div>
      </section>
    </main>
  );
}
