import './assets/main.scss'
import { createApp, h } from 'vue'
import { VueIgniter } from './libraries/Ghivarra/VueIgniter'
import { createRouter, createWebHistory } from 'vue-router'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import router from './router'
import axios from 'axios'

// set axios settings
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// load router
const vueRouter = createRouter({
    history: createWebHistory(),
    routes: router
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