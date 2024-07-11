const VueIgniter = (options) => {
    let id = (typeof options.rootId === 'undefined') ? '#app' : `#${options.rootId}`
    const pageData = JSON.parse(document.querySelector(id).getAttribute('data-page'))

    if (import.meta.env.VITE_MODE === 'chunked') {

        const allPages = import.meta.glob('../../../views/**/*.vue')
        const page = allPages[`../../../views/${pageData.view}.vue`]()

        page.then((app) => {
            options.setup(app.default, Object.assign({}, pageData.data), id)
        })

    } else if (import.meta.env.VITE_MODE === 'single') {

        const allPages = import.meta.glob('../../../views/**/*.vue', { eager: true })
        const page = allPages[`../../../views/${pageData.view}.vue`]

        options.setup(page.default, Object.assign({}, pageData.data), id)
    }
}

export { VueIgniter }