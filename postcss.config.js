import purgeCSSPlugin from "@fullhuman/postcss-purgecss"
import { loadEnv } from "vite"

/* global process */

const env = loadEnv(null, process.cwd());
const isProduction = env.VITE_NODE_ENV === 'production'

export default {
    plugins: [
        isProduction && purgeCSSPlugin({
            content: [
                `./app/Views/**/*.php`,
                `./resources/**/*.js`,
                `./resources/**/*.vue`,
            ],
            defaultExtractor: (content) => {
                const contentWithoutStyleBlocks = content.replace(/<style[^]+?<\/style>/gi, "")
                return contentWithoutStyleBlocks.match(/[A-Za-z0-9-_/:]*[A-Za-z0-9-_/]+/g) || [];
            },
            safelist: {
                standard: [
                    /-(leave|enter|appear)(|-(to|from|active))$/,
                    /^(?!(|.*?:)cursor-move).+-move$/,
                    /^router-link(|-exact)-active$/,
                    /data-v-.*/,
                ],
                deep: [
                    
                ],
                greedy: [
                    /^modal-/,
                    /^dropdown-/,
                    /^form-/,
                ]
            }
        })
    ]
}