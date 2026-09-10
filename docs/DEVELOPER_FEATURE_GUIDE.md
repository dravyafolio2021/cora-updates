# Cora Platform — Developer Feature & Optimization Guide (v4.9.32)

This guide defines the standardized architectural patterns, blueprints, and performance guidelines for engineering new modules and extending features across the Cora SaaS Workspace (`app/public/wp-content/plugins/cora-workspace`) and Marketing Frontend (`cora-frontend`).

---

## 1. Core Architectural Principles & Zero-Regression Policy

1. **Strict Module Isolation**: Modifying or introducing a module must NEVER break, alter, or cause side effects in neighboring views.
2. **Dedicated Schema & Tenant Scope**: Always isolate records via `agency_id = %d` (and optionally `branch_id = %d`) on custom `wp_cora_*` tables. Never mix multi-tenant operational data in `wp_posts` / `wp_postmeta`.
3. **Atomic Design System Tokens**: Utilize Level 00–04 tokens from `cora-design-tokens.js` (and `cora-tokens.ts` for Next.js).
4. **Dialogue & Mobile Ergonomics SOP**:
   - Direct all alerts, errors, and system confirmations to the custom monochromatic Toast Notification system (`window.coraShowToast`).
   - **Zero Mobile Side Drawers**: All mobile action panels, multi-step creators, and filter trays MUST open as bottom-up slide sheets (`translate-y-full` to `translate-y-0`).
   - **Top-Down Floating Banners**: Alerts and toasts float from top-center (`top: 68px` on mobile) to eliminate collisions with bottom sheets and navigation islands.
5. **Zero Naked Global `!important` Utilities**: Never declare un-namespaced global utility overrides with `!important` (e.g. `.hidden { display: none !important; }`). All visibility states must use scoped component classes (e.g. `.cora-drawer.collapsed`, `.cora-modal:not(.open)`).
6. **Mobile Touch Snappiness**: Enforce `touch-action: manipulation; -webkit-tap-highlight-color: transparent;` on all interactive buttons and triggers to eliminate the 300ms tap delay.
7. **High-Speed Micro-Cache Layer**: Use `cora_cache_get()` and `cora_cache_set()` for sub-millisecond query caching.

---

## 2. 5-Step Feature Creation Blueprint

### Step 1: Database Migration & Multi-Tenant Model
Add table creation to `cora_workspace_install_schema()` in `cora-workspace.php` using `dbDelta`:
```php
$table_name = $wpdb->prefix . 'cora_my_feature';
$sql = "CREATE TABLE $table_name (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    agency_id bigint(20) unsigned NOT NULL,
    branch_id bigint(20) unsigned DEFAULT NULL,
    title varchar(255) NOT NULL,
    status varchar(50) NOT NULL DEFAULT 'active',
    metadata longtext DEFAULT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (id),
    KEY agency_idx (agency_id),
    KEY status_idx (status)
) $charset_collate;";
dbDelta($sql);
```

---

### Step 2: View Template (`views/view-{feature}.php`)
Create an isolated view file inside `app/public/wp-content/plugins/cora-workspace/views/`. Follow standard multi-industry scoping and monochromatic styling:
```php
<?php
if (!defined('ABSPATH')) exit;

$agency_id = function_exists('cora_get_current_agency_id') ? cora_get_current_agency_id() : 1;
$industry  = function_exists('cora_get_active_industry') ? cora_get_active_industry() : 'real_estate';
$is_studio = ( strpos( strtolower( $industry ), 'photo' ) !== false || strpos( strtolower( $industry ), 'studio' ) !== false );
?>
<div class="cora-view-container max-w-[1280px] mx-auto p-4 md:p-8 pb-24">
    <!-- Header with Action Button & AI Brand Stack -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-zinc-950 font-display">
                <?php echo $is_studio ? 'Studio Resource Hub' : 'Feature Management'; ?>
            </h1>
            <p class="text-xs md:text-sm text-zinc-500">Manage tenant resources with real-time synchronization.</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="cora-create-item-btn" onclick="window.coraOpenFeatureDrawer()" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-950 text-white text-xs font-semibold rounded-xl hover:bg-zinc-800 active:scale-95 transition-all shadow-xs" style="touch-action: manipulation;">
                <span>+ New Item</span>
            </button>
        </div>
    </div>

    <!-- Data Table / Notion Container -->
    <div class="bg-white border border-zinc-200/80 rounded-2xl overflow-hidden shadow-xs">
        <!-- Content items -->
    </div>
</div>
```

---

