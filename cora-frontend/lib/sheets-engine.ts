/**
 * Cora Spreadsheet Engine - 100% Client-Side Pure JavaScript Execution
 * Zero external cloud dependencies. Fast, private, and secure in-browser data processing.
 */

export interface ParsedSheetData {
  headers: string[];
  rows: string[][];
  rowCount: number;
  colCount: number;
  delimiter: string;
}

/**
 * Auto-detect delimiter and parse RFC 4180 compliant CSV / TSV text
 */
export function parseDelimitedText(text: string): ParsedSheetData {
  const trimmed = text.trim();
  if (!trimmed) {
    return { headers: [], rows: [], rowCount: 0, colCount: 0, delimiter: ',' };
  }

  // Detect delimiter from first 5 lines
  const sampleLines = trimmed.split(/\r?\n/).slice(0, 5);
  const commaCount = (sampleLines.join('').match(/,/g) || []).length;
  const tabCount = (sampleLines.join('').match(/\t/g) || []).length;
  const semiCount = (sampleLines.join('').match(/;/g) || []).length;
  const pipeCount = (sampleLines.join('').match(/\|/g) || []).length;

  let delimiter = ',';
  if (tabCount > commaCount && tabCount > semiCount) delimiter = '\t';
  else if (semiCount > commaCount && semiCount > tabCount) delimiter = ';';
  else if (pipeCount > commaCount && pipeCount > semiCount) delimiter = '|';

  const rows: string[][] = [];
  let currentRow: string[] = [];
  let currentField = '';
  let inQuotes = false;

  for (let i = 0; i < text.length; i++) {
    const char = text[i];
    const nextChar = text[i + 1];

    if (char === '"') {
      if (inQuotes && nextChar === '"') {
        currentField += '"';
        i++; // skip escaped quote
      } else {
        inQuotes = !inQuotes;
      }
    } else if (char === delimiter && !inQuotes) {
      currentRow.push(currentField.trim());
      currentField = '';
    } else if ((char === '\r' || char === '\n') && !inQuotes) {
      if (char === '\r' && nextChar === '\n') {
        i++; // skip \n of \r\n
      }
      currentRow.push(currentField.trim());
      if (currentRow.some(val => val.length > 0)) {
        rows.push(currentRow);
      }
      currentRow = [];
      currentField = '';
    } else {
      currentField += char;
    }
  }

  if (currentField.length > 0 || currentRow.length > 0) {
    currentRow.push(currentField.trim());
    if (currentRow.some(val => val.length > 0)) {
      rows.push(currentRow);
    }
  }

  if (rows.length === 0) {
    return { headers: [], rows: [], rowCount: 0, colCount: 0, delimiter };
  }

  const headers = rows[0].map((h, idx) => h || `Column_${idx + 1}`);
  const dataRows = rows.slice(1);

  // Normalize column count
  const maxCols = Math.max(headers.length, ...dataRows.map(r => r.length));
  while (headers.length < maxCols) {
    headers.push(`Column_${headers.length + 1}`);
  }

  const normalizedRows = dataRows.map(r => {
    const row = [...r];
    while (row.length < maxCols) row.push('');
    return row.slice(0, maxCols);
  });

  return {
    headers,
    rows: normalizedRows,
    rowCount: normalizedRows.length,
    colCount: maxCols,
    delimiter,
  };
}

/**
 * Serialize sheet rows back to standard RFC 4180 CSV
 */
export function serializeToCsv(headers: string[], rows: string[][], delimiter = ','): string {
  const escapeCell = (val: string) => {
    const str = val === undefined || val === null ? '' : String(val);
    if (str.includes(delimiter) || str.includes('"') || str.includes('\n') || str.includes('\r')) {
      return `"${str.replace(/"/g, '""')}"`;
    }
    return str;
  };

  const headerLine = headers.map(escapeCell).join(delimiter);
  const dataLines = rows.map(row => row.map(escapeCell).join(delimiter));

  return [headerLine, ...dataLines].join('\r\n');
}

/**
 * Generate native Excel Workbook XML (.xlsx compatible) in pure browser memory
 */
