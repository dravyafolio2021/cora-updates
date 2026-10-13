#!/usr/bin/env node
/** Validate the single source and the new contract surface without failing legacy UI. */
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
import { fileURLToPath } from 'node:url';
import ts from 'typescript';

const frontend = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const repository = path.resolve(frontend, '..');
const errors = [];
const fail = (message) => errors.push(message);
const args = process.argv.slice(2);
const requestedFiles = [];
if (args.length) {
  if (args[0] !== '--files' || args.length < 2 || args.slice(1).some((arg) => arg.startsWith('--'))) {
    fail('Usage: npm run check:design-system -- --files components/Feature.tsx components/Feature.module.css');
  } else {
    for (const requested of args.slice(1)) {
      const absolute = path.resolve(frontend, requested);
      if (!fs.existsSync(absolute) || !fs.statSync(absolute).isFile()) { fail(`Explicit target is not a file: ${requested}`); continue; }
      const real = fs.realpathSync(absolute);
      if (!real.startsWith(`${repository}${path.sep}`) || /(?:^|[\\/])(?:node_modules|\.git)(?:[\\/]|$)/.test(real) || !/\.(?:tsx?|jsx?|mjs|css|php)$/.test(real)) {
        fail(`Explicit target must be a repository UI source file: ${requested}`); continue;
      }
      const relative = path.relative(frontend, real);
      if (relative === 'lib/design-system.ts') continue; // Authoritative token literals are validated separately.
      requestedFiles.push(relative);
    }
  }
}

// Reviewed content exceptions only: exact file + rule + literal, rationale and canonical source.
// No exemptions currently exist. Never add file-wide ignores or exemptions for dialogs,
// unknown tokens, inaccessible contrast, or selected-card perimeter styling.
const STYLE_EXCEPTIONS = [];
const exceptionRules = new Set(['color-literal', 'fixed-color', 'font-family']);
for (const exception of STYLE_EXCEPTIONS) {
  if (!exception.file || /[*?]/.test(exception.file) || !exceptionRules.has(exception.rule) || !exception.literal || !exception.reason || !exception.source) {
    fail('Every reviewed style exception needs file, permitted rule, exact literal, reason, and canonical source.');
  }
}
const reportStyle = (relative, line, rule, literal, message) => {
  if (!STYLE_EXCEPTIONS.some((item) => item.file === relative && item.rule === rule && item.literal === literal)) {
    fail(`${relative}:${line}: ${message}`);
  }
};
const read = (relative) => {
  const absolute = path.join(frontend, relative);
  if (!fs.existsSync(absolute)) { fail(`Missing ${relative}`); return ''; }
  return fs.readFileSync(absolute, 'utf8');
};
const escapeRegex = (value) => value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
const source = read('lib/design-system.ts');
let contract;
let css = '';
let markdown = '';

if (source) {
  try {
    // Execute only this standalone data module, with no file, network, or import access.
    const compiled = ts.transpileModule(source, {
      compilerOptions: { module: ts.ModuleKind.CommonJS, target: ts.ScriptTarget.ES2020 },
      reportDiagnostics: true,
    });
    const diagnostics = (compiled.diagnostics || []).filter((item) => item.category === ts.DiagnosticCategory.Error);
    if (diagnostics.length) throw new Error(diagnostics.map((item) => ts.flattenDiagnosticMessageText(item.messageText, '\n')).join('\n'));
    const context = vm.createContext({ exports: {} });
    vm.runInContext(compiled.outputText, context, { timeout: 2000, filename: 'design-system.ts' });
    const result = vm.runInContext(`JSON.stringify({
      contract: exports.designSystem,
      css: exports.designSystemCss(),
      markdown: exports.designSystemMarkdown()
    })`, context, { timeout: 2000 });
    ({ contract, css, markdown } = JSON.parse(result));
    if (!contract || typeof contract !== 'object') throw new Error('Export designSystem must be a serializable object.');
    if (!/^\d+\.\d+\.\d+$/.test(contract.version ?? '')) fail('designSystem.version must use semantic versioning.');
    if (!Array.isArray(contract.tokens) || !contract.tokens.length) fail('designSystem.tokens must be a non-empty array.');
    if (typeof css !== 'string' || !css.includes('--cora-')) fail('designSystemCss() must generate namespaced CSS.');
    if (typeof markdown !== 'string' || !markdown.includes(contract.version)) fail('designSystemMarkdown() must include the contract version.');
  } catch (error) { fail(`Cannot load canonical contract: ${error.message}`); }
}

