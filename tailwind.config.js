/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./admin/**/*.php",
    "./src/**/*.js", // Include your JavaScript files
    "./components/**/*.html", // If you use HTML components
  ],

  theme: {
    extend: {},
  },
  plugins: [],
};
