# Handoff Report — Codebase & Environment Analysis

## 1. Observation
Below are the exact commands executed, configurations inspected, and outputs captured during the environment and codebase audit.

### A. Running WordPress Services & Port Mappings
1. **Nginx router and site instance identification:**
   Checking processes on the machine via `ps aux` showed Nginx and PHP-FPM running under Local:
   - Master Nginx router: `/Users/shrutian/Library/Application Support/Local/lightning-services/nginx-1.26.1+3/bin/darwin-arm64/sbin/nginx -c /Users/shrutian/Library/Application Support/Local/run/router/nginx/conf/nginx.conf`
   - Site Nginx instance: `/Users/shrutian/Library/Application Support/Local/lightning-services/nginx-1.26.1+3/bin/darwin-arm64/sbin/nginx -g daemon off; -c /Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/conf/nginx/nginx.conf`
   - Active database process: `/Users/shrutian/Library/Application Support/Local/lightning-services/mysql-8.4.0/bin/darwin-arm64/bin/mysqld`
   - Active PHP-FPM socket master process: `/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/conf/php/php-fpm.conf`

2. **Listening Ports:**
   Running `lsof -i -P -n | grep LISTEN` identified:
   - `nginx` (router) listening on ports `80` (HTTP) and `443` (HTTPS)
   - `nginx` (site instance) listening on `127.0.0.1:10003` (and `[::1]:10003`)
   - `mysqld` listening on `127.0.0.1:10004` (and `[::1]:10004`)
   - `mailpit` listening on `10000` and `10001`
   - `Local` control app listening on `127.0.0.1:4000`

3. **Routing Configuration:**
   Inspecting `/Users/shrutian/Library/Application Support/Local/run/router/nginx/conf/route.cora.local.conf` revealed:
   ```nginx
   server {
       server_name cora.local *.cora.local;
       ...
       location / {
           proxy_pass http://127.0.0.1:10003;
           include location-block.conf;
       }
   }
   ```
   Curling `cora.local` verified it handles requests for the WordPress instance:
   ```bash
   curl -I -H "Host: cora.local" http://127.0.0.1/
   # HTTP/1.1 200 OK
   # Link: <http://cora.local/wp-json/>; rel="https://api.w.org/"
   ```

### B. Utility and Testing Tools
Running check commands on the host system returned the following:
- **Node.js**: `v22.23.0`
- **NPM**: `10.9.8`
- **Python**: `Python 3.9.6`
- **Playwright**: `Version 1.61.1` (installed)
- **Cypress**: Dynamically downloaded via `npx cypress --version` (ver. `15.18.1` / Bundled Node `22.19.0`)
- **PHPUnit**: Not installed on host. PHP environment runs PHP `8.2.29` inside Local.
- **Composer**: Pre-configured in `/Applications/Local.app/Contents/Resources/extraResources/bin/composer/posix`

Sourcing environment paths from `/Users/shrutian/Desktop/cora/app/.envrc` enables database connection inside CLI commands:
```bash
MYSQL_HOME="/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/conf/mysql" \
PHPRC="/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/conf/php" \
WP_CLI_CONFIG_PATH="/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/config.yaml" \
WP_CLI_DISABLE_AUTO_CHECK_UPDATE=1 \
PATH="/Users/shrutian/Library/Application Support/Local/lightning-services/mysql-8.4.0/bin/darwin-arm64/bin:/Users/shrutian/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin:/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/posix:/Applications/Local.app/Contents/Resources/extraResources/bin/composer/posix:$PATH" \
wp plugin list
```
Output:
```
name	status	update	version	update_version	auto_update
cora-real-estate	active	none	1.0.0		off
cora-studio-ai-locked	inactive	none	1.0.0		off
```

### C. 6 Core Replacement Modules & JS Stubs Review
An audit of `views/view-*.php` files and comparison with definitions in `assets/js/admin-script.js` revealed:

