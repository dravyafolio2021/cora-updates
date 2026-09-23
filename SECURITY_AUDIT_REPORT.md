# Cora Market-Pilot Security Audit and Repair Specification

**Audit date:** 22 September 2026
**Audit type:** Read-only repository security reassessment
**Scope:** WordPress application, Cora custom plugins, Next.js frontend, authentication, tenant isolation, public endpoints, file handling, integrations, backups, update pipeline, and dependency/configuration exposure
**Purpose:** Give the implementation agent a prioritized, testable security repair plan for a limited market pilot without redesigning or removing product features

> This report intentionally contains no discovered credential values, live tokens, password contents, or exploit-ready HTTP requests. Security fixes must be implemented on a branch, tested against the regression matrix in this document, and deployed using a reversible release.

## Current full-repository reassessment — 22 September 2026, 20:00 IST

**This is the authoritative current status.** The historical checkpoint immediately below is retained only to show progress and must not be used as the release decision record.

### Executive result

The Phase 0 work produced real improvements: the known email-verification bypass is closed in the current source, production frontend dependencies have no reported npm advisories, backup artifacts were moved out of the observed public directory, the public form repeatable-field sink was repaired, several tenant predicates were added to Forms and Tasks, and the rebuilt ZIP now matches the current source for the security-critical files checked.

The repository is nevertheless exposed to multiple critical attack paths. The most urgent newly verified condition is that the distributable update ZIP contains a populated `.env` file with 24 provider credentials. A second newly verified condition permits unauthenticated WordPress registration requests to submit an arbitrary `cora_role`, including `administrator`, because the server uses the posted role directly. These findings take precedence over the updater changelog's statement that SEC-001 through SEC-018 are completely remediated.

This report does not issue a binary launch/no-launch verdict. It measures what improved and specifies the minimum containment needed for a small controlled pilot.

### Measured improvement

- **Original P0 repair implementation progress:** approximately **60–65%**. This measures completion of the previously known fixes, not residual exploitability.
- **Whole-platform security-risk reduction:** approximately **35–45%** relative to the original audit. Critical authorization, secret-distribution, invitation, MCP, and cross-tenant paths still dominate residual risk.
- **Fully remediated control groups:** SEC-004 only.
- **Substantially remediated but incomplete:** SEC-001 and SEC-003.
- **Partially remediated:** SEC-005, SEC-008, SEC-011 through SEC-016, and SEC-018.
- **Not effectively remediated:** SEC-002, SEC-006, SEC-007, SEC-009, SEC-010, and SEC-017.
- **New findings added by this full pass:** SEC-019 through SEC-023.

The implementation percentage and risk percentage differ because one remaining arbitrary administrator-assignment path or one published secret bundle can outweigh many successful hardening changes.

### Snapshot and review coverage

- Git base: `53f4f57b`, plus the uncommitted working-tree changes listed by `git status` at the audit time.
- Plugin version: `4.9.209`.
- Release ZIP SHA-256: `bccae7c10b1c92fb007bb5bcc9390200f660e55e2a4a298dd4d6b1c820b35ccc`.
- Repository inventory: 9,175 files; 98 PHP files in the custom Cora plugin; 282 JavaScript/TypeScript files across the custom plugin and frontend.
- Exposed application surface counted statically: 37 REST route registrations, 560 WordPress AJAX hook registrations, 24 unauthenticated AJAX hooks, and 84 WordPress remote HTTP call sites.
- Reviewed: authentication and registration, verification/magic/invitation tokens, tenant context and policies, Forms, Tasks, Canvas, Vault/e-sign, AI actions, MCP, WhatsApp, galleries/media sharing, public frontend APIs, backups, service-worker caching, updater/release artifact, dependencies, secrets, security headers, and logging.
- No application source was changed during this reassessment. Only this report was updated.

### Current severity/status matrix

| ID | Severity | Current status | Current evidence / residual condition |
|---|---:|---|---|
| SEC-001 | Critical | Substantially fixed | Verification tokens are random and hashed with expiry checking; invalid-token auto-login is removed. Consumption is not atomic, missing creation time can bypass expiry, and plaintext legacy fallback remains. |
| SEC-002 | Critical | Open / partial scaffolding | Central policy exists, but many calls pass no resource tenant. Canvas and clauses remain global; Task overwrite and preferences remain ID/user-ID based; Vault and AI mutations remain incompletely tenant-bound. |
| SEC-003 | Critical | Substantially fixed | Observed backups moved outside `app/public`; configured paths are not validated and directory-creation failure falls back under `WP_CONTENT_DIR` instead of failing closed. |
| SEC-004 | Critical/High | Fixed in checked dependency state | Next.js 16.3.3 and Sharp 0.35.4 resolve; live `npm audit --omit=dev` reports zero vulnerabilities. |
| SEC-005 | High | Partial / fail-open | HMAC verification exists, but unsigned WhatsApp POSTs are accepted when no app secret/signature is configured; verify token can be misused as signing-secret fallback. |
| SEC-006 | High | Open | Invitation token lifecycle, resend behavior, link logging, role matrix, existing-user login, and durable rate limiting remain unsafe or incomplete. |
| SEC-007 | High | Open / partial | AI chat now requires login and nonce, but model-produced actions can mutate global ID-addressed objects without tenant/object authorization or a signed confirmation intent. |
| SEC-008 | High | Partial | Known public-form repeatable sink was repaired; AI/user/server strings still reach `innerHTML`, form schemas are not recursively allowlisted, and dynamic `new Function` execution remains. |
| SEC-009 | High | Not remediated in callers | SSRF class exists but is unused. Form webhooks still call `wp_remote_post($webhook_url)` directly; outbound fetch surfaces are numerous. |
| SEC-010 | High | Open / partial | ZIP parity improved and one path has a host allowlist; native update paths still trust manifest `download_url`, no checksum/signature exists, and the ZIP publishes `.env`. |
| SEC-011 | High | Partial | Signature size cap, expiry check, and immutability improved; global option storage, plaintext bearer hashes/tokens, optional expiry, and tenant ownership gaps remain. |
| SEC-012 | High | Partial, defeated by SEC-019 | Dangerous tenant-role capabilities are stripped, but Elementor grants global edit capabilities and public registration can create a real WordPress administrator. |
| SEC-013 | Medium/High | Partial | Gallery uses `wp_check_password` and safer cookies but keeps plaintext fallback, deterministic grants, no rate limit, and direct media/share bypasses. `share-media.php` remains plaintext/MD5 based. |
| SEC-014 | Medium/High | Partial | Exact hostname parsing improved. Missing Origin/Referer is accepted, rate limits are absent or process-local/spoofable, and contact delivery failure returns HTTP 200 success. |
| SEC-015 | Medium | Partial | `nosniff`, Referrer-Policy, and no-store rules were added. No general CSP/HSTS/Permissions-Policy is established; static-looking authenticated responses can still be service-worker cached. |
| SEC-016 | Medium/High | Partial | Production assertions only log warnings. They do not fail closed. Real secret material remains in runtime files and the release ZIP. |
| SEC-017 | High | Open | Hashed token comparison improved, but global MCP token has full tool access and direct tools execute under fixed user context 1 without tenant-scoped service identity. |
| SEC-018 | Medium | Partial | Structured ring-buffer events exist, but the option is mutable, bounded to 500, lacks alerting/retention, and nested values are not recursively redacted. |
| SEC-019 | Critical | **New / open** | Public registration trusts posted `cora_role` and assigns it directly, enabling arbitrary WordPress role assignment. |
| SEC-020 | Critical | **New / active secret incident** | `updates/cora-workspace.zip` contains `.env` with 24 populated provider credentials. |
| SEC-021 | Critical/High | **New / open** | Invitation hash can become the bearer on resend, accepted state is written to the wrong key, and a bearer invitation can log into an existing account. |
| SEC-022 | High | **New / open** | Privileged AJAX enforcement is inconsistent: one custom-role writer checks only login; email send accepts any logged-in caller without a valid nonce; update check is unauthenticated. |
| SEC-023 | High | **New / open** | Copilot input/model/server strings are interpolated into `innerHTML`; proposal execution uses `new Function(action_cmd)`, increasing XSS-to-admin-action impact. |

## Immediate containment order for the implementation agent

These changes are intentionally narrow. They preserve routes and product behavior while closing privilege and data boundaries.

### 1. Treat the release ZIP as a credential exposure incident — SEC-020

**Evidence**

