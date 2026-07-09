# Handoff: Visual Canvas Page Builder Integration Analysis

## 1. Observation

During the exploration of the Cora Real Estate plugin at `/Users/shrutian/Desktop/cora`, the following files, code structures, and mechanics were observed:

### A. Sidebar Navigation and Menu Rendering
- **File**: `app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php`
- **Location**: Menu container `<!-- Navigation Menu -->` / class `cora-sidebar-nav` (lines 2318 to 2575).
- **Structure**: The navigation items are list items (`<li class="cora-nav-item ...">`) structured into groups like `Workspace`, `CRM & Booking`, `Operations`, `Content & Marketing`, `WordPress Core`, and `Quick Links`. For example:
  ```php
  <li class="cora-nav-item <?php echo $sub_page === 'pages' ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm text-zinc-650 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="pages" title="Pages">
      <div class="flex items-center gap-3">
          <span class="cora-nav-icon text-zinc-400">...</span>
          <span class="cora-nav-text">Pages</span>
      </div>
  </li>
  ```
- **Subpage Inclusion**: In the main content body (lines 6274 to 6320), the dashboard checks `$sub_page` and loads corresponding subviews:
  ```php
  <?php if ( $sub_page === 'pages' ) : ?>
      <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-pages.php'; ?>
  <?php endif; ?>
  ```

### B. Allowed Pages List & Subpage Access Checks
- **File 1**: `app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php`
  - **Route Handlers / Access Control**: In `cora_real_estate_ai_handle_workspace_route()` (lines 151-170), the backend parses the `sub_page` variable from the request URL path and validates it against the `$allowed_features` list:
    ```php
    $sub_page = isset( $path_parts[1] ) ? sanitize_title( $path_parts[1] ) : 'dashboard';
    // ...
    if ( $current_user_role === 'administrator' ) {
        $allowed_features = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'attendance', 'tasks' );
    }
    // Prevent accessing disallowed sub-pages
    if ( $sub_page !== 'dashboard' && $sub_page !== 'feature-hub' && ! in_array( $sub_page, $allowed_features ) ) {
        wp_redirect( home_url( '/workspace/dashboard' ) );
        exit;
    }
    ```
  - **Default Role Seeds**: Inside `cora_real_estate_ai_seed_data()` (lines 722-771), default capability matrices are written to option `cora_role_permissions`.
  - **Permissions Save Handler**: In `cora_ajax_save_role_permissions()` (lines 1117-1130), administrator permissions are hardcoded when saving permissions matrix.
- **File 2**: `app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js`
  - **Frontend Navigation Gate**: In `window.coraNavigateTo()` (lines 95-105), the front-end checks permissions and blocks unauthorized redirects:
    ```javascript
    if (activeRole === 'administrator') {
        allowed = ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'attendance', 'tasks', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite'];
    }
    ```

### C. Theme Redirect and Homepage Interception
- **File**: `app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php`
- **Location**: Interception of custom frontend homepage rendering in `cora_real_estate_ai_serve_frontend_homepage()` (lines 1834-1859) hooked at `template_redirect` priority 5.

### D. AI Key Management and Chat Proxy Patterns
- **File**: `app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php`
- **Location**: AI Proxy configuration handler `cora_ajax_ai_chat()` (lines 4107-4210) retrieves decrypted keys:
  - Gemini: retrieves option `cora_re_ai_gemini_key`, decodes it via base64, and calls the Google Generative Language endpoints.
  - OpenAI: retrieves option `cora_re_ai_openai_key`, decodes it via base64, and calls the OpenAI Chat Completions API.

---

## 2. Logic Chain

Based on these observations, integrating the Visual Canvas builder requires linking together the dashboard navigation, role permissions, theme bypass, front-end editor UI, and backend AI handlers:

1. **Routing and Menu**: To make the Visual Builder accessible, we must register a new slug `visual-builder`. A navigation menu item should be appended to the sidebar navigation inside `admin-dashboard.php`, and a corresponding section check `if ( $sub_page === 'visual-builder' )` should load `views/view-visual-builder.php`.
2. **Access Control Authorization**: If we only add the menu item to the markup, the navigation will be blocked by both backend and frontend. Thus, we must add the `visual-builder` feature to the permitted feature arrays for the `administrator` (and potentially `cora_manager`) roles in:
   - `cora-real-estate.php` -> `cora_real_estate_ai_handle_workspace_route()`
   - `cora-real-estate.php` -> `cora_real_estate_ai_seed_data()`
   - `cora-real-estate.php` -> `cora_ajax_save_role_permissions()`
   - `assets/js/admin-script.js` -> `window.coraNavigateTo()`
