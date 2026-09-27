'use client';

import React from 'react';
import Link from 'next/link';
import { Layers, Shield, TrendingUp, Bot, Receipt, ArrowRight, BookOpen } from 'lucide-react';
import { TOPIC_CLUSTERS, type TopicCluster } from '@/lib/blog-data';

const iconMap: Record<string, React.ReactNode> = {
  Layers: <Layers className="w-4 h-4 text-zinc-900" />,
  Shield: <Shield className="w-4 h-4 text-zinc-900" />,
  TrendingUp: <TrendingUp className="w-4 h-4 text-zinc-900" />,
  Bot: <Bot className="w-4 h-4 text-zinc-900" />,
  Receipt: <Receipt className="w-4 h-4 text-zinc-900" />,
};

export function BlogTopicClusters() {
  return (
    <section aria-label="Topic Clusters" className="my-14 pt-8 border-t border-zinc-200/80">
      <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6">
        <div>
          <div className="text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-500">
            SYSTEM ARCHITECTURES
          </div>
          <h2 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-950 mt-1">
            Explore Operating Topic Clusters
          </h2>
        </div>
        <p className="text-xs text-zinc-500 max-w-sm">
          Browse structured frameworks grouped by agency operational function.
        </p>
      </div>

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {TOPIC_CLUSTERS.map((cluster) => {
          const icon = cluster.iconName ? iconMap[cluster.iconName] || <BookOpen className="w-4 h-4 text-zinc-900" /> : <BookOpen className="w-4 h-4 text-zinc-900" />;
          return (
            <div
              key={cluster.id}
              className="group rounded-2xl border border-zinc-200/90 bg-[#FBFaf7] p-5 shadow-2xs hover:border-zinc-300 hover:shadow-xs transition-all flex flex-col justify-between"
            >
              <div>
                <div className="flex items-center justify-between mb-3">
                  <div className="w-8 h-8 rounded-xl bg-white border border-zinc-200 flex items-center justify-center shadow-2xs">
                    {icon}
                  </div>
                  <Link
                    href={`/blog/${cluster.slug}/`}
                    className="text-[10px] font-mono font-bold uppercase text-zinc-500 hover:text-zinc-950 transition-colors"
                  >
                    View All →
                  </Link>
                </div>

                <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors">
                  <Link href={`/blog/${cluster.slug}/`}>
                    {cluster.name}
                  </Link>
                </h3>

                <p className="mt-1.5 text-xs text-zinc-600 leading-relaxed font-normal">
                  {cluster.description}
                </p>
              </div>

              {cluster.pillarSlug && (
                <div className="mt-4 pt-3 border-t border-zinc-200/70">
                  <Link
                    href={`/blog/${cluster.pillarSlug}/`}
                    className="inline-flex items-center gap-1 text-[11px] font-mono font-semibold text-zinc-800 hover:text-zinc-950 group-hover:translate-x-0.5 transition-transform"
                  >
                    <span>Read Pillar Guide</span>
                    <ArrowRight className="w-3 h-3 text-zinc-600" />
                  </Link>
                </div>
              )}
            </div>
          );
        })}
      </div>
    </section>
  );
}
