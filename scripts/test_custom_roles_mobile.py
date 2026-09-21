import asyncio
import os
from playwright.async_api import async_playwright

async def main():
    output_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
    os.makedirs(output_dir, exist_ok=True)

    async with async_playwright() as p:
        # 1. Test on Mobile Viewport (390x844 iPhone 12/13/14)
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(
            viewport={'width': 390, 'height': 844},
            user_agent='Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
            is_mobile=True,
            has_touch=True
        )
        page = await context.new_page()

        print("1. Logging into local test environment at http://cora.local/workspace/login (Mobile)...")
        await page.goto("http://cora.local/workspace/login", timeout=15000)
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(2000)

        print("\n2. Navigating to team/users view on mobile...")
        await page.goto("http://cora.local/workspace/team-roles")
        await page.wait_for_timeout(2000)
        print("Current page URL:", page.url)

        # Switch to Custom Roles tab
        print("\n3. Switching to Custom Roles tab...")
        custom_roles_tab = page.locator("button[data-tab='tab-custom-roles'], #cora-team-tab-custom-roles, button:has-text('Custom Roles')")
        if await custom_roles_tab.count() > 0:
            await custom_roles_tab.first.click()
            await page.wait_for_timeout(1000)
            print("Clicked Custom Roles tab.")

        # Check Active Custom Roles Overview on Mobile for horizontal overflow
        print("\n4. Checking Mobile Custom Roles layout & horizontal scroll...")
        overflow_check = await page.evaluate('''() => {
            const el = document.querySelector('#tab-custom-roles');
            const cardFeed = document.querySelector('#tab-custom-roles .block.md\\\\:hidden');
            const body = document.body;
            return {
                windowWidth: window.innerWidth,
                bodyScrollWidth: document.documentElement.scrollWidth,
                hasWindowHScroll: document.documentElement.scrollWidth > window.innerWidth,
                tabScrollWidth: el ? el.scrollWidth : 0,
                tabClientWidth: el ? el.clientWidth : 0,
                cardFeedPresent: !!cardFeed,
                cardsCount: cardFeed ? cardFeed.querySelectorAll('.border.rounded-xl').length : 0
            };
        }''')
        print(f"Mobile Overflow Check: {overflow_check}")

        screenshot_path = f"{output_dir}/mobile_custom_roles_overview.png"
        await page.screenshot(path=screenshot_path)
        print(f"Saved screenshot: {screenshot_path}")

        # 5. Open Create Custom Role drawer
        print("\n5. Opening 'Define Custom Role' drawer on mobile...")
        create_role_btn = page.locator("button:has-text('Create Custom Role'), button[onclick*='openCreateCustomRoleDrawer']").first
        if await create_role_btn.count() > 0:
            await create_role_btn.click()
            await page.wait_for_timeout(1000)

            # Check drawer visibility and capabilities
            drawer_info = await page.evaluate('''() => {
                const drawer = document.querySelector('#cora-create-custom-role-drawer');
                const scrollable = drawer ? drawer.querySelector('.overflow-y-auto') : null;
                const submitBtn = document.querySelector('#create-role-submit-btn');
                const caps = drawer ? drawer.querySelectorAll('.cora-perm-card').length : 0;
                return {
                    drawerVisible: drawer ? getComputedStyle(drawer).display !== 'none' && !drawer.classList.contains('hidden') : false,
                    drawerHeight: drawer ? drawer.clientHeight : 0,
                    scrollablePresent: !!scrollable,
                    scrollHeight: scrollable ? scrollable.scrollHeight : 0,
                    clientHeight: scrollable ? scrollable.clientHeight : 0,
                    canScroll: scrollable ? scrollable.scrollHeight > scrollable.clientHeight : false,
                    capabilitiesCount: caps,
                    submitBtnVisible: submitBtn ? submitBtn.offsetParent !== null : false
                };
            }''')
            print(f"Create Role Drawer Info: {drawer_info}")

            # Take screenshot of top of drawer
            await page.screenshot(path=f"{output_dir}/mobile_create_role_drawer_top.png")

            # Scroll the drawer body down to test scrolling
            print("\n6. Testing vertical scrolling inside create role drawer on mobile...")
            await page.evaluate('''() => {
                const drawer = document.querySelector('#cora-create-custom-role-drawer');
                const scrollable = drawer ? drawer.querySelector('.overflow-y-auto') : null;
                if (scrollable) {
                    scrollable.scrollTop = scrollable.scrollHeight;
                }
            }''')
            await page.wait_for_timeout(500)

            # Check if submit button is visible & sticky footer intact
            submit_visible = await page.locator("#create-role-submit-btn").is_visible()
            print(f"Submit button visible after scroll: {submit_visible}")
            await page.screenshot(path=f"{output_dir}/mobile_create_role_drawer_scrolled.png")

            # Close drawer
            await page.locator("#cora-create-custom-role-drawer button[onclick*='closeCreateCustomRoleDrawer']").first.click()
            await page.wait_for_timeout(500)

        # 6. Test Delete Role Modal
        print("\n7. Testing Delete Custom Role modal...")
        delete_btns = page.locator("button[onclick*='promptDeleteCustomRole']")
        del_count = await delete_btns.count()
        print(f"Found {del_count} delete role buttons.")

        if del_count > 0:
            await delete_btns.first.click()
            await page.wait_for_timeout(500)

            modal_info = await page.evaluate('''() => {
                const modal = document.querySelector('#cora-delete-custom-role-modal');
                const name = document.querySelector('#cora-delete-role-target-name');
                const key = document.querySelector('#cora-delete-role-target-key');
                return {
                    modalVisible: modal ? getComputedStyle(modal).display !== 'none' && !modal.classList.contains('hidden') : false,
                    targetName: name ? name.textContent : '',
                    targetKey: key ? key.textContent : ''
                };
            }''')
            print(f"Delete Role Modal Info: {modal_info}")
            await page.screenshot(path=f"{output_dir}/mobile_delete_role_modal.png")

            # Close modal
            await page.locator("#cora-delete-custom-role-modal button[onclick*='closeDeleteCustomRoleModal']").click()
            await page.wait_for_timeout(500)

        print("\nAll mobile custom roles verification checks completed successfully!")
        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
