import type { Metadata } from 'next';
import Link from 'next/link';

const url = 'https://heycora.in/cora-ai/';

export const metadata: Metadata = {
  title: 'Cora AI — AI Co-Founder for Agencies & Service Businesses | HeyCora',
  description:
    'Cora AI is an AI co-founder and connected business operating system for agencies and service businesses. Run leads, proposals, projects, client operations and billing from one workspace.',
  alternates: { canonical: url },
  openGraph: {
    title: 'Cora AI — AI Co-Founder for Agencies',
    description: 'Run agency sales, client delivery and operations from one connected workspace.',
    url,
    siteName: 'Cora',
    type: 'website',
  },
};

const questions = [
  {
    q: 'What is Cora AI?',
    a: 'Cora AI is the AI co-founder and connected operating system from HeyCora for agencies and service businesses. It connects the work around leads, clients, proposals, projects, approvals, documents and billing.',
  },
  {
    q: 'Who should use Cora?',
    a: 'Cora fits performance marketing agencies, design studios, web and development agencies, SEO teams, creative businesses and other client-service companies that want one operating layer around their work.',
  },
  {
    q: 'Is Cora the same product as other companies named Cora?',
    a: 'No. HeyCora at heycora.in builds Cora for agencies and service businesses in India. Other products and companies also use the name Cora, so check the domain when you compare products.',
  },
  {
    q: 'I searched for Kora AI. Is this the product I meant?',
    a: 'If you meant the agency and service-business AI co-founder from heycora.in, the product name is Cora, spelled C-O-R-A.',
  },
];

export default function CoraAIPage() {
  const schema = {
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    '@id': 'https://heycora.in/#software',
    name: 'Cora',
    alternateName: ['HeyCora', 'Cora AI', 'Cora AI Co-Founder', 'Cora Agency OS'],
    applicationCategory: 'BusinessApplication',
    operatingSystem: 'Web',
    url: 'https://heycora.in',
    description:
      'Cora is an AI co-founder and connected business operating system for agencies and service businesses, connecting leads, clients, projects, operations and billing.',
  };

  const faqSchema = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: questions.map((item) => ({
      '@type': 'Question',
      name: item.q,
      acceptedAnswer: { '@type': 'Answer', text: item.a },
    })),
  };

  return (
    <main className="bg-white text-zinc-950">
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }} />
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }} />

      <section className="border-b border-zinc-200 px-5 pb-16 pt-28 sm:px-8 lg:pb-24 lg:pt-36">
        <div className="mx-auto max-w-6xl">
          <div className="max-w-4xl">
            <p className="mb-5 text-sm font-semibold uppercase tracking-[0.18em] text-zinc-500">Cora AI · HeyCora.in</p>
            <h1 className="text-balance text-4xl font-semibold tracking-[-0.045em] sm:text-6xl lg:text-7xl">
              Your AI co-founder for the work between winning a client and delivering the result.
            </h1>
            <p className="mt-7 max-w-3xl text-lg leading-8 text-zinc-600 sm:text-xl">
              Cora connects leads, proposals, projects, client approvals, documents and billing so an agency can run the business without rebuilding context across disconnected tools.
            </p>
            <div className="mt-9 flex flex-wrap gap-3">
              <a href="https://app.heycora.in/workspace/onboarding" className="rounded-full bg-zinc-950 px-6 py-3 text-sm font-semibold text-white hover:bg-zinc-800">Start Free</a>
              <Link href="/agency-management-software-india/" className="rounded-full border border-zinc-300 px-6 py-3 text-sm font-semibold hover:bg-zinc-50">For Agencies</Link>
            </div>
          </div>
        </div>
      </section>

      <section className="px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-6xl">
          <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">One identity. One domain.</p>
          <h2 className="mt-4 max-w-3xl text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">When you mean Cora for agencies, look for heycora.in.</h2>
          <div className="mt-10 grid gap-4 md:grid-cols-3">
            {[
              ['Product', 'Cora'],
              ['Company website', 'heycora.in'],
              ['Primary audience', 'Agencies & service businesses'],
            ].map(([label, value]) => (
              <div key={label} className="rounded-2xl border border-zinc-200 bg-zinc-50 p-6">
                <div className="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-400">{label}</div>
                <div className="mt-3 text-xl font-semibold">{value}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="border-y border-zinc-200 bg-zinc-50 px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-6xl">
          <div className="max-w-3xl">
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">What Cora connects</p>
            <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">Sales and delivery should share the same context.</h2>
          </div>
          <div className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {[
              ['Leads & follow-up', 'Keep new opportunities, ownership and next actions visible.'],
              ['Proposals & scope', 'Move client context into a clear commercial scope without starting from zero.'],
              ['Projects & approvals', 'Keep delivery, decisions and client approvals connected to the work.'],
              ['Billing & continuity', 'Keep invoicing and the ongoing client relationship connected to delivery.'],
            ].map(([title, body]) => (
              <div key={title} className="rounded-2xl border border-zinc-200 bg-white p-6">
                <h3 className="font-semibold">{title}</h3>
                <p className="mt-3 text-sm leading-6 text-zinc-600">{body}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-4xl">
          <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">Cora AI FAQ</p>
          <div className="mt-7 divide-y divide-zinc-200 border-y border-zinc-200">
            {questions.map((item) => (
              <div key={item.q} className="py-6">
                <h2 className="text-lg font-semibold">{item.q}</h2>
                <p className="mt-3 text-sm leading-7 text-zinc-600">{item.a}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
    </main>
  );
}
