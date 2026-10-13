<!-- BEGIN:nextjs-agent-rules -->

# This is NOT the Next.js you know

This version has breaking changes — APIs, conventions, and file structure may all differ from your training data. Read the relevant guide in `node_modules/next/dist/docs/` (resolved from this file's directory; in monorepos the `next` package may not be visible from the repo root) before writing any code. Heed deprecation notices.

This block is written and re-added by `next dev` — verify at `node_modules/next/dist/server/lib/generate-agent-files.js`. Removing it from a diff only re-creates the uncommitted change; committing it with your work keeps the tree clean.

<!-- END:nextjs-agent-rules -->

# Required Cora design contract

Before any UI change, read `lib/design-system.ts` and choose its marketing or workspace profile. The public contract is `/design-system/manifest.json`; the readable version is `/design-system/llms.txt`. Reuse `components/design-system/primitives.tsx` and the listed feature shells. Use semantic `--cora-*` tokens, preserve keyboard focus and mobile bottom-sheet behavior, and document intentional exceptions in the canonical source.

Marketing uses General Sans; product workspace uses Inter. Legacy `lib/cora-tokens.ts` and the parent repository's HTML/token snapshots are historical references, not the current source of truth. New patterns must update implementation, states, token definitions and the public contract together.

Run `npm run check:design-system` and the relevant build/feature checks. For new feature code, pass its files through `npm run check:design-system -- --files <paths>` to check style drift in the changed scope. Inspect mobile, desktop and keyboard behavior. The prebuild guard checks the canonical reference; it does not certify all legacy screens. More detailed guidance lives at `../docs/design-system-agent-guide.md` when working in the full platform checkout.
