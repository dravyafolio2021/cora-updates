# Cora workspace and legacy design-system audit

Audit date: 2026-10-13. Evidence is repository source inspection, not a runtime visual review. No production behavior or authentication was exercised. Paths below are relative to the platform repository root.

## Findings that explain repeated design regressions

1. **Disconnected representations.** `cora-design-tokens.js` defines a v4.0.0 object, root `cora-design-system.html` calls itself v2.40 and duplicates values in inline Tailwind configuration and markup, WordPress `tailwind.config.js` independently defines brand gold/bronze/cream, and Next.js `lib/cora-tokens.ts` independently defines tokens. Search of product source found no consumption of `CoraDesignTokens` or root token script. Documentation currently describes token usage that product code does not enforce.
2. **Contradictory mobile rules.** Root token rule `useSlidingSideDrawers: true` has no viewport qualifier. `.agents/AGENTS.md` Rules 1 and 12 prohibit mobile horizontal drawers and require bottom sheets. `admin-style.css:170` enables desktop drawers at 641px, while `admin-style.css:821` imposes mobile bottom sheets through 1023px. Developer feature-guide example switches to desktop at Tailwind `md` (768px). A single responsive contract needs an explicit boundary.
3. **Contradictory backdrops.** `.agents/AGENTS.md` Rule 12 specifies dark blurred mobile scrims. Developer guide Rule 58 prescribes transparent inspector backdrops. Later executable CSS (`admin-style.css:2095–2116`) forces generic drawer/modal/sheet backdrops transparent with no blur. New documentation must distinguish modal sheets from modeless inspectors rather than copying every historical instruction.
4. **Identity leak in legacy examples.** Root tokens contain owner identity, and legacy HTML embeds it as administrator/sample identity. Current `.agents/AGENTS.md` Rule 3 expressly requires generic fictitious sample names and `Studio Admin`. Public registry should contain no private identity or authentication examples.
5. **Accessibility gaps masquerading as design policy.** `admin-style.css:1826–1844` removes `:focus-visible` outlines and shadows from tabs without an evident replacement. Workspace header mobile AI buttons explicitly use 24px dimensions (`includes/workspace-header.php:385–401`). New components should preserve the monochromatic tone while adding a clearly perceivable keyboard-focus treatment and adequate touch target padding.

## Existing canonical visual language

### Palette

The explicit current visual policy is a restrained neutral Zinc ramp, no decorative rainbow gradients, no emojis, thin monochromatic SVGs, subtle structural borders, and tonal selection surfaces. Defined by `.agents/AGENTS.md` Rules 4, 5, 13 and `cora-design-tokens.js`.

Neutral reference values: 50 `#fafafa`, 100 `#f4f4f5`, 200 `#e4e4e7`, 300 `#d4d4d8`, 400 `#a1a1aa`, 500 `#71717a`, 600 `#52525b`, 700 `#3f3f46`, 800 `#27272a`, 900 `#18181b`, 950 `#09090b`; white `#ffffff`, black `#000000`.

Executable workspace surface differs from legacy neutral-only examples: main canvas uses warm cream `#FBFaf7` and dark canvas `#0c0c0e` (`assets/css/admin-style.css:674–777`). Raised light cards and toast cards use white; raised dark cards use Zinc 900. Structural borders use Zinc 200 light and Zinc 800 dark. Selection uses `bg-zinc-100/90 dark:bg-zinc-800/80` with uniform subtle borders, never a heavy perimeter stroke. Primary actions are filled Zinc 950/900 and white text, with inverted equivalents for dark contexts.

Legacy `stateAccents` definitions are Active green (`#16a34a` text, `#22c55e` solid, `#4ade80` dark text), Warning yellow (`#ca8a04`, `#eab308`, `#facc15`), Critical red (`#dc2626`, `#ef4444`, `#f87171`), Neutral Zinc (`#52525b`, `#71717a`, `#a1a1aa`); backgrounds use corresponding channel color at 10%, borders at 20%. Treat these as semantic state indicators only, not branded decoration. Some existing values need contrast verification before endorsement for small text. Public proofing uses cream `#FBFaf7` and warm border `#E8E5DE` (developer guide public proofing section). Brand gold/bronze in plugin Tailwind configuration is historical drift, not a recommended new product palette.

### Typography

Policy: Inter sans for interface text; JetBrains Mono for code, metrics, identifiers. Root token scale: displayHero 48px/1.1/800/-0.025em; displayApp 32px/1.2/700/-0.02em; h1 24px/1.3/600/-0.015em; h2 18px/1.4/600/-0.01em; h3 14px/1.4/500; body 14px/1.5/400; caption 12px/1.4/400; code 13px/1.5/500. These are declared legacy reference tokens, not uniformly applied.

