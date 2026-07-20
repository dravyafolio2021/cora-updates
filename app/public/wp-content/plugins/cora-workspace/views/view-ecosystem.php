<?php
/**
 * Cora Real Estate - Interactive Product Ecosystem & YouTube Presentation Map
 * Standalone presentation screen decoupled from the main workspace dashboard UI.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cora OS Ecosystem Map</title>
    <!-- Tailwind CSS for rich aesthetics -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        zinc: {
                            150: '#ececee',
                            650: '#4c4c52',
                            850: '#202024'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F9F6F0; /* Warm cream branding base */
        }
    </style>
</head>
<body class="min-h-screen p-4 sm:p-8 flex items-center justify-center">

<div class="cora-ecosystem-container w-full max-w-7xl p-6 sm:p-8 bg-[#FBFaf7] border border-zinc-200/80 rounded-xl space-y-8 select-none shadow-sm relative">
    
    <!-- Toast Container for presentation page feedback -->
    <div id="cora-toast-container" class="fixed bottom-4 right-4 z-[9999] flex flex-col-reverse gap-2 pointer-events-none"></div>

    <!-- Top Header & Narrative Pitch -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-zinc-200">
        <div>
            <h1 class="text-2xl font-black text-zinc-900 tracking-tight flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-800">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
                Cora OS Ecosytem Map
            </h1>
            <p class="text-xs text-zinc-500 mt-1">An interactive presentation canvas to showcase Cora's operational value proposition live on YouTube.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <!-- Back to Workspace Link -->
            <a href="<?php echo esc_url( home_url( '/workspace/dashboard' ) ); ?>" class="px-4.5 py-2 border border-zinc-250 bg-white rounded-lg text-xs font-bold text-zinc-700 hover:bg-zinc-50 transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                Go to Workspace
            </a>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60 gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Presentation Mode Active
            </span>
        </div>
    </div>

    <!-- 4 Pillars Core Blueprint -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <!-- Pillar 1 -->
        <div class="bg-white border border-zinc-200/80 p-5 rounded-lg space-y-3 shadow-sm hover:border-zinc-950 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="p-2 bg-zinc-50 border border-zinc-200 rounded-md">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700">
                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                    </svg>
                </span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest font-mono">Pillar 01</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900">Engagement OS</h3>
                <p class="text-xs text-zinc-500 mt-1 leading-relaxed">Captures and qualifies leads natively with risk scoring formulas. Replaces expensive Typeform subscriptions.</p>
            </div>
        </div>

        <!-- Pillar 2 -->
        <div class="bg-white border border-zinc-200/80 p-5 rounded-lg space-y-3 shadow-sm hover:border-zinc-950 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="p-2 bg-zinc-50 border border-zinc-200 rounded-md">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest font-mono">Pillar 02</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900">Showcase OS</h3>
                <p class="text-xs text-zinc-500 mt-1 leading-relaxed">Fast listing portfolios enqueued with Google Fonts preloads & watermarks. Sub-1.2s mobile loading.</p>
            </div>
        </div>

        <!-- Pillar 3 -->
        <div class="bg-white border border-zinc-200/80 p-5 rounded-lg space-y-3 shadow-sm hover:border-zinc-950 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="p-2 bg-zinc-50 border border-zinc-200 rounded-md">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest font-mono">Pillar 03</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900">Vault OS</h3>
                <p class="text-xs text-zinc-500 mt-1 leading-relaxed">Secured document share portals. Protects Aadhaar/PAN cards and tracks visitor compliance via audit trails.</p>
            </div>
        </div>

        <!-- Pillar 4 -->
        <div class="bg-white border border-zinc-200/80 p-5 rounded-lg space-y-3 shadow-sm hover:border-zinc-950 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="p-2 bg-zinc-50 border border-zinc-200 rounded-md">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest font-mono">Pillar 04</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900">Team OS</h3>
                <p class="text-xs text-zinc-500 mt-1 leading-relaxed">Restricts assistant access. Dynamically coordinates lead flow and tracks multi-office showing schedules.</p>
            </div>
        </div>

    </div>

    <!-- Two-Column Interactive Segment -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Column A: Cost Audit Calculator -->
        <div class="bg-white border border-zinc-200 rounded-xl p-6 space-y-6 shadow-sm">
            <div>
                <h2 class="text-base font-bold text-zinc-900">Interactive Cost Audit Calculator</h2>
                <p class="text-xs text-zinc-500 mt-1">Select the tools you currently pay for to see how much revenue Cora OS returns straight to your bottom line.</p>
            </div>

            <!-- Checklist -->
            <div class="space-y-3">
                
                <div class="flex items-center justify-between p-3 border border-zinc-150 rounded-lg hover:bg-zinc-50/50 transition-colors cursor-pointer select-none" onclick="coraToggleTool('typeform')">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="tool-typeform" checked class="rounded border-zinc-300 text-zinc-950 focus:ring-0 cursor-pointer pointer-events-none">
                        <div>
                            <span class="text-xs font-bold text-zinc-800 block">Form Builder (Typeform / WPForms Pro)</span>
                            <span class="text-[10px] text-zinc-400 block">Required for customer questionnaires</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-zinc-850 font-mono">₹1,500/mo</span>
                </div>

                <div class="flex items-center justify-between p-3 border border-zinc-150 rounded-lg hover:bg-zinc-50/50 transition-colors cursor-pointer select-none" onclick="coraToggleTool('calendly')">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="tool-calendly" checked class="rounded border-zinc-300 text-zinc-950 focus:ring-0 cursor-pointer pointer-events-none">
                        <div>
                            <span class="text-xs font-bold text-zinc-800 block">Scheduler (Calendly Pro)</span>
                            <span class="text-[10px] text-zinc-400 block">Required for booking home tours</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-zinc-850 font-mono">₹1,200/mo</span>
                </div>

                <div class="flex items-center justify-between p-3 border border-zinc-150 rounded-lg hover:bg-zinc-50/50 transition-colors cursor-pointer select-none" onclick="coraToggleTool('storage')">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="tool-storage" checked class="rounded border-zinc-300 text-zinc-950 focus:ring-0 cursor-pointer pointer-events-none">
                        <div>
                            <span class="text-xs font-bold text-zinc-800 block">Storage & Shared Vaults (Google Drive)</span>
                            <span class="text-[10px] text-zinc-400 block">Required for secure buyer documents</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-zinc-850 font-mono">₹650/mo</span>
                </div>

                <div class="flex items-center justify-between p-3 border border-zinc-150 rounded-lg hover:bg-zinc-50/50 transition-colors cursor-pointer select-none" onclick="coraToggleTool('crm')">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="tool-crm" checked class="rounded border-zinc-300 text-zinc-950 focus:ring-0 cursor-pointer pointer-events-none">
                        <div>
                            <span class="text-xs font-bold text-zinc-800 block">Agency CRM & Commissions Board</span>
                            <span class="text-[10px] text-zinc-400 block">Required for listing matching</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-zinc-850 font-mono">₹3,500/mo</span>
                </div>

                <div class="flex items-center justify-between p-3 border border-zinc-150 rounded-lg hover:bg-zinc-50/50 transition-colors cursor-pointer select-none" onclick="coraToggleTool('whatsapp')">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="tool-whatsapp" checked class="rounded border-zinc-300 text-zinc-950 focus:ring-0 cursor-pointer pointer-events-none">
                        <div>
                            <span class="text-xs font-bold text-zinc-800 block">WhatsApp Dispatch API / Twilio gateway</span>
                            <span class="text-[10px] text-zinc-400 block">Required for instant notifications</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-zinc-850 font-mono">₹1,500/mo</span>
                </div>

            </div>

            <!-- Results Card -->
            <div class="bg-zinc-50 border border-zinc-200/80 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="space-y-1 text-center sm:text-left">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block font-mono">Annual Stack Cost Saved</span>
                    <h3 class="text-2xl font-black text-zinc-950 font-mono" id="cal-savings-display">₹1,00,200</h3>
                    <span class="text-[10px] text-zinc-500">Cora OS Consolidated Price: <strong class="text-zinc-850">₹2,000/month</strong></span>
                </div>
                <div class="w-full sm:w-[150px] bg-zinc-200 h-2.5 rounded-full overflow-hidden shrink-0">
                    <div id="savings-progress-bar" class="bg-zinc-950 h-full transition-all duration-300" style="width: 100%;"></div>
                </div>
            </div>

        </div>

        <!-- Column B: Speed-to-Lead Simulator -->
        <div class="bg-white border border-zinc-200 rounded-xl p-6 space-y-6 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-zinc-900">Lead Flow Speed Simulator</h2>
                <p class="text-xs text-zinc-500 mt-1">Simulate the speed-to-lead latency. Click path nodes to see why agencies lose 90% of listing inquiries.</p>
            </div>

            <!-- Path Toggles -->
            <div class="grid grid-cols-2 gap-3">
                <button id="btn-path-traditional" class="py-2.5 border border-zinc-200 rounded-lg text-xs font-bold text-zinc-650 hover:bg-zinc-50 transition-all cursor-pointer flex items-center justify-center gap-1.5" onclick="coraToggleSimPath('traditional')">
                    <span class="w-2 h-2 rounded-full bg-zinc-300" id="indicator-traditional"></span>
                    Manual Portal Path
                </button>
                <button id="btn-path-cora" class="py-2.5 border border-zinc-900 bg-zinc-950 text-white rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm" onclick="coraToggleSimPath('cora')">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" id="indicator-cora"></span>
                    Cora OS Auto Path
                </button>
            </div>

            <!-- Simulator Board -->
            <div class="border border-zinc-200 bg-[#FBFaf7] rounded-xl p-5 flex flex-col justify-between gap-4 h-[220px] relative overflow-hidden">
                
                <!-- Flow node connections -->
                <div class="flex items-center justify-between w-full relative z-10">
                    
                    <div class="flex flex-col items-center gap-1.5">
                        <span class="p-2 bg-white border border-zinc-200 rounded-lg shadow-sm" id="sim-node-inquiry">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-800">
                                <circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                        </span>
                        <span class="text-[9px] font-bold text-zinc-600 font-mono">Inquiry</span>
                    </div>

                    <div class="flex-1 h-0.5 border-t border-dashed border-zinc-300 mx-2 relative">
                        <div id="sim-dot-1" class="w-2 h-2 bg-zinc-950 rounded-full absolute -top-1 left-0 transition-all duration-1000 hidden"></div>
                    </div>

                    <div class="flex flex-col items-center gap-1.5">
                        <span class="p-2 bg-white border border-zinc-200 rounded-lg shadow-sm" id="sim-node-router">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-800">
                                <polyline points="16 3 21 3 21 8"></polyline><line x1="4" y1="20" x2="21" y2="3"></line><polyline points="21 16 21 21 16 21"></polyline><line x1="15" y1="15" x2="21" y2="21"></line><line x1="4" y1="4" x2="9" y2="9"></line>
                            </svg>
                        </span>
                        <span class="text-[9px] font-bold text-zinc-600 font-mono" id="sim-node-router-text">Router</span>
                    </div>

                    <div class="flex-1 h-0.5 border-t border-dashed border-zinc-300 mx-2 relative">
                        <div id="sim-dot-2" class="w-2 h-2 bg-zinc-950 rounded-full absolute -top-1 left-0 transition-all duration-1000 hidden"></div>
                    </div>

                    <div class="flex flex-col items-center gap-1.5">
                        <span class="p-2 bg-white border border-zinc-200 rounded-lg shadow-sm" id="sim-node-dispatch">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-800">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                            </svg>
                        </span>
                        <span class="text-[9px] font-bold text-zinc-600 font-mono" id="sim-node-dispatch-text">WhatsApp</span>
                    </div>

                </div>

                <!-- Simulation outcome box -->
                <div class="bg-zinc-50 border border-zinc-200 rounded-lg p-3 relative z-10 flex items-center gap-3">
                    <span class="p-2 bg-emerald-50 text-emerald-800 rounded-lg" id="sim-status-icon">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </span>
                    <div>
                        <span class="text-xs font-bold text-zinc-850 block" id="sim-status-title">Speed-to-lead time: 1.2 seconds</span>
                        <span class="text-[10px] text-zinc-500 block leading-tight font-normal" id="sim-status-desc font-sans">Cora REST router matches lead variables and delivers the digital PDF brochure instantly.</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- YouTube Strategy and Figma Prompts -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
        
        <!-- FigJam Prompt Widget -->
        <div class="bg-white border border-zinc-200 rounded-xl p-5 md:col-span-2 space-y-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900">Figma AI FigJam Whiteboard Prompt</h3>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Copy this curated prompt to generate a beautiful overview whiteboard inside FigJam.</p>
                </div>
                <button class="px-3 py-1.5 bg-zinc-950 text-white rounded text-[11px] font-bold hover:bg-zinc-850 active:scale-95 transition-all cursor-pointer flex items-center gap-1.5 shadow-sm" onclick="coraCopyFigJamPrompt()">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    Copy Prompt
                </button>
            </div>
            
            <div class="p-3.5 bg-zinc-50 border border-zinc-200/85 rounded-lg">
                <p id="figjam-prompt-block" class="text-xs text-zinc-650 leading-relaxed font-mono select-text">Create a professional whiteboard diagram for a Real Estate operating system named Cora OS. The diagram must structure 4 clear pillars: 1. Engagement OS (Notion-style lead forms & Stripe pay), 2. Showcase OS (Fast Google Fonts preload portfolios & watermarks), 3. Vault OS (Secured password-protected Aadhaar share with visitor audit logs), and 4. Team OS (Manager vs showing assistant dashboard roles). Use neutral shades of white, charcoal gray, and cream backgrounds. Connect the blocks in a flow starting from Portal Lead Inquiry -> Cora REST Endpoint -> Dynamic Database Log -> Immediate Automatic WhatsApp brochures delivery. Make the diagram clean, clear, and extremely easy for a non-technical property dealer to understand.</p>
            </div>
        </div>

        <!-- YouTube Live checklist -->
        <div class="bg-white border border-zinc-200 rounded-xl p-5 space-y-4 shadow-sm">
            <div>
                <h3 class="text-sm font-bold text-zinc-900">Build-In-Public Streams</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Quick reference checklist for your upcoming YouTube live coding streams.</p>
            </div>
            
            <div class="space-y-2 text-xs">
                
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="font-bold text-zinc-800">Stream 1: Slashed Bills</span>
                    <span class="text-[10px] font-bold text-zinc-400 font-mono">PLANNING</span>
                </div>

                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="font-bold text-zinc-800">Stream 2: Speed-to-Lead</span>
                    <span class="text-[10px] font-bold text-zinc-400 font-mono">PLANNING</span>
                </div>

                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="font-bold text-zinc-800">Stream 3: Secured Vaults</span>
                    <span class="text-[10px] font-bold text-zinc-400 font-mono">PLANNING</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="font-bold text-zinc-800">Stream 4: Multi-Branch</span>
                    <span class="text-[10px] font-bold text-zinc-400 font-mono">PLANNING</span>
                </div>

            </div>
        </div>

    </div>

