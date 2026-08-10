<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cora Platform Developer Hub</title>
    
    <!-- Outfit & Inter Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
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
        .prose h1, .prose h2, .prose h3 {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            color: #09090b;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .prose h1 { font-size: 1.25rem; }
        .prose h2 { font-size: 1.125rem; }
        .prose h3 { font-size: 1rem; }
        .prose p { margin-bottom: 1rem; line-height: 1.6; }
        .prose ul, .prose ol { margin-bottom: 1rem; padding-left: 1.25rem; list-style-type: disc; }
        .prose li { margin-bottom: 0.25rem; }
        
        /* Monochromatic navigation link styling overrides */
        .cora-nav-link:hover {
            background-color: rgba(244, 244, 245, 0.7) !important; /* zinc-100/70 */
            color: #09090b !important; /* zinc-950 */
        }
        .cora-nav-link.bg-zinc-950,
        .cora-nav-link.bg-zinc-950:hover {
            background-color: #09090b !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body class="text-zinc-850 font-sans min-h-screen flex flex-col justify-between selection:bg-zinc-950 selection:text-white">

    <!-- TASK 1: Header component -->
    <?php include CORA_WORKSPACE_PATH . 'views/view-public-docs-header.php'; ?>

    <!-- Main Workspace Container -->
    <div class="flex-1 flex max-w-7xl w-full mx-auto px-6 py-8 gap-8 items-start">
        
        <!-- TASK 2: Sidebar Navigation component -->
        <?php include CORA_WORKSPACE_PATH . 'views/view-public-docs-sidebar.php'; ?>

        <!-- Main Display Panel -->
        <main class="flex-1 min-w-0 flex gap-8 items-start">
            
            <!-- TASK 3: Content area component -->
            <?php include CORA_WORKSPACE_PATH . 'views/view-public-docs-content.php'; ?>
            
            <!-- TASK 4: Right widgets column component -->
            <?php include CORA_WORKSPACE_PATH . 'views/view-public-docs-widgets.php'; ?>
            
        </main>
    </div>

    <!-- Minimal footer -->
    <footer class="border-t border-zinc-200/60 py-6 px-6 bg-zinc-50 text-center text-[10.5px] text-zinc-400">
        &copy; <?php echo date('Y'); ?> Cora Platform. Read-only developer reference docs.
    </footer>

    <!-- TASK 5 & 6: Search modal & AJAX routing script -->
    <?php include CORA_WORKSPACE_PATH . 'views/view-public-docs-search.php'; ?>

    <!-- Cora Backlink Badge -->
    <?php include CORA_WORKSPACE_PATH . 'views/view-backlink-badge.php'; ?>

</body>
</html>
