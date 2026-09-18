# Cora Platform Security Assessment and Remediation Specification

**Assessment date:** 18 September 2026  
**Assessment type:** Read-only static repository security review  
**Repository:** Cora WordPress workspace, custom plugins, Next.js marketing frontend, local/deployment configuration, update and backup mechanisms  
**Release decision:** **NO-GO — do not expose the current build to the public internet**

> This document intentionally does not reproduce any discovered password, token, API key, or other secret value. Any credential described as exposed must be treated as compromised and rotated.

## 1. Executive summary

Cora currently has a **Critical security posture**. The platform must not be launched until the release-blocking findings in this report are remediated and independently retested.

The most serious issue is a systemic authorization failure. Two REST permission functions return `true` even when the caller is unauthenticated. Those functions protect endpoints that read and modify forms, submissions, tasks, user preferences, clauses, audit records, and email. An anonymous attacker can therefore reach administrative operations without possessing an account or nonce.

This failure combines with several other weaknesses:

- Anonymous users default to agency/tenant 1.
- Form structures are stored without recursive sanitization and later rendered with `innerHTML`, creating an anonymous-to-stored-XSS chain.
- Full SQL and ZIP database backups are written below the public uploads directory. The included Nginx rules do not deny access to these file types.
- Production-looking SMTP and Hostinger API credentials are committed in tracked source files.
- Every logged-in user is dynamically granted broad WordPress editing and theme capabilities, undermining the intended role model.
- Several queries and AI operations accept caller-controlled tenant or user identifiers without proving membership.
- Draft previews and executive reports are publicly enumerable or accept arbitrary agency identifiers.
- The remote website migration feature can perform server-side requests to attacker-selected URLs.
- The plugin updater installs remotely supplied code without cryptographic package verification.
- Password reset links are persisted to the database and error logs.
- The PWA service worker caches authenticated HTML without purging it at logout.
- The frontend lockfile contains dependency versions with current critical/high npm advisories.

### Confirmed finding totals

| Severity | Count | Release meaning |
|---|---:|---|
| Critical | 6 | Public launch blocked |
| High | 8 | Must be resolved before production launch |
| Medium | 4 | Resolve during the same hardening cycle |
| Total | 18 | Requires architectural remediation, not isolated hotfixes |

The repository contains some good defensive practices—prepared SQL statements in many locations, sanitization helpers, nonce checks on some administrative actions, constant-time MCP token comparison, and a rule preventing PHP execution in uploads. These controls are insufficient because authentication, authorization, tenant scoping, secret management, and sensitive-file placement fail at more fundamental boundaries.

## 2. Scope and methodology

### 2.1 Components reviewed

- WordPress core configuration and included Nginx/PHP configuration.
- `app/public/wp-content/plugins/cora-workspace`, including its primary monolithic plugin file, templates, JavaScript, REST routes, AJAX actions, backup/update systems, AI integrations, PWA, public shares, forms, Canvas, tasks, email, and tenant helpers.
- Legacy custom plugins `cora-real-estate` and `cora-studio-ai-locked` at a security-surface level.
- `cora-frontend`, including Next.js route handlers, configuration, lockfile, contact flow, AI preview, and client rendering patterns.
- Tracked archives, credential-related files, scripts, environment-file handling, and Git ignore behavior.
- Production dependency advisories through `npm audit`.

### 2.2 Validation performed

- Manual trust-boundary and data-flow review.
- REST and AJAX registration/permission analysis.
- Authentication, authorization, IDOR/BOLA, BFLA, tenant-isolation, XSS, SSRF, secrets, backup, update, caching, rate-limit, and configuration review.
- PHP 8.2 syntax validation of 111 custom PHP files: all passed.
- Production npm audit of the root, workspace plugin, and Next.js frontend.
- Confirmation that no repository files were changed during the original assessment.

### 2.3 Limitations

This is a static repository assessment, not a guarantee of complete security. It did not include destructive exploitation, live Hostinger/cloud configuration, WAF/firewall rules, DNS, production database permissions, provider-account audit logs, social engineering, denial-of-service load testing, mobile application testing, or confirmation that no prior compromise occurred.

After remediation, Cora still requires a separate dynamic penetration test using at least two tenants and every role.

## 3. System threat model

### 3.1 High-value assets

- Customer and lead PII.
- Form submissions and intake records.
- Financial ledgers, invoices, payment details, and business telemetry.
- Staff identity, attendance, location/geofence, task, and scheduling data.
- Contracts, signatures, documents, galleries, media, and unreleased content.
- WordPress authentication cookies, nonces, password reset tokens, OAuth tokens, SMTP credentials, API keys, GitHub tokens, and MCP tokens.
- Tenant configuration, AI context/memory, and administrator capabilities.
- Server filesystem and plugin execution environment.

