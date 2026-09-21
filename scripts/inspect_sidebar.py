import asyncio
from playwright.async_api import async_playwright

async def inspect_sidebar():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(viewport={'width': 1280, 'height': 800})
        page = await context.new_page()

        # Login
        await page.goto("http://cora.local/wp-login.php")
        await page.fill("#user_login", "studio_owner")
        await page.fill("#user_pass", "cora_secure_pass_123")
        await page.click("#wp-submit")
        await page.wait_for_load_state("networkidle")

        # Go to dashboard
        await page.goto("http://cora.local/workspace/dashboard?industry=photography_studio")
        await page.wait_for_load_state("networkidle")
        await page.wait_for_timeout(2000)

        # Inspect sidebar HTML
        sidebar_html = await page.evaluate("""() => {
            const sidebar = document.querySelector('.cora-sidebar, #cora-sidebar');
            const nav = document.querySelector('.cora-sidebar-nav');
            const groups = document.querySelectorAll('.cora-nav-group');
            const items = document.querySelectorAll('.cora-nav-item');
            const searchInput = document.querySelector('#cora-sidebar-search-input');
            const searchVal = searchInput ? searchInput.value : null;
            const searchResults = document.querySelector('#cora-sidebar-search-results');
            const isNavHidden = nav ? getComputedStyle(nav).display : 'no nav';
            return {
                sidebarExists: !!sidebar,
                navExists: !!nav,
                isNavHidden: isNavHidden,
                groupsCount: groups.length,
                itemsCount: items.length,
                searchVal: searchVal,
                searchResultsDisplay: searchResults ? getComputedStyle(searchResults).display : 'no search results',
                navInnerHTML: nav ? nav.innerHTML.substring(0, 500) : 'null'
            };
        }""")
        print("Sidebar inspection result:", sidebar_html)

        # Take screenshot
        await page.screenshot(path="/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output/inspect_sidebar_desktop.png")

        await browser.close()

asyncio.run(inspect_sidebar())
