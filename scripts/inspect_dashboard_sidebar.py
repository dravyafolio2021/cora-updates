import asyncio
from playwright.async_api import async_playwright

async def inspect_browser():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(viewport={'width': 1280, 'height': 800})
        page = await context.new_page()

        print("1. Logging in...")
        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(3000)

        print("2. Navigating to dashboard...")
        await page.goto("http://cora.local/workspace/dashboard")
        await page.wait_for_timeout(2000)

        # Inspect sidebar DOM
        dom_info = await page.evaluate("""() => {
            const sidebar = document.querySelector('.cora-sidebar, #cora-sidebar');
            const nav = document.querySelector('.cora-sidebar-nav');
            const groups = document.querySelectorAll('.cora-nav-group');
            const items = document.querySelectorAll('.cora-nav-item');
            const lis = document.querySelectorAll('.cora-sidebar-nav li');
            const searchResults = document.querySelector('#cora-sidebar-search-results');
            
            return {
                bodyClasses: document.body.className,
                sidebarClasses: sidebar ? sidebar.className : null,
                sidebarStyles: sidebar ? sidebar.getAttribute('style') : null,
                navClasses: nav ? nav.className : null,
                navStyles: nav ? nav.getAttribute('style') : null,
                navComputedDisplay: nav ? getComputedStyle(nav).display : null,
                groupsCount: groups.length,
                groupsDetails: Array.from(groups).map(g => ({
                    label: g.querySelector('.cora-nav-group-label') ? g.querySelector('.cora-nav-group-label').textContent.trim() : null,
                    labelClasses: g.querySelector('.cora-nav-group-label') ? g.querySelector('.cora-nav-group-label').className : null,
                    groupClasses: g.className,
                    computedDisplay: getComputedStyle(g).display,
                    liCount: g.querySelectorAll('li').length
                })),
                totalLis: lis.length,
                totalItems: items.length,
                searchResultsClasses: searchResults ? searchResults.className : null,
                searchResultsDisplay: searchResults ? getComputedStyle(searchResults).display : null,
            };
        }""")
        print("DOM Info:", dom_info)

        # Take full page and sidebar screenshot
        await page.screenshot(path="/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output/inspect_dashboard_sidebar.png")

        await browser.close()

asyncio.run(inspect_browser())
