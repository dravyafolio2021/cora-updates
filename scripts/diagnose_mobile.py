import asyncio
import os
from playwright.async_api import async_playwright

async def run():
    output_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
    os.makedirs(output_dir, exist_ok=True)

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        # Mobile iPhone 14
        context = await browser.new_context(viewport={"width": 390, "height": 844}, is_mobile=True, has_touch=True)
        page = await context.new_page()

        print("1. Logging in...")
        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(3000)

        print("2. On Dashboard:", page.url)
        await page.screenshot(path=f"{output_dir}/mobile_diag_dashboard.png")

        # Check touch scrolling on dashboard
        scroll_before = await page.evaluate("() => ({ window: window.scrollY, main: document.querySelector('.cora-main') ? document.querySelector('.cora-main').scrollTop : null })")
        print("Scroll before:", scroll_before)

        # Perform touch drag upwards (scroll down)
        await page.touchscreen.tap(200, 500)
        # Mouse / touch drag
        await page.mouse.move(200, 500)
        await page.mouse.down()
        await page.mouse.move(200, 200, steps=10)
        await page.mouse.up()
        await page.wait_for_timeout(1000)

        scroll_after = await page.evaluate("() => ({ window: window.scrollY, main: document.querySelector('.cora-main') ? document.querySelector('.cora-main').scrollTop : null, doc: document.documentElement.scrollTop, body: document.body.scrollTop })")
        print("Scroll after touch drag:", scroll_after)

        # Check what element is at (200, 500)
        el_at_point = await page.evaluate("""() => {
            const el = document.elementFromPoint(200, 500);
            return {
                tag: el.tagName,
                id: el.id,
                className: el.className,
                rect: el.getBoundingClientRect()
            };
        }""")
        print("Element at (200, 500):", el_at_point)

        # Test bottom hamburger navigation
        print("3. Testing bottom nav hamburger...")
        hamburger = page.locator("#cora-mobile-nav-trigger, .cora-mobile-nav-toggle, button:has(svg line[x1='3'])").first
        if await hamburger.count() > 0:
            print("Found hamburger button! Clicking...")
            await hamburger.click()
            await page.wait_for_timeout(1000)
            await page.screenshot(path=f"{output_dir}/mobile_diag_hamburger_clicked.png")
        else:
            print("No hamburger button found with selector, checking elements in bottom bar...")
            bottom_bar_info = await page.evaluate("""() => {
                const b = document.querySelector('.cora-mobile-bottom-bar, #cora-mobile-island, .cora-bottom-island, [class*=\"bottom-bar\"], [class*=\"island\"]');
                return b ? { html: b.outerHTML, className: b.className } : 'No bottom bar found';
            }""")
            print("Bottom bar:", bottom_bar_info)

        # Navigate to /workspace/forms
        print("4. Navigating to forms...")
        await page.goto("http://cora.local/workspace/forms")
        await page.wait_for_timeout(2000)
        await page.screenshot(path=f"{output_dir}/mobile_diag_forms.png")

        # Try touch scrolling on forms
        await page.mouse.move(200, 500)
        await page.mouse.down()
        await page.mouse.move(200, 100, steps=10)
        await page.mouse.up()
        await page.wait_for_timeout(1000)

        forms_scroll = await page.evaluate("() => ({ window: window.scrollY, doc: document.documentElement.scrollTop, body: document.body.scrollTop, main: document.querySelector('.cora-main') ? document.querySelector('.cora-main').scrollTop : null })")
        print("Forms scroll after drag:", forms_scroll)

        # Try clicking a tab button on forms
        print("5. Clicking tab button on forms...")
        tab_btn = page.locator(".cora-sub-tab:has-text('Funnel Analytics')").first
        if await tab_btn.count() > 0:
            await tab_btn.click()
            await page.wait_for_timeout(1000)
            print("Clicked Funnel Analytics tab!")
            await page.screenshot(path=f"{output_dir}/mobile_diag_forms_tab_clicked.png")

        await browser.close()
        print("Diagnostic test completed!")

if __name__ == "__main__":
    asyncio.run(run())
