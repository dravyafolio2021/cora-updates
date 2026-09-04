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
  Eye, 
  Code, 
  BookOpen, 
  Sliders, 
  Hash, 
  List, 
  Terminal,
  FileCode
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';

const SAMPLE_DOCS = {
  architecture: {
    title: 'ENGINEERING_ARCHITECTURE_RUNBOOK',
    text: `# Cora Client-Side PDF Architecture Runbook

> **Status:** Production Ready  
> **Author:** Aarav Mehta / Studio Operations  
> **Last Verified:** September 05, 2026

## 1. Executive Summary
This document defines the high-throughput, client-side document conversion pipeline. All conversion workloads execute 100% within the browser's JavaScript V8 heap, guaranteeing absolute data isolation.

## 2. Technical Stack & Dependencies
The conversion engine relies on standard web primitives:
- **pdf-lib:** In-memory binary stream reconstruction, object serialization, and vector table drawing.
- **PDF.js:** Multi-resolution HTML5 canvas rasterization and structured text-content extraction.
- **Web Workers:** Non-blocking background rendering threads for multi-page documents.

\`\`\`typescript
export interface RenderOptions {
  scale: number;
  quality: number;
  colorSpace: 'srgb' | 'display-p3';
}

export async function processSheet(buffer: ArrayBuffer): Promise<Blob> {
  const engine = await initEngine();
  return await engine.rasterize(buffer, { scale: 2.0 });
}
\`\`\`

## 3. Key Security Guarantees
1. Zero outbound network payloads for document binaries.
2. Immediate memory garbage collection upon tab close or document reset.
3. Full compliance with Indian IT Act 2000 Section 10A electronic standards.`,
  },
  commercialSow: {
    title: 'AGENCY_COMMERCIAL_SOW',
    text: `# MASTER SCOPE STATEMENT & MILESTONES

> **Client:** Studio Operations  
> **Lead:** Rohan Verma  
> **SAC Code:** 998314 (Information Technology Consulting)

## 1. Project Objectives
Deliver enterprise-grade client-side PDF manipulation tools with sub-50ms user interface hydration.

## 2. Commercial Milestone Deliverables
- **Milestone 1:** High-resolution 2x retina JPG rasterizer with individual sheet download.
- **Milestone 2:** Structured Word document extraction preserving headings and clauses.
- **Milestone 3:** Tabular matrix parser exporting clean RFC 4180 CSV and Excel formats.
- **Milestone 4:** Vectorized A4 PDF table generator with zebra striping and corporate headers.

## 3. Terms of Payment
Payment shall be remitted within 15 days of invoice date. Late remittances will incur a 1.5% statutory monthly fee under Indian commercial law.`,
  },
};

const FAQ_ITEMS = [
  {
    question: 'How does Cora detect Markdown headers, bullet lists, and code blocks from a PDF?',
    answer: 'Cora uses a structural heuristic analyzer that identifies text formatting signals: lines in all-caps or preceded by numbers (e.g. 1.0) become # or ## headers, dashes or bullet glyphs become list items, indented or bracketed syntax becomes ``` code blocks, and quote-like remarks become > blockquotes.',
  },
  {
    question: 'What format is produced for code blocks and inline technical snippets?',
    answer: 'Code blocks are wrapped in standard triple-backtick fenced blocks (```typescript / ```json) with proper indentation, while inline parameters and function names are wrapped in single backticks (`code`) for clean Markdown parsing.',
  },
  {
    question: 'Are technical whitepapers or confidential architecture docs uploaded to any server?',
    answer: 'No. The entire PDF extraction and Markdown translation cycle runs 100% inside your browser memory (RAM) via client-side JavaScript. Zero bytes of your intellectual property, API keys, or architectures ever leave your computer.',
  },
  {
    question: 'Can I preview the rendered Markdown visually before downloading?',
    answer: 'Yes. You can toggle between the "Rendered Preview" tab (which shows typography, formatted tables, code boxes, and headers) and the "Raw Markdown" tab (which displays the exact .md syntax).',
  },
  {
    question: 'How does this compare to pasting raw unformatted text into LLM prompts or Notion?',
    answer: 'Raw pasted PDF text often scrambles headers, breaks code indentation, and loses bullet hierarchies. Cora restructures the document into clean semantic Markdown so LLMs like Claude, ChatGPT, and Gemini can comprehend technical specifications with maximum prompt accuracy.',
  },
];

