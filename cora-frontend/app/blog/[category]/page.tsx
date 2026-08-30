import type { Metadata } from 'next';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { ArrowRight } from 'lucide-react';
import { BLOG_CATEGORIES, getBlogCategory, getBlogPostUrl, getPostsForCategory } from '@/lib/blog-data';

type PageProps = { params: Promise<{ category: string }> };

export function generateStaticParams() {
  return BLOG_CATEGORIES.map(({ slug }) => ({ category: slug }));
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { category: slug } = await params;
  const category = getBlogCategory(slug);
  if (!category) return { title: 'Blog category not found', robots: { index: false, follow: false } };
  return {
    title: `${category.name} Guides — Cora Blog`,
    description: category.description,
    alternates: { canonical: `/blog/${category.slug}/` },
    openGraph: { title: `${category.name} Guides — Cora Blog`, description: category.description, url: `/blog/${category.slug}/`, type: 'website' },
  };
}

export default async function BlogCategoryPage({ params }: PageProps) {
  const { category: slug } = await params;
  const category = getBlogCategory(slug);
  if (!category) notFound();
  const posts = getPostsForCategory(slug);

  const schema = {
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    url: `https://heycora.in/blog/${category.slug}/`,
    name: `${category.name} Guides`,
    description: category.description,
    hasPart: posts.map((post) => ({ '@type': 'BlogPosting', headline: post.title, url: `https://heycora.in${getBlogPostUrl(post)}` })),
  };

  return (
    <main className="min-h-screen bg-white pt-32 sm:pt-40 pb-24 text-zinc-950">
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }} />
      <header className="max-w-5xl mx-auto px-4 sm:px-6">
        <nav className="text-xs text-zinc-500" aria-label="Breadcrumb">
          <Link href="/" className="hover:text-zinc-950">Home</Link><span className="mx-2">/</span>
          <Link href="/blog/" className="hover:text-zinc-950">Blog</Link><span className="mx-2">/</span>
          <span className="text-zinc-900">{category.name}</span>
        </nav>
        <h1 className="mt-8 font-display text-4xl sm:text-6xl font-bold tracking-[-0.04em]">{category.name} guides</h1>
        <p className="mt-5 max-w-3xl text-base sm:text-xl leading-relaxed text-zinc-600">{category.description}</p>
      </header>

      <section className="max-w-5xl mx-auto mt-14 px-4 sm:px-6">
        <div className="grid gap-6 md:grid-cols-2">
          {posts.map((post) => (
            <article key={post.slug} className="rounded-3xl border border-zinc-200 p-6 sm:p-8">
              <time className="font-mono text-xs text-zinc-500" dateTime={post.publishedAt}>{post.publishedAt}</time>
              <h2 className="mt-4 font-display text-2xl font-bold tracking-tight">{post.title}</h2>
              <p className="mt-3 text-sm leading-relaxed text-zinc-600">{post.description}</p>
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
