/** @type {import('tailwindcss').Config} */
import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#003366', // UDOM Blue
                    50: '#f0f5fa',
                    100: '#e1ebf5',
                    200: '#c3d7eb',
                    300: '#a5c3e1',
                    400: '#699ccd',
                    500: '#2d75b9',
                    600: '#0055a5', // Base
                    700: '#003366', // Darker (UDOM)
                    800: '#00264d',
                    900: '#001933',
                },
                secondary: {
                    DEFAULT: '#FFD700', // Gold
                    50: '#fffdf0',
                    100: '#fffac2',
                    200: '#fff794',
                    300: '#fff466',
                    400: '#fff138',
                    500: '#ffd700', // Base
                    600: '#ccac00',
                    700: '#998100',
                    800: '#665600',
                    900: '#332b00',
                }
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['Merriweather', ...defaultTheme.fontFamily.serif], // Academic feel
            },
        },
    },
    plugins: [require('@tailwindcss/forms')],
};
