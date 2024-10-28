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
            <div v-show="useLoader">
                <Transition name="loader">
                    <preload-component v-if="loaderState"></preload-component>
                </Transition>
            </div>

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

// import bootstrap
import 'bootstrap/js/dist/modal'
import 'bootstrap/js/dist/button'
import 'bootstrap/js/dist/dropdown'
import 'bootstrap/js/dist/offcanvas'

// import others
import PanelHeaderComponent from '../../components/PanelHeaderComponent.vue'
import PreloadComponent from '../../components/PreloadComponent.vue'
import PanelSidebarComponent from '../../components/PanelSidebarComponent.vue'
import { dom } from '@fortawesome/fontawesome-svg-core'
import { computed } from 'vue'
import { panelUrl, checkAxiosError, generateBreadcrumb } from '../../libraries/Function'
import axios from 'axios'
import swal from 'sweetalert'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBars, faPenToSquare, faSliders, faTrashCan, faPlus, faMagnifyingGlass, faXmark, faGear, faKey, faRightFromBracket, faTableCellsLarge, faUser, faUserTie, faTableColumns, faGlobe, faChevronRight, faSave, faEllipsisVertical, faEye, faEyeSlash, faEnvelope, faClockRotateLeft, faCircleInfo, faList } from '@fortawesome/free-solid-svg-icons'

library.add(faBars, faPenToSquare, faSliders, faTrashCan, faPlus, faMagnifyingGlass, faXmark, faGear, faKey, faRightFromBracket, faTableCellsLarge, faUser, faUserTie, faTableColumns, faGlobe, faChevronRight, faSave, faEllipsisVertical, faEye, faEyeSlash, faEnvelope, faClockRotateLeft, faCircleInfo, faList)

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
            useLoader: (import.meta.env.VITE_USE_LOADER == 'true'),
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
            this.showSidebar = false

            if (import.meta.env.VITE_LOADER_ON_CHANGE_PAGE == 'true') {
                this.loaderState = true
            }
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
            app.menu.forEach((group) => {
                if (group.menu.length > 0) {
                    group.menu.forEach((groupMenu) => {
                        if (groupMenu.type !== 'Parent') {
                            groupMenu.is_active = (groupMenu.router_name === app.$route.name)
                        }
                        if (typeof groupMenu.childs !== 'undefined') {
                            groupMenu.childs.forEach((childMenu) => {
                                if (childMenu.router_name === app.$route.name) {
                                    childMenu.is_active = true
                                    groupMenu.is_active = true
                                } else {
                                    childMenu.is_active = false
                                    // groupMenu.is_active = false
                                }
                            })
                        }
                    })
                }
            })
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
                    if (res.status !== 'success') {
                        swal({
                            title: 'Whoopss!!',
                            icon: 'warning',
                            text: res.message,
                            buttons: {
                                confirm: {
                                    className: 'btn btn-primary',
                                    text: 'OK'
                                }
                            }
                        }).then(function() {
                            window.location.reload()
                        })
                    } else {
                        app.admin = res.data
                    }
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
            },
            loggingOut: function() {
                axios.get(panelUrl('public/logout'))
                        .then(function(res) {
                            let data = res.data
                            if(typeof data.status === 'undefined' || data.status !== 'success') {
                                swal({
                                    title: 'Whoopss!!',
                                    icon: 'warning',
                                    text: 'Koneksi jaringan atau server sedang bermasalah, silahkan coba lagi',
                                    buttons: {
                                        confirm: {
                                            className: 'btn btn-primary',
                                            text: 'OK'
                                        }
                                    }
                                })
                            } else {
                                window.location.reload()
                            }
                        }).catch(function(res) {
                            checkAxiosError(res.request.status)
                        })
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
                if (res.status !== 'success') {
                    swal({
                        title: 'Whoopss!!',
                        icon: 'warning',
                        text: res.message,
                        buttons: {
                            confirm: {
                                className: 'btn btn-primary',
                                text: 'OK'
                            }
                        }
                    }).then(function() {
                        window.location.reload()
                    })
                } else {
                    app.menu = res.data
                    app.activateMenu()
                }
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

table .table-dropdown::after {
    display: none;
}

.ghivarra-vue-table-wrapper {
    .col-no {
        text-align: center;
    }
}

.swal-overlay {
    .swal-modal {
        .swal-button {

            &.btn {
                font-size: 1.15rem;
                padding-left: 1.2rem;
                padding-right: 1.2rem;

                &.btn-sm {
                    font-size: 0.95rem;
                    padding-left: 1rem;
                    padding-right: 1rem;
                }   
            }

            &.btn-primary:not([disabled]):hover, &.btn-secondary:not([disabled]):hover, &.btn-danger:not([disabled]):hover, &.btn-outline-primary:not([disabled]):hover, &.btn-outline-secondary:not([disabled]):hover, &.btn-outline-danger:not([disabled]):hover {
                background-color: var(--bs-btn-hover-bg);
            }
        }
        
        .swal-footer {
            text-align: center;
        }

        .swal-title {
            margin-bottom: 1.2rem;
        }

        .swal-text {
            text-align: center;
        }

        &.confirmation-alert {
            .swal-title {
                margin-top: 0;
                padding: 1rem 1.5rem;
                border-bottom: 1px solid #dcdcdc;
                font-size: 1.25rem;
                text-align: left;
                background-color: #efefef;
                border-radius: 8px 8px 0 0;
            }
            .swal-text {
                text-align: left;
            }
            .swal-footer {
                margin-top: 2rem;
                padding: .75rem 1rem;
                border-top: 1px solid #dcdcdc;
                font-size: 1.25rem;
                text-align: right;
            }
        }
    }

}

</style>