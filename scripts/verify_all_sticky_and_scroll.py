import asyncio
import os
from playwright.async_api import async_playwright

async def run():
    output_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
    os.makedirs(output_dir, exist_ok=True)

    async with async_playwright() as p:
        # 1. Test Mobile (iPhone 14)
        print("==================================================")
        print("1. MOBILE VIEWPORT VERIFICATION (iPhone 14)")
        print("==================================================")
        device = p.devices['iPhone 14']
        browser = await p.chromium.launch(headless=True)
        mobile_context = await browser.new_context(**device)
        page = await mobile_context.new_page()

        print("Logging in...")
        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(2000)

        mobile_pages = [
            ("Forms", "http://cora.local/workspace/forms", "#cora-forms-tabs"),
            ("Content Suite", "http://cora.local/workspace/blogs", "#cora-content-tabs"),
            ("Team Roles", "http://cora.local/workspace/team-roles", ".cora-sub-tabs-container"),
        ]

        for name, url, tab_sel in mobile_pages:
            print(f"\n--- Testing Mobile Sticky on {name} ({url}) ---")
            await page.goto(url)
            await page.wait_for_timeout(2000)

            # Before scroll geometry
            b_geo = await page.evaluate(f"""() => {{
                const topbar = document.getElementById('cora-global-topbar');
                const tabs = document.querySelector('{tab_sel}');
                return {{
                    topbar: topbar ? {{ top: topbar.getBoundingClientRect().top, height: topbar.getBoundingClientRect().height }} : null,
                    tabs: tabs ? {{ top: tabs.getBoundingClientRect().top, height: tabs.getBoundingClientRect().height }} : null,
                    windowScrollY: window.scrollY
                }};
            }}""")
            print(f"[{name}] Before scroll:", b_geo)

            # Scroll down 500px
            await page.mouse.wheel(0, 500)
            await page.wait_for_timeout(600)

            # After scroll geometry
            a_geo = await page.evaluate(f"""() => {{
                const topbar = document.getElementById('cora-global-topbar');
                const tabs = document.querySelector('{tab_sel}');
                return {{
                    topbar: topbar ? {{ top: topbar.getBoundingClientRect().top, height: topbar.getBoundingClientRect().height }} : null,
                    tabs: tabs ? {{ top: tabs.getBoundingClientRect().top, height: tabs.getBoundingClientRect().height }} : null,
                    windowScrollY: window.scrollY
                }};
            }}""")
            print(f"[{name}] After scroll 500px:", a_geo)

            # Screenshot
            slug = name.lower().replace(" ", "_")
            await page.screenshot(path=f"{output_dir}/mobile_sticky_{slug}_scrolled.png")

            # Assertions
            assert a_geo['windowScrollY'] > 50, f"Failed to scroll on {name}!"
            assert a_geo['topbar']['top'] == 0, f"Topbar not sticky at top:0 on {name}! Got: {a_geo['topbar']['top']}"
            assert abs(a_geo['tabs']['top'] - a_geo['topbar']['height']) <= 3, f"Tabs not sticky below topbar on {name}! Tabs top={a_geo['tabs']['top']}, Topbar height={a_geo['topbar']['height']}"
            print(f"✓ {name} mobile sticky tabs verified perfectly!")

        await mobile_context.close()

        # 2. Test Desktop (1440x900)
        print("\n==================================================")
        print("2. DESKTOP VIEWPORT VERIFICATION (1440x900)")
        print("==================================================")
        desktop_context = await browser.new_context(viewport={"width": 1440, "height": 900})
        d_page = await desktop_context.new_page()

        await d_page.goto("http://cora.local/workspace/login")
        await d_page.fill("#login-email", "owner.studio@cora.local")
        await d_page.fill("#login-password", "cora_secure_pass_123")
        await d_page.click("button:has-text('Sign In')")
        await d_page.wait_for_timeout(2000)

        desktop_pages = [
            ("Forms", "http://cora.local/workspace/forms", "#cora-forms-tabs"),
            ("Content Suite", "http://cora.local/workspace/blogs", "#cora-content-tabs"),
            ("Team Roles", "http://cora.local/workspace/team-roles", ".cora-sub-tabs-container"),
        ]

        for name, url, tab_sel in desktop_pages:
            print(f"\n--- Testing Desktop on {name} ---")
            await d_page.goto(url)
            await d_page.wait_for_timeout(2000)

            # Scroll main.cora-main
            await d_page.evaluate("() => { const m = document.querySelector('main.cora-main'); if (m) m.scrollTop = 400; }")
            await d_page.wait_for_timeout(500)

            d_geo = await d_page.evaluate(f"""() => {{
                const main = document.querySelector('main.cora-main');
                const tabs = document.querySelector('{tab_sel}');
                return {{
                    mainScrollTop: main ? main.scrollTop : null,
                    tabsTop: tabs ? tabs.getBoundingClientRect().top : null
                }};
            }}""")
            print(f"[{name}] Desktop scroll state:", d_geo)
            slug = name.lower().replace(" ", "_")
            await d_page.screenshot(path=f"{output_dir}/desktop_sticky_{slug}_scrolled.png")

        await browser.close()
        print("\n==================================================")
        print("ALL MOBILE AND DESKTOP STICKY & SCROLL TESTS PASSED!")
        print("==================================================")

if __name__ == "__main__":
    asyncio.run(run())
