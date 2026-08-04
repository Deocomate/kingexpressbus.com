/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/admin/**/*.blade.php',
        './public/js/admin.js',
        './public/js/admin/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#fff9e6',
                    100: '#ffefbf',
                    200: '#ffe08a',
                    300: '#ffd156',
                    400: '#ffc43a',
                    500: '#FFC900',
                    600: '#FF9B00',
                    700: '#d97d00',
                    800: '#a85f00',
                    900: '#744100',
                },
            },
            fontFamily: {
                sans: ['Be Vietnam Pro', 'system-ui', 'sans-serif'],
            },
            borderRadius: {
                none: '0px',
                sm: '2px',
                DEFAULT: '4px',
            },
        },
    },
    plugins: [],
};
