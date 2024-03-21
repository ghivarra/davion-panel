import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import basicSsl from '@vitejs/plugin-basic-ssl';
import path from "path";

export default defineConfig(() => {
	const env = loadEnv(null, process.cwd());

	return {
		plugins: [
            vue(), 
            basicSsl(),
        ],
		build: {
			emptyOutDir: false,
            copyPublicDir: false,
			outDir: 'public',
			assetsDir: env.VITE_ASSETS_DIR,
			manifest: true,
			rollupOptions: {
				input: `./${env.VITE_RESOURCES_DIR}/${env.VITE_ENTRY_FILE}`,
			},
		},
		server: {
			origin: env.VITE_ORIGIN,
			port: env.VITE_PORT,
			strictPort: true,
            https: true,
            hmr: {
                host: env.VITE_HOST,
                port: env.VITE_PORT,
                protocol: 'wss'
            }
		},
		resolve: {
			alias: {
				"@": path.resolve(__dirname, `./${env.VITE_RESOURCES_DIR}`),
			},
		},
	};
});
