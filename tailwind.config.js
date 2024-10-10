// tailwind.config.js
module.exports = {
  content: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './resources/**/*.vue',
  ],
  theme: {
      extend: {
        colors: {
          'primary-500': '#f56565',
        },
      },
  },
  plugins: [],
};