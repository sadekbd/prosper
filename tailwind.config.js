import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  // ── Enable class-based dark mode ──────────────────────────────
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        'pm-navy':  '#0B132B',
        'pm-cyan':  '#00B4D8',
        'pm-gold':  '#FFB703',
        'pm-grey':  '#F8F9FA',
        'pm-slate': '#2D3142',
      },
      fontFamily: {
        heading: ['Montserrat', ...defaultTheme.fontFamily.sans],
        body:    ['Inter',       ...defaultTheme.fontFamily.sans],
      },
      keyframes: {
        fadeInUp: {
          '0%':   { opacity: '0', transform: 'translateY(24px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%':      { transform: 'translateY(-10px)' },
        },
      },
      animation: {
        'fade-in-up': 'fadeInUp 0.7s ease-out forwards',
        'float':      'float 4s ease-in-out infinite',
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
  ],
};