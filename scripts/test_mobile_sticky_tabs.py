import asyncio
import os
from playwright.async_api import async_playwright

async def run():
    output_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
    os.makedirs(output_dir, exist_ok=True)

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(viewport={"width": 390, "height": 844}, is_mobile=True)
        page = await context.new_page()

        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(3000)

        await page.goto("http://cora.local/workspace/team-roles")
        await page.wait_for_timeout(2000)

        # Inspect parents and styles of .cora-sub-tabs-container
        inspect_data = await page.evaluate("""() => {
            const tabs = document.querySelector('.cora-sub-tabs-container');
            if (!tabs) return { error: 'No tabs found' };
            
            let el = tabs;
            const chain = [];
            while (el && el !== document.body) {
                const cs = window.getComputedStyle(el);
                chain.push({
                    tag: el.tagName,
                    id: el.id,
                    className: el.className,
                    overflow: cs.overflow,
                    overflowX: cs.overflowX,
                    overflowY: cs.overflowY,
                    position: cs.position,
                    top: cs.top,
                    zIndex: cs.zIndex
                });
                el = el.parentElement;
            }
            return {
                tabsRect: tabs.getBoundingClientRect(),
                chain: chain
            };
        }""")
        print("INSPECT DATA INITIAL:", inspect_data)

        # Scroll 200px
        await page.evaluate("""() => { window.scrollTo(0, 200); }""")
        await page.wait_for_timeout(1000)

        scrolled_data = await page.evaluate("""() => {
            const tabs = document.querySelector('.cora-sub-tabs-container');
            return {
                windowScrollY: window.scrollY,
                docScrollTop: document.documentElement.scrollTop,
                bodyScrollTop: document.body.scrollTop,
                tabsRect: tabs ? tabs.getBoundingClientRect() : null
            };
        }""")
        print("SCROLLED DATA:", scrolled_data)

        await page.screenshot(path=f"{output_dir}/tabs_team_mobile_scroll_debug.png")
        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
