# Cora design system: agent implementation guide

This is the repository entry protocol for every AI agent changing Cora UI. Its purpose is to carry approved visual and interaction decisions into each new feature, so users do not have to repeat corrections. The canonical contract is maintained with production code and exposed publicly through the existing Next.js site.

## Read order and source authority

1. Read the applicable `AGENTS.md` files, then this guide.
2. Read `cora-frontend/lib/design-system.ts` or fetch `/design-system/manifest.json` from the marketing site. The JSON is the primary remote machine interface. Use `/design-system/llms.txt` for a readable contract and `/design-system/tokens.css` for generated CSS custom properties.
3. Inspect the relevant surface profile, token, pattern, component, and terminology entries. Check each entry's status and source references. Distinguish implemented baseline rules from recommended future improvements.
4. Inspect the production component that serves the same purpose as the feature being built. Reuse its pattern or extend it coherently. Inspect legacy implementations for compatibility risks, not as a substitute for the canonical contract.
5. Use `/design-system` for interactive examples, theme preview, search, and mobile inspection. The human presentation and machine exports share the same source.

## Using the public visual catalog

People can open `/design-system/#catalog`, choose Cards, Controls, Overlays, Content, Data & workflows, or Images & icons, and try the live examples. Search finds a specimen by name, variant, state or usage. Choose marketing/workspace and light/dark to inspect its surface. Expand keeps the same interactive specimen in a wider row; “Usage & code” reveals rules, required states, source paths, linked contracts and a copyable structural example. Each specimen has a stable `#specimen-<id>` permalink.

Developers and agents read `catalogGuide` and `catalog` in the canonical JSON/source alongside `components`, `primitiveApi`, tokens and the chosen profile. `componentIds` links specimens to their contracts. `shared-primitive` means the documented primitive can be imported from the shared module. `reference-recipe` demonstrates a composition that must be adapted to the host feature; it is not a separate shared API or a claim that all legacy screens have been migrated. Examples require caller state and handlers. The preview implementation is `cora-frontend/components/design-system/CatalogPreviews.tsx`.

To extend the catalog, add a stable record to `designSystem.catalog`, implement its specimen in `CatalogPreviews`, document variants and the complete state requirements, and run the scoped checks and browser verification. JSON and plain text derive from the same registry automatically. Update the contract version and change history in the same change. Do not edit a generated export separately. Public examples contain fictional data, and interactive previews do not perform uploads, payments, publication or real record changes.

Publish `/design-system` and its exports through the existing marketing site's normal release. The public page does not require authentication. Building locally prepares the exported artifacts; it does not deploy them to the production domain.

Canonical authority belongs to `cora-frontend/lib/design-system.ts`; renderer and export functions derive from it. Existing `cora-design-tokens.js`, `cora-design-system.html`, and `cora-frontend/lib/cora-tokens.ts` remain legacy references. They contain useful history but may differ from executed surfaces. For example, the marketing site executes General Sans while the workspace executes Inter. A legacy token must not silently override a surface's actual typeface.

The public contract must contain design decisions and safe source references only. Keep credentials, customer data, private host details, session identifiers, and operational secrets out of all public exports and examples.

## Feature implementation protocol

For each UI task record, in the implementation or review notes:

- **Surface:** marketing, workspace, or a documented sub-surface; its typography and density profile.
- **Pattern:** existing component or interaction reused, and the canonical source path.
- **Tokens:** semantic names used for backgrounds, text, borders, states, spacing, type, shape, and motion.
- **States:** loading, empty, success, error, disabled, selected, hover, and focus behavior that apply.
- **Responsive behavior:** narrow-screen navigation, stacking, scrolling, touch targets, and containment.
- **Validation:** contract check, build or module checks, and the observed behavior at desktop and mobile sizes.

Choose semantic tokens by purpose. A success state is not a decorative brand accent. A muted text token must remain readable on its intended surface. A workspace density rule does not justify making touch controls too small. Use named CSS variables and shared primitives instead of hard-coded colors or fresh font stacks in feature components.

The new frontend primitives live in `cora-frontend/components/design-system/primitives.tsx`. Import those components where they match the task. Use generated namespaced `--cora-*` variables for other elements. Avoid inventing a second style system, duplicate buttons with slightly different padding, or feature-specific interpretations of the same selected state.

For the PHP workspace, use the contract as the authoritative specification while respecting existing module boundaries. Map canonical values through the workspace's established style entry points; do not import a React component into PHP or globally rewrite unrelated workspace rules. A targeted workspace migration must identify the changed selectors and verify existing interactions.

## Interaction and accessibility requirements

Use semantic links for navigation and buttons for actions. Labels must describe the user action or visible entity. Keep machine internals out of product copy unless they help the user make a decision. Follow the contract's terminology entries rather than creating alternate labels for the same concept.

Every interactive element needs a visible keyboard focus state. The preference for quiet borders and tonal selection never permits removing focus visibility. Support keyboard navigation, Escape dismissal where relevant, programmatic labels, and focus return for dialogs or drawers. Do not communicate status with color alone.

