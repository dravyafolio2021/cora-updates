# Public design-system delivery

Task: Add an agent-first design system to the existing marketing frontend at `/design-system/`.

- [x] Workspace and legacy design audit — `workspace_audit` agent.
- [x] Marketing UI and reusable pattern audit — `frontend_audit` agent.
- [x] Canonical versioned registry and generated JSON, text and CSS — primary agent.
- [x] Responsive public explorer, search, token categories and component playground — primary agent.
- [x] Shared button, field, badge, card and native modal sheet primitives — primary agent.
- [x] Persistent agent instructions and scoped contract checker — `agent_contract` agent.
- [x] Footer, sitemap and AI discovery links — primary agent.
- [x] Read-only implementation review — `frontend_audit` agent; canvas, search focus and safe-area findings resolved.
- [x] Browser verification — 1440px desktop, 390px phone and 320px narrow reflow; search, no-results, jump/focus, profile/theme, errors, disabled/loading, modal Tab cycling/Escape/focus return, and copy confirmation verified. Homepage, docs and tools render with existing host styles.
- [x] Final static build and exported-artifact verification — all 338 static pages generated; design-system JSON/text/CSS match the canonical source exactly and full reference HTML is present.

The authenticated product was audited through source. Its screens are not globally restyled by this task. Existing unrelated edits are preserved. Public availability on the production domain requires the normal frontend deployment; this working tree has unrelated unpublished changes.

Verification: contract 1.0.0 has 82 tokens, 25 component contracts and five shared primitives. Default/prebuild and four explicit feature-file checks pass, including 38 generated theme contrast pairs. The build also logs the existing staging-host DNS fallback failure; it succeeds. Authenticated workspace flows were audited through source, not runtime-tested.

Machine HTTP verification: manifest.json, llms.txt and tokens.css each return 200 with application/json, text/plain and text/css respectively. Final exported artifacts match source byte-for-byte (JSON normalized). Preview screenshot saved at /private/tmp/cora-design-system-desktop.jpg.

## Visual catalog extension — contract 1.1.0

- [x] Registry: 24 source-backed specimens in six categories, stable permalinks, linked component contracts, variants, required states and structural examples. JSON and text derive from this registry.
- [x] Visual catalog: category/search controls, marketing/workspace and light/dark previews, expansion, usage/code disclosure and copy controls. All specimens are present in static HTML.
- [x] Live examples: surface/feature/project/metric/pricing/blog cards; buttons, form validation, selection, tabs, filters; modal, responsive drawer, confirmation and feedback; disclosure, editorial and collection states; table, board, stepper; image frames, file selection and icons.
- [x] Shared Sheet placements: modal centered at every width; drawer right-edge at >=1024px and bottom sheet below; existing default placement preserved. Focus containment, Escape, focus return and document scroll lock verified.
- [x] Verification: default checker and explicit targets for all nine changed/new UI files pass; TypeScript passes; final production build passes. All 24 IDs exist in exported HTML; JSON, text and CSS match canonical source exactly.
- [x] Browser: 1440px and 1120px desktop, 768px tablet, 390px phone and 320px reflow; no page overflow. Verified category/search/no-results/reset, hidden-specimen search jump and focus, theme/profile, expansion/code/copy, required/email validation, loading/error/disabled previews, tab arrows, notification dismissal, table sort preserving statuses, board move, icon selection, image ratio and local file selection/rejection. Docs and tools still render. Desktop preview: /private/tmp/cora-design-system-catalog-desktop.jpg.

The examples intentionally do not upload, charge, publish or mutate real records. Production backend states remain required in their contracts, not simulated as completed operations. This catalog does not certify every legacy screen or screen-reader combination. Production deployment remains pending through the existing marketing site's normal release; unrelated working-tree changes remain preserved.
