import asyncio
import os
from playwright.async_api import async_playwright

async def run():
    output_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
    os.makedirs(output_dir, exist_ok=True)

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(viewport={"width": 1440, "height": 900})
        page = await context.new_page()

        print("1. Logging in...")
        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(3000)

        # 1. Forms page
        print("2. Forms initial...")
        await page.goto("http://cora.local/workspace/forms")
        await page.wait_for_timeout(2000)
        await page.screenshot(path=f"{output_dir}/tabs_forms_initial_v2.png")

        print("3. Forms scrolled...")
        await page.evaluate("""() => {
            const main = document.querySelector('.cora-main') || document.documentElement;
            main.scrollTop = 400;
            window.scrollBy(0, 400);
        }""")
        await page.wait_for_timeout(1500)
        await page.screenshot(path=f"{output_dir}/tabs_forms_scrolled_v2.png")

        # 2. Team & Roles page
        print("4. Team & Roles initial...")
        await page.goto("http://cora.local/workspace/team-roles")
        await page.wait_for_timeout(2000)
        await page.screenshot(path=f"{output_dir}/tabs_team_initial_v2.png")

        print("5. Team & Roles scrolled...")
        await page.evaluate("""() => {
            const main = document.querySelector('.cora-main') || document.documentElement;
            main.scrollTop = 400;
            window.scrollBy(0, 400);
        }""")
        await page.wait_for_timeout(1500)
        await page.screenshot(path=f"{output_dir}/tabs_team_scrolled_v2.png")

        # 3. Content Suite / Blogs page
        print("6. Content Suite initial...")
        await page.goto("http://cora.local/workspace/blogs")
        await page.wait_for_timeout(2000)
        await page.screenshot(path=f"{output_dir}/tabs_content_initial_v2.png")

        print("7. Content Suite scrolled...")
        await page.evaluate("""() => {
            const main = document.querySelector('.cora-main') || document.documentElement;
            main.scrollTop = 400;
            window.scrollBy(0, 400);
        }""")
        await page.wait_for_timeout(1500)
        await page.screenshot(path=f"{output_dir}/tabs_content_scrolled_v2.png")

        await browser.close()
        print("All screenshots taken successfully!")

if __name__ == "__main__":
    asyncio.run(run())
