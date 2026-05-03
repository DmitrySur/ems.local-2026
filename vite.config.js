import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import basicSsl from '@vitejs/plugin-basic-ssl'
import path from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/inertia/app.js'],
            refresh: true,
        }),
        vue(),
        basicSsl(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js/inertia'),
        },
    },
    server: {
        host: 'ems.local',
        port: 5173,
        strictPort: true,
        https: true,
        cors: true,
        hmr: {
            host: 'ems.local',
            protocol: 'wss',
            port: 5173,
        },
    },
})
