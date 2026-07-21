<?php if (!defined('ABSPATH')) exit; ?>

<aside id="cora-approval-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[500px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="px-6 py-4 border-b border-zinc-200 flex justify-between items-center shrink-0">
        <div>
            <h2 class="text-base font-bold text-zinc-900">Content Approval</h2>
            <p class="text-xs text-zinc-500 mt-0.5">Review and approve or request revisions.</p>
        </div>
        <button onclick="closeApprovalDrawer()" class="text-zinc-400 hover:text-zinc-900">
            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="p-6 flex-1 overflow-y-auto space-y-5">
        <input type="hidden" id="approval-item-id">
        <input type="hidden" id="approval-post-id">

        <!-- Item Info Header -->
        <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-4">
            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Content Item</div>
            <div class="text-sm font-bold text-zinc-900" id="approval-item-title">Loading...</div>
            <div class="flex items-center gap-3 mt-2">
                <span class="text-[10px] font-bold text-zinc-500" id="approval-item-stage"></span>
                <span class="text-[10px] font-bold text-zinc-500" id="approval-item-seo"></span>
            </div>
        </div>

        <!-- SEO Gate Check -->
        <div class="border border-zinc-200 rounded-xl overflow-hidden">
            <div class="px-4 py-3 bg-zinc-50 border-b border-zinc-200">
                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Pre-Flight SEO Gate</h3>
            </div>
            <div class="p-4 space-y-2" id="approval-seo-checklist">
                <div class="text-xs text-zinc-500">Run SEO check first...</div>
            </div>
            <div class="px-4 pb-3">
                <button onclick="runApprovalSEOCheck()" class="text-xs font-bold text-zinc-700 hover:text-zinc-900 underline">Re-run SEO Analysis</button>
            </div>
        </div>

        <!-- Compliance Toggles -->
        <div class="border border-zinc-200 rounded-xl p-4 space-y-3">
            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Compliance Checklist</h3>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" id="compliance-fair-housing" class="w-4 h-4 rounded border-zinc-300">
                <div>
                    <div class="text-xs font-bold text-zinc-800">Fair Housing Compliance</div>
                    <div class="text-[10px] text-zinc-500">Content does not discriminate based on protected characteristics.</div>
                </div>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" id="compliance-media-release" class="w-4 h-4 rounded border-zinc-300">
                <div>
                    <div class="text-xs font-bold text-zinc-800">Media / Client Release Verified</div>
                    <div class="text-[10px] text-zinc-500">All photos, testimonials, and client data are properly released.</div>
                </div>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" id="compliance-factcheck" class="w-4 h-4 rounded border-zinc-300">
                <div>
                    <div class="text-xs font-bold text-zinc-800">Fact-Checked & Accurate</div>
                    <div class="text-[10px] text-zinc-500">All statistics, prices, and property data have been verified.</div>
                </div>
            </label>
        </div>

        <!-- Approver Role & Notes -->
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-zinc-700 mb-1">Approver Role</label>
                <select id="approver-role" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-sm">
                    <option value="content_manager">Content Manager</option>
                    <option value="broker">Broker / Realtor in Charge</option>
                    <option value="cmo">CMO / Marketing Director</option>
                    <option value="studio_lead">Studio Lead (Photography)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-700 mb-1">Approval / Revision Notes</label>
                <textarea id="approval-notes" rows="3" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-sm" placeholder="Add notes for the writer or record reasoning..."></textarea>
            </div>
        </div>

        <!-- WordPress Publish Options (shown after approval) -->
        <div class="border border-zinc-200 rounded-xl p-4 space-y-3" id="wp-publish-section" style="display:none">
            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">WordPress Publishing</h3>
            <div>
                <label class="block text-xs font-bold text-zinc-700 mb-1">Post Type</label>
                <select id="wp-post-type" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-sm">
                    <option value="post">Blog Post</option>
                    <option value="page">Page</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-700 mb-1">Publish Status</label>
                <select id="wp-publish-status" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-sm">
                    <option value="draft">Save as Draft</option>
                    <option value="publish">Publish Now</option>
                    <option value="future">Schedule for Later</option>
                </select>
            </div>
            <div id="wp-schedule-date-row" style="display:none">
                <label class="block text-xs font-bold text-zinc-700 mb-1">Publish Date & Time</label>
                <input type="datetime-local" id="wp-schedule-date" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <button onclick="syncToWordPress()" class="w-full bg-zinc-900 hover:bg-zinc-700 text-white font-bold py-2.5 rounded-lg text-sm transition-colors">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="inline mr-1"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>
                Sync to WordPress
            </button>
        </div>

        <!-- Stage Audit Trail -->
        <div class="border border-zinc-200 rounded-xl overflow-hidden">
            <div class="px-4 py-3 bg-zinc-50 border-b border-zinc-200">
                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Stage History</h3>
            </div>
            <div class="p-4 space-y-2 max-h-48 overflow-y-auto" id="approval-stage-log">
                <div class="text-xs text-zinc-400">Loading history...</div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="px-6 py-4 border-t border-zinc-200 bg-zinc-50 flex gap-3 shrink-0">
        <button onclick="processApprovalAction('reject')" class="flex-1 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-800 font-bold py-2.5 rounded-lg text-sm transition-colors">
            Request Revisions
        </button>
        <button onclick="processApprovalAction('approve')" class="flex-1 bg-zinc-900 hover:bg-zinc-700 text-white font-bold py-2.5 rounded-lg text-sm transition-colors">
            Approve & Schedule
        </button>
    </div>
