import { PDFDocument, rgb, degrees, StandardFonts } from 'pdf-lib';

/**
 * Cora PDF Engine - 100% Client-Side Pure JavaScript Execution
 * Zero file uploads to servers. Fast, secure, and privacy-preserving.
 */

export interface PageInfo {
  pageIndex: number;
  width: number;
  height: number;
}

/**
 * Inspect PDF document in browser memory and return basic metadata
 */
export async function getPdfInfo(file: File): Promise<{
  pageCount: number;
  pages: PageInfo[];
  fileSizeBytes: number;
  fileName: string;
}> {
  const arrayBuffer = await file.arrayBuffer();
  const pdfDoc = await PDFDocument.load(arrayBuffer, { ignoreEncryption: true });
  const pageCount = pdfDoc.getPageCount();
  const pages: PageInfo[] = [];

  for (let i = 0; i < pageCount; i++) {
    const page = pdfDoc.getPage(i);
    const { width, height } = page.getSize();
    pages.push({ pageIndex: i, width, height });
  }

  return {
    pageCount,
    pages,
    fileSizeBytes: file.size,
    fileName: file.name,
  };
}

/**
 * Merge multiple PDF files in sequential order
 */
export async function mergePdfFiles(files: File[]): Promise<Uint8Array> {
  const mergedPdf = await PDFDocument.create();

  for (const file of files) {
    const arrayBuffer = await file.arrayBuffer();
    const sourcePdf = await PDFDocument.load(arrayBuffer);
    const copiedPages = await mergedPdf.copyPages(sourcePdf, sourcePdf.getPageIndices());
    copiedPages.forEach((page) => mergedPdf.addPage(page));
  }

  return await mergedPdf.save();
}

/**
 * Split PDF: Extract specific 0-based page indices into a new PDF
 */
export async function extractPdfPages(file: File, pageIndices: number[]): Promise<Uint8Array> {
  const arrayBuffer = await file.arrayBuffer();
  const sourcePdf = await PDFDocument.load(arrayBuffer);
  const newPdf = await PDFDocument.create();

  const copiedPages = await newPdf.copyPages(sourcePdf, pageIndices);
  copiedPages.forEach((page) => newPdf.addPage(page));

  return await newPdf.save();
}

/**
 * Rotate all or selected pages by specified degrees (e.g. 90, 180, 270)
 */
export async function rotatePdfPages(file: File, rotationDegrees: number, pageIndices?: number[]): Promise<Uint8Array> {
  const arrayBuffer = await file.arrayBuffer();
  const pdfDoc = await PDFDocument.load(arrayBuffer);
  const pages = pdfDoc.getPages();
  const targetIndices = pageIndices || pages.map((_, i) => i);

  for (const idx of targetIndices) {
    if (pages[idx]) {
      const currentRotation = pages[idx].getRotation().angle;
      pages[idx].setRotation(degrees((currentRotation + rotationDegrees) % 360));
    }
  }

  return await pdfDoc.save();
}

/**
 * Convert multiple image files (JPG, PNG, WebP) into a clean, formatted multi-page PDF
 */
export async function convertImagesToPdf(
  imageFiles: File[],
  options: {
    pageSize?: 'a4' | 'letter' | 'fit';
    margin?: number;
  } = {}
): Promise<Uint8Array> {
  const pdfDoc = await PDFDocument.create();
  const { pageSize = 'a4', margin = 20 } = options;

  const pageDims = pageSize === 'letter' ? { width: 612, height: 792 } : { width: 595.28, height: 841.89 };

  for (const file of imageFiles) {
    let embeddedImage;
    const isPng = file.type === 'image/png' || file.name.toLowerCase().endsWith('.png');
    const isJpg = file.type === 'image/jpeg' || file.type === 'image/jpg' || file.name.toLowerCase().endsWith('.jpg') || file.name.toLowerCase().endsWith('.jpeg');

    if (isPng) {
      try {
        const arrayBuffer = await file.arrayBuffer();
        embeddedImage = await pdfDoc.embedPng(arrayBuffer);
      } catch {
        // Fallback to canvas rasterization below
      }
    } else if (isJpg) {
      try {
        const arrayBuffer = await file.arrayBuffer();
        embeddedImage = await pdfDoc.embedJpg(arrayBuffer);
      } catch {
        // Fallback to canvas rasterization below
      }
    }

    // If WebP, non-standard JPEG, or canvas-required format
    if (!embeddedImage && typeof window !== 'undefined') {
      try {
        const pngBytes = await new Promise<Uint8Array>((resolve, reject) => {
          const img = new Image();
          const url = URL.createObjectURL(file);
          img.onload = () => {
            try {
              const canvas = document.createElement('canvas');
              canvas.width = img.naturalWidth || img.width;
              canvas.height = img.naturalHeight || img.height;
              const ctx = canvas.getContext('2d');
              if (!ctx) throw new Error('Canvas context not available');
              ctx.drawImage(img, 0, 0);
              canvas.toBlob(async (blob) => {
                URL.revokeObjectURL(url);
                if (blob) {
                  const buf = await blob.arrayBuffer();
                  resolve(new Uint8Array(buf));
                } else {
                  reject(new Error('Failed to convert image to blob'));
                }
              }, 'image/png');
            } catch (err) {
              URL.revokeObjectURL(url);
              reject(err);
            }
          };
          img.onerror = () => {
            URL.revokeObjectURL(url);
            reject(new Error(`Failed to load image: ${file.name}`));
          };
          img.src = url;
        });
        embeddedImage = await pdfDoc.embedPng(pngBytes);
      } catch (err) {
        console.error('Image embedding error:', err);
      }
    }

    if (!embeddedImage) {
      // Last-ditch direct attempt
      const arrayBuffer = await file.arrayBuffer();
      embeddedImage = isPng ? await pdfDoc.embedPng(arrayBuffer) : await pdfDoc.embedJpg(arrayBuffer);
    }

    const { width: imgW, height: imgH } = embeddedImage;

    if (pageSize === 'fit') {
      const page = pdfDoc.addPage([imgW + margin * 2, imgH + margin * 2]);
      page.drawImage(embeddedImage, {
        x: margin,
        y: margin,
        width: imgW,
        height: imgH,
      });
    } else {
      const availW = pageDims.width - margin * 2;
      const availH = pageDims.height - margin * 2;
      const scale = Math.min(availW / imgW, availH / imgH, 1);

      const drawW = imgW * scale;
      const drawH = imgH * scale;
      const x = (pageDims.width - drawW) / 2;
      const y = (pageDims.height - drawH) / 2;

      const page = pdfDoc.addPage([pageDims.width, pageDims.height]);
      page.drawImage(embeddedImage, {
        x,
        y,
        width: drawW,
        height: drawH,
      });
    }
  }

  return await pdfDoc.save();
}

