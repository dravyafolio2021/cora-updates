<?php
/**
 * Cora Workspace - Workspace Automations & Workflows
 * File: views/view-automations.php
 * Premium, monochromatic high-fidelity interactive automation control suite.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Sample workflows data
$workflows = array(
    array(
        'id'        => 'wf_101',
        'name'      => 'Booking WhatsApp Confirmation & Details',
        'trigger'   => 'When a Shoot Booking is Confirmed',
        'action'    => 'Send WhatsApp Template to Client & Assignee',
        'status'    => 'Active',
        'runs_24h'  => 14,
        'success'   => '100%',
        'icon'      => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>'
    ),
    array(
        'id'        => 'wf_102',
        'name'      => 'Leads Geo-Location & Assigned Alerts',
        'trigger'   => 'When new lead enters Funnel',
        'action'    => 'Audit GPS parameters & Email team member',
        'status'    => 'Active',
        'runs_24h'  => 32,
        'success'   => '98.2%',
        'icon'      => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>'
    ),
    array(
        'id'        => 'wf_103',
        'name'      => 'E-Signature Document Vault Auto-Restoration',
        'trigger'   => 'When contract is fully E-Signed',
        'action'    => 'Generate PDF, archive in vault, notify owner',
        'status'    => 'Active',
        'runs_24h'  => 8,
        'success'   => '100%',
        'icon'      => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>'
    ),
    array(
        'id'        => 'wf_104',
        'name'      => 'Daily Operational Summary Report Dispatch',
        'trigger'   => 'Cron trigger: Every day at 8:00 PM',
        'action'    => 'Aggregate logs, compile metrics, dispatch email',
        'status'    => 'Active',
        'runs_24h'  => 1,
        'success'   => '100%',
        'icon'      => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'
    ),
    array(
        'id'        => 'wf_105',
        'name'      => 'Google Business Review Follow-up Campaign',
        'trigger'   => 'When shoot completes successfully',
        'action'    => 'Wait 24 hrs -> Send review invite via email & SMS',
        'status'    => 'Disabled',
        'runs_24h'  => 0,
        'success'   => '-',
        'icon'      => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>'
    )
);

// Sample logs data
$recent_logs = array(
    array(
        'time'     => '10 mins ago',
        'workflow' => 'wf_101',
        'event'    => 'WhatsApp sent successfully to Vipul Malhotra (Shoot #4991)',
        'type'     => 'success'
    ),
    array(
        'time'     => '1 hour ago',
        'workflow' => 'wf_102',
        'event'    => 'New Lead: Johnathan Smith assigned to Rajesh Sharma (E-mail sent)',
        'type'     => 'success'
    ),
    array(
        'time'     => '3 hours ago',
        'workflow' => 'wf_103',
        'event'    => 'E-Sign contract fully verified for Apex Realty Partners. Archived.',
        'type'     => 'success'
    ),
    array(
        'time'     => '4 hours ago',
        'workflow' => 'wf_101',
        'event'    => 'WhatsApp sent successfully to Aria Group (Showing #3902)',
        'type'     => 'success'
    )
);
?>

<div class="space-y-6 font-sans text-zinc-900 select-none max-w-[1700px] mx-auto pb-12">
    <!-- Page Header -->
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-zinc-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-950">● Automations & Workflows</h1>
            <p class="text-xs font-medium text-zinc-500 mt-1">Configure workspace event triggers, notifications, and cron automations.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="if(window.coraShowToast) window.coraShowToast('Trigger test execution completed.', 'success')" class="px-4 py-2 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 text-xs font-bold rounded-xl transition-all shadow-2xs cursor-pointer flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                Test Run All
            </button>
            <button onclick="if(window.coraShowToast) window.coraShowToast('Workflow Builder coming soon!', 'info')" class="px-4.5 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Create Workflow
            </button>
        </div>
    </header>

    <!-- Main Workspace Split Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- 3/4 Width: Active Workflows List -->
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white border border-zinc-200 rounded-2xl shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-150 flex items-center justify-between">
                    <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Active Workspace Workflows</span>
                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-extrabold rounded-full">4 Active</span>
                </div>

                <div class="divide-y divide-zinc-100">
                    <?php foreach ($workflows as $wf) : ?>
                        <div class="p-5 hover:bg-zinc-50/50 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="p-3 bg-zinc-100 text-zinc-800 rounded-xl shrink-0">
                                    <?php echo $wf['icon']; ?>
                                </div>
                                <div class="space-y-1">
                                    <h3 class="text-sm font-bold text-zinc-900"><?php echo esc_html($wf['name']); ?></h3>
                                    <div class="flex flex-wrap items-center gap-2 text-[11px] text-zinc-500 font-medium">
                                        <span class="font-bold uppercase text-[9px] bg-zinc-200/80 px-2 py-0.5 rounded text-zinc-650">Trigger</span>
                                        <span><?php echo esc_html($wf['trigger']); ?></span>
                                        <span class="font-bold text-zinc-400">·</span>
                                        <span class="font-bold uppercase text-[9px] bg-zinc-200/80 px-2 py-0.5 rounded text-zinc-650">Action</span>
                                        <span><?php echo esc_html($wf['action']); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-6 shrink-0 md:justify-end">
                                <div class="text-right hidden md:block">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">24H Runs</div>
                                    <div class="text-xs font-extrabold text-zinc-900 font-mono"><?php echo $wf['runs_24h']; ?></div>
                                </div>
                                <div class="text-right hidden md:block">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Success</div>
                                    <div class="text-xs font-extrabold text-zinc-900 font-mono"><?php echo $wf['success']; ?></div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full <?php echo ($wf['status'] === 'Active') ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-100 text-zinc-500'; ?>">
                                        <?php echo esc_html($wf['status']); ?>
                                    </span>
                                    <!-- Toggle Switch Button -->
                                    <button class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none <?php echo ($wf['status'] === 'Active') ? 'bg-zinc-950' : 'bg-zinc-200'; ?>" onclick="if(window.coraShowToast) window.coraShowToast('Workflow status toggled.', 'info')">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out <?php echo ($wf['status'] === 'Active') ? 'translate-x-4' : 'translate-x-0'; ?>"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 1/4 Width: Real-time Execution Log -->
        <div class="space-y-4">
            <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Live Run History</span>
                    <button class="text-[10px] font-extrabold text-zinc-400 hover:text-zinc-900 cursor-pointer" onclick="if(window.coraShowToast) window.coraShowToast('Logs refreshed.', 'success')">Refresh</button>
                </div>

                <div class="space-y-3.5">
                    <?php foreach ($recent_logs as $log) : ?>
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-[10px] font-bold">
                                <span class="text-zinc-450 uppercase"><?php echo esc_html($log['time']); ?></span>
                                <span class="text-emerald-600">✓ Success</span>
                            </div>
                            <p class="text-xs font-medium text-zinc-800 leading-normal"><?php echo esc_html($log['event']); ?></p>
                            <div class="w-full h-px bg-zinc-100 pt-1"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
