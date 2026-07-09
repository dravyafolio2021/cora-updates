# Visual Canvas Page Builder Integration Analysis Report

This report presents findings from the investigation of the Cora codebase to determine requirements for integrating the Visual Canvas page builder.

---

## 1. Observation

Direct observations and file paths from the Cora codebase:

### Sidebar Menu Rendering
In `app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php`, the sidebar navigation menu is defined as a static `<nav>` list container:
* **File Path**: `app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php`
* **Nav Menu Code Block**: Lines 2318 to 2576. E.g., for pages at lines 2539-2544:
  ```php
  <li class="cora-nav-item <?php echo $sub_page === 'pages' ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm text-zinc-650 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="pages" title="Pages">
      <div class="flex items-center gap-3">
          <span class="cora-nav-icon text-zinc-400"><svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg></span>
          <span class="cora-nav-text">Pages</span>
      </div>
  </li>
  ```
* **Mobile Navigation**: Defined using the `.cora-bottom-nav-item` elements at lines 7438 to 7481.
* **JavaScript Route Switching**: In `app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js`, the menu transition triggers redirection to `/workspace/{target}` at lines 107-114:
  ```javascript
  if (targetPageId !== coraREData.currentPage) {
      let siteUrl = coraREData.siteUrl || '';
      if (siteUrl.endsWith('/')) {
          siteUrl = siteUrl.slice(0, -1);
      }
      window.location.href = siteUrl + '/workspace/' + targetPageId;
  }
  ```

### Allowed Pages Check
In `app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php`, allowed sub-pages are checked inside `cora_real_estate_ai_handle_workspace_route()` (hooked to `template_redirect` at line 233):
* **File Path**: `app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php`
* **Route Validation**: Lines 157-170:
  ```php
  $cora_permissions = get_option( 'cora_role_permissions', array() );
  $current_user_role = ! empty( $user->roles ) ? $user->roles[0] : 'subscriber';
  
  $allowed_features = isset( $cora_permissions[$current_user_role] ) ? $cora_permissions[$current_user_role] : array();
  if ( $current_user_role === 'administrator' ) {
      $allowed_features = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'attendance', 'tasks' );
  }

  // Prevent accessing disallowed sub-pages
  if ( $sub_page !== 'dashboard' && $sub_page !== 'feature-hub' && ! in_array( $sub_page, $allowed_features ) ) {
      wp_redirect( home_url( '/workspace/dashboard' ) );
      exit;
  }
  ```
* **Frontend Permission Checks**: Hardcoded user capabilities list for the `'administrator'` role in `app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js` (lines 99 and 2369). E.g.:
  ```javascript
  allowed = ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'attendance', 'tasks', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite'];
  ```

### Hooking Template Redirects
* In `cora-real-estate.php`, the plugin intercepts requests on `template_redirect`. Example:
  ```php
  add_action( 'template_redirect', 'cora_real_estate_ai_serve_frontend_homepage', 5 );
  ```

### AI Configuration & API Keys
* Option names: `cora_re_ai_gemini_key` and `cora_re_ai_openai_key` are base64-encoded.
* Active model configuration option name: `cora_re_active_ai_model`.
* Standard structure for fetching keys in `cora_ajax_ai_chat` (lines 4121-4122):
  ```php
  $gemini_key_b64 = get_option( 'cora_re_ai_gemini_key', '' );
  $openai_key_b64 = get_option( 'cora_re_ai_openai_key', '' );
  ```

---

## 2. Logic Chain

1. **Sidebar Integration**: To add a new subpage for the Visual Canvas page builder, we must declare a new navigation item in the sidebar structure inside `admin-dashboard.php`. We will also need to add matching rules to the Mobile/Responsive bottom menu bar if needed.
2. **Tab Access Control**: Since subpage transitions are controlled by both PHP route verification and frontend JS dynamic role enforcement, adding a new tab like `visual-builder` requires registering `'visual-builder'` in:
   - `$allowed_features` inside `cora-real-estate.php`.
   - The hardcoded `'administrator'` allowed list in `admin-script.js`.
3. **Bypassing Theme Constraints**: Serving page layouts designed visually in GrapesJS requires that we bypass standard WordPress theme styles, sidebars, and templates. In WordPress, intercepting template rendering is cleanly done by hook redirection during the `template_redirect` action (prior to loading standard page templates).
4. **GrapesJS Frontend Setup**: `views/view-visual-builder.php` must render the GrapesJS editor canvas. Standard script/style CDNs must be included in this view to load GrapesJS core stylesheet and scripts directly, keeping workspace styling clean and isolated.
5. **AI Integration**: The `Prompt-to-Layout` handler must use the existing API keys structure (Gemini key and OpenAI key) saved as base64 options. The JSON parsing logic should safely parse the LLM outputs and return code structures representing layout modules.

---

## 3. Caveats

* Standard page template selection in WordPress (e.g. full-width, default) relies on files located in the active WordPress theme directory. Intercepting these templates via `template_redirect` bypasses theme logic entirely. However, if visual builder layouts require specific theme stylesheets or footer scripts, these would need to be loaded explicitly in the redirection view.
* OpenAI's `response_format` JSON mode and Gemini's `responseMimeType` are utilized to ensure the API output matches a structural JSON schema.

---

## 4. Conclusion

For a fully integrated Visual Canvas page builder:
1. **Sidebar Integration**: Edit `admin-dashboard.php` to add a new `cora-nav-item` inside the `AI & Content` group, targeting `visual-builder`.
2. **Access Verification**: Update the administrator features list inside `cora-real-estate.php` and `admin-script.js` to include `'visual-builder'`.
3. **Bypassing Theme Constraints**: Add a `template_redirect` hook in `cora-real-estate.php` to catch singular page queries that match a custom layout flag (e.g., `_cora_is_visual_builder_page`), render the clean layout HTML/CSS directly, and terminate execution using `exit;`.
4. **CDN Integration**: In `views/view-visual-builder.php`, load GrapesJS core elements via CDN URLs (cdnjs & unpkg) inside `<head>` and `<body>` structures.
5. **AI Code Layout Generator**: Register a custom AJAX endpoint `wp_ajax_cora_ai_prompt_to_layout` in `cora-real-estate.php` that implements the standard base64 decoding routine for API keys, formats the system prompts, calls the APIs, and returns JSON structure containing the generated HTML and CSS.

---

## 5. Verification Method

* Run verification tests or inspect pages using standard theme checking tools to confirm theme styles are successfully bypassed.
* Inspect `admin-dashboard.php`, `cora-real-estate.php`, and `assets/js/admin-script.js` to check formatting correctness and structure alignments.
