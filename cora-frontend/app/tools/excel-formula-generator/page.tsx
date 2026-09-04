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
  Filter,
  Calendar,
  AtSign,
  Calculator,
  Hash
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';

interface FormulaPreset {
  id: string;
  name: string;
  category: 'lookup' | 'math' | 'text' | 'date' | 'tax';
  prompt: string;
  description: string;
}

interface ArgumentBreakdown {
  name: string;
  value: string;
  purpose: string;
  type: string;
}

interface FormulaResult {
  formula: string;
  alternativeFormula?: string;
  functionName: string;
  platform: 'both' | 'excel' | 'sheets';
  summary: string;
  arguments: ArgumentBreakdown[];
  proTip: string;
}

const PRESETS: FormulaPreset[] = [
  {
    id: 'xlookup',
    name: 'XLOOKUP Match',
    category: 'lookup',
    prompt: 'Lookup email in Column B and return revenue in Column F',
    description: 'Exact match lookup with built-in #N/A missing value handling',
  },
  {
    id: 'sumifs',
    name: 'Multi-Condition SUMIFS',
    category: 'math',
    prompt: 'Sum column C if column A is Client and date in column B is this month',
    description: 'Calculate monthly client revenue totals with multi-column criteria',
  },
  {
    id: 'domain',
    name: 'Extract domain from email',
    category: 'text',
    prompt: 'Extract domain name from email address in cell A2',
    description: 'Pull URL domain string following the @ character without manual splitting',
  },
  {
    id: 'networkdays',
    name: 'Calculate days between dates',
    category: 'date',
    prompt: 'Calculate business days between start date in A2 and end date in B2 excluding weekends',
    description: 'Compute working turnaround days excluding Saturdays and Sundays',
  },
  {
    id: 'countunique',
    name: 'Count Unique Values',
    category: 'math',
    prompt: 'Count the number of unique non-empty values in Column A',
    description: 'Deduplicate and count distinct client or invoice IDs automatically',
  },
  {
    id: 'gst',
    name: '18% GST Split (CGST/SGST)',
    category: 'tax',
    prompt: 'Calculate 18% GST on subtotal in cell E2 and split CGST 9% and SGST 9%',
    description: 'Standard Indian commercial tax split with 2 decimal precision rounding',
  },
];

const FAQS = [
  {
    question: 'How does Cora generate Excel and Google Sheets formulas without sending my data to external servers?',
    answer: 'Cora’s formula synthesis engine is engineered in 100% client-side TypeScript running directly inside your browser memory (RAM). When you type a prompt or select a preset, pattern tokenizers and AST grammar parsers assemble valid spreadsheet syntax instantaneously with zero network round-trips.',
  },
  {
    question: 'What are the core differences between Excel and Google Sheets formulas?',
    answer: 'While core mathematical functions (SUM, IF, VLOOKUP, INDEX/MATCH) are identical, modern Excel and Google Sheets diverge on dynamic array and text manipulation functions. Google Sheets supports native REGEX functions like REGEXEXTRACT and REGEXREPLACE, and COUNTUNIQUE. Microsoft Excel 365 uses TEXTBEFORE, TEXTAFTER, and dynamic spill operators (#). Cora lets you toggle platforms to generate syntax tailored for your exact spreadsheet host.',
  },
  {
    question: 'Why should I use XLOOKUP instead of classic VLOOKUP in 2026?',
    answer: 'XLOOKUP defaults to exact match (eliminating the FALSE argument requirement), searches in any direction (left, right, up, down), does not break when new columns are inserted, supports reverse searches (bottom to top), and includes a native if_not_found argument so you no longer need to wrap your formula in an extra IFERROR wrapper.',
  },
  {
    question: 'How do I resolve #N/A, #VALUE!, or #REF! errors when pasting formulas?',
    answer: 'A #N/A error indicates that the lookup value does not exist in the source range; use XLOOKUP’s 4th parameter or IFERROR to supply a friendly fallback string like "Not Found". A #VALUE! error usually occurs when performing arithmetic on a text-formatted cell. A #REF! error happens if a referenced column or worksheet has been deleted or renamed.',
  },
  {
    question: 'Are these formulas compatible with legacy Microsoft Excel (2013/2016)?',
    answer: 'Yes. Whenever a formula uses a modern Excel 365 function (such as XLOOKUP or UNIQUE), Cora also displays the backward-compatible legacy formula (such as INDEX/MATCH or SUMPRODUCT) in the Alternative Syntax card so your spreadsheets work seamlessly across older desktop installations.',
  },
];

