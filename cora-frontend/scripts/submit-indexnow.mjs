import fs from 'node:fs';
import path from 'node:path';

const projectRoot = process.cwd();
const sitemapPath = path.join(projectRoot, 'out', 'sitemap.xml');
const keyFileName = 'cora-studio-indexnow-2026.txt';
const keyPath = path.join(projectRoot, 'public', keyFileName);

if (!fs.existsSync(sitemapPath)) throw new Error('Missing out/sitemap.xml. Run npm run build first.');
if (!fs.existsSync(keyPath)) throw new Error(`Missing public/${keyFileName}.`);

const key = fs.readFileSync(keyPath, 'utf8').trim();
const requestedUrls = process.argv.slice(2);
const sitemap = fs.readFileSync(sitemapPath, 'utf8');
const sitemapArticleUrls = [...sitemap.matchAll(/<loc>(https:\/\/heycora\.in\/articles\/[^<]+)<\/loc>/g)].map((match) => match[1]);
const urlList = requestedUrls.length > 0 ? requestedUrls : sitemapArticleUrls;

if (urlList.length === 0) throw new Error('No article URLs were provided or found in the generated sitemap.');
for (const url of urlList) {
  if (!url.startsWith('https://heycora.in/articles/')) throw new Error(`Refusing non-article URL: ${url}`);
}

const response = await fetch('https://api.indexnow.org/indexnow', {
  method: 'POST',
  headers: { 'content-type': 'application/json; charset=utf-8' },
  body: JSON.stringify({
    host: 'heycora.in',
    key,
    keyLocation: `https://heycora.in/${keyFileName}`,
    urlList,
  }),
});

if (!response.ok && response.status !== 202) {
  throw new Error(`IndexNow returned HTTP ${response.status}: ${await response.text()}`);
}

process.stdout.write(`IndexNow accepted ${urlList.length} article URL(s) with HTTP ${response.status}.\n`);
