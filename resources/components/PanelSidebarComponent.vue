<template>
    <section class="panel-sidebar shadow-lg sticky-top" v-bind:class="{ active: showSidebar }">
        <div class="p-3 d-flex panel-sidebar-logo-wrapper">
            <router-link v-bind:to="{ name: 'panel.dashboard' }">
                <img class="panel-sidebar-logo" v-bind:src="logo" v-bind:alt="config.name">
            </router-link>
            <button v-on:click="$emit('sidebarToggleClick')" type="button"
                class="panel-sidebar-logo-toggle btn btn-link align-items-center">
                <font-awesome icon="fa fa-xmark" class="text-danger fs-3"></font-awesome>
            </button>
        </div>
        <div class="p-3">
            <div v-for="(group, a) in menu" v-bind:key="a">
                <span v-if="group.name !== 'Default'"
                    class="d-block pt-2 ps-3 mb-2 panel-sidebar-label text-uppercase">{{ group.name }}</span>
                <div v-for="(item, n) in group.menu" v-bind:key="n"
                    v-bind:class="{ active: (item.type === 'Primary') ? this.activePrimary.includes(item.id) : this.activeParent.includes(item.id), 'parent': (item.type === 'Parent') }"
                    class="panel-sidebar-link mb-1">

                    <!-- Primary Menu -->
                    <router-link v-if="item.type === 'Primary'" v-bind:to="{ name: item.router_name }"
                        v-on:click="mainMenuClick(item)"
                        class="d-flex align-items-center panel-sidebar-link-button btn btn-link w-100 text-decoration-none">
                        <font-awesome v-bind:icon="item.icon" class="panel-sidebar-link-icon me-2"></font-awesome>
                        {{ item.title }}
                    </router-link>

                    <!-- Parent Menu -->
                    <button v-else v-on:click="mainMenuClick(item)" type="button"
                        class="d-flex align-items-center panel-sidebar-link-button btn btn-link w-100 text-decoration-none">
                        <font-awesome v-bind:icon="item.icon" class="panel-sidebar-link-icon me-2"></font-awesome>
                        {{ item.title }}
                        <div class="arrow">
                            <font-awesome icon="fas fa-chevron-right" class="arrow-icon"></font-awesome>
                        </div>
                    </button>

                    <!-- Childs Menu Wrapper -->
                    <div v-if="(typeof item.childs !== 'undefined')"
                        class="panel-sidebar-link-child-wrapper text-white pt-2">
                        <router-link v-for="(child, x) in item.childs" v-bind:key="x" v-on:click="childMenuClick(child)"
                            v-bind:to="{ name: child.router_name }"
                            v-bind:class="{ active: this.activePrimary.includes(child.id) }"
                            class="d-flex mb-2 align-items-center panel-sidebar-link-button child btn btn-link w-100 text-decoration-none">
                            <font-awesome icon="fa-regular fa-circle"
                                class="panel-sidebar-link-icon me-2"></font-awesome>
                            {{ child.title }}
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>

import { imageUrl } from "../libraries/Function"
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTableCellsLarge, faUser, faUserTie, faTableColumns, faGlobe, faChevronRight } from '@fortawesome/free-solid-svg-icons'
import { faCircle } from "@fortawesome/free-regular-svg-icons"

library.add(faTableCellsLarge, faUser, faUserTie, faTableColumns, faGlobe, faChevronRight, faCircle)

