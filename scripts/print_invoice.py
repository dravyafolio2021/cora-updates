#!/usr/bin/env python3
"""
Cora Platform — Professional Invoice CLI & System Printer Trigger
Usage:
    python3 scripts/print_invoice.py --latest
    python3 scripts/print_invoice.py --sale-id 1
    python3 scripts/print_invoice.py --invoice-no INV-2026-8801
    python3 scripts/print_invoice.py --list
"""

import argparse
import subprocess
import json
import sys
import os
import shutil

PHP_BIN = "/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php"
if not os.path.exists(PHP_BIN):
    PHP_BIN = shutil.which("php") or "php"

def run_php_code(code: str):
    cmd = [PHP_BIN, "-r", code]
    res = subprocess.run(cmd, capture_output=True, text=True, cwd=os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    if res.returncode != 0:
        print(f"Error running PHP: {res.stderr}", file=sys.stderr)
        return None
    try:
        return json.loads(res.stdout)
    except Exception as e:
        print(f"Raw Output: {res.stdout}")
        return res.stdout

def list_invoices():
    code = """
    require 'app/public/wp-load.php';
    global $wpdb;
    $table = $wpdb->prefix . 'cora_inventory_sales';
    $results = $wpdb->get_results("SELECT id, invoice_no, customer_name, phone, grand_total, payment_mode, payment_status, created_at FROM {$table} ORDER BY id DESC LIMIT 15", ARRAY_A);
    echo json_encode($results);
    """
    data = run_php_code(code)
    if not data or not isinstance(data, list):
        print("No invoices found.")
        return

    print("=" * 80)
    print(f"{'ID':<4} {'Invoice No':<16} {'Customer':<26} {'Amount (₹)':<12} {'Mode':<8} {'Status':<8}")
    print("=" * 80)
    for inv in data:
        amt = f"₹{float(inv.get('grand_total', 0)):,.2f}"
        print(f"{inv['id']:<4} {inv['invoice_no']:<16} {inv.get('customer_name',''):<26} {amt:<12} {inv.get('payment_mode',''):<8} {inv.get('payment_status',''):<8}")
    print("=" * 80)

def print_invoice(sale_id=None, invoice_no=None, latest=False, trigger_system_printer=True):
    where_clause = ""
    if sale_id:
        where_clause = f"$where = 'WHERE id = {int(sale_id)}';"
    elif invoice_no:
        where_clause = f"$where = $wpdb->prepare('WHERE invoice_no = %s', '{invoice_no}');"
    else:
        where_clause = "$where = 'ORDER BY id DESC LIMIT 1';"

    code = f"""
    require 'app/public/wp-load.php';
    global $wpdb;
    $table_s = $wpdb->prefix . 'cora_inventory_sales';
    $table_si = $wpdb->prefix . 'cora_inventory_sales_items';
    {where_clause}
    $sale = $wpdb->get_row("SELECT * FROM {{$table_s}} {{$where}}", ARRAY_A);
    if (!$sale) {{
        echo json_encode(['error' => 'Invoice not found']);
        exit;
    }}
    $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM {{$table_si}} WHERE sale_id = %d", $sale['id']), ARRAY_A);
    $sale['items'] = $items;
    echo json_encode($sale);
    """
    
    sale = run_php_code(code)
    if not sale or "error" in sale:
        print(f"Error: {sale.get('error') if isinstance(sale, dict) else 'Failed to load invoice'}")
        return

    print("\n" + "=" * 70)
    print("                CORA STATIONERY MANUFACTURING & LOGISTICS")
    print("                     OFFICIAL GST TAX INVOICE")
    print("=" * 70)
    print(f" Invoice No  : {sale['invoice_no']}")
    print(f" Date        : {sale.get('sale_date') or sale.get('created_at')}")
    print(f" Customer    : {sale.get('customer_name')}")
    print(f" Phone       : {sale.get('phone') or 'N/A'}")
    print(f" GSTIN       : {sale.get('gstin') or 'URP / Consumer'}")
    print(f" Payment     : {str(sale.get('payment_mode')).upper()} ({str(sale.get('payment_status')).upper()})")
    print("-" * 70)
    print(f"{'#':<3} {'Item Description':<34} {'Qty/Wgt':<10} {'Rate (₹)':<10} {'Total (₹)':<10}")
    print("-" * 70)

    items = sale.get('items', [])
    if items:
        for idx, it in enumerate(items, 1):
            is_wgt = (it.get('pricing_type') == 'weight_based') or (float(it.get('weight_kg') or 0) > 0)
            qty_str = f"{float(it['weight_kg']):.2f} kg" if is_wgt else f"{it['quantity']} Pcs"
            rate_str = f"₹{float(it.get('weight_rate') or 401.25):.2f}/kg" if is_wgt else f"₹{float(it['unit_price']):.2f}"
            tot_str = f"₹{float(it['line_total']):.2f}"
            print(f"{idx:<3} {it['product_name'][:33]:<34} {qty_str:<10} {rate_str:<10} {tot_str:<10}")
    else:
        print(f"1   Stationery Field Supply Lot            1 Lot      ₹{float(sale['subtotal']):.2f}   ₹{float(sale['subtotal']):.2f}")

    print("-" * 70)
    print(f" Taxable Subtotal : ₹{float(sale.get('subtotal', 0)):,.2f}")
    print(f" CGST (9%)        : ₹{float(sale.get('tax_amount', 0))/2:,.2f}")
    print(f" SGST (9%)        : ₹{float(sale.get('tax_amount', 0))/2:,.2f}")
    print(f" GRAND TOTAL      : ₹{float(sale.get('grand_total', 0)):,.2f}")
    print(f" Amount Paid      : ₹{float(sale.get('paid_amount', sale.get('grand_total', 0))):,.2f}")
    print("=" * 70)

    # Check connected system printers via CUPS/lpr
    if trigger_system_printer:
        try:
            printers_res = subprocess.run(["lpstat", "-p"], capture_output=True, text=True)
            if printers_res.returncode == 0 and printers_res.stdout.strip():
                print("\n[System Printer Detected]")
                print(printers_res.stdout.strip())
            else:
                print("\n[Note: No physical CUPS printer currently attached. Printable URL prepared.]")
        except Exception:
            pass

    invoice_url = f"http://cora.local/wp-admin/admin-ajax.php?action=cora_inventory_render_sale_invoice&sale_id={sale['id']}&autoprint=1"
    print(f"\nPrintable Invoice Direct URL:\n👉 {invoice_url}\n")

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Cora Invoice CLI & Printer Trigger")
    parser.add_argument("--sale-id", type=int, help="Sale ID")
    parser.add_argument("--invoice-no", type=str, help="Invoice Number (e.g. INV-2026-8801)")
    parser.add_argument("--latest", action="store_true", help="Print latest invoice")
    parser.add_argument("--list", action="store_true", help="List recent invoices")

    args = parser.parse_args()

    if args.list:
        list_invoices()
    elif args.sale_id or args.invoice_no or args.latest:
        print_invoice(sale_id=args.sale_id, invoice_no=args.invoice_no, latest=args.latest)
    else:
        # Default to latest invoice
        print_invoice(latest=True)
