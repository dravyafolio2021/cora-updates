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

        print("2. Navigating to settings-suite on mobile...")
        await page.goto("http://cora.local/workspace/settings-suite")
        await page.wait_for_timeout(2000)
        print(f"Current page URL: {page.url}")

        # Check before scroll geometry
        b_geo = await page.evaluate("""() => {
            const topbar = document.getElementById('cora-global-topbar');
            const tabs = document.getElementById('cora-settings-tabs') || document.querySelector('.cora-sub-tabs-container');
            if (!tabs || !topbar) return null;
            const tRect = topbar.getBoundingClientRect();
            const tabRect = tabs.getBoundingClientRect();
            return {
                topbar: { top: tRect.top, bottom: tRect.bottom, height: tRect.height },
                tabs: { top: tabRect.top, bottom: tabRect.bottom, height: tabRect.height },
                scrollY: window.scrollY
            };
        }""")
        print("Before scroll geometry:", b_geo)
        await page.screenshot(path=f"{output_dir}/mobile_settings_top.png")

        # Scroll down 400px
        print("\n3. Scrolling down 400px...")
        await page.evaluate("() => window.scrollTo(0, 400)")
        await page.wait_for_timeout(800)

        a_geo = await page.evaluate("""() => {
            const topbar = document.getElementById('cora-global-topbar');
            const tabs = document.getElementById('cora-settings-tabs') || document.querySelector('.cora-sub-tabs-container');
            if (!tabs || !topbar) return null;
            const tRect = topbar.getBoundingClientRect();
            const tabRect = tabs.getBoundingClientRect();
            const cs = window.getComputedStyle(tabs);
            return {
                topbar: { top: tRect.top, bottom: tRect.bottom, height: tRect.height },
                tabs: { top: tabRect.top, bottom: tabRect.bottom, height: tabRect.height },
                scrollY: window.scrollY,
                position: cs.position,
                topVal: cs.top,
                isStickyUnderTopbar: Math.abs(tabRect.top - tRect.bottom) <= 2
            };
        }""")
        print("After scroll geometry:", a_geo)
        await page.screenshot(path=f"{output_dir}/mobile_settings_scrolled.png")

        # Test clicking 2nd tab ("Activity Timeline & Logs")
        print("\n4. Clicking 2nd tab...")
        tabs = await page.locator(".cora-settings-nav-mobile").all()
        print(f"Found {len(tabs)} mobile settings tabs.")
        if len(tabs) > 1:
            await tabs[1].click()
            await page.wait_for_timeout(800)
            await page.screenshot(path=f"{output_dir}/mobile_settings_tab2_active.png")
            print("Saved mobile_settings_tab2_active.png")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
