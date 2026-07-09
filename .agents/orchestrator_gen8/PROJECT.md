# Project: Cora Real Estate Platform v0.1 - Advanced Features (gen8)

## Architecture
This project extends the Cora Real Estate WordPress plugin with a Lead Ingestion webhook, shortcode form, 3rd-party listing sync system, and AI SEO meta-data generation, all styled in the mobile-first "Studio Minimalist" aesthetic.

### Data Flow
1. **Frontend Lead Form / Webhook Ingestion**:
   - Shortcode `[cora_lead_form]` submits lead via AJAX to `wp_ajax_cora_re_submit_lead`.
   - Webhook endpoint `wp-json/cora/v1/leads` ingests simulated external JSON payloads and stores them.
   - Manual entry in the Admin Panel submits AJAX payloads to create/update lead records.
   - All leads are stored in WordPress option `cora_re_leads`.
2. **3rd-Party Listing Sync**:
   - Agent inputs a 3rd-party portal link (e.g. Zillow, 99acres, Magicbricks).
   - Sync service handles the URL (simulates data retrieval/parsing) and populates title, description, category, and RERA ID.
   - Inventory items are stored in WordPress option `cora_re_listings_inventory`.
3. **AI SEO Optimizer**:
   - Triggers automatically upon listing save/sync.
   - Generates optimized meta title, meta description, and keywords based on property name, category, and location.
   - Stores SEO meta tags in `cora_re_listings_inventory`.

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|---|---|---|---|
| M6 | Lead_Capture_Pipeline | Custom shortcode `[cora_lead_form]`, webhook REST API endpoint at `wp-json/cora/v1/leads`, and dashboard integration. | None | PLANNED |
| M7 | Listing_Sync | Sync listings via dummy 3rd-party portal link; mock scraper mapping details to inventory. | M6 | PLANNED |
| M8 | AI_SEO_Optimization | Auto-generate/optimize meta titles, descriptions, and tags upon saving a listing. | M7 | PLANNED |
| M9 | Mobile_UI_Polish | Update UI to "Real Estate" taxonomy, ensure right-sliding drawers and monochromatic toasts for all features, test responsive 375px/430px layouts. | M8 | PLANNED |

## Interface Contracts
### 1. Webhook Ingestion (`POST /wp-json/cora/v1/leads`)
- Payload structure:
  ```json
  {
    "name": "Jane Doe",
    "email": "jane@example.com",
    "city": "Mumbai",
    "notes": "Looking for 3BHK",
    "price": "₹2.5 Cr",
    "scale": "buy"
  }
  ```
- Response structure:
  ```json
  {
    "success": true,
    "lead_id": "lead_..."
  }
  ```

### 2. 3rd-Party Portal Sync AJAX Action (`cora_sync_listing_link`)
- Request parameters: `link` (URL), `security` (cora_ajax_nonce)
- Response structure:
  ```json
  {
    "success": true,
    "data": {
      "name": "Extracted Villa Name",
      "category": "Villa",
      "rera_reg_id": "RERA-SYNC-12345",
      "notes": "Synced from 99acres..."
    }
  }
  ```

## Code Layout
- `cora-real-estate.php` - Bootstrapping, AJAX routers, REST API routes, shortcode handlers.
- `admin-dashboard.php` - Sidebar dashboard views, CRM Kanban, listings layout.
- `assets/js/admin-script.js` - JS controllers for drawer forms, AJAX triggers, and toast notifications.
