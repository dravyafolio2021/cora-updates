/**
 * Cora Platform & Marketing Design Tokens (v2.4.0)
 * Monochromatic Notion / Shopify Visual System Standard
 * Core User: Shruti (Studio Administrator)
 */

const CoraDesignTokens = {
  version: "2.4.0",
  systemOwner: "Shruti",
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
    xl: "0 20px 25px -5px rgba(0, 0, 0, 0.12), 0 10px 10px -5px rgba(0, 0, 0, 0.04)"
  },

  layout: {
    appMaxWidth: "80rem",
    marketingMaxWidth: "72rem",
    drawerWidth: "28rem"
  },

  rules: {
    user: "Shruti",
    avatarInitials: "S",
    noNativeAlerts: true,
    useMonochromaticToasts: true,
    useSlidingSideDrawers: true,
    stickyAdminPopover: true,
    claudeCreamBRoll: "#FBFaf7"
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = CoraDesignTokens;
} else if (typeof window !== 'undefined') {
  window.CoraDesignTokens = CoraDesignTokens;
}
