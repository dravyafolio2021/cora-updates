'use client';

import React from 'react';
import Image from 'next/image';
import { ArrowRight, Sparkles, Shield, Clock } from 'lucide-react';

interface DemoPersonalizedCalloutProps {
  onOpenDrawer: () => void;
}

export function DemoPersonalizedCallout({ onOpenDrawer }: DemoPersonalizedCalloutProps) {
  return (
    <section className="py-16 sm:py-24 bg-white">
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        <div className="w-full rounded-[36px] bg-[#0A0D10] text-white p-8 sm:p-12 md:p-16 border border-zinc-800 shadow-2xl relative overflow-hidden">
          
          {/* Subtle Ambient Backlight */}
          <div
            className="absolute top-0 right-1/4 w-[500px] h-[500px] pointer-events-none opacity-20"
            style={{
              background: 'radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%)',
            }}
          />

          <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
            
            {/* Left Content Column */}
            <div className="lg:col-span-6 space-y-6 text-center lg:text-left">
              
              <div className="flex items-center justify-center lg:justify-start gap-2">
                <span className="font-display font-bold text-2xl tracking-tight text-white">
                  Cora
                </span>
                <span className="text-zinc-500">•</span>
                <span className="text-xs font-mono text-zinc-400 uppercase tracking-wider">
                  1:1 Enterprise Walkthrough
                </span>
              </div>

              <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-white tracking-tight leading-[1.15]">
                Want a 1:1 demo instead?
              </h2>

              <p className="text-sm sm:text-base text-zinc-300 font-normal leading-relaxed max-w-[480px]">
                See how Cora boosts studio productivity, automates 18% GST billing, and streamlines commercial shoot agreements with a dedicated workflow specialist.
              </p>

              <div className="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                <button
                  type="button"
                  onClick={onOpenDrawer}
                  className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-white text-zinc-950 text-sm font-semibold hover:bg-zinc-100 transition-all shadow-md"
                >
                  <span>Book a demo</span>
                  <ArrowRight className="w-4 h-4" />
                </button>

                <div className="flex items-center gap-2 text-xs font-mono text-zinc-400">
                  <Clock className="w-3.5 h-3.5" />
                  <span>30 mins • Personalized to your studio</span>
                </div>
              </div>

            </div>

            {/* Right UI Preview Graphic Column */}
            <div className="lg:col-span-6 relative flex justify-center lg:justify-end">
              <div className="relative w-full max-w-[480px] h-[280px] sm:h-[320px] rounded-2xl overflow-hidden border border-zinc-800 shadow-2xl bg-zinc-900">
                <Image
                  src="/images/cora_unified_hub_sprawl_clean.png"
                  alt="Cora Studio Workspace Dashboard"
                  fill
                  sizes="(max-width: 768px) 100vw, 480px"
                  className="object-cover object-left-top filter contrast-105"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-[#0A0D10]/80 via-transparent to-transparent pointer-events-none" />
                
                {/* Floating Badge */}
                <div className="absolute bottom-4 left-4 right-4 bg-zinc-950/90 backdrop-blur-md p-3 rounded-xl border border-zinc-700/80 flex items-center justify-between text-xs font-mono">
                  <div className="flex items-center gap-2">
                    <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                    <span className="text-zinc-200 font-bold">Studio OS v4.0</span>
                  </div>
                  <span className="text-zinc-400">All Modules Integrated</span>
                </div>
              </div>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
