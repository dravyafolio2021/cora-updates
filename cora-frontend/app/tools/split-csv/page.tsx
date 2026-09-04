'use client';

import React, { useState, useMemo, useRef } from 'react';
import { 
  Scissors, 
  UploadCloud, 
  Download, 
  FileSpreadsheet, 
  Layers, 
  ShieldCheck, 
  RotateCcw, 
  FileCheck, 
  Sliders, 
  Check, 
  ChevronRight, 
  Eye, 
  FileDown, 
  Hash, 
  ListFilter,
  Sparkles
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { 
  parseDelimitedText, 
  serializeToCsv, 
  triggerBrowserDownload, 
  splitCsvByRowCount, 
  splitCsvByColumnValue,
  SplitCsvChunk 
} from '@/lib/sheets-engine';

const SAMPLE_CSV_DATA = `Invoice ID,Client Name,Category,City,Gross Amount (INR),Payment Status
INV-2026-01,Rohan Verma,Creative Retainer,Mumbai,85000,Paid
INV-2026-02,Kavya Patel,Brand Architecture,Bengaluru,120000,Paid
INV-2026-03,Aarav Mehta,Legal Advisory,Delhi NCR,95000,Pending
INV-2026-04,Pooja Sundaram,Video Production,Chennai,60000,Paid
INV-2026-05,Devendra Sharma,Creative Retainer,Mumbai,75000,Overdue
INV-2026-06,Ananya Iyer,Brand Architecture,Bengaluru,110000,Paid
INV-2026-07,Vikram Malhotra,Legal Advisory,Delhi NCR,88000,Paid
INV-2026-08,Siddharth Rao,Video Production,Pune,54000,Pending
INV-2026-09,Neha Deshmukh,Creative Retainer,Mumbai,92000,Paid
INV-2026-10,Rahul Bansal,Brand Architecture,Hyderabad,105000,Overdue
INV-2026-11,Meera Nambiar,Video Production,Chennai,68000,Paid
INV-2026-12,Aditya Sengupta,Legal Advisory,Kolkata,78000,Paid`;

const FAQ_ITEMS = [
  {
    question: 'Does splitting by column value preserve the header in each resulting file?',
    answer: 'Yes. Every generated file contains the exact same original header row at Line 1, ensuring that each partition is a fully valid, standalone CSV ready for immediate import into databases, CRMs, or Excel.',
  },
  {
    question: 'How do I split a large dataset when Excel hits its 1,048,576 row limit?',
    answer: 'Cora bypasses Excel desktop limits completely because it runs client-side in browser RAM without GUI grid constraints. You can split a 2,000,000-row database export into four 500,000-row chunks in under 3 seconds.',
  },
  {
    question: 'Is my data or customer list uploaded to external cloud servers?',
    answer: 'No. The entire partitioning routine executes 100% in your local browser memory via JavaScript. Zero bytes of confidential figures or personal records are ever transmitted over the network.',
  },
  {
    question: 'How does splitting by unique column value name the output files?',
    answer: 'Cora automatically creates descriptive filenames using your base file name, the selected column header, and the sanitized category value (for example: "invoices_city_mumbai.csv" or "invoices_city_delhi_ncr.csv").',
  },
  {
    question: 'Can I split tab-delimited (TSV) or semicolon-delimited CSV files?',
    answer: 'Yes. Cora automatically detects commas, semicolons, tabs, and pipes in the input stream, aligns the tabular columns, and exports standardized RFC 4180 CSV files.',
  },
];

export default function SplitCsvPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Input State
  const [activeTab, setActiveTab] = useState<'upload' | 'paste'>('upload');
  const [pastedRawText, setPastedRawText] = useState<string>('');
  const [rawText, setRawText] = useState<string>(SAMPLE_CSV_DATA);
  const [fileName, setFileName] = useState<string>('cora_commercial_invoices.csv');
  const [isDragging, setIsDragging] = useState<boolean>(false);

  // Split Configuration State
  const [splitMode, setSplitMode] = useState<'row_count' | 'column_value'>('row_count');
  const [rowsPerChunk, setRowsPerChunk] = useState<number>(4);
  const [selectedColIndex, setSelectedColIndex] = useState<number>(3); // Default to 'City' (index 3)

  // Preview Chunk Modal / Accordion State
  const [previewChunkIndex, setPreviewChunkIndex] = useState<number | null>(0);
  const [isDownloadingAll, setIsDownloadingAll] = useState<boolean>(false);

  // Parse input
  const parsedData = useMemo(() => {
    return parseDelimitedText(rawText);
  }, [rawText]);

  // Generate Chunks
  const chunks: SplitCsvChunk[] = useMemo(() => {
    if (parsedData.headers.length === 0 || parsedData.rows.length === 0) {
      return [];
    }

    if (splitMode === 'row_count') {
      return splitCsvByRowCount(
        parsedData.headers,
        parsedData.rows,
        Math.max(1, rowsPerChunk),
        fileName
      );
    } else {
      const validColIdx = selectedColIndex >= 0 && selectedColIndex < parsedData.headers.length
        ? selectedColIndex
        : 0;
      return splitCsvByColumnValue(
        parsedData.headers,
        parsedData.rows,
        validColIdx,
        fileName
      );
    }
  }, [parsedData, splitMode, rowsPerChunk, selectedColIndex, fileName]);

  // File Upload Handlers
  const handleFileUpload = (file: File) => {
    if (!file.name.match(/\.(csv|tsv|txt)$/i)) {
      showToast('Please upload a valid .csv or .tsv file');
      return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
      const text = e.target?.result as string;
      if (text) {
        setRawText(text);
        setFileName(file.name);
        const parsed = parseDelimitedText(text);
        if (parsed.headers.length > 0) {
          setSelectedColIndex(0);
        }
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
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFileUpload(e.dataTransfer.files[0]);
    }
  };

  const handleApplyPasted = () => {
    if (!pastedRawText.trim()) {
      showToast('Please paste spreadsheet text or rows first');
      return;
    }
    setRawText(pastedRawText);
    setFileName('pasted_split_data.csv');
    const parsed = parseDelimitedText(pastedRawText);
    if (parsed.headers.length > 0) {
      setSelectedColIndex(0);
    }
    showToast(`Parsed ${parsed.rowCount.toLocaleString()} rows successfully`);
  };

  const handleResetSample = () => {
    setRawText(SAMPLE_CSV_DATA);
    setFileName('cora_commercial_invoices.csv');
    setRowsPerChunk(4);
    setSelectedColIndex(3);
    showToast('Loaded sample dataset');
  };

  // Download Individual Chunk
  const handleDownloadChunk = (chunk: SplitCsvChunk) => {
    const csvContent = serializeToCsv(chunk.headers, chunk.rows);
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    triggerBrowserDownload(blob, chunk.filename);
    showToast(`Downloaded ${chunk.filename}`);
  };

  // Download All Chunks Sequentially
  const handleDownloadAllChunks = async () => {
    if (chunks.length === 0) {
      showToast('No chunks to download');
      return;
    }

    setIsDownloadingAll(true);
    showToast(`Starting batch download of ${chunks.length} files...`);

    for (let i = 0; i < chunks.length; i++) {
      const chunk = chunks[i];
      handleDownloadChunk(chunk);
      // Brief pause between browser download prompts
      await new Promise(resolve => setTimeout(resolve, 280));
    }

    setIsDownloadingAll(false);
    showToast(`Completed download of all ${chunks.length} CSV files`);
  };

  return (
    <ToolPageShell
      toolId="split-csv"
      badgeTag="SHEETS PARTITIONER"
      title="Split CSV File"
      subtitle="Divide oversized CSV spreadsheets into smaller files by row count or automatically partition by distinct values in any column in local browser RAM."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['merge-csv', 'remove-duplicates-csv', 'clean-sheet-data', 'csv-to-excel']}
    >
      <div className="w-full max-w-5xl mx-auto space-y-6">

        {/* ── 1. SOURCE INPUT: UPLOAD OR PASTE ── */}
        <div className="bg-white rounded-2xl border border-zinc-200/80 p-5 sm:p-6 shadow-xs">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-zinc-100">
            <div className="flex items-center gap-2">
              <span className="p-2 rounded-xl bg-zinc-100 text-zinc-900">
                <FileSpreadsheet className="w-4 h-4" strokeWidth={2} />
              </span>
              <div>
                <h3 className="text-sm font-semibold text-zinc-950">Source Spreadsheet</h3>
                <p className="text-xs text-zinc-500">Upload a large CSV or paste rows to partition</p>
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
                    if (e.target.files && e.target.files.length > 0) {
                      handleFileUpload(e.target.files[0]);
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
                      Supports large CSV files (processes hundreds of thousands of rows locally)
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
                placeholder="Paste rows directly from Excel or Google Sheets..."
                rows={5}
                className="w-full p-3 font-mono text-xs bg-zinc-50/80 border border-zinc-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-zinc-900/10 focus:border-zinc-900 text-zinc-800 placeholder:text-zinc-400 resize-y"
              />
              <div className="flex items-center justify-between">
                <span className="text-xs text-zinc-500">
                  Tip: Copy any spreadsheet range and paste here.
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

        {/* ── 2. PARTITIONING CONFIGURATION ── */}
        <div className="bg-white rounded-2xl border border-zinc-200/80 p-5 sm:p-6 shadow-xs">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-zinc-100">
            <div className="flex items-center gap-2">
              <span className="p-2 rounded-xl bg-zinc-100 text-zinc-900">
                <Scissors className="w-4 h-4" strokeWidth={2} />
              </span>
              <div>
                <h3 className="text-sm font-semibold text-zinc-950">Partitioning Mode</h3>
                <p className="text-xs text-zinc-500">Choose between batch row limits or categorical column splits</p>
              </div>
            </div>

            {/* Mode Toggle */}
            <div className="inline-flex p-1 rounded-xl bg-zinc-100 border border-zinc-200/60 text-xs font-medium">
              <button
                type="button"
                onClick={() => setSplitMode('row_count')}
                className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all ${
                  splitMode === 'row_count' 
                    ? 'bg-white text-zinc-950 shadow-2xs font-semibold' 
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                <Hash className="w-3.5 h-3.5" />
                <span>By Row Count</span>
              </button>
              <button
                type="button"
                onClick={() => setSplitMode('column_value')}
                className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all ${
                  splitMode === 'column_value' 
                    ? 'bg-white text-zinc-950 shadow-2xs font-semibold' 
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                <ListFilter className="w-3.5 h-3.5" />
                <span>By Column Value</span>
              </button>
            </div>
          </div>

          {/* Mode 1: Row Count Controls */}
          {splitMode === 'row_count' && (
            <div className="mt-4 space-y-3">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <label className="text-xs font-medium text-zinc-700">
                  Maximum rows per generated file:
                </label>
                <div className="flex items-center gap-2">
                  {[4, 100, 500, 1000, 5000].map((num) => (
                    <button
                      key={num}
                      type="button"
                      onClick={() => setRowsPerChunk(num)}
                      className={`px-2.5 py-1 rounded-lg text-xs font-mono font-medium border transition-all ${
                        rowsPerChunk === num
                          ? 'bg-zinc-900 text-white border-zinc-900'
                          : 'bg-zinc-50 text-zinc-600 border-zinc-200 hover:bg-zinc-100'
                      }`}
                    >
                      {num.toLocaleString()}
                    </button>
                  ))}
                  <div className="flex items-center gap-1">
                    <input
                      type="number"
                      min={1}
                      max={500000}
                      value={rowsPerChunk}
                      onChange={(e) => setRowsPerChunk(Math.max(1, parseInt(e.target.value) || 1))}
                      className="w-20 px-2.5 py-1 text-xs font-mono bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-800 text-right focus:outline-hidden focus:ring-1 focus:ring-zinc-900"
                    />
                    <span className="text-xs text-zinc-500">rows</span>
                  </div>
                </div>
              </div>
              <p className="text-xs text-zinc-500">
                Will split {parsedData.rowCount.toLocaleString()} total rows into{' '}
                <span className="font-semibold text-zinc-900">{chunks.length} separate file(s)</span>.
              </p>
            </div>
          )}

          {/* Mode 2: Column Value Controls */}
          {splitMode === 'column_value' && (
            <div className="mt-4 space-y-3">
              <div className="text-xs font-medium text-zinc-700 mb-1">
                Select column to group and partition by:
              </div>
              <div className="flex flex-wrap gap-2">
                {parsedData.headers.map((h, idx) => {
                  const isSelected = selectedColIndex === idx;
                  return (
                    <button
                      key={idx}
                      type="button"
                      onClick={() => setSelectedColIndex(idx)}
                      className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium border transition-all ${
                        isSelected
                          ? 'bg-zinc-900 text-white border-zinc-900 shadow-2xs'
                          : 'bg-zinc-50 text-zinc-600 border-zinc-200 hover:border-zinc-300 hover:bg-zinc-100'
                      }`}
                    >
                      {isSelected && <Check className="w-3 h-3 text-white" />}
                      <span>{h}</span>
                    </button>
                  );
                })}
              </div>
              <p className="text-xs text-zinc-500">
                Found <span className="font-semibold text-zinc-900">{chunks.length} distinct values</span> in column &ldquo;{parsedData.headers[selectedColIndex]}&rdquo;. Each unique value generates its own standalone CSV.
              </p>
            </div>
          )}
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
            <span className="text-[11px] text-zinc-400 mt-1 block">Full source dataset</span>
          </div>

          <div className="bg-white rounded-2xl border border-zinc-200/80 p-4 shadow-xs">
            <span className="text-xs font-medium text-zinc-500 uppercase tracking-wider block">Generated Files</span>
            <div className="mt-1 flex items-baseline gap-2">
              <span className="text-2xl font-bold font-mono text-zinc-950">
                {chunks.length}
              </span>
            </div>
            <span className="text-[11px] text-emerald-600 font-medium mt-1 block">
              Batch ready to export
            </span>
          </div>

          <div className="bg-white rounded-2xl border border-zinc-200/80 p-4 shadow-xs">
            <span className="text-xs font-medium text-zinc-500 uppercase tracking-wider block">Average Batch Size</span>
            <div className="mt-1 flex items-baseline gap-2">
              <span className="text-2xl font-bold font-mono text-zinc-950">
                {chunks.length > 0 ? Math.round(parsedData.rowCount / chunks.length) : 0}
              </span>
            </div>
            <span className="text-[11px] text-zinc-400 mt-1 block">Rows per file</span>
          </div>

          <div className="bg-white rounded-2xl border border-zinc-200/80 p-4 shadow-xs">
            <span className="text-xs font-medium text-zinc-500 uppercase tracking-wider block">Local Engine</span>
            <div className="mt-1 flex items-center gap-1.5 text-zinc-950">
              <ShieldCheck className="w-5 h-5 text-emerald-600 shrink-0" strokeWidth={2} />
              <span className="text-sm font-semibold">100% In-RAM</span>
            </div>
            <span className="text-[11px] text-zinc-400 mt-1 block">Zero cloud transmissions</span>
          </div>
        </div>

        {/* ── 4. GENERATED BATCH CHUNKS & DOWNLOAD ── */}
        <div className="bg-white rounded-2xl border border-zinc-200/80 shadow-xs overflow-hidden">
          <div className="p-4 sm:p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h3 className="text-sm font-semibold text-zinc-950">
                Partitioned Batch Outputs ({chunks.length} files)
              </h3>
              <p className="text-xs text-zinc-500">Download files individually or export all files in one click</p>
            </div>

            <button
              type="button"
              onClick={handleDownloadAllChunks}
              disabled={isDownloadingAll || chunks.length === 0}
              className="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 disabled:opacity-50 rounded-xl transition-all shadow-2xs self-start sm:self-auto"
            >
              <Download className="w-3.5 h-3.5" />
              <span>{isDownloadingAll ? 'Downloading All...' : `Download All (${chunks.length} Files)`}</span>
            </button>
          </div>

          {/* Chunks List */}
          <div className="divide-y divide-zinc-100">
            {chunks.length === 0 ? (
              <div className="p-12 text-center text-zinc-400 text-xs">
                No data loaded to split. Upload a CSV or load sample data above.
              </div>
            ) : (
              chunks.map((chunk, idx) => {
                const isExpanded = previewChunkIndex === idx;
                return (
                  <div key={idx} className="p-4 hover:bg-zinc-50/60 transition-colors">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                      <div className="flex items-center gap-3 min-w-0">
                        <span className="p-2 rounded-xl bg-zinc-100 text-zinc-800 shrink-0 font-mono text-xs font-semibold">
                          #{idx + 1}
                        </span>
                        <div className="min-w-0">
                          <span className="text-xs font-semibold text-zinc-900 block truncate">
                            {chunk.filename}
                          </span>
                          <span className="text-[11px] text-zinc-500">
                            {chunk.label}
                          </span>
                        </div>
                      </div>

                      <div className="flex items-center gap-2 self-end sm:self-auto">
                        <button
                          type="button"
                          onClick={() => setPreviewChunkIndex(isExpanded ? null : idx)}
                          className="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-zinc-600 hover:text-zinc-950 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-lg transition-all"
                        >
                          <Eye className="w-3.5 h-3.5" />
                          <span>{isExpanded ? 'Hide' : 'Preview'}</span>
                        </button>
                        <button
                          type="button"
                          onClick={() => handleDownloadChunk(chunk)}
                          className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-zinc-900 hover:text-white bg-zinc-100 hover:bg-zinc-900 border border-zinc-200 rounded-lg transition-all"
                        >
                          <Download className="w-3.5 h-3.5" />
                          <span>Download</span>
                        </button>
                      </div>
                    </div>

                    {/* Preview Table Accordion */}
                    {isExpanded && (
                      <div className="mt-3 p-3 bg-zinc-50 rounded-xl border border-zinc-200/80 overflow-x-auto">
                        <div className="text-[11px] font-semibold text-zinc-600 mb-2">
                          Previewing first {Math.min(chunk.rows.length, 5)} rows of {chunk.filename}:
                        </div>
                        <table className="w-full text-left text-[11px] font-mono border-collapse">
                          <thead>
                            <tr className="border-b border-zinc-200 text-zinc-500">
                              {chunk.headers.map((h, hIdx) => (
                                <th key={hIdx} className="pb-1.5 pr-3 font-medium whitespace-nowrap">
                                  {h}
                                </th>
                              ))}
                            </tr>
                          </thead>
                          <tbody className="divide-y divide-zinc-200/60 text-zinc-800">
                            {chunk.rows.slice(0, 5).map((row, rIdx) => (
                              <tr key={rIdx}>
                                {row.map((cell, cIdx) => (
                                  <td key={cIdx} className="py-1.5 pr-3 whitespace-nowrap">
                                    {cell}
                                  </td>
                                ))}
                              </tr>
                            ))}
                          </tbody>
                        </table>
                      </div>
                    )}
                  </div>
                );
              })
            )}
          </div>
        </div>

      </div>
    </ToolPageShell>
  );
}