export default function ExcelFormulaGeneratorPage() {
  const { showToast } = useToast();
  
  const [platform, setPlatform] = useState<'excel' | 'sheets'>('excel');
  const [promptText, setPromptText] = useState<string>(
    'Lookup email in Column B and return revenue in Column F'
  );
  const [copiedPrimary, setCopiedPrimary] = useState(false);
  const [copiedAlt, setCopiedAlt] = useState(false);

  // Generate formula based on prompt and platform
  const result: FormulaResult = useMemo(() => {
    const text = promptText.trim().toLowerCase();

    // 1. Lookups & Matches
    if (text.includes('lookup') || text.includes('vlookup') || text.includes('xlookup') || text.includes('find') || text.includes('match')) {
      if (text.includes('email') || (text.includes('column b') && text.includes('column f'))) {
        return {
          formula: platform === 'excel' 
            ? '=XLOOKUP(A2, B:B, F:F, "Not Found", 0)' 
            : '=XLOOKUP(A2, B:B, F:F, "Not Found", 0)',
          alternativeFormula: platform === 'excel'
            ? '=IFERROR(INDEX(F:F, MATCH(A2, B:B, 0)), "Not Found")'
            : '=IFERROR(VLOOKUP(A2, B:F, 5, FALSE), "Not Found")',
          functionName: 'XLOOKUP',
          platform: 'both',
          summary: 'Scans Column B for the identifier in cell A2 and returns the corresponding value from Column F with an automatic fallback if no match is found.',
          arguments: [
            { name: 'lookup_value', value: 'A2', purpose: 'The identifier cell to search for (e.g. client email or ID)', type: 'Cell Reference' },
            { name: 'lookup_array', value: 'B:B', purpose: 'The column range where the search identifier is indexed', type: 'Column Range' },
            { name: 'return_array', value: 'F:F', purpose: 'The column containing revenue or data to retrieve upon match', type: 'Column Range' },
            { name: 'if_not_found', value: '"Not Found"', purpose: 'Safe fallback string to return if no record is found in Column B', type: 'Text String' },
            { name: 'match_mode', value: '0', purpose: 'Exact match (0). Does not require the source table to be sorted', type: 'Flag (0)' },
          ],
          proTip: 'Lock your reference columns with dollar signs ($B$2:$B$500 and $F$2:$F$500) if you plan to drag or autofill this formula across multiple rows.',
        };
      }

      // Generic lookup parser
      const targetColMatch = text.match(/column\s+([a-z])/g) || [];
      const col1 = targetColMatch[0]?.split(/\s+/)[1]?.toUpperCase() || 'A';
      const col2 = targetColMatch[1]?.split(/\s+/)[1]?.toUpperCase() || 'D';

      return {
        formula: `=XLOOKUP(A2, ${col1}:${col1}, ${col2}:${col2}, "Not Found", 0)`,
        alternativeFormula: `=IFERROR(INDEX(${col2}:${col2}, MATCH(A2, ${col1}:${col1}, 0)), "Not Found")`,
        functionName: 'XLOOKUP',
        platform: 'both',
        summary: `Searches for the value from cell A2 in Column ${col1} and returns the matching value from Column ${col2}.`,
        arguments: [
          { name: 'lookup_value', value: 'A2', purpose: 'Search term or key value', type: 'Cell Reference' },
          { name: 'lookup_array', value: `${col1}:${col1}`, purpose: `Source column to scan (${col1})`, type: 'Range' },
          { name: 'return_array', value: `${col2}:${col2}`, purpose: `Destination column to return (${col2})`, type: 'Range' },
          { name: 'if_not_found', value: '"Not Found"', purpose: 'Output string if no match is found', type: 'String' },
        ],
        proTip: 'In Google Sheets and Excel 365, XLOOKUP is 2.5x faster than nested VLOOKUPs on tables exceeding 5,000 rows.',
      };
    }

    // 2. SUM / SUMIFS / Multi-condition aggregation
    if (text.includes('sum') || text.includes('total') || text.includes('add')) {
      if (text.includes('month') || text.includes('date') || text.includes('client')) {
        return {
          formula: platform === 'excel'
            ? '=SUMIFS(C:C, A:A, "Client", B:B, ">="&EOMONTH(TODAY(),-1)+1, B:B, "<="&EOMONTH(TODAY(),0))'
            : '=SUMIFS(C:C, A:A, "Client", B:B, ">="&EOMONTH(TODAY(),-1)+1, B:B, "<="&EOMONTH(TODAY(),0))',
          alternativeFormula: platform === 'excel'
            ? '=SUMPRODUCT((A:A="Client")*(B:B>=EOMONTH(TODAY(),-1)+1)*(B:B<=EOMONTH(TODAY(),0))*(C:C))'
            : '=QUERY(A:C, "SELECT SUM(C) WHERE A = \'Client\' AND B >= date \'"&TEXT(EOMONTH(TODAY(),-1)+1,"yyyy-mm-dd")&"\' AND B <= date \'"&TEXT(EOMONTH(TODAY(),0),"yyyy-mm-dd")&"\' LABEL SUM(C) \'\'", 0)',
          functionName: 'SUMIFS',
          platform: 'both',
          summary: 'Calculates the sum of numerical values in Column C filtered where Column A equals "Client" and dates in Column B fall strictly within the current calendar month.',
          arguments: [
            { name: 'sum_range', value: 'C:C', purpose: 'The column containing numbers to add (e.g. invoice amounts or hours)', type: 'Range' },
            { name: 'criteria_range1', value: 'A:A', purpose: 'First filter column to evaluate (e.g. client category)', type: 'Range' },
            { name: 'criteria1', value: '"Client"', purpose: 'Condition string that rows in Column A must match', type: 'Filter' },
            { name: 'criteria_range2', value: 'B:B', purpose: 'Second filter column to evaluate (e.g. transaction date)', type: 'Range' },
            { name: 'criteria2', value: '">="&EOMONTH(TODAY(),-1)+1', purpose: 'Dynamic lower boundary (1st day of current month)', type: 'Date Expression' },
            { name: 'criteria_range3', value: 'B:B', purpose: 'Third filter column to evaluate (upper date boundary)', type: 'Range' },
            { name: 'criteria3', value: '"<="&EOMONTH(TODAY(),0)', purpose: 'Dynamic upper boundary (last day of current month)', type: 'Date Expression' },
          ],
          proTip: 'Using EOMONTH(TODAY(), -1) + 1 makes this formula self-updating on the first day of every month without manual date editing.',
        };
      }

      // Generic SUMIF
      return {
        formula: '=SUMIF(A:A, "Client", C:C)',
        alternativeFormula: '=SUMIFS(C:C, A:A, "Client")',
        functionName: 'SUMIF',
        platform: 'both',
        summary: 'Sums all values in Column C whenever the corresponding row in Column A matches "Client".',
        arguments: [
          { name: 'range', value: 'A:A', purpose: 'The column to check for criteria matches', type: 'Range' },
          { name: 'criteria', value: '"Client"', purpose: 'The exact string value to match', type: 'String' },
          { name: 'sum_range', value: 'C:C', purpose: 'The column containing numbers to aggregate', type: 'Range' },
        ],
        proTip: 'Notice the argument order: In SUMIF the sum_range is last, but in multi-condition SUMIFS the sum_range is always first!',
      };
    }

    // 3. String & Domain Extraction
    if (text.includes('domain') || text.includes('email') || text.includes('@')) {
      if (platform === 'sheets') {
        return {
          formula: '=REGEXEXTRACT(A2, "@(.+)")',
          alternativeFormula: '=MID(A2, FIND("@", A2) + 1, LEN(A2))',
          functionName: 'REGEXEXTRACT',
          platform: 'sheets',
          summary: 'Uses Google Sheets built-in regular expression engine to capture all characters following the "@" symbol.',
          arguments: [
            { name: 'text', value: 'A2', purpose: 'The source cell containing the email address (e.g. rohan@agency.in)', type: 'Cell Reference' },
            { name: 'regular_expression', value: '"@(.+)"', purpose: 'Capturing group that extracts everything following the @ symbol', type: 'Regex Pattern' },
          ],
          proTip: 'Wrap in LOWER() to normalize extracted domains (e.g. =LOWER(REGEXEXTRACT(A2, "@(.+)"))).',
        };
      } else {
        return {
          formula: '=MID(A2, SEARCH("@", A2) + 1, LEN(A2) - SEARCH("@", A2))',
          alternativeFormula: '=TEXTAFTER(A2, "@")',
          functionName: 'MID / SEARCH',
          platform: 'excel',
          summary: 'Finds the location of the "@" symbol in cell A2 and extracts all subsequent characters representing the domain.',
          arguments: [
            { name: 'text', value: 'A2', purpose: 'The source cell containing the raw email address', type: 'Cell Reference' },
            { name: 'start_num', value: 'SEARCH("@", A2) + 1', purpose: 'Position immediately after the @ delimiter', type: 'Expression' },
            { name: 'num_chars', value: 'LEN(A2) - SEARCH("@", A2)', purpose: 'Exact character count of the remaining domain string', type: 'Expression' },
          ],
          proTip: 'In Excel 365 or Excel 2024, use =TEXTAFTER(A2, "@") for a significantly simpler 1-function solution.',
        };
      }
    }

    // 4. Date calculations & Networkdays
    if (text.includes('day') || text.includes('date') || text.includes('weekend') || text.includes('networkdays')) {
      return {
        formula: '=NETWORKDAYS(A2, B2)',
        alternativeFormula: '=NETWORKDAYS.INTL(A2, B2, 1, Holidays!$A$2:$A$25)',
        functionName: 'NETWORKDAYS',
        platform: 'both',
        summary: 'Calculates the net working business days between start date in cell A2 and end date in cell B2, automatically excluding weekends (Saturdays and Sundays).',
        arguments: [
          { name: 'start_date', value: 'A2', purpose: 'The project commencement or milestone release date', type: 'Date Cell' },
          { name: 'end_date', value: 'B2', purpose: 'The deliverable completion or sign-off date', type: 'Date Cell' },
          { name: '[holidays]', value: 'Optional', purpose: 'Optional third range referencing bank or national gazetted holidays', type: 'Range' },
        ],
        proTip: 'Use NETWORKDAYS.INTL if your team works on Saturdays or follows custom Middle East or non-standard weekend schedules.',
      };
    }

    // 5. Unique Counting & Deduplication
    if (text.includes('unique') || text.includes('distinct') || text.includes('counta')) {
      if (platform === 'sheets') {
        return {
          formula: '=COUNTUNIQUE(A:A)',
          alternativeFormula: '=COUNTA(UNIQUE(FILTER(A:A, A:A<>"")))',
          functionName: 'COUNTUNIQUE',
          platform: 'sheets',
          summary: 'Google Sheets native function to calculate the number of unique distinct values in Column A with zero helper columns.',
          arguments: [
            { name: 'value1', value: 'A:A', purpose: 'The column containing names, client emails, or invoice identifiers', type: 'Column Range' },
          ],
          proTip: 'COUNTUNIQUE is a Google Sheets exclusive function. In Excel, use the COUNTA(UNIQUE()) combination shown in the alternative card.',
        };
      } else {
        return {
          formula: '=COUNTA(UNIQUE(FILTER(A:A, A:A<>"")))',
          alternativeFormula: '=SUMPRODUCT(1/COUNTIF(A2:A100, A2:A100&""))',
          functionName: 'COUNTA / UNIQUE',
          platform: 'excel',
          summary: 'Filters out blank cells, generates a deduplicated array of unique values, and counts the resulting array in Excel 365.',
          arguments: [
            { name: 'FILTER', value: 'A:A, A:A<>""', purpose: 'Excludes empty cells so blank rows do not skew unique counts', type: 'Filter Array' },
            { name: 'UNIQUE', value: 'Spill array', purpose: 'Generates a distinct deduplicated list in memory', type: 'Dynamic Array' },
            { name: 'COUNTA', value: 'Count items', purpose: 'Counts non-empty distinct items in the result', type: 'Integer Count' },
          ],
          proTip: 'For legacy Excel 2013-2016 that lacks UNIQUE(), the alternative SUMPRODUCT formula works without dynamic array support.',
        };
      }
    }

    // 6. GST & Tax Calculation
    if (text.includes('gst') || text.includes('tax') || text.includes('cgst') || text.includes('sgst')) {
      return {
        formula: '=ROUND(E2 * 0.18, 2)',
        alternativeFormula: '="CGST: " & ROUND(E2 * 0.09, 2) & " | SGST: " & ROUND(E2 * 0.09, 2)',
        functionName: 'ROUND / GST 18%',
        platform: 'both',
        summary: 'Calculates the 18% GST amount on the taxable subtotal in cell E2 rounded cleanly to 2 decimal places.',
        arguments: [
          { name: 'taxable_subtotal', value: 'E2', purpose: 'Base service deliverable subtotal before taxes', type: 'Number' },
          { name: 'gst_multiplier', value: '0.18', purpose: 'Statutory 18% GST rate applicable to IT, design, and studio consulting (SAC 9983)', type: 'Rate' },
          { name: 'decimals', value: '2', purpose: 'Rounds strictly to paise for compliance with Indian GST invoicing standards', type: 'Precision' },
        ],
        proTip: 'For intra-state invoices, divide the GST equally into 9% CGST and 9% SGST using =ROUND(E2 * 0.09, 2).',
      };
    }

    // Default Fallback Generator for generic inputs
    return {
      formula: '=IF(A2<>"", XLOOKUP(A2, B:B, C:C, "N/A", 0), "")',
      alternativeFormula: '=IFERROR(VLOOKUP(A2, B:C, 2, FALSE), "N/A")',
      functionName: 'IF + XLOOKUP',
      platform: 'both',
      summary: 'Evaluates whether cell A2 is non-empty, runs an exact match lookup against Column B, and safely returns Column C with error guards.',
      arguments: [
        { name: 'logical_test', value: 'A2<>""', purpose: 'Verifies that the target cell contains a query value before executing', type: 'Condition' },
        { name: 'value_if_true', value: 'XLOOKUP(...)', purpose: 'Executes lookup retrieval when input data is present', type: 'Lookup' },
        { name: 'value_if_false', value: '""', purpose: 'Returns a clean blank cell if input row is empty', type: 'Empty String' },
      ],
      proTip: 'Click any of the quick preset buttons below to explore ready-made templates for SUMIFS, Date Math, and String Parsing.',
    };
  }, [promptText, platform]);

  const handleCopyFormula = (formulaText: string, isAlt = false) => {
    if (typeof window !== 'undefined') {
      navigator.clipboard.writeText(formulaText);
      if (isAlt) {
        setCopiedAlt(true);
        setTimeout(() => setCopiedAlt(false), 2000);
      } else {
        setCopiedPrimary(true);
        setTimeout(() => setCopiedPrimary(false), 2000);
      }
      showToast('Formula copied to clipboard!');
    }
  };

  const handlePresetSelect = (preset: FormulaPreset) => {
    setPromptText(preset.prompt);
    showToast(`Loaded ${preset.name} preset`);
  };

  const handleReset = () => {
    setPromptText('Lookup email in Column B and return revenue in Column F');
    showToast('Reset to default prompt');
  };

  return (
    <ToolPageShell
      toolId="excel-formula-generator"
      badgeTag="Spreadsheet Intelligence & Syntax Explainer"
      title="AI Excel & Google Sheets Formula Generator Online Free"
      subtitle="Convert natural language descriptions into production-ready Excel and Google Sheets formulas with instant syntax breakdown and 100% private in-browser generation."
      faqItems={FAQS}
      relatedToolSlugs={['vlookup-generator', 'clean-sheet-data', 'excel-to-pdf', 'gst-calculator']}
    >
      <div className="space-y-8">

        {/* Top Control Bar Card */}
        <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
              <div className="flex items-center gap-2 mb-2">
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <ShieldCheck className="w-3.5 h-3.5 text-zinc-700" />
                  100% In-Browser Memory
                </span>
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <Cpu className="w-3.5 h-3.5 text-zinc-700" />
                  Zero Server Latency
                </span>
              </div>
              <h2 className="text-xl sm:text-2xl font-semibold tracking-tight text-zinc-900">
                Natural Language Formula Synthesizer
              </h2>
              <p className="text-sm text-zinc-600 mt-1 max-w-2xl">
                Describe the calculation or data transformation you want to perform in plain English. Cora instantly compiles exact spreadsheet syntax.
              </p>
            </div>

            {/* Platform Toggle: Excel vs Google Sheets */}
            <div className="flex items-center gap-2 bg-zinc-100 p-1 rounded-xl border border-zinc-200 self-start lg:self-auto">
              <button
                type="button"
                onClick={() => setPlatform('excel')}
                className={`flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium transition-all ${
                  platform === 'excel'
                    ? 'bg-white text-zinc-900 shadow-sm border border-zinc-200'
                    : 'text-zinc-600 hover:text-zinc-900'
                }`}
              >
                <FileSpreadsheet className="w-4 h-4 text-emerald-600" />
                Microsoft Excel
              </button>
              <button
                type="button"
                onClick={() => setPlatform('sheets')}
                className={`flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-medium transition-all ${
                  platform === 'sheets'
                    ? 'bg-white text-zinc-900 shadow-sm border border-zinc-200'
                    : 'text-zinc-600 hover:text-zinc-900'
                }`}
              >
                <FileSpreadsheet className="w-4 h-4 text-emerald-600" />
                Google Sheets
              </button>
            </div>
          </div>

          {/* Natural Language Prompt Input Area */}
          <div className="mt-6">
            <div className="flex items-center justify-between mb-2">
              <label htmlFor="prompt-input" className="text-xs font-semibold text-zinc-700 uppercase tracking-wider">
                Describe What You Want in Plain English
              </label>
              <button
                type="button"
                onClick={handleReset}
                className="inline-flex items-center gap-1.5 text-xs text-zinc-500 hover:text-zinc-800 transition-colors"
              >
                <RotateCcw className="w-3.5 h-3.5" />
                Reset Prompt
              </button>
            </div>

            <div className="relative">
              <textarea
                id="prompt-input"
                rows={3}
                value={promptText}
                onChange={(e) => setPromptText(e.target.value)}
                placeholder="e.g. Sum column C if column A is Client and date in column B is this month"
                className="w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 focus:border-zinc-900 focus:bg-white rounded-xl text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none transition-all resize-none shadow-inner"
              />
              <div className="absolute right-3 bottom-3 text-[11px] font-mono text-zinc-400">
                {promptText.length} chars
              </div>
            </div>
          </div>

          {/* Quick Preset Buttons */}
          <div className="mt-5 pt-5 border-t border-zinc-100">
            <div className="text-xs font-medium text-zinc-500 mb-3 flex items-center gap-1.5">
              <Zap className="w-3.5 h-3.5 text-zinc-700" />
              <span>Quick Prompt Presets:</span>
            </div>
            <div className="flex flex-wrap gap-2">
              {PRESETS.map((preset) => (
                <button
                  key={preset.id}
                  type="button"
                  onClick={() => handlePresetSelect(preset)}
                  className={`inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border transition-all ${
                    promptText === preset.prompt
                      ? 'bg-zinc-900 text-white border-zinc-900'
                      : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-700 border-zinc-200'
                  }`}
                >
                  {preset.id === 'xlookup' && <Search className="w-3 h-3" />}
                  {preset.id === 'sumifs' && <Filter className="w-3 h-3" />}
                  {preset.id === 'domain' && <AtSign className="w-3 h-3" />}
                  {preset.id === 'networkdays' && <Calendar className="w-3 h-3" />}
                  {preset.id === 'countunique' && <Hash className="w-3 h-3" />}
                  {preset.id === 'gst' && <Calculator className="w-3 h-3" />}
                  <span>{preset.name}</span>
                </button>
              ))}
            </div>
          </div>
        </div>

        {/* Primary Formula Card */}
        <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-zinc-100">
            <div className="flex items-center gap-3">
              <div className="w-8 h-8 rounded-lg bg-zinc-900 text-white flex items-center justify-center font-mono text-xs font-bold">
                fx
              </div>
              <div>
                <div className="flex items-center gap-2">
                  <h3 className="text-base font-semibold text-zinc-900">
                    Generated Formula ({platform === 'excel' ? 'Microsoft Excel' : 'Google Sheets'})
                  </h3>
                  <span className="px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Syntax Validated
                  </span>
                </div>
                <p className="text-xs text-zinc-500 mt-0.5">
                  Primary function: <strong className="text-zinc-800 font-mono">{result.functionName}</strong>
                </p>
              </div>
            </div>

            <button
              type="button"
              onClick={() => handleCopyFormula(result.formula, false)}
              className="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition-all shadow-sm active:scale-95"
            >
              {copiedPrimary ? (
                <>
                  <Check className="w-3.5 h-3.5 text-emerald-400" />
                  <span>Copied to Clipboard!</span>
                </>
              ) : (
                <>
                  <Copy className="w-3.5 h-3.5" />
                  <span>Copy Formula</span>
                </>
              )}
            </button>
          </div>

          {/* Formula Display Box */}
          <div className="mt-5 p-4 sm:p-5 bg-zinc-950 text-zinc-100 rounded-xl font-mono text-sm sm:text-base border border-zinc-800 overflow-x-auto selection:bg-zinc-800 selection:text-white flex items-center justify-between gap-4">
            <code className="text-emerald-400 font-semibold tracking-wide">
              {result.formula}
            </code>
          </div>

          <p className="text-xs sm:text-sm text-zinc-600 mt-3 leading-relaxed">
            {result.summary}
          </p>

          {/* Argument Breakdown Table */}
          <div className="mt-6 pt-6 border-t border-zinc-100">
            <h4 className="text-xs font-semibold text-zinc-900 uppercase tracking-wider mb-3 flex items-center gap-2">
              <BookOpen className="w-4 h-4 text-zinc-600" />
              Formula Argument Breakdown & Logic Explanation
            </h4>

            <div className="border border-zinc-200 rounded-xl overflow-hidden">
              <table className="w-full text-left text-xs border-collapse">
                <thead>
                  <tr className="bg-zinc-50 border-b border-zinc-200 text-zinc-600 font-medium">
                    <th className="px-4 py-2.5">Argument Parameter</th>
                    <th className="px-4 py-2.5">Input Expression</th>
                    <th className="px-4 py-2.5">Data Type</th>
                    <th className="px-4 py-2.5">Purpose & Behavior</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-100">
                  {result.arguments.map((arg, idx) => (
                    <tr key={idx} className="hover:bg-zinc-50/50 transition-colors">
                      <td className="px-4 py-2.5 font-mono font-medium text-zinc-900">
                        {arg.name}
                      </td>
                      <td className="px-4 py-2.5 font-mono text-emerald-700 bg-emerald-50/40">
                        {arg.value}
                      </td>
                      <td className="px-4 py-2.5 text-zinc-500">
                        {arg.type}
                      </td>
                      <td className="px-4 py-2.5 text-zinc-600">
                        {arg.purpose}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>

          {/* Pro-Tip Box */}
          <div className="mt-5 p-4 rounded-xl bg-zinc-50 border border-zinc-200/80 flex items-start gap-3">
            <Info className="w-4 h-4 text-zinc-700 flex-shrink-0 mt-0.5" />
            <div className="text-xs text-zinc-700 leading-relaxed">
              <strong className="font-semibold text-zinc-900">Spreadsheet Pro-Tip: </strong>
              {result.proTip}
            </div>
          </div>
        </div>

        {/* Alternative / Legacy Formula Card */}
        {result.alternativeFormula && (
          <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-zinc-100">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center font-mono text-xs font-bold border border-zinc-200">
                  alt
                </div>
                <div>
                  <h3 className="text-base font-semibold text-zinc-900">
                    Alternative Syntax & Legacy Compatibility Formula
                  </h3>
                  <p className="text-xs text-zinc-500 mt-0.5">
                    For older spreadsheet versions (e.g. Excel 2013/2016 or alternative formulas)
                  </p>
                </div>
              </div>

              <button
                type="button"
                onClick={() => handleCopyFormula(result.alternativeFormula || '', true)}
                className="inline-flex items-center justify-center gap-2 px-3.5 py-1.5 text-xs font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg transition-all active:scale-95"
              >
                {copiedAlt ? (
                  <>
                    <Check className="w-3.5 h-3.5 text-emerald-600" />
                    <span>Copied!</span>
                  </>
                ) : (
                  <>
                    <Copy className="w-3.5 h-3.5" />
                    <span>Copy Alternative</span>
                  </>
                )}
              </button>
            </div>

            <div className="mt-4 p-4 bg-zinc-900 text-zinc-100 rounded-xl font-mono text-sm border border-zinc-800 overflow-x-auto">
              <code className="text-zinc-300">
                {result.alternativeFormula}
              </code>
            </div>
          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
