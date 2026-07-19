# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: tier6-new-refinements.spec.ts >> Tier 6: New Refinements E2E Tests >> 6. Advanced Command Search Modal (Command Palette) Keyboard Validation
- Location: tests/e2e/tier6-new-refinements.spec.ts:220:7

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: locator('#cora-command-palette')
Expected: visible
Timeout: 15000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" with timeout 15000ms
  - waiting for locator('#cora-command-palette')

```

```yaml
- banner:
  - img
  - text: cora Beta
  - img
  - text: Search anything... ⌘ K
  - button "Ask Cora AI":
    - img
    - text: Ask Cora AI
  - button "Notifications":
    - img
  - text: C cora_admin
  - img
- complementary:
  - text: E E2E Testing Office
  - img
  - button "Collapse Sidebar":
    - img
  - img
  - textbox "Search menu..."
  - navigation:
    - text: Workspace
    - list:
      - listitem:
        - img
        - text: Dashboard
      - listitem:
        - img
        - text: Content Suite
      - listitem:
        - img
        - text: Buyer Leads (CRM)
      - listitem:
        - img
        - text: Site Showings 0
      - listitem:
        - img
        - text: Financial Overview
      - listitem:
        - img
        - text: User & Roles
    - text: Property Portfolio
    - list:
      - listitem:
        - img
        - text: Property Listings 0
      - listitem:
        - img
        - text: Secure Vault
    - text: Sales Channel
    - list:
      - listitem:
        - img
        - text: Canvas
    - text: AI Marketing
    - list:
      - listitem:
        - img
        - text: Google Profile
      - listitem:
        - img
        - text: AI Tools MCP
    - text: Settings
    - list:
      - listitem:
        - img
        - text: App Modules
      - listitem:
        - img
        - text: Settings
  - text: co cora_admin Super Admin
  - img
