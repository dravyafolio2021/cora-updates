'use client';

import React, { useState, useMemo } from 'react';
import { 
  Sparkles, 
  Copy, 
  Check, 
  RotateCcw, 
  FileSpreadsheet, 
  Code, 
  ArrowRight, 
  Layers, 
  Terminal, 
  Cpu, 
  BookOpen, 
  Info, 
  CheckCircle2,
  ExternalLink,
  Zap,
  ShieldCheck,
  Search,
  Sliders,
  HelpCircle,
  Table,
  ArrowDownRight,
  SplitSquareVertical,
  CheckSquare,
  Lock,
  Unlock
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';

interface PresetTemplate {
  name: string;
  category: string;
  lookupValue: string;
  tableRange: string;
  returnColIndex: number;
  exactMatch: boolean;
  fallbackValue: string;
  description: string;
}

const PRESETS: PresetTemplate[] = [
  {
    name: 'Product Catalog Pricing',
    category: 'Inventory',
    lookupValue: 'A2',
    tableRange: 'Products!$A$2:$F$500',
    returnColIndex: 4,
    exactMatch: true,
    fallbackValue: 'Not Found',
    description: 'Lookup Product SKU in column A and return unit retail price in column D (col 4)',
  },
  {
    name: 'Employee Department',
    category: 'HR & Roster',
    lookupValue: 'A2',
    tableRange: `'Staff Roster'!$A$2:$E$150`,
    returnColIndex: 3,
    exactMatch: true,
    fallbackValue: 'Unassigned',
    description: 'Lookup Employee Code and pull Department division from column C (col 3)',
  },
  {
    name: 'Client Invoice Status',
    category: 'Billing',
    lookupValue: 'B2',
    tableRange: `'Client Ledger'!$A$2:$G$300`,
    returnColIndex: 5,
    exactMatch: true,
    fallbackValue: 'Pending Verification',
    description: 'Lookup Invoice ID in Column A and return payment clearance status in column E',
  },
  {
    name: 'SAC / GST Tax Rate',
    category: 'Taxation',
    lookupValue: 'C2',
    tableRange: `'Tax Master'!$A$2:$D$40`,
    returnColIndex: 2,
    exactMatch: true,
    fallbackValue: '0%',
    description: 'Lookup 6-digit SAC Code and extract statutory GST rate percentage in column B',
  },
];

const FAQS = [
  {
    question: 'What happens if the return column index in VLOOKUP exceeds the table array width?',
    answer: 'If your col_index_num is greater than the total number of columns in your table_array, Excel or Google Sheets immediately throws a #REF! error. For example, if your range is A:D (4 columns wide) and you request column 5, VLOOKUP cannot resolve the index. Modern XLOOKUP avoids this completely by referencing direct column ranges.',
  },
  {
    question: 'Why does modern XLOOKUP not require a numeric column index number?',
    answer: 'Unlike VLOOKUP which relies on a rigid integer index (e.g. 4), XLOOKUP takes two explicit column vectors: the lookup_array (where to search) and the return_array (what to retrieve). This means if you insert, delete, or reorder intermediate columns later, your XLOOKUP formula never breaks or returns shifted data.',
  },
  {
    question: 'Should I always set the Range Lookup argument to FALSE (0) in VLOOKUP?',
    answer: 'In 99% of business, client, and financial use cases, yes. Setting range_lookup to FALSE enforces an exact character-by-character match. If set to TRUE (or omitted), VLOOKUP performs an approximate match which assumes the first column is sorted alphabetically/numerically, frequently causing silent, inaccurate match errors.',
  },
  {
    question: 'Can VLOOKUP search for values to the left of the lookup column?',
    answer: 'No. VLOOKUP can only look to the right because the search key must always reside in the first (leftmost) column of the table array. To perform leftward lookups, you must either upgrade to XLOOKUP or use the INDEX + MATCH combination.',
  },
  {
    question: 'Why does Cora automatically wrap the generated VLOOKUP in IFERROR?',
    answer: 'Standard VLOOKUP returns an unsightly, jarring #N/A error whenever a lookup value does not match. Wrapping it in IFERROR(...) catches the error cleanly and displays your custom fallback string (such as "Not Found" or "-") so reports and financial summaries remain professional and boardroom-ready.',
  },
];

// Helper to convert column letter/number
const colNumberToLetter = (n: number): string => {
  let s = '';
  while (n > 0) {
    const m = (n - 1) % 26;
    s = String.fromCharCode(65 + m) + s;
    n = Math.floor((n - m) / 26);
  }
  return s || 'A';
};

// Parse sheet name and column boundaries from range string
function parseRangeComponents(rangeStr: string, returnColIndex: number) {
  const trimmed = rangeStr.trim();
  let sheetPrefix = '';
  let rangePart = trimmed;

  if (trimmed.includes('!')) {
    const parts = trimmed.split('!');
    sheetPrefix = parts[0] + '!';
    rangePart = parts[1];
  }

  // Check if starts with column letter
  const match = rangePart.match(/^\$?([A-Z]+)(\$?\d+)?:\$?([A-Z]+)(\$?\d+)?$/i);
  let startCol = 'A';
  let startRow = '';
  let endRow = '';

  if (match) {
    startCol = match[1].toUpperCase();
    startRow = match[2] || '';
    endRow = match[4] || '';
  }

  // Calculate return column letter based on startCol index + returnColIndex - 1
  const startColCode = startCol.charCodeAt(0) - 64;
  const targetColCode = Math.max(1, startColCode + returnColIndex - 1);
  const returnCol = colNumberToLetter(targetColCode);

  const hasDollar = rangePart.includes('$');
  const lookupArrayCol = hasDollar 
    ? `${sheetPrefix}$${startCol}$${startRow.replace(/\$/g, '') || '2'}:$${startCol}$${endRow.replace(/\$/g, '') || '500'}`
    : `${sheetPrefix}${startCol}:${startCol}`;
  
  const returnArrayCol = hasDollar 
    ? `${sheetPrefix}$${returnCol}$${startRow.replace(/\$/g, '') || '2'}:$${returnCol}$${endRow.replace(/\$/g, '') || '500'}`
    : `${sheetPrefix}${returnCol}:${returnCol}`;

  return {
    sheetPrefix,
    startCol,
    returnCol,
    lookupArrayCol,
    returnArrayCol,
  };
}

export default function VlookupGeneratorPage() {
  const { showToast } = useToast();

  // Builder Input States
  const [lookupValue, setLookupValue] = useState<string>('A2');
  const [tableRange, setTableRange] = useState<string>('Products!$A$2:$F$500');
  const [returnColIndex, setReturnColIndex] = useState<number>(4);
  const [exactMatch, setExactMatch] = useState<boolean>(true);
  const [fallbackValue, setFallbackValue] = useState<string>('Not Found');
  const [wrapWithIfError, setWrapWithIfError] = useState<boolean>(true);
  const [lockReferences, setLockReferences] = useState<boolean>(true);

  // Copy state
  const [copiedVlookup, setCopiedVlookup] = useState<boolean>(false);
  const [copiedXlookup, setCopiedXlookup] = useState<boolean>(false);

  // Parse columns for dynamic display & XLOOKUP assembly
  const rangeInfo = useMemo(() => {
    return parseRangeComponents(tableRange, returnColIndex);
  }, [tableRange, returnColIndex]);

  // Generate Formulas
  const generatedFormulas = useMemo(() => {
    // 1. Classic VLOOKUP
    const matchFlag = exactMatch ? 'FALSE' : 'TRUE';
    const fallbackQuoted = fallbackValue ? `"${fallbackValue.replace(/"/g, '""')}"` : '""';
    const rawVlookup = `=VLOOKUP(${lookupValue}, ${tableRange}, ${returnColIndex}, ${matchFlag})`;
    const vlookupWithFallback = wrapWithIfError
      ? `=IFERROR(VLOOKUP(${lookupValue}, ${tableRange}, ${returnColIndex}, ${matchFlag}), ${fallbackQuoted})`
      : rawVlookup;

    // 2. Modern XLOOKUP
    const xlookupMatchMode = exactMatch ? '0' : '1';
    const xlookup = `=XLOOKUP(${lookupValue}, ${rangeInfo.lookupArrayCol}, ${rangeInfo.returnArrayCol}, ${fallbackQuoted}, ${xlookupMatchMode})`;

    return {
      vlookup: vlookupWithFallback,
      rawVlookup,
      xlookup,
    };
  }, [lookupValue, tableRange, returnColIndex, exactMatch, fallbackValue, wrapWithIfError, rangeInfo]);

  const handleCopy = (text: string, type: 'vlookup' | 'xlookup') => {
    if (typeof window !== 'undefined') {
      navigator.clipboard.writeText(text);
      if (type === 'vlookup') {
        setCopiedVlookup(true);
        setTimeout(() => setCopiedVlookup(false), 2000);
      } else {
        setCopiedXlookup(true);
        setTimeout(() => setCopiedXlookup(false), 2000);
      }
      showToast(`${type.toUpperCase()} formula copied to clipboard!`);
    }
  };

  const handleApplyPreset = (preset: PresetTemplate) => {
    setLookupValue(preset.lookupValue);
    setTableRange(preset.tableRange);
    setReturnColIndex(preset.returnColIndex);
    setExactMatch(preset.exactMatch);
    setFallbackValue(preset.fallbackValue);
    showToast(`Loaded ${preset.name} preset`);
  };

  const handleToggleLock = () => {
    if (lockReferences) {
      // Remove dollar signs
      setTableRange((prev) => prev.replace(/\$/g, ''));
      setLockReferences(false);
      showToast('Unlocked relative references');
    } else {
      // Add dollar signs to range
      const converted = tableRange.replace(/([A-Z]+)(\d+):([A-Z]+)(\d+)/, '$$$1$$$2:$$$3$$$4');
      setTableRange(converted);
      setLockReferences(true);
      showToast('Locked absolute references ($)');
    }
  };

  const handleReset = () => {
    setLookupValue('A2');
    setTableRange('Products!$A$2:$F$500');
    setReturnColIndex(4);
    setExactMatch(true);
    setFallbackValue('Not Found');
    setWrapWithIfError(true);
    setLockReferences(true);
    showToast('Reset to default configuration');
  };

  return (
    <ToolPageShell
      toolId="vlookup-generator"
      badgeTag="Visual Lookup Formula Builder"
      title="VLOOKUP & XLOOKUP Formula Generator Online Free"
      subtitle="Interactive visual builder for Microsoft Excel and Google Sheets. Configure lookup parameters, column offsets, and missing-value fallbacks with instant 1-click copy."
      faqItems={FAQS}
      relatedToolSlugs={['excel-formula-generator', 'clean-sheet-data', 'excel-to-pdf', 'pdf-to-excel']}
    >
      <div className="space-y-8">

        {/* Top Control & Visual Builder Card */}
        <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 pb-6 border-b border-zinc-100">
            <div>
              <div className="flex items-center gap-2 mb-2">
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <ShieldCheck className="w-3.5 h-3.5 text-zinc-700" />
                  100% In-Browser Memory
                </span>
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <SplitSquareVertical className="w-3.5 h-3.5 text-zinc-700" />
                  Dual VLOOKUP + XLOOKUP
                </span>
              </div>
              <h2 className="text-xl sm:text-2xl font-semibold tracking-tight text-zinc-900">
                Interactive Lookup Formula Architect
              </h2>
              <p className="text-sm text-zinc-600 mt-1 max-w-2xl">
                Configure your search value, table boundary, and return column index. Cora instantly generates both classic VLOOKUP and next-generation XLOOKUP.
              </p>
            </div>

            <div className="flex items-center gap-3">
              <button
                type="button"
                onClick={handleToggleLock}
                className="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
                title="Toggle absolute reference dollar signs"
              >
                {lockReferences ? (
                  <>
                    <Lock className="w-3.5 h-3.5 text-emerald-600" />
                    <span>Locked ($)</span>
                  </>
                ) : (
                  <>
                    <Unlock className="w-3.5 h-3.5 text-zinc-400" />
                    <span>Relative</span>
                  </>
                )}
              </button>

              <button
                type="button"
                onClick={handleReset}
                className="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-zinc-500 hover:text-zinc-800 transition-colors"
              >
                <RotateCcw className="w-3.5 h-3.5" />
                Reset
              </button>
            </div>
          </div>

          {/* Quick Preset Buttons */}
          <div className="mt-5 mb-6">
            <div className="text-xs font-medium text-zinc-500 mb-2.5 flex items-center gap-1.5">
              <Zap className="w-3.5 h-3.5 text-zinc-700" />
              <span>Load Ready-Made Blueprint Presets:</span>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
              {PRESETS.map((p) => (
                <button
                  key={p.name}
                  type="button"
                  onClick={() => handleApplyPreset(p)}
                  className="p-3 text-left rounded-xl border border-zinc-200 hover:border-zinc-900 bg-zinc-50/60 hover:bg-zinc-50 transition-all text-xs group"
                >
                  <div className="font-semibold text-zinc-900 group-hover:text-black">
                    {p.name}
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1 line-clamp-1">
                    {p.description}
                  </div>
                </button>
              ))}
            </div>
          </div>

          {/* Interactive Form Controls Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 pt-5 border-t border-zinc-100">
            
            {/* Input 1: Lookup Value */}
            <div>
              <label htmlFor="lookup-val" className="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                1. Lookup Value (search cell)
              </label>
              <div className="relative">
                <input
                  id="lookup-val"
                  type="text"
                  value={lookupValue}
                  onChange={(e) => setLookupValue(e.target.value)}
                  placeholder="e.g. A2 or C10"
                  className="w-full px-3.5 py-2.5 bg-zinc-50 border border-zinc-200 focus:border-zinc-900 focus:bg-white rounded-xl text-xs font-mono text-zinc-900 focus:outline-none transition-all"
                />
                <span className="absolute right-3 top-2.5 text-[11px] text-zinc-400">
                  Target Cell
                </span>
              </div>
              <p className="text-[11px] text-zinc-500 mt-1">
                The identifier cell you want to search for in your lookup table.
              </p>
            </div>

            {/* Input 2: Table Array / Range */}
            <div>
              <label htmlFor="table-range" className="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                2. Table Array / Range
              </label>
              <div className="relative">
                <input
                  id="table-range"
                  type="text"
                  value={tableRange}
                  onChange={(e) => setTableRange(e.target.value)}
                  placeholder="e.g. Sheet2!A:E or Products!$A$2:$F$500"
                  className="w-full px-3.5 py-2.5 bg-zinc-50 border border-zinc-200 focus:border-zinc-900 focus:bg-white rounded-xl text-xs font-mono text-zinc-900 focus:outline-none transition-all"
                />
                <span className="absolute right-3 top-2.5 text-[11px] text-zinc-400">
                  Source Data
                </span>
              </div>
              <p className="text-[11px] text-zinc-500 mt-1">
                Lookup column must be column 1 ({rangeInfo.startCol}) of this range.
              </p>
            </div>

            {/* Input 3: Return Column Index */}
            <div>
              <label htmlFor="return-col" className="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                3. Return Column Index (Col #{returnColIndex} → {rangeInfo.returnCol})
              </label>
              <div className="flex items-center gap-2">
                <input
                  id="return-col"
                  type="number"
                  min={1}
                  max={50}
                  value={returnColIndex}
                  onChange={(e) => setReturnColIndex(Math.max(1, parseInt(e.target.value) || 1))}
                  className="w-24 px-3.5 py-2.5 bg-zinc-50 border border-zinc-200 focus:border-zinc-900 focus:bg-white rounded-xl text-xs font-mono text-zinc-900 focus:outline-none transition-all text-center"
                />
                <div className="flex-1 px-3 py-2 bg-zinc-100 rounded-xl text-xs text-zinc-700 flex items-center justify-between">
                  <span>Column Letter:</span>
                  <span className="font-mono font-bold text-zinc-900">{rangeInfo.returnCol}</span>
                </div>
              </div>
              <p className="text-[11px] text-zinc-500 mt-1">
                Offset count from leftmost column to return value.
              </p>
            </div>

            {/* Input 4: Fallback Value */}
            <div>
              <label htmlFor="fallback-val" className="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                4. Missing Fallback (&quot;If Not Found&quot;)
              </label>
              <input
                id="fallback-val"
                type="text"
                value={fallbackValue}
                onChange={(e) => setFallbackValue(e.target.value)}
                placeholder="e.g. Not Found or -"
                className="w-full px-3.5 py-2.5 bg-zinc-50 border border-zinc-200 focus:border-zinc-900 focus:bg-white rounded-xl text-xs font-mono text-zinc-900 focus:outline-none transition-all"
              />
              <p className="text-[11px] text-zinc-500 mt-1">
                Replaces ugly #N/A errors with friendly text.
              </p>
            </div>

            {/* Input 5: Match Mode Toggle */}
            <div className="flex flex-col justify-between">
              <label className="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                5. Match Precision Mode
              </label>
              <button
                type="button"
                onClick={() => setExactMatch(!exactMatch)}
                className={`flex items-center justify-between px-3.5 py-2.5 rounded-xl border text-xs font-medium transition-all ${
                  exactMatch
                    ? 'bg-zinc-900 text-white border-zinc-900'
                    : 'bg-zinc-50 text-zinc-700 border-zinc-200'
                }`}
              >
                <span>{exactMatch ? 'Exact Match (FALSE / 0)' : 'Approximate Match (TRUE / 1)'}</span>
                <Check className={`w-4 h-4 ${exactMatch ? 'opacity-100 text-emerald-400' : 'opacity-0'}`} />
              </button>
              <p className="text-[11px] text-zinc-500 mt-1">
                Exact match prevents erroneous adjacent matches.
              </p>
            </div>

            {/* Input 6: IFERROR Protection */}
            <div className="flex flex-col justify-between">
              <label className="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                6. IFERROR Shield (VLOOKUP)
              </label>
              <button
                type="button"
                onClick={() => setWrapWithIfError(!wrapWithIfError)}
                className={`flex items-center justify-between px-3.5 py-2.5 rounded-xl border text-xs font-medium transition-all ${
                  wrapWithIfError
                    ? 'bg-zinc-900 text-white border-zinc-900'
                    : 'bg-zinc-50 text-zinc-700 border-zinc-200'
                }`}
              >
                <span>{wrapWithIfError ? 'Enabled: Wrap with IFERROR' : 'Disabled: Raw VLOOKUP'}</span>
                <Check className={`w-4 h-4 ${wrapWithIfError ? 'opacity-100 text-emerald-400' : 'opacity-0'}`} />
              </button>
              <p className="text-[11px] text-zinc-500 mt-1">
                Suppresses #N/A crashes in legacy spreadsheets.
              </p>
            </div>

          </div>

          {/* Visual Column Mapping Diagram */}
          <div className="mt-8 pt-6 border-t border-zinc-100">
            <h4 className="text-xs font-semibold text-zinc-900 uppercase tracking-wider mb-3 flex items-center gap-2">
              <Table className="w-4 h-4 text-zinc-600" />
              Visual Lookup Offset & Range Diagram
            </h4>

            <div className="bg-zinc-50 border border-zinc-200 rounded-xl p-4 overflow-x-auto">
              <div className="flex items-center gap-2 min-w-[540px]">
                
                {/* Column 1 (Search Key) */}
                <div className="flex-1 p-3 rounded-lg bg-zinc-900 text-white border border-zinc-900 shadow-sm text-center">
                  <div className="text-[10px] uppercase font-mono text-zinc-400">Column 1 (Lookup)</div>
                  <div className="text-sm font-bold mt-0.5">Col {rangeInfo.startCol}</div>
                  <div className="text-[11px] font-mono text-emerald-400 mt-1 truncate">
                    Find {lookupValue}
                  </div>
                </div>

                {/* Arrow indicator */}
                <div className="flex flex-col items-center justify-center px-2">
                  <div className="text-[10px] font-mono text-zinc-500 font-semibold mb-0.5">
                    Offset: +{returnColIndex - 1} cols
                  </div>
                  <div className="flex items-center text-zinc-400">
                    <div className="h-[2px] w-8 bg-zinc-300" />
                    <ArrowRight className="w-4 h-4 text-zinc-600 -ml-1" />
                  </div>
                </div>

                {/* Return Column */}
                <div className="flex-1 p-3 rounded-lg bg-emerald-50 text-emerald-950 border border-emerald-300 shadow-sm text-center">
                  <div className="text-[10px] uppercase font-mono text-emerald-700">Column {returnColIndex} (Return)</div>
                  <div className="text-sm font-bold mt-0.5">Col {rangeInfo.returnCol}</div>
                  <div className="text-[11px] font-mono text-emerald-800 mt-1 truncate">
                    Pull Result
                  </div>
                </div>

                {/* Fallback Box */}
                <div className="flex flex-col items-center justify-center px-2">
                  <div className="text-[10px] font-mono text-zinc-400 mb-0.5">If missing</div>
                  <ArrowRight className="w-4 h-4 text-zinc-300" />
                </div>

                <div className="flex-1 p-3 rounded-lg bg-zinc-100 text-zinc-800 border border-zinc-200 text-center">
                  <div className="text-[10px] uppercase font-mono text-zinc-500">Fallback Text</div>
                  <div className="text-xs font-mono font-semibold mt-1 truncate">
                    &quot;{fallbackValue}&quot;
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>

        {/* Dual Generated Output Cards (Side-by-Side or Stacked) */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">

          {/* Modern XLOOKUP Card */}
          <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm flex flex-col justify-between">
            <div>
              <div className="flex items-center justify-between pb-4 border-b border-zinc-100 mb-4">
                <div>
                  <div className="flex items-center gap-2">
                    <h3 className="text-base font-semibold text-zinc-900">
                      Modern XLOOKUP
                    </h3>
                    <span className="px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                      Recommended
                    </span>
                  </div>
                  <p className="text-xs text-zinc-500 mt-0.5">
                    Excel 365, Excel 2021+, and Google Sheets
                  </p>
                </div>

                <button
                  type="button"
                  onClick={() => handleCopy(generatedFormulas.xlookup, 'xlookup')}
                  className="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition-all shadow-sm active:scale-95"
                >
                  {copiedXlookup ? (
                    <>
                      <Check className="w-3.5 h-3.5 text-emerald-400" />
                      <span>Copied!</span>
                    </>
                  ) : (
                    <>
                      <Copy className="w-3.5 h-3.5" />
                      <span>Copy Formula</span>
                    </>
                  )}
                </button>
              </div>

              {/* Code display */}
              <div className="p-4 bg-zinc-950 text-emerald-400 rounded-xl font-mono text-xs sm:text-sm border border-zinc-800 overflow-x-auto selection:bg-zinc-800">
                <code>{generatedFormulas.xlookup}</code>
              </div>

              {/* XLOOKUP Benefits List */}
              <div className="mt-5 space-y-2 text-xs text-zinc-600">
                <div className="flex items-start gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" />
                  <span><strong>Zero Column Breaking:</strong> Inserting or deleting columns never alters return data.</span>
                </div>
                <div className="flex items-start gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" />
                  <span><strong>Native Fallback:</strong> Built-in 4th argument handles &quot;{fallbackValue}&quot; without nested IFERROR.</span>
                </div>
                <div className="flex items-start gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" />
                  <span><strong>Bidirectional:</strong> Capable of looking left or right across any two distinct columns.</span>
                </div>
              </div>
            </div>

            <div className="mt-5 pt-4 border-t border-zinc-100 text-[11px] text-zinc-400 font-mono">
              Syntax: =XLOOKUP(val, lookup_col, return_col, [if_not_found], [match_mode])
            </div>
          </div>

          {/* Classic VLOOKUP Card */}
          <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm flex flex-col justify-between">
            <div>
              <div className="flex items-center justify-between pb-4 border-b border-zinc-100 mb-4">
                <div>
                  <div className="flex items-center gap-2">
                    <h3 className="text-base font-semibold text-zinc-900">
                      Classic VLOOKUP
                    </h3>
                    <span className="px-2 py-0.5 rounded text-[11px] font-medium bg-zinc-100 text-zinc-700 border border-zinc-200">
                      Universal Legacy
                    </span>
                  </div>
                  <p className="text-xs text-zinc-500 mt-0.5">
                    Excel 2010, 2013, 2016, 2019 & all spreadsheet viewers
                  </p>
                </div>

                <button
                  type="button"
                  onClick={() => handleCopy(generatedFormulas.vlookup, 'vlookup')}
                  className="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-medium text-zinc-800 bg-zinc-100 hover:bg-zinc-200 rounded-xl transition-all active:scale-95"
                >
                  {copiedVlookup ? (
                    <>
                      <Check className="w-3.5 h-3.5 text-emerald-600" />
                      <span>Copied!</span>
                    </>
                  ) : (
                    <>
                      <Copy className="w-3.5 h-3.5" />
                      <span>Copy Formula</span>
                    </>
                  )}
                </button>
              </div>

              {/* Code display */}
              <div className="p-4 bg-zinc-900 text-zinc-200 rounded-xl font-mono text-xs sm:text-sm border border-zinc-800 overflow-x-auto selection:bg-zinc-800">
                <code>{generatedFormulas.vlookup}</code>
              </div>

              {/* VLOOKUP Considerations List */}
              <div className="mt-5 space-y-2 text-xs text-zinc-600">
                <div className="flex items-start gap-2">
                  <Info className="w-4 h-4 text-zinc-500 flex-shrink-0 mt-0.5" />
                  <span><strong>Rightward Only:</strong> The search key must exist in column {rangeInfo.startCol}. Cannot search to the left.</span>
                </div>
                <div className="flex items-start gap-2">
                  <Info className="w-4 h-4 text-zinc-500 flex-shrink-0 mt-0.5" />
                  <span><strong>Static Column Count:</strong> If anyone inserts a column in the middle of {tableRange}, index {returnColIndex} shifts.</span>
                </div>
                <div className="flex items-start gap-2">
                  <Info className="w-4 h-4 text-zinc-500 flex-shrink-0 mt-0.5" />
                  <span><strong>Protected with IFERROR:</strong> Wrapped to prevent unsightly #N/A formula breaks.</span>
                </div>
              </div>
            </div>

            <div className="mt-5 pt-4 border-t border-zinc-100 text-[11px] text-zinc-400 font-mono">
              Syntax: =IFERROR(VLOOKUP(val, table_range, col_index, FALSE), fallback)
            </div>
          </div>

        </div>

        {/* Comparison Matrix Card */}
        <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
          <h4 className="text-sm font-semibold text-zinc-900 uppercase tracking-wider mb-4 flex items-center gap-2">
            <BookOpen className="w-4 h-4 text-zinc-700" />
            VLOOKUP vs XLOOKUP: Engineering Comparison
          </h4>

          <div className="border border-zinc-200 rounded-xl overflow-hidden">
            <table className="w-full text-left text-xs border-collapse">
              <thead>
                <tr className="bg-zinc-50 border-b border-zinc-200 text-zinc-600 font-medium">
                  <th className="px-4 py-3">Feature Capability</th>
                  <th className="px-4 py-3 text-emerald-800 bg-emerald-50/50">Modern XLOOKUP</th>
                  <th className="px-4 py-3">Classic VLOOKUP</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-100">
                <tr className="hover:bg-zinc-50/50 transition-colors">
                  <td className="px-4 py-3 font-medium text-zinc-900">Lookup Direction</td>
                  <td className="px-4 py-3 text-emerald-700 font-medium bg-emerald-50/20">Any direction (Left, Right, Up, Down)</td>
                  <td className="px-4 py-3 text-zinc-500">Rightward only (Key must be column 1)</td>
                </tr>
                <tr className="hover:bg-zinc-50/50 transition-colors">
                  <td className="px-4 py-3 font-medium text-zinc-900">Column Insert Resilience</td>
                  <td className="px-4 py-3 text-emerald-700 font-medium bg-emerald-50/20">100% resilient (Range references auto-update)</td>
                  <td className="px-4 py-3 text-zinc-500">Fragile (Static integer index breaks)</td>
                </tr>
                <tr className="hover:bg-zinc-50/50 transition-colors">
                  <td className="px-4 py-3 font-medium text-zinc-900">Default Match Behavior</td>
                  <td className="px-4 py-3 text-emerald-700 font-medium bg-emerald-50/20">Defaults to Exact Match</td>
                  <td className="px-4 py-3 text-zinc-500">Defaults to Approximate (Requires FALSE flag)</td>
                </tr>
                <tr className="hover:bg-zinc-50/50 transition-colors">
                  <td className="px-4 py-3 font-medium text-zinc-900">Error Handling</td>
                  <td className="px-4 py-3 text-emerald-700 font-medium bg-emerald-50/20">Native built-in [if_not_found] argument</td>
                  <td className="px-4 py-3 text-zinc-500">Requires outer IFERROR(...) wrapper</td>
                </tr>
                <tr className="hover:bg-zinc-50/50 transition-colors">
                  <td className="px-4 py-3 font-medium text-zinc-900">Software Compatibility</td>
                  <td className="px-4 py-3 text-emerald-700 font-medium bg-emerald-50/20">Excel 365, Excel 2021+, Google Sheets</td>
                  <td className="px-4 py-3 text-zinc-500">Universal (All versions back to Excel 97)</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </ToolPageShell>
  );
}
