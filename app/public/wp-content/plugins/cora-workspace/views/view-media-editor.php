<?php
/**
 * Cora Real Estate CRM - Module 5: Media Library Advanced Editor & Metadata
 * Studio-Grade Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get recent attachments for editor dropdown
$recent_media = get_posts( array(
    'post_type'      => 'attachment',
    'post_mime_type' => 'image',
    'post_status'    => 'inherit',
    'posts_per_page' => 30,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
?>

<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Media Library Advanced Editor</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Crop aspect ratios (1:1, 4:3, 16:9), rotate canvas, scale dimensions, and optimize SEO Alt attributes.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button class="cora-btn-secondary px-3.5 py-2 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 font-semibold rounded-md text-xs transition-colors cursor-pointer flex items-center gap-2 shadow-sm" onclick="coraOpenMediaUploader()">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            Upload New Media
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Interactive Image Manipulation Canvas (2 cols) -->
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4">
            <!-- Media Selector Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-100 pb-4">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-zinc-800">Select Image to Edit:</label>
                    <select id="cora-editor-media-select" class="bg-zinc-50 border border-zinc-300 rounded-lg px-3 py-1.5 text-xs font-semibold text-zinc-900 focus:outline-none" onchange="coraLoadMediaIntoEditor(this.value)">
                        <?php if ( empty( $recent_media ) ) : ?>
                            <option value="0">No image attachments found</option>
                        <?php else : ?>
                            <?php foreach ( $recent_media as $m ) : ?>
                                <option value="<?php echo esc_attr( $m->ID ); ?>" data-url="<?php echo esc_url( wp_get_attachment_url( $m->ID ) ); ?>" data-title="<?php echo esc_attr( $m->post_title ); ?>"><?php echo esc_html( $m->post_title ? $m->post_title : 'Untitled Image #' . $m->ID ); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="flex items-center gap-1.5">
                    <button class="px-2.5 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-[11px] font-bold rounded transition-colors cursor-pointer" onclick="coraResetEditorCanvas()">Reset Canvas</button>
                </div>
            </div>

            <!-- Aspect Ratio & Transformation Toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-3 bg-zinc-50 p-3 rounded-lg border border-zinc-200/60">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider mr-1">Crop Presets:</span>
                    <button class="px-2.5 py-1 bg-white border border-zinc-300 hover:border-zinc-900 text-zinc-800 text-xs font-semibold rounded transition-all cursor-pointer shadow-2xs" onclick="coraSetCropRatio(1, 1)">1:1 Square</button>
                    <button class="px-2.5 py-1 bg-white border border-zinc-300 hover:border-zinc-900 text-zinc-800 text-xs font-semibold rounded transition-all cursor-pointer shadow-2xs" onclick="coraSetCropRatio(4, 3)">4:3 Standard</button>
                    <button class="px-2.5 py-1 bg-white border border-zinc-300 hover:border-zinc-900 text-zinc-800 text-xs font-semibold rounded transition-all cursor-pointer shadow-2xs" onclick="coraSetCropRatio(16, 9)">16:9 Widescreen</button>
                    <button class="px-2.5 py-1 bg-white border border-zinc-300 hover:border-zinc-900 text-zinc-800 text-xs font-semibold rounded transition-all cursor-pointer shadow-2xs" onclick="coraSetCropRatio(null)">Free Crop</button>
                </div>

                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider mr-1">Transform:</span>
                    <button class="p-1.5 bg-white border border-zinc-300 hover:border-zinc-900 text-zinc-800 rounded transition-all cursor-pointer shadow-2xs" title="Rotate 90° Clockwise" onclick="coraRotateImage(90)">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                    </button>
                    <button class="p-1.5 bg-white border border-zinc-300 hover:border-zinc-900 text-zinc-800 rounded transition-all cursor-pointer shadow-2xs" title="Rotate 90° Counter-Clockwise" onclick="coraRotateImage(-90)">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M2.5 2v6h6M2.66 15.57a10 10 0 1 0 .57-8.38l-5.67-5.67"></path></svg>
                    </button>
                    <button class="p-1.5 bg-white border border-zinc-300 hover:border-zinc-900 text-zinc-800 rounded transition-all cursor-pointer shadow-2xs" title="Flip Horizontal" onclick="coraFlipImage('h')">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Image Canvas Preview Box -->
            <div id="cora-editor-canvas-container" class="w-full bg-zinc-950 rounded-xl overflow-hidden min-h-[380px] flex items-center justify-center relative border border-zinc-900">
                <?php if ( ! empty( $recent_media ) ) : $first_url = wp_get_attachment_url( $recent_media[0]->ID ); ?>
                    <img id="cora-editor-preview-img" src="<?php echo esc_url( $first_url ); ?>" class="max-h-[420px] max-w-full object-contain transition-transform" alt="Editor Preview" loading="lazy">
                <?php else : ?>
                    <div class="text-zinc-600 text-center space-y-2 p-8">
                        <svg viewBox="0 0 24 24" width="36" height="36" stroke="currentColor" stroke-width="1.5" fill="none" class="mx-auto"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        <p class="text-xs font-bold text-zinc-400">No Image Selected for Editing</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Scale Dimensions & Save Footer -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-3 border-t border-zinc-100">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-zinc-800">Scale Dimensions:</span>
                    <input type="number" id="cora-scale-width" placeholder="Width" class="w-20 bg-white border border-zinc-300 rounded px-2 py-1 text-xs text-zinc-900 font-mono">
                    <span class="text-zinc-400">×</span>
                    <input type="number" id="cora-scale-height" placeholder="Height" class="w-20 bg-white border border-zinc-300 rounded px-2 py-1 text-xs text-zinc-900 font-mono">
                    <span class="text-[11px] text-zinc-400 font-mono">px</span>
                </div>
                <button class="px-5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors shadow-sm cursor-pointer flex items-center gap-2" onclick="coraSaveEditedImage()">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Apply & Save Image Transformation
                </button>
            </div>
        </div>
    </div>

    <!-- Right Column: SEO Attachment Metadata Editor -->
    <div class="space-y-6">
        <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4">
            <div class="border-b border-zinc-100 pb-3 flex items-center justify-between">
                <h3 class="text-sm font-bold text-zinc-900">Attachment SEO Metadata</h3>
                <span class="text-[10px] bg-zinc-900 text-white px-2 py-0.5 rounded font-bold uppercase">SEO</span>
            </div>

            <input type="hidden" id="cora-meta-attachment-id" value="<?php echo ! empty( $recent_media ) ? esc_attr( $recent_media[0]->ID ) : ''; ?>">

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Attachment Title</label>
                    <input type="text" id="cora-meta-title" value="<?php echo ! empty( $recent_media ) ? esc_attr( $recent_media[0]->post_title ) : ''; ?>" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-zinc-800">Alternative Text (Alt Attribute)</label>
                        <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">Required for Accessibility</span>
                    </div>
                    <input type="text" id="cora-meta-alt" value="<?php echo ! empty( $recent_media ) ? esc_attr( get_post_meta( $recent_media[0]->ID, '_wp_attachment_image_alt', true ) ) : ''; ?>" placeholder="Describe image purpose for screen readers & SEO" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Caption</label>
                    <input type="text" id="cora-meta-caption" value="<?php echo ! empty( $recent_media ) ? esc_attr( $recent_media[0]->post_excerpt ) : ''; ?>" placeholder="Displayed directly below image on frontend" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Description / Notes</label>
                    <textarea id="cora-meta-description" rows="4" placeholder="Internal notes or detailed media description..." class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900"><?php echo ! empty( $recent_media ) ? esc_textarea( $recent_media[0]->post_content ) : ''; ?></textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-zinc-100 flex items-center justify-end">
                <button class="w-full py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors shadow-sm cursor-pointer" onclick="coraSaveMediaMetadata()">
                    Update SEO Metadata
                </button>
            </div>
        </div>
    </div>
</div>
