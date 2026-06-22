<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cora_users = ( in_array( $sub_page, array( 'dashboard', 'bookings', 'team-roles', 'equipment' ) ) ) ? get_users() : array();
$cora_equipment = ( in_array( $sub_page, array( 'dashboard', 'equipment' ) ) ) ? get_option( 'cora_equipment_inventory', array() ) : array();
$cora_permissions = get_option( 'cora_role_permissions', array() );
$cora_shoot_assignments = get_option( 'cora_shoot_crew_assignments', array() );
$cora_documents = get_option( 'cora_studio_documents', array() );
$current_wp_user = wp_get_current_user();
$current_user_role = ! empty( $current_wp_user->roles ) ? $current_wp_user->roles[0] : 'subscriber';

$cora_role_labels = array(
    'administrator' => 'Super Admin',
    'cora_manager' => 'Manager',
    'cora_photographer' => 'Photographer',
    'cora_videographer' => 'Videographer',
    'cora_drone_pilot' => 'Drone Pilot',
    'cora_editor' => 'Editor'
);

$current_user_display_name = $current_wp_user->exists() ? $current_wp_user->display_name : 'Dravya Bansal';
$current_user_role_label = isset($cora_role_labels[$current_user_role]) ? $cora_role_labels[$current_user_role] : ucfirst($current_user_role);
if ($current_user_role === 'administrator') {
    $current_user_role_label = 'Super Admin';
}
$current_user_avatar = $current_wp_user->exists() ? get_user_meta( $current_wp_user->ID, 'cora_avatar_url', true ) : '';

$photographers = array();
$videographers = array();
$drone_pilots = array();
$editors = array();
$all_crew_names = array();

foreach ($cora_users as $user) {
    $all_crew_names[] = $user->display_name;
    $roles = $user->roles;
    $role = !empty($roles) ? $roles[0] : '';
    if ($role === 'cora_photographer' || $role === 'administrator') {
        $photographers[] = $user;
    }
    if ($role === 'cora_videographer' || $role === 'administrator') {
        $videographers[] = $user;
    }
    if ($role === 'cora_drone_pilot' || $role === 'administrator') {
        $drone_pilots[] = $user;
    }
    if ($role === 'cora_editor' || $role === 'administrator') {
        $editors[] = $user;
    }
}

$s1_assignments = isset($cora_shoot_assignments['shoot1']) ? $cora_shoot_assignments['shoot1'] : array();
$s2_assignments = isset($cora_shoot_assignments['shoot2']) ? $cora_shoot_assignments['shoot2'] : array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cora Studio AI - Workspace</title>
    
    <!-- Load Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        workspace: {
                            sidebar: '#f7f7f5',
                            content: '#ffffff',
                        }
                    }
                }
            }
        }
    </script>
    
    <style id="cora-workspace-custom-styles">
        /* Force sans-serif typography globally within the workspace dashboard */
        *, *::before, *::after {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }

        /* Dynamic state controls for sections switching */
        .cora-page-section {
            display: none !important;
        }
        .cora-page-section.cora-active {
            display: block !important;
        }

        /* Sidebar active link styling */
        .cora-nav-item.cora-active {
            background-color: #e4e4e7 !important; /* zinc-200 */
            color: #09090b !important; /* zinc-950 */
            font-weight: 600 !important;
        }
        .cora-nav-item.cora-active .cora-nav-icon svg {
            stroke: #09090b !important;
            stroke-width: 2.2 !important;
        }

        /* Mobile bottom nav active link styling */
        .cora-bottom-nav-item.cora-active {
            color: #09090b !important;
            font-weight: 600 !important;
        }
        .cora-bottom-nav-item.cora-active svg {
            stroke: #09090b !important;
            stroke-width: 2.2 !important;
        }

        /* CRM filter buttons active styling */
        .cora-filter-tab.active {
            background-color: #09090b !important;
            color: #ffffff !important;
            border-color: #09090b !important;
        }

        /* Media SEO side selector active row styling */
        .cora-media-item-row.active {
            background-color: #f4f4f5 !important; /* zinc-100 */
            border-left: 3px solid #09090b !important;
        }

        /* Notion AI Sidebar collapsing drawer positioning */
        #cora-ai-sidebar.collapsed, 
        #cora-add-shoot-drawer.collapsed, 
        #cora-team-management-drawer.collapsed,
        #cora-add-user-drawer.collapsed,
        #cora-add-equipment-drawer.collapsed,
        #cora-assign-equipment-drawer.collapsed,
        #cora-share-drawer.collapsed {
            transform: translateX(100%) !important;
        }

        /* Switch toggle helpers */
        .cora-module-status-pill.active {
            background-color: #09090b !important;
            color: #ffffff !important;
        }
        .cora-module-status-pill.inactive {
            background-color: #f4f4f5 !important;
            color: #71717a !important;
        }

        /* Dynamic badging styles */
        .cora-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.125rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
            line-height: 1;
        }
        .cora-badge-blue {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid rgba(29, 78, 216, 0.15) !important;
        }
        .cora-badge-yellow {
            background-color: #fef3c7 !important;
            color: #d97706 !important;
            border: 1px solid rgba(217, 119, 6, 0.15) !important;
        }
        .cora-badge-green {
            background-color: #ecfdf5 !important;
            color: #047857 !important;
            border: 1px solid rgba(4, 120, 87, 0.15) !important;
        }
        .cora-badge-purple {
            background-color: #faf5ff !important;
            color: #6b21a8 !important;
            border: 1px solid rgba(107, 33, 168, 0.15) !important;
        }
        .cora-badge-orange {
            background-color: #fff7ed !important;
            color: #c2410c !important;
            border: 1px solid rgba(194, 65, 12, 0.15) !important;
        }
        .cora-badge-teal {
            background-color: #f0fdf4 !important;
            color: #0f766e !important;
            border: 1px solid rgba(15, 118, 110, 0.15) !important;
        }
        .cora-badge-soon {
            background-color: #e0e7ff !important;
            color: #4338ca !important;
            border: 1px solid rgba(67, 56, 202, 0.15) !important;
        }
        .cora-badge-locked {
            background-color: #fef3c7 !important;
            color: #b45309 !important;
            border: 1px solid rgba(180, 83, 9, 0.15) !important;
        }

        /* Dynamic buttons inserted by JS */
        .cora-btn-icon-only {
            padding: 0.375rem;
            border-radius: 0.375rem;
            border: 1px solid #e4e4e7;
            color: #71717a;
            background-color: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            cursor: pointer;
        }
        .cora-btn-icon-only:hover {
            color: #09090b;
            background-color: #f4f4f5;
            border-color: #d4d4d8;
        }
        .cora-btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 0.375rem;
            border: 1px solid #d4d4d8;
            color: #27272a;
            background-color: transparent;
            transition: all 0.15s;
            cursor: pointer;
        }
        .cora-btn-action:hover {
            background-color: #fafafa;
        }
        .cora-btn-action:active {
            transform: scale(0.95);
        }
        .cora-delivered-text {
            font-size: 0.75rem;
            color: #047857;
            font-weight: 500;
            margin-right: 0.5rem;
        }

        /* Dynamic chat history styling */
        .chat-bubble {
            max-width: 85% !important;
            border-radius: 0.5rem !important;
            padding: 0.75rem !important;
            font-size: 0.75rem !important;
            line-height: 1.5 !important;
            white-space: pre-line !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }
        .chat-bubble.user {
            background-color: #09090b !important;
            color: #ffffff !important;
            border-bottom-right-radius: 0px !important;
            align-self: flex-end !important;
        }
        .chat-bubble.ai {
            background-color: #f4f4f5 !important;
            color: #18181b !important;
            border-bottom-left-radius: 0px !important;
            align-self: flex-start !important;
            border: 1px solid rgba(228, 228, 231, 0.5) !important;
        }

        /* Spin animation for scanner */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .spin-icon {
            animation: spin 1s linear infinite;
        }

        /* Sidebar collapse transitions and width resets */
        .cora-sidebar {
            transition: width 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        /* Collapsed sidebar classes */
        .cora-sidebar.collapsed-sidebar {
            width: 4rem !important; /* w-16 */
        }
        
        /* Hide text labels when collapsed */
        .cora-sidebar.collapsed-sidebar .cora-studio-info,
        .cora-sidebar.collapsed-sidebar .cora-switcher-arrow,
        .cora-sidebar.collapsed-sidebar .cora-sidebar-search span,
        .cora-sidebar.collapsed-sidebar .cora-sidebar-search .cora-kbd,
        .cora-sidebar.collapsed-sidebar .cora-nav-group-label,
        .cora-sidebar.collapsed-sidebar .cora-nav-text,
        .cora-sidebar.collapsed-sidebar .cora-badge-sidebar,
        .cora-sidebar.collapsed-sidebar .cora-sidebar-footer span,
        .cora-sidebar.collapsed-sidebar .cora-user-info,
        .cora-sidebar.collapsed-sidebar .cora-user-settings-btn {
            display: none !important;
        }

        /* Center icons/items when collapsed */
        .cora-sidebar.collapsed-sidebar .cora-sidebar-header {
            justify-content: center !important;
            padding: 1rem 0.5rem !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-sidebar-search {
            justify-content: center !important;
            margin-left: 0.5rem !important;
            margin-right: 0.5rem !important;
            padding: 0.5rem !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-nav-item {
            justify-content: center !important;
            padding: 0.5rem !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-nav-item-link {
            justify-content: center !important;
            padding: 0.5rem !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-sidebar-footer {
            justify-content: center !important;
            padding: 1rem 0.5rem !important;
        }
        .cora-sidebar.collapsed-sidebar .cora-user-profile {
            justify-content: center !important;
            padding: 1rem 0.5rem !important;
        }

        /* Popover placement when sidebar is collapsed */
        .cora-sidebar.collapsed-sidebar #cora-profile-popover {
            left: 4.5rem !important; /* place it to the right of the collapsed sidebar */
            right: auto !important;
            width: 180px !important;
            bottom: 1rem !important;
        }

        /* High-End Notion Monochrome Dark Theme Overrides */
        #cora-workspace.cora-dark-theme {
            background-color: #0c0c0e !important;
            color: #f4f4f5 !important;
        }
        .cora-dark-theme .cora-sidebar {
            background-color: #121214 !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-sidebar-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        }
        .cora-dark-theme .cora-sidebar-header:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        .cora-dark-theme .cora-sidebar-search {
            background-color: rgba(255, 255, 255, 0.03) !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme .cora-sidebar-search:hover {
            background-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme .cora-nav-item:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-nav-item.cora-active {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-nav-item.cora-active .cora-nav-icon svg {
            stroke: #ffffff !important;
        }
        .cora-dark-theme .cora-badge-sidebar {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #e4e4e7 !important;
        }
        .cora-dark-theme .cora-user-profile {
            border-top-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme .cora-user-profile:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        .cora-dark-theme .cora-main {
            background-color: #0c0c0e !important;
        }
        .cora-dark-theme .cora-topbar {
            background-color: rgba(12, 12, 14, 0.95) !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-breadcrumb-root:hover {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-breadcrumb-current {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-stat-card {
            background-color: #121214 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-stat-label {
            color: #a1a1aa !important;
        }
        .cora-dark-theme .cora-stat-value {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-callout {
            background-color: #121214 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-callout-text {
            color: #e4e4e7 !important;
        }
        .cora-dark-theme .cora-table-header {
            background-color: rgba(255, 255, 255, 0.02) !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
            color: #a1a1aa !important;
        }
        .cora-dark-theme .cora-table-row {
            border-bottom-color: rgba(255, 255, 255, 0.05) !important;
            color: #e4e4e7 !important;
        }
        .cora-dark-theme .cora-table-row:hover {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }
        .cora-dark-theme .cora-card {
            background-color: #121214 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-card-title {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-input,
        .cora-dark-theme #cora-role-preview-select,
        .cora-dark-theme #cora-add-shoot-drawer input,
        .cora-dark-theme #cora-add-shoot-drawer select,
        .cora-dark-theme #cora-team-management-drawer select,
        .cora-dark-theme #cora-ai-sidebar textarea {
            background-color: #18181b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-input:focus,
        .cora-dark-theme #cora-role-preview-select:focus,
        .cora-dark-theme #cora-add-shoot-drawer input:focus,
        .cora-dark-theme #cora-add-shoot-drawer select:focus,
        .cora-dark-theme #cora-team-management-drawer select:focus,
        .cora-dark-theme #cora-ai-sidebar textarea:focus {
            border-color: #ffffff !important;
        }
        .cora-dark-theme #cora-profile-popover {
            background-color: #121214 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5) !important;
        }
        .cora-dark-theme #cora-profile-popover .border-b,
        .cora-dark-theme #cora-profile-popover .border-t {
            border-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme #cora-profile-popover select {
            background-color: #18181b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
        .cora-dark-theme #cora-profile-popover button,
        .cora-dark-theme #cora-profile-popover a {
            color: #a1a1aa !important;
        }
        .cora-dark-theme #cora-profile-popover button:hover,
        .cora-dark-theme #cora-profile-popover a:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        .cora-dark-theme #cora-profile-popover .bg-\[\#fafaf9\] {
            background-color: #18181b !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme #cora-profile-popover .text-zinc-900 {
            color: #ffffff !important;
        }
        .cora-dark-theme #cora-profile-popover .text-zinc-400 {
            color: #71717a !important;
        }
        .cora-dark-theme #cora-add-shoot-drawer,
        .cora-dark-theme #cora-ai-sidebar {
            background-color: #121214 !important;
            border-left-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .chat-bubble.ai {
            background-color: rgba(255, 255, 255, 0.04) !important;
            color: #f4f4f5 !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
        }
        .cora-dark-theme .cora-status-text {
            color: #a1a1aa !important;
        }
        .cora-dark-theme .cora-badge {
            background-color: rgba(255, 255, 255, 0.06) !important;
            color: #e4e4e7 !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        .cora-dark-theme .cora-badge-soon {
            background-color: rgba(99, 102, 241, 0.15) !important;
            color: #a5b4fc !important;
            border-color: rgba(99, 102, 241, 0.25) !important;
        }
        .cora-dark-theme .cora-badge-locked {
            background-color: rgba(245, 158, 11, 0.15) !important;
            color: #fcd34d !important;
            border-color: rgba(245, 158, 11, 0.25) !important;
        }
        .cora-dark-theme select option {
            background-color: #121214 !important;
            color: #ffffff !important;
        }
        
        /* Feature Hub styling */
        .cora-feature-card {
            transition: all 0.2s ease-in-out !important;
        }
        .cora-feature-card:hover {
            transform: translateY(-2px) !important;
        }
        .cora-dark-theme .cora-feature-card {
            background-color: #121214 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-feature-card:hover {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
        .cora-dark-theme .cora-feature-card h3 {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-feature-card p {
            color: #a1a1aa !important;
        }
        .cora-dark-theme .cora-feature-card svg {
            stroke: #a1a1aa !important;
        }
        .cora-dark-theme .cora-feature-card div.border-t {
            border-top-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-feature-card .text-zinc-500 {
            color: #a1a1aa !important;
        }
        .cora-dark-theme .cora-feature-card:hover .text-zinc-500 {
            color: #ffffff !important;
        }
        .cora-dark-theme #cora-card-manage-team {
            background-color: #18181b !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }
        .cora-dark-theme #cora-card-manage-team:hover {
            border-color: #ffffff !important;
        }
        .cora-dark-theme #cora-card-manage-team svg {
            stroke: #34d399 !important;
        }
        .cora-dark-theme #cora-team-management-drawer {
            background-color: #121214 !important;
            border-left-color: rgba(255, 255, 255, 0.08) !important;
        }
        
        /* Drawer and Form Overrides */
        .cora-drawer-footer {
            background-color: #f9f9f9;
            border-top: 1px solid #e5e7eb;
        }
        .cora-dark-theme #cora-team-management-drawer,
        .cora-dark-theme #cora-add-shoot-drawer,
        .cora-dark-theme #cora-ai-sidebar {
            background-color: #121214 !important;
            border-left-color: rgba(255, 255, 255, 0.08) !important;
            color: #f4f4f5 !important;
        }
        .cora-dark-theme .cora-ai-sidebar-header {
            background-color: #18181b !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme .cora-ai-sidebar-header span {
            color: #ffffff !important;
        }
        .cora-dark-theme .cora-drawer-footer {
            background-color: #18181b !important;
            border-top-color: rgba(255, 255, 255, 0.08) !important;
        }
        .cora-dark-theme #cora-team-management-drawer label,
        .cora-dark-theme #cora-add-shoot-drawer label {
            color: #a1a1aa !important;
        }
        .cora-dark-theme #cora-team-management-drawer .text-zinc-800,
        .cora-dark-theme #cora-add-shoot-drawer .text-zinc-800 {
            color: #ffffff !important;
        }
        .cora-dark-theme #cora-team-management-drawer .text-zinc-500,
        .cora-dark-theme #cora-add-shoot-drawer .text-zinc-500 {
            color: #a1a1aa !important;
        }
        .cora-dark-theme #cora-team-management-drawer button.bg-white,
        .cora-dark-theme #cora-add-shoot-drawer button.bg-white {
            background-color: #18181b !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }
        .cora-dark-theme #cora-team-management-drawer button.bg-white:hover,
        .cora-dark-theme #cora-add-shoot-drawer button.bg-white:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        .cora-dark-theme .cora-badge-green {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
            border-color: rgba(16, 185, 129, 0.25) !important;
        }

        /* Google Docs A4 Emulation styles */
        #cora-paper-container {
            font-family: Arial, Helvetica, sans-serif !important;
            transition: all 0.2s ease;
        }
        #cora-doc-paper * {
            font-family: inherit !important;
        }
        #cora-doc-paper:focus {
            outline: none;
        }
        #cora-doc-paper h1 {
            font-size: 2.25rem !important;
            font-weight: 800 !important;
            margin-top: 1.5rem !important;
            margin-bottom: 1rem !important;
            line-height: 1.2 !important;
        }
        #cora-doc-paper h2 {
            font-size: 1.75rem !important;
            font-weight: 700 !important;
            margin-top: 1.5rem !important;
            margin-bottom: 0.75rem !important;
            line-height: 1.3 !important;
        }
        #cora-doc-paper h3 {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            margin-top: 1.25rem !important;
            margin-bottom: 0.5rem !important;
            line-height: 1.4 !important;
        }
        #cora-doc-paper p {
            margin-bottom: 1rem !important;
            line-height: 1.6 !important;
        }
        #cora-doc-paper ul {
            list-style-type: disc !important;
            padding-left: 1.5rem !important;
            margin-bottom: 1rem !important;
        }
        #cora-doc-paper ol {
            list-style-type: decimal !important;
            padding-left: 1.5rem !important;
            margin-bottom: 1rem !important;
        }
        #cora-doc-paper li {
            margin-bottom: 0.25rem !important;
        }
        #cora-doc-paper blockquote {
            border-left: 4px solid #e4e4e7 !important;
            padding-left: 1rem !important;
            color: #71717a !important;
            font-style: italic !important;
            margin-bottom: 1rem !important;
        }
        
        #cora-doc-paper[placeholder]:empty::before {
            content: attr(placeholder);
            color: #a1a1aa;
            font-style: italic;
        }

        /* Shopify-style mobile navigation and table refinements */
        @media (max-width: 767px) {
            .cora-content-wrapper {
                padding: 1rem !important;
            }
            .cora-topbar {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            #cora-vault-stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem !important;
            }
        }

        /* Print isolation mode */
        body.cora-printing-mode {
            background: white !important;
            color: black !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        body.cora-printing-mode #cora-workspace {
            display: none !important;
        }
        body.cora-printing-mode #cora-print-paper-container {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            border: none !important;
            box-shadow: none !important;
            padding: 20mm !important;
            margin: 0 !important;
        }
        #cora-paper-header-preview {
            cursor: pointer;
            transition: opacity 0.2s ease;
        }
        #cora-paper-header-preview:hover {
            opacity: 0.8;
        }
        #cora-paper-footer-preview[placeholder]:empty::before {
            content: attr(placeholder);
            color: #a1a1aa;
            font-style: italic;
        }
    </style>
    
    <!-- Load WordPress core jQuery -->
    <script src="<?php echo esc_url( includes_url( 'js/jquery/jquery.min.js' ) ); ?>"></script>
    
    <!-- Pass WordPress environment variables to JavaScript -->
    <script>
        var coraData = {
            ajaxUrl: "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>",
            siteUrl: "<?php echo esc_url( get_site_url() ); ?>",
            restUrl: "<?php echo esc_url( rest_url() ); ?>",
            nonce: "<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>",
            ajaxNonce: "<?php echo esc_js( wp_create_nonce( 'cora_ajax_nonce' ) ); ?>",
            currentRole: "<?php echo esc_js( $current_user_role ); ?>",
            userPermissions: <?php echo json_encode( $cora_permissions ); ?>,
            currentPage: "<?php echo esc_js( $sub_page ); ?>",
            documents: <?php echo json_encode( $cora_documents ); ?>
        };
    </script>
