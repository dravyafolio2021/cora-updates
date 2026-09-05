'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  ShieldCheck, 
  Check, 
  Share2, 
  Sparkles,
  ChevronRight
} from 'lucide-react';
import { ToolAgentData } from '@/lib/tools-agent-config';
import { useToast } from '@/components/ui/Toast';

interface ToolAiSdrHeroCardProps {
  toolId: string;
  agentData: ToolAgentData;
}

export function ToolAiSdrHeroCard({ toolId, agentData }: ToolAiSdrHeroCardProps) {
  const { showToast } = useToast();
  const [copiedShare, setCopiedShare] = useState(false);

  const handleShareTool = () => {
    if (typeof window !== 'undefined') {
      navigator.clipboard.writeText(window.location.href);
      setCopiedShare(true);
      showToast('Tool link copied to clipboard!');
      setTimeout(() => setCopiedShare(false), 2200);
    }
  };

  const firstName = agentData.agent.name.split(' ')[0];

  return (
    <div className="relative rounded-2xl overflow-hidden shadow-lg border border-zinc-800/90 bg-zinc-950 h-[218px] flex flex-col justify-between p-3.5 text-white group">
      
      {/* Full-Bleed 3D Background Artwork */}
      <Image
        src={agentData.card1.image}
        alt={agentData.card1.headline}
        fill
        priority
        className="object-cover object-top opacity-55 group-hover:scale-105 transition-transform duration-700 pointer-events-none"
        sizes="(max-width: 768px) 100vw, 380px"
      />

      {/* Top soft vignette */}
      <div className="absolute inset-x-0 top-0 h-14 bg-gradient-to-b from-black/85 via-black/40 to-transparent pointer-events-none" />

      {/* Bottom gradient melt */}
      <div className="absolute inset-x-0 bottom-0 h-[82%] bg-gradient-to-t from-zinc-950 via-zinc-950/95 via-55% to-transparent pointer-events-none" />

      {/* Top Header Row: Live Agent Persona + Zero Account Badge */}
      <div className="relative z-10 flex items-center justify-between gap-2">
        <div className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/70 backdrop-blur-md text-zinc-200 text-[11px] font-medium border border-white/15 shadow-sm">
          <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
          <span className="font-semibold">{agentData.agent.name}</span>
          <span className="text-zinc-500 font-mono text-[9.5px]">• AI Co-Founder</span>
        </div>

        <button
          type="button"
          onClick={handleShareTool}
          title="Share this tool"
          className="p-1 rounded-lg bg-white/10 hover:bg-white text-zinc-300 hover:text-zinc-950 transition-all cursor-pointer backdrop-blur-md border border-white/15 shadow-xs shrink-0"
        >
          {copiedShare ? (
            <Check className="w-3 h-3 text-emerald-400" />
          ) : (
            <Share2 className="w-3 h-3" />
          )}
        </button>
      </div>

      {/* Bottom Content Area: Value Proposition & Subtle TOFU Invitation */}
      <div className="relative z-10 space-y-2 mt-auto pt-2">
        
        {/* Freedom & Privacy Guarantee Banner */}
        <div className="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-950/50 border border-emerald-500/30 text-emerald-300 backdrop-blur-md">
          <ShieldCheck className="w-3 h-3 text-emerald-400 shrink-0" />
          <span className="text-[10px] font-mono font-bold tracking-wide">
            100% Free Forever • Zero Login Required
          </span>
        </div>

        {/* Co-Founder Advisory Statement */}
        <p className="text-[11.5px] text-zinc-300 font-normal leading-snug line-clamp-2">
          {agentData.card1.primaryText}
        </p>

        {/* Soft, Non-Salesy Call-to-Action to Claim a Free Workspace */}
        <Link
          href={`/workspace/login?mode=signup&ref=tofu_${toolId}&agent=${encodeURIComponent(agentData.agent.name)}`}
          className="w-full py-1.5 px-3 rounded-xl bg-white hover:bg-zinc-100 text-zinc-950 font-bold text-xs flex items-center justify-between gap-1.5 shadow-sm active:scale-[0.99] transition-all group/btn"
        >
          <span className="flex items-center gap-1.5">
            <Sparkles className="w-3 h-3 text-zinc-800" />
            <span>Claim Free Workspace with {firstName}</span>
          </span>
          <span className="text-[10px] font-mono font-medium text-zinc-500 group-hover/btn:text-zinc-950 flex items-center">
            No Card Needed
            <ChevronRight className="w-3 h-3 text-zinc-900 group-hover/btn:translate-x-0.5 transition-transform ml-0.5" />
          </span>
        </Link>
      </div>

    </div>
  );
}
