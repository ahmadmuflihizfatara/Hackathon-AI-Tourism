import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            // ── Custom Color Palette ─────────────────────────────────────
            colors: {
                // Terracotta (primary)
                'terracotta': {
                    DEFAULT: '#C1602B',
                    light:   '#F4C9B0',
                    dark:    '#9C4A1F',
                },

                // Warm Sand (background)
                'warm-sand': {
                    DEFAULT: '#F5EFE6',
                    light:   '#FAF7F2',
                    dark:    '#EDE3D6',
                },

                // Emerald Green (accent)
                'emerald': {
                    DEFAULT: '#3A7D5C',
                    light:   '#B8DBCA',
                    dark:    '#265C42',
                },

                // Amber (secondary accent for budget/tips)
                'amber': {
                    DEFAULT: '#D4872E',
                    light:   '#F5D9B0',
                    dark:    '#A86420',
                },
            },

            // ── Typography ───────────────────────────────────────────────
            fontFamily: {
                heading: ['Poppins', ...defaultTheme.fontFamily.sans],
                body:    ['Open Sans', ...defaultTheme.fontFamily.sans],
                sans:    ['Open Sans', ...defaultTheme.fontFamily.sans],
            },

            // ── Border Radius ────────────────────────────────────────────
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
            },

            // ── Box Shadow ───────────────────────────────────────────────
            boxShadow: {
                'terracotta': '0 4px 24px -4px rgba(193, 96, 43, 0.15)',
                'emerald':    '0 4px 24px -4px rgba(58, 125, 92, 0.12)',
                'warm':       '0 2px 16px -2px rgba(193, 96, 43, 0.08)',
            },

            // ── Animation ────────────────────────────────────────────────
            animation: {
                'fade-in': 'fadeIn 0.4s ease-out forwards',
                'slide-up': 'slideUp 0.5s ease-out forwards',
            },

            keyframes: {
                fadeIn: {
                    '0%':   { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%':   { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [],
};
