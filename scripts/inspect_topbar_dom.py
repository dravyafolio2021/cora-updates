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

        topbar_diag = await page.evaluate("""() => {
            const topbar = document.getElementById('cora-global-topbar');
            const res = {
                topbarHtml: topbar.outerHTML.slice(0, 300),
                ancestors: []
            };

            let curr = topbar.parentElement;
            while (curr) {
                const cs = window.getComputedStyle(curr);
                res.ancestors.push({
                    tag: curr.tagName,
                    id: curr.id,
                    className: curr.className.toString().slice(0, 50),
                    overflow: cs.overflow,
                    overflowX: cs.overflowX,
                    overflowY: cs.overflowY,
                    position: cs.position,
                    height: cs.height,
                    transform: cs.transform,
                    contain: cs.contain,
                    filter: cs.filter,
                    perspective: cs.perspective
                });
                curr = curr.parentElement;
            }
            return res;
        }""")

        import pprint
        pprint.pprint(topbar_diag)

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