/**
 * Stamp text watermark across all pages
 */
export async function watermarkPdf(
  file: File,
  watermarkText: string,
  options: {
    opacity?: number;
    color?: { r: number; g: number; b: number };
    fontSize?: number;
    angle?: number;
  } = {}
): Promise<Uint8Array> {
  const arrayBuffer = await file.arrayBuffer();
  const pdfDoc = await PDFDocument.load(arrayBuffer);
  const font = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
  const pages = pdfDoc.getPages();

  const {
    opacity = 0.22,
    color = { r: 0.5, g: 0.5, b: 0.5 },
    fontSize = 54,
    angle = 45,
  } = options;

  for (const page of pages) {
    const { width, height } = page.getSize();
    const textWidth = font.widthOfTextAtSize(watermarkText, fontSize);
    const textHeight = font.heightAtSize(fontSize);

    // Calculate rotation angle in radians for true center offset
    const rad = (angle * Math.PI) / 180;
    const cos = Math.cos(rad);
    const sin = Math.sin(rad);

    const cx = width / 2;
    const cy = height / 2;
    const x = cx - (textWidth / 2) * cos + (textHeight / 2) * sin;
    const y = cy - (textWidth / 2) * sin - (textHeight / 2) * cos;

    page.drawText(watermarkText, {
      x,
      y,
      size: fontSize,
      font,
      color: rgb(color.r, color.g, color.b),
      opacity,
      rotate: degrees(angle),
    });
  }

  return await pdfDoc.save();
}

/**
 * Embed digital e-signature image onto a specific page of a PDF document
 */
export async function stampSignatureOnPdf(
  file: File,
  signaturePngDataUrl: string,
  options: {
    pageNumber: number;
    xPercent: number;
    yPercent: number;
    widthPercent: number;
  }
): Promise<Uint8Array> {
  const arrayBuffer = await file.arrayBuffer();
  const pdfDoc = await PDFDocument.load(arrayBuffer);
  const pages = pdfDoc.getPages();
  const pageIndex = Math.max(0, Math.min(options.pageNumber - 1, pages.length - 1));
  const targetPage = pages[pageIndex];

  const base64Data = signaturePngDataUrl.split(',')[1];
  const binaryString = atob(base64Data);
  const bytes = new Uint8Array(binaryString.length);
  for (let i = 0; i < binaryString.length; i++) {
    bytes[i] = binaryString.charCodeAt(i);
  }

  const signatureImage = await pdfDoc.embedPng(bytes);
  const { width: pageWidth, height: pageHeight } = targetPage.getSize();

  const imgW = (pageWidth * options.widthPercent) / 100;
  const imgH = (imgW / signatureImage.width) * signatureImage.height;
  const xPos = (pageWidth * options.xPercent) / 100;
  const yPos = (pageHeight * options.yPercent) / 100;

  targetPage.drawImage(signatureImage, {
    x: xPos,
    y: yPos,
    width: imgW,
    height: imgH,
  });

  return await pdfDoc.save();
}

/**
 * Trigger client-side browser file download from Uint8Array
 */
export function downloadPdfBlob(pdfBytes: Uint8Array, fileName: string) {
  const blob = new Blob([pdfBytes as unknown as BlobPart], { type: 'application/pdf' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = fileName.endsWith('.pdf') ? fileName : `${fileName}.pdf`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