export function generateExcelXmlBlob(headers: string[], rows: string[][], sheetName = 'Sheet1'): Blob {
  const escapeXml = (unsafe: string) => {
    return String(unsafe || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&apos;');
  };

  let xml = `<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Segoe UI" x:Family="Swiss" ss:Size="10" ss:Color="#18181B"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="Header">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D4D4D8"/>
   </Borders>
   <Font ss:FontName="Segoe UI" x:Family="Swiss" ss:Size="10.5" ss:Color="#09090B" ss:Bold="1"/>
   <Interior ss:Color="#F4F4F5" ss:Pattern="Solid"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="${escapeXml(sheetName)}">
  <Table ss:DefaultColumnWidth="110" ss:DefaultRowHeight="20">
   <Row ss:StyleID="Header">
`;

  headers.forEach(h => {
    xml += `    <Cell><Data ss:Type="String">${escapeXml(h)}</Data></Cell>\n`;
  });

  xml += `   </Row>\n`;

  rows.forEach(r => {
    xml += `   <Row>\n`;
    r.forEach(cell => {
      const isNum = !isNaN(Number(cell)) && cell.trim() !== '';
      const type = isNum ? 'Number' : 'String';
      xml += `    <Cell><Data ss:Type="${type}">${escapeXml(cell)}</Data></Cell>\n`;
    });
    xml += `   </Row>\n`;
  });

  xml += `  </Table>
 </Worksheet>
</Workbook>`;

  return new Blob([xml], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
}

/**
 * Trigger download of any text or binary blob
 */
export function triggerBrowserDownload(blob: Blob, filename: string) {
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

/**
 * Deduplicate rows based on selected key columns
 */
export function deduplicateSheetRows(
  headers: string[],
  rows: string[][],
  keyColIndices: number[],
  caseSensitive = false
): {
  uniqueRows: string[][];
  removedCount: number;
  duplicateIndices: number[];
} {
  const seenKeys = new Set<string>();
  const uniqueRows: string[][] = [];
  const duplicateIndices: number[] = [];

  rows.forEach((row, idx) => {
    const key = keyColIndices
      .map(colIdx => {
        const val = row[colIdx] || '';
        return caseSensitive ? val.trim() : val.trim().toLowerCase();
      })
      .join('___CORA_KEY___');

    if (seenKeys.has(key)) {
      duplicateIndices.push(idx);
    } else {
      seenKeys.add(key);
      uniqueRows.push(row);
    }
  });

  return {
    uniqueRows,
    removedCount: duplicateIndices.length,
    duplicateIndices,
  };
}

/**
 * Clean & standardize sheet cells
 */
export function cleanSheetCells(
  headers: string[],
  rows: string[][],
  options: {
    trimWhitespace?: boolean;
    standardizePhoneNumbers?: boolean;
    formatDatesToIso?: boolean;
    capitalizeNames?: boolean;
    removeEmptyRows?: boolean;
  }
): {
  cleanedRows: string[][];
  changesCount: number;
} {
  const {
    trimWhitespace = true,
    standardizePhoneNumbers = false,
    formatDatesToIso = false,
    capitalizeNames = false,
    removeEmptyRows = true,
  } = options;

  let changesCount = 0;
  let workingRows = rows;

  if (removeEmptyRows) {
    const beforeCount = workingRows.length;
    workingRows = workingRows.filter(r => r.some(cell => cell.trim().length > 0));
    changesCount += beforeCount - workingRows.length;
  }

  const cleanedRows = workingRows.map(row => {
    return row.map(cell => {
      let val = cell;
      const initial = val;

      if (trimWhitespace) {
        val = val.replace(/\s+/g, ' ').trim();
      }

      if (standardizePhoneNumbers) {
        // Normalize 10-digit Indian phones or global numbers
        const digits = val.replace(/\D/g, '');
        if (digits.length === 10) {
          val = `+91 ${digits.slice(0, 5)} ${digits.slice(5)}`;
        } else if (digits.length === 12 && digits.startsWith('91')) {
          val = `+91 ${digits.slice(2, 7)} ${digits.slice(7)}`;
        }
      }

      if (formatDatesToIso) {
        // DD/MM/YYYY or DD-MM-YYYY to YYYY-MM-DD
        const dmyMatch = val.match(/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/);
        if (dmyMatch) {
          const day = dmyMatch[1].padStart(2, '0');
          const month = dmyMatch[2].padStart(2, '0');
          const year = dmyMatch[3];
          val = `${year}-${month}-${day}`;
        }
      }

      if (capitalizeNames) {
        // Title Case Words
        if (val.length > 2 && !val.includes('@') && isNaN(Number(val))) {
          val = val
            .toLowerCase()
            .split(' ')
            .map(w => w.charAt(0).toUpperCase() + w.slice(1))
            .join(' ');
        }
      }

      if (val !== initial) {
        changesCount++;
      }

      return val;
    });
  });

  return { cleanedRows, changesCount };
}

/**
 * Convert Rows to JSON Array or Key-Value Record
 */
export function convertSheetToJson(
  headers: string[],
  rows: string[][],
  mode: 'arrayOfObjects' | 'arrayOfArrays' | 'keyedObject' = 'arrayOfObjects',
  keyColIndex = 0
): string {
  if (mode === 'arrayOfArrays') {
    return JSON.stringify([headers, ...rows], null, 2);
  }

  if (mode === 'arrayOfObjects') {
    const objects = rows.map(row => {
      const obj: Record<string, string | number> = {};
      headers.forEach((h, idx) => {
        const val = row[idx] || '';
        const num = Number(val);
        obj[h] = !isNaN(num) && val.trim() !== '' ? num : val;
      });
      return obj;
    });
    return JSON.stringify(objects, null, 2);
  }

  // Keyed Object
  const dict: Record<string, Record<string, string | number>> = {};
  rows.forEach(row => {
    const key = row[keyColIndex] || `Row_${Math.random().toString(36).substring(7)}`;
    const obj: Record<string, string | number> = {};
    headers.forEach((h, idx) => {
      if (idx !== keyColIndex) {
        const val = row[idx] || '';
        const num = Number(val);
        obj[h] = !isNaN(num) && val.trim() !== '' ? num : val;
      }
    });
    dict[key] = obj;
  });

  return JSON.stringify(dict, null, 2);
}

export interface CsvFileInput {
  name: string;
  headers: string[];
  rows: string[][];
}

export interface MergedCsvResult {
  headers: string[];
  rows: string[][];
  totalFiles: number;
  totalRows: number;
  fileBreakdowns: Array<{ name: string; rowCount: number }>;
}

/**
 * Merge multiple CSV files, aligning column headers and consolidating rows
 */
export function mergeCsvFiles(files: CsvFileInput[]): MergedCsvResult {
  if (!files || files.length === 0) {
    return { headers: [], rows: [], totalFiles: 0, totalRows: 0, fileBreakdowns: [] };
  }

  // Build unified header list preserving order of first appearance
  const unifiedHeaders: string[] = [];
  const headerKeyMap = new Map<string, string>(); // lowercase -> canonical name

  files.forEach(file => {
    file.headers.forEach(h => {
      const trimmed = h.trim();
      if (!trimmed) return;
      const lower = trimmed.toLowerCase();
      if (!headerKeyMap.has(lower)) {
        headerKeyMap.set(lower, trimmed);
        unifiedHeaders.push(trimmed);
      }
    });
  });

  const mergedRows: string[][] = [];
  const fileBreakdowns: Array<{ name: string; rowCount: number }> = [];

  files.forEach(file => {
    fileBreakdowns.push({ name: file.name, rowCount: file.rows.length });
    const colIndexMap: number[] = file.headers.map(h => {
      const canonical = headerKeyMap.get(h.trim().toLowerCase());
      return canonical ? unifiedHeaders.indexOf(canonical) : -1;
    });

    file.rows.forEach(row => {
      const newRow = new Array(unifiedHeaders.length).fill('');
      row.forEach((val, idx) => {
        const unifiedIdx = colIndexMap[idx];
        if (unifiedIdx >= 0) {
          newRow[unifiedIdx] = val;
        }
      });
      mergedRows.push(newRow);
    });
  });

  return {
    headers: unifiedHeaders,
    rows: mergedRows,
    totalFiles: files.length,
    totalRows: mergedRows.length,
    fileBreakdowns,
  };
}

export interface SplitCsvChunk {
  filename: string;
  label: string;
  headers: string[];
  rows: string[][];
  rowCount: number;
}

/**
 * Split rows by row count (e.g. 500 rows per file)
 */
export function splitCsvByRowCount(
  headers: string[],
  rows: string[][],
  rowsPerChunk: number,
  baseFilename = 'split_data'
): SplitCsvChunk[] {
  if (rowsPerChunk <= 0 || rows.length === 0) return [];
  const cleanBase = baseFilename.replace(/\.[^/.]+$/, '');
  const chunks: SplitCsvChunk[] = [];
  const totalChunks = Math.ceil(rows.length / rowsPerChunk);

  for (let i = 0; i < totalChunks; i++) {
    const chunkRows = rows.slice(i * rowsPerChunk, (i + 1) * rowsPerChunk);
    const chunkNumber = i + 1;
    const padDigits = totalChunks >= 100 ? 3 : totalChunks >= 10 ? 2 : 1;
    const padded = String(chunkNumber).padStart(padDigits, '0');
    chunks.push({
      filename: `${cleanBase}_part_${padded}.csv`,
      label: `Part ${chunkNumber} (${chunkRows.length.toLocaleString()} rows)`,
      headers,
      rows: chunkRows,
      rowCount: chunkRows.length,
    });
  }
  return chunks;
}

/**
 * Split rows by unique values in a chosen column (e.g. City, Vendor, Status)
 */
export function splitCsvByColumnValue(
  headers: string[],
  rows: string[][],
  colIndex: number,
  baseFilename = 'split_data'
): SplitCsvChunk[] {
  if (colIndex < 0 || colIndex >= headers.length || rows.length === 0) return [];
  const cleanBase = baseFilename.replace(/\.[^/.]+$/, '');
  const groups = new Map<string, string[][]>();

  rows.forEach(row => {
    const rawVal = row[colIndex] || '';
    const key = rawVal.trim() || 'unspecified';
    if (!groups.has(key)) {
      groups.set(key, []);
    }
    groups.get(key)!.push(row);
  });

  const chunks: SplitCsvChunk[] = [];
  const colName = headers[colIndex].replace(/[^a-zA-Z0-9_-]/g, '_').toLowerCase();

  groups.forEach((groupRows, key) => {
    const safeKey = key.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 30);
    chunks.push({
      filename: `${cleanBase}_${colName}_${safeKey}.csv`,
      label: `${headers[colIndex]}: "${key}" (${groupRows.length.toLocaleString()} rows)`,
      headers,
      rows: groupRows,
      rowCount: groupRows.length,
    });
  });

  return chunks;
}
