import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { VitePWA } from "vite-plugin-pwa";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        // ==== PWA ====
        VitePWA({
            registerType: "autoUpdate",
            manifest: {
                name: "Absensi Karyawan",
                short_name: "Absensi",
                start_url: "/",
                display: "standalone",
                background_color: "#ffffff",
                theme_color: "#800000",
                icons: [
                    {
                        src: "/icons/icon-192.png",
                        sizes: "192x192",
                        type: "image/png",
                    },
                    {
                        src: "/icons/icon-512.png",
                        sizes: "512x512",
                        type: "image/png",
                    },
                ],
            },
            workbox: {
                runtimeCaching: [
                    // Force network fetch for HTML pages (no caching)
                    {
                        urlPattern: ({ request }) => request.destination === 'document',
                        handler: 'NetworkOnly',
                    },
                    // Existing asset caching (scripts, styles, images)
                    {
                        urlPattern: ({ request }) =>
                            request.destination === 'script' ||
                            request.destination === 'style' ||
                            request.destination === 'image',
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'assets-cache',
                            expiration: { maxEntries: 100, maxAgeSeconds: 60 * 60 * 24 * 30 },
                        },
                    },
                ],
            },
        }),
    ],
    server: {
        host: "0.0.0.0",
        port: 5173,
        hmr: {
            host: "192.168.1.27",
        },
    },
});
