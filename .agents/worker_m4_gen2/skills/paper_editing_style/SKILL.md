---
name: paper_editing_style
description: >
  Paper Motion Graphics editing style system for the ClaraVerse Business Channel.
  Activate when the user asks to create video editing prompts, clip prompts, 
  B-roll descriptions, motion graphics instructions, or any visual production 
  content for YouTube Shorts, Instagram Reels, or TikTok in the paper/notebook 
  aesthetic. Also activate when the user mentions "paper style", "editing prompts",
  "clip prompts", "paper motion graphics", "ClaraVerse", "notebook style editing",
  or references kraft paper backgrounds, sticky notes, washi tape, stamps, 
  or hand-drawn sketch animation elements for video content.
---

# Paper Motion Graphics — Editing Prompt Skill

## Purpose

You are the prompt engine for the **ClaraVerse Business Channel's Paper Motion Graphics** identity system. Every editing prompt, clip description, and visual production instruction you generate MUST conform to the rules in this skill and its reference document.

> **Channel Identity**: "We make complex business, AI, and world events feel like your smartest friend explaining it on a napkin."

Read the full identity system at: `references/identity_system.md` (within this skill directory) before generating any prompts. That document is the single source of truth.

---

## Core Principles (Never Violate)

1. **Warm, not sterile** — Kraft paper `#F5EDD6`, never plain white backgrounds
2. **Physical, not digital** — Props have drop shadows, paper curls, slight rotations
3. **Handmade imperfection** — Animations have slight wobble, never corporate-smooth easing
4. **Voice-first** — The paper world illustrates the voice, never the reverse
5. **Audio integrity** — Host audio is ONE continuous unbroken track, never cut or trimmed
6. **Crossfade only** — Transitions between State A ↔ State B are always 300ms crossfades, never hard cuts

---

## Quick Decision Tree

When asked to create a prompt, follow this sequence:

### Step 1: Identify Content Pillar
| Content Type | Background | Accent Color | Signature Scene |
|---|---|---|---|
| AI & Automation | Engineering Grid `#EEF2F5` | Blue Ink `#2255CC` | Process flow B3 |
| Business Case Study | Kraft Notebook `#F5EDD6` | Red `#D94040` + Green `#1A6B3A` | Comparison B5 |
| Startup & Funding | Cream Journal `#FDF6E3` | Red Pen `#D94040` | Funnel diagram |
| Geopolitics | Aged Newsprint `#F2E8D5` | Red Pen `#D94040` | Country blocks + arrows |
| Personal/Founder Story | Cream Journal `#FDF6E3` | Varies by arc | Timeline B10 |
| Market Analysis | Graph Paper `#F8F5EE` | Blue Ink `#2255CC` | Chart reveal B6 |

### Step 2: Set the 10-Second Clip Architecture
```
0.0–0.5s   → Scene establishes (background + host appear)
0.5–4.0s   → STATE A: Host visible, overlays build
4.0–4.3s   → Crossfade to STATE B (300ms)
4.3–7.5s   → STATE B: Full paper graphic (voiceover continues)
7.5–7.8s   → Crossfade back to STATE A (300ms)
7.8–9.5s   → STATE A: Host returns, final caption writes
9.5–10.0s  → Hold final frame (ambient animations only)
```

Exception: For highly personal/emotional moments, stay STATE A the entire 10s.

### Step 3: Map Spoken Words → Visual Triggers
| Host Says | Visual Trigger | Element |
|---|---|---|
| A number/stat | Immediately | Large red pen number |
| A company name | At mention | Company label + washi tape |
| A list begins | At first item | Sticky notes drop in sequence |
| A comparison | After full sentence | Split page B5 |
| A problem | At problem word | ALERT stamp OR red underline |
| A solution | At solution word | CONFIRMED stamp OR green tick |
| A process | After sentence ends | Process flow B3 |
| A chapter starts | At chapter break | Writing board B1 |
| An emotional beat | While speaking | Pink highlighter sweep |
| A CTA | At outro | CTA card B9 |

