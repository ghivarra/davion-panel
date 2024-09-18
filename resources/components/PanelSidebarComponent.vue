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
            <div v-for="(group, groupIndex) in menu" v-bind:key="groupIndex">
                <span v-if="group.name !== 'Default'"
                    class="d-block pt-2 ps-3 mb-2 panel-sidebar-label text-uppercase">{{ group.name }}</span>
                <div v-for="(menu, menuIndex) in group.menu" v-bind:key="menuIndex"
                    v-bind:class="{ active: menu.is_active, parent: (menu.type === 'Parent') }"
                    class="panel-sidebar-link mb-1">

                    <!-- Primary Menu -->
                    <router-link v-if="menu.type === 'Primary'" v-bind:to="{ name: (menu.router_name === null) ? '' : menu.router_name }"
                        v-on:click="mainMenuClick(menu)"
                        class="d-flex align-items-center panel-sidebar-link-button btn btn-link w-100 text-decoration-none">
                        <font-awesome v-bind:icon="(menu.icon === null) ? 'fa-regular fa-circle' : menu.icon"
                            class="panel-sidebar-link-icon me-2"></font-awesome>
                        {{ menu.title }}
                    </router-link>

                    <!-- Parent Menu -->
                    <button v-else v-on:click="mainMenuClick(menu)" type="button" class="d-flex align-items-center panel-sidebar-link-button btn btn-link w-100 text-decoration-none">
                        <font-awesome v-bind:icon="menu.icon" class="panel-sidebar-link-icon me-2"></font-awesome>
                        {{ menu.title }}
                        <div class="arrow">
                            <font-awesome icon="fas fa-chevron-right" class="arrow-icon"></font-awesome>
                        </div>
                    </button>

                    <!-- Childs Menu Wrapper -->
                    <div v-if="(typeof menu.childs !== 'undefined')"
                        class="panel-sidebar-link-child-wrapper text-white pt-2">
                        <router-link v-for="(childMenu, childMenuIndex) in menu.childs" v-bind:key="childMenuIndex" 
                            v-bind:to="{ name: childMenu.router_name }"
                            v-bind:class="{ active: childMenu.is_active }"
                            class="d-flex mb-2 align-items-center panel-sidebar-link-button child btn btn-link w-100 text-decoration-none">
                            <font-awesome icon="fa-regular fa-circle"
                                class="panel-sidebar-link-icon me-2"></font-awesome>
                            {{ childMenu.title }}
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
    methods:{
        isActive: function(menu) {
            if (menu.type === 'Primary') {
                return this.activePrimary.includes(menu.id)
            } else if (menu.type === 'Parent') {
                return this.activeParent.includes(menu.id)
            }
        },
        mainMenuClick: function(menu) {
            this.menu.forEach((group) => {
                if (group.menu.length > 0) {
                    group.menu.forEach((groupMenu) => {
                        if (groupMenu.type === 'Parent' && groupMenu.id === menu.id) {
                            groupMenu.is_active = (groupMenu.is_active) ? false : true
                        }
                    })
                }
            })
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
    overflow: auto;

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