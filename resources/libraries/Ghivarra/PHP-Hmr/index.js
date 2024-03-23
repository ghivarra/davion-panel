const customHotReload = () => {
    return {
        name: 'custom-hot-reload',
        enforce: 'post',
        handleHotUpdate: ({file, server}) => {
            if (file.endsWith('.php')) {
                console.log('reload PHP file')
                server.ws.send({
                    type: 'full-reload',
                    path: 'app/*'
                })
            }
        }
    }
}

export { customHotReload }