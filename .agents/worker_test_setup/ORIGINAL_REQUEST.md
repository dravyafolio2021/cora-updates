## 2026-07-08T00:54:23Z
You are a Worker. Your working directory is /Users/shrutian/Desktop/cora/.agents/worker_test_setup.
Your task is to set up a Playwright E2E testing infrastructure in `/Users/shrutian/Desktop/cora/` and implement the test cases for Tiers 1, 2, 3, and 4.

Please complete the following steps:
1. **WP Site URL Verification**:
   Sourcing `app/.envrc`, run WP-CLI command `wp --path=app/public option get siteurl` to verify the actual URL of the site. Use this URL (e.g., `http://cora.local` or with its port if any) as the `baseURL` in the Playwright config.
2. **Infrastructure Setup**:
   - Create a `package.json` in the project root containing:
     - Name: `cora-e2e-tests`
     - Version: `1.0.0`
     - Scripts: `{"test": "playwright test"}`
     - DevDependencies: `@playwright/test` and `typescript` (or use NPX to run). Since Node.js v22.23.0 is available, run `npm install --save-dev @playwright/test` or construct package.json directly and run npm install.
   - Create `playwright.config.ts` in the project root configured to run tests inside `tests/` directory with `baseURL` set to the site URL, headless: true, and video/screenshots on failure.
3. **Admin Authentication**:
   - Write a helper/fixture to handle logging in with credentials `cora_admin` / `cora_secure_pass_123` via `/wp-login.php`.
   - Store state/cookies if possible, or perform login in `beforeEach` for the test files.
4. **Implement Test Cases**:
   Create the following files in `tests/` directory:
   - `tests/tier1-feature-coverage.spec.ts`: Contains >=30 tests covering features (>=5 per module for Pages, Comments, Appearance, Tools, Media-Editor, Settings-Suite).
   - `tests/tier2-boundary-cases.spec.ts`: Contains >=30 boundary and corner case tests (>=5 per module).
   - `tests/tier3-pairwise-combinations.spec.ts`: Contains >=6 cross-feature pairwise interaction tests.
   - `tests/tier4-real-world-scenarios.spec.ts`: Contains >=5 real-world application scenarios.
   
   Note: Some views (Comments, Appearance, Tools, Media Editor) have known bugs or stubs (e.g., JavaScript casing mismatches, missing endpoints). Write the tests to verify the *expected correct behavior* under the specification. Note that if any tests fail because the plugin has stubs/bugs, this is acceptable for now (the E2E Testing Track's goal is to implement the tests and establish the baseline, and the subsequent Milestones will fix the plugin code to make the tests pass). You can use assertions that check for the expected correct UI outputs (e.g., toast messages, element visibility, database state via WP-CLI).
   
5. **Run the Test Suite**:
   Run `npm run test` or `npx playwright test` on the host to execute all implemented test cases. Document which tests passed and which failed (failures are expected due to the identified bugs/stubs in the plugin).
6. **Generate Handoff**:
   Write a detailed handoff report in /Users/shrutian/Desktop/cora/.agents/worker_test_setup/handoff.md documenting:
   - package.json and playwright.config.ts content.
   - List of all implemented tests by tier.
   - Commands run to install and execute the tests.
   - Test execution results (count of passes, fails, and descriptions of failures corresponding to known plugin bugs).
7. **Notify me** when complete.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.
