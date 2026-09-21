import asyncio
import os
from playwright.async_api import async_playwright

async def main():
    output_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
    os.makedirs(output_dir, exist_ok=True)

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(viewport={'width': 1280, 'height': 900})
        page = await context.new_page()

        print("1. Logging into local test environment at http://cora.local/workspace/login...")
        await page.goto("http://cora.local/workspace/login", timeout=15000)
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(2000)
        print("Logged in successfully. Current URL:", page.url)

        print("\n2. Navigating to team/users view...")
        await page.goto("http://cora.local/workspace/team-roles")
        await page.wait_for_timeout(2000)
        print(f"Current page URL: {page.url}")

        # Check for edit buttons
        edit_btns = await page.locator(".cora-edit-user-btn, button[onclick*='openEditUserDrawer']").all()
        print(f"Found {len(edit_btns)} edit user buttons.")

        if edit_btns:
            print("Clicking first user's Edit button...")
            await edit_btns[0].click()
            await page.wait_for_timeout(1000)

            # Click on Tab 5 (AI & Security)
            ai_tab = page.locator('.drawer-edit-tab[data-drawer-tab="tab-edit-ai-security"]')
            if await ai_tab.count() > 0:
                await ai_tab.click()
                await page.wait_for_timeout(500)
                print("Clicked AI & Security tab.")

                # Read token display text and slider value
                token_display = await page.locator("#ai-token-display").text_content()
                slider_val = await page.locator("#edit-ai-token-limit").input_value()
                slider_max = await page.locator("#edit-ai-token-limit").get_attribute("max")
                print(f"AI Token Display: '{token_display.strip()}'")
                print(f"AI Slider Value: '{slider_val}', Max: '{slider_max}'")

                # Take screenshot of edit drawer
                screenshot_path = f"{output_dir}/user_token_budget_drawer.png"
                await page.screenshot(path=screenshot_path)
                print(f"Saved screenshot to {screenshot_path}")

                # Test dragging slider
                print("\n3. Testing slider interaction...")
                await page.locator("#edit-ai-token-limit").fill("250000")
                await page.locator("#edit-ai-token-limit").dispatch_event("input")
                updated_display = await page.locator("#ai-token-display").text_content()
                print(f"Display after setting 250,000: '{updated_display.strip()}'")

                # Test clicking 'Reset to Equal Share'
                print("\n4. Testing 'Reset to Equal Share' button...")
                await page.locator("button:has-text('Reset to Equal Share')").click()
                await page.wait_for_timeout(500)
                reset_display = await page.locator("#ai-token-display").text_content()
                reset_val = await page.locator("#edit-ai-token-limit").input_value()
                print(f"Display after Reset: '{reset_display.strip()}', Slider Val: '{reset_val}'")

                # Take screenshot after reset
                screenshot_path2 = f"{output_dir}/user_token_budget_after_reset.png"
                await page.screenshot(path=screenshot_path2)
                print(f"Saved screenshot to {screenshot_path2}")
            else:
                print("AI & Security tab not found in drawer.")
        else:
            print("No edit user buttons found on page.")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