</aside>

<script>
(function() {
    window.openApprovalDrawer = function(itemId) {
        if(typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
        const drawer = document.getElementById('cora-approval-drawer');
        if(!drawer) return;
        drawer.classList.remove('collapsed', 'translate-x-full');
        const bd = document.getElementById('cora-drawer-backdrop');
        if(bd) bd.classList.remove('hidden');
        
        document.getElementById('approval-item-id').value = itemId;
        document.getElementById('approval-item-title').textContent = 'Loading...';
        
        // Load item
        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_get_content_item',
            nonce: coraREWPData.ajaxNonce,
            item_id: itemId
        }, function(r) {
            if(!r.success) return;
            const d = r.data;
            document.getElementById('approval-item-title').textContent = d.title || '';
            document.getElementById('approval-item-stage').textContent = 'Stage: ' + (d.stage || '').replace(/_/g,' ');
            document.getElementById('approval-item-seo').textContent = 'SEO: ' + (d.seo_score || 0) + '/100';
            if(d.post_id) document.getElementById('approval-post-id').value = d.post_id;
            
            // Show WP publish section if approved
            if(d.stage === 'approval' || d.stage === 'scheduled' || d.stage === 'published') {
                document.getElementById('wp-publish-section').style.display = '';
            }
            
            // Load stage log
            if(d.stage_log) renderStageLog(d.stage_log);
        });
    };

    window.closeApprovalDrawer = function() {
        if(typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    };

    // Schedule date visibility
    document.addEventListener('change', function(e) {
        if(e.target.id === 'wp-publish-status') {
            document.getElementById('wp-schedule-date-row').style.display = e.target.value === 'future' ? '' : 'none';
        }
    });

    window.runApprovalSEOCheck = function() {
        const itemId = document.getElementById('approval-item-id').value;
        const postId = document.getElementById('approval-post-id').value;
        if(!postId) { window.coraShowToast('No linked WP post to analyze', 'error'); return; }
        
        const list = document.getElementById('approval-seo-checklist');
        list.innerHTML = '<div class="text-xs text-zinc-500 animate-pulse">Running SEO analysis...</div>';
        
        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_run_seo_analysis',
            nonce: coraREWPData.ajaxNonce,
            post_id: postId
        }, function(r) {
            if(!r.success) { list.innerHTML = '<div class="text-xs text-red-500">Analysis failed.</div>'; return; }
            const d = r.data;
            const score = d.overall_score || 0;
            const statusIcon = (s) => {
                if(s === 'pass') return '<svg viewBox="0 0 24 24" width="12" height="12" stroke="#16a34a" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                if(s === 'fail') return '<svg viewBox="0 0 24 24" width="12" height="12" stroke="#dc2626" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
                return '<svg viewBox="0 0 24 24" width="12" height="12" stroke="#d97706" stroke-width="2.5" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path></svg>';
            };
            const labels = {word_count:'Word Count ≥1000',keyword_in_h1:'Keyword in H1',h2_present:'H2 Headings',internal_links:'Internal Links',images_alt:'Image Alt Text',meta_title_len:'Meta Title (50-60 chars)',meta_desc_len:'Meta Description',slug_clean:'Clean Slug',has_faq:'FAQ Section',has_schema:'JSON-LD Schema',has_stats:'Statistics Cited'};
            let html = `<div class="flex items-center gap-2 mb-3"><div class="text-2xl font-bold text-zinc-900">${score}</div><div class="text-xs text-zinc-500">/100 SEO Score</div><div class="ml-auto text-xs font-bold ${score >= 85 ? 'text-zinc-900' : 'text-zinc-500'}">${score >= 85 ? '✓ Gate Passed' : '⚠ Below 85 threshold'}</div></div><div class="space-y-1.5">`;
            Object.entries(d.checks || {}).forEach(([k, v]) => {
                html += `<div class="flex items-center gap-2 text-xs text-zinc-700">${statusIcon(v)}<span>${labels[k] || k}</span></div>`;
            });
            html += '</div>';
            list.innerHTML = html;
        });
    };

    window.processApprovalAction = function(decision) {
        const itemId = document.getElementById('approval-item-id').value;
        if(!itemId) return;
        const notes = document.getElementById('approval-notes').value;
        const compFH = document.getElementById('compliance-fair-housing').checked;
        const compMR = document.getElementById('compliance-media-release').checked;
        const compFC = document.getElementById('compliance-factcheck').checked;
        
        if(decision === 'approve' && (!compFH || !compMR || !compFC)) {
            window.coraShowToast('Please confirm all compliance checks before approving.', 'error');
            return;
        }
        
        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_process_approval_action',
            nonce: coraREWPData.ajaxNonce,
            item_id: itemId,
            decision: decision,
            notes: notes,
            compliance_fair_housing: compFH ? 1 : 0,
            compliance_media_release: compMR ? 1 : 0,
            compliance_factcheck: compFC ? 1 : 0
        }, function(r) {
            if(r.success) {
                const msg = decision === 'approve' ? 'Content approved!' : 'Revisions requested. Writer notified.';
                window.coraShowToast(msg, 'success');
                if(decision === 'approve') {
                    document.getElementById('wp-publish-section').style.display = '';
                }
                if(typeof window.loadContentWorkspace === 'function') window.loadContentWorkspace();
            } else {
                window.coraShowToast(r.data || 'Action failed', 'error');
            }
        });
    };

    window.syncToWordPress = function() {
        const itemId = document.getElementById('approval-item-id').value;
        const postType = document.getElementById('wp-post-type').value;
        const status = document.getElementById('wp-publish-status').value;
        const schedDate = document.getElementById('wp-schedule-date').value;
        
        window.coraShowToast('Syncing to WordPress...', 'info');
        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_sync_to_wordpress',
            nonce: coraREWPData.ajaxNonce,
            item_id: itemId,
            wp_post_type: postType,
            publish_status: status,
            publish_timestamp: schedDate
        }, function(r) {
            if(r.success) {
                window.coraShowToast('Successfully published to WordPress!', 'success');
                if(r.data.permalink) {
                    window.open(r.data.permalink, '_blank');
                }
            } else {
                window.coraShowToast(r.data || 'WordPress sync failed', 'error');
            }
        });
    };

    function renderStageLog(log) {
        const container = document.getElementById('approval-stage-log');
        if(!container || !log.length) { if(container) container.innerHTML = '<div class="text-xs text-zinc-400">No stage history yet.</div>'; return; }
        container.innerHTML = log.map(entry => `
            <div class="flex items-start gap-2 text-xs">
                <div class="w-1 h-1 rounded-full bg-zinc-400 mt-1.5 shrink-0"></div>
                <div>
                    <span class="font-bold text-zinc-700">${(entry.from_stage || 'Start').replace(/_/g,' ')} → ${(entry.to_stage || '').replace(/_/g,' ')}</span>
                    ${entry.notes ? `<span class="text-zinc-500"> · ${entry.notes}</span>` : ''}
                    <div class="text-zinc-400 text-[10px]">${entry.changed_at || ''} by ${entry.changed_by_name || 'System'}</div>
                </div>
            </div>
        `).join('');
    }
})();
</script>
