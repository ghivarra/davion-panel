<template>

    <div class="d-flex panel-body">

        <!-- SIDEBAR -->
        <aside>
            <panel-sidebar-component 
                v-bind:menu="menu"
                v-bind:showSidebar="showSidebar" 
                v-on:sidebarToggleClick="toggleSidebar">
            </panel-sidebar-component>
        </aside>

        <!-- MAIN -->
        <main class="panel-main px-4 py-3">

            <!-- PRELOADER -->
            <Transition name="loader">
                <preload-component v-if="loaderState"></preload-component>
            </Transition>

            <!-- HEADER -->
            <panel-header-component v-on:sidebarToggleClick="toggleSidebar"></panel-header-component>

            <!-- VIEW -->
            <router-view v-on:loaded="stopLoader"></router-view>
        </main>

    </div>

</template>

<script>

import PanelHeaderComponent from '../../components/PanelHeaderComponent.vue'
import PreloadComponent from '../../components/PreloadComponent.vue'
import PanelSidebarComponent from '../../components/PanelSidebarComponent.vue'
import { dom } from '@fortawesome/fontawesome-svg-core'
import { computed } from 'vue'
import { baseUrl, imageUrl, panelUrl, checkAxiosError } from '../../libraries/Function'
import axios from 'axios'

export default {
    name: 'panel-index-view',
    props: ['website', 'title'],
    components: {
        'preload-component': PreloadComponent,
        'panel-sidebar-component': PanelSidebarComponent,
        'panel-header-component': PanelHeaderComponent
    },
    data: function() {
        return {
            webInfo: this.website,
            pageTitle: this.title,
            admin: {},
            menu: [],
            firstLoad: true,
            loaderState: true,
            showSidebar: false
        }
    },
    watch: {
        '$route.name': function() {
            this.pageTitle = this.$router.currentRoute.value.meta.pageName
            this.updateMetaData()
            this.loaderState = true
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
        },
        toggleSidebar: function() {
            this.showSidebar = !this.showSidebar
        },
        stopLoader: function() {
            let app = this
            if (app.firstLoad) {
                app.firstLoad = false
                setTimeout(() => {
                    app.loaderState = false
                }, 1000)
            } else {
                app.$nextTick(() => {
                    app.loaderState = false
                })
            }
        }
    },
    provide: function() {
        let app = this
        return {
            config: computed(() => app.webInfo),
            admin: computed(() => app.admin),
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
    created: function() {
        let app = this

        // get account data
        axios.get(panelUrl('public/session-data'))
            .then(function(res) {
                res = res.data
                app.admin = res.data
            }).catch(function(res) {
                checkAxiosError(res.request.status)
            })

        // get menu
        axios.get(panelUrl('public/menu'))
            .then(function(res) {
                res = res.data
                app.menu = res.data
            }).catch(function(res) {
                checkAxiosError(res.request.status)
            })
    },
    mounted: function() {
        // watch icons
        dom.watch();
    }
}

</script>

<style lang="scss">

.panel {
    &-main {
        background-color: darken(#ffffff, 5%);
        width: calc(100vw - 260px);
        width: calc(100dvw - 260px);

        @media (max-width: 991.98px) {
            width: 100vw;
            width: 100dvw;
        }
    }

    &-box {
        border-radius: 6px;
        border: 1px solid lighten(#000000, 85%);
        box-shadow: 0 0.125em 0.25em rgba(0,0,0,.15);
    }
}

</style>