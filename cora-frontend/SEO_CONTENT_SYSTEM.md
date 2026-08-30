# Cora SEO + AI-search content system

This file is the operating guide for building search visibility on heycora.in. The goal is not to publish a large number of generic articles. The goal is to answer repeated buyer questions with distinct, useful pages that lead into the product.

## Positioning rule

Use this as the consistent search story:

> Your website should help run the business, not create more admin.

Cora should not claim that every business must abandon WordPress. The practical entry point is:

1. Keep the public website if it works.
2. Connect the enquiry and client workflow.
3. Measure whether the new workflow is easier and produces relevant conversations.
4. Migrate more of the website only when the evidence supports it.

## Site architecture

```text
/
├── /blog/                                           All articles and categories
│   ├── /wordpress/                                 WordPress category hub
│   │   └── /alternative-for-agencies/              Category alternative article
│   ├── /elementor/                                 Elementor category hub
│   │   └── /elementor-alternative-for-agencies/    Page-builder frustration article
│   ├── /woocommerce/                               WooCommerce category hub
│   │   └── /woocommerce-alternative-for-service-businesses/ Transaction-model article
│   └── /content-marketing/                         Content marketing category hub
│       └── /content-publishing-workflow-for-agencies/ Manual publishing article
├── /integrations/wordpress/                        Product integration and setup
├── /compare/                                       Named software comparisons
├── /use-cases/                                     Industry and workflow proof
├── /features/                                      Product capability proof
└── /docs/                                          Technical and implementation proof
```

The blog directory links to every category and article. Each category links to its articles, and each article links to three related articles. The existing WordPress integration page links into the cluster, and the global footer provides a persistent crawl path.

The canonical blog URL pattern is `/blog/{category}/{topic}/`. Keeping all editorial content under `/blog/` avoids collisions with product routes and makes the publishing system easier to extend. Categories organize discovery; they should not be created merely to repeat a keyword.

## First content and intent map

| Page | Primary search intent | Reader | Product bridge |
| --- | --- | --- | --- |
| Blog directory | Website, workflow, SEO, and AI-search help | Service-business founder exploring ideas | Choose a relevant category |
| WordPress alternative for agencies | Replace or simplify WordPress agency stack | Creative or service agency | Connect Cora before migrating |
| Elementor alternative for agencies | Escape maintenance and publishing dependence | Founder or marketer | Test structured pages and connected enquiries |
| WooCommerce alternative for services | Sell services without forcing them into a cart | Agency, consultant, production team | Use a client workflow for custom work |
| Agency content workflow | Publish consistently without repetitive manual work | Founder or content owner | Connect questions, creation, and publishing |

## Page contract

Every new search page must ship with all of these fields:

- One audience and one distinct search/buyer question.
- A human-readable slug and a unique canonical URL.
- A unique title and meta description.
- A direct answer in the first two paragraphs.
- Specific pain points grounded in a real workflow.
- A fair “when the current tool is still right” section.
- Visible FAQs that exactly match `FAQPage` structured data.
- `Article` and `BreadcrumbList` structured data when appropriate.
- At least three contextual internal links.
- Published and updated dates.
- One relevant next action with a trackable `source` value.
- Inclusion in the generated sitemap and `llms.txt`.

The current implementation stores article content in `lib/wordpress-content.ts`, assigns categories in `lib/blog-data.ts`, and renders it through reusable blog routes. Add a new entry only when it answers a meaningfully different question. As more topics are added, split content into domain-specific data files while keeping `lib/blog-data.ts` as the shared index.

## Next content queue

Do not build all of these at once. Publish the next page after a question repeats in conversations, search data, community discussions, or onboarding.

1. WordPress plugin overload for agencies: what to remove first.
2. Why an agency website stops generating qualified enquiries.
3. WordPress CRM plugin vs a connected client workspace.
4. Elementor form to CRM workflow for service agencies.
5. WooCommerce deposits and milestone payments for custom projects.
6. How to migrate an agency site without losing search visibility.
7. Cora vs WordPress for a service-business website.
8. Cora for creative agencies: enquiry-to-invoice workflow.

## Evidence and quality rules

- Prefer first-hand examples from Cora's own agency and early customers.
- Do not invent ratings, customer counts, savings, compliance, performance, or integration behavior.
- Label roadmap capabilities clearly; do not present them as live.
- Update comparison prices and competitor facts only after checking primary sources.
- Do not create several near-duplicate pages for small keyword variations.
- Keep visible page copy and structured data consistent.
- Use AI to accelerate research and drafting, but keep a human accountable for claims and final usefulness.

## Indexation checklist after deployment

1. Replace `cora-google-search-console-verification` in the production environment with the actual Google Search Console verification token.
2. Deploy and confirm that each new URL returns HTTP 200 on its final trailing-slash URL.
3. Confirm `https://heycora.in/robots.txt` and `https://heycora.in/sitemap.xml` are public.
4. Submit the sitemap in Google Search Console and Bing Webmaster Tools.
5. Inspect the blog directory and the highest-priority article URL in Search Console and request indexing once after deployment.
6. Validate structured data and inspect the rendered canonical on one hub, guide, integration, and comparison page.
7. Record impressions, clicks, queries, relevant signups, and conversations by landing page every week.

Search Console verification and sitemap submission are account actions; they cannot be completed from the repository alone.

## Weekly operating loop

1. Collect exact questions from calls, forms, support, communities, and search queries.
2. Group them by audience, problem, and decision stage.
3. Update an existing page when the intent already exists.
4. Create a new page only for a distinct question.
5. Publish, add internal links, and request indexing when the page is commercially important.
6. Review discovery and conversion signals by landing page.
7. Feed what was learned back into the copy and product.