### 3.2 Expected attacker classes

- Anonymous internet attacker.
- Spam/automation bot.
- Legitimate user with the lowest Cora role.
- User belonging to a different tenant.
- Malicious or compromised workspace owner.
- Attacker controlling a remote website, imported repository, update manifest, dependency, or shared-device browser.

### 3.3 Primary trust boundaries

1. Internet to public WordPress/Next.js endpoints.
2. Unauthenticated visitor to authenticated workspace.
3. Low-privilege user to manager/owner/super-admin operations.
4. Tenant A to tenant B.
5. Application to email, AI, Google, GitHub, and other external providers.
6. Browser/service worker cache to authenticated user data.
7. Web root to private backups, logs, secrets, and executable plugin code.

## 4. Detailed findings

## SEC-001 — Administrative REST endpoints allow anonymous access

**Severity:** Critical  
**Category:** Broken access control, BOLA/BFLA, CWE-284/CWE-862  
**Release blocker:** Yes

### Evidence

- `cora_forms_rest_permission_check()` returns `true` after failed authentication and nonce checks: `app/public/wp-content/plugins/cora-workspace/cora-workspace.php:5123-5131`.
- `cora_tasks_rest_permission_check()` also returns `true` after failed authentication: `cora-workspace.php:55753-55768`.
- Forms, submissions, email, clause, and audit routes use the forms permission callback: `cora-workspace.php:4942-5035`.
- Task and task-preference GET/POST routes use the tasks permission callback: `cora-workspace.php:55890-55916`.
- Email accepts a caller-selected recipient and invokes `wp_mail()`: `cora-workspace.php:38295-38385`.
- Anonymous/empty tenant resolution maps to agency 1: `cora-workspace.php:26883-26896`.
- Task queries accept caller-controlled `workspace_id`; values `all` and `super` disable the workspace predicate: `cora-workspace.php:55493-55518`.
- Task preferences accept caller-controlled `user_id`, falling back to user 1 when unauthenticated: `cora-workspace.php:55846-55880`.

### Attack scenario

An anonymous caller sends requests directly to `/wp-json/cora/v1/...` or `/wp-json/cora-workspace/v1/...`. Because the permission callback succeeds, the attacker can read tenant-1 forms and submissions, change or delete forms, read email logs, send arbitrary mail from Cora's SMTP identity, change clauses, read or replace tasks, and modify another user's notification preferences.

### Impact

- PII and confidential-business-data breach.
- Destructive modification or deletion.
- Trusted-domain spam/phishing and SMTP reputation loss.
- Notification redirection and operational disruption.
- Regulatory and contractual incident exposure.

### Required remediation

1. Remove every unconditional success path from private permission callbacks.
2. Return `WP_Error` or `false` unless authentication, capability, and tenant membership all succeed.
3. Use different permission functions for read, create, update, delete, email-send, and audit-log operations.
4. Derive user ID and tenant ID from the authenticated server-side identity. Never authorize using request-provided `user_id`, `agency_id`, or `workspace_id`.
5. Permit `workspace_id=all` only for a separately authenticated platform-super-admin path.
6. Restrict email recipients according to the product's actual business rules and implement rate, daily-volume, and abuse limits.
7. Add immutable security audit events for rejected and successful privileged actions.

### Acceptance criteria

- Every affected endpoint returns `401` or `403` without a valid session.
- A user from tenant A cannot read or modify any tenant-B object, including when using a known numeric ID.
- A viewer cannot invoke any create/update/delete/email operation.
- Request-provided identity fields are ignored or rejected.
- Automated tests cover anonymous, viewer, manager, owner, tenant mismatch, deleted user, expired session, and super-admin cases.

## SEC-002 — Complete database backups are placed under the public web root

**Severity:** Critical  
**Category:** Sensitive data exposure, CWE-552/CWE-200  
**Release blocker:** Yes

### Evidence

- SQL and ZIP snapshots are written to `wp-content/uploads/cora-backups`: `cora-workspace.php:39720-39749`, `39838-39863`, and `39891-39953`.
- The plugin creates `.htaccess` as its primary access control: `cora-workspace.php:39724-39729`.
- Included Nginx configuration ignores `.htaccess` and denies only PHP under uploads: `conf/nginx/includes/restrictions.conf.hbs:14-26`.
- Five real `.sql`/`.zip` snapshots were present in `app/public/wp-content/uploads/cora-backups` during review.
- Backup filenames contain version and timestamp patterns, reducing filename secrecy.

### Attack scenario

An attacker obtains or predicts a backup filename from logs, screenshots, browser history, source patterns, an administrative response, or timestamp guessing, and requests it directly from the uploads URL. Nginx serves the SQL or ZIP as a static file.

