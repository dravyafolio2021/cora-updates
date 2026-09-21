<?php
/**
 * Cora Workspace — AI Tools & MCP Developer Gateway
 * Personalized AI Co-Founder with Dual Chat & Live Voice Modes, MCP Protocol 2.0 & Living Memory RAG.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$mcp_token = get_option( 'cora_mcp_access_token' );
if ( empty( $mcp_token ) ) {
    $mcp_token = bin2hex( wp_generate_password( 32, false ) );
    update_option( 'cora_mcp_access_token', $mcp_token );
}
$mcp_url = home_url( '/wp-json/cora/v1/mcp' );

// User Personalization & Dynamic Greeting Context
$current_user = wp_get_current_user();
$raw_first_name = function_exists('cora_get_resolved_user_first_name') ? cora_get_resolved_user_first_name() : '';
if ( empty( $raw_first_name ) && ! empty( $current_user->display_name ) ) {
    $raw_first_name = $current_user->display_name;
}
// Rule 3: Zero Use of Owner Name (Shruti/Shravya/etc.)
$is_studio = function_exists('cora_is_studio_active') ? cora_is_studio_active() : true;
$default_role_title = $is_studio ? 'Studio Director' : 'Workspace Owner';

if ( empty( $raw_first_name ) || preg_match('/shrut|shravya/i', $raw_first_name) ) {
    $user_first_name = $default_role_title;
} else {
    $user_first_name = esc_html( $raw_first_name );
}
$user_role_label = function_exists('cora_get_current_user_role_label') ? cora_get_current_user_role_label() : $default_role_title;

$hour = intval( date( 'H' ) );
$greeting_time = ( $hour < 12 ) ? 'Good morning' : ( ( $hour < 17 ) ? 'Good afternoon' : 'Good evening' );

$industry_name = $is_studio ? 'Photography Studio' : 'Workspace';

// RAG Memory Counts
global $wpdb;
$agency_id = function_exists( 'cora_db_get_agency_id' ) ? ( cora_db_get_agency_id() ?: 1 ) : 1;
$rag_table = $wpdb->prefix . 'cora_rag_knowledge';
$rag_fragment_count = 0;
if ( function_exists('cora_table_exists') && cora_table_exists( $rag_table ) ) {
    $rag_fragment_count = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$rag_table} WHERE agency_id = %d", $agency_id ) ) ) ?: 0;
}
?>
<style>
    /* ─── AI Tools & MCP Scoped Styles ────────────────────────────────────────── */
    #cora-page-mcp {
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    /* Main Workspace Card */
    .cora-ai-workspace {
        display: flex;
        flex-direction: column;
        min-height: 600px;
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .dark .cora-ai-workspace {
        background: #18181b;
        border-color: #27272a;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    /* AI Assistant Header Ribbon */
    .cora-chat-header {
        min-height: 56px;
        border-bottom: 1px solid #e4e4e7;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        background: rgba(250, 250, 250, 0.9);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        flex-wrap: wrap;
        position: relative;
        z-index: 20;
    }
    .dark .cora-chat-header {
        border-bottom-color: #27272a;
        background: rgba(18, 18, 20, 0.9);
    }

    /* Model Popover Dropdown */
    #cora-model-popover {
        animation: coraPopoverIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes coraPopoverIn {
        from { opacity: 0; transform: translateY(-4px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .cora-model-opt-item {
        transition: background-color 0.12s ease;
    }
    .cora-model-opt-item:hover {
        background-color: rgba(244, 244, 245, 0.9);
    }
    .dark .cora-model-opt-item:hover {
        background-color: rgba(39, 39, 42, 0.9);
    }
    .cora-model-opt-item.selected {
        background-color: rgba(244, 244, 245, 1);
    }
    .dark .cora-model-opt-item.selected {
        background-color: rgba(39, 39, 42, 1);
    }

    /* Segmented Mode Switcher (Chat vs Live Voice) */
    .cora-mode-segmented {
        display: inline-flex;
        align-items: center;
        padding: 3px;
        background: #f4f4f5;
        border-radius: 9999px;
        border: 1px solid #e4e4e7;
        gap: 2px;
    }
    .dark .cora-mode-segmented {
        background: #27272a;
        border-color: #3f3f46;
    }
    .cora-mode-btn {
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 600;
        color: #71717a;
        border-radius: 9999px;
        border: none;
        background: transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        user-select: none;
        touch-action: manipulation;
    }
    .cora-mode-btn:hover {
        color: #18181b;
    }
    .dark .cora-mode-btn:hover {
        color: #f4f4f5;
    }
    .cora-mode-btn.active {
        background: #ffffff;
        color: #09090b;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .dark .cora-mode-btn.active {
        background: #09090b;
        color: #ffffff;
    }

    /* Chat Messages Canvas */
    .cora-ai-chat-container {
        display: flex;
        flex-direction: column;
        height: 620px;
        position: relative;
    }
    .cora-ai-messages {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        scroll-behavior: smooth;
    }

    /* Message Bubbles */
    .cora-ai-message {
        display: flex;
        flex-direction: column;
        max-width: 82%;
        border-radius: 16px;
        padding: 12px 16px;
        font-size: 13px;
        line-height: 1.55;
        animation: coraFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        word-break: break-word;
    }
    @keyframes coraFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .cora-ai-message.user {
        align-self: flex-end;
        background: #09090b;
        color: #ffffff;
        border-bottom-right-radius: 4px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .dark .cora-ai-message.user {
        background: #f4f4f5;
        color: #09090b;
    }
    .cora-ai-message.assistant {
        align-self: flex-start;
        background: #fafafa;
        color: #18181b;
        border: 1px solid #e4e4e7;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .dark .cora-ai-message.assistant {
        background: #27272a;
        color: #f4f4f5;
        border-color: #3f3f46;
    }

    /* Skeleton Chat Loader */
    .cora-skeleton-chat {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 220px;
        padding: 4px 0;
    }
    .cora-skeleton-line {
        height: 10px;
        background: #e4e4e7;
        border-radius: 4px;
        animation: coraSkeletonPulse 1.4s infinite ease-in-out;
    }
    .dark .cora-skeleton-line {
        background: #3f3f46;
    }
    .cora-skeleton-line.w-80 { width: 80%; }
    .cora-skeleton-line.w-95 { width: 95%; }
    .cora-skeleton-line.w-60 { width: 60%; }
    @keyframes coraSkeletonPulse {
        0%, 100% { opacity: 0.55; }
        50% { opacity: 1; }
    }

    /* Follow-up Suggestion Chips */
    .cora-ai-followup-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 12px;
        border-top: 1px dashed #e4e4e7;
        padding-top: 10px;
    }
    .dark .cora-ai-followup-chips {
        border-top-color: #3f3f46;
    }
    .cora-ai-followup-chip {
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 9999px;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 500;
        color: #52525b;
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: left;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .dark .cora-ai-followup-chip {
        background: #18181b;
        border-color: #3f3f46;
        color: #a1a1aa;
    }
    .cora-ai-followup-chip:hover {
        background: #f4f4f5;
        color: #09090b;
        border-color: #18181b;
    }
    .dark .cora-ai-followup-chip:hover {
        background: #27272a;
        color: #ffffff;
        border-color: #71717a;
    }

    .cora-ai-message-meta {
        font-size: 10px;
        opacity: 0.65;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Chat Input Bar */
    .cora-ai-input-wrapper {
        padding: 12px 16px;
        border-top: 1px solid #e4e4e7;
        display: flex;
        gap: 8px;
        align-items: center;
        background: #ffffff;
    }
    .dark .cora-ai-input-wrapper {
        border-top-color: #27272a;
        background: #18181b;
    }
    .cora-ai-input {
        flex: 1;
        border: 1px solid #e4e4e7;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        outline: none;
        background: #fafafa;
        color: inherit;
        transition: all 0.15s ease;
    }
    .dark .cora-ai-input {
        background: #27272a;
        border-color: #3f3f46;
        color: #f4f4f5;
    }
    .cora-ai-input:focus {
        border-color: #09090b;
        background: #ffffff;
    }
    .dark .cora-ai-input:focus {
        border-color: #f4f4f5;
        background: #18181b;
    }
    .cora-ai-btn {
        height: 40px;
        padding: 0 16px;
        background: #09090b;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.15s ease;
    }
    .dark .cora-ai-btn {
        background: #ffffff;
        color: #09090b;
    }
    .cora-ai-btn:hover {
        opacity: 0.9;
        transform: scale(0.98);
    }
    .cora-ai-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    /* ─── Personalized Welcome Screen ─────────────────────────────────────────── */
    .cora-ai-welcome {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        padding: 30px 20px;
        gap: 18px;
    }
    .cora-welcome-avatar {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        background: #09090b;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        position: relative;
    }
    .dark .cora-welcome-avatar {
        background: #ffffff;
        color: #09090b;
    }
    .cora-welcome-halo {
        position: absolute;
        inset: -4px;
        border-radius: 22px;
        border: 2px solid rgba(16, 185, 129, 0.4);
        animation: coraHaloPulse 2.5s infinite ease-in-out;
    }
    @keyframes coraHaloPulse {
        0%, 100% { transform: scale(1); opacity: 0.4; }
        50% { transform: scale(1.08); opacity: 0.9; }
    }

    .cora-ai-chips {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        width: 100%;
        max-width: 640px;
    }
    .cora-ai-chip {
        background: #fafafa;
        border: 1px solid #e4e4e7;
        border-radius: 14px;
        padding: 12px 14px;
        text-align: left;
        cursor: pointer;
        transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .dark .cora-ai-chip {
        background: #27272a;
        border-color: #3f3f46;
    }
    .cora-ai-chip:hover {
        background: #ffffff;
        border-color: #09090b;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    .dark .cora-ai-chip:hover {
        background: #18181b;
        border-color: #f4f4f5;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    /* ─── LIVE VOICE ASSISTANT CANVAS ─────────────────────────────────────────── */
    #cora-mcp-voice-view {
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        height: 620px;
        padding: 32px 24px;
        position: relative;
        background: radial-gradient(circle at 50% 35%, rgba(244, 244, 245, 0.8) 0%, rgba(255, 255, 255, 1) 70%);
    }
    .dark #cora-mcp-voice-view {
        background: radial-gradient(circle at 50% 35%, rgba(39, 39, 42, 0.6) 0%, rgba(24, 24, 27, 1) 70%);
    }

    /* Center Pulsing Voice Sphere */
    .cora-voice-orb-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: auto;
        position: relative;
    }
    .cora-voice-sphere {
        width: 120px;
        height: 120px;
        border-radius: 9999px;
        background: linear-gradient(135deg, #09090b 0%, #27272a 50%, #52525b 100%);
        box-shadow: 0 12px 40px rgba(0,0,0,0.2), inset 0 2px 6px rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        cursor: pointer;
        position: relative;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
    }
    .dark .cora-voice-sphere {
        background: linear-gradient(135deg, #ffffff 0%, #e4e4e7 50%, #a1a1aa 100%);
        color: #09090b;
        box-shadow: 0 12px 40px rgba(255,255,255,0.15), inset 0 2px 6px rgba(0,0,0,0.2);
    }
    .cora-voice-sphere.listening {
        animation: coraOrbPulse 1.8s infinite ease-in-out;
    }
    .cora-voice-sphere.speaking {
        animation: coraOrbWave 1.2s infinite alternate ease-in-out;
    }

    @keyframes coraOrbPulse {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        50% { transform: scale(1.06); box-shadow: 0 0 0 18px rgba(16, 185, 129, 0); }
    }
    @keyframes coraOrbWave {
        0% { transform: scale(0.98); }
        100% { transform: scale(1.12); }
    }

    /* Soundwave bars */
    .cora-voice-waveforms {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        height: 32px;
        margin-top: 20px;
    }
    .cora-voice-wave-bar {
        width: 3px;
        height: 6px;
        background: #09090b;
        border-radius: 99px;
        transition: height 0.1s ease;
    }
    .dark .cora-voice-wave-bar {
        background: #f4f4f5;
    }
    .cora-voice-sphere.listening ~ .cora-voice-waveforms .cora-voice-wave-bar:nth-child(odd) {
        animation: coraWaveform 0.6s infinite alternate ease-in-out;
    }
    .cora-voice-sphere.listening ~ .cora-voice-waveforms .cora-voice-wave-bar:nth-child(even) {
        animation: coraWaveform 0.8s 0.2s infinite alternate ease-in-out;
    }
    @keyframes coraWaveform {
        0% { height: 4px; }
        100% { height: 26px; }
    }

    /* Live Voice Live Transcript Box */
    .cora-voice-transcript-card {
        width: 100%;
        max-width: 580px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #e4e4e7;
        border-radius: 16px;
        padding: 14px 18px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        backdrop-filter: blur(8px);
        margin-top: 16px;
        min-height: 72px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .dark .cora-voice-transcript-card {
        background: rgba(39, 39, 42, 0.9);
        border-color: #3f3f46;
    }

    /* Voice Bottom Controls */
    .cora-voice-controls-bar {
        width: 100%;
        max-width: 580px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 14px;
        border-top: 1px solid #e4e4e7;
    }
    .dark .cora-voice-controls-bar {
        border-top-color: #27272a;
    }

    /* Drawer Pointer Events Lifecycle */
    #cora-ai-settings-drawer.translate-x-full {
        pointer-events: none !important;
    }
    #cora-ai-settings-drawer.translate-x-0 {
        pointer-events: auto !important;
    }
    #cora-ai-drawer-backdrop.hidden {
        pointer-events: none !important;
    }
    #cora-ai-drawer-backdrop:not(.hidden) {
        pointer-events: auto !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #cora-page-mcp {
            margin-bottom: 84px;
            border-radius: 14px;
        }
        .cora-ai-chips {
            grid-template-columns: 1fr;
        }
        .cora-chat-header {
            padding: 10px 12px;
            flex-wrap: wrap;
        }
        .cora-ai-messages {
            padding: 16px;
            padding-bottom: 24px;
        }
        .cora-voice-controls-bar {
            padding-bottom: 16px;
        }
        #cora-mcp-voice-view {
            padding-bottom: 24px;
        }
    }
</style>

<script>
    // Define global switches early to eliminate click race conditions
    window.coraSwitchAIPanel = function(panelId, btnEl) {
        const tabs = document.querySelectorAll('.cora-sub-tabs-container .cora-sub-tab, .cora-sticky-mcp-tabs .cora-tab-btn, #cora-sub-navigation-tabs button');
        const chatPanel = document.getElementById('cora-ai-panel-chat');
        const settingsPanel = document.getElementById('cora-ai-panel-mcp-settings');
        const ragPanel = document.getElementById('cora-ai-panel-rag-settings');

        tabs.forEach(t => {
            t.classList.remove('active', 'border-zinc-950', 'text-zinc-900', 'dark:border-white', 'dark:text-white', 'dark:text-zinc-100');
            t.classList.add('border-transparent', 'text-zinc-500', 'dark:text-zinc-400');
        });

        const activeBtn = btnEl || document.querySelector(`[data-target="${panelId}"], [data-tab="${panelId}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active', 'border-zinc-950', 'text-zinc-900', 'dark:border-white', 'dark:text-white', 'dark:text-zinc-100');
            activeBtn.classList.remove('border-transparent', 'text-zinc-500', 'dark:text-zinc-400');
            try {
                activeBtn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            } catch(e) {}
        }

        if (panelId === 'chat' || panelId === 'cora-ai-panel-chat') {
            if (chatPanel) chatPanel.style.display = 'flex';
            if (settingsPanel) settingsPanel.style.display = 'none';
            if (ragPanel) ragPanel.style.display = 'none';
            try { history.replaceState(null, '', '#chat'); } catch(e) {}
        } else if (panelId === 'mcp-settings' || panelId === 'cora-ai-panel-mcp-settings') {
            if (chatPanel) chatPanel.style.display = 'none';
            if (settingsPanel) settingsPanel.style.display = 'block';
            if (ragPanel) ragPanel.style.display = 'none';
            try { history.replaceState(null, '', '#mcp-settings'); } catch(e) {}
        } else if (panelId === 'rag-settings' || panelId === 'cora-ai-panel-rag-settings') {
            if (chatPanel) chatPanel.style.display = 'none';
            if (settingsPanel) settingsPanel.style.display = 'none';
            if (ragPanel) ragPanel.style.display = 'block';
            try { history.replaceState(null, '', '#rag-settings'); } catch(e) {}
        }
    };

    window.coraToggleModelPopover = function(force) {
        const popover = document.getElementById('cora-model-popover');
        const chevron = document.getElementById('cora-model-chevron');
        if (!popover) return;
        const isClosed = popover.classList.contains('hidden');
        const shouldOpen = force !== undefined ? force : isClosed;
        if (shouldOpen) {
            popover.classList.remove('hidden');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            popover.classList.add('hidden');
            if (chevron) chevron.style.transform = '';
        }
    };

    window.coraSelectModel = function(provider, model, label, badge) {
        const providerInput = document.getElementById('cora-ai-provider');
        const modelInput = document.getElementById('cora-ai-model');
        const labelEl = document.getElementById('cora-selected-model-label');
        const badgeEl = document.getElementById('cora-selected-model-badge');

        if (providerInput) providerInput.value = provider;
        if (modelInput) modelInput.value = model;
        if (labelEl) labelEl.textContent = label;
        if (badgeEl) badgeEl.textContent = badge;

        document.querySelectorAll('.cora-model-opt-item').forEach(item => {
            const checkDot = item.querySelector('.cora-model-check div');
            const itemOnClick = item.getAttribute('onclick') || '';
            if (itemOnClick.includes(`'${model}'`)) {
                item.classList.add('selected');
                if (checkDot) checkDot.classList.remove('hidden');
            } else {
                item.classList.remove('selected');
                if (checkDot) checkDot.classList.add('hidden');
            }
        });

        coraToggleModelPopover(false);
        if (window.coraShowToast) {
            window.coraShowToast(`AI model switched to ${label} (${badge}).`);
        }
    };
</script>

<?php
// Render Standard Workspace Header with Sub-Navigation Tabs
$mcp_header_args = array(
    'title'              => 'AI Assistant & MCP Developer Gateway',
    'mobile_title'       => 'AI Tools & MCP',
    'description'        => 'Personalized AI Co-Founder with Dual Chat & Live Voice Modes, MCP Protocol 2.0 & Living Memory RAG.',
    'mobile_description' => 'Personalized AI Co-Founder & MCP Gateway',
    'icon'               => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 2a8 8 0 0 0-8 8c0 3.31 2.01 6.16 4.9 7.37L8 21l3.5-1.5L15 21l-.9-3.63C16.99 16.16 19 13.31 19 10a8 8 0 0 0-7-8z"></path><circle cx="9" cy="10" r="1"></circle><circle cx="15" cy="10" r="1"></circle></svg>',
    'ai_stack'           => true,
    'tutorial_onclick'   => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'tabs'               => array(
        array(
            'id'           => 'chat',
            'dom_id'       => 'mcp-tab-chat',
            'label'        => 'AI Assistant & Voice',
            'mobile_label' => 'AI Studio',
            'icon'         => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>',
            'active'       => true,
            'onclick'      => "coraSwitchAIPanel('chat', this)",
        ),
        array(
            'id'           => 'mcp-settings',
            'dom_id'       => 'mcp-tab-gateway',
            'label'        => 'MCP Developer Gateway',
            'mobile_label' => 'MCP Gateway',
            'icon'         => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>',
            'active'       => false,
            'onclick'      => "coraSwitchAIPanel('mcp-settings', this)",
        ),
        array(
            'id'           => 'rag-settings',
            'dom_id'       => 'mcp-tab-rag',
            'label'        => 'Living Memory RAG',
            'mobile_label' => 'Living Memory',
            'icon'         => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>',
            'badge'        => '<span class="ml-1 px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[9px] font-bold rounded-full">' . intval($rag_fragment_count) . '</span>',
            'active'       => false,
            'onclick'      => "coraSwitchAIPanel('rag-settings', this)",
        ),
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $mcp_header_args );
}
?>

<!-- Local backdrop for AI settings drawer -->
<div id="cora-ai-drawer-backdrop" onclick="coraToggleAISettingsDrawer(false)" class="hidden fixed inset-0 bg-black/30 z-[99988] backdrop-blur-[1.5px] transition-opacity duration-200 cursor-pointer"></div>

<!-- Right-Sliding AI Settings Drawer -->
<div id="cora-ai-settings-drawer" class="fixed inset-y-0 right-0 z-[99999] w-84 bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 translate-x-full">
    <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">AI Model & Persona Settings</h3>
        <button type="button" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 cursor-pointer p-1 border-none bg-transparent" onclick="coraToggleAISettingsDrawer(false)">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <div class="flex-1 p-5 overflow-y-auto space-y-5">
        <div class="cora-ai-field">
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Executive Tone / Persona</label>
            <select id="cora-mcp-ai-tone" class="w-full text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg p-2.5 outline-none text-zinc-900 dark:text-zinc-100">
                <option value="executive" selected>Executive Brief (Concise & Data-Driven)</option>
                <option value="co_founder">Strategic Co-Founder (Action-Oriented)</option>
                <option value="creative_director">Creative Director & Media Architect</option>
                <option value="hinglish">Urban Hinglish (Modern Indian Business)</option>
            </select>
        </div>

        <div class="cora-ai-field border-t border-zinc-200 dark:border-zinc-800 pt-4">
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Creativity Temperature (<span id="cora-ai-temp-val">0.7</span>)</label>
            <input type="range" id="cora-ai-temperature" min="0" max="1" step="0.1" value="0.7" oninput="document.getElementById('cora-ai-temp-val').innerText = this.value" class="w-full">
        </div>

        <div class="cora-ai-field border-t border-zinc-200 dark:border-zinc-800 pt-4">
            <label class="flex items-center gap-2.5 cursor-pointer text-xs font-bold text-zinc-700 dark:text-zinc-300">
                <input type="checkbox" id="cora-ai-tts-toggle" class="rounded border-zinc-300 text-zinc-950 focus:ring-zinc-950">
                ElevenLabs High-Definition Voice Audio
            </label>
        </div>

        <div class="cora-ai-field border-t border-zinc-200 dark:border-zinc-800 pt-4">
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Custom System Prompt Override</label>
            <textarea id="cora-ai-system" rows="5" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg p-2.5 text-xs bg-zinc-50 dark:bg-zinc-950 outline-none text-zinc-800 dark:text-zinc-200 font-sans">You are Cora AI, the personalized Co-Founder & Executive Operating Intelligence for <?php echo esc_attr($industry_name); ?>. Provide concise, direct 1-2 sentence insights, execute workspace action tags, and query living memory.</textarea>
        </div>
        
        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 space-y-2">
            <button type="button" class="w-full px-4 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 rounded-xl text-center font-bold text-xs transition-colors border-none cursor-pointer" onclick="coraClearConversation(); coraToggleAISettingsDrawer(false);">Clear Chat History</button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- TAB 1: AI Assistant & Voice Studio                                        -->
<!-- ========================================================================= -->
<div id="cora-ai-panel-chat" class="cora-ai-workspace">
    <!-- Chat Header Ribbon -->
    <div class="cora-chat-header">
        <!-- Left: Model Popover Selector & Living Memory Status Pill -->
        <div class="flex items-center gap-2 flex-wrap">
            <div class="relative" id="cora-model-selector-wrapper">
                <button type="button" id="cora-model-selector-btn" onclick="coraToggleModelPopover()" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-zinc-800/90 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-xs font-semibold text-zinc-900 dark:text-zinc-100 shadow-2xs hover:border-zinc-400 dark:hover:border-zinc-600 transition-all cursor-pointer select-none">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span id="cora-selected-model-label" class="truncate max-w-[150px] sm:max-w-[200px]">Gemini 3.5 Flash</span>
                    <span id="cora-selected-model-badge" class="hidden sm:inline-flex px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 text-[9.5px] font-bold rounded-md">Real-time</span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0 transition-transform duration-200" id="cora-model-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>

                <!-- Floating Monochromatic Popover Dropdown -->
                <div id="cora-model-popover" class="hidden absolute top-full left-0 mt-1.5 w-76 sm:w-84 max-w-[calc(100vw-32px)] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl z-[9999] p-2 space-y-2 select-none">
                    <!-- Hidden inputs for backward-compatible form dispatch -->
                    <input type="hidden" id="cora-ai-provider" value="gemini">
                    <input type="hidden" id="cora-ai-model" value="gemini-flash-latest">

                    <div class="px-2.5 pt-1.5 pb-1 flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Select AI Intelligence</span>
                        <span class="text-[9px] font-bold font-mono text-zinc-400">Zero-Lag Switching</span>
                    </div>

                    <div class="max-h-72 overflow-y-auto space-y-3 px-1 scrollbar-thin">
                        <!-- Group 1: Google Gemini -->
                        <div>
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider px-2 py-1 flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="10" height="10" fill="currentColor" class="text-blue-500"><path d="M11.04 19.32Q12 21.51 12 24q0-2.49.93-4.68.96-2.19 2.58-3.81t3.81-2.55Q21.51 12 24 12q-2.49 0-4.68-.93a12.3 12.3 0 0 1-3.81-2.58 12.3 12.3 0 0 1-2.58-3.81Q12 2.49 12 0q0 2.49-.96 4.68-.93 2.19-2.55 3.81a12.3 12.3 0 0 1-3.81 2.58Q2.49 12 0 12q2.49 0 4.68.96 2.19.93 3.81 2.55t2.55 3.81"/></svg>
                                <span>Google Gemini</span>
                            </div>
                            <div class="space-y-0.5">
                                <div class="cora-model-opt-item flex items-center justify-between p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors selected" onclick="coraSelectModel('gemini', 'gemini-flash-latest', 'Gemini 3.5 Flash', 'Real-time')">
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center cora-model-check">
                                            <div class="w-2 h-2 rounded-full bg-zinc-950 dark:bg-white"></div>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">Gemini 3.5 Flash</div>
                                            <div class="text-[10px] text-zinc-400">Fast reasoning & multimodel analysis</div>
                                        </div>
                                    </div>
                                    <span class="px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 text-[9px] font-bold rounded-md border border-emerald-200/50">Real-time</span>
                                </div>

                                <div class="cora-model-opt-item flex items-center justify-between p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors" onclick="coraSelectModel('gemini', 'gemini-flash-lite-latest', 'Gemini Flash Lite', '120ms')">
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center cora-model-check">
                                            <div class="w-2 h-2 rounded-full bg-zinc-950 dark:bg-white hidden"></div>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">Gemini Flash Lite</div>
                                            <div class="text-[10px] text-zinc-400">Ultra-low latency instant replies</div>
                                        </div>
                                    </div>
                                    <span class="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-[9px] font-bold rounded-md">120ms</span>
                                </div>

                                <div class="cora-model-opt-item flex items-center justify-between p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors" onclick="coraSelectModel('gemini', 'gemini-pro-latest', 'Gemini 3.5 Pro', 'Deep Logic')">
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center cora-model-check">
                                            <div class="w-2 h-2 rounded-full bg-zinc-950 dark:bg-white hidden"></div>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">Gemini 3.5 Pro</div>
                                            <div class="text-[10px] text-zinc-400">Complex financial & contract audits</div>
                                        </div>
                                    </div>
                                    <span class="px-1.5 py-0.5 bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 text-[9px] font-bold rounded-md border border-purple-200/50">Deep Logic</span>
                                </div>
                            </div>
                        </div>

                        <!-- Group 2: Anthropic Claude -->
                        <div>
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider px-2 py-1 flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="10" height="10" fill="currentColor" class="text-amber-600"><path d="m4.7144 15.9555 4.7174-2.6471.079-.2307-.079-.1275h-.2307l-.7893-.0486-2.6956-.0729-2.3375-.0971-2.2646-.1214-.5707-.1215-.5343-.7042.0546-.3522.4797-.3218.686.0608 1.5179.1032 2.2767.1578 1.6514.0972 2.4468.255h.3886l.0546-.1579-.1336-.0971-.1032-.0972L6.973 9.8356l-2.55-1.6879-1.3356-.9714-.7225-.4918-.3643-.4614-.1578-1.0078.6557-.7225.8803.0607.2246.0607.8925.686 1.9064 1.4754 2.4893 1.8336.3643.3035.1457-.1032.0182-.0728-.164-.2733-1.3539-2.4467-1.445-2.4893-.6435-1.032-.17-.6194c-.0607-.255-.1032-.4674-.1032-.7285L6.287.1335 6.6997 0l.9957.1336.419.3642.6192 1.4147 1.0018 2.2282 1.5543 3.0296.4553.8985.2429.8318.091.255h.1579v-.1457l.1275-1.706.2368-2.0947.2307-2.6957.0789-.7589.3764-.9107.7468-.4918.5828.2793.4797.686-.0668.4433-.2853 1.8517-.5586 2.9021-.3643 1.9429h.2125l.2429-.2429.9835-1.3053 1.6514-2.0643.7286-.8196.85-.9046.5464-.4311h1.0321l.759 1.1293-.34 1.1657-1.0625 1.3478-.8804 1.1414-1.2628 1.7-.7893 1.36.0729.1093.1882-.0183 2.8535-.607 1.5421-.2794 1.8396-.3157.8318.3886.091.3946-.3278.8075-1.967.4857-2.3072.4614-3.4364.8136-.0425.0304.0486.0607 1.5482.1457.6618.0364h1.621l3.0175.2247.7892.522.4736.6376-.079.4857-1.2142.6193-1.6393-.3886-3.825-.9107-1.3113-.3279h-.1822v.1093l1.0929 1.0686 2.0035 1.8092 2.5075 2.3314.1275.5768-.3218.4554-.34-.0486-2.2039-1.6575-.85-.7468-1.9246-1.621h-.1275v.17l.4432.6496 2.3436 3.5214.1214 1.0807-.17.3521-.6071.2125-.6679-.1214-1.3721-1.9246L14.38 17.959l-1.1414-1.9428-.1397.079-.674 7.2552-.3156.3703-.7286.2793-.6071-.4614-.3218-.7468.3218-1.4753.3886-1.9246.3157-1.53.2853-1.9004.17-.6314-.0121-.0425-.1397.0182-1.4328 1.9672-2.1796 2.9446-1.7243 1.8456-.4128.164-.7164-.3704.0667-.6618.4008-.5889 2.386-3.0357 1.4389-1.882.929-1.0868-.0062-.1579h-.0546l-6.3385 4.1164-1.1293.1457-.4857-.4554.0608-.7467.2307-.2429 1.9064-1.3114Z"/></svg>
                                <span>Anthropic Claude</span>
                            </div>
                            <div class="space-y-0.5">
                                <div class="cora-model-opt-item flex items-center justify-between p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors" onclick="coraSelectModel('openrouter', 'anthropic/claude-3.5-sonnet', 'Claude 3.5 Sonnet', 'Top Tier')">
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center cora-model-check">
                                            <div class="w-2 h-2 rounded-full bg-zinc-950 dark:bg-white hidden"></div>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">Claude 3.5 Sonnet</div>
                                            <div class="text-[10px] text-zinc-400">Superior executive writing & strategy</div>
                                        </div>
                                    </div>
                                    <span class="px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 text-[9px] font-bold rounded-md border border-amber-200/50">Top Tier</span>
                                </div>
                            </div>
                        </div>

                        <!-- Group 3: OpenAI -->
                        <div>
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider px-2 py-1 flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="10" height="10" fill="currentColor" class="text-emerald-600"><path d="M9.205 8.658v-2.26c0-.19.072-.333.238-.428l4.543-2.616c.619-.357 1.356-.523 2.117-.523 2.854 0 4.662 2.212 4.662 4.566 0 .167 0 .357-.024.547l-4.71-2.759a.797.797 0 00-.856 0l-5.97 3.473zm10.609 8.8V12.06c0-.333-.143-.57-.429-.737l-5.97-3.473 1.95-1.118a.433.433 0 01.476 0l4.543 2.617c1.309.76 2.189 2.378 2.189 3.948 0 1.808-1.07 3.473-2.76 4.163zM7.802 12.703l-1.95-1.142c-.167-.095-.239-.238-.239-.428V5.899c0-2.545 1.95-4.472 4.591-4.472 1 0 1.927.333 2.712.928L8.23 5.067c-.285.166-.428.404-.428.737v6.898zM12 15.128l-2.795-1.57v-3.33L12 8.658l2.795 1.57v3.33L12 15.128zm1.796 7.23c-1 0-1.927-.332-2.712-.927l4.686-2.712c.285-.166.428-.404.428-.737v-6.898l1.974 1.142c.167.095.238.238.238.428v5.233c0 2.545-1.974 4.472-4.614 4.472zm-5.637-5.303l-4.544-2.617c-1.308-.761-2.188-2.378-2.188-3.948A4.482 4.482 0 014.21 6.327v5.423c0 .333.143.571.428.738l5.947 3.449-1.95 1.118a.432 4.432 0 01-.476 0zm-.262 3.9c-2.688 0-4.662-2.021-4.662-4.519 0-.19.024-.38.047-.57l4.686 2.71c.286.167.571.167.856 0l5.97-3.448v2.26c0 .19-.07.333-.237.428l-4.543 2.616c-.619.357-1.356.523-2.117.523z"/></svg>
                                <span>OpenAI</span>
                            </div>
                            <div class="space-y-0.5">
                                <div class="cora-model-opt-item flex items-center justify-between p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors" onclick="coraSelectModel('gpt_oss_nv', 'openai/gpt-4o', 'GPT-4o Omnimodel', 'Multimodal')">
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center cora-model-check">
                                            <div class="w-2 h-2 rounded-full bg-zinc-950 dark:bg-white hidden"></div>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">GPT-4o Omnimodel</div>
                                            <div class="text-[10px] text-zinc-400">High-capacity reasoning & vision</div>
                                        </div>
                                    </div>
                                    <span class="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-[9px] font-bold rounded-md">Multimodal</span>
                                </div>
                            </div>
                        </div>

                        <!-- Group 4: Groq Ultra-Fast -->
                        <div>
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider px-2 py-1 flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-orange-500"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                <span>Groq Ultra-Fast</span>
                            </div>
                            <div class="space-y-0.5">
                                <div class="cora-model-opt-item flex items-center justify-between p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors" onclick="coraSelectModel('groq', 'llama-3.3-70b-versatile', 'Llama 3.3 70B (Groq)', 'Sub-200ms')">
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center cora-model-check">
                                            <div class="w-2 h-2 rounded-full bg-zinc-950 dark:bg-white hidden"></div>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">Llama 3.3 70B (Groq)</div>
                                            <div class="text-[10px] text-zinc-400">Near-instantaneous token generation</div>
                                        </div>
                                    </div>
                                    <span class="px-1.5 py-0.5 bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 text-[9px] font-bold rounded-md border border-orange-200/50">Sub-200ms</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Living Memory RAG Pill -->
            <button type="button" onclick="coraSwitchAIPanel('rag-settings');" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50/80 hover:bg-emerald-100/80 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 rounded-xl text-[10px] font-bold tracking-wide transition-colors cursor-pointer select-none" title="View Indexed Knowledge Memory">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="inline">Living Memory</span>
                <span class="font-mono opacity-80">(<?php echo intval($rag_fragment_count); ?>)</span>
            </button>
        </div>

        <!-- Center: Segmented Chat / Voice Switcher -->
        <div class="cora-mode-segmented">
            <button type="button" id="cora-assistant-tab-chat" class="cora-mode-btn active" onclick="coraSwitchAssistantMode('chat')">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span>Text Chat</span>
            </button>
            <button type="button" id="cora-assistant-tab-voice" class="cora-mode-btn" onclick="coraSwitchAssistantMode('voice')">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
                <span>Live Voice</span>
            </button>
        </div>

        <!-- Right: Actions & Settings Trigger -->
        <div class="flex items-center gap-1.5">
            <button type="button" onclick="coraClearConversation()" class="h-8 px-2.5 text-zinc-600 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-zinc-100 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer shadow-2xs" title="Start New Conversation">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                <span class="hidden sm:inline">New Chat</span>
            </button>
            <button type="button" onclick="coraToggleAISettingsDrawer(true)" class="h-8 w-8 text-zinc-600 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-zinc-100 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl flex items-center justify-center transition-colors cursor-pointer shadow-2xs" title="AI Assistant Settings">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </button>
        </div>
    </div>

    <!-- 1. TEXT CHAT VIEW CONTAINER -->
    <div id="cora-mcp-chat-view" class="cora-ai-chat-container">
        <div id="cora-ai-messages" class="cora-ai-messages">
            <!-- Personalized Welcome Screen -->
            <div class="cora-ai-welcome" id="cora-ai-welcome-screen">
                <div class="cora-welcome-avatar">
                    <svg viewBox="0 0 24 24" width="26" height="26" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 2a8 8 0 0 0-8 8c0 3.31 2.01 6.16 4.9 7.37L8 21l3.5-1.5L15 21l-.9-3.63C16.99 16.16 19 13.31 19 10a8 8 0 0 0-7-8z"></path><circle cx="9" cy="10" r="1"></circle><circle cx="15" cy="10" r="1"></circle></svg>
                    <div class="cora-welcome-halo"></div>
                </div>

                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center justify-center gap-1.5">
                        <span><?php echo esc_html( $greeting_time ); ?>,</span>
                        <span><?php echo esc_html( $user_first_name ?: $user_role_label ); ?></span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-md mt-1">Your personalized <?php echo esc_html( $industry_name ); ?> AI Co-Founder & Living Memory Second Brain is ready.</p>
                </div>

                <!-- Personalized Action Prompt Matrix -->
                <div class="cora-ai-chips">
                    <?php if ( $is_studio ) : ?>
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Give me a full studio revenue breakdown, pending client receivables, and monthly runway.')">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                            <span>Financial Runway Analysis</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Audit revenue collected and unpaid client receivables.</p>
                    </div>

                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Review upcoming photography shoots, crew call-times, and gear rosters.')">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span>Shoot Production Briefing</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Check scheduled sessions, call-times, and gear rosters.</p>
                    </div>

                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Audit our media library storage quota, largest video deliverables, and shared delivery link telemetry.')">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                            <span>Media Storage & Telemetry</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Audit storage headroom, folder structure, and download telemetry.</p>
                    </div>

                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Search the living knowledge base for our latest commercial photography contract terms and GST tax rules.')">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            <span>Contract Knowledge Search</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Query indexed vault agreements and business rules.</p>
                    </div>
                    <?php else : ?>
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Give me a full financial health breakdown with revenue, escrow ledger, and receivables.')">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                            <span>Financial Runway Analysis</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Audit brokerage commissions and escrow balances.</p>
                    </div>

                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Summarize our active CRM buyer pipeline and highlight high-intent property leads.')">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>CRM Pipeline Audit</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Identify high-intent deals in the conversion window.</p>
                    </div>

                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Inspect field agent attendance logs and office geofencing punch compliance.')">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>Agent Roster & Attendance</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Review field agent check-ins and GPS geofence compliance.</p>
                    </div>

                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Search the living knowledge base for indexed sale agreements, NOCs, and KYC rules.')">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            <span>Contract Knowledge Search</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Query indexed vault agreements and business rules.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Audio Playback element -->
        <audio id="cora-ai-audio-player" style="display:none;"></audio>

        <!-- Bottom Chat Input Dock -->
        <div class="cora-ai-input-wrapper">
            <button type="button" onclick="coraTriggerInlineVoiceInput()" class="p-2.5 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors border-none bg-transparent cursor-pointer flex items-center justify-center shrink-0" title="Voice Input / Dictation">
                <svg viewBox="0 0 24 24" width="17" height="17" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
            </button>
            <input type="text" id="cora-ai-input" class="cora-ai-input" placeholder="Ask Cora AI about your <?php echo esc_attr(strtolower($industry_name)); ?>, leads, invoices, bookings..." onkeydown="if(event.key==='Enter') coraSendChatMessage()">
            <button type="button" id="cora-ai-send-btn" class="cora-ai-btn" onclick="coraSendChatMessage()">
                <span>Ask</span>
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </div>
    </div>

    <!-- 2. LIVE VOICE ASSISTANT CANVAS -->
    <div id="cora-mcp-voice-view">
        <!-- Top Status Indicator -->
        <div class="flex items-center gap-2">
            <span id="cora-voice-status-pill" class="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-full text-xs font-semibold text-zinc-800 dark:text-zinc-200">
                <span id="cora-voice-status-dot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span id="cora-voice-status-text">Live Voice Ready &bull; Tap Orb or Mic to speak</span>
            </span>
        </div>

        <!-- Center Voice Orb -->
        <div class="cora-voice-orb-container">
            <div id="cora-voice-sphere-el" class="cora-voice-sphere" onclick="coraToggleLiveVoiceMic()">
                <svg viewBox="0 0 24 24" width="44" height="44" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
            </div>

            <div class="cora-voice-waveforms">
                <span class="cora-voice-wave-bar"></span>
                <span class="cora-voice-wave-bar"></span>
                <span class="cora-voice-wave-bar"></span>
                <span class="cora-voice-wave-bar"></span>
                <span class="cora-voice-wave-bar"></span>
                <span class="cora-voice-wave-bar"></span>
                <span class="cora-voice-wave-bar"></span>
                <span class="cora-voice-wave-bar"></span>
                <span class="cora-voice-wave-bar"></span>
            </div>
        </div>

        <!-- Realtime Spoken Transcript Card -->
        <div class="cora-voice-transcript-card">
            <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Live Transcript Stream</div>
            <p id="cora-voice-live-transcript" class="text-xs font-medium text-zinc-800 dark:text-zinc-200 leading-relaxed italic m-0">"Speak naturally to discuss strategy, audit cashflow, or schedule bookings..."</p>
        </div>

        <!-- Bottom Voice Control Strip -->
        <div class="cora-voice-controls-bar">
            <button type="button" onclick="coraSwitchAssistantMode('chat')" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xl text-xs font-bold border-none cursor-pointer flex items-center gap-1.5 transition-colors">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span>Switch to Text Chat</span>
            </button>

            <button type="button" id="cora-voice-mic-trigger-btn" onclick="coraToggleLiveVoiceMic()" class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:text-zinc-950 rounded-full text-xs font-bold border-none cursor-pointer flex items-center gap-2 shadow-md transition-transform active:scale-95">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
                <span id="cora-voice-mic-label">Tap to Speak</span>
            </button>

            <button type="button" onclick="coraToggleLiveAudioSpeaker()" id="cora-voice-speaker-btn" class="p-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl border-none cursor-pointer flex items-center justify-center transition-colors" title="Toggle Voice Response Audio">
                <svg id="cora-voice-speaker-icon" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- TAB 2: MCP Developer Gateway Settings                                     -->
<!-- ========================================================================= -->
<div id="cora-ai-panel-mcp-settings" class="space-y-6 w-full" style="display:none;">
    
    <!-- Connector 1: ChatGPT Custom GPT Actions -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-zinc-950 text-white flex items-center justify-center font-bold text-xs">
                    GPT
                </div>
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">ChatGPT Custom GPT Connector (OpenAPI 3.1.0)</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Connect ChatGPT directly to your workspace using Actions & Bearer Token authentication.</p>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-wider self-start sm:self-center">OpenAPI 3.1.0</span>
        </div>

        <div class="space-y-3">
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300">OpenAPI Schema URL</label>
                <div class="flex flex-col gap-2">
                    <input type="text" id="cora-mcp-openapi-url" readonly value="<?php echo esc_url( home_url( '/wp-json/cora/v1/mcp/openapi.json' ) ); ?>" class="w-full font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs px-3 py-2 outline-none text-zinc-850 dark:text-zinc-200">
                    <div class="grid grid-cols-2 sm:flex sm:flex-row gap-2">
                        <button type="button" class="w-full sm:w-auto px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-xl transition-colors cursor-pointer shrink-0" onclick="coraCopyToClipboardDirect('cora-mcp-openapi-url')">Copy Schema URL</button>
                        <button type="button" class="w-full sm:w-auto px-4 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-750 text-zinc-900 dark:text-zinc-100 font-bold text-xs rounded-xl transition-colors cursor-pointer shrink-0" onclick="coraFetchAndCopyOpenAPISchema()">Copy JSON Schema</button>
                    </div>
                </div>
            </div>

            <!-- ChatGPT Setup Instructions Accordion -->
            <details class="p-3 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-950 text-xs text-zinc-650 dark:text-zinc-400 space-y-2 cursor-pointer">
                <summary class="font-bold text-zinc-900 dark:text-zinc-100 flex items-center justify-between">
                    <span>How to configure ChatGPT Custom GPT Actions</span>
                    <span class="text-[10px] text-zinc-400">Expand Guide ▾</span>
                </summary>
                <ol class="list-decimal list-inside space-y-1.5 pt-2 text-zinc-600 dark:text-zinc-400">
                    <li>In ChatGPT, go to <strong>Explore GPTs</strong> &rarr; <strong>Create a GPT</strong> &rarr; <strong>Configure</strong>.</li>
                    <li>Scroll down and click <strong>Create new action</strong>.</li>
                    <li>Click <strong>Import from URL</strong>, paste the OpenAPI Schema URL copied above, and click <strong>Import</strong>.</li>
                    <li>Under <strong>Authentication</strong>, select <strong>API Key</strong> &rarr; Auth Type: <strong>Bearer</strong> &rarr; paste your Secure Access Token below.</li>
                    <li>Save your GPT. You can now prompt ChatGPT: <em>"Check our workspace revenue"</em> or <em>"List my CRM leads"</em>!</li>
                </ol>
            </details>
        </div>
    </div>

    <!-- Connector 2: Claude Desktop, Cursor & Antigravity IDE -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-zinc-950 text-white flex items-center justify-center font-bold text-xs">
                    MCP
                </div>
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Claude Desktop, Cursor & Antigravity IDE</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Native JSON-RPC 2.0 stdio / SSE bridge configuration.</p>
                </div>
            </div>
            <a href="<?php echo esc_url( CORA_WORKSPACE_URL . 'cora-bridge.py' ); ?>" download class="w-full sm:w-auto justify-center px-3 py-1.5 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-850 dark:text-zinc-200 text-xs font-bold transition-colors cursor-pointer flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Download Bridge Script
            </a>
        </div>

        <div class="space-y-2">
            <div class="bg-zinc-950 text-zinc-100 rounded-xl p-4 font-mono text-[11px] leading-relaxed overflow-x-auto shadow-inner relative border border-zinc-800">
                <button type="button" class="absolute top-3 right-3 px-2.5 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded text-[10px] font-bold cursor-pointer transition-colors" onclick="coraCopyClaudeConfigDirect()">Copy Config</button>
                <pre id="cora-claude-config-code-direct"><code>{
  "mcpServers": {
    "cora-workspace": {
      "command": "python3",
      "args": [
        "/path/to/cora-bridge.py",
        "<?php echo esc_url( $mcp_url ); ?>",
        "<?php echo esc_attr( $mcp_token ); ?>"
      ]
    }
  }
}</code></pre>
            </div>
        </div>
    </div>

    <!-- Human-Friendly AI Action & Natural Language Command Playground -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">AI Command Center & Action Playground</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Control your entire workspace using plain English natural language or guided actions.</p>
            </div>
            
            <!-- Mode Switcher Tabs -->
            <div class="inline-flex p-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl gap-1 text-xs font-semibold">
                <button type="button" id="cora-mcp-mode-nl-btn" onclick="coraSetMCPPlaygroundMode('nl')" class="px-3 py-1 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs cursor-pointer transition-all flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    Natural Language
                </button>
                <button type="button" id="cora-mcp-mode-guided-btn" onclick="coraSetMCPPlaygroundMode('guided')" class="px-3 py-1 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    Guided Actions
                </button>
                <button type="button" id="cora-mcp-mode-dev-btn" onclick="coraSetMCPPlaygroundMode('dev')" class="px-3 py-1 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                    Raw JSON
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Input Column -->
            <div class="space-y-4">
                
                <!-- MODE 1: Natural Language Commands (Default) -->
                <div id="cora-mcp-panel-nl" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">What would you like Cora AI to do?</label>
                        <div class="relative">
                            <textarea id="cora-mcp-nl-input" rows="3" class="w-full text-xs font-medium bg-zinc-50 dark:bg-zinc-950 border border-zinc-250 dark:border-zinc-800 rounded-xl p-3 outline-none text-zinc-850 dark:text-zinc-100 focus:border-zinc-900 dark:focus:border-zinc-100 transition-all placeholder:text-zinc-400" placeholder="Type in plain English, e.g.: 'Show me all unpaid invoices for this month' or 'Search for Rahul in CRM leads' or 'What are our scheduled shoots this week?'..."></textarea>
                        </div>
                    </div>

                    <!-- Quick Natural Language Suggestion Chips -->
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Try Natural Language Prompts:</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="coraSetNLPrompt('Give me a full workspace health summary with total revenue, unpaid receivables, and active bookings.')" class="px-2.5 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg text-[11px] font-medium transition-colors cursor-pointer text-left flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                                Health & Revenue Snapshot
                            </button>
                            <button type="button" onclick="coraSetNLPrompt('Check all unpaid client receivables and show overdue invoices.')" class="px-2.5 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg text-[11px] font-medium transition-colors cursor-pointer text-left flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                                Audit Unpaid Invoices
                            </button>
                            <button type="button" onclick="coraSetNLPrompt('List all active CRM deals and highlight hot leads.')" class="px-2.5 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg text-[11px] font-medium transition-colors cursor-pointer text-left flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                High-Priority CRM Leads
                            </button>
                            <button type="button" onclick="coraSetNLPrompt('Show all upcoming shoot bookings, call-times, and locations.')" class="px-2.5 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg text-[11px] font-medium transition-colors cursor-pointer text-left flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                Upcoming Shoot Schedule
                            </button>
                            <button type="button" onclick="coraSetNLPrompt('Search the living knowledge base for our latest commercial shoot contract terms.')" class="px-2.5 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg text-[11px] font-medium transition-colors cursor-pointer text-left flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                Search Contract Terms
                            </button>
                            <button type="button" onclick="coraSetNLPrompt('Show recent workspace activity pulse and audit trail.')" class="px-2.5 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg text-[11px] font-medium transition-colors cursor-pointer text-left flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                Activity Stream & Audit
                            </button>
                        </div>
                    </div>

                    <button type="button" id="cora-mcp-run-nl-btn" onclick="coraExecuteNaturalLanguageCommand()" class="w-full py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 shadow-xs">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        Execute Natural Language Command
                    </button>
                </div>

                <!-- MODE 2: Guided Action Presets -->
                <div id="cora-mcp-panel-guided" class="space-y-3" style="display: none;">
                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">Select Human-Friendly Action</label>
                        <select id="cora-mcp-guided-tool-select" class="w-full text-xs font-semibold bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 outline-none cursor-pointer text-zinc-850 dark:text-zinc-100" onchange="coraOnGuidedToolChange()">
                            <option value="cora_get_workspace_overview">Workspace Overview & KPI Snapshot</option>
                            <option value="cora_search_knowledge_base">Search Living Memory & Documents</option>
                            <option value="cora_query_financials">Check Financials & Invoices</option>
                            <option value="cora_record_financial_transaction">Record Payment or Expense</option>
                            <option value="cora_manage_crm_leads">Manage CRM Leads & Deals</option>
                            <option value="cora_manage_bookings">Shoot Bookings & Schedule</option>
                            <option value="cora_manage_tasks">Tasks & Client Deliverables</option>
                            <option value="cora_manage_documents">Vault Documents & Contracts</option>
                            <option value="cora_manage_reviews">Client Reviews & AI Replies</option>
                            <option value="cora_get_activity_pulse">Real-time Activity Pulse</option>
                            <option value="cora_ask_workspace_copilot">Ask AI Agent</option>
                        </select>
                    </div>

                    <!-- Dynamic Guided Form Fields -->
                    <div id="cora-mcp-guided-dynamic-fields" class="space-y-2 p-3 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-950">
                        <!-- Populated by JS -->
                    </div>

                    <button type="button" onclick="coraExecuteGuidedTool()" class="w-full py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 shadow-xs">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        Run Guided Action
                    </button>
                </div>

                <!-- MODE 3: Developer JSON Mode -->
                <div id="cora-mcp-panel-dev" class="space-y-3" style="display: none;">
                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">Developer Tool Schema</label>
                        <select id="cora-mcp-test-tool-select" class="w-full text-xs font-semibold bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 outline-none cursor-pointer text-zinc-850 dark:text-zinc-100" onchange="coraOnMCPToolSelectChange()">
                            <option value="cora_get_workspace_overview">cora_get_workspace_overview</option>
                            <option value="cora_search_knowledge_base">cora_search_knowledge_base</option>
                            <option value="cora_query_financials">cora_query_financials</option>
                            <option value="cora_record_financial_transaction">cora_record_financial_transaction</option>
                            <option value="cora_manage_crm_leads">cora_manage_crm_leads</option>
                            <option value="cora_manage_bookings">cora_manage_bookings</option>
                            <option value="cora_manage_tasks">cora_manage_tasks</option>
                            <option value="cora_manage_documents">cora_manage_documents</option>
                            <option value="cora_manage_reviews">cora_manage_reviews</option>
                            <option value="cora_get_activity_pulse">cora_get_activity_pulse</option>
                            <option value="cora_ask_workspace_copilot">cora_ask_workspace_copilot</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">JSON Arguments</label>
                        <textarea id="cora-mcp-test-tool-args" rows="4" class="w-full font-mono text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg p-2.5 outline-none text-zinc-800 dark:text-zinc-200">{}</textarea>
                    </div>

                    <button type="button" onclick="coraExecuteMCPTestTool()" class="w-full py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 shadow-xs">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        Execute Raw Tool Call
                    </button>
                </div>

            </div>

            <!-- Right Results & Formatted Output Column -->
            <div class="space-y-2 flex flex-col">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300">Live Response</label>
                    <div class="inline-flex gap-1 text-[10px] font-bold">
                        <button type="button" id="cora-mcp-view-formatted-btn" onclick="coraSetOutputView('formatted')" class="px-2 py-0.5 bg-zinc-900 text-white rounded cursor-pointer">Formatted</button>
                        <button type="button" id="cora-mcp-view-raw-btn" onclick="coraSetOutputView('raw')" class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded cursor-pointer">Raw JSON</button>
                    </div>
                </div>

                <!-- Formatted Output Card -->
                <div id="cora-mcp-formatted-output" class="flex-1 min-h-[220px] bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 rounded-2xl p-4 text-xs leading-relaxed border border-zinc-200 dark:border-zinc-800 overflow-y-auto whitespace-pre-wrap">
Ready to run your first natural language command or guided action. Type a request on the left or select a prompt chip to begin.
                </div>

                <!-- Raw JSON Output Container -->
                <div id="cora-mcp-test-output" class="flex-1 min-h-[220px] bg-zinc-950 text-zinc-200 rounded-2xl p-4 font-mono text-[11px] leading-relaxed overflow-y-auto border border-zinc-800 whitespace-pre-wrap" style="display: none;">
Ready to execute tool call...
                </div>
            </div>
        </div>
    </div>

    <!-- Developer Token & Credentials -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">MCP Secure Access Token</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Use this token to authenticate all external AI connections.</p>
        </div>

        <form id="cora-mcp-token-form" method="post" action="" class="space-y-4">
            <?php wp_nonce_field( 'cora_save_mcp_token_direct', 'cora_mcp_nonce' ); ?>
            <div class="space-y-2">
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="password" id="cora-mcp-access-token-direct" name="cora_mcp_access_token_direct" value="<?php echo esc_attr( $mcp_token ); ?>" class="w-full font-mono bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs px-3 py-2 outline-none cora-credential-input text-zinc-800 dark:text-zinc-200" oncopy="return false;" oncut="return false;" ondragstart="return false;" ondrop="return false;" autocomplete="off">
                    <div class="flex gap-2 w-full sm:w-auto shrink-0 justify-end">
                        <button type="button" class="flex-1 sm:flex-none px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-850 dark:text-zinc-200 font-bold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraToggleTokenVisibilityDirect()">Show</button>
                        <button type="button" class="flex-1 sm:flex-none px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-850 dark:text-zinc-200 font-bold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraGenerateNewMCPTokenDirect()">Regenerate</button>
                        <button type="submit" name="cora_save_mcp_token_direct_submit" class="flex-1 sm:flex-none px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-lg transition-colors cursor-pointer flex items-center justify-center gap-1.5 shadow-sm">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            Save Token
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- TAB 3: RAG Knowledge Base                                                 -->
<!-- ========================================================================= -->
<div id="cora-ai-panel-rag-settings" class="space-y-6 w-full" style="display:none;">
    <?php include CORA_WORKSPACE_PATH . 'views/view-rag.php'; ?>
</div>

<script>
    window.coraToggleAISettingsDrawer = function(open) {
        const drawer = document.getElementById('cora-ai-settings-drawer');
        const backdrop = document.getElementById('cora-ai-drawer-backdrop');
        if (!drawer || !backdrop) return;
        if (open) {
            drawer.classList.remove('translate-x-full');
            drawer.classList.add('translate-x-0');
            backdrop.classList.remove('hidden');
        } else {
            drawer.classList.remove('translate-x-0');
            drawer.classList.add('translate-x-full');
            backdrop.classList.add('hidden');
        }
    };

    // Global click dismiss for Model Popover
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('cora-model-selector-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            window.coraToggleModelPopover(false);
        }
    });

    // Auto-check URL hash on load
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash === '#rag-settings' || window.location.hash === '#rag') {
            coraSwitchAIPanel('rag-settings');
        } else if (window.location.hash === '#mcp-settings' || window.location.hash === '#mcp') {
            coraSwitchAIPanel('mcp-settings');
        }

        // Bind clicks directly to any tabs rendered
        document.querySelectorAll('.cora-sub-tabs-container .cora-sub-tab, #cora-sub-navigation-tabs button').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const target = this.getAttribute('data-target') || this.getAttribute('data-tab');
                if (target) {
                    coraSwitchAIPanel(target, this);
                }
            });
        });
    });

    // ─── Assistant Mode Switcher: Text Chat vs Live Voice ──────────────────────
    let coraActiveAssistantMode = 'chat';

    window.coraSwitchAssistantMode = function(mode) {
        coraActiveAssistantMode = mode;
        const chatView = document.getElementById('cora-mcp-chat-view');
        const voiceView = document.getElementById('cora-mcp-voice-view');
        const btnChat = document.getElementById('cora-assistant-tab-chat');
        const btnVoice = document.getElementById('cora-assistant-tab-voice');

        if (mode === 'voice') {
            if (chatView) chatView.style.display = 'none';
            if (voiceView) voiceView.style.display = 'flex';
            if (btnChat) btnChat.classList.remove('active');
            if (btnVoice) btnVoice.classList.add('active');
            coraInitLiveVoiceCanvas();
        } else {
            if (chatView) chatView.style.display = 'flex';
            if (voiceView) voiceView.style.display = 'none';
            if (btnChat) btnChat.classList.add('active');
            if (btnVoice) btnVoice.classList.remove('active');
            coraStopLiveVoice();
        }
    };

    // ─── Live Voice Assistant Engine ───────────────────────────────────────────
    let coraVoiceRecognition = null;
    let coraIsVoiceListening = false;
    let coraVoiceSpeakerActive = true;

    function coraInitLiveVoiceCanvas() {
        const transcriptEl = document.getElementById('cora-voice-live-transcript');
        const statusText = document.getElementById('cora-voice-status-text');
        const statusDot = document.getElementById('cora-voice-status-dot');
        const sphereEl = document.getElementById('cora-voice-sphere-el');
        const micLabel = document.getElementById('cora-voice-mic-label');

        if (statusText) statusText.textContent = "Listening... Speak naturally";
        if (statusDot) statusDot.className = "w-2 h-2 rounded-full bg-emerald-500 animate-pulse";
        if (sphereEl) {
            sphereEl.classList.add('listening');
            sphereEl.classList.remove('speaking');
        }
        if (micLabel) micLabel.textContent = "Mute Voice";

        coraStartSpeechRecognition();
    }

    function coraStartSpeechRecognition() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            window.coraShowToast("Web Speech Recognition is not supported on this browser.");
            return;
        }

        if (coraVoiceRecognition) {
            try { coraVoiceRecognition.stop(); } catch(e) {}
        }

        coraVoiceRecognition = new SpeechRecognition();
        coraVoiceRecognition.continuous = true;
        coraVoiceRecognition.interimResults = true;
        coraVoiceRecognition.lang = 'en-US';

        coraIsVoiceListening = true;

        coraVoiceRecognition.onresult = function(event) {
            let interimTranscript = '';
            let finalTranscript = '';

            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    finalTranscript += event.results[i][0].transcript;
                } else {
                    interimTranscript += event.results[i][0].transcript;
                }
            }

            const currentText = finalTranscript || interimTranscript;
            const transcriptEl = document.getElementById('cora-voice-live-transcript');
            if (transcriptEl && currentText) {
                transcriptEl.textContent = `"${currentText}"`;
            }

            if (finalTranscript && finalTranscript.trim().length > 3) {
                coraHandleSpokenVoiceQuery(finalTranscript.trim());
            }
        };

        coraVoiceRecognition.onerror = function(event) {
            console.warn('Speech recognition error:', event.error);
        };

        coraVoiceRecognition.onend = function() {
            if (coraIsVoiceListening && coraActiveAssistantMode === 'voice') {
                try { coraVoiceRecognition.start(); } catch(e) {}
            }
        };

        try {
            coraVoiceRecognition.start();
        } catch(e) {
            console.warn('Could not start recognition:', e);
        }
    }

    function coraStopLiveVoice() {
        coraIsVoiceListening = false;
        if (coraVoiceRecognition) {
            try { coraVoiceRecognition.stop(); } catch(e) {}
        }
        const sphereEl = document.getElementById('cora-voice-sphere-el');
        if (sphereEl) {
            sphereEl.classList.remove('listening', 'speaking');
        }
    }

    window.coraToggleLiveVoiceMic = function() {
        const micLabel = document.getElementById('cora-voice-mic-label');
        const statusText = document.getElementById('cora-voice-status-text');
        const statusDot = document.getElementById('cora-voice-status-dot');
        const sphereEl = document.getElementById('cora-voice-sphere-el');

        if (coraIsVoiceListening) {
            coraStopLiveVoice();
            if (micLabel) micLabel.textContent = "Tap to Speak";
            if (statusText) statusText.textContent = "Microphone muted. Tap to speak";
            if (statusDot) statusDot.className = "w-2 h-2 rounded-full bg-zinc-400";
            window.coraShowToast("Microphone muted.");
        } else {
            coraInitLiveVoiceCanvas();
            window.coraShowToast("Listening started...");
        }
    };

    window.coraToggleLiveAudioSpeaker = function() {
        coraVoiceSpeakerActive = !coraVoiceSpeakerActive;
        const icon = document.getElementById('cora-voice-speaker-icon');
        if (coraVoiceSpeakerActive) {
            if (icon) icon.innerHTML = '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>';
            window.coraShowToast("Voice audio output unmuted.");
        } else {
            if (icon) icon.innerHTML = '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><line x1="23" y1="9" x2="17" y2="15"></line><line x1="17" y1="9" x2="23" y2="15"></line>';
            const audioPlayer = document.getElementById('cora-ai-audio-player');
            if (audioPlayer) audioPlayer.pause();
            window.coraShowToast("Voice audio output muted.");
        }
    };

    function coraHandleSpokenVoiceQuery(spokenText) {
        const statusText = document.getElementById('cora-voice-status-text');
        const statusDot = document.getElementById('cora-voice-status-dot');
        const sphereEl = document.getElementById('cora-voice-sphere-el');

        if (statusText) statusText.textContent = "Processing query...";
        if (statusDot) statusDot.className = "w-2 h-2 rounded-full bg-amber-500 animate-pulse";
        if (sphereEl) {
            sphereEl.classList.remove('listening');
            sphereEl.classList.add('speaking');
        }

        const provider = document.getElementById('cora-ai-provider').value;
        const model = document.getElementById('cora-ai-model').value;
        const tone = document.getElementById('cora-mcp-ai-tone')?.value || 'executive';
        const systemPrompt = `You are Cora AI Voice Assistant. Respond in 1-2 concise, spoken English sentences. Tone: ${tone}.`;

        const ajaxUrlEndpoint = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php');

        jQuery.post(ajaxUrlEndpoint, {
            action: 'cora_ai_chat_query',
            security: (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '',
            message: spokenText,
            provider: provider,
            model: model,
            temperature: 0.6,
            system_prompt: systemPrompt
        }, function(res) {
            let data = res;
            if (typeof res === 'string') {
                try { data = JSON.parse(res); } catch(e) {}
            }

            if (data && data.success && data.data && data.data.reply) {
                const reply = data.data.reply;
                const transcriptEl = document.getElementById('cora-voice-live-transcript');
                if (transcriptEl) {
                    transcriptEl.innerHTML = `<span class="text-zinc-500">You: "${spokenText}"</span><br><span class="font-bold text-zinc-950 dark:text-zinc-100">Cora: "${reply.replace(/[*_`#]/g, '')}"</span>`;
                }

                if (statusText) statusText.textContent = "Cora Speaking...";
                if (statusDot) statusDot.className = "w-2 h-2 rounded-full bg-blue-500 animate-pulse";

                if (coraVoiceSpeakerActive) {
                    coraSpeakSynthesizedText(reply);
                } else {
                    setTimeout(() => {
                        if (coraActiveAssistantMode === 'voice') coraInitLiveVoiceCanvas();
                    }, 2500);
                }
            } else {
                if (statusText) statusText.textContent = "Listening... Speak naturally";
                if (statusDot) statusDot.className = "w-2 h-2 rounded-full bg-emerald-500 animate-pulse";
            }
        });
    }

    function coraSpeakSynthesizedText(text) {
        const clean = text.replace(/[*_`#\[\]]/g, '').trim();
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(clean);
            utterance.rate = 1.05;
            utterance.pitch = 1.0;
            utterance.onend = function() {
                if (coraActiveAssistantMode === 'voice') {
                    coraInitLiveVoiceCanvas();
                }
            };
            window.speechSynthesis.speak(utterance);
        } else {
            setTimeout(() => {
                if (coraActiveAssistantMode === 'voice') coraInitLiveVoiceCanvas();
            }, 3000);
        }
    }

    // Inline Mic Dictation trigger in chat mode
    window.coraTriggerInlineVoiceInput = function() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            window.coraShowToast("Speech recognition not supported on this device.");
            return;
        }

        const recognition = new SpeechRecognition();
        recognition.lang = 'en-US';
        recognition.interimResults = false;

        window.coraShowToast("Listening... Speak now");

        recognition.onresult = function(event) {
            if (event.results[0] && event.results[0][0]) {
                const transcript = event.results[0][0].transcript;
                const input = document.getElementById('cora-ai-input');
                if (input) {
                    input.value = transcript;
                    coraSendChatMessage();
                }
            }
        };
        recognition.start();
    };

    // ─── Chat Message Dispatch ─────────────────────────────────────────────────
    let conversationHistory = [];

    function coraClearConversation() {
        conversationHistory = [];
        const viewport = document.getElementById('cora-ai-messages');
        if (!viewport) return;
        viewport.innerHTML = `
            <div class="cora-ai-welcome" id="cora-ai-welcome-screen">
                <div class="cora-welcome-avatar">
                    <svg viewBox="0 0 24 24" width="26" height="26" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 2a8 8 0 0 0-8 8c0 3.31 2.01 6.16 4.9 7.37L8 21l3.5-1.5L15 21l-.9-3.63C16.99 16.16 19 13.31 19 10a8 8 0 0 0-7-8z"></path><circle cx="9" cy="10" r="1"></circle><circle cx="15" cy="10" r="1"></circle></svg>
                    <div class="cora-welcome-halo"></div>
                </div>
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center justify-center gap-1.5">
                        <span><?php echo esc_js( $greeting_time ); ?>,</span>
                        <span><?php echo esc_js( $user_first_name ?: $user_role_label ); ?></span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-md mt-1">Your personalized <?php echo esc_js( $industry_name ); ?> AI Co-Founder & Living Memory Second Brain is ready.</p>
                </div>
                <div class="cora-ai-chips">
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Give me a full financial health breakdown with revenue, outstanding invoices, and runway analysis.')">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                            <span>Financial Runway Analysis</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Audit revenue collected and unpaid client receivables.</p>
                    </div>
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Summarize our active CRM lead pipeline and highlight high-priority deals.')">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>CRM Pipeline Audit</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Identify high-intent deals in the conversion window.</p>
                    </div>
                </div>
            </div>
        `;
        window.coraShowToast("Conversation cleared.");
    }

    function coraUsePromptChip(promptText) {
        const input = document.getElementById('cora-ai-input');
        if (input) {
            input.value = promptText;
            coraSendChatMessage();
        }
    }

    function coraSendChatMessage() {
        const inputEl = document.getElementById('cora-ai-input');
        const promptText = (inputEl?.value || '').trim();
        if (!promptText) return;

        inputEl.value = '';
        const welcomeScreen = document.getElementById('cora-ai-welcome-screen');
        if (welcomeScreen) welcomeScreen.remove();

        coraAppendMessage('user', promptText);

        const startTime = Date.now();
        const loaderId = coraAppendMessage('assistant', `
            <div class="cora-skeleton-chat">
                <div class="cora-skeleton-line w-80"></div>
                <div class="cora-skeleton-line w-95"></div>
                <div class="cora-skeleton-line w-60"></div>
                <div class="text-[10px] text-zinc-400 mt-2 font-mono flex items-center gap-1.5">
                    <svg class="animate-spin inline-block" viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>Reasoning... <span class="cora-ai-timer-sec">0.0</span>s elapsed</span>
                </div>
            </div>
        `);

        const timerInterval = setInterval(() => {
            const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
            const timerEl = document.querySelector(`#${loaderId} .cora-ai-timer-sec`);
            if (timerEl) timerEl.textContent = elapsed;
        }, 100);

        const sendBtn = document.getElementById('cora-ai-send-btn');
        if (sendBtn) sendBtn.disabled = true;
        if (inputEl) inputEl.disabled = true;

        const provider = document.getElementById('cora-ai-provider').value;
        const model = document.getElementById('cora-ai-model').value;
        const temp = document.getElementById('cora-ai-temperature').value;
        const systemPrompt = document.getElementById('cora-ai-system').value;
        const ttsEnabled = document.getElementById('cora-ai-tts-toggle').checked;

        const ajaxUrlEndpoint = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php');

        jQuery.post(ajaxUrlEndpoint, {
            action: 'cora_ai_chat_query',
            security: (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '',
            message: promptText,
            provider: provider,
            model: model,
            temperature: temp,
            system_prompt: systemPrompt
        }, function(res) {
            clearInterval(timerInterval);
            const duration = ((Date.now() - startTime) / 1000).toFixed(1);

            if (sendBtn) sendBtn.disabled = false;
            if (inputEl) {
                inputEl.disabled = false;
                inputEl.focus();
            }

            const bubble = document.getElementById(loaderId);
            let data = res;
            if (typeof res === 'string') {
                try { data = JSON.parse(res); } catch(e) {
                    if (bubble) bubble.innerHTML = `<span class="text-red-500 font-bold">Invalid response format.</span>`;
                    coraScrollToBottom();
                    return;
                }
            }

            if (data && data.success && data.data && data.data.reply) {
                const replyText = data.data.reply;
                if (bubble) {
                    bubble.innerHTML = coraFormatAIResponse(replyText);
                    const chipsHtml = coraGetFollowupChips(promptText, replyText);

                    bubble.innerHTML += `
                        <div class="cora-ai-message-meta">
                            <span>${(data.data.provider || 'AI').toUpperCase()} / ${data.data.model || 'model'} &bull; ${duration}s</span>
                            <button type="button" class="cursor-pointer underline text-[9px] text-zinc-500 hover:text-zinc-700 bg-transparent border-none p-0" onclick="coraCopyMessageText(this)">Copy Text</button>
                        </div>
                        ${chipsHtml}
                    `;
                }

                if (ttsEnabled) {
                    coraPlayResponseAudio(replyText, bubble);
                }
            } else {
                const errMsg = (data && data.data && data.data.message) ? data.data.message : 'Error retrieving chat completion.';
                if (bubble) bubble.innerHTML = `<span class="text-red-500 font-bold">Error:</span> <span class="text-zinc-700 dark:text-zinc-300 text-xs">${errMsg}</span>`;
            }
            coraScrollToBottom();
        }).fail(function(xhr, status, error) {
            clearInterval(timerInterval);
            if (sendBtn) sendBtn.disabled = false;
            if (inputEl) inputEl.disabled = false;
            const bubble = document.getElementById(loaderId);
            if (bubble) {
                bubble.innerHTML = `<span class="text-red-500 font-bold">Network Request Failed (${xhr.status}):</span> <span class="text-zinc-600 text-xs">${error || 'Connection refused'}</span>`;
            }
            coraScrollToBottom();
        });
    }

    function coraAppendMessage(role, content) {
        const viewport = document.getElementById('cora-ai-messages');
        const msgId = 'cora-msg-' + Math.random().toString(36).substr(2, 9);
        const msgDiv = document.createElement('div');
        msgDiv.className = `cora-ai-message ${role}`;
        msgDiv.id = msgId;
        msgDiv.innerHTML = content;
        if (viewport) {
            viewport.appendChild(msgDiv);
            coraScrollToBottom();
        }
        return msgId;
    }

    function coraScrollToBottom() {
        const viewport = document.getElementById('cora-ai-messages');
        if (viewport) viewport.scrollTop = viewport.scrollHeight;
    }

    function coraFormatAIResponse(text) {
        return text
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`([^`]+)`/g, '<code class="bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded font-mono text-xs">$1</code>')
            .replace(/\n/g, '<br>');
    }

    function coraCopyMessageText(btnEl) {
        const msgDiv = btnEl.closest('.cora-ai-message');
        if (!msgDiv) return;
        const clone = msgDiv.cloneNode(true);
        const meta = clone.querySelector('.cora-ai-message-meta');
        if (meta) meta.remove();
        const chips = clone.querySelector('.cora-ai-followup-chips');
        if (chips) chips.remove();
        navigator.clipboard.writeText(clone.innerText.trim());
        window.coraShowToast("Copied text to clipboard.");
    }

    function coraPlayResponseAudio(text, bubbleElement) {
        const cleanedText = text.replace(/[*`#_]/g, '');
        window.coraShowToast("Synthesizing voice audio...");
        const ajaxUrlEndpoint = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';

        jQuery.post(ajaxUrlEndpoint, {
            action: 'cora_ai_generate_tts',
            security: (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '',
            text: cleanedText
        }, function(res) {
            if (res.success && res.data.audio) {
                const player = document.getElementById('cora-ai-audio-player');
                if (player) {
                    player.src = res.data.audio;
                    player.play();
                }
            }
        });
    }

    function coraGetFollowupChips(prompt, reply) {
        let chips = [
            'Provide the top 3 action items / next steps',
            'Summarize this into a 1-line briefing',
            'Query living memory for related records'
        ];
        let html = '<div class="cora-ai-followup-chips">';
        chips.forEach(chip => {
            const escaped = chip.replace(/'/g, "\\'");
            html += `<button type="button" class="cora-ai-followup-chip" onclick="coraUsePromptChip('${escaped}')">+ ${chip}</button>`;
        });
        html += '</div>';
        return html;
    }

    // Direct helper functions for MCP & Token
    function coraCopyToClipboardDirect(inputId) {
        var copyText = document.getElementById(inputId);
        if (!copyText) return;
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        window.coraShowToast("Copied to clipboard.");
    }

    function coraFetchAndCopyOpenAPISchema() {
        const url = '<?php echo esc_url( home_url( "/wp-json/cora/v1/mcp/openapi.json" ) ); ?>';
        fetch(url).then(r => r.text()).then(t => {
            navigator.clipboard.writeText(t);
            window.coraShowToast("OpenAPI JSON Schema copied to clipboard.");
        }).catch(e => {
            window.coraShowToast("Could not fetch schema: " + e.message);
        });
    }

    function coraCopyClaudeConfigDirect() {
        var codeText = document.getElementById("cora-claude-config-code-direct").innerText;
        navigator.clipboard.writeText(codeText);
        window.coraShowToast("Claude configuration copied to clipboard.");
    }

    function coraToggleTokenVisibilityDirect() {
        var x = document.getElementById("cora-mcp-access-token-direct");
        if (!x) return;
        x.type = (x.type === "password") ? "text" : "password";
    }

    function coraGenerateNewMCPTokenDirect() {
        window.coraConfirmAction(
            'Regenerate MCP Token',
            'Are you sure you want to regenerate the secure token? Current active AI tools connections will immediately fail authentication.',
            function() {
                var chars = 'abcdef0123456789';
                var newToken = '';
                for (var i = 0; i < 32; i++) {
                    newToken += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                document.getElementById("cora-mcp-access-token-direct").value = newToken;
                window.coraShowToast("New secure token generated. Save to persist.");
            }
        );
    }

    // Playground Controls
    let coraActiveMCPPlaygroundMode = 'nl';
    function coraSetMCPPlaygroundMode(mode) {
        coraActiveMCPPlaygroundMode = mode;
        const panelNL = document.getElementById('cora-mcp-panel-nl');
        const panelGuided = document.getElementById('cora-mcp-panel-guided');
        const panelDev = document.getElementById('cora-mcp-panel-dev');
        const btnNL = document.getElementById('cora-mcp-mode-nl-btn');
        const btnGuided = document.getElementById('cora-mcp-mode-guided-btn');
        const btnDev = document.getElementById('cora-mcp-mode-dev-btn');

        [panelNL, panelGuided, panelDev].forEach(p => { if (p) p.style.display = 'none'; });
        [btnNL, btnGuided, btnDev].forEach(b => {
            if (b) b.className = 'px-3 py-1 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all';
        });

        if (mode === 'nl') {
            if (panelNL) panelNL.style.display = 'block';
            if (btnNL) btnNL.className = 'px-3 py-1 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs cursor-pointer transition-all font-bold';
        } else if (mode === 'guided') {
            if (panelGuided) panelGuided.style.display = 'block';
            if (btnGuided) btnGuided.className = 'px-3 py-1 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs cursor-pointer transition-all font-bold';
            coraOnGuidedToolChange();
        } else if (mode === 'dev') {
            if (panelDev) panelDev.style.display = 'block';
            if (btnDev) btnDev.className = 'px-3 py-1 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs cursor-pointer transition-all font-bold';
        }
    }

    function coraSetNLPrompt(promptText) {
        const input = document.getElementById('cora-mcp-nl-input');
        if (input) {
            input.value = promptText;
            input.focus();
        }
    }

    function coraSetOutputView(view) {
        const formattedBox = document.getElementById('cora-mcp-formatted-output');
        const rawBox = document.getElementById('cora-mcp-test-output');
        const btnFormatted = document.getElementById('cora-mcp-view-formatted-btn');
        const btnRaw = document.getElementById('cora-mcp-view-raw-btn');

        if (view === 'formatted') {
            if (formattedBox) formattedBox.style.display = 'block';
            if (rawBox) rawBox.style.display = 'none';
            if (btnFormatted) btnFormatted.className = 'px-2 py-0.5 bg-zinc-900 text-white rounded cursor-pointer font-bold';
            if (btnRaw) btnRaw.className = 'px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded cursor-pointer font-medium';
        } else {
            if (formattedBox) formattedBox.style.display = 'none';
            if (rawBox) rawBox.style.display = 'block';
            if (btnFormatted) btnFormatted.className = 'px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded cursor-pointer font-medium';
            if (btnRaw) btnRaw.className = 'px-2 py-0.5 bg-zinc-900 text-white rounded cursor-pointer font-bold';
        }
    }

    async function coraExecuteNaturalLanguageCommand() {
        const promptInput = document.getElementById('cora-mcp-nl-input');
        const query = (promptInput?.value || '').trim();
        if (!query) {
            window.coraShowToast("Please enter a command.");
            return;
        }

        const runBtn = document.getElementById('cora-mcp-run-nl-btn');
        const formattedBox = document.getElementById('cora-mcp-formatted-output');
        const rawBox = document.getElementById('cora-mcp-test-output');
        const token = document.getElementById('cora-mcp-access-token-direct').value;
        const mcpUrl = '<?php echo esc_url( home_url( "/wp-json/cora/v1/mcp" ) ); ?>';

        if (runBtn) {
            runBtn.disabled = true;
            runBtn.innerHTML = '<span class="animate-spin inline-block mr-1">⟳</span> Executing...';
        }

        if (formattedBox) formattedBox.innerHTML = '<div class="flex items-center gap-2 text-zinc-500"><span class="animate-spin text-sm">⟳</span> Running workspace intelligence...</div>';

        try {
            const res = await fetch(mcpUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({
                    jsonrpc: '2.0',
                    method: 'tools/call',
                    params: {
                        name: 'cora_ask_workspace_copilot',
                        arguments: { question: query }
                    },
                    id: Date.now()
                })
            });

            const data = await res.json();
            if (rawBox) rawBox.innerText = JSON.stringify(data, null, 2);

            if (data.result && data.result.content && data.result.content[0]) {
                const text = data.result.content[0].text;
                if (formattedBox) formattedBox.innerHTML = `<div class="font-sans text-xs text-zinc-800 dark:text-zinc-200 leading-relaxed whitespace-pre-wrap">${text}</div>`;
                window.coraShowToast("Command executed.");
            } else if (data.error) {
                if (formattedBox) formattedBox.innerHTML = `<div class="p-3 rounded-lg bg-red-50 text-red-700 text-xs font-medium">Error: ${data.error.message || 'Error'}</div>`;
            }
        } catch (e) {
            if (formattedBox) formattedBox.innerHTML = `<div class="p-3 rounded-lg bg-red-50 text-red-700 text-xs font-medium">Network Error: ${e.message}</div>`;
        } finally {
            if (runBtn) {
                runBtn.disabled = false;
                runBtn.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg> Execute Natural Language Command';
            }
        }
    }

    function coraOnGuidedToolChange() {
        const tool = document.getElementById('cora-mcp-guided-tool-select')?.value;
        const container = document.getElementById('cora-mcp-guided-dynamic-fields');
        if (!container) return;
        container.innerHTML = `<p class="text-xs text-zinc-500 m-0">Action <code>${tool}</code> selected. Click Run Guided Action to execute with current workspace context.</p>`;
    }

    async function coraExecuteGuidedTool() {
        const tool = document.getElementById('cora-mcp-guided-tool-select')?.value;
        const formattedBox = document.getElementById('cora-mcp-formatted-output');
        const rawBox = document.getElementById('cora-mcp-test-output');
        const token = document.getElementById('cora-mcp-access-token-direct').value;
        const mcpUrl = '<?php echo esc_url( home_url( "/wp-json/cora/v1/mcp" ) ); ?>';

        if (formattedBox) formattedBox.innerHTML = '<div class="text-zinc-500"><span class="animate-spin inline-block mr-1">⟳</span> Executing guided action...</div>';

        try {
            const res = await fetch(mcpUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({
                    jsonrpc: '2.0',
                    method: 'tools/call',
                    params: { name: tool, arguments: {} },
                    id: Date.now()
                })
            });
            const data = await res.json();
            if (rawBox) rawBox.innerText = JSON.stringify(data, null, 2);
            if (data.result && data.result.content && data.result.content[0]) {
                if (formattedBox) formattedBox.innerHTML = `<div class="font-sans text-xs text-zinc-800 dark:text-zinc-200 whitespace-pre-wrap">${data.result.content[0].text}</div>`;
                window.coraShowToast("Guided action executed.");
            }
        } catch(e) {
            if (formattedBox) formattedBox.innerText = "Error: " + e.message;
        }
    }

    async function coraExecuteMCPTestTool() {
        const tool = document.getElementById('cora-mcp-test-tool-select')?.value;
        const rawArgs = document.getElementById('cora-mcp-test-tool-args')?.value || '{}';
        const rawBox = document.getElementById('cora-mcp-test-output');
        const formattedBox = document.getElementById('cora-mcp-formatted-output');
        const token = document.getElementById('cora-mcp-access-token-direct').value;
        const mcpUrl = '<?php echo esc_url( home_url( "/wp-json/cora/v1/mcp" ) ); ?>';

        let parsedArgs = {};
        try { parsedArgs = JSON.parse(rawArgs); } catch(e) { window.coraShowToast("Invalid JSON in arguments."); return; }

        if (rawBox) rawBox.innerText = "Executing raw tool call...";
        try {
            const res = await fetch(mcpUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({
                    jsonrpc: '2.0',
                    method: 'tools/call',
                    params: { name: tool, arguments: parsedArgs },
                    id: Date.now()
                })
            });
            const data = await res.json();
            if (rawBox) rawBox.innerText = JSON.stringify(data, null, 2);
            if (data.result && data.result.content && data.result.content[0]) {
                if (formattedBox) formattedBox.innerText = data.result.content[0].text;
            }
            window.coraShowToast("Raw tool call finished.");
        } catch(e) {
            if (rawBox) rawBox.innerText = "Error: " + e.message;
        }
    }

    function coraOnMCPToolSelectChange() {
        const tool = document.getElementById('cora-mcp-test-tool-select')?.value;
        const args = document.getElementById('cora-mcp-test-tool-args');
        if (args) args.value = '{}';
    }

    function coraTriggerReindexLivingMemory(btn) {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin inline-block mr-1">⟳</span> Re-Indexing Memory...';
        }
        window.coraShowToast("Starting living memory re-index sweep...");
        const ajaxUrlEndpoint = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';

        jQuery.post(ajaxUrlEndpoint, {
            action: 'cora_rag_reindex_knowledge',
            security: (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : ''
        }, function(resp) {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg> Re-Index Workspace Knowledge';
            }
            if (resp.success) {
                window.coraShowToast(resp.data.message || "Living memory updated.");
                setTimeout(() => location.reload(), 1200);
            } else {
                window.coraShowToast(resp.data?.message || "Re-indexing error.");
            }
        });
    }

    // Expose globally
    window.coraSendChatMessage = coraSendChatMessage;
</script>
