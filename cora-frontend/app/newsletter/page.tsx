import type { Metadata } from 'next';
import { NewsletterCapture } from '@/components/newsletter/NewsletterCapture';

const url = 'https://heycora.in/newsletter/';

export const metadata: Metadata = {
  title: 'Cora Operator Brief — Weekly Systems for Agency Owners',
  description:
    'One practical weekly brief for agency owners: client onboarding, scope control, retainers, reporting, delivery systems and useful AI workflows.',
  alternates: { canonical: url },
  openGraph: {
    title: 'Cora Operator Brief',
    description: 'One useful agency operating system every week.',
    url,
    siteName: 'Cora',
    type: 'website',
  },
};

const topics = [
  ['Client onboarding', 'Reduce back-and-forth and start projects with clearer ownership.'],
  ['Scope & retainers', 'Protect delivery quality without turning every client conversation into friction.'],
  ['Reporting & approvals', 'Make client communication easier to understand and easier to act on.'],
  ['AI workflows', 'Use AI where it actually removes repetitive agency work.'],
];

export default function NewsletterPage() {
  return (
    <main className="bg-white text-zinc-950">
      <section className="relative overflow-hidden border-b border-zinc-200 bg-gradient-to-b from-sky-200 via-white to-white px-5 pb-16 pt-28 sm:px-8 lg:pb-24 lg:pt-36">
        <div className="mx-auto max-w-6xl text-center">
          <div className="inline-flex rounded-full border border-white/80 bg-white/80 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-zinc-700 shadow-sm backdrop-blur">
            Cora Operator Brief
          </div>
          <h1 className="mx-auto mt-6 max-w-4xl text-balance font-display text-4xl font-semibold tracking-[-0.045em] sm:text-6xl lg:text-7xl">
            One useful agency system every week.
          </h1>
          <p className="mx-auto mt-6 max-w-2xl text-base leading-7 text-zinc-600 sm:text-lg">
            No generic SaaS updates. Just practical systems, templates and operating ideas for running a calmer, more profitable client-service business.
          </p>
          <div className="mx-auto mt-10 max-w-3xl text-left">
            <NewsletterCapture
              source="newsletter_page"
              eyebrow="JOIN FREE"
              title="Get the next Operator Brief."
              description="One useful note each week. Unsubscribe whenever it stops being useful."
            />
          </div>
        </div>
      </section>

      <section className="px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-6xl">
          <div className="mx-auto max-w-3xl text-center">
            <p className="text-[11px] font-semibold uppercase tracking-[0.16em] text-zinc-400">What you will get</p>
            <h2 className="mt-3 font-display text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">
              Built for people who actually run the agency.
            </h2>
          </div>
          <div className="mt-10 grid gap-4 md:grid-cols-2">
            {topics.map(([title, body]) => (
              <div key={title} className="rounded-2xl border border-zinc-200 bg-zinc-50 p-6 sm:p-7">
                <h3 className="text-lg font-semibold tracking-[-0.02em]">{title}</h3>
                <p className="mt-2 text-sm leading-6 text-zinc-600">{body}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="border-y border-zinc-200 bg-zinc-950 px-5 py-16 text-white sm:px-8 lg:py-20">
        <div className="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
          <div>
            <p className="text-[11px] font-semibold uppercase tracking-[0.16em] text-zinc-500">The rule</p>
            <h2 className="mt-3 max-w-3xl font-display text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">
              If it does not save time, reduce chaos or improve a client outcome, we do not send it.
            </h2>
          </div>
          <a href="#top" className="hidden" aria-hidden="true">Back to top</a>
        </div>
      </section>
    </main>
  );
}
