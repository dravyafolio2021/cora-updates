# Cora Real Estate — Comprehensive Features & Capabilities Report

This report outlines the complete suite of features, modules, API integrations, and database schemas built within the **Cora Real Estate** (Realtors Workspace) WordPress plugin.

---

## 1. System Shell & Core UI Layout
*Notion-style monochromatic layout for professional real estate operations.*

- **Sidebar Navigation**:
  - Dark, professional theme (`#111827` background) with collapsible mobile drawer toggle and highlight indicator for active pages.
  - Sidebar header rendering the user's current Agency name and assigned Branch Office location.
- **Header Topbar Controls**:
  - Time-based dynamic user greetings ("Good morning/afternoon/evening, Dravya") and local branch date label.
  - Active preview-role dropdown select allowing broker-owners to preview permissions layouts.
  - Warning banner alerting the user when a simulated preview role is active, with a quick-action link to restore original permissions.
  - In-app notification bell triggering a slide-down popover displaying recent user alerts, marked read on click or globally via a "Mark all as read" button.
- **Admin Settings Widget**:
  - Sticky widget at the bottom of the sidebar.
  - Popover toggle displaying active database connection quality indicator, AI model select selector, and storage/API quota meter bars.

---

## 2. CRM & Leads Lifecycle Management
*Fully-featured client lifecycle pipeline to monitor property search progress.*

- **Dynamic CRM Pipeline Bar**:
  - A proportional visual progress tracker mapping leads across five CRM stages: `New` ➔ `Contacted` ➔ `Site Visit` ➔ `Negotiation` ➔ `Closed`.
  - Proportional segment widths representing the percentage count of records at each stage.
- **Lead Management Drawer**:
  - A right-sliding drawer layout for adding/editing leads without refreshing.
  - Dedicated tabs: **General Info** (name, budget, location), **Assets & Demos** (linked portfolios), and **Interested Listings**.
- **Follow-up Date Scheduling**:
  - Integration of `followup_date` datetime selectors inside lead drawers.
  - **Today's Follow-ups** widget displaying scheduled follow-ups, with overdue rows highlighted in red and showing elapsed days.
- **Client Conversion**:
  - A single-click trigger converting verified leads directly to the permanent client directory database table.

---

## 3. Booking Schedule & Crew Allocation
*Showing coordinator tool mapping client tours to properties and field crews.*

- **Showings Schedule Logger**:
  - Form to record showing appointments containing client name, location, viewing date, package value, and deal type.
  - Status management badges mapping tours as `Confirmed`, `Editing`, or `Completed`.
- **Field Crew Assignments Panel**:
  - Dynamic crew assignments modal enabling coordinators to assign specific field personnel to a showing:
    - **Managing Agent** (Photographer)
    - **Showing Assistant** (Videographer)
    - **Property Valuer** (Drone Pilot)

---

## 4. Client Portfolios & Google Drive Sync
*Client-facing asset delivery portals with security and folder integrations.*

- **Media Gallery Folders**:
  - Custom galleries for active properties supporting **Grid**, **Masonry**, and **Carousel** layout selectors.
- **Google Drive Integration**:
  - Direct linking of external Google Drive asset file URLs (images, videos).
  - Public folder syncer simulating direct drive imports of property walk-through files.
- **Expiring Share Link Generator**:
  - Encrypted secure link exporter with date-expiration selectors and a "Never Expires" option.
- **Password Protection Lock**:
  - Client password verification page. Locked galleries prompt for a passcode before unlocking content.

---

## 5. Double-Entry Financial Ledger
*Transactional log tracing agency income and expenses.*

- **Inflow/Outflow Registry**:
  - Log sheet for adding transactions labeled as Cash Inflow (Income) or Cash Outflow (Expense).
- **Client & Lead Mappings**:
  - Dropdown lists linking ledger transactions directly to active CRM leads or clients for unified records.
- **Verification Status**:
  - Monochromatic status markers designating records as `Received/Paid` or `Pending`.

---

## 6. Advanced Media Editor & SEO Suite
*In-app asset optimization tools preparing property photos for listing.*

- **Visual Crop & Transform Workspace**:
  - In-browser workspace loaded with slider inputs adjusting rotation, scaling, focal point parameters, and crop dimensions.
- **Attachment Metadata SEO Injector**:
  - Dedicated fields capturing SEO Meta Title, Meta Description, and SEO Keywords.
  - Automated AI metadata helper populating values based on property titles, RERA registration numbers, and category attributes.

---

## 7. Workspace Settings & Branches Suite
*Core administrative settings defining agency boundaries and workspace rules.*

- **General Workspace Identity**:
  - Workspace Name, Tax Registration IDs, and official business address settings.
- **Branch Management**:
  - Dynamic interface to add, list, and delete physical agency branches (Chennai Hub, Gurgaon Center, etc.).
- **Security & Password Policy**:
  - Strict policy check boxes requiring minimum password lengths, uppercase characters, numeric values, and special characters.

---

## 8. GDPR Privacy Compliance
*Security guidelines and personal data audit tools.*

- **GDPR Privacy Panel**:
  - Official privacy guidelines reviewer.
- **PII Erasure and Export Tools**:
  - Search tool allowing agents to locate all personal records matching an email.
- **Selective Data Erasure**:
  - Dedicated AJAX buttons to export a client's data or trigger a complete erase workflow purging their logs from leads, bookings, and ledger tables.

---

## 9. System Activity Audit Logs
*Security trail recording all administrator actions.*

- **Centralized Event Logging**:
  - Log entries recording details of every action (creation, updates, deletions).
- **Cora AI Badge**:
  - Distinct violet dots and `✦ Cora` badges designating automated actions performed by the AI agent.
- **CSV Data Exporter**:
  - Standard button exporting the database logs table to a downloaded CSV file.

---

## 10. Visual Page Builder
*Visual canvas module designing custom landing pages.*

- **Drag-and-Drop Elements**:
  - Components library (Heading, Text Block, Button, Image).
- **Element Inspector Column**:
  - Properties editor updating values on the active widget.
- **Publish & Save Draft**:
  - In-app action buttons publishing layout configurations directly.

---

## 11. Database Options Schema & REST APIs

### Key Database Options (`wp_options` table):
- `cora_re_leads`: Array of active leads and client lifecycle data.
- `cora_re_listings_inventory`: Real estate property listings database.
- `cora_re_clients`: Permanent client directory.
- `cora_re_client_bookings`: Scheduled tours and crew allocation mappings.
- `cora_re_ledger`: Double-entry transaction logs.
- `cora_branches`: List of registered physical offices.
- `cora_notifications`: User system alerts.
- `cora_activity_logs`: Audit trail logs.

### Scoped REST API Endpoints (`/api/v1/`):
- `GET /api/v1/dashboard/summary`: Scoped stats card values.
- `GET /api/v1/dashboard/pipeline`: Proportional pipeline stage statistics.
- `GET /api/v1/dashboard/follow-ups`: Chronological checklist items (overdue first).
- `GET /api/v1/dashboard/activity`: Audit logs with agent attributes.
- `POST /api/v1/users/invite`: Secured crew onboarding link.
- `GET /api/v1/users`: List of agency crew members.
