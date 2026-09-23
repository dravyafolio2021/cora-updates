import { MetadataRoute } from 'next';
import { BUILT_MODULES } from '@/lib/features-data';
import { DOCS_DATA } from '@/lib/docs-data';
import { ARTICLES_DATA, ARTICLE_CATEGORIES } from '@/lib/articles-data';
import { INDUSTRY_WORKSPACES } from '@/lib/industry-data';
import { COMPARISONS_DATA } from '@/lib/comparisons-data';
import { INTEGRATIONS_LIST } from '@/lib/integrations-data';
import { TOOLS_DATA } from '@/lib/tools-data';

export const dynamic = 'force-static';

export default function sitemap(): MetadataRoute.Sitemap {
  const baseUrl = 'https://heycora.in';
  const now = new Date();

  const articleCategoryUrls = ARTICLE_CATEGORIES.map((category) => ({
    url: `${baseUrl}/articles/${category.id}/`,
    lastModified: now,
    changeFrequency: 'weekly' as const,
    priority: 0.85,
  }));

  const articleUrls = ARTICLES_DATA.map((article) => ({
    url: `${baseUrl}/articles/${article.category}/${article.slug}/`,
    lastModified: article.updatedAt,
    changeFrequency: 'weekly' as const,
    priority: 0.9,
  }));

  const featureUrls = BUILT_MODULES.map((mod) => ({
    url: `${baseUrl}/features/${mod.slug}`,
    lastModified: now,
    changeFrequency: 'weekly' as const,
    priority: 0.9,
  }));

  const docUrls = DOCS_DATA.map((doc) => ({
    url: `${baseUrl}/docs/${doc.slug}`,
    lastModified: now,
    changeFrequency: 'weekly' as const,
    priority: 0.85,
  }));

  const industryUrls = INDUSTRY_WORKSPACES.map((ind) => ({
    url: `${baseUrl}/industries/${ind.slug}`,
    lastModified: now,
    changeFrequency: 'weekly' as const,
    priority: 0.9,
  }));

  const useCaseUrls = INDUSTRY_WORKSPACES.map((uc) => ({
    url: `${baseUrl}/use-cases/${uc.slug}`,
    lastModified: now,
    changeFrequency: 'weekly' as const,
    priority: 0.9,
  }));

  const comparisonUrls = Object.keys(COMPARISONS_DATA).map((slug) => ({
    url: `${baseUrl}/compare/${slug}`,
    lastModified: now,
    changeFrequency: 'weekly' as const,
    priority: 0.85,
  }));

  const integrationUrls = INTEGRATIONS_LIST.map((integ) => ({
    url: `${baseUrl}/integrations/${integ.slug}`,
    lastModified: now,
    changeFrequency: 'weekly' as const,
    priority: 0.9,
  }));

  const toolUrls = TOOLS_DATA.map((tool) => ({
    url: `${baseUrl}/tools/${tool.slug}`,
    lastModified: now,
    changeFrequency: 'weekly' as const,
    priority: 0.85,
  }));

  const staticHubPages = [
    { url: baseUrl, lastModified: now, changeFrequency: 'daily' as const, priority: 1.0 },
    { url: `${baseUrl}/cora-ai/`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.98 },
    { url: `${baseUrl}/agency-management-software-india/`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.95 },
    { url: `${baseUrl}/partners/agencies/`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.95 },
    { url: `${baseUrl}/newsletter/`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.94 },
    { url: `${baseUrl}/tools/agency-proposal-generator/`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.92 },
    { url: `${baseUrl}/articles`, lastModified: now, changeFrequency: 'daily' as const, priority: 0.95 },
    { url: `${baseUrl}/docs`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.95 },
    { url: `${baseUrl}/features`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.95 },
    { url: `${baseUrl}/industries`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.95 },
    { url: `${baseUrl}/use-cases`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.95 },
    { url: `${baseUrl}/ai-agent`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.95 },
    { url: `${baseUrl}/pricing`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.95 },
    { url: `${baseUrl}/demo`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.9 },
    { url: `${baseUrl}/get-a-demo`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.9 },
    { url: `${baseUrl}/changelog`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.9 },
    { url: `${baseUrl}/about`, lastModified: now, changeFrequency: 'monthly' as const, priority: 0.9 },
    { url: `${baseUrl}/contact`, lastModified: now, changeFrequency: 'monthly' as const, priority: 0.9 },
    { url: `${baseUrl}/compare`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.9 },
    { url: `${baseUrl}/tools`, lastModified: now, changeFrequency: 'daily' as const, priority: 0.85 },
    { url: `${baseUrl}/integrations`, lastModified: now, changeFrequency: 'weekly' as const, priority: 0.9 },
    { url: `${baseUrl}/brand`, lastModified: now, changeFrequency: 'monthly' as const, priority: 0.8 },
    { url: `${baseUrl}/brand-assets`, lastModified: now, changeFrequency: 'monthly' as const, priority: 0.8 },
    { url: `${baseUrl}/terms`, lastModified: now, changeFrequency: 'monthly' as const, priority: 0.8 },
    { url: `${baseUrl}/privacy`, lastModified: now, changeFrequency: 'monthly' as const, priority: 0.8 },
    { url: `${baseUrl}/refund-policy`, lastModified: now, changeFrequency: 'monthly' as const, priority: 0.8 },
    { url: `${baseUrl}/security`, lastModified: now, changeFrequency: 'monthly' as const, priority: 0.8 },
    { url: `${baseUrl}/sla`, lastModified: now, changeFrequency: 'monthly' as const, priority: 0.8 },
    { url: `${baseUrl}/status`, lastModified: now, changeFrequency: 'daily' as const, priority: 0.8 },
  ];

  return [
    ...staticHubPages,
    ...articleCategoryUrls,
    ...articleUrls,
    ...docUrls,
    ...featureUrls,
    ...industryUrls,
    ...useCaseUrls,
    ...comparisonUrls,
    ...integrationUrls,
    ...toolUrls,
  ];
}