</div>

<!-- Interactive presentation scripts -->
<script>
    // Local custom Toast Notification implementation for standalone presenter
    window.coraShowToast = function(message) {
        let toastContainer = document.getElementById('cora-toast-container');
        if (!toastContainer) return;
        
        // Prevent duplicate toast stacking
        let duplicateFound = false;
        Array.from(toastContainer.children).forEach(function(child) {
            if (child.querySelector('span').textContent === message) {
                duplicateFound = true;
            }
        });
        if (duplicateFound) return;

        const toastId = 'toast-' + Date.now();
        const toastEl = document.createElement('div');
        toastEl.id = toastId;
        toastEl.className = "bg-zinc-950 text-white text-xs font-semibold px-4 py-2.5 rounded-lg shadow-xl border border-zinc-800 flex items-center gap-2 pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0 select-none";
        toastEl.innerHTML = `
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <span>${message}</span>
        `;
        toastContainer.appendChild(toastEl);
        
        setTimeout(() => {
            toastEl.classList.remove('translate-y-2', 'opacity-0');
        }, 50);
        
        setTimeout(() => {
            toastEl.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => {
                toastEl.remove();
            }, 300);
        }, 3000);
    };

    // Tools pricing index
    const toolsData = {
        typeform: { price: 1500, active: true },
        calendly: { price: 1200, active: true },
        storage: { price: 650, active: true },
        crm: { price: 3500, active: true },
        whatsapp: { price: 1500, active: true }
    };

    function coraToggleTool(toolId) {
        const checkbox = document.getElementById('tool-' + toolId);
        if (!checkbox) return;
        
        toolsData[toolId].active = !toolsData[toolId].active;
        checkbox.checked = toolsData[toolId].active;
        
        coraRecalculateSavings();
    }

    function coraRecalculateSavings() {
        let totalCost = 0;
        let totalPossibleCost = 0;
        
        for (const key in toolsData) {
            totalPossibleCost += toolsData[key].price;
            if (toolsData[key].active) {
                totalCost += toolsData[key].price;
            }
        }
        
        const annualSavings = totalCost * 12;
        const display = document.getElementById('cal-savings-display');
        display.textContent = '₹' + annualSavings.toLocaleString('en-IN');
        
        const percent = totalPossibleCost > 0 ? (totalCost / totalPossibleCost) * 100 : 0;
        const bar = document.getElementById('savings-progress-bar');
        bar.style.width = percent + '%';
    }

    // Lead Flow simulator path toggle
    function coraToggleSimPath(path) {
        const trBtn = document.getElementById('btn-path-traditional');
        const coBtn = document.getElementById('btn-path-cora');
        
        const trIndicator = document.getElementById('indicator-traditional');
        const coIndicator = document.getElementById('indicator-cora');
        
        const nodeRouter = document.getElementById('sim-node-router-text');
        const nodeDispatch = document.getElementById('sim-node-dispatch-text');
        
        const statusIcon = document.getElementById('sim-status-icon');
        const statusTitle = document.getElementById('sim-status-title');
        const statusDesc = document.getElementById('sim-status-desc');
        
        const dot1 = document.getElementById('sim-dot-1');
        const dot2 = document.getElementById('sim-dot-2');

        dot1.className = 'w-2 h-2 rounded-full absolute -top-1 left-0 transition-all duration-1000 hidden';
        dot2.className = 'w-2 h-2 rounded-full absolute -top-1 left-0 transition-all duration-1000 hidden';

        if (path === 'traditional') {
            trBtn.className = "py-2.5 border border-zinc-900 bg-zinc-950 text-white rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm";
            coBtn.className = "py-2.5 border border-zinc-200 rounded-lg text-xs font-bold text-zinc-650 hover:bg-zinc-50 transition-all cursor-pointer flex items-center justify-center gap-1.5";
            
            trIndicator.className = "w-2 h-2 rounded-full bg-rose-500 animate-pulse";
            coIndicator.className = "w-2 h-2 rounded-full bg-zinc-300";

            nodeRouter.textContent = "Agent Excel";
            nodeDispatch.textContent = "WhatsApp (Manual)";

            statusIcon.className = "p-2 bg-rose-50 text-rose-800 rounded-lg";
            statusIcon.innerHTML = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`;
            
            statusTitle.textContent = "Speed-to-lead time: 4.5 Hours";
            statusDesc.textContent = "Lead sits in email client. Agent manually copies parameters, finds PDF on phone, and texts client. 85% client drop-off rate.";
            
            setTimeout(() => {
                dot1.className = 'w-2 h-2 bg-rose-500 rounded-full absolute -top-1 left-0 transition-all duration-[2000ms] block';
                setTimeout(() => {
                    dot1.style.left = '100%';
                    setTimeout(() => {
                        dot2.className = 'w-2 h-2 bg-rose-500 rounded-full absolute -top-1 left-0 transition-all duration-[2000ms] block';
                        setTimeout(() => {
                            dot2.style.left = '100%';
                        }, 50);
                    }, 2000);
                }, 50);
            }, 100);

        } else {
            coBtn.className = "py-2.5 border border-zinc-900 bg-zinc-950 text-white rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm";
            trBtn.className = "py-2.5 border border-zinc-200 rounded-lg text-xs font-bold text-zinc-650 hover:bg-zinc-50 transition-all cursor-pointer flex items-center justify-center gap-1.5";
            
            coIndicator.className = "w-2 h-2 rounded-full bg-emerald-400 animate-pulse";
            trIndicator.className = "w-2 h-2 rounded-full bg-zinc-300";

            nodeRouter.textContent = "REST Router";
            nodeDispatch.textContent = "WhatsApp (Auto)";

            statusIcon.className = "p-2 bg-emerald-50 text-emerald-800 rounded-lg";
            statusIcon.innerHTML = `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>`;

            statusTitle.textContent = "Speed-to-lead time: 1.2 Seconds";
            statusDesc.textContent = "Cora REST router matches lead variables and delivers the digital PDF brochure instantly. Zero manual copying required.";

            setTimeout(() => {
                dot1.className = 'w-2 h-2 bg-emerald-500 rounded-full absolute -top-1 left-0 transition-all duration-[400ms] block';
                setTimeout(() => {
                    dot1.style.left = '100%';
                    setTimeout(() => {
                        dot2.className = 'w-2 h-2 bg-emerald-500 rounded-full absolute -top-1 left-0 transition-all duration-[400ms] block';
                        setTimeout(() => {
                            dot2.style.left = '100%';
                        }, 50);
                    }, 400);
                }, 50);
            }, 100);
        }
    }

    // Copy FigJam Prompt to Clipboard callback with copy fallback
    function coraCopyFigJamPrompt() {
        const text = document.getElementById('figjam-prompt-block').textContent;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).catch(err => {
                console.log('Clipboard write failed', err);
            });
        } else {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
            } catch (err) {}
            document.body.removeChild(textArea);
        }
        
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast("FigJam Prompt copied to clipboard!");
        }
    }

    // Auto-trigger simulation on load
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(() => {
            coraToggleSimPath('cora');
        }, 500);
    });
</script>

</body>
</html>
