'use client';

import React, { useEffect, useRef, useState } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { ArrowRight, CheckCircle2 } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

// 7 Key Ecosystem Connectors
const RADIAL_NODES = [
  {
    id: 'whatsapp',
    name: 'WhatsApp Cloud API',
    category: 'Native Dispatch',
    angle: 165, // far left
    icon: (
      <svg className="w-6 h-6 text-[#25D366]" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.592 2.654-.696c1.004.573 1.772.884 2.806.884 3.181 0 5.767-2.586 5.768-5.766.001-3.18-2.585-5.767-5.768-5.767zm7.659 5.765c-.002 4.225-3.441 7.661-7.66 7.661-1.258 0-2.45-.308-3.497-.852l-4.533 1.189 1.21-4.417c-.628-1.096-.962-2.348-.96-3.581.002-4.225 3.441-7.661 7.66-7.661 4.222 0 7.66 3.438 7.66 7.663z" />
      </svg>
    ),
  },
  {
    id: 'claude',
    name: 'Anthropic Claude 3.5',
    category: 'Frontier AI',
    angle: 140, // mid left
    icon: (
      <svg className="w-6 h-6 text-[#D97706]" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2L9.5 9.5 2 12l7.5 2.5L12 22l2.5-7.5L22 12l-7.5-2.5z" />
      </svg>
    ),
  },
  {
    id: 'openai',
    name: 'OpenAI GPT-4o',
    category: 'Frontier AI',
    angle: 115, // upper mid left
    icon: (
      <svg className="w-6 h-6 text-zinc-900" viewBox="0 0 24 24" fill="currentColor">
        <path d="M20.5 10.5c-.3-1.6-1.5-2.9-3.1-3.3-.4-.9-1.2-1.7-2.2-2.1-1.6-.7-3.5-.4-4.8.7-1-.3-2.1-.1-3 .5-1.4.9-2.1 2.6-1.8 4.2-1.1.5-1.9 1.5-2.1 2.7-.4 1.7.4 3.4 1.9 4.3.1 1.1.7 2.1 1.7 2.7 1.4.9 3.2.9 4.6.1.9.7 2.1 1 3.3.7 1.6-.4 2.9-1.6 3.3-3.2 1.1-.5 1.9-1.5 2.2-2.7.4-1.6-.3-3.4-1.8-4.3z" />
      </svg>
    ),
  },
  {
    id: 'razorpay',
    name: 'Razorpay UPI & RuPay',
    category: 'Instant Indian Settlement',
    angle: 90, // top center
    icon: (
      <svg className="w-6 h-6 text-[#0C2340]" viewBox="0 0 24 24" fill="currentColor">
        <path d="M13.5 2L4 14h7l-1.5 8L19 10h-7l1.5-8z" />
      </svg>
    ),
  },
  {
    id: 'gemini',
    name: 'Google Gemini 2.0 Flash',
    category: 'Sub-400ms AI',
    angle: 65, // upper mid right
    icon: (
      <svg className="w-6 h-6 text-[#1A73E8]" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-6h2v6zm0-8h-2V7h2v2zm4 8h-2V7h2v10z" />
      </svg>
    ),
  },
  {
    id: 'stripe',
    name: 'Stripe International',
    category: 'Global USD/EUR Rails',
    angle: 40, // mid right
    icon: (
      <svg className="w-6 h-6 text-[#635BFF]" viewBox="0 0 24 24" fill="currentColor">
        <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.977 15.617.414 12.899.414c-6.103 0-10.222 3.167-10.222 8.528 0 4.195 2.923 6.326 6.848 7.674 2.378.826 3.187 1.547 3.187 2.502 0 .977-.852 1.503-2.316 1.503-2.617 0-5.424-1.206-7.252-2.155L2.2 24c1.944 1.011 4.966 1.586 8.016 1.586 6.376 0 10.639-3.084 10.639-8.541 0-4.444-2.825-6.524-6.879-7.895z" />
      </svg>
    ),
  },
  {
    id: 'gdrive',
    name: 'Google Drive Cloud Vault',
    category: '4K RAW Media Storage',
    angle: 15, // far right
    icon: (
      <svg className="w-6 h-6 text-[#0F9D58]" viewBox="0 0 24 24" fill="currentColor">
        <path d="M7.71 3.5L1.15 15l3.43 6 6.56-11.5L7.71 3.5zm8.58 0H9.43l6.56 11.5 3.43-6-3.13-5.5zm1.14 12H4.57L1.14 21.5h19.72l-3.43-6z" />
      </svg>
    ),
  },
];

