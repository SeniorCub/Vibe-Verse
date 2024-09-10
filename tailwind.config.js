/** @type {import('tailwindcss').Config} */
module.exports = {
     content: ["./src/**/*.{html,js}"],
     theme: {
          extend: {
               fontFamily: {
                    sans: ['Recursive', 'sans-serif'],
               },
               colors: {
                    primary: '#31302E',
                    gold: '#8A8D56',
                    dark: '#939185',
                    black: '#333',
                    white: '#d7d7cd',
               }
          },
     },
     plugins: [],
};
