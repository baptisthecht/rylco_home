/** @type {DefaultColors} */
const colors = require('tailwindcss/colors')
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {},
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      black: colors.black,
      white: colors.white,
      gray: colors.gray,
      blue: colors.blue,
      amber: colors.amber,
      emerald: colors.emerald,
      indigo: colors.indigo,
      yellow: colors.yellow,
      orange: colors.orange,
      rose: colors.rose,
      slate: colors.slate,
      sky: colors.sky,
      gradient1: '#f7364d',
      gradient2: '#f77557',
      primary: {
        DEFAULT: '#f85551',
        hover: '#f9726e',
        menubg: '#ffebeb'
      }
    }
  },
  plugins: [],
}

