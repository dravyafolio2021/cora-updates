<?php
/**
 * Cora Real Estate CRM - Module 6: System Settings Complete Suite
 * Studio-Grade Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$active_tab = isset( $_GET['settings_tab'] ) ? sanitize_text_field( $_GET['settings_tab'] ) : 'general';
$pages      = get_pages();
$categories = get_categories();
$roles      = wp_roles()->get_names();
?>

<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">System Settings Complete Suite</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Global network parameters, reading/writing defaults, discussion moderation rules, and SEO permalinks.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button class="cora-btn-primary px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-md text-xs transition-colors cursor-pointer flex items-center gap-2 shadow-sm" onclick="coraSaveSystemSettingsSuite()">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save All Settings
        </button>
    </div>
</div>

<!-- Settings Tab Navigation -->
<div class="flex items-center gap-2 border-b border-zinc-200/80 pb-3 overflow-x-auto">
    <?php
    $tabs = array(
        'general'   => 'General Settings',
        'brand'     => 'Branding & API Keys',
        'reading'   => 'Reading & SEO Indexing',
        'writing'   => 'Writing Defaults',
        'discussion'=> 'Discussion & Moderation',
        'permalinks'=> 'SEO Permalinks',
        'privacy'   => 'Privacy Policy',
    );
    foreach ( $tabs as $tab_key => $tab_label ) :
        $is_active = ( $active_tab === $tab_key );
    ?>
    <a href="?page=cora-workspace&sub=settings-suite&settings_tab=<?php echo esc_attr( $tab_key ); ?>" class="px-3.5 py-2 rounded-lg text-xs font-semibold transition-all <?php echo $is_active ? 'bg-zinc-950 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900'; ?>">
        <?php echo esc_html( $tab_label ); ?>
    </a>
    <?php endforeach; ?>
</div>

<div class="bg-white border border-zinc-200/80 rounded-xl p-6 shadow-sm">
    <form id="cora-settings-suite-form" onsubmit="event.preventDefault(); coraSaveSystemSettingsSuite();">
        <input type="hidden" name="active_tab" value="<?php echo esc_attr( $active_tab ); ?>">

        <?php if ( $active_tab === 'general' ) : ?>
        <!-- TAB 1: GENERAL SETTINGS -->
        <div class="space-y-6 max-w-2xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">General Site Configuration</h3>
                <p class="text-xs text-zinc-500">Core identity and default user registration parameters.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Site Title</label>
                    <input type="text" name="blogname" value="<?php echo esc_attr( get_option('blogname') ); ?>" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Tagline / Subtitle</label>
                    <input type="text" name="blogdescription" value="<?php echo esc_attr( get_option('blogdescription') ); ?>" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Administration Email Address</label>
                    <input type="email" name="admin_email" value="<?php echo esc_attr( get_option('admin_email') ); ?>" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">New User Default Role</label>
                    <select name="default_role" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                        <?php foreach ( $roles as $role_key => $role_name ) : ?>
                            <option value="<?php echo esc_attr( $role_key ); ?>" <?php selected( get_option('default_role'), $role_key ); ?>><?php echo esc_html( $role_name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="pt-2">
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                    <input type="checkbox" name="users_can_register" value="1" <?php checked( get_option('users_can_register'), 1 ); ?> class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                    <span>Membership: Anyone can register for an account</span>
                </label>
            </div>
        </div>

        <?php elseif ( $active_tab === 'brand' ) : ?>
        <!-- TAB 7: BRANDING & API KEYS -->
        <div class="space-y-6 max-w-2xl animate-in fade-in duration-200">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">Brand Identity & API Integrations</h3>
                <p class="text-xs text-zinc-500">Configure your agency's logo, custom favicon, currency layout, and third-party developer API credentials.</p>
            </div>
            
            <div class="space-y-5">
                <!-- Agency Logo Settings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-zinc-200 rounded-xl bg-zinc-50/50">
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Agency Logo URL</label>
                            <div class="flex gap-2">
                                <input type="url" id="cora-brand-logo-url-suite" name="cora_brand_logo_url" value="<?php echo esc_url( get_option('cora_brand_logo_url', '') ); ?>" placeholder="https://..." class="flex-1 bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                                <button type="button" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraOpenMediaSelector('cora-brand-logo-url-suite')">Browse</button>
                            </div>
                        </div>
                        <p class="text-[11px] text-zinc-400">Upload your real estate group's official logo. This will be used on all shared portfolios, custom client portals, and invoice headers.</p>
                    </div>
                    <div class="flex items-center justify-center border border-zinc-200 rounded-lg bg-white p-3 h-28">
                        <?php $logo_url = get_option('cora_brand_logo_url', ''); ?>
                        <div id="cora-suite-logo-preview" class="w-full h-full flex items-center justify-center overflow-hidden">
                            <?php if ( ! empty( $logo_url ) ) : ?>
                                <img src="<?php echo esc_url( $logo_url ); ?>" class="max-h-full max-w-full object-contain" alt="Logo Preview">
                            <?php else : ?>
                                <span class="text-[10px] text-zinc-400 uppercase font-semibold">No Logo Set</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Custom Favicon Settings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-zinc-200 rounded-xl bg-zinc-50/50">
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Custom Favicon URL (32x32 / 64x64 PNG)</label>
                            <div class="flex gap-2">
                                <input type="url" id="cora-brand-favicon-url-suite" name="cora_brand_favicon_url" value="<?php echo esc_url( get_option('cora_brand_favicon_url', '') ); ?>" placeholder="https://..." class="flex-1 bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                                <button type="button" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraOpenMediaSelector('cora-brand-favicon-url-suite')">Browse</button>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="px-2.5 py-1.5 border border-zinc-200 hover:border-zinc-450 bg-white text-zinc-700 font-semibold text-[10px] rounded transition-colors cursor-pointer" onclick="coraSetDefaultPremiumFavicon()">
                                Set to Premium Monogram Icon
                            </button>
                            <button type="button" class="px-2.5 py-1.5 border border-zinc-200 hover:border-zinc-450 bg-white text-zinc-700 font-semibold text-[10px] rounded transition-colors cursor-pointer" onclick="document.getElementById('cora-brand-favicon-url-suite').value='';">
                                Clear Favicon
                            </button>
                        </div>
                        <script>
                            function coraSetDefaultPremiumFavicon() {
                                const url = "<?php echo esc_url( CORA_REAL_ESTATE_AI_URL . 'assets/images/cora-favicon.png' ); ?>";
                                document.getElementById('cora-brand-favicon-url-suite').value = url;
                                const img = document.querySelector('#cora-suite-favicon-preview img');
                                if (img) {
                                    img.src = url;
                                } else {
                                    document.getElementById('cora-suite-favicon-preview').innerHTML = `<img src="${url}" class="w-10 h-10 object-contain" alt="Favicon Preview">`;
                                }
                                window.coraShowToast("Premium Monogram Icon selected as Favicon.");
                            }
                        </script>
                        <p class="text-[11px] text-zinc-400">Configure your website browser tab favicon. You can use your own or select the unique custom-designed Cora Real Estate monogram favicon.</p>
                    </div>
                    <div class="flex flex-col items-center justify-center border border-zinc-200 rounded-lg bg-white p-3 h-28 space-y-1.5">
                        <span class="text-[9px] text-zinc-400 uppercase font-bold tracking-wider">Tab Favicon</span>
                        <?php 
                        $favicon_url = get_option('cora_brand_favicon_url', ''); 
                        if ( empty( $favicon_url ) ) {
                            $favicon_url = CORA_REAL_ESTATE_AI_URL . 'assets/images/cora-favicon.png';
                        }
                        ?>
                        <div id="cora-suite-favicon-preview" class="w-12 h-12 flex items-center justify-center border border-zinc-100 rounded-md bg-zinc-50">
                            <img src="<?php echo esc_url( $favicon_url ); ?>" class="w-8 h-8 object-contain" alt="Favicon Preview">
                        </div>
                    </div>
                </div>

                <!-- Google Maps API and WhatsApp integration -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">Google Maps API Key</label>
                        <input type="text" name="cora_gbp_maps_api_key" value="<?php echo esc_attr( get_option('cora_gbp_maps_api_key', '') ); ?>" placeholder="AIzaSy..." class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                        <p class="text-[10px] text-zinc-400 mt-1">Required for geolocating listing properties and rendering location matrix details on property maps.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">System Currency Layout</label>
                        <select name="cora_currency_format" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                            <?php $curr_format = get_option('cora_currency_format', 'INR_LAKHS'); ?>
                            <option value="INR_LAKHS" <?php selected( $curr_format, 'INR_LAKHS' ); ?>>Indian Rupees (Lakhs/Crores - e.g. ₹1.80 L / ₹4.50 Cr)</option>
                            <option value="INR_STANDARD" <?php selected( $curr_format, 'INR_STANDARD' ); ?>>Indian Rupees Standard (Comma separated - e.g. ₹1,80,000)</option>
                            <option value="USD" <?php selected( $curr_format, 'USD' ); ?>>US Dollars (Standard - e.g. $180,000)</option>
                        </select>
                        <p class="text-[10px] text-zinc-400 mt-1">Determines how prices, transactions, invoices, and payouts are formatted in the Ledger.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-zinc-100 pt-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">WhatsApp Cloud API Token</label>
                        <input type="password" name="cora_whatsapp_api_token" value="<?php echo esc_attr( get_option('cora_whatsapp_api_token', '') ); ?>" placeholder="EAAW..." class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">WhatsApp Business Phone ID</label>
                        <input type="text" name="cora_whatsapp_phone_number" value="<?php echo esc_attr( get_option('cora_whatsapp_phone_number', '') ); ?>" placeholder="e.g. 1093847291039" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                    </div>
                    <p class="sm:col-span-2 text-[10px] text-zinc-400">Configure WhatsApp credentials to activate automated transaction notifications, client shortlisting alerts, and showing follow-ups.</p>
                </div>
            </div>
        </div>

        <?php elseif ( $active_tab === 'reading' ) : ?>
        <!-- TAB 2: READING & SEO SETTINGS -->
        <div class="space-y-6 max-w-2xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">Reading & Search Engine Indexing</h3>
                <p class="text-xs text-zinc-500">Control homepage display mode and search engine crawler visibility.</p>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-2">Homepage Displays</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-xs text-zinc-800 font-medium cursor-pointer">
                            <input type="radio" name="show_on_front" value="posts" <?php checked( get_option('show_on_front'), 'posts' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <span>Your latest blog posts feed</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs text-zinc-800 font-medium cursor-pointer">
                            <input type="radio" name="show_on_front" value="page" <?php checked( get_option('show_on_front'), 'page' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <span>A static landing page</span>
                        </label>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">Static Homepage</label>
                        <select name="page_on_front" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                            <option value="0">— Select Page —</option>
                            <?php foreach ( $pages as $p ) : ?>
                                <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( get_option('page_on_front'), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">Posts Page (Blog Archive)</label>
                        <select name="page_for_posts" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                            <option value="0">— Select Page —</option>
                            <?php foreach ( $pages as $p ) : ?>
                                <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( get_option('page_for_posts'), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="pt-4 border-t border-zinc-100">
                    <label class="flex items-start gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                        <input type="checkbox" name="blog_public" value="0" <?php checked( get_option('blog_public'), 0 ); ?> class="rounded border-zinc-300 text-red-600 focus:ring-red-600 mt-0.5">
                        <div>
                            <span class="text-red-700 font-bold">Discourage search engines from indexing this site</span>
                            <p class="text-[11px] text-zinc-500 font-normal">Modifies robots.txt and meta tags. Note: It is up to search engines to honor this request.</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <?php elseif ( $active_tab === 'writing' ) : ?>
        <!-- TAB 3: WRITING DEFAULTS -->
        <div class="space-y-6 max-w-xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">Writing & Content Defaults</h3>
                <p class="text-xs text-zinc-500">Configure default taxonomy labeling and publishing format presets.</p>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Default Post Category</label>
                    <select name="default_category" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                        <?php foreach ( $categories as $cat ) : ?>
                            <option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( get_option('default_category'), $cat->term_id ); ?>><?php echo esc_html( $cat->name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Default Post Format</label>
                    <select name="default_post_format" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                        <option value="0" <?php selected( get_option('default_post_format'), '0' ); ?>>Standard</option>
                        <option value="gallery" <?php selected( get_option('default_post_format'), 'gallery' ); ?>>Gallery</option>
                        <option value="video" <?php selected( get_option('default_post_format'), 'video' ); ?>>Video</option>
                        <option value="quote" <?php selected( get_option('default_post_format'), 'quote' ); ?>>Quote</option>
                    </select>
                </div>
            </div>
        </div>

        <?php elseif ( $active_tab === 'discussion' ) : ?>
        <!-- TAB 4: DISCUSSION & MODERATION -->
        <div class="space-y-6 max-w-2xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">Discussion & Comment Moderation Rules</h3>
                <p class="text-xs text-zinc-500">Automate spam filtering, link limits, and moderation blacklists.</p>
            </div>
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                        <input type="checkbox" name="default_pingback_flag" value="1" <?php checked( get_option('default_pingback_flag'), 1 ); ?> class="rounded border-zinc-300 text-zinc-900">
                        <span>Allow link notifications from other blogs (pingbacks and trackbacks)</span>
                    </label>
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                        <input type="checkbox" name="default_comment_status" value="open" <?php checked( get_option('default_comment_status'), 'open' ); ?> class="rounded border-zinc-300 text-zinc-900">
                        <span>Allow people to submit comments on new articles</span>
                    </label>
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                        <input type="checkbox" name="comment_moderation" value="1" <?php checked( get_option('comment_moderation'), 1 ); ?> class="rounded border-zinc-300 text-zinc-900">
                        <span>Comment must be manually approved before publishing</span>
                    </label>
                </div>
                <div class="pt-2">
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Comment Moderation Queue Keywords</label>
                    <textarea name="moderation_keys" rows="3" placeholder="One word, IP address, or URL per line..." class="w-full bg-white border border-zinc-300 rounded-lg p-2.5 text-xs text-zinc-900 font-mono focus:outline-none"><?php echo esc_textarea( get_option('moderation_keys') ); ?></textarea>
                    <p class="text-[11px] text-zinc-400 mt-1">When a comment contains any of these words in its content, name, URL, email, or IP address, it will be held in the moderation queue.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-red-800 mb-1">Disallowed Comment Keys (Automatic Trash/Spam)</label>
                    <textarea name="disallowed_keys" rows="3" placeholder="One word, IP address, or URL per line..." class="w-full bg-white border border-red-300 rounded-lg p-2.5 text-xs text-zinc-900 font-mono focus:outline-none"><?php echo esc_textarea( get_option('disallowed_keys') ); ?></textarea>
                </div>
            </div>
        </div>

        <?php elseif ( $active_tab === 'permalinks' ) : ?>
        <!-- TAB 5: SEO PERMALINKS -->
        <div class="space-y-6 max-w-xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">SEO URL Permalinks Structure</h3>
                <p class="text-xs text-zinc-500">Choose clean, human-readable URL routing schemas for better search engine rankings.</p>
            </div>
            <div class="space-y-3">
                <?php $current_permalink = get_option('permalink_structure'); ?>
                <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3 border border-zinc-200 rounded-lg bg-zinc-50 hover:bg-zinc-100 cursor-pointer transition-colors gap-2">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="permalink_structure" value="" <?php checked( $current_permalink, '' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                        <span class="text-xs font-bold text-zinc-900">Plain</span>
                    </div>
                    <code class="text-[11px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/?p=123') ); ?></code>
                </label>

                <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3 border border-zinc-200 rounded-lg bg-zinc-50 hover:bg-zinc-100 cursor-pointer transition-colors gap-2">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="permalink_structure" value="/%year%/%monthnum%/%day%/%postname%/" <?php checked( $current_permalink, '/%year%/%monthnum%/%day%/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                        <span class="text-xs font-bold text-zinc-900">Day and name</span>
                    </div>
                    <code class="text-[11px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/2026/07/08/sample-post/') ); ?></code>
                </label>

                <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3 border border-zinc-200 rounded-lg bg-zinc-50 hover:bg-zinc-100 cursor-pointer transition-colors gap-2">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="permalink_structure" value="/%year%/%monthnum%/%postname%/" <?php checked( $current_permalink, '/%year%/%monthnum%/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                        <span class="text-xs font-bold text-zinc-900">Month and name</span>
                    </div>
                    <code class="text-[11px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/2026/07/sample-post/') ); ?></code>
                </label>

                <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3 border border-zinc-900 rounded-lg bg-zinc-900/5 hover:bg-zinc-900/10 cursor-pointer transition-colors gap-2">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="permalink_structure" value="/%postname%/" <?php checked( $current_permalink, '/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                        <div>
                            <span class="text-xs font-bold text-zinc-900">Post name (Recommended SEO)</span>
                        </div>
                    </div>
                    <code class="text-[11px] text-zinc-900 font-bold font-mono truncate break-all"><?php echo esc_url( home_url('/sample-post/') ); ?></code>
                </label>
            </div>
        </div>

        <?php elseif ( $active_tab === 'privacy' ) : ?>
        <!-- TAB 6: PRIVACY POLICY -->
        <div class="space-y-6 max-w-xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">Privacy Policy Page Assignment</h3>
                <p class="text-xs text-zinc-500">Designate an official privacy policy page for legal compliance and user transparency.</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Change your Privacy Policy page</label>
                <div class="flex gap-2">
                    <select name="wp_page_for_privacy_policy" class="flex-1 bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                        <option value="0">— Select Page —</option>
                        <?php foreach ( $pages as $p ) : ?>
                            <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( get_option('wp_page_for_privacy_policy'), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a href="?page=cora-workspace&sub=pages" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-lg transition-colors flex items-center gap-1">Create New Page</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="pt-6 mt-6 border-t border-zinc-200 flex items-center justify-end">
            <button type="submit" class="px-6 py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors shadow-sm cursor-pointer flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2 2H5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Save All Settings
            </button>
        </div>
    </form>
</div>
