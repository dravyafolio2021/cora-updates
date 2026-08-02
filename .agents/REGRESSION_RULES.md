# Cora Platform - Regression Testing & Safety Rules

This document outlines the safety checks and architectural guidelines to prevent regression bugs in upcoming platform updates and new industry modules.

## 1. AJAX Routine Hook Registration Naming Safety
* **Rule**: All custom AJAX operations dispatched via client-side JavaScript (e.g. `$.post(coraREData.ajaxUrl, { action: 'cora_ajax_NAME', ... })`) must have corresponding server-side hooks registered in the main router file (`cora-workspace.php`).
* **Requirement**: For any AJAX action `cora_ajax_action_name`, you must register:
  ```php
  add_action( 'wp_ajax_cora_ajax_action_name', 'cora_ajax_action_name' );
  ```
  And if accessed by non-logged-in users (where applicable):
  ```php
  add_action( 'wp_ajax_nopriv_cora_ajax_action_name', 'cora_ajax_action_name' );
  ```
* **Guard**: Never assume standard prefixes (e.g. mapping `wp_ajax_cora_create_...` to match a JS dispatch of `cora_ajax_create_...`). Keep names 1-to-1.

## 2. Centralized, Dynamic Industry-Scoped Labels
* **Rule**: Never hardcode localized industry features or quota terminology (e.g. "Equipment", "Showings", "Shoots", "Bookings") directly inside template views or layout markup.
* **Requirement**: Look up labels dynamically based on the active industry context. Ensure the `$is_studio_mode` flag is resolved:
  ```php
  $is_studio_mode = ( strpos( strtolower( $active_industry ), 'photo' ) !== false || strpos( strtolower( $active_industry ), 'studio' ) !== false );
  ```
* **Label Maps**: Use a centralized label array (e.g. `$feature_labels`) mapping keys to dynamic values:
  - `crm_leads` -> Real Estate: `"Buyer Leads (CRM)"` | Studio: `"Client Leads (CRM)"`
  - `showings_bookings` -> Real Estate: `"Showings & Bookings"` | Studio: `"Shoots & Bookings"`
  - `equipment` -> Real Estate: `"Property Listings"` | Studio: `"Camera Equipment"`
  - `quota_label` -> Real Estate: `"Max Showing/Listing Quota (Monthly)"` | Studio: `"Max Shoot/Booking Quota (Monthly)"`

## 3. Duplicate Function Declarations Prevention
* **Rule**: Keep the codebase clean of duplicate legacy functions.
* **Requirement**: When updating complex methods (such as `cora_ajax_delete_custom_role`), clean up legacy duplicates and wrap definitions in:
  ```php
  if ( ! function_exists( 'function_name' ) ) {
      function function_name() { ... }
  }
  ```
  Always ensure that the active, corrected version is not pre-empted by an un-scoped legacy version earlier in the file.

## 4. Strict Module Isolation & Safe Execution Requirement
* **Rule**: Any code edit, layout update, or script change performed on a specific module (e.g. Media Library, Content Suite, Document Vault, Forms, Crew Scheduler, Settings) must be strictly isolated to that target context.
* **Requirement**: Updating a feature must NEVER alter, degrade, or disturb the functionality, styling, or API contracts of any other module.
* **Scope Guarding**: Use component-scoped CSS classes, namespaced event handlers (e.g. `$(document).on('click.coraVaultModule', ...)`), and isolated state keys.