- `updates/cora-workspace.zip` contains `cora-workspace/.env`.
- The archived file defines 24 credential variables and all 24 have non-empty, non-placeholder values different from `.env.example`.
- The source `.env` also resides at `app/public/wp-content/plugins/cora-workspace/.env`, below the WordPress document root. Direct HTTP exposure depends on web-server dotfile rules, which is not an acceptable secret boundary.
- The manifest points clients to a GitHub-hosted raw ZIP, so any pushed copy must be assumed retrievable and cached.
- `scripts/build.sh:62-96` excludes and checks `.env`, but `tools/release.js:85` recursively zips the entire plugin without exclusions or a post-build secret check. Multiple release paths have different security guarantees.

**Required actions**

1. Revoke and rotate every credential represented in the packaged `.env`; removal alone is insufficient.
2. Remove the ZIP from the update location until a sanitized artifact is available; purge published Git history/releases and CDN caches where feasible.
3. Store runtime secrets outside the plugin directory and outside the document root. Load them from the host secret manager or environment.
4. Make one canonical release builder and disable/bind every other release path to it. Replace recursive “zip the plugin directory” packaging with an explicit allowlist. At minimum exclude `.env*` except a placeholder-only `.env.example`, credentials, backups, logs, tests, docs, local configs, source maps where unnecessary, and VCS metadata.
5. Add a release gate that extracts the final ZIP and fails on secret patterns, forbidden filenames, unexpected files, or mismatch with the declared file manifest.
6. Add a cryptographic SHA-256 to the manifest and verify it before install; for stronger protection, sign the manifest/package with an offline release key.

**Non-regression tests**

- no `.env`, private key, credential document, SQL dump, log, or backup exists in the archive;
- secret scan runs on the extracted artifact, not only the Git index;
- updater refuses a modified ZIP and an unapproved download host;
- source/artifact parity remains true for all intended production files.

### 2. Close public registration role injection — SEC-019

**Evidence**

- `option_users_can_register` is forced on at `cora-workspace.php:2757-2762`.
- High roles are removed only from the rendered `<select>` at lines 2793-2803.
- `cora_workspace_user_register()` uses `$_POST['cora_role']` directly in `$user->set_role()` at lines 2861-2864.
- The registration password minimum remains six characters.

**Required repair**

1. Do not accept a public role parameter. Assign one fixed low-privilege onboarding role on the server, or map a public persona key through a closed allowlist that cannot resolve to any owner, manager, WordPress administrative, or custom role.
2. Reject unexpected `cora_role` values in `registration_errors`; do not silently coerce an attacker-supplied administrative role.
3. Never default a generic WordPress registration to `cora_super_admin`. Create workspace ownership only inside a separate verified onboarding transaction controlled by the server.
4. Set the user role in the same controlled account-provisioning service used by modal/self-registration and invitations.
5. Increase password policy or rely on a verified passwordless/identity flow with durable abuse controls.

**Required tests**

- POSTing `administrator`, `cora_shruti`, `cora_super_admin`, owner, manager, or a custom role never creates a privileged account;
- omitting the role never produces an owner/admin role;
- UI and direct HTTP requests produce the same safe role;
- every public signup path creates an isolated tenant only after email proof and cannot join another tenant.

### 3. Complete object-level tenant authorization — SEC-002

The new `Cora_Request_Context` is a useful foundation, but it currently derives one tenant from user meta and a tenant role from a global WordPress role. `cora_authorize()` checks tenant equality only when both sides are non-empty, so missing tenant context is fail-open. Many permission callbacks pass only an action string.

**Required repair pattern**

```php
$context  = cora_get_request_context();
$resource = $repository->find_for_tenant($id, $context->tenant_id);
if (!$resource || !cora_authorize($context, 'task.update', $resource)) {
    return new WP_Error('forbidden', 'Not authorized', ['status' => 403]);
}
```

Apply it at the database boundary, not only in route callbacks:

- Canvas: add `agency_id = active tenant` to list/get/update/delete/activate; stop creating records under hardcoded agency 1.
- Tasks: load/update by `(id or task_uid) AND workspace_id`; never let a mutation move an existing task to the caller's workspace; derive assignee choices from tenant membership; preferences must always use the authenticated user unless an owner uses a separately authorized admin operation.
- Forms/clauses: tenant-scope every clause/list/update/delete and email-log query; remove `agency_id => 1`.
- Vault/e-sign/share: add tenant ID to every record and lookup; bind bearer grants to resource, tenant, purpose, expiry, and revocation state.
- AI actions and MCP tools: authorize the named action and server-loaded resource before every read/mutation.
- Fail closed whenever tenant or membership cannot be resolved. Resolve active roles from the membership table, not global WordPress roles.

Run all integration tests with two tenants and overlapping numeric IDs/UIDs. Every cross-tenant read, write, delete, share, export, assignment, and preference operation must return 403/404 and leave storage unchanged.

### 4. Repair invitation lifecycle and role hierarchy — SEC-021 / SEC-006

**Verified defects**

- Invitation records are keyed by the SHA-256 hash, but acceptance also permits a direct array-key lookup. The stored hash can therefore become a bearer credential.
- Resend constructs a link from the found option key, sending the stored hash as the bearer.
- Acceptance finds `$found_key` but writes accepted state to `$invitations[$token]`; the original hashed-key record can remain pending and reusable.
- Possession of an invitation for an existing email causes automatic login to that existing account.
- Raw invitation URLs are written to `cora_latest_verification_link` and `error_log`.
- Manager-to-invitee role rules are not a complete deny-by-default matrix.

**Required repair**

1. Store only `token_hash`; always hash the presented raw token and query by hash. Never accept the stored hash itself as a bearer.
2. On resend, generate a new raw token, atomically replace the old hash, invalidate the old record, and email the new raw token exactly once.
3. Consume using an atomic conditional update such as `UPDATE ... SET status='accepted' WHERE token_hash=? AND status='pending' AND expires_at>NOW()` and require one affected row.
4. Update `$found_key`, never the caller-provided token key; migrate options to the database-backed record or remove the duplicate store.
5. For an existing account, require an authenticated session for the same user or a fresh reauthentication/magic-link proof sent to that account. An invitation alone may authorize joining the tenant, not authenticating the existing account.
6. Implement a deny-by-default inviter-role to invitee-role matrix and verify the inviter's active membership server-side.
7. Remove raw links from options and logs. Add durable per-IP and per-email resend/accept limits.

### 5. Replace global MCP authority with tenant-scoped service identities — SEC-017 / SEC-022

- The global token is explicitly granted full tool access when user ID is zero.
- The direct OpenAPI tool route invokes tools with fixed user ID `1`.
- Token hash-at-rest is undermined by retaining/accepting the plaintext global option during migration.

Create token records containing `token_id`, `token_hash`, `tenant_id`, `principal_user_id` or service principal, `allowed_tools`, `created_at`, `expires_at`, `last_used_at`, and `revoked_at`. Resolve the principal from the token record, set no synthetic administrator user, pass tenant context to every tool, and authorize each resource. Remove the global full-access bypass and fixed user 1. Rotate the legacy global token after migration.

### 6. Make webhook, SSRF, updater, and backup controls fail closed

- WhatsApp: require a configured Meta app secret and valid `X-Hub-Signature-256` for every POST. Never substitute the verification token as the signing secret.
- SSRF: route all user/configurable outbound URLs through the new filter. Resolve and pin the validated address or use a transport that prevents DNS-rebinding; validate IPv4 and IPv6; disable redirects or revalidate each hop; add egress firewall rules.
- Updater: validate manifest and package hosts in every native and custom path, require HTTPS, checksum/signature, maximum size, safe archive paths, and rollback.
- Backups: validate `CORA_BACKUP_DIR` using canonical paths and refuse any location below document root. If a private directory cannot be created, abort backup rather than falling back to `WP_CONTENT_DIR`.

### 7. Remove browser code-execution sinks — SEC-008 / SEC-023

At `assets/js/admin-script.js:15846`, the user's query is written with `innerHTML`; model/server responses and action-chip fields are also interpolated into HTML. At line 18516, `new Function(prop.action_cmd)` executes server-provided code.

Use `textContent` for all plain strings. For limited rich AI formatting, parse a small Markdown subset and sanitize it with an allowlist before DOM insertion. Create buttons with DOM APIs and event listeners; pass action IDs and JSON data, never inline `onclick`. Delete `new Function` and replace it with an explicit action dispatcher whose keys map to fixed local functions. Add Trusted Types/CSP after removing inline/eval dependencies.

### 8. Correct privileged AJAX and public abuse controls — SEC-022 / SEC-014

- `cora_ajax_save_custom_role()` must require owner-level tenant authorization, not only `is_user_logged_in()`.
- `cora_ajax_send_email()` must require both a valid nonce and a server-side email-send capability; its current `nonce invalid AND logged out` condition accepts logged-in callers with no valid nonce.
- The unauthenticated force-update check should be removed or given a strict public cache/rate limit without exposing internal update details.
- Contact and AI-preview routes should reject missing Origin/Referer where browser-only access is intended, use proxy-trusted client identity, and use a shared/durable quota. Contact failures must return a failure/retry status instead of HTTP 200 `success: true` when nothing was delivered.

