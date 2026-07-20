<?php
$allowed_roles = array('administrator', 'cora_shruti', 'cora_super_admin');
if (!in_array($current_user_role, $allowed_roles) && !cora_is_super_owner()) {
    echo '<div class="text-zinc-400 text-sm p-8">Access denied.</div>';
    return;
}
?>
<div class="space-y-8" style="font-family: system-ui, sans-serif;">

  <!-- Section 1: Page Header -->
  <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <div class="flex items-center gap-3">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900 dark:text-zinc-100"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        <div>
          <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 m-0 leading-tight">Cora Roles & Platform Overview</h2>
          <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 mt-1">Restricted to workspace administrators. Manage role definitions, permissions, and monitor platform capabilities.</p>
        </div>
      </div>
      <div class="flex items-center gap-1.5 px-2.5 py-1 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 rounded-md text-[10px] font-bold uppercase tracking-wider">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        Admin Only
      </div>
    </div>
    <div class="flex flex-wrap gap-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
      <div class="text-xs text-zinc-600 dark:text-zinc-400"><span class="font-semibold text-zinc-900 dark:text-zinc-100">Total Roles:</span> 6</div>
      <div class="text-zinc-300 dark:text-zinc-700 hidden sm:block">|</div>
      <div class="text-xs text-zinc-600 dark:text-zinc-400"><span class="font-semibold text-zinc-900 dark:text-zinc-100">Platform Version:</span> <?php echo defined('CORA_WORKSPACE_VERSION') ? CORA_WORKSPACE_VERSION : 'v2.2.0'; ?></div>
      <div class="text-zinc-300 dark:text-zinc-700 hidden sm:block">|</div>
      <div class="text-xs text-zinc-600 dark:text-zinc-400"><span class="font-semibold text-zinc-900 dark:text-zinc-100">Features:</span> 24</div>
      <div class="text-zinc-300 dark:text-zinc-700 hidden sm:block">|</div>
      <div class="text-xs text-zinc-600 dark:text-zinc-400"><span class="font-semibold text-zinc-900 dark:text-zinc-100">Last Updated:</span> <?php echo date('M j, Y'); ?></div>
    </div>
  </div>

  <!-- Section 2: Role Hierarchy Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- 1. Cora Super Owner -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-950 dark:border-zinc-100 rounded-xl p-5 space-y-4">
      <div class="flex items-center gap-2">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900 dark:text-zinc-100"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Cora Super Owner</h3>
      </div>
      <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1">
        <div class="bg-zinc-900 dark:bg-zinc-100 rounded-full h-1" style="width:100%"></div>
      </div>
      <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 leading-relaxed">Unrestricted god-level access. Can view all workspaces, impersonate users, and manage the entire Cora platform. Only assigned to Shruti.</p>
      <ul class="space-y-1.5 pl-0 m-0 list-none">
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Platform Control Panel</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Impersonate any user</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>View all workspaces</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Manage all settings</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Access update system</li>
      </ul>
      <p class="text-[10px] text-zinc-400 dark:text-zinc-500 italic m-0 pt-2 border-t border-zinc-100 dark:border-zinc-800">Cannot be assigned — tied to platform account</p>
    </div>

    <!-- 2. Workspace Administrator -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl p-5 space-y-4">
      <div class="flex items-center gap-2">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900 dark:text-zinc-100"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Workspace Administrator</h3>
      </div>
      <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1">
        <div class="bg-zinc-900 dark:bg-zinc-100 rounded-full h-1" style="width:85%"></div>
      </div>
      <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 leading-relaxed">Full control over their workspace. Can manage users, roles, settings, and all workspace content.</p>
      <ul class="space-y-1.5 pl-0 m-0 list-none">
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Manage members & roles</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Configure geofencing</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>View attendance reports</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Access all workspace data</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Manage integrations</li>
      </ul>
    </div>

    <!-- 3. Manager -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 space-y-4">
      <div class="flex items-center gap-2">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900 dark:text-zinc-100"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Manager</h3>
      </div>
      <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1">
        <div class="bg-zinc-900 dark:bg-zinc-100 rounded-full h-1" style="width:65%"></div>
      </div>
      <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 leading-relaxed">Oversees team operations. Can view reports, approve bookings, and manage daily workflows.</p>
      <ul class="space-y-1.5 pl-0 m-0 list-none">
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>View all team logs</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Manage bookings</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Approve requests</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>View analytics</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Export reports</li>
      </ul>
    </div>

    <!-- 4. Agent / Staff Member -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 space-y-4">
      <div class="flex items-center gap-2">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900 dark:text-zinc-100"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Agent / Staff Member</h3>
      </div>
      <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1">
        <div class="bg-zinc-900 dark:bg-zinc-100 rounded-full h-1" style="width:40%"></div>
      </div>
      <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 leading-relaxed">Regular team member. Can perform daily tasks, log attendance, and access assigned modules.</p>
      <ul class="space-y-1.5 pl-0 m-0 list-none">
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Log punch in/out</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>View own records</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Access assigned tools</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Submit forms</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>View announcements</li>
      </ul>
    </div>

    <!-- 5. Client / Guest -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 space-y-4">
      <div class="flex items-center gap-2">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900 dark:text-zinc-100"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Client / Guest</h3>
      </div>
      <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1">
        <div class="bg-zinc-900 dark:bg-zinc-100 rounded-full h-1" style="width:20%"></div>
      </div>
      <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 leading-relaxed">External stakeholder with read-only access to selected documents and portals.</p>
      <ul class="space-y-1.5 pl-0 m-0 list-none">
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>View shared documents</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Access public galleries</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Submit contact forms</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>View invoices</li>
        <li class="flex items-start gap-2 text-xs text-zinc-700 dark:text-zinc-300"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Download approved files</li>
      </ul>
    </div>

    <!-- 6. Custom Roles -->
    <div class="bg-white dark:bg-zinc-900 border border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 space-y-4 flex flex-col">
      <div class="flex items-center gap-2">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900 dark:text-zinc-100"><path d="M12 20v-8"></path><path d="M8 16l4-4 4 4"></path><path d="M12 4v4"></path></svg>
        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Custom Roles</h3>
      </div>
      <div class="w-full flex gap-1 h-1">
        <div class="bg-zinc-900 dark:bg-zinc-100 rounded-full h-1 w-1/4"></div>
        <div class="bg-zinc-900 dark:bg-zinc-100 rounded-full h-1 w-1/4"></div>
        <div class="bg-zinc-300 dark:bg-zinc-700 rounded-full h-1 w-1/4"></div>
        <div class="bg-zinc-300 dark:bg-zinc-700 rounded-full h-1 w-1/4"></div>
      </div>
      <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 leading-relaxed">Workspace admins can define custom roles through the User Management panel.</p>
      <p class="text-[10px] text-zinc-400 dark:text-zinc-500 italic m-0 pt-2 mt-auto border-t border-zinc-100 dark:border-zinc-800">Managed from Users & Roles → Custom Roles tab</p>
    </div>
  </div>

  <!-- Section 3: Feature Access Matrix -->
  <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
    <div class="p-5 border-b border-zinc-200/80 dark:border-zinc-800">
      <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Feature Access Matrix</h3>
      <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 mt-1">Breakdown of standard permissions across core roles.</p>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-xs text-left">
        <thead>
          <tr class="bg-zinc-50 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800">
            <th class="px-5 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Feature</th>
            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px] text-center">Super Owner</th>
            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px] text-center">Admin</th>
            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px] text-center">Manager</th>
            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px] text-center">Agent</th>
            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px] text-center">Client</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
          <?php
          $full = '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" class="text-zinc-900 dark:text-zinc-100 mx-auto"><circle cx="12" cy="12" r="6"/></svg>';
          $partial = '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" class="text-zinc-400 mx-auto"><circle cx="12" cy="12" r="6"/></svg>';
          $none = '<span class="block text-center text-zinc-200 dark:text-zinc-700 font-bold">—</span>';

          $rows = [
            ["Dashboard Access", $full, $full, $full, $full, $none],
            ["User Management", $full, $full, $none, $none, $none],
            ["Role Configuration", $full, $full, $none, $none, $none],
            ["Attendance Logs (all)", $full, $full, $full, $none, $none],
            ["Attendance Logs (own)", $full, $full, $full, $full, $none],
            ["GPS Geofencing Setup", $full, $full, $none, $none, $none],
            ["Email Automations", $full, $full, $none, $none, $none],
            ["Punch Header Widget", $full, $full, $full, $full, $none],
            ["Workspace Settings", $full, $full, $none, $none, $none],
            ["Billing & Subscription", $full, $full, $none, $none, $none],
            ["Media Library", $full, $full, $full, $full, $none],
            ["Canvas Builder", $full, $full, $partial, $none, $none],
            ["Forms & Submissions", $full, $full, $full, $partial, $none],
            ["Document Vault", $full, $full, $full, $partial, $partial],
            ["Audit Logs", $full, $full, $partial, $none, $none],
            ["Platform Control Panel", $full, $none, $none, $none, $none],
            ["Impersonation Engine", $full, $none, $none, $none, $none],
            ["Backup & Recovery", $full, $full, $none, $none, $none],
            ["Custom Branding", $full, $full, $none, $none, $none],
            ["API & Integrations", $full, $full, $partial, $none, $none]
          ];

          foreach ($rows as $row) {
            echo '<tr class="odd:bg-white even:bg-zinc-50/40 dark:odd:bg-zinc-900 dark:even:bg-zinc-950/30 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">';
            echo '<td class="px-5 py-3 text-xs font-semibold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">' . htmlspecialchars($row[0]) . '</td>';
            for ($i = 1; $i <= 5; $i++) {
              echo '<td class="px-4 py-3">' . $row[$i] . '</td>';
            }
            echo '</tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Section 4: Platform Changelog -->
  <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
    <div class="mb-6">
      <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Platform Changelog</h3>
      <p class="text-xs text-zinc-500 dark:text-zinc-400 m-0 mt-1">Recent feature additions and system updates</p>
    </div>
    <div class="border-l-2 border-zinc-200 dark:border-zinc-800 ml-3 pl-6 space-y-8">
      
      <!-- Entry 1 -->
      <div class="relative">
        <span class="absolute -left-[31px] top-1 w-3 h-3 rounded-full bg-zinc-900 dark:bg-zinc-100 ring-4 ring-white dark:ring-zinc-900"></span>
        <div class="space-y-2">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[10px] font-bold px-2 py-0.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 rounded-full">v2.2.0</span>
            <span class="text-[10px] text-zinc-400 dark:text-zinc-500"><?php echo date('M j, Y'); ?></span>
          </div>
          <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">GPS Geofencing & Attendance Automation</h4>
          <ul class="space-y-1.5 pl-0 m-0 list-none">
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-900 dark:text-zinc-100"><polyline points="20 6 9 17 4 12"></polyline></svg>Office geofencing with Google Maps URL parser</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-900 dark:text-zinc-100"><polyline points="20 6 9 17 4 12"></polyline></svg>Daily punch email reminders (9AM & 6PM)</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-900 dark:text-zinc-100"><polyline points="20 6 9 17 4 12"></polyline></svg>Admin summary report (8PM daily)</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-900 dark:text-zinc-100"><polyline points="20 6 9 17 4 12"></polyline></svg>Header punch widget with GPS verification</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-900 dark:text-zinc-100"><polyline points="20 6 9 17 4 12"></polyline></svg>CSV attendance report export</li>
          </ul>
        </div>
      </div>

      <!-- Entry 2 -->
      <div class="relative">
        <span class="absolute -left-[31px] top-1 w-3 h-3 rounded-full bg-zinc-300 dark:bg-zinc-600 ring-4 ring-white dark:ring-zinc-900"></span>
        <div class="space-y-2">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[10px] font-bold px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-full">v2.1.1</span>
          </div>
          <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Platform Control Panel & User Impersonation</h4>
          <ul class="space-y-1.5 pl-0 m-0 list-none">
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Super admin control panel</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Secure user impersonation with HMAC verification</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Role-based dynamic sidebar injection</li>
          </ul>
        </div>
      </div>

      <!-- Entry 3 -->
      <div class="relative">
        <span class="absolute -left-[31px] top-1 w-3 h-3 rounded-full bg-zinc-300 dark:bg-zinc-600 ring-4 ring-white dark:ring-zinc-900"></span>
        <div class="space-y-2">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[10px] font-bold px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-full">v2.0.0</span>
          </div>
          <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Investor-Ready Rename & Modular Architecture</h4>
          <ul class="space-y-1.5 pl-0 m-0 list-none">
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Plugin renamed to cora-workspace</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Multi-industry module system</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Automated database option migration</li>
          </ul>
        </div>
      </div>

      <!-- Entry 4 -->
      <div class="relative">
        <span class="absolute -left-[31px] top-1 w-3 h-3 rounded-full bg-zinc-300 dark:bg-zinc-600 ring-4 ring-white dark:ring-zinc-900"></span>
        <div class="space-y-2">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[10px] font-bold px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-full">v1.6.4</span>
          </div>
          <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Auto-Updater & One-Click Upgrades</h4>
          <ul class="space-y-1.5 pl-0 m-0 list-none">
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>GitHub manifest-based auto-update</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>One-click update button</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Silent background plugin extraction</li>
          </ul>
        </div>
      </div>

      <!-- Entry 5 -->
      <div class="relative">
        <span class="absolute -left-[31px] top-1 w-3 h-3 rounded-full bg-zinc-300 dark:bg-zinc-600 ring-4 ring-white dark:ring-zinc-900"></span>
        <div class="space-y-2">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[10px] font-bold px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-full">v1.6.3</span>
          </div>
          <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">White-Labeling & WordPress Concealment</h4>
          <ul class="space-y-1.5 pl-0 m-0 list-none">
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Custom fatal error handler</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Email sender white-labeling</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Login redirect to /workspace/login</li>
          </ul>
        </div>
      </div>

      <!-- Entry 6 -->
      <div class="relative">
        <span class="absolute -left-[31px] top-1 w-3 h-3 rounded-full bg-zinc-300 dark:bg-zinc-600 ring-4 ring-white dark:ring-zinc-900"></span>
        <div class="space-y-2">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[10px] font-bold px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-full">v1.6.0</span>
          </div>
          <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 m-0">Unified Workspace Platform Launch</h4>
          <ul class="space-y-1.5 pl-0 m-0 list-none">
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Multi-module workspace platform</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Command palette (Cmd+K) search</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Real-time sidebar search</li>
            <li class="flex items-start gap-2 text-xs text-zinc-500 dark:text-zinc-400"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 text-zinc-400 dark:text-zinc-500"><polyline points="20 6 9 17 4 12"></polyline></svg>Dark/light mode support</li>
          </ul>
        </div>
      </div>

    </div>
  </div>

</div>
