import type { Metadata } from 'next';
import { getAllGuides, Guide } from '@/lib/guides-data';
import { fetchContentEntries, adaptCmsEntryToGuide } from '@/lib/content-api';
import { GuidesHubClientView } from '@/components/guides/GuidesHubClientView';

const url = 'https://heycora.in/guides/';

export const metadata: Metadata = {
  title: 'Cora Guides & Playbooks — Operating Frameworks for Agencies',
  description:
    'Chaptered digital books and operating playbooks to onboard clients, eliminate scope creep, protect margins, and automate daily agency operations.',
  alternates: { canonical: url },
  openGraph: {
    title: 'Cora Guides & Playbooks — Operating Frameworks for Agencies',
    description:
      'Chaptered digital books and operating playbooks to onboard clients, eliminate scope creep, protect margins, and automate daily agency operations.',
    url,
    siteName: 'Cora',
    type: 'website',
  },
};

export default async function GuidesHubPage() {
  const staticGuides = getAllGuides(false);
  const growthEntries = await fetchContentEntries({ type: 'guide', status: 'published' }).catch(() => []);
  const adaptedGrowth = growthEntries.map(adaptCmsEntryToGuide);

  const cmsSlugSet = new Set(adaptedGrowth.map((g) => g.slug));
  const allGuides: Guide[] = [
    ...adaptedGrowth,
    ...staticGuides.filter((g) => !cmsSlugSet.has(g.slug)),
  ];

  const featured = allGuides.find((g) => g.featured) || allGuides[0];

  const collectionSchema = {
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    name: 'Cora Guides & Playbooks',
    description: 'Chaptered digital books and operating playbooks for agencies and creative studios.',
    url,
    publisher: {
      '@type': 'Organization',
      name: 'Cora',
      url: 'https://heycora.in',
      logo: 'https://heycora.in/favicon.png',
    },
    hasPart: allGuides.map((guide) => ({
      '@type': 'CreativeWork',
      name: guide.title,
      description: guide.dek || guide.excerpt,
      url: `https://heycora.in/guides/${guide.slug}/`,
    })),
  };

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(collectionSchema) }}
      />
      <GuidesHubClientView initialGuides={allGuides} initialFeaturedGuide={featured} />
    </>
  );
}