| View Module | HTML / PHP Event Click Targets | JS Function Status (`admin-script.js`) | Assessment / Stubs / Errors |
| :--- | :--- | :--- | :--- |
| **view-pages.php** | `coraOpenPageDrawer(page_id)`<br>`coraDeletePage(page_id)`<br>`coraClosePageDrawer()`<br>`coraSubmitPage()` | `window.coraOpenPageDrawer` (L6849)<br>`window.coraDeletePage` (L6962)<br>`window.coraClosePageDrawer` (L6906)<br>`window.coraSubmitPage` (L6911) | **Fully Matches**. Integrates Quill Rich Text Editor; performs actual AJAX posts to backend endpoints. |
| **view-settings-suite.php** | `coraSaveSystemSettingsSuite()` (on form submit and save button) | `window.coraSaveSystemSettingsSuite` (L7089) | **Fully Matches**. Performs form serialization and updates active configuration tabs via AJAX post. |
| **view-tools.php** | `coraCopySiteDiagnostics()` (L33)<br>`coraRunXMLExport()` (L130)<br>`coraShowSelectedImportFile(this)` (L154)<br>`coraRunXMLImport()` (L162)<br>`coraRunGDPRExport()` (L185)<br>`coraRunGDPRErase()` (L193) | `window.coraCopySiteDiagnostics` (L6984)<br>`window.coraRunXMLExport` (L6997)<br>`window.coraRunXMLImport` (L7016)<br>`window.coraRunGDPRExport` (L7020)<br>`window.coraRunGDPRErase` (L7039) | **Broken/Missing element**: `coraShowSelectedImportFile` is referenced in HTML (`onchange`) but completely missing in JS. `coraRunXMLImport` is a stub that only triggers a toast. |
| **view-appearance.php** | `coraSaveAppearanceSettings()` (L35)<br>`coraOpenMediaSelector(field)` (L78, L85)<br>`coraOpenNewMenuDrawer()` (L110)<br>`coraRemoveMenuItem(item_id)` (L138)<br>`coraOpenAddMenuItemDrawer()` (L149)<br>`coraCloseAddMenuItemDrawer()` (L166, 202)<br>`coraToggleMenuItemTypeFields(val)` (L174)<br>`coraSubmitMenuItem()` (L203) | `window.coraActivateTheme` (L6631)<br>`window.coraSaveMenuStructure` (L6635)<br>`window.coraSaveWidgetSettings` (L6639)<br>`window.coraSaveCustomCSS` (L6643) | **Extensive Mismatches**: None of the handlers in `view-appearance.php` are defined in `admin-script.js`. For example, PHP expects `coraSaveAppearanceSettings()` but JS only has `coraSaveMenuStructure()`. Drawers for menus/items have no JS logic. |
| **view-comments.php** | `coraRefreshComments()` (L41)<br>`coraModerateComment(id, act)` (L125)<br>`coraOpenCommentReplyDrawer(id, auth, exc)` (L136)<br>`coraDeleteCommentPermanent(id)` (L158)<br>`coraCloseCommentReplyDrawer()` (L178)<br>`coraSubmitCommentReply()` (L205) | `window.coraOpenCommentReplyModal` (L6601)<br>`window.coraSubmitCommentReply` (L6608)<br>`window.coraUpdateCommentStatus` (L6618)<br>`window.coraFilterComments` (L6622) | **Extensive Mismatches**: PHP views call `coraOpenCommentReplyDrawer` and `coraCloseCommentReplyDrawer` but JS defines `coraOpenCommentReplyModal` and lacks a close function. PHP calls `coraModerateComment()` but JS defines `coraUpdateCommentStatus()`. `coraRefreshComments` and `coraDeleteCommentPermanent` are completely missing from JS. |
| **view-media-editor.php**| `coraOpenMediaUploader()` (L37)<br>`coraLoadMediaIntoEditor(val)` (L52)<br>`coraResetEditorCanvas()` (L63)<br>`coraSetCropRatio(x, y)` (L71)<br>`coraRotateImage(deg)` (L79)<br>`coraFlipImage(dir)` (L85)<br>`coraSaveEditedImage()` (L112)<br>`coraSaveMediaMetadata()` (L156) | `window.coraOpenMediaEditorModal` (L6685)<br>`window.coraApplyMediaTransform` (L6694)<br>`window.coraSaveMediaEdits` (L6713)<br>`window.coraSaveMediaMetadata` (L7058)<br>`window.coraSaveEditedImage` (L7085) | **Extensive Mismatches**: View uses canvas interactions and toolbar actions like `coraRotateImage` and `coraFlipImage`, but JS defines `coraApplyMediaTransform` and `coraSaveMediaEdits`. Dropdown loading functions like `coraLoadMediaIntoEditor` are missing in JS. |

