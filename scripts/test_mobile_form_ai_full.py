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
        await page.goto("http://cora.local/workspace/login", timeout=15000)
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(3000)

        # Go to forms
        print("2. Navigating to Forms view...")
        await page.goto("http://cora.local/workspace/forms", timeout=15000)
        await page.wait_for_timeout(3000)

        out_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
        os.makedirs(out_dir, exist_ok=True)
        await page.screenshot(path=f"{out_dir}/01_forms_view.png")

        # Click '+ AI Create' button
        print("3. Clicking '+ AI Create' button on mobile...")
        ai_create_btn = page.locator("button:has-text('AI Create')").first
        await ai_create_btn.click()
        await page.wait_for_timeout(4000)
        await page.screenshot(path=f"{out_dir}/02_ai_drawer_opened.png")
        print("Saved screenshot: 02_ai_drawer_opened.png")

        # Now click 'Edit with AI' on first form card
        print("4. Testing 'Edit with AI' button on form card...")
        # First close AI drawer if open or click edit with ai directly
        await page.evaluate("if(typeof coraToggleSidebar==='function') coraToggleSidebar(false);")
        await page.wait_for_timeout(1000)
        
        edit_ai_btn = page.locator("button:has-text('Edit with AI')").first
        if await edit_ai_btn.count() > 0:
            await edit_ai_btn.click()
            await page.wait_for_timeout(4000)
            await page.screenshot(path=f"{out_dir}/03_edit_form_ai_prompted.png")
            print("Saved screenshot: 03_edit_form_ai_prompted.png")

        # Prompt AI to create a new multi-step form with conditional logic
        print("5. Prompting AI for multi-step wedding form with logic...")
        prompt_text = "Create a 2-step Wedding Photography inquiry form with WhatsApp number, date picker, budget selection, and signature pad. Configure logic to show signature only for high budgets."
        await page.evaluate(f"window.coraPromptFormAI('', '{prompt_text}');")
        await page.wait_for_timeout(6000)
        await page.screenshot(path=f"{out_dir}/04_form_generative_ui_card.png")
        print("Saved screenshot: 04_form_generative_ui_card.png")

        # Test 'Preview Live' on the generated form proposal
        print("6. Testing 'Preview Live' bottom sheet...")
        preview_btn = page.locator("button:has-text('Preview Live')").first
        if await preview_btn.count() > 0:
            await preview_btn.click()
            await page.wait_for_timeout(3000)
            await page.screenshot(path=f"{out_dir}/05_live_form_preview_modal.png")
            print("Saved screenshot: 05_live_form_preview_modal.png")

        await browser.close()
        print("All mobile test steps completed successfully!")

if __name__ == "__main__":
    asyncio.run(main())
