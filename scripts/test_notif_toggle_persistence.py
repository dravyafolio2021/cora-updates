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

        print("\n2. Navigating to Settings Suite Notifications tab...")
        await page.goto("http://cora.local/workspace/settings-suite?tab=notifications")
        await page.wait_for_timeout(2000)

        await page.evaluate("window.coraSwitchSettingsTab && window.coraSwitchSettingsTab('notifications')")
        await page.wait_for_timeout(1000)

        # Inspect initial states
        inapp_init = await page.evaluate("document.querySelector('input[name=\"cora_notif_global_inapp\"]').checked")
        push_init = await page.evaluate("document.querySelector('input[name=\"cora_notif_global_push\"]').checked")
        lead_inapp_init = await page.evaluate("document.querySelector('input[name=\"notif_inapp_lead_created\"]').checked")
        lead_push_init = await page.evaluate("document.querySelector('input[name=\"notif_push_lead_created\"]').checked")

        print(f"Initial inapp_cb checked: {inapp_init}")
        print(f"Initial push_cb checked: {push_init}")
        print(f"Initial lead_inapp_cb checked: {lead_inapp_init}")
        print(f"Initial lead_push_cb checked: {lead_push_init}")

        # Uncheck them via evaluate
        print("\n3. Toggling OFF global in-app, push, and lead_created matrix toggles...")
        await page.evaluate("""() => {
            const setCB = (name, val) => {
                const el = document.querySelector(`input[name="${name}"]`);
                if (el) {
                    el.checked = val;
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                }
            };
            setCB('cora_notif_global_inapp', false);
            setCB('cora_notif_global_push', false);
            setCB('notif_inapp_lead_created', false);
            setCB('notif_push_lead_created', false);
        }""")

        inapp_now = await page.evaluate("document.querySelector('input[name=\"cora_notif_global_inapp\"]').checked")
        push_now = await page.evaluate("document.querySelector('input[name=\"cora_notif_global_push\"]').checked")
        lead_inapp_now = await page.evaluate("document.querySelector('input[name=\"notif_inapp_lead_created\"]').checked")
        lead_push_now = await page.evaluate("document.querySelector('input[name=\"notif_push_lead_created\"]').checked")

        print(f"After unchecking -> inapp_cb: {inapp_now}, push_cb: {push_now}, lead_inapp: {lead_inapp_now}, lead_push: {lead_push_now}")

        await page.screenshot(path=f"{output_dir}/notif_toggles_before_save.png")

        print("\n4. Saving Settings Suite...")
        save_res = await page.evaluate("""() => {
            return new Promise((resolve) => {
                const form = $('#cora-settings-suite-form');
                const formData = form.serializeArray();
                const data = {
                    action: 'cora_save_system_settings_suite',
                    nonce: coraREData.ajaxNonce
                };
                $.each(formData, function(i, field) {
                    data[field.name] = field.value;
                });
                form.find('input[type="checkbox"]').each(function() {
                    var name = $(this).attr('name');
                    if (!name) return;
                    if ($(this).is(':checked')) {
                        data[name] = $(this).val() || '1';
                    } else {
                        if (name === 'default_comment_status') {
                            data[name] = 'closed';
                        } else if (name === 'blog_public') {
                            data[name] = 1;
                        } else {
                            data[name] = 0;
                        }
                    }
                });
                $.post(coraREData.ajaxUrl, data, function(res) {
                    resolve(res);
                });
            });
        }""")
        print("Save AJAX response:", save_res)
        await page.wait_for_timeout(1000)

        print("\n5. Reloading the page to test persistence...")
        await page.goto("http://cora.local/workspace/settings-suite?tab=notifications")
        await page.wait_for_timeout(2000)
        await page.evaluate("window.coraSwitchSettingsTab && window.coraSwitchSettingsTab('notifications')")
        await page.wait_for_timeout(1000)

        inapp_reloaded = await page.evaluate("document.querySelector('input[name=\"cora_notif_global_inapp\"]').checked")
        push_reloaded = await page.evaluate("document.querySelector('input[name=\"cora_notif_global_push\"]').checked")
        lead_inapp_reloaded = await page.evaluate("document.querySelector('input[name=\"notif_inapp_lead_created\"]').checked")
        lead_push_reloaded = await page.evaluate("document.querySelector('input[name=\"notif_push_lead_created\"]').checked")

        print(f"Reloaded state -> inapp_cb: {inapp_reloaded}, push_cb: {push_reloaded}, lead_inapp: {lead_inapp_reloaded}, lead_push: {lead_push_reloaded}")
        await page.screenshot(path=f"{output_dir}/notif_toggles_after_reload.png")

        assert inapp_reloaded is False, "Error: cora_notif_global_inapp was reset to checked after reload!"
        assert push_reloaded is False, "Error: cora_notif_global_push was reset to checked after reload!"
        assert lead_inapp_reloaded is False, "Error: notif_inapp_lead_created was reset to checked after reload!"
        assert lead_push_reloaded is False, "Error: notif_push_lead_created was reset to checked after reload!"

        print("\n✅ Verification SUCCESS: All toggles persisted as OFF across refresh!")

        # Now test turning them back ON
        print("\n6. Testing toggling back ON...")
        await page.evaluate("""() => {
            const setCB = (name, val) => {
                const el = document.querySelector(`input[name="${name}"]`);
                if (el) {
                    el.checked = val;
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                }
            };
            setCB('cora_notif_global_inapp', true);
            setCB('cora_notif_global_push', true);
            setCB('notif_inapp_lead_created', true);
            setCB('notif_push_lead_created', true);
        }""")

        await page.evaluate("window.coraSaveSystemSettingsSuite()")
        await page.wait_for_timeout(2500)

        await page.goto("http://cora.local/workspace/settings-suite?tab=notifications")
        await page.wait_for_timeout(2000)
        await page.evaluate("window.coraSwitchSettingsTab && window.coraSwitchSettingsTab('notifications')")
        await page.wait_for_timeout(1000)

        inapp_on_reloaded = await page.evaluate("document.querySelector('input[name=\"cora_notif_global_inapp\"]').checked")
        push_on_reloaded = await page.evaluate("document.querySelector('input[name=\"cora_notif_global_push\"]').checked")
        lead_inapp_on_reloaded = await page.evaluate("document.querySelector('input[name=\"notif_inapp_lead_created\"]').checked")
        lead_push_on_reloaded = await page.evaluate("document.querySelector('input[name=\"notif_push_lead_created\"]').checked")

        print(f"Reloaded ON state -> inapp_cb: {inapp_on_reloaded}, push_cb: {push_on_reloaded}, lead_inapp: {lead_inapp_on_reloaded}, lead_push: {lead_push_on_reloaded}")

        assert inapp_on_reloaded is True, "Error: cora_notif_global_inapp did not persist ON!"
        assert push_on_reloaded is True, "Error: cora_notif_global_push did not persist ON!"
        assert lead_inapp_on_reloaded is True, "Error: notif_inapp_lead_created did not persist ON!"
        assert lead_push_on_reloaded is True, "Error: notif_push_lead_created did not persist ON!"

        await page.screenshot(path=f"{output_dir}/notif_toggles_on_after_reload.png")
        print("\n✅ Verification COMPLETE: Both OFF and ON toggle states persist 100% reliably across refreshes!")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
