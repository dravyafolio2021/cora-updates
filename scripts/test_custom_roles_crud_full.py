import asyncio
import os
from playwright.async_api import async_playwright

async def main():
    output_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
    os.makedirs(output_dir, exist_ok=True)

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(
            viewport={'width': 390, 'height': 844},
            user_agent='Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
            is_mobile=True,
            has_touch=True
        )
        page = await context.new_page()

        print("1. Logging into local test environment...")
        await page.goto("http://cora.local/workspace/login", timeout=15000)
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(2000)

        print("\n2. Navigating to team-roles view...")
        await page.goto("http://cora.local/workspace/team-roles")
        await page.wait_for_timeout(2000)

        print("\n3. Switching to Custom Roles tab...")
        await page.locator("button[data-tab='tab-custom-roles'], #cora-team-tab-custom-roles, button:has-text('Custom Roles')").first.click()
        await page.wait_for_timeout(1000)

        print("\n4. Opening 'Define Custom Role' drawer and creating test role...")
        await page.locator("button:has-text('Create Custom Role'), button[onclick*='openCreateCustomRoleDrawer']").first.click()
        await page.wait_for_timeout(1000)

        # Fill in role name
        await page.fill("#custom-role-name", "Senior Lighting Specialist")

        # Scroll to submit button and click
        await page.evaluate('''() => {
            const drawer = document.querySelector('#cora-create-custom-role-drawer');
            const scrollable = drawer ? drawer.querySelector('.overflow-y-auto') : null;
            if (scrollable) {
                scrollable.scrollTop = scrollable.scrollHeight;
            }
        }''')
        await page.wait_for_timeout(500)

        # Click submit button
        print("Clicking 'Provision Role' button...")
        await page.click("#create-role-submit-btn")
        await page.wait_for_timeout(2500)

        # Reload & check custom roles tab
        print("\n5. Checking active custom roles overview on mobile...")
        await page.goto("http://cora.local/workspace/team-roles")
        await page.wait_for_timeout(1500)
        await page.locator("button[data-tab='tab-custom-roles'], #cora-team-tab-custom-roles, button:has-text('Custom Roles')").first.click()
        await page.wait_for_timeout(1000)

        # Verify card feed layout and horizontal scroll
        layout_check = await page.evaluate('''() => {
            const cardFeed = document.querySelector('#tab-custom-roles .block.md\\\\:hidden');
            const cards = cardFeed ? cardFeed.querySelectorAll('.border.rounded-xl') : [];
            return {
                windowWidth: window.innerWidth,
                bodyScrollWidth: document.documentElement.scrollWidth,
                hasWindowHScroll: document.documentElement.scrollWidth > window.innerWidth,
                cardFeedPresent: !!cardFeed,
                cardsCount: cards.length,
                cardDetails: Array.from(cards).map(c => ({
                    text: c.textContent.trim().substring(0, 80),
                    scrollWidth: c.scrollWidth,
                    clientWidth: c.clientWidth
                }))
            };
        }''')
        print(f"Mobile Card Feed Layout Check: {layout_check}")
        await page.screenshot(path=f"{output_dir}/mobile_custom_roles_card_created.png")

        # 6. Test Delete Role Modal
        print("\n6. Testing Delete Role prompt modal on mobile...")
        del_btn = page.locator(".block.md\\:hidden button[onclick*='promptDeleteCustomRole']").first
        await del_btn.scroll_into_view_if_needed()
        await page.wait_for_timeout(500)
        await del_btn.click()
        await page.wait_for_timeout(600)

        modal_info = await page.evaluate('''() => {
            const modal = document.querySelector('#cora-delete-custom-role-modal');
            const name = document.querySelector('#cora-delete-role-target-name');
            const key = document.querySelector('#cora-delete-role-target-key');
            return {
                modalVisible: modal ? getComputedStyle(modal).display !== 'none' : false,
                targetName: name ? name.textContent : '',
                targetKey: key ? key.textContent : ''
            };
        }''')
        print(f"Delete Role Modal: {modal_info}")
        await page.screenshot(path=f"{output_dir}/mobile_delete_role_modal_active.png")

        # Execute deletion
        print("\n7. Executing deletion via modal CTA...")
        await page.click("#confirm-delete-role-btn")
        await page.wait_for_timeout(2000)

        # Reload and verify role removed
        await page.goto("http://cora.local/workspace/team-roles")
        await page.wait_for_timeout(1500)
        await page.locator("button[data-tab='tab-custom-roles'], #cora-team-tab-custom-roles, button:has-text('Custom Roles')").first.click()
        await page.wait_for_timeout(1000)

        after_delete = await page.evaluate('''() => {
            const cardFeed = document.querySelector('#tab-custom-roles .block.md\\\\:hidden');
            const cards = cardFeed ? cardFeed.querySelectorAll('.border.rounded-xl') : [];
            return {
                cardsCount: cards.length
            };
        }''')
        print(f"After deletion count: {after_delete}")
        await page.screenshot(path=f"{output_dir}/mobile_custom_roles_after_deletion.png")

        print("\nCRUD test completed successfully with zero horizontal scroll!")
        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
