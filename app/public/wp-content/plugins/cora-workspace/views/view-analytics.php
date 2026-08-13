<?php
/**
 * Cora Workspace - Workspace Analytics
 * File: views/view-analytics.php
 * Premium, monochromatic high-fidelity interactive analytics dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Sample stats
$metrics = array(
    array(
        'label' => 'Total Bookings',
        'value' => '142',
        'delta' => '+12.4% vs last month',
        'trend' => 'up'
    ),
    array(
        'label' => 'Lead Conversions',
        'value' => '24.8%',
        'delta' => '+4.1% vs last month',
        'trend' => 'up'
    ),
    array(
        'label' => 'Monthly Revenue',
        'value' => '₹4,82,900',
        'delta' => '+8.7% vs last month',
        'trend' => 'up'
    ),
    array(
        'label' => 'Active Crew Members',
        'value' => '12',
        'delta' => 'Steady state',
        'trend' => 'neutral'
    )
);

// Performance rankings
$crew_rankings = array(
    array(
        'name'      => 'Rajesh Sharma',
        'role'      => 'Lead Broker',
        'completed' => '32 Shoots',
        'rating'    => '4.9★'
    ),
    array(
        'name'      => 'Anil Kumar',
        'role'      => 'Photographer',
        'completed' => '28 Shoots',
        'rating'    => '4.8★'
    ),
    array(
        'name'      => 'Neha Malhotra',
        'role'      => 'Legal Broker',
        'completed' => '21 audits',
        'rating'    => '5.0★'
    )
);
?>

<div class="space-y-6 font-sans text-zinc-900 select-none max-w-[1700px] mx-auto pb-12">
    <!-- Page Header -->
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-zinc-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-950">● Workspace Business Intelligence</h1>
            <p class="text-xs font-medium text-zinc-500 mt-1">Analyze lead conversion rates, revenue performance metrics, and crew operation rankings.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="if(window.coraShowToast) window.coraShowToast('Analytics report exported as PDF.', 'success')" class="px-4 py-2 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 text-xs font-bold rounded-xl transition-all shadow-2xs cursor-pointer flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                Export Report PDF
            </button>
        </div>
    </header>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ($metrics as $metric) : ?>
            <div class="bg-white border border-zinc-200 p-5 rounded-2xl shadow-3xs space-y-2">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block"><?php echo esc_html($metric['label']); ?></span>
                <div class="text-2xl font-black text-zinc-950 tracking-tight font-mono"><?php echo esc_html($metric['value']); ?></div>
                <div class="flex items-center gap-1 text-[10px] font-bold text-zinc-500">
                    <?php if ($metric['trend'] === 'up') : ?>
                        <span class="text-emerald-600">▲</span>
                    <?php endif; ?>
                    <span><?php echo esc_html($metric['delta']); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Charts & Tables Split Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 2/3 Width: Chart Mockup -->
        <div class="lg:col-span-2 bg-white border border-zinc-200 rounded-2xl p-6 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
                <h3 class="text-xs font-bold text-zinc-850 uppercase tracking-wider">Revenue Trend Line (Last 6 Months)</h3>
                <span class="text-[10px] font-extrabold text-zinc-400 bg-zinc-50 px-2.5 py-1 rounded-full uppercase tracking-wider">FY 2026</span>
            </div>

            <!-- Custom High-Fidelity SVG Chart -->
            <div class="h-64 flex flex-col justify-between pt-4 relative select-none">
                <div class="flex-1 flex items-end justify-between border-b border-zinc-150 pb-2 px-4 relative">
                    <!-- Grid background lines -->
                    <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-20">
                        <div class="w-full h-px bg-zinc-400"></div>
                        <div class="w-full h-px bg-zinc-400"></div>
                        <div class="w-full h-px bg-zinc-400"></div>
                        <div class="w-full h-px bg-zinc-400"></div>
                    </div>

                    <!-- Custom SVG Sparkline Path -->
                    <svg viewBox="0 0 500 150" class="absolute inset-0 w-full h-full pr-4 pb-2" preserveAspectRatio="none">
                        <path d="M 0 130 Q 100 110 200 70 T 400 30 T 500 10" fill="none" stroke="currentColor" stroke-width="2.5" class="text-zinc-950"/>
                        <circle cx="200" cy="70" r="4.5" class="text-zinc-900 fill-white stroke-2" stroke="currentColor"/>
                        <circle cx="400" cy="30" r="4.5" class="text-zinc-900 fill-white stroke-2" stroke="currentColor"/>
                        <circle cx="500" cy="10" r="4.5" class="text-zinc-900 fill-white stroke-2" stroke="currentColor"/>
                    </svg>

                    <!-- Chart Bars/Nodes Hover Placeholders -->
                    <div class="h-full w-8 hover:bg-zinc-50 transition-all rounded"></div>
                    <div class="h-full w-8 hover:bg-zinc-50 transition-all rounded"></div>
                    <div class="h-full w-8 hover:bg-zinc-50 transition-all rounded"></div>
                    <div class="h-full w-8 hover:bg-zinc-50 transition-all rounded"></div>
                    <div class="h-full w-8 hover:bg-zinc-50 transition-all rounded"></div>
                    <div class="h-full w-8 hover:bg-zinc-50 transition-all rounded"></div>
                </div>

                <!-- X Axis Month Labels -->
                <div class="flex justify-between px-4 pt-2 text-[10px] font-bold text-zinc-400 uppercase tracking-widest font-mono select-none">
                    <span>Mar</span>
                    <span>Apr</span>
                    <span>May</span>
                    <span>Jun</span>
                    <span>Jul</span>
                    <span>Aug</span>
                </div>
            </div>
        </div>

        <!-- 1/3 Width: Crew Performance Leaderboard -->
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-2xs space-y-4">
            <div class="border-b border-zinc-100 pb-4">
                <h3 class="text-xs font-bold text-zinc-850 uppercase tracking-wider">Top Team Performers</h3>
            </div>

            <div class="space-y-4">
                <?php foreach ($crew_rankings as $idx => $crew) : ?>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50/50 hover:bg-zinc-50 rounded-xl border border-zinc-150 transition-all">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg font-extrabold flex items-center justify-center text-[10px] bg-zinc-900 text-white"><?php echo ($idx + 1); ?></span>
                            <div>
                                <h4 class="text-xs font-bold text-zinc-950"><?php echo esc_html($crew['name']); ?></h4>
                                <p class="text-[10px] text-zinc-500 font-medium"><?php echo esc_html($crew['role']); ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-extrabold text-zinc-900"><?php echo esc_html($crew['completed']); ?></div>
                            <span class="text-[9px] font-extrabold text-zinc-450 uppercase tracking-wider"><?php echo esc_html($crew['rating']); ?> score</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
