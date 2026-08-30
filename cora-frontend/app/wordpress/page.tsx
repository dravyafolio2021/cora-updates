import type { Metadata } from 'next';
import Link from 'next/link';
import { ArrowRight, CheckCircle2 } from 'lucide-react';
import { WORDPRESS_CONTENT } from '@/lib/wordpress-content';

export const metadata: Metadata = {
  title: 'WordPress Alternatives & Workflows for Service Agencies | Cora',
  description:
    'Practical guides for agencies frustrated by WordPress, Elementor, WooCommerce, plugin maintenance, and manual content publishing.',
  alternates: { canonical: '/wordpress/' },
  openGraph: {
    title: 'When an Agency Website Needs to Do More Than Sit Online',
    description:
      'Compare WordPress, Elementor, and WooCommerce workflows with a connected website and business workspace.',
    url: '/wordpress/',
    type: 'website',
  },
};

const schema = {
  '@context': 'https://schema.org',
  '@type': 'CollectionPage',
  '@id': 'https://heycora.in/wordpress/#page',
  url: 'https://heycora.in/wordpress/',
  name: 'WordPress alternatives and workflows for service agencies',
  description: metadata.description,
  isPartOf: { '@id': 'https://heycora.in/#website' },
  about: [
    { '@type': 'SoftwareApplication', name: 'WordPress' },
    { '@type': 'SoftwareApplication', name: 'Elementor' },
    { '@type': 'SoftwareApplication', name: 'WooCommerce' },
  ],
  hasPart: WORDPRESS_CONTENT.map((page) => ({
    '@type': 'Article',
    name: page.title,
    url: `https://heycora.in/wordpress/${page.slug}/`,
  })),
};

export default function WordPressSearchHub() {
  return (
    <main className="min-h-screen bg-white pt-32 sm:pt-40 pb-24 text-zinc-950">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
      />

      <section className="max-w-5xl mx-auto px-4 sm:px-6">
        <nav aria-label="Breadcrumb" className="mb-8 text-xs text-zinc-500">
          <Link href="/" className="hover:text-zinc-950">Home</Link>
          <span className="mx-2">/</span>
          <span className="text-zinc-900">WordPress guides</span>
        </nav>

        <div className="max-w-4xl">
          <p className="font-mono text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">
            WordPress, Elementor &amp; WooCommerce for agencies
          </p>
          <h1 className="mt-4 font-display text-4xl sm:text-6xl font-bold tracking-[-0.04em] leading-[1.07]">
            Your website should help run the business—not create more admin.
          </h1>
          <p className="mt-6 max-w-3xl text-base sm:text-xl leading-relaxed text-zinc-600">
            WordPress can publish pages. The frustration begins when publishing stops, plugins multiply,
            and every enquiry has to be copied into a separate operating stack. These guides help service
            and creative agencies decide what to keep, what to connect, and what to simplify.
          </p>
        </div>

        <div className="mt-10 grid gap-3 sm:grid-cols-3">
          {[
            'No forced website migration',
            'Written for service workflows',
            'Clear comparisons, not feature hype',
          ].map((item) => (
            <div key={item} className="flex items-center gap-2 rounded-2xl border border-zinc-200 bg-zinc-50 p-4 text-sm font-semibold">
              <CheckCircle2 className="h-4 w-4 shrink-0 text-emerald-600" />
              {item}
            </div>
          ))}
        </div>
      </section>

      <section className="max-w-5xl mx-auto mt-20 px-4 sm:px-6">
        <div className="mb-8 max-w-2xl">
          <h2 className="font-display text-3xl font-bold tracking-tight">Start with the bottleneck you actually have</h2>
          <p className="mt-3 text-zinc-600 leading-relaxed">
            Each page answers a different search and buying question. Choose the one closest to the
            problem your team feels today.
          </p>
        </div>

        <div className="grid gap-6 md:grid-cols-2">
          {WORDPRESS_CONTENT.map((page, index) => (
            <article key={page.slug} className="flex flex-col rounded-3xl border border-zinc-200 bg-white p-6 sm:p-8 shadow-sm">
              <div className="font-mono text-xs font-bold text-zinc-400">0{index + 1}</div>
              <h2 className="mt-4 font-display text-2xl font-bold tracking-tight">{page.shortTitle}</h2>
              <p className="mt-3 flex-1 text-sm leading-relaxed text-zinc-600">{page.description}</p>
              <Link
                href={`/wordpress/${page.slug}/`}
                className="mt-7 inline-flex items-center gap-2 text-sm font-bold text-zinc-950 hover:text-emerald-700"
              >
                Read the guide <ArrowRight className="h-4 w-4" />
              </Link>
            </article>
          ))}
        </div>
      </section>

      <section className="max-w-5xl mx-auto mt-20 px-4 sm:px-6">
        <div className="rounded-[32px] bg-zinc-950 p-8 sm:p-12 text-white">
          <p className="font-mono text-xs font-bold uppercase tracking-[0.16em] text-emerald-400">The practical starting point</p>
          <h2 className="mt-4 max-w-2xl font-display text-3xl sm:text-4xl font-bold tracking-tight">
            Keep the website. Fix what happens around it first.
          </h2>
          <p className="mt-4 max-w-2xl text-sm sm:text-base leading-relaxed text-zinc-300">
            Connect the enquiry and client workflow before deciding whether a full website migration is
            worth it. That produces evidence quickly and protects what already works.
          </p>
          <a
            href="https://app.heycora.in/workspace/login?source=wordpress_search_hub"
            className="mt-7 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-zinc-950 hover:bg-zinc-100"
          >
            Start a free workspace <ArrowRight className="h-4 w-4" />
          </a>
        </div>
      </section>
    </main>
  );
}
