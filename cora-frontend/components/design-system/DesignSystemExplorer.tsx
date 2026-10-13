'use client';

import { useMemo, useRef, useState } from 'react';
import Link from 'next/link';
import { ArrowDownToLine, ArrowLeft, ArrowRight, Check, Code2, Copy, Search, SlidersHorizontal, X } from 'lucide-react';
import { designSystem as ds } from '@/lib/design-system';
import { Badge, Button, Card, Sheet, TextField } from './primitives';
import { VisualCatalog, type VisualCatalogHandle } from './VisualCatalog';
import styles from './DesignSystem.module.css';

const sections = [
  ['start', 'Start here'], ['catalog', 'Visual catalog'], ['principles', 'Principles'], ['profiles', 'Surface profiles'],
  ['tokens', 'Design tokens'], ['typography', 'Typography'], ['playground', 'Live components'],
  ['components', 'Component contracts'], ['patterns', 'Product patterns'], ['responsive', 'Responsive layout'],
  ['accessibility', 'Accessibility'], ['content', 'Content & terminology'], ['audit', 'Audit & migration'], ['governance', 'Agent workflow'],
] as const;
const categories = [...new Set(ds.tokens.map(t => t.category))];
function SectionHeader({ number, title, description }: { number: string; title: string; description: string }) {
  return <div className={styles.sectionHeading}><span className={styles.eyebrow}>{number} / REFERENCE</span><h2>{title}</h2><p>{description}</p></div>;
}
function SourceList({ paths }: { paths: readonly string[] }) {
  return <div className={styles.sources}><span>Source references</span>{paths.map(p => <code key={p}>{p}</code>)}</div>;
}

