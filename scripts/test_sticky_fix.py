import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        device = p.devices['iPhone 14']
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(**device)
        page = await context.new_page()

        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(2000)

        await page.goto("http://cora.local/workspace/forms")
        await page.wait_for_timeout(2000)

        # Apply overflow-x: clip to html and body, and check if sticky works
        await page.evaluate("""() => {
            document.documentElement.style.overflowX = 'clip';
            document.documentElement.style.overflowY = 'visible';
            document.body.style.overflowX = 'clip';
            document.body.style.overflowY = 'visible';
        }""")

        # Check before scroll
        before = await page.evaluate("""() => {
            const topbar = document.getElementById('cora-global-topbar');
            const tabs = document.getElementById('cora-forms-tabs');
            return {
                topbar: topbar ? topbar.getBoundingClientRect() : null,
                tabs: tabs ? tabs.getBoundingClientRect() : null,
                scrollY: window.scrollY
            };
        }""")
        print("Before scroll:", before)

        # Scroll down 400px
        await page.mouse.wheel(0, 400)
        await page.wait_for_timeout(500)

        after = await page.evaluate("""() => {
            const topbar = document.getElementById('cora-global-topbar');
            const tabs = document.getElementById('cora-forms-tabs');
            return {
                topbar: topbar ? topbar.getBoundingClientRect() : null,
                tabs: tabs ? tabs.getBoundingClientRect() : null,
                scrollY: window.scrollY
            };
        }""")
        print("After scroll 400px with overflow: clip:", after)

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