### D. Overlay Drawer Styles & Native Dialogues
1. **Drawer Styles:** All sliding sheets utilize absolute/fixed placement on the right with a full off-screen translation by default (`translate-x-full`). They are toggled via JS by removing `translate-x-full` (e.g. `L6903: $('#cora-drawer-page').removeClass('translate-x-full');`).
2. **Native Dialogue Audit:** Browser native `alert()`, `confirm()`, and `prompt()` calls do not exist in the source code. Custom alerts use `window.coraShowToast()`, and folder creation prompt uses a custom DOM overlay modal `$('#cora-prompt-modal')` (L2793).

---

## 2. Logic Chain
1. The listening ports check shows that router Nginx binds to port 80/443 and maps the server name `cora.local` to the backend site instance listening on `127.0.0.1:10003`.
2. Sourcing `/Users/shrutian/Desktop/cora/app/.envrc` configures the active environment variables needed by the PHP CLI interpreter to resolve the SQLite/MySQL socket connection configuration without throwing database connection failure errors.
3. Therefore, WP-CLI commands must run with this prepended path structure to allow test scripts or diagnostic tools to read/write properties on the live server.
4. Comparing click/onchange event attributes in PHP template scripts against global window functions in `assets/js/admin-script.js` confirms that:
   - Core page builder and general system settings suite views match their respective JS targets and connect correctly.
   - Core diagnostics features match but have incomplete/stub implementations.
   - Modules for Appearance & Navigation (Module 3), Client Discussions/Comment Moderation (Module 2), and Media Editor (Module 5) have massive callback function name mismatches and/or missing JavaScript functions, making them currently interactive stubs or entirely broken elements.

---

## 3. Caveats
- Evaluated system packages from the zsh host environment. Container-based tools were not examined, as the environment is hosted locally using Flywheel lightning-services.
- No source modifications were made. The verification commands check read-only states or execute stubs.

---

## 4. Conclusion
1. The local environment is fully configured with WordPress active at `http://cora.local` (port 10003/80).
2. Command-line interaction is fully supported by prepending the Local environment path structure to `wp` and `php` commands.
3. Playwright and Node/Python are available on the host, making the setup of Milestone M1 (E2E Test Infrastructure) completely feasible.
4. Implementing Milestone M2 (UI Polish & Views functionality) will require rewriting the mismatched callback functions in `admin-script.js` or matching PHP files to synchronize their naming conventions.

---

## 5. Verification Method
Verify server connectivity, hostname resolution, and WP-CLI options execution by running the following commands from the root directory `/Users/shrutian/Desktop/cora`:

1. **Verify Host Routing & Nginx HTTP Response:**
   ```bash
   curl -I -H "Host: cora.local" http://127.0.0.1/
   ```
   *Expected: HTTP/1.1 200 OK*

2. **Verify Database connection via WP-CLI:**
   ```bash
   MYSQL_HOME="/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/conf/mysql" \
   PHPRC="/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/conf/php" \
   WP_CLI_CONFIG_PATH="/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/config.yaml" \
   WP_CLI_DISABLE_AUTO_CHECK_UPDATE=1 \
   PATH="/Users/shrutian/Library/Application Support/Local/lightning-services/mysql-8.4.0/bin/darwin-arm64/bin:/Users/shrutian/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin:/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/posix:/Applications/Local.app/Contents/Resources/extraResources/bin/composer/posix:$PATH" \
   wp option get siteurl
   ```
   *Expected: http://cora.local*
