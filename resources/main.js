import './assets/main.scss'
import { createApp, h } from 'vue'
import { VueIgniter } from './libraries/Ghivarra/VueIgniter'
import { createRouter, createWebHistory } from 'vue-router'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import indexSingle from './router/index-single'
import indexChunked from './router/index-chunked'
import axios from 'axios'

// set axios settings
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// load router
const vueRouter = createRouter({
    history: createWebHistory(),
    routes: (import.meta.env === 'single') ? indexSingle : indexChunked
});

// create new Vue CodeIgniter
VueIgniter({
    rootId: import.meta.env.VITE_APP_ID,
    setup: (App, props, root) => {
        const app = createApp({
            render: () => h(App, props)
        })
        app.config.unwrapInjectedRef = true

        app.use(vueRouter)
        app.component('font-awesome', FontAwesomeIcon)
        app.mount(root)
    }
})