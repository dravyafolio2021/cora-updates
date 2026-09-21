import asyncio
import os
from playwright.async_api import async_playwright

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        
        # Test 1: Desktop Viewport (1280 x 800)
        context = await browser.new_context(
            viewport={'width': 1280, 'height': 800},
            user_agent='Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        )
        page = await context.new_page()

        out_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
        os.makedirs(out_dir, exist_ok=True)

        print("1. Logging into local environment on Desktop...")
        await page.goto("http://cora.local/workspace/login", timeout=15000)
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(3000)

        print("2. Navigating to Forms list...")
        await page.goto("http://cora.local/workspace/forms", timeout=15000)
        await page.wait_for_timeout(2000)
        await page.screenshot(path=f"{out_dir}/20_forms_desktop_list.png")

        print("3. Opening Form Editor via #new (or Create Form button)...")
        await page.click("#btn-create-form")
        await page.wait_for_timeout(2000)

        # Check editor state visibility and topbar
        editor_visible = await page.is_visible("#form-editor-state")
        back_btn_visible = await page.is_visible("#btn-back-to-list")
        title_visible = await page.is_visible("#editor-form-title")
        publish_btn_visible = await page.is_visible("#btn-save-form")
        draft_btn_visible = await page.is_visible("#btn-save-draft")
        view_btn_visible = await page.is_visible("#btn-view-form")
        share_btn_visible = await page.is_visible("#btn-share-editor")
        undo_btn_visible = await page.is_visible("#btn-editor-undo")
        redo_btn_visible = await page.is_visible("#btn-editor-redo")

        print(f"Editor State Visible: {editor_visible}")
        print(f"Back Button Visible: {back_btn_visible}")
        print(f"Title Input Visible: {title_visible}")
        print(f"Publish Button Visible: {publish_btn_visible}")
        print(f"Save Draft Visible: {draft_btn_visible}")
        print(f"View Button Visible: {view_btn_visible}")
        print(f"Share Button Visible: {share_btn_visible}")
        print(f"Undo Button Visible: {undo_btn_visible}")
        print(f"Redo Button Visible: {redo_btn_visible}")

        # Check bounding box of top control bar
        back_box = await page.locator("#btn-back-to-list").bounding_box()
        publish_box = await page.locator("#btn-save-form").bounding_box()
        print(f"Back button box: {back_box}")
        print(f"Publish button box: {publish_box}")

        assert editor_visible, "Form editor state must be visible!"
        assert back_btn_visible, "Back button must be visible!"
        assert publish_btn_visible, "Publish button must be visible!"
        assert back_box and back_box['y'] >= 0 and back_box['y'] < 30, f"Back button must be at the very top (y={back_box['y'] if back_box else 'None'})"

        await page.screenshot(path=f"{out_dir}/21_form_editor_top_panel_desktop.png")

        print("4. Testing Back button returns to forms list...")
        await page.click("#btn-back-to-list")
        await page.wait_for_timeout(2000)
        list_visible = await page.is_visible("#forms-list-state")
        print(f"Forms list visible after back click: {list_visible}")
        assert list_visible, "Forms list must be visible after clicking back!"
        await page.screenshot(path=f"{out_dir}/22_forms_list_after_back.png")

        print("5. Opening existing Form to Edit (#edit/...)...")
        edit_link = page.locator(".btn-edit-form").first
        if await edit_link.count() > 0:
            await edit_link.click()
        else:
            await page.goto("http://cora.local/workspace/forms#edit/1")
        await page.wait_for_timeout(3000)

        editor_visible_edit = await page.is_visible("#form-editor-state")
        back_btn_visible_edit = await page.is_visible("#btn-back-to-list")
        publish_btn_visible_edit = await page.is_visible("#btn-save-form")
        title_val = await page.input_value("#editor-form-title")
        print(f"Editor Visible on Edit Mode: {editor_visible_edit}")
        print(f"Back Button Visible on Edit Mode: {back_btn_visible_edit}")
        print(f"Publish Button Visible on Edit Mode: {publish_btn_visible_edit}")
        print(f"Form Title: {title_val}")

        assert editor_visible_edit, "Editor must be visible on edit mode!"
        assert back_btn_visible_edit, "Back button must be visible on edit mode!"
        assert publish_btn_visible_edit, "Publish button must be visible on edit mode!"
        await page.screenshot(path=f"{out_dir}/23_form_editor_existing_form_desktop.png")

        print("All tests completed successfully!")
        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
