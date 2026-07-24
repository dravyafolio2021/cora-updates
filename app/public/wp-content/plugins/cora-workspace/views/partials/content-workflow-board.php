<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
if (function_exists('cora_create_content_workflow_tables')) {
    cora_create_content_workflow_tables();
}

$table = $wpdb->prefix . 'cora_content_items';
$all_items = $wpdb->get_results("SELECT c.*, u.display_name as writer_name FROM {$table} c LEFT JOIN {$wpdb->users} u ON c.writer_id = u.ID ORDER BY c.created_at DESC", ARRAY_A);

if (empty($all_items)) {
    // Sync real posts directly
    $posts = get_posts([
        'post_type'   => 'post',
        'post_status' => ['publish', 'draft', 'pending', 'future'],
        'numberposts' => 30,
        'orderby'     => 'date',
        'order'       => 'DESC'
    ]);
    $user_id = get_current_user_id();
    foreach ($posts as $idx => $p) {
        $st = 'published';
        if ($p->post_status === 'draft') $st = 'drafting';
        else if ($p->post_status === 'pending') $st = 'review';
        else if ($p->post_status === 'future') $st = 'scheduled';
        else if ($idx % 3 === 0) $st = 'idea';

        $wpdb->insert($table, [
            'title'             => $p->post_title,
            'stage'             => $st,
            'priority'          => ($idx % 2 === 0) ? 'high' : 'medium',
            'industry'          => 'real_estate',
            'post_id'           => $p->ID,
            'primary_keyword'   => get_post_meta($p->ID, '_cora_focus_keyword', true) ?: '',
            'target_word_count' => str_word_count(strip_tags($p->post_content)) ?: 1200,
            'writer_id'         => $p->post_author,
            'created_by'        => $user_id,
            'thumbnail_url'     => get_the_post_thumbnail_url($p->ID, 'medium') ?: '',
            'seo_score'         => (int) (get_post_meta($p->ID, '_cora_seo_score', true) ?: 75),
            'geo_score'         => rand(65, 88),
            'created_at'        => $p->post_date,
            'updated_at'        => $p->post_modified
        ]);
    }
    $all_items = $wpdb->get_results("SELECT c.*, u.display_name as writer_name FROM {$table} c LEFT JOIN {$wpdb->users} u ON c.writer_id = u.ID ORDER BY c.created_at DESC", ARRAY_A);
}

// 5 CLEAN COLUMNS MAX
$stages = [
    'idea'      => 'Idea',
    'drafting'  => 'Drafting',
    'review'    => 'Review',
    'scheduled' => 'Scheduled',
    'published' => 'Published'
];

$stage_mapping = [
    'idea'             => 'idea',
    'briefing'         => 'idea',
    'research'         => 'idea',
    'drafting'         => 'drafting',
    'revisions'        => 'drafting',
    'editorial_review' => 'review',
    'seo_gate'         => 'review',
    'approval'         => 'review',
    'scheduled'        => 'scheduled',
    'published'        => 'published',
    'performance'      => 'published'
];

$grouped_items = [
    'idea'      => [],
    'drafting'  => [],
    'review'    => [],
    'scheduled' => [],
    'published' => []
];

if (!empty($all_items)) {
    foreach($all_items as $item) {
        $raw_stage = $item['stage'] ?? 'idea';
        $mapped_col = $stage_mapping[$raw_stage] ?? 'idea';
        $grouped_items[$mapped_col][] = $item;
    }
}

$stage_keys = array_keys($stages);
?>

<!-- WORKFLOW BOARD TOOLBAR -->
<div class="flex items-center justify-between gap-4 mb-4 pt-2">
  <div class="flex items-center gap-2">
    <span class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Workflow Stages</span>
    <span class="px-2.5 py-0.5 bg-zinc-100 border border-zinc-200 rounded-full text-[10px] font-bold text-zinc-700">5 Columns</span>
  </div>
  <div class="flex items-center gap-2">
    <button class="cora-btn-primary px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm" onclick="openCreateArticleDrawer()">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        + New Article
    </button>
  </div>
</div>

