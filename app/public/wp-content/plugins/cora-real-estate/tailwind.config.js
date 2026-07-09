/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./admin-dashboard.php",
    "./cora-real-estate.php",
    "./src/**/*.{js,jsx,ts,tsx}",
    "./views/**/*.php",
    "./apex-realty-group/**/*.html"
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
        display: ['Inter', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
      },
      colors: {
        workspace: {
          sidebar: '#f7f7f5',
          content: '#ffffff',
        },
        brand: {
          gold: '#B89A74',
          bronze: '#8A6D4B',
          cream: '#FAF9F6',
          bgsecondary: '#F4F3EB',
          card: '#FFFFFF',
          dark: '#1A1A1A',
        }
      },
      animation: {
        'fade-up': 'fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
        'fade-in': 'fadeIn 1s ease forwards',
        'pulse-glow': 'pulseGlow 2s infinite alternate',
        'marquee': 'marquee 25s linear infinite',
      },
      keyframes: {
        fadeUp: {
          '0%': { opacity: '0', transform: 'translateY(24px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        pulseGlow: {
          '0%': { transform: 'scale(1)', opacity: '0.8', filter: 'drop-shadow(0 0 4px rgba(184, 154, 116, 0.4))' },
          '100%': { transform: 'scale(1.05)', opacity: '1', filter: 'drop-shadow(0 0 12px rgba(184, 154, 116, 0.8))' },
        },
        marquee: {
          '0%': { transform: 'translateX(0)' },
          '100%': { transform: 'translateX(-100%)' }
        }
      }
    },
  },
  plugins: [],
}
