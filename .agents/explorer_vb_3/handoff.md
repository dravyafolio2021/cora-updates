# Visual Canvas Page Builder Integration Analysis Report

## 1. Observation

### Sidebar Menu Rendering
In `app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php`, the sidebar container elements begin at line 2274 and end at line 2611:
```html
2274:     <aside class="cora-sidebar w-64 bg-[#f7f7f5] border-r border-zinc-200/80 flex flex-col justify-between shrink-0 h-screen fixed lg:sticky top-0 left-0 z-50 lg:z-30 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 flex">
```
The navigation elements are listed inside a `<nav>` tag:
```html
2319:             <nav class="cora-sidebar-nav px-2 py-4 space-y-6">
```
Each navigation link corresponds to a sub-page checked against a static string variable, for example:
```html
2539:                         <li class="cora-nav-item <?php echo $sub_page === 'pages' ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm text-zinc-650 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="pages" title="Pages">
```

### Access Control Verification
In `app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php`, user capability checks and allowed pages are verified within `cora_real_estate_ai_handle_workspace_route()`.
Specifically:
- Administrator role allowed subpages are listed at lines 162-164:
```php
162:         if ( $current_user_role === 'administrator' ) {
163:             $allowed_features = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'attendance', 'tasks' );
164:         }
```
- Subpage verification occurs at lines 167-170:
```php
167:         if ( $sub_page !== 'dashboard' && $sub_page !== 'feature-hub' && ! in_array( $sub_page, $allowed_features ) ) {
168:             wp_redirect( home_url( '/workspace/dashboard' ) );
169:             exit;
170:         }
```

### Template Redirect Hook
In `cora-real-estate.php`, custom page serving (such as the frontend homepage) uses the `template_redirect` hook to render static files and calls `exit` to bypass default WordPress themes. For example:
```php
1834: function cora_real_estate_ai_serve_frontend_homepage() {
1835:     if ( is_front_page() && ! is_admin() ) {
1836:         $frontend_file = plugin_dir_path( __FILE__ ) . 'nitin-arora-photography/index.html';
...
1854:             echo $html;
1855:             exit;
1856:         }
1857:     }
1858: }
1859: add_action( 'template_redirect', 'cora_real_estate_ai_serve_frontend_homepage', 5 );
```

### AI Configuration & API Proxying
In `cora-real-estate.php` at lines 4120-4122, API keys for OpenAI/Gemini are fetched and decoded:
```php
4120:     $active_model   = get_option( 'cora_re_active_ai_model', 'cora-core-v2' );
4121:     $gemini_key_b64 = get_option( 'cora_re_ai_gemini_key', '' );
4122:     $openai_key_b64 = get_option( 'cora_re_ai_openai_key', '' );
```
If configured, the message is sent to Gemini (using `gemini-2.0-flash`) or OpenAI (using `gpt-4o-mini`) via `wp_remote_post`. If neither is configured, it falls back to a JSON error response at line 4205.

---

## 2. Logic Chain

1. **Sidebar Menu Expansion**: Since the sidebar rendering in `admin-dashboard.php` is represented by static list items (`<li>` tags) inside groups like Workspace, CRM, Operations, etc., integrating the visual builder page requires appending a new item checking `$sub_page === 'visual-builder'`.
2. **Access Control Settings**: Access validation checks in `cora-real-estate.php` restrict access to a hardcoded array of `allowed_features` (e.g., line 163). Attempting to load `/workspace/visual-builder` without updating this list will trigger a redirect back to `/workspace/dashboard`. Therefore, we must add `'visual-builder'` to the `$allowed_features` array.
3. **Bypassing Theme Constraints**: WordPress themes override output templates unless interrupted. The standard hook where custom content can output and call `exit` to bypass layout wrapper logic is `template_redirect`. Using this hook to check post meta tags (like `_cora_visual_builder`) is consistent with standard route handling in Cora.
4. **GrapesJS Layout Design**: The view `views/view-visual-builder.php` must render the GrapesJS editor using CDN libraries. In order to conform to visual theme constraints (Notion/Shopify Monochromatic style: neutral grays, clean thin vector SVGs, custom slide drawers for settings, custom toaster notifications), GrapesJS panels, canvas, and blocks need custom CSS overrides.
5. **AI Layout generation**: The prompt-to-layout AJAX endpoint should follow `cora_ajax_ai_chat()` routing logic. By requesting JSON outputs `{ "html": "...", "css": "..." }` and falling back to a pre-defined monochromatic Claude-style dashboard mockup, the editor maintains high usability and visual consistency even if API keys are missing or offline.

---

## 3. Caveats
- Since `views/view-visual-builder.php` does not exist in the source repository yet, the integration plan assumes the files will be created in that specific directory.
- Database schemas are assumed to use standard WordPress post/meta architecture for custom layout storage.

---

## 4. Conclusion
Integrating the Visual Canvas builder requires:
1. Registering the `'visual-builder'` key in the `$allowed_features` list inside `cora-real-estate.php` and matching it in `admin-dashboard.php` to include the view file `views/view-visual-builder.php`.
2. Appending the visual builder sidebar item to `admin-dashboard.php`.
3. Hooking into `template_redirect` at default priority `10` to intercept pages saved with the visual builder meta properties.
4. Enqueuing GrapesJS CSS/JS via CDN in the new view, overriding layout components to follow strict monochromatic formatting.
5. Adding an AJAX handler `cora_ajax_visual_builder_generate_layout` mimicking the AI proxy client logic of `cora_ajax_ai_chat()`, providing a static template fallback.

---

## 5. Verification Method

### Files to Inspect:
- `app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php`
- `app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php`

### Verification Steps:
1. Verify that checking the slug `/workspace/visual-builder` succeeds after adding it to the allowed list (admin role should not be redirected).
2. Validate that saving visual builder metadata on a WordPress page renders only the visual editor output, completely ignoring theme constraints.
3. Confirm that querying the custom AJAX layout handler generates a valid layout stub when API keys are empty.
