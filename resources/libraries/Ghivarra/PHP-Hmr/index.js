import * as path from 'path';

/* global __dirname */

const customHotReload = () => {
    return {
        name: 'custom-hot-reload',
        enforce: 'post',
        handleHotUpdate: ({file, server}) => {
            let rootPath = path.normalize(`${__dirname}/../../../../app/Views`)
            let filePath = path.normalize(file)
            if (filePath.endsWith('.php') && filePath.startsWith(rootPath)) {
                server.ws.send({
                    type: 'full-reload',
                    path: '*'
                })
            }
        }
    }
}

export { customHotReload }