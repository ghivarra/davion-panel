import swal from "sweetalert";

/**
 * @param {string} uri
 */
function baseUrl(uri) {
    return import.meta.env.VITE_URL + `/${uri}`
}

/**
 * @param {interface} status
 */
function checkAxiosError(status = 200, options = { title: undefined, icon: undefined, text: undefined }) {

    let switchNotDetected = false

    switch (status) {
        case 400:
            swal({
                title: (typeof options?.title !== 'undefined') ? options.title : 'Whoopss!!',
                icon: (typeof options?.icon !== 'undefined') ? options.icon : 'warning',
                text: (typeof options?.text !== 'undefined') ? options.text : 'Ada kesalahan dalam pengisian form',
            })
            break;

        case 401:
            swal({
                title: (typeof options?.title !== 'undefined') ? options.title : 'Whoopss!!',
                icon: (typeof options?.icon !== 'undefined') ? options.icon : 'warning',
                text: (typeof options?.text !== 'undefined') ? options.text : 'Sesi login anda sudah kedaluwarsa, silahkan login kembali',
            }).then(() => {
                window.location.reload()
            })
            break;
        
        case 403:
            swal({
                title: (typeof options?.title !== 'undefined') ? options.title : 'Whoopss!!',
                icon: (typeof options?.icon !== 'undefined') ? options.icon : 'error',
                text: (typeof options?.text !== 'undefined') ? options.text : 'Anda tidak memiliki izin untuk mengakses halaman ini',
            })
            break;

        case 404:
            swal({
                title: (typeof options?.title !== 'undefined') ? options.title : 'Whoopss!!',
                icon: (typeof options?.icon !== 'undefined') ? options.icon : 'error',
                text: (typeof options?.text !== 'undefined') ? options.text : 'Halaman tidak ditemukan',
            })
            break;
            
        default:
            switchNotDetected = true
            break;
    }

    if (switchNotDetected && status >= 500) {
        swal({
            title: (typeof options?.title !== 'undefined') ? options.title : 'Whoopss!!',
            icon: (typeof options?.icon !== 'undefined') ? options.icon : 'warning',
            text: (typeof options?.text !== 'undefined') ? options.text : 'Server sedang sibuk, silahkan coba lagi dalam beberapa saat',
        })
    }
}

/**
 * @param {interface} router
 */
function generateBreadcrumb(router) {
    const currentPath = window.location.pathname.substring(1).split('/')
    let eachPath = ''
    let breadcrumbs = []

    currentPath.forEach((item) => {
        eachPath += `/${item}`
        let resolvedPath = router.resolve(eachPath)
        if (resolvedPath.name !== 'pageNotFound') {
            breadcrumbs.push({
                path: eachPath,
                name: (typeof resolvedPath.name === 'undefined') ? null : resolvedPath.name,
                title: (typeof resolvedPath.meta.pageName === 'undefined') ? null : resolvedPath.meta.pageName
            })
        }
    })

    // return
    return breadcrumbs;
}

/**
 * @param {string} uri
 * @param {string|null|integer} width
 * @param {string|null|integer} height
 * @param {string} priority
 */
function imageUrl(uri, width = null, height = null, priority = 'width') {
    let url = baseUrl(`assets/images/${uri}?priority=${priority}`)

    if (width !== null)
    {
        url += `&width=${width}`
    }

    if (height !== null)
    {
        url += `&height=${height}`
    }

    return url
}

/**
 * @param {string} uri
 */
function loginUrl(uri) {
    return import.meta.env.VITE_URL + '/' + import.meta.env.VITE_LOGIN_PAGE + `/${uri}`
}

/**
 * @param {string} uri
 */
function panelUrl(uri) {
    return import.meta.env.VITE_URL + '/' + import.meta.env.VITE_PANEL_PAGE + `/${uri}`
}


/**
 * @param {array} parentsElement
 */
function parents(child, parent) {
    let parentsElement = []
    while (child !== parent && child !== document) {
        child = child.parentNode
        parentsElement.push(child)
    }

    return parentsElement
}

/**
 * @param {object} reactiveElement
 */
function restructurized(obj) {
    return Array.isArray(obj) ? [... obj] : {... obj}
}

// export functions
export { baseUrl, checkAxiosError, generateBreadcrumb, imageUrl, loginUrl, panelUrl, parents, restructurized }