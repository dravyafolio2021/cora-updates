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

        # Test Forms page
        await page.goto("http://cora.local/workspace/forms")
        await page.wait_for_timeout(2000)

        # Check topbar and tabs geometry before and after scroll
        info = await page.evaluate("""() => {
            const topbar = document.getElementById('cora-global-topbar');
            const tabs = document.getElementById('cora-forms-tabs') || document.querySelector('.cora-sticky-sub-tabs, #cora-content-tabs');
            
            const getElInfo = (el) => {
                if (!el) return null;
                const rect = el.getBoundingClientRect();
                const cs = window.getComputedStyle(el);
                return {
                    id: el.id,
                    className: el.className,
                    position: cs.position,
                    top: cs.top,
                    zIndex: cs.zIndex,
                    rect: { top: rect.top, bottom: rect.bottom, height: rect.height },
                    display: cs.display
                };
            };

            // Check all ancestors of tabs
            const ancestors = [];
            let curr = tabs ? tabs.parentElement : null;
            while (curr && curr !== document.documentElement) {
                const cs = window.getComputedStyle(curr);
                ancestors.push({
                    tag: curr.tagName,
                    id: curr.id,
                    className: curr.className.toString().slice(0, 50),
                    overflow: cs.overflow,
                    overflowX: cs.overflowX,
                    overflowY: cs.overflowY,
                    position: cs.position,
                    height: cs.height,
                    transform: cs.transform
                });
                curr = curr.parentElement;
            }

            return {
                topbar: getElInfo(topbar),
                tabs: getElInfo(tabs),
                ancestors: ancestors
            };
        }""")

        import pprint
        print("--- BEFORE SCROLL ---")
        pprint.pprint(info)

        # Scroll down 400px
        await page.mouse.wheel(0, 400)
        await page.wait_for_timeout(500)

        info_scrolled = await page.evaluate("""() => {
            const topbar = document.getElementById('cora-global-topbar');
            const tabs = document.getElementById('cora-forms-tabs') || document.querySelector('.cora-sticky-sub-tabs, #cora-content-tabs');
            
            const getElInfo = (el) => {
                if (!el) return null;
                const rect = el.getBoundingClientRect();
                const cs = window.getComputedStyle(el);
                return {
                    id: el.id,
                    rect: { top: rect.top, bottom: rect.bottom, height: rect.height },
                    windowScrollY: window.scrollY
                };
            };

            return {
                topbar: getElInfo(topbar),
                tabs: getElInfo(tabs)
            };
        }""")

        print("\n--- AFTER SCROLL 400px ---")
        pprint.pprint(info_scrolled)

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