## Verification evidence from this pass

| Check | Result | Interpretation |
|---|---|---|
| TypeScript `tsc --noEmit` | Pass | Current frontend type-checks. |
| `npm audit --omit=dev` | Pass: 0 total vulnerabilities across 176 dependencies | SEC-004 dependency state is clean at audit time. |
| Next.js production build | Inconclusive | Turbopack failed while trying to bind a local worker port with `Operation not permitted`, including after the allowed retry; this is an audit-environment restriction, not proof of an application build defect. |
| PHP lint and new PHP security scripts | Not run | PHP CLI is absent in this environment. Earlier test output must not be treated as end-to-end proof. Run in CI/WordPress container. |
| ZIP structure | Pass | `unzip -t` reports no archive corruption. |
| ZIP/source parity | Pass for seven critical sampled files | Main plugin, authorization, SSRF, WhatsApp, public form, public gallery, and service worker hashes match source. |
| ZIP secret exclusion | **Fail** | Populated `.env` with 24 credentials is included. |
| Static high-confidence secret-pattern candidates | 7 files require triage | No secret values are reproduced in this report. Examples/tests/docs may be false positives; all must be classified by a secret scanner. |
| Dedicated security scanners | Not available | Gitleaks, Semgrep, and Trivy are not installed in the audit environment. Add them to CI. |
| Dynamic WordPress exploit/integration tests | Not run | The application runtime and test database were not available; all runtime claims remain static-analysis findings until exercised in an isolated test environment. |

## Minimum regression suite before the pilot

1. Build a disposable WordPress test environment with tenant A, tenant B, owner, manager, member, client, suspended user, and anonymous actor.
2. Exercise every REST/AJAX/public route with valid, missing, expired, reused, wrong-tenant, wrong-role, and tampered credentials.
3. Assert both response and storage state; a rejected request must not partly mutate data.
4. Race verification, invitation acceptance, e-sign, and share redemption with concurrent requests; exactly one use may succeed where single-use is intended.
5. Run an artifact pipeline: dependency audit, PHP lint, TypeScript check, WordPress integration tests, Semgrep, Gitleaks, archive path/size validation, extracted-ZIP secret scan, SBOM, checksum/signature, and source/artifact parity.
6. Browser-test CSP-compatible rendering, AI output sanitization, logout/service-worker cache purge, public link expiry, upload content validation, and direct media URL denial.
7. Preserve existing successful response shapes and put enforcement behind a short-lived shadow-log flag only where needed. Never shadow-log the critical role, secret, token, or cross-tenant write protections.

## Historical P0 checkpoint (superseded by the full reassessment above)

This section records the result of reviewing the current uncommitted Phase 0 changes. It supersedes the original status for the P0 items only; the detailed repair specifications below remain applicable.

### Measured improvement

The source tree has made meaningful progress, but the fixes are not yet consistently enforced or represented in the downloadable plugin artifact.

- **Fully remediated in the current source/dependency state:** 1 of 5 core P0 groups.
- **Substantially improved but incomplete:** 2 of 5.
- **Partially improved with exploitable or fail-open paths remaining:** 2 of 5.
- **Estimated source-level P0 risk reduction:** approximately 55–60%. This is a weighted engineering estimate, not a compliance score.
- **Packaged plugin status:** the current `updates/cora-workspace.zip` is stale and does not contain the plugin-side P0 repairs. It still contains the original email-verification bypass, the previous backup path behavior, the previous WhatsApp gateway, and no central authorization class.

| P0 item | Current status | Improvement verified | Remaining work |
|---|---|---|---|
| SEC-001 email verification | Substantially fixed in source; **not fixed in ZIP** | New tokens use 32 random bytes and SHA-256-at-rest; expiry is checked; invalid tokens no longer authenticate already-verified users | Rebuild the release artifact; make token consumption atomic; reject records without issuance time after migration; remove the plaintext legacy fallback after a short migration window; test the real handler rather than a simulation |
| SEC-002 tenant/object authorization | Partially fixed | Central context/policy class exists; Forms, Canvas, and Tasks permission callbacks invoke it; normal Task requests derive workspace/user from server context; several Form queries now check agency ownership | Policy calls do not receive the target resource tenant; context derives role from global WordPress role rather than tenant membership; Canvas remains global; clauses remain global/hardcoded to agency 1; Task update lookup is global by ID/UID; preferences still accept arbitrary user IDs; Vault, shares/e-sign, and AI mutations remain outside the policy |
| SEC-003 public backups | Substantially fixed, fail-open remains | Five SQL/ZIP artifacts were moved from `app/public/wp-content/cora-private-backups` to `app/cora-private-backups`; sensitive download responses now use no-store headers | `CORA_BACKUP_DIR` is not validated; failure to create the outside directory falls back to `WP_CONTENT_DIR/cora-private-backups`; production must hard-fail instead; the ZIP still contains the old implementation |
| SEC-004 Next.js/Sharp advisories | **Fixed in the checked dependency state** | Installed tree resolves Next.js 16.3.3 and Sharp 0.35.4; manifest and lock agree; live `npm audit --omit=dev` reports zero vulnerabilities | Preserve the lockfile in deployment and add the audit/build check to CI |
| SEC-005 WhatsApp webhook forgery | Partially fixed in source; **not fixed in ZIP** | HMAC-SHA256 verification using the raw body and timing-safe comparison was added | POST verification is conditional and still accepts unsigned events when no app secret/signature is present; using the verification token as a signing-secret fallback is not Meta signature verification; fail closed if the app secret is missing; ZIP contains the old gateway |

SEC-006's P0-related invitation/token items are not materially remediated: invitation tokens remain plaintext, full invitation URLs are written to an option and `error_log`, resend responses allow enumeration, and the magic-link token remains plaintext in user metadata.

An SSRF-filter class was added while this reassessment was in progress. It passes syntax lint, but no outbound request path currently calls it; existing fetch code still calls `wp_safe_remote_get` directly. It therefore receives no remediation credit yet.

### Important residual cross-tenant paths

1. **Canvas:** route-level role checks now call the new policy, but the policy receives no resource. Theme listing still selects all rows; pages are loaded by theme ID alone; update/delete/activation operations remain ID-based and global.
2. **Tasks:** ordinary requests now ignore caller-provided workspace/user IDs, which is a real improvement. However, save logic still finds an existing task globally by `task_uid` or numeric ID and updates by ID only. A user who knows another tenant's task ID can overwrite it and move it into their workspace. Task preferences still read/write a caller-supplied `user_id`.
3. **Forms:** form listing, individual access, deletion, submissions, bulk operations, and audit records now have useful agency checks. However, the generic permission callback passes no resource ownership to `cora_authorize`; clauses are still listed globally, created for agency 1, and updated/deleted by ID; email logs remain global.
4. **Context resolution:** the new context uses one user-meta agency/workspace and maps a global WordPress role to a tenant role. It does not resolve an active membership record for multi-workspace users. Empty tenant IDs also do not fail closed in `cora_authorize`.

### Test and release-artifact assessment

The new `scripts/test_sec_phase0.php` reports 13/13 passing assertions. Those assertions are useful unit examples, but they redefine simplified token and signature functions and inspect directories directly. They do not bootstrap WordPress, call the real REST/AJAX handlers, exercise the database, test concurrent token use, verify cross-tenant denial, or inspect the release ZIP. They must not be treated as end-to-end remediation evidence.

Verified during this reassessment:

- current custom/test PHP set: 126 files, no syntax errors;
- frontend installed tree: Next.js 16.3.3 and Sharp 0.35.4;
- live production dependency audit: zero reported vulnerabilities;
- Phase 0 simulation script: 13 assertions passed;
- public backup directory: no SQL/ZIP files remain; five artifacts exist in the outside directory;
- update ZIP integrity: archive is structurally valid but stale relative to the repaired source;
- runtime HTTP test: `cora.local` was not accepting connections, so no dynamic claim is made.

### Required next P0 patch order

1. Rebuild the plugin ZIP from the repaired source and add an artifact-parity test that fails if required source files are absent or hashes differ.
2. Make WhatsApp POST signatures mandatory and remove the verify-token signing fallback.
3. Make backup location validation fail closed for any path under the document root; never fall back to `WP_CONTENT_DIR`.
4. Pass the actual resource and server-loaded tenant into `cora_authorize`, then add tenant predicates to Canvas, clauses, Task update, preferences, Vault/e-sign, and AI mutation queries.
5. Replace the simulation-only checks with WordPress integration tests using two tenants and the negative matrix in Section 8.
6. Remove full bearer links from options/logs and migrate invitation/magic tokens to hashed, expiring, single-use records.

