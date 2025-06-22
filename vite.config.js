import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import react from "@vitejs/plugin-react";
import { resolve } from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/App.tsx"],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            "@": resolve(__dirname, "resources/js"),
            "@/components": resolve(__dirname, "resources/js/components"),
            "@/hooks": resolve(__dirname, "resources/js/hooks"),
            "@/utils": resolve(__dirname, "resources/js/utils"),
            "@/types": resolve(__dirname, "resources/js/types"),
            "@/services": resolve(__dirname, "resources/js/services"),
        },
    },
    server: {
        hmr: {
            host: "localhost",
        },
    },
});
