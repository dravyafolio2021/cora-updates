# Cora Studio AI - Workspace Rules

This file outlines workspace rules and style guides that apply across this project.

## 1. Dialogue and Alert Guidelines
- **No Browser Defaults**: Never invoke browser-native dialogue overlays such as `alert()`, `confirm()`, or `prompt()`.
- **Custom Toast System**: Direct all alerts, errors, and confirmation feedback through the custom monochromatic Toast Notification system (`window.coraShowToast`).
- **Form Drawers**: Replace overlay modals or prompts for complex forms (such as adding shoot bookings) with right-sliding side drawer sheets to maximize screen layout efficiency.

## 2. Admin User and Bottom Sidebar Popovers
- **Sidebar Admin Popover**: The administrator/user widget must sit sticky at the bottom of the sidebar. 
- **Floating Option Card**: Trigger options by clicking the widget to open a clean popover card directly above it (or floating next to it when collapsed) containing quick actions and quick settings.
- **Rich Interactive Elements**: Include a workspace status connection indicator, an active AI model selector, and quota metrics directly in the popover menu.

## 3. Visual Systems and Theme
- **Monochromatic Theme**: Adhere strictly to the Notion/Shopify monochromatic visual palette (neutral shades of white, black, and zinc/grey) with zero colorful gradients or emojis.
- **Light/Dark Mode Support**: Maintain functional classes for light and dark modes, ensuring smooth theme switching with persistent preferences.
- **Clean SVG Iconography**: Utilize thin-lined vector SVGs (`stroke-width: 1.8` or `2.2`) for all indicator elements.
- **System Font Stack**: Fallback automatically to system-ui sans-serif fonts to guarantee caching safety and browser consistency.