- main:
  - img
  - heading "System Settings Complete Suite" [level=1]
  - paragraph: Global network parameters, reading/writing defaults, discussion moderation rules, and SEO permalinks.
  - button "Save All Settings":
    - img
    - text: Save All Settings
  - link "General Settings Workspace details & identity":
    - /url: "?page=cora-workspace&sub=settings-suite&settings_tab=general"
    - img
    - text: General Settings Workspace details & identity
  - link "Password Policy Enforce security parameters":
    - /url: "?page=cora-workspace&sub=settings-suite&settings_tab=pwd-policy"
    - img
    - text: Password Policy Enforce security parameters
  - link "Branches Brokerage physical offices":
    - /url: "?page=cora-workspace&sub=settings-suite&settings_tab=branches"
    - img
    - text: Branches Brokerage physical offices
  - link "Branding & APIs Favicon, logos, integrations":
    - /url: "?page=cora-workspace&sub=settings-suite&settings_tab=brand"
    - img
    - text: Branding & APIs Favicon, logos, integrations
  - link "Reading & SEO Homepage and search engines":
    - /url: "?page=cora-workspace&sub=settings-suite&settings_tab=reading"
    - img
    - text: Reading & SEO Homepage and search engines
  - link "Writing Category & format variables":
    - /url: "?page=cora-workspace&sub=settings-suite&settings_tab=writing"
    - img
    - text: Writing Category & format variables
  - link "Discussion Moderation & blacklists":
    - /url: "?page=cora-workspace&sub=settings-suite&settings_tab=discussion"
    - img
    - text: Discussion Moderation & blacklists
  - link "Permalinks SEO URL structures":
    - /url: "?page=cora-workspace&sub=settings-suite&settings_tab=permalinks"
    - img
    - text: Permalinks SEO URL structures
  - link "Privacy Compliance terms page":
    - /url: "?page=cora-workspace&sub=settings-suite&settings_tab=privacy"
    - img
    - text: Privacy Compliance terms page
  - link "Git Sync Lovable & GitHub Integrations":
    - /url: "?page=cora-workspace&sub=settings-suite&settings_tab=git-sync"
    - img
    - text: Git Sync Lovable & GitHub Integrations
  - link "Audit & Logs System activity & cost analysis":
    - /url: "?page=cora-workspace&sub=settings-suite&settings_tab=audit"
    - img
    - text: Audit & Logs System activity & cost analysis
  - heading "General Site Configuration" [level=3]
  - paragraph: Core identity and default user registration parameters.
  - text: Site Title
  - textbox
  - text: Tagline / Subtitle
  - textbox: Luxury Properties Delhi
  - text: Administration Email Address
  - textbox: dravya.shs@gmail.com
  - text: New User Default Role
  - combobox:
    - option "Administrator"
    - option "Editor"
    - option "Author"
    - option "Contributor"
    - option "Subscriber" [selected]
    - option "Cora Manager"
    - option "Cora Photographer"
    - option "Cora Videographer"
    - option "Cora Drone Pilot"
    - option "Cora Editor"
    - option "Cora Branch Manager"
    - option "Cora Viewer"
  - 'checkbox "Membership: Anyone can register for an account" [checked]'
  - text: "Membership: Anyone can register for an account"
  - heading "General Workspace Settings" [level=3]
  - paragraph: Corporate identity, localized workspace address, and billing tax descriptors.
  - text: Workspace Name
  - textbox "e.g. Mumbai Main Office": E2E Testing Office
  - text: Tax Registration Details
  - textbox "e.g. VAT / GSTIN / PAN details": GST-E2E-12345
  - text: Workspace Address
  - textbox "Full physical office location": 404 Main St, Mumbai
  - text: Activity Log Auto-Archive Threshold
  - combobox:
    - option "Never (Keep all logs)"
    - option "30 Days"
    - option "90 Days" [selected]
    - option "180 Days"
    - option "1 Year"
  - paragraph: Prune system activity log events older than the selection to optimize database performance.
  - checkbox "Enable Workspace Interactive Tour guides for first-time logins"
  - text: Enable Workspace Interactive Tour guides for first-time logins
  - heading "Database Optimization" [level=3]
  - paragraph: Clean up legacy key-value storage once you have verified custom database tables are fully working.
  - paragraph:
    - text: Purging legacy data removes the redundant data tables from
    - code: wp_options
    - text: .
    - strong: "Note:"
    - text: Make sure you have verified the data migration counts are correct before purging.
  - button "Purge Old wp_options Cache":
    - img
    - text: Purge Old wp_options Cache
  - text: Configure your Cora system environment.
  - button "Save Settings":
    - img
    - text: Save Settings
- complementary:
  - img
  - text: Cora AI Assistant
  - button:
    - img
  - text: Hello! I am Cora, your real estate workspace intelligence. Ask me about bookings, client messages, or writing listing descriptions. Quick Prompts
  - button "Draft a reminder for Ananya"
  - button "Check Rohit & Sneha's deal"
  - textbox "Ask Cora AI..."
  - button "Send"
- complementary:
  - img
  - text: Create New Showing
  - button:
    - img
  - text: Client Full Name
  - textbox "e.g. Ramesh Kumar"
  - text: Deal Type
  - combobox:
    - option "Residential Buy" [selected]
    - option "Luxury Villa Sale"
    - option "Commercial Lease"
    - option "Off-Plan Sale"
    - option "Commercial Campaign"
  - text: Property Location
  - textbox "e.g. Lodhi Gardens, Delhi"
  - text: Viewing Date
  - textbox "e.g. 28th Jun, 2026"
  - text: Package Value
  - textbox "e.g. ₹15,000"
  - button "Create Booking"
  - button "Cancel"
