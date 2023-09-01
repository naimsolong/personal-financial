import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import dotenv from 'dotenv'

dotenv.config() // load env vars from .env

const host = process.env.VITE_APP_URL;
const port = process.env.VITE_PORT;

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
    server: { 
        host, 
        hmr: { host }, 
        port: port, 
        // https: { 
        //     key: fs.readFileSync(`/path/to/${host}.key`), 
        //     cert: fs.readFileSync(`/path/to/${host}.crt`), 
        // }, 
    },
});
