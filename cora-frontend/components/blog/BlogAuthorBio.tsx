import React from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { Linkedin } from 'lucide-react';
import type { BlogAuthor } from '@/lib/blog-data';

interface BlogAuthorBioProps {
  author: BlogAuthor;
}

export function BlogAuthorBio({ author }: BlogAuthorBioProps) {
  return (
    <div className="my-10 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-[#FBFaf7] dark:bg-zinc-900/60 p-6 sm:p-7 shadow-sm">
      <div className="flex flex-col sm:flex-row items-start gap-4 sm:gap-5">
        <div className="relative w-14 h-14 rounded-2xl overflow-hidden border border-zinc-300 dark:border-zinc-700 bg-zinc-950 text-white flex items-center justify-center font-display text-lg font-bold shrink-0">
          <span>{author.name.charAt(0)}</span>
        </div>

        <div className="flex-1 min-w-0">
          <div className="flex flex-wrap items-center justify-between gap-2">
            <div>
              <div className="text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-600 dark:text-zinc-400">
                WRITTEN BY
              </div>
              <h4 className="font-display text-base sm:text-lg font-bold text-zinc-950 dark:text-white">
                {author.name}
              </h4>
              <p className="text-xs text-zinc-600 dark:text-zinc-400 font-medium">{author.role}</p>
            </div>

            <div className="flex items-center gap-2">
              {author.linkedin && (
                <a
                  href={author.linkedin}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="p-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                  aria-label="LinkedIn Profile"
                >
                  <Linkedin className="w-4 h-4" />
                </a>
              )}
              {author.x && (
                <a
                  href={author.x}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="p-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors text-xs font-bold"
                  aria-label="X (Twitter) Profile"
                >
                  𝕏
                </a>
              )}
            </div>
          </div>

          <p className="mt-3 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
            {author.bio}
          </p>
        </div>
      </div>
    </div>
  );
}