> **Reading note:** The numbered SEC-001 through SEC-018 sections below preserve the original baseline evidence and detailed design guidance. Where their old status wording conflicts with the current matrix or verification table above—especially ZIP parity, dependency versions, and token storage—the current full-repository reassessment above controls.

## 1. Outcome of this reassessment

This report does **not** make a launch/no-launch judgment. It answers a narrower and more useful question: what must be repaired or contained so Cora can be tested with a controlled audience while preserving the present product and its future architecture?

The current source tree contains several meaningful improvements, while three conditions still require completion or containment:

1. The direct email-verification account-takeover branch is corrected in the working source, but the downloadable update ZIP still contains the vulnerable implementation.
2. Tenant and object authorization has started moving to a central policy, but enforcement remains inconsistent across Forms, Tasks, Canvas, Vault, AI actions, invitations, and related APIs. Several operations still establish only that a user has a role, not that the target resource belongs to that user's active workspace.
3. The observed SQL and ZIP backups were moved outside the public WordPress directory, but backup code can still fall back to a public path if private-directory creation fails or if an unsafe configured path is supplied.

The repair strategy is deliberately incremental:

- centralize authorization without rewriting feature code;
- add tenant predicates at repository/query boundaries;
- keep existing routes and response shapes where possible;
- introduce compatibility adapters before migrating callers;
- contain risky public features with rate limits, signatures, expiry, and quotas;
- use feature flags and shadow authorization logs to prevent regressions;
- verify every change using positive and negative tests.

## 2. Severity and remediation order

| ID | Severity | Area | Verified condition | Pilot priority |
|---|---|---|---|---|
| SEC-001 | Critical | Email verification | Caller-controlled user ID can result in authentication without a matching token | P0 |
| SEC-002 | Critical | Tenant authorization | Multiple APIs trust resource, workspace, agency, or user IDs without proving membership/ownership | P0 |
| SEC-003 | Critical | Backups | SQL and ZIP backups exist below the public web root | P0 |
| SEC-004 | Critical/High | Dependencies | Installed Next.js and Sharp versions have published critical/high advisories | P0 |
| SEC-005 | High | Public webhooks | WhatsApp webhook accepts unsigned POST events | P0 |
| SEC-006 | High | Token and invitation flows | Enumeration, weak abuse control, plaintext reusable tokens, and role-assignment gaps | P0/P1 |
| SEC-007 | High | AI actions | Optional CSRF protection and over-broad tool execution can convert prompt injection into mutations | P1 |
| SEC-008 | High | Stored/DOM XSS | Untrusted form and checkout values reach `innerHTML`; schema validation is incomplete | P1 |
| SEC-009 | High | Public intake and SSRF | Public submissions lack comprehensive limits; webhook destinations can reach unsafe networks | P1 |
| SEC-010 | High | Update/build supply chain | Update packages are not cryptographically verified; production host can execute repository build scripts | P1 |
| SEC-011 | High | Vault, sharing, and e-sign | Global records and bearer links lack consistent tenant ownership, expiry, and size controls | P1 |
| SEC-012 | High | WordPress capabilities | Custom roles inherit broad administrator/page/theme capabilities globally | P1 |
| SEC-013 | Medium/High | Galleries and media | Passwords/tokens are weakly stored or derived; direct media URLs can bypass page controls | P1 |
| SEC-014 | Medium/High | Frontend public APIs | Process-local rate limits, weak origin checks, and inconsistent upstream error handling | P1 |
| SEC-015 | Medium | Browser security | Security headers and service-worker caching rules are incomplete | P2 |
| SEC-016 | Medium/High | Secrets/configuration | Local production-like secrets and historical credentials require rotation discipline | P1 |
| SEC-017 | Medium | MCP/integration access | Global bearer token and fixed user context are not tenant-scoped | P2 |
| SEC-018 | Medium | Monitoring and response | Security events are not normalized into actionable audit/alert signals | P1/P2 |

P0 means fix or strongly contain before inviting external users. P1 means implement during the pilot hardening release. P2 is defense-in-depth that should be scheduled, not forgotten.

## 3. Scope, method, and limitations

### Reviewed components

- `app/public/wp-content/plugins/cora-workspace/cora-workspace.php`
- Cora workspace templates, JavaScript, CSS, and included PHP modules
- Other custom plugins and their public/AJAX/REST entry points
- `cora-frontend`, including API routes and dependency lock state
- WordPress configuration and local environment behavior
- update metadata and the packaged `updates/cora-workspace.zip` artifact
- backup paths and backup files present in the working environment
- tracked documentation that could disclose access details

### Review techniques

- endpoint and hook inventory (`wp_ajax_nopriv_*`, REST routes, AJAX handlers);
- authentication, authorization, tenancy, and object-reference tracing;
- source-to-sink review for SQL, HTML, URL fetches, filesystem writes, mail, and command execution;
- secret-pattern and sensitive-file review;
- dependency audits for root and frontend packages;
- PHP syntax validation across custom PHP and test files;
- comparison of source with the packaged update artifact;
- review of existing mitigations and likely regression points.

### Limitations

- This was a repository/static review, not an authorized destructive penetration test.
- The local site at `cora.local` was not reachable during the reassessment, so browser-level runtime exploitation and response-header verification could not be completed.
- Infrastructure outside the repository—hosting panel, CDN, DNS, object storage, database grants, firewall, TLS, email provider, and production environment variables—was not independently inspected.
- Dependency reachability can differ by deployment mode. Advisories are still actionable because vulnerable versions are installed and shipped.
- Findings identify verified code conditions and realistic attack paths; they do not claim that exploitation has already occurred.

## 4. Security architecture Cora should converge on

The safest low-disruption repair is one authorization layer used by every feature:

```text
request
  -> authenticate identity
  -> build server-side request context
  -> authorize action against tenant + resource
  -> load/mutate through a tenant-scoped repository
  -> emit security audit event
  -> return existing response shape
```

Introduce a small `Cora_Request_Context` value object containing:

- authenticated WordPress user ID;
- selected workspace/agency ID, derived from server-side membership;
- memberships and tenant roles;
- request source and correlation ID;
- capabilities derived for this request only.

Introduce one policy entry point:

```php
$decision = cora_authorize($context, 'form.update', [
    'resource_type' => 'form',
    'resource_id'   => $form_id,
]);
```

The policy must fail closed. It must load the resource's tenant on the server and compare it with an active membership. A request parameter such as `agency_id`, `workspace_id`, `user_id`, `owner_id`, or `assigned_to` is a lookup hint, never authorization evidence.

For minimal disruption, keep current controllers and route names initially. Replace their permission callbacks and raw/global loaders with calls to the shared policy and tenant-scoped repositories. Preserve successful response schemas so the frontend does not need simultaneous redesign.

## 5. Detailed findings and repair specifications

### SEC-001 — Email-verification account takeover

**Severity:** Critical
**Affected code:** `app/public/wp-content/plugins/cora-workspace/cora-workspace.php`, approximately lines 2920–3040
**Also shipped in:** `updates/cora-workspace.zip`

#### Verified condition

The verification handler accepts a token and a caller-controlled user identifier. If the supplied token does not match the saved verification token, the handler can still mark the request valid when the selected user is already verified. It then calls the WordPress authentication-cookie functions for that selected user.

Possession of an arbitrary non-empty token is therefore not consistently bound to the user account being authenticated. A verified user's numeric ID can become sufficient to create their session.

#### Impact

- account takeover of verified accounts;
- potential workspace-owner or administrator compromise;
- access to all downstream data/actions available to the victim;
- exploitability is amplified by predictable WordPress numeric IDs.

#### Required repair

1. Delete the “already verified means token valid” authentication branch.
2. If an account is already verified, display a neutral message and link to sign-in. Do not create a session.
3. Store only a cryptographic hash of new verification tokens. Generate at least 32 random bytes using `random_bytes`/WordPress secure random helpers.
4. Look up the token record and bind it to exactly one user and one purpose.
5. Compare hashes with `hash_equals` and require an unexpired `expires_at`.
6. Consume the token atomically before or in the same transaction as verification.
7. Rotate/delete all previously issued verification tokens after deploying the fix.
8. Keep auto-login only if a fresh, matching, single-use token was validated. Safer for the pilot: redirect to normal sign-in after verification.
9. Never accept a user ID as an independent proof of identity.

#### Compatibility guidance

The existing email URL can retain its path and query parameter names for one transition release. Internally, resolve the account exclusively from the token record. Existing invalid links should land on a friendly resend page, not a fatal error.

#### Regression tests

