/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
            'primary-dark': '#6a1b9a',  
            'primary-light': '#007bff', 
            'accent-green': '#4CAF50',  
            'accent-red': '#dc3545',    
            'accent-yellow': '#fbc02d', 
            'dark-bg': '#0f2027',
            'dark-sidebar': '#203a43',  
            'light-text': '#ffffff',    
            'dark-text': '#333333',     
            'input-bg': '#eeeeee',      
            'button-primary': '#00d8ff',
            'button-primary-hover': '#00c0e6',
        },
        fontFamily: {
            sans: ['"Segoe UI"', 'Tahoma', 'Geneva', 'Verdana', 'sans-serif'], },
    },
    plugins: [],
    },
}