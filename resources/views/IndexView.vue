<template>
    <div v-show="useLoader">
        <Transition name="loader">
            <preload-component v-if="loaderState"></preload-component>
        </Transition>
    </div>
    <router-view v-bind:token="csrfToken" v-bind:hash="csrfHash"></router-view>
</template>

<script>

import { dom } from '@fortawesome/fontawesome-svg-core'
import { computed } from 'vue'
import { baseUrl, imageUrl } from '../libraries/Function'
import PreloadComponent from '../components/PreloadComponent.vue'

export default {
    name: 'index-view',
    props: ['website', 'title', 'csrfToken', 'csrfHash'],
    components: {
        'preload-component': PreloadComponent
    },
    data: function() {
        return {
            webInfo: this.website,
            pageTitle: this.title,
            firstLoad: true,
            useLoader: (import.meta.env.VITE_USE_LOADER == 'true'),
            loaderState: true
        }
    },
    watch: {
        '$route.name': function() {
            if (this.firstLoad) {
                this.loaderState = true
            }
        }
    },
    methods: {
        updateMetaData: function() {
            document.querySelector('title').innerHTML = `${this.pageTitle} | ${this.webInfo.name} - ${this.webInfo.tagline}`
            document.querySelector('meta[name=description]').setAttribute('content', this.webInfo.description)
            document.querySelector('link[data-id=favicon]').setAttribute('href', baseUrl('favicon.ico?v=' + this.webInfo.icon_version))
            document.querySelector('link[data-id=iconApple]').setAttribute('href', imageUrl(`icon/${this.webInfo.icon}`, 180))
            document.querySelector('link[data-id=icon32]').setAttribute('href', imageUrl(`icon/${this.webInfo.icon}`, 32))
            document.querySelector('link[data-id=icon16]').setAttribute('href', imageUrl(`icon/${this.webInfo.icon}`, 16))
        }
    },
    provide: function() {
        let app = this
        return {
            config: computed(() => app.webInfo),
            completeTitle: computed(() => `${app.pageTitle} | ${app.webInfo.name} - ${app.webInfo.tagline}`),
            updateConfig: function(newConfig) {
                app.webInfo = newConfig
                app.updateMetaData()
            },
            showLoader: function() {
                app.loaderState = true
            },
            hideLoader: function() {
                app.loaderState = false
            }
        }
    },
    mounted: function() {
        let app = this
        app.$nextTick(() => {
            app.firstLoad = false
            app.loaderState = false
        })

        // watch icons
        dom.watch();
    }
}

</script>