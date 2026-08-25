# Cora for Studio - Workspace & Global Rules

This file outlines workspace rules, global execution guidelines, and design system standards that apply across this project.

## 1. Dialogue and Alert Guidelines
- **No Browser Defaults**: Never invoke browser-native dialogue overlays such as `alert()`, `confirm()`, or `prompt()`.
- **Custom Toast System**: Direct all alerts, errors, and confirmation feedback through the custom monochromatic Toast Notification system (`window.coraShowToast`).
- **Form Drawers**: Replace overlay modals or prompts for complex forms (such as adding shoot bookings) with right-sliding side drawer sheets to maximize screen layout efficiency.

## 2. Admin User and Bottom Sidebar Popovers
- **Sidebar Admin Popover**: The administrator widget for **Shruti** (Avatar `S`) must sit sticky at the bottom of the sidebar.
- **Floating Option Card**: Trigger options by clicking the widget to open a clean popover card directly above it (or floating next to it when collapsed) containing quick actions and quick settings.
- **Rich Interactive Elements**: Include a workspace status connection indicator, an active AI model selector (Gemini 3.5 Flash, Claude 3.5 Sonnet, GPT-4o), and quota metrics directly in the popover menu.

## 3. Visual Systems and Theme Rules
- **Monochromatic Palette**: Adhere strictly to the Notion/Shopify monochromatic visual palette (neutral shades `zinc-50` through `zinc-950`, pure white `#ffffff`, and pure black `#000000`) with zero colorful gradients or emojis.
- **Claude Cream B-Roll Theme**: All generated video assets, presentation slides, and B-rolls must follow the Anthropic Claude design theme with warm cream backgrounds (`#FBFaf7` or `#F9F6F0`).
- **Light/Dark Mode Support**: Maintain functional classes for light and dark modes, ensuring smooth theme switching with persistent preferences.
- **Clean SVG Iconography**: Utilize thin-lined vector SVGs (`stroke-width: 1.8` or `2.2`) for all indicator elements.
- **Typography Stack**: Use `Inter` for UI sans-serif text and `JetBrains Mono` for code, numbers, and identifiers.

## 4. Master Atomic Design System Specification
The Cora platform enforces a full 5-level Atomic Component Architecture defined in `cora-design-tokens.js` and benchmarked in `cora-design-system.html`:
- **Level 00 (Foundations & Tokens)**: 11-step neutral gray ramp (`zinc-50` to `zinc-950`), Claude Cream (`#FBFaf7`), 4-step state accents (Active 🟢, Pending 🟡, Critical 🔴, Neutral ⚪), 24-icon 1.8px vector SVG library, typography scale, radii, and elevation shadows.
- **Level 01 (Atoms)**: Button primitive matrix (Solid Primary, Secondary Outline, Soft Ghost, Destructive, Loading Spinner, Segmented Control Groups), Status Pills & Removable Tags, Input Controls (Text, Password Eye Toggle, URL Addons), File Upload Dropzone, Date & Call-Time Slot Pickers, Accordion Collapsible Panels, Tooltips & Keyboard Shortcuts (`⌘K`), Skeleton Loaders, and Administrator Avatar (**Shruti**, initials **S**).
- **Level 02 (Molecules)**: Guided 5-Step Progress Stepper (`1. Details` -> `2. Terms` -> `3. GST Math` -> `4. E-Sign` -> `5. Complete`), Aspect-Ratio Crop Preset Selector (`1:1`, `4:3`, `16:9`), GST Tax Calculation Breakdown Card, and Monochromatic Toast Cards.
- **Level 03 (Product SaaS Organisms - `cora-workspace`)**: Sticky Sidebar Bottom Admin Widget & Popover, Notion-Styled Interactive Data Table, Kanban Lead Funnel Pipeline, SEO & GEO Content Inspector, and Document Vault E-Sign Registry.
- **Level 04 (Marketing Site Organisms - `cora-frontend`)**: Monochromatic Hero Header Assembly, Marquee Brand Ticker, 3-Column Feature Cards, Interactive 3-Tier Pricing Matrix, and CTA Lead Capture Banner.

## 5. Plugin Packaging
- **Version Increment**: Whenever you are asked to package or zip the plugin for distribution, you MUST first increment the plugin version number in the main plugin file header (e.g. `cora-studio-ai.php`) before creating the zip archive.