### Step 3: Secure AJAX Router with Micro-Cache Integration
Register AJAX hooks in `cora-workspace.php` with nonce verification, tenant guard, and micro-caching:
```php
add_action('wp_ajax_cora_save_my_feature', 'cora_handle_save_my_feature');
function cora_handle_save_my_feature() {
    check_ajax_referer('cora_workspace_nonce', 'nonce');
    
    $agency_id = cora_get_current_agency_id();
    if (!$agency_id) {
        wp_send_json_error(['message' => 'Unauthorized workspace session.'], 403);
    }
    
    global $wpdb;
    $title = sanitize_text_field($_POST['title'] ?? '');
    
    if (empty($title)) {
        wp_send_json_error(['message' => 'Title is required.'], 400);
    }
    
    $table = $wpdb->prefix . 'cora_my_feature';
    $wpdb->insert($table, [
        'agency_id' => $agency_id,
        'title'     => $title,
        'status'    => 'active'
    ]);
    
    // Invalidate micro-cache
    if (function_exists('cora_cache_set')) {
        cora_cache_set("my_feature_list_{$agency_id}", null);
    }
    
    wp_send_json_success(['message' => 'Item saved successfully.', 'id' => $wpdb->insert_id]);
}
```

---

### Step 4: Standard Mobile Bottom-Sheet & Desktop Drawer Markup
Add standard responsive drawer/sheet markup with drag handle, rounded corners, and spring easing:
```html
<!-- Responsive Action Sheet / Drawer -->
<div id="cora-feature-drawer-backdrop" class="fixed inset-0 bg-zinc-950/40 backdrop-blur-xs z-50 transition-opacity duration-200 hidden opacity-0" onclick="window.coraCloseFeatureDrawer()"></div>

<div id="cora-feature-drawer" class="fixed z-50 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]
    inset-x-0 bottom-0 max-h-[90vh] rounded-t-3xl translate-y-full
    md:inset-y-0 md:right-0 md:left-auto md:w-[480px] md:rounded-l-3xl md:rounded-tr-none md:translate-y-0 md:translate-x-full
    bg-white border-t md:border-t-0 md:border-l border-zinc-200 p-6 overflow-y-auto flex flex-col shadow-2xl">
    
    <!-- Mobile Drag Handle -->
    <div class="md:hidden w-10 h-1 rounded-full bg-zinc-300 mx-auto mb-4 shrink-0"></div>
    
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-zinc-100 shrink-0">
        <h3 class="text-sm font-bold text-zinc-900 font-display">Create New Resource</h3>
        <button type="button" onclick="window.coraCloseFeatureDrawer()" class="w-7 h-7 rounded-lg hover:bg-zinc-100 flex items-center justify-center text-zinc-400 hover:text-zinc-900 transition-colors">
            ✕
        </button>
    </div>
    
    <!-- Form Body -->
    <div class="py-4 space-y-4 flex-1">
        <!-- Form Fields -->
    </div>
    
    <!-- Actions -->
    <div class="pt-4 border-t border-zinc-100 flex items-center justify-end gap-2 shrink-0">
        <button type="button" onclick="window.coraCloseFeatureDrawer()" class="px-4 py-2 text-xs font-semibold text-zinc-600 hover:text-zinc-900">Cancel</button>
        <button type="button" onclick="window.coraSubmitFeatureForm()" class="px-4 py-2 bg-zinc-950 text-white text-xs font-semibold rounded-xl hover:bg-zinc-800">Save Resource</button>
    </div>
</div>
```

---

### Step 5: Route Registration & Tab Dispatcher
In `admin-dashboard.php`, register the new route inside the view tab router:
```php
case 'my-feature':
    require_once plugin_dir_path(__FILE__) . 'views/view-my-feature.php';
    break;
```

---

## 3. Explicit Save Workflow Pattern

For configuration panels and module customizers (such as `view-feature-hub.php` and `view-settings-suite.php`), enforce the **Explicit Save Workflow**:
1. State changes update a local JavaScript state object in memory without immediate AJAX calls.
2. An unsaved changes banner slides up from the bottom (`translate-y-0`) displaying an alert indicator and a "Save Changes" button.
3. Clicking "Save Changes" dispatches a single batch AJAX payload to atomically persist all modifications.
4. On success, `window.coraShowToast('Settings saved successfully.', 'success')` is called and the unsaved changes banner slides away.

---

## 4. Regression & E2E Validation

Verify every new feature against the following automated and manual criteria:
* **Tenant Isolation**: Confirms data created in Agency 1 is completely invisible to Agency 2.
* **Mobile Responsiveness**: Confirms all action panels open as bottom sheets without horizontal overflow.
* **No Browser Defaults**: Ensures zero native `alert()` or `confirm()` calls exist.
* **Zero Naked `!important`**: Ensures no un-namespaced global CSS overrides were introduced.

---

*Cora Developer Feature Guide v4.9.32 — Last updated: September 2026.*
