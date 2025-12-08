// 👉 Redirects
export const redirects = [
    {
        path: '/:pathMatch(.*)*',
        redirect: () => ({
            name: 'errors$404'
        })
    },
    {
        path: '/',
        redirect: () => ({
            name: 'dashboard'
        })
    },
];
export const routes = [];
