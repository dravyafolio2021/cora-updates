'use client';

import React, { useEffect, useRef, useState } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { ArrowRight, MessageSquare, CreditCard, Sheet, Mail, Receipt, Database } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

const RADIAL_NODES = [
  {
    id: 'whatsapp',
    name: 'WhatsApp Business',
    category: 'Lead Capture & Alerts',
    angle: 165,
    icon: <MessageSquare className="w-5 h-5 text-[#25D366]" />,
  },
  {
    id: 'razorpay',
    name: 'Razorpay & UPI',
    category: 'Instant Payments',
    angle: 135,
    icon: <CreditCard className="w-5 h-5 text-[#0C2340]" />,
  },
  {
    id: 'sheets',
    name: 'Google Sheets',
    category: 'Auto Sync',
    angle: 105,
    icon: <Sheet className="w-5 h-5 text-[#0F9D58]" />,
  },
  {
    id: 'gmail',
    name: 'Gmail & Email',
    category: 'PDF Invoices',
    angle: 75,
    icon: <Mail className="w-5 h-5 text-[#EA4335]" />,
  },
  {
    id: 'tally',
    name: 'Tally & Excel',
    category: 'Tax & Accounts',
    angle: 45,
    icon: <Receipt className="w-5 h-5 text-[#2563EB]" />,
  },
  {
    id: 'gdrive',
    name: 'Google Drive',
    category: 'Document Vault',
    angle: 15,
    icon: <Database className="w-5 h-5 text-[#F59E0B]" />,
  },
];

export function IntegrationsRadialSection() {
  const sectionRef = useRef<HTMLElement>(null);
  const [hoveredNode, setHoveredNode] = useState<string | null>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
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
        
        <div className="integ-anim-item relative w-full rounded-[36px] overflow-hidden bg-gradient-to-b from-[#F9FBFD] via-[#F3F7FA] to-[#E9F1F6] border border-zinc-200/90 shadow-2xs pt-12 sm:pt-16 pb-0 flex flex-col items-center text-center">
          
          <div
            className="absolute inset-0 pointer-events-none opacity-60"
            style={{
              background:
                'radial-gradient(circle at 50% 25%, rgba(202, 230, 252, 0.7) 0%, transparent 65%)',
            }}
          />

          {/* Header */}
          <div className="relative z-10 max-w-[760px] mx-auto px-4 mb-8 sm:mb-10">
            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-800 mb-4 shadow-2xs">
              <span className="w-2 h-2 rounded-full bg-emerald-500" />
              <span>INTEGRATIONS</span>
            </div>

            <h2 className="font-display text-3xl xs:text-4xl sm:text-[46px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
              Connects with your daily Indian workflow.
            </h2>

            <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed max-w-[620px] mx-auto">
              Keep using your favorite tools. Cora syncs with WhatsApp, Razorpay payment gateways, Google Drive, and your bank exports without custom code.
            </p>

            <div className="mt-6">
              <a
                href="https://app.heycora.in/workspace/login?source=integrations_section"
                onClick={() => trackEvent('cta_click', { section: 'integrations_section' })}
                className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
              >
                <span>Connect your tools for free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>
            </div>
          </div>

          {/* Radial Orbit Arc Display */}
          <div className="relative z-10 w-full max-w-[860px] h-[280px] sm:h-[340px] md:h-[380px] flex items-end justify-center mt-4">
            
            <div 
              className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[340px] sm:w-[500px] md:w-[620px] h-[340px] sm:h-[500px] md:h-[620px] rounded-full border border-dashed border-zinc-300/80 pointer-events-none"
            />

            {RADIAL_NODES.map((node) => {
              const rad = (node.angle * Math.PI) / 180;
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
                    <div className="w-11 h-11 sm:w-13 sm:h-13 md:w-14 md:h-14 rounded-full bg-white shadow-2xs border border-zinc-200/90 flex items-center justify-center hover:scale-110 transition-all duration-200">
                      {node.icon}
                    </div>

                    <div className="absolute -top-12 z-30 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none duration-200 whitespace-nowrap bg-zinc-950 text-white text-[11px] font-semibold px-3 py-1 rounded-lg shadow-md flex items-center gap-1.5">
                      <span className="w-1.5 h-1.5 rounded-full bg-emerald-400" />
                      <span>{node.name}</span>
                    </div>
                  </div>
                </div>
              );
            })}

            {/* Central Cora Hub Badge */}
            <div className="relative z-30 mb-8 flex flex-col items-center">
              <div className="w-18 h-18 sm:w-22 sm:h-22 md:w-24 md:h-24 rounded-full bg-zinc-950 text-white shadow-md flex items-center justify-center border-2 border-zinc-800 transition-transform hover:scale-105 duration-300">
                <span className="font-mono text-xl sm:text-2xl font-bold tracking-tighter text-white">
                  CORA
                </span>
              </div>

              <h3 className="font-display font-bold text-zinc-950 text-sm sm:text-base mt-3.5 tracking-tight">
                Seamless sync with your Indian tool stack
              </h3>
            </div>

          </div>

          <div className="relative w-full h-24 sm:h-36 md:h-44 mt-auto overflow-hidden pointer-events-none select-none">
            <Image
              src="/images/cora_hero_landscape.jpg"
              alt="Meadow Landscape Base"
              fill
              sizes="100vw"
              className="object-cover object-[center_68%]"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-transparent via-[#E9F1F6]/40 to-[#E9F1F6]" />
          </div>

        </div>

      </div>
    </section>
  );
}
