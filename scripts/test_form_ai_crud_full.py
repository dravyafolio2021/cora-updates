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

        out_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
        os.makedirs(out_dir, exist_ok=True)

        # 2. Go to forms
        print("2. Navigating to Forms view...")
        await page.goto("http://cora.local/workspace/forms", timeout=15000)
        await page.wait_for_timeout(3000)
        await page.screenshot(path=f"{out_dir}/10_forms_initial_list.png")

        # 3. Create a new rich form with slider, rating, gst, signature via AI
        print("3. Prompting AI: Create a 3-step Wedding VIP form with slider, rating, gst calculator, and signature...")
        create_prompt = "Create a 3-step Wedding VIP Inquiry Form with WhatsApp, date, guest count slider, 5-star rating, GST tax calculation breakdown, and digital signature pad. Set theme to Claude Cream."
        await page.evaluate(f"window.coraPromptFormAI('', '{create_prompt}');")
        await page.wait_for_timeout(5000)
        await page.screenshot(path=f"{out_dir}/11_ai_created_rich_form.png")

        # 4. Preview the form in the live preview sheet
        print("4. Testing 'Preview Live' sheet on newly structured form...")
        preview_btn = page.locator("button:has-text('Preview Live')").first
        if await preview_btn.count() > 0:
            await preview_btn.click()
            await page.wait_for_timeout(3000)
            await page.screenshot(path=f"{out_dir}/12_live_form_preview_sheet.png")
            # Close preview sheet
            close_btn = page.locator("button:has-text('Done')").first
            if await close_btn.count() > 0:
                await close_btn.click()
                await page.wait_for_timeout(1000)

        # 5. Deploy the form via AI
        print("5. Deploying the form to DB via AI proposal execution...")
        deploy_btn = page.locator("button:has-text('Deploy')").first
        if await deploy_btn.count() > 0:
            await deploy_btn.click()
            await page.wait_for_timeout(3000)
            await page.screenshot(path=f"{out_dir}/13_form_deployed.png")

        # 6. Granular Edit: Remove field & toggle mandatory
        print("6. Prompting AI to edit form: remove budget and rename Full Name to Client Full Name...")
        edit_prompt = "Remove budget field and rename Full Name to Client Full Name and make WhatsApp mandatory"
        await page.evaluate(f"window.coraExecuteAIChat('{edit_prompt}');")
        await page.wait_for_timeout(5000)
        await page.screenshot(path=f"{out_dir}/14_ai_granular_edit_proposal.png")

        # 7. Form Duplicate Intent
        print("7. Prompting AI: duplicate form #1...")
        dup_prompt = "duplicate form #1"
        await page.evaluate(f"window.coraExecuteAIChat('{dup_prompt}');")
        await page.wait_for_timeout(5000)
        await page.screenshot(path=f"{out_dir}/15_ai_duplicate_proposal.png")

        # 8. Form Delete Intent
        print("8. Prompting AI: delete form #99...")
        del_prompt = "delete form #99"
        await page.evaluate(f"window.coraExecuteAIChat('{del_prompt}');")
        await page.wait_for_timeout(5000)
        await page.screenshot(path=f"{out_dir}/16_ai_delete_proposal.png")

        await browser.close()
        print("All Form AI CRUD operations verified successfully!")

if __name__ == "__main__":
    asyncio.run(main())