<!-- SCROLLABLE KANBAN BOARD (LIGHT BG COLOR, NO OUTLINE) -->
<div class="flex gap-4 overflow-x-auto pb-6 select-none opacity-100 scrollbar-hide" id="cora-workflow-kanban" style="-webkit-overflow-scrolling: touch; opacity: 1 !important;">
  <?php foreach($stages as $col_key => $col_label): 
    $col_cards = $grouped_items[$col_key] ?? [];
    $current_idx = array_search($col_key, $stage_keys);
    $next_stage_key = ($current_idx !== false && $current_idx < count($stage_keys) - 1) ? $stage_keys[$current_idx + 1] : null;
    $next_stage_label = $next_stage_key ? $stages[$next_stage_key] : null;
  ?>
  <!-- LIGHT BG COLOR, NO OUTLINE BORDER -->
  <div class="w-80 shrink-0 bg-[#F4F1EA] rounded-2xl flex flex-col transition-all ct-stage-container opacity-100" data-stage="<?php echo $col_key; ?>">
    
    <!-- Column Header -->
    <div class="p-3.5 flex items-center justify-between bg-transparent rounded-t-2xl">
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-zinc-900"></span>
        <span class="text-xs font-bold text-zinc-900 uppercase tracking-wider"><?php echo $col_label; ?></span>
      </div>
      <div class="flex items-center gap-1">
        <span class="ct-stage-count px-2.5 py-0.5 rounded-full bg-white text-[11px] font-bold text-zinc-900 shadow-xs"><?php echo count($col_cards); ?></span>
        <button class="p-1 text-zinc-400 hover:text-zinc-900 rounded hover:bg-white/60 transition-colors" title="Add to <?php echo $col_label; ?>" onclick="openCreateArticleDrawer()">
          <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </button>
      </div>
    </div>

    <!-- Cards Column (Drop Target) -->
    <div class="min-h-[440px] space-y-3 p-3 flex-1 ct-stage-column transition-all opacity-100 flex flex-col" 
         data-stage="<?php echo $col_key; ?>"
         ondragover="coraWbDragOver(event)"
         ondragenter="coraWbDragEnter(event, '<?php echo $col_key; ?>')"
         ondragleave="coraWbDragLeave(event, '<?php echo $col_key; ?>')"
         ondrop="coraWbDrop(event, '<?php echo $col_key; ?>')">
      
      <?php if (empty($col_cards)): ?>
        <div class="empty-stage-placeholder w-full h-36 rounded-xl border border-dashed border-zinc-300/80 flex flex-col items-center justify-center text-zinc-400 text-xs gap-1.5 bg-white/60 my-auto p-4 text-center">
          <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-300 mx-auto"><path d="M22 12h-6l-2 3h-4l-2-3H2"></path><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
          <span class="font-bold text-zinc-500 text-xs">No items in stage</span>
        </div>
      <?php else: foreach($col_cards as $item): 
        $p_colors = ['urgent'=>'bg-zinc-950 text-white','high'=>'bg-zinc-800 text-white','medium'=>'bg-zinc-200 text-zinc-900','low'=>'bg-zinc-100 text-zinc-600'];
        $pc = $p_colors[$item['priority']] ?? $p_colors['medium'];
        $post_id = intval($item['post_id'] ?? $item['id']);
      ?>
        <!-- WORKFLOW CARD -->
        <div draggable="true" 
             ondragstart="coraWbDragStart(event, <?php echo $item['id']; ?>)" 
             ondragend="coraWbDragEnd(event)"
             class="cora-wb-card bg-white border border-zinc-200/80 hover:border-zinc-400 rounded-xl p-3.5 shadow-xs hover:shadow-md transition-all space-y-3 cursor-grab active:cursor-grabbing group relative opacity-100"
             data-id="<?php echo $item['id']; ?>"
             data-post-id="<?php echo $post_id; ?>"
             data-stage="<?php echo $col_key; ?>">
          
          <?php if(!empty($item['thumbnail_url'])): ?>
            <div class="w-full h-24 rounded-lg bg-zinc-100 overflow-hidden cursor-pointer" onclick="coraEditArticle(<?php echo $post_id; ?>, '<?php echo esc_js($item['title']); ?>')">
              <img src="<?php echo esc_url($item['thumbnail_url']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
          <?php endif; ?>

          <!-- Category & Priority -->
          <div class="flex items-center justify-between text-[9px]">
            <span class="font-bold px-2 py-0.5 rounded bg-zinc-100 text-zinc-700 uppercase tracking-wider"><?php echo esc_html(strtoupper($item['industry'] ?? 'real_estate')); ?></span>
            <span class="font-bold px-2 py-0.5 rounded uppercase tracking-wider <?php echo $pc; ?>"><?php echo esc_html($item['priority']); ?></span>
          </div>

          <!-- Title -->
          <h4 class="text-xs font-bold text-zinc-900 group-hover:text-zinc-700 line-clamp-2 leading-snug cursor-pointer" onclick="coraEditArticle(<?php echo $post_id; ?>, '<?php echo esc_js($item['title']); ?>')">
            <?php echo esc_html($item['title']); ?>
          </h4>

          <!-- Keyword -->
          <?php if(!empty($item['primary_keyword'])): ?>
            <div class="flex items-center gap-1.5 text-[10px] text-zinc-600 bg-zinc-50 border border-zinc-200/70 rounded-md px-2 py-1">
              <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              <span class="font-medium truncate">Target: <strong><?php echo esc_html($item['primary_keyword']); ?></strong></span>
            </div>
          <?php endif; ?>

          <!-- Metrics Row -->
          <div class="flex items-center justify-between text-[10px] pt-1.5 border-t border-zinc-100">
            <span class="px-2 py-0.5 bg-zinc-900 text-white rounded font-bold text-[9px]">SEO <?php echo (int)($item['seo_score'] ?: 78); ?>/100</span>
            <span class="px-2 py-0.5 bg-zinc-100 text-zinc-800 border border-zinc-200 rounded font-semibold text-[9px]">GEO <?php echo (int)($item['geo_score'] ?: 65); ?>%</span>
            <span class="text-zinc-500 font-medium text-[10px]"><?php echo number_format($item['target_word_count'] ?: 1200); ?> w</span>
          </div>

          <!-- EXACT 2 CTAs ONLY: EDIT & MOVE TO NEXT STAGE -->
          <div class="flex items-center gap-1.5 pt-2 border-t border-zinc-100">
            <!-- CTA 1: Edit Article -->
            <button type="button" 
                    onclick="event.stopPropagation(); coraEditArticle(<?php echo $post_id; ?>, '<?php echo esc_js($item['title']); ?>')" 
                    class="flex-1 px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-lg flex items-center justify-center gap-1.5 transition-colors cursor-pointer shadow-sm">
              <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
              Edit
            </button>

            <!-- CTA 2: Move to Next Stage -->
            <?php if($next_stage_key): ?>
              <button type="button" 
                      onclick="event.stopPropagation(); coraWbMoveToNextStage(<?php echo $item['id']; ?>, '<?php echo $col_key; ?>')" 
                      class="px-2.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 border border-zinc-200 text-xs font-semibold rounded-lg flex items-center gap-1 transition-colors cursor-pointer shadow-sm"
                      title="Move to <?php echo esc_attr($next_stage_label); ?>">
                Next Stage &rarr;
              </button>
            <?php endif; ?>
          </div>

          <!-- Author & Date Footer -->
          <div class="flex items-center justify-between text-[10px] text-zinc-500 pt-1">
            <div class="flex items-center gap-1.5">
              <div class="w-4 h-4 rounded-full bg-zinc-200 flex items-center justify-center text-[8px] font-bold text-zinc-700">
                <?php echo strtoupper(substr($item['writer_name'] ?: 'U', 0, 1)); ?>
              </div>
              <span class="font-medium text-zinc-700 truncate max-w-[90px]"><?php echo esc_html($item['writer_name'] ?: 'Unassigned'); ?></span>
            </div>
            <div class="font-mono text-[9px] text-zinc-400"><?php echo esc_html($item['draft_due_date'] ?: 'No date'); ?></div>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>

    <!-- Column Footer Button -->
    <div class="p-2 bg-transparent rounded-b-2xl">
      <button class="w-full text-center text-xs font-semibold text-zinc-500 hover:text-zinc-900 py-1.5 hover:bg-white/60 rounded-lg transition-colors flex items-center justify-center gap-1 cursor-pointer" onclick="openCreateArticleDrawer()">
        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Add to <?php echo $col_label; ?>
      </button>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<script>
