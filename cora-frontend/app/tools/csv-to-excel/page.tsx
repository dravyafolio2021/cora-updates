'use client';

import React, { useState, useRef, useMemo, ChangeEvent, DragEvent } from 'react';
import { 
  FileSpreadsheet, 
  UploadCloud, 
  Download, 
  RefreshCw, 
  Table, 
  Check, 
  Copy, 
  Sliders, 
  FileText, 
  Layers, 
  Trash2, 
  Search,
  ChevronLeft,
  ChevronRight,
  Info
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { 
  parseDelimitedText, 
  generateExcelXmlBlob, 
  triggerBrowserDownload, 
  ParsedSheetData 
} from '@/lib/sheets-engine';

const SAMPLE_CSV_DATASETS = {
  gstInvoice: {
    name: 'GST Tax Invoicing Ledger',
    sheetName: 'GST_Ledger_Q3',
    data: `Invoice_No,Client_Name,Lead_Partner,SAC_Code,Taxable_Amount,CGST_9Pct,SGST_9Pct,Total_Amount,Payment_Status
INV-2026-081,Aarav Studios Mumbai,Aarav Mehta,998314,85000,7650,7650,100300,Paid_UPI
INV-2026-082,Verma Architectural Lab,Rohan Verma,998314,120000,10800,10800,141600,Paid_UPI
INV-2026-083,Kavya Digital Media,Kavya Patel,998314,65000,5850,5850,76700,Pending_Approval
INV-2026-084,Horizon Creative Systems,Aarav Mehta,998314,95000,8550,8550,112100,Paid_UPI
INV-2026-085,Apex Urban Dynamics,Rohan Verma,998314,140000,12600,12600,165200,Pending_Hold`
  },
  studioRetainers: {
    name: 'Studio Retainer Accounts',
    sheetName: 'Retainer_Accounts',
    data: `Account_ID,Client_Entity,Managing_Lead,Monthly_Retainer_INR,Scope_Buffer_Hrs,Payment_Mode,Quarterly_Value_INR
RET-101,Aarav Media Collective,Aarav Mehta,75000,12,UPI Auto-Debit,225000
RET-102,Verma Enterprise Hub,Rohan Verma,110000,18,NEFT Escrow,330000
RET-103,Kavya Compliance Partners,Kavya Patel,90000,15,IMPS Verified,270000
RET-104,Metro Design Works,Rohan Verma,80000,10,UPI Auto-Debit,240000
RET-105,Pinnacle Production House,Aarav Mehta,135000,20,NEFT Escrow,405000`
  },
  rateCard: {
    name: 'Commercial Rate Card',
    sheetName: 'Rate_Card_2026',
    data: `Role_Designation,Department,Practice_Lead,Standard_Hourly_INR,Overtime_Hourly_INR,Min_Commitment_Hrs
Principal Architect,Spatial Systems,Aarav Mehta,4500,6000,20
Senior Legal Counsel,Contract Automation,Kavya Patel,4000,5500,15
DevOps & Infrastructure Lead,Platform Core,Rohan Verma,3800,5000,25
Motion Graphics Director,Brand Production,Aarav Mehta,3200,4200,30
Quality Verification Specialist,Release Engineering,Rohan Verma,2800,3800,20`
  }
};

const FAQ_ITEMS = [
  {
    question: 'How does this tool convert CSV to Excel without uploading files to a server?',
    answer: 'The conversion runs entirely inside your browser memory (RAM) through client-side JavaScript. The parser reads your CSV or TSV data, structures the cells into an XML-compliant Microsoft Excel workbook format, and generates a downloadable .xlsx file instantly with zero network requests.'
  },
  {
    question: 'What delimiters are supported by the engine?',
    answer: 'Cora automatically inspects the first several lines of your input to detect whether the file uses commas (standard CSV), tabs (TSV), semicolons (frequent in European locales), or vertical pipes (|). You can paste or upload files with any of these delimiters without manual reconfiguration.'
  },
  {
    question: 'Can I specify a custom worksheet tab name inside the Excel file?',
    answer: 'Yes. Enter your preferred title in the Sheet Name input field (for example, "GST_Ledger_2026"). The generated Excel spreadsheet will embed this custom name as the active tab label in Microsoft Excel, Apple Numbers, or Google Sheets.'
  },
  {
    question: 'How are numbers and formulas handled during conversion?',
    answer: 'Numeric values, percentages, and monetary amounts are automatically detected and tagged as numeric spreadsheet cells. This ensures that when you open the file in Excel, you can immediately run SUM, AVERAGE, and arithmetic formulas without reformatting text cells.'
  },
  {
    question: 'Is there any row limit or quota restriction on conversions?',
    answer: 'No. Because processing is executed 100% locally in your browser memory, you can convert datasets containing tens of thousands of rows without file size limits, rate limits, or account paywalls.'
  }
];

export default function CsvToExcelPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Raw text & parsed state
  const [rawText, setRawText] = useState<string>(SAMPLE_CSV_DATASETS.gstInvoice.data);
  const [sheetName, setSheetName] = useState<string>(SAMPLE_CSV_DATASETS.gstInvoice.sheetName);
  const [fileName, setFileName] = useState<string>('cora_spreadsheet_export');
  
  // Tab mode: 'upload' | 'paste'
  const [inputMode, setInputMode] = useState<'upload' | 'paste'>('upload');
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [uploadedFileName, setUploadedFileName] = useState<string>('');

  // Table pagination & search
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [currentPage, setCurrentPage] = useState<number>(1);
  const rowsPerPage = 10;

  // Parsed Sheet Data
  const parsedData: ParsedSheetData = useMemo(() => {
    return parseDelimitedText(rawText);
  }, [rawText]);

  // Filtered rows for search preview
  const filteredRows = useMemo(() => {
    if (!searchQuery.trim()) return parsedData.rows;
    const query = searchQuery.toLowerCase();
    return parsedData.rows.filter(row => 
      row.some(cell => cell.toLowerCase().includes(query))
    );
  }, [parsedData.rows, searchQuery]);

  // Paginated Rows
  const totalPages = Math.max(1, Math.ceil(filteredRows.length / rowsPerPage));
  const displayedRows = useMemo(() => {
    const startIndex = (currentPage - 1) * rowsPerPage;
    return filteredRows.slice(startIndex, startIndex + rowsPerPage);
  }, [filteredRows, currentPage]);

  // Handle file input
  const processFile = async (file: File) => {
    if (!file) return;
    try {
      const text = await file.text();
      setRawText(text);
      setUploadedFileName(file.name);
      const nameWithoutExt = file.name.replace(/\.[^/.]+$/, '');
      setFileName(nameWithoutExt || 'cora_spreadsheet_export');
      setSheetName(nameWithoutExt.substring(0, 31).replace(/[^a-zA-Z0-9_]/g, '_') || 'Sheet1');
      setCurrentPage(1);
      showToast(`Loaded ${file.name} successfully`);
    } catch (err) {
      console.error(err);
      showToast('Failed to read file. Please ensure it is a valid text or CSV file.');
    }
  };

  const handleFileInputChange = (e: ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      processFile(file);
    }
  };

  const handleDrop = (e: DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
    const file = e.dataTransfer.files?.[0];
    if (file) {
      processFile(file);
    }
  };

  const handleDragOver = (e: DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(true);
  };

  const handleDragLeave = (e: DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
  };

  // 1-Click Download Excel .xlsx
  const handleDownloadExcel = () => {
    if (parsedData.rows.length === 0 || parsedData.headers.length === 0) {
      showToast('No tabular data available to generate Excel spreadsheet');
      return;
    }

    try {
      const validSheetName = (sheetName.trim() || 'Sheet1').substring(0, 31);
      const blob = generateExcelXmlBlob(parsedData.headers, parsedData.rows, validSheetName);
      const exportName = (fileName.trim() || 'cora_spreadsheet_export').replace(/\.xlsx$/i, '');
      triggerBrowserDownload(blob, `${exportName}.xlsx`);
      showToast('Generated and downloaded Excel workbook (.xlsx)');
    } catch (err) {
      console.error(err);
      showToast('Failed to generate Excel file');
    }
  };

  // 1-Click Copy as TSV
  const handleCopyTsv = () => {
    if (parsedData.rows.length === 0) {
      showToast('No data to copy');
      return;
    }
    const headerLine = parsedData.headers.join('\t');
    const rowLines = parsedData.rows.map(r => r.join('\t'));
    const tsvContent = [headerLine, ...rowLines].join('\r\n');
    navigator.clipboard.writeText(tsvContent);
    showToast('Copied table as TSV to clipboard (Ready to paste in Excel / Sheets)');
  };

  // Load sample dataset
  const loadSample = (key: keyof typeof SAMPLE_CSV_DATASETS) => {
    const sample = SAMPLE_CSV_DATASETS[key];
    setRawText(sample.data);
    setSheetName(sample.sheetName);
    setFileName(sample.sheetName.toLowerCase());
    setUploadedFileName('');
    setCurrentPage(1);
    setSearchQuery('');
    showToast(`Loaded sample: ${sample.name}`);
  };

  // Reset all
  const handleReset = () => {
    setRawText('');
    setUploadedFileName('');
    setSheetName('Sheet1');
    setFileName('cora_spreadsheet_export');
    setCurrentPage(1);
    setSearchQuery('');
    if (fileInputRef.current) fileInputRef.current.value = '';
    showToast('Cleared all spreadsheet data');
  };

  const getDelimiterLabel = (d: string) => {
    switch (d) {
      case '\t': return 'Tab (\\t / TSV)';
      case ';': return 'Semicolon (;)';
      case '|': return 'Pipe (|)';
      default: return 'Comma (,)';
    }
  };

  return (
    <ToolPageShell
      toolId="csv-to-excel"
      badgeTag="Spreadsheet Engine"
      title="CSV to Excel (.xlsx) Converter"
      subtitle="Convert CSV, TSV, and delimited tables into styled Microsoft Excel spreadsheets with custom worksheet names. 100% private in-browser memory compilation."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['excel-to-csv', 'excel-to-json', 'pdf-to-excel', 'excel-to-pdf']}
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
              onClick={() => loadSample('gstInvoice')}
              className="px-3 py-1.5 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
            >
              GST Tax Ledger
            </button>
            <button
              type="button"
              onClick={() => loadSample('studioRetainers')}
              className="px-3 py-1.5 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
            >
              Retainer Accounts
            </button>
            <button
              type="button"
              onClick={() => loadSample('rateCard')}
              className="px-3 py-1.5 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
            >
              Commercial Rate Card
            </button>
          </div>

          <div className="flex items-center gap-2">
            <button
              type="button"
              onClick={handleReset}
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 transition-colors cursor-pointer"
            >
              <Trash2 className="w-3.5 h-3.5" />
              <span>Clear Data</span>
            </button>
          </div>
        </div>

        {/* ── Input Mode Selector: Upload Dropzone vs Paste Text ── */}
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
              <span>Upload CSV / TSV File</span>
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
                    Drag and drop your CSV, TSV, or TXT file here
                  </h3>
                  <p className="text-xs text-zinc-500 max-w-md mx-auto mb-4">
                    Supports comma, semicolon, tab, and pipe separated files. Max privacy: processed 100% in local memory.
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
                  <span>Paste comma, tab, or semicolon separated spreadsheet records:</span>
                  <span>{rawText.length} characters</span>
                </div>
                <textarea
                  rows={8}
                  value={rawText}
                  onChange={(e) => {
                    setRawText(e.target.value);
                    setCurrentPage(1);
                  }}
                  placeholder="Invoice_No,Client_Name,Lead_Partner,Amount&#10;INV-001,Aarav Mehta,Operations,85000&#10;INV-002,Rohan Verma,Engineering,120000"
                  className="w-full font-mono text-xs p-3.5 rounded-xl border border-zinc-200 bg-zinc-50/40 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:bg-white resize-y"
                />
              </div>
            )}
          </div>
        </div>

        {/* ── Configuration Card: Sheet Name & Export Settings ── */}
        <div className="p-5 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-4">
          <div className="flex items-center gap-2 pb-2 border-b border-zinc-100">
            <Sliders className="w-4 h-4 text-zinc-700" />
            <h3 className="text-sm font-bold text-zinc-900">Workbook & Output Configuration</h3>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-xs font-semibold text-zinc-700 mb-1.5">
                Worksheet Tab Name (Default: Sheet1)
              </label>
              <div className="relative">
                <input
                  type="text"
                  maxLength={31}
                  value={sheetName}
                  onChange={(e) => setSheetName(e.target.value)}
                  placeholder="Sheet1"
                  className="w-full px-3.5 py-2 text-xs font-mono rounded-xl border border-zinc-200 bg-zinc-50/50 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:bg-white"
                />
                <span className="absolute right-3 top-2 text-[10px] font-mono text-zinc-400">
                  {31 - sheetName.length} chars left
                </span>
              </div>
              <p className="text-[11px] text-zinc-500 mt-1">
                Name of the tab displayed inside Microsoft Excel or Google Sheets.
              </p>
            </div>

            <div>
              <label className="block text-xs font-semibold text-zinc-700 mb-1.5">
                Output File Name
              </label>
              <div className="flex items-center">
                <input
                  type="text"
                  value={fileName}
                  onChange={(e) => setFileName(e.target.value)}
                  placeholder="cora_spreadsheet_export"
                  className="flex-1 px-3.5 py-2 text-xs font-mono rounded-l-xl border border-r-0 border-zinc-200 bg-zinc-50/50 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:bg-white"
                />
                <span className="px-3 py-2 text-xs font-mono font-semibold bg-zinc-100 border border-zinc-200 rounded-r-xl text-zinc-600">
                  .xlsx
                </span>
              </div>
              <p className="text-[11px] text-zinc-500 mt-1">
                Compatible with Microsoft Excel 2007+, Apple Numbers, LibreOffice & Google Sheets.
              </p>
            </div>
          </div>
        </div>

        {/* ── Live Data Table Preview & Metrics ── */}
        <div className="p-5 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-4">
          
          {/* Header Bar with Metrics */}
          <div className="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-zinc-100">
            <div className="flex flex-wrap items-center gap-2">
              <div className="flex items-center gap-1.5 text-xs font-bold text-zinc-900">
                <Table className="w-4 h-4 text-zinc-700" />
                <span>Live Data Preview</span>
              </div>
              <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-mono font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">
                {parsedData.rowCount} {parsedData.rowCount === 1 ? 'Row' : 'Rows'}
              </span>
              <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-mono font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">
                {parsedData.colCount} Columns
              </span>
              <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-mono font-medium bg-zinc-50 text-zinc-600 border border-zinc-200">
                Delimiter: {getDelimiterLabel(parsedData.delimiter)}
              </span>
            </div>

            {/* Quick search input */}
            {parsedData.rowCount > 0 && (
              <div className="relative w-full sm:w-56">
                <Search className="w-3.5 h-3.5 text-zinc-400 absolute left-3 top-2.5" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => {
                    setSearchQuery(e.target.value);
                    setCurrentPage(1);
                  }}
                  placeholder="Search table rows..."
                  className="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-zinc-200 bg-zinc-50/50 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:bg-white"
                />
              </div>
            )}
          </div>

          {/* Table Container */}
          {parsedData.headers.length > 0 ? (
            <div className="border border-zinc-200/80 rounded-xl overflow-hidden">
              <div className="overflow-x-auto max-h-[380px] overflow-y-auto">
                <table className="w-full text-left text-xs border-collapse font-mono">
                  <thead className="sticky top-0 z-10 bg-zinc-900 text-white font-sans text-[11px] uppercase tracking-wider">
                    <tr>
                      <th className="py-2.5 px-3 w-12 text-center text-zinc-400 border-b border-zinc-800 font-mono">
                        #
                      </th>
                      {parsedData.headers.map((head, idx) => (
                        <th 
                          key={idx} 
                          className="py-2.5 px-3 font-semibold border-b border-zinc-800 whitespace-nowrap"
                        >
                          {head}
                        </th>
                      ))}
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-zinc-200/70 bg-white">
                    {displayedRows.length > 0 ? (
                      displayedRows.map((row, rIdx) => {
                        const actualRowIndex = (currentPage - 1) * rowsPerPage + rIdx + 1;
                        return (
                          <tr 
                            key={rIdx} 
                            className={rIdx % 2 === 0 ? 'bg-white hover:bg-zinc-50/80' : 'bg-zinc-50/40 hover:bg-zinc-100/60'}
                          >
                            <td className="py-2 px-3 text-center text-[10.5px] text-zinc-400 font-mono select-none">
                              {actualRowIndex}
                            </td>
                            {row.map((cell, cIdx) => (
                              <td 
                                key={cIdx} 
                                className="py-2 px-3 text-zinc-800 whitespace-nowrap text-xs truncate max-w-[240px]"
                                title={cell}
                              >
                                {cell || <span className="text-zinc-300 italic">null</span>}
                              </td>
                            ))}
                          </tr>
                        );
                      })
                    ) : (
                      <tr>
                        <td 
                          colSpan={parsedData.headers.length + 1} 
                          className="py-8 text-center text-zinc-400 text-xs"
                        >
                          No rows match your search query: "{searchQuery}"
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>

              {/* Table Pagination Footer */}
              {totalPages > 1 && (
                <div className="px-4 py-2.5 bg-zinc-50 border-t border-zinc-200 flex items-center justify-between text-xs text-zinc-600">
                  <span>
                    Showing {((currentPage - 1) * rowsPerPage) + 1} to {Math.min(currentPage * rowsPerPage, filteredRows.length)} of {filteredRows.length} rows
                  </span>
                  <div className="flex items-center gap-1.5">
                    <button
                      type="button"
                      disabled={currentPage === 1}
                      onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
                      className="p-1 rounded-md border border-zinc-200 bg-white hover:bg-zinc-100 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                    >
                      <ChevronLeft className="w-3.5 h-3.5" />
                    </button>
                    <span className="font-mono text-[11px] px-2">
                      Page {currentPage} of {totalPages}
                    </span>
                    <button
                      type="button"
                      disabled={currentPage === totalPages}
                      onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
                      className="p-1 rounded-md border border-zinc-200 bg-white hover:bg-zinc-100 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                    >
                      <ChevronRight className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              )}
            </div>
          ) : (
            <div className="py-12 text-center text-zinc-400 text-xs">
              <FileSpreadsheet className="w-8 h-8 text-zinc-300 mx-auto mb-2" />
              <p>No valid data parsed. Upload a CSV file or paste spreadsheet records above.</p>
            </div>
          )}

          {/* ── Primary Action Bar ── */}
          <div className="pt-2 flex flex-wrap items-center justify-between gap-3">
            <div className="flex items-center gap-2">
              <button
                type="button"
                onClick={handleCopyTsv}
                disabled={parsedData.rowCount === 0}
                className="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-800 font-semibold text-xs transition-colors shadow-2xs disabled:opacity-50 cursor-pointer"
              >
                <Copy className="w-3.5 h-3.5 text-zinc-600" />
                <span>Copy TSV for Sheets</span>
              </button>
            </div>

            <div className="flex items-center gap-3">
              <button
                type="button"
                onClick={handleDownloadExcel}
                disabled={parsedData.rowCount === 0}
                className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs shadow-sm transition-all active:scale-[0.99] disabled:opacity-50 cursor-pointer"
              >
                <Download className="w-4 h-4 text-white" />
                <span>Download Excel (.xlsx)</span>
              </button>
            </div>
          </div>

        </div>

      </div>
    </ToolPageShell>
  );
}
