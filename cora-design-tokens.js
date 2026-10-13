/**
 * Cora Platform & Marketing Design Tokens (v4.0.0)
 * Monochromatic Notion / Shopify Visual System Standard
 * Core User: Studio Admin (Studio Administrator)
 */

const CoraDesignTokens = {
  version: "4.0.0",
  systemOwner: "Studio Admin",
  palette: {
    neutral: {
      50: "#fafafa",
      100: "#f4f4f5",
      200: "#e4e4e7",
      300: "#d4d4d8",
      400: "#a1a1aa",
      500: "#71717a",
      600: "#52525b",
      700: "#3f3f46",
      800: "#27272a",
      900: "#18181b",
      950: "#09090b",
      pureWhite: "#ffffff",
      pureBlack: "#000000"
    },
    brandSpecial: {
      claudeCream: "#FBFaf7",
      claudeCreamBorder: "#F2EDE4"
    },
    stateAccents: {
      active: {
        bg: "rgba(34, 197, 94, 0.1)",
        border: "rgba(34, 197, 94, 0.2)",
        text: "#16a34a",
        darkText: "#4ade80",
        solid: "#22c55e"
      },
      warning: {
        bg: "rgba(234, 179, 8, 0.1)",
        border: "rgba(234, 179, 8, 0.2)",
        text: "#ca8a04",
        darkText: "#facc15",
        solid: "#eab308"
      },
      critical: {
        bg: "rgba(239, 68, 68, 0.1)",
        border: "rgba(239, 68, 68, 0.2)",
        text: "#dc2626",
        darkText: "#f87171",
        solid: "#ef4444"
      },
      neutral: {
        bg: "rgba(113, 113, 122, 0.1)",
        border: "rgba(113, 113, 122, 0.2)",
        text: "#52525b",
        darkText: "#a1a1aa",
        solid: "#71717a"
      }
    }
  },

  typography: {
    fontFamilies: {
      sans: "'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
      mono: "'JetBrains Mono', Menlo, Monaco, Consolas, monospace"
    },
    scale: {
      displayHero: { size: "3rem", lineHeight: "1.1", weight: "800", tracking: "-0.025em" },
      displayApp: { size: "2rem", lineHeight: "1.2", weight: "700", tracking: "-0.02em" },
      h1: { size: "1.5rem", lineHeight: "1.3", weight: "600", tracking: "-0.015em" },
      h2: { size: "1.125rem", lineHeight: "1.4", weight: "600", tracking: "-0.01em" },
      h3: { size: "0.875rem", lineHeight: "1.4", weight: "500", tracking: "0" },
      body: { size: "0.875rem", lineHeight: "1.5", weight: "400", tracking: "0" },
      caption: { size: "0.75rem", lineHeight: "1.4", weight: "400", tracking: "0" },
      code: { size: "0.8125rem", lineHeight: "1.5", weight: "500", tracking: "0" }
    }
  },

  radii: {
    none: "0px",
    sm: "2px",
    md: "6px",
    lg: "8px",
    xl: "12px",
    "2xl": "16px",
    full: "9999px"
  },

  shadows: {
    sm: "0 1px 2px 0 rgba(0, 0, 0, 0.05)",
    md: "0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)",
    lg: "0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04)",
    xl: "0 20px 25px -5px rgba(0, 0, 0, 0.12), 0 10px 10px -5px rgba(0, 0, 0, 0.04)",
    drawer: "-16px 0 48px rgba(0, 0, 0, 0.16)",
    mobileSheet: "0 -16px 48px rgba(0, 0, 0, 0.18)"
  },

  layout: {
    appMaxWidth: "80rem",
    marketingMaxWidth: "72rem",
    drawerWidth: "40rem", // 640px full master standard
    drawerBackdropZIndex: 100040,
    drawerZIndex: 100050
  },

  drawerStandard: {
    architecture: "shopifyMainAreaDockedPanel",
    motion: "bottomSlideUp", // Slides up smoothly from bottom into docked position
    coverage: "availableMainArea", // Fills exact available space of main area based on sidebar status
    sidebarAccessible: true, // Sidebar remains 100% visible, clickable, and accessible
    overlay: false, // Zero overlay / zero backdrop behind panel
    shadow: "none", // Zero shadow, clean architectural boundary
    borders: "clearStructural", // Clear border-left and border-top
    desktop: {
      position: "fixed",
      top: "var(--cora-main-top, 52px)", // Docks directly below header
      bottom: "0px",
      left: "var(--cora-main-left, 16rem)", // Aligned to sidebar edge
      right: "0px",
      width: "var(--cora-main-width, calc(100vw - 16rem))", // Fills available space
      maxWidth: "var(--cora-main-width, calc(100vw - 16rem))",
      height: "var(--cora-main-height, calc(100vh - 52px))",
      borderRadius: "0px", // Square, flush formal architecture
      borderTop: "1px solid #e4e4e7",
      borderLeft: "1px solid #e4e4e7",
      boxShadow: "none",
      zIndex: 100050,
      transition: "transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.15s ease"
    },
    mobile: {
      position: "fixed",
      top: "var(--cora-main-top, 52px)",
      bottom: "0px",
      left: "0px",
      right: "0px",
      width: "100vw",
      borderRadius: "0px",
      boxShadow: "none",
      zIndex: 100050
    },
    backdrop: false, // No backdrop overlay
    portaling: "document.body",
    mainContentScrollLockOnly: true, // Lock only main.cora-main; NEVER lock document.body
    dismissOnEscape: true
  },

  rules: {
    user: "Studio Admin",
    avatarInitials: "S",
    noNativeAlerts: true,
    useMonochromaticToasts: true,
    useMainAreaDockedPanels: true, // Shopify-inspired: all drawers slide from bottom and dock into main area
    noOverlayOrBackdrop: true,     // Zero dark blur overlay behind drawers
    zeroShadowDockedUI: true,      // Clean structural borders, zero shadows
    sidebarNeverOverlaid: true,    // Sidebar remains 100% visible and accessible
    portalDrawersToBody: true,
    stickyAdminPopover: true,
    claudeCreamBRoll: "#FBFaf7",
    noOutlineStyling: true,
    useTonalSelection: true,
    parallelExecutionAndTaskTracking: true,
    strictModuleIsolation: true
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = CoraDesignTokens;
} else if (typeof window !== 'undefined') {
  window.CoraDesignTokens = CoraDesignTokens;
}