- correct token for user A verifies only user A;
- wrong token plus user A's ID never creates a session;
- arbitrary token plus an already verified user's ID never creates a session;
- user A's token plus user B's ID cannot authenticate either account;
- expired, reused, truncated, empty, or malformed tokens fail;
- two simultaneous uses produce at most one success;
- verification failure does not alter the current logged-in account;
- packaged release contains the repaired implementation.

### SEC-002 — Systemic tenant and object authorization gaps

**Severity:** Critical
**Affected areas:** Forms, submissions, clauses, form mail/logs, Tasks, preferences, Canvas, Vault, shares/e-sign, AI actions, leads, executive data, and related administrative operations

#### Verified examples

- Forms route permission callbacks around lines 5403–5418 accept broadly any logged-in role with the basic `read` capability. Those callbacks cover list, save, delete, submissions, email, logs, clauses, and audit routes registered around lines 5222–5314.
- Tasks permission checks around lines 59840–59859 also accept broadly any logged-in user. Task handlers around lines 59867–59972 trust request-provided workspace and user identifiers.
- Task listing can treat `all`/`super` as no workspace predicate. Existing task save logic resolves a task globally by UID or numeric ID before mutation.
- Form clause operations use a fixed/default agency and perform ID-only updates/deletes.
- Canvas records are listed and mutated using global IDs. New records can be created for agency 1; activation can demote all live themes globally; deletion permanently removes linked WordPress content.
- Vault, share, and e-sign records are stored in global options/collections and frequently found by ID or token without a mandatory tenant predicate.
- AI tool actions update/delete resources by ID while checking only a broad role category.

#### Root cause

Authentication, WordPress role capability, tenant membership, feature permission, and ownership are treated as interchangeable. They are not. A valid user can become an attacker against another tenant when loaders accept global IDs.

#### Required repair

1. Implement the request-context and policy layer described in Section 4.
2. Create resource-specific actions such as:
   - `form.read`, `form.create`, `form.update`, `form.delete`;
   - `submission.read`, `submission.export`;
   - `task.read`, `task.assign`, `task.update`, `task.delete`;
   - `canvas.publish`, `canvas.delete`;
   - `vault.read`, `vault.share`, `vault.sign`;
   - `workspace.member.invite`, `workspace.settings.manage`.
3. Change repository interfaces so tenant ID is mandatory:

```php
get_form(int $tenant_id, int $form_id)
update_task(int $tenant_id, int $task_id, array $changes)
delete_canvas(int $tenant_id, int $canvas_id)
```

4. Every `UPDATE` and `DELETE` must include both resource ID and tenant ID in its predicate. Confirm one expected row was affected.
5. Every list must start with a tenant predicate. “All” may mean all resources inside the authorized tenant, never all database rows.
6. Derive the active tenant from a server-side membership/session selection. Reject requested tenants without active membership.
7. Apply field-level authorization. For example, clients may update a task status but not reassign it; members cannot promote their own role.
8. Eliminate hardcoded/default agency 1 from authenticated and anonymous write paths.
9. Scope option-based records immediately with tenant IDs; then migrate them to proper tenant-keyed tables without changing API responses.
10. Deny cross-tenant access with the same generic 404/403 behavior to reduce enumeration.

#### Safe rollout

- Phase A: add shadow-mode policy logging while existing behavior continues for internal accounts.
- Phase B: enforce reads for pilot tenants and log denied cases.
- Phase C: enforce mutations, exports, shares, and deletes.
- Keep a narrowly controlled support override requiring a dedicated capability, reason, short expiry, and immutable audit event. Do not use role name or `is_admin()` as the override.

#### Regression matrix

For every resource endpoint and UI action test:

| Actor | Same tenant, allowed object | Same tenant, forbidden action | Different tenant | Missing membership | Anonymous |
|---|---:|---:|---:|---:|---:|
| Owner | allow | policy-dependent | deny | deny | deny |
| Manager | policy-dependent | deny | deny | deny | deny |
| Member | policy-dependent | deny | deny | deny | deny |
| Client/visitor | narrowly allowed | deny | deny | deny | deny |

Add automated tests for sequential numeric IDs, UUIDs copied from another tenant, changed `workspace_id`, changed `user_id`, mixed tenant/object pairs, bulk endpoints, exports, and AI-triggered actions.

### SEC-003 — Backups beneath the public web root

**Severity:** Critical
**Affected code:** backup directory and download logic around lines 42987–42996 of `cora-workspace.php`
**Observed data:** five SQL/ZIP backup artifacts totaling approximately 13 MB beneath `wp-content/cora-private-backups`

#### Verified condition

The default backup location is below `WP_CONTENT_DIR`, which is normally web-accessible. Access relies on `.htaccess`. That file may protect Apache but does not automatically protect Nginx, a CDN, a copied object-store prefix, a backup restore, or a misconfigured static file server. The authenticated download handler also sets cache-oriented headers that are inappropriate for database backups.

#### Impact

A single proxy/server configuration mistake can expose database content, user records, password hashes, private business data, configuration, and tokens.

#### Required repair

1. Move backups outside `ABSPATH` and every configured document root.
2. In production, hard-fail backup creation if the resolved path is inside a public root.
3. Prefer encrypted private object storage with short-lived server-generated download authorization.
4. Encrypt each backup at rest using a key not stored in the same directory or artifact.
5. Stream downloads through an authenticated, authorized controller that validates tenant/admin scope.
6. Send `Cache-Control: no-store, private`, `Pragma: no-cache`, `X-Content-Type-Options: nosniff`, and a fixed attachment filename.
7. Add explicit Nginx/CDN denial for the old path before migration, then remove the old artifacts after a verified private copy exists.
8. Record create/download/delete events without logging paths containing tokens or secret material.
9. Apply retention limits and periodically restore-test a backup in an isolated environment.

#### Regression tests

- direct HTTP requests for every old and new backup filename fail;
- directory listing fails;
- symlink/path traversal cannot move output under the public root;
- unauthorized users cannot enumerate or download backups;
- authorized download responses are not cached;
- restore test succeeds from the encrypted private artifact.

### SEC-004 — Vulnerable frontend dependency lock state

**Severity:** Critical/High
**Affected files:** `cora-frontend/package.json`, `cora-frontend/package-lock.json`

#### Verified condition

The manifest currently asks for Next.js `^16.3.3`, but the lock/install state resolves Next.js 16.3.1 and Sharp 0.35.3. `npm ls` reports the installed Next.js version as invalid relative to the updated manifest. The production-only audit reports:

- critical Next.js advisories affecting versions before 16.3.3, including image-processing and Windows-hosted execution scenarios;
- a high-severity Sharp advisory affecting versions before 0.35.4.

The current static-export and unoptimized-image configuration may reduce reachability of some Next image paths, but it is not a substitute for removing vulnerable code from the build/deployment set.

#### Required repair

1. Regenerate the lockfile with Next.js 16.3.3 or later and Sharp 0.35.4 or later.
2. Run a clean `npm ci`, production audit, build, and smoke test.
3. Commit manifest and lockfile together.
4. Confirm the deployment system uses the committed lockfile and `npm ci`, not an unconstrained install.
5. Add dependency review/SBOM generation to release CI and define patch SLAs: critical within 24–72 hours, high within 7 days.

#### Regression tests

- `npm ls next sharp` has no invalid/deduplication errors;
- `npm audit --omit=dev` has no critical/high advisories accepted without a documented exception;
- static export, contact form, AI preview, and asset loading still work;
- deployed package inventory matches the lockfile.

### SEC-005 — Unsigned WhatsApp webhook events

**Severity:** High
**Affected code:** public webhook route around lines 5317–5397

#### Verified condition

The POST webhook is publicly callable and processes event payloads without validating Meta's `X-Hub-Signature-256` against the raw request body and application secret.

#### Impact

Attackers can forge inbound events, pollute conversations/audit history, trigger workflow behavior, and create support confusion or downstream actions.

#### Required repair

- capture the exact raw body before JSON parsing;
- compute the expected HMAC-SHA256 with the correct app secret;
- require the expected prefix and compare using `hash_equals`;
- reject missing/invalid signatures before parsing or writing;
- validate payload shape, account/phone identifiers, timestamp freshness, and deduplicate provider event IDs;
- apply body-size and processing-time limits;
- return quickly and process valid events asynchronously where possible;
- never log message bodies or credentials by default.

### SEC-006 — Verification, reset, magic-link, and invitation weaknesses

**Severity:** High

#### Verified conditions

- Resend-verification responses distinguish missing accounts, pending invitations, and already verified accounts, enabling enumeration.
- Public nonce checks do not provide durable rate limiting; a public page can supply a nonce to an automated attacker.
- Magic-link registration/login can create a workspace-owner account when the email does not exist. Abuse controls are not strong enough for that privilege transition.
- Verification, magic-link, invitation, and some share tokens are stored in plaintext or logged as full URLs.
- Invitation acceptance can authenticate an existing user from a bearer invitation link.
- Invitation role selection is not uniformly constrained by an explicit inviter-role-to-invitee-role matrix.
- Development helpers store or log recent verification links; production misclassification can turn this into credential disclosure.

