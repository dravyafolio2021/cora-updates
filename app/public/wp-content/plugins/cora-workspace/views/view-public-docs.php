<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Variables passed from routing context:
// $pages - Array of published pages
// $changelogs - Array of released changelogs
// $apis - Array of registered APIs
// $active_page - Page object currently loaded
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cora Platform Developer Hub</title>
    
    <!-- Outfit & Inter Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (for styled consistency across public pages) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: {
                            50: '#FBFaf7',
                            100: '#F9F6F0',
                        },
                        zinc: {
                            850: '#1f1f23',
                            950: '#09090b',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background-color: #fafafa;
            -webkit-font-smoothing: antialiased;
        }
        pre {
            background-color: #09090b;
            color: #f4f4f5;
            padding: 1rem;
            border-radius: 0.75rem;
            font-family: monospace;
            font-size: 11px;
            overflow-x: auto;
            border: 1px solid #27272a;
        }
        code {
            font-family: monospace;
        }
    </style>
</head>
<body class="text-zinc-850 font-sans min-h-screen flex flex-col justify-between selection:bg-zinc-950 selection:text-white">

    <!-- Header bar -->
    <header class="sticky top-0 bg-white/90 backdrop-blur-md border-b border-zinc-200/60 z-30 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-zinc-950">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </span>
            <span class="font-display font-bold text-sm tracking-tight text-zinc-950">Cora <span class="font-normal text-zinc-500">Developer Docs</span></span>
        </div>
        
        <div class="flex items-center gap-4 text-xs font-medium">
            <span class="text-zinc-400">Platform: <strong class="text-zinc-800">v2.2.1</strong></span>
            <span class="text-zinc-300">|</span>
            <span class="px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 font-bold border border-zinc-200 flex items-center gap-1 text-[10px]">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Live
            </span>
        </div>
    </header>

    <!-- Content Workspace -->
    <div class="flex-1 flex max-w-7xl w-full mx-auto px-6 py-8 gap-8 items-start">
        
        <!-- Sidebar Navigation -->
        <aside class="w-64 shrink-0 space-y-6 hidden md:block">
            <!-- Sidebar Search -->
            <div class="relative">
                <input type="text" id="cora-public-search" oninput="coraPublicFilterSidebar(this.value)" class="w-full border border-zinc-200 rounded-lg py-1.5 pl-8 pr-3 text-xs bg-white focus:border-zinc-400 focus:outline-none" placeholder="Search page title...">
                <span class="absolute left-2.5 top-2.5 text-zinc-400">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
            </div>

            <!-- Page Sections lists -->
            <div class="space-y-4">
                
                <!-- Category 1: Overview -->
                <div class="space-y-1 cora-public-nav-group" id="nav-group-overview">
                    <span class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 block px-2 mb-1.5">Overview</span>
                    <?php foreach ($pages as $p) : 
                        if ( $p['category'] !== 'overview' ) continue;
                        $active_class = ($active_page && $active_page['id'] === $p['id']) ? 'bg-white text-zinc-950 font-semibold shadow-xs border border-zinc-200/50' : 'text-zinc-650 hover:text-zinc-900';
                    ?>
                    <a href="<?php echo esc_url( home_url( '/docs/' . $p['slug'] ) ); ?>" onclick="coraPublicLoadPage(event, '<?php echo esc_js($p['slug']); ?>', this)" class="cora-public-nav-item flex items-center gap-2 px-3 py-1.5 text-xs rounded-lg transition-colors <?php echo $active_class; ?>" data-slug="<?php echo esc_attr($p['slug']); ?>">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                        <span class="truncate"><?php echo esc_html($p['title']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Category 2: Modules -->
                <div class="space-y-1 cora-public-nav-group" id="nav-group-modules">
                    <span class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 block px-2 mb-1.5">Modules</span>
                    <?php foreach ($pages as $p) : 
                        if ( $p['category'] !== 'modules' ) continue;
                        $active_class = ($active_page && $active_page['id'] === $p['id']) ? 'bg-white text-zinc-950 font-semibold shadow-xs border border-zinc-200/50' : 'text-zinc-650 hover:text-zinc-900';
                    ?>
                    <a href="<?php echo esc_url( home_url( '/docs/' . $p['slug'] ) ); ?>" onclick="coraPublicLoadPage(event, '<?php echo esc_js($p['slug']); ?>', this)" class="cora-public-nav-item flex items-center gap-2 px-3 py-1.5 text-xs rounded-lg transition-colors <?php echo $active_class; ?>" data-slug="<?php echo esc_attr($p['slug']); ?>">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                        <span class="truncate"><?php echo esc_html($p['title']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Static Tab: API Reference -->
                <div class="space-y-1 cora-public-nav-group" id="nav-group-api">
                    <span class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 block px-2 mb-1.5">Reference</span>
                    <button onclick="coraPublicShowSection('api')" class="w-full cora-public-nav-item flex items-center gap-2 px-3 py-1.5 text-xs rounded-lg transition-colors text-zinc-650 hover:text-zinc-900 text-left" id="btn-section-api">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                        API Endpoint Registry
                    </button>
                    <button onclick="coraPublicShowSection('changelog')" class="w-full cora-public-nav-item flex items-center gap-2 px-3 py-1.5 text-xs rounded-lg transition-colors text-zinc-650 hover:text-zinc-900 text-left" id="btn-section-changelog">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                        Changelog Feed
                    </button>
                </div>

                <!-- Category 3: Guides -->
                <div class="space-y-1 cora-public-nav-group" id="nav-group-guides">
                    <span class="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 block px-2 mb-1.5">Guides</span>
                    <?php foreach ($pages as $p) : 
                        if ( $p['category'] !== 'guides' && $p['category'] !== 'roadmap' ) continue;
                        $active_class = ($active_page && $active_page['id'] === $p['id']) ? 'bg-white text-zinc-950 font-semibold shadow-xs border border-zinc-200/50' : 'text-zinc-650 hover:text-zinc-900';
                    ?>
                    <a href="<?php echo esc_url( home_url( '/docs/' . $p['slug'] ) ); ?>" onclick="coraPublicLoadPage(event, '<?php echo esc_js($p['slug']); ?>', this)" class="cora-public-nav-item flex items-center gap-2 px-3 py-1.5 text-xs rounded-lg transition-colors <?php echo $active_class; ?>" data-slug="<?php echo esc_attr($p['slug']); ?>">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                        <span class="truncate"><?php echo esc_html($p['title']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>

            </div>
        </aside>

        <!-- Main Display Panel -->
        <main class="flex-1 min-w-0">
            
            <!-- SECTION 1: Standard Markdown Page rendering -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-8 md:p-10 shadow-sm space-y-6 max-w-4xl" id="cora-public-main-content">
                <?php if ( $active_page ) : ?>
                    <div class="flex items-center justify-between border-b border-zinc-150 pb-5 mb-2 flex-wrap gap-2">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-950 font-display"><?php echo esc_html($active_page['title']); ?></h1>
                            <span class="text-[10px] text-zinc-400 mt-1 block">Category: <strong class="uppercase text-zinc-550"><?php echo esc_html(str_replace('_', ' ', $active_page['category'])); ?></strong></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-zinc-400 block font-mono">Last Updated: <?php echo date('M j, Y H:i', strtotime($active_page['updated_at'])); ?></span>
                            <span class="text-[10px] text-zinc-400 block">Version: <strong class="font-mono text-zinc-650">v1.0.0</strong></span>
                        </div>
                    </div>

                    <div class="prose max-w-none text-xs leading-relaxed font-sans space-y-4">
                        <?php echo cora_markdown_to_html($active_page['content']); ?>
                    </div>
                <?php else : ?>
                    <div class="text-center py-16 text-zinc-400 text-xs">No documentation pages loaded.</div>
                <?php endif; ?>
            </div>

            <!-- SECTION 2: API Endpoints (Hidden by default, loaded dynamically) -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-8 md:p-10 shadow-sm space-y-6 max-w-4xl hidden" id="cora-public-api-section">
                <div class="border-b border-zinc-150 pb-5">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-950 font-display">API Registry & Reference</h1>
                    <p class="text-xs text-zinc-500 mt-0.5">Explore the endpoints, capabilities, permission tiers, and schemas required to build integrations on the platform.</p>
                </div>

                <div class="space-y-6">
                    <?php foreach ($apis as $api) : 
                        $method_colors = array(
                            'GET' => 'bg-zinc-100 text-zinc-900 border-zinc-200',
                            'POST' => 'bg-zinc-950 text-white border-zinc-950',
                            'PUT' => 'bg-zinc-150 text-zinc-800 border-zinc-300',
                            'DELETE' => 'bg-rose-50 text-rose-800 border-rose-150'
                        );
                        $method_class = $method_colors[$api['method']] ?? 'bg-zinc-100 text-zinc-850';
                    ?>
                    <div class="border border-zinc-200/60 rounded-xl p-5 space-y-3 shadow-xs">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-0.5 text-[9.5px] font-mono font-bold rounded-md uppercase border <?php echo $method_class; ?>"><?php echo esc_html($api['method']); ?></span>
                                <code class="text-xs font-mono font-bold text-zinc-950"><?php echo esc_html($api['path']); ?></code>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php if ( $api['mcp_compatible'] ) : ?>
                                <span class="px-2 py-0.5 rounded-full text-[9px] bg-zinc-100 border border-zinc-300 text-zinc-700 font-bold uppercase tracking-wider flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> MCP Compatible
                                </span>
                                <?php endif; ?>
                                <span class="px-2 py-0.5 rounded-full text-[9px] bg-zinc-50 border border-zinc-200 text-zinc-500 font-bold uppercase">Auth: <?php echo esc_html($api['permission_level']); ?></span>
                            </div>
                        </div>

                        <p class="text-xs text-zinc-650"><?php echo esc_html($api['description']); ?></p>

                        <div class="pt-3 border-t border-zinc-100 font-mono text-[11px] space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-zinc-400 font-bold text-[9px] uppercase tracking-wider block mb-1">Request Payload:</span>
                                    <pre class="max-h-48"><?php echo esc_html($api['request_schema'] ?: '{}'); ?></pre>
                                </div>
                                <div>
                                    <span class="text-zinc-400 font-bold text-[9px] uppercase tracking-wider block mb-1">Response Payload:</span>
                                    <pre class="max-h-48"><?php echo esc_html($api['response_schema'] ?: '{}'); ?></pre>
                                </div>
                            </div>
                            <?php if ( ! empty($api['example']) ) : ?>
                            <div>
                                <span class="text-zinc-400 font-bold text-[9px] uppercase tracking-wider block mb-1">Execution Example:</span>
                                <pre class="max-h-64"><?php echo esc_html($api['example']); ?></pre>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- SECTION 3: Changelog timeline (Hidden by default, loaded dynamically) -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-8 md:p-10 shadow-sm space-y-6 max-w-4xl hidden" id="cora-public-changelog-section">
                <div class="border-b border-zinc-150 pb-5">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-950 font-display">Changelog Feed</h1>
                    <p class="text-xs text-zinc-500 mt-0.5">Historical tracking of system-wide releases, module versions, and build milestones.</p>
                </div>

                <div class="space-y-6">
                    <?php foreach ($changelogs as $entry) : 
                        $badge_class = empty($entry['module_key']) ? 'bg-zinc-950 text-white' : 'bg-zinc-100 text-zinc-800';
                        $badge_label = empty($entry['module_key']) ? 'Platform Core' : $entry['module_key'];
                    ?>
                    <div class="border border-zinc-200/60 rounded-xl p-5 space-y-2">
                        <div class="flex items-start justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2 py-0.5 text-[9.5px] font-bold rounded-full uppercase <?php echo $badge_class; ?>"><?php echo esc_html($badge_label); ?></span>
                                <span class="text-xs font-mono font-bold text-zinc-400">v<?php echo esc_html($entry['version']); ?></span>
                                <?php if ( ! empty($entry['ticket_id']) ) : ?>
                                <span class="text-xs font-mono text-zinc-450">ID: <?php echo esc_html($entry['ticket_id']); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="text-[10px] text-zinc-400 font-mono"><?php echo date('M j, Y', strtotime($entry['created_at'])); ?></span>
                        </div>
                        <h3 class="text-xs font-bold text-zinc-950"><?php echo esc_html($entry['title']); ?></h3>
                        <p class="text-xs text-zinc-650 font-sans leading-relaxed"><?php echo esc_html($entry['description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </main>
    </div>

    <!-- Minimal footer -->
    <footer class="border-t border-zinc-200/60 py-6 px-6 bg-zinc-50 text-center text-[10.5px] text-zinc-400">
        &copy; <?php echo date('Y'); ?> Cora Platform. Read-only developer reference docs.
    </footer>

    <!-- INTERACTION SCRIPTS -->
    <script>
        function coraPublicShowSection(sectionId) {
            // Hide standard content
            document.getElementById('cora-public-main-content').classList.add('hidden');
            document.getElementById('cora-public-api-section').classList.add('hidden');
            document.getElementById('cora-public-changelog-section').classList.add('hidden');
            
            // De-select links
            document.querySelectorAll('.cora-public-nav-item').forEach(el => el.classList.remove('bg-white', 'text-zinc-950', 'font-semibold', 'shadow-xs', 'border', 'border-zinc-200/50'));
            
            // Show target
            if (sectionId === 'api') {
                document.getElementById('cora-public-api-section').classList.remove('hidden');
                document.getElementById('btn-section-api').classList.add('bg-white', 'text-zinc-950', 'font-semibold', 'shadow-xs', 'border', 'border-zinc-200/50');
            } else if (sectionId === 'changelog') {
                document.getElementById('cora-public-changelog-section').classList.remove('hidden');
                document.getElementById('btn-section-changelog').classList.add('bg-white', 'text-zinc-950', 'font-semibold', 'shadow-xs', 'border', 'border-zinc-200/50');
            }
        }

        function coraPublicLoadPage(e, slug, el) {
            if (e) {
                if (e.metaKey || e.ctrlKey) return;
                e.preventDefault();
            }
            
            // Hide custom sections
            document.getElementById('cora-public-api-section').classList.add('hidden');
            document.getElementById('cora-public-changelog-section').classList.add('hidden');
            document.getElementById('cora-public-main-content').classList.remove('hidden');

            // De-select other links
            document.querySelectorAll('.cora-public-nav-item').forEach(item => {
                item.classList.remove('bg-white', 'text-zinc-950', 'font-semibold', 'shadow-xs', 'border', 'border-zinc-200/50');
                item.classList.add('text-zinc-650', 'hover:text-zinc-900');
            });
            
            if (el) {
                el.classList.add('bg-white', 'text-zinc-950', 'font-semibold', 'shadow-xs', 'border', 'border-zinc-200/50');
                el.classList.remove('text-zinc-650', 'hover:text-zinc-900');
            } else {
                const targetEl = document.querySelector(`.cora-public-nav-item[data-slug="${slug}"]`);
                if (targetEl) {
                    targetEl.classList.add('bg-white', 'text-zinc-950', 'font-semibold', 'shadow-xs', 'border', 'border-zinc-200/50');
                    targetEl.classList.remove('text-zinc-650', 'hover:text-zinc-900');
                }
            }

            const mainContent = document.getElementById('cora-public-main-content');
            mainContent.innerHTML = `
                <div class="flex items-center justify-center py-16">
                    <svg class="animate-spin h-5 w-5 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            `;

            // Fetch page content
            fetch('<?php echo admin_url("admin-ajax.php"); ?>?action=cora_public_get_page&slug=' + encodeURIComponent(slug))
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mainContent.innerHTML = `
                            <div class="flex items-center justify-between border-b border-zinc-150 pb-5 mb-2 flex-wrap gap-2">
                                <div>
                                    <h1 class="text-2xl font-bold tracking-tight text-zinc-950 font-display">${escapeHtml(data.data.title)}</h1>
                                    <span class="text-[10px] text-zinc-400 mt-1 block">Category: <strong class="uppercase text-zinc-550">${escapeHtml(data.data.category)}</strong></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-zinc-400 block font-mono">Last Updated: ${escapeHtml(data.data.updated_at)}</span>
                                    <span class="text-[10px] text-zinc-400 block">Version: <strong class="font-mono text-zinc-650">v1.0.0</strong></span>
                                </div>
                            </div>
                            <div class="prose max-w-none text-xs leading-relaxed font-sans space-y-4">
                                ${data.data.html}
                            </div>
                        `;
                        history.pushState({ slug: slug }, data.data.title, '<?php echo home_url("/docs/"); ?>' + slug);
                    } else {
                        mainContent.innerHTML = `<div class="text-center py-16 text-zinc-400 text-xs">Failed to load documentation page.</div>`;
                    }
                })
                .catch(() => {
                    mainContent.innerHTML = `<div class="text-center py-16 text-zinc-400 text-xs">Error communicating with server.</div>`;
                });
        }

        function escapeHtml(str) {
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        window.onpopstate = function(event) {
            if (event.state && event.state.slug) {
                coraPublicLoadPage(null, event.state.slug, null);
            } else {
                window.location.reload();
            }
        };

        function coraPublicFilterSidebar(val) {
            const query = val.toLowerCase().trim();
            if (!query) {
                document.querySelectorAll('.cora-public-nav-item').forEach(el => el.classList.remove('hidden'));
                document.querySelectorAll('.cora-public-nav-group').forEach(el => el.classList.remove('hidden'));
                return;
            }

            document.querySelectorAll('.cora-public-nav-group').forEach(group => {
                let matchCount = 0;
                group.querySelectorAll('.cora-public-nav-item').forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.indexOf(query) !== -1) {
                        item.classList.remove('hidden');
                        matchCount++;
                    } else {
                        item.classList.add('hidden');
                    }
                });

                if (matchCount > 0) {
                    group.classList.remove('hidden');
                } else {
                    group.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