### Impact

A single download can expose the entire database: password hashes, user and customer records, API/OAuth tokens, reset links, forms, submissions, financial information, configuration, and audit history.

### Required remediation

1. Immediately remove backup files from every public document root.
2. Store backups in a dedicated private object store or filesystem directory outside the web root.
3. Encrypt backups at rest with managed keys and implement retention/deletion policies.
4. Use random opaque backup object names; do not rely on filename entropy as authorization.
5. Serve downloads only through an authenticated, capability-checked streaming endpoint.
6. Add explicit Nginx/Apache denial for `/wp-content/uploads/cora-backups/` as defense in depth.
7. Review web access logs for any prior requests to backup file extensions or paths.

### Acceptance criteria

- Direct HTTP requests to old and newly generated backup paths return `404` or `403`.
- No `.sql`, database ZIP, credential export, or private manifest exists below the document root.
- A backup download requires a fresh authenticated authorization decision.
- Backups are encrypted and restoration is tested without making them public.

## SEC-003 — Production credentials and predictable administrator passwords are tracked

**Severity:** Critical  
**Category:** Secret exposure, CWE-798  
**Release blocker:** Yes

### Evidence

- SMTP credentials are hardcoded and written into WordPress options: `cora-workspace.php:38469-38495`.
- A Hostinger email API bearer token is used as a source-code fallback: `cora-frontend/app/api/contact/route.ts:41-44`.
- Predictable account passwords, including administrator accounts, are stored in `scripts/setup_local_accounts.php:44-120`.
- `LOCAL_CREDENTIALS.md`, `scripts/setup_local_accounts.php`, `updates/cora-workspace.zip`, and the affected source files are Git-tracked.
- Environment files are ignored, which is positive, but already tracked secrets remain in Git history and release archives.

### Impact

- SMTP/API account takeover.
- Email spoofing, phishing, quota exhaustion, and domain reputation loss.
- Administrator account compromise if provisioning scripts or passwords reach a shared/staging/production database.
- Persistent exposure through Git history, caches, forks, logs, archives, and developer machines.

### Required remediation

1. Rotate/revoke every exposed credential immediately—not only after code cleanup.
2. Review Hostinger, SMTP, GitHub, AI provider, OAuth, and WordPress logs for suspicious usage.
3. Remove secret fallbacks from source; fail closed when a required secret is absent.
4. Load secrets through a deployment secret manager or environment injection with least privilege.
5. Remove credentials from current Git state and purge sensitive history using a coordinated history-rewrite process.
6. Rebuild all distributable ZIPs from a clean source tree.
7. Replace fixed test passwords with generated values supplied only in isolated test execution.
8. Install pre-commit and CI secret scanning.

### Acceptance criteria

- Secret scanning finds no live credential in the working tree, history, artifacts, or logs.
- All old credentials fail authentication at their providers.
- The application refuses to start or disables the integration safely when a secret is missing.
- Test provisioning cannot alter a non-test environment and generates unique credentials.

## SEC-004 — Anonymous form modification creates stored XSS

**Severity:** Critical  
**Category:** Stored cross-site scripting, CWE-79  
**Release blocker:** Yes

### Evidence

- Anonymous callers can reach the form-save route through SEC-001.
- Nested `blocks`, `logic`, `styling`, and `settings` structures are serialized without schema validation or recursive sanitization: `cora-workspace.php:37116-37126` and `37174-37196`.
- Form data is embedded in JavaScript using plain `json_encode()`: `app/public/wp-content/plugins/cora-workspace/public-form-view.php:385`.
- Labels, placeholders, choices, expressions, saved values, and related fields are interpolated into `innerHTML`: `public-form-view.php:849-928` and `1160-1186`.

### Attack scenario

An attacker modifies or creates a form with markup/event-handler content inside a nested block field. When a public visitor or logged-in administrator opens the shared form, the browser executes attacker-controlled JavaScript under the Cora origin. A logged-in victim's same-origin access can turn this into account takeover or privileged data access.

### Required remediation

1. Fix SEC-001 first so only authorized users can modify forms.
2. Define a strict server-side schema for every block type and accepted field.
3. Recursively validate types, lengths, enum values, URLs, numbers, and text.
4. Use `wp_json_encode()` with safe hexadecimal flags when embedding JSON in scripts.
5. Replace dynamic `innerHTML` assembly with DOM APIs and `textContent` for untrusted strings.
6. If limited rich text is required, sanitize it with a narrowly defined allowlist on both storage and rendering.
7. Add a restrictive CSP as defense in depth; do not treat CSP as the primary fix.

### Acceptance criteria

