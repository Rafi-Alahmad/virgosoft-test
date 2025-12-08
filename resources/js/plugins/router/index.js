import { createRouter, createWebHistory } from 'vue-router';
import { routes } from 'vue-router/auto-routes';
import { setupLayouts } from 'virtual:generated-layouts';
import { redirects } from './additional-routes';
import { setupGuards } from './guards';

const router = createRouter({
    history: createWebHistory(),
    routes: [...redirects, ...setupLayouts(routes)],
});
setupGuards(router);

export { router };
export default function (app) {
    app.use(router);
}