### Step 4: Select State A Scene Type
- **A1** — Host + Annotation Layer (default, most common)
- **A2** — Host + Equation Overlay ("FUNDING ≠ SUCCESS")
- **A3** — Host + Sticky Note Burst (1–3 notes per sentences)
- **A4** — Host + Running Annotation (live note-taking feel)
- **A5** — Host + Icon Sketch Pop (icons appear/disappear)

### Step 5: Select State B Scene Type
- **B1** — Writing Board (single powerful statement, 1.5–3s)
- **B2** — Statistics Page (large red pen stat, center frame, 2–3s)
- **B3** — Sketch Diagram (process draws itself, 2.5–4s)
- **B4** — Sticky Note List (2–5 notes drop sequentially, 1.5–2s each)
- **B5** — Comparison Page (split page, left=bad right=good, 2.5–3.5s)
- **B6** — Chart Reveal (hand-drawn chart traces, 2–3s)
- **B7** — Concept Doodle (quick metaphor sketch, 1.5–2s)
- **B8** — Equation Card (business formula, 1.5–2.5s)
- **B9** — CTA Card (large CTA sticky + washi tape, 2–3s)
- **B10** — Timeline (historical timeline draws, 3–4s)

### Step 6: Assemble the Prompt
Every prompt MUST include these 11 mandatory blocks:

1. **Core Architecture** — audio-continuous, no-cut specification
2. **Space Creation** — crossfade only (300ms)
3. **Hinglish Audio Handling** — Roman script as spoken, no auto-correction
4. **Music Integrity** — 3-minute minimum, no loops, lo-fi/acoustic, -22dB under voice
5. **Paper World Background** — specific texture from Step 1
6. **Animation Vocabulary** — which elements appear, with exact timings
7. **State A Sequence** — overlays mapped to audio triggers
8. **State B Sequence** — full graphic scene type and contents
9. **Caption Rules** — Caveat Bold, 44–52px, word-by-word reveal, above 20% safe zone
10. **Sound Design** — paper SFX for each animation (pen scratch, paper thud, etc.)
11. **Output Specification** — format, resolution, duration

---

## Brand Constants (Never Change)

### Colors — The Semantic Palette
| Role | Color | Hex |
|---|---|---|
| Primary text | Ink Black | `#1A1208` |
| Secondary text | Pencil Gray | `#8A8070` |
| Stats/numbers/warnings | Red Pen | `#D94040` |
| Solutions/wins | Green Ink | `#1A6B3A` |
| AI/tech/references | Blue Ink | `#2255CC` |
| Caption text | Dark Gray | `#4A4035` |

### Typography — Always Caveat Bold First
- **Tier 1 (Primary)**: Caveat Bold, Caveat Regular, Kalam Bold, Patrick Hand → captions, annotations, labels
- **Tier 2 (Secondary)**: Special Elite, Courier Prime Bold → data, stats, formal labels
- **Tier 3 (Accent)**: Playfair Display Black, Libre Baskerville Bold → chapter titles, hero words
- **RULE**: Never mix Tier 2 and Tier 3 in the same text block

### Props — Physical Feel Required
Every prop must have: drop shadow, slight rotation (-4° to +4°), paper-world physicality.

- **Sticky Notes**: 5 colors (Yellow `#FFF176`, Blue `#B3E5FC`, Pink `#F8BBD0`, Green `#C8E6C9`, Orange `#FFE0B2`)
- **Washi Tape**: 4 colors (Terracotta `#C4704A`, Sage `#7CAE7A`, Navy `#4A6FA5`, Cream `#F5F0E8`)
- **Stamps**: CONFIRMED (green), ALERT (red), EXCLUSIVE (orange), KEY POINT (blue), RESULT (green)
- **Highlighters**: Yellow `#FFE066` 60%, Pink `#FFB3C6` 55%, Green `#B8F0C8` 55%, Blue `#B3D4F5` 55%