Actual workspace is dense: standard header mobile title 14px bold, mobile helper text 10px, desktop primary CTA 12px bold (`includes/workspace-header.php:355,372,374`). Many forms use `text-xs` 12px; toast title and text use 11–12.5px (`admin-style.css:1016–1047`). Rich document content is an explicit semantic exception: serif/sans/mono authoring choices are rendered in document canvas (`admin-dashboard.php:13397–13608`). Do not change document typography as part of interface standardization.

Recommended future rule: preserve a 14px body default and 12px metadata floor, allowing dense tables to be compact without making critical form labels or explanatory prose 10px. Mobile inputs should use a readable size suitable for mobile keyboards and zoom behavior. Establish label/body/heading/code roles rather than copying arbitrary `text-[...]` values.

### Radius, spacing, elevation, motion

Declared radii: 0, 2, 6, 8, 12, 16px, pill 9999px. Actual mobile bottom-sheet corner radius is 24px (`admin-style.css:838`), while toast cards use 16px. Add sheet radius explicitly instead of pretending 16px covers all components. Current controls mostly use 8–12px radii; cards 12–16px; pills circular.

Root tokens have no spacing scale. Existing UI follows Tailwind increments: 4/8/12/16/24/32px, dense form rows 12px, forms gap 16px, panels 24px. Treat a 4px base spacing scale as a proposed formalization. Feature-guide example uses panel padding 24px, field spacing 16px, footer gap 8px. Desktop controls often 36–40px height, sticky sub-tabs exactly 40px (`admin-style.css:1764,1785`). Public registry should describe visual dimensions separately from expanded minimum hit areas.

Root shadow definitions: small `0 1px 2px rgba(0,0,0,.05)`; medium soft 4/6px and 2/4px layers; large 10/15px and 4/6px layers; extra-large 20/25px and 10/10px layers. Actual toasts use a soft 12/36px plus 4/12px shadow; mobile sheets use `0 -12px 48px rgba(0,0,0,.18)`. Elevation should be reserved for floating surfaces; base cards have subtle borders with low/no shadow.

Actual sheet animation: transform 320ms with `cubic-bezier(0.16,1,0.3,1)`, opacity 220ms; toast transform 280ms same easing and opacity 240ms (`admin-style.css:847,943`). Legacy universal transition is 150ms. Add motion roles (feedback 150ms, floating panels 220–320ms) and reduced-motion handling as a proposed improvement. No reduced-motion contract was evident in inspected workspace stylesheet.

## Existing reusable components and integration points

- `includes/workspace-header.php` exposes `cora_render_workspace_header($args)` with title/mobile title, description/mobile description, icon, primary CTA, AI stack, tutorial handler, visible tab list and container class. Reuse this in WordPress modules rather than creating a second page header. Desktop header/title/description/CTA; mobile compact title; full-width sticky scrollable tabs. Registry must document the PHP and React implementations separately.
- `assets/js/admin-script.js:292,323` implements reference-counted `window.coraLockScroll()` / `window.coraUnlockScroll(forceAll)` preserving page and main-pane scroll position and allowing interactive inner inputs. New modal sheets must use balanced calls and recognized `.cora-drawer-scrollable` / `[data-cora-scrollable]` containers. Do not implement another global scroll lock.
- `assets/js/admin-script.js:707` exposes `window.coraShowToast(message, type='info', actionConfig=null)` with success/error/warning/info types, duplicate suppression, plain monochromatic cards, and action support. Existing toast position is top center on mobile; bottom-right at >=768px with CTA-overlap adjustment. The old "always top-floating" prose is inaccurate for desktop. Native browser alert/confirm/prompt are forbidden; use custom `window.coraConfirm` for destructive confirmation.
- Sidebar administrator/profile block stays sticky at the bottom and opens an adjacent/above popover. Main desktop navigation can collapse to icon mode. Mobile uses floating island navigation and bottom-sheet portals (`admin-style.css:2119+`, `admin-dashboard.php` sidebar sections). Maintain safe-area spacing and do not obscure high-frequency actions.
- Root atomic catalogue covers button variants, segmented controls, pills/tags, text/password/URL controls, file dropzones, date/time selectors, accordions, tooltips/keyboard shortcuts, skeletons, avatars; guided 5-step document studio; aspect-ratio presets; GST breakdown; tables; Kanban; SEO inspector; vault e-sign registry. This is documentation coverage, not proof of a shared exported component library.

## Product structure and workflow inventory

Feature view files live in `app/public/wp-content/plugins/cora-workspace/views/`. Public designs must cover these areas rather than only marketing cards:

