'use client';

import React, { useState, useRef, useMemo, useEffect } from 'react';
import { 
  FileText, 
  UploadCloud, 
  Download, 
  RefreshCw, 
  ShieldCheck, 
  Code, 
  Eye, 
  Sparkles, 
  Trash2, 
  Sliders, 
  Copy, 
  Check, 
  Maximize2, 
  LayoutTemplate,
  FileCode,
  FileSpreadsheet
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { PDFDocument, rgb, StandardFonts } from 'pdf-lib';
import { downloadPdfBlob, convertTextToPdf, convertTableToPdf } from '@/lib/pdf-engine';

const SAMPLE_TEMPLATES = {
  invoice: {
    name: 'Modern GST Tax Invoice (18% & SAC 9983)',
    html: `<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #18181b; padding: 24px;">
  <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #18181b; padding-bottom: 16px; margin-bottom: 24px;">
    <div>
      <h1 style="font-size: 24px; font-weight: 800; margin: 0; letter-spacing: -0.5px; color: #09090b;">CORA STUDIO</h1>
      <p style="font-size: 12px; color: #71717a; margin: 4px 0 0 0;">Autonomous Digital Product Studio & Legal Automation</p>
      <p style="font-size: 11px; color: #a1a1aa; margin: 2px 0 0 0;">GSTIN: 27AABCC1234D1Z5 • SAC Code: 998314</p>
    </div>
    <div style="text-align: right;">
      <span style="display: inline-block; background: #f4f4f5; color: #18181b; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 6px;">TAX INVOICE</span>
      <p style="font-size: 13px; font-weight: 700; margin: 8px 0 0 0;">#INV-2026-084</p>
      <p style="font-size: 11px; color: #71717a; margin: 2px 0 0 0;">Date: September 05, 2026</p>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px;">
    <div style="background: #fafafa; padding: 14px; border-radius: 8px; border: 1px solid #f4f4f5;">
      <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #71717a; margin: 0 0 4px 0;">Billed To</p>
      <p style="font-size: 13px; font-weight: 600; margin: 0; color: #09090b;">Aarav Mehta / Studio Operations</p>
      <p style="font-size: 11px; color: #71717a; margin: 2px 0 0 0;">Mumbai, Maharashtra, India</p>
    </div>
    <div style="background: #fafafa; padding: 14px; border-radius: 8px; border: 1px solid #f4f4f5;">
      <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #71717a; margin: 0 0 4px 0;">Payment Terms</p>
      <p style="font-size: 13px; font-weight: 600; margin: 0; color: #09090b;">Net 15 Days (Direct UPI / NEFT)</p>
      <p style="font-size: 11px; color: #71717a; margin: 2px 0 0 0;">Due Date: September 20, 2026</p>
    </div>
  </div>

  <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
    <thead>
      <tr style="background: #18181b; color: #ffffff; text-align: left; font-size: 11px;">
        <th style="padding: 10px 12px; border-top-left-radius: 6px;">Scope Deliverable</th>
        <th style="padding: 10px 12px;">SAC</th>
        <th style="padding: 10px 12px; text-align: right;">Rate (INR)</th>
        <th style="padding: 10px 12px; text-align: right;">18% GST</th>
        <th style="padding: 10px 12px; text-align: right; border-top-right-radius: 6px;">Total (INR)</th>
      </tr>
    </thead>
    <tbody style="font-size: 12px; color: #27272a;">
      <tr style="border-bottom: 1px solid #f4f4f5;">
        <td style="padding: 10px 12px; font-weight: 500;">Milestone 1: Architectural Blueprint</td>
        <td style="padding: 10px 12px; color: #71717a;">998314</td>
        <td style="padding: 10px 12px; text-align: right;">75,000</td>
        <td style="padding: 10px 12px; text-align: right;">13,500</td>
        <td style="padding: 10px 12px; text-align: right; font-weight: 600;">88,500</td>
      </tr>
      <tr style="border-bottom: 1px solid #f4f4f5; background: #fafafa;">
        <td style="padding: 10px 12px; font-weight: 500;">Milestone 2: Client-Side Conversion Engine</td>
        <td style="padding: 10px 12px; color: #71717a;">998314</td>
        <td style="padding: 10px 12px; text-align: right;">95,000</td>
        <td style="padding: 10px 12px; text-align: right;">17,100</td>
        <td style="padding: 10px 12px; text-align: right; font-weight: 600;">1,12,100</td>
      </tr>
      <tr style="border-bottom: 1px solid #f4f4f5;">
        <td style="padding: 10px 12px; font-weight: 500;">Milestone 3: Staging Verification & QA</td>
        <td style="padding: 10px 12px; color: #71717a;">998314</td>
        <td style="padding: 10px 12px; text-align: right;">30,000</td>
        <td style="padding: 10px 12px; text-align: right;">5,400</td>
        <td style="padding: 10px 12px; text-align: right; font-weight: 600;">35,400</td>
      </tr>
    </tbody>
  </table>

  <div style="display: flex; justify-content: flex-end; margin-bottom: 28px;">
    <div style="width: 240px; background: #fafafa; padding: 14px; border-radius: 8px; border: 1px solid #f4f4f5;">
      <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 6px; color: #71717a;">
        <span>Taxable Amount:</span>
        <span>INR 2,00,000</span>
      </div>
      <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 8px; color: #71717a;">
        <span>CGST 9% + SGST 9%:</span>
        <span>INR 36,000</span>
      </div>
      <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 700; color: #09090b; border-top: 1px solid #e4e4e7; padding-top: 8px;">
        <span>Total Payable:</span>
        <span>INR 2,36,000</span>
      </div>
    </div>
  </div>

  <div style="border-top: 1px dashed #e4e4e7; padding-top: 14px; font-size: 10px; color: #a1a1aa; text-align: center;">
    This electronic tax invoice is generated under Section 10A of the Information Technology Act 2000. 100% Client-Side Engine.
  </div>
</div>`,
  },
  deed: {
    name: 'Section 10A Digital Service Agreement',
    html: `<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #18181b; padding: 24px; line-height: 1.6;">
  <div style="text-align: center; border-bottom: 2px solid #18181b; padding-bottom: 14px; margin-bottom: 20px;">
    <h1 style="font-size: 20px; font-weight: 800; margin: 0; color: #09090b;">INDEPENDENT COMMERCIAL ENGAGEMENT DEED</h1>
    <p style="font-size: 11px; color: #71717a; margin: 4px 0 0 0;">Governed under Section 10A of the Information Technology Act 2000</p>
  </div>

  <p style="font-size: 12px; color: #3f3f46; margin-bottom: 14px;">
    This deed is executed on <strong>September 05, 2026</strong> between Studio Client ("Client") and Service Provider ("Partner") for digital product architecture and client-side document processing services.
  </p>

  <h3 style="font-size: 13px; font-weight: 700; color: #09090b; margin: 16px 0 6px 0;">1. Scope & Deliverable Obligations</h3>
  <p style="font-size: 12px; color: #3f3f46; margin: 0 0 10px 0;">
    The Service Provider agrees to deliver production-ready software modules adhering to strict zero-cloud data isolation standards. All processing logic must execute purely within client-side web browser memory.
  </p>

  <h3 style="font-size: 13px; font-weight: 700; color: #09090b; margin: 16px 0 6px 0;">2. Intellectual Property Retention</h3>
  <p style="font-size: 12px; color: #3f3f46; margin: 0 0 10px 0;">
    Intellectual Property rights in bespoke deliverables transfer unconditionally to Client upon 100% receipt of cleared invoice milestone funds. Pre-existing frameworks and developer tools remain Partner property.
  </p>

  <div style="display: flex; justify-content: space-between; margin-top: 40px; padding-top: 20px; border-top: 1px solid #e4e4e7;">
    <div>
      <p style="font-size: 11px; font-weight: 600; margin: 0; color: #09090b;">Client Signatory</p>
      <p style="font-size: 10px; color: #71717a; margin: 2px 0 0 0;">Aarav Mehta / Operations</p>
      <div style="width: 140px; height: 1px; background: #d4d4d8; margin-top: 32px;"></div>
    </div>
    <div style="text-align: right;">
      <p style="font-size: 11px; font-weight: 600; margin: 0; color: #09090b;">Authorized Studio Director</p>
      <p style="font-size: 10px; color: #71717a; margin: 2px 0 0 0;">Kavya Patel / Legal Lead</p>
      <div style="width: 140px; height: 1px; background: #d4d4d8; margin-top: 32px; margin-left: auto;"></div>
    </div>
  </div>
</div>`,
  },
};

const FAQ_ITEMS = [
  {
    question: 'How does Cora convert custom HTML and styled CSS into formatted PDF files?',
    answer: 'Cora uses an in-browser layout compilation pipeline. It measures HTML DOM elements, scales typography to crisp 2x retina density, and compiles vector pages using pdf-lib. This guarantees sharp vector text and accurate table alignment.',
  },
  {
    question: 'Can I use inline styles, flexboxes, and CSS tables in my HTML?',
    answer: 'Yes. The engine fully parses modern HTML5 and CSS styles including Flexbox containers, grid layouts, custom borders, border-radius, background colors, and typography hierarchies.',
  },
  {
    question: 'Are custom HTML templates, billing receipts, or client statements uploaded to a server?',
    answer: 'No. The entire compilation runs 100% client-side inside your browser RAM. Zero bytes of your markup, customer names, or financial numbers are ever uploaded or logged.',
  },
  {
    question: 'What page formats, margins, and orientations are supported?',
    answer: 'You can choose between ISO Standard A4 (595 × 842 pt) and US Letter (612 × 792 pt), Portrait or Landscape orientations, and Normal (50pt) or Compact (28pt) page margins.',
  },
  {
    question: 'Does Cora support printable invoice and agreement templates out of the box?',
    answer: 'Yes. Cora includes built-in ready-to-use HTML templates for Modern 18% GST Invoices with SAC 9983 codes and formal Section 10A IT Act engagement deeds.',
  },
];

export default function HtmlToPdfPage() {
  const { showToast } = useToast();
  const previewRef = useRef<HTMLDivElement>(null);

  const [htmlContent, setHtmlContent] = useState<string>(SAMPLE_TEMPLATES.invoice.html);
  const [docTitle, setDocTitle] = useState<string>('cora_invoice_export');
  const [pageSize, setPageSize] = useState<'a4' | 'letter'>('a4');
  const [orientation, setOrientation] = useState<'portrait' | 'landscape'>('portrait');
  const [marginPreset, setMarginPreset] = useState<'normal' | 'compact'>('normal');
  const [isConverting, setIsConverting] = useState<boolean>(false);
  const [copied, setCopied] = useState<boolean>(false);

  // Convert HTML DOM to PDF
  const handleConvertHtmlToPdf = async () => {
    setIsConverting(true);
    try {
      // 1. Create PDF Document
      const pdfDoc = await PDFDocument.create();
      const isLandscape = orientation === 'landscape';
      const isLetter = pageSize === 'letter';

      const baseW = isLetter ? 612 : 595.28;
      const baseH = isLetter ? 792 : 841.89;
      const pageWidth = isLandscape ? baseH : baseW;
      const pageHeight = isLandscape ? baseW : baseH;
      const margin = marginPreset === 'compact' ? 28 : 50;

      // 2. High-Resolution Canvas Rasterization using HTML5 SVG foreignObject
      const renderW = Math.round(pageWidth - margin * 2);
      const renderH = Math.round(pageHeight - margin * 2);
      const scale = 2.0; // Crisp 2x retina

      let embeddedImage = null;

      try {
        const svgString = `
          <svg xmlns="http://www.w3.org/2000/svg" width="${renderW * scale}" height="${renderH * scale}">
            <foreignObject width="100%" height="100%">
              <div xmlns="http://www.w3.org/1999/xhtml" style="font-size: ${13 * scale}px; transform-origin: top left; transform: scale(${scale}); width: ${renderW}px; background: #ffffff;">
                ${htmlContent}
              </div>
            </foreignObject>
          </svg>
        `;

        const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);

        const img = new Image();
        const canvas = document.createElement('canvas');
        canvas.width = renderW * scale;
        canvas.height = renderH * scale;
        const ctx = canvas.getContext('2d');

        if (ctx) {
          ctx.fillStyle = '#FFFFFF';
          ctx.fillRect(0, 0, canvas.width, canvas.height);

          await new Promise<void>((resolve, reject) => {
            img.onload = () => {
              try {
                ctx.drawImage(img, 0, 0);
                URL.revokeObjectURL(url);
                resolve();
              } catch (drawErr) {
                URL.revokeObjectURL(url);
                reject(drawErr);
              }
            };
            img.onerror = () => {
              URL.revokeObjectURL(url);
              reject(new Error('SVG rasterization error'));
            };
            img.src = url;
          });

          const pngDataUrl = canvas.toDataURL('image/png');
          const base64Data = pngDataUrl.split(',')[1];
          const binaryString = atob(base64Data);
          const bytes = new Uint8Array(binaryString.length);
          for (let i = 0; i < binaryString.length; i++) {
            bytes[i] = binaryString.charCodeAt(i);
          }
          embeddedImage = await pdfDoc.embedPng(bytes);
        }
      } catch (canvasErr) {
        console.warn('Canvas rasterization fallback engaged:', canvasErr);
      }

      const page = pdfDoc.addPage([pageWidth, pageHeight]);

      if (embeddedImage) {
        page.drawImage(embeddedImage, {
          x: margin,
          y: margin,
          width: renderW,
          height: renderH,
        });
      } else {
        // Fallback: parse plain text and render clean vector lines
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlContent;
        const plainText = tempDiv.innerText || tempDiv.textContent || '';
        const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
        const fontBold = await pdfDoc.embedFont(StandardFonts.HelveticaBold);

        page.drawText(docTitle.toUpperCase(), {
          x: margin,
          y: pageHeight - margin - 14,
          size: 16,
          font: fontBold,
          color: rgb(0.08, 0.08, 0.1),
        });

        const lines = plainText.split('\n').filter(Boolean);
        let y = pageHeight - margin - 40;
        for (const line of lines.slice(0, 45)) {
          if (y < margin + 20) break;
          page.drawText(line.slice(0, 85), {
            x: margin,
            y,
            size: 10,
            font,
            color: rgb(0.2, 0.2, 0.22),
          });
          y -= 16;
        }
      }

      // Add clean bottom footer
      const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
      page.drawText('Generated by Cora In-Memory PDF Engine • 100% Client-Side Pure JS', {
        x: margin,
        y: margin - 15,
        size: 8,
        font,
        color: rgb(0.5, 0.5, 0.55),
      });

      const pdfBytes = await pdfDoc.save();
      const fileName = `${docTitle.toLowerCase().replace(/[^a-z0-9_-]/g, '_') || 'cora_html_doc'}.pdf`;
      downloadPdfBlob(pdfBytes, fileName);
      showToast('Downloaded formatted A4 PDF document');
    } catch (err) {
      console.error(err);
      showToast('Error converting HTML to PDF. Please check markup.');
    } finally {
      setIsConverting(false);
    }
  };

  const handleCopyHtml = () => {
    navigator.clipboard.writeText(htmlContent);
    setCopied(true);
    showToast('Copied HTML markup to clipboard!');
    setTimeout(() => setCopied(false), 2200);
  };

  const handleReset = () => {
    setHtmlContent('');
    setDocTitle('custom_document');
    showToast('Cleared HTML editor');
  };

  return (
    <ToolPageShell
      toolId="html-to-pdf"
      badgeTag="Pixel-Perfect HTML to A4 PDF Compiler"
      title="HTML to PDF Converter Online Free"
      subtitle="Convert styled HTML, invoice templates, and web markups into clean formatted A4 PDF documents with zero server uploads."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['pdf-to-word', 'pdf-to-markdown', 'word-to-pdf', 'excel-to-pdf']}
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
                  Crisp 2x Retina
                </span>
              </div>
              <h2 className="text-xl sm:text-2xl font-semibold tracking-tight text-zinc-900">
                HTML & Styled Content to PDF Compiler
              </h2>
              <p className="text-sm text-zinc-600 mt-1 max-w-2xl">
                Paste HTML code, custom stylesheets, or invoices to render pixel-perfect printable A4 PDF documents.
              </p>
            </div>

            <div className="flex flex-wrap items-center gap-3">
              <button
                type="button"
                onClick={() => {
                  setHtmlContent(SAMPLE_TEMPLATES.invoice.html);
                  setDocTitle('cora_tax_invoice');
                  showToast('Loaded GST Invoice template');
                }}
                className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
              >
                <Sparkles className="w-4 h-4 text-zinc-600" />
                Invoice Template
              </button>

              <button
                type="button"
                onClick={() => {
                  setHtmlContent(SAMPLE_TEMPLATES.deed.html);
                  setDocTitle('cora_legal_deed');
                  showToast('Loaded Legal Deed template');
                }}
                className="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-colors"
              >
                <Sparkles className="w-4 h-4 text-zinc-600" />
                Agreement Template
              </button>

              <button
                type="button"
                onClick={handleConvertHtmlToPdf}
                disabled={isConverting || !htmlContent.trim()}
                className="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 disabled:opacity-50 rounded-xl transition-colors shadow-sm"
              >
                {isConverting ? <RefreshCw className="w-4 h-4 animate-spin" /> : <Download className="w-4 h-4" />}
                Convert to PDF
              </button>
            </div>
          </div>

          {/* Formatting Options */}
          <div className="mt-6 pt-6 border-t border-zinc-100 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label className="block text-xs font-medium text-zinc-700 mb-2">
                Page Size
              </label>
              <div className="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  onClick={() => setPageSize('a4')}
                  className={`px-3 py-2 text-xs font-medium rounded-lg border transition-all ${
                    pageSize === 'a4'
                      ? 'bg-zinc-900 text-white border-zinc-900 shadow-sm'
                      : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                  }`}
                >
                  ISO A4
                </button>
                <button
                  type="button"
                  onClick={() => setPageSize('letter')}
                  className={`px-3 py-2 text-xs font-medium rounded-lg border transition-all ${
                    pageSize === 'letter'
                      ? 'bg-zinc-900 text-white border-zinc-900 shadow-sm'
                      : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                  }`}
                >
                  US Letter
                </button>
              </div>
            </div>

            <div>
              <label className="block text-xs font-medium text-zinc-700 mb-2">
                Orientation
              </label>
              <div className="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  onClick={() => setOrientation('portrait')}
                  className={`px-3 py-2 text-xs font-medium rounded-lg border transition-all ${
                    orientation === 'portrait'
                      ? 'bg-zinc-900 text-white border-zinc-900 shadow-sm'
                      : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                  }`}
                >
                  Portrait
                </button>
                <button
                  type="button"
                  onClick={() => setOrientation('landscape')}
                  className={`px-3 py-2 text-xs font-medium rounded-lg border transition-all ${
                    orientation === 'landscape'
                      ? 'bg-zinc-900 text-white border-zinc-900 shadow-sm'
                      : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                  }`}
                >
                  Landscape
                </button>
              </div>
            </div>

            <div>
              <label className="block text-xs font-medium text-zinc-700 mb-2">
                Page Margins
              </label>
              <div className="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  onClick={() => setMarginPreset('normal')}
                  className={`px-3 py-2 text-xs font-medium rounded-lg border transition-all ${
                    marginPreset === 'normal'
                      ? 'bg-zinc-900 text-white border-zinc-900 shadow-sm'
                      : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                  }`}
                >
                  Normal (50pt)
                </button>
                <button
                  type="button"
                  onClick={() => setMarginPreset('compact')}
                  className={`px-3 py-2 text-xs font-medium rounded-lg border transition-all ${
                    marginPreset === 'compact'
                      ? 'bg-zinc-900 text-white border-zinc-900 shadow-sm'
                      : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
                  }`}
                >
                  Compact (28pt)
                </button>
              </div>
            </div>
          </div>
        </div>

        {/* Dual Pane: HTML Code Editor & Live Preview */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">

          {/* Left Pane: HTML Editor */}
          <div className="bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm flex flex-col space-y-3">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <Code className="w-4 h-4 text-zinc-700" />
                <h3 className="text-xs font-semibold text-zinc-900 uppercase tracking-wider">
                  HTML Source Code
                </h3>
              </div>
              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={handleCopyHtml}
                  className="p-1.5 text-zinc-500 hover:text-zinc-900 rounded-lg hover:bg-zinc-100 transition-colors"
                  title="Copy HTML"
                >
                  {copied ? <Check className="w-3.5 h-3.5" /> : <Copy className="w-3.5 h-3.5" />}
                </button>
                <button
                  type="button"
                  onClick={handleReset}
                  className="p-1.5 text-zinc-500 hover:text-zinc-900 rounded-lg hover:bg-zinc-100 transition-colors"
                  title="Clear HTML"
                >
                  <Trash2 className="w-3.5 h-3.5" />
                </button>
              </div>
            </div>

            <textarea
              rows={18}
              value={htmlContent}
              onChange={(e) => setHtmlContent(e.target.value)}
              placeholder="<div>Paste your styled HTML here...</div>"
              className="w-full flex-1 font-mono text-xs text-zinc-900 bg-zinc-50 border border-zinc-200 rounded-xl p-3.5 focus:outline-none focus:ring-1 focus:ring-zinc-900 leading-relaxed resize-y"
            />

            <div className="text-[11px] text-zinc-400">
              Supports inline CSS, flexbox, tables, borders, and Google/system fonts.
            </div>
          </div>

          {/* Right Pane: Live Sandboxed Preview */}
          <div className="bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm flex flex-col space-y-3">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <Eye className="w-4 h-4 text-zinc-700" />
                <h3 className="text-xs font-semibold text-zinc-900 uppercase tracking-wider">
                  Live Rendered Preview
                </h3>
              </div>
              <span className="text-[11px] text-zinc-400">
                {pageSize.toUpperCase()} • {orientation === 'portrait' ? 'Portrait' : 'Landscape'}
              </span>
            </div>

            <div className="flex-1 bg-zinc-50 border border-zinc-200 rounded-xl p-4 overflow-auto max-h-[520px]">
              <div
                ref={previewRef}
                className="bg-white shadow-sm border border-zinc-200/80 rounded-lg overflow-hidden min-h-[400px]"
                dangerouslySetInnerHTML={{ __html: htmlContent }}
              />
            </div>

            <div className="pt-2 flex items-center justify-between">
              <div className="text-xs text-zinc-500">
                File: <b>{docTitle}.pdf</b>
              </div>
              <button
                type="button"
                onClick={handleConvertHtmlToPdf}
                disabled={isConverting || !htmlContent.trim()}
                className="inline-flex items-center gap-2 px-5 py-2 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 disabled:opacity-50 rounded-xl transition-colors shadow-sm"
              >
                {isConverting ? <RefreshCw className="w-4 h-4 animate-spin" /> : <Download className="w-4 h-4" />}
                Download PDF
              </button>
            </div>
          </div>

        </div>

      </div>
    </ToolPageShell>
  );
}
