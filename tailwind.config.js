/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "templates/**/*.html.twig",
  ],
  theme: {
    fontFamily: {
        'serif': ['serif'],
        'mono': ['monospace'],
    },
    extend: {
      flex: {
        '2': '2 2 0%',
        '4': '4 4 0%'
      }
    },
  },
  plugins: [],
}

