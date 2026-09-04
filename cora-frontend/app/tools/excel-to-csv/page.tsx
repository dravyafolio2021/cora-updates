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
  Layers, 
  Code, 
  CheckCircle2, 
  RefreshCw 
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { 
  parseDelimitedText, 
  serializeToCsv, 
  triggerBrowserDownload, 
  ParsedSheetData 
} from '@/lib/sheets-engine';

const SAMPLE_SPREADSHEET_DATASETS = {
  operationsRoster: {
    name: 'Studio Operations Roster',
    data: `ID\tTeam_Member\tPractice_Group\tAllocated_Client\tCapacity_Hours\tHourly_Rate_INR\tStatus
OPS-01\tAarav Mehta\tDesign Systems\tStudio Mumbai\t45\t3500\tActive
OPS-02\tRohan Verma\tCloud Infrastructure\tEnterprise Hub\t50\t3800\tCommitted
OPS-03\tKavya Patel\tLegal Automation\tNational Media\t35\t4000\tActive
OPS-04\tAarav Mehta\tClient Onboarding\tBangalore Studios\t25\t3500\tReview
OPS-05\tRohan Verma\tSecurity Audit\tFintech Corp\t40\t3800\tActive`
  },
  gstInvoiceRegister: {
    name: 'GST Commercial Ledger',
    data: `Invoice_Number;Customer_Legal_Name;SAC_Classification;Taxable_Base_INR;CGST_Amount;SGST_Amount;Gross_Invoice_INR
INV-901;Aarav Design Works;998314;150000;13500;13500;177000
INV-902;Verma Technology Labs;998314;220000;19800;19800;259600
INV-903;Kavya Compliance Group;998314;85000;7650;7650;100300
INV-904;Apex Media Collective;998314;110000;9900;9900;129800
INV-905;Metro Studio Systems;998314;195000;17550;17550;230100`
  },
  projectMilestones: {
    name: 'Project Milestones & Deliverables',
    data: `Phase_Code|Deliverable_Scope|Owner_Lead|Target_Date|Escrow_Release_INR|Verification_Status
PH-01|System Architecture & DB Schemas|Rohan Verma|2026-09-15|95000|Approved
PH-02|Client-Side Encryption Harness|Kavya Patel|2026-09-22|120000|In_Progress
PH-03|Design System Tokens & A11y Suite|Aarav Mehta|2026-09-29|80000|Pending
PH-04|Automated Testing & Release Pipeline|Rohan Verma|2026-10-06|65000|Scheduled
PH-05|Final Sign-Off & IT Act Digital Seal|Kavya Patel|2026-10-15|45000|Scheduled`
  }
};

const FAQ_ITEMS = [
  {
    question: 'How do I convert rows copied directly from Microsoft Excel or Google Sheets to CSV?',
    answer: 'Select your rows in Microsoft Excel or Google Sheets, press ⌘C (or Ctrl+V), switch to the "Paste Spreadsheet Data" tab in this tool, and press ⌘V. Cora automatically detects the clipboard tab delimiters, structures the columns, and lets you convert and download a standard RFC 4180 CSV with any delimiter.'
  },
  {
    question: 'What is the purpose of choosing custom delimiters like Semicolon or Pipe?',
    answer: 'Standard comma-separated files can conflict with regional number conventions (such as European decimals) or addresses containing commas. Semicolon-delimited CSVs are preferred for European Excel editions, while Pipe delimiters (|) are standard in database ingestion pipelines, SQL bulk loaders, and data warehousing systems.'
  },
  {
    question: 'Does the generator adhere to official RFC 4180 CSV specifications?',
    answer: 'Yes. Every cell containing your selected delimiter, line breaks, or quotation marks is automatically wrapped in double quotes, and internal quotes are properly escaped with double-quote pairs (""). This guarantees maximum compatibility across Microsoft Excel, Python Pandas, R, Postgres, and MySQL loaders.'
  },
  {
    question: 'Are my confidential spreadsheet records uploaded to any remote server?',
    answer: 'No. All parsing, delimiter swapping, quote escaping, and CSV file compilation run 100% locally in your web browser RAM via client-side JavaScript. Zero bytes of company data, payroll numbers, or client names ever leave your device.'
  },
  {
    question: 'Can I copy the converted CSV output directly without downloading a file?',
    answer: 'Yes. You can use the "Copy to Clipboard" button to instantly copy the formatted CSV text directly to your clipboard, allowing you to paste it into code editors, API requests, or terminal tools immediately.'
  }
];

