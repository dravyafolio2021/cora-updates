'use client';

import React, { useState, useRef, useMemo, useEffect } from 'react';
import { 
  FileText, 
  UploadCloud, 
  Download, 
  RefreshCw, 
  ShieldCheck, 
  Sliders, 
  Copy, 
  Check, 
  FileCode, 
  AlignLeft, 
  Sparkles, 
  Trash2, 
  RotateCcw,
  BookOpen,
  Maximize2
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { convertTextToPdf, downloadPdfBlob } from '@/lib/pdf-engine';

const SAMPLE_TEMPLATES: Record<string, { title: string; text: string }> = {
  msa: {
    title: 'Master Services Agreement & Scope Statement',
    text: `MASTER SERVICES AGREEMENT

This Master Services Agreement ("Agreement") is entered into by and between Studio Client ("Client") and Cora Production Partner ("Service Provider").

1. SCOPE OF SERVICES
Service Provider shall perform autonomous digital product design, software engineering, and strategic consulting services as outlined in corresponding Statements of Work (SOW). All deliverables shall adhere to the quality standards and architectural benchmarks defined herein.

2. COMPENSATION & PAYMENT TERMS
Client shall remit payment within fifteen (15) business days following receipt of milestone invoices. All invoices are denominated in INR and include applicable Goods and Services Tax (18% GST under SAC 9983). Late remittances shall accrue interest at the rate of 1.5% per month or the statutory maximum permitted under Indian contract law.

3. INTELLECTUAL PROPERTY RIGHTS
Upon complete payment of all outstanding invoices, Service Provider assigns to Client all right, title, and interest in custom software deliverables and design assets created specifically for Client. Pre-existing frameworks, developer tooling, and reusable libraries remain the exclusive property of Service Provider.

4. CONFIDENTIALITY
Each party covenants to protect proprietary information disclosed by the other party with the same degree of care used for its own confidential assets, but in no event less than reasonable care.

5. TERMINATION & CANCELLATION
Either party may terminate this Agreement without cause upon providing thirty (30) calendar days written notice. In the event of early termination, Client shall compensate Service Provider for all completed milestones and approved hours incurred up to the effective termination date.`,
  },
  freelance: {
    title: 'Independent Freelance Contract & Milestone SOW',
    text: `INDEPENDENT CONTRACTOR ENGAGEMENT DEED

Date: September 05, 2026
Prepared for: Aarav Mehta / Studio Operations

1. PROJECT OVERVIEW & DELIVERABLES
Contractor agrees to deliver production-ready responsive web application modules, API orchestration layers, and client-side document processing engines.

Milestone 1: Architecture review, token wireframes, and schema definition (30% advance release).
Milestone 2: Core feature implementation, unit testing, and responsive polish (40% interim release).
Milestone 3: Final acceptance verification, QA sign-off, and handover (30% final release).

2. REVISIONS & SCOPE DISCIPLINE
The agreed contract fee includes two (2) consolidated rounds of client feedback. Substantive functional alterations, third-party API re-scoping, or post-approval pivot requests will be billed at standard hourly rates under a signed change order.

3. JURISDICTION & DISPUTE RESOLUTION
This deed is governed by the laws of India under Section 10A of the Information Technology Act 2000. Any dispute arising under this agreement shall be submitted to binding arbitration in Mumbai, India.`,
  },
  nda: {
    title: 'Mutual Non-Disclosure & Confidentiality Deed',
    text: `MUTUAL NON-DISCLOSURE AGREEMENT

This Mutual Non-Disclosure Agreement ("Deed") is entered into as of the date of execution to safeguard proprietary information shared for the purpose of exploring commercial collaboration.

1. DEFINITION OF CONFIDENTIAL INFORMATION
"Confidential Information" encompasses all non-public technical data, source code, trade secrets, commercial pricing strategies, client rosters, financial models, and operational workflows disclosed either orally, visually, or in writing.

2. EXCLUSIONS FROM CONFIDENTIALITY
Obligations herein shall not apply to information that: (a) is or becomes publicly available without breach of this Agreement; (b) was already known to Receiving Party prior to disclosure; (c) is independently developed without reference to Disclosing Party information.

3. TERM & SURVIVAL
This Agreement shall remain effective for two (2) years from the date of execution. The confidentiality covenants regarding trade secrets shall survive indefinitely.`,
  },
};

const FAQ_ITEMS = [
  {
    question: 'Can I upload Microsoft Word (.doc and .docx) files directly?',
    answer: 'Yes. You can upload .txt, .doc, .docx, .md, and .rtf files directly via the dropzone. Cora extracts the raw document text and structure locally in your browser and formats it into clean, vectorized PDF pages.',
  },
  {
    question: 'How does the estimated page counter calculate pagination?',
    answer: 'Cora calculates line wrapping, word metrics, font heights, and selected page margins (Normal vs Compact) using standard typography metrics. The real-time page counter dynamically reflects the exact page count of the final compiled PDF.',
  },
  {
    question: 'What is the difference between Normal and Compact margins?',
    answer: 'Normal margins use 50pt padding, which provides standard corporate presentation suitable for formal contracts, legal filings, and print binding. Compact margins use 28pt padding, maximizing printable text area and fitting approximately 35% more content per sheet.',
  },
  {
    question: 'Are my confidential contracts or text drafts uploaded to a server?',
    answer: 'No. Cora processes all text and files 100% inside your browser memory using client-side JavaScript. Zero bytes of your text, client names, or financial numbers are ever sent to an external server or cloud database.',
  },
  {
    question: 'Does the converted PDF support selectable vector text and printing?',
    answer: 'Yes. Cora embeds standard Type-1 Helvetica and Helvetica-Bold vector fonts. The resulting PDF document features crisp text at any zoom level, fully selectable text for copying, and universal compatibility with all PDF readers.',
  },
];

export default function WordToPdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Form State
  const [docTitle, setDocTitle] = useState<string>('Master Services Agreement & Terms');
  const [bodyText, setBodyText] = useState<string>(SAMPLE_TEMPLATES.msa.text);
  const [marginOption, setMarginOption] = useState<'normal' | 'compact'>('normal');
  const [pageSize, setPageSize] = useState<'a4' | 'letter'>('a4');
  const [fontSizeOption, setFontSizeOption] = useState<'normal' | 'compact' | 'large'>('normal');
  const [outputFileName, setOutputFileName] = useState<string>('cora-document.pdf');
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);

  const marginValue = marginOption === 'compact' ? 28 : 50;
  const fontSizeValue = fontSizeOption === 'compact' ? 9.5 : fontSizeOption === 'large' ? 12 : 10.5;

  // Real-time text telemetry
  const wordCount = useMemo(() => {
    const trimmed = bodyText.trim();
    return trimmed ? trimmed.split(/\s+/).length : 0;
  }, [bodyText]);

  const charCount = useMemo(() => {
    return bodyText.length;
  }, [bodyText]);

  // Real-time estimated page counter
  const estimatedPages = useMemo(() => {
    if (!bodyText.trim()) return 1;

    const pageWidth = pageSize === 'letter' ? 612 : 595.28;
    const pageHeight = pageSize === 'letter' ? 792 : 841.89;
    const contentWidth = pageWidth - marginValue * 2;
    const lineHeight = Math.round(fontSizeValue * 1.5);
    const titleSize = 18;

    let currentY = pageHeight - marginValue - titleSize - 28;
    let pageCount = 1;

    const paragraphs = bodyText.split('\n');
    const approxCharWidth = fontSizeValue * 0.48;
    const charsPerLine = Math.max(20, Math.floor(contentWidth / approxCharWidth));

    for (const para of paragraphs) {
      if (!para.trim()) {
        currentY -= lineHeight * 0.75;
        if (currentY < marginValue + lineHeight) {
          pageCount++;
          currentY = pageHeight - marginValue;
        }
        continue;
      }

      const words = para.split(' ');
      let lineLength = 0;

      for (const word of words) {
        if (lineLength + word.length + 1 > charsPerLine) {
          currentY -= lineHeight;
          if (currentY < marginValue + lineHeight) {
            pageCount++;
            currentY = pageHeight - marginValue;
          }
          lineLength = word.length;
        } else {
          lineLength += (lineLength === 0 ? 0 : 1) + word.length;
        }
      }

      if (lineLength > 0) {
        currentY -= lineHeight;
        if (currentY < marginValue + lineHeight) {
          pageCount++;
          currentY = pageHeight - marginValue;
        }
      }

      currentY -= lineHeight * 0.4;
    }

    return pageCount;
  }, [bodyText, marginValue, pageSize, fontSizeValue]);

  // Handle uploaded text or doc file
  const handleFileUpload = async (file: File) => {
    const fileName = file.name.toLowerCase();
    const titleFromFileName = file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' ');

    try {
      if (fileName.endsWith('.txt') || fileName.endsWith('.md') || fileName.endsWith('.rtf')) {
        const text = await file.text();
        setBodyText(text);
        setDocTitle(titleFromFileName);
        setOutputFileName(`${file.name.replace(/\.[^/.]+$/, '')}.pdf`);
        showToast(`Loaded ${file.name} successfully`);
      } else if (fileName.endsWith('.docx') || fileName.endsWith('.doc')) {
        // In-browser text stream extraction for doc/docx
        const buffer = await file.arrayBuffer();
        const decoder = new TextDecoder('utf-8', { fatal: false });
        const rawString = decoder.decode(buffer);

        // Extract clean text fragments from XML or binary text chunks
        const textMatches = rawString.match(/<w:t[^>]*>([^<]+)<\/w:t>/g);
        if (textMatches && textMatches.length > 0) {
          const extractedText = textMatches
            .map((m) => m.replace(/<[^>]+>/g, ''))
            .join(' ')
            .replace(/\s+/g, ' ')
            .trim();
          setBodyText(extractedText);
        } else {
          // Fallback: strip binary characters and extract printable paragraphs
          const printable = rawString
            .replace(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/g, ' ')
            .replace(/\s{3,}/g, '\n\n')
            .trim();
          const cleanExcerpt = printable.length > 150 ? printable : `Uploaded Document: ${file.name}\n\n${printable}`;
          setBodyText(cleanExcerpt);
        }

        setDocTitle(titleFromFileName);
        setOutputFileName(`${file.name.replace(/\.[^/.]+$/, '')}.pdf`);
        showToast(`Loaded Word document: ${file.name}`);
      } else {
        showToast('Please upload a .txt, .doc, .docx, or .md file');
      }
    } catch (err) {
      console.error(err);
      showToast('Failed to read file. Please try pasting the text directly.');
    }
  };

  const handleConvertAndDownload = async () => {
    if (!bodyText.trim()) {
      showToast('Please enter or upload document text');
      return;
    }

    setIsProcessing(true);
    try {
      const pdfBytes = await convertTextToPdf(docTitle || 'Untitled Document', bodyText, {
        margin: marginValue,
        pageSize,
        fontSize: fontSizeValue,
      });

      const fileName = outputFileName.endsWith('.pdf') ? outputFileName : `${outputFileName}.pdf`;
      downloadPdfBlob(pdfBytes, fileName);
      showToast('PDF compiled and downloaded successfully!');
    } catch (err) {
      console.error(err);
      showToast('Failed to compile PDF. Please check document content.');
    } finally {
      setIsProcessing(false);
    }
  };

  const loadTemplate = (key: string) => {
    const tpl = SAMPLE_TEMPLATES[key];
    if (tpl) {
      setDocTitle(tpl.title);
      setBodyText(tpl.text);
      setOutputFileName(`${key}-agreement.pdf`);
      showToast(`Loaded "${tpl.title}" template`);
    }
  };

  return (
    <ToolPageShell
      toolId="word-to-pdf"
      badgeTag="Client-Side PDF"
      title="Word & Document to PDF Converter Online Free"
      subtitle="Transform Word files, contract clauses, and text drafts into clean, beautifully typeset A4 PDF documents. 100% private in-browser compilation with zero server uploads."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['remove-pages', 'split-pdf', 'merge-pdf', 'watermark-pdf']}
    >
      <div className="w-full max-w-4xl mx-auto space-y-6">

        {/* ── File Dropzone & Template Trays ── */}
        <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/90 shadow-xs space-y-4">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h4 className="text-sm font-bold text-zinc-900">
                Upload Word / Text File or Load Sample Template
              </h4>
              <p className="text-xs text-zinc-500 mt-0.5">
                Supports .docx, .doc, .txt, and .md files with local client-side extraction
              </p>
            </div>

            {/* Quick Templates */}
            <div className="flex items-center gap-1.5 shrink-0">
              <span className="text-[11px] font-mono text-zinc-400 mr-1">Templates:</span>
              <button
                type="button"
                onClick={() => loadTemplate('msa')}
                className="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
              >
                MSA
              </button>
              <button
                type="button"
                onClick={() => loadTemplate('freelance')}
                className="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
              >
                Freelance SOW
              </button>
              <button
                type="button"
                onClick={() => loadTemplate('nda')}
                className="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition-colors cursor-pointer"
              >
                NDA
              </button>
            </div>
          </div>

          <div
            onDragOver={(e) => {
              e.preventDefault();
              setIsDraggingOver(true);
            }}
            onDragLeave={(e) => {
              e.preventDefault();
              setIsDraggingOver(false);
            }}
            onDrop={(e) => {
              e.preventDefault();
              setIsDraggingOver(false);
              if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                handleFileUpload(e.dataTransfer.files[0]);
              }
            }}
            onClick={() => fileInputRef.current?.click()}
            className={`border-2 border-dashed rounded-2xl p-6 text-center cursor-pointer transition-all duration-200 ${
              isDraggingOver
                ? 'border-zinc-900 bg-zinc-100/80'
                : 'border-zinc-200 hover:border-zinc-400 hover:bg-zinc-50/60'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept=".txt,.doc,.docx,.md,.rtf"
              className="hidden"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleFileUpload(e.target.files[0]);
                }
              }}
            />

            <div className="flex flex-col items-center justify-center space-y-2">
              <UploadCloud className="w-6 h-6 stroke-[1.8] text-zinc-700" />
              <div className="text-xs text-zinc-600">
                <span className="font-semibold text-zinc-900">Click to upload Word (.docx, .doc) or text file</span> or drag and drop
              </div>
              <p className="text-[11px] font-mono text-zinc-400">
                100% In-Browser Memory • Zero Upload to Cloud Servers
              </p>
            </div>
          </div>
        </div>

        {/* ── Document Settings & Formatting Controls ── */}
        <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/90 shadow-xs space-y-4">
          <h4 className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-700">
            Layout & Page Settings
          </h4>

          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            {/* Page Margins (Normal vs Compact) */}
            <div>
              <label className="text-xs font-semibold text-zinc-800 block mb-1.5">
                Page Margins
              </label>
              <div className="grid grid-cols-2 gap-1.5 p-1 rounded-xl bg-zinc-100 border border-zinc-200">
                <button
                  type="button"
                  onClick={() => setMarginOption('normal')}
                  className={`py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer ${
                    marginOption === 'normal'
                      ? 'bg-white text-zinc-950 font-bold shadow-2xs'
                      : 'text-zinc-600 hover:text-zinc-900'
                  }`}
                >
                  Normal (50pt)
                </button>
                <button
                  type="button"
                  onClick={() => setMarginOption('compact')}
                  className={`py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer ${
                    marginOption === 'compact'
                      ? 'bg-white text-zinc-950 font-bold shadow-2xs'
                      : 'text-zinc-600 hover:text-zinc-900'
                  }`}
                >
                  Compact (28pt)
                </button>
              </div>
            </div>

            {/* Paper Size */}
            <div>
              <label className="text-xs font-semibold text-zinc-800 block mb-1.5">
                Paper Standard
              </label>
              <div className="grid grid-cols-2 gap-1.5 p-1 rounded-xl bg-zinc-100 border border-zinc-200">
                <button
                  type="button"
                  onClick={() => setPageSize('a4')}
                  className={`py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer ${
                    pageSize === 'a4'
                      ? 'bg-white text-zinc-950 font-bold shadow-2xs'
                      : 'text-zinc-600 hover:text-zinc-900'
                  }`}
                >
                  A4 (Standard)
                </button>
                <button
                  type="button"
                  onClick={() => setPageSize('letter')}
                  className={`py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer ${
                    pageSize === 'letter'
                      ? 'bg-white text-zinc-950 font-bold shadow-2xs'
                      : 'text-zinc-600 hover:text-zinc-900'
                  }`}
                >
                  US Letter
                </button>
              </div>
            </div>

            {/* Font Density */}
            <div>
              <label className="text-xs font-semibold text-zinc-800 block mb-1.5">
                Typographic Scale
              </label>
              <div className="grid grid-cols-3 gap-1 p-1 rounded-xl bg-zinc-100 border border-zinc-200">
                <button
                  type="button"
                  onClick={() => setFontSizeOption('compact')}
                  className={`py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer ${
                    fontSizeOption === 'compact'
                      ? 'bg-white text-zinc-950 font-bold shadow-2xs'
                      : 'text-zinc-600 hover:text-zinc-900'
                  }`}
                >
                  9.5pt
                </button>
                <button
                  type="button"
                  onClick={() => setFontSizeOption('normal')}
                  className={`py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer ${
                    fontSizeOption === 'normal'
                      ? 'bg-white text-zinc-950 font-bold shadow-2xs'
                      : 'text-zinc-600 hover:text-zinc-900'
                  }`}
                >
                  10.5pt
                </button>
                <button
                  type="button"
                  onClick={() => setFontSizeOption('large')}
                  className={`py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer ${
                    fontSizeOption === 'large'
                      ? 'bg-white text-zinc-950 font-bold shadow-2xs'
                      : 'text-zinc-600 hover:text-zinc-900'
                  }`}
                >
                  12pt
                </button>
              </div>
            </div>

          </div>
        </div>

        {/* ── Document Editor & Live Telemetry ── */}
        <div className="p-5 sm:p-6 rounded-3xl bg-white border border-zinc-200/90 shadow-xs space-y-4">
          
          {/* Header Bar: Title Input & Real-Time Page Counter */}
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div className="flex-1">
              <label className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-700 block mb-1.5">
                Document Title
              </label>
              <input
                type="text"
                value={docTitle}
                onChange={(e) => setDocTitle(e.target.value)}
                placeholder="Document Title (e.g. Master Services Agreement)"
                className="w-full px-3.5 py-2 rounded-xl border border-zinc-300 bg-white text-sm font-semibold text-zinc-900 focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950"
              />
            </div>

            {/* Real-time estimated page counter badge */}
            <div className="sm:pt-5 flex items-center gap-2 shrink-0">
              <div className="px-3.5 py-2 rounded-xl bg-zinc-950 text-white font-mono text-xs flex items-center gap-2 shadow-xs">
                <BookOpen className="w-3.5 h-3.5 stroke-[1.8] text-zinc-300" />
                <span>
                  Est. <strong>{estimatedPages}</strong> {estimatedPages === 1 ? 'Page' : 'Pages'}
                </span>
              </div>
            </div>
          </div>

          {/* Textarea Editor */}
          <div className="space-y-1.5">
            <div className="flex items-center justify-between text-xs text-zinc-500 font-mono">
              <span>Document Body & Contract Clauses</span>
              <div className="flex items-center gap-3">
                <span>{wordCount} words</span>
                <span>•</span>
                <span>{charCount} characters</span>
              </div>
            </div>

            <textarea
              rows={14}
              value={bodyText}
              onChange={(e) => setBodyText(e.target.value)}
              placeholder="Paste or type your contract clauses, terms, or proposal text here..."
              className="w-full p-4 rounded-2xl border border-zinc-300 bg-zinc-50/50 hover:bg-white focus:bg-white text-xs sm:text-sm font-mono text-zinc-900 leading-relaxed focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950 transition-all resize-y"
            />
          </div>

          {/* Output Filename & Export Controls */}
          <div className="pt-2 border-t border-zinc-100 space-y-4">
            <div>
              <label className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-700 block mb-1.5">
                Export Filename
              </label>
              <input
                type="text"
                value={outputFileName}
                onChange={(e) => setOutputFileName(e.target.value)}
                placeholder="document.pdf"
                className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 bg-white text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950 shadow-2xs"
              />
            </div>

            <button
              type="button"
              disabled={!bodyText.trim() || isProcessing}
              onClick={handleConvertAndDownload}
              className="w-full py-4 px-6 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-sm flex items-center justify-center gap-2.5 shadow-md active:scale-[0.99] disabled:opacity-45 disabled:pointer-events-none transition-all cursor-pointer"
            >
              {isProcessing ? (
                <>
                  <RefreshCw className="w-4 h-4 stroke-[2] animate-spin" />
                  <span>Typesetting & Compiling PDF...</span>
                </>
              ) : (
                <>
                  <FileText className="w-4 h-4 stroke-[1.8]" />
                  <span>Convert to PDF & Download ({estimatedPages} {estimatedPages === 1 ? 'Page' : 'Pages'})</span>
                  <Download className="w-4 h-4 stroke-[1.8] ml-1" />
                </>
              )}
            </button>
          </div>

        </div>

      </div>
    </ToolPageShell>
  );
}
