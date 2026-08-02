import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Custom Roles & Dynamic Permissions E2E Tests', () => {

  test.beforeEach(async ({ page }) => {
    page.on('console', msg => {
      if (msg.type() === 'error') {
        console.log(`PAGE CONSOLE ERROR: "${msg.text()}"`);
      }
    });
    page.on('pageerror', err => {
      console.log(`PAGE UNHANDLED EXCEPTION: "${err.message}"`);
    });
  });

  test('Create, Edit, Duplicate, and Delete Custom Role in Real Estate Workspace', async ({ page }) => {
    await login(page);

    const rand = Math.floor(Math.random() * 100000);
    const roleName = `E2E RE Agent Role ${rand}`;
    const roleNameModified = `E2E RE Agent Role Modified ${rand}`;
    const copyRoleName = `Copy of E2E RE Agent Role Modified ${rand}`;

    // 1. Navigate to users workspace under Real Estate industry
    await page.goto('/workspace/team-roles?industry=real_estate');
    await page.waitForLoadState('networkidle');

    // 2. Select the Custom Roles tab button
    const customRolesTabBtn = page.locator('button[data-target="tab-custom-roles"]').first();
    await expect(customRolesTabBtn).toBeVisible();
    await customRolesTabBtn.click();

    // 3. Verify the Custom Roles panel is shown
    await expect(page.locator('#tab-custom-roles')).toBeVisible();

    // 4. Click "+ Create Custom Role" button to open the Define Custom Role drawer
    const createRoleBtn = page.locator('button:has-text("Create Custom Role")');
    await expect(createRoleBtn).toBeVisible();
    await createRoleBtn.click();

    // 5. Verify the create drawer is open
    const createDrawer = page.locator('#cora-create-custom-role-drawer');
    await expect(createDrawer).toBeVisible();

    // 6. Verify dynamic features labels for Real Estate in the checkboxes:
    // "Property Listings" (equipment), "Showings & Bookings" (showings_bookings)
    const propertyListingsLabel = createDrawer.locator('label:has-text("Property Listings")');
    const showingsBookingsLabel = createDrawer.locator('label:has-text("Showings & Bookings")');
    await expect(propertyListingsLabel).toBeVisible();
    await expect(showingsBookingsLabel).toBeVisible();

    // Verify quota label is "Max Showing/Listing Quota (Monthly)"
    const quotaLabel = createDrawer.locator('label:has-text("Max Showing/Listing Quota (Monthly)")');
    await expect(quotaLabel).toBeVisible();

    // 7. Fill the Create Custom Role form
    const roleNameInput = createDrawer.locator('#custom-role-name');
    await roleNameInput.fill(roleName);

    // Select contributor base template
    await createDrawer.locator('#custom-role-base-template').selectOption('cora_re_agent');

    // Set Max Quota
    const maxQuotaInput = createDrawer.locator('#custom-role-max-quota');
    await maxQuotaInput.fill('15');

    // Ensure Property Listings checkbox is checked (it's mapped to value="equipment")
    const listingsCheckbox = createDrawer.locator('input[value="equipment"]');
    await listingsCheckbox.check();

    // Ensure Showings & Bookings checkbox is checked (value="showings_bookings")
    const showingsCheckbox = createDrawer.locator('input[value="showings_bookings"]');
    await showingsCheckbox.check();

    // Submit Create Custom Role form
    const submitCreateBtn = createDrawer.locator('#create-role-submit-btn');
    await submitCreateBtn.click();

    // 8. Verify the role was created successfully (toast check and table reload)
    const toast = page.locator('#cora-toast-container');
    await expect(toast).toContainText('Custom role created successfully');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Verify roleName is visible in the overview table
    const tableBody = page.locator('#tab-custom-roles table tbody');
    await expect(tableBody).toContainText(roleName);

    // Verify that feature permission tags display "Property Listings" and "Showings"
    const row = tableBody.locator(`tr:has-text("${roleName}")`);
    await expect(row.locator('span:has-text("Property Listings")')).toBeVisible();
    await expect(row.locator('span:has-text("Showings")')).toBeVisible();

    // 9. Edit the custom role
    const editBtn = row.locator('button.cora-edit-custom-role-btn');
    await expect(editBtn).toBeVisible();
    await editBtn.click();

    // Verify the edit drawer is open
    const editDrawer = page.locator('#cora-edit-custom-role-drawer');
    await expect(editDrawer).toBeVisible();

    // Verify edit drawer also has dynamic label "Property Listings"
    await expect(editDrawer.locator('label:has-text("Property Listings")')).toBeVisible();

    // Change Name
    const editNameInput = editDrawer.locator('#edit-custom-role-name');
    await editNameInput.fill(roleNameModified);

    // Submit Edit Custom Role form
    const submitEditBtn = editDrawer.locator('#save-custom-role-btn');
    await submitEditBtn.click();

    // Verify update success
    await expect(toast).toContainText('Custom role permissions updated successfully');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Verify updated row exists in the overview table
    const updatedRow = tableBody.locator(`tr:has-text("${roleNameModified}")`);
    await expect(updatedRow).toBeVisible();

    // 10. Duplicate the custom role
    const duplicateBtn = updatedRow.locator('button.cora-duplicate-custom-role-btn');
    await expect(duplicateBtn).toBeVisible();
    await duplicateBtn.click();

    // Verify duplicate success
    await expect(toast).toContainText('Custom role duplicated successfully');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Verify the duplicated role exists in the overview table
    const duplicatedRow = tableBody.locator(`tr:has-text("${copyRoleName}")`);
    await expect(duplicatedRow).toBeVisible();

    // 11. Delete both the copy and the original custom roles
    // First, delete the copy
    const deleteCopyBtn = duplicatedRow.locator('button[title="Delete Role"]');
    await deleteCopyBtn.click();
    // Wait for the button text to transition to "Confirm Delete"
    await expect(deleteCopyBtn).toHaveText('Confirm Delete');
    // Click again to confirm delete
    await deleteCopyBtn.click();

    // Verify deletion toast
    await expect(toast).toContainText('Custom role deleted successfully');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Verify copy is removed from table
    await expect(tableBody.locator(`tr:has-text("${copyRoleName}")`)).toBeHidden();

    // Next, delete the original modified role
    const deleteOrigBtn = tableBody.locator(`tr:has-text("${roleNameModified}") button[title="Delete Role"]`);
    await deleteOrigBtn.click();
    await expect(deleteOrigBtn).toHaveText('Confirm Delete');
    await deleteOrigBtn.click();

    // Verify deletion toast
    await expect(toast).toContainText('Custom role deleted successfully');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Verify original is removed from table
    await expect(tableBody.locator(`tr:has-text("${roleNameModified}")`)).toBeHidden();
  });

  test('Verify Dynamic Feature Labels in Photography Studio Workspace', async ({ page }) => {
    await login(page);

    // 1. Navigate to users workspace under Photography Studio industry
    await page.goto('/workspace/team-roles?industry=photography_studio');
    await page.waitForLoadState('networkidle');

    // 2. Select the Custom Roles tab button
    const customRolesTabBtn = page.locator('button[data-target="tab-custom-roles"]').first();
    await expect(customRolesTabBtn).toBeVisible();
    await customRolesTabBtn.click();

    // 3. Click "+ Create Custom Role" button to open the Define Custom Role drawer
    const createRoleBtn = page.locator('button:has-text("Create Custom Role")');
    await expect(createRoleBtn).toBeVisible();
    await createRoleBtn.click();

    // 4. Verify the create drawer is open
    const createDrawer = page.locator('#cora-create-custom-role-drawer');
    await expect(createDrawer).toBeVisible();

    // 5. Verify dynamic features labels for Photography Studio in the checkboxes:
    // "Camera Equipment" (equipment), "Shoots & Bookings" (showings_bookings)
    const cameraEquipmentLabel = createDrawer.locator('label:has-text("Camera Equipment")');
    const shootsBookingsLabel = createDrawer.locator('label:has-text("Shoots & Bookings")');
    await expect(cameraEquipmentLabel).toBeVisible();
    await expect(shootsBookingsLabel).toBeVisible();

    // Verify quota label is "Max Shoot/Booking Quota (Monthly)"
    const quotaLabel = createDrawer.locator('label:has-text("Max Shoot/Booking Quota (Monthly)")');
    await expect(quotaLabel).toBeVisible();

    // Close the drawer
    await createDrawer.locator('button:has-text("Cancel")').click();
    await expect(createDrawer).toBeHidden();
  });

  test('Verify Permissions Matrix Save E2E', async ({ page }) => {
    await login(page);

    // 1. Navigate to team-roles page
    await page.goto('/workspace/team-roles?industry=real_estate');
    await page.waitForLoadState('networkidle');

    // 2. Click the Permissions Matrix tab
    const permissionsMatrixTabBtn = page.locator('button[data-target="tab-permissions-matrix"]').first();
    await expect(permissionsMatrixTabBtn).toBeVisible();
    await permissionsMatrixTabBtn.click();

    // 3. Verify Permissions Matrix container is visible
    await expect(page.locator('#tab-permissions-matrix')).toBeVisible();

    // 4. Find the first checkbox for a regular role (not Super Admin or Administrator)
    const checkbox = page.locator('#tab-permissions-matrix tbody tr:not([data-role="administrator"]):not([data-role="cora_super_admin"]) input[type="checkbox"]').first();
    await expect(checkbox).toBeVisible();
    
    // 5. Toggle the checkbox to trigger the autosave
    const isCheckedBefore = await checkbox.isChecked();
    if (isCheckedBefore) {
      await checkbox.uncheck();
    } else {
      await checkbox.check();
    }

    // 6. Verify toast feedback
    const toast = page.locator('#cora-toast-container');
    await expect(toast).toContainText('Permissions matrix saved successfully');
  });

});
