<?php
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
?>
<style>
    /* AI Chat Workspace Scoped Styles */
    #cora-page-mcp {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .cora-ai-tabs {
        display: flex;
        border-bottom: 1px solid var(--border-color, #e4e4e7);
        gap: 16px;
        margin-bottom: 8px;
    }
    .cora-ai-tab {
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary, #71717a);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .cora-ai-tab.active {
        color: var(--text-primary, #09090b);
        border-bottom-color: var(--text-primary, #09090b);
    }
    .cora-ai-workspace {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 20px;
        min-height: 550px;
        background: #fff;
        border: 1px solid #e4e4e7;
        border-radius: 16px;
        overflow: hidden;
    }
    .dark .cora-ai-workspace {
        background: #18181b;
        border-color: #27272a;
    }
    .cora-ai-sidebar {
        background: #fafafa;
        border-right: 1px solid #e4e4e7;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        font-size: 12px;
    }
    .dark .cora-ai-sidebar {
        background: #121214;
        border-right-color: #27272a;
    }
    .cora-ai-chat-container {
        display: flex;
        flex-direction: column;
        height: 600px;
        position: relative;
    }
    .cora-ai-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .cora-ai-message {
        display: flex;
        flex-direction: column;
        max-width: 85%;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        line-height: 1.5;
        animation: coraFadeIn 0.3s ease;
    }
    @keyframes coraFadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .cora-ai-message.user {
        align-self: flex-end;
        background: #09090b;
        color: #fff;
    }
    .dark .cora-ai-message.user {
        background: #f4f4f5;
        color: #09090b;
    }
    .cora-ai-message.assistant {
        align-self: flex-start;
        background: #f4f4f5;
        color: #18181b;
        border: 1px solid #e4e4e7;
    }
    .dark .cora-ai-message.assistant {
        background: #27272a;
        color: #f4f4f5;
        border-color: #3f3f46;
    }
    .cora-skeleton-chat {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 180px;
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
        0%, 100% { opacity: 0.65; }
        50% { opacity: 1; }
    }
    @keyframes coraSpin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .cora-ai-spin {
        animation: coraSpin 1.5s linear infinite;
    }
    .cora-ai-followup-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
        border-top: 1px dashed #e4e4e7;
        padding-top: 8px;
    }
    .dark .cora-ai-followup-chips {
        border-top-color: #3f3f46;
    }
    .cora-ai-followup-chip {
        background: #f4f4f5;
        border: 1px solid #e4e4e7;
        border-radius: 9999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 500;
        color: #52525b;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
    }
    .dark .cora-ai-followup-chip {
        background: #27272a;
        border-color: #3f3f46;
        color: #a1a1aa;
    }
    .cora-ai-followup-chip:hover {
        background: #e4e4e7;
        color: #18181b;
        border-color: #d4d4d8;
    }
    .dark .cora-ai-followup-chip:hover {
        background: #3f3f46;
        color: #f4f4f5;
        border-color: #52525b;
    }
    .cora-ai-message-meta {
        font-size: 9px;
        opacity: 0.6;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .cora-ai-input-wrapper {
        padding: 16px;
        border-top: 1px solid #e4e4e7;
        display: flex;
        gap: 8px;
        align-items: center;
        background: #fff;
    }
    .dark .cora-ai-input-wrapper {
        border-top-color: #27272a;
        background: #18181b;
    }
    .cora-ai-input {
        flex: 1;
        border: 1px solid #e4e4e7;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        outline: none;
        background: transparent;
        color: inherit;
    }
    .dark .cora-ai-input {
        border-color: #27272a;
    }
    .cora-ai-input:focus {
        border-color: #09090b;
    }
    .dark .cora-ai-input:focus {
        border-color: #f4f4f5;
    }
    .cora-ai-btn {
        padding: 10px;
        background: #09090b;
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
    }
    .dark .cora-ai-btn {
        background: #f4f4f5;
        color: #09090b;
    }
    .cora-ai-btn:hover {
        opacity: 0.9;
    }
    .cora-ai-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .cora-ai-welcome {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        padding: 40px;
        gap: 20px;
    }
    .cora-ai-chips {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        max-width: 500px;
        margin-top: 10px;
    }
    .cora-ai-chip {
        padding: 10px;
        border: 1px solid #e4e4e7;
        border-radius: 8px;
        cursor: pointer;
        font-size: 11px;
        text-align: left;
        transition: all 0.2s;
    }
    .dark .cora-ai-chip {
        border-color: #27272a;
    }
    .cora-ai-chip:hover {
        background: #f4f4f5;
    }
    .dark .cora-ai-chip:hover {
        background: #27272a;
    }
    .cora-ai-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .cora-ai-field label {
        font-weight: 600;
        color: var(--text-primary);
    }
    .cora-ai-select, .cora-ai-textarea {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid #e4e4e7;
        border-radius: 6px;
        font-size: 12px;
        background: #fff;
        color: inherit;
        outline: none;
    }
    .dark .cora-ai-select, .dark .cora-ai-textarea {
        border-color: #27272a;
        background: #18181b;
    }
    @media (max-width: 768px) {
        .cora-ai-tabs {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 4px;
        }
        .cora-ai-tab {
            flex-shrink: 0;
            padding: 8px 12px;
            font-size: 12px;
        }
        .cora-ai-workspace {
            grid-template-columns: 1fr;
            min-height: auto;
        }
        .cora-ai-sidebar {
            border-right: none;
            border-bottom: 1px solid #e4e4e7;
        }
        .dark .cora-ai-sidebar {
            border-bottom-color: #27272a;
        }
    }
    @media (max-width: 480px) {
        .cora-ai-chips {
            grid-template-columns: 1fr;
        }
        .cora-ai-welcome {
            padding: 20px;
        }
    }
</style>

<!-- Tabs Navigation -->
<div class="cora-ai-tabs">
    <div class="cora-ai-tab active" onclick="coraSwitchAIPanel('chat')">AI Chat Assistant</div>
    <div class="cora-ai-tab" onclick="coraSwitchAIPanel('mcp-settings')">MCP Developer Gateway</div>
    <div class="cora-ai-tab" onclick="coraSwitchAIPanel('rag-settings')">RAG Knowledge Base</div>
</div>

<!-- TAB 1: AI Chat Assistant -->
<div id="cora-ai-panel-chat" class="cora-ai-workspace">
    <!-- Left Sidebar Settings -->
    <div class="cora-ai-sidebar">
        <div class="cora-ai-field">
            <label>AI Provider</label>
            <select id="cora-ai-provider" class="cora-ai-select" onchange="coraOnProviderChange()">
                <option value="gemini" selected>Google Gemini</option>
                <option value="groq">Groq</option>
                <option value="openrouter">OpenRouter</option>
                <option value="llama_nv">Llama (NVIDIA)</option>
                <option value="deepseek_nv" disabled>DeepSeek (NVIDIA) (Coming Soon)</option>
                <option value="gemma_nv" disabled>Gemma (NVIDIA) (Coming Soon)</option>
                <option value="gpt_oss_nv">GPT OSS (NVIDIA)</option>
                <option value="glm_nv" disabled>GLM (NVIDIA) (Coming Soon)</option>
                <option value="minimax_nv" disabled>Minimax (NVIDIA) (Coming Soon)</option>
                <option value="moonshot_nv" disabled>Moonshot (NVIDIA) (Coming Soon)</option>
            </select>
        </div>

        <div class="cora-ai-field">
            <label>Model Selector</label>
            <select id="cora-ai-model" class="cora-ai-select"></select>
        </div>

        <div class="cora-ai-field">
            <label>Temperature (<span id="cora-ai-temp-val">0.7</span>)</label>
            <input type="range" id="cora-ai-temperature" min="0" max="1" step="0.1" value="0.7" oninput="document.getElementById('cora-ai-temp-val').innerText = this.value" class="w-full">
        </div>

        <div class="cora-ai-field border-t border-zinc-200 pt-3">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="cora-ai-tts-toggle">
                ElevenLabs Audio Output
            </label>
        </div>

        <div class="cora-ai-field border-t border-zinc-200 pt-3">
            <label>System Instructions</label>
            <textarea id="cora-ai-system" rows="5" class="cora-ai-textarea">You are Cora AI, the unified platform assistant for the Cora Workspace Platform. Help users with leads, billing calculations, and setting queries.</textarea>
        </div>

        <button type="button" class="mt-auto px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 rounded-lg text-center font-semibold transition-colors" onclick="coraClearConversation()">Clear Conversation</button>
    </div>

    <!-- Chat Window -->
    <div class="cora-ai-chat-container">
        <div id="cora-ai-messages" class="cora-ai-messages">
            <!-- Welcome Screen -->
            <div class="cora-ai-welcome" id="cora-ai-welcome-screen">
                <div class="w-12 h-12 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800 border border-zinc-200 shadow-sm">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-zinc-900 ">Welcome to Cora AI</h3>
                    <p class="text-xs text-zinc-500 max-w-sm mt-1">Ask questions, generate draft assets, or perform GST calculations in a few quick keystrokes.</p>
                </div>
                <div class="cora-ai-chips">
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Draft a contract for a property sale')">
                        <strong>Draft Contract</strong>
                        <p class="text-zinc-400 mt-1">Draft a contract for a property sale.</p>
                    </div>
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Calculate GST for a ₹50,000 booking split between Delhi and Jaipur')">
                        <strong>GST Split Math</strong>
                        <p class="text-zinc-400 mt-1">Calculate GST for ₹50,000 Delhi-Jaipur split.</p>
                    </div>
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Explain the geofenced office location coordinate checking')">
                        <strong>Geofencing Help</strong>
                        <p class="text-zinc-400 mt-1">Explain coordinate checking bounds.</p>
                    </div>
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('How do I manage workspace API roles?')">
                        <strong>Roles Governance</strong>
                        <p class="text-zinc-400 mt-1">How do I manage workspace API roles?</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audio Playback element -->
        <audio id="cora-ai-audio-player" style="display:none;"></audio>

        <div class="cora-ai-input-wrapper">
            <input type="text" id="cora-ai-input" class="cora-ai-input" placeholder="Ask Cora AI..." onkeydown="if(event.key==='Enter') coraSendChatMessage()">
            <button type="button" id="cora-ai-send-btn" class="cora-ai-btn" onclick="coraSendChatMessage()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </div>
    </div>
</div>

<!-- TAB 2: MCP Developer Gateway Settings -->
<div id="cora-ai-panel-mcp-settings" class="space-y-6 max-w-3xl" style="display:none;">
    <!-- Developer Preview & Token Generation -->
    <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-xs space-y-4">
        <div class="border-b border-zinc-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 ">Model Context Protocol (MCP) AI Tools Server</h3>
                <p class="text-xs text-zinc-500 mt-0.5">Connect your custom external AI agents directly with Cora's data schemas.</p>
            </div>
            <span class="px-2.5 py-0.5 rounded-full bg-zinc-100 text-zinc-850 text-[9px] font-bold uppercase tracking-wider self-start sm:self-center">Beta Gateway</span>
        </div>

        <div class="p-4 border border-zinc-200 rounded-xl bg-zinc-50/50 ">
            <p class="text-xs text-zinc-650 leading-relaxed">
                Cora exposes an <strong>MCP tool server</strong> endpoint. By registering this gateway in your local AI platform (like Claude Desktop or Cursor), your AI assistant can query listings, create leads, check audit logs, and retrieve workspace statistics in real-time.
            </p>
        </div>

        <!-- MCP Gateway URL -->
        <div class="space-y-2">
            <label class="block text-xs font-bold text-zinc-700 ">MCP Gateway Endpoint URL</label>
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" id="cora-mcp-gateway-url-direct" readonly value="<?php echo esc_url( $mcp_url ); ?>" class="w-full font-mono bg-zinc-55/40 border border-zinc-200 rounded-lg text-xs px-3 py-2 outline-none">
                <button type="button" class="w-full sm:w-auto px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-lg transition-colors cursor-pointer shrink-0" onclick="coraCopyToClipboardDirect('cora-mcp-gateway-url-direct')">Copy URL</button>
            </div>
        </div>

        <!-- MCP Secure Token -->
        <form id="cora-mcp-token-form" method="post" action="" class="space-y-4 pt-2">
            <?php wp_nonce_field( 'cora_save_mcp_token_direct', 'cora_mcp_nonce' ); ?>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-zinc-700 ">Secure Bearer Access Token</label>
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="password" id="cora-mcp-access-token-direct" name="cora_mcp_access_token_direct" value="<?php echo esc_attr( $mcp_token ); ?>" class="w-full font-mono bg-white border border-zinc-200 rounded-lg text-xs px-3 py-2 outline-none cora-credential-input" oncopy="return false;" oncut="return false;" ondragstart="return false;" ondrop="return false;" autocomplete="off">
                    <div class="flex gap-2 w-full sm:w-auto shrink-0 justify-end">
                        <button type="button" class="flex-1 sm:flex-none px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-850 font-bold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraToggleTokenVisibilityDirect()">Show</button>
                        <button type="button" class="flex-1 sm:flex-none px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-850 font-bold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraGenerateNewMCPTokenDirect()">Regenerate</button>
                        <button type="submit" name="cora_save_mcp_token_direct_submit" class="flex-1 sm:flex-none px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-lg transition-colors cursor-pointer flex items-center justify-center gap-1.5 shadow-sm">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            Save Token
                        </button>
                    </div>
                </div>
                <p class="text-[10px] text-zinc-400 mt-1">Authenticate requests by sending this value in the HTTP header: <code>Authorization: Bearer &lt;token&gt;</code>.</p>
            </div>
        </form>
    </div>

    <!-- Configuration Example Card -->
    <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-xs space-y-4 mt-6">
        <div class="border-b border-zinc-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 ">Claude Desktop & Cursor Integration</h3>
                <p class="text-xs text-zinc-500 mt-0.5">Use the local stdio bridge script to connect external AI agents to Cora.</p>
            </div>
            <a href="<?php echo esc_url( CORA_WORKSPACE_URL . 'cora-bridge.py' ); ?>" download class="w-full sm:w-auto justify-center px-2.5 py-1.5 rounded bg-zinc-100 hover:bg-zinc-200 text-zinc-850 text-[10px] font-bold transition-colors cursor-pointer flex items-center gap-1">
                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Download Bridge Script
            </a>
        </div>

        <div class="p-4 border border-zinc-200 rounded-xl bg-zinc-50/50 space-y-2">
            <h4 class="text-xs font-bold text-zinc-850 ">How to Setup:</h4>
            <ol class="list-decimal list-inside text-xs text-zinc-650 space-y-1">
                <li>Click the button above to download the <code>cora-bridge.py</code> script.</li>
                <li>Save the script to a stable folder on your local computer (e.g. <code>/Users/yourname/cora-bridge.py</code>).</li>
                <li>Open your local AI client settings configuration (e.g. <code>~/Library/Application Support/Claude/claude_desktop_config.json</code>).</li>
                <li>Copy and paste the configuration code block below into your settings file, ensuring you replace the local script path.</li>
            </ol>
        </div>
        
        <div class="space-y-2">
            <div class="bg-zinc-900 text-zinc-100 rounded-xl p-4 font-mono text-[10px] leading-relaxed overflow-x-auto shadow-inner relative">
                <button type="button" class="absolute top-3 right-3 px-2 py-1 bg-zinc-800 hover:bg-zinc-750 text-zinc-300 rounded text-[9px] cursor-pointer" onclick="coraCopyClaudeConfigDirect()">Copy Config</button>
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
</div>

<!-- TAB 3: RAG Knowledge Base -->
<div id="cora-ai-panel-rag-settings" class="space-y-6 max-w-4xl" style="display:none;">
    <?php include CORA_WORKSPACE_PATH . 'views/view-rag.php'; ?>
</div>

<script>
    // Models list mapping
    const coraAIModels = {
        groq: [
            { value: 'llama-3.1-8b-instant', label: 'Llama 3.1 8b Instant (Default)' },
            { value: 'mixtral-8x7b-32768', label: 'Mixtral 8x7b' }
        ],
        openrouter: [
            { value: 'meta-llama/llama-3.1-8b-instruct', label: 'Llama 3.1 8b Instruct (Default)' },
            { value: 'meta-llama/llama-3.1-70b-instruct', label: 'Llama 3.1 70b Instruct' }
        ],
        gemini: [
            { value: 'gemini-flash-latest', label: 'Gemini Flash (Default)' },
            { value: 'gemini-flash-lite-latest', label: 'Gemini Flash Lite' }
        ],
        llama_nv: [
            { value: 'meta/llama-3.1-70b-instruct', label: 'Llama 3.1 70b Instruct (Working - Default)' },
            { value: 'meta/llama-3.1-8b-instruct', label: 'Llama 3.1 8b Instruct (Working)' },
            { value: 'meta/llama-3.3-70b-instruct', label: 'Llama 3.3 70b Instruct' },
            { value: 'meta/llama-3.2-3b-instruct', label: 'Llama 3.2 3b Instruct' },
            { value: 'meta/llama-3.2-1b-instruct', label: 'Llama 3.2 1b Instruct' },
            { value: 'llama-guard-4-12b', label: 'Llama Guard 4 12b' }
        ],
        deepseek_nv: [
            { value: 'deepseek-ai/deepseek-v4-flash', label: 'DeepSeek v4 Flash' },
            { value: 'deepseek-ai/deepseek-v4-pro', label: 'DeepSeek v4 Pro' }
        ],
        gemma_nv: [
            { value: 'google/gemma-4-31b-it', label: 'Gemma 4 31b IT' }
        ],
        gpt_oss_nv: [
            { value: 'openai/gpt-oss-20b', label: 'GPT OSS 20b (Working - Default)' },
            { value: 'openai/gpt-oss-120b', label: 'GPT OSS 120b' }
        ],
        glm_nv: [
            { value: 'z-ai/glm-5.2', label: 'GLM 5.2' }
        ],
        minimax_nv: [
            { value: 'minimaxai/minimax-m3', label: 'Minimax M3' }
        ],
        moonshot_nv: [
            { value: 'moonshotai/kimi-k2.6', label: 'Moonshot kimi-k2.6' }
        ]
    };

    function coraOnProviderChange() {
        const provider = document.getElementById('cora-ai-provider').value;
        const modelSelect = document.getElementById('cora-ai-model');
        modelSelect.innerHTML = '';
        coraAIModels[provider].forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.value;
            opt.innerText = m.label;
            modelSelect.appendChild(opt);
        });
    }

    // On page load setup models
    coraOnProviderChange();

    function coraSwitchAIPanel(panelId) {
        const tabs = document.querySelectorAll('.cora-ai-tab');
        const chatPanel = document.getElementById('cora-ai-panel-chat');
        const settingsPanel = document.getElementById('cora-ai-panel-mcp-settings');
        const ragPanel = document.getElementById('cora-ai-panel-rag-settings');

        tabs.forEach(t => t.classList.remove('active'));
        if (panelId === 'chat') {
            tabs[0].classList.add('active');
            chatPanel.style.display = 'grid';
            settingsPanel.style.display = 'none';
            if (ragPanel) ragPanel.style.display = 'none';
        } else if (panelId === 'mcp-settings') {
            tabs[1].classList.add('active');
            chatPanel.style.display = 'none';
            settingsPanel.style.display = 'block';
            if (ragPanel) ragPanel.style.display = 'none';
        } else if (panelId === 'rag-settings') {
            tabs[2].classList.add('active');
            chatPanel.style.display = 'none';
            settingsPanel.style.display = 'none';
            if (ragPanel) ragPanel.style.display = 'block';
        }
    }

    function coraCopyToClipboardDirect(inputId) {
        var copyText = document.getElementById(inputId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        window.coraShowToast("Copied to clipboard.");
    }

    function coraToggleTokenVisibilityDirect() {
        var x = document.getElementById("cora-mcp-access-token-direct");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
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

    function coraCopyClaudeConfigDirect() {
        var codeText = document.getElementById("cora-claude-config-code-direct").innerText;
        navigator.clipboard.writeText(codeText);
        window.coraShowToast("Claude configuration copied to clipboard.");
    }

    // Conversation Management
    let conversationHistory = [];

    function coraClearConversation() {
        conversationHistory = [];
        const viewport = document.getElementById('cora-ai-messages');
        viewport.innerHTML = `
            <div class="cora-ai-welcome" id="cora-ai-welcome-screen">
                <div class="w-12 h-12 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800 border border-zinc-200 shadow-sm">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-zinc-900 ">Welcome to Cora AI</h3>
                    <p class="text-xs text-zinc-500 max-w-sm mt-1">Ask questions, generate draft assets, or perform GST calculations in a few quick keystrokes.</p>
                </div>
                <div class="cora-ai-chips">
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Draft a contract for a property sale')">
                        <strong>Draft Contract</strong>
                        <p class="text-zinc-400 mt-1">Draft a contract for a property sale.</p>
                    </div>
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Calculate GST for a ₹50,000 booking split between Delhi and Jaipur')">
                        <strong>GST Split Math</strong>
                        <p class="text-zinc-400 mt-1">Calculate GST for ₹50,000 Delhi-Jaipur split.</p>
                    </div>
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('Explain the geofenced office location coordinate checking')">
                        <strong>Geofencing Help</strong>
                        <p class="text-zinc-400 mt-1">Explain coordinate checking bounds.</p>
                    </div>
                    <div class="cora-ai-chip" onclick="coraUsePromptChip('How do I manage workspace API roles?')">
                        <strong>Roles Governance</strong>
                        <p class="text-zinc-400 mt-1">How do I manage workspace API roles?</p>
                    </div>
                </div>
            </div>
        `;
        window.coraShowToast("Conversation cleared.");
    }

    function coraUsePromptChip(promptText) {
        document.getElementById('cora-ai-input').value = promptText;
        coraSendChatMessage();
    }

    function coraSendChatMessage() {
        const inputEl = document.getElementById('cora-ai-input');
        const promptText = inputEl.value.trim();
        if (!promptText) return;

        // Clear input and welcome screen
        inputEl.value = '';
        const welcomeScreen = document.getElementById('cora-ai-welcome-screen');
        if (welcomeScreen) {
            welcomeScreen.remove();
        }

        // Append user message
        coraAppendMessage('user', promptText);

        const startTime = Date.now();

        // Show loader indicator with a dynamic timer
        const loaderId = coraAppendMessage('assistant', `
            <div class="cora-skeleton-chat">
                <div class="cora-skeleton-line w-80"></div>
                <div class="cora-skeleton-line w-95"></div>
                <div class="cora-skeleton-line w-60"></div>
                <div class="text-[10px] text-zinc-400 mt-2 font-mono flex items-center gap-1.5">
                    <svg class="cora-ai-spin" viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>Generating... <span class="cora-ai-timer-sec">0.0</span>s elapsed</span>
                </div>
            </div>
        `);

        // Realtime ticking timer update
        const timerInterval = setInterval(() => {
            const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
            const timerEl = document.querySelector(`#${loaderId} .cora-ai-timer-sec`);
            if (timerEl) {
                timerEl.textContent = elapsed;
            }
        }, 100);

        // Disable controls during request
        const sendBtn = document.getElementById('cora-ai-send-btn');
        sendBtn.disabled = true;
        inputEl.disabled = true;

        // Gather parameters
        const provider = document.getElementById('cora-ai-provider').value;
        const model = document.getElementById('cora-ai-model').value;
        const temp = document.getElementById('cora-ai-temperature').value;
        const systemPrompt = document.getElementById('cora-ai-system').value;
        const ttsEnabled = document.getElementById('cora-ai-tts-toggle').checked;

        const ajaxUrlEndpoint = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'));

        jQuery.post(ajaxUrlEndpoint, {
            action: 'cora_ai_chat_query',
            security: (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxNonce : ''),
            message: promptText,
            provider: provider,
            model: model,
            temperature: temp,
            system_prompt: systemPrompt
        }, function(res) {
            clearInterval(timerInterval);
            const duration = ((Date.now() - startTime) / 1000).toFixed(1);

            // Enable inputs
            sendBtn.disabled = false;
            inputEl.disabled = false;
            inputEl.focus();

            const bubble = document.getElementById(loaderId);
            
            // Catch raw HTML or warning-wrapped responses and parse them safely
            let data = res;
            if (typeof res === 'string') {
                try {
                    data = JSON.parse(res);
                } catch(e) {
                    bubble.innerHTML = `<span style="color:var(--status-critical, #ef4444); font-weight:bold;">Parser Error: Invalid JSON response from server.</span><pre class="bg-zinc-100 p-2 rounded text-[10px] overflow-auto max-h-40 mt-2 font-mono border border-zinc-200 text-zinc-700 ">${res}</pre>`;
                    coraScrollToBottom();
                    return;
                }
            }

            if (data && data.success && data.data && data.data.reply) {
                const replyText = data.data.reply;
                bubble.innerHTML = coraFormatAIResponse(replyText);
                
                const chipsHtml = coraGetFollowupChips(promptText, replyText);

                let noticeHtml = '';
                if (data.data.fallback_notice) {
                    noticeHtml = `
                        <div class="mt-2 text-[10px] text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-2.5 py-1.5 flex items-start gap-1.5 font-medium max-w-md">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <span>${data.data.fallback_notice}</span>
                        </div>
                    `;
                }

                bubble.innerHTML += `
                    ${noticeHtml}
                    <div class="cora-ai-message-meta">
                        <span>${data.data.provider.toUpperCase()} / ${data.data.model} &bull; Response in ${duration}s</span>
                        <button type="button" class="cursor-pointer underline text-[9px] text-zinc-500 hover:text-zinc-700 bg-transparent border-none p-0" onclick="coraCopyMessageText(this)">Copy Text</button>
                    </div>
                    ${chipsHtml}
                `;

                // If TTS enabled, synthesize voice
                if (ttsEnabled) {
                    coraPlayResponseAudio(replyText, bubble);
                }
            } else {
                const errMsg = (data && data.data && data.data.message) ? data.data.message : 'Error retrieving chat completion.';
                bubble.innerHTML = `<span style="color:var(--status-critical, #ef4444); font-weight:bold;">API Error:</span> <span class="text-zinc-700 text-xs">${errMsg}</span>`;
            }
            coraScrollToBottom();
        }).fail(function(xhr, status, error) {
            clearInterval(timerInterval);
            sendBtn.disabled = false;
            inputEl.disabled = false;
            const bubble = document.getElementById(loaderId);
            bubble.innerHTML = `
                <span style="color:var(--status-critical, #ef4444); font-weight:bold;">Network Request Failed:</span>
                <div class="text-[11px] text-zinc-600 mt-2 font-mono p-2 bg-zinc-50 border border-zinc-200 rounded">
                    <div>HTTP Status: ${xhr.status} (${xhr.statusText || 'Unknown'})</div>
                    <div>Status Label: ${status}</div>
                    <div>Error detail: ${error || 'Connection refused or aborted'}</div>
                </div>
            `;
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
        viewport.appendChild(msgDiv);
        coraScrollToBottom();
        return msgId;
    }

    // Simple auto scroll helper
    function coraScrollToBottom() {
        const viewport = document.getElementById('cora-ai-messages');
        viewport.scrollTop = viewport.scrollHeight;
    }

    function coraFormatAIResponse(text) {
        // Simple markdown formatting helper
        let formatted = text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`([^`]+)`/g, '<code class="bg-zinc-100 px-1 py-0.5 rounded font-mono text-xs">$1</code>')
            .replace(/\n/g, '<br>');
        return formatted;
    }

    function coraCopyMessageText(btnEl) {
        const msgDiv = btnEl.closest('.cora-ai-message');
        // Get inner text excluding the metadata row
        const clone = msgDiv.cloneNode(true);
        const meta = clone.querySelector('.cora-ai-message-meta');
        if (meta) meta.remove();
        navigator.clipboard.writeText(clone.innerText.trim());
        window.coraShowToast("Message copied.");
    }

    function coraPlayResponseAudio(text, bubbleElement) {
        // Clean markdown or tags for clean speech
        const cleanedText = text.replace(/[*`#_]/g, '');

        window.coraShowToast("Synthesizing voice output...");

        const ajaxUrlEndpoint = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'));

        jQuery.post(ajaxUrlEndpoint, {
            action: 'cora_ai_generate_tts',
            security: (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxNonce : ''),
            text: cleanedText
        }, function(res) {
            if (res.success && res.data.audio) {
                const player = document.getElementById('cora-ai-audio-player');
                player.src = res.data.audio;
                player.play();
                window.coraShowToast("Playing audio response.");
            } else {
                console.error('TTS failed:', res.data);
            }
        });
    }

    function coraGetFollowupChips(prompt, reply) {
        let chips = [];
        const lowerPrompt = prompt.toLowerCase();
        const lowerReply = reply.toLowerCase();
        
        if (lowerPrompt.includes('contract') || lowerPrompt.includes('sale') || lowerPrompt.includes('agreement') || lowerPrompt.includes('draft') || lowerReply.includes('agreement') || lowerReply.includes('contract')) {
            chips = [
                'Summarize the critical dates and deadlines',
                'Add a severe breach and termination clause',
                'Translate this contract terms into plain English'
            ];
        } else if (lowerPrompt.includes('gst') || lowerPrompt.includes('tax') || lowerPrompt.includes('calculate') || lowerPrompt.includes('split') || lowerReply.includes('gst') || lowerReply.includes('tax')) {
            chips = [
                'Break down the exact SGST & CGST calculations',
                'Apply a 10% promotional discount and recalculate',
                'Draft a summary email to send to the client'
            ];
        } else if (lowerPrompt.includes('geofence') || lowerPrompt.includes('office') || lowerPrompt.includes('check-in') || lowerReply.includes('geofence')) {
            chips = [
                'Show me the JavaScript function bounding coordinates check',
                'Explain how to edit the location geofencing radius',
                'What happens if a check-in is logged outside the boundary?'
            ];
        } else {
            chips = [
                'Shorten this response for a quick briefing',
                'Elaborate in further detail with examples',
                'Provide the top 3 action items / next steps'
            ];
        }
        
        let html = '<div class="cora-ai-followup-chips">';
        chips.forEach(chip => {
            const escapedChip = chip.replace(/'/g, "\\'");
            html += `
                <button type="button" class="cora-ai-followup-chip" onclick="coraUsePromptChip('${escapedChip}')">
                    + ${chip}
                </button>
            `;
        });
        html += '</div>';
        return html;
    }

    // Expose send chat message globally
    window.coraSendChatMessage = coraSendChatMessage;

    // Check for pending prompt on page load
    jQuery(document).ready(function() {
        const pendingPrompt = sessionStorage.getItem('cora_pending_ai_prompt');
        if (pendingPrompt) {
            sessionStorage.removeItem('cora_pending_ai_prompt');
            const inputEl = document.getElementById('cora-ai-input');
            if (inputEl) {
                inputEl.value = pendingPrompt;
                coraSendChatMessage();
            }
        }
    });
</script>
