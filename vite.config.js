import { defineConfig, loadEnv } from "vite"
import vue from "@vitejs/plugin-vue"
import basicSsl from '@vitejs/plugin-basic-ssl'
import { fileURLToPath, URL } from 'url'
import { customHotReload } from "./resources/libraries/Ghivarra/PHP-Hmr"
import ViteRestart from 'vite-plugin-restart'

/*global process*/

export default defineConfig(() => {
	const env = loadEnv(null, process.cwd());

	return {
		plugins: [
            vue(), 
            basicSsl(),
			customHotReload(),
			ViteRestart({
				restart: [
					'postcss.config.js'
				]
			})
        ],
		css: {
			postcss: './postcss.config.js',
		},
		build: {
			emptyOutDir: false,
            copyPublicDir: false,
			outDir: 'public',
			assetsDir: env.VITE_ASSETS_DIR,
			manifest: true,
			rollupOptions: {
				input: `./${env.VITE_RESOURCES_DIR}/${env.VITE_ENTRY_FILE}`,
				output: {
					manualChunks: (id) => {

						// the first order executed first so make sure the dependency from the packages is
						// chunked first to prevent uncaught reference error after bundled
						const chunkedFiles = [
							'fortawesome', // chunk big icons packages
							'node_modules', // chunk all another dependency packages,
							'resources/libraries', // chunk all custom library packages
						];

						// debug all libs needed
						// console.log(id)

						for (let i = 0; i < chunkedFiles.length; i++) {
							const chunked = chunkedFiles[i];
							if (id.includes(chunked)) {
								return 'chunk-' + chunked
							}
						}

						return undefined
					}
				}
			},
		},
		server: {
			origin: env.VITE_ORIGIN,
			port: env.VITE_PORT,
			strictPort: true,
            https: true,
			cors: true,
            hmr: {
                host: env.VITE_HOST,
                port: env.VITE_PORT,
                protocol: 'wss'
            }
		},
		resolve: {
			alias: {
				'@': fileURLToPath(new URL(`./${env.VITE_RESOURCES_DIR}`, import.meta.url))
			}
		}
	};
});
