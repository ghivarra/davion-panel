const router = () => {
    // uncomment this if using chunked files
    // return import('@/router/config/index-chunked')

    // uncomment this if using single files
    return import('@/router/config/index-single')
}

export default router