#### Required repair

1. Use one generic response for forgot-password, resend, invitation lookup, and magic-link request endpoints.
2. Rate-limit by a privacy-preserving combination of normalized account key, IP prefix, device/session signal, and global provider quota. Use shared storage, not process memory.
3. Add progressive delay and CAPTCHA/challenge after suspicious volume, not on every normal request.
4. Store only SHA-256/HMAC hashes of random bearer tokens with purpose, user/invitation ID, tenant ID, expiry, used-at, and issuance metadata.
5. Never log full bearer URLs or put them in options/debug pages. Redact token query parameters centrally.
6. Invitation acceptance should join the currently authenticated matching user, or require normal authentication before joining. Do not silently switch an existing browser session to another account.
7. Define and enforce an explicit role matrix. An inviter can only assign roles below or equal to their tenant authority; WordPress administrator/super roles must never be request-selectable.
8. Require reauthentication/MFA for role escalation, owner transfer, recovery-address change, and bulk invitation.
9. Rotate all outstanding legacy bearer tokens after migration.

### SEC-007 — AI prompt injection can reach privileged mutations

**Severity:** High
**Affected code:** AI chat/action handling around lines 17846–19413 and related action implementations

#### Verified condition

The AI chat nonce is optional in at least one path. Model output is parsed for action tags and can be executed automatically. The action executor blocks only a short owner-only list, leaving many mutations available to broad role categories. Several action implementations load or mutate an object by global ID without tenant ownership checks. Broad workspace settings are also supplied to the model context.

#### Risk

Untrusted content in forms, documents, messages, or model output can attempt to invoke tools. Prompt injection then inherits the authorization defects of the tool executor. This is an authorization problem, not something a better system prompt can solve.

#### Required repair

1. Require authentication and a valid CSRF nonce on every browser-originated AI request.
2. Treat model output as untrusted data. Replace free-form action tags with a typed, allowlisted action DTO validated against a strict JSON schema.
3. Execute every action through the same `cora_authorize` and tenant-scoped repository used by the normal UI.
4. Ignore model-supplied tenant/user/role fields unless they are authorized lookup inputs.
5. Require an expiring, server-signed confirmation intent for destructive, external, financial, permission-changing, or bulk actions.
6. Bind confirmation to user, tenant, action type, exact normalized parameters, and expiry. One confirmation cannot authorize changed parameters.
7. Redact secrets, private provider configuration, and unnecessary personal data from model context.
8. Set action budgets, per-tenant spend quotas, timeouts, and circuit breakers.
9. Log action type and resource identifiers, not private prompt content by default.

### SEC-008 — Stored and DOM cross-site scripting

**Severity:** High
**Affected examples:** public form rendering, checkout/mock checkout, task/vault/form interfaces, and dynamically generated administrative UI

#### Verified condition

The form sanitizer now handles several important fields, which is an improvement. However, styling, settings, logic, and component-type structures are not fully governed by a recursive versioned schema. In `templates/public-form-view.php` around lines 848–864, repeatable-field values are interpolated into `innerHTML`/HTML attributes. Mock checkout code around lines 1792–1840 constructs document HTML using URL-derived amount, currency, and form identifiers. Other `innerHTML` sites require source-by-source validation.

#### Required repair

- replace string-built HTML with `createElement`, `textContent`, `setAttribute`/properties, and event listeners;
- never place untrusted text in inline event handlers;
- define an allowlist of supported component types, property names, enums, URL protocols, nesting depth, array count, and total schema bytes;
- sanitize rich text using a small server-side HTML allowlist and sanitize again at the rendering boundary;
- JSON-encode state into a non-executable data block rather than JavaScript/HTML interpolation;
- reject dangerous URL protocols and CSS constructs;
- add Content Security Policy in report-only mode, remove inline script dependencies, then enforce with nonces/hashes;
- use contextual WordPress escaping at the last output boundary (`esc_html`, `esc_attr`, `esc_url`, constrained `wp_kses`).

#### Regression payloads

Test script tags, event-handler attributes, malformed SVG, `javascript:` URLs, CSS URL payloads, quote breaking, nested JSON, template syntax, Unicode encodings, and saved payloads opened by a different tenant administrator. Tests should assert DOM behavior, not only response text.

### SEC-009 — Public intake abuse and server-side request forgery

**Severity:** High

#### Verified conditions

- Public form submission allows broad cross-origin access and has database-backed recent-IP throttling, but lacks consistent maximum body size, nesting depth, field count, attachment budget, and per-form/global quotas.
- Form webhook delivery accepts a valid-looking URL and uses a general HTTP client call rather than a strict SSRF-safe destination policy.
- Public lead creation can resolve a default/anonymous tenant and accepts assignment-like fields without sufficient authority.
- Public document/RAG and other anonymous utilities rely on public nonces without durable cost quotas.
- A public update-check endpoint can invalidate cache and provoke outbound work.

#### Required repair

1. Set web-server and application body-size limits; separately limit field count, nesting depth, string bytes, repeatable rows, and attachment totals.
2. Use shared rate limiting with endpoint, tenant/form, IP prefix, account/device, and global dimensions.
3. Add per-tenant quotas for mail, AI/RAG, storage, webhook delivery, and expensive searches.
4. For webhooks, use an explicit destination allowlist or a registration/verification workflow.
5. Resolve DNS and reject loopback, link-local, RFC1918/private, carrier-grade NAT, multicast, metadata, and reserved addresses for IPv4 and IPv6. Revalidate every redirect and connect to the validated address while preserving TLS hostname verification.
6. Restrict ports, schemes, redirects, response bytes, and timeouts. Use WordPress safe HTTP APIs as an additional layer, not the whole policy.
7. Do not permit public callers to select assignees, owners, internal status, role, or tenant. Resolve these server-side.
8. Remove the anonymous cache-invalidating update action or require a signed internal job request.
9. Return neutral responses and queue email/webhook delivery.

### SEC-010 — Update and production-build supply chain

**Severity:** High
**Affected areas:** `updates/cora-workspace.json`, updater code around lines 42659–42725, Git synchronization/build code around lines 24773–24912

#### Verified condition

The update manifest does not provide a cryptographically authenticated package digest/signature. The updater downloads and extracts plugin code without a release signature verification step. The packaged ZIP currently contains the email-verification flaw described in SEC-001. Separately, an administrative Git workflow can download repository content and run `npm run build` on the application host; repository build scripts are executable code.

#### Required repair

- publish a manifest containing version, exact SHA-256, package size, minimum platform version, release time, and Ed25519 signature;
- embed only the public verification key in the plugin and verify signature plus digest before extraction;
- pin the update host and require HTTPS with normal certificate validation;
- download to a staging directory, validate archive paths/size/file types, prevent zip-slip/symlinks, and atomically swap with rollback;
- retain the previous known-good package for rollback;
- build artifacts in isolated CI, generate an SBOM/provenance record, scan them, and deploy prebuilt output;
- do not run arbitrary repository lifecycle scripts on the production WordPress host;
- if Git import remains a product feature, sandbox it with no production credentials/network access and strict resource limits.

### SEC-011 — Vault, public shares, and e-sign boundaries

**Severity:** High

#### Verified condition

Vault documents and shares are held in global option-style collections. Several operations search by document ID or share token without making tenant ownership mandatory. Public e-sign scans global documents using document/share identifiers. Signature image/data inputs lack consistent byte limits. Share creation can allow bearer access without a forced expiry.

#### Required repair

Create a share-grant record with:

- grant ID and tenant/resource ownership;
- SHA-256/HMAC token hash, never the raw token;
- purpose and narrowly allowed operations;
- created-by, issued-at, expires-at, revoked-at, use count, and optional recipient binding;
- audit events for view, download, sign, revoke, and expiry.

Additional requirements:

- expiry is mandatory; use short defaults and explicit renewal;
- rotate the token when permissions/recipient/expiry changes;
- apply document and tenant predicates before every operation;
- cap signature bytes and dimensions, decode safely, and store outside executable/public paths;
- prevent one signer from overwriting another signer or a finalized document;
- serve pages with `Cache-Control: no-store`, restrictive referrer policy, frame policy, and no third-party resources unless necessary;
- provide immediate revocation without deleting the underlying document;
- migrate option collections to tenant-keyed tables using dual-read, backfill, consistency checks, then cutover.

### SEC-012 — Broad dynamic WordPress capabilities

**Severity:** High
**Affected code:** role/capability mutation around lines 719–773