export default {
    name: 'panel-sidebar-component',
    props: ['showSidebar', 'menu', 'activeMenu', 'activeParentMenu'],
    inject: ['config'],
    data: function() {
        return {
            activePrimary: [],
            activeParent: []
        }
    },
    watch: {
        activeMenu: function(newValue) {
            this.activePrimary = []
            this.activePrimary.push(newValue)
        },
        activeParentMenu: function(newValue) {
            this.activeParent.push(newValue)
        }
    },
    methods:{
        isActive: function(menu) {
            if (menu.type === 'Primary') {
                return this.activePrimary.includes(menu.id)
            } else if (menu.type === 'Parent') {
                return this.activeParent.includes(menu.id)
            }
        },
        mainMenuClick: function(menu) {
            if (menu.type === 'Primary') {

                // clean active Menu
                this.activePrimary = []
                this.activeParent = []

                // push new routes
                this.activePrimary.push(menu.id)

            } else if (menu.type === 'Parent') {
                if (this.activeParent.includes(menu.id)) {
                    // search and activate parent
                    var index = this.activeParent.indexOf(menu.id);
                    this.activeParent.splice(index, 1);
                } else {
                    this.activeParent.push(menu.id)
                }
            }
        },
        childMenuClick: function(menu) {
            // clean active Menu
            this.activePrimary = []

            // push new routes
            this.activePrimary.push(menu.id)
        }
    },
    computed: {
        logo: function() {
            return imageUrl(`logo/${this.config.logo}`, 180)
        }
    }
}

</script>

<style lang="scss">

@import "../assets/base.scss";

.panel-sidebar {
    min-width: 260px;
    height: 100vh;
    height: 100dvh;
    background-color: darken($primary, 25%);
    transition: all 250ms ease-in-out;

    @media (max-width: 991.98px) {
        position: fixed;
        min-width: 310px;
        transform: translateX(-310px);
        z-index: 100;
    }

    @media (max-width: 575.98px) {
        min-width: 100%;
        transform: translate(-100%);
    }

    &.active {
        @media (max-width: 991.98px) {
            transform: translateX(0);
        }
    }

    &-logo {
        max-width: 180px;
        filter: brightness(10);

        &-wrapper {
            justify-content: center;

            @media (max-width: 991.98px) {
                padding-left: 2em !important;
                justify-content: space-between;
            }
        }

        &-toggle {
            display: none;

            @media (max-width: 991.98px) {
                display: flex;
            }
        }
    }

    &-label {
        color: darken(#ffffff, 35%);
    }

    &-link {

        &-button {
            color: darken(#ffffff, 20%);
            padding: .5em 1.25em .5em 1.25em;
            border-radius: 8px;
            cursor: pointer;
            transition: all 100ms ease-out;
            position: relative;

            &:hover {
                color: #ffffff;
                background-color: adjust-color(#ffffff, $alpha: -0.95);
            }

            &-icon {
                width: 1em;
            }

            .arrow {
                align-self: flex-end;
                position: absolute;
                right: 10px;
                top: 8px;
                width: 16px;
                height: 16px;
                transition: all 100ms ease-in;

                &-icon {
                    width: 16px;
                    height: 16px;
                }
            }
        }
        
        &.active {

            .panel-sidebar-link-button {
                background: linear-gradient(73deg, $primary 25%, adjust-color($primary, $alpha: -0.3) 78%);
                box-shadow: 0px 2px 6px 0px adjust-color($primary, $lightness: 20%, $alpha: -0.52);
                color: #ffffff;
            }
        }

        
        &.parent {

            .panel-sidebar-link-child-wrapper {
                overflow: hidden;
                height: 0;
            }

            &.active {
                .panel-sidebar-link-button {
                    background: adjust-color(#ffffff, $alpha: -0.85);

                    &.child {
                        background: none;
                        box-shadow: none;

                        &:hover {
                            color: #ffffff;
                            background-color: adjust-color(#ffffff, $alpha: -0.95);
                        }

                        &.active {
                            background: linear-gradient(73deg, $primary 25%, adjust-color($primary, $alpha: -0.3) 78%);
                            box-shadow: 0px 2px 6px 0px adjust-color($primary, $lightness: 20%, $alpha: -0.52);
                            color: #ffffff;
                        }
                    }

                    .arrow {
                        rotate: 90deg;
                        top: 10px;
                    }
                }

                .panel-sidebar-link-child-wrapper {
                    height: 100%
                }
            }
        }
        
    }
}

</style>