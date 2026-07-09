# BRIEFING — 2026-07-08T09:52:03Z

## Mission
Execute follow-up requirements to add advanced real estate listing management, 3rd-party portal syncing, and a comprehensive lead capture pipeline with mobile-first Studio Minimalist UI/UX.

## 🔒 My Identity
- Archetype: Project Orchestrator
- Roles: orchestrator, user_liaison, human_reporter, successor
- Working directory: /Users/shrutian/Desktop/cora/.agents/orchestrator_gen8
- Original parent: parent
- Original parent conversation ID: dbd5d168-2932-4900-96ce-1e570c4faf23

## 🔒 My Workflow
- **Pattern**: Project Pattern
- **Scope document**: /Users/shrutian/Desktop/cora/.agents/orchestrator_gen8/PROJECT.md
1. **Decompose**: Decompose the task into milestones for Lead Ingestion, Portal Syncing, AI/SEO, and Mobile UI integration.
2. **Dispatch & Execute** (pick ONE):
   - **Direct (iteration loop)**: Spawn Worker, Reviewer, Challenger, and Forensic Auditor subagents.
   - **Delegate (sub-orchestrator)**: N/A (We'll run direct iteration loops for milestones or delegate simple tasks).
3. **On failure** (in this order):
   - Retry: nudge stuck agent or re-send task
   - Replace: spawn fresh agent with partial progress
   - Skip: proceed without (only if non-critical)
   - Redistribute: split stuck agent's remaining work
   - Redesign: re-partition decomposition
   - Escalate: report to parent (sub-orchestrators only, last resort)
4. **Succession**: Self-succeed at 16 spawns, write handoff.md, spawn successor.
- **Work items**:
  1. Decompose & Plan [pending]
  2. Implement R1: Lead Capture Pipeline [pending]
  3. Implement R2: 3rd-Party Listing Sync [pending]
  4. Implement R3: AI SEO Optimization [pending]
  5. Implement R4: Mobile UI Polish & Integration [pending]
  6. E2E Verification & Audit [pending]
- **Current phase**: 1
- **Current focus**: Decompose & Plan

## 🔒 Key Constraints
- NEVER write, modify, or create source code files directly.
- NEVER run build/test commands yourself.
- All dialogue and UI must follow "Cora Platform - Global Agent Rules" (no browser defaults, monochromatic toasts, right-sliding drawers).
- Never reuse a subagent after it has delivered its handoff.

## Current Parent
- Conversation ID: dbd5d168-2932-4900-96ce-1e570c4faf23
- Updated: 2026-07-08T09:52:03Z

## Key Decisions Made
- Use WordPress REST API for the webhook ingestion endpoint.
- Use a dedicated frontend shortcode [cora_lead_form] for the custom form module.
- Add fields for 3rd-party listing URLs, AI SEO meta fields to the existing property inventory.

## Team Roster
| Agent | Type | Work Item | Status | Conv ID |
|-------|------|-----------|--------|---------|
| worker_1 | teamwork_preview_worker | Implement advanced real estate features (R1-R4) | completed | 1b3dc8f8-342f-4186-871b-0d8e77c04d89 |
| reviewer_1 | teamwork_preview_reviewer | Review correctness & UI style (R1-R4) | in-progress | c7b0ca6e-193a-4c31-bc80-4d035c919f52 |
| reviewer_2 | teamwork_preview_reviewer | Review correctness & UI style (R1-R4) | in-progress | d3ed3463-c06e-4d8e-8860-99c09a37a984 |
| challenger_1 | teamwork_preview_challenger | Empirically verify AJAX tests & UI | in-progress | 1d80d508-dab3-45db-be5f-b25dd59dc027 |
| challenger_2 | teamwork_preview_challenger | Empirically verify AJAX tests & UI | in-progress | 44ada88a-9218-4621-b598-ffed6b0fcb8d |
| auditor_1 | teamwork_preview_auditor | Forensic audit of source code and tests | in-progress | 84699f7b-7281-4cd7-a7ee-5b6ce9f9a401 |

## Succession Status
- Succession required: no
- Spawn count: 6 / 16
- Pending subagents: c7b0ca6e-193a-4c31-bc80-4d035c919f52, d3ed3463-c06e-4d8e-8860-99c09a37a984, 1d80d508-dab3-45db-be5f-b25dd59dc027, 44ada88a-9218-4621-b598-ffed6b0fcb8d, 84699f7b-7281-4cd7-a7ee-5b6ce9f9a401
- Predecessor: none
- Successor: not yet spawned

## Active Timers
- Heartbeat cron: 1103075c-16d4-42d6-98e1-e602e972325a/task-89
- Safety timer: none
- On succession: kill all timers before spawning successor
- On context truncation: run manage_task(Action="list") — re-create if missing

## Artifact Index
- /Users/shrutian/Desktop/cora/.agents/orchestrator_gen8/ORIGINAL_REQUEST.md — Original User Request
- /Users/shrutian/Desktop/cora/.agents/orchestrator_gen8/progress.md — Liveness Heartbeat
- /Users/shrutian/Desktop/cora/.agents/orchestrator_gen8/PROJECT.md — Global Project Plan
