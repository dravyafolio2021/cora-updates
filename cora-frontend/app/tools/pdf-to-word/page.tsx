'use client';

import React, { useState, useRef, useMemo } from 'react';
import { 
  FileText, 
  UploadCloud, 
  Download, 
  RefreshCw, 
  ShieldCheck, 
  Copy, 
  Check, 
  Sparkles, 
  Trash2, 
  Edit3, 
  Type, 
  AlignLeft, 
  BookOpen, 
  Sliders, 
  Layers
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo, convertTextToPdf } from '@/lib/pdf-engine';

interface ParsedSection {
  id: string;
  type: 'title' | 'heading' | 'paragraph' | 'bullet';
  content: string;
}

const SAMPLE_DOCS = {
  contract: {
    title: 'MASTER SERVICES AGREEMENT & STATEMENT OF WORK',
    text: `MASTER SERVICES AGREEMENT

Prepared for: Aarav Mehta / Studio Operations
Effective Date: September 05, 2026
Governing Jurisdiction: Section 10A of the Information Technology Act 2000

1. SCOPE OF ENGAGEMENT
The Service Provider agrees to deliver enterprise digital product design, architectural consulting, and high-performance client-side document processing engines in accordance with agreed milestone specifications.

2. COMPENSATION, TAXES & RELEASES
Client shall remit milestone payments within fifteen (15) calendar days of invoice presentation. All compensation figures are denominated in INR and include statutory Goods and Services Tax (18% GST under SAC Code 9983).

3. INTELLECTUAL PROPERTY & CASE STUDIES
Title, copyright, and intellectual property rights in custom deliverables transfer exclusively to Client upon full receipt of cleared milestone funds. Pre-existing frameworks, design token registries, and core developer tools remain Service Provider proprietary assets.

4. CONFIDENTIALITY & DATA ISOLATION
Both parties covenant to protect confidential commercial data with strict care. Zero confidential files, client lists, or codebases shall be disclosed to unauthorized third parties.

5. EXECUTION & SIGNATORIES
IN WITNESS WHEREOF, the authorized representatives have executed this Master Agreement on the date first written above.`,
  },
  retainer: {
    title: 'CREATIVE STUDIO RETAINER & SLA SPECIFICATION',
    text: `MONTHLY CREATIVE RETAINER BRIEF

Client: Aarav Mehta / Studio Operations
Period: Q3 - Q4 2026

1. MONTHLY CAPACITY ALLOCATION
- Dedicated Studio Design & Development Hours: 40 Hours / Month
- Priority Turnaround SLA: 48 Business Hours for Creative Reviews
- Emergency Bug Fix Turnaround: Sub-4 Hours

2. DELIVERABLES SUMMARY
- Brand Identity Polish & Design Tokens
- Interactive Pitch Decks & Boardroom Presentations
- Automated In-Browser Document Processing Workflows

3. RETAINER COMMERCIALS
Monthly Retainer Fee: INR 1,20,000 + 18% GST (SAC 9983). Invoiced on the 1st of every calendar month with 10-day payment clearance terms.`,
  },
};

const FAQ_ITEMS = [
  {
    question: 'Can I edit and redline the extracted text before downloading as Word?',
    answer: 'Yes. Cora provides an interactive in-browser document editor where you can freely modify headings, edit clause wording, add notes, and correct formatting before triggering the 1-click Word document download.',
  },
  {
    question: 'How does Cora structure extracted PDF text into Word headings and paragraphs?',
    answer: 'Cora analyzes text capitalization, clause numbering prefixes (e.g. 1., 2.1), and line break cadences to classify text into formal document titles, section headers, standard paragraphs, and bullet points with appropriate font hierarchies.',
  },
  {
    question: 'Are confidential contracts or legal agreements uploaded to any cloud server?',
    answer: 'No. All PDF parsing, text extraction, and Word document compilation execute 100% locally in your browser RAM. Zero bytes are transmitted to any cloud server, ensuring complete confidentiality for client NDAs and financial agreements.',
  },
  {
    question: 'What file format does the 1-click download produce?',
    answer: 'The tool generates a standards-compliant Word document (.doc) with embedded Microsoft Office XML namespaces. It opens natively and cleanly in Microsoft Word, Google Docs, Apple Pages, and LibreOffice with standard typography, margins, and bullet styles intact.',
  },
  {
    question: 'Can I copy the formatted text directly to my clipboard?',
    answer: 'Yes. The 1-click "Copy Text" button copies the entire cleaned document to your system clipboard so you can paste it directly into Google Docs, Slack, Notion, or email.',
  },
];