export default function ExcelToCsvPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Raw text state
  const [rawInput, setRawInput] = useState<string>(SAMPLE_SPREADSHEET_DATASETS.operationsRoster.data);
  const [targetDelimiter, setTargetDelimiter] = useState<string>(',');
  const [fileName, setFileName] = useState<string>('cora_converted_data');
  
  // UI States
  const [inputMode, setInputMode] = useState<'upload' | 'paste'>('upload');
  const [previewMode, setPreviewMode] = useState<'csv' | 'grid'>('csv');
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [uploadedFileName, setUploadedFileName] = useState<string>('');
  const [copiedSuccess, setCopiedSuccess] = useState<boolean>(false);

  // Parse raw text into structured data
  const parsedData: ParsedSheetData = useMemo(() => {
    return parseDelimitedText(rawInput);
  }, [rawInput]);

  // Serialized CSV output based on target delimiter
  const convertedCsv: string = useMemo(() => {
    if (parsedData.headers.length === 0 && parsedData.rows.length === 0) return '';
    return serializeToCsv(parsedData.headers, parsedData.rows, targetDelimiter);
  }, [parsedData, targetDelimiter]);

  // File processing
  const processFile = async (file: File) => {
    if (!file) return;
    try {
      const text = await file.text();
      setRawInput(text);
      setUploadedFileName(file.name);
      const nameWithoutExt = file.name.replace(/\.[^/.]+$/, '');
      setFileName(nameWithoutExt ? `${nameWithoutExt}_converted` : 'cora_converted_data');
      showToast(`Loaded ${file.name} successfully`);
    } catch (err) {
      console.error(err);
      showToast('Error reading file. Ensure it is a valid text or delimited file.');
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

  // 1-Click Download CSV
  const handleDownloadCsv = () => {
    if (!convertedCsv) {
      showToast('No tabular data to export');
      return;
    }

    try {
      // Prepend UTF-8 BOM for clean Excel UTF-8 character recognition
      const blob = new Blob(['\uFEFF' + convertedCsv], { type: 'text/csv;charset=utf-8;' });
      const exportName = (fileName.trim() || 'cora_converted_data').replace(/\.csv$/i, '');
      triggerBrowserDownload(blob, `${exportName}.csv`);
      showToast('Downloaded RFC 4180 CSV file');
    } catch (err) {
      console.error(err);
      showToast('Failed to download CSV');
    }
  };

  // 1-Click Copy to Clipboard
  const handleCopyClipboard = () => {
    if (!convertedCsv) {
      showToast('No CSV data to copy');
      return;
    }
    navigator.clipboard.writeText(convertedCsv);
    setCopiedSuccess(true);
    showToast('Copied converted CSV to clipboard');
    setTimeout(() => setCopiedSuccess(false), 2000);
  };

  // Load sample dataset
  const loadSample = (key: keyof typeof SAMPLE_SPREADSHEET_DATASETS) => {
    const sample = SAMPLE_SPREADSHEET_DATASETS[key];
    setRawInput(sample.data);
    setUploadedFileName('');
    setFileName(sample.name.toLowerCase().replace(/[^a-z0-9_-]/g, '_'));
    showToast(`Loaded sample: ${sample.name}`);
  };

  // Reset
  const handleReset = () => {
    setRawInput('');
    setUploadedFileName('');
    setFileName('cora_converted_data');
    if (fileInputRef.current) fileInputRef.current.value = '';
    showToast('Cleared input data');
  };

  const delimiterOptions = [
    { label: 'Comma (,)', value: ',', desc: 'Standard RFC 4180' },
    { label: 'Semicolon (;)', value: ';', desc: 'European Excel standard' },
    { label: 'Tab (\\t)', value: '\t', desc: 'TSV / Sheet clipboard' },
    { label: 'Pipe (|)', value: '|', desc: 'Databases & ETL pipelines' },
  ];

  return (
    <ToolPageShell
      toolId="excel-to-csv"
      badgeTag="Spreadsheet Engine"
      title="Excel & Spreadsheet to CSV Converter"
      subtitle="Convert spreadsheet rows, TSV tables, and delimited grids into clean RFC 4180 CSV files with custom delimiter selection. 100% private in-browser memory tool."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['csv-to-excel', 'excel-to-json', 'pdf-to-excel', 'excel-to-pdf']}
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
              onClick={() => loadSample('operationsRoster')}
              className="px-3 py-1.5 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
            >
              Operations Roster (TSV)
            </button>
            <button
              type="button"
              onClick={() => loadSample('gstInvoiceRegister')}
              className="px-3 py-1.5 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
            >
              GST Commercial (Semicolon)
            </button>
            <button
              type="button"
              onClick={() => loadSample('projectMilestones')}
              className="px-3 py-1.5 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
            >
              Milestones (Pipe)
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

        {/* ── Delimiter Selector ── */}
        <div className="p-5 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-3">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2">
              <Sliders className="w-4 h-4 text-zinc-700" />
              <h3 className="text-sm font-bold text-zinc-900">Target CSV Delimiter</h3>
            </div>
            <span className="text-[11px] font-mono text-zinc-500">
              Input Detected: {parsedData.delimiter === '\t' ? 'Tab' : parsedData.delimiter === ';' ? 'Semicolon' : parsedData.delimiter === '|' ? 'Pipe' : 'Comma'}
            </span>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            {delimiterOptions.map((opt) => {
              const isSelected = targetDelimiter === opt.value;
              return (
                <button
                  key={opt.value}
                  type="button"
                  onClick={() => setTargetDelimiter(opt.value)}
                  className={`p-3 rounded-xl border text-left transition-all cursor-pointer ${
                    isSelected
                      ? 'border-zinc-950 bg-zinc-950 text-white shadow-xs'
                      : 'border-zinc-200 bg-zinc-50/50 hover:bg-zinc-100/70 text-zinc-900'
                  }`}
                >
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-xs font-bold font-mono">{opt.label}</span>
                    {isSelected && <Check className="w-3.5 h-3.5 text-white" />}
                  </div>
                  <p className={`text-[10.5px] leading-tight ${isSelected ? 'text-zinc-300' : 'text-zinc-500'}`}>
                    {opt.desc}
                  </p>
                </button>
              );
            })}
          </div>
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
              <span>Upload CSV / TXT / TSV</span>
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
              <span>Paste Spreadsheet Rows</span>
            </button>
          </div>

          <div className="p-6">
            {inputMode === 'upload' ? (
              <div>
                <input
                  ref={fileInputRef}
                  type="file"
                  accept=".csv,.txt,.tsv"
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
                    Drag and drop your spreadsheet file here
                  </h3>
                  <p className="text-xs text-zinc-500 max-w-md mx-auto mb-4">
                    Supports .csv, .tsv, and .txt files. Converted instantly into RFC 4180 CSV in local memory.
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
                  <span>Paste raw spreadsheet rows (copied from Excel, Google Sheets, or text):</span>
                  <span>{rawInput.length} characters</span>
                </div>
                <textarea
                  rows={7}
                  value={rawInput}
                  onChange={(e) => setRawInput(e.target.value)}
                  placeholder="ID&#9;Name&#9;Department&#9;Amount&#10;1&#9;Aarav Mehta&#9;Design&#9;85000&#10;2&#9;Rohan Verma&#9;Engineering&#9;120000"
                  className="w-full font-mono text-xs p-3.5 rounded-xl border border-zinc-200 bg-zinc-50/40 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:bg-white resize-y"
                />
              </div>
            )}
          </div>
        </div>

        {/* ── Live Preview & Converted Output ── */}
        <div className="p-5 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-4">
          
          {/* Header Bar with Toggle & Stats */}
          <div className="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-zinc-100">
            <div className="flex flex-wrap items-center gap-2">
              <span className="text-xs font-bold text-zinc-900">Converted Output Preview</span>
              <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-mono font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">
                {parsedData.rowCount} Rows
              </span>
              <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-mono font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">
                {parsedData.colCount} Columns
              </span>
            </div>

            {/* Preview Mode Switcher */}
            <div className="flex items-center p-0.5 rounded-lg bg-zinc-100 border border-zinc-200">
              <button
                type="button"
                onClick={() => setPreviewMode('csv')}
                className={`px-3 py-1 rounded-md text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer ${
                  previewMode === 'csv' ? 'bg-white text-zinc-950 shadow-2xs' : 'text-zinc-500 hover:text-zinc-800'
                }`}
              >
                <Code className="w-3.5 h-3.5" />
                <span>Raw CSV</span>
              </button>
              <button
                type="button"
                onClick={() => setPreviewMode('grid')}
                className={`px-3 py-1 rounded-md text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer ${
                  previewMode === 'grid' ? 'bg-white text-zinc-950 shadow-2xs' : 'text-zinc-500 hover:text-zinc-800'
                }`}
              >
                <Table className="w-3.5 h-3.5" />
                <span>Data Grid</span>
              </button>
            </div>
          </div>

          {/* View Container */}
          {previewMode === 'csv' ? (
            <div className="relative">
              <textarea
                readOnly
                rows={10}
                value={convertedCsv}
                placeholder="Converted CSV output will appear here..."
                className="w-full font-mono text-xs p-4 rounded-xl border border-zinc-200 bg-zinc-950 text-zinc-100 focus:outline-none focus:ring-0 leading-relaxed overflow-x-auto resize-y"
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
                      <span>Copy</span>
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
                          No spreadsheet rows detected.
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
              {parsedData.rows.length > 15 && (
                <div className="p-2.5 bg-zinc-50 border-t border-zinc-200 text-center text-[11px] text-zinc-500 font-mono">
                  Showing first 15 rows of {parsedData.rows.length} total rows. Full dataset included in download.
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
                  placeholder="cora_converted_data"
                  className="flex-1 px-3 py-1.5 text-xs font-mono rounded-l-xl border border-r-0 border-zinc-200 bg-zinc-50 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:bg-white"
                />
                <span className="px-2.5 py-1.5 text-xs font-mono font-semibold bg-zinc-100 border border-zinc-200 rounded-r-xl text-zinc-600">
                  .csv
                </span>
              </div>
            </div>

            <div className="flex items-center gap-3">
              <button
                type="button"
                onClick={handleCopyClipboard}
                disabled={!convertedCsv}
                className="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-800 font-semibold text-xs transition-colors shadow-2xs disabled:opacity-50 cursor-pointer"
              >
                <Copy className="w-3.5 h-3.5 text-zinc-600" />
                <span>Copy CSV Text</span>
              </button>

              <button
                type="button"
                onClick={handleDownloadCsv}
                disabled={!convertedCsv}
                className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs shadow-sm transition-all active:scale-[0.99] disabled:opacity-50 cursor-pointer"
              >
                <Download className="w-4 h-4 text-white" />
                <span>Download CSV (.csv)</span>
              </button>
            </div>
          </div>

        </div>

      </div>
    </ToolPageShell>
  );
}
