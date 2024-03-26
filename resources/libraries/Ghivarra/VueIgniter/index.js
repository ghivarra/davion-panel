const VueIgniter = (options) => {
    let id = (typeof options.rootId === 'undefined') ? '#app' : `#${options.rootId}`
    const pageData = JSON.parse(document.querySelector(id).getAttribute('data-page'))

    const allPages = import.meta.glob('../../../views/**/*.vue');
    const page = allPages[`../../../views/${pageData.view}.vue`]();

    page.then((app) => {
        options.setup(app.default, Object.assign({}, pageData.data), id)
    })
}

export { VueIgniter }