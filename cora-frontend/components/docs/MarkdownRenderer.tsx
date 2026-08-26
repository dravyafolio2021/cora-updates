'use client';

import React, { useState } from 'react';
import { Check, Copy } from 'lucide-react';

interface MarkdownRendererProps {
  content: string;
}

export function MarkdownRenderer({ content }: MarkdownRendererProps) {
  const [copiedCode, setCopiedCode] = useState<string | null>(null);

  const handleCopy = (code: string) => {
    navigator.clipboard.writeText(code);
    setCopiedCode(code);
    setTimeout(() => setCopiedCode(null), 2000);
  };

  // Helper to parse lines into structured blocks
  const lines = content.trim().split('\n');
  const blocks: React.ReactNode[] = [];

  let i = 0;
  while (i < lines.length) {
    const line = lines[i];

    // 1. Code Block
    if (line.startsWith('```')) {
      const lang = line.slice(3).trim();
      const codeLines: string[] = [];
      i++;
      while (i < lines.length && !lines[i].startsWith('```')) {
        codeLines.push(lines[i]);
        i++;
      }
      const codeText = codeLines.join('\n');
      blocks.push(
        <div key={`code-${i}`} className="my-6 rounded-2xl bg-zinc-950 text-zinc-100 border border-zinc-800 overflow-hidden shadow-sm group">
          <div className="flex items-center justify-between px-4 py-2.5 bg-zinc-900/90 border-b border-zinc-800 text-[11px] font-mono text-zinc-400">
            <span>{lang || 'code'}</span>
            <button
              type="button"
              onClick={() => handleCopy(codeText)}
              className="inline-flex items-center gap-1 text-[11px] hover:text-white transition-colors cursor-pointer"
            >
              {copiedCode === codeText ? (
                <>
                  <Check className="w-3 h-3 text-emerald-400" />
                  <span className="text-emerald-400">Copied</span>
                </>
              ) : (
                <>
                  <Copy className="w-3 h-3 text-zinc-400 group-hover:text-white" />
                  <span>Copy</span>
                </>
              )}
            </button>
          </div>
          <pre className="p-4 overflow-x-auto text-[12px] font-mono leading-relaxed selection:bg-zinc-800 selection:text-white">
            <code>{codeText}</code>
          </pre>
        </div>
      );
      i++;
      continue;
    }

    // 2. Table Block
    if (line.startsWith('|')) {
      const tableLines: string[] = [];
      while (i < lines.length && lines[i].startsWith('|')) {
        tableLines.push(lines[i]);
        i++;
      }

      if (tableLines.length >= 2) {
        const headerRow = tableLines[0].split('|').filter((_, idx, arr) => idx > 0 && idx < arr.length - 1).map((c) => c.trim());
        const bodyRows = tableLines.slice(2).map((row) =>
          row.split('|').filter((_, idx, arr) => idx > 0 && idx < arr.length - 1).map((c) => c.trim())
        );

        blocks.push(
          <div key={`table-${i}`} className="my-6 overflow-x-auto rounded-2xl border border-zinc-200 bg-white shadow-2xs">
            <table className="w-full text-left text-xs border-collapse">
              <thead>
                <tr className="bg-zinc-50/80 border-b border-zinc-200">
                  {headerRow.map((h, hIdx) => (
                    <th key={hIdx} className="px-4 py-3 font-semibold text-zinc-900 tracking-tight">
                      {h}
                    </th>
                  ))}
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-100">
                {bodyRows.map((r, rIdx) => (
                  <tr key={rIdx} className="hover:bg-zinc-50/50 transition-colors">
                    {r.map((c, cIdx) => (
                      <td key={cIdx} className="px-4 py-2.5 text-zinc-600 leading-relaxed">
                        {renderInline(c)}
                      </td>
                    ))}
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        );
      }
      continue;
    }

    // 3. Headings
    if (line.startsWith('# ')) {
      // Skip the first top-level # heading if it's at the very beginning of the content
      if (i === 0 || blocks.length === 0) {
        i++;
        continue;
      }
      blocks.push(
        <h1 key={`h1-${i}`} className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight mt-8 mb-4">
          {line.replace('# ', '')}
        </h1>
      );
      i++;
      continue;
    }

    if (line.startsWith('## ')) {
      const title = line.replace('## ', '');
      const id = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
      blocks.push(
        <h2 id={id} key={`h2-${i}`} className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight mt-10 mb-3 pt-4 border-t border-zinc-100 scroll-mt-24">
          {title}
        </h2>
      );
      i++;
      continue;
    }

    if (line.startsWith('### ')) {
      const title = line.replace('### ', '');
      const id = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
      blocks.push(
        <h3 id={id} key={`h3-${i}`} className="font-display text-base sm:text-lg font-semibold text-zinc-900 tracking-tight mt-6 mb-2 scroll-mt-24">
          {title}
        </h3>
      );
      i++;
      continue;
    }

    // 4. Horizontal Rule
    if (line === '---') {
      blocks.push(<hr key={`hr-${i}`} className="my-8 border-zinc-100" />);
      i++;
      continue;
    }

    // 5. Unordered List
    if (line.startsWith('- ') || line.startsWith('* ')) {
      const listItems: string[] = [];
      while (i < lines.length && (lines[i].startsWith('- ') || lines[i].startsWith('* '))) {
        listItems.push(lines[i].replace(/^[-*]\s+/, ''));
        i++;
      }
      blocks.push(
        <ul key={`ul-${i}`} className="my-4 space-y-2 list-disc list-outside pl-5 text-sm text-zinc-600 leading-relaxed">
          {listItems.map((item, lIdx) => (
            <li key={lIdx}>{renderInline(item)}</li>
          ))}
        </ul>
      );
      continue;
    }

    // 6. Ordered List
    if (/^\d+\.\s+/.test(line)) {
      const listItems: string[] = [];
      while (i < lines.length && /^\d+\.\s+/.test(lines[i])) {
        listItems.push(lines[i].replace(/^\d+\.\s+/, ''));
        i++;
      }
      blocks.push(
        <ol key={`ol-${i}`} className="my-4 space-y-2 list-decimal list-outside pl-5 text-sm text-zinc-600 leading-relaxed">
          {listItems.map((item, lIdx) => (
            <li key={lIdx}>{renderInline(item)}</li>
          ))}
        </ol>
      );
      continue;
    }

    // 7. Standard Paragraph
    if (line.trim() !== '') {
      blocks.push(
        <p key={`p-${i}`} className="my-3 text-sm sm:text-[14.5px] text-zinc-600 leading-[1.7] font-normal">
          {renderInline(line)}
        </p>
      );
    }

    i++;
  }

  return <div className="docs-prose space-y-1">{blocks}</div>;
}

// Inline parser for bold, italics, inline code, and links
function renderInline(text: string): React.ReactNode {
  // Regex to split by bold (**text**), code (`code`), or links ([text](url))
  const parts = text.split(/(\*\*.*?\*\*|`.*?`|\[.*?\]\(.*?\))/g);

  return parts.map((part, idx) => {
    if (part.startsWith('**') && part.endsWith('**')) {
      return (
        <strong key={idx} className="font-semibold text-zinc-950">
          {part.slice(2, -2)}
        </strong>
      );
    }
    if (part.startsWith('`') && part.endsWith('`')) {
      return (
        <code key={idx} className="px-1.5 py-0.5 rounded-md bg-zinc-100 text-zinc-900 font-mono text-[12px] border border-zinc-200/70">
          {part.slice(1, -1)}
        </code>
      );
    }
    if (part.startsWith('[') && part.includes('](')) {
      const match = part.match(/\[(.*?)\]\((.*?)\)/);
      if (match) {
        return (
          <a key={idx} href={match[2]} className="text-zinc-950 underline decoration-zinc-300 underline-offset-4 hover:decoration-zinc-950 font-medium transition-colors">
            {match[1]}
          </a>
        );
      }
    }
    return part;
  });
}
