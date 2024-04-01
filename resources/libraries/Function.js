import Swal from 'sweetalert2'

/**
 * @param {string} uri
 */
function baseUrl(uri) {
    return import.meta.env.VITE_URL + `/${uri}`
}

/**
 * @param {string} uri
 */
function panelUrl(uri) {
    return import.meta.env.VITE_URL + '/' + import.meta.env.VITE_PANEL_PAGE + `/${uri}`
}

/**
 * @param {string} uri
 */
function loginUrl(uri) {
    return import.meta.env.VITE_URL + '/' + import.meta.env.VITE_LOGIN_PAGE + `/${uri}`
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
 * @param {interface} status
 */
function checkAxiosError(status) {

    // return if nothing happened
    if (status === 200) {
        return false
    }

    // check if connection or server error 
    if (status >= 500) {
        Swal.fire('Whoopss!!', 'Jaringan internet anda bermasalah atau server sedang sibuk, silahkan coba lagi', 'error')

    // check if session already ended
    } else if (status === 401) {
        Swal.fire('Whoopss!!', 'Sesi login anda sudah kedaluwarsa, silahkan login kembali', 'warning').then(() => {
            window.location.reload()
        })
    
    // if error but differ from above
    } else if (status === 403) {
        Swal.fire('Whoopss!!', 'Anda tidak memiliki izin untuk mengakses halaman ini', 'error').then(() => {
            window.history.back()
        })
    
    // if error but differ from above
    } else {
        Swal.fire('Whoopss!!', 'Server sedang sibuk, silahkan coba lagi di lain waktu', 'error')
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
        breadcrumbs.push({
            path: eachPath,
            name: (typeof resolvedPath.name === 'undefined') ? null : resolvedPath.name,
            title: (typeof resolvedPath.meta.pageName === 'undefined') ? null : resolvedPath.meta.pageName
        })
    })

    // return
    return breadcrumbs;
}

// export functions
export { baseUrl, panelUrl, loginUrl, imageUrl, checkAxiosError, generateBreadcrumb }