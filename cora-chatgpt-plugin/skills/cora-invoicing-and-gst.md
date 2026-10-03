# Cora Invoicing & GST Tax Skill

## Purpose
Enforces statutory compliance for Indian GST invoicing (CGST, SGST, IGST) and quotation generation across creative and commercial projects.

## Execution Rules
1. **GST Calculation Verification**:
   - For intra-state transactions (client in same state): Apply CGST (half of total GST rate) + SGST (half of total GST rate).
   - For inter-state transactions (`is_interstate = true`): Apply IGST (full GST rate).
   - Use `cora_calculate_gst` to verify rounding, tax breakdown, and gross total before generating the final invoice.

2. **Invoice Generation (`cora_create_invoice`)**:
   - Provide itemized line items with description, quantity, and rate.
   - Include due date and terms/bank payment instructions.
