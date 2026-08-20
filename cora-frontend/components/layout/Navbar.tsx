'use client';

import React from 'react';
import Link from 'next/link';
import { ArrowRight } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export function Navbar() {
  const handleNavClick = (section: string) => {
    trackEvent('nav_link_clicked', { target_section: section });
  };

  return (
    <header className="w-full py-4 sm:py-5 absolute top-0 left-0 right-0 z-50 bg-transparent transition-all">
      <div className="w-full max-w-[1140px] mx-auto px-4 sm:px-6">
        <div className="flex items-center justify-between gap-4">
          
          {/* Brand Logo */}
          <Link href="/" className="flex items-center gap-2.5 text-zinc-950 font-display font-extrabold text-xl tracking-tight group shrink-0">
            <div className="w-8 h-8 sm:w-9 sm:h-9 shrink-0 aspect-square bg-zinc-950 text-white rounded-[10px] flex items-center justify-center font-mono text-sm font-bold shadow-sm transition-transform duration-200 group-hover:scale-105 border border-zinc-800">
              &lt; &gt;
            </div>
            <span className="font-display font-bold text-xl tracking-tight text-zinc-950">Cora</span>
          </Link>

          {/* Desktop Navigation Links */}
          <nav className="hidden md:flex items-center gap-6 text-xs font-semibold text-zinc-700 font-sans">
            <Link
              href="/features"
              onClick={() => handleNavClick('features')}
              className="hover:text-zinc-950 transition-colors py-1"
            >
              Features
            </Link>
            <Link
              href="/#how-it-works"
              onClick={() => handleNavClick('how-it-works')}
              className="hover:text-zinc-950 transition-colors py-1"
            >
              How It Works
            </Link>
            <Link
              href="/use-cases"
              onClick={() => handleNavClick('use-cases')}
              className="hover:text-zinc-950 transition-colors py-1"
            >
              Use Cases
            </Link>
            <Link
              href="/pricing"
              onClick={() => handleNavClick('pricing')}
              className="hover:text-zinc-950 transition-colors py-1"
            >
              Pricing
            </Link>
            <Link
              href="/about"
              onClick={() => handleNavClick('about')}
              className="hover:text-zinc-950 transition-colors py-1"
            >
              About
            </Link>
            <Link
              href="/tools"
              onClick={() => handleNavClick('tools')}
              className="hover:text-zinc-950 transition-colors py-1 flex items-center gap-1"
            >
              <span>Free Tools</span>
              <span className="text-[0.625rem] font-bold uppercase tracking-wider px-1.5 py-0.5 bg-zinc-950 text-white rounded">New</span>
            </Link>
          </nav>

          {/* Header CTA - Following Design System Monochromatic Solid Black */}
          <div className="flex items-center">
            <a
              href="https://app.heycora.in/workspace/login"
              onClick={() => trackEvent('header_cta_clicked')}
              className="inline-flex items-center justify-center gap-1.5 bg-zinc-950 text-white border border-zinc-800 px-4 py-2 rounded-xl text-xs font-semibold hover:bg-zinc-800 transition-all shadow-sm active:translate-y-0 hover:-translate-y-0.5 whitespace-nowrap shrink-0 group"
            >
              <span>Get started for Free</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
            </a>
          </div>

        </div>
      </div>
    </header>
  );
}