3. **Bypassing Theme Restraints**: To let landing pages render exactly as designed without active theme wrappers (headers, sidebars, styles), a custom hook must be added early to the WordPress `template_redirect` loop. Checking for visual builder page metadata enables rendering the custom raw HTML and CSS and executing `exit` immediately.
4. **Editor Interface (GrapesJS)**: The `view-visual-builder.php` file must include the GrapesJS engine from CDN and initialize it inside a container styled under Notion/Shopify monochromatic UI aesthetics.
5. **AI Layout Generation Handler**: We can build `wp_ajax_cora_prompt_to_layout` to consume the prompt string and send a structured layout generation prompt requesting JSON containing `'html'` and `'css'`. Using base64-encoded BYOK keys allows using whichever provider (Gemini or OpenAI) the user has configured, falling back to a structured mockup if no key is configured.

---

## 3. Caveats

- **Network Restrictions**: Since we are in CODE_ONLY network mode, we could not issue outgoing cURL or curl-based requests to external servers to verify GrapesJS CDN availability or call live API endpoints.
- **Save Actions**: Storing page markup requires post meta mapping. This analysis assumes standard page post-types are used, utilizing `_cora_is_visual_builder`, `_cora_visual_builder_html`, and `_cora_visual_builder_css` keys.

---

## 4. Conclusion & Proposed Code Snippets

### A. Sidebar Menu Integration
In `app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php`, append a new navigation menu item:
```html
<li class="cora-nav-item <?php echo $sub_page === 'visual-builder' ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm text-zinc-650 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="visual-builder" title="Visual Builder">
    <div class="flex items-center gap-3">
        <span class="cora-nav-icon text-zinc-400">
            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="9" y1="3" x2="9" y2="21"></line>
                <line x1="15" y1="3" x2="15" y2="21"></line>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="3" y1="15" x2="21" y2="15"></line>
            </svg>
        </span>
        <span class="cora-nav-text">Visual Builder</span>
    </div>
</li>
```
And add subpage loading condition inside `admin-dashboard.php`:
```php
<?php if ( $sub_page === 'visual-builder' ) : ?>
    <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-visual-builder.php'; ?>
<?php endif; ?>
```

### B. Access Matrix Updates
In `cora-real-estate.php` and `assets/js/admin-script.js`, append `'visual-builder'` to the administrator features array.
- **In `cora-real-estate.php` line 163**:
  ```php
  $allowed_features = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'attendance', 'tasks', 'visual-builder' );
  ```
- **In `cora-real-estate.php` line 1126**:
  ```php
  $permissions['administrator'] = array( 'dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'settings', 'vault', 'portfolio', 'leads', 'clients', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'visual-builder' );
  ```
- **In `assets/js/admin-script.js` line 99**:
  ```javascript
  allowed = ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'attendance', 'tasks', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'visual-builder'];
  ```

### C. Hooking Template Redirect for Visual Builder Pages
Hook custom pages before standard template rendering to load them cleanly on the frontend:
```php
/**
 * Bypass active theme constraints for pages constructed by the Visual Page Builder.
 */
function cora_visual_builder_serve_page() {
    if ( is_singular( 'page' ) && ! is_admin() ) {
        $page_id = get_queried_object_id();
        $is_visual_builder = get_post_meta( $page_id, '_cora_is_visual_builder', true );
        
        if ( $is_visual_builder ) {
            $html = get_post_meta( $page_id, '_cora_visual_builder_html', true );
            $css  = get_post_meta( $page_id, '_cora_visual_builder_css', true );
            
            nocache_headers();
            header( 'Content-Type: text/html; charset=UTF-8' );
            ?>
            <!DOCTYPE html>
            <html <?php language_attributes(); ?>>
            <head>
                <meta charset="<?php bloginfo( 'charset' ); ?>">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php the_title(); ?></title>
                <?php wp_head(); ?>
                <?php if ( ! empty( $css ) ) : ?>
                    <style id="cora-vb-custom-styles"><?php echo $css; ?></style>
                <?php endif; ?>
            </head>
            <body class="cora-visual-builder-layout">
                <?php echo $html; ?>
                <?php wp_footer(); ?>
            </body>
            </html>
            <?php
            exit;
        }
    }
}
add_action( 'template_redirect', 'cora_visual_builder_serve_page', 5 );
```

