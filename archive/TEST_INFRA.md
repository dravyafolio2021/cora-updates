# Playwright E2E Testing Infrastructure

This document outlines the End-to-End (E2E) testing infrastructure implemented for the Cora Real Estate workspace platform.

## 1. Directory Structure

All test files are stored in `tests/e2e/` at the project root:
- `tests/e2e/helpers.ts` — Authentication and utility helper functions.
- `tests/e2e/test-connection.spec.ts` — Verifies basic WP page access and connection.
- `tests/e2e/tier1-feature-coverage.spec.ts` — Feature coverage tests (30 tests) for WordPress Core replacement modules (Pages, Comments, Appearance, Tools, Media-Editor, Settings-Suite).
- `tests/e2e/tier2-boundary-cases.spec.ts` — Boundary/negative cases (30 tests) verifying input validation, empty lists, invalid formats, and server errors.
- `tests/e2e/tier3-pairwise-combinations.spec.ts` — Pairwise integration tests (6 tests) covering cross-module flows.
- `tests/e2e/tier4-workload-flows.spec.ts` — Comprehensive real-user workload flows (6 tests) simulating complex lifecycle actions.

## 2. Configuration (`playwright.config.ts`)

The Playwright configuration is located at the project root (`/Users/shrutian/Desktop/cora/playwright.config.ts`) and specifies:
- **Base URL**: `http://cora.local` (local WordPress site).
- **Browsers**: Chromium (headless mode by default).
- **Reporters**: List & HTML.
- **Trace & Screenshots**: Screenshot on failure, trace on first retry.
- **Parallelism**: Disabled (`fullyParallel: false`, `workers: 1`) to prevent database write conflicts during E2E operations.

## 3. Authentication & State Management

Authentication uses Playwright's storage state mechanism:
- Users log in using `helpers.ts` with the credentials:
  - **Username**: `cora_admin`
  - **Password**: `cora_secure_pass_123`
- Session cookie states are stored dynamically per-context to keep the tests secure and isolated, while eliminating login overhead in each individual test block.

## 4. How to Execute Tests

To install dependencies and run the E2E test suite:

```bash
# Install dependencies
npm install

# Install Playwright browser engines
npx playwright install chromium

# Run all E2E tests
npx playwright test

# Run a specific tier of tests
npx playwright test tests/e2e/tier1-feature-coverage.spec.ts
npx playwright test tests/e2e/tier2-boundary-cases.spec.ts
npx playwright test tests/e2e/tier3-pairwise-combinations.spec.ts
npx playwright test tests/e2e/tier4-workload-flows.spec.ts
```
