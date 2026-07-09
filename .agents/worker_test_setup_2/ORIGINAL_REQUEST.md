## 2026-07-08T01:40:26Z
You are a Worker agent (implementer, qa, specialist).
Your working directory is /Users/shrutian/Desktop/cora/.agents/worker_test_setup_2.
Please create your own folder /Users/shrutian/Desktop/cora/.agents/worker_test_setup_2 for your progress.md, BRIEFING.md, and handoff.md.

### Mission
Set up Playwright E2E testing infrastructure in Cora workspace, design and implement Tier 1-4 tests, run them against http://cora.local (port 10003 or port 80), and publish TEST_INFRA.md and TEST_READY.md at the project root.

### Objectives
1. Verify the WP site URL and accessibility:
   - Source `app/.envrc` from the repository root and run WP-CLI commands (like `wp --path=app/public option get siteurl` and `wp --path=app/public plugin list`) to verify the WordPress local environment.
   - Verify the site URL (should be http://cora.local or similar).
2. Setup Playwright E2E infrastructure in the project:
   - Create a clean `package.json` in the workspace root `/Users/shrutian/Desktop/cora` containing necessary dependencies (playwright, typescript, ts-node, @types/node, etc.) and run `npm install` to set up the environment.
   - Create `playwright.config.ts` targeting `http://cora.local` (or the port verified from WP-CLI, typically http://localhost:10003 or http://cora.local:10003 or port 80).
   - Ensure the config does not run the web server if it is already running.
3. Design E2E test cases across 4 tiers:
   - Tier 1: Feature Coverage (>=5 tests per module for the 6 core replacement modules: pages, comments, appearance, tools, media-editor, settings-suite; total >=30 tests).
   - Tier 2: Boundary & Corner Cases (>=5 tests per module; total >=30 tests).
   - Tier 3: Cross-Feature Combinations (pairwise interactions; total >=6 tests).
   - Tier 4: Real-World Application Scenarios (total >=5 tests).
4. Create the test files under `/Users/shrutian/Desktop/cora/tests/` (e.g., `tests/tier1.spec.ts`, `tests/tier2.spec.ts`, etc., or structured by feature/tier).
5. Ensure the E2E tests use `cora_admin` / `cora_secure_pass_123` to authenticate via `/wp-login.php`. Implement a shared auth fixture or login helper.
6. Write `TEST_INFRA.md` in the workspace root following the template:
   - Test Philosophy (opaque-box, requirement-driven, no dependency on implementation details).
   - Feature Inventory (6 modules, table of features with count of Tier 1-3 tests).
   - Test Architecture (runner, format, layout).
   - Real-World Application Scenarios (Tier 4 details).
7. Run the test suite on the host against the local site. Note that since the plugin implementation track is in progress (some features may be placeholders/mismatched), some tests might fail. That is expected. Ensure you capture the command output and test results.
8. Generate the finalized E2E test results summary and publish `TEST_READY.md` at the project root containing:
   - Test Runner command.
   - Coverage Summary (Tier 1-4 count).
   - Feature Checklist.
9. Deliver your work by writing a detailed `handoff.md` report in your working directory and notifying the parent agent.

### Mandatory Integrity Warning:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

### Constraints & Workspace Rules:
- Adhere strictly to the workspace rules in `/Users/shrutian/Desktop/cora/.agents/AGENTS.md`.
- No browser defaults (use custom toast `window.coraShowToast` if triggering alerts in tests, though opaque-box E2E tests should just verify elements).
- Do not access external websites or services (CODE_ONLY mode).
