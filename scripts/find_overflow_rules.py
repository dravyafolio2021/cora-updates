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

        rules = await page.evaluate("""() => {
            const results = [];
            for (let sheet of document.styleSheets) {
                try {
                    for (let rule of sheet.cssRules) {
                        if (rule.selectorText && (rule.selectorText.includes('body') || rule.selectorText.includes('html') || rule.selectorText.includes('cora-workspace') || rule.selectorText.includes('cora-main') || rule.selectorText.includes('cora-forms-tabs'))) {
                            if (rule.style && (rule.style.overflow || rule.style.overflowX || rule.style.overflowY || rule.style.position)) {
                                results.push({
                                    selector: rule.selectorText,
                                    media: rule.parentRule && rule.parentRule.conditionText ? rule.parentRule.conditionText : 'none',
                                    overflow: rule.style.overflow,
                                    overflowX: rule.style.overflowX,
                                    overflowY: rule.style.overflowY,
                                    position: rule.style.position,
                                    cssText: rule.cssText
                                });
                            }
                        }
                    }
                } catch(e) {}
            }
            return results;
        }""")

        import pprint
        pprint.pprint(rules)
        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