#### Verified condition

Manager/super-style roles inherit a large portion of administrator capabilities globally. Builder roles receive global page/theme abilities including `edit_theme_options`. WordPress capabilities are site-wide, while Cora permissions are expected to be workspace-specific.

#### Required repair

- define explicit Cora capabilities instead of copying administrator capabilities;
- use WordPress meta-capability mapping for object checks and call the shared tenant policy;
- grant Elementor/builder access only to mapped posts belonging to the authorized Cora canvas/site;
- remove `edit_theme_options` and unrelated content/plugin/user capabilities from tenant roles;
- keep WordPress administrator as an infrastructure role, not a tenant business role;
- add a migration that removes legacy broad capabilities only after logging and testing affected workflows.

### SEC-013 — Gallery/media access controls

**Severity:** Medium/High
**Affected examples:** `templates/public-gallery-view.php`, media share handlers

#### Verified condition

Some share passwords are stored or compared in plaintext. Gallery access uses an MD5-derived cookie and does not consistently set `Secure`, `HttpOnly`, and `SameSite`. Media URLs can be directly reachable even when the surrounding share page has a password.

#### Required repair

- store passwords using `wp_hash_password` and verify using `wp_check_password`;
- after successful password entry, issue a random server-side grant cookie with `Secure`, `HttpOnly`, `SameSite=Lax/Strict`, narrow path, and short expiry;
- rate-limit password attempts and use generic errors;
- serve protected files through an authorized controller or signed private-origin URL;
- do not expose the permanent storage URL in HTML;
- prevent CDN caching of protected responses and vary on authorization when appropriate;
- migrate existing plaintext records on next successful verification, then erase plaintext.

### SEC-014 — Frontend contact and AI preview APIs

**Severity:** Medium/High
**Affected files:** `cora-frontend` API routes for contact and AI preview

#### Positive changes

The contact route now reads secrets from environment variables, escapes content, and applies several input limits. These are meaningful improvements.

#### Remaining conditions

- Rate limits are process-local or otherwise unsuitable for multiple server instances/restarts.
- AI preview origin validation uses substring matching and can accept a missing origin.
- Forwarded IP headers may be trusted without a defined trusted-proxy boundary.
- Provider/API cost abuse is possible without durable per-user/tenant quotas.
- Contact delivery failures can return inconsistent success status, and raw exception messages may be exposed.
- The contact response reveals delivery-recipient details unnecessarily.

#### Required repair

- use an exact allowlist of normalized origins; require browser origin/fetch metadata on browser-only routes;
- trust forwarding headers only from configured proxies and prefer platform-provided client IP metadata;
- move limits to shared storage with atomic increments and expiry;
- add global and per-tenant spend/request caps for AI preview;
- return a stable public error envelope and keep provider errors server-side;
- return an appropriate retryable failure status when delivery did not occur;
- do not reveal internal recipient addresses;
- apply bot challenges progressively when rate/reputation thresholds trigger.

### SEC-015 — Browser headers and service-worker caching

**Severity:** Medium

#### Verified condition

A frame-ancestor CSP is applied only on selected builder routes. There is no consistent application-wide policy for CSP, HSTS, MIME sniffing, referrer leakage, permissions, or sensitive cache control. The service worker avoids caching authenticated HTML, an improvement, but still caches same-origin resources based partly on file-like extensions. That can include private media on a shared origin.

#### Required repair

- set HTTPS-only HSTS after confirming every production subdomain supports HTTPS;
- apply `X-Content-Type-Options: nosniff`, a restrictive `Referrer-Policy`, and minimal `Permissions-Policy` globally;
- deploy CSP report-only, inventory violations, remove unsafe inline/eval dependencies, then enforce with per-response nonces/hashes;
- explicitly set `frame-ancestors` per route: deny by default, allow only required trusted editors/embeds;
- add `no-store, private` to authenticated HTML, token pages, downloads, and sensitive JSON;
- service-worker cache only explicit, immutable, versioned public plugin assets;
- never cache responses with authorization, share/token query parameters, `private`, or `no-store`;
- clear user/tenant caches on logout, tenant switch, authorization failure, and service-worker version upgrade.

### SEC-016 — Secrets and environment classification

**Severity:** Medium/High

#### Verified condition

The local ignored WordPress configuration contains database credentials and authentication salts, which is normal for local development but remains sensitive. Debug mode is enabled locally, cron behavior is changed, and several code paths relax security or reveal links when the environment is considered local. Tracked documentation includes local credential guidance. Earlier hardcoded contact/SMTP credential patterns appear to have been removed from current code, but repository history contains matches that should be treated as potentially disclosed.

#### Required repair

1. Rotate every credential/token that has ever been committed, logged, shared in a ticket/chat, or included in a package—even after deleting it from current source.
2. Use a managed secret store or protected deployment variables; never ship secrets in frontend bundles, update ZIPs, database exports, or logs.
3. Make production mode explicit and fail closed. Do not infer “local” security behavior solely from hostname or a mutable request value.
4. Add a production startup assertion that rejects debug display, default salts, local bypasses, public backups, mock-payment shortcuts, and test-link exposure.
5. Add secret scanning to pre-commit and CI, including Git history and release artifacts.
6. Keep local credential documentation free of actual passwords/tokens and exclude it from release packages.
7. Encrypt provider credentials at rest with a key outside the database. Base64 is encoding, not encryption.

### SEC-017 — MCP/integration bearer access is global

**Severity:** Medium

#### Verified condition

The MCP route performs a bearer-token comparison, which is positive, but the token is stored globally and tool execution can use a fixed user context. The credential is not strongly scoped to tenant, tool, user, expiry, source, or request budget.

#### Required repair

- create hashed integration credentials with tenant, allowed tools/actions, creator, expiry, last-used, and revocation state;
- never accept bearer tokens in query strings;
- resolve an integration principal rather than impersonating user ID 1;
- run every tool through normal authorization and tenant-scoped repositories;
- provide rotation, overlapping grace period, immediate revocation, IP restrictions where practical, and rate limits;
- audit calls by credential ID and action without logging the bearer token or sensitive payload.

### SEC-018 — Security observability and incident readiness

**Severity:** Medium

Security repairs are difficult to trust without signals showing attempted bypasses and regressions.

#### Required events

Record structured, immutable security events for:

- authentication success/failure, verification, password reset, magic-link use, MFA, logout;
- invitation create/accept/revoke and role/owner changes;
- authorization denials and support overrides;
- cross-tenant lookup attempts;
- backup create/download/delete/restore;
- share create/view/sign/revoke;
- AI action proposal/confirmation/execution/denial;
- webhook signature failure and repeated event IDs;
- integration token create/rotate/revoke/use;
- update verification/install/rollback;
- secret/config safety-check failure.

Do not record passwords, bearer links, tokens, signature images, document content, message bodies, or full AI prompts by default. Alert on verification anomalies, repeated cross-tenant access, owner-role changes, bulk exports, backup downloads, update-signature failures, and quota spikes.

## 6. Public attack-surface inventory

The repository registers roughly two dozen unauthenticated AJAX actions and dozens of REST routes. Public access is not automatically a vulnerability, but every public entry point needs an explicit threat model, durable abuse controls, strict schema, and a test proving that it cannot cross tenants or trigger privileged work.

Notable public actions include account login/recovery/resend, invitation acceptance, magic links, self-registration, lead and trial creation, public document search/RAG, reviews, suspension appeals, e-sign, portfolio likes, media tracking, email-open tracking, version/update checks, and modal registration.

For each public action, the implementation agent must document:

- why it must be public;
- maximum request/body/file sizes;
- schema and normalization rules;
- tenant resolution rule;
- rate/quota keys and thresholds;
- CSRF/origin/signature expectations;
- outbound side effects and their limits;
- data returned to anonymous users;
- logging/redaction behavior;
- negative tests.

If an action does not need to be public, remove its `wp_ajax_nopriv_*` registration rather than relying on an internal check that future refactors may bypass.

## 7. Phased implementation plan that preserves features

### Phase 0 — Immediate containment

Target: smallest changes with the highest risk reduction.

- repair SEC-001 and invalidate outstanding verification tokens;
- deploy web-server/CDN denial for current backup paths and prepare an out-of-web-root destination;
- update and lock Next.js/Sharp dependencies;
- require WhatsApp webhook signatures;
- stop logging/storing bearer links;
- disable only the specific anonymous maintenance/update action that has no user-facing need;
- add pilot-level edge rate limits for auth, form, AI/RAG, lead, share-password, and email-triggering endpoints;
- rotate historically exposed production-like credentials.

### Phase 1 — Authorization foundation

