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
        'from-cyan-500',
        'to-indigo-600',
        'focus:ring-cyan-500',
        'text-cyan-500', 
        'bg-cyan-500',
        'border-cyan-500',
        'text-indigo-600',
        'bg-indigo-600',
        'from-indigo-500',
        'to-purple-600',
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
