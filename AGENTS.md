# Cora repository instructions

## Required design-system contract for UI work

Before changing any user-facing UI, read `docs/design-system-agent-guide.md` and the canonical contract in `cora-frontend/lib/design-system.ts`. The public entry point is `/design-system`; machine-readable exports are `/design-system/manifest.json`, `/design-system/llms.txt`, and `/design-system/tokens.css` on the existing marketing site.

Use the contract's semantic tokens, component patterns, terminology, surface profiles, and accessibility rules. Reuse the production primitives in `cora-frontend/components/design-system/primitives.tsx` when their purpose fits. Preserve the difference between the General Sans marketing surface and the Inter workspace surface. Existing duplicated tokens, one-off colors, and legacy screenshots are evidence to audit, not permission to copy drift into new work.

When the contract lacks a necessary rule, extend its single source and document the reason before introducing an isolated UI value. All generated exports must continue to derive from that source. Do not independently maintain another token list or a hand-written manifest. Do not silently redefine an existing token's meaning; follow the contract change protocol in the guide.

For frontend changes run `npm run check:design-system -- --files components/Feature.tsx components/Feature.module.css` from `cora-frontend`, listing every new or changed UI component and its styles, plus the build and relevant task checks. Paths resolve from `cora-frontend`; workspace files can use `../app/public/wp-content/plugins/cora-workspace/...`. The default check and build precheck cover the canonical surface only, so unrelated dirty legacy code does not block a release. Explicit targets enforce semantic colors/fonts, known tokens, custom feedback and tonal selection in the touched feature. Document narrowly reviewed artwork/content exceptions; never bypass an entire file. The check verifies declared light/dark contrast pairs and component metadata, but does not prove every legacy screen complies. Verify changed screens at mobile and desktop sizes, including keyboard focus, loading, empty, error, and disabled states where relevant. State the exact checks performed and remaining limits.

Keep work scoped to the requested feature. Existing unrelated changes may be present in this shared checkout; preserve them. Respect more specific instructions, including `cora-frontend/AGENTS.md` and its installed Next.js documentation requirement.
