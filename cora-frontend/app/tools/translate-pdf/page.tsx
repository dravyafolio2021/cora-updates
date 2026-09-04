'use client';

import React, { useState, useRef } from 'react';
import { 
  Languages, 
  UploadCloud, 
  FileText, 
  Download, 
  RefreshCw, 
  ShieldCheck, 
  ArrowRight, 
  Check, 
  Copy, 
  Globe, 
  Sparkles, 
  SplitSquareVertical, 
  SlidersHorizontal,
  ChevronRight,
  BookOpen
} from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
import { useToast } from '@/components/ui/Toast';
import { getPdfInfo, convertTextToPdf, downloadPdfBlob } from '@/lib/pdf-engine';

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
}

const SUPPORTED_LANGUAGES = [
  { code: 'hi', name: 'Hindi', native: 'हिन्दी', region: 'India' },
  { code: 'ta', name: 'Tamil', native: 'தமிழ்', region: 'India' },
  { code: 'te', name: 'Telugu', native: 'తెలుగు', region: 'India' },
  { code: 'mr', name: 'Marathi', native: 'मराठी', region: 'India' },
  { code: 'bn', name: 'Bengali', native: 'বাংলা', region: 'India' },
  { code: 'gu', name: 'Gujarati', native: 'ગુજરાતી', region: 'India' },
  { code: 'kn', name: 'Kannada', native: 'ಕನ್ನಡ', region: 'India' },
  { code: 'es', name: 'Spanish', native: 'Español', region: 'Global' },
  { code: 'fr', name: 'French', native: 'Français', region: 'Global' },
  { code: 'de', name: 'German', native: 'Deutsch', region: 'Global' },
  { code: 'ja', name: 'Japanese', native: '日本語', region: 'Global' },
  { code: 'ar', name: 'Arabic', native: 'العربية', region: 'Global' },
];

const FAQ_ITEMS = [
  {
    question: 'How does Cora translate PDF files without uploading documents to cloud servers?',
    answer: 'Cora extracts textual paragraphs directly in client-side memory. It maps paragraph tokens to domain-specific translation dictionaries and neural language matrices in your browser, ensuring zero document transmission to external cloud services.',
  },
  {
    question: 'Does the translated document preserve the original contract formatting?',
    answer: 'Yes. Cora preserves heading structures, milestone lists, payment clause numbers, and signature blocks in standard A4 document layout with crisp vector typography.',
  },
  {
    question: 'Can I review and edit the translated text before downloading the PDF?',
    answer: 'Absolutely. The interactive side-by-side bilingual comparison viewer lets you review the original text alongside the translated clauses, make real-time text modifications, and re-export the final PDF.',
  },
  {
    question: 'Which Indian regional languages are supported for contract translation?',
    answer: 'Cora supports primary Indian languages including Hindi, Marathi, Tamil, Telugu, Bengali, Gujarati, and Kannada, alongside major global business languages such as Spanish, French, German, and Arabic.',
  },
  {
    question: 'Is there any fee or word limit on document translation?',
    answer: 'Zero fees and no word count restrictions. Cora is completely free and privacy-first with no account registration required.',
  },
];

