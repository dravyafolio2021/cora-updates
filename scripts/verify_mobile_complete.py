import asyncio
import os
from playwright.async_api import async_playwright

async def run():
    output_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
    os.makedirs(output_dir, exist_ok=True)

    async with async_playwright() as p:
        device = p.devices['iPhone 14']
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(**device)
        page = await context.new_page()

        print("1. Logging into local workspace...")
        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(2500)

        # Clear any tour flag to test fresh load behavior
        await page.evaluate("localStorage.removeItem('cora_re_tour_completed'); localStorage.removeItem('cora_platform_tour_completed');")
        await page.goto("http://cora.local/workspace/dashboard")
        await page.wait_for_timeout(2000)

        print("\n--- 2. Dashboard Verification ---")
        await page.screenshot(path=f"{output_dir}/verify_dashboard_top.png")

        # Check backdrop status
        bd_check = await page.evaluate("""() => {
            const tb = document.querySelector('.cora-tour-backdrop');
            const cb = document.querySelector('#cora-customizer-backdrop');
            return {
                tourBackdrop: tb ? { display: window.getComputedStyle(tb).display, pointerEvents: window.getComputedStyle(tb).pointerEvents } : 'None',
                customizerBackdrop: cb ? { display: window.getComputedStyle(cb).display, pointerEvents: window.getComputedStyle(cb).pointerEvents } : 'None',
                htmlOverflowY: window.getComputedStyle(document.documentElement).overflowY,
                bodyOverflowY: window.getComputedStyle(document.body).overflowY
            };
        }""")
        print("Dashboard backdrops & overflow:", bd_check)

        # Test wheel/touch scroll down
        await page.mouse.wheel(0, 800)
        await page.wait_for_timeout(800)
        scroll_dash = await page.evaluate("() => ({ windowY: window.scrollY, docY: document.documentElement.scrollTop })")
        print("Dashboard scroll after 800px wheel down:", scroll_dash)
        await page.screenshot(path=f"{output_dir}/verify_dashboard_scrolled.png")
        assert scroll_dash['windowY'] > 100 or scroll_dash['docY'] > 100, "Dashboard failed to scroll!"

        # Scroll back top
        await page.mouse.wheel(0, -800)
        await page.wait_for_timeout(500)

        print("\n--- 3. Testing Mobile Bottom Navigation Island & Drawer ---")
        # 3a. Switch Island to Navigation state using menu button
        menu_btn = page.locator("#cora-island-state-menu-btn").first
        if await menu_btn.count() > 0:
            print("Switching Island to Nav tabs mode...")
            await menu_btn.click()
            await page.wait_for_timeout(500)
            await page.screenshot(path=f"{output_dir}/verify_island_nav_mode.png")

        # 3b. Click 'More' on island nav
        more_btn = page.locator("#cora-island-view-nav a[data-island-target='more']").first
        if await more_btn.count() > 0:
            print("Clicking 'More' tab in Island Nav...")
            await more_btn.click()
            await page.wait_for_timeout(800)
            await page.screenshot(path=f"{output_dir}/verify_mobile_drawer_open.png")
            
            drawer_state = await page.evaluate("""() => {
                const d = document.getElementById('cora-mobile-nav-drawer');
                return {
                    isOpen: d.classList.contains('open'),
                    display: window.getComputedStyle(d).display,
                    pointerEvents: window.getComputedStyle(d).pointerEvents
                };
            }""")
            print("Drawer state:", drawer_state)
            assert drawer_state['isOpen'] == True, "Mobile nav drawer failed to open!"

            # Click a link inside drawer, e.g. Forms
            forms_link = page.locator("#cora-mobile-nav-drawer-sheet a[href*='forms'], #cora-mobile-nav-drawer-sheet a:has-text('Forms')").first
            if await forms_link.count() > 0:
                print("Clicking Forms from mobile drawer...")
                await forms_link.click()
                await page.wait_for_timeout(2500)
                print("Navigated to:", page.url)
                await page.screenshot(path=f"{output_dir}/verify_forms_page.png")
                assert "forms" in page.url, "Failed to navigate to Forms page via drawer!"

        print("\n--- 4. Forms Page Scroll & Tab Interaction ---")
        # Check forms scroll
        await page.mouse.wheel(0, 700)
        await page.wait_for_timeout(800)
        scroll_forms = await page.evaluate("() => ({ windowY: window.scrollY, docY: document.documentElement.scrollTop })")
        print("Forms scroll after 700px down:", scroll_forms)
        await page.screenshot(path=f"{output_dir}/verify_forms_scrolled.png")
        assert scroll_forms['windowY'] > 100 or scroll_forms['docY'] > 100, "Forms page failed to scroll!"

        # Test clicking a sub-tab on forms
        print("Clicking 'Templates' sub-tab...")
        tpl_tab = page.locator("#cora-forms-tabs button:has-text('Templates'), .cora-sub-tab:has-text('Templates')").first
        if await tpl_tab.count() > 0:
            await tpl_tab.click()
            await page.wait_for_timeout(1000)
            await page.screenshot(path=f"{output_dir}/verify_forms_template_tab.png")
            print("Templates tab clicked successfully!")

        print("\n--- 5. Content Suite Verification ---")
        await page.goto("http://cora.local/workspace/content-suite")
        await page.wait_for_timeout(2000)
        await page.screenshot(path=f"{output_dir}/verify_content_suite_top.png")

        # Scroll content suite
        await page.mouse.wheel(0, 900)
        await page.wait_for_timeout(800)
        scroll_content = await page.evaluate("() => ({ windowY: window.scrollY, docY: document.documentElement.scrollTop })")
        print("Content Suite scroll after 900px down:", scroll_content)
        await page.screenshot(path=f"{output_dir}/verify_content_suite_scrolled.png")
        assert scroll_content['windowY'] > 100 or scroll_content['docY'] > 100, "Content Suite failed to scroll!"

        # Click SEO tab on Content Suite
        seo_tab = page.locator("#cora-content-tabs button:has-text('SEO'), .cora-tab-btn:has-text('SEO')").first
        if await seo_tab.count() > 0:
            print("Clicking 'SEO & AI Visibility' tab...")
            await seo_tab.click()
            await page.wait_for_timeout(1000)
            await page.screenshot(path=f"{output_dir}/verify_content_seo_tab.png")
            print("SEO tab clicked successfully!")

        print("\n--- 6. Team Roles Verification ---")
        await page.goto("http://cora.local/workspace/team-roles")
        await page.wait_for_timeout(2000)
        await page.screenshot(path=f"{output_dir}/verify_team_roles_top.png")

        # Scroll team roles
        await page.mouse.wheel(0, 800)
        await page.wait_for_timeout(800)
        scroll_users = await page.evaluate("() => ({ windowY: window.scrollY, docY: document.documentElement.scrollTop })")
        print("Team Roles scroll after 800px down:", scroll_users)
        await page.screenshot(path=f"{output_dir}/verify_team_roles_scrolled.png")
        assert scroll_users['windowY'] > 100 or scroll_users['docY'] > 100, "Team Roles failed to scroll!"

        print("\n==========================================")
        print("ALL MOBILE SCROLL & NAVIGATION TESTS PASSED!")
        print("==========================================")
        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
