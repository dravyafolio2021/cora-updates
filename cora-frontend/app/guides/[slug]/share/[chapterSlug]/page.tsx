import type { Metadata } from 'next';
import { notFound, redirect } from 'next/navigation';
import { getGuideBySlug, getAllGuideSlugs } from '@/lib/guides-data';
import Link from 'next/link';
import { BookOpen, ArrowRight, Loader2 } from 'lucide-react';

interface ChapterSharePageProps {
  params: Promise<{ slug: string; chapterSlug: string }>;
}

export const dynamicParams = false;

export async function generateStaticParams() {
  const guideSlugs = getAllGuideSlugs(true);
  const paths: { slug: string; chapterSlug: string }[] = [];

  for (const slug of guideSlugs) {
    const guide = getGuideBySlug(slug, true);
    if (guide && guide.chapters) {
      for (const ch of guide.chapters) {
        paths.push({
          slug,
          chapterSlug: ch.slug,
        });
      }
    }
  }

  return paths;
}

export async function generateMetadata({ params }: ChapterSharePageProps): Promise<Metadata> {
  const { slug, chapterSlug } = await params;
  const guide = getGuideBySlug(slug, true);

  if (!guide) {
    return { title: 'Chapter Not Found' };
  }

  const chapter = guide.chapters.find((ch) => ch.slug === chapterSlug) || guide.chapters[0];
  const shareTitle = `${chapter.title} — Chapter ${chapter.number} | Cora Playbooks`;
  const shareDescription = chapter.summary || guide.dek || guide.excerpt;
  const shareImage = chapter.featuredImage || guide.ogImage || guide.coverImage;
  const canonicalUrl = `https://heycora.in/guides/${guide.slug}/`;

  return {
    title: shareTitle,
    description: shareDescription,
    alternates: {
      canonical: canonicalUrl,
    },
    robots: {
      index: false,
      follow: true,
    },
    openGraph: {
      title: shareTitle,
      description: shareDescription,
      url: `https://heycora.in/guides/${slug}/share/${chapterSlug}`,
      siteName: 'Cora',
      type: 'article',
      images: [
        {
          url: shareImage,
          width: 1200,
          height: 630,
          alt: `${chapter.title} — Cora Digital Playbook`,
        },
      ],
    },
    twitter: {
      card: 'summary_large_image',
      title: shareTitle,
      description: shareDescription,
      images: [shareImage],
      creator: '@heycora_in',
      site: '@heycora_in',
    },
  };
}

export default async function ChapterShareRedirectPage({ params }: ChapterSharePageProps) {
  const { slug, chapterSlug } = await params;
  const guide = getGuideBySlug(slug, true);

  if (!guide) {
    notFound();
  }

  const destinationUrl = `/guides/${slug}/#${chapterSlug}`;

  return (
    <div className="min-h-screen bg-white flex items-center justify-center p-6 text-center font-sans">
      <div className="max-w-md space-y-4 animate-in fade-in duration-300">
        <div className="w-12 h-12 rounded-2xl bg-zinc-950 text-white flex items-center justify-center mx-auto shadow-md">
          <BookOpen className="w-6 h-6 stroke-[2]" />
        </div>

        <h1 className="font-display text-xl font-bold text-zinc-950">
          Opening Digital Playbook...
        </h1>

        <p className="text-xs text-zinc-600">
          Taking you straight into Chapter {guide.chapters.find((c) => c.slug === chapterSlug)?.number || ''}:{' '}
          {guide.chapters.find((c) => c.slug === chapterSlug)?.title || guide.title}
        </p>

        <div className="pt-3">
          <Link
            href={destinationUrl}
            className="inline-flex items-center gap-2 bg-zinc-950 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-colors shadow-sm"
          >
            <span>Click here if not redirected automatically</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </Link>
        </div>

        {/* Client-side immediate redirect */}
        <script
          dangerouslySetInnerHTML={{
            __html: `window.location.replace("${destinationUrl}");`,
          }}
        />
      </div>
    </div>
  );
}