- Standard XSS test strings render as inert text in every form field and public/admin view.
- Closing-script sequences cannot escape the JSON script context.
- Event attributes, `javascript:` URLs, SVG/script payloads, and malformed encodings are rejected or neutralized.
- Existing stored forms are migrated through the new sanitizer before rendering.

## SEC-005 — Tenant and role isolation is structurally unsafe

**Severity:** Critical  
**Category:** Multi-tenant isolation failure, privilege escalation, CWE-269/CWE-639  
**Release blocker:** Yes

### Evidence

- All logged-in users receive `edit_posts`, `edit_pages`, `edit_published_pages`, `edit_others_pages`, `publish_pages`, `elementor_edit_posts`, and `edit_theme_options`: `cora-workspace.php:736-756`.
- Custom roles themselves are created with only `read`, after which the global capability filter overrides separation: `cora-workspace.php:3453-3476`.
- Canvas themes are selected without tenant filtering: `cora-workspace.php:5162-5170`.
- The AI handler accepts request-provided agency/workspace identity without membership verification: `cora-workspace.php:17781-17792`.
- The AI context subsequently incorporates tenant users, finances, forms, tasks, documents, and operational information: `cora-workspace.php:17814-18080`.
- Business data is repeatedly stored in shared global options such as `cora_workspace_leads`, `cora_workspace_clients`, `cora_workspace_attendance_logs`, and `cora_financial_ledger`.
- Multiple object queries and update operations use IDs without adding an agency predicate.

### Impact

A legitimate low-privilege user or user from another tenant may gain editing capabilities, query or alter objects belonging to other customers, and cause the AI layer to disclose cross-tenant context.

### Required remediation

1. Remove the global capability grant for all logged-in users.
2. Design a written role/capability matrix before changing endpoints.
3. Introduce one canonical authorization service: `authorize(action, object, authenticated_user)`.
4. Resolve tenant identity exclusively from authenticated user membership/session context.
5. Require `agency_id` on every tenant-owned table and include it in every read/update/delete predicate.
6. Move shared option-backed business data into tenant-scoped tables or rigorously tenant-prefixed storage with centralized access methods.
7. Prohibit raw `$wpdb` object access outside a tenant-aware repository layer for tenant-owned entities.
8. Separate platform-super-admin privileges from workspace-owner privileges.
9. Treat AI retrieval, memory, tools, and action execution as normal privileged data access; enforce the same authorization before context construction and tool execution.

### Acceptance criteria

- A complete role/action matrix is enforced in automated tests.
- Tenant A receives `404` or `403` for every known tenant-B object ID.
- Database queries for tenant-owned objects contain a server-derived tenant predicate.
- AI requests cannot override their tenant through parameters or prompts.
- Viewer and field roles cannot edit WordPress pages, themes, other users, or global settings.

## SEC-006 — Frontend resolves dependencies with critical/high advisories

**Severity:** Critical, deployment-dependent  
**Category:** Vulnerable dependencies, CWE-1104  
**Release blocker:** Yes

### Evidence

- `cora-frontend/package-lock.json` resolves Next.js 16.3.1 and `sharp` 0.35.3.
- Production `npm audit` reports critical Next.js advisories for affected versions below 16.3.3:
  - `GHSA-p293-qw3h-jr36` — unauthenticated RCE on Windows-hosted servers.
  - `GHSA-2xp9-vwfh-vxw4` — unauthenticated RCE in the image optimization API when AVIF is used.
- `npm audit` reports high-severity `GHSA-rgj7-g3m4-5g8c` for `sharp` versions below 0.35.4.
- `cora-frontend/next.config.ts:3-10` currently declares static export and unoptimized images, which may make some affected server paths unreachable in a pure static deployment. However, the repository also contains API route handlers, so actual deployment behavior must be verified.

### Required remediation

1. Upgrade Next.js to a release outside all affected ranges, at minimum 16.3.3 for these advisories.
2. Upgrade `sharp` to at least 0.35.4.
3. Regenerate and commit the lockfile through the normal dependency process.
4. Establish whether the site is truly static or runs a Next.js server. Do not rely on static-export assumptions while deploying route handlers.
5. Make production dependency audit a required CI gate.

### Acceptance criteria

- `npm audit --omit=dev` reports no critical or high production vulnerabilities.
- Deployment documentation explicitly states whether a Next.js server and image optimizer exist.
- Security tests target the actual deployed runtime rather than only local static export.

## SEC-007 — Public draft preview and executive-report IDORs

**Severity:** High  
**Category:** IDOR/BOLA, CWE-639

### Evidence

