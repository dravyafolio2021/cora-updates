<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
if (function_exists('cora_create_content_workflow_tables')) {
    cora_create_content_workflow_tables();
}

$table = $wpdb->prefix . 'cora_content_items';
$count_items = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
if ($count_items === 0) {
    $user_id = get_current_user_id();
    $sample_items = [
        ['title' => 'Corporate Commercial Lease Space Rates inside DLF CyberCity Gurgaon', 'stage' => 'editorial_review', 'priority' => 'urgent', 'primary_keyword' => 'commercial lease rates cyber city', 'draft_due_date' => date('Y-m-d', strtotime('+3 days'))],
        ['title' => 'Luxury Villa Market Report 2026: Golf Course Extension Road', 'stage' => 'drafting', 'priority' => 'high', 'primary_keyword' => 'luxury villas gurgaon', 'draft_due_date' => date('Y-m-d', strtotime('+5 days'))],
        ['title' => 'Complete Guide to Builder Floor Registration & Stamp Duty', 'stage' => 'briefing', 'priority' => 'medium', 'primary_keyword' => 'builder floor stamp duty', 'draft_due_date' => date('Y-m-d', strtotime('+7 days'))],
        ['title' => 'Top 10 Architectural Photography Lighting Rigs for Interior Shoots', 'stage' => 'idea', 'priority' => 'low', 'primary_keyword' => 'interior photography lighting', 'draft_due_date' => date('Y-m-d', strtotime('+12 days'))],
        ['title' => 'High-Yield Commercial Real Estate Investment Opportunities in Aerocity', 'stage' => 'published', 'priority' => 'high', 'primary_keyword' => 'commercial real estate aerocity', 'draft_due_date' => date('Y-m-d', strtotime('-2 days'))]
    ];
    foreach($sample_items as $si) {
        $wpdb->insert($table, array_merge($si, [
            'industry' => 'real_estate',
            'target_word_count' => 1200,
            'writer_id' => $user_id,
            'created_by' => $user_id,
            'created_at' => current_time('mysql')
        ]));
    }
}

$all_items = $wpdb->get_results("SELECT c.*, u.display_name as writer_name FROM {$table} c LEFT JOIN {$wpdb->users} u ON c.writer_id = u.ID ORDER BY c.created_at DESC", ARRAY_A);
$grouped_items = [];
foreach($all_items as $item) {
    $grouped_items[$item['stage']][] = $item;
}

$stages = [
    'idea' => 'Idea',
    'briefing' => 'Briefing',
    'research' => 'Research',
    'drafting' => 'Drafting',
    'editorial_review' => 'Editorial Review',
    'revisions' => 'Revisions',
    'seo_gate' => 'SEO Gate',
    'approval' => 'Approval',
    'scheduled' => 'Scheduled',
    'published' => 'Published',
    'performance' => 'Performance'
];
?>

<div class="flex items-center gap-3 mb-4">
  <button class="cora-btn-primary px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-md text-sm transition-colors cursor-pointer flex items-center gap-2 ml-auto" onclick="openContentBriefDrawer()">
      <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      New Content Brief
  </button>
</div>

