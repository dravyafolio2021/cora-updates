<?php
/**
 * Cora Real Estate CRM - Module 2: Client Discussions & WP Comment Moderation
 * Studio-Grade Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Fetch comments based on filter if set
$status_filter = isset( $_GET['comment_status'] ) ? sanitize_text_field( $_GET['comment_status'] ) : '';
$allowed_statuses = array( '', 'all', 'hold', 'approve', 'spam', 'trash' );
if ( in_array( $status_filter, $allowed_statuses, true ) ) {
    $args = array(
        'status' => $status_filter ? $status_filter : 'all',
        'number' => 50,
    );
    $cora_comments = get_comments( $args );
} else {
    $cora_comments = array();
}

// Counts for status tabs
$all_count     = wp_count_comments()->total_comments;
$pending_count = wp_count_comments()->moderated;
$approved_count = wp_count_comments()->approved;
$spam_count    = wp_count_comments()->spam;
$trash_count   = wp_count_comments()->trash;
?>

<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                <line x1="9" y1="10" x2="15" y2="10"></line>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Client Discussions & Lead Notes</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Moderate blog comments, client inquiries, and track follow-up communication timelines.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button class="cora-btn-secondary px-3.5 py-2 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 font-semibold rounded-md text-xs transition-colors cursor-pointer flex items-center gap-2 shadow-sm" onclick="coraRefreshComments()">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
            Refresh Feed
        </button>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="flex items-center gap-2 border-b border-zinc-200/80 pb-3 overflow-x-auto">
    <a href="?page=cora-workspace&sub=comments" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all <?php echo empty($status_filter) ? 'bg-zinc-950 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900'; ?>">
        All <span class="ml-1 text-[10px] opacity-70">(<?php echo esc_html($all_count); ?>)</span>
    </a>
    <a href="?page=cora-workspace&sub=comments&comment_status=hold" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all <?php echo $status_filter === 'hold' ? 'bg-zinc-950 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900'; ?>">
        Pending <span class="ml-1 text-[10px] opacity-70"><?php if($pending_count > 0): ?><span class="inline-flex items-center px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-amber-500 text-white"><?php echo esc_html($pending_count); ?></span><?php else: ?>(0)<?php endif; ?></span>
    </a>
    <a href="?page=cora-workspace&sub=comments&comment_status=approve" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all <?php echo $status_filter === 'approve' ? 'bg-zinc-950 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900'; ?>">
        Approved <span class="ml-1 text-[10px] opacity-70">(<?php echo esc_html($approved_count); ?>)</span>
    </a>
    <a href="?page=cora-workspace&sub=comments&comment_status=spam" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all <?php echo $status_filter === 'spam' ? 'bg-zinc-950 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900'; ?>">
        Spam <span class="ml-1 text-[10px] opacity-70">(<?php echo esc_html($spam_count); ?>)</span>
    </a>
    <a href="?page=cora-workspace&sub=comments&comment_status=trash" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all <?php echo $status_filter === 'trash' ? 'bg-zinc-950 text-white shadow-sm' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900'; ?>">
        Trash <span class="ml-1 text-[10px] opacity-70">(<?php echo esc_html($trash_count); ?>)</span>
    </a>
</div>

<!-- Comments Timeline Feed -->
<div class="bg-white border border-zinc-200/80 rounded-xl shadow-sm overflow-hidden">
    <?php if ( empty( $cora_comments ) ) : ?>
        <div class="p-12 text-center flex flex-col items-center justify-center gap-3">
            <div class="w-12 h-12 rounded-full bg-zinc-100 flex items-center justify-center text-zinc-400">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900">No discussions found</h3>
                <p class="text-xs text-zinc-500 mt-1">There are no comments or lead notes matching your selected filter.</p>
            </div>
        </div>
    <?php else : ?>
        <div class="divide-y divide-zinc-200/70">
            <?php foreach ( $cora_comments as $comment ) : 
                $comment_id = $comment->comment_ID;
                $author_name = $comment->comment_author ? $comment->comment_author : 'Anonymous Client';
                $author_email = $comment->comment_author_email;
                $comment_date = date_i18n( 'M j, Y @ g:i a', strtotime( $comment->comment_date ) );
                $post_title = get_the_title( $comment->comment_post_ID );
                $post_url   = get_permalink( $comment->comment_post_ID );
                $status     = wp_get_comment_status( $comment_id );
            ?>
            <div id="cora-comment-<?php echo esc_attr( $comment_id ); ?>" class="p-5 hover:bg-zinc-50/50 transition-colors flex flex-col sm:flex-row gap-4 items-start justify-between group">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <!-- Avatar -->
                    <div class="w-9 h-9 rounded-full bg-zinc-900 text-white font-bold text-xs flex items-center justify-center shrink-0 uppercase shadow-sm">
                        <?php echo esc_html( substr( $author_name, 0, 2 ) ); ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-bold text-zinc-900 truncate"><?php echo esc_html( $author_name ); ?></span>
                            <?php if ( $author_email ) : ?>
                                <span class="text-xs text-zinc-500 truncate">&lt;<?php echo esc_html( $author_email ); ?>&gt;</span>
                            <?php endif; ?>
                            <span class="text-[11px] text-zinc-400 font-mono"><?php echo esc_html( $comment_date ); ?></span>
                            <?php if ( $status === 'unapproved' ) : ?>
                                <span class="px-2 py-0.5 bg-amber-500/10 border border-amber-500/20 text-amber-700 text-[10px] font-bold rounded-full uppercase">Pending</span>
                            <?php elseif ( $status === 'spam' ) : ?>
                                <span class="px-2 py-0.5 bg-red-500/10 border border-red-500/20 text-red-700 text-[10px] font-bold rounded-full uppercase">Spam</span>
                            <?php elseif ( $status === 'trash' ) : ?>
                                <span class="px-2 py-0.5 bg-zinc-500/10 border border-zinc-500/20 text-zinc-700 text-[10px] font-bold rounded-full uppercase">Trash</span>
                            <?php else : ?>
                                <span class="px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 text-[10px] font-bold rounded-full uppercase">Approved</span>
                            <?php endif; ?>
                        </div>
                        <div class="mt-1.5 text-xs sm:text-sm text-zinc-700 leading-relaxed font-normal break-words">
                            <?php echo wp_kses_post( $comment->comment_content ); ?>
                        </div>
                        <div class="mt-2 text-[11px] text-zinc-500 flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            <span>In response to:</span>
                            <a href="<?php echo esc_url( $post_url ); ?>" target="_blank" class="font-semibold text-zinc-900 hover:underline"><?php echo esc_html( $post_title ? $post_title : 'Direct Note / Untitled' ); ?></a>
                        </div>

                        <!-- Inline Quick Actions -->
                        <div class="mt-3 flex flex-wrap items-center gap-3 pt-2 border-t border-zinc-100 opacity-90 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                            <?php if ( $status === 'unapproved' ) : ?>
                                <button class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors cursor-pointer flex items-center gap-1" onclick="coraModerateComment(<?php echo esc_js( $comment_id ); ?>, 'approve')">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Approve
                                </button>
                            <?php elseif ( $status === 'approved' ) : ?>
                                <button class="text-xs font-semibold text-amber-600 hover:text-amber-700 transition-colors cursor-pointer flex items-center gap-1" onclick="coraModerateComment(<?php echo esc_js( $comment_id ); ?>, 'hold')">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="10" y1="15" x2="10" y2="9"></line><line x1="14" y1="15" x2="14" y2="9"></line></svg>
                                    Unapprove
                                </button>
                            <?php endif; ?>

                            <button class="text-xs font-semibold text-zinc-700 hover:text-zinc-950 transition-colors cursor-pointer flex items-center gap-1" onclick="coraOpenCommentReplyDrawer(<?php echo esc_js( $comment_id ); ?>, '<?php echo esc_js( $author_name ); ?>', '<?php echo esc_js( substr( strip_tags( $comment->comment_content ), 0, 60 ) ); ?>...')">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 17 4 12 9 7"></polyline><path d="M20 18v-2a4 4 0 0 0-4-4H4"></path></svg>
                                Reply
                            </button>

                            <?php if ( $status !== 'spam' ) : ?>
                                <button class="text-xs font-semibold text-zinc-500 hover:text-red-600 transition-colors cursor-pointer flex items-center gap-1" onclick="coraModerateComment(<?php echo esc_js( $comment_id ); ?>, 'spam')">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                                    Spam
                                </button>
                            <?php endif; ?>

                            <?php if ( $status !== 'trash' ) : ?>
                                <button class="text-xs font-semibold text-zinc-500 hover:text-red-700 transition-colors cursor-pointer flex items-center gap-1" onclick="coraModerateComment(<?php echo esc_js( $comment_id ); ?>, 'trash')">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    Trash
                                </button>
                            <?php else : ?>
                                <button class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors cursor-pointer flex items-center gap-1" onclick="coraModerateComment(<?php echo esc_js( $comment_id ); ?>, 'restore')">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                                    Restore
                                </button>
                                <button class="text-xs font-bold text-red-700 hover:underline transition-colors cursor-pointer flex items-center gap-1" onclick="coraDeleteCommentPermanent(<?php echo esc_js( $comment_id ); ?>)">
                                    Delete Permanently
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Right-Sliding Drawer: Comment Reply -->
<div id="cora-drawer-comment-reply" class="fixed inset-y-0 right-0 z-[99999] w-full sm:w-[480px] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 translate-x-full">
    <div class="cora-drawer-header p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
        <div class="flex items-center gap-2.5">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-900"><polyline points="9 17 4 12 9 7"></polyline><path d="M20 18v-2a4 4 0 0 0-4-4H4"></path></svg>
            <h3 class="text-base font-bold text-zinc-900">Reply to Client Discussion</h3>
        </div>
        <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer p-1" onclick="coraCloseCommentReplyDrawer()">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-6 space-y-5">
        <input type="hidden" id="cora-reply-parent-id" value="">
        
        <!-- Original Comment Preview Card -->
        <div class="p-4 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-1.5">
            <div class="flex items-center justify-between text-xs font-bold text-zinc-800">
                <span id="cora-reply-author-name">Client Name</span>
                <span class="text-[10px] font-normal text-zinc-400 uppercase tracking-wider">Original Note</span>
            </div>
            <p id="cora-reply-content-preview" class="text-xs text-zinc-600 italic line-clamp-3">Comment excerpt preview goes here...</p>
        </div>

        <!-- Reply Content Input -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-zinc-800 uppercase tracking-wider">Your Reply / Team Note</label>
            <textarea id="cora-reply-textarea" rows="6" placeholder="Type your response to the client or team follow-up note here..." class="w-full bg-white border border-zinc-300 rounded-xl p-3.5 text-sm text-zinc-900 focus:outline-none focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 transition-all placeholder:text-zinc-400"></textarea>
            <p class="text-[11px] text-zinc-400">Replies will be notified via email if client notifications are active.</p>
        </div>
    </div>

    <div class="p-5 border-t border-zinc-200 bg-zinc-50/50 flex items-center justify-end gap-3">
        <button class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="coraCloseCommentReplyDrawer()">Cancel</button>
        <button id="cora-btn-submit-comment-reply" class="px-5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors shadow-sm cursor-pointer flex items-center gap-2" onclick="coraSubmitCommentReply()">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            Send Reply
        </button>
    </div>
</div>
