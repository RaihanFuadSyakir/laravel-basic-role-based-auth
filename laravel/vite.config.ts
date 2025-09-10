import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';
const env = process.env;
const host = env.VITE_HOST || 'localhost';
const port = parseInt(env.VITE_PORT || '8000', 10);
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
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
        host: host, // Or your local IP address, e.g., '192.168.1.100'
        port: port, // Ensure this matches the port Laravel expects
        hmr: {
          host: 'localhost', // important: force HMR to use localhost, not IPv6 or IP
        },
    },
});
