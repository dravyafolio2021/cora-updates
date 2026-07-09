## 2026-07-08T01:28:27Z
You are a worker. Your working directory is /Users/shrutian/Desktop/cora/.agents/worker_m4_gen2.
Your task is to execute Milestone M4 (Packaging):
1. Find all PHP files in `app/public/wp-content/plugins/cora-real-estate`.
2. Run syntax check (`php -l`) on each PHP file using the PHP binary `/Users/shrutian/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php` and verify there are no syntax errors.
3. Clean the workspace of any temporary or unnecessary files (such as `.DS_Store`, patch files, or other temporary residues) that should not be in the workspace or the package. Do NOT delete critical configuration, `.agents` coordinate logs, or test files.
4. Compress the `app/public/wp-content/plugins/cora-real-estate` plugin directory into `cora-real-estate-v0.1.zip` in the workspace root `/Users/shrutian/Desktop/cora`. Ensure the zip file has a top-level directory structure `cora-real-estate/` containing the plugin files (so that when unzipped, WordPress extracts it into `cora-real-estate/`).
5. Write your handoff report to `/Users/shrutian/Desktop/cora/.agents/worker_m4_gen2/handoff.md`, listing the syntax checked files, deleted temporary files, and verification of the created zip package.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All packaging must be genuine. Do not bypass syntax checks or fabricate ZIP contents.

When done, send a message back to the parent indicating completion.
