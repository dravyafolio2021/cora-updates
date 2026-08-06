# Task List - Fix User Drawer Permissions & Media Button Styling

- `[/]` 1. Research & Discovery
  - `[x]` Find restrictive check `administrator`/`cora_manager`/`cora_branch_manager` in `cora-real-estate.php`
  - `[x]` Reproduce permission save failure under `owner.studio@cora.local` in Playwright
  - `[x]` Check `.cora-media-select-btn` styles presence on active workspaces
- `[ ]` 2. Implementation
  - `[x]` Implement PageSpeed AJAX endpoints and options in `cora-workspace.php`
  - `[x]` Add Localhost/Private domain detection and simulation fallback in the AJAX audit controller
  - `[/]` Update the Canvas Themes dashboard UI in `view-canvas.php` to fetch and render dynamic PageSpeed metrics
- `[ ]` 3. Verification
  - `[ ]` Verify `tests/e2e/test-save-permission.spec.ts` passes
  - `[ ]` Verify `tests/e2e/test-avatar-drawer.spec.ts` passes under `photography_studio`
  - `[ ]` Run full E2E regression suite to ensure zero regressions
