import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', 'Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['Cormorant Garamond', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                brand: {
                    50: '#f4f9f6',
                    100: '#e3f0ea',
                    200: '#c5e0d4',
                    300: '#9bc9b5',
                    400: '#6aab8f',
                    500: '#4a9074',
                    600: '#3a735c',
                    700: '#2f5c4a',
                    800: '#274a3d',
                    900: '#213d33',
                },
                gold: {
                    50: '#fbf8f1',
                    100: '#f5edd8',
                    200: '#ead9ae',
                    300: '#dcc07a',
                    400: '#cfa54f',
                    500: '#b8893a',
                    600: '#9a6d2f',
                },
            },
            boxShadow: {
                card: '0 4px 24px -4px rgba(33, 61, 51, 0.08)',
                'card-hover': '0 12px 40px -8px rgba(33, 61, 51, 0.15)',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'slide-up': 'slideUp 0.4s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
