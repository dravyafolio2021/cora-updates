<?php
require_once __DIR__ . '/wp-load.php';
header('Content-Type: application/json');

echo json_encode(array(
    'cora_git_sync_enabled' => get_option('cora_git_sync_enabled'),
    'cora_git_sync_repo' => get_option('cora_git_sync_repo'),
    'cora_git_sync_branch' => get_option('cora_git_sync_branch'),
    'cora_git_sync_live_url' => get_option('cora_git_sync_live_url'),
), JSON_PRETTY_PRINT);
