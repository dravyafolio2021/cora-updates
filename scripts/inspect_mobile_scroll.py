import asyncio
import os
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(viewport={"width": 390, "height": 844}, is_mobile=True, has_touch=True)
        page = await context.new_page()

        await page.goto("http://cora.local/workspace/login")
        await page.fill("#login-email", "owner.studio@cora.local")
        await page.fill("#login-password", "cora_secure_pass_123")
        await page.click("button:has-text('Sign In')")
        await page.wait_for_timeout(2000)

        # Let's inspect pages: dashboard, forms, team-roles, content, media, clients
        pages_to_test = [
            "http://cora.local/workspace/dashboard",
            "http://cora.local/workspace/forms",
            "http://cora.local/workspace/team-roles",
            "http://cora.local/workspace/content-suite",
            "http://cora.local/workspace/clients",
        ]

        for url in pages_to_test:
            print(f"\n================ Testing {url} ================")
            await page.goto(url)
            await page.wait_for_timeout(2000)

            diag = await page.evaluate("""() => {
                const results = {};
                results.windowInnerHeight = window.innerHeight;
                results.bodyScrollHeight = document.body.scrollHeight;
                results.docScrollHeight = document.documentElement.scrollHeight;
                results.bodyClientHeight = document.body.clientHeight;
                results.docClientHeight = document.documentElement.clientHeight;
                
                const getStyles = (el) => {
                    if (!el) return null;
                    const cs = window.getComputedStyle(el);
                    return {
                        overflow: cs.overflow,
                        overflowX: cs.overflowX,
                        overflowY: cs.overflowY,
                        height: cs.height,
                        maxHeight: cs.maxHeight,
                        minHeight: cs.minHeight,
                        position: cs.position,
                        touchAction: cs.touchAction,
                        scrollHeight: el.scrollHeight,
                        clientHeight: el.clientHeight,
                        scrollTop: el.scrollTop
                    };
                };

                results.html = getStyles(document.documentElement);
                results.body = getStyles(document.body);
                results.workspace = getStyles(document.getElementById('cora-workspace'));
                results.main = getStyles(document.querySelector('.cora-main'));
                results.contentWrapper = getStyles(document.querySelector('.cora-content-wrapper'));
                
                // Find all scrollable containers on the page
                const allElements = Array.from(document.querySelectorAll('*'));
                const scrollables = [];
                for (const el of allElements) {
                    const cs = window.getComputedStyle(el);
                    if ((cs.overflowY === 'auto' || cs.overflowY === 'scroll') && el.scrollHeight > el.clientHeight + 50) {
                        scrollables.push({
                            tag: el.tagName,
                            id: el.id,
                            className: el.className.toString().slice(0, 50),
                            scrollHeight: el.scrollHeight,
                            clientHeight: el.clientHeight,
                            overflowY: cs.overflowY,
                            touchAction: cs.touchAction
                        });
                    }
                }
                results.scrollableContainers = scrollables;

                // Tour backdrop check
                const tourBackdrop = document.querySelector('.cora-tour-backdrop');
                results.tourBackdrop = tourBackdrop ? {
                    display: window.getComputedStyle(tourBackdrop).display,
                    opacity: window.getComputedStyle(tourBackdrop).opacity,
                    pointerEvents: window.getComputedStyle(tourBackdrop).pointerEvents,
                    className: tourBackdrop.className
                } : null;

                // Customizer backdrop check
                const customizerBackdrop = document.querySelector('#cora-customizer-backdrop');
                results.customizerBackdrop = customizerBackdrop ? {
                    display: window.getComputedStyle(customizerBackdrop).display,
                    pointerEvents: window.getComputedStyle(customizerBackdrop).pointerEvents,
                    className: customizerBackdrop.className
                } : null;

                // Any full screen fixed overlays intercepting clicks
                const overlays = [];
                for (const el of allElements) {
                    const cs = window.getComputedStyle(el);
                    if (cs.position === 'fixed' && cs.pointerEvents !== 'none' && cs.display !== 'none' && cs.visibility !== 'hidden') {
                        const rect = el.getBoundingClientRect();
                        if (rect.width >= window.innerWidth && rect.height >= window.innerHeight * 0.8) {
                            overlays.push({
                                tag: el.tagName,
                                id: el.id,
                                className: el.className.toString().slice(0, 60),
                                zIndex: cs.zIndex,
                                opacity: cs.opacity
                            });
                        }
                    }
                }
                results.fullScreenOverlays = overlays;

                return results;
            }""")

            print("Diagnostics for", url)
            import pprint
            pprint.pprint(diag)

            # Test touch scroll
            await page.mouse.move(200, 500)
            await page.mouse.down()
            await page.mouse.move(200, 150, steps=10)
            await page.mouse.up()
            await page.wait_for_timeout(500)

            scroll_res = await page.evaluate("() => ({ windowY: window.scrollY, docTop: document.documentElement.scrollTop, bodyTop: document.body.scrollTop, mainTop: document.querySelector('.cora-main') ? document.querySelector('.cora-main').scrollTop : null })")
            print("Scroll result after drag:", scroll_res)

        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
