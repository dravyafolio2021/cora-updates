# Cora Platform — CRM Lead Detail Drawer Systematic Overhaul (`feature/crm-module-suite`)

## Active Task: 3-Tier Structured Drawer Architecture

- [x] Phase 0: Task Decomposition & Architecture Blueprinting <!-- id: 100 -->
- [x] Phase 1: Header / Top Section (Orange Box) — Quick Control Panel & Dynamic Lead Card Preview <!-- id: 101 -->
  - [x] High-contrast avatar initial + Dynamic Lead Title + Format & Hub Subtitle
  - [x] 5-Channel Outreach Cluster (WhatsApp, Phone, Email, Instagram, Website)
  - [x] Dynamic Shoot Date & Follow-up Badges
  - [x] Width Snap Presets (30%, 50%, 70%) & Close Button
- [x] Phase 2: AI Lifecycle Summary (Yellow Box) — Interactive Synthesis Deck <!-- id: 102 -->
  - [x] Compact AI Executive Summary Card
  - [x] 1-Tap "Summarize with AI" interactive lifecycle analysis
  - [x] "Recommended Next Move" strategic action pill
- [x] Phase 3: Interactive Controls & Metrics (Purple Box) <!-- id: 103 -->
  - [x] Pipeline Stage Dropdown + Intent Pills (🔥 Hot, ☀️ Warm, ❄️ Cold, 🟢 Won)
  - [x] 4-Stat Metric Deck (Deal Value, Stage, AI Match %, SLA Timer)
  - [x] "Convert to Client" primary action button
- [x] Phase 4: Grouped Detail Forms (View / Edit / Create) <!-- id: 104 -->
  - [x] Group 1: Contact & Client Profile (with quick city hub chips)
  - [x] Group 2: Commercial Scope & Deal Terms
  - [x] Group 3: Timeline & Milestone Progression
  - [x] Group 4: Team Assignment & Internal Notes
  - [x] Subtabs: Automations, Intake Checklist Tasks, Audit Trail & AI Logs
  - [x] Sticky Bottom Actions: Delete Lead, Cancel, Save Deal Changes
- [x] Phase 5: JavaScript Hydration & Interaction Synchronization <!-- id: 105 -->
  - [x] Synchronized `coraOpenLeadDetailDrawer` in `views/view-leads.php` and `assets/js/admin-script.js`
  - [x] Hydrated social links, dynamic date badges, AI summary, metric cards, and form fields
  - [x] Enforced scroll reset (`scrollTop = 0`) and responsive clamping (`360px` to `70vw`)
- [x] Phase 6: Tailwind Compilation & Verification <!-- id: 106 -->
  - [x] Recompiled `tailwind-built.css`
  - [x] Regression verification across CRM board and directory table



