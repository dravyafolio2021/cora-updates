import React from 'react';
import { ArrowDown, ArrowRight } from 'lucide-react';

interface EditorialVisualGuideProps {
  guide?: 'publishing-postmortem';
}

const oldWorkflow = [
  ['01', 'Find an idea'],
  ['02', 'Search manually'],
  ['03', 'Draft elsewhere'],
  ['04', 'Copy to WordPress'],
  ['05', 'Format and repair'],
];

const newWorkflow = [
  'Begin with a question we have actually heard.',
  'Create one evidence-backed editorial brief.',
  'Develop the written answer and visual explanation together.',
  'Run one publishing check, then release the page.',
  'Improve the same URL as real questions arrive.',
];

function PublishingPostmortemGuide() {
  return (
    <figure aria-labelledby="visual-field-guide-title" className="my-4 border-y-2 border-zinc-950 bg-[#F1EDE3] text-[#181713]">
      <div className="grid border-b border-zinc-950 lg:grid-cols-[1fr_15rem]">
        <div className="px-5 py-8 sm:px-8 sm:py-11 lg:border-r lg:border-zinc-950">
          <div className="mb-10 flex items-center justify-between gap-4 border-b border-zinc-400 pb-3 font-mono text-[10px] font-bold uppercase tracking-[0.18em]">
            <span>Field note 01</span>
            <span>Publishing systems</span>
          </div>
          <p className="mb-3 font-mono text-[10px] font-bold uppercase tracking-[0.18em] text-[#B6422B]">The real publishing problem</p>
          <h2 id="visual-field-guide-title" className="max-w-4xl font-display text-3xl font-black leading-[1.05] tracking-[-0.035em] sm:text-5xl">
            We did not run out of ideas. We made every idea cross an obstacle course.
          </h2>
          <p className="mt-6 max-w-2xl border-l-2 border-[#B6422B] pl-4 text-sm leading-7 text-zinc-700 sm:text-base">
            When the path from a useful answer to a published page has too many handoffs, consistency becomes a willpower problem.
          </p>
        </div>

        <div className="grid grid-cols-2 lg:grid-cols-1">
          <div className="border-r border-zinc-950 px-5 py-7 lg:border-b lg:border-r-0 lg:px-7">
            <div className="font-display text-6xl font-black leading-none">2</div>
            <div className="mt-3 font-mono text-[10px] uppercase leading-5 tracking-wider text-zinc-600">Months after the website launched</div>
          </div>
          <div className="px-5 py-7 lg:px-7">
            <div className="font-display text-6xl font-black leading-none text-[#B6422B]">0</div>
            <div className="mt-3 font-mono text-[10px] uppercase leading-5 tracking-wider text-zinc-600">Useful articles published</div>
          </div>
        </div>
      </div>

      <div className="px-5 py-9 sm:px-8 sm:py-12">
        <div className="mb-6 grid gap-3 sm:grid-cols-[10rem_1fr]">
          <div className="font-mono text-[10px] font-bold uppercase tracking-[0.18em] text-[#B6422B]">Figure A</div>
          <div>
            <h3 className="font-display text-2xl font-black tracking-tight">The five-handoff maze</h3>
            <p className="mt-1 text-sm text-zinc-600">Friction accumulated at every transfer of context.</p>
          </div>
        </div>

        <div className="border-y border-zinc-950">
          <div className="grid md:grid-cols-5">
            {oldWorkflow.map(([number, label], index) => (
              <div key={label} className="relative grid grid-cols-[3.5rem_1fr] items-center border-b border-zinc-400 py-4 last:border-b-0 md:block md:min-h-40 md:border-b-0 md:border-r md:px-4 md:py-5 md:last:border-r-0">
                <span className="font-mono text-[10px] text-zinc-500">{number}</span>
                <p className="font-display text-lg font-bold leading-tight md:mt-14">{label}</p>
                {index < oldWorkflow.length - 1 && <ArrowRight className="absolute -right-2.5 top-7 z-10 hidden h-4 w-4 bg-[#F1EDE3] text-[#B6422B] md:block" />}
              </div>
            ))}
          </div>
        </div>

        <div className="grid border-b border-zinc-950 sm:grid-cols-[1fr_4rem_1fr]">
          <div className="py-6 sm:pr-6">
            <div className="font-mono text-[10px] uppercase tracking-wider text-zinc-500">Repeated decisions</div>
            <div className="mt-2 font-display text-xl font-black">Research + tools + formatting</div>
          </div>
          <div className="flex items-center justify-center border-y border-zinc-400 py-3 font-display text-3xl font-black text-[#B6422B] sm:border-x sm:border-y-0">×</div>
          <div className="py-6 sm:pl-6">
            <div className="font-mono text-[10px] uppercase tracking-wider text-zinc-500">No definition of done</div>
            <div className="mt-2 font-display text-xl font-black">Every post becomes a project</div>
          </div>
        </div>

        <div className="flex items-center justify-center gap-3 py-6 text-center">
          <ArrowDown className="h-4 w-4 text-[#B6422B]" />
          <p className="font-display text-lg font-black">Useful ideas stayed private. The website stayed a brochure.</p>
        </div>

        <div className="mt-8 grid border-t-2 border-zinc-950 lg:grid-cols-[0.7fr_1.3fr]">
          <div className="border-b border-zinc-950 py-8 lg:border-b-0 lg:border-r lg:pr-8">
            <div className="font-mono text-[10px] font-bold uppercase tracking-[0.18em] text-[#B6422B]">The lesson</div>
            <blockquote className="mt-5 font-display text-3xl font-black leading-tight tracking-tight">A content problem can actually be an operations problem.</blockquote>
            <p className="mt-5 text-sm leading-6 text-zinc-700">Motivation is unreliable. A reusable path from question to publication makes useful work repeatable.</p>
          </div>

          <div className="py-8 lg:pl-8">
            <div className="mb-5 flex items-end justify-between border-b border-zinc-400 pb-3">
              <div>
                <div className="font-mono text-[10px] font-bold uppercase tracking-[0.18em] text-[#B6422B]">Figure B</div>
                <h3 className="mt-2 font-display text-2xl font-black">The new publishing loop</h3>
              </div>
              <span className="hidden font-mono text-[10px] uppercase tracking-wider text-zinc-500 sm:block">One source · one URL</span>
            </div>
            <ol>
              {newWorkflow.map((step, index) => (
                <li key={step} className="grid grid-cols-[3rem_1fr] border-b border-zinc-400 py-4 last:border-b-0">
                  <span className="font-mono text-xs font-bold text-[#B6422B]">0{index + 1}</span>
                  <div className="flex items-center justify-between gap-4">
                    <span className="text-sm font-semibold leading-6">{step}</span>
                    {index < newWorkflow.length - 1 && <ArrowDown className="h-3.5 w-3.5 shrink-0 text-zinc-400" />}
                  </div>
                </li>
              ))}
            </ol>
          </div>
        </div>
      </div>

      <figcaption className="border-t border-zinc-950 px-5 py-4 font-mono text-[10px] uppercase leading-5 tracking-wider text-zinc-600 sm:px-8">
        Read the diagram for the operating lesson. Read the article below for the evidence, decisions, and complete explanation.
      </figcaption>
    </figure>
  );
}

export function EditorialVisualGuide({ guide }: EditorialVisualGuideProps) {
  if (guide === 'publishing-postmortem') return <PublishingPostmortemGuide />;
  return null;
}