<div class="flex gap-4 overflow-x-auto pb-4" id="cora-workflow-kanban">
  <?php foreach($stages as $stage_key => $stage_label): 
    $stage_cards = $grouped_items[$stage_key] ?? [];
  ?>
  <div class="w-72 shrink-0 bg-zinc-50 border border-zinc-200 rounded-xl flex flex-col" data-stage="<?php echo $stage_key; ?>">
    <div class="p-3 border-b border-zinc-200 flex items-center justify-between">
      <span class="text-xs font-bold text-zinc-700 uppercase tracking-wider"><?php echo $stage_label; ?></span>
      <span class="ct-stage-count text-[10px] font-bold text-zinc-400"><?php echo count($stage_cards); ?></span>
    </div>
    <div class="p-2 space-y-2 overflow-y-auto flex-1 ct-stage-column" data-stage="<?php echo $stage_key; ?>" style="max-height:560px">
      <?php if (empty($stage_cards)): ?>
        <div class="text-center text-zinc-400 text-xs py-6">No items</div>
      <?php else: foreach($stage_cards as $item): 
        $p_colors = ['urgent'=>'bg-zinc-900 text-white','high'=>'bg-zinc-700 text-white','medium'=>'bg-zinc-200 text-zinc-800','low'=>'bg-zinc-100 text-zinc-600'];
        $pc = $p_colors[$item['priority']] ?? $p_colors['medium'];
      ?>
        <div class="bg-white border border-zinc-200 rounded-lg p-3 shadow-sm cursor-pointer hover:border-zinc-400 hover:shadow-md transition-all group" onclick="openContentBriefDrawer(<?php echo $item['id']; ?>)">
          <div class="flex items-start justify-between gap-2 mb-2">
            <div class="text-xs font-bold text-zinc-900 line-clamp-2 flex-1"><?php echo esc_html($item['title']); ?></div>
            <span class="shrink-0 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase <?php echo $pc; ?>"><?php echo esc_html($item['priority']); ?></span>
          </div>
          <?php if(!empty($item['primary_keyword'])): ?>
            <div class="text-[10px] text-zinc-500 mb-2 flex items-center gap-1">
              <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              <?php echo esc_html($item['primary_keyword']); ?>
            </div>
          <?php endif; ?>
          <div class="flex items-center justify-between mt-2">
            <div class="text-[10px] text-zinc-400"><?php echo esc_html($item['draft_due_date'] ?: 'No deadline'); ?></div>
            <?php if(!empty($item['writer_name'])): ?>
              <span class="text-[10px] text-zinc-600 font-medium"><?php echo esc_html($item['writer_name']); ?></span>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
    <div class="p-2 border-t border-zinc-100">
      <button class="w-full text-left text-xs text-zinc-400 hover:text-zinc-700 py-1 flex items-center gap-1 ct-add-item-btn" data-stage="<?php echo $stage_key; ?>" onclick="openContentBriefDrawer()">
        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Add item
      </button>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<script>
