/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php", // For PHP files in the root directory
    "./admin/**/*.php", // For PHP files inside the 'admin' folder
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
