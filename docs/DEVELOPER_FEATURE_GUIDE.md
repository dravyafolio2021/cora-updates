# Cora Platform — Developer Feature & Optimization Guide

This guide defines the standardized 5-step architecture blueprint for building new features and refactoring existing modules across the Cora SaaS Workspace (`app/public/wp-content/plugins/cora-workspace`) and Marketing Frontend (`cora-frontend`).

---

## 1. Architectural Principles

1. **Zero-Regression & Strict Module Isolation**: Modifying or introducing a module must never break, alter, or cause side effects in neighboring views.
2. **Dedicated Schema & Tenant Scope**: Always isolate records via `agency_id = %d` on custom `wp_cora_*` tables. Never mix agency data in `wp_posts` / `wp_postmeta`.
3. **Atomic Design System Alignment**: Use Level 00–04 tokens from `cora-design-tokens.js` (and `cora-tokens.ts` for Next.js).
4. **Dialogue & Mobile Ergonomics**:
   - Monochromatic toasts (`window.coraShowToast`) for all alerts and feedback.
   - Bottom-up slide sheets on mobile viewports (`translate-y-full` to `translate-y-0`). Zero mobile side drawers.
5. **Dynamic PWA Invalidation**: Increment `CORA_WORKSPACE_VERSION` in `cora-workspace.php` whenever modifying shell assets to automatically cycle the Service Worker cache.

---

## 2. 5-Step Feature Creation Blueprint

### Step 1: Database Migration & Model Layer
Add table creation to `cora_workspace_install_schema()` in `cora-workspace.php` using `dbDelta`:
```php
$table_name = $wpdb->prefix . 'cora_my_feature';
$sql = "CREATE TABLE $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    agency_id bigint(20) NOT NULL,
    branch_id bigint(20) DEFAULT NULL,
    title varchar(255) NOT NULL,
    status varchar(50) DEFAULT 'active',
    metadata longtext DEFAULT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (id),
    KEY agency_idx (agency_id),
    KEY status_idx (status)
) $charset_collate;";
dbDelta($sql);
```

### Step 2: View Template (`views/view-{feature}.php`)
Create an isolated view file inside `app/public/wp-content/plugins/cora-workspace/views/`. Follow the standard structure:
```php
<?php
if (!defined('ABSPATH')) exit;
$agency_id = cora_get_current_agency_id();
$industry = cora_get_active_industry();
?>
<div class="cora-view-container max-w-[1280px] mx-auto p-4 md:p-8">
    <!-- Header with Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-zinc-950">Feature Title</h1>
            <p class="text-sm text-zinc-500">Feature description and operational context.</p>
        </div>
        <button id="cora-create-item-btn" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-950 text-white text-xs font-semibold rounded-lg hover:bg-zinc-800 transition-colors">
            + New Item
        </button>
    </div>

    <!-- Data Table / Notion Container -->
    <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-xs">
        <!-- Content items -->
    </div>
</div>
```

### Step 3: Secure AJAX Router & Tenant Guard
Register AJAX hooks in `cora-workspace.php` with nonce check and tenant isolation:
```php
add_action('wp_ajax_cora_save_my_feature', 'cora_handle_save_my_feature');
function cora_handle_save_my_feature() {
    check_ajax_referer('cora_workspace_nonce', 'nonce');
    $agency_id = cora_get_current_agency_id();
    if (!$agency_id) {
        wp_send_json_error(['message' => 'Unauthorized workspace session.'], 403);
    }
    
    // Process input & query database using $wpdb->prepare() with agency_id
    wp_send_json_success(['message' => 'Item saved successfully.']);
}
```

### Step 4: Route Registration in Dashboard Controller
In `admin-dashboard.php`, register the new route in the tab router:
```php
case 'my-feature':
    require_once plugin_dir_path(__FILE__) . 'views/view-my-feature.php';
    break;
```

### Step 5: Regression & E2E Validation
Create a corresponding Playwright spec in `tests/e2e/test-my-feature.spec.ts`:
```typescript
import { test, expect } from '@playwright/test';
import { bypassOnboarding, loginAsAdmin } from './helpers';

test('Verify My Feature loads and saves with tenant isolation', async ({ page }) => {
    await loginAsAdmin(page);
    await page.goto('/workspace/dashboard?view=my-feature');
    await expect(page.locator('h1')).toContainText('Feature Title');
});
```

---

## 3. Marketing Site Integration (`cora-frontend`)

When exposing a new capability or micro-tool to the public marketing site:
1. Create the page route in `cora-frontend/app/{feature}/page.tsx` or `cora-frontend/app/tools/{tool-slug}/page.tsx`.
2. Add metadata and JSON-LD structured data in `layout.tsx` / `page.tsx`.
3. Register the tool or feature card in `cora-frontend/app/tools/page.tsx` or `cora-frontend/lib/features-data.ts`.
4. Run `npm run build` inside `cora-frontend/` to verify type safety and static page generation.

---

## 4. Multi-Branch Tracking & Synchronization

Always keep `MODULES_STATUS.md` updated with:
- Active branch name
- Modified files and touchpoints
- Verification status (100% green tests)
