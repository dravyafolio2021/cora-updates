import fs from 'node:fs';
import path from 'node:path';

const projectRoot = process.cwd();
const outRoot = path.join(projectRoot, 'out');
const articlesRoot = path.join(outRoot, 'articles');
const sitemapPath = path.join(outRoot, 'sitemap.xml');
const robotsPath = path.join(outRoot, 'robots.txt');

const errors = [];
const warnings = [];

if (!fs.existsSync(articlesRoot)) errors.push('Missing out/articles. Run the production build first.');
if (!fs.existsSync(sitemapPath)) errors.push('Missing out/sitemap.xml.');
if (!fs.existsSync(robotsPath)) errors.push('Missing out/robots.txt.');

const sitemap = fs.existsSync(sitemapPath) ? fs.readFileSync(sitemapPath, 'utf8') : '';
const robots = fs.existsSync(robotsPath) ? fs.readFileSync(robotsPath, 'utf8') : '';

for (const crawler of ['Googlebot', 'Bingbot', 'OAI-SearchBot', 'PerplexityBot']) {
  if (!robots.includes(`User-agent: ${crawler}`)) errors.push(`robots.txt is missing ${crawler}.`);
}

const articleFiles = [];
if (fs.existsSync(articlesRoot)) {
  for (const category of fs.readdirSync(articlesRoot, { withFileTypes: true })) {
    if (!category.isDirectory()) continue;
    const categoryPath = path.join(articlesRoot, category.name);
    for (const slug of fs.readdirSync(categoryPath, { withFileTypes: true })) {
      if (!slug.isDirectory()) continue;
      const htmlPath = path.join(categoryPath, slug.name, 'index.html');
      if (fs.existsSync(htmlPath)) articleFiles.push({ category: category.name, slug: slug.name, htmlPath });
    }
  }
}

if (articleFiles.length === 0) errors.push('No generated article pages were found.');

const seenTitles = new Map();
const seenCanonicals = new Map();
const riskyClaimPatterns = [
  /\b100% compliant\b/i,
  /\bguarantee(?:d|s)?\b/i,
  /\bstrictly compl(?:y|ies|iant)\b/i,
  /\blegally admissible\b/i,
  /\bjoin\s+[\d,]+\+\b/i,
  /\b8-figure\b/i,
];

for (const article of articleFiles) {
  const html = fs.readFileSync(article.htmlPath, 'utf8');
  const expectedUrl = `https://heycora.in/articles/${article.category}/${article.slug}/`;
  const title = html.match(/<title>(.*?)<\/title>/s)?.[1]?.trim();
  const description = html.match(/<meta name="description" content="(.*?)"\/>/s)?.[1]?.trim();
  const canonical = html.match(/<link rel="canonical" href="(.*?)"\/>/s)?.[1]?.trim();
  const h1Count = (html.match(/<h1\b/g) || []).length;

  if (!title) errors.push(`${article.category}/${article.slug}: missing title.`);
  if (!description) errors.push(`${article.category}/${article.slug}: missing description.`);
  if (canonical !== expectedUrl) errors.push(`${article.category}/${article.slug}: canonical is ${canonical || 'missing'}, expected ${expectedUrl}.`);
  if (h1Count !== 1) errors.push(`${article.category}/${article.slug}: expected one H1, found ${h1Count}.`);
  if (!html.includes('"@type":"Article"')) errors.push(`${article.category}/${article.slug}: missing Article structured data.`);
  if (!sitemap.includes(`<loc>${expectedUrl}</loc>`)) errors.push(`${article.category}/${article.slug}: missing from sitemap.`);

  if (title) {
    if (seenTitles.has(title)) errors.push(`${article.category}/${article.slug}: duplicate title also used by ${seenTitles.get(title)}.`);
    seenTitles.set(title, `${article.category}/${article.slug}`);
    if (title.replace(/&amp;/g, '&').length > 65) warnings.push(`${article.category}/${article.slug}: title is longer than 65 characters.`);
  }
  if (description && description.replace(/&amp;/g, '&').length > 165) warnings.push(`${article.category}/${article.slug}: description is longer than 165 characters.`);
  if (canonical) {
    if (seenCanonicals.has(canonical)) errors.push(`${article.category}/${article.slug}: duplicate canonical also used by ${seenCanonicals.get(canonical)}.`);
    seenCanonicals.set(canonical, `${article.category}/${article.slug}`);
  }

  for (const pattern of riskyClaimPatterns) {
    if (pattern.test(html)) {
      warnings.push(`${article.category}/${article.slug}: review risky absolute claim matching ${pattern}.`);
      break;
    }
  }
}

for (const warning of warnings) process.stderr.write(`WARNING: ${warning}\n`);
for (const error of errors) process.stderr.write(`ERROR: ${error}\n`);

process.stdout.write(`Validated ${articleFiles.length} generated articles with ${warnings.length} warning(s) and ${errors.length} error(s).\n`);
if (errors.length > 0) process.exit(1);
