'use client';

import React, { useState, useRef, useMemo } from 'react';
import { 
  FileText, 
  UploadCloud, 
  Download, 
  RefreshCw, 
  ShieldCheck, 
  Table, 
  Sparkles, 
  Trash2, 
  Plus, 
  Sliders, 
  Eye, 
  Check, 
  Layers, 
  FileSpreadsheet, 
  Maximize2,
  FileCheck,
  RotateCcw
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { convertTableToPdf, downloadPdfBlob } from '@/lib/pdf-engine';

const SAMPLE_DATASETS = {
  financial: {
    title: 'STUDIO COMMERCIAL STATEMENT & GST TAX LEDGER',
    subtitle: 'Prepared for: Aarav Mehta / Studio Operations • September 05, 2026',
    headers: ['Item #', 'Deliverable Scope', 'SAC Code', 'Qty', 'Unit Rate (INR)', '18% GST (INR)', 'Total (INR)'],
    rows: [
      ['01', 'Architectural Design Blueprint', '998314', '1', '75,000', '13,500', '88,500'],
      ['02', 'Client-Side PDF Engine Modules', '998314', '1', '95,000', '17,100', '1,12,100'],
      ['03', 'Component Design System Tokens', '998314', '1', '60,000', '10,800', '70,800'],
      ['04', 'Section 10A E-Signature Harness', '998314', '1', '45,000', '8,100', '53,100'],
      ['05', 'Automated QA & Staging Verification', '998314', '1', '35,000', '6,300', '41,300'],
    ],
  },
  capacity: {
    title: 'QUARTERLY TEAM CAPACITY & ALLOCATION SCHEDULE',
    subtitle: 'Operations Lead: Rohan Verma • Active Retainers Q3/Q4',
    headers: ['Resource / Lead', 'Core Competency', 'Allocated Client', 'Monthly Cap (Hrs)', 'Rate / Hr (INR)', 'Status'],
    rows: [
      ['Aarav Mehta', 'Design Systems & Tokens', 'Studio Client A', '40', '3,500', 'Committed'],
      ['Kavya Patel', 'Legal Automation & SOW', 'Studio Client B', '30', '3,500', 'Committed'],
      ['Rohan Verma', 'Fullstack & Engine Core', 'Internal / Infra', '50', '3,500', 'Active'],
      ['Senior Engineer', 'Frontend Performance', 'Client Mobile App', '35', '3,000', 'Active'],
    ],
  },
};

const FAQ_ITEMS = [
  {
    question: 'How does Cora format spreadsheet tables into publication-ready A4 PDF documents?',
    answer: 'Cora computes proportional column widths, renders high-contrast header bars, applies alternating zebra striping, and places page-numbered footers using vectorized pdf-lib commands. This ensures clean, crisp vector lines that print sharply at 300 DPI.',
  },
  {
    question: 'Can I choose between Portrait and Landscape page orientations?',
    answer: 'Yes. For wide tables with 6 or more columns, Landscape mode (842 × 595 pt) provides expansive horizontal breathing room. For standard invoice tables, Portrait mode (595 × 842 pt) delivers classic letterhead proportions.',
  },
  {
    question: 'Can I paste spreadsheet rows directly from Excel or Google Sheets?',
    answer: 'Yes. You can switch to the "Paste Spreadsheet" tab, press ⌘V (or Ctrl+V) with cells copied directly from Excel or Google Sheets, and Cora will automatically parse the tab-separated clipboard data into clean table columns.',
  },
  {
    question: 'Are financial spreadsheets or payroll numbers uploaded to external servers?',
    answer: 'No. The entire parsing and PDF generation engine runs 100% inside your local browser memory (RAM) via client-side JavaScript. Zero bytes of sensitive numbers, rates, or employee records are ever sent across the network.',
  },
  {
    question: 'Does the generator handle multi-page tables with repeated column headers?',
    answer: 'Yes. If your table contains dozens of rows that exceed a single page, Cora automatically breaks pagination cleanly and redraws the dark table header bar at the top of every subsequent page for continuous readability.',
  },
];

export default function ExcelToPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Table Configuration State
  const [docTitle, setDocTitle] = useState<string>('STUDIO COMMERCIAL STATEMENT');
  const [docSubtitle, setDocSubtitle] = useState<string>('Prepared for: Aarav Mehta / Studio Operations');
  const [headers, setHeaders] = useState<string[]>([
    'Item #', 'Deliverable Scope', 'SAC Code', 'Qty', 'Unit Rate (INR)', '18% GST (INR)', 'Total (INR)'
  ]);
  const [rows, setRows] = useState<string[][]>([
    ['01', 'Architectural Design Blueprint', '998314', '1', '75,000', '13,500', '88,500'],
    ['02', 'Client-Side PDF Engine Modules', '998314', '1', '95,000', '17,100', '1,12,100'],
    ['03', 'Component Design System Tokens', '998314', '1', '60,000', '10,800', '70,800'],
    ['04', 'Section 10A E-Signature Harness', '998314', '1', '45,000', '8,100', '53,100'],
  ]);

  // View Mode: 'builder' | 'paste'
  const [inputMode, setInputMode] = useState<'builder' | 'paste'>('builder');
  const [pastedRawText, setPastedRawText] = useState<string>('');

  // PDF Layout Options
  const [orientation, setOrientation] = useState<'portrait' | 'landscape'>('portrait');
  const [zebraStriping, setZebraStriping] = useState<boolean>(true);
  const [fontSize, setFontSize] = useState<number>(9);
  const [isGenerating, setIsGenerating] = useState<boolean>(false);

  // Quick Metrics
  const metrics = useMemo(() => {
    return {
      rowCount: rows.length,
      colCount: headers.length,
      estimatedPages: Math.max(1, Math.ceil((rows.length * 24 + 100) / (orientation === 'landscape' ? 480 : 720))),
    };
  }, [rows, headers, orientation]);

  // Parse CSV or TSV file
  const handleFileLoad = async (file: File) => {
    try {
      const text = await file.text();
      parseSpreadsheetText(text, file.name.replace(/\.[^/.]+$/, ''));
      showToast(`Loaded ${file.name}`);
    } catch (err) {
      console.error(err);
      showToast('Error reading spreadsheet file');
    }
  };

  const parseSpreadsheetText = (rawText: string, suggestedTitle?: string) => {
    const lines = rawText.split(/\r?\n/).map((l) => l.trim()).filter(Boolean);
    if (lines.length < 2) {
      showToast('Please provide at least a header row and one data row');
      return;
    }

    // Detect separator: Tab, Semicolon, Comma, Pipe
    const firstLine = lines[0];
    let delimiter = ',';
    if (firstLine.includes('\t')) delimiter = '\t';
    else if (firstLine.includes(';') && !firstLine.includes(',')) delimiter = ';';
    else if (firstLine.includes('|')) delimiter = '|';

    const parseLine = (line: string): string[] => {
      if (delimiter === ',') {
        // Simple regex CSV splitter handling quotes
        const matched = line.match(/(".*?"|[^",\s]+)(?=\s*,|\s*$)/g);
        if (matched) return matched.map((m) => m.replace(/^"|"$/g, '').trim());
      }
      return line.split(delimiter).map((c) => c.replace(/^"|"$/g, '').trim());
    };

    const parsedHeaders = parseLine(lines[0]);
    const parsedRows: string[][] = [];

    for (let i = 1; i < lines.length; i++) {
      const cells = parseLine(lines[i]);
      if (cells.length > 0) {
        // Pad or trim to match header length
        while (cells.length < parsedHeaders.length) cells.push('');
        parsedRows.push(cells.slice(0, parsedHeaders.length));
      }
    }

    if (parsedHeaders.length > 0 && parsedRows.length > 0) {
      setHeaders(parsedHeaders);
      setRows(parsedRows);
      if (suggestedTitle) setDocTitle(suggestedTitle.toUpperCase());
      setInputMode('builder');
      showToast(`Parsed ${parsedRows.length} rows and ${parsedHeaders.length} columns`);
    }
  };

  const handleApplyPastedText = () => {
    if (!pastedRawText.trim()) {
      showToast('Please paste spreadsheet data first');
      return;
    }
    parseSpreadsheetText(pastedRawText, 'PASTED_SPREADSHEET_TABLE');
  };

  const handleLoadSample = (key: 'financial' | 'capacity') => {
    const sample = SAMPLE_DATASETS[key];
    setDocTitle(sample.title);
    setDocSubtitle(sample.subtitle);
    setHeaders([...sample.headers]);
    setRows(sample.rows.map((r) => [...r]));
    setOrientation(key === 'financial' ? 'portrait' : 'landscape');
    setInputMode('builder');
    showToast(`Loaded ${key === 'financial' ? 'GST statement' : 'capacity ledger'} preset`);
  };

  const updateHeaderCell = (colIdx: number, val: string) => {
    setHeaders((prev) => {
      const next = [...prev];
      next[colIdx] = val;
      return next;
    });
  };

  const updateDataCell = (rowIdx: number, colIdx: number, val: string) => {
    setRows((prev) => {
      const next = [...prev];
      next[rowIdx] = [...next[rowIdx]];
      next[rowIdx][colIdx] = val;
      return next;
    });
  };

  const addTableRow = () => {
    setRows((prev) => [...prev, new Array(headers.length).fill('')]);
    showToast('Added row');
  };

  const removeTableRow = (idx: number) => {
    setRows((prev) => prev.filter((_, i) => i !== idx));
    showToast('Row removed');
  };

  // Generate & Download PDF
  const handleGeneratePdf = async () => {
    if (rows.length === 0 || headers.length === 0) {
      showToast('No table data to export');
      return;
    }

    setIsGenerating(true);
    try {
      const pdfBytes = await convertTableToPdf(headers, rows, {
        orientation,
        pageSize: 'a4',
        title: docTitle,
        subtitle: docSubtitle,
        zebra: zebraStriping,
        fontSize,
      });

      const fileName = `${docTitle.toLowerCase().replace(/[^a-z0-9_-]/g, '_') || 'cora_table'}.pdf`;
      downloadPdfBlob(pdfBytes, fileName);
      showToast(`Generated publication-ready PDF (${orientation})`);
    } catch (err) {
      console.error(err);
      showToast('Error generating PDF table. Please check input columns.');
    } finally {
      setIsGenerating(false);
    }
  };

  return (
    <ToolPageShell
      toolId="excel-to-pdf"
      badgeTag="A4 Publication-Ready Table Maker"
      title="Excel & CSV to PDF Table Converter Online Free"
      subtitle="Convert spreadsheets, CSV files, and pasted table rows into formatted A4 PDF tables with zebra striping and custom headers."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['pdf-to-excel', 'pdf-to-word', 'gst-calculator', 'compress-pdf']}
    >
      <div className="space-y-8">

        {/* Top Control Header Card */}
        <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
              <div className="flex items-center gap-2 mb-2">
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <ShieldCheck className="w-3.5 h-3.5 text-zinc-700" />
                  100% In-Browser Memory
                </span>
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <Sparkles className="w-3.5 h-3.5 text-zinc-700" />
                  A4 Vector Tables
                </span>
              </div>
              <h2 className="text-xl sm:text-2xl font-semibold tracking-tight text-zinc-900">
                Excel & CSV to A4 PDF Table Maker
              </h2>
              <p className="text-sm text-zinc-600 mt-1 max-w-2xl">
                Format raw spreadsheets and financial columns into polished, boardroom-ready A4 PDF documents.
              </p>
            </div>

            <div className="flex flex-wrap items-center gap-3">
              <button
                type="button"
                onClick={() => handleLoadSample('financial')}
                className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
              >
                <Sparkles className="w-4 h-4 text-zinc-600" />
                GST Ledger Sample
              </button>

              <button
                type="button"
                onClick={() => handleLoadSample('capacity')}
                className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
              >
                <Sparkles className="w-4 h-4 text-zinc-600" />
                Capacity Schedule
              </button>

              <button
                type="button"
                onClick={handleGeneratePdf}
                disabled={isGenerating}
                className="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 disabled:opacity-50 rounded-xl transition-colors shadow-sm"
              >
                {isGenerating ? <RefreshCw className="w-4 h-4 animate-spin" /> : <Download className="w-4 h-4" />}
                Export A4 PDF
              </button>
            </div>
          </div>

          {/* Styling & Orientation Presets */}
          <div className="mt-6 pt-6 border-t border-zinc-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
              <label className="block text-xs font-medium text-zinc-700 mb-2">
                Page Orientation
              </label>
              <div className="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  onClick={() => setOrientation('portrait')}
                  className={`px-3 py-2 text-xs font-medium rounded-lg border transition-all ${
                    orientation === 'portrait'
                      ? 'bg-zinc-900 text-white border-zinc-900 shadow-sm'
                      : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                  }`}
                >
                  Portrait (A4)
                </button>
                <button
                  type="button"
                  onClick={() => setOrientation('landscape')}
                  className={`px-3 py-2 text-xs font-medium rounded-lg border transition-all ${
                    orientation === 'landscape'
                      ? 'bg-zinc-900 text-white border-zinc-900 shadow-sm'
                      : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                  }`}
                >
                  Landscape (Wide)
                </button>
              </div>
            </div>

            <div>
              <label className="block text-xs font-medium text-zinc-700 mb-2">
                Zebra Row Striping
              </label>
              <div className="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  onClick={() => setZebraStriping(true)}
                  className={`px-3 py-2 text-xs font-medium rounded-lg border transition-all ${
                    zebraStriping
                      ? 'bg-zinc-900 text-white border-zinc-900 shadow-sm'
                      : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                  }`}
                >
                  Zebra Bands
                </button>
                <button
                  type="button"
                  onClick={() => setZebraStriping(false)}
                  className={`px-3 py-2 text-xs font-medium rounded-lg border transition-all ${
                    !zebraStriping
                      ? 'bg-zinc-900 text-white border-zinc-900 shadow-sm'
                      : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                  }`}
                >
                  Clean Plain
                </button>
              </div>
            </div>

            <div>
              <label className="block text-xs font-medium text-zinc-700 mb-2">
                Font Size Density
              </label>
              <div className="grid grid-cols-3 gap-2">
                {[
                  { label: '8pt', val: 8 },
                  { label: '9pt', val: 9 },
                  { label: '10pt', val: 10 },
                ].map((f) => (
                  <button
                    key={f.val}
                    type="button"
                    onClick={() => setFontSize(f.val)}
                    className={`px-2 py-2 text-xs font-medium rounded-lg border transition-all ${
                      fontSize === f.val
                        ? 'bg-zinc-900 text-white border-zinc-900 shadow-sm'
                        : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                    }`}
                  >
                    {f.label}
                  </button>
                ))}
              </div>
            </div>

            <div className="flex flex-col justify-end">
              <div className="p-2.5 bg-zinc-50 rounded-xl border border-zinc-100 flex items-center justify-between text-xs text-zinc-600">
                <span>{metrics.rowCount} Rows • {metrics.colCount} Cols</span>
                <span className="font-semibold text-zinc-900">~{metrics.estimatedPages} Page(s)</span>
              </div>
            </div>
          </div>
        </div>

        {/* Input Mode Selector Bar */}
        <div className="flex items-center gap-3 border-b border-zinc-200 pb-3 px-1">
          <button
            type="button"
            onClick={() => setInputMode('builder')}
            className={`inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${
              inputMode === 'builder'
                ? 'bg-zinc-900 text-white shadow-sm'
                : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100'
            }`}
          >
            <Table className="w-3.5 h-3.5" />
            Interactive Table Builder
          </button>
          <button
            type="button"
            onClick={() => setInputMode('paste')}
            className={`inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${
              inputMode === 'paste'
                ? 'bg-zinc-900 text-white shadow-sm'
                : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100'
            }`}
          >
            <FileSpreadsheet className="w-3.5 h-3.5" />
            Paste Spreadsheet (TSV / CSV)
          </button>

          <div className="ml-auto">
            <input
              ref={fileInputRef}
              type="file"
              accept=".csv,.tsv,.txt"
              className="hidden"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleFileLoad(e.target.files[0]);
                }
              }}
            />
            <button
              type="button"
              onClick={() => fileInputRef.current?.click()}
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 transition-colors"
            >
              <UploadCloud className="w-3.5 h-3.5" />
              Upload .CSV / .TSV
            </button>
          </div>
        </div>

        {/* Paste Box Mode */}
        {inputMode === 'paste' && (
          <div className="bg-white border border-zinc-200 rounded-2xl p-6 sm:p-8 space-y-4 shadow-sm">
            <h3 className="text-sm font-semibold text-zinc-900">
              Paste Table Rows from Excel or Google Sheets
            </h3>
            <p className="text-xs text-zinc-500">
              Copy a range of cells directly in your spreadsheet (⌘C) and paste them here. Tabs or commas will be detected automatically.
            </p>
            <textarea
              rows={8}
              value={pastedRawText}
              onChange={(e) => setPastedRawText(e.target.value)}
              placeholder="Paste table data here (e.g. Header 1	Header 2	Header 3&#10;Row 1	Data A	Data B)"
              className="w-full font-mono text-xs text-zinc-900 bg-zinc-50 border border-zinc-200 rounded-xl p-3 focus:outline-none focus:ring-1 focus:ring-zinc-900"
            />
            <div className="flex justify-end gap-3">
              <button
                type="button"
                onClick={() => setInputMode('builder')}
                className="px-4 py-2 text-xs font-medium text-zinc-600 hover:text-zinc-900"
              >
                Cancel
              </button>
              <button
                type="button"
                onClick={handleApplyPastedText}
                className="px-5 py-2 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition-colors shadow-sm"
              >
                Parse into Table
              </button>
            </div>
          </div>
        )}

        {/* Interactive Builder Mode */}
        {inputMode === 'builder' && (
          <div className="bg-white border border-zinc-200 rounded-2xl p-6 sm:p-8 space-y-6 shadow-sm">
            {/* Header Title Customization */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-2">
              <div>
                <label className="block text-[11px] font-medium text-zinc-500 uppercase tracking-wider mb-1">
                  Document Header Title
                </label>
                <input
                  type="text"
                  value={docTitle}
                  onChange={(e) => setDocTitle(e.target.value)}
                  className="w-full text-base font-bold text-zinc-900 border-b border-zinc-200 focus:border-zinc-900 focus:outline-none pb-1 bg-transparent"
                  placeholder="DOCUMENT STATEMENT TITLE"
                />
              </div>
              <div>
                <label className="block text-[11px] font-medium text-zinc-500 uppercase tracking-wider mb-1">
                  Document Subtitle / Sign-off
                </label>
                <input
                  type="text"
                  value={docSubtitle}
                  onChange={(e) => setDocSubtitle(e.target.value)}
                  className="w-full text-xs text-zinc-600 border-b border-zinc-200 focus:border-zinc-900 focus:outline-none pb-1 bg-transparent mt-1"
                  placeholder="Prepared by: Studio Operations"
                />
              </div>
            </div>

            {/* Table Matrix */}
            <div className="border border-zinc-200 rounded-xl overflow-hidden">
              <div className="overflow-x-auto max-h-[480px]">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="bg-zinc-900 text-white text-xs font-semibold sticky top-0 z-10">
                      <th className="py-2.5 px-3 w-10 text-center text-zinc-400 font-normal">#</th>
                      {headers.map((h, colIdx) => (
                        <th key={colIdx} className="py-2.5 px-3 min-w-[130px]">
                          <input
                            type="text"
                            value={h}
                            onChange={(e) => updateHeaderCell(colIdx, e.target.value)}
                            className="w-full bg-transparent text-white font-semibold focus:outline-none focus:bg-zinc-800 rounded px-1.5 py-0.5 border border-transparent focus:border-zinc-700 text-xs"
                          />
                        </th>
                      ))}
                      <th className="py-2.5 px-2 w-10 text-center text-zinc-400 font-normal">Act</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-zinc-100 text-xs">
                    {rows.map((row, rowIdx) => (
                      <tr
                        key={rowIdx}
                        className={`transition-colors group ${
                          zebraStriping && rowIdx % 2 === 1 ? 'bg-zinc-50/70' : 'bg-white'
                        } hover:bg-zinc-100/60`}
                      >
                        <td className="py-2 px-3 text-center text-zinc-400 font-mono text-[11px]">
                          {rowIdx + 1}
                        </td>
                        {headers.map((_, colIdx) => (
                          <td key={colIdx} className="py-1.5 px-2">
                            <input
                              type="text"
                              value={row[colIdx] ?? ''}
                              onChange={(e) => updateDataCell(rowIdx, colIdx, e.target.value)}
                              className="w-full bg-transparent focus:bg-white text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 rounded px-2 py-1 text-xs border border-transparent focus:border-zinc-300"
                            />
                          </td>
                        ))}
                        <td className="py-1.5 px-2 text-center">
                          <button
                            type="button"
                            onClick={() => removeTableRow(rowIdx)}
                            className="opacity-0 group-hover:opacity-100 p-1 text-zinc-400 hover:text-zinc-700 transition-opacity"
                            title="Delete row"
                          >
                            <Trash2 className="w-3.5 h-3.5" />
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Table Action Bar */}
              <div className="p-3 bg-zinc-50 border-t border-zinc-100 flex items-center justify-between text-xs">
                <button
                  type="button"
                  onClick={addTableRow}
                  className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-zinc-200 text-zinc-800 hover:bg-zinc-100 font-medium transition-colors"
                >
                  <Plus className="w-3.5 h-3.5" />
                  Add Row
                </button>

                <div className="text-zinc-500">
                  Orientation: <b>{orientation === 'portrait' ? 'Portrait A4' : 'Landscape A4'}</b> • Vector Helvetica Rendering
                </div>
              </div>
            </div>

            {/* Bottom PDF Generation Trigger */}
            <div className="pt-4 border-t border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div className="text-xs text-zinc-500">
                Compiles directly into A4 vector PDF in browser memory • No file transmission
              </div>
              <button
                type="button"
                onClick={handleGeneratePdf}
                disabled={isGenerating}
                className="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 disabled:opacity-50 rounded-xl transition-colors shadow-sm"
              >
                {isGenerating ? <RefreshCw className="w-4 h-4 animate-spin" /> : <Download className="w-4 h-4" />}
                Download Formatted A4 PDF
              </button>
            </div>
          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