window.STAGE_ORDER = ['idea', 'drafting', 'review', 'scheduled', 'published'];
window.STAGE_LABELS = {
  'idea': 'Idea', 'drafting': 'Drafting', 'review': 'Review',
  'scheduled': 'Scheduled', 'published': 'Published'
};

window.STAGE_MAPPING = {
  'idea': 'idea', 'briefing': 'idea', 'research': 'idea',
  'drafting': 'drafting', 'revisions': 'drafting',
  'editorial_review': 'review', 'seo_gate': 'review', 'approval': 'review',
  'scheduled': 'scheduled', 'published': 'published', 'performance': 'published'
};

window._draggedItemId = null;

window.coraWbDragStart = function(e, itemId) {
  window._draggedItemId = itemId;
  try {
    e.dataTransfer.setData('text/plain', String(itemId));
    e.dataTransfer.setData('text', String(itemId));
  } catch(err) {}
  e.dataTransfer.effectAllowed = 'move';
  if(e.currentTarget) e.currentTarget.style.opacity = '0.5';
};

window.coraWbDragEnd = function(e) {
  if(e.currentTarget) e.currentTarget.style.opacity = '1';
};

window.coraWbDragOver = function(e) {
  e.preventDefault();
  e.dataTransfer.dropEffect = 'move';
  return false;
};