- complementary:
  - img
  - text: Lead Deal Panel
  - button "Create Proposal"
  - button:
    - img
  - button "General Info"
  - button "Assets & Demos"
  - button "Interested Listings"
  - img
  - text: Contact Profile Client / Couple Names
  - textbox "e.g. Aashna & Kabir"
  - text: Contact Email
  - textbox "e.g. client@email.com"
  - img
  - text: Property Scope Event Scale
  - combobox:
    - option "Residential Sale" [selected]
    - option "Luxury Mandate"
    - option "Grand Destination"
    - option "Residential Lease"
  - text: Location / City
  - textbox "e.g. Udaipur"
  - text: Estimated Budget
  - textbox "e.g. ₹5.5L - ₹8L"
  - text: Funnel Status
  - combobox:
    - option "New Lead" [selected]
    - option "Nurturing"
    - option "Closing"
    - option "Converted"
  - img
  - text: Creative Brief Vision Notes / Scope Details
  - textbox "Enter vision details..."
  - button "Save Details"
  - button "Cancel"
  - button "Convert to Client Directory":
    - img
    - text: Convert to Client Directory
  - button "Delete"
- complementary:
  - img
  - text: Listing Details
  - button:
    - img
  - text: 3rd-Party Listing Link
  - textbox "e.g. Zillow, 99acres or Magicbricks link"
  - button "Sync"
  - text: Listing Name *
  - textbox "e.g. DLF Kings Court Penthouse"
  - text: Category *
  - combobox:
    - option "Villa" [selected]
    - option "Apartment"
    - option "Penthouse"
    - option "Plot"
    - option "Commercial"
  - text: RERA Reg ID / Plot Number *
  - textbox "e.g. HR-ERA-2023-88"
  - text: Notes / Description
  - textbox "Property details, notes, etc."
  - text: Property Image No Photo
  - button "Choose Photo"
  - heading "AI-Generated SEO Meta" [level=4]:
    - img
    - text: AI-Generated SEO Meta
  - paragraph: Automatically generated based on listing name, category, RERA ID, and sync link. Feel free to edit/overwrite.
  - text: Meta SEO Title
  - textbox "Auto-generated title"
  - text: Meta SEO Description
  - textbox "Auto-generated description"
  - text: SEO Keywords
  - textbox "Auto-generated keywords"
  - button "Cancel"
  - button "Save Details"
- complementary:
  - text: Client Hub
  - heading "Client Name" [level=3]
  - button "New Invoice"
  - button:
    - img
  - heading "Lead Profiling" [level=4]:
    - img
    - text: Lead Profiling
  - text: Contact - Location - Vision Notes -
  - heading "Viewing Bookings" [level=4]:
    - img
    - text: Viewing Bookings
  - heading "Vault Documents" [level=4]:
    - img
    - text: Vault Documents
  - heading "Assets & Delivery" [level=4]:
    - img
    - text: Assets & Delivery
  - text: No portfolios delivered yet.
  - button "Close Dashboard"
- complementary:
  - img
  - text: Team Crew Assignments
  - button:
    - img
  - paragraph: Select the active agents and listing coordinators for your scheduled showings.
  - text: Rohit & Sneha - Luxury Villa Sale Lead Managing Agent
  - combobox:
    - option "None / Unassigned" [selected]
  - text: Lead Showing Assistant
  - combobox:
    - option "None / Unassigned" [selected]
  - text: Property Valuer
  - combobox:
    - option "None / Unassigned" [selected]
  - text: Ananya Sharma - Residential Buy Lead Managing Agent
  - combobox:
    - option "None / Unassigned" [selected]
  - text: Showing Assistant
  - combobox:
    - option "None / Unassigned" [selected]
  - button "Save Crew Assignments"
  - button "Cancel"
- complementary:
  - img
  - text: Secure Sharing
  - button:
    - img
  - paragraph: Send an encrypted, self-expiring link directly to the client's email via secure mail relay.
  - text: Client Email Address
  - textbox "e.g. client@example.com"
  - text: Link Expiration Date
  - textbox
  - checkbox "Never Expires (Permanent Link)"
  - text: Never Expires (Permanent Link)
  - button "Send & Generate Link"
  - button "Cancel"
