<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="flex-1 flex flex-col min-h-0 bg-zinc-50 dark:bg-zinc-950 p-6 md:p-8" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <!-- Top Header Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-zinc-200 dark:border-zinc-800 shrink-0">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-2.5">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-800 dark:text-zinc-200"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                Emails
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Compose and send official communications using your Hostinger business mail service.</p>
        </div>
        <!-- Connection Badge -->
        <div class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 px-3 py-1.5 rounded-xl shrink-0">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">Hostinger Business SMTP Connected</span>
        </div>
    </div>

    <!-- Main Workspace Content Grid -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6 min-h-0">
        <!-- Left Panel: Compose Email (7 columns) -->
        <div class="lg:col-span-7 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 shadow-xs">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                New Communication
            </h3>
            
            <form id="cora-email-compose-form" class="space-y-4 mt-4 flex-1 flex flex-col">
                <!-- To/Recipient -->
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Recipient Email (To)</label>
                    <input type="email" id="email-to" placeholder="client@example.com" required
                           class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden focus:border-zinc-400 dark:focus:border-zinc-500 transition-all shadow-3xs" />
                </div>

                <!-- Subject -->
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Subject</label>
                    <input type="text" id="email-subject" placeholder="Your appointment/service confirmation" required
                           class="w-full h-10 px-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden focus:border-zinc-400 dark:focus:border-zinc-500 transition-all shadow-3xs" />
                </div>

                <!-- Message Box -->
                <div class="space-y-1 flex-1 flex flex-col">
                    <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Message Content</label>
                    <textarea id="email-message" placeholder="Write your professional email content here..." required
                              class="w-full flex-1 min-h-64 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-hidden focus:border-zinc-400 dark:focus:border-zinc-500 transition-all resize-none shadow-3xs leading-relaxed"></textarea>
                </div>

                <!-- Action Toolbar -->
                <div class="flex items-center justify-between pt-2 shrink-0">
                    <span class="text-[10px] text-zinc-400 font-mono">Sent officially as: <?php echo esc_html(get_option('admin_email')); ?></span>
                    <button type="submit" id="btn-send-email"
                            class="h-10 px-5 rounded-xl bg-zinc-950 dark:bg-white hover:bg-zinc-900 dark:hover:bg-zinc-50 text-white dark:text-zinc-950 text-xs font-bold flex items-center gap-2 cursor-pointer transition-all border-none shadow-xs">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        Send Email
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Panel: Sent History Logs (5 columns) -->
        <div class="lg:col-span-5 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-5 shadow-xs min-h-0">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2 shrink-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 8v4l3 3"></path><circle cx="12" cy="12" r="10"></circle></svg>
                Sent History
            </h3>
            
            <!-- History Logs List (Scrollable) -->
            <div id="cora-email-logs-list" class="flex-1 overflow-y-auto mt-4 pr-1 space-y-3 min-h-0">
                <div class="text-center py-12 text-zinc-400 dark:text-zinc-500 italic text-xs">
                    Loading sent history...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Preview Slide Drawer -->
