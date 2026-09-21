import asyncio
from playwright.async_api import async_playwright

async def main():
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

        print("2. Navigating to team-roles...")
        await page.goto("http://cora.local/workspace/team-roles")
        await page.wait_for_timeout(2000)

        # Inspect computed styles of .cora-sub-tabs-container
        tab_info = await page.evaluate("""() => {
            const tabs = document.querySelector('.cora-sub-tabs-container') || document.querySelector('.cora-sticky-sub-tabs');
            if (!tabs) return null;
            const cs = window.getComputedStyle(tabs);
            const children = Array.from(tabs.children).map(c => ({
                text: c.innerText.trim(),
                width: c.offsetWidth,
                tag: c.tagName
            }));
            return {
                scrollWidth: tabs.scrollWidth,
                clientWidth: tabs.clientWidth,
                canScrollHorizontally: tabs.scrollWidth > tabs.clientWidth,
                overflowX: cs.overflowX,
                overflowY: cs.overflowY,
                touchAction: cs.touchAction,
                display: cs.display,
                flexWrap: cs.flexWrap,
                children: children
            };
        }""")
        print("Tab container info:", tab_info)

        # Test scrolling right on the tabs
        print("3. Testing scrollLeft on tabs...")
        scroll_result = await page.evaluate("""() => {
            const tabs = document.querySelector('.cora-sub-tabs-container');
            if (!tabs) return false;
            const before = tabs.scrollLeft;
            tabs.scrollLeft = 200;
            const after = tabs.scrollLeft;
            return { before, after, scrolled: after > before };
        }""")
        print("Scroll result:", scroll_result)

        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
