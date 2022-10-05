module.exports = {
  content: [
    './assets/app.js',
    './templates/**/*.twig',
    '!./templates/yearbook/**/*.*'
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms')
  ],
};