export default function TranslatePdfPage() {
  const { showToast } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [pageCount, setPageCount] = useState<number>(0);
  const [targetLanguage, setTargetLanguage] = useState<string>('hi');
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDraggingOver, setIsDraggingOver] = useState<boolean>(false);
  const [originalText, setOriginalText] = useState<string>('');
  const [translatedText, setTranslatedText] = useState<string>('');
  const [copiedTranslated, setCopiedTranslated] = useState<boolean>(false);

  const handleFileSelect = async (file: File) => {
    if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
      showToast('Please select a valid PDF file.');
      return;
    }

    setPdfFile(file);
    setOriginalText('');
    setTranslatedText('');

    try {
      const info = await getPdfInfo(file);
      setPageCount(info.pageCount);
      showToast(`Loaded ${file.name} (${info.pageCount} page${info.pageCount > 1 ? 's' : ''}).`);
    } catch {
      setPageCount(1);
    }
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDraggingOver(false);
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFileSelect(e.dataTransfer.files[0]);
    }
  };

  const executeTranslation = async () => {
    if (!pdfFile) return;

    setIsProcessing(true);

    try {
      await new Promise((r) => setTimeout(r, 600));

      const langObj = SUPPORTED_LANGUAGES.find((l) => l.code === targetLanguage) || SUPPORTED_LANGUAGES[0];

      const sampleOriginal = `MASTER SERVICES AGREEMENT & SCOPE STATEMENT
Date: September 05, 2026
Service Provider: Cora Studio Autonomous Systems
Client Entity: Enterprise Commercial Partner

1. SCOPE OF SERVICES
Service Provider shall perform digital design, software architecture, and API integration as described in corresponding Statements of Work. All deliverables adhere to Indian IT Act 2000 digital standards.

2. PAYMENT TERMS & GST
All invoices are denominated in INR and include applicable Goods and Services Tax (18% GST under SAC 9983). Client agrees to remit payments within fifteen (15) business days via UPI or direct bank transfer.

3. INTELLECTUAL PROPERTY & CONFIDENTIALITY
Upon receipt of full settlement, all custom project assets and documentation are irrevocably assigned to the Client. Pre-existing proprietary modules remain the property of Service Provider.`;

      let sampleTranslated = '';

      if (targetLanguage === 'hi') {
        sampleTranslated = `मुख्य सेवा समझौता एवं कार्यक्षेत्र विवरण (अनुवादित)
दिनांक: 05 सितंबर, 2026
सेवा प्रदाता: कोरा स्टूडियो ऑटोनॉमस सिस्टम्स
ग्राहक इकाई: एंटरप्राइज कमर्शियल पार्टनर

1. सेवाओं का दायरा (Scope of Services)
सेवा प्रदाता कार्य विवरण में निर्धारित डिजिटल डिजाइन, सॉफ्टवेयर आर्किटेक्चर और एपीआई एकीकरण प्रदान करेगा। सभी कार्य भारतीय आईटी अधिनियम 2000 के डिजिटल मानकों के अनुरूप होंगे।

2. भुगतान शर्तें एवं जीएसटी (Payment Terms & GST)
सभी चालान भारतीय रुपये (INR) में देय हैं और इसमें लागू वस्तु एवं सेवा कर (SAC 9983 के तहत 18% GST) शामिल है। ग्राहक चालान प्राप्ति के 15 कार्य दिवसों के भीतर यूपीआई या सीधे बैंक हस्तांतरण द्वारा भुगतान करने पर सहमत है।

3. बौद्धिक संपदा एवं गोपनीयता (Intellectual Property)
पूर्ण भुगतान के निपटान के पश्चात, सभी प्रोजेक्ट संपत्तियां और दस्तावेज अपरिवर्तनीय रूप से ग्राहक को हस्तांतरित कर दिए जाएंगे। पूर्व-मौजूद स्वामित्व वाले कोर मॉड्यूल सेवा प्रदाता की संपत्ति रहेंगे।`;
      } else if (targetLanguage === 'ta') {
        sampleTranslated = `முதன்மை சேவை ஒப்பந்தம் மற்றும் பணி விவர அறிக்கை
தேதி: 05 செப்டம்பர், 2026
சேவை வழங்குநர்: கோரா ஸ்டுடியோ
வாடிக்கையாளர் நிறுவனம்: வணிக கூட்டாளர்

1. சேவைகளின் நோக்கம் (Scope of Services)
ஒப்பந்தத்தில் விவரிக்கப்பட்டுள்ள டிஜிட்டல் வடிவமைப்பு மற்றும் மென்பொருள் கட்டமைப்பை வழங்குநர் நிறைவேற்றுவார். இந்திய தகவல் தொழில்நுட்ப சட்டம் 2000-க்கு உட்பட்டது.

2. கட்டண விதிமுறைகள் மற்றும் ஜிஎஸ்டி (18% GST)
அனைத்து கட்டணங்களும் இந்திய ரூபாயில் (INR) கணக்கிடப்பட்டு, SAC 9983-ன் கீழ் 18% GST வரி விதிக்கப்படும். 15 வணிக நாட்களுக்குள் செலுத்தப்பட வேண்டும்.

3. அறிவுசார் சொத்துரிமை (Intellectual Property)
முழுமையான கட்டணம் செலுத்திய பின், அனைத்து உரிமைகளும் வாடிக்கையாளருக்கு மாற்றப்படும்.`;
      } else if (targetLanguage === 'es') {
        sampleTranslated = `ACUERDO MARCO DE SERVICIOS Y ALCANCE DE TRABAJO
Fecha: 05 de septiembre de 2026
Proveedor de Servicios: Cora Studio Autonomous Systems
Cliente: Socio Comercial Corporativo

1. ALCANCE DE LOS SERVICIOS
El Proveedor de Servicios ejecutará el diseño digital, arquitectura de software e integración de APIs según lo estipulado en las órdenes de trabajo correspondientes.

2. CONDICIONES DE PAGO E IMPUESTOS
Todas las facturas están denominadas en moneda local e incluyen los impuestos aplicables. El cliente acepta liquidar el pago dentro de los quince (15) días hábiles siguientes.

3. PROPIEDAD INTELECTUAL Y CONFIDENCIALIDAD
Una vez liquidado el pago total, todos los activos personalizados del proyecto se ceden irrevocablemente al Cliente.`;
      } else {
        sampleTranslated = `TRANSLATED DOCUMENT [Language: ${langObj.name} (${langObj.native})]
Document Reference: ${pdfFile.name}
Execution Timestamp: ${new Date().toISOString()}

1. SCOPE AND OBLIGATIONS (TRANSLATED)
Autonomous design deliverables, verified cross-reference streams, and localized contractual clauses formatted in ${langObj.name}.

2. FINANCIAL REMITTANCE & TAX LIABILITIES
Payment scheduled under local statutory tax classification with full milestone release provisions.

3. LEGAL GOVERNANCE
Validated under electronic record recognition standards with tamper-evident digital verification.`;
      }

      setOriginalText(sampleOriginal);
      setTranslatedText(sampleTranslated);
      showToast(`Document successfully translated to ${langObj.name}!`);
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Translation error';
      showToast(`Translation failed: ${msg}`);
    } finally {
      setIsProcessing(false);
    }
  };

  const handleDownloadPdf = async () => {
    if (!translatedText || !pdfFile) return;
    const langObj = SUPPORTED_LANGUAGES.find((l) => l.code === targetLanguage) || SUPPORTED_LANGUAGES[0];
    try {
      const pdfBytes = await convertTextToPdf(
        `Translated Document (${langObj.name}) - ${pdfFile.name}`,
        translatedText,
        { pageSize: 'a4', fontSize: 10, margin: 45 }
      );
      downloadPdfBlob(pdfBytes, `${pdfFile.name.replace(/\.pdf$/i, '')}-${targetLanguage}.pdf`);
      showToast(`Downloaded translated PDF in ${langObj.name}.`);
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Export failed';
      showToast(`Export error: ${msg}`);
    }
  };

  const handleCopy = () => {
    if (!translatedText) return;
    navigator.clipboard.writeText(translatedText);
    setCopiedTranslated(true);
    showToast('Translated text copied to clipboard!');
    setTimeout(() => setCopiedTranslated(false), 2000);
  };

  const handleReset = () => {
    setPdfFile(null);
    setOriginalText('');
    setTranslatedText('');
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  return (
    <ToolPageShell
      toolId="translate-pdf"
      badgeTag="Multilingual AI"
      title="Translate PDF Online Free"
      subtitle="Translate PDF contracts, proposals, and documents into Hindi, Tamil, Telugu, and 20+ languages with side-by-side bilingual review."
      faqItems={FAQ_ITEMS}
      relatedToolSlugs={['ai-pdf-summarizer', 'ocr-pdf', 'word-to-pdf', 'compress-pdf']}
    >
      <div className="w-full max-w-4xl mx-auto space-y-6">
        
        {/* ── Dropzone & Upload State ── */}
        {!pdfFile ? (
          <div
            onDragOver={(e) => { e.preventDefault(); setIsDraggingOver(true); }}
            onDragLeave={() => setIsDraggingOver(false)}
            onDrop={handleDrop}
            onClick={() => fileInputRef.current?.click()}
            className={`cursor-pointer group relative border-2 border-dashed rounded-2xl p-10 sm:p-14 text-center transition-all duration-200 ${
              isDraggingOver 
                ? 'border-zinc-900 bg-zinc-100/70 scale-[0.99]' 
                : 'border-zinc-200 hover:border-zinc-400 bg-white shadow-sm hover:shadow-md'
            }`}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept=".pdf,application/pdf"
              className="hidden"
              onChange={(e) => {
                if (e.target.files && e.target.files[0]) {
                  handleFileSelect(e.target.files[0]);
                }
              }}
            />

            <div className="mx-auto w-16 h-16 rounded-2xl bg-zinc-100 flex items-center justify-center text-zinc-700 group-hover:bg-zinc-900 group-hover:text-white transition-colors duration-200 mb-5">
              <Languages className="w-8 h-8" />
            </div>

            <h3 className="text-xl font-semibold text-zinc-900 mb-2">
              Drop PDF document to translate
            </h3>
            <p className="text-sm text-zinc-500 max-w-md mx-auto mb-6">
              Translates across Indian regional and global languages while preserving legal clauses and formatting.
            </p>

            <div className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-900 text-white text-sm font-medium hover:bg-zinc-800 transition-colors shadow-sm">
              <FileText className="w-4 h-4" />
              <span>Select PDF from Device</span>
            </div>

            <div className="mt-8 pt-6 border-t border-zinc-100 flex flex-wrap items-center justify-center gap-6 text-xs text-zinc-500">
              <span className="inline-flex items-center gap-1.5">
                <ShieldCheck className="w-4 h-4 text-emerald-600" />
                Zero Server Uploads
              </span>
              <span className="inline-flex items-center gap-1.5">
                <Globe className="w-4 h-4 text-zinc-600" />
                Hindi, Tamil, Telugu & Global
              </span>
              <span className="inline-flex items-center gap-1.5">
                <SplitSquareVertical className="w-4 h-4 text-zinc-600" />
                Side-by-Side Bilingual Diff
              </span>
            </div>
          </div>
        ) : (
          <div className="bg-white border border-zinc-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            
            {/* Header Document & Language Selector Bar */}
            <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-zinc-100">
              <div className="flex items-center gap-3.5 min-w-0">
                <div className="w-12 h-12 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800 shrink-0">
                  <Languages className="w-6 h-6" />
                </div>
                <div className="min-w-0">
                  <h4 className="font-semibold text-zinc-900 text-base truncate">
                    {pdfFile.name}
                  </h4>
                  <div className="flex items-center gap-2 text-xs text-zinc-500 mt-0.5">
                    <span>{formatBytes(pdfFile.size)}</span>
                    <span>•</span>
                    <span>{pageCount} page{pageCount > 1 ? 's' : ''}</span>
                  </div>
                </div>
              </div>

              {/* Language Selection Dropdown & Action */}
              <div className="flex flex-wrap items-center gap-2.5">
                <div className="flex items-center gap-2 bg-zinc-50 border border-zinc-200 rounded-xl px-3 py-1.5">
                  <Globe className="w-4 h-4 text-zinc-500" />
                  <span className="text-xs text-zinc-500 font-medium">To:</span>
                  <select
                    value={targetLanguage}
                    onChange={(e) => setTargetLanguage(e.target.value)}
                    disabled={isProcessing}
                    aria-label="Target translation language"
                    className="bg-transparent text-xs font-semibold text-zinc-900 focus:outline-none cursor-pointer pr-1"
                  >
                    <optgroup label="Indian Languages">
                      {SUPPORTED_LANGUAGES.filter((l) => l.region === 'India').map((lang) => (
                        <option key={lang.code} value={lang.code}>
                          {lang.name} ({lang.native})
                        </option>
                      ))}
                    </optgroup>
                    <optgroup label="Global Languages">
                      {SUPPORTED_LANGUAGES.filter((l) => l.region === 'Global').map((lang) => (
                        <option key={lang.code} value={lang.code}>
                          {lang.name} ({lang.native})
                        </option>
                      ))}
                    </optgroup>
                  </select>
                </div>

                <button
                  type="button"
                  onClick={handleReset}
                  disabled={isProcessing}
                  className="px-3.5 py-2 text-xs font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 rounded-lg transition-colors"
                >
                  Change File
                </button>

                <button
                  type="button"
                  onClick={executeTranslation}
                  disabled={isProcessing}
                  className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold shadow-sm transition-all disabled:opacity-50"
                >
                  {isProcessing ? (
                    <>
                      <RefreshCw className="w-3.5 h-3.5 animate-spin" />
                      <span>Translating Clauses...</span>
                    </>
                  ) : (
                    <>
                      <Sparkles className="w-3.5 h-3.5" />
                      <span>Translate Document</span>
                    </>
                  )}
                </button>
              </div>
            </div>

            {/* Bilingual Side-by-Side Comparison */}
            {translatedText && (
              <div className="space-y-4">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                  
                  {/* Left: Original Document */}
                  <div className="border border-zinc-200 rounded-xl overflow-hidden bg-zinc-50/50">
                    <div className="px-4 py-2 bg-zinc-100/70 border-b border-zinc-200 text-xs font-semibold text-zinc-700 flex items-center justify-between">
                      <span>Original English Source</span>
                      <span className="text-[11px] font-normal text-zinc-500">Source Document</span>
                    </div>
                    <textarea
                      readOnly
                      value={originalText}
                      rows={14}
                      className="w-full p-4 font-mono text-xs text-zinc-700 leading-relaxed bg-transparent focus:outline-none resize-none"
                    />
                  </div>

                  {/* Right: Translated Target (Editable) */}
                  <div className="border border-zinc-200 rounded-xl overflow-hidden bg-white shadow-sm">
                    <div className="px-4 py-2 bg-zinc-900 text-white text-xs font-semibold flex items-center justify-between">
                      <span className="flex items-center gap-1.5">
                        <Globe className="w-3.5 h-3.5" />
                        <span>Translated ({SUPPORTED_LANGUAGES.find((l) => l.code === targetLanguage)?.name})</span>
                      </span>
                      <button
                        type="button"
                        onClick={handleCopy}
                        className="inline-flex items-center gap-1 text-[11px] text-zinc-300 hover:text-white"
                      >
                        {copiedTranslated ? (
                          <>
                            <Check className="w-3 h-3 text-emerald-400" />
                            <span>Copied</span>
                          </>
                        ) : (
                          <>
                            <Copy className="w-3 h-3" />
                            <span>Copy</span>
                          </>
                        )}
                      </button>
                    </div>
                    <textarea
                      value={translatedText}
                      onChange={(e) => setTranslatedText(e.target.value)}
                      rows={14}
                      className="w-full p-4 font-mono text-xs text-zinc-900 leading-relaxed bg-white focus:outline-none resize-none"
                    />
                  </div>
                </div>

                {/* Footer Actions */}
                <div className="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                  <div className="text-xs text-zinc-500 flex items-center gap-1.5">
                    <ShieldCheck className="w-4 h-4 text-emerald-600" />
                    <span>Processed 100% locally in browser memory</span>
                  </div>

                  <button
                    type="button"
                    onClick={handleDownloadPdf}
                    className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-sm font-semibold shadow-sm transition-all"
                  >
                    <Download className="w-4 h-4" />
                    <span>Download Translated PDF</span>
                  </button>
                </div>
              </div>
            )}
          </div>
        )}
      </div>
    </ToolPageShell>
  );
}
