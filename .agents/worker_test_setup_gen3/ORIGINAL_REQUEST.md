## 2026-07-08T01:03:22Z
Objective: Setup Playwright E2E testing framework in the Cora project root, design and implement Tier 1-4 tests (Feature, Boundary, Pairwise, Workload) (>= 71 tests total), verify that they run and pass against the local WP site, and publish TEST_INFRA.md and TEST_READY.md at the project root.

Working Directory: `/Users/shrutian/Desktop/cora/.agents/worker_test_setup_gen3`

Your tasks:
1. **WP Site Check & Setup**:
   - Check which local URL is responsive: `http://cora.local` (port 80 or port 10003), `http://localhost:10003`, or `http://127.0.0.1:10003`.
   - Setup a `package.json` in the project root `/Users/shrutian/Desktop/cora/` (installing `@playwright/test` and `typescript` as devDependencies).
   - Write a `playwright.config.ts` in the project root targeting the responsive base URL. Configure it to support headless mode, trace on first retry, and standard settings.
   - Credentials for testing: `cora_admin` / `cora_secure_pass_123` via `/wp-login.php`. Implement an authentication setup (e.g., using global setup or a login helper) to log in as administrator and access `/workspace`.

2. **Design & Implement Tests**:
   Create the following E2E test files under `/Users/shrutian/Desktop/cora/tests/e2e/`:
   - `tier1-feature-coverage.spec.ts` (30 tests)
     - Pages (5 tests): list view; create page (title, slug, template, publish); edit page; change status; delete page (via confirm modal).
     - Comments (5 tests): list view; filter by status; approve pending; reply to comment via reply drawer; spam/trash.
     - Appearance (5 tests): save branding options (tagline, logo, favicon); select menu; create menu; add menu item; remove menu item.
     - Tools (5 tests): view diagnostics & copy; XML export run; XML import run; GDPR export run; GDPR erase run.
     - Media-Editor (5 tests): view media list; apply crop ratio; rotate/flip; save transformation; save SEO metadata.
     - Settings-Suite (5 tests): tab navigation; save general; save reading/writing; save discussion; save permalinks (flush rewrite).
   - `tier2-boundary-cases.spec.ts` (30 tests)
     - Pages (5 tests): empty title; duplicate slug; long title; cancel deletion; search/filter empty.
     - Comments (5 tests): empty reply; double moderation; cancel deletion; HTML/script in reply; empty comment list state.
     - Appearance (5 tests): save brand empty; create menu empty name; create menu duplicate name; custom menu invalid URL; long menu label.
     - Tools (5 tests): GDPR export invalid email; GDPR erase invalid email; XML import invalid file; export empty selection; GDPR erase non-existent email.
     - Media-Editor (5 tests): empty SEO title/alt; extremely long SEO inputs; transform without image; invalid scale inputs; custom crop 0 size.
     - Settings-Suite (5 tests): empty site title; invalid admin email; negative post limit; invalid permalink custom structure; offline/AJAX fail toast.
   - `tier3-pairwise-combinations.spec.ts` (6 tests)
     - Pages + Settings-Suite (Reading): Create page, set as homepage in Settings-Suite, verify.
     - Media-Editor + Appearance: Upload/select image, set as Agency Logo URL in Appearance, verify.
     - Pages + Comments: Create page with comments, post comment, approve comment in Comments feed.
     - Pages + Appearance (Menu): Create page, add to menu, verify.
     - Tools + Pages: Create page, run XML export (Pages only), verify references, delete page.
     - Settings-Suite (Permalinks) + Pages: Change permalink structure, verify page view links change format.
   - `tier4-workload-scenarios.spec.ts` (5 tests)
     - Site Launch Prep: Brand settings, permalinks, home/privacy pages, homepage assignment.
     - Moderation workflow: Feed check, reply inquiry, spam mark, approve valid.
     - Navigation/Branding update: Create 3 pages, create menu, add pages, remove old, save logo.
     - Media SEO optimization: Select image, crop, rotate, save SEO metadata, insert on a page.
     - GDPR request: Verify comments, export user data, GDPR erase user data, verify comments are anonymized.

   *Note*: Since the current codebase might have stubs or AJAX mock logic (e.g. `coraSaveEditedImage` or `coraRunXMLImport` only showing toast alerts, or missing `coraShowSelectedImportFile` causing ReferenceError unless mocked/declared), ensure that your test assertions verify the UI elements, toasts, and behaviors that are *actually* present. For example, if saving transformation only triggers a toast saying 'Media updated successfully.', assert that toast exists!

3. **Verify and Generate Reports**:
   - Run the full test suite using `npx playwright test`.
   - Ensure all tests run successfully and pass.
   - Publish `TEST_INFRA.md` in the project root containing:
     - The feature inventory, test runner info, and how to execute.
     - Verification checklist.
   - Publish `TEST_READY.md` in the project root with the test summary, counts per tier, and coverage table.
   - Provide a detailed handoff report when complete.
