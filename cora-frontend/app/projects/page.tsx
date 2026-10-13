'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  ArrowRight, 
  Search, 
  TrendingUp, 
  Lock, 
  ShieldCheck, 
  Sparkles, 
  CheckCircle2, 
  Layers, 
  ExternalLink,
  ChevronRight
} from 'lucide-react';
import { SEED_PROJECTS, ProjectCaseStudy } from '@/lib/projects-data';

const PROJECT_CATEGORIES = [
  { id: 'all', label: 'All Projects' },
  { id: 'seo_growth', label: 'SEO & Growth' },
  { id: 'paid_media', label: 'Paid Media' },
  { id: 'headless_web', label: 'Next.js & Web' },
  { id: 'ai_systems', label: 'AI & Automation' },
  { id: 'brand_design', label: 'Branding & UI' }
];

export default function ProjectsPortfolioPage() {
  const [activeCategory, setActiveCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');

  const filteredProjects = useMemo(() => {
    return SEED_PROJECTS.filter((proj) => {
      const matchesSearch = 
        proj.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        proj.clientName.toLowerCase().includes(searchQuery.toLowerCase()) ||
        proj.clientIndustry.toLowerCase().includes(searchQuery.toLowerCase()) ||
        proj.services.some(s => s.toLowerCase().includes(searchQuery.toLowerCase()));

      if (!matchesSearch) return false;
      if (activeCategory === 'all') return true;

      const servicesStr = proj.services.join(' ').toLowerCase();
      if (activeCategory === 'seo_growth') return servicesStr.includes('seo') || servicesStr.includes('growth');
      if (activeCategory === 'paid_media') return servicesStr.includes('paid') || servicesStr.includes('media');
      if (activeCategory === 'headless_web') return servicesStr.includes('next.js') || servicesStr.includes('web') || servicesStr.includes('ecommerce');
      if (activeCategory === 'ai_systems') return servicesStr.includes('ai') || servicesStr.includes('rag') || servicesStr.includes('automation');
      if (activeCategory === 'brand_design') return servicesStr.includes('brand') || servicesStr.includes('design');

      return true;
    });
  }, [activeCategory, searchQuery]);

  return (
    <div className="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 selection:bg-zinc-900 selection:text-white dark:selection:bg-white dark:selection:text-zinc-900 pt-24 pb-20">
      
      {/* 1. HERO SECTION */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-12 sm:mb-16">
        <div className="flex flex-col items-start gap-4">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono tracking-tight bg-zinc-100 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
            Verified Case Studies & Transformation Registry
          </div>

          <h1 className="text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50 max-w-3xl leading-[1.12]">
            Proven Growth Outcomes, Not Just Theoretical Claims.
          </h1>

          <p className="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 max-w-2xl leading-relaxed">
            Explore how modern marketing agencies, growth studios, and consultants leverage Cora to scale organic acquisition, accelerate conversion velocities, and deploy autonomous AI operations.
          </p>

          {/* Quick Stats Strip */}
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 w-full mt-4">
            <div className="p-4 rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800">
              <span className="text-xs text-zinc-400 font-mono block">Published Studies</span>
              <span className="text-xl sm:text-2xl font-bold font-mono text-zinc-950 dark:text-zinc-50 mt-1 block">3</span>
            </div>
            <div className="p-4 rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800">
              <span className="text-xs text-zinc-400 font-mono block">Average Pipeline ROAS</span>
              <span className="text-xl sm:text-2xl font-bold font-mono text-zinc-950 dark:text-zinc-50 mt-1 block">3.8x</span>
            </div>
            <div className="p-4 rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800">
              <span className="text-xs text-zinc-400 font-mono block">Median Mobile Latency</span>
              <span className="text-xl sm:text-2xl font-bold font-mono text-zinc-950 dark:text-zinc-50 mt-1 block">420ms</span>
            </div>
            <div className="p-4 rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800">
              <span className="text-xs text-zinc-400 font-mono block">AI Lead Precision</span>
              <span className="text-xl sm:text-2xl font-bold font-mono text-zinc-950 dark:text-zinc-50 mt-1 block">94%</span>
            </div>
          </div>
        </div>
      </section>

      {/* 2. FILTER & SEARCH CONTROL BAR */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 sticky top-20 z-20 backdrop-blur-md py-2">
        <div className="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 p-2 bg-white/95 dark:bg-zinc-900/95 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-sm">
          
          {/* Category Tabs */}
          <div className="flex items-center gap-1 overflow-x-auto no-scrollbar pb-1 md:pb-0">
            {PROJECT_CATEGORIES.map((cat) => {
              const active = activeCategory === cat.id;
              return (
                <button
                  key={cat.id}
                  onClick={() => setActiveCategory(cat.id)}
                  className={`px-3.5 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all duration-150 cursor-pointer ${
                    active
                      ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-sm'
                      : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800'
                  }`}
                >
                  {cat.label}
                </button>
              );
            })}
          </div>

          {/* Search Box */}
          <div className="relative shrink-0 w-full md:w-64">
            <Search className="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400" />
            <input
              type="text"
              placeholder="Search case studies..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full pl-9 pr-3 py-1.5 rounded-xl text-xs bg-zinc-100/80 dark:bg-zinc-800/80 border border-zinc-200/60 dark:border-zinc-700/60 focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-colors"
            />
          </div>
        </div>
      </section>

      {/* 3. CASE STUDIES GRID */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        {filteredProjects.length === 0 ? (
          <div className="p-12 text-center bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800">
            <Layers className="w-8 h-8 mx-auto text-zinc-400 mb-2" />
            <p className="text-sm font-medium text-zinc-700 dark:text-zinc-300">No case studies match your query</p>
            <p className="text-xs text-zinc-500 mt-1">Try clearing your search term or selecting "All Projects".</p>
            <button
              onClick={() => { setActiveCategory('all'); setSearchQuery(''); }}
              className="mt-4 px-4 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition"
            >
              Reset Filters
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredProjects.map((project) => (
              <Link 
                key={project.id}
                href={`/projects/${project.slug}`}
                className="group flex flex-col justify-between bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden hover:border-zinc-400 dark:hover:border-zinc-600 transition-all duration-200 shadow-sm hover:shadow-md"
              >
                <div>
                  {/* Hero Image Container */}
                  <div className="relative aspect-[16/10] w-full overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    <img 
                      src={project.heroImageUrl} 
                      alt={project.title}
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    
                    {/* Top Floating Badges */}
                    <div className="absolute top-3 left-3 right-3 flex items-center justify-between gap-2 pointer-events-none">
                      <span className="px-2.5 py-1 rounded-full text-[11px] font-mono font-medium bg-zinc-900/80 text-white backdrop-blur-md">
                        {project.clientIndustry}
                      </span>

                      {project.isConfidential ? (
                        <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-zinc-900/80 text-zinc-200 backdrop-blur-md">
                          <Lock className="w-2.5 h-2.5 text-zinc-400" />
                          Stealth
                        </span>
                      ) : (
                        <span className="px-2.5 py-1 rounded-full text-[11px] font-medium bg-zinc-900/80 text-zinc-200 backdrop-blur-md">
                          {project.yearCompleted}
                        </span>
                      )}
                    </div>

                    {/* Bottom Primary KPI Pill */}
                    {project.kpis && project.kpis.length > 0 && (
                      <div className="absolute bottom-3 left-3 pointer-events-none">
                        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/95 dark:bg-zinc-900/95 text-zinc-950 dark:text-zinc-50 border border-zinc-200/80 dark:border-zinc-800 shadow-sm backdrop-blur-md">
                          <TrendingUp className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                          <span className="text-xs font-bold font-mono">{project.kpis[0].value}</span>
                          <span className="text-[11px] text-zinc-500 font-sans">{project.kpis[0].label}</span>
                        </div>
                      </div>
                    )}
                  </div>

                  {/* Body Content */}
                  <div className="p-5">
                    {/* Client Name & Services */}
                    <div className="flex items-center justify-between text-xs text-zinc-500 font-medium mb-2">
                      <span>{project.clientName}</span>
                      <span>{project.projectDuration}</span>
                    </div>

                    <h2 className="text-base font-bold text-zinc-950 dark:text-zinc-50 leading-snug group-hover:text-zinc-700 dark:group-hover:text-zinc-300 transition-colors line-clamp-2">
                      {project.title}
                    </h2>

                    <p className="text-xs text-zinc-600 dark:text-zinc-400 mt-2 line-clamp-2 leading-relaxed">
                      {project.executiveSummary}
                    </p>

                    {/* Services Tags */}
                    <div className="flex flex-wrap gap-1.5 mt-4">
                      {project.services.slice(0, 3).map((svc, i) => (
                        <span 
                          key={i}
                          className="px-2 py-0.5 rounded-lg text-[10px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200/60 dark:border-zinc-700/60"
                        >
                          {svc}
                        </span>
                      ))}
                      {project.services.length > 3 && (
                        <span className="px-1.5 py-0.5 rounded-lg text-[10px] font-mono text-zinc-400">
                          +{project.services.length - 3}
                        </span>
                      )}
                    </div>
                  </div>
                </div>

                {/* Card Footer */}
                <div className="px-5 py-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-xs font-medium text-zinc-900 dark:text-zinc-100 group-hover:bg-zinc-50/50 dark:group-hover:bg-zinc-800/40 transition-colors">
                  <span>View Full Case Study</span>
                  <ChevronRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                </div>
              </Link>
            ))}
          </div>
        )}
      </section>

      {/* 4. CONVERSION CTA BANNER */}
      <section className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="p-8 sm:p-12 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-center relative overflow-hidden shadow-sm">
          <div className="max-w-2xl mx-auto">
            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 mb-4">
              <Sparkles className="w-3.5 h-3.5 text-zinc-900 dark:text-zinc-100" />
              Tailored Agency Growth Sprint
            </div>

            <h2 className="text-2xl sm:text-4xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">
              Ready to Achieve Indisputable Results for Your Brand?
            </h2>

            <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 mt-3 leading-relaxed">
              Book a 30-minute growth teardown with our senior marketing team. We'll audit your current organic search footprint, ad spend attribution, and conversion funnel.
            </p>

            <div className="flex flex-col sm:flex-row items-center justify-center gap-3 mt-6">
              <Link
                href="/contact"
                className="w-full sm:w-auto px-6 py-2.5 rounded-xl text-xs font-medium bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-950 transition shadow-sm"
              >
                Schedule Strategy Audit
              </Link>
              <Link
                href="/features"
                className="w-full sm:w-auto px-6 py-2.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 transition"
              >
                Explore Agency Features
              </Link>
            </div>
          </div>
        </div>
      </section>

    </div>
  );
}