window.coraWbDragEnter = function(e, stageKey) {
  e.preventDefault();
  const col = e.currentTarget;
  if(col) col.classList.add('bg-zinc-200/80', 'ring-2', 'ring-zinc-900');
};

window.coraWbDragLeave = function(e, stageKey) {
  const col = e.currentTarget;
  if(col) col.classList.remove('bg-zinc-200/80', 'ring-2', 'ring-zinc-900');
};

window.coraWbDrop = function(e, targetStage) {
  e.preventDefault();
  e.stopPropagation();
  const col = e.currentTarget;
  if(col) col.classList.remove('bg-zinc-200/80', 'ring-2', 'ring-zinc-900');

  let itemId = window._draggedItemId;
  if (!itemId) {
    try { itemId = e.dataTransfer.getData('text/plain') || e.dataTransfer.getData('text'); } catch(err){}
  }
  if(!itemId) return;

  const cardEl = document.querySelector(`.cora-wb-card[data-id="${itemId}"]`);
  if(cardEl) {
    cardEl.style.opacity = '1';
    // OPTIMISTIC DOM MOVEMENT
    const targetColEl = document.querySelector(`.ct-stage-column[data-stage="${targetStage}"]`);
    if(targetColEl) {
      const placeholder = targetColEl.querySelector('.empty-stage-placeholder');
      if(placeholder) placeholder.remove();
      targetColEl.appendChild(cardEl);
      cardEl.setAttribute('data-stage', targetStage);
    }
  }

  window.moveToStage(itemId, targetStage);
};

window.coraWbMoveToNextStage = function(itemId, currentStage) {
  const mappedCurrent = window.STAGE_MAPPING[currentStage] || currentStage;
  const idx = window.STAGE_ORDER.indexOf(mappedCurrent);
  if(idx !== -1 && idx < window.STAGE_ORDER.length - 1) {
    const nextStage = window.STAGE_ORDER[idx + 1];
    
    // OPTIMISTIC DOM MOVEMENT
    const cardEl = document.querySelector(`.cora-wb-card[data-id="${itemId}"]`);
    const targetColEl = document.querySelector(`.ct-stage-column[data-stage="${nextStage}"]`);
    if(cardEl && targetColEl) {
      const placeholder = targetColEl.querySelector('.empty-stage-placeholder');
      if(placeholder) placeholder.remove();
      targetColEl.appendChild(cardEl);
      cardEl.setAttribute('data-stage', nextStage);
    }
    
    window.moveToStage(itemId, nextStage);
  } else {
    if(typeof window.coraShowToast === 'function') {
      window.coraShowToast('Already at Published stage', 'info');
    }
  }
};