- complementary:
  - img
  - text: Create Gallery Folder
  - button:
    - img
  - text: Gallery Title
  - textbox "e.g. Gurgaon Penthouse Showcase"
  - text: Layout Template
  - combobox:
    - option "Grid Layout" [selected]
    - option "Masonry Layout"
    - option "Carousel Slider"
  - text: Access Password (Optional)
  - textbox "Leave empty for public"
  - text: Media Assets (Images & Videos)
  - button "+ Add Asset"
  - button "Save Gallery Folder"
  - button "Cancel"
- complementary:
  - img
  - text: Add Ledger Entry
  - button:
    - img
  - text: Entry Type
  - combobox:
    - option "Cash Inflow (Income)" [selected]
    - option "Cash Outflow (Expense)"
  - text: Date
  - textbox: 2026-07-19
  - text: Description
  - textbox "e.g. 50% Commission for Commercial Lease"
  - text: Amount (₹)
  - textbox "e.g. 15,000"
  - text: Category
  - combobox
  - text: Link Client / Lead (Optional)
  - combobox:
    - option "— Unlinked —" [selected]
  - text: Payment Status
  - combobox:
    - option "Received / Paid" [selected]
    - option "Pending"
  - button "Save Entry"
  - button "Cancel"
- complementary:
  - img
  - heading "Captured Leads" [level=3]
  - paragraph: Attributed CRM submissions from this article
  - button:
    - img
  - table:
    - rowgroup:
      - row "Lead Contact Details / Request Date":
        - columnheader "Lead Contact"
        - columnheader "Details / Request"
        - columnheader "Date"
    - rowgroup:
      - row "Loading captured leads...":
        - cell "Loading captured leads..."
- heading "Share Gallery" [level=3]:
  - img
  - text: Share Gallery
- button:
  - img
- text: Gallery Template
- combobox:
  - option "Grid (Default)" [selected]
  - option "Masonry"
  - option "Carousel"
- text: Items to Share
- checkbox "Images" [checked]
- text: Images
- checkbox "Videos" [checked]
- text: Videos Client Email (Optional)
- textbox "client@example.com"
- paragraph: If provided, an email with the link will be sent directly.
- text: Password Protection (Optional)
- img
- textbox "Leave blank for public access"
- button "Cancel"
- button "Save & Generate Link":
  - img
  - text: Save & Generate Link
- heading "Link Drive URL" [level=3]:
  - img
  - text: Link Drive URL
- button:
  - img
- paragraph: Paste a direct link to a Google Drive file to add it to this portfolio without downloading.
- text: Google Drive URL
- textbox "https://drive.google.com/file/d/..."
- text: Asset Name
- textbox "E.g., highlight-video.mp4"
- text: Asset Type
- combobox:
  - option "Image (Photo)" [selected]
  - option "Video"
- button "Cancel"
- button "Link Asset"
- heading "Sync Drive Folder" [level=3]:
  - img
  - text: Sync Drive Folder
- button:
  - img
- paragraph: Link a public Google Drive folder to automatically sync its contents to this portfolio. Since direct connection requires credentials, this demo simulates syncing by adding 3 professional listing photos and 5 premium video walk-throughs.
- text: Public Drive Folder URL
- textbox "https://drive.google.com/drive/folders/..."
- button "Cancel"
- button "Sync with Drive":
  - img
  - text: Sync with Drive
- heading "Asset Details" [level=3]:
  - img
  - text: Asset Details
- button:
  - img
- text: Asset Name
- textbox
- text: Alt Text (SEO)
- textbox "Describe the image for screen readers"
- text: Description
- textbox "Detailed description for client viewing..."
- text: Asset Actions
- button "Share Asset":
  - img
  - text: Share Asset
