## 2026-07-08T03:50:13Z
You are teamwork_preview_worker (worker archetype).
Your working directory is /Users/shrutian/Desktop/cora/.agents/worker_m5_verify_tests/.

YOUR MISSION:
1. Verify the local WordPress environment is running and accessible (e.g. check `curl -I http://cora.local`).
2. Run PHP syntax check (`php -l`) on all PHP files in `app/public/wp-content/plugins/cora-real-estate` using Local WP's PHP binary at `/Users/shrutian/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php`.
3. Extract `cora-real-estate-v0.1.zip` from `/Users/shrutian/Desktop/cora/` to a temporary directory, and verify:
   - There are 38 files/directories.
   - The root folder is `cora-real-estate/`.
   - Run PHP syntax check (`php -l`) on all PHP files inside the zip package.
   - Check that no hidden/temporary files (like `.DS_Store`, `__MACOSX`, backup/git files) exist inside the zip.
4. Run the full Playwright E2E test suite using `npx playwright test`. Verify that all 73 tests pass successfully.
5. Write your handoff.md and progress.md in your working directory.
6. Send a message to your parent when done.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.
