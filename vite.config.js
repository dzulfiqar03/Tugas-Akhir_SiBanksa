import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/Pages/data-sampah.js', 'resources/js/Pages/data-nasabah.js'],
            refresh: true,
        }),
    ],
});