- `/shared-preview/{post_id}` accepts a sequential post ID and checks only `post_type === post`: `cora-workspace.php:1633-1651`.
- It does not verify status, tenant, recipient, token, or expiry before rendering `public-preview-view.php`.
- The executive report treats an empty nonce as valid, accepts caller-provided `agency_id`, and is registered for unauthenticated callers: `cora-workspace.php:54293-54309`.

### Impact

- Enumeration of drafts, pending/private posts, author information, and unreleased client material.
- Exposure of customer, finance, staff attendance/location, task, and business-health information through executive reports.

### Required remediation

- Use a random, single-purpose, tenant-bound, expiring token stored as a hash.
- Verify object status and intended recipient before rendering.
- Require authentication and an explicit reporting capability for executive reports.
- Derive report tenant from the authenticated user; do not accept arbitrary agency IDs.
- Return indistinguishable `404` responses for invalid and unauthorized objects.

## SEC-008 — Remote website migration permits SSRF

**Severity:** High  
**Category:** Server-side request forgery, CWE-918

### Evidence

- Migration performs `wp_remote_get()` on attacker-selected URLs with up to five redirects and `sslverify => false`: `includes/class-cora-html-website-migrator.php:128-148` and `391-409`.
- AJAX handlers permit users with `edit_pages`: `cora-workspace.php:35329-35388`.
- SEC-005 grants `edit_pages` to every logged-in user.

### Required remediation

- Use `wp_safe_remote_get()` and allow only `http`/`https`.
- Resolve DNS and reject loopback, link-local, RFC1918, carrier-grade NAT, multicast, Unix socket, and cloud metadata destinations for IPv4 and IPv6.
- Revalidate the destination after every redirect and defend against DNS rebinding.
- Enforce response-size, content-type, redirect-count, and timeout limits.
- Run migration in an isolated worker with restricted egress.
- Restore TLS certificate verification.

## SEC-009 — Update and import mechanisms can execute unverified remote code

**Severity:** High  
**Category:** Supply-chain compromise, CWE-494

### Evidence

- The updater trusts a remote manifest's `download_url`, downloads a ZIP, and extracts it into the plugin directory without signature or pinned-hash verification: `cora-workspace.php:39405-39479`.
- The updater class disables TLS verification while retrieving metadata: `includes/class-cora-workspace-updater.php:58-68`.
- GitHub import downloads repository content and runs `npm run build`: `cora-workspace.php:22484-22566`.

### Required remediation

- Sign manifests and packages using an offline/private release key and verify with a pinned public key.
- Verify exact SHA-256/stronger package digests before extraction.
- Require TLS verification and restrict manifest/package hosts.
- Build release artifacts in CI, never on the production web server.
- Remove server-side execution of package scripts from imported repositories.
- Make updates atomic, rollback-capable, and auditable.

## SEC-010 — Password reset tokens are copied to database options and logs

**Severity:** High  
**Category:** Authentication token exposure, CWE-532/CWE-312

### Evidence

- Reset tokens are stored in plaintext user metadata.
- The complete reset URL is copied to `cora_latest_reset_link`: `cora-workspace.php:28813-28821`.
- The same URL is written to the PHP error log: `cora-workspace.php:28824-28825`.

### Required remediation

- Use WordPress core password-reset mechanisms where possible.
- Store only a cryptographic hash of custom reset tokens.
- Never store or log a complete bearer reset URL.
- Make tokens short-lived, single-use, user-bound, and invalidated after password/authentication changes.
- Add rate limits by account and trustworthy network identity without enabling account enumeration.

## SEC-011 — Service worker caches authenticated HTML

**Severity:** High  
**Category:** Sensitive browser-cache exposure, CWE-525

### Evidence

- Every successful HTML navigation response is placed in the PWA cache: `assets/pwa/service-worker.js:137-153`.
- API/AJAX requests are excluded, but workspace HTML pages are not.
- No logout message or cache purge was found.

### Required remediation

- Never cache authenticated workspace HTML or responses marked `private`/`no-store`.
- Cache only an explicit allowlist of versioned public/static assets.
- Purge all Cora caches and unregister sensitive service workers at logout/account switch.
- Partition any necessary cache by authenticated user and tenant, while avoiding tokens in cache keys or URLs.

## SEC-012 — Public and low-privilege users can mutate global configuration

**Severity:** High  
**Category:** Broken function-level authorization, CSRF

### Evidence

- `cora_ajax_switch_industry_mode()` performs no authentication, nonce, or capability check and is registered as `wp_ajax_nopriv`: `cora-workspace.php:44321-44344`.
- Several other settings flows rely on login alone, weak boolean conditions, or broad `read`/`edit_pages` capabilities rather than an action-specific permission.

### Required remediation

- Remove anonymous registration from every non-public mutation.
- Require both CSRF validation and explicit capability checks. A nonce is not authorization.
- Store tenant configuration per tenant rather than in global options.
- Add a centralized inventory test that fails if a mutating AJAX/REST action lacks an approved authorization policy.