### D. GrapesJS CDN Integration (`views/view-visual-builder.php`)
```php
<?php
/**
 * View: Visual Builder Canvas
 * Notion/Shopify Monochromatic Theme UI/UX
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!-- CDN Enqueues -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/grapesjs/0.21.2/css/grapes.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/grapesjs/0.21.2/grapes.min.js"></script>

<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Visual Page Builder</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Design customized, fast-loading landing pages with real-time AI layout generation.</p>
        </div>
    </div>
</div>

<div class="border border-zinc-200 rounded-lg overflow-hidden bg-white shadow-sm mt-6 flex flex-col md:flex-row" style="height: 750px;">
    <!-- Builder Canvas Wrapper -->
    <div class="flex-1 relative flex flex-col">
        <!-- AI Prompt Bar -->
        <div class="p-3 border-b border-zinc-200 flex items-center gap-3 bg-zinc-50">
            <input type="text" id="cora-ai-layout-prompt" placeholder="Ask AI to build a layout (e.g. 'Generate a luxury villa contact section')..." class="flex-1 bg-white border border-zinc-200 rounded-md px-3 py-1.5 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900 transition-all">
            <button onclick="coraGenerateAILayout()" class="px-4 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-md text-xs transition-colors cursor-pointer flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Generate Layout
            </button>
        </div>
        <!-- Editor Container -->
        <div id="cora-gjs-editor" class="flex-1"></div>
    </div>
</div>

<style>
    /* Monochromatic GrapesJS UI Custom Overrides */
    .gjs-one-bg { background-color: #ffffff !important; }
    .gjs-two-color { color: #18181b !important; }
    .gjs-three-bg { background-color: #f4f4f5 !important; }
    .gjs-four-color, .gjs-four-color-h:hover { color: #09090b !important; }
    .gjs-pn-btn { border-radius: 4px; color: #71717a !important; }
    .gjs-pn-btn.gjs-pn-active { background-color: #e4e4e7 !important; color: #09090b !important; box-shadow: none !important; }
    .gjs-cv-canvas { background-color: #fafafa !important; }
    .gjs-sm-sector-title { background-color: #fafafa !important; border-bottom: 1px solid #e4e4e7 !important; }
</style>

<script>
jQuery(document).ready(function($) {
    // Initialize GrapesJS Editor
    const editor = grapesjs.init({
        container: '#cora-gjs-editor',
        fromElement: true,
        height: '100%',
        width: 'auto',
        storageManager: false
    });

    window.coraGenerateAILayout = function() {
        const prompt = $('#cora-ai-layout-prompt').val();
        if (!prompt) {
            window.coraShowToast("Please enter a prompt first!");
            return;
        }

        window.coraShowToast("AI is generating your layout...");

        $.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_prompt_to_layout',
                security: coraREData.nonce,
                prompt: prompt
            },
            success: function(response) {
                if (response.success) {
                    const html = response.data.html;
                    const css = response.data.css;
                    editor.setComponents(html);
                    if (css) {
                        editor.setStyle(css);
                    }
                    window.coraShowToast("Layout generated successfully!");
                } else {
                    window.coraShowToast("Failed: " + response.data);
                }
            },
            error: function() {
                window.coraShowToast("Network/Server error generating layout.");
            }
        });
    };
});
</script>
```

