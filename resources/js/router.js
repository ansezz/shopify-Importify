import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: [
        {path: '/', component: require('./views/index.vue').default},
        {path: '/page/help', component: require('./views/pages/help.vue').default},
        {path: '*', component: require('./views/pages/404.vue').default},
    ]
})

export default router
