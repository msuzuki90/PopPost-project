/** @type {import('tailwindcss').Config} */
module.exports = {
  mode: 'jit',
  darkMode:'class',
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  safelist: [
    'h-16', 'w-16',
    'h-24', 'w-24',
    'h-32', 'w-32',
    'h-40', 'w-40',
  ],
  theme: {
    extend: {
      spacing: {
        '16': '4rem',
        '24': '6rem',
        '32': '8rem',
        '40': '10rem',
        // Add other sizes as necessary
      },
    },
  },

  plugins: [],
}

