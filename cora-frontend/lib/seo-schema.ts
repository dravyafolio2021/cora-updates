import type { Article, ArticleCategory } from './articles-data';

export function generatePlatformSchemas() {
  const organizationSchema = {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    '@id': 'https://heycora.in/#organization',
    name: 'Cora',
    alternateName: ['HeyCora', 'Cora AI', 'Cora AI Co-Founder', 'Cora Agency OS'],
    url: 'https://heycora.in',
    logo: 'https://heycora.in/apple-touch-icon.png',
    image: 'https://heycora.in/og-image.png',
    description:
      'Cora is an AI co-founder and connected business operating system for agencies and service businesses, connecting leads, clients, projects, operations and billing.',
    sameAs: [
      'https://twitter.com/dravyafolio',
      'https://linkedin.com/in/dravyafolio',
      'https://instagram.com/dravyafolio',
      'https://github.com/dravyafolio2021',
    ],
    founder: {
      '@type': 'Person',
      name: 'Dravya Bansal',
      jobTitle: 'Founder',
    },
    contactPoint: {
      '@type': 'ContactPoint',
      contactType: 'Customer Support',
      email: 'support@heycora.in',
      url: 'https://heycora.in/contact/',
    },
  };

  const webSiteSchema = {
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    '@id': 'https://heycora.in/#website',
    url: 'https://heycora.in',
    name: 'Cora',
    alternateName: ['HeyCora', 'Cora AI', 'Cora AI Co-Founder', 'Cora for Agencies'],
    description:
      'Cora is an AI co-founder and connected operating system for agencies and service businesses.',
    publisher: { '@id': 'https://heycora.in/#organization' },
  };

  const softwareApplicationSchema = {
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    '@id': 'https://heycora.in/#software',
    name: 'Cora',
    alternateName: ['Cora AI', 'Cora AI Co-Founder', 'Cora Agency OS'],
    operatingSystem: 'Web',
    applicationCategory: 'BusinessApplication',
    description:
      'Cora connects agency and service-business workflows around leads, clients, proposals, projects, approvals, documents and billing.',
    url: 'https://heycora.in',
    brand: { '@id': 'https://heycora.in/#organization' },
  };

  const faqSchema = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: [
      {
        '@type': 'Question',
        name: 'What is Cora?',
        acceptedAnswer: {
          '@type': 'Answer',
          text: 'Cora is an AI co-founder and connected business operating system for agencies and service businesses. It connects leads, clients, proposals, projects, approvals, documents and billing in one workspace.',
        },
      },
      {
        '@type': 'Question',
        name: 'Who is Cora built for?',
        acceptedAnswer: {
          '@type': 'Answer',
          text: 'Cora is built for client-service businesses including performance marketing agencies, design studios, web and development agencies, SEO teams, creative businesses and other service companies.',
        },
      },
      {
        '@type': 'Question',
        name: 'Is Cora the same product as other companies named Cora?',
        acceptedAnswer: {
          '@type': 'Answer',
          text: 'No. HeyCora at heycora.in builds Cora for agencies and service businesses. Other unrelated products and companies also use the Cora name.',
        },
      },
    ],
  };

  return [organizationSchema, webSiteSchema, softwareApplicationSchema, faqSchema];
}

export function generateArticleSchema(article: Article) {
  const articleUrl = `https://heycora.in/articles/${article.category}/${article.slug}/`;

  return {
    '@context': 'https://schema.org',
    '@type': 'Article',
    '@id': `${articleUrl}#article`,
    headline: article.title,
    description: article.description,
    image: 'https://heycora.in/images/cora_hero_landscape.jpg',
    author: {
      '@type': article.author.name === 'Dravya Bansal' ? 'Person' : 'Organization',
      name: article.author.name,
      description: article.author.role,
    },
    publisher: { '@id': 'https://heycora.in/#organization' },
    articleSection: article.categoryLabel,
    inLanguage: 'en-IN',
    datePublished: article.publishedAt,
    dateModified: article.updatedAt,
    mainEntityOfPage: {
      '@type': 'WebPage',
      '@id': articleUrl,
    },
  };
}

export function generateArticleBreadcrumbs(category: ArticleCategory, article?: Article) {
  const items = [
    {
      '@type': 'ListItem',
      position: 1,
      name: 'Home',
      item: 'https://heycora.in',
    },
    {
      '@type': 'ListItem',
      position: 2,
      name: 'Articles & Guides',
      item: 'https://heycora.in/articles/',
    },
    {
      '@type': 'ListItem',
      position: 3,
      name: category.name,
      item: `https://heycora.in/articles/${category.id}/`,
    },
  ];

  if (article) {
    items.push({
      '@type': 'ListItem',
      position: 4,
      name: article.shortTitle || article.title,
      item: `https://heycora.in/articles/${category.id}/${article.slug}/`,
    });
  }

  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items,
  };
}

export function generateArticleFaqSchema(faqs: { question: string; answer: string }[]) {
  return {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faqs.map((f) => ({
      '@type': 'Question',
      name: f.question,
      acceptedAnswer: {
        '@type': 'Answer',
        text: f.answer,
      },
    })),
  };
}
