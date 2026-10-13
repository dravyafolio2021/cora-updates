'use client';

import { useEffect, useId, useRef, type ButtonHTMLAttributes, type InputHTMLAttributes, type HTMLAttributes, type ReactNode } from 'react';
import styles from './primitives.module.css';

export function Button({ variant = 'primary', loading = false, disabled, className = '', children, type = 'button', ...props }: ButtonHTMLAttributes<HTMLButtonElement> & { variant?: 'primary' | 'secondary' | 'ghost' | 'danger'; loading?: boolean }) {
  return <button {...props} type={type} disabled={disabled || loading} aria-busy={loading || undefined} className={`${styles.button} ${styles[variant]} ${className}`}>
    {loading && <span className={styles.spinner} aria-hidden="true" />}{children}
  </button>;
}

export function TextField({ label, hint, error, id, className = '', 'aria-describedby': describedBy, ...props }: InputHTMLAttributes<HTMLInputElement> & { label: string; hint?: string; error?: string }) {
  const generated = useId();
  const fieldId = id || generated;
  const descriptionId = `${fieldId}-description`;
  return <div className={styles.field}>
    <label htmlFor={fieldId}>{label}{props.required && <span aria-hidden="true"> *</span>}</label>
    <input {...props} id={fieldId} aria-invalid={error ? true : props['aria-invalid']} aria-describedby={[describedBy, (error || hint) && descriptionId].filter(Boolean).join(' ') || undefined} className={`${styles.input} ${className}`} />
    {(error || hint) && <p id={descriptionId} className={error ? styles.error : styles.hint}>{error || hint}</p>}
  </div>;
}

export function Badge({ tone = 'neutral', children }: { tone?: 'neutral' | 'success' | 'warning' | 'danger'; children: ReactNode }) {
  return <span className={`${styles.badge} ${styles[tone]}`}>{children}</span>;
}

export function Card({ className = '', ...props }: HTMLAttributes<HTMLDivElement>) {
  return <div {...props} className={`${styles.card} ${className}`} />;
}

/** Native modal supplies background inertness, keyboard containment and focus return. */
export function Sheet({ open, onClose, title, children, footer, placement = 'sheet' }: { open: boolean; onClose: () => void; title: string; children: ReactNode; footer?: ReactNode; placement?: 'modal' | 'drawer' | 'sheet' }) {
  const dialog = useRef<HTMLDialogElement>(null);
  const titleId = useId();
  useEffect(() => {
    if (open && !dialog.current?.open) dialog.current?.showModal();
    if (!open && dialog.current?.open) dialog.current.close();
  }, [open]);
  return <dialog ref={dialog} className={`${styles.sheet} ${styles[placement]} ${!footer ? styles.footerless : ''}`} aria-labelledby={titleId} onCancel={e => { e.preventDefault(); onClose(); }} onClose={onClose} onKeyDown={e => {
    if (e.key !== 'Tab') return;
    const controls = Array.from(e.currentTarget.querySelectorAll<HTMLElement>('button:not(:disabled), [href], input:not(:disabled), select:not(:disabled), textarea:not(:disabled), [tabindex]:not([tabindex="-1"])')).filter(el => el.tabIndex >= 0 && !el.matches(':disabled') && el.getClientRects().length > 0);
    const first = controls[0]; const last = controls[controls.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last?.focus(); }
    if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first?.focus(); }
  }}>
    <div className={styles.handle} aria-hidden="true" />
    <header className={styles.sheetHeader}><h2 id={titleId}>{title}</h2><Button variant="ghost" onClick={onClose} aria-label="Close sheet">Close</Button></header>
    <div className={styles.sheetBody}>{children}</div>
    {footer && <footer className={styles.sheetFooter}>{footer}</footer>}
  </dialog>;
}
