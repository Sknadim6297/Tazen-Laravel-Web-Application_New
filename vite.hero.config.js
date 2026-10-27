import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [tailwindcss()],
    publicDir: false,
    build: {
        outDir: 'storage/hero-build',
        emptyOutDir: true,
        rollupOptions: {
            input: 'resources/css/yuktione-hero.css',
        },
    },
});