export function IntegrationsRadialSection() {
  const sectionRef = useRef<HTMLElement>(null);
  const [hoveredNode, setHoveredNode] = useState<string | null>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      // Staggered reveal for header & card
      gsap.fromTo(
        '.integ-anim-item',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          stagger: 0.1,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 78%',
          },
        }
      );

      // Subtle float animation on each radial node
      RADIAL_NODES.forEach((node) => {
        gsap.to(`.radial-node-${node.id}`, {
          y: '+=6',
          duration: 2.8 + Math.random() * 1.5,
          repeat: -1,
          yoyo: true,
          ease: 'sine.inOut',
          delay: Math.random() * 0.5,
        });
      });
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  return (
    <section
      ref={sectionRef}
      id="integrations"
      className="py-20 sm:py-28 relative z-10 bg-white border-b border-zinc-200/70 overflow-hidden"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── Main Showcase Container ── */}
        <div className="integ-anim-item relative w-full rounded-[36px] overflow-hidden bg-gradient-to-b from-[#F9FBFD] via-[#F3F7FA] to-[#E9F1F6] border border-zinc-200/90 shadow-[0px_20px_50px_rgba(0,0,0,0.06)] pt-12 sm:pt-16 pb-0 flex flex-col items-center text-center">
          
          {/* Subtle Ambient Radial Sky Glow */}
          <div
            className="absolute inset-0 pointer-events-none opacity-60"
            style={{
              background:
                'radial-gradient(circle at 50% 25%, rgba(202, 230, 252, 0.7) 0%, transparent 65%)',
            }}
          />

          {/* ── Header ── */}
          <div className="relative z-10 max-w-[760px] mx-auto px-4 mb-8 sm:mb-10">
            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-800 mb-4 shadow-2xs">
              <span className="w-2 h-2 rounded-full bg-emerald-500" />
              <span>Integrations</span>
            </div>

            <h2 className="font-display text-3xl xs:text-4xl sm:text-[46px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
              Connect with the tools you already use
            </h2>

            <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed max-w-[620px] mx-auto">
              Seamlessly integrate with your client channels, financial rails, and frontier AI models to keep everything connected in real time.
            </p>

            {/* CTA Button following Cora Design System */}
            <div className="mt-6">
              <a
                href="https://app.heycora.in/workspace/login?source=integrations_section"
                onClick={() => trackEvent('cta_click', { section: 'integrations_section' })}
                className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm border border-zinc-800 group"
              >
                <span>Explore all integrations</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
              </a>
            </div>
          </div>

          {/* ── Radial Orbit Arc Display ── */}
          <div className="relative z-10 w-full max-w-[860px] h-[280px] sm:h-[340px] md:h-[380px] flex items-end justify-center mt-4">
            
            {/* Semicircular Dotted Guide Line */}
            <div 
              className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[340px] sm:w-[500px] md:w-[620px] h-[340px] sm:h-[500px] md:h-[620px] rounded-full border border-dashed border-zinc-300/80 pointer-events-none"
            />

            {/* 7 Floating Radial Nodes along the Upper Arc */}
            {RADIAL_NODES.map((node) => {
              // Convert angle to radians for x, y positioning
              const rad = (node.angle * Math.PI) / 180;
              // Responsive radius: ~140px mobile, ~200px tablet, ~260px desktop
              return (
                <div
                  key={node.id}
                  onMouseEnter={() => setHoveredNode(node.id)}
                  onMouseLeave={() => setHoveredNode(null)}
                  className={`radial-node-${node.id} absolute z-20 transition-transform duration-300 cursor-pointer`}
                  style={{
                    left: `calc(50% + ${Math.cos(rad) * 44}% - 26px)`,
                    bottom: `calc(32% + ${Math.sin(rad) * 42}%)`,
                  }}
                >
                  <div className="relative group flex flex-col items-center">
                    
                    {/* Node Circle Badge */}
                    <div className="w-11 h-11 sm:w-13 sm:h-13 md:w-14 md:h-14 rounded-full bg-white shadow-[0px_8px_24px_rgba(0,0,0,0.08)] border border-zinc-200/90 flex items-center justify-center hover:scale-115 hover:shadow-lg transition-all duration-200">
                      {node.icon}
                    </div>

                    {/* Tooltip on Hover */}
                    <div className="absolute -top-12 z-30 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none duration-200 whitespace-nowrap bg-zinc-950 text-white text-[11px] font-semibold px-3 py-1 rounded-lg shadow-md flex items-center gap-1.5">
                      <span className="w-1.5 h-1.5 rounded-full bg-emerald-400" />
                      <span>{node.name}</span>
                    </div>

                  </div>
                </div>
              );
            })}

            {/* Central Cora Command Hub Badge */}
            <div className="relative z-30 mb-8 flex flex-col items-center">
              <div className="w-18 h-18 sm:w-22 sm:h-22 md:w-24 md:h-24 rounded-full bg-zinc-950 text-white shadow-[0px_16px_40px_rgba(0,0,0,0.25)] flex items-center justify-center border-2 border-zinc-800 transition-transform hover:scale-105 duration-300">
                <span className="font-mono text-xl sm:text-2xl font-bold tracking-tighter text-white">
                  &lt; &gt;
                </span>
              </div>

              {/* Sub-Hub Label */}
              <h3 className="font-display font-bold text-zinc-950 text-sm sm:text-base mt-3.5 tracking-tight">
                100+ integrations available and growing
              </h3>
            </div>

          </div>

          {/* ── Bottom Meadow Landscape Horizon Base ── */}
          <div className="relative w-full h-24 sm:h-36 md:h-44 mt-auto overflow-hidden pointer-events-none select-none">
            <Image
              src="/images/cora_hero_landscape.jpg"
              alt="Meadow Landscape Base"
              fill
              sizes="100vw"
              className="object-cover object-[center_68%]"
            />
            {/* Top gradient fade into the card */}
            <div className="absolute inset-0 bg-gradient-to-t from-transparent via-[#E9F1F6]/40 to-[#E9F1F6]" />
          </div>

        </div>

      </div>
    </section>
  );
}
