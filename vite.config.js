import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import vue from "@vitejs/plugin-vue";
import VueRouter from "unplugin-vue-router/vite";
import {
    VueRouterAutoImports,
    getPascalCaseRouteName,
} from "unplugin-vue-router";
import AutoImport from "unplugin-auto-import/vite";
import Components from "unplugin-vue-components/vite";
import Layouts from "vite-plugin-vue-layouts";

export default defineConfig({
    plugins: [
        VueRouter({
            routesFolder: "resources/js/pages",
            dts: "resources/js/typed-router.d.ts",
            importMode: "async",
        }),
        Layouts({
            layoutsDirs: "resources/js/layouts",
            defaultLayout: "default",
        }),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
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

        AutoImport({
            imports: ["vue", "vue-router", VueRouterAutoImports, "pinia"],
            dirs: [
                "./resources/js/composables/",
                "./resources/js/utils/",
                "./resources/js/plugins/",
                "./resources/js/stores/",
            ],
            vueTemplate: true,
            dts: true,
            eslintrc: {
                enabled: true,
                filepath: "./.eslintrc-auto-import.json",
            },
        }),

        Components({
            dirs: [
                "resources/js/components",
                "resources/js/layouts",
            ],
            extensions: ["vue"],
            deep: true,
            dts: true,
            directoryAsNamespace: false,
            collapseSamePrefixes: false,
        }),
    ],
    resolve: {
        alias: {
            "@": "/resources/js",
            "@layouts": "/resources/js/layouts",
            "@components": "/resources/js/components",
            "@composables": "/resources/js/composables",
            "@utils": "/resources/js/utils",
            "@plugins": "/resources/js/plugins",
            "@stores": "/resources/js/stores",
            "@assets": "/resources/js/assets",
            "@styles": "/resources/js/styles",
        },
    },
});
