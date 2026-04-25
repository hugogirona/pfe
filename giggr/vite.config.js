import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import VitePluginSvgSpritemap from "@spiriit/vite-plugin-svg-spritemap";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
        VitePluginSvgSpritemap("./resources/icons/*.svg", {
            output: {
                filename: "default.svg",
            },
            styles: "resources/css/spritemap.css",
        }),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
