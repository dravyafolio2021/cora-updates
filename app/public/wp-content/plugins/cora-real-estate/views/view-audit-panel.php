<?php
/**
 * Cora Real Estate CRM - Agency Subscription Cost Audit View
 * Studio-Grade Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Agency Cost & Tool Audit</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Identify SaaS subscription overlap, estimate operational leaks, and verify the impact of a consolidated workspace.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button class="cora-btn-secondary px-3.5 py-2 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 font-semibold rounded-md text-xs transition-colors cursor-pointer flex items-center gap-2 shadow-sm" onclick="resetToMockup()">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
            Reset to Default
        </button>
    </div>
</div>

<!-- Interactive Sheet Container -->
<div class="bg-white border border-zinc-200/80 rounded-xl shadow-sm overflow-hidden space-y-0">
    <div class="px-5 py-4 border-b border-zinc-150 bg-zinc-50/50 flex justify-between items-center">
        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Indian Agency Tool Subscription Sheet (10-Agent Team)</h3>
        <span class="text-[11px] font-mono text-zinc-500 uppercase">Live Calculator Mode</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-xs text-zinc-800 min-w-[800px]">
            <thead>
                <tr class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 font-semibold">
                    <th class="py-2.5 px-4 w-12 text-center border-r border-zinc-200">#</th>
                    <th class="py-2.5 px-4 w-44 border-r border-zinc-200">Business Function</th>
                    <th class="py-2.5 px-4 w-48 border-r border-zinc-200">Popular Tool in India</th>
                    <th class="py-2.5 px-4 w-36 text-right border-r border-zinc-200">Monthly (INR)</th>
                    <th class="py-2.5 px-4 w-36 text-right border-r border-zinc-200">Annual (INR)</th>
                    <th class="py-2.5 px-4 border-r border-zinc-200">Pain Point / Friction</th>
                    <th class="py-2.5 px-4 w-52">Unified Solution Impact</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200">
                <!-- Row 1 -->
                <tr class="hover:bg-zinc-50/40 transition-colors">
                    <td class="py-3 px-4 text-center font-mono text-zinc-400 border-r border-zinc-100">01</td>
                    <td class="py-3 px-4 font-semibold text-zinc-900 border-r border-zinc-100">CRM & Lead Pipelines</td>
                    <td class="py-3 px-4 text-zinc-600 border-r border-zinc-100">Sell.do / Salesforce</td>
                    <td class="py-3 px-4 border-r border-zinc-100">
                        <div class="flex items-center justify-end gap-1 font-mono text-sm font-semibold text-zinc-900">
                            <span>₹</span>
                            <input type="number" id="cora-audit-m-1" class="w-20 text-right bg-transparent border border-dashed border-transparent hover:border-zinc-200 focus:border-zinc-500 focus:bg-white rounded px-1.5 py-0.5 outline-none transition-all font-semibold" value="25000" oninput="calculateAuditRow(1)">
                        </div>
                    </td>
                    <td class="py-3 px-4 text-right font-mono text-sm font-semibold text-zinc-900 border-r border-zinc-100" id="cora-audit-a-1">₹3,00,000</td>
                    <td class="py-3 px-4 text-zinc-500 border-r border-zinc-100 leading-relaxed">Too complex; requires extensive training; doesn't store direct check-in coordinates.</td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-2 py-0.5 rounded bg-zinc-950 text-white text-[10px] font-bold uppercase tracking-wider mb-1">Cora Core CRM</span>
                        <p class="text-[11px] text-zinc-500">Zero learning curve; built for realtors.</p>
                    </td>
                </tr>

                <!-- Row 2 -->
                <tr class="hover:bg-zinc-50/40 transition-colors">
                    <td class="py-3 px-4 text-center font-mono text-zinc-400 border-r border-zinc-100">02</td>
                    <td class="py-3 px-4 font-semibold text-zinc-900 border-r border-zinc-100">Field Attendance</td>
                    <td class="py-3 px-4 text-zinc-600 border-r border-zinc-100">Keka HR / Spine HR</td>
                    <td class="py-3 px-4 border-r border-zinc-100">
                        <div class="flex items-center justify-end gap-1 font-mono text-sm font-semibold text-zinc-900">
                            <span>₹</span>
                            <input type="number" id="cora-audit-m-2" class="w-20 text-right bg-transparent border border-dashed border-transparent hover:border-zinc-200 focus:border-zinc-500 focus:bg-white rounded px-1.5 py-0.5 outline-none transition-all font-semibold" value="7000" oninput="calculateAuditRow(2)">
                        </div>
                    </td>
                    <td class="py-3 px-4 text-right font-mono text-sm font-semibold text-zinc-900 border-r border-zinc-100" id="cora-audit-a-2">₹84,000</td>
                    <td class="py-3 px-4 text-zinc-500 border-r border-zinc-100 leading-relaxed">No integration with property coordinates; tracking is administrative, not operational.</td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 text-[10px] font-bold uppercase tracking-wider mb-1">GPS Geotagging</span>
                        <p class="text-[11px] text-zinc-500">Match check-ins with properties.</p>
                    </td>
                </tr>

                <!-- Row 3 -->
                <tr class="hover:bg-zinc-50/40 transition-colors">
                    <td class="py-3 px-4 text-center font-mono text-zinc-400 border-r border-zinc-100">03</td>
                    <td class="py-3 px-4 font-semibold text-zinc-900 border-r border-zinc-100">WhatsApp Automation</td>
                    <td class="py-3 px-4 text-zinc-600 border-r border-zinc-100">AiSensy / Wati / Interakt</td>
                    <td class="py-3 px-4 border-r border-zinc-100">
                        <div class="flex items-center justify-end gap-1 font-mono text-sm font-semibold text-zinc-900">
                            <span>₹</span>
                            <input type="number" id="cora-audit-m-3" class="w-20 text-right bg-transparent border border-dashed border-transparent hover:border-zinc-200 focus:border-zinc-500 focus:bg-white rounded px-1.5 py-0.5 outline-none transition-all font-semibold" value="2000" oninput="calculateAuditRow(3)">
                        </div>
                    </td>
                    <td class="py-3 px-4 text-right font-mono text-sm font-semibold text-zinc-900 border-r border-zinc-100" id="cora-audit-a-3">₹24,000</td>
                    <td class="py-3 px-4 text-zinc-500 border-r border-zinc-100 leading-relaxed">Needs manual message configuration and copy-pasting to trigger API templates.</td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 text-[10px] font-bold uppercase tracking-wider mb-1">Auto Webhooks</span>
                        <p class="text-[11px] text-zinc-500">Direct coordinate & booking alerts.</p>
                    </td>
                </tr>

                <!-- Row 4 -->
                <tr class="hover:bg-zinc-50/40 transition-colors">
                    <td class="py-3 px-4 text-center font-mono text-zinc-400 border-r border-zinc-100">04</td>
                    <td class="py-3 px-4 font-semibold text-zinc-900 border-r border-zinc-100">Media Portal Storage</td>
                    <td class="py-3 px-4 text-zinc-600 border-r border-zinc-100">Google Drive / Dropbox</td>
                    <td class="py-3 px-4 border-r border-zinc-100">
                        <div class="flex items-center justify-end gap-1 font-mono text-sm font-semibold text-zinc-900">
                            <span>₹</span>
                            <input type="number" id="cora-audit-m-4" class="w-20 text-right bg-transparent border border-dashed border-transparent hover:border-zinc-200 focus:border-zinc-500 focus:bg-white rounded px-1.5 py-0.5 outline-none transition-all font-semibold" value="13000" oninput="calculateAuditRow(4)">
                        </div>
                    </td>
                    <td class="py-3 px-4 text-right font-mono text-sm font-semibold text-zinc-900 border-r border-zinc-100" id="cora-audit-a-4">₹1,56,000</td>
                    <td class="py-3 px-4 text-zinc-500 border-r border-zinc-100 leading-relaxed">Unbranded links feel unprofessional; client requests constant download access.</td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 text-[10px] font-bold uppercase tracking-wider mb-1">Branded Portals</span>
                        <p class="text-[11px] text-zinc-500">Dedicated portfolio galleries.</p>
                    </td>
                </tr>

                <!-- Row 5 -->
                <tr class="hover:bg-zinc-50/40 transition-colors">
                    <td class="py-3 px-4 text-center font-mono text-zinc-400 border-r border-zinc-100">05</td>
                    <td class="py-3 px-4 font-semibold text-zinc-900 border-r border-zinc-100">Social Scheduling</td>
                    <td class="py-3 px-4 text-zinc-600 border-r border-zinc-100">Hootsuite / Buffer</td>
                    <td class="py-3 px-4 border-r border-zinc-100">
                        <div class="flex items-center justify-end gap-1 font-mono text-sm font-semibold text-zinc-900">
                            <span>₹</span>
                            <input type="number" id="cora-audit-m-5" class="w-20 text-right bg-transparent border border-dashed border-transparent hover:border-zinc-200 focus:border-zinc-500 focus:bg-white rounded px-1.5 py-0.5 outline-none transition-all font-semibold" value="25000" oninput="calculateAuditRow(5)">
                        </div>
                    </td>
                    <td class="py-3 px-4 text-right font-mono text-sm font-semibold text-zinc-900 border-r border-zinc-100" id="cora-audit-a-5">₹3,00,000</td>
                    <td class="py-3 px-4 text-zinc-500 border-r border-zinc-100 leading-relaxed">Requires manually downloading files and draft copies; high daily overhead.</td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 text-[10px] font-bold uppercase tracking-wider mb-1">AI Publisher</span>
                        <p class="text-[11px] text-zinc-500">Post properties directly via AI helper.</p>
                    </td>
                </tr>

                <!-- Total Row -->
                <tr class="bg-zinc-50/80 font-bold border-t-2 border-zinc-900">
                    <td class="py-3.5 px-4 text-center border-r border-zinc-200 font-mono">-</td>
                    <td class="py-3.5 px-4 border-r border-zinc-200 text-zinc-900" colspan="2">TOTAL SAAS SUBSCRIPTION OUTFLOW</td>
                    <td class="py-3.5 px-4 text-right font-mono text-sm border-r border-zinc-200 text-zinc-900" id="cora-audit-m-total">₹72,000</td>
                    <td class="py-3.5 px-4 text-right font-mono text-sm border-r border-zinc-200 text-zinc-900" id="cora-audit-a-total">₹8,64,000</td>
                    <td class="py-3.5 px-4 text-zinc-500 font-normal leading-relaxed border-r border-zinc-200" colspan="2">
                        Indian real estate agencies waste approximately <strong class="text-zinc-900 font-bold" id="cora-audit-waste-text">₹8.64 Lakhs/year</strong> in fragmented subscriptions.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="px-5 py-3 bg-zinc-50 border-t border-zinc-150 text-[11px] text-zinc-500 flex items-center gap-2">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        <span>Interactive Mode Enabled. Modify the values in the <strong>Monthly (INR)</strong> column directly to see simulated savings calculations update.</span>
    </div>
</div>

<!-- Tips / Next Step Callout -->
<div class="bg-zinc-50 border border-zinc-200 rounded-xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="space-y-1">
        <h4 class="text-sm font-bold text-zinc-900">💡 Video Presentation Simulation Tip</h4>
        <p class="text-xs text-zinc-500 leading-relaxed">Edit any subscription value above. Cora will automatically recalculate the values live on-screen, ideal for demonstration and presentations.</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="<?php echo esc_url( home_url( '/cora-landing/video-planner.html' ) ); ?>" target="_blank" class="px-4 py-2 border border-zinc-250 hover:bg-zinc-100 text-zinc-800 font-semibold rounded-md text-xs transition-colors shadow-sm flex items-center gap-1.5 no-underline">
            View Video Script
            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
        </a>
    </div>
</div>

<script>
function calculateAuditRow(rowNum) {
    const monthlyInput = document.getElementById(`cora-audit-m-${rowNum}`);
    const annualCell = document.getElementById(`cora-audit-a-${rowNum}`);
    
    if (!monthlyInput || !annualCell) return;
    
    const val = parseFloat(monthlyInput.value) || 0;
    const annualVal = val * 12;
    
    annualCell.textContent = formatAuditCurrency(annualVal);
    recalculateAuditTotals();
}

function formatAuditCurrency(amount) {
    return '₹' + amount.toLocaleString('en-IN');
}

function recalculateAuditTotals() {
    let monthlyTotal = 0;
    for (let i = 1; i <= 5; i++) {
        const input = document.getElementById(`cora-audit-m-${i}`);
        if (input) {
            monthlyTotal += parseFloat(input.value) || 0;
        }
    }
    
    const annualTotal = monthlyTotal * 12;
    
    const mTotal = document.getElementById('cora-audit-m-total');
    const aTotal = document.getElementById('cora-audit-a-total');
    const wasteText = document.getElementById('cora-audit-waste-text');
    
    if (mTotal) mTotal.textContent = formatAuditCurrency(monthlyTotal);
    if (aTotal) aTotal.textContent = formatAuditCurrency(annualTotal);
    
    if (wasteText) {
        const lakhs = (annualTotal / 100000).toFixed(2);
        wasteText.innerHTML = `₹${lakhs} Lakhs/year`;
    }
}

function resetToMockup() {
    const defaults = [25000, 7000, 2000, 13000, 25000];
    for (let i = 1; i <= 5; i++) {
        const input = document.getElementById(`cora-audit-m-${i}`);
        if (input) {
            input.value = defaults[i-1];
            calculateAuditRow(i);
        }
    }
    if (window.coraShowToast) {
        window.coraShowToast("Subscription matrix values reset to defaults.");
    }
}

// Initial calculation
recalculateAuditTotals();
</script>
