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
        else if ($p->post_status === 'pending') $st = 'editorial_review';
        else if ($p->post_status === 'future') $st = 'scheduled';
        else if ($idx % 3 === 0) $st = 'idea';
        else if ($idx % 4 === 0) $st = 'briefing';

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

$grouped_items = [];
if (!empty($all_items)) {
    foreach($all_items as $item) {
        $grouped_items[$item['stage']][] = $item;
    }
}

$stages = [
    'idea'             => 'Idea',
    'briefing'         => 'Briefing',
    'research'         => 'Research',
    'drafting'         => 'Drafting',
    'editorial_review' => 'Editorial Review',
    'revisions'        => 'Revisions',
    'seo_gate'         => 'SEO Gate',
    'approval'         => 'Approval',
    'scheduled'        => 'Scheduled',
    'published'        => 'Published',
    'performance'      => 'Performance'
];
?>

<!-- WORKFLOW BOARD TOOLBAR -->
<div class="flex items-center justify-between gap-4 mb-4 pt-2">
  <div class="flex items-center gap-2">
    <span class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Workflow Stages</span>
    <span class="px-2 py-0.5 bg-zinc-100 border border-zinc-200 rounded-full text-[10px] font-bold text-zinc-600"><?php echo count($stages); ?> Columns</span>
  </div>
  <div class="flex items-center gap-2">
    <button class="cora-btn-primary px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm" onclick="openContentBriefDrawer()">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        New Content Brief
    </button>
  </div>
</div>