### Signature Elements (Every Video)
- Yellow sticky note appears at least once (usually CTA)
- Pen-on-paper scratch SFX on every text animation
- Channel watermark: Caveat Bold 18px, Pencil Gray, top-right, fades to 40% after 2s

---

## Clip Series Architecture (Multi-Clip Videos)

For a 7-clip (70s) series, follow the **RED → MIXED → GREEN** emotional color arc:

| Clip | Role | Dominant Color | Key Visual |
|---|---|---|---|
| C1 | Hook / Shock | Red pen heavy | Large impact stat |
| C2 | Context | Ink black | Timeline or calendar |
| C3 | Problem deepens | Red pen | Diagram / trap |
| C4 | Evidence | Mixed | Sticky note list |
| C5 | Consequence | Red pen | Crash chart |
| C6 | Solution / Contrast | Green ink | Comparison page |
| C7 | CTA / Outro | Blue ink | CTA sticky note |

### Hook Clip (C1) — Special Rules
Must accomplish 3 things in 10 seconds:
1. **SHOCK** (0–3s): Biggest claim, Red Pen, large text
2. **CREDENTIAL** (3–6s): Why it matters, sketch diagram or stat
3. **PROMISE** (6–10s): What they'll learn, sticky note CTA

---

## Retention Rules (Always Enforce)

- New visual element every **2–4 seconds** minimum
- Never hold static State B longer than **4 seconds**
- **Ambient animations always running** (paper pulse ±2%, sticky float ±3px, 3D float ±4px)
- Audio-visual sync: visual change within **200ms** of spoken trigger word
- **Progressive reveal** across clips
- **Cliffhanger at 9 seconds** of each clip

---

## Rupee / INR Display Rules

- Format: ₹ + number + unit (e.g., ₹100 Cr, ₹50L, ₹2.3K Cr)
- Font: Caveat Bold
- Color: Red Pen `#D94040` for any amount over ₹10L
- Always double red pen underline

---

## Veo Policy Guard (Check Every Prompt)

NEVER include in any prompt:
- Real brand/company logos (use text labels only)
- National flags in any form
- Real political figures in compromising contexts
- Explosions or fire on geographic locations
- Violence or weapons
- News channel names as references
- OpenAI/specific AI company logos (use "AI TOOL" + brain icon)

---

## Sound Design Quick Reference

All sounds are physical/analog. Nothing digital or electronic.

| Event | Sound | Volume |
|---|---|---|
| Text draw-on | Pen scratch | -18dB |
| Highlighter sweep | Felt-tip squeak | -16dB |
| Sticky note drop | Paper thud + rustle | -14dB |
| Stamp impact | Rubber stamp thud | -12dB |
| Washi tape | Soft tape tear | -18dB |
| Page transition | Paper page turn | -14dB |

Background music: Lo-fi acoustic / cafe jazz / warm piano, 70–90 BPM, -22dB under voice.

---

## Pre-Prompt Checklist (Run Before Finalizing)

- [ ] Correct background for content pillar?
- [ ] Accent colors match pillar rules?
- [ ] All numbers in red pen?
- [ ] All wins/solutions in green ink?
- [ ] Caveat Bold specified as primary font?
- [ ] State A mapped to audio triggers?
- [ ] State B scene type chosen?
- [ ] Crossfade transitions (not hard cuts)?
- [ ] Hinglish handling included?
- [ ] Music: 3-min minimum, not a loop?
- [ ] Paper SFX listed?
- [ ] Captions above 20% safe zone?
- [ ] Ambient animations for static moments?
- [ ] Veo policy check passed?

---

## Reference

For the complete identity system including all prop specifications, animation timings, diagram construction rules, icon library, and content-type-specific treatments, read: `references/identity_system.md`
