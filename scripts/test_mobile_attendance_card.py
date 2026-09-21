import asyncio
import os
from playwright.async_api import async_playwright

async def main():
    output_dir = "/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output"
    os.makedirs(output_dir, exist_ok=True)

    async with async_playwright() as p:
        # Test on small mobile device (360x740 width)
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(
            viewport={'width': 360, 'height': 740},
            user_agent='Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
            is_mobile=True,
            has_touch=True
        )
        page = await context.new_page()

        print("1. Logging in...")
        await page.goto("http://cora.local/workspace/login", timeout=15000)
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(2000)

        print("\n2. Navigating to team-roles view...")
        await page.goto("http://cora.local/workspace/team-roles")
        await page.wait_for_timeout(2000)

        print("\n3. Switching to Attendance Logs tab...")
        att_tab = page.locator("button[data-tab='tab-attendance-logs'], #cora-team-tab-attendance-logs, button:has-text('Attendance')").first
        if await att_tab.count() > 0:
            await att_tab.click()
            await page.wait_for_timeout(1500)

        # Trigger a test punch in if no logs exist
        punch_in_btn = page.locator("#cora-user-punch-in-btn")
        if await punch_in_btn.count() > 0:
            print("Punching in to generate a fresh attendance record...")
            await punch_in_btn.click()
            await page.wait_for_timeout(2000)

        # Inspect mobile card elements for truncation / text content
        print("\n4. Inspecting mobile card elements for truncation...")
        card_data = await page.evaluate('''() => {
            const list = document.querySelector('#cora-user-attendance-mobile-list');
            const cards = list ? list.querySelectorAll('.p-3\\\\.5, .sm\\\\:p-4') : [];
            return Array.from(cards).map(card => {
                const nameEl = card.querySelector('h4');
                const timeEl = card.querySelector('.font-mono.select-all');
                const gpsEl = card.querySelector('a[href*="maps"]');
                return {
                    nameText: nameEl ? nameEl.textContent.trim() : '',
                    nameTruncated: nameEl ? nameEl.scrollWidth > nameEl.clientWidth : false,
                    timeText: timeEl ? timeEl.textContent.trim() : '',
                    timeHasEllipsis: timeEl ? timeEl.textContent.includes('...') : false,
                    timeTruncated: timeEl ? timeEl.scrollWidth > timeEl.clientWidth : false,
                    gpsText: gpsEl ? gpsEl.textContent.trim() : '',
                    gpsTruncated: gpsEl ? gpsEl.scrollWidth > gpsEl.clientWidth : false
                };
            });
        }''')
        print(f"Mobile Card Data (360px viewport): {card_data}")

        # Scroll to mobile card list
        await page.evaluate('''() => {
            const list = document.querySelector('#cora-user-attendance-mobile-list');
            if (list) list.scrollIntoView();
        }''')
        await page.wait_for_timeout(500)

        screenshot_path = f"{output_dir}/mobile_attendance_cards_360px.png"
        await page.screenshot(path=screenshot_path)
        print(f"Saved screenshot: {screenshot_path}")

        print("\nMobile attendance logs verification completed successfully!")
        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
