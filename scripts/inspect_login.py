import asyncio
from playwright.async_api import async_playwright

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        page = await browser.new_page()

        print("Checking staging /workspace/login...")
        try:
            res = await page.goto("https://stagging.heycora.in/workspace/login", timeout=15000)
            print(f"Staging /workspace/login status: {res.status}")
            print(f"URL: {page.url}")
            inputs = await page.locator("input").all()
            for inp in inputs:
                inp_id = await inp.get_attribute("id")
                inp_name = await inp.get_attribute("name")
                inp_type = await inp.get_attribute("type")
                print(f"  Input: id={inp_id}, name={inp_name}, type={inp_type}")
        except Exception as e:
            print(f"Staging failed: {e}")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
