'use client';

import React from 'react';
import { Minus, Plus } from 'lucide-react';

export interface ToolNumberInputProps {
  id?: string;
  label: string;
  helperText?: string;
  helpText?: string;
  value: number;
  onChange: (val: number) => void;
  min?: number;
  max?: number;
  step?: number;
  prefix?: string;
  suffix?: string;
  showSlider?: boolean;
}

export function ToolNumberInput({
  id,
  label,
  helperText,
  helpText,
  value,
  onChange,
  min = 0,
  max = 100000,
  step = 1,
  prefix,
  suffix,
  showSlider = true,
}: ToolNumberInputProps) {
  const subtitle = helpText || helperText;

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const raw = e.target.value.replace(/[^0-9.-]/g, '');
    const num = parseFloat(raw);
    if (!isNaN(num)) {
      onChange(Math.min(max, Math.max(min, num)));
    } else if (raw === '') {
      onChange(min);
    }
  };

  const handleSliderChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    onChange(parseFloat(e.target.value));
  };

  const handleDecrement = () => {
    const next = Math.max(min, Math.round((value - step) * 100) / 100);
    onChange(next);
  };

  const handleIncrement = () => {
    const next = Math.min(max, Math.round((value + step) * 100) / 100);
    onChange(next);
  };

  return (
    <div className="w-full space-y-2">
      {/* Label & Contextual Subtitle */}
      <div className="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-0.5 sm:gap-2">
        <label htmlFor={id} className="text-xs sm:text-[13px] font-bold text-zinc-900 leading-tight">
          {label}
        </label>
        {subtitle && (
          <span className="text-[11px] text-zinc-500 font-normal leading-tight">
            {subtitle}
          </span>
        )}
      </div>

      {/* Main Input Control with Mobile Steppers */}
      <div className="flex items-center gap-1.5">
        {/* Decrement Button */}
        <button
          type="button"
          onClick={handleDecrement}
          disabled={value <= min}
          aria-label={`Decrease ${label}`}
          className="min-h-[44px] min-w-[44px] flex items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 disabled:opacity-40 disabled:cursor-not-allowed text-zinc-700 active:scale-95 transition-all touch-manipulation shrink-0"
        >
          <Minus className="w-3.5 h-3.5" />
        </button>

        {/* Input Field with Prefix/Suffix */}
        <div className="relative flex-1 flex items-center">
          {prefix && (
            <span className="absolute left-3 text-zinc-500 font-mono text-base sm:text-sm font-semibold pointer-events-none select-none">
              {prefix}
            </span>
          )}
          <input
            id={id}
            type="text"
            inputMode="decimal"
            value={isNaN(value) ? '' : value.toLocaleString()}
            onChange={handleInputChange}
            className={`w-full min-h-[44px] bg-white border border-zinc-200/90 rounded-xl py-2.5 text-base sm:text-sm font-mono font-bold text-zinc-900 shadow-2xs focus:outline-none focus:border-zinc-900 transition-colors touch-manipulation ${
              prefix ? 'pl-8' : 'pl-3.5'
            } ${suffix ? 'pr-14' : 'pr-3.5'}`}
          />
          {suffix && (
            <span className="absolute right-3 text-zinc-500 font-mono text-xs font-medium pointer-events-none select-none">
              {suffix}
            </span>
          )}
        </div>

        {/* Increment Button */}
        <button
          type="button"
          onClick={handleIncrement}
          disabled={value >= max}
          aria-label={`Increase ${label}`}
          className="min-h-[44px] min-w-[44px] flex items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 disabled:opacity-40 disabled:cursor-not-allowed text-zinc-700 active:scale-95 transition-all touch-manipulation shrink-0"
        >
          <Plus className="w-3.5 h-3.5" />
        </button>
      </div>

      {/* Range Slider for Quick Visual Adjustments */}
      {showSlider && (
        <div className="pt-1 px-1">
          <input
            type="range"
            min={min}
            max={max}
            step={step}
            value={value}
            onChange={handleSliderChange}
            className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-900 touch-pan-x"
          />
        </div>
      )}
    </div>
  );
}
