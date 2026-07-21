<aside id="cora-content-brief-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[600px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="px-6 py-4 border-b border-zinc-200 flex justify-between items-center bg-zinc-50">
        <div>
            <h2 class="text-lg font-bold text-zinc-900">Content Brief</h2>
            <p class="text-xs text-zinc-500">Define strategy, outline, and assets.</p>
        </div>
        <button class="text-zinc-400 hover:text-zinc-900 cursor-pointer" onclick="closeContentBriefDrawer()">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Inner Tabs -->
    <div class="px-6 pt-4 border-b border-zinc-200 flex gap-4 text-sm font-semibold">
        <button class="pb-2 border-b-2 border-zinc-900 text-zinc-900">Strategy</button>
        <button class="pb-2 border-b-2 border-transparent text-zinc-500 hover:text-zinc-900">Outline</button>
        <button class="pb-2 border-b-2 border-transparent text-zinc-500 hover:text-zinc-900">Assets</button>
        <button class="pb-2 border-b-2 border-transparent text-zinc-500 hover:text-zinc-900">Settings</button>
        <button class="pb-2 border-b-2 border-transparent text-zinc-500 hover:text-zinc-900">Comments</button>
    </div>

    <div class="flex-1 overflow-y-auto p-6 space-y-6">
        <input type="hidden" id="cb-item-id">
        
        <!-- Strategy Tab Content (Default visible) -->
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-zinc-700 mb-1">Title</label>
                <input type="text" id="cb-title" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-700 mb-1">Primary Keyword</label>
                    <input type="text" id="cb-keyword" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-700 mb-1">Target Word Count</label>
                    <input type="number" id="cb-word-count" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm" value="1000">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-700 mb-1">Secondary Keywords</label>
                <input type="text" id="cb-secondary-keywords" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm" placeholder="Comma separated...">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-700 mb-1">Audience Persona</label>
                <input type="text" id="cb-persona" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm" placeholder="e.g. First-time homebuyers, High-net-worth investors...">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-700 mb-1">Writer</label>
                    <select id="cb-writer" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
                        <option value="">Unassigned</option>
                        <?php if (isset($cora_users)) { foreach($cora_users as $u): ?>
                            <option value="<?php echo esc_attr($u->ID); ?>"><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; } ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-700 mb-1">Editor / Approver</label>
                    <select id="cb-editor" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
                        <option value="">Unassigned</option>
                        <?php if (isset($cora_users)) { foreach($cora_users as $u): ?>
                            <option value="<?php echo esc_attr($u->ID); ?>"><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; } ?>
                    </select>
                </div>
                <div class="hidden">
                    <select id="cb-approver" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm"></select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-700 mb-1">Draft Due Date</label>
                    <input type="date" id="cb-draft-due" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-700 mb-1">Publish Date</label>
                    <input type="date" id="cb-publish-date" class="w-full border border-zinc-200 rounded px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="mt-8 pt-4 border-t border-zinc-200">
            <h3 class="text-sm font-bold text-zinc-900 mb-3">Feedback &amp; Comments</h3>
            <div id="cb-comments-list" class="space-y-3 mb-3">
                <div class="text-xs text-zinc-400 py-2">No comments yet.</div>
            </div>
            <div class="flex gap-2">
                <input type="text" id="cb-comment-input" class="flex-1 border border-zinc-200 rounded px-3 py-1.5 text-sm" placeholder="Add a comment...">
                <button class="bg-zinc-900 text-white px-3 py-1.5 rounded text-sm font-bold" onclick="submitComment(document.getElementById('cb-item-id').value)">Send</button>
            </div>
        </div>

    </div>

    <div class="p-4 border-t border-zinc-200 bg-zinc-50">
        <div class="flex gap-2 overflow-x-auto pb-3 mb-3">
            <?php
            $cb_stages = [
                'idea' => 'Idea', 'briefing' => 'Briefing', 'research' => 'Research',
                'drafting' => 'Drafting', 'editorial_review' => 'Editorial Review',
                'revisions' => 'Revisions', 'seo_gate' => 'SEO Gate',
                'approval' => 'Approval', 'scheduled' => 'Scheduled',
                'published' => 'Published', 'performance' => 'Performance'
            ];
            foreach($cb_stages as $k => $v):
            ?>
                <button class="cb-stage-pill px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0 bg-zinc-100 text-zinc-600 hover:bg-zinc-200" data-stage="<?php echo $k; ?>" onclick="moveToStage(document.getElementById('cb-item-id').value, '<?php echo $k; ?>')"><?php echo $v; ?></button>
            <?php endforeach; ?>
        </div>
        <div class="flex gap-3">
            <button class="flex-1 border border-zinc-300 text-zinc-800 font-bold py-2 rounded hover:bg-zinc-100 transition-colors" onclick="closeContentBriefDrawer()">Cancel</button>
            <button class="flex-1 bg-zinc-900 text-white font-bold py-2 rounded hover:bg-zinc-800 transition-colors" onclick="saveBriefData(event)">Save Brief</button>
        </div>
    </div>
</aside>
