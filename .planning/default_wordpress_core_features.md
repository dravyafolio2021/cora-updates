# Comprehensive Guide to Default WordPress Core Features (WP Admin & Architecture)
*A complete reference of all built-in features, administrative tools, and technical architectures available out-of-the-box in default WordPress before custom platform development.*

---

## 1. Dashboard (`/wp-admin/index.php`)
The central landing screen providing high-level site metrics, shortcuts, and system status checks.
- **At a Glance Widget**: Displays total counts of published posts, pages, and comments, along with the active theme name and WordPress version.
- **Activity Widget**: Lists upcoming scheduled posts, recently published posts, and the most recent comments with direct inline moderation actions (Approve, Reply, Edit, Spam, Trash).
- **Quick Draft Widget**: A mini-editor allowing users to quickly save post ideas and draft titles with basic notes directly from the dashboard.
- **WordPress Events and News**: Real-time RSS feed of upcoming WordCamps, local WordPress meetups, and core software announcements.
- **Welcome Panel**: An introduction banner with quick links to customize the site, write a blog post, add an About page, and view the frontend.
- **Screen Options & Drag-and-Drop Layout**: Users can hide/show individual dashboard widgets and rearrange them across 1, 2, 3, or 4 columns.

---

## 2. Posts & Blogging Engine (`/wp-admin/edit.php`)
The core content management system for time-stamped articles, news, and structured feeds.
- **All Posts Table**: Central list view with filters for Date (month/year), Category, and SEO/readability status. Supports search and pagination.
- **Bulk Actions & Quick Edit**: Ability to simultaneously publish, draft, trash, categorize, or change author/comments settings for multiple posts at once without opening the full editor.
- **Gutenberg Block Editor / Classic Editor**: Modular block-based editing system supporting headings, paragraphs, galleries, quotes, tables, code blocks, embeds (YouTube, Twitter), and reusable patterns.
- **Post Attributes & Scheduling**:
  - **Status**: Draft, Pending Review, Private, Published, Scheduled for future publication.
  - **Visibility**: Public, Private (visible only to logged-in admins/editors), Password Protected.
  - **Revisions & Auto-Save**: Built-in version control that saves changes periodically, allowing users to inspect character-by-character diffs and restore previous timestamps.
- **Taxonomies (Categorization)**:
  - **Categories (`/wp-admin/edit-tags.php?taxonomy=category`)**: Hierarchical taxonomy supporting parent-child category trees, custom slugs, and descriptions.
  - **Tags (`/wp-admin/edit-tags.php?taxonomy=post_tag`)**: Non-hierarchical keyword labeling for granular content indexing.
- **Post Metadata**: Featured Image (thumbnail), Excerpts (custom summaries), Trackbacks/Pingbacks, and Custom Fields (arbitrary key-value pair metadata attached to posts).

---

## 3. Media Library & File Management (`/wp-admin/upload.php`)
A centralized digital asset management system for images, videos, audio, and documents.
- **Visual Interfaces**: Toggle between a responsive visual **Grid View** and a data-rich **List View**.
- **Multi-File Uploader**: Drag-and-drop asynchronous uploader with progress bars and server file-size limit checks.
- **Supported File Types**:
  - Images: JPG, JPEG, PNG, GIF, WebP, AVIF, ICO (SVG via secure plugins).
  - Audio: MP3, M4A, OGG, WAV.
  - Video: MP4, M4V, WEBM, OGV, WMV, AVI.
  - Documents: PDF, DOC, DOCX, PPT, PPTX, PPS, PPSX, ODT, XLS, XLSX, PSD.
- **Attachment Metadata**: Store and edit Title, Caption, Alt Text (for SEO and accessibility compliance), and Description for every uploaded file.
- **Built-in Image Editor**:
  - Crop (with custom aspect ratio presets like 1:1, 4:3, 16:9).
  - Rotate (clockwise and counter-clockwise) and Flip (horizontal and vertical).
  - Scale image dimensions down while preserving proportions.
  - Restore original image if editing mistakes occur.

---

