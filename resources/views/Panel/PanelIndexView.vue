<template>

    <div class="d-flex">

        <!-- BACKDROP FOR SIDEBAR -->
        <div v-if="showSidebar" v-on:click.prevent="toggleSidebar" id="sidebarBackdrop"></div>

        <!-- SIDEBAR -->
        <aside>
            <panel-sidebar-component v-bind:activeMenu="activeMenuId" v-bind:activeParentMenu="activeParentMenuId"
                v-bind:menu="menu" v-bind:showSidebar="showSidebar" v-on:sidebarToggleClick="toggleSidebar">
            </panel-sidebar-component>
        </aside>

        <!-- MAIN -->
        <div class="panel-body px-4 py-3">

            <!-- PRELOADER -->
            <Transition name="loader">
                <preload-component v-if="loaderState"></preload-component>
            </Transition>

            <!-- HEADER -->
            <panel-header-component v-on:sidebarToggleClick="toggleSidebar"></panel-header-component>

            <!-- VIEW -->
            <router-view v-slot="{ Component }">
                <component v-on:loaded="stopLoader" v-bind:is="Component">
                    <template v-slot:breadcrumb>
                        <header class="panel-main-header my-4 justify-content-between align-items-center">
                            <h4 class="panel-main-header-title text-primary fw-bold">{{
                $router.currentRoute.value.meta.pageName }}</h4>
                            <div class="d-flex align-items-center">
                                <router-link v-for="(bread, n) in breadcrumbs" v-bind:key="n"
                                    v-bind:to="{ name: bread.name }" class="text-decoration-none link-secondary">
                                    <span v-if="(n > 0)" class="mx-1">/</span>
                                    {{ bread.title }}
                                </router-link>
                            </div>
                        </header>
                    </template>
                </component>
            </router-view>
        </div>

    </div>

</template>

<script>

/*eslint no-unused-vars: 0*/
import * as bootstrap from 'bootstrap'
import PanelHeaderComponent from '../../components/PanelHeaderComponent.vue'
import PreloadComponent from '../../components/PreloadComponent.vue'
import PanelSidebarComponent from '../../components/PanelSidebarComponent.vue'
import { dom } from '@fortawesome/fontawesome-svg-core'
import { computed } from 'vue'
import { panelUrl, checkAxiosError, generateBreadcrumb } from '../../libraries/Function'
import axios from 'axios'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBars, faPenToSquare, faSliders, faTrashCan, faPlus, faMagnifyingGlass, faXmark, faGear, faKey, faRightFromBracket, faTableCellsLarge, faUser, faUserTie, faTableColumns, faGlobe, faChevronRight, faSave, faEllipsisVertical, faEye, faEyeSlash } from '@fortawesome/free-solid-svg-icons'

library.add(faBars, faPenToSquare, faSliders, faTrashCan, faPlus, faMagnifyingGlass, faXmark, faGear, faKey, faRightFromBracket, faTableCellsLarge, faUser, faUserTie, faTableColumns, faGlobe, faChevronRight, faSave, faEllipsisVertical, faEye, faEyeSlash)

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
            breadcrumbs: [],
            webInfo: this.website,
            pageTitle: this.title,
            admin: {},
            menu: [],
            firstLoad: true,
            loaderState: true,
            showSidebar: false,
            activeMenuId: null,
            activeParentMenuId: null,
        }
    },
    watch: {
        '$route.name': function() {
            this.pageTitle = this.$router.currentRoute.value.meta.pageName
            this.updateMetaData()
            this.activateMenu()
            this.breadcrumbs = generateBreadcrumb(this.$router)
            this.loaderState = true
            this.showSidebar = false
        }
    },
    methods: {
        updateMetaData: function() {
            document.querySelector('title').innerHTML = `${this.pageTitle} | ${this.webInfo.name} - ${this.webInfo.tagline}`
        },
        toggleSidebar: function() {
            this.showSidebar = !this.showSidebar
        },
        activateMenu: function() {
            let app = this
            let currentRouteName = app.$router.currentRoute.value.name

            // menu exist
            let menuExist = false

            app.menu.forEach((group) => {
                group.menu.forEach((item) => {
                    if (typeof item.router_name !== 'undefined' && item.router_name === currentRouteName) {
                        menuExist = true
                        app.activeMenuId = item.id
                    }
                    if (typeof item.childs !== 'undefined') {
                        item.childs.forEach((child) => {
                            if (typeof child.router_name !== 'undefined' && child.router_name === currentRouteName) {
                                menuExist = true
                                app.activeParentMenuId = item.id
                                app.activeMenuId = child.id
                            }
                        })
                    }
                })
            })

            if (!menuExist) {
                app.activeMenuId = null
            }
        },
        stopLoader: function() {
            let app = this
            if (app.firstLoad) {
                app.firstLoad = false
                setTimeout(() => {
                    app.loaderState = false
                    app.activateMenu()
                }, 1000)
            } else {
                app.$nextTick(() => {
                    app.loaderState = false
                })
            }
        },
        updateAdminData: function() {
            let app = this
            return axios.get(panelUrl('public/session-data'))
                .then(function(res) {
                    res = res.data
                    app.admin = res.data
                }).catch(function(res) {
                    checkAxiosError(res.request.status)
                })
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
            },
            updateAdminData: function() {
                app.updateAdminData()
            }
        }
    },
    created: function() {
        let app = this

        // get account data
        app.updateAdminData()

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
        this.breadcrumbs = generateBreadcrumb(this.$router)
        dom.watch();
    }
}

</script>

<style lang="scss">

@import "../../assets/base.scss";

$largeBreakpoint: "991.98px";

.panel {
    &-body {
        background-color: darken(#ffffff, 5%);
        width: calc(100vw - 260px);
        width: calc(100dvw - 260px);
        display: block;

        @media (max-width: $largeBreakpoint) {
            width: 100vw;
            width: 100dvw;
            background-color: #ffffff;
        }
    }

    &-main {
        &-header {
            display: flex;

            @media (max-width: $largeBreakpoint) {
                display: block;
            }

            &-title {
                margin-bottom: 0;
                font-size: 1.6rem;

                @media (max-width: $largeBreakpoint) {
                    margin-bottom: .4rem;
                }
            }
        }
    }

    &-box {
        border-radius: 6px;
        border: 1px solid lighten(#000000, 85%);
        box-shadow: 0 0.125em 0.25em rgba(0, 0, 0, .15);
        overflow: hidden;

        &-header {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--bs-light);
            background-color: lighten($primary, 15);
            padding: .75rem 1rem;
        }
    }
}

table {
    .col {
        &-no {
            width: 100px;
        }
        
    }
}

#sidebarBackdrop {
    background-color: #000000;
    min-width: 100%;
    min-height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100;
    opacity: .2;
}

</style>