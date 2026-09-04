'use client';

import React, { useState, useRef, useCallback } from 'react';
import { 
  FileText, 
  Download, 
  ShieldCheck, 
  RotateCcw, 
  RotateCw, 
  Copy, 
  Trash2, 
  ArrowLeft, 
  ArrowRight, 
  CheckCircle2, 
  Sparkles, 
  Layers, 
  SlidersHorizontal,
  GripVertical,
  Plus
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo, organizePdfPages, downloadPdfBlob, PageInfo, PageOrganizeItem } from '@/lib/pdf-engine';

const ORGANIZE_PDF_FAQS = [
  {
    question: 'How does the drag-and-drop page organizer work?',
    answer: 'Simply click and hold any page tile, drag it to your preferred position in the sequence, and release. You can also use the inline Move Left and Move Right buttons for precision keyboard-accessible sorting.'
  },
  {
    question: 'Can I duplicate pages like invoice schedules or signoff sheets?',
    answer: 'Yes. Click the Duplicate button on any page tile to instantly clone that sheet right beside the original. You can duplicate individual pages as many times as required.'
  },
  {
    question: 'Can I rotate individual landscape sheets while leaving portrait pages untouched?',
    answer: 'Yes. Each page tile includes an independent 90° clockwise rotation control. You can correct sideways scans or orientation mismatches on a sheet-by-sheet basis or rotate all sheets at once.'
  },
  {
    question: 'Are my confidential documents uploaded to any external server?',
    answer: 'Never. Page reordering, duplication, and rotation take place 100% locally in your browser memory using WebAssembly. Your files never leave your device.'
  },
  {
    question: 'Does rearranging or duplicating pages reduce vector document quality?',
    answer: 'No. Cora copies the native underlying PDF page object tree losslessly. All fonts, vector illustrations, high-resolution imagery, and form fields retain 100% of their original visual sharpness.'
  }
];

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

interface PageTile {
  id: string;
  originalIndex: number; // 0-based
  rotation: number; // degrees: 0, 90, 180, 270
}