## 4. Pages (`/wp-admin/edit.php?post_type=page`)
The management interface for static, non-chronological site content (e.g., About Us, Contact, Legal Terms, Landing Pages).
- **Page Hierarchy**: Support for Parent and Child page relationships, creating structured URL paths (e.g., `/services/real-estate-consulting/`).
- **Page Templates**: Ability to assign custom architectural layout files (provided by the active theme or child theme) to specific pages.
- **Page Order**: Numeric index attribute allowing custom sorting of pages in menus and queries.
- **Full Editor Features**: Inherits all Gutenberg block features, revisions, featured images, and visibility controls from the standard post engine.

---

## 5. Comments & Discussion System (`/wp-admin/edit-comments.php`)
A native moderation and community engagement engine that can also be repurposed for internal team notes or activity logs.
- **Status Filtering**: Segregate comments by All, Pending Moderation, Approved, Spam, and Trash.
- **Inline Moderation Actions**: Approve, Unapprove, Reply, Quick Edit (edit text without reloading), Edit (full screen), Mark as Spam, and Move to Trash.
- **Comment Metadata**: Captures Author Name, Email Address, Website URL, IP Address, Timestamp, and the specific parent Post/Page being commented on.
- **Threaded Discussions**: Supports nested replies up to X levels deep, avatar rendering (Gravatar integration), and pagination for high-volume threads.

---

## 6. Appearance & Design (`/wp-admin/themes.php`)
The presentation layer controlling visual layouts, typography, navigation, and component rendering.
- **Themes Management**: Install, activate, live preview, delete, and switch visual themes. Upload `.zip` packages or install directly from the WordPress Theme Directory.
- **Site Editor / Full Site Editing (FSE) (`/wp-admin/site-editor.php`)**: For modern block themes, allows visual drag-and-drop editing of entire website layouts, including Headers, Footers, Sidebars, Single Post templates, Archive templates, and 404 pages.
- **Global Styles**: Centralized interface to configure site-wide typography (font families, weights), color palettes (primary, secondary, background), and spacing tokens.
- **Classic Theme Customizer (`/wp-admin/customize.php`)**: Real-time live preview editor for legacy themes covering Site Identity (Logo, Title, Favicon), Colors, Background Image, Widgets, Homepage Settings, and Additional Custom CSS.
- **Menus (`/wp-admin/nav-menus.php`)**: Visual menu builder allowing users to combine Pages, Posts, Custom Links, Categories, and Tags into nested dropdown hierarchies and assign them to theme display locations (e.g., Primary Nav, Footer Menu).
- **Widgets (`/wp-admin/widgets.php`)**: Block-based management of modular content areas such as sidebars, footer columns, and announcement banners.
- **Theme File Editor (`/wp-admin/theme-editor.php`)**: Built-in syntax-highlighted code editor for directly modifying theme PHP style and layout files (protected by a safety confirmation prompt).

---

## 7. Plugins & Modular Architecture (`/wp-admin/plugins.php`)
The extensibility engine allowing custom business logic without modifying core software.
- **Plugin Management**: Activate, Deactivate, Delete, and view detailed descriptions of installed plugins.
- **Auto-Updates**: Granular toggle switches to enable or disable automatic background updates for individual plugins.
- **Plugin Repository Integration**: Search, filter (by Popular, Recommended, Favorites), and install free plugins directly from the WordPress.org ecosystem of 60,000+ extensions.
- **Plugin File Editor (`/wp-admin/plugin-editor.php`)**: Direct administrative code editor for inspecting and debugging installed plugin PHP/JS/CSS scripts.
- **Must-Use Plugins (`mu-plugins`) & Drop-ins**: Architecture for installing permanent, non-deactivatable server-level plugins and custom database/cache replacement scripts.

---

## 8. Users & Authentication (`/wp-admin/users.php`)
Comprehensive Role-Based Access Control (RBAC) and user identity management.
- **User List & Filtering**: Sort and search users by Username, Name, Email, Role, and Post Count.
- **Default Roles & Capabilities**:
  - **Administrator**: Full access to all administrative features, plugins, themes, and settings.
  - **Editor**: Can publish, edit, and delete any posts/pages, including those written by others, and moderate comments.
  - **Author**: Can publish, edit, and delete only their own posts, and upload media files.
  - **Contributor**: Can write and edit their own posts but cannot publish them or upload media files.
  - **Subscriber**: Can only manage their own profile and read public content.
