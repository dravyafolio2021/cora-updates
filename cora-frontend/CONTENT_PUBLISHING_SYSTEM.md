# Cora organic search and AI-discovery system

This is the operating guide for publishing useful, searchable articles without a CMS. The repository is the source of truth. A production push publishes the articles as static pages.

## Canonical architecture

```text
/
├── /articles/                                      Editorial directory
│   ├── /{category}/                                Category hub
│   │   └── /{slug}/                                Individual article
├── /compare/                                       Product comparisons
├── /integrations/                                  Integration and setup pages
├── /features/                                      Product capability pages
├── /use-cases/                                     Industry workflows
└── /docs/                                          Technical documentation
```

Editorial content must use `/articles/{category}/{slug}/`. Do not create a second `/blog` route or duplicate an article under a product route.

## Content thesis

Use this as the consistent search story:

> Your website should help run the business, not create more admin.

Cora does not need to tell every company to abandon WordPress. A credible entry point is:

1. Keep the public site if it works.
2. Connect the enquiry and client workflow.
3. Measure whether the workflow becomes easier and produces relevant conversations.
4. Migrate more only when evidence supports the decision.

## Article contract

Every article must contain:

- One intended audience and one distinct buyer/search question.
- A descriptive slug and one canonical URL.
- A unique title and description.
- A direct answer near the beginning.
- First-hand evidence, an original example, or a tool the reader can use.
- Primary sources for product, legal, financial, security, and technical claims.
- Honest discussion of when the current tool remains appropriate.
- Visible FAQs that match the structured data.
- Published and meaningfully updated dates.
- Three useful internal links and one relevant product bridge.
- One trackable CTA source.
- Sitemap inclusion and a successful production build.

## Creation workflow

1. Collect exact questions from calls, enquiries, onboarding, support, communities, and Search Console.
2. Group them by audience, problem, and decision stage.
3. Update an existing article when it already serves the intent.
4. Create a brief describing the question, evidence, unique thesis, sources, internal links, and CTA.
5. Draft for the reader, with the answer first and Cora introduced only where relevant.
6. Review factual claims, visible copy, metadata, structured data, links, and mobile readability.
7. Run `npm run check:publish`.
8. Push the reviewed change. The deployment generates static article and category pages.
9. Submit changed URLs to IndexNow after deployment and inspect priority URLs in Search Console.
10. Review discovery and conversion signals weekly and improve existing articles before scaling volume.

## First 90-day focus

- 70% creative and service agencies.
- 20% WordPress, Elementor, WooCommerce, and content-publishing pain.
- 10% real-estate-agency validation.

The next content queue is:

1. Why we did not publish an agency article for two months—and the system we built.
2. WordPress alternative for creative and service agencies.
3. Elementor alternative for agencies tired of website maintenance.
4. Why agency websites become brochures instead of business systems.
5. WordPress plugin overload: what an agency should remove first.
6. A practical content-publishing workflow for small agencies.
7. WooCommerce alternative for service businesses selling projects.
8. What should happen after someone submits an agency website form.
9. How to migrate an agency site without losing search visibility.
10. Real-estate-agency website lead workflow: enquiry to site visit.

## Evidence and quality rules

- Do not invent ratings, customer counts, savings, revenue, performance, legal validity, compliance, or integration behavior.
- Label roadmap capabilities clearly instead of presenting them as live.
- Use a real author or an accurately named editorial organization.
- Comparison prices and competitor facts require current primary sources.
- Legal and financial content requires qualified review and a clear informational disclaimer.
- Do not create near-duplicate pages for keyword variations.
- AI may accelerate research and drafting, but a human remains accountable for every published claim.

## Discovery after deployment

1. Confirm the new URL returns HTTP 200 and its canonical resolves to the same trailing-slash URL.
2. Confirm the URL is present in `sitemap.xml` and internally linked.
3. Submit the changed URL through IndexNow.
4. Use Search Console URL Inspection for high-priority commercial pages.
5. Distribute one useful founder post and one community-native answer derived from the article.
6. Track Google, Bing, ChatGPT, Perplexity, referral, signup, and assisted-conversion signals.

## Success metrics

- Published versus indexed articles.
- Non-branded impressions and queries.
- Qualified organic visits by landing page.
- Signups and conversations attributed to an article.
- ChatGPT referrals containing `utm_source=chatgpt.com`.
- Perplexity and other AI-search referrals.
- Citations for a fixed monthly set of buyer questions.
- Articles improved from actual conversations and search data.
