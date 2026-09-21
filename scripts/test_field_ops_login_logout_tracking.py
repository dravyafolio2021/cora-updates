import sys
import time
from playwright.sync_api import sync_playwright

def run_test():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            permissions=['geolocation'],
            geolocation={'latitude': 12.9716, 'longitude': 77.5946}
        )
        page = context.new_page()

        print("[1] Navigating to login page...")
        page.goto("http://cora.local/workspace/login", wait_until="networkidle")

        print("[2] Logging in as Photography Studio Owner...")
        page.fill('#login-email', 'owner.studio@cora.local')
        page.fill('#login-password', 'cora_secure_pass_123')
        page.click('#login-submit-btn, button[type="submit"]')
        time.sleep(3)
        page.wait_for_load_state("networkidle")

        print(f"Current URL after login: {page.url}")

        # Check tracking state on dashboard
        tracking_status = page.evaluate("""() => {
            return {
                currentUserId: window.coraCurrentUserId || (window.coraREData && window.coraREData.currentUserId),
                isLoggedIn: (window.coraREData && window.coraREData.isLoggedIn),
                fieldOpsLoaded: typeof window.CoraFieldOps !== 'undefined',
                isTrackingActive: sessionStorage.getItem('cora_employee_tracking_active'),
                trackingUserId: localStorage.getItem('cora_tracking_user_id'),
                hasWatchId: window.CoraFieldOps ? (window.CoraFieldOps.watchId !== null) : false
            };
        }""")
        print(f"[3] Telemetry Status on Dashboard: {tracking_status}")
        assert tracking_status['fieldOpsLoaded'], "CoraFieldOps is not loaded!"
        assert tracking_status['currentUserId'] > 0, f"Current User ID is not set! ({tracking_status['currentUserId']})"
        assert tracking_status['isTrackingActive'] == '1', f"Employee tracking is not active in sessionStorage! ({tracking_status['isTrackingActive']})"

        # Navigate to Users tab / Field Ops & Route Tracker
        print("[4] Navigating to Users & Field Ops tab...")
        page.goto("http://cora.local/workspace/dashboard?sub_page=users&tab=tab-field-ops-tracking", wait_until="networkidle")
        time.sleep(2)

        # Ensure Field Ops tab is active
        page.evaluate("""() => {
            $('.cora-tab-content').addClass('hidden');
            $('#tab-field-ops-tracking').removeClass('hidden');
            if (window.CoraFieldOps && typeof window.CoraFieldOps.init === 'function') {
                window.CoraFieldOps.init();
            }
        }""")
        time.sleep(1.5)

        # Check subtabs and generate demo telemetry if needed
        print("[5] Generating high-precision demo field telemetry...")
        page.evaluate("""() => {
            if (window.CoraFieldOps && typeof window.CoraFieldOps.simulateDemoShift === 'function') {
                window.CoraFieldOps.simulateDemoShift();
            }
        }""")
        time.sleep(4)

        # Verify Field Ops Map and Live Crew view
        field_ops_state = page.evaluate("""() => {
            return {
                mapInitialized: window.CoraFieldOps && window.CoraFieldOps.map !== null,
                activeSubTab: window.CoraFieldOps ? window.CoraFieldOps.activeSubTab : null,
                hasRouteData: window.CoraFieldOps && window.CoraFieldOps.currentRouteData !== null
            };
        }""")
        print(f"[6] Field Ops UI State: {field_ops_state}")
        page.screenshot(path="/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output/field_ops_route_map_verified.png", full_page=True)

        # Switch to Live Crew subtab
        page.evaluate("""() => {
            if (window.CoraFieldOps && typeof window.CoraFieldOps.switchSubTab === 'function') {
                window.CoraFieldOps.switchSubTab('field-ops-panel-crew');
            }
        }""")
        time.sleep(1)
        page.screenshot(path="/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/test_output/field_ops_live_crew_verified.png")

        # Now test logout
        print("[7] Triggering logout...")
        page.evaluate("window.coraLogout()")
        time.sleep(3)
        page.wait_for_load_state("networkidle")
        print(f"URL after logout: {page.url}")

        # Check tracking stopped
        after_logout_state = page.evaluate("""() => {
            return {
                isTrackingActive: sessionStorage.getItem('cora_employee_tracking_active'),
                trackingUserId: localStorage.getItem('cora_tracking_user_id'),
                hasWatchId: window.CoraFieldOps ? (window.CoraFieldOps.watchId !== null) : false
            };
        }""")
        print(f"[8] Tracking State After Logout: {after_logout_state}")
        assert after_logout_state['isTrackingActive'] is None, "Tracking session was not removed on logout!"
        assert after_logout_state['trackingUserId'] is None, "Tracking user was not removed on logout!"

        print("=== TEST PASSED: Auto-tracking on login and auto-stop on logout verified! ===")
        browser.close()

if __name__ == '__main__':
    run_test()
