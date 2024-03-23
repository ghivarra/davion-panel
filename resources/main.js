import { createApp, h } from 'vue'
import { VueIgniter } from './library/Ghivarra/VueIgniter'

// create new Vue CodeIgniter
VueIgniter({
    rootId: import.meta.env.VITE_APP_ID,
    setup: (App, props, root) => {
        const app = createApp({
            render: () => h(App, props)
        })
        //app.use(router)
        app.mount(root)
    }
})