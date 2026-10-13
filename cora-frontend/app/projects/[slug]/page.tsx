import React from 'react';
import { notFound } from 'next/navigation';
import Link from 'next/link';
import { Metadata } from 'next';
import { 
  ArrowLeft, 
  ArrowRight, 
  ExternalLink, 
  Lock, 
  ShieldCheck, 
  TrendingUp, 
  CheckCircle2, 
  Layers, 
  Sparkles, 
  Clock, 
  Building2, 
  Quote 
} from 'lucide-react';
import { SEED_PROJECTS, getProjectBySlug, ProjectCaseStudy } from '@/lib/projects-data';

interface PageProps {
  params: Promise<{ slug: string }>;
}

export async function generateStaticParams() {
  return SEED_PROJECTS.map((project) => ({
    slug: project.slug,
  }));
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;
  const project = await getProjectBySlug(slug);

  if (!project) {
    return {
      title: 'Case Study Not Found | Cora Agency',
      description: 'The requested case study could not be located.'
    };
  }

  const title = project.metaTitle || `${project.title} | Cora Agency Case Study`;
  const description = project.metaDescription || project.executiveSummary;

  return {
    title,
    description,
    openGraph: {
      title,
      description,
      images: [
        {
          url: project.heroImageUrl,
          width: 1200,
          height: 630,
          alt: project.title,
        }
      ]
    }
  };
}