const tokenNames = new Set();
for (const token of contract?.tokens ?? []) {
  if (!/^--cora-[a-z0-9-]+$/.test(token.name ?? '')) fail(`Invalid semantic token name: ${token.name}`);
  if (tokenNames.has(token.name)) fail(`Duplicate token: ${token.name}`);
  tokenNames.add(token.name);
  if (typeof token.value !== 'string' || !token.value.trim() || /[{};]/.test(token.value)) fail(`Invalid CSS value: ${token.name}`);
  if (!token.category || !token.usage) fail(`Token ${token.name} needs category and usage guidance.`);
  const declaration = new RegExp(`${escapeRegex(token.name)}\\s*:\\s*${escapeRegex(token.value)}\\s*;`);
  if (!declaration.test(css)) fail(`Generated CSS does not contain the canonical value of ${token.name}.`);
}

function requiredText(object, fields, label) {
  for (const field of fields) if (typeof object?.[field] !== 'string' || !object[field].trim()) fail(`${label} needs non-empty ${field}.`);
}
function requiredTextList(object, fields, label) {
  for (const field of fields) {
    if (!Array.isArray(object?.[field]) || !object[field].length || object[field].some((entry) => typeof entry !== 'string' || !entry.trim())) {
      fail(`${label} needs non-empty ${field}.`);
    }
  }
}
function verifyCatalogSource(reference, label) {
  if (typeof reference !== 'string' || !reference.trim()) return;
  const relative = reference.startsWith('cora-frontend/') ? reference.slice('cora-frontend/'.length) : reference;
  const absolute = reference.startsWith('app/public/') ? path.resolve(repository, reference) : path.resolve(frontend, relative);
  if (!absolute.startsWith(`${repository}${path.sep}`) || /(?:^|[\\/])(?:node_modules|\.git)(?:[\\/]|$)/.test(absolute) || !fs.existsSync(absolute) || !fs.statSync(absolute).isFile()) {
    fail(`${label} references a missing or invalid source file: ${reference}.`);
    return;
  }
  if (!fs.realpathSync(absolute).startsWith(`${repository}${path.sep}`)) fail(`${label} source must remain inside the repository: ${reference}.`);
}
if (contract) {
  requiredText(contract, ['schemaVersion', 'version', 'updatedAt', 'name', 'description', 'canonicalUrl', 'source'], 'Contract metadata');
  if (!/^\d+\.\d+\.\d+$/.test(contract.schemaVersion ?? '')) fail('schemaVersion must use semantic versioning.');
  if (!/^\d{4}-\d{2}-\d{2}$/.test(contract.updatedAt ?? '')) fail('updatedAt must be an ISO date.');
  const componentIds = new Set();
  if (!Array.isArray(contract.components) || !contract.components.length) fail('Component contracts must be a non-empty array.');
  for (const item of contract.components ?? []) {
    requiredText(item, ['id', 'name', 'level', 'implementation', 'status'], `Component ${item.id}`);
    if (!/^[a-z][a-z0-9-]*$/.test(item.id ?? '') || componentIds.has(item.id)) fail(`Invalid or duplicate stable component ID: ${item.id}`);
    componentIds.add(item.id);
    for (const field of ['rules', 'states']) if (!Array.isArray(item[field]) || !item[field].length || item[field].some((entry) => typeof entry !== 'string' || !entry.trim())) fail(`Component ${item.id} needs non-empty ${field}.`);
    if (!markdown.includes(`### ${item.id}:`)) fail(`Markdown omits stable component ID ${item.id}.`);
  }
  const catalogCategories = new Set(['cards', 'controls', 'overlays', 'content', 'media', 'data']);
  const catalogStatuses = new Set(['reference-recipe', 'shared-primitive']);
  const catalogIds = new Set();
  if (!Array.isArray(contract.catalog) || !contract.catalog.length) fail('Visual catalog must be a non-empty array.');
  for (const item of Array.isArray(contract.catalog) ? contract.catalog : []) {
    const label = `Catalog entry ${item?.id ?? '(missing ID)'}`;
    requiredText(item, ['id', 'category', 'name', 'description', 'status', 'source', 'example'], label);
    requiredTextList(item, ['componentIds', 'variants', 'states', 'usage'], label);
    if (!item || typeof item !== 'object') continue;
    if (!/^[a-z][a-z0-9-]*$/.test(item.id ?? '') || catalogIds.has(item.id)) fail(`Invalid or duplicate stable catalog ID: ${item.id}.`);
    catalogIds.add(item.id);
    if (!catalogCategories.has(item.category)) fail(`${label} has unknown category ${item.category}.`);
    if (!catalogStatuses.has(item.status)) fail(`${label} has unknown implementation status ${item.status}.`);
    for (const id of Array.isArray(item.componentIds) ? item.componentIds : []) {
      if (!componentIds.has(id)) fail(`${label} references unknown component contract ${id}.`);
    }
    if (Array.isArray(item.componentIds) && new Set(item.componentIds).size !== item.componentIds.length) fail(`${label} repeats a component contract reference.`);
    if (item.status === 'shared-primitive' && !contract.components?.some((component) => item.componentIds?.includes(component.id) && component.status === 'implemented')) {
      fail(`${label} must reference an implemented component when marked shared-primitive.`);
    }
    verifyCatalogSource(item.source, label);
    if (!markdown.includes(`### ${item.id}: ${item.name}`)) fail(`Markdown omits catalog ID or name ${item.id}: ${item.name}.`);
  }
  requiredText(contract.catalogGuide, ['renderer', 'previews', 'authority', 'examples', 'sources', 'publishing'], 'Visual catalog guide');
  verifyCatalogSource(contract.catalogGuide?.renderer, 'Visual catalog renderer');
  verifyCatalogSource(contract.catalogGuide?.previews, 'Visual catalog previews');
  const previewSource = read('components/design-system/CatalogPreviews.tsx');
  const previewAst = ts.createSourceFile('CatalogPreviews.tsx', previewSource, ts.ScriptTarget.Latest, true, ts.ScriptKind.TSX);
  const previewLiterals = new Set();
  const collectPreviewIds = node => {
    if (ts.isStringLiteral(node)) previewLiterals.add(node.text);
    ts.forEachChild(node, collectPreviewIds);
  };
  collectPreviewIds(previewAst);
  for (const id of catalogIds) if (!previewLiterals.has(id)) fail(`Catalog entry ${id} has no corresponding preview dispatch. Verify its live specimen.`);
  const stablePrimitives = { button: 'Button', input: 'TextField', badge: 'Badge', card: 'Card', sheet: 'Sheet' };
  const primitiveSource = read('components/design-system/primitives.tsx');
  const primitiveAst = ts.createSourceFile('primitives.tsx', primitiveSource, ts.ScriptTarget.Latest, true, ts.ScriptKind.TSX);
  const exports = new Set(primitiveAst.statements.filter((item) => ts.isFunctionDeclaration(item) && item.modifiers?.some((modifier) => modifier.kind === ts.SyntaxKind.ExportKeyword)).map((item) => item.name?.text));
  const apiNames = new Set();
  if (!Array.isArray(contract.primitiveApi) || !contract.primitiveApi.length) fail('primitiveApi must document shared component imports and props.');
  for (const api of contract.primitiveApi ?? []) {
    requiredText(api, ['name', 'import', 'props', 'example', 'tokens'], `Primitive API ${api.name}`);
    if (apiNames.has(api.name)) fail(`Duplicate primitive API: ${api.name}`);
    apiNames.add(api.name);
    if (!exports.has(api.name)) fail(`Primitive API ${api.name} has no matching production export.`);
    if (api.import !== '@/components/design-system/primitives') fail(`Primitive ${api.name} must use the shared production import.`);
    if (!api.example?.includes(`<${api.name}`)) fail(`Primitive ${api.name} example must use its production component.`);
    for (const name of api.tokens?.match(/--cora-[a-z0-9-]+/g) ?? []) if (!tokenNames.has(name)) fail(`Primitive ${api.name} references unknown token ${name}.`);
  }
  for (const [id, name] of Object.entries(stablePrimitives)) {
    const component = contract.components?.find((item) => item.id === id);
    if (!component || component.status !== 'implemented' || !component.implementation.includes(name) || !apiNames.has(name)) fail(`Stable component ${id} must document the implemented ${name} primitive and API.`);
  }
}

