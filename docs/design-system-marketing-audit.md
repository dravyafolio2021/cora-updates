# Cora marketing frontend design audit

Source scope: cora-frontend/app and components, runtime global styles, Tailwind configuration, existing tokens, brand page, docs shell, homepage, feature directory and tool frameworks. Read cora-frontend/AGENTS.md and installed Next 16.3 layouts/pages and route-handler guides. This is a source audit; no visual browser inspection was performed. No credentials were read and no repository files were changed.

## Existing visual language to preserve

- White main canvas (#ffffff), zinc primary text (#09090b), zinc secondary copy (#52525b), zinc borders (#e4e4e7), muted background (#fafafa), black primary CTA with white label. The runtime CSS variables are the authoritative executed foundation: cora-frontend/styles/globals.css:6–21.
- Marketing typography is General Sans. It loads from Fontshare in globals.css:1 and heads both body and heading stacks in tailwind.config.ts:16–18. Inter is fallback body via --font-sans; Inter Tight is fallback heading via --font-display. JetBrains Mono is for code, values and technical metadata; Instrument Serif and Caveat are expressive options. next/font definitions: app/layout.tsx:14–46. Actual General Sans import supplies 200–700, so documentation claiming loaded 800 is inaccurate.
- Marketing has two layers: neutral functional UI and expressive scenic artwork. Hero.tsx:75–107 uses landscape imagery and sky gradients, while ArtisticHeroBackground.tsx:17–61 supplies a precision grid variant and :67 onward supplies sky art. This is already executed branding, so a prohibition on all colorful imagery would contradict product reality.
- Primary buttons commonly have 12px radius (rounded-xl), black fill, semibold 13–16px text, subtle shadow, right arrow. Secondary actions are white or transparent with zinc border/text. Hero.tsx:163–188 and DocsClient.tsx:169–181 show representative recipes.
- Cards commonly use white fill, 1px zinc 200 border, 16px radius, modest shadow. Spacious promotional cards use 24px radius. Inputs and action controls use 12px. Pills use full rounding. ToolCard.tsx:70–95 and brand components provide examples.
- Content widths: 1240px is main marketing standard (77 appearances); 1100px is editorial/tool width (17); docs uses 1440px with 290–300px left navigation. Gutters are 16px mobile / 24px sm and beyond. Hero.tsx:107, ToolPageShell.tsx:121, ToolContainer.tsx:37, DocsClient.tsx:126/194.
- Lucide icons dominate functional UI, usually 14–20px, stroke width around 1.8–2; third-party logos and illustrations retain source colors. ToolCard.tsx:86–94.

## Critical causes of recurring drift

1. Tokens are disconnected. Searching CORA_DESIGN_TOKENS finds only its declaration and type; no consumers. cora-frontend/lib/cora-tokens.ts:7–109 therefore describes intentions while UI independently repeats values.
2. Three contradictory typography specifications: tokens say Inter with 48px hero; /brand says Inter and 64px hero; runtime uses General Sans and homepage 36px mobile / 62px desktop hero. Evidence: cora-tokens.ts:61–73, BrandTypographySection.tsx:5–11/40, Hero.tsx:131.
3. State tokens conflict: tokens active solid #22c55e and warning #eab308; brand active #10b981 and warning #f59e0b; existing tool UI uses emerald and amber Tailwind utilities. Evidence: cora-tokens.ts:30–44, BrandColorsSection.tsx:30–34, ToolContainer.tsx:54–63. Preserve contexts explicitly rather than claim all legacy instances have been migrated.
4. Layout token marketingMaxWidth is 72rem/1152px, while most runtime marketing layouts are 1240px. cora-tokens.ts:91–95 versus Hero.tsx:107.
5. Radius token map omits a common executed size: lg=8px, xl=12px and 2xl=16px align runtime, but 3xl=24px is missing from the token export despite being explicitly configured in Tailwind and used for large cards. Explicitly publish 24px large cards.
6. General UI primitives are absent: components/ui contains only Toast and MadeInCoraBadge. Buttons, cards, fields, badges and overlays repeat inline class lists across the app. A public reference without reusable recipes and agent instructions will not prevent drift.
7. Brand color copy cards are clickable divs with no keyboard operation: BrandColorsSection.tsx:77–81 and :131–135. Existing Toast.tsx:27–33 has no role=status or aria-live. New design system examples should meet stronger accessibility requirements and identify older gaps.
8. Motion is not consistently reduced: Hero.tsx:23–62 always runs entrance/floating/parallax; SmoothScrollProvider.tsx:13–24 skips touch but does not inspect prefers-reduced-motion. Make reduced motion mandatory for future work without describing current components as compliant.
9. Tailwind 3.4 project uses newer/unconfigured utility names, e.g. shadow-2xs, shadow-xs, p-4.5, gap-4.5, xs:* and focus:outline-hidden. tailwind.config.ts defines neither custom shadows/spacing nor xs breakpoint and has no plugins. These names can silently produce no CSS. New reference should use compiled classes or semantic CSS recipes.

Aggregate source inventory across 322 TSX files: 287 distinct six-digit hex literals (includes artwork and third-party brands); 64 arbitrary max-width values (includes small internal layouts). This is evidence of decentralization, not 287 errors or a recommendation to collapse every art color.

## Reusable components and appropriate application

- ToolPageShell: existing utility shell. Category badge, back link, intro, trust points, AI persona promotion, tool canvas, outcomes, FAQs, related tools. Cream #FAFAF9 and max 1240px. Source components/tools/ToolPageShell.tsx:29–46/100–154.
- ToolContainer: newer editorial business tool shell with title, breadcrumb, execution badge, main canvas. Cream #FBFaf7 and max 1100px. components/tools/framework/ToolContainer.tsx:8–23/30–90.
- Tool framework: ToolNumberInput, ToolScoreBand, ToolFindingsList, ToolMethodologyCard, ToolPrivacyDisclosure, ToolWorkedExample, ToolFaqAccordion, ToolJsonLd, ToolContextualCoraCta, ToolMobileBar, ToolMobileBottomSheet, ToolMobileViewToggle. Import barrel components/tools/framework/index.ts. Reuse for business decision tools; do not create another calculator framework.
- ToolNumberInput uses 44px control targets and 16px mobile input text; however optional id can break label binding, and range input has no accessible name. components/tools/framework/ToolNumberInput.tsx:68–139.
- ToolMobileBottomSheet is already an established interaction but lacks Escape dismissal, dialog semantics, focus trap/return and safe area padding. components/tools/framework/ToolMobileBottomSheet.tsx:23–83. Do not advertise it as fully accessible without repairs.
- ToolCard and ToolCategoryHeroCard build the tools catalog. Tools index filters search/category state and uses rotating colored words, FAQs and AI input. app/tools/page.tsx:27–96. Use function-first labels, category filters and clear empty states.
- DocsClient is dedicated docs navigation with search, desktop left navigation, content and right TOC; Cmd/Ctrl+K and / search shortcuts; IntersectionObserver scroll spy. app/docs/page.tsx:23–26 and components/docs/DocsClient.tsx:44–105/193 onward. This is best precedent for public design-system slug.
- Feature directory has category/industry/search/status filters, desktop sidebar, mobile drawer and live/roadmap state. app/features/page.tsx:103–151. Reuse this organizational pattern for component search and categorized rules.
- Global Navbar hides on /docs; Footer retains docs footer but suppresses its promotional CTA. Navbar.tsx:153 and Footer.tsx:115–126. A design system can receive a dedicated documentation shell by applying the same explicit route treatment.
- Shared toast is useToast from components/ui/Toast.tsx:39. Use it for reversible copy success after adding live-region semantics, instead of native alert or a new page-specific notification style.
- Metadata and route patterns already exist for docs pages; machine-readable routes can live under /design-system/spec.json etc. Installed Next guide confirms page and route cannot be at the exact same route segment; place manifest endpoint in nested segment.

## Proposed canonical rules (recommendations, not historical user approvals)

Use one typed source for page content, machine manifest, downloadable Markdown, CSS token export and agent instructions. Version it and retain source evidence. Include implementationStatus=existing/adopt-for-new-work/recommended so machine readers can distinguish facts from choices. Do not conflate token changes with redesigning all product screens.

Publish separate marketing and product typography profiles: marketing General Sans with Inter/Inter Tight fallbacks and current family variables; product profile based on platform audit. Canonical marketing type: hero 36px mobile, 48px sm, 62px desktop; section 28–36px; card 18–20px; body 14px mobile / 16px regular; code 12–14px. Retain 10–12px only for nonessential metadata; never required form guidance. Avoid introducing new fonts or expressive serif to functional application forms.

Publish semantic colors (canvas/surface/subtle/text.primary/text.secondary/text.muted/border/default/action), then zinc raw ramp and functional status profiles. Controls remain monochrome. Colorful atmospheric art and third-party logo exceptions should be explicitly bounded to marketing artwork and authentic brand assets, not random tinted forms or action buttons.

Spacing: 4px base grid; 8/12/16/24/32/48/64/80/96 tokens. Marketing width 1240px; editorial 1100px; docs 1440px; reading prose ~680–760px; mobile gutters 16px then 24px. Each section should state profile and width rather than invent a arbitrary size.

Component contract should include import path, props/API, token bindings, allowed variants, responsive behavior, states (default/hover/focus/loading/disabled/error/empty/success), keyboard behavior, usage and non-usage rules, executable examples and source files. At minimum add shared button/card/badge/input recipes; prefer actual components where safe for new work.

Accessibility for new work: semantic buttons for copy/actions; persistent labels and help/error association; 44px minimum touch target; visible focus; single primary H1; named nav landmarks; keyboard operational tabs/accordions; overlay Escape, focus trap and return; non-color status labels; reduced motion; sticky/mobile bars safe-area padding; overflow only within code/table wrappers; no body overflow masking as a substitute for responsive layout.

Governance: root agent instructions must require reading public manifest/local schema before UI changes, reusing listed components and tokens, updating the registry when conventions change, and running a narrow drift check. A guardrail should inspect new/changed UI files for disallowed native alert/confirm/prompt and unapproved raw style values, with scoped exceptions for established art, third-party marks, dynamic user colors and existing legacy components. A source-link checker can ensure exported component paths remain valid. Public page and machine routes should be generated from one source and discoverable in llms.txt and footer/docs navigation.
