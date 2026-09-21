import asyncio
import os
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        # iPhone 14
        device = p.devices['iPhone 14']
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(**device)
        page = await context.new_page()

        print("1. Logging in...")
        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(2000)

        pages = [
            ("Dashboard", "http://cora.local/workspace/dashboard"),
            ("Forms", "http://cora.local/workspace/forms"),
            ("Team Roles", "http://cora.local/workspace/team-roles"),
            ("Content", "http://cora.local/workspace/content-suite"),
            ("Clients", "http://cora.local/workspace/clients"),
            ("Visual Builder", "http://cora.local/workspace/canvas"),
        ]

        for name, url in pages:
            print(f"\n--- Testing page: {name} ({url}) ---")
            await page.goto(url)
            await page.wait_for_timeout(1500)

            # Check if any tour or backdrop is visible or active
            backdrop_info = await page.evaluate("""() => {
                const tb = document.querySelector('.cora-tour-backdrop');
                const cb = document.querySelector('#cora-customizer-backdrop');
                const cd = document.querySelector('#cora-dashboard-customizer-drawer');
                return {
                    tourBackdrop: tb ? { display: window.getComputedStyle(tb).display, opacity: window.getComputedStyle(tb).opacity, pointerEvents: window.getComputedStyle(tb).pointerEvents } : null,
                    customizerBackdrop: cb ? { display: window.getComputedStyle(cb).display, opacity: window.getComputedStyle(cb).opacity, pointerEvents: window.getComputedStyle(cb).pointerEvents } : null,
                    customizerDrawer: cd ? { display: window.getComputedStyle(cd).display, pointerEvents: window.getComputedStyle(cd).pointerEvents } : null
                };
            }""")
            print("Backdrop info:", backdrop_info)

            # Test scrolling via wheel / touch
            before_scroll = await page.evaluate("() => ({ windowY: window.scrollY, bodyY: document.body.scrollTop, docY: document.documentElement.scrollTop, mainY: document.querySelector('.cora-main') ? document.querySelector('.cora-main').scrollTop : null })")
            print("Before scroll:", before_scroll)

            # Simulate wheel scrolling down
            await page.mouse.wheel(0, 500)
            await page.wait_for_timeout(600)

            after_scroll = await page.evaluate("() => ({ windowY: window.scrollY, bodyY: document.body.scrollTop, docY: document.documentElement.scrollTop, mainY: document.querySelector('.cora-main') ? document.querySelector('.cora-main').scrollTop : null })")
            print("After scroll (wheel down 500):", after_scroll)

            # Test clicking navigation links in the bottom island nav
            island_nav = page.locator("#cora-island-view-nav, .cora-mobile-bottom-bar, [data-island-target]").first
            has_island = await island_nav.count() > 0
            print("Has bottom island nav:", has_island)

            # Test opening hamburger drawer
            more_btn = page.locator("[data-island-target='more'], #cora-mobile-nav-trigger, .cora-mobile-nav-toggle").first
            if await more_btn.count() > 0:
                print("Clicking 'More' / Hamburger...")
                await more_btn.click()
                await page.wait_for_timeout(600)
                drawer_open = await page.evaluate("""() => {
                    const d = document.getElementById('cora-mobile-nav-drawer');
                    return d ? { display: window.getComputedStyle(d).display, visibility: window.getComputedStyle(d).visibility, isOpen: d.classList.contains('open') } : 'No drawer';
                }""")
                print("Mobile nav drawer status:", drawer_open)
                
                # Try clicking a link inside the drawer
                nav_link = page.locator("#cora-mobile-nav-drawer-sheet a").first
                if await nav_link.count() > 0:
                    link_text = await nav_link.inner_text()
                    print(f"Drawer has link: {link_text.strip()[:30]}")

                # Close drawer
                close_btn = page.locator("#cora-mobile-nav-drawer button, #cora-mobile-nav-drawer [onclick*='false']").first
                if await close_btn.count() > 0:
                    await close_btn.click()
                    await page.wait_for_timeout(400)

        await browser.close()
        print("\nAll tests completed!")

if __name__ == "__main__":
    asyncio.run(run())
