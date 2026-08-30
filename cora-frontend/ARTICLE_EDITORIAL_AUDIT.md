# Article editorial audit

Audit date: 2026-08-30

The `/articles/{category}/{slug}/` foundation builds correctly. The original 23-page inventory is technically publishable, but it should not be promoted as a finished authority library until each page passes factual and product review.

## Release rule

An article is ready to promote only when:

1. Every live product workflow mentioned in the article has been tested against the current product.
2. Legal, tax, accounting, security, performance, savings, and competitor claims have primary sources or have been softened appropriately.
3. The title and description communicate the reader’s question without hype.
4. The author/byline accurately describes who wrote or reviewed the page.
5. The article adds first-hand evidence, an original example, or a useful tool beyond a generic summary.
6. `npm run check:publish` completes with no structural errors and its warnings have been reviewed.

## Inventory triage

### Priority A — align with the immediate agency search strategy

Review and improve these first:

- `why-our-agency-published-no-articles-for-two-months` — new first-hand cornerstone article
- `generative-engine-optimization-geo-for-service-businesses`
- `commercial-photography-studio-management-guide`
- `real-estate-media-agency-scaling-guide`
- `deal-profitability-and-margin-simulation-playbook`

These are closest to the selected agency and real-estate audiences, but they still need first-hand examples and claim verification.

### Priority B — useful product education after workflow testing

- `how-to-set-up-creative-studio-workspace`
- `how-to-automate-shoot-booking-and-contracts`
- `how-to-dispatch-whatsapp-crew-call-sheets`
- `how-to-install-and-use-cora-pwa`
- `preventing-camera-gear-conflicts-and-double-bookings`
- `how-autonomous-ai-co-founders-run-creative-studios`
- `voice-to-scope-turning-whatsapp-voice-notes-into-contracts`
- `connecting-cursor-and-claude-to-cora-via-mcp`

These pages must match the current interface and live behavior. Screenshots or short product demonstrations would materially improve their usefulness.

### Priority C — qualified review required

- `complete-guide-to-18-percent-gst-for-photographers-and-studios`
- `sac-code-998381-gst-rates-and-exemptions-explained`
- `how-to-calculate-advance-gst-and-input-tax-credit`
- `indian-it-act-2000-electronic-contract-validity-guide`
- `essential-clauses-every-commercial-production-contract-needs`
- `tamper-evident-sha256-digital-contracts-explained`

Do not actively distribute these until a qualified Indian tax, accounting, legal, or security reviewer checks the relevant claims. The page template now adds a visible educational-information disclaimer, but a disclaimer does not replace factual review.

### Priority D — current competitor research required

- `cora-vs-honeybook`
- `cora-vs-studio-ninja`
- `cora-vs-hubspot`
- `cora-vs-dubsado`
- `cora-vs-pixieset`

Verify features, prices, limitations, and Cora parity against current primary product documentation. Comparisons should acknowledge when the competitor is the better fit and should not duplicate the canonical `/compare/` pages.

## New content to create before scaling volume

1. WordPress alternative for creative and service agencies.
2. Elementor alternative for agencies tired of website maintenance.
3. Why agency websites become brochures instead of business systems.
4. WordPress plugin overload: what to remove first.
5. A practical content-publishing workflow for small agencies.
6. WooCommerce alternative for service businesses selling projects.
7. What should happen after someone submits an agency website form.

These topics directly reflect the founder’s first-hand problem and the intended acquisition strategy. They should be created one at a time, reviewed, internally linked, and measured rather than released as a bulk batch.

## Current automated warnings

- Twenty-two generated titles exceed the validator’s 65-character review threshold.
- Two pages contain wording that triggers an absolute-claim review.
- The validator treats these as warnings so editors can use judgment; structural problems such as missing canonicals, duplicate metadata, absent schema, crawler blocks, or sitemap omissions fail the command.
