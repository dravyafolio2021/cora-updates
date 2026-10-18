# Task Tracker: Standardize & Update Official AI Tool Logos Globally

## Task Overview
The user requested:
> "Make it standard and update globally on all teh Commponent, 
> I want to have Official logo instead of this customSVG icon of these AI tools"
(Referencing the overlapping 5-icon stack showing ChatGPT/OpenAI, Claude, Gemini, Perplexity, YouTube).

Replace all custom, inaccurate, and monochromatic placeholder SVGs across the platform with authentic, official vector brand logos and official brand colors (OpenAI green `#10A37F`, Claude terracotta/amber `#D97706`, Gemini Google gradient sparkle `#4E82EE`→`#9B72CB`→`#D96570`, Perplexity teal `#20808D`, and YouTube red `#FF0000` + `#FFFFFF` play button).

---

## Subtasks & Execution Status

- [x] **Subtask 1: Universal Official Brand Logos Engine (Frontend & TypeScript)**
  - Status: Completed
  - Target: `cora-frontend/components/features/OfficialBrandLogos.tsx`, `cora-frontend/components/features/CapabilityVisualCard.tsx`, `cora-frontend/components/showcase/ProductShowcase.tsx`
  - Checklist:
    - [x] `OpenAILogo` / `ChatGPTLogo`: Official OpenAI rosette in `#10A37F`
    - [x] `ClaudeLogo`: Authentic Anthropic Claude asterisk burst in `#D97706` (replacing custom polygon)
    - [x] `GeminiLogo`: Authentic Google Gemini 4-point sparkle with official multi-stop gradient
    - [x] `PerplexityLogo`: Official Simple Icons Perplexity geometric knot in `#20808D` (replacing custom isometric cube)
    - [x] `YouTubeLogo`: Official YouTube `#FF0000` rounded container with crisp `#FFFFFF` play triangle
    - [x] Clean re-exports and import in `CapabilityVisualCard.tsx` and `ProductShowcase.tsx`
    - [x] TypeScript compilation check passed (`npx tsc --noEmit` code 0)
    - [x] Next.js production build check passed (`npm run build` code 0, 338 routes compiled)

- [x] **Subtask 2: Workspace PHP Universal Brand Logo Engine**
  - Status: Completed
  - Target: `app/public/wp-content/plugins/cora-workspace/includes/workspace-header.php`
  - Checklist:
    - [x] Define standard reusable function `cora_get_ai_platform_official_logo($platform, $class, $size, $monochrome)`
    - [x] Support `'openai'`, `'chatgpt'`, `'claude'`, `'anthropic'`, `'gemini'`, `'google'`, `'perplexity'`, `'youtube'`
    - [x] PHP syntax lint check passed (0 syntax errors)

- [x] **Subtask 3: Update `.cora-platform-stack` in Workspace Header (Desktop & Mobile)**
  - Status: Completed
  - Target: `app/public/wp-content/plugins/cora-workspace/includes/workspace-header.php`, `assets/css/admin-style.css`
  - Checklist:
    - [x] Replace custom SVGs in desktop `.cora-platform-stack` (lines 460-504) with official brand logos
    - [x] Replace custom SVGs in mobile `.cora-platform-stack` (lines 540-562) with official brand logos
    - [x] Standardize circular button containers (`bg-white dark:bg-zinc-800`, subtle border `border-zinc-200/90 dark:border-zinc-700/80`, smooth hover scale, and z-index elevation)

- [x] **Subtask 4: Update Global Components Across Workspace**
  - Status: Completed
  - Target: `views/view-mcp.php`, `views/view-canvas.php`, `views/view-settings-suite.php`, `admin-dashboard.php`
  - Checklist:
    - [x] `view-mcp.php`: Update Model Selector dropdown (Gemini, Claude, OpenAI) with official logos
    - [x] `view-mcp.php`: Update "Connect AI Assistant" cards and modal JS (ChatGPT, Claude, Gemini) with official logos
    - [x] `view-canvas.php`: Update MCP client connection cards and connect modal JS with official logos
    - [x] `view-settings-suite.php`: Update Model Cards Grid (Gemini, Claude, ChatGPT 4o) with official logos
    - [x] `admin-dashboard.php`: Replace emojis (`⚡`, `✦`, `⚙️`) in sidebar popover active AI model selector with official SVG logos
    - [x] `admin-dashboard.php`: Add official SVG logos to Settings drawer active model radios

- [x] **Subtask 5: Verification & Quality Assurance**
  - Status: Completed
  - Checklist:
    - [x] PHP syntax check on all modified PHP files (`php -l` on all files: 0 errors)
    - [x] TypeScript & Next.js production build verification (0 errors)
    - [x] Rule 3 privacy compliance audit (zero owner name occurrences)
    - [x] Rule 8 zero-regression verification across neighboring modules
