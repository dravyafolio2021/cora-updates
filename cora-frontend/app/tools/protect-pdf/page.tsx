'use client';

import React, { useState, useRef, useCallback } from 'react';
import { 
  FileText, 
  Download, 
  ShieldCheck, 
  Lock, 
  Eye, 
  EyeOff, 
  RotateCcw, 
  Printer, 
  Copy, 
  Edit3, 
  CheckCircle2, 
  KeyRound,
  FileKey,
  ShieldAlert
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo, protectPdf, downloadPdfBlob, PageInfo } from '@/lib/pdf-engine';

const PROTECT_PDF_FAQS = [
  {
    question: 'What encryption standard does Cora use to protect PDFs?',
    answer: 'Cora uses military-grade AES-256 (Advanced Encryption Standard with 256-bit keys, PDF ISO 32000-1 Revision 5 standard). It applies SHA-256 key derivation with salted hashes and AES-CBC block ciphering directly inside your browser memory.'
  },
  {
    question: 'Can someone open the protected PDF without knowing the password?',
    answer: 'No. AES-256 encryption is mathematically intractable to crack via brute force. Without the exact user or owner password, the encrypted streams and text payloads cannot be parsed or viewed by any PDF reader.'
  },
  {
    question: 'Are my passwords or confidential documents uploaded to any server?',
    answer: 'Never. Cora runs 100% locally in your browser using pure client-side Web Crypto and WebAssembly. Your files, metadata, and plain-text passwords never travel across the internet or touch any cloud server.'
  },
  {
    question: 'What is the difference between an Open Password and Permission Restrictions?',
    answer: 'An Open Password (User Password) is required to decrypt and view the document contents. Permission Restrictions (Owner Password) allow recipients to read the document while legally and cryptographically blocking printing, text extraction, form modification, and page rearrangement.'
  },
  {
    question: 'Will protected PDFs open in standard viewers like Adobe Acrobat, Chrome, and Apple Preview?',
    answer: 'Yes. The resulting file strictly adheres to official Adobe PDF 1.7 / 2.0 security specifications. Adobe Acrobat, macOS Preview, Google Chrome, Edge, and iOS Files will natively prompt for the password and enforce permission restrictions.'
  }
];

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function calculatePasswordStrength(pass: string): { score: number; label: string; color: string } {
  if (!pass) return { score: 0, label: 'Empty', color: 'bg-zinc-200' };
  let score = 0;
  if (pass.length >= 8) score += 1;
  if (pass.length >= 12) score += 1;
  if (/[A-Z]/.test(pass) && /[a-z]/.test(pass)) score += 1;
  if (/[0-9]/.test(pass)) score += 1;
  if (/[^A-Za-z0-9]/.test(pass)) score += 1;

  if (score <= 2) return { score, label: 'Weak', color: 'bg-rose-500' };
  if (score <= 3) return { score, label: 'Moderate', color: 'bg-amber-500' };
  if (score === 4) return { score, label: 'Strong', color: 'bg-emerald-500' };
  return { score, label: 'Enterprise Grade', color: 'bg-zinc-950' };
}

