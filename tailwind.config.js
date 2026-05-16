import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                cream: { 50: '#FBF6EA', 100: '#F6EFDE', 200: '#EFE5CB', 300: '#E5D6B0', 400: '#D9C492', 500: '#C7AC74' },
                sage:  { 50: '#F1F3EC', 100: '#E2E7D7', 200: '#C8D2B5', 300: '#A8B891', 400: '#8BA174', 500: '#6F875C', 600: '#586B48', 700: '#465638' },
                clay:  { 50: '#FAF1E8', 100: '#F1DEC9', 200: '#E5C39E', 300: '#D29F70', 400: '#B97D4D', 500: '#9A6238', 600: '#7B4D2C' },
                sun:   { 50: '#FEF7E1', 100: '#FBEBB0', 200: '#F7D87A', 300: '#F2C246', 400: '#E8A92B', 500: '#C58A1B' },
                peach: { 100: '#FCE4D2', 200: '#F8C8A8', 300: '#F2A878', 400: '#E88753' },
                ink:   { 700: '#3B342B', 800: '#2A2520', 900: '#1F1B17' },
            },
            fontFamily: {
                serif: ['Newsreader', 'Georgia', 'serif'],
                sans: ['"DM Sans"', ...defaultTheme.fontFamily.sans],
                hand: ['Caveat', 'cursive'],
                modern: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: { '4xl': '2rem', '5xl': '2.5rem' },
            boxShadow: {
                card: '0 1px 2px rgba(60,42,20,.05), 0 8px 24px -8px rgba(60,42,20,.18), 0 28px 60px -30px rgba(60,42,20,.25)',
                lift: '0 4px 10px rgba(60,42,20,.07), 0 24px 50px -16px rgba(60,42,20,.28), 0 60px 100px -50px rgba(60,42,20,.35)',
                note: '0 1px 1px rgba(60,42,20,.05), 0 6px 12px -4px rgba(60,42,20,.15)',
            },
        },
    },

    plugins: [forms],
};
