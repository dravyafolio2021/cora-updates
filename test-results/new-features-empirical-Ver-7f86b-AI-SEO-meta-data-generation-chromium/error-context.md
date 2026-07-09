# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: new-features-empirical.spec.ts >> Verify New Features Empirically >> 3. 3rd-party Sync & AI SEO meta-data generation
- Location: tests/e2e/new-features-empirical.spec.ts:111:7

# Error details

```
Test timeout of 90000ms exceeded.
```

```
Error: page.fill: Test timeout of 90000ms exceeded.
Call log:
  - waiting for locator('#user_login')

```

# Page snapshot

```yaml
- generic [active] [ref=e1]:
  - text: "Fatal error: Cannot redeclare cora_ajax_resend_verification() (previously declared in /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php:583) in /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php on line 6393"
  - generic [ref=e2]:
    - paragraph [ref=e3]:
      - text: There has been a critical error on this website. Please check your site admin email inbox for instructions. If you continue to have problems, please try the
      - link "support forums" [ref=e4] [cursor=pointer]:
        - /url: https://wordpress.org/support/forums/
      - text: .
    - paragraph [ref=e5]:
      - link "Learn more about troubleshooting WordPress." [ref=e6] [cursor=pointer]:
        - /url: https://wordpress.org/documentation/article/faq-troubleshooting/
```

# Test source

```ts
  1  | import { Page } from '@playwright/test';
  2  | 
  3  | export async function login(page: Page) {
  4  |   await page.goto('/wp-login.php');
> 5  |   await page.fill('#user_login', 'cora_admin');
     |              ^ Error: page.fill: Test timeout of 90000ms exceeded.
  6  |   await page.fill('#user_pass', 'cora_secure_pass_123');
  7  |   await page.click('#wp-submit');
  8  |   await page.waitForURL(url => url.pathname.includes('/wp-admin') || url.pathname.includes('/workspace'));
  9  | }
  10 | 
  11 | 
```