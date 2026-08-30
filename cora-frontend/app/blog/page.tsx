import type { Metadata } from 'next';
import Link from 'next/link';
import { ArrowRight } from 'lucide-react';
import { BLOG_CATEGORIES, BLOG_POSTS, getBlogPostUrl } from '@/lib/blog-data';

export const metadata: Metadata = {
  title: 'Cora Blog — Websites, Workflows, SEO & AI Search',
  description: 'Practical guides for service businesses and agencies improving their websites, client workflows, content publishing, SEO, and AI-search visibility.',
  alternates: { canonical: '/blog/' },
  openGraph: {
    title: 'Cora Blog — Build a Website That Helps Run the Business',
    description: 'Practical, candid guidance for service agencies building connected websites and workflows.',
    url: '/blog/',
    type: 'website',
  },
};

export default function BlogPage() {
  const schema = {
    '@context': 'https://schema.org',
    '@type': 'Blog',
    '@id': 'https://heycora.in/blog/#blog',
    url: 'https://heycora.in/blog/',
    name: 'Cora Blog',
    description: metadata.description,
    publisher: { '@id': 'https://heycora.in/#organization' },
    blogPost: BLOG_POSTS.map((post) => ({
      '@type': 'BlogPosting',
      headline: post.title,
      url: `https://heycora.in${getBlogPostUrl(post)}`,
    })),
  };

  return (
    <main className="min-h-screen bg-white pt-32 sm:pt-40 pb-24 text-zinc-950">
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }} />
      <header className="max-w-5xl mx-auto px-4 sm:px-6">
        <p className="font-mono text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">Cora Blog</p>
        <h1 className="mt-4 max-w-4xl font-display text-4xl sm:text-6xl font-bold tracking-[-0.04em] leading-[1.07]">
          Practical ideas for websites that do more than sit online.
        </h1>
        <p className="mt-6 max-w-3xl text-base sm:text-xl leading-relaxed text-zinc-600">
          Guides for service businesses and agencies working through WordPress frustration, content operations,
          client workflows, SEO, and visibility in AI-powered search.
        </p>
      </header>

      <section className="max-w-5xl mx-auto mt-16 px-4 sm:px-6">
        <h2 className="font-display text-2xl font-bold tracking-tight">Browse by category</h2>
        <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {BLOG_CATEGORIES.map((category) => (
            <Link key={category.slug} href={`/blog/${category.slug}/`} className="rounded-2xl border border-zinc-200 p-5 hover:border-zinc-400 transition-colors">
              <h3 className="font-display text-lg font-bold">{category.name}</h3>
              <p className="mt-2 text-xs leading-relaxed text-zinc-600">{category.description}</p>
              <ArrowRight className="mt-5 h-4 w-4 text-zinc-400" />
            </Link>
          ))}
        </div>
      </section>

      <section className="max-w-5xl mx-auto mt-20 px-4 sm:px-6">
        <h2 className="font-display text-3xl font-bold tracking-tight">Latest guides</h2>
        <div className="mt-7 grid gap-6 md:grid-cols-2">
          {BLOG_POSTS.map((post) => (
            <article key={`${post.category.slug}/${post.slug}`} className="flex flex-col rounded-3xl border border-zinc-200 p-6 sm:p-8 shadow-sm">
              <Link href={`/blog/${post.category.slug}/`} className="font-mono text-xs font-bold uppercase tracking-wider text-emerald-700 hover:text-emerald-900">
                {post.category.name}
              </Link>
              <h2 className="mt-4 font-display text-2xl font-bold tracking-tight">{post.title}</h2>
              <p className="mt-3 flex-1 text-sm leading-relaxed text-zinc-600">{post.description}</p>
              <Link href={getBlogPostUrl(post)} className="mt-7 inline-flex items-center gap-2 text-sm font-bold hover:text-emerald-700">
                Read article <ArrowRight className="h-4 w-4" />
              </Link>
            </article>
          ))}
        </div>
      </section>
    </main>
  );
}
