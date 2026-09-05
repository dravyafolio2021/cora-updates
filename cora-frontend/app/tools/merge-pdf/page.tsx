'use client';

import React, { useState, useRef, useCallback } from 'react';
import { 
  FileText, 
  ArrowDown, 
  ArrowUp, 
  Trash2, 
  Plus, 
  Download, 
  Check, 
  Sparkles, 
  Layers, 
  ShieldCheck, 
  AlertCircle,
  FileCheck2,
  FileSpreadsheet,
  GripVertical
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { ToolOutcomeRoiBanner, ToolOutcomeData } from '@/components/tools/ToolOutcomeRoiBanner';
import { ToolOutcomeModalData } from '@/components/tools/ToolOutcomeDrawerModal';
import { TOOL_AGENT_REGISTRY } from '@/lib/tools-agent-config';
import { useToast } from '@/components/ui/Toast';
import { mergePdfFiles, downloadPdfBlob, getPdfInfo } from '@/lib/pdf-engine';

interface PdfFileItem {
  id: string;
  file: File;
  name: string;
  size: number;
  pageCount: number;
}

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
}

export default function MergePdfPage() {
  const [files, setFiles] = useState<PdfFileItem[]>([]);
  const [outputFileName, setOutputFileName] = useState<string>('cora-merged-proposal.pdf');
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [draggedIndex, setDraggedIndex] = useState<number | null>(null);
  const [activeOutcome, setActiveOutcome] = useState<ToolOutcomeData | null>(null);
  const [activeOutcomeModal, setActiveOutcomeModal] = useState<ToolOutcomeModalData | null>(null);

  const fileInputRef = useRef<HTMLInputElement>(null);
  const { showToast } = useToast();

  const handleAddFiles = useCallback(async (incomingFiles: FileList | File[]) => {
    const validPdfFiles: File[] = [];
    for (let i = 0; i < incomingFiles.length; i++) {
      const file = incomingFiles[i];
      if (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')) {
        validPdfFiles.push(file);
      }
    }

    if (validPdfFiles.length === 0) {
      showToast('Please select valid PDF documents (.pdf)');
      return;
    }

    const newItems: PdfFileItem[] = [];

    for (const file of validPdfFiles) {
      try {
        const info = await getPdfInfo(file);
        newItems.push({
          id: `${file.name}-${Date.now()}-${Math.random().toString(36).substring(2, 7)}`,
          file,
          name: file.name,
          size: file.size,
          pageCount: info.pageCount,
        });
      } catch (err) {
        newItems.push({
          id: `${file.name}-${Date.now()}-${Math.random().toString(36).substring(2, 7)}`,
          file,
          name: file.name,
          size: file.size,
          pageCount: 1,
        });
      }
    }

    setFiles((prev) => [...prev, ...newItems]);
    showToast(`Added ${newItems.length} PDF ${newItems.length === 1 ? 'file' : 'files'}`);
  }, [showToast]);

  const handleFileInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      handleAddFiles(e.target.files);
      e.target.value = '';
    }
  };

  const handleDrop = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleAddFiles(e.dataTransfer.files);
    }
  };

  const handleDragOver = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(true);
  };

  const handleDragLeave = (e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
  };

  // Reordering handlers
  const moveFile = (index: number, direction: 'up' | 'down') => {
    const targetIndex = direction === 'up' ? index - 1 : index + 1;
    if (targetIndex < 0 || targetIndex >= files.length) return;

    setFiles((prev) => {
      const updated = [...prev];
      const temp = updated[index];
      updated[index] = updated[targetIndex];
      updated[targetIndex] = temp;
      return updated;
    });
  };

  const removeFile = (index: number) => {
    setFiles((prev) => prev.filter((_, i) => i !== index));
    showToast('Document removed from merge list');
  };

  const clearAllFiles = () => {
    setFiles([]);
    showToast('Cleared all files');
  };

  // Drag & drop item sorting
  const handleItemDragStart = (index: number) => {
    setDraggedIndex(index);
  };

  const handleItemDragEnter = (targetIndex: number) => {
    if (draggedIndex === null || draggedIndex === targetIndex) return;

    setFiles((prev) => {
      const updated = [...prev];
      const itemToMove = updated.splice(draggedIndex, 1)[0];
      updated.splice(targetIndex, 0, itemToMove);
      return updated;
    });
    setDraggedIndex(targetIndex);
  };

  const handleItemDragEnd = () => {
    setDraggedIndex(null);
  };

  // Merge Action
  const handleMergePdf = async () => {
    if (files.length < 2) {
      showToast('Please upload at least 2 PDF files to merge');
      return;
    }

    setIsProcessing(true);
    try {
      const rawFiles = files.map((f) => f.file);
      const mergedBytes = await mergePdfFiles(rawFiles);
      
      const fileName = outputFileName.trim().length > 0 
        ? (outputFileName.endsWith('.pdf') ? outputFileName : `${outputFileName}.pdf`)
        : 'cora-merged-proposal.pdf';

      downloadPdfBlob(mergedBytes, fileName);
      showToast('Merged PDF successfully downloaded!');

      // 1. Trigger AI-SDR Value & ROI Milestone Banner
      setActiveOutcome({
        summaryTitle: `${files.length} Documents Merged (${totalPages} Pages, ${formatBytes(totalSizeBytes)})`,
        timeSavedEstimate: '~20 mins administrative formatting saved',
        securityProof: '0 bytes sent to external servers • 100% In-Browser RAM',
        suggestedNextStep: {
          headline: 'Sending this merged proposal or contract to a client?',
          description: 'Add legally valid Section 10A digital signatures, track when the client views the document, and collect 0% fee advance UPI payments automatically.',
          ctaLabel: 'Collect Signatures & Advance with Kavya (Free)',
          ctaHref: `/workspace/login?mode=signup&ref=tofu_merged_proposal&docs=${files.length}`,
        },
      });

      // 2. Trigger Post-Download Outcome Drawer Modal (Instant Bridge)
      setActiveOutcomeModal({
        summaryTitle: `Merged ${files.length} Documents (${totalPages} Pages, ${formatBytes(totalSizeBytes)})`,
        timeSavedEstimate: '~20 mins manual document assembly saved',
        securityProof: '100% In-Browser RAM • Zero Cloud File Storage',
        downloadFileName: fileName,
        suggestedNextStep: {
          badge: 'Next Step for Proposals & Deeds',
          headline: 'Sending this merged proposal or contract to a client?',
          description: 'Turn this PDF into a court-admissible proposal with Section 10A signatures, real-time client open tracking, and automated 0% fee advance UPI billing on WhatsApp.',
          ctaLabel: 'Collect Signatures & Advance with Kavya (Free)',
          ctaHref: `/workspace/login?mode=signup&ref=tofu_modal_merged&docs=${files.length}`,
        },
      });
    } catch (err: any) {
      console.error('Merge error:', err);
      showToast(err?.message || 'Failed to merge PDF files. Please try again.');
    } finally {
      setIsProcessing(false);
    }
  };

  const totalPages = files.reduce((acc, curr) => acc + curr.pageCount, 0);
  const totalSizeBytes = files.reduce((acc, curr) => acc + curr.size, 0);

  const mergeFaqs = [
    {
      question: 'Is it safe to merge confidential client proposals and NDAs here?',
      answer: 'Yes, 100% confidential. Unlike third-party cloud converters that transmit your files to remote web servers, Cora executes the entire PDF compilation locally inside your web browser via WebAssembly. Zero document bytes or metadata leave your computer or mobile device.'
    },
    {
      question: 'How do I rearrange the order of pitch deck slides and contract annexures?',
      answer: 'You can drag and drop items in the list, or on mobile devices tap the Move Up and Move Down arrow buttons next to each file. The resulting PDF will concatenate your files in the exact sequential order shown.'
    },
    {
      question: 'Is there a file size or document limit?',
      answer: 'There are no artificial file limits. You can combine 2, 10, or 25+ PDF files totaling hundreds of megabytes. Processing speed depends only on your local device CPU and available RAM memory.'
    },
    {
      question: 'Will vector graphics, form fields, and high-resolution images be preserved?',
      answer: 'Yes. Cora performs lossless binary page extraction and reassembly. Native typography, CMYK/RGB color profiles, embedded vector branding logos, and full-resolution graphics remain completely uncompressed.'
    }
  ];

  return (
    <ToolPageShell
      toolId="merge-pdf"
      badgeTag="100% Client-Side Pure JS"
      title="Merge PDF Files Online"
      subtitle="Combine client proposals, confidential pitch decks, and legal annexures into a single seamless document. Zero file uploads — 100% private in browser memory."
      faqItems={mergeFaqs}
      activeOutcome={activeOutcome}
      activeOutcomeModal={activeOutcomeModal}
      onCloseOutcomeModal={() => setActiveOutcomeModal(null)}
    >
      <div className="space-y-6">
        {/* Hidden File Input */}
        <input
          ref={fileInputRef}
          type="file"
          accept="application/pdf,.pdf"
          multiple
          onChange={handleFileInputChange}
          className="hidden"
          id="cora-pdf-upload-input"
        />

        {/* ── Dropzone & Upload Target ── */}
        <div
          onDrop={handleDrop}
          onDragOver={handleDragOver}
          onDragLeave={handleDragLeave}
          onClick={() => fileInputRef.current?.click()}
          className={`relative rounded-3xl border-2 border-dashed p-6 sm:p-10 text-center transition-all cursor-pointer select-none group ${
            isDraggingOver
              ? 'border-zinc-950 bg-zinc-100/80 shadow-md scale-[0.99]'
              : 'border-zinc-200/90 bg-white hover:border-zinc-400 hover:bg-zinc-50/50 shadow-2xs'
          }`}
        >
          <div className="max-w-md mx-auto flex flex-col items-center justify-center space-y-3">
            <div className="w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-200/80 flex items-center justify-center text-zinc-900 group-hover:scale-105 transition-transform duration-200">
              <Layers className="w-6 h-6 stroke-[1.75]" />
            </div>

            <div className="space-y-1">
              <h3 className="text-sm sm:text-base font-bold text-zinc-950 tracking-tight">
                Drop your PDF files here, or <span className="underline underline-offset-2 text-zinc-900">browse files</span>
              </h3>
              <p className="text-xs text-zinc-500 leading-relaxed">
                Select multiple contracts, decks, or invoices (.pdf) to combine in sequence
              </p>
            </div>

            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 text-[11px] font-mono text-zinc-600 border border-zinc-200/60">
              <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" />
              <span>Zero server upload &bull; 100% private in memory</span>
            </div>

            <div className="pt-2">
              <button
                type="button"
                onClick={async (e) => {
                  e.stopPropagation();
                  // Generate 2 sample in-memory mock PDF files for instant test
                  const { PDFDocument, rgb } = await import('pdf-lib');
                  
                  const doc1 = await PDFDocument.create();
                  const p1 = doc1.addPage([595, 842]);
                  p1.drawText('Sample Master Service Agreement - Part 1', { x: 50, y: 750, size: 18 });
                  p1.drawText('Confidential Client Proposal - Deliverables & Scope', { x: 50, y: 720, size: 12 });
                  const bytes1 = await doc1.save();
                  const f1 = new File([bytes1.buffer.slice(bytes1.byteOffset, bytes1.byteOffset + bytes1.byteLength) as ArrayBuffer], 'client-master-agreement.pdf', { type: 'application/pdf' });

                  const doc2 = await PDFDocument.create();
                  const p2 = doc2.addPage([595, 842]);
                  p2.drawText('Commercial Terms & Payment Milestone Annexure', { x: 50, y: 750, size: 18 });
                  p2.drawText('SAC Code 9983 • Net 15 Payment Terms', { x: 50, y: 720, size: 12 });
                  const bytes2 = await doc2.save();
                  const f2 = new File([bytes2.buffer.slice(bytes2.byteOffset, bytes2.byteOffset + bytes2.byteLength) as ArrayBuffer], 'payment-terms-annexure.pdf', { type: 'application/pdf' });

                  handleAddFiles([f1, f2]);
                  showToast('Loaded 2 sample proposal documents for demonstration!');
                }}
                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-semibold transition-colors"
              >
                <Sparkles className="w-3.5 h-3.5 text-zinc-700" />
                <span>Try with Sample Documents</span>
              </button>
            </div>
          </div>
        </div>

        {/* ── File Queue Console ── */}
        {files.length > 0 && (
          <div className="bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs space-y-5">
            {/* Header & Control Bar */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-zinc-100">
              <div className="flex items-center gap-2">
                <span className="w-2 h-2 rounded-full bg-emerald-500" />
                <h3 className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-800">
                  Document Sequence ({files.length} {files.length === 1 ? 'file' : 'files'})
                </h3>
              </div>

              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={() => fileInputRef.current?.click()}
                  className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-800 text-xs font-semibold transition-colors cursor-pointer"
                >
                  <Plus className="w-3.5 h-3.5" />
                  <span>Add More</span>
                </button>
                <button
                  type="button"
                  onClick={clearAllFiles}
                  className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-zinc-200 text-zinc-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50/50 text-xs font-medium transition-colors cursor-pointer"
                >
                  <Trash2 className="w-3.5 h-3.5" />
                  <span>Clear</span>
                </button>
              </div>
            </div>

            {/* Reorderable List */}
            <div className="space-y-2.5">
              {files.map((item, idx) => (
                <div
                  key={item.id}
                  draggable
                  onDragStart={() => handleItemDragStart(idx)}
                  onDragEnter={() => handleItemDragEnter(idx)}
                  onDragEnd={handleItemDragEnd}
                  onDragOver={(e) => e.preventDefault()}
                  className={`flex items-center justify-between gap-3 p-3.5 rounded-2xl border transition-all ${
                    draggedIndex === idx
                      ? 'border-zinc-950 bg-zinc-50 opacity-50'
                      : 'border-zinc-200/80 bg-zinc-50/40 hover:bg-zinc-50 hover:border-zinc-300'
                  }`}
                >
                  {/* Left: Drag Handle, Number, Title & Info */}
                  <div className="flex items-center gap-3 min-w-0 flex-1">
                    <div className="hidden sm:flex items-center text-zinc-400 cursor-grab active:cursor-grabbing hover:text-zinc-600">
                      <GripVertical className="w-4 h-4" />
                    </div>

                    <div className="w-6 h-6 rounded-lg bg-zinc-900 text-white font-mono text-[11px] font-bold flex items-center justify-center shrink-0">
                      {idx + 1}
                    </div>

                    <div className="w-8 h-8 rounded-xl bg-white border border-zinc-200/80 flex items-center justify-center text-zinc-700 shrink-0">
                      <FileText className="w-4 h-4" />
                    </div>

                    <div className="min-w-0 flex-1">
                      <p className="text-xs sm:text-sm font-bold text-zinc-900 truncate leading-snug">
                        {item.name}
                      </p>
                      <div className="flex items-center gap-2 text-[11px] font-mono text-zinc-500 mt-0.5">
                        <span>{formatBytes(item.size)}</span>
                        <span>&bull;</span>
                        <span>{item.pageCount} {item.pageCount === 1 ? 'page' : 'pages'}</span>
                      </div>
                    </div>
                  </div>

                  {/* Right: Reorder & Remove Action Buttons */}
                  <div className="flex items-center gap-1 shrink-0">
                    <button
                      type="button"
                      disabled={idx === 0}
                      onClick={() => moveFile(idx, 'up')}
                      title="Move Up"
                      className="p-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:text-zinc-950 hover:bg-white disabled:opacity-30 disabled:pointer-events-none transition-colors cursor-pointer"
                    >
                      <ArrowUp className="w-3.5 h-3.5" />
                    </button>

                    <button
                      type="button"
                      disabled={idx === files.length - 1}
                      onClick={() => moveFile(idx, 'down')}
                      title="Move Down"
                      className="p-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:text-zinc-950 hover:bg-white disabled:opacity-30 disabled:pointer-events-none transition-colors cursor-pointer"
                    >
                      <ArrowDown className="w-3.5 h-3.5" />
                    </button>

                    <button
                      type="button"
                      onClick={() => removeFile(idx)}
                      title="Remove file"
                      className="p-1.5 rounded-lg border border-zinc-200 text-zinc-400 hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-colors cursor-pointer ml-1"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              ))}
            </div>

            {/* ── Summary & Output Filename ── */}
            <div className="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/80 space-y-3">
              <div className="flex flex-wrap items-center justify-between gap-2 text-xs font-mono">
                <span className="text-zinc-500">Summary:</span>
                <div className="flex items-center gap-3 font-semibold text-zinc-800">
                  <span>{files.length} Documents</span>
                  <span>&bull;</span>
                  <span>{totalPages} Total Pages</span>
                  <span>&bull;</span>
                  <span>{formatBytes(totalSizeBytes)}</span>
                </div>
              </div>

              <div>
                <label className="text-[11px] font-mono font-bold text-zinc-600 uppercase tracking-wider block mb-1.5">
                  Export Filename
                </label>
                <input
                  type="text"
                  value={outputFileName}
                  onChange={(e) => setOutputFileName(e.target.value)}
                  placeholder="merged-proposal.pdf"
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 bg-white text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950 shadow-2xs"
                />
              </div>
            </div>

            {/* ── Merge Action Button ── */}
            <div className="pt-2">
              <button
                type="button"
                disabled={files.length < 2 || isProcessing}
                onClick={handleMergePdf}
                className="w-full py-3.5 px-5 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-sm flex items-center justify-center gap-2.5 shadow-md active:scale-[0.99] disabled:opacity-50 disabled:pointer-events-none transition-all cursor-pointer"
              >
                {isProcessing ? (
                  <>
                    <div className="w-4 h-4 rounded-full border-2 border-white/30 border-t-white animate-spin" />
                    <span>Merging {files.length} Documents...</span>
                  </>
                ) : (
                  <>
                    <Layers className="w-4 h-4" />
                    <span>Merge {files.length} PDF Files & Download</span>
                  </>
                )}
              </button>
              {files.length < 2 && (
                <p className="text-[11px] text-zinc-400 text-center mt-2">
                  Upload at least 2 PDF files to enable merging
                </p>
              )}
            </div>

            {/* ── Direct In-Context Post-Download Outcome Banner (Right below the merge button, zero scrolling needed) ── */}
            {activeOutcome && (
              <div id="post-merge-outcome" className="mt-5 animate-in fade-in slide-in-from-top-3 duration-300">
                <ToolOutcomeRoiBanner
                  toolId="merge-pdf"
                  agentData={TOOL_AGENT_REGISTRY['merge-pdf']}
                  outcome={activeOutcome}
                />
              </div>
            )}
          </div>
        )}
      </div>
    </ToolPageShell>
  );
}