- button "Move to Folder":
  - img
  - text: Move to Folder
- button "Set as Cover":
  - img
  - text: Set as Cover
- button "Cancel"
- button "Save Changes":
  - img
  - text: Save Changes
- heading "Advanced Media Upload" [level=3]:
  - img
  - text: Advanced Media Upload
- button:
  - img
- img
- paragraph: Drag and drop files here
- paragraph: or click to browse your computer
- button "Select Files"
- text: Destination Folder
- combobox:
  - option "/ Root (Main Gallery)" [selected]
  - option "Exterior & Façade"
  - option "Interior Rooms"
  - option "Aerial & Drone Shots"
  - option "Floor Plans"
  - option "Amenities & Community"
- text: Organize files into sub-folders immediately. Batch Tags (SEO)
- textbox "e.g. luxury, villa, penthouse"
- text: Comma separated. Applied to all files in this batch. Ready to upload
- button "Cancel"
- button "Upload Files":
  - img
  - text: Upload Files
- complementary:
  - heading "Media Library" [level=3]:
    - img
    - text: Media Library
  - button:
    - img
  - img
  - paragraph: Click or drag to upload
  - paragraph: "Maximum file size: 10MB"
  - text: Vault Images
  - button "Refresh"
```

# Test source

```ts
  125 |     await expect(page.locator('#cora-audit-cost-section')).toBeHidden();
  126 | 
  127 |     // Intercept download for CSV Export
  128 |     const [download] = await Promise.all([
  129 |       page.waitForEvent('download'),
  130 |       page.click('button:has-text("Export CSV")')
  131 |     ]);
  132 | 
  133 |     expect(download.suggestedFilename()).toContain('propOS_audit_logs');
  134 |   });
  135 | 
  136 |   test('5. Model Context Protocol (MCP) Server Validation', async ({ page }) => {
  137 |     // 1. Retrieve the secure token from workspace mcp tab
  138 |     await page.goto('/workspace/mcp');
  139 |     await page.waitForSelector('input[name="cora_mcp_access_token_direct"]');
  140 |     const validToken = await page.inputValue('input[name="cora_mcp_access_token_direct"]');
  141 |     expect(validToken.length).toBeGreaterThan(0);
  142 | 
  143 |     // 2. Perform request with invalid token -> 401 Unauthorized
  144 |     const resInvalid = await page.request.post('/wp-json/cora/v1/mcp', {
  145 |       headers: {
  146 |         'Authorization': 'Bearer invalid_token_123',
  147 |         'Content-Type': 'application/json'
  148 |       },
  149 |       data: {
  150 |         jsonrpc: '2.0',
  151 |         method: 'tools/list',
  152 |         id: 1
  153 |       }
  154 |     });
  155 |     expect(resInvalid.status()).toBe(401);
  156 |     const bodyInvalid = await resInvalid.json();
  157 |     expect(bodyInvalid.error.message).toContain('Unauthorized');
  158 | 
  159 |     // 3. Perform request with valid token -> tools/list list of tools
  160 |     const resList = await page.request.post('/wp-json/cora/v1/mcp', {
  161 |       headers: {
  162 |         'Authorization': `Bearer ${validToken}`,
  163 |         'Content-Type': 'application/json'
  164 |       },
  165 |       data: {
  166 |         jsonrpc: '2.0',
  167 |         method: 'tools/list',
  168 |         id: 2
  169 |       }
  170 |     });
  171 |     expect(resList.status()).toBe(200);
  172 |     const bodyList = await resList.json();
  173 |     expect(bodyList.result.tools).toBeDefined();
  174 |     const tools = bodyList.result.tools;
  175 |     expect(tools.some(t => t.name === 'cora_get_platform_info')).toBe(true);
  176 |     expect(tools.some(t => t.name === 'cora_get_leads')).toBe(true);
  177 | 
  178 |     // 4. Perform tool call -> cora_get_platform_info
  179 |     const resCallInfo = await page.request.post('/wp-json/cora/v1/mcp', {
  180 |       headers: {
  181 |         'Authorization': `Bearer ${validToken}`,
  182 |         'Content-Type': 'application/json'
  183 |       },
  184 |       data: {
  185 |         jsonrpc: '2.0',
  186 |         method: 'tools/call',
  187 |         params: {
  188 |           name: 'cora_get_platform_info'
  189 |         },
  190 |         id: 3
  191 |       }
  192 |     });
  193 |     expect(resCallInfo.status()).toBe(200);
  194 |     const bodyCallInfo = await resCallInfo.json();
  195 |     expect(bodyCallInfo.result.isError).toBe(false);
  196 |     expect(bodyCallInfo.result.content[0].text).toContain('Cora Platform Info');
  197 | 
  198 |     // 5. Perform tool call -> cora_get_leads
  199 |     const resCallLeads = await page.request.post('/wp-json/cora/v1/mcp', {
  200 |       headers: {
  201 |         'Authorization': `Bearer ${validToken}`,
  202 |         'Content-Type': 'application/json'
  203 |       },
  204 |       data: {
  205 |         jsonrpc: '2.0',
  206 |         method: 'tools/call',
  207 |         params: {
  208 |           name: 'cora_get_leads',
  209 |           arguments: { limit: 2 }
  210 |         },
  211 |         id: 4
  212 |       }
  213 |     });
  214 |     expect(resCallLeads.status()).toBe(200);
  215 |     const bodyCallLeads = await resCallLeads.json();
  216 |     expect(bodyCallLeads.result.isError).toBe(false);
  217 |     expect(bodyCallLeads.result.content[0].text).toContain('Recent CRM Leads');
  218 |   });
  219 | 
  220 |   test('6. Advanced Command Search Modal (Command Palette) Keyboard Validation', async ({ page }) => {
  221 |     await page.goto('/workspace/settings-suite');
  222 | 
  223 |     // 1. Click sidebar search bar -> opens command palette modal
  224 |     await page.click('.cora-sidebar-search');
> 225 |     await expect(page.locator('#cora-command-palette')).toBeVisible();
      |                                                         ^ Error: expect(locator).toBeVisible() failed
  226 | 
  227 |     // 2. Close it via Escape key
  228 |     await page.keyboard.press('Escape');
  229 |     await expect(page.locator('#cora-command-palette')).toBeHidden();
  230 | 
  231 |     // 3. Open it via Ctrl+K shortcut key
  232 |     await page.keyboard.press('Control+k');
  233 |     await expect(page.locator('#cora-command-palette')).toBeVisible();
  234 | 
  235 |     // 4. Verify search input has focus and type "Password"
  236 |     await page.fill('#cora-command-input', 'Password');
  237 |     await page.waitForTimeout(300); // Wait for debounce and REST fetch
  238 | 
  239 |     // 5. Verify results contain Password Policy item
  240 |     await expect(page.locator('#cora-command-results')).toContainText('Password Policy');
  241 | 
  242 |     // 6. Test arrow keys down navigation
  243 |     await page.keyboard.press('ArrowDown');
  244 |     const selectedItem = page.locator('.cora-command-item.selected');
  245 |     await expect(selectedItem).toBeVisible();
  246 | 
  247 |     // 7. Test filter pills click (which searches "Password" under Leads filter, yielding no results)
  248 |     await page.click('#cora-command-palette .cora-search-pill[data-filter="leads"]');
  249 |     await page.waitForTimeout(300);
  250 |     await expect(page.locator('#cora-command-results')).toContainText('No results found');
  251 | 
  252 |     // 8. Press Escape to close palette modal
  253 |     await page.keyboard.press('Escape');
  254 |     await expect(page.locator('#cora-command-palette')).toBeHidden();
  255 |   });
  256 | 
  257 | });
  258 | 
```