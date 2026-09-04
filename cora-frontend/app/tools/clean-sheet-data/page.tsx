'use client';

import React, { useState, useRef, useMemo, useCallback } from 'react';
import { 
  UploadCloud, 
  Download, 
  Copy, 
  Check, 
  RotateCcw, 
  FileSpreadsheet, 
  Table, 
  Sparkles, 
  ShieldCheck, 
  CheckCircle2, 
  FileCheck, 
  Trash2, 
  RefreshCw, 
  Sliders, 
  Eye, 
  Layers, 
  Filter, 
  Calendar, 
  Phone, 
  Type, 
  Scissors, 
  FileText,
  AlertCircle
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { 
  parseDelimitedText, 
  cleanSheetCells, 
  serializeToCsv, 
  generateExcelXmlBlob, 
  triggerBrowserDownload,
  ParsedSheetData 
} from '@/lib/sheets-engine';

interface CleaningOptions {
  trimWhitespace: boolean;
  standardizePhoneNumbers: boolean;
  formatDatesToIso: boolean;
  capitalizeNames: boolean;
  removeEmptyRows: boolean;
}

const SAMPLE_MESSY_CSV = `Full Name,Contact Phone,Onboarding Date,Designation,Account Status
  rohan   verma  ,9820192831,14/08/2026,  fullstack   engineer  ,ACTIVE
kavya patel,+919811234567,05-09-2026,LEGAL ADVISOR,ACTIVE
    ,   ,   ,   ,   
aarav mehta,98200 11223,28.07.2026,FINANCE LEAD,ACTIVE
vikram malhotra,919988776655,01/06/2026,design system lead,PENDING
    ,   ,   ,   ,   
neha sharma,9876543210,12/12/2025,brand strategist,ACTIVE`;

const FAQS = [
  {
    question: 'Is my sensitive customer, lead, or financial data uploaded to external cloud servers?',
    answer: 'No. The entire data parsing, regex matching, date normalization, and CSV re-serialization executes 100% in local browser memory (RAM) via client-side JavaScript. Zero bytes are ever transmitted over the network or stored in databases.',
  },
  {
    question: 'How does the Indian phone number standardizer format telephone numbers?',
    answer: 'The standardizer strips all non-numeric characters (hyphens, spaces, brackets). For 10-digit Indian mobile numbers or 12-digit numbers starting with 91, it formats them into standard E.164-compliant national display strings: "+91 XXXXX XXXXX". Numbers that do not match 10 or 12 digits are preserved safely without corruption.',
  },
  {
    question: 'Which date formats are recognized and converted to standard ISO YYYY-MM-DD?',
    answer: 'The date engine recognizes standard DD/MM/YYYY, DD-MM-YYYY, and DD.MM.YYYY formats commonly generated in Indian accounting ledgers, banking exports, and CRM rosters, converting them into universal ISO 8601 strings (YYYY-MM-DD) suitable for SQL databases and modern spreadsheet sorting.',
  },
  {
    question: 'Does the name capitalizer handle multi-word names and email addresses?',
    answer: 'Yes. The name sanitizer applies Title Case word capitalization to alphabetical names while automatically ignoring email addresses (strings containing "@") and purely numerical identifiers.',
  },
  {
    question: 'Can I copy the cleaned data directly back into Google Sheets or Microsoft Excel?',
    answer: 'Yes. You can click "Copy CSV" to copy the sanitized RFC 4180 data to your clipboard, or click "Download Excel (.xls)" to export an immediate native workbook XML file that opens directly in desktop Excel without import wizards.',
  },
];

export default function CleanSheetDataPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Raw State
  const [rawText, setRawText] = useState<string>(SAMPLE_MESSY_CSV);
  const [inputMode, setInputMode] = useState<'preview' | 'paste'>('preview');
  const [isDragging, setIsDragging] = useState<boolean>(false);
  const [viewTab, setViewTab] = useState<'after' | 'before'>('after');
  const [copiedCsv, setCopiedCsv] = useState<boolean>(false);

  // Cleaning Checkbox Options
  const [options, setOptions] = useState<CleaningOptions>({
    trimWhitespace: true,
    standardizePhoneNumbers: true,
    formatDatesToIso: true,
    capitalizeNames: true,
    removeEmptyRows: true,
  });

  // Parse Raw Sheet Data
  const parsedBefore: ParsedSheetData = useMemo(() => {
    return parseDelimitedText(rawText);
  }, [rawText]);

  // Clean Sheet Data Reactively
  const { cleanedRows, changesCount } = useMemo(() => {
    if (parsedBefore.headers.length === 0) {
      return { cleanedRows: [], changesCount: 0 };
    }
    return cleanSheetCells(parsedBefore.headers, parsedBefore.rows, options);
  }, [parsedBefore, options]);

  // Handle File Upload
  const handleFileUpload = async (file: File) => {
    try {
      const text = await file.text();
      setRawText(text);
      setInputMode('preview');
      showToast(`Loaded ${file.name} (${(file.size / 1024).toFixed(1)} KB)`);
    } catch (err) {
      console.error(err);
      showToast('Error reading spreadsheet file');
    }
  };

  const handleDrop = useCallback((e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDragging(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFileUpload(e.dataTransfer.files[0]);
    }
  }, []);

  const handleDragOver = useCallback((e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDragging(true);
  }, []);

  const handleDragLeave = useCallback((e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDragging(false);
  }, []);

  // Download Cleaned CSV
  const handleDownloadCsv = () => {
    if (cleanedRows.length === 0 || parsedBefore.headers.length === 0) {
      showToast('No cleaned data to export');
      return;
    }
    const csvContent = serializeToCsv(parsedBefore.headers, cleanedRows);
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    triggerBrowserDownload(blob, 'cora_cleaned_data.csv');
    showToast('Downloaded sanitized CSV file');
  };

  // Download Cleaned Excel
  const handleDownloadExcel = () => {
    if (cleanedRows.length === 0 || parsedBefore.headers.length === 0) {
      showToast('No cleaned data to export');
      return;
    }
    const blob = generateExcelXmlBlob(parsedBefore.headers, cleanedRows, 'CleanedData');
    triggerBrowserDownload(blob, 'cora_cleaned_data.xls');
    showToast('Downloaded native Excel workbook');
  };

  // Copy Cleaned CSV
  const handleCopyCleaned = () => {
    if (cleanedRows.length === 0 || parsedBefore.headers.length === 0) {
      showToast('No cleaned data to copy');
      return;
    }
    const csvContent = serializeToCsv(parsedBefore.headers, cleanedRows);
    if (typeof window !== 'undefined') {
      navigator.clipboard.writeText(csvContent);
      setCopiedCsv(true);
      setTimeout(() => setCopiedCsv(false), 2000);
      showToast('Cleaned CSV copied to clipboard!');
    }
  };

  const handleLoadSample = () => {
    setRawText(SAMPLE_MESSY_CSV);
    setInputMode('preview');
    showToast('Loaded messy CRM sample dataset');
  };

  const handleClear = () => {
    setRawText('');
    showToast('Cleared all spreadsheet data');
  };

  return (
    <ToolPageShell
      toolId="clean-sheet-data"
      badgeTag="In-Browser Sheet Sanitizer"
      title="Clean & Standardize Spreadsheet Data Online Free"
      subtitle="Trim irregular spaces, format Indian phone numbers (+91), standardize ISO dates, title-case names, and purge empty rows directly in browser memory."
      faqItems={FAQS}
      relatedToolSlugs={['excel-formula-generator', 'vlookup-generator', 'excel-to-pdf', 'pdf-to-excel']}
    >
      <div className="space-y-8">

        {/* Top Control Bar Card */}
        <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 pb-6 border-b border-zinc-100">
            <div>
              <div className="flex items-center gap-2 mb-2">
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <ShieldCheck className="w-3.5 h-3.5 text-zinc-700" />
                  100% In-Browser Memory
                </span>
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <FileCheck className="w-3.5 h-3.5 text-zinc-700" />
                  RFC 4180 Serialization
                </span>
              </div>
              <h2 className="text-xl sm:text-2xl font-semibold tracking-tight text-zinc-900">
                Data Sanitization & Format Normalizer
              </h2>
              <p className="text-sm text-zinc-600 mt-1 max-w-2xl">
                Upload or paste messy CRM contacts, lead sheets, or financial exports. Toggle cleaning rules to standardize your rows instantly.
              </p>
            </div>

            <div className="flex flex-wrap items-center gap-3">
              <button
                type="button"
                onClick={handleLoadSample}
                className="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
              >
                <Sparkles className="w-3.5 h-3.5 text-zinc-600" />
                Load Messy Sample
              </button>

              <button
                type="button"
                onClick={() => fileInputRef.current?.click()}
                className="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
              >
                <UploadCloud className="w-3.5 h-3.5 text-zinc-600" />
                Upload CSV / TSV
              </button>
              <input
                ref={fileInputRef}
                type="file"
                accept=".csv,.tsv,.txt"
                className="hidden"
                onChange={(e) => {
                  if (e.target.files && e.target.files[0]) {
                    handleFileUpload(e.target.files[0]);
                  }
                }}
              />

              <button
                type="button"
                onClick={handleClear}
                className="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-zinc-500 hover:text-zinc-800 transition-colors"
              >
                <Trash2 className="w-3.5 h-3.5" />
                Clear
              </button>
            </div>
          </div>

          {/* Mode Switcher & Drag-and-Drop Area */}
          <div className="mt-6">
            <div className="flex items-center gap-2 mb-3">
              <button
                type="button"
                onClick={() => setInputMode('preview')}
                className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-all ${
                  inputMode === 'preview'
                    ? 'bg-zinc-900 text-white'
                    : 'bg-zinc-100 text-zinc-600 hover:text-zinc-900'
                }`}
              >
                Data Dropzone & File Status
              </button>
              <button
                type="button"
                onClick={() => setInputMode('paste')}
                className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-all ${
                  inputMode === 'paste'
                    ? 'bg-zinc-900 text-white'
                    : 'bg-zinc-100 text-zinc-600 hover:text-zinc-900'
                }`}
              >
                Paste Raw Spreadsheet Rows
              </button>
            </div>

            {inputMode === 'preview' ? (
              <div
                onDrop={handleDrop}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onClick={() => fileInputRef.current?.click()}
                className={`border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-all ${
                  isDragging
                    ? 'border-zinc-900 bg-zinc-100'
                    : 'border-zinc-200 hover:border-zinc-400 bg-zinc-50/50'
                }`}
              >
                <div className="w-10 h-10 rounded-full bg-zinc-100 flex items-center justify-center mx-auto mb-2 text-zinc-600">
                  <UploadCloud className="w-5 h-5" />
                </div>
                <div className="text-xs font-semibold text-zinc-900">
                  Drop your CSV or TSV file here, or click to browse
                </div>
                <div className="text-[11px] text-zinc-500 mt-1">
                  Supports comma, semicolon, tab, or pipe delimiters up to 50,000 rows
                </div>
              </div>
            ) : (
              <div>
                <textarea
                  rows={5}
                  value={rawText}
                  onChange={(e) => setRawText(e.target.value)}
                  placeholder="Paste delimited rows directly from Excel or Google Sheets here..."
                  className="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 focus:border-zinc-900 focus:bg-white rounded-xl text-xs font-mono text-zinc-900 placeholder:text-zinc-400 focus:outline-none transition-all resize-y"
                />
              </div>
            )}
          </div>

          {/* Checkbox Cleaning Options Matrix */}
          <div className="mt-6 pt-6 border-t border-zinc-100">
            <div className="text-xs font-semibold text-zinc-900 uppercase tracking-wider mb-3 flex items-center gap-2">
              <Sliders className="w-3.5 h-3.5 text-zinc-700" />
              Active Sanitization Rules:
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              
              {/* Option 1: Trim Whitespace */}
              <label className="flex items-start gap-2.5 p-3 rounded-xl border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-50 cursor-pointer transition-colors">
                <input
                  type="checkbox"
                  checked={options.trimWhitespace}
                  onChange={(e) => setOptions({ ...options, trimWhitespace: e.target.checked })}
                  className="mt-0.5 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900"
                />
                <div className="text-xs">
                  <div className="font-semibold text-zinc-900 flex items-center gap-1">
                    <Scissors className="w-3 h-3 text-zinc-600" />
                    Trim Whitespace
                  </div>
                  <div className="text-zinc-500 text-[11px] mt-0.5">
                    Collapse multi-spaces and strip leading/trailing spaces
                  </div>
                </div>
              </label>

              {/* Option 2: Standardize Phone Numbers */}
              <label className="flex items-start gap-2.5 p-3 rounded-xl border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-50 cursor-pointer transition-colors">
                <input
                  type="checkbox"
                  checked={options.standardizePhoneNumbers}
                  onChange={(e) => setOptions({ ...options, standardizePhoneNumbers: e.target.checked })}
                  className="mt-0.5 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900"
                />
                <div className="text-xs">
                  <div className="font-semibold text-zinc-900 flex items-center gap-1">
                    <Phone className="w-3 h-3 text-zinc-600" />
                    Standardize Phones (+91)
                  </div>
                  <div className="text-zinc-500 text-[11px] mt-0.5">
                    Format 10-digit Indian numbers to +91 XXXXX XXXXX
                  </div>
                </div>
              </label>

              {/* Option 3: Convert Dates to ISO */}
              <label className="flex items-start gap-2.5 p-3 rounded-xl border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-50 cursor-pointer transition-colors">
                <input
                  type="checkbox"
                  checked={options.formatDatesToIso}
                  onChange={(e) => setOptions({ ...options, formatDatesToIso: e.target.checked })}
                  className="mt-0.5 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900"
                />
                <div className="text-xs">
                  <div className="font-semibold text-zinc-900 flex items-center gap-1">
                    <Calendar className="w-3 h-3 text-zinc-600" />
                    Format Dates to ISO
                  </div>
                  <div className="text-zinc-500 text-[11px] mt-0.5">
                    Convert DD/MM/YYYY or DD-MM-YYYY to YYYY-MM-DD
                  </div>
                </div>
              </label>

              {/* Option 4: Capitalize Names */}
              <label className="flex items-start gap-2.5 p-3 rounded-xl border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-50 cursor-pointer transition-colors">
                <input
                  type="checkbox"
                  checked={options.capitalizeNames}
                  onChange={(e) => setOptions({ ...options, capitalizeNames: e.target.checked })}
                  className="mt-0.5 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900"
                />
                <div className="text-xs">
                  <div className="font-semibold text-zinc-900 flex items-center gap-1">
                    <Type className="w-3 h-3 text-zinc-600" />
                    Title-Case Names
                  </div>
                  <div className="text-zinc-500 text-[11px] mt-0.5">
                    Capitalize full names while preserving email addresses
                  </div>
                </div>
              </label>

              {/* Option 5: Remove Empty Rows */}
              <label className="flex items-start gap-2.5 p-3 rounded-xl border border-zinc-200 bg-zinc-50/50 hover:bg-zinc-50 cursor-pointer transition-colors">
                <input
                  type="checkbox"
                  checked={options.removeEmptyRows}
                  onChange={(e) => setOptions({ ...options, removeEmptyRows: e.target.checked })}
                  className="mt-0.5 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900"
                />
                <div className="text-xs">
                  <div className="font-semibold text-zinc-900 flex items-center gap-1">
                    <Filter className="w-3 h-3 text-zinc-600" />
                    Purge Empty Ghost Rows
                  </div>
                  <div className="text-zinc-500 text-[11px] mt-0.5">
                    Eliminate blank spreadsheet lines and phantom commas
                  </div>
                </div>
              </label>

            </div>
          </div>
        </div>

        {/* Live Before & After Table Preview */}
        <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
          
          {/* Header & Metrics Bar */}
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-zinc-100">
            <div>
              <div className="flex items-center gap-3">
                <h3 className="text-base font-semibold text-zinc-900">
                  Interactive Live Data Preview
                </h3>
                <span className="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                  {changesCount} Modifications Applied
                </span>
              </div>
              <p className="text-xs text-zinc-500 mt-0.5">
                Rows: <strong className="text-zinc-800">{cleanedRows.length}</strong> (Original: {parsedBefore.rows.length}) • Columns: <strong className="text-zinc-800">{parsedBefore.headers.length}</strong>
              </p>
            </div>

            {/* View Switcher: Before vs After */}
            <div className="flex items-center gap-2 bg-zinc-100 p-1 rounded-xl border border-zinc-200">
              <button
                type="button"
                onClick={() => setViewTab('after')}
                className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-all ${
                  viewTab === 'after'
                    ? 'bg-white text-zinc-900 shadow-sm border border-zinc-200'
                    : 'text-zinc-600 hover:text-zinc-900'
                }`}
              >
                Cleaned Data Preview
              </button>
              <button
                type="button"
                onClick={() => setViewTab('before')}
                className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-all ${
                  viewTab === 'before'
                    ? 'bg-white text-zinc-900 shadow-sm border border-zinc-200'
                    : 'text-zinc-600 hover:text-zinc-900'
                }`}
              >
                Original Raw Data
              </button>
            </div>
          </div>

          {/* Table Container */}
          <div className="mt-5 border border-zinc-200 rounded-xl overflow-hidden">
            {parsedBefore.headers.length === 0 ? (
              <div className="p-12 text-center text-zinc-400 text-xs">
                No spreadsheet data detected. Paste rows or upload a file above.
              </div>
            ) : (
              <div className="overflow-x-auto max-h-[420px]">
                <table className="w-full text-left text-xs border-collapse">
                  <thead className="sticky top-0 bg-zinc-900 text-white z-10 shadow-sm">
                    <tr>
                      <th className="px-3.5 py-2.5 font-mono text-[11px] text-zinc-400 w-12 border-r border-zinc-800 text-center">
                        #
                      </th>
                      {parsedBefore.headers.map((head, idx) => (
                        <th key={idx} className="px-4 py-2.5 font-semibold text-zinc-100 border-r border-zinc-800 last:border-r-0 whitespace-nowrap">
                          {head}
                        </th>
                      ))}
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-zinc-100">
                    {(viewTab === 'after' ? cleanedRows : parsedBefore.rows).map((row, rIdx) => (
                      <tr key={rIdx} className="hover:bg-zinc-50/70 transition-colors">
                        <td className="px-3.5 py-2 text-center font-mono text-[11px] text-zinc-400 bg-zinc-50/50 border-r border-zinc-100">
                          {rIdx + 1}
                        </td>
                        {row.map((cell, cIdx) => (
                          <td key={cIdx} className="px-4 py-2 text-zinc-800 border-r border-zinc-100 last:border-r-0 whitespace-nowrap font-mono text-xs">
                            {cell || <span className="text-zinc-300 italic">(blank)</span>}
                          </td>
                        ))}
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>

          {/* Action Export Bar */}
          <div className="mt-6 pt-5 border-t border-zinc-100 flex flex-wrap items-center justify-between gap-4">
            <div className="text-xs text-zinc-500">
              Ready for CRM import, database seeding, or spreadsheet presentation.
            </div>

            <div className="flex flex-wrap items-center gap-3">
              <button
                type="button"
                onClick={handleCopyCleaned}
                className="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-zinc-800 bg-zinc-100 hover:bg-zinc-200 rounded-xl transition-all active:scale-95"
              >
                {copiedCsv ? (
                  <>
                    <Check className="w-3.5 h-3.5 text-emerald-600" />
                    <span>Copied Cleaned CSV!</span>
                  </>
                ) : (
                  <>
                    <Copy className="w-3.5 h-3.5" />
                    <span>Copy Cleaned CSV</span>
                  </>
                )}
              </button>

              <button
                type="button"
                onClick={handleDownloadExcel}
                className="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-zinc-800 bg-zinc-100 hover:bg-zinc-200 rounded-xl transition-all active:scale-95"
              >
                <FileSpreadsheet className="w-3.5 h-3.5 text-emerald-600" />
                <span>Download Excel (.xls)</span>
              </button>

              <button
                type="button"
                onClick={handleDownloadCsv}
                className="inline-flex items-center gap-2 px-5 py-2 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition-all shadow-sm active:scale-95"
              >
                <Download className="w-3.5 h-3.5" />
                <span>Download Cleaned CSV</span>
              </button>
            </div>
          </div>

        </div>

      </div>
    </ToolPageShell>
  );
}
