'use client';

import React, { useState, useMemo } from 'react';
import { Search, Sparkles, Filter, X } from 'lucide-react';
import { ArticleCard } from '@/components/articles/ArticleCard';
import { ARTICLE_CATEGORIES, type Article } from '@/lib/articles-data';

interface ArticlesClientHubProps {
  initialArticles: Article[];
}

export function ArticlesClientHub({ initialArticles }: ArticlesClientHubProps) {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [selectedDifficulty, setSelectedDifficulty] = useState<string>('all');

  const filteredArticles = useMemo(() => {
    return initialArticles.filter((article) => {
      // Category filter
      if (selectedCategory !== 'all' && article.category !== selectedCategory) {
        return false;
      }
      // Difficulty filter
      if (selectedDifficulty !== 'all' && article.difficulty !== selectedDifficulty) {
        return false;
      }
      // Search query
      if (searchQuery.trim()) {
        const q = searchQuery.toLowerCase();
        const inTitle = article.title.toLowerCase().includes(q);
        const inDesc = article.description.toLowerCase().includes(q);
        const inContent = article.content.toLowerCase().includes(q);
        const inCategory = article.categoryLabel.toLowerCase().includes(q);
        return inTitle || inDesc || inContent || inCategory;
      }
      return true;
    });
  }, [initialArticles, searchQuery, selectedCategory, selectedDifficulty]);

  return (
    <div className="space-y-8">
      {/* ── Search & Filter Controls Bar ── */}
      <div className="p-4 sm:p-5 rounded-2xl bg-white border border-zinc-200/90 shadow-[0px_4px_20px_rgba(0,0,0,0.03)] space-y-4">
        <div className="flex flex-col sm:flex-row items-center gap-4">

          {/* Search Input */}
          <div className="relative flex-1 w-full">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
            <input
              type="text"
              placeholder="Search guides, 18% GST rules, call sheets, AI models..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full pl-10 pr-10 py-2.5 rounded-xl bg-zinc-50 border border-zinc-200 text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-hidden focus:border-zinc-900 focus:bg-white transition-all font-normal"
            />
            {searchQuery && (
              <button
                onClick={() => setSearchQuery('')}
                className="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-zinc-400 hover:text-zinc-700"
              >
                <X className="w-3.5 h-3.5" />
              </button>
            )}
          </div>

          {/* Difficulty Filter Dropdown */}
          <div className="flex items-center gap-2 w-full sm:w-auto">
            <span className="text-xs font-mono text-zinc-400 hidden sm:inline">Level:</span>
            <select
              value={selectedDifficulty}
              onChange={(e) => setSelectedDifficulty(e.target.value)}
              className="px-3 py-2.5 rounded-xl bg-zinc-50 border border-zinc-200 text-xs font-medium text-zinc-700 focus:outline-hidden focus:border-zinc-900"
            >
              <option value="all">All Difficulty Levels</option>
              <option value="Beginner">Beginner</option>
              <option value="Intermediate">Intermediate</option>
              <option value="Advanced">Advanced</option>
            </select>
          </div>

        </div>

        {/* Category Filter Pills */}
        <div className="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
          <button
            onClick={() => setSelectedCategory('all')}
            className={`px-3 py-1.5 rounded-full font-medium transition-all shrink-0 ${
              selectedCategory === 'all'
                ? 'bg-zinc-950 text-white shadow-2xs'
                : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/80 hover:text-zinc-950'
            }`}
          >
            All Categories ({initialArticles.length})
          </button>

          {ARTICLE_CATEGORIES.map((cat) => {
            const count = initialArticles.filter((a) => a.category === cat.id).length;
            const isSelected = selectedCategory === cat.id;
            return (
              <button
                key={cat.id}
                onClick={() => setSelectedCategory(cat.id)}
                className={`px-3 py-1.5 rounded-full font-medium transition-all shrink-0 flex items-center gap-1.5 ${
                  isSelected
                    ? 'bg-zinc-950 text-white shadow-2xs'
                    : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/80 hover:text-zinc-950'
                }`}
              >
                <span>{cat.label}</span>
                <span className={`text-[10px] font-mono ${isSelected ? 'text-zinc-400' : 'text-zinc-400'}`}>
                  ({count})
                </span>
              </button>
            );
          })}
        </div>
      </div>

      {/* ── Articles Grid ── */}
      {filteredArticles.length > 0 ? (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredArticles.map((article) => (
            <ArticleCard key={article.slug} article={article} />
          ))}
        </div>
      ) : (
        <div className="p-12 text-center rounded-3xl bg-white border border-zinc-200/80 space-y-3">
          <div className="w-10 h-10 rounded-full bg-zinc-100 mx-auto flex items-center justify-center text-zinc-500">
            <Search className="w-5 h-5" />
          </div>
          <h4 className="text-base font-bold text-zinc-900">No articles matched your criteria</h4>
          <p className="text-xs text-zinc-500 max-w-sm mx-auto">
            Try adjusting your search keywords or switching category filters to discover related studio guides.
          </p>
          <button
            onClick={() => {
              setSearchQuery('');
              setSelectedCategory('all');
              setSelectedDifficulty('all');
            }}
            className="px-4 py-1.5 rounded-full bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
          >
            Reset All Filters
          </button>
        </div>
      )}
    </div>
  );
}
