import asyncio
import os
from playwright.async_api import async_playwright

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(
            viewport={'width': 393, 'height': 852},
            user_agent='Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1'
        )
        page = await context.new_page()

        print("1. Navigating to local /workspace/login...")
        try:
            await page.goto("http://cora.local/workspace/login", timeout=8000)
            print("Local is up! Logging in...")
            await page.fill("#login-email", "owner.studio@cora.local")
            await page.fill("#login-password", "cora_secure_pass_123")
            await page.click("button:has-text('Sign In')")
            await page.wait_for_timeout(3000)
            print(f"URL after login: {page.url}")

            # Go to forms
            await page.goto("http://cora.local/workspace/forms", timeout=8000)
            await page.wait_for_timeout(3000)
        except Exception as e:
            print(f"Local failed: {e}")
            print("Trying staging with wp-login...")
            await page.goto("https://stagging.heycora.in/wp-login.php", timeout=15000)
            await page.fill("#user_login", "cora_admin")
            await page.fill("#user_pass", "cora_secure_pass_123")
            await page.click("#wp-submit")
            await page.wait_for_timeout(3000)
            await page.goto("https://stagging.heycora.in/workspace/forms", timeout=15000)
            await page.wait_for_timeout(3000)

        out_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
        os.makedirs(out_dir, exist_ok=True)
        await page.screenshot(path=f"{out_dir}/01_forms_view.png")
        print(f"Saved screenshot: 01_forms_view.png at URL {page.url}")

        # Test mobile edit button or new form button
        print("Checking mobile buttons...")
        # Check if + New Form button triggers coraPromptFormAI
        new_btn = page.locator("button:has-text('+ New Form'), a:has-text('+ New Form'), button:has-text('New Form')").first
        if await new_btn.count() > 0:
            print("Found + New Form button. Clicking it...")
            await new_btn.click()
            await page.wait_for_timeout(3000)
            await page.screenshot(path=f"{out_dir}/02_ai_assistant_opened.png")
            print("Saved screenshot: 02_ai_assistant_opened.png")

        # Check AI prompt and proposal response
        print("Prompting AI for multi-step wedding form with logic...")
        ai_input = page.locator("#cora-ai-sidebar-input, #cora-island-ai-input").first
        if await ai_input.count() > 0:
            await ai_input.fill("Create a 2-step Wedding Inquiry form with WhatsApp phone, date, budget tier, and digital signature pad. Add conditional logic to show signature if budget is high.")
            send_btn = page.locator("#cora-ai-sidebar-send, button:has-text('Send')").first
            if await send_btn.count() > 0:
                await send_btn.click()
            else:
                await ai_input.press("Enter")
            
            print("Waiting for response...")
            await page.wait_for_timeout(8000)
            await page.screenshot(path=f"{out_dir}/03_form_proposal_card.png")
            print("Saved screenshot: 03_form_proposal_card.png")

        await browser.close()
        print("Done testing!")

if __name__ == "__main__":
    asyncio.run(main())
