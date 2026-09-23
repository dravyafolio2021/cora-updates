import type { Metadata } from 'next';
import Link from 'next/link';

const pageUrl = 'https://heycora.in/agency-management-software-india/';

export const metadata: Metadata = {
  title: 'Agency Management Software India — CRM, Projects, Clients & Billing | Cora',
  description:
    'Cora is agency management software for Indian marketing, design, development and service agencies. Manage leads, proposals, projects, client work, invoices and operations from one connected workspace.',
  alternates: {
    canonical: pageUrl,
  },
  openGraph: {
    title: 'Agency Management Software for Indian Agencies | Cora',
    description:
      'Run leads, proposals, client delivery, projects and billing from one connected agency workspace.',
    url: pageUrl,
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Agency Management Software for Indian Agencies | Cora',
    description:
      'A connected workspace for agency sales, delivery, client operations and billing.',
  },
};

const workflow = [
  {
    step: '01',
    title: 'Lead comes in',
    description: 'Keep enquiries, client context and follow-ups in one place instead of scattered chats and sheets.',
  },
  {
    step: '02',
    title: 'Scope the work',
    description: 'Move from the conversation to a proposal, scope, commercial terms and the next client action.',
  },
  {
    step: '03',
    title: 'Deliver the project',
    description: 'Give the team one operational view of projects, tasks, ownership, deadlines and client work.',
  },
  {
    step: '04',
    title: 'Keep the client informed',
    description: 'Use a client-facing workspace for updates, approvals, documents and the information clients actually need.',
  },
  {
    step: '05',
    title: 'Bill and continue',
    description: 'Keep invoicing and the ongoing client relationship connected to the work instead of operating as a separate process.',
  },
];

const painPoints = [
  'The founder is still the only person who knows the real status of every client.',
  'Leads live in WhatsApp, projects in another tool and invoices somewhere else.',
  'Client approvals and decisions disappear inside chats, calls and email threads.',
  'The team spends time rebuilding status updates instead of moving work forward.',
  'Adding more clients creates more coordination instead of more leverage.',
  'Generic CRM software stops at the sale while agency work continues for months.',
];

const faq = [
  {
    question: 'What is agency management software?',
    answer:
      'Agency management software connects the recurring operating work of an agency: leads, clients, proposals, projects, team coordination, approvals, billing and ongoing client delivery. The goal is to reduce the number of disconnected systems the team has to maintain.',
  },
  {
    question: 'Who is Cora built for?',
    answer:
      'Cora is designed for service businesses and agencies, including performance marketing agencies, design and development studios, SEO teams, creative businesses and other client-service companies that need sales and delivery to stay connected.',
  },
  {
    question: 'Does Cora replace Meta Ads, Google Ads, Figma or specialist delivery tools?',
    answer:
      'No. Cora is the operating layer around the client relationship and delivery workflow. Specialist tools can remain where they are useful; Cora focuses on connecting the business process around the work.',
  },
  {
    question: 'Can a small agency start with Cora?',
    answer:
      'Yes. Cora is designed so a small team can begin with the workflows it needs now and add more structure as the agency grows. A free plan is available for teams that want to try the workspace before committing to a paid plan.',
  },
];