## 6. Parallel Execution & Task Tracking (Global Workflow Rule)
- **Granular Task Decomposition**: Always break down every task into small, well-defined subtasks.
- **Parallel Subagent Dispatch**: Invoke subagents (`invoke_subagent`) in parallel to execute independent subtasks concurrently, maximizing speed and throughput.
- **Real-Time Task & Progress Tracker**: Maintain a dedicated live tracker artifact (`task.md` or progress status tracker) for every run displaying:
  - Completed items (`[x]`)
  - In-progress items (`[/]`) with assigned subagent roles/IDs
  - Pending/queued items (`[ ]`)
  - Estimated time/completion status for each subtask.

## 7. Safe Execution & Strict Module Isolation (Zero-Regression Policy)
- **Strict Module Isolation**: Updating, adding, or refactoring one feature, view, component, or module MUST NEVER disturb, break, degrade, or alter the UI layout, state, API contracts, or functionality of any other module across the Cora platform.
- **Scoped Namespacing**: All CSS styles, DOM selectors, JavaScript event listeners, and global variable keys must be strictly namespaced or scoped to their target component to prevent global collisions or cross-module interference.
- **Zero Side Effects**: Modifying shared utilities, core tokens (`cora-design-tokens.js`), or AJAX routers (`cora-workspace.php`) requires auditing all dependent views to guarantee backward compatibility and zero side effects.
- **Mandatory Regression Verification**: Before completing any task, execute regression verification checks across neighboring modules to confirm that existing workflows remain 100% functional and intact.

## 8. Local Test Environment Credentials (`http://cora.local`)
- **Login Portal URL**: `http://cora.local/workspace/login` or `http://cora.local/wp-login.php`
- **Platform Super Admin (Shruti)**: Email: `admin@cora.local` | Username: `cora_admin` | Password: `cora_secure_pass_123`
- **Real Estate Workspace Owner**: Email: `owner.realestate@cora.local` | Username: `re_owner` | Password: `cora_secure_pass_123` | Direct URL: `http://cora.local/workspace/dashboard?industry=real_estate`
- **Photography Studio Workspace Owner**: Email: `owner.studio@cora.local` | Username: `studio_owner` | Password: `cora_secure_pass_123` | Direct URL: `http://cora.local/workspace/dashboard?industry=photography_studio`
- **Provisioning Utility Script**: `php scripts/setup_local_accounts.php`

## 9. PWA & Mobile Performance Standard Operating Procedure (SOP)
- **Pure Light Mode PWA Splash**: `cora-manifest.json` and `<meta name="theme-color">` must always specify `#ffffff` to guarantee immediate zero-lag native splash screen rendering without jarring dark-to-light flash transitions.
- **Dynamic Versioned Icon Sync**: All PWA manifest icons, favicons, and Apple touch icons MUST include dynamic `?v=CORA_WORKSPACE_VERSION` query stamps. `cora-manifest.json` dynamically outputs versioned URLs to force OS and browser WebAPKs to auto-refresh app icons immediately on release.
- **Service Worker Cache Lifecycle**: The service worker (`cora-service-worker.js`) must automatically interpolate `CORA_WORKSPACE_VERSION` into cache namespace keys (`cora-workspace-v{VERSION}`, `cora-dynamic-v{VERSION}`). On activation, outdated version caches are purged immediately. HTML navigation uses sub-400ms network-first with instant cache fallback for sub-50ms screen painting.
- **Zero Artificial Delays**: Never add artificial `setTimeout` preloading or skeleton blocking delays to page loads or navigation handlers (`window.coraNavigateTo`). Hydration must occur instantaneously on `DOMContentLoaded`.
- **Standalone In-App Link Retention**: In standalone PWA mode (`navigator.standalone === true` or `(display-mode: standalone)`), all internal navigation MUST be retained inside the standalone WebApp window via the PWA link retention engine to prevent browser breakouts.
- **Mobile Touch Snappiness**: Maintain `touch-action: manipulation; -webkit-tap-highlight-color: transparent;` across all interactive elements (buttons, inputs, island nav, drawer sheets) to eliminate the mobile 300ms tap delay.

