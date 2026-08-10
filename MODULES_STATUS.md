# Cora Platform — Module Progress & Branch Synchronization Manifest

> **Single Source of Truth for Multi-Branch Parallel Development**
> This file is automatically updated by the `module-sync-monitor` subagent to ensure parallel chats and feature branches remain fully synchronized and never break shared platform APIs or files.

---

## 1. Branch Index & Active Modules

| Module Name | Branch Name | Status | Main Touchpoint Files | Assigned Agent / Chat |
|---|---|---|---|---|
| **Core Platform** | `main` | 🟢 Stable Base | `cora-workspace.php`, `view-settings-suite.php` | Main Orchestrator |
<!-- MODULE_ROWS_START -->
| **Email Management** | `feature/email-management` | 🟢 Feature Complete | `views/view-emails.php`, `cora-workspace.php` | Email Module Agent |
| **File Manager** | `feature/document-studio-vault` | 🟢 Merged to Main | `views/view-vault.php`, `cora-workspace.php` | Dedicated Vault Agent |
| **Forms & Reviews** | `feature/forms-module` | 🟢 Merged to Main | `views/view-forms.php`, `public-form-view.php`, `views/view-emails.php` | Dedicated Forms Agent |
| **Media Module** | `feature/media-module` | 🟢 Merged to Main | `views/view-media.php`, `views/view-media-editor.php`, `cora-workspace.php` | Media Module Agent |
| **Studio Module** | `feature/studio-module` | 🟡 Active Work | `cora-workspace.php`, Studio Views & Features | Studio Module Agent (Arjun) |
| **Content Module** | `feature/content-ai-copilot` | 🟢 Merged to Main | `views/view-content-suite.php` | Content Module Agent (AI Copilot) |
| **Lead Management** | `feature/lead-management` | 🟡 Active Work | `views/view-leads.php`, `cora-workspace.php`, `admin-dashboard.php` | Lead Suite Agent |
| **Frontend Module** | `feature/frontend-module` | 🟡 Active Work | `cora-frontend/*`, `cora-workspace.php` | Frontend Module Agent |
<!-- MODULE_ROWS_END -->

---

## 2. Shared File Touchpoints & Conflict Guard

> [!IMPORTANT]
> If multiple feature branches modify any of the following shared files simultaneously, coordinators must review parameter signatures and line ranges to prevent merge conflicts:

- `app/public/wp-content/plugins/cora-workspace/cora-workspace.php` (Core AJAX Handlers & Hooks, Universal Auto-Save Engine)
- `app/public/wp-content/plugins/cora-workspace/views/view-emails.php` (Email Center View & Outbox Composer)
- `app/public/wp-content/plugins/cora-workspace/views/view-vault.php` (Document Vault & Document Studio Wizard)
- `app/public/wp-content/plugins/cora-workspace/views/view-settings-suite.php` (Settings & Backup Views)

---

## 3. Branch Activity & Progress Log

### `main` (Production Base)
- **Latest Commit**: `27114d09` — `fix(content-suite): complete structural overhaul and event handler repair across all 7 tabs v3.2.88`
- **Health**: 100% Operational & Clean Base.

<!-- BRANCH_LOGS_START -->
### `feature/frontend-module` (Active Branch)
- **Status**: 🟡 Active — Frontend Module: Lovable Sync Integration and Marketing Frontend.
- **Main Touchpoint**: `cora-frontend/index.html`, `cora-workspace.php`.

### `feature/lead-management` (Active Branch)
- **Status**: 🟡 Active — Enterprise Lead Management Suite: Drag & drop Kanban funnel, searchable directory table, funnel & revenue analytics, sliding side drawers (deal details, create/edit lead, schedule follow-up), lead activity timeline, direct email outreach, and lead conversion to client.
- **Main Touchpoint**: `views/view-leads.php`, `cora-workspace.php`, `admin-dashboard.php`.

### `feature/content-ai-copilot` (Merged Branch)
- **Status**: 🟢 Merged to `main` — Content AI Suite: Complete structural overhaul across all 7 tabs (v3.2.88), custom 32px padding (v3.2.87), consolidated Myra assistant, removed dark mode (v3.2.83), and locked portrait orientation (v3.2.85).
- **Main Touchpoint**: `views/view-content-suite.php`.

### `feature/studio-module` (Active Branch)
- **Status**: 🟡 Active — Studio Features, Studio Booking & Management, Equipment & Crew Integration, and Studio Suite workflows.
- **Main Touchpoint**: `cora-workspace.php`, `views/view-crew-scheduler.php`, Studio plugin assets & views.

### `feature/email-management` (Active Feature Branch)
- **Status**: 🟢 Feature Complete — Enterprise Studio Email Suite: Sub-Tabs, Live Preview Card, Template Studio, Drip Sequences, Outbox Logs Table, Hostinger SMTP Settings, and Diagnostic Drawers.
- **Main Touchpoint**: `views/view-emails.php`, `cora-workspace.php`.

### `feature/document-studio-vault` (Merged Feature Branch)
- **Status**: 🟢 Merged to `main` (commit `edc5d5a4`) — Document Vault Dashboard, 5-Step Guided Document Studio Wizard, E-Sign Audit Registry, Quality Health Check, GST math, reactive KPI cards, watermark preview.
- **Main Touchpoint**: `views/view-vault.php`, `cora-workspace.php`.

### `feature/forms-module` (Merged Feature Branch)
- **Status**: 🟢 Merged to `main` (commit `121ba12f`) — Multi-channel review settings, WhatsApp-first automation rules with Hinglish presets, Notion-styled form inputs.
- **Main Touchpoint**: `views/view-forms.php`, `public-form-view.php`, `views/view-emails.php`.

### `feature/media-module` (Merged Feature Branch)
- **Status**: 🟢 Merged to `main` (commit `e27bc7f3`) — Studio-grade Media Library Dashboard with folder navigation, search & MIME filtering, bulk toolbar, right-sliding detail sheet, folder email sharing, storage meter, dynamic aspect-ratio crop presets (1:1, 4:3, 16:9), transformations, and SEO metadata manager.
- **Main Touchpoint**: `views/view-media.php`, `views/view-media-editor.php`, `cora-workspace.php`.
<!-- BRANCH_LOGS_END -->

---

## 4. Multi-Agent Development Protocol

1. **Before Starting Work on a New Module**:
   - Check `MODULES_STATUS.md` to see if your target shared files are currently being modified by another active branch.
   - Run `git checkout main` → `git pull` → `git checkout -b feature/<module-name>`.

2. **During Feature Development**:
   - Keep commits granular with prefix: `feat(<module>): ...` or `fix(<module>): ...`.
   - Update your branch status in this file before pushing or requesting merge.

3. **Before Merging to Main**:
   - Run `git diff main..feature/<module-name> --stat` to ensure only module-scoped files were touched.
