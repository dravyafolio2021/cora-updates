import type { Metadata } from 'next';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { ArrowRight, CheckCircle2 } from 'lucide-react';
import { BLOG_POSTS, getBlogPost, getBlogPostUrl } from '@/lib/blog-data';

type PageProps = { params: Promise<{ category: string; slug: string }> };

export function generateStaticParams() {
  return BLOG_POSTS.map((post) => ({ category: post.category.slug, slug: post.slug }));
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { category, slug } = await params;
  const post = getBlogPost(category, slug);
  if (!post) return { title: 'Article not found', robots: { index: false, follow: false } };

  const url = getBlogPostUrl(post);
  return {
    title: `${post.title} | Cora`,
    description: post.description,
    keywords: [post.eyebrow, `${post.category.name} guides`, 'agency website workflow', 'Cora'],
    alternates: { canonical: url },
    openGraph: {
      title: post.title,
      description: post.description,
      url,
      type: 'article',
      publishedTime: post.publishedAt,
      modifiedTime: post.updatedAt,
    },
  };
}

export default async function BlogArticlePage({ params }: PageProps) {
  const { category, slug } = await params;
  const post = getBlogPost(category, slug);
  if (!post) notFound();

  const relativeUrl = getBlogPostUrl(post);
  const canonical = `https://heycora.in${relativeUrl}`;
  const relatedPosts = BLOG_POSTS.filter((item) => item.slug !== post.slug).slice(0, 3);
  const schemas = [
    {
      '@context': 'https://schema.org',
      '@type': 'BlogPosting',
      '@id': `${canonical}#article`,
      headline: post.title,
      description: post.description,
      datePublished: post.publishedAt,
      dateModified: post.updatedAt,
      mainEntityOfPage: canonical,
      articleSection: post.category.name,
      author: { '@type': 'Organization', '@id': 'https://heycora.in/#organization', name: 'Cora' },
      publisher: { '@id': 'https://heycora.in/#organization' },
      isPartOf: { '@id': 'https://heycora.in/blog/#blog' },
    },
    {
      '@context': 'https://schema.org',
      '@type': 'BreadcrumbList',
      itemListElement: [
        { '@type': 'ListItem', position: 1, name: 'Home', item: 'https://heycora.in/' },
        { '@type': 'ListItem', position: 2, name: 'Blog', item: 'https://heycora.in/blog/' },
        { '@type': 'ListItem', position: 3, name: post.category.name, item: `https://heycora.in/blog/${post.category.slug}/` },
        { '@type': 'ListItem', position: 4, name: post.shortTitle, item: canonical },
      ],
    },
    {
      '@context': 'https://schema.org',
      '@type': 'FAQPage',
      mainEntity: post.faqs.map((faq) => ({
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
            <Link href="/" className="hover:text-zinc-950">Home</Link><span className="mx-2">/</span>
            <Link href="/blog/" className="hover:text-zinc-950">Blog</Link><span className="mx-2">/</span>
            <Link href={`/blog/${post.category.slug}/`} className="hover:text-zinc-950">{post.category.name}</Link><span className="mx-2">/</span>
            <span className="text-zinc-900">{post.shortTitle}</span>
          </nav>

          <Link href={`/blog/${post.category.slug}/`} className="font-mono text-xs font-bold uppercase tracking-[0.16em] text-emerald-700 hover:text-emerald-900">
            {post.category.name}
          </Link>
          <h1 className="mt-4 font-display text-4xl sm:text-6xl font-bold tracking-[-0.04em] leading-[1.07]">{post.title}</h1>
          <p className="mt-6 text-base sm:text-xl leading-relaxed text-zinc-600">{post.summary}</p>
          <div className="mt-7 flex flex-wrap gap-x-5 gap-y-2 text-xs text-zinc-500">
            <time dateTime={post.publishedAt}>Published {post.publishedAt}</time>
            <time dateTime={post.updatedAt}>Updated {post.updatedAt}</time>
            <span>By Cora</span>
          </div>
          <div className="mt-8 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm leading-relaxed text-emerald-950">
            <strong>Who this is for:</strong> {post.intent}
          </div>
        </header>

        <section className="max-w-5xl mx-auto mt-16 px-4 sm:px-6">
          <h2 className="font-display text-3xl font-bold tracking-tight">Why teams start looking for another way</h2>
          <div className="mt-7 grid gap-5 md:grid-cols-3">
            {post.painPoints.map((pain) => (
              <div key={pain.title} className="rounded-3xl border border-zinc-200 bg-zinc-50 p-6">
                <h3 className="font-display text-lg font-bold">{pain.title}</h3>
                <p className="mt-3 text-sm leading-relaxed text-zinc-600">{pain.description}</p>
              </div>
            ))}
          </div>
        </section>

        <div className="max-w-3xl mx-auto mt-16 px-4 sm:px-6">
          {post.sections.map((section) => (
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
              <thead className="bg-zinc-50"><tr><th className="p-5 font-bold">Decision</th><th className="p-5 font-bold">Typical WordPress stack</th><th className="p-5 font-bold text-emerald-800">Cora approach</th></tr></thead>
              <tbody className="divide-y divide-zinc-200">
                {post.comparison.map((row) => (
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
            {post.faqs.map((faq) => (
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
          <h2 className="font-display text-3xl sm:text-4xl font-bold tracking-tight">Improve the workflow before rebuilding the whole site.</h2>
          <p className="mt-4 max-w-2xl text-sm sm:text-base leading-relaxed text-zinc-300">Start with one real enquiry flow and decide what to migrate from evidence.</p>
          <a href={`https://app.heycora.in/workspace/login?source=blog_${post.category.slug}_${post.slug}`} className="mt-7 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-zinc-950 hover:bg-zinc-100">
            Start free <ArrowRight className="h-4 w-4" />
          </a>
        </div>
        <div className="mt-14">
          <h2 className="font-display text-2xl font-bold">Related guides</h2>
          <div className="mt-5 grid gap-4 md:grid-cols-3">
            {relatedPosts.map((related) => (
              <Link key={`${related.category.slug}/${related.slug}`} href={getBlogPostUrl(related)} className="rounded-2xl border border-zinc-200 p-5 hover:border-zinc-400">
                <span className="font-mono text-[10px] font-bold uppercase text-emerald-700">{related.category.name}</span>
                <span className="mt-2 block text-sm font-bold">{related.shortTitle}</span>
                <ArrowRight className="mt-4 h-4 w-4 text-zinc-400" />
              </Link>
            ))}
          </div>
        </div>
      </aside>
    </main>
  );
}