// Verify the generated stylesheet, not only the in-memory theme descriptions.
let contrastChecks = 0;
const luminance = (hex) => {
  if (!/^#[\da-f]{6}$/i.test(hex ?? '')) return NaN;
  const channels = [1, 3, 5].map((offset) => {
    const value = parseInt(hex.slice(offset, offset + 2), 16) / 255;
    return value <= 0.04045 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4;
  });
  return channels[0] * 0.2126 + channels[1] * 0.7152 + channels[2] * 0.0722;
};
const pairs = [
  ...['bg', 'surface', 'selected'].flatMap((background) => ['text', 'muted'].map((foreground) => [foreground, background, 4.5])),
  ['subtle', 'bg', 4.5], ['subtle', 'surface', 4.5],
  ['on-action', 'action', 4.5], ['on-action', 'action-hover', 4.5],
  ...['success', 'warning', 'danger'].map((state) => [state, `${state}-bg`, 4.5]),
  ...['bg', 'surface', 'selected'].flatMap((background) => ['focus', 'border-strong'].map((foreground) => [foreground, background, 3])),
];
for (const theme of ['light', 'dark']) {
  const values = contract?.themes?.[theme];
  const rule = css.match(new RegExp(`\\[data-cora-theme=["']${theme}["']\\]\\s*\\{([^}]*)\\}`));
  if (!values || !rule) { fail(`Missing generated ${theme} semantic theme.`); continue; }
  const generated = Object.fromEntries([...rule[1].matchAll(/--cora-([a-z0-9-]+)\s*:\s*([^;]+);/g)].map((match) => [match[1], match[2].trim()]));
  for (const [name, value] of Object.entries(values)) {
    if (!tokenNames.has(`--cora-${name}`)) fail(`${theme} theme defines unknown token --cora-${name}.`);
    if (generated[name] !== value) fail(`Generated ${theme} theme differs from canonical ${name}.`);
  }
  for (const [foreground, background, minimum] of pairs) {
    const first = luminance(generated[foreground]);
    const second = luminance(generated[background]);
    const ratio = (Math.max(first, second) + 0.05) / (Math.min(first, second) + 0.05);
    contrastChecks += 1;
    if (!Number.isFinite(ratio) || ratio < minimum) fail(`${theme} ${foreground} on ${background}: ${Number.isFinite(ratio) ? ratio.toFixed(2) : 'invalid color'} contrast; needs ${minimum}:1.`);
  }
}

const routes = [
  ['app/design-system/manifest.json/route.ts', 'designSystem'],
  ['app/design-system/llms.txt/route.ts', 'designSystemMarkdown'],
  ['app/design-system/tokens.css/route.ts', 'designSystemCss'],
];
const endpointFields = ['manifest', 'instructions', 'stylesheet'];
routes.forEach(([relative], index) => {
  const expected = `/${relative.replace(/^app\//, '').replace(/\/route\.ts$/, '')}`;
  if (contract && contract.endpoints?.[endpointFields[index]] !== expected) fail(`Manifest ${endpointFields[index]} endpoint must match ${expected}.`);
});
for (const [relative, exported] of routes) {
  const route = read(relative);
  const derives = exported === 'designSystem'
    ? /(?:json|stringify)\(\s*designSystem\b/.test(route)
    : new RegExp(`\\b${exported}\\s*\\(`).test(route);
  if (route && (!/from\s+['"][^'"]*lib\/design-system['"]/.test(route) || !derives)) {
    fail(`${relative} must derive from ${exported} in lib/design-system.ts.`);
  }
  if (route && !/export\s+const\s+dynamic\s*=\s*['"]force-static['"]/.test(route)) fail(`${relative} must remain available in the static export.`);
  if (route && /#[\da-f]{3,8}\b|\b(?:rgb|rgba|hsl|hsla)\(/i.test(route)) fail(`${relative} must not maintain independent color values.`);
}
const layout = read('app/layout.tsx');
if (layout && !/\bdesignSystemCss\(/.test(layout)) fail('app/layout.tsx must expose generated designSystemCss() tokens.');
read('app/design-system/page.tsx');

function filesIn(relative) {
  const absolute = path.join(frontend, relative);
  if (!fs.existsSync(absolute)) { fail(`Missing ${relative}`); return []; }
  return fs.readdirSync(absolute, { withFileTypes: true }).flatMap((entry) => {
    const child = path.join(relative, entry.name);
    return entry.isDirectory() ? filesIn(child) : /\.(?:tsx?|css)$/.test(entry.name) ? [child] : [];
  });
}
const scopedFiles = [...new Set([...filesIn('app/design-system'), ...filesIn('components/design-system'), ...requestedFiles])];
for (const relative of scopedFiles) {
  const contents = read(relative).replace(/\/\*[\s\S]*?\*\//g, (comment) => comment.replace(/[^\n]/g, ' '));
  const lines = contents.split('\n');
  lines.forEach((line, index) => {
    const location = `${relative}:${index + 1}`;
    for (const match of line.matchAll(/#[\da-f]{3,8}\b|\b(?:rgb|rgba|hsl|hsla|oklch|oklab|lab|lch|color)\([^)]*\)/gi)) {
      reportStyle(relative, index + 1, 'color-literal', match[0], 'use a semantic token instead of a color literal.');
    }
    for (const match of line.matchAll(/\b(?:bg|text|border|ring|fill|stroke|shadow)-(?:white|black|gray|zinc|neutral|slate|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)(?:-\d{2,3})?\b/g)) {
      reportStyle(relative, index + 1, 'fixed-color', match[0], 'use a semantic token instead of a fixed Tailwind color.');
    }
    for (const match of line.matchAll(/(?:color|background(?:-?color)?|border(?:-?color)?|fill|stroke|outline(?:-?color)?)\s*:\s*['"]?(white|black|red|blue|green|yellow|orange|purple|pink|gray|grey|silver|gold|navy|teal|cyan|magenta|lime|maroon|olive|aqua|fuchsia)\b/gi)) {
      reportStyle(relative, index + 1, 'color-literal', match[1], 'use a semantic token instead of a named color.');
    }
    for (const font of line.matchAll(/(?:font-family\s*:|fontFamily\s*:)\s*([^;}]*)(?:;|}|$)/g)) {
      if (!/var\(|inherit|unset/.test(font[1])) reportStyle(relative, index + 1, 'font-family', font[1].trim(), 'font families must derive from tokens or inherit.');
    }
    for (const name of new Set(line.match(/--cora-[a-z0-9][a-z0-9-]*/g) ?? [])) {
      if (!tokenNames.has(name)) fail(`${location}: unknown canonical token ${name}.`);
    }
  });
  if (!relative.endsWith('.css')) {
    const ast = ts.createSourceFile(relative, contents, ts.ScriptTarget.Latest, true, relative.endsWith('x') ? ts.ScriptKind.TSX : ts.ScriptKind.JS);
    const visit = (node) => {
      const colorProperties = new Set(['color', 'background', 'backgroundColor', 'borderColor', 'outlineColor', 'fill', 'stroke']);
      const staticColor = ts.isPropertyAssignment(node) && colorProperties.has(node.name.getText(ast).replace(/['"]/g, '')) && ts.isStringLiteral(node.initializer)
        ? node.initializer.text
        : ts.isJsxAttribute(node) && colorProperties.has(node.name.getText(ast)) && node.initializer && ts.isStringLiteral(node.initializer)
          ? node.initializer.text : undefined;
      if (staticColor && /^[a-z]+$/i.test(staticColor) && !/^(?:transparent|currentColor|inherit|initial|unset|revert|none)$/i.test(staticColor)) {
        reportStyle(relative, ast.getLineAndCharacterOfPosition(node.getStart(ast)).line + 1, 'color-literal', staticColor, 'use a semantic token instead of a named color.');
      }
      if (ts.isCallExpression(node)) {
        const called = node.expression;
        let dialog;
        if (ts.isIdentifier(called)) dialog = called.text;
        if (ts.isPropertyAccessExpression(called) && ['window', 'globalThis'].includes(called.expression.getText(ast))) dialog = called.name.text;
        if (ts.isElementAccessExpression(called) && ['window', 'globalThis'].includes(called.expression.getText(ast)) && ts.isStringLiteral(called.argumentExpression)) dialog = called.argumentExpression.text;
        if (['alert', 'confirm', 'prompt'].includes(dialog)) fail(`${relative}:${ast.getLineAndCharacterOfPosition(node.getStart(ast)).line + 1}: use the host toast or custom confirmation surface instead of native ${dialog}().`);
      }
      if (ts.isConditionalExpression(node) && /selected|active|checked/i.test(node.condition.getText(ast))) {
        for (const branch of [node.whenTrue, node.whenFalse]) {
          for (const match of branch.getText(ast).matchAll(/(?:[a-z-]+:)*(?:border-[2-9]|ring-[2-9]|border-\[[^\]]+\])/g)) {
            if (!/focus(?:-visible|-within)?:/.test(match[0])) fail(`${relative}:${ast.getLineAndCharacterOfPosition(branch.getStart(ast)).line + 1}: selected surfaces need tonal fills and subtle structural borders, not ${match[0]}.`);
          }
        }
      }
      ts.forEachChild(node, visit);
    };
    visit(ast);
  }
  for (const block of contents.matchAll(/([^{}]+)\{([^{}]*)\}/g)) {
    const selector = block[1];
    if (!/(?:\.(?:selected|active|checked)\b|\[(?:aria-selected|aria-pressed|data-selected)\s*=\s*['"]?true|\[data-state\s*=\s*['"]?(?:selected|active|checked)|:checked\b)/i.test(selector) || /:focus(?:-visible|-within)?\b/.test(selector)) continue;
    const declaration = block[2];
    const heavy = [...declaration.matchAll(/(?:border(?:-(?:width|top|right|bottom|left))?|outline)\s*:\s*(\d*\.?\d+)(px|rem|em)\b/g)].some((match) => Number(match[1]) * (match[2] === 'px' ? 1 : 16) > 1);
    const highContrast = /border(?:-color)?\s*:[^;]*var\(--cora-(?:text|action|on-action)\)/.test(declaration);
    if (heavy || highContrast) fail(`${relative}:${contents.slice(0, block.index).split('\n').length}: selected surfaces need tonal fills and subtle structural borders; keep keyboard focus indicators separate.`);
  }
}

if (errors.length) {
  console.error(`Design-system check failed (${errors.length}):\n${errors.map((message) => `- ${message}`).join('\n')}`);
  process.exitCode = 1;
} else {
  console.log(`Design system ${contract.version}: ${tokenNames.size} tokens, ${contrastChecks} light/dark contrast pairs, ${contract.components.length} component contracts, ${contract.catalog.length} visual catalog entries, 3 shared exports, and ${scopedFiles.length} scoped files verified.`);
  console.log(`Explicit feature targets: ${requestedFiles.length}. Unselected legacy styles are outside this check. Verify actual states, keyboard access, and responsive behavior.`);
}
