import { WORDPRESS_CONTENT, type SearchContentPage } from '@/lib/wordpress-content';

export type BlogCategory = {
  slug: string;
  name: string;
  description: string;
};

export type BlogPost = SearchContentPage & {
  category: BlogCategory;
};

export const BLOG_CATEGORIES: BlogCategory[] = [
  {
    slug: 'wordpress',
    name: 'WordPress',
    description: 'Practical decisions about WordPress websites, plugin stacks, publishing, and connected agency workflows.',
  },
  {
    slug: 'elementor',
    name: 'Elementor',
    description: 'Guides for teams evaluating Elementor maintenance, publishing ownership, and alternatives.',
  },
  {
    slug: 'woocommerce',
    name: 'WooCommerce',
    description: 'Commerce and payment workflow guidance for service businesses that sell projects rather than products.',
  },
  {
    slug: 'content-marketing',
    name: 'Content Marketing',
    description: 'Repeatable research, writing, SEO, AI-search, internal linking, and publishing workflows for agencies.',
  },
];

const CATEGORY_BY_POST: Record<string, string> = {
  'alternative-for-agencies': 'wordpress',
  'elementor-alternative-for-agencies': 'elementor',
  'woocommerce-alternative-for-service-businesses': 'woocommerce',
  'content-publishing-workflow-for-agencies': 'content-marketing',
};

export const BLOG_POSTS: BlogPost[] = WORDPRESS_CONTENT.map((post) => {
  const categorySlug = CATEGORY_BY_POST[post.slug];
  const category = BLOG_CATEGORIES.find((item) => item.slug === categorySlug);
  if (!category) throw new Error(`Missing blog category for ${post.slug}`);
  return { ...post, category };
});

export const BLOG_POSTS_BY_PATH = Object.fromEntries(
  BLOG_POSTS.map((post) => [`${post.category.slug}/${post.slug}`, post]),
) as Record<string, BlogPost>;

export function getBlogCategory(slug: string) {
  return BLOG_CATEGORIES.find((category) => category.slug === slug);
}

export function getPostsForCategory(slug: string) {
  return BLOG_POSTS.filter((post) => post.category.slug === slug);
}

export function getBlogPost(category: string, slug: string) {
  return BLOG_POSTS_BY_PATH[`${category}/${slug}`];
}

export function getBlogPostUrl(post: BlogPost) {
  return `/blog/${post.category.slug}/${post.slug}/`;
}
