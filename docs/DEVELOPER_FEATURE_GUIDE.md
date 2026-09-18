# Cora Platform — Developer Feature & Optimization Guide (v4.9.118)

This guide defines the standardized architectural patterns, blueprints, and performance guidelines for engineering new modules and extending features across the Cora SaaS Workspace (`app/public/wp-content/plugins/cora-workspace`) and Marketing Frontend (`cora-frontend`).

---

## 1. Core Architectural Principles & Zero-Regression Policy

1. **Strict Module Isolation**: Modifying or introducing a module must NEVER break, alter, or cause side effects in neighboring views.
2. **Dedicated Schema & Tenant Scope**: Always isolate records via `agency_id = %d` (and optionally `branch_id = %d`) on custom `wp_cora_*` tables. Never mix multi-tenant operational data in `wp_posts` / `wp_postmeta`.
3. **Atomic Design System Tokens**: Utilize Level 00–04 tokens from `cora-design-tokens.js` (and `cora-tokens.ts` for Next.js).
4. **Dialogue & Mobile Ergonomics SOP**:
   - Direct all alerts, errors, and system confirmations to the custom monochromatic Toast Notification system (`window.coraShowToast`).
   - **Zero Mobile Side Drawers**: All mobile action panels, multi-step creators, and filter trays MUST open as bottom-up slide sheets (`translate-y-full` to `translate-y-0`).
   - **Top-Down Floating Banners (Mobile) / Dynamic Offset (Desktop)**: Alerts and toasts float from top-center (`top: 68px` on mobile) to eliminate collisions with bottom sheets and navigation islands. On desktop, toasts anchor bottom-right and dynamically elevate above active Studio Drawers.
5. **Universal Body Scroll Lock System**: Always call `window.coraLockScroll()` when opening any modal, drawer, or bottom sheet, and `window.coraUnlockScroll()` upon closing. Ensure scrollable inner containers are marked with `.cora-drawer-scrollable` or `[data-cora-scrollable]`.
6. **Zero Naked Global `!important` Utilities**: Never declare un-namespaced global utility overrides with `!important` (e.g. `.hidden { display: none !important; }`). All visibility states must use scoped component classes (e.g. `.cora-drawer.collapsed`, `.cora-modal:not(.open)`).
7. **Mobile Touch Snappiness**: Enforce `touch-action: manipulation; -webkit-tap-highlight-color: transparent;` on all interactive buttons and triggers to eliminate the 300ms tap delay.
8. **High-Speed Micro-Cache Layer**: Use `cora_cache_get()` and `cora_cache_set()` for sub-millisecond query caching.
9. **Semantic RESTful Routing**: Always use semantic path routing (`/workspace/{subpage}`) rather than JavaScript void links.
10. **Phone Input Validation**: Enforce numeric regex checks (`/^[0-9+ -]{7,15}$/`) across all contact forms and profiles.
11. **Security URL Masking**: Ensure all internal asset and core requests route through `/assets/` and `/core/` without exposing raw WordPress paths.
12. **Strict Single Workspace Owner Policy**: Every agency must have exactly one Workspace Owner. Never allow multiple owners per tenant or expose the Workspace Owner role in standard member assignment dropdowns.
13. **Free Map Tiles & Geolocation SOP**: All GIS/mapping features must utilize free, unmetered tiles (Esri World Imagery, Esri Streets, OpenStreetMap, CartoDB Dark) with zero watermarks and zero external API keys.
14. **Strict Role Scoping & Terminal Isolation**: For operational field roles (such as `cora_field_vendor`), implement server-side route redirection, omit central administrative containers from the DOM, lock mobile island navigation strictly to terminal/AI tools, strip topbar chrome (`.cora-driver-mode-active`), suppress simulation banners, and ground the AI Copilot strictly to route and sales operations.
15. **Safe Restocking Rollback Protocol**: Any deletion or editing of field transactions (consignments or spot invoices) must atomically reverse stock debits and restore unallocated units back to parent inventory balances.
16. **Single Consolidated 24-Hour Executive PDF Reporting & Anti-Spam Policy**: Ephemeral and recurring micro-events (SEO ranking shifts, morning/evening attendance pings, individual location updates, user status logs, 0-task briefings) are strictly prohibited from dispatching emails and MUST route 100% to In-App Bell and PWA Web Push alerts. Master operational summaries, sales figures, and AI directives are consolidated into a single 24-Hour Executive PDF Report delivered strictly once per 24 hours per owner.
17. **CRM Kanban & High-Density Card Pattern**: Lead cards must adhere to the ultra-compact 3-level anatomy (Header, Context Badges, 1-tap single-row quick outreach footer: WhatsApp/Phone/Email). Kanban columns must feature in-column search, context sorting, custom pastel column tints, and real-time live count/valuation synchronization.
18. **Agency Team Governance & Dynamic Roles Blueprint**: Dynamic custom roles must be stored in `wp_cora_roles` with tenant scoping. Permissions matrices must gate access at runtime across active features, with strict immutable lock on Workspace Owner privileges.
19. **Tab Customization Engine Pattern**: Desktop and mobile tab reordering drawers must support drag-and-drop handles, toggleable visibility, and dual-layer persistence (localStorage for instant 0ms painting + asynchronous user meta AJAX updates).
20. **Dedicated CRM Group Navigation Hierarchy**: The platform establishes CRM as an independent first-class navigation group housing Leads Pipeline, Calendar, and Finance across all industry verticals, ensuring high-velocity deal tracking and revenue visibility.

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
<div class="cora-view-container max-w-[1280px] mx-auto p-4 md:p-8 pb-28 md:pb-12">
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

