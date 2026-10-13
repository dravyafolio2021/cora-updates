'use client';

import { forwardRef, useImperativeHandle, useState } from 'react';
import { Check, Copy, Expand, Link2, Minimize2 } from 'lucide-react';
import { designSystem as ds } from '@/lib/design-system';
import { Badge, Button } from './primitives';
import { CatalogPreview } from './CatalogPreviews';
import styles from './VisualCatalog.module.css';

export type VisualCatalogHandle = { reveal: (id: string) => void };
const categories = [['cards', 'Cards'], ['controls', 'Controls'], ['overlays', 'Overlays'], ['content', 'Content'], ['data', 'Data & workflows'], ['media', 'Images & icons']] as const;

export const VisualCatalog = forwardRef<VisualCatalogHandle>(function VisualCatalog(_, ref) {
  const [category, setCategory] = useState('all');
  const [query, setQuery] = useState('');
  const [theme, setTheme] = useState('light');
  const [profile, setProfile] = useState('marketing');
  const [expanded, setExpanded] = useState('');
  const [copied, setCopied] = useState('');
  const [feedback, setFeedback] = useState('');
  useImperativeHandle(ref, () => ({ reveal(id) {
    setQuery(''); setCategory('all');
    requestAnimationFrame(() => {
      const target = document.getElementById(`specimen-${id}`);
      target?.scrollIntoView({ behavior: 'instant', block: 'start' });
      target?.focus({ preventScroll: true });
    });
  } }), []);
  const visible = ds.catalog.filter(item => (category === 'all' || item.category === category) && `${item.name} ${item.description} ${item.variants.join(' ')} ${item.states.join(' ')} ${item.usage.join(' ')}`.toLowerCase().includes(query.trim().toLowerCase()));
  async function copy(value: string, key: string) {
    try { await navigator.clipboard.writeText(value); setCopied(key); setFeedback('Copied to clipboard.'); }
    catch { setFeedback('Copy is unavailable. Select the example text or use the agent export.'); }
  }
  return <div className={styles.catalog}>
    <div className={styles.guide}>
      <div><h3>For people</h3><p>Browse a category, try the controls, and expand an example. Open “Usage & code” for variants, required states, and its source.</p></div>
      <div><h3>For developers & agents</h3><p>Reuse shared primitives. Adapt reference recipes to the feature, then verify every required state. Read the <a href={ds.endpoints.manifest}>JSON manifest</a> or <a href={ds.endpoints.instructions}>agent contract</a> before implementation.</p></div>
    </div>
    <div className={styles.controls}>
      <label className={styles.search}>Find a specimen<input type="search" placeholder="Cards, dialog, loading, icons…" value={query} onChange={e => setQuery(e.target.value)} /></label>
      <label>Preview surface<select value={profile} onChange={e => setProfile(e.target.value)}><option value="marketing">Marketing</option><option value="workspace">Workspace</option></select></label>
      <label>Preview theme<select value={theme} onChange={e => setTheme(e.target.value)}><option value="light">Light</option><option value="dark">Dark</option></select></label>
    </div>
    <div className={styles.filters} role="group" aria-label="Visual catalog categories">
      <button aria-pressed={category === 'all'} onClick={() => setCategory('all')}>All <span>{ds.catalog.length}</span></button>
      {categories.map(([id, label]) => <button key={id} aria-pressed={category === id} onClick={() => setCategory(id)}>{label} <span>{ds.catalog.filter(c => c.category === id).length}</span></button>)}
    </div>
    <p className={styles.count} role="status">{visible.length} of {ds.catalog.length} specimens · Interactive examples use fictional data.</p>
    <p className={styles.feedback} role="status">{feedback}</p>
    {visible.length === 0 && <div className={styles.empty}><h3>No matching specimens</h3><p>Try another name or reset the category and search.</p><Button variant="secondary" onClick={() => { setQuery(''); setCategory('all'); }}>Clear filters</Button></div>}
    <div className={styles.grid}>{visible.map(item => <article id={`specimen-${item.id}`} tabIndex={-1} key={item.id} className={`${styles.tile} ${expanded === item.id ? styles.expanded : ''}`}>
      <div className={styles.heading}><div><span className={styles.category}>{categories.find(([id]) => id === item.category)?.[1]}</span><h3>{item.name}</h3></div><Badge tone={item.status === 'shared-primitive' ? 'success' : 'neutral'}>{item.status === 'shared-primitive' ? 'Shared primitive' : 'Reference recipe'}</Badge></div>
      <p className={styles.description}>{item.description}</p>
      <div className={styles.frame} data-cora-theme={theme} data-cora-profile={profile}><CatalogPreview id={item.id} compact={expanded !== item.id} /></div>
      <div className={styles.actions}><Button variant="ghost" aria-expanded={expanded === item.id} aria-label={`${expanded === item.id ? 'Collapse' : 'Expand'} ${item.name}`} onClick={() => setExpanded(expanded === item.id ? '' : item.id)}>{expanded === item.id ? <Minimize2 size={14} aria-hidden="true" /> : <Expand size={14} aria-hidden="true" />}{expanded === item.id ? 'Collapse' : 'Expand'}</Button><a href={`#specimen-${item.id}`} aria-label={`Link to ${item.name}`}><Link2 size={14} aria-hidden="true" />Permalink</a></div>
      <details className={styles.details}><summary>Usage & code</summary><div className={styles.reference}>
        <h4>When to use</h4><ul>{item.usage.map(rule => <li key={rule}>{rule}</li>)}</ul>
        <h4>Variants</h4><div className={styles.tags}>{item.variants.map(v => <Badge key={v}>{v}</Badge>)}</div>
        <h4>Required states</h4><div className={styles.tags}>{item.states.map(s => <Badge key={s}>{s}</Badge>)}</div>
        <h4>Source & contracts</h4><code>{item.source}</code><div className={styles.contracts}>{item.componentIds.map(id => <a key={id} href={`#component-${id}`}>{ds.components.find(c => c.id === id)?.name}</a>)}</div>
        <h4>Structural example</h4><p>Implement the caller state and handlers. Preview compositions live in <code>{ds.catalogGuide.previews}</code>.</p><pre><code>{item.example}</code></pre><Button variant="secondary" onClick={() => copy(item.example, item.id)}>{copied === item.id ? <Check size={14} aria-hidden="true" /> : <Copy size={14} aria-hidden="true" />}Copy example</Button>
      </div></details>
    </article>)}</div>
    <p className={styles.note}>{ds.catalogGuide.authority}</p>
  </div>;
});
