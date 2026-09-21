import asyncio
import os
from playwright.async_api import async_playwright

async def main():
    output_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
    os.makedirs(output_dir, exist_ok=True)

    async with async_playwright() as p:
        device = p.devices['iPhone 14']
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(**device)
        page = await context.new_page()

        print("1. Logging into mobile...")
        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(2000)

        print("2. Navigating to team-roles on mobile...")
        await page.goto("http://cora.local/workspace/team-roles")
        await page.wait_for_timeout(2000)

        # Screenshot initial
        await page.screenshot(path=f"{output_dir}/mobile_team_tabs_initial.png")
        print("Saved mobile_team_tabs_initial.png")

        # Check sub-tab buttons
        tabs_data = await page.evaluate("""() => {
            const container = document.querySelector('.cora-sub-tabs-container');
            const tabs = Array.from(container.querySelectorAll('.cora-sub-tab'));
            return {
                scrollWidth: container.scrollWidth,
                clientWidth: container.clientWidth,
                tabs: tabs.map(t => ({
                    text: t.innerText.trim(),
                    target: t.getAttribute('data-target'),
                    active: t.classList.contains('active'),
                    offsetLeft: t.offsetLeft,
                    offsetWidth: t.offsetWidth
                }))
            };
        }""")
        print(f"Tabs scrollWidth: {tabs_data['scrollWidth']}, clientWidth: {tabs_data['clientWidth']}")
        for t in tabs_data['tabs']:
            print(f"  - {t['text']} (target={t['target']}, active={t['active']}, offsetLeft={t['offsetLeft']})")

        # Click on 'Permissions Matrix' tab (3rd tab)
        print("\n3. Clicking 'Permissions Matrix' tab...")
        await page.click(".cora-sub-tab[data-target='tab-permissions-matrix']")
        await page.wait_for_timeout(800)

        # Check scrollLeft after clicking
        scroll_left = await page.evaluate("() => document.querySelector('.cora-sub-tabs-container').scrollLeft")
        print(f"Container scrollLeft after clicking 3rd tab: {scroll_left}px")
        await page.screenshot(path=f"{output_dir}/mobile_team_tabs_matrix.png")
        print("Saved mobile_team_tabs_matrix.png")

        # Click on 'Custom Roles' tab (6th tab, offscreen on mobile)
        print("\n4. Clicking 'Custom Roles' tab...")
        await page.click(".cora-sub-tab[data-target='tab-custom-roles']")
        await page.wait_for_timeout(800)

        scroll_left2 = await page.evaluate("() => document.querySelector('.cora-sub-tabs-container').scrollLeft")
        print(f"Container scrollLeft after clicking 6th tab: {scroll_left2}px")
        await page.screenshot(path=f"{output_dir}/mobile_team_tabs_custom_roles.png")
        print("Saved mobile_team_tabs_custom_roles.png")

        # Click back to 'Members' (1st tab)
        print("\n5. Clicking back to 'Members' tab...")
        await page.click(".cora-sub-tab[data-target='tab-active-members']")
        await page.wait_for_timeout(800)

        scroll_left3 = await page.evaluate("() => document.querySelector('.cora-sub-tabs-container').scrollLeft")
        print(f"Container scrollLeft after clicking 1st tab: {scroll_left3}px")
        await page.screenshot(path=f"{output_dir}/mobile_team_tabs_members.png")
        print("Saved mobile_team_tabs_members.png")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
