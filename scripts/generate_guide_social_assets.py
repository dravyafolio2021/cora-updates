#!/usr/bin/env python3
"""
Generate crisp, high-resolution editorial cover & Open Graph preview images
for Cora Guides & Playbooks.
Follows Claude warm cream aesthetic (#FBFaf7), subtle zinc borders, modern typography, and clean badges.
"""

import os
from PIL import Image, ImageDraw, ImageFont

OUTPUT_DIR = "/Users/shrutian/Desktop/cora/cora-frontend/public/images/guides"
os.makedirs(OUTPUT_DIR, exist_ok=True)

GUIDES = [
    {
        "slug": "agency-client-onboarding-playbook",
        "category": "AGENCY OPERATIONS",
        "quality": "OPERATIONAL PLAYBOOK",
        "title": "The Agency Client Onboarding Playbook",
        "subtitle": "From Verbal Yes to Active Retainer in 48 Hours • 6 Chapters + SOP Pack",
        "read_time": "18 MIN READ",
        "chapters": "6 CHAPTERS",
        "author": "Dravya Bansal",
    },
]

def get_font(size, bold=False):
    font_paths = [
        "/System/Library/Fonts/SFPro-Bold.otf" if bold else "/System/Library/Fonts/SFPro-Regular.otf",
        "/System/Library/Fonts/SFProText-Bold.otf" if bold else "/System/Library/Fonts/SFProText-Regular.otf",
        "/System/Library/Fonts/HelveticaNeue.ttc",
        "/System/Library/Fonts/Supplemental/Arial Bold.ttf" if bold else "/System/Library/Fonts/Supplemental/Arial.ttf",
        "/System/Library/Fonts/Supplemental/Helvetica.ttc",
    ]
    for p in font_paths:
        if os.path.exists(p):
            try:
                return ImageFont.truetype(p, size)
            except Exception:
                continue
    return ImageFont.load_default()

def wrap_text(text, font, max_width, draw):
    words = text.split()
    lines = []
    current_line = []
    for word in words:
        current_line.append(word)
        test_line = ' '.join(current_line)
        bbox = draw.textbbox((0, 0), test_line, font=font)
        w = bbox[2] - bbox[0]
        if w > max_width and len(current_line) > 1:
            current_line.pop()
            lines.append(' '.join(current_line))
            current_line = [word]
    if current_line:
        lines.append(' '.join(current_line))
    return lines

def generate_guide_og_image(guide):
    width, height = 1200, 630
    img = Image.new("RGB", (width, height), color="#FBFaf7")
    draw = ImageDraw.Draw(img)

    # Outer border
    draw.rectangle([(24, 24), (width - 24, height - 24)], outline="#E4E4E7", width=2)
    # Inner panel
    draw.rectangle([(48, 48), (width - 48, height - 48)], fill="#FFFFFF", outline="#E4E4E7", width=1)

    font_brand = get_font(22, bold=True)
    font_badge = get_font(13, bold=True)
    font_title = get_font(42, bold=True)
    font_sub = get_font(20, bold=False)
    font_footer = get_font(15, bold=False)
    font_footer_bold = get_font(15, bold=True)

    # Header
    draw.text((80, 80), "CORA", fill="#09090B", font=font_brand)
    draw.text((155, 82), "•  GUIDES & PLAYBOOKS", fill="#71717A", font=get_font(16, bold=False))

    # Badge Pill
    badge_text = f"{guide['quality']} • {guide['category']}"
    bbox_badge = draw.textbbox((0, 0), badge_text, font=font_badge)
    badge_w = bbox_badge[2] - bbox_badge[0]
    badge_x = width - 80 - badge_w - 24
    draw.rounded_rectangle([(badge_x, 74), (width - 80, 108)], radius=17, fill="#F4F4F5", outline="#E4E4E7", width=1)
    draw.text((badge_x + 12, 83), badge_text, fill="#18181B", font=font_badge)

    draw.line([(80, 130), (width - 80, 130)], fill="#F4F4F5", width=2)

    # Title
    title_lines = wrap_text(guide['title'], font_title, 1020, draw)
    curr_y = 175
    for line in title_lines[:3]:
        draw.text((80, curr_y), line, fill="#09090B", font=font_title)
        curr_y += 54

    curr_y += 16
    sub_lines = wrap_text(guide['subtitle'], font_sub, 1000, draw)
    for line in sub_lines[:2]:
        draw.text((80, curr_y), line, fill="#52525B", font=font_sub)
        curr_y += 30

    footer_y = height - 105
    draw.line([(80, footer_y - 25), (width - 80, footer_y - 25)], fill="#F4F4F5", width=2)

    draw.text((80, footer_y), "Author: ", fill="#71717A", font=font_footer)
    bbox_pre = draw.textbbox((0, 0), "Author: ", font=font_footer)
    draw.text((80 + bbox_pre[2], footer_y), guide['author'], fill="#09090B", font=font_footer_bold)

    right_text = f"{guide['chapters']}  •  {guide['read_time']}  •  heycora.in/guides"
    bbox_r = draw.textbbox((0, 0), right_text, font=font_footer)
    draw.text((width - 80 - (bbox_r[2] - bbox_r[0]), footer_y), right_text, fill="#71717A", font=font_footer)

    out_path = os.path.join(OUTPUT_DIR, f"{guide['slug']}-og.webp")
    img.save(out_path, "WEBP", quality=92)
    print(f"Generated Guide OG: {out_path}")

