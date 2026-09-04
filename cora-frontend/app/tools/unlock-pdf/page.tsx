'use client';

import React, { useState, useRef, useCallback } from 'react';
import { 
  FileText, 
  Download, 
  ShieldCheck, 
  Unlock, 
  Lock, 
  Eye, 
  EyeOff, 
  RotateCcw, 
  CheckCircle2, 
  KeyRound,
  FileCheck2,
  Sparkles
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { unlockPdf, downloadPdfBlob, getPdfInfo } from '@/lib/pdf-engine';

const UNLOCK_PDF_FAQS = [
  {
    question: 'Can I unlock a PDF if I forgot the open password?',
    answer: 'Standard AES-256 encrypted documents require the open password to derive the mathematical decryption key. However, documents locked with owner restrictions (such as disabled printing, content copying, or form editing) can be unlocked instantly without entering any password.'
  },
  {
    question: 'Does unlocking remove print, copy, and edit restrictions permanently?',
    answer: 'Yes. Cora reconstructs the PDF object tree in browser memory without the encryption dictionary. The downloaded file will open in any viewer (Adobe Acrobat, Preview, Chrome) without ever asking for a password or blocking copy/print actions.'
  },
  {
    question: 'Are my private bank statements or salary slips uploaded to any server?',
    answer: 'Zero bytes leave your computer. All cryptographic decryption and byte manipulation occurs 100% locally in your web browser memory. Your confidential financial data and passwords never touch an external server.'
  },
  {
    question: 'Why do Indian bank statements and salary slips have passwords?',
    answer: 'Financial institutions (such as HDFC, SBI, ICICI, and Axis Bank) password-protect monthly statements using predictable personal identifiers like your DOB (DDMMYYYY) or PAN. Once unlocked with Cora, you can store and share a clean, restriction-free version.'
  },
  {
    question: 'Is it legal to unlock my own documents and tax returns?',
    answer: 'Yes. Under Indian and international copyright and privacy laws, you possess the full legal right to decrypt, remove restrictions from, and manage digital documents that belong to you or for which you have explicit authorization.'
  }
];

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

export default function UnlockPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [password, setPassword] = useState<string>('');
  const [showPassword, setShowPassword] = useState<boolean>(false);
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [requiresPassword, setRequiresPassword] = useState<boolean>(false);
  const [unlockedBytes, setUnlockedBytes] = useState<Uint8Array | null>(null);
  const [pageCount, setPageCount] = useState<number | null>(null);

  const handleFileLoad = async (loadedFile: File) => {
    if (loadedFile.type !== 'application/pdf' && !loadedFile.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please select a valid PDF file');
      return;
    }

    setPdfFile(loadedFile);
    setUnlockedBytes(null);
    setPassword('');

    // Try inspecting document to see if open password is required
    try {
      const info = await getPdfInfo(loadedFile);
      setPageCount(info.pageCount);
      setRequiresPassword(false);
      showToast('File loaded. Ready to strip restrictions.');
    } catch {
      // Document is likely encrypted with an open password
      setRequiresPassword(true);
      setPageCount(null);
      showToast('This document is encrypted. Please enter the password.');
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
    setPassword('');
    setRequiresPassword(false);
    setUnlockedBytes(null);
    setPageCount(null);
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  const handleUnlock = async () => {
    if (!pdfFile) return;

    if (requiresPassword && !password.trim()) {
      showToast('Please enter the password to unlock this document');
      return;
    }

    setIsProcessing(true);
    try {
      const cleanBytes = await unlockPdf(pdfFile, password.trim() || undefined);
      setUnlockedBytes(cleanBytes);
      showToast('PDF unlocked and restrictions stripped successfully!');
    } catch (err: any) {
      showToast(err?.message || 'Incorrect password or unsupported encryption format.');
    } finally {
      setIsProcessing(false);
    }
  };

  const handleDownload = () => {
    if (!unlockedBytes || !pdfFile) return;
    const baseName = pdfFile.name.replace(/\.pdf$/i, '');
    const outName = `${baseName}_unlocked.pdf`;
    downloadPdfBlob(unlockedBytes, outName);
    showToast('Unlocked PDF downloaded');
  };

  return (
    <ToolPageShell
      toolId="unlock-pdf"
      badgeTag="Instant PDF Decryption"
      title="Unlock PDF & Strip Security Restrictions"
      subtitle="Remove password protection, print restrictions, and copy blocks from PDF files 100% in your browser. Fast, permanent, and private client-side decryption."
      faqItems={UNLOCK_PDF_FAQS}
      relatedToolSlugs={['protect-pdf', 'redact-pdf', 'compress-pdf', 'organize-pdf']}
    >
      <div className="max-w-4xl mx-auto space-y-8">
        
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
              <Unlock className="w-8 h-8 stroke-[1.8]" />
            </div>

            <h3 className="text-lg sm:text-xl font-bold text-zinc-950 mb-1">
              Select or Drop Locked PDF
            </h3>
            <p className="text-sm text-zinc-500 max-w-md mx-auto mb-4">
              Unlock password-protected statements, remove print blocks, and eliminate copy restrictions.
            </p>

            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 text-xs font-mono text-zinc-600">
              <ShieldCheck className="w-3.5 h-3.5 stroke-[1.8] text-zinc-900" />
              <span>100% In-Browser Memory Decryption • Zero Cloud Uploads</span>
            </div>
          </div>
        ) : (
          /* Step 2: Decrypt & Unlock Workspace */
          <div className="space-y-6">
            
            {/* File Info Bar */}
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
                    {pageCount !== null && (
                      <>
                        <span>•</span>
                        <span className="text-zinc-900 font-semibold">{pageCount} pages</span>
                      </>
                    )}
                  </p>
                </div>
              </div>

              <button
                type="button"
                onClick={resetAll}
                className="px-3 py-1.5 rounded-lg border border-zinc-200 hover:bg-zinc-100 text-zinc-700 text-xs font-medium flex items-center gap-1.5 transition-colors cursor-pointer"
              >
                <RotateCcw className="w-3.5 h-3.5 stroke-[1.8]" />
                Change Document
              </button>
            </div>

            {/* Password Entry Panel */}
            <div className="p-6 sm:p-8 rounded-3xl bg-white border border-zinc-200 shadow-xs space-y-5">
              <div>
                <h3 className="text-base font-bold text-zinc-950 flex items-center gap-2">
                  <KeyRound className="w-4 h-4 stroke-[1.8]" />
                  {requiresPassword ? 'Enter Password to Decrypt' : 'Strip Security & Restrictions'}
                </h3>
                <p className="text-xs text-zinc-500 mt-1">
                  {requiresPassword
                    ? 'This PDF requires an open password to decrypt the internal streams.'
                    : 'Owner permission locks (print, copy, modify) can be permanently stripped with one click.'}
                </p>
              </div>

              {requiresPassword ? (
                <div className="max-w-md space-y-3">
                  <label className="block text-xs font-mono font-bold uppercase tracking-wider text-zinc-700">
                    Document Password *
                  </label>
                  <div className="relative">
                    <input
                      type={showPassword ? 'text' : 'password'}
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      placeholder="Enter file password"
                      className="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-zinc-300 bg-white text-sm font-mono text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950"
                      onKeyDown={(e) => {
                        if (e.key === 'Enter') handleUnlock();
                      }}
                    />
                    <button
                      type="button"
                      onClick={() => setShowPassword(!showPassword)}
                      className="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-700"
                    >
                      {showPassword ? <EyeOff className="w-4 h-4 stroke-[1.8]" /> : <Eye className="w-4 h-4 stroke-[1.8]" />}
                    </button>
                  </div>
                  <p className="text-[11px] text-zinc-400">
                    Common formats for Indian bank statements: Date of birth (DDMMYYYY), PAN number, or last 4 digits of account.
                  </p>
                </div>
              ) : (
                <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-start gap-3">
                  <div className="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0 mt-0.5">
                    <Sparkles className="w-4 h-4 stroke-[2]" />
                  </div>
                  <div>
                    <h4 className="text-xs font-bold text-zinc-900">
                      Zero Open Password Required
                    </h4>
                    <p className="text-[11px] text-zinc-500 mt-0.5">
                      This document can be opened directly. Clicking Unlock will strip all lingering printing blocks, copy barriers, and digital rights restrictions cleanly.
                    </p>
                  </div>
                </div>
              )}

              {/* Action Bar */}
              <div className="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-zinc-100">
                <div className="flex items-center gap-2 text-xs font-mono text-zinc-500">
                  <ShieldCheck className="w-4 h-4 text-zinc-900 stroke-[1.8]" />
                  <span>Decrypted output will be 100% DRM & restriction-free</span>
                </div>

                {!unlockedBytes ? (
                  <button
                    type="button"
                    onClick={handleUnlock}
                    disabled={isProcessing || (requiresPassword && !password.trim())}
                    className="w-full sm:w-auto px-6 py-2.5 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white text-sm font-bold flex items-center justify-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shadow-xs active:scale-95"
                  >
                    {isProcessing ? (
                      <>
                        <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                        Decrypting Document...
                      </>
                    ) : (
                      <>
                        <Unlock className="w-4 h-4 stroke-[2]" />
                        Unlock & Decrypt PDF
                      </>
                    )}
                  </button>
                ) : (
                  <button
                    type="button"
                    onClick={handleDownload}
                    className="w-full sm:w-auto px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold flex items-center justify-center gap-2 transition-all cursor-pointer shadow-xs active:scale-95"
                  >
                    <Download className="w-4 h-4 stroke-[2]" />
                    Download Unlocked PDF
                  </button>
                )}
              </div>
            </div>

            {/* Success Download Banner */}
            {unlockedBytes && (
              <div className="p-5 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-in fade-in">
                <div className="flex items-start gap-3">
                  <div className="w-10 h-10 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center shrink-0 text-emerald-800">
                    <FileCheck2 className="w-5 h-5 stroke-[2]" />
                  </div>
                  <div>
                    <h4 className="text-sm font-bold">Document Successfully Decrypted!</h4>
                    <p className="text-xs text-emerald-800 mt-0.5">
                      All password security and restrictions have been purged. Clean size: {formatBytes(unlockedBytes.length)}
                    </p>
                  </div>
                </div>

                <button
                  type="button"
                  onClick={handleDownload}
                  className="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold flex items-center gap-1.5 transition-colors cursor-pointer shrink-0"
                >
                  <Download className="w-3.5 h-3.5 stroke-[2]" />
                  Save Clean File
                </button>
              </div>
            )}

          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
