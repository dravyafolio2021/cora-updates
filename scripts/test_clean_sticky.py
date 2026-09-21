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

        # Inject CSS to clear all overflow on html/body and set clean sticky
        await page.evaluate("""() => {
            const style = document.createElement('style');
            style.innerHTML = `
                @media (max-width: 1023px) {
                    html, body {
                        overflow: visible !important;
                        overflow-x: clip !important;
                        overflow-y: visible !important;
                        height: auto !important;
                        max-height: none !important;
                    }
                    #cora-workspace {
                        overflow: visible !important;
                        height: auto !important;
                        min-height: 100vh !important;
                    }
                    .cora-main {
                        overflow: visible !important;
                        height: auto !important;
                    }
                    #cora-global-topbar {
                        position: -webkit-sticky !important;
                        position: sticky !important;
                        top: 0 !important;
                        z-index: 50 !important;
                    }
                    #cora-forms-tabs,
                    #cora-content-tabs,
                    .cora-sticky-content-tabs,
                    .cora-sticky-sub-tabs,
                    .cora-sub-tabs-container {
                        position: -webkit-sticky !important;
                        position: sticky !important;
                        top: 53px !important;
                        z-index: 45 !important;
                    }
                }
            `;
            document.head.appendChild(style);
        }""")

        # Before scroll
        b = await page.evaluate("""() => {
            const topbar = document.getElementById('cora-global-topbar');
            const tabs = document.getElementById('cora-forms-tabs');
            return {
                topbar: topbar ? topbar.getBoundingClientRect() : null,
                tabs: tabs ? tabs.getBoundingClientRect() : null,
                scrollY: window.scrollY
            };
        }""")
        print("Before scroll:", b)

        # Scroll down 400px
        await page.mouse.wheel(0, 400)
        await page.wait_for_timeout(500)

        a = await page.evaluate("""() => {
            const topbar = document.getElementById('cora-global-topbar');
            const tabs = document.getElementById('cora-forms-tabs');
            return {
                topbar: topbar ? topbar.getBoundingClientRect() : null,
                tabs: tabs ? tabs.getBoundingClientRect() : null,
                scrollY: window.scrollY
            };
        }""")
        print("After scroll 400px with clean sticky rules:", a)

        await page.screenshot(path="/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output/mobile_sticky_tabs_fixed.png")
        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
