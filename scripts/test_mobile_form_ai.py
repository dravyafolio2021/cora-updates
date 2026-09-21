import asyncio
import os
from playwright.async_api import async_playwright

async def main():
    async with async_playwright() as p:
        # Launch mobile viewport browser (iPhone 14 Pro emulation: 393x852)
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(
            viewport={'width': 393, 'height': 852},
            user_agent='Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1'
        )
        page = await context.new_page()

        print("1. Navigating to staging /workspace/login...")
        await page.goto("https://stagging.heycora.in/workspace/login", timeout=20000)
        
        # Login with admin@cora.local or cora_admin or staging user
        print("2. Filling login form...")
        await page.fill("#login-email", "admin@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        
        # Click login button
        login_btn = page.locator("button:has-text('Sign In'), #btn-login-submit, button[type='submit']").first
        await login_btn.click()
        print("Clicked login button, waiting for navigation...")
        await page.wait_for_timeout(4000)
        print(f"Current URL: {page.url}")

        # Navigate to Forms page
        print("3. Navigating to Forms module (/workspace/forms)...")
        await page.goto("https://stagging.heycora.in/workspace/forms", timeout=20000)
        await page.wait_for_timeout(3000)

        out_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
        os.makedirs(out_dir, exist_ok=True)
        await page.screenshot(path=f"{out_dir}/01_forms_list_mobile.png")
        print("Saved screenshot: 01_forms_list_mobile.png")

        # Check if mobile Edit or Create Form opens AI drawer
        print("4. Testing mobile '+ New Form' button click...")
        new_btn = page.locator("button:has-text('+ New Form'), a:has-text('+ New Form'), button:has-text('New Form')").first
        if await new_btn.count() > 0:
            await new_btn.click()
            print("Clicked '+ New Form' button.")
            await page.wait_for_timeout(3000)
            await page.screenshot(path=f"{out_dir}/02_ai_drawer_opened.png")
            print("Saved screenshot: 02_ai_drawer_opened.png")

        # Check if hash navigation to #new is intercepted
        print("5. Testing direct hash change to #new on mobile (<1024px)...")
        await page.evaluate("window.location.hash = '#new'")
        await page.wait_for_timeout(2000)
        cur_hash = await page.evaluate("window.location.hash")
        print(f"Hash after navigating to #new on mobile: {cur_hash}")

        # Test conversational form prompt
        print("6. Testing conversational form prompt in AI Drawer...")
        ai_input = page.locator("#cora-ai-sidebar-input, #cora-island-ai-input").first
        if await ai_input.count() > 0:
            prompt_text = "Create a 2-step Wedding Photography inquiry form with WhatsApp number, date picker, budget selection, and signature pad."
            await ai_input.fill(prompt_text)
            
            send_btn = page.locator("#cora-ai-sidebar-send, button:has-text('Send')").first
            if await send_btn.count() > 0:
                await send_btn.click()
            else:
                await ai_input.press("Enter")
            
            print("Waiting for AI response and card rendering...")
            await page.wait_for_timeout(8000)
            await page.screenshot(path=f"{out_dir}/03_ai_form_card_response.png")
            print("Saved screenshot: 03_ai_form_card_response.png")

        await browser.close()
        print("Test completed successfully!")

if __name__ == "__main__":
    asyncio.run(main())
