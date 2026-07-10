# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: tier6-new-refinements.spec.ts >> Tier 6: New Refinements E2E Tests >> 5. Model Context Protocol (MCP) Server Validation
- Location: tests/e2e/tier6-new-refinements.spec.ts:136:7

# Error details

```
Test timeout of 90000ms exceeded while running "beforeEach" hook.
```

```
Error: page.waitForURL: Test timeout of 90000ms exceeded.
=========================== logs ===========================
waiting for navigation until "load"
============================================================
```

# Page snapshot

```yaml
- generic [ref=e1]:
  - heading "Log In" [level=1] [ref=e2]
  - generic [ref=e3]:
    - link "Cora for Real Estate" [ref=e4] [cursor=pointer]:
      - /url: http://cora.local
    - text: Cora for Real Estate
    - generic [ref=e5]:
      - paragraph [ref=e6]:
        - generic [ref=e7]: Username or Email Address
        - textbox "Username or Email Address" [ref=e8]: cora_secure_pass_123
      - generic [ref=e9]:
        - generic [ref=e10]: Password
        - generic [ref=e11]:
          - textbox "Password" [active] [ref=e12]
          - button "Show password" [ref=e13] [cursor=pointer]:
            - generic [ref=e14]: 
      - paragraph [ref=e15]:
        - checkbox "Remember Me" [ref=e16] [cursor=pointer]
        - generic [ref=e17]: Remember Me
      - paragraph:
        - button "Log In" [ref=e18] [cursor=pointer]
    - paragraph [ref=e19]:
      - link "Register" [ref=e20] [cursor=pointer]:
        - /url: http://cora.local/wp-login.php?action=register
      - text: "|"
      - link "Lost your password?" [ref=e21] [cursor=pointer]:
        - /url: http://cora.local/wp-login.php?action=lostpassword
```

# Test source

```ts
  1  | import { Page } from '@playwright/test';
  2  | 
  3  | export async function login(page: Page) {
  4  |   await page.goto('/wp-login.php');
  5  |   await page.fill('#user_login', 'cora_admin');
  6  |   await page.fill('#user_pass', 'cora_secure_pass_123');
  7  |   await page.click('#wp-submit');
> 8  |   await page.waitForURL(url => url.pathname.includes('/wp-admin') || url.pathname.includes('/workspace'));
     |              ^ Error: page.waitForURL: Test timeout of 90000ms exceeded.
  9  | }
  10 | 
  11 | 
```