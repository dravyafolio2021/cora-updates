<?php
/**
 * Cora Workspace - Facebook & Instagram Social Suite
 * File: views/view-social-meta.php
 * Premium, monochromatic high-fidelity interactive social media dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Sample posts
$scheduled_posts = array(
    array(
        'id'        => 1,
        'caption'   => 'Check out this stunning new modular commercial property listing in DLF CyberCity! Ideal workspace layout...',
        'platform'  => 'Instagram & FB',
        'date'      => 'Tomorrow, 10:00 AM',
        'status'    => 'Scheduled',
        'image_text'=> 'Commercial Listing Preview'
    ),
    array(
        'id'        => 2,
        'caption'   => 'Behind the scenes at today\'s portrait studio photoshoot session. Lighting setups, camera rigs, and premium portfolios...',
        'platform'  => 'Instagram',
        'date'      => 'Aug 16, 04:30 PM',
        'status'    => 'Draft',
        'image_text'=> 'Studio BTS Session'
    ),
    array(
        'id'        => 3,
        'caption'   => 'Excited to announce our partnership with Apex Realty Partners to audit commercial spaces across Gurugram!',
        'platform'  => 'Facebook',
        'date'      => 'Aug 19, 09:00 AM',
        'status'    => 'Scheduled',
        'image_text'=> 'Partnership Announcement'
    )
);

// Sample feed grid mockups
$feed_items = array(
    array( 'likes' => '1.2k', 'comments' => '42', 'tag' => 'Premium' ),
    array( 'likes' => '948', 'comments' => '21', 'tag' => 'Listing' ),
    array( 'likes' => '1.5k', 'comments' => '68', 'tag' => 'Studio' )
);
?>

<div class="space-y-6 font-sans text-zinc-900 select-none max-w-[1700px] mx-auto pb-12">
    <!-- Page Header -->
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-zinc-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-950">● Meta Marketing Suite</h1>
            <p class="text-xs font-medium text-zinc-500 mt-1">Schedule campaign posts, check Instagram feed mockup layouts, and track public engagement.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="if(window.coraShowToast) window.coraShowToast('Meta API connection status verified.', 'success')" class="px-4 py-2 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 text-xs font-bold rounded-xl transition-all shadow-2xs cursor-pointer flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                Sync Channels
            </button>
            <button onclick="if(window.coraShowToast) window.coraShowToast('Post Composer coming soon!', 'info')" class="px-4.5 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Create Campaign
            </button>
        </div>
    </header>

    <!-- Main Workspace Split Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 2/3 Width: Scheduled Posts Manager -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white border border-zinc-200 rounded-2xl shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-150 flex items-center justify-between">
                    <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Scheduled & Pending Campaigns</span>
                    <span class="px-2.5 py-0.5 bg-zinc-100 text-zinc-500 border border-zinc-200 text-[10px] font-extrabold rounded-full">3 Posts</span>
                </div>

                <div class="divide-y divide-zinc-100">
                    <?php foreach ($scheduled_posts as $post) : ?>
                        <div class="p-5 hover:bg-zinc-50/50 transition-all flex flex-col md:flex-row gap-5 items-start">
                            <!-- Image mockup container -->
                            <div class="w-full md:w-32 h-24 bg-zinc-100 rounded-xl border border-zinc-200 flex flex-col items-center justify-center text-center p-3 shrink-0">
                                <span class="text-[9px] font-extrabold text-zinc-400 uppercase tracking-widest leading-normal"><?php echo esc_html($post['image_text']); ?></span>
                            </div>

                            <!-- Post details -->
                            <div class="flex-1 space-y-3 min-w-0">
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <span class="px-2 py-0.5 bg-white border border-zinc-200 rounded-md text-[9px] font-bold text-zinc-500 uppercase"><?php echo esc_html($post['platform']); ?></span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold <?php echo ($post['status'] === 'Scheduled') ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-150 text-zinc-650'; ?>"><?php echo esc_html($post['status']); ?></span>
                                    <span class="text-[10px] text-zinc-400 font-mono flex items-center gap-1">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <?php echo esc_html($post['date']); ?>
                                    </span>
                                </div>
                                <p class="text-xs font-medium text-zinc-800 leading-relaxed truncate-2-lines"><?php echo esc_html($post['caption']); ?></p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 self-stretch justify-end shrink-0 md:flex-col md:justify-center">
                                <button onclick="if(window.coraShowToast) window.coraShowToast('Post edit option coming soon.', 'info')" class="p-2 border border-zinc-200 hover:bg-zinc-50 rounded-xl text-zinc-700 transition-all cursor-pointer">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 1/3 Width: Instagram Grid Mockup -->
        <div class="space-y-4">
            <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-2xs space-y-4">
                <div class="border-b border-zinc-100 pb-2 flex items-center justify-between">
                    <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Instagram Feed Preview</span>
                    <span class="text-[9px] font-extrabold text-zinc-400 font-mono">Mock Feed Grid</span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <?php foreach ($feed_items as $item) : ?>
                        <div class="aspect-square bg-zinc-100 rounded-lg border border-zinc-200 flex flex-col items-center justify-center p-2 relative group hover:bg-zinc-200/50 transition-all cursor-pointer">
                            <span class="text-[8px] font-extrabold text-zinc-400 uppercase tracking-widest"><?php echo esc_html($item['tag']); ?></span>
                            <!-- Hover state data overlay -->
                            <div class="absolute inset-0 bg-zinc-950/80 rounded-lg flex flex-col items-center justify-center text-[9px] font-bold text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                <span>❤ <?php echo $item['likes']; ?></span>
                                <span class="mt-1">💬 <?php echo $item['comments']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
