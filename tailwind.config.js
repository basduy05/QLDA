import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        'from-emerald-500',
        'to-green-600',
        'focus:ring-emerald-500',
        'text-emerald-500', 
        'bg-emerald-500',
        'border-emerald-500',
        'text-green-600',
        'bg-green-600',
    ],

    theme: {
        extend: {
            colors: {
                accent: 'var(--accent)',
                'accent-strong': 'var(--accent-strong)',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
