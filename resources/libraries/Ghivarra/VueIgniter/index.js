const env = import.meta.env
const VueIgniter = (options) => {
    let id = (typeof options.rootId === 'undefined') ? '#app' : `#${options.rootId}`
    const pageData = JSON.parse(document.querySelector(id).getAttribute('data-page'))

    if (env.VITE_MODE === 'chunked') {

        const allPages = import.meta.glob('@/views/**/*.vue')
        const name = `/${import.meta.env.VITE_RESOURCES_DIR}/views/${pageData.view}.vue`
        const page = allPages[name]()

        page.then((app) => {
            options.setup(app.default, Object.assign({}, pageData.data), id)
        })

    } else if (env.VITE_MODE === 'single') {

        const allPages = import.meta.glob('@/views/**/*.vue', { eager: true })        
        const name = `/${import.meta.env.VITE_RESOURCES_DIR}/views/${pageData.view}.vue`
        const page = allPages[name]
        
        options.setup(page.default, Object.assign({}, pageData.data), id)
    }
}

export { VueIgniter }