export default function OrganizePdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [pages, setPages] = useState<PageInfo[]>([]);
  const [pageTiles, setPageTiles] = useState<PageTile[]>([]);
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [organizedBytes, setOrganizedBytes] = useState<Uint8Array | null>(null);

  // Drag-and-drop state
  const [draggedTileIndex, setDraggedTileIndex] = useState<number | null>(null);
  const [dragOverTileIndex, setDragOverTileIndex] = useState<number | null>(null);

  const handleFileLoad = async (loadedFile: File) => {
    if (loadedFile.type !== 'application/pdf' && !loadedFile.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please select a valid PDF file');
      return;
    }

    try {
      const info = await getPdfInfo(loadedFile);
      setPdfFile(loadedFile);
      setPages(info.pages);

      const initialTiles: PageTile[] = info.pages.map((p, idx) => ({
        id: 'tile_' + idx + '_' + Math.random().toString(36).substr(2, 4),
        originalIndex: idx,
        rotation: 0,
      }));

      setPageTiles(initialTiles);
      setOrganizedBytes(null);
      showToast('Loaded ' + loadedFile.name + ' (' + info.pageCount + ' pages)');
    } catch {
      showToast('Unable to open PDF. File may be encrypted.');
    }
  };

  const handleDrop = useCallback((e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      handleFileLoad(e.dataTransfer.files[0]);
    }
  }, []);

  const handleDragOver = useCallback((e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(true);
  }, []);

  const handleDragLeave = useCallback((e: React.DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDraggingOver(false);
  }, []);

  const resetAll = () => {
    setPdfFile(null);
    setPages([]);
    setPageTiles([]);
    setOrganizedBytes(null);
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  // Reorder via Drag and Drop
  const handleTileDragStart = (e: React.DragEvent<HTMLDivElement>, index: number) => {
    setDraggedTileIndex(index);
    e.dataTransfer.effectAllowed = 'move';
  };

  const handleTileDragOver = (e: React.DragEvent<HTMLDivElement>, index: number) => {
    e.preventDefault();
    setDragOverTileIndex(index);
  };

  const handleTileDrop = (e: React.DragEvent<HTMLDivElement>, targetIndex: number) => {
    e.preventDefault();
    if (draggedTileIndex === null || draggedTileIndex === targetIndex) {
      setDraggedTileIndex(null);
      setDragOverTileIndex(null);
      return;
    }

    const updated = [...pageTiles];
    const [moved] = updated.splice(draggedTileIndex, 1);
    updated.splice(targetIndex, 0, moved);

    setPageTiles(updated);
    setDraggedTileIndex(null);
    setDragOverTileIndex(null);
    showToast('Reordered page to position ' + (targetIndex + 1));
  };

  // Move left / right buttons
  const moveTile = (index: number, direction: 'left' | 'right') => {
    const targetIndex = direction === 'left' ? index - 1 : index + 1;
    if (targetIndex < 0 || targetIndex >= pageTiles.length) return;

    const updated = [...pageTiles];
    const temp = updated[index];
    updated[index] = updated[targetIndex];
    updated[targetIndex] = temp;

    setPageTiles(updated);
  };

  // Duplicate a page
  const duplicateTile = (index: number) => {
    const item = pageTiles[index];
    const newItem: PageTile = {
      id: 'dup_' + Date.now() + '_' + Math.random().toString(36).substr(2, 4),
      originalIndex: item.originalIndex,
      rotation: item.rotation,
    };
    const updated = [...pageTiles];
    updated.splice(index + 1, 0, newItem);
    setPageTiles(updated);
    showToast('Duplicated Page ' + (index + 1));
  };

  // Rotate a page 90 degrees
  const rotateTile = (index: number) => {
    const updated = [...pageTiles];
    updated[index] = {
      ...updated[index],
      rotation: (updated[index].rotation + 90) % 360,
    };
    setPageTiles(updated);
  };

  // Delete a page
  const deleteTile = (index: number) => {
    if (pageTiles.length <= 1) {
      showToast('A PDF must contain at least one page');
      return;
    }
    const updated = pageTiles.filter((_, i) => i !== index);
    setPageTiles(updated);
    showToast('Removed page from document');
  };

  // Bulk operations
  const reversePageSequence = () => {
    setPageTiles((prev) => [...prev].reverse());
    showToast('Reversed page sequence');
  };

  const rotateAllPages = () => {
    setPageTiles((prev) =>
      prev.map((t) => ({ ...t, rotation: (t.rotation + 90) % 360 }))
    );
    showToast('Rotated all pages 90° clockwise');
  };

  const resetToOriginal = () => {
    if (!pages.length) return;
    const original: PageTile[] = pages.map((p, idx) => ({
      id: 'tile_' + idx + '_' + Math.random().toString(36).substr(2, 4),
      originalIndex: idx,
      rotation: 0,
    }));
    setPageTiles(original);
    showToast('Reset to original sequence');
  };

  // Save reorganized PDF
  const handleSaveOrganized = async () => {
    if (!pdfFile || pageTiles.length === 0) return;

    setIsProcessing(true);
    try {
      const items: PageOrganizeItem[] = pageTiles.map((t) => ({
        id: t.id,
        originalIndex: t.originalIndex,
        rotation: t.rotation,
      }));

      const output = await organizePdfPages(pdfFile, items);
      setOrganizedBytes(output);
      showToast('PDF organized with ' + pageTiles.length + ' page(s)!');
    } catch (err: any) {
      showToast(err?.message || 'Failed to reorganize PDF');
    } finally {
      setIsProcessing(false);
    }
  };

  const handleDownload = () => {
    if (!organizedBytes || !pdfFile) return;
    const baseName = pdfFile.name.replace(/\.pdf$/i, '');
    downloadPdfBlob(organizedBytes, `${baseName}_organized.pdf`);
    showToast('Organized PDF downloaded');
  };

  return (
    <ToolPageShell
      toolId="organize-pdf"
      badgeTag="Visual Page Organizer"
      title="Organize PDF - Rearrange, Reorder & Duplicate"
      subtitle="Drag and drop page tiles to reorder sequences, duplicate important sheets, and rotate page orientations visually. 100% private in-browser document sequencer."
      faqItems={ORGANIZE_PDF_FAQS}
      relatedToolSlugs={['rotate-pdf', 'crop-pdf', 'remove-pages', 'compress-pdf']}
    >
      <div className="max-w-6xl mx-auto space-y-8">
        
        {/* Step 1: Upload Dropzone */}
        {!pdfFile ? (
          <div
            onDrop={handleDrop}
            onDragOver={handleDragOver}
            onDragLeave={handleDragLeave}
            onClick={() => fileInputRef.current?.click()}
            className={`relative rounded-3xl border-2 border-dashed p-10 sm:p-14 text-center cursor-pointer transition-all duration-200 ${
              isDraggingOver
                ? 'border-zinc-950 bg-zinc-100/70 scale-[0.99]'
                : 'border-zinc-300 bg-white hover:border-zinc-400 hover:bg-zinc-50/50'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept="application/pdf,.pdf"
              className="hidden"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleFileLoad(e.target.files[0]);
                }
              }}
            />

            <div className="w-16 h-16 mx-auto mb-4 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900 shadow-2xs">
              <Layers className="w-8 h-8 stroke-[1.8]" />
            </div>

            <h3 className="text-lg sm:text-xl font-bold text-zinc-950 mb-1">
              Select or Drop PDF to Organize
            </h3>
            <p className="text-sm text-zinc-500 max-w-md mx-auto mb-4">
              Rearrange page order, duplicate invoices, and rotate orientation with drag-and-drop tiles.
            </p>

            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 text-xs font-mono text-zinc-600">
              <ShieldCheck className="w-3.5 h-3.5 stroke-[1.8] text-zinc-900" />
              <span>Lossless In-Memory Page Sequencing • Zero Server Uploads</span>
            </div>
          </div>
        ) : (
          /* Step 2: Interactive Tile Grid Workspace */
          <div className="space-y-6">
            
            {/* Top Status & File Bar */}
            <div className="p-4 sm:p-5 rounded-2xl bg-white border border-zinc-200 flex flex-wrap items-center justify-between gap-4 shadow-xs">
              <div className="flex items-center gap-3.5 min-w-0">
                <div className="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0 text-zinc-900">
                  <FileText className="w-5 h-5 stroke-[1.8]" />
                </div>
                <div className="min-w-0">
                  <h4 className="text-sm font-bold text-zinc-900 truncate max-w-xs sm:max-w-md">
                    {pdfFile.name}
                  </h4>
                  <p className="text-xs font-mono text-zinc-500 flex items-center gap-2 mt-0.5">
                    <span>{formatBytes(pdfFile.size)}</span>
                    <span>•</span>
                    <span className="text-zinc-900 font-semibold">{pageTiles.length} total pages in sequence</span>
                  </p>
                </div>
              </div>

              <button
                type="button"
                onClick={resetAll}
                className="px-3 py-1.5 rounded-lg border border-zinc-200 hover:bg-zinc-100 text-zinc-700 text-xs font-medium flex items-center gap-1.5 transition-colors cursor-pointer"
              >
                <RotateCcw className="w-3.5 h-3.5 stroke-[1.8]" />
                Replace File
              </button>
            </div>

            {/* Quick Bulk Action Tools */}
            <div className="p-4 sm:p-5 rounded-3xl bg-white border border-zinc-200 shadow-xs flex flex-wrap items-center justify-between gap-3">
              <div className="flex items-center gap-2 text-xs font-mono text-zinc-500">
                <SlidersHorizontal className="w-3.5 h-3.5 stroke-[1.8] text-zinc-900" />
                <span className="font-bold text-zinc-900">Sequence Tools:</span>
              </div>

              <div className="flex flex-wrap items-center gap-2">
                <button
                  type="button"
                  onClick={reversePageSequence}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
                >
                  Reverse Sequence
                </button>
                <button
                  type="button"
                  onClick={rotateAllPages}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer flex items-center gap-1"
                >
                  <RotateCw className="w-3 h-3 stroke-[2]" />
                  Rotate All 90°
                </button>
                <button
                  type="button"
                  onClick={resetToOriginal}
                  className="px-3 py-1.5 rounded-xl text-xs font-medium border border-zinc-200 hover:bg-zinc-100 text-zinc-600 transition-colors cursor-pointer"
                >
                  Reset Sequence
                </button>
              </div>
            </div>

            {/* Interactive Drag-and-Drop Page Grid */}
            <div className="p-6 sm:p-8 rounded-3xl bg-zinc-50 border border-zinc-200 shadow-inner space-y-4">
              <div className="flex items-center justify-between text-xs font-mono text-zinc-500">
                <span>Drag tiles to reorder • Hover for duplicate, rotate & delete actions</span>
                <span>{pageTiles.length} sheets in current draft</span>
              </div>

              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                {pageTiles.map((tile, index) => {
                  const originalPage = pages[tile.originalIndex];
                  const isHoveredTarget = dragOverTileIndex === index;

                  return (
                    <div
                      key={tile.id}
                      draggable
                      onDragStart={(e) => handleTileDragStart(e, index)}
                      onDragOver={(e) => handleTileDragOver(e, index)}
                      onDrop={(e) => handleTileDrop(e, index)}
                      className={`group relative p-3.5 rounded-2xl border bg-white text-zinc-900 transition-all duration-150 flex flex-col justify-between shadow-xs select-none cursor-grab active:cursor-grabbing ${
                        isHoveredTarget
                          ? 'border-zinc-950 ring-2 ring-zinc-950 scale-105 shadow-md z-10'
                          : 'border-zinc-200 hover:border-zinc-400 hover:shadow-sm'
                      }`}
                    >
                      {/* Top Header: Current Position & Move Arrows */}
                      <div className="flex items-center justify-between w-full mb-2">
                        <div className="flex items-center gap-1.5">
                          <GripVertical className="w-3.5 h-3.5 text-zinc-400 group-hover:text-zinc-950" />
                          <span className="text-xs font-mono font-bold text-zinc-950">
                            #{index + 1}
                          </span>
                        </div>

                        <div className="flex items-center gap-1">
                          <button
                            type="button"
                            onClick={(e) => { e.stopPropagation(); moveTile(index, 'left'); }}
                            disabled={index === 0}
                            className="p-1 rounded hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 disabled:opacity-20 cursor-pointer"
                            title="Move Left"
                          >
                            <ArrowLeft className="w-3 h-3 stroke-[2]" />
                          </button>
                          <button
                            type="button"
                            onClick={(e) => { e.stopPropagation(); moveTile(index, 'right'); }}
                            disabled={index === pageTiles.length - 1}
                            className="p-1 rounded hover:bg-zinc-100 text-zinc-400 hover:text-zinc-900 disabled:opacity-20 cursor-pointer"
                            title="Move Right"
                          >
                            <ArrowRight className="w-3 h-3 stroke-[2]" />
                          </button>
                        </div>
                      </div>

                      {/* Mock Sheet Preview Card */}
                      <div className="relative w-full aspect-[3/4] bg-zinc-50 border border-zinc-200 rounded-lg overflow-hidden p-3 flex flex-col justify-between mb-3">
                        {/* Rotation indicator badge */}
                        {tile.rotation > 0 && (
                          <span className="absolute top-1.5 right-1.5 px-1.5 py-0.5 rounded bg-zinc-950 text-white font-mono text-[9px] font-bold">
                            {tile.rotation}°
                          </span>
                        )}

                        <div
                          className="w-full h-full flex flex-col justify-between transition-transform duration-200"
                          style={{ transform: `rotate(${tile.rotation}deg)` }}
                        >
                          <div className="space-y-1.5 opacity-40">
                            <div className="h-1.5 w-1/2 bg-zinc-400 rounded" />
                            <div className="h-1 w-full bg-zinc-300 rounded" />
                            <div className="h-1 w-5/6 bg-zinc-300 rounded" />
                            <div className="h-1 w-4/6 bg-zinc-300 rounded" />
                          </div>

                          <div className="text-center font-mono text-[10px] text-zinc-400">
                            Source: P{tile.originalIndex + 1}
                          </div>

                          <div className="space-y-1 opacity-30">
                            <div className="h-1 w-full bg-zinc-300 rounded" />
                            <div className="h-1 w-3/4 bg-zinc-300 rounded" />
                          </div>
                        </div>
                      </div>

                      {/* Bottom Action Strip */}
                      <div className="flex items-center justify-between pt-1 border-t border-zinc-100 text-xs">
                        <button
                          type="button"
                          onClick={(e) => { e.stopPropagation(); rotateTile(index); }}
                          className="p-1.5 rounded-lg hover:bg-zinc-100 text-zinc-600 hover:text-zinc-950 transition-colors cursor-pointer"
                          title="Rotate 90° Clockwise"
                        >
                          <RotateCw className="w-3.5 h-3.5 stroke-[1.8]" />
                        </button>

                        <button
                          type="button"
                          onClick={(e) => { e.stopPropagation(); duplicateTile(index); }}
                          className="p-1.5 rounded-lg hover:bg-zinc-100 text-zinc-600 hover:text-zinc-950 transition-colors cursor-pointer"
                          title="Duplicate Page"
                        >
                          <Copy className="w-3.5 h-3.5 stroke-[1.8]" />
                        </button>

                        <button
                          type="button"
                          onClick={(e) => { e.stopPropagation(); deleteTile(index); }}
                          className="p-1.5 rounded-lg hover:bg-rose-50 text-zinc-400 hover:text-rose-600 transition-colors cursor-pointer"
                          title="Delete Page"
                        >
                          <Trash2 className="w-3.5 h-3.5 stroke-[1.8]" />
                        </button>
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Action Execution Bar */}
            <div className="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 rounded-3xl bg-zinc-900 text-white shadow-lg">
              <div>
                <div className="text-sm font-bold flex items-center gap-2">
                  <ShieldCheck className="w-4 h-4 text-emerald-400 stroke-[2]" />
                  Ready to Export Organized PDF
                </div>
                <p className="text-xs text-zinc-400 mt-0.5">
                  Re-binding {pageTiles.length} page(s) in custom sequence • Lossless in browser memory
                </p>
              </div>

              {!organizedBytes ? (
                <button
                  type="button"
                  onClick={handleSaveOrganized}
                  disabled={isProcessing || pageTiles.length === 0}
                  className="w-full sm:w-auto px-6 py-3 rounded-2xl bg-white hover:bg-zinc-100 text-zinc-950 text-sm font-bold flex items-center justify-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shadow-md active:scale-95"
                >
                  {isProcessing ? (
                    <>
                      <div className="w-4 h-4 border-2 border-zinc-950 border-t-transparent rounded-full animate-spin" />
                      Organizing In Memory...
                    </>
                  ) : (
                    <>
                      <Layers className="w-4 h-4 stroke-[2]" />
                      Save & Organize PDF
                    </>
                  )}
                </button>
              ) : (
                <button
                  type="button"
                  onClick={handleDownload}
                  className="w-full sm:w-auto px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-sm font-bold flex items-center justify-center gap-2 transition-all cursor-pointer shadow-md active:scale-95"
                >
                  <Download className="w-4 h-4 stroke-[2]" />
                  Download Organized PDF
                </button>
              )}
            </div>

            {/* Success Banner */}
            {organizedBytes && (
              <div className="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between gap-3 text-xs font-mono animate-in fade-in">
                <div className="flex items-center gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-700 stroke-[2]" />
                  <span>Organized PDF generated with {pageTiles.length} pages! Size: <strong>{formatBytes(organizedBytes.length)}</strong></span>
                </div>
                <button
                  type="button"
                  onClick={handleDownload}
                  className="underline font-bold hover:text-emerald-950 cursor-pointer"
                >
                  Save File
                </button>
              </div>
            )}

          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
