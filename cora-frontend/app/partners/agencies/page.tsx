import type { Metadata } from 'next';
import Link from 'next/link';
import { AgencyPartnerApplication } from './AgencyPartnerApplication';

const url = 'https://heycora.in/partners/agencies/';

export const metadata: Metadata = {
  title: 'Cora Agency Partner Program — Free Pro Access + 25–30% Commission',
  description:
    'Join the Cora Agency Partner Program. Run your own agency on Cora Professional at no cost and earn 25–30% commission on eligible client referrals.',
  alternates: { canonical: url },
  openGraph: {
    title: 'Cora Agency Partner Program',
    description: 'Use Cora Professional for your agency at no cost and earn 25–30% commission on eligible client referrals.',
    url,
    siteName: 'Cora',
    type: 'website',
  },
};

const whoFits = [
  'Performance marketing agencies',
  'Web & Shopify development agencies',
  'Branding and design studios',
  'SEO and content agencies',
  'Automation and implementation consultancies',
  'Creative service businesses with recurring clients',
];

const benefits = [
  {
    title: 'Use Cora Professional free',
    body: 'Run your own agency on the Professional plan while you stay active in the partner program. Learn the product by using it in your own operations first.',
  },
  {
    title: 'Earn 25–30% commission',
    body: 'Refer eligible clients who need a connected operating system and earn partner commission when they become paying Cora customers.',
  },
  {
    title: 'Keep the client relationship',
    body: 'You stay the trusted advisor. Cora becomes the software layer you can recommend when a client needs better operations, workflows and visibility.',
  },
  {
    title: 'Create recurring software income',
    body: 'Add a new revenue stream without building and maintaining your own SaaS product. Focus on the services your agency already sells well.',
  },
];

const faqs = [
  {
    q: 'Who can join the Cora Agency Partner Program?',
    a: 'We built the program for agencies and consultants that already work with service businesses, including performance marketing, development, design, SEO, automation and creative agencies.',
  },
  {
    q: 'Do agencies pay for their own Cora Professional workspace?',
    a: 'Approved agency partners can use Cora Professional for their own agency at no cost while they remain active in the partner program, subject to the partner terms.',
  },
  {
    q: 'How much commission can a partner earn?',
    a: 'Cora partner commission ranges from 25% to 30% on eligible referred customers. The applicable rate depends on the partner tier and current program terms.',
  },
  {
    q: 'Do I need to sell Cora to every client?',
    a: 'No. Recommend Cora only when it solves a real client problem. The program works best when agencies use the product themselves, understand the workflow and introduce it where it fits.',
  },
];

