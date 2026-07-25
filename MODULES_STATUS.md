# Cora Platform — Module Progress & Branch Synchronization Manifest

> **Single Source of Truth for Multi-Branch Parallel Development**
> This file is automatically updated by the `module-sync-monitor` subagent to ensure parallel chats and feature branches remain fully synchronized and never break shared platform APIs or files.

---

## 1. Branch Index & Active Modules

| Module Name | Branch Name | Status | Main Touchpoint Files | Assigned Agent / Chat |
|---|---|---|---|---|
| **Core Platform** | `main` | 🟢 Stable Base | `cora-workspace.php`, `view-settings-suite.php` | Main Orchestrator |
<!-- MODULE_ROWS_START -->
<!-- MODULE_ROWS_END -->

---

## 2. Shared File Touchpoints & Conflict Guard

> [!IMPORTANT]
> If multiple feature branches modify any of the following shared files simultaneously, coordinators must review parameter signatures and line ranges to prevent merge conflicts:

- `app/public/wp-content/plugins/cora-workspace/cora-workspace.php` (Core AJAX Handlers & Hooks)
- `app/public/wp-content/plugins/cora-workspace/views/view-settings-suite.php` (Settings & Backup Views)
- `app/public/wp-content/plugins/cora-workspace/views/view-content-suite.php` (Content Suite)

---

## 3. Branch Activity & Progress Log

### `main` (Production Base)
- **Latest Commit**: `d29daee3` — `style(reviews): integrate official WhatsApp green receiver logo, official SMS icon badge, and official Google 4-color G logo in status pills`
- **Health**: 100% Operational & Clean Working Tree.

<!-- BRANCH_LOGS_START -->
*No active feature branches detected. Working tree clean.*
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