export default function PdfToWordPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [documentTitle, setDocumentTitle] = useState<string>('CONVERTED_DOCUMENT');
  const [sections, setSections] = useState<ParsedSection[]>([]);
  const [rawText, setRawText] = useState<string>('');
  const [pdfFileName, setPdfFileName] = useState<string>('');
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [copied, setCopied] = useState<boolean>(false);

  // Dynamic Metrics
  const stats = useMemo(() => {
    const fullText = sections.map((s) => s.content).join('\n\n') || rawText;
    const words = fullText.trim() ? fullText.trim().split(/\s+/).length : 0;
    const chars = fullText.length;
    const paragraphs = sections.filter((s) => s.type === 'paragraph').length;
    const readingTimeMins = Math.max(1, Math.ceil(words / 200));
    return { words, chars, paragraphs, readingTimeMins };
  }, [sections, rawText]);

  // Load PDF.js dynamically
  const loadPdfJs = async () => {
    if (typeof window === 'undefined') return null;
    if ((window as any).pdfjsLib) return (window as any).pdfjsLib;

    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
      script.crossOrigin = 'anonymous';
      script.onload = () => {
        const pdfjs = (window as any).pdfjsLib;
        if (pdfjs) {
          pdfjs.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
          resolve(pdfjs);
        } else {
          reject(new Error('PDF.js failed to initialize'));
        }
      };
      script.onerror = () => reject(new Error('Failed to load PDF.js script'));
      document.head.appendChild(script);
    });
  };

  const parseTextIntoSections = (text: string, titleHint = 'Extracted Document'): ParsedSection[] => {
    const lines = text.split('\n').map((l) => l.trim()).filter(Boolean);
    const parsed: ParsedSection[] = [];

    let isFirstLine = true;
    for (let i = 0; i < lines.length; i++) {
      const line = lines[i];

      // Detect main title
      if (isFirstLine && line.length < 80) {
        parsed.push({ id: `sec-${i}`, type: 'title', content: line });
        isFirstLine = false;
        continue;
      }
      isFirstLine = false;

      // Detect numbered or capital headers (e.g., "1. SCOPE", "ARTICLE II", "SECTION 10A")
      const isHeader =
        /^([0-9]+\.|\bARTICLE\b|\bSECTION\b|\bCLAUSE\b)/i.test(line) ||
        (line.length < 60 && line === line.toUpperCase() && !/[.!?]$/.test(line));

      // Detect bullets
      const isBullet = /^[-*•–]\s+/.test(line) || /^\([a-z0-9]\)\s+/i.test(line);

      if (isHeader) {
        parsed.push({ id: `sec-${i}`, type: 'heading', content: line });
      } else if (isBullet) {
        parsed.push({ id: `sec-${i}`, type: 'bullet', content: line.replace(/^[-*•–]\s+/, '') });
      } else {
        parsed.push({ id: `sec-${i}`, type: 'paragraph', content: line });
      }
    }

    if (parsed.length === 0 && text.trim()) {
      parsed.push({ id: 'sec-0', type: 'paragraph', content: text });
    }

    return parsed;
  };

  const handleProcessPdf = async (file: File) => {
    setIsProcessing(true);
    setPdfFileName(file.name);

    try {
      let extractedText = '';
      const arrayBuffer = await file.arrayBuffer();

      try {
        const pdfjs = await loadPdfJs();
        const loadingTask = pdfjs.getDocument({ data: arrayBuffer });
        const pdf = await loadingTask.promise;

        const textChunks: string[] = [];
        for (let i = 1; i <= pdf.numPages; i++) {
          const page = await pdf.getPage(i);
          const textContent = await page.getTextContent();
          const pageText = textContent.items
            .map((item: any) => item.str || '')
            .join(' ')
            .replace(/\s+/g, ' ');
          if (pageText.trim()) textChunks.push(pageText.trim());
        }
        extractedText = textChunks.join('\n\n');
      } catch (pdfErr) {
        console.warn('PDF.js text parser fallback engaged:', pdfErr);
        // Fallback: extract string tokens from array buffer
        const decoder = new TextDecoder('utf-8', { fatal: false });
        const rawString = decoder.decode(arrayBuffer);
        const clean = rawString
          .replace(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/g, ' ')
          .replace(/stream[\s\S]*?endstream/g, ' ')
          .replace(/[<>[\]()/]/g, ' ')
          .replace(/\s+/g, ' ')
          .trim();
        extractedText = clean.length > 100 ? clean : 'Unable to extract text stream directly. Document may be scanned or image-only.';
      }

      setRawText(extractedText);
      const cleanTitle = file.name.replace(/\.[^/.]+$/, '').toUpperCase();
      setDocumentTitle(cleanTitle);

      const parsed = parseTextIntoSections(extractedText, cleanTitle);
      setSections(parsed);
      showToast(`Extracted ${parsed.length} structured sections from ${file.name}`);
    } catch (err) {
      console.error(err);
      showToast('Error reading PDF file. Please verify document integrity.');
    } finally {
      setIsProcessing(false);
    }
  };

  const handleFileUpload = (file: File) => {
    if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
      showToast('Please upload a valid PDF document');
      return;
    }
    handleProcessPdf(file);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDraggingOver(false);
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      handleFileUpload(e.dataTransfer.files[0]);
    }
  };

  const handleLoadSample = (key: 'contract' | 'retainer') => {
    const sample = SAMPLE_DOCS[key];
    setPdfFileName(`cora-sample-${key}.pdf`);
    setDocumentTitle(sample.title);
    setRawText(sample.text);
    const parsed = parseTextIntoSections(sample.text, sample.title);
    setSections(parsed);
    showToast(`Loaded sample ${key === 'contract' ? 'agreement' : 'retainer'} deed`);
  };

  const updateSectionContent = (id: string, newContent: string) => {
    setSections((prev) =>
      prev.map((sec) => (sec.id === id ? { ...sec, content: newContent } : sec))
    );
  };

  const deleteSection = (id: string) => {
    setSections((prev) => prev.filter((sec) => sec.id !== id));
    showToast('Section removed');
  };

  const addParagraphSection = () => {
    const newId = `sec-${Date.now()}`;
    setSections((prev) => [...prev, { id: newId, type: 'paragraph', content: 'New clause or paragraph text...' }]);
    showToast('Added new paragraph');
  };

  // Download Word (.doc) with Office XML headers
  const handleDownloadDoc = () => {
    if (sections.length === 0) {
      showToast('No document content to export');
      return;
    }

    const titleHtml = `<h1 style="font-size: 18pt; font-weight: bold; margin-bottom: 16pt; color: #09090b;">${documentTitle}</h1>`;
    const sectionsHtml = sections
      .map((sec) => {
        if (sec.type === 'title') {
          return `<h1 style="font-size: 16pt; font-weight: bold; margin-top: 14pt; margin-bottom: 8pt; color: #09090b;">${sec.content}</h1>`;
        }
        if (sec.type === 'heading') {
          return `<h2 style="font-size: 13pt; font-weight: bold; margin-top: 12pt; margin-bottom: 6pt; color: #18181b;">${sec.content}</h2>`;
        }
        if (sec.type === 'bullet') {
          return `<li style="font-size: 11pt; line-height: 1.5; margin-bottom: 4pt; color: #27272a;">${sec.content}</li>`;
        }
        return `<p style="font-size: 11pt; line-height: 1.55; margin-bottom: 10pt; color: #27272a; text-align: justify;">${sec.content}</p>`;
      })
      .join('\n');

    const fullWordHtml = `
      <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
      <head>
        <meta charset='utf-8'>
        <title>${documentTitle}</title>
        <!--[if gte mso 9]>
        <xml>
        <w:WordDocument>
          <w:View>Print</w:View>
          <w:Zoom>100</w:Zoom>
          <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
        </xml>
        <![endif]-->
        <style>
          @page {
            size: A4;
            margin: 1.0in 1.0in 1.0in 1.0in;
          }
          body {
            font-family: 'Calibri', 'Arial', sans-serif;
            font-size: 11pt;
            color: #18181b;
            line-height: 1.5;
          }
          ul { margin-top: 4pt; margin-bottom: 8pt; padding-left: 20pt; }
        </style>
      </head>
      <body>
        ${titleHtml}
        ${sectionsHtml}
        <br/>
        <p style="font-size: 9pt; color: #71717a; border-top: 1pt solid #e4e4e7; padding-top: 8pt;">
          Extracted via Cora In-Memory PDF Engine • Section 10A IT Act Compliant
        </p>
      </body>
      </html>
    `;

    const blob = new Blob([fullWordHtml], { type: 'application/msword;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${documentTitle.toLowerCase().replace(/[^a-z0-9_-]/g, '_') || 'cora_doc'}.doc`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    showToast('Downloaded Word document (.doc)');
  };

  // Download Plain Text
  const handleDownloadTxt = () => {
    const fullText = sections.map((s) => s.content).join('\n\n');
    const blob = new Blob([fullText], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${documentTitle.toLowerCase().replace(/[^a-z0-9_-]/g, '_') || 'cora_doc'}.txt`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    showToast('Downloaded plain text file (.txt)');
  };

  // Copy to Clipboard
  const handleCopyClipboard = () => {
    const fullText = sections.map((s) => s.content).join('\n\n');
    navigator.clipboard.writeText(fullText);
    setCopied(true);
    showToast('Copied full document text to clipboard!');
    setTimeout(() => setCopied(false), 2200);
  };

  const handleReset = () => {
    setPdfFileName('');
    setSections([]);
    setRawText('');
    setDocumentTitle('CONVERTED_DOCUMENT');
    if (fileInputRef.current) fileInputRef.current.value = '';
    showToast('Cleared loaded document');
  };

  return (
    <ToolPageShell
      toolId="pdf-to-word"
      badgeTag="Structured Clause & Heading Extractor"
      title="PDF to Word Converter Online Free"
      subtitle="Extract locked PDF agreements, proposals, and contracts into structured editable Word documents (.doc) with zero server uploads."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['pdf-to-jpg', 'pdf-to-excel', 'pdf-to-markdown', 'word-to-pdf']}
    >
      <div className="space-y-8">

        {/* Top Control Header Card */}
        <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
              <div className="flex items-center gap-2 mb-2">
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <ShieldCheck className="w-3.5 h-3.5 text-zinc-700" />
                  100% In-Browser RAM
                </span>
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <Sparkles className="w-3.5 h-3.5 text-zinc-700" />
                  Preserves Structure
                </span>
              </div>
              <h2 className="text-xl sm:text-2xl font-semibold tracking-tight text-zinc-900">
                PDF to Word (.doc / .docx) Extractor
              </h2>
              <p className="text-sm text-zinc-600 mt-1 max-w-2xl">
                Unlock static read-only PDFs into editable Microsoft Word documents with structured titles, headings, and clauses.
              </p>
            </div>

            <div className="flex flex-wrap items-center gap-3">
              <button
                type="button"
                onClick={() => handleLoadSample('contract')}
                className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
              >
                <Sparkles className="w-4 h-4 text-zinc-600" />
                Sample Agreement
              </button>

              <button
                type="button"
                onClick={() => handleLoadSample('retainer')}
                className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
              >
                <Sparkles className="w-4 h-4 text-zinc-600" />
                Sample Retainer
              </button>

              {sections.length > 0 && (
                <>
                  <button
                    type="button"
                    onClick={handleCopyClipboard}
                    className="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
                  >
                    {copied ? <Check className="w-4 h-4 text-zinc-800" /> : <Copy className="w-4 h-4" />}
                    {copied ? 'Copied' : 'Copy Text'}
                  </button>

                  <button
                    type="button"
                    onClick={handleDownloadDoc}
                    className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition-colors shadow-sm"
                  >
                    <Download className="w-4 h-4" />
                    Download Word (.doc)
                  </button>
                </>
              )}

              {sections.length > 0 && (
                <button
                  type="button"
                  onClick={handleReset}
                  className="inline-flex items-center gap-1.5 px-3 py-2.5 text-xs font-medium text-zinc-600 hover:text-zinc-900 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
                  title="Clear document"
                >
                  <Trash2 className="w-4 h-4" />
                </button>
              )}
            </div>
          </div>

          {/* Document Telemetry Metrics */}
          {sections.length > 0 && (
            <div className="mt-6 pt-6 border-t border-zinc-100 grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100">
                <span className="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Words</span>
                <span className="text-lg font-semibold text-zinc-900">{stats.words}</span>
              </div>
              <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100">
                <span className="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Characters</span>
                <span className="text-lg font-semibold text-zinc-900">{stats.chars}</span>
              </div>
              <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100">
                <span className="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Paragraphs</span>
                <span className="text-lg font-semibold text-zinc-900">{stats.paragraphs}</span>
              </div>
              <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100">
                <span className="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Est. Read Time</span>
                <span className="text-lg font-semibold text-zinc-900">{stats.readingTimeMins} min</span>
              </div>
            </div>
          )}
        </div>

        {/* Upload Dropzone */}
        {sections.length === 0 && !isProcessing && (
          <div
            onDragOver={(e) => {
              e.preventDefault();
              setIsDraggingOver(true);
            }}
            onDragLeave={() => setIsDraggingOver(false)}
            onDrop={handleDrop}
            onClick={() => fileInputRef.current?.click()}
            className={`border-2 border-dashed rounded-3xl p-10 sm:p-14 text-center cursor-pointer transition-all duration-200 ${
              isDraggingOver
                ? 'border-zinc-900 bg-zinc-50 scale-[0.99]'
                : 'border-zinc-300 hover:border-zinc-400 bg-white hover:bg-zinc-50/50'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept=".pdf,application/pdf"
              className="hidden"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleFileUpload(e.target.files[0]);
                }
              }}
            />

            <div className="w-16 h-16 mx-auto rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center mb-4">
              <UploadCloud className="w-8 h-8 text-zinc-700" />
            </div>

            <h3 className="text-base sm:text-lg font-semibold text-zinc-900 mb-1">
              Select or Drop your PDF Document
            </h3>
            <p className="text-xs sm:text-sm text-zinc-500 max-w-md mx-auto mb-4">
              Extract vendor contracts, scope statements, or invoices into editable Microsoft Word documents.
            </p>

            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-zinc-100 text-zinc-700 text-xs font-medium">
              <ShieldCheck className="w-3.5 h-3.5 text-zinc-600" />
              100% In-Browser Memory • Zero Uploads
            </div>
          </div>
        )}

        {/* Processing Indicator */}
        {isProcessing && (
          <div className="bg-white border border-zinc-200 rounded-2xl p-8 text-center space-y-3">
            <RefreshCw className="w-6 h-6 text-zinc-800 animate-spin mx-auto" />
            <p className="text-sm font-medium text-zinc-900">
              Extracting document structure and paragraphs...
            </p>
            <p className="text-xs text-zinc-500">
              Analyzing text layout, heading hierarchies, and clauses in browser RAM
            </p>
          </div>
        )}

        {/* Interactive Document Editor */}
        {sections.length > 0 && (
          <div className="space-y-4">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-1">
              <div className="flex items-center gap-2">
                <Edit3 className="w-4 h-4 text-zinc-700" />
                <h3 className="text-sm font-semibold text-zinc-900">
                  Editable Document Preview ({pdfFileName || 'Extracted Document'})
                </h3>
              </div>
              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={addParagraphSection}
                  className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-medium transition-colors"
                >
                  + Add Paragraph
                </button>
                <button
                  type="button"
                  onClick={handleDownloadTxt}
                  className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-medium transition-colors"
                >
                  <FileText className="w-3.5 h-3.5" />
                  .TXT
                </button>
              </div>
            </div>

            {/* Document Title Editable Header */}
            <div className="bg-white border border-zinc-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
              <div>
                <label className="block text-[11px] font-medium text-zinc-500 uppercase tracking-wider mb-1.5">
                  Document Header Title
                </label>
                <input
                  type="text"
                  value={documentTitle}
                  onChange={(e) => setDocumentTitle(e.target.value)}
                  className="w-full text-lg sm:text-xl font-bold text-zinc-900 border-b border-zinc-200 focus:border-zinc-900 focus:outline-none pb-1 bg-transparent"
                  placeholder="DOCUMENT TITLE"
                />
              </div>

              {/* Sections Stream */}
              <div className="space-y-4 pt-2">
                {sections.map((sec, index) => (
                  <div
                    key={sec.id}
                    className="group relative border border-zinc-100 hover:border-zinc-200 rounded-xl p-4 transition-colors bg-zinc-50/40 hover:bg-white"
                  >
                    <div className="flex items-center justify-between mb-2">
                      <span className="text-[10px] font-medium text-zinc-400 uppercase tracking-wider">
                        {sec.type === 'heading' ? 'Section Header' : sec.type === 'bullet' ? 'List Item' : 'Paragraph'}
                      </span>
                      <button
                        type="button"
                        onClick={() => deleteSection(sec.id)}
                        className="opacity-0 group-hover:opacity-100 p-1 text-zinc-400 hover:text-zinc-700 transition-opacity"
                        title="Remove section"
                      >
                        <Trash2 className="w-3.5 h-3.5" />
                      </button>
                    </div>

                    {sec.type === 'heading' ? (
                      <input
                        type="text"
                        value={sec.content}
                        onChange={(e) => updateSectionContent(sec.id, e.target.value)}
                        className="w-full text-sm font-bold text-zinc-900 bg-transparent focus:outline-none focus:ring-1 focus:ring-zinc-900 rounded px-1.5 py-1"
                      />
                    ) : (
                      <textarea
                        rows={sec.type === 'bullet' ? 2 : 4}
                        value={sec.content}
                        onChange={(e) => updateSectionContent(sec.id, e.target.value)}
                        className="w-full text-sm text-zinc-800 bg-transparent focus:outline-none focus:ring-1 focus:ring-zinc-900 rounded px-1.5 py-1 leading-relaxed resize-y font-normal"
                      />
                    )}
                  </div>
                ))}
              </div>

              {/* Bottom Action Footer */}
              <div className="pt-6 border-t border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div className="text-xs text-zinc-500">
                  Ready to export • Standard Calibri typography with A4 margin specs
                </div>
                <div className="flex items-center gap-3">
                  <button
                    type="button"
                    onClick={handleCopyClipboard}
                    className="px-4 py-2 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
                  >
                    {copied ? 'Copied to Clipboard' : 'Copy All Text'}
                  </button>
                  <button
                    type="button"
                    onClick={handleDownloadDoc}
                    className="inline-flex items-center gap-2 px-5 py-2 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition-colors shadow-sm"
                  >
                    <Download className="w-4 h-4" />
                    Download Word Document (.doc)
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}

      </div>
    </ToolPageShell>
  );
}
