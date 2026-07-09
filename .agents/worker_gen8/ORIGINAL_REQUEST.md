## 2026-07-08T09:53:58Z
You are a worker agent tasked with implementing advanced real estate features for the Cora Real Estate Platform plugin.
Your working directory is `/Users/shrutian/Desktop/cora/.agents/worker_gen8`.

### Scope of Work:
1. **R1: Comprehensive Lead Capture Pipeline**:
   - Register a WordPress frontend shortcode `[cora_lead_form]` that outputs a clean, minimal, monochromatic form.
   - The frontend form must submit lead data (names, email, city, notes, price, scale) via AJAX to the existing `cora_re_submit_lead` action.
   - Register a WordPress REST API endpoint at `POST /wp-json/cora/v1/leads` (or namespace `cora/v1` route `/leads`). The callback must parse a JSON payload, validate names and email, generate a unique lead ID (`lead_` + time + random string), and append the lead record to the `cora_re_leads` WordPress option.
   - Ensure the admin dashboard manual lead entry form is fully functional and uses monochromatic toasts for success/error handling.
   
2. **R2: 3rd-Party Listing Sync**:
   - Update the Property Listings (previously Equipment) Add Listing form to include a text input field for "3rd-Party Listing Link".
   - Implement an AJAX endpoint `wp_ajax_cora_sync_listing_link` (hooked to `cora_sync_listing_link`). When a user enters a 3rd-party portal link (e.g. Zillow, 99acres, Magicbricks) and clicks "Sync", trigger this endpoint.
   - The endpoint must simulate syncing by checking the URL and extracting mock/simulated property details: Name, Category (Villa, Apartment, Penthouse, Plot, Commercial), RERA ID, and Notes. Return these details as a JSON response.
   - On the frontend, autofill the form inputs with the synced details and show a success/error toast.

3. **R3: AI-Driven SEO Optimization**:
   - When a listing is saved (via manual add or sync), trigger AI/SEO logic on the server to automatically generate a meta SEO Title, meta SEO Description, and keywords/tags based on the property name, category, RERA ID, and link.
   - Save these SEO meta fields (e.g. `seo_title`, `seo_description`, `seo_keywords`) in the listing object inside the `cora_re_listings_inventory` option.
   - In the listing details view/drawer, display these generated SEO fields and allow the user to edit/overwrite them.

4. **R4: Mobile-First "Studio Minimalist" UI**:
   - Refactor the "Equipment" navigation items, section headers, categories, and labels to "Property Listings" and "Listings" to match the real estate domain (categories: Villa, Apartment, Penthouse, Plot, Commercial).
   - Ensure all drawers (Add Lead, Lead Details, Add Listing, Listing Details) slide in from the right, use SVG icons with thin stroke-widths, use `window.coraShowToast` for all feedback, and have 0 instances of native alert/confirm/prompt overlays.
   - Verify layout rendering on viewports of 375px/430px and ensure no horizontal overflow.

5. **Test Coverage & Verification**:
   - Add new test cases in `tests/run_ajax_tests.php` that programmatically invoke the new AJAX actions (listing sync, saving listings with SEO fields, and REST API lead webhook).
   - Run `php -l` on all modified PHP files to ensure no syntax errors.
   - Run the updated `tests/run_ajax_tests.php` script and ensure all tests pass cleanly.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Please write a handoff report listing the modified files, test results, and layout compliance, and place it at `/Users/shrutian/Desktop/cora/.agents/worker_gen8/handoff.md`.
