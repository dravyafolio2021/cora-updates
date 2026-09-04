import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        background: "var(--bg)",
        foreground: "var(--text-primary)",
      },
      fontFamily: {
        sans: ["'General Sans'", "var(--font-sans)", "Inter", "-apple-system", "sans-serif"],
        display: ["'General Sans'", "var(--font-display)", "-apple-system", "BlinkMacSystemFont", "sans-serif"],
        heading: ["'General Sans'", "var(--font-display)", "-apple-system", "BlinkMacSystemFont", "sans-serif"],
        mono: ["var(--font-mono)", "JetBrains Mono", "monospace"],
        scribble: ["var(--font-scribble)", "Caveat", "cursive"],
      },
      borderRadius: {
        '2xl': '16px',
        '3xl': '24px',
      },
      keyframes: {
        marquee: {
          '0%': { transform: 'translateX(0%)' },
          '100%': { transform: 'translateX(-50%)' },
        },
        'review-scroll': {
          '0%': { transform: 'translateX(0%)' },
          '100%': { transform: 'translateX(-50%)' },
        },
      },
      animation: {
        marquee: 'marquee 30s linear infinite',
        'review-scroll': 'review-scroll 45s linear infinite',
      },
    },
  },
  plugins: [],
};
export default config;
