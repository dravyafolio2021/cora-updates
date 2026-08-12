# Task List - Website Statistics UI Refinement & Server Deployment

- `[x]` 1. Research & Discovery
  - `[x]` Identify divide-y border collision with rounded row corners in `view-canvas.php`
  - `[x]` Verify border properties on Row elements via E2E playwright checks
- `[x]` 2. Implementation
  - `[x]` Remove divide-y classes from container in `view-canvas.php`
  - `[x]` Deployed custom straight separator divs between stats rows
  - `[x]` Increment plugin version to 3.4.0 in `cora-workspace.php` and `updates/cora-workspace.json`
- `[x]` 3. Build release
  - `[x]` Run `build.sh` to package releases zip at `updates/cora-workspace.zip`
- `[x]` 4. Commit and Push
  - `[x]` Stage and commit changes locally
  - `[x]` Push changes to origin main
  - `[x]` Bump version to `3.4.0` and build release zip
- `[x]` 5. Perform deployment to staging and production
  - `[x]` Run deploy script to push zip package to server via SSH
