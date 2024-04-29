<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        <section>
            <div v-for="(group, key) in menus" v-bind:key="key" class="card mb-4">
                <header class="card-header">
                    Grup: {{ group.name }}
                </header>
                <div v-bind:ref="`groupMenu${key}`" class="card-body">
                    <draggable-menu-component v-bind:child="group.child" v-bind:onMove="log" groupName="menus" itemKey="id"></draggable-menu-component>
                </div>
            </div>
        </section>

    </main>
</template>

<script>

import { checkAxiosError, panelUrl } from '@/libraries/Function'
import axios from 'axios'
import draggableMenuComponent from '@/components/DraggableMenuComponent.vue'

export default {
    name: 'panel-menu-view',
    inject: ['showLoader', 'hideLoader'],
    components: {
        'draggable-menu-component': draggableMenuComponent
    },
    data: function() {
        return {
            name: 'Menu',
            menus: []
        }
    },
    methods: {
        log: function(event) {
            let app = this
            let man = 'tul'

            console.log(event)

            for (let i = 0; i < app.menus.length; i++) {
                let menu = app.menus[i].child
                if (menu.length > 0) {
                    for (let n = 0; n < menu.length; n++) {
                        let submenu = menu[n].child
                        if (submenu.length > 0) {
                            for (let x = 0; x < submenu.length; x++) {
                                let submenuChild = submenu[x].child
                                if (submenuChild.length > 0) {
                                    console.log(man)
                                }
                            }
                        }
                    }
                }
            }
        }
    },
    created: function() {
        let app = this

        // get module group list
        axios.get(panelUrl('menu/list'))
            .then(function(res) {
                if (typeof res.data.status !== 'undefined' && res.data.status === 'success') {
                    app.menus = res.data.data
                }
            }).catch(function(res) {
                checkAxiosError(res.request.status)
            })
    },
    mounted: function() {
        this.$nextTick(function() {
            this.$emit('loaded')
        })
    }
}

</script>

<style lang="scss"></style>