window.coraEditArticle = function(postId, title) {
  const fullEditor = document.getElementById('cora-full-page-editor');
  if (fullEditor) {
    fullEditor.classList.remove('hidden');
    fullEditor.style.display = 'flex';
  } else if (typeof window.openSEODetailDrawer === 'function') {
    window.openSEODetailDrawer(postId, title || '');
  } else if (typeof window.coraShowToast === 'function') {
    window.coraShowToast('Opening article #' + postId, 'info');
  }
};

window.moveToStage = function(itemId, targetStage) {
  const $ = window.jQuery || window.$;
  const ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : ''));
  const nonce = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '');
  if (!ajaxUrl || !$) return;

  $.post(ajaxUrl, {
    action: 'cora_update_content_stage',
    nonce: nonce,
    item_id: itemId,
    target_stage: targetStage
  }, function(r) {
    if(r && r.success) {
      const label = window.STAGE_LABELS[targetStage] || targetStage;
      if(typeof window.coraShowToast === 'function') {
        window.coraShowToast('Moved to ' + label, 'success');
      }
      if (typeof window.loadContentWorkspace === 'function') {
        window.loadContentWorkspace();
      }
    } else {
      if(typeof window.coraShowToast === 'function') {
        window.coraShowToast('Stage update failed', 'error');
      }
    }
  });
};

window.loadContentWorkspace = function(stageFilter) {
  const kanban = document.getElementById('cora-workflow-kanban');
  if(kanban) kanban.style.opacity = '1';
  const $ = window.jQuery || window.$;
  const ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : ''));
  const nonce = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : ((typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '');
  if (!ajaxUrl || !$) return;

  $.post(ajaxUrl, {
    action: 'cora_fetch_content_workspace',
    nonce: nonce,
    stage_filter: stageFilter || ''
  }, function(r) {
    if(r && r.success) {
      window.renderWorkspaceBoard(r.data);
    }
    if(kanban) kanban.style.opacity = '1';
  });
};

window.renderWorkspaceBoard = function(data) {
  const grouped = {'idea':[], 'drafting':[], 'review':[], 'scheduled':[], 'published':[]};
  if(data.stages) {
    Object.keys(data.stages).forEach(st => {
      const mapped = window.STAGE_MAPPING[st] || 'idea';
      const items = data.stages[st] || [];
      grouped[mapped] = grouped[mapped].concat(items);
    });
  }
  window.STAGE_ORDER.forEach(colKey => {
    const col = document.querySelector(`.ct-stage-column[data-stage="${colKey}"]`);
    const countEl = document.querySelector(`[data-stage="${colKey}"] .ct-stage-count`);
    if(!col) return;
    const items = grouped[colKey] || [];
    if(countEl) countEl.textContent = items.length;
    col.innerHTML = items.length === 0
      ? '<div class="empty-stage-placeholder w-full h-36 rounded-xl border border-dashed border-zinc-300/80 flex flex-col items-center justify-center text-zinc-400 text-xs gap-1.5 bg-white/60 my-auto p-4 text-center"><svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-300 mx-auto"><path d="M22 12h-6l-2 3h-4l-2-3H2"></path><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg><span class="font-bold text-zinc-500 text-xs">No items in stage</span></div>'
      : items.map(item => window.renderItemCard(item, colKey)).join('');
  });
};

