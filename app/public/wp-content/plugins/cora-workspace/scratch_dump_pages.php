<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

header('Content-Type: text/plain');

global $wpdb;
$table = $wpdb->prefix . 'cora_docs_pages';

if (!cora_table_exists($table)) {
    echo "Table $table does not exist.\n";
    exit;
}

$rows = $wpdb->get_results("SELECT id, title, slug, category, content FROM $table", ARRAY_A);
foreach ($rows as $row) {
    echo "========================================\n";
    echo "ID: {$row['id']} | Slug: {$row['slug']} | Title: {$row['title']} | Category: {$row['category']}\n";
    echo "----------------------------------------\n";
    echo substr($row['content'], 0, 500) . (strlen($row['content']) > 500 ? '...' : '') . "\n";
    echo "========================================\n\n";
}
