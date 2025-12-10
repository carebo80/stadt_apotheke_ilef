// tailwind.config.cjs
const forms = require('@tailwindcss/forms');
module.exports = {
  darkMode: 'class',
  content: [
    "./**/*.php",
    "./assets/js/**/*.js",
    "./assets/css/**/*.css",
    "../plugins/tailpine-components/**/*.php",
  ],
  theme: { extend: {} },
  plugins: [
    forms({ strategy: 'class' }),
    // wenn du Tailwind v4 mit @plugin in CSS nutzt, lass das hier leer
    // ansonsten klassisch aktivieren:
    // require('@tailwindcss/forms'),
    // require('@tailwindcss/typography'),
    // require('@tailwindcss/aspect-ratio'),
  ],
};