## 4. Field Ops & Geolocation Tracking Pattern (v4.9.58)

When implementing or extending geolocation services:
1. **Touch Pan Mode**: Always wrap interactive Leaflet maps with a touch pan mode toggle (`isTouchPanEnabled`) to allow mobile users to scroll past the map without getting trapped in map dragging.
2. **Tile Layer Definitions**:
```javascript
const mapLayers = {
    streets: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 }),
    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 }),
    osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }),
    dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 })
};
```
3. **Stop & Dwell Calculation**: Minimum 300 seconds (5 minutes) within a 50m radius constitutes a stationary stop.

---

## 5. Super Admin Feature Flag Enforcement

To gate features dynamically at runtime:
```php
if (function_exists('cora_is_feature_enabled_for_agency')) {
    if (!cora_is_feature_enabled_for_agency('field_ops', $agency_id)) {
        wp_send_json_error(['message' => 'This module is not enabled for your workspace tier.'], 403);
    }
}
```

---

## 7. Inventory & Field Logistics Engineering Blueprint (v4.9.60 - v4.9.103)

When extending the Stationery Manufacturing or Field Logistics engines:

### 7.1 3-Step Guided SKU Studio Drawer Pattern
Always structure factory product creation/editing forms into the standardized 3-step sequence:
1. `Step 1: Identity & Media` (Product name, SKU, HSN code, packaging units, and image upload via FormData keys `image` and `image_file`).
2. `Step 2: Pricing & GST Margin Math` (Factory cost, margin %, wholesale rate, retailer spread %, MRP, and GST slab).
3. `Step 3: Factory Stock & Logistics` (Opening units, reorder alert threshold, and warehouse bin location).

### 7.2 GST & Margin Mathematical Formulations
All financial calculations must execute identically on both client (`admin-script.js`) and server (`class-cora-inventory-engine.php`):
```javascript
// Mathematical Formula Standards
const factoryMargin = wholesalePrice - baseCost;
const factoryMarginPercent = baseCost > 0 ? ((factoryMargin / baseCost) * 100).toFixed(1) : 0;
const retailerSpread = mrp - wholesalePrice;
const retailerMarginPercent = mrp > 0 ? ((retailerSpread / mrp) * 100).toFixed(1) : 0;
const gstTaxLiability = (wholesalePrice * (gstRate / 100)).toFixed(2);
const netBasePrice = (wholesalePrice / (1 + (gstRate / 100))).toFixed(2);
```

### 7.3 Zero-Cache 1x1 Email Open Tracking Pixel Pattern
When dispatching consignment assignment emails:
```php
$tracking_url = admin_url('admin-ajax.php?action=cora_inventory_track_email_open&token=' . rawurlencode($consignment['tracking_token']));
$pixel_html = '<img src="' . esc_url($tracking_url) . '" width="1" height="1" style="display:none;width:1px;height:1px;border:0;" alt="" />';
```
The endpoint must return a transparent 1x1 GIF with `Cache-Control: no-cache, no-store, must-revalidate` and immediately update `email_opened_at = current_time('mysql')`.

### 7.4 Safe Restocking Rollback on Invoice / Consignment Deletion
When an invoice or consignment is deleted, never leave stock stranded:
```php
// Rollback spot sales items back to active consignment stock
$items = $wpdb->get_results($wpdb->prepare(
    "SELECT product_id, quantity FROM {$wpdb->prefix}cora_inventory_sales_items WHERE sale_id = %d",
    $sale_id
));
foreach ($items as $item) {
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->prefix}cora_inventory_consignment_items 
         SET remaining_quantity = remaining_quantity + %d 
         WHERE consignment_id = %d AND product_id = %d",
        $item->quantity, $consignment_id, $item->product_id
    ));
}
```

---

## 8. Dynamic Dashboard Analytics & Mobile Navigation Customizer (v4.9.63, v4.9.92)

