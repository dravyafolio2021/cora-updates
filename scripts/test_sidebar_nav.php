<?php
require_once __DIR__ . '/../app/public/wp-load.php';

$users = get_users();
echo "Total users in DB: " . count($users) . "\n";
foreach ($users as $u) {
    echo "User: " . $u->user_login . " (ID: " . $u->ID . ", Email: " . $u->user_email . ", Roles: " . implode(',', $u->roles) . ")\n";
    wp_set_current_user($u->ID);
    $ind = cora_get_active_industry();
    $mod = Cora_Module_Registry::get_module($ind);
    $current_role = $u->roles[0] ?? 'subscriber';
    $nav = $mod ? $mod->get_navigation_groups($current_role) : array();
    echo "  Industry: $ind | Module: " . ($mod ? get_class($mod) : 'NULL') . " | Groups: " . count($nav) . "\n";
    $is_super = cora_is_super_owner();
    echo "  Is Super Owner: " . ($is_super ? 'TRUE' : 'FALSE') . "\n";
    foreach ($nav as $g) {
        $item_count = count($g['items'] ?? []);
        echo "    Group: " . ($g['label'] ?? 'Unknown') . " ($item_count items)\n";
        foreach ($g['items'] ?? [] as $target => $item) {
            $has_acc = cora_user_has_feature_access($target);
            echo "      - $target (" . $item['title'] . ") access=" . ($has_acc ? '1' : '0') . "\n";
        }
    }
    echo "----------------------------------------\n";
}
