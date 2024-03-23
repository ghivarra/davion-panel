import Swal from 'sweetalert2'

function baseUrl(uri) {
    return import.meta.env.VITE_URL + `/${uri}`
}

function panelUrl(uri) {
    return import.meta.env.VITE_URL + '/' + import.meta.env.VITE_PANEL_PAGE + `/${uri}`
}

function loginUrl(uri) {
    return import.meta.env.VITE_URL + '/' + import.meta.env.VITE_LOGIN_PAGE + `/${uri}`
}

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
    } else {
        Swal.fire('Whoopss!!', 'Server sedang sibuk, silahkan coba lagi di lain waktu', 'error')
    }
}

// export functions
export { baseUrl, panelUrl, loginUrl, imageUrl, checkAxiosError }