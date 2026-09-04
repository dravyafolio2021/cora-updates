'use client';

import React, { useState, useRef, useMemo, ChangeEvent, DragEvent } from 'react';
import { 
  FileSpreadsheet, 
  UploadCloud, 
  Download, 
  Copy, 
  Check, 
  Table, 
  FileText, 
  Trash2, 
  Sliders, 
  Code2, 
  Brackets, 
  Layers, 
  AlignLeft, 
  Minimize2, 
  KeyRound, 
  Eye, 
  RefreshCw 
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { 
  parseDelimitedText, 
  convertSheetToJson, 
  triggerBrowserDownload, 
  ParsedSheetData 
} from '@/lib/sheets-engine';

const SAMPLE_DATASETS = {
  clientLedger: {
    name: 'Client Accounts & SAC Ledger',
    data: `Account_ID,Client_Name,Lead_Partner,SAC_Code,Monthly_Budget,GST_Rate,Status
ACC-101,Aarav Studios Mumbai,Aarav Mehta,998314,85000,18,Active
ACC-102,Verma Architectural Lab,Rohan Verma,998314,120000,18,Active
ACC-103,Kavya Digital Legal,Kavya Patel,998314,65000,18,Pending
ACC-104,Horizon Creative Systems,Aarav Mehta,998314,95000,18,Active
ACC-105,Apex Production Collective,Rohan Verma,998314,140000,18,Active`
  },
  teamDirectory: {
    name: 'Studio Engineering Team',
    data: `Employee_ID,Full_Name,Designation,Department,Experience_Yrs,Hourly_Billing_INR
EMP-01,Aarav Mehta,Principal Design Systems,Spatial Architecture,8,3500
EMP-02,Rohan Verma,Lead Infrastructure Architect,Platform Core,9,3800
EMP-03,Kavya Patel,Senior Legal Automation Specialist,Compliance,7,4000
EMP-04,Vikram Singh,Senior Fullstack Engineer,Frontend Applications,5,2800
EMP-05,Ananya Rao,Senior Product QA Specialist,Release Verification,6,2600`
  },
  milestoneTracker: {
    name: 'Project Milestones & Deliverables',
    data: `Milestone_Code,Phase_Title,Lead_Signatory,Target_Days,Milestone_Payout_INR,Verification_Complete
M-01,Contract Inception & IT Act Deed,Kavya Patel,7,75000,true
M-02,Token Design System & A11y Suite,Aarav Mehta,14,110000,true
M-03,Client-Side PDF & Sheets Engines,Rohan Verma,21,145000,false
M-04,Staging UAT & Client Verification,Aarav Mehta,28,60000,false
M-05,Production Launch & Seal Registry,Kavya Patel,35,40000,false`
  }
};

const FAQ_ITEMS = [
  {
    question: 'How does the spreadsheet to JSON conversion work?',
    answer: 'Cora uses an in-browser tabular parser that examines the rows and columns of your CSV or spreadsheet text. It extracts the top row as object keys, type-casts numeric values, and formats the output into clean, valid JSON strings in RAM without contacting any cloud servers.'
  },
  {
    question: 'What is the difference between Array of Objects, Array of Arrays, and Keyed Object?',
    answer: 'Array of Objects generates standard REST API structures where each table row is an object with column keys. Array of Arrays produces a 2D matrix suitable for grid renderers or data science libraries. Keyed Object creates a dictionary map indexed by a specific column (such as Account_ID or Milestone_Code).'
  },
  {
    question: 'How are numbers and text values formatted in the generated JSON?',
    answer: 'Numeric values without non-numeric currency prefixes are automatically coerced into native JSON numbers. Text strings retain proper escaping for quotes, backslashes, and line breaks. If an empty cell is encountered, it is preserved as an empty string.'
  },
  {
    question: 'Is my confidential spreadsheet or employee data secure?',
    answer: 'Yes. 100% of data processing occurs inside your web browser memory. No text or files are uploaded to any external server or AI provider. Once you close or refresh the tab, all in-memory data is instantly wiped.'
  },
  {
    question: 'Can I choose between Beautified (indented) and Minified JSON?',
    answer: 'Yes. You can toggle between 2-space indented JSON (ideal for human inspection and documentation) and single-line minified JSON (optimized for minimum file size and API transmission).'
  }
];

export default function ExcelToJsonPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Raw Input & Format Options
  const [rawText, setRawText] = useState<string>(SAMPLE_DATASETS.clientLedger.data);
  const [formatMode, setFormatMode] = useState<'arrayOfObjects' | 'arrayOfArrays' | 'keyedObject'>('arrayOfObjects');
  const [keyColIndex, setKeyColIndex] = useState<number>(0);
  const [isMinified, setIsMinified] = useState<boolean>(false);
  const [fileName, setFileName] = useState<string>('cora_dataset');

  // UI States
  const [inputMode, setInputMode] = useState<'upload' | 'paste'>('upload');
  const [previewMode, setPreviewMode] = useState<'json' | 'table'>('json');
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [uploadedFileName, setUploadedFileName] = useState<string>('');
  const [copiedSuccess, setCopiedSuccess] = useState<boolean>(false);

  // Parse Delimited Text
  const parsedData: ParsedSheetData = useMemo(() => {
    return parseDelimitedText(rawText);
  }, [rawText]);

  // Generate JSON string
  const jsonOutput = useMemo(() => {
    if (parsedData.headers.length === 0 && parsedData.rows.length === 0) return '[]';
    
    try {
      const generated = convertSheetToJson(
        parsedData.headers, 
        parsedData.rows, 
        formatMode, 
        keyColIndex
      );

      if (isMinified) {
        return JSON.stringify(JSON.parse(generated));
      }
      return generated;
    } catch (err) {
      console.error('JSON conversion error:', err);
      return '// Error compiling JSON output';
    }
  }, [parsedData, formatMode, keyColIndex, isMinified]);

  // Size metrics
  const jsonMetrics = useMemo(() => {
    const byteSize = new Blob([jsonOutput]).size;
    const formattedKb = (byteSize / 1024).toFixed(2);
    return {
      byteSize,
      formattedKb,
      recordCount: parsedData.rowCount,
      fieldCount: parsedData.colCount,
    };
  }, [jsonOutput, parsedData]);

  // File loading
  const processFile = async (file: File) => {
    if (!file) return;
    try {
      const text = await file.text();
      setRawText(text);
      setUploadedFileName(file.name);
      const nameWithoutExt = file.name.replace(/\.[^/.]+$/, '');
      setFileName(nameWithoutExt ? `${nameWithoutExt}_data` : 'cora_dataset');
      setKeyColIndex(0);
      showToast(`Loaded ${file.name} successfully`);
    } catch (err) {
      console.error(err);
      showToast('Error reading file. Please provide a valid CSV or spreadsheet file.');
    }
  };

  const handleFileInputChange = (e: ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) processFile(file);
  };

  const handleDrop = (e: DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
    const file = e.dataTransfer.files?.[0];
    if (file) processFile(file);
  };

  const handleDragOver = (e: DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(true);
  };

  const handleDragLeave = (e: DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
  };

  // 1-Click Download JSON
  const handleDownloadJson = () => {
    if (!jsonOutput || jsonOutput === '[]') {
      showToast('No tabular data to export as JSON');
      return;
    }

    try {
      const blob = new Blob([jsonOutput], { type: 'application/json;charset=utf-8;' });
      const exportName = (fileName.trim() || 'cora_dataset').replace(/\.json$/i, '');
      triggerBrowserDownload(blob, `${exportName}.json`);
      showToast('Downloaded JSON file (.json)');
    } catch (err) {
      console.error(err);
      showToast('Failed to download JSON');
    }
  };

  // 1-Click Copy to Clipboard
  const handleCopyClipboard = () => {
    if (!jsonOutput || jsonOutput === '[]') {
      showToast('No JSON output to copy');
      return;
    }
    navigator.clipboard.writeText(jsonOutput);
    setCopiedSuccess(true);
    showToast('Copied JSON payload to clipboard');
    setTimeout(() => setCopiedSuccess(false), 2000);
  };

  // Load sample dataset
  const loadSample = (key: keyof typeof SAMPLE_DATASETS) => {
    const sample = SAMPLE_DATASETS[key];
    setRawText(sample.data);
    setUploadedFileName('');
    setFileName(sample.name.toLowerCase().replace(/[^a-z0-9_-]/g, '_'));
    setKeyColIndex(0);
    showToast(`Loaded sample: ${sample.name}`);
  };

  // Reset
  const handleReset = () => {
    setRawText('');
    setUploadedFileName('');
    setFileName('cora_dataset');
    if (fileInputRef.current) fileInputRef.current.value = '';
    showToast('Cleared input data');
  };

  return (
    <ToolPageShell
      toolId="excel-to-json"
      badgeTag="Developer Engine"
      title="Excel & Spreadsheet to JSON Converter"
      subtitle="Convert spreadsheets, CSV records, and tabular grids into formatted JSON objects, 2D arrays, or indexed dictionary maps. 100% private client-side compilation."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['csv-to-excel', 'excel-to-csv', 'pdf-to-excel', 'excel-to-pdf']}
    >
      <div className="space-y-6">

        {/* ── Top Control & Sample Selector Bar ── */}
        <div className="p-4 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs flex flex-wrap items-center justify-between gap-4">
          <div className="flex flex-wrap items-center gap-2">
            <span className="text-xs font-mono font-medium text-zinc-500 uppercase tracking-wider">
              Quick Samples:
            </span>
            <button
              type="button"
              onClick={() => loadSample('clientLedger')}
              className="px-3 py-1.5 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
            >
              Client Ledger
            </button>
            <button
              type="button"
              onClick={() => loadSample('teamDirectory')}
              className="px-3 py-1.5 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
            >
              Team Directory
            </button>
            <button
              type="button"
              onClick={() => loadSample('milestoneTracker')}
              className="px-3 py-1.5 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
            >
              Milestone Tracker
            </button>
          </div>

          <button
            type="button"
            onClick={handleReset}
            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 transition-colors cursor-pointer"
          >
            <Trash2 className="w-3.5 h-3.5" />
            <span>Clear Data</span>
          </button>
        </div>

        {/* ── JSON Format Selector ── */}
        <div className="p-5 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2">
              <Sliders className="w-4 h-4 text-zinc-700" />
              <h3 className="text-sm font-bold text-zinc-900">Output JSON Schema Format</h3>
            </div>
            
            {/* Minify / Beautify Toggle */}
            <div className="flex items-center gap-2">
              <button
                type="button"
                onClick={() => setIsMinified(!isMinified)}
                className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono border transition-colors cursor-pointer ${
                  isMinified 
                    ? 'bg-zinc-950 text-white border-zinc-950' 
                    : 'bg-zinc-100 text-zinc-700 border-zinc-200 hover:bg-zinc-200'
                }`}
              >
                {isMinified ? <Minimize2 className="w-3 h-3" /> : <AlignLeft className="w-3 h-3" />}
                <span>{isMinified ? 'Minified' : 'Beautified (2-Space)'}</span>
              </button>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
            
            {/* Option 1: Array of Objects */}
            <button
              type="button"
              onClick={() => setFormatMode('arrayOfObjects')}
              className={`p-3.5 rounded-xl border text-left transition-all cursor-pointer ${
                formatMode === 'arrayOfObjects'
                  ? 'border-zinc-950 bg-zinc-950 text-white shadow-xs'
                  : 'border-zinc-200 bg-zinc-50/50 hover:bg-zinc-100/70 text-zinc-900'
              }`}
            >
              <div className="flex items-center justify-between mb-1.5">
                <div className="flex items-center gap-1.5 font-mono text-xs font-bold">
                  <Brackets className="w-3.5 h-3.5" />
                  <span>Array of Objects</span>
                </div>
                {formatMode === 'arrayOfObjects' && <Check className="w-3.5 h-3.5 text-white" />}
              </div>
              <p className={`text-[11px] leading-relaxed ${formatMode === 'arrayOfObjects' ? 'text-zinc-300' : 'text-zinc-500'}`}>
                Each row becomes a key-value object: <code className="font-mono text-[10.5px]">[{`{"col": "val"}`}]</code>. Standard for REST APIs.
              </p>
            </button>

            {/* Option 2: Array of Arrays */}
            <button
              type="button"
              onClick={() => setFormatMode('arrayOfArrays')}
              className={`p-3.5 rounded-xl border text-left transition-all cursor-pointer ${
                formatMode === 'arrayOfArrays'
                  ? 'border-zinc-950 bg-zinc-950 text-white shadow-xs'
                  : 'border-zinc-200 bg-zinc-50/50 hover:bg-zinc-100/70 text-zinc-900'
              }`}
            >
              <div className="flex items-center justify-between mb-1.5">
                <div className="flex items-center gap-1.5 font-mono text-xs font-bold">
                  <Layers className="w-3.5 h-3.5" />
                  <span>Array of Arrays (2D)</span>
                </div>
                {formatMode === 'arrayOfArrays' && <Check className="w-3.5 h-3.5 text-white" />}
              </div>
              <p className={`text-[11px] leading-relaxed ${formatMode === 'arrayOfArrays' ? 'text-zinc-300' : 'text-zinc-500'}`}>
                Header row followed by raw cell values: <code className="font-mono text-[10.5px]">[[&quot;h1&quot;], [&quot;v1&quot;]]</code>. Minimal payload size.
              </p>
            </button>

            {/* Option 3: Keyed Object */}
            <button
              type="button"
              onClick={() => setFormatMode('keyedObject')}
              className={`p-3.5 rounded-xl border text-left transition-all cursor-pointer ${
                formatMode === 'keyedObject'
                  ? 'border-zinc-950 bg-zinc-950 text-white shadow-xs'
                  : 'border-zinc-200 bg-zinc-50/50 hover:bg-zinc-100/70 text-zinc-900'
              }`}
            >
              <div className="flex items-center justify-between mb-1.5">
                <div className="flex items-center gap-1.5 font-mono text-xs font-bold">
                  <KeyRound className="w-3.5 h-3.5" />
                  <span>Keyed Object (Dictionary)</span>
                </div>
                {formatMode === 'keyedObject' && <Check className="w-3.5 h-3.5 text-white" />}
              </div>
              <p className={`text-[11px] leading-relaxed ${formatMode === 'keyedObject' ? 'text-zinc-300' : 'text-zinc-500'}`}>
                Indexed dictionary mapping rows by a key column: <code className="font-mono text-[10.5px]">{`{"id": {...}}`}</code>.
              </p>
            </button>

          </div>

          {/* Primary Key Column Selector (Displayed when Keyed Object is active) */}
          {formatMode === 'keyedObject' && parsedData.headers.length > 0 && (
            <div className="p-3.5 rounded-xl bg-zinc-50 border border-zinc-200 flex flex-wrap items-center justify-between gap-3 animate-in fade-in duration-150">
              <div className="flex items-center gap-2">
                <KeyRound className="w-4 h-4 text-zinc-700" />
                <div>
                  <span className="text-xs font-bold text-zinc-900 block">Select Primary Key Column</span>
                  <span className="text-[11px] text-zinc-500">Each dictionary entry will be keyed by the unique values in this column.</span>
                </div>
              </div>

              <select
                value={keyColIndex}
                onChange={(e) => setKeyColIndex(Number(e.target.value))}
                className="px-3 py-1.5 text-xs font-mono font-semibold rounded-lg border border-zinc-300 bg-white text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900"
              >
                {parsedData.headers.map((h, idx) => (
                  <option key={idx} value={idx}>
                    Column {idx + 1}: {h}
                  </option>
                ))}
              </select>
            </div>
          )}
        </div>

        {/* ── Input Box: Upload File vs Paste Rows ── */}
        <div className="rounded-2xl bg-white border border-zinc-200/80 shadow-2xs overflow-hidden">
          <div className="flex border-b border-zinc-200/80 bg-zinc-50/50">
            <button
              type="button"
              onClick={() => setInputMode('upload')}
              className={`flex-1 py-3 px-4 text-xs font-semibold flex items-center justify-center gap-2 border-b-2 transition-colors cursor-pointer ${
                inputMode === 'upload'
                  ? 'border-zinc-950 text-zinc-950 bg-white'
                  : 'border-transparent text-zinc-500 hover:text-zinc-800'
              }`}
            >
              <UploadCloud className="w-4 h-4" />
              <span>Upload CSV / Spreadsheet File</span>
            </button>
            <button
              type="button"
              onClick={() => setInputMode('paste')}
              className={`flex-1 py-3 px-4 text-xs font-semibold flex items-center justify-center gap-2 border-b-2 transition-colors cursor-pointer ${
                inputMode === 'paste'
                  ? 'border-zinc-950 text-zinc-950 bg-white'
                  : 'border-transparent text-zinc-500 hover:text-zinc-800'
              }`}
            >
              <FileText className="w-4 h-4" />
              <span>Paste Delimited Data</span>
            </button>
          </div>

          <div className="p-6">
            {inputMode === 'upload' ? (
              <div>
                <input
                  ref={fileInputRef}
                  type="file"
                  accept=".csv,.tsv,.txt"
                  onChange={handleFileInputChange}
                  className="hidden"
                />
                <div
                  onDrop={handleDrop}
                  onDragOver={handleDragOver}
                  onDragLeave={handleDragLeave}
                  onClick={() => fileInputRef.current?.click()}
                  className={`border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-all ${
                    isDraggingOver
                      ? 'border-zinc-900 bg-zinc-100/80 scale-[0.99]'
                      : 'border-zinc-200 hover:border-zinc-400 bg-zinc-50/30'
                  }`}
                >
                  <div className="w-12 h-12 rounded-xl bg-white border border-zinc-200 flex items-center justify-center mx-auto mb-3 shadow-2xs">
                    <FileSpreadsheet className="w-6 h-6 text-zinc-700" />
                  </div>
                  <h3 className="text-sm font-bold text-zinc-900 mb-1">
                    Drag and drop your spreadsheet or CSV file here
                  </h3>
                  <p className="text-xs text-zinc-500 max-w-md mx-auto mb-4">
                    Supports comma, semicolon, tab, and pipe separated files. 100% private in-browser memory.
                  </p>
                  <button
                    type="button"
                    className="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white font-medium text-xs shadow-xs transition-colors cursor-pointer"
                  >
                    Select File from Device
                  </button>
                  {uploadedFileName && (
                    <div className="mt-4 inline-flex items-center gap-2 px-3 py-1 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-mono">
                      <Check className="w-3.5 h-3.5 text-emerald-600" />
                      <span>Loaded: {uploadedFileName}</span>
                    </div>
                  )}
                </div>
              </div>
            ) : (
              <div className="space-y-2">
                <div className="flex items-center justify-between text-xs text-zinc-500 font-mono">
                  <span>Paste spreadsheet table rows or CSV text:</span>
                  <span>{rawText.length} characters</span>
                </div>
                <textarea
                  rows={7}
                  value={rawText}
                  onChange={(e) => setRawText(e.target.value)}
                  placeholder="ID,Name,Department,Amount&#10;1,Aarav Mehta,Operations,85000&#10;2,Rohan Verma,Engineering,120000"
                  className="w-full font-mono text-xs p-3.5 rounded-xl border border-zinc-200 bg-zinc-50/40 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:bg-white resize-y"
                />
              </div>
            )}
          </div>
        </div>

        {/* ── Live JSON Preview & Metrics ── */}
        <div className="p-5 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-4">
          
          {/* Header Bar with Mode Toggle & Stats */}
          <div className="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-zinc-100">
            <div className="flex flex-wrap items-center gap-2">
              <span className="text-xs font-bold text-zinc-900">Output JSON Telemetry</span>
              <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-mono font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">
                {jsonMetrics.recordCount} Records
              </span>
              <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-mono font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">
                {jsonMetrics.fieldCount} Fields
              </span>
              <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-mono font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">
                {jsonMetrics.formattedKb} KB
              </span>
            </div>

            {/* Preview Toggle */}
            <div className="flex items-center p-0.5 rounded-lg bg-zinc-100 border border-zinc-200">
              <button
                type="button"
                onClick={() => setPreviewMode('json')}
                className={`px-3 py-1 rounded-md text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer ${
                  previewMode === 'json' ? 'bg-white text-zinc-950 shadow-2xs' : 'text-zinc-500 hover:text-zinc-800'
                }`}
              >
                <Code2 className="w-3.5 h-3.5" />
                <span>JSON Editor</span>
              </button>
              <button
                type="button"
                onClick={() => setPreviewMode('table')}
                className={`px-3 py-1 rounded-md text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer ${
                  previewMode === 'table' ? 'bg-white text-zinc-950 shadow-2xs' : 'text-zinc-500 hover:text-zinc-800'
                }`}
              >
                <Table className="w-3.5 h-3.5" />
                <span>Data Grid</span>
              </button>
            </div>
          </div>

          {/* Editor Container */}
          {previewMode === 'json' ? (
            <div className="relative">
              <textarea
                readOnly
                rows={12}
                value={jsonOutput}
                placeholder="JSON output will render here..."
                className="w-full font-mono text-xs p-4 rounded-xl border border-zinc-200 bg-zinc-950 text-emerald-400 focus:outline-none focus:ring-0 leading-relaxed overflow-x-auto resize-y selection:bg-emerald-800 selection:text-white"
              />
              <div className="absolute right-3 top-3">
                <button
                  type="button"
                  onClick={handleCopyClipboard}
                  className="px-2.5 py-1 rounded-md bg-zinc-800 hover:bg-zinc-700 text-white text-[11px] font-mono font-medium flex items-center gap-1 shadow-xs transition-colors cursor-pointer"
                >
                  {copiedSuccess ? (
                    <>
                      <Check className="w-3 h-3 text-emerald-400" />
                      <span>Copied!</span>
                    </>
                  ) : (
                    <>
                      <Copy className="w-3 h-3 text-zinc-300" />
                      <span>Copy JSON</span>
                    </>
                  )}
                </button>
              </div>
            </div>
          ) : (
            <div className="border border-zinc-200/80 rounded-xl overflow-hidden">
              <div className="overflow-x-auto max-h-[320px] overflow-y-auto">
                <table className="w-full text-left text-xs border-collapse font-mono">
                  <thead className="sticky top-0 z-10 bg-zinc-900 text-white font-sans text-[11px] uppercase tracking-wider">
                    <tr>
                      <th className="py-2.5 px-3 w-12 text-center text-zinc-400 border-b border-zinc-800">#</th>
                      {parsedData.headers.map((h, idx) => (
                        <th key={idx} className="py-2.5 px-3 font-semibold border-b border-zinc-800 whitespace-nowrap">
                          {h}
                        </th>
                      ))}
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-zinc-200/70 bg-white">
                    {parsedData.rows.length > 0 ? (
                      parsedData.rows.slice(0, 15).map((row, rIdx) => (
                        <tr key={rIdx} className={rIdx % 2 === 0 ? 'bg-white' : 'bg-zinc-50/40'}>
                          <td className="py-2 px-3 text-center text-[10.5px] text-zinc-400 font-mono select-none">
                            {rIdx + 1}
                          </td>
                          {row.map((cell, cIdx) => (
                            <td key={cIdx} className="py-2 px-3 text-zinc-800 whitespace-nowrap truncate max-w-[220px]">
                              {cell || <span className="text-zinc-300 italic">null</span>}
                            </td>
                          ))}
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td colSpan={parsedData.headers.length + 1} className="py-8 text-center text-zinc-400 text-xs">
                          No spreadsheet rows available to display.
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
              {parsedData.rows.length > 15 && (
                <div className="p-2.5 bg-zinc-50 border-t border-zinc-200 text-center text-[11px] text-zinc-500 font-mono">
                  Showing first 15 rows of {parsedData.rows.length} total rows. Full dataset converted into JSON.
                </div>
              )}
            </div>
          )}

          {/* ── File Name & Export Action Bar ── */}
          <div className="pt-2 flex flex-wrap items-center justify-between gap-3">
            <div className="flex items-center gap-2 flex-1 max-w-sm">
              <span className="text-xs font-semibold text-zinc-600 shrink-0">Filename:</span>
              <div className="flex items-center w-full">
                <input
                  type="text"
                  value={fileName}
                  onChange={(e) => setFileName(e.target.value)}
                  placeholder="cora_dataset"
                  className="flex-1 px-3 py-1.5 text-xs font-mono rounded-l-xl border border-r-0 border-zinc-200 bg-zinc-50 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:bg-white"
                />
                <span className="px-2.5 py-1.5 text-xs font-mono font-semibold bg-zinc-100 border border-zinc-200 rounded-r-xl text-zinc-600">
                  .json
                </span>
              </div>
            </div>

            <div className="flex items-center gap-3">
              <button
                type="button"
                onClick={handleCopyClipboard}
                disabled={!jsonOutput || jsonOutput === '[]'}
                className="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-800 font-semibold text-xs transition-colors shadow-2xs disabled:opacity-50 cursor-pointer"
              >
                <Copy className="w-3.5 h-3.5 text-zinc-600" />
                <span>Copy JSON</span>
              </button>

              <button
                type="button"
                onClick={handleDownloadJson}
                disabled={!jsonOutput || jsonOutput === '[]'}
                className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs shadow-sm transition-all active:scale-[0.99] disabled:opacity-50 cursor-pointer"
              >
                <Download className="w-4 h-4 text-white" />
                <span>Download JSON (.json)</span>
              </button>
            </div>
          </div>

        </div>

      </div>
    </ToolPageShell>
  );
}