export function DesignSystemExplorer() {
  const catalogRef = useRef<VisualCatalogHandle>(null);
  const [query, setQuery] = useState('');
  const [category, setCategory] = useState('all');
  const [theme, setTheme] = useState('light');
  const [profile, setProfile] = useState('marketing');
  const [variant, setVariant] = useState<'primary' | 'secondary' | 'ghost' | 'danger'>('primary');
  const [fieldState, setFieldState] = useState('default');
  const [projectName, setProjectName] = useState('Autumn campaign');
  const [loading, setLoading] = useState(false);
  const [clicks, setClicks] = useState(0);
  const [copied, setCopied] = useState('');
  const [copyMessage, setCopyMessage] = useState('');
  const [sheetOpen, setSheetOpen] = useState(false);
  const normalized = query.trim().toLowerCase();
  const index = useMemo(() => [
    ...ds.catalog.map(c => ({ id: `specimen-${c.id}`, title: c.name, kind: 'Specimen', text: `${c.description} ${c.usage.join(' ')} ${c.variants.join(' ')} ${c.states.join(' ')}` })),
    ...ds.tokens.map(t => ({ id: `token-${t.name.slice(7)}`, title: t.name, kind: 'Token', text: `${t.value} ${t.category} ${t.usage}` })),
    ...ds.components.map(c => ({ id: `component-${c.id}`, title: c.name, kind: 'Component', text: `${c.rules.join(' ')} ${c.states.join(' ')} ${c.implementation}` })),
    ...ds.principles.map(p => ({ id: 'principles', title: p.title, kind: 'Principle', text: p.detail })),
    ...ds.profiles.map(p => ({ id: 'profiles', title: p.name, kind: 'Profile', text: `${p.description} ${p.sources.join(' ')}` })),
    ...ds.typography.map(t => ({ id: 'typography', title: t.role, kind: 'Typography', text: `${t.token} ${t.usage}` })),
    ...ds.featurePatterns.map(p => ({ id: 'patterns', title: p.name, kind: 'Pattern', text: `${p.rules} ${p.sources.join(' ')}` })),
    ...ds.responsive.map(r => ({ id: 'responsive', title: r.range, kind: 'Layout', text: r.rule })),
    ...ds.accessibility.map((r, i) => ({ id: 'accessibility', title: `Accessibility ${i + 1}`, kind: 'Rule', text: r })),
    ...ds.contentRules.map((r, i) => ({ id: 'content', title: `Content rule ${i + 1}`, kind: 'Rule', text: r })),
    ...ds.deviations.map(d => ({ id: `deviation-${d.id}`, title: d.scope, kind: 'Audit', text: `${d.observed} ${d.decision}` })),
    ...ds.governance.changeProtocol.map((r, i) => ({ id: 'governance', title: `Agent workflow ${i + 1}`, kind: 'Workflow', text: r })),
  ], []);
  const matches = normalized ? index.filter(i => `${i.title} ${i.text}`.toLowerCase().includes(normalized)) : [];
  const visibleTokens = ds.tokens.filter(t => category === 'all' || t.category === category);
  async function copy(value: string, id: string) {
    try {
      await navigator.clipboard.writeText(value);
      setCopied(id); setCopyMessage(`${id === 'prompt' ? 'Agent instructions' : id} copied.`);
    } catch { setCopied(''); setCopyMessage('Copy is unavailable in this browser. Select the text, or use the file download.'); }
  }
  function followResult(id: string) {
    setQuery(''); setCategory('all');
    if (id.startsWith('specimen-')) {
      catalogRef.current?.reveal(id.slice(9));
      window.history.replaceState(null, '', `#${id}`);
      return;
    }
    // Scroll after the results panel is removed and any filtered token is rendered.
    requestAnimationFrame(() => {
      const target = document.getElementById(id);
      target?.scrollIntoView({ behavior: 'instant', block: 'start' });
      target?.focus({ preventScroll: true });
      window.history.replaceState(null, '', `#${id}`);
    });
  }

  return <main className={styles.page} data-cora-profile="marketing">
    <a href="#design-content" className={styles.skip}>Skip to design reference</a>
    <header className={styles.header}>
      <div className={styles.headerInner}>
        <Link href="/" className={styles.brand} aria-label="Cora home">cora<span className={styles.headerDivider} /><span>Design system</span></Link>
        <div className={styles.headerActions}><span className={styles.version}>v{ds.version}</span><Link href="/docs/" className={styles.headerLink}>Documentation <ArrowRight size={14} aria-hidden="true" /></Link></div>
      </div>
    </header>
    <div className={styles.shell}>
      <aside className={styles.sidebar}>
        <Link href="/" className={styles.back}><ArrowLeft size={14} aria-hidden="true" /> Back to Cora</Link>
        <div className={styles.search}><Search size={16} aria-hidden="true" /><label className={styles.srOnly} htmlFor="design-search">Search the entire design system</label><input id="design-search" type="search" placeholder="Search the system…" value={query} onChange={e => setQuery(e.target.value)} />{query && <button onClick={() => setQuery('')} aria-label="Clear search"><X size={16} /></button>}</div>
        <nav aria-label="Design system sections" className={styles.navigation}>{sections.map(([id, title], i) => <a key={id} href={`#${id}`}><span>{String(i).padStart(2, '0')}</span>{title}</a>)}</nav>
        <div className={styles.sidebarFoot}><span className={styles.eyebrow}>FOR AGENTS</span><a href={ds.endpoints.manifest}>Read JSON manifest <Code2 size={14} aria-hidden="true" /></a><a href={ds.endpoints.instructions}>Read plain-text contract <ArrowRight size={14} aria-hidden="true" /></a><p>Source-backed. Versioned.<br />One contract for every surface.</p></div>
      </aside>
      <div id="design-content" className={styles.content} tabIndex={-1}>
        {normalized && <section className={styles.searchResults} aria-label="Search results">
          <div className={styles.resultsTitle}><h2>Search results</h2><Button variant="ghost" onClick={() => setQuery('')}>Clear</Button></div>
          <p role="status">{matches.length} {matches.length === 1 ? 'match' : 'matches'} for “{query}”</p>
          {matches.length === 0 ? <p>Try a token, component, state, feature, or rule such as “sheet” or “focus”.</p> : <div className={styles.resultList}>{matches.map((m, i) => <a key={`${m.id}-${i}`} href={`#${m.id}`} onClick={e => { e.preventDefault(); followResult(m.id); }}><Badge>{m.kind}</Badge><strong>{m.title}</strong><span>{m.text}</span><ArrowRight size={16} aria-hidden="true" /></a>)}</div>}
        </section>}
        <section id="start" tabIndex={-1} className={styles.hero}>
          <div className={styles.heroMeta}><span className={styles.eyebrow}>CORA / DESIGN INFRASTRUCTURE</span><Badge>Public reference</Badge></div>
          <h1>A shared language.<br /><span>For everything we build.</span></h1>
          <p className={styles.heroDescription}>Explore the cards, controls, overlays and content behind Cora. Try real examples, inspect their structure, and build from the same versioned contract as our coding agents.</p>
          <div className={styles.downloads}><a className={styles.primaryLink} href="#catalog">Browse components <ArrowRight size={16} aria-hidden="true" /></a><a className={styles.secondaryLink} href={ds.endpoints.manifest} download="cora-design-system.json"><Code2 size={16} aria-hidden="true" />JSON manifest <ArrowDownToLine size={16} aria-hidden="true" /></a><a className={styles.secondaryLink} href={ds.endpoints.instructions} download="cora-design-system.txt">Agent contract <ArrowDownToLine size={16} aria-hidden="true" /></a><a className={styles.textLink} href={ds.endpoints.stylesheet} download="cora-tokens.css">Token CSS <ArrowDownToLine size={14} aria-hidden="true" /></a></div>
          <div className={styles.metrics}><div><strong>{ds.catalog.length}</strong><span>Visual specimens</span></div><div><strong>{ds.components.length}</strong><span>Component contracts</span></div><div><strong>{ds.tokens.length}</strong><span>Named tokens</span></div><div><strong>02</strong><span>Surface profiles</span></div></div>
          <div className={styles.agentStart}><div className={styles.calloutTitle}><Code2 size={18} aria-hidden="true" /><h2>Start here, agent.</h2></div><p>{ds.governance.agentPrompt}</p><div className={styles.calloutBottom}><code>{ds.source}</code><Button variant="secondary" onClick={() => copy(ds.governance.agentPrompt, 'prompt')}>{copied === 'prompt' ? <Check size={14} /> : <Copy size={14} />} Copy instructions</Button></div></div>
          <p className={styles.baselineNote}>Inherited design direction is preserved. Newly formalized tokens and accessible primitives are recommendations for future work; known legacy differences are recorded below. Updated {ds.updatedAt}.</p>
        </section>

        <section id="catalog" tabIndex={-1} className={styles.section}>
          <SectionHeader number="01" title="The visual catalog" description="An organized library of interactive specimens. Inspect the details, change the surface and theme, and carry each pattern into the next feature." />
          <VisualCatalog ref={catalogRef} />
        </section>
        <section id="principles" tabIndex={-1} className={styles.section}>
          <SectionHeader number="02" title="Principles" description="The decisions that stay consistent across every feature." />
          <div className={styles.twoColumn}>{ds.principles.map(p => <Card key={p.id}><span className={styles.eyebrow}>{p.id}</span><h3>{p.title}</h3><p>{p.detail}</p></Card>)}</div>
        </section>
        <section id="profiles" tabIndex={-1} className={styles.section}>
          <SectionHeader number="03" title="Two surfaces. Shared semantics." description="Choose a profile before writing UI. Preserve the type and canvas that actually ship." />
          <div className={styles.twoColumn}>{ds.profiles.map(p => <Card key={p.id}><div className={styles.cardTop}><Badge>{p.status}</Badge><span className={styles.eyebrow}>{p.id}</span></div><h3>{p.name}</h3><p>{p.description}</p><dl className={styles.definition}><dt>Stack</dt><dd>{p.stack}</dd><dt>Font</dt><dd><code>{p.font}</code></dd><dt>Canvas</dt><dd><code>{p.canvas}</code></dd></dl><h4>Reuse first</h4><ul>{p.reuse.map(r => <li key={r}><code>{r}</code></li>)}</ul><SourceList paths={p.sources} /></Card>)}</div>
        </section>
        <section id="tokens" tabIndex={-1} className={styles.section}>
          <SectionHeader number="04" title="Design tokens" description="Exact values with explicit purpose. Use semantic names in components; raw palette values are reference material." />
          <div className={styles.tokenFilters} role="group" aria-label="Token categories"><button aria-pressed={category === 'all'} onClick={() => setCategory('all')}>All <span>{ds.tokens.length}</span></button>{categories.map(c => <button key={c} aria-pressed={category === c} onClick={() => setCategory(c)}>{c}</button>)}</div>
          <div className={styles.tokenList}>{visibleTokens.map(t => <div key={t.name} id={`token-${t.name.slice(7)}`} tabIndex={-1} className={styles.tokenRow}>
            {(t.category === 'palette' || t.category === 'semantic') ? <span aria-hidden="true" className={styles.swatch} style={{ backgroundColor: t.value }} /> : <span aria-hidden="true" className={styles.tokenGlyph}>{t.category === 'typography' || t.category === 'type scale' ? 'Aa' : t.category === 'spacing' ? '↔' : '·'}</span>}
            <div className={styles.tokenDescription}><code>{t.name}</code><p>{t.usage}</p></div><div className={styles.tokenValue}><code>{t.value}</code><span>{t.category}</span></div><button className={styles.copyToken} aria-label={`Copy ${t.name}`} onClick={() => copy(`var(${t.name})`, t.name)}>{copied === t.name ? <Check size={16} aria-hidden="true" /> : <Copy size={16} aria-hidden="true" />}</button>
          </div>)}</div>
        </section>
        <section id="typography" tabIndex={-1} className={styles.section}>
          <SectionHeader number="05" title="Typography" description="Roles create hierarchy. Marketing uses General Sans; operational UI uses Inter; code uses JetBrains Mono." />
          <div className={styles.typeList}>{ds.typography.map(t => <div key={t.role} className={styles.typeRow}><div><span className={styles.eyebrow}>{t.role}</span><p className={styles.typeSample} style={{ fontSize: `var(${t.token})`, lineHeight: t.lineHeight, letterSpacing: t.tracking, fontWeight: Number(t.weight.split('–')[0]), fontFamily: t.role === 'Code' ? 'var(--cora-font-code)' : 'var(--cora-font-marketing)' }}>Clarity in every detail.</p></div><div><code>{t.token}</code><p>{t.weight} weight · {t.lineHeight} line height</p><p>{t.usage}</p></div></div>)}</div>
        </section>
        <section id="playground" tabIndex={-1} className={styles.section}>
          <SectionHeader number="06" title="Live components" description="Real reusable primitives. Change the surface, theme and state to inspect the same contract in action." />
          <div className={styles.playgroundControls}>
            <label>Surface<select aria-label="Surface" value={profile} onChange={e => setProfile(e.target.value)}><option value="marketing">Marketing</option><option value="workspace">Workspace</option></select></label>
            <label>Theme<select aria-label="Theme" value={theme} onChange={e => setTheme(e.target.value)}><option value="light">Light</option><option value="dark">Dark</option></select></label>
            <label>Button variant<select aria-label="Button variant" value={variant} onChange={e => setVariant(e.target.value as typeof variant)}>{['primary', 'secondary', 'ghost', 'danger'].map(v => <option key={v} value={v}>{v}</option>)}</select></label>
            <label>Field state<select aria-label="Field state" value={fieldState} onChange={e => setFieldState(e.target.value)}>{['default', 'error', 'disabled'].map(v => <option key={v} value={v}>{v}</option>)}</select></label>
          </div>
          <div className={styles.specimen} data-cora-theme={theme} data-cora-profile={profile}>
            <div className={styles.specimenMeta}><span className={styles.eyebrow}>{profile} / {theme} / INTERACTIVE EXAMPLE</span><SlidersHorizontal size={18} aria-hidden="true" /></div>
            <div className={styles.twoColumn}>
              <Card><h3>Project details</h3><p className={styles.specimenIntro}>A local preview. Changes here affect this example.</p><TextField label="Project name" value={projectName} onChange={e => setProjectName(e.target.value)} disabled={fieldState === 'disabled'} hint="Visible to everyone in your team." error={fieldState === 'error' ? 'Enter a project name before saving.' : undefined} /><div className={styles.exampleActions}><Button variant={variant} loading={loading} onClick={() => setClicks(c => c + 1)}>Save changes</Button><Button variant="secondary" disabled>Disabled</Button><Button variant="ghost" onClick={() => setSheetOpen(true)}>Open sheet demo</Button></div><label className={styles.checkbox}><input type="checkbox" checked={loading} onChange={e => setLoading(e.target.checked)} /> Preview loading state</label><p role="status" className={styles.exampleStatus}>{loading ? 'Loading preview: repeat submissions disabled.' : clicks ? `Example action activated ${clicks} ${clicks === 1 ? 'time' : 'times'}.` : 'Use Tab to inspect visible keyboard focus.'}</p></Card>
              <Card><div className={styles.cardTop}><h3>Project summary</h3><Badge tone="success">Active</Badge></div><p className={styles.specimenIntro}>{projectName || 'Untitled project'}</p><div className={styles.statusSamples}><Badge>Draft</Badge><Badge tone="success">Active</Badge><Badge tone="warning">Pending</Badge><Badge tone="danger">Overdue</Badge></div><dl className={styles.summary}><div><dt>Client</dt><dd>Kavya Patel</dd></div><div><dt>Project status</dt><dd>Ready for review</dd></div><div><dt>Identifier</dt><dd><code>PRJ-024</code></dd></div></dl><p className={styles.exampleStatus}>Semantic status labels, tonal surfaces and subtle structural borders.</p></Card>
            </div>
            <Sheet open={sheetOpen} onClose={() => setSheetOpen(false)} title="Project details" footer={<Button onClick={() => setSheetOpen(false)}>Done</Button>}><p>Responsive modal preview. On a phone, this becomes a bottom sheet. Escape or Close dismisses it and returns focus to the opener.</p><TextField label="Sheet project name" defaultValue="Sample project" hint="Demo input; no records are saved." /></Sheet>
          </div>
          <div className={styles.apiList}>{ds.primitiveApi.map(p => <details key={p.name}><summary>{p.name}<code>{p.import}</code></summary><div><p><strong>Props:</strong> {p.props}</p><p><strong>Token bindings:</strong> <code>{p.tokens}</code></p><pre><code>{p.example}</code></pre><Button variant="ghost" onClick={() => copy(`import { ${p.name} } from '${p.import}';\n\n${p.example}`, p.name)}><Copy size={14} aria-hidden="true" /> Copy example</Button></div></details>)}</div>
        </section>
        <section id="components" tabIndex={-1} className={styles.section}>
          <SectionHeader number="07" title="Component contracts" description="Implementation references, accepted states and interaction requirements. Five primitives are shared code; other records describe existing patterns to reuse and improve." />
          <div className={styles.contractList}>{ds.components.map(c => <article key={c.id} id={`component-${c.id}`} tabIndex={-1} className={styles.contract}><div className={styles.contractHeader}><div><span className={styles.eyebrow}>{c.level}</span><h3>{c.name}</h3></div><Badge tone={c.status === 'implemented' ? 'success' : 'neutral'}>{c.status === 'implemented' ? 'Shared primitive' : 'Existing pattern'}</Badge></div><code className={styles.implementation}>{c.implementation}</code><ul>{c.rules.map(r => <li key={r}>{r}</li>)}</ul><div className={styles.states}><span>States</span>{c.states.map(s => <Badge key={s}>{s}</Badge>)}</div></article>)}</div>
        </section>
        <section id="patterns" tabIndex={-1} className={styles.section}>
          <SectionHeader number="08" title="Product & feature patterns" description="Design decisions across the product, beyond individual controls. Workspace source paths resolve under the cora-workspace plugin." />
          <div className={styles.twoColumn}>{ds.featurePatterns.map(p => <Card key={p.name}><h3>{p.name}</h3><p>{p.rules}</p><SourceList paths={p.sources} /></Card>)}</div>
        </section>
        <section id="responsive" tabIndex={-1} className={styles.section}>
          <SectionHeader number="09" title="Responsive layout" description="A single readable workflow at every width. Existing feature breakpoints must be inspected before a panel is migrated." />
          <div className={styles.ruleList}>{ds.responsive.map(r => <div key={r.range}><code>{r.range}</code><p>{r.rule}</p></div>)}</div>
          <Card><h3>Mobile sheet anatomy</h3><div className={styles.sheetDiagram} aria-label="Mobile sheet structure: drag handle, title and close, independently scrolling content, sticky actions and safe area"><div className={styles.sheetHandle} /><div className={styles.diagramHeader}><strong>Sheet title</strong><span>Close</span></div><div className={styles.diagramBody}>Scrollable content<br /><small>Labels · inputs · validation</small></div><div className={styles.diagramFooter}>Sticky actions + safe area</div></div><p>Recommended modal pattern: up to 90dvh, 24px top corners, balanced scroll lock, focus containment and restoration. Modeless desktop inspectors retain their feature-local behavior.</p></Card>
        </section>
        <section id="accessibility" tabIndex={-1} className={styles.section}><SectionHeader number="10" title="Accessibility is part of the system" description="Calm visual design must remain perceivable, operable and understandable." /><ol className={styles.numberedRules}>{ds.accessibility.map(r => <li key={r}>{r}</li>)}</ol></section>
        <section id="content" tabIndex={-1} className={styles.section}><SectionHeader number="11" title="Content & terminology" description="A consistent interface includes the words, sample identities and formats we use." /><ol className={styles.numberedRules}>{ds.contentRules.map(r => <li key={r}>{r}</li>)}</ol></section>
        <section id="audit" tabIndex={-1} className={styles.section}>
          <SectionHeader number="12" title="Audit & migration register" description="Observed differences are explicit. Agents should resolve them within the feature they are changing." />
          <div className={styles.auditScope}><p>{ds.audit.method}</p><p>{ds.audit.findings}</p><p>{ds.audit.migration}</p></div>
          {ds.deviations.map(d => <Card key={d.id} id={`deviation-${d.id}`} tabIndex={-1} className={styles.deviation}><div className={styles.cardTop}><span className={styles.eyebrow}>{d.id}</span><Badge>{d.status.replaceAll('-', ' ')}</Badge></div><h3>{d.scope}</h3><p><strong>Observed.</strong> {d.observed}</p><p><strong>Decision.</strong> {d.decision}</p><SourceList paths={d.sources} /></Card>)}
        </section>
        <section id="governance" tabIndex={-1} className={styles.section}>
          <SectionHeader number="13" title="The workflow for coding agents" description="Keep the implementation and its public contract in the same change." />
          <ol className={styles.numberedRules}>{ds.governance.changeProtocol.map(r => <li key={r}>{r}</li>)}</ol>
          <Card><h3>Authority & approval</h3><p>{ds.governance.baseline}</p><p>{ds.governance.approval}</p><ol>{ds.governance.precedence.map(r => <li key={r}>{r}</li>)}</ol></Card>
          <div className={styles.verification}><h3>Definition of done</h3><ul>{ds.verification.map(r => <li key={r}>{r}</li>)}</ul><pre><code>cd cora-frontend{'\n'}npm run check:design-system{'\n'}npm run build</code></pre></div>
          <p className={styles.baselineNote}>The automated guard checks the shared contract and design-system components. Legacy screens are migrated incrementally and require feature-specific verification.</p>
          <div className={styles.pageEnd}><span>Cora Design System / v{ds.version}</span><a href="#start">Back to start ↑</a></div>
        </section>
      </div>
    </div>
    <p role="status" aria-live="polite" className={styles.copyStatus}>{copyMessage}</p>
  </main>;
}
