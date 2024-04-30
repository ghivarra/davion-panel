<template>
    <main role="main" class="mb-4">
        <slot name="breadcrumb"></slot>
        <section class="accordion mb-5">

            <draggable v-bind:list="menus" itemKey="id">
                <template v-slot:item="{ element, index }">
                    <div class="accordion-item cursor-grab mb-4">
                        <header class="accordion-header position-relative">
                            <div v-bind:data-bs-target="`#group${index}`" v-bind:aria-controls="`group${index}`"
                                type="button" class="accordion-button fw-bold" data-bs-toggle="collapse"
                                aria-expanded="true">
                                {{ element.name }}
                                <span v-if="element.status === 'Aktif'" class="badge ms-2 text-bg-success">Aktif</span>
                                <span v-else class="badge ms-2 text-bg-warning">Nonaktif</span>

                                <button v-on:click.prevent="editButton" type="button" class="btn btn-link edit-menu-button group" title="Edit Menu">
                                    <font-awesome icon="fas fa-gear"></font-awesome>
                                </button>
                            </div>
                        </header>
                        <div v-bind:id="`group${index}`" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <draggable-menu-component v-bind:child="element.child" v-on:editButtonTrigger="editButton" groupName="menus"
                                    itemKey="id"></draggable-menu-component>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>

            <div>
                <button v-on:click.prevent="save" type="button" class="btn btn-primary">
                    <font-awesome icon="fas fa-save" class="me-1"></font-awesome>
                    Simpan Susunan Menu
                </button>
            </div>

        </section>

    </main>
</template>

<script>

import { checkAxiosError, panelUrl } from '@/libraries/Function'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faSave } from '@fortawesome/free-solid-svg-icons'
import draggableMenuComponent from '@/components/DraggableMenuComponent.vue'
import draggable from 'vuedraggable'
import axios from 'axios'
import Swal from 'sweetalert2'

library.add(faSave)

export default {
    name: 'panel-menu-view',
    inject: ['showLoader', 'hideLoader'],
    components: {
        draggable,
        'draggable-menu-component': draggableMenuComponent
    },
    data: function() {
        return {
            name: 'Menu',
            menus: []
        }
    },
    methods: {
        save: function() {
            let app = this
            let form = new FormData()
            let menuIterator = -1
            let group
            let menu
            let submenu

            app.showLoader()

            for (let i = 0; i < app.menus.length; i++) {
                group = app.menus[i]
                form.append(`group[${i}][id]`, group.id)
                form.append(`group[${i}][sort_order]`, (i + 1))

                if (group.child.length > 0) {
                    for (let n = 0; n < group.child.length; n++) {
                        menuIterator++
                        menu = group.child[n]
                        form.append(`menu[${menuIterator}][admin_menu_group_id]`, group.id)
                        form.append(`menu[${menuIterator}][id]`, menu.id)
                        form.append(`menu[${menuIterator}][sort_order]`, (n + 1))
                        
                        if (menu.child.length > 0) {
                            form.append(`menu[${menuIterator}][type]`, 'Parent')

                            for (let x = 0; x < menu.child.length; x++) {
                                menuIterator++
                                submenu = menu.child[x]
                                form.append(`menu[${menuIterator}][id]`, submenu.id)
                                form.append(`menu[${menuIterator}][sort_order]`, (x + 1))
                                form.append(`menu[${menuIterator}][type]`, 'Child')
                                form.append(`menu[${menuIterator}][admin_menu_parent_id]`, menu.id)
                            }

                        } else {
                            form.append(`menu[${menuIterator}][type]`, 'Primary')
                        }
                    }
                }
            }

            // send
            axios.post(panelUrl('menu/sort'), form)
                .then(function(res) {
                    res = res.data
                    app.hideLoader()
                    if (res.status !== 'success') {
                        Swal.fire('Whoopss!!', res.message, 'warning')
                    } else {
                        window.location.reload()
                    }
                }).catch(function(res) {
                    app.hideLoader()
                    checkAxiosError(res.request.status)
                })
        },
        editButton: function() {
            console.log('run edit button')
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

<style lang="scss">

.cursor-grab {
    cursor: grab;

    &:click, &:focus {
        cursor: grabbing
    }
}

.edit-menu-button {
    position: absolute;
    right: 0;
    top: 0;

    &.group {
        right: 2.5rem;
        top: .5rem;
    }
}

</style>