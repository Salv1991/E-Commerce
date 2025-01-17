/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
      extend: {
        colors: {
          'primary-500': '#f56565',
        },
        screens: {
          'xs' : '520px',
        }
      },
  },
  plugins: [],
};