<!-- KANBAN BOARD CONTAINER (SCROLLABLE) -->
<div class="flex gap-3.5 overflow-x-auto pb-6 scrollbar-hide select-none" id="cora-workflow-kanban" style="-webkit-overflow-scrolling: touch;">
  <?php foreach($stages as $stage_key => $stage_label): 
    $stage_cards = $grouped_items[$stage_key] ?? [];
  ?>
  <div class="w-72 shrink-0 bg-zinc-50/80 border border-zinc-200/80 rounded-2xl flex flex-col transition-all ct-stage-container" data-stage="<?php echo $stage_key; ?>">
    
    <!-- Stage Column Header -->
    <div class="p-3 border-b border-zinc-200/70 flex items-center justify-between bg-white rounded-t-2xl">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-zinc-900"></span>
        <span class="text-xs font-bold text-zinc-900 uppercase tracking-wide"><?php echo $stage_label; ?></span>
      </div>
      <div class="flex items-center gap-1">
        <span class="ct-stage-count px-2 py-0.5 rounded-full bg-zinc-100 text-[10px] font-bold text-zinc-700"><?php echo count($stage_cards); ?></span>
        <button class="p-1 text-zinc-400 hover:text-zinc-900 rounded hover:bg-zinc-100 transition-colors" title="Add brief to <?php echo $stage_label; ?>" onclick="openContentBriefDrawer()">
          <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </button>
      </div>
    </div>

    <!-- Stage Cards Column (Drop Target) -->
    <div class="min-h-[550px] space-y-3 p-2.5 flex-1 ct-stage-column transition-all" 
         data-stage="<?php echo $stage_key; ?>"
         ondragover="coraWbDragOver(event)"
         ondragenter="coraWbDragEnter(event, '<?php echo $stage_key; ?>')"
         ondragleave="coraWbDragLeave(event, '<?php echo $stage_key; ?>')"
         ondrop="coraWbDrop(event, '<?php echo $stage_key; ?>')">
      
      <?php if (empty($stage_cards)): ?>
        <div class="h-32 rounded-xl border border-dashed border-zinc-200 flex flex-col items-center justify-center text-zinc-400 text-xs gap-1">
          <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-300"><rect x="3" y="3" width="18" height="18" rx="2"></rect><polyline points="12 8 12 16"></polyline><polyline points="8 12 16 12"></polyline></svg>
          <span class="font-medium text-[11px]">Drop item here</span>
        </div>
      <?php else: foreach($stage_cards as $item): 
        $p_colors = ['urgent'=>'bg-zinc-950 text-white','high'=>'bg-zinc-800 text-white','medium'=>'bg-zinc-200 text-zinc-900','low'=>'bg-zinc-100 text-zinc-600'];
        $pc = $p_colors[$item['priority']] ?? $p_colors['medium'];
        $post_id = intval($item['post_id'] ?? $item['id']);
      ?>
        <!-- WORKFLOW CARD -->
        <div draggable="true" 
             ondragstart="coraWbDragStart(event, <?php echo $item['id']; ?>)" 
             class="cora-wb-card bg-white border border-zinc-200/90 hover:border-zinc-400 rounded-xl p-3.5 shadow-sm hover:shadow-md transition-all space-y-3 cursor-grab active:cursor-grabbing group relative"
             data-id="<?php echo $item['id']; ?>"
             data-post-id="<?php echo $post_id; ?>">
          
          <?php if(!empty($item['thumbnail_url'])): ?>
            <div class="w-full h-24 rounded-lg bg-zinc-100 overflow-hidden cursor-pointer" onclick="coraEditArticle(<?php echo $post_id; ?>)">
              <img src="<?php echo esc_url($item['thumbnail_url']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
          <?php endif; ?>

          <!-- Category & Priority -->
          <div class="flex items-center justify-between text-[9px]">
            <span class="font-bold px-2 py-0.5 rounded bg-zinc-100 text-zinc-700 uppercase tracking-wider"><?php echo esc_html(strtoupper($item['industry'] ?? 'real_estate')); ?></span>
            <span class="font-bold px-2 py-0.5 rounded uppercase tracking-wider <?php echo $pc; ?>"><?php echo esc_html($item['priority']); ?></span>
          </div>

          <!-- Title -->
          <h4 class="text-xs font-bold text-zinc-900 group-hover:text-zinc-700 line-clamp-2 leading-snug cursor-pointer" onclick="coraEditArticle(<?php echo $post_id; ?>)">
            <?php echo esc_html($item['title']); ?>
          </h4>

          <!-- Keyword -->
          <?php if(!empty($item['primary_keyword'])): ?>
            <div class="flex items-center gap-1.5 text-[10px] text-zinc-600 bg-zinc-50 border border-zinc-100 rounded-md px-2 py-1">
              <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              <span class="font-medium truncate">Target: <strong><?php echo esc_html($item['primary_keyword']); ?></strong></span>
            </div>
          <?php endif; ?>

          <!-- Metrics Row -->
          <div class="flex items-center justify-between text-[10px] pt-1.5 border-t border-zinc-100">
            <button type="button" class="flex items-center gap-1 bg-zinc-900 hover:bg-zinc-800 text-white px-2 py-0.5 rounded-md font-bold transition-colors cursor-pointer" onclick="event.stopPropagation(); openSEODetailDrawer(<?php echo $post_id; ?>, '<?php echo esc_js($item['title']); ?>')">
              <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
              <span>SEO <?php echo (int)($item['seo_score'] ?: 78); ?>/100</span>
            </button>
            <div class="flex items-center gap-1 bg-zinc-100 text-zinc-800 border border-zinc-200 px-2 py-0.5 rounded-md font-semibold">
              <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line></svg>
              <span>GEO <?php echo (int)($item['geo_score'] ?: 65); ?>%</span>
            </div>
            <span class="text-zinc-500 font-medium text-[10px]"><?php echo number_format($item['target_word_count'] ?: 1200); ?> w</span>
          </div>

          <!-- CTAs Row -->
          <div class="flex items-center justify-between pt-1.5 border-t border-zinc-100 gap-1.5">
            <button type="button" onclick="event.stopPropagation(); coraEditArticle(<?php echo $post_id; ?>)" class="flex-1 px-2 py-1 bg-zinc-900 hover:bg-zinc-800 text-white text-[10px] font-bold rounded-md flex items-center justify-center gap-1 transition-colors cursor-pointer">
              <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
              Edit Article
            </button>
            <button type="button" onclick="event.stopPropagation(); openContentBriefDrawer(<?php echo $item['id']; ?>)" class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[10px] font-semibold rounded-md flex items-center gap-1 transition-colors cursor-pointer">
              <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
              Brief
            </button>
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
    <div class="p-2 border-t border-zinc-200/70 bg-white rounded-b-2xl">
      <button class="w-full text-center text-xs font-semibold text-zinc-500 hover:text-zinc-900 py-1.5 hover:bg-zinc-100 rounded-lg transition-colors flex items-center justify-center gap-1 cursor-pointer" onclick="openContentBriefDrawer()">
        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Add to <?php echo $stage_label; ?>
      </button>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<script>