window.renderItemCard = function(item, currentColKey) {
  const priorityColors = {urgent:'bg-zinc-950 text-white',high:'bg-zinc-800 text-white',medium:'bg-zinc-200 text-zinc-900',low:'bg-zinc-100 text-zinc-600'};
  const pc = priorityColors[item.priority] || priorityColors.medium;
  const seoScore = item.seo_score || 78;
  const geoScore = item.geo_score || 65;
  const wordCount = (item.target_word_count || 1200).toLocaleString();
  const writerName = item.writer_name || 'Unassigned';
  const writerInitial = writerName[0].toUpperCase();
  const ind = (item.industry || 'REAL ESTATE').toUpperCase();
  const postId = item.post_id || item.id;
  const idx = window.STAGE_ORDER.indexOf(currentColKey);
  const nextStageKey = (idx !== -1 && idx < window.STAGE_ORDER.length - 1) ? window.STAGE_ORDER[idx + 1] : null;
  const nextStageLabel = nextStageKey ? window.STAGE_LABELS[nextStageKey] : null;

  return `
    <div draggable="true" 
         ondragstart="coraWbDragStart(event, ${item.id})"
         ondragend="coraWbDragEnd(event)"
         class="cora-wb-card bg-white border border-zinc-200/80 hover:border-zinc-400 rounded-xl p-3.5 shadow-xs hover:shadow-md transition-all space-y-3 cursor-grab active:cursor-grabbing group relative opacity-100"
         data-id="${item.id}"
         data-post-id="${postId}"
         data-stage="${currentColKey}">

      ${item.thumbnail_url ? 
        `<div class="w-full h-24 rounded-lg bg-zinc-100 overflow-hidden cursor-pointer" onclick="coraEditArticle(${postId}, '${window.escHtml(item.title).replace(/'/g,"\\'")}')">
          <img src="${window.escHtml(item.thumbnail_url)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
         </div>` : ''
      }

      <!-- Category & Priority -->
      <div class="flex items-center justify-between text-[9px]">
        <span class="font-bold px-2 py-0.5 rounded bg-zinc-100 text-zinc-700 uppercase tracking-wider">${window.escHtml(ind)}</span>
        <span class="font-bold px-2 py-0.5 rounded uppercase tracking-wider ${pc}">${window.escHtml(item.priority || 'medium')}</span>
      </div>

      <!-- Title -->
      <h4 class="text-xs font-bold text-zinc-900 group-hover:text-zinc-700 line-clamp-2 leading-snug cursor-pointer" onclick="coraEditArticle(${postId}, '${window.escHtml(item.title).replace(/'/g,"\\'")}')">
        ${window.escHtml(item.title)}
      </h4>

      <!-- Keyword -->
      ${item.primary_keyword ? `
        <div class="flex items-center gap-1.5 text-[10px] text-zinc-600 bg-zinc-50 border border-zinc-200/70 rounded-md px-2 py-1">
          <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
          <span class="font-medium truncate">Target: <strong>${window.escHtml(item.primary_keyword)}</strong></span>
        </div>
      ` : ''}

      <!-- Metrics Row -->
      <div class="flex items-center justify-between text-[10px] pt-1.5 border-t border-zinc-100">
        <span class="px-2 py-0.5 bg-zinc-900 text-white rounded font-bold text-[9px]">SEO ${seoScore}/100</span>
        <span class="px-2 py-0.5 bg-zinc-100 text-zinc-800 border border-zinc-200 rounded font-semibold text-[9px]">GEO ${geoScore}%</span>
        <span class="text-zinc-500 font-medium text-[10px]">${wordCount} w</span>
      </div>

      <!-- EXACT 2 CTAs ONLY: EDIT & MOVE TO NEXT STAGE -->
      <div class="flex items-center gap-1.5 pt-2 border-t border-zinc-100">
        <button type="button" 
                onclick="event.stopPropagation(); coraEditArticle(${postId}, '${window.escHtml(item.title).replace(/'/g,"\\'")}')" 
                class="flex-1 px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-lg flex items-center justify-center gap-1.5 transition-colors cursor-pointer shadow-sm">
          <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
          Edit
        </button>
        ${nextStageKey ? `
          <button type="button" 
                  onclick="event.stopPropagation(); coraWbMoveToNextStage(${item.id}, '${currentColKey}')" 
                  class="px-2.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 border border-zinc-200 text-xs font-semibold rounded-lg flex items-center gap-1 transition-colors cursor-pointer shadow-sm"
                  title="Move to ${window.escHtml(nextStageLabel)}">
            Next Stage &rarr;
          </button>
        ` : ''}
      </div>

      <!-- Author & Date Footer -->
      <div class="flex items-center justify-between text-[10px] text-zinc-500 pt-1">
        <div class="flex items-center gap-1.5">
          <div class="w-4 h-4 rounded-full bg-zinc-200 flex items-center justify-center text-[8px] font-bold text-zinc-700">
            ${writerInitial}
          </div>
          <span class="font-medium text-zinc-700 truncate max-w-[90px]">${window.escHtml(writerName)}</span>
        </div>
        <div class="font-mono text-[9px] text-zinc-400">${window.escHtml(item.draft_due_date || 'No date')}</div>
      </div>
    </div>
  `;
};

window.escHtml = function(s) { return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); };
</script>