- Workspace shell, navigation, profile/admin popover, feature hub, role permission matrices, audit panel, settings. Evidence: `admin-dashboard.php`, `view-feature-hub.php`, `view-users.php`, `view-cora-roles.php`, `view-settings-suite.php`.
- CRM leads, clients and contact detail; table/card switches, Kanban pipeline, filters, stage/status/assignee controls, activity. Evidence: `view-leads.php`, `view-clients.php`, partial task Kanban files.
- Tasks/projects; property matrices, tabs, checklists, scope, assets, activity; drawer resizing; project public portfolio and confidentiality. Evidence: `view-client-task-manager.php`, `view-projects.php`, `tests/e2e/test-task-details-drawer-redesign.spec.ts`.
- Content suite, editorial workflow/calendar, RAG/business context, forms/reviews, inbox/emails, automation. Evidence: corresponding `view-content-suite.php`, `view-calendar.php`, `view-rag.php`, `view-forms.php`, `view-review-acquisition.php`, `view-inbox.php`, `view-emails.php`, `view-automations.php`.
- Media library/editor/proofing, document vault/e-sign, Canvas/visual builder/theme editor, financials, inventory/equipment/crew. Evidence: corresponding `view-media.php`, `view-media-editor.php`, `view-vault.php`, `view-canvas.php`, `view-visual-builder.php`, `view-financials.php`, `view-inventory-management.php`, `view-equipment.php`, `view-crew-scheduler.php`.
- Public gallery/doc/form/client portals and public docs. Evidence: plugin `public-gallery-view.php`, `public-doc-view.php`, `public-form-view.php`, `public-client-portal.php`, and `views/view-public-docs*.php`.

Reusable behavior rules: filter zero-results gets an explicit empty state; config changes use an explicit unsaved-state/save bar and one batch save; row selection supports counts and a bottom bulk-action bar; tabs use persistent preference where customization is supported; optimistic per-item changes patch neighboring board/table representations; drawers portal to body where transformed/overflow parents clip; no artificial navigation/skeleton delays; all UI event names and selectors are feature scoped.

## Mobile/accessibility contract recommended for future implementation

These are recommended clarifications, not a claim that all existing views comply:

1. Define one viewport boundary per shell/panel pattern; new workspace patterns should follow existing shell mobile behavior through <1024px unless a documented component exception is necessary. Marketing navigation may have its own explicit responsive boundary.
2. Modal mobile actions/forms: bottom sheet, 24px top corners, 40x4px handle, max 90dvh with vh fallback, fixed header/actions, independently scrollable body, safe-area footer. Desktop modeless inspector: right panel with minimal/transparent dismiss shield; preserve context. Modal dialogs need a perceivable scrim separate from modeless inspection panels.
3. Semantic buttons and links, labels associated with inputs, aria-describedby for help/errors, aria-invalid after validation, accessible names for icon-only controls, visible keyboard focus using tonal fill plus a narrow neutral inset indicator. Never interpret no-heavy-selected-card-outline as permission to erase keyboard focus.
4. Modal sheets require dialog semantics, initial focus, focus trapping, Escape closure, restoring focus to opener, and background inertness; do not label modeless inspectors aria-modal. Tabs require selected semantics and keyboard navigation when used as actual tab panels.
5. Touch targets should be at least 44px where practical. Expand hit area around small SVGs; 24px overlapped mobile actions are a measured current gap, not a canonical new standard.
6. Text and state semantics must not rely on color alone. Validate actual text/background combinations; supply labels/icons. Avoid faded 10px text for critical information.
7. Reduce or remove motion for prefers-reduced-motion. Do not apply infinite marquees or decorative pulses without user/motion preference consideration.
8. Use min-width:0 for flexible children, single page scroll on mobile, contained horizontal table/tab overflow, no whole-page horizontal scroll at 320px, safe-area padding for floating bottom bars.

## AI-oriented maintenance rules

Public page and machine JSON/Markdown must be generated from the same versioned typed registry. Each rule needs id, scope, status (existing/proposed/deprecated), source paths, exact behavior, implementation reference, accepted variants and test expectations. Do not represent future components as exported production primitives. Add explicit source precedence: registry/current executable component > current policy > historic HTML examples. Record differences as known drift, not automatic prescriptions to recreate them.

Agent task checklist: read repository instructions + machine contract before UI edits; identify product/marketing/document scope; import existing token/component rather than new values; preserve keyboard and mobile behavior; update registry for approved changes; run a small scoped drift check and relevant regressions; state actual tested viewports. Automated enforcement should reject decorative new palettes, heavy selected-card outlines, browser dialogs and unscoped global CSS in newly added UI files without blocking existing legacy debt.