## SEC-013 — TLS certificate verification is disabled

**Severity:** High  
**Category:** Improper certificate validation, CWE-295

### Evidence

- PHPMailer explicitly disables peer/name verification and allows self-signed certificates: `cora-workspace.php:38520-38527`.
- Multiple migrator/updater calls specify `sslverify => false`.

### Impact

An attacker capable of network interception may capture SMTP credentials/content, alter imported websites, or replace update metadata/content.

### Required remediation

- Remove all `sslverify => false`, `verify_peer => false`, `verify_peer_name => false`, and `allow_self_signed => true` production settings.
- Repair the host trust store instead of weakening certificate validation.
- Fail closed on certificate errors.

## SEC-014 — Public contact and AI endpoints have weak abuse controls

**Severity:** High  
**Category:** API abuse, resource exhaustion, HTML injection

### Evidence

- The contact endpoint has no persistent rate limit, CAPTCHA/bot control, body-size limit, or strict schema: `cora-frontend/app/api/contact/route.ts:14-54`.
- Contact values are interpolated into HTML email without contextual HTML escaping: `contact/route.ts:69-160`.
- AI preview origin validation uses substring matching and accepts a missing Origin: `cora-frontend/app/api/ai-preview/route.ts:81-93`.
- AI rate limiting is an in-memory per-process map keyed from `X-Forwarded-For`: `ai-preview/route.ts:3-38` and `96-107`.

### Required remediation

- Enforce strict request schemas and small body/field limits.
- HTML-escape every untrusted email value and safely construct URLs.
- Use exact parsed-origin comparison, not substring matching. Do not treat Origin as authentication.
- Use a shared server-side rate limiter keyed by trusted proxy-derived client identity plus abuse signals.
- Add CAPTCHA or equivalent bot-defense for public high-cost/high-volume actions.
- Apply provider quotas, circuit breakers, and monitoring.

## SEC-015 — Gallery passwords and authentication cookies are weak

**Severity:** Medium  
**Category:** Weak credential storage/session handling

### Evidence

- Gallery passwords are compared as plaintext.
- Authentication state is `md5(password)` and is replayable.
- The cookie is set without `Secure`, `HttpOnly`, or `SameSite`: `public-gallery-view.php:7-25`.
- No brute-force throttling or lockout is present.

### Required remediation

- Hash passwords with `wp_hash_password()` and verify with `wp_check_password()`.
- Use an opaque random server-side session, not a password-derived cookie.
- Apply `Secure`, `HttpOnly`, and appropriate `SameSite` settings.
- Add attempt throttling and generic error responses.
- Consider short-lived signed recipient links instead of shared passwords.

## SEC-016 — Browser security headers are incomplete

**Severity:** Medium  
**Category:** Defense-in-depth configuration

### Evidence

Repository-wide review found no consistent production policy for CSP, HSTS, `X-Content-Type-Options`, `Referrer-Policy`, or `Permissions-Policy`. Frame restrictions are selectively removed for editor/preview routes.

### Required remediation

- Deploy an application-specific CSP, initially in report-only mode, then enforce it.
- Add HSTS only after HTTPS is correct on every production subdomain.
- Add `X-Content-Type-Options: nosniff`, a strict referrer policy, permissions policy, and appropriate frame-ancestor rules.
- Use response-specific `Cache-Control: private, no-store` for sensitive pages.

## SEC-017 — Development/debug configuration is unsafe if promoted

**Severity:** Medium  
**Category:** Security misconfiguration

### Evidence

- `app/public/wp-config.php:91-98` enables `WP_DEBUG`, debug logging, and `WP_ENVIRONMENT_TYPE=local`.
- Some security checks intentionally relax nonce requirements when the environment type is local.
- Included PHP/Nginx development configuration permits very large request/upload sizes and long execution times.

### Required remediation

- Build separate immutable production configuration with debug disabled and environment type `production`.
- Never make security checks conditional on an easily misconfigured environment constant.
- Set conservative request, upload, execution, memory, and concurrency limits.
- Ensure logs are private, structured, retained appropriately, and contain no tokens or PII.

## SEC-018 — Security monitoring and abuse controls are inconsistent

**Severity:** Medium  
**Category:** Detection and operational resilience

### Evidence

- Forty-five `wp_ajax_nopriv_*` registrations and 34 REST registrations create a large public attack surface.
- Rate limiting is absent or local-memory/IP-only across several public email, form, appeal, review, lead, and AI operations.
- Some audit logs trust request IP headers or record insufficient authorization context.

### Required remediation

