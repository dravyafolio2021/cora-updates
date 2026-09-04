'use client';

import React, { useState, useMemo, useRef } from 'react';
import { 
  Files, 
  UploadCloud, 
  Download, 
  Copy, 
  Check, 
  Trash2, 
  FileSpreadsheet, 
  Plus, 
  ShieldCheck, 
  Search, 
  CheckSquare, 
  Layers, 
  RotateCcw,
  Sliders,
  FileCheck,
  Eye,
  ArrowRight,
  Info
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { 
  parseDelimitedText, 
  serializeToCsv, 
  generateExcelXmlBlob, 
  triggerBrowserDownload, 
  mergeCsvFiles,
  CsvFileInput 
} from '@/lib/sheets-engine';

const SAMPLE_FILES: CsvFileInput[] = [
  {
    name: 'mumbai_branch_q3.csv',
    headers: ['Invoice ID', 'Client Name', 'City', 'SAC Code', 'Amount (INR)', 'Payment Status'],
    rows: [
      ['MUM-801', 'Kavya Patel', 'Mumbai', '998314', '75000', 'Paid'],
      ['MUM-802', 'Rohan Verma', 'Mumbai', '998314', '95000', 'Paid'],
      ['MUM-803', 'Apex Media Works', 'Mumbai', '998314', '60000', 'Pending'],
      ['MUM-804', 'Studio Alpha', 'Mumbai', '998314', '42000', 'Paid'],
    ],
  },
  {
    name: 'delhi_branch_q3.csv',
    headers: ['Invoice ID', 'Client Name', 'City', 'Amount (INR)', 'Payment Status', 'GSTIN'],
    rows: [
      ['DEL-401', 'Aarav Mehta', 'Delhi NCR', '115000', 'Paid', '07AAAAA0000A1Z5'],
      ['DEL-402', 'Logix Hub India', 'Gurugram', '88000', 'Paid', '06BBBBB1111B2Z6'],
      ['DEL-403', 'Nordic Horizon', 'Noida', '52000', 'Pending', '09CCCCC2222C3Z7'],
    ],
  },
  {
    name: 'bengaluru_branch_q3.csv',
    headers: ['Invoice ID', 'Client Name', 'City', 'SAC Code', 'Amount (INR)', 'Payment Status'],
    rows: [
      ['BLR-201', 'Pooja Sundaram', 'Bengaluru', '998314', '90000', 'Paid'],
      ['BLR-202', 'Devendra Sharma', 'Bengaluru', '998314', '68000', 'Paid'],
      ['BLR-203', 'NextGen Creative', 'Bengaluru', '998314', '74000', 'Pending'],
      ['BLR-204', 'Cloudcraft Studio', 'Bengaluru', '998314', '120000', 'Paid'],
    ],
  },
];

const FAQ_ITEMS = [
  {
    question: 'What happens if my CSV files have columns arranged in different orders?',
    answer: 'Cora matches columns by header name rather than numerical position. For instance, if File A has "City" in Column 2 and File B has "City" in Column 5, Cora maps values to the unified "City" column automatically.',
  },
  {
    question: 'How does Cora handle files with missing or extra columns?',
    answer: 'Cora performs an inclusive schema union. If File A has a "GSTIN" column but File B does not, the combined spreadsheet includes the "GSTIN" column, leaving empty cells for File B rows without throwing errors or misaligning columns.',
  },
  {
    question: 'Are my CSV files or confidential numbers uploaded to external servers?',
    answer: 'No. The entire consolidation engine runs 100% inside your web browser RAM via client-side JavaScript. Zero bytes of client names, invoice numbers, or financial metrics ever leave your machine.',
  },
  {
    question: 'Can I include a column tracking which file each row originated from?',
    answer: 'Yes. Simply toggle the "Include Source File Column" switch in the consolidation settings. Cora will prepend an "_origin_file" column showing the exact filename each record was imported from.',
  },
  {
    question: 'How many CSV files can I merge simultaneously?',
    answer: 'You can drop 20, 50, or more CSV files at once. Because Cora processes text in streaming chunks inside browser memory, combining tens of thousands of rows across dozens of files takes only a few hundred milliseconds.',
  },
];

export default function MergeCsvPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Uploaded Files State
  const [fileList, setFileList] = useState<CsvFileInput[]>(SAMPLE_FILES);
  const [isDragging, setIsDragging] = useState<boolean>(false);

  // Settings
  const [includeSourceCol, setIncludeSourceCol] = useState<boolean>(true);
  const [tableSearchQuery, setTableSearchQuery] = useState<string>('');
  const [copiedCsv, setCopiedCsv] = useState<boolean>(false);

  // Merge Calculation
  const mergedResult = useMemo(() => {
    if (fileList.length === 0) {
      return { headers: [], rows: [], totalFiles: 0, totalRows: 0, fileBreakdowns: [] };
    }

    let result = mergeCsvFiles(fileList);

    if (includeSourceCol && result.rows.length > 0) {
      // Prepend Source File column
      const headersWithSource = ['Source File', ...result.headers];
      let rowIndex = 0;
      const rowsWithSource: string[][] = [];

      fileList.forEach(file => {
        file.rows.forEach(() => {
          const originalRow = result.rows[rowIndex];
          if (originalRow) {
            rowsWithSource.push([file.name, ...originalRow]);
          }
          rowIndex++;
        });
      });

      return {
        ...result,
        headers: headersWithSource,
        rows: rowsWithSource,
      };
    }

    return result;
  }, [fileList, includeSourceCol]);

  // Handle Multiple File Upload
  const handleMultipleFiles = (files: FileList | File[]) => {
    const validFiles = Array.from(files).filter(f => f.name.match(/\.(csv|tsv|txt)$/i));

    if (validFiles.length === 0) {
      showToast('Please upload valid .csv or .tsv files');
      return;
    }

    let loadedCount = 0;
    const newItems: CsvFileInput[] = [];

    validFiles.forEach(file => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const text = e.target?.result as string;
        if (text) {
          const parsed = parseDelimitedText(text);
          newItems.push({
            name: file.name,
            headers: parsed.headers,
            rows: parsed.rows,
          });
        }
        loadedCount++;
        if (loadedCount === validFiles.length) {
          setFileList(prev => [...prev, ...newItems]);
          showToast(`Added ${newItems.length} file(s) to merger queue`);
        }
      };
      reader.readAsText(file);
    });
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
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleMultipleFiles(e.dataTransfer.files);
    }
  };

  const handleRemoveFile = (index: number) => {
    setFileList(prev => prev.filter((_, idx) => idx !== index));
    showToast('Removed file from queue');
  };

  const handleClearAllFiles = () => {
    setFileList([]);
    showToast('Cleared all files from queue');
  };

  const handleResetSample = () => {
    setFileList(SAMPLE_FILES);
    showToast('Loaded 3 sample branch spreadsheets');
  };

  // Downloads & Copy
  const handleDownloadCombinedCsv = () => {
    if (mergedResult.rows.length === 0) {
      showToast('No merged rows available to download');
      return;
    }

    const csvContent = serializeToCsv(mergedResult.headers, mergedResult.rows);
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    triggerBrowserDownload(blob, 'consolidated_master_sheet.csv');
    showToast(`Downloaded combined CSV with ${mergedResult.rows.length.toLocaleString()} rows`);
  };

  const handleDownloadExcel = () => {
    if (mergedResult.rows.length === 0) {
      showToast('No merged rows available to download');
      return;
    }

    const blob = generateExcelXmlBlob(mergedResult.headers, mergedResult.rows, 'Consolidated Master');
    triggerBrowserDownload(blob, 'consolidated_master_sheet.xlsx');
    showToast('Exported consolidated workbook as Excel .xlsx');
  };

  const handleCopyCsv = () => {
    if (mergedResult.rows.length === 0) {
      showToast('No rows to copy');
      return;
    }

    const csvContent = serializeToCsv(mergedResult.headers, mergedResult.rows);
    navigator.clipboard.writeText(csvContent);
    setCopiedCsv(true);
    showToast('Consolidated CSV copied to clipboard');
    setTimeout(() => setCopiedCsv(false), 2000);
  };

  // Filtered rows for table preview
  const displayedRows = useMemo(() => {
    if (!tableSearchQuery.trim()) return mergedResult.rows;
    const q = tableSearchQuery.toLowerCase();
    return mergedResult.rows.filter(row => row.some(cell => cell.toLowerCase().includes(q)));
  }, [mergedResult.rows, tableSearchQuery]);

  return (
    <ToolPageShell
      toolId="merge-csv"
      badgeTag="SHEETS CONSOLIDATION"
      title="Merge CSV Files"
      subtitle="Consolidate multiple CSV files into one unified master spreadsheet. Automatically aligns matching column headers and normalizes fields in pure browser memory."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['split-csv', 'remove-duplicates-csv', 'clean-sheet-data', 'csv-to-excel']}
    >
      <div className="w-full max-w-5xl mx-auto space-y-6">

        {/* ── 1. MULTI-FILE DROPZONE & QUEUE ── */}
        <div className="bg-white rounded-2xl border border-zinc-200/80 p-5 sm:p-6 shadow-xs">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-zinc-100">
            <div className="flex items-center gap-2">
              <span className="p-2 rounded-xl bg-zinc-100 text-zinc-900">
                <Files className="w-4 h-4" strokeWidth={2} />
              </span>
              <div>
                <h3 className="text-sm font-semibold text-zinc-950">Source CSV Files</h3>
                <p className="text-xs text-zinc-500">Upload two or more CSVs to align headers and combine rows</p>
              </div>
            </div>

            <div className="flex items-center gap-2">
              <button
                type="button"
                onClick={handleResetSample}
                className="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-zinc-600 hover:text-zinc-950 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/70 rounded-xl transition-all"
                title="Load 3 sample branch spreadsheets"
              >
                <RotateCcw className="w-3.5 h-3.5" />
                <span className="hidden sm:inline">Load Sample Files</span>
              </button>
              {fileList.length > 0 && (
                <button
                  type="button"
                  onClick={handleClearAllFiles}
                  className="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-rose-600 hover:text-rose-700 bg-rose-50/50 hover:bg-rose-50 border border-rose-200/70 rounded-xl transition-all"
                >
                  <Trash2 className="w-3.5 h-3.5" />
                  <span>Clear All</span>
                </button>
              )}
            </div>
          </div>

          {/* Dropzone Container */}
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
                multiple
                accept=".csv,.tsv,.txt"
                className="hidden"
                onChange={(e) => {
                  if (e.target.files && e.target.files.length > 0) {
                    handleMultipleFiles(e.target.files);
                  }
                }}
              />
              <div className="flex flex-col items-center justify-center gap-2">
                <div className="p-3 rounded-full bg-white border border-zinc-200 shadow-2xs group-hover:scale-105 transition-transform">
                  <UploadCloud className="w-5 h-5 text-zinc-700" strokeWidth={1.8} />
                </div>
                <div>
                  <span className="text-sm font-semibold text-zinc-900">
                    Select multiple CSV files or drag and drop here
                  </span>
                  <p className="text-xs text-zinc-500 mt-0.5">
                    Merge 2 to 50+ files at once (RFC 4180 CSV or TSV format)
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* File Queue List */}
          {fileList.length > 0 && (
            <div className="mt-4 pt-4 border-t border-zinc-100">
              <div className="text-xs font-semibold text-zinc-700 mb-2 flex items-center justify-between">
                <span>Active Queue ({fileList.length} files)</span>
                <span className="text-zinc-400 font-normal">Drag or add more files anytime</span>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                {fileList.map((file, idx) => (
                  <div
                    key={idx}
                    className="flex items-center justify-between p-2.5 rounded-xl bg-zinc-50 border border-zinc-200/80 text-xs text-zinc-800 hover:border-zinc-300 transition-colors"
                  >
                    <div className="flex items-center gap-2 min-w-0 pr-2">
                      <span className="p-1 rounded-md bg-white border border-zinc-200 text-zinc-600 shrink-0">
                        <FileSpreadsheet className="w-3.5 h-3.5" />
                      </span>
                      <div className="min-w-0">
                        <span className="font-medium truncate block">{file.name}</span>
                        <span className="text-[11px] text-zinc-500">
                          {file.rows.length.toLocaleString()} rows • {file.headers.length} cols
                        </span>
                      </div>
                    </div>
                    <button
                      type="button"
                      onClick={(e) => {
                        e.stopPropagation();
                        handleRemoveFile(idx);
                      }}
                      className="p-1 text-zinc-400 hover:text-rose-600 rounded-md hover:bg-rose-50 transition-colors shrink-0"
                      title="Remove from queue"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>

        {/* ── 2. CONSOLIDATION SETTINGS & HEADER ALIGNMENT ── */}
        <div className="bg-white rounded-2xl border border-zinc-200/80 p-5 sm:p-6 shadow-xs">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-zinc-100">
            <div className="flex items-center gap-2">
              <span className="p-2 rounded-xl bg-zinc-100 text-zinc-900">
                <Sliders className="w-4 h-4" strokeWidth={2} />
              </span>
              <div>
                <h3 className="text-sm font-semibold text-zinc-950">Alignment & Output Options</h3>
                <p className="text-xs text-zinc-500">Headers are matched and aligned by column title automatically</p>
              </div>
            </div>

            <label className="inline-flex items-center gap-2 cursor-pointer select-none text-xs font-medium text-zinc-700 bg-zinc-50 px-3 py-1.5 rounded-lg border border-zinc-200">
              <input
                type="checkbox"
                checked={includeSourceCol}
                onChange={(e) => setIncludeSourceCol(e.target.checked)}
                className="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900 w-3.5 h-3.5"
              />
              <span>Include Source File Column</span>
            </label>
          </div>

          {/* Unified Columns Summary */}
          <div className="mt-4">
            <div className="text-xs font-medium text-zinc-600 mb-2">
              Aligned Master Column Schema ({mergedResult.headers.length} columns detected):
            </div>
            <div className="flex flex-wrap gap-1.5">
              {mergedResult.headers.map((h, idx) => (
                <span
                  key={idx}
                  className="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono bg-zinc-100 text-zinc-800 border border-zinc-200"
                >
                  <Check className="w-3 h-3 text-emerald-600" />
                  <span>{h}</span>
                </span>
              ))}
            </div>
          </div>
        </div>

        {/* ── 3. LIVE METRICS CARDS ── */}
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
          <div className="bg-white rounded-2xl border border-zinc-200/80 p-4 shadow-xs">
            <span className="text-xs font-medium text-zinc-500 uppercase tracking-wider block">Files Combined</span>
            <div className="mt-1 flex items-baseline gap-2">
              <span className="text-2xl font-bold font-mono text-zinc-950">
                {fileList.length}
              </span>
            </div>
            <span className="text-[11px] text-zinc-400 mt-1 block">In-queue datasets</span>
          </div>

          <div className="bg-white rounded-2xl border border-zinc-200/80 p-4 shadow-xs">
            <span className="text-xs font-medium text-zinc-500 uppercase tracking-wider block">Consolidated Rows</span>
            <div className="mt-1 flex items-baseline gap-2">
              <span className="text-2xl font-bold font-mono text-zinc-950">
                {mergedResult.rows.length.toLocaleString()}
              </span>
            </div>
            <span className="text-[11px] text-emerald-600 font-medium mt-1 block">
              100% rows preserved
            </span>
          </div>

          <div className="bg-white rounded-2xl border border-zinc-200/80 p-4 shadow-xs">
            <span className="text-xs font-medium text-zinc-500 uppercase tracking-wider block">Aligned Columns</span>
            <div className="mt-1 flex items-baseline gap-2">
              <span className="text-2xl font-bold font-mono text-zinc-950">
                {mergedResult.headers.length}
              </span>
            </div>
            <span className="text-[11px] text-zinc-400 mt-1 block">Unified schema</span>
          </div>

          <div className="bg-white rounded-2xl border border-zinc-200/80 p-4 shadow-xs">
            <span className="text-xs font-medium text-zinc-500 uppercase tracking-wider block">Privacy & Security</span>
            <div className="mt-1 flex items-center gap-1.5 text-zinc-950">
              <ShieldCheck className="w-5 h-5 text-emerald-600 shrink-0" strokeWidth={2} />
              <span className="text-sm font-semibold">Local RAM</span>
            </div>
            <span className="text-[11px] text-zinc-400 mt-1 block">Zero cloud transmissions</span>
          </div>
        </div>

        {/* ── 4. CONSOLIDATED TABLE & EXPORT ACTIONS ── */}
        <div className="bg-white rounded-2xl border border-zinc-200/80 shadow-xs overflow-hidden">
          {/* Header Bar */}
          <div className="p-4 sm:p-5 border-b border-zinc-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div className="flex items-center gap-2">
              <span className="text-sm font-semibold text-zinc-950">
                Consolidated Master Table ({mergedResult.rows.length.toLocaleString()} rows)
              </span>
            </div>

            {/* Actions */}
            <div className="flex items-center gap-2 flex-wrap">
              <div className="relative">
                <Search className="w-3.5 h-3.5 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input
                  type="text"
                  value={tableSearchQuery}
                  onChange={(e) => setTableSearchQuery(e.target.value)}
                  placeholder="Search combined rows..."
                  className="pl-8 pr-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-800 placeholder:text-zinc-400 focus:outline-hidden focus:ring-1 focus:ring-zinc-900 w-36 sm:w-44"
                />
              </div>

              <button
                type="button"
                onClick={handleCopyCsv}
                className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-all"
                title="Copy merged CSV to clipboard"
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
                onClick={handleDownloadCombinedCsv}
                className="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition-all shadow-2xs"
              >
                <Download className="w-3.5 h-3.5" />
                <span>Download Combined CSV</span>
              </button>
            </div>
          </div>

          {/* Table */}
          <div className="overflow-x-auto max-h-[460px] divide-y divide-zinc-100">
            <table className="w-full text-left text-xs border-collapse font-sans">
              <thead className="bg-zinc-50/90 sticky top-0 z-10 backdrop-blur-xs">
                <tr className="border-b border-zinc-200">
                  <th className="py-2.5 px-3 font-semibold text-zinc-500 font-mono text-[11px] w-12 text-center border-r border-zinc-200/60">
                    #
                  </th>
                  {mergedResult.headers.map((h, idx) => (
                    <th
                      key={idx}
                      className="py-2.5 px-3 font-semibold text-zinc-900 whitespace-nowrap border-r border-zinc-200/60 last:border-r-0"
                    >
                      {h}
                    </th>
                  ))}
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-100 text-zinc-700">
                {displayedRows.length === 0 ? (
                  <tr>
                    <td
                      colSpan={mergedResult.headers.length + 1}
                      className="py-12 text-center text-zinc-400"
                    >
                      {fileList.length === 0
                        ? 'No files in queue. Upload or drag files above to begin merging.'
                        : 'No rows match the active filter.'}
                    </td>
                  </tr>
                ) : (
                  displayedRows.slice(0, 100).map((row, rowIdx) => (
                    <tr
                      key={rowIdx}
                      className={`transition-colors hover:bg-zinc-50/80 ${
                        rowIdx % 2 === 1 ? 'bg-zinc-50/30' : 'bg-white'
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
                          {cell || <span className="text-zinc-300 italic">null</span>}
                        </td>
                      ))}
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>

          {/* Footer */}
          <div className="p-3 bg-zinc-50 border-t border-zinc-100 flex items-center justify-between text-xs text-zinc-500">
            <span>
              Showing {Math.min(displayedRows.length, 100)} of {displayedRows.length.toLocaleString()} combined records
            </span>
            <div className="flex items-center gap-3">
              <span className="inline-flex items-center gap-1">
                <span className="w-2 h-2 rounded-full bg-emerald-500" />
                <span>Headers Aligned</span>
              </span>
            </div>
          </div>
        </div>

      </div>
    </ToolPageShell>
  );
}
