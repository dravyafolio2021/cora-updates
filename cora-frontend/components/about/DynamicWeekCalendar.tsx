'use client';

import React, { useState, useEffect } from 'react';

interface DayData {
  dayName: string;
  shortName: string;
  dayNumber: number;
  isToday: boolean;
  isSavedDay: boolean;
}

export function DynamicWeekCalendar() {
  const [days, setDays] = useState<DayData[]>([]);
  const [selectedDayIndex, setSelectedDayIndex] = useState<number>(3); // Default: Thursday
  const [weekRangeStr, setWeekRangeStr] = useState<string>('');

  useEffect(() => {
    const now = new Date();
    const currentDayOfWeek = now.getDay();
    const distanceToMonday = currentDayOfWeek === 0 ? -6 : 1 - currentDayOfWeek;
    
    const monday = new Date(now);
    monday.setDate(now.getDate() + distanceToMonday);

    const weekDays: DayData[] = [];
    const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const fullDayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    for (let i = 0; i < 7; i++) {
      const d = new Date(monday);
      d.setDate(monday.getDate() + i);
      
      const isToday = 
        d.getDate() === now.getDate() &&
        d.getMonth() === now.getMonth() &&
        d.getFullYear() === now.getFullYear();

      weekDays.push({
        dayName: fullDayNames[i],
        shortName: dayNames[i],
        dayNumber: d.getDate(),
        isToday,
        isSavedDay: i === 3, // Thursday
      });
    }

    setDays(weekDays);

    const startMonth = monday.toLocaleString('en-US', { month: 'short' }).toUpperCase();
    const sunday = new Date(monday);
    sunday.setDate(monday.getDate() + 6);
    const endMonth = sunday.toLocaleString('en-US', { month: 'short' }).toUpperCase();
    const year = sunday.getFullYear();

    if (startMonth === endMonth) {
      setWeekRangeStr(`${startMonth} ${monday.getDate()} – ${sunday.getDate()}, ${year}`);
    } else {
      setWeekRangeStr(`${startMonth} ${monday.getDate()} – ${endMonth} ${sunday.getDate()}, ${year}`);
    }
  }, []);

  return (
    <div className="w-full text-center space-y-8 sm:space-y-10">
      
      {/* Eyebrow */}
      <div className="space-y-2">
        <span className="text-[11px] sm:text-xs font-mono font-semibold uppercase tracking-widest text-zinc-500 block">
          OUR MOTTO
        </span>
        <h3 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-6xl font-bold text-zinc-950 tracking-tight">
          Save one day, Every week.
        </h3>
        <p className="text-sm sm:text-base text-zinc-600 max-w-[540px] mx-auto font-normal">
          Cora automates administrative busywork so creative studios gain back an entire business day every single week.
        </p>
      </div>

      {/* 7-Day Clean Responsive Row (100% width on Mobile & Desktop, No Horizontal Cutoff) */}
      <div className="w-full max-w-[760px] mx-auto">
        <div className="grid grid-cols-7 gap-1.5 xs:gap-2 sm:gap-3 w-full">
          {days.length > 0 ? (
            days.map((day, idx) => {
              const isSaved = day.isSavedDay;
              const isSelected = selectedDayIndex === idx;

              return (
                <button
                  key={idx}
                  type="button"
                  onClick={() => setSelectedDayIndex(idx)}
                  className={`relative py-3 xs:py-4 sm:py-5 px-1 xs:px-2 rounded-xl sm:rounded-2xl flex flex-col items-center justify-center gap-1 sm:gap-1.5 transition-all text-center select-none ${
                    isSaved
                      ? 'bg-zinc-950 text-white shadow-sm'
                      : isSelected
                      ? 'bg-zinc-100 text-zinc-950 border border-zinc-300'
                      : 'bg-zinc-50 text-zinc-700 border border-zinc-200/80 hover:bg-zinc-100 hover:border-zinc-300'
                  }`}
                >
                  <span className={`text-[10px] sm:text-xs font-mono uppercase tracking-wider font-medium ${
                    isSaved ? 'text-zinc-400' : 'text-zinc-500'
                  }`}>
                    {day.shortName}
                  </span>

                  <span className={`font-display text-lg xs:text-xl sm:text-3xl font-bold ${
                    isSaved ? 'text-white' : 'text-zinc-950'
                  }`}>
                    {day.dayNumber}
                  </span>

                  <span className={`text-[9px] sm:text-[10px] font-mono tracking-tight font-medium ${
                    isSaved ? 'text-zinc-300' : 'text-zinc-400'
                  }`}>
                    {isSaved ? 'Saved' : 'Active'}
                  </span>
                </button>
              );
            })
          ) : (
            Array.from({ length: 7 }).map((_, i) => (
              <div
                key={i}
                className="bg-zinc-50 rounded-xl sm:rounded-2xl h-16 sm:h-24 border border-zinc-200/80 animate-pulse"
              />
            ))
          )}
        </div>
      </div>

      {/* Minimal Monochromatic Reassurance Metric Row (Zero Nested Boxes) */}
      <div className="pt-2 flex items-center justify-center flex-wrap gap-x-8 gap-y-3 text-xs sm:text-sm font-mono text-zinc-600">
        <div className="flex items-center gap-2">
          <span className="w-1.5 h-1.5 rounded-full bg-zinc-950" />
          <span>52 Days Saved / Year</span>
        </div>
        <div className="flex items-center gap-2">
          <span className="w-1.5 h-1.5 rounded-full bg-zinc-950" />
          <span>18% GST Autopilot</span>
        </div>
        <div className="flex items-center gap-2">
          <span className="w-1.5 h-1.5 rounded-full bg-zinc-950" />
          <span>SHA-256 E-Sign Vault</span>
        </div>
        <div className="flex items-center gap-2">
          <span className="w-1.5 h-1.5 rounded-full bg-zinc-950" />
          <span>Zero Admin Fatigue</span>
        </div>
      </div>

    </div>
  );
}