<div id="email-detail-drawer" class="fixed inset-y-0 right-0 w-full sm:max-w-md bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-[150] flex flex-col translate-x-full transition-transform duration-300 ease-in-out">
    <div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-800 shrink-0">
        <h3 class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Sent Communication Details</h3>
        <button onclick="closeEmailDrawer()" class="text-zinc-400 hover:text-zinc-650 dark:hover:text-white border-0 bg-transparent cursor-pointer text-sm">✕ Close</button>
    </div>
    <div class="flex-1 overflow-y-auto p-5 space-y-4">
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 dark:text-zinc-550 font-bold uppercase">To</div>
            <div id="drawer-email-to" class="text-xs font-semibold text-zinc-900 dark:text-zinc-100"></div>
        </div>
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 dark:text-zinc-550 font-bold uppercase">Sent At</div>
            <div id="drawer-email-date" class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono"></div>
        </div>
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 dark:text-zinc-500 font-bold uppercase">Subject</div>
            <div id="drawer-email-subject" class="text-xs font-bold text-zinc-900 dark:text-zinc-100"></div>
        </div>
        <div class="space-y-1">
            <div class="text-[10px] text-zinc-400 dark:text-zinc-500 font-bold uppercase">Message</div>
            <div id="drawer-email-message" class="text-xs text-zinc-800 dark:text-zinc-200 bg-zinc-50 dark:bg-zinc-900 p-4 rounded-xl whitespace-pre-wrap leading-relaxed border border-zinc-200 dark:border-zinc-800 font-medium"></div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const $form = $('#cora-email-compose-form');
    const $btn = $('#btn-send-email');
    const $logsList = $('#cora-email-logs-list');
    let emailLogs = [];

    // Fetch and render logs on mount
    function fetchEmailLogs() {
        $.ajax({
            url: '/wp-json/cora/v1/emails/logs',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', window.coraRestNonce || '');
            },
            success: function(res) {
                emailLogs = res || [];
                renderEmailLogs();
            },
            error: function() {
                $logsList.html('<div class="text-center py-12 text-red-500 text-xs font-semibold">Failed to load sent logs.</div>');
            }
        });
    }

    function renderEmailLogs() {
        if (emailLogs.length === 0) {
            $logsList.html('<div class="text-center py-12 text-zinc-400 dark:text-zinc-500 italic text-xs">No emails sent yet.</div>');
            return;
        }

        $logsList.html('');
        emailLogs.forEach((log, idx) => {
            const dateStr = new Date(log.sent_at.replace(/-/g, '/')).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
            const item = $(`
                <div class="p-3.5 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/80 rounded-xl cursor-pointer transition-all flex items-start justify-between gap-3 shadow-3xs" data-idx="${idx}">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-150 truncate">${escapeHtml(log.to)}</span>
                            <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono shrink-0">${dateStr}</span>
                        </div>
                        <div class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 truncate mt-0.5">${escapeHtml(log.subject)}</div>
                        <div class="text-[10px] text-zinc-400 dark:text-zinc-500 truncate mt-1">${escapeHtml(log.message)}</div>
                    </div>
                </div>
            `);
            item.on('click', () => showEmailDetail(log));
            $logsList.append(item);
        });
    }

    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }

    function showEmailDetail(log) {
        const formattedDate = new Date(log.sent_at.replace(/-/g, '/')).toLocaleString([], { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        $('#drawer-email-to').text(log.to);
        $('#drawer-email-date').text(formattedDate);
        $('#drawer-email-subject').text(log.subject);
        $('#drawer-email-message').text(log.message);
        $('#email-detail-drawer').removeClass('translate-x-full');
    }

    window.closeEmailDrawer = function() {
        $('#email-detail-drawer').addClass('translate-x-full');
    };

    // Form submit / Send Email handler
    $form.on('submit', function(e) {
        e.preventDefault();

        const to = $('#email-to').val().trim();
        const subject = $('#email-subject').val().trim();
        const message = $('#email-message').val().trim();

        if (!to || !subject || !message) {
            window.coraShowToast && window.coraShowToast("Please fill in all email fields.", "error");
            return;
        }

        $btn.prop('disabled', true).html(`
            <svg class="animate-spin shrink-0" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M22 12a10 10 0 0 1-10 10"></path></svg>
            Sending...
        `);

        $.ajax({
            url: '/wp-json/cora/v1/emails/send',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ to, subject, message }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', window.coraRestNonce || '');
            },
            success: function(res) {
                window.coraShowToast && window.coraShowToast("Email sent successfully! ✓", "success");
                $form[0].reset();
                fetchEmailLogs();
            },
            error: function(err) {
                const errMsg = err.responseJSON?.message || "Failed to send email. Check SMTP settings.";
                window.coraShowToast && window.coraShowToast(errMsg, "error");
            },
            complete: function() {
                $btn.prop('disabled', false).html(`
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    Send Email
                `);
            }
        });
    });

    // Initial Load
    fetchEmailLogs();
});
</script>