def generate_guide_cover_image(guide):
    width, height = 1600, 900
    img = Image.new("RGB", (width, height), color="#FBFaf7")
    draw = ImageDraw.Draw(img)

    # Outer border
    draw.rectangle([(32, 32), (width - 32, height - 32)], outline="#E4E4E7", width=2)
    # Inner panel
    draw.rectangle([(64, 64), (width - 64, height - 64)], fill="#FFFFFF", outline="#E4E4E7", width=1)

    font_brand = get_font(28, bold=True)
    font_badge = get_font(18, bold=True)
    font_title = get_font(56, bold=True)
    font_sub = get_font(26, bold=False)
    font_footer = get_font(20, bold=False)
    font_footer_bold = get_font(20, bold=True)

    draw.text((110, 110), "CORA", fill="#09090B", font=font_brand)
    draw.text((210, 114), "•  CHAPTERED OPERATING PLAYBOOK", fill="#71717A", font=get_font(22, bold=False))

    badge_text = f"{guide['quality']} • {guide['category']}"
    bbox_badge = draw.textbbox((0, 0), badge_text, font=font_badge)
    badge_w = bbox_badge[2] - bbox_badge[0]
    badge_x = width - 110 - badge_w - 32
    draw.rounded_rectangle([(badge_x, 102), (width - 110, 148)], radius=23, fill="#F4F4F5", outline="#E4E4E7", width=1)
    draw.text((badge_x + 16, 114), badge_text, fill="#18181B", font=font_badge)

    draw.line([(110, 180), (width - 110, 180)], fill="#F4F4F5", width=2)

    title_lines = wrap_text(guide['title'], font_title, 1380, draw)
    curr_y = 250
    for line in title_lines[:3]:
        draw.text((110, curr_y), line, fill="#09090B", font=font_title)
        curr_y += 72

    curr_y += 24
    sub_lines = wrap_text(guide['subtitle'], font_sub, 1360, draw)
    for line in sub_lines[:2]:
        draw.text((110, curr_y), line, fill="#52525B", font=font_sub)
        curr_y += 40

    # Decorative micro-card
    card_x = width - 400
    card_y = height - 280
    draw.rounded_rectangle([(card_x, card_y), (card_x + 290, card_y + 115)], radius=12, fill="#FBFaf7", outline="#E4E4E7", width=1)
    draw.text((card_x + 20, card_y + 20), "CORA OPERATIONAL ASSETS", fill="#71717A", font=get_font(12, bold=True))
    draw.text((card_x + 20, card_y + 45), "Includes Complete SOP Pack", fill="#18181B", font=get_font(15, bold=True))
    draw.text((card_x + 20, card_y + 75), "✓ Notion + PDF + Templates", fill="#10B981", font=get_font(13, bold=True))

    footer_y = height - 130
    draw.line([(110, footer_y - 30), (width - 110, footer_y - 30)], fill="#F4F4F5", width=2)

    draw.text((110, footer_y), "Author: ", fill="#71717A", font=font_footer)
    bbox_pre = draw.textbbox((0, 0), "Author: ", font=font_footer)
    draw.text((110 + bbox_pre[2], footer_y), guide['author'], fill="#09090B", font=font_footer_bold)

    read_text = f"{guide['chapters']}  •  {guide['read_time']}  •  Verified Operating System"
    draw.text((450, footer_y), read_text, fill="#71717A", font=font_footer)

    out_path = os.path.join(OUTPUT_DIR, f"{guide['slug']}-cover.webp")
    img.save(out_path, "WEBP", quality=92)
    print(f"Generated Guide Cover: {out_path}")

for g in GUIDES:
    generate_guide_og_image(g)
    generate_guide_cover_image(g)

print("All guide social assets generated successfully.")
