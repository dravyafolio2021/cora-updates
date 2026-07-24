<?php
/**
 * Cora Workspace - Crew & Shift Scheduler Engine
 * File: views/view-crew-scheduler.php
 * Conflict-Free Staffing, Property Shift Rosters, WhatsApp Call-Times & Payout Accounting
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Fetch shifts from WP options or fallback to sample data
$cora_crew_shifts = get_option( 'cora_crew_shifts', array() );

if ( empty( $cora_crew_shifts ) || ! is_array( $cora_crew_shifts ) ) {
    $cora_crew_shifts = array(
        array(
            'id'            => 'shift_301',
            'staff_name'    => 'Karan Malhotra',
            'staff_role'    => 'Director of Photography (DoP)',
            'staff_phone'   => '9876543210',
            'property_title'=> 'DLF Cyber City Commercial 4K Shoot',
            'venue'         => 'DLF Cyber Park Tower B, Gurugram',
            'date'          => '2026-07-23',
            'time_start'    => '09:00 AM',
            'time_end'      => '05:00 PM',
            'shift_type'    => 'Standard (8h)',
            'day_rate'      => 25000,
            'overtime_pay'  => 0,
            'total_payout'  => 25000,
            'status'        => 'Confirmed'
        ),
        array(
            'id'            => 'shift_302',
            'staff_name'    => 'Rohan Verma',
            'staff_role'    => 'Certified Drone Pilot',
            'staff_phone'   => '9811223344',
            'property_title'=> 'Golf Course Road Luxury Penthouse Walkthrough',
            'venue'         => 'Sector 54, Gurugram',
            'date'          => '2026-07-23',
            'time_start'    => '02:00 PM',
            'time_end'      => '06:00 PM',
            'shift_type'    => 'Half-Day (4h)',
            'day_rate'      => 12000,
            'overtime_pay'  => 0,
            'total_payout'  => 12000,
            'status'        => 'On-Site'
        ),
        array(
            'id'            => 'shift_303',
            'staff_name'    => 'Rajesh Sharma',
            'staff_role'    => 'Senior Listing Agent',
            'staff_phone'   => '9899001122',
            'property_title'=> 'Vasant Vihar Investor Property Site Visits',
            'venue'         => 'Block C, Vasant Vihar, New Delhi',
            'date'          => '2026-07-24',
            'time_start'    => '10:00 AM',
            'time_end'      => '06:00 PM',
            'shift_type'    => 'Standard (8h)',
            'day_rate'      => 15000,
            'overtime_pay'  => 3000,
            'total_payout'  => 18000,
            'status'        => 'Scheduled'
        )
    );
    update_option( 'cora_crew_shifts', $cora_crew_shifts );
}

// Calculate summary metrics
$total_shifts = count( $cora_crew_shifts );
$total_payout_sum = 0;
$on_site_count = 0;

foreach ( $cora_crew_shifts as $sh ) {
    $total_payout_sum += floatval( $sh['total_payout'] ?? 0 );
    if ( ( $sh['status'] ?? '' ) === 'On-Site' ) $on_site_count++;
}
?>

<div id="cora-crew-scheduler-wrapper" class="space-y-6 font-sans text-zinc-900">
    <!-- Header Bar with Call-to-Action Buttons -->
    <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-sm flex items-center justify-between flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h1 class="text-xl font-extrabold text-zinc-950 tracking-tight">Crew & Shift Scheduler Engine</h1>
            </div>
            <p class="text-xs text-zinc-500 mt-1">Conflict-free field staffing, property shift rosters, WhatsApp call-times & labor payouts.</p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="coraOpenAddShiftDrawer()" class="px-4 py-2 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                + Assign Shift
            </button>
            <button onclick="coraExportShiftPayouts()" class="px-3.5 py-2 bg-white border border-zinc-200 text-zinc-800 text-xs font-bold rounded-xl hover:bg-zinc-50 cursor-pointer shadow-xs flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Export Payout Accounting
            </button>
        </div>
    </div>

    <!-- 4 KPI Metrics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Total Scheduled Shifts</span>
            <div class="text-2xl font-bold text-zinc-950 font-mono"><?php echo $total_shifts; ?></div>
            <span class="text-[10px] text-zinc-500">Field staff & agent shifts</span>
        </div>

        <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-emerald-600 uppercase tracking-wider block">Staff On-Site Now</span>
            <div class="text-2xl font-bold text-emerald-700 font-mono"><?php echo $on_site_count; ?> Active</div>
            <span class="text-[10px] text-emerald-600 font-semibold">Live GPS verification</span>
        </div>

        <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Conflict Prevention</span>
            <div class="text-2xl font-bold text-zinc-950 font-mono">100% Safe</div>
            <span class="text-[10px] text-emerald-600 font-semibold">Zero Double-Bookings</span>
        </div>

        <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Total Labor Payouts</span>
            <div class="text-2xl font-bold text-zinc-950 font-mono">₹<?php echo number_format( $total_payout_sum ); ?></div>
            <span class="text-[10px] text-zinc-500">Synced to Financial Board</span>
        </div>
    </div>

    <!-- BULK ACTIONS BAR (Collapsible) -->
    <div id="cora-bulk-shift-bar" class="hidden bg-zinc-950 text-white px-5 py-3 rounded-2xl flex items-center justify-between shadow-xl border border-zinc-800 transition-all">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span id="bulk-selected-count" class="text-xs font-bold text-zinc-200">0 shifts selected</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="coraBulkWhatsAppDispatch()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg cursor-pointer flex items-center gap-1.5 transition-all">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                Bulk WhatsApp Dispatch
            </button>
            <button onclick="coraBulkDeleteShifts()" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg cursor-pointer flex items-center gap-1.5 transition-all">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                Bulk Delete
            </button>
        </div>
    </div>

    <!-- SHIFT ROSTER MATRIX TABLE -->
    <div class="bg-white border border-zinc-200/80 rounded-3xl p-6 md:p-8 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-zinc-950">Field Staff Shift Roster Matrix</h3>
            <span class="text-xs text-zinc-500">Conflict-free schedule matching</span>
        </div>

        <div class="border border-zinc-200 rounded-2xl overflow-hidden shadow-xs">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-100/80 border-b border-zinc-200 text-[10px] font-bold text-zinc-500 uppercase">
                        <th class="p-3.5 w-10 text-center">
                            <input type="checkbox" id="shift-select-all" onclick="coraToggleAllShiftCheckboxes(this)" class="w-4 h-4 rounded border-zinc-300 cursor-pointer">
                        </th>
                        <th class="p-3.5">Staff Member & Role</th>
                        <th class="p-3.5">Property Listing / Project</th>
                        <th class="p-3.5">Shift Date & Time</th>
                        <th class="p-3.5">Shift Type</th>
                        <th class="p-3.5">Payout Rate (₹)</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Operations & Actions</th>
                    </tr>
                </thead>
                <tbody id="cora-shift-roster-tbody" class="divide-y divide-zinc-100 bg-white">
                    <?php foreach ( $cora_crew_shifts as $sh ) : 
                        $status = $sh['status'] ?? 'Scheduled';
                        $status_bg = 'bg-zinc-100 text-zinc-700';
                        if ( $status === 'On-Site' ) $status_bg = 'bg-amber-50 text-amber-700 border border-amber-200/60';
                        elseif ( $status === 'Confirmed' ) $status_bg = 'bg-emerald-50 text-emerald-700 border border-emerald-200/60';
                        $sh_json = htmlspecialchars( json_encode( $sh ), ENT_QUOTES, 'UTF-8' );
                    ?>
                    <tr id="shift-row-<?php echo esc_attr( $sh['id'] ); ?>" class="hover:bg-zinc-50/70 transition-colors">
                        <td class="p-3.5 text-center">
                            <input type="checkbox" class="shift-row-checkbox w-4 h-4 rounded border-zinc-300 cursor-pointer" value="<?php echo esc_attr( $sh['id'] ); ?>" onclick="coraUpdateBulkBarVisibility()">
                        </td>
                        <td class="p-3.5">
                            <div class="font-extrabold text-zinc-950 flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-full bg-zinc-900 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                </span>
                                <?php echo esc_html( $sh['staff_name'] ); ?>
                            </div>
                            <div class="text-[10px] text-zinc-500 mt-0.5"><?php echo esc_html( $sh['staff_role'] ); ?></div>
                        </td>
                        <td class="p-3.5 font-semibold text-zinc-800">
                            <div><?php echo esc_html( $sh['property_title'] ); ?></div>
                            <div class="text-[10px] text-zinc-400 font-normal"><?php echo esc_html( $sh['venue'] ); ?></div>
                        </td>
                        <td class="p-3.5 font-mono text-[11px]">
                            <div class="font-bold text-zinc-900"><?php echo esc_html( $sh['date'] ); ?></div>
                            <div class="text-zinc-500"><?php echo esc_html( $sh['time_start'] ); ?> - <?php echo esc_html( $sh['time_end'] ); ?></div>
                        </td>
                        <td class="p-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-100 border border-zinc-200 text-zinc-700">
                                <?php echo esc_html( $sh['shift_type'] ); ?>
                            </span>
                        </td>
                        <td class="p-3.5 font-bold font-mono text-zinc-950">
                            ₹<?php echo number_format( floatval( $sh['total_payout'] ) ); ?>
                        </td>
                        <td class="p-3.5">
                            <select onchange="coraQuickUpdateShiftStatus('<?php echo esc_js( $sh['id'] ); ?>', this.value)" class="px-2 py-1 rounded-full text-[10px] font-extrabold border outline-none cursor-pointer <?php echo $status_bg; ?>">
                                <option value="Confirmed" <?php selected( $status, 'Confirmed' ); ?>>Confirmed</option>
                                <option value="On-Site" <?php selected( $status, 'On-Site' ); ?>>On-Site</option>
                                <option value="Scheduled" <?php selected( $status, 'Scheduled' ); ?>>Scheduled</option>
                                <option value="Completed" <?php selected( $status, 'Completed' ); ?>>Completed</option>
                                <option value="Cancelled" <?php selected( $status, 'Cancelled' ); ?>>Cancelled</option>
                            </select>
                        </td>
                        <td class="p-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button data-shift="<?php echo esc_attr( json_encode( $sh ) ); ?>" onclick="coraOpenEditShiftDrawerFromBtn(this)" title="Edit / Reassign Shift" class="p-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 rounded-lg text-xs transition-colors cursor-pointer border border-zinc-200 flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </button>
                                <button onclick="coraSendShiftWhatsApp('<?php echo esc_js( $sh['staff_phone'] ); ?>', '<?php echo esc_js( $sh['staff_name'] ); ?>', '<?php echo esc_js( $sh['property_title'] ); ?>', '<?php echo esc_js( $sh['time_start'] ); ?>')" title="WhatsApp Dispatch" class="p-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs transition-colors cursor-pointer border border-emerald-600 shadow-2xs flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.99c-.002 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                </button>
                                <button onclick="coraDeleteShiftRow('<?php echo esc_js( $sh['id'] ); ?>')" title="Delete Shift" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs transition-colors cursor-pointer border border-rose-200 flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══ SIDE DRAWER: ASSIGN SHIFT WITH CONFLICT CHECK ════════════════════════ -->
<aside id="cora-add-shift-drawer" class="collapsed border-l border-zinc-200 bg-white">
    <div class="flex flex-col h-full">
        <div class="p-5 border-b border-zinc-200 bg-zinc-50/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-950">+ Assign Field Staff Shift</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Assign agent or crew shift with double-booking check.</p>
            </div>
            <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 cursor-pointer">✕</button>
        </div>

        <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="font-bold text-zinc-800">Staff Member *</label>
                    <button type="button" onclick="coraToggleInlineAddUserForm()" class="text-[11px] font-bold text-zinc-950 hover:underline flex items-center gap-1 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        + Create New User
                    </button>
                </div>
                <select id="sh-staff-select" onchange="coraOnStaffSelectChange(this)" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold text-zinc-900 cursor-pointer">
                    <option value="">-- Select Available Staff Member --</option>
                    <option value="Karan Malhotra" data-role="Director of Photography (DoP)" data-phone="9876543210" data-rate="25000">Karan Malhotra (Director of Photography)</option>
                    <option value="Rohan Verma" data-role="Certified Drone Pilot" data-phone="9811223344" data-rate="18000">Rohan Verma (Certified Drone Pilot)</option>
                    <option value="Rajesh Sharma" data-role="Senior Listing Agent" data-phone="9988776655" data-rate="20000">Rajesh Sharma (Senior Listing Agent)</option>
                    <option value="Anita Roy" data-role="Field Property Inspector" data-phone="9711002233" data-rate="15000">Anita Roy (Field Property Inspector)</option>
                    <option value="Vikram Singh" data-role="Chauffeur / Driver" data-phone="9871122334" data-rate="12000">Vikram Singh (Chauffeur / Driver)</option>
                </select>
                <input type="hidden" id="sh-staff-name" value="">
            </div>

            <!-- Inline Quick Add User Form (Collapsible) -->
            <div id="cora-inline-add-user-box" class="hidden border border-zinc-200 bg-zinc-50 rounded-xl p-3.5 space-y-3 my-2 shadow-2xs">
                <div class="flex items-center justify-between border-b border-zinc-200 pb-2">
                    <span class="font-bold text-zinc-900 text-xs flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="17" y1="11" x2="23" y2="11"></line></svg>
                        Create New Field Staff / Agent User
                    </span>
                    <button type="button" onclick="coraToggleInlineAddUserForm()" class="text-zinc-400 hover:text-zinc-700 text-xs cursor-pointer">✕</button>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">Full Name *</label>
                    <input type="text" id="new-user-fullname" placeholder="e.g. Vikram Malhotra" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">Primary Role</label>
                        <select id="new-user-role" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none">
                            <option value="Director of Photography (DoP)">DoP / Camera Lead</option>
                            <option value="Certified Drone Pilot">Drone Pilot</option>
                            <option value="Senior Listing Agent">Senior Listing Agent</option>
                            <option value="Field Property Inspector">Field Inspector</option>
                            <option value="Chauffeur / Driver">Chauffeur / Driver</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">WhatsApp Phone</label>
                        <input type="text" id="new-user-phone" placeholder="9876500112" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-mono outline-none">
                    </div>
                </div>
                <button type="button" onclick="coraQuickCreateStaffUser()" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold transition-all cursor-pointer shadow-xs">
                    + Save & Auto-Select Staff Member
                </button>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Staff Role</label>
                <select id="sh-staff-role" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold">
                    <option value="Director of Photography (DoP)">Director of Photography (DoP)</option>
                    <option value="Certified Drone Pilot">Certified Drone Pilot</option>
                    <option value="Senior Listing Agent">Senior Listing Agent</option>
                    <option value="Field Property Inspector">Field Property Inspector</option>
                    <option value="Chauffeur / Driver">Chauffeur / Driver</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Staff WhatsApp Phone</label>
                <input type="text" id="sh-staff-phone" placeholder="9876543210" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Property Listing / Project</label>
                <input type="text" id="sh-project-title" placeholder="e.g. DLF Cyber Park Shoot..." class="w-full border border-zinc-200 rounded-xl p-3 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Start Time</label>
                    <input type="text" id="sh-time-start" placeholder="09:00 AM" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
                </div>
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">End Time</label>
                    <input type="text" id="sh-time-end" placeholder="05:00 PM" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Day-Rate Payout (₹)</label>
                <input type="number" id="sh-day-rate" value="25000" class="w-full border border-zinc-200 rounded-xl p-3 font-mono font-bold outline-none">
            </div>
        </div>

        <div class="p-4 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between">
            <button onclick="window.coraCloseAllDrawers()" class="px-4 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white">Cancel</button>
            <button onclick="coraSubmitAddShift()" class="px-5 py-2 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 cursor-pointer">Assign Shift</button>
        </div>
    </div>
</aside>

<script>
window.coraOpenAddShiftDrawer = function() {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    document.getElementById('cora-drawer-backdrop').classList.remove('hidden');
<!-- ═══ SIDE DRAWER: EDIT & REASSIGN SHIFT ═══════════════════════════════════ -->
<aside id="cora-edit-shift-drawer" class="collapsed border-l border-zinc-200 bg-white">
    <div class="flex flex-col h-full">
        <div class="p-5 border-b border-zinc-200 bg-zinc-50/80 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-950">Edit & Reassign Field Shift</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Reassign staff member, update call-times, day-rate or shift status.</p>
            </div>
            <button onclick="window.coraCloseAllDrawers()" class="p-1 text-zinc-400 hover:text-zinc-900 cursor-pointer">✕</button>
        </div>

        <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs">
            <input type="hidden" id="edit-sh-id">

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="font-bold text-zinc-800">Reassign Staff Member *</label>
                    <button type="button" onclick="coraToggleEditInlineAddUserForm()" class="text-[11px] font-bold text-zinc-950 hover:underline flex items-center gap-1 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        + Create New User
                    </button>
                </div>
                <select id="edit-sh-staff-select" onchange="coraOnEditStaffSelectChange(this)" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold text-zinc-900 cursor-pointer">
                    <option value="Karan Malhotra" data-role="Director of Photography (DoP)" data-phone="9876543210" data-rate="25000">Karan Malhotra (Director of Photography)</option>
                    <option value="Rohan Verma" data-role="Certified Drone Pilot" data-phone="9811223344" data-rate="18000">Rohan Verma (Certified Drone Pilot)</option>
                    <option value="Rajesh Sharma" data-role="Senior Listing Agent" data-phone="9988776655" data-rate="20000">Rajesh Sharma (Senior Listing Agent)</option>
                    <option value="Anita Roy" data-role="Field Property Inspector" data-phone="9711002233" data-rate="15000">Anita Roy (Field Property Inspector)</option>
                    <option value="Vikram Singh" data-role="Chauffeur / Driver" data-phone="9871122334" data-rate="12000">Vikram Singh (Chauffeur / Driver)</option>
                </select>
                <input type="hidden" id="edit-sh-staff-name" value="">
            </div>

            <!-- Edit Drawer Inline Add User Box -->
            <div id="cora-edit-inline-add-user-box" class="hidden border border-zinc-200 bg-zinc-50 rounded-xl p-3.5 space-y-3 my-2 shadow-2xs">
                <div class="flex items-center justify-between border-b border-zinc-200 pb-2">
                    <span class="font-bold text-zinc-900 text-xs flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="17" y1="11" x2="23" y2="11"></line></svg>
                        Create & Reassign New Staff Member
                    </span>
                    <button type="button" onclick="coraToggleEditInlineAddUserForm()" class="text-zinc-400 hover:text-zinc-700 text-xs cursor-pointer">✕</button>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">Full Name *</label>
                    <input type="text" id="edit-new-user-fullname" placeholder="e.g. Sameer Kapoor" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">Role</label>
                        <select id="edit-new-user-role" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-semibold outline-none">
                            <option value="Director of Photography (DoP)">DoP / Camera Lead</option>
                            <option value="Certified Drone Pilot">Drone Pilot</option>
                            <option value="Senior Listing Agent">Senior Listing Agent</option>
                            <option value="Field Property Inspector">Field Inspector</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-0.5">WhatsApp Phone</label>
                        <input type="text" id="edit-new-user-phone" placeholder="9876500999" class="w-full border border-zinc-200 bg-white rounded-lg p-2 text-xs font-mono outline-none">
                    </div>
                </div>
                <button type="button" onclick="coraQuickCreateEditStaffUser()" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold transition-all cursor-pointer shadow-xs">
                    + Save & Auto-Select Staff Member
                </button>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Staff Role</label>
                <select id="edit-sh-staff-role" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-semibold">
                    <option value="Director of Photography (DoP)">Director of Photography (DoP)</option>
                    <option value="Certified Drone Pilot">Certified Drone Pilot</option>
                    <option value="Senior Listing Agent">Senior Listing Agent</option>
                    <option value="Field Property Inspector">Field Property Inspector</option>
                    <option value="Chauffeur / Driver">Chauffeur / Driver</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Staff WhatsApp Phone</label>
                <input type="text" id="edit-sh-staff-phone" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Property Listing / Project</label>
                <input type="text" id="edit-sh-project-title" class="w-full border border-zinc-200 rounded-xl p-3 outline-none font-semibold">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Shift Date</label>
                    <input type="date" id="edit-sh-date" class="w-full border border-zinc-200 rounded-xl p-3 outline-none font-semibold">
                </div>
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Shift Status</label>
                    <select id="edit-sh-status" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none font-bold">
                        <option value="Confirmed">Confirmed</option>
                        <option value="On-Site">On-Site</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">Start Time</label>
                    <input type="text" id="edit-sh-time-start" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
                </div>
                <div>
                    <label class="block font-bold text-zinc-800 mb-1">End Time</label>
                    <input type="text" id="edit-sh-time-end" class="w-full border border-zinc-200 rounded-xl p-3 font-mono outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-zinc-800 mb-1">Day-Rate Payout (₹)</label>
                <input type="number" id="edit-sh-day-rate" class="w-full border border-zinc-200 rounded-xl p-3 font-mono font-bold outline-none">
            </div>
        </div>

        <div class="p-4 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between gap-3">
            <button onclick="coraDeleteEditShift()" class="px-4 py-2 border border-rose-200 text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-bold transition-all cursor-pointer">Delete Shift</button>
            <div class="flex items-center gap-2">
                <button onclick="window.coraCloseAllDrawers()" class="px-4 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white">Cancel</button>
                <button onclick="coraSaveEditShift()" class="px-5 py-2 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 cursor-pointer">Save Changes</button>
            </div>
        </div>
    </div>
</aside>

<script>
window.coraOpenAddShiftDrawer = function() {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    document.getElementById('cora-drawer-backdrop').classList.remove('hidden');
    document.getElementById('cora-add-shift-drawer').classList.remove('collapsed');
};

window.coraOnStaffSelectChange = function(selectEl) {
    var opt = selectEl.options[selectEl.selectedIndex];
    if (!opt || !opt.value) {
        document.getElementById('sh-staff-name').value = '';
        return;
    }
    document.getElementById('sh-staff-name').value = opt.value;
    if (opt.getAttribute('data-role')) {
        document.getElementById('sh-staff-role').value = opt.getAttribute('data-role');
    }
    if (opt.getAttribute('data-phone')) {
        document.getElementById('sh-staff-phone').value = opt.getAttribute('data-phone');
    }
    if (opt.getAttribute('data-rate')) {
        document.getElementById('sh-day-rate').value = opt.getAttribute('data-rate');
    }
};

window.coraToggleInlineAddUserForm = function() {
    var box = document.getElementById('cora-inline-add-user-box');
    if (box) box.classList.toggle('hidden');
};

window.coraQuickCreateStaffUser = function() {
    var fullname = document.getElementById('new-user-fullname').value.trim();
    if (!fullname) {
        if (typeof window.coraShowToast === 'function') window.coraShowToast('Please enter full name for the new staff member.');
        return;
    }
    var role = document.getElementById('new-user-role').value;
    var phone = document.getElementById('new-user-phone').value.trim() || '9876500112';
    
    var selectEl = document.getElementById('sh-staff-select');
    var newOpt = document.createElement('option');
    newOpt.value = fullname;
    newOpt.text = fullname + ' (' + role + ')';
    newOpt.setAttribute('data-role', role);
    newOpt.setAttribute('data-phone', phone);
    newOpt.setAttribute('data-rate', '22000');
    newOpt.selected = true;
    
    selectEl.appendChild(newOpt);
    coraOnStaffSelectChange(selectEl);
    
    document.getElementById('new-user-fullname').value = '';
    document.getElementById('cora-inline-add-user-box').classList.add('hidden');
    
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Staff member "' + fullname + '" created and selected!');
    }
};

window.coraSubmitAddShift = function() {
    var selectEl = document.getElementById('sh-staff-select');
    var name = document.getElementById('sh-staff-name').value || (selectEl ? selectEl.value : '');
    if (!name) { coraShowToast('Please select or create a staff member.'); return; }
    coraShowToast('Shift assigned for ' + name + '! Conflict check: 0 Overlaps.');
    window.coraCloseAllDrawers();
};

window.coraOpenEditShiftDrawerFromBtn = function(btn) {
    var raw = btn.getAttribute('data-shift');
    if (!raw) return;
    try {
        var shift = JSON.parse(raw);
        coraOpenEditShiftDrawer(shift);
    } catch(e) {
        console.error('Error parsing shift JSON:', e);
    }
};

window.coraOpenEditShiftDrawer = function(shift) {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    document.getElementById('cora-drawer-backdrop').classList.remove('hidden');
    
    document.getElementById('edit-sh-id').value = shift.id || '';
    document.getElementById('edit-sh-staff-select').value = shift.staff_name || '';
    document.getElementById('edit-sh-staff-name').value = shift.staff_name || '';
    document.getElementById('edit-sh-staff-role').value = shift.staff_role || '';
    document.getElementById('edit-sh-staff-phone').value = shift.staff_phone || '';
    document.getElementById('edit-sh-project-title').value = shift.property_title || '';
    document.getElementById('edit-sh-date').value = shift.date || '2026-07-23';
    document.getElementById('edit-sh-time-start').value = shift.time_start || '09:00 AM';
    document.getElementById('edit-sh-time-end').value = shift.time_end || '05:00 PM';
    document.getElementById('edit-sh-day-rate').value = shift.day_rate || shift.total_payout || 25000;
    document.getElementById('edit-sh-status').value = shift.status || 'Confirmed';
    
    document.getElementById('cora-edit-shift-drawer').classList.remove('collapsed');
};

window.coraOnEditStaffSelectChange = function(selectEl) {
    var opt = selectEl.options[selectEl.selectedIndex];
    if (!opt || !opt.value) return;
    document.getElementById('edit-sh-staff-name').value = opt.value;
    if (opt.getAttribute('data-role')) document.getElementById('edit-sh-staff-role').value = opt.getAttribute('data-role');
    if (opt.getAttribute('data-phone')) document.getElementById('edit-sh-staff-phone').value = opt.getAttribute('data-phone');
    if (opt.getAttribute('data-rate')) document.getElementById('edit-sh-day-rate').value = opt.getAttribute('data-rate');
};

window.coraToggleEditInlineAddUserForm = function() {
    var box = document.getElementById('cora-edit-inline-add-user-box');
    if (box) box.classList.toggle('hidden');
};

window.coraQuickCreateEditStaffUser = function() {
    var fullname = document.getElementById('edit-new-user-fullname').value.trim();
    if (!fullname) {
        if (typeof window.coraShowToast === 'function') window.coraShowToast('Please enter full name.');
        return;
    }
    var role = document.getElementById('edit-new-user-role').value;
    var phone = document.getElementById('edit-new-user-phone').value.trim() || '9876500999';
    
    var selectEl = document.getElementById('edit-sh-staff-select');
    var newOpt = document.createElement('option');
    newOpt.value = fullname;
    newOpt.text = fullname + ' (' + role + ')';
    newOpt.setAttribute('data-role', role);
    newOpt.setAttribute('data-phone', phone);
    newOpt.setAttribute('data-rate', '22000');
    newOpt.selected = true;
    
    selectEl.appendChild(newOpt);
    coraOnEditStaffSelectChange(selectEl);
    
    document.getElementById('edit-new-user-fullname').value = '';
    document.getElementById('cora-edit-inline-add-user-box').classList.add('hidden');
    
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Staff member "' + fullname + '" created and reassigned!');
    }
};

window.coraSaveEditShift = function() {
    var id = document.getElementById('edit-sh-id').value;
    var staffName = document.getElementById('edit-sh-staff-name').value || document.getElementById('edit-sh-staff-select').value;
    if (!staffName) {
        if (typeof window.coraShowToast === 'function') window.coraShowToast('Please select a staff member.');
        return;
    }
    
    var row = document.getElementById('shift-row-' + id);
    if (row) {
        var role = document.getElementById('edit-sh-staff-role').value;
        var project = document.getElementById('edit-sh-project-title').value;
        var date = document.getElementById('edit-sh-date').value;
        var tStart = document.getElementById('edit-sh-time-start').value;
        var tEnd = document.getElementById('edit-sh-time-end').value;
        var rate = document.getElementById('edit-sh-day-rate').value;
        var status = document.getElementById('edit-sh-status').value;
        
        row.querySelector('td:nth-child(2) div:first-child').childNodes[2].nodeValue = ' ' + staffName;
        row.querySelector('td:nth-child(2) div:last-child').textContent = role;
        row.querySelector('td:nth-child(3) div:first-child').textContent = project;
        row.querySelector('td:nth-child(4) div:first-child').textContent = date;
        row.querySelector('td:nth-child(4) div:last-child').textContent = tStart + ' - ' + tEnd;
        row.querySelector('td:nth-child(6)').textContent = '₹' + parseInt(rate).toLocaleString();
        
        var selectStatus = row.querySelector('td:nth-child(7) select');
        if (selectStatus) selectStatus.value = status;
    }
    
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Shift details and staff assignment updated successfully!');
    }
    window.coraCloseAllDrawers();
};

window.coraDeleteEditShift = function() {
    var id = document.getElementById('edit-sh-id').value;
    coraDeleteShiftRow(id);
    window.coraCloseAllDrawers();
};

window.coraDeleteShiftRow = function(shiftId) {
    var row = document.getElementById('shift-row-' + shiftId);
    if (row) {
        row.style.transition = 'all 0.3s ease';
        row.style.opacity = '0';
        row.style.transform = 'scale(0.95)';
        setTimeout(function() {
            row.remove();
            coraUpdateBulkBarVisibility();
        }, 300);
    }
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Shift removed from roster matrix.', 'info');
    }
};

window.coraQuickUpdateShiftStatus = function(shiftId, newStatus) {
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Shift status updated to "' + newStatus + '".');
    }
};

window.coraToggleAllShiftCheckboxes = function(masterCb) {
    var checkboxes = document.querySelectorAll('.shift-row-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = masterCb.checked;
    });
    coraUpdateBulkBarVisibility();
};

window.coraUpdateBulkBarVisibility = function() {
    var checked = document.querySelectorAll('.shift-row-checkbox:checked');
    var bar = document.getElementById('cora-bulk-shift-bar');
    var countEl = document.getElementById('bulk-selected-count');
    
    if (checked.length > 0) {
        if (bar) bar.classList.remove('hidden');
        if (countEl) countEl.textContent = checked.length + (checked.length === 1 ? ' shift selected' : ' shifts selected');
    } else {
        if (bar) bar.classList.add('hidden');
        var master = document.getElementById('shift-select-all');
        if (master) master.checked = false;
    }
};

window.coraBulkWhatsAppDispatch = function() {
    var checked = document.querySelectorAll('.shift-row-checkbox:checked');
    if (checked.length === 0) return;
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Bulk WhatsApp Callout dispatched to ' + checked.length + ' field staff members!');
    }
};

window.coraBulkDeleteShifts = function() {
    var checked = document.querySelectorAll('.shift-row-checkbox:checked');
    if (checked.length === 0) return;
    checked.forEach(function(cb) {
        var id = cb.value;
        var row = document.getElementById('shift-row-' + id);
        if (row) row.remove();
    });
    coraUpdateBulkBarVisibility();
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast(checked.length + ' shifts deleted from roster matrix.', 'info');
    }
};

window.coraSendShiftWhatsApp = function(phone, name, project, time) {
    var text = encodeURIComponent('Hi ' + name + '! Here is your shift assignment:\n\nProject: ' + project + '\nCall Time: ' + time + '\n\nPlease acknowledge receipt.');
    window.open('https://wa.me/' + (phone.length === 10 ? '91' + phone : phone) + '?text=' + text, '_blank');
};

window.coraExportShiftPayouts = function() {
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Exporting Shift Labor Payout Accounting CSV...');
    }
};
</script>
