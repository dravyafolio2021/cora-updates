'use client';

import React from 'react';
import Link from 'next/link';
import { ArrowLeft } from 'lucide-react';

export default function NotFound() {
  return (
    <main className="min-h-[calc(100vh-160px)] bg-white text-zinc-950 flex flex-col items-center justify-center px-4 sm:px-6 py-16 relative z-20">
      <div className="max-w-md w-full text-center space-y-5">
        
        {/* Subtle Error Code */}
        <p className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-widest">
          404 error
        </p>

        {/* Clear Headline */}
        <h1 className="font-display text-4xl sm:text-5xl font-bold tracking-tight text-zinc-950">
          Page not found
        </h1>

        {/* Effortless Subtitle */}
        <p className="text-sm text-zinc-500 leading-relaxed max-w-sm mx-auto">
          Sorry, we couldn’t find the page you’re looking for. It may have been moved or deleted.
        </p>

        {/* Simple Back Button */}
        <div className="pt-3">
          <Link
            href="/"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-colors shadow-2xs"
          >
            <ArrowLeft className="w-4 h-4" />
            <span>Back to home</span>
          </Link>
        </div>

      </div>
    </main>
  );
}