### E. AI Prompt-to-Layout AJAX Handler
Add the following AJAX callback to `cora-real-estate.php`:
```php
/**
 * AJAX Action: Generate HTML/CSS layout structure from prompt
 */
function cora_ajax_prompt_to_layout() {
    check_ajax_referer( 'cora_ajax_nonce', 'security' );
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'Not authenticated.' );
    }

    if ( ! current_user_can( 'edit_pages' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized access.' );
    }

    $prompt = sanitize_textarea_field( $_POST['prompt'] ?? '' );
    if ( empty( $prompt ) ) {
        wp_send_json_error( 'No prompt provided.' );
    }

    $active_model   = get_option( 'cora_re_active_ai_model', 'cora-core-v2' );
    $gemini_key_b64 = get_option( 'cora_re_ai_gemini_key', '' );
    $openai_key_b64 = get_option( 'cora_re_ai_openai_key', '' );

    $system_prompt = "You are Cora AI Visual Page Builder. Your task is to generate clean, minimal modern landing page layouts based on the user's request.
You must return a valid JSON object only, with two keys: 'html' and 'css'.
The 'html' key must contain semantic HTML markup utilizing Tailwind CSS layout classes (such as flex, grid, gap, slate/zinc monochromatic color schemes). Follow the Notion/Shopify design aesthetic: white, black, slate, neutral grays, thin clean border strokes. DO NOT include <html>, <head>, or <body> tags.
The 'css' key must contain any custom CSS styling that cannot be achieved with Tailwind classes.
Example format:
{
  \"html\": \"<div class='p-8 max-w-xl mx-auto bg-white border border-zinc-200 rounded-lg shadow-sm'>...</div>\",
  \"css\": \"\"
}
Do not include any Markdown wrapping, backticks, or extra commentary. Only output the JSON object.";

    // ── Gemini Integration ──────────────────────────────────────────
    if ( ! empty( $gemini_key_b64 ) && ( $active_model === 'gemini' || $active_model === 'cora-core-v2' || empty( $openai_key_b64 ) ) ) {
        $api_key  = base64_decode( $gemini_key_b64 );
        $model_id = 'gemini-2.0-flash';
        $url      = "https://generativelanguage.googleapis.com/v1beta/models/{$model_id}:generateContent?key=" . urlencode( $api_key );

        $body = json_encode( array(
            'system_instruction' => array(
                'parts' => array( array( 'text' => $system_prompt ) )
            ),
            'contents' => array(
                array(
                    'role'  => 'user',
                    'parts' => array( array( 'text' => $prompt ) ),
                )
            ),
            'generationConfig' => array(
                'responseMimeType' => 'application/json',
                'maxOutputTokens'  => 2048,
                'temperature'      => 0.2,
            ),
        ) );

        $response = wp_remote_post( $url, array(
            'timeout' => 30,
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => $body,
        ) );

        if ( ! is_wp_error( $response ) ) {
            $code = wp_remote_retrieve_response_code( $response );
            $data = json_decode( wp_remote_retrieve_body( $response ), true );
            if ( $code === 200 && ! empty( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
                $raw_text = trim( $data['candidates'][0]['content']['parts'][0]['text'] );
                if ( preg_match( '/^```json(.*)```$/as', $raw_text, $matches ) ) {
                    $raw_text = trim( $matches[1] );
                }
                $json_data = json_decode( $raw_text, true );
                if ( $json_data && isset( $json_data['html'] ) ) {
                    wp_send_json_success( array(
                        'html'     => $json_data['html'],
                        'css'      => $json_data['css'] ?? '',
                        'provider' => 'gemini',
                        'model'    => $model_id,
                    ) );
                }
            }
        }
    }

    // ── OpenAI Integration ──────────────────────────────────────────
    if ( ! empty( $openai_key_b64 ) && ( $active_model === 'gpt-4o' || $active_model === 'openai' || empty( $gemini_key_b64 ) ) ) {
        $api_key  = base64_decode( $openai_key_b64 );
        $model_id = 'gpt-4o-mini';
        $url      = 'https://api.openai.com/v1/chat/completions';

        $body = json_encode( array(
            'model'    => $model_id,
            'messages' => array(
                array( 'role' => 'system', 'content' => $system_prompt ),
                array( 'role' => 'user',   'content' => $prompt ),
            ),
            'response_format' => array( 'type' => 'json_object' ),
            'max_tokens'  => 2048,
            'temperature' => 0.2,
        ) );

        $response = wp_remote_post( $url, array(
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json',
            ),
            'body' => $body,
        ) );

        if ( ! is_wp_error( $response ) ) {
            $code = wp_remote_retrieve_response_code( $response );
            $data = json_decode( wp_remote_retrieve_body( $response ), true );
            if ( $code === 200 && ! empty( $data['choices'][0]['message']['content'] ) ) {
                $raw_text = trim( $data['choices'][0]['message']['content'] );
                $json_data = json_decode( $raw_text, true );
                if ( $json_data && isset( $json_data['html'] ) ) {
                    wp_send_json_success( array(
                        'html'     => $json_data['html'],
                        'css'      => $json_data['css'] ?? '',
                        'provider' => 'openai',
                        'model'    => $model_id,
                    ) );
                }
            }
        }
    }

    // ── Mock Fallback (In case of missing keys or network failure) ────────────────
    $mock_html = '
    <div class="p-8 max-w-xl mx-auto bg-white border border-zinc-200 rounded-lg shadow-sm">
        <h3 class="text-lg font-bold text-zinc-950">Property Viewing: Premium Villa</h3>
        <p class="text-sm text-zinc-500 mt-2">Beautiful minimal structure located in New Delhi, designed with monochrome styling.</p>
        <div class="mt-6 flex items-center justify-between">
            <span class="text-sm font-semibold text-zinc-800">Price: ₹4.5 Cr</span>
            <button class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded text-xs transition-colors">Book a Tour</button>
        </div>
    </div>';
    
    $mock_css = '.cora-visual-builder-layout { font-family: system-ui, sans-serif; background-color: #fafafa; }';

    wp_send_json_success( array(
        'html'     => $mock_html,
        'css'      => $mock_css,
        'provider' => 'mock_fallback',
        'model'    => 'stub-layout-v1',
        'message'  => 'Using mock layout because no valid API provider keys were found or the request failed.'
    ) );
}
add_action( 'wp_ajax_cora_prompt_to_layout', 'cora_ajax_prompt_to_layout' );
```

---

## 5. Verification Method

To verify the integration independently:

1. **Verify PHP syntax**:
   Ensure all edited and new PHP files do not contain syntax errors.
2. **Verify capability checks**:
   Check user roles and corresponding feature loading by loading the dashboard under a simulated manager role vs subscriber role. Subpages other than `dashboard` should block subscriber accounts.
3. **Inspect AJAX endpoints**:
   You can mock call the new AJAX action `cora_prompt_to_layout` by executing a script mimicking `tests/run_ajax_tests.php` to verify output structure and ensure valid fallback JSON is returned.