export default function AgencyManagementSoftwareIndiaPage() {
  const softwareSchema = {
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    name: 'Cora',
    applicationCategory: 'BusinessApplication',
    operatingSystem: 'Web',
    url: pageUrl,
    description:
      'Agency management software for Indian marketing, design, development and service agencies, connecting leads, clients, projects, operations and billing.',
    offers: {
      '@type': 'Offer',
      price: '0',
      priceCurrency: 'INR',
      description: 'Free plan available',
    },
  };

  const faqSchema = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faq.map((item) => ({
      '@type': 'Question',
      name: item.question,
      acceptedAnswer: {
        '@type': 'Answer',
        text: item.answer,
      },
    })),
  };

  return (
    <main className="bg-white text-zinc-950">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(softwareSchema) }}
      />
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }}
      />

      <section className="border-b border-zinc-200 px-5 pb-16 pt-28 sm:px-8 lg:pb-24 lg:pt-36">
        <div className="mx-auto max-w-6xl">
          <div className="max-w-4xl">
            <p className="mb-5 text-sm font-semibold uppercase tracking-[0.18em] text-zinc-500">
              Agency Management Software · India
            </p>
            <h1 className="text-balance text-4xl font-semibold tracking-[-0.04em] sm:text-6xl lg:text-7xl">
              Run the agency without running between six different tools.
            </h1>
            <p className="mt-7 max-w-3xl text-lg leading-8 text-zinc-600 sm:text-xl">
              Cora connects the work around your agency — leads, clients, proposals, projects,
              delivery and billing — so the business can move from enquiry to ongoing client work
              without rebuilding context at every step.
            </p>
            <div className="mt-9 flex flex-wrap gap-3">
              <a
                href="https://app.heycora.in/workspace/onboarding"
                className="rounded-full bg-zinc-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800"
              >
                Start Free
              </a>
              <Link
                href="/pricing/"
                className="rounded-full border border-zinc-300 px-6 py-3 text-sm font-semibold text-zinc-900 transition hover:bg-zinc-50"
              >
                See Pricing
              </Link>
            </div>
          </div>
        </div>
      </section>

      <section className="px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto grid max-w-6xl gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-20">
          <div>
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">The real problem</p>
            <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">
              Agency growth creates coordination debt.
            </h2>
            <p className="mt-5 text-base leading-7 text-zinc-600">
              Most agencies do not fail because they lack another task board. The friction appears
              between systems: a lead becomes a client, the client becomes a project, the project
              needs approvals, and the completed work still has to be billed and renewed.
            </p>
          </div>
          <div className="grid gap-3 sm:grid-cols-2">
            {painPoints.map((point) => (
              <div key={point} className="rounded-2xl border border-zinc-200 bg-zinc-50 p-5 text-sm leading-6 text-zinc-700">
                {point}
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="border-y border-zinc-200 bg-zinc-50 px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-6xl">
          <div className="max-w-3xl">
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">One connected flow</p>
            <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">
              From first enquiry to the next invoice.
            </h2>
            <p className="mt-5 text-base leading-7 text-zinc-600">
              The value of an agency operating system is not how many features it lists. It is how
              little context your team has to recreate when work moves from one stage to another.
            </p>
          </div>
          <div className="mt-10 grid gap-4 md:grid-cols-5">
            {workflow.map((item) => (
              <div key={item.step} className="rounded-2xl border border-zinc-200 bg-white p-5">
                <div className="text-xs font-semibold tracking-[0.16em] text-zinc-400">{item.step}</div>
                <h3 className="mt-4 text-lg font-semibold tracking-[-0.02em]">{item.title}</h3>
                <p className="mt-3 text-sm leading-6 text-zinc-600">{item.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto grid max-w-6xl gap-10 lg:grid-cols-2 lg:gap-16">
          <div className="rounded-3xl bg-zinc-950 p-7 text-white sm:p-9">
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-400">Cora is for</p>
            <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em]">Client-service agencies.</h2>
            <div className="mt-7 grid gap-3 text-sm leading-6 text-zinc-300 sm:grid-cols-2">
              <span>Performance marketing agencies</span>
              <span>Design & branding studios</span>
              <span>Web & development agencies</span>
              <span>SEO & content agencies</span>
              <span>Creative production teams</span>
              <span>Other recurring service businesses</span>
            </div>
          </div>
          <div className="rounded-3xl border border-zinc-200 p-7 sm:p-9">
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">Cora is not</p>
            <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em]">Another specialist execution tool.</h2>
            <p className="mt-5 text-base leading-7 text-zinc-600">
              Keep Meta Ads for advertising, Figma for design and the specialist tools your team
              genuinely needs. Cora is designed to connect the business layer around that work —
              the client, scope, project, approvals, documents, billing and follow-through.
            </p>
          </div>
        </div>
      </section>

      <section className="border-y border-zinc-200 bg-zinc-50 px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-6xl">
          <div className="max-w-3xl">
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">Explore the workflow</p>
            <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">
              Start with the problem you are already trying to fix.
            </h2>
          </div>
          <div className="mt-9 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Link href="/use-cases/marketing-seo/" className="rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-400">
              <div className="font-semibold">Marketing & SEO agencies</div>
              <div className="mt-2 text-sm leading-6 text-zinc-600">See the agency workflow and client operations use case.</div>
            </Link>
            <Link href="/features/" className="rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-400">
              <div className="font-semibold">Explore features</div>
              <div className="mt-2 text-sm leading-6 text-zinc-600">Browse the connected modules available inside Cora.</div>
            </Link>
            <Link href="/compare/cora-vs-hubspot" className="rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-400">
              <div className="font-semibold">Cora vs HubSpot</div>
              <div className="mt-2 text-sm leading-6 text-zinc-600">Understand the difference between a CRM and the broader operating workflow.</div>
            </Link>
            <Link href="/articles/" className="rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-400">
              <div className="font-semibold">Agency operations guides</div>
              <div className="mt-2 text-sm leading-6 text-zinc-600">Read practical guides on websites, delivery and business operations.</div>
            </Link>
          </div>
        </div>
      </section>

      <section className="px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-4xl">
          <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">Questions agency owners ask</p>
          <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">Agency management software FAQ</h2>
          <div className="mt-8 divide-y divide-zinc-200 border-y border-zinc-200">
            {faq.map((item) => (
              <div key={item.question} className="py-6">
                <h3 className="text-lg font-semibold tracking-[-0.02em]">{item.question}</h3>
                <p className="mt-3 text-sm leading-7 text-zinc-600">{item.answer}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="px-5 pb-20 sm:px-8 lg:pb-28">
        <div className="mx-auto max-w-6xl rounded-3xl bg-zinc-950 px-7 py-10 text-white sm:px-10 lg:flex lg:items-center lg:justify-between lg:gap-12">
          <div>
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-400">Built for service businesses</p>
            <h2 className="mt-3 text-3xl font-semibold tracking-[-0.035em]">See whether one connected workspace fits your agency.</h2>
          </div>
          <div className="mt-7 flex shrink-0 flex-wrap gap-3 lg:mt-0">
            <a
              href="https://app.heycora.in/workspace/onboarding"
              className="rounded-full bg-white px-6 py-3 text-sm font-semibold text-zinc-950 transition hover:bg-zinc-200"
            >
              Start Free
            </a>
            <Link
              href="/pricing/"
              className="rounded-full border border-zinc-700 px-6 py-3 text-sm font-semibold text-white transition hover:border-zinc-500"
            >
              Pricing
            </Link>
          </div>
        </div>
      </section>
    </main>
  );
}