- implement `Cora_Request_Context`, membership lookup, `cora_authorize`, and security event writer;
- wrap current Forms and Tasks routes first because they expose broad data/mutation surfaces;
- add tenant predicates to every resource loader and mutation;
- add cross-tenant automated tests before enforcement;
- migrate Canvas, Vault, shares/e-sign, AI actions, leads, and executive data;
- remove hardcoded tenant defaults.

### Phase 2 — Public-input and token hardening

- migrate verification, reset, invitation, magic-link, share, and integration tokens to hashed expiring records;
- add the invitation role matrix and reauthentication for privilege changes;
- implement recursive form-schema validation and eliminate unsafe DOM construction;
- add SSRF-safe outbound webhook delivery;
- add shared rate limits and per-tenant quotas;
- protect galleries/media behind random grants/private delivery.

### Phase 3 — Platform hardening

- narrow WordPress capabilities;
- enforce security headers and CSP after report-only tuning;
- restrict service-worker caching;
- sign release manifests/packages and move builds to isolated CI;
- migrate global option collections to tenant-keyed tables;
- scope MCP credentials and principals;
- add dashboards, alerts, retention, restore tests, and incident runbooks.

## 8. Required automated security test suite

### Authentication and tokens

- invalid, expired, used, wrong-purpose, and wrong-account tokens fail;
- token records are hashed at rest;
- no flow creates a session merely because an account is already verified;
- account-discovery endpoints return equivalent public responses and timing within a reasonable tolerance;
- rate limits persist across processes/restarts;
- session rotation occurs at login and privilege change;
- logout invalidates browser and service-worker-sensitive state.

### Tenant isolation

- create tenant A and B, with owner/manager/member/client users in both;
- enumerate and mutate every resource type using same-tenant and cross-tenant IDs;
- repeat through UI route, REST, AJAX, AI action, export, share, and bulk endpoints;
- attempt mixed inputs: tenant A ID with tenant B resource ID;
- assert database row count/content in both tenants after every denied mutation;
- assert support override is denied without dedicated permission, reason, and expiry.

### Input/output security

- stored and reflected XSS corpus against forms, tasks, vault metadata, names, messages, checkout parameters, and builder state;
- SQL metacharacters and large numeric/UUID IDs against every lookup;
- JSON depth, key count, total bytes, Unicode, malformed encodings, and duplicate keys;
- upload extension/MIME disagreement, polyglots, archives, huge dimensions, and filename traversal;
- SSRF corpus for IPv4/IPv6, decimal/hex/octal forms, redirects, DNS changes, credentials in URL, and metadata addresses.

### Public abuse and integrations

- forged/missing/wrong webhook signatures fail before any write;
- duplicate provider events are idempotent;
- contact/form/lead/invite/AI requests hit expected distributed limits;
- provider failure produces stable safe errors without leaking secrets;
- public tracking cannot change privileged state;
- e-sign/share revocation and expiry take effect immediately.

### Supply chain and deployment

- tampered manifest, package, digest, signature, archive path, and oversized archive fail safely;
- interrupted install rolls back to the last known-good plugin;
- release ZIP contains no secret, backup, local credential file, test helper, or vulnerable old source;
- clean production dependency audit and SBOM are archived with the release;
- production startup safety assertions pass.

## 9. Pilot guardrails outside the code

These controls reduce exposure while code repairs are landing and do not require removing features:

- invite-only pilot; disable open self-registration unless it is part of the test goal;
- use a dedicated pilot production environment, not a developer machine or shared staging database;
- collect the minimum real client data and avoid regulated/highly sensitive documents during the first pilot;
- enforce HTTPS, managed WAF/rate limits, private database networking, least-privilege database account, and encrypted storage;
- separate production secrets from staging/local and rotate them on staff or vendor changes;
- daily encrypted offsite backups with tested restore and strict download audit;
- define one incident owner and a kill switch for public forms, AI actions, shares, integrations, and outbound email independently;
- monitor authentication anomalies, cross-tenant denies, owner changes, export volume, webhook failures, AI spend, and email spikes;
- publish a security contact and internal incident checklist;
- obtain explicit pilot-user consent for beta behavior and clearly describe data handling/retention.

## 10. Instructions for the implementation AI agent

1. Do not rewrite the platform or change public API response shapes unless a security requirement makes it unavoidable.
2. Create one fix branch per security group and keep commits small enough to review and revert.
3. Before editing a flow, add tests that demonstrate the current vulnerability without containing live secrets or destructive payloads.
4. Implement the central authorization/context layer before adding more role conditionals.
5. Never “fix” tenant access by trusting an additional request parameter or hidden frontend field.
6. Every data lookup/mutation must state its tenant and authorization assumptions in code.
7. Use database constraints/indexes for tenant/resource uniqueness and atomic single-use tokens where possible.
8. Preserve compatibility through adapters, dual-read/backfill, and feature flags; avoid indefinite dual-write.
9. Use report-only/shadow modes for authorization and CSP only for a short measured period, then enforce.
10. Do not weaken a security check to make a failing UI test pass. Identify the correct resource ownership or permission.
11. Do not expose raw exceptions, provider responses, SQL errors, filesystem paths, or tokens to clients.
12. Rebuild `updates/cora-workspace.zip` only after source fixes and run the same tests/scans against the extracted artifact.
13. Document every intentionally accepted risk with owner, reason, affected surface, compensating control, and expiry date.
14. Require human review for authentication, authorization, cryptography, update verification, and destructive AI actions.

## 11. Definition of done for each finding

A finding is complete only when all of the following are true:

- the vulnerable code path is removed or made unreachable by an explicit policy;
- positive tests prove legitimate existing workflows still work;
- negative tests prove anonymous, wrong-role, cross-tenant, replay, and malformed cases fail;
- logging contains useful event metadata but no secrets/private payloads;
- migration handles existing records/tokens safely;
- rollback steps are documented and tested where data/schema changes occur;
- the release artifact, not only the source tree, contains the fix;
- a reviewer can map the implementation to the finding and tests.

## 12. Existing protections worth preserving

The reassessment found useful security work that should not be lost during repairs:

- substantial WordPress nonce usage across authenticated AJAX handlers;
- prepared SQL usage in many database paths;
- improved form sanitization for several common fields;
- contact secrets moved to environment configuration with input escaping/limits;
- MCP bearer comparison uses a timing-safe comparison;
- service worker avoids caching authenticated HTML;
- preview access has moved toward HMAC-based authorization;
- WordPress safe HTTP APIs are used in some remote-fetch paths;
- current custom PHP files pass syntax linting.

These controls are building blocks, not complete authorization or abuse prevention. Preserve them and place the missing tenant, token, and quota controls around them.

## 13. Audit evidence record

The following checks were completed during this reassessment:

- repository architecture and tracked/untracked-sensitive-path review;
- endpoint/hook inventory and targeted manual tracing of authentication, tenant, token, public input, filesystem, outbound HTTP, mail, update, and AI-action paths;
- comparison of the account-verification implementation in source and the packaged update ZIP;
- inspection of the configured backup path and files present there;
- PHP syntax lint covered the present 126-file custom/test set with no syntax errors;
- root production dependency audit: no reported vulnerabilities;
- `cora-frontend` production dependency audit: critical Next.js and high Sharp advisories in installed lock state;
- frontend dependency tree check: manifest/lock mismatch for Next.js;
- attempted local HTTP runtime check: `cora.local` was not accepting connections, so dynamic tests were not claimed.

## 14. Final priority checklist

Before bringing external pilot users onto the environment, the implementation team should be able to check off:

- [ ] SEC-001 authentication bypass fixed, tested, deployed, and old tokens invalidated
- [ ] Current backup URLs denied at every edge and backups moved outside the web root
- [ ] Next.js/Sharp lockfile updated and clean build/audit completed
- [ ] WhatsApp webhook signature verification enforced
- [ ] Forms and Tasks protected by tenant/object authorization with cross-tenant tests
- [ ] Canvas, Vault, shares/e-sign, AI actions, and leads placed behind the same policy
- [ ] Invitation/magic-link roles and tokens hardened; full bearer links removed from logs/options
- [ ] Public endpoints have shared rate limits, body limits, quotas, and neutral responses
- [ ] Form/checkout DOM XSS sinks removed and recursive schema validation added
- [ ] Outbound webhook/remote-fetch SSRF policy enforced
- [ ] Broad dynamic WordPress capabilities narrowed
- [ ] Release packages cryptographically verified and production-side arbitrary builds removed/sandboxed
- [ ] Security event monitoring and independent kill switches configured
- [ ] Release artifact scanned and tested separately from the source working tree

This checklist is a sequencing aid, not a claim of perfect security. The objective is a controlled pilot with strong identity binding, tenant isolation, private backups, bounded public abuse, and reversible changes—while keeping the existing Cora feature set operational.