When integrating new dashboard metrics or modules:
1. **Metric Catalog Registration**: Register key and calculation callback in `$cora_available_metrics` array.
2. **Mobile Island Slot Registration**: Add module slug and SVG icon path to `$cora_nav_customizer_modules`.
3. **Empty State Requirement**: Always render a clean monochromatic empty state when search filters produce zero matches.
4. **Human-Readable Labels**: Ensure all slot preview pills render clean title-cased labels (e.g. `coraFormatModuleName(slug)`).

---

## 9. Single Consolidated 24-Hour Executive PDF Reporting Blueprint (v4.9.103)

When extending executive reporting or event notification triggers:
1. **Micro-Event Email Ban**: Never invoke `wp_mail()` for transient state notifications (e.g., SEO score updates, check-in pings, location alerts). Always use `cora_add_notification()` and `cora_pwa_send_push_notification()`.
2. **24-Hour Master Briefing Generation**: Aggregate tenant activity across `wp_cora_ledger`, `wp_cora_inventory_sales`, `wp_cora_inventory_consignments`, and `wp_cora_attendance` into a structured daily audit snapshot.
3. **Recipient Deduplication**: When dispatching executive reports across multiple tenants, deduplicate recipient email arrays so each unique workspace owner receives exactly one aggregated or clean per-tenant report.
4. **Rate Limit Lock**: Check `get_transient("cora_exec_report_lock_{$agency_id}")` before triggering report dispatches to prevent concurrent or repeated runs.

---

## 10. Regression & E2E Validation

Verify every new feature against the following automated and manual criteria:
* **Tenant Isolation**: Confirms data created in Agency 1 is completely invisible to Agency 2.
* **Single Owner Guard**: Confirms existing workspace owners cannot be duplicated or downgraded via user management.
* **Driver Mode Isolation**: Confirms driver accounts and vendor mode strip administrative headers, sidebars, and navigation islands.
* **Mobile Responsiveness**: Confirms all action panels open as bottom sheets without horizontal overflow.
* **No Browser Defaults**: Ensures zero native `alert()` or `confirm()` calls exist.
* **Zero Naked `!important`**: Ensures no un-namespaced global CSS overrides were introduced.
* **Phone Digit Constraints**: Confirms contact fields enforce numeric validation.
* **Scroll Lock State**: Confirms `html.cora-scroll-locked` is applied on drawer open and removed on close.
* **Tour Step Compatibility**: Validates that new high-level actions integrate with `#cora-platform-tour` targets.

---

## 11. CRM Kanban & High-Density Card Engineering Blueprint (v4.9.109 - v4.9.118)

When developing or extending CRM lead pipelines:

### 11.1 Ultra-Compact 3-Level Lead Card Schema
Always structure lead card DOM elements into 3 distinct functional tiers:
1. **Header Tier**: Client/Company name, urgent/priority dot, formatted deal amount in monospace currency (`₹XX,XXX`).
2. **Context Tier**: Stage badge, source pill (WhatsApp/Referral/Ads), estimated close timeline chip.
3. **Footer Action Tier**: Single-row compact CTA buttons (`wa.me` WhatsApp direct trigger, `tel:` dialer, `mailto:` composer, and stage shift popover).

### 11.2 In-Column Filtering & Dynamic Counter Synchronization
Kanban column controllers must implement client-side instant filtering:
```javascript
// Filter cards in-column with 0ms lag
function coraFilterKanbanColumn(stageId, query) {
    const cards = document.querySelectorAll(`[data-stage="${stageId}"] .cora-lead-card`);
    let visibleCount = 0;
    let visibleTotal = 0;
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        const matches = text.includes(query.toLowerCase());
        card.style.display = matches ? '' : 'none';
        if (matches) {
            visibleCount++;
            visibleTotal += parseFloat(card.dataset.dealValue || 0);
        }
    });
    
    // Update live column count and valuation
    document.getElementById(`count-${stageId}`).textContent = visibleCount;
    document.getElementById(`total-${stageId}`).textContent = coraFormatCurrency(visibleTotal);
}
```

---

## 12. Dynamic Role Governance & Tab Customization Blueprint (v4.9.104 - v4.9.108)

When implementing RBAC or tab customizer features:
1. **Tenant-Scoped Roles**: Store custom agency roles in `wp_cora_roles` with `agency_id = %d` and unique slugs.
2. **Immutable Owner Lock**: Ensure the Workspace Owner role permissions cannot be edited or unassigned:
```php
if ($role_slug === 'workspace_owner') {
    wp_send_json_error(['message' => 'Workspace Owner permissions are immutable.'], 403);
}
```
3. **Dual-Layer Tab Customization Persistence**:
   - Save tab ordering immediately to `localStorage.setItem('cora_tabs_order', JSON.stringify(order))` for instantaneous 0ms painting on refresh.
   - Fire a background AJAX request to `cora_save_tabs_order` to persist preferences to user meta (`cora_user_tabs_order`).

---

*Cora Developer Feature Guide v4.9.118 — Last updated: September 2026.*
