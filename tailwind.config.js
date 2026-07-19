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
        'brand-navy': '#1E104E',
        'brand-purple': '#452E5A',
        'brand-orange': '#FF653F',
        'brand-yellow': '#FFC85C',
      }
    },
  },
  plugins: [],
}
