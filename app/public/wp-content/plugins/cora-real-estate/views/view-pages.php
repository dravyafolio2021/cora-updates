<?php
/**
 * View: Static Pages & Landing Page Builder
 * Notion/Shopify Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cora_pages = get_pages( array(
    'post_status' => array( 'publish', 'draft', 'private' ),
    'sort_column' => 'menu_order, post_title',
    'sort_order'  => 'ASC'
) );
if ( ! is_array( $cora_pages ) ) {
    $cora_pages = array();
}

$total_pages     = count( $cora_pages );
$published_pages = 0;
$draft_pages     = 0;

foreach ( $cora_pages as $c_page ) {
    if ( $c_page->post_status === 'publish' ) {
        $published_pages++;
    } else {
        $draft_pages++;
    }
}
?>

<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Static Pages & Landing Pages</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Manage agency content, legal terms, and marketing landing pages.</p>
        </div>
    </div>
    <button class="cora-btn-primary px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-md text-sm transition-colors cursor-pointer flex items-center gap-2 shadow-sm" onclick="coraOpenPageDrawer()">
        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>New Page</span>
    </button>
</div>

<!-- Metrics Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <div class="cora-stat-card p-5 bg-white border border-zinc-200 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Total Pages</span>
            <span class="p-1.5 bg-zinc-100 rounded text-zinc-600">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
            </span>
        </div>
        <div class="text-3xl font-bold text-zinc-900"><?php echo esc_html( $total_pages ); ?></div>
        <div class="text-[11px] text-zinc-500 font-semibold mt-1">Total static & landing pages</div>
    </div>
    <div class="cora-stat-card p-5 bg-white border border-zinc-200 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Published</span>
            <span class="p-1.5 bg-zinc-100 rounded text-zinc-900">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </span>
        </div>
        <div class="text-3xl font-bold text-zinc-900"><?php echo esc_html( $published_pages ); ?></div>
        <div class="text-[11px] text-zinc-500 mt-1">Live accessible content pages</div>
    </div>
    <div class="cora-stat-card p-5 bg-white border border-zinc-200 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Drafts</span>
            <span class="p-1.5 bg-zinc-100 rounded text-zinc-600">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
            </span>
        </div>
        <div class="text-3xl font-bold text-zinc-900"><?php echo esc_html( $draft_pages ); ?></div>
        <div class="text-[11px] text-zinc-500 mt-1">Draft & private pages pending</div>
    </div>
</div>

<!-- Pages Table -->
<div class="border border-zinc-200 rounded-lg overflow-hidden bg-white shadow-sm mt-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-50 border-b border-zinc-200 text-[10px] font-bold text-zinc-400 uppercase tracking-wider select-none">
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Permalink / Slug</th>
                    <th class="py-3 px-4">Parent Page</th>
                    <th class="py-3 px-4">Template</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Quick Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 text-sm text-zinc-700" id="cora-pages-table-body">
                <?php if ( empty( $cora_pages ) ) : ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-zinc-500 text-sm">No pages found. Click "New Page" to create your first static or landing page!</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ( $cora_pages as $page ) : 
                        $page_id       = $page->ID;
                        $page_title    = $page->post_title ? $page->post_title : '(no title)';
                        $page_slug     = urldecode( $page->post_name );
                        $permalink     = get_permalink( $page_id );
                        $parent_title  = ( $page->post_parent > 0 ) ? get_the_title( $page->post_parent ) : '—';
                        $template_slug = get_post_meta( $page_id, '_wp_page_template', true );
                        
                        if ( empty( $template_slug ) || $template_slug === 'default' ) {
                            $template_label = 'Default';
                        } elseif ( $template_slug === 'full-width' || $template_slug === 'template-full-width.php' ) {
                            $template_label = 'Full Width';
                        } elseif ( $template_slug === 'landing-page' || $template_slug === 'template-landing-page.php' ) {
                            $template_label = 'Landing Page';
                        } else {
                            $template_label = ucwords( str_replace( array( '-', '_', '.php' ), ' ', $template_slug ) );
                        }
                        
                        $status = $page->post_status;
                    ?>
                    <?php 
                    $is_visual_builder = get_post_meta( $page_id, '_cora_is_visual_builder', true ) === '1';
                    $row_action = $is_visual_builder 
                        ? "window.location.href = '" . esc_url( home_url( '/workspace/visual-builder?page_id=' . $page_id ) ) . "';" 
                        : "coraOpenPageDrawer(" . esc_attr( $page_id ) . ")";
                    ?>
                    <tr class="hover:bg-zinc-50/50 transition-colors cursor-pointer cora-page-row" onclick="<?php echo $row_action; ?>">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-zinc-900"><?php echo esc_html( $page_title ); ?></div>
                                    <?php if ( $page->menu_order > 0 ) : ?>
                                        <div class="text-[10px] text-zinc-400 font-medium">Order: <?php echo esc_html( $page->menu_order ); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-mono text-xs text-zinc-500 bg-zinc-50 px-2 py-1 rounded border border-zinc-200/60 inline-block max-w-[200px] truncate" title="<?php echo esc_attr( $permalink ); ?>">
                                /<?php echo esc_html( $page_slug ); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-zinc-600 font-medium">
                            <?php echo esc_html( $parent_title ); ?>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-medium text-zinc-700 bg-zinc-100 px-2 py-1 rounded-md inline-block">
                                <?php echo esc_html( $template_label ); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <?php if ( $status === 'publish' ) : ?>
                                <span class="px-2 py-0.5 bg-zinc-900 text-white font-semibold rounded text-[10px] uppercase tracking-wider">Published</span>
                            <?php elseif ( $status === 'draft' ) : ?>
                                <span class="px-2 py-0.5 bg-zinc-100 text-zinc-700 border border-zinc-300 font-semibold rounded text-[10px] uppercase tracking-wider">Draft</span>
                            <?php else : ?>
                                <span class="px-2 py-0.5 bg-zinc-200 text-zinc-800 border border-zinc-300 font-semibold rounded text-[10px] uppercase tracking-wider">Private</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5" onclick="event.stopPropagation();">
                                <?php if ( $is_visual_builder ) : ?>
                                    <button class="cora-btn-action px-2.5 py-1 border border-zinc-200 rounded text-[11px] font-semibold text-zinc-700 hover:bg-zinc-100 transition-colors cursor-pointer" onclick="window.location.href = '<?php echo esc_url( home_url( '/workspace/visual-builder?page_id=' . $page_id ) ); ?>';">Edit</button>
                                <?php else : ?>
                                    <button class="cora-btn-action px-2.5 py-1 border border-zinc-200 rounded text-[11px] font-semibold text-zinc-700 hover:bg-zinc-100 transition-colors cursor-pointer" onclick="coraOpenPageDrawer(<?php echo esc_attr( $page_id ); ?>)">Edit</button>
                                <?php endif; ?>
                                <a href="<?php echo esc_url( $permalink ); ?>" target="_blank" class="px-2.5 py-1 border border-zinc-200 rounded text-[11px] font-semibold text-zinc-700 hover:bg-zinc-100 transition-colors inline-block">View</a>
                                <button class="px-2.5 py-1 border border-zinc-200 rounded text-[11px] font-semibold text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 transition-colors cursor-pointer" onclick="coraDeletePage(<?php echo esc_attr( $page_id ); ?>)">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Right-Sliding Drawer Sheet: Page Editor -->
<div id="cora-drawer-page-overlay" class="fixed inset-0 bg-zinc-900/20 backdrop-blur-[1px] z-[99998] hidden transition-opacity duration-300" onclick="coraClosePageDrawer()"></div>
<div id="cora-drawer-page" class="fixed inset-y-0 right-0 w-[650px] max-w-[95vw] bg-white shadow-2xl border-l border-zinc-200 transform translate-x-full transition-transform duration-300 z-[99999] flex flex-col">
    <div class="flex items-center justify-between p-5 border-b border-zinc-100 bg-zinc-50/50 shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-1.5 bg-zinc-900 text-white rounded">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            </span>
            <h2 id="cora-drawer-page-title" class="text-base font-bold text-zinc-900">Page Editor</h2>
        </div>
        <button class="cora-close-page-drawer text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer p-1 rounded hover:bg-zinc-100" onclick="coraClosePageDrawer()" title="Close Editor">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    
    <div class="p-6 overflow-y-auto flex-1 space-y-5">
        <input type="hidden" id="cora-page-id-input" value="0">
        
        <!-- Page Title -->
        <div>
            <label class="block text-xs font-semibold text-zinc-700 mb-1.5">Page Title *</label>
            <input type="text" id="cora-page-title-input" class="w-full px-3 py-2 text-sm border border-zinc-200 rounded-md focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. About Our Agency">
        </div>
        
        <!-- URL Slug & Status Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5">URL Slug</label>
                <div class="flex items-center border border-zinc-200 rounded-md bg-zinc-50 overflow-hidden focus-within:border-zinc-400 focus-within:bg-white transition-colors">
                    <span class="pl-3 text-xs text-zinc-400 font-mono select-none">/</span>
                    <input type="text" id="cora-page-slug-input" class="w-full px-2 py-2 text-sm bg-transparent border-0 focus:outline-none font-mono text-zinc-800 placeholder-zinc-400" placeholder="about-our-agency">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5">Status</label>
                <select id="cora-page-status-input" class="w-full px-3 py-2 text-sm border border-zinc-200 rounded-md bg-white text-zinc-800 focus:border-zinc-400 focus:outline-none transition-colors">
                    <option value="publish">Published</option>
                    <option value="draft">Draft</option>
                    <option value="private">Private</option>
                </select>
            </div>
        </div>
        
        <!-- Parent Page & Template Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5">Parent Page</label>
                <div id="cora-page-parent-wrapper">
                    <?php
                    $dropdown_args = array(
                        'name'              => 'cora_page_parent',
                        'id'                => 'cora-page-parent-input',
                        'show_option_none'  => '— No Parent (Top Level) —',
                        'option_none_value' => '0',
                        'selected'          => 0,
                        'echo'              => 0,
                        'class'             => 'w-full px-3 py-2 text-sm border border-zinc-200 rounded-md bg-white text-zinc-800 focus:border-zinc-400 focus:outline-none transition-colors'
                    );
                    $parent_dropdown = wp_dropdown_pages( $dropdown_args );
                    if ( empty( $parent_dropdown ) ) {
                        $parent_dropdown = '<select name="cora_page_parent" id="cora-page-parent-input" class="w-full px-3 py-2 text-sm border border-zinc-200 rounded-md bg-white text-zinc-800 focus:border-zinc-400 focus:outline-none transition-colors"><option value="0">— No Parent (Top Level) —</option></select>';
                    }
                    echo $parent_dropdown;
                    ?>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5">Page Template</label>
                <select id="cora-page-template-input" class="w-full px-3 py-2 text-sm border border-zinc-200 rounded-md bg-white text-zinc-800 focus:border-zinc-400 focus:outline-none transition-colors">
                    <option value="default">Default</option>
                    <option value="full-width">Full Width</option>
                    <option value="landing-page">Landing Page</option>
                </select>
            </div>
        </div>
        
        <!-- Menu Order -->
        <div class="w-1/3">
            <label class="block text-xs font-semibold text-zinc-700 mb-1.5">Menu Order</label>
            <input type="number" id="cora-page-order-input" class="w-full px-3 py-2 text-sm border border-zinc-200 rounded-md focus:border-zinc-400 focus:outline-none transition-colors" value="0" step="1">
        </div>
        
        <!-- Content Editor -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-semibold text-zinc-700">Content Editor</label>
                <span class="text-[11px] text-zinc-400">Rich text format</span>
            </div>
            <div class="border border-zinc-200 rounded-md overflow-hidden bg-white shadow-sm">
                <div id="cora-page-quill-editor" class="min-h-[250px] max-h-[400px] text-sm text-zinc-800"></div>
            </div>
        </div>
        
        <!-- SEO Meta Description -->
        <div>
            <label class="block text-xs font-semibold text-zinc-700 mb-1.5">SEO Meta Description</label>
            <textarea id="cora-page-seo-desc-input" rows="3" class="w-full px-3 py-2 text-sm border border-zinc-200 rounded-md focus:border-zinc-400 focus:outline-none transition-colors" placeholder="Write a compelling summary for search engine results..."></textarea>
            <p class="text-[11px] text-zinc-400 mt-1">Recommended length: 150-160 characters.</p>
        </div>
    </div>
    
    <!-- Footer Buttons -->
    <div class="p-5 border-t border-zinc-100 bg-zinc-50 flex justify-end gap-3 shrink-0">
        <button class="px-4 py-2 text-sm font-medium text-zinc-600 bg-white border border-zinc-200 rounded-md hover:bg-zinc-50 transition-colors cursor-pointer" onclick="coraClosePageDrawer()">Cancel</button>
        <button class="px-4 py-2 text-sm font-semibold text-white bg-zinc-900 rounded-md hover:bg-zinc-800 transition-colors flex items-center gap-2 cursor-pointer shadow-sm" onclick="coraSubmitPage()">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>Save Page</span>
        </button>
    </div>
</div>
