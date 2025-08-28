module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/css/**/*.css',
  ],
  theme: {
     extend: {
      fontFamily: {
        sans: ['Noto Sans', 'ui-sans-serif', 'system-ui'],
      },
    },
  },
  plugins: [],
  safelist: [
    'perspective', 'preserve-3d', 'backface-hidden', 'rotate-y-180'
  ],
}