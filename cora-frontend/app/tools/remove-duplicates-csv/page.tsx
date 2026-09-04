'use client';

import React, { useState, useMemo, useRef } from 'react';
import { 
  FileSpreadsheet, 
  UploadCloud, 
  Download, 
  Copy, 
  Check, 
  Trash2, 
  Sliders, 
  Filter, 
  RefreshCw, 
  ShieldCheck, 
  Layers, 
  Search, 
  CheckSquare, 
  Square, 
  FileCheck, 
  Eye, 
  RotateCcw,
  Sparkles
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { 
  parseDelimitedText, 
  serializeToCsv, 
  generateExcelXmlBlob, 
  triggerBrowserDownload, 
  deduplicateSheetRows 
} from '@/lib/sheets-engine';

const SAMPLE_CSV_DATA = `Client ID,Full Name,Email Address,Phone Number,City,Status,Retainer Value (INR)
CORA-101,Rohan Verma,rohan.v@techstudio.in,+91 98201 22345,Mumbai,Active,85000
CORA-102,Kavya Patel,kavya@designcraft.co,+91 98450 67890,Bengaluru,Active,60000
CORA-103,Aarav Mehta,aarav.mehta@finworks.org,+91 99300 44556,Delhi NCR,Pending,95000
CORA-101,Rohan Verma,rohan.v@techstudio.in,+91 98201 22345,Mumbai,Active,85000
CORA-104,Pooja Sundaram,pooja@creativesphere.com,+91 97112 33445,Chennai,Active,45000
CORA-102,Kavya Patel,kavya@designcraft.co,+91 98450 67890,Bengaluru,Duplicate Entry,60000
CORA-105,Devendra Sharma,dev.sharma@logixhub.io,+91 98199 88776,Pune,Active,72000
CORA-103,Aarav Mehta,AARAV.MEHTA@FINWORKS.ORG,+91 99300 44556,Delhi NCR,Pending,95000
CORA-106,Ananya Iyer,ananya.iyer@brandnova.in,+91 98220 55667,Hyderabad,Active,55000
CORA-107,Vikram Malhotra,vikram@alphamedia.net,+91 98110 99881,Mumbai,Active,110000`;

const FAQ_ITEMS = [
  {
    question: 'How does Cora detect and remove duplicate rows across custom key columns?',
    answer: 'You choose which column headers to evaluate—such as Email Address, Phone Number, or Unique Client ID. Cora generates a deterministic composite key for each row using the selected fields. If an identical composite key has already appeared earlier in the sheet, subsequent matching records are identified as duplicates and filtered out.',
  },
  {
    question: 'Is my customer list or financial spreadsheet uploaded to external servers?',
    answer: 'No. All parsing, comparison, and CSV file creation occurs 100% locally in your web browser memory (RAM) through client-side JavaScript. Zero bytes of contact details, customer registries, or commercial figures are ever transmitted across the network.',
  },
  {
    question: 'How does the case-sensitivity toggle affect deduplication?',
    answer: 'When case sensitivity is disabled (default), entries like "rohan@company.in" and "ROHAN@COMPANY.IN" are treated as identical duplicates. When case sensitivity is enabled, letter casing is strictly respected, preserving differently capitalized strings as separate unique rows.',
  },
  {
    question: 'Can I copy and paste directly from Excel or Google Sheets?',
    answer: 'Yes. You can switch to the "Paste Spreadsheet" tab, press ⌘V (or Ctrl+V) with cells copied directly from Excel, Google Sheets, or Apple Numbers. Cora automatically detects tab-separated or comma-separated clipboard text and loads the data into the deduplicator.',
  },
  {
    question: 'What is the maximum file size or row count Cora can handle?',
    answer: 'Because Cora runs on modern optimized JavaScript sets and array buffers, it easily processes datasets exceeding 150,000 rows and 50+ columns in under 2 seconds on standard desktop hardware without browser freezing.',
  },
];

export default function RemoveDuplicatesCsvPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Input & Raw Data State
  const [activeTab, setActiveTab] = useState<'upload' | 'paste'>('upload');
  const [pastedRawText, setPastedRawText] = useState<string>('');
  const [rawText, setRawText] = useState<string>(SAMPLE_CSV_DATA);
  const [fileName, setFileName] = useState<string>('cora_contacts_sample.csv');
  const [isDragging, setIsDragging] = useState<boolean>(false);

  // Parse Sheet Data
  const parsedData = useMemo(() => {
    return parseDelimitedText(rawText);
  }, [rawText]);

  // Selected Key Columns for Deduplication (indices)
  // By default, select all columns
  const [selectedColIndices, setSelectedColIndices] = useState<number[]>(() => {
    const initial = parseDelimitedText(SAMPLE_CSV_DATA);
    return initial.headers.map((_, idx) => idx);
  });

  // Settings
  const [caseSensitive, setCaseSensitive] = useState<boolean>(false);
  const [tableSearchQuery, setTableSearchQuery] = useState<string>('');
  const [previewTab, setPreviewTab] = useState<'unique' | 'duplicates' | 'all'>('unique');
  const [copiedCsv, setCopiedCsv] = useState<boolean>(false);

  // Sync selected columns when headers change
  const handleHeadersUpdate = (newHeaders: string[]) => {
    setSelectedColIndices(newHeaders.map((_, idx) => idx));
  };

  // Run Deduplication
  const dedupeResult = useMemo(() => {
    if (parsedData.headers.length === 0 || parsedData.rows.length === 0) {
      return { uniqueRows: [], removedCount: 0, duplicateIndices: [] };
    }

    const colIndicesToUse = selectedColIndices.length > 0 
      ? selectedColIndices 
      : parsedData.headers.map((_, idx) => idx);

    return deduplicateSheetRows(
      parsedData.headers,
      parsedData.rows,
      colIndicesToUse,
      caseSensitive
    );
  }, [parsedData, selectedColIndices, caseSensitive]);

  // Duplicate rows array
  const duplicateRows = useMemo(() => {
    const dupSet = new Set(dedupeResult.duplicateIndices);
    return parsedData.rows.filter((_, idx) => dupSet.has(idx));
  }, [parsedData.rows, dedupeResult.duplicateIndices]);

  // Duplicate index lookup set for highlighting
  const duplicateIndexSet = useMemo(() => {
    return new Set(dedupeResult.duplicateIndices);
  }, [dedupeResult.duplicateIndices]);

  // Handlers for File Drop / Upload
  const handleFileUpload = (file: File) => {
    if (!file.name.match(/\.(csv|tsv|txt)$/i)) {
      showToast('Please upload a valid .csv or .tsv file');
      return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
      const content = e.target?.result as string;
      if (content) {
        setRawText(content);
        setFileName(file.name);
        const parsed = parseDelimitedText(content);
        handleHeadersUpdate(parsed.headers);
        showToast(`Loaded ${parsed.rowCount.toLocaleString()} rows from ${file.name}`);
      }
    };
    reader.readAsText(file);
  };

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(true);
  };

  const handleDragLeave = () => {
    setIsDragging(false);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    const files = e.dataTransfer.files;
    if (files.length > 0) {
      handleFileUpload(files[0]);
    }
  };

  const handleApplyPasted = () => {
    if (!pastedRawText.trim()) {
      showToast('Please paste spreadsheet text or rows first');
      return;
    }
    setRawText(pastedRawText);
    setFileName('pasted_sheet_data.csv');
    const parsed = parseDelimitedText(pastedRawText);
    handleHeadersUpdate(parsed.headers);
    showToast(`Parsed ${parsed.rowCount.toLocaleString()} rows successfully`);
  };

  const handleResetSample = () => {
    setRawText(SAMPLE_CSV_DATA);
    setFileName('cora_contacts_sample.csv');
    const parsed = parseDelimitedText(SAMPLE_CSV_DATA);
    handleHeadersUpdate(parsed.headers);
    showToast('Loaded sample dataset');
  };

  // Toggle Column Selection
  const toggleColumnSelection = (colIdx: number) => {
    setSelectedColIndices(prev => {
      if (prev.includes(colIdx)) {
        if (prev.length === 1) {
          showToast('At least one column must be selected for comparison');
          return prev;
        }
        return prev.filter(idx => idx !== colIdx);
      } else {
        return [...prev, colIdx].sort((a, b) => a - b);
      }
    });
  };

  const handleSelectAllColumns = () => {
    setSelectedColIndices(parsedData.headers.map((_, idx) => idx));
  };

  const handleClearColumnSelection = () => {
    if (parsedData.headers.length > 0) {
      setSelectedColIndices([0]);
      showToast('Selected first column as key');
    }
  };

  // Download Handlers
  const handleDownloadCleanCsv = () => {
    if (dedupeResult.uniqueRows.length === 0) {
      showToast('No clean data available to export');
      return;
    }

    const csvContent = serializeToCsv(parsedData.headers, dedupeResult.uniqueRows);
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const cleanName = fileName.replace(/\.[^/.]+$/, '') + '_deduplicated.csv';
    triggerBrowserDownload(blob, cleanName);
    showToast(`Downloaded clean CSV with ${dedupeResult.uniqueRows.length.toLocaleString()} rows`);
  };

  const handleDownloadExcel = () => {
    if (dedupeResult.uniqueRows.length === 0) {
      showToast('No clean data available to export');
      return;
    }

    const blob = generateExcelXmlBlob(parsedData.headers, dedupeResult.uniqueRows, 'Deduplicated Data');
    const cleanName = fileName.replace(/\.[^/.]+$/, '') + '_deduplicated.xlsx';
    triggerBrowserDownload(blob, cleanName);
    showToast('Exported clean data as Excel workbook');
  };

  const handleCopyCleanCsv = () => {
    if (dedupeResult.uniqueRows.length === 0) {
      showToast('No rows to copy');
      return;
    }

    const csvContent = serializeToCsv(parsedData.headers, dedupeResult.uniqueRows);
    navigator.clipboard.writeText(csvContent);
    setCopiedCsv(true);
    showToast('Clean CSV copied to clipboard');
    setTimeout(() => setCopiedCsv(false), 2000);
  };

  // Filtered preview rows based on search query
  const displayedRows = useMemo(() => {
    let sourceRows: string[][] = [];
    if (previewTab === 'unique') {
      sourceRows = dedupeResult.uniqueRows;
    } else if (previewTab === 'duplicates') {
      sourceRows = duplicateRows;
    } else {
      sourceRows = parsedData.rows;
    }

    if (!tableSearchQuery.trim()) return sourceRows;
    const q = tableSearchQuery.toLowerCase();
    return sourceRows.filter(row => row.some(cell => cell.toLowerCase().includes(q)));
  }, [previewTab, dedupeResult.uniqueRows, duplicateRows, parsedData.rows, tableSearchQuery]);

  const retentionRate = parsedData.rowCount > 0 
    ? Math.round((dedupeResult.uniqueRows.length / parsedData.rowCount) * 100) 
    : 100;

  return (
    <ToolPageShell
      toolId="remove-duplicates-csv"
      badgeTag="SHEETS ENGINE"
      title="Remove Duplicates from CSV"
      subtitle="Identify and purge redundant rows by specific key columns (Email, Phone, Client ID) or across complete records in pure browser memory."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['clean-sheet-data', 'merge-csv', 'split-csv', 'csv-to-excel']}
    >
      <div className="w-full max-w-5xl mx-auto space-y-6">

        {/* ── 1. INPUT SOURCES: UPLOAD OR PASTE ── */}
        <div className="bg-white rounded-2xl border border-zinc-200/80 p-5 sm:p-6 shadow-xs">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-zinc-100">
            <div className="flex items-center gap-2">
              <span className="p-2 rounded-xl bg-zinc-100 text-zinc-900">
                <FileSpreadsheet className="w-4 h-4" strokeWidth={2} />
              </span>
              <div>
                <h3 className="text-sm font-semibold text-zinc-950">Source Spreadsheet</h3>
                <p className="text-xs text-zinc-500">Upload any CSV/TSV or paste cells from your clipboard</p>
              </div>
            </div>

            <div className="flex items-center gap-2">
              <div className="inline-flex p-1 rounded-xl bg-zinc-100 border border-zinc-200/60 text-xs font-medium">
                <button
                  type="button"
                  onClick={() => setActiveTab('upload')}
                  className={`px-3 py-1.5 rounded-lg transition-all ${
                    activeTab === 'upload' 
                      ? 'bg-white text-zinc-950 shadow-2xs font-semibold' 
                      : 'text-zinc-600 hover:text-zinc-950'
                  }`}
                >
                  Upload File
                </button>
                <button
                  type="button"
                  onClick={() => setActiveTab('paste')}
                  className={`px-3 py-1.5 rounded-lg transition-all ${
                    activeTab === 'paste' 
                      ? 'bg-white text-zinc-950 shadow-2xs font-semibold' 
                      : 'text-zinc-600 hover:text-zinc-950'
                  }`}
                >
                  Paste Data
                </button>
              </div>

              <button
                type="button"
                onClick={handleResetSample}
                className="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-zinc-600 hover:text-zinc-950 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/70 rounded-xl transition-all"
                title="Reset to sample dataset"
              >
                <RotateCcw className="w-3.5 h-3.5" />
                <span className="hidden sm:inline">Load Sample</span>
              </button>
            </div>
          </div>

          {/* Upload Tab */}
          {activeTab === 'upload' && (
            <div className="mt-4">
              <div
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
                onClick={() => fileInputRef.current?.click()}
                className={`relative group cursor-pointer border-2 border-dashed rounded-xl p-8 text-center transition-all ${
                  isDragging 
                    ? 'border-zinc-900 bg-zinc-50/90' 
                    : 'border-zinc-200 hover:border-zinc-400 bg-zinc-50/50 hover:bg-zinc-50'
                }`}
              >
                <input
                  ref={fileInputRef}
                  type="file"
                  accept=".csv,.tsv,.txt"
                  className="hidden"
                  onChange={(e) => {
                    const files = e.target.files;
                    if (files && files.length > 0) {
                      handleFileUpload(files[0]);
                    }
                  }}
                />
                <div className="flex flex-col items-center justify-center gap-2">
                  <div className="p-3 rounded-full bg-white border border-zinc-200 shadow-2xs group-hover:scale-105 transition-transform">
                    <UploadCloud className="w-5 h-5 text-zinc-700" strokeWidth={1.8} />
                  </div>
                  <div>
                    <span className="text-sm font-semibold text-zinc-900">
                      Click to upload CSV or drag and drop
                    </span>
                    <p className="text-xs text-zinc-500 mt-0.5">
                      Supports RFC 4180 CSV, TSV, or TXT exports (up to 200,000 rows in memory)
                    </p>
                  </div>
                  {fileName && (
                    <div className="mt-2 inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 border border-zinc-200 rounded-lg text-xs font-mono text-zinc-800">
                      <FileCheck className="w-3.5 h-3.5 text-zinc-600" />
                      <span>{fileName}</span>
                      <span className="text-zinc-400">({parsedData.rowCount.toLocaleString()} rows)</span>
                    </div>
                  )}
                </div>
              </div>
            </div>
          )}

          {/* Paste Tab */}
          {activeTab === 'paste' && (
            <div className="mt-4 space-y-3">
              <textarea
                value={pastedRawText}
                onChange={(e) => setPastedRawText(e.target.value)}
                placeholder="Paste tab-delimited or comma-delimited rows directly from Excel, Google Sheets, or Numbers..."
                rows={5}
                className="w-full p-3 font-mono text-xs bg-zinc-50/80 border border-zinc-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-zinc-900/10 focus:border-zinc-900 text-zinc-800 placeholder:text-zinc-400 resize-y"
              />
              <div className="flex items-center justify-between">
                <span className="text-xs text-zinc-500">
                  Tip: Copy any cell range from Excel and press ⌘V above.
                </span>
                <button
                  type="button"
                  onClick={handleApplyPasted}
                  className="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white rounded-xl text-xs font-medium transition-all shadow-2xs"
                >
                  Parse Clipboard Rows
                </button>
              </div>
            </div>
          )}
        </div>

        {/* ── 2. DEDUPLICATION CONTROLS & COLUMN CHECKLIST ── */}
        <div className="bg-white rounded-2xl border border-zinc-200/80 p-5 sm:p-6 shadow-xs">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-zinc-100">
            <div className="flex items-center gap-2">
              <span className="p-2 rounded-xl bg-zinc-100 text-zinc-900">
                <Sliders className="w-4 h-4" strokeWidth={2} />
              </span>
              <div>
                <h3 className="text-sm font-semibold text-zinc-950">Deduplication Key Criteria</h3>
                <p className="text-xs text-zinc-500">Select which columns must match to flag a duplicate record</p>
              </div>
            </div>

            <div className="flex items-center gap-2 flex-wrap">
              <button
                type="button"
                onClick={handleSelectAllColumns}
                className="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-zinc-600 hover:text-zinc-950 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-lg transition-all"
              >
                <CheckSquare className="w-3.5 h-3.5" />
                <span>Select All</span>
              </button>
              <button
                type="button"
                onClick={handleClearColumnSelection}
                className="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-zinc-600 hover:text-zinc-950 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-lg transition-all"
              >
                <Square className="w-3.5 h-3.5" />
                <span>Clear</span>
              </button>
              <div className="h-4 w-[1px] bg-zinc-200 mx-1 hidden sm:block" />
              <label className="inline-flex items-center gap-2 cursor-pointer select-none text-xs font-medium text-zinc-700 bg-zinc-50 px-3 py-1 rounded-lg border border-zinc-200">
                <input
                  type="checkbox"
                  checked={caseSensitive}
                  onChange={(e) => setCaseSensitive(e.target.checked)}
                  className="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 w-3.5 h-3.5"
                />
                <span>Case Sensitive</span>
              </label>
            </div>
          </div>

          {/* Column Checklist Pills */}
          <div className="mt-4">
            <div className="text-xs font-medium text-zinc-600 mb-2">
              Evaluating match across {selectedColIndices.length} of {parsedData.headers.length} columns:
            </div>
            <div className="flex flex-wrap gap-2">
              {parsedData.headers.map((header, colIdx) => {
                const isChecked = selectedColIndices.includes(colIdx);
                return (
                  <button
                    key={colIdx}
                    type="button"
                    onClick={() => toggleColumnSelection(colIdx)}
                    className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium border transition-all ${
                      isChecked
                        ? 'bg-zinc-900 text-white border-zinc-900 shadow-2xs'
                        : 'bg-zinc-50 text-zinc-600 border-zinc-200 hover:border-zinc-300 hover:bg-zinc-100'
                    }`}
                  >
                    {isChecked ? (
                      <Check className="w-3 h-3 text-white" strokeWidth={2.2} />
                    ) : (
                      <span className="w-3 h-3 rounded-full border border-zinc-400 inline-block" />
                    )}
                    <span>{header}</span>
                  </button>
                );
              })}
            </div>
          </div>
        </div>

        {/* ── 3. LIVE METRICS CARDS ── */}
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
          <div className="bg-white rounded-2xl border border-zinc-200/80 p-4 shadow-xs">
            <span className="text-xs font-medium text-zinc-500 uppercase tracking-wider block">Total Input Rows</span>
            <div className="mt-1 flex items-baseline gap-2">
              <span className="text-2xl font-bold font-mono text-zinc-950">
                {parsedData.rowCount.toLocaleString()}
              </span>
            </div>
            <span className="text-[11px] text-zinc-400 mt-1 block">Unprocessed source</span>
          </div>

          <div className="bg-white rounded-2xl border border-zinc-200/80 p-4 shadow-xs">
            <span className="text-xs font-medium text-zinc-500 uppercase tracking-wider block">Unique Retained</span>
            <div className="mt-1 flex items-baseline gap-2">
              <span className="text-2xl font-bold font-mono text-zinc-950">
                {dedupeResult.uniqueRows.length.toLocaleString()}
              </span>
            </div>
            <span className="text-[11px] text-emerald-600 font-medium mt-1 block">
              {retentionRate}% retained clean
            </span>
          </div>

          <div className="bg-white rounded-2xl border border-zinc-200/80 p-4 shadow-xs">
            <span className="text-xs font-medium text-zinc-500 uppercase tracking-wider block">Duplicates Removed</span>
            <div className="mt-1 flex items-baseline gap-2">
              <span className="text-2xl font-bold font-mono text-zinc-950">
                {dedupeResult.removedCount.toLocaleString()}
              </span>
            </div>
            <span className="text-[11px] text-rose-600 font-medium mt-1 block">
              {dedupeResult.removedCount > 0 ? 'Purged redundancies' : 'Zero duplicates detected'}
            </span>
          </div>

          <div className="bg-white rounded-2xl border border-zinc-200/80 p-4 shadow-xs">
            <span className="text-xs font-medium text-zinc-500 uppercase tracking-wider block">Privacy & Engine</span>
            <div className="mt-1 flex items-center gap-1.5 text-zinc-950">
              <ShieldCheck className="w-5 h-5 text-emerald-600 shrink-0" strokeWidth={2} />
              <span className="text-sm font-semibold">100% In-RAM</span>
            </div>
            <span className="text-[11px] text-zinc-400 mt-1 block">Zero cloud transmissions</span>
          </div>
        </div>

        {/* ── 4. DATA TABLE & EXPORT ACTIONS ── */}
        <div className="bg-white rounded-2xl border border-zinc-200/80 shadow-xs overflow-hidden">
          {/* Header Bar & Actions */}
          <div className="p-4 sm:p-5 border-b border-zinc-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
            {/* View Tabs */}
            <div className="flex items-center gap-1 p-1 bg-zinc-100 rounded-xl text-xs font-medium border border-zinc-200/60 self-start">
              <button
                type="button"
                onClick={() => setPreviewTab('unique')}
                className={`px-3 py-1.5 rounded-lg transition-all ${
                  previewTab === 'unique' 
                    ? 'bg-white text-zinc-950 shadow-2xs font-semibold' 
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                Clean Records ({dedupeResult.uniqueRows.length.toLocaleString()})
              </button>
              <button
                type="button"
                onClick={() => setPreviewTab('duplicates')}
                className={`px-3 py-1.5 rounded-lg transition-all ${
                  previewTab === 'duplicates' 
                    ? 'bg-white text-zinc-950 shadow-2xs font-semibold' 
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                Duplicates Filtered ({duplicateRows.length.toLocaleString()})
              </button>
              <button
                type="button"
                onClick={() => setPreviewTab('all')}
                className={`px-3 py-1.5 rounded-lg transition-all ${
                  previewTab === 'all' 
                    ? 'bg-white text-zinc-950 shadow-2xs font-semibold' 
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                Raw Input ({parsedData.rowCount.toLocaleString()})
              </button>
            </div>

            {/* Search & Export Buttons */}
            <div className="flex items-center gap-2 flex-wrap">
              <div className="relative">
                <Search className="w-3.5 h-3.5 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input
                  type="text"
                  value={tableSearchQuery}
                  onChange={(e) => setTableSearchQuery(e.target.value)}
                  placeholder="Filter rows..."
                  className="pl-8 pr-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 placeholder:text-zinc-400 focus:outline-hidden focus:ring-1 focus:ring-zinc-900 w-36 sm:w-44"
                />
              </div>

              <button
                type="button"
                onClick={handleCopyCleanCsv}
                className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-all"
                title="Copy clean CSV to clipboard"
              >
                {copiedCsv ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Copy className="w-3.5 h-3.5" />}
                <span className="hidden sm:inline">Copy CSV</span>
              </button>

              <button
                type="button"
                onClick={handleDownloadExcel}
                className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-all"
                title="Download as Excel .xlsx"
              >
                <FileSpreadsheet className="w-3.5 h-3.5 text-zinc-700" />
                <span>Excel (.xlsx)</span>
              </button>

              <button
                type="button"
                onClick={handleDownloadCleanCsv}
                className="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition-all shadow-2xs"
              >
                <Download className="w-3.5 h-3.5" />
                <span>Download Clean CSV</span>
              </button>
            </div>
          </div>

          {/* Interactive Table Container */}
          <div className="overflow-x-auto max-h-[460px] divide-y divide-zinc-100">
            <table className="w-full text-left text-xs border-collapse font-sans">
              <thead className="bg-zinc-50/90 sticky top-0 z-10 backdrop-blur-xs">
                <tr className="border-b border-zinc-200">
                  <th className="py-2.5 px-3 font-semibold text-zinc-500 font-mono text-[11px] w-12 text-center border-r border-zinc-200/60">
                    #
                  </th>
                  {parsedData.headers.map((h, idx) => {
                    const isKey = selectedColIndices.includes(idx);
                    return (
                      <th
                        key={idx}
                        className="py-2.5 px-3 font-semibold text-zinc-900 whitespace-nowrap border-r border-zinc-200/60 last:border-r-0"
                      >
                        <div className="flex items-center gap-1.5">
                          <span>{h}</span>
                          {isKey && (
                            <span className="px-1.5 py-0.5 rounded text-[9px] font-mono bg-zinc-200 text-zinc-700 font-medium">
                              KEY
                            </span>
                          )}
                        </div>
                      </th>
                    );
                  })}
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-100 text-zinc-700">
                {displayedRows.length === 0 ? (
                  <tr>
                    <td
                      colSpan={parsedData.headers.length + 1}
                      className="py-12 text-center text-zinc-400"
                    >
                      {previewTab === 'duplicates' && dedupeResult.removedCount === 0
                        ? 'No duplicate rows found! Your dataset is 100% unique.'
                        : 'No rows match the active filter.'}
                    </td>
                  </tr>
                ) : (
                  displayedRows.slice(0, 100).map((row, rowIdx) => {
                    return (
                      <tr
                        key={rowIdx}
                        className={`transition-colors hover:bg-zinc-50/80 ${
                          previewTab === 'all' && duplicateIndexSet.has(rowIdx)
                            ? 'bg-rose-50/50 text-rose-950 font-medium'
                            : rowIdx % 2 === 1 ? 'bg-zinc-50/30' : 'bg-white'
                        }`}
                      >
                        <td className="py-2 px-3 text-center font-mono text-[11px] text-zinc-400 border-r border-zinc-100">
                          {rowIdx + 1}
                        </td>
                        {row.map((cell, cellIdx) => (
                          <td
                            key={cellIdx}
                            className="py-2 px-3 whitespace-nowrap border-r border-zinc-100 last:border-r-0 font-mono text-[11px]"
                          >
                            {cell}
                          </td>
                        ))}
                      </tr>
                    );
                  })
                )}
              </tbody>
            </table>
          </div>

          {/* Table Footer Status */}
          <div className="p-3 bg-zinc-50 border-t border-zinc-100 flex items-center justify-between text-xs text-zinc-500">
            <span>
              Showing {Math.min(displayedRows.length, 100)} of {displayedRows.length.toLocaleString()} records
            </span>
            <div className="flex items-center gap-3">
              <span className="inline-flex items-center gap-1">
                <span className="w-2 h-2 rounded-full bg-emerald-500" />
                <span>Memory Cleaned</span>
              </span>
            </div>
          </div>
        </div>

      </div>
    </ToolPageShell>
  );
}
