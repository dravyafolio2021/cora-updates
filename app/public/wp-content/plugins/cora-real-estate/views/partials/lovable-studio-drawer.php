<?php
/**
 * Lovable Studio Drawer — Partial view
 * Included from view-canvas.php
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$compat_flags_partial = get_option( 'cora_git_sync_compat_flags', array() );
$lt_partial = get_option( 'cora_git_sync_last_time', 0 );
$ls_partial = get_option( 'cora_git_sync_last_status', '' );
?>
<!-- ╔════════════════════════════════════════════╗ -->
<!-- ║       LOVABLE STUDIO — INLINE CONTAINER    ║ -->
<!-- ╚════════════════════════════════════════════╝ -->
<div id="lovable-studio-overlay" style="display:none;"></div>

<div id="lovable-studio-drawer" style="display:none;width:100%;background:#fff;border:1px solid #e4e4e7;border-radius:14px;flex-direction:column;box-shadow:0 1px 3px rgba(0,0,0,0.04);margin-top:24px;overflow:hidden;">

    <!-- Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid #f4f4f5;background:#fafafa;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:32px;height:32px;background:#18181b;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="white" stroke-width="2" fill="none"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;color:#09090b;line-height:1.2;">Lovable Studio</div>
                <div style="font-size:11px;color:#71717a;">Build Lovable pages that connect to Cora&rsquo;s backend</div>
            </div>
        </div>
        <button onclick="closeLovableStudio()" style="display:none;width:28px;height:28px;background:#f4f4f5;border:none;border-radius:6px;cursor:pointer;align-items:center;justify-content:center;color:#71717a;">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <!-- Step Wizard Bar -->
    <div style="display:flex;align-items:center;padding:0 8px;border-bottom:1px solid #f4f4f5;background:#fff;flex-shrink:0;overflow-x:auto;">
        <button onclick="lsGoToStep(1)" class="ls-step-btn" data-step="1" style="display:flex;align-items:center;gap:7px;padding:13px 12px;border:none;background:transparent;cursor:pointer;border-bottom:2px solid transparent;font-size:11px;font-weight:600;color:#71717a;white-space:nowrap;"><span class="ls-step-num" style="width:18px;height:18px;border-radius:50%;background:#f4f4f5;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;">1</span> Pick Components</button>
        <div style="width:1px;height:14px;background:#e4e4e7;flex-shrink:0;"></div>
        <button onclick="lsGoToStep(2)" class="ls-step-btn" data-step="2" style="display:flex;align-items:center;gap:7px;padding:13px 12px;border:none;background:transparent;cursor:pointer;border-bottom:2px solid transparent;font-size:11px;font-weight:600;color:#71717a;white-space:nowrap;"><span class="ls-step-num" style="width:18px;height:18px;border-radius:50%;background:#f4f4f5;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;">2</span> Build Prompt</button>
        <div style="width:1px;height:14px;background:#e4e4e7;flex-shrink:0;"></div>
        <button onclick="lsGoToStep(3)" class="ls-step-btn" data-step="3" style="display:flex;align-items:center;gap:7px;padding:13px 12px;border:none;background:transparent;cursor:pointer;border-bottom:2px solid transparent;font-size:11px;font-weight:600;color:#71717a;white-space:nowrap;"><span class="ls-step-num" style="width:18px;height:18px;border-radius:50%;background:#f4f4f5;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;">3</span> Push to GitHub</button>
        <div style="width:1px;height:14px;background:#e4e4e7;flex-shrink:0;"></div>
        <button onclick="lsGoToStep(4)" class="ls-step-btn" data-step="4" style="display:flex;align-items:center;gap:7px;padding:13px 12px;border:none;background:transparent;cursor:pointer;border-bottom:2px solid transparent;font-size:11px;font-weight:600;color:#71717a;white-space:nowrap;"><span class="ls-step-num" style="width:18px;height:18px;border-radius:50%;background:#f4f4f5;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;">4</span> Sync to Cora</button>
        <div style="width:1px;height:14px;background:#e4e4e7;flex-shrink:0;"></div>
        <button onclick="lsGoToStep(5)" class="ls-step-btn" data-step="5" style="display:flex;align-items:center;gap:7px;padding:13px 12px;border:none;background:transparent;cursor:pointer;border-bottom:2px solid transparent;font-size:11px;font-weight:600;color:#71717a;white-space:nowrap;"><span class="ls-step-num" style="width:18px;height:18px;border-radius:50%;background:#f4f4f5;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;">5</span> Map &amp; Preview</button>
    </div>

    <!-- Step Content Container -->
    <div style="flex:1;overflow-y:auto;padding:24px;" id="ls-step-scroll">

        <!-- ── STEP 1: Component Picker ── -->
        <div id="ls-step-1" class="ls-step-content">
            <h3 style="font-size:15px;font-weight:700;color:#09090b;margin:0 0 4px;">What do you want to build?</h3>
            <p style="font-size:12px;color:#71717a;margin:0 0 18px;">Select components. Cora auto-generates a Lovable prompt with the correct <code style="background:#f4f4f5;padding:1px 6px;border-radius:4px;font-size:10px;">data-cora-*</code> attributes.</p>

            <div style="margin-bottom:14px;">
                <div style="font-size:10px;font-weight:700;color:#3f3f46;margin-bottom:7px;text-transform:uppercase;letter-spacing:.06em;">Visual Style</div>
                <div style="display:flex;gap:7px;flex-wrap:wrap;" id="ls-style-picker">
                    <button onclick="lsSetStyle('modern')" data-style="modern" style="padding:5px 13px;border:1.5px solid #18181b;background:#18181b;color:#fff;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Modern Clean</button>
                    <button onclick="lsSetStyle('luxury')" data-style="luxury" style="padding:5px 13px;border:1.5px solid #e4e4e7;background:#fff;color:#3f3f46;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Luxury</button>
                    <button onclick="lsSetStyle('minimal')" data-style="minimal" style="padding:5px 13px;border:1.5px solid #e4e4e7;background:#fff;color:#3f3f46;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Minimal</button>
                    <button onclick="lsSetStyle('vibrant')" data-style="vibrant" style="padding:5px 13px;border:1.5px solid #e4e4e7;background:#fff;color:#3f3f46;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Vibrant</button>
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <div style="font-size:10px;font-weight:700;color:#3f3f46;margin-bottom:7px;text-transform:uppercase;letter-spacing:.06em;">Page Layout</div>
                <div style="display:flex;gap:7px;flex-wrap:wrap;" id="ls-layout-picker">
                    <button onclick="lsSetLayout('homepage')" data-layout="homepage" style="padding:5px 13px;border:1.5px solid #18181b;background:#18181b;color:#fff;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Full Homepage</button>
                    <button onclick="lsSetLayout('listings')" data-layout="listings" style="padding:5px 13px;border:1.5px solid #e4e4e7;background:#fff;color:#3f3f46;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Listings Page</button>
                    <button onclick="lsSetLayout('landing')" data-layout="landing" style="padding:5px 13px;border:1.5px solid #e4e4e7;background:#fff;color:#3f3f46;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Lead Landing</button>
                    <button onclick="lsSetLayout('detail')" data-layout="detail" style="padding:5px 13px;border:1.5px solid #e4e4e7;background:#fff;color:#3f3f46;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Property Detail</button>
                </div>
            </div>

            <div style="font-size:10px;font-weight:700;color:#3f3f46;margin-bottom:9px;text-transform:uppercase;letter-spacing:.06em;">Components</div>
            <div id="ls-component-grid" style="display:grid;grid-template-columns:repeat(2,1fr);gap:9px;"></div>

            <div style="margin-top:18px;display:flex;justify-content:flex-end;">
                <button onclick="lsGoToStep(2)" style="padding:9px 20px;background:#18181b;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">Build Prompt &#8594;</button>
            </div>
        </div>

        <!-- ── STEP 2: Prompt Builder ── -->
        <div id="ls-step-2" class="ls-step-content" style="display:none;">
            <h3 style="font-size:15px;font-weight:700;color:#09090b;margin:0 0 4px;">Your Lovable Prompt</h3>
            <p style="font-size:12px;color:#71717a;margin:0 0 14px;">Copy and paste into Lovable. Cora data attributes are already included.</p>
            <div id="ls-prompt-static-note" style="display:none;padding:9px 13px;background:#fefce8;border:1px solid #fde047;border-radius:8px;margin-bottom:10px;font-size:11px;color:#854d0e;">
                <strong>Note:</strong> Selected components are static &mdash; no Cora backend data needed. You can add <code>data-cora-*</code> attributes manually later.
            </div>
            <div style="position:relative;margin-bottom:10px;">
                <textarea id="ls-prompt-output" readonly style="width:100%;height:300px;padding:16px;background:#09090b;color:#d4d4d8;border:1px solid #27272a;border-radius:10px;font-family:monospace;font-size:11px;line-height:1.65;resize:none;box-sizing:border-box;"></textarea>
                <button onclick="lsCopyPrompt()" id="ls-copy-btn" style="position:absolute;top:10px;right:10px;padding:5px 12px;background:#27272a;border:1px solid #3f3f46;border-radius:6px;color:#d4d4d8;font-size:10px;font-weight:600;cursor:pointer;">Copy Prompt</button>
            </div>
            <div id="ls-tech-chips" style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px;"></div>
            <div style="display:flex;gap:10px;justify-content:space-between;align-items:center;">
                <button onclick="lsGoToStep(1)" style="padding:8px 16px;background:#fff;border:1px solid #e4e4e7;color:#3f3f46;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">&#8592; Back</button>
                <div style="display:flex;gap:8px;">
                    <a href="https://lovable.dev" target="_blank" style="padding:9px 16px;background:#fff;border:1.5px solid #e4e4e7;color:#09090b;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">Open Lovable &#8599;</a>
                    <button onclick="lsGoToStep(3)" style="padding:9px 20px;background:#18181b;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">Next: GitHub &#8594;</button>
                </div>
            </div>
        </div>

        <!-- ── STEP 3: GitHub Setup ── -->
        <div id="ls-step-3" class="ls-step-content" style="display:none;">
            <h3 style="font-size:15px;font-weight:700;color:#09090b;margin:0 0 4px;">Connect to GitHub</h3>
            <p style="font-size:12px;color:#71717a;margin:0 0 14px;">After building in Lovable, push to GitHub and enter your repo details.</p>

            <div style="background:#f9f9f9;border:1px solid #e4e4e7;border-radius:10px;padding:14px;margin-bottom:14px;">
                <div style="font-size:10px;font-weight:700;color:#3f3f46;margin-bottom:9px;text-transform:uppercase;letter-spacing:.06em;">Steps</div>
                <div style="display:flex;flex-direction:column;gap:9px;">
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:20px;height:20px;background:#18181b;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">1</div>
                        <div><div style="font-size:12px;font-weight:600;color:#09090b;">Build in Lovable</div><div style="font-size:11px;color:#71717a;margin-top:2px;">Paste your prompt into <a href="https://lovable.dev" target="_blank" style="color:#09090b;font-weight:600;">lovable.dev</a></div></div>
                    </div>
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:20px;height:20px;background:#18181b;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">2</div>
                        <div><div style="font-size:12px;font-weight:600;color:#09090b;">Push to GitHub</div><div style="font-size:11px;color:#71717a;margin-top:2px;">Lovable: Settings &rarr; Integrations &rarr; GitHub &rarr; Connect &amp; Push</div></div>
                    </div>
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:20px;height:20px;background:#18181b;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">3</div>
                        <div><div style="font-size:12px;font-weight:600;color:#09090b;">Generate GitHub Token</div><div style="font-size:11px;color:#71717a;margin-top:2px;"><a href="https://github.com/settings/tokens/new?description=Cora+Sync&scopes=repo" target="_blank" style="color:#09090b;font-weight:600;text-decoration:underline;">Click here</a> &mdash; select <code style="background:#f4f4f5;padding:1px 5px;border-radius:3px;font-size:10px;">repo</code> scope</div></div>
                    </div>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:#3f3f46;display:block;margin-bottom:3px;">GitHub Repository URL</label>
                    <input id="ls-repo-url" type="text" placeholder="https://github.com/username/repo" value="<?php echo esc_attr( get_option( 'cora_git_sync_repo', '' ) ); ?>" style="width:100%;padding:8px 12px;border:1px solid #e4e4e7;border-radius:8px;font-size:12px;outline:none;box-sizing:border-box;" onfocus="this.style.borderColor='#09090b'" onblur="this.style.borderColor='#e4e4e7'">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="font-size:11px;font-weight:600;color:#3f3f46;display:block;margin-bottom:3px;">Branch</label>
                        <input id="ls-repo-branch" type="text" placeholder="main" value="<?php echo esc_attr( get_option( 'cora_git_sync_branch', 'main' ) ); ?>" style="width:100%;padding:8px 12px;border:1px solid #e4e4e7;border-radius:8px;font-size:12px;outline:none;box-sizing:border-box;" onfocus="this.style.borderColor='#09090b'" onblur="this.style.borderColor='#e4e4e7'">
                    </div>
                    <div>
                        <label style="font-size:11px;font-weight:600;color:#3f3f46;display:block;margin-bottom:3px;">Access Token</label>
                        <input id="ls-repo-token" type="password" placeholder="ghp_xxxxxxxxxxxx" value="<?php echo esc_attr( get_option( 'cora_git_sync_token', '' ) ); ?>" style="width:100%;padding:8px 12px;border:1px solid #e4e4e7;border-radius:8px;font-size:12px;outline:none;box-sizing:border-box;" onfocus="this.style.borderColor='#09090b'" onblur="this.style.borderColor='#e4e4e7'">
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;justify-content:space-between;">
                <button onclick="lsGoToStep(2)" style="padding:8px 16px;background:#fff;border:1px solid #e4e4e7;color:#3f3f46;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">&#8592; Back</button>
                <button onclick="lsGoToStep(4)" style="padding:9px 20px;background:#18181b;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">Next: Sync &#8594;</button>
            </div>
        </div>

        <!-- ── STEP 4: Sync ── -->
        <div id="ls-step-4" class="ls-step-content" style="display:none;">
            <h3 style="font-size:15px;font-weight:700;color:#09090b;margin:0 0 4px;">Sync Repository to Cora</h3>
            <p style="font-size:12px;color:#71717a;margin:0 0 14px;">Pull the latest version of your GitHub repo into Cora.</p>

            <div style="background:#f9f9f9;border:1px solid #e4e4e7;border-radius:10px;padding:13px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <div>
                    <div style="font-size:10px;font-weight:700;color:#3f3f46;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Last Sync</div>
                    <div style="font-size:12px;color:#09090b;font-weight:600;"><?php echo $lt_partial ? esc_html( date( 'M j, Y H:i', $lt_partial ) ) : 'Never'; ?></div>
                    <?php if ( $ls_partial ) : ?><div style="font-size:11px;color:#71717a;"><?php echo esc_html( $ls_partial ); ?></div><?php endif; ?>
                </div>
                <div style="display:flex;gap:5px;flex-wrap:wrap;" id="ls-compat-chips">
                    <?php foreach ( $compat_flags_partial as $f ) : ?>
                    <span style="padding:3px 9px;background:#f0fdf4;border:1px solid #86efac;border-radius:12px;font-size:10px;font-weight:600;color:#166534;">&#10003; <?php echo esc_html( $f ); ?></span>
                    <?php endforeach; ?>
                    <?php if ( empty( $compat_flags_partial ) && $lt_partial ) : ?>
                    <span style="padding:3px 9px;background:#fefce8;border:1px solid #fde047;border-radius:12px;font-size:10px;font-weight:600;color:#854d0e;">&#9888; No bridge attrs</span>
                    <?php endif; ?>
                </div>
            </div>

            <div style="border:2px dashed #e4e4e7;border-radius:12px;padding:28px;text-align:center;margin-bottom:16px;">
                <div style="font-size:13px;font-weight:700;color:#09090b;margin-bottom:4px;">Pull Latest from GitHub</div>
                <div style="font-size:11px;color:#71717a;margin-bottom:14px;">Downloads and deploys the latest commit from your repository</div>
                <button onclick="lsTriggerSync()" id="ls-sync-btn" style="padding:10px 28px;background:#18181b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">&#8635; Sync Now</button>
                <div id="ls-sync-progress" style="display:none;margin-top:10px;font-size:11px;color:#71717a;">
                    <div style="width:100%;height:3px;background:#f4f4f5;border-radius:2px;overflow:hidden;margin-bottom:6px;">
                        <div id="ls-sync-progress-bar" style="height:100%;background:#09090b;border-radius:2px;width:0%;transition:width 2s;"></div>
                    </div>
                    Syncing repository&hellip;
                </div>
            </div>

            <div style="display:flex;gap:10px;justify-content:space-between;">
                <button onclick="lsGoToStep(3)" style="padding:8px 16px;background:#fff;border:1px solid #e4e4e7;color:#3f3f46;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">&#8592; Back</button>
                <button onclick="lsGoToStep(5)" style="padding:9px 20px;background:#18181b;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">Next: Map Pages &#8594;</button>
            </div>
        </div>

        <!-- ── STEP 5: Map & Preview ── -->
        <div id="ls-step-5" class="ls-step-content" style="display:none;">
            <h3 style="font-size:15px;font-weight:700;color:#09090b;margin:0 0 4px;">Map Pages &amp; Go Live</h3>
            <p style="font-size:12px;color:#71717a;margin:0 0 14px;">Connect each WordPress page to a Lovable route. Visitors will see your design on that URL.</p>

            <div id="ls-mapper-empty" style="display:none;padding:32px;text-align:center;border:2px dashed #e4e4e7;border-radius:12px;margin-bottom:14px;">
                <div style="font-size:13px;font-weight:600;color:#3f3f46;margin-bottom:4px;">No routes detected yet</div>
                <div style="font-size:11px;color:#71717a;">Sync a GitHub repository first (Step 4).</div>
            </div>

            <div id="ls-mapper-table" style="margin-bottom:14px;">
                <div style="display:grid;grid-template-columns:1fr 24px 1fr;gap:8px;margin-bottom:7px;">
                    <div style="font-size:10px;font-weight:700;color:#71717a;text-transform:uppercase;letter-spacing:.06em;padding:0 4px;">WordPress Page</div>
                    <div></div>
                    <div style="font-size:10px;font-weight:700;color:#71717a;text-transform:uppercase;letter-spacing:.06em;padding:0 4px;">Lovable Route</div>
                </div>
                <div id="ls-mapper-rows" style="display:flex;flex-direction:column;gap:7px;"></div>
            </div>

            <div id="ls-compat-summary" style="margin-bottom:14px;padding:11px 14px;background:#f9f9f9;border:1px solid #e4e4e7;border-radius:10px;font-size:11px;color:#3f3f46;"></div>

            <div style="display:flex;gap:10px;justify-content:space-between;align-items:center;">
                <button onclick="lsGoToStep(4)" style="padding:8px 16px;background:#fff;border:1px solid #e4e4e7;color:#3f3f46;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">&#8592; Back</button>
                <div style="display:flex;gap:8px;">
                    <a id="ls-preview-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" style="padding:9px 16px;background:#fff;border:1.5px solid #e4e4e7;color:#09090b;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">Preview Site &#8599;</a>
                    <button onclick="closeLovableStudio()" style="padding:9px 20px;background:#18181b;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">&#10003; Done</button>
                </div>
            </div>
        </div>

    </div><!-- /step content container -->
</div><!-- /lovable-studio-drawer -->
