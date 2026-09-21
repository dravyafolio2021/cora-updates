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

        # Initial screenshot of tab bar
        await page.screenshot(path=f"{output_dir}/mobile_tabs_initial.png")
        print("Saved mobile_tabs_initial.png")

        # Check tab labels on mobile
        labels = await page.evaluate("""() => {
            const tabs = Array.from(document.querySelectorAll('.cora-sub-tabs-container .cora-sub-tab'));
            return tabs.map(t => ({
                text: t.innerText.trim(),
                box: t.getBoundingClientRect()
            }));
        }""")
        print("Mobile tabs rendered:", [l['text'] for l in labels])

        # Touch swipe simulation
        print("3. Performing touch swipe right-to-left on tabs...")
        tabs_box = await page.locator(".cora-sub-tabs-container").bounding_box()
        if tabs_box:
            start_x = tabs_box['x'] + tabs_box['width'] * 0.8
            start_y = tabs_box['y'] + tabs_box['height'] * 0.5
            end_x = tabs_box['x'] + tabs_box['width'] * 0.1
            end_y = start_y

            # Use touch/mouse drag
            await page.mouse.move(start_x, start_y)
            await page.mouse.down()
            # Move in increments for realistic swipe
            for i in range(10):
                cur_x = start_x + (end_x - start_x) * (i / 10.0)
                await page.mouse.move(cur_x, end_y)
                await asyncio.sleep(0.02)
            await page.mouse.up()
            await page.wait_for_timeout(500)

        scroll_left = await page.evaluate("() => document.querySelector('.cora-sub-tabs-container').scrollLeft")
        print(f"Tabs scrollLeft after swipe: {scroll_left}px")

        # Take screenshot after swipe
        await page.screenshot(path=f"{output_dir}/mobile_tabs_after_swipe.png")
        print("Saved mobile_tabs_after_swipe.png")

        # Test clicking second tab ("Invites")
        print("4. Clicking 'Pending Invitations' / 'Invites' tab...")
        invites_tab = page.locator(".cora-sub-tabs-container button[data-target='tab-pending-invites']")
        await invites_tab.click()
        await page.wait_for_timeout(500)

        # Take screenshot of tab active state
        await page.screenshot(path=f"{output_dir}/mobile_tabs_invites_active.png")
        print("Saved mobile_tabs_invites_active.png")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
