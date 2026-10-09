'use client';

import React from 'react';
import { X, Check } from 'lucide-react';

const AVOID_RULES = [
  {
    title: "Don't stretch or distort",
    desc: 'Never skew, stretch, or alter the aspect ratio of the wordmark or symbol.'
  },
  {
    title: "Don't add neon / rainbow gradients",
    desc: 'Cora strictly enforces a monochromatic neutral palette. Never apply colorful gradients.'
  },
  {
    title: "Don't add heavy drop shadows",
    desc: 'Avoid 3D bevels, heavy glow effects, or harsh drop shadows.'
  },
  {
    title: "Don't rotate the logo",
    desc: 'The logo must always sit horizontally flush with the baseline.'
  },
  {
    title: "Don't use on low-contrast backgrounds",
    desc: 'Always ensure maximum readability (pure black on white, pure white on dark).'
  },
  {
    title: "Don't alter kerning or typography",
    desc: 'Never re-type or swap the font of the official wordmark.'
  }
];

export function BrandLogoGuidanceSection() {
  return (
    <section className="py-14 sm:py-20 border-b border-zinc-100 bg-white">
      <div className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 space-y-12 sm:space-y-16">
        
        {/* Section Header */}
        <div className="space-y-2">
          <div className="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-zinc-100 border border-zinc-200 text-[10.5px] font-mono font-medium text-zinc-700 uppercase">
            <span>GOVERNANCE &amp; BOUNDARIES</span>
          </div>
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            Logo Guidance &amp; Clear Space
          </h2>
          <p className="text-sm sm:text-base text-zinc-600 font-normal">
            Rules and boundaries for maintaining brand integrity across third-party partnerships, integrations, and media.
          </p>
        </div>

        {/* ── Clear Space ── */}
        <div className="space-y-4">
          <h3 className="font-display text-lg font-bold text-zinc-950">
            Clear Space
          </h3>
          <p className="text-xs text-zinc-500 font-normal">
            Always maintain minimum clear space around the logo equal to the height of the &apos;C&apos; mark (1X padding).
          </p>

          <div className="rounded-3xl border border-zinc-200/90 p-8 sm:p-12 bg-zinc-50/50 flex items-center justify-center shadow-2xs">
            <div className="relative border border-dashed border-zinc-400 p-8 sm:p-10 rounded-2xl bg-white flex items-center justify-center shadow-2xs">
              <span className="absolute -top-3 left-4 px-2 py-0.5 rounded bg-zinc-100 border border-zinc-200/70 text-[10px] font-mono text-zinc-600 font-bold">
                Min Padding: 1X
              </span>
              <div className="flex items-center gap-3 select-none">
                <svg width="36" height="36" viewBox="0 0 48 48" fill="none" className="text-zinc-950">
                  <path d="M34 14C31.5 11.5 28 10 24 10C16.268 10 10 16.268 10 24C10 31.732 16.268 38 24 38C28 38 31.5 36.5 34 34" stroke="currentColor" strokeWidth="4.5" strokeLinecap="round"/>
                  <circle cx="34" cy="24" r="3" fill="currentColor"/>
                </svg>
                <span className="font-display text-3xl font-extrabold tracking-tight text-zinc-950">
                  Cora
                </span>
              </div>
            </div>
          </div>
        </div>

        {/* ── Things to Avoid ── */}
        <div className="space-y-4">
          <h3 className="font-display text-lg font-bold text-zinc-950">
            Things to Avoid
          </h3>

          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            {AVOID_RULES.map((rule, idx) => (
              <div
                key={idx}
                className="rounded-2xl border border-zinc-200/80 p-5 bg-white space-y-2.5 shadow-2xs"
              >
                <div className="w-7 h-7 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center">
                  <X className="w-4 h-4" />
                </div>
                <h4 className="font-display text-sm font-bold text-zinc-950">
                  {rule.title}
                </h4>
                <p className="text-xs text-zinc-500 font-normal leading-relaxed">
                  {rule.desc}
                </p>
              </div>
            ))}
          </div>
        </div>

      </div>
    </section>
  );
}
