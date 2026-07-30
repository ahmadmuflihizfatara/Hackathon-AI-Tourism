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
                'primary': {
                    DEFAULT: '#004777',
                    dark: '#002B47',
                    light: '#4D8AB1',
                },
                'secondary': '#2D6A4F',
                'tertiary': '#F4EBD0',
                'neutral-color': '#1A1C1E',
                
                // Restored old colors
                'emerald': {
                    DEFAULT: '#3A7D5C',
                    dark:    '#265C42',
                    light:   '#B8DBCA',
                },
                'amber': {
                    DEFAULT: '#D4872E',
                    dark: '#B4691B',
                },
                
                // Warm Sand (background - keeping for safety but we'll use white/gray)
                'warm-sand': {
                    DEFAULT: '#F5EFE6',
                    light:   '#FAF7F2',
                    dark:    '#EDE3D6',
                },
            },

            // ── Typography ───────────────────────────────────────────────
            fontFamily: {
                heading: ['Montserrat', ...defaultTheme.fontFamily.sans],
                body:    ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                sans:    ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },

            // ── Border Radius ────────────────────────────────────────────
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
            },

            // ── Box Shadow ───────────────────────────────────────────────
            boxShadow: {
                'primary': '0 4px 24px -4px rgba(193, 96, 43, 0.15)',
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