export default function AgencyPartnerPage() {
  const faqSchema = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faqs.map((item) => ({
      '@type': 'Question',
      name: item.q,
      acceptedAnswer: { '@type': 'Answer', text: item.a },
    })),
  };

  return (
    <main className="bg-white text-zinc-950">
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }} />

      <section className="border-b border-zinc-200 px-5 pb-16 pt-28 sm:px-8 lg:pb-24 lg:pt-36">
        <div className="mx-auto max-w-6xl">
          <div className="grid gap-12 lg:grid-cols-[1.08fr_0.92fr] lg:items-center">
            <div>
              <p className="mb-5 text-sm font-semibold uppercase tracking-[0.18em] text-zinc-500">Cora Agency Partner Program</p>
              <h1 className="text-balance text-4xl font-semibold tracking-[-0.045em] sm:text-6xl lg:text-7xl">
                Run your agency on Cora Pro. Free. Then earn when your clients use it too.
              </h1>
              <p className="mt-7 max-w-3xl text-lg leading-8 text-zinc-600 sm:text-xl">
                We give approved agencies Professional access for their own business. You use Cora first, recommend it when it genuinely fits a client, and earn 25–30% commission on eligible referrals.
              </p>
              <div className="mt-9 flex flex-wrap gap-3">
                <a href="#apply" className="rounded-full bg-zinc-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800">Apply as Partner</a>
                <Link href="/agency-management-software-india/" className="rounded-full border border-zinc-300 px-6 py-3 text-sm font-semibold transition hover:bg-zinc-50">See Cora for Agencies</Link>
              </div>
              <p className="mt-4 text-xs leading-5 text-zinc-500">No application fee. We review agency fit before enabling partner benefits.</p>
            </div>

            <div className="rounded-3xl border border-zinc-200 bg-zinc-50 p-6 sm:p-8">
              <div className="text-xs font-semibold uppercase tracking-[0.16em] text-zinc-400">Partner economics</div>
              <div className="mt-6 grid gap-3">
                <div className="rounded-2xl border border-zinc-200 bg-white p-5">
                  <div className="text-sm text-zinc-500">Your agency workspace</div>
                  <div className="mt-2 text-3xl font-semibold tracking-[-0.03em]">₹0</div>
                  <div className="mt-1 text-sm text-zinc-500">Professional access for approved active partners</div>
                </div>
                <div className="rounded-2xl border border-zinc-200 bg-white p-5">
                  <div className="text-sm text-zinc-500">Eligible referral commission</div>
                  <div className="mt-2 text-3xl font-semibold tracking-[-0.03em]">25–30%</div>
                  <div className="mt-1 text-sm text-zinc-500">Based on partner tier and current program terms</div>
                </div>
                <div className="rounded-2xl bg-zinc-950 p-5 text-white">
                  <div className="text-sm text-zinc-400">Simple goal</div>
                  <div className="mt-2 text-2xl font-semibold tracking-[-0.03em]">Use it. Trust it. Refer it.</div>
                  <div className="mt-2 text-sm leading-6 text-zinc-300">We want agencies to recommend Cora from real product experience, not from an affiliate link alone.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-6xl">
          <div className="max-w-3xl">
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">Why agencies join</p>
            <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">Get a product your agency can use before you ever refer it.</h2>
          </div>
          <div className="mt-10 grid gap-4 md:grid-cols-2">
            {benefits.map((benefit) => (
              <div key={benefit.title} className="rounded-2xl border border-zinc-200 p-6 sm:p-7">
                <h3 className="text-xl font-semibold tracking-[-0.02em]">{benefit.title}</h3>
                <p className="mt-3 text-sm leading-7 text-zinc-600">{benefit.body}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="border-y border-zinc-200 bg-zinc-50 px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-6xl">
          <div className="grid gap-12 lg:grid-cols-[0.82fr_1.18fr]">
            <div>
              <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">Who this fits</p>
              <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">Agencies that already solve business problems for clients.</h2>
              <p className="mt-5 text-sm leading-7 text-zinc-600">You do not need a huge audience. A focused client base matters more than follower count.</p>
            </div>
            <div className="grid gap-3 sm:grid-cols-2">
              {whoFits.map((item) => (
                <div key={item} className="rounded-2xl border border-zinc-200 bg-white p-5 text-sm font-medium text-zinc-800">{item}</div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-6xl">
          <div className="max-w-3xl">
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">How it works</p>
            <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">Three steps. No reseller complexity.</h2>
          </div>
          <div className="mt-10 grid gap-4 md:grid-cols-3">
            {[
              ['01', 'Apply', 'Tell us what your agency does, who you serve and how many active clients you manage.'],
              ['02', 'Use Cora', 'We enable the partner benefit and you run your own agency workflow inside Cora Professional.'],
              ['03', 'Refer the right clients', 'Share Cora when a client needs better business operations. We track eligible referrals and partner earnings.'],
            ].map(([step, title, body]) => (
              <div key={step} className="rounded-2xl border border-zinc-200 p-6">
                <div className="text-xs font-semibold tracking-[0.18em] text-zinc-400">{step}</div>
                <h3 className="mt-4 text-xl font-semibold">{title}</h3>
                <p className="mt-3 text-sm leading-7 text-zinc-600">{body}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section id="apply" className="border-y border-zinc-200 bg-zinc-950 px-5 py-16 text-white sm:px-8 lg:py-24">
        <div className="mx-auto grid max-w-6xl gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-16">
          <div>
            <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-400">Apply to partner</p>
            <h2 className="mt-4 text-3xl font-semibold tracking-[-0.035em] sm:text-4xl">Tell us about your agency.</h2>
            <p className="mt-5 text-sm leading-7 text-zinc-300">We use this information to understand fit and prepare the right onboarding path. Keep it simple.</p>
          </div>
          <AgencyPartnerApplication />
        </div>
      </section>

      <section className="px-5 py-16 sm:px-8 lg:py-24">
        <div className="mx-auto max-w-4xl">
          <p className="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-500">Partner FAQ</p>
          <div className="mt-7 divide-y divide-zinc-200 border-y border-zinc-200">
            {faqs.map((item) => (
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
