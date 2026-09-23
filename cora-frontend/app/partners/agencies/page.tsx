import type { Metadata } from 'next';
import Image from 'next/image';
import Link from 'next/link';
import {
  ArrowRight,
  BadgePercent,
  BriefcaseBusiness,
  Check,
  CircleDollarSign,
  Gift,
  Network,
  Sparkles,
  Users,
} from 'lucide-react';
import { AgencyPartnerApplication } from './AgencyPartnerApplication';

const url = 'https://heycora.in/partners/agencies/';

export const metadata: Metadata = {
  title: 'Cora Agency Partner Program — Free Pro Access + 25–30% Commission',
  description:
    'Join the Cora Agency Partner Program. Run your agency on Cora Professional at no cost and earn 25–30% commission on eligible client referrals.',
  alternates: { canonical: url },
  openGraph: {
    title: 'Cora Agency Partner Program',
    description:
      'Use Cora Professional for your agency at no cost and earn 25–30% commission on eligible client referrals.',
    url,
    siteName: 'Cora',
    type: 'website',
  },
};

const agencyTypes = [
  'Performance Marketing',
  'Web & Shopify Development',
  'Branding & Design',
  'SEO & Content',
  'Automation & Implementation',
  'Creative Studios',
];

const faqs = [
  {
    q: 'Who can join the Cora Agency Partner Program?',
    a: 'We built the program for agencies and consultants that already work with service businesses, including performance marketing, development, design, SEO, automation and creative agencies.',
  },
  {
    q: 'Do agency partners pay for their own Cora workspace?',
    a: 'Approved active agency partners can use Cora Professional for their own agency at no cost, subject to the current partner terms.',
  },
  {
    q: 'How much commission can a partner earn?',
    a: 'Cora pays 25% to 30% commission on eligible referred customers. Your rate depends on your partner tier and the current program terms.',
  },
  {
    q: 'Do I need to sell Cora to every client?',
    a: 'No. Use Cora first. Recommend it only where it solves a real client problem. We want product-led referrals, not forced selling.',
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
    <main className="w-full overflow-hidden bg-white text-zinc-900">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }}
      />

      {/* Hero */}
      <section className="relative overflow-hidden bg-gradient-to-b from-[#71b7ef] via-[#dceefe] to-white pt-28 sm:pt-36 pb-20 sm:pb-28">
        <div className="absolute inset-0 pointer-events-none select-none">
          <Image
            src="/images/cora_pricing_pure_sky.jpg"
            alt=""
            fill
            priority
            className="object-cover object-top opacity-90"
            sizes="100vw"
          />
          <div className="absolute inset-0 bg-[linear-gradient(180deg,rgba(255,255,255,0.05)_0%,rgba(255,255,255,0.18)_42%,rgba(255,255,255,0.96)_88%,#fff_100%)]" />
        </div>

        <div className="relative z-10 mx-auto w-full max-w-[1240px] px-4 sm:px-6">
          <div className="grid items-center gap-10 lg:grid-cols-[1.06fr_0.94fr] lg:gap-16">
            <div>
              <div className="inline-flex items-center gap-2 rounded-full border border-white/80 bg-white/90 px-3.5 py-1.5 text-[11px] font-semibold text-zinc-800 shadow-sm backdrop-blur-md">
                <span className="h-2 w-2 rounded-full bg-emerald-500" />
                <span>CORA AGENCY PARTNER PROGRAM</span>
              </div>

              <h1 className="mt-6 max-w-[780px] font-display text-4xl font-bold leading-[1.06] tracking-[-0.04em] text-zinc-950 sm:text-5xl md:text-6xl lg:text-[68px]">
                Use Cora Pro free.
                <br />
                <span className="text-zinc-500">Earn when your clients use it.</span>
              </h1>

              <p className="mt-6 max-w-[650px] text-sm leading-7 text-zinc-600 sm:text-base md:text-lg">
                Run your own agency on Cora Professional at no cost. Learn the product through real work, then recommend it when it fits a client and earn 25–30% commission on eligible referrals.
              </p>

              <div className="mt-8 flex flex-wrap gap-3">
                <a
                  href="#apply"
                  className="inline-flex items-center gap-2 rounded-xl bg-zinc-950 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800"
                >
                  Apply as Partner
                  <ArrowRight className="h-4 w-4" />
                </a>
                <Link
                  href="/agency-management-software-india/"
                  className="inline-flex items-center gap-2 rounded-xl border border-zinc-300 bg-white/90 px-5 py-3 text-sm font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white"
                >
                  Explore Cora
                </Link>
              </div>

              <div className="mt-6 flex flex-wrap gap-x-5 gap-y-2 text-xs text-zinc-600">
                {['No joining fee', 'Free Professional access', '25–30% referral commission'].map((item) => (
                  <span key={item} className="inline-flex items-center gap-1.5">
                    <Check className="h-3.5 w-3.5 text-emerald-600" /> {item}
                  </span>
                ))}
              </div>
            </div>

            <div className="relative mx-auto w-full max-w-[520px] lg:mx-0">
              <div className="absolute -inset-4 rounded-[36px] bg-white/35 blur-2xl" />
              <div className="relative rounded-[30px] border border-white/80 bg-white/88 p-5 shadow-[0_28px_90px_rgba(37,99,235,0.14)] backdrop-blur-xl sm:p-6">
                <div className="flex items-center justify-between border-b border-zinc-200/80 pb-4">
                  <div>
                    <div className="text-[11px] font-semibold uppercase tracking-[0.14em] text-zinc-400">Partner Workspace</div>
                    <div className="mt-1 text-lg font-bold tracking-[-0.02em] text-zinc-950">Agency Growth Layer</div>
                  </div>
                  <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-950 text-white">
                    <Network className="h-5 w-5" />
                  </div>
                </div>

                <div className="mt-4 grid gap-3 sm:grid-cols-2">
                  <div className="rounded-2xl border border-zinc-200 bg-zinc-50 p-4">
                    <div className="flex items-center gap-2 text-xs font-medium text-zinc-500">
                      <Gift className="h-4 w-4" /> Your Cora Pro
                    </div>
                    <div className="mt-3 text-3xl font-bold tracking-[-0.04em] text-zinc-950">₹0</div>
                    <div className="mt-1 text-xs leading-5 text-zinc-500">for approved active agency partners</div>
                  </div>

                  <div className="rounded-2xl border border-zinc-200 bg-zinc-50 p-4">
                    <div className="flex items-center gap-2 text-xs font-medium text-zinc-500">
                      <BadgePercent className="h-4 w-4" /> Commission
                    </div>
                    <div className="mt-3 text-3xl font-bold tracking-[-0.04em] text-zinc-950">25–30%</div>
                    <div className="mt-1 text-xs leading-5 text-zinc-500">on eligible paying referrals</div>
                  </div>
                </div>

                <div className="mt-3 rounded-2xl bg-zinc-950 p-5 text-white">
                  <div className="flex items-center gap-2 text-xs font-medium text-zinc-400">
                    <Sparkles className="h-4 w-4" /> The model
                  </div>
                  <div className="mt-3 text-xl font-semibold tracking-[-0.02em]">Use it → trust it → refer it.</div>
                  <p className="mt-2 text-xs leading-6 text-zinc-400">
                    Your agency gets value before you ever introduce Cora to a client.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Value */}
      <section className="px-4 py-16 sm:px-6 sm:py-20">
        <div className="mx-auto max-w-[1240px]">
          <div className="mx-auto max-w-[720px] text-center">
            <div className="text-[11px] font-semibold uppercase tracking-[0.16em] text-zinc-400">WHY PARTNER WITH CORA</div>
            <h2 className="mt-4 font-display text-3xl font-bold tracking-[-0.035em] text-zinc-950 sm:text-4xl md:text-5xl">
              One product. Two ways to create value.
            </h2>
            <p className="mt-4 text-sm leading-7 text-zinc-600 sm:text-base">
              Improve your own agency operations first. Then create a new recurring revenue stream when Cora fits a client.
            </p>
          </div>

          <div className="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            {[
              [BriefcaseBusiness, 'Run your agency', 'Manage leads, proposals, clients, projects and billing from one connected workspace.'],
              [Gift, 'Get Pro free', 'Approved active partners use Cora Professional for their own agency without a subscription fee.'],
              [CircleDollarSign, 'Earn recurring income', 'Earn 25–30% commission when eligible referred clients become paying Cora customers.'],
              [Users, 'Keep client trust', 'You stay the advisor. Recommend Cora only when it solves a real operational problem.'],
            ].map(([Icon, title, body]) => {
              const IconComponent = Icon as typeof BriefcaseBusiness;
              return (
                <div key={title as string} className="rounded-3xl border border-zinc-200 bg-white p-6 shadow-[0_12px_35px_rgba(0,0,0,0.035)]">
                  <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-950">
                    <IconComponent className="h-5 w-5" />
                  </div>
                  <h3 className="mt-5 text-lg font-bold tracking-[-0.02em] text-zinc-950">{title as string}</h3>
                  <p className="mt-2 text-sm leading-6 text-zinc-600">{body as string}</p>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Fit */}
      <section className="border-y border-zinc-200/80 bg-[#fbfbfc] px-4 py-16 sm:px-6 sm:py-20">
        <div className="mx-auto grid max-w-[1240px] gap-10 lg:grid-cols-[0.72fr_1.28fr] lg:items-start">
          <div className="max-w-[460px]">
            <div className="text-[11px] font-semibold uppercase tracking-[0.16em] text-zinc-400">BUILT FOR AGENCIES</div>
            <h2 className="mt-4 font-display text-3xl font-bold tracking-[-0.035em] text-zinc-950 sm:text-4xl">
              If you already solve client problems, you can become a distribution partner.
            </h2>
            <p className="mt-4 text-sm leading-7 text-zinc-600">
              You do not need a huge following. A focused client base and real product usage matter more.
            </p>
          </div>

          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {agencyTypes.map((item, index) => (
              <div key={item} className="rounded-2xl border border-zinc-200 bg-white p-5">
                <div className="text-[10px] font-mono font-semibold text-zinc-400">0{index + 1}</div>
                <div className="mt-3 text-sm font-semibold text-zinc-900">{item}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Process */}
      <section className="px-4 py-16 sm:px-6 sm:py-20">
        <div className="mx-auto max-w-[1240px]">
          <div className="max-w-[680px]">
            <div className="text-[11px] font-semibold uppercase tracking-[0.16em] text-zinc-400">HOW IT WORKS</div>
            <h2 className="mt-4 font-display text-3xl font-bold tracking-[-0.035em] text-zinc-950 sm:text-4xl">
              Start as a user. Grow as a partner.
            </h2>
          </div>

          <div className="mt-9 grid gap-4 md:grid-cols-3">
            {[
              ['01', 'Apply', 'Tell us what your agency does, who you serve and how many active clients you manage.'],
              ['02', 'Run Cora yourself', 'We enable the partner benefit and your team starts using Cora Professional inside your own agency.'],
              ['03', 'Refer when it fits', 'Introduce Cora to clients who need stronger business operations. We track eligible referrals and earnings.'],
            ].map(([step, title, body]) => (
              <div key={step} className="rounded-3xl border border-zinc-200 bg-white p-6 sm:p-7">
                <div className="flex h-9 w-9 items-center justify-center rounded-full bg-zinc-950 text-xs font-semibold text-white">{step}</div>
                <h3 className="mt-5 text-xl font-bold tracking-[-0.02em] text-zinc-950">{title}</h3>
                <p className="mt-3 text-sm leading-7 text-zinc-600">{body}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Application */}
      <section id="apply" className="px-4 pb-16 sm:px-6 sm:pb-20">
        <div className="mx-auto max-w-[1240px] overflow-hidden rounded-[34px] bg-zinc-950 text-white shadow-[0_28px_80px_rgba(0,0,0,0.12)]">
          <div className="grid gap-0 lg:grid-cols-[0.8fr_1.2fr]">
            <div className="relative overflow-hidden p-7 sm:p-10 lg:p-12">
              <div className="absolute -left-20 -top-20 h-72 w-72 rounded-full bg-blue-500/15 blur-3xl" />
              <div className="relative z-10">
                <div className="text-[11px] font-semibold uppercase tracking-[0.16em] text-zinc-500">PARTNER APPLICATION</div>
                <h2 className="mt-4 font-display text-3xl font-bold tracking-[-0.035em] sm:text-4xl">
                  Tell us about your agency.
                </h2>
                <p className="mt-4 max-w-[430px] text-sm leading-7 text-zinc-400">
                  We review the fit, activate your partner benefits and help you set up Cora around your existing workflow.
                </p>

                <div className="mt-8 space-y-3 text-sm text-zinc-300">
                  {['No joining fee', 'No forced sales target', 'Founder-led onboarding for early partners'].map((item) => (
                    <div key={item} className="flex items-center gap-2">
                      <Check className="h-4 w-4 text-emerald-400" />
                      {item}
                    </div>
                  ))}
                </div>
              </div>
            </div>

            <div className="bg-white/5 p-5 sm:p-7 lg:p-8">
              <AgencyPartnerApplication />
            </div>
          </div>
        </div>
      </section>

      {/* FAQ */}
      <section className="border-t border-zinc-200/80 px-4 py-16 sm:px-6 sm:py-20">
        <div className="mx-auto max-w-[860px]">
          <div className="text-center">
            <div className="text-[11px] font-semibold uppercase tracking-[0.16em] text-zinc-400">PARTNER FAQ</div>
            <h2 className="mt-4 font-display text-3xl font-bold tracking-[-0.035em] text-zinc-950 sm:text-4xl">Simple terms. Clear expectations.</h2>
          </div>

          <div className="mt-9 divide-y divide-zinc-200 border-y border-zinc-200">
            {faqs.map((item) => (
              <div key={item.q} className="py-6">
                <h3 className="text-base font-bold text-zinc-950 sm:text-lg">{item.q}</h3>
                <p className="mt-2 text-sm leading-7 text-zinc-600">{item.a}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
    </main>
  );
}
