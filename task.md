# Task List - Custom Workspace Mode

- `[ ]` 1. Create the Custom Workspace Module Class
  - `[ ]` Create `modules/custom-workspace/class-custom-module.php` with dynamic navigation filtering
  - `[ ]` Register the custom module in `modules/class-cora-module-registry.php`
- `[ ]` 2. Enable Custom Industry Option in Backend
  - `[ ]` Whitelist `custom` in `cora_get_active_industry()` in `cora-workspace.php`
  - `[ ]` Support `custom` activation in `cora_ajax_onboarding_activate_workspace()`
  - `[ ]` Register the `cora_save_custom_features` AJAX handler
- `[ ]` 3. Implement Interactive Module Toggles in View Page
  - `[ ]` Modify `views/view-feature-hub.php` to render custom checkboxes/switches if active industry is `custom`
  - `[ ]` Add jQuery handlers to post toggled states to `cora_save_custom_features` AJAX action
- `[ ]` 4. Update Admin Dashboard Settings dropdowns and Onboarding cards
  - `[ ]` Add "Custom Workspace" option in Settings Suite dropdown
  - `[ ]` Add "Custom Workspace" option in Super Admin Workspace Edit drawer
  - `[x]` Add `#cora-island-ai-popover` HTML structure to `#cora-mobile-floating-island` in `admin-dashboard.php`
- `[/]` Add interactive toggle & click-outside event handlers in `assets/js/admin-script.js` and resolve any syntax issues
  - `[ ]` Bump version to `3.4.18`
  - `[ ]` Deploy updates to staging and demo environments
