import asyncio
import os
from playwright.async_api import async_playwright

async def run():
    output_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
    os.makedirs(output_dir, exist_ok=True)

    pages_to_test = [
        ("forms", "http://cora.local/workspace/forms"),
        ("users", "http://cora.local/workspace/team-roles"),
        ("content", "http://cora.local/workspace/blogs"),
        ("financials", "http://cora.local/workspace/financials"),
        ("vault", "http://cora.local/workspace/vault"),
        ("settings", "http://cora.local/workspace/settings-suite"),
        ("features", "http://cora.local/workspace/feature-hub")
    ]

    async with async_playwright() as p:
        # 1. Desktop Tests (1440x900)
        browser = await p.chromium.launch(headless=True)
        desktop_ctx = await browser.new_context(viewport={"width": 1440, "height": 900})
        page = await desktop_ctx.new_page()

        print("Desktop logging in...")
        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(3000)

        for name, url in pages_to_test:
            print(f"Testing desktop {name}...")
            await page.goto(url)
            await page.wait_for_timeout(1500)
            await page.screenshot(path=f"{output_dir}/desktop_{name}_initial.png")

            # Scroll inside .cora-main
            await page.evaluate("""() => {
                const main = document.querySelector('.cora-main') || document.documentElement;
                main.scrollTop = 400;
            }""")
            await page.wait_for_timeout(1000)
            await page.screenshot(path=f"{output_dir}/desktop_{name}_scrolled.png")

        await desktop_ctx.close()

        # 2. Mobile Tests (390x844)
        mobile_ctx = await browser.new_context(viewport={"width": 390, "height": 844}, is_mobile=True)
        mobile_page = await mobile_ctx.new_page()

        print("Mobile logging in...")
        await mobile_page.goto("http://cora.local/workspace/login")
        await mobile_page.fill("#login-email", "owner.studio@cora.local")
        await mobile_page.fill("#login-password", "cora_secure_pass_123")
        await mobile_page.click("button:has-text('Sign In')")
        await mobile_page.wait_for_timeout(3000)

        for name, url in [("forms", "http://cora.local/workspace/forms"), ("users", "http://cora.local/workspace/team-roles"), ("content", "http://cora.local/workspace/blogs")]:
            print(f"Testing mobile {name}...")
            await mobile_page.goto(url)
            await mobile_page.wait_for_timeout(1500)
            await mobile_page.screenshot(path=f"{output_dir}/mobile_{name}_initial.png")

        await mobile_ctx.close()
        await browser.close()
        print("All tests completed successfully!")

if __name__ == "__main__":
    asyncio.run(run())