- **User Creation & Profiles**: Add new users with username, email, name, website, and automatic secure password generation with email notifications.
- **Profile Customization**: Users can toggle the visual editor, enable syntax highlighting in code editors, choose from 9 Admin Color Schemes (Fresh, Light, Modern, Blue, Coffee, Midnight, Ocean, Sunrise, Ectoplasm), enable keyboard shortcuts, and toggle frontend admin toolbar visibility.
- **Security & Session Management**: Generate **Application Passwords** for third-party API/mobile app authentication without exposing primary passwords. Includes a **"Log Out Everywhere Else"** button to terminate stale or compromised browser sessions.

---

## 9. Administrative Tools (`/wp-admin/tools.php`)
Built-in utilities for data migration, diagnostics, and legal compliance.
- **Available Tools**: Access point for built-in category-to-tag conversion scripts and custom tool registrations.
- **Import Engine (`/wp-admin/import.php`)**: Native migration tools to import content from Blogger, LiveJournal, Movable Type, RSS feeds, Tumblr, and standard **WordPress WXR XML files** (importing posts, pages, custom fields, comments, categories, tags, and media attachments).
- **Export Engine (`/wp-admin/export.php`)**: Generates standard WordPress XML export files. Can export the entire website database or be filtered by specific Content Type, Author, Date Range, Category, or Status.
- **Site Health & Diagnostics (`/wp-admin/site-health.php`)**:
  - **Status Tab**: Automated audits checking PHP version compatibility, MySQL/MariaDB database server status, REST API availability, SSL/HTTPS encryption, scheduled cron event firing, disk space availability, and background update functionality.
  - **Info Tab**: A detailed breakdown of server specifications, database tables, filesystem permissions, active theme configuration, and active/inactive plugin lists—with a one-click **"Copy Site Info to Clipboard"** button for technical debugging.
- **GDPR & Privacy Compliance Tools**:
  - **Export Personal Data (`/wp-admin/export-personal-data.php`)**: Search by email address to generate and send an automated, comprehensive zip file containing all personal data stored about a specific user across posts, comments, and user meta.
  - **Erase Personal Data (`/wp-admin/erase-personal-data.php`)**: Search and permanently anonymize or purge all personal data associated with a user upon request to comply with international privacy laws.

---

## 10. System Settings (`/wp-admin/options-general.php`)
The central configuration dashboard controlling global behavior, defaults, and network parameters.
- **General Settings**:
  - Site Title and Tagline (Subtitle).
  - WordPress Address (URL) and Site Address (URL).
  - Administration Email Address (for system notifications and password resets).
  - Membership Toggle (**"Anyone can register"**) and New User Default Role assignment.
  - Site Language (multi-language translation loading), Timezone (UTC offset or city-based), Date Format, Time Format, and Week Starts On setting.
- **Writing Settings (`/wp-admin/options-writing.php`)**:
  - Default Post Category and Default Post Format (Standard, Aside, Gallery, Link, Image, Quote, Status, Video, Audio).
  - Post via Email setup (mail server, port, login name, password, default category for email-published posts).
  - Update Services (XML-RPC ping URLs notified automatically when new content is published).
- **Reading Settings (`/wp-admin/options-reading.php`)**:
  - Homepage Displays: Choose between displaying **"Your latest posts"** (blog feed) or **"A static page"** (selecting distinct static pages for the Homepage and Posts page).
  - Pagination limits: "Blog pages show at most X posts" and "Syndication feeds show the most recent X items".
  - RSS Feed content rendering: Full text vs Excerpt.
  - **Search Engine Visibility**: Checkbox to **"Discourage search engines from indexing this site"** (modifies `robots.txt` and meta robots tags).
