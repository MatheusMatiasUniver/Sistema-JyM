/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'grip-1': '#F20519',
        'grip-2': '#D9042B',
        'grip-3': '#565959',
        'grip-4': '#A4A6A6',
        'grip-5': '#0D0D0D',
        'grip-6': '#F23529',
        'grip-7': '#3A3A39',
        'grip-red-light': '#F73951',
        'grip-red-dark': '#C20415',
        'overlay-black-99': '#0D0D0D99',

        // Semantic mappings updated to use Grip palette
        'primary-dark': '#0D0D0D', // Grip-5
        'secondary-dark': '#565959', // Grip-3

        // Legacy accents retained for compatibility, prefer Grip reds for new UI
        'accent-blue': {
          DEFAULT: '#00d8ff',
          hover: '#00c0e6',
        },
        'accent-purple': '#6a1b9a',
        'accent-blue-btn': {
          DEFAULT: '#007bff',
          hover: '#0056b3',
        },

        'text-white': '#ffffff',
        'text-black': '#000000',
        'text-main': '#333333',
        'border-light': '#cccccc',

        'error-text': '#dc3545', 
        'success-text': '#155724', 
        'success-bg': '#d4edda',
        'success-border': '#c3e6cb',
      },
      spacing: {
        '5': '20px',
        '7.5': '30px',
        '10': '40px',
        '15px': '15px',
        '30px': '30px',
        '220px': '220px',
      },
      fontSize: {
        'lg': '1.125rem',
        'xl': '1.25rem',
        '2xl': '1.5rem',
        '3xl': '1.875rem',
      }
    },
  },
  plugins: [],
}