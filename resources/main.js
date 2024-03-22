import { createApp, h } from 'vue'
import { VueIgniter } from './library/Ghivarra/VueIgniter'

// create new Vue CodeIgniter
VueIgniter({
    rootId: 'app',
    setup: (App, props, root) => {
        const app = createApp({
            render: () => h(App, props)
        })
        //app.use(router)
        app.mount(root)
    }
})