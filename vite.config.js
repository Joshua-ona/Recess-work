import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
<<<<<<< HEAD
                'resources/css/tailwind.css',
                'resources/js/app.js',
            ],
=======
    'resources/css/app.css',
    'resources/sass/app.scss',
    'resources/js/app.js',
],
>>>>>>> 44d2470e921153fee253e0c93f4c5d1009eeb50f
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