(function() {
  // Drag & Drop Handlers
  let _draggedItemId = null;

  window.coraWbDragStart = function(e, itemId) {
    _draggedItemId = itemId;
    e.dataTransfer.setData('text/plain', itemId);
    e.dataTransfer.effectAllowed = 'move';
    if(e.currentTarget) e.currentTarget.style.opacity = '0.4';
  };

  window.coraWbDragOver = function(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
  };

  window.coraWbDragEnter = function(e, stageKey) {
    e.preventDefault();
    const col = e.currentTarget;
    if(col) col.classList.add('bg-zinc-100', 'ring-2', 'ring-zinc-900', 'rounded-xl');
  };

  window.coraWbDragLeave = function(e, stageKey) {
    const col = e.currentTarget;
    if(col) col.classList.remove('bg-zinc-100', 'ring-2', 'ring-zinc-900', 'rounded-xl');
  };

  window.coraWbDrop = function(e, targetStage) {
    e.preventDefault();
    const col = e.currentTarget;
    if(col) col.classList.remove('bg-zinc-100', 'ring-2', 'ring-zinc-900', 'rounded-xl');

    const itemId = _draggedItemId || e.dataTransfer.getData('text/plain');
    if(!itemId) return;

    // Reset card opacity
    const cardEl = document.querySelector(`.cora-wb-card[data-id="${itemId}"]`);
    if(cardEl) cardEl.style.opacity = '1';

    // Call stage update AJAX
    window.moveToStage(itemId, targetStage);
  };

  // Load workspace data
  window.loadContentWorkspace = function(stageFilter) {
    const kanban = document.getElementById('cora-workflow-kanban');
    if(kanban) kanban.style.opacity = '0.5';
    $.post(coraREWPData.ajaxUrl, {
      action: 'cora_fetch_content_workspace',
      nonce: coraREWPData.ajaxNonce,
      stage_filter: stageFilter || ''
    }, function(r) {
      if(r && r.success) {
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
      const items = data.stages ? (data.stages[stage] || []) : [];
      if(countEl) countEl.textContent = items.length;
      col.innerHTML = items.length === 0
        ? '<div class="h-32 rounded-xl border border-dashed border-zinc-200 flex flex-col items-center justify-center text-zinc-400 text-xs gap-1"><span class="font-medium text-[11px]">Drop item here</span></div>'
        : items.map(item => renderItemCard(item)).join('');
    });
  }

  function renderItemCard(item) {
    const priorityColors = {urgent:'bg-zinc-950 text-white',high:'bg-zinc-800 text-white',medium:'bg-zinc-200 text-zinc-900',low:'bg-zinc-100 text-zinc-600'};
    const pc = priorityColors[item.priority] || priorityColors.medium;
    const seoScore = item.seo_score || 78;
    const geoScore = item.geo_score || 65;
    const wordCount = (item.target_word_count || 1200).toLocaleString();
    const writerName = item.writer_name || 'Unassigned';
    const writerInitial = writerName[0].toUpperCase();
    const ind = (item.industry || 'REAL ESTATE').toUpperCase();
    const postId = item.post_id || item.id;

    return `
      <div draggable="true" 
           ondragstart="coraWbDragStart(event, ${item.id})"
           class="cora-wb-card bg-white border border-zinc-200/90 hover:border-zinc-400 rounded-xl p-3.5 shadow-sm hover:shadow-md transition-all space-y-3 cursor-grab active:cursor-grabbing group relative"
           data-id="${item.id}"
           data-post-id="${postId}">

        ${item.thumbnail_url ? 
          `<div class="w-full h-24 rounded-lg bg-zinc-100 overflow-hidden cursor-pointer" onclick="coraEditArticle(${postId})">
            <img src="${escHtml(item.thumbnail_url)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
           </div>` : ''
        }

        <!-- Category & Priority -->
        <div class="flex items-center justify-between text-[9px]">
          <span class="font-bold px-2 py-0.5 rounded bg-zinc-100 text-zinc-700 uppercase tracking-wider">${escHtml(ind)}</span>
          <span class="font-bold px-2 py-0.5 rounded uppercase tracking-wider ${pc}">${escHtml(item.priority || 'medium')}</span>
        </div>

        <!-- Title -->
        <h4 class="text-xs font-bold text-zinc-900 group-hover:text-zinc-700 line-clamp-2 leading-snug cursor-pointer" onclick="coraEditArticle(${postId})">
          ${escHtml(item.title)}
        </h4>

        <!-- Keyword -->
        ${item.primary_keyword ? `
          <div class="flex items-center gap-1.5 text-[10px] text-zinc-600 bg-zinc-50 border border-zinc-100 rounded-md px-2 py-1">
            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <span class="font-medium truncate">Target: <strong>${escHtml(item.primary_keyword)}</strong></span>
          </div>
        ` : ''}

        <!-- Metrics Row -->
        <div class="flex items-center justify-between text-[10px] pt-1.5 border-t border-zinc-100">
          <button type="button" class="flex items-center gap-1 bg-zinc-900 hover:bg-zinc-800 text-white px-2 py-0.5 rounded-md font-bold transition-colors cursor-pointer" onclick="event.stopPropagation(); openSEODetailDrawer(${postId}, '${escHtml(item.title).replace(/'/g,"\\'")}')">
            <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <span>SEO ${seoScore}/100</span>
          </button>
          <div class="flex items-center gap-1 bg-zinc-100 text-zinc-800 border border-zinc-200 px-2 py-0.5 rounded-md font-semibold">
            <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line></svg>
            <span>GEO ${geoScore}%</span>
          </div>
          <span class="text-zinc-500 font-medium text-[10px]">${wordCount} w</span>
        </div>

        <!-- CTAs Row -->
        <div class="flex items-center justify-between pt-1.5 border-t border-zinc-100 gap-1.5">
          <button type="button" onclick="event.stopPropagation(); coraEditArticle(${postId})" class="flex-1 px-2 py-1 bg-zinc-900 hover:bg-zinc-800 text-white text-[10px] font-bold rounded-md flex items-center justify-center gap-1 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            Edit Article
          </button>
          <button type="button" onclick="event.stopPropagation(); openContentBriefDrawer(${item.id})" class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[10px] font-semibold rounded-md flex items-center gap-1 transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            Brief
          </button>
        </div>

        <!-- Author & Date Footer -->
        <div class="flex items-center justify-between text-[10px] text-zinc-500 pt-1">
          <div class="flex items-center gap-1.5">
            <div class="w-4 h-4 rounded-full bg-zinc-200 flex items-center justify-center text-[8px] font-bold text-zinc-700">
              ${writerInitial}
            </div>
            <span class="font-medium text-zinc-700 truncate max-w-[90px]">${escHtml(writerName)}</span>
          </div>
          <div class="font-mono text-[9px] text-zinc-400">${escHtml(item.draft_due_date || 'No date')}</div>
        </div>
      </div>
    `;
  }

  function escHtml(s) { return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  window.moveToStage = function(itemId, targetStage) {
    if(typeof coraREWPData === 'undefined') return;
    $.post(coraREWPData.ajaxUrl, {
      action: 'cora_update_content_stage',
      nonce: coraREWPData.ajaxNonce,
      item_id: itemId,
      target_stage: targetStage
    }, function(r) {
      if(r && r.success) {
        const stageNames = {'idea':'Idea','briefing':'Briefing','research':'Research','drafting':'Drafting','editorial_review':'Editorial Review','revisions':'Revisions','seo_gate':'SEO Gate','approval':'Approval','scheduled':'Scheduled','published':'Published','performance':'Performance'};
        const label = stageNames[targetStage] || targetStage;
        if(typeof window.coraShowToast === 'function') {
          window.coraShowToast('Moved to ' + label, 'success');
        }
        window.loadContentWorkspace();
      } else {
        if(typeof window.coraShowToast === 'function') {
          window.coraShowToast('Stage update failed', 'error');
        }
      }
    });
  };
})();
</script>
