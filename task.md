# Task List - Fix User Drawer Permissions & Media Button Styling

- `[/]` 1. Research & Discovery
  - `[x]` Find restrictive check `administrator`/`cora_manager`/`cora_branch_manager` in `cora-real-estate.php`
  - `[x]` Reproduce permission save failure under `owner.studio@cora.local` in Playwright
  - `[x]` Check `.cora-media-select-btn` styles presence on active workspaces
- `[ ]` 2. Implementation
  - `[ ]` Update permission checks in 5 AJAX actions inside `cora-real-estate.php`
  - `[ ]` Add `.cora-media-select-btn` CSS rules in `cora-real-estate/admin-dashboard.php`
  - `[ ]` Add `.cora-media-select-btn` CSS rules in `cora-studio-ai-locked/admin-dashboard.php`
- `[ ]` 3. Verification
  - `[ ]` Verify `tests/e2e/test-save-permission.spec.ts` passes
  - `[ ]` Verify `tests/e2e/test-avatar-drawer.spec.ts` passes under `photography_studio`
  - `[ ]` Run full E2E regression suite to ensure zero regressions
