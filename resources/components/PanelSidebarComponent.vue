<template>
    <section class="panel-sidebar shadow-lg" v-bind:class="{ active: showSidebar }">
        <div class="p-3 d-flex panel-sidebar-logo-wrapper">
            <router-link v-bind:to="{ name: 'panel.dashboard' }">
                <img class="panel-sidebar-logo" v-bind:src="logo" v-bind:alt="config.name">
            </router-link>
            <button v-on:click="$emit('sidebarToggleClick')" type="button" class="panel-sidebar-logo-toggle btn btn-link align-items-center">
                <font-awesome icon="fa fa-xmark" class="text-danger fs-3"></font-awesome>
            </button>
        </div>
        <div class="p-3">
            <span v-on:click="activate(key)" v-for="(menuLink, key) in menuLinks" v-bind:key="key" class="panel-sidebar-link mb-2" v-bind:class="{ active: (menuLink.id === activeMenu) }">
                <font-awesome v-bind:icon="menuLink.icon" class="panel-sidebar-link-icon me-2"></font-awesome>
                {{ menuLink.name }}
                <div class="arrow">
                    <font-awesome icon="fas fa-chevron-right" class="arrow-icon"></font-awesome>
                </div>
            </span>
            </div>
    </section>
</template>

<script>

import { imageUrl } from "../libraries/Function"
import { library } from '@fortawesome/fontawesome-svg-core'
import { faEye, faEyeSlash, faGlobe, faChevronRight } from '@fortawesome/free-solid-svg-icons'

library.add(faEye, faEyeSlash, faGlobe, faChevronRight)

export default {
    name: 'panel-sidebar-component',
    props: ['showSidebar'],
    inject: ['config'],
    data: function() {
        return {
            activeMenu: 2,
            menuLinks: [
                {id: 1, icon: 'fas fa-globe', name: 'Pengaturan Website'},
                {id: 2, icon: 'fas fa-eye', name: 'Admin'},
                {id: 3, icon: 'fas fa-eye-slash', name: 'Modul'},
            ]
        }
    },
    methods:{
        activate: function(key) {
            this.activeMenu = this.menuLinks[key].id
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

    &-link {
        color: darken(#ffffff, 20%);
        padding: .5em 1.25em .5em 1.25em;
        border-radius: 8px;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: all 100ms ease-out;
        position: relative;

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

        &:hover {
            color: #ffffff;
            background-color: adjust-color(#ffffff, $alpha: -0.95);
        }

        &.active {
            background: linear-gradient(73deg, $primary 25%, adjust-color($primary, $alpha: -0.3) 78%);
            box-shadow: 0px 2px 6px 0px adjust-color($primary, $lightness: 20%, $alpha: -0.52);
            color: #ffffff;

            .arrow {
                rotate: 90deg;
                top: 10px;
            }
        }
    }
}

</style>