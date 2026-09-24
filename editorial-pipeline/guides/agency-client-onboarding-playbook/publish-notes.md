# Publish Notes - Coding Agent

Use this folder as the editorial source of truth for the next publish pass.

## 1. Replace outdated guide copy

Use `guide-master.md` to update the existing guide content.

Remove / do not reintroduce:
- "48 hours" guarantee language;
- "verified operating system" claims;
- "6 chapters" if final guide renders 8;
- old placeholder Notion/ZIP copy;
- unsupported research claims.

Final guide:
- 8 chapters;
- public and fully readable;
- agency-first positioning;
- contextual Cora CTA only.

## 2. Lead magnet must be PDF-only

Use `lead-magnet-pdf.md` as the source content.

Final asset:
- file type: PDF;
- single downloadable file;
- mobile-friendly layout;
- no ZIP;
- no Notion;
- no JSON or Markdown shown to end users.

Update `DownloadableAsset` for Guide 01:

- `fileType: 'pdf'`
- CTA: `Download PDF`
- description: `10 practical templates in one mobile-friendly PDF.`

The API/download route should serve the real PDF with:

- `Content-Type: application/pdf`
- `Content-Disposition: attachment; filename="cora-agency-client-onboarding-pack.pdf"`

Keep Beehiiv email capture before unlock.

## 3. Replace cover / OG direction

Do not reuse the old monochrome placeholder cover.

Use `visual-directions.md` as the final creative brief.

Target assets:

- `/public/images/guides/agency-client-onboarding-playbook-cover.webp`
- `/public/images/guides/agency-client-onboarding-playbook-og.webp`
- `/public/images/guides/agency-client-onboarding-playbook-download.webp`

Final cover and OG must not contain references to ZIP, Notion, 6 chapters, or 48-hour promises.

## 4. In-guide graphics

Implement the supplied SVGs in `graphics/` or recreate them within the existing responsive infographic renderer.

Minimum visuals:

1. Sales-to-delivery handoff
2. Scope change decision tree
3. Access hierarchy / least privilege
4. Kickoff timeline
5. 30-day roadmap

Graphics should be semantic, responsive, and readable on mobile.

## 5. Sources

Use sources from `research-notes.md` where a claim actually depends on them.

Do not add citations simply for decoration.

## 6. CTA hierarchy

Primary guide conversion:

**Download Agency Client Onboarding Pack (PDF)** -> Beehiiv capture -> PDF unlock.

Secondary:

**See Cora for Agencies** -> `/agency-management-software-india/`

Do not make the guide feel like a product sales page.

## 7. Final QA

Before publishing:

- all 8 chapters render;
- no outline-only experience;
- chapter anchors work;
- mobile typography and tables are readable;
- cover and OG metadata use final visuals;
- Beehiiv source attribution remains intact;
- PDF downloads on mobile Safari and Chrome;
- no Notion/ZIP/JSON wording remains;
- sitemap and canonical are correct;
- status is `published` only after final live check.