export default function ProtectPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [, setPages] = useState<PageInfo[]>([]);
  const [, setIsLoading] = useState<boolean>(false);
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);

  // Form states
  const [userPassword, setUserPassword] = useState<string>('');
  const [confirmPassword, setConfirmPassword] = useState<string>('');
  const [showUserPassword, setShowUserPassword] = useState<boolean>(false);
  const [useSeparateOwnerPassword, setUseSeparateOwnerPassword] = useState<boolean>(false);
  const [ownerPassword, setOwnerPassword] = useState<string>('');
  const [showOwnerPassword, setShowOwnerPassword] = useState<boolean>(false);

  // Granular permissions
  const [allowPrinting, setAllowPrinting] = useState<boolean>(false);
  const [allowCopying, setAllowCopying] = useState<boolean>(false);
  const [allowModifying, setAllowModifying] = useState<boolean>(false);
  const [allowAnnotating, setAllowAnnotating] = useState<boolean>(false);

  // Completed result
  const [securedBytes, setSecuredBytes] = useState<Uint8Array | null>(null);

  const strength = calculatePasswordStrength(userPassword);

  const handleFileLoad = async (loadedFile: File) => {
    if (loadedFile.type !== 'application/pdf' && !loadedFile.name.toLowerCase().endsWith('.pdf')) {
      showToast('Please select a valid PDF document');
      return;
    }

    setIsLoading(true);
    try {
      const info = await getPdfInfo(loadedFile);
      setPdfFile(loadedFile);
      setPageCount(info.pageCount);
      setPages(info.pages);
      setSecuredBytes(null);
      showToast('Loaded ' + loadedFile.name + ' (' + info.pageCount + ' pages)');
    } catch {
      showToast('Unable to read PDF. It may already be encrypted.');
    } finally {
      setIsLoading(false);
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
    setPageCount(0);
    setPages([]);
    setUserPassword('');
    setConfirmPassword('');
    setOwnerPassword('');
    setUseSeparateOwnerPassword(false);
    setSecuredBytes(null);
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  const handleProtect = async () => {
    if (!pdfFile) return;

    if (!userPassword.trim()) {
      showToast('Please enter a password to protect the document');
      return;
    }

    if (userPassword !== confirmPassword) {
      showToast('Passwords do not match. Please verify your entries.');
      return;
    }

    if (userPassword.length < 4) {
      showToast('Password should be at least 4 characters long');
      return;
    }

    setIsProcessing(true);
    try {
      const encryptedBytes = await protectPdf(pdfFile, {
        userPassword: userPassword.trim(),
        ownerPassword: useSeparateOwnerPassword && ownerPassword.trim() ? ownerPassword.trim() : undefined,
        permissions: {
          allowPrinting,
          allowCopying,
          allowModifying,
          allowAnnotating,
        },
      });

      setSecuredBytes(encryptedBytes);
      showToast('PDF encrypted successfully with AES-256!');
    } catch (err: any) {
      showToast(err?.message || 'Failed to encrypt PDF. Please try again.');
    } finally {
      setIsProcessing(false);
    }
  };

  const handleDownload = () => {
    if (!securedBytes || !pdfFile) return;
    const baseName = pdfFile.name.replace(/\.pdf$/i, '');
    const outName = `${baseName}_protected.pdf`;
    downloadPdfBlob(securedBytes, outName);
    showToast('Encrypted PDF downloaded');
  };

  return (
    <ToolPageShell
      toolId="protect-pdf"
      badgeTag="AES-256 PDF Security"
      title="Protect PDF with Password & Permissions"
      subtitle="Encrypt confidential PDF documents with military-grade AES-256 security. Set open passwords, disable printing, block copying, and lock modifications 100% in your browser."
      faqItems={PROTECT_PDF_FAQS}
      relatedToolSlugs={['unlock-pdf', 'redact-pdf', 'esign-pdf', 'compress-pdf']}
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
              <Lock className="w-8 h-8 stroke-[1.8]" />
            </div>

            <h3 className="text-lg sm:text-xl font-bold text-zinc-950 mb-1">
              Select or Drop PDF to Encrypt
            </h3>
            <p className="text-sm text-zinc-500 max-w-md mx-auto mb-4">
              Add passwords and permission locks to commercial contracts, audit reports, or personal tax filings.
            </p>

            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 text-xs font-mono text-zinc-600">
              <ShieldCheck className="w-3.5 h-3.5 stroke-[1.8] text-zinc-900" />
              <span>100% Private Client-Side AES-256 Memory Processing</span>
            </div>
          </div>
        ) : (
          /* Step 2: Configure Password & Permissions */
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
                    <span>•</span>
                    <span className="text-zinc-900 font-semibold">{pageCount} pages</span>
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

            {/* Password Configuration Card */}
            <div className="p-6 sm:p-8 rounded-3xl bg-white border border-zinc-200 shadow-xs space-y-6">
              <div>
                <h3 className="text-base font-bold text-zinc-950 flex items-center gap-2">
                  <KeyRound className="w-4 h-4 stroke-[1.8]" />
                  1. Set Document Open Password
                </h3>
                <p className="text-xs text-zinc-500 mt-1">
                  This password will be required every time someone attempts to open or view this PDF.
                </p>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {/* Enter Password */}
                <div>
                  <label className="block text-xs font-mono font-bold uppercase tracking-wider text-zinc-700 mb-1.5">
                    User Open Password *
                  </label>
                  <div className="relative">
                    <input
                      type={showUserPassword ? 'text' : 'password'}
                      value={userPassword}
                      onChange={(e) => setUserPassword(e.target.value)}
                      placeholder="Enter secure password"
                      className="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-zinc-300 bg-white text-sm font-mono text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950"
                    />
                    <button
                      type="button"
                      onClick={() => setShowUserPassword(!showUserPassword)}
                      className="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-700"
                    >
                      {showUserPassword ? <EyeOff className="w-4 h-4 stroke-[1.8]" /> : <Eye className="w-4 h-4 stroke-[1.8]" />}
                    </button>
                  </div>
                </div>

                {/* Confirm Password */}
                <div>
                  <label className="block text-xs font-mono font-bold uppercase tracking-wider text-zinc-700 mb-1.5">
                    Confirm Password *
                  </label>
                  <input
                    type="password"
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    placeholder="Re-type password"
                    className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 bg-white text-sm font-mono text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950"
                  />
                </div>
              </div>

              {/* Password Strength Indicator */}
              {userPassword && (
                <div className="space-y-1.5 pt-1">
                  <div className="flex items-center justify-between text-xs font-mono">
                    <span className="text-zinc-500">Cryptographic Strength:</span>
                    <span className="font-bold text-zinc-900">{strength.label}</span>
                  </div>
                  <div className="w-full h-1.5 rounded-full bg-zinc-100 overflow-hidden">
                    <div
                      className={`h-full transition-all duration-300 ${strength.color}`}
                      style={{ width: `${Math.min(100, (strength.score / 5) * 100)}%` }}
                    />
                  </div>
                </div>
              )}

              {/* Separate Owner Password Toggle */}
              <div className="pt-2 border-t border-zinc-100">
                <label className="flex items-center gap-3 cursor-pointer select-none">
                  <input
                    type="checkbox"
                    checked={useSeparateOwnerPassword}
                    onChange={(e) => setUseSeparateOwnerPassword(e.target.checked)}
                    className="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-950"
                  />
                  <div>
                    <span className="text-xs font-semibold text-zinc-900 block">
                      Configure separate Master / Owner Password (Optional)
                    </span>
                    <span className="text-[11px] text-zinc-500 block">
                      Master password retains administrative ability to alter permission restrictions later.
                    </span>
                  </div>
                </label>

                {useSeparateOwnerPassword && (
                  <div className="mt-3 max-w-sm">
                    <div className="relative">
                      <input
                        type={showOwnerPassword ? 'text' : 'password'}
                        value={ownerPassword}
                        onChange={(e) => setOwnerPassword(e.target.value)}
                        placeholder="Master Administrator Password"
                        className="w-full pl-3.5 pr-10 py-2 rounded-xl border border-zinc-300 bg-white text-sm font-mono text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950"
                      />
                      <button
                        type="button"
                        onClick={() => setShowOwnerPassword(!showOwnerPassword)}
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-700"
                      >
                        {showOwnerPassword ? <EyeOff className="w-4 h-4 stroke-[1.8]" /> : <Eye className="w-4 h-4 stroke-[1.8]" />}
                      </button>
                    </div>
                  </div>
                )}
              </div>
            </div>

            {/* Granular Permissions Card */}
            <div className="p-6 sm:p-8 rounded-3xl bg-white border border-zinc-200 shadow-xs space-y-4">
              <div>
                <h3 className="text-base font-bold text-zinc-950 flex items-center gap-2">
                  <FileKey className="w-4 h-4 stroke-[1.8]" />
                  2. Document Permission Restrictions
                </h3>
                <p className="text-xs text-zinc-500 mt-1">
                  Specify allowed recipient capabilities once the PDF is unlocked with the password.
                </p>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                {/* Printing */}
                <div 
                  onClick={() => setAllowPrinting(!allowPrinting)}
                  className={`p-3.5 rounded-2xl border transition-all cursor-pointer select-none flex items-start gap-3 ${
                    allowPrinting 
                      ? 'border-zinc-900 bg-zinc-50/70 shadow-2xs' 
                      : 'border-zinc-200 bg-white hover:border-zinc-300'
                  }`}
                >
                  <div className="mt-0.5">
                    <input
                      type="checkbox"
                      checked={allowPrinting}
                      onChange={() => {}}
                      className="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-950 pointer-events-none"
                    />
                  </div>
                  <div>
                    <span className="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
                      <Printer className="w-3.5 h-3.5 stroke-[1.8]" /> Allow Printing
                    </span>
                    <p className="text-[11px] text-zinc-500 mt-0.5">
                      {allowPrinting ? 'Recipients can print paper copies.' : 'Printing disabled (digital screen read-only).'}
                    </p>
                  </div>
                </div>

                {/* Content Copying */}
                <div 
                  onClick={() => setAllowCopying(!allowCopying)}
                  className={`p-3.5 rounded-2xl border transition-all cursor-pointer select-none flex items-start gap-3 ${
                    allowCopying 
                      ? 'border-zinc-900 bg-zinc-50/70 shadow-2xs' 
                      : 'border-zinc-200 bg-white hover:border-zinc-300'
                  }`}
                >
                  <div className="mt-0.5">
                    <input
                      type="checkbox"
                      checked={allowCopying}
                      onChange={() => {}}
                      className="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-950 pointer-events-none"
                    />
                  </div>
                  <div>
                    <span className="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
                      <Copy className="w-3.5 h-3.5 stroke-[1.8]" /> Allow Copying Text & Images
                    </span>
                    <p className="text-[11px] text-zinc-500 mt-0.5">
                      {allowCopying ? 'Text can be selected and copied.' : 'Prevents clipboard text/image extraction.'}
                    </p>
                  </div>
                </div>

                {/* Modifications */}
                <div 
                  onClick={() => setAllowModifying(!allowModifying)}
                  className={`p-3.5 rounded-2xl border transition-all cursor-pointer select-none flex items-start gap-3 ${
                    allowModifying 
                      ? 'border-zinc-900 bg-zinc-50/70 shadow-2xs' 
                      : 'border-zinc-200 bg-white hover:border-zinc-300'
                  }`}
                >
                  <div className="mt-0.5">
                    <input
                      type="checkbox"
                      checked={allowModifying}
                      onChange={() => {}}
                      className="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-950 pointer-events-none"
                    />
                  </div>
                  <div>
                    <span className="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
                      <Edit3 className="w-3.5 h-3.5 stroke-[1.8]" /> Allow Document Modifications
                    </span>
                    <p className="text-[11px] text-zinc-500 mt-0.5">
                      {allowModifying ? 'Pages can be inserted or rotated.' : 'Locks page layout & structure.'}
                    </p>
                  </div>
                </div>

                {/* Annotations */}
                <div 
                  onClick={() => setAllowAnnotating(!allowAnnotating)}
                  className={`p-3.5 rounded-2xl border transition-all cursor-pointer select-none flex items-start gap-3 ${
                    allowAnnotating 
                      ? 'border-zinc-900 bg-zinc-50/70 shadow-2xs' 
                      : 'border-zinc-200 bg-white hover:border-zinc-300'
                  }`}
                >
                  <div className="mt-0.5">
                    <input
                      type="checkbox"
                      checked={allowAnnotating}
                      onChange={() => {}}
                      className="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-950 pointer-events-none"
                    />
                  </div>
                  <div>
                    <span className="text-xs font-bold text-zinc-900 flex items-center gap-1.5">
                      <ShieldAlert className="w-3.5 h-3.5 stroke-[1.8]" /> Allow Annotating & Forms
                    </span>
                    <p className="text-[11px] text-zinc-500 mt-0.5">
                      {allowAnnotating ? 'Recipients can highlight or fill forms.' : 'Blocks form filling & comments.'}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            {/* Action Bar */}
            <div className="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 rounded-3xl bg-zinc-900 text-white shadow-lg">
              <div>
                <div className="text-sm font-bold flex items-center gap-2">
                  <ShieldCheck className="w-4 h-4 text-emerald-400 stroke-[2]" />
                  Ready to Apply AES-256 Encryption
                </div>
                <p className="text-xs text-zinc-400 mt-0.5">
                  {userPassword.trim() ? 'Password verified • Output file stays 100% in browser memory' : 'Please input a password above to continue'}
                </p>
              </div>

              {!securedBytes ? (
                <button
                  type="button"
                  onClick={handleProtect}
                  disabled={isProcessing || !userPassword.trim()}
                  className="w-full sm:w-auto px-6 py-3 rounded-2xl bg-white hover:bg-zinc-100 text-zinc-950 text-sm font-bold flex items-center justify-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shadow-md active:scale-95"
                >
                  {isProcessing ? (
                    <>
                      <div className="w-4 h-4 border-2 border-zinc-950 border-t-transparent rounded-full animate-spin" />
                      Encrypting In Memory...
                    </>
                  ) : (
                    <>
                      <Lock className="w-4 h-4 stroke-[2]" />
                      Encrypt & Protect PDF
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
                  Download Protected PDF
                </button>
              )}
            </div>

            {/* Encryption Confirmation Banner */}
            {securedBytes && (
              <div className="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between gap-3 text-xs font-mono animate-in fade-in">
                <div className="flex items-center gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-700 stroke-[2]" />
                  <span>Document encrypted! Output size: <strong>{formatBytes(securedBytes.length)}</strong></span>
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
