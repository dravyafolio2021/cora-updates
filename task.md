# Task: Email Module & Canvas Themes UI/UX Overhaul

## Current Status
- Overall Status: [x] Completed
- Target Platform Version: `v4.9.198` / `v4.9.199`
- Active Branch: `feature/workspace-development-2026-09-22`
- Focus: Streamline `view-emails.php` and `view-canvas.php` — compact 2x2/1x4 KPI analytical cards, sticky sub-navigation tabs (~36px height), responsive preview cards, dynamic cross-module integration, and zero visual clutter.

---

## Subtask Breakdown

### Phase 1: Email Communications Module Overhaul
- [x] Analyze and refactor `view-emails.php` top KPI cards into high-density 2x2 (mobile) / 1x4 (desktop) tonal cards (Rule 13 zero-outline compliant)
- [x] Implement sticky sub-navigation tabs (~36px height with touch pan-x and underline indicator)
- [x] Remove injected header buttons and restore clean workspace header styling
- [x] Integrate industry-personalized email templates dynamically for Real Estate, Photography Studio, Marketing, Stationery, and Professional Services
- [x] Integrate cross-module dynamic CRM contacts from `wp_cora_clients` and `wp_cora_leads`
- [x] Run Playwright E2E verification test suite (`tests/test_email_module.js`) across mobile and desktop

### Phase 2: Canvas Themes Hub Revamp
- [x] Standardize workspace header via `cora_render_workspace_header()` with title `'Canvas Themes'`, AI stack badges, and `+ Add Theme` CTA
- [x] Consolidate Speed & PageSpeed cards into a high-density 4-card metric strip (Core Web Vitals, LCP Speed, Themes Library Quota, Published Pages)
- [x] Implement sticky sub-navigation tabs (Theme Overview, Draft Library, Speed & Core Web Vitals, Migration & Tools) with `localStorage` persistence
- [x] Overhaul Active Theme card with responsive mini-browser mockup and compact action buttons (`Customize`, `Settings`, `Live Site`, `Duplicate`, `··· Actions`)
- [x] Organize draft themes, speed diagnostic tools, and 1-click migration cards into dedicated sub-tab containers
- [x] Validate PHP syntax and run Playwright E2E test suite (`tests/e2e/test-canvas-revamped.spec.ts`) across desktop (1440x900) and mobile (390x844)
- [x] Capture visual verification screenshots and update `walkthrough.md`