- Create a maintained endpoint inventory with owner, public/private status, allowed methods, capability, tenant rule, CSRF rule, rate limit, and data classification.
- Centralize distributed rate limits and security event logging.
- Alert on bulk enumeration, repeated cross-tenant denials, email spikes, backup requests, authentication anomalies, and permission changes.
- Redact secrets, reset tokens, session values, signatures, and sensitive form data from logs.

## 5. Required remediation architecture

The building agent should avoid patching individual endpoints in isolation. Cora needs shared security primitives so future modules inherit safe behavior.

### 5.1 Canonical request context

Create one immutable server-side context per authenticated request containing:

- Authenticated WordPress user ID.
- Platform role and workspace role.
- Server-derived tenant/agency ID and membership record.
- Branch/facility scope if applicable.
- Request correlation ID.

Do not allow request parameters, cookies containing raw tenant IDs, AI prompts, or client-side state to replace this context.

### 5.2 Central authorization service

Implement explicit actions such as:

- `forms.read`, `forms.create`, `forms.update`, `forms.delete`.
- `submissions.read`, `submissions.export`.
- `email.send`, `email.logs.read`.
- `tasks.read`, `tasks.assign`, `tasks.update`, `tasks.delete`.
- `canvas.read`, `canvas.publish`, `canvas.custom_code`.
- `reports.executive.read`.
- `tenant.settings.update`.
- `platform.update` and `platform.impersonate`.

Each authorization decision must verify authentication, action capability, tenant membership, and object ownership. Super-admin bypasses must be explicit, narrow, audited, and impossible to trigger through caller-selected IDs.

### 5.3 Tenant-aware data layer

- Every tenant-owned row must contain a non-null tenant ID.
- Unique keys should include tenant ID where appropriate.
- Every lookup/update/delete must bind both object ID and tenant ID.
- Prefer repository/service methods over direct `$wpdb` use throughout views and handlers.
- Add database constraints and indexes that support tenant predicates.
- Migrate global option-backed business data into tenant-owned tables.

### 5.4 Secure output model

- Validate on input, encode on output, and keep contexts distinct.
- HTML text: `esc_html()`/`textContent`.
- HTML attributes: `esc_attr()` or DOM property assignment.
- URLs: `esc_url()` plus allowed-protocol validation.
- JavaScript data: `wp_json_encode()` with safe embedding.
- Rich HTML: narrowly scoped allowlist, never arbitrary admin-supplied script unless limited to a separately trusted platform-super-admin feature.

### 5.5 Secrets and integrations

- No production secret may have a source-code default.
- Each integration gets a dedicated least-privilege credential.
- Encrypt sensitive stored integration tokens and restrict administrative display.
- Never return complete tokens after initial creation.
- Add rotation dates, ownership, provider audit links, and revocation procedures.

### 5.6 Public endpoint baseline

Every intentionally public endpoint must have:

- Strict method and content-type enforcement.
- Schema/type/length validation.
- Request-body size limit.
- Persistent distributed rate limit.
- Bot/automation controls where appropriate.
- Safe generic errors.
- No tenant selection through untrusted identifiers.
- Security monitoring and retention-conscious logging.

## 6. Remediation order

### Phase 0 — Immediate containment

1. Keep the platform inaccessible from the public internet.
2. Rotate all exposed credentials and invalidate relevant sessions/tokens.
3. Move/delete public backups and add server-level deny rules.
4. Disable vulnerable forms/tasks/email/report/industry-switch endpoints until fixed.
5. Upgrade vulnerable frontend dependencies.
6. Review access/provider logs for prior abuse.

### Phase 1 — Authentication and authorization repair

1. Implement canonical request context and action-based authorization.
2. Fix all REST permission callbacks and AJAX mutations.
3. Remove broad `user_has_cap` grants.
4. Enforce role matrix and tenant ownership.
5. Lock AI data retrieval and actions to server-derived tenant context.

### Phase 2 — Data and rendering isolation

1. Add/migrate tenant IDs and tenant-aware repository methods.
2. Remove global option storage for tenant business data.
3. Implement form schemas and eliminate unsafe `innerHTML` paths.
4. Protect share links, previews, reports, galleries, and document signatures.

### Phase 3 — Infrastructure and supply-chain hardening

1. Secure backup architecture.
2. Sign and verify releases; remove server-side package builds.
3. Restore TLS verification everywhere.
4. Fix service-worker caching and production headers.
5. Separate production configuration from Local development settings.
6. Add secret/SAST/dependency checks to CI.

### Phase 4 — Verification and release readiness

1. Run complete automated authorization and tenant-isolation tests.
2. Perform dynamic authenticated penetration testing across two or more tenants.
3. Test backup recovery, token rotation, logging, alerts, and incident response.
4. Conduct an independent final security review.
5. Launch only when every Critical/High finding is closed with evidence.

