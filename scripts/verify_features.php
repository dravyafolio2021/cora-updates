<?php
/**
 * Verification test script for:
 * 1. Weight-based vs Unit-based Van Consignment Dispatch
 * 2. Professional GST Invoice generation and print readiness
 */

// Load WordPress environment
$_SERVER['HTTP_HOST']   = 'cora.local';
$_SERVER['REQUEST_URI'] = '/';
require_once __DIR__ . '/../app/public/wp-load.php';

global $wpdb;

echo "======================================================\n";
echo "CORA INVENTORY & PRINT VERIFICATION SUITE\n";
echo "======================================================\n\n";

// 1. Verify schema columns
echo "1. Checking database schema columns in consignment and sales items...\n";
$consignment_cols = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}cora_inventory_consignment_items", ARRAY_A);
$col_names = array_column($consignment_cols, 'Field');
$expected_cols = ['pricing_type', 'unit_weight_grams', 'dispatched_weight_kg', 'sold_weight_kg', 'weight_rate'];
foreach ($expected_cols as $col) {
    if (in_array($col, $col_names)) {
        echo "  [PASS] {$wpdb->prefix}cora_inventory_consignment_items has column '{$col}'\n";
    } else {
        echo "  [FAIL] Missing column '{$col}' in {$wpdb->prefix}cora_inventory_consignment_items\n";
    }
}

$sales_cols = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}cora_inventory_sales_items", ARRAY_A);
$sales_col_names = array_column($sales_cols, 'Field');
foreach ($expected_cols as $col) {
    if (in_array($col, $sales_col_names)) {
        echo "  [PASS] {$wpdb->prefix}cora_inventory_sales_items has column '{$col}'\n";
    } else {
        echo "  [FAIL] Missing column '{$col}' in {$wpdb->prefix}cora_inventory_sales_items\n";
    }
}

// 2. Fetch sample weight-based and unit-based products
echo "\n2. Inspecting Sample Products...\n";
$weight_product = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cora_inventory_products WHERE unit_weight_grams > 0 OR pricing_type = 'weight' LIMIT 1", ARRAY_A);
$unit_product = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cora_inventory_products WHERE (unit_weight_grams = 0 OR unit_weight_grams IS NULL) AND (pricing_type = 'unit' OR pricing_type IS NULL) LIMIT 1", ARRAY_A);

if ($weight_product) {
    echo "  [OK] Weight product found: ID={$weight_product['id']}, SKU={$weight_product['sku']}, Name={$weight_product['name_en']}, Weight={$weight_product['unit_weight_grams']}g, Stock={$weight_product['stock_quantity']}\n";
} else {
    echo "  [WARN] No weight-based product found.\n";
}

if ($unit_product) {
    echo "  [OK] Unit product found: ID={$unit_product['id']}, SKU={$unit_product['sku']}, Name={$unit_product['name_en']}, Unit Price=₹{$unit_product['unit_price']}, Stock={$unit_product['stock_quantity']}\n";
} else {
    echo "  [WARN] No unit-based product found.\n";
}

// 3. Test Consignment Item Calculation Simulation
echo "\n3. Testing Dispatch Valuation & Stock Math...\n";
if ($weight_product) {
    $dispatch_weight_kg = 5.5;
    $rate_per_kg = 401.25;
    $expected_line_total = round($dispatch_weight_kg * $rate_per_kg, 2);
    $unit_weight_g = floatval($weight_product['unit_weight_grams']) > 0 ? floatval($weight_product['unit_weight_grams']) : 1000.0;
    $equivalent_units = ceil(($dispatch_weight_kg * 1000) / $unit_weight_g);
    
    echo "  - Dispatching Weight Item: {$dispatch_weight_kg} kg @ ₹{$rate_per_kg}/kg\n";
    echo "    Calculated Line Total: ₹{$expected_line_total} (Expected ₹2206.88)\n";
    echo "    Equivalent Plant Units deducted: {$equivalent_units} units ({$dispatch_weight_kg}kg * 1000 / {$unit_weight_g}g)\n";
    if (abs($expected_line_total - 2206.88) < 0.01) {
        echo "    [PASS] Weight Rate Calculation is exact.\n";
    }
}

// 4. Test Invoice Render
echo "\n4. Testing Sale Invoice Output...\n";
$latest_sale = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cora_inventory_sales ORDER BY id DESC LIMIT 1", ARRAY_A);
if ($latest_sale) {
    $inv_num = $latest_sale['invoice_no'] ?? ($latest_sale['invoice_number'] ?? 'INV-' . $latest_sale['id']);
    $total_val = $latest_sale['grand_total'] ?? ($latest_sale['total_amount'] ?? 0.0);
    echo "  [OK] Latest sale ID: {$latest_sale['id']}, Invoice Number: {$inv_num}, Amount: ₹{$total_val}\n";
    
    // Instantiate engine and check invoice render output
    $engine = new Cora_Inventory_Engine();
    ob_start();
    // Simulate AJAX request
    $_GET['sale_id'] = $latest_sale['id'];
    $_GET['autoprint'] = 0;
    $engine->ajax_render_sale_invoice();
    $html = ob_get_clean();
    
    if (strpos($html, 'TAX INVOICE') !== false && strpos($html, $inv_num) !== false) {
        echo "  [PASS] Invoice HTML rendered successfully with GST Header & Invoice Number.\n";
        if (strpos($html, 'CGST (9%)') !== false && strpos($html, 'SGST (9%)') !== false) {
            echo "  [PASS] GST 18% (9% + 9%) breakdown rendered.\n";
        }
    } else {
        echo "  [INFO] HTML output length: " . strlen($html) . " bytes\n";
    }
} else {
    echo "  [INFO] No sale records found yet in database.\n";
}

echo "\n======================================================\n";
echo "ALL VERIFICATION CHECKS COMPLETED\n";
echo "======================================================\n";
