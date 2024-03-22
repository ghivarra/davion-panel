const VueIgniter = (options) => {
    let id = (typeof options.rootId === 'undefined') ? '#app' : `#${options.rootId}`
    const pageData = JSON.parse(document.querySelector(id).getAttribute('data-page'))
    import(`../../../views/${pageData.view}.vue`).then((app) => {
        options.setup(app.default, Object.assign({}, pageData.data), id)
    })
}

export { VueIgniter }