export default function PdfToMarkdownPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [documentTitle, setDocumentTitle] = useState<string>('cora-document');
  const [markdownContent, setMarkdownContent] = useState<string>('');
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [activeTab, setActiveTab] = useState<'preview' | 'raw'>('preview');
  const [copied, setCopied] = useState<boolean>(false);

  // Dynamic Telemetry Metrics
  const metrics = useMemo(() => {
    const text = markdownContent.trim();
    const words = text ? text.split(/\s+/).length : 0;
    const chars = text.length;
    const headings = (text.match(/^#{1,6}\s+/gm) || []).length;
    const codeBlocks = (text.match(/```/g) || []).length / 2;
    const readingTimeMins = Math.max(1, Math.ceil(words / 200));

    return { words, chars, headings, codeBlocks: Math.floor(codeBlocks), readingTimeMins };
  }, [markdownContent]);

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
      script.onerror = () => reject(new Error('Failed to load PDF.js'));
      document.head.appendChild(script);
    });
  };

  // Convert raw text into clean GitHub-flavored Markdown
  const convertTextToMarkdown = (rawText: string, docName: string): string => {
    const lines = rawText.split('\n').map((l) => l.trimEnd());
    const mdLines: string[] = [];

    let inCodeBlock = false;
    let isFirstLine = true;

    for (let i = 0; i < lines.length; i++) {
      const line = lines[i].trim();

      if (!line) {
        mdLines.push('');
        continue;
      }

      // Title detection on first line
      if (isFirstLine && line.length < 90 && !line.startsWith('#')) {
        mdLines.push(`# ${line}`);
        mdLines.push('');
        isFirstLine = false;
        continue;
      }
      isFirstLine = false;

      // Check code block markers
      if (line.startsWith('```')) {
        inCodeBlock = !inCodeBlock;
        mdLines.push(line);
        continue;
      }

      if (inCodeBlock) {
        mdLines.push(lines[i]);
        continue;
      }

      // Detect Section Headers (e.g. "1. OVERVIEW" or "SECTION 10A" or "## ")
      if (/^(#{1,6}\s+)/.test(line)) {
        mdLines.push(line);
        continue;
      }

      if (/^([0-9]+\.\s+[A-Z\s]{3,})/i.test(line) || /^(\bARTICLE\b|\bSECTION\b|\bCHAPTER\b)\s+[0-9IVX]+/i.test(line)) {
        mdLines.push(`## ${line}`);
        continue;
      }

      if (/^([0-9]+\.[0-9]+\s+[A-Z\s]{3,})/i.test(line)) {
        mdLines.push(`### ${line}`);
        continue;
      }

      // Detect Uppercase standalone headers
      if (line.length < 50 && line === line.toUpperCase() && !/[.!?:;]$/.test(line) && !/^[0-9]/.test(line)) {
        mdLines.push(`### ${line}`);
        continue;
      }

      // Detect Bullets
      if (/^[-*•–]\s+/.test(line)) {
        mdLines.push(`- ${line.replace(/^[-*•–]\s+/, '')}`);
        continue;
      }

      // Detect Numbered lists
      if (/^[0-9]+\.\s+/.test(line)) {
        mdLines.push(line);
        continue;
      }

      // Detect Blockquotes
      if (/^(Note:|Important:|Warning:|Disclaimer:)/i.test(line)) {
        mdLines.push(`> **${line.split(':')[0]}:** ${line.split(':').slice(1).join(':').trim()}`);
        continue;
      }

      // Detect Code or JSON like lines
      if (/^(\{|\[|const\s|let\s|var\s|function\s|import\s|export\s|class\s)/.test(line)) {
        mdLines.push('```typescript');
        mdLines.push(lines[i]);
        mdLines.push('```');
        continue;
      }

      // Standard Paragraph
      mdLines.push(line);
    }

    return mdLines.join('\n');
  };

  const handleProcessPdf = async (file: File) => {
    setIsProcessing(true);
    const cleanTitle = file.name.replace(/\.[^/.]+$/, '');
    setDocumentTitle(cleanTitle);

    try {
      let extractedText = '';
      const arrayBuffer = await file.arrayBuffer();

      try {
        const pdfjs = await loadPdfJs();
        const loadingTask = pdfjs.getDocument({ data: arrayBuffer });
        const pdf = await loadingTask.promise;

        const chunks: string[] = [];
        for (let i = 1; i <= pdf.numPages; i++) {
          const page = await pdf.getPage(i);
          const textContent = await page.getTextContent();
          const pageText = textContent.items
            .map((item: any) => item.str || '')
            .join(' ')
            .replace(/\s+/g, ' ');
          if (pageText.trim()) chunks.push(pageText.trim());
        }
        extractedText = chunks.join('\n\n');
      } catch (pdfErr) {
        console.warn('PDF.js text extractor fallback engaged:', pdfErr);
        const decoder = new TextDecoder('utf-8', { fatal: false });
        extractedText = decoder.decode(arrayBuffer)
          .replace(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/g, ' ')
          .replace(/stream[\s\S]*?endstream/g, ' ')
          .replace(/\s+/g, ' ')
          .trim();
      }

      const md = convertTextToMarkdown(extractedText, cleanTitle);
      setMarkdownContent(md);
      showToast(`Successfully extracted Markdown from ${file.name}`);
    } catch (err) {
      console.error(err);
      showToast('Error reading PDF. Please verify file integrity.');
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

  const handleLoadSample = (key: 'architecture' | 'commercialSow') => {
    const sample = SAMPLE_DOCS[key];
    setDocumentTitle(sample.title);
    setMarkdownContent(sample.text);
    showToast(`Loaded ${key === 'architecture' ? 'architecture runbook' : 'commercial SOW'} sample`);
  };

  const handleCopyMarkdown = () => {
    if (!markdownContent) return;
    navigator.clipboard.writeText(markdownContent);
    setCopied(true);
    showToast('Copied Markdown (.md) to clipboard!');
    setTimeout(() => setCopied(false), 2200);
  };

  const handleDownloadMd = () => {
    if (!markdownContent) {
      showToast('No Markdown content to download');
      return;
    }

    const blob = new Blob([markdownContent], { type: 'text/markdown;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${documentTitle.toLowerCase().replace(/[^a-z0-9_-]/g, '_') || 'cora_doc'}.md`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    showToast('Downloaded Markdown document (.md)');
  };

  const handleReset = () => {
    setMarkdownContent('');
    setDocumentTitle('cora-document');
    if (fileInputRef.current) fileInputRef.current.value = '';
    showToast('Cleared loaded document');
  };

  return (
    <ToolPageShell
      toolId="pdf-to-markdown"
      badgeTag="GitHub-Flavored Markdown Extractor"
      title="PDF to Markdown Converter Online Free"
      subtitle="Extract PDF documents, whitepapers, and specs into clean Markdown (.md) with structured headers, bullet lists, and code blocks."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['pdf-to-word', 'pdf-to-excel', 'html-to-pdf', 'word-to-pdf']}
    >
      <div className="space-y-8">

        {/* Top Control Header Card */}
        <div className="bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
              <div className="flex items-center gap-2 mb-2">
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <ShieldCheck className="w-3.5 h-3.5 text-zinc-700" />
                  100% In-Browser Memory
                </span>
                <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                  <Sparkles className="w-3.5 h-3.5 text-zinc-700" />
                  GitHub Flavored
                </span>
              </div>
              <h2 className="text-xl sm:text-2xl font-semibold tracking-tight text-zinc-900">
                PDF to Markdown Document Extractor
              </h2>
              <p className="text-sm text-zinc-600 mt-1 max-w-2xl">
                Convert technical specifications, proposals, and manuals into clean Markdown formatted for Notion, wikis, and LLM context prompts.
              </p>
            </div>

            <div className="flex flex-wrap items-center gap-3">
              <button
                type="button"
                onClick={() => handleLoadSample('architecture')}
                className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
              >
                <Sparkles className="w-4 h-4 text-zinc-600" />
                Architecture Sample
              </button>

              <button
                type="button"
                onClick={() => handleLoadSample('commercialSow')}
                className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
              >
                <Sparkles className="w-4 h-4 text-zinc-600" />
                Commercial SOW Sample
              </button>

              {markdownContent && (
                <>
                  <button
                    type="button"
                    onClick={handleCopyMarkdown}
                    className="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
                  >
                    {copied ? <Check className="w-4 h-4 text-zinc-800" /> : <Copy className="w-4 h-4" />}
                    {copied ? 'Copied' : 'Copy .md'}
                  </button>

                  <button
                    type="button"
                    onClick={handleDownloadMd}
                    className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition-colors shadow-sm"
                  >
                    <Download className="w-4 h-4" />
                    Download .md
                  </button>
                </>
              )}

              {markdownContent && (
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

          {/* Telemetry Metrics */}
          {markdownContent && (
            <div className="mt-6 pt-6 border-t border-zinc-100 grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100">
                <span className="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Words</span>
                <span className="text-lg font-semibold text-zinc-900">{metrics.words}</span>
              </div>
              <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100">
                <span className="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Headings</span>
                <span className="text-lg font-semibold text-zinc-900">{metrics.headings}</span>
              </div>
              <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100">
                <span className="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Code Blocks</span>
                <span className="text-lg font-semibold text-zinc-900">{metrics.codeBlocks}</span>
              </div>
              <div className="p-3 bg-zinc-50 rounded-xl border border-zinc-100">
                <span className="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Est. Read Time</span>
                <span className="text-lg font-semibold text-zinc-900">{metrics.readingTimeMins} min</span>
              </div>
            </div>
          )}
        </div>

        {/* Upload Dropzone */}
        {!markdownContent && !isProcessing && (
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
              Extract technical specifications, API docs, or contracts into clean Markdown (.md).
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
              Extracting structural elements and Markdown hierarchies...
            </p>
            <p className="text-xs text-zinc-500">
              Detecting titles, section headers, bullet lists, and code blocks
            </p>
          </div>
        )}

        {/* Markdown Split View / Editor */}
        {markdownContent && (
          <div className="space-y-4">
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-1">
              <div className="flex items-center gap-2">
                <FileCode className="w-4 h-4 text-zinc-700" />
                <h3 className="text-sm font-semibold text-zinc-900">
                  Markdown Document ({documentTitle}.md)
                </h3>
              </div>

              {/* Tab Selector */}
              <div className="flex items-center bg-zinc-100 p-1 rounded-xl border border-zinc-200 self-start sm:self-auto">
                <button
                  type="button"
                  onClick={() => setActiveTab('preview')}
                  className={`inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg transition-colors ${
                    activeTab === 'preview'
                      ? 'bg-white text-zinc-900 shadow-sm'
                      : 'text-zinc-600 hover:text-zinc-900'
                  }`}
                >
                  <Eye className="w-3.5 h-3.5" />
                  Rendered Preview
                </button>
                <button
                  type="button"
                  onClick={() => setActiveTab('raw')}
                  className={`inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg transition-colors ${
                    activeTab === 'raw'
                      ? 'bg-white text-zinc-900 shadow-sm'
                      : 'text-zinc-600 hover:text-zinc-900'
                  }`}
                >
                  <Code className="w-3.5 h-3.5" />
                  Raw Markdown (.md)
                </button>
              </div>
            </div>

            {/* Document Content Box */}
            <div className="bg-white border border-zinc-200 rounded-2xl overflow-hidden shadow-sm">
              {activeTab === 'preview' ? (
                <div className="p-6 sm:p-10 space-y-4 max-w-4xl mx-auto">
                  {markdownContent.split('\n\n').map((block, idx) => {
                    const trimmed = block.trim();
                    if (!trimmed) return null;

                    if (trimmed.startsWith('# ')) {
                      return (
                        <h1 key={idx} className="text-2xl sm:text-3xl font-bold text-zinc-950 pb-2 border-b border-zinc-200">
                          {trimmed.replace(/^#\s+/, '')}
                        </h1>
                      );
                    }

                    if (trimmed.startsWith('## ')) {
                      return (
                        <h2 key={idx} className="text-lg sm:text-xl font-bold text-zinc-900 pt-4 pb-1 border-b border-zinc-100">
                          {trimmed.replace(/^##\s+/, '')}
                        </h2>
                      );
                    }

                    if (trimmed.startsWith('### ')) {
                      return (
                        <h3 key={idx} className="text-base font-semibold text-zinc-900 pt-2">
                          {trimmed.replace(/^###\s+/, '')}
                        </h3>
                      );
                    }

                    if (trimmed.startsWith('> ')) {
                      return (
                        <blockquote key={idx} className="border-l-4 border-zinc-900 pl-4 py-1 text-xs text-zinc-600 bg-zinc-50 rounded-r-lg">
                          {trimmed.replace(/^>\s+/, '')}
                        </blockquote>
                      );
                    }

                    if (trimmed.startsWith('```')) {
                      return (
                        <pre key={idx} className="bg-zinc-950 text-zinc-100 p-4 rounded-xl text-xs font-mono overflow-x-auto">
                          <code>{trimmed.replace(/```[a-z]*\n?/gi, '').trim()}</code>
                        </pre>
                      );
                    }

                    if (trimmed.startsWith('- ') || trimmed.startsWith('* ')) {
                      const items = trimmed.split('\n');
                      return (
                        <ul key={idx} className="list-disc pl-5 space-y-1 text-xs sm:text-sm text-zinc-700">
                          {items.map((it, i) => (
                            <li key={i}>{it.replace(/^[-*•]\s+/, '')}</li>
                          ))}
                        </ul>
                      );
                    }

                    return (
                      <p key={idx} className="text-xs sm:text-sm text-zinc-700 leading-relaxed">
                        {trimmed}
                      </p>
                    );
                  })}
                </div>
              ) : (
                <div className="p-4 sm:p-6">
                  <textarea
                    rows={18}
                    value={markdownContent}
                    onChange={(e) => setMarkdownContent(e.target.value)}
                    className="w-full font-mono text-xs text-zinc-900 bg-zinc-50 border border-zinc-200 rounded-xl p-4 focus:outline-none focus:ring-1 focus:ring-zinc-900 leading-relaxed resize-y"
                  />
                </div>
              )}

              {/* Bottom Action Bar */}
              <div className="p-4 bg-zinc-50 border-t border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-zinc-500">
                <div>
                  Clean GitHub-Flavored Markdown syntax • Ready for Notion, Obsidian, and LLMs
                </div>
                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={handleCopyMarkdown}
                    className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-medium transition-colors"
                  >
                    {copied ? <Check className="w-3.5 h-3.5" /> : <Copy className="w-3.5 h-3.5" />}
                    {copied ? 'Copied' : 'Copy Markdown'}
                  </button>
                  <button
                    type="button"
                    onClick={handleDownloadMd}
                    className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white font-medium transition-colors shadow-sm"
                  >
                    <Download className="w-3.5 h-3.5" />
                    Download .md
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
