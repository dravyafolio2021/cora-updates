import { permanentRedirect } from 'next/navigation';
import { INDUSTRY_WORKSPACES } from '@/lib/industry-data';

export async function generateStaticParams() {
  return INDUSTRY_WORKSPACES.map((item) => ({
    slug: item.slug,
  }));
}

export default async function UseCasesSlugRedirectPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  permanentRedirect(`/industries/${slug}`);
}