(function() {
  // Load workspace data
  window.loadContentWorkspace = function(stageFilter) {
    const kanban = document.getElementById('cora-workflow-kanban');
    if(kanban) kanban.style.opacity = '0.5';
    $.post(coraREWPData.ajaxUrl, {
      action: 'cora_fetch_content_workspace',
      nonce: coraREWPData.ajaxNonce,
      stage_filter: stageFilter || ''
    }, function(r) {
      if(r.success) {
        renderWorkspaceBoard(r.data);
      }
      if(kanban) kanban.style.opacity = '1';
    });
  };

  function renderWorkspaceBoard(data) {
    const stages = ['idea','briefing','research','drafting','editorial_review','revisions','seo_gate','approval','scheduled','published','performance'];
    stages.forEach(stage => {
      const col = document.querySelector(`.ct-stage-column[data-stage="${stage}"]`);
      const countEl = document.querySelector(`[data-stage="${stage}"] .ct-stage-count`);
      if(!col) return;
      const items = data.stages[stage] || [];
      if(countEl) countEl.textContent = items.length;
      col.innerHTML = items.length === 0
        ? '<div class="text-center text-zinc-400 text-xs py-6">No items</div>'
        : items.map(item => renderItemCard(item)).join('');
    });
  }

  function renderItemCard(item) {
    const priorityColors = {urgent:'bg-zinc-900 text-white',high:'bg-zinc-700 text-white',medium:'bg-zinc-200 text-zinc-800',low:'bg-zinc-100 text-zinc-600'};
    const pc = priorityColors[item.priority] || priorityColors.medium;
    return `
      <div class="bg-white border border-zinc-200 rounded-lg p-3 shadow-sm cursor-pointer hover:border-zinc-400 hover:shadow-md transition-all group" onclick="openContentBriefDrawer(${item.id})">
        <div class="flex items-start justify-between gap-2 mb-2">
          <div class="text-xs font-bold text-zinc-900 line-clamp-2 flex-1">${escHtml(item.title)}</div>
          <span class="shrink-0 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase ${pc}">${item.priority}</span>
        </div>
        ${item.primary_keyword ? `<div class="text-[10px] text-zinc-500 mb-2 flex items-center gap-1"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>${escHtml(item.primary_keyword)}</div>` : ''}
        <div class="flex items-center justify-between mt-2">
          <div class="text-[10px] text-zinc-400">${item.draft_due_date || 'No deadline'}</div>
          ${item.writer_name ? `<span class="text-[10px] text-zinc-600 font-medium">${escHtml(item.writer_name)}</span>` : ''}
        </div>
      </div>
    `;
  }

  function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  // Open Content Brief Drawer
  window.openContentBriefDrawer = function(itemId) {
    if(typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    const drawer = document.getElementById('cora-content-brief-drawer');
    if(!drawer) return;
    drawer.classList.remove('collapsed', 'translate-x-full');
    const bd = document.getElementById('cora-drawer-backdrop');
    if(bd) bd.classList.remove('hidden');
    
    if(itemId) {
      // Load item data
      document.getElementById('cb-item-id').value = itemId;
      $.post(coraREWPData.ajaxUrl, {
        action: 'cora_get_content_item',
        nonce: coraREWPData.ajaxNonce,
        item_id: itemId
      }, function(r) {
        if(r.success) populateBriefDrawer(r.data);
      });
    } else {
      // New item
      document.getElementById('cb-item-id').value = '';
      document.getElementById('cb-title').value = '';
      document.getElementById('cb-keyword').value = '';
      document.getElementById('cb-comments-list').innerHTML = '<div class="text-xs text-zinc-400 py-2">No comments yet.</div>';
    }
  };

  window.closeContentBriefDrawer = function() {
    if(typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
  };

  function populateBriefDrawer(data) {
    const set = (id, val) => { const el = document.getElementById(id); if(el) el.value = val || ''; };
    set('cb-title', data.title);
    set('cb-keyword', data.primary_keyword);
    set('cb-secondary-keywords', data.secondary_keywords);
    set('cb-persona', data.audience_persona);
    set('cb-word-count', data.target_word_count);
    set('cb-writer', data.writer_id);
    set('cb-editor', data.editor_id);
    set('cb-approver', data.approver_id);
    set('cb-draft-due', data.draft_due_date);
    set('cb-publish-date', data.publish_date);
    // Highlight current stage
    document.querySelectorAll('.cb-stage-pill').forEach(p => {
      p.classList.toggle('bg-zinc-900', p.dataset.stage === data.stage);
      p.classList.toggle('text-white', p.dataset.stage === data.stage);
      p.classList.toggle('bg-zinc-100', p.dataset.stage !== data.stage);
      p.classList.toggle('text-zinc-600', p.dataset.stage !== data.stage);
    });
    // Load comments
    if(data.comments) renderComments(data.comments);
  }

  window.saveBriefData = function(e) {
    e.preventDefault();
    const itemId = document.getElementById('cb-item-id').value;
    const action = itemId ? 'cora_save_content_brief' : 'cora_create_content_item';
    const payload = {
      action,
      nonce: coraREWPData.ajaxNonce,
      item_id: itemId,
      title: document.getElementById('cb-title').value,
      primary_keyword: document.getElementById('cb-keyword').value,
      secondary_keywords: document.getElementById('cb-secondary-keywords').value,
      audience_persona: document.getElementById('cb-persona').value,
      target_word_count: document.getElementById('cb-word-count').value,
      writer_id: document.getElementById('cb-writer').value,
      editor_id: document.getElementById('cb-editor').value,
      approver_id: document.getElementById('cb-approver').value,
      draft_due_date: document.getElementById('cb-draft-due').value,
      publish_date: document.getElementById('cb-publish-date').value,
      priority: document.querySelector('.cb-priority-btn.active')?.dataset.priority || 'medium'
    };
    $.post(coraREWPData.ajaxUrl, payload, function(r) {
      if(r.success) {
        window.coraShowToast('Content brief saved', 'success');
        window.loadContentWorkspace();
        if(!itemId && r.data.id) document.getElementById('cb-item-id').value = r.data.id;
      } else {
        window.coraShowToast(r.data || 'Save failed', 'error');
      }
    });
  };

  window.moveToStage = function(itemId, targetStage) {
    $.post(coraREWPData.ajaxUrl, {
      action: 'cora_update_content_stage',
      nonce: coraREWPData.ajaxNonce,
      item_id: itemId,
      target_stage: targetStage
    }, function(r) {
      if(r.success) {
        window.coraShowToast('Moved to ' + targetStage.replace(/_/g,' '), 'success');
        window.loadContentWorkspace();
      } else {
        window.coraShowToast('Stage update failed', 'error');
      }
    });
  };

  function renderComments(comments) {
    const container = document.getElementById('cb-comments-list');
    if(!container) return;
    if(!comments || !comments.length) { container.innerHTML = '<div class="text-xs text-zinc-400 py-2">No comments yet.</div>'; return; }
    container.innerHTML = comments.map(c => `
      <div class="flex gap-2 py-2 border-b border-zinc-100">
        <div class="w-6 h-6 rounded-full bg-zinc-200 flex items-center justify-center text-[9px] font-bold text-zinc-700 shrink-0">${(c.author_name||'?')[0].toUpperCase()}</div>
        <div class="flex-1">
          <div class="text-[10px] font-bold text-zinc-700">${escHtml(c.author_name || 'User')}</div>
          <div class="text-xs text-zinc-600 mt-0.5">${escHtml(c.comment)}</div>
          <div class="text-[9px] text-zinc-400 mt-1">${c.created_at || ''}</div>
        </div>
        ${!c.resolved ? `<button onclick="resolveComment(${c.id})" class="text-[9px] text-zinc-400 hover:text-zinc-700 shrink-0">Resolve</button>` : '<span class="text-[9px] text-zinc-300">Resolved</span>'}
      </div>
    `).join('');
  }

  window.submitComment = function(itemId) {
    if(!itemId) {
      window.coraShowToast('Save brief first before commenting.', 'error');
      return;
    }
    const input = document.getElementById('cb-comment-input');
    if(!input || !input.value.trim()) return;
    $.post(coraREWPData.ajaxUrl, {
      action: 'cora_add_content_comment',
      nonce: coraREWPData.ajaxNonce,
      item_id: itemId,
      comment: input.value.trim()
    }, function(r) {
      if(r.success) {
        input.value = '';
        // Reload comments
        $.post(coraREWPData.ajaxUrl, {action:'cora_get_content_item',nonce:coraREWPData.ajaxNonce,item_id:itemId}, function(res) {
          if(res.success) renderComments(res.data.comments);
        });
      }
    });
  };

  window.resolveComment = function(commentId) {
    $.post(coraREWPData.ajaxUrl, {
      action: 'cora_resolve_comment',
      nonce: coraREWPData.ajaxNonce,
      comment_id: commentId
    }, function(r) {
      if(r.success) window.coraShowToast('Comment resolved', 'success');
    });
  };

  // Init
  document.addEventListener('DOMContentLoaded', function() {
    // Load workspace on tab open
    const tab = document.querySelector('[data-tab="ct-workflow"]');
    if(tab) tab.addEventListener('click', function() { window.loadContentWorkspace(); });
  });
})();
</script>
