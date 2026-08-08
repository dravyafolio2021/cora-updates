const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true, args: ['--disable-web-security'] });
  const page = await browser.newPage();
  await page.goto('http://cora.local/wp-login.php');

  const isDocWorkspace = (urlStr) => {
    return urlStr.includes('/wp-admin') || (urlStr.includes('/workspace') && !urlStr.includes('/login') && !urlStr.includes('/register') && !urlStr.includes('/forgot-password') && !urlStr.includes('/reset-password'));
  };

  try {
    await Promise.race([
      page.waitForSelector('#login-email', { timeout: 5000 }),
      page.waitForSelector('#user_login', { timeout: 5000 })
    ]);
    const customEmail = await page.$('#login-email');
    if (customEmail) {
      await page.fill('#login-email', 'owner.studio@cora.local');
      await page.fill('#login-password', 'cora_secure_pass_123');
      await page.click('#login-btn');
    } else {
      await page.fill('#user_login', 'studio_owner');
      await page.fill('#user_pass', 'cora_secure_pass_123');
      await page.click('#wp-submit');
    }
  } catch (e) {}
  try {
    await page.waitForURL(url => isDocWorkspace(url.href), { timeout: 20000 });
  } catch (e) {}

  await page.goto('http://cora.local/workspace/canvas');
  await page.waitForSelector('#website-statistics-card', { state: 'visible', timeout: 10000 });

  const matchedRules = await page.evaluate(() => {
    const el = document.querySelector('#website-statistics-card .space-y-1 > div'); // Row 0
    const matched = [];

    function checkRule(rule, href) {
      if (rule.selectorText) {
        if (el.matches(rule.selectorText)) {
          // Check if it defines border or shadow
          const style = rule.style;
          if (style.border || style.borderBottom || style.borderBottomWidth || style.boxShadow || style.borderRadius) {
            matched.push({
              selector: rule.selectorText,
              cssText: rule.cssText,
              href: href
            });
          }
        }
      } else if (rule.cssRules) {
        // Grouping rule (e.g. @media)
        for (const childRule of Array.from(rule.cssRules)) {
          checkRule(childRule, href);
        }
      }
    }
    
    for (const sheet of Array.from(document.styleSheets)) {
      const sheetHref = sheet.href || 'inline';
      try {
        const rulesList = sheet.cssRules || sheet.rules;
        if (!rulesList) continue;
        for (const rule of Array.from(rulesList)) {
          checkRule(rule, sheetHref);
        }
      } catch (e) {
        matched.push({ error: e.message, href: sheetHref });
      }
    }
    return matched;
  });

  console.log('Recursive rules matching Row 0:');
  console.log(JSON.stringify(matchedRules, null, 2));

  await browser.close();
})();