export default async function ProjectCaseStudyPage({ params }: PageProps) {
  const { slug } = await params;
  const project = await getProjectBySlug(slug);

  if (!project) {
    notFound();
  }

  // Schema.org Graph combining Article and CreativeWork
  const jsonLd = {
    '@context': 'https://schema.org',
    '@graph': [
      {
        '@type': 'Article',
        '@id': `https://cora.local/projects/${project.slug}#article`,
        'headline': project.title,
        'description': project.executiveSummary,
        'image': project.heroImageUrl,
        'datePublished': '2026-09-01T00:00:00Z',
        'dateModified': '2026-10-12T00:00:00Z',
        'mainEntityOfPage': `https://cora.local/projects/${project.slug}`,
        'publisher': {
          '@type': 'Organization',
          'name': 'Cora Agency',
          'url': 'https://cora.local'
        },
        'about': {
          '@type': 'CreativeWork',
          'name': project.title,
          'genre': project.clientIndustry,
          'keywords': project.services
        }
      }
    ]
  };

  return (
    <div className="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 selection:bg-zinc-900 selection:text-white dark:selection:bg-white dark:selection:text-zinc-900 pt-24 pb-20">
      
      {/* JSON-LD Structured Data */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
      />

      <article className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Back Link */}
        <div className="mb-6">
          <Link
            href="/projects"
            className="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-zinc-950 dark:hover:text-zinc-100 transition-colors"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            Back to All Projects & Case Studies
          </Link>
        </div>

        {/* 1. HEADER METADATA STRIP */}
        <div className="flex flex-wrap items-center gap-2 mb-4">
          <span className="px-2.5 py-1 rounded-lg text-xs font-mono font-medium bg-zinc-200/80 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
            {project.clientIndustry}
          </span>

          {project.isConfidential ? (
            <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono font-medium bg-amber-100 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border border-amber-200/60 dark:border-amber-800/60">
              <Lock className="w-3 h-3" />
              Confidential Client
            </span>
          ) : (
            <span className="px-2.5 py-1 rounded-lg text-xs font-mono font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
              Client: {project.clientName}
            </span>
          )}

          {project.projectDuration && (
            <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono text-zinc-500 bg-zinc-100 dark:bg-zinc-800">
              <Clock className="w-3 h-3" />
              {project.projectDuration}
            </span>
          )}

          {project.liveUrl && !project.isConfidential && (
            <a
              href={project.liveUrl}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono font-medium text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white bg-zinc-100 dark:bg-zinc-800 transition-colors ml-auto"
            >
              <span>Visit Production Site</span>
              <ExternalLink className="w-3 h-3" />
            </a>
          )}
        </div>

        {/* 2. CASE STUDY HEADLINE & SUBTITLE */}
        <h1 className="text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50 leading-[1.14]">
          {project.title}
        </h1>

        {project.subtitle && (
          <p className="text-base sm:text-lg text-zinc-600 dark:text-zinc-400 mt-4 leading-relaxed max-w-3xl">
            {project.subtitle}
          </p>
        )}

        {/* Capability Tags */}
        <div className="flex flex-wrap gap-2 mt-6 mb-8">
          {project.services.map((svc, idx) => (
            <span
              key={idx}
              className="px-3 py-1 rounded-xl text-xs font-medium bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs"
            >
              {svc}
            </span>
          ))}
        </div>

        {/* 3. HERO VISUAL CONTAINER */}
        <div className="relative aspect-[16/9] w-full rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-800 shadow-sm mb-12">
          <img
            src={project.heroImageUrl}
            alt={project.title}
            className="w-full h-full object-cover"
          />
        </div>

        {/* 4. QUANTITATIVE PROOF: KPI MATRIX */}
        {project.kpis && project.kpis.length > 0 && (
          <div className="mb-14">
            <h2 className="text-xs font-mono uppercase tracking-wider text-zinc-400 mb-3">
              Verified Business Impact & Outcomes
            </h2>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
              {project.kpis.map((kpi, idx) => (
                <div
                  key={idx}
                  className="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs"
                >
                  <div className="flex items-center justify-between">
                    <span className="text-3xl sm:text-4xl font-bold font-mono text-zinc-950 dark:text-zinc-50 tracking-tight">
                      {kpi.value}
                    </span>
                    <TrendingUp className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                  </div>
                  <h3 className="text-sm font-semibold text-zinc-800 dark:text-zinc-200 mt-2">
                    {kpi.label}
                  </h3>
                  {kpi.context && (
                    <p className="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                      {kpi.context}
                    </p>
                  )}
                </div>
              ))}
            </div>
          </div>
        )}

        {/* 5. EXECUTIVE SUMMARY CARD */}
        <div className="p-6 sm:p-8 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs mb-12">
          <span className="text-xs font-mono uppercase tracking-wider text-zinc-400 block mb-2">
            Executive Summary
          </span>
          <p className="text-sm sm:text-base text-zinc-800 dark:text-zinc-200 leading-relaxed font-medium">
            {project.executiveSummary}
          </p>
        </div>

        {/* 6. TRANSFORMATION NARRATIVE (THE 4-ACT STORY) */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
          
          {/* Act 1: The Challenge */}
          <div className="p-6 sm:p-8 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
            <div className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 mb-4">
              Act I — The Structural Bottleneck
            </div>
            <h2 className="text-lg font-bold text-zinc-950 dark:text-zinc-50 mb-3">
              The Challenge
            </h2>
            <div className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed space-y-3 whitespace-pre-line">
              {project.challengeContent}
            </div>
          </div>

          {/* Act 2: The Solution */}
          <div className="p-6 sm:p-8 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
            <div className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 mb-4">
              Act II — Strategic Methodology
            </div>
            <h2 className="text-lg font-bold text-zinc-950 dark:text-zinc-50 mb-3">
              The Solution & Architecture
            </h2>
            <div className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed space-y-3 whitespace-pre-line">
              {project.solutionContent}
            </div>
          </div>
        </div>

        {/* 7. DELIVERABLES BREAKDOWN */}
        {project.deliverables && project.deliverables.length > 0 && (
          <div className="p-6 sm:p-8 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs mb-12">
            <h2 className="text-base font-bold text-zinc-950 dark:text-zinc-50 mb-4">
              Core Artifacts & Deliverables Completed
            </h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
              {project.deliverables.map((item, idx) => (
                <div key={idx} className="flex items-start gap-2.5 text-xs sm:text-sm text-zinc-700 dark:text-zinc-300">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                  <span>{item}</span>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* 8. GALLERY & VISUAL WALKTHROUGH */}
        {project.galleryImages && project.galleryImages.length > 0 && (
          <div className="mb-14">
            <h2 className="text-xs font-mono uppercase tracking-wider text-zinc-400 mb-3">
              System Interface & Architecture Gallery
            </h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              {project.galleryImages.map((img, idx) => (
                <div
                  key={idx}
                  className="rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs"
                >
                  <div className="aspect-[16/10] overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    <img
                      src={img.url}
                      alt={img.alt || img.caption || 'Project asset'}
                      className="w-full h-full object-cover"
                    />
                  </div>
                  {img.caption && (
                    <div className="p-4 border-t border-zinc-100 dark:border-zinc-800 text-xs text-zinc-600 dark:text-zinc-400 font-medium">
                      {img.caption}
                    </div>
                  )}
                </div>
              ))}
            </div>
          </div>
        )}

        {/* 9. VERIFIED SOCIAL PROOF & TESTIMONIAL */}
        {project.testimonial && (
          <div className="p-8 sm:p-10 rounded-3xl bg-zinc-900 text-white dark:bg-zinc-900 dark:border dark:border-zinc-800 shadow-md mb-14 relative overflow-hidden">
            <Quote className="w-10 h-10 text-zinc-700/60 absolute right-6 top-6" />
            <p className="text-base sm:text-xl font-medium leading-relaxed max-w-2xl text-zinc-100">
              "{project.testimonial.quote}"
            </p>

            <div className="flex items-center gap-3 mt-6 pt-6 border-t border-zinc-800">
              <div className="w-10 h-10 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center font-bold font-mono text-zinc-200 text-xs shrink-0">
                {project.testimonial.author.slice(0, 2).toUpperCase()}
              </div>
              <div>
                <div className="flex items-center gap-1.5">
                  <span className="text-xs sm:text-sm font-bold text-white">
                    {project.testimonial.author}
                  </span>
                  {project.testimonial.verified && (
                    <span className="inline-flex items-center gap-0.5 text-[10px] font-mono text-emerald-400 bg-emerald-950/60 border border-emerald-800/60 px-1.5 py-0.5 rounded-md">
                      <ShieldCheck className="w-2.5 h-2.5" />
                      Verified
                    </span>
                  )}
                </div>
                <p className="text-[11px] text-zinc-400">
                  {project.testimonial.role} · {project.testimonial.company}
                </p>
              </div>
            </div>
          </div>
        )}

        {/* 10. CONVERSION CTA BRIDGE */}
        <div className="p-8 sm:p-12 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-center shadow-sm">
          <div className="max-w-xl mx-auto">
            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 mb-3">
              <Sparkles className="w-3.5 h-3.5 text-zinc-900 dark:text-zinc-100" />
              Tailored Strategy Sprint
            </span>

            <h2 className="text-xl sm:text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">
              {project.ctaTitle}
            </h2>

            {project.ctaSubtitle && (
              <p className="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed">
                {project.ctaSubtitle}
              </p>
            )}

            <div className="mt-6">
              <Link
                href={project.ctaButtonUrl}
                className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-medium bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-950 transition shadow-sm"
              >
                <span>{project.ctaButtonText}</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </Link>
            </div>
          </div>
        </div>

      </article>

    </div>
  );
}