Respect reduced-motion preferences. Prevent horizontal page overflow on narrow screens; use contained overflow for genuinely wide code or tabular content. Preserve readable text, adequate touch targets, and actionable error messages. For each changed interaction verify its real state transitions, not only its initial screenshot.

## Machine-first contract change protocol

1. Search the existing contract and production components before adding a concept. Prefer an existing semantic token or pattern with the same role.
2. Describe the concrete missing capability. Identify affected surfaces, accessibility implications, compatibility constraints, and the implementation source.
3. Add the token or rule to `cora-frontend/lib/design-system.ts`. Add human explanations there so the public page, Markdown, JSON, and CSS remain derived. Mark proposals as recommendations until implemented or accepted; never present an unimplemented suggestion as an executed baseline.
4. Update the associated production primitive or pattern, with real states and responsive behavior. Keep recommendation labels distinct from required rules.
5. Update the contract version and change history when its meaning changes. A compatible new token or component increments the minor version; corrections to descriptions increment the patch version; renamed/removed tokens or changed semantics require a major version and migration notes. Do not bump solely for generated formatting.
6. If replacing a token, add its replacement and deprecation/migration note first. Migrate the touched consumers, check dependent surfaces, and remove the old token only as a deliberate compatible-release decision.
7. Run `npm run check:design-system -- --files components/Feature.tsx components/Feature.module.css` in `cora-frontend`, substituting every UI source and stylesheet touched by the task. Run the build and relevant checks for affected code. Verify real responsive and keyboard behavior. Include the exact contract version and any unresolved migration work in review notes.

No extra approval ceremony is required for routine implementation within an authorized task. Material visual redesigns or changes to approved semantics should be presented explicitly in the reviewable result rather than hidden inside an unrelated feature.

## Automated checks and their boundaries

`cora-frontend/scripts/check-design-system.mjs` validates that the canonical source serializes; token names are unique and well formed; JSON, Markdown, and CSS routes derive from its exports; and generated CSS contains the canonical tokens. It verifies stable component IDs, required component metadata, primitive import/props/example/token fields, and matching production exports. The five production primitive IDs remain `button`, `input`, `badge`, `card`, and `sheet`; changing their meaning or identity requires a deliberate compatibility migration.

The check calculates contrast from the generated light and dark theme declarations. Primary/supporting text on page, surface and selected fills; metadata on page/surface; primary-action labels; and status labels on their matching status surfaces must meet 4.5:1. Focus and strong control boundaries against page, surface and selected fills must meet 3:1. These are explicit tested pairings, not permission to use every foreground token on every background. For example, metadata placed on a selected fill still needs its actual pairing reviewed.

The default checker intentionally scopes style enforcement to the new design-system route and reusable primitives. `prebuild` runs that default automatically. The existing product contains legacy one-off values and duplicated style rules. Failing the whole repository for those would block ordinary development without fixing it. Every future UI task must still follow the repository instructions, and broader migration should be introduced in scoped, verified batches.

For each feature, pass explicit UI files after `--files`. The checker adds those files to its canonical scope and rejects raw color values, fixed Tailwind palette colors, independent font stacks, unknown `--cora-*` variables, browser-native `alert`/`confirm`/`prompt` calls, and detected heavy selected borders. The check reads whole explicitly selected files, so first migrate their relevant legacy declarations or document narrow approved content exceptions. It does not recursively inspect imported feature files: include the component and its styles/dependencies explicitly. Paths resolve from `cora-frontend`; `../app/public/wp-content/plugins/cora-workspace/...` targets the workspace. It accepts repository UI source files, excludes dependency/git trees, and rejects paths outside the repository. The canonical token data module already has its own validation and is not subjected to the UI literal rule.

The checker has an explicit `STYLE_EXCEPTIONS` registry, initially empty. A reviewed exception must specify an exact repository-relative file, permitted rule (`color-literal`, `fixed-color`, or `font-family`), exact literal, reason, and canonical source reference. Use it only for established artwork, authentic brand marks, or user-authored content/dynamic brand sources documented by the contract. Keep the exception close to the actual design rationale in review notes and add the narrow registry entry with the implementation. Wildcards, file-wide bypasses, and unaudited inline suppression comments are unavailable. Dialog calls, unknown tokens, contrast failures, and heavy selected perimeters are never exempted. This is not an extra permission step for authorized feature work; it is a durable record of a real design exception.

The check is necessary but verifies only its declared contrast pairings and statically detected styles/calls. It does not prove all runtime combinations, dynamically computed values, keyboard behavior, responsive layout, screen-reader behavior, or every product screen's compliance. Manual/browser verification remains part of the feature workflow. Never describe a passing check as a complete audit of the entire product.

## Useful local commands

From `cora-frontend`:

```sh
npm run check:design-system
npm run check:design-system -- --files components/Feature.tsx components/Feature.module.css
npm run build
```

Do not create a separate frontend, a new workspace product module, or a second public documentation project to extend this system. Maintain the existing `/design-system` slug and its exports in the current Next.js repository.
