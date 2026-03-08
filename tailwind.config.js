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
        'from-indigo-500',
        'to-violet-600',
        'from-violet-500',
        'to-purple-600',
        'focus:ring-indigo-500',
        'text-indigo-500',
        'bg-indigo-500',
        'border-indigo-500',
        'text-violet-600',
        'bg-violet-600',
        'from-cyan-500',
        'to-indigo-600',
        'focus:ring-cyan-500',
        'text-cyan-500',
        'bg-cyan-500',
        'border-cyan-500',
    ],

    theme: {
        extend: {
            colors: {
                accent: {
                    DEFAULT: 'var(--accent)',
                    hover: 'var(--accent-hover)',
                    soft: 'var(--accent-soft)',
                    strong: 'var(--accent-strong)',
                },
                sidebar: {
                    DEFAULT: 'var(--sidebar-bg)',
                    text: 'var(--sidebar-text)',
                    hover: 'var(--sidebar-hover)',
                    active: 'var(--sidebar-active)',
                    icon: 'var(--sidebar-icon)',
                },
                surface: {
                    DEFAULT: 'var(--surface)',
                    alt: 'var(--surface-alt)',
                },
                ink: {
                    DEFAULT: 'var(--ink)',
                    soft: 'var(--ink-soft)',
                    muted: 'var(--ink-muted)',
                },
            },
            fontFamily: {
                sans: ['Inter', 'Space Grotesk', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                'sm': 'var(--radius-sm)',
                'md': 'var(--radius-md)',
                'lg': 'var(--radius-lg)',
                'xl': 'var(--radius-xl)',
            },
            boxShadow: {
                'sm': 'var(--shadow-sm)',
                'md': 'var(--shadow-md)',
                'lg': 'var(--shadow-lg)',
                'xl': 'var(--shadow-xl)',
                'glow': 'var(--shadow-glow)',
            },
            animation: {
                'fade-in': 'fadeSlideUp 400ms var(--ease-smooth) both',
                'scale-in': 'scaleIn 250ms var(--ease-smooth) both',
                'slide-left': 'slideInLeft 300ms var(--ease-smooth) both',
                'slide-right': 'slideInRight 300ms var(--ease-smooth) both',
                'shimmer': 'shimmer 1.5s infinite',
                'float': 'float 3s ease-in-out infinite',
                'pulse-glow': 'pulse-glow 2s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
