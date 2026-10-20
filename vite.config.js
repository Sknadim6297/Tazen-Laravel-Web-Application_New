import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/tazen.css',
                'resources/css/yuktione-feedback.css',
                'resources/js/yuktione-feedback.js',
                'resources/js/professional-registration.js',
                'resources/js/tazen-v2/navbar.js',
                'resources/js/tazen-v2/hero.js',
                'resources/js/tazen-v2/panel.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
