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

        all_rules = await page.evaluate("""() => {
            const list = [];
            for (let i = 0; i < document.styleSheets.length; i++) {
                const sheet = document.styleSheets[i];
                let href = sheet.href || 'inline-style-' + i;
                try {
                    for (let rule of sheet.cssRules) {
                        const checkRule = (r) => {
                            if (r.selectorText) {
                                const sel = r.selectorText.trim();
                                if (sel === 'html' || sel === 'body' || sel === 'html, body' || sel === 'body, html' || sel.startsWith('html') || sel.startsWith('body')) {
                                    list.push({
                                        sheet: href,
                                        selector: sel,
                                        cssText: r.cssText
                                    });
                                }
                            }
                            if (r.cssRules) {
                                for (let sub of r.cssRules) {
                                    checkRule(sub);
                                }
                            }
                        };
                        checkRule(rule);
                    }
                } catch(e) {}
            }
            return list;
        }""")

        import pprint
        for r in all_rules:
            if 'overflow' in r['cssText']:
                print(r['sheet'], "-->", r['cssText'])

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