- **Discussion Settings (`/wp-admin/options-discussion.php`)**:
  - Default article settings: Attempt to notify blogs linked to from the article, allow link notifications from other blogs (pingbacks/trackbacks), allow people to submit comments on new posts.
  - Comment formatting: Require name and email, require login to comment, automatically close comments on older articles after X days, enable nested threaded comments up to X levels deep, break comments into pages with X top-level comments per page.
  - Email notifications: Send admin emails whenever anyone posts a comment or a comment is held for moderation.
  - **Comment Moderation**: Hold comments in queue if they contain X or more links. Define a list of keywords, names, URLs, emails, or IPs that will trigger automatic moderation queues or automatic sending to Trash/Spam (**Disallowed Comment Keys**).
  - Avatars: Toggle avatar display, select maximum content rating (G, PG, R, X), and choose default avatar styles (Mystery Person, Blank, Gravatar Logo, Identicon, Wavatar, MonsterID, Retro).
- **Media Settings (`/wp-admin/options-media.php`)**:
  - Define explicit pixel dimensions for auto-generated image sizes upon upload:
    - **Thumbnail size** (Width/Height in pixels, with checkbox to crop thumbnail to exact dimensions).
    - **Medium size** (Max width/height constraints).
    - **Large size** (Max width/height constraints).
  - Upload folder organization checkbox: **"Organize my uploads into month- and year-based folders"** (`/wp-content/uploads/2026/07/`).
- **Permalinks Settings (`/wp-admin/options-permalink.php`)**:
  - URL routing rules that generate clean, SEO-friendly URLs without file extensions:
    - Plain (`?p=123`), Day and name (`/2026/07/08/sample-post/`), Month and name (`/2026/07/sample-post/`), Numeric (`/archives/123`), Post name (`/sample-post/`), or Custom Structure using syntax tags (`/%category%/%postname%/`).
  - Optional custom URL prefix prefixes for **Category base** and **Tag base**.
- **Privacy Settings (`/wp-admin/options-privacy.php`)**:
  - Interface to select an existing static page or create a new dedicated **Privacy Policy** page.
  - Interactive **Privacy Policy Guide** providing legal text boilerplate and compliance recommendations from installed plugins.

---

## 11. Under-the-Hood Architectural Capabilities (For Developers)
When building a custom enterprise workspace or SaaS platform on top of WordPress, these internal APIs and database layers are available without requiring external libraries:
1. **WP REST API (`/wp-json/`)**: Out-of-the-box RESTful JSON API providing CRUD endpoints for Posts, Pages, Users, Comments, Media, Categories, Tags, and Settings. Supports custom endpoint registration (`register_rest_route`) with schema validation and authentication guards (`permission_callback`).
2. **Custom Post Types (CPT) & Taxonomy API**: Programmable database entities (`register_post_type`, `register_taxonomy`) allowing developers to create custom data structures (e.g., *Properties, Leads, Site Visits, Contracts*) with native admin routing, UI screens, and REST API support.
3. **Metadata API**: Scalable key-value relational mapping engine (`add_post_meta`, `get_post_meta`, `update_user_meta`) allowing unlimited custom attributes to be linked to any object without altering MySQL table schemas.
4. **WP-Cron (Scheduled Background Jobs)**: Native time-triggered event system (`wp_schedule_event`) for executing asynchronous tasks such as email digests, data synchronization, report generation, and cache clearing.
5. **Hook System (Actions & Filters)**: Event-driven architecture (`add_action`, `add_filter`) allowing non-destructive interception and modification of data payloads, HTML execution flows, and system workflows.
6. **Roles & Capabilities API**: Granular permission checking engine (`current_user_can('edit_others_properties')`, `add_cap`, `add_role`) enabling custom role definitions and strict security boundaries for enterprise hierarchies.
7. **Object Cache & Transients API**: In-memory and database caching abstraction layers (`WP_Object_Cache`, `set_transient`, `get_transient`) to store expensive database query results and API responses for high-performance dashboard rendering.
8. **Database Abstraction Layer (`$wpdb`)**: Secure, Object-Relational Mapping (ORM)-like query builder (`$wpdb->prepare`, `$wpdb->get_results`) supporting prepared statements against SQL injection, custom MySQL table creation, and complex analytical aggregations.
9. **Gutenberg Block API & Shortcodes**: Dynamic frontend and admin rendering systems (`register_block_type`, `add_shortcode`) for building reusable interactive UI components.
10. **WP Filesystem & HTTP API**: Safe abstraction layers for remote API calls (`wp_remote_get`, `wp_remote_post`) and server filesystem interactions without dealing with direct cURL or PHP stream manipulation.
