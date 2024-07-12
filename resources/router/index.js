const routes = () => {
    return (import.meta.env === 'single') ? import('@/router/config/index-single.js') : import('@/router/config/index-chunked')
}

export default routes