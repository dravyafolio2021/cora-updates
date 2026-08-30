import type { Metadata } from 'next';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { ArrowRight, CheckCircle2 } from 'lucide-react';
import { WORDPRESS_CONTENT, WORDPRESS_CONTENT_BY_SLUG } from '@/lib/wordpress-content';

type PageProps = { params: Promise<{ slug: string }> };

export function generateStaticParams() {
  return WORDPRESS_CONTENT.map(({ slug }) => ({ slug }));
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;
  const page = WORDPRESS_CONTENT_BY_SLUG[slug];

  if (!page) return { title: 'Guide not found | Cora', robots: { index: false, follow: false } };

  const url = `/wordpress/${page.slug}/`;
  return {
    title: `${page.title} | Cora`,
    description: page.description,
    keywords: [page.eyebrow, 'WordPress for agencies', 'agency website workflow', 'Cora'],
    alternates: { canonical: url },
    openGraph: {
      title: page.title,
      description: page.description,
      url,
      type: 'article',
      publishedTime: page.publishedAt,
      modifiedTime: page.updatedAt,
    },
  };
}

export default async function WordPressSearchPage({ params }: PageProps) {
  const { slug } = await params;
  const page = WORDPRESS_CONTENT_BY_SLUG[slug];
  if (!page) notFound();

  const canonical = `https://heycora.in/wordpress/${page.slug}/`;
  const relatedPages = WORDPRESS_CONTENT.filter((item) => item.slug !== page.slug).slice(0, 3);
  const schemas = [
    {
      '@context': 'https://schema.org',
      '@type': 'Article',
      '@id': `${canonical}#article`,
      headline: page.title,
      description: page.description,
      datePublished: page.publishedAt,
      dateModified: page.updatedAt,
      mainEntityOfPage: canonical,
      author: { '@type': 'Organization', '@id': 'https://heycora.in/#organization', name: 'Cora' },
      publisher: { '@id': 'https://heycora.in/#organization' },
      about: ['WordPress', 'Elementor', 'WooCommerce', 'Service agencies'],
    },
    {
      '@context': 'https://schema.org',
      '@type': 'BreadcrumbList',
      itemListElement: [
        { '@type': 'ListItem', position: 1, name: 'Home', item: 'https://heycora.in/' },
        { '@type': 'ListItem', position: 2, name: 'WordPress guides', item: 'https://heycora.in/wordpress/' },
        { '@type': 'ListItem', position: 3, name: page.shortTitle, item: canonical },
      ],
    },
    {
      '@context': 'https://schema.org',
      '@type': 'FAQPage',
      mainEntity: page.faqs.map((faq) => ({
        '@type': 'Question',
        name: faq.question,
        acceptedAnswer: { '@type': 'Answer', text: faq.answer },
      })),
    },
  ];

  return (
    <main className="min-h-screen bg-white pt-32 sm:pt-40 pb-24 text-zinc-950">
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schemas) }} />

      <article>
        <header className="max-w-4xl mx-auto px-4 sm:px-6">
          <nav aria-label="Breadcrumb" className="mb-8 text-xs text-zinc-500">
            <Link href="/" className="hover:text-zinc-950">Home</Link>
            <span className="mx-2">/</span>
            <Link href="/wordpress/" className="hover:text-zinc-950">WordPress guides</Link>
            <span className="mx-2">/</span>
            <span className="text-zinc-900">{page.shortTitle}</span>
          </nav>

          <p className="font-mono text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">{page.eyebrow}</p>
          <h1 className="mt-4 font-display text-4xl sm:text-6xl font-bold tracking-[-0.04em] leading-[1.07]">{page.title}</h1>
          <p className="mt-6 text-base sm:text-xl leading-relaxed text-zinc-600">{page.summary}</p>
          <div className="mt-7 flex flex-wrap gap-x-5 gap-y-2 text-xs text-zinc-500">
            <span>Published {page.publishedAt}</span>
            <span>Updated {page.updatedAt}</span>
            <span>By Cora</span>
          </div>
          <div className="mt-8 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm leading-relaxed text-emerald-950">
            <strong>Who this is for:</strong> {page.intent}
          </div>
        </header>

        <section className="max-w-5xl mx-auto mt-16 px-4 sm:px-6">
          <h2 className="font-display text-3xl font-bold tracking-tight">Why teams start looking for another way</h2>
          <div className="mt-7 grid gap-5 md:grid-cols-3">
            {page.painPoints.map((pain) => (
              <div key={pain.title} className="rounded-3xl border border-zinc-200 bg-zinc-50 p-6">
                <h3 className="font-display text-lg font-bold">{pain.title}</h3>
                <p className="mt-3 text-sm leading-relaxed text-zinc-600">{pain.description}</p>
              </div>
            ))}
          </div>
        </section>

        <div className="max-w-3xl mx-auto mt-16 px-4 sm:px-6">
          {page.sections.map((section) => (
            <section key={section.heading} className="mb-14">
              <h2 className="font-display text-3xl font-bold tracking-tight">{section.heading}</h2>
              {section.paragraphs.map((paragraph) => (
                <p key={paragraph} className="mt-5 text-base leading-8 text-zinc-700">{paragraph}</p>
              ))}
              {section.bullets && (
                <ul className="mt-6 space-y-3">
                  {section.bullets.map((item) => (
                    <li key={item} className="flex gap-3 text-base leading-7 text-zinc-700">
                      <CheckCircle2 className="mt-1 h-5 w-5 shrink-0 text-emerald-600" />
                      <span>{item}</span>
                    </li>
                  ))}
                </ul>
              )}
            </section>
          ))}
        </div>

        <section className="max-w-5xl mx-auto mt-16 px-4 sm:px-6">
          <h2 className="font-display text-3xl font-bold tracking-tight">The practical difference</h2>
          <div className="mt-7 overflow-x-auto rounded-3xl border border-zinc-200">
            <table className="w-full min-w-[680px] border-collapse text-left text-sm">
              <thead className="bg-zinc-50">
                <tr>
                  <th className="p-5 font-bold">Decision</th>
                  <th className="p-5 font-bold">Typical WordPress stack</th>
                  <th className="p-5 font-bold text-emerald-800">Cora approach</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-200">
                {page.comparison.map((row) => (
                  <tr key={row.label} className="align-top">
                    <th className="p-5 font-semibold">{row.label}</th>
                    <td className="p-5 leading-relaxed text-zinc-600">{row.wordpress}</td>
                    <td className="p-5 leading-relaxed text-zinc-700 bg-emerald-50/30">{row.cora}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </section>

        <section className="max-w-3xl mx-auto mt-20 px-4 sm:px-6">
          <h2 className="font-display text-3xl font-bold tracking-tight">Frequently asked questions</h2>
          <div className="mt-7 space-y-4">
            {page.faqs.map((faq) => (
              <div key={faq.question} className="rounded-2xl border border-zinc-200 p-6">
                <h3 className="font-display text-lg font-bold">{faq.question}</h3>
                <p className="mt-3 text-sm leading-relaxed text-zinc-600">{faq.answer}</p>
              </div>
            ))}
          </div>
        </section>
      </article>

      <aside className="max-w-5xl mx-auto mt-20 px-4 sm:px-6">
        <div className="rounded-[32px] bg-zinc-950 p-8 sm:p-12 text-white">
          <h2 className="font-display text-3xl sm:text-4xl font-bold tracking-tight">Improve the workflow before you rebuild the whole site.</h2>
          <p className="mt-4 max-w-2xl text-sm sm:text-base leading-relaxed text-zinc-300">
            Start with a free Cora workspace, connect one real enquiry flow, and decide what to migrate from evidence.
          </p>
          <a href={`https://app.heycora.in/workspace/login?source=wordpress_${page.slug}`} className="mt-7 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-zinc-950 hover:bg-zinc-100">
            Start free <ArrowRight className="h-4 w-4" />
          </a>
        </div>

        <div className="mt-14">
          <h2 className="font-display text-2xl font-bold">Related guides</h2>
          <div className="mt-5 grid gap-4 md:grid-cols-3">
            {relatedPages.map((related) => (
              <Link key={related.slug} href={`/wordpress/${related.slug}/`} className="rounded-2xl border border-zinc-200 p-5 hover:border-zinc-400">
                <span className="text-sm font-bold">{related.shortTitle}</span>
                <ArrowRight className="mt-4 h-4 w-4 text-zinc-400" />
              </Link>
            ))}
          </div>
        </div>
      </aside>
    </main>
  );
}