</head>
<body class="bg-white text-zinc-900 antialiased overflow-x-hidden">
<div id="cora-workspace" class="flex min-h-screen bg-[#f7f7f5] text-zinc-900">
    
    <!-- Workspace Sidebar -->
    <aside class="cora-sidebar w-64 bg-[#f7f7f5] border-r border-zinc-200/80 flex flex-col justify-between shrink-0 hidden lg:flex h-screen sticky top-0">
        <!-- UPPER BLOCK: SCROLLABLE NAVIGATION CONTENT -->
        <div class="flex-1 flex flex-col min-h-0 overflow-y-auto">
            <!-- Sidebar Header / Studio Switcher -->
            <div class="cora-sidebar-header flex items-center justify-between p-4 border-b border-zinc-200/50 hover:bg-zinc-200/30 cursor-pointer transition-colors duration-200">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="cora-studio-avatar w-7 h-7 rounded bg-zinc-950 text-white flex items-center justify-center shrink-0">
                        <!-- Professional Local Business AI network hub icon -->
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5" stroke-width="1.8"></polygon>
                            <path d="M12 7v10M8 12h8" opacity="0.5"></path>
                            <circle cx="12" cy="12" r="3.5" stroke-width="1.5"></circle>
                            <circle cx="12" cy="7" r="1" fill="currentColor"></circle>
                            <circle cx="12" cy="17" r="1" fill="currentColor"></circle>
                            <circle cx="8" cy="12" r="1" fill="currentColor"></circle>
                            <circle cx="16" cy="12" r="1" fill="currentColor"></circle>
                        </svg>
                    </div>
                    <div class="cora-studio-info flex flex-col min-w-0">
                        <span class="cora-studio-name text-sm font-semibold tracking-tight text-zinc-900 leading-tight truncate">Cora Studio AI</span>
                        <span class="cora-studio-plan text-[11px] text-zinc-500 font-medium truncate font-medium">Delhi Studio v2</span>
                    </div>
                </div>
                <div class="cora-switcher-arrow text-zinc-400 shrink-0">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="cora-sidebar-search mx-3 mt-4 mb-2 p-2 bg-zinc-200/40 rounded-md border border-zinc-200/55 flex items-center justify-between text-zinc-450 hover:bg-zinc-200/60 hover:text-zinc-500 cursor-pointer transition-all duration-150 text-[11px] font-medium shrink-0" title="Search / Ask AI (Press ⌘K)">
                <div class="flex items-center gap-2 text-zinc-400">
                    <svg class="cora-search-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span>Search / Ask AI...</span>
                </div>
                <kbd class="cora-kbd text-[9px] font-mono bg-zinc-200/80 px-1 py-0.5 rounded text-zinc-500 border border-zinc-300/30">⌘K</kbd>
            </div>

            <!-- Navigation Menu -->
            <nav class="cora-sidebar-nav px-2 py-4 space-y-6">
                <div>
                    <div class="cora-nav-group-label px-3 text-[10px] font-bold tracking-wider text-zinc-400 uppercase">Workspace</div>
                    <ul class="cora-nav-list space-y-0.5 mt-2">
                        <li class="cora-nav-item <?php echo $sub_page === 'dashboard' ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm text-zinc-650 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="dashboard" title="Dashboard (Press 1)">
                            <div class="flex items-center gap-3">
                                <span class="cora-nav-icon text-zinc-400">
                                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="9" rx="1"></rect>
                                        <rect x="14" y="3" width="7" height="5" rx="1"></rect>
                                        <rect x="14" y="12" width="7" height="9" rx="1"></rect>
                                        <rect x="3" y="16" width="7" height="5" rx="1"></rect>
                                    </svg>
                                </span>
                                <span class="cora-nav-text">Dashboard</span>
                            </div>
                        </li>
                        <li class="cora-nav-item <?php echo $sub_page === 'bookings' ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm text-zinc-650 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="bookings" title="Shoot Bookings (Press 2)">
                            <div class="flex items-center gap-3">
                                <span class="cora-nav-icon text-zinc-400">
                                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </span>
                                <span class="cora-nav-text">Shoot Bookings</span>
                            </div>
                            <span class="cora-badge cora-badge-sidebar px-1.5 py-0.5 text-[10px] font-medium bg-zinc-200 text-zinc-800 rounded-full">3</span>
                        </li>
                        <li class="cora-nav-item <?php echo $sub_page === 'feature-hub' ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm text-zinc-655 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="feature-hub" title="Feature Hub (Press 6)">
                            <div class="flex items-center gap-3">
                                <span class="cora-nav-icon text-zinc-400">
                                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                </span>
                                <span class="cora-nav-text">Feature Hub</span>
                            </div>
                        </li>
                        <li class="cora-nav-item <?php echo $sub_page === 'team-roles' ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm text-zinc-650 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="team-roles" title="Team & Roles (Press 7)">
                            <div class="flex items-center gap-3">
                                <span class="cora-nav-icon text-zinc-400">
                                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                </span>
                                <span class="cora-nav-text">Team & Roles</span>
                            </div>
                        </li>
                        <li class="cora-nav-item <?php echo $sub_page === 'equipment' ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm text-zinc-655 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="equipment" title="Equipment Tracking (Press 8)">
                            <div class="flex items-center gap-3">
                                <span class="cora-nav-icon text-zinc-400">
                                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                        <circle cx="12" cy="13" r="4"></circle>
                                    </svg>
                                </span>
                                <span class="cora-nav-text">Equipment</span>
                            </div>
                        </li>
                        <li class="cora-nav-item <?php echo $sub_page === 'vault' ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm text-zinc-655 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="vault" title="Studio Vault (Press 9)">
                            <div class="flex items-center gap-3">
                                <span class="cora-nav-icon text-zinc-400">
                                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <span class="cora-nav-text">Studio Vault</span>
                            </div>
                        </li>
                        <li class="cora-nav-item cora-nav-soon flex items-center justify-between px-3 py-2 text-sm text-zinc-655 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="ai-assistants" title="AI Assistants (Soon - Press 3)">
                             <div class="flex items-center gap-3">
                                 <span class="cora-nav-icon text-zinc-400">
                                     <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                         <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                     </svg>
                                 </span>
                                 <span class="cora-nav-text">AI Assistants</span>
                             </div>
                             <span class="cora-badge cora-badge-soon cora-badge-sidebar px-1.5 py-0.5 text-[9px] font-bold rounded-md select-none">Soon</span>
                         </li>
                         <li class="cora-nav-item cora-nav-locked flex items-center justify-between px-3 py-2 text-sm text-zinc-650 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="gallery-seo" title="Gallery SEO Tags (Locked - Press 4)">
                             <div class="flex items-center gap-3">
                                 <span class="cora-nav-icon text-zinc-400">
                                     <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                         <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                         <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                         <polyline points="21 15 16 10 5 21"></polyline>
                                     </svg>
                                 </span>
                                 <span class="cora-nav-text">Gallery SEO Tags</span>
                             </div>
                             <span class="cora-badge cora-badge-locked cora-badge-sidebar px-1.5 py-0.5 text-[9px] font-bold rounded-md select-none flex items-center gap-0.5">
                                 <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="3" fill="none" class="inline"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                 Locked
                             </span>
                         </li>
                        <li class="cora-nav-item <?php echo $sub_page === 'settings' ? 'cora-active' : ''; ?> flex items-center justify-between px-3 py-2 text-sm text-zinc-650 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150" data-target="settings" title="Settings (Press 5)">
                            <div class="flex items-center gap-3">
                                <span class="cora-nav-icon text-zinc-400">
                                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                    </svg>
                                </span>
                                <span class="cora-nav-text">Settings</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div>
                    <div class="cora-nav-group-label px-3 text-[10px] font-bold tracking-wider text-zinc-400 uppercase">Quick Links</div>
                    <ul class="cora-nav-list space-y-0.5 mt-2">
                        <li>
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="cora-nav-item-link flex items-center gap-3 px-3 py-2 text-sm text-zinc-650 rounded-md cursor-pointer hover:bg-zinc-200/40 hover:text-zinc-900 transition-all duration-150">
                                <span class="cora-nav-icon text-zinc-400">
                                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                    </svg>
                                </span>
                                <span class="cora-nav-text">View Live Site</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- LOWER BLOCK: STICKY AT BOTTOM -->
        <div class="shrink-0 border-t border-zinc-200/60 bg-[#f7f7f5] sticky bottom-0 z-20 relative">
                       <!-- User Profile Popover Card -->
            <div id="cora-profile-popover" class="hidden absolute bottom-20 left-4 right-4 bg-white border border-zinc-200 rounded-2xl shadow-xl p-4 z-30 flex flex-col gap-2.5 animate-in fade-in slide-in-from-bottom-2 duration-150">
                <!-- UID Display -->
                <div class="px-1 flex flex-col select-none">
                    <span class="text-[9px] text-zinc-400 font-semibold tracking-wide">UID : <?php echo esc_html( $current_wp_user->ID ); ?></span>
                </div>
                               <!-- Upgrade Container block -->
                <div class="bg-[#fafaf9] border border-zinc-200/50 rounded-xl p-3 flex justify-between items-center select-none">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-zinc-900"><?php echo esc_html( $current_user_role_label ); ?></span>
                    </div>
                    <button class="bg-[#18181b] hover:bg-zinc-800 text-white font-bold text-[10px] px-3 py-1.5 rounded-lg transition-colors cursor-pointer active:scale-95 shadow-sm" onclick="window.coraShowToast('Upgrade flow is loading... Upgrade to Premium to unlock full AI capabilities!')">
                        Upgrade
                    </button>
                </div>

                <!-- AI Model selection dropdown -->
                <div class="px-1 py-1 flex flex-col gap-1.5 border-b border-zinc-100 pb-3 select-none">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">AI Model</span>
                        <span class="text-[9px] font-bold text-amber-600 flex items-center gap-0.5">
                            <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="3" fill="none" class="inline"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            PREMIUM
                        </span>
                    </div>
                    <div class="relative">
                        <select id="cora-ai-model-selector" class="w-full bg-zinc-50 border border-zinc-200/80 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-zinc-850 focus:border-zinc-400 outline-none transition-all cursor-pointer appearance-none pr-8">
                            <option value="cora-core-v2">Cora Core v2 (Active)</option>
                            <option value="claude-3-5">Claude 3.5 Sonnet (Locked)</option>
                            <option value="gpt-4o">GPT-4o mini (Coming Soon)</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-zinc-450">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Menu Items List -->
                <div class="flex flex-col gap-1">
                    <button class="w-full text-left px-2 py-2 text-xs text-zinc-700 rounded-lg hover:bg-zinc-50 hover:text-zinc-955 font-medium flex items-center gap-3 cursor-pointer transition-colors" onclick="coraNavigateTo('settings'); $('#cora-profile-popover').addClass('hidden');">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 shrink-0">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1-2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        Account Settings
                    </button>

                    <!-- Locked Dark Theme toggling -->
                    <div id="cora-theme-toggle-btn" class="w-full text-left px-2 py-2 text-xs text-zinc-400 rounded-lg flex items-center justify-between cursor-pointer hover:bg-zinc-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                            <span>Dark Theme</span>
                        </div>
                        <span class="px-1.5 py-0.5 text-[8px] font-bold bg-indigo-100 text-indigo-700 rounded-md border border-indigo-200/50 select-none">SOON</span>
                    </div>
                    
                    <div class="border-t border-zinc-100 my-1"></div>

                    <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="w-full text-left px-2 py-2 text-xs text-red-655 rounded-lg hover:bg-red-50 hover:text-red-700 font-semibold flex items-center gap-3 transition-colors">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-red-500 shrink-0"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Sign Out
                    </a>
                </div>
            </div>

            <!-- Sidebar Footer / Status & Collapse Toggle -->
            <div class="cora-sidebar-footer p-4 flex items-center justify-between text-xs text-zinc-500 font-semibold select-none">
                <div class="flex items-center gap-2.5">
                    <div class="cora-status-indicator w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)] shrink-0"></div>
                    <span class="cora-status-text font-semibold text-zinc-550">AI Connected</span>
                </div>
                <button id="cora-sidebar-toggle" class="text-zinc-400 hover:text-zinc-900 p-1 rounded hover:bg-zinc-200/65 cursor-pointer transition-colors duration-200 shrink-0" title="Collapse Sidebar (Press ⌘\)">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" id="cora-toggle-icon">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
            </div>

            <!-- User Profile Widget -->
            <?php
            $current_user_display_name = $current_wp_user->exists() ? $current_wp_user->display_name : 'Dravya Bansal';
            $current_user_role_label = isset($cora_role_labels[$current_user_role]) ? $cora_role_labels[$current_user_role] : ucfirst($current_user_role);
            if ($current_user_role === 'administrator') {
                $current_user_role_label = 'Super Admin';
            }
            $current_user_avatar = $current_wp_user->exists() ? get_user_meta( $current_wp_user->ID, 'cora_avatar_url', true ) : '';
            ?>
            <div class="cora-user-profile p-4 border-t border-zinc-200/50 flex items-center justify-between hover:bg-zinc-200/40 transition-colors duration-200 select-none cursor-pointer" onclick="$('#cora-profile-popover').toggleClass('hidden');">
                <div class="flex items-center gap-3 min-w-0">
                    <?php if ( $current_user_avatar ) : ?>
                        <img src="<?php echo esc_url($current_user_avatar); ?>" class="w-8 h-8 rounded-full object-cover shrink-0 select-none border border-zinc-200/60" alt="<?php echo esc_attr($current_user_display_name); ?>" />
                    <?php else : ?>
                        <div class="w-8 h-8 rounded-full bg-zinc-200 text-zinc-700 flex items-center justify-center font-bold text-xs uppercase shrink-0 select-none">
                            <?php echo esc_html(substr($current_user_display_name, 0, 2)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="cora-user-info flex flex-col min-w-0">
                        <span class="cora-user-name text-xs font-semibold text-zinc-900 truncate leading-tight"><?php echo esc_html($current_user_display_name); ?></span>
                        <span class="cora-user-role text-[10px] text-zinc-400 font-medium truncate"><?php echo esc_html($current_user_role_label); ?></span>
                    </div>
                </div>
                <button class="cora-user-settings-btn text-zinc-450 hover:text-zinc-900 transition-colors shrink-0 cursor-pointer p-1 rounded hover:bg-zinc-200/60" title="User Profile Menu">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1-2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content Pane -->
    <main class="cora-main flex-1 bg-white flex flex-col min-h-screen relative pb-16 lg:pb-0">
        <!-- Topbar -->
        <header class="cora-topbar h-14 border-b border-zinc-200/80 px-6 md:px-8 flex items-center justify-between sticky top-0 bg-white/95 backdrop-blur-sm z-30 shrink-0">
            <div class="cora-breadcrumbs flex items-center gap-1.5 text-xs text-zinc-500 font-medium">
                <span class="cora-breadcrumb-root text-zinc-400 hover:text-zinc-655 cursor-pointer transition-colors">Cora Studio AI</span>
                <span class="cora-breadcrumb-divider text-zinc-300 font-mono text-[10px]">/</span>
                <span id="cora-current-breadcrumb" class="cora-breadcrumb-current font-bold text-zinc-900"><?php 
                    $page_title_map = array(
                        'dashboard' => 'Dashboard',
                        'bookings' => 'Shoot Bookings',
                        'feature-hub' => 'Feature Hub',
                        'team-roles' => 'Team & Roles',
                        'equipment' => 'Equipment Tracking',
                        'settings' => 'Settings',
                        'ai-assistants' => 'AI Assistants',
                        'gallery-seo' => 'Gallery SEO Tags'
                    );
                    echo isset($page_title_map[$sub_page]) ? esc_html($page_title_map[$sub_page]) : esc_html(ucfirst($sub_page));
                ?></span>
            </div>
            <div class="cora-topbar-actions flex items-center gap-2">
                <?php if ( current_user_can( 'manage_options' ) ) : ?>
                <div class="flex items-center gap-1.5 text-xs font-semibold mr-1">
                    <span class="text-zinc-400 select-none">Preview Role:</span>
                    <select id="cora-role-preview-select" class="border border-zinc-200 rounded p-1 text-[11px] bg-white text-zinc-700 font-bold focus:border-zinc-400 focus:outline-none cursor-pointer">
                        <option value="administrator">Super Admin</option>
                        <option value="cora_manager">Manager</option>
                        <option value="cora_photographer">Photographer</option>
                        <option value="cora_videographer">Videographer</option>
                        <option value="cora_drone_pilot">Drone Pilot</option>
                        <option value="cora_editor">Editor</option>
                    </select>
                </div>
                <?php endif; ?>
                <button id="cora-quick-ai-btn" class="cora-btn-secondary px-3 py-1.5 text-xs font-bold border border-zinc-200 rounded-md hover:bg-zinc-50 hover:text-zinc-900 transition-all active:scale-[0.98] inline-flex items-center gap-1.5 text-zinc-700 bg-white shadow-sm cursor-pointer" title="Ask Cora AI (Press ⌘J)">
                    <span class="cora-btn-icon text-zinc-550 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                        </svg>
                    </span> Ask Cora AI
                </button>
            </div>
        </header>

        <!-- Dynamic Content Sections -->
        <div class="cora-content-wrapper p-6 md:p-8 max-w-full w-full flex-1 space-y-6">
            
            <!-- SECTION 1: DASHBOARD -->
            <?php if ( $sub_page === 'dashboard' ) : ?>
            <section id="cora-page-dashboard" class="cora-page-section cora-active space-y-6">
                <div class="cora-page-header flex items-center gap-3">
                    <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="9" rx="1"></rect>
                            <rect x="14" y="3" width="7" height="5" rx="1"></rect>
                            <rect x="14" y="12" width="7" height="9" rx="1"></rect>
                            <rect x="3" y="16" width="7" height="5" rx="1"></rect>
                        </svg>
                    </span>
                    <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Studio Workspace</h1>
                </div>

                <!-- Callout Box -->
                <div class="cora-callout bg-zinc-50 border border-zinc-200/80 rounded-lg p-4 flex gap-3 text-sm text-zinc-650 leading-relaxed shadow-sm">
                    <div class="cora-callout-emoji text-zinc-400 shrink-0 mt-0.5 flex">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                        </svg>
                    </div>
                    <div class="cora-callout-text">
                        <strong>Overview Alert:</strong> Cora AI has verified your records. You have <strong>3 upcoming shoots</strong> scheduled this week. We recommend generating caption packages and media keywords for the <em>Jaipur Destination Wedding</em> shoot completed yesterday.
                    </div>
                </div>

                <!-- Quick Stats Grid -->
                <div class="cora-stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-lg p-4 shadow-sm hover:shadow transition-shadow duration-200 flex flex-col justify-between min-h-[105px]">
                        <span class="cora-stat-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Bookings This Month</span>
                        <span class="cora-stat-value text-2xl font-semibold text-zinc-900 mt-1">24</span>
                        <span class="cora-stat-change positive text-xs mt-2 font-bold text-emerald-600">↑ 12% increase</span>
                    </div>
                    <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-lg p-4 shadow-sm hover:shadow transition-shadow duration-200 flex flex-col justify-between min-h-[105px]">
                        <span class="cora-stat-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Pending Deliveries</span>
                        <span class="cora-stat-value text-2xl font-semibold text-zinc-900 mt-1">5</span>
                        <span class="cora-stat-change warning text-xs mt-2 font-bold text-amber-600">3 in editing</span>
                    </div>
                    <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-lg p-4 shadow-sm hover:shadow transition-shadow duration-200 flex flex-col justify-between min-h-[105px]">
                        <span class="cora-stat-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">AI Captions Drafted</span>
                        <span class="cora-stat-value text-2xl font-semibold text-zinc-900 mt-1">8</span>
                        <span class="cora-stat-change positive text-xs mt-2 font-bold text-emerald-600 font-medium">Ready to publish</span>
                    </div>
                    <div id="cora-dashboard-financial-card" class="cora-stat-card bg-white border border-zinc-200/80 rounded-lg p-4 shadow-sm hover:shadow transition-shadow duration-200 flex flex-col justify-between min-h-[105px]">
                        <span class="cora-stat-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Revenue Estimate</span>
                        <span class="cora-stat-value text-2xl font-semibold text-zinc-900 mt-1">₹2,45,000</span>
                        <span class="cora-stat-change positive text-xs mt-2 font-bold text-zinc-500 font-medium">INR • Invoiced</span>
                    </div>
                </div>

                <!-- Features Preview / Quick Action Blocks -->
                <div class="cora-grid-two-col grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                        <div>
                            <h3 class="cora-card-title text-base font-semibold text-zinc-950 flex items-center">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-zinc-500 shrink-0">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                Active AI Assistants
                            </h3>
                            <p class="cora-card-description text-xs text-zinc-500 mt-1">Cora is monitoring your studio workflows. Click to run standard mock automation routines.</p>
                        </div>
                        
                        <div class="cora-action-list space-y-2">
                            <div class="cora-action-item flex items-center justify-between p-3 border border-zinc-200/80 rounded-lg hover:bg-zinc-50/50 hover:border-zinc-300 cursor-pointer transition-all duration-150 group" onclick="coraNavigateTo('ai-assistants')">
                                <div class="cora-action-info flex flex-col">
                                    <span class="cora-action-title text-sm font-semibold text-zinc-900 leading-snug group-hover:text-black">Instagram Caption Assistant</span>
                                    <span class="cora-action-desc text-[11px] text-zinc-500 block mt-0.5">Draft aesthetic captions matching your photos' mood.</span>
                                </div>
                                <span class="cora-action-arrow text-zinc-400 font-mono text-sm group-hover:text-zinc-900 group-hover:translate-x-0.5 transition-transform">→</span>
                            </div>
                            <div class="cora-action-item flex items-center justify-between p-3 border border-zinc-200/80 rounded-lg hover:bg-zinc-50/50 hover:border-zinc-300 cursor-pointer transition-all duration-150 group" onclick="coraNavigateTo('gallery-seo')">
                                <div class="cora-action-info flex flex-col">
                                    <span class="cora-action-title text-sm font-semibold text-zinc-900 leading-snug group-hover:text-black">AI Media Tagging & Alt Text</span>
                                    <span class="cora-action-desc text-[11px] text-zinc-500 block mt-0.5">Optimize uploaded photos for Google search engine rankings.</span>
                                </div>
                                <span class="cora-action-arrow text-zinc-400 font-mono text-sm group-hover:text-zinc-900 group-hover:translate-x-0.5 transition-transform">→</span>
                            </div>
                        </div>
                    </div>

                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                        <div>
                            <h3 class="cora-card-title text-base font-semibold text-zinc-950 flex items-center">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-zinc-500 shrink-0">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                Quick Shoot Status
                            </h3>
                            <p class="cora-card-description text-xs text-zinc-500 mt-1">Your nearest photography bookings. Track schedules and edit workflows.</p>
                        </div>
                        <div class="cora-mini-table border border-zinc-200/80 rounded-lg overflow-hidden divide-y divide-zinc-200/50 bg-white">
                            <div class="cora-mini-table-row flex items-center justify-between p-3 hover:bg-zinc-50 transition-colors text-xs gap-2">
                                <div class="cora-mini-table-cell main-cell flex-1 min-w-0">
                                    <strong class="font-semibold text-zinc-900 text-sm block truncate">Ananya Sharma</strong>
                                    <span class="text-[10px] text-zinc-500 block truncate">Maternity Portrait • Outdoor</span>
                                </div>
                                <div class="cora-mini-table-cell text-zinc-500 shrink-0 font-medium text-right font-medium">24th Jun</div>
                                <div class="cora-mini-table-cell text-right shrink-0">
                                    <span class="cora-badge cora-badge-blue">Confirmed</span>
                                </div>
                            </div>
                            <div class="cora-mini-table-row flex items-center justify-between p-3 hover:bg-zinc-50 transition-colors text-xs gap-2">
                                <div class="cora-mini-table-cell main-cell flex-1 min-w-0">
                                    <strong class="font-semibold text-zinc-900 text-sm block truncate">Rohit & Sneha</strong>
                                    <span class="text-[10px] text-zinc-500 block truncate">Jaipur Wedding • Pre-shoot</span>
                                </div>
                                <div class="cora-mini-table-cell text-zinc-500 shrink-0 font-medium text-right font-medium">Yesterday</div>
                                <div class="cora-mini-table-cell text-right shrink-0">
                                    <span class="cora-badge cora-badge-yellow">Editing</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Premium AI Modules Manager -->
                <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                    <div>
                        <h3 class="cora-card-title text-base font-semibold text-zinc-950 flex items-center">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-zinc-500 shrink-0">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            Premium AI Modules
                        </h3>
                        <p class="cora-card-description text-xs text-zinc-500 mt-1">Enable or disable specialized AI agents and automation modules built for photography businesses.</p>
                    </div>
                    
                    <div class="cora-modules-grid grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                        <div class="cora-module-item border border-zinc-200/80 rounded-lg p-4 flex flex-col justify-between bg-zinc-50/20 hover:border-zinc-300 transition-colors">
                            <div class="cora-module-details flex flex-col items-start gap-1">
                                <span class="cora-module-name font-semibold text-sm text-zinc-900">WhatsApp Autopilot</span>
                                <span class="cora-module-status-pill text-[9px] font-bold uppercase inline-flex items-center px-1.5 py-0.5 rounded cora-module-status-pill active" id="badge-module-whatsapp">Active</span>
                                <span class="cora-module-desc text-xs text-zinc-500 mt-1 leading-relaxed">Automatically sends confirmations, preview selections, and review requests via WhatsApp.</span>
                            </div>
                            <label class="cora-switch relative inline-flex items-center cursor-pointer mt-4 self-start">
                                <input type="checkbox" id="module-whatsapp" checked onchange="coraToggleModule('whatsapp', this.checked)" class="sr-only peer">
                                <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950"></div>
                            </label>
                        </div>
                        
                        <div class="cora-module-item border border-zinc-200/80 rounded-lg p-4 flex flex-col justify-between bg-zinc-50/20 hover:border-zinc-300 transition-colors">
                            <div class="cora-module-details flex flex-col items-start gap-1">
                                <span class="cora-module-name font-semibold text-sm text-zinc-900">Local SEO Rank Crawler</span>
                                <span class="cora-module-status-pill text-[9px] font-bold uppercase inline-flex items-center px-1.5 py-0.5 rounded cora-module-status-pill active" id="badge-module-seo">Active</span>
                                <span class="cora-module-desc text-xs text-zinc-500 mt-1 leading-relaxed">Monitors local search ranking and auto-injects SEO keywords to WordPress media attachment meta.</span>
                            </div>
                            <label class="cora-switch relative inline-flex items-center cursor-pointer mt-4 self-start">
                                <input type="checkbox" id="module-seo" checked onchange="coraToggleModule('seo', this.checked)" class="sr-only peer">
                                <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950"></div>
                            </label>
                        </div>

                        <div class="cora-module-item border border-zinc-200/80 rounded-lg p-4 flex flex-col justify-between bg-zinc-50/20 hover:border-zinc-300 transition-colors">
                            <div class="cora-module-details flex flex-col items-start gap-1">
                                <span class="cora-module-name font-semibold text-sm text-zinc-900">Smart Contract Generator</span>
                                <span class="cora-module-status-pill text-[9px] font-bold uppercase inline-flex items-center px-1.5 py-0.5 rounded cora-module-status-pill inactive" id="badge-module-contracts">Inactive</span>
                                <span class="cora-module-desc text-xs text-zinc-500 mt-1 leading-relaxed">Generates legal photography shoot terms and cancellation policies dynamically.</span>
                            </div>
                            <label class="cora-switch relative inline-flex items-center cursor-pointer mt-4 self-start">
                                <input type="checkbox" id="module-contracts" onchange="coraToggleModule('contracts', this.checked)" class="sr-only peer">
                                <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- SECTION 2: BOOKINGS CRM -->
            <?php if ( $sub_page === 'bookings' ) : ?>
            <section id="cora-page-bookings" class="cora-page-section cora-active space-y-6">
                <div class="cora-page-header flex items-center gap-3">
                    <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                    <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Shoot Bookings CRM</h1>
                </div>
                <p class="cora-section-desc text-sm text-zinc-500 -mt-2">Manage clients, view shoot schedules, track editing states, and auto-deliver previews using AI agents.</p>

                <!-- Filter Pills -->
                <div class="cora-filters-bar flex items-center flex-wrap gap-2 border-b border-zinc-200 pb-3">
                    <button class="cora-filter-tab active px-3 py-1.5 text-xs font-semibold rounded-md border border-transparent text-zinc-650 hover:text-zinc-900 hover:bg-zinc-100 transition-all cursor-pointer" data-filter="all">All Shoots</button>
                    <button class="cora-filter-tab px-3 py-1.5 text-xs font-semibold rounded-md border border-transparent text-zinc-650 hover:text-zinc-900 hover:bg-zinc-100 transition-all cursor-pointer" data-filter="confirmed">Confirmed</button>
                    <button class="cora-filter-tab px-3 py-1.5 text-xs font-semibold rounded-md border border-transparent text-zinc-650 hover:text-zinc-900 hover:bg-zinc-100 transition-all cursor-pointer" data-filter="editing">Editing</button>
                    <button class="cora-filter-tab px-3 py-1.5 text-xs font-semibold rounded-md border border-transparent text-zinc-650 hover:text-zinc-900 hover:bg-zinc-100 transition-all cursor-pointer" data-filter="completed">Completed</button>
                    <button id="cora-add-booking-btn" class="cora-btn-primary px-3 py-1.5 text-xs font-semibold bg-zinc-950 text-white rounded hover:bg-zinc-800 transition-all active:scale-[0.97] ml-auto cursor-pointer">
                        + New Shoot
                    </button>
                </div>

                <!-- Bookings Table -->
                <div class="cora-table-wrapper border border-zinc-200/80 rounded-xl overflow-x-auto shadow-sm bg-white">
                    <table class="cora-table min-w-full divide-y divide-zinc-200" id="cora-bookings-table">
                        <thead class="bg-zinc-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Client Name</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Shoot Type</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Location / Studio</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Shoot Date</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider cora-financial-col">Package Value</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200/80 bg-white">
                            <!-- Item 1 -->
                            <tr data-status="confirmed" class="hover:bg-zinc-50/30 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="cora-client-meta flex flex-col">
                                        <span class="cora-client-name font-semibold text-sm text-zinc-900">Ananya Sharma</span>
                                        <span class="cora-client-email text-[11px] text-zinc-400">ananya@gmail.com</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap"><span class="cora-badge cora-badge-purple">Maternity Portrait</span></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-500">Lodhi Gardens, Delhi</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-500">24th Jun, 2026</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-zinc-900 cora-financial-col">₹25,000</td>
                                <td class="px-4 py-3 whitespace-nowrap"><span class="cora-badge cora-badge-blue">Confirmed</span></td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button class="cora-btn-icon-only inline-flex items-center justify-center p-1.5 rounded border border-zinc-200 text-zinc-500 hover:text-zinc-955 hover:bg-zinc-100 transition-all cursor-pointer" onclick="coraTriggerAction('whatsapp', 'Ananya Sharma')">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                            </svg>
                                        </button>
                                        <button class="cora-btn-action px-2 py-1 text-xs font-semibold border border-zinc-300 rounded text-zinc-700 hover:bg-zinc-50 active:scale-95 transition-all cursor-pointer" onclick="coraUpdateBookingStatus(this, 'editing')">Advance to Editing</button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Item 2 -->
                            <tr data-status="editing" class="hover:bg-zinc-50/30 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="cora-client-meta flex flex-col">
                                        <span class="cora-client-name font-semibold text-sm text-zinc-900">Rohit & Sneha</span>
                                        <span class="cora-client-email text-[11px] text-zinc-400">rohit.sneha@outlook.com</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap"><span class="cora-badge cora-badge-orange">Destination Wedding</span></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-500">Rambagh Palace, Jaipur</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-500">20th Jun, 2026</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-zinc-900 cora-financial-col">₹1,80,000</td>
                                <td class="px-4 py-3 whitespace-nowrap"><span class="cora-badge cora-badge-yellow">Editing</span></td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button class="cora-btn-icon-only inline-flex items-center justify-center p-1.5 rounded border border-zinc-200 text-zinc-500 hover:text-zinc-955 hover:bg-zinc-100 transition-all cursor-pointer" onclick="coraTriggerAction('caption-quick', 'Rohit & Sneha')">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                            </svg>
                                        </button>
                                        <button class="cora-btn-action px-2 py-1 text-xs font-semibold border border-zinc-300 rounded text-zinc-700 hover:bg-zinc-50 active:scale-95 transition-all cursor-pointer" onclick="coraUpdateBookingStatus(this, 'completed')">Mark Completed</button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Item 3 -->
                            <tr data-status="completed" class="hover:bg-zinc-50/30 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="cora-client-meta flex flex-col">
                                        <span class="cora-client-name font-semibold text-sm text-zinc-900">Rajesh Kumar (Studio B)</span>
                                        <span class="cora-client-email text-[11px] text-zinc-400">rk.enterprises@gmail.com</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap"><span class="cora-badge cora-badge-teal">Product Shoot</span></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-500">Studio A, Delhi</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-500">15th Jun, 2026</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-zinc-900 cora-financial-col">₹40,000</td>
                                <td class="px-4 py-3 whitespace-nowrap"><span class="cora-badge cora-badge-green">Completed</span></td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <span class="cora-delivered-text text-emerald-600 font-semibold text-xs mr-2">✓ Previews Sent</span>
                                        <button class="cora-btn-icon-only inline-flex items-center justify-center p-1.5 rounded border border-zinc-200 text-zinc-500 hover:text-zinc-955 hover:bg-zinc-100 transition-all cursor-pointer" onclick="coraTriggerAction('invoice', 'Rajesh Kumar')">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION 3: AI ASSISTANTS -->
            <?php if ( $sub_page === 'ai-assistants' ) : ?>
            <section id="cora-page-ai-assistants" class="cora-page-section cora-active space-y-6">
                <div class="cora-page-header flex items-center gap-3">
                    <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                        </svg>
                    </span>
                    <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">AI Assistants & Automation</h1>
                </div>
                <p class="cora-section-desc text-sm text-zinc-500 -mt-2">Deploy fine-tuned AI workflows to generate social media content, client follow-up templates, and WhatsApp automations.</p>

                <div class="cora-grid-two-col grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left: Instagram Caption Generator -->
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-5">
                        <div class="cora-card-icon-header flex items-center gap-2 border-b border-zinc-100 pb-3">
                            <span class="cora-card-header-emoji text-zinc-500 flex shrink-0">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </span>
                            <h3 class="cora-card-title text-base font-semibold text-zinc-955">Instagram Caption Generator</h3>
                        </div>
                        
                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Select Booking / Shoot Context</label>
                            <select id="cora-caption-shoot-select" class="cora-form-input w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                <option value="wedding-jaipur">Rohit & Sneha - Destination Wedding (Jaipur)</option>
                                <option value="maternity-delhi">Ananya Sharma - Maternity Portrait (Delhi)</option>
                                <option value="product-delhi">Rajesh Kumar - Product Commercial (Delhi)</option>
                            </select>
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Aesthetic Mood & Tone</label>
                            <select id="cora-caption-mood" class="cora-form-input w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                <option value="cinematic">Cinematic & Storytelling</option>
                                <option value="romantic">Romantic & Poetic (Shayari touch)</option>
                                <option value="minimalist">Minimal & Modern</option>
                                <option value="royal">Royal & Traditional</option>
                            </select>
                        </div>

                        <button id="cora-generate-caption-btn" class="cora-btn-primary cora-btn-full w-full py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm">
                            Generate Captions
                        </button>

                        <!-- Response Box -->
                        <div id="cora-caption-response" class="cora-ai-output-box hidden border border-zinc-200 rounded-lg p-4 bg-zinc-50 space-y-2 mt-4">
                            <div class="cora-output-header flex justify-between items-center text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                                <span>AI Drafts</span>
                                <button class="cora-copy-btn text-zinc-655 hover:text-zinc-955 font-semibold normal-case cursor-pointer" onclick="coraCopyText('cora-caption-text')">Copy</button>
                            </div>
                            <div id="cora-caption-text" class="cora-output-content text-xs text-zinc-800 whitespace-pre-line leading-relaxed font-mono">
                                <!-- JS will populate this -->
                            </div>
                        </div>
                    </div>

                    <!-- Right: WhatsApp Auto-Reminders -->
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-5">
                        <div class="cora-card-icon-header flex items-center gap-2 border-b border-zinc-100 pb-3">
                            <span class="cora-card-header-emoji text-zinc-500 flex shrink-0">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </span>
                            <h3 class="cora-card-title text-base font-semibold text-zinc-950">WhatsApp & Email Auto-Reminders</h3>
                        </div>

                        <div class="cora-toggle-list divide-y divide-zinc-150/80">
                            <div class="cora-toggle-item flex items-center justify-between py-3.5 gap-4">
                                <div class="cora-toggle-details flex-1">
                                    <span class="cora-toggle-title font-semibold text-sm text-zinc-900 block">Booking Confirmation WhatsApp</span>
                                    <span class="cora-toggle-desc text-xs text-zinc-500 block mt-0.5 leading-normal">Automatically ping client with shoot location and timing details upon confirmation.</span>
                                </div>
                                <label class="cora-switch relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950"></div>
                                </label>
                            </div>

                            <div class="cora-toggle-item flex items-center justify-between py-3.5 gap-4">
                                <div class="cora-toggle-details flex-1">
                                    <span class="cora-toggle-title font-semibold text-sm text-zinc-900 block">24h Shoot Reminder</span>
                                    <span class="cora-toggle-desc text-xs text-zinc-500 block mt-0.5 leading-normal">Send automated WhatsApp alert 24 hours prior to shoot with clothes/makeup checklist.</span>
                                </div>
                                <label class="cora-switch relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950"></div>
                                </label>
                            </div>

                            <div class="cora-toggle-item flex items-center justify-between py-3.5 gap-4">
                                <div class="cora-toggle-details flex-1">
                                    <span class="cora-toggle-title font-semibold text-sm text-zinc-900 block">AI Client Photo Selection Link</span>
                                    <span class="cora-toggle-desc text-xs text-zinc-500 block mt-0.5 leading-normal">Automatically emails/WhatsApp the preview gallery once upload is completed.</span>
                                </div>
                                <label class="cora-switch relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-8 h-4.5 bg-zinc-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-zinc-950"></div>
                                </label>
                            </div>
                        </div>

                        <div class="cora-callout bg-zinc-50 border border-zinc-200/80 rounded-lg p-3.5 flex gap-2.5 text-xs text-zinc-550 leading-relaxed shadow-sm">
                            <div class="cora-callout-emoji text-zinc-400 shrink-0 mt-0.5 flex">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                            </div>
                            <div class="cora-callout-text">
                                Local statistics report a substantial increase in client response speeds when deploying WhatsApp messaging templates over conventional emails in India.
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION 4: GALLERY & SEO TAGS -->
            <?php if ( $sub_page === 'gallery-seo' ) : ?>
            <section id="cora-page-gallery-seo" class="cora-page-section cora-active space-y-6">
                <div class="cora-page-header flex items-center gap-3">
                    <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </span>
                    <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Gallery & Media SEO Optimizer</h1>
                </div>
                <p class="cora-section-desc text-sm text-zinc-500 -mt-2">Boost local SEO search rankings. Upload photos, and Cora AI will automatically suggest file names, alt text, and tags to index on search engines.</p>

                <!-- Media Optimizer Layout -->
                <div class="cora-seo-layout grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Media list -->
                    <div class="cora-seo-media-list lg:col-span-1 border border-zinc-200/80 rounded-xl overflow-hidden divide-y divide-zinc-150 bg-white shadow-sm self-start">
                        <div class="bg-zinc-50 px-4 py-2.5 border-b border-zinc-200/50">
                            <h4 class="cora-sidebar-sublabel text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Recent Uploads</h4>
                        </div>
                        <div class="cora-media-item-row active flex items-center gap-3 p-3.5 cursor-pointer hover:bg-zinc-50/50 transition-colors" data-img-id="1">
                            <div class="cora-media-thumb w-8 h-8 bg-zinc-100 rounded border border-zinc-200 flex items-center justify-center text-zinc-450 shrink-0">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="cora-media-info flex flex-col min-w-0 flex-1">
                                <span class="cora-media-filename text-xs font-semibold text-zinc-900 truncate">bride_portrait_jaipur.jpg</span>
                                <span class="cora-media-status optimized text-[9px] font-bold uppercase text-emerald-600 mt-0.5">Optimized</span>
                            </div>
                        </div>
                        <div class="cora-media-item-row flex items-center gap-3 p-3.5 cursor-pointer hover:bg-zinc-50/50 transition-colors" data-img-id="2">
                            <div class="cora-media-thumb w-8 h-8 bg-zinc-100 rounded border border-zinc-200 flex items-center justify-center text-zinc-455 shrink-0">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                                </svg>
                            </div>
                            <div class="cora-media-info flex flex-col min-w-0 flex-1">
                                <span class="cora-media-filename text-xs font-semibold text-zinc-900 truncate">pre_wedding_sunset.jpg</span>
                                <span class="cora-media-status pending text-[9px] font-bold uppercase text-zinc-400 mt-0.5">Pending Scan</span>
                            </div>
                        </div>
                        <div class="cora-media-item-row flex items-center gap-3 p-3.5 cursor-pointer hover:bg-zinc-50/50 transition-colors" data-img-id="3">
                            <div class="cora-media-thumb w-8 h-8 bg-zinc-100 rounded border border-zinc-200 flex items-center justify-center text-zinc-450 shrink-0">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                            <div class="cora-media-info flex flex-col min-w-0 flex-1">
                                <span class="cora-media-filename text-xs font-semibold text-zinc-900 truncate">food_detail_wedding.jpg</span>
                                <span class="cora-media-status pending text-[9px] font-bold uppercase text-zinc-400 mt-0.5">Pending Scan</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: SEO Tag Editor Detail -->
                    <div class="cora-seo-details-card cora-card lg:col-span-2 bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-5">
                        <div class="cora-seo-preview-image">
                            <!-- Simulated Large Image -->
                            <div id="cora-large-media-preview" class="cora-large-preview-placeholder aspect-video bg-zinc-50 rounded-lg border border-zinc-200 flex items-center justify-center text-zinc-400 mb-6 overflow-hidden relative min-h-[180px]">
                                <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Suggested SEO Title</label>
                            <input type="text" id="cora-seo-title" class="cora-form-input w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" value="Aesthetic Indian Bride Portrait at Jaipur Rambagh Palace">
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">AI Alt Text (Description)</label>
                            <textarea id="cora-seo-alt" class="cora-form-input w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" rows="3">Cinematic wedding portrait of an Indian bride in red traditional lehenga posing at sunset inside the historical corridors of Rambagh Palace, Jaipur.</textarea>
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">AI Suggested Keywords / Tags</label>
                            <div id="cora-seo-tags-container" class="cora-tags-wrap flex flex-wrap gap-1.5 mt-1">
                                <span class="cora-tag-pill px-2.5 py-0.5 text-xs bg-zinc-100 border border-zinc-200 text-zinc-700 rounded-full font-medium">jaipur-wedding-photographer</span>
                                <span class="cora-tag-pill px-2.5 py-0.5 text-xs bg-zinc-100 border border-zinc-200 text-zinc-700 rounded-full font-medium">indian-bride-lehenga</span>
                                <span class="cora-tag-pill px-2.5 py-0.5 text-xs bg-zinc-100 border border-zinc-200 text-zinc-700 rounded-full font-medium">destination-wedding-jaipur</span>
                                <span class="cora-tag-pill px-2.5 py-0.5 text-xs bg-zinc-100 border border-zinc-200 text-zinc-700 rounded-full font-medium">rambagh-palace-shoot</span>
                                <span class="cora-tag-pill px-2.5 py-0.5 text-xs bg-zinc-100 border border-zinc-200 text-zinc-700 rounded-full font-medium">cinematic-bride-portrait</span>
                            </div>
                        </div>

                        <div class="cora-btn-group flex flex-wrap items-center gap-3 pt-3">
                            <button id="cora-apply-seo-btn" class="cora-btn-primary px-4 py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraApplySEOMetadata()">
                                Save to WordPress Media Library
                            </button>
                            <button class="cora-btn-secondary px-4 py-2.5 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraReScanAI()">
                                Re-Scan Image
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION 5: SETTINGS -->
            <?php if ( $sub_page === 'settings' ) : ?>
            <section id="cora-page-settings" class="cora-page-section cora-active space-y-6">
                <div class="cora-page-header flex items-center gap-3">
                    <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </span>
                    <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Workspace Settings</h1>
                </div>
                <p class="cora-section-desc text-sm text-zinc-500 -mt-2">Customize settings for the Cora Studio AI assistant and WhatsApp client communication channels.</p>

                <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm max-w-2xl space-y-6">
                    <div class="space-y-4">
                        <h3 class="cora-card-title text-base font-semibold text-zinc-950 border-b border-zinc-100 pb-2">Studio Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Studio / Brand Name</label>
                                <input type="text" class="cora-form-input w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" value="Cora Studio AI">
                            </div>
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Primary Region / Market</label>
                                <select class="cora-form-input w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                    <option value="IN">India (₹ INR)</option>
                                    <option value="US">United States ($ USD)</option>
                                    <option value="UK">United Kingdom (£ GBP)</option>
                                    <option value="AE">United Arab Emirates (AED)</option>
                                </select>
                            </div>
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">AI Tone of Voice</label>
                            <select class="cora-form-input w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                <option value="cinematic">Cinematic & Poetic (Best for fine-art wedding photographers)</option>
                                <option value="professional">Professional & Direct (Best for commercial studios)</option>
                                <option value="friendly">Warm & Welcoming (Best for family / newborn photographers)</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-zinc-150">
                        <h3 class="cora-card-title text-base font-semibold text-zinc-950 border-b border-zinc-100 pb-2">WhatsApp Gateway API (Beta)</h3>
                        <p class="cora-card-description text-xs text-zinc-500 -mt-1 leading-relaxed">Set up your WhatsApp Business Cloud API to enable auto-confirmations and photo links.</p>
                        
                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">WhatsApp Phone ID</label>
                            <input type="text" class="cora-form-input w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" value="10982348579124">
                        </div>
                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="cora-form-label text-[10px] font-bold text-zinc-400 uppercase tracking-wider">System Access Token</label>
                            <input type="password" class="cora-form-input w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" value="••••••••••••••••••••••••••••••••••••••••">
                        </div>
                    </div>

                    <button class="cora-btn-primary px-4 py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraShowToast('Settings saved successfully.')">
                        Save Workspace Settings
                    </button>
            </section>
            <?php endif; ?>

            <!-- SECTION 6: FEATURE HUB -->
            <?php if ( $sub_page === 'feature-hub' ) : ?>
            <section id="cora-page-feature-hub" class="cora-page-section cora-active space-y-6">
                <div class="cora-page-header flex items-center gap-3">
                    <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                        <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </span>
                    <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Feature Hub & Platform Roadmap</h1>
                </div>
                <p class="cora-section-desc text-sm text-zinc-500 -mt-2">Track active tools, in-progress integrations, and upcoming AI workflows designed for photography studios.</p>

                <!-- Grid layout of cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pt-2">
                    
                    <!-- 1. Rank #1 on Google Maps -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Rank #1 on Google Maps">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Rank #1 on Google Maps</h3>
                            <p class="text-xs text-zinc-500 leading-normal">Get found easily when local couples search for "best wedding photographer near me".</p>
                        </div>
                    </div>

                    <!-- 2. Sync Justdial Leads -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Sync Justdial Leads">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Sync Justdial Leads</h3>
                            <p class="text-xs text-zinc-500 leading-normal">Instantly grab incoming inquiries from Justdial right here before your competitors do.</p>
                        </div>
                    </div>

                    <!-- 3. Never Miss an Insta DM -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Never Miss an Insta DM">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Never Miss an Insta DM</h3>
                            <p class="text-xs text-zinc-500 leading-normal">Automatically reply to pricing questions on Instagram 24/7. Stop losing leads while shooting.</p>
                        </div>
                    </div>

                    <!-- 4. WhatsApp Auto-Followup -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="WhatsApp Auto-Followup">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">WhatsApp Auto-Followup</h3>
                            <p class="text-xs text-zinc-500 leading-normal">Send polite, automated WhatsApp reminders to couples who asked for quotes so they don't ghost.</p>
                        </div>
                    </div>

                    <!-- 5. Get More Google Reviews -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Get More Google Reviews">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Get More Google Reviews</h3>
                            <p class="text-xs text-zinc-500 leading-normal">Automatically send a WhatsApp message asking happy couples for a 5-star review after delivering photos.</p>
                        </div>
                    </div>

                    <!-- 6. Track Every Rupee -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Track Every Rupee">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="5" x2="18" y2="5"></line><line x1="6" y1="9" x2="18" y2="9"></line><path d="M9 5a6 6 0 0 1 0 12h3"></path><line x1="12" y1="11" x2="6" y2="17"></line></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Track Every Rupee</h3>
                            <p class="text-xs text-zinc-500 leading-normal">See exactly who has paid the 50% advance, who has pending dues, and track your total monthly revenue.</p>
                        </div>
                    </div>

                    <!-- 7. Beautiful Client Galleries -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Beautiful Client Galleries">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Beautiful Client Galleries</h3>
                            <p class="text-xs text-zinc-500 leading-normal">Send password-protected photo links to impress high-paying clients, replacing messy Google Drive links.</p>
                        </div>
                    </div>

                    <!-- 8. Easy Photo Selection -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Easy Photo Selection">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Easy Photo Selection</h3>
                            <p class="text-xs text-zinc-500 leading-normal">Couples can easily tap the heart icon on their phone to select photos for the printed album.</p>
                        </div>
                    </div>

                    <!-- 9. Manage Your Team (ACTIVE) -->
                    <div id="cora-card-manage-team" class="cora-feature-card bg-[#fafaf9] border border-zinc-300 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-950 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group relative overflow-hidden animate-in fade-in zoom-in-95 duration-200" data-feature="Manage Your Team">
                        <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-emerald-600 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                </span>
                                <span class="cora-badge cora-badge-green text-[8px] !rounded-md px-1.5 py-0.5 flex items-center gap-1 select-none">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                    ACTIVE
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-955 leading-snug group-hover:text-black">Manage Your Team</h3>
                            <p class="text-xs text-zinc-650 leading-normal">Track which photographer, videographer, or drone pilot is assigned to which wedding event.</p>
                        </div>
                        <div class="pt-3 border-t border-zinc-200/60 mt-2 flex justify-between items-center text-[10px] font-bold text-zinc-500 group-hover:text-zinc-900">
                            <span>Open Crew Assign Tool</span>
                            <span>→</span>
                        </div>
                    </div>

                    <!-- 10. Equipment Tracking (ACTIVE) -->
                    <div id="cora-card-equipment-tracking" class="cora-feature-card bg-[#fafaf9] border border-zinc-300 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-950 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group relative overflow-hidden animate-in fade-in zoom-in-95 duration-200" data-feature="Equipment Tracking">
                        <div class="absolute top-0 left-0 h-1 w-full bg-zinc-900"></div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-800 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                </span>
                                <span class="cora-badge cora-badge-green text-[8px] !rounded-md px-1.5 py-0.5 flex items-center gap-1 select-none">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                    ACTIVE
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-955 leading-snug group-hover:text-black">Equipment Tracking</h3>
                            <p class="text-xs text-zinc-650 leading-normal">Keep track of your expensive Sony/Canon cameras, lenses, and gimbals so nothing gets lost at shoots.</p>
                        </div>
                        <div class="pt-3 border-t border-zinc-200/60 mt-2 flex justify-between items-center text-[10px] font-bold text-zinc-500 group-hover:text-zinc-900">
                            <span>Open Equipment Registry</span>
                            <span>→</span>
                        </div>
                    </div>

                    <!-- 11. Instant Quotations -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Instant Quotations">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Instant Quotations</h3>
                            <p class="text-xs text-zinc-500 leading-normal">Generate professional PDFs with your wedding packages and send them to clients in 1 click.</p>
                        </div>
                    </div>

                    <!-- 12. Zero Paperwork -->
                    <div class="cora-feature-card cora-feature-soon bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-zinc-350 transition-all duration-200 cursor-pointer flex flex-col justify-between min-h-[160px] group animate-in fade-in zoom-in-95 duration-200" data-feature="Zero Paperwork">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-500 shrink-0">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 15l2 2 4-4"></path></svg>
                                </span>
                                <span class="cora-badge cora-badge-soon text-[8px] !rounded-md px-1.5 py-0.5 select-none">SOON</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-snug group-hover:text-black">Zero Paperwork</h3>
                            <p class="text-xs text-zinc-500 leading-normal">All your contracts, advance receipts, and GST invoices are created automatically. No more Excel sheets.</p>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION 7: TEAM & ROLES -->
            <?php if ( $sub_page === 'team-roles' ) : ?>
            <section id="cora-page-team-roles" class="cora-page-section cora-active space-y-6">
                <div class="flex items-center justify-between">
                    <div class="cora-page-header flex items-center gap-3">
                        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </span>
                        <div>
                            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Team & Role Permissions</h1>
                            <p class="cora-section-desc text-xs text-zinc-500 mt-1">Manage studio team members, assign custom roles, and configure feature access permissions.</p>
                        </div>
                    </div>
                </div>

                <!-- Sub-Navigation for Team Section -->
                <div class="cora-sub-tabs border-b border-zinc-200 flex gap-4 text-xs font-bold text-zinc-550 select-none pb-0.5">
                    <button class="cora-sub-tab active pb-2 border-b-2 border-zinc-950 text-zinc-950 cursor-pointer" data-sub-target="team-directory">Crew Directory</button>
                    <button class="cora-sub-tab pb-2 border-b-2 border-transparent hover:text-zinc-900 cursor-pointer" id="cora-sub-tab-team-form" data-sub-target="team-form">Add Member</button>
                    <button class="cora-sub-tab pb-2 border-b-2 border-transparent hover:text-zinc-900 cursor-pointer" data-sub-target="team-matrix">Permissions Matrix</button>
                </div>

                <!-- SUB-SECTION 1: CREW DIRECTORY -->
                <div id="cora-sub-page-team-directory" class="cora-sub-section active space-y-4">
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                            <div class="relative w-full max-w-xs">
                                <input type="text" id="cora-team-search" class="w-full border border-zinc-200 rounded-md py-1.5 pl-8 pr-3 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="Search members by name or email...">
                                <span class="absolute left-2.5 top-2.5 text-zinc-400">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                </span>
                            </div>
                            <span class="text-xs bg-zinc-100 text-zinc-655 px-2.5 py-0.5 rounded-full font-medium" id="cora-crew-count-badge"><?php echo count($cora_users); ?> Members</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 text-xs text-left" id="cora-team-table">
                                <thead>
                                    <tr class="bg-zinc-50/50">
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Avatar</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Display Name</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Username</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Email Address</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Studio Role</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-150" id="cora-team-list-container">
                                    <?php foreach ($cora_users as $user): 
                                        $roles = $user->roles;
                                        $role_key = !empty($roles) ? $roles[0] : 'subscriber';
                                        $role_label = isset($cora_role_labels[$role_key]) ? $cora_role_labels[$role_key] : ucfirst($role_key);
                                        $avatar_url = get_user_meta( $user->ID, 'cora_avatar_url', true );
                                    ?>
                                    <tr class="hover:bg-zinc-50/30 cora-member-row" data-id="<?php echo esc_attr($user->ID); ?>" data-username="<?php echo esc_attr($user->user_login); ?>" data-email="<?php echo esc_attr($user->user_email); ?>" data-display-name="<?php echo esc_attr($user->display_name); ?>" data-role="<?php echo esc_attr($role_key); ?>" data-avatar-url="<?php echo esc_attr($avatar_url); ?>">
                                         <td class="px-4 py-3">
                                             <?php if ( $avatar_url ) : ?>
                                                 <img src="<?php echo esc_url($avatar_url); ?>" class="w-7 h-7 rounded-full object-cover select-none border border-zinc-250/80" alt="<?php echo esc_attr($user->display_name); ?>">
                                             <?php else : ?>
                                                 <div class="w-7 h-7 rounded-full bg-zinc-100 border border-zinc-200 text-zinc-700 flex items-center justify-center font-bold text-[10px] uppercase cora-member-avatar-initials">
                                                     <?php echo esc_html(substr($user->display_name, 0, 2)); ?>
                                                 </div>
                                             <?php endif; ?>
                                         </td>
                                        <td class="px-4 py-3 font-bold text-zinc-900"><?php echo esc_html($user->display_name); ?></td>
                                        <td class="px-4 py-3 text-zinc-500 font-mono text-[10px]"><?php echo esc_html($user->user_login); ?></td>
                                        <td class="px-4 py-3 text-zinc-550"><?php echo esc_html($user->user_email); ?></td>
                                        <td class="px-4 py-3">
                                            <span class="cora-badge px-2 py-0.5 text-[9px] font-bold rounded-md select-none <?php echo $role_key === 'administrator' ? 'cora-badge-green' : 'cora-badge-sidebar'; ?>">
                                                <?php echo esc_html($role_label); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer cora-edit-user-btn" onclick="coraInitEditUser(<?php echo esc_attr($user->ID); ?>)">
                                                    Edit
                                                </button>
                                                <?php if (get_current_user_id() !== $user->ID) : ?>
                                                <button class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-red-600 bg-white hover:bg-red-50 hover:border-red-200 transition-all cursor-pointer cora-delete-user-btn" onclick="coraDeleteUser(<?php echo esc_attr($user->ID); ?>)">
                                                    Delete
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- SUB-SECTION 2: ADD / EDIT MEMBER FORM -->
                <div id="cora-sub-page-team-form" class="cora-sub-section hidden space-y-4">
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm max-w-xl space-y-4">
                        <h3 class="text-sm font-bold text-zinc-900 border-b border-zinc-100 pb-2 flex items-center gap-1.5" id="cora-team-form-title">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-555">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <line x1="19" y1="8" x2="19" y2="14"></line>
                                <line x1="16" y1="11" x2="22" y2="11"></line>
                            </svg>
                            Add Studio Member
                        </h3>
                        <p class="text-xs text-zinc-500 leading-normal" id="cora-team-form-desc">Create a new WordPress user profile mapped to your studio's operational roles.</p>
                        
                        <input type="hidden" id="cora-form-user-id">

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Display Name</label>
                            <input type="text" id="cora-user-display-name" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. Vikas Mehta">
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Profile Picture</label>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full overflow-hidden border border-zinc-200 bg-zinc-50 flex items-center justify-center shrink-0" id="cora-user-avatar-preview">
                                    <span class="text-zinc-400 text-xs font-bold" id="cora-avatar-initials">--</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <input type="file" id="cora-user-avatar-file" accept="image/*" class="hidden">
                                    <button type="button" class="cora-btn-secondary px-3 py-1.5 text-xs font-bold border border-zinc-250 rounded bg-white text-zinc-700 hover:bg-zinc-50 active:scale-95 transition-all cursor-pointer" onclick="$('#cora-user-avatar-file').click()">
                                        Choose Image
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="cora-form-group flex flex-col gap-1.5" id="cora-username-form-group">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Username</label>
                            <input type="text" id="cora-user-username" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors disabled:bg-zinc-100 disabled:text-zinc-400 disabled:cursor-not-allowed" placeholder="e.g. vikas_photo">
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Email Address</label>
                            <input type="email" id="cora-user-email" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors disabled:bg-zinc-100 disabled:text-zinc-400 disabled:cursor-not-allowed" placeholder="e.g. vikas@cora.ai">
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Password</label>
                            <div class="relative">
                                <input type="password" id="cora-user-password" class="w-full border border-zinc-200 rounded-md p-2 pr-10 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="Leave blank to keep current password when editing">
                                <button type="button" id="cora-toggle-password-visibility" class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-zinc-650 cursor-pointer" style="background: none; border: none; outline: none;">
                                    <!-- Eye Icon (Show) -->
                                    <svg id="cora-eye-show-icon" viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <!-- Eye Off Icon (Hide) -->
                                    <svg id="cora-eye-hide-icon" viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="hidden">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Operational Role</label>
                            <select id="cora-user-role" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                <option value="cora_photographer">Photographer</option>
                                <option value="cora_videographer">Videographer</option>
                                <option value="cora_drone_pilot">Drone Pilot</option>
                                <option value="cora_editor">Editor</option>
                                <option value="cora_manager">Manager</option>
                                <option value="administrator">Super Admin</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2.5 pt-3">
                            <button id="cora-save-user-btn" class="px-5 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs">
                                Add Member
                            </button>
                            <button id="cora-cancel-user-btn" class="px-4 py-2 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-xs hidden">
                                Cancel Edit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- SUB-SECTION 3: PERMISSIONS MATRIX -->
                <div id="cora-sub-page-team-matrix" class="cora-sub-section hidden space-y-4">
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                            <h3 class="text-sm font-bold text-zinc-900">Granular Role Permissions Matrix</h3>
                            <div class="flex items-center gap-1.5 text-[10px] font-bold text-zinc-550 select-none">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Live Sync Active
                            </div>
                        </div>
                        <p class="text-[11px] text-zinc-400 -mt-2 leading-relaxed">Determine dashboard screen visibilities for each studio role. Super Admin permissions are locked globally.</p>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 text-xs text-left" id="cora-permissions-matrix-table">
                                <thead>
                                    <tr class="bg-zinc-50/50">
                                        <th class="px-4 py-2.5 font-bold text-zinc-550 uppercase tracking-wider text-[10px]">Studio Role</th>
                                        <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Dashboard</th>
                                        <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Shoot CRM</th>
                                        <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Feature Hub</th>
                                        <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Team & Roles</th>
                                        <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Equipment</th>
                                        <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Financials</th>
                                        <th class="px-3 py-2.5 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-center">Settings</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-150">
                                    <!-- Super Admin row (Locked) -->
                                    <tr class="hover:bg-zinc-50/30">
                                        <td class="px-4 py-3 font-semibold text-zinc-900">Super Admin</td>
                                        <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                                        <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                                        <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                                        <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                                        <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                                        <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                                        <td class="text-center"><input type="checkbox" checked disabled class="accent-zinc-950"></td>
                                    </tr>
                                    <!-- Custom roles -->
                                    <?php 
                                    $target_roles = array(
                                        'cora_manager' => 'Manager',
                                        'cora_photographer' => 'Photographer',
                                        'cora_videographer' => 'Videographer',
                                        'cora_drone_pilot' => 'Drone Pilot',
                                        'cora_editor' => 'Editor'
                                    );
                                    $features = array('dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'settings');
                                    
                                    foreach ($target_roles as $role_key => $role_name): 
                                        $allowed_features = isset($cora_permissions[$role_key]) ? $cora_permissions[$role_key] : array();
                                    ?>
                                    <tr class="hover:bg-zinc-50/30 cora-matrix-row" data-role="<?php echo esc_attr($role_key); ?>">
                                        <td class="px-4 py-3 font-semibold text-zinc-800"><?php echo esc_html($role_name); ?></td>
                                        <?php foreach ($features as $feature): 
                                            $checked = in_array($feature, $allowed_features) ? 'checked' : '';
                                        ?>
                                        <td class="text-center">
                                            <input type="checkbox" <?php echo $checked; ?> data-feature="<?php echo esc_attr($feature); ?>" class="cora-permission-checkbox accent-zinc-950 cursor-pointer">
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION 8: EQUIPMENT TRACKING -->
            <?php if ( $sub_page === 'equipment' ) : ?>
            <section id="cora-page-equipment" class="cora-page-section cora-active space-y-6">
                <div class="flex items-center justify-between">
                    <div class="cora-page-header flex items-center gap-3">
                        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                        </span>
                        <div>
                            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Equipment Inventory & Tracking</h1>
                            <p class="cora-section-desc text-xs text-zinc-500 mt-1">Track high-value cameras, lenses, gimbals, and drones assigned to crew members and shoots.</p>
                        </div>
                    </div>
                </div>

                <!-- Sub-Navigation for Equipment Section -->
                <div class="cora-sub-tabs border-b border-zinc-200 flex gap-4 text-xs font-bold text-zinc-550 select-none pb-0.5">
                    <button class="cora-sub-tab active pb-2 border-b-2 border-zinc-950 text-zinc-950 cursor-pointer" data-sub-target="eq-registry">Inventory Registry</button>
                    <button class="cora-sub-tab pb-2 border-b-2 border-transparent hover:text-zinc-900 cursor-pointer" data-sub-target="eq-add">Add Gear</button>
                    <button class="cora-sub-tab pb-2 border-b-2 border-transparent hover:text-zinc-900 cursor-pointer" id="cora-sub-tab-eq-assign" data-sub-target="eq-assign">Assign / Release</button>
                </div>

                <!-- SUB-SECTION 1: EQUIPMENT INVENTORY REGISTRY -->
                <div id="cora-sub-page-eq-registry" class="cora-sub-section active space-y-6">
                    <!-- Stats summary counts -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="cora-equipment-stats-grid">
                        <?php
                        $total_items = count($cora_equipment);
                        $available_items = 0;
                        $in_use_items = 0;
                        $maintenance_items = 0;
                        foreach ($cora_equipment as $item) {
                            if ($item['status'] === 'Available') $available_items++;
                            elseif ($item['status'] === 'In Use') $in_use_items++;
                            elseif ($item['status'] === 'Maintenance') $maintenance_items++;
                        }
                        ?>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Assets</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1" id="cora-eq-stat-total"><?php echo $total_items; ?></span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Available</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1 flex items-center gap-1.5 text-emerald-600" id="cora-eq-stat-avail">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="cora-stat-count-num"><?php echo $available_items; ?></span>
                            </span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">In Use</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1 flex items-center gap-1.5 text-indigo-600" id="cora-eq-stat-use">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                <span class="cora-stat-count-num"><?php echo $in_use_items; ?></span>
                            </span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">In Maintenance</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1 flex items-center gap-1.5 text-amber-600" id="cora-eq-stat-maint">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-550"></span>
                                <span class="cora-stat-count-num"><?php echo $maintenance_items; ?></span>
                            </span>
                        </div>
                    </div>

                    <!-- Equipment Inventory Table -->
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                        <h3 class="text-sm font-bold text-zinc-900 pb-2 border-b border-zinc-100">Equipment Inventory Registry</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 text-xs text-left" id="cora-equipment-table">
                                <thead>
                                    <tr class="bg-zinc-50/50">
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Photo</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Asset Name</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Category</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Serial Number</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Status</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Assigned Crew</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Active Event / Shoot</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Assignment Details</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-150" id="cora-equipment-table-body">
                                    <?php foreach ($cora_equipment as $item): 
                                        $status_class = '';
                                        if ($item['status'] === 'Available') $status_class = 'cora-badge-green';
                                        elseif ($item['status'] === 'In Use') $status_class = 'cora-badge-soon';
                                        elseif ($item['status'] === 'Maintenance') $status_class = 'cora-badge-locked';
                                        
                                        $photo_url = !empty($item['photo_url']) ? $item['photo_url'] : '';
                                        $assignment_note = !empty($item['assignment_note']) ? $item['assignment_note'] : '';
                                    ?>
                                    <tr class="hover:bg-zinc-50/30 cora-eq-row" data-id="<?php echo esc_attr($item['id']); ?>" data-name="<?php echo esc_attr($item['name']); ?>">
                                        <td class="px-4 py-3.5">
                                            <?php if ($photo_url): ?>
                                                <img src="<?php echo esc_url($photo_url); ?>" class="w-8 h-8 rounded-md object-cover border border-zinc-200/80" />
                                            <?php else: ?>
                                                <div class="w-8 h-8 rounded-md bg-zinc-100 flex items-center justify-center border border-zinc-200/50 text-zinc-400">
                                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                                        <circle cx="12" cy="13" r="4"></circle>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3.5 font-bold text-zinc-800"><?php echo esc_html($item['name']); ?></td>
                                        <td class="px-4 py-3.5 text-zinc-550"><?php echo esc_html($item['category']); ?></td>
                                        <td class="px-4 py-3.5 text-zinc-400 font-mono text-[10px]"><?php echo esc_html($item['serial']); ?></td>
                                        <td class="px-4 py-3.5">
                                            <span class="cora-badge px-2 py-0.5 rounded text-[9px] font-semibold <?php echo $status_class; ?>">
                                                <?php echo esc_html($item['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-zinc-650 font-medium"><?php echo esc_html($item['crew'] ?: '—'); ?></td>
                                        <td class="px-4 py-3.5 text-zinc-550 max-w-[200px] truncate"><?php echo esc_html($item['shoot'] ?: '—'); ?></td>
                                        <td class="px-4 py-3.5 text-zinc-550 font-medium max-w-[200px] truncate"><?php echo esc_html($assignment_note ?: '—'); ?></td>
                                        <td class="px-4 py-3.5 text-right font-bold text-zinc-600">
                                            <div class="flex items-center justify-end gap-2">
                                                <button class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer cora-assign-eq-btn" onclick="coraInitAssignEquipment('<?php echo esc_attr($item['id']); ?>')">
                                                    Assign / Release
                                                </button>
                                                <button class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-red-600 bg-white hover:bg-red-50 hover:border-red-200 transition-all cursor-pointer cora-delete-eq-btn" onclick="coraDeleteEquipment('<?php echo esc_attr($item['id']); ?>')">
                                                    Delete
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

                <!-- SUB-SECTION 2: ADD EQUIPMENT FORM -->
                <div id="cora-sub-page-eq-add" class="cora-sub-section hidden space-y-4">
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm max-w-xl space-y-4">
                        <h3 class="text-sm font-bold text-zinc-900 border-b border-zinc-100 pb-2 flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-555">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                            Add Inventory Asset
                        </h3>
                        <p class="text-xs text-zinc-500 leading-normal">Log a new camera body, lens, lighting unit, drone, or gimbal into your active studio registry.</p>
                        
                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Asset Name</label>
                            <input type="text" id="cora-eq-name" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. Canon EOS R5">
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Category</label>
                            <select id="cora-eq-category" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                <option value="Camera">Camera</option>
                                <option value="Lens">Lens</option>
                                <option value="Drone">Drone</option>
                                <option value="Gimbal">Gimbal</option>
                                <option value="Light">Light</option>
                            </select>
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Serial Number</label>
                            <input type="text" id="cora-eq-serial" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. SN-98172461">
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Gear Photo</label>
                            <input type="file" id="cora-gear-photo-file" accept="image/*" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                        </div>

                        <div class="pt-3">
                            <button id="cora-save-equipment-btn" class="px-5 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs">
                                Add Asset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- SUB-SECTION 3: ASSIGN EQUIPMENT FORM -->
                <div id="cora-sub-page-eq-assign" class="cora-sub-section hidden space-y-4">
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm max-w-xl space-y-4">
                        <h3 class="text-sm font-bold text-zinc-900 border-b border-zinc-100 pb-2 flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-555">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <polyline points="16 11 18 13 22 9"></polyline>
                            </svg>
                            Assign Asset & Status
                        </h3>
                        <p class="text-xs text-zinc-500 leading-normal" id="cora-assign-eq-desc">Select an asset from the inventory to allocate to a crew member and active shoot.</p>
                        
                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Select Equipment Asset</label>
                            <select id="cora-assign-eq-id" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                <option value="">-- Choose Gear --</option>
                                <?php foreach ($cora_equipment as $item): ?>
                                <option value="<?php echo esc_attr($item['id']); ?>"><?php echo esc_html($item['name']); ?> (<?php echo esc_html($item['serial']); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="cora-form-group flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Asset Status</label>
                            <select id="cora-assign-eq-status" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                <option value="Available">Available (Unassigned)</option>
                                <option value="In Use">In Use (Assigned to Shoot)</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>

                        <!-- Allocation details, only shown if "In Use" is selected -->
                        <div id="cora-assign-eq-alloc-details" class="space-y-4 pt-2 hidden">
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Assign to Crew Member</label>
                                <select id="cora-assign-eq-crew" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                    <?php foreach ($cora_users as $user): ?>
                                    <option value="<?php echo esc_attr($user->display_name); ?>"><?php echo esc_html($user->display_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Select Active Event/Shoot</label>
                                <select id="cora-assign-eq-shoot" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                    <option value="Rohit & Sneha - Destination Wedding">Rohit & Sneha - Destination Wedding</option>
                                    <option value="Ananya Sharma - Maternity Portrait">Ananya Sharma - Maternity Portrait</option>
                                    <option value="Studio Work / Local Shoot">Studio Work / Local Shoot</option>
                                </select>
                            </div>

                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Assignment Details / Notes</label>
                                <textarea id="cora-assign-eq-note" rows="2" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. Cost: ₹2,500/day, Due: June 30th, Duration: 3 days"></textarea>
                            </div>
                        </div>

                        <div class="pt-3">
                            <button id="cora-confirm-eq-assign-btn" class="px-5 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs">
                                Save Allocation
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- SECTION 9: STUDIO VAULT -->
            <?php if ( $sub_page === 'vault' ) : ?>
            <section id="cora-page-vault" class="cora-page-section cora-active space-y-6">
                <!-- LIST VIEW -->
                <div id="cora-vault-list-view" class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="cora-page-header flex items-center gap-3">
                            <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                                <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                            <div>
                                <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Studio Vault</h1>
                                <p class="cora-section-desc text-xs text-zinc-500 mt-1">Securely manage, preview, and share official proposals, invoices, and contracts with clients.</p>
                            </div>
                        </div>
                        
                        <div>
                            <button id="cora-create-doc-btn" class="px-4 py-2 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs flex items-center gap-2" onclick="coraOpenDocDrawer()">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Add Document
                            </button>
                        </div>
                    </div>

                    <!-- Stats summary counts -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="cora-vault-stats-grid">
                        <?php
                        $total_docs = count($cora_documents);
                        $proposal_count = 0;
                        $invoice_count = 0;
                        $contract_count = 0;
                        $all_doc_types = array( 'Proposal', 'Invoice', 'Contract' );
                        foreach ($cora_documents as $doc) {
                            if ($doc['type'] === 'Proposal') $proposal_count++;
                            elseif ($doc['type'] === 'Invoice') $invoice_count++;
                            elseif ($doc['type'] === 'Contract') $contract_count++;

                            if ( ! in_array( $doc['type'], $all_doc_types ) ) {
                                $all_doc_types[] = $doc['type'];
                            }
                        }
                        ?>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Documents</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1" id="cora-doc-stat-total"><?php echo $total_docs; ?></span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Proposals</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1" id="cora-doc-stat-proposals"><?php echo $proposal_count; ?></span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Invoices</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1" id="cora-doc-stat-invoices"><?php echo $invoice_count; ?></span>
                        </div>
                        <div class="cora-stat-card bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Contracts</span>
                            <span class="text-xl font-bold text-zinc-900 mt-1" id="cora-doc-stat-contracts"><?php echo $contract_count; ?></span>
                        </div>
                    </div>

                    <!-- Document filters & layout -->
                    <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-3 flex-wrap gap-3">
                            <div class="flex gap-2 flex-wrap" id="cora-vault-filters">
                                <button class="cora-filter-btn px-3 py-1.5 rounded-md text-xs font-semibold bg-zinc-950 text-white cursor-pointer" data-filter="all">All Documents</button>
                                <?php foreach ( $all_doc_types as $type ) : 
                                    $label = $type . 's';
                                    if ( substr( $type, -1 ) === 's' ) {
                                        $label = $type;
                                    }
                                ?>
                                <button class="cora-filter-btn px-3 py-1.5 rounded-md text-xs font-semibold border border-zinc-200 text-zinc-650 bg-white hover:bg-zinc-50 cursor-pointer" data-filter="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $label ); ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 text-xs text-left" id="cora-vault-table">
                                <thead>
                                    <tr class="bg-zinc-50/50">
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Title</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Type</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] hidden sm:table-cell">Amount</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] hidden md:table-cell">Date Created</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px]">Status</th>
                                        <th class="px-4 py-3 font-bold text-zinc-500 uppercase tracking-wider text-[10px] text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-150" id="cora-vault-table-body">
                                    <?php foreach ($cora_documents as $doc): 
                                        $type_badge = 'bg-zinc-100 text-zinc-700 border border-zinc-200';
                                        if ($doc['type'] === 'Proposal') $type_badge = 'cora-badge-soon';
                                        elseif ($doc['type'] === 'Invoice') $type_badge = 'cora-badge-green';
                                        elseif ($doc['type'] === 'Contract') $type_badge = 'cora-badge-locked';
                                    ?>
                                    <tr class="hover:bg-zinc-50/30 cora-doc-row" data-type="<?php echo esc_attr($doc['type']); ?>" data-id="<?php echo esc_attr($doc['id']); ?>">
                                        <td class="px-4 py-3.5 font-bold text-zinc-800 flex flex-col gap-1">
                                            <span><?php echo esc_html($doc['title']); ?></span>
                                            <div class="flex flex-wrap items-center gap-1.5 sm:hidden mt-0.5">
                                                <span class="text-[10px] text-zinc-500 font-medium"><?php echo esc_html($doc['amount'] ?: '—'); ?></span>
                                                <span class="text-[9px] text-zinc-300">•</span>
                                                <span class="text-[10px] text-zinc-400 font-mono"><?php echo esc_html($doc['created_date']); ?></span>
                                            </div>
                                            <?php if (!empty($doc['secured_shares'])): ?>
                                                <span class="text-[9px] text-zinc-400 font-medium flex items-center gap-1 mt-0.5">
                                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                    </svg>
                                                    Shared with <?php echo count($doc['secured_shares']); ?> recipient(s)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3.5 text-zinc-550">
                                            <span class="cora-badge px-2 py-0.5 rounded text-[9px] font-semibold <?php echo $type_badge; ?>">
                                                <?php echo esc_html($doc['type']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-zinc-600 font-medium hidden sm:table-cell"><?php echo esc_html($doc['amount'] ?: '—'); ?></td>
                                        <td class="px-4 py-3.5 text-zinc-400 font-mono text-[10px] hidden md:table-cell"><?php echo esc_html($doc['created_date']); ?></td>
                                        <td class="px-4 py-3.5">
                                            <span class="cora-badge px-2 py-0.5 rounded text-[9px] font-semibold cora-badge-status-<?php echo strtolower($doc['status']); ?>">
                                                <?php echo esc_html($doc['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-right font-bold text-zinc-600">
                                            <div class="flex items-center justify-end gap-2">
                                                <button class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer cora-view-doc-btn" onclick="coraViewDocument('<?php echo esc_attr($doc['id']); ?>')">
                                                    View / Edit
                                                </button>
                                                <button class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer cora-share-doc-btn" onclick="coraOpenShareDrawer('<?php echo esc_attr($doc['id']); ?>')">
                                                    Share Securely
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

                <!-- DEDICATED FULL-PAGE EDITOR VIEW -->
                <div id="cora-vault-editor-view" class="hidden space-y-6">
                    <!-- Editor Header -->
                    <div class="flex items-center justify-between border-b border-zinc-200 pb-4 flex-wrap gap-4">
                        <div class="flex items-center gap-4">
                            <button class="flex items-center gap-1.5 px-3 py-1.5 border border-zinc-200 rounded-md text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer" onclick="coraCloseEditor()">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                                Back
                            </button>
                            <input type="text" id="cora-doc-title-input" class="text-base md:text-lg font-bold text-zinc-900 border-b border-transparent hover:border-zinc-200 focus:border-zinc-950 focus:outline-none bg-transparent px-1 py-0.5 transition-all w-48 md:w-80" placeholder="Untitled Document">
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <button onclick="coraDownloadPDF()" class="px-3 py-1.5 border border-zinc-200 rounded-md text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                    <rect x="6" y="14" width="12" height="8"></rect>
                                </svg>
                                Export PDF
                            </button>
                            <button onclick="coraDownloadDOCX()" class="px-3 py-1.5 border border-zinc-200 rounded-md text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Word (DOCX)
                            </button>
                            <button id="cora-save-doc-editor-btn" class="px-4 py-1.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-xs">
                                Save Document
                            </button>
                        </div>
                    </div>

                    <!-- Editor Body Columns -->
                    <div class="flex flex-col lg:flex-row gap-6 items-start">
                        <!-- Main Canvas Area -->
                        <div class="flex-1 w-full bg-zinc-50 border border-zinc-200 rounded-xl overflow-hidden flex flex-col">
                            <!-- Rich Text Editor Toolbar -->
                            <div class="cora-editor-toolbar flex items-center gap-1.5 p-2 bg-white border-b border-zinc-200 overflow-x-auto select-none">
                                <select id="cora-editor-heading" class="bg-transparent border-0 text-xs font-semibold text-zinc-700 focus:outline-none focus:ring-0 cursor-pointer h-7 rounded hover:bg-zinc-100 px-1.5 py-0" onchange="coraEditorApplyHeading(this.value)">
                                    <option value="p">Normal Text</option>
                                    <option value="h1">Heading 1</option>
                                    <option value="h2">Heading 2</option>
                                    <option value="h3">Heading 3</option>
                                </select>
                                
                                <div class="h-4 w-[1px] bg-zinc-200 mx-1"></div>
                                
                                <button type="button" onclick="coraEditorFormat('bold')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Bold (Cmd+B)">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                                        <path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraEditorFormat('italic')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Italic (Cmd+I)">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="19" y1="4" x2="10" y2="4"></line>
                                        <line x1="14" y1="20" x2="5" y2="20"></line>
                                        <line x1="15" y1="4" x2="9" y2="20"></line>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraEditorFormat('underline')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Underline (Cmd+U)">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"></path>
                                        <line x1="4" y1="21" x2="20" y2="21"></line>
                                    </svg>
                                </button>
                                
                                <div class="h-4 w-[1px] bg-zinc-200 mx-1"></div>
                                
                                <button type="button" onclick="coraEditorFormat('insertUnorderedList')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Bullet List">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="8" y1="6" x2="21" y2="6"></line>
                                        <line x1="8" y1="12" x2="21" y2="12"></line>
                                        <line x1="8" y1="18" x2="21" y2="18"></line>
                                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraEditorFormat('insertOrderedList')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Numbered List">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="10" y1="6" x2="21" y2="6"></line>
                                        <line x1="10" y1="12" x2="21" y2="12"></line>
                                        <line x1="10" y1="18" x2="21" y2="18"></line>
                                        <path d="M4 6h1v4H4M4 10h2"></path>
                                        <path d="M4 14h2v2H4v2h2"></path>
                                    </svg>
                                </button>
                                
                                <div class="h-4 w-[1px] bg-zinc-200 mx-1"></div>
                                
                                <button type="button" onclick="coraEditorFormat('justifyLeft')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Align Left">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="17" y1="10" x2="3" y2="10"></line>
                                        <line x1="21" y1="6" x2="3" y2="6"></line>
                                        <line x1="21" y1="14" x2="3" y2="14"></line>
                                        <line x1="17" y1="18" x2="3" y2="18"></line>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraEditorFormat('justifyCenter')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Align Center">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="10" x2="6" y2="10"></line>
                                        <line x1="21" y1="6" x2="3" y2="6"></line>
                                        <line x1="21" y1="14" x2="3" y2="14"></line>
                                        <line x1="18" y1="18" x2="6" y2="18"></line>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraEditorFormat('justifyRight')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Align Right">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="21" y1="10" x2="7" y2="10"></line>
                                        <line x1="21" y1="6" x2="3" y2="6"></line>
                                        <line x1="21" y1="14" x2="3" y2="14"></line>
                                        <line x1="21" y1="18" x2="7" y2="18"></line>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="coraEditorFormat('justifyFull')" class="w-7 h-7 flex items-center justify-center rounded text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer" title="Justify">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="21" y1="10" x2="3" y2="10"></line>
                                        <line x1="21" y1="6" x2="3" y2="6"></line>
                                        <line x1="21" y1="14" x2="3" y2="14"></line>
                                        <line x1="21" y1="18" x2="3" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Document Paper Canvas -->
                            <div class="bg-zinc-100/50 p-4 md:p-8 flex justify-center items-start overflow-y-auto min-h-[calc(100vh-220px)] border-b border-zinc-200">
                                <div id="cora-paper-container" class="w-full max-w-[800px] bg-white border border-zinc-200 rounded-lg shadow-sm p-6 md:p-12 min-h-[297mm] flex flex-col justify-between">
                                    <div class="w-full">
                                        <!-- Paper Header (Logo Preview) -->
                                        <div id="cora-paper-header-preview" class="border-b border-zinc-100 pb-4 mb-6 flex items-center justify-start hidden">
                                            <!-- Image will render here dynamically -->
                                        </div>
                                        
                                        <!-- Editable Body Area -->
                                        <div id="cora-doc-paper" contenteditable="true" class="focus:outline-none prose max-w-none text-zinc-800 text-sm leading-relaxed" placeholder="Start typing your document..."></div>
                                    </div>
                                    
                                    <!-- Paper Footer (Footer Text Preview) -->
                                    <div id="cora-paper-footer-preview" contenteditable="true" class="border-t border-zinc-200 pt-4 mt-8 text-center text-xs text-zinc-400 focus:outline-none focus:ring-0" placeholder="Enter footer branding text..."></div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Settings (Branding & metadata) -->
                        <aside class="w-full lg:w-80 bg-white border border-zinc-200 rounded-xl p-5 shadow-sm space-y-4 shrink-0">
                            <input type="hidden" id="cora-doc-id-hidden" value="">
                            
                            <h2 class="text-xs font-bold text-zinc-800 uppercase tracking-wider border-b border-zinc-100 pb-2">Document Settings</h2>
                            
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Document Type</label>
                                <select id="cora-doc-type-select" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                                    <?php foreach ( $all_doc_types as $type ) : ?>
                                        <option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></option>
                                    <?php endforeach; ?>
                                    <option value="__add_custom_type__" class="font-bold text-zinc-500">+ Add Custom Type...</option>
                                </select>
                            </div>
                            
                            <!-- Inline Custom Type Input (Hidden by default) -->
                            <div id="cora-custom-type-input-group" class="hidden flex items-center gap-2 mt-1">
                                <input type="text" id="cora-custom-type-input" class="flex-1 border border-zinc-200 rounded-md p-2 text-xs bg-white focus:border-zinc-400 focus:outline-none" placeholder="e.g. Quote">
                                <button type="button" id="cora-custom-type-save" class="px-2.5 py-1.5 bg-zinc-950 text-white text-[10px] font-semibold rounded hover:bg-zinc-800 transition-colors cursor-pointer">Add</button>
                                <button type="button" id="cora-custom-type-cancel" class="px-2 py-1.5 border border-zinc-200 text-zinc-655 text-[10px] font-semibold rounded hover:bg-zinc-50 transition-colors cursor-pointer">Cancel</button>
                            </div>
                            
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Amount / Value (Optional)</label>
                                <input type="text" id="cora-doc-amount-input" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. ₹1,50,000">
                            </div>
                            
                            <h2 class="text-xs font-bold text-zinc-800 uppercase tracking-wider border-b border-zinc-100 pt-2 pb-2">Branding Elements</h2>
                            
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Branding Logo</label>
                                <div class="flex items-center gap-3">
                                    <div id="cora-logo-upload-preview" class="w-16 h-16 border border-zinc-200 rounded-md flex items-center justify-center bg-zinc-50 overflow-hidden shrink-0">
                                        <span class="text-[9px] text-zinc-400 text-center px-1">No Logo</span>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <button type="button" id="cora-doc-logo-upload-btn" class="px-2.5 py-1.5 border border-zinc-200 rounded text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 transition-colors cursor-pointer">
                                            Choose Image
                                        </button>
                                        <button type="button" id="cora-doc-logo-remove-btn" class="text-[10px] text-red-500 hover:text-red-700 font-semibold text-left hidden">
                                            Remove Logo
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="cora-doc-logo-url" value="">
                            </div>
                            
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Footer Text</label>
                                <textarea id="cora-doc-footer-text" rows="3" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. © 2026 Nitin Arora Photography. All rights reserved. • Contact: hello@nitinarora.com" oninput="coraEditorUpdateBranding()"></textarea>
                                <span class="text-[9px] text-zinc-400">This text appears centered at the bottom of the page.</span>
                            </div>

                            <h2 class="text-xs font-bold text-zinc-800 uppercase tracking-wider border-b border-zinc-100 pt-2 pb-2">Sync Document</h2>
                            
                            <div class="cora-form-group flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Sync from Google Doc</label>
                                <div class="flex gap-1.5">
                                    <input type="url" id="cora-doc-gdoc-url" class="flex-1 border border-zinc-200 rounded-md p-2 text-xs bg-white focus:border-zinc-400 focus:outline-none" placeholder="Paste Google Doc sharing link">
                                    <button type="button" id="cora-doc-gdoc-sync-btn" class="px-3 py-2 bg-zinc-950 hover:bg-zinc-800 text-white text-[10px] font-bold rounded transition-colors cursor-pointer shrink-0">Sync</button>
                                </div>
                                <span class="text-[9px] text-zinc-400">Ensure the Google Doc is shared publicly ("Anyone with the link can view").</span>
                            </div>
                        </aside>
                    </div>
                </div>
            </section>
            <?php endif; ?>

        </div>
    </main>

    <!-- Collapsible Right-side AI Sidebar (Notion-AI style) -->
    <aside id="cora-ai-sidebar" class="cora-ai-sidebar collapsed fixed top-0 right-0 z-50 h-full w-[350px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header flex justify-between items-center px-4 py-3 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <span class="cora-ai-sidebar-title text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-550">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                </svg>
                Cora AI Assistant
            </span>
            <button class="cora-ai-sidebar-close text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleSidebar(false)">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="cora-ai-sidebar-body flex-1 overflow-y-auto p-4 flex flex-col justify-between gap-6">
            <div class="cora-ai-sidebar-chat-history flex flex-col gap-3" id="cora-sidebar-chat">
                <div class="chat-bubble ai bg-zinc-100 text-zinc-850 rounded-lg rounded-bl-none p-3 text-xs leading-relaxed self-start border border-zinc-200/50 shadow-sm max-w-[85%]">
                    Hello! I am Cora, your studio workspace intelligence. Ask me about bookings, client messages, or writing captions.
                </div>
            </div>
            <!-- AI Prompt Shortcuts -->
            <div class="cora-ai-sidebar-shortcuts pt-4 border-t border-zinc-150">
                <span class="cora-sidebar-sublabel text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2.5 block">Quick Prompts</span>
                <button class="cora-shortcut-btn w-full text-left p-2.5 text-xs text-zinc-650 border border-zinc-200 rounded-md hover:bg-zinc-50 hover:text-zinc-950 transition-colors mb-2 cursor-pointer font-medium" onclick="coraSendShortcut('Draft a WhatsApp reminder for Ananya Sharma')">Draft a reminder for Ananya</button>
                <button class="cora-shortcut-btn w-full text-left p-2.5 text-xs text-zinc-655 border border-zinc-200 rounded-md hover:bg-zinc-50 hover:text-zinc-955 transition-colors cursor-pointer font-medium" onclick="coraSendShortcut('Check status of Rohit & Sneha')">Check Rohit & Sneha's shoot</button>
            </div>
        </div>
        <div class="cora-ai-sidebar-footer-input p-3 border-t border-zinc-200/80 flex items-center gap-2 bg-zinc-50 shrink-0">
            <input type="text" id="cora-sidebar-chat-input" placeholder="Ask Cora AI..." onkeydown="if(event.key === 'Enter') coraSendSidebarChatMessage()" class="flex-1 border border-zinc-200 rounded-md p-2 text-xs bg-white focus:border-zinc-400 focus:outline-none">
            <button class="cora-btn-primary px-3 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded text-xs transition-colors cursor-pointer shrink-0" onclick="coraSendSidebarChatMessage()">Send</button>
        </div>
    </aside>

    <!-- Create Booking Side Drawer (Notion-AI style form space saver) -->
    <aside id="cora-add-shoot-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-[350px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header flex justify-between items-center px-4 py-3 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <span class="text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Create New Shoot
            </span>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleAddShootDrawer(false)">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Client Full Name</label>
                <input type="text" id="cora-drawer-client-name" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. Ramesh Kumar">
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Shoot Type</label>
                <select id="cora-drawer-shoot-type" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                    <option value="Maternity Portrait">Maternity Portrait</option>
                    <option value="Destination Wedding">Destination Wedding</option>
                    <option value="Product Shoot">Product Shoot</option>
                    <option value="Couples Portrait">Couples Portrait</option>
                    <option value="Commercial Campaign">Commercial Campaign</option>
                </select>
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Shoot Location</label>
                <input type="text" id="cora-drawer-location" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. Lodhi Gardens, Delhi">
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Shoot Date</label>
                <input type="text" id="cora-drawer-date" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. 28th Jun, 2026">
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Package Value</label>
                <input type="text" id="cora-drawer-price" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. ₹15,000">
            </div>
        </div>
        <div class="cora-drawer-footer p-4 flex items-center gap-2.5 shrink-0">
            <button id="cora-save-shoot-btn" class="flex-1 py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm">
                Create Booking
            </button>
            <button class="px-4 py-2.5 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraToggleAddShootDrawer(false)">
                Cancel
            </button>
        </div>
    </aside>

    <!-- Team Assignment Side Drawer -->
    <aside id="cora-team-management-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-[350px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header flex justify-between items-center px-4 py-3 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <span class="text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Team Crew Assignments
            </span>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleTeamDrawer(false)">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
            <p class="text-xs text-zinc-500 leading-normal border-b border-zinc-100 pb-3">Select the active photography crew, videographers, and drone pilots for your scheduled shoots.</p>
            
            <!-- Shoot Event 1 -->
            <div class="space-y-3 pb-4 border-b border-zinc-100">
                <span class="text-xs font-bold text-zinc-800 block">Rohit & Sneha - Destination Wedding</span>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Lead Photographer</label>
                    <select id="cora-team-shoot1-photographer" class="w-full border border-zinc-200 rounded-md p-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                        <?php foreach ($photographers as $u): 
                            $selected = (isset($s1_assignments['photographer']) && $s1_assignments['photographer'] === $u->display_name) ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($u->display_name); ?>" <?php echo $selected; ?>><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?php echo !isset($s1_assignments['photographer']) || 'none' === $s1_assignments['photographer'] ? 'selected' : ''; ?>>None / Unassigned</option>
                    </select>
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Lead Videographer</label>
                    <select id="cora-team-shoot1-videographer" class="w-full border border-zinc-200 rounded-md p-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                        <?php foreach ($videographers as $u): 
                            $selected = (isset($s1_assignments['videographer']) && $s1_assignments['videographer'] === $u->display_name) ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($u->display_name); ?>" <?php echo $selected; ?>><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?php echo !isset($s1_assignments['videographer']) || 'none' === $s1_assignments['videographer'] ? 'selected' : ''; ?>>None / Unassigned</option>
                    </select>
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Drone Pilot</label>
                    <select id="cora-team-shoot1-drone" class="w-full border border-zinc-200 rounded-md p-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                        <?php foreach ($drone_pilots as $u): 
                            $selected = (isset($s1_assignments['drone']) && $s1_assignments['drone'] === $u->display_name) ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($u->display_name); ?>" <?php echo $selected; ?>><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?php echo !isset($s1_assignments['drone']) || 'none' === $s1_assignments['drone'] ? 'selected' : ''; ?>>None / Unassigned</option>
                    </select>
                </div>
            </div>

            <!-- Shoot Event 2 -->
            <div class="space-y-3">
                <span class="text-xs font-bold text-zinc-800 block">Ananya Sharma - Maternity Portrait</span>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Lead Photographer</label>
                    <select id="cora-team-shoot2-photographer" class="w-full border border-zinc-200 rounded-md p-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                        <?php foreach ($photographers as $u): 
                            $selected = (isset($s2_assignments['photographer']) && $s2_assignments['photographer'] === $u->display_name) ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($u->display_name); ?>" <?php echo $selected; ?>><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?php echo !isset($s2_assignments['photographer']) || 'none' === $s2_assignments['photographer'] ? 'selected' : ''; ?>>None / Unassigned</option>
                    </select>
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Shoot Assistant</label>
                    <select id="cora-team-shoot2-assistant" class="w-full border border-zinc-200 rounded-md p-1.5 text-xs bg-white focus:border-zinc-400 focus:outline-none transition-colors">
                        <?php foreach ($cora_users as $u): 
                            $selected = (isset($s2_assignments['assistant']) && $s2_assignments['assistant'] === $u->display_name) ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($u->display_name); ?>" <?php echo $selected; ?>><?php echo esc_html($u->display_name); ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?php echo !isset($s2_assignments['assistant']) || 'none' === $s2_assignments['assistant'] ? 'selected' : ''; ?>>None / Unassigned</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="cora-drawer-footer p-4 flex items-center gap-2.5 shrink-0">
            <button id="cora-save-team-btn" class="flex-1 py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm">
                Save Crew Assignments
            </button>
            <button class="px-4 py-2.5 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraToggleTeamDrawer(false)">
                Cancel
            </button>
        </div>
    </aside>
    <!-- Secure Document Share Drawer (Studio Vault) -->
    <aside id="cora-share-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-[380px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <div class="cora-ai-sidebar-header flex justify-between items-center px-4 py-3 border-b border-zinc-200/80 bg-zinc-50 shrink-0">
            <span class="text-xs font-bold text-zinc-800 flex items-center uppercase tracking-wider gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-550">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Secure Sharing
            </span>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer" onclick="coraToggleShareDrawer(false)">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <input type="hidden" id="cora-share-doc-id" value="">
            <p class="text-xs text-zinc-500 leading-normal pb-2 border-b border-zinc-100">Send an encrypted, self-expiring link directly to the client's email via WordPress mail relay.</p>
            
            <div class="cora-form-group flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Client Email Address</label>
                <input type="email" id="cora-share-email" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors" placeholder="e.g. client@example.com">
            </div>
            
            <div class="cora-form-group flex flex-col gap-1.5" id="cora-share-expiry-container">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Link Expiration Date</label>
                <input type="date" id="cora-share-date-picker" class="w-full border border-zinc-200 rounded-md p-2 text-sm bg-white focus:border-zinc-400 focus:outline-none transition-colors">
            </div>
            
            <div class="flex items-center gap-2 mt-2">
                <input type="checkbox" id="cora-share-no-expiry" class="rounded border-zinc-350 text-zinc-950 focus:ring-zinc-500 cursor-pointer">
                <label for="cora-share-no-expiry" class="text-xs text-zinc-650 font-semibold select-none cursor-pointer">Never Expires (Permanent Link)</label>
            </div>
            
            <!-- Output share link if generated -->
            <div id="cora-share-result-box" class="pt-4 space-y-2 hidden">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Generated Secure Link</label>
                <div class="flex gap-2">
                    <input type="text" id="cora-share-link-input" readonly class="flex-1 border border-zinc-200 rounded-md p-2 text-xs bg-zinc-50 font-mono focus:outline-none" value="">
                    <button class="px-3 py-2 border border-zinc-350 rounded-md text-xs font-semibold hover:bg-zinc-50 cursor-pointer active:scale-95" onclick="coraCopyShareLink()">Copy</button>
                </div>
                <span class="text-[10px] text-zinc-400 block" id="cora-share-expiry-text">Expires on: Dec 12, 2026</span>
            </div>
        </div>
        <div class="cora-drawer-footer p-4 flex items-center gap-2.5 shrink-0">
            <button id="cora-share-submit-btn" class="flex-1 py-2.5 bg-zinc-950 text-white font-semibold rounded-md hover:bg-zinc-800 transition-all active:scale-[0.98] cursor-pointer text-sm">
                Send & Generate Link
            </button>
            <button class="px-4 py-2.5 border border-zinc-250 rounded-md text-zinc-700 bg-white font-semibold hover:bg-zinc-50 transition-all active:scale-[0.98] cursor-pointer text-sm" onclick="coraToggleShareDrawer(false)">
                Cancel
            </button>
        </div>
    </aside>

    <!-- Mobile Bottom Navigation (Shopify style) -->
    <nav class="cora-bottom-nav fixed bottom-0 left-0 w-full h-14 bg-white/95 backdrop-blur-sm border-t border-zinc-200 flex justify-around items-center z-45 px-2 lg:hidden shadow-[0_-2px_10px_rgba(0,0,0,0.03)]">
        <div class="cora-bottom-nav-item <?php echo $sub_page === 'dashboard' ? 'cora-active' : ''; ?> flex flex-col items-center justify-center text-[10px] font-medium text-zinc-400 cursor-pointer py-1 flex-1 transition-colors duration-150" data-target="dashboard">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mb-0.5">
                <rect x="3" y="3" width="7" height="9" rx="1"></rect>
                <rect x="14" y="3" width="7" height="5" rx="1"></rect>
                <rect x="14" y="12" width="7" height="9" rx="1"></rect>
                <rect x="3" y="16" width="7" height="5" rx="1"></rect>
            </svg>
            <span>Home</span>
        </div>
        <div class="cora-bottom-nav-item <?php echo $sub_page === 'bookings' ? 'cora-active' : ''; ?> flex flex-col items-center justify-center text-[10px] font-medium text-zinc-400 cursor-pointer py-1 flex-1 transition-colors duration-150" data-target="bookings">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mb-0.5">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>CRM</span>
        </div>
        <div class="cora-bottom-nav-item flex flex-col items-center justify-center text-[10px] font-medium text-zinc-400 cursor-pointer py-1 flex-1 transition-colors duration-150" id="cora-mobile-ai-trigger">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mb-0.5">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
            </svg>
            <span>Cora AI</span>
        </div>
        <div class="cora-bottom-nav-item <?php echo $sub_page === 'gallery-seo' ? 'cora-active' : ''; ?> cora-nav-locked flex flex-col items-center justify-center text-[10px] font-medium text-zinc-400 cursor-pointer py-1 flex-1 transition-colors duration-150" data-target="gallery-seo">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mb-0.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            <span>SEO</span>
        </div>
        <div class="cora-bottom-nav-item <?php echo $sub_page === 'vault' ? 'cora-active' : ''; ?> flex flex-col items-center justify-center text-[10px] font-medium text-zinc-400 cursor-pointer py-1 flex-1 transition-colors duration-150" data-target="vault">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mb-0.5">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span>Vault</span>
        </div>
        <div class="cora-bottom-nav-item <?php echo $sub_page === 'settings' ? 'cora-active' : ''; ?> flex flex-col items-center justify-center text-[10px] font-medium text-zinc-400 cursor-pointer py-1 flex-1 transition-colors duration-150" data-target="settings">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mb-0.5">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1.82-.33H15a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 16 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H15a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
            <span>Settings</span>
        </div>
    </nav>

</div> <!-- #cora-workspace -->

<!-- Workspace Script (Inlined for bulletproof execution) -->
<script>
    <?php include CORA_STUDIO_AI_PATH . 'assets/js/admin-script.js'; ?>
</script>

</body>
</html>