## 7. Mandatory regression-test matrix

The implementation agent should add automated tests for the following cases.

### 7.1 Authentication tests

- Anonymous access to every private REST/AJAX route returns `401`/`403`.
- Missing, invalid, expired, and cross-user nonces fail.
- Deleted, disabled, suspended, and logged-out users cannot reuse prior access.

### 7.2 Role tests

For each role—viewer, field user, editor, manager, workspace owner, platform super admin—test every action family: view, create, update, delete, export, email, publish, settings, users, backups, updates, impersonation.

### 7.3 Tenant-isolation tests

- Create tenants A and B with objects that use identical numeric IDs where possible.
- Attempt every tenant-B read/write/delete as every tenant-A role.
- Try IDs in path, query, body, cookies, headers, AI prompt/tool parameters, bulk actions, exports, and share links.
- Confirm database queries and returned counts never include tenant-B records.

### 7.4 Injection tests

- Stored/reflected/DOM XSS across all form block fields, email templates, Canvas content, galleries, comments, names, titles, URLs, and AI output.
- Script-closing JSON payloads, event attributes, SVG payloads, malformed encodings, and `javascript:` schemes.
- SQL injection probes against identifiers, sorting, filtering, search, bulk IDs, and report inputs.

### 7.5 SSRF tests

- Reject localhost, private networks, link-local/cloud metadata, IPv6 local ranges, alternative numeric IP representations, redirect-to-private, and DNS-rebinding cases.
- Enforce response-size, timeout, method, scheme, and content-type policies.

### 7.6 Browser/session tests

- Logout clears cookies, local storage, IndexedDB where appropriate, and all sensitive PWA caches.
- Account/tenant switching never displays cached data from the prior context.
- Sensitive pages use `no-store` and cannot be recovered offline.
- Gallery/share cookies have secure attributes and expire/revoke correctly.

### 7.7 Operational tests

- Direct backup URL requests fail.
- Old rotated secrets fail.
- Unsigned or hash-mismatched update packages fail without modifying files.
- Rate limits work across multiple application processes.
- Security alerts fire without including secrets or excessive PII.
- `npm audit --omit=dev` contains no critical/high production finding.

## 8. Definition of done for public launch

The platform is not release-ready until all of the following are true:

- All Critical and High findings are remediated and verified.
- No production credential exists in Git history or release artifacts, and all exposed credentials have been rotated.
- No database backup or private export is reachable through the web server.
- Every private endpoint is deny-by-default and has automated negative authorization tests.
- Tenant isolation passes for every entity, role, bulk operation, report, export, AI retrieval, and AI action.
- Stored/reflected/DOM XSS testing passes after existing data is sanitized or migrated.
- SSRF protections pass redirect, DNS, IPv4, and IPv6 test cases.
- Dependencies and release artifacts pass CI security gates.
- Production configuration disables debugging, validates TLS, applies security headers, and prevents sensitive caching.
- Monitoring, rotation, backup recovery, and incident-response procedures have owners.
- An independent penetration test reports no unresolved Critical or High finding.

## 9. Instructions to the implementation agent

1. Treat this as a security architecture task, not a list of cosmetic patches.
2. Start with containment and secret rotation; source cleanup alone does not invalidate leaked credentials.
3. Do not use nonces as a replacement for authorization.
4. Do not trust tenant, user, role, capability, or object-owner values supplied by the client.
5. Do not return `true` by default from permission code. Unknown states must deny access.
6. Do not fix cross-tenant access only in the UI. Enforce it in every server-side query and mutation.
7. Do not rely on hidden links, numeric IDs, filenames, cookies, or `Origin` headers as authentication.
8. Do not use `innerHTML` with stored or remote data.
9. Do not keep backups, logs, source archives, or credentials in the web root.
10. Do not run dependency or repository build scripts on the production web server.
11. For every fix, add a regression test that would have failed on the vulnerable implementation.
12. Produce a closure appendix mapping every `SEC-###` item to commits, tests, deployment changes, secret-rotation evidence, and retest results.

## 10. Positive controls worth preserving

- Prepared SQL and WordPress sanitization/escaping helpers are already used in many paths.
- Several sensitive administrative actions already combine nonce and capability checks.
- MCP bearer tokens are compared with `hash_equals()`.
- Nginx denies PHP execution under uploads and access to hidden files.
- Plugin installation/update capabilities are deliberately restricted in the WordPress UI.
- All reviewed custom PHP files passed PHP 8.2 syntax validation.
- Root and workspace-plugin production npm audits did not report known production dependency vulnerabilities at assessment time.

These controls should be retained, centralized, and expanded while the release-blocking architecture is corrected.

