import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);


const routes = [
    {
        path: '/',
        name: 'ListPage',
        component: () => import('components/ListPage/main.vue'),
        meta: { title: '列表' },
    },
    {
        path: '/map',
        name: 'MapPage',
        component: () => import('components/MapPage/main.vue'),
        meta: { title: '地圖' },
    },
    {
        path: '/travel',
        name: 'TravelPage',
        component: () => import('components/TravelPage/main.vue'),
        meta: { title: '旅行' },
    },
    {
        path: '/live_cam/:LiveCamKey([a-zA-Z0-9_-]{1,20})',
        name: 'LiveCamPage',
        component: () => import('components/LiveCamPage/main.vue'),
        meta: { title: '攝影機' },
        props: true,
    },
    {
        path: '*',
        redirect: {
            name: 'ListPage',
        },
    },
];

const router = new VueRouter({
    linkActiveClass: '',
    linkExactActiveClass: 'is-active',
    // mode: 'history',
    routes,
    scrollBehavior(to, from, savedPosition){
        if (savedPosition) {
            switch (to.name) {
                default:
                    return savedPosition;
                    // break;
            }
        }

        return {
            x: 0,
            y: 0,
        };
    },
});


export default router;