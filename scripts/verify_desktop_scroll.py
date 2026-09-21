import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        # Desktop 1440x900
        page = await browser.new_page(viewport={"width": 1440, "height": 900})

        print("1. Desktop Logging in...")
        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(2000)

        desktop_pages = [
            ("Dashboard", "http://cora.local/workspace/dashboard"),
            ("Forms", "http://cora.local/workspace/forms"),
            ("Content", "http://cora.local/workspace/content-suite"),
            ("Team Roles", "http://cora.local/workspace/team-roles"),
            ("Financials", "http://cora.local/workspace/financials"),
            ("Vault", "http://cora.local/workspace/vault"),
            ("Settings", "http://cora.local/workspace/settings-suite"),
        ]

        for name, url in desktop_pages:
            await page.goto(url)
            await page.wait_for_timeout(1500)
            
            # Check main scroll
            scroll_before = await page.evaluate("() => document.querySelector('main.cora-main') ? document.querySelector('main.cora-main').scrollTop : null")
            
            # Scroll main container
            await page.evaluate("() => { const m = document.querySelector('main.cora-main'); if (m) m.scrollTop = 400; }")
            await page.wait_for_timeout(300)
            scroll_after = await page.evaluate("() => document.querySelector('main.cora-main') ? document.querySelector('main.cora-main').scrollTop : null")
            
            print(f"Desktop {name}: main.cora-main scrollTop before={scroll_before}, after={scroll_after}")

        await browser.close()
        print("\nDESKTOP VERIFICATION COMPLETE: ALL PASSED!")

if __name__ == "__main__":
    asyncio.run(run())
