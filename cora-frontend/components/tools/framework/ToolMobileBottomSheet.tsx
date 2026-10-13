'use client';

import React, { useEffect } from 'react';
import { X } from 'lucide-react';

export interface ToolMobileBottomSheetProps {
  isOpen: boolean;
  onClose: () => void;
  title: string;
  badge?: string;
  children: React.ReactNode;
}

export function ToolMobileBottomSheet({
  isOpen,
  onClose,
  title,
  badge,
  children,
}: ToolMobileBottomSheetProps) {
  // Prevent background scroll when sheet is open
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [isOpen]);

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 lg:hidden">
      {/* Dark Blurred Backdrop Overlay */}
      <div
        onClick={onClose}
        className="absolute inset-0 bg-zinc-950/50 backdrop-blur-md transition-opacity animate-in fade-in duration-200"
      />

      {/* Slide-Up Bottom Sheet */}
      <div className="absolute inset-x-0 bottom-0 max-h-[85vh] bg-white rounded-t-3xl border-t border-zinc-200/90 shadow-2xl flex flex-col animate-in slide-in-from-bottom duration-300 ease-out">
        {/* Drag Indicator Handle */}
        <div className="w-full flex justify-center pt-3 pb-1 cursor-grab active:cursor-grabbing">
          <div className="w-10 h-1 rounded-full bg-zinc-300" />
        </div>

        {/* Sheet Header */}
        <div className="px-5 py-3 border-b border-zinc-100 flex items-center justify-between gap-3">
          <div className="flex items-center gap-2">
            <h3 className="font-display text-base font-bold text-zinc-950 tracking-tight">
              {title}
            </h3>
            {badge && (
              <span className="text-[11px] font-mono px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-700 border border-zinc-200">
                {badge}
              </span>
            )}
          </div>

          <button
            type="button"
            onClick={onClose}
            aria-label="Close sheet"
            className="w-8 h-8 rounded-full bg-zinc-100 hover:bg-zinc-200 flex items-center justify-center text-zinc-600 transition-colors touch-manipulation"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Scrollable Sheet Content */}
        <div className="p-5 overflow-y-auto overscroll-contain space-y-4 pb-8">
          {children}
        </div>
      </div>
    </div>
  );
}
